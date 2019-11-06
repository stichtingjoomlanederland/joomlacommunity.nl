<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/model.php');

class EasyDiscussModelCategory extends EasyDiscussAdminModel
{
	protected $_total = null;
	protected $_pagination = null;
	protected $_data = null;

	public function __construct()
	{
		parent::__construct();

		$limit = ($this->app->getCfg('list_limit') == 0) ? 5 : DiscussHelper::getListLimit();
		$limitstart = $this->input->get('limitstart', '0', 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}


	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return integer
	 */
	public function getPagination()
	{
		return $this->_pagination;
	}

	protected function _getParentIdsWithPost()
	{
		$db	= DiscussHelper::getDBO();
		$my = JFactory::getUser();

		$query	= 'select * from `#__discuss_category`';
		$query	.= ' where `published` = 1';
		$query	.= ' and `parent_id` = 0';
		if($my->id == 0)
		{
			$query	.= ' and `private` = 0';
		}


		$db->setQuery($query);
		$result = $db->loadObjectList();


		$validCat	= array();

		if(count($result) > 0)
		{
			for($i = 0; $i < count($result); $i++)
			{
				$item =& $result[$i];

				$item->childs = null;
				DiscussHelper::buildNestedCategories($item->id, $item);

				$catIds		= array();
				$catIds[]	= $item->id;
				DiscussHelper::accessNestedCategoriesId($item, $catIds);

				$item->cnt	= $this->getTotalPostCount($catIds);

				if($item->cnt > 0)
				{
					$validCat[] = $item->id;
				}

			}
		}

		return $validCat;
	}

	/**
	 * Deletes all acl mapping for a category
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function deleteACLMapping($categoryId)
	{
		$db = $this->db;

		$query	= 'DELETE FROM `#__discuss_category_acl_map`'
				. ' WHERE `category_id` = ' . $db->quote($categoryId)
				. ' AND `type` = ' . $db->quote( 'group' );
		$db->setQuery($query);

		return $db->query();
	}

	/**
	 * Delete a particular acl for a category
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function deleteACL($categoryId, $aclId = '')
	{
		$db = $this->db;

		$query = 'delete from `#__discuss_category_acl_map`';
		$query .= ' where `category_id` = ' . $db->Quote($categoryId);

		if ($aclId) {
			$query	.= ' and `acl_id` = ' . $db->Quote($aclId);
		}

		$db->setQuery($query);
		$db->query();

		return true;
	}

	/**
	 * Deletes a category avatar
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteAvatar(EasyDiscussCategory $category)
	{
		$config = ED::config();

		$avatar = $category->avatar;

		if ($avatar != 'cdefault.png' && !empty($avatar)) {

			$avatar_config_path	= $config->get('main_categoryavatarpath');
			$avatar_config_path	= rtrim($avatar_config_path, '/');
			$avatar_config_path	= JString::str_ireplace('/', DIRECTORY_SEPARATOR, $avatar_config_path);

			$upload_path = JPATH_ROOT . '/' . $avatar_config_path;

			$target_file_path = $upload_path;
			$target_file = JPath::clean($target_file_path . '/' . $avatar);

			$exists = JFile::exists($target_file);

			// File doesn't exists
			if (!$exists) {
				return false;
			}

			// Try to delete the file
			$state = JFile::delete($target_file);

			if (!$state) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Retrieves the default category
	 *
	 * @since	4.0.16
	 * @access	public
	 */
	public function getDefaultCategory()
	{
		$db 	= DiscussHelper::getDBO();

		$query 	= 'SELECT * FROM ' . $db->nameQuote( '#__discuss_category' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'default' ) . '=' . $db->Quote( 1 );
		$db->setQuery($query);
		$result = $db->loadObject();

		if( !$result )
		{
			return false;
		}

		$category 	= DiscussHelper::getTable( 'Category' );
		$category->bind( $result );

		return $category;
	}

	public function getCategories($sort = 'latest', $hideEmptyPost = true, $limit = 0)
	{
		$db	= DiscussHelper::getDBO();

		//blog privacy setting
		$my = JFactory::getUser();

		$orderBy	= '';
		$limit		= ($limit == 0) ? $this->getState('limit') : $limit;
		$limitstart	= $this->getState('limitstart');
		$limitSQL	= ' LIMIT ' . $limitstart . ',' . $limit;

		$andWhere	= array();

		$andWhere[]	= ' a.`published` = 1';
		$andWhere[]	= ' a.`parent_id` = 0';
		if($my->id == 0)
			$andWhere[]	= ' a.`private` = 0';

		if($hideEmptyPost)
		{
			$arrParentIds	= $this->_getParentIdsWithPost();

			if(! empty($arrParentIds))
			{
				$tmpParentId	= implode(',', $arrParentIds);
				$andWhere[]		= ' a.`id` IN (' . $tmpParentId . ')';
			}

			if($my->id == 0)
				$andWhere[]	= ' a.`private` = 0';

			$this->_total	= count($arrParentIds);
		}
		else
		{
			$extra	= ( count( $andWhere ) ? ' WHERE ' . implode( ' AND ', $andWhere ) : '' );

			$query	= 'SELECT a.`id` FROM ' . $db->nameQuote( '#__discuss_category' ) . ' AS a';
			$query	.= ' LEFT JOIN '. $db->nameQuote( '#__discuss_posts' ) . ' AS b';
			$query	.= ' ON a.`id` = b.`category_id`';
			$query	.= ' AND b.`published` = ' . $db->Quote('1');

			$query	.= $extra;
			$query	.= ' GROUP BY a.`id`';

			$db->setQuery( $query );
			$result	= $db->loadResultArray();

			$this->_total	= count($result);

			if($db->getErrorNum())
			{
				JError::raiseError( 500, $db->stderr());
			}
		}

		if( empty($this->_pagination) )
		{
			jimport('joomla.html.pagination');
			$this->_pagination	= new JPagination( $this->_total , $limitstart , $limit);
		}

		$extra 		= ( count( $andWhere ) ? ' WHERE ' . implode( ' AND ', $andWhere ) : '' );

		$query	= 'SELECT a.`id`, a.`title`, a.`alias`, COUNT(b.`id`) AS `cnt`, a.`description`';
		$query	.= ' FROM ' . $db->nameQuote( '#__discuss_category' ) . ' AS `a`';
		$query	.= ' LEFT JOIN '. $db->nameQuote( '#__discuss_posts' ) . ' AS b';
		$query	.= ' ON a.`id` = b.`category_id`';
		$query	.= ' AND b.`published` = ' . $db->Quote('1');
		$query	.= $extra;
		$query	.= ' GROUP BY a.`id`';

		switch($sort)
		{
			case 'popular' :
				$orderBy	= ' ORDER BY `cnt` DESC';
				break;
			case 'alphabet' :
				$orderBy = ' ORDER BY a.`title` ASC';
				break;
			case 'ordering' :
				$orderBy = ' ORDER BY a.`ordering` ASC';
				break;
			case 'latest' :
			default	:
				$orderBy = ' ORDER BY a.`created` DESC';
				break;
		}
		$query	.= $orderBy;
		$query	.= $limitSQL;

		$db->setQuery( $query );
		$result	= $db->loadObjectList();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}

		return $result;

	}


