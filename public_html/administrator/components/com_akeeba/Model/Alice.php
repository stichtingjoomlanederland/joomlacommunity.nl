<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Backup\Admin\Model;

// Protect from unauthorized access
defined('_JEXEC') or die();

use AliceCoreKettenrad;
use AliceFactory;
use AliceUtilLogger;
use AliceUtilTempvars;
use FOF30\Model\Model;

/**
 * ALICE model, the log analuzer
 */
class Alice extends Model
{
	/**
	 * Executes the AJAX commands used to perform the log analysis
	 *
	 * @return array
	 */
	public function runAnalysis()
	{
		$ret_array = array();

		$ajaxTask = $this->getState('ajax');

		switch ($ajaxTask)
		{
			case 'start':
				$ret_array = $this->doStart();

				break;

			case 'step':
				$ret_array = $this->doStep();

				break;

			default:
				break;
		}

		return $ret_array;
	}

	/**
	 * Start the log analysis
	 *
	 * @return  array
	 */
	private function doStart()
	{
		$log      = $this->getState('log');
		$tag = 'alice';

		AliceUtilLogger::WriteLog(true);
		AliceUtilLogger::WriteLog(_AE_LOG_INFO, 'Starting analysis');

		AliceCoreKettenrad::reset(array(
			'maxrun' => 0
		));
		AliceUtilTempvars::reset($tag);

		$kettenrad = AliceCoreKettenrad::load($tag);

		$options = array('logToAnalyze' => \Akeeba\Engine\Factory::getLog()->getLogFilename($log));
		$kettenrad->setup($options);
		$kettenrad->tick();

		if (($kettenrad->getState() != 'running'))
		{
			$kettenrad->tick();
		}

		$ret_array = $kettenrad->getStatusArray();
		$kettenrad->resetWarnings(); // So as not to have duplicate warnings reports
		AliceCoreKettenrad::save($tag);

		return $ret_array;
	}

	/**
	 * Step the log analysis
	 *
	 * @return array
	 */
	private function doStep()
	{
		$tag = 'alice';

		$kettenrad = AliceCoreKettenrad::load($tag);
		$kettenrad->tick();
		$ret_array = $kettenrad->getStatusArray();
		$kettenrad->resetWarnings(); // So as not to have duplicate warnings reports
		AliceCoreKettenrad::save($tag);

		if ($ret_array['HasRun'] == 1)
		{
			// Let's get tests result
			$config   = AliceFactory::getConfiguration();
			$feedback = $config->get('volatile.alice.feedback');

			$ret_array['Results'] = json_encode($feedback);

			// Clean up
			AliceFactory::nuke();
			AliceUtilTempvars::reset($tag);

			return $ret_array;
		}

		return $ret_array;
	}

}
