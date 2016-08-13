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

class listsType{
	function __construct(){

		$listClass = acymailing_get('class.list');
		$this->data = $listClass->getLists('listid');
	}

	function display($map, $value, $js = true, $clickableCategories = false){
		if(empty($this->values)) $this->getValues($clickableCategories);
		$onchange = $js ? 'onchange="document.adminForm.limitstart.value=0;document.adminForm.submit();"' : '';
		return JHTML::_('select.genericlist', $this->values, $map, 'class="inputbox" style="max-width:220px" size="1" '.$onchange, 'value', 'text', $value, str_replace(array('[', ']'), array('_', ''), $map));
	}

	function getData(){
		return $this->data;
	}

	function getValues($clickableCategories = false){
		$allCats = array();
		foreach($this->data as $oneList){
			if(empty($oneList->category)) $oneList->category = JText::_('ACY_NO_CATEGORY');
			$allCats[$oneList->category][] = $oneList->listid;
		}

		$this->values = array();
		$this->values[] = JHTML::_('select.option', '0', JText::_('ALL_LISTS'));
		foreach($allCats as $name => $lists){
			if($clickableCategories){
				$this->values[] = JHTML::_('select.option', implode(',', $lists).',', $name);
			}else{
				$this->values[] = JHTML::_('select.option', '<OPTGROUP>', $name);
			}

			foreach($lists as $listId){
				$this->values[] = JHTML::_('select.option', $listId, (count($allCats) > 1 ? ' - - ' : '').$this->data[$listId]->name);
			}

			if(!$clickableCategories) $msgType[] = JHTML::_('select.option', '</OPTGROUP>');
		}
		return $this->values;
	}
}
