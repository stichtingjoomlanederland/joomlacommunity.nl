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

class EasyDiscussViewRoles extends EasyDiscussAdminView
{

	public function display($tpl = null)
	{
		// @rule: Test for user access if on 1.6 and above
		if( DiscussHelper::getJoomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('discuss.manage.roles' , 'com_easydiscuss') )
			{
				JFactory::getApplication()->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				JFactory::getApplication()->close();
			}
		}
		$task	= JRequest::getCmd('task');
		if( $task == 'roles.edit')
		{
			$this->diplayRole();
		}
		else
		{
			// Initialise variables
			$mainframe		= JFactory::getApplication();

			$filter_state	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.roles.filter_state', 	'filter_state', 	'*', 'word' );
			$search			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.roles.search', 		'search', 			'', 'string' );

			$search			= trim(JString::strtolower( $search ) );
			$order			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.roles.filter_order', 		'filter_order', 	'a.ordering', 'cmd' );
			$orderDirection	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.roles.filter_order_Dir',	'filter_order_Dir',	'asc', 'word' );

			// Get data from the model
			$roles			= $this->get( 'Data' );
			$pagination		= $this->get( 'Pagination' );
			$this->state		= $this->get('State');

			$this->assignRef( 'roles' 		, $roles );
			$this->assignRef( 'pagination'	, $pagination );

			$this->assign( 'state'			, JHTML::_('grid.state', $filter_state ) );
			$this->assign( 'search'			, $search );
			$this->assign( 'order'			, $order );
			$this->assign( 'orderDirection'	, $orderDirection );

			parent::display($tpl);
		}
	}

	public function diplayRole()
	{
		// Initialise variables
		$mainframe	= JFactory::getApplication();

		$roleId		= JRequest::getVar( 'role_id' , '' );

		$role		= DiscussHelper::getTable( 'Role' );

		$role->load( $roleId );

		$role->title	= JString::trim($role->title);

		$this->role	= $role;

		// Set default values for new entries.
		if( empty( $role->created_time ) )
		{
			$date			= DiscussHelper::getHelper( 'Date' )->dateWithOffSet();

			$role->created_time	= $date->toFormat();
			$role->published	= true;
		}

		$groups = DiscussHelper::getJoomlaUserGroups();

		// Remove the selected usergroups
		$db	= DiscussHelper::getDbo();
		$query = 'SELECT `usergroup_id` FROM `#__discuss_roles`';
		$db->setQuery($query);

		$result = $db->loadResultArray();

		if( !empty($result) ) {
			foreach ($groups as $key => $group) {
				if( in_array($group->id, $result) && ($group->id != $role->usergroup_id) ) {
					unset($groups[$key]);
				}
			}
		}

		$usergroupList = JHTML::_('select.genericlist', $groups, 'usergroup_id', 'class="full-width"', 'id', 'name', $role->usergroup_id);

		$this->assignRef( 'role'			, $role );
		$this->assignRef( 'usergroupList'	, $usergroupList );

		$colors		= array();
		$colors[]	= JHTML::_('select.option',  'success',   JText::_( 'COM_EASYDISCUSS_LABEL_COLORCODE_SUCCESS' ) );
		$colors[]	= JHTML::_('select.option',  'warning',   JText::_( 'COM_EASYDISCUSS_LABEL_COLORCODE_WARNING' ) );
		$colors[]	= JHTML::_('select.option',  'important', JText::_( 'COM_EASYDISCUSS_LABEL_COLORCODE_IMPORTANT' ) );
		$colors[]	= JHTML::_('select.option',  'info',      JText::_( 'COM_EASYDISCUSS_LABEL_COLORCODE_INFO' ) );
		$colors[]	= JHTML::_('select.option',  'inverse',   JText::_( 'COM_EASYDISCUSS_LABEL_COLORCODE_INVERSE' ) );

		$colorList = JHTML::_('select.genericlist', $colors, 'colorcode', 'class="full-width"', 'value', 'text', $role->colorcode);

		$this->assignRef( 'colorList'	, $colorList );

		parent::display('edit');
	}

	public function registerToolbar()
	{
		$task	= JRequest::getCmd('task');
		if( $task == 'roles.edit')
		{
			if( $this->role->id != 0 )
			{
				JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_EDITING_ROLE' ), 'roles' );
			}
			else
			{
				JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_ADD_NEW_ROLE' ), 'roles' );
			}

			JToolBarHelper::back( JText::_( 'COM_EASYDISCUSS_BACK' ) , 'index.php?option=com_easydiscuss&view=roles' );
			JToolBarHelper::divider();
			JToolBarHelper::custom( 'save','save.png','save_f2.png', JText::_( 'COM_EASYDISCUSS_SAVE_BUTTON' ) , false);
			JToolBarHelper::custom( 'savePublishNew','save.png','save_f2.png', JText::_( 'COM_EASYDISCUSS_SAVE_AND_NEW' ) , false);
			JToolBarHelper::divider();
			JToolBarHelper::cancel();
		}
		else
		{
			JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_ROLES' ), 'roles' );

			JToolBarHelper::custom( 'home', 'arrow-left', '', JText::_( 'COM_EASYDISCUSS_TOOLBAR_HOME' ), false);
			JToolBarHelper::divider();
			JToolbarHelper::publishList();
			JToolbarHelper::unpublishList();
			JToolBarHelper::divider();
			JToolbarHelper::addNew('roles.edit');
			JToolbarHelper::deleteList();
		}
	}
}
