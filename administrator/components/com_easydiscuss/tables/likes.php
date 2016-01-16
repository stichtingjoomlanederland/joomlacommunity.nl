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

class DiscussLikes extends JTable
{
	/*
	 * The id of the category
	 * @var int
	 */
	var $id 					= null;

	/*
	 * The type
	 * @var string
	 */
	var $type					= null;

	/*
	 * Content ID
	 * @var int
	 */
	var $content_id				= null;

	/*
	 * creator ID
	 * @var int
	 */
	var $created_by				= null;

	/*
	 * Created datetime of the category
	 * @var datetime
	 */
	var $created				= null;



	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	function __construct(& $db )
	{
		parent::__construct( '#__discuss_likes' , 'id' , $db );
	}

	/**
	 * Loads a like object given the post id and the user id.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int 	The post id.
	 * @param	int 	The user id.
	 * @return	boolean
	 */
	public function loadByPost( $postId , $userId )
	{
		$db 	= DiscussHelper::getDBO();
		$query 	= 'SELECT * FROM ' . $db->nameQuote( $this->_tbl ) . ' '
				. 'WHERE ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( 'post' ) . ' '
				. 'AND ' . $db->nameQuote( 'content_id' ) . '=' . $db->Quote( $postId ) . ' '
				. 'AND ' . $db->nameQuote( 'created_by' ) . '=' . $db->Quote( $userId );

		$db->setQuery( $query );
		$data 	= $db->loadObject();

		return parent::bind( $data );
	}
	/**
	 * return false if the user already likes something
	 * else return the existing id
	 */
	function exists()
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'select `id` from `#__discuss_likes`';
		$query	.= ' where `type` = ' . $db->Quote( $this->type );
		$query	.= ' and `content_id` = ' . $db->Quote( $this->content_id );
		$query	.= ' and `created_by` = ' . $db->Quote( $this->created_by );

		$db->setQuery($query);
		$result = $db->loadResult();

		return ( empty( $result )) ? false : $result;
	}
}
