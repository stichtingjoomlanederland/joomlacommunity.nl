<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

class AtsystemFeatureEmailfailedadminlong extends AtsystemFeatureAbstract
{
	protected $loadOrder = 810;

	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		if ($this->cparams->getValue('trackfailedlogins', 0) == 1)
		{
			// When track failed logins is enabled we don't send emails through this feature
			return false;
		}

		if (!$this->container->platform->isBackend())
		{
			return false;
		}

		$emailonfailedadmin = $this->cparams->getValue('emailonfailedadminlogin', '');

		if (empty($emailonfailedadmin))
		{
			return false;
		}

		return true;
	}

	/**
	 * Sends an email upon a failed administrator login
	 *
	 * @param JAuthenticationResponse $response
	 *
	 * @return  void
	 */
	public function onUserLoginFailure($response)
	{
		// Make sure we don't fire unless someone is still in the login page
		$user = $this->container->platform->getUser();

		if (!$user->guest)
		{
			return;
		}

		$option = $this->input->getCmd('option');
		$task   = $this->input->getCmd('task');

		if (($option != 'com_login') && ($task != 'login'))
		{
			return;
		}

		// Exit if the IP is blacklisted; logins originating from blacklisted IPs will be blocked anyway
		if ($this->parentPlugin->runBooleanFeature('isIPBlocked', false, []))
		{
			return;
		}

		// If we are STILL in the login task WITHOUT a valid user, we had a login failure.

		// Load the component's administrator translation files
		$jlang = JFactory::getLanguage();
		$jlang->load('com_admintools', JPATH_ADMINISTRATOR, 'en-GB', true);
		$jlang->load('com_admintools', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
		$jlang->load('com_admintools', JPATH_ADMINISTRATOR, null, true);

		// Get the site name
		$config = $this->container->platform->getConfig();

		// Construct the replacement table
		$substitutions = $this->exceptionsHandler->getEmailVariables(JText::_('COM_ADMINTOOLS_WAFEMAILTEMPLATE_REASON_ADMINLOGINFAIL'));

		// Let's get the most suitable email template
		$template = $this->exceptionsHandler->getEmailTemplate('adminloginfail', true);

		// Got no template, the user didn't published any email template, or the template doesn't want us to
		// send a notification email. Anyway, let's stop here.
		if (!$template)
		{
			return true;
		}
		else
		{
			$subject = $template[0];
			$body = $template[1];
		}

		foreach ($substitutions as $k => $v)
		{
			$subject = str_replace($k, $v, $subject);
			$body    = str_replace($k, $v, $body);
		}

		// Send the email
		try
		{
			$mailer = JFactory::getMailer();

			$mailfrom = $config->get('mailfrom');
			$fromname = $config->get('fromname');

			$recipients = explode(',', $this->cparams->getValue('emailonfailedadminlogin', ''));
			$recipients = array_map('trim', $recipients);

			foreach ($recipients as $recipient)
			{
				if (empty($recipient))
				{
					continue;
				}

				// This line is required because SpamAssassin is BROKEN
				$mailer->Priority = 3;

				$mailer->isHtml(true);
				$mailer->setSender(array($mailfrom, $fromname));

				// Resets the recipients, otherwise they will pile up
				$mailer->clearAllRecipients();

				if ($mailer->addRecipient($recipient) === false)
				{
					// Failed to add a recipient?
					continue;
				}

				$mailer->setSubject($subject);
				$mailer->setBody($body);
				$mailer->Send();
			}
		}
		catch (\Exception $e)
		{
			// Joomla! 3.5 and later throw an exception when crap happens instead of suppressing it and returning false
		}
	}
}
