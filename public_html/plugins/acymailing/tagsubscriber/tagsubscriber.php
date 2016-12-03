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

class plgAcymailingTagsubscriber extends JPlugin{

	var $fields = array();

	function __construct(&$subject, $config){
		parent::__construct($subject, $config);
		if(!isset($this->params)){
			$plugin = JPluginHelper::getPlugin('acymailing', 'tagsubscriber');
			$this->params = new acyParameter($plugin->params);
		}
	}

	function acymailing_getPluginType(){
		$app = JFactory::getApplication();
		if($this->params->get('frontendaccess') == 'none' && !$app->isAdmin()) return;
		$onePlugin = new stdClass();
		$onePlugin->name = JText::_('SUBSCRIBER_SUBSCRIBER');
		$onePlugin->function = 'acymailingtagsubscriber_show';
		$onePlugin->help = 'plugin-tagsubscriber';

		return $onePlugin;
	}

	function acymailingtagsubscriber_show(){
		$fields = acymailing_getColumns('#__acymailing_subscriber');

		$descriptions['subid'] = JText::_('SUBSCRIBER_ID');
		$descriptions['email'] = JText::_('SUBSCRIBER_EMAIL');
		$descriptions['name'] = JText::_('SUBSCRIBER_NAME');
		$descriptions['userid'] = JText::_('SUBSCRIBER_USERID');
		$descriptions['ip'] = JText::_('SUBSCRIBER_IP');
		$descriptions['created'] = JText::_('SUBSCRIBER_CREATED');
		echo '<br style="clear:both;"/>';
		if(JRequest::getVar('type') == 'notification'){
			$text = '<div class="onelineblockoptions">
						<span class="acyblocktitle">'.JText::_('CURRENT_USER_INFO').'</span>
						<table class="acymailing_table" cellpadding="1">';
			$k = 0;
			foreach($fields as $fieldname => $oneField){
				if(!isset($descriptions[$fieldname]) AND $oneField == 'tinyint') continue;
				if(empty($descriptions[$fieldname])) $descriptions[$fieldname] = '';

				$type = '';
				if(in_array($fieldname, array('created', 'confirmed_date', 'lastclick_date', 'lastsent_date', 'lastopen_date'))) $type = '|type:time';
				$text .= '<tr style="cursor:pointer" class="row'.$k.'" onclick="setTag(\'{user:'.$fieldname.$type.'}\');insertTag();" ><td class="acytdcheckbox"></td><td>'.$fieldname.'</td><td>'.$descriptions[$fieldname].'</td></tr>';
				$k = 1 - $k;
			}
			$text .= '</table></div>';
			echo $text;
		}

		$text = '<div class="onelineblockoptions">
					<span class="acyblocktitle">'.JText::_('RECEIVER_INFORMATION').'</span>
					<table class="acymailing_table" cellpadding="1">';

		$others = array();
		$others['{subtag:name|part:first|ucfirst}'] = array('name' => JText::_('SUBSCRIBER_FIRSTPART'), 'desc' => JText::_('SUBSCRIBER_FIRSTPART').' '.JText::_('SUBSCRIBER_FIRSTPART_DESC'));
		$others['{subtag:name|part:last|ucfirst}'] = array('name' => JText::_('SUBSCRIBER_LASTPART'), 'desc' => JText::_('SUBSCRIBER_LASTPART').' '.JText::_('SUBSCRIBER_LASTPART_DESC'));

		$k = 0;

		foreach($others as $tagname => $tag){
			$text .= '<tr style="cursor:pointer" class="row'.$k.'" onclick="setTag(\''.$tagname.'\');insertTag();" ><td class="acytdcheckbox"></td><td>'.$tag['name'].'</td><td>'.$tag['desc'].'</td></tr>';
			$k = 1 - $k;
		}

		foreach($fields as $fieldname => $oneField){
			if(!isset($descriptions[$fieldname]) AND $oneField == 'tinyint') continue;
			if(empty($descriptions[$fieldname])) $descriptions[$fieldname] = '';

			$type = '';
			if(in_array($fieldname, array('created', 'confirmed_date', 'lastclick_date', 'lastopen_date', 'lastsent_date'))) $type = '|type:time';
			$text .= '<tr style="cursor:pointer" class="row'.$k.'" onclick="setTag(\'{subtag:'.$fieldname.$type.'}\');insertTag();" ><td class="acytdcheckbox"></td><td>'.$fieldname.'</td><td>'.$descriptions[$fieldname].'</td></tr>';
			$k = 1 - $k;
		}

		$text .= '</table></div>';

		echo $text;
	}

