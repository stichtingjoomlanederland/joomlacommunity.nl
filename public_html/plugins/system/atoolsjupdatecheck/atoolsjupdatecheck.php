<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\AdminTools\Admin\Helper\Storage;

defined('_JEXEC') or die;

// Uncomment the following line to enable debug mode
// define('ATJUPDATEDEBUG',1);

// PHP version check
if (defined('PHP_VERSION'))
{
	$version = PHP_VERSION;
}
elseif (function_exists('phpversion'))
{
	$version = phpversion();
}
else
{
	$version = '5.0.0'; // all bets are off!
}

if (!version_compare($version, '5.3.4', 'ge'))
{
	return;
}

JLoader::import('joomla.application.plugin');

class plgSystemAtoolsjupdatecheck extends JPlugin
{
	public function onAfterRender()
	{
		// Get the timeout for Joomla! updates
		JLoader::import('joomla.application.component.helper');

		$component     = JComponentHelper::getComponent('com_installer');
		$params        = $component->params;

		$cache_timeout = $params->get('cachetimeout', 6, 'int');
		$cache_timeout = 3600 * $cache_timeout;

		// Do we need to run?
		// Store the last run timestamp inside out table
		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->select($db->qn('value'))
			->from($db->qn('#__admintools_storage'))
			->where($db->qn('key') . ' = ' . $db->q('atoolsjupdatecheck_lastrun'));

		$last = (int)$db->setQuery($query)->loadResult();
		$now  = time();

		if (!defined('ATJUPDATEDEBUG') && (abs($now - $last) < $cache_timeout))
		{
			return;
		}

		// Update last run status
		// If I have the time of the last run, I can update, otherwise insert
		if ($last)
		{
			$query = $db->getQuery(true)
				->update($db->qn('#__admintools_storage'))
				->set($db->qn('value') . ' = ' . $db->q($now))
				->where($db->qn('key') . ' = ' . $db->q('atoolsjupdatecheck_lastrun'));
		}
		else
		{
			$query = $db->getQuery(true)
				->insert($db->qn('#__admintools_storage'))
				->columns(array($db->qn('key'), $db->qn('value')))
				->values($db->q('atoolsjupdatecheck_lastrun') . ', ' . $db->q($now));
		}

		try
		{
			$result = $db->setQuery($query)->execute();
		}
		catch (Exception $exc)
		{
			$result = false;
		}

		if (!$result)
		{
			return;
		}

		// This is the extension ID for Joomla! itself
		$eid = 700;

		// Get any available updates
		$updater = JUpdater::getInstance();
		$results = $updater->findUpdates(array($eid), $cache_timeout);

		if (!$results)
		{
			return;
		}

		require_once JPATH_ADMINISTRATOR . '/components/com_installer/models/update.php';

		$model = JModelLegacy::getInstance('Update', 'InstallerModel');

		$model->setState('filter.extension_id', $eid);
		$updates = $model->getItems();

		if (empty($updates))
		{
			return;
		}

		$update = array_pop($updates);

		// Check the version. It must be different than the current version.
		if (version_compare($update->version, JVERSION, 'eq'))
		{
			return;
		}

		// If we're here, we have updates. Let's create an OTP.
		$uri  = JUri::base();
		$uri  = rtrim($uri, '/');

		$uri .= (substr($uri, -13) != 'administrator') ? '/administrator/' : '/';

		$link = 'index.php?option=com_joomlaupdate';

		$superAdmins     = array();
		$superAdminEmail = $this->params->get('email', '');

		if (!empty($superAdminEmail))
		{
			$superAdmins = $this->_getSuperAdministrators($superAdminEmail);
		}

		if (empty($superAdmins))
		{
			$superAdmins = $this->_getSuperAdministrators();
		}

		if (empty($superAdmins))
		{
			return;
		}

		$this->loadLanguage();
		$email_subject = <<<ENDSUBJECT
THIS EMAIL IS SENT FROM YOUR SITE "[SITENAME]" - Update available
ENDSUBJECT;


			$autoLoginReminder = <<< ALREND
Visiting this link will require you to enter your login credentials (typically
your username and password) into your site's administrator login page in order
to initiate the update process. If you are not sure about the legitimacy of
this email message we strongly recommend you to visit your site's
administrator page manually, log in, and check for the availability of updates
yourself.

ALREND;

		$email_body = <<<ENDBODY
This email IS NOT sent by Joomla.org or Akeeba Ltd. It is sent automatically
by your own site, [SITENAME]

================================================================================
UPDATE INFORMATION
================================================================================

Your site has determined that there is an updated version of Joomla!
available for download.

Joomla! version currently installed:        [CURVERSION]
Joomla! version available for installation: [NEWVERSION]

This email is sent to you by your site to remind you of this fact. The authors
of Joomla! (Open Source Matters) or Admin Tools (Akeeba Ltd) will not contact
you about available updates of Joomla!.

================================================================================
UPDATE INSTRUCTIONS
================================================================================

To install the update on [SITENAME] please click the following link. (If the URL
is not a link, simply copy & paste it to your browser).

Update link: [LINK]

$autoLoginReminder

================================================================================
WHY AM I RECEIVING THIS EMAIL?
================================================================================

This email has been automatically sent by a plugin you, or the person who built
or manages your site, has installed and explicitly activated. This plugin looks
for updated versions of Joomla! and sends an email notification to all Super
Users. You will receive several similar emails from your site, up to 6 times
per day, until you either update the software or disable these emails.

To disable these emails, please unpublish the 'System - Joomla! Update Email'
plugin in the Plugin Manager on your site.

If you do not understand what this means, please do not contact the authors of
Joomla! or Admin Tools. They are NOT sending you this email and they cannot
help you. Instead, please contact the person who built or manages your site.

If you are the person who built or manages your website, please note that you
activated the update email notification feature during Admin Tools' first run,
by clicking on a check box with a clear explanation of how this feature works
printed under it.

================================================================================
WHO SENT ME THIS EMAIL?
================================================================================

This email is sent to you by your own site, [SITENAME]

ENDBODY;

		$newVersion = $update->version;

		$jVersion = new JVersion;
		$currentVersion = $jVersion->getShortVersion();

		$jconfig = JFactory::getConfig();
		$sitename = $jconfig->get('sitename');

		$substitutions = array(
			'[NEWVERSION]' => $newVersion,
			'[CURVERSION]' => $currentVersion,
			'[SITENAME]'   => $sitename
		);

		// If Admin Tools Professional is installed, fetch the administrator secret key as well
		$adminpw   = '';
		$helperFile = JPATH_ROOT . '/administrator/components/com_admintools/Helper/Storage.php';

		if (@file_exists($helperFile))
		{
			include_once $helperFile;

			$model   = Storage::getInstance();
			$adminpw = $model->getValue('adminpw', '');
		}

		foreach ($superAdmins as $sa)
		{
			$emaillink = $uri . $link;

			if (!empty($adminpw))
			{
				$emaillink .= '&' . urlencode($adminpw);
			}

			$substitutions['[LINK]'] = $emaillink;

			foreach ($substitutions as $k => $v)
			{
				$email_subject = str_replace($k, $v, $email_subject);
				$email_body = str_replace($k, $v, $email_body);
			}

			try
			{
				$mailer   = JFactory::getMailer();
				$mailfrom = $jconfig->get('mailfrom');
				$fromname = $jconfig->get('fromname');

				// This line is required because SpamAssassin is BROKEN
				$mailer->Priority = 3;

				$mailer->setSender(array($mailfrom, $fromname));
				$mailer->addRecipient($sa->email);
				$mailer->setSubject($email_subject);
				$mailer->setBody($email_body);
				$mailer->Send();
			}
			catch (Exception $e)
			{
				// Joomla 3.5 is written by incompetent bonobos
			}
		}
	}

