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

class EasyDiscussThemesHelperUser
{
	/**
	 * Generates the user's role html tag
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function role(DiscussProfile $user)
	{
		$theme = ED::themes();
		$theme->set('user', $user);

		$output = $theme->output('site/html/user.role');

		return $output;
	}

	/**
	 * Generates the private messaging button for the user
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public static function pm($targetId = null, $layout = 'list')
	{
		$config = ED::config();
		$acl = ED::acl();
		$my = JFactory::getUser();

		// Guests cannot use PM feature
		if ($my->guest || !$config->get('main_conversations') || !$acl->allowed('allow_privatemessage')) {
			return;
		}

		// They shouldn't be able to pm themselves
		if ($my->id == $targetId) {
			return;
		}

		// If configured to use Jomsocial, use the html provided by Jomsocial
		$jomsocial = ED::jomsocial();
		if ($config->get('integration_jomsocial_messaging') && $jomsocial->exists()) {
			
			$output = $jomsocial->getPmHtml($targetId, $layout);

			return $output;
		}

		// If configured to use EasySocial, use the html provided by EasySocial
		$easysocial = ED::easysocial();
		if ($config->get('integration_easysocial_messaging') && $easysocial->exists()) {
			$output = $easysocial->getPmHtml($targetId, $layout);

			return $output;
		}

		$user = ED::user($targetId);

		$theme = ED::themes();
		$theme->set("user", $user);
		
		$namespace = $layout == 'list' ? 'user.pm' : 'user.popbox.pm';

		$output = $theme->output('site/html/' . $namespace);

		return $output;
	}

	/**
	 * Generates a user avatar html tag
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function avatar(DiscussProfile $user, $options = array())
	{
		$config = ED::config();

		$rank = isset($options['rank']) ? $options['rank'] : false;
		$role = isset($options['role']) ? $options['role'] : false;
		$status = isset($options['status']) ? $options['status'] : false;
		$size = isset($options['size']) ? $options['size'] : 'sm';

		// default to true
		$popbox = isset($options['popbox']) ? $options['popbox'] : true;

		// If the Jomsocial messaging integration enabled, we need to initialize the script.
		$jomsocial = ED::jomsocial();

		// Only init if these requirements are meet
		if ($popbox && $config->get('integration_jomsocial_messaging') && $jomsocial->exists()) {
			$jomsocial->init();
		}

		$theme = ED::themes();
		$theme->set('user', $user);
		$theme->set('rank', $rank);
		$theme->set('role', $role);
		$theme->set('status', $status);
		$theme->set('size', $size);
		$theme->set('popbox', $popbox);

		$output = $theme->output('site/html/user.avatar');

		return $output;
	}
}
