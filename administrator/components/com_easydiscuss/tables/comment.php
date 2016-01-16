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

require_once DISCUSS_HELPERS . '/string.php';

class DiscussComment extends JTable
{
	/*
	 * The id of the comment
	 * @var int
	 */
	public $id			= null;

	/*
	* The id of the blog
	* @var int
	*/
	public $post_id		= null;

	/*
	* The comment
	* @var string
	*/
	public $comment		= null;

	/*
	* The name of the commenter
	* @var string
	*/
	public $name		= null;

	/*
	* The title of the comment
	* optional
	* @var string
	*/
	public $title		= null;

	/*
	* The email of the commenter
	* optional
	* @var string
	*/
	public $email		= null;

	/*
	* The website of the commenter
	* optional
	* @var string
	*/
	public $url			= null;

	/*
	* The ip of the visitor
	* optional
	* @var string
	*/
	public $ip			= null;


	/*
	* Created datetime of the comment
	* @var datetime
	*/
	public $created		= null;

	/*
	* modified datetime of the comment
	* optional
	* @var datetime
	*/
	public $modified	= null;

	/*
	* Tag publishing status
	* @var int
	*/
	public $published	= null;


	/*
	* Comment ordering
	* @var int
	*/
	public $ordering	= null;

	/*
	* user id
	* @var int
	*/
	public $user_id		= null;

	public $parent_id	= null;
	public $sent		= null;
	public $lft			= null;
	public $rgt			= null;


	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(& $db )
	{
		parent::__construct( '#__discuss_comments' , 'id' , $db );
	}

	public function bind($post, $isPost = false)
	{
		parent::bind( $post );

		if($isPost)
		{
			$date	= DiscussHelper::getDate();
			jimport( 'joomla.filter.filterinput' );
			$filter	= JFilterInput::getInstance();

			//replace a url to link
			$comment			= $filter->clean( $post->comment );
			$comment			= DiscussStringHelper::url2link( $comment );
			$this->comment		= $comment;

			$this->name			= $filter->clean( $post->name );
			$this->email		= $filter->clean( $post->email );

			$this->created		= $date->toMySQL();
			$this->modified		= $date->toMySQL();
			$this->published	= '1';
		}

		return true;
	}

	/**
	 * Determines if the comment can be converted to a discussion reply by the current user.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function canConvert()
	{
		$acl 	= DiscussHelper::getHelper( 'ACL' );

		if( DiscussHelper::isSiteAdmin() )
		{
			return true;
		}

		return false;
	}

	public function canDelete($pk = null, $joins = null)
	{
		$aclHelper	= DiscussHelper::getHelper( 'ACL' );

		$canDelete = ( DiscussHelper::isSiteAdmin()  || $aclHelper->allowed( 'delete_comment' ) || ($aclHelper->isOwner( $this->user_id ) ) );

		return $canDelete;
	}
}
