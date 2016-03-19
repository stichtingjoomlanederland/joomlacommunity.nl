<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

/**
 * The updates provisioning Controller
 */
class AkeebaControllerUpdate extends F0FController
{
	/**
	 * Executes a given controller task. The onBefore<task> and onAfter<task>
	 * methods are called automatically if they exist.
	 *
	 * @param   string $task The task to execute, e.g. "browse"
	 *
	 * @throws  Exception   Exception thrown if the onBefore<task> returns false
	 *
	 * @return  null|bool  False on execution failure
	 */
	public function execute($task)
	{
		$validTasks = array('force', 'overview', 'startupdate', 'download', 'extract', 'install', 'cleanup');

		if (!in_array($task, $validTasks))
		{
			$task = 'overview';
		}

		return parent::execute($task);
	}

	public function force()
	{
		$msg = null;

		/** @var AkeebaModelUpdates $model */
		$model = $this->getThisModel();
		$model->getUpdates(true);
		$msg = JText::_('AKEEBA_COMMON_UPDATE_INFORMATION_RELOADED');

		$url = 'index.php?option=' . $this->input->getCmd('option', '');

		if ($this->input->getInt('update', 0))
		{
			$url .= '&view=update';
		}

		$this->setRedirect($url, $msg);
	}

	public function overview()
	{
		$cpanelModel = F0FModel::getTmpInstance('Cpanels', 'AkeebaModel');
		$this->getThisView()->setModel($cpanelModel, false, 'cpanel');

		$this->display(false);
	}

	public function startupdate()
	{
		// Anti-CSRF token check
		if ($this->csrfProtection)
		{
			$this->_csrfProtection();
		}

		// If we still need the FTP information to perform the update go back to the previous page
		/** @var AkeebaModelUpdates $model */
		$model = $this->getThisModel();
		$needsFTP = $model->needsFTPCredentials();

		if ($needsFTP)
		{
			$msg = JText::_('COM_AKEEBA_UPDATE_ERR_FTPINFOMISSING');

			$url = 'index.php?option=' . $this->input->getCmd('option', '') . '&view=update';

			$this->setRedirect($url, $msg, 'error');

			return true;
		}

		// Show the page where the user is informed the download is about to begin
		$this->display(true);
	}

	public function download()
	{
		// Anti-CSRF token check
		if ($this->csrfProtection)
		{
			$this->_csrfProtection();
		}

		/** @var AkeebaModelCpanels $cpanelModel */
		$cpanelModel = $this->getModel('cpanels');

		/** @var AkeebaModelUpdates $model */
		$model = $this->getThisModel();

		// Registers FTP credentials from the session
		$needsFTP = $model->needsFTPCredentials();

		try
		{
			// Do I really have an update? We force-reload the information to be extra certain we really have the LATEST
			// update information
			$updateInfo = $model->getUpdates(true);

			if (!$updateInfo['hasUpdate'])
			{
				throw new RuntimeException(JText::_('UPDATE_ERROR_NOUPDATES'));
			}

			// Check that the Download ID is correct
			if ($cpanelModel->needsDownloadID())
			{
				throw new RuntimeException(JText::_('UPDATE_ERROR_USERNAMEPASSREQUIRED2'));
			}

			// Download the update package
			$downloadedPackage = $model->downloadUpdate();

			// Store the path to the update package in the session
			$session = JFactory::getSession();
			$session->set('downloadedPackage', $downloadedPackage, 'com_akeeba.update');
		}
		catch (Exception $e)
		{
			$url = 'index.php?option=' . $this->input->getCmd('option', '') . '&view=update';

			$this->setRedirect($url, $e->getMessage(), 'error');

			return true;
		}

		// Show the page where the user is informed the extraction is about to begin
		$this->display(false);
	}

	public function extract()
	{
		// Anti-CSRF token check
		if ($this->csrfProtection)
		{
			$this->_csrfProtection();
		}

		// Required for Joomla! 1.x/2.x/3.0/3.1
		jimport('joomla.installer.helper');
		jimport('cms.installer.helper');

		// Get the downloaded package path
		$session = JFactory::getSession();
		$updatePackagePath = $session->get('downloadedPackage', null, 'com_akeeba.update');

		/** @var AkeebaModelUpdates $model */
		$model = $this->getThisModel();

		// Registers FTP credentials from the session
		$needsFTP = $model->needsFTPCredentials();

		try
		{
			if (empty($updatePackagePath) || !is_file($updatePackagePath) || !is_readable($updatePackagePath))
			{
				throw new RuntimeException(JText::_('UPDATE_ERROR_CANTDOWNLOAD2'));
			}

			// Unpack the downloaded package file
			$package = JInstallerHelper::unpack($updatePackagePath);

			if (!$package)
			{
				throw new RuntimeException(JText::_('UPDATE_ERROR_CANTEXTRACT'));
			}

			// Save the extracted path in the session
			$session->set('installationDirectory', $package['extractdir'], 'com_akeeba.update');
		}
		catch (Exception $e)
		{
			// Remove the downloaded update file
			jimport('jooomla.filesystem.file');
			jimport('jooomla.filesystem.folder');

			if (is_file($updatePackagePath))
			{
				if (!@unlink($updatePackagePath))
				{
					JFile::delete($updatePackagePath);
				}
			}

			// Clear the stale session information with Joomla! 1.x way
			$session->set('downloadedPackage', null, 'com_akeeba.update');
			$session->set('installationDirectory', null, 'com_akeeba.update');
			// ... and with Joomla! 2.x+ way
			if (method_exists($session, 'clear'))
			{
				$session->clear('downloadedPackage', 'com_akeeba.update');
				$session->clear('installationDirectory', 'com_akeeba.update');
			}

			// Redirect to the error page
			$url = 'index.php?option=' . $this->input->getCmd('option', '') . '&view=update';

			$this->setRedirect($url, $e->getMessage(), 'error');

			return true;
		}

		// Show the page where the user is informed the update installation is about to begin
		$this->display(false);
	}

