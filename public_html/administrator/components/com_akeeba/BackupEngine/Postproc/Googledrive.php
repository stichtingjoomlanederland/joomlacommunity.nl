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
use Akeeba\Engine\Postproc\Connector\GoogleDrive as ConnectorGoogleDrive;
use Psr\Log\LogLevel;

class Googledrive extends Base
{
	/** @var int The retry count of this file (allow up to 2 retries after the first upload failure) */
	private $tryCount = 0;

	/** @var ConnectorGoogleDrive The Google Drive API instance */
	private $googleDrive;

	/** @var string The currently configured directory */
	private $directory;

	/** @var int Chunk size (MB) */
	private $chunk_size = 10;

	public function __construct()
	{
		$this->can_download_to_browser = false;
		$this->can_delete = true;
		$this->can_download_to_file = true;
	}

	/**
	 * Opens the OAuth window
	 *
	 * @param   array   $params  Passed by the backup extension, used for the callback URI
	 *
	 * @return  void  Redirects on success
	 */
	public function oauthOpen($params = array())
	{
		$callback = $params['callbackURI'] . '&method=oauthCallback';

		$url = ConnectorGoogleDrive::helperUrl;
		$url .= (strpos($url, '?') !== false) ? '&' : '?';
		$url .= 'callback=' . urlencode($callback);
		$url .= '&dlid=' . Platform::getInstance()->get_platform_configuration_option('update_dlid', '');

		Platform::getInstance()->redirect($url);
	}

	/**
	 * Fetches the authentication token from the OAuth helper script, after you've run the
	 * first step of the OAuth process.
	 *
	 * @return array
	 */
	public function oauthCallback($params)
	{
		$input = $params['input'];

		$data = (object)array(
			'access_token' => $input['access_token'],
			'refresh_token' => $input['refresh_token'],
		);

		$serialisedData = json_encode($data);

		return <<< HTML
<script type="application/javascript">
	window.opener.akeeba_googledrive_oauth_callback($serialisedData);
</script>
HTML;
	}

	/**
	 * Used by the interface to display a list of drives to choose from.
	 *
	 * @param   array  $params
	 *
	 * @return  array
	 */
	public function getDrives($params = array())
	{
		// Make sure we can get a connector object
		$validSettings = $this->initialiseConnector($params);

		if ($validSettings === false)
		{
			return array();
		}

		$baseItem = 'Google Drive (personal)';

		if (class_exists('JText'))
		{
			$baseItem = \JText::_('COM_AKEEBA_CONFIG_GOOGLEDRIVE_TEAMDRIVE_OPT_PERSONAL');
		}

		if (class_exists('\Awf\Text\Text'))
		{
			$baseItem = \Awf\Text\Text::_('COM_AKEEBA_CONFIG_GOOGLEDRIVE_TEAMDRIVE_OPT_PERSONAL');
		}

		$items = array_merge(array(
			'' => $baseItem
		), $this->googleDrive->getTeamDrives());

		$ret = array();

		foreach ($items as $k => $v)
		{
			$ret[] = array($k, $v);
		}

		return $ret;
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
	 *
	 * @throws \Exception
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
		$this->remote_path = trim($directory, '/') . '/' . $basename;

		// Have I already made sure the remote directory exists?
		$folderId    = $config->get('volatile.engine.postproc.googledrive.check_directory', 0);
		$teamDriveID = $config->get('engine.postproc.googledrive.team_drive', '');

		if (!$folderId)
		{
			try
			{
				Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - Preparing to upload to Google Drive, file path = {$this->remote_path}.");
				list($fileName, $folderId) = $this->googleDrive->preprocessUploadPath($this->remote_path, $teamDriveID);
				Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - Google Drive folder ID = " . $folderId);
			}
			catch (\Exception $e)
			{
				$this->setWarning("Could not create Google Drive directory $directory. " . $e->getCode() . ': ' . $e->getMessage());

				return false;
			}

			$config->set('volatile.engine.postproc.googledrive.check_directory', $folderId);
		}

		// Get the remote file's pathname
		$remotePath = $this->remote_path;

		// Check if the size of the file is compatible with chunked uploading
		clearstatcache();
		$totalSize   = filesize($absolute_filename);

		/**
		 * Google Drive is broken.
		 *
		 * When you use Simple Upload it will upload your files in two(!!!) places at the same time: the folder you tell
		 * it and the Drive's root. Why? Probably because Google gives fat bonuses to morons who publish broken stuff
		 * instead of the poor souls who fix all the bugs.
		 *
		 * The kind daft way around this is using chunked upload even for tiny files, less than the part size. Many more
		 * requests to the API server yet it works. Beats me, man...
		 */
		// Are we already processing a multipart upload?
		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - Using chunked upload, part size {$this->chunk_size}");

		$offset    = $config->get('volatile.engine.postproc.googledrive.offset', 0);
		$upload_id = $config->get('volatile.engine.postproc.googledrive.upload_id', null);

		if (empty($upload_id))
		{
			// Convert path to folder ID and file ID, creating missing folders and deleting existing files in the process
			Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - Trying to create possibly missing directories and remove existing file by the same name ($remotePath)");
			list($fileName, $folderId) = $this->googleDrive->preprocessUploadPath($remotePath, $teamDriveID);

			Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - Creating new upload session");

			try
			{
				$upload_id = $this->googleDrive->createUploadSession($folderId, $absolute_filename, $fileName);
			}
			catch (\Exception $e)
			{
				$this->setWarning("The upload session for remote file $remotePath cannot be created. Debug info: #" . $e->getCode() . ' – ' . $e->getMessage());

				return false;
			}

			Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - New upload session $upload_id");
			$config->set('volatile.engine.postproc.googledrive.upload_id', $upload_id);
		}

		try
		{
			if (empty($offset))
			{
				$offset = 0;
			}

			Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - Uploading chunked part (offset:$offset // chunk size: {$this->chunk_size})");

			$result = $this->googleDrive->uploadPart($upload_id, $absolute_filename, $offset, $this->chunk_size);

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

			Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - Error detected, trying to force-refresh the tokens");

			$this->forceRefreshTokens();

			Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - Retrying chunk upload");

			return -1;
		}

		// Are we done uploading?
		$nextOffset = $offset + $this->chunk_size - 1;

		if (isset($result['name']) || ($nextOffset > $totalSize))
		{
			Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - Chunked upload is now complete");

			$config->set('volatile.engine.postproc.googledrive.offset', null);
			$config->set('volatile.engine.postproc.googledrive.upload_id', null);

			$this->tryCount = 0;

			return true;
		}

		// Otherwise, continue uploading
		$config->set('volatile.engine.postproc.googledrive.offset', $offset + $this->chunk_size);

		return -1;
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
			$engineConfig = Factory::getConfiguration();
			$teamDriveID  = $engineConfig->get('engine.postproc.googledrive.team_drive', '');
			$fileId       = $this->googleDrive->getIdForFile($remotePath, false, $teamDriveID);
			$this->googleDrive->download($fileId, $localFile);
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
			$engineConfig = Factory::getConfiguration();
			$teamDriveID  = $engineConfig->get('engine.postproc.googledrive.team_drive', '');
			$fileId       = $this->googleDrive->getIdForFile($path, false, $teamDriveID);
			$this->googleDrive->delete($fileId, true);
		}
		catch (\Exception $e)
		{
			$this->setWarning($e->getMessage());

			return false;
		}

		return true;
	}

