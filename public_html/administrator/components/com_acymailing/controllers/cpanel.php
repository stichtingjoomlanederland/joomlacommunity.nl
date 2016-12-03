<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php

class CpanelController extends acymailingController{


	function __construct($config = array()){
		parent::__construct($config);
		$this->registerDefaultTask('display');
	}

	function save(){
		$this->store();
		return $this->cancel();
	}

	function apply(){
		$this->store();
		return $this->display();
	}

	function listing(){
		if(!$this->isAllowed('configuration', 'manage')) return;
		return $this->display();
	}

	function store(){
		if(!$this->isAllowed('configuration', 'manage')) return;

		JRequest::checkToken() or die('Invalid Token');

		$source = is_array($_POST['config']) ? 'POST' : 'REQUEST';
		$formData = JRequest::getVar('config', array(), $source, 'array');

		$aclcats = JRequest::getVar('aclcat', array(), 'POST', 'array');

		if(!empty($aclcats)){

			if(JRequest::getString('acl_configuration', 'all') != 'all' && !acymailing_isAllowed($formData['acl_configuration_manage'])){
				acymailing_enqueueMessage(JText::_('ACL_WRONG_CONFIG'), 'notice');
				unset($formData['acl_configuration_manage']);
			}

			$deleteAclCats = array();
			$unsetVars = array('save', 'create', 'manage', 'modify', 'delete', 'fields', 'export', 'import', 'view', 'send', 'schedule', 'bounce', 'test');
			foreach($aclcats as $oneCat){
				if(JRequest::getString('acl_'.$oneCat) == 'all'){
					foreach($unsetVars as $oneVar){
						unset($formData['acl_'.$oneCat.'_'.$oneVar]);
					}
					$deleteAclCats[] = $oneCat;
				}
			}
		}

		if(!empty($formData['hostname'])){
			$formData['hostname'] = preg_replace('#https?://#i', '', $formData['hostname']);
			$formData['hostname'] = preg_replace('#[^a-z0-9_.-]#i', '', $formData['hostname']);
		}

		$reasons = JRequest::getVar('unsub_reasons', array(), 'POST', 'array');
		$unsub_reasons = array();
		foreach($reasons as $oneReason){
			if(empty($oneReason)) continue;
			$unsub_reasons[] = strip_tags($oneReason);
		}
		$formData['unsub_reasons'] = serialize($unsub_reasons);

		$config =& acymailing_config();
		$status = $config->save($formData);

		if(!empty($deleteAclCats)){
			$db = JFactory::getDBO();
			$db->setQuery("DELETE FROM `#__acymailing_config` WHERE `namekey` LIKE 'acl_".implode("%' OR `namekey` LIKE 'acl_", $deleteAclCats)."%'");
			$db->query();
		}

		if($status){
			acymailing_enqueueMessage(JText::_('JOOMEXT_SUCC_SAVED'), 'message');
		}else{
			acymailing_enqueueMessage(JText::_('ERROR_SAVING'), 'error');
		}

		$config->load();
	}

