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

class EasyDiscussViewLabels extends EasyDiscussAdminView
{
	/**
	 * Display the list of the post labels
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		$this->checkAccess('discuss.manage.labels');

		JToolbarHelper::addNew();
		JToolbarHelper::publishList();
		JToolbarHelper::unpublishList();
		JToolbarHelper::deleteList();

		$this->setHeading('COM_ED_POST_LABELS_TITLE');

		$state = $this->getUserState('types.filter_state', 'filter_state', '*', 'word');

		// Ordering
		$order = $this->getUserState('types.filter_order', 'filter_order', 'a.id', 'cmd');
		$orderDirection = $this->getUserState('types.filter_order_Dir', 'filter_order_Dir', '', 'word');

		$model = ED::model('PostLabels');

		$labels = $model->getData();
		$pagination = $model->getPagination();

		$browse = $this->input->get('browse', 0,' int');
		$browseFunction = $this->input->get('browseFunction', '');

		// Search query
		$search = $this->getUserState('labels.search', 'search', '', 'string');
		$search = trim(strtolower($search));
		
		$this->set('state', $state);
		$this->set('pagination', $pagination);
		$this->set('state', $state);
		$this->set('search', $search);
		$this->set('labels', $labels);
		$this->set('browse', $browse);
		$this->set('browseFunction', $browseFunction);
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);

		parent::display('labels/default');
	}

	/**
	 * Renders the edit and creation form
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function form()
	{
		$this->checkAccess('discuss.manage.labels');

		JToolbarHelper::apply();
		JToolbarHelper::save();
		JToolbarHelper::save2new();
		JToolBarHelper::cancel();

		$title = 'COM_ED_ADD_LABELS_TITLE';

		$id = $this->input->get('id', 0, 'int');
		$label = ED::table('Labels');

		// Default colour code and published state
		$label->colour = '#428BCA';
		$label->published = 1;

		if ($id) {
			$label->load($id);

			$title = 'COM_ED_EDIT_LABELS_TITLE';
		}

		$this->setHeading($title);

		$this->set('label', $label);

		parent::display('labels/form');
	}
}
