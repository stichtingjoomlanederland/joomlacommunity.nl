<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div id="acy_content">
	<?php $app = JFactory::getApplication();
	$adminPath = $app->isAdmin() ? '../' : JURI::base(true)."/";
	if(empty($this->mailingstats->mailid)) die('No statistics recorded yet'); ?>
	<script language="JavaScript" type="text/javascript">
		function drawChartSendProcess(){
			document.getElementById('sendprocess').style.display = 'block';
			document.getElementById('open').style.display = 'block';
			var dataTable = new google.visualization.DataTable();
			dataTable.addColumn('string');
			dataTable.addColumn('number');
			dataTable.addRows(3);

			dataTable.setValue(0, 0, '<?php echo intval($this->mailingstats->senthtml).' '.JText::_('SENT_HTML', true); ?>');
			dataTable.setValue(1, 0, '<?php echo intval($this->mailingstats->senttext).' '.JText::_('SENT_TEXT', true); ?>');
			dataTable.setValue(2, 0, '<?php echo intval($this->mailingstats->fail).' '.JText::_('FAILED', true); ?>');

			dataTable.setValue(0, 1, <?php echo intval($this->mailingstats->senthtml); ?>);
			dataTable.setValue(1, 1, <?php echo intval($this->mailingstats->senttext); ?>);
			dataTable.setValue(2, 1, <?php echo intval($this->mailingstats->fail); ?>);

			var vis = new google.visualization.PieChart(document.getElementById('sendprocess'));
			var options = {
				width: 360, height: 300, colors: ['#40A640', '#5F78B5', '#A42B37'], title: '<?php echo JText::_('SEND_PROCESS', true);?>', is3D: true, legendTextStyle: {color: '#333333'}
			};
			vis.draw(dataTable, options);
		}

		function drawChartOpen(){
			var vis = new google.visualization.PieChart(document.getElementById('open'));
			var options = {
				width: 360, height: 300, colors: ['#40A640', '#5F78B5'], title: '<?php echo JText::_('OPEN', true);?>', is3D: true, legendTextStyle: {color: '#333333'}
			};
			vis.draw(getDatadrawChartOpen(), options);
		}
		function getDatadrawChartOpen(){
			var dataTable = new google.visualization.DataTable();
			dataTable.addRows(2);

			dataTable.addColumn('string');
			dataTable.addColumn('number');
			dataTable.setValue(0, 0, '<?php echo intval($this->mailingstats->openunique).' '.JText::_('OPEN', true); ?>');
			dataTable.setValue(0, 1, <?php echo intval($this->mailingstats->openunique); ?>);
			<?php
			if(acymailing_level(3)){
				$bounceval = $this->mailingstats->bounceunique;
			}else{
				$bounceval = 0;
			}
			?>
			dataTable.setValue(1, 0, '<?php $notOpen = intval($this->mailingstats->senthtml - $this->mailingstats->openunique - $bounceval); echo $notOpen.' '.JText::_('NOT_OPEN', true); ?>');
			dataTable.setValue(1, 1, <?php echo $notOpen; ?>);

			return dataTable;
		}

		function getDatadrawChartOpenclick(){
			var dataTable = new google.visualization.DataTable();
			dataTable.addRows(<?php echo count($this->openclick->open); ?>);

			dataTable.addColumn('string');

			dataTable.addColumn('number', '<?php echo JText::_('OPEN', true); ?>');
			dataTable.addColumn('number', '<?php echo JText::_('CLICKED_LINK', true);?>');

			<?php
			foreach($this->openclick->open as $i => $oneResult){
				if(!empty($this->openclick->legend[$i])) echo "dataTable.setValue($i, 0, '".addslashes($this->openclick->legend[$i])."');";
				echo "dataTable.setValue($i, 1, $oneResult);";
				echo "dataTable.setValue($i,2,".$this->openclick->click[$i].");";
			}
			?>
			return dataTable;
		}
		function drawChartOpenclick(){


			var vis = new google.visualization.LineChart(document.getElementById('openclicktotal'));
			var options = {
				width: 730, height: 300, colors: ['#40A640', '#5F78B5'], legend: 'bottom', title: '<?php echo JText::_('ACY_STAT_OPEN_CLICK', true); ?>', legendTextStyle: {color: '#333333'}
			};
			vis.draw(getDatadrawChartOpenclick(), options);
		}

		function displayChart(chartname){
			if(chartname == 'all'){
				document.getElementById('chartopenratetable').style.display = 'block';
				document.getElementById('openclicktotal').style.display = 'block';
				<?php if(!empty($this->openclickday)){ ?>
				document.getElementById('openclickperday').style.display = 'block';
				<?php } ?>
				<?php if(!empty($this->opendaydata)){ ?>
				document.getElementById('opendaydata').style.display = 'block';
				<?php } ?>
				<?php if(!empty($this->browserstats)){ ?>
				document.getElementById('browser').style.display = 'block';
				<?php } ?>
				<?php if(!empty($this->ismobilestats) || !empty($this->mobileosstats)){ ?>
				document.getElementById('chartdevicetable').style.display = 'block';
				<?php } ?>

			}else{
				document.getElementById('chartopenratetable').style.display = 'none';
				document.getElementById('openclicktotal').style.display = 'none';
				document.getElementById('openclickperday').style.display = 'none';
				document.getElementById('opendaydata').style.display = 'none';
				document.getElementById('browser').style.display = 'none';
				document.getElementById('chartdevicetable').style.display = 'none';

				document.getElementById(chartname).style.display = 'block';
			}

		}

		function getDatadrawChartOpenclickperday(){
			var dataTable = new google.visualization.DataTable();
			dataTable.addRows(<?php echo min(10, count($this->openclickday)); ?>);

			dataTable.addColumn('string');

			dataTable.addColumn('number', '<?php echo JText::_('OPEN', true); ?>');
			dataTable.addColumn('number', '<?php echo JText::_('CLICKED_LINK', true);?>');

			<?php $i = min(10, count($this->openclickday)) - 1;
			$nextDate = '';
			foreach($this->openclickday as $oneResult){
				if(!empty($nextDate) AND $nextDate != $oneResult['date']){
					echo "dataTable.setValue($i, 0, '...'); ";
					if($i-- == 0) break;
				}
				echo "dataTable.setValue($i, 0, '".addslashes($oneResult['date'])."'); ";
				echo "dataTable.setValue($i, 1, ".intval(@$oneResult['open']->totalopen)."); ";
				echo "dataTable.setValue($i,2,".intval(@$oneResult['click']->totalclick)."); ";
				$nextDate = $oneResult['nextdate'];
				if($i-- == 0) break;
			}
			?>

			return dataTable;
		}

		function drawChartOpenclickperday(){
			var vis = new google.visualization.ColumnChart(document.getElementById('openclickperday'));
			var options = {
				width: 730, height: 300, colors: ['#40A640', '#5F78B5'], legend: 'bottom', title: '<?php echo JText::_('ACY_STAT_OPEN_CLICK_DAY', true); ?>', legendTextStyle: {color: '#333333'}
			};
			vis.draw(getDatadrawChartOpenclickperday(), options);

		}
		<?php if(!empty($this->browserstats)){ ?>
		function drawChartBrowser(){
			var vis = new google.visualization.PieChart(document.getElementById('browser'));
			var options = {
				width: 730, height: 300, title: '<?php echo JText::_('ACY_STAT_BROWSER', true);?>', is3D: true, legendTextStyle: {color: '#333333'}
			};
			vis.draw(getDatadrawChartBrowser(), options);
		}
		function getDatadrawChartBrowser(){
			var dataTable = new google.visualization.DataTable();
			dataTable.addRows(<?php echo count($this->browserstats);?>);

			dataTable.addColumn('string');
			dataTable.addColumn('number');
			var i = 0;
			<?php foreach($this->browserstats as $oneBrowser => $detailBrowser){ ?>
			dataTable.setValue(i, 0, '<?php echo intval($detailBrowser->nbBrowser).' '.$oneBrowser; ?>');
			dataTable.setValue(i, 1, <?php echo intval($detailBrowser->nbBrowser); ?>);
			i += 1;
			<?php } ?>

			return dataTable;
		}
		<?php } ?>
		<?php if(!empty($this->ismobilestats)){ ?>
		function drawChartIsMobile(){
			var vis = new google.visualization.PieChart(document.getElementById('device'));
			var options = {
				width: 360, height: 300, colors: ['#40A640', '#5F78B5'], title: '<?php echo JText::_('ACY_STAT_MOBILE_USAGE', true);?>', is3D: true, legendTextStyle: {color: '#333333'}
			};
			vis.draw(getDatadrawChartIsMobile(), options);
		}
		function getDatadrawChartIsMobile(){
			var dataTable = new google.visualization.DataTable();
			dataTable.addRows(2);

			dataTable.addColumn('string');
			dataTable.addColumn('number');
			<?php $valNoMob = (empty($this->ismobilestats[0]) ? 0 : $this->ismobilestats[0]->nbMobile);
			$valMob = (empty($this->ismobilestats[1]) ? 0 : $this->ismobilestats[1]->nbMobile); ?>
			dataTable.setValue(0, 0, '<?php echo intval($valNoMob).' '.JText::_('ACY_STAT_NOMOBILE'); ?>');
			dataTable.setValue(0, 1, <?php echo intval($valNoMob); ?>);
			dataTable.setValue(1, 0, '<?php echo intval($valMob).' '.JText::_('ACY_STAT_MOBILE'); ?>');
			dataTable.setValue(1, 1, <?php echo intval($valMob); ?>);

			return dataTable;
		}
		<?php } else{ ?>
		function drawChartIsMobile(){
			document.getElementById('device').style.display = 'none';
		}
		<?php } ?>
		<?php if(!empty($this->mobileosstats)){ ?>
		function drawChartMobileOS(){
			var vis = new google.visualization.PieChart(document.getElementById('mobileos'));
			var options = {
				width: 360, height: 300, title: '<?php echo JText::_('ACY_STAT_MOBILEOS', true);?>', is3D: true, legendTextStyle: {color: '#333333'}, chartAreaLeft: 10
			};
			vis.draw(getDatadrawChartMobileOS(), options);
		}
		function getDatadrawChartMobileOS(){
			var dataTable = new google.visualization.DataTable();
			dataTable.addRows(<?php echo count($this->mobileosstats);?>);

			dataTable.addColumn('string');
			dataTable.addColumn('number');

			var i = 0;
			<?php foreach($this->mobileosstats as $oneOS => $detailOS){ ?>
			dataTable.setValue(i, 0, '<?php echo intval($detailOS->nbOS).' '.$oneOS; ?>');
			dataTable.setValue(i, 1, <?php echo intval($detailOS->nbOS); ?>);
			i += 1;
			<?php } ?>

			return dataTable;
		}
		<?php } else{ ?>
		function drawChartMobileOS(){
			document.getElementById('mobileos').style.display = 'none';
		}
		<?php } ?>

		function getDataDrawChartOpenDay(){
			var dataTable = new google.visualization.DataTable();
			dataTable.addRows(<?php echo count($this->opendaydata); ?>);

			dataTable.addColumn('string');
			dataTable.addColumn('number', '<?php echo JText::_('OPEN', true); ?>');
			dataTable.addColumn({type: 'string', role: 'annotation'});

			<?php
			foreach($this->opendaydata as $i => $oneResult){
				echo "dataTable.setValue($i, 0, '".$this->opendaylabel[$i]."');";
				echo "dataTable.setValue($i, 1, $oneResult);";
				echo "dataTable.setValue($i, 2, '".addslashes($this->opendayannotation[$i])."');";
			} ?>
			return dataTable;
		}
		function drawChartOpenDay(){
			var vis = new google.visualization.LineChart(document.getElementById('opendaydata'));
			var options = {
				width: 730, height: 300, title: '<?php echo JText::_('OPEN', true);?>', is3D: true, legend: 'none', hAxis: {textPosition: 'none'}, vAxis: {viewWindow: {min: 0}, format: 'short'}
			};
			vis.draw(getDataDrawChartOpenDay(), options);
		}

		google.load("visualization", "1", {packages: ["corechart"]});
		google.setOnLoadCallback(drawChartSendProcess);
		google.setOnLoadCallback(drawChartOpen);
	</script>
	<style type="text/css">
		div.miniacycharts{
			width: 80px;
			float: left;
			text-align: center;
		}

		span.statnumber{
			font-size: 14px
		}

		body{
			height: auto
		}
	</style>
	<h1 class="onlyprint acystatpopuptitle"><?php echo $this->mailing->subject; ?></h1>
	<?php if(!$app->isAdmin()){ ?>
		<form action="index.php?option=<?php echo ACYMAILING_COMPONENT ?>" method="post" name="adminForm" autocomplete="off" id="adminForm">
			<fieldset class="acyheaderarea">
				<div class="acyheader icon-48-stats" style="float: left;"><?php echo $this->mailing->subject; ?></div>
				<div class="toolbar" id="toolbar" style="float: right;">
					<table>
						<tr>
							<td><a onclick="window.print(); return false;" href="#"><span class="icon-32-acyprint" title="<?php echo JText::_('ACY_PRINT', true); ?>"></span><?php echo JText::_('ACY_PRINT'); ?></a></td>
						</tr>
					</table>
				</div>
			</fieldset>
		</form>
	<?php } ?>
	<table width="100%" style="border: none;">
		<tr style="border: none;">
			<td width="45%" style="border: none;">
				<div class="acyblockoptions acystatsummary">
					<span class="acyblocktitle"><?php echo JText::_('ACY_SUMMARY'); ?></span>
					<ul>
						<li>
							<?php
							$text = '<span class="statnumber">'.((int)$this->mailingstats->senthtml + (int)$this->mailingstats->senttext).'</span>';
							if(acymailing_isAllowed($this->config->get('acl_subscriber_view', 'all'))){
								$link = 'stats&task=detaillisting';
								if(!$app->isAdmin()) $link = 'frontnewsletter&task=detailstats&listid='.JRequest::getInt('listid').'&mailid='.$this->mailing->mailid;
								$text = '<a href="'.acymailing_completeLink($link.'&filter_status=0&filter_mail='.$this->mailing->mailid, true).'">'.$text.'</a>';
							}
							echo JText::sprintf('TOTAL_EMAIL_SENT', $text) ?></li>
						<?php if(!empty($this->mailingstats->queue)){ ?>
							<li><?php echo JText::sprintf('NB_PENDING_EMAIL', $this->mailingstats->queue, '<b><i>'.$this->mailing->subject.'</i></b>'); ?></li>
						<?php } ?>
						<?php if(!empty($this->mailingstats->senddate)){ ?>
							<li><?php echo JText::_('SEND_DATE').' : <span class="statnumber">'.acymailing_getDate($this->mailingstats->senddate); ?></span></li>
						<?php } ?>
						<?php if(!empty($this->mailingstats->senthtml)){ ?>
							<li>
								<?php
								if(acymailing_level(3)){
									$cleanSent = $this->mailingstats->senthtml + $this->mailingstats->senttext - $this->mailingstats->bounceunique;
								}else $cleanSent = $this->mailingstats->senthtml + $this->mailingstats->senttext;
								$pourcent = ($cleanSent == 0 ? '0%' : (substr($this->mailingstats->openunique / $cleanSent * 100, 0, 5)).'%');
								$text = '<span class="statnumber">'.$pourcent.'</span>';
								if(acymailing_isAllowed($this->config->get('acl_subscriber_view', 'all'))){
									$link = 'stats&task=detaillisting';
									if(!$app->isAdmin()) $link = 'frontnewsletter&task=detailstats&listid='.JRequest::getInt('listid').'&mailid='.$this->mailing->mailid;
									$text = '<a href="'.acymailing_completeLink($link.'&filter_status=open&filter_mail='.$this->mailing->mailid, true).'">'.$text.'</a>';
								}

								echo JText::sprintf('NB_OPEN_USERS', $text).' ( '.$this->mailingstats->openunique.' / '.$cleanSent.' )';
								?>
							</li>
							<li>
								<?php
								$pourcent = ($cleanSent == 0 ? '0%' : (substr($this->mailingstats->clickunique / $cleanSent * 100, 0, 5)).'%');
								$text = '<span class="statnumber">'.$pourcent.'</span>';
								if(acymailing_isAllowed($this->config->get('acl_subscriber_view', 'all'))){
									$link = 'statsurl&task=detaillisting';
									if(!$app->isAdmin()) $link = 'frontnewsletter&task=statsclick&listid='.JRequest::getInt('listid').'&mailid='.$this->mailing->mailid;
									$text = '<a href="'.acymailing_completeLink($link.'&filter_url=0&filter_mail='.$this->mailing->mailid, true).'">'.$text.'</a>';
								}

								echo JText::sprintf('NB_CLICK_USERS', $text).' ( '.$this->mailingstats->clickunique.' / '.$cleanSent.' )';
								?>
							</li>
							<li>
								<?php
								$pourcent = ($this->mailingstats->openunique == 0 ? '0%' : (substr($this->mailingstats->clickunique / $this->mailingstats->openunique * 100, 0, 5)).'%');
								$text = '<span class="statnumber">'.$pourcent.'</span>';
								if(acymailing_isAllowed($this->config->get('acl_subscriber_view', 'all'))){
									$link = 'statsurl&task=detaillisting';
									if(!$app->isAdmin()) $link = 'frontnewsletter&task=statsclick&listid='.JRequest::getInt('listid').'&mailid='.$this->mailing->mailid;
									$text = '<a href="'.acymailing_completeLink($link.'&filter_url=0&filter_mail='.$this->mailing->mailid, true).'">'.$text.'</a>';
								}
								echo JText::sprintf('ACY_CLICK_EFFICIENCY_DESC', $text).' ( '.$this->mailingstats->clickunique.' / '.$this->mailingstats->openunique.' )';
								?>
							</li>
						<?php } ?>

						<li><?php $pourcent = ($cleanSent == 0) ? '0%' : (substr($this->mailingstats->unsub / $cleanSent * 100, 0, 5)).'%';
							$text = '<span class="statnumber">'.$pourcent.'</span>';
							if(acymailing_isAllowed($this->config->get('acl_subscriber_view', 'all'))){
								$link = 'stats&start=0&task=unsubscribed';
								if(!$app->isAdmin()) $link = 'frontnewsletter&task=unsubscribed&listid='.JRequest::getInt('listid').'&mailid='.$this->mailing->mailid;
								$text = '<a href="'.acymailing_completeLink($link.'&fromdetail=1&filter_mail='.$this->mailing->mailid, true).'">'.$text.'</a>';
							}
							echo JText::sprintf('NB_UNSUB_USERS', $text).' ( '.$this->mailingstats->unsub.' )'; ?></li>

						<?php if(acymailing_level(3)){ ?>
							<li>
								<?php
								$pourcent = (empty($this->mailingstats->senthtml) AND empty($this->mailingstats->senttext)) ? '0%' : (substr($this->mailingstats->bounceunique / ($this->mailingstats->senthtml + $this->mailingstats->senttext) * 100, 0, 5)).'%';
								$text = '<span class="statnumber">'.$pourcent.'</span>';
								if(acymailing_isAllowed($this->config->get('acl_subscriber_view', 'all'))){
									$link = 'stats&task=detaillisting';
									if(!$app->isAdmin()) $link = 'frontnewsletter&task=detailstats&listid='.JRequest::getInt('listid').'&mailid='.$this->mailing->mailid;
									$text = '<a href="'.acymailing_completeLink($link.'&filter_status=bounce&filter_mail='.$this->mailing->mailid, true).'">'.$text.'</a>';
								}
								echo JText::sprintf('NB_BOUNCED_USERS', $text).' ( '.$this->mailingstats->bounceunique.' )';
								?>
							</li>
						<?php } ?>
					</ul>
				</div>
			</td>
			<td width="55%" valign="top" style="border: none;">
				<?php if(!empty($this->mailinglinks)){ ?>
					<div class="acyblockoptions acypopularlinks">
						<span class="acyblocktitle"><?php echo JText::_('MOST_POPULAR_LINKS'); ?></span>
						<ul>
							<?php foreach($this->mailinglinks as $oneLink){
								$urlName = $oneLink->name;
								if(strlen($urlName) > 45) $urlName = substr($oneLink->name, 0, 20).'...'.substr($oneLink->name, -20);
								$text = '<span class="statnumber">'.$oneLink->uniqueclick.'</span>';
								if(acymailing_isAllowed($this->config->get('acl_subscriber_view', 'all'))){
									$link = 'statsurl&task=detaillisting';
									if(!$app->isAdmin()) $link = 'frontnewsletter&task=statsclick&listid='.JRequest::getInt('listid').'&mailid='.$this->mailing->mailid;
									$text = '<a href="'.acymailing_completeLink($link.'&filter_url='.$oneLink->urlid.'&filter_mail='.$this->mailing->mailid, true).'">'.$text.'</a>';
								}
								echo '<li>'.JText::sprintf('NB_USERS_CLICKED_ON', $text, '<a target="_blank" href="'.$oneLink->url.'">'.$urlName.'</a>').'</li>';
							} ?>
						</ul>
					</div>
				<?php } ?>
			</td>
		</tr>
	</table>

	<div class="acyblockoptions" style="<?php echo $app->isAdmin() ? 'padding-left:0px;padding-right:0px;' : 'padding-left:7px;padding-right:7px;'; ?>">
		<table id="acy_selectChart" style="margin-left: 20px;">
			<tr>
				<td>
					<label for="selectChartAll" class="selectChart"><input type="radio" name="selectchart" onclick="displayChart('all'); drawChartSendProcess(); drawChartOpen(); drawChartOpenclick(); <?php if(!empty($this->openclickday)){ ?>drawChartOpenclickperday(); <?php } ?><?php if(!empty($this->browserstats)){ ?> drawChartBrowser(); <?php } ?><?php if(!empty($this->ismobilestats) || !empty($this->mobileosstats)){ ?>drawChartIsMobile(); drawChartMobileOS();<?php } ?>" id="selectChartAll"/><?php echo JText::_('ACY_ALL'); ?></label>
				</td>
			</tr>
			<tr>
				<td><label for="selectChartOpenRate" class="selectChart"><input type="radio" name="selectchart" checked="checked" onclick="displayChart('chartopenratetable');drawChartSendProcess();drawChartOpen();" id="selectChartOpenRate"/><?php echo JText::_('ACY_STAT_OPEN_RATE'); ?></label></td>
				<td><label for="selectChartOpenClick" class="selectChart"><input type="radio" name="selectchart" onclick="displayChart('openclicktotal');drawChartOpenclick();" id="selectChartOpenClick"/><?php echo JText::_('ACY_STAT_OPEN_CLICK'); ?></label></td>
				<?php if(!empty($this->openclickday)){ ?>
					<td><label for="selectChartOpenClickPerDay" class="selectChart"><input type="radio" name="selectchart" onclick="displayChart('openclickperday');drawChartOpenclickperday();" id="selectChartOpenClickPerDay"/><?php echo JText::_('ACY_STAT_OPEN_CLICK_DAY'); ?></label></td>
				<?php } ?>
				<?php if(!empty($this->opendaydata)){ ?>
					<td><label for="selectChartOpenClickDayData" class="selectChart"><input type="radio" name="selectchart" onclick="displayChart('opendaydata');drawChartOpenDay();" id="selectChartOpenClickDayData"/><?php echo JText::_('ACY_STAT_OPEN_TIME'); ?></label></td>
				<?php } ?>
			</tr>
			<tr>
				<?php if(!empty($this->browserstats)){ ?>
					<td><label for="selectChartBrowser" class="selectChart"><input type="radio" name="selectchart" onclick="displayChart('browser');drawChartBrowser();" id="selectChartBrowser"/><?php echo JText::_('ACY_STAT_BROWSER'); ?></label></td>
				<?php } ?>
				<?php if(!empty($this->ismobilestats) || !empty($this->mobileosstats)){ ?>
					<td><label for="selectChartMobile" class="selectChart"><input type="radio" name="selectchart" onclick="displayChart('chartdevicetable');drawChartIsMobile();drawChartMobileOS();" id="selectChartMobile"/><?php echo JText::_('ACY_STAT_MOBILE_USAGE'); ?></label></td>
				<?php } ?>
			</tr>
		</table>

		<table width="100%" border="0" id="chartopenratetable">
			<tr>
				<td width="50%">
					<div id="sendprocess" class="acychart acysmallchart"></div>
				</td>
				<td width="50%">
					<div id="open" class="acychart acysmallchart"></div>
				</td>
			</tr>
		</table>
		<div width="100%" style="display:none" id="openclicktotal" class="acychart acynormalchart"></div>
		<div width="100%" style="display:none" id="openclickperday" class="acychart acynormalchart"></div>
		<div width="100%" style="display:none" id="opendaydata" class="acychart acynormalchart"></div>
		<div width="100%" style="display:none" id="browser" class="acychart acynormalchart"></div>
		<table width="100%" style="display:none" border="0" id="chartdevicetable">
			<tr>
				<td width="50%">
					<div id="device" class="acychart acysmallchart"></div>
				</td>
				<td width="50%">
					<div id="mobileos" class="acychart acysmallchart"></div>
				</td>
			</tr>
		</table>
	</div>
	<div style="clear: both;"/>
</div>
