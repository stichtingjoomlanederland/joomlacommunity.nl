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

class EasyDiscussThemesHelperUser
{
	/**
	 * Generates a user avatar html tag
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function avatar(DiscussProfile $user, $options = array(), $isAnonymous = false, $renderAvatarImageOnly = false)
	{
		$config = ED::config();

		$hyperlink = ED::normalize($options, 'hyperlink', true);
		$useCache = ED::normalize($options, 'useCache', true);
		$rank = ED::normalize($options, 'rank', false);
		$role = ED::normalize($options, 'role', false);
		$status = ED::normalize($options, 'status', false);
		$size = ED::normalize($options, 'size', 'sm');
		$popbox = ED::normalize($options, 'popbox', true);
		$customClasses = ED::normalize($options, 'customClasses', '');

		// Translate avatar size from
		// large => lg
		// medium => md
		$size = $this->translateSize($size);

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
		if ($popbox && $config->get('integration_easysocial_popbox')) {
			$easysocial = ED::easysocial();

			if ($easysocial->exists()) {
				$easysocialPopbox = true;

				// Disabled default popbox
				$popbox = false;
			}
		}

		// If this is guest, do not display any popbox.
		if (!$user->id || $isAnonymous) {
			$popbox = false;
			$easysocialPopbox = false;
		}

		// Show text avatar name
		$textAvatarName = '';

		if ($config->get('layout_text_avatar')) {
			$textAvatarName = $user->getNameInitial($isAnonymous)->text;
		}

		$theme = ED::themes();
		$theme->set('useCache', $useCache);
		$theme->set('hyperlink', $hyperlink);
		$theme->set('user', $user);
		$theme->set('rank', $rank);
		$theme->set('role', $role);
		$theme->set('status', $status);
		$theme->set('size', $size);
		$theme->set('popbox', $popbox);
		$theme->set('customClasses', $customClasses);
		$theme->set('isAnonymous', $isAnonymous);
		$theme->set('userPermalink', $userPermalink);
		$theme->set('textAvatarName', $textAvatarName);
		$theme->set('easysocialPopbox', $easysocialPopbox);
		$theme->set('renderAvatarImageOnly', $renderAvatarImageOnly);

		$output = $theme->output('site/html/user/avatar');

		return $output;
	}

	/**
	 * Renders the profile header of a user
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function header(DiscussProfile $user, $options = [])
	{
		$displayStatistics = ED::normalize($options, 'displayStatistics', false);

		$theme = ED::themes();
		$theme->set('displayStatistics', $displayStatistics);
		$theme->set('user', $user);

		$output = $theme->output('site/helpers/user/header');

		return $output;
	}

	/**
	 * Generates the user's role html tag
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function role(DiscussProfile $user)
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
	public function pm($targetId = null, $layout = 'list')
	{
		$config = ED::config();
		$acl = ED::acl();
		$my = JFactory::getUser();

		// Guests cannot use PM feature
		if ($my->guest || !$acl->allowed('allow_privatemessage')) {
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

		// If easysocial is enabled, always use the conversation from easysocial
		$easysocial = ED::easysocial();
		
		if ($config->get('integration_easysocial_messaging') && $easysocial->exists()) {
			$output = $easysocial->getPmHtml($targetId, $layout);

			return $output;
		}

		if (!$config->get('main_conversations')) {
			return;
		}

		$user = ED::user($targetId);

		$theme = ED::themes();
		$theme->set("user", $user);
		
		$namespace = $layout == 'list' ? 'user.pm' : 'user.popbox.pm';

		$output = $theme->output('site/html/' . $namespace);

		return $output;
	}

	/**
	 * Generates a user name and permalink
	 *
	 * @since	4.1.12
	 * @access	public
	 */
	public function username(DiscussProfile $user, $options = array())
	{
		$config = ED::config();

		$popbox = ED::normalize($options, 'popbox', false);
		$isAnonymous = ED::normalize($options, 'isAnonymous', false);
		$canViewAnonymousUsername = ED::normalize($options, 'canViewAnonymousUsername', false);
		$posterName = ED::normalize($options, 'posterName', '');
		$hyperlink = ED::normalize($options, 'hyperlink', true);

		$isESVerified = false;
		$username = $user->getName($posterName);
		$permalink = $user->getPermalink();

		// show Easysocial verification icon from the user name
		if (ED::easysocial()->exists() && $config->get('layout_avatarIntegration', 'default') == 'easysocial') {

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
		$theme->set('popbox', $popbox);
		$theme->set('user', $user);
		$theme->set('username', $username);
		$theme->set('permalink', $permalink);
		$theme->set('isESVerified', $isESVerified);

		$namespace = $hyperlink ? 'name.hyperlink' : 'name.text';
		$output = $theme->output('site/html/user/' . $namespace);

		return $output;
	}	

	/**
	 * Generates anonymous user html tag
	 *
	 * @since	4.1.9
	 * @access	public
	 */
	public function anonymous(DiscussProfile $user, $isAnonymous = false, $options = array())
	{
		$config = ED::config();

		$size = ED::normalize($options, 'size', 'sm');

		$showProfileImage = !$config->get('layout_text_avatar');

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

	/**
	 * Contains a dictionary of size mapping
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	private function translateSize($size)
	{
		if ($size == 'xlarge') {
			return 'xl';
		}

		if ($size == 'large') {
			return 'lg';
		}

		if ($size == 'medium') {
			return 'md';
		}

		if ($size == 'small') {
			return 'sm';
		}

		if ($size == 'xsmall') {
			return 'xs';
		}

		return $size;
	}
}
