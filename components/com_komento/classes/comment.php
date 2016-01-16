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

/**
 * All the logics, event trigger, integration regarding to comment's CRUD operation,
 * handles all application interaction with comment.
 */
class KomentoComment extends JObject
{
	public function __construct( $id )
	{
		if( $id )
		{
			$this->load( $id );
		}
	}

	public function load( $id )
	{
		$commentTable = Komento::getTable( 'Comments' );

		if (!$commentTable->load($id))
		{
			$this->setError( 'Invalid Comment ID' );
			return false;
		}

		if( !is_object( $commentTable->params ) )
		{
			$commentTable->params = json_decode( $commentTable->params );
		}

		if( empty( $commentTable->params ) )
		{
			$commentTable->params = new stdClass();
		}

		// bind with comment table data
		$this->setProperties($commentTable->getProperties());

		return true;
	}

	// Bind an object or array to this comment object
	public function bind( $data )
	{
		if( isset( $data['commentid'] ) )
		{
			$this->id = $data['commentid'];
		}

		$this->id		= $data['id'] ? (int) $data[$id] : null;

		$filter			= JFilterInput::getInstance();
		$this->comment	= $filter->clean($data['comment']);

		if( isset( $data['name'] ) )
		{
			$this->name		= $filter->clean($data['name']);
		}

		if( isset( $data['title'] ) )
		{
			$this->title	= $filter->clean($data['title']);
		}

		if( isset( $data['email'] ) )
		{
			$this->email	= $filter->clean($data['email']);
		}

		if( isset( $data['url'] ) )
		{
			$this->url		= $filter->clean($data['url']);
		}

		if( isset( $data['params'] ) )
		{
			if( !is_object( $data['params'] ) )
			{
				$this->params = json_decode( $data['params'] );
			}

			if( empty( $this->params ) )
			{
				$this->params = new stdClass();
			}
		}
	}

	public function save()
	{
		$config		= Komento::getConfig();

		// Create the comment table object
		$commentsModel	= Komento::getModel( 'comments' );
		$commentTable = Komento::getTable( 'Comments' );
		$commentTable->bind($this->getProperties());

		// empty name, email defaults to user
		// or if guest, then default empty name to 'Guest'
		$profile	= Komento::getProfile();
		$now		= Komento::getDate()->toMySQL();

		// @rule: Determine if this record is new or not.
		$isNew  	= ( empty( $this->id ) ) ? true : false;

		// Trigger onBeforeSaveComment( $this->component, $this->cid, &$this )
		$triggerResult = Komento::trigger( 'onBeforeSaveComment', array( 'component' => $this->component, 'cid' => $this->cid, 'comment' => &$this ) );

		if( $triggerResult === false )
		{
			$this->setError( 'Trigger onBeforeSaveComment false' );
			return false;
		}

		if( !$commentTable->store() )
		{
			$this->setError( 'Comment save failed' );
			return false;
		}

		$this->id = $commentTable->id;

		// Trigger onAfterSaveComment( $this->component, $this->cid, &$this )
		Komento::trigger( 'onAfterSaveComment', array( 'component' => $this->component, 'cid' => $this->cid, 'comment' => &$this ) );

		// Add activity
		$action = $commentTable->parent_id ? 'reply' : 'comment';
		$activity = Komento::getHelper( 'activity' )->process( $action, $commentTable->id );

		// Send notifications
		if( $config->get( 'notification_enable' ) )
		{
			if( $commentTable->published == 1 && $isNew && ( ( $action == 'comment' && $config->get( 'notification_event_new_comment' ) ) || ( $action == 'reply' && $config->get( 'notification_event_new_reply' ) ) ) )
			{
				Komento::getHelper( 'Notification' )->push( $action, 'subscribers,author,usergroups', array( 'commentId' => $this->id ) );
			}

			if( $commentTable->published == 2 && $config->get( 'notification_event_new_pending' ) )
			{
				Komento::getHelper( 'Notification' )->push( 'pending', 'author,usergroups', array( 'commentId' => $this->id ) );
			}
		}

		return true;
	}

