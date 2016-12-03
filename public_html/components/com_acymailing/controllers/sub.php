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

class SubController extends acymailingController{

	function notask(){

		$ajax = JRequest::getInt('ajax',0);
		if($ajax) header("Content-type:text/html; charset=utf-8");

		if($ajax){
			echo '{"message":"Please enable the Javascript to be able to subscribe","type":"error","code":"0"}';
			exit;
		}else{
			$redirectUrl = urldecode(JRequest::getVar('redirect','','','string'));
			$this->_checkRedirectUrl($redirectUrl);
			$this->setRedirect($redirectUrl,'Please enable the Javascript to be able to subscribe','notice');
		}
		return false;
	}

	function display($dummy1 = false, $dummy2 = false){
		$moduleId = JRequest::getInt('formid');
		if(empty($moduleId)) return;

		if(JRequest::getInt('interval') > 0) setcookie('acymailingSubscriptionState', true, time() + JRequest::getInt('interval'), '/');

		$db = JFactory::getDBO();
	 	$db->setQuery('SELECT * FROM #__modules WHERE id = '.intval($moduleId).' AND `module` LIKE \'%acymailing%\' AND published = 1 LIMIT 1');
	 	$module = $db->loadObject();
	 	if(empty($module)){ echo 'No module found'; exit; }

		$module->user  	= substr( $module->module, 0, 4 ) == 'mod_' ?  0 : 1;
		$module->name = $module->user ? $module->title : substr( $module->module, 4 );
		$module->style = null;
		$module->module = preg_replace('/[^A-Z0-9_\.-]/i', '', $module->module);

		$params = array();
		if(JRequest::getInt('autofocus',0)){
			acymailing_loadMootools();
			$js = "
				window = addEvent('load', function(){
					this.focus();
					var moduleInputs = document.getElementsByTagName('input');
					if(moduleInputs){
						var i = 0;
						while(moduleInputs[i].disabled == true){
							i++;
						}
						if(moduleInputs[i]) moduleInputs[i].focus();
					}
				});";

			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration($js);
		}

		echo JModuleHelper::renderModule($module, $params);
	}

