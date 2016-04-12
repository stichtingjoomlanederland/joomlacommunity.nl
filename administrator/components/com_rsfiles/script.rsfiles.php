<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class com_rsfilesInstallerScript 
{
	function install($parent) {}
	
	public function preflight($type, $parent) {
		$app = JFactory::getApplication();
		
		$jversion = new JVersion();
		if (!$jversion->isCompatible('2.5.5')) {
			$app->enqueueMessage('Please upgrade to at least Joomla! 2.5.5 before continuing!', 'error');
			return false;
		}
		
		return true;
	}

	function postflight($type, $parent) {
		$db = JFactory::getDbo();
		
		// Get a new installer
		$installer = new JInstaller();
		
		// Install the module
		$installer->install($parent->getParent()->getPath('source').'/other/plugins/editor');
		$installer->install($parent->getParent()->getPath('source').'/other/plugins/system');
		$installer->install($parent->getParent()->getPath('source').'/other/plugins/installer');
		
		$db->setQuery('UPDATE '.$db->qn('#__extensions').' SET '.$db->qn('enabled').' = 1 WHERE '.$db->qn('element').' = '.$db->q('rsfiles').' AND '.$db->qn('type').' = '.$db->q('plugin').' AND '.$db->qn('folder').' = '.$db->q('editors-xtd'));
		$db->execute();
			
		$db->setQuery('UPDATE '.$db->qn('#__extensions').' SET '.$db->qn('enabled').' = 1 WHERE '.$db->qn('element').' = '.$db->q('rsfiles').' AND '.$db->qn('type').' = '.$db->q('plugin').' AND '.$db->qn('folder').' = '.$db->q('system'));
		$db->execute();
			
		$db->setQuery('UPDATE '.$db->qn('#__extensions').' SET '.$db->qn('enabled').' = 1 WHERE '.$db->qn('element').' = '.$db->q('rsfiles').' AND '.$db->qn('type').' = '.$db->q('plugin').' AND '.$db->qn('folder').' = '.$db->q('installer'));
		$db->execute();
		
		$db->setQuery("SELECT `id` FROM `#__rsfiles_emails` WHERE `type` = 'briefcaseupload' AND `lang` = 'en-GB'");
		if (!$db->loadResult()) {
			$db->setQuery("INSERT IGNORE INTO `#__rsfiles_emails` (`id`, `type`, `lang`, `enable`, `mode`, `to`, `subject`, `message`) VALUES('', 'briefcaseupload', 'en-GB', 1, 1, '', 'New files uploaded.', '<p>Hello {name},</p>\r\n<p>New files have been uploaded to your briefcase folder by {uploader}.</p>\r\n<p>Here are the uploaded files:</p>\r\n<p>{file}</p>')");
			$db->execute();
		}
		
		$db->setQuery("SELECT `id` FROM `#__rsfiles_emails` WHERE `type` = 'moderate' AND `lang` = 'en-GB'");
		if (!$db->loadResult()) {
			$db->setQuery("INSERT IGNORE INTO `#__rsfiles_emails` (`id`, `type`, `lang`, `enable`, `mode`, `to`, `subject`, `message`) VALUES('', 'moderate', 'en-GB', 1, 1, '', 'A new file was uploaded and needs to be moderated.', '<p>Hello,</p>\r\n<p>a new file was uploaded and needs your approval. If you wish to view the file please click <a href=\"{file}\">here</a>, otherwise click <a href=\"{approve}\">here</a> to appove it.</p>')");
			$db->execute();
		}
		
		if ($type == 'install') {
			// Create the default download folder
			if (!is_dir(JPATH_SITE.'/downloads')) {
				JFolder::create(JPATH_SITE.'/downloads');
			}
			
			$db->setQuery('SELECT '.$db->qn('ConfigValue').' FROM '.$db->qn('#__rsfiles_config').' WHERE '.$db->qn('ConfigName').' = '.$db->q('download_folder').' ');
			if (!$db->loadResult()) {
				$db->setQuery('UPDATE '.$db->qn('#__rsfiles_config').' SET '.$db->qn('ConfigValue').' = '.$db->q(realpath(JPATH_SITE.'/downloads')).' WHERE '.$db->qn('ConfigName').' = '.$db->q('download_folder').' ');
				$db->execute();
			}
			
			// Create the default briefcase folder
			if (!is_dir(JPATH_SITE.'/briefcase')) {
				JFolder::create(JPATH_SITE.'/briefcase');
			}
			
			$db->setQuery('SELECT '.$db->qn('ConfigValue').' FROM '.$db->qn('#__rsfiles_config').' WHERE '.$db->qn('ConfigName').' = '.$db->q('briefcase_folder').' ');
			if (!$db->loadResult()) {
				$db->setQuery('UPDATE '.$db->qn('#__rsfiles_config').' SET '.$db->qn('ConfigValue').' = '.$db->q(realpath(JPATH_SITE.'/briefcase')).' WHERE '.$db->qn('ConfigName').' = '.$db->q('briefcase_folder').' ');
				$db->execute();
			}
		}
		
		if ($type == 'update') {
			$sqlfile = JPATH_ADMINISTRATOR.'/components/com_rsfiles/install.mysql.sql';
			$buffer = file_get_contents($sqlfile);
			if ($buffer === false) {
				JError::raiseWarning(1, JText::_('JLIB_INSTALLER_ERROR_SQL_READBUFFER'));
				return false;
			}
			
			jimport('joomla.installer.helper');
			$queries = JInstallerHelper::splitSql($buffer);
			if (count($queries) == 0) {
				// No queries to process
				return 0;
			}
			
			// Process each query in the $queries array (split out of sql file).
			foreach ($queries as $query) {
				$query = trim($query);
				if ($query != '' && $query{0} != '#') {
					$db->setQuery($query);
					if (!$db->execute()) {
						JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
						return false;
					}
				}
			}
			
			$this->updateProcess();
		}
		
		$messages = array(
			'plugins' 	=> array(),
			'modules' 	=> array(),
			'messages' 	=> array()
		);
		
		$this->checkPlugins($messages, $parent);
		$this->showInstall($messages);
	}

	function uninstall($parent) {
		$db			= JFactory::getDbo();
		$installer	= new JInstaller();
		
		$db->setQuery('SELECT '.$db->qn('extension_id').' FROM '.$db->qn('#__extensions').' WHERE '.$db->qn('element').' = '.$db->q('rsfiles').' AND '.$db->qn('folder').' = '.$db->q('system').' AND '.$db->qn('type').' = '.$db->q('plugin').' LIMIT 1');
		$sysid = $db->loadResult();
		if ($sysid) $installer->uninstall('plugin', $sysid);
		
		$db->setQuery('SELECT '.$db->qn('extension_id').' FROM '.$db->qn('#__extensions').' WHERE '.$db->qn('element').' = '.$db->q('rsfiles').' AND '.$db->qn('folder').' = '.$db->q('editors-xtd').' AND '.$db->qn('type').' = '.$db->q('plugin').' LIMIT 1');
		$extid = $db->loadResult();
		if ($extid) $installer->uninstall('plugin', $extid);
		
		$db->setQuery('SELECT '.$db->qn('extension_id').' FROM '.$db->qn('#__extensions').' WHERE '.$db->qn('element').' = '.$db->q('rsfiles').' AND '.$db->qn('folder').' = '.$db->q('installer').' AND '.$db->qn('type').' = '.$db->q('plugin').' LIMIT 1');
		$instid = $db->loadResult();
		if ($instid) $installer->uninstall('plugin', $instid);
		
		$this->showUninstall();
	}
	
	protected function updateProcess() {
		$db = JFactory::getDbo();
		
		// Joomla! 1.5 update scripts
		$db->setQuery('DESCRIBE '.$db->qn('#__rsfiles_files'));
		$files_table = $db->loadObjectList();
		
		$hasPublished = false;
		$hasDownloadLimit = false;
		$hasDownloads = false;
		$hasMetaTitle = false;
		$hasMetaDescription = false;
		$hasMetaKeywords = false;
		$hasPublishDown = false;
		$hasFileType = false;
		$hasFileParent = false;
		
		foreach ($files_table as $obj) {
			if ($obj->Field == 'published')			$hasPublished = true;
			if ($obj->Field == 'DownloadLimit') 	$hasDownloadLimit = true;
			if ($obj->Field == 'Downloads')			$hasDownloads = true;
			if ($obj->Field == 'metatitle')			$hasMetaTitle = true;
			if ($obj->Field == 'metadescription')	$hasMetaDescription = true;
			if ($obj->Field == 'metakeywords')		$hasMetaKeywords = true;
			if ($obj->Field == 'publish_down')		$hasPublishDown = true;
			if ($obj->Field == 'FileType')			$hasFileType = true;
			if ($obj->Field == 'FileParent')		$hasFileParent = true;
			if ($obj->Field == 'ScreenshotsTags')	$hasScreenshotsTags = true;	
		}
		
		if (!$hasPublished) {
			$db->setQuery("ALTER TABLE `#__rsfiles_files` ADD `published` INT( 2 ) NOT NULL DEFAULT '1'");
			$db->execute();
		}
		
		if (!$hasDownloadLimit)	{
			$db->setQuery("ALTER TABLE `#__rsfiles_files` ADD `DownloadLimit` INT( 11 ) NOT NULL AFTER `DownloadMethod`");
			$db->execute();
		}
		
		if (!$hasDownloads) {
			$db->setQuery("ALTER TABLE `#__rsfiles_files` ADD `Downloads` INT( 11 ) NOT NULL AFTER `hits`");
			$db->execute();
		}
		
		if (!$hasMetaTitle) {
			$db->setQuery("ALTER TABLE `#__rsfiles_files` ADD `metatitle` VARCHAR( 255 ) NOT NULL AFTER `CanView`");
			$db->execute();
		}
		
		if (!$hasMetaDescription) {
			$db->setQuery("ALTER TABLE `#__rsfiles_files` ADD `metadescription` TEXT NOT NULL AFTER `CanView`");
			$db->execute();
		}
		
		if (!$hasMetaKeywords) {
			$db->setQuery("ALTER TABLE `#__rsfiles_files` ADD `metakeywords` TEXT NOT NULL AFTER `CanView`");
			$db->execute();
		}
		
		if (!$hasPublishDown) {
			$db->setQuery("ALTER TABLE `#__rsfiles_files` ADD `publish_down` INT( 15 ) NOT NULL");
			$db->execute();
		}
		
		if (!$hasFileType) {
			$db->setQuery("ALTER TABLE `#__rsfiles_files` ADD `FileType` TINYINT( 1 ) NOT NULL");
			$db->execute();
		}
		
		if (!$hasFileParent) {
			$db->setQuery("ALTER TABLE `#__rsfiles_files` ADD `FileParent` TEXT NOT NULL");
			$db->execute();
		}
		
		if (!$hasScreenshotsTags) {
			$db->setQuery("ALTER TABLE `#__rsfiles_files` ADD `ScreenshotsTags` VARCHAR( 255 ) NOT NULL AFTER `FileParent`");
			$db->execute();
		}
		
		$db->setQuery("SHOW COLUMNS FROM `#__rsfiles_groups` WHERE `Field`='CanDownloadBriefcase'");
		if (!$db->loadResult()) {
			$db->setQuery("ALTER TABLE `#__rsfiles_groups` ADD `CanDownloadBriefcase` tinyint(4) NOT NULL AFTER `GroupName`");
			$db->execute();

			$db->setQuery("ALTER TABLE `#__rsfiles_groups` ADD `CanUploadBriefcase` tinyint(4) NOT NULL AFTER `CanDownloadBriefcase`");
			$db->execute();

			$db->setQuery("ALTER TABLE `#__rsfiles_groups` ADD `CanDeleteBriefcase` TINYINT( 4 ) NOT NULL AFTER `CanUploadBriefcase`");
			$db->execute();

			$db->setQuery("ALTER TABLE `#__rsfiles_groups` ADD `CanMaintainBriefcase` tinyint( 4 ) NOT NULL AFTER `CanDeleteBriefcase`");
			$db->execute();

			$db->setQuery("ALTER TABLE `#__rsfiles_groups` ADD `MaxFilesNo` INT( 11 ) NOT NULL AFTER `CanMaintainBriefcase`");
			$db->execute();

			$db->setQuery("ALTER TABLE `#__rsfiles_groups` ADD `MaxFilesSize` INT( 11 ) NOT NULL AFTER `MaxFilesNo`");
			$db->execute();

			$db->setQuery("ALTER TABLE `#__rsfiles_groups` ADD `MaxFileSize` INT( 11 ) NOT NULL AFTER `MaxFilesSize`");
			$db->execute();
		}
		
		$db->setQuery("SHOW COLUMNS FROM `#__rsfiles_email_downloads` WHERE `Field`='email'");
		if (!$db->loadResult()) {
			$db->setQuery("ALTER TABLE `#__rsfiles_email_downloads` ADD `email` VARCHAR( 255 ) NOT NULL AFTER `date`");
			$db->execute();
		}
		
		$db->setQuery("SHOW COLUMNS FROM `#__rsfiles_email_downloads` WHERE `Field`='name'");
		if (!$db->loadResult()) {
			$db->setQuery("ALTER TABLE `#__rsfiles_email_downloads` ADD `name` VARCHAR( 255 ) NOT NULL");
			$db->execute();
		}
		// Joomla! 1.5 update scripts
		
		
		$db->setQuery("SHOW COLUMNS FROM #__rsfiles_statistics WHERE Field = ".$db->q('Date')."");
		if ($datest = $db->loadObject()) {
			if ($datest->Type == 'int(15)') {
				$db->setQuery("ALTER TABLE #__rsfiles_statistics CHANGE `Date` `Date` VARCHAR(255) NOT NULL");
				$db->execute();
				$db->setQuery("UPDATE #__rsfiles_statistics SET `Date` = FROM_UNIXTIME(`Date`)");
				$db->execute();
				$db->setQuery("UPDATE #__rsfiles_statistics SET `Date` = '0000-00-00 00:00:00' WHERE `Date` LIKE '1970-01-01%'");
				$db->execute();					
				$db->setQuery("ALTER TABLE #__rsfiles_statistics CHANGE `Date` ".$db->qn('Date')." DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'");
				$db->execute();
			}
		}
		
		$db->setQuery("SHOW COLUMNS FROM #__rsfiles_reports WHERE Field = ".$db->q('date')."");
		if ($dater = $db->loadObject()) {
			if ($dater->Type == 'int(20)') {
				$db->setQuery("ALTER TABLE #__rsfiles_reports CHANGE `date` `date` VARCHAR(255) NOT NULL");
				$db->execute();
				$db->setQuery("UPDATE #__rsfiles_reports SET `date` = FROM_UNIXTIME(`date`)");
				$db->execute();
				$db->setQuery("UPDATE #__rsfiles_reports SET `date` = '0000-00-00 00:00:00' WHERE `date` LIKE '1970-01-01%'");
				$db->execute();					
				$db->setQuery("ALTER TABLE #__rsfiles_reports CHANGE `date` ".$db->qn('date')." DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'");
				$db->execute();
			}
		}
		
		$db->setQuery("SHOW COLUMNS FROM #__rsfiles_files WHERE Field = ".$db->q('DateAdded')."");
		if ($datef1 = $db->loadObject()) {
			if ($datef1->Type == 'int(15)') {
				$db->setQuery("ALTER TABLE #__rsfiles_files CHANGE `DateAdded` `DateAdded` VARCHAR(255) NOT NULL");
				$db->execute();
				$db->setQuery("UPDATE #__rsfiles_files SET `DateAdded` = FROM_UNIXTIME(`DateAdded`)");
				$db->execute();
				$db->setQuery("UPDATE #__rsfiles_files SET `DateAdded` = '0000-00-00 00:00:00' WHERE `DateAdded` LIKE '1970-01-01%'");
				$db->execute();					
				$db->setQuery("ALTER TABLE #__rsfiles_files CHANGE `DateAdded` ".$db->qn('DateAdded')." DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'");
				$db->execute();
			}
		}
		
		$db->setQuery("SHOW COLUMNS FROM #__rsfiles_files WHERE Field = ".$db->q('ModifiedDate')."");
		if ($datef1 = $db->loadObject()) {
			if ($datef1->Type == 'int(15)') {
				$db->setQuery("ALTER TABLE #__rsfiles_files CHANGE `ModifiedDate` `ModifiedDate` VARCHAR(255) NOT NULL");
				$db->execute();
				$db->setQuery("UPDATE #__rsfiles_files SET `ModifiedDate` = FROM_UNIXTIME(`ModifiedDate`)");
				$db->execute();
				$db->setQuery("UPDATE #__rsfiles_files SET `ModifiedDate` = '0000-00-00 00:00:00' WHERE `ModifiedDate` LIKE '1970-01-01%'");
				$db->execute();					
				$db->setQuery("ALTER TABLE #__rsfiles_files CHANGE `ModifiedDate` ".$db->qn('ModifiedDate')." DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'");
				$db->execute();
			}
		}
		
		$db->setQuery("SHOW COLUMNS FROM #__rsfiles_files WHERE Field = ".$db->q('publish_down')."");
		if ($datef1 = $db->loadObject()) {
			if ($datef1->Type == 'int(15)') {
				$db->setQuery("ALTER TABLE #__rsfiles_files CHANGE `publish_down` `publish_down` VARCHAR(255) NOT NULL");
				$db->execute();
				$db->setQuery("UPDATE #__rsfiles_files SET `publish_down` = FROM_UNIXTIME(`publish_down`)");
				$db->execute();
				$db->setQuery("UPDATE #__rsfiles_files SET `publish_down` = '0000-00-00 00:00:00' WHERE `publish_down` LIKE '1970-01-01%'");
				$db->execute();					
				$db->setQuery("ALTER TABLE #__rsfiles_files CHANGE `publish_down` ".$db->qn('publish_down')." DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'");
				$db->execute();
			}
		}
		
		$db->setQuery("SHOW COLUMNS FROM `#__rsfiles_groups` WHERE `Field` = 'jgroups'");
		if (!$db->loadResult()) {
			$db->setQuery("ALTER TABLE `#__rsfiles_groups` ADD `jgroups` TEXT NOT NULL AFTER `GroupName`");
			$db->execute();
		}
		
		$db->setQuery("SHOW COLUMNS FROM `#__rsfiles_groups` WHERE `Field` = 'jusers'");
		if (!$db->loadResult()) {
			$db->setQuery("ALTER TABLE `#__rsfiles_groups` ADD `jusers` TEXT NOT NULL AFTER `jgroups`");
			$db->execute();
		}
		
		// Update groups table with data
		$tables = $db->getTableList();
		if (in_array($db->getPrefix().'rsfiles_group_details', $tables)) {
			$db->setQuery("SELECT g.IdGroup, gd.IdUsers, gd.IdAcls FROM `#__rsfiles_group_details` gd LEFT JOIN #__rsfiles_groups g ON g.IdGroup = gd.IdGroup");
			if ($permissions = $db->loadObjectList()) {
				foreach ($permissions as $permission) {
					$jgroups = '';
					$jusers = '';
					if (!empty($permission->IdAcls)) {
						$acls = explode(',',$permission->IdAcls);
						$registry = new JRegistry;
						$registry->loadArray($acls);
						$jgroups = $registry->toString();
					}
					
					if (!empty($permission->IdUsers)) {
						$users = explode(',',$permission->IdUsers);
						$registry = new JRegistry;
						$registry->loadArray($users);
						$jusers = $registry->toString();
					}
					
					$db->setQuery("UPDATE #__rsfiles_groups SET `jgroups` = '".$db->escape($jgroups)."', `jusers` = '".$db->escape($jusers)."' WHERE `IdGroup` = '".(int) $permission->IdGroup."' ");
					$db->execute();
				}
			}
		}
		
		// Drop groups permissions table
		$db->setQuery("DROP TABLE IF EXISTS `#__rsfiles_group_details`");
		$db->execute();
		
		// Emails update
		$db->setQuery("SELECT `ConfigValue` FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_admin_enable'");
		$email_admin_enable = $db->loadResult();
		if (!is_null($email_admin_enable)) {
			$db->setQuery("UPDATE `#__rsfiles_emails` SET `enable` = ".(int) $email_admin_enable." WHERE `type` = 'admin'");
			$db->execute();
			$db->setQuery("DELETE FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_admin_enable'");
			$db->execute();
		}
		
		$db->setQuery("SELECT `ConfigValue` FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_admin_mode'");
		$email_admin_mode = $db->loadResult();
		if (!is_null($email_admin_mode)) {
			$db->setQuery("UPDATE `#__rsfiles_emails` SET `mode` = ".(int) $email_admin_mode." WHERE `type` = 'admin'");
			$db->execute();
			$db->setQuery("DELETE FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_admin_mode'");
			$db->execute();
		}
		
		$db->setQuery("SELECT `ConfigValue` FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_admin_subject'");
		$email_admin_subject = $db->loadResult();
		
		if (!is_null($email_admin_subject)) {
			$db->setQuery("UPDATE `#__rsfiles_emails` SET `subject` = ".$db->q($email_admin_subject)." WHERE `type` = 'admin'");
			$db->execute();
			$db->setQuery("DELETE FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_admin_subject'");
			$db->execute();
		}
		
		$db->setQuery("SELECT `ConfigValue` FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_admin_to'");
		$email_admin_to = $db->loadResult();
		if (!is_null($email_admin_to)) {
			$db->setQuery("UPDATE `#__rsfiles_emails` SET `to` = ".$db->q($email_admin_to)." WHERE `type` = 'admin'");
			$db->execute();
			$db->setQuery("DELETE FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_admin_to'");
			$db->execute();
		}
		
		$db->setQuery("SELECT `ConfigValue` FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_admin_message'");
		$email_admin_message = $db->loadResult();
		if (!is_null($email_admin_message)) {
			$db->setQuery("UPDATE `#__rsfiles_emails` SET `message` = ".$db->q($email_admin_message)." WHERE `type` = 'admin'");
			$db->execute();
			$db->setQuery("DELETE FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_admin_message'");
			$db->execute();
		}
		
		$db->setQuery("SELECT `ConfigValue` FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_download_subject'");
		$email_download_subject = $db->loadResult();
		if (!is_null($email_download_subject)) {
			$db->setQuery("UPDATE `#__rsfiles_emails` SET `subject` = ".$db->q($email_download_subject)." WHERE `type` = 'download'");
			$db->execute();
			$db->setQuery("DELETE FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_download_subject'");
			$db->execute();
		}
		
		$db->setQuery("SELECT `ConfigValue` FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_download_message'");
		$email_download_message = $db->loadResult();
		if (!is_null($email_download_message)) {
			$db->setQuery("UPDATE `#__rsfiles_emails` SET `message` = ".$db->q($email_download_message)." WHERE `type` = 'download'");
			$db->execute();
			$db->setQuery("DELETE FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_download_message'");
			$db->execute();
		}
		
		$db->setQuery("SELECT `ConfigValue` FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_download_mode'");
		$email_download_mode = $db->loadResult();
		if (!is_null($email_download_mode)) {
			$db->setQuery("UPDATE `#__rsfiles_emails` SET `mode` = ".(int) $email_download_mode." WHERE `type` = 'download'");
			$db->execute();
			$db->setQuery("DELETE FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_download_mode'");
			$db->execute();
		}
		
		$db->setQuery("SELECT `ConfigValue` FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_upload_enable'");
		$email_upload_enable = $db->loadResult();
		if (!is_null($email_upload_enable)) {
			$db->setQuery("UPDATE `#__rsfiles_emails` SET `enable` = ".(int) $email_upload_enable." WHERE `type` = 'upload'");
			$db->execute();
			$db->setQuery("DELETE FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_upload_enable'");
			$db->execute();
		}
		
		$db->setQuery("SELECT `ConfigValue` FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_upload_mode'");
		$email_upload_mode = $db->loadResult();
		if (!is_null($email_upload_mode)) {
			$db->setQuery("UPDATE `#__rsfiles_emails` SET `mode` = ".(int) $email_upload_mode." WHERE `type` = 'upload'");
			$db->execute();
			$db->setQuery("DELETE FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_upload_mode'");
			$db->execute();
		}
		
		$db->setQuery("SELECT `ConfigValue` FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_upload_subject'");
		$email_upload_subject = $db->loadResult();
		if (!is_null($email_upload_subject)) {
			$db->setQuery("UPDATE `#__rsfiles_emails` SET `subject` = ".$db->q($email_upload_subject)." WHERE `type` = 'upload'");
			$db->execute();
			$db->setQuery("DELETE FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_upload_subject'");
			$db->execute();
		}
		
		$db->setQuery("SELECT `ConfigValue` FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_upload_to'");
		$email_upload_to = $db->loadResult();
		if (!is_null($email_upload_to)) {
			$db->setQuery("UPDATE `#__rsfiles_emails` SET `to` = ".$db->q($email_upload_to)." WHERE `type` = 'upload'");
			$db->execute();
			$db->setQuery("DELETE FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_upload_to'");
			$db->execute();
		}
		
		$db->setQuery("SELECT `ConfigValue` FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_upload_message'");
		$email_upload_message = $db->loadResult();
		if (!is_null($email_upload_message)) {
			$db->setQuery("UPDATE `#__rsfiles_emails` SET `message` = ".$db->q($email_upload_message)." WHERE `type` = 'upload'");
			$db->execute();
			$db->setQuery("DELETE FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_upload_message'");
			$db->execute();
		}
		
		$db->setQuery("SELECT `ConfigValue` FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_report_enable'");
		$email_report_enable = $db->loadResult();
		if (!is_null($email_report_enable)) {
			$db->setQuery("UPDATE `#__rsfiles_emails` SET `enable` = ".(int) $email_report_enable." WHERE `type` = 'report'");
			$db->execute();
			$db->setQuery("DELETE FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_report_enable'");
			$db->execute();
		}
		
		$db->setQuery("SELECT `ConfigValue` FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_report_mode'");
		$email_report_mode = $db->loadResult();
		if (!is_null($email_report_mode)) {
			$db->setQuery("UPDATE `#__rsfiles_emails` SET `mode` = ".(int) $email_report_mode." WHERE `type` = 'report'");
			$db->execute();
			$db->setQuery("DELETE FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_report_mode'");
			$db->execute();
		}
		
		$db->setQuery("SELECT `ConfigValue` FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_report_subject'");
		$email_report_subject = $db->loadResult();
		if (!is_null($email_report_subject)) {
			$db->setQuery("UPDATE `#__rsfiles_emails` SET `subject` = ".$db->q($email_report_subject)." WHERE `type` = 'report'");
			$db->execute();
			$db->setQuery("DELETE FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_report_subject'");
			$db->execute();
		}
		
		$db->setQuery("SELECT `ConfigValue` FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_report_to'");
		$email_report_to = $db->loadResult();
		if (!is_null($email_report_to)) {
			$db->setQuery("UPDATE `#__rsfiles_emails` SET `to` = ".$db->q($email_report_to)." WHERE `type` = 'report'");
			$db->execute();
			$db->setQuery("DELETE FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_report_to'");
			$db->execute();
		}
		
		$db->setQuery("SELECT `ConfigValue` FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_report_message'");
		$email_report_message = $db->loadResult();
		if (!is_null($email_report_message)) {
			$db->setQuery("UPDATE `#__rsfiles_emails` SET `message` = ".$db->q($email_report_message)." WHERE `type` = 'report'");
			$db->execute();
			$db->setQuery("DELETE FROM `#__rsfiles_config` WHERE `ConfigName` = 'email_report_message'");
			$db->execute();
		}
		
		// Update menu items
		$db->setQuery("SELECT `ConfigValue` FROM `#__rsfiles_config` WHERE `ConfigName` = 'default_order'");
		$order = $db->loadResult();
		$db->setQuery("SELECT `ConfigValue` FROM `#__rsfiles_config` WHERE `ConfigName` = 'default_order_way'");
		$direction = $db->loadResult();
		$direction = strtolower($direction);
		
		$db->setQuery("SELECT `id`, `link`, `params` FROM #__menu WHERE `link` LIKE 'index.php?option=com_rsfiles&view=files' AND `type` = 'component' AND `client_id` = 0");
		if ($filemenus = $db->loadObjectList()) {
			foreach ($filemenus as $filemenu) {
				$registry = new JRegistry();
				$registry->loadString($filemenu->params);
				$registry->set('order',$order);
				$registry->set('order_way',$direction);
				$menuparams = $registry->toString();
				
				$db->setQuery("UPDATE `#__menu` SET `link` = 'index.php?option=com_rsfiles&view=rsfiles', `params` = ".$db->q($menuparams)." WHERE `id` = ".(int) $filemenu->id." ");
				$db->execute();
			}
		}
		
		$db->setQuery("SELECT `id`, `link`, `params` FROM #__menu WHERE `link` LIKE 'index.php?option=com_rsfiles&view=files&layout=timeperiod' AND `type` = 'component' AND `client_id` = 0");
		if ($filetimemenus = $db->loadObjectList()) {
			foreach ($filetimemenus as $filetimemenu) {
				$registry = new JRegistry();
				$registry->loadString($filetimemenu->params);
				$registry->set('order',$order);
				$registry->set('order_way',$direction);
				$menuparams = $registry->toString();
				
				$db->setQuery("UPDATE `#__menu` SET `link` = 'index.php?option=com_rsfiles&view=rsfiles', `params` = ".$db->q($menuparams)." WHERE `id` = ".(int) $filetimemenu->id." ");
				$db->execute();
			}
		}
		
		$db->setQuery("SELECT `id`, `link`, `params` FROM #__menu WHERE `link` LIKE 'index.php?option=com_rsfiles&view=files&layout=briefcase&destination=briefcase' AND `type` = 'component' AND `client_id` = 0");
		if ($briefcasemenus = $db->loadObjectList()) {
			foreach ($briefcasemenus as $briefcasemenu) {
				$registry = new JRegistry();
				$registry->loadString($briefcasemenu->params);
				$registry->set('order',$order);
				$registry->set('order_way',$direction);
				$menuparams = $registry->toString();
				
				$db->setQuery("UPDATE `#__menu` SET `link` = 'index.php?option=com_rsfiles&view=rsfiles&layout=briefcase', `params` = ".$db->q($menuparams)." WHERE `id` = ".(int) $briefcasemenu->id." ");
				$db->execute();
			}
		}
		
		// Update config
		$db->setQuery("SELECT `ConfigValue` FROM `#__rsfiles_config` WHERE `ConfigName` = 'show_folder_desc'");
		$show_folder_desc = $db->loadResult();
		if (!is_null($show_folder_desc)) {
			$show_folder_desc = $show_folder_desc == 2 ? 1 : $show_folder_desc;
			$db->setQuery("UPDATE `#__rsfiles_config` SET `ConfigValue` = ".(int) $show_folder_desc." WHERE `ConfigName` = 'show_folder_desc'");
			$db->execute();
		}
		
		$db->setQuery("SELECT `ConfigValue` FROM `#__rsfiles_config` WHERE `ConfigName` = 'show_descriptions'");
		$show_descriptions = $db->loadResult();
		if (!is_null($show_descriptions)) {
			$show_descriptions = $show_descriptions == 2 ? 1 : $show_descriptions;
			$db->setQuery("UPDATE `#__rsfiles_config` SET `ConfigValue` = ".(int) $show_descriptions." WHERE `ConfigName` = 'show_descriptions'");
			$db->execute();
		}
		
		$db->setQuery("SELECT `ConfigValue` FROM `#__rsfiles_config` WHERE `ConfigName` = 'show_file_size'");
		$show_file_size = $db->loadResult();
		if (!is_null($show_file_size)) {
			$show_file_size = $show_file_size == 2 ? 1 : $show_file_size;
			$db->setQuery("UPDATE `#__rsfiles_config` SET `ConfigValue` = ".(int) $show_file_size." WHERE `ConfigName` = 'show_file_size'");
			$db->execute();
		}
		
		$db->setQuery("SELECT `ConfigValue` FROM `#__rsfiles_config` WHERE `ConfigName` = 'show_date_added'");
		$show_date_added = $db->loadResult();
		if (!is_null($show_date_added)) {
			$show_date_added = $show_date_added == 2 ? 1 : $show_date_added;
			$db->setQuery("UPDATE `#__rsfiles_config` SET `ConfigValue` = ".(int) $show_date_added." WHERE `ConfigName` = 'show_date_added'");
			$db->execute();
		}
		
		$db->setQuery("SELECT `ConfigValue` FROM `#__rsfiles_config` WHERE `ConfigName` = 'show_date_updated'");
		$show_date_updated = $db->loadResult();
		if (!is_null($show_date_updated)) {
			$show_date_updated = $show_date_updated == 2 ? 1 : $show_date_updated;
			$db->setQuery("UPDATE `#__rsfiles_config` SET `ConfigValue` = ".(int) $show_date_updated." WHERE `ConfigName` = 'show_date_updated'");
			$db->execute();
		}
		
		$db->setQuery("SELECT `ConfigValue` FROM `#__rsfiles_config` WHERE `ConfigName` = 'show_license'");
		$show_license = $db->loadResult();
		if (!is_null($show_license)) {
			$show_license = $show_license == 2 ? 1 : $show_license;
			$db->setQuery("UPDATE `#__rsfiles_config` SET `ConfigValue` = ".(int) $show_license." WHERE `ConfigName` = 'show_license'");
			$db->execute();
		}
		
		$db->setQuery("SELECT `ConfigValue` FROM `#__rsfiles_config` WHERE `ConfigName` = 'show_file_version'");
		$show_file_version = $db->loadResult();
		if (!is_null($show_file_version)) {
			$show_file_version = $show_file_version == 2 ? 1 : $show_file_version;
			$db->setQuery("UPDATE `#__rsfiles_config` SET `ConfigValue` = ".(int) $show_file_version." WHERE `ConfigName` = 'show_file_version'");
			$db->execute();
		}
		
		$db->setQuery("SELECT `ConfigValue` FROM `#__rsfiles_config` WHERE `ConfigName` = 'show_number_of_downloads'");
		$show_number_of_downloads = $db->loadResult();
		if (!is_null($show_number_of_downloads)) {
			$show_number_of_downloads = $show_number_of_downloads == 2 ? 1 : $show_number_of_downloads;
			$db->setQuery("UPDATE `#__rsfiles_config` SET `ConfigValue` = ".(int) $show_number_of_downloads." WHERE `ConfigName` = 'show_number_of_downloads'");
			$db->execute();
		}
		
		# Version 1.14.0
		
		$db->setQuery("SHOW COLUMNS FROM `#__rsfiles_files` WHERE `Field` = 'briefcase'");
		if (!$db->loadResult()) {
			$db->setQuery("ALTER TABLE `#__rsfiles_files` ADD `briefcase` TINYINT( 1 ) NOT NULL AFTER `FilePath`");
			$db->execute();
		}
		
		$db->setQuery("SHOW INDEX FROM #__rsfiles_files WHERE Key_name = 'FilePath'");
		if ($db->loadObject()) {
			$db->setQuery("ALTER TABLE `#__rsfiles_files` DROP INDEX `FilePath`");
			$db->execute();
		}
		
		$db->setQuery("SHOW COLUMNS FROM `#__rsfiles_files` WHERE `Field` = 'CanCreate'");
		if (!$db->loadResult()) {
			$db->setQuery("ALTER TABLE `#__rsfiles_files` ADD `CanCreate` VARCHAR( 225 ) NOT NULL");
			$db->execute();
		}
		
		$db->setQuery("SHOW COLUMNS FROM `#__rsfiles_files` WHERE `Field` = 'CanUpload'");
		if (!$db->loadResult()) {
			$db->setQuery("ALTER TABLE `#__rsfiles_files` ADD `CanUpload` VARCHAR( 225 ) NOT NULL");
			$db->execute();
		}
		
		$db->setQuery("SHOW COLUMNS FROM `#__rsfiles_files` WHERE `Field` = 'CanDelete'");
		if (!$db->loadResult()) {
			$db->setQuery("ALTER TABLE `#__rsfiles_files` ADD `CanDelete` VARCHAR( 225 ) NOT NULL");
			$db->execute();
		}
		
		$db->setQuery("SHOW COLUMNS FROM `#__rsfiles_files` WHERE `Field` = 'CanEdit'");
		if (!$db->loadResult()) {
			$db->setQuery("ALTER TABLE `#__rsfiles_files` ADD `CanEdit` VARCHAR( 225 ) NOT NULL");
			$db->execute();
		}
		
		$db->setQuery("SHOW COLUMNS FROM `#__rsfiles_files` WHERE `Field` = 'preview'");
		if (!$db->loadResult()) {
			$db->setQuery("ALTER TABLE `#__rsfiles_files` ADD `preview` VARCHAR( 225 ) NOT NULL");
			$db->execute();
		}
		
		$db->setQuery("SHOW COLUMNS FROM `#__rsfiles_files` WHERE `Field` = 'CanPerformMaintenance'");
		if ($db->loadResult()) {
			// Get download and briefcase folder
			$db->setQuery("SELECT `ConfigValue` FROM `#__rsfiles_config` WHERE `ConfigName` = 'download_folder' ");
			$download_folder = $db->loadResult();
			$db->setQuery("SELECT `ConfigValue` FROM `#__rsfiles_config` WHERE `ConfigName` = 'briefcase_folder' ");
			$briefcase_folder = $db->loadResult();
			
			$db->setQuery("SELECT `IdFile`, `FilePath`, `CanPerformMaintenance`, `FileType`, `FileParent` FROM `#__rsfiles_files`");
			if ($files = $db->loadObjectList()) {
				foreach ($files as $file) {
					$permission = $file->CanPerformMaintenance;
					$briefcase	= 0;
					
					if ($file->FileType == 1) {
						$briefcase = 0;
						$path = str_replace($download_folder.DIRECTORY_SEPARATOR, '', $file->FileParent);
						
						$db->setQuery("UPDATE `#__rsfiles_files` SET `FileParent` = ".$db->q($path).", `CanCreate` = ".$db->q($permission)." , `CanUpload` = ".$db->q($permission)." , `CanDelete` = ".$db->q($permission)." , `CanEdit` = ".$db->q($permission)." , `briefcase` = ".$db->q($briefcase)." WHERE `IdFile` = ".$db->q($file->IdFile)." ");
					} else {
						if (strpos($file->FilePath, $download_folder) !== false) {
							$briefcase = 0;
							$path = str_replace($download_folder.DIRECTORY_SEPARATOR, '', $file->FilePath);
						} else if (strpos($file->FilePath, $briefcase_folder) !== false) {
							$briefcase = 1;
							$path = str_replace($briefcase_folder.DIRECTORY_SEPARATOR, '', $file->FilePath);
						}
						
						$db->setQuery("UPDATE `#__rsfiles_files` SET `FilePath` = ".$db->q($path).", `CanCreate` = ".$db->q($permission)." , `CanUpload` = ".$db->q($permission)." , `CanDelete` = ".$db->q($permission)." , `CanEdit` = ".$db->q($permission)." , `briefcase` = ".$db->q($briefcase)." WHERE `IdFile` = ".$db->q($file->IdFile)." ");
					}
					
					$db->execute();
				}
			}
			
			$db->setQuery("ALTER TABLE `#__rsfiles_files` DROP `CanPerformMaintenance`");
			$db->execute();
		}
		
		$db->setQuery("SELECT `ConfigValue` FROM `#__rsfiles_config` WHERE `ConfigName` = 'download_maintenance' ");
		if ($download_maintenance = $db->loadResult()) {
			$db->setQuery("UPDATE `#__rsfiles_config` SET `ConfigValue` = ".$db->q($download_maintenance)." WHERE `ConfigName` = 'download_cancreate'");
			$db->execute();
			$db->setQuery("UPDATE `#__rsfiles_config` SET `ConfigValue` = ".$db->q($download_maintenance)." WHERE `ConfigName` = 'download_canupload'");
			$db->execute();
			$db->setQuery("DELETE FROM `#__rsfiles_config` WHERE `ConfigName` = 'download_maintenance'");
			$db->execute();
		}
		
		$db->setQuery("SHOW INDEX FROM #__rsfiles_files WHERE Key_name = 'RSFKey'");
		if ($db->loadObject()) {
			$db->setQuery("ALTER TABLE `#__rsfiles_files` DROP INDEX `RSFKey`");
			$db->execute();
		}
		
		# Version 1.15.0
		 
		$db->setQuery("SHOW COLUMNS FROM `#__rsfiles_groups` WHERE `Field` = 'moderate'");
		if (!$db->loadResult()) {
			$db->setQuery("ALTER TABLE `#__rsfiles_groups` ADD `moderate` TINYINT( 2 ) NOT NULL");
			$db->execute();
		}
		
		$db->setQuery("SHOW COLUMNS FROM `#__rsfiles_files` WHERE `Field` = 'show_preview'");
		if (!$db->loadResult()) {
			$db->setQuery("ALTER TABLE `#__rsfiles_files` ADD `show_preview` TINYINT( 2 ) NOT NULL");
			$db->execute();
			$db->setQuery("UPDATE `#__rsfiles_files` SET `show_preview` = 1");
			$db->execute();
		}
		
		# Version 1.15.6
		
		$db->setQuery("SHOW COLUMNS FROM `#__rsfiles_groups` WHERE `Field` = 'editown'");
		if (!$db->loadResult()) {
			$db->setQuery("ALTER TABLE `#__rsfiles_groups` ADD `editown` TINYINT( 2 ) NOT NULL");
			$db->execute();
		}
		
		$db->setQuery("SHOW COLUMNS FROM `#__rsfiles_groups` WHERE `Field` = 'deleteown'");
		if (!$db->loadResult()) {
			$db->setQuery("ALTER TABLE `#__rsfiles_groups` ADD `deleteown` TINYINT( 2 ) NOT NULL");
			$db->execute();
		}
	}
	
	protected function checkPlugins(&$messages, $parent = null) {
		$lang = JFactory::getLanguage();
		$plugins = array(
			'rsfiles',
			'plg_rssearch_rsfiles',
			'rsmrsfiles'
		);
		
		if ($installed = $this->getPlugins($plugins)) {
			// need to update old plugins
			foreach ($installed as $plugin) {
				$file = JPATH_SITE.'/plugins/'.$plugin->folder.'/'.$plugin->element.'/'.$plugin->element.'.xml';
				if (file_exists($file)) {
					$xml = file_get_contents($file);
					
					// Check for old 1.5 plugins
					if (strpos($xml, '<extension') === false) {
						$this->disableExtension($plugin->extension_id);
						
						$status = 'warning';
						$text	= 'Disabled';
						
						$messages['plugins'][] = (object) array(
							'name' 		=> $plugin->name,
							'status' 	=> $status,
							'text'		=> $text
						);
					} else {
						if ($plugin->element == 'rsfiles' && $plugin->folder == 'search') {
							if ($this->checkVersion($xml,'1.2','>')) {
								$lang->load('plg_search_rsfiles');
								$this->unpublishPlugin($plugin->extension_id);
								$messages['messages'][] = 'Please update the plugin "'.JText::_($plugin->name).'" manually.';
							}
						}
						
						if ($plugin->element == 'rsfiles' && $plugin->folder == 'rssearch') {
							if ($this->checkVersion($xml,'1.1','>')) {
								$lang->load('plg_rssearch_rsfiles');
								$this->unpublishPlugin($plugin->extension_id);
								$messages['messages'][] = 'Please update the plugin "'.JText::_($plugin->name).'" manually.';
							}
						}
						
						if ($plugin->element == 'plg_rssearch_rsfiles') {
							if ($this->checkVersion($xml,'1.1','>')) {
								$lang->load('plg_rssearch_rsfiles');
								$this->unpublishPlugin($plugin->extension_id);
								$messages['messages'][] = 'Please update the plugin "'.JText::_($plugin->name).'" manually.';
							}
						}
						
						if ($plugin->element == 'rsmrsfiles') {
							if ($this->checkVersion($xml,'1.0.1','>')) {
								$lang->load('rsmrsfiles');
								$this->unpublishPlugin($plugin->extension_id);
								$messages['messages'][] = 'Please update the plugin "'.JText::_($plugin->name).'" manually.';
							}
						}
					}
				}
			}
		}
		
		$modules = array(
			'mod_rsfiles_folder_content_viewer',
			'mod_rsfiles_latest'
		);
		
		if ($installed = $this->getModules($modules)) {
			foreach ($installed as $module) {
				$file = JPATH_SITE.'/modules/'.$module->element.'/'.$module->element.'.xml';
				if (file_exists($file)) {
					$xml = file_get_contents($file);
					
					if (strpos($xml, '<install') !== false) {
						$this->disableExtension($module->extension_id);
						
						$messages['modules'][] = (object) array(
							'name' 		=> $module->name,
							'status' 	=> 'warning',
							'text'		=> 'Disabled'
						);
					} else {
						if ($module->element == 'mod_rsfiles_folder_content_viewer') {
							$lang->load('mod_rsfiles_folder_content_viewer',JPATH_SITE);
							if ($this->checkVersion($xml,'1.1.1','>')) {
								$this->unpublishModule($module->element);
								$messages['messages'][] = 'Please update the module "'.JText::_($module->name).'" manually.';
							}
						}
						
						if ($module->element == 'mod_rsfiles_latest') {
							$lang->load('mod_rsfiles_latest',JPATH_SITE);
							if ($this->checkVersion($xml,'1.1.1','>')) {
								$this->unpublishModule($module->element);
								$messages['messages'][] = 'Please update the module "'.JText::_($module->name).'" manually.';
							}
						}
					}
				}
			}
		}
	}
	
	protected function disableExtension($extension_id) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->update('#__extensions')
			  ->set($db->quoteName('enabled').'='.$db->quote(0))
			  ->where($db->quoteName('extension_id').'='.$db->quote($extension_id));
		$db->setQuery($query);
		$db->execute();
	}
	
	protected function unpublishModule($module) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->update('#__modules')
			  ->set($db->quoteName('published').'='.$db->quote(0))
			  ->where($db->quoteName('module').'='.$db->quote($module));
		$db->setQuery($query);
		$db->execute();
	}
	
	protected function unpublishPlugin($extension_id) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->update('#__extensions')
			  ->set($db->quoteName('enabled').'='.$db->quote(0))
			   ->where($db->quoteName('extension_id').'='.$db->quote($extension_id));
		$db->setQuery($query);
		$db->execute();
	}
	
	protected function getModules($element) {
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$one	= false;
		if (!is_array($element)) {
			$element = array($element);
			$one = true;
		}
		
		$query->select('*')
			  ->from('#__extensions')
			  ->where($db->quoteName('type').'='.$db->quote('module'))
			  ->where($db->quoteName('element').' IN ('.$this->quoteImplode($element).')');
		$db->setQuery($query);
		
		return $one ? $db->loadObject() : $db->loadObjectList();
	}
	
	protected function getPlugins($element) {
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$one	= false;
		if (!is_array($element)) {
			$element = array($element);
			$one = true;
		}
		
		$query->select('*')
			  ->from('#__extensions')
			  ->where($db->quoteName('type').'='.$db->quote('plugin'))
			  ->where($db->quoteName('folder').' IN ('.$this->quoteImplode(array('search', 'system', 'rsmail', 'rssearch')).')')
			  ->where($db->quoteName('element').' IN ('.$this->quoteImplode($element).')');
		$db->setQuery($query);
		
		return $one ? $db->loadObject() : $db->loadObjectList();
	}
	
	protected function quoteImplode($array) {
		$db = JFactory::getDbo();
		foreach ($array as $k => $v) {
			$array[$k] = $db->quote($v);
		}
		
		return implode(',', $array);
	}
	
	protected function checkVersion($string, $version, $operator = '>') {
		preg_match('#<version>(.*?)<\/version>#is',$string,$match);
		if (isset($match) && isset($match[1])) {
			return version_compare($version,$match[1],$operator);
		}
		
		return false;
	}
	
	protected function showUninstall() {
		echo 'RSFiles! component has been successfully uninstaled!';
	}
	
	protected function showInstall($messages) {
?>
<style type="text/css">
.version-history {
	margin: 0 0 2em 0;
	padding: 0;
	list-style-type: none;
}
.version-history > li {
	margin: 0 0 0.5em 0;
	padding: 0 0 0 4em;
}
.version,
.version-new,
.version-fixed,
.version-upgraded {
	float: left;
	font-size: 0.8em;
	margin-left: -4.9em;
	width: 4.5em;
	color: white;
	text-align: center;
	font-weight: bold;
	text-transform: uppercase;
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	border-radius: 4px;
}

.version {
	background: #000;
}

.version-new {
	background: #7dc35b;
}
.version-fixed {
	background: #e9a130;
}
.version-upgraded {
	background: #61b3de;
}

.install-ok {
	background: #7dc35b;
	color: #fff;
	padding: 3px;
}

.install-not-ok {
	background: #E9452F;
	color: #fff;
	padding: 3px;
}

#installer-left {
	float: left;
	width: 230px;
	padding: 5px;
}

