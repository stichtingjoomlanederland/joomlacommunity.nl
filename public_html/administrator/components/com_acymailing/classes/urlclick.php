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

class urlclickClass extends acymailingClass{

	var $tables = array('urlclick');

	function addClick($urlid,$mailid,$subid){
		$mailid = intval($mailid);
		$urlid = intval($urlid);
		$subid = intval($subid);
		if(empty($mailid) OR empty($urlid) OR empty($subid)) return true;

		$statsClass = acymailing_get('class.stats');
		$statsClass->countReturn = false;
		$statsClass->mailid = $mailid;
		$statsClass->subid = $subid;
		if(!$statsClass->saveStats()) return true;

		$date = time();
		$ipClass = acymailing_get('helper.user');
		$ip = $ipClass->getIP();

		$query = 'INSERT IGNORE INTO '.acymailing_table('urlclick').' (urlid,mailid,subid,date,click,ip) VALUES ('.$urlid.','.$mailid.','.$subid.','.$date.',1,'.$this->database->Quote($ip).')';
		$this->database->setQuery($query);
		if(!$this->database->query()){
			acymailing_display($this->database->getErrorMsg(),'error');
			exit;
		}

		if(!$this->database->getAffectedRows()){
			$query = 'UPDATE '.acymailing_table('urlclick').' SET click = click +1,`date` = '.$date.' WHERE mailid = '.$mailid.' AND urlid = '.$urlid.' AND subid = '.$subid.' LIMIT 1';
			$this->database->setQuery($query);
			$this->database->query();
		}

		$query = 'SELECT SUM(click) FROM '.acymailing_table('urlclick').' WHERE mailid = '.$mailid.' AND subid = '.$subid;
		$this->database->setQuery($query);
		$totalUserClick = $this->database->loadResult();

		$query = 'UPDATE '.acymailing_table('stats').' SET clicktotal = clicktotal + 1 ';
		if($totalUserClick <= 1){
			$query .= ' , clickunique = clickunique + 1';
		}
		$query .= ' WHERE mailid = '.$mailid.' LIMIT 1';
		$this->database->setQuery($query);
		$this->database->query();

		$this->database->setQuery('UPDATE #__acymailing_subscriber SET lastclick_date = '.time().' WHERE subid = '.$subid);
		$this->database->query();

		$filterClass = acymailing_get('class.filter');
		$filterClass->subid = $subid;
		$filterClass->trigger('clickurl');

		$classGeoloc = acymailing_get('class.geolocation');
		$classGeoloc->saveGeolocation('clic', $subid);

		JPluginHelper::importPlugin('acymailing');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onAcyClickLink',array($subid,$mailid,$urlid));

		return true;
	}

}
