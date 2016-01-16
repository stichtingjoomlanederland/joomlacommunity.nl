<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');?>

<?php if (!empty($this->events)) { ?>
<?php foreach($this->events as $eventid) { ?>
<?php $details = rseventsproHelper::details($eventid->id); ?>
<?php if (isset($details['event']) && !empty($details['event'])) $event = $details['event']; else continue; ?>
<?php if (!rseventsproHelper::canview($eventid->id) && $event->owner != $this->user) continue; ?>
<?php $full = rseventsproHelper::eventisfull($event->id); ?>
<?php $ongoing = rseventsproHelper::ongoing($event->id); ?>
<?php $categories = (isset($details['categories']) && !empty($details['categories'])) ? JText::_('COM_RSEVENTSPRO_GLOBAL_CATEGORIES').': '.$details['categories'] : '';  ?>
<?php $tags = (isset($details['tags']) && !empty($details['tags'])) ? JText::_('COM_RSEVENTSPRO_GLOBAL_TAGS').': '.$details['tags'] : '';  ?>
<?php $incomplete = !$event->completed ? ' rs_incomplete' : ''; ?>
<?php $featured = $event->featured ? ' rs_featured' : ''; ?>
<?php $repeats = rseventsproHelper::getRepeats($event->id); ?>
<li class="rs_event_detail<?php echo $incomplete.$featured; ?>" id="rs_event<?php echo $event->id; ?>" itemscope itemtype="http://schema.org/Event">
	
	<div class="rs_options" style="display:none;">
		<?php if ((!empty($this->permissions['can_edit_events']) || $event->owner == $this->user || $event->sid == $this->user || $this->admin) && !empty($this->user)) { ?>
			<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=edit&id='.rseventsproHelper::sef($event->id,$event->name)); ?>">
				<img src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/edit.png" alt="<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_EDIT'); ?>" />
			</a>
		<?php } ?>
		<?php if ((!empty($this->permissions['can_delete_events']) || $event->owner == $this->user || $event->sid == $this->user || $this->admin) && !empty($this->user)) { ?>
			<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.remove&id='.rseventsproHelper::sef($event->id,$event->name)); ?>"  onclick="return confirm('<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_DELETE_CONFIRMATION'); ?>');">
				<img src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/delete.png" alt="<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_DELETE'); ?>" />
			</a>
		<?php } ?>
	</div>
	
	<div class="rs_event_image" itemprop="image">
		<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name)); ?>" class="rs_event_link">
			<?php if (!empty($event->icon)) { ?>
				<img src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/events/thumbs/s_<?php echo $event->icon.'?nocache='.uniqid(''); ?>" alt="" width="<?php echo $this->config->icon_small_width; ?>" />
			<?php } else { ?>
				<img src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/blank.png" alt="" width="70" />
			<?php }  ?>
		</a>
	</div>
	
	<div class="rs_event_details">
		<span itemprop="name">
			<a itemprop="url" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name)); ?>" class="rs_event_link<?php echo $full ? ' rs_event_full' : ''; ?><?php echo $ongoing ? ' rs_event_ongoing' : ''; ?>"><?php echo $event->name; ?></a> <?php if (!$event->completed) echo JText::_('COM_RSEVENTSPRO_GLOBAL_INCOMPLETE_EVENT'); ?> <?php if (!$event->published) echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNPUBLISHED_EVENT'); ?>
		</span>
		<span>
			<?php if ($event->allday) { ?>
			<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_ON'); ?> <b><?php echo rseventsproHelper::date($event->start,$this->config->global_date,true); ?></b>
			<?php } else { ?>
			<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_FROM'); ?> <b><?php echo rseventsproHelper::date($event->start,rseventsproHelper::showMask('start',$event->options),true); ?></b> <?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_TO_LOWERCASE'); ?> <b><?php echo rseventsproHelper::date($event->end,rseventsproHelper::showMask('end',$event->options),true); ?></b>
			<?php } ?>
		</span>
		<span>
			<?php if ($event->locationid && $event->lpublished) { echo JText::_('COM_RSEVENTSPRO_GLOBAL_AT'); ?> <a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=location&id='.rseventsproHelper::sef($event->locationid,$event->location)); ?>"><?php echo $event->location; ?></a> <?php } ?>
			<?php echo $categories.' '.$tags; ?>
		</span>
		<?php if ($this->params->get('repeatcounter',1)) { ?>
		<span class="rs_event_repeats">
			<?php if ($repeats) { ?> 
			(<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=default&parent='.rseventsproHelper::sef($event->id,$event->name)); ?>"><?php echo JText::sprintf('COM_RSEVENTSPRO_GLOBAL_REPEATS',$repeats); ?></a>) 
			<?php } ?>
		</span>
		<?php } ?>
	</div>
	
	<div style="display:none"><span itemprop="startDate"><?php echo rseventsproHelper::date($event->start,'Y-m-d H:i:s'); ?></span></div>
	<div style="display:none"><span itemprop="endDate"><?php echo rseventsproHelper::date($event->end,'Y-m-d H:i:s'); ?></span></div>
</li>
<?php } ?>
<?php } ?>