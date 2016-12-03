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

class mailClass extends acymailingClass{

	var $tables = array('queue', 'listmail', 'stats', 'userstats', 'urlclick', 'mail');
	var $pkey = 'mailid';
	var $namekey = 'alias';
	var $allowedFields = array('subject', 'published', 'fromname', 'fromemail', 'replyname', 'replyemail', 'type', 'visible', 'alias', 'html', 'tempid', 'altbody', 'filter', 'metakey', 'metadesc', 'language', 'summary', 'thumb', 'params');

	function get($id, $default = null){

		if(empty($id)) return null;

		$query = 'SELECT a.* FROM '.acymailing_table('mail').' as a WHERE ';
		$query .= is_numeric($id) ? 'a.mailid' : 'a.alias';
		$query .= ' = '.$this->database->Quote($id);
		$query .= ' LIMIT 1';

		$this->database->setQuery($query);
		$mail = $this->database->loadObject();

		if(!empty($mail->userid)){
			$this->database->setQuery('SELECT b.username,b.name,b.email FROM #__users as b WHERE b.id = '.intval($mail->userid).' LIMIT 1');
			$author = $this->database->loadObject();
			if(!empty($author)){
				foreach($author as $var => $value){
					$mail->$var = $value;
				}
			}
		}

		if(!empty($mail)){
			$mail->attach = empty($mail->attach) ? array() : unserialize($mail->attach);
			$mail->favicon = empty($mail->favicon) ? new stdClass() : unserialize($mail->favicon);
			$mail->params = empty($mail->params) ? array() : unserialize($mail->params);
			$mail->filter = empty($mail->filter) ? array() : unserialize($mail->filter);
		}

		return $mail;
	}

