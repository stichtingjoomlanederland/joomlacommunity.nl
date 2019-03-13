<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussScripts extends EasyDiscuss
{
	static $scripts = array();
	static $loaded = array();
	static $isLoaded = false;

	public function add($uid, $script, $unique = true)
	{
		// If this is a unique addition, we should ensure that it doesn't get added more than once
		if ($unique && in_array($uid, self::$loaded)) {
			return true;
		}

		self::$scripts[] = $script;
		self::$loaded[] = $uid;
	}

	public function exists($uid)
	{
		return in_array($uid, self::$loaded);
	}

	public function getScripts()
	{
		self::$isLoaded = true;
		return implode('', self::$scripts);
	}

	public function clearScripts()
	{
		if (self::$isLoaded) {
			self::$scripts = array();
			self::$isLoaded = false;
		}
		return true;
	}
}
