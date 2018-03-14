<?php

/**
 * @package   Blue Flame Network (bfNetwork)
 * @copyright Copyright (C) 2011, 2012, 2013, 2014, 2015, 2016, 2017 Blue Flame Digital Solutions Ltd. All rights reserved.
 * @license   GNU General Public License version 3 or later
 * @link      https://myJoomla.com/
 * @author    Phil Taylor / Blue Flame Digital Solutions Limited.
 *
 * bfNetwork is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * bfNetwork is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this package.  If not, see http://www.gnu.org/licenses/
 */

/**
 * Class bfAudit
 */
final class bfAudit
{
	/**
	 *
	 * @var bfTimer Our timer class
	 */
	public $_timer;
	/**
	 * @var JDatabaseMysql The database Connector
	 */
	private $db;
	/**
	 * @var array
	 */
	private $_encryptedAndSuspectIds = array();
	/**
	 * @var array
	 */
	private $_encryptedIds = array();
	/**
	 * @var array
	 */
	private $_suspectIds = array();
	/**
	 * @var array
	 */
	private $_uploaderIds = array();
	/**
	 * @var array
	 */
	private $_mailerIds = array();
	/**
	 * @var array
	 */
	private $_notencryptedAndSuspectIds = array();
	/**
	 * @var
	 */
	private $alreadyAddedRootDirs = FALSE;
	/**
	 * @var
	 */
	private $foundDirs;
	/**
	 * @var
	 */
	private $foundFiles;
	/**
	 * @var
	 */
	private $suspectfiles;
	/**
	 * @var bool
	 */
	private $noMoreFoldersToScan = FALSE;
	/**
	 * @var bool
	 */
	private $noMoreFilesToScan = FALSE;
	/**
	 * @var bool
	 */
	private $deepscancomplete = FALSE;
	/**
	 * @var int
	 */
	private $tickOver = 0;
	/**
	 * @var
	 */
	private $startTime;
	/**
	 * @var
	 */
	private $endTime;
	/**
	 * @var
	 */
	private $version;
	/**
	 * @var
	 */
	private $platform;
	/**
	 * @var
	 */
	private $scancomplete;
	/**
	 * @var
	 */
	private $foundRecentlyModifiedFilesTotal;
	/**
	 * @var
	 */
	private $hashfailedcount;
	/**
	 * @var int
	 */
	private $step;
	/**
	 * @var
	 */
	private $connectorversion;
	/**
	 * @var
	 */
	private $files_777;
	/**
	 * @var
	 */
	private $hacked;
	/**
	 * @var
	 */
	private $zerobytes;
	/**
	 * @var
	 */
	private $folders_777;
	/**
	 * @var
	 */
	private $hidden_folders;
	/**
	 * @var
	 */
	private $hidden_files;
	/**
	 * @var
	 */
	private $renamedtohidefiles;
	/**
	 * @var
	 */
	private $nestedinstalls;
	/**
	 * @var
	 */
	private $error_logs_seen;
	/**
	 * @var
	 */
	private $encrypted_files;
	/**
	 * @var
	 */
	private $large_files;
	/**
	 * @var
	 */
	private $has_robots_modified;
	/**
	 * @var
	 */
	private $user_hasdefaultuserids;
	/**
	 * @var
	 */
	private $archive_files;
	/**
	 * @var
	 */
	private $htaccess_files;
	/**
	 * @var
	 */
	private $phpiniseen;
	/**
	 * @var
	 */
	private $uploader;
	/**
	 * @var
	 */
	private $mailer;
	/**
	 * @var
	 */
	private $max_allowed_packet;
	/**
	 * @var
	 */
	private $phpinwrongplace;
	/**
	 * @var
	 */
	private $notcorefiles;
	/**
	 * @var
	 */
	private $missingcorefiles;
	/**
	 * @var
	 */
	private $modifiedfilessincelastaudit;

	/**
	 * Set up the audit, reading from cached state if needed
	 * Also handles the uploading of the scanner config
	 *
	 * @param stdClass $request The decrypted request
	 */
	public function __construct($request)
	{
		$this->_cleanUpStuff();

		bfLog::log(_BF_SPEED);

		if (_BF_API_DEBUG === TRUE)
		{
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
		}

		// Check that the permissions are set correctly before proceeding
		$this->_checkOurPerms();

		// Connect to the database
		$this->initDb();

		// init Joomla
		require 'bfInitJoomla.php';

		/**
		 * Should we abandon/clear the current audit and restart
		 *
		 * If this is the first time we are running then also reset
		 */
		if ((property_exists($request, 'forceRestart') && @$request->forceRestart) || (file_exists('./FIRSTRUN') && TRUE === _BF_CONFIG_RESET_STATE_ON_UPGRADE))
		{

			// reset the state
			$this->resetState();
		}

		// remove the trigger for the first run
		if (file_exists('./FIRSTRUN'))
		{
			@unlink('./FIRSTRUN');
		}

		// If there is a non encrypted md5's file then import it to the db
		if (property_exists($request, 'NOTENCRYPTED') && array_key_exists('md5s', $request->NOTENCRYPTED))
		{

			// clean up first
			$this->db->setQuery('TRUNCATE bf_core_hashes');
			$this->db->query();

			$url = base64_decode($request->NOTENCRYPTED ['md5s']);

			$options = array(
				'http' => array(
					'method' => "GET",
					'header' => "Accept-language: en\r\n" .
						"User-Agent: " . $_SERVER['HTTP_HOST'] . "\r\n"
				)
			);

			$context = stream_context_create($options);

			// get the data from the request
			$data = file_get_contents($url, FALSE, $context);

			// F.M.L - I hate crap servers!
			if (!$data)
			{

				$ch = curl_init();

				// Set up bare minimum CURL Options needed for myJoomla.com
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
				curl_setopt($ch, CURLOPT_HEADER, FALSE);
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_HOST']);

				// Attempt to download using CURL and CURLOPT_SSL_VERIFYPEER set to TRUE
				$data = curl_exec($ch);

				// Did we succeed in getting something?????
				if (!$data)
				{

					/**
					 * ** CRAPPY SERVER ALERT ** CRAPPY SERVER ALERT ** CRAPPY SERVER ALERT ** CRAPPY SERVER ALERT **
					 *
					 * Ok try without validation of the SSL (gulp) but this is needed on some servers without a pem file
					 * and we need to be compatible as possible - even on crappy webhosts when they need us most ;-(
					 */
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

					//  Second Attempt to download using CURL and CURLOPT_SSL_VERIFYPEER set to FALSE (gulp)
					$data = curl_exec($ch);
				}

				curl_close($ch);
			}

			if (!$data)
			{
				bfEncrypt::reply(bfReply::ERROR, 'We could not download a required file from the CDN (Nothing downloaded!) - seek assistance from phil@phil-taylor.com');
			}

			if (!function_exists('gzinflate'))
			{
				bfEncrypt::reply(bfReply::ERROR, 'Your server doesnt meet the minimum requirements of Joomla - it has no gzinflate function in PHP!');
			}

			if (!gzinflate($data))
			{
				bfEncrypt::reply(bfReply::ERROR, 'We could not download and inflate a required file from the CDN (Something wrong with the downloaded data or gzinflate of that data) - seek assistance from phil@phil-taylor.com');
			}

			$dataLines = explode("\n", gzinflate($data));

			// Import the md5s to the database - easier to query a db than a
			// single file
			$sql    = 'INSERT INTO bf_core_hashes (filewithpath, hash) VALUES ';
			$values = array();
			foreach ($dataLines as $line)
			{
				$parts = explode("\t", $line);

				// Do it this way for speed, 1 query instead of 4000+ queries!
				$values [] = sprintf('("/%s", "%s")', $parts [0], $parts [1]);
			}

			// import now!
			$this->db->setQuery($sql . implode(' , ', $values));
			$this->db->query();

			// memory cleanup
			unset ($parts);
			unset ($dataLines);
			unset ($data);
		}

		// get the base bfnetwork folder
		$base = dirname(__FILE__);

		// Save our patterns to a file
		if (property_exists($request, 'NOTENCRYPTED') && array_key_exists('pattern', $request->NOTENCRYPTED))
		{
			bfLog::log('Saving audit pattern config');

			$url = base64_decode($request->NOTENCRYPTED ['pattern']);

			$options = array(
				'http' => array(
					'method' => "GET",
					'header' => "Accept-language: en\r\n" .
						"User-Agent: " . $_SERVER['HTTP_HOST'] . "\r\n"
				)
			);

			$context = stream_context_create($options);

			// get the data from the request
			$patterns = file_get_contents($url, FALSE, $context);

			// F.M.L - I hate crap servers!
			if (!$patterns)
			{

				$ch = curl_init();

				// Set up bare minimum CURL Options needed for myJoomla.com
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
				curl_setopt($ch, CURLOPT_HEADER, FALSE);
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_HOST']);

				// Attempt to download using CURL and CURLOPT_SSL_VERIFYPEER set to TRUE
				$patterns = curl_exec($ch);

				// Did we succeed in getting something?????
				if (!$patterns)
				{

					/**
					 * ** CRAPPY SERVER ALERT ** CRAPPY SERVER ALERT ** CRAPPY SERVER ALERT ** CRAPPY SERVER ALERT **
					 *
					 * Ok try without validation of the SSL (gulp) but this is needed on some servers without a pem file
					 * and we need to be compatible as possible - even on crappy webhosts when they need us most ;-(
					 */
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

					//  Second Attempt to download using CURL and CURLOPT_SSL_VERIFYPEER set to FALSE (gulp)
					$patterns = curl_exec($ch);
				}

				curl_close($ch);
			}

			if (!$patterns)
			{
				bfEncrypt::reply(bfReply::ERROR, 'We could not download a required file from the CDN (Nothing downloaded!) - seek assistance from phil@phil-taylor.com');
			}

			if (FALSE === file_put_contents($base . '/tmp/tmp.pattern', $patterns))
			{
				bfEncrypt::reply(bfReply::ERROR, 'Could not save audit patterns to ' . $base . '/tmp/tmp.pattern');
			}

			// finally as a last ditch attempt - ensure its worth running an audit!
			if (!filesize($base . '/tmp/tmp.pattern'))
			{
				bfEncrypt::reply(bfReply::ERROR, 'We have no audit config to run with - this is fatal - seek assistance!');
			}

		}

		if (property_exists($request, 'NOTENCRYPTED') && array_key_exists('config', $request->NOTENCRYPTED))
		{

			// just in case
			if (!is_writable($base . '/bfConfig.php'))
			{
				@chmod($base . '/bfConfig.php', 0777);
			}

			// write config to file
			file_put_contents($base . '/bfConfig.php', gzinflate(base64_decode($request->NOTENCRYPTED ['config'])));

			// reset permissions to be more secure
			@chmod($base . '/bfConfig.php', 0644);
		}

		// reset permissions - just to be sure!
		@chmod('tmp/', 0755);

		// remove all the request
		unset ($request);

		bfLog::log('Waking the lab rats from their sleep...');

		// Get the current status from the database
		$this->wakeUp();

		// init the timer
		bfLog::log('Priming the lab rats with a timer...');
		$this->_timer = bfTimer::getInstance();

		// init the steps
		bfLog::log('Teaching the lab rats to dance...');
		$this->_steps = new STEP ($this->step);

		// belt and braces - check we have a step
		if (!$this->step)
		{
			$this->step = STEP::TESTCONNECTION;
		}
	}

	/**
	 * Remove all the fluff that we need to,
	 * Including old crap that we used to have installed and we dont need any longer
	 */
	private function _cleanUpStuff()
	{

	}

	/**
	 * Checks and sets permissions on files/folders as tight as we can be
	 * depending on the environment this script is running in
	 * - I dont want 0777 but sometimes its required on some stupid environments
	 * :-(
	 */
	private function _checkOurPerms()
	{
		// attempt to ensure our tmp folder is writable
		if (!is_writeable(dirname(__FILE__) . '/tmp'))
		{
			@chmod(dirname(__FILE__) . '/tmp', 0755);
		}

		// Argh!
		if (!is_writeable(dirname(__FILE__) . '/tmp'))
		{
			@chmod(dirname(__FILE__) . '/tmp', 0777);
		}

		// Give Up!
		if (!is_writeable(dirname(__FILE__) . '/tmp'))
		{
			bfEncrypt::reply(bfReply::ERROR, 'Our ' . dirname(__FILE__) . '/tmp folder on your site is not writable!');
		}

		// attempt to ensure our folder is writable
		if (!is_writeable(dirname(__FILE__)))
		{
			@chmod(dirname(__FILE__), 0755);
		}

		// Argh!
		if (!is_writeable(dirname(__FILE__)))
		{
			@chmod(dirname(__FILE__), 0777);
		}

		// Give Up!
		if (!is_writeable(dirname(__FILE__)))
		{
			bfEncrypt::reply(bfReply::ERROR, dirname(__FILE__) . '/ folder not writeable');
		}
	}

	/**
	 * Init the Joomla db connection
	 *
	 * @todo Rip this out and use our own database connection
	 */
	private function initDb()
	{
		bfLog::log('init database connection...');

		// require all we need to access Joomla API
		require 'bfInitJoomla.php';

		$this->db = JFactory::getDBO();

		// ok then, while were are here lets look up
		// the Joomla version we are in
		$VERSION       = new JVersion ();
		$this->version = $VERSION->getShortVersion();
	}