	function saveForm(){
		$app = JFactory::getApplication();
		$config =& acymailing_config();

		$mail = new stdClass();
		$mail->mailid = acymailing_getCID('mailid');

		$formData = JRequest::getVar('data', array(), '', 'array');
		if(!empty($formData['mail']['subject'])) $formData['mail']['subject'] = str_replace(chr(226).chr(128).chr(168), '', $formData['mail']['subject']);
		foreach($formData['mail'] as $column => $value){
			if(!$app->isAdmin() && !in_array($column, $this->allowedFields)) continue;
			acymailing_secureField($column);
			if(in_array($column, array('params', 'summary'))){
				$mail->$column = $value;
			}else{
				$mail->$column = strip_tags($value, '<ADV>');
			}
		}

		$mail->body = JRequest::getVar('editor_body', '', '', 'string', JREQUEST_ALLOWRAW);
		if(ACYMAILING_J25) $mail->body = JComponentHelper::filterText($mail->body);

		$acypluginsHelper = acymailing_get('helper.acyplugins');
		$acypluginsHelper->cleanHtml($mail->body);

		$mail->attach = array();
		$attachments = JRequest::getVar('attachments', array(), '', 'array');

		if(!empty($attachments)){
			foreach($attachments as $id => $filepath){
				if(empty($filepath)) continue;
				$attachment = new stdClass();
				$attachment->filename = $filepath;
				$attachment->size = filesize(JPATH_SITE.'/'.$filepath);
				$extension = substr($attachment->filename, strrpos($attachment->filename, '.'));

				if(preg_match('#\.(php.?|.?htm.?|pl|py|jsp|asp|sh|cgi)#Ui', $attachment->filename)){
					acymailing_enqueueMessage(JText::sprintf('ACCEPTED_TYPE', substr($attachment->filename, strrpos($attachment->filename, '.') + 1), $config->get('allowedfiles')), 'notice');
					continue;
				}
				$attachment->filename = str_replace(array('.', ' '), '_', substr($attachment->filename, 0, strpos($attachment->filename, $extension))).$extension;

				$mail->attach[] = $attachment;
			}
		}

		$faviconRequest = JRequest::getVar('favicon', '');
		if(!empty($faviconRequest[0])){
			$faviconRequest = $faviconRequest[0];
			$favicon = new stdClass();
			$favicon->filename = $faviconRequest;
			$favicon->size = filesize(JPATH_SITE.'/'.$faviconRequest);
			$extension = substr($favicon->filename, strrpos($favicon->filename, '.'));
			if(preg_match('#\.(php.?|.?htm.?|pl|py|jsp|asp|sh|cgi)#Ui', $favicon->filename)){
				acymailing_enqueueMessage(JText::sprintf('ACCEPTED_TYPE', substr($favicon->filename, strrpos($favicon->filename, '.') + 1), $config->get('allowedfiles')), 'notice');
			}

			$favicon->filename = str_replace(array('.', ' '), '_', substr($favicon->filename, 0, strpos($favicon->filename, $extension))).$extension;

			$mail->favicon = $favicon;
		}

		if(isset($mail->filter)){
			$mail->filter = array();
			$filterData = JRequest::getVar('filter');
			foreach($filterData['type'] as $num => $oneType){
				if(empty($oneType)) continue;
				$mail->filter['type'][$num] = $oneType;
				$mail->filter[$num][$oneType] = $filterData[$num][$oneType];
			}
		}

		$toggleHelper = acymailing_get('helper.toggle');
		if(!empty($mail->type) && $mail->type == 'followup' && !empty($mail->mailid)){
			$oldMail = $this->get($mail->mailid);
			if(!empty($mail->published) AND !$oldMail->published){
				$this->_publishfollowup($mail);
			}
			if($oldMail->senddate != $mail->senddate){
				$text = JText::_('FOLLOWUP_CHANGED_DELAY_INFORMED');
				$text .= ' '.$toggleHelper->toggleText('update', $mail->mailid, 'followup', JText::_('FOLLOWUP_CHANGED_DELAY'));
				acymailing_enqueueMessage($text, 'notice');
			}
		}

		if(preg_match('#<a[^>]*subid=[0-9].*</a>#Uis', $mail->body, $pregResult)){
			acymailing_enqueueMessage(JText::sprintf('ACY_PERSONAL_LINK', $pregResult[0]), 'warning');
		}

		if(empty($mail->thumb)){
			unset($mail->thumb);
		}elseif($mail->thumb == 'delete'){
			$mail->thumb = '';
		}
		if(isset($mail->published) && $mail->published != 1) $mail->published = 0;
		if(isset($mail->html) && $mail->html != 1) $mail->html = 0;
		if(isset($mail->visible) && $mail->visible != 1) $mail->visible = 0;
		$mailid = $this->save($mail);
		if(!$mailid) return false;
		JRequest::setVar('mailid', $mailid);

		$status = true;

		if(!empty($formData['listmail'])){
			$receivers = array();
			$remove = array();

			foreach($formData['listmail'] as $listid => $receiveme){
				if(!empty($receiveme)){
					$receivers[] = $listid;
				}else{
					$remove[] = $listid;
				}
			}

			$listMailClass = acymailing_get('class.listmail');
			$status = $listMailClass->save($mailid, $receivers, $remove);
		}

		if(!empty($mail->type) && $mail->type == 'followup' && empty($mail->mailid) && !empty($mail->published)){
			$mail->mailid = $mailid;
			$this->_publishfollowup($mail);
		}

		return $status;
	}

	function addFollowUpQueue($mailid, $all = false){
		$followup = $this->get($mailid);
		if(empty($followup->mailid)){
			$this->errors[] = 'Could not load mailid '.$mailid;
			return false;
		}

		$listmailClass = acymailing_get('class.listmail');
		$mycampaign = $listmailClass->getCampaign($followup->mailid);
		if(empty($mycampaign->listid)){
			$this->errors[] = 'Could not get the attached campaign';
			return false;
		}

		$config = acymailing_config();

		$db = JFactory::getDBO();
		$query = 'INSERT IGNORE INTO `#__acymailing_queue` (`mailid`,`senddate`,`priority`,`subid`) ';
		$query .= 'SELECT '.$followup->mailid.', b.`subdate` + '.intval($followup->senddate).' , '.(int)$config->get('priority_followup', 2).', b.`subid` ';
		$query .= 'FROM `#__acymailing_listsub` as b';
		$query .= ' WHERE b.`status` = 1 AND b.`listid` = '.intval($mycampaign->listid);
		if(!$all) $query .= ' AND b.`subdate` > '.(time() - $followup->senddate);
		$db->setQuery($query);
		$db->query();
		$nbinserted = $db->getAffectedRows();

		if(!empty($nbupdated)){
			$campaignHelper = acymailing_get('helper.campaign');
			$campaignHelper->updateUnsubdate($mycampaign->listid, $followup->senddate);
		}

		return $nbinserted;
	}

