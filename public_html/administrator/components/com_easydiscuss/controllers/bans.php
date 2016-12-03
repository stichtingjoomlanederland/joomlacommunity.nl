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

class EasyDiscussControllerBans extends EasyDiscussController
{
	function __construct()
	{
		parent::__construct();
	}

	public function purge()
	{
		ED::checkToken();

		$db = ED::db();
		$query = 'DELETE FROM ' . $db->quoteName('#__discuss_users_banned');

		$db->setQuery($query);
		$db->Query();

		ED::setMessage(JText::_('COM_EASYDISCUSS_BANS_PURGED'), 'success');

		$this->setRedirect('index.php?option=com_easydiscuss&view=bans');
	}

	function remove()
	{
		// Check for request forgeries
		ED::checkToken();

		$bans = JRequest::getVar('cid', '', 'POST');

		$message = '';
		$type = 'success';

		if (empty($bans)) {
			$message = JText::_('COM_EASYDISCUSS_NO_BAN_ID_PROVIDED');
			$type = 'error';
		}

		$table = ED::table('Ban');

		foreach ($bans as $id) {
			
			$table->load($id);

			if (!$table->delete()) {
				
				ED::setMessage(JText::_('COM_EASYDISCUSS_SPOOLS_DELETE_ERROR'), 'error');

				$this->setRedirect('index.php?option=com_easydiscuss&view=bans');
				return;
			}
		}

		$message = JText::_('COM_EASYDISCUSS_BAN_LISTS_DELETED');

		ED::setMessage($message, $type);

		$this->setRedirect('index.php?option=com_easydiscuss&view=bans');
	}
}
