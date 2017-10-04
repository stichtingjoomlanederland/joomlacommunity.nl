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

ED::import('admin:/tables/table');

class DiscussVotes extends EasyDiscussTable
{
	public $id			= null;
	public $user_id		= null;
	public $post_id		= null;
	public $created		= null;
	public $session_id	= null;
	public $value		= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(& $db )
	{
		parent::__construct( '#__discuss_votes' , 'id' , $db );
	}

	/**
	 * Try to load a user's vote.
	 */
	public function loadComposite($postId, $userId = null, $sessionId = null)
	{
		$db		= ED::db();

		$query = 'SELECT * FROM ' . $db->nameQuote($this->_tbl);
		$query .= ' WHERE ' . $db->nameQuote('post_id') . '=' . $db->Quote($postId);

		if ($userId) {
			$query .= ' AND ' . $db->nameQuote('user_id') . '=' . $db->Quote( $userId );
		} else {
			$query .= ' AND ' . $db->nameQuote('session_id') . '=' . $db->Quote( $sessionId );
		}

		$db->setQuery($query);

		$result = $db->loadObject();

		return parent::bind($result);
	}

	/**
	 * Method to update posts total neg vote count.
	 */
	public function addNegVoteCount($postId)
	{
		$db = ED::db();
		$val = 1;

		if (empty($postId)) {
			return false;
		}

		$query	= 'UPDATE `#__discuss_posts` SET `num_negvote` = `num_negvote` + ' . $db->Quote($val);
		$query	.= ' WHERE `id` = ' . $db->Quote($postId);
		$db->setQuery($query);
		$db->query();

		return true;
	}

	public function sumPostVote($post, $val, $own = false)
	{
		$db	= ED::db();

		if (!$post->id) {
			return false;
		}

		$query = "";

		if ($own) {
			// this means user is attempt to resetting the vote.
			if ($val < 0) {
				$val--;
			} else {
				$val++;
			}
		}

		if ($post->isQuestion()) {
			// if this is a question, we will need to update thread table as well.

			$query = "update " . $db->nameQuote('#__discuss_posts') . " as a inner join " . $db->nameQuote('#__discuss_thread') . " as b on a.`id` = b.`post_id`";
			$query .= "	set a.`sum_totalvote` = a.`sum_totalvote` + " . $db->Quote($val) . ",";
			$query .= "	    b.`sum_totalvote` = b.`sum_totalvote` + " . $db->Quote($val);
			if ($val < 0) {
				$query  .= " , a.`num_negvote` = a.`num_negvote` + 1";
				$query  .= " , b.`num_negvote` = b.`num_negvote` + 1";
			} else if ($own) {
				$query  .= " , a.`num_negvote` = a.`num_negvote` - 1";
				$query  .= " , b.`num_negvote` = b.`num_negvote` - 1";
			}

			// now increase the total votes count ( irregardless if the vote is up vote or down vote.)
			// we'll only increase this once.
			if (!$own) {
				$query .= ", a.`vote` = a.`vote` + 1";
				$query .= ", b.`vote` = b.`vote` + 1";
			}
		} else {

			$query  = "UPDATE " . $db->nameQuote('#__discuss_posts') . " as a SET a.`sum_totalvote` = a.`sum_totalvote` + " . $db->Quote($val);
			if ($val < 0) {
				$query .= " ,a.`num_negvote` = a.`num_negvote` + 1";
			} else if ($own) {
				$query .= " ,a.`num_negvote` = a.`num_negvote` - 1";
			}

			// now increase the total votes count ( irregardless if the vote is up vote or down vote.)
			$query .= ", a.`vote` = a.`vote` + 1";
		}

		$query  .= " WHERE a.`id` = " . $db->Quote($post->id);

		// echo $query;exit;

		$db->setQuery($query);
		$state = $db->query();

		return $state;
	}
}
