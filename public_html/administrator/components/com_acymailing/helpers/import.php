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

class acyimportHelper{

	var $importUserInLists = array();
	var $totalInserted = 0;
	var $totalTry = 0;
	var $totalValid = 0;
	var $allSubid = array();
	var $db;
	var $dispatcher;
	var $forceconfirm = false;
	var $charsetConvert;
	var $generatename = true;
	var $overwrite = false;
	var $importblocked = false;
	var $removeSep = 0;
	var $dispresults = true;

	var $tablename = '';
	var $equFields = array();
	var $dbwhere = array(); //handle where on import via filter to only import new users for example

	var $subscribedUsers = array();

	public function __construct(){
		acymailing_increasePerf();

		$this->db = JFactory::getDBO();

		JPluginHelper::importPlugin('acymailing');
		$this->dispatcher = JDispatcher::getInstance();
		jimport('joomla.filesystem.file');
	}

	private function getImportedLists(){
		$lists = JRequest::getVar('importlists', array());

		$newListName = JRequest::getString('createlist');
		if(empty($newListName)) return $lists;

		$newList = new stdClass();
		$newList->name = $newListName;
		$newList->published = 1;
		$colors = array('#3366ff', '#7240A4', '#7A157D', '#157D69', '#ECE649');
		$newList->color = $colors[rand(0, count($colors) - 1)];

		$listClass = acymailing_get('class.list');
		$listid = $listClass->save($newList);

		if(!empty($listid)) $lists[$listid] = 1;

		return $lists;
	}

	function database($onlyimport = false){

		$this->forceconfirm = JRequest::getInt('import_confirmed_database');

		$table = empty($this->tablename) ? trim(JRequest::getString('tablename')) : $this->tablename;

		if(empty($table)){
			$listTables = $this->db->getTableList();
			acymailing_enqueueMessage(JText::sprintf('SPECIFYTABLE', implode(' | ', $listTables)), 'notice');
			return false;
		}

		if(empty($this->tablename)){
			$newConfig = new stdClass();
			$newConfig->import_db_table = trim(JRequest::getString('tablename'));
			$newConfig->import_db_fields = serialize(JRequest::getVar('fields', array()));

			$config = acymailing_config();
			$config->save($newConfig);
		}

		$fields = acymailing_getColumns($table);
		if(empty($fields)){
			$listTables = $this->db->getTableList();
			acymailing_enqueueMessage(JText::sprintf('SPECIFYTABLE', implode(' | ', $listTables)), 'notice');
			return false;
		}

		$fields = array_keys($fields);
		$equivalentFields = empty($this->equFields) ? JRequest::getVar('fields', array()) : $this->equFields;

		if(empty($equivalentFields['email'])){
			acymailing_enqueueMessage(JText::_('SPECIFYFIELDEMAIL'), 'notice');
			return false;
		}

		$select = array();
		foreach($equivalentFields as $acyField => $tableField){
			$tableField = trim($tableField);
			if(empty($tableField)) continue;
			if(!in_array($tableField, $fields)){
				acymailing_enqueueMessage(JText::sprintf('SPECIFYFIELD', $tableField, implode(' | ', $fields)), 'notice');
				return false;
			}
			$select['`'.$acyField.'`'] = '`'.$tableField.'`';
		}

		if(empty($select['`created`'])){
			$select['`created`'] = time();
		}
		if($this->forceconfirm && empty($select['`confirmed`'])){
			$select['`confirmed`'] = 1;
		}

		$query = 'INSERT IGNORE INTO `#__acymailing_subscriber` ('.implode(' , ', array_keys($select)).') SELECT '.implode(' , ', $select).' FROM '.$table.' WHERE '.$select['`email`'].' LIKE \'%@%\'';
		if(!empty($this->dbwhere)) $query .= ' AND ( '.implode(' ) AND (', $this->dbwhere).' )';

		$this->db->setQuery($query);
		$this->db->query();
		$affectedRows = $this->db->getAffectedRows();

		acymailing_enqueueMessage(JText::sprintf('IMPORT_NEW', $affectedRows));

		if($onlyimport) return true;

		$query = 'SELECT b.subid FROM '.$table.' as a JOIN '.acymailing_table('subscriber').' as b on a.'.$select['`email`'].' = b.`email`';
		$this->db->setQuery($query);
		$this->allSubid = acymailing_loadResultArray($this->db);

		$this->_subscribeUsers();
		$this->_displaySubscribedResult();

		return true;
	}

	function textarea(){
		$content = JRequest::getString('textareaentries');
		$path = $this->_createUploadFolder();
		$filename = uniqid('import_').'.csv';

		JFile::write($path.$filename, $content);
		JRequest::setVar('filename', $filename);

		return true;
	}

	private function _createUploadFolder(){
		$folderPath = JPath::clean(ACYMAILING_ROOT.trim(html_entity_decode('media'.DS.'com_acymailing'.DS.'import'))).DS;
		if(!is_dir($folderPath)){
			acymailing_createDir($folderPath, true, true);
		}

		if(!is_writable($folderPath)){
			@chmod($folderPath, '0755');
			if(!is_writable($folderPath)){
				acymailing_enqueueMessage(JText::sprintf('WRITABLE_FOLDER', $folderPath), 'notice');
			}
		}
		return $folderPath;
	}

	function file(){
		$importFile = JRequest::getVar('importfile', array(), 'files', 'array');

		if(empty($importFile['name'])){
			acymailing_enqueueMessage(JText::_('BROWSE_FILE'), 'notice');
			return false;
		}

		$extension = strtolower(JFile::getExt($importFile['name']));
		if(in_array($extension, array('xls', 'xlsx'))){
			acymailing_display('Excel files are not supported.<br />Please convert your file into CSV :<ol><li>Open your file with Excel</li><li>Select File => Save as...</li><li>For the type, select "CSV (separator: semi-colon) (*.csv)"</li></ol>', 'error');
			return false;
		}

		$fileError = $_FILES['importfile']['error'];
		if($fileError > 0){
			switch($fileError){
				case 1:
					acymailing_display('The uploaded file exceeds the upload_max_filesize directive in php configuration.', 'error');
					return false;
				case 2:
					acymailing_display('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.', 'error');
					return false;
				case 3:
					acymailing_display('The uploaded file was only partially uploaded.', 'error');
					return false;
				case 4:
					acymailing_display('No file was uploaded.', 'error');
					return false;
				default:
					acymailing_display('Error uploading the file on the server, unknown error '.$fileError, 'error');
					return false;
			}
		}

		$config =& acymailing_config();

		$uploadPath = $this->_createUploadFolder();

		$attachment = new stdClass();
		$attachment->filename = uniqid('import_').'.csv';
		JRequest::setVar('filename', $attachment->filename);

		$attachment->size = $importFile['size'];

		if(!preg_match('#\.('.str_replace(array(',', '.'), array('|', '\.'), $config->get('allowedfiles')).')$#Ui', $attachment->filename, $extension) || preg_match('#\.(php.?|.?htm.?|pl|py|jsp|asp|sh|cgi)$#Ui', $attachment->filename)){
			acymailing_enqueueMessage(JText::sprintf('ACCEPTED_TYPE', htmlspecialchars(substr($attachment->filename, strrpos($attachment->filename, '.') + 1), ENT_COMPAT, 'UTF-8'), $config->get('allowedfiles')), 'notice');
			return false;
		}

		if(!JFile::upload($importFile['tmp_name'], $uploadPath.$attachment->filename)){
			if(!move_uploaded_file($importFile['tmp_name'], $uploadPath.$attachment->filename)){
				acymailing_enqueueMessage(JText::sprintf('FAIL_UPLOAD', '<b><i>'.htmlspecialchars($importFile['tmp_name'], ENT_COMPAT, 'UTF-8').'</i></b>', '<b><i>'.htmlspecialchars($uploadPath.$attachment->filename, ENT_COMPAT, 'UTF-8').'</i></b>'), 'error');
			}
		}
		return true;
	}

	function finalizeImport(){
		$config = acymailing_config();

		$this->forceconfirm = JRequest::getInt('import_confirmed');
		$this->generatename = JRequest::getInt('generatename');
		$this->importblocked = JRequest::getInt('importblocked');
		$this->overwrite = JRequest::getInt('overwriteexisting');

		$newConfig = new stdClass();
		$paramTmp = array();
		if($this->forceconfirm == 1) $paramTmp[] = 'import_confirmed';
		if($this->generatename == 1) $paramTmp[] = 'generatename';
		if($this->importblocked == 1) $paramTmp[] = 'importblocked';
		if($this->overwrite == 1) $paramTmp[] = 'overwriteexisting';

		$importParams = 'import_params';
		$newConfig->$importParams = implode(',', $paramTmp);
		$config->save($newConfig);

		$filename = strtolower(JRequest::getCmd('filename'));
		$extension = '.'.JFile::getExt($filename);
		$filename = str_replace(array('.', ' '), '_', substr($filename, 0, strpos($filename, $extension))).$extension;
		$uploadPath = ACYMAILING_MEDIA.'import'.DS.$filename;

		if(!file_exists($uploadPath)){
			acymailing_enqueueMessage('Uploaded file not found: '.$uploadPath, 'error');
			return;
		}

		$importColumns = JRequest::getString('import_columns');
		if(empty($importColumns)){
			acymailing_enqueueMessage('Columns not found', 'error');
			return false;
		}
		$columns = explode(',', $importColumns);
		$db = JFactory::getDBO();
		$acyColumns = acymailing_getColumns('#__acymailing_subscriber');
		foreach($columns as $oneColumn){
			if($oneColumn == 1 || $oneColumn == 'listids' || $oneColumn == 'listname' || isset($acyColumns[$oneColumn])) continue; // Ignored or existing column
			$checkColumn = preg_replace('#[^A-Za-z0-9_]#Uis', '', $oneColumn);
			if(empty($checkColumn)){
				acymailing_enqueueMessage('Invalid field name: '.$oneColumn, 'error');
				return false;
			}
			$oneColumn = $checkColumn;

			if(!acymailing_level(3)){ // Make sure we can't create a custom field
				acymailing_enqueueMessage(JText::_('EXTRA_FIELDS').' '.JText::_('ONLY_FROM_ENTERPRISE'), 'error');
				return false;
			}

			if(empty($ordering)){
				$db->setQuery('SELECT MAX(ordering) FROM #__acymailing_fields');
				$ordering = $db->loadResult();
			}
			$ordering++;
			$db->setQuery('ALTER TABLE `#__acymailing_subscriber` ADD `'.acymailing_secureField(strtolower($oneColumn)).'` VARCHAR ( 250 ) NOT NULL DEFAULT ""');
			$db->query();
			$query = "INSERT INTO `#__acymailing_fields` (`fieldname`, `namekey`, `type`, `value`, `published`, `ordering`, `options`, `core`, `required`, `backend`, `frontcomp`, `default`, `listing`, `frontlisting`, `frontform`) VALUES
			(".$db->quote($oneColumn).", ".$db->quote(strtolower($oneColumn)).", 'text', '', 1, ".intval($ordering).", '', 0, 0, 1, 0, '',0,0,1);";
			$db->setQuery($query);
			$db->query();
		}

		$contentFile = file_get_contents($uploadPath);

		if(JRequest::getCmd('charsetconvert', '') != ''){
			$encodingHelper = acymailing_get('helper.encoding');
			$contentFile = $encodingHelper->change($contentFile, JRequest::getCmd('charsetconvert'), 'UTF-8');
		}

		$cutContent = str_replace(array("\r\n", "\r"), "\n", $contentFile);
		$allLines = explode("\n", $cutContent);

		$listSeparators = array("\t", ';', ',');
		$separator = ',';
		foreach($listSeparators as $sep){
			if(strpos($allLines[0], $sep) !== false){
				$separator = $sep;
				break;
			}
		}
		$importColumns = str_replace(',', $separator, $importColumns);

		if(strpos($allLines[0], '@')){
			$contentFile = $importColumns."\n".$contentFile;
		}else{
			$allLines[0] = $importColumns;
			$contentFile = implode("\n", $allLines);
		}

		$this->_handleContent($contentFile);
		$this->_displaySubscribedResult();

		unlink($uploadPath);
		$this->_cleanImportFolder();
	}

