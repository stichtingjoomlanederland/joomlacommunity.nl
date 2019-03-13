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

class EasyDiscussViewSpools extends EasyDiscussAdminView
{
	public function display($tpl = null)
	{
		$this->checkAccess('discuss.manage.spools');

		$this->setHeading('COM_EASYDISCUSS_SPOOLS_TITLE', 'COM_EASYDISCUSS_SPOOLS_DESC');

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