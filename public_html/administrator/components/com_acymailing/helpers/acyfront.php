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

class acyfrontHelper{

}

function acyCheckAccessList(){
	$listid = JRequest::getInt('listid');
	if(empty($listid)) return false;
	$my = JFactory::getUser();
	$listClass = acymailing_get('class.list');
	$myList = $listClass->get($listid);
	if(empty($myList->listid)) die('Invalid List');
	if(!empty($my->id) AND (int)$my->id == (int)$myList->userid) return true;
	if(empty($my->id) OR $myList->access_manage =='none') return false;
	if($myList->access_manage != 'all'){
		if(!acymailing_isAllowed($myList->access_manage)) return false;
	}
	return true;
}

function acyCheckEditUser(){
	$listid = JRequest::getInt('listid');
	$subid = acymailing_getCID('subid');

	if(empty($subid)) return true;

	$db = JFactory::getDBO();
	$db->setQuery('SELECT status FROM #__acymailing_listsub WHERE subid='.intval($subid).' AND listid = '.intval($listid));
	$status = $db->loadResult();
	if(empty($status)) return false;

	return true;
}

function acyCheckEditNewsletter($edit = true){
	$mailid = acymailing_getCID('mailid');

	$listClass = acymailing_get('class.list');
	$lists = $listClass->getFrontendLists();
	$frontListsIds = array();
	foreach($lists as $oneList){
		$frontListsIds[] = $oneList->listid;
	}

	if(empty($mailid)) return true;

	$db = JFactory::getDBO();

	$db->setQuery('SELECT * FROM `#__acymailing_mail` WHERE mailid = '.intval($mailid));
	$mail = $db->loadObject();
	if(empty($mail->mailid)) return false;
	$config = acymailing_config();
	$my = JFactory::getUser();
	if($edit AND !$config->get('frontend_modif',1) AND $my->id != $mail->userid) return false;
	if($edit AND !$config->get('frontend_modif_sent',1) AND !empty($mail->senddate)) return false;

	$db->setQuery('SELECT `mailid` FROM `#__acymailing_listmail` WHERE `mailid` = '.intval($mailid).' AND `listid` IN ('.implode(',',$frontListsIds).')');
	$result = $db->loadResult();
	if(empty($result) && $my->id != $mail->userid) return false;

	return true;
}