	function acymailing_replaceusertags(&$email, &$user, $send = true){
		$this->pluginsHelper = acymailing_get('helper.acyplugins');
		$extractedTags = $this->pluginsHelper->extractTags($email, 'subtag');
		if(empty($extractedTags)) return;

		$tags = array();
		foreach($extractedTags as $i => $oneTag){
			if(isset($tags[$i])) continue;
			$tags[$i] = $this->replaceSubTag($oneTag, $user);
		}

		$this->pluginsHelper->replaceTags($email, $tags);
	}

	private function replaceSubTag(&$mytag, $user){
		if(!empty($mytag->juser)){
			$subClass = acymailing_get('class.subscriber');
			if(strpos($mytag->juser, '@') !== false){
				$userTmp = $subClass->get($mytag->juser);
			}else{
				$db = JFactory::getDBO();
				$query = "SELECT * FROM #__users WHERE username= ".$db->quote($mytag->juser);
				$db->setQuery($query);
				$JuserTmp = $db->loadObject();
				if(!empty($JuserTmp->email)) $userTmp = $subClass->get($JuserTmp->email);
			}
			$app = JFactory::getApplication();
			if(!empty($userTmp)){
				$user = $userTmp;
			}else $app->enqueueMessage('User not found for tag juser', 'warning');
		}

		$field = $mytag->id;
		if(empty($mytag->titlevalue)){
			$replaceme = (isset($user->$field) && strlen($user->$field) > 0) ? $user->$field : $mytag->default;
		}else{
			$fieldClass = acymailing_get('class.fields');
			if(!isset($this->fields[$field])){
				$this->fields[$field] = $fieldClass->get($field);
			}
			$replaceme = (isset($user->$field) && strlen($user->$field) > 0 && !empty($this->fields[$field]->value[$user->$field]->value)) ? $fieldClass->trans($this->fields[$field]->value[$user->$field]->value) : $mytag->default;
		}
		$replaceme = nl2br($replaceme);

		$this->pluginsHelper->formatString($replaceme, $mytag);

		return $replaceme;
	}

	function onAcyDisplayFilters(&$type, $context = "massactions"){

		if($this->params->get('displayfilter_'.$context, true) == false) return;

		$db = JFactory::getDBO();
		$fields = acymailing_getColumns('#__acymailing_subscriber');
		if(empty($fields)) return;

		$field = array();
		$field[] = JHTML::_('select.option', 0, '- - -');
		foreach($fields as $oneField => $fieldType){
			$field[] = JHTML::_('select.option', $oneField, $oneField);
		}
		$type['acymailingfield'] = JText::_('ACYMAILING_FIELD');

		$jsOnChange = "displayCondFilter('displaySubscriberValues', 'toChange__num__',__num__,'map='+document.getElementById('filter__num__acymailingfieldmap').value+'&cond='+document.getElementById('filter__num__acymailingfieldoperator').value+'&value='+document.getElementById('filter__num__acymailingfieldvalue').value); ";

		$operators = acymailing_get('type.operators');
		$operators->extra = 'onchange="'.$jsOnChange.'"';

		$return = '<div id="filter__num__acymailingfield">'.JHTML::_('select.genericlist', $field, "filter[__num__][acymailingfield][map]", 'onchange="'.$jsOnChange.'" class="inputbox" size="1"', 'value', 'text');
		$return .= ' '.$operators->display("filter[__num__][acymailingfield][operator]").' <span id="toChange__num__"><input onchange="countresults(__num__)" class="inputbox" type="text" name="filter[__num__][acymailingfield][value]" style="width:200px" value="" id="filter__num__acymailingfieldvalue"></span></div>';

		return $return;
	}