	public function _cleanImportFolder(){
		jimport('joomla.filesystem.folder');
		$files = JFolder::files(ACYMAILING_MEDIA.'import', '.', false, true, array());
		foreach($files as $oneFile){
			if(JFile::getExt($oneFile) != 'csv') continue;
			if(filectime($oneFile) < time() - 86400) unlink($oneFile);
		}
	}

	public function _handleContent(&$contentFile){
		$success = true;
		$app = JFactory::getApplication();

		$contentFile = str_replace(array("\r\n", "\r"), "\n", $contentFile);
		$importLines = explode("\n", $contentFile);

		$i = 0;
		$this->header = '';
		$this->allSubid = array();
		while(empty($this->header) && $i < 10){
			$this->header = trim($importLines[$i]);
			$i++;
		}

		if(strpos($this->header, '@') && !strpos($this->header, ',') && !strpos($this->header, ';') && !strpos($this->header, "\t")){
			$this->header = 'email';
			$i--;
		}

		if(!$this->_autoDetectHeader()){
			acymailing_enqueueMessage(JText::sprintf('IMPORT_HEADER', htmlspecialchars($this->header, ENT_COMPAT, 'UTF-8')), 'error');
			acymailing_enqueueMessage(JText::_('IMPORT_EMAIL'), 'error');
			acymailing_enqueueMessage(JText::_('IMPORT_EXAMPLE'), 'error');
			return false;
		}

		$numberColumns = count($this->columns);

		$userHelper = acymailing_get('helper.user');

		$encodingHelper = acymailing_get('helper.encoding');

		$importUsers = array();

		$errorLines = array();

		$this->db->setQuery('SELECT COUNT(subid) FROM `#__acymailing_subscriber`');
		$countUsersBeforeImport = $this->db->loadResult();

		$listClass = acymailing_get('class.list');
		$allLists = $listClass->getLists('name');

		while(isset($importLines[$i])){
			if(strpos($importLines[$i], '"') !== false){
				$data = array();
				$j = $i + 1;
				$position = -1;

				while($j < ($i + 30)){

					$quoteOpened = substr($importLines[$i], $position + 1, 1) == '"';

					if($quoteOpened){
						$nextQuotePosition = strpos($importLines[$i], '"', $position + 2);
						while($nextQuotePosition !== false && $nextQuotePosition + 1 != strlen($importLines[$i]) && substr($importLines[$i], $nextQuotePosition + 1, 1) != $this->separator){
							$nextQuotePosition = strpos($importLines[$i], '"', $nextQuotePosition + 1);
						}
						if($nextQuotePosition === false){
							if(!isset($importLines[$j])) break;

							$importLines[$i] .= "\n".$importLines[$j];
							$importLines[$i] = rtrim($importLines[$i], $this->separator);
							unset($importLines[$j]);
							$j++;
							continue;
						}else{

							if(strlen($importLines[$i]) - 1 == $nextQuotePosition){
								$data[] = substr($importLines[$i], $position + 1);
								break;
							}
							$data[] = substr($importLines[$i], $position + 1, $nextQuotePosition + 1 - ($position + 1));
							$position = $nextQuotePosition + 1;
						}
					}else{
						$nextSeparatorPosition = strpos($importLines[$i], $this->separator, $position + 1);
						if($nextSeparatorPosition === false){
							$data[] = substr($importLines[$i], $position + 1);
							break;
						}else{ // If found the next separator, add the value in $data and change the position
							$data[] = substr($importLines[$i], $position + 1, $nextSeparatorPosition - ($position + 1));
							$position = $nextSeparatorPosition;
						}
					}
				}

				$importLines = array_merge($importLines);
			}else{
				$data = explode($this->separator, rtrim(trim($importLines[$i]), $this->separator));
			}

			if(!empty($this->removeSep)){
				for($b = $numberColumns + $this->removeSep - 1; $b >= $numberColumns; $b--){
					if(isset($data[$b]) AND (strlen($data[$b]) == 0 || $data[$b] == ' ')){
						unset($data[$b]);
					}
				}
			}

			$i++;
			if(empty($importLines[$i - 1])) continue;

			$this->totalTry++;
			if(count($data) > $numberColumns){
				$copy = $data;
				foreach($copy as $oneelem => $oneval){
					if(!empty($oneval[0]) AND $oneval[0] == '"' AND $oneval[strlen($oneval) - 1] != '"' AND isset($copy[$oneelem + 1]) AND $copy[$oneelem + 1][strlen($copy[$oneelem + 1]) - 1] == '"'){
						$data[$oneelem] = $copy[$oneelem].$this->separator.$copy[$oneelem + 1];
						unset($data[$oneelem + 1]);
					}
				}
				$data = array_values($data);
			}

			if(count($data) < $numberColumns){
				for($a = count($data); $a < $numberColumns; $a++){
					$data[$a] = '';
				}
			}

			if(count($data) != $numberColumns){
				$success = false;
				static $errorcount = 0;
				if(empty($errorcount)){
					acymailing_enqueueMessage(JText::sprintf('IMPORT_ARGUMENTS', $numberColumns), 'error');
				}
				$errorcount++;
				if($errorcount < 20){
					acymailing_enqueueMessage(JText::sprintf('IMPORT_ERRORLINE', '<b><i>'.htmlspecialchars($importLines[$i - 1], ENT_COMPAT, 'UTF-8').'</i></b>'), 'notice');
				}elseif($errorcount == 20){
					acymailing_enqueueMessage('...', 'notice');
				}

				if($this->totalTry == 1) return false;
				if(empty($errorLines)) $errorLines[] = $importLines[0];
				$errorLines[] = $importLines[$i - 1];
				continue;
			}

			$newUser = new stdClass();

			$emailKey = array_search('email', $this->columns);
			$newUser->email = trim(strip_tags($data[$emailKey]), '\'" ');
			$newUser->email = trim(str_replace(array(' ', "\t"), '', $encodingHelper->change($newUser->email, 'UTF-8', 'ISO-8859-1')));

			if(!$userHelper->validEmail($newUser->email)){
				$success = false;
				static $errorcountfail = 0;
				$errorcountfail++;
				if($errorcountfail < 10){
					acymailing_enqueueMessage(JText::sprintf('NOT_VALID_EMAIL', '<b><i>'.htmlspecialchars($newUser->email, ENT_COMPAT | ENT_IGNORE, 'UTF-8').'</i></b>').' | '.($i - 1).' : '.$importLines[$i - 1], 'notice');
				}elseif($errorcountfail == 10){
					acymailing_enqueueMessage('...', 'notice');
				}
				if(empty($errorLines)) $errorLines[] = $importLines[0];
				$errorLines[] = $importLines[$i - 1];
				continue;
			}

			foreach($data as $num => $value){
				if($num == $emailKey) continue;

				$field = $this->columns[$num];

				if($field == 1) continue;

				if($field == 'listids'){
					$liststosub = explode('-', trim($value, '\'" '));
					foreach($liststosub as $onelistid){
						$this->importUserInLists[intval(trim($onelistid))][] = $this->db->Quote($newUser->email);
					}
					continue;
				}

				if($field == 'listname'){
					$liststosub = explode('-', trim($value, '\'" '));
					foreach($liststosub as $onelistName){
						if(empty($onelistName)) continue;
						$onelistName = trim($onelistName);
						if(empty($allLists[$onelistName])){
							$newList = new stdClass();
							$newList->name = $onelistName;
							$newList->published = 1;
							$colors = array('#3366ff', '#7240A4', '#7A157D', '#157D69', '#ECE649');
							$newList->color = $colors[rand(0, count($colors) - 1)];
							$listid = $listClass->save($newList);
							$newList->listid = $listid;
							$allLists[$onelistName] = $newList;
						}
						$this->importUserInLists[intval($allLists[$onelistName]->listid)][] = $this->db->Quote($newUser->email);
					}
					continue;
				}

				if($value == 'null'){
					$newUser->$field = '';
				}else{
					$newUser->$field = trim(strip_tags($value), '\'" ');
				}
			}

			unset($newUser->subid);
			unset($newUser->userid);

			$importUsers[] = $newUser;
			$this->totalValid++;

			if($this->totalValid % 50 == 0){
				$this->_insertUsers($importUsers);
				$importUsers = array();
			}
		}

		if(!empty($errorLines)){
			$filename = strtolower(JRequest::getCmd('filename', ''));
			if(!empty($filename)){
				$extension = '.'.JFile::getExt($filename);
				$filename = str_replace(array('.', ' '), '_', substr($filename, 0, strpos($filename, $extension))).$extension;
				$errorFile = implode("\n", $errorLines);
				JFile::write(ACYMAILING_MEDIA.'import'.DS.'error_'.$filename, $errorFile);
				acymailing_enqueueMessage('<a target="_blank" href="index.php?option=com_acymailing&ctrl='.($app->isAdmin() ? '' : 'front').'data&task=downloadimport&filename=error_'.JFile::stripExt($filename).'" >'.JText::_('ACY_DOWNLOAD_IMPORT_ERRORS').'</a>', 'notice');
			}
		}
		$this->_insertUsers($importUsers);

		$this->db->setQuery('SELECT COUNT(subid) FROM `#__acymailing_subscriber`');
		$countUsersAfterImport = $this->db->loadResult();
		$this->totalInserted = $countUsersAfterImport - $countUsersBeforeImport;

		if($this->dispresults){
			acymailing_enqueueMessage(JText::sprintf('ACY_IMPORT_REPORT', $this->totalTry, $this->totalInserted, $this->totalTry - $this->totalValid, $this->totalValid - $this->totalInserted));
		}

		$this->_subscribeUsers();
		return $success;
	}

