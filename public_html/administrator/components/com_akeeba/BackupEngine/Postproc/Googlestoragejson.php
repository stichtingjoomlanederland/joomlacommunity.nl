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
use Akeeba\Engine\Platform;
use Akeeba\Engine\Postproc\Connector\GoogleStorage as ConnectorGoogleStorage;
use Psr\Log\LogLevel;

class Googlestoragejson extends Base
{
	/** @var int The retry count of this file (allow up to 2 retries after the first upload failure) */
	private $tryCount = 0;

	/** @var ConnectorGoogleStorage The Google Drive API instance */
	private $connector;

	/** @var string The currently configured bucket */
	private $bucket;

	/** @var string The currently configured directory */
	private $directory;

	/** @var bool Are we using chunk uploads? */
	private $chunked = false;

	/** @var int Chunk size (MB) */
	private $chunk_size = 10;

	/** @var array The decoded Google Cloud JSON configuration file */
	private $config = array();

	public function __construct()
	{
		$this->can_download_to_browser = false;
		$this->can_delete = true;
		$this->can_download_to_file = true;
	}

	/**
	 * This function takes care of post-processing a backup archive's part, or the
	 * whole backup archive if it's not a split archive type. If the process fails
	 * it should return false. If it succeeds and the entirety of the file has been
	 * processed, it should return true. If only a part of the file has been uploaded,
	 * it must return 1.
	 *
	 * @param   string $absolute_filename Absolute path to the part we'll have to process
	 * @param   string $upload_as         Base name of the uploaded file, skip to use $absolute_filename's
	 *
	 * @return  boolean|integer  False on failure, true on success, 1 if more work is required
	 */
	public function processPart($absolute_filename, $upload_as = null)
	{
		// Make sure we can get a connector object
		$validSettings = $this->initialiseConnector();

		if ($validSettings === false)
		{
			return false;
		}

		// Get a reference to the engine configuration
		$config = Factory::getConfiguration();

		// Store the absolute remote path in the class property
		$directory         = $this->directory;
		$basename          = empty($upload_as) ? basename($absolute_filename) : $upload_as;
		$this->remote_path = $directory . '/' . $basename;

		// Check if the size of the file is compatible with chunked uploading
		clearstatcache();
		$totalSize   = filesize($absolute_filename);
		$isBigEnough = $this->chunked ? ($totalSize > $this->chunk_size) : false;

		// Chunked uploads if the feature is enabled and the file is at least as big as the chunk size.
		if ($this->chunked && $isBigEnough)
		{
			Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - Using chunked upload, part size {$this->chunk_size}");

			$offset    = $config->get('volatile.engine.postproc.googlestoragejson.offset', 0);
			$upload_id = $config->get('volatile.engine.postproc.googlestoragejson.upload_id', null);

			if (empty($upload_id))
			{
				Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - Creating new upload session");

				try
				{
					$upload_id = $this->connector->createUploadSession($this->bucket, $this->remote_path, $absolute_filename);
				}
				catch (\Exception $e)
				{
					$this->setWarning("The upload session for remote file {$this->remote_path} cannot be created. Debug info: #" . $e->getCode() . ' – ' . $e->getMessage());

					return false;
				}

				Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - New upload session $upload_id");
				$config->set('volatile.engine.postproc.googlestoragejson.upload_id', $upload_id);
			}

			try
			{
				if (empty($offset))
				{
					$offset = 0;
				}

				Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - Uploading chunked part");

				$result = $this->connector->uploadPart($upload_id, $absolute_filename, $offset, $this->chunk_size);

				Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - Got uploadPart result " . print_r($result, true));
			}
			catch (\Exception $e)
			{
				Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - Got uploadPart Exception " . $e->getCode() . ': ' . $e->getMessage());

				$this->setWarning($e->getMessage());

				$result = false;
			}

			// Did we fail uploading?
			if ($result === false)
			{
				// Let's retry
				$this->tryCount++;

				// However, if we've already retried twice, we stop retrying and call it a failure
				if ($this->tryCount > 2)
				{
					Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - Maximum number of retries exceeded. The upload has failed.");

					return false;
				}

				Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - Error detected, retrying chunk upload");

				return -1;
			}

			// Are we done uploading?
			$nextOffset = $offset + $this->chunk_size - 1;

			if (isset($result['name']) || ($nextOffset > $totalSize))
			{
				Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - Chunked upload is now complete");

				$config->set('volatile.engine.postproc.googlestoragejson.offset', null);
				$config->set('volatile.engine.postproc.googlestoragejson.upload_id', null);

				return true;
			}

			// Otherwise, continue uploading
			$config->set('volatile.engine.postproc.googlestoragejson.offset', $offset + $this->chunk_size);

			return -1;
		}

		// Single part upload
		try
		{
			Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - Performing simple upload.");

			$result = $this->connector->simpleUpload($this->bucket, $this->remote_path, $absolute_filename);
		}
		catch (\Exception $e)
		{
			Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - Simple upload failed, " . $e->getCode() . ": " . $e->getMessage());

			$this->setWarning($e->getMessage());

			$result = false;
		}

		if ($result === false)
		{
			// Let's retry
			$this->tryCount++;

			// However, if we've already retried twice, we stop retrying and call it a failure
			if ($this->tryCount > 2)
			{
				Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - Maximum number of retries exceeded. The upload has failed.");

				return false;
			}

			Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - Error detected, retrying upload");

			return -1;
		}

		// Upload complete. Reset the retry counter.
		$this->tryCount = 0;

		return true;
	}

