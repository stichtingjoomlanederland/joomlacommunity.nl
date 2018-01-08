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
				$resetHits = $this->input->get('resetHits', false, 'boolean');
				$migrateSignature = $this->input->get('migrateSignature', false, 'boolean');
				$migrateAvatar = $this->input->get('migrateAvatar', false, 'boolean');
				$replies = $this->input->get('replies', false, 'boolean');

				if ($replies) {
					$total = $this->input->get('total', 0, 'int');
					$migrator->migrateReplies($total);
				} else {
					$migrator->migrate($resetHits, $migrateSignature, $migrateAvatar);
				}

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

			case 'com_discussions':
				$migrator = ED::migrator()->getAdapter('discussions');

				$migrator->migrate();

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
}
