<?php
/**
 * Shlib - programming library
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2016
 * @package     shlib
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     0.3.1.587
 * @date				2016-10-31
 */

// Security check to ensure this file is being included by a parent file.
defined('_JEXEC') or die;

/**
 * Implements read and store methods
 * to decorate an ShlCache_Manager
 *
 * @author yannick
 *
 */
class ShlCache_Memcache extends ShlSystem_Abstractdecorator {

  const DEFAULT_HOST = '127.0.0.1';
  const DEFAULT_PORT =  11211;
  const DEFAULT_PERSISTENT = true;
  const DEFAULT_WEIGHT  = 1;
  const DEFAULT_TIMEOUT = 1;
  const DEFAULT_RETRY_INTERVAL = 15;
  const DEFAULT_COMPRESSION = 0;

  protected $_instanceParams = array();
  protected $_server = null;

  /**
   * Check if setup is correct, ie APC extension is loaded in php
   *
   * @throws ShlException
   */
  public function init( $params) {

    $this->_instanceParams = $params;

    // maybe we're trying to call this a second time?
    if( class_exists('Memcache') && $this->_server instanceof Memcache) {
      return true;
    }

    // first call, check setup and initialize server connection data
    $this->enabled = extension_loaded('memcache');
    if(!$this->enabled) {
      ShlSystem_Log::error( 'shlib', __METHOD__ . ': Memcache extension not loaded, unable to create cache manager using it');
    }

    // sort of validate params, using defaults if missing
    if($this->enabled) {
      $this->_instanceParams['host'] = empty($this->_instanceParams['host']) ? self::DEFAULT_HOST: $this->_instanceParams['host'];
      $this->_instanceParams['port'] = empty($this->_instanceParams['port']) ? self::DEFAULT_PORT: $this->_instanceParams['port'];
      $this->_instanceParams['persistent'] = empty($this->_instanceParams['persistent']) ? self::DEFAULT_PERSISTENT: $this->_instanceParams['persistent'];
      $this->_instanceParams['weight'] = empty($this->_instanceParams['weight']) ? self::DEFAULT_WEIGHT: $this->_instanceParams['weight'];
      $this->_instanceParams['timeout'] = empty($this->_instanceParams['timeout']) ? self::DEFAULT_TIMEOUT: $this->_instanceParams['timeout'];
      $this->_instanceParams['retry_interval'] = empty($this->_instanceParams['retry_interval']) ? self::DEFAULT_RETRY_INTERVAL: $this->_instanceParams['retry_interval'];
      $this->_instanceParams['enable_compression'] = empty($this->_instanceParams['enable_compression']) ? self::DEFAULT_COMPRESSION: $this->_instanceParams['enable_compression'];


      // now establish connection
      $this->_server = new Memcache;
      @$this->_server->addServer( $this->_instanceParams['host'], $this->_instanceParams['port'], $this->_instanceParams['persistent'], $this->_instanceParams['weight'], $this->_instanceParams['timeout'], $this->_instanceParams['retry_interval']);
      $this->enabled = 0 != @$this->_server->getServerStatus($this->_instanceParams['host'], $this->_instanceParams['port']);
      if(!$this->enabled) {
        ShlSystem_Log::error( 'shlib', __METHOD__ . ': Memcache server not responding at %s:%s', $this->_instanceParams['host'], $this->_instanceParams['port']);
      }
    }

    return true;

  }

  public function doRead( $id) {

    if($this->enabled) {
      $read = @$this->_server->get( $id);
      return $read;
    } else {
      throw new ShlException( __METHOD__ . ': trying to read from a disabled cache');
    }
  }

  public function doStore( $id, $value, $ttl) {

    if($this->enabled) {
      $stored =  @$this->_server->set( $id, $value, $this->_instanceParams['enable_compression'], $ttl);
      return $stored;
    } else {
      throw new ShlException( __METHOD__ . ': trying to write to a disabled cache');
    }
  }

  public function doRemove( $id) {

    if($this->enabled) {
      $removed = $this->_server->delete($id);
      return $removed;
    } else {
      throw new ShlException( __METHOD__ . ': trying to delete from a disabled cache');
    }
  }

  public function doClear() {

    if($this->enabled) {
      $cleared = $this->_server->flush();
      return $cleared;
    } else {
      throw new ShlException( __METHOD__ . ': trying to clear a disabled cache');
    }
  }

}
