<?php
/**
 * @package    AkeebaBackup
 * @subpackage backuponupdate
 * @copyright  Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license    GNU General Public License version 3, or later
 *
 * @since      3.3
 */
defined('_JEXEC') or die();

if (!version_compare(PHP_VERSION, '5.3.3', '>='))
{
	return;
}

// Make sure Akeeba Backup is installed
if (!file_exists(JPATH_ADMINISTRATOR . '/components/com_akeeba'))
{
	return;
}

// Load F0F
if (!defined('F0F_INCLUDED'))
{
	include_once JPATH_SITE . '/libraries/f0f/include.php';
}

if (!defined('F0F_INCLUDED') || !class_exists('F0FLess', true))
{
	return;
}

// If this is not the Professional release, bail out. So far I have only
// received complaints about this feature from users of the Core release
// who never bothered to read the documentation. FINE! If you are bitching
// about it, you don't get this feature (unless you are a developer who can
// come here and edit the code). Fair enough.
JLoader::import('joomla.filesystem.file');
$db = JFactory::getDBO();

// Is Akeeba Backup enabled?
$qn = version_compare(JVERSION, '2.5.0', 'lt') ? 'nameQuote' : 'qn';
$query = $db->getQuery(true)
            ->select($db->$qn('enabled'))
            ->from($db->$qn('#__extensions'))
            ->where($db->$qn('element') . ' = ' . $db->quote('com_akeeba'))
            ->where($db->$qn('type') . ' = ' . $db->quote('component'));
$db->setQuery($query);
$enabled = $db->loadResult();

if (!$enabled)
{
	return;
}

// Is it the Pro release?
include_once JPATH_ADMINISTRATOR . '/components/com_akeeba/version.php';

if (!defined('AKEEBA_PRO'))
{
	return;
}

if (!AKEEBA_PRO)
{
	return;
}

JLoader::import('joomla.application.plugin');

class plgSystemBackuponupdate extends JPlugin
{
	public function onAfterInitialise()
	{
		// Make sure this is the back-end
		$app = JFactory::getApplication();

		if (!in_array($app->getName(), array('administrator', 'admin')))
		{
			return;
		}

		if (version_compare(JVERSION, '2.5.0', 'lt'))
		{
			$this->autoDisable();

			return;
		}

		// Get the input variables
		$ji        = new JInput();
		$component = $ji->getCmd('option', '');
		$task      = $ji->getCmd('task', '');
		$view      = $ji->getCmd('view', '');
		$backedup  = $ji->getInt('is_backed_up', 0);

		// Perform a redirection on Joomla! Update download or install task, unless we have already backed up the site
		if (($component == 'com_joomlaupdate') && ($task == 'update.install') && !$backedup)
		{
			// Get the backup profile ID
			$profileId = (int) $this->params->get('profileid', 1);

			if ($profileId <= 0)
			{
				$profileId = 1;
			}

			// Get the return URL
			$return_url = JUri::base() . 'index.php?option=com_joomlaupdate&task=update.install&is_backed_up=1';

			// Get the redirect URL
			$token        = JFactory::getSession()->getToken();
			$redirect_url = JUri::base() . 'index.php?option=com_akeeba&view=backup&autostart=1&returnurl=' . urlencode($return_url) . '&profileid=' . $profileId . "&$token=1";

			// Perform the redirection
			$app = JFactory::getApplication();
			$app->redirect($redirect_url);
		}
	}

	public function autoDisable()
	{
		$this->loadLanguage();
		$pluginName = JText::_('PLG_SYSTEM_BACKUPONUPDATE_TITLE');

		if ($pluginName == 'PLG_SYSTEM_BACKUPONUPDATE_TITLE')
		{
			$pluginName = 'System - Backup on update';
		}

		$msg = JText::sprintf('PLG_SYSTEM_BACKUPONUPDATE_MSG_AUTODISABLE', $pluginName);

		if ($msg == 'PLG_SYSTEM_AKEEBAUPDATECHECK_MSG')
		{
			$msg = sprintf('The plugin %s will only work on Joomla! 2.5. Since it is incompatible with your version of Joomla! it will now disable itself.', $pluginName);
		}

		JFactory::getApplication()->enqueueMessage($msg, 'warning');

		$db = JFactory::getDbo();

		// Let's get the information of the update plugin
		$query = $db->getQuery(true)
		            ->select('*')
		            ->from($db->nameQuote('#__extensions'))
		            ->where($db->nameQuote('folder') . ' = ' . $db->quote('system'))
		            ->where($db->nameQuote('element') . ' = ' . $db->quote('backuponupdate'))
		            ->where($db->nameQuote('type') . ' = ' . $db->quote('plugin'))
		            ->order($db->nameQuote('ordering') . ' ASC');
		$db->setQuery($query);

		$plugin = $db->loadObject();

		if (!is_object($plugin))
		{
			return;
		}

		// Otherwise, try to enable it and report false (so the user knows what he did wrong)
		$pluginObject = (object) array(
				'extension_id' => $plugin->extension_id,
				'enabled'      => 0
		);

		try
		{
			$db->updateObject('#__extensions', $pluginObject, 'extension_id');
		}
		catch (Exception $e)
		{
		}

		if (!class_exists('F0FUtilsCacheCleaner'))
		{
			include_once JPATH_SITE . '/libraries/f0f/include.php';
		}

		if (class_exists('F0FUtilsCacheCleaner'))
		{
			F0FUtilsCacheCleaner::clearPluginsCache();
		}
	}
}
