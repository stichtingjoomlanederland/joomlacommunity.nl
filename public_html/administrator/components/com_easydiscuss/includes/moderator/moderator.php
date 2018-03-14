<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussModerator extends EasyDiscuss
{
	public static function isModerator($categoryId = null, $userId = null)
	{
		static $result	= array();

		if (!$userId) {
			$userId = JFactory::getUser()->id;
		}

		// If user id is 0, we know for sure they are not a moderator.
		if (!$userId) {
			return false;
		}

		// Site admin is always a moderator.
		if (ED::isSiteAdmin($userId)) {
			return true;
		}

		// If category is not supplied, caller might just want to check if
		// the user is a moderator of any category.
		if (is_null($categoryId)){

			if (isset($result['isModerator'])) {
				return $result['isModerator'];
			}

			$db = ED::db();

			// Get the user's groups first.
			$gids = ED::getUserGids($userId);

			// Now, check if the current user has any assignments to this acl id or not.
			$query = array();
			$query[] = 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__discuss_category_acl_map');
			$query[] = 'WHERE ' . $db->nameQuote('acl_id') . ' = ' . $db->Quote(DISCUSS_CATEGORY_ACL_MODERATOR);

			if ($userId) {
				$query[] = 'AND ((';

				if ($gids) {
					$query[] = $db->nameQuote('type') . '=' . $db->Quote('group');
					$query[] = 'AND ' . $db->nameQuote('content_id') . ' IN(';

					$gstring = '';
					for ($i = 0; $i < count($gids); $i++) {
						$gval = $db->Quote($gids[$i]);
						$gstring .= $gstring ? ',' . $gval : $gval;
					}
					$query[] = $gstring;

					$query[] = ')';
				}

				$query[] = ')';
				$query[] = 'OR';
				$query[] = '(' . $db->nameQuote('type') . ' = ' . $db->Quote('user');
				$query[] = 'AND ' . $db->nameQuote('content_id') . '=' . $db->Quote($userId);
				$query[] = '))';
			}

			$query = implode(' ', $query);

			// echo $query;exit;

			$db->setQuery($query);

			$count = $db->loadResult();

			$isModerator = $count > 0;

			$result['isModerator'] = $isModerator;

			return $result['isModerator'];
		}

		if (!array_key_exists('groupId', $result)) {
			$table = ED::category($categoryId);
			$result[$categoryId] = $table->getModerators();
		}

		$isModerator = in_array($userId, $result[$categoryId]);

		return $isModerator;
	}

	// Return an array of moderators names, given an array of moderators ids
	public static function getModeratorsNames($moderatorIds)
	{
		$modNames = array();

		if (!empty($moderatorIds)) {
			//preload users
			ED::user($moderatorIds);

			foreach ($moderatorIds as $userId) {
				$mod = ED::user($userId);
				$modNames[] = $mod->getLinkHTML();
			}
		}

		return $modNames;
	}

	/**
	 * Displays the html code showing the moderator's name.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function html($categoryId)
	{
		$category = ED::category($categoryId);

		if (!$category->id) {
			return '';
		}

		$moderators = $category->getModerators();
		$modNames = self::getModeratorsNames( $moderators );

		if (!empty($modNames)) {
			return JText::_('COM_EAYDISCUSS_CATEGORY_MODERATORS') . ': ' . implode(', ', $modNames);
		}

		return false;
	}

	public static function getSelectOptions($categoryId)
	{
		$category = ED::category($categoryId);
		$mods = $category->getModerators();

		$options = array();
		$options[] = JHTML::_('select.option', 0, JText::_('COM_EASYDISCUSS_MODERATOR_OPTION_NONE'));

		//preload users
		ED::user($mods);

		foreach ($mods as $userId) {
			$user = ED::user($userId);
			$options[] = JHTML::_('select.option', $userId, $user->getName());
		}

		return $options;
	}

	/**
	 * Displays a drop down list of moderators on the site.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int 	The unique category id.
	 *
	 */
	public static function getModeratorsDropdown($categoryId)
	{
		$category = ED::category($categoryId);

		if (!$category->id) {
			return '';
		}

		$moderators = array();
		$moderators[0] = JText::_('COM_EASYDISCUSS_MODERATOR_OPTION_NONE');

		$list = $category->getModerators();

		// Find super admins.
		$siteAdmins = ED::getSAUsersIds();
		foreach ($siteAdmins as $id) {
			$list[] = $id;
		}

		// lets preload all the moderator.
		$list = array_unique($list);

		// Get the current logged in user
		$my = JFactory::getUser();

		if ($list) {

			// preload users
			ED::user($list);

			foreach ($list as $userId) {
				$mod = ED::user($userId);
				$moderators[$mod->id] = $my->id == $mod->id ? JText::_('COM_EASYDISCUSS_MYSELF') : $mod->getName();
			}
		}

		return $moderators;
	}

	public static function getModeratorsEmails($categoryId)
	{
		$category = ED::category($categoryId);

		if (!$category->id) {
			return array();
		}

		$emails = array();

		$moderators = $category->getModerators();

		if ($moderators) {
			foreach ($moderators as $userid) {
				$emails[] = JFactory::getUser($userid)->email;
			}
		}

		return $emails;
	}
}
