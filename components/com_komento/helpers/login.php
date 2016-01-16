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

defined('_JEXEC') or die('Restricted access');

class KomentoLoginHelper
{
	public function getRegistrationLink()
	{
		$config = Komento::getConfig();

		$link	= JRoute::_( 'index.php?option=com_users&view=registration' );

		switch( $config->get( 'login_provider' ) )
		{
			case 'cb':
				$link 	= JRoute::_( 'index.php?option=com_comprofiler&task=registers' );
				break;
			break;

			case 'joomla':
				if( Komento::isJoomla15() )
				{
					$link	= JRoute::_( 'index.php?option=com_user&view=register' );
				}
				else
				{
					$link	= JRoute::_( 'index.php?option=com_users&view=registration' );
				}
			break;

			case 'jomsocial':
				$link	= JRoute::_( 'index.php?option=com_community&view=register' );
			break;

			case 'easysocial':
				$es = Komento::getHelper( 'EasySocial' );

				if( $es->exists() )
				{
					$link = FRoute::registration();
				}
			break;
		}

		return $link;
	}

	public function getLoginLink( $returnURL = '' )
	{
		$config 	= Komento::getConfig();

		if( !empty( $returnURL ) )
		{
			$returnURL	= '&return=' . $returnURL;
		}

		$link = JRoute::_('index.php?option=com_users&view=login' . $returnURL );

		switch( $config->get( 'login_provider' ) )
		{
			case 'cb':
				$link 	= JRoute::_( 'index.php?option=com_comprofiler&task=login' . $returnURL);
				break;
			break;

			case 'joomla':
			case 'jomsocial':
				if( Komento::isJoomla15() )
				{
					$link	= JRoute::_( 'index.php?option=com_user&view=login' . $returnURL );
				}
				else
				{
					$link 	= JRoute::_('index.php?option=com_users&view=login' . $returnURL );
				}
			break;

			case 'easysocial':
				$es = Komento::getHelper( 'EasySocial' );

				if( $es->exists() )
				{
					$link = FRoute::login();
				}
			break;
		}

		return $link;
	}

	public function getResetPasswordLink()
	{
		$config	= Komento::getConfig();

		$link = JRoute::_( 'index.php?option=com_users&view=reset' );

		switch( $config->get( 'login_provider' ) )
		{
			case 'cb':
				$link 		= JRoute::_( 'index.php?option=com_comprofiler&task=lostpassword' );
			break;

			case 'joomla':
			case 'jomsocial':
				if( Komento::isJoomla15() )
				{
					$link	= JRoute::_( 'index.php?option=com_user&view=reset' );
				}
				else
				{
					$link	= JRoute::_( 'index.php?option=com_users&view=reset' );
				}
			break;

			case 'easysocial':
				$es = Komento::getHelper( 'EasySocial' );

				if( $es->exists() )
				{
					$link = FRoute::profile( array( 'layout' => 'forgetPassword' ) );
				}
			break;
		}

		return $link;
	}

	public function getRemindUsernameLink()
	{
		$config 	= Komento::getConfig();

		$link = JRoute::_( 'index.php?option=com_users&view=remind' );

		switch( $config->get( 'login_provider' ) )
		{
			case 'easysocial':
				$es = Komento::getHelper( 'EasySocial' );

				if( $es->exists() )
				{
					$link = FRoute::profile( array( 'layout' => 'forgetPassword' ) );
				}
			break;

			default:

				if( Komento::isJoomla15() )
				{
					$link	= JRoute::_( 'index.php?option=com_user&view=remind' );
				}
				else
				{
					$link	= JRoute::_( 'index.php?option=com_users&view=remind' );
				}

			break;
		}

		return $link;
	}
}
