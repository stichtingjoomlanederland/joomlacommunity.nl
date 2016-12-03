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

class acyautonewsHelper{

	var $messages = array();
	var $mailClass = null;
	var $dispatcher = null;
	var $time;

	function generate(){
		$db = JFactory::getDBO();
		$this->time = time();
		$query = 'SELECT `mailid` FROM '.acymailing_table('mail').' WHERE `type` = \'autonews\' AND `published`= 1 AND `senddate` < '.$this->time;
		$db->setQuery($query);

		$autonews = acymailing_loadResultArray($db);


		if(empty($autonews)) return false;

		$this->mailClass = acymailing_get('class.mail');

		JPluginHelper::importPlugin('acymailing');
		$this->dispatcher = JDispatcher::getInstance();

		foreach($autonews as $mailid){
			$oneAutonews = $this->mailClass->get($mailid);

			if(empty($oneAutonews->params['lastgenerateddate'])){
				if(is_numeric($oneAutonews->frequency)){
					$oneAutonews->params['lastgenerateddate'] = $this->time - $oneAutonews->frequency;
				}else{
					$hourMin = empty($oneAutonews->senddate) ? '' : date(' H:i', $oneAutonews->senddate);

					if(strpos($oneAutonews->frequency, 'on_') !== false){
						$values = explode('_', ltrim($oneAutonews->frequency, 'on_'));
						$currentDayKey = array_search(date('l'), $values);
						if($currentDayKey === false) $currentDayKey = 0;

						if(isset($values[$currentDayKey-1])){
							$oneAutonews->params['lastgenerateddate'] = strtotime('previous '.$values[$currentDayKey-1].$hourMin);
						}else{
							$oneAutonews->params['lastgenerateddate'] = strtotime('previous '.end($values).$hourMin);
						}
					}else{
						$detailDay = explode('_', $oneAutonews->frequency);
						$dateTmp = strtotime($detailDay[0].' '.$detailDay[1].' of '.date('F Y').$hourMin);
						if(time() < $dateTmp){
							$newMonthYear = date('F Y', mktime(date("H", $dateTmp), date("i", $dateTmp), date("s", $dateTmp), date("n", $dateTmp) - 1, date("j", $dateTmp), date("Y", $dateTmp)));
							$dateTmp = strtotime($detailDay[0].' '.$detailDay[1].' of '.$newMonthYear.$hourMin);
						}
						$oneAutonews->params['lastgenerateddate'] = $dateTmp;
					}
				}
			}
			if(!$this->_generatingStatus($oneAutonews)) continue;
			$this->_generateAutoNews($oneAutonews);
		}

		return true;
	}


	private function _generateAutoNews($newNewsletter){

		$newNewsletter->senddate = $this->time;
		$newNewsletter->type = 'news';
		if(!empty($newNewsletter->params['generate'])){
			$newNewsletter->published = 2;
		}else $newNewsletter->published = 0;

		$notifyUsers = $newNewsletter->params['generateto'];

		$mailidModel = $newNewsletter->mailid;
		unset($newNewsletter->mailid);
		unset($newNewsletter->username);
		unset($newNewsletter->name);
		unset($newNewsletter->email);

		$newNewsletter->key = acymailing_generateKey(8);

		$issueNb = $newNewsletter->params['issuenb'];
		$newNewsletter->body = str_replace('{issuenb}', $issueNb, $newNewsletter->body);
		$newNewsletter->altbody = str_replace('{issuenb}', $issueNb, $newNewsletter->altbody);
		$newNewsletter->subject = str_replace('{issuenb}', $issueNb, $newNewsletter->subject);

		unset($newNewsletter->template);
		$newNewsletter->mailid = $this->mailClass->save($newNewsletter);

		if(!empty($newNewsletter->tempid)){
			if(!isset($this->templateClass)) $this->templateClass = acymailing_get('class.template');
			$newNewsletter->template = $this->templateClass->get($newNewsletter->tempid);
		}

		$mailer = acymailing_get('helper.mailer');
		$mailer->triggerTagsWithRightLanguage($newNewsletter, false);

		unset($newNewsletter->template);
		unset($newNewsletter->attach);
		$this->mailClass->save($newNewsletter);

		$query = 'INSERT IGNORE INTO '.acymailing_table('listmail').' (mailid,listid) SELECT '.$newNewsletter->mailid.', b.`listid` FROM '.acymailing_table('listmail').' as b';
		$query .= ' WHERE b.mailid = '.$mailidModel;

		$db = JFactory::getDBO();
		$db->setQuery($query);
		if(!$db->query()){
			$this->messages[] = 'Could not assign the Newsletter to your lists : '.$db->getErrorMsg();
		}

		$this->messages[] = JText::sprintf('NEWSLETTER_GENERATED', $newNewsletter->mailid, '<b><i>'.$newNewsletter->subject.'</i></b>');

		if(!empty($notifyUsers)){
			$app = JFactory::getApplication();
			$mailer->report = $app->isAdmin();
			$mailer->autoAddUser = true;
			$mailer->checkConfirmField = false;
			$mailer->checkEnabled = false;
			$mailer->checkPublished = false;
			$mailer->checkAccept = false;
			$mailer->loadedToSend = true;
			$mailer->addParam('issuenb', $issueNb);
			$intro = JText::_('AUTONEWS_GENERATE_INTRO').'<br /><br />';
			if(!empty($newNewsletter->params['generate'])){
				$intro .= JText::_('AUTONEWS_GENERATE_INTRO_SENT');
			}else{
				$config = acymailing_config();
				$itemId = $config->get('itemid', 0);
				$item = empty($itemId) ? '' : '&Itemid='.$itemId;

				$intro .= '<a href="'.acymailing_frontendLink('index.php?option=com_acymailing&ctrl=archive&task=sendautonews&mailid='.$newNewsletter->mailid.'&key='.$newNewsletter->key.'-'.$newNewsletter->senddate).$item.'" >'.JText::_('AUTONEWS_GENERATE_INTRO_REVIEW').'</a>';
			}

			$mailer->introtext = '<div align="center" style="max-width:600px;margin:auto;margin-top:10px;margin-bottom:10px;padding:10px;border:1px solid #cccccc;background-color:#f6f6f6;color:#333333;">'.$intro.'</div>';

			$allUsers = explode(' ', trim(str_replace(array(';', ','), ' ', $notifyUsers)));
			foreach($allUsers as $oneUser){
				if(empty($oneUser)) continue;
				$mailer->sendOne($newNewsletter->mailid, $oneUser);
			}
		}
	}


