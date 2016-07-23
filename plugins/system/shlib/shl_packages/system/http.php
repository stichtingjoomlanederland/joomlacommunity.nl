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

class ShlSystem_Http {

  // return code
  const RETURN_OK = 200;
  const RETURN_BAD_REQUEST = 400;
  const RETURN_UNAUTHORIZED = 401;
  const RETURN_FORBIDDEN = 403;
  const RETURN_NOT_FOUND = 404;
  const RETURN_PROXY_AUTHENTICATION_REQUIRED = 407;
  const RETURN_SERVICE_UNAVAILABLE = 503;


  public static function abort( $code = self::RETURN_NOT_FOUND, $cause = '') {

    $header = self::getHeader( $code, $cause);

    // clean all buffers
    ob_end_clean();

    $msg = empty($cause) ? $header->msg : $cause;
    if (!headers_sent()) {
      header( $header->raw);
    }
    die($msg);
  }

  public static function getHeader( $code, $cause) {

    $code = intval( $code);
    $header = new stdClass();

    switch ($code) {
       
      case self::RETURN_BAD_REQUEST:
        $header->raw = 'HTTP/1.0 400 BAD REQUEST';
        $header->msg = '<h1>Unauthorized</h1>';
        break;
      case self::RETURN_UNAUTHORIZED:
        $header->raw = 'HTTP/1.0 401 UNAUTHORIZED';
        $header->msg = '<h1>Unauthorized</h1>';
        break;
      case self::RETURN_FORBIDDEN:
        $header->raw = 'HTTP/1.0 403 FORBIDDEN';
        $header->msg = '<h1>Forbidden access</h1>';
        break;
      case self::RETURN_NOT_FOUND:
        $header->raw = 'HTTP/1.0 404 NOT FOUND';
        $header->msg = '<h1>Page not found</h1>';
        break;
      case self::RETURN_PROXY_AUTHENTICATION_REQUIRED:
        $header->raw = 'HTTP/1.0 407 PROXY AUTHENTICATION REQUIRED';
        $header->msg = '<h1>Proxy authentication required</h1>';
        break;
      case self::RETURN_SERVICE_UNAVAILABLE:
        $header->raw = 'HTTP/1.0 503 SERVICE UNAVAILABLE';
        $header->msg = '<h1>Service unavailable</h1>';
        break;

      default:
        $header->raw = 'HTTP/1.0 ' . $code;
        $header->msg = $cause;
        break;
    }

    return $header;
  }

}
