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

class urlmailType{

	function __construct(){
		$query = 'SELECT b.subject,b.mailid,count(distinct a.urlid) as totalmail FROM '.acymailing_table('urlclick').' as a';
		$query .= ' JOIN '.acymailing_table('mail').' as b ON a.mailid = b.mailid';
		$query .= ' GROUP BY a.mailid ORDER BY a.mailid DESC';
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$mails = $db->loadObjectList();

		$this->values = array();
		$this->values[] = JHTML::_('select.option', '0', JText::_('ALL_EMAILS') );
		foreach($mails as $oneMail){
			$this->values[] = JHTML::_('select.option', $oneMail->mailid, $oneMail->subject.' ( '.$oneMail->totalmail.' )' );
		}
	}

	function display($map,$value){
		return JHTML::_('select.genericlist',   $this->values, $map, ' size="1" onchange="document.adminForm.submit( );" style="max-width:200px;"', 'value', 'text', (int) $value );
	}
}
