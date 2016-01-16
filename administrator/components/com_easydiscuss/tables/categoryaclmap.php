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

class DiscussCategoryAclMap extends JTable
{
	/*
	 * The id of the category acl
	 * @var int
	 */
	public $id			= null;

	/*
	* The category id
	* @var int
	*/
	public $category_id	= null;

	/*
	* Category acl content id (joomla group id)
	* @var int
	*/
	public $content_id	= null;


	/*
	* Category acl type (group)
	* @var string
	*/
	public $type		= null;

	/*
	* Category status
	* @var int
	*/
	public $status		= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(& $db )
	{
		parent::__construct( '#__discuss_category_acl_map' , 'id' , $db );
	}
}
