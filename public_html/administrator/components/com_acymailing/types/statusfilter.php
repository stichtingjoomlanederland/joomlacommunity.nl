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

class statusfilterType{
	function __construct(){
		$this->values = array();
		$this->values[] = JHTML::_('select.option', '0', JText::_('ALL_STATUS') );
		$this->values[] = JHTML::_('select.option',  '<OPTGROUP>', JText::_( 'ACCEPT_REFUSE' ) );
		$this->values[] = JHTML::_('select.option', '1', JText::_('ACCEPT_EMAIL') );
		$this->values[] = JHTML::_('select.option', '-1', JText::_('REFUSE_EMAIL') );
		$this->values[] = JHTML::_('select.option',  '</OPTGROUP>');
		$config = acymailing_config();
		if($config->get('require_confirmation',0)){
			$this->values[] = JHTML::_('select.option',  '<OPTGROUP>', JText::_( 'SUBSCRIPTION' ) );
			$this->values[] = JHTML::_('select.option', '2', JText::_('PENDING_SUBSCRIPTION') );
			$this->values[] = JHTML::_('select.option',  '</OPTGROUP>');
		}
		$this->values[] = JHTML::_('select.option',  '<OPTGROUP>', JText::_( 'ENABLED_DISABLED' ) );
		$this->values[] = JHTML::_('select.option', '3', JText::_('ENABLED') );
		$this->values[] = JHTML::_('select.option', '-3', JText::_('DISABLED') );
		$this->values[] = JHTML::_('select.option',  '</OPTGROUP>');
	}

	function display($map,$value){
		return JHTML::_('select.genericlist',   $this->values, $map, 'size="1" onchange="document.adminForm.limitstart.value=0;document.adminForm.submit( );"', 'value', 'text', (int) $value );
	}
}
