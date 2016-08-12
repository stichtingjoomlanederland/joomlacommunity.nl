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

class fieldsClass extends acymailingClass{

	var $tables = array('fields');
	var $pkey = 'fieldid';
	var $errors = array();
	var $prefix = 'field_';
	var $suffix = '';
	var $excludeValue = array();
	var $formoption = '';

	var $labelClass = '';

	var $dispatcher;

	var $currentUser;

	var $origin;

	function __construct($config = array()){
		JPluginHelper::importPlugin('acymailing');
		$this->dispatcher = JDispatcher::getInstance();
		return parent::__construct($config);
	}

	function getFields($area, &$user){

		if(empty($user)) $user = new stdClass();

		$where = array();
		$where[] = 'a.`published` = 1';
		if($area == 'backend'){
			$where[] = 'a.`backend` = 1';
			$where[] = 'a.`core` = 0';
		}elseif($area == 'backlisting'){
			$where[] = 'a.`listing` = 1';
			$where[] = 'a.`type` != \'category\'';
		}elseif($area == 'frontcomp'){
			$where[] = 'a.`frontcomp` = 1';
		}elseif($area == 'frontform'){
			$where[] = 'a.`frontform` = 1';
			$where[] = 'a.`core` = 0';
		}elseif($area == 'frontlisting'){
			$where[] = 'a.`frontlisting` = 1';
			$where[] = 'a.`type` != \'category\'';
		}elseif($area == 'frontjoomlaprofile'){
			$where[] = 'a.`frontjoomlaprofile` = 1';
			$where[] = 'a.`type` != \'category\'';
		}elseif($area == 'frontjoomlaregistration'){
			$where[] = 'a.`frontjoomlaregistration` = 1';
			$where[] = 'a.`type` != \'category\'';
		}elseif($area == 'joomlaprofile'){
			$where[] = 'a.`joomlaprofile` = 1';
			$where[] = 'a.`type` != \'category\'';
		}elseif($area == 'fieldcat'){
			$where[] = "a.`type`='category'";
		}elseif($area == 'module'){
		}elseif($area != 'all'){
			$area = $this->database->Quote($area);
			$namesField = str_replace(",", $area[0].",".$area[0], $area);
			$where[] = "a.`namekey` IN (".$namesField.")";
		}

		$app = JFactory::getApplication();
		if(!$app->isAdmin() && acymailing_level(3)){
			$my = JFactory::getUser();
			if(!ACYMAILING_J16){
				$groups = $my->gid;
				$condGroup = ' OR a.access LIKE (\'%,'.$groups.',%\')';
			}else{
				jimport('joomla.access.access');
				$groups = JAccess::getGroupsByUser($my->id, false);
				$condGroup = '';
				foreach($groups as $group){
					$condGroup .= ' OR a.access LIKE (\'%,'.$group.',%\')';
				}
			}
			$filterAccess = 'AND (a.access = \'all\''.$condGroup.')';
		}else{
			$filterAccess = '';
		}

		$this->database->setQuery('SELECT * FROM `#__acymailing_fields` as a WHERE '.implode(' AND ', $where).' '.$filterAccess.' ORDER BY a.`ordering` ASC');
		$fields = $this->database->loadObjectList('namekey');
		foreach($fields as $namekey => $field){
			if(!empty($fields[$namekey]->options)){
				$fields[$namekey]->options = unserialize($fields[$namekey]->options);
			}
			if(!empty($field->value)){
				$fields[$namekey]->value = $this->explodeValues($fields[$namekey]->value);
			}
			if($field->type == 'file' || $field->type == 'gravatar') $this->formoption = 'enctype="multipart/form-data"';
			if(empty($user->subid)) $user->$namekey = $field->default;
		}
		if(acymailing_level(3)){
			$this->database->setQuery('SELECT * FROM `#__acymailing_fields`');
			$allFields = $this->database->loadObjectList('fieldid');

			$baseElem = array();
			$elemInCat = array();
			foreach($fields as $namekey => $field){
				if($field->fieldcat == 0){
					$baseElem[] = $field;
				} // root element
				else{
					$parentId = $this->getParentCat($field, $fields, $allFields);
					$field->fieldcat = $parentId;
					if($parentId == 0){
						$baseElem[] = $field;
					} // No parent
					else{
						if(empty($elemInCat[$field->fieldcat])) $elemInCat[$field->fieldcat] = array();
						$elemInCat[$field->fieldcat][] = $field;
					}
				}
			}
			$finalField = array();
			foreach($baseElem as $oneField){
				$finalField[$oneField->namekey] = $oneField;
				if($oneField->type == 'category' && !empty($elemInCat[$oneField->fieldid])){
					$childs = $this->getChildFields($oneField->fieldid, $elemInCat);
					$finalField = $finalField + $childs;
				}
			}
			$fields = $finalField;
		}
		return $fields;
	}

	private function getParentCat($elem, $fields, $allFields){
		$parent = $allFields[$elem->fieldcat];
		if(array_key_exists($parent->namekey, $fields)){
			return $parent->fieldid;
		}else{
			if($parent->fieldcat == 0){
				return 0;
			}else return $this->getParentCat($parent, $fields, $allFields);
		}
	}

	private function getChildFields($fieldcatid, $elemInCat){
		$childs = array();
		$childElems = $elemInCat[$fieldcatid];
		foreach($childElems as $oneField){
			$childs[$oneField->namekey] = $oneField;
			if($oneField->type == 'category' && !empty($elemInCat[$oneField->fieldid])){
				$subChilds = $this->getChildFields($oneField->fieldid, $elemInCat);
				$childs = $childs + $subChilds;
			}
		}
		return $childs;
	}

	function getFieldName($field){
		$addLabels = array('textarea', 'text', 'dropdown', 'multipledropdown', 'file');
		return '<label '.(empty($this->labelClass) ? '' : ' class="'.$this->labelClass.'" ').(in_array($field->type, $addLabels) ? ' for="'.$this->prefix.$field->namekey.$this->suffix.'" ' : '').'>'.$this->trans($field->fieldname).'</label>';
	}

	function trans($name){
		if(preg_match('#^[A-Z_]*$#', $name)){
			return JText::_($name);
		}
		return $name;
	}

	function listing($field, $value, $search = ''){
		$functionType = '_listing'.ucfirst($field->type);

		if(method_exists($this, $functionType)) return $this->$functionType($field, $value);

		ob_start();
		$resultTrigger = $this->dispatcher->trigger('onAcyListingField_'.$field->type, array($field, $value));
		$pluginField = ob_get_clean();

		if(!empty($pluginField)){
			return $pluginField;
		}else return acymailing_dispSearch(nl2br($this->trans($value)), $search);
	}

	function explodeValues($values){
		$allValues = explode("\n", $values);
		$returnedValues = array();
		foreach($allValues as $id => $oneVal){
			$line = explode('::', trim($oneVal));
			$var = @$line[0];
			$val = @$line[1];
			if(strlen($val) < 1) continue;

			$obj = new stdClass();
			$obj->value = $val;
			for($i = 2; $i < count($line); $i++){
				$obj->{$line[$i]} = 1;
			}
			$returnedValues[$var] = $obj;
		}
		return $returnedValues;
	}


	function get($fieldid, $default = null){
		$column = is_numeric($fieldid) ? 'fieldid' : 'namekey';
		$query = 'SELECT a.* FROM '.acymailing_table('fields').' as a WHERE a.`'.$column.'` = '.$this->database->Quote($fieldid).' LIMIT 1';
		$this->database->setQuery($query);

		$field = $this->database->loadObject();
		if(!empty($field->options)){
			$field->options = unserialize($field->options);
		}

		if(!empty($field->value)){
			$field->value = $this->explodeValues($field->value);
		}

		return $field;
	}

	function chart($table, $field){

		static $a = false;
		$doc = JFactory::getDocument();
		if(!$a){
			$a = true;
			$doc->addScript("https://www.google.com/jsapi");
		}
		$namekey = acymailing_secureField($field->namekey);
		if(in_array($field->type, array('checkbox', 'multipledropdown'))){
			if(empty($field->value)) return;
			$results = array();
			foreach($field->value as $valName => $oneValue){
				if(strlen($oneValue->value) < 1) continue;
				$this->database->setQuery('SELECT COUNT(subid) as total, '.$this->database->Quote($valName).' as name FROM '.acymailing_table($table).' WHERE `'.$namekey.'` LIKE '.$this->database->Quote('%,'.$valName.',%').' OR `'.$namekey.'` LIKE '.$this->database->Quote($valName.',%').' OR `'.$namekey.'` LIKE '.$this->database->Quote('%,'.$valName).' OR `'.$namekey.'` = '.$this->database->Quote($valName));
				$myResult = $this->database->loadObject();
				if(!empty($myResult->total)) $results[] = $myResult;
			}
		}else{
			$this->database->setQuery('SELECT COUNT(`'.$namekey.'`) as total,'.(($namekey == 'birthday') ? 'YEAR(`'.$namekey.'`)' : ('`'.$namekey.'`')).' as name FROM '.acymailing_table($table).' WHERE `'.$namekey.'` IS NOT NULL'.(($namekey == 'html') ? ' ' : ' AND `'.$namekey.'` != \'\' ').' GROUP BY '.(($namekey == 'birthday') ? 'YEAR(`'.$namekey.'`)' : ('`'.$namekey.'`')).' ORDER BY total DESC LIMIT 20');
			$results = $this->database->loadObjectList();
		}

		?>
		<script language="JavaScript" type="text/javascript">
			function drawChart<?php echo $namekey; ?>(){
				var dataTable = new google.visualization.DataTable();
				dataTable.addColumn('string');
				dataTable.addColumn('number');
				dataTable.addRows(<?php echo count($results); ?>);

				<?php
				$export = '';
				foreach($results as $i => $oneResult){
				$name = isset($field->value[$oneResult->name]) ? $this->trans($field->value[$oneResult->name]->value) : $oneResult->name;
				$export .= "\n".$name.','.$oneResult->total;
				?>
				dataTable.setValue(<?php echo $i ?>, 0, '<?php echo addslashes($name).' ('.$oneResult->total.')'; ?>');
				dataTable.setValue(<?php echo $i ?>, 1, <?php echo intval($oneResult->total); ?>);
				<?php } ?>

				var vis = new google.visualization.<?php echo (in_array($field->type, array('checkbox', 'multipledropdown'))) ? 'ColumnChart' : 'PieChart'; ?>(document.getElementById('fieldchart<?php echo $namekey;?>'));
				var options = {
					height: 400, is3D: true, legendTextStyle: {color: '#333333'}, legend:<?php echo (in_array($field->type, array('checkbox', 'multipledropdown'))) ? "'none'" : "'right'"; ?>
				};
				vis.draw(dataTable, options);
			}
			google.load("visualization", "1", {packages: ["corechart"]});
			google.setOnLoadCallback(drawChart<?php echo $namekey; ?>);

			function exportData<?php echo $namekey;?>(){
				if(document.getElementById('exporteddata<?php echo $namekey;?>').style.display == 'none'){
					document.getElementById('exporteddata<?php echo $namekey;?>').style.display = '';
				}else{
					document.getElementById('exporteddata<?php echo $namekey;?>').style.display = 'none';
				}
			}
		</script>

		<div class="acychart" id="fieldchart<?php echo $namekey; ?>"></div>

		<span id="acy_exportchartlegend" class="acymailing_button" onclick="exportData<?php echo $namekey; ?>();"><?php echo JText::_('ACY_EXPORT'); ?><i class="acyicon-export donotprint" style="cursor:pointer;" alt="<?php echo JText::_('ACY_EXPORT', true) ?>" title="<?php echo JText::_('ACY_EXPORT', true) ?>"></i></span>
		<textarea cols="50" rows="10" id="exporteddata<?php echo $namekey; ?>" style="display:none;position:absolute;margin:-63px 0px 0px 5px; height: 100px;"><?php echo $export; ?></textarea>
		<?php
	}

