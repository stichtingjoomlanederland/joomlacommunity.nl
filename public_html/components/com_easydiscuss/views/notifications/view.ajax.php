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

require_once(DISCUSS_ROOT . '/views/views.php');

class EasyDiscussViewNotifications extends EasyDiscussView
{
	/**
	 * Generates the notifications for the toolbar
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function popbox()
	{
		// Ensure that the user is logged in
		ED::requireLogin();

		if (!$this->config->get('main_notifications')) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_NOT_ALLOWED'));
		}

		$model = ED::model('Notification');
		$notifications = $model->getNotifications($this->my->id, true, $this->config->get('main_notifications_limit'));

		// Format notifications
		ED::notifications()->format($notifications);

		$theme = ED::themes();
		$theme->set('notifications', $notifications);
		$html = $theme->output('site/notifications/popbox');

		return $this->ajax->resolve($html);
	}

	/**
	 * Retrieves the total number of unread notifications
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function count()
	{
		if ($this->my->guest) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_NOT_ALLOWED'));
		}

		$model = ED::model('Notification');
		$count = $model->getTotalNotifications($this->my->id);
		
		return $this->ajax->resolve($count);
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
		ED::requireLogin();

		$model = ED::model('Notification');
		$model->markAllRead();

		return $this->ajax->resolve();
	}	
}
