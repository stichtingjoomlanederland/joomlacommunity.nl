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

class operatorsinType{
	var $js = '';
	function __construct(){

		$this->values = array();

		$this->values[] = JHTML::_('select.option', 'IN',JText::_('ACY_IN'));
		$this->values[] = JHTML::_('select.option', 'NOT IN',JText::_('ACY_NOT_IN'));

	}

	function display($map){
		return JHTML::_('select.genericlist', $this->values, $map, 'class="inputbox" size="1" style="width:120px;" '.$this->js, 'value', 'text');
	}

}