	private function _generatingStatus(&$oneAutonews){
		$results = $this->dispatcher->trigger('acymailing_generateautonews', array(&$oneAutonews));
		$return = true;
		foreach($results as $oneResult){
			if(isset($oneResult->status) && !$oneResult->status){
				$return = false;
				$this->messages[] = JText::sprintf('NEWSLETTER_NOT_GENERATED', $oneAutonews->mailid, $oneResult->message);
				break;
			}
		}

		$newMail = new stdClass();
		$newMail->mailid = $oneAutonews->mailid;

		if(is_numeric($oneAutonews->frequency)){
			if($oneAutonews->frequency >= 2592000 AND $oneAutonews->frequency % 2592000 == 0){
				$newMail->senddate = mktime(date("H", $oneAutonews->senddate), date("i", $oneAutonews->senddate), date("s", $oneAutonews->senddate), date("n", $oneAutonews->senddate) + ($oneAutonews->frequency / 2592000), date("j", $oneAutonews->senddate), date("Y", $oneAutonews->senddate));
			}else{
				$newMail->senddate = $oneAutonews->senddate + $oneAutonews->frequency;
			}
		}else{
			$hourMin = !empty($oneAutonews->senddate) ? date(' H:i', $oneAutonews->senddate) : '';

			if(strpos($oneAutonews->frequency, 'on_') !== false){
				$values = explode('_', ltrim($oneAutonews->frequency, 'on_'));
				$currentDayKey = array_search(date('l'), $values);
				if($currentDayKey === false){
					$return = false;
					$this->messages[] = JText::sprintf('NEWSLETTER_NOT_GENERATED', $oneAutonews->mailid, 'Only sending on '.implode(', ', $values));
					$newMail->senddate = time() + 86400;
				}else{
					if(isset($values[$currentDayKey+1])){
						$newMail->senddate = strtotime('next '.$values[$currentDayKey+1].$hourMin);
					}else{
						$newMail->senddate = strtotime('next '.$values[0].$hourMin);
					}
				}
			}else{
				$detailDay = explode('_', $oneAutonews->frequency);
				$dateTmp = strtotime($detailDay[0].' '.$detailDay[1].' of '.date('F Y').$hourMin);
				if(time() > $dateTmp){
					$newMonthYear = date('F Y', mktime(date("H", $dateTmp), date("i", $dateTmp), date("s", $dateTmp), date("n", $dateTmp) + 1, date("j", $dateTmp), date("Y", $dateTmp)));
					$dateTmp = strtotime($detailDay[0].' '.$detailDay[1].' of '.$newMonthYear.$hourMin);
				}
				$newMail->senddate = $dateTmp;
			}
		}

		$newMail->params = $oneAutonews->params;
		if(is_numeric($oneAutonews->frequency) && ($newMail->senddate < $this->time || $newMail->senddate > $this->time + 2 * $oneAutonews->frequency)) $newMail->senddate = $this->time + $oneAutonews->frequency;

		if($return){
			$newMail->params['lastgenerateddate'] = $this->time;
			$newMail->params['issuenb'] = empty($newMail->params['issuenb']) ? 1 : $newMail->params['issuenb'] + 1;
		}

		$this->mailClass->save($newMail);

		return $return;
	}
}//endclass
