<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
$count = count($this->events); ?>

<h1><?php echo JText::sprintf('COM_RSEVENTSPRO_EVENTS_FROM',$this->date); ?></h1>

<div class="clearfix">
	<a href="<?php echo $this->prev; ?>" class="btn pull-left"><i class="fa fa-chevron-left"></i> <?php echo JText::_('COM_RSEVENTSPRO_PREVIOUS_DAY'); ?></a>
	<a href="<?php echo $this->next; ?>" class="btn pull-right"><?php echo JText::_('COM_RSEVENTSPRO_NEXT_DAY'); ?> <i class="fa fa-chevron-right"></i></a>
</div>

<?php $rss = $this->params->get('rss',1); ?>
<?php $ical = $this->params->get('ical',1); ?>
<?php if ($rss || $ical || $this->config->timezone) { ?>
<div class="rs_rss">
	<?php if ($this->config->timezone) { ?>
	<a href="#timezoneModal" data-toggle="modal" class="<?php echo rseventsproHelper::tooltipClass(); ?> rsepro-timezone" title="<?php echo rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_CHANGE_TIMEZONE')); ?>">
		<i class="fa fa-clock-o"></i>
	</a>
	<?php } ?>
	
	<?php if ($rss) { ?>
	<a href="<?php echo $this->rss; ?>" class="<?php echo rseventsproHelper::tooltipClass(); ?> rsepro-rss" title="<?php echo rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_RSS')); ?>">
		<i class="fa fa-rss-square"></i>
	</a>
	<?php } ?>
	<?php if ($ical) { ?>
	<a href="<?php echo $this->ical; ?>" class="<?php echo rseventsproHelper::tooltipClass(); ?> rsepro-ical" title="<?php echo rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_ICS')); ?>">
		<i class="fa fa-calendar"></i>
	</a>
	<?php } ?>
</div>
<?php } ?>

