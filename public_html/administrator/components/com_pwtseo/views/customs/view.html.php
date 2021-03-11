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
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Toolbar\Toolbar;

defined('_JEXEC') or die;

/**
 * Customs view. Displays a list of custom URL's
 *
 * @since    1.1.0
 */
class PWTSEOViewCustoms extends HtmlView
{
	/**
	 * A list of items.
	 *
	 * @var    array
	 * @since  1.1.0
	 */
	protected $items;

	/**
	 * The Form filter object.
	 *
	 * @var    Form
	 * @since  1.1.0
	 */
	public $filterForm;

	/**
	 * List of active filters.
	 *
	 * @var    array
	 * @since  1.1.0
	 */
	public $activeFilters;

	/**
	 * The model state.
	 *
	 * @var    object
	 * @since  1.1.0
	 */
	protected $state;

	/**
	 * Pagination class.
	 *
	 * @var    Pagination
	 * @since  1.1.0
	 */
	protected $pagination;

	/**
	 * The sidebar to show
	 *
	 * @var    string
	 * @since  1.1.0
	 */
	protected $sidebar;

	/**
	 * The form for the batch dialog
	 *
	 * @var    Form
	 * @since  1.5.0
	 */
	protected $batchForm;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @since   1.1.0
	 */
	public function display($tpl = null)
	{
		$model               = $this->getModel();
		$this->items         = $model->getItems();
		$this->filterForm    = $model->getFilterForm();
		$this->activeFilters = $model->getActiveFilters();
		$this->state         = $model->getState();
		$this->pagination    = $model->getPagination();

		$this->batchForm = Form::getInstance('pwtseo_batch', JPATH_COMPONENT_ADMINISTRATOR . '/models/forms/batch.xml');

		$this->toolbar();

		PWTSEOHelper::addSubmenu('customs');
		$this->sidebar = JHtmlSidebar::render();

		return parent::display($tpl);
	}

	/**
	 * Displays a toolbar for a specific page.
	 *
	 * @return  void
	 *
	 * @since   1.1.0
	 */
	private function toolbar()
	{
		JToolBarHelper::title(Text::_('COM_PWTSEO_CUSTOMS_HEADER'), 'pwtseo');

		$bar   = Toolbar::getInstance('toolbar');
		$canDo = ContentHelper::getActions('com_pwtseo');

		if ($canDo->get('core.create'))
		{
			JToolbarHelper::addNew('custom.add');
		}

		if ($canDo->get('core.edit'))
		{
			$title = Text::_('JTOOLBAR_BATCH');

			$layout = new FileLayout('joomla.toolbar.batch');

			$dhtml = $layout->render(array('title' => $title));
			$bar->appendButton('Custom', $dhtml, 'batch');

			JToolbarHelper::editList('custom.edit');
		}

		if ($canDo->get('core.delete'))
		{
			JToolbarHelper::trash('customs.delete');
		}

		if ($canDo->get('core.edit'))
		{
			JToolbarHelper::custom('export.custom', 'download', 'download', 'COM_PWTSEO_ARTICLES_EXPORT', false);
		}

		// Options button.
		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_pwtseo');
		}
	}
}
