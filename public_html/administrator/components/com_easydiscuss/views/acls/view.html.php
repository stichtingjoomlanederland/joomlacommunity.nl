<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ADMIN_ROOT . '/views/views.php');

class EasyDiscussViewAcls extends EasyDiscussAdminView
{
	/**
	 * Displays the default ACL listings
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display($tpl = null)
	{
		$this->checkAccess('discuss.manage.acls');

		// Determines if we should filter by acl type
		$type = $this->getUserState('acls.filter_type', 'filter_type', 'group', 'word');

		// Filtering
		$filter = new stdClass();
		// $filter->type = $this->getFilterType($type);
		$filter->search = $this->getUserState('acls.search', 'search', '', 'string');

		// Sorting
		$sort = new stdClass();
		$sort->order = $this->getUserState('acls.filter_order', 'filter_order', 'a.`id`', 'cmd');
		$sort->orderDirection = $this->getuserState('acls.filter_order_Dir', 'filter_order_Dir', '', 'word');

		$model = ED::model('Acl', true);
		$rulesets = $model->getRuleSets($type);
		$pagination = $model->getPagination($type);

		$this->title('COM_EASYDISCUSS_ACL_TITLE');
		$this->desc('COM_EASYDISCUSS_ACL_DESC');

		if ($rulesets) {
			foreach ($rulesets as &$ruleset) {
				$ruleset->editLink = 'index.php?option=com_easydiscuss&view=acls&layout=form&id=' . $ruleset->id . '&type=' . $type;
			}
		}

		$this->set('rulesets', $rulesets);
		$this->set('filter', $filter);
		$this->set('sort', $sort);
		$this->set('type', $type);
		$this->set('pagination', $pagination);

		parent::display('acls/default');
	}

	/**
	 * Displays the acl form
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function form($tpl = null)
	{
		$this->checkAccess('discuss.manage.acls');

		$id = $this->input->get('id', 0, 'int');

		if (!$id) {
			ED::setMessage('COM_EASYDISCUSS_INVALID_ACL_ID', 'error');
			return $this->app->redirect('index.php?option=com_easydiscuss&view=acls');
		}

		$model = ED::model('Acl');

		// Get ruleset
		$ruleset = $model->getRuleSet('group', $id);
		$keys = array_keys($ruleset->rules);

		// We should only display tabs that exists
		$tabs = array();

		if ($keys) {
			foreach ($keys as $key) {
				$tab = new stdClass();
				$tab->title = JText::_('COM_EASYDISCUSS_ACL_TAB_' . strtoupper($key));
				$tab->id = str_ireplace(array('.', ' ', '_'), '-', strtolower($key));

				$tabs[] = $tab;
			}
		}

		$this->title($ruleset->name);
		$this->desc('COM_EASYDISCUSS_ACL_DESC');

		$this->set('tabs', $tabs);
		$this->set('ruleset', $ruleset);

		parent::display('acls/form');
	}

	/**
	 * Registers the toolbar
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function registerToolbar()
	{
		$layout = $this->getLayout();


		if ($layout == 'form') {
			JToolBarHelper::apply();
			JToolBarHelper::save();
			JToolBarHelper::divider();
			JToolBarHelper::cancel();

			return;
		}

		// Determines which filter type
		$type = $this->getUserState('acls.filter_type', 'filter_type', 'group', 'word');

		if ($type == 'assigned') {
			JToolBarHelper::divider();
			JToolbarHelper::addNew();
			JToolbarHelper::deleteList();

			return;
		}
	}
}
