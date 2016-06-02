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

use Akeeba\Engine\Util\Encrypt;
use Psr\Log\LogLevel;
use Akeeba\Engine\Factory;

if ( !defined('_JPS_MAJOR'))
{
	define('_JPS_MAJOR', 1);
	define('_JPS_MINOR', 10);
}

/**
 * JoomlaPack Archive Secure (JPS) creation class
 *
 * JPS Format 1.9 implemented, minus BZip2 compression support
 */
class Jps extends Base
{
	/** @var integer How many files are contained in the archive */
	private $_fileCount = 0;

	/** @var integer The total size of files contained in the archive as they are stored */
	private $_compressedSize = 0;

	/** @var integer The total size of files contained in the archive when they are extracted to disk. */
	private $_uncompressedSize = 0;

	/** @var string The name of the file holding the ZIP's data, which becomes the final archive */
	private $_dataFileName;

	/** @var string Standard Header signature */
	private $_archive_signature = "\x4A\x50\x53"; // JPS

	/** @var string Standard Header signature */
	private $_end_of_archive_signature = "\x4A\x50\x45"; // JPE

	/** @var string Entity Block signature */
	private $_fileHeader = "\x4A\x50\x46"; // JPF

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

	/** @var string The password to use */
	private $password = null;

	/** @var Encrypt The encryption object used in this class */
	private $encryptionObject = null;

	/**
	 * Extend the bootstrap code to add some define's used by the JPS format engine
	 *
	 * @return void
	 */
	protected function __bootstrap_code()
	{
		if ( !defined('_JPS_MAJOR'))
		{
			define('_JPS_MAJOR', 1); // JPS Format major version number
			define('_JPS_MINOR', 9); // JPS Format minor version number
		}

		$this->encryptionObject = Factory::getEncryption();

		parent::__bootstrap_code();
	}

	/**
	 * Also remove the encryption object reference
	 *
	 * @codeCoverageIgnore
	 *
	 * @return  void
	 */
	public function _onSerialize()
	{
		parent::_onSerialize();

		$this->encryptionObject = null;
	}

	/**
	 * Initialises the archiver class, creating the archive from an existent
	 * installer's JPA archive.
	 *
	 * @param    string $targetArchivePath Absolute path to the generated archive
	 * @param    array  $options           A named key array of options (optional)
	 *
	 * @return  void
	 */
	public function initialize($targetArchivePath, $options = array())
	{
		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . " :: new instance - archive $targetArchivePath");

		$this->_dataFileName = $targetArchivePath;

		// Make sure the encryption functions are all there
		$test = $this->encryptionObject->AESEncryptCBC('test', 'test');

		if ($test === false)
		{
			$this->setError('Sorry, your server does not support AES-128 encryption. Please use a different archive format.');

			return;
		}

		// Make sure we can really compress stuff
		if ( !function_exists('gzcompress'))
		{
			$this->setError('Sorry, your server does not support GZip compression which is required for the JPS format. Please use a different archive format.');

			return;
		}

		// Get and memorise the password
		$config         = Factory::getConfiguration();
		$this->password = $config->get('engine.archiver.jps.key', '');

		if (empty($this->password))
		{
			$this->setWarning('You are using an empty password. This is not secure at all!');
		}

		// Should we enable split archive feature?
		$registry     = Factory::getConfiguration();
		$fragmentsize = $registry->get('engine.archiver.common.part_size', 0);

		if ($fragmentsize >= 65536)
		{
			// If the fragment size is AT LEAST 64Kb, enable split archive
			$this->_useSplitZIP  = true;
			$this->_fragmentSize = $fragmentsize;

			// Indicate that we have at least 1 part
			$statistics = Factory::getStatistics();
			$statistics->updateMultipart(1);
			$this->_totalFragments = 1;

			Factory::getLog()->log(LogLevel::INFO, __CLASS__ . " :: Spanned JPS creation enabled");
			$this->_dataFileNameBase = dirname($targetArchivePath) . '/' . basename($targetArchivePath, '.jps');
			$this->_dataFileName     = $this->_dataFileNameBase . '.j01';
		}

		// Should I use Symlink Target Storage?
		$dereferencesymlinks = $registry->get('engine.archiver.common.dereference_symlinks', true);

