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

class EasyDiscussViewReports extends EasyDiscussAdminView
{
	public function display($tpl = null)
	{
		$this->checkAccess('discuss.manage.reports');

		$this->setHeading('COM_EASYDISCUSS_REPORTS_TITLE', 'COM_EASYDISCUSS_REPORTS_DESC');
		
		JToolbarHelper::deleteList(JText::_('COM_ED_CONFIRM_DELETE_REPORTS'));

		$order = $this->getUserState('com_easydiscuss.reports.filter_order', 'filter_order', 'a.id', 'cmd');
		$orderDirection = $this->getUserState('com_easydiscuss.reports.filter_order_Dir', 'filter_order_Dir', '', 'word');

		$model = ED::model('Reports');
		$reports = $model->getReports();
		$pagination = $model->getPagination();

		if ($reports) {
			for($i = 0; $i < count($reports); $i++) {

				$report =& $reports[$i];

				$report->post = ED::post($report->id);
				$report->user = JFactory::getUser($report->reporter);
				$report->date = $report->lastreport;

				if ($report->user_id != 0) {
					$actions[] = JHTML::_('select.option',  'E', JText::_( 'COM_EASYDISCUSS_EMAIL_AUTHOR' ) );
				}
			}
		}

		$this->set('reports', $reports);
		$this->set('pagination', $pagination);
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
		$this->checkAccess('discuss.manage.reports');

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

		$this->set('reasons', $result);

		parent::display('reports/reasons');
	}
}
