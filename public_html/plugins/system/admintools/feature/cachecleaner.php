<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use FOF40\Date\Date;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;

defined('_JEXEC') || die;

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
		$minutes = (int) $this->params->get('cache_freq', 0);

		if ($minutes <= 0)
		{
			return;
		}

		$lastJob = $this->getTimestamp('cache_clean');
		$nextJob = $lastJob + $minutes * 60;

		$now = new Date();

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
		// Site client
		$client = class_exists('Joomla\\CMS\\Application\\ApplicationHelper') ? ApplicationHelper::getClientInfo(0) : ApplicationHelper::getClientInfo(0);

		$er    = @error_reporting(0);
		$cache = Factory::getCache('');
		$cache->clean('sillylongnamewhichcantexistunlessyouareacompletelyparanoiddeveloperinwhichcaseyoushouldnotbewritingsoftwareokay', 'notgroup');
		@error_reporting($er);
	}
}
