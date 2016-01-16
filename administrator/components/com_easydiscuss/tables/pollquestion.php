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

class DiscussPollQuestion extends JTable
{
	public $id			= null;
	public $post_id		= null;
	public $title		= null;
	public $multiple	= null;

	protected $_voters	= null;
	protected $_count	= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(& $db )
	{
		parent::__construct( '#__discuss_polls_question' , 'id' , $db );
	}

	/**
	 * Loads a poll question based on the post id.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function loadByPost( $postId )
	{
		if( !$postId )
		{
			return false;
		}

		$db 	= DiscussHelper::getDBO();
		$query 	= 'SELECT * FROM ' . $db->nameQuote( $this->_tbl ) . ' '
				. 'WHERE ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $postId );

		$db->setQuery( $query );

		$data 	= $db->loadObject();

		if( !$data )
		{
			return false;
		}

		return parent::bind( $data );
	}

	/**
	 * Retrieves a list of voters for this poll item.
	 *
	 * @access	public
	 *
	 * @return	Array	An array of DiscussProfile objects.
	 */
	public function getVoters( $limit = null )
	{
		if( !$this->_voters ) {
			$this->getVotersQuery($limit);
		}

		if( empty($this->_voters) )
		{
			return array();
		}

		$res  		= array_unique( $this->_voters );
		$profile	= DiscussHelper::getTable( 'Profile' );
		$profile->init( $res );

		$users	= array();
		foreach( $this->_voters as $res )
		{
			$profile	= DiscussHelper::getTable( 'Profile' );
			$profile->load( $res );

			$users[]	= $profile;
		}

		return $users;
	}

	public function getVotersCount()
	{
		if( is_null( $this->_count ) )
		{
			$db		= DiscussHelper::getDBO();

			$query	= 'select count( distinct a1.`user_id` ) ';
			$query	.= ' from `#__discuss_polls_users` as a1 ';
			$query	.= '   inner join `#__discuss_polls` as b1 on a1.`poll_id` = b1.`id`';
			$query	.= ' where b1.`post_id` = ' . $db->Quote( $this->post_id );

			$db->setQuery( $query );
			$result = $db->loadResult();

			$this->_count = $result;
		}

		return $this->_count;
	}

	protected function getVotersQuery( $limit = null )
	{
		$db		= DiscussHelper::getDBO();


		$query	= 'select distinct a.`user_id`,';
		$query	.= '  (select count( distinct a1.`user_id` ) ';
		$query	.= '          from `#__discuss_polls_users` as a1 ';
		$query	.= '               inner join `#__discuss_polls` as b1 on a1.`poll_id` = b1.`id`';
		$query	.= '           where b1.`post_id` = ' . $db->Quote( $this->post_id ) . ' ) as `total`';
		$query	.= '  from `#__discuss_polls_users` as a';
		$query	.= '  inner join `#__discuss_polls` as b on a.`poll_id` = b.`id`';
		$query	.= ' where b.`post_id` = ' . $db->Quote( $this->post_id );

		if( !is_null( $limit ) )
		{
			$query	.= ' LIMIT ' . $limit;
		}

		$db->setQuery( $query );
		$result = $db->loadObjectList();

		if( count( $result ) <= 0  )
		{
			$this->_voters	= array();
			$this->_count	= 0;
			return;
		}

		$voters = array();
		foreach( $result as $item )
		{
			$this->_count	= $item->total;
			$voters[]       = $item->user_id;
		}

		$this->_voters	= $voters;
	}
}
