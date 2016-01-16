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

require_once DISCUSS_ADMIN_ROOT . '/views.php';

class EasyDiscussViewUsers extends EasyDiscussAdminView
{
	public function display($tpl = null)
	{
		// @rule: Test for user access if on 1.6 and above
		if( DiscussHelper::getJoomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('discuss.manage.users' , 'com_easydiscuss') )
			{
				JFactory::getApplication()->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				JFactory::getApplication()->close();
			}
		}
		// Initialise variables
		$user			= JFactory::getUser();
		$mainframe		= JFactory::getApplication();

		$filter_state	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.users.filter_state', 	'filter_state', 	'*', 'word' );
		$search			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.users.search', 			'search', 			'', 'string' );

		$search			= trim(JString::strtolower( $search ) );
		$order			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.users.filter_order', 	'filter_order', 	'id', 'cmd' );
		$orderDirection	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.users.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		// Get data from the model
		//$users			= $this->get( 'Users', true );
		$users = DiscussHelper::getModel( 'Users' , true );
		$users = $users->getUsers();

		$pagination		= $this->get( 'Pagination' );

		if(DiscussHelper::getJoomlaVersion() >= '1.6')
		{
			if(count($users) > 0)
			{
				for($i = 0; $i < count($users); $i++)
				{
					$row = $users[$i];
					$row->usergroups 	= $this->getGroupTitle( $row->id );
				}
			}
		}


		$this->assignRef( 'users' 		, $users );
		$this->assignRef( 'pagination'	, $pagination );

		$browse			= JRequest::getInt( 'browse' , 0 );
		$browsefunction = JRequest::getVar('browsefunction', 'selectUser');
		$this->assign( 'browse' , $browse );
		$this->assign( 'browsefunction' , $browsefunction );

		$this->assign( 'state'			, JHTML::_('grid.state', $filter_state ) );
		$this->assign( 'search'			, $search );
		$this->assign( 'order'			, $order );
		$this->assign( 'orderDirection'	, $orderDirection );

		parent::display($tpl);
	}

	function getGroupTitle( $user_id )
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

	public function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_USERS' ), 'users' );

		JToolBarHelper::custom( 'home', 'arrow-left', '', JText::_( 'COM_EASYDISCUSS_TOOLBAR_HOME' ), false);
		JToolBarHelper::divider();
		JToolbarHelper::deleteList();
	}

	public function browse()
	{
		$app			= JFactory::getApplication();

		$filter_state	= $app->getUserStateFromRequest( 'com_easydiscuss.users.filter_state',		'filter_state',		'*',	'word' );
		$search			= $app->getUserStateFromRequest( 'com_easydiscuss.users.search',			'search',			'',		'string' );

		$search			= trim(JString::strtolower( $search ) );
		$order			= $app->getUserStateFromRequest( 'com_easydiscuss.users.filter_order',		'filter_order',		'id',	'cmd' );
		$orderDirection	= $app->getUserStateFromRequest( 'com_easydiscuss.users.filter_order_Dir',	'filter_order_Dir',	'',		'word' );

		$userModel		= DiscussHelper::getModel( 'Users' );
		$users			= $userModel->getUsers();

		if(DiscussHelper::getJoomlaVersion() >= '1.6' && count($users) > 0)
		{
			for($i = 0; $i < count($users); $i++)
			{
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
