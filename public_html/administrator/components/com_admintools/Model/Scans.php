<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Model;

defined('_JEXEC') or die;

use Akeeba\AdminTools\Admin\Model\Scanner\Crawler;
use Akeeba\AdminTools\Admin\Model\Scanner\Email;
use Akeeba\AdminTools\Admin\Model\Scanner\Logger\Logger;
use Akeeba\AdminTools\Admin\Model\Scanner\Part;
use Akeeba\AdminTools\Admin\Model\Scanner\Util\Configuration;
use Akeeba\AdminTools\Admin\Model\Scanner\Util\Session;
use FOF30\Container\Container;
use FOF30\Date\Date;
use FOF30\Model\DataModel;
use FOF30\Timer\Timer;

/**
 * @property    int      $id
 * @property    string   $comment
 * @property    string   $scanstart
 * @property    string   $scanend
 * @property    string   $status
 * @property    string   $origin
 * @property    int      $totalfiles
 *
 * @property-read    int $files_modified
 * @property-read    int $files_new
 * @property-read    int $files_suspicious
 *
 * @method  $this  comment()  comment(string $v)
 * @method  $this  scanstart()  scanstart(string $v)
 * @method  $this  scanend()  scanend(string $v)
 * @method  $this  status()  status(string $v)
 * @method  $this  origin()  origin(string $v)
 */
class Scans extends DataModel
{
	public function __construct(Container $container, array $config)
	{
		$config['tableName']   = '#__admintools_scans';
		$config['idFieldName'] = 'id';
		$config['autoChecks']  = false;

		parent::__construct($container, $config);

		$this->addBehaviour('Filters');

		$this->addKnownField('newfile');
		$this->addKnownField('files_new');
		$this->addKnownField('files_modified');
		$this->addKnownField('files_suspicious');
	}

	public function removeIncompleteScans()
	{
		/** @var Scans $model1 */
		$model1 = $this->container->factory->model('Scans')->tmpInstance();
		/** @var Scans $model2 */
		$model2 = $this->container->factory->model('Scans')->tmpInstance();

		/** @var DataModel\Collection $list1 */
		$list1 = $model1
			->status('fail')
			->get();

		$list2 = $model2
			->status('run')
			->get();

		$list = $list1->merge($list2);

		$list->delete();
	}

	/**
	 * Clears the table with files information
	 *
	 * @return  bool
	 */
	public function purgeFilesCache()
	{
		$db = $this->getDbo();

		// The best choice should be the TRUNCATE statement, however there isn't the proper function inside Joomla driver...
		$query = $db->getQuery(true)
			->delete($db->qn('#__admintools_filescache'));

		try
		{
			$result = $db->setQuery($query)->execute();
		}
		catch (\Exception $e)
		{
			$result = false;
		}

		return $result;
	}

	/**
	 * Starts a new file scan
	 *
	 * @return  array
	 */
	public function startScan($origin = 'backend')
	{
		if (function_exists('set_time_limit'))
		{
			@set_time_limit(0);
		}

		// Get the scanner engine's base objects (configuration, session storage and logger)
		$configuration = Configuration::getInstance();
		$session       = Session::getInstance();
		$logger        = new Logger($configuration);

		// Log the start of a new scan
		$logger->reset();
		$logger->info(sprintf("Admin Tools Professional %s (%s)", ADMINTOOLS_VERSION, ADMINTOOLS_DATE));
		$logger->info('PHP File Change Scanner');
		$logger->info('Starting a new scan from the “' . $origin . '” origin.');

		// Get a timer according to the engine's configuration
		$maxExec     = $configuration->get('maxExec');
		$runtimeBias = $configuration->get('runtimeBias');
		$logger->debug(sprintf("Getting a new operations timer, max. exec. time %0.2fs, runtime bias %u%%", $maxExec, $runtimeBias));
		$timer = new Timer($maxExec, $runtimeBias);

		// Reset the session. This marks a brand new scan.
		$logger->debug('Resetting the session storage');
		$session->reset();

		// Create a new scan record and save its ID in the session
		$logger->debug('Creating a new scan record');
		$currentTime = new Date();
		/** @var static $newScanRecord */
		$newScanRecord = $this->tmpInstance()->create([
			'scanstart'  => $currentTime->toSql(),
			'status'     => 'run',
			'origin'     => $origin,
			'totalfiles' => 0,
		]);
		$logger->debug(sprintf('Scan ID: %u', $newScanRecord->getId()));
		$session->set('scanID', $newScanRecord->getId());

		// Run the scanner engine
		$statusArray = $this->tickScannerEngine($configuration, $session, $logger, $timer, true);

		return $this->postProcessStatusArray($statusArray, $logger);
	}

