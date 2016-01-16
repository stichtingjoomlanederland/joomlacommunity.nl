<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );?>

<?php if ($this->params->get('show_page_heading', 1)) { ?>
<?php $title = $this->params->get('page_heading', ''); ?>
<h1><?php echo !empty($title) ? $this->escape($title) : JText::_('COM_RSEVENTSPRO_CALENDAR'); ?></h1>
<?php } ?>

<form method="post" action="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&view=calendar'); ?>" name="adminForm" id="adminForm">

	<?php if ($this->params->get('search',1)) { ?>
	<div class="rs_search" id="rs_calendar">
		<div class="rs_select_top" id="rs_select_top1">
			<?php echo $this->lists['filter_from']; ?>
		</div>
		
		<div class="rs_select_top" id="rs_select_top2">
			<?php echo $this->lists['filter_condition']; ?>
		</div>
		
		<div class="rs_select_top" style="position: relative;">
			<input type="text" name="search[]" id="rseprosearch" onkeyup="rs_search();" onkeydown="rs_stop();" value="" size="30" autocomplete="off" class="rs_input" />
			<button type="button" onclick="rs_add_filter();" class="rs_search_button"><span id="search_btn"></span></button>
			<ul class="rs_results" id="rs_results"></ul>
		</div>
		
		<div class="rs_clear"></div>
		
		<div class="rs_filter">
			<ul id="rs_filters">
				<?php if (!empty($this->columns)) { ?>
				<?php for ($i=0; $i<count($this->columns); $i++) { ?>
				<li>
					<span><?php echo rseventsproHelper::translate($this->columns[$i]); ?></span>
					<span><?php echo rseventsproHelper::translate($this->operators[$i]); ?></span>
					<strong><?php echo $this->escape($this->values[$i]); ?></strong>
					<a class="rsepro_close" href="javascript: void(0);" onclick="rs_remove_filter(<?php echo $i; ?>)"></a>
					<input type="hidden" name="filter_from[]" value="<?php echo $this->escape($this->columns[$i]); ?>" />
					<input type="hidden" name="filter_condition[]" value="<?php echo $this->escape($this->operators[$i]); ?>" />
					<input type="hidden" name="search[]" value="<?php echo $this->escape($this->values[$i]); ?>" />
				</li>
				<?php } ?>
				<li><a href="javascript:void(0)" onclick="rs_clear_filters();"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CLEAR_FILTER'); ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<?php } else { ?>
	<input type="hidden" name="filter_from[]" id="filter_from" value="" />
	<input type="hidden" name="filter_condition[]" id="filter_condition" value="" />
	<input type="hidden" name="search[]" id="rseprosearch" value="" />
	<?php } ?>

	<div id="rs_calendar_component" class="rs_calendar_component<?php echo $this->calendar->class_suffix; ?>">
		<table cellpadding="0" cellspacing="2" border="0" width="100%" class="rs_table">
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
					<a rel="nofollow" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&view=calendar&month='.$previousMonth.'&year='.$previousYear); ?>" class="rs_calendar_arrows" id="rs_calendar_arrow_left">&laquo;</a>
				</td>
				<td align="center">
					<?php echo $this->lists['month']; ?>
					<?php echo $this->lists['year']; ?>
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
					<a rel="nofollow" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&view=calendar&month='.$nextMonth.'&year='.$nextYear); ?>" class="rs_calendar_arrows" id="rs_calendar_arrow_right">&raquo;</a>
				</td>
			</tr>
		</table>
	
		<table class="rs_calendar_component rs_table" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<?php if ($this->params->get('week',1) == 1) { ?><th width="3%"><?php echo JText::_('COM_RSEVENTSPRO_CALENDAR_WEEK'); ?></th><?php } ?>
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
					<?php if ($this->params->get('week',1) == 1) { ?>
					<td class="week">
						<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&view=calendar&layout=week&date='.$unixdate->formatLikeDate('m-d-Y')); ?>"><?php echo $day->week; ?></a>
					</td>
					<?php } ?>
			<?php } ?>
					<td class="<?php echo $day->class; ?>">
						<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&view=calendar&layout=day&date='.$unixdate->formatLikeDate('m-d-Y'));?>">
							<span class="rs_calendar_date"><?php echo $unixdate->formatLikeDate('j'); ?></span>
						</a>
						<?php if (!empty($day->events) && $this->params->get('details',1) == 0) { ?> 
							<div class="rs_calendar_details">
								<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&view=calendar&layout=day&date='.$unixdate->formatLikeDate('m-d-Y'));?>" class="rsttip" title="<?php echo $this->getDetailsSmall($day->events); ?>">
									<?php echo count($day->events).' '.JText::plural('COM_RSEVENTSPRO_CALENDAR_EVENTS',count($day->events)); ?>
								</a>
							</div>
						<?php } ?>
						<?php if (!empty($day->events) && $this->params->get('details',1) == 1) { ?>
						<?php $j = 0; ?>
						<?php $limit = $this->params->get('limit',3); ?>
						<?php foreach ($day->events as $event) { ?>
						<?php if ($j >= $limit) break; ?>
						<?php $evcolor = $this->getColour($event); ?>
						<?php $full = rseventsproHelper::eventisfull($event); ?>
						<?php $style = empty($evcolor) ? 'border-left: 2px solid #809FFF;' : 'border-left: 2px solid '.$evcolor; ?>
						<?php $style = $this->params->get('colors',0) ? $style : ''; ?>
							<div class="rs_calendar_details <?php if (!$this->params->get('fullname',0)) echo 'rs_calendar_events'; ?>"<?php echo ' style="'.$style.'"' ?>>
								<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event,$this->calendar->events[$event]->name)); ?>" class="rsttip rse_event_link <?php echo $full ? ' rs_event_full' : ''; ?>" <?php if ($this->params->get('color',0)) { ?> style="color:<?php echo $this->getColour($event); ?>;" <?php } ?> title="<?php echo $this->getDetailsBig($this->calendar->events[$event]); ?>">
									<?php echo $this->escape($this->calendar->events[$event]->name); ?>
								</a>
							</div>
						<?php $j++; ?>
						<?php } ?>
						<?php } ?>
					</td>
				<?php if ($day->day == $this->calendar->weekend) { ?></tr><?php } ?>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<div class="rs_clear"></div>
	<br />

	<?php if (!empty($this->legend)) { ?>
	<h3><?php echo JText::_('COM_RSEVENTSPRO_CALENDAR_LEGEND'); ?></h3>
	<?php if ($this->params->get('legendtype',1)) { ?>
	<script type="text/javascript">
		window.addEvent('domready', function() {
			$('legend').fancySelect();
		});
	</script>
	
	<select name="legend" id="legend">
	<?php foreach($this->legend as $category) { ?>
		<?php $selected = $category->id == $this->selected ? 'selected="selected"' : ''; ?>
		<option value="<?php echo $category->id; ?>" data-color="<?php echo $category->color; ?>" <?php echo $selected; ?>><?php echo $category->title; ?></option>
	<?php } ?>
	</select>
	<?php } else { ?>
	<table width="100%" class="rs_table" cellspacing="0" cellpadding="0">
	<?php $i = 0; ?>
	<?php foreach($this->legend as $category) { ?>
	<?php $i++; ?>
	<?php if ($i % 3 == 1) { ?><tr><?php } ?>
		<td width="33%">
			<div class="rsepro_legend_block">
				<span class="rsepro_legend_color" style="background:<?php echo $category->color; ?>;border:2px solid <?php echo $category->color; ?>"></span> 
				<a class="rsepro_legend_text" href="javascript:void(0);" onclick="rs_calendar_add_filter('<?php echo !empty($category->id) ? $this->escape($category->title) : ''; ?>');"><?php echo $category->title; ?></a>
			</div>
		</td>
	<?php if ($i % 3 == 0) { ?></tr><?php } ?>
	<?php } ?>
	</table>
	<?php } ?>
	<?php } ?>

	<input type="hidden" name="rs_clear" id="rs_clear" value="0" />
	<input type="hidden" name="rs_remove" id="rs_remove" value="" />
	<input type="hidden" name="option" value="com_rseventspro" />
	<input type="hidden" name="view" value="calendar" />