<?php if (!empty($this->events)) { ?>
<ul class="rs_events_container" id="rs_events_container">

	<?php $eventIds = rseventsproHelper::getEventIds($this->events, 'id'); ?>
	<?php $this->events = rseventsproHelper::details($eventIds); ?>
	<?php foreach($this->events as $details) { ?>
	<?php if (isset($details['event']) && !empty($details['event'])) $event = $details['event']; else continue; ?>
	<?php $full = rseventsproHelper::eventisfull($event->id); ?>
	<?php $ongoing = rseventsproHelper::ongoing($event->id); ?>
	<?php $categories = (isset($details['categories']) && !empty($details['categories'])) ? JText::_('COM_RSEVENTSPRO_GLOBAL_CATEGORIES').': '.$details['categories'] : '';  ?>
	<?php $tags = (isset($details['tags']) && !empty($details['tags'])) ? JText::_('COM_RSEVENTSPRO_GLOBAL_TAGS').': '.$details['tags'] : '';  ?>
	<?php $incomplete = !$event->completed ? ' rs_incomplete' : ''; ?>
	<?php $featured = $event->featured ? ' rs_featured' : ''; ?>
	
	<li class="rs_event_detail<?php echo $incomplete.$featured; ?>" id="rs_event<?php echo $event->id; ?>" itemscope itemtype="http://schema.org/Event">
	
		<div class="rs_options" style="display:none;">
			<?php if ((!empty($this->permissions['can_edit_events']) || $event->owner == $this->user || $event->sid == $this->user || $this->admin) && !empty($this->user)) { ?>
				<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=edit&id='.rseventsproHelper::sef($event->id,$event->name)); ?>">
					<i class="fa fa-pencil"></i>
				</a>
			<?php } ?>
			
			<?php if ((!empty($this->permissions['can_delete_events']) || $event->owner == $this->user || $event->sid == $this->user || $this->admin) && !empty($this->user)) { ?>
				<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.remove&id='.rseventsproHelper::sef($event->id,$event->name)); ?>" onclick="return confirm('<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_DELETE_CONFIRMATION'); ?>');">
					<i class="fa fa-trash"></i>
				</a>
			<?php } ?>
		</div>
		
		<?php if (!empty($event->options['show_icon_list'])) { ?>
		<div class="rs_event_image" itemprop="image">
			<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name),false,rseventsproHelper::itemid($event->id)); ?>" class="rs_event_link thumbnail">
				<img src="<?php echo JRoute::_('index.php?option=com_rseventspro&task=image&id='.rseventsproHelper::sef($event->id,$event->name), false); ?>" alt="" width="<?php echo $this->config->icon_small_width; ?>" />
			</a>
		</div>
		<?php } ?>
		
		<div class="rs_event_details">
			<div itemprop="name">
				<a itemprop="url" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name),false,rseventsproHelper::itemid($event->id)); ?>" class="rs_event_link<?php echo $full ? ' rs_event_full' : ''; ?><?php echo $ongoing ? ' rs_event_ongoing' : ''; ?>"><?php echo $event->name; ?></a> <?php if (!$event->completed) echo JText::_('COM_RSEVENTSPRO_GLOBAL_INCOMPLETE_EVENT'); ?> <?php if (!$event->published) echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNPUBLISHED_EVENT'); ?>
			</div>
			<div>
				<?php if ($event->allday) { ?>
				<?php if (!empty($event->options['start_date_list'])) { ?>
				<span class="rsepro-event-on-block">
				<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_ON'); ?> <b><?php echo rseventsproHelper::showdate($event->start,$this->config->global_date,true); ?></b>
				</span>
				<?php } ?>
				<?php } else { ?>
				
				<?php if (!empty($event->options['start_date_list']) || !empty($event->options['start_time_list']) || !empty($event->options['end_date_list']) || !empty($event->options['end_time_list'])) { ?>
				<?php if (!empty($event->options['start_date_list']) || !empty($event->options['start_time_list'])) { ?>
				<?php if ((!empty($event->options['start_date_list']) || !empty($event->options['start_time_list'])) && empty($event->options['end_date_list']) && empty($event->options['end_time_list'])) { ?>
				<span class="rsepro-event-starting-block">
				<?php echo JText::_('COM_RSEVENTSPRO_EVENT_STARTING_ON'); ?>
				<?php } else { ?>
				<span class="rsepro-event-from-block">
				<?php echo JText::_('COM_RSEVENTSPRO_EVENT_FROM'); ?> 
				<?php } ?>
				<b><?php echo rseventsproHelper::showdate($event->start,rseventsproHelper::showMask('list_start',$event->options),true); ?></b>
				</span>
				<?php } ?>
				<?php if (!empty($event->options['end_date_list']) || !empty($event->options['end_time_list'])) { ?>
				<?php if ((!empty($event->options['end_date_list']) || !empty($event->options['end_time_list'])) && empty($event->options['start_date_list']) && empty($event->options['start_time_list'])) { ?>
				<span class="rsepro-event-ending-block">
				<?php echo JText::_('COM_RSEVENTSPRO_EVENT_ENDING_ON'); ?>
				<?php } else { ?>
				<span class="rsepro-event-until-block">
				<?php echo JText::_('COM_RSEVENTSPRO_EVENT_UNTIL'); ?>
				<?php } ?>
				<b><?php echo rseventsproHelper::showdate($event->end,rseventsproHelper::showMask('list_end',$event->options),true); ?></b>
				</span>
				<?php } ?>
				<?php } ?>
				
				<?php } ?>
			</div>
			
			<?php if (!empty($event->options['show_location_list']) || !empty($event->options['show_categories_list']) || !empty($event->options['show_tags_list'])) { ?>
			<div>
				<?php if ($event->locationid && $event->lpublished && !empty($event->options['show_location_list'])) { ?>
				<span class="rsepro-event-location-block" itemprop="location" itemscope itemtype="http://schema.org/Place"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_AT'); ?> <a itemprop="url" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=location&id='.rseventsproHelper::sef($event->locationid,$event->location)); ?>"><span itemprop="name"><?php echo $event->location; ?></span></a>
				<span itemprop="address" style="display:none;"><?php echo $event->address; ?></span>
				</span> 
				<?php } ?>
				<?php if (!empty($event->options['show_categories_list'])) { ?>
				<span class="rsepro-event-categories-block"><?php echo $categories; ?></span> 
				<?php } ?>
				<?php if (!empty($event->options['show_tags_list'])) { ?>
				<span class="rsepro-event-tags-block"><?php echo $tags; ?></span> 
				<?php } ?>
			</div>
			<?php } ?>
		</div>
		
		<meta content="<?php echo rseventsproHelper::showdate($event->start,'Y-m-d H:i:s'); ?>" itemprop="startDate" />
		<?php if (!$event->allday) { ?><meta content="<?php echo rseventsproHelper::showdate($event->end,'Y-m-d H:i:s'); ?>" itemprop="endDate" /><?php } ?>
	</li>
	<?php } ?>
</ul>

<div class="rs_loader" id="rs_loader" style="display:none;">
	<img src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/loader.gif" alt="" />
</div>

<?php if ($this->total > $count) { ?>
<a class="rs_read_more" id="rsepro_loadmore"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_LOAD_MORE'); ?></a>
<?php } ?>

<span id="total" class="rs_hidden"><?php echo $this->total; ?></span>
<span id="Itemid" class="rs_hidden"><?php echo JFactory::getApplication()->input->getInt('Itemid'); ?></span>
<span id="langcode" class="rs_hidden"><?php echo rseventsproHelper::getLanguageCode(); ?></span>
<span id="date" class="rs_hidden"><?php echo JFactory::getApplication()->input->getString('date'); ?></span>
<?php } else echo JText::_('COM_RSEVENTSPRO_GLOBAL_NO_EVENTS'); ?>

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
	jQuery(document).ready(function() {
		<?php if ($this->total > $count) { ?>
		jQuery('#rsepro_loadmore').on('click', function() {
			rspagination('day', jQuery('#rs_events_container > li').length);
		});
		<?php } ?>
		<?php if (!empty($count)) { ?>
		jQuery('#rs_events_container li').on({
			mouseenter: function() {
				jQuery(this).find('div.rs_options').css('display','');
			},
			mouseleave: function() {
				jQuery(this).find('div.rs_options').css('display','none');
			}
		});
		<?php } ?>
	});
</script>