	/**
	 * Retrieves total number of posts from a category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalPosts($categoryId)
	{
		static $_cache = array();

		$options = array();
		$options['idOnly'] = true;

		if ($this->app->isAdmin()) {
			$options['ignorePermission'] = true;
		}

		if (! isset($_cache[$categoryId])) {

			$db = $this->db;

			$model = ED::model('Categories');
			$children = $model->getCategoriesTree($categoryId, $options);

			if ($children) {
				$query = "select count(1) from `#__discuss_thread` as a";

				// exclude blocked users posts
				$query .= " left join " . $db->nameQuote('#__users') . " as uu on a.`user_id` = uu.`id`";

				$query .= " where a.`published` = " . $db->Quote('1');

				// Check for private post
				if (!ED::isSiteAdmin() && !ED::isModerator()) {
					$query .= " AND (a.`private` = " . $db->Quote(0) . " OR (a.`private` = " . $db->Quote(1) . " AND a.`user_id` = " . $db->Quote(ED::user()->id) . "))";
				}

				$query .= " and a.`cluster_id` = " . $db->Quote(0);
				$query .= " and a.`category_id` in (" . implode(',', $children) . ")";

				$query .= " and (uu.`block` = 0 OR uu.`id` IS NULL)";

				$db->setQuery($query);
				$_cache[$categoryId] = $db->loadResult();
			} else {
				$_cache[$categoryId] = '0';
			}

		}

		return $_cache[$categoryId];
	}

	public function getTotalPostCount($catIds)
	{
		$db	= DiscussHelper::getDBO();

		//blog privacy setting
		$my = JFactory::getUser();

		$categoryId = '';
		$isIdArray  = false;

		if(is_array($catIds))
		{
			if(count($catIds) > 1)
			{
				$categoryId	= implode(',', $catIds);
				$isIdArray  = true;
			}
			else
			{
				$categoryId	= $catIds[0];
			}
		}
		else
		{
			$categoryId  = $catIds;
		}


		// $query	= 'SELECT COUNT(b.`id`) AS `cnt`';
		// $query	.= ' FROM ' . $db->nameQuote( '#__discuss_category' ) . ' AS `a`';
		// $query	.= ' LEFT JOIN '. $db->nameQuote( '#__discuss_posts' ) . ' AS b';
		// $query	.= ' ON a.`id` = b.`category_id`';
		// $query	.= ' AND b.`published` = ' . $db->Quote('1');
		// $query	.= ' WHERE a.`published` = 1';
		// $query	.= ($isIdArray) ? ' AND a.`id` IN (' . $categoryId. ')' :  ' AND a.`id` = ' . $db->Quote($categoryId);
		// $query	.= ' GROUP BY a.`id` HAVING (COUNT(b.`id`) > 0)';
		//

		$query = 'select count(1) from `#__discuss_posts` as a';
		$query .= ($isIdArray) ? ' WHERE a.`category_id` IN (' . $categoryId. ')' : ' where a.`category_id` = ' . $db->Quote($categoryId);
		$query .= ' and a.published = 1';

		$db->setQuery($query);
		$result = $db->loadResult();

		return ($result) ? $result : 0;
	}

	/**
	 * Retrieves a list of moderators for a category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getModerators($categoryId)
	{
		$db = $this->db;

		$moderators = array();

		// Get a list of user groups
		$groups = $this->getAssignedModerator($categoryId, 'group');

		if ($groups) {

			$query = 'select b.`id` from `#__users` as b inner join `#__user_usergroup_map` AS a on b.`id` = a.`user_id`';
			$gids = '';

			foreach ($groups as $group) {
				$gval = $db->Quote($group->content_id);
				$gids .= $gids ? ',' . $gval : $gval;
			}

			$query .= ' WHERE a.`group_id` IN (' . $gids . ')';
			$query .= ' AND b.`block` = 0';

			$db->setQuery($query);
			$result = $db->loadColumn();

			$moderators = array_merge($moderators, $result);
		}

		// Get a list of users assigned as moderator
		$users = $this->getAssignedModerator($categoryId, 'user');

		if ($users) {
			foreach ($users as $user) {
				$moderators[] = $user->content_id;
			}
		}

		return $moderators;
	}


	/**
	 * Updates a category ACL
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function updateACL($categoryId, $data, $action = null, $isInstaller = false)
	{
		$db = $this->db;

		$exclude = $isInstaller ? 'moderate' : '';

		// Get category acl
		$rules = $this->getAvailableAclRules($action, $exclude);

		// Delete acl mapping for this category first
		$this->deleteACL($categoryId);

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

				$table = ED::table('CategoryAclMap');
				$table->category_id = $categoryId;
				$table->acl_id = $rule->id;
				$table->content_id = $groupId;
				$table->type = 'group';
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
		// and for moderate, we only want to assign to super user.
		foreach ($rules as $rule) {

			$groups = ED::getJoomlaUserGroups();

			foreach ($groups as $group) {

				// we only want to assign moderation to super admin group.
				if ($rule->action == 'moderate' && $group->id != '8') {
					continue;
				}

				$table = ED::table('CategoryAclMap');
				$table->category_id = $categoryId;
				$table->acl_id = $rule->id;
				$table->content_id = $group->id;
				$table->type = 'group';
				$table->status = 1;

				$table->store();
			}
		}

		return true;

		// // $catRuleItems = ED::table('CategoryAclItem');
		// // $categoryRules = $catRuleItems->getAllRuleItems();

		// $itemtypes = array('group', 'user');

		// $added = 0;

		// foreach ($categoryRules as $rule) {

		// 	foreach ($itemtypes as $type) {

		// 		$key = 'acl_' . $type . '_' . $rule->action;

		// 		if (!isset($post[$key])) {
		// 			continue;
		// 		}

		// 		if (count($post[$key]) <= 0) {
		// 			continue;
		// 		}

		// 		foreach ($post[$key] as $contendid) {

		// 			$catRule = ED::table('CategoryAclMap');

		// 			$catRule->category_id = $categoryId;
		// 			$catRule->acl_id = $rule->id;
		// 			$catRule->type = $type;
		// 			$catRule->content_id = $contendid;
		// 			$catRule->status = '1';
		// 			$catRule->store();

		// 			$added++;
		// 		}
		// 	}
		// }

		// // If nothing is provided, we need to assign a default ACL for the category
		// if (!$added) {
		// 	$defaultKeys = array('1','2','3','4');
		// 	$joomlaGroups = ED::getJoomlaUserGroups();

		// 	foreach ($defaultKeys as $ruleId) {
		// 		foreach ($joomlaGroups as $joomlaGroup) {
		// 			$catRule = JTable::getInstance( 'CategoryAclMap' , 'Discuss' );

		// 			$catRule->category_id = $categoryId;
		// 			$catRule->acl_id = $ruleId;
		// 			$catRule->type = 'group';
		// 			$catRule->content_id = $joomlaGroup->id;
		// 			$catRule->status = '1';
		// 			$catRule->store();
		// 		}
		// 	}
		// }

		// return true;
	}

	/**
	 * Retrieves all ACL rules available for category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAvailableAclRules($action = null, $excludeActions = 'moderate')
	{
		$db = $this->db;

		$query = 'SELECT * FROM ' . $db->nameQuote('#__discuss_category_acl_item')
				. ' WHERE ' . $db->nameQuote('published') . '=' . $db->Quote('1');

		if ($action) {
			$query .= ' AND ' . $db->qn('action') . '=' . $db->Quote($action);
		}

		// Exclude moderate action if user didn't set any user group from the category permission tab
		if ($excludeActions) {
			$query .= ' AND ' . $db->qn('action') . ' NOT IN (' . $db->Quote($excludeActions) . ')';
		}

		$db->setQuery($query);
		$results = $db->loadObjectList();

		if (!$results) {
			return false;
		}

		return $results;
	}


	/**
	 * Retrieves list of assigned moderators to a particular category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAssignedModerator($categoryId, $type = 'group')
	{
		$db = $this->db;
		$acl = array();

		// By default we assume that the type is group
		$querySelect = ' b.title AS title';
		$queryJoin = ' LEFT JOIN `#__usergroups` AS b ON b.id = a.content_id';

		// For user type, we need to join with a different table
		if ($type == 'user') {
			$querySelect = ' b.name AS title';
			$queryJoin = ' LEFT JOIN `#__users` AS b ON b.id = a.content_id';
		}

		$query	= 'SELECT a.*, '
				. $querySelect
				. ' FROM `#__discuss_category_acl_map` AS a'
				. $queryJoin
				. ' WHERE a.`category_id` = ' . $db->quote($categoryId)
				. ' AND a.`acl_id` = ' . $db->quote(DISCUSS_CATEGORY_ACL_MODERATOR)
				. ' AND a.`type` = ' . $db->quote($type)
				. ' AND a.`status` = 1';

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Determines if a user can access a particular category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDisallowedCategories($userId = null, $aclType = DISCUSS_CATEGORY_ACL_ACTION_VIEW)
	{
		$db = $this->db;
		$user = JFactory::getUser($userId);

		static $result = array();

		$key = (int) $user->id . '-' . (int) $aclType;

		if (isset($result[$key])) {
			return $result[$key];
		}

		$excludeCats = array();

		// If the user is a guest we only want to display public categories
		if (!$user->id) {

			$catQuery = 'select distinct a.`id`, a.`private`';
			$catQuery .= ' from `#__discuss_category` as a';
			$catQuery .= ' 	left join `#__discuss_category_acl_map` as b on a.`id` = b.`category_id`';
			$catQuery .= ' 		and b.`acl_id` = ' . $db->Quote($aclType);
			$catQuery .= ' 		and b.`type` = ' . $db->Quote('group');
			$catQuery .= ' where a.`private` != ' . $db->Quote('0');

			$gids = '';
			$gid = ED::getUserGroupId($user);

			if ($gid) {
				foreach ($gid as $id) {
					$gids .= (empty($gids))? $db->Quote($id) : ',' . $db->Quote($id);
				}
				$catQuery .= ' and a.`id` NOT IN (';
				$catQuery .= ' SELECT c.category_id FROM `#__discuss_category_acl_map` as c ';
				$catQuery .= ' WHERE c.acl_id = ' .$db->Quote($aclType);
				$catQuery .= ' AND c.type = ' . $db->Quote('group');
				$catQuery .= ' AND c.content_id IN (' . $gids . ') )';
			}

			$db->setQuery($catQuery);
			$result = $db->loadObjectList();
		} else {
			$result = ED::getAclCategories($aclType, $user->id);
		}

		foreach ($result as &$row) {

			$row->childs = null;

			ED::buildNestedCategories($row->id, $row, true);

			$catIds = array();
			$catIds[] = $row->id;

			ED::accessNestedCategoriesId($row, $catIds);

			$excludeCats = array_merge($excludeCats, $catIds);
		}

		$result[$key] = $excludeCats;

		return $result[$key];
	}

	/**
	 * Get a list of assigned acl for a category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAssignedGroups($categoryId, $action = 'view')
	{
		$db = $this->db;

		$query = 'SELECT'
				. ' a.' . $db->qn('content_id')
				. ' FROM ' . $db->qn('#__discuss_category_acl_map') . ' AS a'
				. ' LEFT JOIN ' . $db->qn('#__discuss_category_acl_item') . ' AS b'
				. ' ON a.' . $db->qn('acl_id') . '=' . 'b.' . $db->qn('id')
				. ' WHERE a.' . $db->qn('category_id') . '=' . $db->Quote($categoryId)
				. ' AND a.' . $db->qn('type') . '=' . $db->Quote('group')
				. ' AND b.' . $db->qn('action') . '=' . $db->Quote($action);
		$db->setQuery($query);
		$result = $db->loadColumn();

		if (!$result) {
			return $result;
		}

		return $result;
	}

	/**
	 * Retrieves a list of assigned users to a group's acl
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAssignedACL($groupId, $type = 'group')
	{
		$db = ED::db();
		$acl = array();

		$query	= 'SELECT a.`category_id`, a.`content_id`, a.`status`, b.`id` as `acl_id`';
		$query	.= ' FROM `#__discuss_category_acl_map` as a';
		$query	.= ' LEFT JOIN `#__discuss_category_acl_item` as b';
		$query	.= ' ON a.`acl_id` = b.`id`';
		$query	.= ' WHERE a.`category_id` = ' . $db->Quote($groupId);
		$query	.= ' AND a.`type` = ' . $db->Quote($type);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (count($result) <= 0) {
			return null;
		}

		$acl = null;

		if ($type == 'group') {
			$joomlaGroups = ED::getJoomlaUserGroups();
			$acl = $this->mapRules($result, $joomlaGroups);
		} else {
			$users = $this->getAclUsers($result);
			$acl = $this->mapRules($result, $users);
		}

		return $acl;
	}

	public function getAclUsers($aclUsers)
	{
		$db = $this->db;
		$users  = array();

		foreach ($aclUsers as $item) {
			$users[] = $item->content_id;
		}

		$userlist   = '';

		foreach ($users as $user) {
			$userlist .= ( $userlist == '') ? $db->Quote($user) : ', ' . $db->Quote($user);
		}

		$query  = 'select id, name from `#__users` where `id` IN (' . $userlist . ')';
		$db->setQuery($query);

		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Map category rules
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function mapRules($catRules, $joomlaGroups)
	{
		$db = $this->db;
		$acl = array();

		$query = 'select * from `#__discuss_category_acl_item` order by id';
		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		foreach( $result as $item )
		{
			$aclId		= $item->id;
			$default	= $item->default;

			foreach( $joomlaGroups as $joomla )
			{
				$groupId		= $joomla->id;
				$catRulesCnt	= count($catRules);
				//now match each of the catRules
				if( $catRulesCnt > 0)
				{
					$cnt = 0;
					foreach( $catRules as $rule)
					{
						if($rule->acl_id == $aclId && $rule->content_id == $groupId)
						{
							$acl[$aclId][$groupId]				= new stdClass();
							$acl[$aclId][$groupId]->status		= $rule->status;
							$acl[$aclId][$groupId]->acl_id		= $aclId;
							$acl[$aclId][$groupId]->groupname	= $joomla->name;
							$acl[$aclId][$groupId]->groupid		= $groupId;
							break;
						}
						else
						{
							$cnt++;
						}
					}

					if( $cnt == $catRulesCnt)
					{
						//this means the rules not exist in this joomla group.
						$acl[$aclId][$groupId]				= new stdClass();
						$acl[$aclId][$groupId]->status		= '0';
						$acl[$aclId][$groupId]->acl_id		= $aclId;
						$acl[$aclId][$groupId]->groupname	= $joomla->name;
						$acl[$aclId][$groupId]->groupid		= $groupId;
					}
				}
				else
				{
					$acl[$aclId][$groupId]->status		= $default;
					$acl[$aclId][$groupId]->acl_id		= $aclId;
					$acl[$aclId][$groupId]->groupname	= $joomla->name;
					$acl[$aclId][$groupId]->groupid		= $groupId;
				}
			}
		}

		return $acl;
	}



	/**
	 * Method to get total category created so far iregardless the status.
	 *
	 * @access public
	 * @return integer
	 */
	public function getTotalCategory($userId = 0 )
	{
		$db		= DiscussHelper::getDBO();
		$where	= array();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_category' );

		if(! empty($userId))
			$where[]  = '`created_by` = ' . $db->Quote($userId);

		$extra	= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		$query	= $query . $extra;

		$db->setQuery( $query );

		$result	= $db->loadResult();

		return (empty($result)) ? 0 : $result;
	}

