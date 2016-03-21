<?php
/*
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

// no direct access
defined('_JEXEC') or die('Restricted access');

class modJcLoginHelper
{
	public static function getReturnURL($params, $isLogged=false)
	{
		$type = empty($isLogged) ? 'login' : 'logout';

		if($itemid =  $params->get($type))
		{
			$app = JFactory::getApplication();
			$menu = $app->getMenu();
			$item = $menu->getItem($itemid);
			if ($item)
			{
				$url = $item->link;
			}
			else
			{
				// stay on the same page
				$uri = JFactory::getURI();
				$url = $uri->toString(array('path', 'query', 'fragment'));
			}
		}
		else
		{
			// Proceeed to the front page of EasyDiscuss.
			$itemid = DiscussRouter::getItemId();
			$url = JRoute::_('index.php?option=com_easydiscuss', false);
		}

		return base64_encode($url);
	}

	public static function getLoginStatus()
	{
		$user = JFactory::getUser();
		return (!empty($user->id)) ? true : false;
	}
}
