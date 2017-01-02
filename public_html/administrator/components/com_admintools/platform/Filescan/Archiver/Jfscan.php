<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Engine\Archiver;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Factory;
use Psr\Log\LogLevel;

// Load the diff engine
require_once __DIR__ . '/../Util/diff.php';

class Jfscan extends Base
{
	/**
	 * Should I generate diffs for each modified file?
	 *
	 * @var  bool
	 */
	private $generateDiff = null;

	/**
	 * Should I ignore files with zero threat score?
	 *
	 * @var  bool
	 */
	private $ignoreNonThreats = null;

	/**
	 * Common code which gets called on instance creation or wake-up (unserialization). Reloads the component's
	 * parameters.
	 *
	 * @return  void
	 */
	protected function __bootstrap_code()
	{
		if (is_null($this->generateDiff))
		{
			\JLoader::import('joomla.html.parameter');
			\JLoader::import('joomla.application.component.helper');

			$db  = \JFactory::getDbo();
			$sql = $db->getQuery(true)
					  ->select($db->qn('params'))
					  ->from($db->qn('#__extensions'))
					  ->where($db->qn('type') . ' = ' . $db->q('component'))
					  ->where($db->qn('element') . ' = ' . $db->q('com_admintools'));
			$db->setQuery($sql);
			$rawparams = $db->loadResult();
			$params    = new \JRegistry();
			$params->loadString($rawparams, 'JSON');

			$this->generateDiff     = $params->get('scandiffs', false);
			$this->ignoreNonThreats = $params->get('scanignorenonthreats', false);
			$email                  = $params->get('scanemail', '');

			Factory::getConfiguration()->set('admintools.scanner.email', $email);
		}

		parent::__bootstrap_code();
	}

	/**
	 * Initialises the archiver class. We are required by the Akeeba Engine API to define this method. However, since
	 * we don't have a backup archive to initialise we simply have an empty body.
	 *
	 * @param    string $targetArchivePath Absolute path to the generated archive
	 * @param    array  $options           A named key array of options (optional)
	 *
	 * @return  void
	 */
	public function initialize($targetArchivePath, $options = array())
	{
	}

	/**
	 * Required by the Akeeba Engine API to perform whatever finalization is needed for the archive to be considered
	 * complete and useful. Since we don't have an archive we simply have an empty body.
	 *
	 * @return  void
	 */
	public function finalize()
	{
	}

	/**
	 * Returns a string with the extension (including the dot) of the files produced by this archiver class. Since we
	 * do not create archives we just return an empty string.
	 *
	 * @return  string
	 */
	public function getExtension()
	{
		return '';
	}

