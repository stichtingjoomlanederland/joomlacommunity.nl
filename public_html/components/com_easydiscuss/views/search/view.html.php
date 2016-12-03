<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
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

		// var_dump($post);

		// Get the category?
		$category = $this->input->get('category_id', 0, 'int');

		$catfilters = $this->input->get('categories', array(), 'array');


		$cats = array();
		$tags = array();

		$tagItems = array(); // for display
		$catItems = array(); // for display

		if ($category) {
			$cats[] = $category;
		}

		if ($catfilters) {
			foreach($catfilters as $item) {
				$cats[] = (int) $item;

				$obj = new stdClass();
				$obj->id = (int) $item;

				$obj->title = JString::substr($item, Jstring::strpos($item, ':') + 1);

				$catItems[] = $obj;

			}
			array_unique($cats);
		}


		$tagfilters = $this->input->get('tags', array(), 'array');

		if ($tagfilters) {
			foreach($tagfilters as $item) {
				$tags[] = (int) $item;

				$obj = new stdClass();
				$obj->id = (int) $item;

				$obj->title = JString::substr($item, Jstring::strpos($item, ':') + 1);

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

		if ($query) {

			$model = ED::model('Search');

			// Get the result
			$results = $model->getData($options);
			$pagination = $model->getPagination();

			if ($results) {
				foreach($results as $result) {
					$items[] = ED::searchitem($result);
				}
			}
		}

		$this->set('query', $query);
		$this->set('posts', $items);
		$this->set('paginationType', DISCUSS_SEARCH_TYPE);
		$this->set('pagination', $pagination);
		$this->set('parent_id', $query);

		$this->set('tagFilters', $tagItems);
		$this->set('catFilters', $catItems);

		parent::display('search/default');
	}
}
