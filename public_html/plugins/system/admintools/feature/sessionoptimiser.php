<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

class AtsystemFeatureSessionoptimiser extends AtsystemFeatureAbstract
{
	protected $loadOrder = 600;

	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return ($this->params->get('sesoptimizer', 0) == 1);
	}

	public function onAfterInitialise()
	{
		$minutes = (int)$this->params->get('sesopt_freq', 0);

		if ($minutes <= 0)
		{
			return;
		}

		$lastJob = $this->getTimestamp('session_optimize');
		$nextJob = $lastJob + $minutes * 60;

		JLoader::import('joomla.utilities.date');
		$now = new JDate();

		if ($now->toUnix() >= $nextJob)
		{
			$this->setTimestamp('session_optimize');
			$this->sessionOptimize();
		}
	}

	/**
	 * Optimizes the session table. The idea is that as users log in and out,
	 * vast amounts of records are created and deleted, slowly fragmenting the
	 * underlying database file and slowing down user session operations. At
	 * some point, your site might even crash. By doing a periodic optimization
	 * of the sessions table this is prevented. An optimization per hour should
	 * be adequate, even for huge sites.
	 *
	 * Note: this is not necessary if you're not using the database to save
	 * session data. Using disk files, memcache, APC or other alternative caches
	 * has no impact on your database performance. In this case you should not
	 * enable this option, as you have nothing to gain.
	 */
	private function sessionOptimize()
	{
		$db = $this->db;

		// First, make sure this is MySQL!
		$dbClass = get_class($db);

		if (substr($dbClass, 0, 15) == 'JDatabaseDriver')
		{
			$dbClass = substr($dbClass, 15);
		}
		else
		{
			$dbClass = str_replace('JDatabase', '', $dbClass);
		}

		if (!in_array(strtolower($dbClass), array('mysql', 'mysqli')))
		{
			return;
		}

		$db->setQuery('CHECK TABLE ' . $db->quoteName('#__session'));
		$result = $db->loadObjectList();

		$isOK = false;

		if (!empty($result))
		{
			foreach ($result as $row)
			{
				if (($row->Msg_type == 'status') && (
						($row->Msg_text == 'OK') ||
						($row->Msg_text == 'Table is already up to date')
					)
				)
				{
					$isOK = true;
				}
			}
		}

		// Run a repair only if it is required
		if (!$isOK)
		{
			// The table needs repair
			$db->setQuery('REPAIR TABLE ' . $db->quoteName('#__session'));
			$db->execute();
		}

		// Finally, optimize
		$db->setQuery('OPTIMIZE TABLE ' . $db->quoteName('#__session'));
		$db->execute();
	}
}