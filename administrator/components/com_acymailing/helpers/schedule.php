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

class acyscheduleHelper{

	var $nbNewsletterScheduled = 0;

	function getScheduled(){
		$db = JFactory::getDBO();
		$db->setQuery('SELECT * FROM '.acymailing_table('mail').' WHERE published = 2 ORDER BY senddate ASC');
		return $db->loadObjectList();
	}

	function getReadyMail(){
		$db = JFactory::getDBO();
		$db->setQuery('SELECT mailid,senddate,subject,params FROM '.acymailing_table('mail').' WHERE published = 2 AND senddate <= '.(time()+1200).' ORDER BY senddate ASC');
		return $db->loadObjectList('mailid');
	}

	function queueScheduled(){

		$this->messages = array();
		$mailReady = $this->getReadyMail();
		if(empty($mailReady)){
			$this->messages[] = JText::_('NO_SCHED');
			return false;
		}

		$this->nbNewsletterScheduled = count($mailReady);

		$queueClass = acymailing_get('class.queue');
		foreach($mailReady as $mailid => $mail){
			$params = unserialize($mail->params);
			$queueClass->onlynew = !empty($params['onlynew']);

			$nbQueue = $queueClass->queue($mailid,$mail->senddate);
			$this->messages[] = JText::sprintf('ADDED_QUEUE_SCHEDULE',$nbQueue,$mailid,'<b><i>'.$mail->subject.'</i></b>');
		}

		$db = JFactory::getDBO();
		$db->setQuery('UPDATE '.acymailing_table('mail').' SET published = 1 WHERE mailid IN ('.implode(',',array_keys($mailReady)).')');
		$db->query();

		return true;
	}

}//endclass
