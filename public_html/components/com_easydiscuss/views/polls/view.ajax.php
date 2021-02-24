<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussViewPolls extends EasyDiscussView
{
	/**
	 * Process a poll voting
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function vote()
	{
		// Get the choice id
		$id	= $this->input->get('id', 0, 'int');

		// Get the post id
		$postId = $this->input->get('postId', 0, 'int');

		// Load the poll choice
		$choice = ED::pollchoice($id);

		// Ensure that the poll choice is valid
		if (!$id || !$choice->id) {
			throw ED::exception('COM_EASYDISCUSS_NOT_ALLOWED', ED_MSG_ERROR);
		}

		// Get the poll
		$poll = $choice->getPoll();

		// Ensure that the user can really vote
		if (!$poll->canVote()) {
			throw ED::exception('COM_EASYDISCUSS_NOT_ALLOWED', ED_MSG_ERROR);
		}

		// Vote for the choice now.
		$choice->vote();

		// Get a list of poll answers for this question.
		$result = array();
		$choices = $poll->getChoices(true);

		foreach ($choices as $choice) {
			$result[] = $choice->toData();
		}

		$model = ED::model('Polls');
		$totalVotes = $model->getTotalVotes($postId);
		$totalVotes = JText::sprintf('COM_EASYDISCUSS_POLLS_TOTAL_VOTES', $totalVotes);

		return $this->ajax->resolve($result, $totalVotes);
	}

	/**
	 * Retrieves a list of voters for a choice
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getVoters()
	{
		$id = $this->input->get('id', 0, 'int');

		$choice = ED::pollchoice($id);

		if (!$id || !$choice->id) {
			throw ED::exception('COM_EASYDISCUSS_NOT_ALLOWED', ED_MSG_ERROR);
		}

		$voters = $choice->getVoters();

		$theme = ED::themes();
		$theme->set('voters', $voters);
		$contents = $theme->output('site/polls/voters');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Allows caller to lock polls
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function lock()
	{
		$postId = $this->input->get('id', 0, 'int');

		if (!$postId) {
			throw ED::exception('COM_EASYDISCUSS_NOT_ALLOWED', ED_MSG_ERROR);
		}


		$post = ED::post($postId);

		// Ensure that the user is really allowed to lock the polls
		if (!$post->canLockPolls()) {
			throw ED::exception('COM_EASYDISCUSS_NOT_ALLOWED', ED_MSG_ERROR);
		}

		// Lock the polls
		$post->lockPolls();

		return $this->ajax->resolve();
	}

	/**
	 * Allows caller to unlock polls
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function unlock()
	{
		$postId = $this->input->get('id', 0, 'int');

		if (!$postId) {
			throw ED::exception('COM_EASYDISCUSS_NOT_ALLOWED', ED_MSG_ERROR);
		}

		$post = ED::post($postId);

		// Ensure that the user is really allowed to lock the polls
		if (!$post->canLockPolls()) {
			throw ED::exception('COM_EASYDISCUSS_NOT_ALLOWED', ED_MSG_ERROR);
		}

		// Lock the polls
		$post->unlockPolls();

		return $this->ajax->resolve();
	}
}
