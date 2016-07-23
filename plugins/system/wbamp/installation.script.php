<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.4.2.551
 * @date        2016-07-19
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Installation/Uninstallation script
 *
 */
class plgSystemWbampInstallerScript
{
	const MIN_JOOMLA_VERSION = '3.4.2';
	const MAX_JOOMLA_VERSION = '4';
	const MIN_SHLIB_VERSION = '0.3.0';
	const MAX_SHLIB_VERSION = '';

	private $_shlibVersion = '';
	private $_skipInstall  = array();
	private $_isInstalled;

	private $_wbampPlugins = array(
		'wbamp' => array('content', 'k2'),
		'editors-xtd' => array('wbamp')
	);

	public function install($parent)
	{
	}

	public function update($parent)
	{
	}

	public function uninstall($parent)
	{
		$this->_doUninstall($parent);
	}

	/**
	 * Check Joomla and possibly existing shLib versions
	 *
	 * @param $route
	 * @param $installer
	 * @return bool
	 */
	public function preflight($route, $installer)
	{
		if ($route == 'install' || $route == 'update')
		{
			// check Joomla! version
			if (version_compare(JVERSION, self::MIN_JOOMLA_VERSION, '<') || version_compare(JVERSION, self::MAX_JOOMLA_VERSION, 'ge'))
			{
				JFactory::getApplication()
				        ->enqueueMessage(
					        sprintf(
						        'wbAMP requires Joomla! version between %s and %s (you are using %s). Aborting installation',
						        self::MIN_JOOMLA_VERSION, self::MAX_JOOMLA_VERSION, JVERSION
					        ), 'error'
				        );

				return false;
			}

			// make sure resource manager is available, we'll need it during plugins installs
			if (!class_exists('ShlSystem_Resourcemanager'))
			{
				$path = $installer->getParent()->getPath('source') . '/vendors/weeblr/shlib/shl_packages/system/resourcemanager.php';
				if (file_exists($path))
				{
					require_once $installer->getParent()->getPath('source') . '/vendors/weeblr/shlib/shl_packages/system/resourcemanager.php';
				}
				else
				{
					JFactory::getApplication()->enqueueMessage('Cannot find file ' . $path . ' aborting installation. This package appear to have been damaged.');
					return false;
				}
			}

			// check authorization to install for shared resources
			$newVersionFile = $installer->getParent()->getPath('source') . '/vendors/weeblr/shlib/shlib.xml';
			if (!file_exists($newVersionFile))
			{
				JFactory::getApplication()->enqueueMessage('Cannot find file ' . $path . ' aborting installation. This package appear to have been damaged.');
				return false;
			}
			$this->_shlibVersion = ShlSystem_Resourcemanager::getXmlFileVersion($newVersionFile);
			$installCheckResult = ShlSystem_Resourcemanager::canInstall('shlib', $this->_shlibVersion, $allowDowngrade = false, self::MIN_SHLIB_VERSION, self::MAX_SHLIB_VERSION);

			if ($installCheckResult->canInstall == 'no')
			{
				JFactory::getApplication()
				        ->enqueueMessage(
					        'Cannot install wbAMP: not allowed to install shLib version ' . $this->_shlibVersion . ': ' . $installCheckResult->reason,
					        'error'
				        );
			}
			if ($installCheckResult->canInstall == 'skip')
			{
				$this->_shlibVersion = '';
				$this->_skipInstall[] = 'shlib';
				JFactory::getApplication()
				        ->enqueueMessage('shLib: skipping install of shLib version ' . $this->_shlibVersion . ': ' . $installCheckResult->reason);
			}

			$canInstall = $installCheckResult->canInstall != 'no';

			// check if we are already installed
			$this->isInstalled('wbamp', 'system');

			return $canInstall;
		}
	}

	/**
	 * Post install: Register that we are using shLib
	 *
	 * @param string $basePath , the base path to get original files from
	 */
	public function postflight($type, $parent)
	{
		if (function_exists('apc_clear_cache'))
		{
			apc_clear_cache();
		}
		if (function_exists('opcache_reset'))
		{
			opcache_reset();
		}

		$this->_doInstallUpdate($parent);

		// installed a shared resource? register it with version
		if (!in_array('shlib', $this->_skipInstall))
		{
			ShlSystem_Resourcemanager::registerResource('shlib', $this->_shlibVersion);
		}

		// register that we are now using shLib
		ShlSystem_Resourcemanager::register(array('resource' => 'shlib', 'context' => 'wbamp', 'min_version' => self::MIN_SHLIB_VERSION, 'max_version' => self::MAX_SHLIB_VERSION));

		// make sure the update site is appropriate
		$this->_processUpdateSite($type, $parent);
	}

