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

class DiscussBadgesUsers extends JTable
{
	public $id			= null;
	public $badge_id	= null;
	public $user_id		= null;
	public $created		= null;
	public $published	= null;
	public $custom 		= null;
	
	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(& $db )
	{
		parent::__construct( '#__discuss_badges_users' , 'id' , $db );
	}

	public function loadByUser( $userId , $badgeId )
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'SELECT * FROM ' . $db->nameQuote( $this->_tbl ) . ' '
				. 'WHERE ' . $db->nameQuote( 'user_id' ) . ' = ' . $db->Quote( $userId ) . ' '
				. 'AND ' . $db->nameQuote( 'badge_id' ) . ' = ' . $db->Quote( $badgeId );
		$db->setQuery( $query );

		$row	= $db->loadObject();

		if( !$row )
		{
			return false;
		}
		
		return parent::bind( $row );
	}
}