	function _subscribeUsers(){

		if(empty($this->allSubid)) return true;

		$subdate = time();

		$listClass = acymailing_get('class.list');

		if(empty($this->importUserInLists)){
			$lists = $this->getImportedLists();

			if(acymailing_level(3)){
				$campaignClass = acymailing_get('helper.campaign');
				$listCampaign = $listClass->getCampaigns(array_keys($lists));
			}else{
				$listCampaign = array();
			}

			foreach($lists as $listid => $val){
				if(empty($val)) continue;

				if($val == -1){
					$dateColumn = 'unsubdate';
					$status = -1;
				}else{
					$dateColumn = 'subdate';
					$status = 1;
				}

				$nbsubscribed = 0;
				$listid = (int)$listid;
				$query = 'INSERT IGNORE INTO '.acymailing_table('listsub').' (listid,subid,'.$dateColumn.',status) VALUES ';
				$b = 0;
				$currentSubids = array();
				foreach($this->allSubid as $subid){
					$currentSubids[] = $subid;
					$b++;

					if($b > 200){
						$query = rtrim($query, ',');
						if($val == -1){
							$query .= ' ON DUPLICATE KEY UPDATE status = -1';
							$this->db->setQuery('SELECT COUNT(*) FROM #__acymailing_listsub WHERE listid = '.$listid.' AND status != -1 AND subid IN ('.implode(',', $currentSubids).')');
							$nbsubscribed = -$this->db->loadResult();
						}
						$this->db->setQuery($query);
						$this->db->query();
						$nbsubscribed += $this->db->getAffectedRows();
						$b = 0;
						$currentSubids = array();
						$query = 'INSERT IGNORE INTO '.acymailing_table('listsub').' (listid,subid,'.$dateColumn.',status) VALUES ';
					}

					$query .= "($listid,$subid,$subdate,$status),";
				}
				$query = rtrim($query, ',');
				if($val == -1){
					$query .= ' ON DUPLICATE KEY UPDATE status = -1';
					if(!empty($currentSubids)){
						$this->db->setQuery('SELECT COUNT(*) FROM #__acymailing_listsub WHERE listid = '.$listid.' AND status != -1 AND subid IN ('.implode(',', $currentSubids).')');
						$nbsubscribed = -$this->db->loadResult();
					}
				}
				$this->db->setQuery($query);
				$this->db->query();
				$nbsubscribed += $this->db->getAffectedRows();

				if(isset($this->subscribedUsers[$listid])){
					$this->subscribedUsers[$listid]->nbusers += $nbsubscribed;
				}else{
					$myList = $listClass->get($listid);
					$myList->status = $val;
					$this->subscribedUsers[$listid] = $myList;
					$this->subscribedUsers[$listid]->nbusers = $nbsubscribed;
				}

				if(in_array($val, array(2, -1)) && !empty($listCampaign[$listid])){
					$function = $val == 2 ? 'autoSubCampaign' : 'unsubCampaign';
					foreach($listCampaign[$listid] as $campaignId){
						$campaignClass->$function($this->allSubid, $campaignId);
					}
				}
			}
		}else{
			foreach($this->importUserInLists as $listid => $arrayEmails){
				if(empty($listid)) continue;

				$listid = (int)$listid;
				$query = 'INSERT IGNORE INTO '.acymailing_table('listsub').' (listid,subid,subdate,status) ';
				$query .= "SELECT $listid,`subid`,$subdate,1 FROM ".acymailing_table('subscriber')." WHERE `email` IN (";
				$query .= implode(',', $arrayEmails).')';
				$this->db->setQuery($query);
				$this->db->query();

				$nbsubscribed = $this->db->getAffectedRows();
				if(isset($this->subscribedUsers[$listid])){
					$this->subscribedUsers[$listid]->nbusers += $nbsubscribed;
				}else{
					$myList = $listClass->get($listid);
					$this->subscribedUsers[$listid] = $myList;
					$this->subscribedUsers[$listid]->nbusers = $nbsubscribed;
				}
			}
		}

		return true;
	}

	function _displaySubscribedResult(){
		foreach($this->subscribedUsers as $myList){
			if(empty($myList->status) || $myList->status != -1){
				acymailing_enqueueMessage(JText::sprintf('IMPORT_SUBSCRIBE_CONFIRMATION', $myList->nbusers, '<b><i>'.$myList->name.'</i></b>'));
			}else{
				acymailing_enqueueMessage(JText::sprintf('IMPORT_UNSUBSCRIBE_CONFIRMATION', $myList->nbusers, '<b><i>'.$myList->name.'</i></b>'));
			}
		}
	}

	function _insertUsers($users){
		if(empty($users)) return true;

		$importedCols = array_keys(get_object_vars($users[0]));
		if($this->forceconfirm) $importedCols[] = 'confirmed';
		if($this->importblocked) $importedCols[] = 'enabled';

		foreach($users as $a => $oneUser){
			$this->_checkData($users[$a]);
		}

		$columns = reset($users);
		$colNames = array_keys(get_object_vars($columns));
		$query = 'INSERT'.($this->overwrite ? '' : ' IGNORE').' INTO '.acymailing_table('subscriber').' (`'.implode('`,`', $colNames).'`) VALUES (';
		$values = array();
		$allemails = array();
		foreach($users as $a => $oneUser){
			$value = array();
			$this->dispatcher->trigger('onAcyBeforeUserImport', array(&$oneUser));
			foreach($oneUser as $map => $oneValue){
				if($map == 'enabled' && !empty($this->importblocked) && $this->importblocked == true){
					$value[] = 0;
				}elseif($map != 'subid'){
					$value[] = $this->db->Quote($oneValue);
				}else{
					$value[] = $oneValue;
				}
				if($map == 'email'){
					$allemails[] = $this->db->Quote($oneValue);
				}
			}
			$values[] = implode(',', $value);
		}
		$query .= implode('),(', $values).')';
		if($this->overwrite){
			$query .= ' ON DUPLICATE KEY UPDATE ';
			foreach($importedCols as &$oneColumn){
				$oneColumn = '`'.$oneColumn.'`=VALUES(`'.$oneColumn.'`)';
			}
			$query .= implode(',', $importedCols);
		}

		$this->db->setQuery($query);
		$this->db->query();
		$this->db->setQuery('SELECT subid FROM '.acymailing_table('subscriber').' WHERE email IN ('.implode(',', $allemails).')');

		$this->allSubid = array_merge($this->allSubid, acymailing_loadResultArray($this->db));

		return true;
	}


	function _checkData(&$user){
		if(empty($user->created)){
			$user->created = time();
		}elseif(!is_numeric($user->created)) $user->created = strtotime($user->created);

		if(!isset($user->accept) || strlen($user->accept) == 0) $user->accept = 1;
		if(!isset($user->enabled) || strlen($user->enabled) == 0) $user->enabled = 1;
		if(!isset($user->html) || strlen($user->html) == 0) $user->html = 1;
		if(empty($user->source)) $user->source = 'import';

		if(!empty($user->confirmed_date) && !is_numeric($user->confirmed_date)) $user->confirmed_date = strtotime($user->confirmed_date);
		if(!empty($user->lastclick_date) && !is_numeric($user->lastclick_date)) $user->lastclick_date = strtotime($user->lastclick_date);
		if(!empty($user->lastopen_date) && !is_numeric($user->lastopen_date)) $user->lastopen_date = strtotime($user->lastopen_date);
		if(!empty($user->lastsent_date) && !is_numeric($user->lastsent_date)) $user->lastsent_date = strtotime($user->lastsent_date);


		if(empty($user->name) AND $this->generatename) $user->name = ucwords(trim(str_replace(array('.', '_', '-', 1, 2, 3, 4, 5, 6, 7, 8, 9, 0), ' ', substr($user->email, 0, strpos($user->email, '@')))));

		if((!isset($user->confirmed) || strlen($user->confirmed) == 0) AND $this->forceconfirm) $user->confirmed = 1;

		if(empty($user->key)) $user->key = acymailing_generateKey(14);
	}


	function _autoDetectHeader(){
		$this->separator = ',';

		$this->header = str_replace("\xEF\xBB\xBF", "", $this->header);

		$listSeparators = array("\t", ';', ',');
		foreach($listSeparators as $sep){
			if(strpos($this->header, $sep) !== false){
				$this->separator = $sep;
				break;
			}
		}


		$this->columns = explode($this->separator, $this->header);

		for($i = count($this->columns) - 1; $i >= 0; $i--){
			if(strlen($this->columns[$i]) == 0){
				unset($this->columns[$i]);
				$this->removeSep++;
			}
		}

		$columns = acymailing_getColumns('#__acymailing_subscriber');
		foreach($columns as $i => $oneColumn){
			$columns[strtolower($i)] = $oneColumn;
		}

		foreach($this->columns as $i => $oneColumn){
			$this->columns[$i] = strtolower(trim($oneColumn, '\'" '));
			if(in_array($this->columns[$i], array('listids', 'listname'))) continue;
			if(!isset($columns[$this->columns[$i]]) && $this->columns[$i] != 1){
				acymailing_enqueueMessage(JText::sprintf('IMPORT_ERROR_FIELD', '<b><i>'.htmlspecialchars($this->columns[$i], ENT_COMPAT, 'UTF-8').'</i></b>', implode(' | ', array_diff(array_keys($columns), array('subid', 'userid', 'key')))), 'error');
				return false;
			}
		}

		if(!in_array('email', $this->columns)) return false;

		return true;
	}