	/**
	 * Initialises the Google Drive connector object
	 *
	 * @return  bool  True on success, false if we cannot proceed
	 */
	protected function initialiseConnector($overrides = array())
	{
		// Retrieve engine configuration data
		$config = Factory::getConfiguration();
		$config->mergeArray($overrides);

		$access_token = trim($config->get('engine.postproc.googledrive.access_token', ''));
		$refresh_token = trim($config->get('engine.postproc.googledrive.refresh_token', ''));

		$this->chunk_size = $config->get('engine.postproc.googledrive.chunk_upload_size', 10) * 1024 * 1024;
		$this->directory = $config->get('volatile.postproc.directory', null);

		if (empty($this->directory))
		{
			$this->directory = $config->get('engine.postproc.googledrive.directory', '');
		}

		// Sanity checks
		if (empty($refresh_token))
		{
			$this->setError('You have not linked Akeeba Backup with your Google Drive account');

			return false;
		}

        if (!function_exists('curl_init'))
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

		// Get Download ID
		$dlid = Platform::getInstance()->get_platform_configuration_option('update_dlid', '');

		if (empty($dlid))
		{
			$this->setWarning('You must enter your Download ID in the application configuration before using the “Upload to Google Drive” feature.');

			return false;
		}

		$this->googleDrive = new ConnectorGoogleDrive($access_token, $refresh_token, $dlid);

		// Validate the tokens
		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - Validating the Google Drive tokens");
		$pingResult = $this->googleDrive->ping();

		// Save new configuration if there was a refresh
		if ($pingResult['needs_refresh'])
		{
			Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - Google Drive tokens were refreshed");
			$config->set('engine.postproc.googledrive.access_token', $pingResult['access_token'], false);

			$profile_id = Platform::getInstance()->get_active_profile();
			Platform::getInstance()->save_configuration($profile_id);
		}

		return true;
	}

	/**
	 * Forcibly refresh the Google Drive tokens
	 *
	 * @return  void
	 */
	protected function forceRefreshTokens()
	{
		// Retrieve engine configuration data
		$config = Factory::getConfiguration();

		$pingResult = $this->googleDrive->ping(true);

		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . '::' . __METHOD__ . " - Google Drive tokens were forcibly refreshed");
		$config->set('engine.postproc.googledrive.access_token', $pingResult['access_token'], false);

		$profile_id = Platform::getInstance()->get_active_profile();
		Platform::getInstance()->save_configuration($profile_id);
	}
}