	function saveForm(){

		$field = new stdClass();
		$field->fieldid = acymailing_getCID('fieldid');

		$formData = JRequest::getVar('data', array(), '', 'array');

		foreach($formData['fields'] as $column => $value){
			acymailing_secureField($column);
			if(is_array($value)){
				if(isset($value['day']) || isset($value['month']) || isset($value['year'])){
					$value = (empty($value['year']) ? '0000' : intval($value['year'])).'-'.(empty($value['month']) ? '00' : intval($value['month'])).'-'.(empty($value['day']) ? '00' : intval($value['day']));
				}else{
					$value = implode(',', $value);
				}
			}
			$field->$column = strip_tags($value);
		}

		$fieldValues = JRequest::getVar('fieldvalues', array(), '', 'array');
		if(!empty($fieldValues)){
			$field->value = array();
			foreach($fieldValues['title'] as $i => $title){
				$title = trim(strip_tags($title));
				$value = trim(strip_tags($value));
				if(strlen($title) < 1 AND strlen($fieldValues['value'][$i]) < 1) continue;
				$value = strlen($fieldValues['value'][$i]) < 1 ? $title : $fieldValues['value'][$i];
				$extra = '';
				if(!empty($fieldValues['disabled'][$i])) $extra .= '::disabled';
				$field->value[] = $title.'::'.$value.$extra;
			}
			$field->value = implode("\n", $field->value);
		}

		$fieldsOptions = JRequest::getVar('fieldsoptions', array(), '', 'array');
		foreach($fieldsOptions as $column => $value){
			if(!in_array($value, array('<', '<='))){
				$fieldsOptions[$column] = strip_tags($value);
			}else $fieldsOptions[$column] = $value;
		}
		if($field->type == "customtext"){
			$fieldsOptions['customtext'] = JRequest::getVar('fieldcustomtext', '', '', 'string', JREQUEST_ALLOWHTML);
			if(empty($field->fieldid)) $field->namekey = 'customtext_'.date('z_G_i_s');
		}
		if($field->type == 'category'){
			if(empty($field->fieldid)) $field->namekey = 'category_'.date('z_G_i_s');
		}

		if(in_array($field->type, array('birthday', 'date')) && !empty($fieldsOptions['format']) && strpos($fieldsOptions['format'], '%') === false){
			acymailing_enqueueMessage('Invalid Format: "'.$fieldsOptions['format'].'"<br /><br />Please use a combination of:<br /> - %d (which will be replaced by days)<br /> - %m (which will be replaced by months)<br /> - %Y (which will be replaced by years)', 'notice');
			$fieldsOptions['format'] = '';
		}

		$field->options = serialize($fieldsOptions);

		if(empty($field->fieldid) && !in_array($field->type, array('customtext', 'category'))){
			if(empty($field->namekey)) $field->namekey = $field->fieldname;
			$field->namekey = substr(preg_replace('#[^a-z0-9_]#i', '', strtolower($field->namekey)), 0, 50);
			if(empty($field->namekey) || !preg_match('#^[a-z]#', $field->namekey)){
				$this->errors[] = 'Please specify a valid Column Name';
				return false;
			}

			$columns = acymailing_getColumns('#__acymailing_subscriber');

			if(isset($columns[$field->namekey])){
				$this->errors[] = 'The field "'.$field->namekey.'" already exists';
				return false;
			}

			if($field->type == 'textarea'){
				$query = 'ALTER TABLE `#__acymailing_subscriber` ADD `'.$field->namekey.'` TEXT NOT NULL DEFAULT ""';
			}else{
				$query = 'ALTER TABLE `#__acymailing_subscriber` ADD `'.$field->namekey.'` VARCHAR ( 250 ) NOT NULL DEFAULT ""';
			}
			$this->database->setQuery($query);
			if(!$this->database->query()) return false;
		}

		$fieldid = $this->save($field);
		if(!$fieldid) return false;

		if(empty($field->fieldid)){
			$orderClass = acymailing_get('helper.order');
			$orderClass->pkey = 'fieldid';
			$orderClass->table = 'fields';
			$orderClass->reOrder();
		}
		JRequest::setVar('fieldid', $fieldid);
		return true;
	}

	function delete($elements){
		if(!is_array($elements)){
			$elements = array($elements);
		}

		JArrayHelper::toInteger($elements);

		if(empty($elements)) return false;

		$this->database->setQuery('SELECT `namekey`,`fieldid` FROM `#__acymailing_fields`  WHERE `core` = 0 AND `fieldid` IN ('.implode(',', $elements).')');
		$fieldsToDelete = $this->database->loadObjectList('fieldid');
		if(empty($fieldsToDelete)) return false;

		$this->database->setQuery('SELECT fieldid, fieldname, options from #__acymailing_fields WHERE fieldid NOT IN ('.implode(',', $elements).')');
		$allOtherFields = $this->database->loadObjectList();
		$nameKeyToDel = array();
		foreach($fieldsToDelete as $fieldid => $oneFieldToDel){
			$nameKeyToDel[$fieldid] = $oneFieldToDel->namekey;
		}
		$used = false;
		$fieldUsed = array();
		$usedInFields = array();
		foreach($allOtherFields as $oneField){
			$fieldOptions = unserialize($oneField->options);
			$i = -1;
			do{
				$i++;
				if(!empty($fieldOptions['displim_field'.$i]) && in_array($fieldOptions['displim_field'.$i], $nameKeyToDel)){
					$fieldUsed[] = $fieldOptions['displim_field'.$i];
					$usedInFields[] = '<a href="'.acymailing_completeLink('fields&task=edit&fieldid='.$oneField->fieldid).'" target="_blank">'.$oneField->fieldname.' ['.JText::_('ACY_ID').' '.$oneField->fieldid.']</a>';
					$used = true;
				}
			}while(!empty($fieldOptions['displim_rel'.$i]));
		}
		if($used){
			acymailing_enqueueMessage(JText::_('ACY_NO_FIELD_DELETED'), 'error');
			foreach($fieldUsed as $k => $oneField){
				acymailing_enqueueMessage(JText::sprintf('ACY_FIELD_USED_DISPLAY', '<b>'.$fieldUsed[$k].'</b>', $usedInFields[$k]), 'error');
			}
			return false;
		}

		$namekeys = array();
		foreach($fieldsToDelete as $oneField){
			if(substr($oneField->namekey, 0, 11) == 'customtext_' || substr($oneField->namekey, 0, 9) == 'category_') continue;
			$namekeys[] = $oneField->namekey;
		}
		if(!empty($namekeys)){
			$this->database->setQuery('ALTER TABLE `#__acymailing_subscriber` DROP `'.implode('`, DROP `', $namekeys).'`');
			$this->database->query();
		}


		$this->database->setQuery('UPDATE `#__acymailing_fields` SET fieldcat = 0 WHERE fieldcat IN ('.implode(',', array_keys($fieldsToDelete)).')');
		$this->database->query();

		$this->database->setQuery('DELETE FROM `#__acymailing_fields` WHERE `fieldid` IN ('.implode(',', array_keys($fieldsToDelete)).')');
		$result = $this->database->query();
		if(!$result) return false;

		$affectedRows = $this->database->getAffectedRows();

		$orderClass = acymailing_get('helper.order');
		$orderClass->pkey = 'fieldid';
		$orderClass->table = 'fields';
		$orderClass->reOrder();

		return $affectedRows;
	}

	private function _listingFile($field, $value){
		if(empty($value)) return;
		static $path = '';
		if(empty($path)){
			$config = acymailing_config();
			$path = trim(JPath::clean(html_entity_decode(acymailing_getFilesFolder())), DS.' ').DS;
			$path = ACYMAILING_LIVE.str_replace(DS, '/', $path.'userfiles/');
		}

		if(preg_match('#\.(jpg|gif|png|jpeg|ico|bmp)$#i', $value)){
			$fileName = '<img class="acymailing_user_avatar" src="'.$path.$value.'" style="max-width:120px;max-height:80px;"/>';
		}else{
			$fileName = str_replace('_', ' ', substr($value, strpos($value, '_')));
		}
		return '<a href="'.$path.$value.'" target="_blank">'.$fileName.'</a>';
	}

	private function _listingGravatar($field, $value){
		if(empty($value) && !empty($this->currentUser)){
			$emailUser = $this->currentUser->email;
			$url = 'https://www.gravatar.com/avatar/';
			$url .= md5(strtolower(trim($emailUser)));
			$url .= '?d=mm&s=50'; // Default picture (Mystery Man -> mm) and size 50px
			$img = '<img class="acymailing_user_avatar" src="'.$url.'" />';
			return $img;
		}else{
			return $this->_listingFile($field, $value);
		}
	}

	private function _listingPhone($field, $value){
		return str_replace(array(','), ' ', $value);
	}

