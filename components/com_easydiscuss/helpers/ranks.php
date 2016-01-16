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

class DiscussRanksHelper
{
	public function assignRank( $userId = '', $type = 'posts' )
	{
		$db			= DiscussHelper::getDBO();
		$config		= DiscussHelper::getConfig();

		// return false if rank disabled.
		if(! $config->get('main_ranking', 0) ) return false;

		$user		= JFactory::getUser( $userId );

		// return false if user is a guest.
		if( $user->id == 0 ) return false;

		$profile	= DiscussHelper::getTable( 'Profile' );
		$profile->load( $user->id );

		$curUserScore	= 0;

		if( $config->get( 'main_ranking_calc_type', 'posts' ) == 'posts' )
		{
			if( $type == 'points' )
			{
				$curUserScore = 0;
			}
			else
			{
				if( $profile->id != 0 )
				{
					$tmpNumPostCreated	= $profile->numPostCreated;
					$tmpNumPostAnswered	= $profile->numPostAnswered;
					$curUserScore		= $tmpNumPostCreated + $tmpNumPostAnswered;
				}
			}
		}
		else
		{
			if( $type == 'posts' )
			{
				$curUserScore = 0;
			}
			else
			{
				$curUserScore	= $profile->points;
			}
		}

		if( $curUserScore == 0 )
			return false;

		//get current user rank
		$userRank	= DiscussHelper::getTable( 'RanksUsers' );
		$userRank->load( $user->id , true);

		$query  = 'select `id`, `title`, `end` from `#__discuss_ranks`';
		$query  .= ' where ( (' . $db->Quote( $curUserScore ) . ' >= `start` and ' . $db->Quote( $curUserScore ) . ' <= `end` ) OR ' . $db->Quote( $curUserScore ) . ' > `end` )';
		if( !empty($userRank->rank_id) )
		{
			$query	.= ' and `id` > ' . $db->Quote( $userRank->rank_id );
		}
		$query  .= ' ORDER BY `end` DESC limit 1';

		$db->setQuery($query);
		$newRank	= $db->loadObject();

		if(! is_null( $newRank ) )
		{
			if( empty($newRank->id) )
			{
				return true;
			}


			// insert new rank into users
			$data = array();
			$data['rank_id']	= $newRank->id;
			$data['user_id']	= $user->id;

			$userNewRank	= DiscussHelper::getTable( 'RanksUsers' );
			$userNewRank->bind( $data);
			$userNewRank->store();

			$rank			= new stdClass();
			$rank->rank_id	= $newRank->id;
			$rank->user_id	= $user->id;
			$rank->title	= $newRank->title;
			$rank->uniqueId	= $userNewRank->id;
			
			//insert into JS stream.
			if( $config->get( 'integration_jomsocial_activity_ranks', 0 ) )
			{
				DiscussHelper::getHelper( 'jomsocial' )->addActivityRanks( $rank );
			}

			DiscussHelper::getHelper( 'easysocial' )->rankStream( $rank );
		}

		return true;
	}

	public function getScore( $userId = '', $percentage = true )
	{
		$db			= DiscussHelper::getDBO();
		$config		= DiscussHelper::getConfig();

		if( $userId == '' )
		{
			$userId = '0';
		}

		static $scores 	= array();

		$index 			= $userId . $percentage;

		if( !isset( $scores[ $index ] ) )
		{
			// get the points from profile table
			$profile = DiscussHelper::getTable( 'Profile' );
			$profile->load( $userId );

			$score	= 0;

			if( $config->get( 'main_ranking_calc_type', 'posts' ) == 'posts' )
			{
				if( $profile->id != 0 )
				{
					$tmpNumPostCreated	= $profile->numPostCreated;
					$tmpNumPostAnswered	= $profile->numPostAnswered;
					$score				= $tmpNumPostCreated + $tmpNumPostAnswered;
				}
			}
			else
			{
				$score	= $profile->points;
			}

			if( $percentage )
			{
				$query  = 'SELECT MAX(`end`) FROM `#__discuss_ranks`';
				$db->setQuery($query);

				$maxResult  = $db->loadResult();

				// Initial value
				$scores[ $index ]	= '0';

				if( !empty( $maxResult ) )
				{
					if( $score >= $maxResult )
					{
						$scores[ $index ]	= 100;
					}
					else
					{
						$scorePercentage 	= round( ( $score / $maxResult ) * 100 );
						$scores[ $index ]	= $scorePercentage;
					}
				}

			}
			else
			{
				$scores[ $index ]	= $score;
			}
		}

		return $scores[ $index ];
	}

	/**
	 * Get a user's rank title.
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function getRank( $userId = '' )
	{
		$config		= DiscussHelper::getConfig();

		if(! $config->get( 'main_ranking' ) )
			return;

		// Load language string.
		JFactory::getLanguage()->load( 'com_easydiscuss' , JPATH_ROOT );

		$db		= DiscussHelper::getDBO();
		$user	= null;

		if( $userId == '' )
		{
			$userId = '0';
		}

		$user	= JFactory::getUser( $userId );
		$title	= '';

		static $mapping	= array();

		if( !isset( $mapping[ $user->id ] ) )
		{
			$query 		= array();
			$query[] 	= 'SELECT b.' . $db->nameQuote( 'title' );
			$query[]	= 'FROM ' . $db->nameQuote( '#__discuss_ranks_users' ) . ' AS a';
			$query[]	= 'INNER JOIN ' . $db->nameQuote( '#__discuss_ranks' ) . ' AS b';
			$query[]	= 'ON a.' . $db->nameQuote( 'rank_id' ) . '= b.' . $db->nameQuote( 'id' );
			$query[]	= 'WHERE a.' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $user->id );
			$query[]	= 'ORDER BY b.`id` DESC LIMIT 1';

			$query 		= implode( ' ' , $query );

			$db->setQuery( $query );
			$title 		= $db->loadResult();

			if( !$title )
			{
				$title 	= 'COM_EASYDISCUSS_NO_RANKING';
			}

			$mapping[ $user->id ]	= JText::_( $title );

		}
		return $mapping[ $user->id ];
	}

}
