<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

ED::import('admin:/tables/table');

class DiscussPoll extends EasyDiscussTable
{
	public $id				= null;
	public $post_id			= null;
	public $value			= null;
	public $count			= null;
	public $multiple_polls	= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(& $db )
	{
		parent::__construct( '#__discuss_polls' , 'id' , $db );
	}


	public function loadByValue( $value , $postId )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( $this->_tbl ) . ' '
				. 'WHERE ' . $db->nameQuote( 'value' ) . '=' . $db->Quote( $value ) . ' '
				. 'AND ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $postId );
		$db->setQuery( $query );
		$result	= $db->loadObject();

		if( !$result )
		{
			return;
		}

		return parent::bind( $result );
	}

	public function delete($pk = null)
	{
		$state	= parent::delete( $pk );

		$db		= DiscussHelper::getDBO();

		$query	= 'DELETE FROM ' . $db->nameQuote( '#__discuss_polls_users' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'poll_id' ) . '=' . $db->Quote( $this->id );
		$db->setQuery( $query );
		$db->Query();

		return $state;
	}
}
