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

class FileController extends acymailingController{
	function language(){
		JRequest::setVar('layout', 'language');
		return parent::display();
	}

	function save(){
		JRequest::checkToken() or die('Invalid Token');

		$this->_savelanguage();
		return $this->language();
	}

	function savecss(){
		if(!$this->isAllowed('configuration', 'manage')) return;
		JRequest::checkToken() or die('Invalid Token');

		$file = JRequest::getCmd('file');
		if(!preg_match('#^([-a-z0-9]*)_([-_a-z0-9]*)$#i', $file, $result)){
			acymailing_display('Could not load the file '.$file.' properly');
			exit;
		}
		$type = $result[1];
		$fileName = $result[2];

		jimport('joomla.filesystem.file');

		$path = ACYMAILING_MEDIA.'css'.DS.$type.'_'.$fileName.'.css';
		$csscontent = JRequest::getString('csscontent');

		$alreadyExists = file_exists($path);

		if(JFile::write($path, $csscontent)){
			acymailing_enqueueMessage(JText::_('JOOMEXT_SUCC_SAVED'), 'success');
			$varName = JRequest::getCmd('var');
			if(!$alreadyExists){
				$js = "var optn = document.createElement(\"OPTION\");
						optn.text = '$fileName'; optn.value = '$fileName';
						mydrop = window.top.document.getElementById('".$varName."_choice');
						mydrop.options.add(optn);
						lastid = 0; while(mydrop.options[lastid+1]){lastid = lastid+1;} mydrop.selectedIndex = lastid;
						window.top.updateCSSLink('".$varName."','$type','$fileName');";
				$doc = JFactory::getDocument();
				$doc->addScriptDeclaration($js);
			}
			$config = acymailing_config();
			$newConfig = new stdClass();
			$newConfig->$varName = $fileName;
			$config->save($newConfig);
		}else{
			acymailing_enqueueMessage(JText::sprintf('FAIL_SAVE', $path), 'error');
		}

		return $this->css();
	}

	function css(){
		JRequest::setVar('layout', 'css');
		return parent::display();
	}

	function latest(){
		return $this->language();
	}

	function send(){
		if(!$this->isAllowed('configuration', 'manage')) return;
		JRequest::checkToken() or die('Invalid Token');

		$bodyEmail = JRequest::getString('mailbody');
		$code = JRequest::getCmd('code');
		JRequest::setVar('code', $code);

		if(empty($code)) return;

		jimport('joomla.filesystem.file');

		$config = acymailing_config();
		$mailer = acymailing_get('helper.mailer');
		$mailer->Subject = '[ACYMAILING LANGUAGE FILE] '.$code;
		$mailer->Body = 'The website '.ACYMAILING_LIVE.' using AcyMailing '.$config->get('level').' '.$config->get('version').' sent a language file : '.$code;
		$mailer->Body .= "\n"."\n"."\n".$bodyEmail;

		$extrafile = JLanguage::getLanguagePath(JPATH_ROOT).DS.$code.DS.$code.'.com_acymailing_custom.ini';

		if(file_exists($extrafile)){
			$mailer->Body .= "\n"."\n"."\n".'Custom content:'."\n".file_get_contents($extrafile);
		}
		$user = JFactory::getUser();
		$mailer->AddAddress($user->email, $user->name);
		$mailer->AddAddress('translate@acyba.com', 'Acyba Translation Team');
		$mailer->report = false;

		$path = JPath::clean(JLanguage::getLanguagePath(JPATH_ROOT).DS.$code.DS.$code.'.com_acymailing.ini');
		$mailer->AddAttachment($path);

		$result = $mailer->Send();
		if($result){
			acymailing_display(JText::_('THANK_YOU_SHARING'), 'success');
			acymailing_display($mailer->reportMessage, 'success');
		}else{
			acymailing_display($mailer->reportMessage, 'error');
		}
	}

	function share(){
		if(!$this->isAllowed('configuration', 'manage')) return;
		JRequest::checkToken() or die('Invalid Token');

		if($this->_savelanguage()){
			JRequest::setVar('layout', 'share');
			return parent::display();
		}else{
			return $this->language();
		}
	}

	function _savelanguage(){
		if(!$this->isAllowed('configuration', 'manage')) return;
		JRequest::checkToken() or die('Invalid Token');

		jimport('joomla.filesystem.file');
		$code = JRequest::getCmd('code');
		JRequest::setVar('code', $code);
		$content = JRequest::getVar('content', '', '', 'string', JREQUEST_ALLOWHTML);
		$content = str_replace('</textarea>', '', $content);

		if(empty($code) OR empty($content)) return;

		$path = JLanguage::getLanguagePath(JPATH_ROOT).DS.$code.DS.$code.'.com_acymailing.ini';
		$result = JFile::write($path, $content);
		if($result){
			acymailing_enqueueMessage(JText::_('JOOMEXT_SUCC_SAVED'), 'success');
			$js = "window.top.document.getElementById('image$code').className = 'acyicon-edit'";
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration($js);

			$updateHelper = acymailing_get('helper.update');
			$updateHelper->installMenu($code);
		}else{
			acymailing_enqueueMessage(JText::sprintf('FAIL_SAVE', $path), 'error');
		}

		$customcontent = JRequest::getVar('customcontent', '', '', 'string', JREQUEST_ALLOWHTML);
		$customcontent = str_replace('</textarea>', '', $customcontent);
		$custompath = JLanguage::getLanguagePath(JPATH_ROOT).DS.$code.DS.$code.'.com_acymailing_custom.ini';
		$customresult = JFile::write($custompath, $customcontent);
		if(!$customresult) acymailing_enqueueMessage(JText::sprintf('FAIL_SAVE', $custompath), 'error');

		return $result;
	}

	function installLanguages(){
		$languages = JRequest::getString('languages');
		ob_start();
		$languagesContent = acymailing_fileGetContent(ACYMAILING_UPDATEURL.'loadLanguages&json=1&codes='.$languages);
		$warnings = ob_get_clean();
		if(!empty($warnings) && defined('JDEBUG') && JDEBUG) echo $warnings;

		if(empty($languagesContent)){
			acymailing_display('Could not load the language files from our server, you can update them in the AcyMailing configuration page, tab "Languages" or start your own translation and share it', 'error');
			exit;
		}

		$decodedLanguages = json_decode($languagesContent, true);

		jimport('joomla.filesystem.file');
		$updateHelper = acymailing_get('helper.update');
		$success = array();
		$error = array();

		foreach($decodedLanguages as $code => $content){
			if(empty($content)){
				$error[] = 'The language '.$code.' was not found on our server, you can start your own translation in the AcyMailing configuration page, tab "Languages" then share it';
				continue;
			}

			if(JFile::write(JPATH_SITE.DS.'language'.DS.$code.DS.$code.'.com_acymailing.ini', $content)){
				$updateHelper->installMenu($code);
				$success[] = 'Successfully installed language: '.$code;
			}else{
				$error[] = JText::sprintf('FAIL_SAVE', $code.'.com_acymailing.ini');
			}
		}

		if(!empty($success)) acymailing_display($success, 'success');
		if(!empty($error)) acymailing_display($error, 'error');
		exit;
	}

	function select(){
		JRequest::setVar('layout', 'select');
		return parent::display();
	}
}