	/**
	 * Steps the file scan
	 *
	 * @return  array
	 */
	public function stepScan()
	{
		// Get the scanner engine's base objects (configuration, session storage and logger)
		$configuration = Configuration::getInstance();
		$session       = Session::getInstance();
		$logger        = new Logger($configuration);

		// Get a timer according to the engine's configuration
		$maxExec     = $configuration->get('maxExec');
		$runtimeBias = $configuration->get('runtimeBias');
		$logger->debug(sprintf("Getting a new operations timer, max. exec. time %0.2fs, runtime bias %u%%", $maxExec, $runtimeBias));
		$timer = new Timer($maxExec, $runtimeBias);

		// Run the scanner engine
		$statusArray = $this->tickScannerEngine($configuration, $session, $logger, $timer, true);

		return $this->postProcessStatusArray($statusArray, $logger);
	}

	protected function onAfterGetItemsArray(&$resultArray)
	{
		// Don't process an empty list
		if (empty($resultArray))
		{
			return;
		}

		// Get the scan_id's and initialise the special fields
		$scanids = [];
		$map     = [];

		foreach ($resultArray as $index => &$row)
		{
			$scanids[]     = $row->id;
			$map[$row->id] = $index;

			$row->files_new      = 0;
			$row->files_modified = 0;
		}

		// Fetch the stats for the IDs at hand
		$ids = implode(',', $scanids);

		$db    = $this->getDbo();
		$query = $db->getQuery(true)
			->select([
				$db->qn('scan_id'),
				'(' . $db->qn('diff') . ' = ' . $db->q('') . ') AS ' . $db->qn('newfile'),
				'COUNT(*) AS ' . $db->qn('count'),
			])
			->from($db->qn('#__admintools_scanalerts'))
			->where($db->qn('scan_id') . ' IN (' . $ids . ')')
			->group([
				$db->qn('scan_id'),
				$db->qn('newfile'),
			]);

		$alertstats = $db->setQuery($query)->loadObjectList();

		$query = $db->getQuery(true)
			->select([
				$db->qn('scan_id'),
				'COUNT(*) AS ' . $db->qn('count'),
			])
			->from($db->qn('#__admintools_scanalerts'))
			->where($db->qn('scan_id') . ' IN (' . $ids . ')')
			->where($db->qn('threat_score') . ' > ' . $db->q('0'))
			->where($db->qn('acknowledged') . ' = ' . $db->q('0'))
			->group($db->qn('scan_id'));

		$suspiciousstats = $db->setQuery($query)->loadObjectList();

		// Update the $resultArray with the loaded stats
		if (!empty($alertstats))
		{
			foreach ($alertstats as $stat)
			{
				$idx = $map[$stat->scan_id];

				if ($stat->newfile)
				{
					$resultArray[$idx]->files_new = $stat->count;
				}
				else
				{
					$resultArray[$idx]->files_modified = $stat->count;
				}
			}
		}

		if (!empty($suspiciousstats))
		{
			foreach ($suspiciousstats as $stat)
			{
				$idx                                 = $map[$stat->scan_id];
				$resultArray[$idx]->files_suspicious = $stat->count;
			}
		}
	}

	protected function onAfterDelete($id)
	{
		$this->deleteScanAlerts($id);
	}

	protected function onBeforeSave(&$data)
	{
		// Let's remove all the fields created on the fly
		$fakeFields = ['newfile', 'files_new', 'files_modified', 'files_suspicious'];

		foreach ($fakeFields as $field)
		{
			// I can't use `isset` since if we have a null key the check will return false, but that
			// would cause an error during the update
			if (array_key_exists($field, $this->recordData))
			{
				unset($this->recordData[$field]);
			}
		}
	}