		if ( !$dereferencesymlinks)
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

		// Try to kill the archive if it exists
		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . " :: Killing old archive");

		$this->fp = $this->_fopen($this->_dataFileName, "wb");

		if ( !($this->fp === false))
		{
			@ftruncate($this->fp, 0);
		}
		else
		{
			if (file_exists($this->_dataFileName))
			{
				@unlink($this->_dataFileName);
			}

			@touch($this->_dataFileName);

			if (function_exists('chmod'))
			{
				chmod($this->_dataFileName, 0666);
			}

			$this->fp = $this->_fopen($this->_dataFileName, "wb");

			if ($this->fp !== false)
			{
				$this->setError("Could not open archive file '{$this->_dataFileName}' for append!");

				return;
			}
		}

		// Write the initial instance of the archive header
		$this->writeArchiveHeader();

		if ($this->getError())
		{
			return;
		}
	}

	/**
	 * Updates the Standard Header with current information
	 *
	 * @return  void
	 */
	public function finalize()
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

		$this->_closeAllFiles();

		// If spanned JPS and there is no .jps file, rename the last fragment to .jps
		if ($this->_useSplitZIP)
		{
			$extension = substr($this->_dataFileName, -3);

			if ($extension != '.jps')
			{
				Factory::getLog()->log(LogLevel::DEBUG, 'Renaming last JPS part to .JPS extension');
				$newName = $this->_dataFileNameBase . '.jps';

				if ( !@rename($this->_dataFileName, $newName))
				{
					$this->setError('Could not rename last JPS part to .JPS extension.');

					return;
				}

				$this->_dataFileName = $newName;
			}
		}

		// Write the end of archive header
		$this->writeEndOfArchiveHeader();

		if ($this->getError())
		{
			return;
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
		return '.jps';
	}

	/**
	 * Outputs a Standard Header at the top of the file
	 *
	 * @return  void
	 */
	protected function writeArchiveHeader()
	{
		if (is_null($this->fp))
		{
			$this->fp = @$this->_fopen($this->_dataFileName, 'r+');
		}

		if ($this->fp === false)
		{
			$this->setError('Could not open ' . $this->_dataFileName . ' for writing. Check permissions and open_basedir restrictions.');

			return;
		}

		$this->_fwrite($this->fp, $this->_archive_signature); // ID string (JPS)

		if ($this->getError())
		{
			return;
		}

		$this->_fwrite($this->fp, pack('C', _JPS_MAJOR)); // Major version
		$this->_fwrite($this->fp, pack('C', _JPS_MINOR)); // Minor version
		$this->_fwrite($this->fp, pack('C', $this->_useSplitZIP ? 1 : 0)); // Is it a split archive?
		$this->_fwrite($this->fp, pack('v', 0)); // Extra header length (0 bytes)

		if (function_exists('chmod'))
		{
			@chmod($this->_dataFileName, 0755);
		}
	}

	/**
	 * Outputs the end of the Standard Header at the file
	 *
	 * @return  void
	 */
	protected function writeEndOfArchiveHeader()
	{
		if ( !is_null($this->fp))
		{
			$this->_fclose($this->fp);
			$this->fp = null;
		}

		$this->fp = @$this->_fopen($this->_dataFileName, 'ab');

		if ($this->fp === false)
		{
			$this->setError('Could not open ' . $this->_dataFileName . ' for writing. Check permissions and open_basedir restrictions.');

			return;
		}
		$this->_fwrite($this->fp, $this->_end_of_archive_signature); // ID string (JPE)
		$this->_fwrite($this->fp, pack('v', $this->_totalFragments)); // Total number of parts
		$this->_fwrite($this->fp, pack('V', $this->_fileCount)); // Total number of files
		$this->_fwrite($this->fp, pack('V', $this->_uncompressedSize)); // Uncompressed size
		$this->_fwrite($this->fp, pack('V', $this->_compressedSize)); // Compressed size
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
	 * @return boolean True on success, false otherwise
	 */
	protected function _addFile($isVirtual, &$sourceNameOrData, $targetName)
	{
		if ($isVirtual)
		{
			Factory::getLog()->log(LogLevel::DEBUG, "-- Adding $targetName to archive (virtual data)");
		}
		else
		{
			Factory::getLog()->log(LogLevel::DEBUG, "-- Adding $targetName to archive (source: $sourceNameOrData)");
		}

		$configuration = Factory::getConfiguration();
		$timer         = Factory::getTimer();

		// Initialize inode change timestamp
		$filectime = 0;

		$processingFile = $configuration->get('volatile.engine.archiver.processingfile', false);

		// Open data file for output
		if (is_null($this->fp))
		{
			$this->fp = @$this->_fopen($this->_dataFileName, "ab");
		}

		if ($this->fp === false)
		{
			$this->fp = null;
			$this->setError("Could not open archive file '{$this->_dataFileName}' for append!");

			return false;
		}

		if ( !$processingFile)
		{
			// Uncache data
			$configuration->set('volatile.engine.archiver.sourceNameOrData', null);
			$configuration->set('volatile.engine.archiver.unc_len', null);
			$configuration->set('volatile.engine.archiver.resume', null);
			$configuration->set('volatile.engine.archiver.processingfile', false);

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
				$fileSize  = $this->stringLength($sourceNameOrData);
				$filectime = time();
			}
			else
			{
				if ($isSymlink)
				{
					$fileSize = $this->stringLength(@readlink($sourceNameOrData));
				}
				else
				{
					// Is the file readable?
					if ( !is_readable($sourceNameOrData))
					{
						// Unreadable files won't be recorded in the archive file
						$this->setWarning('Unreadable file ' . $sourceNameOrData . '. Check permissions');

						return false;
					}
					else
					{
						// Really, REALLY check if it is readable (PHP sometimes lies, dammit!)
						$myfp = @$this->_fopen($sourceNameOrData, 'rb');

						if ($myfp === false)
						{
							// Unreadable file, skip it.
							$this->setWarning('Unreadable file ' . $sourceNameOrData . '. Check permissions');

							return false;
						}

						@$this->_fclose($myfp);
					}

					// Get the filesize and modification time
					$fileSize  = $isDir ? 0 : @filesize($sourceNameOrData);
					$filectime = $isDir ? 0 : @filemtime($sourceNameOrData);
				}
			}

			// Decide if we will compress
			if ($isDir || $isSymlink)
			{
				// don't compress directories and symlinks...
				$compressionMethod = 0;
			}
			else
			{
				// always compress files using gzip
				$compressionMethod = 1;
			}

			// Fix stored name for directories
			$storedName = $targetName;
			$storedName .= ($isDir) ? "/" : "";

			// Get file permissions
			$perms = $isVirtual ? 0755 : @fileperms($sourceNameOrData);

			// Get file type
			if (( !$isDir) && ( !$isSymlink))
			{
				$fileType = 1;
			}
			elseif ($isSymlink)
			{
				$fileType = 2;
			}
			elseif ($isDir)
			{
				$fileType = 0;
			}

			// Create the Entity Description Block Data
			$headerData =
				pack('v', $this->stringLength($storedName)) // Length of entity path
				. $storedName // Entity path
				. pack('c', $fileType) // Entity type
				. pack('c', $compressionMethod) // Compression type
				. pack('V', $fileSize) // Uncompressed size
				. pack('V', $perms) // Entity permissions
				. pack('V', $filectime) // File Modification Time
			;

			// Create and write the Entity Description Block Header
			$decryptedSize = $this->stringLength($headerData);
			$headerData    = $this->encryptionObject->AESEncryptCBC($headerData, $this->password, 128);
			$encryptedSize = $this->stringLength($headerData);

			$headerData =
				$this->_fileHeader . // JPF
				pack('v', $encryptedSize) . // Encrypted size
				pack('v', $decryptedSize) . // Decrypted size
				$headerData // Encrypted Entity Description Block Data
			;

			// Do we have enough space to store the header?
			if ($this->_useSplitZIP)
			{
				// Compare to free part space
				clearstatcache();
				$current_part_size = @filesize($this->_dataFileName);
				$free_space        = $this->_fragmentSize - ($current_part_size === false ? 0 : $current_part_size);

				if ($free_space <= $this->stringLength($headerData))
				{
					// Not enough space on current part, create new part
					if ( !$this->_createNewPart())
					{
						$this->setError('Could not create new JPS part file ' . basename($this->_dataFileName));

						return false;
					}

					// Open data file for output
					$this->fp = @$this->_fopen($this->_dataFileName, "ab");

					if ($this->fp === false)
					{
						$this->fp = null;
						$this->setError("Could not open archive file '{$this->_dataFileName}' for append!");

						return false;
					}
				}
			}

			// Write the header data
			$this->_fwrite($this->fp, $headerData);

			// Cache useful information about the file
			$configuration->set('volatile.engine.archiver.sourceNameOrData', $sourceNameOrData);
			$configuration->set('volatile.engine.archiver.unc_len', $fileSize);

			// Update global stats
			$this->_fileCount++;
			$this->_uncompressedSize += $fileSize;
		}
		else
		{
			$isDir     = false;
			$isSymlink = false;
		}

		// Symlink: Single step, one block, uncompressed
		if ($isSymlink)
		{
			$data = @readlink($sourceNameOrData);
			$this->_writeEncryptedBlock($data);
			$this->_compressedSize += $this->stringLength($data);

			if ($this->getError())
			{
				return false;
			}
		}
		// Virtual: Single step, multiple blocks, compressed
		elseif ($isVirtual)
		{
			// Loop in 64Kb blocks
			while (strlen($sourceNameOrData) > 0)
			{
				$data = substr($sourceNameOrData, 0, 65535);

				if ($this->stringLength($data) < $this->stringLength($sourceNameOrData))
				{
					$sourceNameOrData = substr($sourceNameOrData, 65535);
				}
				else
				{
					$sourceNameOrData = '';
				}

				$data = gzcompress($data);
				$data = substr(substr($data, 0, -4), 2);
				$this->_writeEncryptedBlock($data);
				$this->_compressedSize += $this->stringLength($data);

				if ($this->getError())
				{
					return false;
				}
			}
		}
		// Regular file: multiple step, multiple blocks, compressed
		else
		{
			// Get resume information of required
			if ($configuration->get('volatile.engine.archiver.processingfile', false))
			{
				$sourceNameOrData = $configuration->get('volatile.engine.archiver.sourceNameOrData', '');
				$fileSize         = $configuration->get('volatile.engine.archiver.unc_len', 0);
				$resume           = $configuration->get('volatile.engine.archiver.resume', 0);
				Factory::getLog()->log(LogLevel::DEBUG, "(cont) Source: $sourceNameOrData - Size: $fileSize - Resume: $resume");
			}

			// Open the file
			$zdatafp = @fopen($sourceNameOrData, "rb");

			if ($zdatafp === false)
			{
				$this->setWarning('Unreadable file ' . $sourceNameOrData . '. Check permissions');

				return false;
			}

			// Seek to the resume point if required
			if ($configuration->get('volatile.engine.archiver.processingfile', false))
			{
				// Seek to new offset
				$seek_result = @fseek($zdatafp, $resume);

				if ($seek_result === -1)
				{
					// What?! We can't resume!
					$this->setError(sprintf('Could not resume packing of file %s. Your archive is damaged!', $sourceNameOrData));
					@fclose($zdatafp);

					return false;
				}

				// Doctor the uncompressed size to match the remainder of the data
				$fileSize = $fileSize - $resume;
			}

			while ( !feof($zdatafp) && ($timer->getTimeLeft() > 0) && ($fileSize > 0))
			{
				$zdata = @fread($zdatafp, AKEEBA_CHUNK);
				$fileSize -= min($this->stringLength($zdata), AKEEBA_CHUNK);
				$zdata = gzcompress($zdata);
				$zdata = substr(substr($zdata, 0, -4), 2);
				$this->_writeEncryptedBlock($zdata);
				$this->_compressedSize += $this->stringLength($zdata);

				if ($this->getError())
				{
					@fclose($zdatafp);

					return false;
				}
			}
			// WARNING!!! The extra $fileSize != 0 check is necessary as PHP won't reach EOF for 0-byte files.
			if ( !feof($zdatafp) && ($fileSize != 0))
			{
				// We have to break, or we'll time out!
				$resume = @ftell($zdatafp);
				$configuration->set('volatile.engine.archiver.resume', $resume);
				$configuration->set('volatile.engine.archiver.processingfile', true);
				@fclose($zdatafp);

				return true;
			}
			else
			{
				$configuration->set('volatile.engine.archiver.resume', null);
				$configuration->set('volatile.engine.archiver.processingfile', false);
			}

			@$this->_fclose($zdatafp);
		}

		return true;
	}

	/**
	 * Creates a new archive part
	 *
	 * @param bool $finalPart Set to true if it is the final part (therefore has the .jps extension)
	 *
	 * @return bool True on success
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

		$this->_totalFragments++;
		$this->_currentFragment = $this->_totalFragments;

		if ($finalPart)
		{
			$this->_dataFileName = $this->_dataFileNameBase . '.jps';
		}
		else
		{
			$this->_dataFileName = $this->_dataFileNameBase . '.j' . sprintf('%02d', $this->_currentFragment);
		}

		Factory::getLog()->log(LogLevel::INFO, 'Creating new JPS part #' . $this->_currentFragment . ', file ' . $this->_dataFileName);

		// Inform that we have chenged the multipart number
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

	/**
	 * Writes an encrypted block to the archive
	 *
	 * @param string   $data Raw binary data to encrypt and write
	 *
	 * @return bool True on success
	 */
	protected function _writeEncryptedBlock($data)
	{
		$decryptedSize = $this->stringLength($data);
		$data          = $this->encryptionObject->AESEncryptCBC($data, $this->password, 128);
		$encryptedSize = $this->stringLength($data);

		// Do we have enough space to store the 8 byte header?
		if ($this->_useSplitZIP)
		{
			// Compare to free part space
			clearstatcache();
			$current_part_size = @filesize($this->_dataFileName);
			$free_space        = $this->_fragmentSize - ($current_part_size === false ? 0 : $current_part_size);

			if ($free_space <= 8)
			{
				@$this->_fclose($this->fp);
				$this->fp = null;

				// Not enough space on current part, create new part
				if ( !$this->_createNewPart())
				{
					$this->setError('Could not create new JPS part file ' . basename($this->_dataFileName));

					return false;
				}

				// Open data file for output
				$this->fp = @$this->_fopen($this->_dataFileName, "ab");

				if ($this->fp === false)
				{
					$this->fp = null;
					$this->setError("Could not open archive file '{$this->_dataFileName}' for append!");

					return false;
				}
			}
		}
		else
		{
			$free_space = $encryptedSize + 8;
		}

		// Write the header
		$this->_fwrite($this->fp,
			pack('V', $encryptedSize) .
			pack('V', $decryptedSize)
		);

		if ($this->getError())
		{
			return false;
		}

		$free_space -= 8;

		// Do we have enough space to write the data in one part?
		if ($free_space >= $encryptedSize)
		{
			$this->_fwrite($this->fp, $data);

			if ($this->getError())
			{
				return false;
			}
		}
		else
		{
			// Split between parts - Write first part
			$firstPart  = substr($data, 0, $free_space);
			$secondPart = substr($data, $free_space);

			if (md5($firstPart . $secondPart) != md5($data))
			{
				$this->setError('Multibyte character problems detected');

				return false;
			}

			$this->_fwrite($this->fp, $firstPart, $free_space);

			if ($this->getError())
			{
				return false;
			}

			// Create new part
			if ( !$this->_createNewPart())
			{
				// Die if we couldn't create the new part
				$this->setError('Could not create new JPA part file ' . basename($this->_dataFileName));

				return false;
			}

			// Close the old data file
			@$this->_fclose($this->fp);

			// Open data file for output
			$this->fp = @$this->_fopen($this->_dataFileName, "ab");

			if ($this->fp === false)
			{
				$this->fp = null;
				$this->setError("Could not open archive file {$this->_dataFileName} for append!");

				return false;
			}

			// Write the rest of the data
			$this->_fwrite($this->fp, $secondPart, $encryptedSize - $free_space);
		}

		return true;
	}

	/**
	 * Returns the length of a string in BYTES, not characters
	 *
	 * @param string $string The string to get the length for
	 *
	 * @return int The size in BYTES
	 */
	public function stringLength($string)
	{
		return function_exists('mb_strlen') ? mb_strlen($string, '8bit') : strlen($string);
	}
}