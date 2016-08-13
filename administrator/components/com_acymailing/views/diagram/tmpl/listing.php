<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><style type="text/css" xmlns="http://www.w3.org/1999/html">
	table#printstats{
		background-color: white;
		border-collapse: collapse;
	}

	#printstats th, #printstats td{
		border: 1px solid #CCCCCC;
		padding: 4px;
	}

	#printstats thead{
		background-color: #5471B5;
		color: white;
	}

</style>
<div id="acy_content">
	<div id="iframedoc"></div>
	<form action="index.php?option=<?php echo ACYMAILING_COMPONENT ?>&amp;ctrl=diagram" method="post" name="adminForm" id="adminForm">

		<div class="acyblockoptions donotprint" style="width: 40%; min-width: 400px;">
			<span class="acyblocktitle"><?php echo JText::_('DISPLAY'); ?></span>
			<table class="acymailing_table">
				<tr>
					<td class="fieldkey"><?php echo JText::_('CHART_TYPE'); ?></td>
					<td>
						<?php
						$value = (empty($this->display['charttype']) ? 'ColumnChart' : $this->display['charttype']);
						$values = array(JHTML::_('select.option', 'ColumnChart', JText::_('COLUMN_CHART')), JHTML::_('select.option', 'LineChart', JText::_('LINE_CHART')));
						echo JHTML::_('acyselect.radiolist', $values, 'display[charttype]', '', 'value', 'text', $value);
						?>
					</td>
				</tr>
				<tr>
					<td class="fieldkey"><?php echo JText::_('ACY_PERIOD'); ?></td>
					<td>
						<?php echo JText::_('ACY_FROM_DATE').' ';
						echo JHTML::_('calendar', @$this->display['datemin'], 'display[datemin]', 'display_datemin', '%Y-%m-%d', array('style' => 'width:120px'));
						echo '<br/>'.JText::_('ACY_TO_DATE').' ';
						echo JHTML::_('calendar', @$this->display['datemax'], 'display[datemax]', 'display_datemax', '%Y-%m-%d', array('style' => 'width:120px')); ?>
					</td>
				</tr>
				<tr>
					<td class="fieldkey"><?php echo JText::_('ACY_INTERVAL'); ?></td>
					<td>
						<?php
						$value = (empty($this->display['interval']) ? 'month' : $this->display['interval']);
						$values = array(JHTML::_('select.option', 'day', JText::_('ACY_DAY')), JHTML::_('select.option', 'month', JText::_('ACY_MONTH')), JHTML::_('select.option', 'year', JText::_('ACY_YEAR')));
						echo JHTML::_('acyselect.radiolist', $values, 'display[interval]', '', 'value', 'text', $value);
						?>
					</td>
				</tr>
				<tr>
					<td class="fieldkey"><?php echo JText::_('ACY_STATS_ADDUP'); ?></td>
					<td>
						<?php
						$value = (empty($this->display['sumup']) ? '0' : $this->display['sumup']);
						$values = array(JHTML::_('select.option', '0', JText::_('JOOMEXT_NO')), JHTML::_('select.option', '1', JText::_('JOOMEXT_YES')),);
						echo JHTML::_('acyselect.radiolist', $values, 'display[sumup]', '', 'value', 'text', $value);
						?>
					</td>
				</tr>
			</table>
		</div>

		<div class="acyblockoptions donotprint" style="width: 40%; min-width: 400px;">
			<span class="acyblocktitle"><?php echo JText::_('ACY_COMPARE'); ?></span>

			<div><input onclick="document.getElementById('alllists').style.display = 'none'; if(this.checked){document.getElementById('alllists').style.display = 'block'}" <?php if(!empty($this->compares['lists'])) echo 'checked="checked"'; ?> type="checkbox" value="lists" name="compares[lists]" id="compares_lists"/> <label for="compares_lists"><?php echo JText::_('LISTS'); ?></label></div>

			<div class="acyblockoptions" id="alllists" <?php if(empty($this->compares['lists'])) echo 'style="display:none"' ?> >
				<?php
				if(!empty($this->lists)){
					foreach($this->lists as $oneList){
						echo '<span style="display: inline-block;margin-right:15px;"><input type="checkbox" '.(empty($this->filterlists) || !in_array($oneList->listid, $this->filterlists) ? '' : 'checked="checked"').' value="'.$oneList->listid.'" name="filterlists[]" id="list_'.$oneList->listid.'" style="margin:3px;padding:0px;"/><label style="margin:3px;padding:0px;" for="list_'.$oneList->listid.'">'.$oneList->name.'</label></span>';
					}
				}
				?>
			</div>
			<br/>

			<div><input <?php if(!empty($this->compares['years'])) echo 'checked="checked"'; ?> type="checkbox" value="years" name="compares[years]" id="compares_years"/> <label for="compares_years"><?php echo JText::_('ACY_YEARS'); ?></label></div>
		</div>
		<div style="text-align:center; clear: both;" class="donotprint">
			<input type="submit" class="acymailing_button" onclick="document.adminForm.task.value='';" value="<?php echo JText::_('GENERATE_CHART'); ?>"><i class="acyicon-refresh"></i></input>
		</div>
		<input type="hidden" name="option" value="<?php echo ACYMAILING_COMPONENT; ?>"/>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>"/>
	</form>

	<?php if (!empty($this->export)){ ?>
	<div class="printarea" style="margin-top:30px;float: none;">
		<div id="chart" style="width:100%; float: none;"></div>
		<span id="acy_exportchartlegend" class="acymailing_button donotprint" onclick="exportData();"><?php echo JText::_('ACY_EXPORT_CHART'); ?><i class="acyicon-export donotprint" style="cursor:pointer;" alt="<?php echo JText::_('ACY_EXPORT', true) ?>" title="<?php echo JText::_('ACY_EXPORT', true) ?>"></i></span>
		<textarea cols="100" rows="10" id="exporteddata" style="display:none;position:absolute;margin-top:-63px; height: 100px;"><?php echo implode("\n", $this->export); ?></textarea>
		<?php
		$lists = explode(',', $this->export[0]);
		echo '<table id="printstats" class="onlyprint acymailing_table"><thead><tr>';
		for($i = 0; $i < count($lists); $i++){
			echo '<th>'.$lists[$i].'</th>';
		}
		echo '</tr></thead><tbody>';
		foreach($this->export as $exportNumber => $oneExport){
			if($exportNumber == '0') continue;
			$total = explode(',', $this->export[$exportNumber]);
			echo '<tr>';
			for($i = 0; $i < count($total); $i++){
				echo '<td align="center" style="text-align:center" >'.$total[$i].'</td>';
			}
			echo '</tr>';
		}
		echo '</tbody></table>';
		}
		?>
		<br style="clear:both"/>
	</div>
</div>
