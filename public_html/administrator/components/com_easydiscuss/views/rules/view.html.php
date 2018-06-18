<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ADMIN_ROOT . '/views/views.php');

class EasyDiscussViewRules extends EasyDiscussAdminView
{
	public function display($tpl = null)
	{
		$this->checkAccess('discuss.manage.rules');

		$this->setHeading('COM_EASYDISCUSS_MANAGE_RULES');
		JToolBarHelper::custom('newrule', 'save.png', 'save_f2.png', JText::_('COM_EASYDISCUSS_NEW_RULE_BUTTON'), false);
		JToolbarHelper::deleteList();

		$filter = $this->getUserState('rules.filter_state', 'filter_state', '*', 'word');
		$search = $this->app->getUserState('rules.search', 'search', '', 'string');

		$search = trim(JString::strtolower($search));
		$order = $this->app->getUserState('rules.filter_order', 'filter_order', 'a.id', 'cmd');
		$orderDirection	= $this->app->getUserState('rules.filter_order_Dir', 'filter_order_Dir', '', 'word');

		$model = ED::model('rules');
		$rules = $model->getRules();

		$pagination = ED::pagination();

		$this->set('rules', $rules);
		$this->set('pagination', $pagination);
		$this->set('state', $filter);
		$this->set('search', $search);
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);

		parent::display('rules/default');
	}

	public function install()
	{
		$this->title('COM_EASYDISCUSS_INSTALL_NEW_RULES');
		$this->desc('COM_EASYDISCUSS_INSTALL_NEW_RULES_DESC');
		
		return parent::display('rules/install');
	}
}