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

jimport('joomla.application.component.controller');
require_once JPATH_ROOT . '/components/com_easydiscuss/helpers/helper.php';

class EasyDiscussControllerProfile extends EasyDiscussController
{
	/**
	 * Display the view
	 *
	 * @since 0.1
	 */
	function display($cachable = false, $urlparams = false)
	{
		$document	= JFactory::getDocument();
		$viewType	= $document->getType();
		$viewName	= JRequest::getCmd( 'view', $this->getName() );
		$view 		= $this->getView( $viewName,'',  $viewType);
		$view->display();
	}

	function saveProfile()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$mainframe	= JFactory::getApplication();
		$config		= DiscussHelper::getConfig();

		$post		= JRequest::get( 'post' );

		array_walk($post, array($this, '_trim') );

		if(! $this->_validateProfileFields($post))
		{
			$this->setRedirect( DiscussRouter::_('index.php?option=com_easydiscuss&view=profile&layout=edit' , false ) );
			return;
		}

		$my			= JFactory::getUser();
		$my->name	= $post['fullname'];


		// We check for password2 instead off password because apparently it is still autofill the form although is autocomplete="off"
		if(!empty($post['password2']))
		{
			$my->password = $post['password'];
			$my->bind($post);
		}

		// Cheap fix: Do not allow user to override `edited` field.
		// Ideally, this should just be passed as ignore into the table.
		if( isset( $post['edited' ] ) )
		{
			unset( $post[ 'edited' ] );
		}

		// column mapping.
		$post['location'] = $post['address'];

		$profile	= DiscussHelper::getTable( 'Profile' );
		$profile->load( $my->id );
		$profile->bind( $post );

		//save avatar
		$file = JRequest::getVar( 'Filedata', '', 'files', 'array' );
		if(! empty($file['name']))
		{
			$newAvatar			= $this->_upload( $profile );

			// @rule: If this is the first time the user is changing their profile picture, give a different point
			if( $profile->avatar == 'default.png' )
			{
				// @rule: Process AUP integrations
				DiscussHelper::getHelper( 'Aup' )->assign( DISCUSS_POINTS_NEW_AVATAR , $my->id , $newAvatar );
			}
			else
			{
				// @rule: Process AUP integrations
				DiscussHelper::getHelper( 'Aup' )->assign( DISCUSS_POINTS_UPDATE_AVATAR , $my->id , $newAvatar );
			}

			// @rule: Badges when they change their profile picture
			DiscussHelper::getHelper( 'History' )->log( 'easydiscuss.new.avatar' , $my->id , JText::_( 'COM_EASYDISCUSS_BADGES_HISTORY_UPDATED_AVATAR') );

			DiscussHelper::getHelper( 'Badges' )->assign( 'easydiscuss.new.avatar' , $my->id );
			DiscussHelper::getHelper( 'Points' )->assign( 'easydiscuss.new.avatar' , $my->id );

			// Reset the points
			$profile->updatePoints();

			$profile->avatar    = $newAvatar;
		}

		//save params
		$userparams	= DiscussHelper::getRegistry('');

		if ( isset($post['facebook']) )
		{
			$userparams->set( 'facebook', $post['facebook'] );
		}
		if ( isset($post['show_facebook']) )
		{
			$userparams->set( 'show_facebook', $post['show_facebook']);
		}


		if ( isset($post['twitter']) )
		{
			$userparams->set( 'twitter', $post['twitter'] );
		}

		if ( isset($post['show_twitter']) )
		{
			$userparams->set( 'show_twitter', $post['show_twitter']);
		}
		if ( isset($post['linkedin']) )
		{
			$userparams->set( 'linkedin', $post['linkedin'] );
		}
		if ( isset($post['show_linkedin']) )
		{
			$userparams->set( 'show_linkedin', $post['show_linkedin']);
		}
		if ( isset($post['skype']) )
		{
			$userparams->set( 'skype', $post['skype'] );
		}
		if ( isset($post['show_skype']) )
		{
			$userparams->set( 'show_skype', $post['show_skype']);
		}
		if ( isset($post['website']) )
		{
			$userparams->set( 'website', $post['website'] );
		}
		if ( isset($post['show_website']) )
		{
			$userparams->set( 'show_website', $post['show_website']);
		}

		$profile->params	= $userparams->toString();

		// Save site details
		$siteDetails	= DiscussHelper::getRegistry('');
		if ( isset($post['siteUrl']) )
		{
			$siteDetails->set( 'siteUrl', $post['siteUrl'] );
		}
		if ( isset($post['siteUsername']) )
		{
			$siteDetails->set( 'siteUsername', $post['siteUsername'] );
		}
		if ( isset($post['sitePassword']) )
		{
			$siteDetails->set( 'sitePassword', $post['sitePassword'] );
		}
		if ( isset($post['ftpUrl']) )
		{
			$siteDetails->set( 'ftpUrl', $post['ftpUrl'] );
		}
		if ( isset($post['ftpUsername']) )
		{
			$siteDetails->set( 'ftpUsername', $post['ftpUsername'] );
		}
		if ( isset($post['ftpPassword']) )
		{
			$siteDetails->set( 'ftpPassword', $post['ftpPassword'] );
		}
		if ( isset($post['optional']) )
		{
			$siteDetails->set( 'optional', $post['optional'] );
		}

