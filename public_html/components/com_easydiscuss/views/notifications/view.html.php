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

require_once(DISCUSS_ROOT . '/views/views.php');

class EasyDiscussViewNotifications extends EasyDiscussView
{
	/**
	 * Determines if notifications are enabled
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function isFeatureAvailable()
	{
		if (!$this->config->get('main_notifications')) {
			return false;
		}

		return true;
	}

	public function display($tpl = null)
	{
		// Ensure that the user is logged in
		ED::requireLogin();

		$my = ED::user();

		if (!$my->id) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_PLEASE_LOGIN_FIRST'), 'error');
			$this->app->redirect(EDR::getRoutedURL('index.php?option=com_easydiscuss', false, false));
		}

		ED::setPageTitle('COM_EASYDISCUSS_TITLE_NOTIFICATIONS');
		if (! EDR::isCurrentActiveMenu('notifications')) {
			$this->setPathway(JText::_( 'COM_EASYDISCUSS_BREADCRUMBS_NOTIFICATIONS'));
		}

		$limit = $this->config->get('main_notification_listings_limit', 5);

		$model = ED::model('Notification');
		// Get all notifications of the particular user given read and unread notifications.
		$notifications = $model->getNotifications($my->id, false, $limit);

		// Get the total unread notifications
		$totalNotifications = $model->getTotalNotifications($my->id);

		ED::Notifications()->format($notifications, true);

		// Get pagination
		$pagination = $model->getPagination();
		$pagination = $pagination->getPagesLinks();

		$this->set('notifications', $notifications);
		$this->set('totalNotifications', $totalNotifications);
		$this->set('pagination', $pagination);

		parent::display('notifications/default');
	}
}
