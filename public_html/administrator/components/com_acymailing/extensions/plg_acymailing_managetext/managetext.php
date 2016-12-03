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
defined('_JEXEC') or die('Restricted access');

class plgAcymailingManagetext extends JPlugin{
	var $foundtags = array();

	function __construct(&$subject, $config){
		parent::__construct($subject, $config);
		if(!isset($this->params)){
			$plugin = JPluginHelper::getPlugin('acymailing', 'managetext');
			$this->params = new acyParameter($plugin->params);
		}
	}

	function acymailing_replacetags(&$email, $send = true){
		$this->_replaceConstant($email);
		$this->_replaceRandom($email);
	}

	function acymailing_replaceusertags(&$email, &$user, $send = true){
		$this->_removetext($email);
		$this->_addfooter($email);
		$this->_ifstatement($email, $user);
	}

	private function _replaceConstant(&$email){
		$acypluginsHelper = acymailing_get('helper.acyplugins');
		$tags = $acypluginsHelper->extractTags($email, '(?:const|trans|config)');
		if(empty($tags)) return;

		$jconfig = JFactory::getConfig();

		$tagsReplaced = array();
		foreach($tags as $i => $oneTag){
			$val = '';
			$arrayVal = array();
			foreach($oneTag as $valname => $oneValue){
				if($valname == 'id'){
					$val = trim(strip_tags($oneValue));
				}elseif($valname != 'default'){
					$arrayVal[] = '{'.$valname.'}';
				}
			}

			if(empty($val)) continue;
			$tagValues = explode(':', $i);
			$type = ltrim($tagValues[0], '{');
			if($type == 'const'){
				$tagsReplaced[$i] = defined($val) ? constant($val) : 'Constant not defined : '.$val;
			}elseif($type == 'config'){
				if($val == 'sitename'){
					$tagsReplaced[$i] = ACYMAILING_J30 ? $jconfig->get($val) : $jconfig->getValue('config.'.$val);
				}
			}else{
				static $done = false;
				if(!$done && strpos($val, 'COM_USERS') !== false){
					$done = true;
					$lang = JFactory::getLanguage();
					$lang->load('com_users', JPATH_SITE);
					$lang->load('com_users', JPATH_ADMINISTRATOR);
				}
				if(!empty($arrayVal)){
					$tagsReplaced[$i] = nl2br(vsprintf(JText::_($val), $arrayVal));
				}else{
					$tagsReplaced[$i] = JText::_($val);
				}
			}
		}

		$acypluginsHelper->replaceTags($email, $tagsReplaced, true);
	}

	private function _replaceRandom(&$email){
		$pluginHelper = acymailing_get('helper.acyplugins');
		$randTag = $pluginHelper->extractTags($email, "rand");
		if(empty($randTag)) return;
		foreach($randTag as $oneRandTag){
			$results[$oneRandTag->id] = explode(';', $oneRandTag->id);
			$randNumber = rand(0, count($results[$oneRandTag->id]) - 1);
			$results[$oneRandTag->id][count($results[$oneRandTag->id])] = $results[$oneRandTag->id][$randNumber];
		}

		$tags = array();
		foreach(array_keys($results) as $oneResult){
			$tags['{rand:'.$oneResult.'}'] = end($results[$oneResult]);
		}

		if(empty($tags)) return;
		$pluginHelper->replaceTags($email, $tags, true);
	}


