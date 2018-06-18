<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/controller.php');

class EasyDiscussControllerInstallSync extends EasyDiscussSetupController
{
	/**
	 * Synchronizes database tables
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function execute()
	{
		$this->engine();

		// For development mode, we want to skip all this
		if ($this->isDevelopment()) {
			return $this->output($this->getResultObj('COM_EASYDISCUSS_INSTALLATION_DEVELOPER_MODE', true));
		}

		$version = $this->getInstalledVersion();
		$previous = $this->getPreviousVersion('dbversion');

		$affected = '';

		// Run the db scripts sync if needed.
		if ($previous !== false) {
			$db = ED::db();
			$affected = $db->sync($previous);
		}

		// Update the version in the database to the latest now
		$config = ED::table('Configs');
		$config->load(array('name' => 'dbversion'));
		$config->name = 'dbversion';
		$config->params = $version;

		// Save the configuration
		$config->store($config->name);

		// If the previous version is empty, we can skip this altogether as we know this is a fresh installation
		if (!empty($affected)) {
			$this->setInfo(JText::sprintf('COM_EASYDISCUSS_INSTALLATION_MAINTENANCE_DB_SYNCED', $version));
			return $this->output();
		}
		
		$this->setInfo(JText::sprintf('COM_EASYDISCUSS_INSTALLATION_MAINTENANCE_DB_NOTHING_TO_SYNC', $version));
		return $this->output();
	}
}