	public function store()
	{
		$commentTable = Komento::getTable( 'Comments' );
		$commentTable->bind($this->getProperties());

		$state = $commentTable->store();

		return $state;
	}

	public function delete()
	{
		// Create the comment table object
		$commentTable = Komento::getTable( 'Comments' );
		$commentTable->bind($this->getProperties());

		// Trigger onBeforeDeleteComment( $this->component, $this->cid, &$this )
		$triggerResult = Komento::trigger( 'onBeforeDeleteComment', array( 'component' => $this->component, 'cid' => $this->cid, 'comment' => &$this ) );

		if( $triggerResult === false )
		{
			$this->setError( 'Trigger onBeforeDeleteComment false' );
			return false;
		}

		if( !$commentTable->delete() )
		{
			$this->setError( 'Comment delete failed' );
			return false;
		}

		// Trigger onAfterDeleteComment( $this->component, $this->cid, &$this )
		Komento::trigger( 'onAfterDeleteComment', array( 'component' => $this->component, 'cid' => $this->cid, 'comment' => &$this ) );

		// Always move child up regardless of deleting child or not
		$commentModel = Komento::getModel( 'Comments' );
		$commentModel->moveChildsUp( $this->id );

		// Clear activities
		$activityModel	= Komento::getModel( 'Activity' );
		$activityModel->delete( $this->id );

		// Clear actions
		$actionsModel = Komento::getModel( 'Actions' );
		$actionsModel->removeAction('all', $this->id, 'all');

		// Process activities
		$activity = Komento::getHelper( 'activity' )->process( 'remove', $this->id );

		// Delete attachments
		Komento::getHelper( 'file' )->clearAttachments( $this->id );

		return true;
	}

	public function publish( $type = '1' )
	{
		// set new = false
		$new = false;

		// Create the comment table object
		$commentTable = Komento::getTable( 'Comments' );
		$commentTable->bind($this->getProperties());

		// get date
		$now = Komento::getDate()->toMySQL();

		// check original status == 2
		if( $commentTable->published == 2 )
		{
			$new = true;
		}

		$commentTable->published = $type;

		if( $type == '1' )
		{
			$commentTable->publish_up = $now;
		}
		else
		{
			$commentTable->publish_down = $now;
		}

		// Trigger onBeforePublishComment( $this->component, $this->cid, &$this )
		// Trigger onBeforeUnpublishComment( $this->component, $this->cid, &$this )
		$triggerResult = true;
		if( $type == '1' )
		{
			$triggerResult = Komento::trigger( 'onBeforePublishComment', array( 'component' => $this->component, 'cid' => $this->cid, 'comment' => &$this ) );
		}
		else
		{
			$triggerResult = Komento::trigger( 'onBeforeUnpublishComment', array( 'component' => $this->component, 'cid' => $this->cid, 'comment' => &$this ) );
		}

		if( $triggerResult === false )
		{
			$this->setError( 'Trigger onBeforePublishComment/onBeforeUnpublishComment false' );
			return false;
		}

		if( !$commentTable->store() )
		{
			$this->setError( 'Comment publish/unpublish failed' );
			return false;
		}

		// bind with comment table data after successfully saving comment table.
		$this->setProperties($commentTable->getProperties());

		// Trigger onAfterPublishComment( $this->component, $this->cid, &$this )
		// Trigger onAfterUnpublishComment( $this->component, $this->cid, &$this )
		if( $type == '1' )
		{
			Komento::trigger( 'onAfterPublishComment', array( 'component' => $this->component, 'cid' => $this->cid, 'comment' => &$this ) );
		}
		else
		{
			Komento::trigger( 'onAfterUnpublishComment', array( 'component' => $this->component, 'cid' => $this->cid, 'comment' => &$this ) );
		}

		if( $new )
		{
			$config = Komento::getConfig( $commentTable->component );

			$action = $commentTable->parent_id ? 'reply' : 'comment';

			// send email
			if( $config->get( 'notification_enable' ) )
			{
				if( ( $action == 'comment' && $config->get( 'notification_event_new_comment' ) ) || ( $action == 'reply' && $config->get( 'notification_event_new_reply' ) ) )
				{
					Komento::getHelper( 'Notification' )->push( $action, 'subscribers,author,usergroups', array( 'commentId' => $commentTable->id ) );
				}
			}

			// process activities
			$activity = Komento::getHelper( 'activity' )->process( $action, $commentTable->id );

			// Manually get the attachment of this comment and process the "upload" activity
			$attachments = $commentTable->getAttachments();
			$totalAttachments = count( $attachments );
			for( $i = 0; $i < $totalAttachments; $i++ )
			{
				Komento::getHelper( 'activity' )->process( 'upload', $commentTable->id );
			}
		}

		return true;
	}

