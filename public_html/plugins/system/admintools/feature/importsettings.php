<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\AdminTools\Admin\Model\ImportAndExport;
use FOF30\Date\Date;
use FOF30\Download\Download;

defined('_JEXEC') or die;

class AtsystemFeatureImportsettings extends AtsystemFeatureAbstract
{
	protected $loadOrder = 660;

	private $remote_url = '';
	private $freq = 0;

	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		$this->remote_url = $this->params->get('autoimport_url', '');
		$this->freq       = $this->params->get('autoimport_freq', 0);

		// Do not run if we don't have an URL or a frequency set
		return ($this->remote_url && ($this->freq > 0));
	}

	/**
	 * Run the settings import  on a schedule
	 */
	public function onAfterInitialise()
	{
		$lastJob = $this->getTimestamp('autoimport_settings');
		$nextJob = $lastJob + $this->freq * 60 * 60;

		$now = new Date();

		if ($now->toUnix() >= $nextJob)
		{
			$this->setTimestamp('autoimport_settings');
			$this->importSettings();
		}
	}

	/**
	 * Actually imports settings file from a remote URL
	 */
	private function importSettings()
	{
		$download = new Download($this->container);

		// Triple check we actually have an adapter that can be used
		if (!$download->getAdapterName())
		{
			return;
		}

		$settings = $download->getFromURL($this->remote_url);

		// Something happened during the download, simply ignore it to avoid the site to crash
		if (!$settings)
		{
			return;
		}

		/** @var ImportAndExport $importModel */
		$importModel = $this->container->factory->model('ImportAndExport')->tmpInstance();

		try
		{
			$importModel->importData($settings);
		}
		catch (Exception $e)
		{
			// Do not die if anything goes wrong (ie bad or invalid settings file)
		}
	}
}