	/**
	 * Reset the state of our audit, cleaning files and database
	 */
	private function resetState()
	{
		bfLog::log('Creating our database tables');

		$this->db->setQuery('SHOW TABLES LIKE "bf_files_last"');
		if ($this->db->loadResult())
		{
			$this->db->setQuery('DROP TABLE IF EXISTS `bf_files_last`');
			$this->db->query();
		}

		$this->db->setQuery('SHOW TABLES LIKE "bf_files"');
		if ($this->db->loadResult())
		{
			$this->db->setQuery('RENAME TABLE `bf_files` TO `bf_files_last`');
			$this->db->query();
		}

		// Drop and recreate our database tables
		$sql  = file_get_contents('./db/blank.sql');
		$sqls = explode(';', $sql);
		foreach ($sqls as $sql)
		{
			if (trim($sql) != '')
			{
				$this->db->setQuery($sql);
				if (!$this->db->query())
				{
					bfEncrypt::reply(bfReply::ERROR, $this->db->getErrorMsg());
				}
			}
		}

		// remove any tmp files we might have created
		@unlink(dirname(__FILE__) . '/tmp/tmp.md5s');
		@unlink(dirname(__FILE__) . '/tmp/tmp.pattern');
		@unlink(dirname(__FILE__) . '/tmp/tmp.pattern.unenc');
		@unlink(dirname(__FILE__) . '/tmp/tmp.false');
		@unlink(dirname(__FILE__) . '/tmp/tmp.log');
		@unlink(dirname(__FILE__) . '/tmp/tmp.ob');
		@unlink(dirname(__FILE__) . '/tmp/large.sql');
		@unlink(dirname(__FILE__) . '/tmp/large1.sql');
		@unlink(dirname(__FILE__) . '/tmp/large2.sql');
		@unlink(dirname(__FILE__) . '/tmp/large3.sql');
		@unlink(dirname(__FILE__) . '/tmp/large4.sql');
		@unlink(dirname(__FILE__) . '/tmp/large5.sql');
		@unlink(dirname(__FILE__) . '/tmp/large6.sql');
		@unlink(dirname(__FILE__) . '/tmp/speedup.sql');
		@unlink(dirname(__FILE__) . '/tmp/STATE');
		@unlink(dirname(__FILE__) . '/tmp/STATE.php');
		@unlink(dirname(__FILE__) . '/tmp/Folders');
		@unlink(dirname(__FILE__) . '/tmp/Files');

		bfLog::truncate();
	}

	/**
	 * Wake up the audit from the state files
	 */
	public function wakeUp()
	{
		if (!file_exists('tmp/STATE.php'))
		{
			return FALSE;
		}

		// load state
		$result = unserialize(str_replace(array('<?php die();?>',
			'<? die();?>'), '', file_get_contents('tmp/STATE.php')));

		// Doh!
		if (!$result)
		{
			return;
		}

		// populate state into worker
		foreach ($result as $k => $v)
		{
			$this->$k = $v;
		}
	}

	/**
	 * Tick over
	 */
	public function tick()
	{
		if ($this->scancomplete != 1)
		{

			// init the start of the timer to prevent max time overruns
			if (!$this->startTime)
			{
				$this->startTime = time();
			}

			// increment the ticker, just shows how many ticks we have had
			$this->tickOver++;

			// Run the correct stepAction method
			$function = $this->_steps->getStepFunction($this->step);
			bfLog::log('Running method ' . $function);
			$this->$function ();

			// sleep and die
			bfLog::log('Sleeping in tick');
			$this->saveState(FALSE, __LINE__);
		}
		else
		{

			// Scan is already complete!
			bfLog::log('Sleeping as scan already complete');
			$this->saveState(TRUE, __LINE__);
		}
	}

	/**
	 * ZZZzzz......
	 * Sleep state to the database to provide session persistance
	 * We need a few seconds to run this :-(
	 *
	 * @param bool $alreadyComplete
	 */
	public function saveState($alreadyComplete = FALSE, $line = 0)
	{

		// bfLog::log('Sleeping audit status to persistent db store');

		// When did we complete this step/audit
		$this->endTime = time();

		// make sure we cache the connectorversion
		$this->connectorversion = file_get_contents('./VERSION');

		// Inject the state to the database
		$obj = new stdClass ();
		foreach ($this as $k => $v)
		{

			// Dont save private/system objects
			if ($k == 'db' || $k == '_steps' || $k == '_timer' || substr($k, 0, 1) == '_')
			{
				continue;
			}

			// convert objects and arrays to strings
			if (is_object($v) || is_array($v))
			{
				$v = json_encode($v);
			}

			// inject to the object we will return
			$obj->$k = $v;
		}

		// Save state
		file_put_contents('tmp/STATE.php', '<?php die();?>' . serialize($obj));

		// save the step we are on
		$obj->step = ( string ) $this->_steps;

		// report back to service with json object;
		$obj->maxPHPMemoryUsed = round((memory_get_peak_usage(TRUE) / 1048576), 2);

		$obj->queuecount = $this->_getQueueCount('files');

		// legacy
		$obj->filestoscan = $obj->queuecount;

		$obj->logtail = bfLog::getTail();

		// close db
		unset ($this->db);
		unset ($this->_timer);
		unset ($this->_steps);

		// go to sleep, but first tell the service we are dreaming...
		bfEncrypt::reply(bfReply::SUCCESS, $obj);
	}

	/**
	 * See whats left in the queue
	 *
	 * @return int The number of rows
	 */
	private function _getQueueCount($tbl)
	{
		$this->dbPing();
		$this->db->setQuery('SELECT count(*) FROM bf_' . $tbl . ' WHERE queued = 1');

		return $this->db->loadResult();
	}

	private function dbPing()
	{
		bfLog::log('   == 1 pinging to the db with class ');
		if ($this->db === NULL)
		{
			$this->db = JFactory::getDbo();
		}

		if (!$this->db->connected())
		{
			if (method_exists($this->db, 'getConnection'))
			{
				switch (get_class($this->db->getConnection()))
				{
					case 'mysql':
						@mysql_ping($this->db->getConnection());
						break;
					case 'mysqli':
						mysqli_ping($this->db->getConnection());
						break;
				}
			}
			else
			{
				// Joomla 1.freaking.5
				switch (get_class($this->db->name))
				{
					case 'mysql':
						@mysql_ping($this->db->_resource);
						break;
					case 'mysqli':
						mysqli_ping($this->db->_resource);
						break;
				}
			}

		}
	}

	/**
	 * This simply adds the JPATH_BASE / folders to the scan queue
	 */
	public function scanningrootdirsAction()
	{

		// Add the root folder to the scan quque
		$this->addDirToScanQueue($this->getFolders(JPATH_BASE));

		// mark scan
		$this->alreadyAddedRootDirs = TRUE;

		// move to the next scan step
		$this->nextStepPlease();
	}

	/**
	 * Add a folder to the scan queue
	 *
	 * @param array $arr Array of folders to add to the queue
	 * @param int   $queued
	 *
	 * @return array
	 */
	private function addDirToScanQueue($arr, $queued = 1)
	{
		if (!count($arr))
		{
			return array();
		}
		// Update stats
		$this->foundDirs = $this->foundDirs + count($arr);

		bfLog::log('Adding ' . count($arr) . ' folders To the audit queue');

		// skip if no folders in the array
		if (FALSE === $arr)
		{
			return;
		}

		$parts = array();
		foreach ($arr as $folder)
		{

			// clean up
			$folder = $this->_cleanupFileFolderName($folder);
			$folder = trim(str_replace('\\', '/', $folder));
			$folder = str_replace('////', '/', $folder);

			// Dont allow blank or invalid folders
			if (!is_dir(JPATH_BASE . DIRECTORY_SEPARATOR . $folder)
				&& !is_dir(JPATH_BASE . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR)
				|| is_link(JPATH_BASE . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR)
				|| is_link(JPATH_BASE . DIRECTORY_SEPARATOR . $folder) || !$folder
			)
			{
				continue;
			}

			$perms = $this->_getFolderPerms($folder);

			$insertFolderToDb [] = " ( '" . addslashes($folder) . "', '" . $perms . "', " . $queued . ")";
		}

		if (count($insertFolderToDb))
		{
			$sqlprefix = "INSERT INTO bf_folders ( folderwithpath, folderinfo, queued) VALUES ";
			$sqlToRun  = $sqlprefix . implode(', ', $insertFolderToDb);

			if (strlen($sqlToRun) > 1048576)
			{
				$insertFolderToDb = $this->array_split($insertFolderToDb, 4);

				$sqlToRun1 = $sqlprefix . implode(', ', $insertFolderToDb[0]);
				bfLog::log('sql size 1= ' . strlen($sqlToRun1));
				$this->db->setQuery($sqlToRun1);
				$this->db->query();

				$sqlToRun2 = $sqlprefix . implode(', ', $insertFolderToDb[1]);
				bfLog::log('sql size 2= ' . strlen($sqlToRun2));
				$this->db->setQuery($sqlToRun2);
				$this->db->query();

				$sqlToRun3 = $sqlprefix . implode(', ', $insertFolderToDb[2]);
				bfLog::log('sql size 3= ' . strlen($sqlToRun3));
				$this->db->setQuery($sqlToRun3);
				$this->db->query();

				$sqlToRun4 = $sqlprefix . implode(', ', $insertFolderToDb[3]);
				bfLog::log('sql size 4= ' . strlen($sqlToRun4));
				$this->db->setQuery($sqlToRun4);
				$this->db->query();


			}
			else
			{
				$this->db->setQuery($sqlToRun);
				$this->db->query();
			}


		}

		return array();

	}

	/**
	 * Clean up a string, a path name
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	private function _cleanupFileFolderName($str)
	{
		$str = str_replace('////', '/', $str);
		$str = str_replace('///', '/', $str);
		$str = str_replace('//', '/', $str);
		$str = str_replace('\\/', '/', $str);
		$str = str_replace("\\t", '/t', $str);
		$str = str_replace("\/", '/', $str);

		return addslashes($str);
	}

	/**
	 * Clean up the folder name and then get the right perms
	 *
	 * @param $folder
	 *
	 * @return string
	 */
	private function _getFolderPerms($folder)
	{
		$folder = $this->ensureRooted($this->_cleanupFileFolderName($folder));
		$perms  = substr(decoct(fileperms($folder)), 2);

		return $perms;
	}

	/**
	 * Ensure that we are rooted to the JPATH_BASE
	 *
	 * @param string $folder
	 *            A filewithpath
	 *
	 * @return string
	 */
	private function ensureRooted($folder)
	{
		// This looks stupid to me, but I'm sure there was a reason I did this!?
		return JPATH_BASE . str_replace(JPATH_BASE, '', stripslashes($folder));
	}

	/**
	 * Spilt an array
	 *
	 * @param     $array
	 * @param int $pieces
	 *
	 * @return array
	 */
	private function array_split($array, $pieces = 2)
	{
		if ($pieces < 2)
			return array($array);
		$newCount = ceil(count($array) / $pieces);
		$a        = array_slice($array, 0, $newCount);
		$b        = $this->array_split(array_slice($array, $newCount), $pieces - 1);

		return array_merge(array($a), $b);
	}

	/**
	 * Function taken from Akeeba filesystem.php
	 *
	 * @copyright Copyright (c)2009 Nicholas K. Dionysopoulos
	 * @license   GNU GPL version 3 or, at your option, any later version
	 * @package   akeebaengine
	 * @version   Id: scanner.php 158 2010-06-10 08:46:49Z nikosdion
	 */
	private function getFolders($folder)
	{

		// Initialize variables
		$arr   = array();
		$false = FALSE;

		$folder = trim($folder);

		if (!is_dir($folder) && !is_dir($folder . DIRECTORY_SEPARATOR) || is_link($folder . DIRECTORY_SEPARATOR) || is_link($folder) || !$folder)
			return $false;

		if (@file_exists($folder . DIRECTORY_SEPARATOR . '.myjoomla.ignore.folder'))
		{
			return array();
		}

		$handle = @opendir($folder);
		if ($handle === FALSE)
		{
			$handle = @opendir($folder . DIRECTORY_SEPARATOR);
		}
		// If directory is not accessible, just return FALSE
		if ($handle === FALSE)
		{
			return $false;
		}

		while ((($file = @readdir($handle)) !== FALSE))
		{
			if (($file != '.') && ($file != '..') && (trim($file) != NULL))
			{
				$ds    = ($folder == '') || ($folder == DIRECTORY_SEPARATOR) || (@substr($folder, -1) == DIRECTORY_SEPARATOR) || (@substr($folder, -1) == DIRECTORY_SEPARATOR) ? '' : DIRECTORY_SEPARATOR;
				$dir   = trim($folder . $ds . $file);
				$isDir = @is_dir($dir);
				if ($isDir)
				{
					$fidledFolderName = preg_replace('#^' . JPATH_BASE . '#i', '', $folder . DIRECTORY_SEPARATOR . $file);
					$r                = $this->_cleanupFileFolderName($fidledFolderName);
					$arr []           = $r;
				}
			}
		}
		@closedir($handle);

		return $arr;
	}

