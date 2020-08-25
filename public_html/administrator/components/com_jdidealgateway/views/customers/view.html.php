<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Registry\Registry;

defined('_JEXEC') or die;

/**
 * Customers list.
 *
 * @package  JDiDEAL
 * @since    5.0.0
 */
class JdidealgatewayViewCustomers extends HtmlView
{
	/**
	 * List of properties
	 *
	 * @var    array
	 * @since  5.0.0
	 */
	protected $items = array();

	/**
	 * The model state
	 *
	 * @var    Registry
	 * @since  5.0.0
	 */
	protected $state;

	/**
	 * Form with filters
	 *
	 * @var    array
	 * @since  5.0.0
	 */
	public $filterForm = array();

	/**
	 * List of active filters
	 *
	 * @var    array
	 * @since  5.0.0
	 */
	public $activeFilters = array();

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
	 * @since   5.0.0
	 */
	public function display($tpl = null)
	{
		/** @var JdidealgatewayModelCustomers $model */
		$model = $this->getModel();
		$this->items         = $model->getItems();
		$this->state         = $model->getState();
		$this->pagination    = $model->getPagination();
		$this->filterForm    = $model->getFilterForm();
		$this->activeFilters = $model->getActiveFilters();
		$this->canDo         = ContentHelper::getActions('com_jdidealgateway');

		// Show the toolbar
		$this->toolbar();

		// Render the sidebar
		$jdidealgatewayHelper = new JdidealGatewayHelper;
		$jdidealgatewayHelper->addSubmenu('customers');
		$this->sidebar = JHtmlSidebar::render();

		// Display it all
		return parent::display($tpl);
	}

	/**
	 * Displays a toolbar for a specific page.
	 *
	 * @return  void.
	 *
	 * @since   5.0.0
	 */
	private function toolbar()
	{
		ToolbarHelper::title(Text::_('COM_ROPAYMENTS_CUSTOMERS'), 'user');

		if ($this->canDo->get('core.create'))
		{
			ToolbarHelper::addNew('customer.add');
		}

		if ($this->canDo->get('core.edit') || $this->canDo->get('core.edit.own'))
		{
			ToolbarHelper::editList('customer.edit');
		}

		if ($this->canDo->get('core.delete'))
		{
			ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'customers.delete', 'JTOOLBAR_DELETE');
		}
	}
}
