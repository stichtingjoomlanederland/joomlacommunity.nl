<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

require_once(DISCUSS_ROOT . '/views.php');

class EasyDiscussViewIndex extends EasyDiscussView
{
	public function display($tpl = null)
	{
		// Initialise variables
		$doc		= JFactory::getDocument();
		$my			= JFactory::getUser();
		$config		= DiscussHelper::getConfig();
		$app		= JFactory::getApplication();

		$registry	= DiscussHelper::getRegistry();
		$categoryId	= JRequest::getInt( 'category_id' , 0 );

		// Perform redirection if there is a category_id in the index view.
		if( !empty( $categoryId ) )
		{
			$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=categories&layout=listings&category_id=' . $categoryId , false ) );
			$app->close();
		}

		// Try to detect if there's any category id being set in the menu parameter.
		$activeMenu = $app->getMenu()->getActive();

		if( $activeMenu && !$categoryId )
		{
			// Load menu params to the registry.
			$registry->loadString( $activeMenu->params );

			if( $registry->get( 'category_id' ) )
			{
				$categoryId	= $registry->get( 'category_id' );
			}
		}

		// Get the current logged in user's access.
		$acl			= DiscussHelper::getHelper( 'ACL' );

		// Todo: Perhaps we should fix the confused naming of filter and sort to type and sort
		$filter	= JRequest::getString( 'filter' , $registry->get( 'filter' ) );
		$sort			= JRequest::getString( 'sort' , $registry->get( 'sort' ) );

		// Get the pagination limit
		$limit			= $registry->get( 'limit' );
		$limit			= ( $limit == '-2' ) ? DiscussHelper::getListLimit() : $limit;
		$limit			= ( $limit == '-1' ) ? DiscussHelper::getJConfig()->get('list_limit') : $limit;

		// Add view to this page.
		$this->logView();

		// set page title.
		DiscussHelper::setPageTitle();

		// Set the meta of the page.
		DiscussHelper::setMeta();

		// Add rss feed into headers
		DiscussHelper::getHelper( 'Feeds' )->addHeaders( 'index.php?option=com_easydiscuss&view=index' );

		// Get list of categories on the site.
		$catModel		= $this->getModel( 'Categories' );

		// Pagination is by default disabled.
		$pagination 	= false;

		// Get the model.
		$postModel		= DiscussHelper::getModel( 'Posts' );

		// Get a list of accessible categories
		$cats	= $this->getAccessibleCategories( $categoryId );

		// Get featured posts from this particular category.
		$featured   			= array();
		if( $config->get( 'layout_featuredpost_frontpage' ) )
		{
			$options 	= array(
									'pagination' => false,
									'category'		=> $cats,
									'sort'		 => $sort,
									'filter'	=> $filter,
									'limit'		=> $config->get( 'layout_featuredpost_limit' , $limit ),
									'featured'	=> true
							);
			$featured	= $postModel->getDiscussions( $options );
			if( is_null( $featured ) )
			{
				$featured = array();
			}
		}

		// Get normal discussion posts.
		$options 	= array(
						'sort'		=> $sort,
						'category'	=> $cats,
						'filter'	=> $filter,
						'limit'		=> $limit,
						'featured'	=> false
					);

		$posts		= $postModel->getDiscussions( $options );
		if( is_null( $posts ) )
		{
			$posts = array();
		}


		$authorIds		= array();
		$topicIds 		= array();

		$tmpPostsArr    = array_merge($featured, $posts);

		if( count($tmpPostsArr) > 0 )
		{
			foreach( $tmpPostsArr as $tmpArr )
			{
				$authorIds[]  	= $tmpArr->user_id;
				$topicIds[]     = $tmpArr->id;
			}
		}


		$pagination = $postModel->getPagination( 0 , 'latest' , '' , $cats , false );

		$postLoader   = EDC::getTable('Posts');
		$postLoader->loadBatch( $topicIds );

		$postTagsModel		= EDC::getModel( 'PostsTags' );
		$postTagsModel->setPostTagsBatch( $topicIds );

		$model 				= EDC::getModel( 'Posts' );
		$lastReplyUser      = $model->setLastReplyBatch( $topicIds );

		// Reduce SQL queries by pre-loading all author object.
		$authorIds	= array_merge( $lastReplyUser, $authorIds );
		$authorIds  = array_unique( $authorIds );

		// Initialize the list of user's so we run lesser sql queries.
		$profile	= EDC::getTable( 'Profile' );
		$profile->init( $authorIds );

		// Format featured entries.
		$featured 	= EDC::formatPost( $featured , false , true );

		// Format normal entries
		$posts 		= EDC::formatPost( $posts , false , true );


		// Get unread count
		$unreadCount 		= $model->getUnreadCount($cats, false);

		// Get unresolved count
		// Change the "all" to TRUE or FALSE to include/exclude featured post count
		$unresolvedCount 	= $model->getUnresolvedCount( '', $cats, '', 'all' );

		// Get resolved count
		$resolvedCount 		= $model->getTotalResolved();

		// Get unanswered count
		$unansweredCount 	= EDC::getUnansweredCount( $cats, true);

		// Get assigned post count that isn't answered yet.
		$assignedCount 		= 0;

		if (EDC::isSiteAdmin() || EDC::isModerator()) {

			$assignedModel 	= EDC::getModel('Assigned');
			$assignedCount 	= $assignedModel->getTotalUnresolved();
		}

		$activeFilter = $config->get( 'layout_frontpage_sorting' );

		// Let's render the layout now.
		$theme				= new DiscussThemes();

		$theme->set('assignedCount', $assignedCount);
		$theme->set( 'activeFilter'		, $activeFilter );
		$theme->set( 'activeSort'		, $sort );
		$theme->set( 'categories'		, $categoryId );
		$theme->set( 'unreadCount'		, $unreadCount );
		$theme->set( 'unansweredCount'	, $unansweredCount );
		$theme->set( 'resolvedCount'	, $resolvedCount );
		$theme->set( 'unresolvedCount'	, $unresolvedCount );
		$theme->set( 'posts' 		, $posts );
		$theme->set( 'featured' 	, $featured );
		$theme->set( 'pagination'	, $pagination );

		echo $theme->fetch( 'frontpage.index.php' );
	}

	private function getAccessibleCategories( $categoryId )
	{
		// We only want the user to view stuffs that they can really see.
		if( !is_array( $categoryId ) )
		{
			$accessibleCategories 	= DiscussHelper::getAccessibleCategories();
			$cats 					= array();

			if( $accessibleCategories )
			{
				foreach( $accessibleCategories as $category )
				{
					$cats[]	= $category->id;
				}
			}
		}
		else
		{
			$accessibleCategories 	= DiscussHelper::getAccessibleCategories();
			$cats 			= array();

			if( $accessibleCategories )
			{
				foreach( $accessibleCategories as $category )
				{
					if( in_array( $category->id , $categoryId ) )
					{
						$cats[]	= $category->id;
					}
				}
			}

		}

		return $cats;
	}
}
