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


class SendViewSend extends acymailingView{
	function display($tpl = null){
		$function = $this->getLayout();
		if(method_exists($this, $function)) $this->$function();

		parent::display($tpl);
	}

	function sendconfirm(){

		$mailid = acymailing_getCID('mailid');
		$mailClass = acymailing_get('class.mail');
		$listmailClass = acymailing_get('class.listmail');
		$queueClass = acymailing_get('class.queue');
		$mail = $mailClass->get($mailid);

		$values = new stdClass();
		$values->nbqueue = $queueClass->nbQueue($mailid);

		if(empty($values->nbqueue)){
			$lists = $listmailClass->getReceivers($mailid);
			$this->assignRef('lists', $lists);

			$db = JFactory::getDBO();
			$db->setQuery('SELECT count(subid) FROM `#__acymailing_userstats` WHERE `mailid` = '.intval($mailid));
			$values->alreadySent = $db->loadResult();
		}

		$this->assignRef('values', $values);
		$this->assignRef('mail', $mail);
	}

	function scheduleconfirm(){
		$this->chosen = false;

		$app = JFactory::getApplication();
		$mailid = acymailing_getCID('mailid');
		$listmailClass = acymailing_get('class.listmail');
		$mailClass = acymailing_get('class.mail');

		$listHours = array();
		$listMinutess = array();
		$defaultMinutes = ceil(acymailing_getDate(time(), '%M') / 5) * 5;
		$defaultHours = acymailing_getDate(time(), '%H');

		for($i = 0; $i < 24; $i++){
			$listHours[] = JHTML::_('select.option', $i, ($i < 10 ? '0'.$i : $i));
		}
		$hours = JHTML::_('select.genericlist', $listHours, 'sendhours', 'class="inputbox" size="1" style="width:60px;"', 'value', 'text', $defaultHours);
		for($i = 0; $i < 60; $i += 5){
			$listMinutess[] = JHTML::_('select.option', $i, ($i < 10 ? '0'.$i : $i));
		}
		$minutes = JHTML::_('select.genericlist', $listMinutess, 'sendminutes', 'class="inputbox" size="1" style="width:60px;"', 'value', 'text', $defaultMinutes);

		$db = JFactory::getDBO();
		$db->setQuery('SELECT count(subid) FROM `#__acymailing_userstats` WHERE `mailid` = '.intval($mailid));
		$alreadySent = $db->loadResult();

		$this->assignRef('alreadySent', $alreadySent);
		$this->assign('lists', $listmailClass->getReceivers($mailid));
		$this->assign('mail', $mailClass->get($mailid));
		$this->assign('hours', $hours);
		$this->assign('minutes', $minutes);
		$this->assign('app', $app);
	}

	function addqueue(){
		$subid = JRequest::getInt('subid');
		if(empty($subid)) exit;

		$subscriberClass = acymailing_get('class.subscriber');
		$subscriber = $subscriberClass->getFull($subid);

		$db = JFactory::getDBO();
		$app = JFactory::getApplication();
		if($app->isAdmin()){
			$db->setQuery("SELECT `mailid`, `subject`,`alias`, `type` FROM `#__acymailing_mail` WHERE `type` NOT IN ('notification','autonews') OR `alias` = 'confirmation' AND `published` = 1 ORDER BY `type`,`subject` ASC ");
		}else{
			$listClass = acymailing_get('class.list');
			$lists = $listClass->getFrontendLists();
			$listids = array(-1);
			foreach($lists as $oneList) $listids[] = intval($oneList->listid);
			$query = "SELECT m.`mailid`, m.`subject`, m.`alias`, m.`type` "."FROM `#__acymailing_mail` AS m "."LEFT JOIN #__acymailing_listmail AS lm "."ON lm.mailid = m.mailid "."LEFT JOIN #__acymailing_list AS l "."ON l.listid = lm.listid "."WHERE (m.`type` NOT IN ('notification','autonews') OR m.`alias` = 'confirmation') "."AND m.`published` = 1 "."AND (m.userid = ".intval($subscriber->userid)." OR l.listid IN (".implode(',', $listids).")) "."GROUP BY m.mailid "."ORDER BY m.`type`,m.`subject` ASC ";
			$db->setQuery($query);
		}
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
			$emailsToDisplay[] = JHTML::_('select.option', $oneMail->mailid, $oneMail->subject.' ('.$oneMail->mailid.' : '.$oneMail->alias.')');
		}
		$emailsToDisplay[] = JHTML::_('select.option', '</OPTGROUP>');

		$emaildrop = JHTML::_('select.genericlist', $emailsToDisplay, 'mailid', 'class="inputbox" size="1" style="width:300px;"', 'value', 'text', JRequest::getInt('mailid'));

		$listHours = array();
		$listMinutess = array();
		$defaultMinutes = ceil(acymailing_getDate(time(), '%M') / 5) * 5;
		$defaultHours = acymailing_getDate(time(), '%H');

		for($i = 0; $i < 24; $i++){
			$listHours[] = JHTML::_('select.option', $i, ($i < 10 ? '0'.$i : $i));
		}
		$hours = JHTML::_('select.genericlist', $listHours, 'sendhours', 'class="inputbox" size="1" style="width:60px;"', 'value', 'text', $defaultHours);
		for($i = 0; $i < 60; $i += 5){
			$listMinutess[] = JHTML::_('select.option', $i, ($i < 10 ? '0'.$i : $i));
		}
		$minutes = JHTML::_('select.genericlist', $listMinutess, 'sendminutes', 'class="inputbox" size="1" style="width:60px;"', 'value', 'text', $defaultMinutes);

		if($app->isAdmin()){
			$this->assignRef('subscriber', $subscriber);
			$this->assignRef('emaildrop', $emaildrop);
			$this->assign('hours', $hours);
			$this->assign('minutes', $minutes);
		}else{
			return array('subscriber' => $subscriber, 'emaildrop' => $emaildrop, 'hours' => $hours, 'minutes' => $minutes);
		}
	}

}
