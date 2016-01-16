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

class KomentoViewMailq extends KomentoAdminView
{
	function display($tpl = null)
	{
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();

		if( Komento::joomlaVersion() >= '1.6' )
		{
			if(!$user->authorise('komento.manage.mailq' , 'com_komento') )
			{
				$mainframe->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				$mainframe->close();
			}
		}

		// Get data from the model
		$model			= Komento::getModel( 'Mailq', true );

		$pagination		= $model->getPagination();
		$items			= $model->getItems();

		$this->assignRef( 'items'		, $items );
		$this->assignRef( 'pagination'	, $pagination );

		parent::display($tpl);
	}

	function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_KOMENTO_MAIL_QUEUE' ), 'mailq' );

		JToolBarHelper::back( JText::_( 'COM_KOMENTO_ADMIN_HOME' ) , 'index.php?option=com_komento');
		JToolBarHelper::divider();
		JToolBarHelper::custom('purge','purge','icon-32-unpublish.png', 'COM_KOMENTO_PURGE_ITEMS', false);
	}
}
