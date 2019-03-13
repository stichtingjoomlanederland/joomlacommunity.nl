<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ADMIN_ROOT . '/views/views.php');

class EasyDiscussViewCategories extends EasyDiscussAdminView
{
	/**
	 * Renders the category listing at the back end
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		// Set page attributes
		$this->title('COM_EASYDISCUSS_CATEGORIES_TITLE');

		JToolbarHelper::addNew();
		JToolBarHelper::divider();
		JToolBarHelper::makeDefault('makeDefault');
		JToolBarHelper::divider();
		JToolbarHelper::publishList();
		JToolbarHelper::unpublishList();
		JToolBarHelper::divider();
		JToolbarHelper::deleteList();

		$filter_state = $this->getUserState('categories.filter_state', 'filter_state', '*', 'word');

		// Search
		$search = $this->getUserState('categories.search', 'search', '', 'string');
		$search = trim(strtolower($search));

		// Ordering
		$order = $this->getUserState('categories.filter_order', 'filter_order', 'lft', 'cmd');
		$orderDirection = $this->getUserState('categories.filter_order_Dir', 'filter_order_Dir', 'asc', 'word');

		// Get data from the model
		$model = ED::model('Categories');
		$rows = $model->getData();
		$categories = array();
		$ordering = array();

		foreach ($rows as $row) {

			$category = ED::table('Category');
			$category->bind($row);

			$category->depth = $row->depth;
			$category->count = $model->getUsedCount($category->id, false);
			$category->child_count = $model->getChildCount($category->id);

			$category->link = 'index.php?option=com_easydiscuss&view=categories&layout=form&id='. $category->id;

			$category->user = JFactory::getUser($category->created_by);
			$categories[] = $category;
			$ordering[$category->parent_id][] = $category->id;
		}



		$pagination = $model->getPagination();

		$this->set('categories', $categories);
		$this->set('pagination', $pagination);
		$this->set('ordering', $ordering);
		$this->set('state', $filter_state);
		$this->set('search', $search);
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);

		parent::display('categories/default');
	}

	/**
	 * Renders the category form
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function form($tpl = null)
	{
		$this->checkAccess('discuss.manage.categories');

		$id = $this->input->get('id', 0, 'int');

		$category = ED::category($id);

		// Set the new category as publish.
		if (!$category->id) {
			$category->set('published', true);
		}

		$this->title('COM_EASYDISCUSS_CATEGORIES_ADD_CATEGORY_TITLE');

		if ($category->id) {
			$this->title('COM_EASYDISCUSS_EDIT_CATEGORY_TITLE');
		}

		JToolbarHelper::apply();
		JToolbarHelper::save();
		JToolbarHelper::save2new();
		JToolBarHelper::cancel();

		// // Get assigned group acl
		$parentList = ED::populateCategories('', '', 'select', 'parent_id', $category->parent_id, true, false, false, false, '', array($category->id));

		// Get the default WYSIWYG editor
		$editor = JFactory::getEditor($this->jconfig->get('editor'));

		// Get active tab
		$active = $this->input->get('active', 'general', 'word');

		$this->set('active', $active);
		$this->set('editor', $editor);
		$this->set('category', $category);
		$this->set('categories', $parentList);

		parent::display('categories/form');
	}
}
