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

// Load FOF
if (!defined('FOF30_INCLUDED') && !@include_once(JPATH_LIBRARIES . '/fof30/include.php'))
{
	return;
}

// If this is not the Professional release, bail out. So far I have only
// received complaints about this feature from users of the Core release
// who never bothered to read the documentation. FINE! If you are bitching
// about it, you don't get this feature (unless you are a developer who can
// come here and edit the code). Fair enough.
JLoader::import('joomla.filesystem.file');
$db = JFactory::getDbo();

// Is Akeeba Backup enabled?
$query = $db->getQuery(true)
            ->select($db->qn('enabled'))
            ->from($db->qn('#__extensions'))
            ->where($db->qn('element') . ' = ' . $db->q('com_akeeba'))
            ->where($db->qn('type') . ' = ' . $db->q('component'));
$db->setQuery($query);
$enabled = $db->loadResult();

if (!$enabled)
{
	return;
}

// Is it the Pro release?
@include_once(JPATH_ADMINISTRATOR . '/components/com_akeeba/version.php');

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

		// Get the input variables
		$ji        = new JInput();
		$component = $ji->getCmd('option', '');
		$task      = $ji->getCmd('task', '');
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
			$redirect_url = JUri::base() . 'index.php?option=com_akeeba&view=Backup&autostart=1&returnurl=' . urlencode($return_url) . '&profileid=' . $profileId . "&$token=1";

			// Perform the redirection
			$app = JFactory::getApplication();
			$app->redirect($redirect_url);
		}
	}
}
