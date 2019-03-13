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

class EasyDiscussViewPriorities extends EasyDiscussAdminView
{
	public function display($tpl = null)
	{
		$this->checkAccess('discuss.manage.priorities');

		// Set page attributes
		$this->title('COM_EASYDISCUSS_PRIORITIES_TITLE');

		JToolbarHelper::addNew();
		JToolBarHelper::divider();
		JToolbarHelper::deleteList();

		$model = ED::model('Priorities');

		// Get a list of priorities
		$priorities = $model->getPriorities();
		$pagination = $model->getPagination();

		// Search query
		$search = $this->getUserState('priorities.search', 'search', '', 'string');
		$search = trim(strtolower($search));

		$this->set('priorities', $priorities);
		$this->set('search', $search);
		$this->set('pagination', $pagination);

		parent::display('priorities/default');
	}

	/**
	 * Renders the priority form
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function form()
	{
		$this->checkAccess('discuss.manage.priorities');

		$pageTitle = 'COM_EASYDISCUSS_ADD_PRIORITY_TITLE';

		// This determines if we are editing
		$id = $this->input->get('id', 0, 'int');

		$priority = ED::table('Priority');
		$priority->color = '#000000';

		if ($id) {
			$priority->load($id);

			$pageTitle = 'COM_EASYDISCUSS_EDIT_PRIORITY_TITLE';
		}


		$this->setHeading($pageTitle);


		JToolbarHelper::apply();
		JToolbarHelper::save();
		JToolbarHelper::save2new();
		JToolBarHelper::cancel();

		$this->set('priority', $priority);

		// This will go to form.php
		parent::display('priorities/form');
	}
}
