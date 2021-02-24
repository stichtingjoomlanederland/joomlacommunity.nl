<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
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

	public static function getInstance()
	{
		static $instance = null;

		if (!$instance) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * Detects if the string is a json parseable string
	 *
	 * @since  1.2
	 * @access public
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
}
