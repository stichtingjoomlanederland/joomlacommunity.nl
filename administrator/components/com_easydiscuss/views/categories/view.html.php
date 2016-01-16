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

require_once DISCUSS_ADMIN_ROOT . '/views.php';

class EasyDiscussViewCategories extends EasyDiscussAdminView
{
	public function display($tpl = null)
	{
		// @rule: Test for user access if on 1.6 and above
		if( DiscussHelper::getJoomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('discuss.manage.categories' , 'com_easydiscuss') )
			{
				JFactory::getApplication()->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				JFactory::getApplication()->close();
			}
		}
		// Initialise variables
		$document		= JFactory::getDocument();
		$user			= JFactory::getUser();
		$mainframe		= JFactory::getApplication();

		$filter_state	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.categories.filter_state', 		'filter_state', 	'*', 'word' );
		$search			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.categories.search', 			'search', 			'', 'string' );

		$search			= trim(JString::strtolower( $search ) );
		$order			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.categories.filter_order', 		'filter_order', 	'lft', 'cmd' );
		$orderDirection	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.categories.filter_order_Dir',	'filter_order_Dir',	'asc', 'word' );

		// Get data from the model
		$model			= $this->getModel( 'Categories' );
		$categories		= $model->getData();
		$ordering		= array();

		JTable::addIncludePath( DISCUSS_TABLES );
		$category		= JTable::getInstance( 'Category' , 'Discuss' );

		for( $i = 0 ; $i < count( $categories ); $i++ )
		{
			$category	= $categories[ $i ];

			$category->count	= $model->getUsedCount( $category->id, false, true );
			$category->child_count	= $model->getChildCount( $category->id );

			// Preprocess the list of items to find ordering divisions.
			$ordering[$category->parent_id][] = $category->id;
		}
		$pagination 	= $this->get( 'Pagination' );

		$this->addPathway( 'Home' , 'index.php?option=com_easydiscuss' );
		$this->addPathway( JText::_( 'COM_EASYDISCUSS_BREADCRUMBS_CATEGORIES' ) );

		$this->assignRef( 'categories' 	, $categories );
		$this->assignRef( 'pagination'	, $pagination );
		$this->assignRef( 'ordering'	, $ordering );

		$this->assign( 'state'			, JHTML::_('grid.state', $filter_state ) );
		$this->assign( 'search'			, $search );
		$this->assign( 'order'			, $order );
		$this->assign( 'orderDirection'	, $orderDirection );

		parent::display($tpl);
	}

	public function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_CATEGORIES_TITLE' ), 'category' );

		JToolBarHelper::custom( 'home', 'arrow-left', '', JText::_( 'COM_EASYDISCUSS_TOOLBAR_HOME' ), false);
		JToolBarHelper::divider();
		JToolBarHelper::makeDefault( 'makeDefault' );
		JToolBarHelper::divider();
		JToolbarHelper::publishList();
		JToolbarHelper::unpublishList();
		JToolBarHelper::divider();
		JToolbarHelper::addNew();
		JToolbarHelper::deleteList();
	}
}
