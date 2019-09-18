<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussBadges extends EasyDiscuss
{
	var $exists	= null;

	public function assign($command, $userId)
	{
		// We don't have to give any badge to guests.
		if (!$userId || $userId == 0) {
			return;
		}

		// @task: Load necessary language files
		JFactory::getLanguage()->load('com_easydiscuss', JPATH_ROOT);
		
		$config = ED::config();

		// If badges is disabled, do not proceed.
		if (!$config->get('main_badges')) {
			return;
		}

		// @task: Compute the count of the history already matches any badge for this user.
		$total = $this->getTotal($command, $userId);

		// @task: Get the badges that is relevant to this command
		$badges	= $this->getBadges($command, $userId);

		if (!$badges) {
			return false;
		}

		foreach ($badges as $badge) {

			// @TODO: If the badge is configure to points instead of frequency, we should skip it.
			if ($badge->achieve_type != 'frequency') {
				continue;
			}

			if ($total >= $badge->rule_limit) {
				$table = ED::table('BadgesUsers');
				$table->set('badge_id', $badge->id);
				$table->set('user_id', $userId);
				$table->set('created', ED::date()->toSql());
				$table->set('published', 1);

				$table->store();

				// @task: Add a new notification when they earned a new badge.
				$notification = ED::table('Notifications');

				$notification->bind(array(
						'title' => JText::sprintf('COM_EASYDISCUSS_NEW_BADGE_NOTIFICATION_TITLE', JText::_($badge->title)),
						'cid' => $badge->id,
						'type' => DISCUSS_NOTIFICATIONS_BADGE,
						'target' => $userId,
						'author' => $userId,
						'permalink' => 'index.php?option=com_easydiscuss&view=profile&id=' . $userId
					));

				$notification->store();
				
				//insert into JS stream.
				if ($config->get('integration_jomsocial_activity_badges', 0)) {
					$badgeTable = ED::table('Badges');
					$badgeTable->load($badge->id);
					$badgeTable->uniqueId = $table->id;
					ED::jomsocial()->addActivityBadges($badgeTable);
				}
			}
		}

		return true;
	}

	/**
	 * Update user badge based on points achievement type
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function assignBadgesByCommand($command, $userId)
	{
		$user = ED::user($userId);

		if (!$user->id) {
			return;
		}

		// Get list of badges that are associated with point rule give.
		$model = ED::model('Badges');
		$badges = $model->getBadgesByCommand($command, $userId);

		if (!$badges) {
			return;
		}

		$achieveCommand = array();
		$removeCommand = array();

		// Now we get the points that are associated with the command
		foreach ($badges as $badge) {
			if ($badge->badge_achieve_rule) {
				$achieveCommand[] = $badge->badge_achieve_rule;
			}

			if ($badge->badge_remove_rule) {
				$removeCommand[] = $badge->badge_remove_rule;
			}
		}

		$pointsModel = ED::model('Points');
		$pointsLib = ED::points();

		// Get the compute points for all the rules above.
		$overallAchieveCommand = $pointsModel->getTotalPointsHistory($userId, $achieveCommand);
		$overallRemoveCommand  = $pointsModel->getTotalPointsHistory($userId, $removeCommand);

		// Now we get the threshold for each badges
		foreach ($badges as $badge) {

			$totalAchieve = 0;
			$totalRemove = 0;
			$negativeThreshold = false;

			if (isset($overallAchieveCommand[$badge->badge_achieve_rule])) {
				$totalAchieve = $overallAchieveCommand[$badge->badge_achieve_rule];

				// Multiply with points
				$points = $pointsLib->getPoints($badge->badge_achieve_rule);
				$totalAchieve = $totalAchieve * $points[0]->rule_limit;

				// Determeine the integer sign for points threshsold
				if ($points[0]->rule_limit < 0) {
					$negativeThreshold = true;
				}
			}

			if (isset($overallRemoveCommand[$badge->badge_remove_rule])) {
				$totalRemove = $overallRemoveCommand[$badge->badge_remove_rule];

				// Multiply with points
				$points = $pointsLib->getPoints($badge->badge_remove_rule);
				$totalRemove = $totalRemove * $points[0]->rule_limit;
			}

			// Sum both value together
			$totalPoints = $totalAchieve + $totalRemove;

			// Get badge points threshold
			$threshold = $badge->points_threshold;

			if ($negativeThreshold) {
				// Convert point threshold to negative value.
				$threshold = -1 * abs($threshold);

				// Remove the badge if necessary
				if ($totalPoints > $threshold) {
					$user->removeBadge($badge->id);
				}

				// Add the badge
				if ($totalPoints <= $threshold) {
					$user->addBadge($badge->id);
				}
			} else {

				// Add badge
				if ($totalPoints < $threshold) {
					$user->removeBadge($badge->id);
				}

				// Remove badge
				if ($totalPoints >= $threshold) {
					$user->addBadge($badge->id);
				}
			}
		}

		return;
	}

	/**
	 * Retrieve a list of badges for the specific command
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	private function getBadges($command, $userId)
	{
		$db = ED::db();

		$query	= 'SELECT a.* FROM ' . $db->nameQuote( '#__discuss_badges' ) . ' AS a '
				. 'INNER JOIN ' . $db->nameQuote( '#__discuss_rules' ) . ' AS b '
				. 'ON b.' . $db->nameQuote( 'id' ) . '= a.' . $db->nameQuote( 'rule_id' ) . ' '
				. 'WHERE b.' . $db->nameQuote( 'command' ) . '=' . $db->Quote( $command ) . ' '
				. 'AND a.' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 ) . ' '
				. 'AND a.id NOT IN( '
				. ' SELECT ' . $db->nameQuote( 'badge_id' ) . ' FROM ' . $db->nameQuote( '#__discuss_badges_users' ) . ' AS x '
				. ' WHERE x.' . $db->nameQuote( 'user_id') . '=' . $db->Quote( $userId ) . ' '
				. ')';
					
		$db->setQuery($query);

		$badges	= $db->loadObjectList(); 

		return $badges;
	}

	/**
	 * Retrieve total history for a user based on a specific command
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	private function getTotal($command, $userId)
	{
		$db = ED::db();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__discuss_users_history') . ' '
				. 'WHERE ' . $db->nameQuote('user_id') . '=' . $db->Quote($userId) . ' '
				. 'AND ' . $db->nameQuote('command') . '=' . $db->Quote($command);

		$db->setQuery($query);

		return (int) $db->loadResult();
	}

	/**
	 * Allow caller to manually assign the badge to the user
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function create($userId, $badgeId, $dateAchieved)
	{
		// Convert the date achieved to sql format.
		$dateAchieved = ED::Date($dateAchieved)->toSql();

		$table = ED::table('BadgesUsers');
		$table->set('badge_id', $badgeId);
		$table->set('user_id', $userId);
		$table->set('created', $dateAchieved);
		$table->set('published', 1);

		$state = $table->store();

		if (!$state) {
			return false;
		}

		return true;
	}

	/**
	 * Generates the html for badges in a post
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getPostHtml($userId, $limit = 3)
	{
		static $data = array();

		// by default we don't have assign any badge to guest user.
		// we should skip it here.
		if (!$userId) {
			return;
		}

		if (!isset($data[$userId])) {
			$user = ED::user($userId);
			$userBadges = $user->getBadges();
			$total = count($userBadges);
			$hasMoreBadges = $total > $limit;

			// Get the initial displayed badges
			$badges = array_splice($userBadges, 0, $limit);

			$theme = ED::themes();
			$theme->set('hasMoreBadges', $hasMoreBadges);
			$theme->set('userBadges', $userBadges);
			$theme->set('badges', $badges);
			$data[$userId] = $theme->output('site/badges/post');
		}
		
		return $data[$userId];
	}

	/**
	 * Generates the html for badges in the dropdown
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getToolbarHtml()
	{
		if (!$this->my->id) {
			return;
		}

		if (ED::easysocial()->hasToolbar($this->my->id)) {
			return ED::easysocial()->getToolbarBadgesHtml();
		}

		$user = ED::user();
		$badges = $user->getBadges();

		$theme = ED::themes();
		$theme->set('badges', $badges);

		$output = $theme->output('site/toolbar/badges');

		return $output;
	}

	/**
	 * Make sure the badges is enabled
	 *
	 * @since	4.1.7
	 * @access	public
	 */
	public function isEnabled()
	{
		$config = ED::config();

		if (!$config->get('main_badges')) {
			return false;
		}

		if (!$config->get('layout_badges_in_post')){
			return false;
		}

		return true;
	}
}