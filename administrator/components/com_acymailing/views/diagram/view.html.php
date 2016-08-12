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


class DiagramViewDiagram extends acymailingView{
	var $ctrl = 'diagram';
	var $charttype = 'ColumnChart';
	var $interval = 'month';

	function display($tpl = null){

		$doc = JFactory::getDocument();
		$doc->addScript("https://www.google.com/jsapi");

		$function = $this->getLayout();
		if(method_exists($this, $function)) $this->$function();

		parent::display($tpl);
	}

	function listing(){

		$acyToolbar = acymailing::get('helper.toolbar');
		$acyToolbar->custom('export', JText::_('ACY_EXPORT'), 'export', false, '');
		$acyToolbar->directPrint();
		$acyToolbar->link(acymailing_completeLink('subscriber'), JText::_('ACY_CANCEL'), 'cancel');
		$acyToolbar->divider();
		$acyToolbar->help('charts');
		$acyToolbar->setTitle(JText::_('CHARTS'), 'diagram');
		$acyToolbar->display();

		$db = JFactory::getDBO();
		$where = array();


		$groupby = array();
		$groupingtype = array();
		$groupby[] = 'groupingdate';
		$groupby[] = 'groupingtype';
		$selectfield = 'sub.created';
		$listsneeded = false;

		$listClass = acymailing_get('class.list');
		$allLists = $listClass->getLists('listid');
		$this->assignRef('lists', $allLists);

		$display = JRequest::getVar('display', array());
		$this->assignRef('display', $display);
		foreach($display as $var => $val){
			$var = acymailing_securefield($var);
			$this->$var = acymailing_securefield($val);
		}

		if(empty($display)) return;


		$compares = JRequest::getVar('compares', array());
		$this->assignRef('compares', $compares);
		if(!empty($compares['lists'])){
			$groupingtype[] = 'list.name';
			$listsneeded = true;
			$selectfield = 'listsub.subdate';
			$where[] = "list.type = 'list'";
			$filterLists = JRequest::getVar('filterlists', array());
			JArrayHelper::toInteger($filterLists);
			if(!empty($filterLists)){
				$where[] = "listsub.listid IN (".implode(',', $filterLists).")";
			}
			$this->assignRef('filterlists', $filterLists);
		}

		if($this->interval == 'day'){
			$groupingdate = "DATE_FORMAT(FROM_UNIXTIME(".$selectfield."),'%Y-%m-%d')";
			$this->dateformat = '%d %B %Y';
		}elseif($this->interval == 'month'){
			$groupingdate = "DATE_FORMAT(FROM_UNIXTIME(".$selectfield."),'%Y-%m-01')";
			$this->dateformat = '%B %Y';
		}elseif($this->interval == 'year'){
			$groupingdate = "DATE_FORMAT(FROM_UNIXTIME(".$selectfield."),'%Y-01-01')";
			$this->dateformat = '%Y';
		}else{
			return;
		}

		if(!empty($compares['years'])){
			$groupingtype[] = "DATE_FORMAT(FROM_UNIXTIME(".$selectfield."),'%Y')";
			$this->dateformat = str_replace('%Y', '', $this->dateformat);
			$groupingdate = str_replace('%Y', '2000', $groupingdate);
		}

		$fieldtotal = 'COUNT(sub.subid)';
		$fieldtype = empty($groupingtype) ? "'Total'" : "CONCAT('Total - ',".implode(", ' - ' ,", $groupingtype).")";

		if(!empty($this->sumup)){
			$min = empty($this->datemin) ? 0 : acymailing_getTime($this->datemin);
			$max = empty($this->datemax) ? time() : acymailing_getTime($this->datemax);

			if(empty($min)){
				$db->setQuery('SELECT min(created) FROM #__acymailing_subscriber WHERE created > 0 LIMIT 1');
				$min = $db->loadResult();
			}

			$this->results = array();
			$maxInter = $min;
			$nbqueries = 0;
			while($maxInter < $max){
				$nbqueries++;
				if($nbqueries > 100){
					if($nbqueries == 101) acymailing_display('There are too many requests, please reduce the date range or change the interval');
					continue;
				}
				$previous = $maxInter;
				if($this->interval == 'day'){
					$maxInter = mktime(0, 0, 0, date("n", $maxInter), date("j", $maxInter) + 1, date("Y", $maxInter));
				}elseif($this->interval == 'month'){
					$maxInter = mktime(0, 0, 0, date("n", $maxInter) + 1, 1, date("Y", $maxInter));
				}elseif($this->interval == 'year'){
					$maxInter = mktime(0, 0, 0, 1, 1, date("Y", $maxInter) + 1);
				}

				$whereCond = array();
				if($listsneeded){
					$whereCond[] = 'listsub.status != 2';
					$whereCond[] = 'listsub.subdate > 0';
					$whereCond[] = 'listsub.subdate < '.$maxInter;
					$whereCond[] = '(listsub.status = 1 OR listsub.unsubdate >'.$maxInter.')';
					$query = "SELECT COUNT(listsub.subid) as total, ".$fieldtype." as groupingtype, '".acymailing_getDate($previous + 43200, '%Y-%m-%d')."' as groupingdate";
					$query .= " FROM #__acymailing_listsub as listsub ";
					$query .= " JOIN #__acymailing_list as list ON listsub.listid = list.listid ";
					$query .= " WHERE (".implode(") AND (", array_merge($where, $whereCond)).")";
					$query .= " GROUP BY listsub.listid";
				}else{
					$whereCond[] = 'sub.created < '.$maxInter;
					$whereCond[] = 'sub.created > 0';
					$query = "SELECT COUNT(sub.subid) as total, ".$fieldtype." as groupingtype, '".acymailing_getDate($previous + 43200, '%Y-%m-%d')."' as groupingdate";
					$query .= " FROM #__acymailing_subscriber as sub";
					$query .= " WHERE (".implode(") AND (", array_merge($where, $whereCond)).")";
				}
				$db->setQuery($query);
				$this->results = array_merge($this->results, $db->loadObjectList());
			}
		}else{
			if(!empty($this->datemin)){
				$where[] = $groupingdate.' >= '.$db->Quote($this->datemin);
			}

			if(!empty($this->datemax)){
				$where[] = $groupingdate.' < '.$db->Quote($this->datemax);
			}

			$where[] = 'sub.created > 0';

			$query = "SELECT ".$fieldtotal." as total, ".$fieldtype." as groupingtype, ".$groupingdate." as groupingdate";
			if($listsneeded){
				$query .= " FROM #__acymailing_listsub as listsub ";
				$query .= " JOIN #__acymailing_subscriber as sub ON listsub.subid = sub.subid ";
				$query .= " JOIN #__acymailing_list as list ON listsub.listid = list.listid ";
			}else{
				$query .= " FROM #__acymailing_subscriber as sub ";
			}

			if(!empty($where)) $query .= " WHERE (".implode(") AND (", $where).")";
			if(!empty($groupby)) $query .= " GROUP BY ".implode(',', $groupby);
			$query .= " ORDER BY groupingdate ASC";

			$db->setQuery($query);
			$this->results = $db->loadObjectList();
		}

		if(empty($this->results)) acymailing_enqueueMessage(JText::_('NO_SUBSCRIBER'), 'notice');

		return $this->displayResult();
	}

