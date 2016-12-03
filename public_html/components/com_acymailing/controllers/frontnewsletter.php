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
$my = JFactory::getUser();
if(empty($my->id)){
	$usercomp = !ACYMAILING_J16 ? 'com_user' : 'com_users';
	$uri = JFactory::getURI();
	$url = 'index.php?option='.$usercomp.'&view=login&return='.base64_encode($uri->toString());
	$app = JFactory::getApplication();
	$app->redirect($url, JText::_('ACY_NOTALLOWED'), 'error');
	return false;
}

$config = acymailing_config();
if(!acymailing_isAllowed($config->get('acl_newsletter_manage', 'all'))) die('You are not allowed to access this page');

$frontHelper = acymailing_get('helper.acyfront');
include(ACYMAILING_BACK.'controllers'.DS.'newsletter.php');

class FrontnewsletterController extends NewsletterController{
	function __construct($config = array()){
		parent::__construct($config);

		$app = JFactory::getApplication();

		$listid = JRequest::getInt('listid');
		if(empty($listid)){
			$listid = JRequest::getInt('filter_lists');
		}
		if(empty($listid)){
			$listid = $app->getUserState("com_acymailing.frontnewsletterfilter_list");
		}
		if(empty($listid)){
			$listClass = acymailing_get('class.list');
			$allAllowedLists = $listClass->getFrontendLists();
			if(!empty($allAllowedLists)){
				$firstList = reset($allAllowedLists);
				$listid = $firstList->listid;
				JRequest::setVar('listid', $listid);
			}
		}
		JRequest::setVar('filter_lists', $listid);
		JRequest::setVar('listid', $listid);

		if(!acyCheckAccessList()){
			$app->enqueueMessage('You can not have access to this list', 'error');
			$app->redirect('index.php');
			return false;
		}

		if(!in_array(JRequest::getVar('task'), array('remove', 'form', 'cancel', 'copy'))){
			$mailid = acymailing_getCID('mailid');
			if(!empty($mailid) && !in_array(JRequest::getVar('task'), array('stats', 'detailstats', 'statsclick'))){
				$edit = true;
			}else $edit = false;
			if(!acyCheckEditNewsletter($edit)){
				$app->enqueueMessage(JText::sprintf('NO_ACCESS_NEWSLETTER', $mailid), 'error');
				$app->redirect('index.php?option=com_acymailing&ctrl=frontnewsletter&listid='.$listid);
				return false;
			}
		}
	}

	function form(){
		return $this->edit();
	}

	function remove(){
		$cids = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cids);
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$config = acymailing_config();

		if(empty($cids)) $app->redirect('index.php?option=com_acymailing&ctrl=frontnewsletter');

		$rightDeleteOther = $config->get('frontend_modif', 1);
		if($rightDeleteOther){
			$listClass = acymailing_get('class.list');
			$lists = $listClass->getFrontendLists();
			$frontListsIds = array();
			foreach($lists as $oneList){
				$frontListsIds[] = $oneList->listid;
			}
			JArrayHelper::toInteger($frontListsIds);
			$db->setQuery('SELECT mailid FROM #__acymailing_listmail WHERE mailid IN ('.implode(",", $cids).') AND listid IN ('.implode(',', $frontListsIds).') GROUP BY mailid');
			$mails = acymailing_loadResultArray($db);
			$result = array_diff($cids, $mails);
			foreach($result as $mailOtherList){
				$app->enqueueMessage(JText::sprintf('NO_ACCESS_NEWSLETTER', $mailOtherList->mailid), 'error');
			}
			$cids = $mails;
		}else{
			$db->setQuery('SELECT * FROM `#__acymailing_mail` WHERE mailid IN ('.implode(',', $cids).')');
			$mails = $db->loadObjectList();
			$my = JFactory::getUser();
			foreach($mails as $mail){
				if($my->id != $mail->userid){
					$app->enqueueMessage(JText::sprintf('NO_ACCESS_NEWSLETTER', $mail->mailid), 'error');
					array_splice($cids, array_search($mail->mailid, $cids), 1);
				}
			}
		}

