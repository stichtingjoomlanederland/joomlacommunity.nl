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

require_once dirname( __FILE__ ) . '/model.php';

class EasyDiscussModelCustomFields extends EasyDiscussAdminModel
{
	/**
	 * Category total
	 *
	 * @var integer
	 */
	protected $_total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	protected $_pagination = null;

	/**
	 * Category data array
	 *
	 * @var array
	 */
	protected $_data = null;


	public function __construct()
	{
		parent::__construct();

		$limit		= $this->app->getUserStateFromRequest( 'com_easydiscuss.customs.limit', 'limit', $this->app->getCfg('list_limit'), 'int');
		$limitstart	= $this->input->get('limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Method to get the total nr of the categories
	 *
	 * @access public
	 * @return integer
	 */
	public function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return integer
	 */
	public function getPagination()
	{
		if (empty($this->_pagination)) {
			$this->_pagination = ED::getPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	/**
	 * Method to build the query for the customs
	 *
	 * @access private
	 * @return string
	 */
	protected function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildQueryWhere();
		$orderby	= $this->_buildQueryOrderBy();
		$db			= DiscussHelper::getDBO();

		$query	= 'SELECT a.* FROM `#__discuss_customfields` AS a '
				. $where . ' '
				. $orderby;

		return $query;
	}

	protected function _buildQueryWhere()
	{
		$mainframe		= JFactory::getApplication();
		$db				= DiscussHelper::getDBO();

		$filter_state	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.customs.filter_state', 'filter_state', '', 'word' );
		$search = $mainframe->getUserStateFromRequest('com_easydiscuss.fields.search', 'search', '', 'string');
		$search = $db->getEscaped(trim(JString::strtolower($search)));

		$where = array();

		if ( $filter_state )
		{
			if ( $filter_state == 'P' )
			{
				$where[] = $db->nameQuote( 'a.published' ) . '=' . $db->Quote( '1' );
			}
			else if ($filter_state == 'U' )
			{
				$where[] = $db->nameQuote( 'a.published' ) . '=' . $db->Quote( '0' );
			}
		}

		if ($search) {
			$where[] = ' LOWER(' . $db->nameQuote('title') . ') LIKE \'%' . $search . '%\' ';
		}

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}

	protected function _buildQueryOrderBy()
	{
		$mainframe			= JFactory::getApplication();

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.customs.filter_order', 		'filter_order', 	'a.ordering', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.customs.filter_order_Dir',	'filter_order_Dir',		'', 'word' );

		$orderby 			= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}

	/**
	 * Method to get categories item data
	 *
	 * @access public
	 * @return array
	 */
	public function getData( $usePagination = true)
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();

			if($usePagination)
				$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			else
				$this->_data = $this->_getList($query);
		}

		return $this->_data;
	}

	/**
	 * Method to publish or unpublish customs
	 *
	 * @access public
	 * @return array
	 */
	public function publishFields($customs = array(), $publish = 1)
	{
		if( count( $customs ) > 0 )
		{
			$db		= DiscussHelper::getDBO();

			$ids	= implode( ',' , $customs );

			$query	= 'UPDATE ' . $db->nameQuote( '#__discuss_customfields' ) . ' '
					. 'SET ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( $publish ) . ' '
					. 'WHERE ' . $db->nameQuote( 'id' ) . ' IN (' . $ids . ')';
			$db->setQuery( $query );

			if( !$db->query() )
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			return true;
		}
		return false;
	}

	public function sortDescending($a, $b)
	{
		// Descending sort based on the object property "ordering"
		return ($a->ordering < $b->ordering) ? 1 : -1;
	}

