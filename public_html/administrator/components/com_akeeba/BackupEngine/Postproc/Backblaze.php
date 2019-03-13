<?php
/**
 * Akeeba Engine
 * The PHP-only site backup engine
 *
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 */

namespace Akeeba\Engine\Postproc;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Factory;
use Akeeba\Engine\Postproc\Connector\Backblaze as ConnectorBackblaze;
use Akeeba\Engine\Postproc\Connector\Backblaze\Exception\Base as BackblazeBaseException;
use Psr\Log\LogLevel;

/**
 * Upload to Backblaze post-processing engine for Akeeba Engine
 */
class Backblaze extends Base
{
	/**
	 * The upload ID of the multipart upload in progress
	 *
	 * @var   null|string
	 */
	protected $fileId = null;

	/**
	 * The Upload URL structure returned by Backblaze, required in multipart uplaods
	 *
	 * @var   ConnectorBackblaze\UploadURL
	 */
	protected $uploadUrl = null;

	/**
	 * The part number for the multipart upload in progress
	 *
	 * @var null|int
	 */
	protected $partNumber = null;

	/**
	 * The SHA-1 checksums of the uploaded chunks, used to finalise the multipart upload
	 *
	 * @var  array
	 */
	protected $sha1Parts = array();

	/**
	 * Used in log messages.
	 *
	 * @var  string
	 */
	protected $engineLogName = 'BackBlaze B2';

	/**
	 * The prefix to use for volatile key storage
	 *
	 * @var  string
	 */
	protected $volatileKeyPrefix = 'volatile.postproc.backblaze.';

	/**
	 * The ID of the bucket specified in the engine configuration
	 *
	 * @var  string
	 */
	protected $bucketId = '';

	/**
	 * Initialise the class, setting its capabilities
	 */
	public function __construct()
	{
		$this->can_delete              = true;
		$this->can_download_to_browser = true;
		$this->can_download_to_file    = true;
	}

	/**
	 * This function takes care of post-processing a backup archive's part, or the
	 * whole backup archive if it's not a split archive type. If the process fails
	 * it should return false. If it succeeds and the entirety of the file has been
	 * processed, it should return true. If only a part of the file has been uploaded,
	 * it must return 1.
	 *
	 * @param   string  $absolute_filename  Absolute path to the part we'll have to process
	 * @param   string  $upload_as          Base name of the uploaded file, skip to use $absolute_filename's
	 *
	 * @return  boolean|integer  False on failure, true on success, 1 if more work is required
	 */
	public function processPart($absolute_filename, $upload_as = null)
	{
		// Retrieve engine configuration data
		$akeebaConfig = Factory::getConfiguration();

		// Load multipart information from temporary storage
		$this->fileId    = $akeebaConfig->get($this->volatileKeyPrefix . 'fileId', null);
		$this->uploadUrl = new ConnectorBackblaze\UploadURL($akeebaConfig->get($this->volatileKeyPrefix . 'uploadUrl', array()));

		// Get the configuration parameters
		$engineConfig     = $this->getEngineConfiguration();
		$bucket           = $engineConfig['bucket'];
		$disableMultipart = $engineConfig['disableMultipart'];

		// The directory is a special case. First try getting a cached directory
		$directory        = $akeebaConfig->get('volatile.postproc.directory', null);
		$processDirectory = false;

		// If there is no cached directory, fetch it from the engine configuration
		if (is_null($directory))
		{
			$directory        = $engineConfig['directory'];
			$processDirectory = true;
		}

		// The very first time we deal with the directory we need to process it.
		if ($processDirectory)
		{
			if (!empty($directory))
			{
				$directory = str_replace('\\', '/', $directory);
				$directory = rtrim($directory, '/');
				$directory = trim($directory);
				$directory = ltrim(Factory::getFilesystemTools()->TranslateWinPath($directory), '/');
				$directory = Factory::getFilesystemTools()->replace_archive_name_variables($directory);
			}
			else
			{
				$directory = '';
			}

			// Store the parsed directory in temporary storage
			$akeebaConfig->set('volatile.postproc.directory', $directory);
		}

		// Get the file size and disable multipart uploads for files shorter than 5Mb
		$fileSize = @filesize($absolute_filename);

		if ($fileSize <= 5242880)
		{
			$disableMultipart = true;
		}

		// Calculate relative remote filename
		$remoteKey = empty($upload_as) ? basename($absolute_filename) : $upload_as;

		if (!empty($directory) && ($directory != '/'))
		{
			$remoteKey = $directory . '/' . $remoteKey;
		}

		// Store the absolute remote path in the class property
		$this->remote_path = $remoteKey;

		// Create the API connector instance
		$connector = $this->getConnector();

		if (!is_object($connector))
		{
			return false;
		}

		// If we do not have the bucket ID let's fetch it now
		$bucketId = $this->getBucketId($connector);

		// Are we already processing a multipart upload or asked to perform a multipart upload?
		if (!empty($this->fileId) || !$disableMultipart)
		{
			$this->partNumber = $akeebaConfig->get($this->volatileKeyPrefix . 'partNumber', null);
			$this->sha1Parts  = $akeebaConfig->get($this->volatileKeyPrefix . 'sha1Parts', '[]');
			$this->sha1Parts  = json_decode($this->sha1Parts, true);
			$this->sha1Parts  = empty($this->sha1Parts) ? array() : $this->sha1Parts;

			return $this->multipartUpload($bucketId, $remoteKey, $absolute_filename, $connector);
		}

		return $this->simpleUpload($bucketId, $remoteKey, $absolute_filename, $connector);
	}

