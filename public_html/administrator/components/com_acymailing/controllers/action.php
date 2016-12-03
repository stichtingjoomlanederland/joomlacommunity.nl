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

class ActionController extends acymailingController{

	var $pkey = 'action_id';
	var $table = 'action';
	var $aclCat = 'distribution';

	function listing(){
		if(!acymailing_level(3)){
			$acyToolbar = acymailing::get('helper.toolbar');
			$acyToolbar->setTitle(JText::_('ACY_DISTRIBUTION'), 'action');
			$acyToolbar->help('distributionlists#listing');
			$acyToolbar->display();
			$config = acymailing_config();
			$level = $config->get('level');
			$url = ACYMAILING_HELPURL.'paidversion&utm_source=acymailing-'.$level.'&utm_medium=back-end&utm_content=distributionlist-display&utm_campaign=upgrade';
			$iFrame = "<iframe class='paidversion' frameborder='0' src='$url' width='100%' height='100%' scrolling='auto'></iframe>";
			echo $iFrame.'<div id="iframedoc"></div>';
			return;
		}

		return parent::listing();
	}

	function store(){
		if(!$this->isAllowed($this->aclCat, 'manage')) return;
		acymailing_checkToken();

		$actionClass = acymailing_get('class.action');
		$status = $actionClass->saveForm();
		if($status){
			acymailing_enqueueMessage(JText::_('JOOMEXT_SUCC_SAVED'), 'message');
		}else{
			acymailing_enqueueMessage(JText::_('ERROR_SAVING'), 'error');
			if(empty($actionClass->errors)) return;
			foreach($actionClass->errors as $oneError){
				acymailing_enqueueMessage($oneError, 'error');
			}
		}
	}

	function copy(){
		if(!$this->isAllowed($this->aclCat, 'manage')) return;
		acymailing_checkToken();

		$cids = JRequest::getVar('cid', array(), '', 'array');
		$db = JFactory::getDBO();

		$my = JFactory::getUser();
		$creatorId = intval($my->id);

		foreach($cids as $oneActionid){
			$query = 'INSERT INTO `#__acymailing_action` (`name`, `description`, `server`, `port`, `connection_method`, `secure_method`, `self_signed`, `username`, `password`, `userid`, `conditions`, `actions`, `report`, `frequency`, `nextdate`, `published`)';
			$query .= ' SELECT CONCAT("copy_",`name`), `description`, `server`, `port`, `connection_method`, `secure_method`, `self_signed`, `username`, `password`, '.$creatorId.', `conditions`, `actions`, "", `frequency`, `nextdate`, 0 FROM `#__acymailing_action` WHERE `action_id` = '.intval($oneActionid);
			$db->setQuery($query);
			$db->query();
		}

		return $this->listing();
	}

	function remove(){
		if(!$this->isAllowed($this->aclCat, 'delete')) return;
		acymailing_checkToken();

		$actionIds = JRequest::getVar('cid', array(), '', 'array');

		$actionClass = acymailing_get('class.action');
		$num = $actionClass->delete($actionIds);

		acymailing_enqueueMessage(JText::sprintf('SUCC_DELETE_ELEMENTS', $num), 'message');

		JRequest::setVar('layout', 'listing');
		return parent::display();
	}

	function test(){
		$this->store();
		acymailing_increasePerf();

		$bounceHelper = acymailing_get('helper.bounce');
		$bounceHelper->report = true;

		$actionClass = acymailing_get('class.action');
		$action = $actionClass->get(acymailing_getCID());
		if(empty($action)){
			JRequest::setVar('layout', 'form');
			return parent::display();
		}
		$bounceHelper->action = $action;

		if($bounceHelper->init()){
			if($bounceHelper->connect()){
				$nbMessages = $bounceHelper->getNBMessages();
				acymailing_enqueueMessage(JText::sprintf('BOUNCE_CONNECT_SUCC', $action->username));
				acymailing_enqueueMessage(JText::sprintf('NB_MAIL_MAILBOX', $nbMessages));
				$bounceHelper->close();
				if(!empty($nbMessages)){
					acymailing_enqueueMessage('<a class="modal" style="text-decoration:blink" rel="{handler: \'iframe\', size: {x: 640, y: 480}}" href="'.acymailing_completeLink("action&task=process&action_id=".$action->action_id, true).'">'.JText::_('CLICK_BOUNCE').'</a>');
				}
			}else{
				$errors = $bounceHelper->getErrors();
				if(!empty($errors)){
					acymailing_enqueueMessage($errors, 'error');
					$errorString = implode(' ', $errors);
					if(preg_match('#certificate#i', $errorString) && !$action->self_signed){
						acymailing_enqueueMessage('You may need to turn ON the option <i>'.JText::_('BOUNCE_CERTIF').'</i>', 'warning');
					}elseif(!empty($action->port) && !in_array($action->port, array('993', '143', '110'))){
						acymailing_enqueueMessage('Are you sure you selected the right port? You can leave it empty if you do not know what to specify', 'warning');
					}
				}
			}
		}

		JRequest::setVar('layout', 'form');
		return parent::display();
	}

	function process(){
		if(!$this->isAllowed($this->aclCat, 'manage')) die('Not allowed');
		acymailing_increasePerf();

		$actionClass = acymailing_get('class.action');
		$action = $actionClass->get(JRequest::getInt('action_id', 0));
		if(empty($action)) exit;

		$bounceHelper = acymailing_get('helper.bounce');
		$bounceHelper->report = true;
		$bounceHelper->action = $action;

		if(!$bounceHelper->init()) exit;
		if(!$bounceHelper->connect()){
			acymailing_display($bounceHelper->getErrors(), 'error');
			exit;
		}

		echo "<html>\n<head>\n<meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\" />\n";
		echo '<title>'.addslashes(JText::_('ACY_DISTRIBUTION'))."</title>\n";
		echo "<style>body{font-size:12px;font-family: Arial,Helvetica,sans-serif;padding-top:30px;}</style>\n</head>\n<body>";

		acymailing_display(JText::sprintf('BOUNCE_CONNECT_SUCC', $action->username), 'success');
		$nbMessages = $bounceHelper->getNBMessages();
		acymailing_display(JText::sprintf('NB_MAIL_MAILBOX', $nbMessages), 'info');

		if(empty($nbMessages)) exit;

		$bounceHelper->handleAction();
		$bounceHelper->close();

		echo "</body></html>";
		while($bounceHelper->obend-- > 0){
			ob_start();
		}
		exit;
	}
}
