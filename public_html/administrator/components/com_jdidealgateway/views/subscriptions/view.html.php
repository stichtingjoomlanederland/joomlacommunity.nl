<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Registry\Registry;

/**
 * Subscriptions list.
 *
 * @package  JDiDEAL
 * @since    5.0.0
 */
class JdidealgatewayViewSubscriptions extends HtmlView
{
	/**
	 * Form with filters
	 *
	 * @var    Form
	 * @since  5.0.0
	 */
	public $filterForm = [];

	/**
	 * List of active filters
	 *
	 * @var    array
	 * @since  5.0.0
	 */
	public $activeFilters = [];

	/**
	 * List of properties
	 *
	 * @var    array
	 * @since  5.0.0
	 */
	protected $items = [];

	/**
	 * The model state
	 *
	 * @var    Registry
	 * @since  5.0.0
	 */
	protected $state;

	/**
	 * The pagination object
	 *
	 * @var    Pagination
	 * @since  5.0.0
	 */
	protected $pagination;

	/**
	 * Access rights of a user
	 *
	 * @var    Registry
	 * @since  5.0.0
	 */
	protected $canDo;

	/**
	 * The sidebar to show
	 *
	 * @var    string
	 * @since  5.0.0
	 */
	protected $sidebar = '';

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @throws  Exception
	 *
	 * @since   5.0.0
	 */
	public function display($tpl = null)
	{
		/** @var JdidealgatewayModelSubscriptions $model */
		$model               = $this->getModel();
		$this->items         = $model->getItems();
		$this->state         = $model->getState();
		$this->pagination    = $model->getPagination();
		$this->filterForm    = $model->getFilterForm();
		$this->activeFilters = $model->getActiveFilters();
		$this->canDo         = ContentHelper::getActions('com_jdidealgateway');

		$this->toolbar();

		if (JVERSION < 4)
		{
			$jdidealgatewayHelper = new JdidealGatewayHelper;
			$jdidealgatewayHelper->addSubmenu('subscriptions');
			$this->sidebar = JHtmlSidebar::render();
		}

		return parent::display($tpl);
	}

	/**
	 * Displays a toolbar for a specific page.
	 *
	 * @return  void
	 *
	 * @since   5.0.0
	 */
	private function toolbar(): void
	{
		ToolbarHelper::title(Text::_('COM_ROPAYMENTS_SUBSCRIPTIONS'), 'calendar');

		if ($this->canDo->get('core.create'))
		{
//			ToolbarHelper::addNew('subscription.add');
			ToolbarHelper::custom('subscriptions.sync', 'refresh', 'refresh', Text::_('COM_ROPAYMENTS_SYNC'), false);
		}

		if ($this->canDo->get('core.edit') || $this->canDo->get('core.edit.own'))
		{
//			ToolbarHelper::editList('subscription.edit');
		}

		if ($this->canDo->get('core.delete'))
		{
			ToolbarHelper::deleteList('COM_ROPAYMENTS_CONFIRM_SUBSCRIPTION_DELETE', 'subscriptions.delete', 'JTOOLBAR_CANCEL');
		}
	}
}
