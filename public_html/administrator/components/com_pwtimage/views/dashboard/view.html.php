<?php
/**
 * @package    Pwtimage
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2020 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\MVC\View\HtmlView;

defined('_JEXEC') or die;

/**
 * PWT image view.
 *
 * @package  Pwtimage
 * @since    1.0
 */
class PwtimageViewDashboard extends HtmlView
{
	/**
	 * Array with profiles
	 *
	 * @var    array
	 * @since  1.5.0
	 */
	protected $items;
	/**
	 * Access rights of a user
	 *
	 * @var    Joomla\CMS\Object\CMSObject
	 * @since  1.5.0
	 */
	protected $canDo;

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
	 *
	 * @throws  Exception
	 */
	public function display($tpl = null)
	{
		/** @var PwtImageModelProfiles $profiles */
		$profiles       = BaseDatabaseModel::getInstance('Profiles', 'PwtImageModel', ['ignore_request' => true]);
		$this->profiles = $profiles->getItems();
		$this->canDo    = ContentHelper::getActions('com_pwtimage');

		// Render the sidebar
		$pwtimageHelper = new PwtimageHelper;
		$pwtimageHelper->addSubmenu('dashboard');
		$this->sidebar = JHtmlSidebar::render();

		// Add the toolbar
		$this->addToolbar();

		// Display it all
		return parent::display($tpl);
	}

	/**
	 * Displays a toolbar for a specific page.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 *
	 * @throws  Exception
	 */
	private function addToolbar()
	{
		$canDo = ContentHelper::getActions('com_pwtimage');

		JToolbarHelper::title(Text::_('COM_PWTIMAGE_SUBMENU_DASHBOARD'), 'pwtimage');

		if ($canDo->get('core.admin') || $canDo->get('core.options'))
		{
			JToolbarHelper::preferences('com_pwtimage');
		}
	}
}
