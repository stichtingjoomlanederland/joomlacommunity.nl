<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

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
			$contents = JFile::read(JPATH_ROOT. '/administrator/components/com_easydiscuss/easydiscuss.xml');
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
	public function getInfo($update = false)
	{
		// Get the md5 hash from the server.
		$resource = curl_init();

		// If this is an update, we want to tell the server that this is being updated from which version
		$version = $this->getVersion();

		// We need to pass the api keys to the server
		curl_setopt($resource, CURLOPT_POST, true);
		curl_setopt($resource, CURLOPT_POSTFIELDS, 'apikey=' . ED_KEY . '&from=' . $version);
		curl_setopt($resource, CURLOPT_URL, ED_MANIFEST);
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
	 * @since	4.0
	 * @access	public
	 */
	public function engine()
	{
		$file = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php';

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

		$path = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/easydiscuss.xml';
		$contents = JFile::read($path);

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
}

