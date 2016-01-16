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

class DiscussUnread extends JTable
{
	public $id			= null;
	public $user_id		= null;
	public $post_id		= null;
	public $created		= null;
	public $status		= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(& $db )
	{
		parent::__construct( '#__discuss_unread' , 'id' , $db );
	}

	public function load( $userId = null, $postId = null, $key = null )
	{
		if($userId && $postId == null)
		{
			return parent::load( $key );
		}

		$db		= DiscussHelper::getDBO();
		$query	= ' SELECT '.$db->nameQuote( 'id' ).' FROM '.$db->nameQuote( '#__discuss_unread' );
		$query	.= ' WHERE '.$db->nameQuote( 'user_id' ). ' = '.$db->quote( $userId );
		$query	.= ' AND '.$db->nameQuote( 'post_id' ). ' = '.$db->quote( $postId );

		$db->setQuery( $query );
		$result = $db->loadResult();

		return $result;
	}

}
