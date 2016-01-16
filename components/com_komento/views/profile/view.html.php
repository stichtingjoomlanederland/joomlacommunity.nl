<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die();

require_once( KOMENTO_ROOT . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'view.php' );

class KomentoViewProfile extends KomentoView
{
	public function display( $tpl = null )
	{
		$konfig			= Komento::getKonfig();
		$id				= JRequest::getInt( 'id', 0 );

		// @task: If profiles are disabled, do not show the profile here.
		if( !$konfig->get( 'profile_enable' ) )
		{
			$app 	= JFactory::getApplication();
			$app->redirect( 'index.php' , JText::_( 'COM_KOMENTO_PROFILE_SYSTEM_DISABLED' ) );
			$app->close();
		}

		$profileModel	= Komento::getModel( 'Profile' );
		$activityModel	= Komento::getModel( 'Activity' );
		$commentsModel	= Komento::getModel( 'Comments' );
		$actionsModel	= Komento::getModel( 'Actions' );

		$count			= new stdClass;

		$user = JFactory::getUser();

		if( $id === 0 && $user->id > 0 )
		{
			$id = $user->id;
		}

		// Block non-exists profile
		if (!$profileModel->exists( $id ))
		{
			echo JText::_( 'COM_KOMENTO_PROFILE_NOT_FOUND' );
			return;
		}

		// TODO: Block custom profile id
		// ..

		$profile		= Komento::getProfile( $id );
		// $activities		= $activityModel->getUserActivities( $profile->id );
		$count->totalComments	= $commentsModel->getTotalComment( $profile->id );
		$count->likesReceived	= $actionsModel->getLikesReceived( $profile->id );
		$count->likesGiven		= $actionsModel->getLikesGiven( $profile->id );

		// Set Pathway
		// Check if Komento profile menu item exist before setting profile pathway
		$app	= JFactory::getApplication();
		$menu	= $app->getMenu();
		$item	= $menu->getActive();
		if( empty( $item ) || $item->query['view'] != 'profile' )
		{
			$this->setPathway( JText::_('COM_KOMENTO_PROFILE') , '' );
		}
		$this->setPathway( $profile->getName() , '' );

		// Set browser title
		$document = JFactory::getDocument();
		$document->setTitle( JText::_('COM_KOMENTO_USER_PROFILE') . ' - ' . $profile->getName() );

		// set component to com_komento
		Komento::setCurrentComponent( 'com_komento' );
		$theme = Komento::getTheme();
		$theme->set( 'profile', $profile );
		$theme->set( 'count', $count );
		// $theme->set( 'activities', $activities );
		echo $theme->fetch('profile/profile.php');
	}

	function getModel( $name = null )
	{
		return Komento::getModel( 'Profile' );
	}
}
