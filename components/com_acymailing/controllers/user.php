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

class UserController extends acymailingController{


	function __construct($config = array()){
		parent::__construct($config);

		$this->registerDefaultTask('subscribe');
		$this->registerTask('optout', 'unsub');
		$this->registerTask('out', 'unsub');
	}

	function confirm(){
		if(acymailing_isRobot()) return false;

		$config = acymailing_config();
		$app = JFactory::getApplication();


		$userClass = acymailing_get('class.subscriber');
		$userClass->geolocRight = true;

		$user = $userClass->identify();
		if(empty($user)) return false;

		$redirectUrl = $config->get('confirm_redirect');
		$listRedirection = '';
		$subscription = $userClass->getSubscriptionStatus($user->subid);
		foreach($subscription as $i => $onelist){
			if(!in_array($onelist->status, array(1, 2)) || JText::_('REDIRECTION_CONFIRMATION_'.$i) == 'REDIRECTION_CONFIRMATION_'.$i) continue;
			$listRedirection = JText::_('REDIRECTION_CONFIRMATION_'.$i);
			break;
		}

		if(!empty($listRedirection)) $redirectUrl = $listRedirection;

		if(!empty($redirectUrl)){
			$replace = array();
			foreach($user as $key => $val){
				$replace['{'.$key.'}'] = $val;
				$replace['{user:'.$key.'}'] = $val;
			}
			$redirectUrl = str_replace(array_keys($replace), $replace, $redirectUrl);
			$this->setRedirect($redirectUrl);
		}

		if($config->get('confirmation_message', 1)){
			if($user->confirmed && strlen(JText::_('ALREADY_CONFIRMED')) > 0){
				$app->enqueueMessage(JText::_('ALREADY_CONFIRMED'));
			}elseif(!$user->confirmed && strlen(JText::_('SUBSCRIPTION_CONFIRMED')) > 0) $app->enqueueMessage(JText::_('SUBSCRIPTION_CONFIRMED'));
		}

		if(!$user->confirmed) $userClass->confirmSubscription($user->subid);

		$notifConfirm = $config->get('notification_confirm');
		if(!empty($notifConfirm)){
			$listsubClass = acymailing_get('class.listsub');
			$userHelper = acymailing_get('helper.user');
			$mailer = acymailing_get('helper.mailer');
			$mailer->autoAddUser = true;
			$mailer->checkConfirmField = false;
			$mailer->report = false;
			foreach($user as $field => $value) $mailer->addParam('user:'.$field, $value);
			$mailer->addParam('user:subscription', $listsubClass->getSubscriptionString($user->subid));
			$mailer->addParam('user:subscriptiondates', $listsubClass->getSubscriptionString($user->subid, true));
			$mailer->addParam('user:ip', $userHelper->getIP());
			if(!empty($userClass->geolocData)){
				foreach($userClass->geolocData as $map => $value){
					$mailer->addParam('geoloc:notif_'.$map, $value);
				}
			}
			$mailer->addParamInfo();
			$allUsers = explode(' ', trim(str_replace(array(';', ','), ' ', $notifConfirm)));
			foreach($allUsers as $oneUser){
				if(empty($oneUser)) continue;
				$mailer->sendOne('notification_confirm', $oneUser);
			}
		}

		JRequest::setVar('layout', 'confirm');
		return parent::display();
	}//endfct

	function modify(){
		$userClass = acymailing_get('class.subscriber');
		$userClass->geolocRight = true;

		$user = $userClass->identify(true);
		if(empty($user)) return $this->subscribe();

		JRequest::setVar('layout', 'modify');
		return parent::display();
	}

	function subscribe(){

		$user = JFactory::getUser();
		$userClass = acymailing_get('class.subscriber');
		$userClass->geolocRight = true;

		if(!empty($user->id) AND $userClass->identify(true)){
			return $this->modify();
		}

		$config = acymailing_config();
		$allowvisitor = $config->get('allow_visitor', 1);
		if(empty($allowvisitor)){
			$app = JFactory::getApplication();
			$usercomp = !ACYMAILING_J16 ? 'com_user' : 'com_users';
			$uri = JFactory::getURI();
			$url = 'index.php?option='.$usercomp.'&view=login&return='.base64_encode($uri->toString());
			$app = JFactory::getApplication();
			$app->redirect($url, JText::_('ONLY_LOGGED'));
			return false;
		}

		JRequest::setVar('layout', 'modify');
		return parent::display();
	}

