<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussRanks extends EasyDiscuss
{
	public function assignRank($userId = '', $type = 'posts')
	{

		$this->db = ED::db();

		// return false if rank disabled.
		if (!$this->config->get('main_ranking', 0) || !$userId) {
			return false;
		}

		$user = ED::user($userId);

		$curUserScore = 0;

		if ($this->config->get('main_ranking_calc_type', 'posts') == 'posts') {

			if ($type == 'points') {
				$curUserScore = 0;
			}

			if ($user->id != 0) {
				$tmpNumPostCreated = $user->numPostCreated;
				$tmpNumPostAnswered = $user->numPostAnswered;
				$curUserScore = $tmpNumPostCreated + $tmpNumPostAnswered;
			}
		} else {

			if ($type == 'posts') {
				$curUserScore = 0;
			} else {

				// We need to respect if user integrated with ES's point
				$esUserPoint = ED::easysocial()->getUserPoints($userId);

				if ($esUserPoint === false) {
					$curUserScore = $user->points;
				} else {
					$curUserScore = $esUserPoint;
				}
			}
		}

		if ($curUserScore == 0) {
			return false;
		}

		//get current user rank
		$userRank = ED::table('RanksUsers');
		$userRank->load($user->id, true);

		$query = 'select `id`, `title`, `end` from `#__discuss_ranks`';
		$query .= ' where ( (' . $this->db->Quote($curUserScore) . ' >= `start` and ' . $this->db->Quote($curUserScore) . ' <= `end` ) OR ' . $this->db->Quote($curUserScore) . ' > `end` )';

		// it is not always need to be higher than a current rank. 
		// In some cases, like change from 'number of post' to 'points' might lower down the rank.
		// if (!empty($userRank->rank_id)) {
		// 	$query .= ' and `id` > ' . $this->db->Quote($userRank->rank_id);
		// }

		$query .= ' ORDER BY `end` DESC limit 1';

		$this->db->setQuery($query);
		$newRank = $this->db->loadObject();

		if (!is_null($newRank)) {

			if (empty($newRank->id)) {
				return true;
			}

			// Before we assign new rank, we must remove the current rank.
			if (!is_null($userRank->id)) {
				$userRank->delete();
			}

			// insert new rank into users
			$data = array();
			$data['rank_id'] = $newRank->id;
			$data['user_id'] = $user->id;

			$userNewRank = ED::table('RanksUsers');
			$userNewRank->bind($data);
			$userNewRank->store();

			$rank = new stdClass();
			$rank->rank_id = $newRank->id;
			$rank->user_id = $user->id;
			$rank->title = $newRank->title;
			$rank->uniqueId = $userNewRank->id;

			//insert into JS stream.
			if ($this->config->get('integration_jomsocial_activity_ranks', 0 )) {
				ED::jomsocial()->addActivityRanks($rank);
			}

			ED::easysocial()->rankStream($rank);
		}

		return true;
	}

	public function getScore($userId = '', $percentage = true)
	{
		$this->db = ED::db();

		if ($userId == '') {
			$userId = '0';
		}

		static $scores = array();
		static $max = null;

		$index = $userId . (int) $percentage;

		if (!isset($scores[$index])) {

			// get the points from profile table
			$user = ED::user($userId);

			$score = 0;

			if ($this->config->get( 'main_ranking_calc_type', 'posts') == 'posts') {

				if ($user->id != 0) {

					$tmpNumPostCreated = $user->numPostCreated;
					$tmpNumPostAnswered = $user->numPostAnswered;
					$score = $tmpNumPostCreated + $tmpNumPostAnswered;
				}
			} else {
				$score = $user->points;
			}

			if ($percentage) {

				if (is_null($max)) {
					$query  = 'SELECT MAX(`end`) FROM `#__discuss_ranks`';
					$this->db->setQuery($query);

					$maxResult = $this->db->loadResult();
					$max = $maxResult; // cache
				}

				// Initial value
				$scores[$index] = '0';

				if (!empty($max)) {
					if ($score >= $max) {
						$scores[$index] = 100;
					} else {
						$scorePercentage = round(($score / $max) * 100);
						$scores[ $index ] = $scorePercentage;
					}
				}

			} else {
				$scores[$index]	= $score;
			}
		}

		return $scores[$index];
	}

	/**
	 * Get a user's rank title.
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function getRank($userId = '')
	{
		$this->db = ED::db();

		if (!$this->config->get('main_ranking')) {
			return;
		}

		// Load language string.
		JFactory::getLanguage()->load('com_easydiscuss', JPATH_ROOT);

		if ($userId == '') {
			$userId = '0';
		}

		$user = ED::user($userId);
		$title = '';

		static $mapping	= array();

		if (!isset($mapping[$user->id])) {

			$query = array();
			$query[] = 'SELECT b.' . $this->db->nameQuote('title');
			$query[] = 'FROM ' . $this->db->nameQuote('#__discuss_ranks_users') . ' AS a';
			$query[] = 'INNER JOIN ' . $this->db->nameQuote('#__discuss_ranks') . ' AS b';
			$query[] = 'ON a.' . $this->db->nameQuote('rank_id') . '= b.' . $this->db->nameQuote('id');
			$query[] = 'WHERE a.' . $this->db->nameQuote('user_id') . '=' . $this->db->Quote($user->id);
			$query[] = 'ORDER BY a.`rank_id` DESC LIMIT 1';

			$query = implode(' ', $query);

			$this->db->setQuery($query);
			$title = $this->db->loadResult();

			if (!$title) {
				$title = 'COM_EASYDISCUSS_NO_RANKING';
			}

			$mapping[$user->id]	= JText::_($title);
		}

		return $mapping[$user->id];
	}

}
