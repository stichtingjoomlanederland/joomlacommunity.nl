<?php
/*
 *  Akeeba Backup JMonitoring integration
 *  Copyright (C) 2012-2013  Nicholas K. Dionysopoulos / AkeebaBackup.com
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
// no direct access
defined('_JEXEC') or die();


// Basic check #1 - is PHP5 installed?
if(defined('PHP_VERSION')) {
	$version = PHP_VERSION;
} elseif(function_exists('phpversion')) {
	$version = phpversion();
} else {
	// No version info. I'll lie and hope for the best.
	$version = '5.0.0';
}

// Old PHP version detected. EJECT! EJECT! EJECT!
if(!version_compare($version, '5.2.7', '>=')) return;

// Basic check #2 - is Akeeba Backup installed?
JLoader::import('joomla.filesystem.file');
if( !JFile::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_akeeba'.DS.'version.php') ) return;

// Basic check #3: Make sure Akeeba Backup is enabled
JLoader::import('joomla.application.component.helper');
if (!JComponentHelper::isEnabled('com_akeeba', true))
{
	//JError::raiseError('E_JPNOTENABLED', JText('MOD_AKADMIN_AKEEBA_NOT_ENABLED'));
	return;
}

// Load FOF
if(!defined('FOF_INCLUDED') || !class_exists('FOFLess', true)) {
	include_once JPATH_SITE.'/libraries/fof/include.php';
}

// Do we really, REALLY have Akeeba Engine?
if(!defined('AKEEBAENGINE')) {
	define('AKEEBAENGINE', 1); // Required for accessing Akeeba Engine's factory class
}
@include_once JPATH_ADMINISTRATOR.'/components/com_akeeba/akeeba/factory.php';
if(!class_exists('AEFactory', false)) {
	return;
}


//accès à la classe JMonitoring
require_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_jmonitoringslave'.DS.'jmonitoringpluginmonitoring'.DS.'jmonitoringpluginmonitoring.php');

class plgJmonitoringAkeebabackup extends JMonitoringPluginMonitoring
{
	function onMonitoringCall($oldValuesSerialized = null)
	{
		// Plugin setup
		$this->setName("AkeebaBackup"); //on donne une nom (unique) au plugin
		$this->setDescription("Checks the status of your Akeeba Backup backups"); //on donne une description au plugin

		// Load language strings
		$jlang = JFactory::getLanguage();
		$jlang->load('com_akeeba', JPATH_ADMINISTRATOR, 'en-GB', true);
		$jlang->load('com_akeeba'.'.override', JPATH_ADMINISTRATOR, 'en-GB', true);
		
		// Load Akeeba Backup's configuration
		require_once JPATH_ADMINISTRATOR.'/components/com_akeeba/akeeba/factory.php';
		$aeconfig = AEFactory::getConfiguration();
		AEPlatform::getInstance()->load_configuration();

		// Get latest non-SRP backup ID
		$filters = array(
			array(
				'field'			=> 'tag',
				'operand'		=> '<>',
				'value'			=> 'restorepoint'
			)
		);
		$ordering = array(
			'by'		=> 'backupstart',
			'order'		=> 'DESC'
		);
		require_once JPATH_ADMINISTRATOR.'/components/com_akeeba/models/statistics.php';
		$model = new AkeebaModelStatistics();
		$list = $model->getStatisticsListWithMeta(false, $filters, $ordering);

		if(!empty($list)) {
			$record = (object)array_shift($list);
		} else {
			$record = null;
		}
		
		// Process "failed backup" warnings, if specified
		if(!is_null($record))
		{
			JLoader::import('joomla.utilities.date');
			$jOn = new JDate($record->backupstart);
			
			// Warn on failed backups
			if($record->status == 'fail') {
				$this->createJMonitoringAlert(2, "The latest backup which started on ".$jOn->toSql(false)." has failed");
			}
			
			// Warn on still running backups
			if($record->status == 'run') {
				$this->createJMonitoringAlert(1, "The latest backup which started on ".$jOn->toSql(false)." is still running");
			}
			
			// Warn on out of date backups
			if(version_compare(JVERSION, '3.0', 'ge')) {
				$maxperiod = $this->params->get('maxbackupperiod', 24);
			} else {
				$maxperiod = $this->params->getValue('maxbackupperiod', 24);
			}
			if($maxperiod > 0) {
				$lastBackupRaw = $record->backupstart;
				$lastBackupObject = new JDate($lastBackupRaw);
				$lastBackup = $lastBackupObject->toUnix(false);
				$maxBackup = time() - $maxperiod * 3600;
				if($lastBackup < $maxBackup) {
					$this->createJMonitoringAlert(1, "The backup is out of date");
				}
			}
			
			// Set up some values
			switch($record->status)
			{
				case 'run':
					$status = JText::_('STATS_LABEL_STATUS_PENDING');
					break;

				case 'fail':
					$status = JText::_('STATS_LABEL_STATUS_FAIL');
					break;

				case 'complete':
					$status = JText::_('STATS_LABEL_STATUS_OK');
					break;
			}

			switch($record->origin)
			{
				case 'frontend':
					$origin = JText::_('STATS_LABEL_ORIGIN_FRONTEND');
					break;

				case 'backend':
					$origin = JText::_('STATS_LABEL_ORIGIN_BACKEND');
					break;

				case 'cli':
					$origin = JText::_('STATS_LABEL_ORIGIN_CLI');
					break;

				default:
					$origin = '&ndash;';
					break;
			}
			$backupStatus = $record->status;
			$this->createJMonitoringValue('Akeeba Backup – Status', $status);
			$this->createJMonitoringValue('Akeeba Backup – Origin', $origin);
			$this->createJMonitoringValue('Akeeba Backup – Start', $jOn->format(JText::_('DATE_FORMAT_LC2'), true));
		}
		
		return $this;
	}
}