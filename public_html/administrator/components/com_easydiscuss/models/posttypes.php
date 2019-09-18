<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once dirname( __FILE__ ) . '/model.php';

class EasyDiscussModelPostTypes extends EasyDiscussAdminModel
{
	public $_data = null;
	public $_pagination = null;
	public $_total;

	public function __construct()
	{
		parent::__construct();

		$mainframe = $this->app;

		// Get the number of events from database
		$limit = $mainframe->getUserStateFromRequest('com_easydiscuss.post_types.limit', 'limit', $mainframe->getCfg('list_limit') , 'int');
		$limitstart	= $this->input->get('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Builds query.
	 *
	 * @since	4.0
	 * @access	public
	 */
	protected function _buildQuery($frontend = false)
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where = $this->_buildQueryWhere($frontend);
		$orderby= $this->_buildQueryOrderBy();

		$query = 'SELECT a.* FROM `#__discuss_post_types` AS a '
				. $where . ' ' . $orderby;

		return $query;
	}

	/**
	 * Builds query where.
	 *
	 * @since	4.0
	 * @access	public
	 */
	protected function _buildQueryWhere($frontend = false)
	{
		$mainframe = $this->app;
		$db = $this->db;

		$filter_state = $mainframe->getUserStateFromRequest('com_easydiscuss.post_types.filter_state', 'filter_state', '', 'word');
		$search = $mainframe->getUserStateFromRequest('com_easydiscuss.post_types.search', 'search', '', 'string');
		$search = $db->getEscaped(trim(JString::strtolower($search)));

		$where = array();

		// This is for frontend
		if ($frontend) {
			$where[] = $db->nameQuote('a.published') . '=' . $db->Quote('1');
			$where[] = '(' . $db->qn('a.type') . '=' . $db->Quote('global') . ' OR ' . $db->qn('a.type') . '="")';
		}

		if ($filter_state) {
			if ($filter_state == 'P') {
				$where[] = $db->nameQuote('a.published') . '=' . $db->Quote('1');
			}
			else if ($filter_state == 'U') {
				$where[] = $db->nameQuote('a.published') . '=' . $db->Quote('0');
			}
		}

		if ($search) {
			$where[] = 'LOWER(' . $db->nameQuote('title') . ') LIKE ' . $db->Quote('%' . $search . '%')
					. 'OR LOWER(' . $db->nameQuote('alias') . ') LIKE ' . $db->Quote('%' . $search . '%');
		}

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		return $where;
	}

	/**
	 * Builds query order by.
	 *
	 * @since	4.0
	 * @access	public
	 */
	protected function _buildQueryOrderBy()
	{
		$mainframe = JFactory::getApplication();

		$filter_order = $mainframe->getUserStateFromRequest('com_easydiscuss.customs.filter_order','filter_order', 'a.lft', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest('com_easydiscuss.customs.filter_order_Dir', 'filter_order_Dir', '', 'word');

		$orderby = ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir;

		return $orderby;
	}

	/**
	 * Retrieves the list of post types available.
	 *
	 * @since	4.0.14
	 * @access	public
	 */
	public function getTypes($frontend = false)
	{
		$db = $this->db;

		$query = $this->_buildQuery($frontend);

		// You need this in order limit to work.
		if ($frontend) {
			$this->_data = $this->_getList($query);
		} else {
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_data;
	}


	/**
	 * Total type
	 *
	 * @access public
	 * @return array
	 */
	public function getTotalTypes()
	{
		$db = ED::db();

		$query = 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__discuss_post_types');

		$db->setQuery($query);

		$result = $db->loadResult();

		return $result;
	}


	/**
	 * Retrieves the pagination.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');

			$this->_pagination = ED::getPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	/**
	 * Retrieves the total number of post types available.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotal()
	{
		// Load total number of rows
		if (empty($this->_total)) {
			$this->_total = $this->_getListCount($this->_buildQuery());
		}

		return $this->_total;
	}

	/**
	 * Retrieves the post types available on the ask form
	 *
	 * @since	4.0.14
	 * @access	public
	 */
	public function getPostTypes($categoryId = null, $order = 'ASC', $searchBar = false)
	{
		$db = ED::db();
		$query = array();

		if (!is_null($categoryId)) {
			$categoryId = (int) $categoryId;
		}

		$query[] = 'SELECT a.* FROM ' . $db->qn('#__discuss_post_types') . ' AS a';

		if ($categoryId) {
			$query[] = 'LEFT JOIN ' . $db->qn('#__discuss_post_types_category') . ' AS b';
			$query[] = 'ON a.' . $db->qn('id') . ' = b.' . $db->qn('type_id');
		}

		$query[] = 'WHERE';
		$query[] = 'a.' . $db->qn('published') . '=' . $db->Quote(1);
		$query[] = 'AND';
		$query[] = '(';
		$query[] = 'a.' . $db->qn('type') . '=' . $db->Quote('global');
		$query[] = 'OR';
        $query[] = 'a.' . $db->qn('type') . '=' . $db->Quote('');

		if ($searchBar) {
			$query[] = 'OR';
			$query[] = 'a.' . $db->qn('type') . '=' . $db->Quote('category');
		}

		if ($categoryId) {
			$query[] = 'OR (';
			$query[] = 'a.' . $db->qn('type') . '=' . $db->Quote('category');
			$query[] = 'AND b.' . $db->qn('category_id') . ' = ' . $db->Quote($categoryId);
			$query[] = ')';
		}

		$query[] = ')';

		$query[] = 'ORDER BY `lft` ' . $order;

		$query = implode(' ', $query);

		// echo str_ireplace('#__', 'jos_', $query);
		// exit;

		$db->setQuery($query);
		$types = $db->loadObjectList();

		// dump($types);

		return $types;
	}

	public function getSuffix($alias = null)
	{
		$db = ED::db();
		$query	= 'SELECT `suffix` FROM ' . $db->nameQuote('#__discuss_post_types')
				. ' WHERE ' . $db->nameQuote('alias') . '=' . $db->quote($alias)
				. ' AND ' . $db->nameQuote('published') . '=' . $db->quote(1);

		$db->setQuery($query);
		$result	= $db->loadResult();

		return $result;
	}

	public function getTitle($alias = null)
	{
		$db = ED::db();
		$query	= 'SELECT `title` FROM ' . $db->nameQuote('#__discuss_post_types')
				. ' WHERE ' . $db->nameQuote('alias') . '=' . $db->quote($alias)
				. ' AND ' . $db->nameQuote('published') . '=' . $db->quote(1);

		$db->setQuery($query);
		$result	= $db->loadResult();

		return $result;
	}

	public function setPostTagsBatch( $ids )
	{
		$db = DiscussHelper::getDBO();

		if( count( $ids ) > 0 )
		{

			$query	= 'SELECT a.`id`, a.`title`, a.`alias`, b.`post_id`';
			$query .= ' FROM `#__discuss_tags` AS a';
			$query .= ' LEFT JOIN `#__discuss_posts_tags` AS b';
			$query .= ' ON a.`id` = b.`tag_id`';
			if( count( $ids ) == 1 )
			{
				$query .= ' WHERE b.`post_id` = '.$db->Quote( $ids[0] );
			}
			else
			{
				$query .= ' WHERE b.`post_id` IN (' . implode(',', $ids) . ')';
			}

			$db->setQuery( $query );
			$result = $db->loadObjectList();

			if( count( $result ) > 0 )
			{
				foreach( $result as $item )
				{
					self::$_postTags[ $item->post_id ][] = $item;
				}
			}

			foreach( $ids as $id )
			{
				if(! isset( self::$_postTags[ $id ] ) )
				{
					self::$_postTags[ $id ] = array();
				}
			}


		}
	}

	/*
	 * method to get post tags.
	 *
	 * param postId - int
	 * return object list
	 */
	public function getPostTags($postId)
	{

		if( isset( self::$_postTags[ $postId ] ) )
		{
			return self::$_postTags[ $postId ];
		}


		$db = DiscussHelper::getDBO();

		$query	= 'SELECT a.`id`, a.`title`, a.`alias`';
		$query .= ' FROM `#__discuss_tags` AS a';
		$query .= ' LEFT JOIN `#__discuss_posts_tags` AS b';
		$query .= ' ON a.`id` = b.`tag_id`';
		$query .= ' WHERE b.`post_id` = '.$db->Quote($postId);
		$query .= ' AND a.`published`=' . $db->Quote( 1 );

		$db->setQuery($query);

		if($db->getErrorNum() > 0)
		{
			JError::raiseError( $db->getErrorNum() , $db->getErrorMsg() . $db->stderr());
		}

		$result	= $db->loadObjectList();

		self::$_postTags[ $postId ] = $result;
		return $result;

	}

	public function add( $tagId , $postId , $creationDate )
	{
		$db				= DiscussHelper::getDBO();

		$obj			= new stdClass();
		$obj->tag_id	= $tagId;
		$obj->post_id	= $postId;
		$obj->created	= $creationDate;

		return $db->insertObject( '#__discuss_posts_tags' , $obj );
	}

	public function deletePostTag($postId)
	{
		$db	= DiscussHelper::getDBO();

		$query	= ' DELETE FROM ' . $db->nameQuote('#__discuss_posts_tags')
				. ' WHERE ' . $db->nameQuote('post_id') . ' =  ' . $db->quote($postId);

		$db->setQuery($query);
		$result	= $db->Query();

		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}

		return $result;
	}

	/**
	 * Creates the association for post types
	 *
	 * @since	4.0.14
	 * @access	public
	 */
	public function createAssociation($postType, $categories = array())
	{
		if (!$categories) {
			return;
		}

		// Delete existing items first
		$db = ED::db();
		$query = array();
		$query[] = 'DELETE FROM ' . $db->qn('#__discuss_post_types_category');
		$query[] = ' WHERE ' . $db->qn('type_id') . '=' . $db->Quote($postType->id);

		$query = implode(' ', $query);
		$db->setQuery($query);
		$db->Query();

		foreach ($categories as $categoryId) {
			$id = (int) $categoryId;

			$table = ED::table('PostTypesCategory');
			$table->type_id = $postType->id;
			$table->category_id = $id;
			$table->store();
		}

		return true;
	}

	/**
	 * Retrieves a list of associated categories with the post type
	 *
	 * @since	4.0.14
	 * @access	public
	 */
	public function getAssociatedCategories($postType)
	{
		$db = ED::db();
		$query = array();

		$query[] = 'SELECT b.* FROM ' . $db->qn('#__discuss_post_types_category') . ' AS a';
		$query[] = 'LEFT JOIN ' . $db->qn('#__discuss_category') . ' AS b';
		$query[] = 'ON a.' . $db->qn('category_id') . ' = b.' . $db->qn('id');
		$query[] = 'WHERE a.' . $db->qn('type_id') . ' = ' . $db->Quote($postType->id);

		$query = implode(' ', $query);
		$db->setQuery($query);

		$categories = $db->loadObjectList();

		return $categories;
	}

	/**
	 * Method to check if the lft and rgt columns are valid or not.
	 * If not, it will auto rebuild the ordering.
	 *
	 * @since 4.0.23
	 * @access public
	 */
	public function verifyOrdering($rebuild = true)
	{
		$db = $this->db;
		$query = "SELECT count(1) FROM `#__discuss_post_types` where `lft` = 0";
		$db->setQuery($query);

		$count = $db->loadResult();

		if ($count && $rebuild) {
			$this->rebuildOrdering();
		}

		return true;
	}

	public function rebuildOrdering($id = null, $leftId = 0)
	{
		$db = $this->db;

		$query = "SELECT `id` FROM `#__discuss_post_types`";

		if ($id) {
			$query .= " WHERE `id` = " . $db->Quote($id);
		}

		$query .= " ORDER BY lft, id";

		$db->setQuery($query);
		$children = $db->loadObjectList();

		// The right value of this node is the left + 1
		$rightId = $leftId + 1;

		if (count($children) > 1) {
			// Execute this function recursively over all children.
			foreach ($children as $node) {
				// The $rightId is the current right value, which has been incremented on recursion return.
				// Increment the level for the children.
				// Add this item's alias to the path (but avoid a leading /)
				$rightId = $this->rebuildOrdering($node->id, $rightId);

				// If there is an update failure, return false to break the recursion.
				if ($rightId === false) return false;
			}
		}

		// Now we've got the left value, and the right value.
		$updateQuery = "UPDATE `#__discuss_post_types` SET";
		$updateQuery .= " `lft` = " . $db->Quote($leftId);
		$updateQuery .= ", `rgt` = " . $db->Quote($rightId);
		$updateQuery .= " WHERE `id` = " . $db->Quote($id);

		$db->setQuery($updateQuery);

		if (!$db->execute()) {
			return false;
		}

		return $rightId + 1;
	}

	public function moveOrder($id, $direction)
	{
		$db = $this->db;

		$query = "SELECT * FROM `#__discuss_post_types` WHERE `id` = " . $db->Quote($id);

		$db->setQuery($query);
		$current = $db->loadObject();

		$options = array();
		if ($direction == DISCUSS_ORDER_UP) {

			$query = "SELECT `id`, `lft`, `rgt` FROM `#__discuss_post_types` WHERE `lft` < " . $db->Quote($current->lft) . " ORDER BY `lft` DESC LIMIT 1";
			$db->setQuery($query);
			$prevNode = $db->loadObject();

			$options['direction'] = 'up';
			$options['currentLeft'] = $current->lft;
			$options['currentRight'] = $current->rgt;
			$options['currentNewLeft'] = $prevNode->lft;
			$options['currentNewRight'] = $prevNode->rgt;
			$options['nodeId'] = $prevNode->id;
			$options['nodeLeft'] = $current->lft;
			$options['nodeRight'] = $current->rgt;
		} else {

			$query = "SELECT `id`, `lft`, `rgt` FROM `#__discuss_post_types` WHERE `lft` > " . $db->Quote($current->lft) . " ORDER BY `lft` ASC LIMIT 1";
			$db->setQuery($query);
			$nextNode = $db->loadObject();

			$options['direction'] = 'down';
			$options['currentLeft'] = $current->lft;
			$options['currentRight'] = $current->rgt;
			$options['currentNewLeft'] = $nextNode->lft;
			$options['currentNewRight'] = $nextNode->rgt;
			$options['nodeId'] = $nextNode->id;
			$options['nodeLeft'] = $current->lft;
			$options['nodeRight'] = $current->rgt;
		}

		// Set the current node.
		$query = "UPDATE `#__discuss_post_types` SET";
		$query .= " `lft` = " . $db->Quote($options['currentNewLeft']);
		$query .= ", `rgt` = " . $db->Quote($options['currentNewRight']);
		$query .= " WHERE `id` = " . $db->Quote($current->id);

		$db->setQuery($query);
		$db->execute();

		// Set the affected node.
		$query = "UPDATE `#__discuss_post_types` SET";
		$query .= " `lft` = " . $db->Quote($options['nodeLeft']);
		$query .= ", `rgt` = " . $db->Quote($options['nodeRight']);
		$query .= " WHERE `id` = " . $db->Quote($options['nodeId']);

		$db->setQuery($query);
		$db->execute();

		return true;
	}
}
