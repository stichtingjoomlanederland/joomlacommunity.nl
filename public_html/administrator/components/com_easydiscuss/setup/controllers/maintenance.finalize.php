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

class EasyDiscussControllerMaintenanceFinalize extends EasyDiscussSetupController
{
	/**
	 * Perform the finalization of scripts
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function execute()
	{
		$this->engine();

		$version = $this->getInstalledVersion();

		if ($this->isDevelopment()) {
			$this->removeInstallationFile();

			return $this->output($this->getResultObj('COM_EASYDISCUSS_INSTALLATION_DEVELOPER_MODE', true));
		}

		// Update the version in the database to the latest now
		$config = ED::table('Configs');
		$config->load(array('name' => 'scriptversion'));

		$config->name = 'scriptversion';
		$config->params = $version;

		// Save the new config
		$config->store($config->name);

		// Remove any folders in the temporary folder.
		$this->cleanup(ED_TMP);

		$result = $this->getResultObj(JText::sprintf('COM_EASYDISCUSS_INSTALLATION_MAINTENANCE_UPDATED_MAINTENANCE_VERSION', $version), 1, 'success');

		return $this->output($result);
	}

	/**
	 * Deletes the installation file
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	private function removeInstallationFile()
	{
		// Remove installation temporary file
		return JFile::delete(JPATH_ROOT . '/tmp/easydiscuss.installation');
	}

	/**
	 * Since 4.x, easydiscuss no longer requires on foundry 3.1
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function removeFoundry()
	{
		$folder = JPATH_ROOT . '/media/foundry/3.1';

		if (!JFolder::exists($folder)) {
			return;
		}

		// we need to check if the site has komento installed or not. if yes, we CANNOT remove the foundry
		// as komento still using foundry 3.1.
		$komento = JPATH_ROOT . '/administrator/components/com_komento/komento.php';

		// We also need to check if user are using easyblog 3.9 and below, do not remove foundry 3.1.
		$easyblog = JPATH_ROOT .'/components/com_easyblog/helpers/helper.php';

		if (!JFile::exists($komento) && !JFile::exists($easyblog)) {
			return JFolder::delete($folder);
		}

		return true;
	}

	/**
	 * Perform system wide cleanups after the installation is completed.
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function cleanup($path)
	{
		$folders = JFolder::folders($path, '.', false, true);
		$files = JFolder::files($path, '.', false, true);

		if ($folders) {
			foreach ($folders as $folder) {
				JFolder::delete($folder);
			}
		}

		if ($files) {
			foreach ($files as $file) {
				JFile::delete($file);
			}
		}

		// Cleanup javascript files
		$this->removeOldJavascripts();

		// Remove helpers folder and constants.php if this is an upgrade from 3.x to 5.x
		$this->removeLegacyFolders();
		$this->removeConstantsFile();

		// Remove foundry as we no longer require it
		$this->removeFoundry();

		// Remove installation files
		$this->removeInstallationFile();
	}

	/**
	 * Remove all old javascript files
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function removeOldJavascripts()
	{
		// Get the current installed version
		$version = $this->getInstalledVersion();

		$path = JPATH_ROOT . '/media/com_easydiscuss/scripts';
		$sections = array('admin', 'site');

		// Ignored files
		$ignored = array('.svn', 'CVS', '.DS_Store', '__MACOSX');

		foreach ($sections as $section) {
			$ignored[] = 'easydiscuss-basic-' . $version . '.js';
			$ignored[] = 'easydiscuss-' . $version . '.js';
			$ignored[] = 'core.js';

			$files = JFolder::files($path . '/' . $section, '.', false, true, $ignored);

			if ($files) {
				foreach ($files as $file) {
					JFile::delete($file);
				}
			}
		}
	}

	/**
	 * Since 4.x, constants are moved to the back end.
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function removeConstantsFile()
	{
		// old constants.php location.
		$file = JPATH_ROOT . '/components/com_easydiscuss/constants.php';

		if (JFile::exists($file)) {
			JFile::delete($file);
		}
	}

	/**
	 * Since 4.x, we no longer use legacy helper and classes folders
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function removeLegacyFolders()
	{
		$folders = array(
						JPATH_ROOT . '/components/com_easydiscuss/models',
						JPATH_ROOT . '/components/com_easydiscuss/classes',
						JPATH_ROOT . '/components/com_easydiscuss/helpers'
					);

		// Go through each folders and remove them
		foreach ($folders as $folder) {
			$exists = JFolder::exists($folder);

			if ($exists) {
				JFolder::delete($folder);
			}
		}

		return true;
	}
}