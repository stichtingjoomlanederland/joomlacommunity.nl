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

class EasyDiscussAup extends EasyDiscuss
{
	var $exists	= null;
	var $rules	= array(
					DISCUSS_POINTS_NEW_DISCUSSION => 'new_discussion',
					DISCUSS_POINTS_DELETE_DISCUSSION => 'delete_discussion',
					DISCUSS_POINTS_VIEW_DISCUSSION => 'view_discussion',
					DISCUSS_POINTS_NEW_AVATAR => 'new_avatar',
					DISCUSS_POINTS_UPDATE_AVATAR => 'update_avatar',
					DISCUSS_POINTS_NEW_REPLY => 'new_reply',
					DISCUSS_POINTS_DELETE_REPLY => 'delete_reply',
					DISCUSS_POINTS_NEW_COMMENT => 'new_comment',
					DISCUSS_POINTS_DELETE_COMMENT => 'delete_comment',
					DISCUSS_POINTS_ACCEPT_REPLY => 'accept_reply',
					DISCUSS_POINTS_ANSWER_VOTE_UP => 'answer_vote_up',
					DISCUSS_POINTS_ANSWER_VOTE_DOWN => 'answer_vote_down',
					DISCUSS_POINTS_QUESTION_VOTE_UP => 'question_vote_up',
					DISCUSS_POINTS_QUESTION_VOTE_DOWN => 'question_vote_down'

				);

	/**
	 * Determines if Altauser points is enabled
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function exists()
	{
		static $exists = null;

		if (is_null($exists)) {
			jimport('joomla.filesystem.file');

			$file = JPATH_ROOT . '/components/com_altauserpoints/helper.php';
			$enabled = $this->config->get('integration_altauserpoints_enable');

			if (!JFile::exists($file) || !$enabled) {
				$exists = false;
				return $exists;
			}
		
			include_once($file);

			$exists = true;
		}

		return $exists;
	}

	/**
	 * Assign points via Altauser Points
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function assign($rule, $userId, $title = '')
	{
		if (!$this->exists()) {
			return false;
		}

		$aupId = AltaUserPointsHelper::getAnyUserReferreID($userId);
		
		if (!$aupId || !isset($this->rules[$rule])) {
			return false;
		}

		JFactory::getLanguage()->load('com_easydiscuss', JPATH_ROOT);
	
		$rule = $this->rules[$rule];
		$rule = 'plgaup_easydiscuss_' . strtolower($rule);
		
		return AltaUserPointsHelper::newpoints($rule, $aupId);
	}

	/**
	 * Retrieve the user point from Altauser
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function getUserPoints($userId)
	{
		static $points;

		if (!isset($points)) {
			$points = array();
		}

		if (empty($points[$userId])) {
			
			$db = ED::db();
			$query = 'SELECT `points` FROM `#__alpha_userpoints` WHERE `userid` = ' . $db->Quote($userId);

			$db->setQuery($query);
			$points[$userId] = $db->loadResult();
		}

		return $points[$userId];
	}	
}
