<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php $app = JFactory::getApplication();
$backend = $app->isAdmin(); ?>
<style type="text/css">
	.respuserinfo{
		float: left;
		display: inline-table;
	<?php if(!$backend){ ?> max-width: 900px;
		min-width: 60%;
		width: 100%;
	<?php } else{ ?> max-width: 600px;
		min-width: 30%;
	<?php } ?>
	}

	.respuserinfo50{
		min-width: 50%;
	}

	.respuserinfogeneral{
		display: inline-table;
		float: left;
		min-width: 60%;
		width: 100%;
		max-width: 900px;
	}

	#acysubscriberinfo{
		clear: both;
	<?php if(!$backend){
		echo "overflow:auto;
			max-width:750px;
			min-width:80%";
	} ?>
	}

	<?php if(!$backend){
		echo "#acy_content .current {
				display: table;
			}			";
	} ?>

</style>
<script language="javascript" type="text/javascript">
	<?php if(!ACYMAILING_J16){ ?>
	function submitbutton(pressbutton){
		<?php }else{ ?>
		Joomla.submitbutton = function(pressbutton){
			<?php } ?>
			var form = document.adminForm;
			if(pressbutton != 'cancel' && form.email){
				form.email.value = form.email.value.replace(/ /g, "");
				var filter = /^([a-z0-9_'&\.\-\+=])+\@(([a-z0-9\-])+\.)+([a-z0-9]{2,10})+$/i;
				if(!filter.test(form.email.value)){
					alert("<?php echo JText::_('VALID_EMAIL', true); ?>");
					return false;
				}
			}
			<?php if(!ACYMAILING_J16){ ?>
			submitform(pressbutton);
		}
		<?php }else{ ?>
		Joomla.submitform(pressbutton, form);
	}
	;
	<?php } ?>
</script>
<?php if(!empty($this->geoloc)){ ?>
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	<script language="JavaScript" type="text/javascript">
		var mapOptions = {
			legend: 'none', displayMode: 'markers', sizeAxis: {minSize: 6, maxSize: 24, minValue: 1, maxValue: 10}, enableRegionInteractivity: 'true', region: '<?php echo $this->geoloc_region; ?>'
		};
		google.load('visualization', '1', {'packages': ['geochart']});
		google.setOnLoadCallback(drawMarkersMap);
		var chart;
		var data;
		function drawMarkersMap(){
			data = new google.visualization.DataTable();
			data.addColumn('string', 'Address');
			data.addColumn('number', 'Color');
			data.addColumn('number', 'Size');
			data.addColumn({type: 'string', role: 'tooltip'});
			<?php
			$myData = array();
			foreach($this->geoloc_city as $key => $city){
				$toolTipTxt = str_replace("'", "\'", JText::_('GEOLOC_NB_ACTIONS')).': '.$this->geoloc_details[$key]['nbInCity'];
				$lineData = "['".str_replace("'", "\'", $this->geoloc_details[$key]['address'])."', 1, ".$this->geoloc_details[$key]['nbInCity'].", '".$toolTipTxt."']";
				array_push($myData, $lineData);
			}
			echo "data.addRows([".implode(", ", $myData)."]);";
			?>
			chart = new google.visualization.GeoChart(document.getElementById('mapGeoloc_div'));
			chart.draw(data, mapOptions);
		}
		;

	</script>
<?php } ?>
<div id="acy_content">
	<div id="iframedoc"></div>

	<form action="<?php echo JRoute::_('index.php?option='.ACYMAILING_COMPONENT.'&ctrl='.JRequest::getCmd('ctrl')); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off" <?php if(!empty($this->fieldsClass->formoption)) echo $this->fieldsClass->formoption; ?> >
		<input type="hidden" name="cid[]" value="<?php echo @$this->subscriber->subid; ?>"/>
		<input type="hidden" name="option" value="<?php echo ACYMAILING_COMPONENT; ?>"/>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="acy_source" value="<?php $app = JFactory::getApplication();
		echo $app->isAdmin() ? 'management_back' : 'management_front'; ?>"/>
		<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>"/>
		<?php $selectedList = JRequest::getInt('filter_lists');
		if(!empty($selectedList)){ ?>
			<input type="hidden" name="filter_lists" value="<?php echo $selectedList; ?>"/>
		<?php }
		if(!empty($this->Itemid)) echo '<input type="hidden" name="Itemid" value="'.$this->Itemid.'" />';
		echo JHTML::_('form.token'); ?>
		<div class="<?php echo $this->isAdmin ? 'acyblockoptions' : 'onelineblockoptions'; ?>">
			<span class="acyblocktitle"><?php echo JText::_('USER_INFORMATIONS'); ?></span>

			<div>
				<?php if(!acymailing_level(3) || empty($this->extraFields)){
					echo '<div class="acytable_userinfo">';
				} ?>
				<table class="acymailing_table" cellspacing="1">
					<tr id="trname">
						<td width="150" class="acykey">
							<label for="name">
								<?php echo JText::_('JOOMEXT_NAME'); ?>
							</label>
						</td>
						<td>
							<?php
							if(empty($this->subscriber->userid)){
								echo '<input type="text" name="data[subscriber][name]" id="name" class="inputbox" style="width:200px" value="'.$this->escape(@$this->subscriber->name).'" />';
							}else{
								echo $this->escape($this->subscriber->name);
							}
							?>
						</td>
					</tr>
					<tr id="tremail">
						<td class="acykey">
							<label for="email">
								<?php echo JText::_('JOOMEXT_EMAIL'); ?>
							</label>
						</td>
						<td>
							<?php
							if(empty($this->subscriber->userid)){
								echo '<input class="inputbox required" type="text" name="data[subscriber][email]" id="email" style="width:200px" value="'.$this->escape(@$this->subscriber->email).'" />';
							}else{
								echo $this->escape($this->subscriber->email);
							}
							?>
						</td>
					</tr>
					<tr id="trcreated">
						<td class="acykey">
							<label for="created">
								<?php echo JText::_('CREATED_DATE'); ?>
							</label>
						</td>
						<td>
							<?php echo acymailing_getDate($this->subscriber->created); ?>
						</td>
					</tr>
					<tr id="trip">
						<td class="acykey">
							<label for="ip">
								<?php echo JText::_('IP'); ?>
							</label>
						</td>
						<td>
							<?php echo $this->escape($this->subscriber->ip); ?>
						</td>
					</tr>

					<?php
					if(!empty($this->subscriber->userid)){
						?>
						<tr id="trusername">
							<td class="acykey">
								<label for="username">
									<?php echo JText::_('ACY_USERNAME'); ?>
								</label>
							</td>
							<td>
								<?php echo $this->escape($this->subscriber->username); ?>
							</td>
						</tr>
						<tr id="truserid">
							<td class="acykey">
								<label for="userid">
									<?php echo JText::_('USER_ID'); ?>
								</label>
							</td>
							<td>
								<?php echo $this->subscriber->userid; ?>
							</td>
						</tr>
						<?php
					}
					if(!acymailing_level(3) || empty($this->extraFields)){
						echo '</table></div><div class="acytable_userinfo"><table class="acymailing_table" cellspacing="1">';
					} ?>
					<tr id="trhtml">
						<td class="acykey">
							<label for="html">
								<?php echo JText::_('RECEIVE'); ?>
							</label>
						</td>
						<td nowrap="nowrap">
							<?php echo JHTML::_('acyselect.booleanlist', "data[subscriber][html]", '', $this->subscriber->html, JText::_('HTML'), JText::_('JOOMEXT_TEXT')); ?>
						</td>
					</tr>
					<tr id="trconfirmed">
						<td class="acykey">
							<label for="confirmed">
								<?php echo JText::_('CONFIRMED'); ?>
							</label>
						</td>
						<td>
							<?php echo JHTML::_('acyselect.booleanlist', "data[subscriber][confirmed]", '', $this->subscriber->confirmed, JText::_('JOOMEXT_YES'), JTEXT::_('JOOMEXT_NO')); ?>
						</td>
					</tr>
					<tr id="trenabled">
						<td class="acykey">
							<label for="block">
								<?php echo JText::_('ENABLED'); ?>
							</label>
						</td>
						<td>
							<?php echo JHTML::_('acyselect.booleanlist', "data[subscriber][enabled]", '', $this->subscriber->enabled, JText::_('JOOMEXT_YES'), JTEXT::_('JOOMEXT_NO')); ?>
						</td>
					</tr>
					<tr id="traccept">
						<td class="acykey">
							<label for="accept">
								<?php echo JText::_('ACCEPT_EMAIL'); ?>
							</label>
						</td>
						<td>
							<?php echo JHTML::_('acyselect.booleanlist', "data[subscriber][accept]", '', $this->subscriber->accept, JText::_('JOOMEXT_YES'), JTEXT::_('JOOMEXT_NO')); ?>
						</td>
					</tr>
				</table>
				<?php if(!acymailing_level(3) || empty($this->extraFields)){
					echo '</div>';
				} ?>
			</div>
		</div>
		<?php
		if(!empty($this->extraFields)){
			$app = JFactory::getApplication();
			$this->fieldsClass->currentUser = $this->subscriber;
			include(dirname(__FILE__).DS.'extrafields.'.basename(__FILE__));
		} ?>
		<div class="onelineblockoptions acytabsystem" style="clear:both;<?php echo $this->isAdmin ? '' : 'max-width:700px;'; ?>">
			<div id="acysubscriberinfo">
				<?php $tabs = acymailing_get('helper.acytabs');
				$tabs->setOptions(array('useCookie' => true));

				echo $tabs->startPane('user_tabs');
				echo $tabs->startPanel(JText::_('SUBSCRIPTION'), 'user_subscription');

				if(count($this->subscription) > 10){ ?>
					<script language="javascript" type="text/javascript">
						<!--
						function acymailing_searchAList(){
							var filter = document.getElementById("acymailing_searchList").value.toLowerCase();
							for(var i = 0; i <<?php echo count($this->subscription); ?>; i++){
								var itemName = document.getElementById("listName_" + i).innerHTML.toLowerCase();
								if(itemName.indexOf(filter) > -1){
									document.getElementById("acylistrow_" + i).style.display = "table-row";
								}else{
									document.getElementById("acylistrow_" + i).style.display = "none";
								}
							}
						}
						//-->
					</script>
				<?php } ?>
				<div>
					<table class="acymailing_table">
						<thead>
						<tr>
							<th class="title titlenum">
								<?php echo JText::_('ACY_NUM'); ?>
							</th>
							<th class="title titlecolor">
							</th>
							<th class="title" nowrap="nowrap">
								<?php echo JText::_('LIST_NAME');
								if(count($this->subscription) > 10){ ?>
									<input onkeyup="acymailing_searchAList();" type="text" style="width:170px;max-width:100%;margin-left:50px;margin-top:5px;" placeholder="<?php echo JText::_('ACY_SEARCH'); ?>" id="acymailing_searchList">
								<?php } ?>
							</th>
							<th class="title" nowrap="nowrap">
								<?php echo JText::_('STATUS'); ?>
								<span class="quickstatuschange" style="display:inline-block;font-style:italic;margin-left:50px"><?php echo $this->filters->statusquick; ?></span>
							</th>
							<th class="title titledate">
								<?php echo JText::_('SUBSCRIPTION_DATE'); ?>
							</th>
							<th class="title titledate">
								<?php echo JText::_('UNSUBSCRIPTION_DATE'); ?>
							</th>
							<th class="title titleid">
								<?php echo JText::_('ACY_ID'); ?>
							</th>
						</tr>
						</thead>
						<tbody>
						<?php
						$k = 0;
						$i = 0;
						foreach($this->subscription as $j => $row){
							$listClass = 'acy_list_status_'.str_replace('-', 'm', (int)@$row->status); ?>
							<tr class="<?php echo "row$k $listClass"; ?>" id="acylistrow_<?php echo $i; ?>">
								<td align="center" style="text-align:center">
									<?php echo $i + 1; ?>
								</td>
								<td width="12">
									<?php echo '<div class="roundsubscrib rounddisp" style="background-color:'.$row->color.'"></div>'; ?>
								</td>
								<td>
									<span style="display:none;" id="listName_<?php echo $i; ?>"><?php echo $row->name; ?></span>
									<?php echo acymailing_tooltip($row->description, $row->name, 'tooltip.png', $row->name); ?>
								</td>
								<td align="center" style="text-align:center" nowrap="nowrap">
									<?php echo $this->statusType->display('data[listsub]['.$row->listid.'][status]', (empty($this->subscriber->subid) && JRequest::getInt('filter_lists') == $row->listid) ? 1 : @$row->status); ?>
								</td>
								<td align="center" style="text-align:center">
									<?php if(!empty($row->subdate)) echo acymailing_getDate($row->subdate); ?>
								</td>
								<td align="center" style="text-align:center">
									<?php if(!empty($row->unsubdate)) echo acymailing_getDate($row->unsubdate); ?>
								</td>
								<td align="center" style="text-align:center">
									<?php echo $row->listid; ?>
								</td>
							</tr>
							<?php
							$k = 1 - $k;
							$i++;
						} ?>
						</tbody>
					</table>
				</div>
				<?php echo $tabs->endPanel();
				if(!empty($this->open)){
					echo $tabs->startPanel(JText::_('ACY_SENT_EMAILS'), 'user_open');
					?>

					<div>
						<table class="acymailing_table">
							<thead>
							<tr>
								<th class="title titlenum">
									<?php echo JText::_('ACY_NUM'); ?>
								</th>
								<th class="title titledate">
									<?php echo JText::_('SEND_DATE'); ?>
								</th>
								<th class="title">
									<?php echo JText::_('JOOMEXT_SUBJECT'); ?>
								</th>
								<th class="title titletoggle">
									<?php echo JText::_('RECEIVED_VERSION'); ?>
								</th>
								<th class="title titletoggle">
									<?php echo JText::_('OPEN'); ?>
								</th>
								<th class="title titledate">
									<?php echo JText::_('OPEN_DATE'); ?>
								</th>
								<?php if(acymailing_level(3)){ ?>
									<th class="title titletoggle">
										<?php echo JText::_('CLICKED_LINK'); ?>
									</th>
									<th class="title titletoggle">
										<?php echo JText::_('BOUNCES'); ?>
									</th>
								<?php } ?>
								<th class="title titletoggle">
									<?php echo JText::_('ACY_SENT'); ?>
								</th>
							</tr>
							</thead>
							<tbody>
							<?php
							$width = intval($this->config->get('popup_width', 750));
							$height = intval($this->config->get('popup_height', 550));
							$k = 0;

							for($i = 0, $a = count($this->open); $i < $a; $i++){
								$row =& $this->open[$i];
								?>
								<tr class="<?php echo "row$k"; ?>">
									<td align="center" style="text-align:center">
										<?php echo $i + 1; ?>
									</td>
									<td>
										<?php echo acymailing_getDate($row->senddate); ?>
									</td>
									<td>
										<?php
										if($app->isAdmin()){
											$link = acymailing_completeLink('queue&task=preview&mailid='.$row->mailid.'&subid='.$this->subscriber->subid, true);
											echo '<a class="modal" rel="{handler: \'iframe\', size: {x: '.$width.', y: '.$height.'}}" href="'.$link.'">'.$row->subject.'</a>';
										}else{
											$text = '<b>'.JText::_('ACY_ID').' : </b>'.$row->mailid;
											echo acymailing_tooltip( $text, $row->subject, '', $row->subject);
										}
										?>
									</td>
									<td align="center" style="text-align:center">
										<?php echo $row->html ? JText::_('HTML') : JText::_('JOOMEXT_TEXT'); ?>
									</td>
									<td align="center" style="text-align:center">
										<?php echo $row->open; ?>
									</td>
									<td align="center" style="text-align:center">
										<?php if(!empty($row->opendate)) echo acymailing_getDate($row->opendate); ?>
									</td>
									<?php if(acymailing_level(3)){ ?>
										<td align="center" style="text-align:center">
											<?php echo $this->toggleClass->display('visible', empty($this->clickedNews[$row->mailid]) ? false : true); ?>
										</td>
										<td align="center" style="text-align:center">
											<?php echo $row->bounce; ?>
										</td>
									<?php } ?>
									<td align="center" style="text-align:center">
										<?php echo $this->toggleClass->display('visible', empty($row->fail) ? true : false); ?>
									</td>
								</tr>
								<?php
								$k = 1 - $k;
							}
							?>
							</tbody>
						</table>
					</div>

					<?php
					echo $tabs->endPanel();
				}

				if(!empty($this->clicks)){
					echo $tabs->startPanel(JText::_('CLICK_STATISTICS'), 'user_clicks'); ?>

					<div>
						<table class="acymailing_table">
							<thead>
							<tr>
								<th class="title titlenum">
									<?php echo JText::_('ACY_NUM'); ?>
								</th>
								<th class="title titledate">
									<?php echo JText::_('CLICK_DATE'); ?>
								</th>
								<th class="title">
									<?php echo JText::_('JOOMEXT_SUBJECT'); ?>
								</th>
								<th class="title">
									<?php echo JText::_('URL'); ?>
								</th>
								<th class="title titletoggle">
									<?php echo JText::_('TOTAL_HITS'); ?>
								</th>
							</tr>
							</thead>
							<tbody>
							<?php
							$k = 0;

							for($i = 0, $a = count($this->clicks); $i < $a; $i++){
								$row =& $this->clicks[$i];
								$id = 'urlclick'.$i;
								?>
								<tr class="<?php echo "row$k"; ?>" id="<?php echo $id; ?>">
									<td align="center" style="text-align:center">
										<?php echo $i + 1; ?>
									</td>
									<td>
										<?php echo acymailing_getDate($row->date); ?>
									</td>
									<td>
										<?php
										$text = '<b>'.JText::_('ACY_ID').' : </b>'.$row->mailid;
										echo acymailing_tooltip($text, $row->subject, '', $row->subject);
										?>
									</td>
									<td>
										<a target="_blank" href="<?php echo strip_tags($row->url); ?>"><?php echo $row->urlname; ?></a>
									</td>
									<td align="center" style="text-align:center">
										<?php echo $row->click; ?>
									</td>
								</tr>
								<?php
								$k = 1 - $k;
							}
							?>
							</tbody>
						</table>
					</div>

					<?php echo $tabs->endPanel();
				}

				if(!empty($this->queue)){
					echo $tabs->startPanel(JText::_('QUEUE'), 'user_queue'); ?>

					<div>
						<table class="acymailing_table">
							<thead>
							<tr>
								<th class="title titlenum">
									<?php echo JText::_('ACY_NUM'); ?>
								</th>
								<th class="title titledate">
									<?php echo JText::_('SEND_DATE'); ?>
								</th>
								<th class="title">
									<?php echo JText::_('JOOMEXT_SUBJECT'); ?>
								</th>
								<th class="title titlenum">
									<?php echo JText::_('PRIORITY'); ?>
								</th>
								<th class="title titlenum">
									<?php echo JText::_('TRY'); ?>
								</th>
								<th class="title titletoggle">
									<?php echo JText::_('ACY_DELETE'); ?>
								</th>
							</tr>
							</thead>
							<tbody>
							<?php
							$k = 0;

							for($i = 0, $a = count($this->queue); $i < $a; $i++){
								$row =& $this->queue[$i];
								$id = 'queue'.$i;
								?>
								<tr class="<?php echo "row$k"; ?>" id="<?php echo $id; ?>">
									<td align="center" style="text-align:center">
										<?php echo $i + 1; ?>
									</td>
									<td>
										<?php echo acymailing_getDate($row->senddate); ?>
									</td>
									<td>
										<?php
										$text = '<b>'.JText::_('ACY_ID').' : </b>'.$row->mailid;
										echo acymailing_tooltip($text, $row->subject, '', $row->subject);
										?>
									</td>
									<td align="center" style="text-align:center">
										<?php echo $row->priority; ?>
									</td>
									<td align="center" style="text-align:center">
										<?php echo $row->try; ?>
									</td>
									<td align="center" style="text-align:center">
										<?php echo $this->toggleClass->delete($id, $row->subid.'_'.$row->mailid, 'queue'); ?>
									</td>
								</tr>
								<?php
								$k = 1 - $k;
							}
							?>
							</tbody>
						</table>
					</div>

					<?php echo $tabs->endPanel();
				}

				if(!empty($this->history)){
					echo $tabs->startPanel(JText::_('ACY_HISTORY'), 'user_history');
					?>

					<div>
						<table class="acymailing_table">
							<thead>
							<tr>
								<th class="title titlenum">
									<?php echo JText::_('ACY_NUM'); ?>
								</th>
								<th class="title titledate">
									<?php echo JText::_('FIELD_DATE'); ?>
								</th>
								<th class="title">
									<?php echo JText::_('ACY_ACTION'); ?>
								</th>
								<th class="title">
									<?php echo JText::_('ACY_DETAILS'); ?>
								</th>
								<th class="title">
									<?php echo JText::_('IP'); ?>
								</th>
								<th class="title" width="30%">
									<?php echo JText::_('ACY_SOURCE'); ?>
								</th>
							</tr>
							</thead>
							<tbody>
							<?php
							$k = 0;

							for($i = 0, $a = count($this->history); $i < $a; $i++){
								$row =& $this->history[$i];
								?>
								<tr class="<?php echo "row$k"; ?>">
									<td align="center" style="text-align:center" valign="top">
										<?php echo $i + 1; ?>
									</td>
									<td align="center" style="text-align:center">
										<?php echo acymailing_getDate($row->date); ?>
									</td>
									<td valign="top">
										<?php echo JText::_('ACTION_'.strtoupper($row->action)); ?>
									</td>
									<td valign="top">
										<?php
										if(!empty($row->data)){
											$data = explode("\n", $row->data);
											$id = 'history_details'.$i;
											echo '<div style="cursor:pointer;text-align:center" onclick="if(document.getElementById(\''.$id.'\').style.display == \'none\'){document.getElementById(\''.$id.'\').style.display = \'block\'}else{document.getElementById(\''.$id.'\').style.display = \'none\'}">'.JText::_('VIEW_DETAILS').'</div>';
											echo '<div id="'.$id.'" style="display:none">';
											if(!empty($row->mailid)) echo '<b>'.JText::_('NEWSLETTER').' : </b>'.$this->escape($row->subject).' ( '.JText::_('ACY_ID').' : '.$row->mailid.' )<br />';
											foreach($data as $value){
												if(!strpos($value, '::')){
													echo $value;
													continue;
												}
												list($part1, $part2) = explode("::", $value);
												if(preg_match('#^[A-Z_]*$#', $part2)) $part2 = JText::_($part2);
												echo '<b>'.$this->escape(JText::_($part1)).' : </b>'.$this->escape($part2).'<br />';
											}
											echo '</div>';
										}
										?>
									</td>
									<td valign="top">
										<?php echo $row->ip ?>
									</td>
									<td valign="top">
										<?php
										if(!empty($row->source)){
											$id = 'history_source'.$i;
											$source = explode("\n", $row->source);
											echo '<div style="cursor:pointer;text-align:center" onclick="if(document.getElementById(\''.$id.'\').style.display == \'none\'){document.getElementById(\''.$id.'\').style.display = \'block\'}else{document.getElementById(\''.$id.'\').style.display = \'none\'}">'.JText::_('VIEW_DETAILS').'</div>';
											echo '<div id="'.$id.'" style="display:none">';
											foreach($source as $value){
												if(!strpos($value, '::')) continue;
												list($part1, $part2) = explode("::", $value);
												echo '<b>'.$this->escape($part1).' : </b>'.$this->escape($part2).'<br />';
											}
											echo '</div>';
										}
										?>
									</td>
								</tr>
								<?php
								$k = 1 - $k;
							}
							?>
							</tbody>
						</table>
					</div>
					<?php
					echo $tabs->endPanel();
				}

				if(!empty($this->geoloc)){
					echo $tabs->startPanel('<span onclick="setTimeout(function(){chart.draw(data, mapOptions)},100);setTimeout(function(){chart.draw(data, mapOptions)},2000);">'.JText::_('GEOLOCATION').'</span>', 'geoloc');
					?>


					<div>
						<div id="mapGeoloc_div" style="width:900px; max-width:100%; float:left; padding-right:20px;"></div>
						<div style="float:left; min-width:400px; max-width:800px;">
							<table class="acymailing_table">
								<thead>
								<tr>
									<th class="title titledate">
										<?php echo JText::_('FIELD_DATE'); ?>
									</th>
									<th class="title">
										<?php echo JText::_('ACY_ACTION'); ?>
									</th>
									<th class="title">
										<?php echo JText::_('COUNTRYCAPTION'); ?>
									</th>
									<th class="title">
										<?php echo JText::_('STATECAPTION'); ?>
									</th>
									<th class="title">
										<?php echo JText::_('CITYCAPTION'); ?>
									</th>
									<th class="title">
										<?php echo JText::_('IP'); ?>
									</th>
								</tr>
								</thead>
								<tbody>
								<?php
								$k = 0;
								foreach($this->geoloc as $action){
									?>
									<tr class="<?php echo "row$k"; ?>">
										<td align="center" style="text-align:center" valign="top">
											<?php echo acymailing_getDate($action->geolocation_created); ?>
										</td>
										<td valign="top">
											<?php echo $this->escape($action->geolocation_type); ?>
										</td>
										<td valign="top">
											<?php echo $this->escape($action->geolocation_country); ?>
										</td>
										<td valign="top">
											<?php echo $this->escape($action->geolocation_state); ?>
										</td>
										<td valign="top">
											<?php echo $this->escape($action->geolocation_city); ?>
										</td>
										<td valign="top">
											<?php echo $this->escape($action->geolocation_ip); ?>
										</td>
									</tr>
									<?php
									$k = 1 - $k;
								}
								?>
								<tbody>
							</table>
						</div>
						<div style="clear: both"></div>
					</div>
					<?php
					echo $tabs->endPanel();
				}

				if(!empty($this->neighbours)){
					echo $tabs->startPanel(JText::_('ACY_NEIGHBOUR'), 'user_neighbour');
					?>

					<div>
						<table class="acymailing_table">
							<thead>
							<tr>
								<th class="title titlenum">
									<?php echo JText::_('ACY_NUM'); ?>
								</th>
								<th class="title">
									<?php echo JText::_('JOOMEXT_NAME'); ?>
								</th>
								<th class="title">
									<?php echo JText::_('JOOMEXT_EMAIL'); ?>
								</th>
								<th class="title titleid">
									<?php echo JText::_('ACY_ID'); ?>
								</th>
							</tr>
							</thead>
							<tbody>
							<?php
							$k = 0;
							foreach($this->neighbours as $num => $oneNeighbour){
								?>
								<tr class="<?php echo "row$k"; ?>">
									<td align="center" style="text-align:center" valign="top">
										<?php echo($num + 1) ?>
									</td>
									<td valign="top">
										<?php echo $this->escape($oneNeighbour->name); ?>
									</td>
									<td valign="top">
										<?php echo '<a href="index.php?option=com_acymailing&ctrl=subscriber&task=edit&subid='.$oneNeighbour->subid.'" target="_blank">'.$this->escape($oneNeighbour->email).'</a>'; ?>
									</td>
									<td align="center" style="text-align:center" valign="top">
										<?php echo $oneNeighbour->subid; ?>
									</td>
								</tr>
								<?php
								$k = 1 - $k;
							} ?>
							</tbody>
						</table>
					</div>
					<?php
					echo $tabs->endPanel();
				}
				echo $tabs->endPane(); ?>
			</div>
		</div>
		<div class="clr"></div>
	</form>
</div>
