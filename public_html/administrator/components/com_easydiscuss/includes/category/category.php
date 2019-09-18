<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussCategory extends EasyDiscuss
{
	// This is the DiscussConversation table
	protected $table = null;

	// This is the binded data
	protected $bindData = array();

	public function __construct($item)
	{
		parent::__construct();

		$this->table = ED::table('Category');

		// For object that is being passed in
		if (is_object($item) && !($item instanceof DiscussCategory)) {
			$this->table->bind($item);
		}

		// If the object is DiscussConversation, just map the variable back.
		if ($item instanceof DiscussCategory) {
			$this->table = $item;
		}

		// If this is an integer
		if (is_int($item) || is_string($item)) {
			$this->table->load($item);
		}
	}

	/**
	 * Magic method to get properties which don't exist on this object but on the table
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function __get($key)
	{
		if (isset($this->table->$key)) {
			return $this->table->$key;
		}

		if (isset($this->$key)) {
			return $this->$key;
		}

		return $this->table->$key;
	}

	/**
	 * Allows caller to set properties to the table without directly accessing it
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function set($key, $value)
	{
		$this->table->$key = $value;
	}

	/**
	 * Allows caller to bind properties to the table without directly accessing it
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function bind($data)
	{
		$this->table->bind($data);

		$this->bindData = $data;
	}

	/**
	 * Normalizes the category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function normalize()
	{
		// Ensure that there is creation date.
		if (!$this->table->created) {
			$date = ED::date();
			$this->table->created = $date->toSql();
		}

		// Generate an alias for this category
		if (!$this->table->alias && $this->table->title) {
			$this->table->alias = $this->generateAlias();
		}

		// Check for a valid alias
		 $this->table->alias = ED::getAlias($this->table->alias, 'category', $this->table->id);
	}

	/**
	 * Ensures that this category is valid
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function validate()
	{
		// Normalize the content
		$this->normalize();

		if (!$this->table->title) {
            $this->setError('COM_EASYDISCUSS_CATEGORY_EMPTY_TITLE');
			return false;
		}

		// Check the category container shouldn't allow to set if that category already contain post.
		// We check only if this is an update.
		$postCount = 0;
		if ($this->table->id) {
			$model = ED::model('Categories');
			$postCount = $model->getUsedCount($this->table->id, false);
		}

		// if user enable container option to yes, we need to ensure that
		// do not have any post created in this category
		if ($postCount != 0 && $this->table->container) {
            $this->setError('COM_EASYDISCUSS_CATEGORY_UNABLE_SET_AS_CONTAINER');
			return false;
		}

		return true;
	}

	/**
	 * Allows caller to bind properties to the table without directly accessing it
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function save($updateOrdering = false)
	{
		// Normalize the content
		$this->normalize();

		$state = $this->table->store($updateOrdering);

		if (!$state) {
			return $state;
		}

		// Whenever a category is saved, we need to update the acl as well
		$model = ED::model('Category');
		$model->updateACL($this->table->id, $this->bindData, null);

		// We also need to update the ordering of all categories when a category is saved
		// This is to ensure the ordering doesn't get messed up
		$model->rebuildOrdering();

		return $state;
	}

	/**
	 * Deletes a category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function delete()
	{
		// Do not delete this category if there are still posts in it otherwise these posts would be an orphan.
		$total = $this->getTotalPosts();

		if ($total) {
			$this->setError(JText::sprintf('COM_EASYDISCUSS_CATEGORIES_DELETE_ERROR_POST_NOT_EMPTY', $this->table->title));
			return false;
		}

		// Do not delete if this is a default category
		if ($this->table->default) {
			$this->setError(JText::sprintf('COM_EASYDISCUSS_CATEGORIES_DELETE_ERROR_DEFAULT_CATEGORY', $this->table->title));
			return false;
		}

		// Do not delete this category if this is a parent category
		$childs = $this->getTotalSubcategories();

		if ($childs) {
			$this->setError(JText::sprintf('COM_EASYDISCUSS_CATEGORIES_DELETE_ERROR_CHILD_NOT_EMPTY', $this->table->title));
			return false;
		}

		// Try to delete the category now
		$state = $this->table->delete();

		if (!$state) {
			$this->setError($this->table->getError());
			return false;
		}

		// Delete other relations to this category
		$this->deleteAvatar();
		$this->deleteACL();

		return true;
	}

	/**
	 * Deletes acl for the category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteACL()
	{
		$model = ED::model('Category');
		return $model->deleteACLMapping($this->table->id);
	}

	/**
	 * Deletes a category avatar
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteAvatar($store = false)
	{
		$model = ED::model('Category');
		$state = $model->deleteAvatar($this);

		if ($store) {
			$this->table->avatar = '';
			$this->table->store();
		}

		return $state;
	}

	/**
	 * Generates an alias
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function generateAlias()
	{
		jimport('joomla.filesystem.filter.filteroutput');

		$i = 1;

		$alias = $this->table->alias ? $this->table->alias : $this->table->title;
		$alias = ED::permalinkSlug($alias);

		$tmp = $alias;

		$model = ED::model('Category');

		while ($model->aliasExists($tmp, $this->table->id) || !$tmp) {

			$alias = !$alias ? ED::permalinkSlug($this->table->title) : $alias;
			$tmp = !$tmp ? ED::permalinkSlug($this->table->title) : $alias . '-' . $i;

			$i++;
		}

		return $tmp;
	}

	/**
	 * Retrieves alias for the category
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAlias()
	{
		$config = ED::config();
		$alias = $this->table->alias;

		if ($config->get('main_sef_unicode') || !EDR::isSefEnabled()) {
			$alias = $this->table->id . ':' . $this->table->alias;
		}

		return $alias;
	}

	/**
	 * Determines if this category is public
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isPublic()
	{
		return $this->private == DISCUSS_PRIVACY_PUBLIC;
	}

	/**
	 * Determines if this category is a subcategory
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isSubcategory()
	{
		return $this->parent_id;
	}

	/**
	 * Determines if this category is a container
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isContainer()
	{
		return $this->container;
	}

	/**
	 * Determines if the current user can view replies from posts in this category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function canViewReplies($userId = null)
	{
		static $items = array();

		if (!isset($items[$this->table->id])) {

			$items[$this->table->id] = ED::isModerator($this->table->id);

			// If user is not a moderator, check again
			if (!$items[$this->table->id]) {

				$model = ED::model('Category');
				$disallowed = $model->getDisallowedCategories($userId, DISCUSS_CATEGORY_ACL_ACTION_VIEWREPLY);
				$items[$this->table->id] = !in_array($this->table->id, $disallowed);

			}
		}

		return $items[$this->table->id];
	}

	/**
	 * Determines if public users can access this category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function canPublicAccess()
	{
		if ($this->isPublic()) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the user can start a new discussion in this category.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function canPost($userId = null)
	{

		// if this is a container, no one can post into this category.
		if ($this->isContainer()) {
			return false;
		}

		// If user is a site admin, they are always allowed regardless.
		if (ED::isSiteAdmin()) {
			return true;
		}

		// Public categories should always be allowed
		if ($this->isPublic()) {
			return true;
		}

		static $items = array();

		if (isset($items[$this->table->id])) {
			return $items[$this->table->id];
		}


		$user = ED::user($userId);
		$allowed = false;


		// If this is a private category, we need to do additional checks here.
		$model = ED::model('Category');
		$disallowed = $model->getDisallowedCategories($userId, DISCUSS_CATEGORY_ACL_ACTION_SELECT);

		if (!in_array($this->table->id, $disallowed)) {
			$allowed = true;
		}

		$items[$this->table->id] = $allowed;

		return $items[$this->table->id];
	}

	/**
	 * Determines if this category can be accessed
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function canAccess($userId = null)
	{
		static $items = array();

		if (isset($items[$this->table->id])) {
			return $items[$this->table->id];
		}

		$model = ED::model('Category');
		$disallowed = $model->getDisallowedCategories($userId);
		$items[$this->table->id] = !in_array($this->table->id, $disallowed);

		return $items[$this->table->id];
	}

	/**
	 * Determines if the user can reply to discussions under this category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function canReply($userId = null)
	{
		static $items = array();

		if (isset($items[$this->table->id])) {
			return $items[$this->table->id];
		}

		$model = ED::model('Category');
		$disallowed = $model->getDisallowedCategories($userId, DISCUSS_CATEGORY_ACL_ACTION_REPLY);

		$items[$this->table->id] = !in_array($this->table->id, $disallowed);

		return $items[$this->table->id];
	}

	/**
	 * Retrieves the title of the category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTitle()
	{
		$title = JText::_($this->table->title);

		return $title;
	}

	/**
	 * Retrieves the description of the category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDescription()
	{
		$desc = JText::_($this->table->description);

		return $desc;
	}


	/**
	 * Retrieves the total number of unresolved posts from this category.
	 *
	 * @since	3.0
	 * @access	public
	 * @return	int
	 */
	public function getUnresolvedCount( $excludeFeatured = false )
	{
		$sig = 'unresolvedcount-' . (int) $excludeFeatured;

		if(! isset( self::$_data[ $this->id ][$sig] ) )
		{
			$model 	= ED::model( 'Posts' );
			$featuredOnly   = ( $excludeFeatured ) ? false : 'all';
			$count 	= $model->getUnresolvedCount( '' , $this->id , null, $featuredOnly );

			self::$_data[ $this->id ][$sig] = $count;
		}

		return self::$_data[ $this->id ][$sig];
	}


	/**
	 * Retrieves the total number of unread posts from this category by user
	 *
	 * @since	4.0
	 * @access	public
	 * @return	int
	 */
	public function getUnreadCount($excludeFeatured = false)
	{
		static $items = array();

		$key = (int) $excludeFeatured;
		$key = $this->id . $key;

		if (isset($items[$key])) {
			return $items[$key];
		}

		$model = ED::model('Posts');
		$items[$key] = $model->getUnreadCount($this->table->id, $excludeFeatured);

		return $items[$key];
	}

	/**
	 * Retrieves the total number of unanswered posts from this category.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getUnansweredCount($excludeFeatured = false)
	{
		static $items = array();

		$key = (int) $excludeFeatured;

		if (isset($items[$key])) {
			return $items[$key];
		}

		$items[$key] = ED::getUnansweredCount($this->table->id, $excludeFeatured);

		return $items[$key];
	}

	/**
	 * Retrieves the avatar of the category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAvatar()
	{
		$source = '';
		$default = rtrim(JURI::root(), '/') . '/media/com_easydiscuss/images/default_category.png';

		$defaults = array('cdefault.png', 'default_category.png', 'components/com_easydiscuss/themes/default/images/default_category.png', 'components/com_easydiscuss/assets/images/cdefault.png', 'components/com_easydiscuss/themes/wireframe/images/default_category.png');

		if (in_array($this->table->avatar, $defaults) || !$this->table->avatar) {
			return $default;
		}

		// If there is an avatar, get the path to the avatar
		$relativePath = ED::image()->getAvatarRelativePath('category') . '/' . $this->table->avatar;
		$url = rtrim(JURI::root(), '/') . '/' . $relativePath;

		return $url;
	}

	/**
	 * Retrieves the RSS link for the category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getRSSLink()
	{
		return $this->getRSSPermalink();
	}

	public function getRSSPermalink($external = false)
	{
  		$url = 'view=categories&layout=listings&category_id=' . $this->id;
		$url = ED::feeds()->getFeedURL($url);

		return $url;
	}

	/**
	 * Returns a list of moderators for this category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getModerators()
	{
		static $items = array();

		if (isset($items[$this->table->id])) {
			return $items[$this->table->id];
		}


		$model = ED::model('Category');
		$items[$this->table->id] = $model->getModerators($this->table->id);

		return $items[$this->table->id];
	}

	/**
	 * Retrieves the Atom link for the category.
	 *
	 * @since	4.0
	 * @access	public
	 * @return	string	The RSS url.
	 */
	public function getAtomLink()
	{
		$url = 'view=categories&category_id=' . $this->table->id;
		$url = ED::feeds()->getFeedURL($url);

		return $url;
	}

	/**
	 * Retrieves the permalink of the category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPermalink($xhtml = true, $external = false)
	{
		$url = 'view=forums&category_id=' . $this->table->id;
		// $url = 'view=categories&layout=listings&category_id=' . $this->table->id;

		if ($external) {
			$url = EDR::getRoutedURL($url, false, true);
		} else {
			$url = EDR::_($url, $xhtml);
		}


		return $url;
	}

	/**
	 * Retrieves the total number of subcategories this category has
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalSubcategories()
	{
		static $items = array();

		if (!isset($items[$this->id])) {
			$model = ED::model('Category');
			$items[$this->id] = $model->getTotalSubcategories($this->table->id);
		}

		return $items[$this->id];
	}

	/**
	 * Retrieves total number of posts from a category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalPosts($options = array())
	{
		static $items = array();

		if (isset($items[$this->table->id])) {
			return $items[$this->table->id];
		}

		$model = ED::model('Category');
		$items[$this->table->id] = $model->getTotalPosts($this->table->id);

		return $items[$this->table->id];
	}

	/**
	 * Retrieves the category params
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getParams()
	{
		static $items = array();

		if (isset($items[$this->table->id])) {
			return $items[$this->table->id];
		}

		$params = new JRegistry($this->table->params);
		$items[$this->table->id] = $params;

		return $items[$this->table->id];
	}

	/**
	 * Retrieves the category settings
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getParam($key, $default = '')
	{
		$params = $this->getParams();

		return $params->get($key, $default);
	}

	/**
	 * Retrieves the pathway of a category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getBreadcrumbs()
	{
		$obj = new stdClass();
		$obj->id = $this->table->id;
		$obj->link = $this->getPermalink();
		$obj->title = $this->getTitle();

		$items = array($obj);

		// Detects if this is a subcategory
		if (!$this->isSubcategory()) {
			return $items;
		}

		// If this is a subcategory, we should traverse it's parents
		$this->getNestedPathway($this->table->parent_id, $items);

		// Reverse the data so we get it in a proper order.
		$items = array_reverse($items);

		return $items;
	}

	/**
	 * Gets the nested pathway
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function getNestedPathway($parent, &$data)
	{
		$category = ED::category($parent);

		$obj = new stdClass();
		$obj->id = $category->id;
		$obj->title = $category->getTitle();
		$obj->link = $category->getPermalink();

		$data[] = $obj;

		if ($category->isSubcategory()) {
			$this->getNestedPathway($category->parent_id, $data);
		}
	}


	public function genCategoryAccessSQL($columnId, $options = array(), $acl = DISCUSS_CATEGORY_ACL_ACTION_VIEW)
	{
		$gid = array();
		$db = ED::db();

		$userId = $this->my->id;

		if (isset($options['useUserId'])) {
			if ($options['useUserId']) {
				$gid = JAccess::getGroupsByUser($options['useUserId'], false);
			} else {
				$gid = JAccess::getGroupsByUser(0, false);
			}

			$userId = $options['useUserId'];
		} else {
			if ($this->my->guest) {
				$gid = JAccess::getGroupsByUser(0, false);
			} else {
				$gid = JAccess::getGroupsByUser($this->my->id, false);
			}
		}


		$gids = '';

		if (count($gid) > 0) {
			foreach ($gid as $id) {
				$gids .= (empty($gids)) ? $id : ',' . $id;
			}
		}

		$includeChilds = isset($options['includeChilds']) ? $options['includeChilds'] : false;
		$include = (isset($options['include']) && $options['include']) ? $options['include'] : array();

		$excludeCatSQL = '';
		$includeCatSQL = '';
		$typeCatSQL = '';
		$statCatSQL = '';

		if ($options) {
			if (isset($options['exclude']) && $options['exclude']) {

				if (is_array($options['exclude'])) {
					$options['exclude'] = array_unique($options['exclude']);
				}

				if (is_array($options['exclude']) && count($options['exclude']) > 1) {
					$excludeCatSQL = " AND acat.`id` NOT IN (" . implode(',', $options['exclude']) . ")";
				} else {
					$excludeCatSQL = (is_array($options['exclude'])) ? " AND acat.`id` != " . $options['exclude'][0] : " AND acat.`id` != " . $options['exclude'];
				}
			}

			if ($include) {
				if (is_array($options['include'])) {
					$include = array_unique($include);
				}

				$catAlias = ($includeChilds) ? 'pcat' : 'acat';

				if (is_array($include) && count($include) > 1) {
					$includeCatSQL = " AND " . $db->nameQuote($catAlias . '.id') . " IN (" . implode(',', $include) . ")";
				} else {
					$includeCatSQL = (is_array($include)) ? " AND " . $db->nameQuote($catAlias . ".id") . " = " . $include[0] : " AND " . $db->nameQuote($catAlias . ".id") . " = " . $include;
				}
			}

		}

		//starting bracket
		$sql = "1 <= (";

		if ($includeChilds && $include) {
			$sql .= "SELECT COUNT(1) FROM " . $db->nameQuote('#__discuss_category') . " AS pcat";
			$sql .= "	INNER JOIN " . $db->nameQuote('#__discuss_category') . " AS acat ON (pcat.`lft` <= acat.`lft` AND pcat.`rgt` >= acat.`rgt`)";
		} else {
			$sql .= "SELECT COUNT(1) FROM " . $db->nameQuote('#__discuss_category') . " as acat";
		}

		$sql .=	" WHERE acat.`id` = $columnId";
		$sql .= $includeCatSQL;
		$sql .= $excludeCatSQL;
		$sql .= " AND (";
		$sql .= " 	( acat.`private` = 0 ) OR";
		$sql .= " 	( (acat.`private` = 1) AND (" . $userId . " > 0) ) OR";
		// joomla groups.
		$sql .= " 	( (acat.`private` = 2) AND ( (select count(1) from " . $db->nameQuote('#__discuss_category_acl_map') . " as cacl WHERE cacl.`category_id` = acat.id AND cacl.`acl_id` = $acl AND cacl.type = 'group' AND cacl.`content_id` in (" . $gids . ")) > 0 ) )";
		$sql .= " )";
		//ending bracket
		$sql .= " )";

		// echo $sql;
		// echo '<br><br>';

		return $sql;

	}

	public static function getChildIds( $parentId = 0 )
	{
		static $childIds = array();

		if( !isset( $childIds[$parentId] ) )
		{
			$result = array();
			self::getNestedIds( $parentId , $result );

			$childIds[$parentId] = $result;
		}

		return $childIds[$parentId];
	}

	private static function getNestedIds( $parentId , &$result )
	{
		static $categories = array();

		if( empty($categories) )
		{
			$db		= DiscussHelper::getDBO();
			$query	= 'SELECT * FROM ' . $db->nameQuote( '#__discuss_category' ) . ' order by lft asc';

			$db->setQuery( $query );
			$items	= $db->loadObjectList();

			foreach ( $items as $category)
			{
				if( !empty( $category->parent_id ) )
				{
					$categories[$category->parent_id][] = $category;
				}
			}
		}

		if( isset( $categories[ $parentId ] ) )
		{
			foreach ($categories[ $parentId ] as $category)
			{
				$result[] = $category->id;
				self::getNestedIds( $category->id , $result );
			}

		}
	}

	public function getChildCategories($parentId , $isPublishedOnly = false, $includePrivate = true, $exclusion = array(), $ordering = false, $aclType = DISCUSS_CATEGORY_ACL_ACTION_VIEW)
	{
		static $categories = array();

		$config = ED::getConfig();
		$db	= ED::db();
		$my	= JFactory::getUser();
		$app = JFactory::getApplication();

		$sig = $parentId . '-' . (int) $isPublishedOnly . '-' . (int) $includePrivate;

		if (!array_key_exists($sig, $categories)) {
			$db	= ED::db();

			$sortConfig = $config->get('layout_ordering_category','latest');

			if ($ordering) {
				$sortConfig = $ordering;
			}

			// $query  = 'SELECT a.`id`, a.`title`, a.`alias`, a.`private`,a.`default`, a.`container`';
			$query = 'SELECT a.*';
			$query .= ' FROM `#__discuss_category` as a';
			$query .= ' WHERE a.`parent_id` = ' . $db->Quote($parentId);

			if (!$app->isAdmin()) {
				// We don't need to check for language in backend because only frontend has the language filter
				$filterLanguage = JFactory::getApplication()->getLanguageFilter();

				if ($filterLanguage) {
					$query .= ' AND ' . ED::getLanguageQuery('a.language');
				}
			}

			if ($isPublishedOnly) {
				$query	.=  ' AND a.`published` = ' . $db->Quote('1');
			}

			if (!$app->isAdmin()) {

				if (!$includePrivate) {
					//check categories acl here.
					$catIds = ED::getAclCategories($aclType, $my->id, $parentId);

					if (count($catIds) > 0) {

						$strIds = '';

						foreach ($catIds as $cat) {
							$strIds = (empty($strIds)) ? $cat->id : $strIds . ', ' . $cat->id;
						}

						$query .= ' AND a.id NOT IN (';
						$query .= $strIds;
						$query .= ')';
					}
				}
			}

			// Exclude category list.
			if (!empty($exclusion)) {

				$excludeQuery = 'AND a.`id` NOT IN (';

				for ($i = 0 ; $i < count($exclusion); $i++) {

					$id = $exclusion[$i];

					$excludeQuery .= $db->Quote($id);

					if (next($exclusion) !== false) {
						$excludeQuery .= ',';
					}
				}

				$excludeQuery .= ')';

				$query .= $excludeQuery;
			}

			switch($sortConfig) {
				case 'alphabet' :
					$orderBy = ' ORDER BY a.`title` ';
					break;
				case 'ordering' :
					$orderBy = ' ORDER BY a.`lft` ';
					break;
				case 'latest' :
					$orderBy = ' ORDER BY a.`created` ';
					break;
				default	:
					$orderBy = ' ORDER BY a.`lft` ';
					break;
			}

			$sort = $config->get('layout_sort_category', 'asc');

			$query .= $orderBy.$sort;

			$db->setQuery($query);
			$result = $db->loadObjectList();

			$categories[$sig] = $result;
		}

		return $categories[$sig];
	}

	/**
	 * Get a list of acl that is assigned to this category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAssignedGroups($action = 'view')
	{
		$model = ED::model('Category');
		$groups = $model->getAssignedGroups($this->table->id, $action);

		return $groups;
	}

	/**
	 * print the child categories in tree listing.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function printTrees($child, $level)
	{
        $wrapperStart = '<ul class="ed-tree__list">';
        $wrapperEnd = '</ul>';

        $addWrapper = false;
        $tree = '';

        foreach ( $child as $item ) {
            if ($item->parent_id == $level ) {

                $addWrapper = true;

                $theme = ED::themes();
                $theme->set('category', $item);
                $output = $theme->output('site/categories/default.item');

                $tree = $tree . '<li class="ed-tree__item">' . $output . $this->printTrees($child, $item->id) . "</li>";
            }
        }

        $tree = $addWrapper ? $wrapperStart . $tree . $wrapperEnd : $tree;
        return $tree;
	}

	/**
	 * move category ordering the lft and rgt column
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function move( $direction , $where = '' )
	{
		$db = ED::db();

		if( $direction == -1) //moving up
		{
			// getting prev parent
			$query  = 'select `id`, `lft`, `rgt` from `#__discuss_category` where `lft` < ' . $db->Quote($this->lft);
			if($this->parent_id == 0)
				$query  .= ' and parent_id = 0';
			else
				$query  .= ' and parent_id = ' . $db->Quote($this->parent_id);
			$query  .= ' order by lft desc limit 1';

			$db->setQuery($query);
			$preParent  = $db->loadObject();

			// calculating new lft
			$newLft = $this->lft - $preParent->lft;
			$preLft = ( ($this->rgt - $newLft) + 1) - $preParent->lft;

			//get prevParent's id and all its child ids
			$query  = 'select `id` from `#__discuss_category`';
			$query  .= ' where lft >= ' . $db->Quote($preParent->lft) . ' and rgt <= ' . $db->Quote($preParent->rgt);
			$db->setQuery($query);

			$preItemChilds = $db->loadResultArray();
			$preChildIds   = implode(',', $preItemChilds);
			$preChildCnt   = count($preItemChilds);

			//get current item's id and it child's id
			$query  = 'select `id` from `#__discuss_category`';
			$query  .= ' where lft >= ' . $db->Quote($this->lft) . ' and rgt <= ' . $db->Quote($this->rgt);
			$db->setQuery($query);

			$itemChilds = $db->loadResultArray();
			$childIds   = implode(',', $itemChilds);
			$ChildCnt   = count($itemChilds);

			//now we got all the info we want. We can start process the
			//re-ordering of lft and rgt now.
			//update current parent block
			$query  = 'update `#__discuss_category` set';
			$query  .= ' lft = lft - ' . $db->Quote($newLft);
			if( $ChildCnt == 1 ) //parent itself.
			{
				$query  .= ', `rgt` = `lft` + 1';
			}
			else
			{
				$query  .= ', `rgt` = `rgt` - ' . $db->Quote($newLft);
			}
			$query  .= ' where `id` in (' . $childIds . ')';

			$db->setQuery($query);
			$db->query();

			$query  = 'update `#__discuss_category` set';
			$query  .= ' lft = lft + ' . $db->Quote($preLft);
			$query  .= ', rgt = rgt + ' . $db->Quote($preLft);
			$query  .= ' where `id` in (' . $preChildIds . ')';

			$db->setQuery($query);
			$db->query();

			//now update the ordering.
			if( $this->ordering > 0 )
			{
				$query  = 'update `#__discuss_category` set';
				$query  .= ' `ordering` = `ordering` - 1';
				$query  .= ' where `id` = ' . $db->Quote($this->id);
				$db->setQuery($query);
				$db->query();
			}

			//now update the previous parent's ordering.
			$query  = 'update `#__discuss_category` set';
			$query  .= ' `ordering` = `ordering` + 1';
			$query  .= ' where `id` = ' . $db->Quote($preParent->id);
			$db->setQuery($query);
			$db->query();

			return true;
		}
		else //moving down
		{
			// getting next parent
			$query  = 'select `id`, `lft`, `rgt` from `#__discuss_category` where `lft` > ' . $db->Quote($this->lft);
			if($this->parent_id == 0)
				$query  .= ' and parent_id = 0';
			else
				$query  .= ' and parent_id = ' . $db->Quote($this->parent_id);
			$query  .= ' order by lft asc limit 1';

			$db->setQuery($query);
			$nextParent  = $db->loadObject();


			$nextLft	= $nextParent->lft - $this->lft;
			$newLft		= ( ($nextParent->rgt - $nextLft) + 1) - $this->lft;


			//get nextParent's id and all its child ids
			$query  = 'select `id` from `#__discuss_category`';
			$query  .= ' where lft >= ' . $db->Quote($nextParent->lft) . ' and rgt <= ' . $db->Quote($nextParent->rgt);
			$db->setQuery($query);

			$nextItemChilds	= $db->loadResultArray();
			$nextChildIds	= implode(',', $nextItemChilds);
			$nextChildCnt	= count($nextItemChilds);

			//get current item's id and it child's id
			$query	= 'select `id` from `#__discuss_category`';
			$query	.= ' where lft >= ' . $db->Quote($this->lft) . ' and rgt <= ' . $db->Quote($this->rgt);
			$db->setQuery($query);

			$itemChilds	= $db->loadResultArray();
			$childIds	= implode(',', $itemChilds);

			//now we got all the info we want. We can start process the
			//re-ordering of lft and rgt now.

			//update next parent block
			$query	= 'update `#__discuss_category` set';
			$query	.= ' `lft` = `lft` - ' . $db->Quote($nextLft);
			if( $nextChildCnt == 1 ) //parent itself.
			{
				$query  .= ', `rgt` = `lft` + 1';
			}
			else
			{
				$query  .= ', `rgt` = `rgt` - ' . $db->Quote($nextLft);
			}
			$query  .= ' where `id` in (' . $nextChildIds . ')';

			$db->setQuery($query);
			$db->query();

			//update current parent
			$query	= 'update `#__discuss_category` set';
			$query	.= ' lft = lft + ' . $db->Quote($newLft);
			$query	.= ', rgt = rgt + ' . $db->Quote($newLft);
			$query	.= ' where `id` in (' . $childIds. ')';

			$db->setQuery($query);
			$db->query();

			//now update the ordering.
			$query	= 'update `#__discuss_category` set';
			$query	.= ' `ordering` = `ordering` + 1';
			$query	.= ' where `id` = ' . $db->Quote($this->id);

			$db->setQuery($query);
			$db->query();

			if( $nextParent->ordering > 0)
			{
				//now update the previous parent's ordering.
				$query	= 'update `#__discuss_category` set';
				$query	.= ' `ordering` = `ordering` - 1';
				$query	.= ' where `id` = ' . $db->Quote($nextParent->id);

				$db->setQuery($query);
				$db->query();
			}

			return true;
		}
	}

    /**
     * Maps existing data back to the table
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function toData()
    {
        // Convert the table to an array
        $data = new stdClass();

        $data->id = $this->table->id;
        $data->permalink = $this->getPermalink(false, true);
        $data->title = $this->table->title;
        $data->alias = $this->table->alias;
        $data->description = $this->table->description;
        $data->created = $this->table->created;
        $data->published = $this->table->published;
        $data->posts = $this->getTotalPosts();

        $data->subcategories = array();

        $subcategories = $this->getChildCategories($this->table->id);

        if ($subcategories) {
        	foreach ($subcategories as $subcategory) {
        		$category = ED::category($subcategory);

        		$data->subcategories[] = $category->toData();
        	}
        }

        return $data;
    }

}