	function unsub(){
		$userClass = acymailing_get('class.subscriber');

		$user = $userClass->identify();
		if(empty($user)) return false;

		$statsClass = acymailing_get('class.stats');
		$statsClass->countReturn = false;
		$statsClass->saveStats();

		JRequest::setVar('layout', 'unsub');
		return parent::display();
	}

	function saveunsub(){

		acymailing_checkRobots();

		$app = JFactory::getApplication();

		$subscriberClass = acymailing_get('class.subscriber');
		$subscriberClass->sendConf = false;

		$listsubClass = acymailing_get('class.listsub');
		$userHelper = acymailing_get('helper.user');
		$config = acymailing_config();


		$subscriber = new stdClass();
		$subscriber->subid = JRequest::getInt('subid');

		$user = $subscriberClass->identify();
		if(!$user || empty($subscriber->subid) || $user->subid != $subscriber->subid){
			echo "<script>alert('ERROR : You are not allowed to modify this user'); window.history.go(-1);</script>";
			exit;
		}

		$refusemails = JRequest::getInt('refuse');
		$unsuball = JRequest::getInt('unsuball');
		$mailid = JRequest::getInt('mailid');

		$oldUser = $subscriberClass->get($subscriber->subid);

		$survey = JRequest::getVar('survey', array(), '', 'array');
		$tagSurvey = '';
		$data = array();
		if(!empty($survey)){
			foreach($survey as $oneResult){
				if(empty($oneResult)) continue;
				$data[] = "REASON::".str_replace(array("\n", "\r"), array('<br />', ''), strip_tags($oneResult));
			}

			$tagSurvey = implode('<br />', $data);
		}

		$replace = array();
		$replace['REASON::'] = '<br />'.JText::_('REASON').' : ';
		$reasons = unserialize($config->get('unsub_reasons'));
		foreach($reasons as $i => $oneReason){
			if(preg_match('#^[A-Z_]*$#', $oneReason)){
				$replace[$oneReason] = JText::_($oneReason);
			}
		}

		$tagSurvey = str_replace(array_keys($replace), $replace, $tagSurvey);

		$historyClass = acymailing_get('class.acyhistory');
		$historyClass->insert($subscriber->subid, 'unsubscribed', $data, $mailid);

		$notifToSend = '';

		$incrementUnsub = false;
		if($refusemails OR $unsuball){

			if($refusemails){
				$subscriber->accept = 0;
				if($config->get('unsubscription_message', 1) && strlen(JText::_('CONFIRM_UNSUB_FULL')) > 0) $app->enqueueMessage(JText::_('CONFIRM_UNSUB_FULL'));
				$notifToSend = 'notification_refuse';
			}elseif($unsuball){
				$notifToSend = 'notification_unsuball';
			}


			$subscription = $subscriberClass->getSubscriptionStatus($subscriber->subid);
			$updatelists = array();
			foreach($subscription as $listid => $oneList){
				if($oneList->status != -1){
					$updatelists[-1][] = $listid;
				}
			}

			$listsubClass->sendNotif = false;

			if(!empty($updatelists)){
				$status = $listsubClass->updateSubscription($subscriber->subid, $updatelists);
				if($config->get('unsubscription_message', 1) && strlen(JText::_('CONFIRM_UNSUB_ALL')) > 0) $app->enqueueMessage(JText::_('CONFIRM_UNSUB_ALL'));
				$incrementUnsub = true;
			}else{
				if($config->get('unsubscription_message', 1) && strlen(JText::_('ERROR_NOT_SUBSCRIBED')) > 0) $app->enqueueMessage(JText::_('ERROR_NOT_SUBSCRIBED'));
			}

			$subscriber->confirmed = 0;
			$subscriberClass->save($subscriber);
		}else{

			$subscription = $subscriberClass->getSubscriptionStatus($subscriber->subid);

			$db = JFactory::getDBO();
			$db->setQuery('SELECT b.listid, b.name, b.type FROM '.acymailing_table('listmail').' as a JOIN '.acymailing_table('list').' as b on a.listid = b.listid WHERE a.mailid = '.$mailid);
			$allLists = $db->loadObjectList();

			if(empty($allLists)){
				$db->setQuery('SELECT b.listid, b.name, b.type FROM '.acymailing_table('list').' as b WHERE b.welmailid = '.$mailid.' OR b.unsubmailid = '.$mailid);
				$allLists = $db->loadObjectList();
			}

			if(empty($allLists)){
				$db->setQuery('SELECT b.listid, b.name, b.type FROM #__acymailing_listsub as a JOIN #__acymailing_list as b on a.listid = b.listid WHERE a.subid = '.$subscriber->subid);
				$allLists = $db->loadObjectList();
			}


			$otherSubscriptionsBoxes = JRequest::getVar('unsubotherlists', array(), 'post', 'ARRAY');
			$otherSubscriptionsId = JRequest::getVar('unsubotherlistsid', array(), 'post', 'ARRAY');
			$othersubscriptionsToRemove = array();
			if(!empty($otherSubscriptionsBoxes)){
				$i = 0;
				foreach($otherSubscriptionsBoxes as $anotherSubscriptionsBox => $value){
					if($value == 1) $othersubscriptionsToRemove[] = $otherSubscriptionsId[$i];
					$i++;
				}

				$db->setQuery('SELECT listid, name, type FROM #__acymailing_list WHERE listid IN ('.implode(',', $othersubscriptionsToRemove).')');
				$otherSubscriptions = $db->loadObjectList();

				foreach($otherSubscriptions as $anotherSubscription){
					array_push($allLists, $anotherSubscription);
				}
			}


			if(empty($allLists)){
				echo "<script>alert('ERROR : Could not get the list for the mailing $mailid'); window.history.go(-1);</script>";
				exit;
			}

			$campaignList = array();
			$unsubList = array();
			foreach($allLists as $oneList){
				if(isset($subscription[$oneList->listid]) AND $subscription[$oneList->listid]->status != -1){
					if($oneList->type == 'campaign'){
						$campaignList[] = $oneList->listid;
					}else{
						$unsubList[$oneList->listid] = $oneList;
					}
				}
			}

			if(!empty($campaignList)){
				$db->setQuery('SELECT b.listid, b.name, b.type FROM '.acymailing_table('listcampaign').' as a LEFT JOIN '.acymailing_table('list').' as b on a.listid = b.listid WHERE a.campaignid IN ('.implode(',', $campaignList).')');
				$otherLists = $db->loadObjectList();
				if(!empty($otherLists)){
					foreach($otherLists as $oneList){
						if(isset($subscription[$oneList->listid]) AND $subscription[$oneList->listid]->status != -1){
							$unsubList[$oneList->listid] = $oneList;
						}
					}
				}
			}

			if(!empty($unsubList)){
				$updatelists = array();
				$updatelists[-1] = array_keys($unsubList);
				$listsubClass->survey = $tagSurvey;
				$status = $listsubClass->updateSubscription($subscriber->subid, $updatelists);
				if($config->get('unsubscription_message', 1) && strlen(JText::_('CONFIRM_UNSUB_CURRENT')) > 0) $app->enqueueMessage(JText::_('CONFIRM_UNSUB_CURRENT'));
				$incrementUnsub = true;
			}else{
				if($config->get('unsubscription_message', 1) && strlen(JText::_('ERROR_NOT_SUBSCRIBED_CURRENT')) > 0) $app->enqueueMessage(JText::_('ERROR_NOT_SUBSCRIBED_CURRENT'));
			}
		}

		if($incrementUnsub){
			$db = JFactory::getDBO();
			$db->setQuery('SELECT subid FROM #__acymailing_history WHERE `action` = "unsubscribed" AND `subid` = '.intval($subscriber->subid).' AND `mailid` = '.intval($mailid).' LIMIT 1,1');
			$alreadythere = $db->loadResult();

			if(empty($alreadythere)){
				$db->setQuery('UPDATE '.acymailing_table('stats').' SET `unsub` = `unsub` +1 WHERE `mailid` = '.(int)$mailid);
				$db->query();
			}
		}

		$classGeoloc = acymailing_get('class.geolocation');
		$classGeoloc->saveGeolocation('unsubscription', $subscriber->subid);

		if(!empty($notifToSend)){
			$notifyUsers = $config->get($notifToSend);

			if(!empty($notifyUsers)){
				$mailer = acymailing_get('helper.mailer');
				$mailer->autoAddUser = true;
				$mailer->checkConfirmField = false;
				$mailer->report = false;
				foreach($oldUser as $field => $value) $mailer->addParam('user:'.$field, $value);
				$mailer->addParam('user:subscription', $listsubClass->getSubscriptionString($oldUser->subid));
				$mailer->addParam('user:subscriptiondates', $listsubClass->getSubscriptionString($oldUser->subid, true));
				$mailer->addParam('user:ip', $userHelper->getIP());
				$mailer->addParam('survey', $tagSurvey);
				$mailer->addParamInfo();
				$allUsers = explode(' ', trim(str_replace(array(';', ','), ' ', $notifyUsers)));
				foreach($allUsers as $oneUser){
					if(empty($oneUser)) continue;
					$mailer->sendOne('notification_unsuball', $oneUser);
				}
			}
		}


		$redirectUnsub = $config->get('unsub_redirect');
		if(!empty($redirectUnsub)){
			$replace = array();
			foreach($oldUser as $key => $val){
				$replace['{'.$key.'}'] = $val;
				$replace['{user:'.$key.'}'] = $val;
			}
			$redirectUnsub = str_replace(array_keys($replace), $replace, $redirectUnsub);
			$this->setRedirect($redirectUnsub);
			return;
		}

		JRequest::setVar('layout', 'saveunsub');
		return parent::display();
	}

