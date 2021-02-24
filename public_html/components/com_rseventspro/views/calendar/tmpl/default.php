<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
$nofollow = $this->params->get('nofollow',0) ? 'rel="nofollow"' : ''; 
JText::script('COM_RSEVENTSPRO_GLOBAL_FREE');

$showWeek		= $this->params->get('week', 1) == 1;
$showDetails	= $this->params->get('details', 1) == 1;
$showFullName	= $this->params->get('fullname', 0);
$limit			= (int) $this->params->get('limit', 3);
$showSearch		= $this->params->get('search', 1);
$showColors		= $this->params->get('colors', 0); ?>

<script type="text/javascript">
	var rseproMask 		= '<?php echo $this->escape($this->mask); ?>';
	var rseproCurrency  = '<?php echo $this->escape($this->currency); ?>';
	var rseproDecimals	= '<?php echo $this->escape($this->decimals); ?>';
	var rseproDecimal 	= '<?php echo $this->escape($this->decimal); ?>';
	var rseproThousands	= '<?php echo $this->escape($this->thousands); ?>';
</script>

<?php if ($this->params->get('show_page_heading', 1)) { ?>
<?php $title = $this->params->get('page_heading', ''); ?>
<h1><?php echo !empty($title) ? $this->escape($title) : JText::_('COM_RSEVENTSPRO_CALENDAR'); ?></h1>
<?php } ?>

