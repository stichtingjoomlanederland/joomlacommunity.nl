<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once(DISCUSS_ADMIN_ROOT . '/views/views.php');

class EasyDiscussViewCustomFields extends EasyDiscussAdminView
{
	/**
	 * Renders the custom fields listing
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		$this->checkAccess('discuss.manage.customfields');

		// Set page attributes
		$this->title('COM_EASYDISCUSS_CUSTOMFIELDS_MAIN_TITLE');

		JToolbarHelper::addNew();
		JToolbarHelper::publishList();
		JToolbarHelper::unpublishList();
		JToolBarHelper::divider();
		JToolbarHelper::deleteList();

		// States
		$filter = $this->getUserState('com_easydiscuss.fields.filter_state', 'filter_state', '*', 'word');

		// Search queries
		$search = $this->getUserState('com_easydiscuss.fields.search', 'search', '', 'string');
		$search = trim(strtolower($search));

		// Ordering
		$order = $this->getUserState('com_easydiscuss.fields.filter_order', 'filter_order', 'a.ordering', 'cmd');
		$orderDirection = $this->getUserState('com_easydiscuss.fields.filter_order_Dir', 'filter_order_Dir', 'asc', 'word');

		// Get data from the model
		$model = ED::model('CustomFields');
		$result = $model->getData();
		$fields = array();

		// By default we do not enable ordering
		$allowOrdering = false;

		if ($order == 'a.ordering') {
			$allowOrdering = true;
		}

		$ordering = array();

		if ($result) {
			foreach ($result as $row) {
				$field = ED::field($row);

				$ordering[] = $field->id;

				$field->orderingIndex = array_search($field->id, $ordering) + 1;

				$fields[] = $field;
			}
		}

		$pagination = $model->getPagination();

		$joomlaGroups = ED::getJoomlaUserGroups();

		$saveOrder = $order == 'a.ordering' && $orderDirection == 'asc';
		$ordering = $order == 'a.ordering';

		$this->title('COM_EASYDISCUSS_CUSTOM_FIELDS_TITLE');
		$this->desc('COM_EASYDISCUSS_CUSTOM_FIELDS_DESC');

		// dump($allowOrdering);
		$this->set('allowOrdering', $allowOrdering);
		$this->set('ordering', $ordering);
		$this->set('originalOrders', array());
		$this->set('saveOrder', $saveOrder);
		$this->set('fields', $fields);
		$this->set('pagination', $pagination);
		$this->set('ordering', $ordering);
		$this->set('filter', $filter);
		$this->set('search', $search);
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);
		$this->set('joomlaGroups', $joomlaGroups);

		parent::display('fields/default');
	}

	/**
	 * Renders the custom field form
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function form()
	{
		$this->checkAccess('discuss.manage.customfields');

		// Get the field id
		$id = $this->input->get('id', 0, 'int');

		// Load the custom field
		$field = ED::field($id);

		// Set page attributes
		$this->title('COM_EASYDISCUSS_EDITING_CUSTOMFIELDS');

		if ($field->id || is_null($field->id)) {
			$this->title('COM_EASYDISCUSS_ADD_NEW_CUSTOMFIELDS');
		}

		JToolbarHelper::apply();
		JToolbarHelper::save();
		JToolBarHelper::save2new();
		JToolBarHelper::divider();
		JToolBarHelper::cancel();

		// Get active tab
		$active = $this->input->get('active', 'general', 'word');

		$this->set('active', $active);
		$this->set('field', $field);

		parent::display('fields/form');
	}
}