	private function _generateCountryArray(){
		$this->flagPosition = array();
		$this->flagPosition['93'] = array('x' => -48, 'y' => 0);
		$this->flagPosition['355'] = array('x' => -96, 'y' => 0);
		$this->flagPosition['213'] = array('x' => -160, 'y' => -33);
		$this->flagPosition['1684'] = array('x' => -176, 'y' => 0);
		$this->flagPosition['376'] = array('x' => -16, 'y' => 0);
		$this->flagPosition['244'] = array('x' => -144, 'y' => 0);
		$this->flagPosition['1264'] = array('x' => -80, 'y' => 0);
		$this->flagPosition['672'] = array('x' => 0, 'y' => -176); //antartica
		$this->flagPosition['1268'] = array('x' => -64, 'y' => 0);
		$this->flagPosition['54'] = array('x' => -160, 'y' => 0);
		$this->flagPosition['374'] = array('x' => -112, 'y' => 0);
		$this->flagPosition['297'] = array('x' => -224, 'y' => 0);
		$this->flagPosition['247'] = array('x' => -16, 'y' => -176); //ascenscion island
		$this->flagPosition['61'] = array('x' => -208, 'y' => 0);
		$this->flagPosition['43'] = array('x' => -192, 'y' => 0);
		$this->flagPosition['994'] = array('x' => -240, 'y' => 0);
		$this->flagPosition['1242'] = array('x' => -208, 'y' => -11);
		$this->flagPosition['973'] = array('x' => -96, 'y' => -11);
		$this->flagPosition['880'] = array('x' => -32, 'y' => -11);
		$this->flagPosition['1246'] = array('x' => -16, 'y' => -11);
		$this->flagPosition['375'] = array('x' => -16, 'y' => -22);
		$this->flagPosition['32'] = array('x' => -48, 'y' => -11);
		$this->flagPosition['501'] = array('x' => -32, 'y' => -22);
		$this->flagPosition['229'] = array('x' => -128, 'y' => -11);
		$this->flagPosition['1441'] = array('x' => -144, 'y' => -11);
		$this->flagPosition['975'] = array('x' => -224, 'y' => -11);
		$this->flagPosition['591'] = array('x' => -176, 'y' => -11);
		$this->flagPosition['387'] = array('x' => 0, 'y' => -11);
		$this->flagPosition['267'] = array('x' => 0, 'y' => -22);
		$this->flagPosition['55'] = array('x' => -192, 'y' => -11);
		$this->flagPosition['1284'] = array('x' => -240, 'y' => -154);
		$this->flagPosition['673'] = array('x' => -160, 'y' => -11);
		$this->flagPosition['359'] = array('x' => -80, 'y' => -11);
		$this->flagPosition['226'] = array('x' => -64, 'y' => -11);
		$this->flagPosition['257'] = array('x' => -112, 'y' => -11);
		$this->flagPosition['855'] = array('x' => -64, 'y' => -77);
		$this->flagPosition['237'] = array('x' => -192, 'y' => -22);
		$this->flagPosition['1'] = array('x' => -48, 'y' => -22);
		$this->flagPosition['238'] = array('x' => -16, 'y' => -33);
		$this->flagPosition['1345'] = array('x' => -192, 'y' => -77);
		$this->flagPosition['236'] = array('x' => -96, 'y' => -22);
		$this->flagPosition['235'] = array('x' => -112, 'y' => -143);
		$this->flagPosition['56'] = array('x' => -176, 'y' => -22);
		$this->flagPosition['86'] = array('x' => -208, 'y' => -22);
		$this->flagPosition['6724'] = array('x' => -32, 'y' => -176); //christmas island
		$this->flagPosition['6722'] = array('x' => -48, 'y' => -176); //coco keeling island
		$this->flagPosition['57'] = array('x' => -224, 'y' => -22);
		$this->flagPosition['269'] = array('x' => -96, 'y' => -77);
		$this->flagPosition['243'] = array('x' => -80, 'y' => -22);
		$this->flagPosition['242'] = array('x' => -112, 'y' => -22);
		$this->flagPosition['682'] = array('x' => -160, 'y' => -22);
		$this->flagPosition['506'] = array('x' => -240, 'y' => -22);
		$this->flagPosition['225'] = array('x' => -144, 'y' => -22);
		$this->flagPosition['385'] = array('x' => 0, 'y' => -66);
		$this->flagPosition['53'] = array('x' => 0, 'y' => -33);
		$this->flagPosition['357'] = array('x' => -48, 'y' => -33);
		$this->flagPosition['420'] = array('x' => -64, 'y' => -33);
		$this->flagPosition['45'] = array('x' => -112, 'y' => -33);
		$this->flagPosition['253'] = array('x' => -96, 'y' => -33);
		$this->flagPosition['1767'] = array('x' => -128, 'y' => -33);
		$this->flagPosition['1809'] = array('x' => -144, 'y' => -33);
		$this->flagPosition['593'] = array('x' => -176, 'y' => -33);
		$this->flagPosition['20'] = array('x' => -208, 'y' => -33);
		$this->flagPosition['503'] = array('x' => -32, 'y' => -143);
		$this->flagPosition['240'] = array('x' => -96, 'y' => -55);
		$this->flagPosition['291'] = array('x' => 0, 'y' => -44);
		$this->flagPosition['372'] = array('x' => -192, 'y' => -33);
		$this->flagPosition['251'] = array('x' => -32, 'y' => -44);
		$this->flagPosition['500'] = array('x' => -96, 'y' => -44);
		$this->flagPosition['298'] = array('x' => -128, 'y' => -44);
		$this->flagPosition['679'] = array('x' => -80, 'y' => -44);
		$this->flagPosition['358'] = array('x' => -64, 'y' => -44);
		$this->flagPosition['33'] = array('x' => -144, 'y' => -44);
		$this->flagPosition['596'] = array('x' => -80, 'y' => -99);
		$this->flagPosition['594'] = array('x' => -128, 'y' => -176); //french guiana
		$this->flagPosition['689'] = array('x' => -224, 'y' => -110);
		$this->flagPosition['241'] = array('x' => -160, 'y' => -44);
		$this->flagPosition['220'] = array('x' => -48, 'y' => -55);
		$this->flagPosition['995'] = array('x' => -208, 'y' => -44);
		$this->flagPosition['49'] = array('x' => -80, 'y' => -33);
		$this->flagPosition['233'] = array('x' => 0, 'y' => -55);
		$this->flagPosition['350'] = array('x' => -16, 'y' => -55);
		$this->flagPosition['30'] = array('x' => -112, 'y' => -55);
		$this->flagPosition['299'] = array('x' => -32, 'y' => -55);
		$this->flagPosition['1473'] = array('x' => -192, 'y' => -44);
		$this->flagPosition['590'] = array('x' => -80, 'y' => -55);
		$this->flagPosition['1671'] = array('x' => -160, 'y' => -55);
		$this->flagPosition['502'] = array('x' => -144, 'y' => -55);
		$this->flagPosition['224'] = array('x' => -64, 'y' => -55);
		$this->flagPosition['245'] = array('x' => -176, 'y' => -55);
		$this->flagPosition['592'] = array('x' => -192, 'y' => -55);
		$this->flagPosition['509'] = array('x' => -16, 'y' => -66);
		$this->flagPosition['504'] = array('x' => -240, 'y' => -55);
		$this->flagPosition['852'] = array('x' => -208, 'y' => -55);
		$this->flagPosition['36'] = array('x' => -32, 'y' => -66);
		$this->flagPosition['354'] = array('x' => -192, 'y' => -66);
		$this->flagPosition['91'] = array('x' => -128, 'y' => -66);
		$this->flagPosition['62'] = array('x' => -64, 'y' => -66);
		$this->flagPosition['964'] = array('x' => -160, 'y' => -66);
		$this->flagPosition['98'] = array('x' => -176, 'y' => -66);
		$this->flagPosition['353'] = array('x' => -80, 'y' => -66);
		$this->flagPosition['972'] = array('x' => -96, 'y' => -66);
		$this->flagPosition['39'] = array('x' => -208, 'y' => -66);
		$this->flagPosition['1876'] = array('x' => -240, 'y' => -66);
		$this->flagPosition['81'] = array('x' => -16, 'y' => -77);
		$this->flagPosition['962'] = array('x' => 0, 'y' => -77);
		$this->flagPosition['254'] = array('x' => -32, 'y' => -77);
		$this->flagPosition['686'] = array('x' => -80, 'y' => -77);
		$this->flagPosition['3774'] = array('x' => -64, 'y' => -176); //kosovo
		$this->flagPosition['965'] = array('x' => -176, 'y' => -77);
		$this->flagPosition['996'] = array('x' => -48, 'y' => -77);
		$this->flagPosition['856'] = array('x' => -224, 'y' => -77);
		$this->flagPosition['371'] = array('x' => -112, 'y' => -88);
		$this->flagPosition['961'] = array('x' => -240, 'y' => -77);
		$this->flagPosition['266'] = array('x' => -64, 'y' => -88);
		$this->flagPosition['231'] = array('x' => -48, 'y' => -88);
		$this->flagPosition['218'] = array('x' => -128, 'y' => -88);
		$this->flagPosition['423'] = array('x' => -16, 'y' => -88);
		$this->flagPosition['370'] = array('x' => -80, 'y' => -88);
		$this->flagPosition['352'] = array('x' => -96, 'y' => -88);
		$this->flagPosition['853'] = array('x' => -48, 'y' => -99);
		$this->flagPosition['389'] = array('x' => -240, 'y' => -88);
		$this->flagPosition['261'] = array('x' => -208, 'y' => -88);
		$this->flagPosition['265'] = array('x' => -176, 'y' => -99);
		$this->flagPosition['60'] = array('x' => -208, 'y' => -99);
		$this->flagPosition['960'] = array('x' => -160, 'y' => -99);
		$this->flagPosition['223'] = array('x' => 0, 'y' => -99);
		$this->flagPosition['356'] = array('x' => -128, 'y' => -99);
		$this->flagPosition['692'] = array('x' => -224, 'y' => -88);
		$this->flagPosition['222'] = array('x' => -96, 'y' => -99);
		$this->flagPosition['230'] = array('x' => -144, 'y' => -99);
		$this->flagPosition['52'] = array('x' => -192, 'y' => -99);
		$this->flagPosition['691'] = array('x' => -112, 'y' => -44);
		$this->flagPosition['373'] = array('x' => -176, 'y' => -88);
		$this->flagPosition['377'] = array('x' => -160, 'y' => -88);
		$this->flagPosition['976'] = array('x' => -32, 'y' => -99);
		$this->flagPosition['382'] = array('x' => -192, 'y' => -88);
		$this->flagPosition['1664'] = array('x' => -112, 'y' => -99);
		$this->flagPosition['212'] = array('x' => -144, 'y' => -88);
		$this->flagPosition['258'] = array('x' => -224, 'y' => -99);
		$this->flagPosition['95'] = array('x' => -16, 'y' => -99);
		$this->flagPosition['264'] = array('x' => -240, 'y' => -99);
		$this->flagPosition['674'] = array('x' => -128, 'y' => -110);
		$this->flagPosition['977'] = array('x' => -112, 'y' => -110);
		$this->flagPosition['31'] = array('x' => -80, 'y' => -110);
		$this->flagPosition['599'] = array('x' => -128, 'y' => 0);
		$this->flagPosition['687'] = array('x' => 0, 'y' => -110);
		$this->flagPosition['64'] = array('x' => -160, 'y' => -110);
		$this->flagPosition['505'] = array('x' => -64, 'y' => -110);
		$this->flagPosition['227'] = array('x' => -16, 'y' => -110);
		$this->flagPosition['234'] = array('x' => -48, 'y' => -110);
		$this->flagPosition['683'] = array('x' => -144, 'y' => -110);
		$this->flagPosition['6723'] = array('x' => -32, 'y' => -110);
		$this->flagPosition['850'] = array('x' => -128, 'y' => -77);
		$this->flagPosition['47'] = array('x' => -96, 'y' => -110);
		$this->flagPosition['968'] = array('x' => -176, 'y' => -110);
		$this->flagPosition['92'] = array('x' => -16, 'y' => -121);
		$this->flagPosition['680'] = array('x' => -80, 'y' => -176); //palau
		$this->flagPosition['970'] = array('x' => -96, 'y' => -121);
		$this->flagPosition['507'] = array('x' => -192, 'y' => -110);
		$this->flagPosition['675'] = array('x' => -240, 'y' => -110);
		$this->flagPosition['595'] = array('x' => -144, 'y' => -121);
		$this->flagPosition['51'] = array('x' => -208, 'y' => -110);
		$this->flagPosition['63'] = array('x' => 0, 'y' => -121);
		$this->flagPosition['48'] = array('x' => -32, 'y' => -121);
		$this->flagPosition['351'] = array('x' => -112, 'y' => -121);
		$this->flagPosition['1787'] = array('x' => -80, 'y' => -121);
		$this->flagPosition['974'] = array('x' => -160, 'y' => -121);
		$this->flagPosition['262'] = array('x' => -144, 'y' => -176); //reunion island
		$this->flagPosition['40'] = array('x' => -192, 'y' => -121);
		$this->flagPosition['7'] = array('x' => -224, 'y' => -121);
		$this->flagPosition['250'] = array('x' => -240, 'y' => -121);
		$this->flagPosition['1670'] = array('x' => -96, 'y' => -176); //marianne
		$this->flagPosition['378'] = array('x' => -176, 'y' => -132);
		$this->flagPosition['239'] = array('x' => -16, 'y' => -143);
		$this->flagPosition['966'] = array('x' => 0, 'y' => -132);
		$this->flagPosition['221'] = array('x' => -192, 'y' => -132);
		$this->flagPosition['381'] = array('x' => -208, 'y' => -121);
		$this->flagPosition['248'] = array('x' => -32, 'y' => -132);
		$this->flagPosition['232'] = array('x' => -160, 'y' => -132);
		$this->flagPosition['65'] = array('x' => -96, 'y' => -132);
		$this->flagPosition['421'] = array('x' => -144, 'y' => -132);
		$this->flagPosition['386'] = array('x' => -128, 'y' => -132);
		$this->flagPosition['677'] = array('x' => -16, 'y' => -132);
		$this->flagPosition['252'] = array('x' => -208, 'y' => -132);
		$this->flagPosition['685'] = array('x' => -112, 'y' => -176); //somoa
		$this->flagPosition['27'] = array('x' => -128, 'y' => -165);
		$this->flagPosition['82'] = array('x' => -144, 'y' => -77);
		$this->flagPosition['34'] = array('x' => -16, 'y' => -44);
		$this->flagPosition['94'] = array('x' => -32, 'y' => -88);
		$this->flagPosition['290'] = array('x' => -112, 'y' => -132);
		$this->flagPosition['1869'] = array('x' => -112, 'y' => -77);
		$this->flagPosition['1758'] = array('x' => 0, 'y' => -88);
		$this->flagPosition['508'] = array('x' => -48, 'y' => -121);
		$this->flagPosition['1784'] = array('x' => -208, 'y' => -154);
		$this->flagPosition['249'] = array('x' => -64, 'y' => -132);
		$this->flagPosition['597'] = array('x' => -240, 'y' => -132);
		$this->flagPosition['268'] = array('x' => -80, 'y' => -143);
		$this->flagPosition['46'] = array('x' => -80, 'y' => -132);
		$this->flagPosition['41'] = array('x' => -128, 'y' => -22);
		$this->flagPosition['963'] = array('x' => -64, 'y' => -143);
		$this->flagPosition['886'] = array('x' => -64, 'y' => -154);
		$this->flagPosition['992'] = array('x' => -176, 'y' => -143);
		$this->flagPosition['255'] = array('x' => -80, 'y' => -154);
		$this->flagPosition['66'] = array('x' => -160, 'y' => -143);
		$this->flagPosition['228'] = array('x' => -144, 'y' => -143);
		$this->flagPosition['690'] = array('x' => -192, 'y' => -143);
		$this->flagPosition['676'] = array('x' => 0, 'y' => -154);
		$this->flagPosition['1868'] = array('x' => -32, 'y' => -154);
		$this->flagPosition['216'] = array('x' => -240, 'y' => -143);
		$this->flagPosition['90'] = array('x' => -16, 'y' => -154);
		$this->flagPosition['993'] = array('x' => -224, 'y' => -143);
		$this->flagPosition['1649'] = array('x' => -96, 'y' => -143);
		$this->flagPosition['688'] = array('x' => -48, 'y' => -154);
		$this->flagPosition['256'] = array('x' => -112, 'y' => -154);
		$this->flagPosition['380'] = array('x' => -96, 'y' => -154);
		$this->flagPosition['971'] = array('x' => -32, 'y' => 0);
		$this->flagPosition['44'] = array('x' => -176, 'y' => -44);
		$this->flagPosition['598'] = array('x' => -160, 'y' => -154);
		$this->flagPosition['1 '] = array('x' => -144, 'y' => -154);
		$this->flagPosition['998'] = array('x' => -176, 'y' => -154);
		$this->flagPosition['678'] = array('x' => -32, 'y' => -165);
		$this->flagPosition['3966'] = array('x' => -192, 'y' => -154);
		$this->flagPosition['58'] = array('x' => -224, 'y' => -154);
		$this->flagPosition['84'] = array('x' => -16, 'y' => -165);
		$this->flagPosition['1340'] = array('x' => 0, 'y' => -165);
		$this->flagPosition['681'] = array('x' => -64, 'y' => -165);
		$this->flagPosition['967'] = array('x' => -96, 'y' => -165);
		$this->flagPosition['260'] = array('x' => -160, 'y' => -165);
		$this->flagPosition['263'] = array('x' => -176, 'y' => -165);
		$this->flagPosition[''] = array('x' => -160, 'y' => -176);


		$this->country = array();
		$this->country['93'] = 'Afghanistan';
		$this->country['355'] = 'Albania';
		$this->country['213'] = 'Algeria';
		$this->country['1684'] = 'American Samoa';
		$this->country['376'] = 'Andorra';
		$this->country['244'] = 'Angola';
		$this->country['1264'] = 'Anguilla';
		$this->country['672'] = 'Antarctica';
		$this->country['1268'] = 'Antigua & Barbuda';
		$this->country['54'] = 'Argentina';
		$this->country['374'] = 'Armenia';
		$this->country['297'] = 'Aruba';
		$this->country['247'] = 'Ascension Island';
		$this->country['61'] = 'Australia';
		$this->country['43'] = 'Austria';
		$this->country['994'] = 'Azerbaijan';
		$this->country['1242'] = 'Bahamas';
		$this->country['973'] = 'Bahrain';
		$this->country['880'] = 'Bangladesh';
		$this->country['1246'] = 'Barbados';
		$this->country['375'] = 'Belarus';
		$this->country['32'] = 'Belgium';
		$this->country['501'] = 'Belize';
		$this->country['229'] = 'Benin';
		$this->country['1441'] = 'Bermuda';
		$this->country['975'] = 'Bhutan';
		$this->country['591'] = 'Bolivia';
		$this->country['387'] = 'Bosnia/Herzegovina';
		$this->country['267'] = 'Botswana';
		$this->country['55'] = 'Brazil';
		$this->country['1284'] = 'British Virgin Islands';
		$this->country['673'] = 'Brunei';
		$this->country['359'] = 'Bulgaria';
		$this->country['226'] = 'Burkina Faso';
		$this->country['257'] = 'Burundi';
		$this->country['855'] = 'Cambodia';
		$this->country['237'] = 'Cameroon';
		$this->country['1'] = 'Canada/USA';
		$this->country['238'] = 'Cape Verde Islands';
		$this->country['1345'] = 'Cayman Islands';
		$this->country['236'] = 'Central African Republic';
		$this->country['235'] = 'Chad Republic';
		$this->country['56'] = 'Chile';
		$this->country['86'] = 'China';
		$this->country['6724'] = 'Christmas Island';
		$this->country['6722'] = 'Cocos Keeling Island';
		$this->country['57'] = 'Colombia';
		$this->country['269'] = 'Comoros';
		$this->country['243'] = 'Congo Democratic Republic';
		$this->country['242'] = 'Congo, Republic of';
		$this->country['682'] = 'Cook Islands';
		$this->country['506'] = 'Costa Rica';
		$this->country['225'] = 'Cote D\'Ivoire';
		$this->country['385'] = 'Croatia';
		$this->country['53'] = 'Cuba';
		$this->country['357'] = 'Cyprus';
		$this->country['420'] = 'Czech Republic';
		$this->country['45'] = 'Denmark';
		$this->country['253'] = 'Djibouti';
		$this->country['1767'] = 'Dominica';
		$this->country['1809'] = 'Dominican Republic';
		$this->country['593'] = 'Ecuador';
		$this->country['20'] = 'Egypt';
		$this->country['503'] = 'El Salvador';
		$this->country['240'] = 'Equatorial Guinea';
		$this->country['291'] = 'Eritrea';
		$this->country['372'] = 'Estonia';
		$this->country['251'] = 'Ethiopia';
		$this->country['500'] = 'Falkland Islands';
		$this->country['298'] = 'Faroe Island';
		$this->country['679'] = 'Fiji Islands';
		$this->country['358'] = 'Finland';
		$this->country['33'] = 'France';
		$this->country['596'] = 'French Antilles/Martinique';
		$this->country['594'] = 'French Guiana';
		$this->country['689'] = 'French Polynesia';
		$this->country['241'] = 'Gabon Republic';
		$this->country['220'] = 'Gambia';
		$this->country['995'] = 'Georgia';
		$this->country['49'] = 'Germany';
		$this->country['233'] = 'Ghana';
		$this->country['350'] = 'Gibraltar';
		$this->country['30'] = 'Greece';
		$this->country['299'] = 'Greenland';
		$this->country['1473'] = 'Grenada';
		$this->country['590'] = 'Guadeloupe';
		$this->country['1671'] = 'Guam';
		$this->country['502'] = 'Guatemala';
		$this->country['224'] = 'Guinea Republic';
		$this->country['245'] = 'Guinea-Bissau';
		$this->country['592'] = 'Guyana';
		$this->country['509'] = 'Haiti';
		$this->country['504'] = 'Honduras';
		$this->country['852'] = 'Hong Kong';
		$this->country['36'] = 'Hungary';
		$this->country['354'] = 'Iceland';
		$this->country['91'] = 'India';
		$this->country['62'] = 'Indonesia';
		$this->country['964'] = 'Iraq';
		$this->country['98'] = 'Iran';
		$this->country['353'] = 'Ireland';
		$this->country['972'] = 'Israel';
		$this->country['39'] = 'Italy';
		$this->country['1876'] = 'Jamaica';
		$this->country['81'] = 'Japan';
		$this->country['962'] = 'Jordan';
		$this->country['254'] = 'Kenya';
		$this->country['686'] = 'Kiribati';
		$this->country['3774'] = 'Kosovo';
		$this->country['965'] = 'Kuwait';
		$this->country['996'] = 'Kyrgyzstan';
		$this->country['856'] = 'Laos';
		$this->country['371'] = 'Latvia';
		$this->country['961'] = 'Lebanon';
		$this->country['266'] = 'Lesotho';
		$this->country['231'] = 'Liberia';
		$this->country['218'] = 'Libya';
		$this->country['423'] = 'Liechtenstein';
		$this->country['370'] = 'Lithuania';
		$this->country['352'] = 'Luxembourg';
		$this->country['853'] = 'Macau';
		$this->country['389'] = 'Macedonia';
		$this->country['261'] = 'Madagascar';
		$this->country['265'] = 'Malawi';
		$this->country['60'] = 'Malaysia';
		$this->country['960'] = 'Maldives';
		$this->country['223'] = 'Mali Republic';
		$this->country['356'] = 'Malta';
		$this->country['692'] = 'Marshall Islands';
		$this->country['222'] = 'Mauritania';
		$this->country['230'] = 'Mauritius';
		$this->country['52'] = 'Mexico';
		$this->country['691'] = 'Micronesia';
		$this->country['373'] = 'Moldova';
		$this->country['377'] = 'Monaco';
		$this->country['976'] = 'Mongolia';
		$this->country['382'] = 'Montenegro';
		$this->country['1664'] = 'Montserrat';
		$this->country['212'] = 'Morocco';
		$this->country['258'] = 'Mozambique';
		$this->country['95'] = 'Myanmar (Burma)';
		$this->country['264'] = 'Namibia';
		$this->country['674'] = 'Nauru';
		$this->country['977'] = 'Nepal';
		$this->country['31'] = 'Netherlands';
		$this->country['599'] = 'Netherlands Antilles';
		$this->country['687'] = 'New Caledonia';
		$this->country['64'] = 'New Zealand';
		$this->country['505'] = 'Nicaragua';
		$this->country['227'] = 'Niger Republic';
		$this->country['234'] = 'Nigeria';
		$this->country['683'] = 'Niue Island';
		$this->country['6723'] = 'Norfolk';
		$this->country['850'] = 'North Korea';
		$this->country['47'] = 'Norway';
		$this->country['968'] = 'Oman Dem Republic';
		$this->country['92'] = 'Pakistan';
		$this->country['680'] = 'Palau Republic';
		$this->country['970'] = 'Palestine';
		$this->country['507'] = 'Panama';
		$this->country['675'] = 'Papua New Guinea';
		$this->country['595'] = 'Paraguay';
		$this->country['51'] = 'Peru';
		$this->country['63'] = 'Philippines';
		$this->country['48'] = 'Poland';
		$this->country['351'] = 'Portugal';
		$this->country['1787'] = 'Puerto Rico';
		$this->country['974'] = 'Qatar';
		$this->country['262'] = 'Reunion Island';
		$this->country['40'] = 'Romania';
		$this->country['7'] = 'Russia';
		$this->country['250'] = 'Rwanda Republic';
		$this->country['1670'] = 'Saipan/Mariannas';
		$this->country['378'] = 'San Marino';
		$this->country['239'] = 'Sao Tome/Principe';
		$this->country['966'] = 'Saudi Arabia';
		$this->country['221'] = 'Senegal';
		$this->country['381'] = 'Serbia';
		$this->country['248'] = 'Seychelles Island';
		$this->country['232'] = 'Sierra Leone';
		$this->country['65'] = 'Singapore';
		$this->country['421'] = 'Slovakia';
		$this->country['386'] = 'Slovenia';
		$this->country['677'] = 'Solomon Islands';
		$this->country['252'] = 'Somalia Republic';
		$this->country['685'] = 'Somoa';
		$this->country['27'] = 'South Africa';
		$this->country['82'] = 'South Korea';
		$this->country['34'] = 'Spain';
		$this->country['94'] = 'Sri Lanka';
		$this->country['290'] = 'St. Helena';
		$this->country['1869'] = 'St. Kitts';
		$this->country['1758'] = 'St. Lucia';
		$this->country['508'] = 'St. Pierre';
		$this->country['1784'] = 'St. Vincent';
		$this->country['249'] = 'Sudan';
		$this->country['597'] = 'Suriname';
		$this->country['268'] = 'Swaziland';
		$this->country['46'] = 'Sweden';
		$this->country['41'] = 'Switzerland';
		$this->country['963'] = 'Syria';
		$this->country['886'] = 'Taiwan';
		$this->country['992'] = 'Tajikistan';
		$this->country['255'] = 'Tanzania';
		$this->country['66'] = 'Thailand';
		$this->country['228'] = 'Togo Republic';
		$this->country['690'] = 'Tokelau';
		$this->country['676'] = 'Tonga Islands';
		$this->country['1868'] = 'Trinidad & Tobago';
		$this->country['216'] = 'Tunisia';
		$this->country['90'] = 'Turkey';
		$this->country['993'] = 'Turkmenistan';
		$this->country['1649'] = 'Turks & Caicos Island';
		$this->country['688'] = 'Tuvalu';
		$this->country['256'] = 'Uganda';
		$this->country['380'] = 'Ukraine';
		$this->country['971'] = 'United Arab Emirates';
		$this->country['44'] = 'United Kingdom';
		$this->country['598'] = 'Uruguay';
		$this->country['1 '] = 'USA/Canada';
		$this->country['998'] = 'Uzbekistan';
		$this->country['678'] = 'Vanuatu';
		$this->country['3966'] = 'Vatican City';
		$this->country['58'] = 'Venezuela';
		$this->country['84'] = 'Vietnam';
		$this->country['1340'] = 'Virgin Islands (US)';
		$this->country['681'] = 'Wallis/Futuna Islands';
		$this->country['967'] = 'Yemen Arab Republic';
		$this->country['260'] = 'Zambia';
		$this->country['263'] = 'Zimbabwe';
		$this->country[''] = JText::_('ACY_PHONE_NOCOUNTRY');
	}

