<?php
/**
 * @package    PwtAcl
 *
 * @author     Sander Potjer - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 - 2020 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com/pwt-acl
 */

use Joomla\CMS\Access\Access;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

defined('_JEXEC') or die;

/**
 * Assets HTML view class.
 *
 * @since   3.0
 */
class PwtAclViewAssets extends HtmlView
{
	/**
	 * @var     $items
	 * @since   3.0
	 */
	protected $items;

	/**
	 * @var     $pagination
	 * @since   3.0
	 */
	protected $pagination;

	/**
	 * @var     $state
	 * @since   3.0
	 */
	protected $state;

	/**
	 * @var     $group
	 * @since   3.0
	 */
	protected $group;

	/**
	 * @var     $user
	 * @since   3.0
	 */
	protected $user;

	/**
	 * @var     $type
	 * @since   3.0
	 */
	public $type;

	/**
	 * @var     $filterForm
	 * @since   3.0
	 */
	public $filterForm;

	/**
	 * @var     $coreactions
	 * @since   3.0
	 */
	protected $coreactions;

	/**
	 * @var     $activeFilters
	 * @since   3.0
	 */
	public $activeFilters;

	/**
	 * @var     $assets
	 * @since   3.0
	 */
	protected $assets;

	/**
	 * @var     $sidebar
	 * @since   3.0
	 */
	protected $sidebar;

	/**
	 * @var     $params
	 * @since   3.0
	 */
	protected $params;

	/**
	 * @var     $groupsParent
	 * @since   3.0
	 */
	protected $groupsParent;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template
	 *
	 * @return  mixed
	 * @since   3.0
	 * @throws  Exception on errors
	 */
	public function display($tpl = null)
	{
		$this->params        = ComponentHelper::getParams('com_pwtacl');
		$this->coreactions   = $this->get('CoreActions');
		$this->state         = $this->get('State');
		$this->assets        = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		$this->groupsParent  = $this->get('GroupsParent');
		$this->group         = $this->state->get('group');
		$this->user          = $this->state->get('user');
		$this->type          = $this->state->get('type');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		// Show recommendation to not edit the groupsParent
		if ($this->group === $this->groupsParent)
		{
			Factory::getApplication()->enqueueMessage(Text::_('COM_PWTACL_ASSETS_NOTICE_PUBLIC_PERMISSIONS'), 'info');
		}

		// Show message that you can't edit user permissions
		if ($this->user)
		{
			Factory::getApplication()->enqueueMessage(Text::_('COM_PWTACL_ASSETS_NOTICE_USER_PERMISSIONS'), 'info');
		}

		HTMLHelper::_('jquery.framework');
		HTMLHelper::_('script', 'media/com_pwtacl/js/permissions.js', ['version' => 'auto']);

		// Add options for JS
		Factory::getDocument()->addScriptOptions('pwtacl', [
			'superuseralert' => Text::_('COM_PWTACL_ASSETS_NOTICE_SUPERUSER_ACCESS')
		]);

		// Load the toolbar
		$this->addToolbar();

		// Load the sidebar
		PwtAclHelper::addSubmenu($this->type);
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
		$bar = Toolbar::getInstance('toolbar');

		// Toolbar Title
		$title = Text::_('COM_PWTACL_SUBMENU_' . $this->type);

		// Add group name
		if ($this->group)
		{
			$title .= ' - ' . Access::getGroupTitle($this->group);
		}

		// Add user name
		if ($this->user)
		{
			$title .= ' - ' . Factory::getUser($this->user)->get('name');
		}

		ToolbarHelper::title($title, 'pwtacl');

		// Toolbar buttons based on permissions
		$canDo = ContentHelper::getActions('com_pwtacl');

		// Reset & clear buttons for group
		if ($this->group && $canDo->get('core.edit'))
		{
			// Clear permissions button for non-Super-User groups
			if (!Access::checkGroup($this->group, 'core.admin', 'root.1'))
			{
				ToolbarHelper::custom('assets.clear', 'delete.png', 'delete.png', 'COM_PWTACL_TOOLBAR_CLEAR', false);
			}

			// Reset button for default user groups
			if ($this->group <= 9)
			{
				ToolbarHelper::custom('assets.reset', 'refresh.png', 'refresh.png', 'COM_PWTACL_TOOLBAR_REVERT', false);
			}

			// Instantiate a new FileLayout instance and render the copy button
			$layout = new FileLayout('joomla.toolbar.modal');

			// Copy button
			$copyButton = $layout->render([
				'selector' => 'copyModal',
				'icon'     => 'copy',
				'text'     => Text::_('COM_PWTACL_TOOLBAR_COPY'),
			]);

			$bar->appendButton('Custom', $copyButton, 'copy');

			// Export button
			ToolbarHelper::custom('assets.export', 'download.png', 'download.png', 'COM_PWTACL_TOOLBAR_EXPORT', false);

			// Import button
			$importButton = $layout->render([
				'selector' => 'importModal',
				'icon'     => 'upload',
				'text'     => Text::_('COM_PWTACL_TOOLBAR_IMPORT'),
			]);

			$bar->appendButton('Custom', $importButton, 'copy');
		}

		// Options button
		if ($canDo->get('core.admin') || $canDo->get('core.options'))
		{
			ToolbarHelper::preferences('com_pwtacl');
		}
	}
}
