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

class StatsController extends acymailingController{

	var $aclCat = 'statistics';

	function detaillisting(){
		if(!$this->isAllowed('statistics','manage')) return;
		JRequest::setVar( 'layout', 'detaillisting'  );
		return parent::display();
	}

	function unsubscribed(){
		if(!$this->isAllowed('statistics','manage')) return;
		JRequest::setVar( 'layout', 'unsubscribed'  );
		return parent::display();
	}

	function forward(){
		if(!$this->isAllowed('statistics','manage')) return;
		JRequest::setVar( 'layout', 'forward'  );
		return parent::display();
	}

	function unsubchart(){
		if(!$this->isAllowed('statistics','manage')) return;
		JRequest::setVar( 'layout', 'unsubchart'  );
		return parent::display();
	}

	function mailinglist(){
		if(!$this->isAllowed('statistics','manage')) return;
		JRequest::setVar( 'layout', 'mailinglist'  );
		return parent::display();
	}

	function remove(){
		if(!$this->isAllowed('statistics','delete')) return;
		JRequest::checkToken() or die( 'Invalid Token' );

		$cids = JRequest::getVar( 'cid', array(), '', 'array' );

		$class = acymailing_get('class.stats');
		$num = $class->delete($cids);

		acymailing_enqueueMessage(JText::sprintf('SUCC_DELETE_ELEMENTS',$num), 'message');

		return $this->listing();
	}

	function export(){
		$selectedMail = JRequest::getInt('filter_mail',0);
		$selectedStatus = JRequest::getString('filter_status','');
		$selectedBounce = JRequest::getString('filter_bounce','');

		$db = JFactory::getDBO();

		$filters = array();
		if(!empty($selectedMail)) $filters[] = 'userstats.mailid = '.$selectedMail;
		if(!empty($selectedStatus)){
			if($selectedStatus == 'bounce') $filters[] = 'userstats.bounce > 0';
			elseif($selectedStatus == 'open') $filters[] = 'userstats.open > 0';
			elseif($selectedStatus == 'notopen') $filters[] = 'userstats.open < 1';
			elseif($selectedStatus == 'failed') $filters[] = 'userstats.fail > 0';
		}
		if(!empty($selectedStatus) && $selectedStatus == 'bounce' && !empty($selectedBounce)) $filters[] = "userstats.bouncerule = ".$db->Quote($selectedBounce);

		$query = 'FROM `#__acymailing_userstats` as userstats JOIN `#__acymailing_subscriber` as s ON s.subid = userstats.subid';
		if(!empty($filters)) $query .= ' WHERE ('.implode(') AND (',$filters).')';

		$currentSession = JFactory::getSession();
		$currentSession->set('acyexportquery',$query);

		$app = JFactory::getApplication();
		$tmplVar = JRequest::getString('tmpl','') == 'component' ? '&tmpl=component' : '';
		$app->redirect(acymailing_completeLink(($app->isAdmin() ? '' : 'front').'data&task=export&sessionquery=1'.$tmplVar,false,true));
	}

	public function exportUnsubscribed(){
		return $this->exportData('unsubscribed');
	}


	public function exportForward(){
		return $this->exportData('forward');
	}

	private function exportData($action){
		$selectedMail = JRequest::getInt('filter_mail',0);
		$filters = array();
		$db = JFactory::getDBO();
		$filters[] = "hist.action = ".$db->Quote($action);
		if(!empty($selectedMail)) $filters[] = 'hist.mailid = '.intval($selectedMail);

		$query = 'FROM #__acymailing_history as hist JOIN #__acymailing_mail as b on hist.mailid = b.mailid JOIN #__acymailing_subscriber as s on hist.subid = s.subid';
		if(!empty($filters)) $query .= ' WHERE ('.implode(') AND (',$filters).')';

		$currentSession = JFactory::getSession();
		$currentSession->set('acyexportquery',$query);
		$this->setRedirect(acymailing_completeLink('data&task=export&sessionquery=1',true,true));
	}

