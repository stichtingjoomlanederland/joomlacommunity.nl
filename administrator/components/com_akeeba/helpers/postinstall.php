<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 *
 * @since     4.0
 *
 * Helper functions for the Post-installation Messages in Joomla! 3.2.0 and later
 */

defined('_JEXEC') or die();

/**
 * Should I show the backup on update message?
 *
 * @return bool
 */
function com_akeeba_postinstall_backuponupdate_condition()
{
	$db = JFactory::getDBO();

	$query = $db->getQuery(true)
		->select($db->qn('enabled'))
		->from($db->qn('#__extensions'))
		->where($db->qn('element') . ' = ' . $db->q('backuponupdate'))
		->where($db->qn('folder') . ' = ' . $db->q('system'));
	$db->setQuery($query);
	$enabledBOU = $db->loadResult();

	return !$enabledBOU;
}

/**
 * Enable the backup on update feature
 */
function com_akeeba_postinstall_backuponupdate_action()
{
	$db = JFactory::getDBO();

	$query = $db->getQuery(true)
		->update($db->qn('#__extensions'))
		->set($db->qn('enabled') . ' = ' . $db->q('1'))
		->where($db->qn('element') . ' = ' . $db->q('backuponupdate'))
		->where($db->qn('folder') . ' = ' . $db->q('system'));
	$db->setQuery($query);
	$db->execute();
}

/**
 * Should I show the configuration wizard message?
 *
 * @return bool
 */
function com_akeeba_postinstall_confwiz_condition()
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

	$lv = $params->get('lastversion', '');

	return empty($lv);
}

/**
 * Run the configuration wizard
 *
 * @return void
 */
function com_akeeba_postinstall_confwiz_action()
{
	com_akeeba_postinstall_common_savesettings(0);

	$url = 'index.php?option=com_akeeba&view=confwiz';
	JFactory::getApplication()->redirect($url);
}

/**
 * Should I show the ANGIE upgrade message?
 *
 * @return bool
 */
function com_akeeba_postinstall_angie_condition()
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

	$angieupgrade = $params->get('angieupgrade', '0');

	return !$angieupgrade;
}

/**
 * Apply the ANGIE upgrade
 */
function com_akeeba_postinstall_angie_action()
{
	// Necessary defines for Akeeba Engine
	if (!defined('AKEEBAENGINE'))
	{
		define('AKEEBAENGINE', 1); // Required for accessing Akeeba Engine's factory class
		define('AKEEBAROOT', dirname(__FILE__) . '/../akeeba');
		define('ALICEROOT', dirname(__FILE__) . '/../alice');
	}

	// Load the factory
	require_once JPATH_ADMINISTRATOR . '/components/com_akeeba/engine/Factory.php';
	\Akeeba\Engine\Platform::addPlatform('joomla25', JPATH_ADMINISTRATOR . '/components/com_akeeba/platform/joomla25');

	if (!file_exists(JPATH_ADMINISTRATOR . '/components/com_akeeba/alice/factory.php'))
	{
		@require_once JPATH_ADMINISTRATOR . '/components/com_akeeba/alice/factory.php';
	}

	// Get all profiles
	include_once JPATH_SITE.'/libraries/f0f/include.php';
	if (!defined('F0F_INCLUDED') || !class_exists('F0FForm', true))
	{
		JError::raiseError ('500', 'Your Akeeba Backup installation is broken; please re-install. Alternatively, extract the installation archive and copy the fof directory inside your site\'s libraries directory.');
		return;
	}

	$model = F0FModel::getTmpInstance('Cpanels', 'AkeebaModel');
	$db = JFactory::getDbo();

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

	// Upgrade all profiles
	foreach ($profiles as $profile_id)
	{
		\Akeeba\Engine\Factory::nuke();
		\Akeeba\Engine\Platform::getInstance()->load_configuration($profile_id);
		$config = \Akeeba\Engine\Factory::getConfiguration();
		$config->set('akeeba.advanced.embedded_installer', 'angie');
		\Akeeba\Engine\Platform::getInstance()->save_configuration($profile_id);
	}

	// Restore the old profile
	\Akeeba\Engine\Factory::nuke();
	\Akeeba\Engine\Platform::getInstance()->load_configuration($oldProfile);

	com_akeeba_postinstall_common_savesettings(1);
}

function com_akeeba_postinstall_common_savesettings($upgradedAngie = 0)
{
	include_once JPATH_SITE.'/libraries/f0f/include.php';

	if (!defined('F0F_INCLUDED') || !class_exists('F0FForm', true))
	{
		JError::raiseError ('500', 'Your Akeeba Backup installation is broken; please re-install. Alternatively, extract the installation archive and copy the fof directory inside your site\'s libraries directory.');
		return;
	}

	$db = JFactory::getDbo();

	// Update last version check and minstability. DO NOT USE JCOMPONENTHELPER!
	$sql = $db->getQuery(true)
		->select($db->qn('params'))
		->from($db->qn('#__extensions'))
		->where($db->qn('type') . ' = ' . $db->q('component'))
		->where($db->qn('element') . ' = ' . $db->q('com_akeeba'));
	$db->setQuery($sql);

	$rawparams = $db->loadResult();

	$params = new JRegistry();
	$params->loadString($rawparams);

	if (!defined('AKEEBA_VERSION'))
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_akeeba/version.php';
	}

	$version = AKEEBA_VERSION;

	if ($upgradedAngie)
	{
		$params->set('angieupgrade', $upgradedAngie);
	}
	else
	{
		$params->set('lastversion', $version);
	}

	$data = $params->toString('JSON');
	$sql = $db->getQuery(true)
		->update($db->qn('#__extensions'))
		->set($db->qn('params') . ' = ' . $db->q($data))
		->where($db->qn('element') . ' = ' . $db->q('com_akeeba'))
		->where($db->qn('type') . ' = ' . $db->q('component'));
	$db->setQuery($sql);
	$db->execute();

	// Even better, create the "akeeba.lastversion.php" file with this information
	$fileData = "<" . "?php\ndefined('_JEXEC') or die();\ndefine('AKEEBA_LASTVERSIONCHECK','" .
		$version . "');";
	JLoader::import('joomla.filesystem.file');
	$fileName = JPATH_ADMINISTRATOR . '/components/com_akeeba/akeeba.lastversion.php';
	JFile::write($fileName, $fileData);

	// Reset the plugins and modules cache
	F0FUtilsCacheCleaner::clearPluginsCache();
}