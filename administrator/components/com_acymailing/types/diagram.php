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

class diagramType{
	function __construct(){

		$this->values = array();
		$this->values[] = JHTML::_('select.option', 'lists', JText::_('NB_SUB_UNSUB') );
		$this->values[] = JHTML::_('select.option', 'subscription', JText::_('SUB_HISTORY') );
	}

	function display($map,$value){
		return JHTML::_('select.genericlist',   $this->values, $map, 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text',$value);
	}
}