	private function _publishfollowup(&$mail){
		$listmailClass = acymailing_get('class.listmail');
		$mycampaign = $listmailClass->getCampaign($mail->mailid);

		if(empty($mycampaign->listid)){
			return;
		}

		$db = JFactory::getDBO();
		$toggleHelper = acymailing_get('helper.toggle');
		$startdate = (time() - $mail->senddate);
		$db->setQuery('SELECT COUNT(subid) as total FROM `#__acymailing_listsub` as b WHERE b.`status` = 1 AND b.`listid` = '.intval($mycampaign->listid).' AND b.`subdate` > '.intval($startdate));
		$total = $db->loadResult();

		$db->setQuery('SELECT COUNT(subid) as total FROM `#__acymailing_listsub` as b WHERE b.`status` = 1 AND b.`listid` = '.intval($mycampaign->listid));
		$totalall = $db->loadResult();

		if(empty($total) && empty($totalall)) return;

		$text = JText::_('FOLLOWUP_PUBLISHED_INFORMED');
		$text .= '<ul>';
		if(!empty($total)) $text .= '<li>'.$toggleHelper->toggleText('add', $mail->mailid, 'followup', JText::sprintf('FOLLOWUP_ADDQUEUE_USERS', acymailing_getDate($startdate)).' ( '.JText::sprintf('SELECTED_USERS', $total).' )').'</li>';
		if(!empty($totalall)) $text .= '<li>'.$toggleHelper->toggleText('addall', $mail->mailid, 'followup', JText::_('FOLLOWUP_ADDQUEUE_ALLUSERS').' ( '.JText::sprintf('SELECTED_USERS', $totalall).' )').'</li>';

		acymailing_enqueueMessage($text, 'notice');
	}

	function save($mail){

		if(isset($mail->alias) OR empty($mail->mailid)){
			if(empty($mail->alias)) $mail->alias = $mail->subject;
			$jconfig = JFactory::getConfig();
			$method = $jconfig->get('unicodeslugs', 0) == 1 ? 'stringURLUnicodeSlug' : 'stringURLSafe';
			$mail->alias = JFilterOutput::$method(trim($mail->alias));
		}

		if(empty($mail->mailid)){
			if(empty($mail->created)) $mail->created = time();
			if(empty($mail->userid)){
				$user = JFactory::getUser();
				$mail->userid = $user->id;
			}
			if(empty($mail->key)) $mail->key = acymailing_generateKey(8);
		}else{
			if(!empty($mail->attach)){
				$oldMailObject = $this->get($mail->mailid);
				if(!empty($oldMailObject) && is_array($oldMailObject->attach)){
					$mail->attach = array_merge($oldMailObject->attach, $mail->attach);
				}
			}
		}

		if(!empty($mail->attach) AND !is_string($mail->attach)) $mail->attach = serialize($mail->attach);
		if(!empty($mail->favicon) AND !is_string($mail->favicon)) $mail->favicon = serialize($mail->favicon);
		if(isset($mail->filter) AND !is_string($mail->filter)) $mail->filter = serialize($mail->filter);

		if(!empty($mail->params)){
			if(!empty($mail->params['lastgenerateddate']) && !is_numeric($mail->params['lastgenerateddate'])){
				$mail->params['lastgenerateddate'] = acymailing_getTime($mail->params['lastgenerateddate']);
			}
			$mail->params = serialize($mail->params);
		}

		if(!empty($mail->senddate) && !is_numeric($mail->senddate)){
			$mail->senddate = acymailing_getTime($mail->senddate);
		}

		JPluginHelper::importPlugin('acymailing');
		$dispatcher = JDispatcher::getInstance();

		if(empty($mail->mailid)){
			$dispatcher->trigger('onAcyBeforeMailCreate', array(&$mail));
			$status = $this->database->insertObject(acymailing_table('mail'), $mail);
		}else{
			$dispatcher->trigger('onAcyBeforeMailModify', array(&$mail));
			$status = $this->database->updateObject(acymailing_table('mail'), $mail, 'mailid');
		}

		if(!$status){
			$this->errors[] = substr(strip_tags($this->database->getErrorMsg()), 0, 200).'...';
		}

		if(!empty($mail->params) AND is_string($mail->params)) $mail->params = unserialize($mail->params);
		if(!empty($mail->attach) AND is_string($mail->attach)) $mail->attach = unserialize($mail->attach);
		if(!empty($mail->favicon) AND is_string($mail->favicon)) $mail->favicon = unserialize($mail->favicon);

		if($status) return empty($mail->mailid) ? $this->database->insertid() : $mail->mailid;
		return false;
	}

