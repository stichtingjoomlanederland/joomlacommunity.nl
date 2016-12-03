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

require_once dirname( __FILE__ ) . '/model.php';

class EasyDiscussModelRatings extends EasyDiscussAdminModel
{
	public function preloadRatings($postIds = array())
	{
		$db = ED::db();

		$query = array();
		$query[] = 'SELECT AVG(' . $db->qn('value') . ') AS ' . $db->qn('ratings');
		$query[] = ',COUNT(1) AS ' . $db->qn('total');
		$query[] = ',' . $db->qn('uid');
		$query[] = 'FROM ' . $db->qn('#__discuss_ratings') . ' AS a';
		$query[] = 'WHERE a.' . $db->qn('uid') . ' IN(' . implode(',', $postIds) . ')';
		$query[] = 'AND ' . $db->qn('type') . '=' . $db->Quote('question');
		$query[] = 'GROUP BY ' . $db->qn('uid');

		// we do this order by null to avoid filesort in mysql.
		$query[] = 'ORDER BY NULL';

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (!$result) {
			return array();
		}

		$ratings = array();

		foreach ($result as $row) {

			$obj = new stdClass();
			$obj->ratings = round($row->ratings);
			$obj->total = $row->total;

			$ratings[$row->uid] = $obj;
		}

		return $ratings;
	}

	public function hasRated($uid, $type, $userId = 0, $hash = '', $ipaddr = '')
	{
		$db = ED::db();
		$query	= 'SELECT COUNT(1) FROM ' . $db->qn( '#__discuss_ratings' ) . ' '
				. 'WHERE ' . $db->qn( 'uid' ) . '=' . $db->Quote( $uid ) . ' '
				. 'AND ' . $db->qn( 'type' ) . '=' . $db->Quote( $type );

		if ($userId) {
			$query .= ' AND ' . $db->qn( 'created_by' ) . '=' . $db->Quote( $userId );
		} else {
			// guest. we need to check the session as well as the ipaddr
			$query .= ' AND ' . $db->qn( 'created_by' ) . '=' . $db->Quote(0);
			$query .= ' AND (' . $db->qn('sessionid') . ' = ' . $db->Quote($hash) . ' OR ' . $db->qn('ip') . ' = ' . $db->Quote($ipaddr) . ')';
		}

		$db->setQuery($query);
		$rating	= $db->loadResult();

		return ($rating) ? $rating : 0;
	}

	
}
