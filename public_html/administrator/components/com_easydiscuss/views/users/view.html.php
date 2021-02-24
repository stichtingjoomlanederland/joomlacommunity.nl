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

class EasyDiscussViewUsers extends EasyDiscussAdminView
{
	/**
	 * Renders the user's listing
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		$this->checkAccess('discuss.manage.users');

		$this->title('COM_EASYDISCUSS_USERS');

		// Register toolbar items
		JToolBarHelper::custom('assignBadge', 'badge', '', JText::_('COM_ED_ASSIGN_BADGE'));
		JToolbarHelper::deleteList(JText::_('COM_ED_DELETE_USER_CONFIRMATION'));

		$categoryId = $this->input->get('category_id', 0, 'int');
		$moderator = $this->input->get('moderator', 0, 'int');

		// Get the selected filter
		$filter = $this->getUserState('users.filter_state', 'filter_state', '*', 'word');

		// Get the current search query
		$search = $this->getUserState('users.search', 'search', '', 'string');
		$search = trim(strtolower($search));

		// Ordering options
		$order = $this->getUserState('users.filter_order', 'filter_order', 'id', 'cmd');
		$orderDirection = $this->getUserState('users.filter_order_Dir', 'filter_order_Dir', '', 'word');

		$browse = $this->input->get('browse', 0, 'int');
		$browseFunction = $this->input->get('browsefunction', 'selectUser', 'default');

		$model = ED::model('Users');
		$users = array();

		if ($categoryId && $moderator) {
			$users = $model->getModerators($categoryId);
		} else {
			$users = $model->getUsers();
		}

		// Get the pagination
		$pagination = $model->getPagination();

		if ($users) {
			foreach ($users as &$user) {
				$user->usergroups = $this->getGroupTitle($user->id);
				$user->totalTopics = ($browse) ? 0 : $this->getTotalTopicCreated($user->id);
			}
		}

		$this->set('filter', $filter);
		$this->set('search', $search);
		$this->set('users', $users);
		$this->set('pagination', $pagination);
		$this->set('browse', $browse);
		$this->set('browsefunction', $browseFunction);

		$this->set('categoryId', $categoryId);
		$this->set('moderator', $moderator);
		
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);

		parent::display('users/default');
	}

	/**
	 * Renders the form to edit or create user
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function form($tpl = null)
	{
		$this->checkAccess('discuss.manage.users');

		$id = $this->input->get('id', 0, 'int');

		$profile = ED::user($id);

		$this->setHeading('COM_ED_EDITING_USER');

		JToolBarHelper::apply();
		JToolBarHelper::save();
		JToolBarHelper::cancel();
		JToolbarHelper::custom('resetRank', '', '', JText::_('COM_EASYDISCUSS_RESET_RANK'), false);

		$userparams	= json_decode($profile->get('params'));
		$userparams = ED::getRegistry($userparams);

		$avatarIntegration = $this->config->get('layout_avatarIntegration', 'default');

		$user = JFactory::getUser($id);
		$isNew = ($user->id == 0) ? true : false;

		$badges = $profile->getBadges();

		$model = ED::model('Badges');
		$history = $model->getBadgesHistory($profile->id);

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

		if (!$this->config->get('layout_text_avatar')) {
			$maxSizeInMB = (int) $this->config->get('main_upload_maxsize', 0);
			$this->set('maxSizeInMB', $maxSizeInMB);
		}

		// Get editor for signature.
		$opt = array('defaults', $profile->getSignature(true));
		$composer = ED::composer($opt);

		$this->set('active', $active);
		$this->set('badgeIds', $badgeIds);
		$this->set('badges', $badges);
		$this->set('history', $history);
		$this->set('profile', $profile);
		$this->set('user', $user);
		$this->set('isNew', $isNew);
		$this->set('avatarIntegration', $avatarIntegration);
		$this->set('userparams', $userparams);
		$this->set('composer', $composer);

		parent::display('user/default');
	}

	/**
	 * Renders a list of pending users
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function downloads()
	{
		$this->checkAccess('discuss.manage.users');

		$this->setHeading('COM_ED_DOWNLOAD_REQUESTS');
		$this->addHelpButton('/docs/easydiscuss/administrators/configuration/general-data-protection-regulation');

		JToolbarHelper::deleteList('', 'removeRequest');
		JToolBarHelper::custom('purgeAll','purgeAll','icon-32-unpublish.png', 'COM_EASYDISCUSS_SPOOLS_PURGE_ALL_BUTTON', false);

		// Get the user's model.
		$model = ED::model('Download');
		$requests = $model->getRequests();
		$pagination = $model->getPagination();

		$this->set('requests', $requests);
		$this->set('pagination', $pagination);

		parent::display('users/downloads/default');
	}

	/**
	 * Allows viewer to download data from backend
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function downloadData()
	{
		$id = $this->input->get('id', 0, 'int');

		$table = ED::table('Download');
		$table->load($id);

		return $table->showArchiveDownload();
	}

	public function getGroupTitle($user_id)
	{
		$db = ED::db();

		$sql = "SELECT title FROM `#__usergroups` AS ug";
		$sql .= " left join  `#__user_usergroup_map` as map on (ug.id = map.group_id)";
		$sql .= " WHERE map.user_id=". $db->Quote($user_id);

		$db->setQuery($sql);
		$result = $db->loadResultArray();

		if (count($result) > 1) {
			return JText::_('Multiple Groups');
		}

		return nl2br(implode("\n", $result));
	}

	public function getTotalTopicCreated($userId)
	{
		$db = ED::db();

		$query  = 'SELECT COUNT(1) AS CNT FROM `#__discuss_posts`';
		$query  .= ' WHERE `user_id` = ' . $db->Quote($userId);
		$query  .= ' AND `parent_id` = 0';

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	public function browse()
	{
		$app = JFactory::getApplication();

		$filter_state = $app->getUserStateFromRequest('com_easydiscuss.users.filter_state', 'filter_state', '*', 'word');
		$search = $app->getUserStateFromRequest('com_easydiscuss.users.search', 'search', '', 'string');

		$search			= trim(EDJString::strtolower( $search ) );
		$order			= $app->getUserStateFromRequest( 'com_easydiscuss.users.filter_order',		'filter_order',		'id',	'cmd' );
		$orderDirection	= $app->getUserStateFromRequest( 'com_easydiscuss.users.filter_order_Dir',	'filter_order_Dir',	'',		'word' );

		$userModel = ED::model('Users');
		$users = $userModel->getUsers();

		if (count($users) > 0) {
			for ($i = 0; $i < count($users); $i++) {

				$joomlaUser = JFactory::getUser($users[$i]->id);
				$userGroupsKeys = array_keys($joomlaUser->groups);
				$userGroups = implode(', ', $userGroupsKeys);
				$users[$i]->usergroups = $userGroups;
			}
		}

		$pagination = $userModel->getPagination();

		$state = JHTML::_('grid.state', $filter_state);

		$this->assign('users' , $users);
		$this->assign('pagination' , $pagination);
		$this->assign('search' , $search);
		$this->assign('state' , $state);
		$this->assign('orderDirection' , $orderDirection);
		$this->assign('order' , $order);
		$this->assign('pagination' , $pagination);

		parent::display('users');
	}
}