	function saveastmpl(){
		$tmplClass = acymailing_get('class.template');
		$newTmpl = new stdClass();

		$formData = JRequest::getVar('data', array(), '', 'array');
		if(!empty($formData['mail']['tempid'])){
			$template = $tmplClass->get($formData['mail']['tempid']);
			$newTmpl->styles = $template->styles;
			$newTmpl->stylesheet = $template->stylesheet;
			$newTmpl->category = $template->category;
		}
		if(!empty($formData['mail']['subject'])){
			$formData['mail']['subject'] = str_replace(chr(226).chr(128).chr(168), '', $formData['mail']['subject']);
			$newTmpl->subject = strip_tags($formData['mail']['subject']);
			$newTmpl->name = strip_tags($formData['mail']['subject']);
		}

		$newTmpl->body = JRequest::getVar('editor_body', '', '', 'string', JREQUEST_ALLOWRAW);
		if(ACYMAILING_J25) $newTmpl->body = JComponentHelper::filterText($newTmpl->body);
		$acypluginsHelper = acymailing_get('helper.acyplugins');
		$acypluginsHelper->cleanHtml($newTmpl->body);

		if(!empty($formData['mail']['thumb']) && $formData['mail']['thumb'] == 'delete'){
			$newTmpl->thumb = null;
		}elseif(!empty($formData['mail']['thumb'])){
			$newTmpl->thumb = strip_tags($formData['mail']['thumb']);
		}else{
			$mailid = acymailing_getCID('mailid');
			if(!empty($mailid)){
				$mail = $this->get($mailid);
				$newTmpl->thumb = $mail->thumb;
			}
		}
		if(!empty($formData['mail']['altbody'])) $newTmpl->altbody = strip_tags($formData['mail']['altbody']);
		if(!empty($formData['mail']['fromname'])) $newTmpl->fromname = strip_tags($formData['mail']['fromname']);
		if(!empty($formData['mail']['fromemail'])) $newTmpl->fromemail = strip_tags($formData['mail']['fromemail']);
		if(!empty($formData['mail']['replyname'])) $newTmpl->replyname = strip_tags($formData['mail']['replyname']);
		if(!empty($formData['mail']['replyemail'])) $newTmpl->replyemail = strip_tags($formData['mail']['replyemail']);
		if(!empty($formData['mail']['summary'])) $newTmpl->description = strip_tags($formData['mail']['summary']);
		$newTmpl->ordering = 1;

		$tempid = $tmplClass->save($newTmpl);
		if(!empty($tempid)){
			$formData['mail']['tempid'] = $tempid;
			acymailing_enqueueMessage(JText::_('ACY_SAVEASTMPL_VALID'), 'message');
		}else{
			acymailing_enqueueMessage(JText::_('ERROR_SAVING'), 'error');
		}

		return true;
	}