	private function _displayCountry($value, $map, $displayCountry = 1){
		static $id = 0;
		$id++;
		$divCountryCode = '';
		if($id === 1){
			$divCountryCode .= '
			<style rel="stylesheet" type="text/css">
				.acymailing_divCountryCode{
					width:260px;
					height:200px;
					position:absolute;
					padding:20px !important;
					border:1px solid #aaaaaa;
					box-shadow: 2px 5px 10px #666	;
					background-color:white;
					overflow-x:hidden;
					z-index:5;
				}

				.acymailing_buttonCountryCode td{
					padding:0  !important;
				}

				.acymailing_module .acymailing_divCountryCode{
					width:260px;
					height:200px;
					overflow-y:scroll;
					position:absolute;
					padding:20px !important;
					border:1px solid #aaaaaa;
					box-shadow: 2px 5px 10px #666	;
					background-color:white;
					overflow-x:hidden;
				}

				.acymailing_buttonCountryCode{
					height:25px;
					padding:0 2px 0 5px;
					margin-bottom:1px;
					cursor:pointer;
					vertical-align:top;
					}


				.acymailing_divCountryCode .acymailing_countryLine{
					background-color: #ffffff;
					cursor:pointer;
					height:23px;
					width:260px;
					display:inline-block;
					white-space:nowrap;
				}

				.acymailing_divCountryCode .acymailing_countryLine span {
					display:inline-block;
				}

				.acymailing_divCountryCode .acymailing_countryLine:hover {
					background-color: #ededed;
				}

				.acymailing_divCountryCode .acymailing_countryLine span{
					padding:0 10px 0 0 !important;
					vertical-align:middle !important;
				}

				.acymailing_buttonCountryCode img.flag, .acymailing_divCountryCode img.flag{
					max-width:none;
					margin-right:5px;
					vertical-align:baseline;
				}

				.acymailing_buttonCountryCode span img
				{
					margin-top:0 !important;
					margin-bottom:0 !important;
					vertical-align:baseline;
				}
			</style>
			<script language="javascript" type="text/javascript">
			var acymailing_idbutton;
				function acymailing_displayDivCountryCode(id)
				{
					divCountryCode = document.getElementById("acymailing_divCountryCode"+id);
					styleDivBefore = divCountryCode.style.display;
					allDivCountryCode = document.getElementsByClassName("acymailing_divCountryCode");
					for (i=0; i<allDivCountryCode.length;i++)
					{
						allDivCountryCode[i].style.display="none";
					}
					acymailing_idbutton=id;

					if (styleDivBefore=="block")
						divCountryCode.style.display ="none"
					else
						divCountryCode.style.display ="block"

					document.getElementById("acymailing_searchACountry"+id).focus();
				}

				function acymailing_selectACountry(countryCode,countrySelected,positionX,positionY,id,displayCountry)
				{
					document.getElementById("acymailing_buttonCountryCodeImage"+id).style.backgroundPosition=positionX+"px "+positionY+"px";
					if (displayCountry == 1)
						document.getElementById("acymailing_buttonCountryValue"+id).innerHTML = countrySelected;
					document.getElementById("acymailing_divCountryCode"+id).style.display = "none";
					document.getElementById("acymailing_valueSelectedCountryCode"+id).value = "+"+countryCode;
				}

				function acymailing_searchACountry(idDivCountry)
				{
					divCountry = document.getElementById("acymailing_divCountryCode"+idDivCountry);
					filter = document.getElementById("acymailing_searchACountry"+idDivCountry).value.toLowerCase();
					countries = divCountry.getElementsByClassName("acymailing_countryLine");
					for(i=0;i<countries.length;i++)
					{
						countryName = countries[i].childNodes[1].innerHTML.toLowerCase();
						countryPrefix = countries[i].childNodes[2].innerHTML;
						if(countryName.indexOf(filter)>-1||countryPrefix.indexOf(filter)>-1)
							countries[i].style.display = "inline-block";
						else
							countries[i].style.display = "none";
					}

				}
				function acymailing_resetCountryCode(positionX, positionY,id)
				{
					document.getElementById("acymailing_buttonCountryCodeImage"+id).style.backgroundPosition=positionX+"px "+positionY+"px";
					document.getElementById("acymailing_divCountryCode"+id).style.display = "none";
					document.getElementById("acymailing_valueSelectedCountryCode"+id).value = "";

				}
			</script>';
		}
		$CountryCode = "";
		if($displayCountry == 1){
			if(!(empty($value))){
				$CountryCode .= '<td id="acymailing_buttonCountryValue'.$id.'">'.$this->country[substr($value, 1)].'</td>';
			}else{
				$CountryCode .= '<td id="acymailing_buttonCountryValue'.$id.'"></td>';
			}
		}

		if(empty($value)){
			$selectedFlagImage = '<img id="acymailing_buttonCountryCodeImage'.$id.'" class="flag" src="'.ACYMAILING_IMAGES.'blank.png" style="background:url('.ACYMAILING_IMAGES.'flags2.png)-160px -176px">';
		}else{
			$selectedFlagImage = '<img id="acymailing_buttonCountryCodeImage'.$id.'" class="flag" src="'.ACYMAILING_IMAGES.'blank.png" style="background:url('.ACYMAILING_IMAGES.'flags2.png)'.$this->flagPosition[substr($value, 1)]['x'].'px '.$this->flagPosition[substr($value, 1)]['y'].'px;">';
		}
		$divCountryCode .= '<input type="hidden" id="acymailing_valueSelectedCountryCode'.$id.'" name="'.$map.'" value="'.$value.'">';
		$divCountryCode .= '
		<button type="button" class="acymailing_buttonCountryCode" onclick="acymailing_displayDivCountryCode('.$id.')">
		'.$selectedFlagImage.'<img class="arrow" src="'.ACYMAILING_IMAGES.'arrow.png">'.$CountryCode.'
		</button>';

		$divCountryCode .= '<span class="acymailing_divCountryCode" id="acymailing_divCountryCode'.$id.'" style="display:none;  overflow-y:scroll !important;">';
		$divCountryCode .= '<span style="position:relative;"><input onkeyup="acymailing_searchACountry('.$id.')" type="text" style="width:100%; margin-bottom:5px;" placeholder="'.JText::_('ACY_SEARCH').'" id="acymailing_searchACountry'.$id.'" class="acymailing_searchACountry" autocomplete="off"></span>';

		foreach($this->country as $code => $country){
			if(isset($this->flagPosition[$code])){
				$image = '<img class="flag" src="'.ACYMAILING_IMAGES.'blank.png" style="background:url('.ACYMAILING_IMAGES.'flags2.png)'.$this->flagPosition[$code]['x'].'px '.$this->flagPosition[$code]['y'].'px;">';
				if($code != ''){
					$divCountryCode .= '<span class="acymailing_countryLine" onclick="acymailing_selectACountry('.$code.',\''.$country.'\','.$this->flagPosition[$code]['x'].','.$this->flagPosition[$code]['y'].',acymailing_idbutton,'.$displayCountry.')"><span>'.$image.'</span><span>'.$country.'</span><span style="color:#666; float:right; margin:2px 10px 0 0;">+'.$code.'</span></span>';
				}else{
					$divCountryCode .= '<span class="acymailing_countryLine" onclick="acymailing_resetCountryCode('.$this->flagPosition[$code]['x'].','.$this->flagPosition[$code]['y'].',acymailing_idbutton)"><span>'.$image.'</span><span>'.$country.'</span><span style="color:#666; float:right; margin:2px 10px 0 0;"></span></span>';
				}
			}
		}
		$divCountryCode .= '</span>';
		return $divCountryCode;
	}


