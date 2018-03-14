<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once DISCUSS_ADMIN_ROOT . '/views/views.php';

class EasyDiscussViewUser extends EasyDiscussAdminView
{
	public function display($tpl = null)
	{
		$this->checkAccess('discuss.manage.users');

		// Initialise variables
		$config = ED::config();

		$id = $this->input->get('id', 0, 'int');

		$profile = ED::user($id);

		$userparams	= json_decode($profile->get('params'));
		$siteDetails = json_decode($profile->get('site'));

		$userparams = ED::getRegistry($userparams);
		$siteDetails = ED::getRegistry($siteDetails);

		$avatarIntegration = $config->get('layout_avatarIntegration', 'default');

		$user = JFactory::getUser($id);
		$isNew = ($user->id == 0) ? true : false;

		$badges = $profile->getBadges();

		$model = ED::model('Badges');
		$history = $model->getBadgesHistory($profile->id);

		$params = $user->getParameters(true);

		// Badge id's that are assigned to the user.
		$badgeIds = '';

		for ($i = 0; $i < count($badges); $i++) {
			$badgeIds .= $badges[$i]->id;

			if (next($badges) !== false) {
				$badgeIds .= ',';
			}

			$badgeUser = ED::table('BadgesUsers');
			$badgeUser->loadByUser($id, $badges[$i]->id);

			$badges[$i]->reference_id = $badgeUser->id;
			$badges[$i]->custom = $badgeUser->custom;
		}

		// Get active tab
		$active = $this->input->get('active', 'account', 'word');

		if ($this->config->get('layout_avatar')) {
			$maxSizeInMB = (int) $this->config->get( 'main_upload_maxsize', 0 );
		}

		// Get editor for signature.
		$opt = array('defaults', $profile->getSignature(true));
		$composer = ED::composer($opt);

		$this->set('maxSizeInMB', $maxSizeInMB);
		$this->set('active', $active);
		$this->set('badgeIds', $badgeIds);
		$this->set('badges', $badges);
		$this->set('history', $history);
		$this->set('config', $config);
		$this->set('profile', $profile);
		$this->set('user', $user);
		$this->set('isNew', $isNew);
		$this->set('params', $params);
		$this->set('avatarIntegration', $avatarIntegration);
		$this->set('userparams', $userparams);
		$this->set('siteDetails', $siteDetails);
		$this->set('composer', $composer);

		parent::display('user/default');
	}

	public function registerToolbar()
	{
		$id	= JRequest::getInt('id');
		$user = JTable::getInstance('User', 'JTable');
		$user->load($id);

		$title = ($user->id == 0) ? JText::_('COM_EASYDISCUSS_NEW_USER') : JText::sprintf('COM_EASYDISCUSS_EDITING_USER', $user->name);

		JToolBarHelper::title($title, 'users');

		JToolBarHelper::back(JText::_('COM_EASYDISCUSS_BACK'), 'index.php?option=com_easydiscuss&view=users');
		JToolBarHelper::divider();

		JToolBarHelper::apply();
		JToolBarHelper::save();

		JToolBarHelper::divider();
		JToolBarHelper::cancel();
	}
}
