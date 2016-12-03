<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
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

		$db = ED::db();
		$query = 'DELETE FROM ' . $db->nameQuote('#__discuss_mailq');

		$db->setQuery($query);
		$db->Query();

		ED::setMessage(JText::_('COM_EASYDISCUSS_MAILS_PURGED'), 'success');

		return $this->app->redirect('index.php?option=com_easydiscuss&view=spools');
	}

	public function remove()
	{
		// Check for request forgeries
		ED::checkToken();

		$mails = $this->input->get('cid', '', 'POST');
		$message = '';
		$type = 'success';

		if (empty($mails)) {
			$message = JText::_('COM_EASYDISCUSS_NO_MAIL_ID_PROVIDED');
			$type = 'error';
		} else {
			$table = ED::table('MailQueue');

			foreach($mails as $id) {

				$table->load($id);

				if (!$table->delete()) {
					ED::setMessage(JText::_('COM_EASYDISCUSS_SPOOLS_DELETE_ERROR'), 'error');
					return $this->app->redirect('index.php?option=com_easydiscuss&view=spools');
				}
			}

			$message = JText::_('COM_EASYDISCUSS_SPOOLS_EMAILS_DELETED');
		}

		ED::setMessage($message, $type);

		$this->app->redirect('index.php?option=com_easydiscuss&view=spools');
	}
}
