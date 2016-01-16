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


class DiscussCategoryAclItem extends JTable
{
	/*
	 * The id of the category acl item
	 * @var int
	 */
	public $id			= null;
	public $action		= null;
	public $description	= null;
	public $published	= null;
	public $default		= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(& $db )
	{
		parent::__construct( '#__discuss_category_acl_item' , 'id' , $db );
	}

	public function getAllRuleItems()
	{
		$db = DiscussHelper::getDBO();

		$query = 'select * from `#__discuss_category_acl_item` order by id';
		$db->setQuery($query);

		return $db->loadObjectList();
	}

}
