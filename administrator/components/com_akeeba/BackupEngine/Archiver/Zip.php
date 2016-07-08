<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 *
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 *
 */

namespace Akeeba\Engine\Archiver;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Factory;
use Akeeba\Engine\Util\CRC32;
use Psr\Log\LogLevel;

class Zip extends Base
{
	/** @var string Beginning of central directory record. */
	private $_ctrlDirHeader = "\x50\x4b\x01\x02";

	/** @var string End of central directory record. */
	private $_ctrlDirEnd = "\x50\x4b\x05\x06";

	/** @var string Beginning of file contents. */
	private $_fileHeader = "\x50\x4b\x03\x04";

	/** @var string The name of the temporary file holding the ZIP's Central Directory */
	private $_ctrlDirFileName;

	/** @var string The name of the file holding the ZIP's data, which becomes the final archive */
	private $_dataFileName;

	/** @var integer The total number of files and directories stored in the ZIP archive */
	private $_totalFileEntries;

	/** @var integer The total size of data in the archive. Note: On 32-bit versions of PHP, this will overflow for archives over 2Gb! */
	private $_totalDataSize = 0;

	/** @var integer The chunk size for CRC32 calculations */
	private $AkeebaPackerZIP_CHUNK_SIZE;

	/** @var bool Should I use Split ZIP? */
	private $_useSplitZIP = false;

	/** @var int Maximum fragment size, in bytes */
	private $_fragmentSize = 0;

	/** @var int Current fragment number */
	private $_currentFragment = 1;

	/** @var int Total number of fragments */
	private $_totalFragments = 1;

	/** @var string Archive full path without extension */
	private $_dataFileNameBase = '';

	/** @var bool Should I store symlinks as such (no dereferencing?) */
	private $_symlink_store_target = false;

	/** @var CRC32 The CRC32 calculations object */
	private $crcCalculator = null;

	/**
	 * Extend the bootstrap code to add some define's used by the ZIP format engine
	 *
	 * @return  void
	 */
	protected function __bootstrap_code()
	{
		if ( !defined('_AKEEBA_COMPRESSION_THRESHOLD'))
		{
			$config = Factory::getConfiguration();
			define("_AKEEBA_COMPRESSION_THRESHOLD", $config->get('engine.archiver.common.big_file_threshold')); // Don't compress files over this size
			define("_AKEEBA_DIRECTORY_READ_CHUNK", $config->get('engine.archiver.zip.cd_glue_chunk_size')); // How much data to read at once when finalizing ZIP archives
		}

		$this->crcCalculator = Factory::getCRC32Calculator();

		parent::__bootstrap_code();
	}

	/**
	 * Class constructor - initializes internal operating parameters
	 *
	 * @return Zip
	 */
	public function __construct()
	{
		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . " :: New instance");

		// Get chunk override
		$registry = Factory::getConfiguration();

		if ($registry->get('engine.archiver.common.chunk_size', 0) > 0)
		{
			$this->AkeebaPackerZIP_CHUNK_SIZE = AKEEBA_CHUNK;
		}
		else
		{
			// Try to use as much memory as it's possible for CRC32 calculation
			$memLimit = ini_get("memory_limit");

			if (strstr($memLimit, 'M'))
			{
				$memLimit = (int)$memLimit * 1048576;
			}
			elseif (strstr($memLimit, 'K'))
			{
				$memLimit = (int)$memLimit * 1024;
			}
			elseif (strstr($memLimit, 'G'))
			{
				$memLimit = (int)$memLimit * 1073741824;
			}
			else
			{
				$memLimit = (int)$memLimit;
			}

			// 1.2a3 -- Rare case with memory_limit < 0, e.g. -1Mb!
			if (is_numeric($memLimit) && ($memLimit < 0))
			{
				$memLimit = "";
			}

			if (($memLimit == ""))
			{
				// No memory limit, use 2Mb chunks (fairly large, right?)
				$this->AkeebaPackerZIP_CHUNK_SIZE = 2097152;
			}
			elseif (function_exists("memory_get_usage"))
			{
				// PHP can report memory usage, see if there's enough available memory; the containing application / CMS alone eats about 5-6Mb! This code is called on files <= 1Mb
				$memLimit     = $this->_return_bytes($memLimit);
				$availableRAM = $memLimit - memory_get_usage();

				if ($availableRAM <= 0)
				{
					// Some PHP implemenations also return the size of the httpd footprint!
					if (($memLimit - 6291456) > 0)
					{
						$this->AkeebaPackerZIP_CHUNK_SIZE = $memLimit - 6291456;
					}
					else
					{
						$this->AkeebaPackerZIP_CHUNK_SIZE = 2097152;
					}
				}
				else
				{
					$this->AkeebaPackerZIP_CHUNK_SIZE = $availableRAM * 0.5;
				}
			}
			else
			{
				// PHP can't report memory usage, use a conservative 512Kb
				$this->AkeebaPackerZIP_CHUNK_SIZE = 524288;
			}
		}

		// NEW 2.3: Should we enable Split ZIP feature?
		$fragmentsize = $registry->get('engine.archiver.common.part_size', 0);

		if ($fragmentsize >= 65536)
		{
			// If the fragment size is AT LEAST 64Kb, enable Split ZIP
			$this->_useSplitZIP  = true;
			$this->_fragmentSize = $fragmentsize;
			// Indicate that we have at least 1 part
			$statistics = Factory::getStatistics();
			$statistics->updateMultipart(1);
		}

		// NEW 2.3: Should I use Symlink Target Storage?
		$dereferencesymlinks = $registry->get('engine.archiver.common.dereference_symlinks', true);

		if (!$dereferencesymlinks)
		{
			// We are told not to dereference symlinks. Are we on Windows?
			if (function_exists('php_uname'))
			{
				$isWindows = stristr(php_uname(), 'windows');
			}
			else
			{
				$isWindows = (DIRECTORY_SEPARATOR == '\\');
			}

			// If we are not on Windows, enable symlink target storage
			$this->_symlink_store_target = !$isWindows;
		}