	function savechanges(){
		JRequest::checkToken() or die('Please make sure your cookies are enabled');
		acymailing_checkRobots();
		$app = JFactory::getApplication();

		$config = acymailing_config();
		$subscriberClass = acymailing_get('class.subscriber');
		$subscriberClass->geolocRight = true;
		$subscriberClass->extendedEmailVerif = true;

		if(acymailing_level(1) AND $config->get('captcha_enabled') AND !$subscriberClass->identify(true)){
			$captchaClass = acymailing_get('class.acycaptcha');
			$captchaClass->state = 'acycaptchacomponent';
			if(!$captchaClass->check(JRequest::getString('acycaptcha'))){
				$captchaClass->returnError();
			}
		}

		$status = $subscriberClass->saveForm();
		$subscriberClass->sendNotification();
		if($status){
			if($subscriberClass->confirmationSent){
				if($config->get('subscription_message', 1) && strlen(JText::_('CONFIRMATION_SENT')) > 0) $app->enqueueMessage(JText::_('CONFIRMATION_SENT'), 'message');
				$redirectlink = $config->get('sub_redirect');
			}elseif($subscriberClass->newUser){
				if($config->get('subscription_message', 1) && strlen(JText::_('SUBSCRIPTION_OK')) > 0) $app->enqueueMessage(JText::_('SUBSCRIPTION_OK'), 'message');
				$redirectlink = $config->get('sub_redirect');
			}else{
				if(strlen(JText::_('SUBSCRIPTION_UPDATE_OK')) > 0) $app->enqueueMessage(JText::_('SUBSCRIPTION_UPDATED_OK'), 'message');
				$redirectlink = $config->get('modif_redirect');
			}
		}elseif($subscriberClass->requireId){
			if(strlen(JText::_('IDENTIFICATION_SENT')) > 0) $app->enqueueMessage(JText::_('IDENTIFICATION_SENT'), 'notice');
		}else{
			if(strlen(JText::_('ERROR_SAVING')) > 0) $app->enqueueMessage(JText::_('ERROR_SAVING'), 'error');
		}

		if(!empty($redirectlink)){

			$this->setRedirect($redirectlink);
			return;
		}

		if($subscriberClass->identify(true)) return $this->modify();
		return $this->subscribe();
	}
}
