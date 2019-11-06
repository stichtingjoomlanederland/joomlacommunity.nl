<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
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
	 */
	public static function avatar(DiscussProfile $user, $options = array(), $isAnonymous = false, $renderAvatarImageOnly = false)
	{
		$config = ED::config();

		$rank = isset($options['rank']) ? $options['rank'] : false;
		$role = isset($options['role']) ? $options['role'] : false;
		$status = isset($options['status']) ? $options['status'] : false;
		$size = isset($options['size']) ? $options['size'] : 'sm';

		// default to true
		$popbox = isset($options['popbox']) ? $options['popbox'] : $config->get('layout_avatar_popbox');

		// Render user profile permalink
		// Do not render the user permalink if that is guest user or anonymous post 
		$userPermalink = !$user->id || $isAnonymous ? 'javascript:void(0);' : $user->getPermalink();

		// If the Jomsocial messaging integration enabled, we need to initialize the script.
		$jomsocial = ED::jomsocial();

		// Only init if these requirements are meet
		if ($popbox && $config->get('integration_jomsocial_messaging') && $jomsocial->exists()) {
			$jomsocial->init();
		}

		$easysocialPopbox = false;

		// Check for easysocial popbox.
		if ($config->get('integration_easysocial_popbox')) {
			$easysocial = ED::easysocial();

			if ($easysocial->exists()) {
				$easysocialPopbox = true;

				// Disabled default popbox
				$popbox = false;
			}
		}

		// If this is guest, do not display any popbox.
		if (!$user->id) {
			$popbox = false;
			$easysocialPopbox = false;
		}

		// Show text avatar name
		$textAvatarName = $user->getNameInitial($isAnonymous)->text;

		$theme = ED::themes();
		$theme->set('user', $user);
		$theme->set('rank', $rank);
		$theme->set('role', $role);
		$theme->set('status', $status);
		$theme->set('size', $size);
		$theme->set('popbox', $popbox);
		$theme->set('isAnonymous', $isAnonymous);
		$theme->set('userPermalink', $userPermalink);
		$theme->set('textAvatarName', $textAvatarName);
		$theme->set('easysocialPopbox', $easysocialPopbox);
		$theme->set('renderAvatarImageOnly', $renderAvatarImageOnly);

		$output = $theme->output('site/html/user.avatar');

		return $output;
	}

	/**
	 * Generates a user name and permalink
	 *
	 * @since	4.1.12
	 * @access	public
	 */
	public static function username(DiscussProfile $user, $options = array())
	{
		$config = ED::config();

		$isAnonymous = isset($options['isAnonymous']) ? $options['isAnonymous'] : false;
		$canViewAnonymousUsername = isset($options['canViewAnonymousUsername']) ? $options['canViewAnonymousUsername'] : false;
		$posterName = isset($options['posterName']) ? $options['posterName'] : '';

		// t-fs--md font size
		$fontsize = isset($options['fontsize']) ? $options['fontsize'] : 'md';

		// t-lg-mt--md = trumps-largescreen-margintop--mediumSpaces
		$lgMarginTop = isset($options['lgMarginTop']) ? 't-lg-mt--md' : '';

		// t-lg-mb--md = trumps-largescreen-marginBottom--mediumSpaces
		$lgMarginBottom = isset($options['lgMarginBottom']) ? 't-lg-mb--sm' : '';

		$isESVerified = false;
		$username = $user->getName($posterName);
		$permalink = $user->getPermalink();

		// show Easysocial verification icon from the user name
		if (ED::easysocial()->exists() && $config->get('layout_avatarLinking') && $config->get('layout_avatarIntegration', 'default') == 'easysocial') {

			// Only execute this if that is register user
			if ($user->id) {
				$esUser = ES::user($user->id);
				$isESVerified = $esUser->isVerified();
			}
		}

		// do not render any user permalink if that owner post is anonymous
		if ($isAnonymous) {

			$isESVerified = false;

			if ($canViewAnonymousUsername) {
				$username = JText::_('COM_EASYDISCUSS_ANONYMOUS_USER') . '(' . $user->getName($posterName) . ')';

			} else {
				$username = JText::_('COM_EASYDISCUSS_ANONYMOUS_USER');
				$permalink = "javascript:void(0);";
			}
		}

		$theme = ED::themes();
		$theme->set('username', $username);
		$theme->set('permalink', $permalink);
		$theme->set('isESVerified', $isESVerified);
		$theme->set('lgMarginTop', $lgMarginTop);
		$theme->set('lgMarginBottom', $lgMarginBottom);
		$theme->set('fontsize', $fontsize);

		$output = $theme->output('site/html/user.name');

		return $output;
	}	

	/**
	 * Generates anonymous user html tag
	 *
	 * @since	4.1.9
	 * @access	public
	 */
	public static function anonymous(DiscussProfile $user, $isAnonymous = false, $options = array())
	{
		$config = ED::config();

		$size = isset($options['size']) ? $options['size'] : 'sm';

		$showProfileImage = $config->get('layout_avatar');

		// show text avatar name
		$textAvatarName = $user->getNameInitial($isAnonymous)->text;

		$theme = ED::themes();
		$theme->set('user', $user);
		$theme->set('size', $size);
		$theme->set('isAnonymous', $isAnonymous);
		$theme->set('textAvatarName', $textAvatarName);
		$theme->set('showProfileImage', $showProfileImage);

		$output = $theme->output('site/html/user.anonymous');

		return $output;
	}	
}
