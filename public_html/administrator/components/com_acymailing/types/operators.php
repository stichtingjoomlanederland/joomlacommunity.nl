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

class operatorsType{
	var $extra = '';
	function __construct(){

		$this->values = array();

		$this->values[] = JHTML::_('select.option', '<OPTGROUP>',JText::_('ACY_NUMERIC'));
		$this->values[] = JHTML::_('select.option', '=','=');
		$this->values[] = JHTML::_('select.option', '!=','!=');
		$this->values[] = JHTML::_('select.option', '>','>');
		$this->values[] = JHTML::_('select.option', '<','<');
		$this->values[] = JHTML::_('select.option', '>=','>=');
		$this->values[] = JHTML::_('select.option', '<=','<=');
		$this->values[] = JHTML::_('select.option', '</OPTGROUP>');
		$this->values[] = JHTML::_('select.option', '<OPTGROUP>',JText::_('ACY_STRING'));
		$this->values[] = JHTML::_('select.option', 'BEGINS',JText::_('ACY_BEGINS_WITH'));
		$this->values[] = JHTML::_('select.option', 'END',JText::_('ACY_ENDS_WITH'));
		$this->values[] = JHTML::_('select.option', 'CONTAINS',JText::_('ACY_CONTAINS'));
		$this->values[] = JHTML::_('select.option', 'NOTCONTAINS',JText::_('ACY_NOT_CONTAINS'));
		$this->values[] = JHTML::_('select.option', 'LIKE','LIKE');
		$this->values[] = JHTML::_('select.option', 'NOT LIKE','NOT LIKE');
		$this->values[] = JHTML::_('select.option', 'REGEXP','REGEXP');
		$this->values[] = JHTML::_('select.option', 'NOT REGEXP','NOT REGEXP');
		$this->values[] = JHTML::_('select.option', '</OPTGROUP>');
		$this->values[] = JHTML::_('select.option', '<OPTGROUP>',JText::_('OTHER'));
		$this->values[] = JHTML::_('select.option', 'IS NULL','IS NULL');
		$this->values[] = JHTML::_('select.option', 'IS NOT NULL','IS NOT NULL');
		$this->values[] = JHTML::_('select.option', '</OPTGROUP>');

	}

	function display($map, $valueSelected = '', $otherClass = ''){
		return JHTML::_('select.genericlist', $this->values, $map, 'class="inputbox'. (!empty($otherClass)?' '.$otherClass:'') .'" size="1" style="width:120px;" '.$this->extra, 'value', 'text', $valueSelected);
	}

}
