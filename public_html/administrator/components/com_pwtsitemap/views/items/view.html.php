<?php
/**
 * @package    Pwtsitemap
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View class for a list of menuitems
 *
 * @since  1.0.0
 */
class PwtSitemapViewItems extends HtmlView
{
	/**
	 * Filters form
	 *
	 * @var    Form
	 * @since  1.0.0
	 */
	public $filterForm;

	/**
	 * Selected filters
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	public $activeFilters = [];

	/**
	 * The item data.
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	protected $items = [];

	/**
	 * Pagination class
	 *
	 * @var    Pagination
	 * @since  1.0.0
	 */
	protected $pagination;

	/**
	 * The model state.
	 *
	 * @var    CMSObject
	 * @since  1.0.0
	 */
	protected $state;

	/**
	 * The sidebar menu
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $sidebar = '';

	/**
	 * Menu item ordering
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	protected $ordering = [];

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @since   1.0.0
	 *
	 * @throws  Exception
	 */
	public function display($tpl = null)
	{
		/** @var PwtSitemapModelItems $model */
		$model               = $this->getModel();
		$this->items         = $model->getItems();
		$this->pagination    = $model->getPagination();
		$this->filterForm    = $model->getFilterForm();
		$this->activeFilters = $model->getActiveFilters();
		$this->state         = $model->getState();

		$this->ordering = [];

		// Preprocess the list of items to find ordering divisions.
		foreach ($this->items as $item)
		{
			
			$this->ordering[$item->parent_id][] = $item->id;
			
		}

		// Add submenus
		PwtSitemapHelper::addSubmenu('items');
		$this->sidebar = JHtmlSidebar::render();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		$this->addToolbar();

		return parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	protected function addToolbar()
	{
		ToolbarHelper::title(Text::_('COM_PWTSITEMAP_TITLE_ITEMS'), 'pwtsitemap');

		$title = Text::_('JTOOLBAR_BATCH');

		// Instantiate a new JLayoutFile instance and render the batch button
		$layout = new FileLayout('joomla.toolbar.batch');

		$dhtml = $layout->render(['title' => $title]);
		Toolbar::getInstance()->appendButton('Custom', $dhtml, 'batch');
	}
}
