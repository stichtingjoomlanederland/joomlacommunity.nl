<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once dirname( __FILE__ ) . '/model.php';

class EasyDiscussModelRanks extends EasyDiscussAdminModel
{
	var $_data = null;

	public function __construct()
	{
		parent::__construct();
	}

	public function getRanks()
	{
		$db	= ED::db();
		$query = 'SELECT * FROM ' . $db->nameQuote('#__discuss_ranks');
		$query .= ' ORDER BY `id`';

		$db->setQuery($query);
		
		return $db->loadObjectList();
	}

	/**
	 * Remove existing rank items
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function removeRanks($ids)
	{
		if (!$ids) {
			return;
		}

		$table = ED::table('Ranks');

		foreach ($ids as $id) {
			$table->load($id);

			if (!$table->id) {
				continue;
			}

			$rankTitle = $table->title;

			$state = $table->delete();

			// log the current action into database.
			$actionlog = ED::actionlog();
			$actionlog->log('COM_ED_ACTIONLOGS_RANKS_DELETED', 'rank', array(
				'rankTitle' => JText::_($rankTitle)
			));
		}		
	}
}
