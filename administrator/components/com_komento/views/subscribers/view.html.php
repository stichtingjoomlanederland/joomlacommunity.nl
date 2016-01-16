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

class KomentoViewSubscribers extends KomentoAdminView
{
	function display($tpl = null)
	{
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();

		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();

		if( Komento::joomlaVersion() >= '1.6' )
		{
			if(!$user->authorise('komento.manage.subscribers' , 'com_komento') )
			{
				$mainframe->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				$mainframe->close();
			}
		}

		$filter_component = $mainframe->getUserStateFromRequest( 'com_komento.subscribers.filter_component', 'filter_component', '*', 'string' );
		$order			= $mainframe->getUserStateFromRequest( 'com_komento.subscribers.filter_order', 	'filter_order', 	'created', 'cmd' );
		$orderDirection	= $mainframe->getUserStateFromRequest( 'com_komento.subscribers.filter_order_Dir',	'filter_order_Dir',	'DESC', 'word' );

		// Get data from the model
		$subscriptionModel	= Komento::getModel( 'subscription' );
		$subscribers		= $subscriptionModel->getData();
		$pagination			= $subscriptionModel->getPagination();

		foreach( $subscribers as $subscriber )
		{
			$subscriber = self::process( $subscriber );
		}

		$this->assignRef( 'subscribers'	, $subscribers );
		$this->assignRef( 'pagination'	, $pagination );
		$this->assign( 'order'			, $order );
		$this->assign( 'orderDirection'	, $orderDirection );

		$this->assign( 'component', $this->getComponentState($filter_component));

		parent::display($tpl);
	}

	function process( $row )
	{
		Komento::setCurrentComponent( $row->component );

		// set extension object
		$row->extension = Komento::loadApplication( $row->component )->load( $row->cid );

		if( $row->extension === false )
		{
			$row->extension = Komento::getErrorApplication( $row->component, $row->cid );
		}

		// get permalink
		$row->pagelink = $row->extension->getContentPermalink();

		// set content title
		$row->contenttitle = $row->extension->getContentTitle();

		// set component title
		$row->componenttitle = $row->extension->getComponentName();

		return $row;
	}

	function registerToolbar()
	{
		// JToolBarHelper::title( text, iconfilename )

		JToolBarHelper::title( JText::_( 'COM_KOMENTO_SUBSCRIBERS' ), 'subscribers' );

		JToolBarHelper::back( JText::_( 'COM_KOMENTO_ADMIN_HOME' ) , 'index.php?option=com_komento');
		JToolBarHelper::divider();
		JToolbarHelper::deleteList();
	}

	function getComponentState($filter_component = '*')
	{
		$model = Komento::getModel( 'subscription' );
		$components = $model->getUniqueComponents();

		$component_state[] = JHTML::_('select.option', '*', JText::_( 'COM_KOMENTO_ALL_COMPONENTS' ) );

		foreach($components as $component)
		{
			$component_state[] = JHTML::_('select.option', $component, Komento::loadApplication( $component )->getComponentName() );
		}

		return JHTML::_('select.genericlist', $component_state, 'filter_component', 'class="inputbox" .fa- onchange="submitform();"', 'value', 'text', $filter_component);
	}
}