	/**
	 * Returns the Super Users email information. If you provide a comma separated $email list
	 * we will check that these emails do belong to Super Users and that they have not blocked
	 * system emails.
	 *
	 * @param   null|string  $email  A list of Super Users to email
	 *
	 * @return  array  The list of Super User emails
	 */
	private function _getSuperAdministrators($email = null)
	{
		// Get a reference to the database object
		$db = JFactory::getDbo();

		// Convert the email list to an array
		if (!empty($email))
		{
			$temp = explode(',', $email);
			$emails = array();

			foreach ($temp as $entry)
			{
				$entry = trim($entry);
				$emails[] = $db->q($entry);
			}

			$emails = array_unique($emails);
		}
		else
		{
			$emails = array();
		}

		// Get a list of groups which have Super User privileges
		$ret = array();

		try
		{
			$query = $db->getQuery(true)
				->select($db->qn('rules'))
				->from($db->qn('#__assets'))
				->where($db->qn('parent_id') . ' = ' . $db->q(0));
			$db->setQuery($query, 0, 1);
			$rulesJSON	 = $db->loadResult();
			$rules		 = json_decode($rulesJSON, true);

			$rawGroups = $rules['core.admin'];
			$groups = array();

			if (empty($rawGroups))
			{
				return $ret;
			}

			foreach ($rawGroups as $g => $enabled)
			{
				if ($enabled)
				{
					$groups[] = $db->q($g);
				}
			}

			if (empty($groups))
			{
				return $ret;
			}
		}
		catch (Exception $exc)
		{
			return $ret;
		}

		// Get the user IDs of users belonging to the SA groups
		try
		{
			$query = $db->getQuery(true)
				->select($db->qn('user_id'))
				->from($db->qn('#__user_usergroup_map'))
				->where($db->qn('group_id') . ' IN(' . implode(',', $groups) . ')' );
			$db->setQuery($query);
			$rawUserIDs = $db->loadColumn(0);

			if (empty($rawUserIDs))
			{
				return $ret;
			}

			$userIDs = array();

			foreach ($rawUserIDs as $id)
			{
				$userIDs[] = $db->q($id);
			}
		}
		catch (Exception $exc)
		{
			return $ret;
		}

		// Get the user information for the Super Administrator users
		try
		{
			$query = $db->getQuery(true)
				->select(array(
					$db->qn('id'),
					$db->qn('username'),
					$db->qn('email'),
				))->from($db->qn('#__users'))
				->where($db->qn('id') . ' IN(' . implode(',', $userIDs) . ')')
				->where($db->qn('sendEmail') . ' = ' . $db->q('1'));

			if (!empty($emails))
			{
				$query->where($db->qn('email') . 'IN(' . implode(',', $emails) . ')');
			}

			$db->setQuery($query);
			$ret = $db->loadObjectList();
		}
		catch (Exception $exc)
		{
			return $ret;
		}

		return $ret;
	}
}