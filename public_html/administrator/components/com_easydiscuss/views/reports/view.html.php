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

class EasyDiscussViewReports extends EasyDiscussAdminView
{
	public function display($tpl = null)
	{
		$this->checkAccess('discuss.manage.reports');

		$this->setHeading('COM_EASYDISCUSS_REPORTS_TITLE', 'COM_EASYDISCUSS_REPORTS_DESC');
		JToolbarHelper::publishList();
		JToolbarHelper::unpublishList();

		$filter_state = $this->getUserState('com_easydiscuss.reports.filter_state', 'filter_state', '*', 'word');

		// Ordering
		$order = $this->getUserState('com_easydiscuss.reports.filter_order', 'filter_order', 'a.id', 'cmd');
		$orderDirection = $this->getUserState('com_easydiscuss.reports.filter_order_Dir', 'filter_order_Dir', '', 'word');

		$model = ED::model('Reports');

		$reports = $model->getReports();
		$pagination = $model->getPagination();

		if ($reports) {
			for($i = 0; $i < count($reports); $i++) {

				$report =& $reports[$i];

				$user = JFactory::getUser($report->reporter);

				$report->user = $user;

				$editLink	= JRoute::_('index.php?option=com_easydiscuss&controller=reports&task=edit&id='.$report->id);
				$published 	= JHTML::_('grid.published', $report, $i );

				$report->date = $report->lastreport;

				$actions	= array();
				$actions[]	= JHTML::_('select.option',  '', '- '. JText::_( 'COM_EASYDISCUSS_SELECT_ACTION' ) .' -' );
				$actions[]	= JHTML::_('select.option',  'D', JText::_( 'COM_EASYDISCUSS_DELETE_POST' ) );
				$actions[]	= JHTML::_('select.option',  'C', JText::_( 'COM_EASYDISCUSS_REMOVE_REPORT' ) );
				$actions[]	= JHTML::_('select.option',  'P', JText::_( 'COM_EASYDISCUSS_REPORT_PUBLISHED' ) );
				$actions[]	= JHTML::_('select.option',  'U', JText::_( 'COM_EASYDISCUSS_REPORT_UNPUBLISHED' ) );

				if ($report->user_id != 0) {
					$actions[] = JHTML::_('select.option',  'E', JText::_( 'COM_EASYDISCUSS_EMAIL_AUTHOR' ) );
				}

				$report->actions = JHTML::_('select.genericlist',   $actions, 'report-action-' . $report->id, ' style="width:250px;margin: 0;" data-action-type data-id="' . $report->id . '"', 'value', 'text', '*' );
				$report->viewLink = JURI::root() . 'index.php?option=com_easydiscuss&view=post&id=' . $report->id;

				if ($report->parent_id != 0) {
					$report->viewLink = JURI::root() . 'index.php?option=com_easydiscuss&view=post&id=' . $report->parent_id . '#' . JText::_('COM_EASYDISCUSS_REPORT_REPLY_PERMALINK') . '-' . $report->id;
				}
			}
		}

		$this->set('reports', $reports);
		$this->set('pagination', $pagination);
		$this->set('filter_state', $filter_state);
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);

		parent::display('reports/default');
	}

	/**
	 * Previews a reports
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function preview()
	{
		// Check for acl rules.
		$this->checkAccess('discuss.manage.spools');

		// Get the mail id
		$id = $this->input->get('id', 0, 'int');

		$reportModel = ED::model('reports');
		$reasons = $reportModel->getReasons($id);

		$result = array();
		if ($reasons) {
			foreach ($reasons as $row) {
				$user = JFactory::getUser($row->created_by);
				$row->user = $user;
				$row->date = ED::date($row->created);
				$result[] = $row;
			}
		}

		$theme = ED::themes();
		$theme->set('reasons', $result);

		echo $theme->output('admin/reports/reasons');

		exit;
	}
}