</form>

<script type="text/javascript">	
	window.addEvent('domready', function(){
	<?php if ($this->params->get('search',1)) { ?>
		new elSelect( {container : 'rs_select_top1'} );
		new elSelect( {container : 'rs_select_top2'} );
	<?php } ?>
	
	<?php if ($this->params->get('details',1) == 1 && !$this->params->get('fullname',0)) { ?>
	window.event_hovering = 0;
	$$('body').addEvent('mouseover', function(evt){
		if (evt.target) {
			if (!window.event_hovering) {
				$$('#cloneevent').dispose();
				$$('.rs_calendar_events a').set('style', 'visibility:visible');
			}
			
			if (evt.target.get('id') != 'cloneevent' && !evt.target.hasClass('rse_event_link')) {
				window.event_hovering = 0;
			}
		}
	});
	
	$$('.rs_calendar_events a').each(function(el){
		el.addEvent('mouseover', function() {
			window.event_hovering = 1;
		});
		el.addEvent('mouseenter', function() {
			el.set('style','visibility:hidden');
			var eltitle = el.get('title');
			var position = el.getPosition();
			var clone = el.clone();
			el.set('styles', {
				display: 'inline-block'
			});
			clone.set('id','cloneevent');
			clone.set('title',eltitle);
			clone.set('styles', {
				position: 'absolute',
				visibility: 'visible'
			});
			clone.setPosition(position);
			cloneposition = clone.getPosition();
			clone.addEvent('mouseleave', function() {
				window.event_hovering = 0;
				clone.dispose();
				el.set('style','visibility:visible');
				el.setPosition(cloneposition);
			});
			clone.addEvent('mouseover', function() {
				window.event_hovering = 1;
			});
			
			document.id(document.body).adopt(clone);
			new FloatingTips('.rsttip', { position: 'bottom', html: true });
		});
	});
	<?php } else { ?>
	new FloatingTips('.rsttip', { position: 'bottom', html: true });
	<?php } ?>
	});
</script>