	private function deleteScanAlerts($scan_id)
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true)
			->delete('#__admintools_scanalerts')
			->where($db->qn('scan_id') . ' = ' . $db->q($scan_id));

		$db->setQuery($query)->execute();

		return true;
	}

	private function postProcessStatusArray(array $statusArray, Logger $logger)
	{
		// Get the current scan record
		$session       = Session::getInstance();
		$configuration = Configuration::getInstance();
		$scanID        = $session->get('scanID');
		$scanRecord    = $this->tmpInstance()->findOrFail($scanID);
		$currentTime   = new Date();
		$warnings      = $logger->getAndResetWarnings();

		// Apply common updates to the backup record
		$scanRecord->bind([
			'totalfiles' => $session->get('scannedFiles'),
			'scanend'    => $currentTime->toSql(),
		]);

		// More work to do
		if ($statusArray['HasRun'] && (empty($statusArray['Error'])))
		{
			$logger->debug('** More work necessary. Will resume in the next step.');

			$scanRecord->save([
				'status' => 'run',
			]);

			// Still have work to do
			return [
				'status'   => true,
				'done'     => false,
				'error'    => '',
				'warnings' => $warnings,
			];
		}

		// An error occurred
		if (!empty($statusArray['Error']))
		{
			$logger->debug('** An error occurred. The scan has died.');

			$scanRecord->save([
				'status' => 'fail',
			]);
			$session->reset();

			return [
				'status'   => false,
				'done'     => true,
				'error'    => $statusArray['Error'],
				'warnings' => $warnings,
			];
		}

		// Just finished
		// -- Send emails, if necessary
		if ($scanRecord->origin != 'backend')
		{
			$logger->debug('Finished scanning. Evaluating whether to send email with scan results.');
			$email = new Email($configuration, $session, $logger);
			$email->sendEmail();
		}

		$logger->debug('** This scan is now finished.');
		$scanRecord->save([
			'status' => 'complete',
		]);
		$session->reset();

		return [
			'status'   => true,
			'done'     => true,
			'error'    => '',
			'warnings' => $warnings,
		];
	}

	/**
	 * @param   Configuration  $configuration
	 * @param   Session        $session
	 * @param   Logger         $logger
	 * @param   Timer          $timer
	 * @param   bool           $enforceMinimumExecutionTime
	 *
	 * @return  array
	 *
	 * @since   5.4.0
	 */
	private function tickScannerEngine(Configuration $configuration, Session $session, Logger $logger, Timer $timer, $enforceMinimumExecutionTime = true)
	{
		// Get the crawler and step it while we have enough time left
		$crawler   = new Crawler($configuration, $session, $logger, $timer);
		$step      = $session->get('step', 0);
		$operation = 0;
		$logger->debug(sprintf('===== Starting Step #%u =====', ++$step));

		while (true)
		{
			$logger->debug(sprintf('----- Starting operation #%u -----', ++$operation));
			$statusArray = $crawler->tick();
			$logger->debug(sprintf('----- Finished operation #%u -----', $operation));

			// Did we run into an error?
			if ($crawler->getState() == Part::STATE_ERROR)
			{
				$logger->debug('The scanner engine has experienced an error.');

				break;
			}

			// Are we done?
			if ($crawler->getState() == Part::STATE_FINISHED)
			{
				$logger->debug('The scanner engine finished scanning your site.');

				break;
			}

			// Did we run out of time?
			if ($timer->getTimeLeft() <= 0)
			{
				$logger->debug('We are running out of time.');

				break;
			}

			// Is the Break Flag set?
			if ($session->get('breakFlag', false))
			{
				$logger->debug('The Break Flag is set.');

				break;
			}
		}

		$logger->debug(sprintf('===== Finished Step #%u =====', $step));

		// Reset the break flag
		$session->set('breakFlag', false);

		// Do I need to enforce the minimum execution time?
		if (!$enforceMinimumExecutionTime)
		{
			return $statusArray;
		}

		$minExec    = $configuration->get('minExec');
		$alreadyRun = $timer->getRunningTime();
		$waitTime   = $alreadyRun - $minExec;

		// Negative wait times mean that we shouldn't wait. Also, waiting for less than 10 msec is daft.
		if ($waitTime <= 0.01)
		{
			return $statusArray;
		}

		if (!function_exists('time_nanosleep'))
		{
			usleep(1000000 * $waitTime);

			return $statusArray;
		}

		$seconds    = floor($waitTime);
		$fractional = $waitTime - $seconds;
		time_nanosleep($seconds, $fractional * 1000000000);

		return $statusArray;
	}
}
