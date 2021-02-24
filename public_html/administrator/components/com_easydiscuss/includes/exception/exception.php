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

class EasyDiscussException extends Exception
{
	private static $codeMap = [
		ED_MSG_ERROR   => 400,
		ED_MSG_SUCCESS => 200,
		ED_MSG_WARNING => 200,
		ED_MSG_INFO    => 200
	];

	public function __construct($message, $type = ED_MSG_ERROR, $previous = null)
	{
		if (is_integer($type)) {
			$code = $type;
		}

		if (is_string($type)) {
			$code = isset(self::$codeMap[$type]) ? self::$codeMap[$type] : null;
		}

		if (is_array($type)) {
			$code = $type[0];
		}

		// Translate message so a user can pass in the language string directly.
		$message = JText::_($message);

		// Construct exception so we can retrieve the rest of the properties
		parent::__construct($message, $code, $previous);
	}
}