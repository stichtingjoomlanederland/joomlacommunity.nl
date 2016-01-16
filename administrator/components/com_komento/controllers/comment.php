<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'controller.php' );

class KomentoControllerComment extends KomentoController
{
	function __construct()
	{
		parent::__construct();

		$this->registerTask( 'add' , 'edit' );
	}

	function cancel()
	{
		$this->setRedirect( 'index.php?option=com_komento&view=comments' );

		return;
	}

	function edit()
	{
		JRequest::setVar( 'view', 'comment' );
		JRequest::setVar( 'commentid' , JRequest::getVar( 'commentid' , '' , 'REQUEST' ) );

		parent::display();
	}

	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$comments	= JRequest::getVar( 'cid' , array(0) , 'POST' );
		$message	= '';
		$type		= 'message';

		if( empty( $comments ) )
		{
			$message	= JText::_( 'COM_KOMENTO_COMMENTS_COMMENT_INVALID_ID' );
			$type		= 'error';
		}
		else
		{
			$model		= Komento::getModel( 'comments' );

			if( $model->remove( $comments ) )
			{
				$message	= JText::_('COM_KOMENTO_COMMENTS_COMMENT_REMOVED');
			}
			else
			{
				$message	= JText::_('COM_KOMENTO_COMMENTS_COMMENT_REMOVE_ERROR');
				$type		= 'error';
			}
		}

		/*
		$parentId = JRequest::getInt('parentid', 0);
		$parentId = $this->getTable('comments')->load($parentId)->parent_id;
		$this->setRedirect( 'index.php?option=com_komento&view=comments&parentid=' . $parentId , $message , $type );
		*/