	/**
	 * Called by Akeeba Engine when it needs to "add a file / folder to the archive". Since we're just doing file
	 * scanning in this class we ignore directories and only perform scanning on files meeting our criteria (they
	 * have the right extension). This is the brains of the scanner.
	 *
	 * @param   boolean $isVirtual        If true, the next parameter contains file data instead of a file name
	 * @param   string  $sourceNameOrData Absolute file name to read data from or the file data itself is $isVirtual is
	 *                                    true
	 * @param   string  $targetName       The (relative) file name under which to store the file in the archive
	 *
	 * @return  boolean  True on success, false otherwise
	 */
	protected function _addFile($isVirtual, &$sourceNameOrData, $targetName)
	{
		if ($isVirtual)
		{
			return true;
		}

		$extensions =
			explode('|', Factory::getConfiguration()->get('akeeba.basic.file_extensions', 'php|phps|php3|inc'));
		$ignore     = true;

		foreach ($extensions as $extension)
		{
			if (('.' . $extension) == (substr($targetName, -(strlen($extension) + 1))))
			{
				$ignore = false;

				break;
			}
		}

		if ($ignore)
		{
			Factory::getLog()->log(LogLevel::DEBUG, "Skipped $targetName");
			unset($extensions);

			return true;
		}

		unset($extensions);

		Factory::getLog()->log(LogLevel::DEBUG, "Scanning $targetName (extension: $extension)");

		// Count one more file scanned
		$multipart = Factory::getConfiguration()->get('volatile.statistics.multipart', 0);
		$multipart++;
		Factory::getConfiguration()->set('volatile.statistics.multipart', $multipart);

		$filedata = (object)array(
			'path'       => $targetName,
			'filedate'   => @filemtime($sourceNameOrData),
			'filesize'   => @filesize($sourceNameOrData),
			'data'       => '',
			'checksum'   => md5_file($sourceNameOrData),
			'sourcePath' => $sourceNameOrData,
		);

		if ($this->generateDiff)
		{
			$filedata->data = gzdeflate(@file_get_contents($sourceNameOrData), 9);
		}

		$db = \JFactory::getDbo();

		if (class_exists('ReflectionClass') && (count($db->getLog()) > 100))
		{
			// I need to reset the query log, otherwise it consumes all available memory and crashes our code.
			$mirror = new \ReflectionClass($db);
			if ($mirror->hasProperty('log'))
			{
				$property = $mirror->getProperty('log');
				$property->setAccessible(true);
				$property->setValue($db, array());
			}
		}

		$sql = $db->getQuery(true)
				  ->select('*')
				  ->from($db->qn('#__admintools_filescache'))
				  ->where($db->qn('path') . ' = ' . $db->q($targetName));
		$db->setQuery($sql, 0, 1);
		$oldRecord = $db->loadObject();

		if (!is_null($oldRecord))
		{
			// Check for changes
			$fileModified = false;

			if ($oldRecord->filedate != $filedata->filedate)
			{
				$fileModified = true;
			}

			if ($oldRecord->filesize != $filedata->filesize)
			{
				$fileModified = true;
			}

			if ($oldRecord->checksum != $filedata->checksum)
			{
				$fileModified = true;
			}

			if ($fileModified)
			{
				// ### MODIFIED FILE ###
				$this->_logFileChange($filedata, $oldRecord);
				unset($oldRecord);

				// Replace the old record
				$sql = $db->getQuery(true)
						  ->delete($db->qn('#__admintools_filescache'))
						  ->where($db->qn('path') . ' = ' . $db->q($targetName));
				$db->setQuery($sql);
				$db->execute();

				unset($filedata->sourcePath);
				$db->insertObject('#__admintools_filescache', $filedata);
			}
			else
			{
				unset($oldRecord);

				// Existing file. Get the last log record.
				$sql = $db->getQuery(true)
						  ->select('*')
						  ->from($db->qn('#__admintools_scanalerts'))
						  ->where($db->qn('path') . ' = ' . $db->q($targetName))
						  ->order($db->qn('scan_id') . ' DESC');

				$db->setQuery($sql, 0, 1);
				$lastRecord = $db->loadObject();

				// If the file is not "acknowledged", we have to
				// check its threat score.
				if (is_object($lastRecord))
				{
					if ($lastRecord->acknowledged)
					{
						unset($lastRecord);

						return true;
					}
				}

				unset($lastRecord);

				// Not acknowledged. Proceed.
				$text        = @file_get_contents($sourceNameOrData);
				$threatScore = $this->_getThreatScore($text);

				if ($threatScore == 0)
				{
					return true;
				}

				// ### SUSPICIOUS EXISTING FILE ###

				// Still here? It's a possible threat! Log it as a modified file.
				$alertRecord = array(
					'path'         => $targetName,
					'scan_id'      => Factory::getStatistics()->getId(),
					'diff'         => "###SUSPICIOUS FILE###\n",
					'threat_score' => $threatScore,
					'acknowledged' => 0
				);

				if ($this->generateDiff)
				{
					$alertRecord['diff'] = <<<ENDFILEDATA
###SUSPICIOUS FILE###
>> Admin Tools detected that this file contains potentially suspicious code.
>> This DOES NOT necessarily mean that it is a hacking script. There is always
>> the possibility of a false alarm. The contents of the file are included
>> below this line so that you can review them.
$text
ENDFILEDATA;
				}

				unset($text);
				$alertRecord = (object)$alertRecord;
				$db->insertObject('#__admintools_scanalerts', $alertRecord);
				unset($alertRecord);
			}
		}
		else
		{
			// ### NEW FILE ###
			$this->_logFileChange($filedata);

			// Add a new file record
			unset($filedata->sourcePath);
			$db->insertObject('#__admintools_filescache', $filedata);
			unset($filedata);
		}

		return true;
	}

