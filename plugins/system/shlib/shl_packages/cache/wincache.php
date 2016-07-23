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
 * Implements read and store methods
 * to decorate an ShlCache_Manager
 *
 * @author yannick
 *
 */
class ShlCache_Wincache extends ShlSystem_Abstractdecorator {

  protected $_instanceParams = array();

  /**
   * Check if setup is correct, ie APC extension is loaded in php
   *
   * @throws ShlException
   */
  public function init($params) {

    $this->_instanceParams = $params;
    $this->enabled =  extension_loaded('wincache');
    if(!$this->enabled) {
      ShlSystem_Log::error( 'shlib', __METHOD__ . ': WinCache extension not loaded, unable to create cache manager using it');
    }
    
    return true;

  }

  public function doRead( $id) {

    if($this->enabled) {
      $read = wincache_ucache_get( $id);
      return $read;
    } else {
      throw new ShlException( __METHOD__ . ': trying to read from a disabled cache');
    }
  }

  public function doStore( $id, $value, $ttl) {

    if($this->enabled) {
      $stored = wincache_ucache_set( $id, $value, $ttl);
      return $stored;
    } else {
      throw new ShlException( __METHOD__ . ': trying to write to a disabled cache');
    }

  }

  public function doRemove( $id) {

    if($this->enabled) {
      $removed = wincache_ucache_delete( $id);
      return $removed;
    } else {
      throw new ShlException( __METHOD__ . ': trying to delete from a disabled cache');
    }
  }

  public function doClear( $group = 'user') {

    if($this->enabled) {
      $cleared = wincache_ucache_clear();
      return $cleared;
    } else {
      throw new ShlException( __METHOD__ . ': trying to clear a disabled cache');
    }
  }

}
