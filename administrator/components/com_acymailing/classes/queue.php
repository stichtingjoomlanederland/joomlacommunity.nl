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

class queueClass extends acymailingClass{

	var $onlynew = false;
	var $mindelay = 0;
	var $limit = 0;
	var $orderBy = '';
	var $emailtypes = array();

	function delete($filters){

		if(!empty($filters)){
			$query = 'DELETE a.* FROM '.acymailing_table('queue').' as a';
			$query .= ' JOIN '.acymailing_table('subscriber').' as b on a.subid = b.subid';
			$query .= ' JOIN '.acymailing_table('mail').' as c on a.mailid = c.mailid';
			$query .= ' WHERE ('.implode(') AND (', $filters).')';
		}else{
			$this->database->setQuery('SELECT COUNT(*) FROM #__acymailing_queue');
			$nbRecords = $this->database->loadResult();

			$query = 'TRUNCATE TABLE '.acymailing_table('queue');
		}
		$this->database->setQuery($query);
		$this->database->query();
		if(empty($nbRecords)) $nbRecords = $this->database->getAffectedRows();

		return $nbRecords;
	}

	function nbQueue($mailid){
		$mailid = (int)$mailid;
		$this->database->setQuery('SELECT count(subid) FROM '.acymailing_table('queue').' WHERE mailid = '.$mailid.' GROUP BY mailid');
		return $this->database->loadResult();
	}

	function queue($mailid, $time){
		$mailid = intval($mailid);
		if(empty($mailid)) return false;

		$classLists = acymailing_get('class.listmail');
		$lists = $classLists->getReceivers($mailid, false);
		if(empty($lists)) return 0;

		JPluginHelper::importPlugin('acymailing');

		$mailClass = acymailing_get('class.mail');
		$mail = $mailClass->get($mailid);

		$filterClass = acymailing_get('class.filter');
		$queryClass = new acyQuery();
		if(!empty($mail->filter['type'])){
			$dispatcher = JDispatcher::getInstance();
			foreach($mail->filter['type'] as $num => $oneType){
				if(empty($oneType)) continue;
				$dispatcher->trigger('onAcyProcessFilter_'.$oneType, array(&$queryClass, $mail->filter[$num][$oneType], $num));
			}
		}

		$config = acymailing_config();

		$querySelect = 'SELECT DISTINCT a.subid,'.$mailid.','.$time.','.(int)$config->get('priority_newsletter', 3);
		$querySelect .= ' FROM '.acymailing_table('listsub').' as a ';
		$querySelect .= ' JOIN '.acymailing_table('subscriber').' as sub ON a.subid = sub.subid ';
		if(!empty($queryClass->join)) $querySelect .= ' JOIN '.implode(' JOIN ', $queryClass->join);
		if(!empty($queryClass->leftjoin)) $querySelect .= ' LEFT JOIN '.implode(' LEFT JOIN ', $queryClass->leftjoin);

		$querySelect .= ' WHERE sub.enabled = 1 AND sub.accept = 1 ';
		if(!empty($queryClass->where)) $querySelect .= ' AND ('.implode(') AND (', $queryClass->where).')';
		$querySelect .= ' AND a.listid IN ('.implode(',', array_keys($lists)).') AND a.status = 1 ';
		$config = acymailing_config();
		if($config->get('require_confirmation', '0')){
			$querySelect .= 'AND sub.confirmed = 1 ';
		}

		if(!empty($this->orderBy)){
			$querySelect .= ' ORDER BY '.$this->orderBy;
		}elseif(!empty($queryClass->orderBy)) $querySelect .= ' ORDER BY '.$queryClass->orderBy;

		if(!empty($this->limit)){
			$querySelect .= ' LIMIT '.$this->limit;
		}elseif(!empty($queryClass->limit)) $querySelect .= ' LIMIT '.$queryClass->limit;

		$query = 'INSERT IGNORE INTO '.acymailing_table('queue').' (subid,mailid,senddate,priority) '.$querySelect;

		$this->database->setQuery($query);
		if(!$this->database->query()){
			acymailing_display($this->database->getErrorMsg(), 'error');
		}
		$totalinserted = $this->database->getAffectedRows();

		if($this->onlynew){
			$this->database->setQuery('DELETE b.* FROM `#__acymailing_userstats` as a JOIN `#__acymailing_queue` as b on a.subid = b.subid WHERE a.mailid = '.$mailid);
			$this->database->query();
			$totalinserted = $totalinserted - $this->database->getAffectedRows();
		}

		if(!empty($this->mindelay)){
			$this->database->setQuery('DELETE b.* FROM `#__acymailing_userstats` as a JOIN `#__acymailing_queue` as b on a.subid = b.subid WHERE a.senddate > '.(time() - ($this->mindelay * 24 * 60 * 60)));
			$this->database->query();
			$totalinserted = $totalinserted - $this->database->getAffectedRows();
		}

		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onAcySendNewsletter', array($mailid));

		return $totalinserted;
	}

