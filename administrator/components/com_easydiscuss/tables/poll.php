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

class DiscussPoll extends JTable
{
	public $id				= null;
	public $post_id			= null;
	public $value			= null;
	public $count			= null;
	public $multiple_polls	= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(& $db )
	{
		parent::__construct( '#__discuss_polls' , 'id' , $db );
	}

	/**
	 * Retrieves a list of voters for this poll item.
	 *
	 * @access	public
	 *
	 * @return	Array	An array of DiscussProfile objects.
	 */
	public function getVoters( $limit = null )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT `user_id` FROM ' . $db->nameQuote( '#__discuss_polls_users' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'poll_id' ) . '=' . $db->Quote( $this->id );


		if( !is_null( $limit ) )
		{
			$query	.= 'LIMIT 0,' . $limit;
		}

		$db->setQuery( $query );
		$result	= $db->loadResultArray();

		if( !$result )
		{
			return $result;
		}

		$users	= array();

		foreach( $result as $res )
		{
			$profile	= DiscussHelper::getTable( 'Profile' );
			$profile->load( $res );

			$users[]	= $profile;
		}

		return $users;
	}

	public function getPercentage( $total = null )
	{
		static $totalVotes 	= null;

		$db 	= DiscussHelper::getDBO();

		// Get all poll items
		if( is_null( $totalVotes ) )
		{
			$query 	= 'SELECT COUNT(b.' . $db->nameQuote( 'id' ) . ') FROM ' . $db->nameQuote( '#__discuss_polls' ) . ' AS a '
					. 'INNER JOIN ' . $db->nameQuote( '#__discuss_polls_users' ) . ' AS b '
					. 'ON a.' . $db->nameQuote( 'id' ) . ' = b.' . $db->nameQuote( 'poll_id' ) . ' '
					. 'WHERE a.' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $this->post_id );
			$db->setQuery( $query );
			$totalVotes 	= $db->loadResult();
		}

		if( !$totalVotes )
		{
			return 0;
		}

		$percentage 	= ( $this->count / $totalVotes ) * 100;
		return round( $percentage );
	}

	/**
	 * Recalculates all votes for the particular vote items.
	 */
	public function updateCount()
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_polls_users' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'poll_id' ) . '=' . $db->Quote( $this->id );
		$db->setQuery( $query );

		$this->count	= $db->loadResult();
		$this->store();
	}

	public function removeExistingVote( $userId , $postId, $sessionId )
	{
		$db		= DiscussHelper::getDBO();

		if( $userId == 0 )
		{
			$where = 'WHERE ' . $db->nameQuote( 'session_id' ) . '=' . $db->Quote( $sessionId ) . ' ';
		}
		else
		{
			$where = 'WHERE ' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId ) . ' ';
		}

		$query	= 'DELETE FROM ' . $db->nameQuote( '#__discuss_polls_users' ) . ' '
				. $where
				. 'AND ' . $db->nameQuote( 'poll_id' ) . 'IN('
				. 'SELECT ' . $db->nameQuote( 'id' ) . ' FROM ' . $db->nameQuote( '#__discuss_polls' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $postId ) . ' '
				. ')';
		$db->setQuery( $query );
		$db->Query();
	}

	public function istheSamePoll( $userId, $pollId, $sessionId )
	{
		$db		= DiscussHelper::getDBO();

		if( $userId == 0 )
		{
			$where = ' WHERE ' . $db->nameQuote( 'session_id' ) . '=' . $db->Quote( $sessionId );
		}
		else
		{
			$where = ' WHERE ' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId );
		}

		$query	= 'SELECT ' . $db->nameQuote( 'id' ) . ' FROM ' . $db->nameQuote( '#__discuss_polls_users' )
				. $where
				. ' AND ' . $db->nameQuote( 'poll_id' ) . ' = ' . $db->Quote( $pollId );
		$db->setQuery( $query );
		$samePoll = $db->loadResult();

		$voted = ( empty($samePoll) ) ? false : true ;

		return $voted;
	}

	public function removeSamePoll( $userId, $pollId, $sessionId )
	{
		$db		= DiscussHelper::getDBO();

		if( $userId == 0 )
		{
			$where = ' WHERE ' . $db->nameQuote( 'session_id' ) . '=' . $db->Quote( $sessionId );
		}
		else
		{
			$where = ' WHERE ' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId );
		}

		$query	= 'DELETE FROM ' . $db->nameQuote( '#__discuss_polls_users' )
				. $where
				. ' AND ' . $db->nameQuote( 'poll_id' ) . ' = ' . $db->Quote( $pollId );
		$db->setQuery( $query );
		$db->Query();
	}
	/**
	 * Tests if the user has already voted for this discussion's poll before.
	 *
	 * @access	public
	 * @param	int $userId		The user id to check for.
	 * @return	boolean			True if voted, false otherwise.
	 */
	public function hasVotedPoll( $userId, $sessionId )
	{
		$db		= DiscussHelper::getDBO();

		if( $userId == 0 )
		{
			$where = 'WHERE ' . $db->nameQuote( 'session_id' ) . '=' . $db->Quote( $sessionId ) . ' ';
		}
		else
		{
			$where = 'WHERE ' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId ) . ' ';
		}

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_polls_users' ) . ' '
			. $where
			. 'AND ' . $db->nameQuote( 'poll_id' ) . ' IN('
			. 'SELECT `id` FROM ' . $db->nameQuote( '#__discuss_polls' ) . ' WHERE ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $this->post_id )
			. ')';
		$db->setQuery( $query );
		$voted	= $db->loadResult();

		return $voted > 0;
	}

	/**
	 * Tests if the user has already voted for this particular polls in the post
	 *
	 * @access	public
	 * @param	int $userId		The user id to check for.
	 * @return	boolean	True if voted, false otherwise.
	 */
	public function voted( $userId )
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_polls_users' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId ) . ' '
				. 'AND ' . $db->nameQuote( 'poll_id' ) . '=' . $db->Quote( $this->id );
		$db->setQuery( $query );
		$total	= $db->loadResult();

		return $total > 0;
	}

	public function loadByValue( $value , $postId )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( $this->_tbl ) . ' '
				. 'WHERE ' . $db->nameQuote( 'value' ) . '=' . $db->Quote( $value ) . ' '
				. 'AND ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $postId );
		$db->setQuery( $query );
		$result	= $db->loadObject();

		if( !$result )
		{
			return;
		}

		return parent::bind( $result );
	}

	public function delete($pk = null)
	{
		$state	= parent::delete( $pk );

		$db		= DiscussHelper::getDBO();

		$query	= 'DELETE FROM ' . $db->nameQuote( '#__discuss_polls_users' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'poll_id' ) . '=' . $db->Quote( $this->id );
		$db->setQuery( $query );
		$db->Query();

		return $state;
	}
}
