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

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die;

class ShlSystem_Abstractdecorator {

  protected $_decorated;
  protected $_decoratedClass;
  protected $_decoratedIsDecorator;

  public function __construct( $decoratedObject) {

    $this->_decorated = $decoratedObject;
    $this->_decoratedClass = get_class( $decoratedObject);
    $this->_decoratedIsDecorator = is_subclass_of( $this->_decoratedClass, __CLASS__);
  }

  public function __call( $method, $arguments) {

    // only call call_user_func_array if the decorated object method exists
    // or if the decorated object is itself a decorator. If the object is NOT
    // a decorator, then $this->_decorated->$method will not exist (otherwise
    // the first part of the OR test would have succeeded). This will cause php
    // to fire a warning "call_user_func_arra() invalid call back".
    // Instead, in such a case, we will simply fire an exception
    // if the decorated object is itself a decorator, then there is no problem
    // as it will have a __call() method, and call_user_func_array will be happy with
    // that, not firing any warning
    if ( method_exists( $this->_decorated, $method) || $this->_decoratedIsDecorator) {
      return call_user_func_array( array($this->_decorated, $method), $arguments);
    } else {
      // temporary (yeah, right) workaround for http://joomlacode.org/gf/project/joomla/tracker/?action=TrackerItemEdit&tracker_item_id=27923
      if($method == 'getError' && !empty($this->_decorated) && $this->_decorated instanceof JDatabase) {
        return false;
      }
      throw new ShlException( 'Method ' . $method . ' not defined');
    }
  }

  public function __set( $property, $value) {

    $this->_decorated->$property = $value;
  }

  public function __get( $property) {

    if (property_exists( $this->_decorated, $property) || $this->_decoratedIsDecorator) {
      return $this->_decorated->$property;
    } else {
      throw new ShlException('Trying to get non-existent property ('.(empty($property) ? 'N/A':$property).') for class ' . $this->_decoratedClass);
    }
  }

  public function __isset( $property) {

    return isset($this->_decorated->$property);
  }

  public function __unset( $property) {

    if (isset( $this->_decorated->$property) || $this->_decoratedIsDecorator) {
      unset( $this->_decorated->$property);
    }
  }


}