	private function _ifstatement(&$email, $user, $loop = 1){
		if(isset($this->noIfStatementTags[$email->mailid])) return;

		$isAdmin = JFactory::getApplication()->isAdmin();

		if($loop > 3){
			if($isAdmin) acymailing_display('You cannot have more than 3 nested {if} tags.', 'warning');
			return;
		}

		$match = '#{if:(((?!{if).)*)}(((?!{if).)*){/if}#Uis';
		$variables = array('subject', 'body', 'altbody', 'From', 'FromName', 'ReplyTo');
		$found = false;
		foreach($variables as $var){
			if(empty($email->$var)) continue;
			if(is_array($email->$var)){
				foreach($email->$var as $i => &$arrayField){
					if(empty($arrayField) || !is_array($arrayField)) continue;
					foreach($arrayField as $key => &$oneval){
						$found = preg_match_all($match, $oneval, $results[$var.$i.'-'.$key]) || $found;
						if(empty($results[$var.$i.'-'.$key][0])) unset($results[$var.$i.'-'.$key]);
					}
				}
			}else{
				$found = preg_match_all($match, $email->$var, $results[$var]) || $found;
				if(empty($results[$var][0])) unset($results[$var]);
			}
		}

		if(!$found){
			if($loop == 1) $this->noIfStatementTags[$email->mailid] = true;
			return;
		}

		static $a = false;

		$tags = array();
		foreach($results as $var => $allresults){
			foreach($allresults[0] as $i => $oneTag){
				if(isset($tags[$oneTag])) continue;
				if(!preg_match('#^([^=!<>~]+)(=|!=|<|>|&gt;|&lt;|~|!~)([^=!<>~]+)$#i', $allresults[1][$i], $operators)){
					if($isAdmin) acymailing_display('Operation not found : '.$allresults[1][$i], 'error');
					$tags[$oneTag] = $allresults[3][$i];
					continue;
				};
				$field = trim($operators[1]);
				$prop = '';

				$operatorsParts = explode('.', $operators[1]);
				$operatorComp = 'acymailing';
				if(count($operatorsParts) == 2 && in_array($operatorsParts[0], array('acymailing', 'joomla', 'var'))){
					$operatorComp = $operatorsParts[0];
					$field = $operatorsParts[1];
				}

				if($operatorComp == 'joomla'){
					if(!empty($user->userid)){
						if($field == 'gid' && ACYMAILING_J16){
							$db = JFactory::getDBO();
							$db->setQuery('SELECT group_id FROM #__user_usergroup_map WHERE user_id = '.intval($user->userid));
							$prop = implode(';', acymailing_loadResultArray($db));
						}else{
							$db = JFactory::getDBO();
							$db->setQuery('SELECT * FROM #__users WHERE id = '.intval($user->userid));
							$juser = $db->loadObject();
							if(isset($juser->{$field})){
								$prop = strtolower($juser->{$field});
							}else{
								if($isAdmin && !$a) acymailing_display('User variable not set : '.$field.' in '.$allresults[1][$i], 'error');
								$a = true;
							}
						}
					}
				}elseif($operatorComp == 'var'){
					$prop = strtolower($field);
				}else{
					if(!isset($user->{$field})){
						if($isAdmin && !$a) acymailing_display('User variable not set : '.$field.' in '.$allresults[1][$i], 'error');
						$a = true;
					}else{
						$prop = strtolower($user->{$field});
					}
				}

				$tags[$oneTag] = '';
				$val = trim(strtolower($operators[3]));
				if($operators[2] == '=' && ($prop == $val || in_array($prop, explode(';', $val)) || in_array($val, explode(';', $prop)))){
					$tags[$oneTag] = $allresults[3][$i];
				}elseif($operators[2] == '!=' && $prop != $val){
					$tags[$oneTag] = $allresults[3][$i];
				}elseif(($operators[2] == '>' || $operators[2] == '&gt;') && $prop > $val){
					$tags[$oneTag] = $allresults[3][$i];
				}elseif(($operators[2] == '<' || $operators[2] == '&lt;') && $prop < $val){
					$tags[$oneTag] = $allresults[3][$i];
				}elseif($operators[2] == '~' && strpos($prop, $val) !== false){
					$tags[$oneTag] = $allresults[3][$i];
				}elseif($operators[2] == '!~' && strpos($prop, $val) === false){
					$tags[$oneTag] = $allresults[3][$i];
				}
			}
		}

		foreach($variables as &$var){
			if(empty($email->$var)) continue;
			if(is_array($email->$var)){
				foreach($email->$var as &$arrayField){
					if(empty($arrayField) || !is_array($arrayField)) continue;
					foreach($arrayField as &$oneval){
						$oneval = str_replace(array_keys($tags), $tags, $oneval);
					}
				}
			}else{
				$email->$var = str_replace(array_keys($tags), $tags, $email->$var);
			}
		}
		$this->_ifstatement($email, $user, $loop + 1);
	}

