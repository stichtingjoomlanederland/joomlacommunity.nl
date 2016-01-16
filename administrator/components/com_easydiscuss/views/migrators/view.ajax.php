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

require_once DISCUSS_ADMIN_ROOT . '/views.php';
require_once DISCUSS_CLASSES . '/json.php';
jimport( 'joomla.utilities.utility' );

class EasyDiscussViewMigrators extends EasyDiscussAdminView
{
	public function communitypolls()
	{
		$ajax 	= DiscussHelper::getHelper( 'Ajax' );

		// Migrate Community Poll categories
		$categories	= $this->getCPCategories();
		$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_COMMUNITY_POLLS_TOTAL_CATEGORIES' , count( $categories) ) , 'communitypolls' );

		$json 	= new Services_JSON();
		$items 	= array();

		foreach( $categories as $category )
		{
			$items[]	= $category->id;
		}

		$ajax->resolve( $items );
	}


	/**
	 * Migrates discusions from com_discussions
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function discussions()
	{
		$ajax = new Disjax();

		$categories = $this->getDiscussionCategories();

		$this->log($ajax, JText::sprintf('Total categories found: <strong>%1s</strong>', count($categories)), 'discussions');

		$items = array();

		foreach ($categories as $category) {
			$items[] = $category->id;
		}

		$data = json_encode($items);
		$ajax->script('runMigrationCategory("discussions",' . $data . ');');

		return $ajax->send();
	}

	public function discussionsPostItem($current, $items)
	{
		$ajax = new Disjax();

		// @task: If categories is no longer an array, then it most likely means that there's nothing more to process.
		if( $current == 'done' ) {
			echo 'done';exit;
			$this->log( $ajax , JText::_( 'COM_EASYDISCUSS_MIGRATORS_MIGRATION_COMPLETED' ) , 'discussions' );

			// lets check if there is any new replies or not.
			$posts = $this->getKunenaReplies( true );
			$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_KUNENA_TOTAL_POSTS' , $posts ) , 'discussions' );

			$ajax->script( 'runMigrationReplies("discussions");' );

			return $ajax->send();
		}

		// Get the discussion object
		$oldItem = $this->getDiscussionPost($current);
		$item = DiscussHelper::getTable('Post');

		// @task: Skip the category if it has already been migrated.
		if ($this->migrated('com_discussions', $current, 'post')) {

			$data = json_encode($items);
			$this->log($ajax, JText::sprintf('Post <strong>%1s</strong> has already been migrated. <strong>Skipping this</strong>...', $oldItem->id), 'discussions');
			$ajax->script('runMigrationItem("discussions", ' . $data . ');');
			return $ajax->send();
		}

		$this->log($ajax, JText::sprintf('Migrating post <strong>%1s</strong>.', $oldItem->id), 'discussions');
		$this->mapDiscussionItem($oldItem, $item);

		// @task: Once the post is migrated successfully, we'll need to migrate the child items.
		$this->log($ajax, JText::sprintf('Migrating replies for post <strong>%1s</strong>.' , $oldItem->id), 'discussions');
		$this->mapDiscussionItemChilds($oldItem, $item);


		// @task: If categories is no longer an array, then it most likely means that there's nothing more to process.
		if (!$items) {
			$this->log($ajax, JText::_('<strong>Migration process completed</strong>.'), 'discussions');
			$this->showMigrationButton($ajax);
			return $ajax->send();
		}

		$data = json_encode($items);
		$ajax->script('runMigrationItem("discussions" , ' . $data . ');' );

		$ajax->send();
	}

	private function mapDiscussionItem($oldItem, &$item, $parent = null)
	{
		$item->bind($oldItem);

		// Unset the id
		$item->id = null;

		$item->title = $oldItem->subject;
		$item->content = $oldItem->message;
		$item->category_id = $this->getDiscussionNewCategory($oldItem);
		$item->user_type = DISCUSS_POSTER_MEMBER;
		$item->created = $oldItem->date;
		$item->modified = $item->created;
		$item->replied = $oldItem->counter_replies;
		$item->poster_name = $oldItem->name;
		$item->poster_email = $oldItem->email;
		$item->content_type = 'bbcode';
		$item->parent_id = 0;
		$item->islock = $oldItem->locked;

		if ($parent) {
			$item->parent_id = $parent->id;
		}

		if (!$item->user_id) {
			$item->user_type = DISCUSS_POSTER_GUEST;
		}

		// Save the item
		$state = $item->store();

		$this->added('com_discussions', $item->id, $oldItem->id, 'post' );
	}

	private function mapDiscussionItemChilds($oldItem, $parent)
	{
		$items = $this->getDiscussionPosts($oldItem->id);

		if (!$items) {
			return false;
		}

		foreach ($items as $oldItemChild) {
			$newItem = DiscussHelper::getTable('Post');

			$this->mapDiscussionItem($oldItemChild, $newItem, $parent);
		}
	}

	private function getDiscussionNewCategory($oldItem)
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT ' . $db->nameQuote( 'internal_id' ) . ' '
				. 'FROM ' . $db->nameQuote( '#__discuss_migrators' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'external_id' ) . ' = ' . $db->Quote( $oldItem->cat_id ) . ' '
				. 'AND ' . $db->nameQuote( 'type' ) . ' = ' . $db->Quote( 'category' ) . ' '
				. 'AND ' . $db->nameQuote( 'component' ) . ' = ' . $db->Quote( 'com_discussions' );

		$db->setQuery( $query );
		$categoryId	= $db->loadResult();

		return $categoryId;
	}

	private function getDiscussionPost($id)
	{
		$db		= DiscussHelper::getDBO();
		$query = 'SELECT * FROM ' . $db->qn('#__discussions_messages');
		$query .= ' WHERE ' . $db->qn('id') . '=' . $db->Quote($id);

		$db->setQuery($query);
		$item	= $db->loadObject();

		return $item;
	}


	public function discussionsCategoryItem($current = "", $categories = "")
	{
		$ajax = new Disjax();

		// Get the discussions category
		$oldCategory = $this->getDiscussionCategory($current);

		// @task: If categories is no longer an array, then it most likely means that there's nothing more to process.
		if ($current == 'done') {
			// category migration done. let reset the ordering here.
			$catTbl = DiscussHelper::getTable( 'Category' );
			$catTbl->rebuildOrdering();

			$this->log($ajax, JText::_('<strong>Category migration completed</strong>'), 'discussions');

			// Get a list of post id's from com_discussions
			$posts = $this->getDiscussionPostsIds();

			$data = implode( '|', $posts );
			$data = json_encode($data);

			$this->log($ajax, JText::sprintf('Total posts found: <strong>%1s</strong>', count($posts)), 'discussions');

			if (count($posts) <= 0) {
				$ajax->script('runMigrationItem("discussions" , "done");');
			} else {
				$ajax->script('runMigrationItem("discussions" , ' . $data . ');' );
			}

			return $ajax->send();
		}

		// @task: Skip the category if it has already been migrated.
		$migratedId = $this->migrated('com_discussions', $current, 'category');
		$category = DiscussHelper::getTable('Category');

		if (!$migratedId) {
			$this->mapDiscussionCategory($oldCategory, $category);
			$this->log($ajax , JText::sprintf('Migrated category <strong>%1s</strong>', $oldCategory->name), 'discussions');
		} else {
			$category->load($migratedId);
		}

		// Migrate all child categories if needed
		$this->processDiscussionCategoryTree($oldCategory, $category);

		if ($migratedId) {
			$data = json_encode($categories);
			$this->log($ajax , JText::sprintf('Category <strong>%1s</strong> has already been migrated. <strong>Skipping this</strong>...', $oldCategory->name), 'discussions');
			$ajax->script('runMigrationCategory("discussions" , ' . $data . ');' );

			return $ajax->send();
		}

		$data = json_encode($categories);
		$ajax->script('runMigrationCategory("discussions", ' . $data . ');');

		return $ajax->send();
	}

	private function getDiscussionPostsIds()
	{
		$db = DiscussHelper::getDBO();

		$query = 'SELECT * FROM ' . $db->qn('#__discussions_messages');
		$query .= ' WHERE ' . $db->qn('parent_id') . '=' . $db->Quote(0);

		$db->setQuery($query);

		$result = $db->loadColumn();

		return $result;
	}

	private function getDiscussionPosts($parent = null)
	{
		$db = DiscussHelper::getDBO();

		$query = 'SELECT * FROM ' . $db->qn('#__discussions_messages');

		if ($parent == null) {
			
			$query .= ' WHERE ' . $db->qn('parent_id') . '=' . $db->Quote(0);
		} else {
			$query .= ' WHERE ' . $db->qn('parent_id') . '=' . $db->Quote($parent);
		}


		$db->setQuery($query);

		$result = $db->loadObjectList();

		return $result;
	}

	private function processDiscussionCategoryTree($oldCategory, $category)
	{
		$ajax = new Disjax();

		$db = DiscussHelper::getDBO();
		$query = 'SELECT * FROM ' . $db->qn('#__discussions_categories')
				.' WHERE ' . $db->qn('parent_id') . '=' . $db->Quote($oldCategory->id)
				.' ORDER BY ' . $db->qn('ordering') . ' ASC';

		$db->setQuery($query);
		$result	= $db->loadObjectList();

		if (!$result) {
			return false;
		}

		foreach ($result as $childCategory) {
			$subcategory = DiscussHelper::getTable('Category');
			$migratedId = $this->migrated('com_discussions', $childCategory->id, 'category');

			if (!$migratedId) {
				$this->mapDiscussionCategory($childCategory, $subcategory, $category->id);
			} else {
				$subcategory->load($migratedId);
			}

			$this->processDiscussionCategoryTree($childCategory, $subcategory);
		}
	}

	private function mapDiscussionCategory($oldCategory, &$category, $parentId = 0)
	{
		$parentId = ($parentId) ? $parentId : 0;

		$category->title = $oldCategory->name;
		$category->description = $oldCategory->description;
		$category->published = $oldCategory->published;
		$category->parent_id = $parentId;
		$category->created_by = DiscussHelper::getDefaultSAIds();

		// Save the new category
		$category->store(true);

		$this->added('com_discussions', $category->id, $oldCategory->id, 'category');
	}

	private function getDiscussionCategory($id)
	{
		$db = JFactory::getDBO();
		$query = 'SELECT * FROM ' . $db->qn('#__discussions_categories');
		$query .= ' WHERE ' . $db->qn('id') . '=' . $db->Quote($id);
		$query .= ' AND ' . $db->qn('parent_id') . '=' . $db->Quote(0);

		$db->setQuery($query);
		$category = $db->loadObject();

		return $category;
	}

	private function getDiscussionCategories()
	{
		$db = JFactory::getDBO();
		$query = 'SELECT * FROM ' . $db->qn('#__discussions_categories');

		$db->setQuery($query);

		$categories = $db->loadObjectList();

		return $categories;
	}	

	public function kunena()
	{
		$ajax		= new Disjax();

		// @task: Get list of categories from Kunena first.
		$categories	= $this->getKunenaCategories();
		$catCnt 	= $this->getKunenaCategoriesCount();

		// @task: Add some logging
		$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_KUNENA_TOTAL_CATEGORIES' , $catCnt ) , 'kunena' );

		$json	= new Services_JSON();
		$items	= array();

		foreach( $categories as $category )
		{
			$items[]	= $category->id;
		}

		$data	= $json->encode( $items );

		// @task: Start migration process, passing back to the AJAX methods
		$ajax->script( 'runMigrationCategory("kunena", ' . $data . ');' );

		return $ajax->send();
	}

	public function jomsocialgroups()
	{
		$ajax		= new Disjax();

		$groups 	= $this->getJomSocialGroups();

		// @task: Add some logging
		$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_JOMSOCIAL_TOTAL_GROUPS' , count( $groups ) ) , 'jomsocialgroups' );


		$json	= new Services_JSON();
		$items	= array();

		foreach( $groups as $group )
		{
			$items[]	= $group->id;
		}

		$data	= $json->encode( $items );

		// @task: Start migration process, passing back to the AJAX methods
		$ajax->script( 'runMigrationCategory("jomsocialgroups", ' . $data . ');' );

		return $ajax->send();
	}

	public function getJomSocialGroups()
	{
		$db 	= DiscussHelper::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__community_groups' );
		$db->setQuery( $query );
		$result 	= $db->loadObjectList();

		return $result;
	}

	public function showMigrationButton( &$ajax )
	{
		$ajax->script( 'EasyDiscuss.$(".migrator-button").show();' );
	}

	public function jomsocialgroupsCategoryItem( $current , $groups )
	{
		$ajax	= new Disjax();

		$group 		= $this->getJomSocialGroup( $current );

		// @task: Skip the category if it has already been migrated.
		if( $this->migrated( 'com_community' , $current , 'groups') && $groups != 'done' )
		{
			$data	= $this->json_encode( $groups );
			$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_JOMSOCIAL_GROUP_MIGRATED_SKIPPING' , $group->name ) , 'jomsocialgroups' );
			$ajax->script( 'runMigrationCategory("jomsocialgroups" , ' . $data . ');' );
			return $ajax->send();
		}

		// @task: Create the category
		$category	= DiscussHelper::getTable( 'Category' );
		$this->mapJomsocialCategory( $group , $category );
		$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_JOMSOCIAL_GROUP_MIGRATED' , $group->name ) , 'jomsocialgroups' );

		$data	= $this->json_encode( $groups );

		// @task: If categories is no longer an array, then it most likely means that there's nothing more to process.
		if( $groups == 'done' )
		{
			$this->log( $ajax , JText::_( 'COM_EASYDISCUSS_MIGRATORS_CATEGORY_MIGRATION_COMPLETED' ) , 'jomsocialgroups' );

			$posts		= $this->getJomsocialPostsIds();
			$data		= $this->json_encode( $posts );

			$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_JOMSOCIAL_TOTAL_DISCUSSIONS' , count( $posts ) ) , 'jomsocialgroups' );

			// @task: Run migration for post items.
			$ajax->script( 'runMigrationItem("jomsocialgroups" , ' . $data . ');' );
			return $ajax->send();
		}

		$ajax->script( 'runMigrationCategory("jomsocialgroups" , ' . $data . ');' );

		$ajax->send();
	}

	public function communitypollsCategoryItem()
	{
		$ajax 		= DiscussHelper::getHelper( 'Ajax' );
		$current 	= JRequest::getVar( 'current' );
		$categories	= JRequest::getVar( 'categories' );

		$cpCategory	= $this->getCPCategory( $current );

		// @task: If categories is no longer an array, then it most likely means that there's nothing more to process.
		if( !$categories && !$current )
		{
			$this->log( $ajax , JText::_( 'COM_EASYDISCUSS_MIGRATORS_CATEGORY_MIGRATION_COMPLETED' ) , 'communitypolls' );

			$posts		= $this->getCPPostsIds();

			$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_COMMUNITY_POLLS_TOTAL_POLLS' , count( $posts ) ) , 'communitypolls' );

			// @task: Run migration for post items.
			$ajax->migratePolls( $posts );

			return $ajax->resolve( 'done' , true );
		}

		// @task: Skip the category if it has already been migrated.
		if( $this->migrated( 'com_communitypolls' , $current , 'category') )
		{
			$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_KUNENA_CATEGORY_MIGRATED_SKIPPING' , $cpCategory->title ) , 'communitypolls' );
		}
		else
		{
			// @task: Create the category
			$category	= DiscussHelper::getTable( 'Category' );
			$this->mapCPCategory( $cpCategory , $category );
			$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_COMMUNITY_POLLS_CATEGORY_MIGRATED' , $cpCategory->title ) , 'communitypolls' );
		}

		$ajax->resolve( $categories , false );
	}

	public function kunenaCategoryItem( $current = "" , $categories = "" )
	{
		$ajax	= new Disjax();

		$kCategory	= $this->getKunenaCategory( $current );

		// @task: If categories is no longer an array, then it most likely means that there's nothing more to process.
		if( $current == 'done' )
		{
			// category migration done. let reset the ordering here.
			$catTbl = DiscussHelper::getTable( 'Category' );
			$catTbl->rebuildOrdering();

			$this->log( $ajax , JText::_( 'COM_EASYDISCUSS_MIGRATORS_CATEGORY_MIGRATION_COMPLETED' ) , 'kunena' );

			$posts		= $this->getKunenaPostsIds();
			// $posts = array( '44034', '44070', '46167' );

			//$data		= $this->json_encode( $posts );
			$data		= implode( '|', $posts );
			$data		= $this->json_encode( $data );

			$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_KUNENA_TOTAL_POSTS' , count( $posts ) ) , 'kunena' );

			if( count( $posts ) <= 0 )
			{
				$ajax->script( 'runMigrationItem("kunena" , "done");' );
			}
			else
			{
				// @task: Run migration for post items.
				$ajax->script( 'runMigrationItem("kunena" , ' . $data . ');' );
			}

			return $ajax->send();
		}

		// @task: Skip the category if it has already been migrated.
		$migratedId = $this->migrated( 'com_kunena' , $current , 'category');
		$category	= DiscussHelper::getTable( 'Category' );


		if( ! $migratedId )
		{
			// @task: Create the category
			$this->mapKunenaCategory( $kCategory , $category );
			$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_KUNENA_CATEGORY_MIGRATED' , $kCategory->name ) , 'kunena' );
		}
		else
		{
			$category->load( $migratedId );
		}

		// now let migrate all the child categories for this parent
		$this->processKunenaCategoryTree( $kCategory, $category );


		if( $migratedId )
		{
			$data	= $this->json_encode( $categories );
			$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_KUNENA_CATEGORY_MIGRATED_SKIPPING' , $kCategory->name ) , 'kunena' );
			$ajax->script( 'runMigrationCategory("kunena" , ' . $data . ');' );
			return $ajax->send();
		}


		$data	= $this->json_encode( $categories );

		$ajax->script( 'runMigrationCategory("kunena" , ' . $data . ');' );

		$ajax->send();
	}



	private function processKunenaCategoryTree( $kCategory, $category )
	{
		$ajax	= new Disjax();

		require_once JPATH_ROOT . '/administrator/components/com_kunena/api.php';

		$columnName = 'parent';

		if( class_exists('KunenaForum') && KunenaForum::version() >= '2.0' )
		{
			$columnName = 'parent_id';
		}


		$db = DiscussHelper::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__kunena_categories' )
				. ' where ' . $db->nameQuote( $columnName ) . ' = ' . $db->Quote( $kCategory->id )
				. ' ORDER BY ' . $db->nameQuote( 'ordering' ) . ' ASC';

		$db->setQuery( $query );
		$result	= $db->loadObjectList();

		if( $result )
		{
			foreach( $result as $kItemCat )
			{
				$subcategory	= DiscussHelper::getTable( 'Category' );

				$migratedId = $this->migrated( 'com_kunena' , $kItemCat->id , 'category');

				if( ! $migratedId )
				{
					$this->mapKunenaCategory( $kItemCat, $subcategory, $category->id );
					//$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_KUNENA_CATEGORY_MIGRATED' , $kItemCat->name ) , 'kunena' );
				}
				else
				{
					$subcategory->load( $migratedId );
				}

				$this->processKunenaCategoryTree( $kItemCat, $subcategory );
			}
		}
		else
		{
			return false;
		}

	}





	public function jomsocialgroupsPostItem( $current , $items )
	{
		$ajax	= new Disjax();

		// @task: Map main discussion from group with EasyDiscuss
		$discussion	= $this->getJomsocialPost( $current );
		$item		= DiscussHelper::getTable( 'Post' );

		// @task: Skip the category if it has already been migrated.
		if( $this->migrated( 'com_community' , $current , 'discussions') && $items != 'done' )
		{
			$data	= $this->json_encode( $items );
			$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_POST_MIGRATED_SKIPPING' , $discussion->title ) , 'jomsocialgroups' );
			$ajax->script( 'runMigrationItem("jomsocialgroups" , ' . $data . ');' );
			return $ajax->send();
		}


		$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_POST_MIGRATED' , $discussion->title ) , 'jomsocialgroups' );
		$this->mapJomsocialItem( $discussion , $item );

		// @task: Once the post is migrated successfully, we'll need to migrate the child items.
		$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_POST_REPLIES_MIGRATED' , $discussion->title ) , 'jomsocialgroups' );
		$this->mapJomsocialItemChilds( $discussion , $item );


		// @task: If categories is no longer an array, then it most likely means that there's nothing more to process.
		if( !is_array( $items ) )
		{
			$this->log( $ajax , JText::_( 'COM_EASYDISCUSS_MIGRATORS_MIGRATION_COMPLETED' ) , 'jomsocialgroups' );
			$this->showMigrationButton( $ajax );
			return $ajax->send();
		}

		$data	= $this->json_encode( $items );

		$ajax->script( 'runMigrationItem("jomsocialgroups" , ' . $data . ');' );

		$ajax->send();
	}

	public function communitypollsPostItem()
	{
		$ajax 	= DiscussHelper::getHelper( 'Ajax' );

		$current 	= JRequest::getVar( 'current' );
		$items		= JRequest::getVar( 'items' );


		// Map community polls item with EasyDiscuss item.
		$cpItem 	= $this->getCPPost( $current );
		$item		= DiscussHelper::getTable( 'Post' );

		// @task: Skip the category if it has already been migrated.
		if( $this->migrated( 'com_communitypolls' , $current , 'post') )
		{
			$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_POST_MIGRATED_SKIPPING' , $cpItem->id ) , 'communitypolls' );

			return $ajax->resolve( $items );
		}

		$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_COMMUNITY_POLLS_POLL_MIGRATED' , $cpItem->id ) , 'communitypolls' );
		$this->mapCPItem( $cpItem , $item );

		return $ajax->resolve( $items );
	}

	public function kunenaPostReplies()
	{
		$ajax	= new Disjax();

		$replies = $this->getKunenaReplies();

		if( !$replies || count( $replies ) <= 0 )
		{
			// @task: If categories is no longer an array, then it most likely means that there's nothing more to process.
			$this->log( $ajax , JText::_( 'COM_EASYDISCUSS_MIGRATORS_MIGRATION_COMPLETED' ) , 'kunena' );
			$this->showMigrationButton( $ajax );
			return $ajax->send();

		}

		$db		= DiscussHelper::getDBO();

		foreach( $replies as $kItem )
		{
			// getting parent thread.
			$query = 'select `first_post_id` from `#__kunena_topics` where id = ' . $db->Quote( $kItem->thread );
			$db->setQuery( $query );
			$fPostId = $db->loadResult();


			$result = '';
			if( $fPostId )
			{
				$query = 'select * from `#__discuss_migrators` where `type` = ' . $db->Quote( 'post' ) . ' and `component` = ' . $db->Quote( 'com_kunena' ) . ' and external_id = ' . $db->Quote( $fPostId );

				$db->setQuery( $query );
				$result = $db->loadObject();
			}

			if(! $result )
			{
				// this mean its might be a thread post and somehow it doesnt get migrated.
				// lets migrate.
				$item	= DiscussHelper::getTable( 'Post' );

				$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_POST_MIGRATED' , $kItem->id ) , 'kunena' );
				$this->mapKunenaItem( $kItem , $item );

				// adding poll items to this thread
				$this->mapKunenaItemPolls( $kItem, $item );

			}
			else
			{
				$parent	= DiscussHelper::getTable( 'Post' );
				$parent->load( $result->internal_id );

				$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_POST_MIGRATED' , $kItem->id ) , 'kunena' );
				$citem	= DiscussHelper::getTable( 'Post' );
				$this->mapKunenaItem( $kItem , $citem , $parent );
			}

		}

		$ajax->script( 'runMigrationReplies("kunena");' );
		return $ajax->send();
	}

	public function getKunenaReplies( $countOnly = false )
	{
		$db		= DiscussHelper::getDBO();

		$query = '';
		if( $countOnly )
		{
			$query	= 'SELECT count(1) FROM ' . $db->nameQuote( '#__kunena_messages' ) . ' as a';
		}
		else
		{
			$query	= 'SELECT a.* FROM ' . $db->nameQuote( '#__kunena_messages' ) . ' as a';
		}

		$query .= ' where not exists ( select b.`external_id` from `#__discuss_migrators` as b ';
		$query .= ' 						where a.`id` = b.`external_id` and b.`component` = ' . $db->Quote( 'com_kunena' ) . ' and b.`type` = ' . $db->Quote( 'post') . ')';

		if( !$countOnly )
		{
			$query .= ' limit 10';
		}

		$db->setQuery( $query );

		$result = '';

		if( $countOnly )
		{
			$result = $db->loadResult();
		}
		else
		{
			$result = $db->loadObjectList();
		}


		return $result;
	}


	public function kunenaPostItem( $current , $items)
	{
		$ajax	= new Disjax();


		// @task: If categories is no longer an array, then it most likely means that there's nothing more to process.
		if( $current == 'done' )
		{
			$this->log( $ajax , JText::_( 'COM_EASYDISCUSS_MIGRATORS_MIGRATION_COMPLETED' ) , 'kunena' );

			// lets check if there is any new replies or not.
			$posts = $this->getKunenaReplies( true );
			$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_KUNENA_TOTAL_POSTS' , $posts ) , 'kunena' );

			$ajax->script( 'runMigrationReplies("kunena");' );

			return $ajax->send();
		}

		// lets split the data into array
		// $items = explode( ',', $items );


		// @task: Map kunena post item with EasyDiscuss items.
		$kItem	= $this->getKunenaPost( $current );
		$item	= DiscussHelper::getTable( 'Post' );

		// @task: Skip the category if it has already been migrated.
		if( $this->migrated( 'com_kunena' , $current , 'post') )
		{

			$data		= $this->json_encode( $items );
			$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_POST_MIGRATED_SKIPPING' , $kItem->id ) , 'kunena' );

			$ajax->script( 'runMigrationItem("kunena" , ' . $data . ');' );
			return $ajax->send();
		}


		$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_POST_MIGRATED' , $kItem->id ) , 'kunena' );
		$this->mapKunenaItem( $kItem , $item );

		// @task: Once the post is migrated successfully, we'll need to migrate the child items.
		$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_POST_REPLIES_MIGRATED' , $kItem->id ) , 'kunena' );
		$this->mapKunenaItemChilds( $kItem , $item );


		// adding poll items to this thread
		$this->mapKunenaItemPolls( $kItem, $item );


		// @task: If categories is no longer an array, then it most likely means that there's nothing more to process.
		//if( !is_array( $items ) )
		if( !$items )
		{
			$this->log( $ajax , JText::_( 'COM_EASYDISCUSS_MIGRATORS_MIGRATION_COMPLETED' ) , 'kunena' );
			$this->showMigrationButton( $ajax );
			return $ajax->send();
		}

		$data	= $this->json_encode( $items );

		$ajax->script( 'runMigrationItem("kunena" , ' . $data . ');' );

		$ajax->send();
	}

	private function json_encode( $data )
	{
		$json	= new Services_JSON();
		$data	= $json->encode( $data );

		return $data;
	}

	private function json_decode( $data )
	{
		$json	= new Services_JSON();
		$data	= $json->decode( $data );

		return $data;
	}

	private function log( &$ajax , $message , $type )
	{
		if( $ajax instanceof DiscussAjaxHelper )
		{
			$ajax->updateLog( $message );
		}
		else
		{
			$ajax->script( 'appendLog("' . $type . '" , "' . $message . '");' );
		}
	}

	private function mapCPCategory( $cpCategory , &$category )
	{
		$category->set( 'title'			, $cpCategory->title );
		$category->set( 'alias'			, $cpCategory->alias );
		$category->set( 'published'		, $cpCategory->published );
		$category->set( 'parent_id'		, 0 );

		// @task: Since CP does not store the creator of the category, we'll need to assign a default owner.
		$category->set( 'created_by'	, DiscussHelper::getDefaultSAIds() );

		// @TODO: Detect if it has a parent id and migrate according to the category tree.
		$category->store( true );

		$this->added( 'com_communitypolls' , $category->id , $cpCategory->id , 'category' );
	}

	private function mapKunenaCategory( $kCategory , &$category, $parentId = 0 )
	{
		$parentId = ( $parentId ) ? $parentId : 0;

		$category->set( 'title'			, $kCategory->name );

		$category->set( 'description'	, $kCategory->description );
		$category->set( 'published'		, $kCategory->published );
		$category->set( 'parent_id'		, $parentId );

		if( $parentId == 0 )
		{
			$category->set( 'container'		, 1);
		}

		// @task: Since Kunena does not store the creator of the category, we'll need to assign a default owner.
		$category->set( 'created_by'	, DiscussHelper::getDefaultSAIds() );

		// @TODO: Detect if it has a parent id and migrate according to the category tree.
		$category->store( true );

		$this->added( 'com_kunena' , $category->id , $kCategory->id , 'category' );
	}

	private function mapJomsocialCategory( $group , &$category )
	{
		$category->set( 'title'			, $group->name );

		$category->set( 'description'	, $group->description );
		$category->set( 'published'		, $group->published );
		$category->set( 'parent_id'		, 0 );

		// @task: Since Kunena does not store the creator of the category, we'll need to assign a default owner.
		$category->set( 'created_by'	, $group->ownerid );

		// @TODO: Detect if it has a parent id and migrate according to the category tree.
		$category->store( true );

		$this->added( 'com_community' , $category->id , $group->id , 'groups' );
	}

	private function mapJomsocialItem( $discussion , &$item , &$parent = null )
	{
		require_once JPATH_ROOT . '/components/com_community/libraries/core.php';

		// @task: If this is a child post, we definitely have the item's id.
		if( $parent )
		{
			$item->set( 'parent_id'	, $parent->id );
			$user 		= CFactory::getUser( $discussion->post_by );

			$content 	= $discussion->comment;

			$item->set( 'title' 		, 'RE: ' . $parent->title );
			$item->set( 'created'	 	, DiscussHelper::getDate( $discussion->date )->toMySQL() );
			$item->set( 'replied' 		, DiscussHelper::getDate( $discussion->date )->toMySQL() );
			$item->set( 'category_id' 	, 0 );
			$item->set( 'islock'		, 0 );

			$type 		= 'replies';
		}
		else
		{
			$user 		= CFactory::getUser( $discussion->creator );
			$content 	= $this->getJomsocialMessage( $discussion );

			$item->set( 'parent_id'		, 0 );
			$item->set( 'title' 		, $discussion->title );
			$item->set( 'created'	 	, DiscussHelper::getDate( $discussion->created )->toMySQL() );
			$item->set( 'replied' 		, DiscussHelper::getDate( $discussion->lastreplied )->toMySQL() );
			$item->set( 'category_id' 	, $this->getJomsocialNewCategory( $discussion ) );
			$item->set( 'islock'		, $discussion->lock );

			$type 		= 'discussions';
		}

		$item->set( 'content'		, $content );
		$item->set( 'hits'			, 0 );
		$item->set( 'user_id'		, $user->id );
		$item->set( 'user_type' 	, DISCUSS_POSTER_MEMBER );
		$item->set( 'poster_name'	, $user->getDisplayName() );
		$item->set( 'poster_email'	, $user->email );
		$item->set( 'published'		, DISCUSS_ID_PUBLISHED );

		$item->store();

		$this->added( 'com_community' , $item->id , $discussion->id , $type );
	}

	private function mapCPItem( $cpItem , &$item , &$parent = null )
	{

		$item->set( 'title' 		, $cpItem->title );
		$item->set( 'alias' 		, $cpItem->alias );
		$item->set( 'content'		, $cpItem->description );
		$item->set( 'category_id' 	, $this->getCPNewCategory( $cpItem ) );
		$item->set( 'user_id'		, $cpItem->created_by );
		$item->set( 'user_type' 	, DISCUSS_POSTER_MEMBER );
		$item->set( 'created'	 	, $cpItem->created );
		$item->set( 'modified'	 	, $cpItem->created );
		$item->set( 'parent_id'		, 0 );
		$item->set( 'published'		, DISCUSS_ID_PUBLISHED );
		$item->store();

		// Get poll answers
		$answers 	= $this->getCPAnswers( $cpItem );

		if( $answers )
		{
			// Create a new poll question
			$pollQuestion 		= DiscussHelper::getTable( 'PollQuestion' );
			$pollQuestion->title 	= $cpItem->title;
			$pollQuestion->post_id 	= $item->id;
			$pollQuestion->multiple	= $cpItem->type == 'checkbox' ? true : false;

			$pollQuestion->store();

			foreach( $answers as $answer )
			{
				$poll = DiscussHelper::getTable( 'Poll' );

				$poll->post_id 	= $item->id;
				$poll->value 	= $answer->title;
				$poll->count 	= $answer->votes;

				$poll->store();

				// Get all voters information
				$voters 		= $this->getCPVoters( $answer->id );

				foreach($voters as $voter)
				{
					$pollUser 	= DiscussHelper::getTable( 'PollUser' );
					$pollUser->user_id 	= $voter->voter_id;
					$pollUser->poll_id 	= $poll->id;

					$pollUser->store();
				}
			}
		}


		$this->added( 'com_communitypolls' , $item->id , $cpItem->id , 'post' );
	}

	private function mapKunenaItem( $kItem , &$item , $parent = null )
	{
		$content	= $this->getKunenaMessage( $kItem );

		$hits 			= ( isset( $kItem->threadhits ) ) ? $kItem->threadhits : $kItem->hits;
		$lastreplied 	= ( isset( $kItem->threadlastreplied ) ) ? $kItem->threadlastreplied : $kItem->time;

		$item->set( 'content'		, $content );

		$subject = $kItem->subject;

		if (! $parent && $kItem->threadsubject) {
			$subject = $kItem->threadsubject;
		}

		$item->set( 'title' 		, $subject );
		$item->set( 'category_id' 	, $this->getNewCategory( $kItem ) );
		$item->set( 'user_id'		, $kItem->userid );
		$item->set( 'user_type' 	, DISCUSS_POSTER_MEMBER );
		$item->set( 'hits'			, $hits );
		$item->set( 'created'	 	, DiscussHelper::getDate( $kItem->time )->toMySQL() );
		$item->set( 'modified' 		, DiscussHelper::getDate( $kItem->time )->toMySQL() );
		$item->set( 'replied' 		, DiscussHelper::getDate( $lastreplied )->toMySQL() );
		$item->set( 'poster_name'	, $kItem->name );
		$item->set( 'ip'			, $kItem->ip );
		$item->set( 'content_type'	, 'bbcode' );
		$item->set( 'parent_id'		, 0 );

		// @task: If this is a child post, we definitely have the item's id.
		if( $parent )
		{
			$item->set( 'parent_id'	, $parent->id );
		}

		$item->set( 'islock'		, $kItem->locked );
		$item->set( 'poster_email'	, $kItem->email );


		$state = ( $kItem->hold == 0 ) ? DISCUSS_ID_PUBLISHED : DISCUSS_ID_UNPUBLISHED;
		$item->set( 'published'		, $state );

		if( !$kItem->userid )
		{
			$item->set( 'user_type' , DISCUSS_POSTER_GUEST );
		}

		$item->store();

		// @task: Get attachments
		$files	= $this->getKunenaAttachments( $kItem );

		if( $files )
		{
			foreach( $files as $kAttachment )
			{
				$attachment	= DiscussHelper::getTable( 'Attachments');

				$attachment->set( 'uid' 	, $item->id );
				$attachment->set( 'size'	, $kAttachment->size );
				$attachment->set( 'title'	, $kAttachment->filename );
				$attachment->set( 'type'	, $item->getType() );
				$attachment->set( 'published',	DISCUSS_ID_PUBLISHED );
				$attachment->set( 'mime'	, $kAttachment->filetype );

				// Regenerate the path

				$isJoomla30 = DiscussHelper::isJoomla30();

				if( $isJoomla30 )
				{
					// JUtility::getHash is deprecated
					$path	= JApplication::getHash( $kAttachment->filename . DiscussHelper::getDate()->toMySQL() );
				}
				else
				{
					$path	= JUtility::getHash( $kAttachment->filename . DiscussHelper::getDate()->toMySQL() );
				}


				$attachment->set( 'path'	, $path );

				// Copy files over.
				$config		= DiscussHelper::getConfig();

				$folderPath = DISCUSS_MEDIA . '/' . trim( $config->get( 'attachment_path' ) , DIRECTORY_SEPARATOR ) ;
				$storage	= $folderPath . '/' . $path;
				$kStorage	= JPATH_ROOT . '/' . rtrim( $kAttachment->folder , '/' )  . '/' . $kAttachment->filename;

				// create folder if it not exists
				if(! JFolder::exists( $folderPath ) )
				{
					JFolder::create( $folderPath );
					JFile::copy( DISCUSS_ROOT . '/index.html' , $path . '/index.html' );
				}

				if( JFile::exists( $kStorage ) )
				{
					JFile::copy( $kStorage , $storage );

					if( DiscussHelper::getHelper( 'Image' )->isImage( $kAttachment->filename ) )
					{
						require_once DISCUSS_CLASSES . '/simpleimage.php';
						$image	= new SimpleImage;

						@$image->load( $kStorage );
						@$image->resizeToFill( 160 , 120 );
						@$image->save( $storage . '_thumb', $image->image_type);
					}
				}

				// @task: Since Kunena does not store this, we need to generate the own creation timestamp.
				$attachment->set( 'created'	, DiscussHelper::getDate()->toMySQL() );

				$attachment->store();
			}
		}

		//perform cleanup


		$this->added( 'com_kunena' , $item->id , $kItem->id , 'post' );
	}

	private function mapKunenaItemPolls( $kItem, $item )
	{
		$db		= DiscussHelper::getDBO();

		$query = 'select * from `#__kunena_polls` where `threadid` = ' . $db->Quote( $kItem->id );

		// echo $query;

		$db->setQuery( $query );
		$kPolls = $db->loadObjectList();

		// var_dump( $kPolls );
		// exit;

		if( $kPolls )
		{
			foreach( $kPolls as $kPoll )
			{
				$pollQuestion	= DiscussHelper::getTable( 'PollQuestion');

				$pollQuestion->post_id 	= $item->id;
				$pollQuestion->title 	= $kPoll->title;
				$pollQuestion->multiple = 0;
				$pollQuestion->locked 	= 0;

				$pollQuestion->store();

				// get the poll options.
				$query = 'select * from `#__kunena_polls_options` where `pollid` = ' . $db->Quote( $kPoll->id );
				$db->setQuery( $query );
				$kPollsOptions = $db->loadObjectList();

				if( $kPollsOptions )
				{
					foreach( $kPollsOptions as $kPollOption )
					{
						$poll	= DiscussHelper::getTable( 'Poll' );

						$poll->post_id 			= $item->id;
						$poll->value 			= $kPollOption->text;
						$poll->count 			= $kPollOption->votes;

						$poll->store();

						// now we need to insert the users who vote for this option.
						$query = 'select * from `#__kunena_polls_users` where `pollid` = ' . $db->Quote( $kPoll->id );
						$query .= ' and `lastvote` = ' . $db->Quote( $kPollOption->id );

						$db->setQuery( $query );
						$kPollsUsers = $db->loadObjectList();

						if( $kPollsUsers )
						{
							foreach( $kPollsUsers as $kPollUser )
							{
								$pollUser	= DiscussHelper::getTable( 'PollUser' );

								$pollUser->poll_id = $poll->id;
								$pollUser->user_id = $kPollUser->userid;

								$pollUser->store();
							}

						} // if kPollsUsers

					} // foreach kPollsOptions

				} // if kPollsOptions

			} // foreach kPolls

		} // if kPolls

	}

	private function mapKunenaItemChilds( $kItem , $parent )
	{
		$items	= $this->getKunenaPosts( $kItem );

		if( !$items )
		{
			return false;
		}

		foreach( $items as $kChildItem )
		{
			$item	= DiscussHelper::getTable( 'Post' );
			$this->mapKunenaItem( $kChildItem , $item , $parent );
		}
	}

	private function mapJomsocialItemChilds( $discussion , &$parent )
	{
		$items	= $this->getJomSocialPosts( $discussion );

		if( !$items )
		{
			return false;
		}

		foreach( $items as $discussChildItem )
		{
			$item	= DiscussHelper::getTable( 'Post' );
			$this->mapJomsocialItem( $discussChildItem , $item , $parent );
		}
	}

	private function added( $component , $internalId , $externalId , $type )
	{
		$migrator	= DiscussHelper::getTable( 'Migrators' );
		$migrator->set( 'component' 	, $component );
		$migrator->set( 'external_id'	, $externalId );
		$migrator->set( 'internal_id'	, $internalId );
		$migrator->set( 'type'			, $type );

		return $migrator->store();
	}

	private function getNewCategory( $kItem )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT ' . $db->nameQuote( 'internal_id' ) . ' '
				. 'FROM ' . $db->nameQuote( '#__discuss_migrators' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'external_id' ) . ' = ' . $db->Quote( $kItem->catid ) . ' '
				. 'AND ' . $db->nameQuote( 'type' ) . ' = ' . $db->Quote( 'category' ) . ' '
				. 'AND ' . $db->nameQuote( 'component' ) . ' = ' . $db->Quote( 'com_kunena' );

		$db->setQuery( $query );
		$categoryId	= $db->loadResult();

		return $categoryId;
	}

	private function getCPNewCategory( $cpItem )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT ' . $db->nameQuote( 'internal_id' ) . ' '
				. 'FROM ' . $db->nameQuote( '#__discuss_migrators' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'external_id' ) . ' = ' . $db->Quote( $cpItem->category ) . ' '
				. 'AND ' . $db->nameQuote( 'type' ) . ' = ' . $db->Quote( 'category' ) . ' '
				. 'AND ' . $db->nameQuote( 'component' ) . ' = ' . $db->Quote( 'com_communitypolls' );

		$db->setQuery( $query );
		$categoryId	= $db->loadResult();

		return $categoryId;
	}

	private function getJomsocialNewCategory( $discussion )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT ' . $db->nameQuote( 'internal_id' ) . ' '
				. 'FROM ' . $db->nameQuote( '#__discuss_migrators' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'external_id' ) . ' = ' . $db->Quote( $discussion->groupid ) . ' '
				. 'AND ' . $db->nameQuote( 'type' ) . ' = ' . $db->Quote( 'groups' ) . ' '
				. 'AND ' . $db->nameQuote( 'component' ) . ' = ' . $db->Quote( 'com_community' );

		$db->setQuery( $query );
		$categoryId	= $db->loadResult();

		return $categoryId;
	}

	private function getKunenaMessage( $kItem )
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'SELECT ' . $db->nameQuote( 'message' ) . ' FROM ' . $db->nameQuote( '#__kunena_messages_text' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'mesid' ) . '=' . $db->Quote( $kItem->id );
		$db->setQuery( $query );

		$message	= $db->loadResult();

		// @task: Replace unwanted bbcode's.
		$message	= preg_replace( '/\[attachment\="?(.*?)"?\](.*?)\[\/attachment\]/ms' , '' , $message );
		$message	= preg_replace( '/\[quote=(.+?)\d+\]/ms' , '[quote]' , $message );

		return $message;
	}

	private function getJomsocialMessage( $discussion )
	{
		$config 	= DiscussHelper::getConfig();

		if( $config->get( 'layout_editor') == 'bbcode' )
		{
			// Convert content to bbcode
			require_once DISCUSS_HELPERS . '/parser.php';
			return EasyDiscussParser::html2bbcode( $discussion->message );
		}

		return $discussion->message;
	}

	private function getCPAnswers( $cpItem )
	{
		$db 	= DiscussHelper::getDBO();

		$query 	= 'SELECT * FROM `#__jcp_options` WHERE `poll_id`=' . $db->Quote( $cpItem->id );
		$db->setQuery( $query );

		return $db->loadObjectList();
	}

	private function getKunenaPostsIds()
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'SELECT a.`id` FROM ' . $db->nameQuote( '#__kunena_messages' ) . ' as a'
				. ' inner join `#__kunena_topics` as t on a.`thread` = t.`id` and a.`id` = t.`first_post_id`'
				. ' where not exists ( select b.`external_id` from `#__discuss_migrators` as b where a.`id` = b.`external_id` and b.`component` = ' . $db->Quote( 'com_kunena' ) . ' and b.`type` = ' . $db->Quote( 'post') . ')';

		$db->setQuery( $query );

		return $db->loadResultArray();
	}

	private function getCPPostsIds()
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT `id` FROM ' . $db->nameQuote( '#__jcp_polls' );
		$db->setQuery( $query );
		return $db->loadResultArray();
	}

	private function getJomsocialPostsIds()
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT `id` FROM ' . $db->nameQuote( '#__community_groups_discuss' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'parentid' ) . '=' . $db->Quote( 0 );
		$db->setQuery( $query );
		return $db->loadResultArray();
	}

	private function getKunenaPost( $id )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT a.*, b.`hits` as `threadhits`, b.`last_post_time` as `threadlastreplied`, b.`subject` as `threadsubject` FROM ' . $db->nameQuote( '#__kunena_messages' ) . ' as a'
				. ' LEFT JOIN ' . $db->nameQuote( '#__kunena_topics' ) . ' as b'
				. ' on a.`thread` = b.`id`'
				. ' WHERE a.' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $id );

		$db->setQuery( $query );
		$item	= $db->loadObject();

		return $item;
	}

	private function getCPPost( $id )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__jcp_polls' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $id );
		$db->setQuery( $query );
		$item	= $db->loadObject();

		return $item;
	}

	private function getCPVoters( $answerId )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__jcp_votes' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'option_id' ) . '=' . $db->Quote( $answerId );
		$db->setQuery( $query );
		$item	= $db->loadObjectList();

		return $item;
	}

	private function getJomsocialPost( $id )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__community_groups_discuss' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $id );
		$db->setQuery( $query );
		$item	= $db->loadObject();

		return $item;
	}

	private function getKunenaAttachments( $kItem )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__kunena_attachments' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'mesid' ) . '=' . $db->Quote( $kItem->id );
		$db->setQuery( $query );
		$attachments	= $db->loadObjectList();

		return $attachments;
	}

	private function getJomsocialPosts( $discussion = null , $category = null )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__community_wall' );

		$query	.= ' WHERE ' . $db->nameQuote( 'contentid' ) . ' = ' . $db->Quote( $discussion->id );
		$query	.= ' AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( 'discussions' );


		$db->setQuery( $query );

		$result	= $db->loadObjectList();

		if( !$result )
		{
			return false;
		}

		return $result;
	}

	private function getKunenaPosts( $kItem = null , $kCategory = null )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__kunena_messages' );

		if( !is_null( $kItem ) )
		{
			$query	.= ' WHERE ' . $db->nameQuote( 'thread' ) . ' = ' . $db->Quote( $kItem->thread );
			$query	.= ' AND ' . $db->nameQuote( 'id') . '!=' . $db->Quote( $kItem->id );
		}

		$query	.= ' ORDER BY `time` asc';


		$db->setQuery( $query );
		$result	= $db->loadObjectList();

		if( !$result )
		{
			return false;
		}

		return $result;
	}

	private function getJomSocialGroup( $id )
	{
		require_once JPATH_ROOT . '/components/com_community/libraries/core.php';

		JTable::addIncludePath( JPATH_ROOT . '/components/com_community/tables' );
		$group 	= JTable::getInstance( 'Group' , 'CTable' );
		$group->load( $id );

		return $group;
	}

	private function getKunenaCategory( $id )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__kunena_categories' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $id );
		$db->setQuery( $query );

		return $db->loadObject();
	}

	private function getCPCategory( $id )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__jcp_categories' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $id );
		$db->setQuery( $query );

		return $db->loadObject();
	}

	/**
	 * Determines if an item is already migrated
	 */
	private function migrated( $component , $externalId , $type )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT ' . $db->nameQuote( 'internal_id' )
				. 'FROM ' . $db->nameQuote( '#__discuss_migrators' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'external_id' ) . ' = ' . $db->Quote( $externalId ) . ' '
				. 'AND ' . $db->nameQuote( 'type' ) . ' = ' . $db->Quote( $type ) . ' '
				. 'AND ' . $db->nameQuote( 'component' ) . ' = ' . $db->Quote( $component );
		$db->setQuery( $query );