		JRequest::setVar('cid', $cids);
		return parent::remove();
	}

	function scheduleconfirm(){
		JRequest::setVar('layout', 'scheduleconfirm');
		return parent::display();
	}

	function schedule(){
		if(!$this->isAllowed('newsletters', 'schedule')) return;
		JRequest::checkToken() or die('Invalid Token');

		$mailid = acymailing_getCID('mailid');

		$senddate = JRequest::getString('senddate', '');
		$user = JFactory::getUser();

		if(empty($senddate)){
			acymailing_display(JText::_('SPECIFY_DATE'), 'warning');
			return $this->scheduleconfirm();
		}

		$realSendDate = acymailing_getTime($senddate);
		if($realSendDate < time()){
			acymailing_display(JText::_('DATE_FUTURE'), 'warning');
			return $this->scheduleconfirm();
		}

		$mail = new stdClass();
		$mail->mailid = $mailid;
		$mail->senddate = $realSendDate;
		$mail->sentby = $user->id;
		$mail->published = 2;

		$mailClass = acymailing_get('class.mail');
		$mailClass->save($mail);

		$myNewsletter = $mailClass->get($mailid);

		JRequest::setVar('tmpl', 'component');

		acymailing_display(JText::sprintf('AUTOSEND_DATE', '<b><i>'.$myNewsletter->subject.'</i></b>', acymailing_getDate($realSendDate)), 'success');

		$config = acymailing_config();
		$redirecturl = $config->get('redirect_schedule');
		if(empty($redirecturl)) $redirecturl = "index.php?option=com_acymailing&ctrl=frontnewsletter&listid=".JRequest::getInt('listid');

		$js = "setTimeout('redirect()',2000); function redirect(){window.top.location.href = '".$redirecturl."'; }";

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);
	}

	function delete(){

		list($mailid, $attachid) = explode('_', JRequest::getCmd('value'));
		$mailid = intval($mailid);
		if(empty($mailid)) return false;

		$db = JFactory::getDBO();
		$db->setQuery('SELECT `attach` FROM '.acymailing_table('mail').' WHERE mailid = '.$mailid.' LIMIT 1');
		$attachment = $db->loadResult();
		if(empty($attachment)) return;
		$attach = unserialize($attachment);

		unset($attach[$attachid]);
		$attachdb = serialize($attach);

		$db->setQuery('UPDATE '.acymailing_table('mail').' SET attach = '.$db->Quote($attachdb).' WHERE mailid = '.$mailid.' LIMIT 1');

		$db->query();
		exit;
	}

	function edit(){
		JRequest::setVar('layout', 'form');
		return parent::display();
	}

	function sendconfirm(){
		JRequest::setVar('layout', 'sendconfirm');
		return parent::display();
	}

	function send(){
		if(!$this->isAllowed('newsletters', 'send')) return;
		JRequest::checkToken() || (method_exists(JSession, 'checkToken') && JSession::checkToken('get')) || die('Invalid Token');

		$app = JFactory::getApplication();
		$user = JFactory::getUser();

		$mailid = acymailing_getCID('mailid');
		if(empty($mailid)) exit;

		$time = time();
		$queueClass = acymailing_get('class.queue');
		$nbEmails = $queueClass->nbQueue($mailid);
		if($nbEmails > 0){
			$app->enqueueMessage(JText::sprintf('ALREADY_QUEUED', $nbEmails), 'notice');
			return;
		}

		$queueClass->onlynew = JRequest::getInt('onlynew');
		$queueClass->mindelay = JRequest::getInt('mindelay');
		$totalSub = $queueClass->queue($mailid, $time);

		if(empty($totalSub)){
			$app->enqueueMessage(JText::_('NO_RECEIVER'), 'notice');
			return;
		}

		$mailObject = new stdClass();
		$mailObject->senddate = $time;
		$mailObject->published = 1;
		$mailObject->mailid = $mailid;
		$mailObject->sentby = $user->id;
		$db = JFactory::getDBO();
		$db->updateObject(acymailing_table('mail'), $mailObject, 'mailid');

		acymailing_display(JText::sprintf('ADDED_QUEUE', $totalSub));
		acymailing_display(JText::sprintf('AUTOSEND_CONFIRMATION', $totalSub));

		$config = acymailing_config();
		$redirecturl = $config->get('redirect_send');
		if(empty($redirecturl)) $redirecturl = acymailing_completeLink("frontnewsletter&listid=".JRequest::getInt('listid'), false, true);
		$js = "setTimeout('redirect()',2000); function redirect(){window.top.location.href = '$redirecturl'; }";
		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);
		return false;
	}

	function spamtest(){
		include_once(ACYMAILING_BACK.'controllers'.DS.'send.php');
		$sendController = new SendController();
		$sendController->spamtest();
	}

	function stats(){
		if(!$this->isAllowed('statistics', 'manage')) return;
		JRequest::setVar('layout', 'stats');
		return parent::display();
	}

	function statsclick(){
		if(!$this->isAllowed('statistics', 'manage') || !$this->isAllowed('subscriber', 'view')) return;
		JRequest::setVar('layout', 'statsclick');
		return parent::display();
	}

	function detailstats(){
		if(!$this->isAllowed('statistics', 'manage') || !$this->isAllowed('subscriber', 'view')) return;
		JRequest::setVar('layout', 'detailstats');
		return parent::display();
	}

	function unsubscribed(){
		if(!$this->isAllowed('statistics', 'manage') || !$this->isAllowed('subscriber', 'view')) return;
		JRequest::setVar('layout', 'unsubscribed');
		return parent::display();
	}

	function export(){
		$mailid = JRequest::getInt('mailid', 0);
		if(!empty($mailid)) JRequest::setVar('filter_mail', $mailid);
		if(JRequest::getVar('filter_url', false) === false || JRequest::getVar('filter_status', false) !== false){
			$controller = 'stats';
		}else $controller = 'statsurl';
		include_once(ACYMAILING_BACK.'controllers'.DS.$controller.'.php');
		$controller = ucfirst($controller).'Controller';
		$statsController = new $controller();
		$statsController->export();
	}
}