	/**
	 * Downloads a remote file to a local file, optionally doing a range download. If the
	 * download fails we return false. If the download succeeds we return true. If range
	 * downloads are not supported, -1 is returned and nothing is written to disk.
	 *
	 * @param $remotePath string The path to the remote file
	 * @param $localFile  string The absolute path to the local file we're writing to
	 * @param $fromOffset int|null The offset (in bytes) to start downloading from
	 * @param $length     int|null The amount of data (in bytes) to download
	 *
	 * @return bool|int True on success, false on failure, -1 if ranges are not supported
	 */
	public function downloadToFile($remotePath, $localFile, $fromOffset = null, $length = null)
	{
		// Get settings
		$settings = $this->initialiseConnector();

		if ($settings === false)
		{
			return false;
		}

		if (!is_null($fromOffset))
		{
			// Ranges are not supported
			return -1;
		}

		// Download the file
		try
		{
			$this->connector->download($this->bucket, $remotePath, $localFile);
		}
		catch (\Exception $e)
		{
			$this->setWarning($e->getMessage());

			return false;
		}

		return true;
	}

	public function delete($path)
	{
		// Get settings
		$settings = $this->initialiseConnector();

		if ($settings === false)
		{
			return false;
		}

		try
		{
			$this->connector->delete($this->bucket, $path, true);
		}
		catch (\Exception $e)
		{
			$this->setWarning($e->getMessage());

			return false;
		}

		return true;
	}

	/**
	 * Initialises the Google Storage connector object
	 *
	 * @return  bool  True on success, false if we cannot proceed
	 */
	protected function initialiseConnector()
	{
		// Retrieve engine configuration data
		$config = Factory::getConfiguration();

		if (!$this->canReadJsonConfig())
		{
			return false;
		}

		$this->chunked    = $config->get('engine.postproc.googlestoragejson.chunk_upload', true);
		$this->chunk_size = $config->get('engine.postproc.googlestoragejson.chunk_upload_size', 10) * 1024 * 1024;
		$this->bucket     = $config->get('engine.postproc.googlestoragejson.bucket', null);
		$this->directory  = $config->get('volatile.postproc.directory', null);

		if (empty($this->directory))
		{
			$this->directory = $config->get('engine.postproc.googlestoragejson.directory', '');
		}

		// Environment checks
		if (!function_exists('curl_init'))
		{
			$this->setWarning('cURL is not enabled, please enable it in order to post-process your archives');

			return false;
		}

		if (!function_exists('openssl_sign') || !function_exists('openssl_get_md_methods'))
		{
			$this->setWarning('The PHP module for OpenSSL integration is not enabled or openssl_sign() is disabled. Please contact your host and ask them to fix this issue for the version of PHP you are currently using on your site (PHP reports itself as version ' . PHP_VERSION . ').');

			return false;
		}

		$openSSLAlgos = openssl_get_md_methods(true);

		if (!in_array('sha256WithRSAEncryption', $openSSLAlgos))
		{
			$this->setWarning('The PHP module for OpenSSL integration does not support the sha256WithRSAEncryption signature algorithm. Please ask your host to compile BOTH a newer version of the OpenSSL library AND the OpenSSL module for PHP against this (new) OpenSSL library for the version of PHP you are currently using on your site (PHP reports itself as version ' . PHP_VERSION . ').');

			return false;

		}

		// Fix the directory name, if required
		if (!empty($this->directory))
		{
			$this->directory = trim($this->directory);
			$this->directory = ltrim(Factory::getFilesystemTools()->TranslateWinPath($this->directory), '/');
		}
		else
		{
			$this->directory = '';
		}

		// Parse tags
		$this->directory = Factory::getFilesystemTools()->replace_archive_name_variables($this->directory);
		$config->set('volatile.postproc.directory', $this->directory);

		$this->connector = new ConnectorGoogleStorage($this->config['client_email'], $this->config['private_key']);

		return true;
	}

