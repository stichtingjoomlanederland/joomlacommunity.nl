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


class StatsViewStats extends acymailingView{

	var $searchFields = array('b.subject', 'b.alias', 'a.mailid');
	var $selectFields = array('b.subject', 'b.alias', 'b.type', 'a.*', 'a.bouncedetails');
	var $searchHistory = array('b.subject', 'c.email', 'c.name');
	var $historyFields = array('a.*', 'b.subject', 'c.email', 'c.name');
	var $detailSearchFields = array('b.subject', 'b.alias', 'a.mailid', 'c.name', 'c.email', 'a.subid');
	var $detailSelectFields = array('b.subject', 'b.alias', 'c.name', 'c.email', 'b.type', 'a.ip', 'a.*');


	function display($tpl = null){
		$function = $this->getLayout();
		if(method_exists($this, $function)) $this->$function();

		parent::display($tpl);
	}

	function unsubchart(){
		$mailid = JRequest::getInt('mailid');
		if(empty($mailid)) return;

		$doc = JFactory::getDocument();
		$doc->addStyleSheet(ACYMAILING_CSS.'acyprint.css?v='.filemtime(ACYMAILING_MEDIA.'css'.DS.'acyprint.css'), 'text/css', 'print');

		$db = JFactory::getDBO();
		$db->setQuery('SELECT * FROM #__acymailing_history WHERE mailid = '.intval($mailid).' AND action="unsubscribed" LIMIT 10000');
		$entries = $db->loadObjectList();

		if(empty($entries)){
			acymailing_display("No data recorded for that Newsletter", 'warning');
			return;
		}

		$acyToolbar = acymailing::get('helper.toolbar');
		$acyToolbar->link(acymailing_completeLink('stats&task=unsubchart&export=1&mailid='.JRequest::getInt('mailid'), true), JText::_('ACY_EXPORT'), 'export');
		$acyToolbar->directPrint();
		$acyToolbar->setTitle(JText::_('ACTION_UNSUBSCRIBED'));
		$acyToolbar->display();

		$unsubreasons = array();
		$unsubreasons['NO_REASON'] = 0;
		foreach($entries as $oneEntry){
			if(empty($oneEntry->data)){
				$unsubreasons['NO_REASON']++;
				continue;
			}

			$allReasons = explode("\n", $oneEntry->data);
			$added = false;
			foreach($allReasons as $oneReason){
				list($reason, $value) = explode('::', $oneReason);
				if(empty($value) || $reason != 'REASON') continue;
				$unsubreasons[$value] = @$unsubreasons[$value] + 1;
				$added = true;
			}
			if(!$added) $unsubreasons['NO_REASON']++;
		}

		$finalReasons = array();
		foreach($unsubreasons as $oneReason => $total){
			$name = $oneReason;
			if(preg_match('#^[A-Z_]*$#', $name)) $name = JText::_($name);
			$finalReasons[$name] = $total;
		}

		arsort($finalReasons);

		$doc = JFactory::getDocument();
		$doc->addScript("https://www.google.com/jsapi");

		$this->assignRef('unsubreasons', $finalReasons);

		if(JRequest::getCmd('export')){
			$exportHelper = acymailing_get('helper.export');
			$exportHelper->exportOneData($finalReasons, 'unsub_'.JRequest::getInt('mailid'));
		}
	}

	function forward(){
		$this->unsubscribed();
		$this->setLayout('unsubscribed');
	}

