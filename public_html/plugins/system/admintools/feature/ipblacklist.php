<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

class AtsystemFeatureIpblacklist extends AtsystemFeatureAbstract
{
	protected $loadOrder = 20;

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
			return;
		}

		$inList = AtsystemUtilFilter::IPinList($ipTable);

		if ($inList !== true)
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
			$session = JFactory::getSession();

			// Using a view
			if (!$session->get('block', false, 'com_admintools') || $this->helper->isBackend())
			{
				// This is inside an if-block so that we don't end up in an infinite redirection loop
				$session->set('block', true, 'com_admintools');
				$session->set('message', $message, 'com_admintools');
				$session->close();

				$base = JUri::base();

				if ($this->helper->isBackend())
				{
					$base = rtrim($base);
					$base = substr($base, 0, -13);
				}

				$this->app->redirect($base);
			}

			return;
		}

		if ($this->helper->isBackend())
		{
			// You can't use Joomla!'s error page in the admin area. Improvise!
			header('HTTP/1.1 403 Forbidden');
			echo $message;

			$this->app->close();
		}

		// Using Joomla!'s error page
		throw new Exception($message, 403);
	}
}