	private function _displayPhone($field, $value, $map, $inside){
		$this->_generateCountryArray();

		$value = trim($value, ',');

		$mycountry = '';
		if(strpos($value, ',')){
			$mycountry = substr($value, 0, strpos($value, ','));
			$num = substr($value, strlen($mycountry) + 1);
		}elseif(strpos($value, ' ') > 1 && strpos($value, ' ') < 7){
			$mycountry = substr($value, 0, strpos($value, ' '));
			$num = substr($value, strlen($mycountry) + 1);
		}else{
			$num = $value;
			if(strpos($value, '+') === 0){
				$numChar = 4;
				while($numChar > 0){
					if(isset($this->country[substr($value, 1, $numChar)])){
						$mycountry = substr($value, 0, $numChar + 1);
						$num = substr($value, $numChar + 1);
						break;
					}
					$numChar--;
				}
			}
		}

		if(strpos($mycountry, '+') !== 0 && substr($mycountry, 0, 2) == '00'){
			$mycountry = str_replace('00', '+', $mycountry);
		}

		$style = array();
		$class = empty($field->required) ? ' class="inputbox"' : ' class="inputbox required"';
		if(!empty($field->options['size'])){
			$style[] = 'width:'.(is_numeric($field->options['size']) ? ($field->options['size'].'px') : $field->options['size']);
		}

		$styleline = ' style="vertical-align:bottom; margin-left:0;';
		$styleline .= empty($style) ? '"' : implode($style, ';').'"';
		if(!isset($this->country[trim($mycountry, '+')])){
			$mycountry = '';
			$num = $value;
		}

		$js = '';
		if($inside AND strlen($num) < 1){
			$num = $this->trans(@$field->fieldname);
			$valueInside = addslashes($num);
			$this->excludeValue[$field->namekey] = $valueInside;
			$js = 'onfocus="if(this.value == \''.$valueInside.'\') this.value = \'\';" onblur="if(this.value==\'\') this.value=\''.$valueInside.'\';"';
		}

		$countrycode = $this->_displayCountry($mycountry, $map.'[country]', 0);
		$inputphone = '<input type="text" id="'.$this->prefix.$field->namekey.$this->suffix.'" name="'.$map.'[num]" '.$js.$class.$styleline.' value="'.htmlspecialchars($num, ENT_COMPAT, 'UTF-8').'" title="'.$this->trans('PHONECAPTION').'" />';
		return $countrycode.' '.$inputphone;
	}

