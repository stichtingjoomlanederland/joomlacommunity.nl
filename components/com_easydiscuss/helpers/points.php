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

class DiscussPointsHelper
{
	public function assign( $command , $userId, $post = null)
	{

		// Assign points via EasySocial
		DiscussHelper::getHelper( 'EasySocial' )->assignPoints( $command , $userId, $post);
		
		if( !$userId )
		{
			return false;
		}

		$points		= $this->getPoints( $command );

		if( !$points )
		{
			return false;
		}

		$profile	= DiscussHelper::getTable( 'Profile' );
		$profile->load( $userId );

		foreach( $points as $point )
		{
			$profile->addPoint( $point->rule_limit );
		}

		$profile->store();

		// ranking
		// DiscussHelper::getHelper( 'ranks' )->assignRank( $userId, 'points' );

		return true;
	}


	/**
	 * Retrieve a list of points for the specific command
	 *
	 * @access	private
	 * @param	string	$command	The action string.
	 * @param	int		$userId		The actor's id.
	 *
	 * @return	Array	An array of BadgesHistory object.
	 **/
	public function getPoints( $command )
	{

		$db		= DiscussHelper::getDBO();

		$query	= 'SELECT a.* FROM ' . $db->nameQuote( '#__discuss_points' ) . ' AS a '
				. 'INNER JOIN ' . $db->nameQuote( '#__discuss_rules' ) . ' AS b '
				. 'ON b.' . $db->nameQuote( 'id' ) . '= a.' . $db->nameQuote( 'rule_id' ) . ' '
				. 'WHERE b.' . $db->nameQuote( 'command' ) . '=' . $db->Quote( $command ) . ' '
				. 'AND a.' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 );

		$db->setQuery( $query );

		$points	= $db->loadObjectList();

		return $points;
	}
}
