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

require_once( DISCUSS_ROOT . '/views.php' );

class EasyDiscussViewBadges extends EasyDiscussView
{
	public function display( $tmpl = null )
	{
		$doc		= JFactory::getDocument();
		DiscussHelper::setPageTitle( JText::_( 'COM_EASYDISCUSS_BADGES_TITLE' ) );

		$mainframe	= JFactory::getApplication();
		$my			= JFactory::getUser();

		$config		= DiscussHelper::getConfig();

		$this->setPathway( JText::_( 'COM_EASYDISCUSS_BADGES' ) );

		// [model:badges]
		$model		= $this->getModel( 'Badges' );

		$badges		= $model->getBadges();



		$theme		= new DiscussThemes();
		$theme->set( 'badges' , $badges );

		echo $theme->fetch( 'badges.php' );
	}

	public function listings()
	{
		$app		= JFactory::getApplication();
		$config		= DiscussHelper::getConfig();
		$id			= JRequest::getInt( 'id' );

		if( empty( $id ) )
		{
			$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=badges' , false ) , JText::_( 'COM_EASYDISCUSS_INVALID_BADGE' ) );
			$app->close();
		}

		$badge		= DiscussHelper::getTable( 'Badges' );
		$badge->load( $id );

		$this->setPathway( JText::_( 'COM_EASYDISCUSS_BADGES' ) , DiscussRouter::_( 'index.php?option=com_easydiscuss&view=badges' ) );
		$this->setPathway( JText::_( $badge->get( 'title') ) );

		DiscussHelper::setPageTitle( JText::sprintf( 'COM_EASYDISCUSS_VIEWING_BADGE_TITLE' , $this->escape( $badge->title ) ) );
		$users		= $badge->getUsers();

		$theme		= new DiscussThemes();
		$theme->set( 'badge'	, $badge );
		$theme->set( 'users'	, $users );
		echo $theme->fetch( 'badge.php' );
	}
}