	function ab_test($abTestDetail, $mailsArray, $nbTotalReceivers){
		$db = JFactory::getDBO();
		$query = "UPDATE #__acymailing_mail SET abtesting=".$db->quote(serialize($abTestDetail)).", published=1 WHERE mailid IN (".implode(',', $mailsArray).")";
		$db->setQuery($query);
		$db->query();

		if($abTestDetail['action'] != 'manual'){
			$config = acymailing_config();
			$currentAbTests = $config->get('currentABTests', '');
			if(!empty($currentAbTests)){
				$currentData = unserialize($currentAbTests);
			}else $currentData = array();
			$newTest = new stdClass();
			$newTest->sendDate = $abTestDetail['time'] + ($abTestDetail['delay'] * 86400);
			$newTest->ids = $abTestDetail['mailids'];
			$currentData[] = $newTest;
			$newconfig = new stdClass();
			$newconfig->currentABTests = serialize($currentData);
			$config->save($newconfig);
		}

		$statsClass = acymailing_get('class.stats');
		$statsClass->delete($mailsArray);

		$queueClass = acymailing_get('class.queue');
		$time = time();
		$nbReceiversTest = floor($nbTotalReceivers * $abTestDetail['prct'] / 100);
		$queueClass->limit = $nbReceiversTest;
		$queueClass->orderBy = 'RAND()';
		$queueClass->queue($mailsArray[0], $time);
		$nbReceiversPerMail = floor($nbReceiversTest / count($mailsArray));
		foreach($mailsArray as $oneMail){
			if($oneMail == $mailsArray[0]) continue;
			$query = "UPDATE #__acymailing_queue SET mailid=".intval($oneMail)." WHERE mailid=".intval($mailsArray[0])." LIMIT ".$nbReceiversPerMail;
			$db->setQuery($query);
			$db->query();
		}
		$query = "UPDATE #__acymailing_mail SET senddate=".$time." WHERE mailid IN (".implode(',', $mailsArray).")";
		$db->setQuery($query);
		$db->query();
		return $nbReceiversTest;
	}


	function complete_abtest($typeAction, $mailid){
		$db = JFactory::getDBO();
		$db->setQuery("SELECT abtesting FROM #__acymailing_mail WHERE mailid=".(int)$mailid);
		$resDetails = acymailing_loadResultArray($db);
		$abTestDetail = unserialize($resDetails[0]);
		$dataForCopy = array('mailid' => $mailid, 'abTestDetail' => $abTestDetail);
		$newMailid = $this->abTest_createFinalNewletter($typeAction, $dataForCopy);

		$queueClass = acymailing_get('class.queue');
		$time = time();
		$queueClass->queue($newMailid, $time);

		$mailidsTest = $abTestDetail['mailids'];
		$db->setQuery("SELECT subid FROM #__acymailing_userstats WHERE mailid IN (".$mailidsTest.")");
		$resUsersFromTest = acymailing_loadResultArray($db);
		if(!empty($resUsersFromTest)){
			$db->setQuery("DELETE FROM #__acymailing_queue WHERE subid IN (".implode(',', $resUsersFromTest).") AND mailid=".$newMailid);
			$db->query();
		}

		$abTestDetail['status'] = 'abTestFinalSend';
		$abTestDetail['newMail'] = $newMailid;
		$query = "UPDATE #__acymailing_mail SET abtesting=".$db->quote(serialize($abTestDetail))." WHERE mailid IN (".$mailidsTest.")";
		$db->setQuery($query);
		$db->query();

		return $newMailid;
	}

