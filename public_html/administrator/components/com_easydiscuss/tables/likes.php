<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

ED::import('admin:/tables/table');

class DiscussLikes extends EasyDiscussTable
{
	var $id = null;
	var $type = null;
	var $content_id	= null;
	var $created_by	= null;
	var $created = null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	function __construct(& $db)
	{
		parent::__construct('#__discuss_likes', 'id', $db);
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
	public function loadByPost($postId , $userId)
	{
		$db = ED::db();
		$query 	= 'SELECT * FROM ' . $db->nameQuote($this->_tbl) . ' '
				. 'WHERE ' . $db->nameQuote('type') . '=' . $db->Quote('post') . ' '
				. 'AND ' . $db->nameQuote('content_id') . '=' . $db->Quote($postId) . ' '
				. 'AND ' . $db->nameQuote('created_by') . '=' . $db->Quote($userId);

		$db->setQuery($query);
		$data = $db->loadObject();

		return parent::bind($data);
	}
	/**
	 * return false if the user already likes something
	 * else return the existing id
	 */
	public function likeExists()
	{
		$db	= ED::db();

		$query = 'select `id` from `#__discuss_likes`';
		$query	.= ' where `type` = ' . $db->Quote($this->type);
		$query	.= ' and `content_id` = ' . $db->Quote($this->content_id);
		$query	.= ' and `created_by` = ' . $db->Quote($this->created_by);

		$db->setQuery($query);
		$result = $db->loadResult();

		return (empty($result)) ? false : $result;
	}
}