	/**
	 * Adds a log entry to the #__admintools_scanalerts table, marking a modified, added or suspicious file.
	 *
	 * @param   \stdClass       $newFileRecord  The record of the current version of the file
	 * @param   \stdClass|null  $oldFileRecord  The record of the old version of the file (or null if it's an added file)
	 *
	 * @return  void
	 */
	private function _logFileChange(&$newFileRecord, &$oldFileRecord = null)
	{
		// Initialise the new alert record
		$alertRecord = array(
			'path'         => $newFileRecord->path,
			'scan_id'      => Factory::getStatistics()->getId(),
			'diff'         => '',
			'threat_score' => 0,
			'acknowledged' => 0
		);

		// Produce the diff if there is an old file
		if (!is_null($oldFileRecord))
		{
			if ($this->generateDiff)
			{
				// Modified file, generate diff
				$newText  = gzinflate($newFileRecord->data);
				$newText  = str_replace("\r\n", "\n", $newText);
				$newText  = str_replace("\r", "\n", $newText);
				$newLines = explode("\n", $newText);
				unset($newText);

				$oldText  = gzinflate($oldFileRecord->data);
				$oldText  = str_replace("\r\n", "\n", $oldText);
				$oldText  = str_replace("\r", "\n", $oldText);
				$oldLines = explode("\n", $oldText);
				unset($oldText);

				$diffObject          = new \Diff($oldLines, $newLines);
				$renderer            = new \Diff_Renderer_Text_Unified();
				$alertRecord['diff'] = $diffObject->render($renderer);
				unset($renderer);
				unset($diffObject);
				unset($newLines);
				unset($oldLines);

				$alertRecord['threat_score'] = $this->_getThreatScore($alertRecord['diff']);
			}
			else
			{
				// Modified file, do not generate diff
				$alertRecord['diff']         = "###MODIFIED FILE###\n";
				$newText                     = @file_get_contents($newFileRecord->sourcePath);
				$alertRecord['threat_score'] = $this->_getThreatScore($newText);
				unset($newText);
			}
		}
		else
		{
			// New file
			$newText                     = @file_get_contents($newFileRecord->sourcePath);
			$alertRecord['threat_score'] = $this->_getThreatScore($newText);
			unset($newText);
		}

		// Do not create a record for non-threat files
		if ($this->ignoreNonThreats && !$alertRecord['threat_score'])
		{
			return;
		}

		$alertRecord = (object)$alertRecord;
		$db          = \JFactory::getDbo();
		$db->insertObject('#__admintools_scanalerts', $alertRecord);
		unset($alertRecord);
	}

	/**
	 * Performs a threat score assessment on the given file contents.
	 *
	 * @param   string  $text  The file contents to scan
	 *
	 * @return  int
	 */
	private function _getThreatScore($text)
	{
		// These are the lists of signatures, initially empty
		static $suspiciousWords = null;
		static $knownHackSignatures = null;
		static $suspiciousRegEx = null;

		// ****
		// Note to self: The encoded configuration is built by the build/hacksignatures/create_lists.php
		// ****
		//
		// Build the lists of signatures from the encoded, compressed configuration.
		//
		// We have to go through this silly method because some eager malware scanners would consider the signatures
		// as an indication that this is a hacking script thus renaming or deleting the file, or even suspending the
		// hosting account! Ironically enough, thinking as a real hacker (zip and hex encode the part of the file
		// triggering the malware scanner) is enough to bypass this kind of protection.
		if (is_null($suspiciousWords) || is_null($knownHackSignatures) || is_null($suspiciousRegEx))
		{
			/** @var string $encodedConfig Defined in the included file */
			require_once __DIR__ . '/../../encodedconfig.php';

			$zipped = pack('H*', $encodedConfig);
			unset($encodedConfig);

			$json_encoded = gzinflate($zipped);
			unset($zipped);

			$new_list = json_decode($json_encoded, true);
			extract($new_list);

			unset($new_list);
		}

		$score = 0;
		$hits  = 0;
		$count = 0;

		foreach ($suspiciousWords as $word)
		{
			$count = substr_count($text, $word);

			if ($count)
			{
				$hits += $count;
				$score += $count;
			}
		}

		foreach ($knownHackSignatures as $signature => $sigscore)
		{
			$count = substr_count($text, $signature);

			if ($count)
			{
				$hits += $count;
				$score += $count * $sigscore;
			}
		}

		foreach ($suspiciousRegEx as $pattern => $value)
		{
			$count = preg_match_all($pattern, $text, $matches);

			if ($count)
			{
				$hits += $count;
				$score += $value * $count;
			}
		}

		unset($count);

		if ($hits == 0)
		{
			unset($hits);

			return 0;
		}

		unset($hits);

		return (int)$score;
	}
}