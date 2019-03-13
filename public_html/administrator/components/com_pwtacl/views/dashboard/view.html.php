<?php
/**
 * @package    PwtAcl
 *
 * @author     Sander Potjer - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com/pwt-acl
 */

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\MVC\View\HtmlView;

// No direct access.
defined('_JEXEC') or die;

/**
 * Dashboard HTML view class.
 *
 * @since  3.0
 */
class PwtaclViewDashboard extends HtmlView
{
	/**
	 * @var     $sidebar
	 * @since   3.0
	 */
	protected $sidebar;

	/**
	 * Display the view
	 *
	 * @param   string $tpl Template
	 *
	 * @return  mixed
	 * @since   3.0
	 * @throws  Exception on errors
	 */
	public function display($tpl = null)
	{
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		// Include jQuery
		HTMLHelper::_('jquery.framework');

		// Load Javascript.
		HTMLHelper::_('script', 'media/com_pwtacl/js/datatables.min.js', array('version' => 'auto'));

		// Load the toolbar
		$this->addToolbar();

		// Load the sidebar
		PwtaclHelper::addSubmenu('dashboard');
		$this->sidebar = JHtmlSidebar::render();

		return parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 * @since   3.0
	 */
	protected function addToolbar()
	{
		// Title
		JToolBarHelper::title(Text::_('COM_PWTACL_SUBMENU_DASHBOARD'), 'pwtacl');

		// Toolbar buttons based on permissions
		$canDo = ContentHelper::getActions('com_pwtacl');

		if ($canDo->get('core.admin') || $canDo->get('core.options'))
		{
			JToolbarHelper::preferences('com_pwtacl');
		}
	}
}
