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
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussControllerNotification extends EasyDiscussController
{
	/**
	 * Mark a single notification item as read
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function markread()
	{
		// Ensure that the user is logged in
		ED::requireLogin();

		// Get the notification id
		$id = $this->input->get('id', '', 'int');

		$notification = ED::table('Notifications');
		$notification->load($id);

		$redirect = EDR::_('', false);

		if ($notification->target != $this->my->id) {
			ED::setMessage('COM_EASYDISCUSS_NOT_ALLOWED_TO_MARK_READ', 'error');

			return $this->app->redirect($redirect);
		}

		$notification->state = 0;
		$notification->store();

		ED::setMessage('COM_EASYDISCUSS_NOTIFICATION_MARKED_AS_READ');

		$redirect = EDR::_('view=notifications', false);
		return $this->app->redirect($redirect);
	}

	/**
	 * Marks all discussion as read
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function markreadall()
	{
		// Ensure that the user is logged in
		ED::requireLogin();

		$redirect = EDR::_('view=notifications', false);

		$model = ED::model('Notification');
		$model->markAllRead();

		ED::setMessage('COM_EASYDISCUSS_ALL_NOTIFICATIONS_MARKED_AS_READ');
		
		return $this->app->redirect($redirect);
	}
}
