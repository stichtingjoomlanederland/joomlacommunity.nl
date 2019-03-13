<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussViewParent extends JViewLegacy
{
	public function __construct($config = array())
	{
		return parent::__construct($config);
	}
}

class EasyDiscussAdminView extends EasyDiscussViewParent
{
	public $panelTitle = '';
	public $panelDescription = '';

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

		if ($this->doc->getType() != 'html') {
			$this->ajax = ED::ajax();
		}
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
			ED::setMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			return $this->app->redirect('index.php?option=com_easydiscuss');
		}
	}

	public function display($tpl = null)
	{
		$format = $this->input->get('format', 'html', 'word');
		$view = $this->getName();
		$layout = $this->getLayout();

		$tpl = 'admin/' . $tpl;

		if ($this->doc->getType() == 'html') {

			// Initialize whatever that is necessary
			JHTML::_('behavior.framework', true);

			ED::init('admin');

			// get the bbcode settings
			$bbcodeSettings = $this->theme->output('admin/structure/settings');

			// Get the contents of the view.
			$contents = $this->theme->output($tpl);

			// attached bbcode settings
			$contents = $bbcodeSettings . $contents;

			// We need to output the structure
			$theme = ED::themes();

			// Set the ajax url
			$ajaxUrl = JURI::root() . 'administrator/index.php';

			$browse = $this->input->get('browse', '', 'default');

			// Get the sidebar
			$sidebar = $this->getSidebar();

			$message = ED::getMessageQueue();
			$version = ED::getLocalVersion();
			
			$theme->set('version', $version);
			$theme->set('title', $this->panelTitle);
			$theme->set('desc', $this->panelDescription);
			$theme->set('message', $message);
			$theme->set('sidebar', $sidebar);
			$theme->set('browse', $browse);
			$theme->set('contents', $contents);
			$theme->set('layout', $layout);
			$theme->set('view', $view);
			$theme->set('ajaxUrl', $ajaxUrl);

			$output = $theme->output('admin/structure/default');

			// Get the scripts
			$scripts = ED::scripts()->getScripts();

			echo $output;
			echo $scripts;

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
	 * @since	1.2
	 * @access	public
	 */
	public function getSidebar()
	{
		$file = JPATH_COMPONENT . '/defaults/menus.json';
		$contents = JFile::read($file);

		$view = $this->input->get('view', '', 'cmd');
		$layout = $this->input->get('layout', '', 'cmd');
		$result = json_decode($contents);
		$menus = array();

		foreach ($result as &$row) {

			// Check if the user is allowed to view this sidebar
			if (isset($row->access) && $row->access) {
				if (!$this->my->authorise($row->access, 'com_easydiscuss')) {
					continue;
				}
			}

			if (!isset($row->view)) {
				$row->link = 'index.php?option=com_easydiscuss';
				$row->view = '';
			}

			if (isset($row->counter)) {
				$row->counter = $this->getCounter($row->counter);
			}

			if (!isset($row->link)) {
				$row->link = 'index.php?option=com_easydiscuss&view=' . $row->view;
			}

			// Translate the sidebar title
			$row->title = JText::_($row->title);

			// Default properties of each menu
			$row->class = $view == $row->view ? ' active ' : '';

			if (isset($row->childs) && $row->childs) {

				foreach ($row->childs as &$child) {

					// Update the child's link
					$child->link = 'index.php?option=com_easydiscuss&view=' . $row->view;

					if ($child->url) {

						foreach ($child->url as $key => $value) {
							if (!empty($value)) {
								$child->link .= '&' . $key . '=' . $value;
							}

							// Determines if the child is active
							$child->class = '';

							if ($key == 'layout' && $layout == $value) {
								$child->class = 'active';
							}
						}

					}

					$child->title = JText::_($child->title);


					// If it has a "counter" property, we need to execute it here
					if (isset($child->counter)) {
						list($modelName, $methodName) = explode('/', $child->counter);
						
						$model = ED::model($modelName);
						$child->counter = $model->$methodName();
					}
				}
			} else {
				$row->childs = array();
			}

			$menus[] = $row;
		}

		$theme = ED::themes();
		$theme->set('layout', $layout);
		$theme->set('view', $view);
		$theme->set('menus', $menus);

		$output = $theme->output('admin/structure/sidebar');

		return $output;
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
