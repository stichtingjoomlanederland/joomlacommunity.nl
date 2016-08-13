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

class NewsletterController extends acymailingController{

	var $aclCat = 'newsletters';

	function replacetags(){
		if(!$this->isAllowed($this->aclCat, 'manage')) return;
		$this->store();
		return $this->edit();
	}

	function copy(){
		if(!$this->isAllowed($this->aclCat, 'manage')) return;
		JRequest::checkToken() || JRequest::checkToken('get') || JSession::checkToken('get') || die('Invalid Token');

		$cids = JRequest::getVar('cid', array(), '', 'array');
		$db = JFactory::getDBO();
		$time = time();

		$my = JFactory::getUser();
		$creatorId = intval($my->id);

		$addSendDate = '';
		if(!empty($this->copySendDate)) $addSendDate = ', `senddate`';

		foreach($cids as $oneMailid){
			$query = 'INSERT INTO `#__acymailing_mail` (`subject`, `body`, `altbody`, `published`'.$addSendDate.', `created`, `fromname`, `fromemail`, `replyname`, `replyemail`, `bccaddresses`, `type`, `visible`, `userid`, `alias`, `attach`, `html`, `tempid`, `key`, `frequency`, `params`,`filter`,`metakey`,`metadesc`)';
			$query .= " SELECT CONCAT('copy_',`subject`), `body`, `altbody`, 0".$addSendDate.", '.$time.', `fromname`, `fromemail`, `replyname`, `replyemail`, `bccaddresses`, `type`, `visible`, '.$creatorId.', `alias`, `attach`, `html`, `tempid`, ".$db->Quote(acymailing_generateKey(8)).', `frequency`, `params`,`filter`,`metakey`,`metadesc` FROM `#__acymailing_mail` WHERE `mailid` = '.(int)$oneMailid;
			$db->setQuery($query);
			$db->query();
			$newMailid = $db->insertid();
			$db->setQuery('INSERT IGNORE INTO `#__acymailing_listmail` (`listid`,`mailid`) SELECT `listid`,'.$newMailid.' FROM `#__acymailing_listmail` WHERE `mailid` = '.(int)$oneMailid);
			$db->query();
		}

		return $this->listing();
	}

	function store(){
		if(!$this->isAllowed($this->aclCat, 'manage')) return;
		JRequest::checkToken() or die('Invalid Token');

		$mailClass = acymailing_get('class.mail');
		$status = $mailClass->saveForm();
		if($status){
			acymailing_enqueueMessage(JText::_('JOOMEXT_SUCC_SAVED'), 'message');
		}else{
			acymailing_enqueueMessage(JText::_('ERROR_SAVING'), 'error');
			if(!empty($mailClass->errors)){
				foreach($mailClass->errors as $oneError){
					acymailing_enqueueMessage($oneError, 'error');
				}
			}
		}
	}

	function unschedule(){
		if(!$this->isAllowed($this->aclCat, 'schedule')) return;
		$mailid = acymailing_getCID('mailid');

		(JRequest::checkToken() && !empty($mailid)) or die('Invalid Token');
		$mail = new stdClass();
		$mail->mailid = $mailid;
		$mail->senddate = 0;
		$mail->published = 0;

		$mailClass = acymailing_get('class.mail');
		$mailClass->save($mail);

		acymailing_enqueueMessage(JText::_('SUCC_UNSCHED'));

		return $this->preview();
	}

	function remove(){
		if(!$this->isAllowed($this->aclCat, 'delete')) return;
		JRequest::checkToken() or die('Invalid Token');

		$cids = JRequest::getVar('cid', array(), '', 'array');

		$class = acymailing_get('class.mail');
		$num = $class->delete($cids);

		acymailing_enqueueMessage(JText::sprintf('SUCC_DELETE_ELEMENTS', $num), 'message');

		return $this->listing();
	}

	function savepreview(){
		$this->store();
		return $this->preview();
	}


	function saveastmpl(){
		$this->store();
		$mailclass = acymailing_get('class.mail');
		$mailclass->saveastmpl();
		return $this->edit();
	}

	function preview(){
		JRequest::setVar('layout', 'preview');
		JRequest::setVar('hidemainmenu', 1);
		return parent::display();
	}

	function sendtest(){
		$this->_sendtest();
		return $this->preview();
	}