	/**
	 * Deletes a remote file
	 *
	 * @param   $path  string  Absolute path to the file we're deleting
	 *
	 * @return  bool|int  False on failure, true on success, 1 if more work is required
	 */
	public function delete($path)
	{
		$connector = $this->getConnector();

		if (!is_object($connector))
		{
			return false;
		}

		try
		{
			$bucketId = $this->getBucketId($connector);
			$connector->deleteByFileName($bucketId, $path);
		}
		catch (\Exception $e)
		{
			$this->setError($e->getCode() . ' :: ' . $e->getMessage());

			return false;
		}

		return true;
	}

	/**
	 * Downloads a remote file to a local file, optionally doing a range download. If the
	 * download fails we return false. If the download succeeds we return true. If range
	 * downloads are not supported, -1 is returned and nothing is written to disk.
	 *
	 * @param   $remotePath  string    The path to the remote file
	 * @param   $localFile   string    The absolute path to the local file we're writing to
	 * @param   $fromOffset  int|null  The offset (in bytes) to start downloading from
	 * @param   $length      int|null  The amount of data (in bytes) to download
	 *
	 * @return  bool|int  True on success, false on failure, -1 if ranges are not supported
	 */
	public function downloadToFile($remotePath, $localFile, $fromOffset = null, $length = null)
	{
		$engineConfig = $this->getEngineConfiguration();
		$bucket       = $engineConfig['bucket'];
		$bucket       = str_replace('/', '', $bucket);

		$connector = $this->getConnector();

		if (!is_object($connector))
		{
			return false;
		}

		$headers = array();

		if ($fromOffset && $length)
		{
			$toOffset         = $fromOffset + $length - 1;
			$headers['Range'] = 'bytes=' . $fromOffset . '-' . $toOffset;
		}

		try
		{
			$connector->downloadFile($bucket, $remotePath, $localFile, $headers);
		}
		catch (\Exception $e)
		{
			$this->setWarning($e->getCode() . ' :: ' . $e->getMessage());

			return false;
		}

		return true;
	}

