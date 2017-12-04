<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ADMIN_ROOT . '/views/views.php');

class EasyDiscussViewBans extends EasyDiscussAdminView
{
	public function display($tpl = null)
	{
		JToolBarHelper::title(JText::_('COM_EASYDISCUSS_BANS'), 'users');
		JToolbarHelper::deleteList();
		JToolBarHelper::custom('purge','purge','icon-32-unpublish.png', 'COM_EASYDISCUSS_SPOOLS_PURGE_ALL_BUTTON', false);

		$state = $this->getUserState('com_easydiscuss.bans.filter_state', 'filter_state', '*', 'word');

		// Search
		$search = $this->getUserState('com_easydiscuss.bans.search', 'search', '', 'string');
		$search = trim(strtolower($search));

		// Ordering
		$order = $this->getUserState('com_easydiscuss.bans.filter_order', 'filter_order', 'id', 'cmd');
		$orderDirection = $this->getUserState('com_easydiscuss.bans.filter_order_Dir', 'filter_order_Dir', 'asc', 'word');

		// Get data from the model
		$model = ED::model('Bans');
		$bans = $model->getData();

		if ($bans) {
			foreach ($bans as $ban) {
				if ($ban->created_by == 0) {
					$ban->created_by = 'Guest';
				} else {
					$user = JFactory::getUser($ban->created_by);
					$ban->created_by = $user->name;
				}
			}
		}


		$pagination = $model->getPagination();
		$saveOrder = $order == 'a.ordering';

		$this->title('COM_EASYDISCUSS_BANS_TITLE');
		$this->desc('COM_EASYDISCUSS_BANS_DESC');

		$this->set('saveOrder', $saveOrder);
		$this->set('bans', $bans);
		$this->set('pagination', $pagination);
		$this->set('state', $state);
		$this->set('search', $search);
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);

		parent::display('bans/default');
	}
}