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

class EasyDiscussIntegrate extends EasyDiscuss
{
	private $default = [
		'avatarLink' => '',
		'profileLink' => '#'
	];

	public function __construct()
	{
		parent::__construct();

		$this->default['avatarLink'] = DISCUSS_JURIROOT . '/media/com_easydiscuss/images/default_avatar.png';
	}

	/**
	 * Retrieves the profile and the avatar link for a user
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getField($profile = null, $isThumb = true)
	{
		// Unknown or guest users, uses default properties
		if (is_null($profile) || !is_object($profile) || !isset($profile->id) || $profile->id == 0) {
			return $this->default;
		}

		static $cache = array();

		$index = $profile->id . (int) $isThumb;

		if (!isset($cache[$index])) {
			$integration = strtolower($this->config->get('layout_avatarIntegration', 'default'));

			// Default to easydiscuss method
			if (!method_exists($this, $integration)) {
				$integration = 'easydiscuss';
			}

			$socialFields = $this->$integration($profile, $isThumb);

			if (empty($socialFields) || empty($socialFields[0]) || empty($socialFields[1])) {
				$socialFields = $this->easydiscuss($profile);
			}

			$editProfileLink = '';

			if (isset($socialFields[2]) && $socialFields[2]) {
				$editProfileLink = $socialFields[2];
			}

			$avatarData = [
				'integration' => $integration,
				'avatarLink' => $socialFields[0], 
				'profileLink' => $socialFields[1], 
				'editProfileLink' => $editProfileLink
			];

			$field[$index] = $avatarData;
		}

		return $field[$index];
	}

	/**
	 * Default profile system
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	private function easydiscuss($profile)
	{
		$legacy	= ($profile->avatar == 'default_avatar.png' || $profile->avatar == 'default.png' || $profile->avatar == 'media/com_easydiscuss/images/default.png' || empty($profile->avatar));

		$avatarLink	= $legacy ? 'media/com_easydiscuss/images/default_avatar.png' : ED::image()->getAvatarRelativePath() . '/' . $profile->avatar;

		$avatarLink = rtrim(DISCUSS_JURIROOT, '/') . '/' . $avatarLink;
		$profileLink = EDR::_('index.php?option=com_easydiscuss&view=profile&id='. $profile->id, false);

		return array($avatarLink, $profileLink);
	}

	/**
	 * JSN Profile avatar syste
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	private function jsn($profile)
	{
		$file = JPATH_ROOT . '/components/com_jsn/helpers/helper.php';

		if (!JFile::exists($file)) {
			return false;
		}

		require_once($file);

		$app = JFactory::getApplication(); 
		$menu = $app->getMenu();

		$user = JsnHelper::getUser($profile->id);

		// Retrieve the default avatar from the user
		$avatarImg = JsnHelper::defaultAvatar();

		if (isset($user->avatar) && $user->avatar) {
			$avatarImg = $user->avatar;
		}

		$avatarLink = '/' . $avatarImg;	

		// check the EasyProfile is it got created user profile menu item
		$menuItem = $menu->getItems('link', 'index.php?option=com_jsn&view=profile', true);

		$profileLink = JRoute::_('index.php?option=com_jsn&view=profile&id=' . $profile->id, false);

		if (!empty($menuItem->id)) {
			$profileLink = JRoute::_('index.php?option=com_jsn&view=profile&id=' . $profile->id . '&Itemid=' . $menuItem->id, false);
		}

		return array($avatarLink, $profileLink);
	}
	
	/**
	 * Renders profile integration with EasySocial
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	private function easysocial($profile)
	{ 
		$exists = ED::easysocial()->exists();

		if (!$exists) {
			return false;
		}

		$avatarLink = ES::user($profile->id)->getAvatar(SOCIAL_AVATAR_MEDIUM);
		$profileLink = ES::user($profile->id)->getPermalink();

		$editProfileLink = '';

		if ($this->config->get('integration_easysocial_toolbar_profile')) {
			$editProfileLink = ESR::profile(array('layout' => 'edit'));
		}

		return array($avatarLink, $profileLink, $editProfileLink);
	}

	private function jomwall($profile, $isThumb = true)
	{
		$file = JPATH_ROOT . '/components/com_awdwall/helpers/user.php';

		if (!JFile::exists($file)) {
			return false;
		}

		require_once($file);

		$avatarLink = AwdwallHelperUser::getBigAvatar51($profile->id);
		$Itemid = AwdwallHelperUser::getComItemId();
		$profileLink = AwdwallHelperUser::getUserProfileUrl($profile->id,$Itemid);

		$editProfileLink = JRoute::_('index.php?option=com_awdwall&view=mywall&wuid='. $profile->id .'&Itemid=' . $Itemid, false);

		return array($avatarLink, $profileLink, $editProfileLink);
	}

	private function k2($profile)
	{
		$file1 = JPATH_ROOT . '/components/com_k2/helpers/route.php';
		$file2 = JPATH_ROOT . '/components/com_k2/helpers/utilities.php';

		if (!JFile::exists($file1) || !JFile::exists($file2)) {
			return false;
		}

		require_once($file1);
		require_once($file2);

		$db = ED::db();
		$query = 'SELECT * FROM ' . $db->nameQuote('#__k2_users') . ' '
				. 'WHERE ' . $db->nameQuote('userID') . '=' . $db->Quote($profile->id);

		$db->setQuery($query);
		$result	= $db->loadObject();

		if (!$result || !$result->image) {
			return false;
		}

		$avatarLink = DISCUSS_JURIROOT . '/media/k2/users/' . $result->image;
		$profileLink = K2HelperRoute::getUserRoute($profile->id);

		return array( $avatarLink, $profileLink );
	}

	public function jfbconnect($profile)
	{
		$jfbconnect = ED::jfbconnect();

		if (!$jfbconnect->exists()) {
			return false;
		}

		$table = ED::table('profile');

		$jfbcUser = $table->getJfbconnectUserDetails($profile->id);

		if (!$jfbcUser) {
			return false;
		}

		// Get avatar
		$avatar = JFBCFactory::provider($jfbcUser->provider)->profile->getAvatarUrl($jfbcUser->id,false,null);

		// Get profile link
		$params = new JRegistry();
		$params->loadString($jfbcUser->params);
		$profileLink = $params->get('profile_url');

		return array($avatar, $profileLink);
	}

	private function jomsocial($profile, $isThumb = true)
	{
		if (!ED::jomsocial()->exists()) {
			return false;
		}

		$user = CFactory::getUser($profile->id);
		$avatarLink = ($isThumb) ? $user->getThumbAvatar() : $user->getAvatar();

		$profileLink = CRoute::_('index.php?option=com_community&view=profile&userid=' . $profile->id);
		$editProfileLink = CRoute::_('index.php?option=com_community&view=profile&task=edit');

		return array($avatarLink, $profileLink, $editProfileLink);
	}

	private function kunena($profile)
	{
		if (!class_exists('KunenaFactory')) {
			return false;
		}

		$userKNN = KunenaFactory::getUser($profile->id);
		$avatarLink = $userKNN->getAvatarURL('kavatar');

		$profileKNN = KunenaFactory::getProfile($profile->id);
		$profileLink = $profileKNN->getProfileURL($profile->id, '');
		$editProfileLink = $profileKNN->getEditProfileURL($profile->id);

		return array($avatarLink, $profileLink, $editProfileLink);
	}

	private function communitybuilder($profile, $isThumb = true)
	{
		$files = JPATH_ROOT . '/administrator/components/com_comprofiler/plugin.foundation.php';

		if (!JFile::exists($files)) {
			return false;
		}

		require_once( $files );

		cbimport('cb.database');
		cbimport('cb.tables');
		cbimport('cb.tabs');

		global $_CB_framework;

		$user = CBuser::getInstance($profile->id);

		if (!$user) {
			$user = CBuser::getInstance( null );
		}

		// Prevent CB from adding anything to the page.
		ob_start();
		$source = $user->getField('avatar' , null , 'php');
		$reset = ob_get_contents();
		ob_end_clean();
		unset($reset);

		$avatarLink = $source['avatar'];

		if (!$isThumb) {
			$avatarLink = str_ireplace('tn' , '' ,$avatarLink);
		}

		$profileLink = $_CB_framework->userProfileUrl($profile->id);

		$editProfileLink = $this->config->get('integration_cb_edit_profile') ? $_CB_framework->userProfileEditUrl() : EDR::_('index.php?option=com_easydiscuss&view=profile&id='. $profile->id, false);;

		return [$avatarLink, $profileLink, $editProfileLink];
	}

	private function gravatar($profile)
	{
		$user = JFactory::getUser($profile->id);

		if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)
			|| isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ) {
			$avatarLink = 'https://secure.gravatar.com/avatar.php?gravatar_id=';
		} else {
			$avatarLink = 'http://www.gravatar.com/avatar.php?gravatar_id=';
		}

		$avatarLink = $avatarLink . md5($user->email) . '?s=160';
		$avatarLink = $avatarLink.'&d=wavatar';

		$profileLink = EDR::_('index.php?option=com_easydiscuss&view=profile&id='.$profile->id, false);

		return array($avatarLink, $profileLink);
	}

	private function easyblog($profile)
	{
		$file = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

		jimport('joomla.filesystem.file');

		if (!JFile::exists($file)) {
			return false;
		}

		require_once($file);

		$profileEB = EB::table('Profile');
		$profileEB->load($profile->id);

		$editProfileLink = EB::getEditProfileLink();

		return array($profileEB->getAvatar(), $profileEB->getPermalink(), $editProfileLink);
	}
}
