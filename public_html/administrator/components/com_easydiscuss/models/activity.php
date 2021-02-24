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

require_once dirname(__FILE__) . '/model.php';

class EasyDiscussModelActivity extends EasyDiscussAdminModel
{

	/**
	 * Get post's activity logs
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function getLogs($utype, $uid, $options = array())
	{
		$db = ED::db();

		$start = ED::normalize($options, 'start', '');
		$end = ED::normalize($options, 'end', '');
		$sort = ED::normalize($options, 'sort', '');

		$query = "select a.*, 1 as `isActivity`";
		$query .= " FROM `#__discuss_activities` AS a";
		$query .= " WHERE a.`utype` = " . $db->Quote($utype);
		$query .= " and a.`uid` = " . $db->Quote($uid);

		if ($start && $end) {
			if ($start == $end) {
				$query .= " and a.`created` >= " . $db->Quote($start);
			} else {
				$query .= " and (a.`created` >= " . $db->Quote($start) . " and a.`created` <= " . $db->Quote($end) . ")";
			}
			
		} else if ($start && !$end) {
			$query .= " and a.`created` <= " . $db->Quote($start);
		} else if (!$start && $end) {
			$query .= " and a.`created` <= " . $db->Quote($end);
		}

		$query .= " ORDER BY a.`created`";
		$query .= $sort == 'oldest' ? " ASC" : " DESC"; 

		// echo $query;
		// echo '<br><br>';

		$db->setQuery($query);

		$results = $db->loadObjectList();

		if ($results) {
			$tmp = array();
			$counters = array();

			foreach ($results as $data) {

				$key = $data->created;

				$tbl = ED::table('Activity');
				$tbl->bind($data);

				$tbl->isActivity = $data->isActivity;

				if (isset($tmp[$key])) {

					// this mean we have activity logs that has the same timestamp. probably due to 
					// multiple labellig at the same time. e.g. using slash command to add label or status.

					$count = $counters[$key];
					$tmpCreated = new JDate($data->created . ' +' . $count++ .' second');

					// update the key count here.
					$counters[$key] = $count;

					// now let assign new key
					$key = $tmpCreated->toSql();

				} else {
					$counters[$key] = 1;
				}

				$tmp[$key] = $tbl;
			}

			$results = $tmp;
		}

		return $results;
	}

}
