<?php
/**
 * @package         Regular Labs Extension Manager
 * @version         7.4.9
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2021 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper as JComponentHelper;
use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Table\Table as JTable;

require_once __DIR__ . '/script.install.helper.php';

class Com_RegularLabsManagerInstallerScript extends Com_RegularLabsManagerInstallerScriptHelper
{
	public $name           = 'REGULARLABSEXTENSIONMANAGER';
	public $alias          = 'extensionmanager';
	public $extname        = 'regularlabsmanager';
	public $extension_type = 'component';
	public $is_nonumber    = false;

	public function uninstall($adapter)
	{
		$this->uninstallPlugin($this->extname, 'actionlog');
	}

	public function onBeforeInstall($route)
	{
		if ( ! parent::onBeforeInstall($route))
		{
			return false;
		}

		$this->is_nonumber = (JFactory::getApplication()->input->get('option') == 'com_nonumbermanager');

		return true;
	}

	public function onAfterInstall($route)
	{
		$this->deleteOldFiles();
		$this->fixAssetsRules();

		// Check if old NoNumber Extension Manager is still installed
		if ( ! JFolder::exists(JPATH_ADMINISTRATOR . '/components/com_nonumbermanager'))
		{
			return;
		}

		if ($this->is_nonumber)
		{
			$this->copyParamsFromNoNumberExtensionManager();
			$this->preUninstallNoNumberExtensionManager();

			return;
		}

		$this->uninstallNoNumberExtensionManager();

		return parent::onAfterInstall($route);
	}

	private function deleteOldFiles()
	{
		$this->delete(
			[
				JPATH_SITE . '/components/com_regularlabsmanager',
			]
		);
	}

	private function uninstallNoNumberExtensionManager()
	{
		$this->uninstallComponent('com_nonumbermanager', false);
	}

	private function copyParamsFromNoNumberExtensionManager()
	{
		$data = JComponentHelper::getComponent('com_regularlabsmanager');
		$data = json_decode(json_encode($data), true);

		if ( ! empty($data['params']))
		{
			return;
		}

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('params'))
			->from('#__extensions')
			->where($db->quoteName('element') . ' = ' . $db->quote('com_nonumbermanager'));
		$db->setQuery($query);
		$params = $db->loadResult();

		if (empty($params))
		{
			return;
		}

		$data['params'] = $params;

		$table = JTable::getInstance('extension');

		// Load the previous Data
		if ( ! $table->load($data['id']))
		{
			return;
		}

		unset($data['id']);

		$table->bind($data) && $table->check() && $table->store();
	}

	private function preUninstallNoNumberExtensionManager()
	{
		// Copy uninstall file
		JFile::copy(__DIR__ . '/nonumbermanager.php', JPATH_ADMINISTRATOR . '/components/com_nonumbermanager/nonumbermanager.php');

		// Remove the version number in the xml file so that the NoNumber Framework requirement check ignores this
		$contents = file_get_contents(JPATH_ADMINISTRATOR . '/components/com_nonumbermanager/nonumbermanager.xml');
		$contents = preg_replace('#@version.*\n#', '', $contents);
		file_put_contents(JPATH_ADMINISTRATOR . '/components/com_nonumbermanager/nonumbermanager.xml', $contents);

		// Remove admin menu item
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->delete('#__menu')
			->where($db->quoteName('alias') . ' = ' . $db->quote('com-nonumbermanager'));

		$db->setQuery($query);
		$db->execute();
	}
}
