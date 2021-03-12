<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use FOF40\Date\Date;
use Joomla\CMS\Factory;

defined('_JEXEC') || die;

class AtsystemFeatureCacheexpire extends AtsystemFeatureAbstract
{
	protected $loadOrder = 640;

	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return ($this->params->get('cacheexpire', 0) == 1);
	}

	public function onAfterInitialise()
	{
		$minutes = (int) $this->params->get('cacheexp_freq', 0);

		if ($minutes <= 0)
		{
			return;
		}

		$lastJob = $this->getTimestamp('cache_expire');
		$nextJob = $lastJob + $minutes * 60;

		$now = new Date();

		if ($now->toUnix() >= $nextJob)
		{
			$this->setTimestamp('cache_expire');
			$this->expireCache();
		}
	}

	/**
	 * Expires cache items
	 */
	private function expireCache()
	{
		$er    = @error_reporting(0);
		$cache = Factory::getCache('');
		$cache->gc();
		@error_reporting($er);
	}
}