	function test(){
		if(!$this->isAllowed('configuration', 'manage')) return;
		$this->store();

		acymailing_displayErrors();

		$config = acymailing_config();
		$user = JFactory::getUser();

		$mailClass = acymailing_get('helper.mailer');
		$addedName = $config->get('add_names', true) ? $mailClass->cleanText($user->name) : '';
		$mailClass->AddAddress($user->email, $addedName);
		$mailClass->Subject = 'Test e-mail from '.ACYMAILING_LIVE;
		$mailClass->Body = JText::_('TEST_EMAIL');
		$mailClass->SMTPDebug = 1;
		if(defined('JDEBUG') AND JDEBUG) $mailClass->SMTPDebug = 2;
		$result = $mailClass->send();

		if(!$result){
			$bounce = $config->get('bounce_email');
			if($config->get('mailer_method') == 'smtp' && $config->get('smtp_secured') == 'ssl' && !function_exists('openssl_sign')){
				acymailing_enqueueMessage('The PHP Extension openssl is not enabled on your server, this extension is required to use an SSL connection, please enable it', 'notice');
			}elseif(!empty($bounce) AND !in_array($config->get('mailer_method'), array('smtp', 'elasticemail'))){
				acymailing_enqueueMessage(JText::sprintf('ADVICE_BOUNCE', '<b><i>'.$bounce.'</i></b>'), 'notice');
			}elseif($config->get('mailer_method') == 'smtp' AND !$config->get('smtp_auth') AND strlen($config->get('smtp_password')) > 1){
				acymailing_enqueueMessage(JText::_('ADVICE_SMTP_AUTH'), 'notice');
			}elseif((strpos(ACYMAILING_LIVE, 'localhost') OR strpos(ACYMAILING_LIVE, '127.0.0.1')) AND in_array($config->get('mailer_method'), array('sendmail', 'qmail', 'mail'))){
				acymailing_enqueueMessage(JText::_('ADVICE_LOCALHOST'), 'notice');
			}elseif($config->get('mailer_method') == 'smtp' AND $config->get('smtp_port') AND !in_array($config->get('smtp_port'), array(25, 2525, 465, 587))){
				acymailing_enqueueMessage(JText::sprintf('ADVICE_PORT', $config->get('smtp_port')), 'notice');
			}
		}

		return $this->display();
	}

	function plgtrigger(){
		$pluginToTrigger = JRequest::getCmd('plg');
		$pluginType = JRequest::getCmd('plgtype', 'acymailing');
		$fctName = 'onAcy'.JRequest::getCmd('fctName', 'TestPlugin');
		$methodParam = JRequest::getCmd('param', 'NoParam');

		if(!ACYMAILING_J16){
			$path = JPATH_PLUGINS.DS.$pluginType.DS.$pluginToTrigger.'.php';
		}else{
			$path = JPATH_PLUGINS.DS.$pluginType.DS.$pluginToTrigger.DS.$pluginToTrigger.'.php';
		}

		if(!file_exists($path)){
			acymailing_display('Plugin not found: '.$path, 'error');
			return;
		}

		require_once($path);
		$className = 'plg'.$pluginType.$pluginToTrigger;
		if(!class_exists($className)){
			acymailing_display('Class not found: '.$className, 'error');
			return;
		}

		$dispatcher = JDispatcher::getInstance();
		$instance = new $className($dispatcher, array('name' => $pluginToTrigger, 'type' => $pluginType));

		$fctName = ($fctName == 'onAcyTestPlugin') ? 'onTestPlugin' : $fctName;
		if(!method_exists($instance, $fctName)){
			acymailing_display('Method "'.$fctName.'" not found in: '.$className, 'error');
			return;
		}

		if($methodParam == 'NoParam'){
			$instance->$fctName();
		}else $instance->$fctName($methodParam);
		return;
	}

	function seereport(){
		if(!$this->isAllowed('configuration', 'manage')) return;
		$config = acymailing_config();

		$path = trim(html_entity_decode($config->get('cron_savepath')));
		if(!preg_match('#^[a-z0-9/_\-]*\.log$#i', $path)){
			acymailing_display('The log file must only contain alphanumeric characters and end with .log', 'error');
			return;
		}

		$reportPath = JPath::clean(ACYMAILING_ROOT.$path);

		if(file_exists($reportPath)){
			try{
				$lines = 10000;
				$f = fopen($reportPath, "rb");
				fseek($f, -1, SEEK_END);
				if(fread($f, 1) != "\n") $lines -= 1;

				$logFile = '';
				while(ftell($f) > 0 && $lines >= 0){
					$seek = min(ftell($f), 4096); // Figure out how far back we should jump
					fseek($f, -$seek, SEEK_CUR);
					$logFile = ($chunk = fread($f, $seek)).$logFile; // Get the line
					fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);
					$lines -= substr_count($chunk, "\n"); // Move to previous line
				}

				while($lines++ < 0){
					$logFile = substr($logFile, strpos($logFile, "\n") + 1);
				}
				fclose($f);
			}catch(Exception $e){
				$logFile = '';
			}
		}

