<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\AdminTools\Admin\Model\DatabaseTools;
use FOF40\Container\Container;
use FOF40\Date\Date;

defined('_JEXEC') || die;

class AtsystemFeatureSessioncleaner extends AtsystemFeatureAbstract
{
	protected $loadOrder = 610;

	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return ($this->params->get('sescleaner', 0) == 1);
	}

	/**
	 * Run the session cleaner (garbage collector) on a schedule
	 */
	public function onAfterInitialise()
	{
		$minutes = (int) $this->params->get('ses_freq', 0);

		if ($minutes <= 0)
		{
			return;
		}

		$lastJob = $this->getTimestamp('session_clean');
		$nextJob = $lastJob + $minutes * 60;

		$now = new Date();

		if ($now->toUnix() >= $nextJob)
		{
			$this->setTimestamp('session_clean');
			$this->purgeSession();
		}
	}

	/**
	 * Purges expired sessions
	 */
	private function purgeSession()
	{
		if (!defined('FOF40_INCLUDED') && !@include_once(JPATH_LIBRARIES . '/fof40/include.php'))
		{
			// This extension requires FOF 4.
			return;
		}

		$container = Container::getInstance('com_admintools');

		try
		{
			/** @var DatabaseTools $model */
			$model = $container->factory->model('DatabaseTools')->tmpInstance();

			// This also runs the first batch of deletions
			$model->garbageCollectSessions();
		}
		catch (Throwable $e)
		{
			// Avoid any blank page on error
		}
	}
}
