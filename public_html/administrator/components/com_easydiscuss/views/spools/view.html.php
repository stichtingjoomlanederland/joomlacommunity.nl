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

class EasyDiscussViewSpools extends EasyDiscussAdminView
{
	public function display($tpl = null)
	{
		$this->checkAccess('discuss.manage.spools');

		$this->setHeading('COM_EASYDISCUSS_SPOOLS_TITLE', 'COM_EASYDISCUSS_SPOOLS_DESC');
		$this->addHelpButton('/docs/easydiscuss/administrators/cronjobs/understanding-cronjobs');
		
		JToolbarHelper::deleteList();
		JToolBarHelper::custom('purge','purge','icon-32-unpublish.png', 'COM_EASYDISCUSS_SPOOLS_PURGE_ALL_BUTTON', false);

		$model = ED::model('Spools');
		$mails = $model->getData();

		$pagination = $model->getPagination();

		// Filtering state
		$filter = $this->getUserState('spools.filter_state', 'filter_state', 'U', 'word');

		if ($mails) {
			foreach ($mails as &$mail) {
				$date = ED::date($mail->created);
				$mail->date = $date->display(JText::_('DATE_FORMAT_LC5'));
			}
		}

		// Determine the last execution time of the cronjob if there is
		$cronLastExecuted = $this->config->get('cron_last_execute', '');

		if ($cronLastExecuted) {
			$cronLastExecuted = JFactory::getDate($cronLastExecuted)->format(JText::_('DATE_FORMAT_LC2'));
		}

		$this->set('cronLastExecuted', $cronLastExecuted);
		$this->set('filter', $filter);
		$this->set('mails', $mails);
		$this->set('pagination', $pagination);

		parent::display('spools/default');
	}

	/**
	 * Previews a mail
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

		$mailq = ED::table('Mailqueue');
		$mailq->load($id);

		$contents = $mailq->getBody();

		echo $contents;
		exit;
	}
}