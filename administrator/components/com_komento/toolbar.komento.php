<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'toolbar.php' );

$submenus	= array(
	'komento'		=> JText::_('COM_KOMENTO_TAB_HOME'),
	'system'		=> JText::_('COM_KOMENTO_TAB_SYSTEM'),
	'integrations'	=> JText::_('COM_KOMENTO_TAB_INTEGRATIONS'),
	'comments'		=> JText::_('COM_KOMENTO_TAB_COMMENTS'),
	'pending'		=> JText::_('COM_KOMENTO_TAB_PENDINGS'),
	'reports'		=> JText::_('COM_KOMENTO_TAB_REPORTS'),
	'subscribers'	=> JText::_('COM_KOMENTO_TAB_SUBSCRIBERS'),
	'acl'			=> JText::_('COM_KOMENTO_TAB_ACL'),
	'migrators'		=> JText::_('COM_KOMENTO_TAB_MIGRATORS'),
	'mailq'			=> JText::_('COM_KOMENTO_TAB_MAILQ'),
);

$current	= JRequest::getVar( 'view' , 'komento' );

$user		= JFactory::getUser();

// @task: For the frontpage, we just show the the icons.
if( $current == 'komento' )
{
	$submenus	= array( 'komento' => JText::_('COM_KOMENTO_TAB_HOME') );
}
foreach( $submenus as $view => $title )
{
	// Check submenus to add based on JACL
	if( !Komento::isJoomla15() )
	{
		$jacl = $current === 'komento' ? 'core.manage' : 'komento.manage.' . $view;
		if( !$user->authorise( $jacl, 'com_komento' ) )
		{
			continue;
		}
	}

	$isActive	= ( $current == $view );

	$notification = '';
	$count = 0;

	switch( $view )
	{
		case 'pendings':
				$count = Komento::getModel( 'comments' )->getCount( 'all', 'all', array( 'published' => 2 ) );
			break;
		case 'reports':
				$count = Komento::getModel( 'reports', true )->getTotal();
			break;
	}

	if( $count > 0 )
	{
		$notification = '<b>' . $count . '</b>';
	}

 	JSubMenuHelper::addEntry( $title . $notification, 'index.php?option=com_komento&view=' . $view , $isActive );
}