	function joomla(){
		$query = 'UPDATE IGNORE '.acymailing_table('users', false).' as b, '.acymailing_table('subscriber').' as a SET a.email = b.email, a.name = b.name, a.enabled = 1 - b.block WHERE a.userid = b.id AND a.userid > 0';
		$this->db->setQuery($query);
		$this->db->query();
		$nbUpdated = $this->db->getAffectedRows();

		$query = 'UPDATE IGNORE '.acymailing_table('users', false).' as b, '.acymailing_table('subscriber').' as a SET a.userid = b.id WHERE a.email = b.email';
		$this->db->setQuery($query);
		$this->db->query();
		$nbUpdated += $this->db->getAffectedRows();

		acymailing_enqueueMessage(JText::sprintf('IMPORT_UPDATE', $nbUpdated));

		$query = 'SELECT subid FROM '.acymailing_table('subscriber').' as a LEFT JOIN '.acymailing_table('users', false).' as b on a.userid = b.id WHERE b.id IS NULL AND a.userid > 0';
		$this->db->setQuery($query);
		$deletedSubid = acymailing_loadResultArray($this->db);

		$query = 'SELECT subid FROM '.acymailing_table('subscriber').' as a LEFT JOIN '.acymailing_table('users', false).' as b on a.email = b.email WHERE b.id IS NULL AND a.userid > 0';
		$this->db->setQuery($query);
		$deletedSubid = array_merge(acymailing_loadResultArray($this->db), $deletedSubid);

		if(!empty($deletedSubid)){
			$userClass = acymailing_get('class.subscriber');
			$deletedUsers = $userClass->delete($deletedSubid);
			acymailing_enqueueMessage(JText::sprintf('IMPORT_DELETE', $deletedUsers));
		}

		$time = time();
		$query = 'INSERT IGNORE INTO '.acymailing_table('subscriber').' (`email`,`name`,`confirmed`,`userid`,`created`,`enabled`,`accept`,`html`) SELECT `email`,`name`,1-`block`,`id`,UNIX_TIMESTAMP(`registerDate`),1-`block`,1,1 FROM '.acymailing_table('users', false);
		$this->db->setQuery($query);
		$this->db->query();
		$insertedUsers = $this->db->getAffectedRows();

		acymailing_enqueueMessage(JText::sprintf('IMPORT_NEW', $insertedUsers));

		$lists = $this->getImportedLists();
		$listsSubscribe = array();
		foreach($lists as $listid => $val){
			if(!empty($val)) $listsSubscribe[] = (int)$listid;
		}

		if(empty($listsSubscribe)) return true;

		if(acymailing_level(3)){
			$listClass = acymailing_get('class.list');
			$campaignClass = acymailing_get('helper.campaign');
			$listCampaign = $listClass->getCampaigns(array_keys($lists));
			foreach($lists as $listid => $val){
				if($val == 2 && !empty($listCampaign[$listid])){
					$query = 'SELECT sub.subid FROM #__acymailing_subscriber sub LEFT JOIN #__acymailing_listsub list ON sub.subid=list.subid AND list.listid='.intval($listid).' WHERE list.subid IS NULL AND sub.userid > 0 ';
					$this->db->setQuery($query);
					$listSubidNotInList = acymailing_loadResultArray($this->db);
					if(empty($listSubidNotInList)) continue;
					foreach($listCampaign[$listid] as $campaignId){
						$campaignClass->autoSubCampaign($listSubidNotInList, $campaignId);
					}
				}
			}
		}

		$query = 'INSERT IGNORE INTO '.acymailing_table('listsub').' (`listid`,`subid`,`subdate`,`status`) ';
		$query .= 'SELECT a.`listid`, b.`subid` ,'.$time.',1 FROM '.acymailing_table('list').' as a, '.acymailing_table('subscriber').' as b  WHERE a.`listid` IN ('.implode(',', $listsSubscribe).') AND b.`userid` > 0';
		$this->db->setQuery($query);
		$this->db->query();
		$nbsubscribed = $this->db->getAffectedRows();
		acymailing_enqueueMessage(JText::sprintf('IMPORT_SUBSCRIPTION', $nbsubscribed));

		return true;
	}

	function acajoom(){
		$query = 'INSERT IGNORE INTO '.acymailing_table('subscriber').' (email,name,confirmed,created,enabled,accept,html) SELECT email,name,confirmed,UNIX_TIMESTAMP(`subscribe_date`),1-blacklist,1,receive_html FROM '.acymailing_table('acajoom_subscribers', false);
		$this->db->setQuery($query);
		$this->db->query();
		$insertedUsers = $this->db->getAffectedRows();

		acymailing_enqueueMessage(JText::sprintf('IMPORT_NEW', $insertedUsers));

		if(JRequest::getInt('acajoom_lists', 0) == 1) $this->_importAcajoomLists();

		$query = 'SELECT b.subid FROM '.acymailing_table('acajoom_subscribers', false).' as a JOIN '.acymailing_table('subscriber').' as b on a.email = b.email';
		$this->db->setQuery($query);
		$this->allSubid = acymailing_loadResultArray($this->db);
		$this->_subscribeUsers();
		$this->_displaySubscribedResult();

		return true;
	}

	function _importYancLists(){
		$query = 'SELECT `id`, `name`, `description`, `state` as `published` FROM `#__yanc_letters`';
		$this->db->setQuery($query);
		$yancLists = $this->db->loadObjectList('id');
		$user = JFactory::getUser();

		$query = 'SELECT `listid`, `alias` FROM '.acymailing_table('list').' WHERE `alias` IN (\'yanclist'.implode('\',\'yanclist', array_keys($yancLists)).'\')';
		$this->db->setQuery($query);
		$joomLists = $this->db->loadObjectList('alias');

		$listClass = acymailing_get('class.list');
		$time = time();

		foreach($yancLists as $oneList){
			$oneList->alias = 'yanclist'.$oneList->id;
			$oneList->userid = $user->id;

			$yancListId = $oneList->id;
			if(isset($joomLists[$oneList->alias])){
				$joomListId = $joomLists[$oneList->alias]->listid;
			}else{
				unset($oneList->id);
				$joomListId = $listClass->save($oneList);
				acymailing_enqueueMessage(JText::sprintf('IMPORT_LIST', '<b><i>'.$oneList->name.'</i></b>'));
			}

			$querySelect = 'SELECT DISTINCT c.subid,'.$joomListId.','.$time.',1 FROM `#__yanc_subscribers` as a ';
			$querySelect .= 'JOIN '.acymailing_table('subscriber').' as c on a.email = c.email ';
			$querySelect .= 'WHERE a.lid = '.$yancListId.' AND a.state = 1 AND c.subid > 0';
			$queryInsert = 'INSERT IGNORE INTO '.acymailing_table('listsub').' (subid,listid,subdate,status) ';

			$this->db->setQuery($queryInsert.$querySelect);
			$this->db->query();

			acymailing_enqueueMessage(JText::sprintf('IMPORT_SUBSCRIBE_CONFIRMATION', $this->db->getAffectedRows(), '<b><i>'.$oneList->name.'</i></b>'));
		}

		return true;
	}

	private function _importccNewsletterNews(){
		$replacements = array();
		$replacements['[unsubscribe link]'] = '{unsubscribe}'.JText::_('UNSUBSCRIBE').'{/unsubscribe}';
		$replacements['[view online link]'] = '{readonline}'.JText::_('VIEW_ONLINE').'{/readonline}';
		$replacements['[sitename]'] = '{config:sitename}';
		$replacements['[name]'] = '{subtag:name}';

		$fields = array();
		$fields['groupid'] = '`groupid`';

		$fields['subject'] = '`name`';
		$fields['body'] = '`body`';
		$fields['published'] = '`enabled`';
		$fields['senddate'] = 'UNIX_TIMESTAMP(`lastsentdate`)';
		$fields['type'] = '"news"';
		$fields['visible'] = '1';
		$fields['html'] = '1';


		$query = 'SELECT ';
		foreach($fields as $as => $select){
			$query .= $select.' as '.$as.',';
		}
		$query = rtrim($query, ',');
		$query .= ' FROM #__ccnewsletter_newsletters WHERE `enabled` >= 0';
		$this->db->setQuery($query);
		$ccNewsletters = $this->db->loadObjectList();

		if(empty($ccNewsletters)) return true;

		$mailClass = acymailing_get('class.mail');
		$lists = array();
		foreach($ccNewsletters as $oneNewsletter){
			$ccList = $oneNewsletter->groupid;
			unset($oneNewsletter->groupid);

			$oneNewsletter->subject = str_replace(array_keys($replacements), $replacements, $oneNewsletter->subject);
			$oneNewsletter->body = str_replace(array_keys($replacements), $replacements, $oneNewsletter->body);
			$acyId = $mailClass->save($oneNewsletter);
			$lists[$acyId] = 'ccnewsletterlist'.$ccList;
		}

		acymailing_enqueueMessage(JText::sprintf('NB_IMPORT_NEWSLETTER', '<b>'.count($lists).'</b>'));

		$query = 'SELECT listid, alias FROM #__acymailing_list WHERE alias LIKE "ccnewsletterlist%"';
		$this->db->setQuery($query);
		$acylists = $this->db->loadObjectList('alias');

		$equ = array();
		foreach($lists as $mailid => $cclist){
			if(empty($acylists[$cclist])) continue;
			$equ[] = $mailid.','.$acylists[$cclist]->listid;
		}

		if(empty($equ)) return true;
		$query = 'INSERT IGNORE INTO #__acymailing_listmail (`mailid`, `listid`) VALUES ('.implode('),(', $equ).')';
		$this->db->setQuery($query);
		$this->db->query();

		return true;
	}

