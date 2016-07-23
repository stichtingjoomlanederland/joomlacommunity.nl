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

// no direct access
defined( '_JEXEC' ) or die;

class ShlSystem_Email {

  const PRIO_NORMAL = 0;
  const PRIO_SHOULD_ALREADY_BE_THERE = 127;
  const PRIO_COOL_DOWN_NO_RUSH = -128;
  const PRIO_RUN_BABY_RUN = 64;

  const STATUS_PENDING = 0;
  const STATUS_SUSPENDED = 1;
  const STATUS_RUNNING = 2;
  const STATUS_DONE = 3;

  /**
   * Send out an email
   *
   * @access  public
   * @param array $params array containing the various parts of the message
   * @param boolean noLog: if true, no attempt is made to log errors. This is in case the logger is itself trying to send an email, this cause an infinite loop
   * @return  boolean true on success
   */
  public static function send( $params = array(), $noLog = false) {

    $app = JFactory::getApplication();

    //load the language strings
    JPlugin::loadLanguage( 'plg_system_shlib', JPATH_ADMINISTRATOR);

    // check provided email parts, and supply defaults

    $params['html'] = (!isset($params['html']) || $params['html'] == true) ? true : false;
    $params['mailfrom'] = empty($params['mailfrom']) ? $app->getCfg('mailfrom') : $params['mailfrom'];
    $params['fromname'] = empty($params['fromname']) ? $app->getCfg('fromname') : $params['fromname'];
    $params['subject'] = empty($params['subject']) ? JText::sprintf('PLG_SHLIB_EMAIL_DEFAULT_SUBJECT', $app->getCfg('sitename')) : $params['subject'];
    $params['recipient'] = empty($params['recipient']) ?  $app->getCfg('mailfrom') : $params['recipient'];
    $body = empty($params['body']) ? JText::sprintf('PLG_SHLIB_EMAIL_DEFAULT_BODY', $app->getCfg('sitename')) : $params['body'];
    if(empty( $params['recipient'])) {
      $body .= ($html ? '<br /><br />' : "\n\n") . 'This message was dispatched without a recipient email address properly set. It is forwarded to you, but this is probably an internal error condition.';
    }
    $params['body'] = $body;
    $params['cc'] = empty($params['cc']) ? null : $params['cc'];
    $params['bcc'] = empty($params['bcc']) ? null : $params['bcc'];

    // priority: normal is 0. Positive has higher priority, negative has lower priority
    $params['priority'] = empty($params['priority']) ? self::PRIO_NORMAL : $params['priority'];

    // check if message sending should be deferred
    $params['deferredTo'] = empty( $params['deferredTo']) ? '0000-00-00 00:00:00' : $params['deferredTo'];

    // default is 'local', will use Joomla's framework to send out email
    $result = self::_sendEmail( $params);

    if (is_array($result)) {
      if(!$noLog) {
        ShlSystem_Log::error( 'shlib', $result['message']);
      }
      return false;
    }

    return $result;

  }

  protected static function _sendEmail( & $message ) {

    static $cannotSend = false;

    // check if message sending should be deferred : we can't do that
    // so for these message, we'll just do nothing, and let
    // other plugins do the job
    if(!empty( $message['deferredTo']) && $message['deferredTo'] != '0000-00-00 00:00:00') {

      try {
        $defTo = new DateTime( $message['deferredTo']);
      } catch (Exception $e) {
        // this is an error, as defer to date is invalid. Return status
        $status = array( 'message' => 'Local emailer error sending to ' . (empty($message['recipient']) ? 'N/A' : $message['recipient']) . ': ' . $e->getMessage());
        return $status;
      }

      // we have a valid defer to date, leave it to others to handle, just log a debug message
      ShlSystem_Log::debug( 'shlib', 'Local emailer: receiving deferred message for ' . (empty($message['recipient']) ? 'N/A' : $message['recipient']) . ', deferred until ' . $message['deferredTo']. ' : not sending');

      // exit with successful message, as there was no actual error
      return true;
    }

    // send email. In case of error while sending, we have raised the $cannotSend flag, so as to avoid trying for a very
    // long time to send messages, while there is no chances this will succeed
    if (!$cannotSend) {
      // get framework class
      jimport('joomla.mail.mail');
      try {
        $status = JMail::sendMail($message['mailfrom'], $message['fromname'], $message['recipient'], $message['subject'], $message['body'], $message['html'], $message['cc'], $message['bcc'] );
      }catch (Exception $e) {
        $status = $e;
      }
    }

    // log error : exception cannot be used, as they would break the plugins chain
    if ($status instanceof Exception) {
      $code = $status->getCode();
      if ($code == 500) {
        $cannotSend = true;
      }
      $status = array( 'message' => 'Local emailer error sending to ' . (empty($message['recipient']) ? 'N/A' : $message['recipient']) . ': ' . $status->getMessage());
    } else {
      $status = true;
    }

    // return status
    return $status;

  }


}

