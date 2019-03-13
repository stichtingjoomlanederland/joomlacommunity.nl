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
use Akeeba\Engine\Postproc\Connector\S3v4\Configuration;
use Akeeba\Engine\Postproc\Connector\S3v4\Connector;
use Akeeba\Engine\Postproc\Connector\S3v4\Input;
use Psr\Log\LogLevel;

/**
 * Upload to Amazon S3 (new version) post-processing engine for Akeeba Engine
 *
 * @package Akeeba\Engine\Postproc
 */
class Amazons3 extends Base
{
	/**
	 * The upload ID of the multipart upload in progress
	 *
	 * @var   null|string
	 */
	protected $uploadId = null;

	/**
	 * The part number for the multipart upload in progress
	 *
	 * @var null|int
	 */
	protected $partNumber = null;

	/**
	 * The ETags of the uploaded chunks, used to finalise the multipart upload
	 *
	 * @var  array
	 */
	protected $eTags = array();

	/**
	 * Used in log messages. Check out children classes to understand why we have this here.
	 *
	 * @var  string
	 */
	protected $engineLogName = 'Amazon S3';

	/**
	 * The prefix to use for volatile key storage
	 *
	 * @var  string
	 */
	protected $volatileKeyPrefix = 'volatile.postproc.amazons3.';

	/**
	 * HTTP headers. Used when trying to fetch the S3 credentials from an EC2 instance's attached role.
	 *
	 * @var  array
	 */
	protected $headers = array();

	/**
	 * Cached copy of the S3 credentials provisioned by the EC2 instance's attached role.
	 *
	 * @var  null|array
	 */
	protected $provisionedCredentials = null;

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
	 * @param   string $absolute_filename Absolute path to the part we'll have to process
	 * @param   string $upload_as         Base name of the uploaded file, skip to use $absolute_filename's
	 *
	 * @return  boolean|integer  False on failure, true on success, 1 if more work is required
	 */
	public function processPart($absolute_filename, $upload_as = null)
	{
		// Retrieve engine configuration data
		$akeebaConfig = Factory::getConfiguration();

		// Load multipart information from temporary storage
		$this->uploadId = $akeebaConfig->get($this->volatileKeyPrefix . 'uploadId', null);

		// Get the configuration parameters
		$engineConfig     = $this->getEngineConfiguration();
		$bucket           = $engineConfig['bucket'];
		$disableMultipart = $engineConfig['disableMultipart'];
		$storageType      = $engineConfig['rrs'];

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

		// Remove any slashes from the bucket
		$bucket = str_replace('/', '', $bucket);

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

		// Create the S3 client instance
		$s3Client = $this->getS3Client();

		if (!is_object($s3Client))
		{
			return false;
		}

		// Are we already processing a multipart upload or asked to perform a multipart upload?
		if (!empty($this->uploadId) || !$disableMultipart)
		{
			$this->partNumber = $akeebaConfig->get($this->volatileKeyPrefix . 'partNumber', null);
			$this->eTags      = $akeebaConfig->get($this->volatileKeyPrefix . 'eTags', '{}');
			$this->eTags      = json_decode($this->eTags, true);
			$this->eTags      = empty($this->eTags) ? array() : $this->eTags;

			return $this->multipartUpload($bucket, $remoteKey, $absolute_filename, $s3Client, 'bucket-owner-full-control', $storageType);
		}

		return $this->simpleUpload($bucket, $remoteKey, $absolute_filename, $s3Client, 'bucket-owner-full-control', $storageType);
	}

