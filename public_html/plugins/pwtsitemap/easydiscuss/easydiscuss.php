<?php
/**
 * @package    Pwtsitemap
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;

JLoader::register('EasyDiscussModelCategories', JPATH_ADMINISTRATOR . '/components/com_easydiscuss/models/categories.php');
JLoader::register('EasyDiscussModelTags', JPATH_ADMINISTRATOR . '/components/com_easydiscuss/models/tags.php');
JLoader::register('EasyDiscussModelPosts', JPATH_ADMINISTRATOR . '/components/com_easydiscuss/models/posts.php');
JLoader::register('EasyDiscussModelUsers', JPATH_ADMINISTRATOR . '/components/com_easydiscuss/models/users.php');

/**
 * PWT Sitemap EasyDiscuss Plugin
 *
 * @since  1.0.0
 */
class PlgPwtSitemapEasydiscuss extends PwtSitemapPlugin
{
	/**
	 * Populate the PWT sitemap plugin to use it a base class
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function populateSitemapPlugin()
	{
		$this->component = 'com_easydiscuss';
		$this->views     = ['categories', 'tags', 'users', 'index'];
	}

	/**
	 * Run for every menuitem passed
	 *
	 * @param   StdClass  $item    Menu items
	 * @param   string    $format  Sitemap format that is rendered
	 *
	 * @return  array List of sitemap items
	 *
	 * @since   1.0.0
	 */
	public function onPwtSitemapBuildSitemap($item, $format)
	{
		$sitemapItems = [];

		if ($this->checkDisplayParameters($item, $format))
		{
			switch ($item->query['view'])
			{
				case 'index':
					$sitemapItems = $this->indexSitemapItems($item);
					break;
				case 'categories':
					$sitemapItems = $this->categoriesSitemapItems($item);
					break;
				case 'tags':
					$sitemapItems = $this->tagsSitemapItems($item);
					break;
				case 'users':
					$sitemapItems = $this->usersSitemapItems($item);
			}
		}

		return $sitemapItems;
	}

	/**
	 * Get sitemap items for view categories.
	 *
	 * @param   object  $item  The category to get the items for
	 *
	 * @return  array  List of sitemap items.
	 *
	 * @since   1.3.0
	 */
	private function categoriesSitemapItems($item)
	{
		$sitemapItems = [];

		if (isset($item->query['category_id']) && $item->query['view'] === 'categories')
		{
			$posts = $this->getPostsByCategoryIds([$item->query['category_id']]);

			if ($posts !== false)
			{
				foreach ($posts as $post)
				{
					$link           = Route::_('index.php?option=com_easydiscuss&view=post&id=' . $post->id);
					$sitemapItems[] = new pwtSitemapitem($post->title, $link, $item->level + 1);
				}
			}
		}
		elseif ($item->query['view'] === 'categories')
		{
			$categories = $this->getCategories();

			if ($categories !== false)
			{
				foreach ($categories as $category)
				{
					$link           = Route::_('index.php?option=com_easydiscuss&view=categories&layout=listings&category_id=' . $category->id);
					$sitemapItems[] = new pwtSitemapitem($category->title, $link, $item->level + 1);
				}
			}
		}

		return $sitemapItems;
	}

	/**
	 * Get sitemap items for view tags.
	 *
	 * @param   object  $item  The tag to get the items for
	 *
	 * @return  array  List of sitemap items.
	 *
	 * @since   1.3.0
	 */
	private function tagsSitemapItems($item)
	{
		$sitemapItems = [];

		if (isset($item->query['id']) && $item->query['view'] === 'tags')
		{
			$posts = $this->getPostsByTagId($item->query['id']);

			if ($posts !== false)
			{
				foreach ($posts as $post)
				{
					$link           = Route::_('index.php?option=com_easydiscuss&view=post&id=' . $post->id);
					$sitemapItems[] = new pwtSitemapitem($post->title, $link, $item->level + 1);
				}
			}
		}
		elseif ($item->query['view'] === 'tags')
		{
			$tags = $this->getTags();

			if ($tags !== false)
			{
				foreach ($tags as $tag)
				{
					$link           = Route::_('index.php?option=com_easydiscuss&view=tags&id=' . $tag->id);
					$sitemapItems[] = new pwtSitemapitem($tag->title, $link, $item->level + 1);
				}
			}
		}

		return $sitemapItems;
	}

