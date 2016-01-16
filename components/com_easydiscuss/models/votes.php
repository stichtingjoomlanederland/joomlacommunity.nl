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

require_once dirname( __FILE__ ) . '/model.php';

class EasyDiscussModelVotes extends EasyDiscussModel
{
	/**
	 * Check if a user vote exists in the system.
	 *
	 * @since	3.0
	 * @param	int		The unique post id.
	 * @param	int 	The user's unique id.
	 * @param	string	The user's ip address.
	 * @param	string	The unique session id.
	 * @return	boolean	True if user has already voted.
	 */
	public function hasVoted( $postId , $userId = null , $sessionId = null )
	{
		$db 		= DiscussHelper::getDBO();
		$query 		= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_votes' );

		if( $userId )
		{	
			$query	.= ' WHERE ' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId );
			$query	.= ' AND ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $postId );
		}
		else
		{
			$query	.= ' WHERE ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $postId );
			$query	.= ' AND ' . $db->nameQuote( 'session_id' ) . '=' . $db->Quote( $sessionId );	
		}

		$db->setQuery($query);

		$voted 	= $db->loadResult() ? true : false;

		return $voted;
	}

	/**
	 * Gets the vote type.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The unique post id.
	 * @param	int		The user's unique id.
	 * @param	string	The unique session id.
	 */
	function getVoteType( $postId , $userId = null , $sessionId = null )
	{
		$db 		= DiscussHelper::getDBO();
		$query 		= 'SELECT ' . $db->nameQuote( 'value' ) . ' FROM ' . $db->nameQuote( '#__discuss_votes' );

		if( $userId )
		{
			$query 	.= ' WHERE ' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId );
		}
		else
		{
			$query 	.= ' WHERE ' . $db->nameQuote( 'session_id' ) . '=' . $db->Quote( $sessionId );
		}

		$query 	.= ' AND ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $postId );
		$db->setQuery($query);
		$result	= $db->loadResult();

		return $result;
	}

	/**
	 * Get's the total number of votes made for a specific post.
	 *
	 * @since	3.0
	 * @param	int		The unique post id.
	 * @return	int		The total number of votes.
	 *
	 */
	public function getTotalVotes( $postId )
	{
		$db 	= DiscussHelper::getDBO();

		$query 	= 'SELECT SUM(' . $db->nameQuote( 'value' ) . ') AS ' . $db->nameQuote( 'total' );
		$query 	.= ' FROM ' . $db->nameQuote( '#__discuss_votes' );
		$query 	.= ' WHERE ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $postId );

		$db->setQuery( $query );

		$total 	= $db->loadResult();

		return $total;
	}

	/**
	 * Gets a list of voters for a particular post.
	 *
	 * @since	3.0
	 * @param	int 	The unique post id.
	 * @return	Array	An array of voter objects.
	 */
	public function getVoters( $id )
	{
		$db 	= DiscussHelper::getDBO();
		$query 	= 'SELECT * '
				. 'FROM ' . $db->nameQuote( '#__discuss_votes' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $id );
		$db->setQuery( $query );

		$result 	= $db->loadObjectList();

		return $result;
	}
}