	function onAcyTriggerFct_displaySubscriberValues(){
		$num = JRequest::getInt('num');
		$map = JRequest::getCmd('map');
		$cond = JRequest::getVar('cond', '', '', 'string', JREQUEST_ALLOWHTML);
		$value = JRequest::getVar('value', '', '', 'string', JREQUEST_ALLOWHTML);

		$emptyInputReturn = '<input onchange="countresults('.$num.')" class="inputbox" type="text" name="filter['.$num.'][acymailingfield][value]" id="filter'.$num.'acymailingfieldvalue" style="width:200px" value="'.$value.'">';
		$dateInput = '<input onClick="displayDatePicker(this,event)" onchange="countresults('.$num.')" class="inputbox" type="text" name="filter['.$num.'][acymailingfield][value]" id="filter'.$num.'acymailingfieldvalue" style="width:200px" value="'.$value.'">';

		if(in_array($map, array('created', 'confirmed_date', 'lastopen_date', 'lastclick_date'))) return $dateInput;

		if(empty($map) || $map == 'key' || !in_array($cond, array('=', '!='))) return $emptyInputReturn;

		$db = JFactory::getDBO();
		$query = 'SELECT DISTINCT `'.acymailing_secureField($map).'` AS value FROM #__acymailing_subscriber LIMIT 100';
		$db->setQuery($query);
		$prop = $db->loadObjectList();

		if(empty($prop) || count($prop) >= 100 || (count($prop) == 1 && (empty($prop[0]->value) || $prop[0]->value == '-'))) return $emptyInputReturn;

		return JHTML::_('select.genericlist', $prop, "filter[$num][acymailingfield][value]", 'onchange="countresults('.$num.')" class="inputbox" size="1" style="width:200px"', 'value', 'value', $value, 'filter'.$num.'acymailingfieldvalue');
	}

	function onAcyDisplayFilter_acymailingfield($filter){
		return JText::_('ACYMAILING_FIELD').' : '.$filter['map'].' '.$filter['operator'].' '.$filter['value'];
	}

	function onAcyProcessFilter_acymailingfield(&$query, $filter, $num){
		if(empty($filter['map'])) return;
		$type = '';
		$value = acymailing_replaceDate($filter['value']);

		if(strpos($filter['value'], '{time}') !== false && !in_array($filter['map'], array('created', 'confirmed_date', 'lastclick_date', 'lastopen_date', 'lastsent_date'))){
			$value = strftime('%Y-%m-%d', $value);
		}

		if(in_array($filter['map'], array('created', 'confirmed_date', 'lastclick_date', 'lastopen_date', 'lastsent_date'))){
			if(!is_numeric($value)) $value = strtotime($value);
			$type = 'timestamp';
		}

		$query->where[] = $query->convertQuery('sub', $filter['map'], $filter['operator'], $value, $type);
	}

	function onAcyProcessFilterCount_acymailingfield(&$query, $filter, $num){
		$this->onAcyProcessFilter_acymailingfield($query, $filter, $num);
		return JText::sprintf('SELECTED_USERS', $query->count());
	}

