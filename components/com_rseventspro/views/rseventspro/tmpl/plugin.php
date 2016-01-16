<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
$event = $this->details['event']; 
$categories = (isset($this->details['categories']) && !empty($this->details['categories'])) ? JText::_('COM_RSEVENTSPRO_GLOBAL_CATEGORIES').': '.$this->details['categories'] : '';
$tags = (isset($this->details['tags']) && !empty($this->details['tags'])) ? JText::_('COM_RSEVENTSPRO_GLOBAL_TAGS').': '.$this->details['tags'] : ''; ?>

<div class="rsepro_plugin_container">
	
	<?php if (!empty($event->options['show_icon_list'])) { ?>
	<div class="rsepro_plugin_image">
		<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name).$this->itemid); ?>" class="rs_event_link thumbnail">
			<img src="<?php echo JRoute::_('index.php?option=com_rseventspro&task=image&id='.rseventsproHelper::sef($event->id,$event->name).$this->itemid, false); ?>" alt="" width="<?php echo $this->config->icon_small_width; ?>" />
		</a>
	</div>
	<?php } ?>
	
	<div class="rsepro_plugin_content">
		<div>
			<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name).$this->itemid); ?>" class="rsepro_plugin_link">
				<?php echo $event->name; ?>
			</a>
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
</div>