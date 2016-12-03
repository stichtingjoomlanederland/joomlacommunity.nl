<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 *
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 */

namespace Akeeba\Engine\Postproc;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;
use Akeeba\Engine\Postproc\Connector\Dropbox2 as ConnectorDropboxV2;
use Psr\Log\LogLevel;

class Dropbox2 extends Base
{
	/** @var int The retry count of this file (allow up to 2 retries after the first upload failure) */
	private $tryCount = 0;

	/** @var ConnectorDropboxV2 The DropBox API instance */
	private $dropbox2;

	/** @var string The currently configured directory */
	private $directory;

	/** @var bool Are we using chunk uploads? */
	private $chunked = false;

	/** @var int Chunk size (Mb) */
	private $chunk_size = 10;

	public function __construct()
	{
		$this->can_download_to_browser = true;
		$this->can_delete = true;
		$this->can_download_to_file = true;
	}

	/**
	 * Opens the OAuth window
	 *
	 * @param   array   $params  Passed by the backup extension, used for the callback URI
	 *
	 * @return  boolean  False on failure, redirects on success
	 */
	public function oauthOpen($params = array())
	{
		$callback = $params['callbackURI'] . '&method=oauthCallback';

		$url = ConnectorDropboxV2::helperUrl;
		$url .= (strpos($url, '?') !== false) ? '&' : '?';
		$url .= 'callback=' . urlencode($callback);

		Platform::getInstance()->redirect($url);
	}

	/**
	 * Fetches the authentication token from OAuth helper script, after you've run the
	 * first step of the OAuth process.
	 *
	 * @return array
	 */
	public function oauthCallback($params)
	{
		$input = $params['input'];

		$data = (object)array(
			'access_token' => $input['access_token'],
		);

		$serialisedData = json_encode($data);

		return <<< HTML
<script type="application/javascript">
	window.opener.akeeba_dropbox2_oauth_callback($serialisedData);
</script>
HTML;
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
		$directory = $this->directory;
		$basename = empty($upload_as) ? basename($absolute_filename) : $upload_as;
		$this->remote_path = $directory . '/' . $basename;

		// Do not use multipart uploads when in an immediate post-processing step,
		// i.e. we are uploading a part right after its creation
		if ($this->chunked)
		{
			// Retrieve engine configuration data
			$config = Factory::getConfiguration();

			$immediateEnabled = $config->get('engine.postproc.common.after_part', 0);

			if ($immediateEnabled)
			{
				$this->chunked = false;
			}
		}

		// Have I already made sure the remote directory exists?
		$haveCheckedRemoteDirectory = $config->get('volatile.engine.postproc.dropbox2.check_directory', 0);

		if (!$haveCheckedRemoteDirectory)
		{
			try
			{
				$this->dropbox2->makeDirectory($directory);
			}
			catch (\Exception $e)
			{
				$this->setWarning("Could not create directory $directory. " . $e->getCode() . ': ' . $e->getMessage());

				return false;
			}

			$config->set('volatile.engine.postproc.dropbox2.check_directory', 1);
		}

		// Get the remote file's pathname
		$remotePath = trim($directory, '/') . '/' . basename($absolute_filename);

		// Are we already processing a multipart upload?
		if ($this->chunked)
		{
			Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - Using chunked upload, part size {$this->chunk_size}");

			$offset = $config->get('volatile.engine.postproc.dropbox2.offset', 0);
			$upload_id = $config->get('volatile.engine.postproc.dropbox2.upload_id', null);

			if (empty($upload_id))
			{
				Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - Creating new upload session");

				try
				{
					$upload_id = $this->dropbox2->createUploadSession();
				}
				catch (\Exception $e)
				{
					$this->setWarning("The upload session cannot be created. Debug info: #" . $e->getCode() . ' – ' . $e->getMessage());

					return false;
				}

				Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - New upload session $upload_id");
				$config->set('volatile.engine.postproc.dropbox2.upload_id', $upload_id);
			}

			try
			{
				if (empty($offset))
				{
					$offset = 0;
				}

				Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - Uploading chunked part");

				$this->dropbox2->uploadPart($upload_id, $absolute_filename, $offset, $this->chunk_size);

				$result = true;
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

				Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - Retrying chunk upload");

				return -1;
			}

			// Are we done uploading?
			clearstatcache();
			$totalSize = filesize($absolute_filename);
			$nextOffset = $offset + $this->chunk_size - 1;

			if (isset($result['name']) || ($nextOffset >= $totalSize))
			{
				Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - Finializing chunked upload, saving uploaded file as $remotePath");

				try
				{
					$this->dropbox2->finishUploadSession($upload_id, $remotePath, $totalSize);
				}
				catch (\Exception $e)
				{
					Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - Chunk upload finalization failed. Debug info: #" . $e->getCode() . ' – ' . $e->getMessage());

					return false;
				}

				Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - Chunked upload is now complete");

				$config->set('volatile.engine.postproc.dropbox2.offset', null);
				$config->set('volatile.engine.postproc.dropbox2.upload_id', null);

				return true;
			}

			// Otherwise, continue uploading
			$config->set('volatile.engine.postproc.dropbox2.offset', $offset + $this->chunk_size);

			return -1;
		}

		// Single part upload
		try
		{
			Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - Performing simplified upload of $absolute_filename to $remotePath");
			$result = $this->dropbox2->upload($remotePath, $absolute_filename);
		}
		catch (\Exception $e)
		{
			Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - Simplified upload failed, " . $e->getCode() . ": " . $e->getMessage());

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

			Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - Retrying upload");

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
			$this->dropbox2->download($remotePath, $localFile);
		}
		catch (\Exception $e)
		{
			$this->setWarning($e->getMessage());

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
		// Get settings
		$settings = $this->initialiseConnector();

		if ($settings === false)
		{
			return false;
		}

		return $this->dropbox2->getAuthenticatedUrl($remotePath);
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
			$this->dropbox2->delete($path);
		}
		catch (\Exception $e)
		{
			$this->setWarning($e->getMessage());

			return false;
		}

		return true;
	}

	/**
	 * Initialises the OneDrive connector object
	 *
	 * @return  bool  True on success, false if we cannot proceed
	 */
	protected function initialiseConnector()
	{
		// Retrieve engine configuration data
		$config = Factory::getConfiguration();

		$access_token = trim($config->get('engine.postproc.dropbox2.access_token', ''));

		$this->chunked = $config->get('engine.postproc.dropbox2.chunk_upload', true);
		$this->chunk_size = $config->get('engine.postproc.dropbox2.chunk_upload_size', 10) * 1024 * 1024;
		$this->directory = $config->get('volatile.postproc.directory', null);

		if (empty($this->directory))
		{
			$this->directory = $config->get('engine.postproc.dropbox2.directory', '');
		}

		// Sanity checks
		if (empty($access_token))
		{
			$this->setError('You have not linked Akeeba Backup with your Dropbox account');

			return false;
		}

        if(!function_exists('curl_init'))
        {
            $this->setWarning('cURL is not enabled, please enable it in order to post-process your archives');

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

		$this->dropbox2 = new ConnectorDropboxV2($access_token);

		return true;
	}
}