		Factory::getLog()->log(LogLevel::DEBUG, "Chunk size for CRC is now " . $this->AkeebaPackerZIP_CHUNK_SIZE . " bytes");

		parent::__construct();
	}

	/**
	 * Initialises the archiver class, creating the archive from an existent
	 * installer's JPA archive.
	 *
	 * @param string $sourceJPAPath     Absolute path to an installer's JPA archive
	 * @param string $targetArchivePath Absolute path to the generated archive
	 * @param array  $options           A named key array of options (optional). This is currently not supported
	 *
	 * @return void
	 */
	public function initialize($targetArchivePath, $options = array())
	{
		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . " :: initialize - archive $targetArchivePath");

		// Get names of temporary files
		$configuration          = Factory::getConfiguration();
		$this->_ctrlDirFileName = tempnam($configuration->get('akeeba.basic.output_directory'), 'akzcd');
		$this->_dataFileName    = $targetArchivePath;

		// If we use splitting, initialize
		if ($this->_useSplitZIP)
		{
			Factory::getLog()->log(LogLevel::INFO, __CLASS__ . " :: Split ZIP creation enabled");

			$this->_dataFileNameBase = dirname($targetArchivePath) . '/' . basename($targetArchivePath, '.zip');
			$this->_dataFileName     = $this->_dataFileNameBase . '.z01';
		}

		$this->_ctrlDirFileName = basename($this->_ctrlDirFileName);
		$pos                    = strrpos($this->_ctrlDirFileName, '/');

		if ($pos !== false)
		{
			$this->_ctrlDirFileName = substr($this->_ctrlDirFileName, $pos + 1);
		}

		$pos = strrpos($this->_ctrlDirFileName, '\\');

		if ($pos !== false)
		{
			$this->_ctrlDirFileName = substr($this->_ctrlDirFileName, $pos + 1);
		}

		$this->_ctrlDirFileName = Factory::getTempFiles()->registerTempFile($this->_ctrlDirFileName);

		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . " :: CntDir Tempfile = " . $this->_ctrlDirFileName);

		// Create temporary file
		if ( !@touch($this->_ctrlDirFileName))
		{
			$this->setError("Could not open temporary file for ZIP archiver. Please check your temporary directory's permissions!");

			return;
		}

		if (function_exists('chmod'))
		{
			chmod($this->_ctrlDirFileName, 0666);
		}

		// Try to kill the archive if it exists
		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . " :: Killing old archive");
		$this->fp = $this->_fopen($this->_dataFileName, "wb");

		if ( !($this->fp === false))
		{
			ftruncate($this->fp, 0);
		}
		else
		{
			@unlink($this->_dataFileName);
		}

		if ( !@touch($this->_dataFileName))
		{
			$this->setError("Could not open archive file for ZIP archiver. Please check your output directory's permissions!");

			return;
		}

		if (function_exists('chmod'))
		{
			chmod($this->_dataFileName, 0666);
		}

		// On split archives, include the "Split ZIP" header, for PKZIP 2.50+ compatibility
		if ($this->_useSplitZIP)
		{
			file_put_contents($this->_dataFileName, "\x50\x4b\x07\x08");

			// Also update the statistics table that we are a multipart archive...
			$statistics = Factory::getStatistics();
			$statistics->updateMultipart(1);
		}
	}

	/**
	 * Creates the ZIP file out of its pieces.
	 * Official ZIP file format: http://www.pkware.com/appnote.txt
	 *
	 * @return void
	 */
	public function finalize()
	{
		// 1. Get size of central directory
		clearstatcache();
		$cdOffset = @filesize($this->_dataFileName);
		$this->_totalDataSize += $cdOffset;
		$cdSize = @filesize($this->_ctrlDirFileName);

		// 2. Append Central Directory to data file and remove the CD temp file afterwards
		if ( !is_null($this->fp))
		{
			$this->_fclose($this->fp);
		}

		if ( !is_null($this->cdfp))
		{
			$this->_fclose($this->cdfp);
		}

		$this->fp   = $this->_fopen($this->_dataFileName, "ab");
		$this->cdfp = $this->_fopen($this->_ctrlDirFileName, "rb");

		if ($this->fp === false)
		{
			$this->setError('Could not open ZIP data file ' . $this->_dataFileName . ' for reading');

			return;
		}

		if ($this->cdfp === false)
		{
			// Already glued, return
			$this->_fclose($this->fp);
			$this->fp   = null;
			$this->cdfp = null;

			return;
		}

		if ( !$this->_useSplitZIP)
		{
			while ( !feof($this->cdfp))
			{
				$chunk = fread($this->cdfp, _AKEEBA_DIRECTORY_READ_CHUNK);
				$this->_fwrite($this->fp, $chunk);

				if ($this->getError())
				{
					return;
				}
			}

			unset($chunk);

			$this->_fclose($this->cdfp);
		}
		else
			// Special considerations for Split ZIP
		{
			// Calculate size of Central Directory + EOCD records
			$comment_length     = function_exists('mb_strlen') ? mb_strlen($this->_comment, '8bit') : strlen($this->_comment);
			$total_cd_eocd_size = $cdSize + 22 + $comment_length;

			// Free space on the part
			clearstatcache();
			$current_part_size = @filesize($this->_dataFileName);
			$free_space        = $this->_fragmentSize - ($current_part_size === false ? 0 : $current_part_size);

			if (($free_space < $total_cd_eocd_size) && ($total_cd_eocd_size > 65536))
			{
				// Not enough space on archive for CD + EOCD, will go on separate part
				// Create new final part
				if ( !$this->_createNewPart(true))
				{
					// Die if we couldn't create the new part
					$this->setError('Could not create new ZIP part file ' . basename($this->_dataFileName));

					return;
				}

				// Close the old data file
				$this->_fclose($this->fp);

				// Open data file for output
				$this->fp = @$this->_fopen($this->_dataFileName, "ab");

				if ($this->fp === false)
				{
					$this->fp = null;
					$this->setError("Could not open archive file {$this->_dataFileName} for append!");

					return;
				}

				// Write the CD record
				while ( !feof($this->cdfp))
				{
					$chunk = fread($this->cdfp, _AKEEBA_DIRECTORY_READ_CHUNK);
					$this->_fwrite($this->fp, $chunk);

					if ($this->getError())
					{
						return;
					}
				}

				unset($chunk);

				$this->_fclose($this->cdfp);
				$this->cdfp = null;
			}
			else
			{
				// Glue the CD + EOCD on the same part if they fit, or anyway if they are less than 64Kb.
				// NOTE: WE *MUST NOT* CREATE FRAGMENTS SMALLER THAN 64Kb!!!!
				while ( !feof($this->cdfp))
				{
					$chunk = fread($this->cdfp, _AKEEBA_DIRECTORY_READ_CHUNK);
					$this->_fwrite($this->fp, $chunk);

					if ($this->getError())
					{
						return;
					}
				}

				unset($chunk);

				$this->_fclose($this->cdfp);
				$this->cdfp = null;
			}
		}

		Factory::getTempFiles()->unregisterAndDeleteTempFile($this->_ctrlDirFileName);

		// 3. Write the rest of headers to the end of the ZIP file
		$this->_fclose($this->fp);
		$this->fp = null;

		clearstatcache();

		$this->fp = $this->_fopen($this->_dataFileName, "ab");

		if ($this->fp === false)
		{
			$this->setError('Could not open ' . $this->_dataFileName . ' for append');

			return;
		}

		$this->_fwrite($this->fp, $this->_ctrlDirEnd);

		if ($this->getError())
		{
			return;
		}

		if ($this->_useSplitZIP)
		{
			// Split ZIP files, enter relevant disk number information
			$this->_fwrite($this->fp, pack('v', $this->_totalFragments - 1)); /* Number of this disk. */
			$this->_fwrite($this->fp, pack('v', $this->_totalFragments - 1)); /* Disk with central directory start. */
		}
		else
		{
			// Non-split ZIP files, the disk numbers MUST be 0
			$this->_fwrite($this->fp, pack('V', 0));
		}

		$this->_fwrite($this->fp, pack('v', $this->_totalFileEntries)); /* Total # of entries "on this disk". */
		$this->_fwrite($this->fp, pack('v', $this->_totalFileEntries)); /* Total # of entries overall. */
		$this->_fwrite($this->fp, pack('V', $cdSize)); /* Size of central directory. */
		$this->_fwrite($this->fp, pack('V', $cdOffset)); /* Offset to start of central dir. */
		$sizeOfComment = $comment_length = function_exists('mb_strlen') ? mb_strlen($this->_comment, '8bit') : strlen($this->_comment);

		// 2.0.b2 -- Write a ZIP file comment
		$this->_fwrite($this->fp, pack('v', $sizeOfComment)); /* ZIP file comment length. */
		$this->_fwrite($this->fp, $this->_comment);
		$this->_fclose($this->fp);

		// If Split ZIP and there is no .zip file, rename the last fragment to .ZIP
		if ($this->_useSplitZIP)
		{
			$extension = substr($this->_dataFileName, -3);

			if ($extension != '.zip')
			{
				Factory::getLog()->log(LogLevel::DEBUG, 'Renaming last ZIP part to .ZIP extension');

				$newName = $this->_dataFileNameBase . '.zip';

				if ( !@rename($this->_dataFileName, $newName))
				{
					$this->setError('Could not rename last ZIP part to .ZIP extension.');

					return;
				}

				$this->_dataFileName = $newName;
			}
		}

		// If Split ZIP and only one fragment, change the signature
		if ($this->_useSplitZIP && ($this->_totalFragments == 1))
		{
			$this->fp = $this->_fopen($this->_dataFileName, 'r+b');
			$this->_fwrite($this->fp, "\x50\x4b\x30\x30");
		}

		if (function_exists('chmod'))
		{
			@chmod($this->_dataFileName, 0755);
		}
	}

	/**
	 * Returns a string with the extension (including the dot) of the files produced
	 * by this class.
	 *
	 * @return string
	 */
	public function getExtension()
	{
		return '.zip';
	}

	/**
	 * The most basic file transaction: add a single entry (file or directory) to
	 * the archive.
	 *
	 * @param bool   $isVirtual        If true, the next parameter contains file data instead of a file name
	 * @param string $sourceNameOrData Absolute file name to read data from or the file data itself is $isVirtual is
	 *                                 true
	 * @param string $targetName       The (relative) file name under which to store the file in the archive
	 *
	 * @return bool True on success, false otherwise
	 */
	protected function _addFile($isVirtual, &$sourceNameOrData, $targetName)
	{
		static $configuration;

		// Note down the starting disk number for Split ZIP archives
		if ($this->_useSplitZIP)
		{
			$starting_disk_number_for_this_file = $this->_currentFragment - 1;
		}
		else
		{
			$starting_disk_number_for_this_file = 0;
		}

		if ( !$configuration)
		{
			$configuration = Factory::getConfiguration();
		}

		// Open data file for output
		if (is_null($this->fp))
		{
			$this->fp = @$this->_fopen($this->_dataFileName, "ab");
		}

		if ($this->fp === false)
		{
			$this->setError("Could not open archive file {$this->_dataFileName} for append!");

			return false;
		}

		if ( !$configuration->get('volatile.engine.archiver.processingfile', false))
		{
			// See if it's a directory
			$isDir = $isVirtual ? false : is_dir($sourceNameOrData);

			// See if it's a symlink (w/out dereference)
			$isSymlink = false;

			if ($this->_symlink_store_target && !$isVirtual)
			{
				$isSymlink = is_link($sourceNameOrData);
			}

			// Get real size before compression
			if ($isVirtual)
			{
				$fileSize = function_exists('mb_strlen') ? mb_strlen($sourceNameOrData, '8bit') : strlen($sourceNameOrData);
			}
			else
			{
				if ($isSymlink)
				{
					$fileSize = function_exists('mb_strlen') ? mb_strlen(@readlink($sourceNameOrData), '8bit') : strlen(@readlink($sourceNameOrData));
				}
				else
				{
					$fileSize = $isDir ? 0 : @filesize($sourceNameOrData);
				}
			}

			// Get last modification time to store in archive
			$ftime = $isVirtual ? time() : @filemtime($sourceNameOrData);

			// Decide if we will compress
			if ($isDir || $isSymlink)
			{
				$compressionMethod = 0; // don't compress directories...
			}
			else
			{
				// Do we have plenty of memory left?
				$memLimit = ini_get("memory_limit");

				if (strstr($memLimit, 'M'))
				{
					$memLimit = (int)$memLimit * 1048576;
				}
				elseif (strstr($memLimit, 'K'))
				{
					$memLimit = (int)$memLimit * 1024;
				}
				elseif (strstr($memLimit, 'G'))
				{
					$memLimit = (int)$memLimit * 1073741824;
				}
				else
				{
					$memLimit = (int)$memLimit;
				}

				if (($memLimit == "") || ($fileSize >= _AKEEBA_COMPRESSION_THRESHOLD))
				{
					// No memory limit, or over 1Mb files => always compress up to 1Mb files (otherwise it times out)
					$compressionMethod = ($fileSize <= _AKEEBA_COMPRESSION_THRESHOLD) ? 8 : 0;
				}
				elseif (function_exists("memory_get_usage"))
				{
					// PHP can report memory usage, see if there's enough available memory; the containing application / CMS alone eats about 5-6Mb! This code is called on files <= 1Mb
					$memLimit          = $this->_return_bytes($memLimit);
					$availableRAM      = $memLimit - memory_get_usage();
					$compressionMethod = (($availableRAM / 2.5) >= $fileSize) ? 8 : 0;
				}
				else
				{
					// PHP can't report memory usage, compress only files up to 512Kb (conservative approach) and hope it doesn't break
					$compressionMethod = ($fileSize <= 524288) ? 8 : 0;;
				}
			}

			$compressionMethod = function_exists("gzcompress") ? $compressionMethod : 0;

			$storedName = $targetName;

			if ($isVirtual)
			{
				Factory::getLog()->log(LogLevel::DEBUG, '  Virtual add:' . $storedName . ' (' . $fileSize . ') - ' . $compressionMethod);
			}

			/* "Local file header" segment. */
			$unc_len = $fileSize; // File size

			if ( !$isDir)
			{
				// Get CRC for regular files, not dirs
				if ($isVirtual)
				{
					$crc = crc32($sourceNameOrData);
				}
				else
				{
					$crc           = $this->crcCalculator->crc32_file($sourceNameOrData, $this->AkeebaPackerZIP_CHUNK_SIZE); // This is supposed to be the fast way to calculate CRC32 of a (large) file.

					// If the file was unreadable, $crc will be false, so we skip the file
					if ($crc === false)
					{
						$this->setWarning('Could not calculate CRC32 for ' . $sourceNameOrData);

						return false;
					}
				}
			}
			else if ($isSymlink)
			{
				$crc = crc32(@readlink($sourceNameOrData));
			}
			else
			{
				// Dummy CRC for dirs
				$crc = 0;
				$storedName .= "/";
				$unc_len = 0;
			}

			// If we have to compress, read the data in memory and compress it
			if ($compressionMethod == 8)
			{
				// Get uncompressed data
				if ($isVirtual)
				{
					$udata =& $sourceNameOrData;
				}
				else
				{
					$udata = @file_get_contents($sourceNameOrData); // PHP > 4.3.0 saves us the trouble
				}

				if ($udata === false)
				{
					// Unreadable file, skip it. Normally, we should have exited on CRC code above
					$this->setWarning('Unreadable file ' . $sourceNameOrData . '. Check permissions');

					return false;
				}
				else
				{
					// Proceed with compression
					$zdata = @gzcompress($udata);

					if ($zdata === false)
					{
						// If compression fails, let it behave like no compression was available
						$c_len             = $unc_len;
						$compressionMethod = 0;
					}
					else
					{
						unset($udata);

						$zdata = substr(substr($zdata, 0, -4), 2);
						$c_len = (function_exists('mb_strlen') ? mb_strlen($zdata, '8bit') : strlen($zdata));
					}
				}
			}
			else
			{
				$c_len = $unc_len;
			}

			/* Get the hex time. */
			$dtime = dechex($this->_unix2DosTime($ftime));

			if ((function_exists('mb_strlen') ? mb_strlen($dtime, '8bit') : strlen($dtime)) < 8)
			{
				$dtime = "00000000";
			}

			$hexdtime = chr(hexdec($dtime[6] . $dtime[7])) .
				chr(hexdec($dtime[4] . $dtime[5])) .
				chr(hexdec($dtime[2] . $dtime[3])) .
				chr(hexdec($dtime[0] . $dtime[1]));

			// If it's a split ZIP file, we've got to make sure that the header can fit in the part
			if ($this->_useSplitZIP)
			{
				// Get header size, taking into account any extra header necessary
				$header_size = 30 + (function_exists('mb_strlen') ? mb_strlen($storedName, '8bit') : strlen($storedName));

				// Compare to free part space
				clearstatcache();

				$current_part_size = @filesize($this->_dataFileName);
				$free_space        = $this->_fragmentSize - ($current_part_size === false ? 0 : $current_part_size);

				if ($free_space <= $header_size)
				{
					// Not enough space on current part, create new part
					if ( !$this->_createNewPart())
					{
						$this->setError('Could not create new ZIP part file ' . basename($this->_dataFileName));

						return false;
					}

					// Open data file for output
					$this->fp = @$this->_fopen($this->_dataFileName, "ab");

					if ($this->fp === false)
					{
						$this->setError("Could not open archive file {$this->_dataFileName} for append!");

						return false;
					}
				}
			}

			$old_offset = @ftell($this->fp);

			if ($this->_useSplitZIP && ($old_offset == 0))
			{
				// Because in split ZIPs we have the split ZIP marker in the first four bytes.
				@fseek($this->fp, 4);
				$old_offset = @ftell($this->fp);
			}

			/**
			 * $seek_result = @fseek($this->fp, 0, SEEK_END);
			 * $old_offset = ($seek_result == -1) ? false : @ftell($this->fp);
			 * if ($old_offset === false)
			 * {
			 * @clearstatcache();
			 * $old_offset = @filesize($this->_dataFileName);
			 * }
			 * /**/

			// Get the file name length in bytes
			if (function_exists('mb_strlen'))
			{
				$fn_length = mb_strlen($storedName, '8bit');
			}
			else
			{
				$fn_length = strlen($storedName);
			}

			$this->_fwrite($this->fp, $this->_fileHeader); /* Begin creating the ZIP data. */

			if ( !$isSymlink)
			{
				$this->_fwrite($this->fp, "\x14\x00"); /* Version needed to extract. */
			}
			else
			{
				$this->_fwrite($this->fp, "\x0a\x03"); /* Version needed to extract. */
			}

			$this->_fwrite($this->fp, pack('v', 2048)); /* General purpose bit flag. Bit 11 set = use UTF-8 encoding for filenames & comments */
			$this->_fwrite($this->fp, ($compressionMethod == 8) ? "\x08\x00" : "\x00\x00"); /* Compression method. */
			$this->_fwrite($this->fp, $hexdtime); /* Last modification time/date. */
			$this->_fwrite($this->fp, pack('V', $crc)); /* CRC 32 information. */

			if ( !isset($c_len))
			{
				$c_len = $unc_len;
			}

			$this->_fwrite($this->fp, pack('V', $c_len)); /* Compressed filesize. */
			$this->_fwrite($this->fp, pack('V', $unc_len)); /* Uncompressed filesize. */
			$this->_fwrite($this->fp, pack('v', $fn_length)); /* Length of filename. */
			$this->_fwrite($this->fp, pack('v', 0)); /* Extra field length. */
			$this->_fwrite($this->fp, $storedName); /* File name. */

			// Cache useful information about the file
			if ( !$isDir && !$isSymlink && !$isVirtual)
			{
				$configuration->set('volatile.engine.archiver.unc_len', $unc_len);
				$configuration->set('volatile.engine.archiver.hexdtime', $hexdtime);
				$configuration->set('volatile.engine.archiver.crc', $crc);
				$configuration->set('volatile.engine.archiver.c_len', $c_len);
				$configuration->set('volatile.engine.archiver.fn_length', $fn_length);
				$configuration->set('volatile.engine.archiver.old_offset', $old_offset);
				$configuration->set('volatile.engine.archiver.storedName', $storedName);
				$configuration->set('volatile.engine.archiver.sourceNameOrData', $sourceNameOrData);
			}
		}
		else
		{
			// Since we are continuing archiving, it's an uncompressed regular file. Set up the variables.
			$compressionMethod = 1;
			$isDir             = false;
			$isSymlink         = false;
			$unc_len           = $configuration->get('volatile.engine.archiver.unc_len');
			$hexdtime          = $configuration->get('volatile.engine.archiver.hexdtime');
			$crc               = $configuration->get('volatile.engine.archiver.crc');
			$c_len             = $configuration->get('volatile.engine.archiver.c_len');
			$fn_length         = $configuration->get('volatile.engine.archiver.fn_length');
			$old_offset        = $configuration->get('volatile.engine.archiver.old_offset');
			$storedName        = $configuration->get('volatile.engine.archiver.storedName');
		}


		/* "File data" segment. */
		if ($compressionMethod == 8)
		{
			// Just dump the compressed data
			if ( !$this->_useSplitZIP)
			{
				$this->_fwrite($this->fp, $zdata);

				if ($this->getError())
				{
					return false;
				}
			}
			else
			{
				// Split ZIP. Check if we need to split the part in the middle of the data.
				clearstatcache();
				$current_part_size = @filesize($this->_dataFileName);
				$free_space        = $this->_fragmentSize - ($current_part_size === false ? 0 : $current_part_size);

				if ($free_space >= (function_exists('mb_strlen') ? mb_strlen($zdata, '8bit') : strlen($zdata)))
				{
					// Write in one part
					$this->_fwrite($this->fp, $zdata);

					if ($this->getError())
					{
						return false;
					}
				}
				else
				{
					$bytes_left = (function_exists('mb_strlen') ? mb_strlen($zdata, '8bit') : strlen($zdata));

					while ($bytes_left > 0)
					{
						clearstatcache();
						$current_part_size = @filesize($this->_dataFileName);
						$free_space        = $this->_fragmentSize - ($current_part_size === false ? 0 : $current_part_size);

						// Split between parts - Write a part
						$this->_fwrite($this->fp, $zdata, min((function_exists('mb_strlen') ? mb_strlen($zdata, '8bit') : strlen($zdata)), $free_space));
						if ($this->getError())
						{
							return false;
						}

						// Get the rest of the data
						$bytes_left = (function_exists('mb_strlen') ? mb_strlen($zdata, '8bit') : strlen($zdata)) - $free_space;

						if ($bytes_left > 0)
						{
							$this->_fclose($this->fp);
							$this->fp = null;

							// Create new part
							if ( !$this->_createNewPart())
							{
								// Die if we couldn't create the new part
								$this->setError('Could not create new ZIP part file ' . basename($this->_dataFileName));

								return false;
							}

							// Open data file for output
							$this->fp = @$this->_fopen($this->_dataFileName, "ab");

							if ($this->fp === false)
							{
								$this->setError("Could not open archive file {$this->_dataFileName} for append!");

								return false;
							}

							$zdata = substr($zdata, -$bytes_left);
						}
					}
				}
			}

			unset($zdata);
		}
		elseif ( !($isDir || $isSymlink))
		{
			// Virtual file, just write the data!
			if ($isVirtual)
			{
				// Just dump the data
				if ( !$this->_useSplitZIP)
				{
					$this->_fwrite($this->fp, $sourceNameOrData);

					if ($this->getError())
					{
						return false;
					}
				}
				else
				{
					// Split ZIP. Check if we need to split the part in the middle of the data.
					clearstatcache();
					$current_part_size = @filesize($this->_dataFileName);
					$free_space        = $this->_fragmentSize - ($current_part_size === false ? 0 : $current_part_size);

					if ($free_space >= (function_exists('mb_strlen') ? mb_strlen($sourceNameOrData, '8bit') : strlen($sourceNameOrData)))
					{
						// Write in one part
						$this->_fwrite($this->fp, $sourceNameOrData);

						if ($this->getError())
						{
							return false;
						}
					}
					else
					{
						$bytes_left = (function_exists('mb_strlen') ? mb_strlen($sourceNameOrData, '8bit') : strlen($sourceNameOrData));

						while ($bytes_left > 0)
						{
							clearstatcache();
							$current_part_size = @filesize($this->_dataFileName);
							$free_space        = $this->_fragmentSize - ($current_part_size === false ? 0 : $current_part_size);

							// Split between parts - Write first part
							$this->_fwrite($this->fp, $sourceNameOrData, min((function_exists('mb_strlen') ? mb_strlen($zdata, '8bit') : strlen($zdata)), $free_space));

							if ($this->getError())
							{
								return false;
							}

							// Get the rest of the data
							$rest_size = (function_exists('mb_strlen') ? mb_strlen($sourceNameOrData, '8bit') : strlen($sourceNameOrData)) - $free_space;

							if ($rest_size > 0)
							{
								$this->_fclose($this->fp);
								$this->fp = null;

								// Create new part if required
								if ( !$this->_createNewPart())
								{
									// Die if we couldn't create the new part
									$this->setError('Could not create new ZIP part file ' . basename($this->_dataFileName));

									return false;
								}

								// Open data file for output
								$this->fp = @$this->_fopen($this->_dataFileName, "ab");

								if ($this->fp === false)
								{
									$this->setError("Could not open archive file {$this->_dataFileName} for append!");

									return false;
								}

								// Get the rest of the compressed data
								$zdata = substr($sourceNameOrData, -$rest_size);
							}

							$bytes_left = $rest_size;
						}
					}
				}
			}
			else
			{
				// IMPORTANT! Only this case can be spanned across steps: uncompressed, non-virtual data
				if ($configuration->get('volatile.engine.archiver.processingfile', false))
				{
					$sourceNameOrData = $configuration->get('volatile.engine.archiver.sourceNameOrData', '');
					$unc_len          = $configuration->get('volatile.engine.archiver.unc_len', 0);
					$resume           = $configuration->get('volatile.engine.archiver.resume', 0);
				}

				// Copy the file contents, ignore directories
				$zdatafp = @fopen($sourceNameOrData, "rb");

				if ($zdatafp === false)
				{
					$this->setWarning('Unreadable file ' . $sourceNameOrData . '. Check permissions');

					return false;
				}
				else
				{
					$timer = Factory::getTimer();

					// Seek to the resume point if required
					if ($configuration->get('volatile.engine.archiver.processingfile', false))
					{
						// Seek to new offset
						$seek_result = @fseek($zdatafp, $resume);

						if ($seek_result === -1)
						{
							// What?! We can't resume!
							$this->setError(sprintf('Could not resume packing of file %s. Your archive is damaged!', $sourceNameOrData));

							return false;
						}

						// Doctor the uncompressed size to match the remainder of the data
						$unc_len = $unc_len - $resume;
					}

					if ( !$this->_useSplitZIP)
					{
						// For non Split ZIP, just dump the file very fast
						while ( !feof($zdatafp) && ($timer->getTimeLeft() > 0) && ($unc_len > 0))
						{
							$zdata = fread($zdatafp, AKEEBA_CHUNK);
							$this->_fwrite($this->fp, $zdata, min((function_exists('mb_strlen') ? mb_strlen($zdata, '8bit') : strlen($zdata)), AKEEBA_CHUNK));
							$unc_len -= AKEEBA_CHUNK;

							if ($this->getError())
							{
								return false;
							}
						}

						if ( !feof($zdatafp) && ($unc_len != 0))
						{
							// We have to break, or we'll time out!
							$resume = @ftell($zdatafp);
							$configuration->set('volatile.engine.archiver.resume', $resume);
							$configuration->set('volatile.engine.archiver.processingfile', true);

							return true;
						}
					}
					else
					{
						// Split ZIP - Do we have enough space to host the whole file?
						clearstatcache();
						$current_part_size = @filesize($this->_dataFileName);
						$free_space        = $this->_fragmentSize - ($current_part_size === false ? 0 : $current_part_size);

						if ($free_space >= $unc_len)
						{
							// Yes, it will fit inside this part, do quick copy
							while ( !feof($zdatafp) && ($timer->getTimeLeft() > 0) && ($unc_len > 0))
							{
								$zdata = fread($zdatafp, AKEEBA_CHUNK);
								$this->_fwrite($this->fp, $zdata, min((function_exists('mb_strlen') ? mb_strlen($zdata, '8bit') : strlen($zdata)), AKEEBA_CHUNK));
								$unc_len -= AKEEBA_CHUNK;

								if ($this->getError())
								{
									return false;
								}

							}

							if ( !feof($zdatafp) && ($unc_len != 0))
							{
								// We have to break, or we'll time out!
								$resume = @ftell($zdatafp);
								$configuration->set('volatile.engine.archiver.resume', $resume);
								$configuration->set('volatile.engine.archiver.processingfile', true);

								return true;
							}
						}
						else
						{
							// No, we'll have to split between parts. We'll loop until we run
							// out of space.
							while ( !feof($zdatafp) && ($timer->getTimeLeft() > 0))
							{
								// No, we'll have to split between parts. Write the first part
								// Find optimal chunk size
								clearstatcache();
								$current_part_size = @filesize($this->_dataFileName);
								$free_space        = $this->_fragmentSize - ($current_part_size === false ? 0 : $current_part_size);

								$chunk_size_primary = min(AKEEBA_CHUNK, $free_space);

								if ($chunk_size_primary <= 0)
								{
									$chunk_size_primary = max(AKEEBA_CHUNK, $free_space);
								}

								// Calculate if we have to read some more data (smaller chunk size)
								// and how many times we must read w/ the primary chunk size
								$chunk_size_secondary = $free_space % $chunk_size_primary;
								$loop_times           = ($free_space - $chunk_size_secondary) / $chunk_size_primary;

								// Read and write with the primary chunk size
								for ($i = 1; $i <= $loop_times; $i++)
								{
									$zdata = fread($zdatafp, $chunk_size_primary);
									$this->_fwrite($this->fp, $zdata, min((function_exists('mb_strlen') ? mb_strlen($zdata, '8bit') : strlen($zdata)), $chunk_size_primary));
									$unc_len -= $chunk_size_primary;

									if ($this->getError())
									{
										return false;
									}

									// Do we have enough time to proceed?
									if (( !feof($zdatafp)) && ($unc_len != 0) && ($timer->getTimeLeft() <= 0))
									{
										// No, we have to break, or we'll time out!
										$resume = @ftell($zdatafp);
										$configuration->set('volatile.engine.archiver.resume', $resume);
										$configuration->set('volatile.engine.archiver.processingfile', true);

										return true;
									}
								}

								// Read and write w/ secondary chunk size, if non-zero
								if ($chunk_size_secondary > 0)
								{
									$zdata = fread($zdatafp, $chunk_size_secondary);
									$this->_fwrite($this->fp, $zdata, min((function_exists('mb_strlen') ? mb_strlen($zdata, '8bit') : strlen($zdata)), $chunk_size_secondary));
									$unc_len -= $chunk_size_secondary;

									if ($this->getError())
									{
										return false;
									}
								}

								// Do we have enough time to proceed?
								if (( !feof($zdatafp)) && ($unc_len != 0) && ($timer->getTimeLeft() <= 0))
								{
									// No, we have to break, or we'll time out!
									$resume = @ftell($zdatafp);
									$configuration->set('volatile.engine.archiver.resume', $resume);
									$configuration->set('volatile.engine.archiver.processingfile', true);

									// ...and create a new part as well
									if ( !$this->_createNewPart())
									{
										// Die if we couldn't create the new part
										$this->setError('Could not create new ZIP part file ' . basename($this->_dataFileName));

										return false;
									}

									// Open data file for output
									$this->fp = @$this->_fopen($this->_dataFileName, "ab");

									if ($this->fp === false)
									{
										$this->setError("Could not open archive file {$this->_dataFileName} for append!");

										return false;
									}

									// ...then, return
									return true;
								}

								// Create new ZIP part, but only if we'll have more data to write
								if ( !feof($zdatafp) && ($unc_len > 0))
								{
									// Create new ZIP part
									if ( !$this->_createNewPart())
									{
										// Die if we couldn't create the new part
										$this->setError('Could not create new ZIP part file ' . basename($this->_dataFileName));

										return false;
									}

									// Close the old data file
									$this->_fclose($this->fp);
									$this->fp = null;

									// We have created the part. If the user asked for immediate post-proc, break step now.
									if ($configuration->get('engine.postproc.common.after_part', 0))
									{
										$resume = @ftell($zdatafp);
										$configuration->set('volatile.engine.archiver.resume', $resume);
										$configuration->set('volatile.engine.archiver.processingfile', true);

										$configuration->set('volatile.breakflag', true);
										@fclose($zdatafp);

										return true;
									}

									// Open data file for output
									$this->fp = @$this->_fopen($this->_dataFileName, "ab");

									if ($this->fp === false)
									{
										$this->setError("Could not open archive file {$this->_dataFileName} for append!");

										return false;
									}
								}

							} // end while

						}
					}

					@fclose($zdatafp);
				}
			}
		}
		elseif ($isSymlink)
		{
			$this->_fwrite($this->fp, @readlink($sourceNameOrData));
		}

		// Open the central directory file for append
		if (is_null($this->cdfp))
		{
			$this->cdfp = @$this->_fopen($this->_ctrlDirFileName, "ab");
		}

		if ($this->cdfp === false)
		{
			$this->setError("Could not open Central Directory temporary file for append!");

			return false;
		}

		$this->_fwrite($this->cdfp, $this->_ctrlDirHeader);

		if ( !$isSymlink)
		{
			$this->_fwrite($this->cdfp, "\x14\x00"); /* Version made by (always set to 2.0). */
			$this->_fwrite($this->cdfp, "\x14\x00"); /* Version needed to extract */
			$this->_fwrite($this->cdfp, pack('v', 2048)); /* General purpose bit flag */
			$this->_fwrite($this->cdfp, ($compressionMethod == 8) ? "\x08\x00" : "\x00\x00"); /* Compression method. */
		}
		else
		{
			// Symlinks get special treatment
			$this->_fwrite($this->cdfp, "\x14\x03"); /* Version made by (version 2.0 with UNIX extensions). */
			$this->_fwrite($this->cdfp, "\x0a\x03"); /* Version needed to extract */
			$this->_fwrite($this->cdfp, pack('v', 2048)); /* General purpose bit flag */
			$this->_fwrite($this->cdfp, "\x00\x00"); /* Compression method. */
		}

		$this->_fwrite($this->cdfp, $hexdtime); /* Last mod time/date. */
		$this->_fwrite($this->cdfp, pack('V', $crc)); /* CRC 32 information. */
		$this->_fwrite($this->cdfp, pack('V', $c_len)); /* Compressed filesize. */

		if ($compressionMethod == 0)
		{
			// When we are not compressing, $unc_len is being reduced to 0 while backing up.
			// With this trick, we always store the correct length, as in this case the compressed
			// and uncompressed length is always the same.
			$this->_fwrite($this->cdfp, pack('V', $c_len)); /* Uncompressed filesize. */
		}
		else
		{
			// When compressing, the uncompressed length differs from compressed length
			// and this line writes the correct value.
			$this->_fwrite($this->cdfp, pack('V', $unc_len)); /* Uncompressed filesize. */
		}

		$this->_fwrite($this->cdfp, pack('v', $fn_length)); /* Length of filename. */
		$this->_fwrite($this->cdfp, pack('v', 0)); /* Extra field length. */
		$this->_fwrite($this->cdfp, pack('v', 0)); /* File comment length. */
		$this->_fwrite($this->cdfp, pack('v', $starting_disk_number_for_this_file)); /* Disk number start. */
		$this->_fwrite($this->cdfp, pack('v', 0)); /* Internal file attributes. */

		if ( !$isSymlink)
		{
			$this->_fwrite($this->cdfp, pack('V', $isDir ? 0x41FF0010 : 0xFE49FFE0)); /* External file attributes -   'archive' bit set. */
		}
		else
		{
			// For SymLinks we store UNIX file attributes
			$this->_fwrite($this->cdfp, "\x20\x80\xFF\xA1"); /* External file attributes for Symlink. */
		}

		$this->_fwrite($this->cdfp, pack('V', $old_offset)); /* Relative offset of local header. */
		$this->_fwrite($this->cdfp, $storedName); /* File name. */
		/* Optional extra field, file comment goes here. */

		// Finally, increase the file counter by one
		$this->_totalFileEntries++;

		// Uncache data
		$configuration->set('volatile.engine.archiver.sourceNameOrData', null);
		$configuration->set('volatile.engine.archiver.unc_len', null);
		$configuration->set('volatile.engine.archiver.resume', null);
		$configuration->set('volatile.engine.archiver.hexdtime', null);
		$configuration->set('volatile.engine.archiver.crc', null);
		$configuration->set('volatile.engine.archiver.c_len', null);
		$configuration->set('volatile.engine.archiver.fn_length', null);
		$configuration->set('volatile.engine.archiver.old_offset', null);
		$configuration->set('volatile.engine.archiver.storedName', null);
		$configuration->set('volatile.engine.archiver.sourceNameOrData', null);

		$configuration->set('volatile.engine.archiver.processingfile', false);

		// ... and return TRUE = success
		return true;
	}

	/**
	 * Converts a UNIX timestamp to a 4-byte DOS date and time format
	 * (date in high 2-bytes, time in low 2-bytes allowing magnitude
	 * comparison).
	 *
	 * @param integer $unixtime The current UNIX timestamp.
	 *
	 * @return integer  The current date in a 4-byte DOS format.
	 */
	protected function _unix2DOSTime($unixtime = null)
	{
		$timearray = (is_null($unixtime)) ? getdate() : getdate($unixtime);

		if ($timearray['year'] < 1980)
		{
			$timearray['year']    = 1980;
			$timearray['mon']     = 1;
			$timearray['mday']    = 1;
			$timearray['hours']   = 0;
			$timearray['minutes'] = 0;
			$timearray['seconds'] = 0;
		}

		return (($timearray['year'] - 1980) << 25) |
			($timearray['mon'] << 21) |
			($timearray['mday'] << 16) |
			($timearray['hours'] << 11) |
			($timearray['minutes'] << 5) |
			($timearray['seconds'] >> 1);
	}

	/**
	 * Creates a new part for the spanned archive
	 *
	 * @param   bool $finalPart Is this the final archive part?
	 *
	 * @return  bool  True on success
	 */
	protected function _createNewPart($finalPart = false)
	{
		// Close any open file pointers
		if (is_resource($this->fp))
		{
			$this->_fclose($this->fp);
		}

		if (is_resource($this->cdfp))
		{
			$this->_fclose($this->cdfp);
		}

		// Remove the just finished part from the list of resumable offsets
		$this->_removeFromOffsetsList($this->_dataFileName);

		// Set the file pointers to null
		$this->fp   = null;
		$this->cdfp = null;

		// Push the previous part if we have to post-process it immediately
		$configuration = Factory::getConfiguration();

		if ($configuration->get('engine.postproc.common.after_part', 0))
		{
			$this->finishedPart[] = $this->_dataFileName;
		}

		// Add the part's size to our rolling sum
		clearstatcache();
		$this->_totalDataSize += filesize($this->_dataFileName);
		$this->_totalFragments++;
		$this->_currentFragment = $this->_totalFragments;

		if ($finalPart)
		{
			$this->_dataFileName = $this->_dataFileNameBase . '.zip';
		}
		else
		{
			$this->_dataFileName = $this->_dataFileNameBase . '.z' . sprintf('%02d', $this->_currentFragment);
		}

		Factory::getLog()->log(LogLevel::INFO, 'Creating new ZIP part #' . $this->_currentFragment . ', file ' . $this->_dataFileName);

		// Inform CUBE that we have changed the multipart number
		$statistics = Factory::getStatistics();
		$statistics->updateMultipart($this->_totalFragments);

		// Try to remove any existing file
		@unlink($this->_dataFileName);

		// Touch the new file
		$result = @touch($this->_dataFileName);

		if (function_exists('chmod'))
		{
			chmod($this->_dataFileName, 0666);
		}

		return $result;
	}
}