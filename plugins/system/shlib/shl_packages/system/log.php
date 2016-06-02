<?php
/**
 * Shlib - programming library
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2015
 * @package     shlib
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     0.3.1.522
 * @date				2016-05-17
 */

// no direct access
defined( '_JEXEC' ) or die;

class ShlSystem_Log {

  const DEBUG = 128;
  const INFO = 64;
  const ERROR = 8;
  const ALERT = 2;

  // log files types, used to select file type for backend display
  const TYPE_GENERAL = 1;
  const TYPE_ERRORS = 2;
  const TYPE_DEBUG = 3;
  const TYPE_ALERTS = 4;

  const TYPE_PURCHASES = 20;


  // levels text message for inclusion in log files
  static public $textLevels = array( self::DEBUG => 'debug', self::INFO => 'info', self::ERROR => 'error', self::ALERT => 'alert');

  // list of levels that must be logged (empty array will disabled logging)
  static protected $_logLevel = array( self::INFO, self::ERROR, self::ALERT, self::DEBUG);

  // list of levels that must be notified
  static protected $_notifyLevel = array( self::ALERT);

  // target email address(es) for notification
  static protected $_notifyTarget = '';

  /**
   * Store configuration, provided by main process
   *
   * @param array $config
   */
  public static function setConfig( $config) {

    if(isset( $config['logLevel'])) {
      self::$_logLevel = $config['logLevel'];
    }

    if(isset( $config['notifyLevel'])) {
      self::$_notifyLevel = $config['notifyLevel'];
    }

    if(isset( $config['notifyTarget'])) {
      self::$_notifyTarget = $config['notifyTarget'];
    }

  }

  public static function getFullConfig() {

    return array( 'logLevel' => self::$_logLevel, 'notifyLevel' => self::$_notifyLevel, 'notifyTarget' => self::$_notifyTarget);
  }

  /**
   * Get current configuration array
   *
   * @return array $config
   */
  public static function getConfig( $type) {

    if(empty( $type)) {
      return array();
    }

    $property = '_' . $type;
    if(!isset( self::$property)) {
      return array();
    }

    return self::$type;

  }

  /**
   * Write a message to a log system, usually a log file
   *
   * Deprecated, legacy code, to be removed
   *
   * @param string $file the target log file/log name
   * @param string $message message to be stored
   * @param string $status short code string, describing status of operation being loggged, such as OK, ERR, ...
   * @param string $level level of logging, only the messages logged with certain levels are actually logged
   */
  public static function log( $file, $message, $status = 'OK', $level = 'info') {

    switch ($level) {
      case 'debug':
        $level = self::DEBUG;
        break;
      case 'info':
        $level = self::INFO;
        break;
      case 'error':
        $level = self::ERROR;
        break;
      case 'alert':
        $level = self::ALERT;
        break;
    }

    $status = self::logCustom( $file, $level, array(), $message);

    return $status;

  }

  /**
   * Log a message with level Error
   *
   * @param string message
   * @param mixed various params to be sprintfed into the msg
   * @return boolean true if success
   */
  public static function error( $prefix) {

    $args = func_get_args();
    $d = array_shift( $args);

    return self::_log( 'errors', self::ERROR, array( 'category' => $prefix), $args);

  }

  /**
   * Deprecated, use ShlSystemLog::error( $prefix)
   */
  public static function logError() {

    $args = func_get_args();
    return self::_log( 'errors', self::ERROR, array( 'category' => 'error'), $args);

  }

  public static function alert( $prefix) {

    $args = func_get_args();
    $d = array_shift( $args);

    return self::_log( 'alerts', self::ALERT, array('category' => $prefix), $args);

  }

  /**
   * Deprecated, use ShlSystemLog::alert( $prefix)
   */
  public static function logAlert() {

    $args = func_get_args();

    return self::_log( 'alerts', self::ALERT, array('category' => 'alert'), $args);

  }

  public static function debug( $prefix) {

    $args = func_get_args();
    $d = array_shift( $args);

    return self::_log( 'debug', self::DEBUG, array('category' => $prefix), $args);

  }

  /**
   * Deprecated, use ShlSystemLog::debug( $prefix)
   */
  public static function logDebug( $prefix) {

    $args = func_get_args();

    return self::_log( 'debug', self::DEBUG, array('category' => 'debug'), $args);

  }

  public static function info( $prefix) {

    $args = func_get_args();
    $d = array_shift( $args);

    return self::_log( 'info', self::INFO, array('category' => $prefix), $args);

  }

