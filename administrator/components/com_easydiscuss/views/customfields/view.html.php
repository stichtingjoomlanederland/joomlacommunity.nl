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

class EasyDiscussViewCustomFields extends EasyDiscussAdminView
{
	public function display($tpl = null)
	{
		// @rule: Test for user access if on 1.6 and above
		if( DiscussHelper::getJoomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('discuss.manage.customfields' , 'com_easydiscuss') )
			{
				JFactory::getApplication()->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				JFactory::getApplication()->close();
			}
		}
		$this->addPathway( 'Home' , 'index.php?option=com_easydiscuss' );
		$this->addPathway( 'Custom Fields' , '' );

		// Initialise variables
		$mainframe		= JFactory::getApplication();

		$filter_state	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.customs.filter_state', 	'filter_state', 	'*', 'word' );
		$search			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.customs.search', 		'search', 			'', 'string' );

		$search			= trim(JString::strtolower( $search ) );
		$order			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.customs.filter_order', 		'filter_order', 	'a.ordering', 'cmd' );
		$orderDirection	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.customs.filter_order_Dir',	'filter_order_Dir',	'asc', 'word' );


		$ordering		= array();
		// Get data from the model
		$customs		= $this->get( 'Data' );

		if( count( $customs )> 0 )
		{
			foreach( $customs as $item )
			{
				$ordering[0][] = $item->id;
			}
		}



		$pagination		= $this->get( 'Pagination' );
		$this->state	= $this->get('State');

		$joomlaGroups	= DiscussHelper::getJoomlaUserGroups();

		$this->assignRef( 'customs' 	, $customs );
		$this->assignRef( 'pagination'	, $pagination );
		$this->assignRef( 'ordering'	, $ordering );

		$this->assign( 'state'			, JHTML::_('grid.state', $filter_state ) );
		$this->assign( 'search'			, $search );
		$this->assign( 'order'			, $order );
		$this->assign( 'orderDirection'	, $orderDirection );
		$this->assign( 'joomlaGroups'	, $joomlaGroups );

		parent::display($tpl);
	}

	public function form()
	{
		$app 		= JFactory::getApplication();
		$id			= JRequest::getInt( 'id' , '' );

		$field 		= DiscussHelper::getTable( 'CustomFields' );
		$field->load( $id );

		$this->addPathway( 'Home' , 'index.php?option=com_easydiscuss' );
		$this->addPathway( 'Custom Fields' , 'index.php?option=com_easydiscuss&view=customfields' );

		$field->title	= JString::trim($field->title);

		$this->id	= $field;
		if( !$field )
		{
			JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_ADD_NEW_CUSTOMFIELDS' ), 'customs' );
			$this->addPathway( 'New Field' , '' );
		}
		else
		{
			JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_EDITING_CUSTOMFIELDS' ), 'customs' );
			$this->addPathway( 'Editing Field' , '' );
		}

		JHTML::_( 'behavior.modal' );

		// Set default values for new entries.
		if( empty( $field->created_time ) )
		{
			$date			= DiscussHelper::getHelper( 'Date' )->dateWithOffSet();

			$field->created_time	= $date->toFormat();
			$field->published	= true;
		}

		$customAclItems		= JTable::getInstance( 'CustomFieldsACL' , 'Discuss' );
		$customFieldsAcl	= $customAclItems->getCustomFieldsACL();
		$assignedGroupACL	= $field->getAssignedACL( 'group' );
		$assignedUserACL	= $field->getAssignedACL( 'user' );

		$joomlaGroups	= DiscussHelper::getJoomlaUserGroups();

		$this->assignRef( 'field'				, $field );
		$this->assignRef( 'assignedGroupACL'	, $assignedGroupACL );
		$this->assignRef( 'assignedUserACL'		, $assignedUserACL );
		$this->assignRef( 'customFieldsAcl'		, $customFieldsAcl );
		$this->assignRef( 'joomlaGroups'		, $joomlaGroups );

		parent::display();
	}

	public function registerToolbar()
	{
		$layout 	= $this->getLayout();

		if( $layout == 'form' )
		{
			JToolBarHelper::back( JText::_( 'COM_EASYDISCUSS_BACK' ) , 'index.php?option=com_easydiscuss&view=customfields' );
			JToolBarHelper::divider();

			JToolbarHelper::apply();
			JToolbarHelper::save();

			if( DiscussHelper::getJoomlaVersion() > '1.6' )
			{
				JToolBarHelper::save2new( 'savePublishNew' );
			}
			else
			{
				JToolBarHelper::save( 'savePublishNew' , JText::_( 'COM_EASYDISCUSS_SAVE_AND_NEW' ) );
			}

			JToolBarHelper::divider();
			JToolBarHelper::cancel();

		}
		else
		{
			JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_CUSTOMFIELDS_MAIN_TITLE' ), 'customs' );
			JToolBarHelper::custom( 'home', 'arrow-left', '', JText::_( 'COM_EASYDISCUSS_TOOLBAR_HOME' ), false);
			JToolBarHelper::divider();
			JToolbarHelper::publishList();
			JToolbarHelper::unpublishList();
			JToolBarHelper::divider();
			JToolbarHelper::addNew('customfields.edit');
			JToolbarHelper::deleteList();
		}
	}
}
