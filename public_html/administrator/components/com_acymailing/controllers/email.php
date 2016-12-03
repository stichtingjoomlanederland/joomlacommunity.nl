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

class EmailController extends acymailingController{
	function test(){

		$this->store();

		$mailHelper = acymailing_get('helper.mailer');

		$user = JFactory::getUser();
		$receiver = $user->email;
		$mailid = acymailing_getCID('mailid');

		$mailHelper->report = false;
		$result = $mailHelper->sendOne($mailid, $receiver);
		acymailing_enqueueMessage($mailHelper->reportMessage, $result ? 'success' : 'error');

		return $this->edit();
	}

	function store(){

		JRequest::checkToken() or die('Invalid Token');

		$oldMailid = acymailing_getCID('mailid');

		$mailClass = acymailing_get('class.mail');

		if($mailClass->saveForm()){
			$data = JRequest::getVar('data');
			$type = @$data['mail']['type'];
			if(!empty($type) AND in_array($type, array('unsub', 'welcome'))){
				$subject = addslashes($data['mail']['subject']);
				$mailid = JRequest::getInt('mailid');
				if($type == 'unsub'){
					$js = "var mydrop = window.top.document.getElementById('datalistunsubmailid'); ";
					$js .= "var type = 'unsub';";
				}else{ //type=welcome
					$js = "var mydrop = window.top.document.getElementById('datalistwelmailid'); ";
					$js .= "var type = 'welcome';";
				}
				if(empty($oldMailid)){
					$js .= 'var optn = document.createElement("OPTION");';
					$js .= "optn.text = '[$mailid] $subject'; optn.value = '$mailid';";
					$js .= 'mydrop.options.add(optn);';
					$js .= 'lastid = 0; while(mydrop.options[lastid+1]){lastid = lastid+1;} mydrop.selectedIndex = lastid;';
					$js .= 'window.top.changeMessage(type,'.$mailid.');';
				}else{
					$js .= "lastid = 0; notfound = true; while(notfound && mydrop.options[lastid]){if(mydrop.options[lastid].value == $mailid){mydrop.options[lastid].text = '[$mailid] $subject';notfound = false;} lastid = lastid+1;}";
				}
				if(ACYMAILING_J30) $js .= 'window.top.jQuery("#datalist'.($type == 'unsub' ? 'unsub' : 'wel').'mailid").trigger("liszt:updated");';
				$doc = JFactory::getDocument();
				$doc->addScriptDeclaration($js);
			}
			acymailing_enqueueMessage(JText::_('JOOMEXT_SUCC_SAVED'), 'success');
		}else{
			acymailing_enqueueMessage(JText::_('ERROR_SAVING'), 'error');
		}
	}//endfct store
}//endclass
