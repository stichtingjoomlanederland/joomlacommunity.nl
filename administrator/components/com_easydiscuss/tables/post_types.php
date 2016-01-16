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

class DiscussPost_types extends JTable
{
	public $id			= null;
	public $title		= null;
	public $suffix		= null;
	public $created		= null;
	public $published	= null;
	public $alias		= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(& $db )
	{
		parent::__construct( '#__discuss_post_types' , 'id' , $db );
	}

	public function load( $key = null, $reset = true )
	{
		$db		= DiscussHelper::getDBO();

		// $query	= 'SELECT ' . $db->nameQuote( 'id' ) . ' FROM ' . $db->nameQuote( $this->_tbl ) . ' '
		// 		. 'WHERE ' . $db->nameQuote( 'alias' ) . '=' . $db->Quote( $key );

		// $db->setQuery( $query );
		// $id		= $db->loadResult();

		// // Try replacing ':' to '-' since Joomla replaces it
		// if( !$id )
		// {
		// 	$query	= 'SELECT id FROM ' . $this->_tbl . ' '
		// 			. 'WHERE alias=' . $db->Quote( JString::str_ireplace( ':' , '-' , $key ) );
		// 	$db->setQuery( $query );

		// 	$id		= $db->loadResult();
		// }
		return parent::load( $key );
	}

	public function delete($pk = null)
	{
		$state	= parent::delete($pk);
		return $state;
	}

	public function updateTopicPostType( $oldValue )
	{
		$db = DiscussHelper::getDBO();

		$query = 'update `#__discuss_posts` set `post_type` = ' . $db->Quote( $this->alias );
		$query .= ' where `post_type` = ' . $db->Quote( $oldValue );

		$db->setQuery( $query );
		$db->query();
	}

}
