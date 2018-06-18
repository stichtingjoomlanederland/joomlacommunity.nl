<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once dirname( __FILE__ ) . '/model.php';

class EasyDiscussModelRanks extends EasyDiscussAdminModel
{
	var $_data = null;

	function __construct()
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
		$db = ED::db();
		$idStr	= '';

		if (is_array($ids)) {
			
			for($i = 0; $i < count($ids); $i++) {
				
				$id	= $ids[$i];
				$idStr = (empty($idStr)) ? $db->Quote($id) : $idStr . ',' . $db->Quote($id);
			}

		} else {
			$idStr = $db->Quote($ids);
		}

		$query = 'DELETE FROM `#__discuss_ranks` WHERE `id` IN ('.$idStr.')';
		$db->setQuery($query);
		$db->query();
	}
}
