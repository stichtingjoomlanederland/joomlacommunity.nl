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

class deliverstatusType{

	function __construct(){

		$this->values = array();
		$this->values[] = JHTML::_('select.option', '0', JText::_('ALL_STATUS') );
		$this->values[] = JHTML::_('select.option', 'open', JText::_('OPEN') );
		$this->values[] = JHTML::_('select.option', 'notopen', JText::_('NOT_OPEN') );
		$this->values[] = JHTML::_('select.option', 'failed', JText::_('FAILED') );
		if(acymailing_level(3)) $this->values[] = JHTML::_('select.option', 'bounce', JText::_('BOUNCES') );

	}

	function display($map,$value){
		return JHTML::_('select.genericlist',   $this->values, $map, 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', $value );
	}
}
