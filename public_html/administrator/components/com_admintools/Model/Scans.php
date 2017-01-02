<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Model;

defined('_JEXEC') or die;

use Akeeba\Engine\Factory;
use Akeeba\Engine\Finalization\Email;
use Akeeba\Engine\Platform;
use FOF30\Container\Container;
use FOF30\Model\DataModel;

/**
 * @property    int       $id
 * @property    string    $description
 * @property    string    $comment
 * @property    string    $backupstart
 * @property    string    $backupend
 * @property    string    $status
 * @property    string    $origin
 * @property    string    $type
 * @property    int       $profile_id
 * @property    string    $archivename
 * @property    string    $absolute_path
 * @property    int       $multipart
 * @property    string    $tag
 * @property    string    $backupid
 * @property    int       $filesexist
 * @property    string    $remote_filename
 * @property    int       $total_size
 *
 * @property-read    int       $files_modified
 * @property-read    int       $files_new
 * @property-read    int       $files_suspicious
 *
 * @method  $this  description()  description(string $v)
 * @method  $this  comment()  comment(string  $v)
 * @method  $this  backupstart()  backupstart(string $v)
 * @method  $this  backupend()  backupend(string $v)
 * @method  $this  status()  status(string $v)
 * @method  $this  origin()  origin(string $v)
 * @method  $this  type()  type(string $v)
 * @method  $this  profile_id()  profile_id(int $v)
 * @method  $this  tag()  tag(string $v)
 * @method  $this  backupid()  backupid(string $v)
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

	protected function onAfterGetItemsArray(&$resultArray)
	{
		// Don't process an empty list
		if (empty($resultArray))
		{
			return;
		}

		// Get the scan_id's and initialise the special fields
		$scanids = array();
		$map     = array();

		foreach ($resultArray as $index => &$row)
		{
			$scanids[]       = $row->id;
			$map[ $row->id ] = $index;

			$row->files_new      = 0;
			$row->files_modified = 0;
		}

		// Fetch the stats for the IDs at hand
		$ids = implode(',', $scanids);

		$db    = $this->getDbo();
		$query = $db->getQuery(true)
		            ->select(array(
			            $db->qn('scan_id'),
			            '(' . $db->qn('diff') . ' = ' . $db->q('') . ') AS ' . $db->qn('newfile'),
			            'COUNT(*) AS ' . $db->qn('count')
		            ))
		            ->from($db->qn('#__admintools_scanalerts'))
		            ->where($db->qn('scan_id') . ' IN (' . $ids . ')')
		            ->group(array(
			            $db->qn('scan_id'),
			            $db->qn('newfile'),
		            ));

		$alertstats = $db->setQuery($query)->loadObjectList();

		$query = $db->getQuery(true)
		            ->select(array(
			            $db->qn('scan_id'),
			            'COUNT(*) AS ' . $db->qn('count')
		            ))
		            ->from($db->qn('#__admintools_scanalerts'))
		            ->where($db->qn('scan_id') . ' IN (' . $ids . ')')
		            ->where('(' . $db->qn('threat_score') . ' > ' . $db->q('0') . ')')
		            ->group($db->qn('scan_id'));

		$suspiciousstats = $db->setQuery($query)->loadObjectList();

		// Update the $resultArray with the loaded stats
		if (!empty($alertstats))
		{
			foreach ($alertstats as $stat)
			{
				$idx = $map[ $stat->scan_id ];

				if ($stat->newfile)
				{
					$resultArray[ $idx ]->files_new = $stat->count;
				}
				else
				{
					$resultArray[ $idx ]->files_modified = $stat->count;
				}
			}
		}

		if (!empty($suspiciousstats))
		{
			foreach ($suspiciousstats as $stat)
			{
				$idx                                   = $map[ $stat->scan_id ];
				$resultArray[ $idx ]->files_suspicious = $stat->count;
			}
		}
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
			->profile_id(1)
			->get();

		$list2 = $model2
			->status('run')
			->profile_id(1)
			->get();

		$list = $list1->merge($list2);

		unset($list1);
		unset($list2);

		if (!empty($list))
		{
			$ids = array(- 1);

			foreach ($list as $item)
			{
				$ids[] = $item->id;
			}

			$ids = implode(',', $ids);

			$db = $this->getDbo();

			$query = $db->getQuery(true)
			            ->delete('#__admintools_scans')
			            ->where($db->qn('id') . ' IN (' . $ids . ')');

			$db->setQuery($query)->execute();

			$query = $db->getQuery(true)
			            ->delete('#__admintools_scanalerts')
			            ->where($db->qn('scan_id') . ' IN (' . $ids . ')');

			$db->setQuery($query)->execute();
		}
	}

	protected function onAfterDelete($id)
	{
		$this->deleteScanAlerts($id);
	}

	protected function onBeforeSave(&$data)
	{
		// Let's remove all the fields created on the fly
		$fakeFields = array('newfile', 'files_new', 'files_modified', 'files_suspicious');

		foreach ($fakeFields as $field)
		{
			// I can't use `isset` since if we have a null key the check will return false, but that
			// would cause an error during the update
			if (array_key_exists($field, $this->recordData))
			{
				unset($this->recordData[ $field ]);
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
	public function startScan()
	{
		if (!$this->scanEngineSetup())
		{
			return array(
				'status' => false,
				'error'  => 'Could not load the file scanning engine; please try reinstalling the component',
				'done'   => true
			);
		}

		Platform::getInstance()->load_configuration(1);
		Factory::resetState();
		Factory::getFactoryStorage()->reset(AKEEBA_BACKUP_ORIGIN);

		$configOverrides['volatile.core.finalization.action_handlers'] = array(
			new Email()
		);

		$configOverrides['volatile.core.finalization.action_queue']    = array(
			'remove_temp_files',
			'update_statistics',
			'update_filesizes',
			'apply_quotas',
			'send_scan_email'
		);

		// Apply the configuration overrides, please
		$platform                  = Platform::getInstance();
		$platform->configOverrides = $configOverrides;

		$kettenrad = Factory::getKettenrad();
		$options   = array(
			'description' => '',
			'comment'     => '',
			'jpskey'      => ''
		);
		$kettenrad->setup($options);

		Factory::getLog()->open(AKEEBA_BACKUP_ORIGIN);
		Factory::getLog()->log(true, '');

		$kettenrad->tick();
		$kettenrad->tick();

		Factory::saveState(AKEEBA_BACKUP_ORIGIN);

		return $this->parseScanArray($kettenrad->getStatusArray());
	}

	/**
	 * Steps the file scan
	 *
	 * @return  array
	 */
	public function stepScan()
	{
		if (!$this->scanEngineSetup())
		{
			return array(
				'status' => false,
				'error'  => 'Could not load the file scanning engine; please try reinstalling the component',
				'done'   => true
			);
		}

		Factory::loadState(AKEEBA_BACKUP_ORIGIN);

		$kettenrad = Factory::getKettenrad();

		$kettenrad->tick();

		Factory::saveState(AKEEBA_BACKUP_ORIGIN);

		return $this->parseScanArray($kettenrad->getStatusArray());
	}

	/**
	 * Sets up the environment to start or continue a file scan
	 *
	 * @return  bool
	 */
	private function scanEngineSetup()
	{
		// Load the Akeeba Engine autoloader
		define('AKEEBAENGINE', 1);
		require_once JPATH_ADMINISTRATOR . '/components/com_admintools/engine/Autoloader.php';

		// Load the platform
		Platform::addPlatform('filescan', JPATH_ADMINISTRATOR . '/components/com_admintools/platform/Filescan');

		// Load the engine configuration
		Platform::getInstance()->load_configuration(1);
		$this->aeconfig = Factory::getConfiguration();

		define('AKEEBA_BACKUP_ORIGIN', 'backend');

		// Unset time limits
		$safe_mode = true;

		if (function_exists('ini_get'))
		{
			$safe_mode = ini_get('safe_mode');
		}

		if (!$safe_mode && function_exists('set_time_limit'))
		{
			@set_time_limit(0);
		}

		return true;
	}

	private function parseScanArray($array)
	{
		$kettenrad = Factory::getKettenrad();
		$kettenrad->resetWarnings();

		if (($array['HasRun'] != 1) && (empty($array['Error'])))
		{
			// Still have work to do
			return array(
				'status' => true,
				'done'   => false,
				'error'  => ''
			);
		}
		elseif (!empty($array['Error']))
		{
			// Error!
			return array(
				'status' => false,
				'done'   => true,
				'error'  => $array['Error']
			);
		}
		else
		{
			// All done
			Factory::getFactoryStorage()->reset(AKEEBA_BACKUP_ORIGIN);

			return array(
				'status' => true,
				'done'   => true,
				'error'  => ''
			);
		}
	}
}