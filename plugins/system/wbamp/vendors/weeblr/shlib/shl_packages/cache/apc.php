<?php
/**
 * Shlib - programming library
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2016
 * @package     shlib
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     0.3.1.580
 * @date				2016-08-25
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
class ShlCache_Apc extends ShlSystem_Abstractdecorator {

  protected $_instanceParams = array();

  /**
   * Check if setup is correct, ie APC extension is loaded in php
   *
   * @throws ShlException
   */
  public function init( $params) {

    $this->_instanceParams = $params;
    $this->enabled = extension_loaded('apc');
    if(!$this->enabled) {
      ShlSystem_Log::error( 'shLib', __METHOD__ . ': APC extension not loaded, unable to create cache manager using it');
    }
    
    return true;

  }

  public function doRead( $id) {

    if($this->enabled) {
      $read = apc_fetch( $id);
      return $read;
    } else {
      throw new ShlException( __METHOD__ . ': trying to read from a disabled cache');
    }
  }

  public function doStore( $id, $value, $ttl) {

    if($this->enabled) {
      $stored = apc_store( $id, $value, $ttl);
      return $stored;
    } else {
      throw new ShlException( __METHOD__ . ': trying to write to a disabled cache');
    }
  }

  public function doRemove( $id) {

    if($this->enabled) {
      $removed = apc_delete( $id);
      return $removed;
    } else {
      throw new ShlException( __METHOD__ . ': trying to delete from a disabled cache');
    }
  }

  public function doClear( $group = 'user') {

    if($this->enabled) {
      $cleared = apc_clear_cache( $group);
      return $cleared;
    } else {
      throw new ShlException( __METHOD__ . ': trying to clear a disabled cache');
    }
  }

}
