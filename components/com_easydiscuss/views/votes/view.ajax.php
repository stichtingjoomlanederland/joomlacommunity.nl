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

class EasyDiscussViewVotes extends EasyDiscussView
{
	/**
	 * Processes ajax method to add a vote.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function add()
	{
		$config = DiscussHelper::getConfig();
		$my 	= JFactory::getUser();
		$ajax 	= DiscussHelper::getHelper( 'Ajax' );

		// Detect the vote type.
		$typeValue 	= JRequest::getWord( 'type' ) == 'down' ? DISCUSS_VOTE_DOWN : DISCUSS_VOTE_UP;

		$id 	= JRequest::getInt( 'id' );
		$post 	= DiscussHelper::getTable( 'Post' );
		$post->load( $id );

		// Since someone tries to hack the system, we just ignore this.
		// We do not need to display friendly messages to users that try
		// to hack the system.
		if(
			( $post->isQuestion() && !$config->get( 'main_allowquestionvote') ) ||
			( $post->isReply() && !$config->get( 'main_allowvote') ) ||
			( !$post->id )
		)
		{
			$ajax->reject();

			return $ajax->send();
		}

		// Get user's session id.
		$session 	= JFactory::getSession();
		$sessionId 	= $session->getId();

		// Detect if the user is trying to vote on his own post.
		if( !$config->get( 'main_allowselfvote') && ($my->id == $post->user_id) )
		{
			$ajax->reject( JText::_( 'COM_EASYDISCUSS_SELF_VOTE_DENIED' ) );
			return $ajax->send();
		}

		$voteModel 			= DiscussHelper::getModel( 'Votes' );

		// Detect if the user has already voted on this item.
		$votedType 			= $voteModel->getVoteType( $post->id , $my->id , $sessionId );

		if( $votedType )
		{
			// Determine what vote type the user has made previously.
			if( $typeValue == $votedType )
			{
				$ajax->reject( JText::_( 'COM_EASYDISCUSS_YOU_ALRREADY_VOTED_FOR_THIS_POST' ) );
				return $ajax->send();
			}
		}

		$vote 				= DiscussHelper::getTable( 'Votes' );

		if( $votedType )
		{
			// Try to load the user's vote and update the vote value
			$vote->loadComposite( $post->id , $my->id , $sessionId );

			$vote->value 	= $typeValue;
		}
		else
		{
			$vote->value 		= $typeValue;
		}

		$vote->post_id 		= $post->id;
		$vote->user_id 		= $my->id;
		$vote->created 		= DiscussHelper::getDate()->toMySQL();
		$vote->session_id	= $sessionId;

		if( !$vote->store() )
		{
			$ajax->reject( $vote->getError() );
			return $ajax->send();
		}

		// Add stream integrations with EasySocial.
		DiscussHelper::getHelper( 'EasySocial' )->voteStream( $post );

		// Update the post's vote count.
		$vote->sumPostVote( $post->id , $typeValue );

		// If this is a reply type, we need to get the main question.
		// By default we assume that the question is the post.
		$question	= $post;
		$isReply	= false;

		if( $post->isReply() )
		{
			$question 	= DiscussHelper::getTable( 'Post' );
			$question->load( $post->parent_id );
		}

		// Add or deduct points accordingly.
		if( $post->user_id != $my->id )
		{

			if( $post->isReply() )
			{
				// votes on reply
				// Vote up
				if( $typeValue == '1' )
				{
					// Add logging for user.
					DiscussHelper::getHelper( 'History' )->log( 'easydiscuss.vote.reply' , $my->id , JText::sprintf( 'COM_EASYDISCUSS_BADGES_HISTORY_VOTE_REPLY' , $question->title ), $post->id );
					DiscussHelper::getHelper( 'Badges' )->assign( 'easydiscuss.vote.reply' , $my->id );

					// Assign badge for EasySocial
					DiscussHelper::getHelper( 'EasySocial' )->assignBadge( 'vote.reply' , $my->id , JText::sprintf( 'COM_EASYDISCUSS_BADGES_HISTORY_VOTE_REPLY' , $question->title ) );

					if( $post->answered == '1' )
					{
						DiscussHelper::getHelper( 'Points' )->assign( 'easydiscuss.vote.answer' , $my->id );

						//AUP
						DiscussHelper::getHelper( 'Aup' )->assign( DISCUSS_POINTS_ANSWER_VOTE_UP , $my->id , $question->title );

					}
					else
					{
						DiscussHelper::getHelper( 'Points' )->assign( 'easydiscuss.vote.reply' , $my->id );
					}

					// @rule: Add notifications for the thread starter
					$notification	= DiscussHelper::getTable( 'Notifications' );
					$notification->bind( array(
							'title'	=> JText::sprintf( 'COM_EASYDISCUSS_VOTE_UP_REPLY' , $post->title ),
							'cid'	=> $post->parent_id,
							'type'	=> 'vote-up-reply',
							'target'	=> $post->get( 'user_id' ),
							'author'	=> $my->id,
							'permalink'	=> 'index.php?option=com_easydiscuss&view=post&id=' . $post->parent_id
						) );
					$notification->store();

				}
				else
				{
					DiscussHelper::getHelper( 'History' )->log( 'easydiscuss.unvote.reply' , $my->id , JText::sprintf( 'COM_EASYDISCUSS_BADGES_HISTORY_UNVOTE_REPLY' , $question->title ), $post->id );
					DiscussHelper::getHelper( 'Badges' )->assign( 'easydiscuss.unvote.reply' , $my->id );

					if( $post->answered == '1' )
					{
						DiscussHelper::getHelper( 'Points' )->assign( 'easydiscuss.unvote.answer' , $my->id );

						//AUP
						DiscussHelper::getHelper( 'Aup' )->assign( DISCUSS_POINTS_ANSWER_VOTE_DOWN , $my->id , $question->title );

					}
					else
					{
						DiscussHelper::getHelper( 'Points' )->assign( 'easydiscuss.unvote.reply' , $my->id );
					}

					// @rule: Add notifications for the thread starter
					$notification	= DiscussHelper::getTable( 'Notifications' );
					$notification->bind( array(
							'title'	=> JText::sprintf( 'COM_EASYDISCUSS_VOTE_DOWN_REPLY' , $post->title ),
							'cid'	=> $post->parent_id,
							'type'	=> 'vote-down-reply',
							'target'	=> $post->get( 'user_id' ),
							'author'	=> $my->id,
							'permalink'	=> 'index.php?option=com_easydiscuss&view=post&id=' . $post->parent_id
						) );
					$notification->store();
				}
			}
			else
			{
				// votes on topic
				$points	= DiscussHelper::getHelper( 'Points' );
				// Vote up
				if( $typeValue == '1' )
				{
					DiscussHelper::getHelper( 'Points' )->assign( 'easydiscuss.vote.question' , $my->id );
					DiscussHelper::getHelper( 'Badges' )->assign( 'easydiscuss.vote.question' , $my->id );

					//AUP
					DiscussHelper::getHelper( 'Aup' )->assign( DISCUSS_POINTS_QUESTION_VOTE_UP , $my->id , $question->title );

					$notification	= DiscussHelper::getTable( 'Notifications' );
					$notification->bind( array(
							'title'	=> JText::sprintf( 'COM_EASYDISCUSS_VOTE_UP_DISCUSSION' , $post->title ),
							'cid'	=> $post->id,
							'type'	=> 'vote-up-discussion',
							'target'	=> $post->get( 'user_id' ),
							'author'	=> $my->id,
							'permalink'	=> 'index.php?option=com_easydiscuss&view=post&id=' . $post->id
						) );
					$notification->store();
				}
				else
				{
					// Voted -1
					DiscussHelper::getHelper( 'Points' )->assign( 'easydiscuss.unvote.question' , $my->id );
					DiscussHelper::getHelper( 'Badges' )->assign( 'easydiscuss.unvote.question' , $my->id );

					//AUP
					DiscussHelper::getHelper( 'Aup' )->assign( DISCUSS_POINTS_QUESTION_VOTE_DOWN , $my->id , $question->title );

					$notification	= DiscussHelper::getTable( 'Notifications' );
					$notification->bind( array(
							'title'	=> JText::sprintf( 'COM_EASYDISCUSS_VOTE_DOWN_DISCUSSION' , $post->title ),
							'cid'	=> $post->id,
							'type'	=> 'vote-down-discussion',
							'target'	=> $post->get( 'user_id' ),
							'author'	=> $my->id,
							'permalink'	=> 'index.php?option=com_easydiscuss&view=post&id=' . $post->id
						) );
					$notification->store();
				}
			}

		}

		// Get the total votes.
		$totalVotes 	= $voteModel->getTotalVotes( $post->id );

		$ajax->resolve( $totalVotes );

		return $ajax->send();
	}

	/**
	 * Displays a list of voters on the site.
	 *
	 * @since	3.0
	 */
	public function showVoters( $id )
	{
		// Allow users to see who voted on the discussion
		$ajax		= new Disjax();
		$config 	= DiscussHelper::getConfig();
		$my	 		= JFactory::getUser();

		// If main_allowguestview_whovoted is lock
		if( !$config->get( 'main_allowguestview_whovoted' ) && !$my->id )
		{
			$ajax->reject( JText::_( 'COM_EASYDISCUSS_NOT_ALLOWED_HERE' ) );
			return $ajax->send();
		}

		$voteModel 	= DiscussHelper::getModel( 'Votes' );
		$voters 	= $voteModel->getVoters( $id );
		$guests 	= 0;
		$users 		= array();

		if( $voters )
		{
			foreach( $voters as $voter )
			{
				if( !$voter->user_id )
				{
					$guests += 1;
				}
				else
				{
					$profile 	= DiscussHelper::getTable( 'Profile' );
					$users[] 	= $profile->load( $voter->user_id );
				}
			}
		}


		$options 			= new stdClass();
		$options->title		= JText::_( 'COM_EASYDISCUSS_VIEWING_VOTERS_TITLE' );

		$theme				= new DiscussThemes();
		$theme->set( 'users' , $users );
		$theme->set( 'guests', $guests );
		$content 	= $theme->fetch( 'voters.php' , array( 'dialog' => true ) );

		$options->content 	= $content;
		$buttons 	= array();

		$button 			= new stdClass();
		$button->title 		= JText::_( 'COM_EASYDISCUSS_BUTTON_CLOSE' );
		$button->action 	= 'disjax.closedlg();';
		$buttons[]			= $button;

		$options->buttons 	= $buttons;
		$ajax->dialog( $options );

		return $ajax->send();
	}


	/**
	 * Ajax Call
	 * Sum all votes
	 */
	public function ajaxSumVote( $postId = null )
	{
		$djax	= new Disjax();

		// load model
		$voteModel = $this->getModel('votes');
		$total = $voteModel->sumPostVotes($postId);

		$djax->send();
		return;
	}

}