	/**
	 * Tries to read the Google Cloud JSON credentials from the configuration. If something doesn't work out it will
	 * return false.
	 *
	 * @return  bool
	 */
	protected function canReadJsonConfig()
	{
		$config = Factory::getConfiguration();

		$hasJsonConfig = false;
		$jsonConfig    = trim($config->get('engine.postproc.googlestoragejson.jsoncreds', ''));
		$jsonConfig    = $this->fixStoredJSON($jsonConfig);

		if (!empty($jsonConfig))
		{
			$hasJsonConfig = true;
			$this->config  = @json_decode($jsonConfig, true);
		}

		if (empty($this->config))
		{
			$hasJsonConfig = false;
		}

		if ($hasJsonConfig && (
				!isset($this->config['type']) ||
				!isset($this->config['project_id']) ||
				!isset($this->config['private_key']) ||
				!isset($this->config['client_email'])
			)
		)
		{
			$hasJsonConfig = false;
		}

		if ($hasJsonConfig && (
				($this->config['type'] != 'service_account') ||
				(empty($this->config['project_id'])) ||
				(empty($this->config['private_key'])) ||
				(empty($this->config['client_email']))
			)
		)
		{
			$hasJsonConfig = false;
		}

		if (!$hasJsonConfig)
		{
			$this->config = [];
			$this->setWarning('You have not provided a valid Google Cloud JSON configuration (googlestorage.json) in the configuration page. As a result I cannot upload anything to Google Storage. Please fix this issue and try backing up again.');

			return false;
		}

		return true;
	}

	/**
	 * The Google JSON API file has the string literal "\n" inside the Private Key. However, the INI parser will
	 * unescape this into a newline character. This causes a newline character to appear inside a string literal in the
	 * JSON file, therefore rendering the JSON invalid.
	 *
	 * Before Akeeba Engine 6.3.4 we used to deal with that by using the PHP INI parser in INI_SCANNER_NORMAL mode.
	 * However, this caused some problems, e.g. with the sequence \$ being squashed to $. The correct solution is using
	 * INI_SCANNER_RAW. However, this does not expand \n in values which is something we need elsewhere in the Engine.
	 *
	 * We needed to do this because before 6.3.4 if your server had disabled parse_ini_string our PHP-based parser would
	 * yield different results than calling PHP's parse_ini_file(). This also meant that on these hosts Google Storage
	 * JSON API was broken.
	 *
	 * The only solution is having this method which recodes the private key in a way that the JSON is valid and the
	 * private key is also usable with Google's API.
	 *
	 * @param   string  $jsonConfig
	 *
	 * @return  string
	 */
	private function fixStoredJSON($jsonConfig)
	{
		// Remove all newlines
		$jsonConfig = str_replace("\n", '', $jsonConfig);

		// Extract the private key
		$startPos = strpos($jsonConfig, '-----BEGIN PRIVATE KEY-----');
		$endPos   = strpos($jsonConfig, '-----END PRIVATE KEY-----') + 25;
		$pk       = substr($jsonConfig, $startPos, $endPos - $startPos);

		// Recode the private key
		$innerPK = trim(substr($pk, 27, -25));
		$innerPK = implode("\\n", str_split($innerPK, 64));
		$pk   = "-----BEGIN PRIVATE KEY-----\\n" . $innerPK ."\\n-----END PRIVATE KEY-----";

		// Assemble a usable JSON string
		return rtrim(substr($jsonConfig, 0, $startPos)) . $pk . ltrim(substr($jsonConfig, $endPos));
	}
}
