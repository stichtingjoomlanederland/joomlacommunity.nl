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

require_once(DISCUSS_ADMIN_ROOT . '/views/views.php');

class EasyDiscussViewSubscription extends EasyDiscussAdminView
{
	public function display($tpl = null)
	{
		$this->checkAccess('discuss.manage.subscriptions');

		// Set page properties
		$this->title('COM_EASYDISCUSS_SUBSCRIPTION');

		$filter = $this->getUserState('subscription.filter', 'filter', 'site', 'word');

		// Search
		$search = $this->getUserState('subscription.search', 'search', '', 'string');
		$search = trim(strtolower($search));

		// Ordering
		$order = $this->getUserState('subscription.filter_order', 'filter_order', 'fullname', 'cmd');
		$orderDirection = $this->getUserState('subscription.filter_order_Dir', 'filter_order_Dir', '', 'word');

		$model = ED::model('Subscribe');
		$subscriptions = $model->getSubscription();

		$pagination = $model->getPagination();

		$this->set('subscriptions', $subscriptions);
		$this->set('pagination', $pagination);
		$this->set('filter', $filter);
		$this->set('search', $search);
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);

		parent::display('subscriptions/default');
	}

	public function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_SUBSCRIPTION' ), 'subscriptions' );

		JToolBarHelper::custom( 'home', 'back', '', JText::_( 'COM_EASYDISCUSS_TOOLBAR_HOME' ), false);
		JToolBarHelper::divider();
		JToolbarHelper::deleteList();
	}
}
