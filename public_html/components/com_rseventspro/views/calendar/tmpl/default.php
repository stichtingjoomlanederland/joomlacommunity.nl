<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
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
$showColors		= $this->params->get('colors', 0);
?>

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
	<div class="rsepro-filter-container">
		<div class="navbar" id="rsepro-navbar">
			<div class="navbar-inner">
				<a data-target=".rsepro-navbar-responsive-collapse" data-toggle="collapse" class="btn btn-navbar collapsed">
					<i class="icon-bar"></i>
					<i class="icon-bar"></i>
					<i class="icon-bar"></i>
				</a>
				<div class="nav-collapse collapse rsepro-navbar-responsive-collapse">
					<ul class="nav">
						<li id="rsepro-filter-from" class="dropdown">
							<a data-toggle="dropdown" class="dropdown-toggle" href="#" rel="<?php echo $this->config->filter_from; ?>"><span><?php echo rseventsproHelper::getFilterText($this->config->filter_from); ?></span> <i class="caret"></i></a>
							<ul class="dropdown-menu">
								<?php foreach ($this->get('filteroptions') as $option) { ?>
								<?php if (!$this->maxPrice && $option->value == 'price') continue; ?>
								<li><a href="javascript:void(0);" rel="<?php echo $option->value; ?>"><?php echo $option->text; ?></a></li>
								<?php } ?>
							</ul>
						</li>
						<li id="rsepro-filter-condition" class="dropdown">
							<a data-toggle="dropdown" class="dropdown-toggle" href="#" rel="<?php echo $this->config->filter_condition; ?>"><span><?php echo rseventsproHelper::getFilterText($this->config->filter_condition); ?></span> <i class="caret"></i></a>
							<ul class="dropdown-menu">
								<?php foreach ($this->get('filterconditions') as $option) { ?>
								<li><a href="javascript:void(0);" rel="<?php echo $option->value; ?>"><?php echo $option->text; ?></a></li>
								<?php } ?>
							</ul>
						</li>
						<li id="rsepro-search" class="navbar-search center">
							<input type="text" id="rsepro-filter" name="rsepro-filter" value="" size="35" />
						</li>
						<li id="rsepro-filter-featured" class="dropdown" style="display: none;">
							<a data-toggle="dropdown" class="dropdown-toggle" href="#" rel="1"><span><?php echo JText::_('JYES'); ?></span> <i class="caret"></i></a>
							<ul class="dropdown-menu">
								<li><a href="javascript:void(0);" rel="1"><?php echo JText::_('JYES'); ?></a></li>
								<li><a href="javascript:void(0);" rel="0"><?php echo JText::_('JNO'); ?></a></li>
							</ul>
						</li>
						<?php if ($this->maxPrice) { ?>
						<li id="rsepro-filter-price" class="dropdown" style="display: none;">
							<span id="price-field-min" class="label rsepro-min-price"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_FREE'); ?></span>
							<input id="price-field" type="text" value="" data-slider-min="0" data-slider-max="<?php echo $this->maxPrice; ?>" data-slider-step="1" data-slider-value="[0,<?php echo $this->maxPrice; ?>]" />
							<span id="price-field-max" class="label rsepro-max-price"><?php echo rseventsproHelper::currency($this->maxPrice, false, 0); ?></span> 
						</li>
						<?php } ?>
						<li class="divider-vertical"></li>
						<li class="center">
							<div class="btn-group">
								<button id="rsepro-filter-btn" type="button" class="btn btn-primary"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_ADD_FILTER'); ?></button>
								<button id="rsepro-clear-btn" type="button" class="btn"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CLEAR_FILTER'); ?></button>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
		
		<ul class="rsepro-filter-filters inline unstyled">
			<li class="rsepro-filter-operator" <?php echo $this->showCondition > 1 ? '' : 'style="display:none"'; ?>>
				<div class="btn-group">
					<a data-toggle="dropdown" class="btn btn-small dropdown-toggle" href="#"><span><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_'.$this->operator)); ?></span> <i class="caret"></i></a>
					<ul class="dropdown-menu">
						<li><a href="javascript:void(0)" rel="AND"><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_AND')); ?></a></li>
						<li><a href="javascript:void(0)" rel="OR"><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_OR')); ?></a></li>
					</ul>
				</div>
				<input type="hidden" name="filter_operator" value="<?php echo $this->operator; ?>" />
			</li>
			
			<?php if (!is_null($price = $this->extra['price'])) { ?>
				<li id="<?php echo sha1('price'); ?>">
					<?php list($min, $max) = explode(',',$price,2); ?>
					<div class="btn-group">
						<span class="btn btn-small"><?php echo JText::_('COM_RSEVENTSPRO_FILTER_PRICE'); ?></span>
						<span class="btn btn-small"><?php echo ($min == 0 ? JText::_('COM_RSEVENTSPRO_GLOBAL_FREE') : rseventsproHelper::currency($min, false, 0)).' - '.rseventsproHelper::currency($max, false, 0); ?></span>
						<input type="hidden" name="filter_price[]" value="<?php echo $this->escape($price); ?>">
						<a href="javascript:void(0)" class="btn btn-small rsepro-close">
							<i class="icon-delete"></i>
						</a>
					</div>
				</li>
				
				<li class="rsepro-filter-conditions" <?php echo $this->showCondition > 1 ? '' : 'style="display: none;"'; ?>>
					<a class="btn btn-small"><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_'.$this->operator));?></a>
				</li>
			<?php } ?>
			
			<?php if (!is_null($featured = $this->extra['featured'])) { ?>
				<li id="<?php echo sha1('featured'); ?>">
					<div class="btn-group">
						<span class="btn btn-small"><?php echo JText::_('COM_RSEVENTSPRO_FILTER_FEATURED'); ?></span>
						<span class="btn btn-small"><?php echo $featured == 0 ? JText::_('JNO') : JText::_('JYES'); ?></span>
						<input type="hidden" name="filter_featured[]" value="<?php echo $this->escape($featured); ?>">
						<a href="javascript:void(0)" class="btn btn-small rsepro-close">
							<i class="icon-delete"></i>
						</a>
					</div>
				</li>
				
				<li class="rsepro-filter-conditions" <?php echo $this->showCondition > 1 ? '' : 'style="display: none;"'; ?>>
					<a class="btn btn-small"><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_'.$this->operator));?></a>
				</li>
			<?php } ?>
			
			<?php if (!empty($this->columns)) { ?>
			<?php for ($i=0; $i < count($this->columns); $i++) { ?>
				<?php $hash = sha1(@$this->columns[$i].@$this->operators[$i].@$this->values[$i]); ?>
				<li id="<?php echo $hash; ?>">
					<div class="btn-group">
						<span class="btn btn-small"><?php echo rseventsproHelper::translate($this->columns[$i]); ?></span>
						<span class="btn btn-small"><?php echo rseventsproHelper::translate($this->operators[$i]); ?></span>
						<span class="btn btn-small"><?php echo $this->escape($this->values[$i]); ?></span>
						<input type="hidden" name="filter_from[]" value="<?php echo $this->escape($this->columns[$i]); ?>">
						<input type="hidden" name="filter_condition[]" value="<?php echo $this->escape($this->operators[$i]); ?>">
						<input type="hidden" name="search[]" value="<?php echo $this->escape($this->values[$i]); ?>">
						<a href="javascript:void(0)" class="btn btn-small rsepro-close">
							<i class="icon-delete"></i>
						</a>
					</div>
				</li>
				
				<li class="rsepro-filter-conditions" <?php echo $i == (count($this->columns) - 1) ? 'style="display: none;"' : ''; ?>>
					<a class="btn btn-small"><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_'.$this->operator));?></a>
				</li>
				
			<?php } ?>
			<?php } ?>
		</ul>
		
		<input type="hidden" name="filter_from[]" value="">
		<input type="hidden" name="filter_condition[]" value="">
		<input type="hidden" name="search[]" value="">
		<input type="hidden" name="filter_featured[]" value="">
		<input type="hidden" name="filter_price[]" value="">
	</div>
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
				<div class="row-fluid">
					<?php if ($this->config->timezone) { ?>
					<a href="#timezoneModal" data-toggle="modal" class="<?php echo rseventsproHelper::tooltipClass(); ?> rsepro-timezone btn pull-left" title="<?php echo rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_CHANGE_TIMEZONE')); ?>">
						<i class="fa fa-clock-o"></i>
					</a>
					<?php } ?>
					<select class="input-medium pull-left" name="month" id="month" onchange="document.adminForm.submit();">
						<?php echo JHtml::_('select.options', $this->months, 'value', 'text', $this->calendar->cmonth); ?>
					</select>
					<select class="input-small pull-left" name="year" id="year" onchange="document.adminForm.submit();">
						<?php echo JHtml::_('select.options', $this->years, 'value', 'text', $this->calendar->cyear); ?>
					</select>
					<ul class="pager pull-right">
						<li>
							<a rel="nofollow" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&view=calendar&month='.$this->calendar->getPrevMonth().'&year='.$this->calendar->getPrevYear()); ?>">
								&larr; <?php echo JText::_('COM_RSEVENTSPRO_CALENDAR_OLDER'); ?>
							</a>
						</li>
						<li>
							<a rel="nofollow" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&view=calendar&month='.$this->calendar->getNextMonth().'&year='.$this->calendar->getNextYear()); ?>">
								<?php echo JText::_('COM_RSEVENTSPRO_CALENDAR_NEWER'); ?> &rarr;
							</a>
						</li>
					</ul>
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
								?>
									<li class="event" <?php echo $style; ?>>
										<a <?php echo $nofollow; ?> href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event,$this->calendar->events[$event]->name),false,rseventsproHelper::itemid($event)); ?>" class="rsttip rse_event_link <?php echo $full ? ' rs_event_full' : ''; ?>" data-content="<?php echo rseventsproHelper::calendarTooltip($event); ?>" title="<?php echo $this->escape($this->calendar->events[$event]->name); ?>">
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