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

class plgAcymailingTagcbuser extends JPlugin{
	var $sendervalues = array();

	function __construct(&$subject, $config){
		parent::__construct($subject, $config);
		if(!isset($this->params)){
			$plugin = JPluginHelper::getPlugin('acymailing', 'tagcbuser');
			$this->params = new JParameter($plugin->params);
		}
	}

	function acymailing_getPluginType(){

		$app = JFactory::getApplication();
		if(!file_exists(ACYMAILING_ROOT.'components'.DS.'com_comprofiler'.DS.'comprofiler.php')) return;
		if($this->params->get('frontendaccess') == 'none' && !$app->isAdmin()) return;
		$onePlugin = new stdClass();
		$onePlugin->name = JText::_('CB User');
		$onePlugin->function = 'acymailingtagcb_show';
		$onePlugin->help = 'plugin-tagcbuser';

		return $onePlugin;
	}

	function onAcyDisplayFilters(&$type, $context = "massactions"){

		if($this->params->get('displayfilter_'.$context, true) == false) return;
		if(!file_exists(ACYMAILING_ROOT.'components'.DS.'com_comprofiler'.DS.'comprofiler.php')) return;

		$db = JFactory::getDBO();
		$fields = acymailing_getColumns('#__comprofiler');
		if(empty($fields)) return;

		$db->setQuery('SELECT name,title FROM #__comprofiler_fields WHERE `table` LIKE '.$db->Quote('#__comprofiler'));
		$fieldTitles = $db->loadObjectList('name');

		$languages = array();
		if(file_exists(JPATH_SITE.DS.'components'.DS.'com_comprofiler'.DS.'plugin'.DS.'language'.DS.'default_language'.DS.'language.php')){
			if(!defined('CBLIB')) include_once(JPATH_SITE.DS.'libraries/CBLib/CB/Application/CBApplication.php');
			$languages = include_once JPATH_SITE.DS.'components'.DS.'com_comprofiler'.DS.'plugin'.DS.'language'.DS.'default_language'.DS.'language.php';
		}elseif(file_exists(JPATH_SITE.DS.'components'.DS.'com_comprofiler'.DS.'plugin'.DS.'language'.DS.'default_language'.DS.'default_language.php')){
			include_once JPATH_SITE.DS.'components'.DS.'com_comprofiler'.DS.'plugin'.DS.'language'.DS.'default_language'.DS.'default_language.php';
		}

		ksort($fields);
		$cbfield = array();
		foreach($fields as $oneField => $fieldType){
			$text = $oneField;
			if(!empty($fieldTitles[$oneField])){
				if(!empty($languages[$fieldTitles[$oneField]->title])){
					$text .= ' ('.$languages[$fieldTitles[$oneField]->title].')';
				}else{
					if(defined($fieldTitles[$oneField]->title)){
						$text .= ' ('.constant($fieldTitles[$oneField]->title).')';
					}else $text .= ' ('.$fieldTitles[$oneField]->title.')';
				}
			}
			$cbfield[] = JHTML::_('select.option', $oneField, $text);
		}
		$type['cbfield'] = JText::_('CB_FIELD');

		$operators = acymailing_get('type.operators');
		$operators->extra = 'onchange="countresults(__num__)"';

		$return = '<div id="filter__num__cbfield">'.JHTML::_('select.genericlist', $cbfield, "filter[__num__][cbfield][map]", 'class="inputbox" size="1" onchange="countresults(__num__)"', 'value', 'text');
		$return .= ' '.$operators->display("filter[__num__][cbfield][operator]").' <input onchange="countresults(__num__)" class="inputbox" type="text" name="filter[__num__][cbfield][value]" style="width:200px" value="" /></div>';

		return $return;
	}

	function onAcyProcessFilter_cbfield(&$query, $filter, $num){
		$query->leftjoin['cbfield'] = '#__comprofiler AS cbfield ON cbfield.id = sub.userid';
		$query->where[] = $query->convertQuery('cbfield', $filter['map'], $filter['operator'], $filter['value']);
	}

	function onAcyProcessFilterCount_cbfield(&$query, $filter, $num){
		$this->onAcyProcessFilter_cbfield($query, $filter, $num);
		return JText::sprintf('SELECTED_USERS', $query->count());
	}

