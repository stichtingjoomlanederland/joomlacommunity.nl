<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once(DISCUSS_ROOT . '/views/views.php');

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
		// Detect the vote type.
		$typeValue = $this->input->get('type', '', 'word') == 'down' ? DISCUSS_VOTE_DOWN : DISCUSS_VOTE_UP;
		$id = $this->input->get('id', '', 'int');

		$post = ED::post($id);

		// Check for permission
		if (!$post->canVote()) {
			$this->ajax->reject(JText::_('COM_EASYDISCUSS_ACL_VOTE_DISABLED'));
		}

		// Get user's session id.
		// $session = ED::getSession();
		$session = JFactory::getSession();
		$sessionId = $session->getId();

		$voteModel = ED::model('Votes');

		// Detect if the user has already voted on this item.
		$votedTypeValue = $voteModel->getVoteType($post->id, $this->my->id, $sessionId); 

		// Add a flag for determine the current use vote this post before or not
		$voted = false;

		if ($votedTypeValue) {

			// if it come in here mean this user already voted this post
			$voted = true;

			// Determine what vote type the user has made previously.
			if ($typeValue == $votedTypeValue) {
				$this->ajax->reject(JText::_('COM_EASYDISCUSS_YOU_ALREADY_VOTED_FOR_THIS_POST'));
				return $this->ajax->send();
			}
		}

		$vote = ED::table('Votes');

		$ownVote = false;

		if ($votedTypeValue) {
			// Try to load the user's vote and update the vote value
			$vote->loadComposite($post->id, $this->my->id, $sessionId);

			if ($vote->id) {
				$ownVote = true;
			}
		}

		$vote->value = $typeValue;
		$vote->post_id = $post->id;
		$vote->user_id = $this->my->id;
		$vote->created = ED::date()->toSql();
		$vote->session_id = $sessionId;

		$state = $vote->store();

		if (! $state) {
			$this->ajax->reject($vote->getError());
			return $this->ajax->send();
		}

		// Add stream integrations with EasySocial.
		ED::easysocial()->voteStream($post);

		// Update the post's vote count.
		$vote->sumPostVote($post, $typeValue, $ownVote);

		// If this is a reply type, we need to get the main question.
		// By default we assume that the question is the post.
		$question = $post;
		$isReply = false;

		if ($post->isReply()) {
			$question = ED::post($post->parent_id);
		}

		// Add or deduct points accordingly.
		if ($post->user_id != $this->my->id) {

			if ($post->isReply()) {
				// votes on reply
				// Vote up
				if ($typeValue == '1') {

					// Add logging for user.
					ED::history()->log('easydiscuss.vote.reply', $this->my->id, JText::sprintf('COM_EASYDISCUSS_BADGES_HISTORY_VOTE_REPLY', $question->title), $post->id);
					ED::badges()->assign('easydiscuss.vote.reply', $this->my->id);

					// Assign badge for EasySocial
					ED::easysocial()->assignBadge('vote.reply', $this->my->id, JText::sprintf('COM_EASYDISCUSS_BADGES_HISTORY_VOTE_REPLY', $question->title));

					if ($post->answered == '1') {

						ED::points()->assign('easydiscuss.vote.answer', $this->my->id, null, $voted);

						//AUP
						ED::aup()->assign(DISCUSS_POINTS_ANSWER_VOTE_UP, $this->my->id, $question->title);
					}

					ED::points()->assign('easydiscuss.vote.reply', $this->my->id, null, $voted);

					// assign new ranks.
					ED::ranks()->assignRank($this->my->id, $this->config->get('main_ranking_calc_type'));

					// @rule: Add notifications for the thread starter
					$notification = ED::table('Notifications');
					$notification->bind( array(
							'title'	=> JText::sprintf('COM_EASYDISCUSS_VOTE_UP_REPLY', $post->title),
							'cid' => $post->parent_id,
							'type' => 'vote-up-reply',
							'target' => $post->user_id,
							'author' => $this->my->id,
							'permalink'	=> 'index.php?option=com_easydiscuss&view=post&id=' . $post->parent_id
						));
					$notification->store();

				} else {
					ED::history()->log('easydiscuss.unvote.reply', $this->my->id, JText::sprintf('COM_EASYDISCUSS_BADGES_HISTORY_UNVOTE_REPLY', $question->title), $post->id);
					ED::badges()->assign('easydiscuss.unvote.reply', $this->my->id);

					if ($post->answered == '1') {

						ED::points()->assign('easydiscuss.unvote.answer', $this->my->id, null, $voted);

						//AUP
						ED::aup()->assign(DISCUSS_POINTS_ANSWER_VOTE_DOWN, $this->my->id, $question->title);

					}

					ED::points()->assign('easydiscuss.unvote.reply', $this->my->id, null, $voted);

					// assign new ranks.
					ED::ranks()->assignRank($this->my->id, $this->config->get('main_ranking_calc_type'));

					// @rule: Add notifications for the thread starter
					$notification = ED::table('Notifications');
					$notification->bind(array(
							'title'	=> JText::sprintf('COM_EASYDISCUSS_VOTE_DOWN_REPLY', $post->title),
							'cid' => $post->parent_id,
							'type' => 'vote-down-reply',
							'target' => $post->user_id,
							'author' => $this->my->id,
							'permalink'	=> 'index.php?option=com_easydiscuss&view=post&id=' . $post->parent_id
						));
					$notification->store();
				}

			} else {
				// votes on topic
				$points	= ED::points();

				// Vote up
				if ($typeValue == '1') {
					ED::points()->assign('easydiscuss.vote.question', $this->my->id, null, $voted);
					ED::badges()->assign('easydiscuss.vote.question', $this->my->id);

					//AUP
					ED::aup()->assign(DISCUSS_POINTS_QUESTION_VOTE_UP, $this->my->id, $question->title);

					// assign new ranks.
					ED::ranks()->assignRank($this->my->id, $this->config->get('main_ranking_calc_type'));

					$notification = ED::table('Notifications');
					$notification->bind(array(
							'title'	=> JText::sprintf( 'COM_EASYDISCUSS_VOTE_UP_DISCUSSION' , $post->title ),
							'cid' => $post->id,
							'type' => 'vote-up-discussion',
							'target' => $post->user_id,
							'author' => $this->my->id,
							'permalink'	=> 'index.php?option=com_easydiscuss&view=post&id=' . $post->id
						));
					$notification->store();

				} else {
					// Voted -1
					ED::points()->assign('easydiscuss.unvote.question', $this->my->id, null, $voted);
					ED::badges()->assign('easydiscuss.unvote.question', $this->my->id);

					//AUP
					ED::aup()->assign(DISCUSS_POINTS_QUESTION_VOTE_DOWN, $this->my->id, $question->title);

					// assign new ranks.
					ED::ranks()->assignRank($this->my->id, $this->config->get('main_ranking_calc_type'));

					$notification = ED::table('Notifications');
					$notification->bind(array(
							'title'	=> JText::sprintf('COM_EASYDISCUSS_VOTE_DOWN_DISCUSSION', $post->title),
							'cid' => $post->id,
							'type' => 'vote-down-discussion',
							'target' => $post->user_id,
							'author' => $this->my->id,
							'permalink'	=> 'index.php?option=com_easydiscuss&view=post&id=' . $post->id
						));
					$notification->store();
				}
			}

			$rule = ($typeValue == '1') ? 'new.voteup' : 'new.votedown';
			ED::easysocial()->notify($rule, $post, $question);
		}

		// Get the total votes.
		$totalVotes = $voteModel->getTotalVotes($post->id);

		$this->ajax->resolve($totalVotes);

		return $this->ajax->send();
	}

	/**
	 * Displays a list of voters on the site.
	 *
	 * @since	3.0
	 * @access	public	 
	 */
	public function showVoters()
	{
		$id = $this->input->get('id', 0, 'int');

		// Allow users to see who voted on the discussion
		// If main_allowguestview_whovoted is lock
		if (!$this->config->get('main_allowguestview_whovoted') && !$this->my->id) {
			$this->ajax->reject(JText::_('COM_EASYDISCUSS_NOT_ALLOWED_HERE'));
			return $this->ajax->send();
		}

		$voteModel = ED::model('Votes');
		$voters = $voteModel->getVoters($id);
		$guests = 0;
		$users = array();

		if ($voters) {
			$ids = array();

			foreach ($voters as $voter) {
				if (!$voter->user_id) {
					$guests += 1;
				} else {
					$ids[] = $voter->user_id;
				}
			}

			if ($ids) {
				// preload users
				ED::user($ids);

				foreach ($ids as $id) {
					$users[] = ED::user($id);
				}
			}
		}

		$theme = ED::themes();
		$theme->set('users', $users);
		$theme->set('guests', $guests);
		$contents = $theme->output('site/dialogs/voters');

		return $this->ajax->resolve($contents);
	}


	/**
	 * Ajax call for sum votes
	 *
	 * @since	3.0
	 * @access	public	 
	 */
	public function ajaxSumVote($postId = null)
	{
		$voteModel = ED::model('votes');
		$total = $voteModel->sumPostVotes($postId);

		return $this->ajax->send();
	}

	/**
	 * Vote undo
	 *
	 * @since	4.0.6
	 * @access	public
	 */
	public function undo()
	{
		$id = $this->input->get('id', '', 'int');

		$post = ED::post($id);

		// Get user's session id.
		$session = JFactory::getSession();
		$sessionId = $session->getId();

		// Let the votes library handles the undo.
		$lib = ED::vote();
		$state = $lib->undoVotes($post, $sessionId);

		if (!$state) {
			$this->ajax->reject(JText::_('COM_EASYDISCUSS_UNDO_FAILED_MSG'));
		}

		$totalVotes = $lib->getTotalVotes($post->id);

		$this->ajax->resolve($totalVotes, JText::_('COM_EASYDISCUSS_VOTE_UNDO_SUCCESS_MSG'));
		return $this->ajax->send();
	}	
}
