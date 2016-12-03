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

class SimplemailController extends acymailingController{
	public function edit(){
		if(!acymailing_level(3)) return;
		JRequest::setVar('layout', 'edit');
		return parent::display();
	}

	public function send(){
		if(!acymailing_level(3)) return;
		$templateClass = acymailing_get('class.template');
		$acypluginsHelper = acymailing_get('helper.acyplugins');

		$failed = array();
		$success = array();

		$body = JRequest::getVar('editor_body', '', '', 'string', JREQUEST_ALLOWRAW);
		$acypluginsHelper->cleanHtml($body);
		$subject = JRequest::getString('subject', '');
		$templateId = JRequest::getInt('tempid', 0);
		$users = explode(',', JRequest::getVar('test_emails', ''));

		JPluginHelper::importPlugin('acymailing');
		$dispatcher = JDispatcher::getInstance();
		$userClass = acymailing_get('class.subscriber');

		$mail = new stdClass();
		$mail->sendHTML = true;

		if($templateId > 0) $mail->tempid = $templateId;

		foreach($users as $to){
			$mailer = acymailing_get('helper.acymailer');
			$mailer->report = false;
			$mailer->isHTML(true);
			if($templateId > 0) $mailer->template = $templateClass->get($templateId);

			$mailer->addAddress($to);
			$user = $userClass->get($to);

			$mail->subject = $subject;
			$mail->body = $body;

			$dispatcher->trigger('acymailing_replacetags', array(&$mail, true));
			$dispatcher->trigger('acymailing_replaceusertags', array(&$mail, &$user, true));

			$mailer->Subject = $mail->subject;
			$mailer->Body = $mail->body;

			$result = $mailer->send();
			if(!$result){
				$failed[] = $to;
			}else{
				$success[] = $to;
			}
		}

		if(sizeof($failed) == 0){
			acymailing_enqueueMessage(JText::sprintf('SIMPLE_SENDING_SUCCESS', implode(', ', $success)));
		}else{
			acymailing_enqueueMessage(JText::sprintf('SIMPLE_SENDING_ERROR', implode(', ', $failed), $mailer->reportMessage), 'error');
		}

		$application = JFactory::getApplication();
		$session = JFactory::getSession();

		$session->set('simplesending_tempid', $templateId);
		$session->set('simplesending_subject', $subject);
		$session->set('simplesending_body', $body);
		$application->redirect('index.php?option=com_acymailing&ctrl=simplemail&task=edit');
	}
}
