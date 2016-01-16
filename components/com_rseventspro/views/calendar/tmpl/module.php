<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );?>
<?php echo 'RS_DELIMITER0'; ?>
<table cellpadding="0" cellspacing="2" border="0" width="100%" class="rs_table" style="width:100%;">
	<tr>
		<td align="left">
			<a rel="nofollow" href="javascript:void(0);" onclick="rs_calendar('<?php echo JURI::root(true); ?>/','<?php echo $this->calendar->getPrevMonth(); ?>','<?php echo $this->calendar->getPrevYear(); ?>','<?php echo $this->module; ?>')" class="rs_calendar_arrows_module" id="rs_calendar_arrow_left_module">&laquo;</a>
		</td>
		<td align="center">
			<?php $current = JFactory::getDate($this->calendar->unixdate); ?>
			<?php if (substr($this->calendar->cmonth,0,1) == 0) $this->calendar->cmonth = str_replace('0','',$this->calendar->cmonth); ?>
			<span id="rscalendarmonth<?php echo $this->module; ?>"><?php echo $this->calendar->months[$this->calendar->cmonth].' '.$current->format('Y'); ?></span>
			<img id="rscalendar<?php echo $this->module; ?>" src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/loader.gif" alt="" style="vertical-align:middle; display:none;" />
		</td>
		<td align="right">
			<a rel="nofollow" href="javascript:void(0);" onclick="rs_calendar('<?php echo JURI::root(true); ?>/','<?php echo $this->calendar->getNextMonth(); ?>','<?php echo $this->calendar->getNextYear(); ?>','<?php echo $this->module; ?>')" class="rs_calendar_arrows_module" id="rs_calendar_arrow_right_module">&raquo;</a>
		</td>
	</tr>
</table>

<div class="rs_clear"></div>

<table class="rs_calendar_module rs_table" cellpadding="0" cellspacing="0" width="100%">
	<thead>
		<tr>
			<?php foreach ($this->calendar->days->weekdays as $weekday) { ?>
			<th><?php echo $weekday; ?></th>
			<?php } ?>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($this->calendar->days->days as $day) { ?>
	<?php $unixdate = JFactory::getDate($day->unixdate); ?>
	<?php if ($day->day == $this->calendar->weekstart) { ?>
		<tr>
	<?php } ?>
			<td class="<?php echo $day->class; ?>">
				<a <?php echo $this->nofollow; ?> href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&view=calendar&layout=day&date='.$unixdate->format('m-d-Y').'&mid='.$this->module,true,$this->itemid);?>" class="<?php echo rseventsproHelper::tooltipClass(); ?>" title="<?php echo rseventsproHelper::tooltipText(modRseventsProCalendar::getDetailsSmall($day->events)); ?>">
					<span class="rs_calendar_date"><?php echo $unixdate->format('j'); ?></span>
				</a>
			</td>
		<?php if ($day->day == $this->calendar->weekend) { ?></tr><?php } ?>
		<?php } ?>
	</tbody>
</table>
<?php echo 'RS_DELIMITER1'; ?>