		$profile->site	= $siteDetails->toString();

		if( $profile->store() && $my->save(true) )
		{
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_PROFILE_SAVED' )  , 'info');

			// @rule: Badges when they change their profile picture
			DiscussHelper::getHelper( 'History' )->log( 'easydiscuss.update.profile' , $my->id , JText::_( 'COM_EASYDISCUSS_BADGES_HISTORY_UPDATED_PROFILE') );
			DiscussHelper::getHelper( 'Badges' )->assign( 'easydiscuss.update.profile' , $my->id );

			// Only give points the first time the user edits their profile.
			if( !$profile->edited )
			{
				DiscussHelper::getHelper( 'Points' )->assign( 'easydiscuss.update.profile' , $my->id );

				// Reload profile again because the points might already update the user's point.
				$updatedProfile 		= DiscussHelper::getTable( 'Profile' );
				$updatedProfile->load( $my->id , false , true );
				$updatedProfile->edited = true;
				$updatedProfile->store();
			}
		}
		else
		{
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_PROFILE_SAVE_ERROR' )  , 'error');
			$this->setRedirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=profile&layout=edit' , false ) );
			return;
		}

		$this->setRedirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=profile&layout=edit' , false ) );
	}

	function _trim(&$text)
	{
		$text = JString::trim($text);
	}

	function _validateProfileFields($post)
	{
		$mainframe	= JFactory::getApplication();
		$valid		= true;


		if(JString::strlen($post['fullname']) == 0)
		{
			$message	= JText::_( 'COM_EASYDISCUSS_REALNAME_EMPTY' );
			DiscussHelper::setMessageQueue( $message , DISCUSS_QUEUE_ERROR );
			return false;
		}

		if(JString::strlen($post['nickname']) == 0)
		{
			$message	= JText::_( 'COM_EASYDISCUSS_NICKNAME_EMPTY'  );
			DiscussHelper::setMessageQueue( $message , DISCUSS_QUEUE_ERROR );
			return false;
		}

		if( !empty( $post[ 'password' ] ) )
		{
			if( JString::strlen( $post[ 'password' ] ) < 4 )
			{
				$message	= JText::_( 'COM_EASYDISCUSS_PROFILE_PASSWORD_TOO_SHORT' );
				DiscussHelper::setMessageQueue( $message , DISCUSS_QUEUE_ERROR );
				return false;			
			}
		}
		
		if(!empty($post['password2']))
		{			
			if ( $post['password'] != $post['password2'] )
			{
				$message	= JText::_( 'COM_EASYDISCUSS_PROFILE_PASSWORD_NOT_MATCH' );
				DiscussHelper::setMessageQueue( $message , DISCUSS_QUEUE_ERROR );
				return false;
			}
		}

		return $valid;
	}

	function _upload( $profile, $type = 'profile' )
	{
		$newAvatar  = '';

		//can do avatar upload for post in future.

		$newAvatar  = DiscussHelper::uploadAvatar($profile);

		return $newAvatar;
	}

	public function removePicture()
	{
		$my 		= JFactory::getUser();

		if( !$my->id )
		{
			return $this->setRedirect( DiscussRouter::_( 'index.php?option=com_easydiscuss' , false ) );
		}

		$profile	= DiscussHelper::getTable( 'Profile' );
		$profile->load( $my->id );
		
		// Delete the user's avatar.
		$profile->deleteAvatar();

		DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_PROFILE_AVATAR_REMOVED_SUCCESSFULLY' ) , DISCUSS_QUEUE_SUCCESS );

		$url 	= DiscussRouter::_( 'index.php?option=com_easydiscuss&view=profile&layout=edit' , false );

		$this->setRedirect( $url );
	}

	public function disableUser()
	{
		// Only allow site admin to disable this.
		if( !DiscussHelper::isSiteAdmin() )
		{
			return $this->setRedirect( DiscussRouter::_( 'index.php?option=com_easydiscuss' , false ) );
		}

		$userId = JRequest::getInt('id');

		$db = DiscussHelper::getDBO();
		$query = 'UPDATE ' . $db->nameQuote( '#__users' )
				. ' SET ' . $db->nameQuote( 'block' ) . '=' . $db->quote( 1 )
				. ' WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->quote( $userId );

		$db->setQuery( $query );
		$result = $db->query();

		if( !$result )
		{
			return $this->setRedirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=profile&id=' . $userId , false ) );
		}

		$message	= JText::_( 'COM_EASYDISCUSS_USER_DISABLED' );
		DiscussHelper::setMessageQueue( $message , DISCUSS_QUEUE_SUCCESS );
		$this->setRedirect( DiscussRouter::_( 'index.php?option=com_easydiscuss' , false ) );
	}
}
