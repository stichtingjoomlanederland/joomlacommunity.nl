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

class DiscussPriority extends EasyDiscussTable
{
	public $id = null;
	public $title = null;
	public $color = null;
	public $created = null;

	public function __construct(&$db)
	{
		parent::__construct('#__discuss_priorities', 'id', $db);
	}

	/**
	 * Overrides parent's delete method to add our own logic.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function delete($pk = null)
	{
		$db = ED::db();

		$query = 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_posts' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'priority' ) . '=' . $db->Quote( $this->id );

		$db->setQuery($query);
		$count = $db->loadResult();

		if ($count > 0) {
			return false;
		}

		return parent::delete();
	}
}
