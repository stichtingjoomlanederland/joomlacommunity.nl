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

class EasyDiscussHistory extends EasyDiscuss
{

	/**
	 * Creates a new history record for the particular action.
	 *
	 * @access	private
	 * @param	string	$command	The current action
	 * @param	int		$userId		The current actor
	 * @param	string	$title		The title of the history or action.
	 * @return	boolean	True on success, false otherwise.
	 **/
	public function log($command, $userId, $title, $content_id = 0)
	{
		$activity = ED::table('History');
		$activity->set('command', $command);
		$activity->set('user_id', $userId);
		$activity->set('title', $title);
		$activity->set('created', ED::date()->toSql());
		$activity->set('content_id', $content_id);

		return $activity->store();
	}

	/**
	 * Removes a log record
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function removeLog($command, $userId, $content_id)
	{
		$db = ED::db();
		$table	= ED::table('History');
		$options = array('command' => $command, 'user_id' => $userId, 'content_id' => $content_id);

		$exists = $table->load($options);

		if (!$exists) {
			return false;
		}

		$table->delete();
		return true;
	}
}