	function onAcyDisplayActions(&$type){
		$type['acymailingfield'] = JText::_('BOUNCE_ACTION');
		$status = array();
		$status[] = JHTML::_('select.option', 'confirm', JText::_('CONFIRM_USERS'));
		$status[] = JHTML::_('select.option', 'unconfirm', JText::_('ACY_ACTION_UNCONFIRM'));
		$status[] = JHTML::_('select.option', 'enable', JText::_('ENABLE_USERS'));
		$status[] = JHTML::_('select.option', 'block', JText::_('BLOCK_USERS'));
		$status[] = JHTML::_('select.option', 'delete', JText::_('DELETE_USERS'));

		$content = '<div id="action__num__acymailingfield">'.JHTML::_('select.genericlist', $status, "action[__num__][acymailingfield][action]", 'class="inputbox" size="1"', 'value', 'text').'</div>';

		if(!acymailing_level(3)) return $content;

		$db = JFactory::getDBO();
		$fields = acymailing_getColumns('#__acymailing_subscriber');
		if(empty($fields)) return $content;

		$field = array();
		$field[] = JHTML::_('select.option', 0, '- - -');
		foreach($fields as $oneField => $fieldType){
			if(in_array($oneField, array('name', 'email', 'subid', 'created', 'ip'))) continue;
			$field[] = JHTML::_('select.option', $oneField, $oneField);
		}

		$jsOnChange = "if(document.getElementById('action__num__acymailingfieldvalvalue')!= undefined){ currentVal=document.getElementById('action__num__acymailingfieldvalvalue').value;} else{currentVal='';}
			displayCondFilter('displayFieldPossibleValues', 'toChangeAction__num__',__num__,'map='+document.getElementById('action__num__acymailingfieldvalmap').value+'&value='+currentVal+'&operator='+document.getElementById('action__num__acymailingfieldvaloperator').value); ";

		$operator = array();
		$operator[] = JHTML::_('select.option', '=', '=');
		$operator[] = JHTML::_('select.option', '+', '+');
		$operator[] = JHTML::_('select.option', '-', '-');
		$operator[] = JHTML::_('select.option', 'addend', JText::_('ACY_OPERATOR_ADDEND'));
		$operator[] = JHTML::_('select.option', 'addbegin', JText::_('ACY_OPERATOR_ADDBEGINNING'));

		$content .= '<div id="action__num__acymailingfieldval">'.JHTML::_('select.genericlist', $field, "action[__num__][acymailingfieldval][map]", 'onchange="'.$jsOnChange.'" class="inputbox" size="1"', 'value', 'text');
		$content .= ' '.JHTML::_('select.genericlist', $operator, "action[__num__][acymailingfieldval][operator]", 'onchange="'.$jsOnChange.'" class="inputbox" size="1" style="width:150px;"', 'value', 'text', '=');
		$content .= ' <span id="toChangeAction__num__"><input class="inputbox" type="text" id="action__num__acymailingfieldvalvalue" name="action[__num__][acymailingfieldval][value]" style="width:200px" value=""></span></div>';

		$type['acymailingfieldval'] = JText::_('SET_SUBSCRIBER_VALUE');

		return $content;
	}

	function onAcyTriggerFct_displayFieldPossibleValues(){
		$num = JRequest::getInt('num');
		$map = JRequest::getCmd('map');
		$value = JRequest::getString('value');
		$operator = JRequest::getString('operator');

		if(in_array($operator, array('addend', 'addbegin'))){
			$emptyInputReturn = '<textarea class="inputbox" type="text" name="action['.$num.'][acymailingfieldval][value]" id="action'.$num.'acymailingfieldvalvalue" style="width:200px">'.$value.'</textarea>';
		}else{
			$emptyInputReturn = '<input class="inputbox" type="text" name="action['.$num.'][acymailingfieldval][value]" id="action'.$num.'acymailingfieldvalvalue" style="width:200px" value="'.$value.'">';
		}

		if(empty($map) || $map == 'key' || $operator != '=') return $emptyInputReturn;

		$fieldClass = acymailing_get('class.fields');
		$myField = $fieldClass->get($map);
		if(empty($myField) || !in_array($myField->type, array('radio', 'checkbox', 'singledropdown', 'multipledropdown'))) return $emptyInputReturn;

		return $fieldClass->display($myField, '', 'action['.$num.'][acymailingfieldval][value]');
	}

