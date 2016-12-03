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



class EasyDiscussViewMaintenance extends EasyDiscussAdminView
{

	public function display($tpl = null)
	{
		$this->checkAccess('discuss.manage.maintenance');

		// Set page attributes
		$this->title('COM_EASYDISCUSS_MAINTENANCE_TITLE_SCRIPTS');
		$this->desc('COM_EASYDISCUSS_MAINTENANCE_TITLE_SCRIPTS_DESC');

		if ($this->input->get('success', 0, 'int')) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_MAINTENANCE_SUCCESSFULLY_EXECUTED_SCRIPT'), 'success');
		}

		JToolbarHelper::custom('maintenance.form', 'refresh', '', JText::_('COM_EASYDISCUSS_MAINTENANCE_EXECUTE_SCRIPTS'));

		// filters
		$version = $this->app->getUserStateFromRequest('com_easydiscuss.maintenance.filter_version', 'filter_version', 'all', 'cmd');

		$order = $this->app->getUserStateFromRequest('com_easydiscuss.maintenance.filter_order', 'filter_order', 'version', 'cmd');
		$orderDirection	= $this->app->getUserStateFromRequest('com_easydiscuss.maintenance.filter_order_Dir', 'filter_order_Dir', 'asc', 'word');

		$versions = array();

		$model = ED::model('Maintenance');
		$model->setState('version', $version);
		$model->setState('ordering', $order);
		$model->setState('direction', $orderDirection);

		$scripts = $model->getItems();
		$pagination = $model->getPagination();

		$versions = $model->getVersions();

		$this->set('version', $version);
		$this->set('scripts', $scripts);
		$this->set('versions', $versions);
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);
		$this->set('pagination', $pagination);

		parent::display('maintenance/default');
	}

	public function form($tpl = null)
	{
		$this->checkAccess('discuss.manage.maintenance');

		$cids = $this->input->get('cid', array(), 'var');

		$scripts = ED::model('Maintenance')->getItemByKeys($cids);

		$this->set('scripts', $scripts);

		parent::display('maintenance/form');
	}

	/**
	 * Displays the theme installer form
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function database($tpl = null)
	{
		// Check for access
		$this->checkAccess('discuss.manage.maintenance');

		$this->title('COM_EASYDISCUSS_MAINTENANCE_TITLE_DATABASE');
		$this->desc('COM_EASYDISCUSS_MAINTENANCE_TITLE_DATABASE_DESC');

		parent::display('maintenance/database');
	}

	public function registerToolbar()
	{
		JToolBarHelper::title(JText::_('COM_EASYDISCUSS_MAINTENANCE_TITLE'), 'maintenance');
	}
}