	private function _importccNewsletterLists(){
		$query = 'SELECT `id`, `group_name` as `name`, `public` as `visible`, `enabled` as `published` FROM '.acymailing_table('ccnewsletter_groups', false).' ORDER BY `ordering` ASC';
		$this->db->setQuery($query);
		$compLists = $this->db->loadObjectList('id');

		$query = 'SELECT `listid`, `alias` FROM '.acymailing_table('list').' WHERE `alias` IN (\'ccnewsletterlist'.implode('\',\'ccnewsletterlist', array_keys($compLists)).'\')';
		$this->db->setQuery($query);
		$joomLists = $this->db->loadObjectList('alias');

		$listClass = acymailing_get('class.list');

		foreach($compLists as $oneList){
			$oneList->alias = 'ccnewsletterlist'.$oneList->id;
			$compListId = $oneList->id;
			if(isset($joomLists[$oneList->alias])){
				$joomListId = $joomLists[$oneList->alias]->listid;
			}else{
				unset($oneList->id);
				$joomListId = $listClass->save($oneList);
				acymailing_enqueueMessage(JText::sprintf('IMPORT_LIST', '<b><i>'.$oneList->name.'</i></b>'));
			}

			$querySelect = 'SELECT DISTINCT c.subid,'.$joomListId.',UNIX_TIMESTAMP(b.`sdate`),1 FROM '.acymailing_table('ccnewsletter_g_to_s', false).' as a ';
			$querySelect .= 'JOIN '.acymailing_table('ccnewsletter_subscribers', false).' as b on a.subscriber_id = b.id ';
			$querySelect .= 'JOIN '.acymailing_table('subscriber').' as c on b.email = c.email ';
			$querySelect .= 'WHERE a.group_id = '.$compListId.' AND c.subid > 0';
			$queryInsert = 'INSERT IGNORE INTO '.acymailing_table('listsub').' (subid,listid,subdate,status) ';

			$this->db->setQuery($queryInsert.$querySelect);
			$this->db->query();

			acymailing_enqueueMessage(JText::sprintf('IMPORT_SUBSCRIBE_CONFIRMATION', $this->db->getAffectedRows(), '<b><i>'.$oneList->name.'</i></b>'));
		}

		return true;
	}

	private function _importjnewsNews(){
		$replacements = array();
		$replacements['#{tag:unsubscribe}#i'] = '{unsubscribe}'.JText::_('UNSUBSCRIBE').'{/unsubscribe}';
		$replacements['#{tag:subscriptions}#i'] = '{modify}'.JText::_('MODIFY_SUBSCRIPTION').'{/modify}';
		$replacements['#{tag:viewonline[^}]*}#i'] = '{readonline}'.JText::_('VIEW_ONLINE').'{/readonline}';
		$replacements['#{tag:confirm}#i'] = '{confirm}'.JText::_('CONFIRM_SUBSCRIPTION').'{/confirm}';
		$replacements['#{tag:firstname}#i'] = '{subtag:name|part:first}';
		$replacements['#{tag:name}#i'] = '{subtag:name}';
		$replacements['#{tag:email}#i'] = '{subtag:email}';
		$replacements['#{tag:title}#i'] = '{mail:subject}';
		$replacements['#{tag:issuenb}#i'] = '{mail:mailid}';

		$fields = array();
		$fields['id'] = '`id`';
		$fields['subject'] = '`subject`';
		$fields['body'] = '`htmlcontent`';
		$fields['altbody'] = '`textonly`';
		$fields['published'] = '`published`';
		$fields['senddate'] = '`send_date`';
		$fields['created'] = '`createdate`';
		$fields['userid'] = '`author_id`';
		$fields['type'] = '"news"';
		$fields['visible'] = '`visible`';
		$fields['html'] = '`html`';

		$query = 'SELECT ';
		foreach($fields as $as => $select){
			$query .= $select.' as '.$as.',';
		}
		$query = rtrim($query, ',');
		$query .= ' FROM #__jnews_mailings WHERE `mailing_type` = 1';
		$this->db->setQuery($query);
		$jnewsNewsletters = $this->db->loadObjectList();

		if(empty($jnewsNewsletters)) return true;

		$mailClass = acymailing_get('class.mail');
		$mailids = array();
		foreach($jnewsNewsletters as $oneNewsletter){
			$jnewsid = $oneNewsletter->id;
			unset($oneNewsletter->id);

			$oneNewsletter->published = min($oneNewsletter->published, 1);
			$oneNewsletter->subject = preg_replace(array_keys($replacements), $replacements, $oneNewsletter->subject);
			$oneNewsletter->body = preg_replace(array_keys($replacements), $replacements, $oneNewsletter->body);
			$mailids[$jnewsid] = $mailClass->save($oneNewsletter);
		}

		acymailing_enqueueMessage(JText::sprintf('NB_IMPORT_NEWSLETTER', '<b>'.count($mailids).'</b>'));

		$query = 'SELECT listid, alias FROM #__acymailing_list WHERE alias LIKE "jnewslist%"';
		$this->db->setQuery($query);
		$acylists = $this->db->loadObjectList('alias');

		$query = 'SELECT list_id,mailing_id FROM #__jnews_listmailings WHERE mailing_id IN ('.implode(',', array_keys($mailids)).')';
		$this->db->setQuery($query);
		$jnewslistmailings = $this->db->loadObjectList();

		$equ = array();
		foreach($jnewslistmailings as $jnewsids){
			if(empty($acylists['jnewslist'.$jnewsids->list_id])) continue;
			if(empty($mailids[$jnewsids->mailing_id])) continue;
			$equ[] = $mailids[$jnewsids->mailing_id].','.$acylists['jnewslist'.$jnewsids->list_id]->listid;
		}

		if(empty($equ)) return true;
		$query = 'INSERT IGNORE INTO #__acymailing_listmail (`mailid`, `listid`) VALUES ('.implode('),(', $equ).')';
		$this->db->setQuery($query);
		$this->db->query();

		return true;
	}

	private function _importjnewsLists(){
		$query = 'SELECT `id`, `list_name` as `name`, `hidden` as `visible`, `list_desc` as `description`, `published`, `owner` as `userid` FROM '.acymailing_table('jnews_lists', false);
		$this->db->setQuery($query);
		$jnewsLists = $this->db->loadObjectList('id');

		$query = 'SELECT `listid`, `alias` FROM '.acymailing_table('list').' WHERE `alias` IN (\'jnewslist'.implode('\',\'jnewslist', array_keys($jnewsLists)).'\')';
		$this->db->setQuery($query);
		$joomLists = $this->db->loadObjectList('alias');

		$listClass = acymailing_get('class.list');

		foreach($jnewsLists as $oneList){
			$oneList->alias = 'jnewslist'.$oneList->id;
			$jnewsListId = $oneList->id;
			if(isset($joomLists[$oneList->alias])){
				$joomListId = $joomLists[$oneList->alias]->listid;
			}else{
				unset($oneList->id);
				$joomListId = $listClass->save($oneList);
				acymailing_enqueueMessage(JText::sprintf('IMPORT_LIST', '<b><i>'.$oneList->name.'</i></b>'));
			}

			$querySelect = 'SELECT DISTINCT c.subid,'.$joomListId.',a.subdate,a.unsubdate,1-(2*a.unsubscribe) FROM '.acymailing_table('jnews_listssubscribers', false).' as a ';
			$querySelect .= 'JOIN '.acymailing_table('jnews_subscribers', false).' as b on a.subscriber_id = b.id ';
			$querySelect .= 'JOIN '.acymailing_table('subscriber').' as c on b.email = c.email ';
			$querySelect .= 'WHERE a.list_id = '.$jnewsListId.' AND c.subid > 0';
			$queryInsert = 'INSERT IGNORE INTO '.acymailing_table('listsub').' (subid,listid,subdate,unsubdate,status) ';

			$this->db->setQuery($queryInsert.$querySelect);
			$this->db->query();

			acymailing_enqueueMessage(JText::sprintf('IMPORT_SUBSCRIBE_CONFIRMATION', $this->db->getAffectedRows(), '<b><i>'.$oneList->name.'</i></b>'));
		}

		return true;
	}

	private function _importAcajoomLists(){
		$query = 'SELECT `id`, `list_name` as `name`, `hidden` as `visible`, `list_desc` as `description`, `published`, `owner` as `userid` FROM '.acymailing_table('acajoom_lists', false);
		$this->db->setQuery($query);
		$acaLists = $this->db->loadObjectList('id');

		$query = 'SELECT `listid`, `alias` FROM '.acymailing_table('list').' WHERE `alias` IN (\'acajoomlist'.implode('\',\'acajoomlist', array_keys($acaLists)).'\')';
		$this->db->setQuery($query);
		$joomLists = $this->db->loadObjectList('alias');

		$listClass = acymailing_get('class.list');
		$time = time();

		foreach($acaLists as $oneList){
			$oneList->alias = 'acajoomlist'.$oneList->id;
			$acaListId = $oneList->id;
			if(isset($joomLists[$oneList->alias])){
				$joomListId = $joomLists[$oneList->alias]->listid;
			}else{
				unset($oneList->id);
				$joomListId = $listClass->save($oneList);
				acymailing_enqueueMessage(JText::sprintf('IMPORT_LIST', '<b><i>'.$oneList->name.'</i></b>'));
			}

			$querySelect = 'SELECT DISTINCT c.subid,'.$joomListId.','.$time.',1 FROM '.acymailing_table('acajoom_queue', false).' as a ';
			$querySelect .= 'JOIN '.acymailing_table('acajoom_subscribers', false).' as b on a.subscriber_id = b.id ';
			$querySelect .= 'JOIN '.acymailing_table('subscriber').' as c on b.email = c.email ';
			$querySelect .= 'WHERE a.list_id = '.$acaListId.' AND c.subid > 0';
			$queryInsert = 'INSERT IGNORE INTO '.acymailing_table('listsub').' (subid,listid,subdate,status) ';

			$this->db->setQuery($queryInsert.$querySelect);
			$this->db->query();

			acymailing_enqueueMessage(JText::sprintf('IMPORT_SUBSCRIBE_CONFIRMATION', $this->db->getAffectedRows(), '<b><i>'.$oneList->name.'</i></b>'));
		}

		return true;
	}

