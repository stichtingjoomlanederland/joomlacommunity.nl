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


class chooselistViewchooselist extends acymailingView
{
	function display($tpl = null)
	{
		$function = $this->getLayout();
		if(method_exists($this,$function)) $this->$function();

		parent::display($tpl);
	}

	function listing(){

		$listClass = acymailing_get('class.list');
		$rows = $listClass->getLists();

		$selectedLists = JRequest::getVar('values','','','string');

		if(strtolower($selectedLists) == 'all'){
			foreach($rows as $id => $oneRow){
				$rows[$id]->selected = true;
			}
		}elseif(!empty($selectedLists)){
			$selectedLists = explode(',',$selectedLists);
			foreach($rows as $id => $oneRow){
				if(in_array($oneRow->listid,$selectedLists)){
					$rows[$id]->selected = true;
				}
			}
		}

		$fieldName = JRequest::getString('task');
		$controlName = JRequest::getString('control','params');
		$popup = JRequest::getString('popup','1');

		$this->assignRef('rows',$rows);
		$this->assignRef('selectedLists',$selectedLists);
		$this->assignRef('fieldName',$fieldName);
		$this->assignRef('controlName',$controlName);
		$this->assignRef('popup',$popup);
	}


	function customfields(){

		$fieldsClass = acymailing_get('class.fields');
		$fake=null;
		$rows = $fieldsClass->getFields('module',$fake);

		$selected = JRequest::getVar('values','','','string');
		$selectedvalues = explode(',',$selected);
		foreach($rows as $id => $oneRow){
			if(in_array($oneRow->namekey,$selectedvalues)){
				$rows[$id]->selected = true;
			}
		}

		$this->assignRef('fieldsClass',$fieldsClass);
		$this->assignRef('rows',$rows);
		$controlName = JRequest::getString('control','params');
		$this->assignRef('controlName',$controlName);
	}
}
