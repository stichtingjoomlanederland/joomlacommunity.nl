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

class generatemodeType{
	function __construct(){

		$this->values = array();
		$this->values[] = JHTML::_('select.option', 1,JText::_('AUTONEWS_SEND'));
		$this->values[] = JHTML::_('select.option', 0,JText::_('AUTONEWS_WAIT'));

	}

	function display($map,$value){
		return JHTML::_('select.genericlist',   $this->values, $map, 'class="inputbox" size="1" ', 'value', 'text',(int) $value );
	}

}