	function displayResult(){
		if(empty($this->results)) return;
		?>
		<script language="JavaScript" type="text/javascript">
			function drawChart(){
				var dataTable = new google.visualization.DataTable();
				dataTable.addColumn('string');
				<?php
				$dates = array();
				$types = array();
				$allVals = array();
				$i = 0;
				$a = 1;
				foreach($this->results as $oneResult){
					if(!isset($dates[$oneResult->groupingdate])){
						$dates[$oneResult->groupingdate] = $i;
						$i++;
						echo "dataTable.addRows(1);"."\n";
						echo "dataTable.setValue(".$dates[$oneResult->groupingdate].", 0, '".acymailing_getDate($oneResult->groupingdate, $this->dateformat)."');";
					}
					if(!isset($types[$oneResult->groupingtype])){
						$types[$oneResult->groupingtype] = $a;
						echo "dataTable.addColumn('number','".str_replace("'", "\'", $oneResult->groupingtype)."');"."\n";
						$a++;
					}
					echo "dataTable.setValue(".$dates[$oneResult->groupingdate].", ".$types[$oneResult->groupingtype].", ".$oneResult->total.");";
					$allVals[$oneResult->groupingdate][$oneResult->groupingtype] = $oneResult->total;
				}
				if(count($dates) < 2) $this->charttype = 'ColumnChart';

				$export = array();
				$export[] = JText::_('FIELD_DATE').','.implode(',', array_keys($types));
				foreach($dates as $oneDate => $datenum){
					$exportLine = array();
					foreach($types as $oneType => $typenum){
						if(!isset($allVals[$oneDate][$oneType])){
							echo "dataTable.setValue(".$datenum.", ".$typenum.",0);";
						}
						$exportLine[] = intval(@$allVals[$oneDate][$oneType]);
					}
					$export[] = strftime($this->dateformat, strtotime($oneDate)).','.implode(',', $exportLine);
				}
				$this->assignRef('export', $export);
				?>

				var vis = new google.visualization.<?php echo $this->charttype; ?>(document.getElementById('chart'));
				var options = {
					height: 500, legend: 'right', is3D: true, title: '<?php echo JText::_('USERS', true)?>', legendTextStyle: {color: '#333333'}
				};
				vis.draw(dataTable, options);
			}

			google.load("visualization", "1", {packages: ["corechart"]});
			google.setOnLoadCallback(drawChart);

			function exportData(){
				if(document.getElementById('exporteddata').style.display == 'none'){
					document.getElementById('exporteddata').style.display = '';
				}else{
					document.getElementById('exporteddata').style.display = 'none';
				}
			}

		</script>
		<?php

		if(JRequest::getCmd('task') == 'export'){
			$config = acymailing_config();
			$encodingClass = acymailing_get('helper.encoding');

			$exportHelper = acymailing_get('helper.export');
			$exportHelper->addHeaders('acymailingexport');

			$eol = "\r\n";
			$before = '"';
			$separator = '"'.str_replace(array('semicolon', 'comma'), array(';', ','), $config->get('export_separator', ';')).'"';
			$exportFormat = $config->get('export_format', 'UTF-8');
			$after = '"';

			for($i = 0, $a = count($export); $i < $a; $i++){
				echo $before.$encodingClass->change(str_replace(',', $separator, $export[$i]), 'UTF-8', $exportFormat).$after.$eol;
			}

			exit;
		}
	}

