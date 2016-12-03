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
if(!acymailing_isAllowed($config->get('acl_subscriber_manage', 'all'))) die('You are not allowed to access this page');

$frontHelper = acymailing_get('helper.acyfront');
include(ACYMAILING_BACK.'controllers'.DS.'subscriber.php');

class FrontsubscriberController extends SubscriberController{

	function __construct($config = array()){
		parent::__construct($config);

		$app = JFactory::getApplication();

		$listid = JRequest::getInt('listid');
		if(empty($listid)){
			$listid = JRequest::getInt('filter_lists');
		}
		if(empty($listid)){
			$listid = $app->getUserState("com_acymailing.frontsubscriberfilter_lists");
		}
		if(empty($listid)){
			$listClass = acymailing_get('class.list');
			$allAllowedLists = $listClass->getFrontendLists();
			if(!empty($allAllowedLists)){
				$firstList = reset($allAllowedLists);
				$listid = $firstList->listid;
			}
		}

		JRequest::setVar('filter_lists', $listid);
		JRequest::setVar('listid', $listid);

		if(!acyCheckAccessList()){
			$app->enqueueMessage('You can not have access to this list', 'error');
			$app->redirect('index.php');
			return false;
		}

		if(in_array(JRequest::getCmd('task'), array('edit', 'add')) && !acyCheckEditUser()){
			$app->enqueueMessage('This user does not belong to your list', 'error');
			$app->redirect('index.php?option=com_acymailing');
			return false;
		}
	}

	function addqueue(){
		if(!$this->isAllowed('newsletters', 'schedule')) return;
		JRequest::setVar('layout', 'addqueue');
		return parent::display();
	}

	function scheduleone(){
		$sendController = acymailing_get('controller.send');
		$sendController->scheduleone();
	}
}