	/**
	 * Returns a public download URL or starts a browser-side download of a remote file.
	 * In the case of a public download URL, a string is returned. If a browser-side
	 * download is initiated, it returns true. In any other case (e.g. unsupported, not
	 * found, etc) it returns false.
	 *
	 * @param   $remotePath  string  The file to download
	 *
	 * @return  string|bool
	 */
	public function downloadToBrowser($remotePath)
	{
		$engineConfig = $this->getEngineConfiguration();
		$bucket       = $engineConfig['bucket'];
		$bucket       = str_replace('/', '', $bucket);

		$connector = $this->getConnector();

		if (!is_object($connector))
		{
			return false;
		}

		return $connector->getSignedUrl($bucket, $remotePath, 30);
	}

	/**
	 * Start a multipart upload
	 *
	 * @param   string             $bucketId   The bucket ID to upload to
	 * @param   string             $remoteKey  The remote filename
	 * @param   string             $sourceFile The full path to the local source file
	 * @param   ConnectorBackblaze $connector  The S3 client object instance
	 *
	 * @return  bool|int  True when we're done uploading, false if an error occurs, 1 if we have more parts
	 */
	protected function multipartUpload($bucketId, $remoteKey, $sourceFile, ConnectorBackblaze $connector)
	{
		$headers = array();

		if (empty($this->fileId))
		{
			Factory::getLog()->log(LogLevel::DEBUG, "{$this->engineLogName} -- Beginning multipart upload of $sourceFile");

			// Initialise the multipart upload if necessary
			try
			{
				$fileInfo     = $connector->startUpload($bucketId, $remoteKey);
				$this->fileId = $fileInfo->fileId;

				Factory::getLog()->log(LogLevel::DEBUG, "{$this->engineLogName} -- Got fileID {$this->fileId}");

				$this->uploadUrl  = $connector->getPartUploadUrl($this->fileId);
				$this->partNumber = 1;
				$this->sha1Parts  = array();

				Factory::getLog()->log(LogLevel::DEBUG, "{$this->engineLogName} -- Got uploadURL {$this->uploadUrl->uploadUrl} - Upload Authorization {$this->uploadUrl->authorizationToken}");
			}
			catch (\Exception $e)
			{
				Factory::getLog()
					->log(LogLevel::DEBUG, "{$this->engineLogName} -- Failed to initialize multipart upload of $sourceFile");
				$this->setWarning('Upload cannot be initialised. ' . $this->engineLogName . ' returned an error message: ' . $e->getCode() . ' :: ' . $e->getMessage());

				return false;
			}
		}
		else
		{
			Factory::getLog()
				->log(LogLevel::DEBUG, "{$this->engineLogName} -- Continuing multipart upload of $sourceFile (fileId: {$this->fileId} –– Part number {$this->partNumber})");
		}

		// Upload a chunk
		$mustFinalize = false;

		try
		{
			$partSize          = $this->getPartSizeForFile($sourceFile, $connector);
			$fileInfo          = $connector->uploadPart($this->uploadUrl, $sourceFile, $this->partNumber, $partSize);
			$this->sha1Parts[] = $fileInfo->contentSha1;
			$this->partNumber++;
		}
		catch (\OutOfBoundsException $e)
		{
			$mustFinalize = true;
		}
		catch (\Exception $e)
		{
			Factory::getLog()
				->log(LogLevel::DEBUG, "{$this->engineLogName} -- Multipart upload of $sourceFile has failed.");
			$this->setWarning('Upload cannot proceed. ' . $this->engineLogName . ' returned an error message: ' . $e->getCode() . ' :: ' . $e->getMessage());

			// Reset the multipart markers in temporary storage
			$akeebaConfig = Factory::getConfiguration();
			$akeebaConfig->set($this->volatileKeyPrefix . 'fileId', null);
			$akeebaConfig->set($this->volatileKeyPrefix . 'uploadUrl', null);
			$akeebaConfig->set($this->volatileKeyPrefix . 'partNumber', null);
			$akeebaConfig->set($this->volatileKeyPrefix . 'sha1Parts', null);

			return false;
		}

		// When we are done uploading we have to finalize
		if ($mustFinalize)
		{
			$count = count($this->sha1Parts);
			Factory::getLog()
				->log(LogLevel::DEBUG, "{$this->engineLogName} -- Finalising multipart upload of $sourceFile (fileId: {$this->fileId} –– $count parts in total)");

			$fileInfo = $connector->finishUpload($this->fileId, $this->sha1Parts);

			Factory::getLog()
				->log(LogLevel::DEBUG, "{$this->engineLogName} -- Finalised multipart upload of $sourceFile (fileId: {$fileInfo->fileId})");

			$this->fileId     = null;
			$this->uploadUrl  = null;
			$this->partNumber = null;
			$this->sha1Parts  = array();
		}

		// Save the internal tracking variables
		$akeebaConfig     = Factory::getConfiguration();
		$uploadURLAsArray = is_null($this->uploadUrl) ? array() : $this->uploadUrl->toArray();
		$akeebaConfig->set($this->volatileKeyPrefix . 'fileId', $this->fileId);
		$akeebaConfig->set($this->volatileKeyPrefix . 'uploadUrl', $uploadURLAsArray);
		$akeebaConfig->set($this->volatileKeyPrefix . 'partNumber', $this->partNumber);
		$akeebaConfig->set($this->volatileKeyPrefix . 'sha1Parts', json_encode($this->sha1Parts));

		// If I have an upload ID I have to do more work
		if (is_string($this->fileId) && !empty($this->fileId))
		{
			return 1;
		}

		// In any other case I'm done uploading the file
		return true;
	}

