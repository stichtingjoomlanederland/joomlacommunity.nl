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

class EasyDiscussViewPoints extends EasyDiscussAdminView
{
	function display($tpl = null)
	{
		// @rule: Test for user access if on 1.6 and above
		if( DiscussHelper::getJoomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('discuss.manage.points' , 'com_easydiscuss') )
			{
				JFactory::getApplication()->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				JFactory::getApplication()->close();
			}
		}
		//initialise variables
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();

		$this->addPathway( 'Home' , 'index.php?option=com_easydiscuss' );
		$this->addPathway( 'Points' , '' );

		if( $this->getLayout() == 'install' )
		{
			return $this->installLayout();
		}

		if( $this->getLayout() == 'managerules' )
		{
			return $this->manageRules();
		}

		$filter_state 		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.points.filter_state', 	'filter_state', 	'*', 'word' );
		$search 			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.points.search', 			'search', 			'', 'string' );

		$search 		= trim(JString::strtolower( $search ) );
		$order			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.points.filter_order', 		'filter_order', 	'a.id', 'cmd' );
		$orderDirection	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.points.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		$points			= $this->get( 'Points' );
		$pagination		= $this->get( 'Pagination' );

		$this->assign( 'points'		, $points );
		$this->assign( 'pagination'	, $pagination );
		$this->assign( 'state'			, $this->getFilterState($filter_state));
		$this->assign( 'search'			, $search );
		$this->assign( 'order'			, $order );
		$this->assign( 'orderDirection'	, $orderDirection );

		parent::display($tpl);
	}

	public function form()
	{
		$id		= JRequest::getInt( 'id' , 0 );

		$this->addPathway( 'Home' , 'index.php?option=com_easydiscuss' );
		$this->addPathway( 'Points' , 'index.php?option=com_easydiscuss&view=points' );

		$point	= DiscussHelper::getTable( 'Points' );
		$point->load( $id );

		if( $point->id )
		{
			$this->addPathway( JText::_( 'COM_EASYDISCUSS_PATHWAY_EDIT_POINT' ) );
		}
		else
		{
			$this->addPathway( JText::_( 'COM_EASYDISCUSS_PATHWAY_NEW_POINT' ) );
		}

		if( !$point->created )
		{
			$date			= DiscussHelper::getHelper( 'Date' )->dateWithOffset( DiscussHelper::getDate()->toMySQL() );
			$point->created	= $date->toMySQL();
		}

		$model	= $this->getModel( 'Points' );
		$rules	= $model->getRules();

		$this->assign( 'rules'	, $rules );
		$this->assign( 'point'	, $point );

		parent::display();
	}

	function getFilterState ($filter_state='*')
	{
		$state[] = JHTML::_('select.option',  '', '- '. JText::_( 'Select State' ) .' -' );
		$state[] = JHTML::_('select.option',  'P', JText::_( 'Published' ) );
		$state[] = JHTML::_('select.option',  'U', JText::_( 'Unpublished' ) );
		$state[] = JHTML::_('select.option',  'A', JText::_( 'Pending' ) );

		return JHTML::_('select.genericlist',   $state, 'filter_state', ' size="1" onchange="submitform( );"', 'value', 'text', $filter_state );
	}

	function registerToolbar()
	{
		$layout 	= $this->getLayout();

		if( $layout == 'form' )
		{
			JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_POINTS' ), 'points' );

			JToolBarHelper::back( JText::_( 'COM_EASYDISCUSS_BACK' ) , 'index.php?option=com_easydiscuss&view=points' );
			JToolBarHelper::divider();
			JToolBarHelper::custom( 'save','save.png','save_f2.png', JText::_( 'COM_EASYDISCUSS_SAVE_BUTTON' ) , false);
			JToolBarHelper::custom( 'saveNew','save.png','save_f2.png', JText::_( 'COM_EASYDISCUSS_SAVE_NEW_BUTTON' ) , false);
			JToolBarHelper::cancel();
		}
		else
		{
			JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_POINTS' ), 'points' );

			JToolBarHelper::custom( 'home', 'arrow-left', '', JText::_( 'COM_EASYDISCUSS_TOOLBAR_HOME' ), false);
			JToolBarHelper::divider();
			JToolBarHelper::custom( 'rules' , 'rules' , '' , JText::_( 'COM_EASYDISCUSS_MANAGE_RULES_BUTTON' ) , false );
			JToolBarHelper::divider();
			JToolbarHelper::publishList();
			JToolbarHelper::unpublishList();
			JToolBarHelper::divider();
			JToolbarHelper::addNew('add');
			JToolbarHelper::deleteList();
		}
	}
}
