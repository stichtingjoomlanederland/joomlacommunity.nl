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

ED::import('admin:/tables/table');

class DiscussPackage extends EasyDiscussTable
{
	public $id = null;
	public $type = null;
	public $group = null;
	public $element = null;
	public $title = null;
	public $description = null;
	public $updated = null;
	public $state = null;
	public $params = null;

	public function __construct(&$db)
	{
		parent::__construct('#__discuss_packages', 'id', $db);
	}

	/**
	 * Downloads the package
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function download()
	{
		// Get the api key
		$config = ED::config();
		$key = $config->get('main_apikey');

		// Download the language file
		$connector = ED::connector();
		$connector->addurl(ED_SERVICE_PACKAGES_DOWNLOAD);
		$connector->setMethod('POST');
		$connector->addQuery('key', $key);
		$connector->addQuery('domain', rtrim(JURI::root(), '/'));
		$connector->addQuery('type', $this->type);
		$connector->addQuery('group', $this->group);
		$connector->addQuery('package', $this->element);
		$connector->execute();
		$result = $connector->getResult();

		$md5 = md5(JFactory::getDate()->toSql());
		$state = json_decode($result);


		if (is_object($state) && $state->code == 400) {
			$this->setError($state->error);
			return false;
		}

		// Create a temporary storage for this file
		$jconfig = ED::jconfig();
		$tmp = $jconfig->get('tmp_path');

		$storage = $tmp . '/' . $md5 . '.zip';

		$state = JFile::write($storage, $result);

		// Set the path for the extracted folder
		$extractedFolder = $tmp . '/' . $md5;

		// Extract the language's archive file
		$state = EDArchive::extract($storage, $extractedFolder);

		// Delete the zip
		JFile::delete($storage);

		return $extractedFolder;
	}

	/**
	 * Retrieve the extension id associated with this package
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getExtensionId()
	{
		$type = $this->type == 'modules' ? 'module' : 'plugin';

		$db = ED::db();
		$query = [
			'select `extension_id` from `#__extensions`',
			'where `type`=' . $db->Quote($type),
			'and `element`=' . $db->Quote($this->element)
		];

		$db->setQuery($query);
		$id = (int) $db->loadResult();

		return $id;
	}

	/**
	 * Installs a package
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function install()
	{
		$downloadedPath = $this->download();

		$app = JFactory::getApplication();

		// Load installer plugins for assistance if required:
		JPluginHelper::importPlugin('installer');
		$dispatcher = JEventDispatcher::getInstance();

		$installType = $app->input->getWord('installtype');

		// Get an installer instance.
		$installer = JInstaller::getInstance();

		$state = $installer->install($downloadedPath);

		// Delete the extracted folder
		JFolder::delete($downloadedPath);

		return $state;
	}

	/**
	 * Uninstalls a package
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function uninstall()
	{
		$installer = JInstaller::getInstance();
		$row = JTable::getInstance('extension');

		$result = false;

		$extensionId = $this->getExtensionId();

		if ($extensionId) {
			$row->load($extensionId);

			if ($row->type) {
				$result = $installer->uninstall($row->type, $extensionId);

				// There was an error in uninstalling the package
				if ($result === false) {
					$this->setError(JText::sprintf('There was an error uninstalling the package %1$s', $this->title));
				}
			}

			ED::clearCache();
		}

		return $result;
	}
}
