<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

defined('_JEXEC') or die;

use Jdideal\Addons\Addon;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Registry\Registry;

/**
 * Logs view.
 *
 * @package  JDiDEAL
 * @since    2.0.0
 */
class JdidealgatewayViewLogs extends HtmlView
{
	/**
	 * Form with filters
	 *
	 * @var    Form
	 * @since  1.0.0
	 */
	public $filterForm = [];

	/**
	 * List of active filters
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	public $activeFilters = [];

	/**
	 * RO Payments helper
	 *
	 * @var    JdIdealgatewayHelper
	 * @since  4.0.0
	 */
	protected $jdidealgatewayHelper;

	/**
	 * List of properties
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	protected $items = [];

	/**
	 * The pagination object
	 *
	 * @var    Pagination
	 * @since  1.0.0
	 */
	protected $pagination;

	/**
	 * Object of the user state
	 *
	 * @var    Registry
	 * @since  1.0.0
	 */
	protected $state;

	/**
	 * List of addons that have been loaded
	 *
	 * @var    Addon
	 * @since  4.0.0
	 */
	protected $addons;

	/**
	 * The log history
	 *
	 * @var    string
	 * @since  4.0.0
	 */
	protected $history = '';

	/**
	 * Access rights of a user
	 *
	 * @var    CMSObject
	 * @since  4.0.0
	 */
	protected $canDo;

	/**
	 * The sidebar to show
	 *
	 * @var    string
	 * @since  4.0.0
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
	 * @since   2.13
	 */
	public function display($tpl = null)
	{
		$task        = Factory::getApplication()->input->get('task');
		$this->canDo = ContentHelper::getActions('com_jdidealgateway');
		/** @var JdidealgatewayModelLogs $model */
		$model       = $this->getModel();

		if ($task === 'history')
		{
			$this->history = $model->getHistory();
		}
		else
		{
			$this->items         = $model->getItems();
			$this->pagination    = $model->getPagination();
			$this->filterForm    = $model->getFilterForm();
			$this->activeFilters = $model->getActiveFilters();

			// Load all addons
			$this->addons = new Addon;
		}

		// Show the toolbar
		$this->toolbar();

		// Render the submenu
		$this->jdidealgatewayHelper = new JdidealGatewayHelper;
		$this->jdidealgatewayHelper->addSubmenu('logs');
		$this->sidebar = JHtmlSidebar::render();

		// Display it all
		return parent::display($tpl);
	}

	/**
	 * Display the toolbar.
	 *
	 * @return  void
	 *
	 * @throws  RuntimeException
	 *
	 * @since   2.13
	 */
	private function toolbar()
	{
		ToolbarHelper::title(Text::_('COM_ROPAYMENTS_JDIDEAL_LOGS'), 'clock');

		if ($this->canDo->get('core.delete'))
		{
			ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'logs.delete', 'JTOOLBAR_DELETE');
		}
	}
}
