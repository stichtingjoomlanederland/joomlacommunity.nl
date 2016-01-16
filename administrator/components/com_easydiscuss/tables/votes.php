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

class DiscussVotes extends JTable
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
	public function loadComposite( $postId , $userId = null , $sessionId = null )
	{
		$db		= DiscussHelper::getDBO();

		$query 	= 'SELECT * FROM ' . $db->nameQuote( $this->_tbl );
		$query 	.= ' WHERE ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $postId );

		if( $userId )
		{
			$query	.= ' AND ' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId );
		}
		else
		{
			$query	.= ' AND ' . $db->nameQuote( 'session_id' ) . '=' . $db->Quote( $sessionId );
		}

		$db->setQuery( $query );

		$result 	= $db->loadObject();

		return parent::bind( $result );
	}

	/**
	 * Method to update posts total neg vote count.
	 */
	public function addNegVoteCount($postId)
	{
		$db		= DiscussHelper::getDBO();
		$val	= 1;

		if(empty($postId))
			return false;

		$query	= 'UPDATE `#__discuss_posts` SET `num_negvote` = `num_negvote` + ' . $db->Quote($val);
		$query	.= ' WHERE `id` = ' . $db->Quote($postId);
		$db->setQuery($query);
		$db->query();

		return true;
	}

	public function sumPostVote($postId, $val)
	{
		$db		= DiscussHelper::getDBO();

		if(empty($postId))
			return false;

		$query  = 'UPDATE `#__discuss_posts` SET `sum_totalvote` = `sum_totalvote` + ' . $db->Quote($val);
		if($val < 0)
		{
			$query  .= ' ,`num_negvote` = `num_negvote` + 1';
		}
		$query  .= ' WHERE `id` = ' . $db->Quote($postId);

		$db->setQuery($query);
		$db->query();

		return true;
	}
}
