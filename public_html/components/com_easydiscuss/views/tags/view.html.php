<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussViewTags extends EasyDiscussView
{
	public function display($tmpl = null)
	{
		// Set the page title
		ED::setPageTitle('COM_EASYDISCUSS_TAGS_TITLE');

		// Set the breadcrumbs
		if (!EDR::isCurrentActiveMenu('tags')) {
			$this->setPathway('COM_EASYDISCUSS_TOOLBAR_TAGS');
		}

		// Determines if we should display a single tag result.
		$id = $this->input->get('id', 0, 'int');
		$sort = $this->input->get('sort', 'title', 'string');
		$search = $this->input->get('search', '', 'string');

		if ($id) {
			return $this->tag($tmpl);
		}

		// Set meta tags.
		ED::setMeta();

		// for now we hardcode the limit as tags page pagination abit special. #232
		$limit = DISCUSS_TAGS_LIMIT;

		$model = ED::model("Tags");
		$tags = $model->getTagCloud($limit, $sort, 'asc', '', true, $search);
		$pagination = $model->getPagination();

		$this->set('search', $search);
		$this->set('tags', $tags);
		$this->set('pagination', $pagination);
		$this->set('activeSort', $sort);

		parent::display('tags/listings/default');
	}

	/**
	 * Displays a list of discussions from a particular tag
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function tag($tmpl = null)
	{
		// Get the tag id
		$id = $this->input->get('id', 0, 'int');

		if (!$id) {
			throw ED::exception('COM_EASYDISCUSS_INVALID_TAG', ED_MSG_ERROR);
		}

		$tag = ED::tag($id);

		// Set meta tags.
		ED::setMeta($tag->id, ED_META_TYPE_TAG, JText::sprintf('COM_ED_POSTS_TAGGED', ED::themes()->html('string.escape', $tag->getTitle())));

		// Set the page title
		$pageTitle = JText::sprintf('COM_EASYDISCUSS_VIEWING_TAG_TITLE', $this->escape($tag->title));
		
		ED::setPageTitle($pageTitle);
		
		if (!EDR::isCurrentActiveMenu('tags', $tag->id)) {
			$this->setPathway($tag->title);
		}

		$tag->attachRssLinks();

		// Add canonical tag for this page
		$this->canonical('index.php?option=com_easydiscuss&view=tags&id=' . $id);

		// Used in post filters
		$baseUrl = 'view=tags&layout=tag&id=' . $tag->id;

		$activeSort = $this->input->get('sort', '', 'string');
		$activeFilter = $this->input->get('filter', 'all', 'string');
		$activeCategory = $this->input->get('category', 0, 'int');

		// Allows caller to filter posts by post types
		$postTypes = $this->input->get('types', array(), 'string');

		// Allows caller to filter posts by labels
		$postLabels = $this->input->get('labels', array(), 'int');

		// Allows caller to filter posts by priority
		$postPriorities = $this->input->get('priorities', array(), 'int');

		$postsModel = ED::model('Posts');

		// Get featured posts from this particular category.
		$featuredOptions = [
			'pagination' => false,
			'filter' => $activeFilter,
			'tag' => $tag->id,
			'category' => $activeCategory,
			'sort' => 'latest',
			'featured' => true,
			'postTypes' => $postTypes,
			'postLabels' => $postLabels,
			'postPriorities' => $postPriorities,
			'limit' => DISCUSS_NO_LIMIT
		];

		$featured = $postsModel->getDiscussions($featuredOptions);

		$options = [
			'filter' => $activeFilter,
			'tag' => (int) $tag->id,
			'category' => $activeCategory,
			'sort' => $activeSort,
			'postTypes' => $postTypes,
			'postLabels' => $postLabels,
			'postPriorities' => $postPriorities,
			'featured' => false,
			'limitstart' => $this->input->get('limitstart', 0, 'int')
		];

		// Get all the posts in this category and it's childs
		$posts = $postsModel->getDiscussions($options);
		$pagination = $postsModel->getPagination();

		// Only load the data when we really have data
		if ($featured || $posts) {
			ED::post(array_merge($featured, $posts));

			// Format featured entries.
			if ($featured) {
				$featured = ED::formatPost($featured, false, true);
			}

			// Format normal entries
			if ($posts) {
				$posts = ED::formatPost($posts, false, true);
			}
		}

		$this->set('activeCategory', $activeCategory);
		$this->set('featured', $featured);
		$this->set('postLabels', $postLabels);
		$this->set('postTypes', $postTypes);
		$this->set('postPriorities', $postPriorities);
		$this->set('activeSort', $activeSort);
		$this->set('activeFilter', $activeFilter);
		$this->set('baseUrl', $baseUrl);
		$this->set('tag', $tag);
		$this->set('posts', $posts);
		$this->set('pagination', $pagination);

		parent::display('tags/item/default');
	}
}
