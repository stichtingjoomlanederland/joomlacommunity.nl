<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2017 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

class AtsystemFeatureIpblacklist extends AtsystemFeatureAbstract
{
	protected $loadOrder = 20;

	/** @var  string  Extra info to log when blocking an IP */
	private $extraInfo = null;

	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return ($this->cparams->getValue('ipbl', 0) == 1);
	}

	/**
	 * Filters visitor access by IP. If the IP of the visitor is included in the
	 * blacklist, she gets a 403 error
	 */
	public function onAfterInitialise()
	{
		if (!$this->isIPBlocked())
		{
			return;
		}

		$message = $this->cparams->getValue('custom403msg', '');

		if (empty($message))
		{
			$message = 'ADMINTOOLS_BLOCKED_MESSAGE';
		}

		// Merge the default translation with the current translation
		$jlang = JFactory::getLanguage();

		// Front-end translation
		$jlang->load('plg_system_admintools', JPATH_ADMINISTRATOR, 'en-GB', true);
		$jlang->load('plg_system_admintools', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
		$jlang->load('plg_system_admintools', JPATH_ADMINISTRATOR, null, true);

		// Do we have an override?
		$langOverride = $this->params->get('language_override', '');

		if (!empty($langOverride))
		{
			$jlang->load('plg_system_admintools', JPATH_ADMINISTRATOR, $langOverride, true);
		}

		$message = JText::_($message);

		if ($message == 'ADMINTOOLS_BLOCKED_MESSAGE')
		{
			$message = "Access Denied";
		}

		// Show the 403 message
		if ($this->cparams->getValue('use403view', 0))
		{
			// Using a view
			if (!$this->container->platform->getSessionVar('block', false, 'com_admintools') || $this->container->platform->isBackend())
			{
				// This is inside an if-block so that we don't end up in an infinite redirection loop
				$this->container->platform->setSessionVar('block', true, 'com_admintools');
				$this->container->platform->setSessionVar('message', $message, 'com_admintools');

				// Close the session (logs out the user)
				JFactory::getSession()->close();

				$base = JUri::base();

				if ($this->container->platform->isBackend())
				{
					$base = rtrim($base);
					$base = substr($base, 0, -13);
				}

				$this->container->platform->redirect($base);
			}

			return;
		}

		if ($this->container->platform->isBackend())
		{
			// You can't use Joomla!'s error page in the admin area. Improvise!
			header('HTTP/1.1 403 Forbidden');
			echo $message;

			$this->app->close();
		}

		// Using Joomla!'s error page
		throw new Exception($message, 403);
	}

	/**
	 * Is the IP blocked by a permanent IP blacklist rule?
	 *
	 * @param   string  $ip  The IP address to check. Skip or pass empty string / null to use the current visitor's IP.
	 *
	 * @return  bool
	 */
	public function isIPBlocked($ip = null)
	{
		if (empty($ip))
		{
			// Get the visitor's IP address
			$ip = AtsystemUtilFilter::getIp();
		}

		// Let's get a list of blocked IP ranges
		$db = $this->db;
		$sql = $db->getQuery(true)
		          ->select($db->qn('ip'))
		          ->from($db->qn('#__admintools_ipblock'));
		$db->setQuery($sql);

		try
		{
			$ipTable = $db->loadColumn();
		}
		catch (Exception $e)
		{
			// Do nothing if the query fails
			$ipTable = null;
		}

		if (empty($ipTable))
		{
			return false;
		}

		$inList = AtsystemUtilFilter::IPinList($ipTable, $ip);

		return ($inList === true);
	}
}