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

class statusquickType{
	function __construct(){
		$this->values = array();
		$this->values[] = JHTML::_('select.option', '0', JText::_('JOOMEXT_RESET') );
		$this->values[] = JHTML::_('select.option', '1', JText::_('SUBSCRIBE_ALL') );

		$js = "function updateStatus(statusval){".
			'var i=0;'.
			"while(window.document.getElementById('status'+i+statusval)){";
		if(ACYMAILING_J30){
			$js .= 'jQuery("label[for=status"+i+statusval+"]").click();';
		}
		$js .= "window.document.getElementById('status'+i+statusval).checked = true;";
		$js .= 'i++;}'.
		'}';
		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration( $js );
	}

	function display($map){
		return JHTML::_('acyselect.radiolist', $this->values, $map , 'class="radiobox" size="1" onclick="updateStatus(this.value)"', 'value', 'text', '','status_all');
	}
}
