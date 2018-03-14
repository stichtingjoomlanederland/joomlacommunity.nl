<?php
/**
 * @package		ACL Manager for Joomla
 * @copyright 	Copyright (c) 2011-2013 Sander Potjer
 * @license 	GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

// Load framework base classes
jimport('joomla.application.component.modellist');

/**
 * Aclmanager Model
 */
class AclmanagerModelHome extends JModelList
{

	/**
	 * Limit data.
	 */
	public function dtLimit()
	{
		$limit = "";
		if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
		{
			$limit = "LIMIT ".intval( $_GET['iDisplayStart'] ).", ".
				intval( $_GET['iDisplayLength'] );
		}
		return $limit;
	}

	/**
	 * Order data.
	 */
	public function dtOrder($columns)
	{
		$order = "";
		if ( isset( $_GET['iSortCol_0'] ) )
		{
			$order = "";
			for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
			{
				if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
				{
					$order .= "`".$columns[ intval( $_GET['iSortCol_'.$i] ) ]."` ".
						($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
				}
			}

			$order = substr_replace( $order, "", -2 );
			if ( $order == "ORDER BY" )
			{
				$order = "";
			}
		}
		return $order;
	}

	/**
	 * Filter data.
	 */
	public function dtWhere($columns)
	{
		$db	= JFactory::getDBO();
		$where = "";
		if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
		{
			$where = "";
			for ( $i=0 ; $i<count($columns) ; $i++ )
			{
				$where .= "`".$columns[$i]."` LIKE '%".$db->escape( $_GET['sSearch'] )."%' OR ";
			}
			$where = substr_replace( $where, "", -3 );
		}
		return $where;
	}

	/**
	 * Get Table data.
	 */
	public function getTableData()
	{
		// Get the database object and a new query object.
		$type	= JFactory::getApplication()->input->get('type');
		$group	= JFactory::getApplication()->input->get('group');
		$user	= JFactory::getApplication()->input->get('user');
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);

		if($type == 'user'){
			$columns = array( 'name', 'username', 'id');
			$tabel = '#__users';
			$index = 'id';
			$select = 'name, username, id';
			$order = $this->dtOrder($columns);
			$where = $this->dtWhere($columns);
		} elseif ($type == 'group'){
			$columns = array( 'title', 'id');
			$tabel = '#__usergroups AS a';
			$index = 'id';
			$select = 'a.title AS title, a.id AS id, COUNT(DISTINCT b.id) AS level';
			$order = 'a.lft ASC';
			$where = 'a.title LIKE \'%'.$db->escape($_GET['sSearch']).'%\'';
		}

		// Build the query.
		$query->select($select);
		$query->from($tabel);
		if ($type == 'group'){
			$query->join('LEFT', $db->quoteName('#__usergroups') . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt');
			$query->group('a.id, a.title, a.lft, a.rgt, a.parent_id');
		}

		// Get Users of Group
		if($group) {
			$acl		= JFactory::getACL();
			$groupusers = $acl->getUsersByGroup($group,false);
			if($groupusers) {
				$query->where('id IN (' . implode(',', array_map('intval', $groupusers)) . ')');
			} else {
				$query->where('id IN (0)');
			}
		}

		// Get Groups of User
		if($user) {
			$acl		= JFactory::getACL();
			$usergroups	= $acl->getGroupsByUser($user,false);
			if($usergroups) {
				$query->where('a.id IN (' . implode(',', array_map('intval', $usergroups)) . ')');
			} else {
				$query->where('a.id IN (0)');
			}
		}

		if($where) {
			$query->where('('.$where.')');
		}

		$query->order($order .' '. $this->dtLimit());
		$db->setQuery($query);
		$data = $db->loadRowList();

		/* Data set length after filtering */
		$query	= $db->getQuery(true);
		$query->select('COUNT('.$index.')');
		$query->from($tabel);
		if($where) {
			$query->where($where);
		}
		$db->setQuery($query);
		$filteredtotal = $db->loadResult();

		/* Total data set length */
		$query	= $db->getQuery(true);
		$query->select('COUNT('.$index.')');
		$query->from($tabel);
		$db->setQuery($query);
		$total = $db->loadResult();

		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $total,
			"iTotalDisplayRecords" => $filteredtotal,
			"data" => $data
		);

		return $output;
	}
}