	private function _listingBirthday($field, $value){
		if(empty($value) || $value == '0000-00-00') return;
		if(empty($field->options['format'])) $field->options['format'] = "%d %m %Y";
		list($year, $month, $day) = explode('-', $value);
		return str_replace(array('%Y', '%m', '%d'), array($year, $month, $day), $field->options['format']);
	}

	function display($field, $value, $map, $inside = false){
		$app = JFactory::getApplication();
		if(!$app->isAdmin() && !empty($this->origin) && in_array($this->origin, array('module', 'subform', 'joomla'))){
			if((isset($field->options['editablecreate']) && $field->options['editablecreate'] == 0 && empty($this->currentUser)) || (isset($field->options['editablemodify']) && $field->options['editablemodify'] == 0 && !empty($this->currentUser))){
				return $this->listing($field, $value);
			}
		}

		if(empty($field->type)) return;
		$functionType = '_display'.ucfirst($field->type);

		if(method_exists($this, $functionType)){
			return $this->$functionType($field, $value, $map, $inside);
		}else{
			ob_start();
			$resultTrigger = $this->dispatcher->trigger('onAcyDisplayField_'.$field->type, array($field, $value, $map, $inside));
			return ob_get_clean();
		}
	}

	private function _displayFile($field, $value, $map, $inside){
		$style = array();
		if(!empty($field->options['size'])){
			$style[] = 'width:'.(is_numeric($field->options['size']) ? ($field->options['size'].'px') : $field->options['size']);
		}
		$styleline = empty($style) ? '' : ' style="'.implode($style, ';').'"';

		$id = str_replace(' ', '_', $this->prefix.$field->namekey.$this->suffix);
		$result = '<input type="file" id="'.$id.'" name="'.$map.'" '.$styleline.' title="'.$this->trans($field->fieldname).'"/>';
		if(empty($value)) return $result;
		$config = acymailing_config();
		$uploadFolder = trim(JPath::clean(html_entity_decode(acymailing_getFilesFolder())), DS.' ').DS;
		$fileName = str_replace('_', ' ', substr($value, strpos($value, '_')));
		$result .= ' <span class="fileuploaded"><a href="'.ACYMAILING_LIVE.str_replace(DS, '/', $uploadFolder).'userfiles/'.$value.'" target="_blank">'.$fileName.'</a></span>';
		return $result;
	}

	private function _displayGravatar($field, $value, $map, $inside){
		if(empty($value)){
			$url = 'https://www.gravatar.com/avatar/';
			if(!empty($this->currentUser) && !empty($this->currentUser->email)) $url .= md5(strtolower(trim($this->currentUser->email)));
			$url .= '?d=mm&s=120'; // Default picture (Mystery Man -> mm) and size 120px
			$img = '<img class="acymailing_user_avatar" src="'.$url.'" />';
			return $img.'<br/>'.$this->_displayFile($field, $value, $map, $inside);
		}else{
			$uploadFolder = trim(JPath::clean(html_entity_decode(acymailing_getFilesFolder())), DS.' ').DS;
			$img = '<img src="'.ACYMAILING_LIVE.str_replace(DS, '/', $uploadFolder).'userfiles/'.$value.'" style="width:120px;height:120px;" />';
			return $img.'<br/>'.$this->_displayFile($field, $value, $map, $inside).'<br/><input type="checkbox" name="'.$map.'" value="" id="delete'.$map.'" /> <label for="delete'.$map.'">'.JText::_('DELETE_PICT').'</label>';
		}
	}

	private function _displayText($field, $value, $map, $inside){
		$class = empty($field->required) ? 'class="inputbox"' : 'class="inputbox required"';
		$style = array();
		if(!empty($field->options['size'])){
			$style[] = 'width:'.(is_numeric($field->options['size']) ? ($field->options['size'].'px') : $field->options['size']);
		}
		$styleline = empty($style) ? '' : ' style="'.implode($style, ';').'"';
		$js = '';
		if($inside AND strlen($value) < 1){
			$value = $this->trans(@$field->fieldname);
			$valueInside = addslashes($value);
			$this->excludeValue[$field->namekey] = $valueInside;
			$js = 'onfocus="if(this.value == \''.$valueInside.'\') this.value = \'\';" onblur="if(this.value==\'\') this.value=\''.$valueInside.'\';"';
		}
		$id = str_replace(' ', '_', $this->prefix.$field->namekey.$this->suffix);
		return '<input id="'.$id.'" '.$styleline.' '.$js.' type="text" '.$class.' name="'.$map.'" value="'.htmlspecialchars($value, ENT_COMPAT, 'UTF-8').'" title="'.(!empty($field->fieldname) ? $this->trans($field->fieldname) : '').'"/>';
	}

	private function _displayTextarea($field, $value, $map, $inside){
		$class = empty($field->required) ? 'class="inputbox"' : 'class="inputbox required"';
		$js = '';
		if($inside AND strlen($value) < 1){
			$value = addslashes($this->trans($field->fieldname));
			$this->excludeValue[$field->namekey] = $value;
			$js = 'onfocus="if(this.value == \''.$value.'\') this.value = \'\';" onblur="if(this.value==\'\') this.value=\''.$value.'\';"';
		}
		$cols = empty($field->options['cols']) ? '' : 'cols="'.intval($field->options['cols']).'"';
		$rows = empty($field->options['rows']) ? '' : 'rows="'.intval($field->options['rows']).'"';
		return '<textarea '.$class.' id="'.$this->prefix.$field->namekey.$this->suffix.'" name="'.$map.'" '.$cols.' '.$rows.' '.$js.' title="'.$this->trans($field->fieldname).'" >'.$value.'</textarea>';
	}

	private function _listingTextarea($field, $value){
		$noHtml = strip_tags($value);
		if(strlen($noHtml) > 80){
			return substr($noHtml, 0, 77).'...';
		}
		return $value;
	}

	private function _listingSelectedvals(&$field, $values){
		$return = '';
		foreach($values as $value){
			if(isset($field->value[$value]->value)){
				$return .= ', '.$this->trans($field->value[$value]->value);
			}else $return .= ', '.$value;
		}

		return trim($return, ', ');
	}

	private function _listingSingledropdown($field, $value){
		return $this->_listingSelectedvals($field, array($value));
	}

	private function _listingMultipledropdown($field, $value){
		return $this->_listingSelectedvals($field, explode(',', $value));
	}

	private function _listingRadio($field, $value){
		return $this->_listingSelectedvals($field, array($value));
	}

	private function _listingCheckbox($field, $value){
		return $this->_listingSelectedvals($field, explode(',', $value));
	}

	private function _displayCustomtext($field, $value, $map, $inside){
		return $this->trans(@$field->options['customtext']);
	}

	private function _displayRadio($field, $value, $map, $inside){
		return $this->_displayRadioCheck($field, $value, $map, 'radio', $inside);
	}

	private function _displaySingledropdown($field, $value, $map, $inside){
		return $this->_displayDropdown($field, $value, $map, 'single', $inside);
	}

	private function _displayMultipledropdown($field, $value, $map, $inside){
		$value = explode(',', $value);
		return $this->_displayDropdown($field, $value, $map, 'multiple', $inside);
	}

