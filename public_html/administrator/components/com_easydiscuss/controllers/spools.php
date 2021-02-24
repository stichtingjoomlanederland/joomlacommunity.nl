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
defined('_JEXEC') or die('Restricted access');

class EasyDiscussControllerSpools extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('discuss.manage.spools');
	}

	public function purge()
	{
		ED::checkToken();

		$model = ED::model('Spools');
		$model->purge();

		// log the current action into database.
		$actionlog = ED::actionlog();
		$actionlog->log('COM_ED_ACTIONLOGS_MAILS_PURGED', 'spools');

		ED::setMessage(JText::_('COM_EASYDISCUSS_MAILS_PURGED'), 'success');
		$redirection = 'index.php?option=com_easydiscuss&view=spools';

		return ED::redirect($redirection);
	}

	public function remove()
	{
		// Check for request forgeries
		ED::checkToken();

		$mails = $this->input->get('cid', '', 'POST');
		$message = '';
		$type = 'success';
		$redirection = 'index.php?option=com_easydiscuss&view=spools';

		// log the current action into database.
		$actionlog = ED::actionlog();

		if (empty($mails)) {
			$message = JText::_('COM_EASYDISCUSS_NO_MAIL_ID_PROVIDED');
			$type = ED_MSG_ERROR;
		} else {
			$table = ED::table('MailQueue');

			foreach($mails as $id) {

				$table->load($id);

				$recipientEmail = $table->recipient;
				$emailSubject = $table->subject;

				$state = $table->delete();

				if (!$state) {
					ED::setMessage(JText::_('COM_EASYDISCUSS_SPOOLS_DELETE_ERROR'), ED_MSG_ERROR);
					return ED::redirect($redirection);
				}

				$actionlog->log('COM_ED_ACTIONLOGS_MAILS_SENT_PURGED', 'spools', array(
					'recipientEmail' => $recipientEmail,
					'emailSubject' => $emailSubject
				));

			}

			$message = JText::_('COM_EASYDISCUSS_SPOOLS_EMAILS_DELETED');
		}

		ED::setMessage($message, $type);

		return ED::redirect($redirection);
	}
}