	function letterman(){
		$time = time();
		$query = 'INSERT IGNORE INTO '.acymailing_table('subscriber').' (`email`,`name`,`confirmed`,`created`,`enabled`,`accept`,`html`) SELECT `subscriber_email`,`subscriber_name`,`confirmed`,UNIX_TIMESTAMP(`subscribe_date`),1,1,1 FROM '.acymailing_table('letterman_subscribers', false);
		$this->db->setQuery($query);
		$this->db->query();
		$insertedUsers = $this->db->getAffectedRows();

		if($insertedUsers == -1){
			$query = 'INSERT IGNORE INTO '.acymailing_table('subscriber').' (`email`,`name`,`confirmed`,`created`,`enabled`,`accept`,`html`) SELECT `email`,`name`,`confirmed`,'.$time.',1,1,1 FROM '.acymailing_table('letterman_subscribers', false);
			$this->db->setQuery($query);
			$this->db->query();
			$insertedUsers = $this->db->getAffectedRows();
			$query = 'SELECT b.subid FROM '.acymailing_table('letterman_subscribers', false).' as a JOIN '.acymailing_table('subscriber').' as b on a.email = b.email';
			$this->db->setQuery($query);
		}else{
			$query = 'SELECT b.subid FROM '.acymailing_table('letterman_subscribers', false).' as a JOIN '.acymailing_table('subscriber').' as b on a.subscriber_email = b.email';
			$this->db->setQuery($query);
		}

		acymailing_enqueueMessage(JText::sprintf('IMPORT_NEW', $insertedUsers));

		$this->allSubid = acymailing_loadResultArray($this->db);
		$this->_subscribeUsers();
		$this->_displaySubscribedResult();

		return true;
	}

	function yanc(){
		$this->db->setQuery('SELECT * FROM #__yanc_subscribers LIMIT 1');
		$oneSubscriber = $this->db->loadObject();
		if(!isset($oneSubscriber->state)){
			$this->db->setQuery("ALTER IGNORE TABLE `#__yanc_subscribers` ADD `state` INT NOT NULL DEFAULT '1'");
			$this->db->query();
		}

		$query = 'INSERT IGNORE INTO '.acymailing_table('subscriber').' (`email`,`name`,`confirmed`,`created`,`enabled`,`accept`,`html`, `ip`) SELECT `email`,`name`,`confirmed`,UNIX_TIMESTAMP(`date`),`state`,1,`html`,`ip` FROM '.acymailing_table('yanc_subscribers', false)." WHERE email LIKE '%@%'";
		$this->db->setQuery($query);
		$this->db->query();
		$insertedUsers = $this->db->getAffectedRows();

		acymailing_enqueueMessage(JText::sprintf('IMPORT_NEW', $insertedUsers));

		if(JRequest::getInt('yanc_lists', 0) == 1) $this->_importYancLists();

		$query = 'SELECT b.subid FROM '.acymailing_table('yanc_subscribers', false).' as a JOIN '.acymailing_table('subscriber').' as b on a.email = b.email';
		$this->db->setQuery($query);
		$this->allSubid = acymailing_loadResultArray($this->db);
		$this->_subscribeUsers();
		$this->_displaySubscribedResult();

		return true;
	}


	function vemod(){
		$time = time();
		$query = "INSERT IGNORE INTO ".acymailing_table('subscriber')." (`email`,`name`,`confirmed`,`created`,`enabled`,`accept`,`html`) SELECT `email`,`name`,1,'.$time.',1,1,`mailformat` FROM `#__vemod_news_mailer_users` WHERE `email` LIKE '%@%' ";
		$this->db->setQuery($query);
		$this->db->query();
		$insertedUsers = $this->db->getAffectedRows();

		acymailing_enqueueMessage(JText::sprintf('IMPORT_NEW', $insertedUsers));

		$query = 'SELECT b.subid FROM `#__vemod_news_mailer_users` as a JOIN '.acymailing_table('subscriber').' as b on a.email = b.email';
		$this->db->setQuery($query);
		$this->allSubid = acymailing_loadResultArray($this->db);
		$this->_subscribeUsers();
		$this->_displaySubscribedResult();

		return true;
	}

	function contact(){
		$time = time();
		$query = 'INSERT IGNORE INTO '.acymailing_table('subscriber')." (`email`,`name`,`confirmed`,`created`,`enabled`,`accept`,`html`) SELECT `email_to`,`name`,1,'.$time.',1,1,1 FROM `#__contact_details` WHERE email_to LIKE '%@%'";
		$this->db->setQuery($query);
		$this->db->query();
		$insertedUsers = $this->db->getAffectedRows();

		acymailing_enqueueMessage(JText::sprintf('IMPORT_NEW', $insertedUsers));

		$query = 'SELECT b.subid FROM `#__contact_details` as a JOIN '.acymailing_table('subscriber').' as b on a.email_to = b.email';
		$this->db->setQuery($query);
		$this->allSubid = acymailing_loadResultArray($this->db);
		$this->_subscribeUsers();
		$this->_displaySubscribedResult();

		return true;
	}

	function ccnewsletter(){
		$ccfields = acymailing_getColumns('#__ccnewsletter_subscribers');

		$fields = array();
		$fields['email'] = '`email`';
		$fields['name'] = '`name`';
		$fields['confirmed'] = '`enabled`';
		$fields['created'] = 'UNIX_TIMESTAMP(`sdate`)';
		$fields['enabled'] = '`enabled`';
		$fields['accept'] = 1;
		$fields['html'] = isset($ccfields['plainText']) ? '1-`plainText`' : 1;

		$query = 'INSERT IGNORE INTO '.acymailing_table('subscriber').' (`'.implode('`,`', array_keys($fields)).'`) SELECT '.implode(',', $fields).' FROM '.acymailing_table('ccnewsletter_subscribers', false);
		$this->db->setQuery($query);
		$this->db->query();
		$insertedUsers = $this->db->getAffectedRows();


		acymailing_enqueueMessage(JText::sprintf('IMPORT_NEW', $insertedUsers));

		if(JRequest::getInt('ccNewsletter_lists', 0) == 1) $this->_importccNewsletterLists();
		if(JRequest::getInt('ccNewsletter_news', 0) == 1) $this->_importccNewsletterNews();


		$query = 'SELECT b.subid FROM '.acymailing_table('ccnewsletter_subscribers', false).' as a JOIN '.acymailing_table('subscriber').' as b on a.email = b.email WHERE b.subid > 0';
		$this->db->setQuery($query);
		$this->allSubid = acymailing_loadResultArray($this->db);
		$this->_subscribeUsers();
		$this->_displaySubscribedResult();

		return true;
	}

	function jnews(){
		$time = time();
		$query = 'INSERT IGNORE INTO '.acymailing_table('subscriber').' (`email`,`name`,`confirmed`,`created`,`enabled`,`accept`,`html`) SELECT `email`,`name`,`confirmed`,`subscribe_date`, 1-`blacklist`,1,`receive_html` FROM '.acymailing_table('jnews_subscribers', false);
		$this->db->setQuery($query);
		$this->db->query();
		$insertedUsers = $this->db->getAffectedRows();

		acymailing_enqueueMessage(JText::sprintf('IMPORT_NEW', $insertedUsers));

		if(JRequest::getInt('jnews_lists', 0) == 1) $this->_importjnewsLists();
		if(JRequest::getInt('jnews_news', 0) == 1) $this->_importjnewsNews();

		$query = 'SELECT b.subid FROM '.acymailing_table('jnews_subscribers', false).' as a JOIN '.acymailing_table('subscriber').' as b on a.email = b.email';
		$this->db->setQuery($query);
		$this->allSubid = acymailing_loadResultArray($this->db);
		$this->_subscribeUsers();
		$this->_displaySubscribedResult();

		return true;
	}

	function nspro(){
		$time = time();
		$query = 'INSERT IGNORE INTO '.acymailing_table('subscriber').' (`email`,`name`,`confirmed`,`created`,`enabled`,`accept`,`html`) SELECT `email`,`name`,`confirmed`,UNIX_TIMESTAMP(`datetime`), 1,1,1 FROM '.acymailing_table('nspro_subs', false);
		$this->db->setQuery($query);
		$this->db->query();
		$insertedUsers = $this->db->getAffectedRows();

		acymailing_enqueueMessage(JText::sprintf('IMPORT_NEW', $insertedUsers));

		if(JRequest::getInt('nspro_lists', 0) == 1) $this->_importnsproLists();

		$query = 'SELECT b.subid FROM '.acymailing_table('nspro_subs', false).' as a JOIN '.acymailing_table('subscriber').' as b on a.email = b.email';
		$this->db->setQuery($query);
		$this->allSubid = acymailing_loadResultArray($this->db);
		$this->_subscribeUsers();
		$this->_displaySubscribedResult();

		return true;
	}

	private function _importnsproLists(){
		$my = JFactory::getUser();

		$query = 'SELECT `id`, `lname` as `name`, 1 as `visible`, `notes` as `description`, `published`, '.intval($my->id).' as `userid` FROM '.acymailing_table('nspro_lists', false);
		$this->db->setQuery($query);
		$nsprolists = $this->db->loadObjectList('id');

		$query = 'SELECT `listid`, `alias` FROM '.acymailing_table('list').' WHERE `alias` IN (\'nsprolist'.implode('\',\'nsprolist', array_keys($nsprolists)).'\')';
		$this->db->setQuery($query);
		$joomLists = $this->db->loadObjectList('alias');

		$listClass = acymailing_get('class.list');

		foreach($nsprolists as $oneList){
			$oneList->alias = 'nsprolist'.$oneList->id;
			$nsproListId = $oneList->id;
			if(isset($joomLists[$oneList->alias])){
				$joomListId = $joomLists[$oneList->alias]->listid;
			}else{
				unset($oneList->id);
				$joomListId = $listClass->save($oneList);
				acymailing_enqueueMessage(JText::sprintf('IMPORT_LIST', '<b><i>'.$oneList->name.'</i></b>'));
			}

			$querySelect = 'SELECT DISTINCT c.subid,'.$joomListId.',c.created,1 FROM '.acymailing_table('nspro_subs', false).' as a ';
			$querySelect .= 'JOIN '.acymailing_table('subscriber').' as c on a.email = c.email ';
			$querySelect .= 'WHERE a.mailing_lists LIKE "'.$nsproListId.'" OR a.mailing_lists LIKE "%,'.$nsproListId.',%" OR a.mailing_lists LIKE "'.$nsproListId.',%"  OR a.mailing_lists LIKE "%,'.$nsproListId.'"';
			$queryInsert = 'INSERT IGNORE INTO '.acymailing_table('listsub').' (subid,listid,subdate,status) ';

			$this->db->setQuery($queryInsert.$querySelect);
			$this->db->query();

			acymailing_enqueueMessage(JText::sprintf('IMPORT_SUBSCRIBE_CONFIRMATION', $this->db->getAffectedRows(), '<b><i>'.$oneList->name.'</i></b>'));
		}

		return true;
	}