	private function _displayDropdown($field, $value, $map, $type, $inside){
		$class = empty($field->required) ? '' : 'class="required"';
		$string = '';
		$style = array();
		if($type == "multiple"){
			$string .= '<input type="hidden" name="'.$map.'" value=" "/>'."\n";
			$map .= '[]';
			$arg = 'multiple="multiple"';
			if(!empty($field->options['size'])) $arg .= ' size="'.intval($field->options['size']).'"';
		}else{
			$arg = 'size="1"';
			if(!empty($field->options['size'])){
				$style[] = 'width:'.(is_numeric($field->options['size']) ? ($field->options['size'].'px') : $field->options['size']);
			}
		}

		$styleline = empty($style) ? '' : ' style="'.implode(';', $style).'"';
		$js = empty($field->filter_onchange) ? '' : ' onchange="'.$field->filter_onchange.'"';
		$string .= '<select '.$class.' id="'.$this->prefix.$field->namekey.$this->suffix.'" name="'.$map.'" '.$arg.$styleline.$js.' title="'.$this->trans($field->fieldname).'">'."\n";

		if(!empty($field->value)){
			foreach($field->value as $oneValue => $myValue){
				$selected = ((is_string($value) && (string)$oneValue === $value) || is_array($value) && in_array($oneValue, $value)) ? 'selected="selected"' : '';
				$id = str_replace(' ', '_', $this->prefix.$field->namekey.$this->suffix.'_'.$oneValue);
				$disabled = empty($myValue->disabled) ? '' : 'disabled="disabled"';
				$string .= '<option value="'.htmlspecialchars($oneValue, ENT_COMPAT, 'UTF-8').'" id="'.$id.'" '.$disabled.' '.$selected.' >'.$this->trans($myValue->value).'</option>'."\n";
			}
		}

		if(!empty($field->options['dbName']) && !empty($field->options['tableName']) && !empty($field->options['valueFromDb']) && !empty($field->options['titleFromDb'])){
			$valueField = acymailing_secureField($field->options['valueFromDb']);
			$titleField = acymailing_secureField($field->options['titleFromDb']);
			$fieldData = $this->_getDataFromDB($field, $valueField, $titleField);
			foreach($fieldData as $valueObject){
				$selected = ((is_string($value) && (string)$valueObject->$valueField === $value) || is_array($value) && in_array($valueObject->$valueField, $value)) ? 'selected="selected"' : '';
				$id = str_replace(' ', '_', $this->prefix.$field->namekey.$this->suffix.'_'.$valueObject->$valueField);
				$string .= '<option value="'.htmlspecialchars($valueObject->$valueField, ENT_COMPAT, 'UTF-8').'" id="'.$id.'" '.$selected.' >'.$this->trans($valueObject->$titleField).'</option>'."\n";
			}
		}

		$string .= '</select>';
		return $string;
	}

	private function _displayRadioCheck($field, $value, $map, $type, $inside){
		$string = '';
		if($inside) $string = $this->trans($field->fieldname).' ';
		if($type == 'checkbox'){
			$string .= '<input type="hidden" name="'.$map.'" value=" " />'."\n";
			$map .= '[]';
		}
		if(empty($field->value) && (empty($field->options['valueFromDb']) || empty($field->options['titleFromDb']) || empty($field->options['tableName']) || empty($field->options['dbName']))) return $string;

		if(!empty($field->value)){
			foreach($field->value as $oneValue => $myValue){
				$checked = ((is_string($value) AND $oneValue == $value) OR is_array($value) AND in_array($oneValue, $value)) ? 'checked="checked"' : '';
				$id = str_replace(' ', '_', $this->prefix.$field->namekey.$this->suffix.'_'.$oneValue);
				$disabled = empty($myValue->disabled) ? '' : 'disabled="disabled"';
				$string .= '<span id="span_'.$id.'"><label for="'.$id.'"><input type="'.$type.'" name="'.$map.'" value="'.htmlspecialchars($oneValue, ENT_COMPAT, 'UTF-8').'" id="'.$id.'" '.$disabled.' '.$checked.' title="'.$this->trans($field->fieldname).'"/> '.$this->trans($myValue->value).'</label></span>'."\n";
			}
		}

		if(!empty($field->options['valueFromDb']) && !empty($field->options['valueFromDb']) && !empty($field->options['tableName']) && !empty($field->options['dbName'])){
			$valueField = acymailing_secureField($field->options['valueFromDb']);
			$titleField = acymailing_secureField($field->options['titleFromDb']);
			$fieldData = $this->_getDataFromDB($field, $valueField, $titleField);
			foreach($fieldData as $valueObject){
				$checked = ((is_string($value) AND $valueObject->$valueField == $value) OR is_array($value) AND in_array($valueObject->$valueField, $value)) ? 'checked="checked"' : '';
				$id = str_replace(' ', '_', $this->prefix.$field->namekey.$this->suffix.'_'.$valueObject->$valueField);
				$string .= '<span id="span_'.$id.'"><label for="'.$id.'"><input type="'.$type.'" name="'.$map.'" value="'.htmlspecialchars($valueObject->$valueField, ENT_COMPAT, 'UTF-8').'" id="'.$id.'" '.$checked.' title="'.$this->trans($field->fieldname).'"/> '.$this->trans($valueObject->$titleField).'</label></span>'."\n";
			}
		}

		return $string;
	}

	private function _getDataFromDB($field, $valueField, $titleField){
		$tableName = acymailing_secureField($field->options['tableName']);
		$dbName = acymailing_secureField($field->options['dbName']);
		$whereCond = !empty($field->options['whereCond']) ? $field->options['whereCond'] : '';
		$whereOp = !empty($field->options['whereOperator']) ? $field->options['whereOperator'] : '';
		$whereValue = !empty($field->options['whereValue']) ? $field->options['whereValue'] : '';
		$orderByField = !empty($field->options['orderField']) ? acymailing_secureField($field->options['orderField']) : '';
		$orderByValue = !empty($field->options['orderValue']) ? acymailing_secureField($field->options['orderValue']) : '';

		if($dbName == 'current'){
			$this->database->setQuery('SELECT DATABASE()');
			$dbName = $this->database->loadResult();
		}
		$query = 'SELECT `'.$valueField.'`, `'.$titleField.'` FROM `'.$dbName.'`.`'.$tableName.'`';
		$query .= ' WHERE `'.$valueField.'`<>\'\' AND `'.$titleField.'`<>\'\'';
		if(!empty($whereValue) && !empty($whereCond)){
			$filterClass = acymailing_get('class.filter');
			$queryClass = new acyQuery();
			$query .= ' AND '.$queryClass->convertQuery($tableName, $whereCond, $whereOp, $whereValue);
		}
		$query .= ' GROUP BY `'.$valueField.'`, `'.$titleField.'`';
		$query .= !empty($orderByField) ? ' ORDER BY `'.$orderByField.'` '.$orderByValue : '';

		try{
			$this->database->setQuery($query);
			$res = $this->database->loadObjectList();
		}catch(Exception $e){
			acymailing_display($e->getMessage(), 'error');
			$res = array();
		}
		return $res;
	}

	private function _displayDate($field, $value, $map, $inside){
		if(empty($field->options['format'])) $field->options['format'] = "%Y-%m-%d";
		$style = array();
		if(!empty($field->options['size'])){
			$style[] = 'width:'.(is_numeric($field->options['size']) ? ($field->options['size'].'px') : $field->options['size']);
		}
		$styleForCalendar = array();
		if(!empty($style)) $styleForCalendar['style'] = implode($style, ';');

		if($inside AND strlen($value) < 1) $styleForCalendar['placeholder'] = $this->trans($field->fieldname);

		$styleForCalendar['title'] = empty($field->fieldname) ? '""' : $field->fieldname;
		if(!empty($field->required)) $styleForCalendar['class'] = 'required';

		if($value == '{now}' AND $map != 'data[fields][default]') $value = strftime($field->options['format'], time());

		if(strlen($value) > 0){
			if(ACYMAILING_J30){
				if(strtotime($value) !== false){
					$value = strftime('%Y-%m-%d', strtotime($value));
				}elseif(version_compare(phpversion(), '5.3.0', '>=')){
					$format = str_replace(array('%A', '%d', '%B', '%m', '%Y', '%y', '%H', '%M', '%S', '%a', '%I', '%p', '%w'), array('l', 'd', 'F', 'm', 'Y', 'y', 'H', 'i', 's', 'D', 'h', 'a', 'w'), $field->options['format']);
					$date = DateTime::createFromFormat($format, $value);
					if(is_object($date)){
						$value = $date->format('Y-m-d');
					}else{
						acymailing_enqueueMessage('The value specified for the date field does not match your date format or you specified a wrong format in the date field\'s configuration: '.$value, 'error');
						$value = '';
					}
				}else{
					$value = '';
					acymailing_enqueueMessage('The format specified for the date field does not allow the value to be displayed correctly, please change the format for the date field or update your PHP version to at least PHP 5.3', 'error');
				}
			}elseif(ACYMAILING_J16){
				$value = str_replace('/', '-', $value);
			}
		}
		$output = JHTML::_('calendar', $value, $map, $this->prefix.$field->namekey.$this->suffix, $field->options['format'], $styleForCalendar);
		return str_replace(array('<div ', '</div>'), array('<span ', '</span>'), $output);
	}

	private function _displayBirthday($field, $value, $map, $inside){
		$class = empty($field->required) ? '' : 'class="required"';
		if(empty($field->options['format'])) $field->options['format'] = "%d %m %Y";
		$vals = explode('-', $value);
		$days = array();
		$days[] = JHTML::_('select.option', '', JText::_('ACY_DAY'));
		for($i = 1; $i < 32; $i++) $days[] = JHTML::_('select.option', (strlen($i) == 1) ? '0'.$i : $i, $i);
		$years = array();
		$years[] = JHTML::_('select.option', '', JText::_('ACY_YEAR'));
		for($i = 1901; $i < date('Y') + 10; $i++) $years[] = JHTML::_('select.option', $i, $i);
		$months = array();
		$months[] = JHTML::_('select.option', '', JText::_('ACY_MONTH'));
		$months[] = JHTML::_('select.option', '01', JText::_('JANUARY'));
		$months[] = JHTML::_('select.option', '02', JText::_('FEBRUARY'));
		$months[] = JHTML::_('select.option', '03', JText::_('MARCH'));
		$months[] = JHTML::_('select.option', '04', JText::_('APRIL'));
		$months[] = JHTML::_('select.option', '05', JText::_('MAY'));
		$months[] = JHTML::_('select.option', '06', JText::_('JUNE'));
		$months[] = JHTML::_('select.option', '07', JText::_('JULY'));
		$months[] = JHTML::_('select.option', '08', JText::_('AUGUST'));
		$months[] = JHTML::_('select.option', '09', JText::_('SEPTEMBER'));
		$months[] = JHTML::_('select.option', '10', JText::_('OCTOBER'));
		$months[] = JHTML::_('select.option', '11', JText::_('NOVEMBER'));
		$months[] = JHTML::_('select.option', '12', JText::_('DECEMBER'));
		$dayField = JHTML::_('select.genericlist', $days, $map.'[day]', $class.' style="max-width:80px;" title="'.$this->trans('ACY_DAY').'"', 'value', 'text', @$vals[2], $this->prefix.$field->namekey.$this->suffix.'_day');
		$monthField = JHTML::_('select.genericlist', $months, $map.'[month]', $class.' style="max-width:130px;" title="'.$this->trans('ACY_MONTH').'"', 'value', 'text', @$vals[1], $this->prefix.$field->namekey.$this->suffix.'_month');
		$yearField = JHTML::_('select.genericlist', $years, $map.'[year]', $class.' style="max-width:100px;" title="'.$this->trans('ACY_YEAR').'"', 'value', 'text', intval(@$vals[0]), $this->prefix.$field->namekey.$this->suffix.'_year');

		return str_replace(array('%d', '%m', '%Y'), array($dayField, $monthField, $yearField), $field->options['format']);
	}

	private function _displayCheckbox($field, $value, $map, $inside){
		$value = explode(',', $value);
		return $this->_displayRadioCheck($field, $value, $map, 'checkbox', $inside);
	}