	function abTest_createFinalNewletter($typeAction, $dataForCopy){
		$db = JFactory::getDBO();

		if($typeAction == 'manual'){
			$mailid = $dataForCopy['mailid'];
			$newMailid = $this->copyOneNewsletter($mailid);
			return $newMailid;
		}

		$queryStat = 'SELECT mailid, openunique, clickunique, senthtml, senttext FROM #__acymailing_stats WHERE mailid IN ('.$dataForCopy['abTestDetail']['mailids'].')';
		$db->setQuery($queryStat);
		$resStat = $db->loadObjectList('mailid');
		$betterClick = -1;
		$betterOpen = -1;
		if(empty($resStat)) return 0;
		foreach($resStat as $mailid => $statsMail){
			if($statsMail->openunique > $betterOpen){
				$idOpen = $mailid;
				$betterOpen = $statsMail->openunique;
			}
			if($statsMail->clickunique > $betterClick){
				$idClick = $mailid;
				$betterClick = $statsMail->clickunique;
			}
		}
		if($dataForCopy['abTestDetail']['action'] == 'open'){
			$newMailid = $this->copyOneNewsletter($idOpen);
		}elseif($dataForCopy['abTestDetail']['action'] == 'click') $newMailid = $this->copyOneNewsletter($idClick);
		elseif($dataForCopy['abTestDetail']['action'] == 'mix'){
			$db->setQuery("SELECT subject, fromname, fromemail, replyname, replyemail FROM #__acymailing_mail WHERE mailid=".$idOpen);
			$newSubject = $db->loadObjectList();
			$newMailid = $this->copyOneNewsletter($idClick, $newSubject[0]);
		}
		return $newMailid;
	}

	function copyOneNewsletter($mailid, $subject = ''){
		$db = JFactory::getDBO();
		$time = time();
		$query = 'INSERT INTO `#__acymailing_mail` (`subject`, `fromname`, `fromemail`, `replyname`, `replyemail`, `body`, `altbody`, `published`, `created`, `type`, `visible`, `userid`, `alias`, `attach`, `html`, `tempid`, `key`, `frequency`, `params`,`filter`,`metakey`,`metadesc`,`summary`,`thumb`,`senddate`)';
		if(empty($subject)){
			$query .= " SELECT `subject`, `fromname`, `fromemail`, `replyname`, `replyemail`";
		}else $query .= " SELECT ".$db->Quote($subject->subject).", ".$db->Quote($subject->fromname).", ".$db->Quote($subject->fromemail).", ".$db->Quote($subject->replyname).", ".$db->Quote($subject->replyemail);
		$query .= ", `body`, `altbody`, `published`, '.$time.', `type`, `visible`, `userid`, `alias`, `attach`, `html`, `tempid`, ".$db->Quote(md5(rand(1000, 999999))).', `frequency`, `params`,`filter`,`metakey`,`metadesc`,`summary`,`thumb`,'.time().' FROM `#__acymailing_mail` WHERE `mailid` = '.(int)$mailid;
		$db->setQuery($query);
		$db->query();
		$newMailid = $db->insertid();
		$db->setQuery('INSERT IGNORE INTO `#__acymailing_listmail` (`listid`,`mailid`) SELECT `listid`,'.$newMailid.' FROM `#__acymailing_listmail` WHERE `mailid` = '.(int)$mailid);
		$db->query();
		return $newMailid;
	}

	function updateAbTest_auto($idsToSend){
		if(empty($idsToSend)) return;
		$db = JFactory::getDBO();
		$db->setQuery("SELECT mailid, abtesting FROM #__acymailing_mail WHERE mailid IN (".$idsToSend.") AND abtesting IS NOT NULL");
		$resDetails = $db->loadObjectList('mailid');
		if(empty($resDetails)) return;

		$oneAbTest = current($resDetails);
		$oneMailid = $oneAbTest->mailid;
		$abTestDetail = unserialize($oneAbTest->abtesting);
		$mailsArray = explode(',', $abTestDetail['mailids']);

		$query = "SELECT COUNT(*) FROM #__acymailing_queue WHERE mailid IN (".$abTestDetail['mailids'].")";
		$db->setQuery($query);
		$queueCheck = $db->loadResult();

		if(empty($queueCheck)){
			if(($abTestDetail['time'] + ($abTestDetail['delay'] * 24 * 3600)) < time()){
				$newMailid = $this->complete_abtest($abTestDetail['action'], $oneMailid);
				return $newMailid;
			}
		}
	}
}
