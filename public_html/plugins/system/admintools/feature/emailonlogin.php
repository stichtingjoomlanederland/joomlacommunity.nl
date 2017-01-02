<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

class AtsystemFeatureEmailonlogin extends AtsystemFeatureAbstract
{
	protected $loadOrder = 220;

	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		if (!$this->helper->isBackend())
		{
			return false;
		}

		if ($this->isAdminAccessAttempt())
		{
			return false;
		}

		$user = JFactory::getUser();

		if ($user->guest)
		{
			return false;
		}

		$email = $this->cparams->getValue('emailonadminlogin', '');

		return !empty($email);
	}

	/**
	 * Sends an email upon accessing an administrator page other than the login screen
	 */
	public function onAfterInitialise()
	{
		$user = JFactory::getUser();

		// Check if the session flag is set (avoid sending thousands of emails!)
		$session = JFactory::getSession();
		$flag = $session->get('waf.loggedin', 0, 'plg_admintools');

		if ($flag == 1)
		{
			return;
		}

		// Set the flag to prevent sending more emails
		$session->set('waf.loggedin', 1, 'plg_admintools');

		// Load the component's administrator translation files
		$jlang = JFactory::getLanguage();
		$jlang->load('com_admintools', JPATH_ADMINISTRATOR, 'en-GB', true);
		$jlang->load('com_admintools', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
		$jlang->load('com_admintools', JPATH_ADMINISTRATOR, null, true);

		// Get the username
		$username = $user->username;
		// Get the site name
		$config = JFactory::getConfig();

		$sitename = $config->get('sitename');

		// Get the IP address
		$ip = AtsystemUtilFilter::getIp();

		if ((strpos($ip, '::') === 0) && (strstr($ip, '.') !== false))
		{
			$ip = substr($ip, strrpos($ip, ':') + 1);
		}

		$country = '';
		$continent = '';

		if (class_exists('AkeebaGeoipProvider'))
		{
			$geoip     = new AkeebaGeoipProvider();
			$country   = $geoip->getCountryCode($ip);
			$continent = $geoip->getContinent($ip);
		}

		if (empty($country))
		{
			$country = '(unknown country)';
		}

		if (empty($continent))
		{
			$continent = '(unknown continent)';
		}

		$uri = JUri::getInstance();
		$url = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port', 'path', 'query', 'fragment'));

		$ip_link = $this->cparams->getValue('iplookupscheme', 'http') . '://' . $this->cparams->getValue('iplookup', 'ip-lookup.net/index.php?ip={ip}');
		$ip_link = str_replace('{ip}', $ip, $ip_link);

		// Construct the replacement table
		$substitutions = array(
			'[SITENAME]'  => $sitename,
			'[REASON]'	  => JText::_('COM_ADMINTOOLS_WAFEMAILTEMPLATE_REASON_ADMINLOGINSUCCESS'),
			'[DATE]'      => gmdate('Y-m-d H:i:s') . " GMT",
			'[URL]'       => $url,
			'[USER]'      => $username,
			'[IP]'        => $ip,
			'[LOOKUP]'    => '<a href="' . $ip_link . '">IP Lookup</a>',
			'[COUNTRY]'   => $country,
			'[CONTINENT]' => $continent,
			'[UA]'		  => $_SERVER['HTTP_USER_AGENT'],
		);

		// Let's get the most suitable email template
		$template = $this->exceptionsHandler->getEmailTemplate('adminloginsuccess');

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
			$body = str_replace($k, $v, $body);
		}

		// Send the email
		try
		{
			$mailer = JFactory::getMailer();

			$mailfrom = $config->get('mailfrom');
			$fromname = $config->get('fromname');

			$recipients = explode(',', $this->cparams->getValue('emailonadminlogin', ''));
			$recipients = array_map('trim', $recipients);

			foreach ($recipients as $recipient)
			{
				// This line is required because SpamAssassin is BROKEN
				$mailer->Priority = 3;

				$mailer->isHtml(true);
				$mailer->setSender(array($mailfrom, $fromname));
				$mailer->addRecipient($recipient);
				$mailer->setSubject($subject);
				$mailer->setBody($body);
				$mailer->Send();
			}
		}
		catch (\Exception $e)
		{
			// Joomla 3.5 is written by incompetent bonobos
		}
	}
} 