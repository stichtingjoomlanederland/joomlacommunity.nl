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

class EasyDiscussControllerBans extends EasyDiscussController
{
	public function purge()
	{
		ED::checkToken();

		$model = ED::model('Bans');
		$model->purge();

		// log the current action into database.
		$actionlog = ED::actionlog();
		$actionlog->log('COM_ED_ACTIONLOGS_AUTHOR_PURGED_BANNED_USER', 'user');

		ED::setMessage(JText::_('COM_EASYDISCUSS_BANS_PURGED'), 'success');
		return ED::redirect('index.php?option=com_easydiscuss&view=bans');
	}

	public function remove()
	{
		// Check for request forgeries
		ED::checkToken();

		$bannedUsers = $this->input->get('cid', array(), 'array');
		$redirection = 'index.php?option=com_easydiscuss&view=bans';

		$message = '';
		$type = 'success';

		if (empty($bannedUsers)) {
			$message = JText::_('COM_EASYDISCUSS_NO_BAN_ID_PROVIDED');
			$type = ED_MSG_ERROR;
		}

		$banTbl = ED::table('Ban');

		// log the current action into database.
		$actionlog = ED::actionlog();

		foreach ($bannedUsers as $banId) {
			
			$banTbl->load($banId);

       		// Delete the banned user record now
			$state = $banTbl->delete();

			if (!$state) {
				ED::setMessage(JText::_('COM_EASYDISCUSS_SPOOLS_DELETE_ERROR'), ED_MSG_ERROR);
				return ED::redirect($redirection);
			}

	        $actionlogUserPermalink = $actionlog->normalizeActionLogUserPermalink($banTbl->userid);
			$actionlog->log('COM_ED_ACTIONLOGS_AUTHOR_DELETED_BANNED_USER', 'user', array(
				'authorLink' => $actionlogUserPermalink,
				'authorName' => $banTbl->banned_username
			));
		}

		$message = JText::_('COM_EASYDISCUSS_BAN_LISTS_DELETED');

		ED::setMessage($message, $type);
		return ED::redirect($redirection);
	}
}
