<?php
/**
* @package      Komento
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once( KOMENTO_ADMIN_ROOT . DIRECTORY_SEPARATOR . 'views.php');

class KomentoViewComments extends KomentoAdminView
{
	function display($tpl = null)
	{
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();

		if( Komento::joomlaVersion() >= '1.6' )
		{
			if(!$user->authorise('komento.manage.comments' , 'com_komento') )
			{
				$mainframe->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				$mainframe->close();
			}
		}

		$filter_publish = $mainframe->getUserStateFromRequest( 'com_komento.comments.filter_publish', 	'filter_publish', 	'*', 'string' );
		$filter_component = $mainframe->getUserStateFromRequest( 'com_komento.comments.filter_component', 'filter_component', '*', 'string' );
		$search 		= $mainframe->getUserStateFromRequest( 'com_komento.comments.search', 			'search', 			'', 'string' );
		$search 		= trim( JString::strtolower( $search ) );
		$order			= $mainframe->getUserStateFromRequest( 'com_komento.comments.filter_order', 	'filter_order', 	'created', 'string' );
		$orderDirection	= $mainframe->getUserStateFromRequest( 'com_komento.comments.filter_order_Dir',	'filter_order_Dir',	'DESC', 'word' );

		$nosearch = JRequest::getInt( 'nosearch', 0 );

		$parentId = JRequest::getVar( 'parentid', 0 );

		// Construct options
		$options['no_tree'] = 0;
		$options['no_search'] = $nosearch;
		$options['parent_id'] = $parentId;

		// Get data from the model
		$commentsModel	= Komento::getModel( 'comments' );
		$comments		= $commentsModel->getData($options);
		$pagination		= $commentsModel->getPagination();

		if($search)
		{
			$parentId = 0;
		}

		$parent		= '';
		if($parentId)
		{
			$parent = Komento::getTable('comments');
			$parent->load($parentId);
			$parent = Komento::getHelper( 'comment' )->process( $parent, 1 );
		}

		$this->assignRef( 'comments'	, $comments );
		$this->assignRef( 'pagination'	, $pagination );
		$this->assign( 'parent'			, $parent );
		$this->assign( 'parentid'		, $parentId );
		$this->assign( 'state'			, $this->getPublishState($filter_publish));
		$this->assign( 'search'			, $search );
		$this->assign( 'order'			, $order );
		$this->assign( 'orderDirection'	, $orderDirection );

		$this->assign( 'component', $this->getComponentState($filter_component));
		$this->assign( 'columns', Komento::getConfig( 'com_komento_comments_columns', false ) );
		$this->assign( 'columnCount', 2 );

		parent::display($tpl);
	}

	function registerToolbar()
	{
		$parentId = JRequest::getVar('parentid', 0);

		// JToolBarHelper::title( text, iconfilename )

		if($parentId)
		{
			if($parentId)
			{
				$parent = Komento::getTable('comments');
				$parent->load($parentId);
			}

			JToolBarHelper::title( JText::_( 'COM_KOMENTO_COMMENTS_TITLE_CHILD_OF' ) . $parentId, 'comments' );
			JToolBarHelper::back( JText::_( 'COM_KOMENTO_BACK' ) , 'index.php?option=com_komento&view=comments&parentid=' . $parent->parent_id);
		}
		else
		{
			JToolBarHelper::title( JText::_( 'COM_KOMENTO_COMMENTS_TITLE' ), 'comments' );
			JToolBarHelper::back( JText::_( 'COM_KOMENTO_ADMIN_HOME' ) , 'index.php?option=com_komento');
		}
		JToolBarHelper::divider();

		if( Komento::joomlaVersion() >= '3.0' )
		{
			JToolBarHelper::custom('stick', 'star', '', JText::_( 'COM_KOMENTO_STICK' ));
			JToolBarHelper::custom('unstick', 'star-empty', '', JText::_( 'COM_KOMENTO_UNSTICK' ));
		}
		else
		{
			JToolBarHelper::custom('stick', 'kmt-stick', '', JText::_( 'COM_KOMENTO_STICK' ));
			JToolBarHelper::custom('unstick', 'kmt-unstick', '', JText::_( 'COM_KOMENTO_UNSTICK' ));
		}
		JToolBarHelper::divider();
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::divider();
		JToolBarHelper::deleteList();

	}

	function getPublishState ($filter_publish = '*')
	{
		$publish[] = JHTML::_('select.option', '*', JText::_( 'COM_KOMENTO_ALL_STATUS' ) );
		$publish[] = JHTML::_('select.option', '1', JText::_( 'COM_KOMENTO_PUBLISHED' ) );
		$publish[] = JHTML::_('select.option', '0', JText::_( 'COM_KOMENTO_UNPUBLISHED' ) );
		$publish[] = JHTML::_('select.option', '2', JText::_( 'COM_KOMENTO_MODERATE' ) );

		return JHTML::_('select.genericlist', $publish, 'filter_publish', 'class="inputbox" .fa- onchange="submitform( );"', 'value', 'text', $filter_publish );
	}

	function getComponentState($filter_component = '*')
	{
		$commentsModel		= Komento::getModel( 'comments' );
		$components			= $commentsModel->getUniqueComponents();

		$component_state[]	= JHTML::_('select.option', '*', JText::_( 'COM_KOMENTO_ALL_COMPONENTS' ) );

		foreach($components as $component)
		{
			$component_state[] = JHTML::_('select.option', $component , Komento::loadApplication( $component )->getComponentName());
		}

		return JHTML::_('select.genericlist', $component_state, 'filter_component', 'class="inputbox" .fa- onchange="submitform();"', 'value', 'text', $filter_component);
	}

	function getColumnsState()
	{
		$columns = array(
			'comment',
			'published',
			'sticked',
			'link',
			'edit',
			'component',
			'article',
			'date',
			'author',
			'email',
			'homepage',
			'ip',
			'latitude',
			'longitude',
			'address',
			'id'
		);

		$columnsConfig = Komento::getConfig( 'com_komento_comments_columns', false );

		$html = $this->renderColumnsConfiguration( $columns, $columnsConfig );

		return $html;
	}
}
