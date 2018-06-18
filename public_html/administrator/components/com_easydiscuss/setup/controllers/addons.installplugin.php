<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/controller.php');

class EasyDiscussControllerAddonsInstallPlugin extends EasyDiscussSetupController
{
	public function execute()
	{
		$this->engine();

		// Get a list of folders in the module and plugins.
		$path = $this->input->get('path', '', 'default');

		// Get the plugin group and element
		$element = $this->input->get('element', '', 'cmd');
		$group = $this->input->get('group', '', 'cmd');

		// Construct the absolute path
		$absolutePath = $path . '/' . $group . '/' . $element;

		// Try to install the plugin now
		$state = $this->installPlugin($element, $group, $absolutePath);

		$this->setInfo(JText::sprintf('Plugin %1$s.%2$s installed on the site', $element, $group), true);
		return $this->output();
	}

	/**
	 * Installation of plugins on the site
	 *
	 * @since   4.2.0
	 * @access  public
	 */
	public function installPlugin($element, $group, $absolutePath)
	{
		if ($this->isDevelopment()) {
			$this->setInfo('ok', true);
			return $this->output();
		}

		// Get Joomla's installer instance
		$installer = JInstaller::getInstance();

		// Allow overwriting of existing plugins
		$installer->setOverwrite(true);

		// Install the plugin now
		$state = $installer->install($absolutePath);

		// Ensure that the plugins are published
		if ($state) {

			$group = strtolower($group);
			$element = strtolower($element);

			$options = array('folder' => $group, 'element' => $element);

			$plugin = JTable::getInstance('Extension');
			$plugin->load($options);

			// set the state to 0 means 'installed'.
			$plugin->state = 0;

			// We put the content plugin to unpublish first.
			$plugin->enabled = ($group == 'content' && $element == 'easydiscuss') ? false : true;
			
			$plugin->store();
		}

		return $state;
	}
}
