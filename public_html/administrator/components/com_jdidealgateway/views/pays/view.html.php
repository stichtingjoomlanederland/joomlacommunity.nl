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
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * Payment view.
 *
 * @package  JDiDEAL
 * @since    2.0.0
 */
class JdidealgatewayViewPays extends HtmlView
{
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
	 * @since  2.0.0
	 */
	protected $items = [];

	/**
	 * The pagination object
	 *
	 * @var    Pagination
	 * @since  2.0.0
	 */
	protected $pagination;

	/**
	 * Form with filters
	 *
	 * @var    Form
	 * @since  2.0.0
	 */
	public $filterForm = [];

	/**
	 * List of active filters
	 *
	 * @var    array
	 * @since  2.0.0
	 */
	public $activeFilters = [];

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
	 * @since  2.0.0
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
	 * @since   2.0.0
	 */
	public function display($tpl = null)
	{
		/** @var JdidealgatewayModelPays $model */
		$model = $this->getModel();
		$this->items         = $model->getItems();
		$this->pagination    = $model->getPagination();
		$this->filterForm    = $model->getFilterForm();
		$this->activeFilters = $model->getActiveFilters();
		$this->canDo         = ContentHelper::getActions('com_jdidealgateway');

		$this->toolbar();

		if (JVERSION < 4)
		{
			$this->jdidealgatewayHelper = new JdidealGatewayHelper;
			$this->jdidealgatewayHelper->addSubmenu('pays');
			$this->sidebar = JHtmlSidebar::render();
		}

		return parent::display($tpl);
	}

	/**
	 * Displays a toolbar for a specific page.
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	private function toolbar(): void
	{
		ToolbarHelper::title(Text::_('COM_ROPAYMENTS_JDIDEAL_PAY'), 'credit');

		if ($this->canDo->get('core.delete'))
		{
			ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'pays.delete', 'JTOOLBAR_DELETE');
		}
	}
}