	function optin(){
		acymailing_checkRobots();
		$my = JFactory::getUser();
		$config = acymailing_config();
		$app = JFactory::getApplication();

		if(!JRequest::getCmd('acy_source') && !empty($_GET['user'])){
			JRequest::setVar('acy_source','url');
		}

		$ajax = JRequest::getInt('ajax',0);
		if($ajax){
			@ob_end_clean();
			header("Content-type:text/html; charset=utf-8");
		}

		if((int) $config->get('allow_visitor',1) != 1 && empty($my->id)){
			if($ajax){
				echo '{"message":"'.str_replace('"','\"',JText::_('ONLY_LOGGED')).'","type":"error","code":"0"}';
				exit;
			}else{
				$app->enqueueMessage(JText::_('ONLY_LOGGED'),'error');
				$usercomp = !ACYMAILING_J16 ? 'com_user' : 'com_users';
				$app->redirect('index.php?option='.$usercomp.'&view=login');
				return;
			}
		}

		if(empty($my->id)){
			if(acymailing_level(1) && $config->get('captcha_enabled')){
				$seckey = JRequest::getString('seckey');
				if(!empty($seckey)){
					if($config->get('security_key') !== $seckey){
						if($ajax){
							echo '{"message":"'.str_replace('"','\"',JText::_('ERROR_SECURE_KEY')).'","type":"error","code":"0"}';
						}else{
							echo JText::_('ERROR_SECURE_KEY',true);
						}
						exit;
					}
				}else{
					$captchaClass = acymailing_get('class.acycaptcha');
					$captchaClass->state = 'acycaptchamodule'.JRequest::getCmd('acyformname');
					if(!$captchaClass->check(JRequest::getString('acycaptcha'), $seckey)){
						if($ajax){
							echo '{"message":"'.str_replace('"','\"',JText::_('ERROR_CAPTCHA')).'","type":"error","code":"0"}';
						}else{
							$captchaClass->returnError();
						}
						exit;
					}
				}
			}
		}

		$userClass = acymailing_get('class.subscriber');

		$userClass->geolocRight = true;

		$redirectUrl = urldecode(JRequest::getVar('redirect','','','string'));

		$user = new stdClass();
		$formData = JRequest::getVar( 'user', array(), '', 'array' );

		if(!empty($formData)){
			$userClass->checkFields($formData,$user);
		}

		$allowUserModifications = (bool) ($config->get('allow_modif','data') == 'all');
		$allowSubscriptionModifications = (bool) ($config->get('allow_modif','data') != 'none');

		if(empty($user->email)){
			$connectedUser = $userClass->identify(true);
			if(!empty($connectedUser->email)){
				$user->email = $connectedUser->email;
				$allowUserModifications = true;
				$allowSubscriptionModifications = true;
			}
		}

		$user->email =  trim($user->email);

		$userHelper = acymailing_get('helper.user');
		if(empty($user->email) || !$userHelper->validEmail($user->email,true)){
			if ($ajax) echo '{"message":"'.str_replace('"','\"',JText::_('VALID_EMAIL')).'","type":"error","code":"0"}';
			else echo "<script>alert('".JText::_('VALID_EMAIL',true)."'); window.history.go(-1);</script>";
			exit;
		}

		$alreadyExists = $userClass->get($user->email);

		if(!empty($alreadyExists->subid)){
			if(!empty($alreadyExists->userid)) unset($user->name);
			$user->subid = $alreadyExists->subid;
			$currentSubscription = $userClass->getSubscriptionStatus($alreadyExists->subid);
		}else{
			$allowSubscriptionModifications = true;
			$allowUserModifications = true;
			$currentSubscription = array();
		}

		$user->accept = 1;

		if($allowUserModifications){
			$userClass->recordHistory = true;
			$user->subid = $userClass->save($user);
		}

		$myuser = $userClass->get($user->subid);
		if(empty($myuser->subid)){
			if ($ajax) echo '{"message":"Could not save the user","type":"error","code":"1"}';
			else echo "<script>alert('Could not save the user'); window.history.go(-1);</script>";
			exit;
		}

		if(empty($myuser->accept)){
			$myuser->accept = 1;
			$userClass->save($myuser);
		}

		if(!$allowUserModifications && !empty($myuser->subid) && empty($myuser->confirmed)){
			$userClass->sendConf($myuser->subid);
		}

		$statusAdd = (empty($myuser->confirmed) AND $config->get('require_confirmation',false)) ? 2 : 1;

		$addlists = array();
		$updatelists = array();

		$hiddenlistsstring = JRequest::getVar('hiddenlists','','','string');
		if(!empty($hiddenlistsstring)){

			$hiddenlists = explode(',',$hiddenlistsstring);

			JArrayHelper::toInteger($hiddenlists);

			foreach($hiddenlists as $id => $idOneList){
				if(!isset($currentSubscription[$idOneList])){
					$addlists[$statusAdd][] = $idOneList;
					continue;
				}

				if($currentSubscription[$idOneList]->status == $statusAdd || $currentSubscription[$idOneList]->status == 1) continue;

				$updatelists[$statusAdd][] = $idOneList;
			}
		}

		$visibleSubscription = JRequest::getVar('subscription','','','array');

		if(!empty($visibleSubscription)){
			foreach($visibleSubscription as $idOneList){
				if(empty($idOneList)) continue;

				if(!isset($currentSubscription[$idOneList])){
					$addlists[$statusAdd][] = $idOneList;
					continue;
				}

				if($currentSubscription[$idOneList]->status == $statusAdd || $currentSubscription[$idOneList]->status == 1) continue;

				$updatelists[$statusAdd][] = $idOneList;
			}
		}

		$visiblelistsstring = JRequest::getVar('visiblelists','','','string');

		if(!empty($visiblelistsstring)){

			$visiblelist = explode(',',$visiblelistsstring);
			JArrayHelper::toInteger($visiblelist);

			foreach($visiblelist as $idList){
				if(!in_array($idList,$visibleSubscription) AND !empty($currentSubscription[$idList]) AND $currentSubscription[$idList]->status != '-1'){
					$updatelists['-1'][] = $idList;
				}
			}
		}

		$listsubClass = acymailing_get('class.listsub');
		$status = true;
		$updateMessage = false;
		$insertMessage = false;
		if($allowSubscriptionModifications){
			if(!empty($updatelists)){
				$status = $listsubClass->updateSubscription($myuser->subid,$updatelists) && $status;
				$updateMessage = true;
			}
			if(!empty($addlists)){
				$status = $listsubClass->addSubscription($myuser->subid,$addlists) && $status;
				$insertMessage = true;
			}
		}else{
			$mailClass = acymailing_get('helper.mailer');
			$mailClass->checkConfirmField = false;
			$mailClass->checkEnabled = false;
			$mailClass->report = false;
			$modifySubscriptionSuccess = $mailClass->sendOne('modif',$myuser->subid);
			$modifySubscriptionError = $mailClass->reportMessage;
		}

		$userClass->sendNotification();

		if($config->get('subscription_message',1) || $ajax){
			if($allowSubscriptionModifications){
				if($statusAdd == 2){
					if($userClass->confirmationSentSuccess){
						$msg = 'CONFIRMATION_SENT';
						$code = 2;
						$msgtype = 'success';
					}else{
						$msg = $userClass->confirmationSentError;
						$code = 7;
						$msgtype = 'error';
					}
				}else{
					if($insertMessage){
						$msg = 'SUBSCRIPTION_OK';
						$code = 3;
						$msgtype = 'success';
					}elseif($updateMessage){

						$msg = 'SUBSCRIPTION_UPDATED_OK';
						$code = 4;
						$msgtype = 'success';
					}else{
						$msg = 'ALREADY_SUBSCRIBED';
						$code = 5;
						$msgtype = 'success';
					}
				}
			}else{
				if($modifySubscriptionSuccess){
					$msg = 'IDENTIFICATION_SENT';
					$code = 6;
					$msgtype = 'warning';
				}else{
					$msg = $modifySubscriptionError;
					$code = 8;
					$msgtype = 'error';
				}
			}

			if($msg == strtoupper($msg)){
				$source = JRequest::getCmd('acy_source');
				if(strpos($source, 'module_') !== false){
					$moduleId = '_'.strtoupper($source);
					if(JText::_($msg.$moduleId) != $msg.$moduleId) $msg = $msg.$moduleId;
				}
				$msg = JText::_($msg);
			}

			$replace = array();
			$replace['{list:name}'] = '';
			foreach($myuser as $oneProp => $oneVal){
				$replace['{user:'.$oneProp.'}'] = $oneVal;
			}
			$msg = str_replace(array_keys($replace),$replace,$msg);

			$redirectUrl = str_replace(array_keys($replace),$replace,$redirectUrl);

			if($ajax){
				$msg = str_replace(array("\n","\r",'"','\\'),array(' ',' ',"'",'\\\\'),$msg);
				echo '{"message":"'.$msg.'","type":"'.($msgtype == 'warning' ? 'success' : $msgtype).'","code":"'.$code.'"}';
			}elseif(empty($redirectUrl)){
				acymailing_display($msg,$msgtype == 'success' ? 'info' : $msgtype);
			}else{
				if(strlen($msg)>0){
					if($msgtype == 'success') $app->enqueueMessage($msg);
					elseif($msgtype == 'warning') $app->enqueueMessage($msg,'notice');
					else $app->enqueueMessage($msg,'error');
				}
			}
		}

		$notifContact = $config->get('notification_contact');
		if(!empty($notifContact)){
			$mailer = acymailing_get('helper.mailer');
			$mailer->autoAddUser = true;
			$mailer->checkConfirmField = false;
			$mailer->report = false;
			foreach($user as $field => $value) $mailer->addParam('user:'.$field,$value);
			$mailer->addParam('user:subscription',$listsubClass->getSubscriptionString($user->subid));
			$mailer->addParam('user:subscriptiondates',$listsubClass->getSubscriptionString($user->subid, true));
			$mailer->addParam('user:ip',$userHelper->getIP());
			if(!empty($userClass->geolocData)){
				foreach($userClass->geolocData as $map=>$value){
					$mailer->addParam('geoloc:notif_'.$map,$value);
				}
			}
			$mailer->addParamInfo();
			$allUsers = explode(' ',trim(str_replace(array(';',','),' ',$notifContact)));
			foreach($allUsers as $oneUser){
				if(empty($oneUser)) continue;
				$mailer->sendOne('notification_contact',$oneUser);
			}
		}

		if ($ajax) exit;

		$this->_closepop($redirectUrl);

		$this->setRedirect($redirectUrl);
		return true;
	}

