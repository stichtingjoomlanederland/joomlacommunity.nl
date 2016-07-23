<?php
/**
 * Shlib - programming library
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2015
 * @package     shlib
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     0.3.1.540
 * @date				2016-07-18
 */

// Security check to ensure this file is being included by a parent file.
defined('_JEXEC') or die;

/**
 * Manages a simplistic cache, storing key/value pairs
 * into a shared memory system such as memcached, apc or xcache
 *
 * @author yannick
 *
 */
class ShlCache_Manager {

  const CACHE_HANDLER_APC = 'apc';
  const CACHE_HANDLER_MEMCACHE = 'memcache';
  const CACHE_HANDLER_MEMCACHED = 'memcached';
  const CACHE_HANDLER_WINCACHE = 'wincache';

  // singleton pattern storage
  protected static $_manager = null;

  // on/off switch
  public $enabled = false;

  // handler
  protected static $_handler = '';

  // unique id for this site, used
  // to distinguish possibly identical data id
  // coming from different sites onto the same
  // cache server
  protected static $_uniqueId = '';

  // data default time to live, 3600 seconds
  protected static $_defaultTTL = 3600;

  // storage for handler specific parameters
  // whatever they are. Can be set using setParams()
  protected static $_params = array();

  // statistics vars
  public static $_totalRequests = array();
  public static $_cacheHits = array();
  public static $_cacheStore = array();
  public static $_cacheRemoved = array();

  public static function setParams( $params) {

    self::$_params = $params;
  }

  /**
   * Change handler to be used by cache manager
   * Can be called only before any use of the cache
   * and can only be called once, subsequent calls will
   * have no effect
   *
   * @param string $handler the cache handler name (can use class constants)
   * @param array $params whatever params the handler may need
   */
  public static function setHandler( $handler, $params = array()) {
    if(empty(self::$_manager)) {
      self::$_handler = strtolower($handler);
      self::$_params = $params;
    }
  }

  public static function getHandler() {

    return ucfirst(self::$_handler);
  }

  public static function setDefaultTTL( $newTTL) {

    $newTTL = (int) $newTTL;

    if( $newTTL < 0) {
      throw new ShlException( __METHOD__ . ': trying to set negative TTL on cache handler');
    }

    self::$_defaultTTL = $newTTL;

    return self::_getInstance(self::$_handler);
  }

  /**
   * Check if setup is correct, ie required extension is loaded in php for instance
   * Stub, should be re-implemented by decorator
   *
   */
  public function init( $params) {
    $this->enabled = false;
    return true;
  }

  /**
   *
   * Read information stored in cache, as identified by id
   * No further deserialization is required by the calling party
   *
   * @param string $id unique id of the element of information
   * @param string $dataset a group the data belongs to, mostly for stats accumulation
   * @return boolean|mixed either the value read or false if none found
   */
  public static function read( $id, $dataset) {

    self::$_totalRequests[$dataset] = empty(self::$_totalRequests[$dataset]) ? 1 : self::$_totalRequests[$dataset] + 1;
    $read = self::_getInstance(self::$_handler)->doRead( self::_computeCacheId( $id, $dataset));
    if($read !== false) {
      self::$_cacheHits[$dataset] = empty(self::$_cacheHits[$dataset]) ? 1 : self::$_cacheHits[$dataset] + 1;
    }
    return $read;
  }

  /**
   *
   * Store some piece of information in cache,
   * using handler defined by a call to ShlCache_Manager::setHandler()
   * or using default handler if setHandler() has not be called yet
   * No serialization is required by the calling party
   *
   * @param string $id unique id for the information
   * @param string $dataset a group the data belongs to, mostly for stats accumulation
   * @param mixed $value a php variable holding the data
   * @param integer $ttl optional time to live for the data, default to 3600 seconds
   * @return boolean, true if success
   * @throws ShlException
   */
  public static function store( $id, $dataset, $value, $ttl = null) {

    $ttl = is_null($ttl) ? self::$_defaultTTL : (int) $ttl;
    if( $ttl < 0) {
      throw new ShlException( __METHOD__ . ': trying to set negative TTL on cache handler');
    }
    $stored = self::_getInstance(self::$_handler)->doStore( self::_computeCacheId( $id, $dataset), $value, $ttl);
    if($stored) {
      self::$_cacheStore[$dataset] = empty(self::$_cacheStore[$dataset]) ? 1 : self::$_cacheStore[$dataset] + 1;
    }
    return $stored;
  }

