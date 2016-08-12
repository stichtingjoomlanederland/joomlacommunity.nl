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

class urlType{

	function __construct(){
		$selectedMail = JRequest::getInt('filter_mail');
		if(!empty($selectedMail)){
			$query = 'SELECT DISTINCT a.name,a.urlid FROM '.acymailing_table('urlclick').' as b JOIN '.acymailing_table('url').' as a on a.urlid = b.urlid WHERE b.mailid = '.$selectedMail.' ORDER BY a.name LIMIT 300';
		}else{
			$query = 'SELECT DISTINCT a.name,a.urlid FROM '.acymailing_table('urlclick').' as b JOIN '.acymailing_table('url').' as a on a.urlid = b.urlid ORDER BY a.name LIMIT 300';
		}

		$db = JFactory::getDBO();
		$db->setQuery($query);
		$urls = $db->loadObjectList();

		$this->values = array();
		$this->values[] = JHTML::_('select.option', '0', JText::_('ALL_URLS') );
		foreach($urls as $onrUrl){
			if(strlen($onrUrl->name)>55) $onrUrl->name = substr($onrUrl->name,0,20).'...'.substr($onrUrl->name,-30);
			$this->values[] = JHTML::_('select.option', $onrUrl->urlid, $onrUrl->name );
		}
	}

	function display($map,$value){
		return JHTML::_('select.genericlist',   $this->values, $map, ' size="1" onchange="document.adminForm.submit( );" style="max-width:200px;"', 'value', 'text', (int) $value );
	}
}
