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

class EasyDiscussViewPost_Types extends EasyDiscussAdminView
{
	public function display($tpl = null)
	{
		// @rule: Test for user access if on 1.6 and above
		if( DiscussHelper::getJoomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('discuss.manage.post_types' , 'com_easydiscuss') )
			{
				JFactory::getApplication()->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				JFactory::getApplication()->close();
			}
		}
		// Initialise variables
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();

		// REMOVE THIS COMMENT LATER: Don't ever use $this->getModel because it will conflict with K2
		$model 			= DiscussHelper::getModel( 'Post_Types' , true );
		$postTypes 		= $model->getTypes();

		$pagination		= $this->get( 'Pagination' );

		$filter_state	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.post_types.filter_state', 	'filter_state', 	'*', 'word' );

		$order			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.post_types.filter_order', 	'filter_order', 	'id', 'cmd' );
		$orderDirection	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.post_types.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		$browse 		= JRequest::getInt( 'browse' , 0 );
		$browseFunction	= JRequest::getVar( 'browseFunction' , '' );
		$search			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.post_types.search', 			'search', 			'', 'string' );
		$search			= trim(JString::strtolower( $search ) );

		$this->assign( 'browseFunction'		, $browseFunction );
		$this->assign( 'browse'				, $browse );
		$this->assign( 'search'				, $search );
		$this->assign( 'postTypes'			, $postTypes );
		$this->assign( 'state'				, $this->getFilterState($filter_state) );

		$this->assign( 'order', $order );
		$this->assign( 'orderDirection'	, $orderDirection );

		$this->assign( 'pagination'			, $pagination );


		parent::display($tpl);
	}

	public function form()
	{
		$id = JRequest::getInt( 'id' );
		$postTypes = DiscussHelper::getTable( 'Post_types' );

		if( !empty($id) )
		{
			$postTypes->load( $id );
		}

		$this->assign( 'postTypes'	, $postTypes );

		// This will go to form.php
		parent::display();
	}

	public function getFilterState( $filter_state='*' )
	{
		$state[] = JHTML::_('select.option',  '', '- '. JText::_( 'COM_EASYDISCUSS_SELECT_STATE' ) .' -' );
		$state[] = JHTML::_('select.option',  'P', JText::_( 'COM_EASYDISCUSS_PUBLISHED' ) );
		$state[] = JHTML::_('select.option',  'U', JText::_( 'COM_EASYDISCUSS_UNPUBLISHED' ) );

		return JHTML::_('select.genericlist',   $state, 'filter_state', ' size="1" onchange="submitform( );"', 'value', 'text', $filter_state );
	}

	public function registerToolbar()
	{
		$layout = JRequest::getVar('layout');
		JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_POST_TYPES_TITLE' ), 'post_types' );


		if( $layout == 'form' )
		{

			JToolbarHelper::addNew('new');
			JToolBarHelper::apply();
			JToolBarHelper::save();
			JToolBarHelper::divider();
			JToolBarHelper::cancel('cancel');

		}
		else
		{
			JToolBarHelper::custom( 'home', 'arrow-left', '', JText::_( 'COM_EASYDISCUSS_TOOLBAR_HOME' ), false);
			JToolBarHelper::divider();
			JToolbarHelper::publishList();
			JToolbarHelper::unpublishList();
			JToolBarHelper::divider();
			JToolbarHelper::addNew('new');
			JToolbarHelper::deleteList();
		}
	}
}
