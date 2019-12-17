<?php
/**
 * @package    PwtAcl
 *
 * @author     Sander Potjer - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com/pwt-acl
 */

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Router\Route;

// No direct access.
defined('_JEXEC') or die;

/**
 * Pwtacl Model
 *
 * @since   3.0
 */
class PwtaclModelDashboard extends ListModel
{
	/**
	 * Get Table data.
	 *
	 * @return  array
	 * @since   3.0
	 * @throws  Exception
	 */
	public function getTableData()
	{
		$app       = Factory::getApplication();
		$type      = $app->input->getString('type', 'groups');
		$search    = $app->input->getString('search');
		$searchkey = $search[0];
		$db        = Factory::getDbo();

		// In case of groups
		if ($type == 'groups')
		{
			$where = ($searchkey) ? $db->quoteName('a.title') . ' LIKE ' . $db->quote('%' . $searchkey . '%') : '';
			$data  = $this->getGroups($where);
			$total = $this->getDataTotal('#__usergroups', $where);
		}

		// In case of users
		if ($type == 'users')
		{
			$where = ($searchkey) ? '(' . $db->quoteName('name') . ' LIKE ' . $db->quote('%' . $searchkey . '%') . ' OR ' .
				$db->quoteName('username') . ' LIKE ' . $db->quote('%' . $searchkey . '%') . ')' : '';
			$data  = $this->getusers($where);
			$total = $this->getDataTotal('#__users', $where);
		}

		// Prepare response
		$output = array(
			'iTotalRecords'        => count($data),
			'iTotalDisplayRecords' => $total,
			'data'                 => $data
		);

		return $output;
	}

	/**
	 * Get User Groups
	 *
	 * @param   string  $where  Search query
	 *
	 * @return  mixed
	 * @since   3.0
	 * @throws  Exception
	 */
	public function getGroups($where)
	{
		$app   = Factory::getApplication();
		$limit = $app->input->getInt('length', 10);
		$start = $app->input->getInt('start', 0);

		// Get Groups
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('a.id, a.title, COUNT(DISTINCT b.id) AS level')
			->from('#__usergroups AS a')
			->join('LEFT', $db->quoteName('#__usergroups') . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt')
			->group(array('a.id, a.title, a.lft, a.rgt, a.parent_id'))
			->order('a.lft ASC');

		// Filter on search
		if ($where)
		{
			$query->where($where);
		}

		try
		{
			$groups = $db->setQuery($query, $start, $limit)->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			throw new Exception($e->getMessage(), 500, $e);
		}

		// Prepare display of data
		foreach ($groups as $group)
		{
			$link         = Route::_('index.php?option=com_pwtacl&view=assets&type=group&group=' . $group->id);
			$group->title = str_repeat('<span class="gi">|&mdash;</span>', $group->level) . '<a href="' . $link . '">' . $group->title . '</a>';
		}

		return $groups;
	}

	/**
	 * Get User Groups
	 *
	 * @param   string  $where  Search query
	 *
	 * @return  mixed
	 * @since   3.0
	 * @throws  Exception
	 */
	public function getUsers($where)
	{
		$app   = Factory::getApplication();
		$limit = $app->input->getInt('length', 10);
		$start = $app->input->getInt('start', 0);

		// Get Users
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('a.id, a.name, a.username')
			->from('#__users AS a')
			->order('name ASC');

		// Filter on search
		if ($where)
		{
			$query->where($where);
		}

		try
		{
			$users = $db->setQuery($query, $start, $limit)->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			throw new Exception($e->getMessage(), 500, $e);
		}

		// Prepare display of data
		foreach ($users as $user)
		{
			$link       = Route::_('index.php?option=com_pwtacl&view=assets&type=user&user=' . $user->id);
			$user->name = '<a href="' . $link . '">' . $user->name . '</a>';
		}

		return $users;
	}

	/**
	 * Get total items for table
	 *
	 * @param   string  $table  Table name
	 * @param   string  $where  Search query
	 *
	 * @return  integer
	 * @since   3.0
	 * @throws  Exception
	 */
	public function getDataTotal($table, $where)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('COUNT(id)')
			->from($table . ' AS a');

		if ($where)
		{
			$query->where($where);
		}

		try
		{
			$total = $db->setQuery($query)->loadResult();
		}
		catch (RuntimeException $e)
		{
			throw new Exception($e->getMessage(), 500, $e);
		}

		return (int) $total;
	}
}
