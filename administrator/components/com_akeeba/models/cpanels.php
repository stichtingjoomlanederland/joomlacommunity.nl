<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 * @since     1.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;

/**
 * The Control Panel model
 *
 */
class AkeebaModelCpanels extends F0FModel
{
	/**
	 * Returns a list of available backup profiles, to be consumed by JHTML in order to build
	 * a drop-down
	 *
	 * @param   bool  $includeId  Should I include the profile ID in front of the name?
	 *
	 * @return  array
	 */
	public function getProfilesList($includeId = true)
	{
		$db = $this->getDbo();

		$query = $db->getQuery(true)
					->select(array(
						$db->qn('id'),
						$db->qn('description')
					))->from($db->qn('#__ak_profiles'))
					->order($db->qn('id') . " ASC");
		$db->setQuery($query);
		$rawList = $db->loadAssocList();

		$options = array();
		if ( !is_array($rawList))
		{
			return $options;
		}

		foreach ($rawList as $row)
		{
			$description = $row['description'];

			if ($includeId)
			{
				$description = '#' . $row['id'] . '. ' . $description;
			}

			$options[] = JHTML::_('select.option', $row['id'], $description);
		}

		return $options;
	}

	/**
	 * Gets a list of profiles which will be displayed as quick icons in the interface
	 *
	 * @return  stdClass[]  Array of objects; each has the properties `id` and `description`
	 */
	public function getQuickIconProfiles()
	{
		$db = $this->getDbo();

		$query = $db->getQuery(true)
					->select(array(
						$db->qn('id'),
						$db->qn('description')
					))->from($db->qn('#__ak_profiles'))
					->where($db->qn('quickicon') . ' = ' . $db->q(1))
					->order($db->qn('id') . " ASC");
		$db->setQuery($query);

		$ret = $db->loadObjectList();

		if (empty($ret))
		{
			$ret = array();
		}

		return $ret;
	}

	/**
	 * Returns the active Profile ID
	 *
	 * @return int The active profile ID
	 */
	public function getProfileID()
	{
		$session = JFactory::getSession();

		return $session->get('profile', null, 'akeeba');
	}

	/**
	 * Creates an icon definition entry
	 *
	 * @param string $iconFile The filename of the icon on the GUI button
	 * @param string $label    The label below the GUI button
	 * @param string $view     The view to fire up when the button is clicked
	 *
	 * @return array The icon definition array
	 */
	public function _makeIconDefinition($iconFile, $label, $view = null, $task = null)
	{
		return array(
			'icon'  => $iconFile,
			'label' => $label,
			'view'  => $view,
			'task'  => $task
		);
	}

	/**
	 * Was the last backup a failed one? Used to apply magic settings as a means of
	 * troubleshooting.
	 *
	 * @return bool
	 */
	public function isLastBackupFailed()
	{
		// Get the last backup record ID
		$list = Platform::getInstance()->get_statistics_list(array('limitstart' => 0, 'limit' => 1));
		if (empty($list))
		{
			return false;
		}
		$id = $list[0];

		$record = Platform::getInstance()->get_statistics($id);

		return ($record['status'] == 'fail');
	}