	function communicator(){
		$time = time();
		$query = 'INSERT IGNORE INTO '.acymailing_table('subscriber').' (`email`,`name`,`confirmed`,`created`,`enabled`,`accept`,`html`) SELECT `subscriber_email`,`subscriber_name`,`confirmed`,'.$time.',1,1,1 FROM '.acymailing_table('communicator_subscribers', false);
		$this->db->setQuery($query);
		$this->db->query();
		$insertedUsers = $this->db->getAffectedRows();

		acymailing_enqueueMessage(JText::sprintf('IMPORT_NEW', $insertedUsers));

		$query = 'SELECT b.subid FROM '.acymailing_table('communicator_subscribers', false).' as a JOIN '.acymailing_table('subscriber').' as b on a.subscriber_email = b.email';
		$this->db->setQuery($query);
		$this->allSubid = acymailing_loadResultArray($this->db);
		$this->_subscribeUsers();
		$this->_displaySubscribedResult();

		return true;
	}

	function civi_import(){
		$this->setciviprefix();
		$query = 'INSERT IGNORE INTO '.acymailing_table('subscriber').' (`email`,`name`,`confirmed`,`created`,`enabled`,`accept`,`html`) ';
		$query .= 'SELECT CONVERT(civiemail.email USING utf8),CONVERT(civicontact.`first_name` USING utf8),1,'.time().', 1-`do_not_email`,1 - civicontact.is_opt_out,1 ';
		$query .= 'FROM '.$this->civiprefix.'email as civiemail JOIN '.$this->civiprefix.'contact as civicontact ON civicontact.id = civiemail.contact_id ';
		$query .= 'WHERE civicontact.is_deleted = 0 AND civiemail.is_primary = 1 AND civiemail.email LIKE \'%@%\'';
		$this->db->setQuery($query);
		$this->db->query();
		return $this->db->getAffectedRows();
	}

	function setciviprefix(){
		if(!empty($this->civiprefix)) return;
		$this->civiprefix = 'civicrm_';
		$civifile = ACYMAILING_ROOT.'administrator'.DS.'components'.DS.'com_civicrm'.DS.'civicrm.settings.php';
		if(!defined('CIVICRM_DSN') && file_exists($civifile)) include_once($civifile);
		if(defined('CIVICRM_DSN')){
			$infos = parse_url(CIVICRM_DSN);
			$db = trim($infos['path'], '/');
			if(!empty($db)) $this->civiprefix = '`'.$db.'`.civicrm_';
		}
	}

	function civi(){
		$this->setciviprefix();

		$insertedUsers = $this->civi_import();
		acymailing_enqueueMessage(JText::sprintf('IMPORT_NEW', $insertedUsers));

		$query = 'SELECT b.subid FROM '.$this->civiprefix.'email as a JOIN '.acymailing_table('subscriber').' as b on CONVERT(a.email USING utf8) = b.email';
		$this->db->setQuery($query);
		$this->allSubid = acymailing_loadResultArray($this->db);
		$this->_subscribeUsers();
		$this->_displaySubscribedResult();
	}

	function ldap(){
		$config = acymailing_config();

		$db = JFactory::getDBO();
		$db->setQuery("DELETE FROM #__acymailing_config WHERE namekey LIKE 'ldapfield_%'");
		$db->query();

		if(!$this->ldap_init()) return false;

		$ldapfields = JRequest::getVar('ldapfield');
		if(empty($ldapfields)){
			acymailing_enqueueMessage(JText::_('SPECIFYFIELDEMAIL'), 'notice');
			return false;
		}

		$newConfig = new stdClass();

		$this->dispresults = false;
		$newConfig->ldap_import_confirm = $this->forceconfirm = JRequest::getInt('ldap_import_confirm');
		$newConfig->ldap_generatename = $this->generatename = JRequest::getInt('ldap_generatename');
		$newConfig->ldap_overwriteexisting = $this->overwrite = JRequest::getInt('ldap_overwriteexisting');
		$newConfig->ldap_deletenotexists = $this->ldap_deletenotexists = JRequest::getInt('ldap_deletenotexists');
		if($this->ldap_deletenotexists){
			$subfields = array_keys(acymailing_getColumns('#__acymailing_subscriber'));
			if(!in_array('ldapentry', $subfields)){
				$db->setQuery("ALTER TABLE #__acymailing_subscriber ADD COLUMN ldapentry TINYINT UNSIGNED DEFAULT 0");
				$db->query();
			}else{
				$db->setQuery("UPDATE #__acymailing_subscriber SET ldapentry = 0");
				$db->query();
			}

			$this->overwrite = 1;
		}
		$newConfig->ldap_subfield = $this->ldap_subfield = JRequest::getString('ldap_subfield');
		if(!empty($this->ldap_subfield)){
			$allValues = JRequest::getVar('ldap_subcond');
			$allLists = JRequest::getVar('ldap_sublists');
			$this->ldap_subscribe = array();
			foreach($allValues as $i => $oneValue){
				$oneValue = strtolower(trim($oneValue));
				if(strlen($oneValue) < 1) continue;
				if(isset($this->ldap_subscribe[$oneValue])){
					$this->ldap_subscribe[$oneValue] .= '-'.intval($allLists[$i]);
				}else{
					$this->ldap_subscribe[$oneValue] = intval($allLists[$i]);
				}
				$valcond = 'ldap_subcond_'.$i;
				$vallist = 'ldap_sublists_'.$i;
				$newConfig->$valcond = $allValues[$i];
				$newConfig->$vallist = $allLists[$i];
			}

			$db->setQuery("DELETE FROM #__acymailing_config WHERE namekey LIKE 'ldap_subcond%' OR namekey LIKE 'ldap_sublists%'");
			$db->query();
		}

		$this->ldap_equivalent = array();
		$this->ldap_selectedFields = array();
		foreach($ldapfields as $oneField => $acyField){
			if(empty($acyField)) continue;
			$configname = 'ldapfield_'.strtolower($oneField);
			$newConfig->$configname = $acyField;
			$this->ldap_equivalent[$acyField] = $oneField;
			$this->ldap_selectedFields[] = $oneField;
		}

		if(!empty($this->ldap_subfield) AND !in_array($this->ldap_subfield, $this->ldap_selectedFields)){
			$this->ldap_selectedFields[] = $this->ldap_subfield;
		}

		$config->save($newConfig);

		if(empty($this->ldap_equivalent['email'])){
			acymailing_enqueueMessage(JText::_('SPECIFYFIELDEMAIL'), 'notice');
			return false;
		}

		$startChars = 'abcdefghijklmnopqrstuvwxyz0123456789_-+&.';

		$nbChars = strlen($startChars);
		$result = true;
		for($i = 0; $i < $nbChars; $i++){
			if(!$this->ldap_import($this->ldap_equivalent['email'].'='.$startChars[$i].'*@*')) $result = false;
		}

		acymailing_enqueueMessage(JText::sprintf('ACY_IMPORT_REPORT', $this->totalTry, $this->totalInserted, $this->totalTry - $this->totalValid, $this->totalValid - $this->totalInserted));

		if($this->ldap_deletenotexists){
			$db->setQuery("SELECT subid FROM #__acymailing_subscriber WHERE ldapentry = 0");
			$allSubids = acymailing_loadResultArray($db);
			$subscriberClass = acymailing_get('class.subscriber');
			$nbAffected = $subscriberClass->delete($allSubids);
			acymailing_enqueueMessage(JText::sprintf('IMPORT_DELETE', $nbAffected));
			$db->setQuery("ALTER TABLE #__acymailing_subscriber DROP COLUMN ldapentry");
			$db->query();
		}

		$this->_displaySubscribedResult();

		return $result;
	}

	function ldap_import($search){
		$searchResult = ldap_search($this->ldap_conn, $this->ldap_basedn, $search, $this->ldap_selectedFields);
		if(!$searchResult){
			acymailing_display('Could not search for elements<br />'.ldap_error($this->ldap_conn), 'warning');
			return false;
		}
		$entries = ldap_get_entries($this->ldap_conn, $searchResult);

		if(empty($entries) || empty($entries['count'])) return true;

		$content = '"'.implode('","', array_keys($this->ldap_equivalent)).'"';
		if($this->ldap_deletenotexists) $content .= ',"ldapentry"';
		if(!empty($this->ldap_subfield)) $content .= ',"listids"';
		$content .= "\n";
		for($i = 0; $i < $entries['count']; $i++){
			foreach($this->ldap_equivalent as $ldapField){
				$fieldVal = isset($entries[$i][$ldapField][0]) ? $entries[$i][$ldapField][0] : '';
				$content .= '"'.$fieldVal.'",';
			}
			if($this->ldap_deletenotexists) $content .= '"1",';
			if(!empty($this->ldap_subfield)){
				static $errorsLists = array();
				if(isset($entries[$i][$this->ldap_subfield][0])){
					$condvalue = strtolower(trim($entries[$i][$this->ldap_subfield][0]));
					if(isset($this->ldap_subscribe[$condvalue])){
						$content .= $this->ldap_subscribe[$condvalue].',';
					}else{
						if(!isset($errorsLists[$condvalue]) AND count($errorsLists) < 5){
							$errorsLists[$condvalue] = true;
							acymailing_enqueueMessage('Could not find a list for the value "'.$condvalue.'" of the field '.$this->ldap_subfield, 'notice');
						}
						$content .= '"",';
					}
				}else{
					$content .= '"",';
				}
			}
			$content = rtrim($content, ',');
			$content .= "\n";
		}
		return $this->_handleContent($content);
	}


