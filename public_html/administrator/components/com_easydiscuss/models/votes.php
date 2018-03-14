<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once dirname( __FILE__ ) . '/model.php';

class EasyDiscussModelVotes extends EasyDiscussAdminModel
{
	/**
	 * Check if a user vote exists in the system.
	 *
	 * @since	4.0
	 */
	public function hasVoted($postId, $userId = null, $sessionId = null)
	{
		$db = $this->db;
		$query = 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__discuss_votes');

		if ($userId) {
			$query	.= ' WHERE ' . $db->nameQuote('user_id') . '=' . $db->Quote($userId);
			$query	.= ' AND ' . $db->nameQuote('post_id') . '=' . $db->Quote($postId);
		} else {
			$query	.= ' WHERE ' . $db->nameQuote('post_id') . '=' . $db->Quote($postId);
			$query	.= ' AND ' . $db->nameQuote('session_id') . '=' . $db->Quote($sessionId);
		}

		$db->setQuery($query);

		$voted = $db->loadResult() ? true : false;

		return $voted;
	}

	/**
	 * Gets the vote type.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getVoteType($postId, $userId = null, $sessionId = null)
	{
		$db = $this->db;
		$query = 'SELECT ' . $db->nameQuote('value') . ' FROM ' . $db->nameQuote('#__discuss_votes');

		if ($userId) {
			$query 	.= ' WHERE ' . $db->nameQuote('user_id') . '=' . $db->Quote($userId);
		} else {
			$query 	.= ' WHERE ' . $db->nameQuote('session_id') . '=' . $db->Quote($sessionId);
		}

		$query 	.= ' AND ' . $db->nameQuote('post_id') . '=' . $db->Quote($postId);
		$db->setQuery($query);
		$result	= $db->loadResult();

		return $result;
	}

	/**
	 * Check whether the current vote is it modifying
	 * 
	 * @since	4.0.6
	 * @access	public
	 */
	public function voteModifying($postId, $userId = null, $sessionId = null)
	{
		$db = $this->db;
	
		// Determine this vote is it modifying
		$query  = 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__discuss_votes');
		$query .= ' WHERE ' . $db->nameQuote('session_id') . '=' . $db->Quote($sessionId);
		$query .= ' AND ' . $db->nameQuote('user_id') . '=' . $db->Quote($userId);
		$query .= ' AND ' . $db->nameQuote('post_id') . '=' . $db->Quote($postId);

		$db->setQuery($query);
		$result = $db->loadResult() ? true : false;

		return $result;
	}

	/**
	 * Undo the user current voting
	 * 
	 * @since	4.0.6
	 * @access	public
	 */
	public function undoVote($post, $userId = null, $sessionId = null)
	{
		$db = $this->db;
		$votes = ED::table('votes');

		// Retrieveing the current votes based on session.
		$votes->loadComposite($post->id, $userId, $sessionId);

		$value = 0;
		// If the previous action is vote up, then we'll need to revert that.
		if ($votes->value == 1) {
			$value = DISCUSS_VOTE_DOWN;
		} else {
			$value = DISCUSS_VOTE_UP;
		}

		$query = array();
		if ($post->isQuestion()) {
			$query[] = 'UPDATE ' . $db->nameQuote('#__discuss_posts') . ' AS a';
			$query[] = 'INNER JOIN ' . $db->nameQuote('#__discuss_thread') . ' AS b';
			$query[] = 'ON a.`id` = b.`post_id`';

			$query[] = 'SET a.`sum_totalvote` = a.`sum_totalvote` + ' . $db->Quote($value) . ',';
			$query[] = 'b.`sum_totalvote` = b.`sum_totalvote` + ' . $db->Quote($value);

			// Now we need to update the total votes count.
			$query[] = ', a.`vote` = a.`vote` - 1';
			$query[] = ', b.`vote` = b.`vote` - 1';

			if ($votes->value == DISCUSS_VOTE_DOWN) {
				$query[] = ', a.`num_negvote` = a.`num_negvote` - 1';
				$query[] = ', b.`num_negvote` = b.`num_negvote` - 1';
			}
		} else {
			$query[] = 'UPDATE ' . $db->nameQuote('#__discuss_posts') . ' AS a';
			$query[] = 'SET a.`sum_totalvote` = a.`sum_totalvote` + ' . $db->Quote($value);

			// Now we'll need to update the total votes count.
			$query[] = ', a.`vote` = a.`vote` - 1';

			if ($votes->value == DISCUSS_VOTE_DOWN) {
				$query[] = ', a.`num_negvote` = a.`num_negvote` - 1';
			}
		}

		$query[] = "WHERE a.`id` = " . $db->Quote($post->id);
		
		$query = implode(' ', $query);
		$db->setQuery($query);
		$state = $db->execute();

		// Now we can delete the current vote record 
		$state = $votes->delete();

		return $state;
	}


	/**
	 * Get's the total number of votes made for a specific post.
	 *
	 * @since	4.0
	 *
	 */
	public function getTotalVotes($postId)
	{
		$db = $this->db;

		$query = 'SELECT SUM(' . $db->nameQuote('value') . ') AS ' . $db->nameQuote('total');
		$query .= ' FROM ' . $db->nameQuote('#__discuss_votes');
		$query .= ' WHERE ' . $db->nameQuote('post_id') . '=' . $db->Quote($postId);

		$db->setQuery($query);

		$total = $db->loadResult();

		if (is_null($total)) {
			$total = 0;
		}
		
		return $total;
	}

	/**
	 * Gets a list of voters for a particular post.
	 *
	 * @since	4.0
	 */
	public function getVoters($id)
	{
		$db = $this->db;
		$query 	= 'SELECT * '
				. 'FROM ' . $db->nameQuote('#__discuss_votes') . ' '
				. 'WHERE ' . $db->nameQuote('post_id') . '=' . $db->Quote($id);
		$db->setQuery($query);

		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Resets all the votes for this particular discussion / reply.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function resetVotes($id)
	{
		$db = $this->db;

		$query = 'DELETE FROM ' . $db->nameQuote('#__discuss_votes') . ' '
			   . 'WHERE ' . $db->nameQuote('post_id') . '=' . $db->Quote($id);

		$db->setQuery($query);
		$db->Query();

	}
}
