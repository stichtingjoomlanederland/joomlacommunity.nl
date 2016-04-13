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
	public function display($tpl = null)
	{
		$my = ED::user();

		if (!$my->id) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_PLEASE_LOGIN_FIRST'), 'error');
			$this->app->redirect(EDR::getRoutedURL('index.php?option=com_easydiscuss', false, false));
		}

		$model = ED::model('Notification');
		$this->setPathway(JText::_( 'COM_EASYDISCUSS_BREADCRUMBS_NOTIFICATIONS'));

		$limit = $this->config->get('main_notifications_limit', 5);

		// Get all notifications of the particular user given read and unread notifications.
		$notifications = $model->getNotifications($my->id, false, $limit);

		// Get the total unread notifications
		$totalNotifications = $model->getTotalNotifications($my->id);

		ED::Notifications()->format($notifications, true);

		// Get pagination
		$pagination = $model->getPagination();

		$this->set('notifications', $notifications);
		$this->set('totalNotifications', $totalNotifications);
		$this->set('pagination', $pagination);
	
		parent::display('notifications/default');
	}
}
