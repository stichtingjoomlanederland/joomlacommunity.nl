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

class SubscriberController extends acymailingController{

	var $pkey = 'subid';
	var $allowedInfo = array();
	var $aclCat = 'subscriber';

	function choose(){
		if(!$this->isAllowed('subscriber', 'view')) return;
		JRequest::setVar('layout', 'choose');
		return parent::display();
	}

	function export(){
		if(!$this->isAllowed('subscriber', 'export')) return;
		$app = JFactory::getApplication();
		$cids = JRequest::getVar('cid');
		$selectedList = JRequest::getInt('filter_lists');
		$_SESSION['acymailing'] = array();
		$redirection = ($app->isAdmin() ? '' : 'front').'data&task=export';
		if(!empty($cids) || !empty($selectedList)){
			if(!empty($cids)){
				$_SESSION['acymailing']['exportusers'] = $cids;
			}else{
				$_SESSION['acymailing']['exportlist'] = $selectedList;
				$_SESSION['acymailing']['exportliststatus'] = JRequest::getInt('filter_statuslist');
			}
			$redirection .= '&sessionvalues=1';
		}

		if(acymailing_level(3)){
			$fieldClass = acymailing_get('class.fields');
			$user = null;
			$allFields = $fieldClass->getFields('all', $user);
			foreach($_REQUEST as $key => $value){
				if(strpos($key, 'fieldfilter_') === 0){
					$fieldName = substr($key, 12);
					if($value != '- '.JText::_($allFields[$fieldName]->fieldname).' -') $_SESSION['acymailing']['fieldfilter'][$fieldName] = $value;
				}
			}
			if(!empty($_SESSION['acymailing']['fieldfilter'])) $redirection .= '&fieldfilters=1';
		}

		$this->setRedirect(acymailing_completeLink($redirection, false, true));
	}

	function store(){
		if(!$this->isAllowed('subscriber', 'manage')) return;
		JRequest::checkToken() or die('Invalid Token');

		$subscriberClass = acymailing_get('class.subscriber');
		$subscriberClass->sendConf = false;
		$subscriberClass->sendNotif = false;
		$subscriberClass->sendWelcome = false;
		$subscriberClass->allowModif = true;
		$subscriberClass->checkAccess = false;
		$subscriberClass->triggerFilterBE = true;
		$subscriberClass->checkVisitor = false;

		$status = $subscriberClass->saveForm();
		if($status){
			acymailing_enqueueMessage(JText::_('JOOMEXT_SUCC_SAVED'), 'message');
		}else{
			acymailing_enqueueMessage(JText::_('ERROR_SAVING'), 'error');
			if(!empty($subscriberClass->errors)){
				foreach($subscriberClass->errors as $oneError){
					acymailing_enqueueMessage($oneError, 'error');
				}
			}
		}
	}

	function remove(){
		JRequest::checkToken() or die('Invalid Token');
		$app = JFactory::getApplication();
		$config = acymailing_config();
		$deleteBehaviour = $config->get('frontend_delete_button', 'delete');
		$subscriberIds = JRequest::getVar('cid', array(), '', 'array');
		if($app->isAdmin() || $deleteBehaviour == 'delete'){
			if(!$this->isAllowed('subscriber', 'delete')) return;

			$subscriberObject = acymailing_get('class.subscriber');
			$num = $subscriberObject->delete($subscriberIds);

			acymailing_enqueueMessage(JText::sprintf('SUCC_DELETE_ELEMENTS', $num), 'message');
		}else{
			if(!$this->isAllowed('subscriber', 'manage')) return;

			$listId = JRequest::getInt('filter_lists', 0);
			if(empty($listId)){
				acymailing_enqueueMessage('List not found', 'error');
			}else{
				$listsubClass = acymailing_get('class.listsub');
				foreach($subscriberIds as $subid){
					$listsubClass->removeSubscription($subid, array($listId));
				}

				$listClass = acymailing_get('class.list');
				$list = $listClass->get($listId);

				acymailing_enqueueMessage(JText::sprintf('IMPORT_REMOVE', count($subscriberIds), $list->name), 'message');
			}
		}

		JRequest::setVar('layout', 'listing');
		return parent::display();
	}

	function getSubscribersByEmail(){
		$app = JFactory::getApplication();
		$NameSearched = JRequest::getString('search', '');
		if(empty($NameSearched) || !$app->isAdmin() || !$this->isAllowed('subscriber', 'view')) exit;

		$db = JFactory::getDBO();
		$NameSearched = '\'%'.acymailing_getEscaped($NameSearched, true).'%\'';
		$db->setQuery('SELECT name, email FROM #__acymailing_subscriber WHERE email LIKE '.$NameSearched.' OR name LIKE '.$NameSearched.' ORDER BY email ASC LIMIT 30');
		$users = $db->loadObjectList();
		if(empty($users)) exit;

		echo '<table style="width:100%;">';
		foreach($users as $oneUser){
			echo '<tr class="row_user" onclick="setUser(\''.str_replace("'", "\'", $oneUser->email).'\');"><td>'.htmlspecialchars($oneUser->name, ENT_COMPAT, 'UTF-8').'</td><td>'.htmlspecialchars($oneUser->email, ENT_COMPAT, 'UTF-8').'</td></tr>';
		}
		echo '</table>';
		exit;
	}
}
