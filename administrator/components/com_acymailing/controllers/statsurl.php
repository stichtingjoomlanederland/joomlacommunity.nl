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

class StatsurlController extends acymailingController{

	var $aclCat = 'statistics';

	function save(){
		if(!$this->isAllowed($this->aclCat, 'manage')) return;
		JRequest::checkToken() or die('Invalid Token');

		$class = acymailing_get('class.url');
		$status = $class->saveForm();
		if($status){
			acymailing_display(JText::_('JOOMEXT_SUCC_SAVED'), 'success');
			return true;
		}else{
			acymailing_display(JText::_('ERROR_SAVING'), 'success');
		}

		return $this->edit();
	}

	function detaillisting(){
		if(!$this->isAllowed($this->aclCat, 'manage') || !$this->isAllowed('subscriber', 'view')) return;
		JRequest::setVar('layout', 'detaillisting');
		return parent::display();
	}

	function export(){
		$selectedMail = JRequest::getInt('filter_mail', 0);
		$selectedUrl = JRequest::getInt('filter_url', 0);

		$filters = array();
		if(!empty($selectedMail)) $filters[] = 'urlclick.mailid = '.$selectedMail;
		if(!empty($selectedUrl)) $filters[] = 'urlclick.urlid = '.$selectedUrl;
		$query = 'FROM `#__acymailing_urlclick` as urlclick JOIN `#__acymailing_subscriber` as s ON s.subid = urlclick.subid JOIN `#__acymailing_url` as url ON url.urlid = urlclick.urlid';
		if(!empty($filters)) $query .= ' WHERE ('.implode(') AND (', $filters).')';

		$currentSession = JFactory::getSession();
		$currentSession->set('acyexportquery', $query);

		$app = JFactory::getApplication();
		$app->redirect(acymailing_completeLink(($app->isAdmin() ? '' : 'front').'data&task=export&sessionquery=1', JRequest::getCmd('tmpl', '') != '', true));
	}

}
