<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

ED::import('admin:/tables/table');

class DiscussRole extends EasyDiscussTable
{
	public $id = null;
	public $title = null;
	public $description	= null;
	public $usergroup_id = null;
	public $colorcode = null;
	public $published = null;
	public $ordering = null;
	public $created_time = null;
	public $created_user_id	= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__discuss_roles', 'id', $db);
	}

	/**
	 * Overrides parent's bind method to add our own logic.
	 *
	 * @param Array $data
	 **/
	public function bind($data, $ignore = array())
	{
		parent::bind($data);

		if (!$this->created_time || $this->created_time == '0000-00-00 00:00:00') {
			$this->created_time = ED::date()->toSql();
		}

		if (empty($this->created_user_id)) {
			$this->created_user_id = JFactory::getUser()->id;
		}
	}

	public function move($direction, $where = '')
	{
		$db = ED::db();

		//moving up
		if ($direction == -1) {
			if ($this->ordering > 0) {
				$query	= 'UPDATE `#__discuss_roles` SET `ordering` = `ordering` - 5 WHERE `ordering` <= ' . $db->quote( $this->ordering - 2 );
				$db->setQuery($query);
				$db->query();

				$query	= 'UPDATE `#__discuss_roles` SET `ordering` = `ordering` - 1 WHERE `ordering` = ' . $db->quote( $this->ordering - 1 );
				$db->setQuery($query);
				$db->query();

				$query	= 'UPDATE `#__discuss_roles` SET `ordering` = `ordering` - 3 WHERE `id` = ' . $db->quote($this->id);
				$db->setQuery($query);
				$db->query();
			}
		} else {
			$query	= 'UPDATE `#__discuss_roles` SET `ordering` = `ordering` + 5 WHERE `ordering` >= ' . $db->quote( $this->ordering + 2 );
			$db->setQuery($query);
			$db->query();

			$query	= 'UPDATE `#__discuss_roles` SET `ordering` = `ordering` + 1 WHERE `ordering` = ' . $db->quote( $this->ordering + 1 );
			$db->setQuery($query);
			$db->query();

			$query	= 'UPDATE `#__discuss_roles` SET `ordering` = `ordering` + 3 WHERE `id` = ' . $db->quote($this->id);
			$db->setQuery($query);
			$db->query();
		}

		return $this->rebuild();
	}

	public function rebuildOrdering()
	{
		// Get the input
		$pks = JRequest::getVar('cid', null, 'post', 'array');
		$order = JRequest::getVar('order', null, 'post', 'array');

		// Sanitize the input
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);

		if (is_array($pks) && is_array($order) && count($pks) == count($order)) {
			$db = ED::db();

			for ($i = 0, $count = count($pks); $i < $count; $i++) {

				$query = 'UPDATE `#__discuss_roles` SET `ordering` = ' . $order[$i] . ' WHERE `id` = ' . $pks[$i];
				$db->setQuery($query);

				if (!$db->query()) {
					return false;
				}
			}

			return $this->rebuild();
		} else {
			return false;
		}
	}

	public function rebuild()
	{
		$db = ED::db();
		$query = 'SELECT id, ordering FROM `#__discuss_roles` ORDER BY ordering, id DESC';
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		foreach ($rows as $i => $row)
		{
			$order = $i + 1;
			$query = 'UPDATE `#__discuss_roles` SET `ordering` = ' . $order . ' WHERE `id` = ' . $row->id;
			$db->setQuery($query);

			if (!$db->query()) {
				return false;
			}
		}

		return true;
	}

	public function getTitle($groups = array())
	{
		$role = $this->getRole($groups);
		return $role->title;
	}

	public function getRoleId($groups = array())
	{
		$role = $this->getRole($groups);
		return $role->id;
	}

	public function getRoleColor($groups = array())
	{
		$role = $this->getRole($groups);
		
		return $role->colorcode;
	}

	public function getRole($groups = array())
	{
		static $roles = array();

		$none = new stdClass;
		$none->id = '';
		$none->title = '';
		$none->colorcode = '';

		if (is_string($groups)) {
			$groups = array($groups);
		}

		if (empty($groups)) {
			return $none;
		}

		$sig = implode('-', $groups);

		if (!isset($roles[$sig])) {

			$db = ED::db();
			$query = "SELECT * FROM `#__discuss_roles` WHERE `usergroup_id` IN ( '" . implode("','", $groups) . "' ) AND `published` = 1 ORDER BY `ordering` LIMIT 1";

			$db->setQuery($query);

			$result = $db->loadObject();

			if (!$result) {
				$result = $none;
			}

			$roles[$sig] = $result;
		}

		return $roles[$sig];
	}
}
