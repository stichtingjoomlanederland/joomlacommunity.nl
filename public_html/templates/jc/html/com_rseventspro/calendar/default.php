<?php
/**
 * @package RSEvents!Pro
 * @copyright (C) 2015 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
$nofollow = $this->params->get('nofollow',0) ? 'rel="nofollow"' : '';
JText::script('COM_RSEVENTSPRO_GLOBAL_FREE'); ?>

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

<form method="post" action="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&view=calendar'); ?>" name="adminForm" id="adminForm" class="form-inline events-calendar">
	<div class="row">
		<div class="col-9 text-left">
			<div class="row">
				<?php if ($this->config->timezone) { ?>
					<a href="#timezoneModal" data-toggle="modal" class="<?php echo rseventsproHelper::tooltipClass(); ?> rsepro-timezone btn pull-left" title="<?php echo rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_CHANGE_TIMEZONE')); ?>">
						<i class="fa fa-clock-o"></i>
					</a>
				<?php } ?>
				
				<div class="form-group">
					<select class="form-control" name="month" id="month" onchange="document.adminForm.submit();">
						<?php echo JHtml::_('select.options', $this->months, 'value', 'text', $this->calendar->cmonth, true); ?>
					</select>
				</div>
				<div class="form-group">
					<select class="form-control" name="year" id="year" onchange="document.adminForm.submit();">
						<?php echo JHtml::_('select.options', $this->years, 'value', 'text', $this->calendar->cyear, true); ?>
					</select>
				</div>
			</div>
		</div>
		<div class="col-3 text-right">
			<div class="row">
				<a rel="nofollow" class="btn btn-default" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&view=calendar&month='.$this->calendar->getPrevMonth().'&year='.$this->calendar->getPrevYear()); ?>">
					&larr; <?php echo JText::_('COM_RSEVENTSPRO_CALENDAR_OLDER'); ?>
				</a>
				<a rel="nofollow" class="btn btn-default" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&view=calendar&month='.$this->calendar->getNextMonth().'&year='.$this->calendar->getNextYear()); ?>">
					<?php echo JText::_('COM_RSEVENTSPRO_CALENDAR_NEWER'); ?> &rarr;
				</a>
			</div>
		</div>
	</div>
	<div id="rseform" class="row rsepro-calendar<?php echo $this->calendar->class_suffix; ?>">
		<table class="table table-bordered">
			<thead>
			<tr>
				<?php if ($this->params->get('week',1) == 1) { ?>
					<th class="week">
						<div class="hidden-lg"><?php echo JText::_('COM_RSEVENTSPRO_CALENDAR_WEEK_SHORT'); ?></div>
						<div class="hidden-xs"><?php echo JText::_('COM_RSEVENTSPRO_CALENDAR_WEEK'); ?></div>
					</th>
				<?php } ?>
				<?php foreach ($this->calendar->days->weekdays as $i => $weekday) { ?>
					<th>
						<?php if (isset($this->calendar->shortweekdays[$i])) { ?><div class="visible-xs"><?php echo $this->calendar->shortweekdays[$i]; ?></div><?php } ?>
						<div class="hidden-xs"><?php echo $weekday; ?></div>
					</th>
				<?php } ?>
			</tr>
			</thead>
			<tbody>
			<?php foreach ($this->calendar->days->days as $day) { ?>
				<?php $unixdate = JFactory::getDate($day->unixdate); ?>
				<?php if ($day->day == $this->calendar->weekstart) { ?>
					<tr>
					<?php if ($this->params->get('week',1) == 1) { ?>
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
						
						<?php if ($this->params->get('details',1) == 1) { ?>
							<ul class="rsepro-calendar-events<?php echo $this->params->get('fullname',0) ? ' rsepro-full-name' : ''; ?>">
								<?php $j = 0; ?>
								<?php $limit = (int) $this->params->get('limit',3); ?>
								<?php $count = count($day->events); ?>
								<?php foreach ($day->events as $event) { ?>
									<?php if ($limit > 0 && $j >= $limit) break; ?>
									<?php $evcolor = $this->getColour($event); ?>
									<?php $full = rseventsproHelper::eventisfull($event); ?>
									<?php $style = empty($evcolor) ? 'border-left: 3px solid #809FFF;' : 'border-left: 3px solid '.$evcolor; ?>
									<?php $style = $this->params->get('colors',0) ? 'style="'.$style.'"' : ''; ?>
									<li class="event" <?php echo $style; ?>>
										<a <?php echo $nofollow; ?> data-toggle="popover" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event,$this->calendar->events[$event]->name),false,rseventsproHelper::itemid($event)); ?>" class="rsttip rse_event_link <?php echo $full ? ' rs_event_full' : ''; ?>" <?php if ($this->params->get('color',0)) { ?> style="color:<?php echo $this->getColour($event); ?>;" <?php } ?> data-content="<?php echo rseventsproHelper::calendarTooltip($event); ?>" title="<?php echo $this->escape($this->calendar->events[$event]->name); ?>">
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
									<a <?php echo $nofollow; ?> href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&view=calendar&layout=day&date='.$unixdate->format('m-d-Y'));?>" class="rsttip" data-content="<?php echo $this->getDetailsSmall($day->events); ?>">
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
	<div id="timezoneModal" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3><?php echo JText::_('COM_RSEVENTSPRO_CHANGE_TIMEZONE'); ?></h3>
		</div>
		<div class="modal-body">
			<form method="post" action="<?php echo htmlentities(JUri::getInstance(), ENT_COMPAT, 'UTF-8'); ?>" id="timezoneForm" name="timezoneForm" class="form-horizontal">
				<div class="control-group">
					<div class="control-label">
						<label><?php echo JText::_('COM_RSEVENTSPRO_DEFAULT_TIMEZONE'); ?></label>
					</div>
					<div class="controls">
						<span class="btn disabled"><?php echo $this->timezone; ?></span>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<label for="timezone"><?php echo JText::_('COM_RSEVENTSPRO_SELECT_TIMEZONE'); ?></label>
					</div>
					<div class="controls">
						<?php echo JHtml::_('rseventspro.timezones','timezone'); ?>
					</div>
				</div>
				<input type="hidden" name="task" value="timezone" />
				<input type="hidden" name="return" value="<?php echo $this->timezoneReturn; ?>" />
			</form>
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></button>
			<button class="btn btn-primary" type="button" onclick="document.timezoneForm.submit();"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_SAVE'); ?></button>
		</div>
	</div>
<?php } ?>

<script type="text/javascript">
	jQuery(document).ready(function(){
		<?php if ($this->params->get('details',1) == 1 && !$this->params->get('fullname',0)) { ?>
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
		
		<?php if ($this->params->get('search',1)) { ?>
		var options = {};
		options.condition = '.rsepro-filter-operator';
		options.events = [{'#rsepro-filter-from' : 'rsepro_select'}];
		
		jQuery().rsjoomlafilter(options);
		<?php } ?>
	});
</script>