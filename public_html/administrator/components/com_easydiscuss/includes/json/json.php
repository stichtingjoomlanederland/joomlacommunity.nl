<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussJSON
{
	private $json = null;

	/**
	 * Object initialisation for the class to fetch the appropriate user
	 * object.
	 *
	 * @since	1.0
	 * @access	public
	 * @param   null
	 * @return  SocialToolbar
	 */
	public static function getInstance()
	{
		static $instance = null;

		if (!$instance) {
			$instance = new self();
		}

		return $instance;
	}

	public function encode($data, $loose = 0)
	{
		return json_encode($data);
	}

	/**
	 * Decodes a json string to an object
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string		The json string data
	 * @return
	 */
	public function decode($data)
	{
		if (empty($data)) {
			return false;
		}

		$pattern = '#^\s*//.+$#m';
		$data = preg_replace($pattern, '', $data);

		$result = json_decode($data);

		if (!$result) {
			// the data might have html entities that breaking the json decode. letg strips the html tag
			$result = json_decode(strip_tags($data));
		}

		return $result;
	}

	/**
	 * Detects if the string is a json parseable string
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @param  string    $string The string to check
	 * @return boolean           True if the string is JSON parseable
	 */
	public function isJsonString($string)
	{
		if (!is_string($string) || empty($string)) {
			return false;
		}

		$pattern = '#^\s*//.+$#m';
		$data = trim(preg_replace($pattern, '', $string));

		if((substr($data, 0, 1) === '{' && substr($data, -1, 1) === '}') || (substr($data, 0, 1) === '[' && substr($data, -1, 1) === ']')) {
			return true;
		}

		return false;
	}

	/**
	 * Renders the json data back to the caller.
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function send($data)
	{
		// For json responses, "application/json; charset=utf-8" is the standard content type.
		// Using "application/json" causes IE9 to download the response as a file.
		// Using "text/html" causes unterminated string literal when parsing json response in IE9.
		// Using "text/plain" causes Firebug not to syntax highlight json response.
		// Using anything other than "application/json" causes older Chrome to make warnings that the content-type is obselete.
		header('Content-type: text/plain; UTF-8');
		echo ES::makeJSON($data);
		exit;
	}
}
