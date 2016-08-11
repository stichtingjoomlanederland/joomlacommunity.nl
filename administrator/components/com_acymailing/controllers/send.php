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

class SendController extends acymailingController{

	function sendready(){
		if(!$this->isAllowed('newsletters', 'send')) return;
		JRequest::setVar('layout', 'sendconfirm');
		return parent::display();
	}

	function send(){
		if(!$this->isAllowed('newsletters', 'send')) return;
		JRequest::checkToken() or die('Invalid Token');

		JRequest::setVar('tmpl', 'component');
		$mailid = acymailing_getCID('mailid');
		if(empty($mailid)) exit;

		$user = JFactory::getUser();
		$time = time();
		$queueClass = acymailing_get('class.queue');
		$queueClass->onlynew = JRequest::getInt('onlynew');
		$queueClass->mindelay = JRequest::getInt('mindelay');
		$totalSub = $queueClass->queue($mailid, $time);

		if(empty($totalSub)){
			acymailing_display(JText::_('NO_RECEIVER'), 'warning');
			return;
		}

		$mailObject = new stdClass();
		$mailObject->senddate = $time;
		$mailObject->published = 1;
		$mailObject->mailid = $mailid;
		$mailObject->sentby = $user->id;
		$db = JFactory::getDBO();
		$db->updateObject(acymailing_table('mail'), $mailObject, 'mailid');

		$config =& acymailing_config();
		$queueType = $config->get('queue_type');
		if($queueType == 'onlyauto'){
			$messages = array();
			$messages[] = JText::sprintf('ADDED_QUEUE', $totalSub);
			$messages[] = JText::_('AUTOSEND_CONFIRMATION');
			acymailing_display($messages, 'success');
			return;
		}else{
			JRequest::setVar('totalsend', $totalSub);
			$app = JFactory::getApplication();
			$app->redirect(acymailing_completeLink('send&task=continuesend&mailid='.$mailid.'&totalsend='.$totalSub, true, true));
			exit;
		}
	}

	function continuesend(){
		$config = acymailing_config();

		if(acymailing_level(1) && $config->get('queue_type') == 'onlyauto'){
			JRequest::setVar('tmpl', 'component');
			acymailing_display(JText::_('ACY_ONLYAUTOPROCESS'), 'warning');
			return;
		}


		$newcrontime = time() + 120;
		if($config->get('cron_next') < $newcrontime){
			$newValue = new stdClass();
			$newValue->cron_next = $newcrontime;
			$config->save($newValue);
		}

		$mailid = acymailing_getCID('mailid');

		$totalSend = JRequest::getVar('totalsend', 0, '', 'int');
		$alreadySent = JRequest::getVar('alreadysent', 0, '', 'int');

		$helperQueue = acymailing_get('helper.queue');
		$helperQueue->mailid = $mailid;
		$helperQueue->report = true;
		$helperQueue->total = $totalSend;
		$helperQueue->start = $alreadySent;
		$helperQueue->pause = $config->get('queue_pause');
		$helperQueue->process();

		JRequest::setVar('tmpl', 'component');



	}

	function scheduleready(){
		if(!$this->isAllowed('newsletters', 'schedule')) return;
		$mailid = acymailing_getCID('mailid');

		if(empty($mailid)) return false;
		$queueClass = acymailing_get('class.queue');
		$values = new stdClass();
		$values->nbqueue = $queueClass->nbQueue($mailid);

		if(!empty($values->nbqueue)){
			$messages = array();
			$messages[] = JText::sprintf('ALREADY_QUEUED', $values->nbqueue);
			$messages[] = JText::sprintf('DELETE_QUEUE');
			acymailing_display($messages, 'warning');
			return;
		}
		JRequest::setVar('layout', 'scheduleconfirm');
		return parent::display();
	}

	function genschedule(){
		JRequest::checkToken() or die('Invalid Token');

		$schedHelper = acymailing_get('helper.schedule');
		$result = $schedHelper->queueScheduled();

		acymailing_display($schedHelper->messages, $result ? 'success' : 'warning');

		return true;
	}