	/**
	 * Set pointer to the next step
	 */
	private function nextStepPlease($alsoSleep = FALSE)
	{
		bfLog::log('Ticking over to the next step');
		$this->step = $this->_steps->nextStepPlease();
		if ($alsoSleep === TRUE)
		{
			$this->saveState(FALSE, __LINE__);
		}
	}

	/**
	 * @see http://davidwalsh.name/php-file-extension
	 *
	 * @param $file_name
	 *
	 * @return string
	 */
	function get_file_extension($file_name)
	{
		return substr(strrchr($file_name, '.'), 1);
	}

	/**
	 * dummy method
	 */
	private function requestscannerconfigAction()
	{
		$this->nextStepPlease();
	}

	/**
	 * I never get here unless all is done :)
	 */
	private function completeAction()
	{

		// Mark the audit as complete
		$this->scancomplete = 1;

		// cleanup
		@unlink('tmp/tmp.md5s');
		@unlink('tmp/tmp.pattern');
		@unlink('tmp/tmp.false');
		@unlink('tmp/Folders');
		@unlink('tmp/Files');

		bfLog::log('===== AUDIT COMPLETE =====');
	}

	/**
	 * @deprecated
	 *
	 * Get information about the datbaase
	 */
	private function dbinfoAction()
	{
		// move onto the next step
		$this->nextStepPlease();
	}

	/**
	 * Do we have any backup tables
	 *
	 * @return string
	 */
	private function _hasBakTables()
	{
		$config = JFactory::getApplication('site');
		$dbname = $config->getCfg('db', '');
		$this->db->setQuery("SHOW TABLES WHERE `Tables_in_{$dbname}` like 'bak_%'");

		return ($this->db->loadResult() ? 'TRUE' : 'FALSE');
	}