	private function _closepop($redirectUrl){
		$this->_checkRedirectUrl($redirectUrl);

		if(empty($redirectUrl) OR !JRequest::getInt('closepop')) return;

		echo '<script type="text/javascript" language="javascript">
					window.parent.document.location.href=\''.str_replace('&amp;','&',$redirectUrl).'\';
					d = window.parent.document;
					w = window.parent;
					var e = d.getElementById(\'sbox-window\');
					if(e && typeof(e.close) != "undefined") {
						e.close();
					}else if(typeof(w.jQuery) != "undefined" && w.jQuery(\'div.modal.in\') && w.jQuery(\'div.modal.in\').hasClass(\'in\')){
						w.jQuery(\'div.modal.in\').modal(\'hide\');
					}else if(w.SqueezeBox !== undefined) {
						w.SqueezeBox.close();
					}
				</script>';

		$app = JFactory::getApplication();
		$messages = $app->getMessageQueue();
		if(!empty($messages)){
			$session = JFactory::getSession();
			$session->set('application.queue', $messages);
		}

		exit;
	}

	function optout(){
		acymailing_checkRobots();
		$config = acymailing_config();
		$app = JFactory::getApplication();
		$userClass = acymailing_get('class.subscriber');
		$userClass->geolocRight = true;
		$my = JFactory::getUser();

		$ajax = JRequest::getInt('ajax',0);
		if($ajax){
			@ob_end_clean();
			header("Content-type:text/html; charset=utf-8");
		}

		if(empty($my->id)){
			if(acymailing_level(1) && $config->get('captcha_enabled')){
				$seckey = JRequest::getString('seckey');
				$captchaCode = JRequest::getString('acycaptcha');

				if(!empty($seckey)){
					if($config->get('security_key') !== $seckey){
						if($ajax){
							echo '{"message":"'.str_replace('"','\"',JText::_('ERROR_SECURE_KEY')).'","type":"error","code":"0"}';
						}else{
							echo JText::_('ERROR_SECURE_KEY',true);
						}
						exit;
					}
				}else{
					$captchaClass = acymailing_get('class.acycaptcha');
					$captchaClass->state = 'acycaptchamodule'.JRequest::getCmd('acyformname');
					if(!$captchaClass->check($captchaCode)){
						if($ajax){
							echo '{"message":"'.str_replace('"','\"',JText::_('ERROR_CAPTCHA')).'","type":"error","code":"0"}';
						}else{
							$captchaClass->returnError();
						}
						exit;
					}
				}
			}
		}

		$redirectUrl = urldecode(JRequest::getString('redirectunsub'));
		if(!empty($redirectUrl)) $this->setRedirect($redirectUrl);

		$formData = JRequest::getVar( 'user', array(), '', 'array' );

		$email = trim(strip_tags(@$formData['email']));

		if(empty($email) && !empty($my->email)){
			$email = $my->email;
		}

		$userHelper = acymailing_get('helper.user');
		if(empty($email) || !$userHelper->validEmail($email)){
			if ($ajax) echo '{"message":"'.str_replace('"','\"',JText::_('VALID_EMAIL')).'","type":"error","code":"7"}';
			else echo "<script>alert('".JText::_('VALID_EMAIL',true)."'); window.history.go(-1);</script>";
			exit;
		}

		$alreadyExists = $userClass->get($email);

		if(empty($alreadyExists->subid)){
			if ($ajax){
				echo '{"message":"'.str_replace('"','\"',JText::sprintf('NOT_IN_LIST','<b><i>'.$email.'</i></b>')).'","type":"error","code":"8"}';
				exit;
			}
			if(empty($redirectUrl)) acymailing_display(JText::sprintf('NOT_IN_LIST','<b><i>'.$email.'</i></b>'),'warning');
			else $app->enqueueMessage(JText::sprintf('NOT_IN_LIST','<b><i>'.$email.'</i></b>'),'notice');
			return $this->_closepop($redirectUrl);
		}

		if($config->get('allow_modif','data') == 'none' AND (empty($my->email) || $my->email != $email)){
			$mailClass = acymailing_get('helper.mailer');
			$mailClass->checkConfirmField = false;
			$mailClass->checkEnabled = false;
			$mailClass->report = false;
			$mailClass->sendOne('modif',$alreadyExists->subid);
			if ($ajax){
				echo '{"message":"'.str_replace('"','\"',JText::_('IDENTIFICATION_SENT')).'","type":"success","code":"9"}';
				exit;
			}
			if(empty($redirectUrl)) acymailing_display(JText::_( 'IDENTIFICATION_SENT' ),'warning');
			else $app->enqueueMessage(JText::_( 'IDENTIFICATION_SENT' ), 'notice');
			return $this->_closepop($redirectUrl);
		}

		$visibleSubscription = JRequest::getVar('subscription','','','array');
		$currentSubscription = $userClass->getSubscriptionStatus($alreadyExists->subid);
		$hiddenSubscription = explode(',',JRequest::getVar('hiddenlists','','','string'));

		$updatelists = array();
		$removeSubscription = array_merge($visibleSubscription,$hiddenSubscription);
		foreach($removeSubscription as $idList){
			if(!empty($currentSubscription[$idList]) AND $currentSubscription[$idList]->status != '-1'){
				$updatelists[-1][] = $idList;
			}
		}

		if(!empty($updatelists)){
			$listsubClass = acymailing_get('class.listsub');
			$listsubClass->updateSubscription($alreadyExists->subid,$updatelists);
			if($config->get('unsubscription_message',1)){
				if ($ajax){
					echo '{"message":"'.str_replace('"','\"',JText::_('UNSUBSCRIPTION_OK')).'","type":"success","code":"10"}';
					exit;
				}
				if(empty($redirectUrl)) acymailing_display(JText::_('UNSUBSCRIPTION_OK'),'info');
				else{
					if(strlen(JText::_('UNSUBSCRIPTION_OK'))>0){
						$app->enqueueMessage(JText::_('UNSUBSCRIPTION_OK'));
					}
				}
			}
		}elseif($config->get('unsubscription_message',1) || $ajax){
			if ($ajax){
				echo '{"message":"'.str_replace('"','\"',JText::_('UNSUBSCRIPTION_NOT_IN_LIST')).'","type":"success","code":"11"}';
				exit;
			}
			if(empty($redirectUrl)) acymailing_display(JText::_('UNSUBSCRIPTION_NOT_IN_LIST'),'info');
			else $app->enqueueMessage(JText::_('UNSUBSCRIPTION_NOT_IN_LIST'));
		}

		if ($ajax) exit;

		return $this->_closepop($redirectUrl);

	}

	private function _checkRedirectUrl($redirectUrl){
		$config = acymailing_config();
		$regex = trim(preg_replace('#[^a-z0-9\|\.]#i','',$config->get('module_redirect')),'|');
		if($regex != 'all' && !empty($redirectUrl)){
			preg_match('#^(https?://)?(www.)?([^/]*)#i',$redirectUrl,$resultsurl);
			$domainredirect = preg_replace('#[^a-z0-9\.]#i','',@$resultsurl[3]);
			if(!preg_match('#^'.$regex.'$#i',$domainredirect)){
				$regex .= '|'.$domainredirect;
				echo "<script>alert('This redirect url is not allowed, you should change the \"".JText::_('REDIRECTION_MODULE',true)."\" parameter from the AcyMailing configuration page to \"".$regex."\" to allow it or set it to \"all\" to allow all urls'); window.history.go(-1);</script>";
				exit;
			}
		}
	}

	function listing(){
		$errorMsg = "You shouldn't see this page. If you come from an external subscription form, maybe the URL in the form action is not valid.";
		if(!empty($_SERVER['HTTP_HOST'])) $errorMsg .= "<br />Host: ".htmlspecialchars($_SERVER['HTTP_HOST'],ENT_COMPAT, 'UTF-8');
		if(!empty($_SERVER['REQUEST_URI'])) $errorMsg .= "<br />URI: ".htmlspecialchars($_SERVER['REQUEST_URI'],ENT_COMPAT, 'UTF-8');
		if(!empty($_SERVER['HTTP_REFERER'])) $errorMsg .= "<br />Referer: ".htmlspecialchars($_SERVER['HTTP_REFERER'],ENT_COMPAT, 'UTF-8');
		acymailing_display($errorMsg, 'error');
	}

}
