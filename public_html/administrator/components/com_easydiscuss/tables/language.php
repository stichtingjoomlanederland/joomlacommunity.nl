<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/table.php');

class DiscussLanguage extends EasyDiscussTable
{
	public $id = null;
	public $title = null;
	public $locale = null;
	public $updated = null;
	public $state = null;
	public $translator = null;
	public $progress = null;
	public $params = null;

	public function __construct(&$db)
	{
		parent::__construct('#__discuss_languages', 'id', $db);
	}

	/**
	 * Installs a language file
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function install()
	{
		$params = new JRegistry($this->params);

		// Get the api key
		$config = ED::config();
		$key = $config->get('main_apikey');

		if (!$key) {
			$this->setError(JText::_('COM_EASYDISCUSS_INVALID_API_KEY_PROVIDED'));
			return false;
		}

		// Get the download url
		$url = $params->get('download');

		if (!$url) {
			$this->setError(JText::_('COM_EASYDISCUSS_INVALID_DOWNLOAD_URL'));
			return false;
		}

		// Download the language file
		$connector = ED::connector();
		$connector->addUrl($url);
		$connector->addQuery('key', $key);
		$connector->setMethod('POST');
		$connector->execute();
		$result = $connector->getResult($url);

		// Generate a random hash
		$hash = md5($this->locale . JFactory::getDate()->toSql());

		// Build storage path
		$storage = JPATH_ROOT . '/tmp/' . $hash . '.zip';

		// Save the file
		$state = JFile::write($storage, $result);

		$folder = JPATH_ROOT . '/tmp/' . $hash;

		jimport( 'joomla.filesystem.archive' );

		// Extract the language's archive file
		$state = JArchive::extract($storage, $folder);

		// Throw some errors when we are unable to extract the zip file.
		if (!$state) {
			$this->setError(JText::_('COM_EASYDISCUSS_UNABLE_TO_EXTRACT_ARCHIVE'));
			return false;
		}

		// Read the meta data
		$raw = JFile::read($folder . '/meta.json');
		$meta = json_decode($raw);

		foreach ($meta->resources as $resource) {

			// Get the correct path based on the meta's path
			$dest = $this->getPath($resource->path) . '/language/' . $this->locale;

			// If language folder don't exist, create it first.
			if (!JFolder::exists($dest)) {
				JFolder::create($dest);
			}

			// Build the source and target files
			$destFile = $dest . '/' . $this->locale . '.' . $resource->title;
			$sourceFile = $folder . '/' . $resource->path . '/' . $this->locale . '.' . $resource->title;

			// Ensure that the source file exists
			if (!JFile::exists($sourceFile)) {
				continue;
			}

			// If the destination file already exists, delete it first
			if (JFile::exists($destFile)) {
				JFile::delete($destFile);
			}

			// Try to copy the file
			$state = JFile::copy($sourceFile, $destFile);

			if (!$state) {
				$this->setError(JText::_('COM_EASYDISCUSS_LANGUAGES_ERROR_COPYING_FILES'));
				return false;
			}
		}

		// After everything is copied, ensure that the extracted folder is deleted to avoid dirty filesystem
		JFile::delete($storage);
		JFolder::delete($folder);

		// Once the language files are copied accordingly, update the state
		$this->state = ED_LANGUAGES_INSTALLED;

		return $this->store();
	}

	public function getPath($metaPath)
	{
		switch($metaPath)
		{
			case 'admin':
			case 'plugins':
			case 'plugin':
			case 'menu':
				$path = JPATH_ROOT . '/administrator';
			break;

			case 'site':
			case 'module':
			default:
				$path	= JPATH_ROOT;
			break;
		}

		return $path;
	}

	/**
	 * Allows caller to uninstall a language
	 *
	 * @since	4.0.21
	 * @access	public
	 */
	public function uninstall()
	{
		$locale = $this->locale;

		$paths = array(JPATH_ADMINISTRATOR . '/language/' . $locale, JPATH_ROOT . '/language/' . $locale);

		// Get the list of files on each folders
		foreach ($paths as $path) {

			$filter = 'easydiscuss';
			$files = JFolder::files($path, $filter, false, true);

			if (!$files) {
				continue;
			}

			foreach ($files as $file) {
				JFile::delete($file);
			}
		}

		$this->state = ED_LANGUAGES_NOT_INSTALLED;
		return $this->store();
	}

	/**
	 * Determine if this language installed
	 *
	 * @since	4.0.21
	 * @access	public
	 */
	public function isInstalled()
	{
		return $this->state == ED_LANGUAGES_INSTALLED;
	}
}
