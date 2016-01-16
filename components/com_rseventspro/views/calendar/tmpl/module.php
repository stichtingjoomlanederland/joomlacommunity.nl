<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );?>
<?php echo 'RS_DELIMITER0'; ?>
<table cellpadding="0" cellspacing="2" border="0" width="100%" class="rs_table" style="width:100%;">
	<tr>
		<td align="left">
			<?php 
				$previousMonth = rseventsproHelper::date($this->calendar->unixdate,null,false,true);
				$previousMonth->setTZByID($previousMonth->getTZID());
				$previousMonth->convertTZ(new RSDate_Timezone('GMT'));
				$previousMonth->addMonths(-1);
				$previousMonth = $previousMonth->formatLikeDate('m');
				
				$previousYear = rseventsproHelper::date($this->calendar->unixdate,null,false,true);
				$previousYear->setTZByID($previousYear->getTZID());
				$previousYear->convertTZ(new RSDate_Timezone('GMT'));
				$previousYear->addMonths(-1);
				$previousYear = $previousYear->formatLikeDate('Y');
			?>
			<a rel="nofollow" href="javascript:void(0);" onclick="rs_calendar('<?php echo JURI::root(); ?>','<?php echo $previousMonth; ?>','<?php echo $previousYear; ?>','<?php echo $this->module; ?>')" class="rs_calendar_arrows_module" id="rs_calendar_arrow_left_module">&laquo;</a>
		</td>
		<td align="center">
			<?php $current = rseventsproHelper::date($this->calendar->unixdate,null,false,true); ?>
			<?php $current->setTZByID($current->getTZID()); ?>
			<?php $current->convertTZ(new RSDate_Timezone('GMT')); ?>
			<?php if (substr($this->calendar->cmonth,0,1) == 0) $this->calendar->cmonth = str_replace('0','',$this->calendar->cmonth); ?>
			<span id="rscalendarmonth<?php echo $this->module; ?>"><?php echo $this->calendar->months[$this->calendar->cmonth].' '.$current->formatLikeDate('Y'); ?></span>
			<img id="rscalendar<?php echo $this->module; ?>" src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/loader.gif" alt="" style="vertical-align:middle; display:none;" />
		</td>
		<td align="right">
			<?php 
				$nextMonth = rseventsproHelper::date($this->calendar->unixdate,null,false,true);
				$nextMonth->setTZByID($nextMonth->getTZID());
				$nextMonth->convertTZ(new RSDate_Timezone('GMT'));
				$nextMonth->addMonths(1);
				$nextMonth = $nextMonth->formatLikeDate('m');
				
				$nextYear = rseventsproHelper::date($this->calendar->unixdate,null,false,true);
				$nextYear->setTZByID($nextYear->getTZID());
				$nextYear->convertTZ(new RSDate_Timezone('GMT'));
				$nextYear->addMonths(1);
				$nextYear = $nextYear->formatLikeDate('Y');
			?>
			<a rel="nofollow" href="javascript:void(0);" onclick="rs_calendar('<?php echo JURI::root(); ?>','<?php echo $nextMonth; ?>','<?php echo $nextYear; ?>','<?php echo $this->module; ?>')" class="rs_calendar_arrows_module" id="rs_calendar_arrow_right_module">&raquo;</a>
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
	<?php $unixdate = rseventsproHelper::date($day->unixdate,null,false,true); ?>
	<?php $unixdate->setTZByID($unixdate->getTZID()); ?>
	<?php $unixdate->convertTZ(new RSDate_Timezone('GMT')); ?>
	<?php if ($day->day == $this->calendar->weekstart) { ?>
		<tr>
	<?php } ?>
			<td class="<?php echo $day->class; ?>">
				<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&view=calendar&layout=day&date='.$unixdate->formatLikeDate('m-d-Y').'&mid='.$this->module,true,$this->itemid);?>" class="hasTip" title="<?php echo modRseventsProCalendar::getDetailsSmall($day->events); ?>">
					<span class="rs_calendar_date"><?php echo $unixdate->formatLikeDate('j'); ?></span>
				</a>
			</td>
		<?php if ($day->day == $this->calendar->weekend) { ?></tr><?php } ?>
		<?php } ?>
	</tbody>
</table>
<?php echo 'RS_DELIMITER1'; ?>