	/**
	 * Retrieves the total number of subcategories a category has
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalSubcategories($categoryId)
	{
		$db = ED::db();

		$query  = 'SELECT count(1) FROM `#__discuss_category` WHERE `parent_id` = ' . $db->Quote($categoryId);
		$db->setQuery($query);

		$total = (int) $db->loadResult();

		return $total;
	}

	public function isExist($categoryName, $excludeCatIds='0')
	{
		$db = DiscussHelper::getDBO();

		$query  = 'SELECT COUNT(1) FROM #__discuss_category';
		$query  .= ' WHERE `title` = ' . $db->Quote($categoryName);
		if($excludeCatIds != '0')
			$query  .= ' AND `id` != ' . $db->Quote($excludeCatIds);

		$db->setQuery($query);
		$result = $db->loadResult();

		return (empty($result)) ? 0 : $result;
	}

	/**
	 * Rebuilds the ordering of a category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function rebuildOrdering($parentId = null, $leftId = 0 )
	{
		$db = $this->db;

		$query	= 'select `id` from `#__discuss_category`';
		$query	.= ' where parent_id = ' . $db->Quote($parentId);
		$query	.= ' order by lft, id';

		$db->setQuery( $query );
		$children = $db->loadObjectList();

		// The right value of this node is the left value + 1
		$rightId = $leftId + 1;

		// execute this function recursively over all children
		foreach ($children as $node) {
			// $rightId is the current right value, which is incremented on recursion return.
			// Increment the level for the children.
			// Add this item's alias to the path (but avoid a leading /)
			$rightId = $this->rebuildOrdering($node->id, $rightId);

			// If there is an update failure, return false to break out of the recursion.
			if ($rightId === false) return false;
		}

		// We've got the left value, and now that we've processed
		// the children of this node we also know the right value.
		$updateQuery	= 'update `#__discuss_category` set';
		$updateQuery	.= ' `lft` = ' . $db->Quote( $leftId );
		$updateQuery	.= ', `rgt` = ' . $db->Quote( $rightId );
		$updateQuery	.= ' where `id` = ' . $db->Quote($parentId);

		$db->setQuery($updateQuery);

		// If there is an update failure, return false to break out of the recursion.
		if (! $db->query())
		{
			return false;
		}

		// Return the right value of this node + 1.
		return $rightId + 1;
	}

	/**
	 * Determines if an alias exists on the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function aliasExists($alias, $categoryId = null)
	{
		$db = $this->db;

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_category' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'alias' ) . '=' . $db->Quote( $alias );

		if ($categoryId) {
			$query .= ' AND ' . $db->nameQuote( 'id' ) . '!=' . $db->Quote($categoryId);
		}

		$db->setQuery($query);

		return $db->loadResult() > 0 ? true : false;
	}

	/**
     * Get the total viewable subcategories based on category permission
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
	public function getTotalViewableChilds($catId, &$count)
    {
    	$db = ED::db();

		$query  = 'SELECT `id` FROM `#__discuss_category` as b';
		$query .= ' WHERE `parent_id` = ' . $db->Quote($catId);
		$query .= ' AND `published` = 1';
		$query .= ' AND ' . ED::category()->genCategoryAccessSQL('b.id', array(), DISCUSS_CATEGORY_ACL_ACTION_VIEW);
		$db->setQuery($query);

		$categories = $db->loadObjectList();
		$model = ED::model('category');

		foreach ($categories as $category) {
			$count++;
			$model->getTotalViewableChilds($category->id, $count);
		}

		return $count;
    }

	/**
	 * Determines if an alias exists on the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getIdFromAlias($alias, $parentId = null)
	{
		$db = $this->db;


		$query = "select id from `#__discuss_category`";
		$query .= " where `alias` = " . $db->Quote($alias);

		if ($parentId) {
			$query .= " AND parent_id = " . $db->Quote($parentId);
		}

		$db->setQuery($query);

		$id = $db->loadResult();

		// Try replacing ':' to '-' since Joomla replaces it
		if ( !$id ) {

			$alias = JString::str_ireplace(':' , '-' , $alias);

			$query = "select id from `#__discuss_category`";
			$query .= " where `alias` = " . $db->Quote($alias);

			if ($parentId) {
				$query .= " AND parent_id = " . $db->Quote($parentId);
			}

			$db->setQuery($query);
			$id = $db->loadResult();
		}

		return $id;
	}


	/**
	 * Return categories hierachy and sort by ordering column.
	 *
	 * @since	4.0.22
	 * @access	public
	 */
	public function generateCategoryFilterList()
	{
		$db = ED::db();

		$query = "select a.*, (SELECT COUNT(id) FROM `#__discuss_category` WHERE `lft` < a.`lft` AND `rgt` > a.`rgt`) AS depth";
		$query .= " from `#__discuss_category` as a";
		$query .= " order by a.`lft`";

		// echo $query;exit;

		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}

}