	function unsubscribed(){
		$app = JFactory::getApplication();

		$pageInfo = new stdClass();
		$pageInfo->filter = new stdClass();
		$pageInfo->filter->order = new stdClass();
		$pageInfo->limit = new stdClass();
		$pageInfo->elements = new stdClass();

		$paramBase = ACYMAILING_COMPONENT.'.'.$this->getName().$this->getLayout();
		$pageInfo->filter->order->value = $app->getUserStateFromRequest($paramBase.".filter_order", 'filter_order', 'a.date', 'cmd');
		$pageInfo->filter->order->dir = $app->getUserStateFromRequest($paramBase.".filter_order_Dir", 'filter_order_Dir', 'desc', 'word');
		if(strtolower($pageInfo->filter->order->dir) !== 'desc') $pageInfo->filter->order->dir = 'asc';
		$pageInfo->search = $app->getUserStateFromRequest($paramBase.".search", 'search', '', 'string');
		$pageInfo->search = JString::strtolower(trim($pageInfo->search));
		$selectedMail = $app->getUserStateFromRequest($paramBase."filter_mail", 'filter_mail', 0, 'int');
		$pageInfo->limit->value = $app->getUserStateFromRequest($paramBase.'.list_limit', 'limit', $app->getCfg('list_limit'), 'int');
		$pageInfo->limit->start = JRequest::getInt('start', $app->getUserStateFromRequest($paramBase.'.limitstart', 'limitstart', 0, 'int'));

		$db = JFactory::getDBO();

		$filters = array();
		$filters[] = "a.action = ".$db->Quote($this->getLayout());

		if(!empty($pageInfo->search)){
			$searchVal = '\'%'.acymailing_getEscaped($pageInfo->search).'%\'';
			$filters[] = implode(" LIKE $searchVal OR ", $this->searchHistory)." LIKE $searchVal";
		}

		if(!empty($selectedMail)){
			$filters[] = 'a.mailid = '.$selectedMail;
		}

		$query = 'SELECT '.implode(' , ', $this->historyFields).' FROM '.acymailing_table('history').' as a';
		$query .= ' JOIN '.acymailing_table('mail').' as b on a.mailid = b.mailid';
		$query .= ' JOIN '.acymailing_table('subscriber').' as c on a.subid = c.subid';
		$query .= ' WHERE ('.implode(') AND (', $filters).')';
		if(!empty($pageInfo->filter->order->value)) $query .= ' ORDER BY '.$pageInfo->filter->order->value.' '.$pageInfo->filter->order->dir;

		if(empty($pageInfo->limit->value)) $pageInfo->limit->value = 100;

		$db->setQuery($query, $pageInfo->limit->start, $pageInfo->limit->value);
		$rows = $db->loadObjectList();

		$queryCount = 'SELECT COUNT(*) FROM #__acymailing_history as a';
		if(!empty($pageInfo->search)){
			$queryCount .= ' JOIN '.acymailing_table('mail').' as b on a.mailid = b.mailid';
			$queryCount .= ' JOIN '.acymailing_table('subscriber').' as c on a.subid = c.subid';
		}
		$queryCount .= ' WHERE ('.implode(') AND (', $filters).')';
		$db->setQuery($queryCount);
		$pageInfo->elements->total = $db->loadResult();

		$pageInfo->elements->page = count($rows);

		jimport('joomla.html.pagination');
		$pagination = new JPagination($pageInfo->elements->total, $pageInfo->limit->start, $pageInfo->limit->value);

		$query = 'SELECT DISTINCT a.mailid FROM `#__acymailing_history` as a WHERE a.action = '.$db->Quote($this->getLayout()).' AND a.mailid > 0';
		$db->setQuery($query);
		$allMailids = acymailing_loadResultArray($db);

		$emails = array();
		if(!empty($allMailids)){
			if(!empty($selectedMail) && !in_array($selectedMail, $allMailids)) array_unshift($allMailids, $selectedMail);
			$query = 'SELECT subject, mailid FROM `#__acymailing_mail` WHERE mailid IN ('.implode(',', $allMailids).') ORDER BY mailid DESC';
			$db->setQuery($query);
			$emails = $db->loadObjectList();
		}


		$newsletters = array();
		$newsletters[] = JHTML::_('select.option', '0', JText::_('ALL_EMAILS'));
		foreach($emails as $oneMail){
			$newsletters[] = JHTML::_('select.option', $oneMail->mailid, $oneMail->subject);
		}
		$filterMail = JHTML::_('select.genericlist', $newsletters, 'filter_mail', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', (int)$selectedMail);

		if($app->isAdmin() && JRequest::getString('tmpl') == 'component'){
			$acyToolbar = acymailing::get('helper.toolbar');
			if(!empty($rows)) $acyToolbar->custom('export'.ucfirst(JRequest::getCmd('task')), JText::_('ACY_EXPORT'), 'export', false, '');
			$acyToolbar->custom('', JText::_('ACY_CANCEL'), 'cancel', false, 'location.href=\''.acymailing_completeLink('diagram&task=mailing&mailid='.JRequest::getInt('filter_mail'), true).'\';');
			$acyToolbar->setTitle(JText::_('UNSUBSCRIBECAPTION'));
			$acyToolbar->topfixed = false;
			$acyToolbar->display();
		}

		$this->assign('app', $app);
		$this->assignRef('filterMail', $filterMail);
		$this->assignRef('rows', $rows);
		$this->assignRef('pageInfo', $pageInfo);
		$this->assignRef('pagination', $pagination);
	}


	function detaillisting(){
		$app = JFactory::getApplication();

		$pageInfo = new stdClass();
		$pageInfo->filter = new stdClass();
		$pageInfo->filter->order = new stdClass();
		$pageInfo->limit = new stdClass();
		$pageInfo->elements = new stdClass();
		$config = acymailing_config();

		$paramBase = ACYMAILING_COMPONENT.'.'.$this->getName().$this->getLayout();
		$pageInfo->filter->order->value = $app->getUserStateFromRequest($paramBase.".filter_order", 'filter_order', 'a.senddate', 'cmd');
		$pageInfo->filter->order->dir = $app->getUserStateFromRequest($paramBase.".filter_order_Dir", 'filter_order_Dir', 'desc', 'word');
		if(strtolower($pageInfo->filter->order->dir) !== 'desc') $pageInfo->filter->order->dir = 'asc';
		$pageInfo->search = $app->getUserStateFromRequest($paramBase.".search", 'search', '', 'string');
		$pageInfo->search = JString::strtolower(trim($pageInfo->search));
		$selectedMail = $app->getUserStateFromRequest($paramBase."filter_mail", 'filter_mail', 0, 'int');
		$selectedStatus = $app->getUserStateFromRequest($paramBase."filter_status", 'filter_status', 0, 'string');
		$selectedBounce = $app->getUserStateFromRequest($paramBase."filter_bounce", 'filter_bounce', 0, 'string');

		$pageInfo->limit->value = $app->getUserStateFromRequest($paramBase.'.list_limit', 'limit', $app->getCfg('list_limit'), 'int');
		$pageInfo->limit->start = $app->getUserStateFromRequest($paramBase.'.limitstart', 'limitstart', 0, 'int');

		$database = JFactory::getDBO();

		$filters = array();
		if(!empty($pageInfo->search)){
			$searchVal = '\'%'.acymailing_getEscaped($pageInfo->search).'%\'';
			$filters[] = implode(" LIKE $searchVal OR ", $this->detailSearchFields)." LIKE $searchVal";
		}

		if(!empty($selectedMail)) $filters[] = 'a.mailid = '.$selectedMail;
		if(!empty($selectedStatus)){
			if($selectedStatus == 'bounce'){
				$filters[] = 'a.bounce > 0';
			}elseif($selectedStatus == 'open') $filters[] = 'a.open > 0';
			elseif($selectedStatus == 'notopen') $filters[] = 'a.open < 1';
			elseif($selectedStatus == 'failed') $filters[] = 'a.fail > 0';
		}
		if(!empty($selectedStatus) && $selectedStatus == 'bounce' && !empty($selectedBounce)) $filters[] = 'a.bouncerule='.$database->Quote($selectedBounce);

		$query = 'SELECT '.implode(' , ', $this->detailSelectFields);
		$query .= ' FROM '.acymailing_table('userstats').' as a';
		$query .= ' JOIN '.acymailing_table('mail').' as b on a.mailid = b.mailid';
		$query .= ' JOIN '.acymailing_table('subscriber').' as c on a.subid = c.subid';
		if(!empty($filters)) $query .= ' WHERE ('.implode(') AND (', $filters).')';
		if(!empty($pageInfo->filter->order->value)) $query .= ' ORDER BY '.$pageInfo->filter->order->value.' '.$pageInfo->filter->order->dir;

		if(empty($pageInfo->limit->value)) $pageInfo->limit->value = 100;

		$database->setQuery($query, $pageInfo->limit->start, $pageInfo->limit->value);
		$rows = $database->loadObjectList();

		if($rows === null){
			acymailing_display(substr(strip_tags($database->getErrorMsg()), 0, 200).'...', 'error');
			if(file_exists(ACYMAILING_BACK.'install.acymailing.php')){
				include_once(ACYMAILING_BACK.'install.acymailing.php');
				$installClass = new acymailingInstall();
				$installClass->fromVersion = '3.7.0';
				$installClass->update = true;
				$installClass->updateSQL();
			}
		}

		$queryCount = 'SELECT COUNT(a.subid) FROM #__acymailing_userstats as a';
		if(!empty($pageInfo->search)){
			$queryCount .= ' JOIN '.acymailing_table('mail').' as b on a.mailid = b.mailid';
			$queryCount .= ' JOIN '.acymailing_table('subscriber').' as c on a.subid = c.subid';
		}
		if(!empty($filters)) $queryCount .= ' WHERE ('.implode(') AND (', $filters).')';
		$database->setQuery($queryCount);
		$pageInfo->elements->total = $database->loadResult();

		$pageInfo->elements->page = count($rows);

		jimport('joomla.html.pagination');
		$pagination = new JPagination($pageInfo->elements->total, $pageInfo->limit->start, $pageInfo->limit->value);

		$toggleClass = acymailing_get('helper.toggle');

		$maildetailstatstype = acymailing_get('type.detailstatsmail');
		$deliverstatus = acymailing_get('type.deliverstatus');
		$filtersType = new stdClass();
		if(JRequest::getString('tmpl') == 'component'){
			$filtersType->mail = '<input type="hidden" value="'.$selectedMail.'" name="filter_mail" />';
			$mailClass = acymailing_get('class.mail');
			$this->assign('mailing', $mailClass->get($selectedMail));
		}else{
			$filtersType->mail = $maildetailstatstype->display('filter_mail', $selectedMail);
		}
		$filtersType->status = $deliverstatus->display('filter_status', $selectedStatus);

		$detailstatsbouncetype = acymailing_get('type.detailstatsbounce');
		if(!empty($selectedStatus) && $selectedStatus == 'bounce'){
			$filtersType->bounce = $detailstatsbouncetype->display('filter_bounce', $selectedBounce);
		}else $filtersType->bounce = '';

		if($app->isAdmin()){
			$acyToolbar = acymailing::get('helper.toolbar');
			if(JRequest::getString('tmpl') == 'component'){
				if(acymailing_isAllowed($config->get('acl_subscriber_export', 'all'))) $acyToolbar->custom('export', JText::_('ACY_EXPORT'), 'export', false);
				$acyToolbar->custom('', JText::_('ACY_CANCEL'), 'cancel', false, 'location.href=\''.acymailing_completeLink('diagram&task=mailing&mailid='.JRequest::getInt('filter_mail'), true).'\';');
				$acyToolbar->setTitle(JText::_('DETAILED_STATISTICS'));
				$acyToolbar->topfixed = false;
			}else{
				if(acymailing_isAllowed($config->get('acl_subscriber_export', 'all'))){
					$acyToolbar->custom('export', JText::_('ACY_EXPORT'), 'export', false);
				}
				$acyToolbar->link(acymailing_completeLink('stats'), JText::_('GLOBAL_STATISTICS'), 'cancel');
				$acyToolbar->divider();
				$acyToolbar->help('statistics');
				$acyToolbar->setTitle(JText::_('DETAILED_STATISTICS'), 'stats&task=detaillisting');
			}
			$acyToolbar->display();
		}

		$this->assignRef('filters', $filtersType);
		$this->assignRef('toggleClass', $toggleClass);
		$this->assignRef('rows', $rows);
		$this->assignRef('pageInfo', $pageInfo);
		$this->assignRef('pagination', $pagination);
	}

	function listing(){
		$app = JFactory::getApplication();
		$pageInfo = new stdClass();
		$pageInfo->filter = new stdClass();
		$pageInfo->filter->order = new stdClass();
		$pageInfo->limit = new stdClass();
		$pageInfo->elements = new stdClass();
		$config = acymailing_config();

		JHTML::_('behavior.modal', 'a.modal');

		$paramBase = ACYMAILING_COMPONENT.'.'.$this->getName().$this->getLayout();
		$pageInfo->filter->order->value = $app->getUserStateFromRequest($paramBase.".filter_order", 'filter_order', 'a.senddate', 'cmd');
		$pageInfo->filter->order->dir = $app->getUserStateFromRequest($paramBase.".filter_order_Dir", 'filter_order_Dir', 'desc', 'word');
		if(strtolower($pageInfo->filter->order->dir) !== 'desc') $pageInfo->filter->order->dir = 'asc';
		$pageInfo->search = $app->getUserStateFromRequest($paramBase.".search", 'search', '', 'string');
		$pageInfo->search = JString::strtolower(trim($pageInfo->search));

		$pageInfo->limit->value = $app->getUserStateFromRequest($paramBase.'.list_limit', 'limit', $app->getCfg('list_limit'), 'int');
		$pageInfo->limit->start = $app->getUserStateFromRequest($paramBase.'.limitstart', 'limitstart', 0, 'int');

		$database = JFactory::getDBO();

		$filters = array();
		if(!empty($pageInfo->search)){
			$searchVal = '\'%'.acymailing_getEscaped($pageInfo->search, true).'%\'';
			$filters[] = implode(" LIKE $searchVal OR ", $this->searchFields)." LIKE $searchVal";
		}

		$listClass = acymailing_get('class.list');
		$lists = $listClass->getLists();
		$msgType = array();
		$msgType[] = JHTML::_('select.option', '0', JText::_('ALL_EMAILS'));
		$msgType[] = JHTML::_('select.option', '<OPTGROUP>', JText::_('NEWSLETTER'));
		$msgType[] = JHTML::_('select.option', 'news', JText::_('ALL_LISTS'));
		foreach($lists as $oneList){
			$msgType[] = JHTML::_('select.option', 'list_'.$oneList->listid, $oneList->name);
		}
		$msgType[] = JHTML::_('select.option', '</OPTGROUP>');
		$msgType[] = JHTML::_('select.option', 'notification', JText::_('NOTIFICATIONS'));
		if(acymailing_level(1)){
			$msgType[] = JHTML::_('select.option', 'autonews', JText::_('AUTONEW'));
			$msgType[] = JHTML::_('select.option', 'joomlanotification', JText::_('JOOMLA_NOTIFICATIONS'));
		}
		if(acymailing_level(3)){
			$listCampaign = acymailing_get('class.list');
			$listCampaign->type = 'campaign';
			$campaigns = $listCampaign->getLists();
			$msgType[] = JHTML::_('select.option', '<OPTGROUP>', JText::_('FOLLOWUP'));
			$msgType[] = JHTML::_('select.option', 'followup', JText::_('ACY_ALL_CAMPAIGNS'));
			foreach($campaigns as $oneCamp){
				$msgType[] = JHTML::_('select.option', 'camp_'.$oneCamp->listid, $oneCamp->name);
			}
			$msgType[] = JHTML::_('select.option', '</OPTGROUP>');
		}
		$msgType[] = JHTML::_('select.option', 'welcome', JText::_('MSG_WELCOME'));
		$msgType[] = JHTML::_('select.option', 'unsub', JText::_('MSG_UNSUB'));
		if(acymailing_level(3)){
			$msgType[] = JHTML::_('select.option', 'action', JText::_('ACY_DISTRIBUTION'));
		}
		$selectedMsgType = $app->getUserStateFromRequest($paramBase."filter_msg", 'filter_msg', 0, 'string');
		$msgTypeChoice = JHTML::_('select.genericlist', $msgType, "filter_msg", 'class="inputbox" style="max-width: 200px;" onchange="document.adminForm.limitstart.value=0;document.adminForm.submit( );"', 'value', 'text', $selectedMsgType);
		$extraJoin = '';
		if(!empty($selectedMsgType)){
			$subfilter = substr($selectedMsgType, 0, 5);
			if($subfilter == 'camp_' || $subfilter == 'list_'){
				$filters[] = " b.type = '".($subfilter == 'camp_' ? 'followup' : 'news')."'";
				$filters[] = " lm.listid = ".substr($selectedMsgType, 5);
				$extraJoin = " JOIN #__acymailing_listmail AS lm ON a.mailid = lm.mailid";
			}else{
				$filters[] = " b.type = '".$selectedMsgType."'";
			}
		}

		$query = 'SELECT '.implode(' , ', $this->selectFields);
		$query .= ', CASE WHEN (a.senthtml+a.senttext) <= a.bounceunique THEN 0 ELSE (a.openunique/(a.senthtml+a.senttext-a.bounceunique)) END AS openprct';
		$query .= ', CASE WHEN (a.senthtml+a.senttext) <= a.bounceunique THEN 0 ELSE (a.clickunique/(a.senthtml+a.senttext-a.bounceunique)) END AS clickprct';
		$query .= ', CASE WHEN a.openunique = 0 THEN 0 ELSE (a.clickunique/a.openunique) END AS efficiencyprct';
		$query .= ', CASE WHEN (a.senthtml+a.senttext) <= a.bounceunique THEN 0 ELSE (a.unsub/(a.senthtml+a.senttext-a.bounceunique)) END AS unsubprct';
		$query .= ', (a.senthtml+a.senttext) as totalsent';
		$query .= ', CASE WHEN (a.senthtml+a.senttext) = 0 THEN 0 ELSE (a.bounceunique/(a.senthtml+a.senttext)) END AS bounceprct';
		$query .= ' FROM '.acymailing_table('stats').' as a';
		$query .= ' JOIN '.acymailing_table('mail').' as b on a.mailid = b.mailid';
		if(!empty($extraJoin)) $query .= $extraJoin;
		if(!empty($filters)) $query .= ' WHERE ('.implode(') AND (', $filters).')';
		if(!empty($pageInfo->filter->order->value)){
			$query .= ' ORDER BY '.$pageInfo->filter->order->value.' '.$pageInfo->filter->order->dir;
		}

		$database->setQuery($query, $pageInfo->limit->start, $pageInfo->limit->value);
		$rows = $database->loadObjectList();

		if($rows === null){
			acymailing_display(substr(strip_tags($database->getErrorMsg()), 0, 200).'...', 'error');
			if(file_exists(ACYMAILING_BACK.'install.acymailing.php')){
				include_once(ACYMAILING_BACK.'install.acymailing.php');
				$installClass = new acymailingInstall();
				$installClass->fromVersion = '3.6.0';
				$installClass->update = true;
				$installClass->updateSQL();
			}
		}

		$queryCount = 'SELECT COUNT(a.mailid) FROM '.acymailing_table('stats').' as a';
		if(!empty($pageInfo->search) || !empty($filters)){
			$queryCount .= ' JOIN '.acymailing_table('mail').' as b on a.mailid = b.mailid';
			if(!empty($extraJoin)) $queryCount .= $extraJoin;
		}
		if(!empty($filters)) $queryCount .= ' WHERE ('.implode(') AND (', $filters).')';

		$database->setQuery($queryCount);
		$pageInfo->elements->total = $database->loadResult();

		$pageInfo->elements->page = count($rows);

		jimport('joomla.html.pagination');
		$pagination = new JPagination($pageInfo->elements->total, $pageInfo->limit->start, $pageInfo->limit->value);


		$acyToolbar = acymailing::get('helper.toolbar');
		$acyToolbar->custom('exportglobal', JText::_('ACY_EXPORT'), 'export', false);
		if(acymailing_isAllowed($config->get('acl_statistics_delete', 'all'))) $acyToolbar->delete();
		$acyToolbar->divider();
		$acyToolbar->help('statistics');
		$acyToolbar->setTitle(JText::_('GLOBAL_STATISTICS'), 'stats');
		$acyToolbar->display();

		$this->assignRef('rows', $rows);
		$this->assignRef('pageInfo', $pageInfo);
		$this->assignRef('pagination', $pagination);
		$this->assign('filterMsg', $msgTypeChoice);
	}

	function mailinglist($export = 0){
		$mailid = JRequest::getInt('mailid');
		if(empty($mailid)) return;

		$doc = JFactory::getDocument();
		$doc->addStyleSheet(ACYMAILING_CSS.'acyprint.css?v='.filemtime(ACYMAILING_MEDIA.'css'.DS.'acyprint.css'), 'text/css', 'print');

		$mailClass = acymailing_get('class.mail');
		$mailing = $mailClass->get($mailid);

		$mydata = array();
		$isData = true;

		$db = JFactory::getDBO();

		if($mailing->type == 'followup'){
			$query = 'SELECT l.listid, l.name, l.color FROM #__acymailing_list l';
			$query .= ' JOIN #__acymailing_listcampaign lc ON l.listid = lc.listid';
			$query .= ' JOIN #__acymailing_listmail lm ON lc.campaignid = lm.listid';
			$query .= ' WHERE lm.mailid = '.intval($mailid).' ORDER BY l.ordering';
			$db->setQuery($query);
			$sqlRes = $db->loadObjectList();
		}else{
			$query = 'SELECT lm.listid, l.name, l.color FROM #__acymailing_list l';
			$query .= ' JOIN #__acymailing_listmail lm ON l.listid=lm.listid';
			$query .= ' WHERE lm.mailid='.intval($mailid).' ORDER BY l.ordering';
			$db->setQuery($query);
			$sqlRes = $db->loadObjectList();
		}

		if(empty($sqlRes)){
			$query = 'SELECT listid, name, color FROM #__acymailing_list';
			$query .= ' WHERE welmailid='.intval($mailid).' OR unsubmailid='.intval($mailid).' GROUP BY listid';
			$db->setQuery($query);
			$sqlRes = $db->loadObjectList();
			if(empty($sqlRes)){
				acymailing_display("This newsletter is not assigned to any list", 'warning');
				$isData = false;
				return;
			}
		}

		$arrayColors = array();
		$arrayList = array();
		foreach($sqlRes as $list){
			$mydata[$list->listid] = array();
			$mydata[$list->listid]['listid'] = $list->listid;
			$mydata[$list->listid]['listname'] = $list->name;
			$mydata[$list->listid]['nbMailSent'] = 0;
			$mydata[$list->listid]['nbHtml'] = 0;
			$mydata[$list->listid]['nbOpen'] = 0;
			$mydata[$list->listid]['nbOpenRatio'] = 0;
			$mydata[$list->listid]['nbClic'] = 0;
			$mydata[$list->listid]['nbClicRatio'] = 0;
			$mydata[$list->listid]['nbForward'] = 0;
			$mydata[$list->listid]['nbBounce'] = 0;
			$mydata[$list->listid]['nbBounceRatio'] = 0;
			$mydata[$list->listid]['nbUnsub'] = 0;
			$mydata[$list->listid]['nbUnsubRatio'] = 0;

			$mydata[$list->listid]['color'] = (!empty($list->color) ? $list->color : '#162955');
			array_push($arrayColors, (!empty($list->color) ? $list->color : '#162955'));
			array_push($arrayList, $list->listid);
		}
		$listColors = "'".implode("', '", $arrayColors)."'";
		$listListes = implode(',', $arrayList);

		$query = 'SELECT ls.listid, COUNT(*) as nbSent, SUM(IF(html=1, 1, 0)) as nbHtml, SUM(IF(open<>0, 1, 0)) as nbOpen, SUM(IF(bounce<>0, 1, 0)) as nbBounce ';
		$query .= ' FROM #__acymailing_userstats us JOIN #__acymailing_listsub ls ON us.subid = ls.subid';
		$query .= ' WHERE ls.listid IN ('.$listListes.') AND us.mailid='.intval($mailid).' GROUP BY ls.listid';
		$db->setQuery($query);
		$sqlRes = $db->loadObjectList();
		$totalSent = 0;
		if(!empty($sqlRes)){
			foreach($sqlRes as $lineRes){
				$mydata[$lineRes->listid]['nbMailSent'] = $lineRes->nbSent;
				$mydata[$lineRes->listid]['nbHtml'] = $lineRes->nbHtml;
				$mydata[$lineRes->listid]['nbOpen'] = $lineRes->nbOpen;
				$mydata[$lineRes->listid]['nbOpenRatio'] = number_format($lineRes->nbOpen / $mydata[$lineRes->listid]['nbHtml'] * 100, 1);
				$mydata[$lineRes->listid]['nbBounce'] = $lineRes->nbBounce;
				$mydata[$lineRes->listid]['nbBounceRatio'] = number_format($lineRes->nbBounce / $mydata[$lineRes->listid]['nbMailSent'] * 100, 1);
				$totalSent += $lineRes->nbSent;
			}
		}else{
			acymailing_display("No statistics recorded", 'warning');
			$isData = false;
			return;
		}

		$query = 'SELECT ls.listid, COUNT(DISTINCT(uc.subid)) AS nbClic FROM #__acymailing_urlclick as uc JOIN #__acymailing_listsub as ls ON uc.subid=ls.subid';
		$query .= ' WHERE ls.listid IN ('.$listListes.') AND uc.mailid='.intval($mailid).' GROUP BY ls.listid';
		$db->setQuery($query);
		$sqlRes = $db->loadObjectList();
		if(!empty($sqlRes)){
			foreach($sqlRes as $lineRes){
				$mydata[$lineRes->listid]['nbClic'] = $lineRes->nbClic;
				$mydata[$lineRes->listid]['nbClicRatio'] = number_format($lineRes->nbClic / $mydata[$lineRes->listid]['nbHtml'] * 100, 1);
			}
		}

		$query = 'SELECT ls.listid, SUM(IF(h.action=\'forward\', 1, 0)) as nbForward, SUM(IF(h.action=\'unsubscribed\', 1, 0)) as nbUnsub';
		$query .= ' FROM #__acymailing_history as h JOIN #__acymailing_listsub ls ON h.subid=ls.subid';
		$query .= ' WHERE ls.listid IN ('.$listListes.') AND h.mailid='.intval($mailid).' GROUP BY ls.listid';
		$db->setQuery($query);
		$sqlRes = $db->loadObjectList();
		if(!empty($sqlRes)){
			foreach($sqlRes as $lineRes){
				$mydata[$lineRes->listid]['nbForward'] = $lineRes->nbForward;
				$mydata[$lineRes->listid]['nbUnsub'] = $lineRes->nbUnsub;
				$mydata[$lineRes->listid]['nbUnsubRatio'] = number_format($lineRes->nbUnsub / $mydata[$lineRes->listid]['nbMailSent'] * 100, 1);
			}
		}

		$app = JFactory::getApplication();
		if($app->isAdmin() && JRequest::getString('tmpl') == 'component'){
			$acyToolbar = acymailing::get('helper.toolbar');
			$acyToolbar->custom('', JText::_('ACY_EXPORT'), 'export', false, 'location.href=\''.acymailing_completeLink('stats&task=mailinglist&export=1&mailid='.JRequest::getInt('mailid'), true).'\';');
			$acyToolbar->directPrint();
			$acyToolbar->setTitle($mailing->subject);
			$acyToolbar->topfixed = false;
			$acyToolbar->display();
		}
		$this->assignRef('app', $app);
		$this->assignRef('mydata', $mydata);
		$this->assignRef('mailing', $mailing);
		$this->assignRef('listColors', $listColors);
		$this->assignRef('isData', $isData);
		$this->assignRef('totalSent', $totalSent);

		if(JRequest::getCmd('export')){
			$exportHelper = acymailing_get('helper.export');
			$config = acymailing_config();
			$encodingClass = acymailing_get('helper.encoding');

			$exportHelper->addHeaders('mailingList_'.JRequest::getInt('mailid'));

			$eol = "\r\n";
			$before = '"';
			$separator = '"'.str_replace(array('semicolon', 'comma'), array(';', ','), $config->get('export_separator', ';')).'"';
			$exportFormat = $config->get('export_format', 'UTF-8');
			$after = '"';

			$titles = array(JText::_('LIST'), JText::_('LIST_NAME'), JText::_('ACY_SENT_EMAILS'), JText::_('SENT_HTML'), JText::_('OPEN'), JText::_('OPEN').' (%)', JText::_('CLICKED_LINK'), JText::_('CLICKED_LINK').' (%)', JText::_('FORWARDED'), JText::_('BOUNCES'), JText::_('BOUNCES').' (%)', JText::_('UNSUBSCRIBED'), JText::_('UNSUBSCRIBED').' (%)', JText::_('COLOUR'));
			$titleLine = $before.implode($separator, $titles).$after.$eol;
			echo $titleLine;

			foreach($mydata as $listid => $listDetails){
				$line = '';
				foreach($listDetails as $name => $value){
					$line .= $value.$separator;
				}
				$line = substr($line, 0, strlen($line) - strlen($separator));
				$line = $before.$encodingClass->change($line, 'UTF-8', $exportFormat).$after.$eol;
				echo $line;
			}
			exit;
		}
	}
}
