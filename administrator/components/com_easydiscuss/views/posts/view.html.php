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

class EasyDiscussViewPosts extends EasyDiscussAdminView
{
	public function display($tpl = null)
	{
		// @rule: Test for user access if on 1.6 and above
		if( DiscussHelper::getJoomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('discuss.manage.posts' , 'com_easydiscuss') )
			{
				JFactory::getApplication()->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				JFactory::getApplication()->close();
			}
		}
		// Initialise variables
		$mainframe	= JFactory::getApplication();

		$filter_state	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.posts.filter_state', 	'filter_state', 	'*', 'word' );
		$search			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.posts.search', 			'search', 			'', 'string' );

		$search			= trim(JString::strtolower( $search ) );
		$order			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.posts.filter_order', 		'filter_order', 	'a.id', 'cmd' );
		$orderDirection	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.posts.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		$parentId		= JRequest::getString('pid', '');
		$parentTitle	= '';

		$this->addPathway( JText::_( 'COM_EASYDISCUSS_BREADCRUMB_HOME' ) , 'index.php?option=com_easydiscuss' );


		if(! empty($parentId))
		{
			$post		= JTable::getInstance( 'Posts' , 'Discuss' );
			$post->load($parentId);
			$parentTitle = $post->title;

			$this->addPathway( JText::_( 'COM_EASYDISCUSS_BREADCRUMB_DISCUSSIONS' ) , 'index.php?option=com_easydiscuss&view=posts' );
			$this->addPathway( JText::sprintf( 'COM_EASYDISCUSS_BREADCRUMB_VIEWING_REPLIES' , $parentTitle ) , '' );
		}
		else
		{
			$this->addPathway( JText::_( 'COM_EASYDISCUSS_BREADCRUMB_DISCUSSIONS' ) , '' );
		}


		$postModel	= $this->getModel('Threaded');

		$filterCategory	= JRequest::getInt( 'category_id' );
		$categoryFilter = DiscussHelper::populateCategories('', '', 'select', 'category_id', $filterCategory , true, false , true , true , 'inputbox' );

		$posts			= $postModel->getPosts();
		$pagination		= $postModel->getPagination();


		$this->assignRef( 'posts' 		, $posts );
		$this->assignRef( 'pagination'	, $pagination );
		$this->assign( 'categoryFilter'	, $categoryFilter );
		$this->assign( 'state'			, $this->getFilterState($filter_state));
		$this->assign( 'search'			, $search );
		$this->assign( 'order'			, $order );
		$this->assign( 'orderDirection'	, $orderDirection );
		$this->assign( 'parentId'		, $parentId );
		$this->assign( 'parentTitle'	, $parentTitle );

		parent::display($tpl);
	}

	public function registerToolbar()
	{
		$parentId		= JRequest::getString('pid', '');

		JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_DISCUSSIONS' ), 'discussions' );

		JToolBarHelper::custom( 'home', 'arrow-left', '', JText::_( 'COM_EASYDISCUSS_TOOLBAR_HOME' ), false);
		JToolBarHelper::divider();

		if( empty( $parentId ) )
		{
			JToolBarHelper::custom( 'showMove' , 'move' , '' , JText::_( 'COM_EASYDISCUSS_MOVE_TOOLBAR' ) );
			JToolBarHelper::custom( 'feature' , 'featured ' , '' , JText::_( 'COM_EASYDISCUSS_FEATURE_TOOLBAR' ) );
			JToolBarHelper::custom( 'unfeature' , 'star-empty' , '' , JText::_( 'COM_EASYDISCUSS_UNFEATURE_TOOLBAR' ) );
			JToolBarHelper::divider();
		}

		JToolbarHelper::publishList();
		JToolbarHelper::unpublishList();
		JToolBarHelper::divider();
		JToolbarHelper::unpublishList( 'resetVotes' , JText::_( 'COM_EASYDISCUSS_RESET_VOTES' ) );
		JToolBarHelper::divider();
		JToolbarHelper::deleteList();
	}
}