	/**
	 * Perform a single-step upload of a file
	 *
	 * @param   string             $bucketId   The bucket ID to upload to
	 * @param   string             $remoteKey  The remote filename
	 * @param   string             $sourceFile The full path to the local source file
	 * @param   ConnectorBackblaze $connector  The S3 client object instance
	 *
	 * @return  bool|int  True when we're done uploading, false if an error occurs, 1 if we have more parts
	 */
	protected function simpleUpload($bucketId, $remoteKey, $sourceFile, ConnectorBackblaze $connector)
	{
		Factory::getLog()
			->log(LogLevel::DEBUG, "{$this->engineLogName} -- Single part upload of " . basename($sourceFile));

		try
		{
			$connector->uploadSingleFile($bucketId, $remoteKey, $sourceFile);
		}
		catch (\Exception $e)
		{
			$this->setWarning($e->getCode() . ' :: ' . $e->getMessage());

			return false;
		}

		return true;
	}

	/**
	 * Get a configured S3 client object.
	 *
	 * @return  ConnectorBackblaze
	 */
	protected function &getConnector()
	{
		// Retrieve engine configuration data
		$config = $this->getEngineConfiguration();

		// Get the configuration parameters
		$accountId        = $config['accountId'];
		$applicationKey   = $config['applicationKey'];
		$bucket           = $config['bucket'];

		// Required since we're returning by reference
		$null = null;

		// Remove any slashes from the bucket
		$bucket = str_replace('/', '', $bucket);

		// Sanity checks
		if (empty($accountId))
		{
			$this->setError('You have not set up your ' . $this->engineLogName . ' Account ID');

			return $null;
		}

		if (empty($applicationKey))
		{
			$this->setError('You have not set up your ' . $this->engineLogName . ' Application Key');

			return $null;
		}

		if (!function_exists('curl_init'))
		{
			$this->setWarning('cURL is not enabled, please enable it in order to post-process your archives');

			return null;
		}

		if (empty($bucket))
		{
			$this->setError('You have not set up your ' . $this->engineLogName . ' Bucket');

			return $null;
		}


		// Create the API connector instance
		$connector = new ConnectorBackblaze($accountId, $applicationKey);

		return $connector;
	}

