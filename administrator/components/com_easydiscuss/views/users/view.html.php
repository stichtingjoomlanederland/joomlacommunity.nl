<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ADMIN_ROOT . '/views/views.php');

class EasyDiscussViewUsers extends EasyDiscussAdminView
{
	/**
	 * Renders the user's listing
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display($tpl = null)
	{
		$this->checkAccess('discuss.manage.users');


		// Set page attributes
		$this->title('COM_EASYDISCUSS_USERS');

		// Register toolbar items
		JToolbarHelper::deleteList();

		// Get the selected filter
		$filter = $this->getUserState('users.filter_state', 'filter_state', '*', 'word');

		// Get the current search query
		$search = $this->getUserState('users.search', 'search', '', 'string');
		$search = trim(strtolower($search));

		// Ordering options
		$order = $this->getUserState('users.filter_order', 'filter_order', 'id', 'cmd');
		$orderDirection = $this->getUserState('users.filter_order_Dir', 'filter_order_Dir', '', 'word');

		$model = ED::model('Users');
		$users = $model->getUsers();

		// Get the pagination
		$pagination = $model->getPagination();

		if ($users) {
			foreach ($users as &$user) {
				$user->usergroups = $this->getGroupTitle($user->id);
				$user->totalTopics = $this->getTotalTopicCreated($user->id);
			}
		}

		$browse = $this->input->get('browse', 0, 'int');
		$browseFunction = $this->input->get('browsefunction', 'selectUser', 'default');
		$prefix = $this->input->get('prefix', '', 'cmd');

		$this->set('filter', $filter);
		$this->set('search', $search);
		$this->set('prefix', $prefix);
		$this->set('users', $users);
		$this->set('pagination', $pagination);
		$this->set('browse', $browse);
		$this->set('browsefunction', $browseFunction);

		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);

		parent::display('users/default');
	}

	public function getGroupTitle($user_id)
	{
		$db = DiscussHelper::getDBO();

		$sql = "SELECT title FROM `#__usergroups` AS ug";
		$sql .= " left join  `#__user_usergroup_map` as map on (ug.id = map.group_id)";
		$sql .= " WHERE map.user_id=". $db->Quote( $user_id );

		$db->setQuery($sql);
		$result = $db->loadResultArray();
		return nl2br( implode("\n", $result) );
	}

	public function getTotalTopicCreated($userId)
	{
		$db = DiscussHelper::getDBO();

		$query  = 'SELECT COUNT(1) AS CNT FROM `#__discuss_posts`';
		$query  .= ' WHERE `user_id` = ' . $db->Quote($userId);
		$query  .= ' AND `parent_id` = 0';

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	public function browse()
	{
		$app			= JFactory::getApplication();

		$filter_state	= $app->getUserStateFromRequest( 'com_easydiscuss.users.filter_state',		'filter_state',		'*',	'word' );
		$search			= $app->getUserStateFromRequest( 'com_easydiscuss.users.search',			'search',			'',		'string' );

		$search			= trim(JString::strtolower( $search ) );
		$order			= $app->getUserStateFromRequest( 'com_easydiscuss.users.filter_order',		'filter_order',		'id',	'cmd' );
		$orderDirection	= $app->getUserStateFromRequest( 'com_easydiscuss.users.filter_order_Dir',	'filter_order_Dir',	'',		'word' );

		$userModel		= ED::model( 'Users' );
		$users			= $userModel->getUsers();

		if (count($users) > 0) {
			for ($i = 0; $i < count($users); $i++) {

				$joomlaUser				= JFactory::getUser($users[$i]->id);
				$userGroupsKeys			= array_keys($joomlaUser->groups);
				$userGroups				= implode(', ', $userGroupsKeys);
				$users[$i]->usergroups	= $userGroups;
			}
		}

		$pagination	= $userModel->getPagination();

		$state	= JHTML::_('grid.state', $filter_state );

		$this->assign( 'users'			, $users );
		$this->assign( 'pagination'		, $pagination );
		$this->assign( 'search'			, $search );
		$this->assign( 'state'			, $state );
		$this->assign( 'orderDirection'	, $orderDirection );
		$this->assign( 'order'			, $order );
		$this->assign( 'pagination'		, $pagination );

		parent::display('users');
	}
}
