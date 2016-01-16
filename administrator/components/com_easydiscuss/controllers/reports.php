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

require_once DISCUSS_HELPERS . '/date.php';
require_once DISCUSS_HELPERS . '/input.php';

class EasyDiscussControllerReports extends EasyDiscussController
{
	function publish()
	{
		$post	= JRequest::getVar( 'cid' , array(0) , 'POST' );

		$message	= '';
		$type		= 'success';

		if( count( $post ) <= 0 )
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			$type		= 'error';
		}
		else
		{
			$model		= $this->getModel( 'Reports' );

			if( $model->publish( $post , 1 ) )
			{
				$message	= JText::_('COM_EASYDISCUSS_POST_PUBLISHED');
			}
			else
			{
				$message	= JText::_('COM_EASYDISCUSS_ERROR_PUBLISHING_POST');
				$type		= 'error';
			}

		}

		DiscussHelper::setMessageQueue( $message , $type );

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=reports' );
	}

	function unpublish()
	{
		$post	= JRequest::getVar( 'cid' , array(0) , 'POST' );

		$message	= '';
		$type		= 'success';

		if( count( $post ) <= 0 )
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			$type		= 'error';
		}
		else
		{
			$model		= $this->getModel( 'Reports' );

			if( $model->publish( $post , 0 ) )
			{
				$message	= JText::_('COM_EASYDISCUSS_POST_UNPUBLISHED');
			}
			else
			{
				$message	= JText::_('COM_EASYDISCUSS_ERROR_UNPUBLISHING_POST');
				$type		= 'error';
			}

		}

		DiscussHelper::setMessageQueue( $message , $type );

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=reports' );
	}

	function togglePublish()
	{
		$postId		= JRequest::getInt( 'post_id' , '0' , 'POST' );
		$postVal	= JRequest::getInt( 'post_val' , '0' , 'POST' );

		$model		= $this->getModel( 'Reports' );
		$message	= '';
		$type		= 'success';

		if(empty($postId))
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			$type		= 'error';
		}

		if($postVal && !empty($postId))
		{
			if( $model->publish( array($postId) , 1 ) )
			{
				$message	= JText::_('COM_EASYDISCUSS_POST_PUBLISHED');
			}
			else
			{
				$message	= JText::_('COM_EASYDISCUSS_ERROR_PUBLISHING_POST');
				$type		= 'error';
			}
		}
		else
		{
			if( $model->publish( array($postId) , 0 ) )
			{
				$message	= JText::_('COM_EASYDISCUSS_POST_UNPUBLISHED');
			}
			else
			{
				$message	= JText::_('COM_EASYDISCUSS_ERROR_UNPUBLISHING_POST');
				$type		= 'error';
			}
		}
		DiscussHelper::setMessageQueue( $message , $type );

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=reports' );
	}

	function removeReports()
	{
		$postId		= JRequest::getInt( 'post_id' , '0' , 'POST' );

		$model		= $this->getModel( 'Reports' );
		$message	= '';
		$type		= 'success';

		if(empty($postId))
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			$type		= 'error';
		}

		$model->removeReports($postId);


		$message	= JText::_('COM_EASYDISCUSS_REPORT_ABUSE_REMOVED');
		DiscussHelper::setMessageQueue( $message , $type );
		$this->setRedirect( 'index.php?option=com_easydiscuss&view=reports' );
	}

	function edit()
	{
		JRequest::setVar( 'view', 'post' );
		JRequest::setVar( 'id' , JRequest::getVar( 'id' , '' , 'REQUEST' ) );
		JRequest::setVar( 'source' , 'reports' );

		parent::display();
	}

	function remove()
	{
		$post	= JRequest::getVar( 'cid' , array(0) , 'POST' );

		$message	= '';
		$type		= 'success';

		if( count( $post ) <= 0 )
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			$type		= 'error';
		}
		else
		{
			$model		= $this->getModel( 'Reports' );

			for($i = 0; $i < count($post); $i++)
			{
				$pid = $post[$i];
				$model->removePostReports($pid);
			}
		}

		$message	= JText::_('COM_EASYDISCUSS_POST_DELETED');
		DiscussHelper::setMessageQueue( $message , $type );
		$this->setRedirect( 'index.php?option=com_easydiscuss&view=reports' );
	}

	function deletePost()
	{
		$id 		= JRequest::getInt( 'post_id' , 0 );
		$model		= $this->getModel( 'Reports' );
		$message	= '';
		$type		= '';

		// Check for errors.
		if(empty($id))
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			$type		= 'error';
			DiscussHelper::setMessageQueue( $message , $type );

			return $this->setRedirect( 'index.php?option=com_easydiscuss&view=reports' );
		}

		$post 	= DiscussHelper::getTable( 'Post' );
		$post->load( $id );

		$isReplies 	= $post->parent_id != 0;

		// Try to delete the post.
		$post->delete();

		// Remove the report item.
		$model->removePostReports($postId);

		$message	= JText::_('COM_EASYDISCUSS_POST_DELETED');
		
		if($repliesDeleted)
		{
			$message	= JText::_('COM_EASYDISCUSS_POST_DELETED_WITH_REPLIES');
		}

		DiscussHelper::setMessageQueue( $message , $type );

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=reports' );
	}
}