	public function getReady($limit, $mailid = 0){
		if(empty($limit)) return array();

		$config = acymailing_config();
		$order = $config->get('sendorder');
		if(empty($order)){
			$order = 'a.`subid` ASC';
		}else{
			if($order == 'rand'){
				$order = 'RAND()';
			}else{
				$ordering = explode(',', $order);
				$order = 'a.`'.acymailing_secureField(trim($ordering[0])).'` '.acymailing_secureField(trim($ordering[1]));
			}
		}

		$query = 'SELECT a.* FROM '.acymailing_table('queue').' as a';
		$query .= ' JOIN '.acymailing_table('mail').' as b on a.`mailid` = b.`mailid` ';
		$query .= ' WHERE a.`senddate` <= '.time().' AND b.`published` = 1';
		if(!empty($this->emailtypes)){
			foreach($this->emailtypes as &$oneType){
				$oneType = $this->database->quote($oneType);
			}
			$query .= ' AND (b.type = '.implode(' OR b.type = ', $this->emailtypes).')';
		}
		if(!empty($mailid)) $query .= ' AND a.`mailid` = '.$mailid;
		$query .= ' ORDER BY a.`priority` ASC, a.`senddate` ASC, '.$order;
		$query .= ' LIMIT '.JRequest::getInt('startqueue', 0).','.intval($limit);
		$this->database->setQuery($query);
		try{
			$results = $this->database->loadObjectList();
		}catch(Exception $e){
			$results = null;
		}

		if($results === null){
			$this->database->setQuery('REPAIR TABLE #__acymailing_queue, #__acymailing_subscriber, #__acymailing_mail');
			$this->database->query();
		}

		if(empty($results)) return array();

		if(!empty($results)){
			$firstElementQueued = reset($results);
			$this->database->setQuery('UPDATE #__acymailing_queue SET senddate = senddate + 1 WHERE mailid = '.$firstElementQueued->mailid.' AND subid = '.$firstElementQueued->subid.' LIMIT 1');
			$this->database->query();
		}

		$subids = array();
		foreach($results as $oneRes){
			$subids[$oneRes->subid] = intval($oneRes->subid);
		}

		$cleanQueue = false;
		if(!empty($subids)){
			$this->database->setQuery('SELECT * FROM #__acymailing_subscriber WHERE subid IN ('.implode(',', $subids).')');
			$allusers = $this->database->loadObjectList('subid');
			foreach($results as $oneId => $oneRes){
				if(empty($allusers[$oneRes->subid])){
					$cleanQueue = true;
					continue;
				}
				foreach($allusers[$oneRes->subid] as $oneVar => $oneVal){
					$results[$oneId]->$oneVar = $oneVal;
				}
			}
		}

		if($cleanQueue){
			$this->database->setQuery('DELETE a.* FROM #__acymailing_queue as a LEFT JOIN #__acymailing_subscriber as b ON a.subid = b.subid WHERE b.subid IS NULL');
			$this->database->query();
		}

		return $results;
	}


	function queueStatus($mailid, $all = false){
		$query = 'SELECT a.mailid, count(a.subid) as nbsub,min(a.senddate) as senddate, b.subject FROM '.acymailing_table('queue').' as a';
		$query .= ' JOIN '.acymailing_table('mail').' as b on a.mailid = b.mailid';
		$query .= ' WHERE b.published > 0';
		if(!$all){
			$query .= ' AND a.senddate < '.time();
			if(!empty($mailid)) $query .= ' AND a.mailid = '.$mailid;
		}
		$query .= ' GROUP BY a.mailid';
		$this->database->setQuery($query);
		$queueStatus = $this->database->loadObjectList('mailid');

		return $queueStatus;
	}

}
