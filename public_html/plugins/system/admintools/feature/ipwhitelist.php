<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

class AtsystemFeatureIpwhitelist extends AtsystemFeatureAbstract
{
	protected $loadOrder = 50;

	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		if (!$this->isAdminAccessAttempt())
		{
			return false;
		}

		return ($this->cparams->getValue('ipwl', 0) == 1);
	}

	/**
	 * Filters back-end access by IP. If the IP of the visitor is not included
	 * in the whitelist, he gets redirected to the home page
	 */
	public function onAfterInitialise()
	{
		// Let's get a list of allowed IP ranges
		$db = $this->db;
		$sql = $db->getQuery(true)
			->select($db->qn('ip'))
			->from($db->qn('#__admintools_adminiplist'));
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

		if ($inList === false)
		{
			if (!$this->exceptionsHandler->logAndAutoban('ipwl'))
			{
				return;
			}

			$this->redirectAdminToHome();
		}
	}
}