	/**
	 * Retrieves a list of a custom fields for a specific post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getFields($aclId = DISCUSS_CUSTOMFIELDS_ACL_INPUT, $operation = null, $post_id = null)
	{
		static $_cache = array();

		$idx = $aclId . '-' . $operation . '-' . $post_id;

		if (isset($_cache[$idx])) {
			return $_cache[$idx];
		}

		$db = $this->db;

		// This determines which fields can the user see.
		$my = JFactory::getUser();
		$groups = ED::getUserGroupId($my);

		// Determine the section of the custom fields. By default the operation should be question. 
		$post = ED::post($post_id);
		$section = ($post->isQuestion() && $operation != 'replying') ? DISCUSS_CUSTOMFIELDS_SECTION_QUESTIONS : DISCUSS_CUSTOMFIELDS_SECTION_REPLIES;

		if (!$groups) {
			return false;
		}

		// Determine whether the current post have custom field, if given post_id
		$noCustomFields = 0;
		if ($post_id) {
			$sql = 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__discuss_customfields_value') . ' WHERE `post_id` = ' . $db->Quote($post_id);
			$db->setQuery($sql);
			$noCustomFields = $db->loadResult();
		}

		$query = array();
		$query[] = 'SELECT a.*, ' . $db->Quote($aclId) . ' AS `acl_id`';
		$query[] = 'FROM ' . $db->nameQuote('#__discuss_customfields') . ' AS a';
		$query[] = 'WHERE a.`published` = ' . $db->Quote('1');
			$query[] = 'AND 1 <= (';
			$query[] = 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__discuss_customfields_rule') . ' AS b';
			$query[] = 'WHERE b.`field_id` = a.`id`';
			$query[] = 'AND b.`content_type` = ' . $db->Quote('group');
			$query[] = 'AND b.`acl_id` = ' . $db->Quote($aclId);
			$query[] = 'AND b.`content_id` IN (' . implode(',', $groups) . ')';
			$query[] = ')';

		// Determine is this a question from "reply branch".
		if ($post_id != NULL && $noCustomFields) {
			$query[] = 'AND 1 <= (';
			$query[] = 'SELECT COUNT(1) AS `count` FROM ' . $db->nameQuote('#__discuss_customfields_value') . ' AS c';
			$query[] = 'WHERE c.`field_id` = a.`id`';
			$query[] = 'AND c.`value` IS NOT NULL';
			$query[] = 'AND c.`post_id` = ' . $db->Quote($post_id);
			$query[] = ')';
		} else {
			// Else this is a normal customfield.
			$query[] = 'AND a.`section` = ' . $db->Quote($section);
		}

		$query[] = 'ORDER BY a.`ordering`';

		$query = implode(' ', $query);

		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (!$result) {
			$_cache[$idx] = array();
			return $result;
		}

		$fields = array();

		foreach ($result as $row) {
			$field = ED::field($row);

			$fields[] = $field;
		}

		$_cache[$idx] = $fields;
		return $fields;
	}

	/**
	 * Returns a list of viewable custom fields from the current viewer
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getViewableFields($postId, $userId = null)
	{
		$db = $this->db;
		$user = JFactory::getUser($userId);

		// Get a list of user groups for this user
		$groups = ED::getUserGroupId($user);

		if (!$groups) {
			return array();
		}

		$query = 'SELECT a.*,'
				. ' b.' . $db->qn('field_id') . ', b.' . $db->qn('acl_id') . ', b.' . $db->qn('content_id') . ','
				. ' b.' . $db->qn('content_type') . ', b.' . $db->qn('status') . ','
				. ' c.' . $db->qn('field_id') . ', c.' . $db->qn('value') . ', c.' . $db->qn('post_id')
				. ' FROM ' . $db->qn('#__discuss_customfields') . ' a'
				. ' LEFT JOIN ' . $db->qn('#__discuss_customfields_rule') . ' b'
				. ' ON a.' . $db->qn('id') . '=' . 'b.' . $db->qn('field_id')
				. ' LEFT JOIN ' . $db->qn('#__discuss_customfields_value') . ' c'
				. ' ON a.' . $db->qn('id') . '=' . 'c.' . $db->qn('field_id')
				. ' AND c.' . $db->qn('post_id') . '=' . $db->Quote($postId)
				. ' WHERE a.' . $db->qn('published') . '=' . $db->Quote('1')
				. ' AND b.' . $db->qn('content_type') . '=' . $db->Quote('group')
				. ' AND b.' . $db->qn('acl_id') . '=' . $db->Quote(DISCUSS_CUSTOMFIELDS_ACL_VIEW);

		// Prepare the group clause
		$query .= ' AND b.' . $db->qn('content_id') . ' IN(';
		foreach ($groups as $groupId) {
			$query .= $db->Quote($groupId);

			if (next($groups) !== false) {
				$query .= ',';
			}
		}
		$query .= ')';
		$query .= ' GROUP BY a.' . $db->qn('id');

		$query .= ' ORDER BY a.' . $db->qn('ordering');

		// // Debug
		// echo str_ireplace('#__', 'jos_', $query);exit;

		$db->setQuery($query);

		$result = $db->loadObjectList();
		return $result;
	}

	public function setNewFields( $aclId )
	{
		$results = $this->getNewFields( $aclId );
		return $results;
	}

	/**
	 * Get a list of assigned acl for a custom field
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAssignedGroups($fieldId, $action = 'view')
	{
		$db = $this->db;

		$query = 'SELECT'
				. ' a.' . $db->qn('content_id')
				. ' FROM ' . $db->qn('#__discuss_customfields_rule') . ' AS a'
				. ' LEFT JOIN ' . $db->qn('#__discuss_customfields_acl') . ' AS b'
				. ' ON a.' . $db->qn('acl_id') . '=' . 'b.' . $db->qn('id')
				. ' WHERE a.' . $db->qn('field_id') . '=' . $db->Quote($fieldId)
				. ' AND a.' . $db->qn('content_type') . '=' . $db->Quote('group')
				. ' AND b.' . $db->qn('action') . '=' . $db->Quote($action);
		$db->setQuery($query);
		$result = $db->loadColumn();

		if (!$result) {
			return $result;
		}

		return $result;
	}

	/**
	 * Maps the acl rules
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function mapRules($assignments, $action = 'view')
	{
		$db = $this->db;

		// Get available rules
		$rules = $this->getAvailableAclRules($action);

		if (!$rules) {
			return array();
		}

		// Get a list of Joomla user groups
		$groups = ED::getJoomlaUserGroups();

		// Default acl
		$acl = array();

		$ruleId = $rule->id;
		$default = $rule->default;

		// Default items
		$acl[$action] = array();

		// If there is an assignment, we need to know which groups

		foreach ($groups as $group) {

			$groupId = $group->id;

			$totalAssignments = count($assignments);

			$item = new stdClass();

			if (!$assignments) {
				$item->status = $default;
				$item->acl_id = $rule->id;
				$item->groupname = $group->name;
				$item->groupid = $group->id;

				continue;
			}

			foreach ($assignments as $assignment) {
				$item->status = $assignment->status;
				$item->acl_id = $rule->id;
				// $item->groupname = $joomla->name;
				$item->groupid = $assignment->content_id;
			}

			$acl[$action][$group->id] = $item;
		}


		return $acl;

	}

	/**
	 * Retrieves the acl for users
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAclUsers($aclUsers)
	{
		$users  = array();

		foreach( $aclUsers as $item)
		{
			$users[] = $item->content_id;
		}

		$userlist   = '';

		foreach($users as $user)
		{
			$userlist .= ( $userlist == '') ? $db->Quote($user) : ', ' . $db->Quote($user);
		}


		$query  = 'SELECT '
				. $db->nameQuote( 'id') . ', ' . $db->nameQuote( 'name' )
				. ' FROM ' . $db->nameQuote( '#__users' )
				. ' WHERE ' . $db->nameQuote( 'id' )
				. ' IN (' . $userlist . ')';
		$db->setQuery($query);

		$result = $db->loadObjectList();

		return $result;
	}

	public function getNewFields( $aclId = null )
	{
		static $loaded = array();

		$sig    = (int) $aclId;

		if( ! isset( $loaded[ $sig ] ) )
		{
			$db = DiscussHelper::getDBO();
			$my = JFactory::getUser();

			$myUserGroups = (array) DiscussHelper::getUserGroupId($my);

			if( empty($myUserGroups) )
			{
				$loaded[ $sig ] = array();
			}
			else
			{
				$query = 'SELECT a.*, b.`acl_id`'
						. ' FROM ' . $db->nameQuote( '#__discuss_customfields' ) . ' AS a'
						. ' LEFT JOIN ' . $db->nameQuote( '#__discuss_customfields_rule' ) . ' AS b'
						. ' ON a.' . $db->nameQuote( 'id' ) . ' = ' . 'b.'  . $db->nameQuote( 'field_id' )
						. ' WHERE a.' . $db->nameQuote( 'published' ) . ' = ' . $db->Quote( '1' )
						. ' AND b.' . $db->nameQuote( 'acl_id' ) . ' = ' . $db->Quote( $aclId );

				$userQuery = $query;
				$userQuery .= ' AND b.' . $db->nameQuote( 'content_type' ) . ' = ' . $db->Quote( 'user' );
				$userQuery .= ' AND b.' . $db->nameQuote( 'content_id' ) . ' = ' . $db->Quote( $my->id );

				$groupQuery = $query;
				$groupQuery .= ' AND b.' . $db->nameQuote( 'content_type' ) . ' = ' . $db->Quote( 'group' );

				if( count($myUserGroups) == 1 )
				{
					$gid    = array_pop($myUserGroups);
					$groupQuery .= ' AND b.' . $db->nameQuote( 'content_id' ) . ' = ' . $db->Quote( $gid );
				}
				else
				{
					$groupQuery .= ' AND b.' . $db->nameQuote( 'content_id' ) . ' IN(' . implode( ', ', $myUserGroups ) . ')';
				}

				$masterQuery    = $userQuery;
				$masterQuery    .= ' UNION ';
				$masterQuery    .= $groupQuery;

				$db->setQuery( $masterQuery );
				$result = $db->loadObjectList();

				$loaded[ $sig ] = $result;
			}
		}

		return $loaded[ $sig ];

	}

	public function checkMyFields( $postId, $aclId )
	{
		$db = DiscussHelper::getDBO();
		$my = JFactory::getUser();

		// GET MY VALUE
		$myResults = $this->getAllFields( $postId, $aclId );
		return $myResults;
	}

	/**
	 * Retrieves a value of a custom field for a post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getFieldValue($fieldId, $postId)
	{
		$db = $this->db;
		$query = 'SELECT ' . $db->qn('value') . ' FROM ' . $db->qn('#__discuss_customfields_value');
		$query .= ' WHERE ' . $db->qn('field_id') . '=' . $db->Quote($fieldId);
		$query .= ' AND ' . $db->qn('post_id') . '=' . $db->Quote($postId);

		$db->setQuery($query);
		$value = $db->loadResult();

		return $value;
	}

	public function getAllFields( $postId = null, $aclId = null )
	{
		if( $aclId == null || $postId == null )
		{
			return false;
		}

		static $loaded = array();

		$sig    = (int) $postId . '-' . (int) $aclId ;

		if( ! isset( $loaded[$sig] ) )
		{
			$my = JFactory::getUser();
			$db = DiscussHelper::getDBO();
			$myUserGroups = (array) DiscussHelper::getUserGroupId($my);

			if( empty($myUserGroups) )
			{
				$loaded[$sig]   = array();
			}
			else
			{
				$query = 'SELECT a.*,'
						. ' b.' . $db->nameQuote( 'field_id' ) . ', b.' . $db->nameQuote( 'acl_id' ) . ', b.' . $db->nameQuote( 'content_id' ) . ','
						. ' b.' . $db->nameQuote( 'content_type' ) . ', b.' . $db->nameQuote( 'status' ) . ','
						. ' c.' . $db->nameQuote( 'field_id' ) . ', c.' . $db->nameQuote( 'value' ) . ', c.' . $db->nameQuote( 'post_id' )
						. ' FROM ' . $db->nameQuote( '#__discuss_customfields' ) . ' a'
						. ' LEFT JOIN ' . $db->nameQuote( '#__discuss_customfields_rule' ) . ' b'
						. ' ON a.' . $db->nameQuote( 'id' ) . '=' . 'b.' . $db->nameQuote( 'field_id' )
						. ' LEFT JOIN ' . $db->nameQuote( '#__discuss_customfields_value' ) . ' c'
						. ' ON a.' . $db->nameQuote( 'id' ) . '=' . 'c.' . $db->nameQuote( 'field_id' )
						. ' AND c.' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $postId );


				$userQuery  = $query;
				$userQuery .= ' WHERE a.' . $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' );
				$userQuery .= ' AND b.' . $db->nameQuote( 'content_type' ) . '=' . $db->Quote( 'user' );
				$userQuery .= ' AND b.' . $db->nameQuote( 'acl_id' ) . '=' . $db->Quote( $aclId );
				$userQuery .= ' AND b.' . $db->nameQuote( 'content_id' ) . '=' . $db->Quote( $my->id );

				$groupQuery  = $query;
				$groupQuery .= ' WHERE a.' . $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' );
				$groupQuery .= ' AND b.' . $db->nameQuote( 'content_type' ) . '=' . $db->Quote( 'group' );
				$groupQuery .= ' AND b.' . $db->nameQuote( 'acl_id' ) . '=' . $db->Quote( $aclId );
				if( count( $myUserGroups ) == 1 )
				{
					$gid    = array_pop( $myUserGroups );
					$groupQuery .= ' AND b.' . $db->nameQuote( 'content_id' ) . ' = ' . $db->Quote( $gid );
				}
				else
				{
					$groupQuery .= ' AND b.' . $db->nameQuote( 'content_id' ) . ' IN(' . implode( ', ', $myUserGroups ) . ')';
				}


				$masterQuery    = $userQuery;
				$masterQuery    .= ' UNION ';
				$masterQuery    .= $groupQuery;

				$db->setQuery( $masterQuery );
				$result = $db->loadObjectList();

				// @user with multiple group will generate duplicate result, hence we remove it
				if( !empty($result) )
				{
					$myFinalResults = array();

					// Remove dupes records which have no values
					foreach ($result as $item)
					{
						if ( !array_key_exists($item->id, $myFinalResults) )
						{
							$myFinalResults[$item->id] = $item;
						}
						else
						{
							if( !empty($item->id) )
							{
								// If the pending item have value, replace the existing record
								$myFinalResults[$item->id] = $item;
							}
						}
					}
					$result = $myFinalResults;
				}



				$loaded[$sig]   = $result;
			}
		}

		return $loaded[$sig];
	}

	/**
	 * Retrieves all ACL rules available for custom fields
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAvailableAclRules($action = null)
	{
		$db = $this->db;

		$query = 'SELECT * FROM ' . $db->nameQuote('#__discuss_customfields_acl')
				. ' WHERE ' . $db->nameQuote('acl_published') . '=' . $db->Quote('1');

		if ($action) {
			$query .= ' AND ' . $db->qn('action') . '=' . $db->Quote($action);
		}

		$db->setQuery($query);
		$results = $db->loadObjectList();

		if (!$results) {
			return false;
		}

		return $results;
	}

	/**
	 * Deletes all acl for a field
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteCustomFieldsAcl($fieldId)
	{
		$db = $this->db;

		$query = 'DELETE FROM ' . $db->qn('#__discuss_customfields_rule')
				. ' WHERE ' . $db->nameQuote('field_id') . '=' . $db->quote($fieldId);
		$db->setQuery($query);

		return $db->query();
	}

	/**
	 * Saves custom field's acls
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function saveCustomFieldRule($fieldId, $data)
	{
		$db = $this->db;

		// Get custom fields acl
		$rules = $this->getAvailableAclRules();

		// We perform FIFO (first in first out) for acl.
		// Delete all existing acl rules first
		$this->deleteCustomFieldsAcl($fieldId);

		// If nobody assign any permission in the permission tab, default everyone to true.
		$setAllDefault = true;

		foreach ($rules as $rule) {

			$key = 'acl_group_' . $rule->action;

			// Check if the given data is provided.
			if (!isset($data[$key])) {
				continue;
			}

			$groups = $data[$key];

			foreach ($groups as $groupId) {

				$table = ED::table('CustomFieldsRule');
				$table->field_id = $fieldId;
				$table->acl_id = $rule->id;
				$table->content_id = $groupId;
				$table->content_type = 'group';
				$table->status = 1;

				$table->store();

				// If there is at least one permission set, we shouldn't be setting any default values
				$setAllDefault = false;
			}
		}

		if (!$setAllDefault) {
			return true;
		}

		// If user didn't set any groups, we should set everything to be enabled by default.
		foreach ($rules as $rule) {

			$groups = ED::getJoomlaUserGroups();

			foreach ($groups as $group) {

				$table = ED::table('CustomFieldsRule');
				$table->field_id = $fieldId;
				$table->acl_id = $rule->id;
				$table->content_id = $group->id;
				$table->content_type = 'group';
				$table->status = 1;

				$table->store();
			}
		}

		return true;
	}

	/**
	 * Deletes all custom fields value for a particular post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteCustomFieldsValue($postId, $type = 'post')
	{
		$db = $this->db;

		$query = 'DELETE';

		// Delete the particular post's custom field's value when the associate post is deleted.
		if ($type == 'post') {
			$query .= ' FROM ' . $db->qn('#__discuss_customfields_value')
					. ' WHERE ' . $db->qn('post_id') . '=' . $db->Quote($postId);
		}

		// Delete all custom field's value of that particular field.
		if ($type == 'field') {
			$query .= ' FROM ' . $db->qn('#__discuss_customfields_value')
					. ' WHERE ' . $db->qn('field_id') . '=' . $db->Quote($postId);
		}

		// If edit post, when certain custom fields is unpublish, we don't want to delete the unpublish because what if the user publish it back? unless he want to delete post
		// Delete published only
		if ($type == 'update') {
			$query .= ' a.*'
					. ' FROM ' . $db->qn('#__discuss_customfields_value') . ' a'
					. ' LEFT JOIN ' . $db->qn('#__discuss_customfields') . ' b'
					. ' ON a.' . $db->qn('field_id') . '=' . 'b.' . $db->qn('id')
					. ' WHERE a.' . $db->qn('post_id') . '=' . $db->Quote($postId)
					. ' AND b.' . $db->qn('published') . '=' . $db->Quote('1');

		}

		$state = $db->setQuery($query);

		if (!$db->query()) {
			return false;
		}

		return true;
	}

	public function deleteCustomFieldsRule( $id )
	{
		if( !$id )
		{
			return false;
		}

		$db = DiscussHelper::getDBO();

		$query = 'DELETE FROM ' . $db->nameQuote( '#__discuss_customfields_rule' )
				. ' WHERE ' . $db->nameQuote( 'field_id' ) . '=' . $db->quote( $id );

		$db->setQuery( $query );

		if( !$db->query() )
		{
			return false;
		}

		return true;
	}

	/**
	 * Rebuilds the ordering of the custom fields
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function rebuildOrdering()
	{
		$db = $this->db;

		$query	= 'SELECT ' . $db->nameQuote( 'id' ) . ', ' . $db->nameQuote( 'ordering' )
				. ' FROM ' .  $db->nameQuote( '#__discuss_customfields' )
				. ' ORDER BY ' . $db->nameQuote( 'ordering' ) . ', ' . $db->nameQuote( 'id' ) . ' DESC';
		$db->setQuery($query);

		$rows = $db->loadObjectList();

		foreach ($rows as $i => $row) {
			$order	= $i + 1;
			$query	= 'UPDATE ' . $db->nameQuote( '#__discuss_customfields' )
					. ' SET ' . $db->nameQuote( 'ordering' ) . '=' . $db->Quote( $order )
					. ' WHERE ' . $db->nameQuote( 'id' ) .  '=' . $db->Quote( $row->id );
			$db->setQuery($query);

			if (!$db->query()) {
				return false;
			}
		}

		return true;
	}
}
