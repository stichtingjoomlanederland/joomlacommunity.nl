<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');

class EasyDiscussJFBConnect extends EasyDiscuss
{
	public function exists()
	{
		jimport('joomla.filesystem.file');

		$file = JPATH_ADMINISTRATOR . '/components/com_jfbconnect/jfbconnect.php';

		if (JFile::exists($file) && class_exists('JFBCFactory')) {
			return true;
		}

		return false;
	}

	public function getButtons()
	{
		if (!$this->exists()) {
			return;
		}

		$providers = JFBCFactory::getAllProviders();
		$params = array('buttonSize' => 'icon_text_button');
		$buttons = '';

		foreach ($providers as $provider) {
			$buttons .= $provider->loginButton($params);
		}

		return $buttons;
	}
}
