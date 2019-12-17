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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * Dashboard view.
 *
 * @package  PWT_Sitemap
 * @since    1.0.0
 */
class PwtSitemapViewDashboard extends HtmlView
{
	/**
	 * PWT_Sitemap helper
	 *
	 * @var    PwtSitemapHelper
	 * @since  1.0.0
	 */
	protected $helper;

	/**
	 * The sidebar to show
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $sidebar = '';

	/**
	 * If the system plugin of pwt sitemap is enabled
	 *
	 * @var    boolean
	 * @since  1.0.0
	 */
	protected $enabled;

	/**
	 * A short overview of PWT Sitemap menu items
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	protected $menuItems = [];

	/**
	 * A list of menu's
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	protected $menusList = [];

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @since   1.0.0
	 *
	 * @throws  Exception
	 */
	public function display($tpl = null)
	{
		$app           = Factory::getApplication();
		$this->enabled = PluginHelper::isEnabled('system', 'pwtsitemap');

		/** @var PwtSitemapModelDashboard $model */
		$model           = $this->getModel();
		$this->menuItems = $model->checkForMenuItems();
		$this->menusList = $model->getMenusList();

		// Show the toolbar
		$this->toolbar();

		// Show the sidebar
		PwtSitemapHelper::addSubmenu('dashboard');
		$this->sidebar = JHtmlSidebar::render();

		// Show messages about the enabled plugin
		if (!$this->enabled)
		{
			$app->enqueueMessage(Text::sprintf('COM_PWTSITEMAP_ERROR_SYSTEM_PLUGIN_DISABLED'), 'error');
		}

		return parent::display($tpl);
	}

	/**
	 * Displays a toolbar for a specific page.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	private function toolbar()
	{
		ToolbarHelper::title(Text::_('COM_PWTSITEMAP_TITLE_DASHBOARD'), 'pwtsitemap');

		$bar = Toolbar::getInstance('toolbar');
		$bar->appendButton(
			'Confirm', 'COM_PWTSITEMAP_ADD_TO_ROBOTS_WARNING', 'wrench', 'COM_PWTSITEMAP_ADD_TO_ROBOTS', 'dashboard.addToRobots', false
		);

		ToolbarHelper::preferences('com_pwtsitemap');
	}
}
