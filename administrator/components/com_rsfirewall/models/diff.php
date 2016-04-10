<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR . '/components/com_rsfirewall/helpers/diff.php';

class RSFirewallModelDiff extends JModelLegacy
{
	const RAW_URL = 'https://raw.githubusercontent.com/joomla/joomla-cms/%s/%s';

	public function getFile()
	{
		return JFactory::getApplication()->input->getPath('file');
	}
	
	public function getHashId()
	{
		return JFactory::getApplication()->input->get('hid');
	}

	protected function getJoomlaVersion()
	{
		$jversion = new JVersion();

		return $jversion->getShortVersion();
	}

	public function getLocalFilename()
	{
		return JPATH_SITE . '/' . $this->getFile();
	}

	public function getLocalFile()
	{
		$path = $this->getLocalFilename();

		if (!file_exists($path))
		{
			throw new Exception(JText::sprintf('COM_RSFIREWALL_FILE_NOT_FOUND', $path));
		}

		if (!is_readable($path))
		{
			throw new Exception(JText::sprintf('COM_RSFIREWALL_FILE_NOT_READABLE', $path));
		}

		if (!is_file($path))
		{
			throw new Exception(JText::sprintf('COM_RSFIREWALL_NOT_A_FILE', $path));
		}

		return file_get_contents($path);
	}

	public function getRemoteFilename()
	{
		return sprintf(self::RAW_URL, $this->getJoomlaVersion(), $this->getFile());
	}

	public function getRemoteFile()
	{
		// Joomla! 2.5.13 minimum
		if (version_compare($this->getJoomlaVersion(), '2.5.13', '<'))
		{
			throw new Exception(JText::_('COM_RSFIREWALL_DIFF_MIN_2513'));
		}

		$url = $this->getRemoteFilename();

		// Try to connect
		$response = $this->connect($url);

		// Error in response code
		if ($response->code != 200)
		{
			throw new Exception(JText::sprintf('COM_RSFIREWALL_HTTP_ERROR_RESPONSE_CODE', $response->code));
		}

		return $response->body;
	}

	public function downloadOriginalFile($localFile)
	{
		$message = array(
			'status' => false,
			'files'  =>  array(
				'localFile' => $localFile
			)
		);

		$message['files']['remoteFile'] = sprintf(self::RAW_URL, $this->getJoomlaVersion(), $message['files']['localFile']);

		try {
			$response = $this->connect($message['files']['remoteFile']);

			// Error in response code
			if ($response->code != 200)
			{
				throw new Exception(JText::sprintf('COM_RSFIREWALL_HTTP_ERROR_RESPONSE_CODE', $response->code));
			}

			jimport('joomla.filesystem.file');

			// Rewrite the localfile with the remote file
			if (! @ JFile::write(JPATH_SITE . '/' . $message['files']['localFile'], $response->body)){
				throw new Exception(JText::_('COM_RSFIREWALL_FILESYSTEM_ERROR_COPY_FAILED'));
			}

			$message['status']  = true;
			$message['message'] = JText::_('COM_RSFIREWALL_FILESYSTEM_FILES_COPIED');

		} catch (Exception $e) {
			$message['message'] = $e->getMessage();
		}

		echo json_encode($message);
		jexit();
	}

	protected function connect($url, $caching = true)
	{
		$cache = JFactory::getCache('com_rsfirewall');
		$cache->setCaching($caching);
		$cache->setLifetime(300);

		return $cache->call(array('RSFirewallModelDiff', 'connectCache'), $url);
	}

	public static function connectCache($url)
	{
		$http     = JHttpFactory::getHttp();
		$response = $http->get($url, null, 30);

		return $response;
	}
}