  public static function logInfo() {

    $args = func_get_args();

    return self::_log( 'info', self::INFO, array('category' => 'info'), $args);

  }

  public static function custom( $prefix, $level, $category) {

  	$args = func_get_args();
  	$d = array_shift( $args);

  	return self::_log( $prefix, $level, array('category' => $category), $args);
  }

  /**
   * Deprecated, use ShlSystemLog::custom( $prefix)
   */
  public static function logCustom( $file, $level, $options) {

    $args = func_get_args();
    $d = array_shift( $args);  // remove file
    $d = array_shift( $args);  // remove level
    $d = array_shift( $args);  // remove options

    return self::_log( $file, $level, $options, $args);
  }

  public static function _log( $file, $level = self::INFO, $options, $args = null) {

    // nothing to do, go away asap
    if(!in_array( $level, self::$_logLevel) && !in_array( $level, self::$_notifyLevel)) {
      return true;
    }

    // something to do, process message
    if (count($args) > 1) {
      // use sprintf
      $message = call_user_func_array('sprintf', $args);
    } else {
      $message = $args[0];  // no variable parts, just use first element as a string
    }

    // results
    $logStatus = true;
    $notifyStatus = true;

    // include user details in logging
    $user = JFactory::getUser();
    $userString = empty( $user->id) ? 'guest' : $user->id . ' (' . $user->email . ')';

    // do logging
    if(in_array( $level, self::$_logLevel)) {
      // note: cannot use Exceptions here, as one plugin throwing an exception
      // would prevent other plugins to be fired
      $params = array( 'file' => $file, 'priority' => $level, 'type' => $level . '-' . self::$textLevels[$level], 'user' => $userString, 'message' => $message);
      // merge in additional options set by caller
      // include: format and timestamp
      if(is_array( $options)) {
        $params = array_merge( $params, $options);
      }
      $logStatus = self::_logToFile($params);
    }

    // do notifying
    if(in_array( $level, self::$_notifyLevel)) {
      // check if we're supposed to send notification to the site admin
      // @TODO: target emails for notifications should be configurable
      // should be able to send to more than one, at least CC or BCC

      $app = JFactory::getApplication();

      // prepare standard email based on template
      $ip = empty($_SERVER['REMOTE_ADDR']) ? 'N/A' : $_SERVER['REMOTE_ADDR'];
      //load the language strings
      JPlugin::loadLanguage( 'plg_system_shlib', JPATH_ADMINISTRATOR);
      $subject = JText::sprintf( 'PLG_SHLIB_ADMIN_ALERT_SUBJECT', $app->getCfg( 'sitename'));
      $body = JText::sprintf( 'PLG_SHLIB_ADMIN_ALERT_BODY', $app->getCfg( 'fromname'), $app->getCfg( 'sitename'), $message, $userString, $ip);
      $emailParams = array( 'body' => $body, 'subject' => $subject);

      // find about recipient(s), if any
      if(!empty(self::$_notifyTarget)) {
        $emailParams['cc'] = explode( ',', self::$_notifyTarget);
      }

      // use email helper
      $notifyStatus = ShlSystem_email::send( $emailParams, $noLog = true);
    }

    return $logStatus && $notifyStatus;
  }

  protected static function _logToFile( $params) {

    // check params
    $defaultParams = array(
        'file' => 'info'
        , 'category' => 'shLib'
        , 'date' => ShlSystem_Date::getSiteNow( 'Y-m-d')
        , 'time' => ShlSystem_Date::getSiteNow( 'H:i:s')
        , 'message' => 'No logging message, probably an error'
        , 'user' => '-'
        , 'priority' => self::INFO
        , 'text_entry_format' => "{DATE}\t{TIME}\t{TYPE}\t{C-IP}\t{USER}\t{MESSAGE}"
        , 'timestamp' => ShlSystem_Date::getSiteNow( 'Y-m-d')
        , 'prefix' => 'shlib');

    $liveParams = array_merge( $defaultParams, $params);

    $options = array();
    $options['text_file'] = $liveParams['category'] . '/' . $liveParams['file'] . '/log_'. $liveParams['file']. '.' . $liveParams['timestamp'] . '.log.php';
    $options['text_entry_format'] = $liveParams['text_entry_format'];
    jimport( 'joomla.error.log');
    JLog::addLogger( $options, $liveParams['priority'], $categories = array($liveParams['category']));

    // create an entry for the log file
    $entry = new JLogEntry( '');
    foreach( $liveParams as $key => $value) {
      $entry->$key = $value;
    }

    // and add it
    JLog::add( $entry);

    return true;
  }

}
