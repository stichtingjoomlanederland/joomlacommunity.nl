<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Model;

// Protect from unauthorized access
use Akeeba\AdminTools\Admin\Helper\ServerTechnology;
use Akeeba\AdminTools\Admin\Helper\Storage;
use FOF30\Container\Container;
use FOF30\Model\Model;
use JFile;
use JLoader;
use JUri;

defined('_JEXEC') or die();

/**
 * Abstract class for .htaccess Maker and similar server configuration file makers
 */
abstract class ServerConfigMaker extends Model
{
	/**
	 * The default configuration of this feature.
	 *
	 * Note that you define an array. It becomes an object in the constructor. We have to do that since PHP doesn't
	 * allow the intitialisation of anonymous objects (like e.g. Javascript) but lets us typecast an array to an object
	 * – just not in the property declaration!
	 *
	 * @var  object
	 */
	public $defaultConfig = [];

	/**
	 * The current configuration of this feature
	 *
	 * @var  object
	 */
	protected $config = null;

	/**
	 * The Admin Tools configuration key under which we'll save $config as a JSON-encoded string
	 *
	 * @var  string
	 */
	protected $configKey = '';

	/**
	 * The methods which are allowed to call the saveConfiguration method. Each line is in the format:
	 * Full\Class\Name::methodName
	 *
	 * @var  array
	 */
	protected $allowedCallersForSave = [];

	/**
	 * The methods which are allowed to call the writeConfigFile method. Each line is in the format:
	 * Full\Class\Name::methodName
	 *
	 * @var  array
	 */
	protected $allowedCallersForWrite = [];

	/**
	 * The methods which are allowed to call the makeConfigFile method. Each line is in the format:
	 * Full\Class\Name::methodName
	 *
	 * @var  array
	 */
	protected $allowedCallersForMake = [];

	/**
	 * The base name of the configuration file being saved by this feature, e.g. ".htaccess". The file is always saved
	 * in the site's root. Any old files under that name are renamed with a .admintools suffix.
	 *
	 * @var string
	 */
	protected $configFileName = '';

	/**
	 * Public class constructor
	 *
	 * It modified the default configuration with the domain name and path of the current site, as detected by Joomla!.
	 *
	 * @param   Container  $container  The configuration variables to this model
	 * @param   array      $config     Configuration values for this model
	 */
	public function __construct(Container $container, array $config)
	{
		parent::__construct($container, $config);

		$myURI = JUri::getInstance();
		$path  = $myURI->getPath();

		$path_parts = explode('/', $path);
		$path_parts = array_slice($path_parts, 0, count($path_parts) - 2);
		$path       = implode('/', $path_parts);

		$myURI->setPath($path);

		// Unset any query parameters
		$myURI->setQuery('');

		$host = $myURI->toString();
		$host = substr($host, strpos($host, '://') + 3);

		$path = trim($path, '/');

		$this->defaultConfig['rewritebase'] = '/';
		$this->defaultConfig['httphost']    = $host;
		$this->defaultConfig['httpshost']   = $host;

		if (!empty($path))
		{
			$this->defaultConfig['rewritebase'] = $path;
		}

		$this->defaultConfig = (object)$this->defaultConfig;
	}

	public function getConfigFileName()
	{
		return $this->configFileName;
	}

	/**
	 * Loads the feature's configuration from the database
	 *
	 * @return  object
	 */
	public function loadConfiguration()
	{
		if (is_null($this->config))
		{
			$params = Storage::getInstance();
			$savedConfig = $params->getValue($this->configKey, '');

			if (!empty($savedConfig))
			{
				if (function_exists('base64_encode') && function_exists('base64_encode'))
				{
					$savedConfig = base64_decode($savedConfig);
				}

				$savedConfig = json_decode($savedConfig, true);
			}
			else
			{
				$savedConfig = array();
			}

			$config = $this->defaultConfig;

			if (!empty($savedConfig))
			{
				foreach ($savedConfig as $key => $value)
				{
					$config->$key = $value;
				}
			}

			$this->config = $config;
		}

		return $this->config;
	}