<form method="post" action="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&view=calendar'); ?>" name="adminForm" id="adminForm">

	<?php if ($showSearch) { ?>
	<?php echo JLayoutHelper::render('rseventspro.filter_'.(rseventsproHelper::isJ4() ? 'j4' : 'j3'), array('view' => $this)); ?>
	<?php } else { ?>
	<input type="hidden" name="filter_from[]" id="filter_from" value="" />
	<input type="hidden" name="filter_condition[]" id="filter_condition" value="" />
	<input type="hidden" name="search[]" id="rseprosearch" value="" />
	<input type="hidden" name="filter_featured[]" value="">
	<input type="hidden" name="filter_price[]" value="">
	<?php } ?>
	
	<div id="rseform" class="rsepro-calendar<?php echo $this->calendar->class_suffix; ?>">
		<table class="table table-bordered">
			<caption>
				<div class="<?php echo RSEventsproAdapterGrid::styles(array('pull-left')); ?>">
					<?php if ($this->config->timezone) { ?>
					<a rel="rs_timezone" <?php if (rseventsproHelper::getConfig('modaltype','int') == 1) echo ' href="#timezoneModal" data-toggle="modal" data-bs-toggle="modal"'; else echo ' href="javascript:void(0)"'; ?> class="<?php echo rseventsproHelper::tooltipClass(); ?> rsepro-timezone <?php echo RSEventsproAdapterGrid::styles(array('btn')); ?>" title="<?php echo rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_CHANGE_TIMEZONE')); ?>">
						<i class="fa fa-clock-o"></i>
					</a>
					<?php } ?>
					
					<select class="custom-select" name="month" id="month" onchange="document.adminForm.submit();">
						<?php echo JHtml::_('select.options', $this->months, 'value', 'text', $this->calendar->cmonth); ?>
					</select>
					
					<select class="custom-select" name="year" id="year" onchange="document.adminForm.submit();">
						<?php echo JHtml::_('select.options', $this->years, 'value', 'text', $this->calendar->cyear); ?>
					</select>
				</div>
				
				<div class="btn-group <?php echo RSEventsproAdapterGrid::styles(array('pull-right')); ?>">
					<a rel="nofollow" class="<?php echo RSEventsproAdapterGrid::styles(array('btn')); ?>" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&view=calendar&month='.$this->calendar->getPrevMonth().'&year='.$this->calendar->getPrevYear()); ?>">
						&larr; <?php echo JText::_('COM_RSEVENTSPRO_CALENDAR_OLDER'); ?>
					</a>
					<a rel="nofollow" class="<?php echo RSEventsproAdapterGrid::styles(array('btn')); ?>" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&view=calendar&month='.$this->calendar->getNextMonth().'&year='.$this->calendar->getNextYear()); ?>">
						<?php echo JText::_('COM_RSEVENTSPRO_CALENDAR_NEWER'); ?> &rarr;
					</a>
				</div>
			</caption>
			<thead>
				<tr>
					<?php if ($showWeek) { ?>
					<th class="week">
						<div class="hidden-desktop hidden-tablet"><?php echo JText::_('COM_RSEVENTSPRO_CALENDAR_WEEK_SHORT'); ?></div>
						<div class="hidden-phone"><?php echo JText::_('COM_RSEVENTSPRO_CALENDAR_WEEK'); ?></div>
					</th>
					<?php } ?>
					<?php foreach ($this->calendar->days->weekdays as $i => $weekday) { ?>
					<th>
						<?php if (isset($this->calendar->shortweekdays[$i])) { ?><div class="hidden-desktop hidden-tablet"><?php echo $this->calendar->shortweekdays[$i]; ?></div><?php } ?>
						<div class="hidden-phone"><?php echo $weekday; ?></div>
					</th>
					<?php } ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->calendar->days->days as $day) { ?>
				<?php $unixdate = JFactory::getDate($day->unixdate); ?>
				<?php if ($day->day == $this->calendar->weekstart) { ?>
					<tr>
						<?php if ($showWeek) { ?>
						<td class="week">
							<a <?php echo $nofollow; ?> href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&view=calendar&layout=week&date='.$unixdate->format('m-d-Y')); ?>"><?php echo $day->week; ?></a>
						</td>
						<?php } ?>
				<?php } ?>
						<td class="<?php echo $day->class; ?>">
							<div class="rsepro-calendar-day">
								<a <?php echo $nofollow; ?> href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&view=calendar&layout=day&date='.$unixdate->format('m-d-Y'));?>">
									<?php echo $unixdate->format('j'); ?>
								</a>
								
								<?php if ($this->admin || $this->permissions['can_post_events']) { ?>
								<a <?php echo $nofollow; ?> class="rsepro-add-event" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=edit&date='.$unixdate->format('Y-m-d'));?>">
									<i class="fa fa-plus"></i>
								</a>
								<?php } ?>
							</div>
							
							<?php if (!empty($day->events)) { ?>
							
							<?php if ($showDetails) { ?>
								<ul class="rsepro-calendar-events<?php echo $showFullName ? ' rsepro-full-name' : ''; ?>">
								<?php
									$j = 0;
									$count = count($day->events);
									foreach ($day->events as $event) {
										if ($limit > 0 && $j >= $limit) break;
										$style = '';
										if ($showColors) {
											$evcolor = $this->getColour($event);
											$style = empty($evcolor) ? 'style="border-left: 3px solid #809FFF;"' : 'style="border-left: 3px solid '.$evcolor.'"';
										}
										
										$full = rseventsproHelper::eventisfull($event);
										$canceled = $this->calendar->events[$event]->published == 3;
								?>
									<li class="event" <?php echo $style; ?>>
										<a <?php echo $nofollow; ?> href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event,$this->calendar->events[$event]->name),false,rseventsproHelper::itemid($event)); ?>" class="rsttip rse_event_link <?php echo $full ? 'rs_event_full' : ''; ?> <?php echo $canceled ? 'rsepro_canceled_event_cal' : ''; ?>" data<?php echo rseventsproHelper::isJ4() ? '-bs' : ''; ?>-content="<?php echo rseventsproHelper::calendarTooltip($event); ?>" title="<?php echo $this->escape($this->calendar->events[$event]->name.($canceled ? ' <small class="text-error">('.JText::_('COM_RSEVENTSPRO_EVENT_CANCELED_TEXT').')</small>' : '')); ?>">
											<i class="fa fa-calendar"></i>
											<span class="event-name"><?php echo $this->escape($this->calendar->events[$event]->name); ?></span>
										</a>
									</li>
								<?php $j++; ?>
								<?php } ?>
								<?php if ($count > $limit) { ?>
								<li class="day-events">
									<a <?php echo $nofollow; ?> href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&view=calendar&layout=day&date='.$unixdate->format('m-d-Y')); ?>">
										<?php echo JText::_('COM_RSEVENTSPRO_CALENDAR_VIEW_MORE'); ?>
									</a>
								</li>
								<?php } ?>
								</ul>
							<?php } else { ?>
							
								<ul class="rsepro-calendar-events">
									<li class="event">
										<a <?php echo $nofollow; ?> href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&view=calendar&layout=day&date='.$unixdate->format('m-d-Y'));?>" class="rsttip" data<?php echo rseventsproHelper::isJ4() ? '-bs' : ''; ?>-content="<?php echo $this->getDetailsSmall($day->events); ?>">
											<i class="fa fa-calendar"></i> 
											<?php echo count($day->events).' '.JText::plural('COM_RSEVENTSPRO_CALENDAR_EVENTS',count($day->events)); ?>
										</a>
									</li>
								</ul>
							
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

	<?php echo $this->loadTemplate('legend'); ?>

	<input type="hidden" name="rs_clear" id="rs_clear" value="0" />
	<input type="hidden" name="rs_remove" id="rs_remove" value="" />
	<input type="hidden" name="option" value="com_rseventspro" />
	<input type="hidden" name="view" value="calendar" />
</form>

<?php if ($this->config->timezone) { ?>
<?php echo rseventsproHelper::timezoneModal(); ?>
<?php } ?>

<script type="text/javascript">
	jQuery(document).ready(function(){
		<?php if ($showDetails && !$showFullName) { ?>
		jQuery('.rsepro-calendar-events a').each(function() {
			var elem = jQuery(this);
			elem.on({
				mouseenter: function() {
					elem.addClass('rsepro-active');
				},
				mouseleave: function() {
					elem.removeClass('rsepro-active');
				}
			});
		});
		<?php } ?>
		jQuery('.rsttip').popover({trigger: 'hover', animation: false, html : true, placement : 'bottom' });
		
		<?php if ($showSearch) { ?>
		var options = {};
		options.condition = '.rsepro-filter-operator';
		options.events = [{'#rsepro-filter-from' : 'rsepro_select'}];
		
		jQuery().rsjoomlafilter(options);	
		<?php } ?>
	});
</script>