	function acymailingtagcb_show(){
		?>

		<script language="javascript" type="text/javascript">
			function applyTag(tagname){
				var string = '{cbtag:' + tagname;
				for(var i = 0; i < document.adminForm.typeinfo.length; i++){
					if(document.adminForm.typeinfo[i].checked){
						string += '|info:' + document.adminForm.typeinfo[i].value;
					}
				}
				string += '}';
				setTag(string);
				insertTag();
			}
		</script>
		<?php
		$typeinfo = array();
		$typeinfo[] = JHTML::_('select.option', "receiver", JText::_('RECEIVER_INFORMATION'));
		$typeinfo[] = JHTML::_('select.option', "sender", JText::_('SENDER_INFORMATIONS'));
		echo JHTML::_('acyselect.radiolist', $typeinfo, 'typeinfo', '', 'value', 'text', 'receiver');

		$text = '<table class="acymailing_table" cellpadding="1">';
		$db = JFactory::getDBO();
		$fields = acymailing_getColumns('#__comprofiler');

		$db->setQuery('SELECT name,type FROM #__comprofiler_fields');
		$fieldType = $db->loadObjectList('name');

		$k = 0;

		$text .= '<tr style="cursor:pointer" class="row1" onclick="applyTag(\'thumb\');" ><td class="acytdcheckbox"></td><td>Thumb Avatar</td></tr>';
		foreach($fields as $fieldname => $oneField){
			$type = '';
			if(strpos(strtolower($oneField), 'date') !== false) $type = '|type:date';
			if(!empty($fieldType[$fieldname]) AND $fieldType[$fieldname]->type == 'image') $type = '|type:image';
			$text .= '<tr style="cursor:pointer" class="row'.$k.'" onclick="applyTag(\''.$fieldname.$type.'\');" ><td class="acytdcheckbox"></td><td>'.$fieldname.'</td></tr>';
			$k = 1 - $k;
		}


		$db->setQuery("SELECT * FROM #__comprofiler_fields WHERE tablecolumns = '' AND published = 1");
		$otherFields = $db->loadObjectList();
		foreach($otherFields as $oneField){
			$text .= '<tr style="cursor:pointer" class="row'.$k.'" onclick="applyTag(\'cbapi_'.$oneField->name.'\');" ><td class="acytdcheckbox"></td><td>'.$oneField->name.'</td></tr>';
			$k = 1 - $k;
		}

		$text .= '</table>';

		echo $text;
	}

	function acymailing_replaceusertags(&$email, &$user, $send = true){
		$match = '#(?:{|%7B)cbtag:(.*)(?:}|%7D)#Ui';
		$variables = array('subject', 'body', 'altbody');
		$found = false;
		foreach($variables as $var){
			if(empty($email->$var)) continue;
			$found = preg_match_all($match, $email->$var, $results[$var]) || $found;
			if(empty($results[$var][0])) unset($results[$var]);
		}

		if(!$found) return;

		$uservalues = null;
		$db = JFactory::getDBO();
		if(!empty($user->userid)){
			$db->setQuery('SELECT * FROM '.acymailing_table('comprofiler', false).' WHERE user_id = '.$user->userid.' LIMIT 1');
			$uservalues = $db->loadObject();
		}

		$db->setQuery('SELECT fieldid, `table`, name, type, params FROM #__comprofiler_fields');
		$fieldObjects = $db->loadObjectList('name');

		include_once(ACYMAILING_ROOT.'administrator'.DS.'components'.DS.'com_comprofiler'.DS.'plugin.foundation.php');
		cbimport('cb.database');
		$pluginsHelper = acymailing_get('helper.acyplugins');
		$currentCBUser = null;

		$tags = array();
		foreach($results as $var => $allresults){
			foreach($allresults[0] as $i => $oneTag){
				if(isset($tags[$oneTag])) continue;

				$arguments = explode('|', $allresults[1][$i]);
				$field = $arguments[0];
				unset($arguments[0]);
				$mytag = new stdClass();
				$mytag->default = $this->params->get('default_'.$field, '');
				if(!empty($arguments)){
					foreach($arguments as $onearg){
						$args = explode(':', $onearg);
						if(isset($args[1])){
							$mytag->{$args[0]} = $args[1];
						}else{
							$mytag->{$args[0]} = 1;
						}
					}
				}

				$values = new stdClass();

				if(!empty($mytag->info) AND $mytag->info == 'sender'){
					if(empty($this->sendervalues[$email->mailid]) AND !empty($email->userid)){
						$db->setQuery('SELECT * FROM #__comprofiler WHERE user_id = '.$email->userid.' LIMIT 1');
						$this->sendervalues[$email->mailid] = $db->loadObject();
					}
					if(!empty($this->sendervalues[$email->mailid])) $values = $this->sendervalues[$email->mailid];
				}else{
					$values = $uservalues;
				}

				if(substr($field, 0, 6) == 'cbapi_'){
					if(!empty($mytag->info) AND $mytag->info == 'sender'){
						if(empty($this->sendervalues[$email->mailid]->$field) AND !empty($email->userid)){
							$currentSender = CBuser::getInstance($email->userid);
							$values->$field = $currentSender->getField(substr($field, 6), $mytag->default, 'html', 'none', 'profile', 0, true);
							$this->sendervalues[$email->mailid]->$field = $values->$field;
						}elseif(!empty($this->sendervalues[$email->mailid]->$field)){
							$values->$field = @$this->sendervalues[$email->mailid]->$field;
						}
					}elseif(!empty($user->userid)){
						if(empty($currentCBUser)) $currentCBUser = CBuser::getInstance($user->userid);
						if(!empty($currentCBUser)) $values->$field = $currentCBUser->getField(substr($field, 6), $mytag->default, 'html', 'none', 'profile', 0, true);
						if(empty($values->$field) && !empty($fieldObjects[substr($field, 6)]) && $fieldObjects[substr($field, 6)]->type == 'progress'){
							$fieldObjects[substr($field, 6)]->decodedParams = json_decode($fieldObjects[substr($field, 6)]->params);
							if(!empty($fieldObjects[substr($field, 6)]->decodedParams->prg_fields)){
								$requiredFields = explode('|*|', $fieldObjects[substr($field, 6)]->decodedParams->prg_fields);
								$filled_in = 0;
								foreach($fieldObjects as $oneField){
									if(!in_array($oneField->fieldid, $requiredFields) || !in_array($oneField->table, array('#__comprofiler', '#__users'))) continue;
									$fieldName = $oneField->name;
									if(!empty($currentCBUser->_cbuser->$fieldName)) $filled_in++;
								}
								$values->$field = intval(($filled_in * 100) / count($requiredFields)).'%';
							}
						}
					}
				}

				$replaceme = isset($values->$field) ? $values->$field : $mytag->default;
				if(!empty($mytag->type)){
					if($mytag->type == 'image' AND !empty($replaceme)){
						$replaceme = '<img src="'.ACYMAILING_LIVE.'images/comprofiler/'.$replaceme.'" alt="'.htmlspecialchars(@$user->name, ENT_COMPAT, 'UTF-8').'" />';
					}
				}

				if($field == 'thumb'){
					$replaceme = '<img src="'.ACYMAILING_LIVE.'images/comprofiler/tn'.$values->avatar.'" alt="'.htmlspecialchars(@$user->name, ENT_COMPAT, 'UTF-8').'" />';
				}elseif($field == 'avatar'){
					$replaceme = '<img src="'.ACYMAILING_LIVE.'images/comprofiler/'.$values->avatar.'" alt="'.htmlspecialchars(@$user->name, ENT_COMPAT, 'UTF-8').'" />';
				}

				$tags[$oneTag] = $replaceme;
				$pluginsHelper->formatString($tags[$oneTag], $mytag);
			}
		}

		foreach($results as $var => $allresults){
			$email->$var = str_replace(array_keys($tags), $tags, $email->$var);
		}
	}