		$this->setRedirect( 'index.php?option=com_komento&view=comments' , $message , $type );
	}

	function apply()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$commentId = JRequest::getInt( 'commentid' );

		$mainframe	= JFactory::getApplication();

		$message	= '';
		$type		= 'message';

		if( JRequest::getMethod() == 'POST' )
		{
			$result = $this->doSave();
			if( $result === true )
			{
				$message = JText::_( 'COM_KOMENTO_COMMENTS_SAVED' );
			}
			else
			{
				$message = $result;
				$type = 'error';
			}
		}
		else
		{
			$message	= JText::_('Invalid request method. This form needs to be submitted through a "POST" request.');
			$type		= 'error';
		}

		$mainframe->redirect( 'index.php?option=com_komento&view=comment&task=edit&c=comment&commentid=' . $commentId , $message , $type );
	}

	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$mainframe	= JFactory::getApplication();

		$message	= '';
		$type		= 'message';

		if( JRequest::getMethod() == 'POST' )
		{
			$result = $this->doSave();
			if( $result === true )
			{
				$message = JText::_( 'COM_KOMENTO_COMMENTS_SAVED' );
			}
			else
			{
				$message = $result;
				$type = 'error';
			}
		}
		else
		{
			$message	= JText::_('Invalid request method. This form needs to be submitted through a "POST" request.');
			$type		= 'error';
		}

		$mainframe->redirect( 'index.php?option=com_komento&view=comments' , $message , $type );
	}

	private function doSave()
	{
		$post				= JRequest::get( 'post' );
		$now				= Komento::getDate()->toMySQL();
		$user				= JFactory::getUser();
		$commentId			= JRequest::getVar( 'commentid' , '' );

		// gettable instead of getobj to avoid sending mails
		$comment			= Komento::getTable( 'comments' );
		$comment->load($commentId);

		/*
			check if postcomment = comment->comment (to update modified_by and modified)
			check if postflag = comment->flag (to update flag_by and flag)
			check if postflag = 0
			check if published different

		*/

		// check if modified

		if($post['comment'] != $comment->comment)
		{
			$comment->modified = $now;
			$comment->modified_by = $user->id;
		}

		$comment->bind( $post );

		// check publish change
		// publish_up
		// publish_down

		if($post['published'] != $comment->published)
		{
			if($post['published'] == 0)
			{
				$comment->publish_down = $now;
			}
			else
			{
				$comment->publish_up = $now;
			}

			$comment->published = $post['published'];
		}

		if( !$comment->store() )
		{
			JError::raiseError( 500, $comment->getError() );
			return $comment->getError();
		}
		else
		{
			return true;
		}
	}

	function publish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$task = $this->getTask();

		if( $task === 'unpublish' )
		{
			$this->unpublish();
			return;
		}

		$comments	= JRequest::getVar( 'cid' , array(0) , 'POST' );

		$message	= '';
		$type		= 'message';

		if( count( $comments ) <= 0 )
		{
			$message	= JText::_( 'COM_KOMENTO_COMMENTS_COMMENT_INVALID_ID' );
			$type		= 'error';
		}
		else
		{
			$model		= Komento::getModel( 'comments' );

			if( $model->publish( $comments , 1 ) )
			{
				$message	= JText::_('COM_KOMENTO_COMMENTS_COMMENT_PUBLISHED');
			}
			else
			{
				$message	= JText::_( 'COM_KOMENTO_COMMENTS_COMMENT_PUBLISH_ERROR' );
				$type		= 'error';
			}

		}

		$parentId = JRequest::getVar('parentid', 0);
		$this->setRedirect( 'index.php?option=com_komento&view=comments&parentid=' . $parentId , $message , $type );
	}

	function unpublish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$comments	= JRequest::getVar( 'cid' , array(0) , 'POST' );

		$message	= '';
		$type		= 'message';

		if( count( $comments ) <= 0 )
		{
			$message	= JText::_( 'COM_KOMENTO_COMMENTS_COMMENT_INVALID_ID' );
			$type		= 'error';
		}
		else
		{
			$model		= Komento::getModel( 'comments' );

			if( $model->unpublish( $comments ) )
			{
				$message	= JText::_( 'COM_KOMENTO_COMMENTS_COMMENT_UNPUBLISHED' );
			}
			else
			{
				$message	= JText::_( 'COM_KOMENTO_COMMENTS_COMMENT_UNPUBLISH_ERROR' );
				$type		= 'error';
			}

		}

		$parentId = JRequest::getVar('parentid', 0);
		$this->setRedirect( 'index.php?option=com_komento&view=comments&parentid=' . $parentId , $message , $type );
	}

	function stick()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$comments	= JRequest::getVar( 'cid' , array(0) , 'POST' );

		$message	= '';
		$type		= 'message';

		if( count( $comments ) <= 0 )
		{
			$message	= JText::_( 'COM_KOMENTO_COMMENTS_COMMENT_INVALID_ID' );
			$type		= 'error';
		}
		else
		{
			$model		= Komento::getModel( 'comments' );

			if( $model->stick( $comments ) )
			{
				$message	= JText::_( 'COM_KOMENTO_COMMENTS_COMMENT_STICKED' );
			}
			else
			{
				$message	= JText::_( 'COM_KOMENTO_COMMENTS_COMMENT_STICKED_ERROR' );
				$type		= 'error';
			}
		}

		$this->setRedirect( 'index.php?option=com_komento&view=comments' , $message , $type );
	}

	function unstick()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$comments	= JRequest::getVar( 'cid' , array(0) , 'POST' );

		if( count( $comments ) <= 0 )
		{
			$message	= JText::_( 'COM_KOMENTO_COMMENTS_COMMENT_INVALID_ID' );
			$type		= 'error';
		}
		else
		{
			$model		= Komento::getModel( 'comments' );

			if( $model->unstick( $comments ) )
			{
				$message	= JText::_( 'COM_KOMENTO_COMMENTS_COMMENT_UNSTICKED' );
			}
			else
			{
				$message	= JText::_( 'COM_KOMENTO_COMMENTS_COMMENT_UNSTICKED_ERROR' );
				$type		= 'error';
			}
		}

		$this->setRedirect( 'index.php?option=com_komento&view=comments' , $message , $type );
	}

	function saveColumns()
	{
		$columns = array(
			'comment',
			'published',
			'sticked',
			'link',
			'edit',
			'component',
			'article',
			'date',
			'author',
			'email',
			'homepage',
			'ip',
			'latitude',
			'longitude',
			'address',
			'id'
		);

		$message = JText::_( 'COM_KOMENTO_COMMENTS_COLUMNS_SAVED' );
		$type = 'message';

		$data = JRequest::get( 'post' );

		foreach( $data as $key => $value )
		{
			if( !in_array( str_replace( 'column_', '', $key ), $columns ) )
			{
				unset($data[$key]);
			}
		}

		$config = Komento::getTable( 'configs' );
		$config->load( 'com_komento_comments_columns' );

		$config->component = 'com_komento_comments_columns';

		$params = $config ? $config->params : '';
		$registry = Komento::getRegistry( $params, 'json' );
		$registry->extend( $data );

		$config->params	= $registry->toString( 'json' );

		if( !$config->store() )
		{
			$message = $config->getError();
			$type = 'error';
		}

		$this->setRedirect( 'index.php?option=com_komento&view=comments' , $message , $type );
	}

	function clean()
	{
		// access with c=comment&task=clean
		// clean up comments
		// eg set invalid parent_id = 0
		// algorithm to clean lft rgt
	}
}
