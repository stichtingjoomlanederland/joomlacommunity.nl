<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

class AtsystemFeatureCleantemp extends AtsystemFeatureAbstract
{
	protected $loadOrder = 650;

	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return ($this->params->get('cleantemp', 0) == 1);
	}

	public function onAfterInitialise()
	{
		$minutes = (int)$this->params->get('cleantemp_freq', 0);

		if ($minutes <= 0)
		{
			return;
		}

		$lastJob = $this->getTimestamp('clean_temp');
		$nextJob = $lastJob + $minutes * 60;

		JLoader::import('joomla.utilities.date');
		$now = new JDate();

		if ($now->toUnix() >= $nextJob)
		{
			$this->setTimestamp('clean_temp');
			$this->tempDirectoryCleanup();
		}
	}

	/**
	 * Cleans up the temporary director
	 */
	private function tempDirectoryCleanup()
	{
		if (!defined('FOF30_INCLUDED') && !@include_once(JPATH_LIBRARIES . '/fof30/include.php'))
		{
			// FOF 3.0 is not installed
			return;
		}

		$container = \FOF30\Container\Container::getInstance('com_admintools');
		
		try
		{
			/** @var \Akeeba\AdminTools\Admin\Model\CleanTempDirectory $model */
			$model = $container->factory->model('CleanTempDirectory')->tmpInstance();
			
			// This also runs the first batch of deletions
			$model->startScanning();

			// and this runs more deletions until the time is up
			$model->run();
		}
		catch (Exception $e)
		{
			// Avoid any blank page on error
		}
	}
}