	/**
	 */
	private function testconnectionAction()
	{
		// Ask Joomla API for some settings
		$config = JFactory::getApplication('site');


		try
		{
			// Send an email to see if we received it... Tests if the Joomla Global Config mailer settings are correct.
			$mailer = JFactory::getMailer();
			$sender = array(
				$config->getCfg('mailfrom'),
				$config->getCfg('fromname'));

			$mailer->setSender($sender);
			$mailer->addRecipient('2349c0921eafe4877634489202c7d530@inbound.postmarkapp.com');
			$mailer->setSubject('Audit Mailer Test');

			$s        = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
			$protocol = substr(strtolower($_SERVER["SERVER_PROTOCOL"]), 0, strpos(strtolower($_SERVER["SERVER_PROTOCOL"]), "/")) . $s;
			$port     = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":" . $_SERVER["SERVER_PORT"]);
			$uri      = $protocol . "://" . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];
			$segments = explode('?', $uri, 2);
			$url      = $segments[0];
			$url      = str_replace(array('plugins/system/bfnetwork/bfAudit.php',
				'plugins/system/bfnetwork/bfnetwork/bfAudit.php'), '', $url);
			$mailer->setBody($url); // ONLY THE URL OF THE SITE IS SENT - NO OTHER DATA
			$mailer->Send();
		}
		catch (Exception $e)
		{
		}

		// move onto the next step
		$this->nextStepPlease();
	}

	/**
	 * @deprecated to snapshot
	 */
	private function compileextensionsAction()
	{
		$this->nextStepPlease();
	}


	/**
	 *
	 */
	private function verifyextensionsAction()
	{
		require 'bfExtensions.php';
		$ext                  = new bfExtensions();
		$this->extensionsjson = $ext->getExtensions();
		$this->nextStepPlease();
	}

	/**
	 * Report on the last 3 days worth of modified files, excluding ours
	 */
	private function lookingupmodifiedfilesAction()
	{
		$time = strtotime('-3 days', time());
		$sql  = "SELECT COUNT(*) FROM bf_files WHERE filemtime > '%s'
				AND filewithpath NOT LIKE '/plugins/system/bfnetwork%%'";
		$this->db->setQuery(sprintf($sql, $time));
		$this->foundRecentlyModifiedFilesTotal = $this->db->LoadResult();

		// move onto the next step
		$this->nextStepPlease();
	}

	/**
	 * Scan folders and save the files that we find in the database
	 * If we have saved large sql files that contain queries then run those and tick over
	 */
	private function initialscanningfilesAction()
	{
		if (file_exists('tmp/large.sql'))
		{
			bfLog::log('Running a cached LARGE SQL insert');
			$sql = file_get_contents('tmp/large.sql');
			if (trim($sql))
			{
				$this->db->setQuery($sql);
				$this->db->query();
			}
			unlink('tmp/large.sql');
			$this->saveState(FALSE, __LINE__);
		}
		if (file_exists('tmp/large1.sql'))
		{
			bfLog::log('Running a cached LARGE1 SQL insert');
			$sql = file_get_contents('tmp/large1.sql');
			if (trim($sql))
			{
				$this->db->setQuery($sql);
				$this->db->query();
			}
			unlink('tmp/large1.sql');
			$this->saveState(FALSE, __LINE__);
		}
		if (file_exists('tmp/large2.sql'))
		{
			bfLog::log('Running a cached LARGE2 SQL insert');
			$sql = file_get_contents('tmp/large2.sql');
			if (trim($sql))
			{
				$this->db->setQuery($sql);
				$this->db->query();
			}
			unlink('tmp/large2.sql');
			$this->saveState(FALSE, __LINE__);
		}
		if (file_exists('tmp/large3.sql'))
		{
			bfLog::log('Running a cached LARGE3 SQL insert');
			$sql = file_get_contents('tmp/large3.sql');
			if (trim($sql))
			{
				$this->db->setQuery($sql);
				$this->db->query();
			}
			unlink('tmp/large3.sql');
			$this->saveState(FALSE, __LINE__);
		}
		if (file_exists('tmp/large4.sql'))
		{
			bfLog::log('Running a cached LARGE4 SQL insert');
			$sql = file_get_contents('tmp/large4.sql');
			if (trim($sql))
			{
				$this->db->setQuery($sql);
				$this->db->query();
			}
			unlink('tmp/large4.sql');
			$this->saveState(FALSE, __LINE__);
		}
		if (file_exists('tmp/large5.sql'))
		{
			bfLog::log('Running a cached LARGE5 SQL insert');
			$sql = file_get_contents('tmp/large5.sql');
			if (trim($sql))
			{
				$this->db->setQuery($sql);
				$this->db->query();
			}
			unlink('tmp/large5.sql');
			$this->saveState(FALSE, __LINE__);
		}
		if (file_exists('tmp/large6.sql'))
		{
			bfLog::log('Running a cached LARGE6 SQL insert');
			$sql = file_get_contents('tmp/large6.sql');
			if (trim($sql))
			{
				$this->db->setQuery($sql);
				$this->db->query();
			}
			unlink('tmp/large6.sql');
			$this->saveState(FALSE, __LINE__);
		}

		// See how much is left
		$this->db->setQuery("SELECT COUNT(*) FROM bf_folders WHERE queued = 1");
		$totalLeft = $this->db->loadResult();

		// Nothing left so die
		if (!$totalLeft)
		{

			// Get all the files with core hash changes :-(
			$sql = 'SELECT f.id FROM bf_files AS f
            LEFT JOIN bf_core_hashes AS ch ON ch.filewithpath = f.filewithpath
             WHERE ch.hash != f.currenthash';

			$this->db->setQuery($sql);

			if (method_exists($this->db, 'loadColumn'))
			{
				$ids = $this->db->loadColumn();
			}
			else
			{
				$ids = $this->db->loadResultArray();
			}

			if (count($ids))
			{
				bfLog::log('Found ' . count($ids) . ' Core file hashes failed');
				$sql = 'UPDATE bf_files SET hashfailed = 1 WHERE id IN (' . implode(', ', $ids) . ')';
				file_put_contents('tmp/hashfailed.sql', $sql);
			}

			// set all the core file flags
			$sql = 'SELECT f.id FROM bf_files AS f
                    WHERE filewithpath IN(
                      SELECT filewithpath FROM bf_core_hashes
                    )';

			$this->db->setQuery($sql);
			if (method_exists($this->db, 'loadColumn'))
			{
				$ids = $this->db->loadColumn();
			}
			else
			{
				$ids = $this->db->loadResultArray();
			}

			if (count($ids))
			{
				bfLog::log('Matched ' . count($ids) . ' Core files');
				$sql = 'UPDATE bf_files SET iscorefile = 1 WHERE id IN (' . implode(', ', $ids) . ')';
				file_put_contents('tmp/corefiles.sql', $sql);
			}

			$this->noMoreFilesToScan = TRUE;
			$this->nextStepPlease(TRUE);
		}

		$removeFoldersFromQueueIds = array();

		// yes run the query again, allows for the while loop nicely, also only
		// loop while we have time
		while ($this->db->loadResult() > 0 && $this->_timer->getTimeLeft() > _BF_CONFIG_FILES_TIMER_ONE)
		{

			// ok so we have a load of folders...
			if (count($removeFoldersFromQueueIds))
			{
				$this->db->setQuery("SELECT id, folderwithpath FROM bf_folders WHERE queued = 1 AND id NOT IN (" . implode(', ', $removeFoldersFromQueueIds) . ") ORDER BY id ASC LIMIT " . _BF_CONFIG_FILES_COUNT_ONE);
			}
			else
			{
				$this->db->setQuery("SELECT id, folderwithpath FROM bf_folders WHERE queued = 1 ORDER BY id ASC LIMIT " . _BF_CONFIG_FILES_COUNT_ONE);
			}
			$dirs_to_scan = $this->db->loadObjectList();

			while (count($dirs_to_scan) && $this->_timer->getTimeLeft() > _BF_CONFIG_FILES_TIMER_ONE)
			{

				// sql values to imploe to the insert
				$sqlvalues = array();

				// get a diretory object to scan
				$dirToScanObj = array_pop($dirs_to_scan);

				// extract the folder
				$dirToScan = $dirToScanObj->folderwithpath;

				$dirToScan = str_replace('////', '/', $dirToScan);

				// remove this current dir form the scan queue - quickly incase we get into indefinite loop;
				//$this->removeFromQueue('folders', array($dirToScanObj->id));
				$removeFoldersFromQueueIds[] = $dirToScanObj->id;

				// Make sure we have a absolute path to the folder
				$dirToScanWithPath = JPATH_BASE . DIRECTORY_SEPARATOR . str_replace(JPATH_BASE, '', $dirToScan);

				$filesInThisFolder = $this->getFiles($dirToScanWithPath);
				bfLog::log('Found ' . count($filesInThisFolder) . ' files in ' . str_replace(JPATH_BASE, '', $dirToScan));

				// If there are any files, and we have time left
				if (count($filesInThisFolder) && $this->_timer->getTimeLeft() > _BF_CONFIG_FILES_TIMER_TWO)
				{

					// for each file then get the info
					foreach ($filesInThisFolder as $file)
					{

						// ok are we getting short of time yet?
						if ($this->_timer->getTimeLeft() <= _BF_CONFIG_FILES_TIMER_TWO)
						{

							$this->db->setQuery('/*6*/ INSERT    INTO    bf_files
                                (filewithpath, fileperms, filemtime, currenthash, size) VALUES '
								. implode(', ', $sqlvalues));
							if (!$this->db->query())
							{
								bfLog::log($this->db->getErrorMsg());
							}
							$this->removeFromQueue('folders', $removeFoldersFromQueueIds);
							$this->saveState(FALSE, __LINE__);
						}

						// Get the file Info...
						$fileInfo = $this->_getFileInfo($dirToScanWithPath . DIRECTORY_SEPARATOR . $file);

						// create the insert
						$sqlinsert = ' ("%s", "%s", "%s", "%s", "%s") ';

						// count
						$this->foundFiles++;

						// clean up the file path so that we always start from /
						// which is where configuration.php is
						$fileBase = str_replace(JPATH_BASE, '', $dirToScanWithPath . '/' . $file);
						$fileBase = $this->_cleanupFileFolderName($fileBase);

						// cache the insert so that we can insert many rows for performance
						$sqlvalues [] = sprintf($sqlinsert, $fileBase, $fileInfo ['perms'], $fileInfo ['mtime'], $fileInfo ['currenthash'], $fileInfo ['size']);
					}

					if (count($filesInThisFolder) > 200)
					{
						bfLog::log('Sleeping as we had more than 200 files in this folder... we are saving:  ' . count($filesInThisFolder));


						$sqlvaluesParts = $this->array_split($sqlvalues, 6);

						file_put_contents('tmp/large1.sql', '/*1*/ INSERT INTO bf_files (filewithpath, fileperms, filemtime, currenthash, size)
                        VALUES ' . implode(', ', $sqlvaluesParts[0]));
						file_put_contents('tmp/large2.sql', '/*2*/ INSERT INTO bf_files (filewithpath, fileperms, filemtime, currenthash, size)
                        VALUES ' . implode(', ', $sqlvaluesParts[1]));
						file_put_contents('tmp/large3.sql', '/*3*/ INSERT INTO bf_files (filewithpath, fileperms, filemtime, currenthash, size)
                        VALUES ' . implode(', ', $sqlvaluesParts[2]));
						file_put_contents('tmp/large4.sql', '/*4*/ INSERT INTO bf_files (filewithpath, fileperms, filemtime, currenthash, size)
                        VALUES ' . implode(', ', $sqlvaluesParts[3]));
						file_put_contents('tmp/large5.sql', '/*5*/ INSERT INTO bf_files (filewithpath, fileperms, filemtime, currenthash, size)
                        VALUES ' . implode(', ', $sqlvaluesParts[4]));
						file_put_contents('tmp/large6.sql', '/*6*/ INSERT INTO bf_files (filewithpath, fileperms, filemtime, currenthash, size)
                        VALUES ' . implode(', ', $sqlvaluesParts[5]));

						bfLog::log('Large SQL files stored for processing...');


						$this->removeFromQueue('folders', $removeFoldersFromQueueIds);
						$this->saveState(FALSE, __LINE__);
					}

					// Save to the database when we get short of time
					if ($this->_timer->getTimeLeft() <= _BF_CONFIG_FILES_TIMER_TWO)
					{
						$this->db->setQuery('/*5*/INSERT INTO bf_files
                         (filewithpath, fileperms, filemtime, currenthash, size) VALUES '
							. implode(', ', $sqlvalues));
						if (!$this->db->query())
						{
							bfEncrypt::reply(bfReply::ERROR, $this->db->getErrorMsg());
						}
						$sqlvalues = array();

						$this->removeFromQueue('folders', $removeFoldersFromQueueIds);
						$this->saveState(FALSE, __LINE__);
					}
				}

				// Save to the database
				if (is_array($sqlvalues) && count($sqlvalues))
				{
					$this->db->setQuery('/*7*/INSERT INTO bf_files
                         (filewithpath, fileperms, filemtime, currenthash, size) VALUES '
						. implode(', ', $sqlvalues));
					if (!$this->db->query())
					{
						bfEncrypt::reply(bfReply::ERROR, $this->db->getErrorMsg());
					}
					$sqlvalues = array();
				}

				// set up for the while loop again
				if (count($removeFoldersFromQueueIds))
				{
					$this->db->setQuery("SELECT count(*) FROM bf_folders WHERE queued = 1 AND id NOT IN (" . implode(', ', $removeFoldersFromQueueIds) . ")");
				}
				else
				{
					$this->db->setQuery("SELECT COUNT(*) FROM bf_folders WHERE queued = 1");
				}

				// are we nearly there yet?
				if ($this->_timer->getTimeLeft() <= _BF_CONFIG_FILES_TIMER_TWO)
				{
					$this->removeFromQueue('folders', $removeFoldersFromQueueIds);
					$this->saveState(FALSE, __LINE__);
				}
			}

		}
		$this->removeFromQueue('folders', $removeFoldersFromQueueIds);
	}

	/**
	 * Function taken from Akeeba filesystem.php
	 *
	 * Akeeba Engine
	 * The modular PHP5 site backup engine
	 *
	 * @copyright Copyright (c)2009 Nicholas K. Dionysopoulos
	 * @license   GNU GPL version 3 or, at your option, any later version
	 * @package   akeebaengine
	 * @version   Id: scanner.php 158 2010-06-10 08:46:49Z nikosdion
	 */
	private function getFiles($folder)
	{
		// Initialize variables
		$arr   = array();
		$false = FALSE;

		$folder = trim($folder);

		if (!is_dir($folder) && !is_dir($folder . DIRECTORY_SEPARATOR) || is_link($folder . DIRECTORY_SEPARATOR) || is_link($folder) || !$folder)
		{
			return $false;
		}

		if (@file_exists($folder . DIRECTORY_SEPARATOR . '.myjoomla.ignore.files'))
		{
			return array();
		}

		$handle = @opendir($folder);
		if ($handle === FALSE)
		{
			$handle = @opendir($folder . '/');
		}
		// If directory is not accessible, just return FALSE
		if ($handle === FALSE)
		{
			return $false;
		}

		while ((($file = @readdir($handle)) !== FALSE))
		{
			if (($file != '.') && ($file != '..'))
			{
				$ds    = ($folder == '') || ($folder == DIRECTORY_SEPARATOR) || (@substr($folder, -1) == DIRECTORY_SEPARATOR) || (@substr($folder, -1) == DIRECTORY_SEPARATOR) ? '' : DIRECTORY_SEPARATOR;
				$dir   = $folder . $ds . $file;
				$isDir = @is_dir($dir);
				if (!$isDir)
				{
					$arr [] = $this->_cleanupFileFolderName($file);
				}
			}
		}
		@closedir($handle);

		return $arr;
	}

	/**
	 * @param       $tbl
	 * @param array $updateIds
	 *
	 * @return array
	 */
	private function removeFromQueue($tbl, $updateIds = array())
	{
		if (count($updateIds))
		{
			bfLog::log('Removing ' . count($updateIds) . ' ' . $tbl . ' from the queue');
			$sql = 'UPDATE bf_' . $tbl . ' SET queued = 0 WHERE id IN (' . implode(', ', $updateIds) . ')';
			$this->db->setQuery($sql);
			if (!$this->db->query())
			{
				bfEncrypt::reply(bfReply::ERROR, $this->db->getErrorMsg());
			}
		}

		return array();
	}

	/**
	 * There are a lot of error supression @'s in this method, mainly to handle
	 * fringe cases where we dont have permissions or some other fringe error
	 * happens
	 *
	 * @param string $file
	 *
	 * @return array
	 */
	private function _getFileInfo($file)
	{

		// clean up
		$file     = stripslashes($this->_cleanupFileFolderName($file));
		$fileInfo = array();

		// Get the File Permissions - if we are allowed (Hence the @)
		$fileInfo ['perms'] = @substr(@decoct(@fileperms($file)), 2);

		// Get the File Modification Time - if we are allowed (Hence the @)
		$fileInfo ['mtime'] = @filemtime($file);

		// Get the File Size - if we are allowed (Hence the @)
		$size              = @filesize($file);
		$fileInfo ['size'] = $size;

		if (!$fileInfo ['size']) $fileInfo ['size'] = '0';


		// only hash small files
		if ($size < 1048576)
		{ // 1 megabyte = 1048576 bytes
			// We need a @ incase of "failed to
			// open stream: Permission denied"
			$hash = @md5_file($file);

			// something went wrong
			if (!$hash)
			{
				$hash = 'Unable To Calc Hash';
			}
		}
		else
		{
			$hash = 'Too Big To Hash';
		}

		// save the has
		$fileInfo ['currenthash'] = $hash;

		return $fileInfo;
	}

	/**
	 * Scan folders and find more files in them
	 */
	private function initialscanningfoldersAction()
	{
		$deleteFoldersIds = array();
		$addToScanQueue   = array();
		$break            = FALSE;
		$count            = 0;

		// See if we have any folders to scan
		$this->db->setQuery("SELECT COUNT(*)  FROM bf_folders WHERE queued = 1");
		$totalLeft = $this->db->loadResult();

		if (!$totalLeft)
		{
			$addToScanQueue = $this->addDirToScanQueue(array('/'), 0);
			$this->toggleQueued('folders', 1);

			// move on, and die
			$this->noMoreFoldersToScan = TRUE;
			$this->nextStepPlease(TRUE);
		}

		// We have some folders to look into and we have some time left
		while ($this->db->loadResult() > 0 && $this->_timer->getTimeLeft() > _BF_CONFIG_FOLDERS_TIMER_ONE)
		{

			// ok so we have a load of folders...
			if (count($deleteFoldersIds))
			{
				$this->db->setQuery("SELECT id, folderwithpath FROM bf_folders WHERE queued = 1 AND id NOT IN (" . implode(',', $deleteFoldersIds) . ") ORDER BY id ASC LIMIT " . _BF_CONFIG_FOLDERS_COUNT_ONE);
			}
			else
			{
				$this->db->setQuery("SELECT id, folderwithpath FROM bf_folders WHERE queued = 1 ORDER BY id ASC LIMIT " . _BF_CONFIG_FOLDERS_COUNT_ONE);
			}
			$dirs_to_scan = $this->db->loadObjectList();

			$COUNTER = 0;
			while (count($dirs_to_scan) && $this->_timer->getTimeLeft() > _BF_CONFIG_FOLDERS_TIMER_ONE)
			{

				$dirToScanObj = array_pop($dirs_to_scan);

				$dirToScan = stripslashes($dirToScanObj->folderwithpath);

				$dirToScan = str_replace('////', '/', $dirToScan);

				// Redundant?
				if ($this->_timer->getTimeLeft() <= _BF_CONFIG_FOLDERS_TIMER_TWO)
				{
					$addToScanQueue   = $this->addDirToScanQueue($addToScanQueue);
					$deleteFoldersIds = $this->removeFromQueue('folders', $deleteFoldersIds);
					$this->saveState(FALSE, __LINE__); // Exits with reply
				}

				// Get the subdirectories in this folder and add to the list of folders to scan enforce only from base root...
				$dirToScanWithPath = JPATH_BASE . preg_replace('#^' . JPATH_BASE . '#i', '', $dirToScan, 1);

				// but if our$dirToScanWithPath is now blank it means a path of /var/www/var/www !
				if ($dirToScanWithPath == JPATH_BASE)
				{

					// need this else we loop when /home/public_html/home/public_html is found!!
					$dirToScanWithPath = JPATH_BASE . $dirToScan;
				}

				$subDirectorys = $this->getFolders($dirToScanWithPath);

				bfLog::log('Found ' . count($subDirectorys) . ' subfolders in ' . str_replace(JPATH_BASE, '', $dirToScan));

				foreach ($subDirectorys as $folder)
				{
					$folder           = str_replace('////', '/', $folder);
					$addToScanQueue[] = $folder;
				}

				$deleteFoldersIds[] = $dirToScanObj->id;

				if ((count($deleteFoldersIds) > 1000) || (count($addToScanQueue) > 1000) || $this->_timer->getTimeLeft() <= _BF_CONFIG_FOLDERS_TIMER_TWO)
				{
					// remove this current dir form the scan queue;
					$addToScanQueue   = str_replace('////', '/', $addToScanQueue);
					$addToScanQueue   = $this->addDirToScanQueue($addToScanQueue);
					$deleteFoldersIds = $this->removeFromQueue('folders', $deleteFoldersIds);
					$this->saveState(FALSE, __LINE__); // Exits with reply
				}
			}

			$this->db->setQuery("SELECT count(*)  FROM bf_folders WHERE queued = 1 AND id NOT IN (" . implode(',', $deleteFoldersIds) . ")");
		}

		$this->addDirToScanQueue($addToScanQueue);
		$this->removeFromQueue('folders', $deleteFoldersIds);

		$this->saveState(FALSE, __LINE__); // Exits with reply
	}

	/**
	 * @param $tblSuffix
	 * @param $queued
	 */
	private function toggleQueued($tblSuffix, $queued)
	{
		$sql = 'UPDATE bf_' . $tblSuffix . ' SET queued = ' . $queued;
		$this->db->setQuery($sql);
		$this->db->query();
	}

	/**
	 * Deep scan
	 */
	private function deepscanAction()
	{
		if (file_exists('tmp/corefiles.sql'))
		{
			bfLog::log('Found core files - marking them as such');
			$this->db->setQuery(file_get_contents('tmp/corefiles.sql'));
			$this->db->query();
			unlink('tmp/corefiles.sql');
			$this->saveState(FALSE, __LINE__);

		}
		if (file_exists('tmp/hashfailed.sql'))
		{
			bfLog::log('Found modified core files - adding to deepscan');
			$this->db->setQuery(file_get_contents('tmp/hashfailed.sql'));
			$this->db->query();
			unlink('tmp/hashfailed.sql');
			$this->saveState(FALSE, __LINE__);
		}


		try
		{

			// if we are not complete
			$this->db->setQuery('SELECT COUNT(*) FROM bf_files WHERE queued = 1');
			$queueCount = $this->db->loadResult();

			if ($queueCount == 0 && $this->deepscancomplete == TRUE)
			{
				// left here in case needed again.
			}

			if (!$queueCount && !$this->deepscancomplete)
			{

				bfLog::log('Adding files to the scan queue');

				/**
				 * Yes I know that this sql gives places for hackers to hide, however its based on extensive, active,
				 * experience and is often changed to reflect current trends
				 */
				$sql = "SELECT id FROM bf_files
                            WHERE
                                (
                                  SIZE < 400000                                 -- 0.4mb Limit
                                AND
                                  SIZE > 0                                      -- Must have content!
                                )
                            AND
                                (
                                  iscorefile = 0                                -- No Core Files
                                OR
                                  iscorefile IS NULL                            -- No Core Files - NULL !== 0 -- IDIOT PHIL!!!!
                                OR
                                  hashfailed = 1                                -- Or core files which hash has failed
                                )
                            AND                                                 -- Filter out the probably ok file extensions and other stuff we are 'fairly' happy to ignore
                            (
                                currenthash != 'f96aa8838dffa02a4a8438f2a8596025' -- ok blank index.html file

                                AND filewithpath NOT LIKE '/plugins/system/bfnetwork%' -- Our stuff
                                AND filewithpath NOT LIKE '%.DS_Store%'         -- Mac finder files
                                AND filewithpath NOT LIKE '%.zip'               -- cant preg_match inside a zip!
                                AND filewithpath NOT LIKE '%.gzip'              -- cant preg_match inside a zip!
                                AND filewithpath NOT LIKE '%.gz'                -- cant preg_match inside a zip!
                                AND filewithpath NOT LIKE '%.doc'
                                AND filewithpath NOT LIKE '%.docx'
                                AND filewithpath NOT LIKE '%.xls'
                                AND filewithpath NOT LIKE '%.ppt'
                                AND filewithpath NOT LIKE '%.pdf'
                                AND filewithpath NOT LIKE '%.rtf'               -- never seen anything bad in a rtf
                                AND filewithpath NOT LIKE '%.mno'
                                AND filewithpath NOT LIKE '%.ashx'
                                AND filewithpath NOT LIKE '%.png'               -- never seen anything bad in a png
                                AND filewithpath NOT LIKE '%.psd'               -- Photoshop, normally a massive file too
                                AND filewithpath NOT LIKE '%.wott'              -- font file
                                AND filewithpath NOT LIKE '%.ttf'               -- font file
                                AND filewithpath NOT LIKE '%.css'               -- plain text css, never seen Joomla hack in css file
                                AND filewithpath NOT LIKE '%.swf'               -- flash
                                AND filewithpath NOT LIKE '%.flv'               -- flash
                                AND filewithpath NOT LIKE '%.po'                -- language files
                                AND filewithpath NOT LIKE '%.mo'
                                AND filewithpath NOT LIKE '%.pot'
                                AND filewithpath NOT LIKE '%.eot'
                                AND filewithpath NOT LIKE '%.ini'
                                AND filewithpath NOT LIKE '%.svg'
                                AND filewithpath NOT LIKE '%.mpeg'              -- No need to audit inside audio files, never seen a Joomla hack in these
                                AND filewithpath NOT LIKE '%.mvk'               -- No need to audit inside audio files, never seen a Joomla hack in these
                                AND filewithpath NOT LIKE '%.mp3'               -- No need to audit inside audio files, never seen a Joomla hack in these
                                AND filewithpath NOT LIKE '%.less'
                                AND filewithpath NOT LIKE '%.sql'
                                AND filewithpath NOT LIKE '%.wsdl'
                                AND filewithpath NOT LIKE '%.woff'
                                AND filewithpath NOT LIKE '%.woff2'
                                AND filewithpath NOT LIKE '%.otf'
                                AND filewithpath NOT LIKE '%.xml'               -- never seen a hack in an xml file
                                AND filewithpath NOT LIKE '%.php_expire'        -- Expired cache file
                                AND filewithpath NOT LIKE '%.jpa'               -- Akeeba backup files
                                AND filewithpath NOT LIKE '%/akeeba_json.%'           -- Akeeba json state file
                                AND filewithpath NOT LIKE '%/administrator/components/com_akeeba/backup/akeeba%'           -- Akeeba json state file
                                AND filewithpath NOT LIKE '%/akeeba_backend.id%'           -- Akeeba json state file
                                AND filewithpath NOT LIKE '%/akeeba_backend.php'           -- Akeeba json state file
                                AND filewithpath NOT LIKE '%/akeeba_backend.log'           -- Akeeba json state file
                                AND filewithpath NOT LIKE '%/akeeba_lazy.php'           -- Akeeba json state file
                                AND filewithpath NOT LIKE '%/akeeba_frontend.php'           -- Akeeba json state file
                                AND filewithpath NOT LIKE '%/cacert.pem'           -- cacert.pem
                                AND filewithpath NOT LIKE '%/GeoIP.dat'          -- never seen a hack in an GeoIP.dat file but the one in RSFirewall/Admin Tools kills the audit :-(
                                AND filewithpath NOT LIKE '%/ca-certificates.crt'          -- never seen a hack in an ca-certificates.crt file but the one in RSFirewall/Admin Tools kills the audit :-(
                                AND filewithpath NOT LIKE '%error_log'      -- PHP error logs, we alert to ALL these in another check
                                AND filewithpath NOT LIKE '%/stats/webalizer.current'           -- Crappy file
                                AND filewithpath NOT LIKE '%/stats/usage_%.html'           -- Crappy file
                                AND filewithpath NOT LIKE '%/components/libraries/cmslib/cache/cache__%' -- Massive folder of cache files
                                AND filewithpath NOT LIKE '%/plugins/system/akgeoip/lib/vendor/guzzle/guzzle/%' -- Akeeba GeoIP Docs
                                AND filewithpath NOT LIKE '%/components/com_jce/editor/tiny_mce/plugins/code/img/icons.gif' -- JCE Code icons
                                AND filewithpath NOT LIKE '%/components/com_jce/editor/libraries/js/pdf.js' -- JCE PDF JS 900kb+
                            )";
				$this->db->setQuery($sql);


				if (method_exists($this->db, 'loadColumn'))
				{
					$ids = $this->db->loadColumn();
				}
				else
				{
					$ids = $this->db->loadResultArray();
				}

				bfLog::log($this->db->getErrorMsg());

				if (!count($ids))
				{
					bfLog::log('NO FILES TO DEEP SCAN = THIS CANNOT BE POSSIBLE RIGHT?');
					$this->db->setQuery($sql);
					$some = $this->db->query();
				}
				else
				{

					$sql = "UPDATE bf_files SET queued = 1 WHERE id IN ( %s )";

					if (strlen(sprintf($sql, implode(', ', $ids))) > $this->max_allowed_packet)
					{

						$parts = $this->array_split($ids, 4);

						$sqlToRun1 = sprintf($sql, implode(', ', $parts[0]));
						bfLog::log('sql size 1= ' . strlen($sqlToRun1));
						$this->db->setQuery($sqlToRun1);
						$this->db->query();

						$sqlToRun2 = sprintf($sql, implode(', ', $parts[1]));
						bfLog::log('sql size 2= ' . strlen($sqlToRun2));
						$this->db->setQuery($sqlToRun2);
						$this->db->query();

						$sqlToRun3 = sprintf($sql, implode(', ', $parts[2]));
						bfLog::log('sql size 3= ' . strlen($sqlToRun3));
						$this->db->setQuery($sqlToRun3);
						$this->db->query();

						$sqlToRun4 = sprintf($sql, implode(', ', $parts[3]));
						bfLog::log('sql size 4= ' . strlen($sqlToRun4));
						$this->db->setQuery($sqlToRun4);
						$this->db->query();

					}
					else
					{

						$sql = "UPDATE bf_files SET queued = 1 WHERE id IN
                        (
                        " . implode(',', $ids) . "
                        )";
						$this->db->setQuery($sql);
						$this->db->query();
					}
				}

				// DEQUEUE known clean files not changed
				bfLog::log('DONE Adding ' . count($ids) . ' files to the scan queue db table');


				bfLog::log('Retrieving global whitelist from cdn');
				$url = 'https://cdn.myjoomla.com/public/global/whitelist';

				$options = array(
					'http' => array(
						'method' => "GET",
						'header' => "Accept-language: en\r\n" .
							"User-Agent: " . $_SERVER['HTTP_HOST'] . "\r\n"
					)
				);

				$context = stream_context_create($options);

				// get the data from the request
				$whitelist = file_get_contents($url, FALSE, $context);

				// F.M.L - I hate crap servers!
				if (!$whitelist)
				{

					$ch = curl_init();

					// Set up bare minimum CURL Options needed for myJoomla.com
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
					curl_setopt($ch, CURLOPT_HEADER, FALSE);
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
					curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_HOST']);

					// Attempt to download using CURL and CURLOPT_SSL_VERIFYPEER set to TRUE
					$whitelist = curl_exec($ch);

					// Did we succeed in getting something?????
					if (!$whitelist)
					{

						/**
						 * ** CRAPPY SERVER ALERT ** CRAPPY SERVER ALERT ** CRAPPY SERVER ALERT ** CRAPPY SERVER ALERT **
						 *
						 * Ok try without validation of the SSL (gulp) but this is needed on some servers without a pem file
						 * and we need to be compatible as possible - even on crappy webhosts when they need us most ;-(
						 */
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
						curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

						//  Second Attempt to download using CURL and CURLOPT_SSL_VERIFYPEER set to FALSE (gulp)
						$whitelist = curl_exec($ch);
					}

					curl_close($ch);
				}

				if (!$whitelist)
				{
					bfEncrypt::reply(bfReply::ERROR, 'We could not download a required file from the CDN (w) - seek assistance from phil@phil-taylor.com');
				}

				$whitelistSQL = 'UPDATE bf_files SET queued = 0, falsepositive = 1 WHERE currenthash IN (' . $whitelist . ')';
				$this->db->setQuery($whitelistSQL);
				bfLog::log('Applying global whitelist from cdn to db');
				$this->db->query();
				bfLog::log('Global whitelist applied!');

				bfLog::log('Retrieving global hacklist from cdn');
				$url = 'https://cdn.myjoomla.com/public/global/hacklist';

				$options = array(
					'http' => array(
						'method' => "GET",
						'header' => "Accept-language: en\r\n" .
							"User-Agent: " . $_SERVER['HTTP_HOST'] . "\r\n"
					)
				);

				$context = stream_context_create($options);

				// get the data from the request
				$hacklist = file_get_contents($url, FALSE, $context);

				// F.M.L - I hate crap servers!
				if (!$hacklist)
				{

					$ch = curl_init();

					// Set up bare minimum CURL Options needed for myJoomla.com
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
					curl_setopt($ch, CURLOPT_HEADER, FALSE);
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
					curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_HOST']);

					// Attempt to download using CURL and CURLOPT_SSL_VERIFYPEER set to TRUE
					$hacklist = curl_exec($ch);

					// Did we succeed in getting something?????
					if (!$hacklist)
					{

						/**
						 * ** CRAPPY SERVER ALERT ** CRAPPY SERVER ALERT ** CRAPPY SERVER ALERT ** CRAPPY SERVER ALERT **
						 *
						 * Ok try without validation of the SSL (gulp) but this is needed on some servers without a pem file
						 * and we need to be compatible as possible - even on crappy webhosts when they need us most ;-(
						 */
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
						curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

						//  Second Attempt to download using CURL and CURLOPT_SSL_VERIFYPEER set to FALSE (gulp)
						$hacklist = curl_exec($ch);
					}

					curl_close($ch);
				}

				if (!$hacklist)
				{
					bfEncrypt::reply(bfReply::ERROR, 'We could not download a required file from the CDN (h) - seek assistance from phil@phil-taylor.com');
				}

				$hacklistSQL = 'UPDATE bf_files SET queued = 0, suspectcontent = 1, hacked = 1 WHERE currenthash IN (' . $hacklist . ')';
				$this->db->setQuery($hacklistSQL);
				bfLog::log('Applying global hacklist from cdn to db');
				$this->db->query();
				bfLog::log('Global hacklist applied!');

				// Cache our patterns
				$pattern = file_get_contents('tmp/tmp.pattern');
				if (file_exists('tmp/tmp.pattern.lastmd5'))
				{
					$lastPatternmd5 = file_get_contents('tmp/tmp.pattern.lastmd5');
				}
				else
				{
					$lastPatternmd5 = "";
				}

				// if encrypted - decrypt
				if (substr($pattern, 0, 4) == 'RC4:')
				{
					$pattern = base64_decode(substr($pattern, 4, strlen($pattern) - 4));
					$RC4     = new Crypt_RC4();
					$RC4->setKey('NotMeantToBeSecure'); // just to hide from other server side scanners
					$pattern = $RC4->decrypt($pattern);

					/**
					 * When developing/debugging we might want to cache the unencrypted patterns
					 * We dont do the normally because some webhost scanners see them as hacks
					 * when they are not!
					 */
					//file_put_contents('tmp/tmp.pattern.unenc', $pattern); //sss

					bfLog::log('LAST PATTERN TEST =   ' . $lastPatternmd5 . '==' . md5($pattern));
					if ($lastPatternmd5 == md5($pattern))
					{
						bfLog::log('SPEEDUP - Yes, we will speedup');
						$doSpeedup = TRUE;
					}
					else
					{
						bfLog::log('SPEEDUP - No, we will not speedup');
						$doSpeedup = FALSE;
					}
				}

				if ($doSpeedup && (_BF_SPEED == 'DEFAULT' || _BF_SPEED == 'FAST'))
				{
					$this->db->setQuery('SHOW TABLES LIKE "bf_files_last"');
					if ($this->db->loadResult())
					{

						$speedupSQL = 'UPDATE bf_files AS NEWTABLE
                                INNER JOIN  (
                                    SELECT
                                        bf_files_last.filewithpath, bf_files_last.suspectcontent,  bf_files_last.falsepositive,  bf_files_last.encrypted  FROM bf_files_last
                                    LEFT JOIN
                                        bf_files ON bf_files_last.filewithpath = bf_files.filewithpath
                                    WHERE
                                        bf_files_last.currenthash = bf_files.currenthash
                                    AND
                                        bf_files_last.filemtime = bf_files.filemtime
                                    AND
                                        bf_files_last.fileperms = bf_files.fileperms
                                    AND
                                        bf_files_last.filewithpath = bf_files.filewithpath
                                ) AS
                                    OLDTABLE
                                   ON
                                    NEWTABLE.filewithpath = OLDTABLE.filewithpath
                                SET
                                    NEWTABLE.filewithpath = OLDTABLE.filewithpath,
                                    NEWTABLE.suspectcontent = OLDTABLE.suspectcontent,
                                    NEWTABLE.falsepositive = OLDTABLE.falsepositive,
                                    NEWTABLE.encrypted = OLDTABLE.encrypted,
                                    NEWTABLE.queued = 0
                                WHERE
                                  OLDTABLE.suspectcontent != 1
                             ';

						bfLog::log('SPEEDUP - saving the sql to run for the speedup');
						file_put_contents('tmp/speedup.sql', $speedupSQL);
					}
				}
				// ok this took a lot of time, so to be careful we will re-tick...
				$this->saveState(FALSE, __LINE__);
			}

			$pattern = file_get_contents('tmp/tmp.pattern');

			// if encrypted - decrypt
			if (substr($pattern, 0, 4) == 'RC4:')
			{
				$pattern = base64_decode(substr($pattern, 4, strlen($pattern) - 4));
				$RC4     = new Crypt_RC4();
				$RC4->setKey('NotMeantToBeSecure'); // just to hide from other server side scanners
				$pattern = $RC4->decrypt($pattern);
			}

			if (file_exists('tmp/speedup.sql') && (_BF_SPEED == 'DEFAULT' || _BF_SPEED == 'FAST'))
			{
				bfLog::log('SPEEDUP Found speedup sql files - removing files to deepscan');
				$this->db->setQuery(file_get_contents('tmp/speedup.sql'));
				$this->db->query();

				// force at least one file to be audited to prevent broken loop
				$this->db->setQuery('UPDATE bf_files SET queued = 1 WHERE filewithpath = "/configuration.php"');
				$this->db->query();
				bfLog::log('SPEEDUP - removing speedup file after applying it');
				unlink('tmp/speedup.sql');
				$this->saveState(FALSE, __LINE__);
			}
			else
			{
				if (file_exists(dirname(__FILE__) . '/tmp/speedup.sql'))
				{
					bfLog::log('SPEEDUP - removing speedup file without applying it  as _BF_SPEED = ' . _BF_SPEED);
					@unlink(dirname(__FILE__) . '/tmp/speedup.sql');
				}
			}

			// A nice while loop while we have time left
			if (count($this->getmergedIds()))
			{
				$this->db->setQuery("SELECT count(*) FROM bf_files WHERE queued = 1 AND id NOT IN (" . implode(',', $this->getmergedIds()) . ")");
			}
			else
			{
				$this->db->setQuery("SELECT COUNT(*) FROM bf_files WHERE queued = 1");
			}

			while ($this->db->loadResult() > 0 && $this->_timer->getTimeLeft() > _BF_CONFIG_DEEPSCAN_TIMER_ONE)
			{

				// ok so we have a load of files....
				if (count($this->getmergedIds()))
				{
					$this->db->setQuery("SELECT * FROM bf_files WHERE queued = 1 AND id NOT IN (" . implode(',', $this->getmergedIds()) . ") ORDER BY id ASC LIMIT " . _BF_CONFIG_DEEPSCAN_COUNT_ONE);
				}
				else
				{
					$this->db->setQuery("SELECT * FROM bf_files WHERE queued = 1 ORDER BY id ASC LIMIT " . _BF_CONFIG_DEEPSCAN_COUNT_ONE);
				}

				$files_to_scan = $this->db->loadObjectList();

				// still, while we have files and time left
				while (count($files_to_scan) && $this->_timer->getTimeLeft() > _BF_CONFIG_DEEPSCAN_TIMER_ONE)
				{

					// get one file
					$file_to_scan = array_pop($files_to_scan);

					/**
					 * If this is a large JS file like jquery.ui.src.js then we need more time dammit!
					 * Also need to do this on WSDL files for some reason
					 */
					$size = filesize(JPATH_BASE . $file_to_scan->filewithpath);
					if (((strpos($file_to_scan->filewithpath, 'wsdl') || strpos($file_to_scan->filewithpath, 'jquery'))
							&& $this->_timer->getTimeLeft() < 4
						) ||
						($size > 100000 && (_BF_SPEED != 'DEFAULT' && _BF_SPEED != 'FAST') && $this->_timer->getTimeLeft() < 4)
					)
					{
						bfLog::log('Next file is a problematic JS/wsdl is of size ' . $size . ' and we only had ' . $this->_timer->getTimeLeft() . ' left so we are Zzz... and will come back next tick');
						$this->updateFilesFromDeepscan(__LINE__);
						$this->saveState(FALSE, __LINE__);
					}

					// toggle if we can safely skip it   } else if
					$skip = 0;

					// Is this a suspect file
					// Is this file encrypted
					// is this file an uploader
					// is this file a mailer
					$isSuspect  = FALSE;
					$encrypted  = FALSE;
					$isUploader = FALSE;
					$isMailer   = FALSE;

					$file_extension = strtolower(pathinfo(JPATH_BASE . $file_to_scan->filewithpath, PATHINFO_EXTENSION));

					// If the file no longer exists then skip
					if (!file_exists(JPATH_BASE . $file_to_scan->filewithpath))
					{
						bfLog::log('SKIP: FILE WAS SKIPPED AS DOES NOT EXIST!!! ' . $file_to_scan->filewithpath);
						$skip = -1;

					}
					else if ($file_extension == 'gif')
					{

						if ($this->is_ani(JPATH_BASE . $file_to_scan->filewithpath))
						{
							bfLog::log('SKIP: FILE WAS ANIMATED GIF - skipping ' . $file_to_scan->filewithpath);
							$skip = -2;
						}

					}
					else if ($file_to_scan->filewithpath == '/backups/akeeba_json.php')
					{
						bfLog::log('SKIP: skipping ' . $file_to_scan->filewithpath);
						$skip = -3;

					}
					else if ($file_to_scan->filewithpath == '/stats/webalizer.current')
					{
						bfLog::log('SKIP: skipping ' . $file_to_scan->filewithpath);
						$skip = -4;

					}
					else if (preg_match('/\.(gif|jpg|png|ico|jpeg|bmp)/ism', basename($file_to_scan->filewithpath))
						&& $this->isValidImage($file_to_scan->filewithpath)
					)
					{
						bfLog::log('SKIP: skipping VALID IMAGE ' . $file_to_scan->filewithpath);
						$skip = -6;

					}
					else if (preg_match('/\/stats\/usage_.*\.html/', $file_to_scan->filewithpath))
					{
						bfLog::log('SKIP: skipping ' . $file_to_scan->filewithpath);
						$skip = -5;

					}
					else if ($skip == 0 && filesize(JPATH_BASE . $file_to_scan->filewithpath) > 1024288)
					{
						bfLog::log('SKIP: FILE WAS OVER 1Mb - skipping ' . $file_to_scan->filewithpath);
						$skip = -7;
					}
					else if ($file_to_scan->filewithpath == '/components/com_dtregister/assets/js/jquery-ui.js')
					{
						$skip = -7;
					}
					else if ($file_to_scan->filewithpath == '/administrator/components/com_akeeba/backup/akeeba.json.log')
					{
						$skip = -7;
					}
					elseif (filesize(JPATH_BASE . $file_to_scan->filewithpath) > 400000)
					{
						bfLog::log('SKIP: FILE WAS SKIPPED AS OVER 4000000!!! ' . $file_to_scan->filewithpath);
						$skip = -1;

					}
					if ($skip !== 0)
					{

						// mark it as false positive (-2) or skipped (-1)
						$sql = sprintf("UPDATE bf_files SET queued = 0, suspectcontent = '%s' WHERE id = '%s'",
							$skip,
							addslashes($file_to_scan->id)
						);
						$this->db->setQuery($sql);
						$this->db->query();
						bfLog::log('SKIP:CONTINUE');
						continue; // no more processing on this file - skipped
					}

					// cleanup
					$fff = JPATH_BASE . stripslashes($file_to_scan->filewithpath);

					// WINDOWS I HATE YOU! - bodge it
					$fff = str_replace('\:', '/:', $fff);

					// need a @ to prevent access denied
					$chunk = @file_get_contents($fff);

					// remove stuff that is likely to be marked as suspect, when we are happy its not...
					$chunk = $this->applyStringExceptions($chunk, $file_to_scan);

					// Not really a chunk now, as we load the whole file into memory
					if (trim($chunk))
					{

						// Need at least 3 seconds to run the preg_match on
						// average slow machine
						if ($this->_timer->getTimeLeft() < _BF_CONFIG_DEEPSCAN_TIMER_TWO)
						{
							bfLog::log('Need at least 3 seconds to run the preg_match on average slow machine');
							$this->saveState(FALSE, __LINE__);
						}


						// hard to audit c99 clones
						preg_match('/(auth_pass).*(default_use_ajax)/ism', $chunk, $matches);
						if (count($matches) >= 3)
						{
							$isSuspect = TRUE;
						}
						else
						{


							if (preg_match('/\.php/', $file_to_scan->filewithpath))
							{
								preg_match('/move_uploaded_file/ism', $chunk, $matches);
								if (count($matches) >= 1)
								{
									$isUploader = TRUE;
								}

								preg_match('/[^a-zA-Z0-9\-]{1}\s*mail\s*\(/ism', $chunk, $matches);
								if (count($matches) >= 1)
								{
									$isMailer = TRUE;
								}
							}

							// Test if suspect
							bfLog::log('Auditing File: ' . $file_to_scan->filewithpath . ' - ' . $file_to_scan->size . ' bytes');
							$isSuspect = (preg_match('/' . $pattern . '/ism', $chunk) ? TRUE : FALSE);

							// Test If encrypted
							$regex     = "/OOO000000|if\(!extension_loaded\('ionCube\sLoader'\)\)|<\?php\s@Zend;|This\sfile\swas\sencoded\sby\sthe.*Zend Encoder/i";
							$encrypted = (preg_match($regex, $chunk) ? 1 : 0);
						}
					}
					else
					{
						bfLog::log('FILE WAS EMPTY!!! ' . $file_to_scan->filewithpath);
					}

					// free up memory
					unset ($chunk);

					$encrypted == (int) $encrypted;
					$isSuspect = (int) $isSuspect;

					if ($encrypted && $isSuspect)
					{
						bfLog::log(' + isEncrypted/suspect');
						$this->_encryptedAndSuspectIds[] = $file_to_scan->id;
					}
					else if ($encrypted)
					{
						bfLog::log(' + isEncrypted');
						$this->_encryptedIds[] = $file_to_scan->id;
					}
					else if ($isSuspect)
					{
						bfLog::log(' + isSuspect');
						$this->_suspectIds[] = $file_to_scan->id;
					}
					else if ($isMailer)
					{
						bfLog::log(' + isMailer');
						$this->_mailerIds[] = $file_to_scan->id;
					}
					else if ($isUploader)
					{
						bfLog::log(' + isUploader');
						$this->_uploaderIds[] = $file_to_scan->id;
					}
					else
					{
						bfLog::log(' + OK');
						$this->_notencryptedAndSuspectIds[] = $file_to_scan->id;
					}

					if (_BF_SPEED == 'CRAPPYWEBHOST' || $this->_timer->getTimeLeft() < _BF_CONFIG_DEEPSCAN_TIMER_TWO)
					{
						$this->updateFilesFromDeepscan(__LINE__);
						$this->saveState(FALSE, __LINE__);
					}
				}

				if ($this->_timer->getTimeLeft() < _BF_CONFIG_DEEPSCAN_TIMER_TWO)
				{
					$this->updateFilesFromDeepscan(__LINE__);
					$this->saveState(FALSE, __LINE__);
				}

				// needed to go back up to the top of the loop
				if (count($this->getmergedIds()))
				{
					$this->db->setQuery("SELECT count(*) FROM bf_files WHERE queued = 1 AND id NOT IN (" . implode(',', $this->getmergedIds()) . ")");
				}
				else
				{
					$this->db->setQuery("SELECT COUNT(*) FROM bf_files WHERE queued = 1");
				}
			}

			if (count($this->getmergedIds()))
			{
				$this->db->setQuery("SELECT count(*) FROM bf_files WHERE queued = 1 AND id NOT IN (" . implode(',', $this->getmergedIds()) . ")");
			}
			else
			{
				$this->db->setQuery("SELECT COUNT(*) FROM bf_files WHERE queued = 1");
			}
			if ($this->db->loadResult() == 0)
			{
				bfLog::log(' ======== deepscancomplete ========');
				$this->deepscancomplete = TRUE;
				$this->updateFilesFromDeepscan(__LINE__);

				// save our latest pattern match
				// decrypt then save the md5!
				file_put_contents('tmp/tmp.pattern.lastmd5', md5($pattern));

				$this->nextStepPlease();
			}
			else
			{

				bfLog::log(' ======== deepscancomplete NOT COMPLETE========');
				$this->updateFilesFromDeepscan(__LINE__);
				$this->saveState(FALSE, __LINE__);
			}

		}
		catch (Exception $e)
		{
			// Just continue...
			if (!defined('_BF_LAST_BREATH')) define('_BF_LAST_BREATH', $e->getMessage());
			bfLog::log(' ======== EXCEPTION =========' . $e->getMessage());
		}
	}

	/**
	 * @return array
	 */
	private function getmergedIds()
	{
		return array_merge($this->_encryptedAndSuspectIds,
			$this->_notencryptedAndSuspectIds,
			$this->_encryptedIds,
			$this->_mailerIds,
			$this->_uploaderIds,
			$this->_suspectIds);
	}

	/**
	 * @param $line
	 */
	private function updateFilesFromDeepscan($line)
	{
		// reconnect to the database
		$this->db = JFactory::getDBO();

		$this->dbPing();

		bfLog::log(' =updateFilesFromDeepscan called from line ' . $line);
		bfLog::log(' =Marking this number of files as _encryptedAndSuspectIds= ' . count($this->_encryptedAndSuspectIds));
		if (count($this->_encryptedAndSuspectIds))
		{
			$sql = "UPDATE bf_files SET encrypted = %s, suspectcontent = %s, queued = 0 WHERE id IN(%s)";
			$sql = sprintf($sql, 1, 1, implode(', ', $this->_encryptedAndSuspectIds));
			$this->db->setQuery($sql);
			if ($this->db->query())
			{
				bfLog::log(' = Removed success = ');
			}
			else
			{
				bfLog::log('=============================================');
				bfLog::log($this->db->getErrorMsg() . $sql);
				bfLog::log('=============================================');
				bfEncrypt::reply(bfReply::ERROR, $this->db->getErrorMsg());
			}
			$this->_encryptedAndSuspectIds = array();
		}

		bfLog::log(' =Marking this number of files as _encryptedIds = ' . count($this->_encryptedIds));
		if (count($this->_encryptedIds))
		{
			$sql = "UPDATE bf_files SET encrypted = %s, suspectcontent = %s, queued = 0 WHERE id IN(%s)";
			$sql = sprintf($sql, 1, 0, implode(', ', $this->_encryptedIds));
			$this->db->setQuery($sql);
			if ($this->db->query())
			{
				bfLog::log(' = Removed success = ');
			}
			else
			{
				bfLog::log('=============================================');
				bfLog::log($this->db->getErrorMsg() . $sql);
				bfLog::log('=============================================');
				bfEncrypt::reply(bfReply::ERROR, $this->db->getErrorMsg());
			}
			$this->_encryptedIds = array();
		}

		bfLog::log(' =Marking this number of files as _suspectIds = ' . count($this->_suspectIds));
		if (count($this->_suspectIds))
		{
			$sql = "UPDATE bf_files SET encrypted = %s, suspectcontent = %s, queued = 0 WHERE id IN(%s)";
			$sql = sprintf($sql, 0, 1, implode(', ', $this->_suspectIds));
			$this->db->setQuery($sql);
			if ($this->db->query())
			{
				bfLog::log(' = Removed success = ');
			}
			else
			{
				bfLog::log('=============================================');
				bfLog::log($this->db->getErrorMsg() . $sql);
				bfLog::log('=============================================');
				bfEncrypt::reply(bfReply::ERROR, $this->db->getErrorMsg());
			}
			$this->_suspectIds = array();
		}

		bfLog::log(' =Marking this number of files as NOT _notencryptedAndSuspectIds = ' . count($this->_notencryptedAndSuspectIds));
		if (count($this->_notencryptedAndSuspectIds))
		{
			$sql = "UPDATE bf_files SET encrypted = %s, suspectcontent = %s, queued = 0 WHERE id IN(%s)";
			$sql = sprintf($sql, 0, 0, implode(', ', $this->_notencryptedAndSuspectIds));
			$this->db->setQuery($sql);
			if ($this->db->query())
			{
				bfLog::log(' = Removed success = ');
			}
			else
			{
				bfLog::log('=============================================');
				bfLog::log($this->db->getErrorMsg() . $sql);
				bfLog::log('=============================================');
				bfEncrypt::reply(bfReply::ERROR, $this->db->getErrorMsg());
			}
			$this->_notencryptedAndSuspectIds = array();
		}

		bfLog::log(' =Marking this number of files as _mailer = ' . count($this->_mailerIds));
		if (count($this->_mailerIds))
		{
			$sql = "UPDATE bf_files SET mailer = 1, queued = 0  WHERE id IN(%s)";
			$sql = sprintf($sql, implode(', ', $this->_mailerIds));
			$this->db->setQuery($sql);
			if ($this->db->query())
			{
				bfLog::log(' = mailer success = ');
			}
			else
			{
				bfLog::log('=============================================');
				bfLog::log($this->db->getErrorMsg() . $sql);
				bfLog::log('=============================================');
				bfEncrypt::reply(bfReply::ERROR, $this->db->getErrorMsg());
			}
			$this->_mailerIds = array();
		}

		bfLog::log(' =Marking this number of files as _uploader = ' . count($this->_uploaderIds));
		if (count($this->_uploaderIds))
		{
			$sql = "UPDATE bf_files SET `uploader` = 1, queued = 0  WHERE id IN(%s)";
			$sql = sprintf($sql, implode(', ', $this->_uploaderIds));
			$this->db->setQuery($sql);
			if ($this->db->query())
			{
				bfLog::log(' = mailer success = ');
			}
			else
			{
				bfLog::log('=============================================');
				bfLog::log($this->db->getErrorMsg() . $sql);
				bfLog::log('=============================================');
				bfEncrypt::reply(bfReply::ERROR, $this->db->getErrorMsg());
			}
			$this->_uploaderIds = array();
		}
	}

	/**
	 * An animated gif contains multiple "frames", with each frame having a header made up of:
	 *  - a static 4-byte sequence (\x00\x21\xF9\x04)
	 *  - 4 variable bytes
	 *  - a static 2-byte sequence (\x00\x2C)
	 *
	 * @see    http://www.php.net/manual/en/function.imagecreatefromgif.php#88005
	 * @thanks Mike H.
	 *
	 * We read through the file til we reach the end of the file, or we've found
	 * at least 2 frame headers
	 *
	 * @param $filename string complete path to the file
	 *
	 * @return bool
	 */
	private function is_ani($filename)
	{
		if (!($fh = @fopen($filename, 'rb')))
			return FALSE;

		$count = 0;

		while (!feof($fh) && $count < 2)
			$chunk = fread($fh, 1024 * 100); //read 100kb at a time
		$count += preg_match_all('#\x00\x21\xF9\x04.{4}\x00\x2C#s', $chunk, $matches);

		fclose($fh);

		return $count > 1;
	}

	/**
	 * Not the correct way to check for a valid image but "good enough" for our purposes
	 * Fast and cross PHP version compatible...
	 *
	 * @param $path
	 *
	 * @return bool
	 */
	private function isValidImage($path)
	{
		bfLog::log('isValidImage? ' . JPATH_BASE . $path);
		$a          = @getimagesize(JPATH_BASE . $path);
		$image_type = $a[2];

		if (in_array($image_type, array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP)))
		{
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Lets munge the files contents to reduce the number of false hits we get for eval and stuff
	 * Try to keep this list as small as possible
	 * @todo benchmark this against a preg_replace
	 * @todo add this to the configurable audit configuration
	 *
	 * @param $chunk
	 * @param $file_to_scan
	 *
	 * @return mixed
	 */
	private function applyStringExceptions($chunk, $file_to_scan)
	{
		$chunk = str_replace('matheval', '_RETRACTED_', $chunk);

		// NoNumber Extensions
		$chunk = str_replace('parseVal', '_RETRACTED_', $chunk);

		// com_avreloaded
		$chunk = str_replace('$this->_data = unserialize(base64_decode($rdata));', '_RETRACTED_', $chunk);

		// plugins/system/k2/k2.php
		$chunk = str_replace('$output = \'<div style="display:none', '_RETRACTED_', $chunk);

		// com_community Extensions
		$chunk = str_replace('doubleval', '_RETRACTED_', $chunk);

		// com_breezingforms Extensions
		$chunk = str_replace('Zend_Json::decode(base64_decode', '_RETRACTED_', $chunk);

		// Joomla Core
		$chunk = str_replace('setRedirect(base64_decode($return)', '_RETRACTED_', $chunk);
		$chunk = str_replace('passthru(\'kill -9 \' . $pid);', '_RETRACTED_', $chunk);
		$chunk = str_replace('system(\'export HOME="\' . $info[\'dir\'] . \'"\');', '_RETRACTED_', $chunk);
		$chunk = str_replace('\'c' . 'u' . 'r' . 'l' . '_version\',\'current\',\'cvsclient_connect\',\'cvsclient_log\'', '_RETRACTED_', $chunk);
		$chunk = str_replace('$mainframe->redirect(base64_decode', '_RETRACTED_', $chunk);
		$chunk = str_replace('$return = base64_encode(base64_decode($return).\'#content\');', '_RETRACTED_', $chunk); //1.5.26
		$chunk = str_replace('json_decode(base64_decode', '_RETRACTED_', $chunk); //2.5.8+ Returns a stdClass so cannot be eval'ed

		// com_weblinks
		$chunk = str_replace('JUri::isInternal(base64_decode', '_RETRACTED_', $chunk);

		// highside.js //
		$chunk = str_replace("y[o.position == 'above' ? 'p1' : 'p2'] = o.offsetHeight;", '_RETRACTED_', $chunk);

		// Admincredible
		// /components/com_admincredible/libraries/vendor/oauth-php/library/OAuthRequestVerifier.php
		$chunk = str_replace('if(isset($_REQUEST[\'oauth_signature\']))', '_RETRACTED_', $chunk);

		// com_k2
		$chunk = str_replace('<div style="display:none">\'.JHTML::_(\'select.ra', '_RETRACTED_', $chunk);
		$chunk = str_replace('use exec() rather than shell_exec(), to play b', '_RETRACTED_', $chunk);

		// Master Htaccess File from Akeeba
		if (strpos($file_to_scan->filewithpath, 'htaccess'))
		{
			$chunk = str_replace('RewriteCond %{HTTP_REFERER} (<|>|\'|%0A|%0D|%27|%3C|%3E|%00) [NC,OR]', '_RETRACTED_', $chunk);
			$chunk = str_replace('RewriteCond %{HTTP_REFERER} ([a-zA-Z0-9]{32}) [NC]', '_RETRACTED_', $chunk);
		}

		// Joomla & Akeeba distribute cacert.pem which has Wells Fargo in it
		$chunk = str_replace('Wells Fargo Root CA', '_RETRACTED_', $chunk);

		// Gantry
		$chunk = str_replace('if (!function_exists(\'c' . 'u' . 'r' . 'l_version\'))', '_RETRACTED_', $chunk);

		// Smarty Template
		$chunk = str_replace('$smarty->_eval', '_RETRACTED_', $chunk);

		// JCE
		$chunk = str_replace('$version = ' . 'c' . 'u' . 'r' . 'l' . '_version();', '_RETRACTED_', $chunk);
		$chunk = str_replace('$ssl_supported = ($version[\'features\'] & C' . 'U' . 'R' . 'L' . '_VERSION_SSL);', '_RETRACTED_', $chunk);

		// Sparkline
		$chunk = str_replace('this.shapes[shape.id] = \'p1\';', '_RETRACTED_', $chunk);

		// com_rsform
		$chunk = str_replace('eval($form->', '_RETRACTED_', $chunk);

		// akeeba
		$chunk = str_replace('base64_decode(\'eyJhcHAiOiJqZn', '_RETRACTED_', $chunk);
		$chunk = str_replace('unserialize(base64_decode', '_RETRACTED_', $chunk);

		$iframe = '<iframe style="width: 0px; height: 0px; border: none;" frameborder="0" marginheight="0" marginwidth="0" height="0" width="0"';
		$chunk  = str_replace($iframe, '_RETRACTED_', $chunk);

		// jQuery
		$iframe = "<iframe frameborder='0' width='0' height='0'/>";
		$chunk  = str_replace($iframe, '_RETRACTED_', $chunk);

		// /media/foundry/2.1/scripts/jplayer.js
		$iframe = '0000" width="0" height="0">';
		$chunk  = str_replace($iframe, '_RETRACTED_', $chunk);

		// Google Tag MAanager
		//        <iframe src="//www.googletagmanager.com/ns.html?id=XXX-XXXXX" height="0" width="0" style="display:none;visibility:hidden"></iframe>
		$chunk = preg_replace('#\<iframe\ssrc=\"\/\/www.googletagmanager.com\/ns\.html\?id\=.*\"\sheight\=\"0\"\swidth\=\"0\"\sstyle\=\"display:none;visibility:hidden\"\>\<\/iframe\>#ism', '', $chunk);

		return $chunk;
	}

	/**
	 * Find out some basic information about this site and its setup
	 */
	private function bestpracticesecurityAction()
	{
		bfLog::log('=============================================');
		bfLog::log('=========bestpracticesecurityAction==========');
		bfLog::log('=============================================');

		$this->platform = 'Joomla';

		//8192029
		$this->db->setQuery("UPDATE bf_files SET hacked = 1, suspectcontent = 1 WHERE size = '8192029'");
		$this->db->query();

		// Am I hacked?
		$this->hacked = $this->checkIfHackedSite();

		// Mark PHP in places PHP should not be!
		if ($ids = $this->_phpInWrongPlaces())
		{
			$this->db->setQuery("UPDATE bf_files SET `suspectcontent` = 1 WHERE id IN (" . implode(',', $ids) . ")");
			$this->db->query();
		}

		// Remove OUR stuff as we dont need to report on that
		$this->db->setQuery("DELETE FROM bf_files WHERE
				filewithpath = '/plugins/system/j15_bfnetwork.xml'
				OR filewithpath = '/plugins/system/j25_30_bfnetwork.xml'
				OR filewithpath = '%.myjoomla.ignore.files'
				OR filewithpath = '%.myjoomla.ignore.folder'
				OR filewithpath LIKE '/plugins/system/bfnetwork%'");
		$this->db->query();

		// Remove OUR stuff as we dont need to report on that
		$this->db->setQuery("DELETE FROM bf_folders WHERE folderwithpath LIKE '/plugins/system/bfnetwork%'");
		$this->db->query();

		// Report count of all .htaccess files
		$this->db->setQuery('SELECT COUNT(*) FROM bf_files WHERE filewithpath LIKE "%.htaccess"');
		$this->htaccess_files = $this->db->LoadResult();

		// Report count of all files with 777 permissions
		$this->db->setQuery('SELECT COUNT(*) FROM bf_files WHERE fileperms LIKE "%777%"');
		$this->files_777 = $this->db->LoadResult();

		$this->db->setQuery('SELECT COUNT(*) FROM bf_files WHERE size = 0');
		$this->zerobytes = $this->db->LoadResult();

		// Report count of all folders with 777 permissions
		$this->db->setQuery('SELECT COUNT(*) FROM bf_folders WHERE folderinfo LIKE "%777%"');
		$this->folders_777 = $this->db->LoadResult();

		// Report all hidden folders like .git or .svn
		$this->db->setQuery('SELECT COUNT(*) FROM bf_folders WHERE folderwithpath LIKE "%/.%"');
		$this->hidden_folders = $this->db->LoadResult();

		// Report all hidden files like .htaccess .hack
		$this->db->setQuery('SELECT COUNT(*) FROM bf_files WHERE filewithpath LIKE "%/.%"');
		$this->hidden_files = $this->db->LoadResult();

		// Report nested Joomla versions
		$this->db->setQuery('SELECT COUNT(*) FROM bf_files WHERE filewithpath LIKE "%/administrator/index.php"');
		$this->nestedinstalls = $this->db->LoadResult();

		// Report file what might have been renamed to hide
		$this->db->setQuery('SELECT COUNT(*) FROM bf_files WHERE filewithpath LIKE "%.old%" OR filewithpath LIKE "%.bak%" OR filewithpath LIKE "%.backup%"');
		$this->renamedtohidefiles = $this->db->LoadResult();

		// Report any error_log files
		$this->db->setQuery('SELECT COUNT(*) FROM bf_files WHERE filewithpath LIKE "%error_log"');
		$this->error_logs_seen = $this->db->LoadResult();

		// Report files in the /tmp folder
		$this->db->setQuery('SELECT count(*) FROM bf_files WHERE filewithpath LIKE "/tmp%" AND filewithpath != "/tmp/index.html"');
		$this->tmp_install_folders = $this->db->LoadResult();

		// Report any encrypted files
		$this->db->setQuery('SELECT COUNT(*) FROM bf_files WHERE encrypted = 1');
		$this->encrypted_files = $this->db->LoadResult();

		// Report suspect files
		$this->db->setQuery('SELECT COUNT(*) FROM bf_files WHERE suspectcontent = 1');
		$this->suspectfiles = $this->db->LoadResult();

		// Report mailer files
		$this->db->setQuery('SELECT COUNT(*) FROM bf_files WHERE mailer = 1');
		$this->mailer = $this->db->LoadResult();

		// Report uploader files
		$this->db->setQuery('SELECT COUNT(*) FROM bf_files WHERE uploader = 1');
		$this->uploader = $this->db->LoadResult();

		// php.ini files
		$this->db->setQuery('SELECT COUNT(*) FROM bf_files WHERE filewithpath LIKE "%php.ini%"');
		$this->phpiniseen = $this->db->LoadResult();

		// count of non core files
		$this->db->setQuery('SELECT COUNT(*) FROM bf_files WHERE iscorefile is null');
		$this->notcorefiles = $this->db->LoadResult();

		$sql = "SELECT count(*) from `bf_core_hashes`
                    WHERE filewithpath NOT IN (
                        SELECT filewithpath from bf_files
                    )
                    AND filewithpath NOT LIKE '/installation/%'
                    AND filewithpath != '/robots.txt.dist'
                    AND filewithpath != '/administrator/manifests/packages/pkg_weblinks.xml'
                    AND filewithpath != '/'
                    AND filewithpath != '/robots.txt.dist'
                    AND filewithpath != '/web.config.txt'
                    AND filewithpath != '/joomla.xml'
                    AND filewithpath != '/build.xml'
                    AND filewithpath != '/LICENSE.txt'
                    AND filewithpath != '/README.txt'
                    AND filewithpath != '/htaccess.txt'
                    AND filewithpath != '/LICENSES.php'
                    AND filewithpath != '/configuration.php-dist'
                    AND filewithpath != '/CHANGELOG.php'
                    AND filewithpath != '/COPYRIGHT.php'
                    AND filewithpath != '/CREDITS.php'
                    AND filewithpath != '/INSTALL.php'
                    AND filewithpath != '/LICENSE.php'
                    AND filewithpath != '/CONTRIBUTING.md'
                    AND filewithpath != '/phpunit.xml.dist'
                    AND filewithpath != '/README.md'
                    AND filewithpath != '/.travis.yml'
                    AND filewithpath != '/travisci-phpunit.xml'
                    AND filewithpath != '/images/banners/osmbanner1.png'
                    AND filewithpath != '/images/banners/osmbanner2.png'
                    AND filewithpath != '/images/banners/shop-ad-books.jpg'
                    AND filewithpath != '/images/banners/shop-ad.jpg'
                    AND filewithpath != '/images/banners/white.png'
                    AND filewithpath != '/images/headers/blue-flower.jpg'
                    AND filewithpath != '/images/headers/maple.jpg'
                    AND filewithpath != '/images/headers/raindrops.jpg'
                    AND filewithpath != '/images/headers/walden-pond.jpg'
                    AND filewithpath != '/images/headers/windows.jpg'
                    AND filewithpath != '/images/joomla_black.gif'
                    AND filewithpath != '/images/joomla_black.png'
                    AND filewithpath != '/images/joomla_green.gif'
                    AND filewithpath != '/images/joomla_logo_black.jpg'
                    AND filewithpath != '/images/powered_by.png'
                    AND filewithpath != '/images/sampledata/fruitshop/apple.jpg'
                    AND filewithpath != '/images/sampledata/fruitshop/bananas_2.jpg'
                    AND filewithpath != '/images/sampledata/fruitshop/fruits.gif'
                    AND filewithpath != '/images/sampledata/fruitshop/tamarind.jpg'
                    AND filewithpath != '/images/sampledata/parks/animals/180px_koala_ag1.jpg'
                    AND filewithpath != '/images/sampledata/parks/animals/180px_wobbegong.jpg'
                    AND filewithpath != '/images/sampledata/parks/animals/200px_phyllopteryx_taeniolatus1.jpg'
                    AND filewithpath != '/images/sampledata/parks/animals/220px_spottedquoll_2005_seanmcclean.jpg'
                    AND filewithpath != '/images/sampledata/parks/animals/789px_spottedquoll_2005_seanmcclean.jpg'
                    AND filewithpath != '/images/sampledata/parks/animals/800px_koala_ag1.jpg'
                    AND filewithpath != '/images/sampledata/parks/animals/800px_phyllopteryx_taeniolatus1.jpg'
                    AND filewithpath != '/images/sampledata/parks/animals/800px_wobbegong.jpg'
                    AND filewithpath != '/images/sampledata/parks/banner_cradle.jpg'
                    AND filewithpath != '/images/sampledata/parks/landscape/120px_pinnacles_western_australia.jpg'
                    AND filewithpath != '/images/sampledata/parks/landscape/120px_rainforest_bluemountainsnsw.jpg'
                    AND filewithpath != '/images/sampledata/parks/landscape/180px_ormiston_pound.jpg'
                    AND filewithpath != '/images/sampledata/parks/landscape/250px_cradle_mountain_seen_from_barn_bluff.jpg'
                    AND filewithpath != '/images/sampledata/parks/landscape/727px_rainforest_bluemountainsnsw.jpg'
                    AND filewithpath != '/images/sampledata/parks/landscape/800px_cradle_mountain_seen_from_barn_bluff.jpg'
                    AND filewithpath != '/images/sampledata/parks/landscape/800px_ormiston_pound.jpg'
                    AND filewithpath != '/images/sampledata/parks/landscape/800px_pinnacles_western_australia.jpg'
                    AND filewithpath != '/images/sampledata/parks/parks.gif'
                    ";
		$this->db->setQuery($sql);
		$this->missingcorefiles = $this->db->LoadResult();

		$this->db->setQuery('SHOW TABLES LIKE "bf_files_last"');
		if ($this->db->loadResult())
		{
			$sql = "select count(*) from `bf_files` as new
                  LEFT JOIN bf_files_last as old ON old.filewithpath = new.filewithpath
                  WHERE old.currenthash != new.currenthash";
			$this->db->setQuery($sql);
			$this->modifiedfilessincelastaudit = $this->db->LoadResult();
		}


		// has_robots_modified
		$sql = 'SELECT c.ch as core_hash,  my.ch as my_hash FROM (
                        SELECT core.hash as ch
                            FROM bf_core_hashes AS core
                        WHERE
                            core.filewithpath = "/robots.txt"
                        OR
                            core.filewithpath = "/robots.txt.dist"
                            LIMIT 1
                        )	as c, (
                        SELECT bf_files.currenthash as ch
                            FROM bf_files
                        WHERE
                            bf_files.filewithpath = "/robots.txt"
                            LIMIT 1
                        )	as my';
		$this->db->setQuery($sql);
		$row = $this->db->loadAssocList();
		if ($row)
		{
			if ($row[0]['core_hash'] && $row[0]['my_hash'] && ($row[0]['core_hash'] === $row[0]['my_hash']))
			{
				$this->has_robots_modified = 0;
			}
			else
			{
				$this->has_robots_modified = 1;
			}
		}
		else
		{
			$this->has_robots_modified = 0;
		}

		// Files over 2Mb
		$this->db->setQuery('SELECT COUNT(*) FROM bf_files WHERE SIZE > 2097152');
		$this->large_files = $this->db->LoadResult();

		// Doing this again here now that I refactored the audit for perfomance I now need to do this much later on :-(
		$this->hashfailedcount = $this->gethashfailurecountAction(TRUE);

		// Report if we have default user ids
		$this->user_hasdefaultuserids = $this->_hasDefaultUserids();

		// PhP in wrong places
		$phpInWrongPlaces      = $this->_phpInWrongPlaces();
		$this->phpinwrongplace = $phpInWrongPlaces ? count($phpInWrongPlaces) : 0;

		/**
		 * @todo add these http://en.wikipedia.org/wiki/List_of_archive_formats
		 */
		// Report all archives
		$this->db->setQuery('SELECT COUNT(*) FROM bf_files WHERE
		filewithpath LIKE "%.zip"
		OR filewithpath LIKE "%.tar"
		OR filewithpath LIKE "%.tar.gz"
		OR filewithpath LIKE "%.bz2"
		OR filewithpath LIKE "%.gzip"
		OR filewithpath LIKE "%.bzip2"');
		$this->archive_files = $this->db->LoadResult();

		$this->nextStepPlease(TRUE);
	}

	/**
	 * Run some very specific checks to see if this site is hacked or not
	 */
	private function checkIfHackedSite()
	{
		$this->db->setQuery("SELECT count(*) FROM bf_files WHERE hacked = 1");
		return $this->db->loadResult();
	}

	/**
	 * @return mixed
	 */
	private function _phpInWrongPlaces()
	{
		$idsSql = "SELECT id FROM bf_files AS b WHERE filewithpath REGEXP '^/images/.*\.php$'"; // OR filewithpath REGEXP '^/media/.*\.php$'
		$this->db->setQuery($idsSql);
		if (method_exists($this->db, 'loadColumn'))
		{
			$ids = $this->db->loadColumn();
		}
		else
		{
			$ids = $this->db->loadResultArray();
		}

		return $ids;
	}

	/**
	 * Count how many core files failed their hash checks
	 */
	private function gethashfailurecountAction($internal = FALSE)
	{
		$sql = 'SELECT COUNT(*) FROM bf_files WHERE iscorefile = 1 AND hashfailed = 1';
		$this->db->setQuery($sql);
		$this->hashfailedcount = $this->db->LoadResult();

		if (FALSE === $internal)
		{
			// move onto the next step
			$this->nextStepPlease();
		}
		else
		{
			return $this->hashfailedcount;
		}
	}

	/**
	 * Do we have any default ids
	 *
	 * @return int
	 */
	private function _hasDefaultUserids()
	{
		$this->db->setQuery("SELECT COUNT(*) FROM #__users WHERE id IN (62 , 42)");

		return $this->db->loadResult();
	}
}
