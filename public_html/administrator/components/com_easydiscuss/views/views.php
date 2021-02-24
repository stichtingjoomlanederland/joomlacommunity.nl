<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussAdminView extends JViewLegacy
{
	public $panelTitle = '';
	public $panelDescription = '';
	public $help = '';

	public function __construct()
	{
		parent::__construct();

		$this->doc = JFactory::getDocument();
		$this->app = JFactory::getApplication();
		$this->my = JFactory::getUser();
		$this->config = ED::config();
		$this->jconfig = ED::jconfig();
		$this->input = ED::request();
		$this->theme = ED::themes();
		$this->showSidebar = true;

		if ($this->doc->getType() != 'html') {
			$this->ajax = ED::ajax();
		}
	}


	/**
	 * Adds a new help button
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function addHelpButton($url)
	{
		$url = 'https://stackideas.com' . $url;

		$this->help = $url;
	}

	/**
	 * Alias to $app->getUserStateFromRequest
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getUserState($key, $name, $default = '', $type = 'string')
	{
		return $this->app->getUserStateFromRequest('com_easydiscuss.' . $key, $name, $default, $type);
	}

	/**
	 * Allows child to set variables
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function set($key, $value = '')
	{
		if ($this->doc->getType() == 'json') {
			$this->props[$key] = $value;

			return;
		}

		$this->theme->set($key, $value);
	}

	/**
	 * Checks if the current viewer can really access this section
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function checkAccess($rule)
	{
		if (!$this->my->authorise($rule , 'com_easydiscuss')) {
			ED::setMessage(JText::_('JERROR_ALERTNOAUTHOR'), ED_MSG_ERROR);
			return ED::redirect('index.php?option=com_easydiscuss');
		}
	}

	public function display($tpl = null)
	{
		$format = $this->input->get('format', 'html', 'word');
		$view = $this->getName();
		$layout = $this->getLayout();

		$tpl = 'admin/' . $tpl;

		if ($this->doc->getType() == 'html') {
			EDCompat::renderJQueryFramework();
			ED::init('admin');

			// Get the contents of the view.
			$contents = $this->theme->output($tpl);

			// attached bbcode settings
			$contents = $contents;

			// We need to output the structure
			$theme = ED::themes();

			// Set the ajax url
			$ajaxUrl = JURI::root() . 'administrator/index.php';

			$browse = $this->input->get('browse', '', 'default');

			// Get the sidebar
			$sidebar = $this->getSidebar();

			$message = ED::getMessageQueue();

			$postOutOfSync = false;

			if (!$browse && $view != 'maintenance') {
				$model = ED::model('Maintenance');
				$postOutOfSync = $model->getTotalSyncRequest();
			}
			
            $theme->set('help', $this->help);
			$theme->set('title', $this->panelTitle);
			$theme->set('desc', $this->panelDescription);
			$theme->set('message', $message);
			$theme->set('sidebar', $sidebar);
			$theme->set('browse', $browse);
			$theme->set('contents', $contents);
			$theme->set('layout', $layout);
			$theme->set('view', $view);
			$theme->set('ajaxUrl', $ajaxUrl);
			$theme->set('postOutOfSync', $postOutOfSync);

			$output = $theme->output('admin/structure/default');

			// Get the scripts
			$scripts = ED::scripts()->getScripts();
			$this->doc->addCustomTag($scripts);

			echo $output;

			// If the toolbar registration exists, load it up
			if (method_exists($this, 'registerToolbar')) {
				$this->registerToolbar();
			}

			return;
		}
	}

	/**
	 * Prepares the sidebar
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getSidebar()
	{
		if (!$this->showSidebar) {
			return;
		}

		$showSidebar = $this->input->get('sidebar', 1, 'int');
		$showSidebar = $showSidebar == 1 ? true : false;

		if (!$showSidebar) {
			return;
		}

		$sidebar = ED::sidebar();

		$view = $this->input->get('view', 'easydiscuss', 'cmd');
		$output = $sidebar->render($view);
		return $output;
	}

	/**
	 * Allows childs to hide the sidebar
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function hideSidebar()
	{
		$this->showSidebar = false;
	}

	/**
	 * Allows caller to set the header title in the structure layout.
	 *
	 * @since	2.1.0
	 * @access	public
	 */
	public function setHeading($title, $description = '')
	{
		// Set the title in Joomla as well
		JToolBarHelper::title(JText::_('COM_EASYDISCUSS_DASHBOARD'));

		$this->panelTitle = JText::_($title);

		if ($description) {
			$this->panelDescription = JText::_($description);
			return;
		}


		$this->panelDescription = JText::_($title . '_DESC');
	}

	/**
	 * Allows caller to set the title
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function title($title)
	{
		$this->panelTitle = JText::_($title);

		// Set the title in Joomla as well
		JToolBarHelper::title(JText::_('COM_EASYDISCUSS_DASHBOARD'));

		// Always set the descripion unless caller explicitly want's to override this
		$this->panelDescription = JText::_($title . '_DESC');
	}

	/**
	 * Allows caller to set the title
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function desc($desc)
	{
		$this->panelDescription = JText::_($desc);
	}
}
