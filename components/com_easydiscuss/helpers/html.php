<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

class DiscussHTMLHelper
{
	public static function get($key)
	{
		list($file, $function) = explode('.', $key);

		$className = 'DiscussHtml' . ucfirst($file);

		if (!class_exists($className)) {
			require_once DISCUSS_HELPERS . '/html/' . $file . '.php';
			if (!class_exists($className)) {
				// File or Class not found
				return false;
			}
		}

		if( is_callable(array($className, $function)) ) {
			$args = func_get_args();
			array_shift($args);

			// PHP 5.3 workaround
			$temp = array();
			foreach ($args as &$arg) {
				$temp[] = &$arg;
			}

			return call_user_func_array(array($className, $function), $temp);
		} else {
			// Function not supported
			return false;
		}
	}

	public static function escape($string) {
		return addslashes(htmlspecialchars($string, ENT_COMPAT, 'UTF-8'));
	}
}
