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

class EasyDiscussControllerInstallExtract extends EasyDiscussSetupController
{
	/**
	 * For users who uploaded the installer and needs a manual extraction
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function execute()
	{
		// Check the api key from the request
		$key = $this->input->get('apikey', '');

		if ($this->isDevelopment()) {
			return $this->output($this->getResultObj('COM_EASYDISCUSS_INSTALLATION_DEVELOPER_MODE', true));
		}

		// Construct storage path
		$storage = ED_PACKAGES . '/' . ED_PACKAGE;
		$exists = JFile::exists($storage);

		// Test if package really exists
		if (!$exists) {
			$this->setInfo('COM_EASYDISCUSS_INSTALLATION_ERROR_PACKAGE_DOESNT_EXIST', false);
			return $this->output();
		}

		// Check if the temporary folder exists
		if (!JFolder::exists(ED_TMP)) {
			JFolder::create(ED_TMP);
		}

		// Extract files to a temporary location
		$tmp = ED_TMP . '/com_easydiscuss_' . uniqid();

		// Delete any folders that already exists
		if (JFolder::exists($tmp)) {
			JFolder::delete($tmp);
		}

		// Try to extract the files
		$state = $this->extractArchive($storage, $tmp);

		// Regardless of the extraction state, delete the zip file.
		@JFile::delete($storage);

		if (!$state) {
			$this->setInfo('COM_EASYDISCUSS_INSTALLATION_ERROR_EXTRACT_ERRORS', false);
			return $this->output();
		}

		$this->setInfo('COM_EASYDISCUSS_INSTALLATION_EXTRACT_SUCCESS', true, array('path' => $tmp));
		return $this->output();
	}
}
