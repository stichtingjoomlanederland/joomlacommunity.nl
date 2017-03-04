<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
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
	public function assign($command , $userId, $post = null)
	{
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
			$user->addPoint($point->rule_limit);		
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
	 * @access	private
	 * @param	string	$command	The action string.
	 * @param	int		$userId		The actor's id.
	 *
	 * @return	Array	An array of BadgesHistory object.
	 **/
	public function getPoints($command)
	{
		$db	= ED::db();

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
	 * @param	string
	 * @return	array
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
				$index = $date->format($this->config->get('layout_dateformat'));
			}

			if (!isset($result[$index])) {
				$result[$index] = array();
			}

			$result[$index][] = $item;
		}

		return $result;
	}
}
