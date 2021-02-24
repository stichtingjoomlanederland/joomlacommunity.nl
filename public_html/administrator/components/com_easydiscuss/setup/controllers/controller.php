<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

// Ensure that this class didn't exists as the CLI class may override this
if (!class_exists('EasyDiscussSetupController')) {

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.archive');
jimport('joomla.database.driver');
jimport('joomla.installer.helper');

class EasyDiscussSetupController
{
	private $result = array();

	public function __construct()
	{
		$this->app = JFactory::getApplication();
		$this->input = $this->app->input;
	}

	protected function data($key, $value)
	{
		$obj = new stdClass();
		$obj->$key = $value;

		$this->result[] = $obj;
	}

	public function setInfo($message, $state = true, $args = array())
	{
		$result = new stdClass();
		$result->state = $state;
		$result->message = JText::_($message);

		if (!empty($args)) {
			foreach ($args as $key => $val) {
				$result->$key = $val;
			}
		}

		$this->result = $result;
	}

	public function output($data = array())
	{
		header('Content-type: text/x-json; UTF-8');

		if (empty($data)) {
			$data = $this->result;
		}

		echo json_encode($data);
		exit;
	}

	/**
	 * Allows caller to set the data
	 *
	 * @since	4.0.12
	 * @access	public
	 */
	public function getResultObj($message, $state, $stateMessage = '')
	{
		$obj = new stdClass();
		$obj->state = $state;
		$obj->stateMessage = $stateMessage;
		$obj->message = JText::_($message);

		return $obj;
	}

	/**
	 * Get's the version of this launcher so we know which to install
	 *
	 * @since	4.0.12
	 * @access	public
	 */
	public function getVersion()
	{
		static $version = null;

		if (is_null($version)) {

			// Get the version from the manifest file
			$contents = file_get_contents(SI_ADMIN_MANIFEST);
			$parser = simplexml_load_string($contents);

			$version = $parser->xpath('version');
			$version = (string) $version[0];
		}

		return $version;
	}

	/**
	 * Retrieve the Joomla Version
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getJoomlaVersion()
	{
		$jVerArr = explode('.', JVERSION);
		$jVersion = $jVerArr[0] . '.' . $jVerArr[1];

		return $jVersion;
	}

	/**
	 * Gets the info about the latest version
	 *
	 * @since	4.0.12
	 * @access	public
	 */
	public function getInfo()
	{
		// Get the md5 hash from the server.
		$resource = curl_init();

		// If this is an update, we want to tell the server that this is being updated from which version
		$version = $this->getVersion();

		// We need to pass the api keys to the server
		curl_setopt($resource, CURLOPT_POST, true);
		curl_setopt($resource, CURLOPT_POSTFIELDS, 'apikey=' . SI_KEY . '&from=' . $version);
		curl_setopt($resource, CURLOPT_URL, SI_MANIFEST);
		curl_setopt($resource, CURLOPT_TIMEOUT, 120);
		curl_setopt($resource, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($resource, CURLOPT_SSL_VERIFYPEER, false);

		$result = curl_exec($resource);
		curl_close($resource);

		if (!$result) {
			return false;
		}


		$obj = json_decode($result);

		return $obj;
	}

	/**
	 * Loads up the EasyDiscuss library if it exists
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function engine()
	{
		$file = SI_ADMIN . '/includes/' . SI_IDENTIFIER_SHORT . '.php';

		if (!JFile::exists($file)) {
			return false;
		}

		// Include foundry framework
		require_once($file);
	}

	/**
	 * method to extract zip file in installation part
	 *
	 * @since	4.2
	 * @access	public
	 */
	public function extractArchive($destination, $extracted)
	{
		if (JVERSION < 4.0) {
			$state = JArchive::extract($destination, $extracted);

			return $state;
		}

		// Joomla 4 method of extracting archive
		$archive = new Joomla\Archive\Archive();
		$state = $archive->extract($destination, $extracted);

		return $state;
	}

	/**
	 * Loads the previous version that was installed
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getInstalledVersion()
	{
		$this->engine();

		$contents = file_get_contents(SI_ADMIN_MANIFEST);

		$parser = simplexml_load_string($contents);

		$version = $parser->xpath('version');
		$version = (string) $version[0];

		return $version;
	}

	/**
	 * Get a configuration item
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getPreviousVersion($versionType)
	{
		// Render EasyDiscuss engine
		$this->engine();

		$table = ED::table('Configs');
		$exists = $table->load(array('name' => $versionType));

		if ($exists) {
			return $table->params;
		}

		// there is no value of the version type. return false.
		return false;
	}

	/**
	 * Determines if we are in development mode
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function isDevelopment()
	{
		$session = JFactory::getSession();
		$developer = $session->get('easydiscuss.developer');

		return $developer;
	}

	/**
	 * Splits a string of multiple queries into an array of individual queries.
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function splitSql($contents)
	{
		if (JVERSION < 4.0) {
			$queries = JInstallerHelper::splitSql($contents);
			return $queries;
		}

		// J4 method of splitting sql strings
		$queries = JDatabaseDriver::splitSql($contents);

		return $queries;
	}

	/**
	 * Saves a configuration item
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function updateConfig($key, $value)
	{
		$this->engine();

		$config = ED::config();
		$config->set($key, $value);

		$jsonString = $config->toString();

		$table = ED::table('Configs');
		$exists = $table->load(array('name' => 'config'));

		if (!$exists) {
			$table->name = 'config';
		}

		$table->params = $jsonString;
		$table->store();
	}

	/**
	 * method to execute query
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function edQuery($db)
	{
		if (JVERSION < 4.0) {
			return $db->query();
		} else {
			return $db->execute();
		}
	}

	/**
	 * Determine if database is set to mysql or not.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isMySQL()
	{
		$jConfig = JFactory::getConfig();
		$dbType = $jConfig->get('dbtype');

		return $dbType == 'mysql' || $dbType == 'mysqli';
	}

	/**
	 * Determine if mysql can support utf8mb4 or not.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function hasUTF8mb4Support()
	{
		static $_cache = null;

		if (is_null($_cache)) {

			$db = JFactory::getDBO();

			if (method_exists($db, 'hasUTF8mb4Support')) {
				$_cache = $db->hasUTF8mb4Support();
				return $_cache;
			}

			// we check the server version 1st
			$server_version = $db->getVersion();
			if (version_compare($server_version, '5.5.3', '<')) {
				 $_cache = false;
				 return $_cache;
			}

			$client_version = '5.0.0';

			if (function_exists('mysqli_get_client_info')) {
				$client_version = mysqli_get_client_info();
			} else if (function_exists('mysql_get_client_info')) {
				$client_version = mysql_get_client_info();
			}

			if (strpos($client_version, 'mysqlnd') !== false) {
				$client_version = preg_replace('/^\D+([\d.]+).*/', '$1', $client_version);
				$_cache = version_compare($client_version, '5.0.9', '>=');
			} else {
				$_cache = version_compare($client_version, '5.5.3', '>=');
			}

		}

		return $_cache;
	}

	/**
	 * Convert utf8mb4 to utf8
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function convertUtf8mb4QueryToUtf8($query)
	{
		if ($this->hasUTF8mb4Support()) {
			return $query;
		}

		// If it's not an ALTER TABLE or CREATE TABLE command there's nothing to convert
		$beginningOfQuery = substr($query, 0, 12);
		$beginningOfQuery = strtoupper($beginningOfQuery);

		if (!in_array($beginningOfQuery, array('ALTER TABLE ', 'CREATE TABLE'))) {
			return $query;
		}

		// Replace utf8mb4 with utf8
		return str_replace('utf8mb4', 'utf8', $query);
	}
}

}