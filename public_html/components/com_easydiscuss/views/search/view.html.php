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

require_once(DISCUSS_ROOT . '/views/views.php');

class EasyDiscussViewSearch extends EasyDiscussView
{
	public function display($tmpl = null)
	{
		// Set page attributes
		ED::setPageTitle('COM_EASYDISCUSS_SEARCH');
		ED::setMeta();
		ED::breadcrumbs('COM_EASYDISCUSS_SEARCH');

		$post = $this->input->getArray('get');

		// Get the category?
		$category = $this->input->get('category_id', 0, 'int');
		$catfilters = $this->input->get('categories', array(), 'array');
		$postType = $this->input->get('post_type', '', 'default');

		$cats = array();
		$tags = array();

		$tagItems = array();
		$catItems = array();

		if ($category) {
			$cats[] = $category;
		}

		if ($catfilters) {
			foreach($catfilters as $item) {
				$id = (int) $item;

				$cats[] = $id;

				$category = ED::table('Category');
				$category->load($id);

				$obj = new stdClass();
				$obj->id = (int) $item;
				$obj->title = JText::_($category->title);

				$catItems[] = $obj;

			}
			array_unique($cats);
		}

		if ($cats) {

			$catOptions = array('idOnly' => true, 'includeChilds' => true);
			$catModel = ED::model('Categories');
			$cats = $catModel->getCategoriesTree($cats, $catOptions);
		}

		$tagfilters = $this->input->get('tags', array(), 'array');

		if ($tagfilters) {

			foreach($tagfilters as $item) {
				$id = (int) $item;
				$tags[] = $id;

				$tag = ED::table('Tags');
				$tag->load($id);
				
				$obj = new stdClass();
				$obj->id = (int) $item;
				$obj->title = JText::_($tag->title);

				$tagItems[] = $obj;
			}
		}

		// Search query
		$query = $this->input->get('query', '', 'string');
		$limitstart	= null;
		$items = array();
		$pagination	= null;

		$options = array();
		$options['usePagination'] = true;
		$options['sort'] = 'latest';
		$options['filter'] = 'allpost';
		$options['category'] = $cats;
		$options['tags'] = $tags;
		$options['post_type'] = $postType;

		if ($query) {
			// Get the result
			$model = ED::model('Search');
			$results = $model->getData($options);
			$pagination = $model->getPagination();

			if ($results) {
				foreach($results as $result) {
					$items[] = ED::searchitem($result);
				}
			}
		}

		$postTypes = false;
		$postTypeValue = $this->input->get('post_type', '', 'default');

		// Get post types list
		if ($this->config->get('layout_post_types')) {
			$postTypesModel = ED::model('PostTypes');
			$postTypes = $postTypesModel->getPostTypes(null, 'ASC', true);
		}

		$this->set('query', $query);
		$this->set('posts', $items);
		$this->set('paginationType', DISCUSS_SEARCH_TYPE);
		$this->set('pagination', $pagination);
		$this->set('parent_id', $query);
		$this->set('postTypes', $postTypes);
		$this->set('postTypeValue', $postTypeValue);

		$this->set('tagFilters', $tagItems);
		$this->set('catFilters', $catItems);

		parent::display('search/default');
	}
}