	/**
	 * Deletes a remote file
	 *
	 * @param $path string Absolute path to the file we're deleting
	 *
	 * @return bool|int False on failure, true on success, 1 if more work is required
	 */
	public function delete($path)
	{
		// Get the configuration parameters
		$engineConfig = $this->getEngineConfiguration();
		$bucket = $engineConfig['bucket'];
		$bucket = str_replace('/', '', $bucket);

		// Create the S3 client instance
		$s3Client = $this->getS3Client();

		if ( !is_object($s3Client))
		{
			return false;
		}

		try
		{
			$s3Client->deleteObject($bucket, $path);
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
	 * @param $remotePath string The path to the remote file
	 * @param $localFile  string The absolute path to the local file we're writing to
	 * @param $fromOffset int|null The offset (in bytes) to start downloading from
	 * @param $length     int|null The amount of data (in bytes) to download
	 *
	 * @return bool|int True on success, false on failure, -1 if ranges are not supported
	 */
	public function downloadToFile($remotePath, $localFile, $fromOffset = null, $length = null)
	{
		// Get the configuration parameters
		$engineConfig = $this->getEngineConfiguration();
		$bucket = $engineConfig['bucket'];
		$bucket = str_replace('/', '', $bucket);

		// Create the S3 client instance
		$s3Client = $this->getS3Client();

		if ( !is_object($s3Client))
		{
			return false;
		}

		$toOffset = null;

		if ($fromOffset && $length)
		{
			$toOffset                  = $fromOffset + $length - 1;
			$serviceArguments['Range'] = $fromOffset . '-' . $toOffset;
		}

		try
		{
			$s3Client->getObject($bucket, $remotePath, $localFile, $fromOffset, $toOffset);
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
	 * @param $remotePath string The file to download
	 *
	 * @return string|bool
	 */
	public function downloadToBrowser($remotePath)
	{
		// Get the configuration parameters
		$engineConfig = $this->getEngineConfiguration();
		$bucket = $engineConfig['bucket'];
		$bucket = str_replace('/', '', $bucket);

		// Create the S3 client instance
		$s3Client = $this->getS3Client();

		if ( !is_object($s3Client))
		{
			return false;
		}

		return $s3Client->getAuthenticatedURL($bucket, $remotePath, 10, true);
	}

	/**
	 * Start a multipart upload
	 *
	 * @param   string    $bucket      The bucket to upload to
	 * @param   string    $remoteKey   The remote filename
	 * @param   string    $sourceFile  The full path to the local source file
	 * @param   Connector $s3Client    The S3 client object instance
	 * @param   string    $acl         Canned ACL privileges to use
	 * @param   int       $storageType The Amazon S3 storage type (0=standard, 1=RRS, 2=Standard-IA)
	 *
	 * @return  bool|int  True when we're done uploading, false if an error occurs, 1 if we have more parts
	 */
	protected function multipartUpload($bucket, $remoteKey, $sourceFile, $s3Client, $acl = 'bucket-owner-full-control', $storageType = 0)
	{
		$endpoint = $s3Client->getConfiguration()->getEndpoint();
		$headers  = array();

		if ($endpoint == 's3.amazonaws.com')
		{
			$headers = array();

			switch ($storageType)
			{
				case 0:
					$headers['X-Amz-Storage-Class'] = 'STANDARD';
					break;

				case 1:
					$headers['X-Amz-Storage-Class'] = 'REDUCED_REDUNDANCY';
					break;

				case 2:
					$headers['X-Amz-Storage-Class'] = 'STANDARD_IA';
					break;

				case 3:
					$headers['X-Amz-Storage-Class'] = 'ONEZONE_IA';
					break;
			}
		}

		$input = Input::createFromFile($sourceFile, null, null);

		if (empty($this->uploadId))
		{
			Factory::getLog()->log(LogLevel::DEBUG, "{$this->engineLogName} -- Beginning multipart upload of $sourceFile");

			// Initialise the multipart upload if necessary
			try
			{
				$this->uploadId   = $s3Client->startMultipart($input, $bucket, $remoteKey, $acl, $headers);
				$this->partNumber = 1;
				$this->eTags      = array();

				Factory::getLog()->log(LogLevel::DEBUG, "{$this->engineLogName} -- Got uploadID {$this->uploadId}");
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
				->log(LogLevel::DEBUG, "{$this->engineLogName} -- Continuing multipart upload of $sourceFile (UploadId: {$this->uploadId} –– Part number {$this->partNumber})");
		}

		// Upload a chunk
		try
		{
			$input = Input::createFromFile($sourceFile, null, null);
			$input->setUploadID($this->uploadId);
			$input->setPartNumber($this->partNumber);
			$input->setEtags($this->eTags);

			// Do NOT send $headers when uploading parts. The RRS header MUST ONLY be sent when we're beginning the multipart upload.
			$eTag = $s3Client->uploadMultipart($input, $bucket, $remoteKey);

			if (!is_null($eTag))
			{
				$this->eTags[] = $eTag;
				$this->partNumber = $input->getPartNumber();
				$this->partNumber++;
			}
			else
			{
				// We just finished. Let's finalise the upload
				$count = count($this->eTags);
				Factory::getLog()
					->log(LogLevel::DEBUG, "{$this->engineLogName} -- Finalising multipart upload of $sourceFile (UploadId: {$this->uploadId} –– $count parts in total");

				$input = Input::createFromFile($sourceFile, null, null);
				$input->setUploadID($this->uploadId);
				$input->setPartNumber($this->partNumber);
				$input->setEtags($this->eTags);

				$s3Client->finalizeMultipart($input, $bucket, $remoteKey);

				$this->uploadId = null;
				$this->partNumber = null;
				$this->eTags = array();
			}
		}
		catch (\Exception $e)
		{
			Factory::getLog()
				->log(LogLevel::DEBUG, "{$this->engineLogName} -- Multipart upload of $sourceFile has failed.");
			$this->setWarning('Upload cannot proceed. ' . $this->engineLogName . ' returned an error message: ' . $e->getCode() . ' :: ' . $e->getMessage());

			// Reset the multipart markers in temporary storage
			$akeebaConfig = Factory::getConfiguration();
			$akeebaConfig->set($this->volatileKeyPrefix . 'uploadId', null);
			$akeebaConfig->set($this->volatileKeyPrefix . 'partNumber', null);
			$akeebaConfig->set($this->volatileKeyPrefix . 'eTags', null);

			return false;
		}

		// Save the internal tracking variables
		$akeebaConfig = Factory::getConfiguration();
		$akeebaConfig->set($this->volatileKeyPrefix . 'uploadId', $this->uploadId);
		$akeebaConfig->set($this->volatileKeyPrefix . 'partNumber', $this->partNumber);
		$akeebaConfig->set($this->volatileKeyPrefix . 'eTags', json_encode($this->eTags));

		// If I have an upload ID I have to do more work
		if (is_string($this->uploadId) && !empty($this->uploadId))
		{
			return 1;
		}

		// In any other case I'm done uploading the file
		return true;
	}

	/**
	 * Perform a single-step upload of a file
	 *
	 * @param   string    $bucket      The bucket to upload to
	 * @param   string    $remoteKey   The remote filename
	 * @param   string    $sourceFile  The full path to the local source file
	 * @param   Connector $s3Client    The S3 client object instance
	 * @param   string    $acl         Canned ACL privileges to use
	 * @param   int       $storageType The Amazon S3 storage type (0=standard, 1=RRS, 2=Standard-IA)
	 *
	 * @return  bool|int  True when we're done uploading, false if an error occurs, 1 if we have more parts
	 */
	protected function simpleUpload($bucket, $remoteKey, $sourceFile, Connector $s3Client, $acl = 'bucket-owner-full-control', $storageType = 0)
	{
		Factory::getLog()
			->log(LogLevel::DEBUG, "{$this->engineLogName} -- Legacy (single part) upload of " . basename($sourceFile));

		$endpoint = $s3Client->getConfiguration()->getEndpoint();
		$headers  = array();

		if ($endpoint == 's3.amazonaws.com')
		{
			$headers = array();

			switch ($storageType)
			{
				case 0:
					$headers['X-Amz-Storage-Class'] = 'STANDARD';
					break;

				case 1:
					$headers['X-Amz-Storage-Class'] = 'REDUCED_REDUNDANCY';
					break;

				case 2:
					$headers['X-Amz-Storage-Class'] = 'STANDARD_IA';
					break;

				case 3:
					$headers['X-Amz-Storage-Class'] = 'ONEZONE_IA';
					break;
			}
		}

		$input = Input::createFromFile($sourceFile, null, null);

		try
		{
			$s3Client->putObject($input, $bucket, $remoteKey, $acl, $headers);
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
	 * @return  Connector
	 */
	protected function &getS3Client()
	{
		// Retrieve engine configuration data
		$config = $this->getEngineConfiguration();

		// Get the configuration parameters
		$accessKey        = $config['accessKey'];
		$secretKey        = $config['secretKey'];
		$useSSL           = $config['useSSL'];
		$customEndpoint   = $config['customEndpoint'];
		$signatureMethod  = $config['signatureMethod'];
		$region           = $config['region'];
		$disableMultipart = $config['disableMultipart'];
		$bucket           = $config['bucket'];

		// Required since we're returning by reference
		$null = null;

		if ($signatureMethod == 's3')
		{
			$signatureMethod = 'v2';
		}

		Factory::getLog()
			->log(LogLevel::DEBUG, "{$this->engineLogName} -- Using signature method $signatureMethod, " . ($disableMultipart ? 'single-part' : 'multipart') . ' uploads');

		// Makes sure the custom endpoint has no protocol and no trailing slash
		$customEndpoint = trim($customEndpoint);

		if ( !empty($customEndpoint))
		{
			$protoPos = strpos($customEndpoint, ':\\');

			if ($protoPos !== false)
			{
				$customEndpoint = substr($customEndpoint, $protoPos + 3);
			}

			$customEndpoint = rtrim($customEndpoint, '/');

			Factory::getLog()
				->log(LogLevel::DEBUG, "{$this->engineLogName} -- Using custom endpoint $customEndpoint");
		}

		// Remove any slashes from the bucket
		$bucket = str_replace('/', '', $bucket);

		// Sanity checks
		if (empty($accessKey))
		{
			$this->setError('You have not set up your ' . $this->engineLogName . ' Access Key');

			return $null;
		}

		if (empty($secretKey))
		{
			$this->setError('You have not set up your ' . $this->engineLogName . ' Secret Key');

			return $null;
		}

		if ( !function_exists('curl_init'))
		{
			$this->setWarning('cURL is not enabled, please enable it in order to post-process your archives');

			return null;
		}

		if (empty($bucket))
		{
			$this->setError('You have not set up your ' . $this->engineLogName . ' Bucket');

			return $null;
		}


		// Prepare the configuration
		$configuration = new Configuration($accessKey, $secretKey, $signatureMethod, $region);
		$configuration->setSSL($useSSL);

		if (!empty($config['token']))
		{
			$configuration->setToken($config['token']);
		}

		if ($customEndpoint)
		{
			$configuration->setEndpoint($customEndpoint);
		}

		// If we're dealing with China AWS, we have to use the Legacy Paths
		if ($region == 'cn-north-1')
		{
			$configuration->setUseLegacyPathStyle(true);
		}

		// Create the S3 client instance
		$s3Client = new Connector($configuration);

		return $s3Client;
	}

	/**
	 * Get the configuration information for this post-processing engine
	 *
	 * @return  array
	 */
	protected function getEngineConfiguration()
	{
		$akeebaConfig = Factory::getConfiguration();

		$config = array(
			'accessKey'        => $akeebaConfig->get('engine.postproc.amazons3.accesskey', ''),
			'secretKey'        => $akeebaConfig->get('engine.postproc.amazons3.secretkey', ''),
			'token'            => '',
			'useSSL'           => $akeebaConfig->get('engine.postproc.amazons3.usessl', 0),
			'customEndpoint'   => $akeebaConfig->get('engine.postproc.amazons3.customendpoint', ''),
			'signatureMethod'  => $akeebaConfig->get('engine.postproc.amazons3.signature', 'v2'),
			'region'           => $akeebaConfig->get('engine.postproc.amazons3.region', ''),
			'disableMultipart' => $akeebaConfig->get('engine.postproc.amazons3.legacy', 0),
			'bucket'           => $akeebaConfig->get('engine.postproc.amazons3.bucket', null),
			'directory'        => $akeebaConfig->get('engine.postproc.amazons3.directory', null),
			'rrs'              => $akeebaConfig->get('engine.postproc.amazons3.rrs', null),
		);

		// No access and secret key? Try to fetch from the EC2 configuration
		if (empty($config['accessKey']) && empty($config['secretKey']))
		{
			Factory::getLog()->debug("There is no configured Access and Secret key. I will try to provision these credentials automatically. This only works when your site runs inside an EC2 instance and you have attached an IAM Role to it which allows access to the configured bucket.");
			$config = $this->provisionCredentials($config);
		}

		return $config;
	}

	/**
	 * Try to automatically provision the S3 credentials. The credentials are searched in the following places (the
	 * first one to be found wins):
	 *
	 * - The provisionedCredentials volatile key for this post-processing engine
	 * - The provisionedCredentials property
	 * - Querying the EC2 instance we are running under (assuming we run under an EC2 instance)
	 *
	 * If the cached provisionedCredentials have expired new ones will be fetched by querying the metadata of the
	 * underlying EC2 instance.
	 *
	 * If no provisioned credentials are found, the returned $config array is identical to the input, presumably lacking
	 * access and secret keys to connect to S3.
	 *
	 * @param   array  $config
	 *
	 * @return  array
	 */
	protected function provisionCredentials(array $config)
	{
		// First, try to fetch credentials from the volatile engine configuration
		$akeebaConfig                 = Factory::getConfiguration();
		$this->provisionedCredentials = $akeebaConfig->get($this->volatileKeyPrefix . 'provisionedCredentials', $this->provisionedCredentials);

		// I must fetch new credentials if I don't have any provisioned credentials
		$mustFetchCredentials = !is_array($this->provisionedCredentials) || empty($this->provisionedCredentials);

		if (!$mustFetchCredentials)
		{
			Factory::getLog()->debug('Cached S3 credentials were found');
		}

		// I must fetch new credentials if the provisioned credentials have already expired
		if (!$mustFetchCredentials && is_array($this->provisionedCredentials) && isset($this->provisionedCredentials['expires']) && !empty($this->provisionedCredentials['expires']))
		{
			$mustFetchCredentials = ($this->provisionedCredentials['expires'] + 30) < time();

			if ($mustFetchCredentials)
			{
				Factory::getLog()->debug('The cached S3 credentials are about to or have already expired.');
			}
		}

		if ($mustFetchCredentials)
		{
			Factory::getLog()->debug('Attempting to retrieve S3 credentials from the underlying EC2 instance (if the site is running inside an EC2 instance)');

			try
			{
				$this->provisionedCredentials = $this->getEC2RoleCredentials();
				$akeebaConfig->set($this->volatileKeyPrefix . 'provisionedCredentials', $this->provisionedCredentials);
			}
			catch (\RuntimeException $e)
			{
				Factory::getLog()->debug("No Amazon S3 credentials found and I got the following error retrieving them from the EC2 instance's role: " . $e->getMessage());

				return $config;
			}
		}

		Factory::getLog()->debug('Applying provisioned S3 credentials');

		$config['accessKey'] = $this->provisionedCredentials['access'];
		$config['secretKey'] = $this->provisionedCredentials['secret'];
		$config['token']     = $this->provisionedCredentials['token'];

		return $config;
	}

	/**
	 * Attempt to retrieve the Amazon S3 credentials from the attached Amazon EC2 instance role.
	 *
	 * This will only work if you are running Akeeba Engine in an Amazon EC2 instance with an attached role. The
	 * attached role must give access to the Amazon S3 bucket you have specified in the configuration of this post-
	 * processing engine.
	 *
	 * @return  array (access, secret, expiration)
	 */
	private function getEC2RoleCredentials()
	{
		$hasCurl = function_exists('curl_init') && function_exists('curl_exec') && function_exists('curl_close');

		if (!$hasCurl)
		{
			throw new \RuntimeException('The PHP cURL module is not activated or installed on this server.');
		}

		$roleName = $this->getURL('http://169.254.169.254/latest/meta-data/iam/security-credentials/');

		if (empty($roleName))
		{
			throw new \RuntimeException("Could not find an attached IAM Role on this EC2 instance or we are not running on an EC2 instance.");
		}

		Factory::getLog()->debug("Getting S3 credentials from EC2 attached IAM Role ‘{$roleName}’.");

		$credentialsDocument = $this->getURL('http://169.254.169.254/latest/meta-data/iam/security-credentials/' . $roleName);
		$result              = @json_decode($credentialsDocument, true);

		if (is_null($result) || empty($result))
		{
			throw new \RuntimeException("Cannot retrieve credentials from IAM role $roleName");
		}

		if (!array_key_exists('Code', $result) || ($result['Code'] != 'Success'))
		{
			throw new \RuntimeException("Querying the IAM role did not return a successful result.");
		}

		$keys = array('AccessKeyId', 'AccessKeyId', 'Expiration', 'Token');

		foreach ($keys as $key)
		{
			if (!array_key_exists($key, $result))
			{
				throw new \RuntimeException("Cannot find key ‘{$key}’ in EC2 metadata document. Automatic provisioning of S3 credentials is not possible.");
			}
		}

		try
		{
			$expiresOn = new \DateTime($result['Expiration']);
			$expires   = $expiresOn->getTimestamp();
		}
		catch (\Exception $e)
		{
			Factory::getLog()->debug('Could not determine the expiration time of the automatically provisioned credentials. Assuming an expiration period of 10 minutes (minimum expiration period).');

			$expires = time() + 600;
		}

		return array(
			'access'  => $result['AccessKeyId'],
			'secret'  => $result['SecretAccessKey'],
			'token'   => $result['Token'],
			'expires' => $expires,
		);
	}

	/**
	 * Returns the contents of a URL. We use this internally to fetch the Amazon S3 credentials from the attached
	 * Amazon EC2 instance role.
	 *
	 * @param   string  $url  The URL to fetch
	 *
	 * @return  string  The contents of the URL
	 */
	private function getURL($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_SSLVERSION, 0);
		curl_setopt($ch, CURLOPT_CAINFO, AKEEBA_CACERT_PEM);
		curl_setopt($ch, CURLOPT_HEADERFUNCTION, array($this, 'reponseHeaderCallback'));
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 1);

		$result = curl_exec($ch);

		$errno       = curl_errno($ch);
		$errmsg      = curl_error($ch);
		$error       = '';
		$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if ($result === false)
		{
			$error = sprintf("(cURL Error %u) %s", $errno, $errmsg);
		}
		elseif (($http_status >= 300) && ($http_status <= 399) && isset($this->headers['location']) && !empty($this->headers['location']))
		{
			return $this->getURL($this->headers['location']);
		}
		elseif ($http_status > 399)
		{
			$errno = $http_status;
			$error = sprintf('HTTP %u error', $http_status);
		}

		curl_close($ch);

		if ($result === false)
		{
			throw new \RuntimeException($error, $errno);
		}

		return $result;
	}

	/**
	 * Handles the HTTP headers returned by cURL.
	 *
	 * @param   resource  $ch    cURL resource handle (unused)
	 * @param   string    $data  Each header line, as returned by the server
	 *
	 * @return  int  The length of the $data string
	 */
	protected function reponseHeaderCallback(&$ch, &$data)
	{
		$strlen = strlen($data);

		if (($strlen) <= 2)
		{
			return $strlen;
		}

		$testForHTTP = substr($data, 0, 4);

		if (strtoupper($testForHTTP) == 'HTTP')
		{
			return $strlen;
		}

		list($header, $value) = explode(': ', trim($data), 2);

		$this->headers[strtolower($header)] = $value;

		return $strlen;
	}
}