	function onAcyProcessAction_acymailingfieldval($cquery, $action, $num){

		$value = is_array($action['value']) ? implode(',', $action['value']) : $action['value'];
		$replace = array('{year}', '{month}', '{weekday}', '{day}');
		$replaceBy = array(date('Y'), date('m'), date('N'), date('d'));
		$value = str_replace($replace, $replaceBy, $value);

		if(preg_match_all('#{(year|month|weekday|day)\|(add|remove):([^}]*)}#Uis', $value, $results)){
			foreach($results[0] as $i => $oneMatch){
				$format = str_replace(array('year', 'month', 'weekday', 'day'), array('Y','m','N','d'), $results[1][$i]);
				$delay = str_replace(array('add', 'remove'), array('+', '-'), $results[2][$i]).intval($results[3][$i]).' '.str_replace('weekday', 'day', $results[1][$i]);
				$value = str_replace($oneMatch, date($format, strtotime($delay)), $value);
			}
		}

		if(empty($action['operator'])) $action['operator'] = '=';

		preg_match_all('#(?:{|%7B)field:(.*)(?:}|%7D)#Ui', $value, $tags);
		$fields = array_keys(acymailing_getColumns('#__acymailing_subscriber'));
		if(!in_array($action['map'], $fields)) return 'Unexisting field: '.$action['map'].' | The available fields are: '.implode(', ', $fields);

		if(in_array($action['operator'], array('+', '-'))){
			if(empty($tags) || empty($tags[1])){
				$value = intval($value);
			}else{
				if(count($tags[1]) > 1 || substr($value, 0, 1) != '{' || substr($value, strlen($value) - 1, 1) != '}'){
					return 'You can\'t use more than one tag for the + and - operators (you also can\'t add or remove a value from the inserted tag for these two operators)';
				}
				if(!in_array($tags[1][0], $fields)) return 'Unexisting field: '.$tags[1][0].' | The available fields are: '.implode(', ', $fields);
				$value = 'sub.`'.acymailing_secureField($tags[1][0]).'`';
			}
		}else{
			$value = $cquery->db->Quote($value);
			if(!empty($tags)){
				foreach($tags[1] as $i => $oneField){
					if(!in_array($oneField, $fields)) return 'Unexisting field: '.$oneField.' | The available fields are: '.implode(', ', $fields);
					$value = str_replace($tags[0][$i], "', sub.`".acymailing_secureField($oneField)."`, '", $value);
				}
				$value = "CONCAT(".$value.")";
			}
		}

		$query = 'UPDATE #__acymailing_subscriber AS sub';
		if(!empty($cquery->join)) $query .= ' JOIN '.implode(' JOIN ', $cquery->join);
		if(!empty($cquery->leftjoin)) $query .= ' LEFT JOIN '.implode(' LEFT JOIN ', $cquery->leftjoin);

		if($action['operator'] == '='){
			$newValue = $value;
		}elseif(in_array($action['operator'], array('+', '-'))){
			$newValue = "sub.`".acymailing_secureField($action['map'])."` ".$action['operator']." ".$value;
		}elseif($action['operator'] == 'addend'){
			$newValue = "CONCAT(sub.`".acymailing_secureField($action['map'])."`, ".$value.")";
		}elseif($action['operator'] == 'addbegin'){
			$newValue = "CONCAT(".$value.", sub.`".acymailing_secureField($action['map'])."`)";
		}else{
			return 'Non existing operator: '.$action['operator'];
		}

		$query .= " SET sub.`".acymailing_secureField($action['map'])."` = ".$newValue;
		if(!empty($cquery->where)) $query .= ' WHERE ('.implode(') AND (', $cquery->where).')';

		$cquery->db->setQuery($query);
		$cquery->db->query();
		$nbAffected = $cquery->db->getAffectedRows();
		return JText::sprintf('NB_MODIFIED', $nbAffected);
	}

	function onAcyProcessAction_acymailingfield($cquery, $action, $num){

		$subClass = acymailing_get('class.subscriber');

		if($action['action'] == 'confirm'){
			$cquery->where['confirmed'] = 'sub.confirmed = 0';
			$cquery->db->setQuery($cquery->getQuery(array('sub.subid')));
			$allSubids = acymailing_loadResultArray($cquery->db);
			if(!empty($allSubids)){
				$subClass->sendConf = false;
				$subClass->sendWelcome = false;
				$subClass->sendNotif = false;
				foreach($allSubids as $oneId){
					$subClass->confirmSubscription($oneId);
				}
			}
			unset($cquery->where['confirmed']);
			return JText::sprintf('NB_CONFIRMED', count($allSubids));
		}

		if($action['action'] == 'enable'){
			$action['map'] = 'enabled';
			$action['value'] = 1;
			return $this->onAcyProcessAction_acymailingfieldval($cquery, $action, $num);
		}

		if($action['action'] == 'block'){
			$action['map'] = 'enabled';
			$action['value'] = 0;
			return $this->onAcyProcessAction_acymailingfieldval($cquery, $action, $num);
		}

		if($action['action'] == 'unconfirm'){
			$action['map'] = 'confirmed';
			$action['value'] = 0;
			return $this->onAcyProcessAction_acymailingfieldval($cquery, $action, $num);
		}

		if($action['action'] == 'delete'){
			$query = $cquery->getQuery(array('sub.subid'));
			$cquery->db->setQuery($query);
			$allSubids = acymailing_loadResultArray($cquery->db);
			$nbAffected = $subClass->delete($allSubids);
			return JText::sprintf('IMPORT_DELETE', $nbAffected);
		}

		return 'Filter AcyMailingField error, action not found : '.$action['action'];
	}
}//endclass
