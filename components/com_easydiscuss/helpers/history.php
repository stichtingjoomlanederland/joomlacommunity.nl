<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

class DiscussHistoryHelper
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
	public function log( $command , $userId , $title, $content_id = 0 )
	{
		$activity	= DiscussHelper::getTable( 'History' );
		$activity->set( 'command' 		, $command );
		$activity->set( 'user_id'		, $userId );
		$activity->set( 'title'			, $title );
		$activity->set( 'created'		, DiscussHelper::getDate()->toMySQL() );
		$activity->set( 'content_id'	, $content_id );

		return $activity->store();
	}

	public function removeLog( $command, $userId, $content_id )
	{
		$db = DiscussHelper::getDBO();
		$table	= DiscussHelper::getTable( 'History' );

		$query = 'SELECT id'
				. ' FROM ' . $db->nameQuote( '#__discuss_users_history' )
				. ' WHERE ' . $db->nameQuote( 'command' ) . '=' . $db->Quote( $command )
				. ' AND ' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId )
				. ' AND ' . $db->nameQuote( 'content_id' ) . '=' . $db->Quote( $content_id );

		$db->setQuery( $query );
		$result = $db->loadResult();

		$table->delete( $result );
	}
}