		$exists	= $db->loadResult();
		return $exists;
	}

	/**
	 * Retrieves a list of categories in Kunena
	 *
	 * @param	null
	 * @return	string	A JSON string
	 **/
	private function getKunenaCategories()
	{
		require_once JPATH_ROOT . '/administrator/components/com_kunena/api.php';

		$columnName = 'parent';

		if( class_exists('KunenaForum') && KunenaForum::version() >= '2.0' )
		{
			$columnName = 'parent_id';
		}

		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__kunena_categories' )
				. ' where ' . $db->nameQuote( $columnName ) . ' = ' . $db->Quote( '0' )
				. ' ORDER BY ' . $db->nameQuote( 'ordering' ) . ' ASC';

		$db->setQuery( $query );
		$result	= $db->loadObjectList();

		if( !$result )
		{
			return false;
		}

		return $result;
	}

	private function getKunenaCategoriesCount()
	{
		$db		= DiscussHelper::getDBO();

		$query = 'select count(1) from ' . $db->nameQuote( '#__kunena_categories' );
		$db->setQuery( $query );
		$result	= $db->loadResult();

		if( !$result )
		{
			return 0;
		}

		return $result;

	}


	/**
	 * Retrieves a list of categories in Community Polls
	 *
	 * @param	null
	 * @return	string	A JSON string
	 **/
	private function getCPCategories()
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__jcp_categories' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'parent_id' ) . ' > ' . $db->Quote( 0 ) . ' '
				. 'ORDER BY ' . $db->nameQuote( 'title' ) . ' ASC';

		$db->setQuery( $query );
		$result	= $db->loadObjectList();

		if( !$result )
		{
			return false;
		}

		return $result;
	}

	public function vBulletin()
	{
		$ajax		= new Disjax();

		// @task: Get list of categories from vBulletin first.
		$categories	= $this->getVBulletinCategories();

		// @task: Add some logging
		$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_VBULLETIN_TOTAL_CATEGORIES' , count( $categories ) ) , 'vBulletin' );

		$json	= new Services_JSON();
		$items	= array();


		if( $categories )
		{
			foreach( $categories as $category )
			{
				$items[]	= $category->forumid;
			}
		}

		$data	= $json->encode( $items );
		// @task: Start migration process, passing back to the AJAX methods
		// goto this function vBulletinCategoryItem()
		$ajax->script( 'runMigrationCategory("vBulletin", ' . $data . ');' );
		return $ajax->send();
	}


	/*
	 * Get parent categories
	 */
	public function getVBulletinCategories()
	{
		// Need to change the prefix
		$db		= DiscussHelper::getDBO();
		$prefix = DiscussHelper::getConfig()->get( 'migrator_vBulletin_prefix' );

		$query	= 'SELECT `forumid` FROM ' . $db->nameQuote( $prefix . 'forum' )
				. ' where `parentid` <= ' . $db->Quote( '0' )
				. ' ORDER BY ' . $db->nameQuote( 'displayorder' ) . ' ASC';
		$db->setQuery( $query );
		$result	= $db->loadObjectList();

		if( !$result )
		{
			return false;
		}

		return $result;
	}

	public function vBulletinCategoryItem( $current = "" , $categories = "" )
	{
		$ajax		= new Disjax();
		$vCategory	= $this->getVBulletinCategory( $current );


		// @task: If categories is no longer an array, then it most likely means that there's nothing more to process.
		if( $current == 'done' )
		{
			$this->log( $ajax , JText::_( 'COM_EASYDISCUSS_MIGRATORS_CATEGORY_MIGRATION_COMPLETED' ) , 'vBulletin' );

			// Get all posts
			$posts		= $this->getVBulletinPostsIds( true );

			// total number of posts
			$data		= $this->json_encode( $posts );

			$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_VBULLETIN_TOTAL_POSTS' , $posts ) , 'vBulletin' );

			// @task: Run migration for post items.
			$ajax->script( 'runMigrationItem("vBulletin" , ' . $data . ');' );
			return $ajax->send();
		}

		// perform some clean up here.
		$vCategory->title = strip_tags( $vCategory->title );

		// @task: Skip the category if it has already been migrated.
		if( $this->migrated( 'vBulletin' , $current , 'category') )
		{
			$data	= $this->json_encode( $categories );
			$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_VBULLETIN_CATEGORY_MIGRATED_SKIPPING' , $vCategory->title ) , 'vBulletin' );
			$ajax->script( 'runMigrationCategory("vBulletin" , ' . $data . ');' );
			return $ajax->send();
		}

		// @task: Create the category
		$category	= DiscussHelper::getTable( 'Category' );
		$this->mapVBulletinCategory( $vCategory , $category );


		// process childs categories here.
		$this->processVBulletinCategoryTree( $vCategory, $category );


		$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_VBULLETIN_CATEGORY_MIGRATED' , $vCategory->title ) , 'vBulletin' );

		$data	= $this->json_encode( $categories );
		$ajax->script( 'runMigrationCategory("vBulletin" , ' . $data . ');' );
		$ajax->send();
	}


	private function processVBulletinCategoryTree( $vCategory, $category )
	{
		$ajax	= new Disjax();

		$db = DiscussHelper::getDBO();
		$prefix = DiscussHelper::getConfig()->get( 'migrator_vBulletin_prefix' );


		$query	= 'SELECT * FROM ' . $db->nameQuote( $prefix . 'forum' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'parentid' ) . '=' . $db->Quote( $vCategory->forumid )
				. ' ORDER BY ' . $db->nameQuote( 'displayorder' ) . ' ASC';

		$db->setQuery( $query );

		$db->setQuery( $query );
		$result	= $db->loadObjectList();

		if( $result )
		{
			foreach( $result as $vItemCat )
			{
				$subcategory	= DiscussHelper::getTable( 'Category' );

				$migratedId = $this->migrated( 'vBulletin' , $vItemCat->forumid , 'category');

				if( ! $migratedId )
				{
					$this->mapVBulletinCategory( $vItemCat, $subcategory, $category->id );
					//$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_KUNENA_CATEGORY_MIGRATED' , $kItemCat->name ) , 'kunena' );
				}
				else
				{
					$subcategory->load( $migratedId );
				}

				$this->processVBulletinCategoryTree( $vItemCat, $subcategory );
			}
		}
		else
		{
			return false;
		}

	}

	public function getVBulletinCategory( $id )
	{
		$db		= DiscussHelper::getDBO();
		$prefix = DiscussHelper::getConfig()->get( 'migrator_vBulletin_prefix' );

		$query	= 'SELECT * FROM ' . $db->nameQuote( $prefix . 'forum' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'forumid' ) . '=' . $db->Quote( $id );
		$db->setQuery( $query );

		return $db->loadObject();
	}

	private function mapVBulletinCategory( $vCategory , &$category, $parentId = 0 )
	{
		$parentId = ( $parentId ) ? $parentId : 0;

		// @task: Since vBulletin does not store the creator of the category, we'll need to assign a default owner.
		$category->set( 'created_by'	, DiscussHelper::getDefaultSAIds() );
		$category->set( 'title'			, strip_tags( $vCategory->title ) );
		$category->set( 'description'	, $vCategory->description );
		$category->set( 'published'		, 1 );
		$category->set( 'parent_id'		, $parentId );

		// @TODO: Detect if it has a parent id and migrate according to the category tree.
		$category->store( true );

		$this->added( 'vBulletin' , $category->id , $vCategory->forumid , 'category' );
	}


	private function getVBulletinPostsIds( $countOnly = false )
	{
		// Get the posts
		$db		= DiscussHelper::getDBO();
		$prefix = DiscussHelper::getConfig()->get( 'migrator_vBulletin_prefix' );


		// $query	= 'SELECT a.`postid` FROM ' . $db->nameQuote( $prefix . 'post' ) . ' as a'
		// 		. ' where not exists ( select b.`external_id` from `#__discuss_migrators` as b where a.`postid` = b.`external_id` and b.`component` = ' . $db->Quote( 'vBulletin' ) . ' and b.`type` = ' . $db->Quote( 'post') . ')'
		// 		. ' and a.`parentid` = ' . $db->Quote( '0' );

		$query	= 'SELECT ';
		if ($countOnly) {
			$query .= ' count(1)';
		} else {
			$query .= ' a.`postid`';
		}
		$query .= ' FROM ' . $db->nameQuote( $prefix . 'post' ) . ' as a';
		$query .= ' inner join ' . $db->nameQuote( $prefix . 'thread' ) . ' as t on a.`postid` = t.`firstpostid`';
		$query .= ' where not exists ( select b.`external_id` from `#__discuss_migrators` as b where a.`postid` = b.`external_id` and b.`component` = ' . $db->Quote( 'vBulletin' ) . ' and b.`type` = ' . $db->Quote( 'post') . ')';

		if (! $countOnly) {
			$query .= ' limit 25';
		}

		$db->setQuery( $query );
		$result = $countOnly ? $db->loadResult() : $db->loadResultArray();
		// $result = array();

		return $result;
	}

	public function vBulletinPostItem( $total )
	{
		$ajax	= new Disjax();

		// @task: If categories is no longer an array, then it most likely means that there's nothing more to process.
		if( $total == 'done' )
		{
			$this->log( $ajax , JText::_( 'COM_EASYDISCUSS_MIGRATORS_MIGRATION_COMPLETED' ) , 'vBulletin' );

			$this->showMigrationButton( $ajax );
			return $ajax->send();
		}

		// lets get the thread items
		$items = $this->getVBulletinPostsIds();

		if (!$items) {
			// no more items
			$this->log( $ajax , JText::_( 'COM_EASYDISCUSS_MIGRATORS_MIGRATION_COMPLETED' ) , 'vBulletin' );
			$this->showMigrationButton( $ajax );
			return $ajax->send();
		}

		foreach ($items as $current) {

			// @task: Map vBulletin post item with EasyDiscuss items.
			$vItem	= $this->getVBulletinPost( $current );
			$item	= DiscussHelper::getTable( 'Post' );

			$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_POST_MIGRATED' , $vItem->postid ) , 'vBulletin' );
			$this->mapVBulletinItem( $vItem , $item );

			// @task: Once the post is migrated successfully, we'll need to migrate the child items.
			$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_POST_REPLIES_MIGRATED' , $vItem->postid ) , 'vBulletin' );
			$this->mapVBulletinItemChilds( $vItem , $item );
		}

		$ajax->script( 'runMigrationItem("vBulletin" , "' . $total . '");' );
		$ajax->send();
	}

	private function getVBulletinPostOri( $id )
	{
		$db		= DiscussHelper::getDBO();
		$prefix = DiscussHelper::getConfig()->get( 'migrator_vBulletin_prefix' );

		$query	= 'SELECT * FROM ' . $db->nameQuote( $prefix . 'post' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'postid' ) . '=' . $db->Quote( $id );
		$db->setQuery( $query );
		$item	= $db->loadObject();

		// Get the post's category id here
		$query = 'SELECT * FROM ' . $db->nameQuote( $prefix . 'thread' )
				. ' WHERE ' . $db->nameQuote( 'threadid' ) . '=' . $db->quote( $item->threadid );

		$db->setQuery( $query );
		$thread = $db->loadObject();


		$item->catid = $thread->forumid;
		$item->hits  = $thread->views;
		$item->created  = $thread->dateline;
		$item->replied  = $thread->lastpost;

		return $item;
	}

	private function getVBulletinPost( $id )
	{
		$db		= DiscussHelper::getDBO();
		$prefix = DiscussHelper::getConfig()->get( 'migrator_vBulletin_prefix' );

		$query = 'select a.*, b.`forumid`, b.`views`, b.`dateline`, b.`lastpost` ';
		$query .= ' from ' . $db->nameQuote( $prefix . 'post' ) . ' as a';
		$query .= ' left join ' . $db->nameQuote( $prefix . 'thread' )  . ' as b';
		$query .= ' 	on a.`threadid` = b.`threadid`';
		$query .= ' where a.`postid` = ' . $db->Quote( $id );

		// echo $query;exit;

		$db->setQuery( $query );
		$item	= $db->loadObject();

		if( $item )
		{
			$item->catid 	= $item->forumid;
			$item->hits  	= $item->views;
			$item->created  = $item->dateline;
			$item->replied  = $item->lastpost;
		}

		return $item;
	}

	private function mapVBulletinItem( $vItem , &$item , &$parent = null )
	{
		$config = DiscussHelper::getConfig();

		$userColumn 		= 'username';
		$user 				= null;

		if( $vItem->{$userColumn} )
		{
			$user 	= $this->getDiscussUser( $vItem->{$userColumn} );
		}

		$item->set( 'content'		, $vItem->pagetext );
		$item->set( 'title' 		, $vItem->title );
		$item->set( 'category_id' 	, $this->getDiscussCategory( $vItem ) );
		$item->set( 'hits'			, $vItem->hits );
		$item->set( 'content_type'	, 'bbcode');
		$item->set( 'created'	 	, DiscussHelper::getDate( $vItem->created )->toMySQL() );
		$item->set( 'modified' 		, DiscussHelper::getDate( $vItem->created )->toMySQL() );
		$item->set( 'replied' 		, DiscussHelper::getDate( $vItem->replied )->toMySQL() );
		$item->set( 'parent_id'		, 0 );

		// @task: If this is a child post, we definitely have the item's id.
		if( $parent )
		{
			$item->set( 'parent_id'	, $parent->id );
		}

		$item->set( 'islock'		, 0 );
		$item->set( 'published'		, DISCUSS_ID_PUBLISHED );
		$item->set( 'ip'			, $vItem->ipaddress );

		if( empty( $vItem->{$userColumn} ) || empty( $user ) )
		{
			$item->set( 'user_id'		, '0' );
			$item->set( 'user_type' 	, DISCUSS_POSTER_GUEST );
			$postername = $vItem->username ? $vItem->username : 'guest';
			$item->set( 'poster_name'	, $postername );
			$item->set( 'poster_email'	, '' );
		}
		else
		{
			$item->set( 'user_id'		, $user->id );
			$item->set( 'user_type' 	, DISCUSS_POSTER_MEMBER );
			$item->set( 'poster_name'	, $user->name );
			$item->set( 'poster_email'	, $user->email );
		}

		$item->store();

		$this->added( 'vBulletin' , $item->id , $vItem->postid , 'post' );
	}

	private function getDiscussCategory( $vItem )
	{
		static $cache = array();

		$key = 'category' . $vItem->catid;

		if (! isset($cache[$key])) {
			$db		= DiscussHelper::getDBO();
			$query	= 'SELECT ' . $db->nameQuote( 'internal_id' ) . ' '
					. 'FROM ' . $db->nameQuote( '#__discuss_migrators' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'external_id' ) . ' = ' . $db->Quote( $vItem->catid ) . ' '
					. 'AND ' . $db->nameQuote( 'type' ) . ' = ' . $db->Quote( 'category' ) . ' '
					. 'AND ' . $db->nameQuote( 'component' ) . ' = ' . $db->Quote( 'vBulletin' );

			$db->setQuery( $query );
			$categoryId	= $db->loadResult();

			$cache[$key] = $categoryId;
		}

		return $cache[$key];
	}

	private function getDiscussUser( $vbUserKeyValue )
	{
		$db = DiscussHelper::getDBO();
		$prefix = DiscussHelper::getConfig()->get( 'migrator_vBulletin_prefix' );

		// currently we not sure there are how many way of bridging the user from vbulletin to joomla.
		// for now, we assume the username is the key to communicate btw vbulletin and joomla
		$column 		= 'username';

		$query = 'SELECT b.* FROM ' . $db->nameQuote( '#__users' ) . ' AS b'
				. ' WHERE b.' . $db->nameQuote( $column ) . '=' . $db->Quote( $vbUserKeyValue );

		$db->setQuery( $query );
		$result = $db->loadObject();

		return $result;
	}

	private function mapVBulletinItemChilds( $vItem , &$parent )
	{
		$db = DiscussHelper::getDBO();
		$items	= $this->getVBulletinPosts( $vItem );
		$prefix = DiscussHelper::getConfig()->get( 'migrator_vBulletin_prefix' );

		if( empty($items) || !$items )
		{
			return false;
		}

		foreach( $items as $vChildItem )
		{
			$item	= DiscussHelper::getTable( 'Post' );

			// Get the post's category id here
			$query = 'SELECT * FROM ' . $db->nameQuote( $prefix . 'thread' )
					. ' WHERE ' . $db->nameQuote( 'threadid' ) . '=' . $db->quote( $vChildItem->threadid );

			$db->setQuery( $query );
			$thread = $db->loadObject();

			$vChildItem->catid = $thread->forumid;
			$vChildItem->hits  = $thread->views;
			$vChildItem->created  = $vChildItem->dateline;
			$vChildItem->replied  = $vChildItem->dateline;

			$this->mapVBulletinItem( $vChildItem , $item , $parent );
		}
	}

	private function getVBulletinPosts( $vItem = null , $vCategory = null )
	{
		$db		= DiscussHelper::getDBO();
		$prefix = DiscussHelper::getConfig()->get( 'migrator_vBulletin_prefix' );

		// $query	= 'SELECT * FROM ' . $db->nameQuote( $prefix . 'post' );
		// $query	.= ' WHERE ' . $db->nameQuote( 'threadid' ) . ' = ' . $db->Quote( $vItem->threadid );
		// $query	.= ' AND ' . $db->nameQuote( 'postid') . '!=' . $db->Quote( $vItem->postid );
		//


		$query	= 'SELECT a.*, b.`forumid` as `catid`, b.`views` as `hits`';
		$query .= ' FROM ' . $db->nameQuote( $prefix . 'post' ) . ' as a';
		$query .= ' INNER JOIN ' . $db->nameQuote( $prefix . 'thread' ) . ' as b on a.`threadid` = b.`threadid`';
		$query .= ' WHERE a.' . $db->nameQuote( 'threadid' ) . ' = ' . $db->Quote( $vItem->threadid );
		$query .= ' AND a.' . $db->nameQuote( 'postid') . '!=' . $db->Quote( $vItem->postid );

		// $query .= ' limit 10';

		// echo $query;exit;

		$db->setQuery( $query );
		$result	= $db->loadObjectList();

		if( !$result )
		{
			return false;
		}

		return $result;
	}


}