#installer-right {
	float: left;
}

.com-rsfiles-button {
	display: inline-block;
	background: #459300 url(components/com_rsfiles/assets/images/icons/bg-button-green.gif) top left repeat-x !important;
	border: 1px solid #459300 !important;
	padding: 2px;
	color: #fff !important;
	cursor: pointer;
	margin: 0;
	-webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
	text-decoration: none !important;
}

.big-warning {
	background: #FAF0DB;
	border: solid 1px #EBC46F;
	padding: 5px;
}

.big-warning b {
	color: red;
}

.big-info {
	background: #D5FFDC;
	border: solid 1px #EBC46F;
	padding: 5px;
}
</style>
<div id="installer-left">
	<img src="components/com_rsfiles/assets/images/logos/rsfiles_box.png" alt="RSFiles! Box" />
</div>
<div id="installer-right">
	
	<?php if ($messages['messages']) { ?>
	<p class="big-info">
		<?php foreach ($messages['messages'] as $message) { ?>
			<?php echo $message; ?> <br />
		<?php } ?>
	</p>
	<?php } ?>
	<?php if ($messages['plugins']) { ?>
		<p class="big-warning"><b>Warning!</b> The following plugins have been temporarily disabled to prevent any errors being shown on your website. Please <a href="http://www.rsjoomla.com/downloads.html" target="_blank">download the latest versions</a> from your account and update your installation before enabling them.</p>
		<?php foreach ($messages['plugins'] as $plugin) { ?>
		<p><?php echo $plugin->name; ?> ...
			<b class="install-<?php echo $plugin->status; ?>"><?php echo $plugin->text; ?></b>
		</p>
		<?php } ?>
	<?php } ?>
	<?php if ($messages['modules']) { ?>
		<p class="big-warning"><b>Warning!</b> The following modules have been temporarily disabled to prevent any errors being shown on your website. Please <a href="http://www.rsjoomla.com/downloads.html" target="_blank">download the latest versions</a> from your account and update your installation before enabling them.</p>
		<?php foreach ($messages['modules'] as $module) { ?>
		<p><?php echo $module->name; ?> ...
			<b class="install-<?php echo $module->status; ?>"><?php echo $module->text; ?></b>
		</p>
		<?php } ?>
	<?php } ?>
	
	<h2>Changelog v1.15.13</h2>
	<ul class="version-history">
		<li><span class="version-fixed">Fix</span> Workaround for Joomla! 3.5.1 JMail breaking backwards compatibility.</li>
	</ul>
	<a class="com-rsfiles-button" href="index.php?option=com_rsfiles">Start using RSFiles!</a>
	<a class="com-rsfiles-button" href="http://www.rsjoomla.com/support/documentation/view-knowledgebase/81-rsfiles.html" target="_blank">Read the RSFiles! User Guide</a>
	<a class="com-rsfiles-button" href="http://www.rsjoomla.com/customer-support/tickets.html" target="_blank">Get Support!</a>
</div>
<div style="clear: both;"></div>
<?php
	}
}