<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

/**
 * Allows users to "rename" their administrator directory. In fact, the "rename" is a smokes and mirrors trick,
 * manipulating Joomla!'s SEF routing to mask the administrator directory.
 */
class AtsystemFeatureCustomadminfolder extends AtsystemFeatureAbstract
{
	protected $loadOrder = 40;

	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		$config = JFactory::getConfig();
		$folder = $this->cparams->getValue('adminlogindir');

		// Custom admin folder is disabled
		if (!$folder || !$config->get('sef') || !$config->get('sef_rewrite'))
		{
			return false;
		}

		return true;
	}

	/**
	 * Hooks to Joomla!'s earliest plugin handler
	 */
	public function onAfterInitialise()
	{
		$this->customAdminFolder();

		if ($this->isAdminAccessAttempt())
		{
			$this->checkCustomAdminFolder();
		}

		if ($this->isAdminLogout())
		{
			$this->setLogoutCookie();
		}
	}

	/**
	 * If the user is trying to access the custom admin folder set the necessary cookies and redirect them to the
	 * administrator page.
	 */
	protected function customAdminFolder()
	{
		$ip = AtsystemUtilFilter::getIp();

		// I couldn't detect the ip, let's stop here
		if (empty($ip) || ($ip == '0.0.0.0'))
		{
			return;
		}

		// Some user agents don't set a UA string at all
		if (!array_key_exists('HTTP_USER_AGENT', $_SERVER))
		{
			return;
		}

		$ua             = $this->app->client;
		$uaString       = $ua->userAgent;
		$browserVersion = $ua->browserVersion;

		$uaShort = str_replace($browserVersion, 'abcd', $uaString);

		$uri = JUri::getInstance();
		$db = $this->db;

		// We're not trying to access to the custom folder
		$folder = $this->cparams->getValue('adminlogindir');

		if (str_replace($uri->root(), '', trim($uri->current(), '/')) != $folder)
		{
			return;
		}

		JLoader::import('joomla.user.helper');

		$hash = JUserHelper::hashPassword($ip . $uaShort);

		$data = (object)array(
			'series'      => JUserHelper::genRandomPassword(64),
			'client_hash' => $hash,
			'valid_to'    => date('Y-m-d H:i:s', time() + 180)
		);

		$db->insertObject('#__admintools_cookies', $data);

		$config = JFactory::getConfig();
		$cookie_domain = $config->get('cookie_domain', '');
		$cookie_path = $config->get('cookie_path', '/');
		$isSecure = $config->get('force_ssl', 0) ? true : false;

		setcookie('admintools', $data->series, time() + 180, $cookie_path, $cookie_domain, $isSecure, true);
		setcookie('admintools_logout', null, 1, $cookie_path, $cookie_domain, $isSecure, true);

		$uri->setPath(str_replace($folder, 'administrator/index.php', $uri->getPath()));

		$this->app->redirect($uri->toString());
	}

	/**
	 * When the user is trying to access the administrator folder without being logged in make sure they had already
	 * entered the custom administrator folder before coming here. Otherwise they are unauthorised and must be booted to
	 * the site's front-end page.
	 */
	protected function checkCustomAdminFolder()
	{
		// Initialise
		$seriesFound = false;
		$db = $this->db;

		// Get the series number from the cookie
		$series = $this->input->cookie->get('admintools', null);

		// If we are told that this is a user logging out redirect them to the front-end home page, do not log a
		// security exception, expire the cookie
		$logout = $this->input->cookie->get('admintools_logout', null, 'string');
		if ($logout == '!!!LOGOUT!!!')
		{
			$config = JFactory::getConfig();
			$cookie_domain = $config->get('cookie_domain', '');
			$cookie_path = $config->get('cookie_path', '/');
			$isSecure = $config->get('force_ssl', 0) ? true : false;
			setcookie('admintools_logout', null, 1, $cookie_path, $cookie_domain, $isSecure, true);

			$this->redirectAdminToHome();

			return;
		}

		// Do we have a series?
		$isValid = !empty($series);

		// Does the series exist in the db? If so, load it
		if ($isValid)
		{
			$query = $db->getQuery(true)
				->select('*')
				->from($db->qn('#__admintools_cookies'))
				->where($db->qn('series') . ' = ' . $db->q($series));
			$db->setQuery($query);
			$storedData = $db->loadObject();

			$seriesFound = true;

			if (!is_object($storedData))
			{
				$isValid = false;
				$seriesFound = false;
			}
		}

		// Is the series still valid or did someone manipulate the cookie expiration?
		if ($isValid)
		{
			$jValid = strtotime($storedData->valid_to);

			if ($jValid < time())
			{
				$isValid = false;
			}
		}

		// Does the UA match the stored series?
		if ($isValid)
		{
			$ip = AtsystemUtilFilter::getIp();

			$ua = $this->app->client;
			$uaString = $ua->userAgent;
			$browserVersion = $ua->browserVersion;

			$uaShort = str_replace($browserVersion, 'abcd', $uaString);

			$notSoSecret = $ip . $uaShort;

			JLoader::import('joomla.user.helper');

			$isValid = JUserHelper::verifyPassword($notSoSecret, $storedData->client_hash);
		}

		// Last check: session state variable
		if (JFactory::getSession()->get('adminlogindir', 0, 'com_admintools'))
		{
			$isValid = true;
		}

		// Delete the series cookie if found
		if ($seriesFound)
		{
			$query = $db->getQuery(true)
				->delete($db->qn('#__admintools_cookies'))
				->where($db->qn('series') . ' = ' . $db->q($series));
			$db->setQuery($query);
			$db->execute();
		}

		// Log an exception and redirect to homepage if we can't validate the user's cookie / session parameter
		if (!$isValid)
		{
			$this->exceptionsHandler->logAndAutoban('admindir');

			$this->redirectAdminToHome();

			return;
		}

		// Otherwise set the session parameter
		if ($seriesFound)
		{
			JFactory::getSession()->set('adminlogindir', 1, 'com_admintools');
		}
	}

	protected function setLogoutCookie()
	{
		$config = JFactory::getConfig();
		$cookie_domain = $config->get('cookie_domain', '');
		$cookie_path = $config->get('cookie_path', '/');
		$isSecure = $config->get('force_ssl', 0) ? true : false;

		setcookie('admintools_logout', '!!!LOGOUT!!!', time() + 180, $cookie_path, $cookie_domain, $isSecure, true);
	}

	/**
	 * Checks if a user is trying to log out
	 *
	 * @return bool
	 */
	protected function isAdminLogout()
	{
		// Not back-end at all. Bail out.
		if (!$this->helper->isBackend())
		{
			return false;
		}

		// If the user is not already logged in we don't have a logout attempt
		$user = JFactory::getUser();

		if ($user->guest)
		{
			return false;
		}

		$input = $this->input;
		$option = $input->getCmd('option', null);
		$task = $input->getCmd('task', null);

		if (($option == 'com_login') && ($task == 'logout'))
		{
			return true;
		}

		// Check for malicious direct post without a valid token. In this case it's not a logout.
		JLoader::import('joomla.utiltiites.utility');
		$token = null;

		if (class_exists('JUtility'))
		{
			if (method_exists('JUtility', 'getToken'))
			{
				$token = JUtility::getToken();
			}
		}

		if (is_null($token))
		{
			$token = JFactory::getSession()->getToken();
		}

		$token = $this->input->get($token, false, 'raw');

		if (($token === false) && method_exists('JSession', 'checkToken'))
		{
			return JSession::checkToken('request');
		}

		return false;
	}
}