	/**
	 * Checks that the media permissions are 0755 for directories and 0644 for files
	 * and fixes them if they are incorrect.
	 *
	 * @param $force    bool    Forcibly check subresources, even if the parent has correct permissions
	 *
	 * @return bool False if we couldn't figure out what's going on
	 */
	public function fixMediaPermissions($force = false)
	{
		// Are we on Windows?
		if (function_exists('php_uname'))
		{
			$isWindows = stristr(php_uname(), 'windows');
		}
		else
		{
			$isWindows = (DIRECTORY_SEPARATOR == '\\');
		}

		// No point changing permissions on Windows, as they have ACLs
		if ($isWindows)
		{
			return true;
		}

		// Check the parent permissions
		$parent      = JPATH_ROOT . '/media/com_akeeba';
		$parentPerms = fileperms($parent);

		// If we can't determine the parent's permissions, bail out
		if ($parentPerms === false)
		{
			return false;
		}

		// Fix the parent's permissions if required
		if (($parentPerms != 0755) && ($parentPerms != 040755))
		{
			$this->chmod($parent, 0755);
		}
		else
		{
			if ( !$force)
			{
				return true;
			}
		}

		// During development we use symlinks and we don't wanna see that big fat warning
		if (@is_link($parent))
		{
			return true;
		}

		JLoader::import('joomla.filesystem.folder');

		$result = true;

		// Loop through subdirectories
		$folders = JFolder::folders($parent, '.', 3, true);
		foreach ($folders as $folder)
		{
			$perms = fileperms($folder);
			if (($perms != 0755) && ($perms != 040755))
			{
				$result &= $this->chmod($folder, 0755);
			}
		}

		// Loop through files
		$files = JFolder::files($parent, '.', 3, true);
		foreach ($files as $file)
		{
			$perms = fileperms($file);
			if (($perms != 0644) && ($perms != 0100644))
			{
				$result &= $this->chmod($file, 0644);
			}
		}

		return $result;
	}

	/**
	 * Tries to change a folder/file's permissions using direct access or FTP
	 *
	 * @param string $path The full path to the folder/file to chmod
	 * @param int    $mode New permissions
	 */
	private function chmod($path, $mode)
	{
		if (is_string($mode))
		{
			$mode = octdec($mode);
			if (($mode < 0600) || ($mode > 0777))
			{
				$mode = 0755;
			}
		}

		// Initialize variables
		JLoader::import('joomla.client.helper');
		$ftpOptions = JClientHelper::getCredentials('ftp');

		// Check to make sure the path valid and clean
		$path = JPath::clean($path);

		if ($ftpOptions['enabled'] == 1)
		{
			// Connect the FTP client
			JLoader::import('joomla.client.ftp');
			$ftp = JClientFTP::getInstance(
				$ftpOptions['host'], $ftpOptions['port'], array(),
				$ftpOptions['user'], $ftpOptions['pass']
			);
		}

		if (@chmod($path, $mode))
		{
			$ret = true;
		}
		elseif ($ftpOptions['enabled'] == 1)
		{
			// Translate path and delete
			$path = JPath::clean(str_replace(JPATH_ROOT, $ftpOptions['root'], $path), '/');
			// FTP connector throws an error
			$ret = $ftp->chmod($path, $mode);
		}
		else
		{
			$ret = false;
		}

		return $ret;
	}

	/**
	 * Checks if we should enable settings encryption and applies the change
	 */
	public function checkSettingsEncryption()
	{
		// Do we have a key file?
		JLoader::import('joomla.filesystem.file');
		$filename = JPATH_COMPONENT_ADMINISTRATOR . '/engine/serverkey.php';
		if (JFile::exists($filename))
		{
			// We have a key file. Do we need to disable it?
			if (Platform::getInstance()->get_platform_configuration_option('useencryption', -1) == 0)
			{
				// User asked us to disable encryption. Let's do it.
				$this->disableSettingsEncryption();
			}
		}
		else
		{
			if ( !Factory::getSecureSettings()->supportsEncryption())
			{
				return;
			}
			if (Platform::getInstance()->get_platform_configuration_option('useencryption', -1) != 0)
			{
				// User asked us to enable encryption (or he left us with the default setting!). Let's do it.
				$this->enableSettingsEncryption();
			}
		}
	}

	private function disableSettingsEncryption()
	{
		// Load the server key file if necessary
		JLoader::import('joomla.filesystem.file');
		$filename = JPATH_COMPONENT_ADMINISTRATOR . '/engine/serverkey.php';
		$key      = Factory::getSecureSettings()->getKey();

		// Loop all profiles and decrypt their settings
		$profilesModel = F0FModel::getTmpInstance('Profiles', 'AkeebaModel');
		$profiles      = $profilesModel->getList(true);
		$db            = $this->getDBO();
		foreach ($profiles as $profile)
		{
			$id     = $profile->id;
			$config = Factory::getSecureSettings()->decryptSettings($profile->configuration, $key);
			$sql    = $db->getQuery(true)
						 ->update($db->qn('#__ak_profiles'))
						 ->set($db->qn('configuration') . ' = ' . $db->q($config))
						 ->where($db->qn('id') . ' = ' . $db->q($id));
			$db->setQuery($sql);
			$db->execute();
		}

		// Finally, remove the key file
		JFile::delete($filename);
	}