	private function _removetext(&$email){
		$removetext = $this->params->get('removetext', '{reg},{/reg},{pub},{/pub}');
		if(!empty($removetext)){
			$removeArray = explode(',', trim($removetext, ' ,'));
			if(!empty($email->body)) $email->body = str_replace($removeArray, '', $email->body);
			if(!empty($email->altbody)) $email->altbody = str_replace($removeArray, '', $email->altbody);
		}


		$removetags = $this->params->get('removetags', 'youtube');
		if(!empty($removetags)){
			$regex = array();
			$removeArray = explode(',', trim($removetags, ' ,'));
			foreach($removeArray as $oneTag){
				if(empty($oneTag)) continue;
				$regex[] = '#(?:{|%7B)'.preg_quote($oneTag, '#').'(?:}|%7D).*(?:{|%7B)/'.preg_quote($oneTag, '#').'(?:}|%7D)#Uis';
				$regex[] = '#(?:{|%7B)'.preg_quote($oneTag, '#').'[^}]*(?:}|%7D)#Uis';
			}

			if(!empty($email->body)) $email->body = preg_replace($regex, '', $email->body);
			if(!empty($email->altbody)) $email->altbody = preg_replace($regex, '', $email->altbody);
		}
	}

	private function _addfooter(&$email){
		$footer = $this->params->get('footer');
		if(!empty($footer)){
			if(strpos($email->body, '</body>')){
				$email->body = str_replace('</body>', '<br />'.$footer.'</body>', $email->body);
			}else{
				$email->body .= '<br />'.$footer;
			}

			if(!empty($email->altbody)){
				$email->altbody .= "\n".$footer;
			}
		}
	}

	function onAcyDisplayFilters(&$type, $context = "massactions"){
		$app = JFactory::getApplication();
		if($this->params->get('displayfilter_'.$context, true) == false || ($this->params->get('frontendaccess') == 'none' && !$app->isAdmin())) return;

		$type['limitrand'] = JText::sprintf('ACY_RAND_LIMIT', 'X');

		$return = '<div id="filter__num__limitrand">'.JText::sprintf('ACY_RAND_LIMIT', '<input type="text" style="width:60px" value="30" name="filter[__num__][limitrand][nbusers]" />').'</div>';

		return $return;
	}

	function onAcyDisplayFilter_limitrand($filter){
		return JText::sprintf('ACY_RAND_LIMIT', $filter['nbusers']);
	}


	function onAcyProcessFilter_limitrand(&$query, $filter, $num){
		$query->limit = intval($filter['nbusers']);
		$query->orderBy = 'RAND()';
	}

