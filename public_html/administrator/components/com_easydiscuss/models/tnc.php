<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once dirname( __FILE__ ) . '/model.php';

class EasyDiscussModelTnc extends EasyDiscussAdminModel
{
	/**
	 * Check if a user vote exists in the system.
	 *
	 * @since	4.0
	 * @param	int		The unique post id.
	 * @param	int 	The user's unique id.
	 * @param	string	The user's ip address.
	 * @param	string	The unique session id.
	 * @return	boolean	True if user has already voted.
	 */
	public function hasAcceptedTnc($userId = null, $type = 'global', $sessionId = null, $ipaddress = null)
	{
		$db = $this->db;
		$query = 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__discuss_tnc');

		if ($userId) {
			$query	.= ' WHERE ' . $db->nameQuote('user_id') . '=' . $db->Quote($userId);
		} else {
			$query .= ' WHERE ' . $db->nameQuote('session_id') . '=' . $db->Quote($sessionId);
			$query .= ' AND ' . $db->nameQuote('ipaddress') . '=' . $db->Quote($ipaddress);
		}

		if ($type != 'global') {
			$query .= ' AND ' . $db->nameQuote('type') . '=' . $db->Quote($type);
		}

		$query .= ' AND ' . $db->nameQuote('state') . '=' . $db->Quote(1);

		$db->setQuery($query);

		$acceptedTnc = $db->loadResult() ? true : false;

		return $acceptedTnc;
	}
}
