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

class EasyDiscussControllerUser extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		$this->registerTask( 'add' , 'edit' );
		$this->registerTask( 'save' , 'apply' );
	}

	public function apply()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$mainframe	= JFactory::getApplication();

		$db			= DiscussHelper::getDBO();
		$my			= JFactory::getUser();
		$acl		= JFactory::getACL();
		$config		= DiscussHelper::getConfig();

		// Create a new JUser object
		$user		= new JUser(JRequest::getVar( 'id', 0, 'post', 'int'));
		$original_gid = $user->get('gid');

		$post		= JRequest::get('post');

		$user->name	= $post['fullname'];

		if(DiscussHelper::getJoomlaVersion() >= '1.6')
		{
			$jformPost = JRequest::getVar('jform', array(), 'post', 'array');
			$post['params'] = $jformPost['params'];
		}

		if (!$user->bind($post))
		{
			DiscussHelper::setMessageQueue( $user->getError() , DISCUSS_QUEUE_ERROR );
			$this->_saveError( $user->id );
		}

		if(DiscussHelper::getJoomlaVersion() >= '1.6')
		{
			if( $user->get('id') == $my->get( 'id' ) && $user->get('block') == 1 )
			{
				DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_CANNOT_BLOCK_YOURSELF' ) , DISCUSS_QUEUE_ERROR );
				$this->_saveError( $user->id );
			}
			else if ( ( $user->authorise('core.admin') ) && $user->get('block') == 1 )
			{
				DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_CANNOT_BLOCK_SUPERUSER' ) , DISCUSS_QUEUE_ERROR );
				$this->_saveError( $user->id );
			}
			else if ( ( $user->authorise('core.admin') ) && !( $my->authorise('core.admin') ) )
			{
				DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_CANNOT_EDIT_SUPERUSER' ) , DISCUSS_QUEUE_ERROR );
				$this->_saveError( $user->id );
			}

			//replacing thr group name with group id so it is save correctly into the Joomla group table.
			$jformPost = JRequest::getVar('jform', array(), 'post', 'array');
			if(!empty($jformPost['groups']))
			{
				$user->groups = array();

				foreach($jformPost['groups'] as $groupid)
				{
					$user->groups[$groupid] = $groupid;
				}
			}

		}
		else
		{
			$objectID 	= $acl->get_object_id( 'users', $user->get('id'), 'ARO' );
			$groups 	= $acl->get_object_groups( $objectID, 'ARO' );
			$this_group = strtolower( $acl->get_group_name( $groups[0], 'ARO' ) );

			if( $user->get('id') == $my->get( 'id' ) && $user->get('block') == 1 )
			{
				DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_CANNOT_BLOCK_YOURSELF' ) , DISCUSS_QUEUE_ERROR );
				$this->_saveError( $user->id );
			}
			else if ( ( $this_group == 'super administrator' ) && $user->get('block') == 1 )
			{
				DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_CANNOT_BLOCK_SUPERUSER' ) , DISCUSS_QUEUE_ERROR );
				$this->_saveError( $user->id );
			}
			else if ( ( $this_group == 'administrator' ) && ( $my->get( 'gid' ) == 24 ) && $user->get('block') == 1 )
			{
				DiscussHelper::setMessageQueue( JText::_( 'WARNBLOCK' ) , DISCUSS_QUEUE_ERROR );
				$this->_saveError( $user->id );
			}
			else if ( ( $this_group == 'super administrator' ) && ( $my->get( 'gid' ) != 25 ) )
			{
				DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_CANNOT_EDIT_SUPERUSER' ) , DISCUSS_QUEUE_ERROR );
				$this->_saveError( $user->id );
			}
		}

		// Are we dealing with a new user which we need to create?
		$isNew 	= ($user->get('id') < 1);

		if(DiscussHelper::getJoomlaVersion() <= '1.5')
		{
			// do this step only for J1.5
			if (!$isNew)
			{
				// if group has been changed and where original group was a Super Admin
				if ( $user->get('gid') != $original_gid && $original_gid == 25 )
				{
					// count number of active super admins
					$query = 'SELECT COUNT( id )'
						. ' FROM #__users'
						. ' WHERE gid = 25'
						. ' AND block = 0'
					;
					$db->setQuery( $query );
					$count = $db->loadResult();

					if ( $count <= 1 )
					{
						DiscussHelper::setMessageQueue( JText::_( 'WARN_ONLY_SUPER' ) , DISCUSS_QUEUE_ERROR );

						// disallow change if only one Super Admin exists
						$this->setRedirect( 'index.php?option=com_easydiscuss&view=users' );
						return false;
					}
				}
			}
		}

		/*
		 * Lets save the JUser object
		 */
		if (!$user->save())
		{
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_CANNOT_SAVE_THE_USER_INFORMATION' ) , DISCUSS_QUEUE_ERROR );
			return $this->execute('edit');
		}

		// If updating self, load the new user object into the session
		if(DiscussHelper::getJoomlaVersion() <= '1.5')
		{
			// If updating self, load the new user object into the session
			if ($user->get('id') == $my->get('id'))
			{
				// Get an ACL object
				$acl = JFactory::getACL();

				// Get the user group from the ACL
				$grp = $acl->getAroGroup($user->get('id'));

				// Mark the user as logged in
				$user->set('guest', 0);
				$user->set('aid', 1);

				// Fudge Authors, Editors, Publishers and Super Administrators into the special access group
				if ($acl->is_group_child_of($grp->name, 'Registered')      ||
					$acl->is_group_child_of($grp->name, 'Public Backend'))    {
					$user->set('aid', 2);
				}

				// Set the usertype based on the ACL group name
				$user->set('usertype', $grp->name);

				$session = JFactory::getSession();
				$session->set('user', $user);
			}
		}

		$post	= JRequest::get( 'post' );

		if($isNew)
		{
			// if this is a new account, we unset the id so
			// that profile jtable will add new record properly.
			unset($post['id']);
		}

		$profile = DiscussHelper::getTable( 'Profile' );
		$profile->load( $user->id );
		$profile->bind($post);

		$file = JRequest::getVar( 'Filedata', '', 'Files', 'array' );
		if(! empty($file['name']))
		{
			$newAvatar			= DiscussHelper::uploadAvatar($profile, true);
			$profile->avatar	= $newAvatar;
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


		$profile->store();

		// Update points
		DiscussHelper::getHelper( 'ranks' )->assignRank( $profile->id, 'points' );

		$app		= JFactory::getApplication();
		$task 		= $this->getTask();

		$url 		= $task == 'apply' ? 'index.php?option=com_easydiscuss&view=user&id=' . $profile->id : 'index.php?option=com_easydiscuss&view=users';

		DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_USER_INFORMATION_SAVED') , DISCUSS_QUEUE_SUCCESS );

		$app->redirect( $url );
	}

	function _saveError( $id = '' )
	{
		$url 	= $this->getTask() == 'apply' ? 'index.php?option=com_easydiscuss&view=user&id=' . $profile->id : 'index.php?option=com_easydiscuss&view=users';
		$app 	= JFactory::getApplication();

		$app->redirect( $url );
	}

	function cancel()
	{
		$this->setRedirect( 'index.php?option=com_easydiscuss&view=users' );

		return;
	}

	function edit()
	{
		JRequest::setVar( 'view', 'user' );
		JRequest::setVar( 'id' , JRequest::getVar( 'id' , '' , 'REQUEST' ) );

		parent::display();
	}

	public function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$cid = JRequest::getVar( 'cid', array(), '', 'array' );

		JArrayHelper::toInteger( $cid );

		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'COM_EASYDISCUSS_SELECT_USER_TO_DELETE', true ) );
		}

		$result = null;

		foreach ($cid as $id)
		{
			$result = null;
			if(DiscussHelper::getJoomlaVersion() >= '1.6')
			{
				$result	= $this->_removeUser16($id);
			}
			else
			{
				$result	= $this->_removeUser($id);
			}

			if(!$result['success']) {
				DiscussHelper::setMessageQueue( $result['msg'] , DISCUSS_QUEUE_ERROR );
				$this->setRedirect( 'index.php?option=com_easydiscuss&view=users', $result['msg']);
			}
		}

		DiscussHelper::setMessageQueue( $result['msg'] , DISCUSS_QUEUE_SUCCESS );
		$this->setRedirect( 'index.php?option=com_easydiscuss&view=users', $result['msg']);
	}

	private function logout()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$mainframe	= JFactory::getApplication();

		$task	= $this->getTask();
		$cids	= JRequest::getVar( 'cid', array(), '', 'array' );
		$client	= JRequest::getVar( 'client', 0, '', 'int' );
		$id		= JRequest::getVar( 'id', 0, '', 'int' );

		JArrayHelper::toInteger($cids);

		if ( count( $cids ) < 1 ) {
			$this->setRedirect( 'index.php?option=com_easydiscuss&view=users', JText::_( 'COM_EASYDISCUSS_USER_DELETED' ) );
			return false;
		}

		foreach($cids as $cid)
		{
			$options = array();

			if ($task == 'logout' || $task == 'block') {
				$options['clientid'][] = 0; //site
				$options['clientid'][] = 1; //administrator
			} else if ($task == 'flogout') {
				$options['clientid'][] = $client;
			}

			$mainframe->logout((int)$cid, $options);
		}


		$msg = JText::_( 'COM_EASYDISCUSS_USER_SESSION_ENDED' );
		switch ( $task )
		{
			case 'flogout':
				$this->setRedirect( 'index.php', $msg );
				break;

			case 'remove':
			case 'block':
				return;
				break;

			default:
				$this->setRedirect( 'index.php?option=com_easydiscuss&view=users', $msg );
				break;
		}
	}

	private function _removeUser16($id)
	{
		$db				= DiscussHelper::getDBO();
		$currentUser	= JFactory::getUser();

		$user			= JFactory::getUser($id);
		$isUserSA		= $user->authorise('core.admin');

		if($isUserSA)
		{
			$msg = JText::_( 'You cannot delete a Super Administrator' );
		}
		else if($id == $currentUser->get( 'id' ))
		{
			$msg = JText::_( 'You cannot delete Yourself!' );
		}
		else
		{
			$count = 2;

			if($isUserSA)
			{
				$saUsers	= DiscussHelper::getSAUsersIds();
				$count		= count($saUsers);
			}

			if ($count <= 1 && $isUserSA)
			{
				// cannot delete Super Admin where it is the only one that exists
				$msg = "You cannot delete this Super Administrator as it is the only active Super Administrator for your site";
			}
			else
			{
				// delete user
				$user->delete();
				$msg = JText::_('User Deleted.');

				JRequest::setVar( 'task', 'remove' );
				JRequest::setVar( 'cid', $id );

				// delete user acounts active sessions
				$this->logout();
				$success = true;
			}

		}

		$result['success']	= $success;
		$result['msg']		= $msg;

		return $result;
	}

	private function _removeUser($id)
	{
		$db				= DiscussHelper::getDBO();
		$currentUser	= JFactory::getUser();
		$acl			= JFactory::getACL();

		// check for a super admin ... can't delete them
		$objectID	= $acl->get_object_id( 'users', $id, 'ARO' );
		$groups		= $acl->get_object_groups( $objectID, 'ARO' );
		$this_group	= strtolower( $acl->get_group_name( $groups[0], 'ARO' ) );

		$success	= false;
		$msg		= '';

		if ( $this_group == 'super administrator' )
		{
			$msg = JText::_( 'COM_EASYDISCUSS_CANNOT_EDIT_SUPER_ADMIN_ACCOUNT' );
		}
		else if ( $id == $currentUser->get( 'id' ) )
		{
			$msg = JText::_( 'COM_EASYDISCUSS_CANNOT_DELETE_SELF' );
		}
		else if ( ( $this_group == 'administrator' ) && ( $currentUser->get( 'gid' ) == 24 ) )
		{
			$msg = JText::_( 'WARNDELETE' );
		}
		else
		{
			$user = JUser::getInstance((int)$id);
			$count = 2;

			if ( $user->get( 'gid' ) == 25 )
			{
				// count number of active super admins
				$query = 'SELECT COUNT( id )'
					. ' FROM #__users'
					. ' WHERE gid = 25'
					. ' AND block = 0'
				;
				$db->setQuery( $query );
				$count = $db->loadResult();
			}

			if ( $count <= 1 && $user->get( 'gid' ) == 25 )
			{
				// cannot delete Super Admin where it is the only one that exists
				$msg = "You cannot delete this Super Administrator as it is the only active Super Administrator for your site";
			}
			else
			{
				// delete user
				$user->delete();
				$msg = JText::_('COM_EASYDISCUSS_USER_DELETED');

				JRequest::setVar( 'task', 'remove' );
				JRequest::setVar( 'cid', $id );

				// delete user acounts active sessions
				$this->logout();
				$success	= true;
			}
		}

		$result['success']	= $success;
		$result['msg']		= $msg;

		return $result;
	}
}
