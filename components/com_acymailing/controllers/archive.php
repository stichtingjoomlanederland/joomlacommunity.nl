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

class ArchiveController extends acymailingController{

	function view(){

		$statsClass = acymailing_get('class.stats');
		$statsClass->countReturn = false;
		$statsClass->saveStats();

		$printEnabled = JRequest::getVar('print', 0);
		if($printEnabled){
			$js = "setTimeout(function(){
					if(document.getElementById('iframepreview')){
						document.getElementById('iframepreview').contentWindow.focus();
						document.getElementById('iframepreview').contentWindow.print();
					}else{
						window.print();
					}
				},2000);";
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration($js);
		}

		JRequest::setVar('layout', 'view');
		return parent::display();
	}


	function sendautonews(){
		$mailid = JRequest::getInt('mailid');
		$security = JRequest::getCmd('key');

		list($key, $senddate) = explode('-', $security);

		if(empty($mailid)){
			acymailing_display('mailid not found', 'error');
			return;
		}

		if(empty($key)){
			acymailing_display('key not found', 'error');
			return;
		}

		if(empty($senddate)){
			acymailing_display('send date not found', 'error');
			return;
		}

		$mailClass = acymailing_get('class.mail');
		$newsletter = $mailClass->get($mailid);

		if(empty($newsletter->mailid)){
			acymailing_display('newsletter not found', 'error');
			return;
		}

		if($newsletter->senddate != $senddate){
			acymailing_display('Wrong send date', 'error');
			return;
		}

		if($newsletter->key != $key){
			acymailing_display('Wrong security key', 'error');
			return;
		}

		if($newsletter->published != 0){
			acymailing_display(JText::_('AUTONEWS_GENERATE_DONE'), 'warning');
			return;
		}

		$db = JFactory::getDBO();
		$db->setQuery('UPDATE '.acymailing_table('mail').' SET published = 1 WHERE mailid = '.intval($newsletter->mailid));
		$db->query();

		$queueClass = acymailing_get('class.queue');
		$totalSub = $queueClass->queue($mailid, time());

		acymailing_display(array(JText::sprintf('ADDED_QUEUE', $totalSub), JText::_('AUTOSEND_CONFIRMATION')), 'success');
	}

	function forward(){
		$config = acymailing_config();
		if(!$config->get('forward', true)) return $this->view();

		$key = JRequest::getCmd('key');
		$mailid = JRequest::getInt('mailid');

		$mailerHelper = acymailing_get('helper.mailer');
		$mailerHelper->loadedToSend = false;
		$mailtosend = $mailerHelper->load($mailid);

		if(empty($key) OR $mailtosend->key !== $key){
			return $this->view();
		}

		JRequest::setVar('layout', 'forward');
		return parent::display();
	}

	function doforward(){
		$config = acymailing_config();
		if(!$config->get('forward', true)) return $this->view();

		JRequest::checkToken() or die('Please make sure your cookies are enabled');
		acymailing_checkRobots();

		$history = acymailing_get('class.acyhistory');
		$forwardusers = JRequest::getVar('forwardusers', array());

		$forwardusers = array_slice($forwardusers, 0, 5, true);

		$sendername = JRequest::getString('sendername');
		$senderemail = strip_tags(JRequest::getString('senderemail'));
		$forwardmsg = nl2br(strip_tags(JRequest::getString('forwardmsg')));
		$forwardmsg = preg_replace('#http(s)?://[^ <]*(?= |<|$)#Uis', '', $forwardmsg);

		if(empty($sendername) || empty($senderemail)){
			echo "<script>alert('".JText::_('FILL_ALL', true)."'); window.history.go(-1);</script>";
			exit;
		}

		$userClass = acymailing_get('helper.user');
		foreach($forwardusers as $oneUser => $infos){
			if(empty($infos['email'])) continue;

			if(empty($infos['name'])){
				echo "<script>alert('".JText::_('FILL_ALL', true)."'); window.history.go(-1);</script>";
				exit;
			}
			if(!$userClass->validEmail($infos['email'], true)){
				echo "<script>alert('".JText::_('VALID_EMAIL', true)."'); window.history.go(-1);</script>";
				exit;
			}
		}

		$config = acymailing_config();
		if($config->get('forward', 0) == 2){
			$captchaClass = acymailing_get('class.acycaptcha');
			if($config->get('captcha_enabled', 0) == 0) $captchaClass->pluginName = 'acycaptcha';
			$captchaClass->state = 'acycaptchacomponent';
			if(!$captchaClass->check(JRequest::getString('acycaptcha'))){
				$captchaClass->returnError();
			}
		}

		$mailid = JRequest::getInt('mailid');
		if(empty($mailid)) return $this->view();

		$mailerHelper = acymailing_get('helper.mailer');
		$mailerHelper->checkConfirmField = false;
		$mailerHelper->checkEnabled = false;
		$mailerHelper->checkAccept = false;
		$mailerHelper->loadedToSend = true;

		$mailToForward = $mailerHelper->load($mailid);

		$key = JRequest::getCmd('key');

		if(empty($key) OR $mailToForward->key !== $key){
			return $this->view();
		}

		$subid = JRequest::getInt('subid');
		foreach($forwardusers as $oneUser => $infos){
			if(empty($infos['email'])) continue;
			$receiver = new stdClass();
			$receiver->email = $infos['email'];
			$receiver->subid = 0;
			$receiver->html = 1;
			$receiver->name = $infos['name'];

			$introtext = '<div align="center" style="max-width:600px;margin:auto;margin-top:10px;margin-bottom:10px;border:1px solid #cccccc;background-color:#f6f6f6;color:#333333;">'.JText::_('MESSAGE_TO_FORWARD').'</div> ';
			$values = array('{user:name}' => $sendername, '{user:email}' => $senderemail, '{forwardmsg}' => $forwardmsg);

			$mailerHelper->introtext = str_replace(array_keys($values), $values, $introtext);

			if($mailerHelper->sendOne($mailid, $receiver)){
				$db = JFactory::getDBO();
				$db->setQuery('UPDATE '.acymailing_table('stats').' SET `forward` = `forward` +1 WHERE `mailid` = '.(int)$mailid);
				$db->query();

				$data = array();
				$data['email'] = 'EMAIL::'.$receiver->email;
				$data['name'] = 'NAME::'.$receiver->name;
				$history->insert($subid, 'forward', $data, $mailid);
			}
		}

		$mailkey = '&key='.$key;
		$subid = JRequest::getCmd('subid');
		if(!empty($subid)) $userkey = '&subid='.$subid;

		if(JRequest::getCmd('tmpl', '') == 'component'){
			$tmpl = '&tmpl=component';
		}else{
			$tmpl = '';
		}

		$app = JFactory::getApplication();
		$url = 'archive&task=view&mailid='.$mailid.$mailkey.$userkey.$tmpl;
		$app->redirect(acymailing_completeLink($url, false, true));
	}

	function sendarchive(){

		$config = acymailing_config();
		if(!$config->get('show_receiveemail', 0)) return $this->listing();

		JRequest::checkToken() or die('Please make sure your cookies are enabled');
		acymailing_checkRobots();

		$receiveEmails = JRequest::getVar('receivemail', array(), '', 'array');

		$email = trim(JRequest::getString('email'));

		$userClass = acymailing_get('helper.user');
		if(!$userClass->validEmail($email, true)){
			echo "<script>alert('".JText::_('VALID_EMAIL', true)."'); window.history.go(-1);</script>";
			exit;
		}

		$captchaClass = acymailing_get('class.acycaptcha');
		$captchaClass->state = 'acycaptchacomponent';
		if(!$captchaClass->check(JRequest::getString('acycaptcha'))){
			$captchaClass->returnError();
		}

		JArrayHelper::toInteger($receiveEmails, array());

		$db = JFactory::getDBO();
		$db->setQuery("SELECT mailid FROM #__acymailing_mail WHERE mailid IN ('".implode("','", $receiveEmails)."') AND published = 1 AND visible = 1");
		$mailids = acymailing_loadResultArray($db);

		$receiver = new stdClass();
		$receiver->email = $email;
		$receiver->subid = 0;
		$receiver->html = 1;
		$receiver->name = trim(strip_tags(JRequest::getString('name', '')));

		$mailerHelper = acymailing_get('helper.mailer');
		$mailerHelper->checkConfirmField = false;
		$mailerHelper->checkEnabled = false;
		$mailerHelper->checkAccept = false;
		$mailerHelper->loadedToSend = true;

		foreach($mailids as $oneMailid){
			$mailerHelper->sendOne($oneMailid, $receiver);
		}

		return $this->listing();
	}

}
