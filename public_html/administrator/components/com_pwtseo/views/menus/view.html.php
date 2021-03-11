<?php
/**
 * @package    Pwtseo
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2021 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\Toolbar;

defined('_JEXEC') or die;

/**
 * Articles view. This gives an overview of the SEO score for articles and provides a link to the article edit page to improve the score.
 *
 * @since    1.2.0
 */
class PWTSEOViewMenus extends HtmlView
{
	/**
	 * A list of articles. This list includes all articles regardless of score or SEO keyword.
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $items;

	/**
	 * The JForm filter object.
	 *
	 * @var    JForm
	 * @since  1.0
	 */
	public $filterForm;

	/**
	 * List of active filters.
	 *
	 * @var    array
	 * @since  1.0
	 */
	public $activeFilters;

	/**
	 * The model state.
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $state;

	/**
	 * Pagination class.
	 *
	 * @var    JPagination
	 * @since  1.0
	 */
	protected $pagination;

	/**
	 * The sidebar to show
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $sidebar;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @see     fetch()
	 * @since   1.0
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

		PWTSEOHelper::addSubmenu('menus');
		$this->sidebar = JHtmlSidebar::render();

		return parent::display($tpl);
	}

	/**
	 * Displays a toolbar for a specific page.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	private function toolbar()
	{
		JToolBarHelper::title(Text::_('COM_PWTSEO_MENUS_HEADER'), 'pwtseo');
		$user  = Factory::getUser();

		$bar = Toolbar::getInstance('toolbar');

		if ($user->authorise('core.edit', 'com_content'))
		{
			$title = Text::_('JTOOLBAR_BATCH');

			$layout = new FileLayout('joomla.toolbar.batch');

			$dhtml = $layout->render(array('title' => $title));
			$bar->appendButton('Custom', $dhtml, 'batch');

			JToolbarHelper::custom('export.menuitems', 'download', 'download', 'COM_PWTSEO_ARTICLES_EXPORT', false);
		}

		// Options button.
		if (Factory::getUser()->authorise('core.admin', 'com_pwtseo'))
		{
			JToolBarHelper::preferences('com_pwtseo');
		}
	}
}
