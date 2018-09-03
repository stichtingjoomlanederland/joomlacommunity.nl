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
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussPoints extends EasyDiscuss
{
	public function assign($command , $userId, $post = null, $voted = false)
	{
		$voteLib = ED::vote();

		// Determine if voting behaviour is contribution
		$isVotingContribution = $voteLib->isVotingContribution();

		// if the is contribution voting behaviour and this user already voted this before
		// Mean this user already gain the point so we have to skip it
		// Because when use this contribution behaviour, it will always check for the upvote point rules only
		if ($isVotingContribution && $voted) {
			return false;
		}

		// Assign points via EasySocial
		ED::easysocial()->assignPoints($command, $userId, $post);

		$config = ED::config();

		// If points is disabled, do not proceed
		if (!$config->get('main_points')) {
			return false;
		}

		if (!$userId) {
			return false;
		}

		$points	= $this->getPoints($command);

		if (!$points) {
			return false;
		}

		$user = ED::user($userId);

		foreach ($points as $point) {

			$offsetPoint = false;

			if ($voted) {

				// this mean user is doing a vote reverse.
				// let try to get the 'opposite' of this command to offset user's previous action points
				if (stristr($command, '.unvote.') !== false) {
					// now we need to reverse previous action which is the up vote.
					$revCommand = str_replace('.unvote.', '.vote.', $command);

				} else if (stristr($command, '.vote.') !== false) {
					// now we need to reverse previous action which is the up vote.
					$revCommand = str_replace('.vote.', '.unvote.', $command);
				}

				// proceed further when the command is now different than the original command
				if ($revCommand != $command) {

					$revPoints = $this->getPoints($revCommand);

					if ($revPoints && isset($revPoints[0])) {
						$revPoint = $revPoints[0]->rule_limit;

						if ($revPoint < 0) {
							$offsetPoint = abs($revPoint);
						} else if ($revPoint > 0) {
							$offsetPoint = -$revPoint;
						}
					}

				}
			}

			$user->addPoint($point->rule_limit, false, $voted, $offsetPoint);
		}

		// Add badges based on the command achievement type
		$badges = ED::badges();
		$badges->assignBadgesByCommand($command, $userId);

		$user->store();

		return true;
	}

	/**
	 * Retrieve a list of points for the specific command
	 *
	 * @since	4.0
	 * @access	public
	 **/
	public function getPoints($command)
	{
		$db	= ED::db();
		$voteLib = ED::vote();

		// Determine if voting behaviour is contribution
		$isVotingContribution = $voteLib->isVotingContribution();

		// if the current voting behaviour is contribution type
		// Ensure that is always look for the upvote point rules
		if ($isVotingContribution) {

			// try to convert those unvote rules to vote
			$replaceRules = $voteLib->convertUpVoteRules($command);

			// if match then only assign to the command
			if ($replaceRules) {
				$command = $replaceRules;
			}
		}

		$query 	= 'SELECT a.* FROM ' . $db->nameQuote('#__discuss_points') . ' AS a '
				. 'INNER JOIN ' . $db->nameQuote('#__discuss_rules') . ' AS b '
				. 'ON b.' . $db->nameQuote('id') . '= a.' . $db->nameQuote('rule_id') . ' '
				. 'WHERE b.' . $db->nameQuote('command') . '=' . $db->Quote($command) . ' '
				. 'AND a.' . $db->nameQuote('published') . '=' . $db->Quote(1);

		$db->setQuery($query);

		$points	= $db->loadObjectList();

		return $points;
	}

	/**
	 * This method should be used to display the result on the page rather than directly using format
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function group(&$items)
	{
		$result	= array();

		foreach ($items as $item) {

			$today = ED::date();
			$date = ED::date($item->created);

			if ($today->format('j/n/Y') == $date->format('j/n/Y')) {
				$index = JText::_('COM_EASYDISCUSS_POINTS_HISTORY_TODAY');
			} else {
				$index = $date->format(JText::_('DATE_FORMAT_LC1'));
			}

			if (!isset($result[$index])) {
				$result[$index] = array();
			}

			$result[$index][] = $item;
		}

		return $result;
	}

	/**
	 * Determine if the point rules publish/unpublished
	 *
	 * @since	4.0
	 * @access	public
	 **/
	public function isPointRulesPublished($command)
	{
		$model = ED::model('points');
		$result = $model->pointRulesExist($command);

		return $result;
	}
}