	private function enableSettingsEncryption()
	{
		$key = $this->createSettingsKey();
		if (empty($key) || ($key == false))
		{
			return;
		}

		// Loop all profiles and encrypt their settings
		$profilesModel = F0FModel::getTmpInstance('Profiles', 'AkeebaModel');
		$profiles      = $profilesModel->getList(true);
		$db            = $this->getDBO();
		if ( !empty($profiles))
		{
			foreach ($profiles as $profile)
			{
				$id     = $profile->id;
				$config = Factory::getSecureSettings()->encryptSettings($profile->configuration, $key);
				$sql    = $db->getQuery(true)
							 ->update($db->qn('#__ak_profiles'))
							 ->set($db->qn('configuration') . ' = ' . $db->q($config))
							 ->where($db->qn('id') . ' = ' . $db->q($id));
				$db->setQuery($sql);
				$db->execute();
			}
		}
	}

	private function createSettingsKey()
	{
		JLoader::import('joomla.filesystem.file');
		$seedA = md5(JFile::read(JPATH_ROOT . '/configuration.php'));
		$seedB = md5(microtime());
		$seed  = $seedA . $seedB;

		$md5 = md5($seed);
		for ($i = 0; $i < 1000; $i++)
		{
			$md5 = md5($md5 . md5(rand(0, 2147483647)));
		}

		$key = base64_encode($md5);

		$filecontents = "<?php defined('AKEEBAENGINE') or die(); define('AKEEBA_SERVERKEY', '$key'); ?>";
		$filename     = JPATH_COMPONENT_ADMINISTRATOR . '/engine/serverkey.php';

		$result = JFile::write($filename, $filecontents);

		if ( !$result)
		{
			return false;
		}
		else
		{
			return base64_decode($key);
		}
	}

	/**
	 * Update the cached live site's URL for the front-end backup feature (altbackup.php)
	 * and the detected Joomla! libraries path
	 */
	public function updateMagicParameters()
	{
		$component = JComponentHelper::getComponent('com_akeeba');

		if (is_object($component->params) && ($component->params instanceof JRegistry))
		{
			$params = $component->params;
		}
		else
		{
			$params = new JRegistry($component->params);
		}

		if (!$params->get('confwiz_upgrade', 0))
		{
			$this->markOldProfilesConfigured();
		}

		$params->set('confwiz_upgrade', 1);
		$params->set('siteurl', str_replace('/administrator', '', JUri::base()));

		if (defined('JPATH_LIBRARIES'))
		{
			$params->set('jlibrariesdir', Factory::getFilesystemTools()->TranslateWinPath(JPATH_LIBRARIES));
		}
		elseif (defined("JPATH_PLATFORM"))
		{
			$params->set('jlibrariesdir', Factory::getFilesystemTools()->TranslateWinPath(JPATH_PLATFORM));
		}

		$params->set('jversion', '1.6');
		$db   = F0FPlatform::getInstance()->getDbo();
		$data = $params->toString();
		$sql  = $db->getQuery(true)
				   ->update($db->qn('#__extensions'))
				   ->set($db->qn('params') . ' = ' . $db->q($data))
				   ->where($db->qn('element') . ' = ' . $db->q('com_akeeba'))
				   ->where($db->qn('type') . ' = ' . $db->q('component'));
		$db->setQuery($sql);
		$db->execute();
	}

	public function mustWarnAboutDownloadIDInCore()
	{
		$ret   = false;
		$isPro = AKEEBA_PRO;

		if ($isPro)
		{
			return $ret;
		}

		JLoader::import('joomla.application.component.helper');
		$dlid = \Akeeba\Engine\Util\Comconfig::getValue('update_dlid', '');

		if (preg_match('/^([0-9]{1,}:)?[0-9a-f]{32}$/i', $dlid))
		{
			$ret = true;
		}

		return $ret;
	}