	function mailing(){

		$doc = JFactory::getDocument();
		$doc->addStyleSheet(ACYMAILING_CSS.'frontendedition.css?v='.filemtime(ACYMAILING_MEDIA.'css'.DS.'frontendedition.css'));
		$doc->addStyleSheet(ACYMAILING_CSS.'acyprint.css', 'text/css', 'print');

		$mailid = JRequest::getInt('mailid');
		if(empty($mailid)) return;


		$db = JFactory::getDBO();
		$db->setQuery('SELECT * FROM '.acymailing_table('stats').' WHERE mailid = '.intval($mailid));
		$mailingstats = $db->loadObject();

		if(empty($mailingstats->mailid)) return;

		$db->setQuery('SELECT COUNT(browser) as nbBrowser, browser FROM '.acymailing_table('userstats').' WHERE browser IS NOT NULL AND mailid = '.intval($mailid).' GROUP BY browser ORDER BY nbBrowser DESC');
		$browserstats = $db->loadObjectList('browser');
		$db->setQuery('SELECT COUNT(*) as nbMobile, is_mobile FROM '.acymailing_table('userstats').' WHERE is_mobile IS NOT NULL AND mailid = '.intval($mailid).' GROUP BY is_mobile');
		$ismobilestats = $db->loadObjectList('is_mobile');
		$db->setQuery('SELECT COUNT(mobile_os) as nbOS, mobile_os FROM '.acymailing_table('userstats').' WHERE mobile_os IS NOT NULL AND mobile_os <> \'\' AND mailid = '.intval($mailid).' GROUP BY mobile_os ORDER BY nbOS DESC');
		$mobileosstats = $db->loadObjectList('mobile_os');

		$mailClass = acymailing_get('class.mail');
		$mailing = $mailClass->get($mailid);

		acymailing_setPageTitle($mailing->subject);

		$db->setQuery('SELECT COUNT(*) FROM `#__acymailing_queue` WHERE `mailid` = '.$mailingstats->mailid.' GROUP BY `mailid`');
		$mailingstats->queue = $db->loadResult();


		$db->setQuery('SELECT min(opendate) as minval, max(opendate) as maxval FROM '.acymailing_table('userstats').' WHERE opendate > 0 AND mailid = '.intval($mailid));
		$datesOpen = $db->loadObject();
		$db->setQuery('SELECT min(`date`) as minval, max(`date`) as maxval FROM '.acymailing_table('urlclick').' WHERE  mailid = '.intval($mailid));
		$datesClick = $db->loadObject();
		$spaces = array();
		$intervals = 10;
		$minDate = min($datesOpen->minval, $datesClick->minval);
		if(empty($minDate)) $minDate = max($datesOpen->minval, $datesClick->minval);
		$maxDate = max($datesOpen->maxval, $datesClick->maxval) + 1;

		$delay = ($maxDate - $minDate) / $intervals;

		for($i = 0; $i < $intervals; $i++){
			$spaces[$i] = (int)($minDate + $delay * $i);
		}
		$spaces[$intervals] = $maxDate;

		$openclick = new stdClass();
		$openclick->open = array();
		$openclick->click = array();
		$openclick->legend = array();
		$dateFormat = '%d %B %Y';

		if(date('Y', $maxDate) == date('Y', $minDate)){
			$dateFormat = '%d %B';
			if(date('m', $maxDate) == date('m', $minDate)){
				$dateFormat = '%A %d';
				if($delay < 172800){
					$dateFormat = '%a %d %H:%M';
				}
			}
		}

		$app = JFactory::getApplication();
		if($app->isAdmin()){
			$acyToolbar = acymailing::get('helper.toolbar');
			$acyToolbar->directPrint();
			$acyToolbar->setTitle(JText::_('DETAILED_STATISTICS'));
			$acyToolbar->topfixed = false;
			$acyToolbar->display();
		}

		for($i = 0; $i <= $intervals; $i++){
			if($i % 2 == 0) $openclick->legend[$i] = acymailing_getDate($spaces[$i], $dateFormat);
			$db->setQuery('SELECT count(subid) FROM '.acymailing_table('userstats').' WHERE opendate < '.$spaces[$i].' AND opendate > 0 AND mailid = '.intval($mailid));
			$openclick->open[$i] = (int)$db->loadResult();
			$db->setQuery('SELECT count(subid) FROM '.acymailing_table('urlclick').' WHERE date < '.$spaces[$i].' AND mailid = '.intval($mailid));
			$openclick->click[$i] = (int)$db->loadResult();
		}

		$joomConfig = JFactory::getConfig();
		$timeoffset = ACYMAILING_J30 ? $joomConfig->get('offset') : $joomConfig->getValue('config.offset');
		$diffTime = $timeoffset - date('Z');
		$groupingFormat = '%Y %j';
		$phpformat = '%d %B';
		$diff = 86400;
		if($delay < 3600){
			$groupingFormat = '%Y %j %H';
			$phpformat = '%a %d %H';
			$diff = 3600;
		}
		$query = "SELECT DATE_FORMAT(FROM_UNIXTIME(a.opendate + $diffTime),'$groupingFormat') AS openday, a.opendate, COUNT(a.subid) AS totalopen ";
		$query .= 'FROM #__acymailing_userstats AS a WHERE opendate > 0 AND mailid = '.intval($mailid);
		$query .= ' GROUP BY openday ORDER BY openday DESC LIMIT 10';

		$db->setQuery($query);
		$datesOpen = $db->loadObjectList('openday');

		$query = "SELECT DATE_FORMAT(FROM_UNIXTIME(a.date + $diffTime),'$groupingFormat') AS clickday, a.date, COUNT(a.subid) AS totalclick ";
		$query .= 'FROM #__acymailing_urlclick AS a WHERE mailid = '.intval($mailid);
		$query .= ' GROUP BY clickday ORDER BY clickday DESC LIMIT 10';

		$db->setQuery($query);
		$datesClick = $db->loadObjectList('clickday');

		$openclickday = array();
		foreach($datesOpen as $time => $oneDate){
			$openclickday[$time] = array();
			$openclickday[$time]['date'] = acymailing_getDate($oneDate->opendate, $phpformat);
			$openclickday[$time]['nextdate'] = acymailing_getDate($oneDate->opendate - $diff, $phpformat);
			$openclickday[$time]['open'] = $oneDate;
		}
		foreach($datesClick as $time => $oneDate){
			if(!isset($openclickday[$time])){
				$openclickday[$time] = array();
				$openclickday[$time]['date'] = acymailing_getDate($oneDate->date, $phpformat);
				$openclickday[$time]['nextdate'] = acymailing_getDate($oneDate->date - $diff, $phpformat);
			}
			$openclickday[$time]['click'] = $oneDate;
		}

		krsort($openclickday);

		$query = 'SELECT c.*, COUNT(a.click) as uniqueclick, SUM(a.click) as totalclick  FROM #__acymailing_urlclick as a';
		$query .= ' JOIN '.acymailing_table('url').' as c on a.urlid = c.urlid';
		$query .= ' WHERE a.mailid = '.intval($mailid).' GROUP BY a.urlid ORDER BY uniqueclick DESC LIMIT 5';
		$db->setQuery($query);
		$mailinglinks = $db->loadObjectList();

		$query = 'SELECT opendate FROM #__acymailing_userstats WHERE mailid='.intval($mailid).' AND opendate!=0';
		$db->setQuery($query);
		$openDay = acymailing_loadResultArray($db);

		$openDayData = array();
		for($i = 0; $i < 56; $i++){
			$openDayData[$i] = 0;
		}
		foreach($openDay as $oneDate){
			$span = (intval(acymailing_getDate($oneDate, 'N')) - 1) * 8 + intval(acymailing_getDate($oneDate, 'H') / 3);
			$openDayData[$span] += 1;
		}
		$openDayLabel = array('0h-3h', '3h-6h', '6h-9h', '9h-12h', '12h-15h', '15h-18h', '18h-21h', '21h-24h', '0h-3h', '3h-6h', '6h-9h', '9h-12h', '12h-15h', '15h-18h', '18h-21h', '21h-24h', '0h-3h', '3h-6h', '6h-9h', '9h-12h', '12h-15h', '15h-18h', '18h-21h', '21h-24h', '0h-3h', '3h-6h', '6h-9h', '9h-12h', '12h-15h', '15h-18h', '18h-21h', '21h-24h', '0h-3h', '3h-6h', '6h-9h', '9h-12h', '12h-15h', '15h-18h', '18h-21h', '21h-24h', '0h-3h', '3h-6h', '6h-9h', '9h-12h', '12h-15h', '15h-18h', '18h-21h', '21h-24h', '0h-3h', '3h-6h', '6h-9h', '9h-12h', '12h-15h', '15h-18h', '18h-21h', '21h-24h');
		$openDayAnnotation = array(JText::_('ACY_MONDAY'), '', '', '', '', '', '', '', JText::_('ACY_TUESDAY'), '', '', '', '', '', '', '', JText::_('ACY_WEDNESDAY'), '', '', '', '', '', '', '', JText::_('ACY_THURSDAY'), '', '', '', '', '', '', '', JText::_('ACY_FRIDAY'), '', '', '', '', '', '', '', JText::_('ACY_SATURDAY'), '', '', '', '', '', '', '', JText::_('ACY_SUNDAY'), '', '', '', '', '', '', '');

		$this->assignRef('app', $app);
		$this->assignRef('mailinglinks', $mailinglinks);
		$this->assignRef('mailing', $mailing);
		$this->assignRef('mailingstats', $mailingstats);
		$this->assignRef('openclick', $openclick);
		$this->assignRef('openclickday', $openclickday);
		$this->assignRef('ctrl', $this->ctrl);
		$this->assignRef('config', acymailing_config());
		$this->assign('browserstats', $browserstats);
		$this->assign('ismobilestats', $ismobilestats);
		$this->assign('mobileosstats', $mobileosstats);
		$this->assign('opendaydata', $openDayData);
		$this->assign('opendaylabel', $openDayLabel);
		$this->assign('opendayannotation', $openDayAnnotation);
		$this->setLayout('mailing');
	}
}
