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

class DiscussMessageState extends JTable
{
	/**
	 * The unique id for the message.
	 * @var int
	 */
	public $id			= null;

	/**
	 * Foreign key to {#__discuss_messages}
	 * @var int
	 */
	public $message_id		= null;

	/**
	 * Deleted state
	 * @var int
	 */
	public $deleted 		= null;

	/**
	 * Deleted time
	 * @var datetime
	 */
	public $deleted_time		= null;


	/**
	 * Read state
	 * @var int
	 */
	public $isread 		= null;

	/**
	 * The user's id.
	 * @var int
	 */
	public $user_id			= null;

	public function __construct(& $db )
	{
		parent::__construct( '#__discuss_messages_states' , 'id' , $db );
	}

	/**
	 * Load a state based on a composite index.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The unique message id.
	 * @param	int		The unique user id.
	 */
	public function loadByComposite( $messageId , $userId )
	{
		$db 	= DiscussHelper::getDBO();

		$query 		= array();
		$query[]	= 'SELECT * FROM ' . $db->nameQuote( $this->_tbl );
		$query[]	= 'WHERE ' . $db->nameQuote( 'message_id' ) . '=' . $db->Quote( $messageId );
		$query[]	= 'AND ' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId );
		$query 		= implode( ' ' , $query );

		$db->setQuery( $query );
		$obj 		= $db->loadObject();

		if( !$obj )
		{
			return false;
		}

		return parent::bind( $obj );
	}
}
