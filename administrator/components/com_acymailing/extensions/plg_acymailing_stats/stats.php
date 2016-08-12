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

class plgAcymailingStats extends JPlugin{
	function __construct(&$subject, $config){
		parent::__construct($subject, $config);
		if(!isset($this->params)){
			$plugin = JPluginHelper::getPlugin('acymailing', 'stats');
			$this->params = new acyParameter($plugin->params);
		}
	}

	function acymailing_replaceusertags(&$email, &$user, $send = true){
		if(!empty($email->altbody)){
			$email->altbody = str_replace(array('{statpicture}', '{nostatpicture}'), '', $email->altbody);
		}

		if(!$email->sendHTML OR empty($email->type) OR !in_array($email->type, array('news', 'autonews', 'followup', 'welcome', 'unsub', 'joomlanotification', 'action')) OR strpos($email->body, '{nostatpicture}')){
			$email->body = str_replace(array('{statpicture}', '{nostatpicture}'), '', $email->body);
			return;
		}

		if(empty($user->subid) || !$send){
			$pictureLink = ACYMAILING_LIVE.$this->params->get('picture', 'media/com_acymailing/images/statpicture.png');
		}else{
			$config = acymailing_config();
			$itemId = $config->get('itemid', 0);
			$item = empty($itemId) ? '' : '&Itemid='.$itemId;
			$pictureLink = acymailing_frontendLink('index.php?option=com_acymailing&ctrl=stats&mailid='.$email->mailid.'&subid='.$user->subid.$item);
		}

		$widthsize = $this->params->get('width', 50);
		$heightsize = $this->params->get('height', 1);
		$width = empty($widthsize) ? '' : ' width="'.$widthsize.'" ';
		$height = empty($heightsize) ? '' : ' height="'.$heightsize.'" ';

		$statPicture = '<img class="spict" alt="'.$this->params->get('alttext', '').'" src="'.$pictureLink.'"  border="0" '.$height.$width.'/>';

		if(strpos($email->body, '{statpicture}')){
			$email->body = str_replace('{statpicture}', $statPicture, $email->body);
		}elseif(strpos($email->body, '</body>')) $email->body = str_replace('</body>', $statPicture.'</body>', $email->body);
		else $email->body .= $statPicture;
	}//endfct

	function acymailing_getstatpicture(){
		return $this->params->get('picture', 'media/com_acymailing/images/statpicture.png');
	}

	function onAcyDisplayTriggers(&$triggers){
		$triggers['opennews'] = JText::_('ON_OPEN_NEWS');
	}

	function onAcyDisplayFilters(&$type, $context = "massactions"){

		if($context != "massactions" AND !$this->params->get('displayfilter_'.$context, false)) return;

		$type['deliverstat'] = JText::_('STATISTICS');

		$db = JFactory::getDBO();
		$db->setQuery("SELECT `mailid`,CONCAT(`subject`,' [',".$db->Quote(JText::_('ACY_ID').' ').", CAST(`mailid` AS char),']') as 'value' FROM `#__acymailing_mail` WHERE `type` IN('news','welcome','unsub','followup','notification','joomlanotification') ORDER BY `senddate` DESC LIMIT 5000");
		$allemails = $db->loadObjectList();
		$element = new stdClass();
		$element->mailid = 0;
		$element->value = JText::_('EMAIL_NAME');
		array_unshift($allemails, $element);

		$actions = array();
		$actions[] = JHTML::_('select.option', 'open', JText::_('OPEN'));
		$actions[] = JHTML::_('select.option', 'notopen', JText::_('NOT_OPEN'));
		$actions[] = JHTML::_('select.option', 'failed', JText::_('FAILED'));
		if(acymailing_level(3)) $actions[] = JHTML::_('select.option', 'bounce', JText::_('BOUNCES'));
		$actions[] = JHTML::_('select.option', 'htmlsent', JText::_('SENT_HTML'));
		$actions[] = JHTML::_('select.option', 'textsent', JText::_('SENT_TEXT'));
		$actions[] = JHTML::_('select.option', 'notsent', JText::_('NOT_SENT'));

		$return = '<div id="filter__num__deliverstat">'.JHTML::_('select.genericlist', $actions, "filter[__num__][deliverstat][action]", 'class="inputbox" onchange="countresults(__num__)" size="1"', 'value', 'text');
		$return .= ' '.JHTML::_('select.genericlist', $allemails, "filter[__num__][deliverstat][mailid]", 'onchange="countresults(__num__)" class="inputbox" size="1" style="max-width:200px"', 'mailid', 'value').'</div>';

		return $return;
	}

	function onAcyProcessFilterCount_deliverstat(&$query, $filter, $num){
		$this->onAcyProcessFilter_deliverstat($query, $filter, $num);
		return JText::sprintf('SELECTED_USERS', $query->count());
	}

	function onAcyProcessFilter_deliverstat(&$query, $filter, $num){
		$alias = 'stats'.$num;
		$jl = '#__acymailing_userstats AS '.$alias.' ON '.$alias.'.subid = sub.subid';
		if(!empty($filter['mailid'])) $jl .= ' AND '.$alias.'.mailid = '.intval($filter['mailid']);

		$query->leftjoin[$alias] = $jl;

		if($filter['action'] == 'open'){
			$where = $alias.'.open > 0';
		}elseif($filter['action'] == 'notopen'){
			$where = $alias.'.open = 0';
		}elseif($filter['action'] == 'failed'){
			$where = $alias.'.fail = 1';
		}elseif($filter['action'] == 'bounce'){
			$where = $alias.'.bounce = 1';
		}elseif($filter['action'] == 'htmlsent'){
			$where = $alias.'.html = 1';
		}elseif($filter['action'] == 'textsent'){
			$where = $alias.'.html = 0';
		}elseif($filter['action'] == 'notsent'){
			$where = $alias.'.subid IS NULL';
		}

		$query->where[] = $where;
	}
}//endclass