	function _sendtest(){
		JRequest::checkToken() or die('Invalid Token');

		$mailid = acymailing_getCID('mailid');
		$test_selection = JRequest::getVar('test_selection', '', '', 'string');

		if(empty($mailid) OR empty($test_selection)) return false;

		$app = JFactory::getApplication();
		$mailer = acymailing_get('helper.mailer');
		$mailer->forceVersion = JRequest::getVar('test_html', 1, '', 'int');
		$mailer->autoAddUser = true;
		if($app->isAdmin()) $mailer->SMTPDebug = 1;
		$mailer->checkConfirmField = false;
		$comment = JRequest::getString('commentTest', '');
		if(!empty($comment)) $mailer->introtext = '<div align="center" style="max-width:600px;margin:auto;margin-top:10px;margin-bottom:10px;padding:10px;border:1px solid #cccccc;background-color:#f6f6f6;color:#333333;">'.nl2br($comment).'</div>';

		$receivers = array();
		if($test_selection == 'users'){
			$receiverEntry = JRequest::getVar('test_emails', '', '', 'string');
			if(!empty($receiverEntry)){
				if(substr_count($receiverEntry, '@') > 1){
					$receivers = explode(',', trim(preg_replace('# +#', '', $receiverEntry)));
				}else{
					$receivers[] = trim($receiverEntry);
				}
			}
		}else{
			$gid = JRequest::getInt('test_group', '-1');
			if($gid == -1) return false;
			$db = JFactory::getDBO();
			if(!ACYMAILING_J16){
				$db->setQuery('SELECT email FROM '.acymailing_table('users', false).' WHERE gid = '.intval($gid));
			}else $db->setQuery('SELECT u.email FROM '.acymailing_table('users', false).' AS u JOIN '.acymailing_table('user_usergroup_map', false).' AS ugm ON u.id = ugm.user_id WHERE ugm.group_id = '.intval($gid));
			$receivers = acymailing_loadResultArray($db);
		}

		if(empty($receivers)){
			acymailing_enqueueMessage(JText::_('NO_SUBSCRIBER'), 'notice');
			return false;
		}

		$result = true;
		foreach($receivers as $receiver){
			$result = $mailer->sendOne($mailid, $receiver) && $result;
		}

		return $result;
	}

	function upload(){
		if(!$this->isAllowed($this->aclCat, 'manage')) return;
		JRequest::setVar('layout', 'upload');
		return parent::display();
	}

	function abtesting(){
		JRequest::setVar('layout', 'abtesting');
		return parent::display();
	}

	function abtest(){
		$nbTotalReceivers = JRequest::getInt('nbTotalReceivers');
		$mailids = JRequest::getString('mailid');
		$mailsArray = explode(',', $mailids);
		JArrayHelper::toInteger($mailsArray);

		$db = JFactory::getDBO();

		$abTesting_prct = JRequest::getInt('abTesting_prct');
		$abTesting_delay = JRequest::getInt('abTesting_delay');
		$abTesting_action = JRequest::getString('abTesting_action');

		if(empty($abTesting_prct)){
			acymailing_display(JText::_('ABTESTING_NEEDVALUE'), 'warning');
			$this->abtesting();
			return;
		}

		$newAbTestDetail = array();
		$newAbTestDetail['mailids'] = implode(',', $mailsArray);
		$newAbTestDetail['prct'] = (!empty($abTesting_prct) ? $abTesting_prct : '');
		$newAbTestDetail['delay'] = (isset($abTesting_delay) && strlen($abTesting_delay) > 0 ? $abTesting_delay : '2');
		$newAbTestDetail['action'] = (!empty($abTesting_action) ? $abTesting_action : 'manual');
		$newAbTestDetail['time'] = time();
		$newAbTestDetail['status'] = 'inProgress';
		$mailClass = acymailing_get('class.mail');
		$nbReceiversTest = $mailClass->ab_test($newAbTestDetail, $mailsArray, $nbTotalReceivers);

		acymailing_enqueueMessage(JText::sprintf('ABTESTING_SUCCESSADD', $nbReceiversTest), 'info');
		JRequest::setVar('validationStatus', 'abTestAdd');
		$this->abtesting();
	}

	function complete_abtest(){
		$mailid = JRequest::getInt('mailToSend');
		$mailClass = acymailing_get('class.mail');
		$newMailid = $mailClass->complete_abtest('manual', $mailid);

		$finalMail = $mailClass->get($newMailid);
		acymailing_enqueueMessage(JText::sprintf('ABTESTING_FINALSEND', $finalMail->subject), 'info');
		JRequest::setVar('validationStatus', 'abTestFinalSend');
		$this->abtesting();
	}

	function douploadnewsletter(){
		if(!$this->isAllowed($this->aclCat, 'manage')) return;
		JRequest::checkToken() or die('Invalid Token');

		$templateClass = acymailing_get('class.template');
		$templateClass->checkAreas = false;
		$statusUpload = $templateClass->doupload();

		if($statusUpload){
			$mailClass = acymailing_get('class.mail');
			$mail = new stdClass();
			$newTemplate = $templateClass->get($templateClass->templateId);
			$mail->subject = $newTemplate->name;
			$mail->body = $newTemplate->body;
			$mail->tempid = $templateClass->templateId;

			$idMailCreated = $mailClass->save($mail);
			if($idMailCreated){
				acymailing_display(JText::_('NEWSLETTER_INSTALLED'), 'success');
				$js = "setTimeout('redirect()',2000); function redirect(){window.top.location.href = 'index.php?option=com_acymailing&ctrl=newsletter&task=edit&mailid=".$idMailCreated."'; }";
				$doc = JFactory::getDocument();
				$doc->addScriptDeclaration($js);
				return;
			}else{
				acymailing_display(JText::_('ERROR_SAVING'), 'error');
				return $this->upload();
			}
		}else{
			return $this->upload();
		}
	}

	function cancelNewsletter(){
		$queueController = acymailing_get('controller.queue');
		$queueController->cancelNewsletter();
		return $this->listing();
	}
}
