<?php
/**
 * @package    Pwtsitemap
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2018 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

defined('_JEXEC') or die;

/**
 * Dashboard view.
 *
 * @package  PWT_Sitemap
 * @since    1.0
 */
class PwtSitemapViewDashboard extends JViewLegacy
{
	/**
	 * PWT_Sitemap helper
	 *
	 * @var    PwtSitemapHelper
	 * @since  1.0
	 */
	protected $helper;

	/**
	 * The sidebar to show
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $sidebar = '';

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @see     fetch()
	 * @since   1.0
	 */
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$this->enabled = JPluginHelper::isEnabled('system', 'pwtsitemap');

		// Show the toolbar
		$this->toolbar();

		// Show the sidebar
		PwtSitemapHelper::addSubmenu('dashboard');
		$this->sidebar = JHtmlSidebar::render();

		// Show messages about the enabled plugin
		if (!$this->enabled )
		{
			$app->enqueueMessage(JText::sprintf('COM_PWTSITEMAP_ERROR_SYSTEM_PLUGIN_DISABLED'), 'error');
		}

		return parent::display($tpl);
	}

	/**
	 * Displays a toolbar for a specific page.
	 *
	 * @return  void.
	 *
	 * @since   1.0
	 */
	private function toolbar()
	{
		JToolBarHelper::title( JText::_('COM_PWTSITEMAP_TITLE_DASHBOARD'), 'pwtsitemap');

		JToolbarHelper::preferences('com_pwtsitemap');
	}
}