	public function install()
	{
		// Anti-CSRF token check
		if ($this->csrfProtection)
		{
			$this->_csrfProtection();
		}

		/** @var AkeebaModelUpdates $model */
		$model = $this->getThisModel();

		// Get the downloaded package path
		$session = JFactory::getSession();
		$updatePackagePath = $session->get('downloadedPackage', null, 'com_akeeba.update');
		$installationDirectory = $session->get('installationDirectory', null, 'com_akeeba.update');

		// Registers FTP credentials from the session
		$needsFTP = $model->needsFTPCredentials();

		try
		{
			if (!is_dir($installationDirectory))
			{
				throw new RuntimeException(JText::_('UPDATE_ERROR_CANTEXTRACT'));
			}

			jimport('joomla.installer.installer');
			jimport('joomla.installer.helper');

			$installer = JInstaller::getInstance();
			$packageType = JInstallerHelper::detectType($installationDirectory);

			if (!$packageType)
			{
				throw new RuntimeException(JText::_('COM_AKEEBA_UPDATE_ERR_WRONGPACKAGETYPE'));
			}

			if (!$installer->install($installationDirectory))
			{
				$model->setState('name', $installer->get('name'));
				$model->setState('message', $installer->message);
				$model->setState('extmessage', $installer->get('extension_message'));

				throw new RuntimeException(JText::_('COM_AKEEBA_UPDATE_ERR_CANTINSTALLUPDATE'));
			}

			$model->setState('name', $installer->get('name'));
			$model->setState('message', $installer->message);
			$model->setState('extmessage', $installer->get('extension_message'));
		}
		catch (Exception $e)
		{
			// Remove the downloaded update file
			jimport('jooomla.filesystem.file');
			jimport('jooomla.filesystem.folder');

			if (is_file($updatePackagePath))
			{
				if (!@unlink($updatePackagePath))
				{
					JFile::delete($updatePackagePath);
				}
			}

			if (is_dir($installationDirectory))
			{
				JFolder::delete($installationDirectory);
			}

			// Clear the stale session information with Joomla! 1.x way
			$session->set('downloadedPackage', null, 'com_akeeba.update');
			$session->set('installationDirectory', null, 'com_akeeba.update');
			// ... and with Joomla! 2.x+ way
			if (method_exists($session, 'clear'))
			{
				$session->clear('downloadedPackage', 'com_akeeba.update');
				$session->clear('installationDirectory', 'com_akeeba.update');
			}

			// Redirect to the error page
			$url = 'index.php?option=' . $this->input->getCmd('option', '') . '&view=update';

			$this->setRedirect($url, $e->getMessage(), 'error');

			return true;
		}

		// Show the page where the user is informed the update installation is complete
		$this->display(false);
	}

	public function cleanup()
	{
		// Anti-CSRF token check
		if ($this->csrfProtection)
		{
			$this->_csrfProtection();
		}

		/** @var AkeebaModelUpdates $model */
		$model = $this->getThisModel();

		// Get information from the session
		$session = JFactory::getSession();
		$updatePackagePath = $session->get('downloadedPackage', null, 'com_akeeba.update');
		$installationDirectory = $session->get('installationDirectory', null, 'com_akeeba.update');

		jimport('jooomla.filesystem.file');
		jimport('jooomla.filesystem.folder');
		jimport('joomla.installer.helper');

		// Registers FTP credentials from the session
		$needsFTP = $model->needsFTPCredentials();

		// First try using Joomla!'s cleanupInstall
		if (class_exists('JInstallerHelper') && method_exists('JInstallerHelper', 'cleanupInstall'))
		{
			JInstallerHelper::cleanupInstall($updatePackagePath, $installationDirectory);
		}

		// Make sure we have cleaned up the downloaded package
		if (is_file($updatePackagePath))
		{
			if (!@unlink($updatePackagePath))
			{
				JFile::delete($updatePackagePath);
			}
		}

		// Make sure we have cleaned up the extracted package's folder
		if (is_dir($installationDirectory))
		{
			JFolder::delete($installationDirectory);
		}

		// Clear Joomla! caches
		if (class_exists('JCache') && method_exists('JCache', 'clean'))
		{
			F0FUtilsCacheCleaner::clearCacheGroups(array(
					'mod_menu'
			));
			F0FUtilsCacheCleaner::clearPluginsCache();
			F0FUtilsCacheCleaner::clearModulesCache();
		}
		else
		{
			$cacheGroups = array('mod_menu', 'com_plugins', 'com_modules');

			foreach ($cacheGroups as $group)
			{
				$cache = JFactory::getCache($group);
				$cache->clean();
			}
		}

		// Force refresh the update information
		$model->getUpdates(true);

		// Reset Joomla!'s update cache as well
		$model->refreshUpdateSite();
	}
}