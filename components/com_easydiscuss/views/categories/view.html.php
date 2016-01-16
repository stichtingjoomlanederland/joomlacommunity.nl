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

require_once( DISCUSS_ROOT . '/views.php' );

require_once DISCUSS_HELPERS . '/date.php';

class EasyDiscussViewCategories extends EasyDiscussView
{
	function display( $tmpl = null )
	{
		DiscussEventsHelper::importPlugin( 'content' );

		$mainframe	= JFactory::getApplication();
		$config		= DiscussHelper::getConfig();
		$my			=  JFactory::getUser();

		$sortConfig	= $config->get('layout_ordering_category','latest');
		$sort		= JRequest::getCmd('sort',$sortConfig);

		$this->setPathway( JText::_( 'COM_EASYDISCUSS_BREADCRUMBS_CATEGORIES' ) );


		DiscussHelper::setPageTitle(JText::_( 'COM_EASYDISCUSS_CATEGORIES_TITLE' ));

		// Set the meta of the page.
		DiscussHelper::setMeta();

		// @task: Add view
		$this->logView();

		$modelP			= DiscussHelper::getModel( 'Posts' );
		$categoryModel	= DiscussHelper::getModel( 'Categories' );

		$hideEmptyPost	= false;
		$categories		= $categoryModel->getCategoryTree();

		$theme	= new DiscussThemes();
		$theme->set( 'categories', $categories );

		echo $theme->fetch( 'categories.php' );
	}