	function onAcyDisplayActions(&$type){
		$fields = acymailing_getColumns('#__comprofiler');

		$field = array();
		$field[] = JHTML::_('select.option', 0, '- - -');
		foreach($fields as $oneField => $fieldType){
			if(in_array($oneField, array('id', 'user_id', 'hits', 'message_last_sent', 'message_number_sent', 'canvas', 'cbactivation'))) continue;
			$field[] = JHTML::_('select.option', $oneField, $oneField);
		}

		$content = '<div id="action__num__cbfieldval">'.JHTML::_('select.genericlist', $field, "action[__num__][cbfieldval][map]", 'class="inputbox" size="1"', 'value', 'text');
		$content .= ' = <input class="inputbox" type="text" id="action__num__cbfieldvalvalue" name="action[__num__][cbfieldval][value]" style="width:200px" value=""></div>';

		$type['cbfieldval'] = 'Community Builder: '.jtext::_('FIELD');

		return $content;
	}

	function onAcyProcessAction_cbfieldval($cquery, $action, $num){

		$replace = array('{year}', '{month}', '{weekday}', '{day}', '{hour}', '{minute}');
		$replaceBy = array(date('Y'), date('m'), date('N'), date('d'), date('H'), date('i'));
		$newValue = str_replace($replace, $replaceBy, acymailing_replaceDate($action['value']));

		if(preg_match_all('#{(year|month|weekday|day)\|(add|remove):([^}]*)}#Uis', $newValue, $results)){
			foreach($results[0] as $i => $oneMatch){
				$format = str_replace(array('year', 'month', 'weekday', 'day'), array('Y','m','N','d'), $results[1][$i]);
				$delay = str_replace(array('add', 'remove'), array('+', '-'), $results[2][$i]).intval($results[3][$i]).' '.str_replace('weekday', 'day', $results[1][$i]);
				$newValue = str_replace($oneMatch, date($format, strtotime($delay)), $newValue);
			}
		}

		if(empty($action['operator'])) $action['operator'] = '=';

		$fields = array_keys(acymailing_getColumns('#__comprofiler'));
		if(!in_array($action['map'], $fields)) return 'Unexisting field: '.$action['map'].' | The available fields are: '.implode(', ', $fields);

		$query = 'UPDATE #__comprofiler AS cb JOIN #__acymailing_subscriber AS sub ON cb.user_id = sub.userid';
		if(!empty($cquery->join)) $query .= ' JOIN '.implode(' JOIN ', $cquery->join);
		if(!empty($cquery->leftjoin)) $query .= ' LEFT JOIN '.implode(' LEFT JOIN ', $cquery->leftjoin);

		$query .= " SET cb.`".acymailing_secureField($action['map'])."` = ".$cquery->db->Quote($newValue);
		if(!empty($cquery->where)) $query .= ' WHERE ('.implode(') AND (', $cquery->where).')';

		$cquery->db->setQuery($query);
		$cquery->db->query();
		$nbAffected = $cquery->db->getAffectedRows();
		return JText::sprintf('NB_MODIFIED', $nbAffected);
	}
}//endclass