	function addJSFunctions(){
		$js = "
			function updateTablesDB(element,dbName){
				try{
					var ajaxCall = new Ajax('index.php?option=com_acymailing&tmpl=component&ctrl=fields&task=updateTablesDB&dbName='+dbName,{
						method: 'get',
						update: document.getElementById(element)
					}).request();

				}catch(err){
					new Request({
						url:'index.php?option=com_acymailing&tmpl=component&ctrl=fields&task=updateTablesDB&dbName='+dbName,
						method: 'get',
						onSuccess: function(responseText, responseXML) {
							document.getElementById(element).innerHTML = responseText;
						}
					}).send();
				}
			}
			function updateFieldBD(element, dbName, tableName, fieldType, defaultValue){
				try{
					var ajaxCall = new Ajax('index.php?option=com_acymailing&tmpl=component&ctrl=fields&task=updateFieldsDB&dbName='+dbName+'&tableName='+tableName+'&fieldType='+fieldType+'&defaultVal='+defaultValue,{
						method: 'get',
						update: document.getElementById(element)
					}).request();

				}catch(err){
					new Request({
						url:'index.php?option=com_acymailing&tmpl=component&ctrl=fields&task=updateFieldsDB&dbName='+dbName+'&tableName='+tableName+'&fieldType='+fieldType+'&defaultVal='+defaultValue,
						method: 'get',
						onSuccess: function(responseText, responseXML) {
							document.getElementById(element).innerHTML = responseText;
						}
					}).send();
				}
			}
		";

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);
	}

	public function prepareConditionalDisplay($fieldsDisplayed, $prefix, $page, $formid = 'adminForm'){
		$app = JFactory::getApplication();
		$precond = '';
		$fieldCond = '';
		foreach($fieldsDisplayed as $fieldName => $oneField){
			$i = 0;
			$currentFieldCond = '';
			do{
				if(empty($oneField->options['displim_field'.($i)])) break;
				$condFieldName = $oneField->options['displim_field'.($i)];
				$operator = $oneField->options['displim_ope'.($i)];
				$value = $oneField->options['displim_value'.($i)];
				if(is_numeric($value)){
					$finCond = $operator." ".$value;
				}else $finCond = $operator." '".$value."'";
				if(!array_key_exists($condFieldName, $fieldsDisplayed) && !in_array($page, array('userProfile', 'joomlaProfile'))) continue;
				if($page == 'userProfile' && !in_array($condFieldName, array('name', 'email', 'html')) && !array_key_exists($condFieldName, $fieldsDisplayed)) continue;
				if($i > 0){
					$currentFieldCond .= '
					 '.$oneField->options['displim_rel'.($i - 1)].' ';
				}

				$field = $this->get($condFieldName);
				if(in_array($condFieldName, array('email', 'name')) && in_array($page, array('userProfile', 'modifyForm'))){
					$currentFieldCond .= "((document.getElementsByName(prefix+'[".$condFieldName."]')[0] && document.getElementsByName(prefix+'[".$condFieldName."]')[0].value ".$finCond.")
						|| (document.getElementsByName('tr'+'".$condFieldName."')[0] && !document.getElementsByName(prefix+'[".$condFieldName."]')[0] && (document.getElementsByName('tr'+'".$condFieldName."')[0].value ".$finCond." || document.getElementsByName('tr'+'".$condFieldName."')[0].childNodes[1].innerText ".$finCond."))
						|| (document.getElementById('tr'+'".$condFieldName."') && !document.getElementsByName(prefix+'[".$condFieldName."]')[0] && !document.getElementsByName('tr'+'".$condFieldName."')[0] && (document.getElementById('tr'+'".$condFieldName."').value ".$finCond." || document.getElementById('tr'+'".$condFieldName."').childNodes[3].innerText ".$finCond.")))";
				}elseif(in_array($condFieldName, array('email', 'name')) && $page == 'joomlaProfile'){
					if(ACYMAILING_J25){
						$currentFieldCond .= "(document.getElementsByName('jform['+'".$condFieldName."]')[0] && document.getElementsByName('jform['+'".$condFieldName."]')[0].value ".$finCond.")";
					}else $currentFieldCond .= "(document.getElementsByName('".$condFieldName."')[0] && document.getElementsByName('".$condFieldName."')[0].value ".$finCond.")";
				}elseif($field->type == 'phone'){
					if($value != ''){
						$currentFieldCond .= "(getMyElement(varform, prefix+'[".$condFieldName."][num]', 'name') && (getMyElement(varform, prefix+'[".$condFieldName."][country]', 'name').value +','+ getMyElement(varform, prefix+'[".$condFieldName."][num]', 'name').value ".$finCond."))";
					}else $currentFieldCond .= "(getMyElement(varform, prefix+'[".$condFieldName."][num]', 'name') && (getMyElement(varform, prefix+'[".$condFieldName."][country]', 'name').value + getMyElement(varform, prefix+'[".$condFieldName."][num]', 'name').value ".$finCond."))";
				}elseif($field->type == 'birthday'){
					$currentFieldCond .= "(getMyElement(varform, prefix+'[".$condFieldName."][day]', 'name') ";
					$currentFieldCond .= " && (getMyElement(varform, prefix+'[".$condFieldName."][year]', 'name').value +";
					if($value != '') $currentFieldCond .= "'-'+";
					$currentFieldCond .= " getMyElement(varform, prefix+'[".$condFieldName."][month]', 'name').value +";
					if($value != '') $currentFieldCond .= "'-'+";
					$currentFieldCond .= " getMyElement(varform, prefix+'[".$condFieldName."][day]', 'name').value ";
					$currentFieldCond .= $finCond."))";
				}elseif($field->type == 'radio'){
					$precond .= "
					var tmpField = getMyElement(varform, prefix+'[".$condFieldName."]', 'name');
					if(tmpField.length > 0){
						precond['tmpField_".$fieldName."'] = '';
						for(var i = 0; i<tmpField.length;i++){
							if(tmpField[i].checked == true){
								precond['tmpField_".$fieldName."'] = tmpField[i].value;
								break;
							}
						}
					}";
					$currentFieldCond .= " (precond['tmpField_".$fieldName."'] ".$finCond.")";
				}else{
					$currentFieldCond .= "(getMyElement(varform, prefix+'[".$condFieldName."]','name') && getMyElement(varform, prefix+'[".$condFieldName."]','name').value ".$finCond.")";
				}
				$i++;
			}while(!empty($oneField->options['displim_rel'.($i - 1)]));
			if(!empty($currentFieldCond)){
				if(in_array($page, array('userProfile', 'modifyForm'))){
					$elem = "document.getElementById('tr".$oneField->namekey."')";
				}elseif($page == 'joomlaProfile') $elem = "document.getElementById('acy".$oneField->namekey."')";
				elseif($page == 'mod_vertical') $elem = "document.getElementById('acymailing_module_".$formid."').getElementsByClassName('acyfield_".$oneField->namekey."')[0].parentElement";
				elseif($page == 'mod_inline') $elem = "document.getElementById('acymailing_module_".$formid."').getElementsByClassName('acyfield_".$oneField->namekey."')[0]";
				elseif($page == 'mod_tableless') $elem = "document.getElementById('acymailing_module_".$formid."').getElementsByClassName('fieldacy".$oneField->namekey."')[0]";

				if($page == 'joomlaProfile' && ACYMAILING_J25 && !ACYMAILING_J30){
					$currentFieldCond = "
					if(".$currentFieldCond."){
						document.getElementById('field_".$oneField->namekey."').parentElement.style.display='';
						document.getElementById('field_".$oneField->namekey."').parentElement.previousSibling.style.display='';
					} else{
						document.getElementById('field_".$oneField->namekey."').parentElement.style.display='none';
						document.getElementById('field_".$oneField->namekey."').parentElement.previousSibling.style.display='none';
					}
					";
				}else{
					$currentFieldCond = "
					if(".$currentFieldCond."){
						".$elem.".style.display='';
					} else{
						".$elem.".style.display='none';
					}
					";
				}
				$fieldCond .= $currentFieldCond;
			}
		}
		if(empty($fieldCond)) return '';
		if(in_array($page, array('userProfile', 'modifyForm'))){
			$fctName = 'updateDisplayLimited_'.$page;
		}else $fctName = 'updateDisplayLimited_'.str_replace('-', '', $formid);
		$js = "
			function getMyElement(myform, field, type){
				if(type == 'name'){
					return myform.elements[field];
				} else{
					return myform[field];
				}
			}
			function ".$fctName."(prefix){
				varform = document['".$formid."'];
				if(!varform){
					varform = document.getElementById('".$formid."');
				} else if(!varform.elements && varform[0].elements){
					console.log('Warning: there are several forms with the same ID!');
					varform = varform[0];
				}
				";
		if(!empty($precond)) $js .= "precond = new Object(); ".$precond;
		$js .= "
					".$fieldCond."
			}
				";
		$jsEvent = "";
		if($page == 'modifyForm'){
			$jsEvent .= "var inputsTag = document.getElementById('acyuserinfo').getElementsByTagName('input');
				var selectsTag = document.getElementById('acyuserinfo').getElementsByTagName('select');";
		}elseif(in_array($page, array('mod_vertical', 'mod_inline', 'mod_tableless'))){
			$jsEvent .= "var inputsTag = document.getElementById('acymailing_module_".$formid."').getElementsByTagName('input');
				var selectsTag = document.getElementById('acymailing_module_".$formid."').getElementsByTagName('select');";
		}elseif($page == 'userProfile'){
			$jsEvent .= "var inputsTag = document.getElementById('acy_content').getElementsByTagName('input');
				var selectsTag = document.getElementById('acy_content').getElementsByTagName('select');";
		}elseif($page == 'joomlaProfile'){
			$jsEvent .= "if(document.getElementById('".$formid."')){
					var inputsTag = document.getElementById('".$formid."').getElementsByTagName('input');
					var selectsTag = document.getElementById('".$formid."').getElementsByTagName('select');
				} else{
					var inputsTag = document.getElementsByName('".$formid."')[0].getElementsByTagName('input');
					var selectsTag = document.getElementsByName('".$formid."')[0].getElementsByTagName('select');
				}";
		}
		$condProfileJoomla = '';
		if($page == 'joomlaProfile'){
			$condProfileJoomla = " || inputsTag[i].name.indexOf('name')>=0 || inputsTag[i].name.indexOf('email') >=0 ";
		}
		$jsEvent .= "
				for(var i = 0; i < inputsTag.length; i++) {
					if(inputsTag[i].name.substring(0,".strlen($prefix).") == '".$prefix."'".$condProfileJoomla."){
						if(inputsTag[i].type == 'radio'){ inputsTag[i].onchange = function(){ ".$fctName."('".$prefix."'); }; }
						else{ inputsTag[i].oninput = function(){ ".$fctName."('".$prefix."'); }; }
					}
				}
				for(var i = 0; i < selectsTag.length; i++) {
					if(selectsTag[i].name.substring(0,".strlen($prefix).") == '".$prefix."'){
						selectsTag[i].onchange = function(){ ".$fctName."('".$prefix."'); };
					}
				}
				".$fctName."('".$prefix."');
			";
		$js .= "
			try{
				window.addEvent('domready', function() {
					".$jsEvent."
				});
			} catch(err){
				jQuery(document).ready(function() {
					".$jsEvent."
				});
			}
		";
		return ($js);
	}

}