	/**
	 * Does the user need to enter a Download ID in the component's Options page?
	 *
	 * @return bool
	 */
	public function needsDownloadID()
	{
		// Do I need a Download ID?
		$ret   = true;
		$isPro = AKEEBA_PRO;

		if ( !$isPro)
		{
			$ret = false;
		}
		else
		{
			JLoader::import('joomla.application.component.helper');
			$dlid = \Akeeba\Engine\Util\Comconfig::getValue('update_dlid', '');

			if (preg_match('/^([0-9]{1,}:)?[0-9a-f]{32}$/i', $dlid))
			{
				$ret = false;
			}
		}

		return $ret;
	}

	/**
	 * Checks the database for missing / outdated tables using the $dbChecks
	 * data and runs the appropriate SQL scripts if necessary.
	 *
	 * @return AkeebaModelCpanels
	 */
	public function checkAndFixDatabase()
	{
		// Install or update database
		$dbInstaller = new F0FDatabaseInstaller(array(
			'dbinstaller_directory' => JPATH_ADMINISTRATOR . '/components/com_akeeba/sql/xml'
		));
		$dbInstaller->updateSchema();

		return $this;
	}

	/**
	 * Akeeba Backup 4.3.2 displays a popup if your profile is not already configured by Configuration Wizard, the
	 * Configuration page or imported from the Profiles page. This bit of code makes sure that existing profiles will
	 * be marked as already configured just the FIRST time you upgrade to the new version from an old version.
	 */
	public function markOldProfilesConfigured()
	{
		// Get all profiles
		$db = F0FPlatform::getInstance()->getDbo();

		$query = $db->getQuery(true)
					->select(array(
						$db->qn('id'),
					))->from($db->qn('#__ak_profiles'))
					->order($db->qn('id') . " ASC");
		$db->setQuery($query);
		$profiles = $db->loadColumn();

		// Save the current profile number
		$session = JFactory::getSession();
		$oldProfile = $session->get('profile', 1, 'akeeba');

		// Update all profiles
		foreach ($profiles as $profile_id)
		{
			\Akeeba\Engine\Factory::nuke();
			\Akeeba\Engine\Platform::getInstance()->load_configuration($profile_id);
			$config = \Akeeba\Engine\Factory::getConfiguration();
			$config->set('akeeba.flag.confwiz', 1);
			\Akeeba\Engine\Platform::getInstance()->save_configuration($profile_id);
		}

		// Restore the old profile
		\Akeeba\Engine\Factory::nuke();
		\Akeeba\Engine\Platform::getInstance()->load_configuration($oldProfile);
	}

	/**
	 * Check the strength of the Secret Word for front-end and remote backups. If it is insecure return the reason it
	 * is insecure as a string. If the Secret Word is secure return an empty string.
	 *
	 * @return  string
	 */
	public function getFrontendSecretWordError()
	{
		// Is frontend backup enabled?
		$febEnabled = Platform::getInstance()->get_platform_configuration_option('frontend_enable', 0) != 0;

		if (!$febEnabled)
		{
			return '';
		}

		$secretWord = Platform::getInstance()->get_platform_configuration_option('frontend_secret_word', '');

		try
		{
			\Akeeba\Engine\Util\Complexify::isStrongEnough($secretWord);
		}
		catch (RuntimeException $e)
		{
			// Ah, the current Secret Word is bad. Create a new one if necessary.
			$session = JFactory::getSession();
			$newSecret = $session->get('newSecretWord', null, 'akeeba.cpanel');

			if (empty($newSecret))
			{
				$random = new \Akeeba\Engine\Util\RandomValue();
				$newSecret = $random->generateString(32);
				$session->set('newSecretWord', $newSecret, 'akeeba.cpanel');
			}

			return $e->getMessage();
		}

		return '';
	}

    /**
     * Checks if the mbstring extension is installed and enabled
     *
     * @return bool
     */
    public function checkMbstring()
    {
        return function_exists('mb_strlen') && function_exists('mb_convert_encoding') &&
		function_exists('mb_substr') && function_exists('mb_convert_case');
    }
}