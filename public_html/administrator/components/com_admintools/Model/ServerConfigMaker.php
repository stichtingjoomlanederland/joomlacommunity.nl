<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
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

		// This keeps JRegistry from hapily corrupting our data :@
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

		if (!@file_put_contents($htaccessPath, $configFileContents))
		{
			return JFile::write($htaccessPath, $configFileContents);
		}

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