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

class editorType{

	function __construct(){
		if(!ACYMAILING_J16){
			$query = 'SELECT DISTINCT element,name FROM '.acymailing_table('plugins',false).' WHERE folder=\'editors\' AND published=1 ORDER BY ordering ASC, name ASC';
 		}else{
			$query = 'SELECT DISTINCT element,name FROM '.acymailing_table('extensions',false).' WHERE folder=\'editors\' AND enabled=1 AND type=\'plugin\' ORDER BY ordering ASC, name ASC';
		}

		$db = JFactory::getDBO();
		$db->setQuery($query);
		$joomEditors = $db->loadObjectList();

		$this->values = array();
		$this->values[] = JHTML::_('select.option', '0',JText::_('ACY_DEFAULT'));
		if(!empty($joomEditors)){
			foreach($joomEditors as $myEditor){
				$this->values[] = JHTML::_('select.option', $myEditor->element,$myEditor->name);
			}
		}
	}

	function display($map,$value){
		return JHTML::_('select.genericlist', $this->values, $map , 'size="1"', 'value', 'text', $value);
	}

}
