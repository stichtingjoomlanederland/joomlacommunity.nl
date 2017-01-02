<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

class AtsystemFeatureCachecleaner extends AtsystemFeatureAbstract
{
	protected $loadOrder = 630;

	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return ($this->params->get('cachecleaner', 0) == 1);
	}

	public function onAfterInitialise()
	{
		$minutes = (int)$this->params->get('cache_freq', 0);

		if ($minutes <= 0)
		{
			return;
		}

		$lastJob = $this->getTimestamp('cache_clean');
		$nextJob = $lastJob + $minutes * 60;

		JLoader::import('joomla.utilities.date');
		$now = new JDate();

		if ($now->toUnix() >= $nextJob)
		{
			$this->setTimestamp('cache_clean');
			$this->purgeCache();
		}
	}

	/**
	 * Completely purges the cache
	 */
	private function purgeCache()
	{
		JLoader::import('joomla.application.helper');
		JLoader::import('joomla.cms.application.helper');

		// Site client
		$client = JApplicationHelper::getClientInfo(0);

		$er = @error_reporting(0);
		$cache = JFactory::getCache('');
		$cache->clean('sillylongnamewhichcantexistunlessyouareacompletelyparanoiddeveloperinwhichcaseyoushouldnotbewritingsoftwareokay', 'notgroup');
		@error_reporting($er);
	}
}