	/**
	 * Displays a list of recent discussions from a particular category.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function listings()
	{
		// Initialise variables
		$doc		= JFactory::getDocument();
		$my			= JFactory::getUser();
		$config		= DiscussHelper::getConfig();
		$app		= JFactory::getApplication();

		$registry	= DiscussHelper::getRegistry();
		$categoryId	= JRequest::getInt( 'category_id' , 0 );

		// Try to detect if there's any category id being set in the menu parameter.
		$activeMenu = $app->getMenu()->getActive();

		// we only need to retrive the category id from menu if the menu is set to categories view
		if ($activeMenu && isset($activeMenu->query['view']) && $activeMenu->query['view'] == 'categories') {
			// Load menu params to the registry.
			$registry->loadString( $activeMenu->params );

			// Set the active category id if exists.
			$categoryId = $registry->get( 'category_id' ) ? $registry->get( 'category_id' ) : $categoryId;
		}

		// Get the current logged in user's access.
		$acl			= DiscussHelper::getHelper( 'ACL' );

		// Todo: Perhaps we should fix the confused naming of filter and sort to type and sort
		$activeFilter	= JRequest::getString( 'filter' , $registry->get( 'filter' ) );
		$sort			= JRequest::getString( 'sort' , $registry->get( 'sort' ) );

		// Get the pagination limit
		$limit			= $registry->get( 'limit' );
		$limit			= ( $limit == '-2' ) ? DiscussHelper::getListLimit() : $limit;
		$limit			= ( $limit == '-1' ) ? DiscussHelper::getJConfig()->get('list_limit') : $limit;


		// Get the active category id if there is any
		$activeCategory	= DiscussHelper::getTable( 'Category' );
		$activeCategory->load( $categoryId );

		DiscussHelper::setPageTitle( $activeCategory->title );

		// Add breadcrumbs for active category.
		if( $activeCategory->id != 0 )
		{
			// Test if user is really allowed to access this category.
			if( !$activeCategory->canAccess() )
			{
				$app->redirect( DiscussRouter::_('index.php?option=com_easydiscuss&view=index' , false ) , JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS') );
				$app->close();
				return;
			}

			// Add pathway for category here.
			DiscussHelper::getHelper( 'Pathway' )->setCategoryPathway( $activeCategory );
		}

		// Add view to this page.
		$this->logView();

		// Set the meta of the page.
		DiscussHelper::setMeta();

		$doc 		= JFactory::getDocument();
		$doc->setMetadata( 'description' , strip_tags( $activeCategory->getDescription() ) );

		// Add rss feed into headers
		DiscussHelper::getHelper( 'Feeds' )->addHeaders( 'index.php?option=com_easydiscuss&view=index' );

		// Get list of categories on the site.
		$catModel		= $this->getModel( 'Categories' );

		// Pagination is by default disabled.
		$pagination 	= false;


		if( $categoryId )
		{
			$category		= DiscussHelper::getTable( 'Category' );
			$category->load( $categoryId );
			$categories[]	= $category;
		}
		else
		{
			$categories		= $catModel->getCategories( $categoryId );

			if( count( $categories ) > 1 )
			{
				$ids = array();

				foreach( $categories as $row)
				{
					$ids[] = $row->id;
				}

				// iniCounts should only called in index page.
				$category		= DiscussHelper::getTable( 'Category' );
				$category->initCounts( $ids, true );
			}
		}

		// Get the model.
		$postModel = DiscussHelper::getModel( 'Posts' );

		$authorIds  = array();
		$topicIds 	= array();

		for( $i = 0; $i < count($categories); $i++ )
		{
			$category =& $categories[$i];

			// building category childs lickage.
			$category->childs 	= null;
			$nestedLinks 		= '';

			// In category page
			if( $config->get( 'layout_show_all_subcategories', '1' ) )
			{
				// By default show all the subcategories of the selected category
				DiscussHelper::buildNestedCategories($category->id, $category, false , true );
			}
			else
			{
				// Show one level of subcategories of the selected category only
				$category->childs = $catModel->getChildCategories( $category->id );
			}

			DiscussHelper::accessNestedCategories($category, $nestedLinks, '0', '', 'listlink', ', ');

			$category->nestedLink	= $nestedLinks;

			// Get featured posts from this particular category.
			$featured	= $postModel->getDiscussions(
								array(
										'pagination' => false,
										'sort'		 => $sort,
										'filter'	=> $activeFilter,
										'category'	=> $category->id,
										'limit'		=> $config->get( 'layout_featuredpost_limit' , $limit ),
										'featured'	=> true
									)
							);

			// Get normal discussion posts.
			$posts		= $postModel->getDiscussions(
								array(
										'sort'		 => $sort,
										'filter'	=> $activeFilter,
										'category'	=> $category->id,
										'limit'		=> $limit,
										'featured' => false
									)
							);

			$tmpPostsArr    = array_merge($featured, $posts);
			if( count($tmpPostsArr) > 0 )
			{
				foreach( $tmpPostsArr as $tmpArr )
				{
					$authorIds[]  = $tmpArr->user_id;
					$topicIds[]   = $tmpArr->id;
				}
			}

			if( $categoryId )
			{
				$pagination 	= $postModel->getPagination( 0 , 'latest' , '' , $categoryId, false );
			}

			// Set these items into the category object.
			$category->featured	= $featured;
			$category->posts	= $posts;

			// Set active filter for the category
			$category->activeFilter	= $activeFilter;
			$category->activeSort	= $sort;
		}

		$lastReplyUser      = $postModel->setLastReplyBatch( $topicIds );
		$authorIds			= array_merge( $lastReplyUser, $authorIds );

		// load all author object 1st.
		$authorIds  = array_unique($authorIds);
		$profile	= DiscussHelper::getTable( 'Profile' );
		$profile->init( $authorIds );

		$postLoader   = DiscussHelper::getTable('Posts');
		$postLoader->loadBatch( $topicIds );

		$postTagsModel		= DiscussHelper::getModel( 'PostsTags' );
		$postTagsModel->setPostTagsBatch( $topicIds );

 		// perform data formating here.
		for( $i = 0; $i < count($categories); $i++ )
		{
			$category =& $categories[$i];

			// perform data formating here.
			if( $category->featured )
			{
				$category->featured	= DiscussHelper::formatPost( $category->featured , false , true );
			}

			if( $category->posts )
			{
				$category->posts		= DiscussHelper::formatPost( $category->posts , false , true );
			}

		}

		// Let's render the layout now.
		$theme 	= new DiscussThemes();

		$theme->set( 'activeFilter'		, $activeFilter );
		$theme->set( 'activeSort'		, $sort );
		$theme->set( 'categories' 		, $categories );
		$theme->set( 'pagination'		, $pagination );

		echo $theme->fetch( 'frontpage.php' );
	}
}