	function schedule(){
		if(!$this->isAllowed('newsletters', 'schedule')) return;
		$mailid = acymailing_getCID('mailid');

		JRequest::setVar('tmpl', 'component');

		(JRequest::checkToken() && !empty($mailid)) or die('Invalid Token');

		$senddate = JRequest::getString('senddate', '');
		$sendhours = JRequest::getString('sendhours', '');
		$sendminutes = JRequest::getString('sendminutes', '');
		$senddateComplete = $senddate.' '.$sendhours.':'.$sendminutes;
		$user = JFactory::getUser();

		if(empty($senddate)){
			acymailing_display(JText::_('SPECIFY_DATE'), 'warning');
			return $this->scheduleready();
		}

		$realSendDate = acymailing_getTime($senddateComplete);
		if($realSendDate < time()){
			acymailing_display(JText::_('DATE_FUTURE'), 'warning');
			return $this->scheduleready();
		}


		$mailClass = acymailing_get('class.mail');
		$myNewsletter = $mailClass->get($mailid);

		$mail = new stdClass();
		$mail->mailid = $mailid;
		$mail->senddate = $realSendDate;
		$mail->sentby = $user->id;
		$mail->published = 2;
		$myNewsletter->params['onlynew'] = JRequest::getInt('onlynew', 0);
		$mail->params = $myNewsletter->params;

		$mailClass->save($mail);

		acymailing_display(JText::sprintf('AUTOSEND_DATE', '<b><i>'.$myNewsletter->subject.'</i></b>', acymailing_getDate($realSendDate)), 'success');

		if(!ACYMAILING_J30){
			$js = "window.top.document.getElementById('a_schedule').innerHTML = '<a class=\"toolbar\" onclick=\"javascript: submitbutton(\'unschedule\')\" href=\"#\"><span class=\"acyicon-schedule\" title=\"".JText::_('UNSCHEDULE', true)."\"> </span>".JText::_('UNSCHEDULE')."</a>';";
		}else{
			$js = "window.top.document.getElementById('toolbar-schedule').innerHTML = '<div id=\"toolbar-unschedule\"><button onclick=\"Joomla.submitbutton(\'unschedule\');\" href=\"#\"><i class=\"acyicon-schedule \"> </i>Unschedule</button></div>';";
		}
		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);
	}

	function addqueue(){
		if(!$this->isAllowed('newsletters', 'schedule')) return;
		JRequest::setVar('layout', 'addqueue');
		return parent::display();
	}

	function scheduleone(){
		if(!$this->isAllowed('newsletters', 'schedule')) return;
		$mailid = JRequest::getInt('mailid');
		$subid = JRequest::getInt('subid');
		$senddate = JRequest::getString('senddate', '');
		$sendhours = JRequest::getString('sendhours', '');
		$sendminutes = JRequest::getString('sendminutes', '');
		$senddateComplete = $senddate.' '.$sendhours.':'.$sendminutes;
		$app = JFactory::getApplication();

		(JRequest::checkToken() && !empty($mailid) && !empty($subid)) or die('Invalid Token');

		$realSendDate = acymailing_getTime($senddateComplete);
		if($realSendDate < time()){
			acymailing_display(JText::_('DATE_FUTURE'), 'warning');
			if($app->isAdmin()){
				return $this->addqueue();
			}else{
				$frontSubController = acymailing_get('controller.frontsubscriber');
				return $frontSubController->addqueue();
			}
		}

		$mailClass = acymailing_get('class.mail');
		$myNewsletter = $mailClass->get($mailid);

		$queueEntry = new stdClass();
		$queueEntry->mailid = $myNewsletter->mailid;
		$queueEntry->subid = $subid;
		$queueEntry->senddate = $realSendDate;
		$queueEntry->priority = 1;

		$db = JFactory::getDBO();
		$status = $db->insertObject('#__acymailing_queue', $queueEntry);

		if($status){
			acymailing_display(JText::sprintf('AUTOSEND_DATE', '<b><i>'.$myNewsletter->subject.'</i></b>', acymailing_getDate($realSendDate)), 'success');
		}else{
			acymailing_display(array(JText::_('ERROR_SAVING'), $db->getErrorMsg()), 'error');
			if($app->isAdmin()){
				return $this->addqueue();
			}else{
				$frontSubController = acymailing_get('controller.frontsubscriber');
				return $frontSubController->addqueue();
			}
		}
	}


	function spamtest(){
		$mailid = JRequest::getInt('mailid');
		if(empty($mailid)) return;

		$config = acymailing_config();
		ob_start();
		$urlSite = trim(base64_encode(preg_replace('#https?://(www\.)?#i', '', ACYMAILING_LIVE)), '=/');
		$url = ACYMAILING_SPAMURL.'spamTestSystem&component=acymailing&level='.strtolower($config->get('level', 'starter')).'&urlsite='.$urlSite;
		$spamtestSystem = acymailing_fileGetContent($url, 30);

		$warnings = ob_get_clean();

		if(empty($spamtestSystem) || $spamtestSystem === false || !empty($warnings)){
			acymailing_display('Could not load your information from our server'.((!empty($warnings) && defined('JDEBUG') && JDEBUG) ? $warnings : ''), 'error');
			return;
		}
		$decodedInformation = json_decode($spamtestSystem, true);
		if(!empty($decodedInformation['messages']) || !empty($decodedInformation['error'])){
			$msgError = (!empty($decodedInformation['messages'])) ? $decodedInformation['messages'].'<br />' : '';
			$msgError .= (!empty($decodedInformation['error'])) ? $decodedInformation['error'] : '';
			acymailing_display($msgError, 'error');
			return;
		}
		if(empty($decodedInformation['email'])){
			acymailing_display('Missing test mail address', 'error');
			return;
		}

		$receiver = new stdClass();
		$receiver->subid = 0;
		$receiver->email = $decodedInformation['email'];
		$receiver->name = $decodedInformation['name'];
		$receiver->html = 1;
		$receiver->confirmed = 1;
		$receiver->enabled = 1;

		$mailerHelper = acymailing_get('helper.mailer');
		$mailerHelper->checkConfirmField = false;
		$mailerHelper->checkEnabled = false;
		$mailerHelper->checkPublished = false;
		$mailerHelper->checkAccept = false;
		$mailerHelper->loadedToSend = true;
		$mailerHelper->report = false;

		if(!$mailerHelper->sendOne($mailid, $receiver)){
			acymailing_display($mailerHelper->reportMessage, 'error');
			return;
		}

		$app = JFactory::getApplication();
		$app->redirect($decodedInformation['displayURL']);

		return;
	}
}
