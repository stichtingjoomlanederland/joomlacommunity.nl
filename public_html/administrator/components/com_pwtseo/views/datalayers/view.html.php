<?php
/**
 * @package    Pwtseo
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2021 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Pagination\Pagination;

defined('_JEXEC') or die;

/**
 * Datalayers view. Displays a list of datalayers
 *
 * @since    1.3.0
 */
class PWTSEOViewDatalayers extends HtmlView
{
	/**
	 * A list of items.
	 *
	 * @var    array
	 * @since  1.3.0
	 */
	protected $items;

	/**
	 * The Form filter object.
	 *
	 * @var    Form
	 * @since  1.3.0
	 */
	public $filterForm;

	/**
	 * List of active filters.
	 *
	 * @var    array
	 * @since  1.3.0
	 */
	public $activeFilters;

	/**
	 * The model state.
	 *
	 * @var    object
	 * @since  1.3.0
	 */
	protected $state;

	/**
	 * Pagination class.
	 *
	 * @var    Pagination
	 * @since  1.3.0
	 */
	protected $pagination;

	/**
	 * The sidebar to show
	 *
	 * @var    string
	 * @since  1.3.0
	 */
	protected $sidebar;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @since   1.3.0
	 */
	public function display($tpl = null)
	{
		$model               = $this->getModel();
		$this->items         = $model->getItems();
		$this->filterForm    = $model->getFilterForm();
		$this->activeFilters = $model->getActiveFilters();
		$this->state         = $model->getState();
		$this->pagination    = $model->getPagination();

		$this->toolbar();

		PWTSEOHelper::addSubmenu('datalayers');
		$this->sidebar = JHtmlSidebar::render();

		return parent::display($tpl);
	}

	/**
	 * Displays a toolbar for a specific page.
	 *
	 * @return  void
	 *
	 * @since   1.3.0
	 */
	private function toolbar()
	{
		JToolBarHelper::title(Text::_('COM_PWTSEO_DATALAYERS_HEADER'), 'pwtseo');

		/** @var \Joomla\CMS\Object\CMSObject $canDo */
		$canDo = ContentHelper::getActions('com_pwtseo');

		if ($canDo->get('core.create'))
		{
			JToolbarHelper::addNew('datalayer.add');
		}

		if ($canDo->get('core.copy'))
		{
			JToolbarHelper::save2copy('datalayer.save2copy');
		}

		if ($canDo->get('core.edit'))
		{
			JToolbarHelper::editList('datalayer.edit');
			JToolbarHelper::publish('datalayers.publish', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::unpublish('datalayers.unpublish', 'JTOOLBAR_UNPUBLISH', true);
		}

		if ($canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'datalayers.delete');
		}

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_pwtseo');
		}
	}
}