		if(empty($logFile)){
			acymailing_display(JText::_('EMPTY_LOG'), 'info');
		}else{
			echo nl2br($logFile);
		}
	}

	function cleanreport(){
		if(!$this->isAllowed('configuration', 'manage')) return;
		jimport('joomla.filesystem.file');
		$config = acymailing_config();
		$path = trim(html_entity_decode($config->get('cron_savepath')));
		if(!preg_match('#^[a-z0-9/_\-]*\.log$#i', $path)){
			acymailing_display('The log file must only contain alphanumeric characters and end with .log', 'error');
			return;
		}

		$reportPath = JPath::clean(ACYMAILING_ROOT.$path);
		if(is_file($reportPath)){
			$result = JFile::delete($reportPath);
			if($result){
				acymailing_display(JText::_('SUCC_DELETE_LOG'), 'success');
			}else{
				acymailing_display(JText::_('ERROR_DELETE_LOG'), 'error');
			}
		}else{
			acymailing_display(JText::_('EXIST_LOG'), 'info');
		}
	}

	function cancel(){
		$this->setRedirect(acymailing_completeLink('dashboard', false, true));
	}

	function checkDB(){
		$queries = file_get_contents(ACYMAILING_BACK.'tables.sql');
		$tables = explode("CREATE TABLE IF NOT EXISTS", $queries);
		$structure = array();
		$createTable = array();
		foreach($tables as $oneTable){
			$fields = explode("\n\t", $oneTable);
			$tableNameTmp = substr($oneTable, strpos($oneTable, '`') + 1, strlen($oneTable) - 1);
			$tableName = substr($tableNameTmp, 0, strpos($tableNameTmp, '`'));
			if(empty($tableName)) continue;
			foreach($fields as $oneField){
				if(substr($oneField, 0, 1) != '`' || substr($oneField, strlen($oneField) - 1, strlen($oneField)) != ',') continue;
				$fieldNameTmp = substr($oneField, strpos($oneField, '`') + 1, strlen($oneField) - 1);
				$fieldName = substr($fieldNameTmp, 0, strpos($fieldNameTmp, '`'));
				$structure[$tableName][$fieldName] = trim($oneField, ",");
			}
			$createTable[$tableName] = "CREATE TABLE IF NOT EXISTS ".$oneTable;
		}

		$db = JFactory::getDBO();
		$tableName = array_keys($structure);
		$structureDB = array();
		foreach($tableName as $oneTableName){
			try{
				$db->setQuery("SHOW COLUMNS FROM ".$oneTableName);
				$fields2 = $db->loadObjectList();
			}catch(Exception $e){
				$fields2 = null;
			}
			if($fields2 == null){
				$errorMessage = (isset($e) ? $e->getMessage() : substr(strip_tags($db->getErrorMsg()), 0, 200));
				echo "<span style=\"color:blue\">Could not load columns from the table : ".$oneTableName." : ".$errorMessage."</span><br />";

				if(strpos($errorMessage, 'marked as crashed')){
					$repairQuery = 'REPAIR TABLE '.$oneTableName;

					$db->setQuery($repairQuery);
					try{
						$isError = $db->query();
					}catch(Exception $e){
						$isError = null;
					}
					if($isError == null){
						echo "<span style=\"color:red\">[ERROR]Could not repair the table ".$oneTableName." </span><br />";
						acymailing_display(isset($e) ? $e->getMessage() : substr(strip_tags($db->getErrorMsg()), 0, 200).'...', 'error');
					}else{
						echo "<span style=\"color:green\">[OK]Problem solved : Table ".$oneTableName." repaired</span><br />";
					}
					continue;
				}

				$db->setQuery($createTable[$oneTableName]);
				try{
					$isError = $db->query();
				}catch(Exception $e){
					$isError = null;
				}
				if($isError == null){
					echo "<span style=\"color:red\">[ERROR]Could not create the table ".$oneTableName." </span><br />";
					acymailing_display(isset($e) ? $e->getMessage() : substr(strip_tags($db->getErrorMsg()), 0, 200).'...', 'error');
				}else{
					echo "<span style=\"color:green\">[OK]Problem solved : Table ".$oneTableName." created</span><br />";
				}
				continue;
			}
			foreach($fields2 as $oneField){
				$structureDB[$oneTableName][$oneField->Field] = $oneField->Field;
			}
		}
		foreach($tableName as $oneTableName){
			if(empty($structureDB[$oneTableName])) continue;
			$resultCompare[$oneTableName] = array_diff(array_keys($structure[$oneTableName]), $structureDB[$oneTableName]);
			if(empty($resultCompare[$oneTableName])){
				echo "<span style=\"color:green\">Table ".$oneTableName." OK</span><br />";
				continue;
			}
			foreach($resultCompare[$oneTableName] as $oneField){
				echo "<span style=\"color:blue\">Field ".$oneField." missing in ".$oneTableName."</span><br />";
				try{
					$db->setQuery("ALTER TABLE ".$oneTableName." ADD ".$structure[$oneTableName][$oneField]);
					$isError = $db->query();
				}catch(Exception $e){
					$isError = null;
				}
				if($isError == null){
					echo "<span style=\"color:red\">[ERROR]Could not add the field ".$oneField." on the table : ".$oneTableName."</span><br />";
					acymailing_display(isset($e) ? $e->getMessage() : substr(strip_tags($db->getErrorMsg()), 0, 200).'...', 'error');
					continue;
				}else{
					echo "<span style=\"color:green\">[OK]Problem solved : Add ".$oneField." in ".$oneTableName."</span><br />";
				}
			}
		}

		$db->setQuery("DELETE listsub.* FROM #__acymailing_listsub as listsub LEFT JOIN #__acymailing_subscriber as sub ON sub.subid = listsub.subid WHERE sub.subid IS NULL");
		$db->query();
		$nbdeleted = $db->getAffectedRows();
		if(!empty($nbdeleted)){
			echo "<span style=\"color:blue\">".$nbdeleted." lost subscriber entries fixed</span><br />";
		}

		$db->setQuery("DELETE listsub.* FROM #__acymailing_listsub AS listsub LEFT JOIN #__acymailing_list AS b ON listsub.listid = b.listid WHERE b.listid IS NULL");
		$db->query();
		$nbdeleted = $db->getAffectedRows();
		if(!empty($nbdeleted)){
			echo "<span style=\"color:blue\">".$nbdeleted." lost list entries fixed</span><br />";
		}

		$db->setQuery('SELECT namekey FROM #__acymailing_fields WHERE type NOT IN (\'category\',\'customtext\')');
		$customFields = array_keys($db->loadObjectList('namekey'));
		$db->setQuery("SHOW COLUMNS FROM #__acymailing_subscriber");
		$subFields = $db->loadObjectList();
		$subFieldsName = array();
		foreach($subFields as $oneField){
			$subFieldsName[] = $oneField->Field;
		}
		$fieldsDiff = array_diff($customFields, $subFieldsName);
		if(!empty($fieldsDiff)){
			echo '<span style="color:red;">At least one field is missing in the subscriber table or has not the same case between fields and subscriber table (they should all be lower case): <span style="font-weight: bold">'.implode(', ', $fieldsDiff).'</span>. You should only create fields using the custom fields interface.</span>';
		}else{
			echo '<span style="color:green;">Custom fields OK</span>';
		}
	}
}
