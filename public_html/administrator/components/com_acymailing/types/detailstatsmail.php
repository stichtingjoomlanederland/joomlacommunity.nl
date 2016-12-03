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

class detailstatsmailType{
	function __construct(){

		$query = 'SELECT b.subject, a.mailid FROM '.acymailing_table('stats').' as a';
		$query .= ' JOIN '.acymailing_table('mail').' as b on a.mailid = b.mailid ORDER BY a.senddate DESC LIMIT 200';
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$emails = $db->loadObjectList();

		$this->values = array();
		$this->values[] = JHTML::_('select.option', '0', JText::_('ALL_EMAILS') );
		foreach($emails as $oneMail){
			$this->values[] = JHTML::_('select.option', $oneMail->mailid, $oneMail->subject );
		}
	}

	function display($map,$value){
		return JHTML::_('select.genericlist',   $this->values, $map, 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', (int) $value );
	}
}
