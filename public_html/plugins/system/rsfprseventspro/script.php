<?php
/**
* @package RSForm!Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class plgSystemRSFPRSEventsProInstallerScript
{
	public function preflight($type, $parent) {
		if ($type == 'uninstall') {
			return true;
		}
		
		$app = JFactory::getApplication();
			
		if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/rsform.php')) {
			$app->enqueueMessage('Please install the RSForm!Pro component before continuing.', 'error');
			return false;
		}
		
		if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_rseventspro/rseventspro.php')) {
			$app->enqueueMessage('Please install the RSEvents!Pro component before continuing.', 'error');
			return false;
		}
		
		if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/assets.php')) {
			$app->enqueueMessage('Please upgrade RSForm! Pro to at least version 1.51.0 before continuing!', 'error');
			return false;
		}
		
		$jversion = new JVersion();
		if (!$jversion->isCompatible('2.5.28')) {
			$app->enqueueMessage('Please upgrade to at least Joomla! 2.5.28 before continuing!', 'error');
			return false;
		}
		
		if (file_exists(JPATH_SITE.'/components/com_rseventspro/helpers/version.php')) {
			require_once JPATH_SITE.'/components/com_rseventspro/helpers/version.php';
			$version = new RSEventsProVersion;
			$version = $version->version;
			
			if (!version_compare($version, '1.8.11', '>=')) {
				$app->enqueueMessage('You need to have at least version 1.8.11 of RSEvents!Pro in order to continue.', 'error');
				return false;
			}
		}
		
		return true;
	}
	
	public function install($parent) {
		$this->copyFiles($parent);
	}
	
	public function update($parent) {
		$db = JFactory::getDbo();
		
		$db->setQuery("SELECT `ComponentTypeId` FROM `#__rsform_component_type_fields` WHERE `FieldName` = 'FLOW' AND `ComponentTypeId` = 33");
		if (!$db->loadResult()) {
			$db->setQuery("INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(33, 'FLOW', 'select', 'HORIZONTAL\r\nVERTICAL', '11')");
			$db->execute();
		}
		
		$this->copyFiles($parent);
		
		$source = $parent->getParent()->getPath('source');
		$this->runSQL($source, 'install.sql');
	}
	
	protected function runSQL($source, $file) {
		$db 	= JFactory::getDbo();
		$driver = strtolower($db->name);
		if (strpos($driver, 'mysql') !== false) {
			$driver = 'mysql';
		} elseif ($driver == 'sqlsrv') {
			$driver = 'sqlazure';
		}
		
		$sqlfile = $source.'/sql/'.$driver.'/'.$file;
		
		if (file_exists($sqlfile)) {
			$buffer = file_get_contents($sqlfile);
			if ($buffer !== false) {
				$queries = JInstallerHelper::splitSql($buffer);
				foreach ($queries as $query) {
					$query = trim($query);
					if ($query != '' && $query{0} != '#') {
						$db->setQuery($query);
						if (!$db->execute()) {
							JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
						}
					}
				}
			}
		}
	}
	
	protected function copyFiles($parent) {
		$app = JFactory::getApplication();
		$installer = $parent->getParent();
		$src = $installer->getPath('source').'/admin';
		$dest = JPATH_ADMINISTRATOR.'/components/com_rsform';
		
		if (!JFolder::copy($src, $dest, '', true)) {
			$app->enqueueMessage('Could not copy to '.str_replace(JPATH_ADMINISTRATOR, '', $dest).', please make sure destination is writable!', 'error');
		}
	}
}