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

class EasyDiscussControllerRoles extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		$this->registerTask( 'add' , 'edit' );
		$this->registerTask( 'savePublishNew' , 'save' );
	}

	public function save()
	{
		$mainframe	= JFactory::getApplication();

		$message	= '';
		$type		= 'success';

		$task 		= $this->getTask();


		if( JRequest::getMethod() == 'POST' )
		{
			$post	= JRequest::get( 'post' );

			if(empty($post['title']))
			{
				$mainframe->enqueueMessage(JText::_('COM_EASYDISCUSS_INVALID_ROLES'), 'error');

				$url  = 'index.php?option=com_easydiscuss&view=roles';
				$mainframe->redirect(JRoute::_($url, false));
				return;
			}

			$my			= JFactory::getUser();
			$post['created_user_id']	= $my->id;
			$roleId	= JRequest::getVar( 'role_id' , '' );
			$role		= DiscussHelper::getTable( 'Role' );

			$role->load( $roleId );

			$role->bind( $post );

			$role->title = JString::trim($role->title);

			if (!$role->store())
			{
				JError::raiseError(500, $role->getError() );
			}
			else
			{
				$message	= JText::_( 'COM_EASYDISCUSS_ROLE_SAVED' );
			}
		}
		else
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_FORM_METHOD');
			$type		= 'error';
		}

		DiscussHelper::setMessageQueue( $message , $type );

		if( $task == 'savePublishNew' )
		{
			$mainframe->redirect( 'index.php?option=com_easydiscuss&view=roles&task=roles.edit' );
			$mainframe->close();
		}

		$mainframe->redirect( 'index.php?option=com_easydiscuss&view=roles' );
	}

	public function cancel()
	{
		$this->setRedirect( 'index.php?option=com_easydiscuss&view=roles' );

		return;
	}

	public function edit()
	{
		JRequest::setVar( 'view', 'role' );
		JRequest::setVar( 'roleid' , JRequest::getVar( 'roleid' , '' , 'REQUEST' ) );

		parent::display();
	}

	public function remove()
	{
		$roles		= JRequest::getVar( 'cid' , '' , 'POST' );

		$message	= '';
		$type		= 'success';

		if( empty( $roles ) )
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_ROLE_ID');
			$type		= 'error';
		}
		else
		{
			$table		= DiscussHelper::getTable( 'Role' );
			foreach( $roles as $role )
			{
				$table->load( $role );

				if( !$table->delete() )
				{
					$message	= JText::_( 'COM_EASYDISCUSS_REMOVE_ROLE_ERROR' );
					$type		= 'error';

					DiscussHelper::setMessageQueue( $message , $type );

					$this->setRedirect( 'index.php?option=com_easydiscuss&view=roles' );
					return;
				}
			}

			$message	= JText::_('COM_EASYDISCUSS_ROLE_DELETED');
		}

		DiscussHelper::setMessageQueue( $message , $type );

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=roles' );
	}

	public function publish()
	{
		$roles	= JRequest::getVar( 'cid' , array(0) , 'POST' );

		$message	= '';
		$type		= 'success';

		if( count( $roles ) <= 0 )
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_ROLE_ID');
			$type		= 'error';
		}
		else
		{
			$model		= $this->getModel( 'Roles' );

			if( $model->publish( $roles , 1 ) )
			{
				$message	= JText::_('COM_EASYDISCUSS_ROLE_PUBLISHED_MSG');
			}
			else
			{
				$message	= JText::_('COM_EASYDISCUSS_ROLE_PUBLISH_ERROR');
				$type		= 'error';
			}

		}

		DiscussHelper::setMessageQueue( $message , $type );

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=roles' );
	}

	public function unpublish()
	{
		$roles		= JRequest::getVar( 'cid' , array(0) , 'POST' );

		$message	= '';
		$type		= 'success';

		if( count( $roles ) <= 0 )
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_ROLE_ID');
			$type		= 'error';
		}
		else
		{
			$model		= $this->getModel( 'Roles' );

			if( $model->publish( $roles , 0 ) )
			{
				$message	= JText::_('COM_EASYDISCUSS_ROLE_UNPUBLISHED');
			}
			else
			{
				$message	= JText::_('COM_EASYDISCUSS_ROLE_UNPUBLISH_ERROR');
				$type		= 'error';
			}

		}

		DiscussHelper::setMessageQueue( $message , $type );

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=roles' );
	}

	public function orderdown()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		self::orderRole(1);
	}

	public function orderup()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		self::orderRole(-1);
	}

	public function orderRole( $direction )
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$app	= JFactory::getApplication();

		// Initialize variables
		$db		= DiscussHelper::getDBO();
		$cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );

		if (isset( $cid[0] ))
		{
			$row = DiscussHelper::getTable( 'Role' );
			$row->load( (int) $cid[0] );
			$row->move($direction);
		}

		$app->redirect( 'index.php?option=com_easydiscuss&view=roles');
		exit;
	}

	public function saveOrder()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$app	= JFactory::getApplication();

		$row = DiscussHelper::getTable( 'Role' );
		$row->rebuildOrdering();

		$message	= JText::_('COM_EASYDISCUSS_ROLES_ORDERING_SAVED');
		$type		= 'message';
		DiscussHelper::setMessageQueue( $message , $type );
		$app->redirect( 'index.php?option=com_easydiscuss&view=roles' );
		exit;
	}
}
