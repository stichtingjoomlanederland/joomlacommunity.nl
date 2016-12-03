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

ED::import('admin:/tables/table');

class DiscussConversation extends EasyDiscussTable
{
	public $id = null;
	public $created = null;
	public $created_by = null;
	public $lastreplied	= null;

	public function __construct(&$db)
	{
		parent::__construct('#__discuss_conversations', 'id', $db);
	}

	/**
	 * Loads a conversation record based on the existing conversations.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		$creator	The node id of the creator.
	 * @param	int		$recipient	The node id of the recipient.
	 */
	public function loadByRelation( $creator , $recipient )
	{
		$db = ED::db();
		$query = array();
		$query[] = 'SELECT COUNT(1) AS `related` , a.*';
		$query[] = 'FROM ' . $db->nameQuote( '#__discuss_conversations' ) . ' AS a';
		$query[] = 'INNER JOIN ' . $db->nameQuote( '#__discuss_conversations_participants' ) . ' AS b';
		$query[]	= 'ON a.' . $db->nameQuote( 'id' ) . ' = b.' . $db->nameQuote( 'conversation_id' );
		$query[]	= 'WHERE';
		$query[]	= '( b.' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $recipient ) . ' OR b.' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $creator ) . ' )';
		$query[]	= 'GROUP BY b.' . $db->nameQuote( 'conversation_id' );
		// here we restrict the conversation can be from one to one only.
		$query[]	= 'HAVING COUNT( b.' . $db->nameQuote( 'conversation_id' ) . ') = 2';
		$query 		= implode( ' ' , $query );

		// echo $query;exit;

		$db->setQuery( $query );

		$data	= $db->loadObject();

		if( !isset( $data->related ) )
		{
			return false;
		}

		if( $data->related >= 2 )
		{
			return parent::bind( $data );
		}
		return false;
	}
}
