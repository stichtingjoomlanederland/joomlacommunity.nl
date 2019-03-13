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

class EasyDiscussControllerInstallCopy extends EasyDiscussSetupController
{
	/**
	 * Responsible to copy the necessary files over.
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function execute()
	{
		// Get which type of data we should be copying
		$type = $this->input->get('type', '');

		// Get the temporary path from the server.
		$tmpPath = $this->input->get('path', '', 'default');

		// Get the path to the zip file
		$archivePath = $tmpPath . '/' . $type . '.zip';

		// Where the extracted items should reside
		$path = $tmpPath . '/' . $type;

		// For development mode, we want to skip all this
		if ($this->isDevelopment()) {
			return $this->output($this->getResultObj('COM_EASYDISCUSS_INSTALLATION_DEVELOPER_MODE', true));
		}

		// Extract the admin folder
		$state = $this->extractArchive($archivePath, $path);

		if (!$state) {
			$this->setInfo(JText::sprintf('COM_EASYDISCUSS_INSTALLATION_COPY_ERROR_UNABLE_EXTRACT', $type), false);
			return $this->output();
		}

		// Look for files in this path
		$files = JFolder::files($path , '.' , false , true );

		// Look for folders in this path
		$folders = JFolder::folders($path , '.' , false , true );

		// Construct the target path first.
		if ($type == 'admin' || $type == 'site') {

			// Cleanup admin folders when installing
			if ($type == 'admin') {
				$this->cleanupAdmin();
			}

			$target = $type == 'site' ? JPATH_ROOT : JPATH_ADMINISTRATOR;
			$target .= '/components/com_easydiscuss';
		}

		// There could be instances where the user did not upload the launcher and just used the update feature.
		if ($type == 'languages') {

			// Copy the admin language file
			JFile::copy($path . '/admin/en-GB.com_easydiscuss.ini', JPATH_ADMINISTRATOR . '/language/en-GB/en-GB.com_easydiscuss.ini');

			// Copy the admin system language file
			JFile::copy($path . '/admin/en-GB.com_easydiscuss.sys.ini', JPATH_ADMINISTRATOR . '/language/en-GB/en-GB.com_easydiscuss.sys.ini');

			// Copy the site language file
			JFile::copy($path . '/site/en-GB.com_easydiscuss.ini', JPATH_ROOT . '/language/en-GB/en-GB.com_easydiscuss.ini');

			$this->setInfo('COM_EASYDISCUSS_INSTALLATION_LANGUAGES_UPDATED', true);
			return $this->output();
		}

		if ($type == 'media') {
			$target = JPATH_ROOT . '/media/com_easydiscuss';
		}

		// Ensure that the target folder exists
		if (!JFolder::exists($target)) {
			JFolder::create($target);
		}

		// Scan for files in the folder
		$totalFiles = 0;
		$totalFolders = 0;

		foreach ($files as $file) {
			$name = basename($file);

			$targetFile = $target . '/' . $name;

			// We need to skip cron.php if the file already exists
			if ($type == 'site' && $name == 'cron.php') {
				$exists = JFile::exists($targetFile);
				if ($exists) {
					continue;
				}
			}

			// Copy the file
			JFile::copy($file, $targetFile);

			$totalFiles++;
		}


		// Scan for folders in this folder
		foreach ($folders as $folder) {
			$name = basename($folder);
			$targetFolder = $target . '/' . $name;

			// Copy the folder across
			JFolder::copy($folder, $targetFolder, '', true);

			$totalFolders++;
		}

		$result = $this->getResultObj(JText::sprintf('COM_EASYDISCUSS_INSTALLATION_COPY_FILES_SUCCESS', $totalFiles, $totalFolders), true);

		return $this->output($result);
	}

	/**
	 * Cleanup admin folder for older residue
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function cleanupAdmin()
	{
		jimport('joomla.filesystem.folder');
		
		$folders = array(
						'/components/com_easydiscuss/controllers',
						'/components/com_easydiscuss/elements',
						'/components/com_easydiscuss/includes',
						'/components/com_easydiscuss/models',
						'/components/com_easydiscuss/tables',
						'/components/com_easydiscuss/themes',
						'/components/com_easydiscuss/views'
					);

		foreach ($folders as $folder) {
			$path = JPATH_ADMINISTRATOR . $folder;
			$exists = JFolder::exists($path);
			
			if ($exists) {
				JFolder::delete($path);
			}
		}

		return;
	}
}