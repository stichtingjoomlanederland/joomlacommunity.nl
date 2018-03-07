<?php
/**
 * @package   Blue Flame Network (bfNetwork)
 * @copyright Copyright (C) 2011, 2012, 2013, 2014, 2015, 2016, 2017 Blue Flame Digital Solutions Ltd. All rights reserved.
 * @license   GNU General Public License version 3 or later
 * @link      https://myJoomla.com/
 * @author    Phil Taylor / Blue Flame Digital Solutions Limited.
 *
 * bfNetwork is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * bfNetwork is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this package.  If not, see http://www.gnu.org/licenses/
 */

/**
 * If we have got here then we have already passed through decrypting
 * the encrypted header and so we are sure we are now secure and no one
 * else cannot run the code below.
 */
final class bfPreferences
{
	/**
	 * @var array
	 */
	public $default_alerting_filewatchlist = array(
		'/includes/defines.php',
		'/includes/framework.php',
		'/configuration.php'
	);

	/**
	 * @var string
	 */
	private $dieStatement = "<?php\nheader('HTTP/1.0 404 Not Found');\ndie();\n?>\n";

	/**
	 * Incoming decrypted vars from the request
	 * @var stdClass
	 */
	private $_dataObj;

	/**
	 * @var string
	 */
	private $_configFile;

	/**
	 * @var mixed|stdClass
	 */
	private $prefs;

	/**
	 * PHP 5 Constructor,
	 * I inject the request to the object
	 *
	 * @param stdClass $dataObj
	 */
	public function __construct($dataObj = NULL)
	{
		$this->_configFile = dirname(__FILE__) . '/tmp/bfLocalConfig.php';

		// Set the request vars
		$this->_dataObj = $dataObj;

		$this->prefs = $this->getPreferences();

	}

	public function getPreferences()
	{
		$this->ensurePrefsFileCreated();

		$prefs = file_get_contents($this->_configFile);
		$prefs = trim(str_replace($this->dieStatement, '', $prefs));

		if (trim($prefs))
		{
			$data = json_decode($prefs);
		}
		else
		{
			$data = new stdClass();
		}

		if (!is_object($data))
		{
			$data = new stdClass();
		}

		if (!property_exists($data, '_BF_LOG'))
		{
			$data->_BF_LOG = FALSE;
		}

		$this->prefs = $data;

		return $this->prefs;
	}

	public function ensurePrefsFileCreated()
	{
		if (!file_exists($this->_configFile))
		{
			$this->prefs          = new stdClass();
			$this->prefs->_BF_LOG = FALSE;
			$this->writeFile();
		}
	}

	public function writeFile()
	{
		file_put_contents($this->_configFile, $this->dieStatement);
		file_put_contents($this->_configFile, json_encode($this->prefs), FILE_APPEND);
	}

	/**
	 * I'm the controller - I run methods based on the request integer
	 */
	public function run($action)
	{
		return $this->$action();
	}

	public function savePreferencesFromService()
	{
		$this->prefs = json_decode($this->_dataObj->preferences);
		$this->writeFile();

	}

	public function savePreference()
	{
		$preference = $this->_dataObj->preference;
		$value      = $this->_dataObj->value;

		$this->prefs->$preference = $value;

		$this->writeFile();
	}
}