	/**
	 * Implementation of install/uninstall scripts
	 */

	private function _doInstallUpdate($parent)
	{
		// install shLib
		$status = $this->installLibraries($parent);

		// enable ourselves on first install
		if ($status && $this->_isInstalled === false)
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);

			$query->update('#__extensions');
			$query->where($db->quoteName('type') . '=' . $db->Quote('plugin'));
			$query->where($db->quoteName('element') . '=' . $db->Quote('wbamp'));
			$query->where($db->quoteName('folder') . '=' . $db->Quote('system'));
			$query->set($db->quoteName('enabled') . '=' . $db->Quote(1));

			$db->setQuery($query);
			$db->execute();
		}
	}

	/**
	 * Try to read a plugin Id from DB, to find if it's already
	 * installed. Joomla JInstaller is Upgrade
	 * always return true.
	 *
	 * @param $pluginElement
	 * @param $pluginFolder
	 * @return bool
	 */
	private function isInstalled($pluginElement, $pluginFolder)
	{
		try
		{
			$this->_isInstalled = null;
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('extension_id')->from('#__extensions')->where($db->qn('type') . '=' . $db->q('plugin'))
			      ->where($db->qn('element') . '=' . $db->q($pluginElement))
			      ->where($db->qn('folder') . '=' . $db->q($pluginFolder));
			$db->setQuery($query);
			$pluginId = $db->loadResult();
			$this->_isInstalled = !empty($pluginId);
		}
		catch (Exception $e)
		{
			$this->_isInstalled = null;
			JFactory::getApplication()->enqueueMessage('Error reading pre-existing plugin record from db: ' . $pluginFolder . ' / ' . $pluginElement);
		}
		return $this->_isInstalled;
	}

	private function installLibraries($parent)
	{
		// install plugins
		if (defined('SHLIB_ROOT_PATH'))
		{
			// trick of the day: we must fetch an instance of the db using the db helper
			// before installing the newest version of shLib system plugin. This will
			// force a decorated db instance to be created and stored, using the shlib
			// db class version that matches that of the shlib db helper class
			// As there was interface changes betwen shLib 0.1.x and 0.2.x, this prevents
			// "method not existing" errors when installing a newer version over an old one
			// make sure resource manager is available, we'll need it during plugins installs
			$db = ShlDbHelper::getInstance();
			$status = $this->_installPlugin('system', 'shlib', $sourcePath = JPATH_ROOT . '/plugins/system/wbamp/vendors/weeblr/shlib');
		}
		else
		{
			$status = $this->_installPlugin('system', 'shlib', $sourcePath = JPATH_ROOT . '/plugins/system/wbamp/vendors/weeblr/shlib');
			// shLib is not installed yet, let's make it available to us
			if (!JFile::exists(JPATH_ROOT . '/plugins/system/shlib/shlib.php'))
			{
				JFactory::getApplication()
				        ->enqueuemessage('shLib was not installed properly, cannot continue. Please try uninstalling and installing again');

				return false;
			}
			require_once JPATH_ROOT . '/plugins/system/shlib/shlib.php';
			$config = array('type' => 'system', 'name' => 'shlib', 'params' => '');
			if (version_compare(JVERSION, '3', 'ge'))
			{
				$dispatcher = JEventDispatcher::getInstance();
			}
			else
			{
				$dispatcher = JDispatcher::getInstance();
			}
			$shLibPlugin = new plgSystemShlib($dispatcher, $config);
			$shLibPlugin->onAfterInitialise();
			$status = true;
		}

		$status = $status && $this->fixPluginsOrder();

		// extension specific plugins
		foreach ($this->_wbampPlugins as $group => $pluginNames)
		{
			foreach ($pluginNames as $pluginName)
			{
				$installed = $this->_installPlugin($group, $pluginName, $sourcePath = JPATH_ROOT . '/plugins/system/wbamp/vendors/weeblr/wbamp/plugins/' . $group . '/' . $pluginName);
				if (!$installed)
				{
					JFactory::getApplication()->enqueuemessage('Error installing plugin ' . $pluginName . ' in group ' . $group);
					$status = false;
				}
			}
		}

		// install template
		$installer = new JInstaller;
		$installed = $installer->install(JPATH_ROOT . '/plugins/system/wbamp/vendors/weeblr/wbamp/templates/wbamp');
		if (!$installed)
		{
			JFactory::getApplication()->enqueuemessage('Error installing template wbAMP');
			$status = false;
		}

		// throw error if any error happened
		if (!$status)
		{
			JFactory::getApplication()
			        ->enqueuemessage('There was an error installing one or more system plugins. Please try uninstalling and installing again');
		}

		return $status;
	}

	/**
	 * Makes sure the shLib, wbamp (and sh404SEF) system
	 * plugin is correct
	 */
	private function fixPluginsOrder()
	{
		// Read current ordering
		$app = JFactory::getApplication();
		try
		{
			// then make sure realtive order
			$this->ensurePluginsOrder('wbamp', 'sh404sef')
			     ->ensurePluginsOrder('shlib', 'wbamp');
		}
		catch (Exception $e)
		{
			$app->enqueueMessage('Error fixing plugin order in database: ' . $e->getMessage());
			return false;
		}

		return true;
	}

	private function ensurePluginsOrder($plugin1, $plugin2)
	{
		$order = $plugin1 . $plugin2;

		// Read current ordering
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')->from('#__extensions')->where($db->qn('type') . '=' . $db->q('plugin'))
		      ->where($db->qn('folder') . '=' . $db->q('system'))
		      ->where($db->qn('element') . " in ('" . $plugin1 . "','" . $plugin2 . "')")
		      ->order('ordering asc');
		$db->setQuery($query);
		$plugins = $db->loadObjectList('element');

		// if one of the plugin is not there, don't go further
		if (empty($plugins[$plugin1]) || empty($plugins[$plugin2]))
		{
			return $this;
		}

		$signature = array_reduce(
			$plugins, function ($carry, $item)
		{
			return $carry . $item->element;
		}
		);

		if ($signature != $order || $plugins[$plugin2]->ordering == $plugins[$plugin1]->ordering)
		{
			// special case, same ordering
			if ($plugins[$plugin2]->ordering == $plugins[$plugin1]->ordering)
			{
				$plugins[$plugin2]->ordering = $plugins[$plugin1]->ordering + 1;
			}

			$query = $db->getQuery(true);
			// not in the right order, swap them
			$query->update($db->qn('#__extensions'))
			      ->set($db->qn('ordering') . '=' . $db->q($plugins[$plugin2]->ordering))
			      ->where($db->qn('extension_id') . '=' . $db->q($plugins[$plugin1]->extension_id));
			$db->setQuery($query)
			   ->query();
			$query = $db->getQuery(true);
			$query->update($db->qn('#__extensions'))
			      ->set($db->qn('ordering') . '=' . $db->q($plugins[$plugin1]->ordering))
			      ->where($db->qn('extension_id') . '=' . $db->q($plugins[$plugin2]->extension_id));
			$db->setQuery($query)
			   ->query();
		}

		return $this;
	}

	/**
	 * Install a given plugin
	 *
	 * @param string $pluginFolder
	 * @param string $pluginElement
	 * @param string $basePath
	 */
	private function _installPlugin($pluginFolder, $pluginElement, $sourcePath)
	{
		if ($pluginFolder == 'system' && $pluginElement == 'shlib')
		{
			if (in_array('shlib', $this->_skipInstall))
			{
				return true;
			}
		}

		$status = true;
		$app = JFactory::getApplication();

		// in case of upgrade, don't touch settings by user
		try
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('*')->from('#__extensions')->where($db->qn('type') . '=' . $db->q('plugin'))
			      ->where($db->qn('element') . '=' . $db->q($pluginElement))
			      ->where($db->qn('folder') . '=' . $db->q($pluginFolder));
			$db->setQuery($query);
			$pluginData = $db->loadObject();
		}
		catch (Exception $e)
		{
			$status = false;
			$app->enqueueMessage('Error reading pre-existing plugin record from db: ' . $pluginFolder . ' / ' . $pluginElement);

			return $status;
		}

		$overrides = empty($pluginData) ? array('ordering' => 10, 'enabled' => 1) : array();

		// make sure main library is loaded first
		if (empty($pluginData) && $pluginElement == 'shlib')
		{
			$overrides['ordering'] = -100;
		}

		// use J! installer to fully install the plugin
		$installer = new JInstaller;
		$result = $installer->install($sourcePath);

		if ($result)
		{
			// overrides data in extension table, possibly overriding some columns from saved data
			if (!empty($overrides))
			{
				try
				{
					$query = $db->getQuery(true);
					$query->select('extension_id')->from('#__extensions')->where($db->qn('type') . '=' . $db->q('plugin'))
					      ->where($db->qn('element') . '=' . $db->q($pluginElement))
					      ->where($db->qn('folder') . '=' . $db->q($pluginFolder));
					$db->setQuery($query);
					$pluginId = $db->loadResult();

					if (!empty($pluginId))
					{
						jimport('joomla.database.table.extension');
						$extension = JTable::getInstance('Extension');
						$extension->load($pluginId);
						$extension->bind($overrides);
						$status = $extension->store();
						if (!$status)
						{
							$app->enqueueMessage('Error writing updated extension record: ' . $extension->getError() . ' for ' . $pluginFolder . ' / ' . $pluginElement);
						}
					}
					else
					{
						$app->enqueueMessage('Error updating plugin DB record: ' . $pluginFolder . ' / ' . $pluginElement);
					}
				}
				catch (Exception $e)
				{
					$status = false;
					$app->enqueueMessage('Error: ' . $e->getMessage());
				}
			}
		}
		else
		{
			$app->enqueueMessage('Error installing wbAMP plugin: ' . $pluginFolder . ' / ' . $pluginElement);
			$status = false;
		}

		return $status;
	}

	/**
	 * Performs pre-uninstall backup of configuration
	 *
	 * @param object $parent
	 */
	private function _doUninstall($parent)
	{
		// unregister from shLib, then possibly uninstall it
		if (JFile::exists(JPATH_ROOT . '/plugins/system/shlib/shl_packages/system/resourcemanager.php'))
		{
			require_once JPATH_ROOT . '/plugins/system/shlib/shl_packages/system/resourcemanager.php';
			ShlSystem_Resourcemanager::unregister('shlib', 'wbamp');
			if (ShlSystem_Resourcemanager::canUninstall('shlib'))
			{
				$this->_uninstallPlugin('system', 'shlib');
			}
		}

		// extension support plugins
		foreach ($this->_wbampPlugins as $group => $pluginNames)
		{
			foreach ($pluginNames as $pluginName)
			{
				$this->_uninstallPlugin($group, $pluginName);
			}
		}

		// remove template
		$this->_uninstallTemplate('wbamp');

		// display results
		echo '<hr /><h3>wbAMP has been uninstalled. </h3><hr />';
	}

	private function _uninstallPlugin($folder, $pluginName)
	{
		try
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('*')
			      ->from('#__extensions')
			      ->where($db->qn('type') . '=' . $db->q('plugin'))
			      ->where($db->qn('element') . '=' . $db->q($pluginName))
			      ->where($db->qn('folder') . '=' . $db->q($folder));
			$db->setQuery($query);
			$result = $db->loadAssoc();

			if (empty($result))
			{
				// invalid plugin name?
				return false;
			}

			// remove plugin db id
			$pluginId = $result['extension_id'];

			// now uninstall
			$installer = new JInstaller;
			$result = $installer->uninstall('plugin', $pluginId);
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueuemessage($e->getMessage(), 'error');
		}
	}

	private function _uninstallTemplate($templateName)
	{
		try
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('extension_id');
			$query->from($db->quoteName('#__extensions'));
			$query->where($db->quoteName('type') . '=' . $db->quote('template'));
			$query->where($db->quoteName('element') . '=' . $db->quote($templateName));
			$db->setQuery($query);
			$templateId = (int) $db->loadResult();
			if (empty($templateId))
			{
				JFactory::getApplication()->enqueuemessage('wbAMP template not found, not uninstalled.');
				return true;
			}

			// use J! installer to fully uninstall the template
			$installer = new JInstaller;
			$result = $installer->uninstall('template', $templateId);

			if (!$result)
			{
				JFactory::getApplication()->enqueuemessage('Error uninstalling wbAMP template', 'error');
			}

			return $result;
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueuemessage($e->getMessage(), 'error');
		}
	}

	/**
	 * Make sure the update site is correct when switching from
	 * one edition to another.
	 * Specifically, wipe out update site when going from an
	 * edition that does auto-update to one that does not,
	 * or if the update file URL is not the same
	 * (ie community to full)
	 *
	 * @param unknown $type
	 * @param unknown $parent
	 *
	 * @return boolean
	 */
	private function _processUpdateSite($type, $parent)
	{
		// figure out the extension id
		try
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('extension_id')->from('#__extensions')
			      ->where($db->qn('type') . '=' . $db->q('plugin'))
			      ->where($db->qn('element') . '=' . $db->q('wbamp'))
			      ->where($db->qn('folder') . '=' . $db->q('system'));
			$db->setQuery($query);
			$extensionId = $db->loadResult();
		}
		catch (Exception $e)
		{
			$extensionId = 0;
		}

		// Make sure we wipe out
		// any existing update site. We use Joomla code for that
		if (!empty($extensionId))
		{
			JPluginHelper::importPlugin('extension');
			$dispatcher = ShlSystem_factory::dispatcher();
			// Fire the onExtensionAfterInstall
			$result = null;
			$dispatcher->trigger('onExtensionAfterUninstall', array('installer' => clone $parent, 'eid' => $extensionId, 'result' => $result));
		}

		return true;
	}
}
