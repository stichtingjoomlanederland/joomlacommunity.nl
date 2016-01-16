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

class DiscussOauthPosts extends JTable
{
	public $id			= null;
	public $post_id		= null;
	public $oauth_id	= null;

	public function __construct(& $db )
	{
		parent::__construct( '#__discuss_oauth_posts' , 'id' , $db );
	}

	public function exists( $postId , $oauthId )
	{
		$postId	= (int) $postId;

		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( $this->_tbl ) . ' '
				. 'WHERE ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $postId ) . ' '
				. 'AND ' . $db->nameQuote( 'oauth_id' ) . '=' . $db->Quote( $oauthId );
		$db->setQuery( $query );
		$result	= $db->loadResult();

		return $result > 0;
	}
}
