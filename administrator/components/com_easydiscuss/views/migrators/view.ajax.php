<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once DISCUSS_ADMIN_ROOT . '/views/views.php';
jimport('joomla.utilities.utility');

class EasyDiscussViewMigrators extends EasyDiscussAdminView
{
	var $err = null;

	public function migrate()
	{
		$component = $this->input->get('component', '', 'string');

		if (!$component) {
			die('Invalid migration');
		}

		switch($component)
		{
		    case 'com_kunena':

				$migrator = ED::migrator()->getAdapter('kunena');

				$migrator->migrate();

		        break;

		    case 'com_community':

				$migrator = ED::migrator()->getAdapter('jomsocial');

				$migrator->migrate();

		        break;

		    case 'vbulletin':
		    	$prefix = $this->input->get('prefix', '', 'string');

				$migrator = ED::migrator()->getAdapter('vbulletin');

				$migrator->migrate($prefix);

		        break;

		    default:
		        break;
		}
	}

	/**
     * Check whether the vBulletin prefix exist
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
	public function checkPrefix()
	{
		$db = ED::db();

		$prefix = $this->input->get('prefix', '', 'string');

		if (empty($prefix)) {
			return $this->ajax->reject(JText::sprintf('COM_EASYDISCUSS_VBULLETN_DB_PREFIX_NOT_FOUND', $prefix));
		}

		// Check if the vBulletin table exist
		$tables = $db->getTableList();
		$exist = in_array($prefix . 'thread', $tables);

		if (empty($exist)) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_VBULLETN_DB_TABLE_NOT_FOUND'));
		}

		$this->ajax->resolve($prefix);
	}








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



	private function getCPAnswers( $cpItem )
	{
		$db 	= DiscussHelper::getDBO();

		$query 	= 'SELECT * FROM `#__jcp_options` WHERE `poll_id`=' . $db->Quote( $cpItem->id );
		$db->setQuery( $query );

		return $db->loadObjectList();
	}

	private function getCPPostsIds()
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT `id` FROM ' . $db->nameQuote( '#__jcp_polls' );
		$db->setQuery( $query );
		return $db->loadResultArray();
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



}
