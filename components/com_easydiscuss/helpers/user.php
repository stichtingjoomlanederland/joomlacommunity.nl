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

class DiscussUserHelper
{
	public static function validateUserType($usertype)
	{
		$config	= DiscussHelper::getConfig();
		$acl	= DiscussHelper::getHelper('ACL', '0');

		switch($usertype)
		{
			case 'guest':
				$enable = $acl->allowed('add_reply', 0);
				break;
			case 'twitter':
				$enable = $config->get('integration_twitter_enable');
				break;
			case 'facebook':
				$enable = $config->get('integration_facebook_enable1');
				break;
			case 'linkedin':
				$enable = $config->get('integration_linkedin_enable1');
				break;
			default:
				$enable = false;
		}

		return $enable;
	}
}
