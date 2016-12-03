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

class statusfilterlistType{
	var $extra = '';
	function __construct(){
		$this->values = array();
		$this->values[] = JHTML::_('select.option', '1', JText::_('SUBSCRIBERS') );
		$this->values[] = JHTML::_('select.option', '2', JText::_('PENDING_SUBSCRIPTION') );
		$this->values[] = JHTML::_('select.option', '-1', JText::_('UNSUBSCRIBERS') );
		$this->values[] = JHTML::_('select.option', '-2', JText::_('NO_SUBSCRIPTION') );
	}

	function display($map,$value,$submit = true){
		$onChange = $submit ? 'onchange="document.adminForm.limitstart.value=0;document.adminForm.submit( );"' : '';
		return JHTML::_('select.genericlist',   $this->values, $map, 'class="inputbox" size="1" '.$onChange.' '.$this->extra, 'value', 'text', (int) $value );
	}
}