	/**
	 * Get the configuration information for this post-processing engine
	 *
	 * @return  array
	 */
	protected function getEngineConfiguration()
	{
		$akeebaConfig = Factory::getConfiguration();

		return array(
			'accountId'        => $akeebaConfig->get('engine.postproc.backblaze.accountId', ''),
			'applicationKey'   => $akeebaConfig->get('engine.postproc.backblaze.applicationKey', ''),
			'disableMultipart' => $akeebaConfig->get('engine.postproc.backblaze.disableMultipart', 0),
			'bucket'           => $akeebaConfig->get('engine.postproc.backblaze.bucket', null),
			'directory'        => $akeebaConfig->get('engine.postproc.backblaze.directory', null),
			'chunkInMB'        => $akeebaConfig->get('engine.postproc.backblaze.chunk_upload_size', null),
		);
	}

	/**
	 * Get the bucket ID, fetching it from BackBlaze if it's not already populated
	 *
	 * @param   ConnectorBackblaze  $connector
	 *
	 * @return  string|bool  The bucket ID or false if fetching it failed
	 */
	protected function getBucketId(ConnectorBackblaze $connector)
	{
		if (empty($this->bucketId) && ($this->bucketId !== false))
		{
			$akeebaConfig = Factory::getConfiguration();
			$engineConfig = $this->getEngineConfiguration();

			try
			{
				$bucket         = $engineConfig['bucket'];
				$bucket         = str_replace('/', '', $bucket);
				$this->bucketId = $connector->getBucketId($bucket);
				$akeebaConfig->set($this->volatileKeyPrefix . 'bucketId', $this->bucketId);
			}
			catch (BackblazeBaseException $e)
			{
				$this->bucketId = false;
			}
		}

		return $this->bucketId;
	}

	/**
	 * Get the applicable part size for a given file. The part size cannot be smaller than the absolute minimum part
	 * size reported by Backblaze (typically 5MB). It also cannot be smaller than the file size divided by 10,000 as
	 * Backblaze will only allow us to upload up to 10,000 parts. This algorithm will try to use the user selected
	 * part size unless it is smaller than these hard requirements.
	 *
	 * Finally note that the part size is cached in volatile storage so that subsequent queries about it will not result
	 * in a performance penalty.
	 *
	 * @param   string              $sourceFile  The local file we want to figure out the part size for
	 * @param   ConnectorBackblaze  $connector   The BackBlaze connector
	 *
	 * @return  int
	 */
	protected function getPartSizeForFile($sourceFile, ConnectorBackblaze $connector)
	{
		$akeebaConfig    = Factory::getConfiguration();
		$savedSourceFile = $akeebaConfig->get($this->volatileKeyPrefix . 'partSizeFile', null);
		$savedPartSize   = $akeebaConfig->get($this->volatileKeyPrefix . 'partSizeValue', null);

		if ($savedSourceFile == $sourceFile)
		{
			return $savedPartSize;
		}

		// Get the part size. Must be <= 100 MB
		$engineConfig = $this->getEngineConfiguration();
		$minPartSize  = $connector->getAccountInformation()->absoluteMinimumPartSize;
		$partSize     = min($engineConfig['chunkInMB'], 100);
		$partSize     = $partSize * 1024 * 1024;
		$partSize     = max($minPartSize, $partSize);

		clearstatcache(false, $sourceFile);
		$fileSize = @filesize($sourceFile);

		/**
		 * Backblaze supports up to 10000 parts. We have to try increasing the part size until we're sure our  file
		 * will upload in a number of parts that's less than that.
		 */
		while ($fileSize / $partSize > 10000)
		{
			// Increase by 5M in each step
			$partSize += 5242880;
		}

		$akeebaConfig->set($this->volatileKeyPrefix . 'partSizeFile', $sourceFile);
		$akeebaConfig->set($this->volatileKeyPrefix . 'partSizeValue', $partSize);

		return $partSize;
	}
}
