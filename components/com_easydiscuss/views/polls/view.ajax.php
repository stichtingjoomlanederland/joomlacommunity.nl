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

require_once( DISCUSS_ROOT . '/views.php' );

class EasyDiscussViewPolls extends EasyDiscussView
{
	/**
	 * Ajax method to process voting on a poll.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function vote()
	{
		$ajax		= DiscussHelper::getHelper( 'Ajax' );
		$config		= DiscussHelper::getConfig();

		$id			= JRequest::getInt( 'id' );
		$poll		= DiscussHelper::getTable( 'Poll' );
		$poll->load( $id );

		$my			= JFactory::getUser();
		$profile	= DiscussHelper::getTable( 'Profile' );
		$profile->load( $my->id );

		$post		= DiscussHelper::getTable( 'Post' );
		$post->load( $poll->post_id );

		if( ( $post->parent_id && !$config->get( 'main_polls_replies' ) ) ||
			( !$post->parent_id && !$config->get( 'main_polls' ) ) ||
			( !$poll->id ) ||
			( !$config->get( 'main_polls_guests' ) && $my->id <= 0 )
		)
		{
			echo JText::_( 'COM_EASYDISCUSS_NOT_ALLOWED' );
			return $ajax->send();;
		}

		// Get user's session id.
		$session 	= JFactory::getSession();
		$sessionId 	= $session->getId();

		// @task: Test if user has voted before. If they have already voted on this item before, we need to update the counts.
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT ' . $db->nameQuote( 'multiple' )
				. ' FROM '  . $db->nameQuote( '#__discuss_polls_question' )
				. ' WHERE ' . $db->nameQuote( 'post_id' ) . ' = ' . $db->quote( $poll->get( 'post_id' ) );
		$db->setQuery( $query );

		$isMultiple 	= $db->loadResult();

		// Initial vote count on poll item.
		$count 			= $poll->count;

		if( !$isMultiple )
		{
			// Legacy or multiple vote is not activated
			if( $poll->hasVotedPoll( $my->id, $sessionId ) )
			{
				// Remove existing vote
				$poll->removeExistingVote( $my->id , $poll->get( 'post_id' ), $sessionId );
			}
			else
			{
				// If this is a new poll, we need to update the counter.
				$count	+= 1;
			}

			// @task: Add a new vote now
			$pollUser = DiscussHelper::getTable( 'PollUser' );
			$pollUser->set( 'poll_id'		, $id );
			$pollUser->set( 'user_id'		, $my->id );
			$pollUser->set( 'session_id'	, $sessionId );
			$pollUser->store();
		}
		else
		{
			// If user has voted on this item before, we need to unvote the particular poll item.
			if( $poll->istheSamePoll( $my->id, $poll->id, $sessionId ) )
			{
				// The user unvoted on the poll item. We need to update the counter.
				$count 	-= 1;

				$poll->removeSamePoll( $my->id, $poll->id, $sessionId );
			}
			else
			{
				// If this is a new poll, we need to update the counter.
				$count	+= 1;

				// Add a new vote for the user.
				$pollUser = DiscussHelper::getTable( 'PollUser' );

				$pollUser->set( 'poll_id'		, $id );
				$pollUser->set( 'user_id'		, $my->id );
				$pollUser->set( 'session_id'	, $sessionId );

				$pollUser->store();
			}
		}
		$post = DiscussHelper::getTable( 'Post' );
		$post->load( $poll->post_id );
		$post->updatePollsCount();

		// Update the poll count
		$poll->count 	= $count;

		// We need to update all the percentages.
		$percentages 	= array();

		// Get a list of poll answers for this question.
		$pollItems 	= $post->getPolls();

		$result		= array();

		foreach( $pollItems as $pollItem )
		{
			$obj 	= new stdClass();

			$obj->id 			= $pollItem->id;
			$obj->percentage 	= $pollItem->getPercentage();
			$obj->count 		= $pollItem->count;
			$obj->votes         = DiscussHelper::getHelper( 'String' )->getNoun('COM_EASYDISCUSS_VOTE_COUNT', $pollItem->count, true);

			// Regenerate voters html
			$output 	= '';
			$voters		= $pollItem->getVoters();

			foreach( $voters as $voter )
			{
				$theme 	= new DiscussThemes();
				$theme->set( 'voter' , $profile );

				$output .= $theme->fetch( 'poll.voters.php' );
			}

			$obj->voters 		= $output;

			$result[]	= $obj;
		}

		$ajax->resolve( $result );

		return $ajax->send();
	}

	/**
	 * Retrieve a list of voters from the site in a dialog.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The unique id for the poll answer.
	 * @return
	 */
	public function getVoters( $pollId )
	{
		$ajax	= new Disjax();

		$poll	= DiscussHelper::getTable( 'Poll' );
		$poll->load( $pollId );

		$voters		= $poll->getVoters();

		$template	= new DiscussThemes();
		$template->set( 'voters' , $voters );

		$option				= new stdClass();
		$option->title		= JText::_( 'COM_EASYDISCUSS_USERS_WHO_VOTED_THIS_POLL' );
		$option->content	= $template->fetch( 'ajax.poll.voters.php' , array( 'dialog' => true ) );

		$buttons			= array();
		$button				= new stdClass();
		$button->title		= JText::_( 'COM_EASYDISCUSS_OK' );
		$button->action		= 'disjax.closedlg();';
		$button->className	= 'btn-primary';
		$buttons[]			= $button;
		$option->buttons	= $buttons;

		$ajax->dialog( $option );

		$ajax->send();
	}

	public function lockPolls()
	{
		$ajax		= DiscussHelper::getHelper( 'Ajax' );
		$id = JRequest::getInt( 'postId' );

		if( !empty($id) )
		{
			$post		= DiscussHelper::getTable( 'Post' );
			$post->load( $id );
			$isQuestion = $post->isQuestion();

			$polls = $post->getPolls();

			foreach($polls as $poll)
			{
				$pollsId[] = $poll->id;
			}

			$state = $post->lockPolls();
		}
		return $ajax->resolve( '<i class="icon-lock"></i>' . JText::_( 'COM_EASYDISCUSS_POLL_IS_LOCKED' ), $isQuestion, $id, $pollsId );
	}

	public function unlockPolls()
	{
		$ajax		= DiscussHelper::getHelper( 'Ajax' );
		$id = JRequest::getInt( 'postId' );

		if( !empty($id) )
		{
			$post		= DiscussHelper::getTable( 'Post' );
			$post->load( $id );
			$isQuestion = $post->isQuestion();

			$polls = $post->getPolls();

			foreach($polls as $poll)
			{
				$pollsId[] = $poll->id;
			}

			$state = $post->unlockPolls();
		}
		return $ajax->resolve( $isQuestion, $id, $pollsId );
	}
}