	function exportglobal(){
		$nlCondition = '';
		$cids = JRequest::getVar('cid');
		JArrayHelper::toInteger($cids);
		if(!empty($cids)) $nlCondition = ' WHERE a.mailid IN (' . implode(', ', $cids) . ') ';

		$db = JFactory::getDBO();
		$query = 'SELECT b.subject, a.senddate, a.* , a.bouncedetails FROM #__acymailing_stats as a JOIN #__acymailing_mail as b on a.mailid = b.mailid '. $nlCondition . ' ORDER BY a.senddate desc';
		$db->setQuery($query);
		$mydata = $db->loadObjectList();

		$exportHelper = acymailing_get('helper.export');
		$config = acymailing_config();
		$encodingClass = acymailing_get('helper.encoding');
		$exportHelper->addHeaders('globalStatistics_' . date('m_d_y'));

		$eol= "\r\n";
		$before = '"';
		$separator = '"'.str_replace(array('semicolon','comma'),array(';',','), $config->get('export_separator',';')).'"';
		$exportFormat = $config->get('export_format','UTF-8');
		$after = '"';

		$forwardEnabled = $config->get('forward', 0);
		$titles = array(JText::_( 'JOOMEXT_SUBJECT'), JText::_( 'SEND_DATE' ), JText::_( 'OPEN_UNIQUE' ), JText::_('OPEN_TOTAL'), JText::_('OPEN').' (%)');
		if(acymailing_level(1)) array_push($titles, JTEXT::_('UNIQUE_HITS'), JTEXT::_('TOTAL_HITS'), JText::_( 'CLICKED_LINK' ).' (%)');
		array_push($titles, JText::_( 'UNSUBSCRIBE' ), JText::_( 'UNSUBSCRIBE' ).' (%)');
		if(acymailing_level(1) && $forwardEnabled == 1) array_push($titles, JText::_( 'FORWARDED' ));
		array_push($titles, JText::_( 'SENT_HTML' ), JText::_( 'SENT_TEXT' ));
		if(acymailing_level(3))  array_push($titles,JText::_( 'BOUNCES' ), JText::_( 'BOUNCES' ).' (%)');
		array_push($titles, JText::_( 'FAILED' ), JText::_( 'ACY_ID' ));

		$titleLine = $before.implode($separator, $titles).$after.$eol;
		echo $titleLine;

		foreach($mydata as $nl){
			$line = $nl->subject . $separator;
			$line.= acymailing_getDate($nl->senddate) . $separator;
			$line.= $nl->openunique . $separator;
			$line.= $nl->opentotal . $separator;
			$cleanSent = $nl->senthtml + $nl->senttext;
			if(acymailing_level(3)) $cleanSent = $cleanSent - $nl->bounceunique;
			$prct = (!empty($cleanSent)? round($nl->openunique/$cleanSent*100,2):'-');
			$line.= $prct . '%' . $separator;
			if(acymailing_level(1)){
				$line.= $nl->clickunique . $separator;
				$line.= $nl->clicktotal . $separator;
				$prct = (!empty($cleanSent)? round($nl->clickunique/$cleanSent*100,2):'-');
				$line.= $prct . '%' . $separator;
			}
			$line.= $nl->unsub . $separator;
			$prct = (!empty($cleanSent)? round($nl->unsub/$cleanSent*100,2):'-');
			$line.= $prct . '%' . $separator;
			if(acymailing_level(1) && $forwardEnabled == 1){
				$line.= $nl->forward . $separator;
			}
			$line.= $nl->senthtml . $separator;
			$line.= $nl->senttext . $separator;
			if(acymailing_level(3)){
				$line.= $nl->bounceunique . $separator;
				$prct = (!empty($nl->senthtml)? round($nl->bounceunique/($nl->senthtml+$nl->senttext)*100,2):'-');
				$line.= $prct . '%' . $separator;
			}
			$line.= $nl->fail . $separator;
			$line.= $nl->mailid;

			$line = $before.$encodingClass->change($line, 'UTF-8', $exportFormat).$after.$eol;
			echo $line;
		}
		exit;
	}
}