  /**
   *
   * Remove a piece of information from the cache
   * using current handler
   *
   * @param string $id unique id fo the information to remove from cache
   * @param string $dataset a group the data belongs to, mostly for stats accumulation
   * @return boolean, true if success
   * @throws ShlException
   */
  public static function remove( $id, $dataset) {

    $removed = self::_getInstance(self::$_handler)->doRemove( self::_computeCacheId( $id, $dataset));
    if($removed) {
      self::$_cacheRemoved[$dataset] = empty(self::$_cacheRemoved[$dataset]) ? 1 : self::$_cacheRemoved[$dataset] + 1;
    }
    return $removed;
  }

  /**
   *
   * Clear the cache. If handler can process groups,
   * it is possible to clear only a group of items
   *
   * @param string $group optional, name of items group to clear
   * @return boolean, true if success
   * @throws ShlException
   */
  public static function clear( $group = '') {

    return self::_getInstance(self::$_handler)->doClear( $group);

  }

  /**
   * Set a unique id, required to distinguish stored
   * information that may otherwise have the same id
   * (such as same db queries from different web sites
   * running on same server)
   * This is NOT the same as the id each piece of information
   * stored in the cache needs, but rather a global "tag" or "namespace"
   * prepended (automatically) by the cache manager to each
   * stored data id
   * If not set (by an extension using shLib), we will default
   * to the Joomla site "secret" word stored in general config.
   *
   * @param string $id a string id, unique for each site providing data to
   * the same cache handler
   * @return string previous value of the unique id
   */
  public static function setUniqueId( $id) {

    $previous = self::$_uniqueId;
    self::$_uniqueId = $id;

    return $previous;
  }

  /**
   *
   * Return a unique id for this web site. If not
   * previously set by user (through ::setUniqueId(),
   * we use Joomla secret key
   */
  public static function getUniqueId() {

    self::$_uniqueId = empty( self::$_uniqueId) ? JFactory::getConfig()->get('secret') : self::$_uniqueId;

    return self::$_uniqueId;
  }

  /**
   *
   * Returns an object holding cache access stats (for the current page load!)
   *
   * @param string $dataset a group the data belongs to, mostly for stats accumulation
   * @return stdClass
   */
  public static function getCacheStats( $dataset) {

    $stats = new stdClass();
    $stats->read = empty(self::$_totalRequests[$dataset]) ? 0 : self::$_totalRequests[$dataset];
    $stats->hits = empty(self::$_cacheHits[$dataset]) ? 0 : self::$_cacheHits[$dataset];
    $stats->stored = empty(self::$_cacheStore[$dataset]) ? 0 : self::$_cacheStore[$dataset];
    $stats->removed = empty(self::$_cacheRemoved[$dataset]) ? 0 : self::$_cacheRemoved[$dataset];

    return $stats;
  }

  /**
   *
   * create a unique instance of a cache manager
   * using specified handler
   * @param string $handler code for cache handler to use
   */
  private static function _getInstance( $handler = '') {

    // create a single manager instance
    if(is_null(self::$_manager)) {
      // instantiate a manager
      $base = new ShlCache_Manager();

      // get default handler, if none supplied
      $handler = empty( $handler) ? self::CACHE_HANDLER_APC : $handler;

      // decorate manager with the proper handler
      $className = 'ShlCache_' . ucfirst( $handler);
      if(class_exists($className)) {
        self::$_manager = new $className( $base);
      } else {
        throw new ShlException( __METHOD__ . ': trying to instantiate a non-existing cache handler: ' . $className);
      }

      // ask handler to check setup is working, ie corresponding extension is loaded in php
      self::$_params['timeout'] = isset(self::$_params['timeout']) ? self::$_params['timeout'] : self::$_defaultTTL;
      self::$_manager->init(self::$_params);
    }

    return self::$_manager;
  }

  protected static function _computeCacheId( $baseString, $dataset) {

    return self::getUniqueId() . '-' . $dataset . '-' .hash( 'sha1', $baseString);
  }

}