	function onAcyDisplayActions(&$type){
		$type['addqueue'] = JText::_('ADD_QUEUE');
		$type['removequeue'] = JText::_('REMOVE_QUEUE');

		$db = JFactory::getDBO();
		$db->setQuery("SELECT `mailid`,`subject`, `type` FROM `#__acymailing_mail` WHERE `type` NOT IN ('notification','autonews','joomlanotification') OR `alias` = 'confirmation' ORDER BY `type`,`senddate` DESC LIMIT 5000");
		$allEmails = $db->loadObjectList();

		$emailsToDisplay = array();
		$typeNews = '';
		foreach($allEmails as $oneMail){
			if($oneMail->type != $typeNews){
				if(!empty($typeNews)) $emailsToDisplay[] = JHTML::_('select.option', '</OPTGROUP>');
				$typeNews = $oneMail->type;
				if($oneMail->type == 'news'){
					$label = JText::_('NEWSLETTERS');
				}elseif($oneMail->type == 'followup'){
					$label = JText::_('FOLLOWUP');
				}elseif($oneMail->type == 'welcome'){
					$label = JText::_('MSG_WELCOME');
				}elseif($oneMail->type == 'unsub'){
					$label = JText::_('MSG_UNSUB');
				}else{
					$label = $oneMail->type;
				}
				$emailsToDisplay[] = JHTML::_('select.option', '<OPTGROUP>', $label);
			}
			$emailsToDisplay[] = JHTML::_('select.option', $oneMail->mailid, $oneMail->subject.' ['.$oneMail->mailid.']');
		}
		$emailsToDisplay[] = JHTML::_('select.option', '</OPTGROUP>');

		$addqueue = '<div id="action__num__addqueue">'.JHTML::_('select.genericlist', $emailsToDisplay, "action[__num__][addqueue][mailid]", 'class="inputbox" size="1"').'<br /><label for="addqueuesenddate__num__">'.JText::_('SEND_DATE').' </label> <input type="text" value="{time}" id="addqueuesenddate__num__" name="action[__num__][addqueue][senddate]" onclick="displayDatePicker(this,event)"/></div>';

		$allMessages = JHTML::_('select.option', 0, JText::_('ACY_ALL'));
		array_unshift($emailsToDisplay, $allMessages);
		$removequeue = '<div id="action__num__removequeue">'.JHTML::_('select.genericlist', $emailsToDisplay, "action[__num__][removequeue][mailid]", 'class="inputbox" size="1"').'</div>';
		return $addqueue.$removequeue;
	}

	function onAcyProcessAction_addqueue($cquery, $action, $num){
		$action['mailid'] = intval($action['mailid']);
		if(empty($action['mailid'])) return 'Mailid not valid';
		if(empty($action['senddate'])) return 'Send date not valid';

		$action['senddate'] = acymailing_replaceDate($action['senddate']);
		if(!is_numeric($action['senddate'])) $action['senddate'] = acymailing_getTime($action['senddate']);
		if(empty($action['senddate'])) return 'send date not valid';

		$query = 'INSERT IGNORE INTO `#__acymailing_queue` (`mailid`,`subid`,`senddate`,`priority`) '.$cquery->getQuery(array($action['mailid'], 'sub.`subid`', $action['senddate'], '2'));
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$db->query();
		return JText::sprintf('ADDED_QUEUE', $db->getAffectedRows());
	}

	function onAcyProcessAction_removequeue($cquery, $action, $num){
		$action['mailid'] = intval($action['mailid']);
		if(!empty($action['mailid'])) $cquery->where['queueremove'] = 'queueremove.mailid = '.$action['mailid'];

		$query = 'DELETE queueremove.* FROM `#__acymailing_queue` as queueremove ';
		$query .= 'JOIN `#__acymailing_subscriber` as sub ON queueremove.subid = sub.subid ';
		if(!empty($cquery->join)) $query .= ' JOIN '.implode(' JOIN ', $cquery->join);
		if(!empty($cquery->leftjoin)) $query .= ' LEFT JOIN '.implode(' LEFT JOIN ', $cquery->leftjoin);
		if(!empty($cquery->where)) $query .= ' WHERE ('.implode(') AND (', $cquery->where).')';

		$db = JFactory::getDBO();
		$db->setQuery($query);
		$db->query();

		unset($cquery->where['queueremove']);

		return JText::sprintf('SUCC_DELETE_ELEMENTS', $db->getAffectedRows());
	}

	function onAcyProcessAction_displayUsers($cquery, $action, $num){

		$res = array();
		$res['countTotal'] = $cquery->count();

		if(empty($cquery->limit) || $cquery->limit > 50) $cquery->limit = 20;

		$query = $cquery->getQuery(array('sub.`subid`', 'sub.email', 'sub.name'));
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$users = $db->loadObjectList();

		$res['users'] = $users;
		return $res;
	}

}//endclass
