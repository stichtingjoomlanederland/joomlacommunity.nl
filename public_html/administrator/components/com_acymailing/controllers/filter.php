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

class FilterController extends acymailingController{
	var $pkey = 'filid';
	var $table = 'filter';

	function listing(){
		return $this->add();
	}

	function countresults(){
		$filterClass = acymailing_get('class.filter');
		$num = JRequest::getInt('num');
		$filters = JRequest::getVar('filter');
		$query = new acyQuery();
		if(empty($filters['type'][$num])) die('No filter type found for the num '.intval($num));
		$currentType = $filters['type'][$num];
		if(empty($filters[$num][$currentType])) die('No filter parameters founds for the num '.intval($num));
		$currentFilterData = $filters[$num][$currentType];
		JPluginHelper::importPlugin('acymailing');
		$dispatcher = JDispatcher::getInstance();
		$messages = $dispatcher->trigger('onAcyProcessFilterCount_'.$currentType,array(&$query,$currentFilterData,$num));
		echo implode(' | ',$messages);
		exit;
	}

	function displayCondFilter(){
		JPluginHelper::importPlugin('acymailing');
		$fct = JRequest::getVar('fct');

		$dispatcher = JDispatcher::getInstance();
		$message = $dispatcher->trigger('onAcyTriggerFct_'.$fct);
		echo implode(' | ',$message);
		exit;
	}

	function process(){
		if(!$this->isAllowed('lists','filter')) return;
		JRequest::checkToken() or die( 'Invalid Token' );

		$filid = JRequest::getInt('filid');
		if(!empty($filid)){
			$this->store();
		}

		$filterClass = acymailing_get('class.filter');
		$filterClass->subid = JRequest::getString('subid');
		$filterClass->execute(JRequest::getVar('filter'),JRequest::getVar('action'));

		if(!empty($filterClass->report)){
			if(JRequest::getCmd('tmpl') == 'component'){
				echo acymailing_display($filterClass->report,'info');
				$js = "setTimeout('redirect()',2000); function redirect(){window.top.location.href = 'index.php?option=com_acymailing&ctrl=subscriber'; }";
				$doc = JFactory::getDocument();
				$doc->addScriptDeclaration( $js );
				return;
			}else{
				foreach($filterClass->report as $oneReport){
					acymailing_enqueueMessage($oneReport);
				}
			}
		}
		return $this->edit();
	}

	function filterDisplayUsers(){
		if(!$this->isAllowed('lists','filter')) return;
		JRequest::checkToken() or die( 'Invalid Token' );
		return $this->edit();
	}

	function store(){
		if(!$this->isAllowed('lists','filter')) return;
		JRequest::checkToken() or die( 'Invalid Token' );

		$class = acymailing_get('class.filter');
		$status = $class->saveForm();
		if($status){
			acymailing_enqueueMessage(JText::_( 'JOOMEXT_SUCC_SAVED' ), 'message');
		}else{
			acymailing_enqueueMessage(JText::_( 'ERROR_SAVING' ), 'error');
			if(!empty($class->errors)){
				foreach($class->errors as $oneError){
					acymailing_enqueueMessage($oneError, 'error');
				}
			}
		}
	}
}
