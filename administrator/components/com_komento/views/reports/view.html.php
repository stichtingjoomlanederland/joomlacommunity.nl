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

class KomentoViewReports extends KomentoAdminView
{
	function display($tpl = null)
	{
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();

		if( Komento::joomlaVersion() >= '1.6' )
		{
			if(!$user->authorise('komento.manage.reports' , 'com_komento') )
			{
				$mainframe->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				$mainframe->close();
			}
		}

		$filter_publish = $mainframe->getUserStateFromRequest( 'com_komento.reports.filter_publish', 	'filter_publish', 	'*', 'string' );
		$filter_component = $mainframe->getUserStateFromRequest( 'com_komento.reports.filter_component', 'filter_component', '*', 'string' );
		$search 		= $mainframe->getUserStateFromRequest( 'com_komento.reports.search', 			'search', 			'', 'string' );
		$search 		= trim( JString::strtolower( $search ) );
		$order			= $mainframe->getUserStateFromRequest( 'com_komento.reports.filter_order', 	'filter_order', 	'created', 'cmd' );
		$orderDirection	= $mainframe->getUserStateFromRequest( 'com_komento.reports.filter_order_Dir',	'filter_order_Dir',	'DESC', 'word' );

		// Get data from the model
		$reportsModel	= Komento::getModel( 'reports', true );
		$comments		= $reportsModel->getData();
		$pagination		= $reportsModel->getPagination();

		$this->assignRef( 'comments'	, $comments );
		$this->assignRef( 'pagination'	, $pagination );
		$this->assign( 'state'			, $this->getPublishState($filter_publish));
		$this->assign( 'search'			, $search );
		$this->assign( 'order'			, $order );
		$this->assign( 'orderDirection'	, $orderDirection );

		$this->assign( 'component', $this->getComponentState($filter_component));

		$this->assign( 'columns', Komento::getConfig( 'com_komento_reports_columns', false ) );
		$this->assign( 'columnCount', 3 );

		parent::display($tpl);
	}

	function registerToolbar()
	{
		// JToolBarHelper::title( text, iconfilename )

		JToolBarHelper::title( JText::_( 'COM_KOMENTO_REPORTS' ), 'reports' );

		JToolBarHelper::back( JText::_( 'COM_KOMENTO_ADMIN_HOME' ) , 'index.php?option=com_komento');
		JToolBarHelper::divider();
		JToolBarHelper::custom( 'clear', 'kmt-clear-reports', '', JText::_( 'COM_KOMENTO_MARK_CLEAR' ) );
		JToolBarHelper::divider();
		JToolbarHelper::publishList();
		JToolbarHelper::unpublishList();
		JToolBarHelper::divider();
		JToolbarHelper::deleteList();
	}

	function getPublishState ($filter_publish = '*')
	{
		$publish[] = JHTML::_('select.option', '*', JText::_( 'COM_KOMENTO_ALL_STATUS' ) );
		$publish[] = JHTML::_('select.option', '1', JText::_( 'COM_KOMENTO_PUBLISHED' ) );
		$publish[] = JHTML::_('select.option', '0', JText::_( 'COM_KOMENTO_UNPUBLISHED' ) );

		return JHTML::_('select.genericlist', $publish, 'filter_publish', 'class="inputbox" .fa- onchange="submitform( );"', 'value', 'text', $filter_publish );
	}

	function getComponentState($filter_component = '*')
	{
		$model = Komento::getModel( 'comments' );
		$components = $model->getUniqueComponents();

		$component_state[] = JHTML::_('select.option', '*', JText::_( 'COM_KOMENTO_ALL_COMPONENTS' ) );

		foreach($components as $component)
		{
			$component_state[] = JHTML::_('select.option', $component, Komento::loadApplication( $component )->getComponentName() );
		}

		return JHTML::_('select.genericlist', $component_state, 'filter_component', 'class="inputbox" .fa- onchange="submitform();"', 'value', 'text', $filter_component);
	}

	function getColumnsState()
	{
		$columns = array(
			'comment',
			'published',
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

		$columnsConfig = Komento::getConfig( 'com_komento_reports_columns', false );

		$html = $this->renderColumnsConfiguration( $columns, $columnsConfig );

		return $html;
	}
}