	/**
	 * Save the configuration to the database
	 *
	 * @param   object|array  $data           The data to save
	 * @param   bool          $isConfigInput  True = $data is object. False (default) = $data is an array.
	 */
	public function saveConfiguration($data, $isConfigInput = false)
	{
		// Make sure we are called by an expected caller
		ServerTechnology::checkCaller($this->allowedCallersForSave);

		if ($isConfigInput)
		{
			$config = $data;
		}
		else
		{
			$config = $this->defaultConfig;

			if (!empty($data))
			{
				$ovars = get_object_vars($config);
				$okeys = array_keys($ovars);

				foreach ($data as $key => $value)
				{
					if (in_array($key, $okeys))
					{
						// Clean up array types coming from textareas
						if (in_array($key, array(
							'hoggeragents', 'bepexdirs',
							'bepextypes', 'fepexdirs', 'fepextypes',
							'exceptionfiles', 'exceptionfolders', 'exceptiondirs', 'fullaccessdirs',
							'httpsurls'
						))
						)
						{
							if (empty($value))
							{
								$value = array();
							}
							else
							{
								$value = trim($value);
								$value = explode("\n", $value);

								if (!empty($value))
								{
									$ret = array();

									foreach ($value as $v)
									{
										$vv = trim($v);

										if (!empty($vv))
										{
											$ret[] = $vv;
										}
									}

									if (!empty($ret))
									{
										$value = $ret;
									}
									else
									{
										$value = array();
									}
								}
							}
						}

						$config->$key = $value;
					}
				}
			}
		}

		// Make sure nobody tried to add the php extension to the list of allowed extension
		$disallowedExtensions = array('php', 'phP', 'pHp', 'pHP', 'Php', 'PhP', 'PHp', 'PHP');

		foreach ($disallowedExtensions as $ext)
		{
			$pos = array_search($ext, $config->bepextypes);

			if ($pos !== false)
			{
				unset($config->bepextypes[ $pos ]);
			}

			$pos = array_search($ext, $config->fepextypes);

			if ($pos !== false)
			{
				unset($config->fepextypes[ $pos ]);
			}
		}

		$this->config = $config;
		$config       = json_encode($config);

		// This keeps JRegistry from happily corrupting our data :@
		if (function_exists('base64_encode') && function_exists('base64_encode'))
		{
			$config = base64_encode($config);
		}

		$params = Storage::getInstance();

		$params->setValue($this->configKey, $config);
		$params->setValue('quickstart', 1);

		$params->save();
	}

	/**
	 * Create and return the configuration file's contents. This is the heart of these features.
	 *
	 * @return  string
	 */
	abstract public function makeConfigFile();

	/**
	 * Make the configuration file and write it to the disk
	 *
	 * @return  bool
	 */
	public function writeConfigFile()
	{
		// Make sure we are called by an expected caller
		ServerTechnology::checkCaller($this->allowedCallersForWrite);

		JLoader::import('joomla.filesystem.file');

		$htaccessPath = JPATH_ROOT . '/' . $this->configFileName;
		$backupPath   = JPATH_ROOT . '/' . $this->configFileName . '.admintools';

		if (@file_exists($htaccessPath))
		{
			if (!@copy($htaccessPath, $backupPath))
			{
				JFile::copy(basename($htaccessPath), basename($backupPath), JPATH_ROOT);
			}
		}

		$configFileContents = $this->makeConfigFile();

		/**
		 * Convert CRLF to LF before saving the file. This would work around an issue with Windows browsers using CRLF
		 * line endings in text areas which would then be transferred verbatim to the output file. Most servers don't
		 * mind, but NginX will break hard when it sees the CR in the configuration file.
		 */
		$configFileContents = str_replace("\r\n", "\n", $configFileContents);

		// Save the hash of the contents as well as the technology used, so later we can inform the user about any manual edit
		// Please note: we have to save the hash only when we actually write to disk, not when we save the config
		// We're going to do that even if the user decided to ignore the warning, because if he changes idea later we can warn him
		$storage = Storage::getInstance();

		$info = [
			'technology' => $this->getName(),
			'contents'   => md5($configFileContents)
		];

		$storage->setValue('configInfo', $info, true);

		if (!@file_put_contents($htaccessPath, $configFileContents))
		{
			return JFile::write($htaccessPath, $configFileContents);
		}

		return true;
	}

	/**
	 * Checks if current redirection rules do match the URL saved inside the live_site variable. For example:
	 * - live_site: www.example.com - Redirect www to non-www   WRONG!
	 * - live_site: www.example.com - Redirect non-www to www   CORRECT!
	 *
	 * @return bool Are the live_site variable and current redirection rules compatible?
	 */
	public function enableRedirects()
	{
		$live_site = $this->container->platform->getConfig()->get('live_site', '');

		// No value set (90% of cases), we're good to go
		if (!$live_site)
		{
			return true;
		}

		// The user put the protocol in the live site? That's an hard no
		if (stripos($live_site, 'http') !== false)
		{
			return false;
		}

		$config = $this->loadConfiguration();

		// No redirection set? We're good to go
		if (!$config->wwwredir)
		{
			return true;
		}

		// Got www site and a redirect from www to non-www, that's wrong
		if ((stripos($live_site, 'www.') === 0) && ($config->wwwredir === 2) )
		{
			return false;
		}

		// Got non-www site and a redirect from non-www to www, that's wrong
		if ((stripos($live_site, 'www.') === false) && ($config->wwwredir === 1) )
		{
			return false;
		}

		// Otherwise we're good to go
		return true;
	}

	/**
	 * Escapes a string so that it's a neutral string inside a regular expression.
	 *
	 * @param   string  $str  The string to escape
	 *
	 * @return  string  The escaped string
	 */
	protected function escape_string_for_regex($str)
	{
		//All regex special chars (according to arkani at iol dot pt below):
		// \ ^ . $ | ( ) [ ]
		// * + ? { } , -

		$patterns = array(
			'/\//', '/\^/', '/\./', '/\$/', '/\|/',
			'/\(/', '/\)/', '/\[/', '/\]/', '/\*/', '/\+/',
			'/\?/', '/\{/', '/\}/', '/\,/', '/\-/'
		);

		$replace = array(
			'\/', '\^', '\.', '\$', '\|', '\(', '\)',
			'\[', '\]', '\*', '\+', '\?', '\{', '\}', '\,', '\-'
		);

		return preg_replace($patterns, $replace, $str);
	}
}
