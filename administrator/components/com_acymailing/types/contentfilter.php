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

class contentfilterType{
	var $onclick = 'updateTag();';
	function __construct(){
	}

	function display($map,$value,$label = true,$modified = true){
		$prefix = $label ? '|filter:' : '';
		$this->values = array();
		$this->values[] = JHTML::_('select.option', "",JText::_('ACY_ALL'));
		$this->values[] = JHTML::_('select.option', $prefix."created",JText::_('ONLY_NEW_CREATED'));
		if($modified) $this->values[] = JHTML::_('select.option', $prefix."modify",JText::_('ONLY_NEW_MODIFIED'));
		return JHTML::_('select.genericlist', $this->values, $map , 'size="1" onchange="'.$this->onclick.'" style="max-width:200px;"', 'value', 'text', (string) $value);
	}
}
