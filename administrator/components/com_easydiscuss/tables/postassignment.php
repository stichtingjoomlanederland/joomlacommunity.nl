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


class DiscussPostAssignment extends JTable
{
	public $id			= null;
	public $post_id		= null;
	public $assignee_id	= null;
	public $assigner_id	= null;
	public $created		= null;
	public $description	= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(& $db )
	{
		parent::__construct( '#__discuss_assignment_map' , 'id' , $db );
	}

	/**
	 * Load by post id. Return Only ONE latest record
	 */
	public function load( $keys = null, $reset = true )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT id FROM `#__discuss_assignment_map` WHERE `post_id` = ' . $db->quote( $keys )
				. ' ORDER BY `created` DESC LIMIT 0, 1';

		$db->setQuery( $query );
		$result = $db->loadResult();

		return parent::load( $result, $reset );
	}

	public function store( $updateNulls = false )
	{
		if( empty($this->created) || $this->created == '0000-00-00 00:00:00' )
		{
			$this->created	= DiscussHelper::getDate()->toMySQL();
		}

		return parent::store();
	}
}
