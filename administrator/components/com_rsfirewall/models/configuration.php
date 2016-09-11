<?php
/**
 * @package        RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link           https://www.rsjoomla.com
 * @license        GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFirewallModelConfiguration extends JModelAdmin
{
	protected $geoip;

	public function __construct()
	{
		$this->geoip = (object) array(
			'path'       => JPATH_COMPONENT . '/assets/geoip/',
			'filename'   => 'GeoIP.dat',
			'filenamev6' => 'GeoIPv6.dat'
		);

		parent::__construct();
	}

	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_rsfirewall.configuration', 'configuration', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	protected function loadFormData()
	{
		$data = (array) $this->getConfig()->getData();

		if (!empty($data['backend_password']))
		{
			$data['backend_password'] = '';
		}

		return $data;
	}

	/* GeoIP functions */
	public function getGeoIPInfo()
	{
		$result = array(
			// Default to true
			'works'		=> true,
			'native' 	=> $this->isGeoIPNative()
		);
		
		// Let's see if dbs are in place
		if ($this->isGeoIPNative()) {
			$result['dbv4'] = $this->hasGeoIPNativeDatabase('GEOIP_COUNTRY_EDITION');
			$result['dbv6'] = $this->hasGeoIPNativeDatabase('GEOIP_COUNTRY_EDITION_V6');
		} else {
			$result['dbv4'] = file_exists($this->getGeoIPPath().$this->getGeoIPFileName('v4'));
			$result['dbv6'] = file_exists($this->getGeoIPPath().$this->getGeoIPFileName('v6'));
			
			$now = JFactory::getDate('now')->toUnix();
			
			// Check file timestamp
			if ($result['dbv4']) {
				$mtime					 = filemtime($this->getGeoIPPath().$this->getGeoIPFileName('v4'));
				$result['dbv4_old'] 	 = $now - $mtime > 86400 * 30;
				$result['dbv4_modified'] = JHtml::_('date.relative', JFactory::getDate($mtime)->toSql());
			}
			if ($result['dbv6']) {
				$mtime					 = filemtime($this->getGeoIPPath().$this->getGeoIPFileName('v6'));
				$result['dbv6_old'] 	 = $now - $mtime > 86400 * 30;
				$result['dbv6_modified'] = JHtml::_('date.relative', JFactory::getDate($mtime)->toSql());
			}
		}
		
		// See if GeoIP is setup correctly and works - IPv4 is the requirement
		if (!$result['dbv4']) {
			$result['works'] = false;
		}
		
		return (object) $result;
	}
	
	public function getIsGeoIPUploaded()
	{
		$path       = $this->getGeoIPPath();
		$filename   = $this->getGeoIPFileName();
		$filenamev6 = $this->getGeoIPFileName('v6');

		$return = array(
			'ipv4' => file_exists($path . $filename),
			'ipv6' => file_exists($path . $filenamev6),
		);

		return $return;
	}

	public function getGeoIPPath()
	{
		return $this->geoip->path;
	}

	public function getGeoIPFileName($version = 'v4')
	{
		switch ($version)
		{
			default:
			case 'v4':
				return $this->geoip->filename;
			break;
			
			case 'v6':
				return $this->geoip->filenamev6;
			break;
		}
	}

	public function isGeoIPNative()
	{
		return function_exists('geoip_database_info');
	}

	public function hasGeoIPNativeDatabase($const = 'GEOIP_COUNTRY_EDITION')
	{
		if (function_exists('geoip_db_avail') && defined($const))
		{
			return geoip_db_avail(constant($const));
		}

		return false;
	}

	public function downloadGeoIPDatabase($version = 'v4')
	{
		$result = array(
			'success' => true,
			'message' => JText::_('COM_RSFIREWALL_GEOIP_SETUP_CORRECTLY')
		);
		
		switch ($version)
		{
			default:
			case 'v4':
				$filename 	= $this->getGeoIPFileName('v4');
				$url 		= 'http://geolite.maxmind.com/download/geoip/database/GeoLiteCountry/GeoIP.dat.gz';
			break;
			
			case 'v6':
				$filename 	= $this->getGeoIPFileName('v6');
				$url 		= 'http://geolite.maxmind.com/download/geoip/database/GeoIPv6.dat.gz';
			break;
		}
		
		try {
			$tmp_path = $this->getGeoIPPath();
			$tmp_name = $filename.'.gz';
			
			// Make sure tmp folder is writable
			if (!is_writable($tmp_path)) {
				throw new Exception(JText::sprintf('COM_RSFIREWALL_GEOIP_DB_FOLDER_NOT_WRITABLE', $tmp_path));
			}
			
			// Connect to server
			$http 		= JHttpFactory::getHttp();
			$response 	= $http->get($url, null, 10);
			
			// Check if request is successful
			if ($response->code != 200) {
				throw new Exception(JText::sprintf('COM_RSFIREWALL_HTTP_ERROR_RESPONSE_CODE', $response->code));
			}
			
			// Write to a temporary file
			if (!file_put_contents($tmp_path.'/'.$tmp_name, $response->body))
			{
				throw new Exception(JText::sprintf('COM_RSFIREWALL_GEOIP_DB_UNABLE_TO_WRITE', $tmp_path.'/'.$tmp_name));
			}
			
			// Hand over to processGeoIPDatabase() function so we can extract the file
			$this->processGeoIPDatabase($tmp_name, $tmp_path.'/'.$tmp_name);
			
			// Remove the tmp file
			if (file_exists($tmp_path.'/'.$tmp_name)) {
				JFile::delete($tmp_path.'/'.$tmp_name);
			}
			
		} catch (Exception $e) {
			$result['success'] = false;
			$result['message'] = $e->getMessage();
		}
		
		return $result;
	}
	
	protected function processGeoIPDatabase($filename, $tmp_file)
	{
		jimport('joomla.filesystem.file');
		
		// Get base path
		$path = $this->getGeoIPPath();
		
		// Check extension is .dat or .gz
		$ext = JFile::getExt($filename);

		// Not a valid extension
		if (!in_array($ext, array('dat', 'gz')))
		{
			throw new Exception(JText::_('COM_RSFIREWALL_GEOIP_DB_INCORRECT_FORMAT'));
		}
		
		// Remove the extension for further checking, we only need to check for .dat files from now on.
		$filename = str_replace('.gz', '', $filename);
		
		// Directory must be writable
		if (!is_writable($path))
		{
			throw new Exception(JText::sprintf('COM_RSFIREWALL_GEOIP_DB_FOLDER_NOT_WRITABLE', $path));
		}
		
		// File already exists but isn't writable (can't overwrite)
		if (file_exists($path . $filename) && !is_writable($path . $filename))
		{
			throw new Exception(JText::sprintf('COM_RSFIREWALL_GEOIP_DB_EXISTS_NOT_WRITABLE', $path));
		}
		
		// Need to unpack this
		if ($ext == 'gz')
		{
			$gzHandle = @gzopen($tmp_file, 'rb');
			if (!$gzHandle)
			{
				throw new Exception(JText::sprintf('COM_RSFIREWALL_GEOIP_DB_UNABLE_TO_READ', $tmp_file));
			}
			
			// Overwrite original file with 0 bytes
			file_put_contents($path . $filename, '');

			while (!gzeof($gzHandle))
			{
				$data = gzread($gzHandle, 1024 * 1024);
				file_put_contents($path . $filename, $data, FILE_APPEND);
			}
			
			gzclose($gzHandle);
		}
	}

	public function uploadGeoIP()
	{
		// input
		$input = JFactory::getApplication()->input;
		$files = $input->files->get('jform', null, 'raw');
		
		if (!empty($files['geoip_upload'])) {
			foreach ($files['geoip_upload'] as $file) {
				if (!in_array($file['name'], array(
					$this->getGeoIPFileName('v4'),
					$this->getGeoIPFileName('v6'),
					$this->getGeoIPFileName('v4').'.gz',
					$this->getGeoIPFileName('v6').'.gz',
				))) {
					$this->setError(JText::_('COM_RSFIREWALL_GEOIP_DB_INCORRECT_FORMAT'));
					return false;
				}
				
				if ($file['error']) {
					if ($file['error'] == UPLOAD_ERR_INI_SIZE)
					{
						$this->setError(JText::_('COM_RSFIREWALL_COULD_NOT_UPLOAD_ERR_INI_SIZE'));
					}
					elseif ($file['error'] == UPLOAD_ERR_FORM_SIZE)
					{
						$this->setError(JText::_('COM_RSFIREWALL_COULD_NOT_UPLOAD_ERR_FORM_SIZE'));
					}
					elseif ($file['error'] == UPLOAD_ERR_PARTIAL)
					{
						$this->setError(JText::_('COM_RSFIREWALL_COULD_NOT_UPLOAD_ERR_PARTIAL'));
					}
					elseif ($file['error'] == UPLOAD_ERR_NO_TMP_DIR)
					{
						$this->setError(JText::_('COM_RSFIREWALL_COULD_NOT_UPLOAD_ERR_NO_TMP_DIR'));
					}
					elseif ($file['error'] == UPLOAD_ERR_CANT_WRITE)
					{
						$this->setError(JText::_('COM_RSFIREWALL_COULD_NOT_UPLOAD_ERR_CANT_WRITE'));
					}
					elseif ($file['error'] == UPLOAD_ERR_EXTENSION)
					{
						$this->setError(JText::_('COM_RSFIREWALL_COULD_NOT_UPLOAD_ERR_EXTENSION'));
					}
					
					return false;
				} else {
					$path 		= $this->getGeoIPPath();
					$filename 	= $file['name'];
					
					try {
						$this->processGeoIPDatabase($filename, $file['tmp_name']);
					} catch (Exception $e) {
						$this->setError($e->getMessage());
						
						return false;
					}
				}
			}
		}
		
		return true;
	}

	public function uploadConfiguration(&$data)
	{
		$files = JFactory::getApplication()->input->files->get('jform', null, 'raw');

		if (isset($files['configuration_upload']))
		{
			// File requested
			$file =& $files['configuration_upload'];

			// Uploaded & no error
			if ($file['tmp_name'] && !$file['error'])
			{
				jimport('joomla.filesystem.file');

				// Check extension is .json
				$ext = JFile::getExt($file['name']);

				if ($ext != 'json')
				{
					$this->setError(JText::_('COM_RSFIREWALL_CONFIGURATION_JSON_INCORRECT_FORMAT'));

					return false;
				}

				if (!is_readable($file['tmp_name']))
				{
					$this->setError(JText::sprintf('COM_RSFIREWALL_CONFIGURATION_JSON_NOT_READABLE', $file['tmp_name']));

					return false;
				}

				$contents = file_get_contents($file['tmp_name']);
				if (!$contents)
				{
					$this->setError(JText::_('COM_RSFIREWALL_CONFIGURATION_JSON_NO_CONTENTS'));

					return false;
				}

				$contents = json_decode($contents, true);
				if ($contents === null)
				{
					$this->setError(JText::_('COM_RSFIREWALL_CONFIGURATION_JSON_NOT_DECODED'));

					return false;
				}

				// Update paths
				if (isset($contents['root']))
				{
					if (!empty($contents['ignore_files_folders']))
					{
						$contents['ignore_files_folders'] = str_replace($contents['root'], JPATH_SITE, $contents['ignore_files_folders']);
					}
					if (!empty($contents['monitor_files']))
					{
						$contents['monitor_files'] = str_replace($contents['root'], JPATH_SITE, $contents['monitor_files']);
					}
				}

				// Workaround so we don't hash the new password twice
				if (isset($contents['backend_password']) && strlen($contents['backend_password']))
				{
					$contents['backend_password_hashed'] = $contents['backend_password'];
				}

				if (empty($data['configuration_update_code']))
				{
					unset($contents['code']);
				}

				// Override configuration data
				$data = $contents;

				return true;
			}
			elseif ($file['error'] != UPLOAD_ERR_NO_FILE)
			{
				if ($file['error'] == UPLOAD_ERR_INI_SIZE)
				{
					$this->setError(JText::_('COM_RSFIREWALL_COULD_NOT_UPLOAD_ERR_INI_SIZE'));
				}
				elseif ($file['error'] == UPLOAD_ERR_FORM_SIZE)
				{
					$this->setError(JText::_('COM_RSFIREWALL_COULD_NOT_UPLOAD_ERR_FORM_SIZE'));
				}
				elseif ($file['error'] == UPLOAD_ERR_PARTIAL)
				{
					$this->setError(JText::_('COM_RSFIREWALL_COULD_NOT_UPLOAD_ERR_PARTIAL'));
				}
				elseif ($file['error'] == UPLOAD_ERR_NO_TMP_DIR)
				{
					$this->setError(JText::_('COM_RSFIREWALL_COULD_NOT_UPLOAD_ERR_NO_TMP_DIR'));
				}
				elseif ($file['error'] == UPLOAD_ERR_CANT_WRITE)
				{
					$this->setError(JText::_('COM_RSFIREWALL_COULD_NOT_UPLOAD_ERR_CANT_WRITE'));
				}
				elseif ($file['error'] == UPLOAD_ERR_EXTENSION)
				{
					$this->setError(JText::_('COM_RSFIREWALL_COULD_NOT_UPLOAD_ERR_EXTENSION'));
				}

				return false;
			}
		}

		return true;
	}

	public function save($data)
	{
		// Upload GeoIP only if it's not built-in
		if (!$this->isGeoIPNative() && !$this->uploadGeoIP())
		{
			return false;
		}

		if (!$this->uploadConfiguration($data))
		{
			return false;
		}

		$db = $this->getDbo();

		// parse rules
		if (isset($data['rules']))
		{
			$rules = new JAccessRules($data['rules']);
			$asset = JTable::getInstance('asset');

			if (!$asset->loadByName($this->option))
			{
				$root = JTable::getInstance('asset');
				$root->loadByName('root.1');
				$asset->name  = $this->option;
				$asset->title = $this->option;
				$asset->setLocation($root->id, 'last-child');
			}
			$asset->rules = (string) $rules;

			if (!$asset->check() || !$asset->store())
			{
				$this->setError($asset->getError());

				return false;
			}
		}

		// get configuration
		$config = $this->getConfig();
		// get configuration keys
		$keys = $config->getKeys();

		// update keys
		foreach ($keys as $key)
		{
			$value = '';
			if (isset($data[$key]))
			{
				$value = $data[$key];
			}

			// Ignore files and folders
			if ($key == 'ignore_files_folders')
			{
				// cleanup the table
				$query = $db->getQuery(true);
				$query->delete('#__rsfirewall_ignored')
					->where($db->qn('type') . '=' . $db->q('ignore_folder') . ' OR ' . $db->qn('type') . '=' . $db->q('ignore_file'));
				$db->setQuery($query);
				$db->execute();

				// save new values
				$values = $this->explode($value);
				foreach ($values as $value)
				{
					$config->append($key, $value);
				}

				// no need to save this in the config
				continue;
			}
			// Protect users
			elseif ($key == 'monitor_users')
			{
				if ($config->get('monitor_users') != $value)
				{
					// cleanup the table
					$query = $db->getQuery(true);
					$query->delete('#__rsfirewall_snapshots')
						->where($db->qn('type') . '=' . $db->q('protect'));
					$db->setQuery($query);
					$db->execute();

					require_once JPATH_COMPONENT . '/helpers/snapshot.php';

					if (is_array($value))
					{
						foreach ($value as $user_id)
						{
							$user_id = (int) $user_id;
							$user    = JFactory::getUser($user_id);

							// Don't save users that cannot be loaded
							if ($user->id == $user_id && $user_id && strlen($user->username))
							{
								$table = JTable::getInstance('Snapshots', 'RSFirewallTable');
								$table->bind(array(
									'user_id'  => $user_id,
									'snapshot' => RSFirewallSnapshot::create($user),
									'type'     => 'protect'
								));
								$table->store();
							}
						}
					}
				}
			}
			// Monitor files
			elseif ($key == 'monitor_files')
			{
				if ($config->get('monitor_files') != $value)
				{
					// cleanup the table
					$query = $db->getQuery(true);
					$query->delete('#__rsfirewall_hashes')
						->where($db->qn('type') . '=' . $db->q('protect'));
					$db->setQuery($query);
					$db->execute();

					// save new values
					$values = $this->explode($value);
					foreach ($values as $value)
					{
						$value = trim($value);
						if (!file_exists($value) || !is_readable($value))
						{
							continue;
						}

						$table = JTable::getInstance('Hashes', 'RSFirewallTable');
						$table->bind(array(
							'id'   => null,
							'file' => $value,
							'hash' => md5_file($value),
							'type' => 'protect',
							'flag' => '',
							'date' => JFactory::getDate()->toSql()
						));
						$table->store();
					}
				}

				continue;
			}
			// Backend password must be encrypted
			elseif ($key == 'backend_password')
			{
				// if we have a value...
				if (!empty($data['backend_password_hashed']) && strlen($data['backend_password_hashed']) == 32)
				{
					$value = $data['backend_password_hashed'];
				}
				elseif (strlen($value))
				{
					$value = md5($value);
				}
				else
				{
					// do not save the blank password
					continue;
				}
			}
			// When we disable the creation of new admin users, we need to remember which are the default ones
			elseif ($key == 'disable_new_admin_users')
			{
				// if the value has changed, store the new admin users
				if ($config->get('disable_new_admin_users') != $value && $value == 1)
				{
					require_once JPATH_ADMINISTRATOR . '/components/com_rsfirewall/helpers/users.php';

					$users       = RSFirewallUsersHelper::getAdminUsers();
					$admin_users = array();
					foreach ($users as $user)
					{
						$admin_users[] = $user->id;
					}

					$config->set('admin_users', $admin_users);
				}
			}
			// don't update this...
			elseif ($key == 'admin_users' || $key == 'grade' || $key == 'log_emails_count' || $key == 'log_emails_send_after' || $key == 'system_check_last_run')
			{
				continue;
			}

			$config->set($key, $value);
		}

		return true;
	}

	public function toJSON()
	{
		$data = $this->getConfig()->getData();

		// Add root so we can move between servers and keep the same Ignored / Protected file paths.
		$data->root = JPATH_SITE;

		return json_encode($data);
	}

	protected function explode($string)
	{
		$string = str_replace(array("\r\n", "\r"), "\n", $string);

		return explode("\n", $string);
	}

	public function getConfig()
	{
		return RSFirewallConfig::getInstance();
	}

	public function getSideBar()
	{
		require_once JPATH_COMPONENT . '/helpers/toolbar.php';

		return RSFirewallToolbarHelper::render();
	}

	public function getRSFieldset()
	{
		require_once JPATH_COMPONENT . '/helpers/adapters/fieldset.php';

		$fieldset = new RSFieldset();

		return $fieldset;
	}

	public function getRSTabs()
	{
		require_once JPATH_COMPONENT . '/helpers/adapters/tabs.php';

		$tabs = new RSTabs('com-rsfirewall-configuration');

		return $tabs;
	}

	public function getIsJ30()
	{
		$jversion = new JVersion();

		return $jversion->isCompatible('3.0');
	}
}