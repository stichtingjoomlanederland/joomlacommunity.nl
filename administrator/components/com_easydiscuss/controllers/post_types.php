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

require_once DISCUSS_HELPERS . '/date.php';
require_once DISCUSS_HELPERS . '/input.php';
require_once DISCUSS_HELPERS . '/filter.php';

class EasyDiscussControllerPost_types extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		// Need to explicitly define this in Joomla 3.0
		$this->registerTask( 'unpublish'	, 'unpublish' );
		$this->registerTask( 'publish'		, 'unpublish' );
		$this->registerTask( 'new'			, 'edit' );
		$this->registerTask( 'cancel'		, 'cancel' );
	}

	public function edit()
	{
		$mainframe	= JFactory::getApplication();

		// It goes to the view.html.php function form()
		$mainframe->redirect( 'index.php?option=com_easydiscuss&view=post_types&layout=form' );
	}

	public function cancel()
	{
		$this->setRedirect( 'index.php?option=com_easydiscuss&view=post_types' );

		return;
	}

	public function remove()
	{
		JRequest::checkToken('request') or jexit( 'Invalid Token' );
		$ids		= JRequest::getVar( 'cid' );

		foreach( $ids as $id )
		{
			$postTypes = DiscussHelper::getTable( 'Post_types' );
			$postTypes->load( $id );
			$postTypes->delete();
		}

		$app	= JFactory::getApplication();

		DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_POST_TYPES_DELETED' ) , DISCUSS_QUEUE_SUCCESS );
		$app->redirect( 'index.php?option=com_easydiscuss&view=post_types' );
	}

	public function unpublish()
	{
		JRequest::checkToken('request') or jexit( 'Invalid Token' );

		$app	= JFactory::getApplication();
		$postTypes	= DiscussHelper::getTable( 'Post_types' );
		$ids	= JRequest::getVar( 'cid' );
		$state	= JRequest::getVar( 'task' ) == 'publish' ? 1 : 0;

		foreach( $ids as $id )
		{
			$id	= (int) $id;

			$postTypes->load( $id );
			$postTypes->set( 'published' , $state );
			$postTypes->store();
		}

		$message	= $state ? JText::_( 'COM_EASYDISCUSS_POST_TYPES_PUBLISHED' ) : JText::_( 'COM_EASYDISCUSS_POST_TYPES_UNPUBLISHED' );

		DiscussHelper::setMessageQueue( $message , DISCUSS_QUEUE_SUCCESS );

		$app->redirect( 'index.php?option=com_easydiscuss&view=post_types' );
	}

	public function apply()
	{
		// This is the save button, not save and close
		$this->save();
	}

	public function save()
	{
		JRequest::checkToken('request') or jexit( 'Invalid Token' );

		$app	= JFactory::getApplication();
		$postTypes	= DiscussHelper::getTable( 'Post_types' );
		$doc 	= JFactory::getDocument();

		$data	= JRequest::get( 'POST' );
		$postTypes->load( $data['id'] );

		$oldTitle	= $postTypes->title;

		$postTypes->bind( $data );

		if( !$postTypes->created )
		{
			$postTypes->created = DiscussHelper::getDate()->toMySQL();
		}

		if ($postTypes->title != $oldTitle || $oldTitle == '')
		{
			$postTypes->alias	= DiscussHelper::getAlias($postTypes->title);

			//since we using the alias to join with discuss_posts.post_type, we need to update the value there as well.
			$postTypes->updateTopicPostType( $oldTitle );
		}

		$postTypes->published = 1;

		// Get the current task
		$task 		= $this->getTask();

		$postTypes->store();

		if( $task == 'apply' )
		{
			$redirect = 'index.php?option=com_easydiscuss&view=post_types&layout=form&id=' . $postTypes->id;
		}
		else
		{
			$redirect = 'index.php?option=com_easydiscuss&view=post_types';
		}

		$message	= !empty( $postTypes->id ) ? JText::_( 'COM_EASYDISCUSS_POST_TYPES_UPDATED' ) : JText::_( 'COM_EASYDISCUSS_POST_TYPES_CREATED' );

		DiscussHelper::setMessageQueue( $message , DISCUSS_QUEUE_SUCCESS );
		$app->redirect( $redirect );
	}
}
