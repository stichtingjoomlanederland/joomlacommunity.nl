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

class EasyDiscussViewUser extends EasyDiscussAdminView
{
	public function display($tpl = null)
	{
		// Initialise variables
		$config		= DiscussHelper::getConfig();

		$id			= JRequest::getInt('id');
		$profile	= JTable::getInstance( 'Profile' , 'Discuss' );
		$profile->load( $id );

		$userparams	= DiscussHelper::getRegistry( $profile->get('params') );
		$siteDetails = DiscussHelper::getRegistry( $profile->get('site') );


		$avatarIntegration = $config->get( 'layout_avatarIntegration', 'default' );

		$user		= JFactory::getUser( $id );
		$isNew		= ($user->id == 0) ? true : false;

		$badges		= $profile->getBadges();

		$model		= $this->getModel( 'Badges' );
		$history	= $model->getBadgesHistory( $profile->id );

		$params		= $user->getParameters(true);

		// Badge id's that are assigned to the user.
		$badgeIds	= '';

		for( $i = 0; $i < count( $badges ); $i++ )
		{
			$badgeIds	.= $badges[$i]->id;

			if( next( $badges ) !== false )
			{
				$badgeIds	.= ',';
			}

			$badgeUser 	= DiscussHelper::getTable( 'BadgesUsers' );
			$badgeUser->loadByUser( $id , $badges[ $i ]->id );

			$badges[ $i ]->reference_id 	= $badgeUser->id;
			$badges[ $i ]->custom 			= $badgeUser->custom;
		}


		$this->assign( 'badgeIds'				, $badgeIds );

		$this->assignRef( 'badges'				, $badges );
		$this->assignRef( 'history'				, $history );
		$this->assignRef( 'config'				, $config );
		$this->assignRef( 'profile'				, $profile );
		$this->assignRef( 'user'				, $user );
		$this->assignRef( 'isNew'				, $isNew );
		$this->assignRef( 'params'				, $params );
		$this->assignRef( 'avatarIntegration'	, $avatarIntegration );
		$this->assignRef( 'userparams'			, $userparams );
		$this->assignRef( 'siteDetails'			, $siteDetails );

		parent::display($tpl);
	}

	public function registerToolbar()
	{
		$id		= JRequest::getInt('id');
		$user	= JTable::getInstance( 'User' , 'JTable' );
		$user->load( $id );

		$title	= ($user->id == 0) ? JText::_('COM_EASYDISCUSS_NEW_USER') : JText::sprintf( 'COM_EASYDISCUSS_EDITING_USER' , $user->name );

		JToolBarHelper::title( $title , 'users' );

		JToolBarHelper::back( JText::_( 'COM_EASYDISCUSS_BACK' ) , 'index.php?option=com_easydiscuss&view=users' );
		JToolBarHelper::divider();

		JToolBarHelper::apply();
		JToolBarHelper::save();

		JToolBarHelper::divider();
		JToolBarHelper::cancel();
	}
}
