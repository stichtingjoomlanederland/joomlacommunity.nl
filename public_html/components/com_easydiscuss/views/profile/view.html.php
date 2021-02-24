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

require_once(__DIR__ . '/view.abstract.php');

class EasyDiscussViewProfile extends EasyDiscussViewProfileAbstract
{
	public function display($tmpl = null)
	{
		// Get the current user that should be displayed
		$id = $this->input->get('id', null, 'int');
		$user = JFactory::getUser($id);

		// Check if the user is allowed to view
		if (!$this->config->get('main_profile_public') && !$this->my->id) {
			ED::setMessage('COM_EASYDISCUSS_LOGIN_TO_VIEW_PROFILE');
			$redirect = EDR::_('view=index', false);
			return ED::redirect($redirect);
		}

		// Load the user's profile
		$profile = ED::user($user->id);

		// If profile is invalid, means the user is viewing from the user profile menu item and is not logged in yet.
		if (!$profile->id) {
			return ED::requireLogin();
		}

		$filter = $this->input->get('filter', 'posts', 'default');

		$posts = $this->getProfilePosts($filter, $user);
		$pagination = $this->getProfilePagination();

		// Set page title
		ED::setPageTitle($profile->getName());
		$this->setPathway(JText::_($profile->getName()));

		// Add canonical tag for this page
		$this->canonical('index.php?option=com_easydiscuss&view=profile&id=' . $profile->id);

		$params = ED::registry($profile->params);

		// User badges are rendered by default since we know there are not many badges in ED
		$badges = $profile->getBadges();

		// Set meta tags.
		ED::setMeta($profile->id, ED_META_TYPE_PROFILE, $profile->description);

		$this->set('filter', $filter);
		$this->set('params', $params);
		$this->set('pagination', $pagination);
		$this->set('posts', $posts);
		$this->set('badges', $badges);
		$this->set('profile', $profile);

		parent::display('profile/default');
	}

	/**
	 * Displays the user editing form
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function edit($tmpl = null)
	{
		if ($this->my->guest) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_YOU_MUST_LOGIN_FIRST'), 'error');
			ED::redirect(EDR::_('index.php?option=com_easydiscuss&view=index'));
			return $this->app->close();
		}

		// Set page properties
		ED::setPageTitle('COM_EASYDISCUSS_EDIT_PROFILE');
		if (! EDR::isCurrentActiveMenu('profile', 0, 'id', 'edit')) {

			$this->setPathway(JText::_('COM_EASYDISCUSS_PROFILE'), EDR::_('index.php?option=com_easydiscuss&view=profile&id=' . $this->my->id));
			$this->setPathway(JText::_('COM_EASYDISCUSS_EDIT_PROFILE'));
		}

		//load porfile info and auto save into table if user is not already exist in discuss's user table.
		$profile = ED::profile();
		$userparams = new JRegistry($profile->params);

		// Get configured max size
		$configMaxSize = $this->config->get('main_upload_maxsize', 0);

		if ($configMaxSize > 0) {

			// Convert MB size to Bytes
			$configMaxSize = $configMaxSize * 1048576;

			// We convert to bytes because the function is accepting bytes
			$configMaxSize  = ED::string()->bytesToSize($configMaxSize);
		}

		$avatar_config_path = $this->config->get('main_avatarpath');
		$avatar_config_path = rtrim($avatar_config_path, '/');
		$avatar_config_path = EDJString::str_ireplace('/', DIRECTORY_SEPARATOR, $avatar_config_path);

		$croppable = false;
		$allowJFBCAvatarEdit = false;

		if ($this->config->get('layout_avatarIntegration') == 'default') {
			$original 	= JPATH_ROOT . '/' . rtrim($this->config->get( 'main_avatarpath' ) , '/' ) . '/' . 'original_' . $profile->avatar;

			if (JFile::exists($original)) {
				$size = getimagesize( $original );

				$width = $size[0];
				$height = $size[1];

				// image ratio always 1:1
				$configAvatarWidth = $this->config->get('layout_avatarwidth', 160);
				$configAvatarHeight = $configAvatarWidth;

				if ($width >= $configAvatarWidth && $height >= $configAvatarHeight) {
					$croppable = true;
				}
			}
		}

		// Check if user are allowed to change username
		$canChangeUsername = JComponentHelper::getParams('com_users')->get('change_login_name') ? true : false;
		
		if ($this->config->get('layout_avatarIntegration') == 'jfbconnect') {
			$hasAvatar = ED::integrate()->jfbconnect($profile);

			if (!$hasAvatar) {
				$croppable = true;
				$allowJFBCAvatarEdit = true;
			}
		}

		$avatar = $profile->avatar;

		if (!$avatar || $avatar == 'default.png') {
			$avatar = false;
		}

		// Get editor for signature.
		$opt = array('defaults', '');
		$composer = ED::composer($opt);
		$composerSignature = ED::composer($opt);

		// Render additional tabs
		$tabs = $this->getExtraTabs($profile);

		// Check if this user has any download request or not
		$download = ED::table('Download');
		$download->load(array('userid' => $this->my->id));

		// Load language from com_users
		$language = JFactory::getLanguage();
		$language->load('com_users');

		$twoFactorMethods = ED::getTwoFactorMethods();
		$otpConfig = ED::getOtpConfig();
		$twoFactorForms = ED::getTwoFactorForms($otpConfig);

		$this->set('download', $download);
		$this->set('tabs', $tabs);
		$this->set('avatar', $avatar);
		$this->set('croppable', $croppable);
		$this->set('allowJFBCAvatarEdit', $allowJFBCAvatarEdit);
		$this->set('user', $this->my);
		$this->set('profile', $profile);
		$this->set('configMaxSize', $configMaxSize );
		$this->set('userparams', $userparams);
		$this->set('composer', $composer);
		$this->set('composerSignature', $composerSignature);
		$this->set('canChangeUsername', $canChangeUsername);
		$this->set('twoFactorMethods', $twoFactorMethods);
		$this->set('otpConfig', $otpConfig);
		$this->set('twoFactorForms', $twoFactorForms);

		parent::display('user/edit/default');
	}

	/**
	 * Trigger additional tabs to be rendered when editing user
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getExtraTabs($user)
	{
		// Render additional tabs
		JPluginHelper::importPlugin('easydiscuss');
		$result = JFactory::getApplication()->triggerEvent('onEditUser', array(&$user));

		$tabs = new stdClass();
		$tabs->heading = '';
		$tabs->contents = '';

		if (!$result) {
			return $tabs;
		}

		foreach ($result as $row) {
			$tabs->heading .= $row->heading;
			$tabs->contents .= $row->contents;
		}

		return $tabs;
	}
}