	public function mark( $type = '0' )
	{
		$commentTable = Komento::getTable( 'Comments' );
		$commentTable->bind($this->getProperties());
		$userId		= JFactory::getUser()->id;

		// remove all reported flags
		$actionsModel = Komento::getModel( 'Actions' );
		$actionsModel->removeAction('spam', $commentTable->id, 'all');
		$actionsModel->removeAction('offensive', $commentTable->id, 'all');
		$actionsModel->removeAction('offtopic', $commentTable->id, 'all');

		$commentTable->flag = $type;
		$commentTable->flag_by = $userId;

		if( !$commentTable->store() )
		{
			$this->setError( 'Comment mark failed' );
			return false;
		}

		return true;
	}

	public function getAttachments()
	{
		$model = Komento::getModel( 'uploads' );
		$attachments = $model->getAttachments( $this->id );

		return $attachments;
	}

	public function getPermalink()
	{
		if( !isset( $this->permalink ) )
		{
			$application = Komento::loadApplication( $this->component )->load( $this->cid );
			$this->permalink = $application->getContentPermalink() . '#kmt-' . $this->id;
		}

		return $this->permalink;
	}

	public function getParent( $process = 0, $admin = 0 )
	{
		if( empty( $this->parent_id ) )
		{
			return false;
		}

		$parent = Komento::getComment( $this->parent_id, $process, $admin );

		return $parent;
	}

	public function action( $action, $type, $userId )
	{
		$config			= Komento::getConfig();

		$actionFunc = $action === 'add' ? 'addAction' : 'removeAction';

		$actionsModel = Komento::getModel( 'actions' );

		$result = $actionsModel->$actionFunc( $type, $this->id, $userId );

		if( $result === false )
		{
			return false;
		}

		// add acitvities
		if( $action == 'add' && $type == 'likes' )
		{
			Komento::getHelper( 'activity' )->process( 'like', $this->id );
		}

		if( $action == 'remove' && $type == 'likes' )
		{
			Komento::getHelper( 'activity' )->process( 'unlike', $this->id );
		}

		if( $action == 'add' && $type == 'report' )
		{
			Komento::getHelper( 'activity' )->process( 'report', $this->id );

			if( $config->get( 'notification_event_reported_comment' ) )
			{
				Komento::getHelper( 'notification' )->push( 'report', 'author,usergroups', array( 'commentId' => $this->id, 'actionId' => $result ) );
			}
		}

		if( $action == 'remove' && $type == 'report' )
		{
			Komento::getHelper( 'activity' )->process( 'unreport', $this->id );
		}

		return true;
	}

	public function setParam( $key, $value )
	{
		if( !is_object( $this->params ) )
		{
			$this->params = json_decode( $this->params );
		}

		if( empty( $this->params ) )
		{
			$this->params = new stdClass();
		}

		$this->params->$key = $value;
	}
}