	/**
	 * Get sitemap items for view users.
	 *
	 * @param   object  $item  The current menu item?
	 *
	 * @return  array  List of sitemap items.
	 *
	 * @since   1.3.0
	 */
	private function usersSitemapItems($item)
	{
		$sitemapItems = [];
		$users        = $this->getUsers();

		if ($users !== false)
		{
			foreach ($users as $user)
			{
				$link           = Route::_('index.php?option=com_easydiscuss&view=profile&id=' . $user->id);
				$sitemapItems[] = new pwtSitemapitem($user->name, $link, $item->level + 1);
			}
		}

		return $sitemapItems;
	}

	/**
	 * Get sitemap items for view index posts.
	 *
	 * @param   object  $item  The index post
	 *
	 * @return  array  List of sitemap items.
	 *
	 * @since   1.0
	 */
	private function indexSitemapItems($item)
	{
		$sitemapItems = [];

		if ($item->query['view'] === 'index')
		{
			$posts = $this->getIndexPost();

			if ($posts !== false)
			{
				foreach ($posts as $post)
				{
					$link           = Route::_('index.php?option=com_easydiscuss&view=post&id=' . $post->id);
					$sitemapItems[] = new pwtSitemapitem($post->title, $link, $item->level + 1);
				}
			}
		}

		return $sitemapItems;
	}

	/**
	 * Get all categories from EasyDiscuss
	 *
	 * @return  mixed  stdClass on success, false otherwise
	 *
	 * @since   1.3.0
	 */
	private function getCategories()
	{
		$categoriesModel = new EasyDiscussModelCategories;

		return $categoriesModel->getCategoryTree();
	}

	/**
	 * Get all posts from a category from EasyDiscuss
	 *
	 * @param   array  $categoryIds  Category id
	 *
	 * @return  mixed  stdClass on success, false otherwise
	 *
	 * @since   1.3.0
	 */
	private function getPostsByCategoryIds($categoryIds)
	{
		$postsModel = new EasyDiscussModelPosts;

		return $postsModel->getData(false, 'latest', null, '', $categoryIds, null, 'all', null, false);
	}

	/**
	 * Get all tags from EasyDiscuss
	 *
	 * @return  mixed  stdClass on success, false otherwise
	 *
	 * @since   1.3.0
	 */
	private function getTags()
	{
		$tagsModel = new EasyDiscussModelTags;

		return $tagsModel->getTags();
	}

	/**
	 * Get all posts from a tag ID from EasyDiscuss
	 *
	 * @param   array  $tagIds  Category id
	 *
	 * @return  mixed  stdClass on success, false otherwise
	 *
	 * @since   1.3.0
	 */
	private function getPostsByTagId($tagIds)
	{
		$postsTagsModel = new EasyDiscussModelPosts;

		return $postsTagsModel->getTaggedPost($tagIds);
	}

	/**
	 * Get all users from EasyDiscuss
	 *
	 * @return  mixed  stdClass on success, false otherwise
	 *
	 * @since   1.3.0
	 */
	private function getUsers()
	{
		$usersModel = new EasyDiscussModelUsers;

		return $usersModel->getUsers();
	}

	/**
	 * Get all recent posts from EasyDiscuss
	 *
	 * @return  mixed  stdClass on success, false otherwise
	 *
	 * @since   1.0.0
	 */
	private function getIndexPost()
	{
		$postsTagsModel = new EasyDiscussModelPosts;
		$postsTagsModel->setState('limit', 50000);

		return $postsTagsModel->getDiscussions();
	}
}
