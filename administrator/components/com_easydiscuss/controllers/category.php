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

jimport('joomla.application.component.controller');

class EasyDiscussControllerCategory extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		$this->registerTask( 'add' , 'edit' );

		$this->registerTask('publish', 'publish');
		$this->registerTask('unpublish', 'unpublish');

		$this->registerTask( 'apply' , 'save' );
		$this->registerTask( 'savepublishnew' , 'save' );
	}

	public function orderdown()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		EasyDiscussControllerCategory::orderCategory(1);
	}

	public function orderup()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		EasyDiscussControllerCategory::orderCategory(-1);
	}

	public function orderCategory( $direction )
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$mainframe  = JFactory::getApplication();

		// Initialize variables
		$db		= DiscussHelper::getDBO();
		$cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );

		if (isset( $cid[0] ))
		{
			$row = JTable::getInstance('Category', 'Discuss');
			$row->load( (int) $cid[0] );
			$row->move($direction);
		}

		$mainframe->redirect( 'index.php?option=com_easydiscuss&view=categories');
		exit;
	}

	public function saveOrder()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$mainframe  = JFactory::getApplication();

		$row = JTable::getInstance('Category', 'Discuss');
		$row->rebuildOrdering();

		$message	= JText::_('COM_EASYDISCUSS_CATEGORIES_ORDERING_SAVED');

		DiscussHelper::setMessageQueue( $message , DISCUSS_QUEUE_SUCCESS );
		$mainframe->redirect( 'index.php?option=com_easydiscuss&view=categories' );
		exit;
	}

	public function removeAvatar()
	{
		// Check for request forgeries
		JRequest::checkToken( 'get' ) or jexit( 'Invalid Token' );

		$id			= JRequest::getInt( 'id' );
		$category	= DiscussHelper::getTable( 'Category' );
		$category->load( $id );

		$state		= $category->removeAvatar( true );

		DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_CATEGORY_AVATAR_REMOVED') , DISCUSS_QUEUE_SUCCESS );

		JFactory::getApplication()->redirect( 'index.php?option=com_easydiscuss&view=category&catid=' . $category->id );
	}

	public function saveOrderOri()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$mainframe  = JFactory::getApplication();
		$cid		= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$order		= JRequest::getVar( 'order', array (0), 'post', 'array' );
		$total		= count($cid);
		$conditions	= array ();
		$groupings	= array();

		JArrayHelper::toInteger($cid, array(0));
		JArrayHelper::toInteger($order, array(0));

		$row = JTable::getInstance('Category', 'Discuss');

		// Update the ordering for items in the cid array
		for ($i = 0; $i < $total; $i ++)
		{
			$row->load( (int) $cid[$i] );

			$groupings[] = $row->parent_id;

			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					JError::raiseError( 500, $db->getErrorMsg() );
					return false;
				}
				// remember to updateOrder this group
				$condition = 'id = '.(int) $row->id;
				$found = false;
				foreach ($conditions as $cond)
					if ($cond[1] == $condition) {
						$found = true;
						break;
					}
				if (!$found)
					$conditions[] = array ($row->id, $condition);
			}
		}

		// execute updateOrder for each group
		foreach ($conditions as $cond)
		{
			$row->load($cond[0]);
			$row->reorder($cond[1]);
		}

		$message	= JText::_('COM_EASYDISCUSS_CATEGORIES_ORDERING_SAVED');

		DiscussHelper::setMessageQueue( $message , DISCUSS_QUEUE_SUCCESS );

		$mainframe->redirect( 'index.php?option=com_easydiscuss&view=categories' );
		exit;
	}

	public function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$mainframe	= JFactory::getApplication();

		$task 		= $this->getTask();

		$message	= '';
		$type		= 'success';

		if( JRequest::getMethod() == 'POST' )
		{
			$post				= JRequest::get( 'post' );

			if(empty($post['title']))
			{
				DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_CATEGORIES_INVALID_CATEGORY') , DISCUSS_QUEUE_ERROR );

				$url  = 'index.php?option=com_easydiscuss&view=categories';
				$mainframe->redirect(JRoute::_($url, false));
				return;
			}

			$category			= JTable::getInstance( 'Category', 'Discuss' );
			$user				= JFactory::getUser();
			$post['created_by']	= $user->id;
			$catId				= JRequest::getVar( 'catid' , '' );

			$isNew				= (empty($catId)) ? true : false;
			$alterOrdering		= true;

			if( !empty( $catId ) )
			{
				$category->load( $catId );
				$newParentId  = $post['parent_id'];

				if( $category->parent_id != $newParentId)
				{
					$alterOrdering  = true;
				}
				else
				{
					$alterOrdering  = false;
				}

				$post['id'] = $catId;
			}

			$category->bind( $post );

			// Description might contain html codes
			$description 			= JRequest::getVar( 'description' , '' , 'post' , 'string' , JREQUEST_ALLOWRAW );
			$category->description 	= $description;

			// Bind params
			$params 			= DiscussHelper::getRegistry('');
			$params->set( 'show_description' , $post['show_description'] );
			$params->set( 'maxlength' , $post['maxlength'] );
			$params->set( 'maxlength_size' , $post['maxlength_size'] );
			$params->set( 'cat_notify_custom' , $post['cat_notify_custom'] );
			$params->set( 'cat_email_parser' , $post['cat_email_parser'] );
			$params->set( 'cat_email_parser_password' , $post['cat_email_parser_password'] );
			$params->set( 'cat_email_parser_switch' , $post['cat_email_parser_switch'] );

			$category->params 	= $params->toString();

			if (!$category->store( $alterOrdering ))
			{
				JError::raiseError(500, $category->getError() );
				exit;
			}

			//save the category acl
			$category->deleteACL();
			$category->saveACL( $post );


			$file = JRequest::getVar( 'Filedata', '', 'files', 'array' );
			if(! empty($file['name']))
			{
				$newAvatar			= DiscussHelper::uploadCategoryAvatar($category, true);
				$category->avatar	= $newAvatar;
				$category->store(); //now update the avatar.
			}

			// now we need to rerun the category ordering incase admin assign the correct category tree as a child of another category.
			$category->rebuildOrdering();

			$message	= JText::_( 'COM_EASYDISCUSS_CATEGORIES_SAVED_SUCCESS' );
		}
		else
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_REQUEST');
			$type		= 'error';
		}

		DiscussHelper::setMessageQueue( $message , $type );

		if( $task == 'savePublishNew' )
		{
			$mainframe->redirect( 'index.php?option=com_easydiscuss&view=category' );
			$mainframe->close();
		}

		if( $task == 'apply' )
		{
			$mainframe->redirect( 'index.php?option=com_easydiscuss&view=category&catid=' . $category->id );
			$mainframe->close();
		}

		$mainframe->redirect( 'index.php?option=com_easydiscuss&view=categories' );
		$mainframe->close();
	}

	public function cancel()
	{
		$this->setRedirect( 'index.php?option=com_easydiscuss&view=categories' );

		return;
	}

	public function edit()
	{
		JRequest::setVar( 'view', 'category' );
		JRequest::setVar( 'catid' , JRequest::getVar( 'catid' , '' , 'REQUEST' ) );

		parent::display();
	}

	public function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$categories	= JRequest::getVar( 'cid' , '' , 'POST' );

		$message	= '';
		$type		= 'success';

		if( empty( $categories ) )
		{
			$message	= JText::_('COM_EASYDISCUSS_CATEGORIES_INVALID_CATEGORY');
			$type		= 'error';
		}
		else
		{
			$table		= JTable::getInstance( 'Category' , 'Discuss' );
			foreach( $categories as $category )
			{
				$table->load( $category );

				if($table->getPostCount())
				{
					$message	= JText::sprintf('COM_EASYDISCUSS_CATEGORIES_DELETE_ERROR_POST_NOT_EMPTY', $table->title);
					$type		= 'error';
					$this->setRedirect( 'index.php?option=com_easydiscuss&view=categories' , $message , $type );
					return;
				}

				if($table->getChildCount())
				{
					$message	= JText::sprintf('COM_EASYDISCUSS_CATEGORIES_DELETE_ERROR_CHILD_NOT_EMPTY', $table->title);
					$type		= 'error';
					$this->setRedirect( 'index.php?option=com_easydiscuss&view=categories' , $message , $type );
					return;
				}

				if( !$table->delete() )
				{
					$message	= JText::_( 'COM_EASYDISCUSS_CATEGORIES_DELETE_ERROR' );
					$type		= 'error';
					$this->setRedirect( 'index.php?option=com_easydiscuss&view=categories' , $message , $type );
					return;
				}
			}
			$message	= JText::_('COM_EASYDISCUSS_CATEGORIES_DELETE_SUCCESS');
		}

		DiscussHelper::setMessageQueue( $message , $type );

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=categories' );
	}

	public function publish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$categories	= JRequest::getVar( 'cid' , array(0) , 'POST' );

		$message	= '';
		$type		= 'message';

		if( count( $categories ) <= 0 )
		{
			$message	= JText::_('COM_EASYDISCUSS_CATEGORIES_INVALID_CATEGORY');
			$type		= 'error';
		}
		else
		{
			$model		= $this->getModel( 'Categories' );

			if( $model->publish( $categories , 1 ) )
			{
				$message	= JText::_('COM_EASYDISCUSS_CATEGORIES_PUBLISHED_SUCCESS');
			}
			else
			{
				$message	= JText::_('COM_EASYDISCUSS_CATEGORIES_PUBLISHED_ERROR');
				$type		= 'error';
			}

		}


		DiscussHelper::setMessageQueue( $message , $type );
		$this->setRedirect( 'index.php?option=com_easydiscuss&view=categories' );
	}

	public function unpublish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$categories	= JRequest::getVar( 'cid' , array(0) , 'POST' );

		$message	= '';
		$type		= 'success';

		if( count( $categories ) <= 0 )
		{
			$message	= JText::_('COM_EASYDISCUSS_CATEGORIES_INVALID_CATEGORY');
			$type		= 'error';
		}
		else
		{
			$model		= $this->getModel( 'Categories' );

			if( $model->publish( $categories , 0 ) )
			{
				$message	= JText::_('COM_EASYDISCUSS_CATEGORIES_UNPUBLISHED_SUCCESS');
			}
			else
			{
				$message	= JText::_('COM_EASYDISCUSS_CATEGORIES_UNPUBLISHED_ERROR');
				$type		= 'error';
			}
		}


		DiscussHelper::setMessageQueue( $message , $type );
		$this->setRedirect( 'index.php?option=com_easydiscuss&view=categories' );
	}

	/*
	 * Logic to make a category as default.
	 */
	public function makeDefault()
	{
		$cid = JRequest::getVar( 'cid' );

		if( is_array( $cid ) )
		{
			$cid = (int) $cid[0];
		}

		$model = $this->getModel( 'Categories' );
		$model->updateDefault( $cid );


		DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_CATEGORY_SET_DEFAULT' ) , DISCUSS_QUEUE_SUCCESS );
		$this->setRedirect( 'index.php?option=com_easydiscuss&view=categories' );
	}

	// Workaround for Joomla 3.0 on issue
	// $this->getModel() doesn't work properly
	public function getModel($name = '', $prefix = '', $config = array())
	{
		require_once JPATH_ROOT . '/administrator/components/com_easydiscuss/models/categories.php';

		return parent::getModel('Categories', 'EasyDiscussModel');
	}
}