	function ldap_init(){
		$config = acymailing_config();
		$newConfig = new stdClass();
		$newConfig->ldap_host = trim(JRequest::getString('ldap_host'));
		$newConfig->ldap_port = JRequest::getInt('ldap_port');
		if(empty($newConfig->ldap_port)) $newConfig->ldap_port = 389;
		$newConfig->ldap_basedn = trim(JRequest::getString('ldap_basedn'));
		$this->ldap_basedn = $newConfig->ldap_basedn;
		$newConfig->ldap_username = trim(JRequest::getString('ldap_username'));
		$newConfig->ldap_password = trim(JRequest::getString('ldap_password'));

		$config->save($newConfig);

		if(empty($newConfig->ldap_host)) return false;

		acymailing_displayErrors();
		$this->ldap_conn = ldap_connect($newConfig->ldap_host, $newConfig->ldap_port);
		if(!$this->ldap_conn){
			acymailing_display('Could not connect to LDAP server : '.$newConfig->ldap_host.':'.$newConfig->ldap_port, 'warning');
			return false;
		}

		ldap_set_option($this->ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($this->ldap_conn, LDAP_OPT_REFERRALS, 0);

		if(empty($newConfig->ldap_username)){
			$bindResult = ldap_bind($this->ldap_conn);
		}else{
			$bindResult = ldap_bind($this->ldap_conn, $newConfig->ldap_username, $newConfig->ldap_password);
		}

		if(!$bindResult){
			acymailing_display('Could not bind to the LDAP directory '.$newConfig->ldap_host.':'.$newConfig->ldap_port.' with specified username and password<br />'.ldap_error($this->ldap_conn), 'warning');
			return false;
		}

		acymailing_display('Successfully connected to '.$newConfig->ldap_host.':'.$newConfig->ldap_port, 'success');

		return true;
	}

	function ldap_ajax(){

		if(!$this->ldap_init()) return;

		$config = acymailing_config();

		$searchResult = @ldap_search($this->ldap_conn, trim(JRequest::getString('ldap_basedn')), 'mail=*@*', array(), 0, 5);
		if(!$searchResult){
			acymailing_display('Could not search for elements<br />'.ldap_error($this->ldap_conn), 'warning');
			return false;
		}
		$entries = ldap_get_entries($this->ldap_conn, $searchResult);

		$fields = array();
		$dropdown = array();
		$object = new stdClass();
		$object->text = ' - - - ';
		$object->value = 0;
		$dropdown[] = $object;
		foreach($entries as $oneEntry){
			if(!is_array($oneEntry)) continue;
			foreach($oneEntry as $field => $value){
				if(!is_numeric($field)) continue;
				$value = strtolower($value);
				if($value == 'objectclass') continue;
				$fields[$value] = $value;
				$object = new stdClass();
				$object->text = $value;
				$object->value = $value;
				$dropdown[$value] = $object;
			}
		}

		if(empty($fields)){
			acymailing_display('Could not load elements<br />'.ldap_error($this->ldap_conn), 'warning');
			return false;
		}

		$db = JFactory::getDBO();
		$subfields = acymailing_getColumns('#__acymailing_subscriber');

		$acyfields = array();
		$acyfields[] = JHTML::_('select.option', '', ' - - - ');
		foreach($subfields as $oneField => $typefield){
			if(in_array($oneField, array('subid', 'confirmed', 'enabled', 'key', 'userid', 'accept', 'html', 'created'))) continue;
			$acyfields[] = JHTML::_('select.option', $oneField, $oneField);
		}

		echo '<table class="admintable" cellspacing="1">';
		foreach($fields as $oneField){
			echo '<tr><td class="acykey" >'.$oneField.'</td><td>'.JHTML::_('select.genericlist', $acyfields, 'ldapfield['.$oneField.']', 'size="1"', 'value', 'text', $config->get('ldapfield_'.$oneField)).'</td></tr>';
		}
		echo '</table>';

		echo '<fieldset><legend>'.JText::_('SUBSCRIPTION').'</legend>';
		echo 'Subscribe the user based on the values of the field '.JHTML::_('select.genericlist', $dropdown, 'ldap_subfield', 'size="1"', 'value', 'text', $config->get('ldap_subfield')).':';
		$listClass = acymailing_get('class.list');
		$lists = $listClass->getLists('listid');

		for($i = 0; $i < 5; $i++){
			echo '<br />Subscribe to list '.JHTML::_('select.genericlist', $lists, 'ldap_sublists['.$i.']', 'class="inputbox" size="1" ', 'listid', 'name', (int)$config->get('ldap_sublists_'.$i)).' if the value is <input type="text" value="'.htmlspecialchars($config->get('ldap_subcond_'.$i), ENT_COMPAT, 'UTF-8').'" name="ldap_subcond['.$i.']" />';
		}
		echo '</fieldset>';

	}

	function zohocrm($action = ''){
		$db = JFactory::getDBO();
		$zohoHelper = acymailing_get('helper.zoho');
		$subscriberClass = acymailing_get('class.subscriber');
		$tableInfos = array_keys(acymailing_getColumns('#__acymailing_subscriber'));
		$config =& acymailing_config();
		if(!in_array('zohoid', $tableInfos)){
			$query = 'ALTER TABLE #__acymailing_subscriber ADD COLUMN zohoid VARCHAR(255)';
			$db->setQuery($query);
			$db->query();
			$query = 'ALTER TABLE `#__acymailing_subscriber` ADD INDEX(`zohoid`)';
			$db->setQuery($query);
			$db->query();
		}
		if(!in_array('zoholist', $tableInfos)){
			$query = 'ALTER TABLE #__acymailing_subscriber ADD COLUMN zoholist CHAR(1)';
			$db->setQuery($query);
			$db->query();
		}

		if($action == 'update'){
			$list = $config->get('zoho_list');
			$zohoHelper->authtoken = $authtoken = $config->get('zoho_apikey');
			$zohoHelper->customView = $config->get('zoho_cv');
			$fields = unserialize($config->get('zoho_fields'));
			$confirmedUsers = $config->get('zoho_confirmed');
			$delete = $config->get('zoho_delete');
			$generateName = $config->get('zoho_generate_name', 'fromemail');
			$importnew = $config->get('zoho_importnew', 0);
		}else{
			$list = JRequest::getVar('zoho_list');
			$fields = JRequest::getVar('zoho_fields');
			$zohoHelper->authtoken = $authtoken = JRequest::getVar('zoho_apikey');
			$zohoHelper->customView = JRequest::getVar('zoho_cv');
			$overwrite = JRequest::getVar('zoho_overwrite');
			$confirmedUsers = JRequest::getVar('zoho_confirmed');
			$delete = JRequest::getVar('zoho_delete');
			$newConfig = new stdClass();
			$newConfig->zoho_fields = serialize($fields);
			$newConfig->zoho_list = $list;
			$newConfig->zoho_apikey = $zohoHelper->authtoken;
			$newConfig->zoho_cv = $zohoHelper->customView;
			$newConfig->zoho_overwrite = $overwrite;
			$newConfig->zoho_confirmed = $confirmedUsers;
			$newConfig->zoho_delete = $delete;
			$newConfig->zoho_generate_name = $generateName = JRequest::getVar('zoho_generate_name', 'fromemail');
			$newConfig->zoho_importnew = $importnew = JRequest::getVar('zoho_importnew', 0);
			$newConfig->zoho_importdate = date('Y-m-d H:i:s');
			$config->save($newConfig);
		}

		if($config->get('zoho_overwrite', false)) $this->overwrite = true;
		if(empty($authtoken)){
			acymailing_enqueueMessage('Pleaser enter a valid API key', 'notice');
			return false;
		}

		$this->allSubid = array();
		$indexDec = 200;
		$res = $zohoHelper->sendInfo($list);
		while(!empty($res)){
			$zohoUsers = $zohoHelper->parseXML($res, $list, $fields, $confirmedUsers, $generateName);
			if(empty($zohoUsers) && $zohoHelper->nbUserRead == 0) break;
			$this->_insertUsers($zohoUsers);
			if($zohoHelper->nbUserRead < 200) break; // No further iteration needed
			$zohoUsers = array();
			$zohoHelper->fromIndex = $zohoHelper->fromIndex + $indexDec;
			$zohoHelper->toIndex = $zohoHelper->toIndex + $indexDec;
			if(!empty($zohoHelper->conn)) $zohoHelper->close();
			$res = $zohoHelper->sendInfo($list);
		}
		$this->_subscribeUsers();
		if(JRequest::getInt('zoho_delete') == '1'){
			$zohoHelper->deleteAddress($this->allSubid, $list);
		}else{
			$query = 'SELECT DISTINCT b.subid FROM #__acymailing_subscriber AS a JOIN #__acymailing_subscriber AS b ON a.zohoid = b.zohoid WHERE a.zohoid IS NOT NULL AND b.subid < a.subid';
			$db->setQuery($query);
			$result = acymailing_loadResultArray($db);
			$subscriberClass->delete($result);
		}
		if(!empty($zohoHelper->conn)) $zohoHelper->close();

		$this->_displaySubscribedResult();
		if(!empty($zohoHelper->error) && defined('JDEBUG') && JDEBUG) acymailing_enqueueMessage(JText::sprintf($zohoHelper->error), 'notice');
	}

	function sobipro(){
		$config = acymailing_config();
		$db = JFactory::getDBO();

		$sobiproImport = JRequest::getVar('config', array(), 'POST', 'array');
		$newConfig = new stdClass();
		$affectedRows = 0;
		$newConfig->sobipro_import = serialize($sobiproImport);
		$config->save($newConfig);

		foreach($sobiproImport as $oneImport => $oneValue){
			$query = 'SELECT fid, nid FROM #__sobipro_field WHERE fid="'.$oneValue['sobiEmail'].'" OR fid="'.$oneValue['sobiName'].'"';
			$db->setQuery($query);
			$nidResult = $db->loadObjectList("fid");
			if(empty($nidResult[$oneValue['sobiEmail']]) OR empty($nidResult[$oneValue['sobiName']])) continue;
			$time = time();
			$query = 'INSERT IGNORE INTO '.acymailing_table('subscriber').' (`email`,`name`,`confirmed`,`created`,`enabled`,`accept`,`html`) SELECT b.baseData AS email, a.baseData AS name, 1 as confirmed, '.$time.' as created, 1 as enabled, 1 as accept, 1 as html FROM #__sobipro_field_data AS a LEFT JOIN #__sobipro_field_data AS b ON a.sid=b.sid WHERE a.`fid` = '.$nidResult[$oneValue["sobiName"]]->fid.' AND b.`fid` = '.$nidResult[$oneValue["sobiEmail"]]->fid.' AND b.baseData LIKE "%@%" AND b.baseData IS NOT NULL AND a.baseData IS NOT NULL ORDER by a.sid ';
			$db->setQuery($query);
			$db->query();
			$affectedRows += $db->getAffectedRows();
		}
		acymailing_enqueueMessage(JText::sprintf('IMPORT_NEW', $affectedRows));
		$query = 'SELECT b.subid FROM `#__sobipro_field_data` as a JOIN '.acymailing_table('subscriber').' as b on a.baseData = b.email';
		$this->db->setQuery($query);
		$this->allSubid = acymailing_loadResultArray($this->db);
		$this->_subscribeUsers();
		$this->_displaySubscribedResult();
		return true;
	}
}
