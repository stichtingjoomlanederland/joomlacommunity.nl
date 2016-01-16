<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
$event = $this->details['event']; 
$categories = (isset($this->details['categories']) && !empty($this->details['categories'])) ? JText::_('COM_RSEVENTSPRO_GLOBAL_CATEGORIES').': '.$this->details['categories'] : '';
$tags = (isset($this->details['tags']) && !empty($this->details['tags'])) ? JText::_('COM_RSEVENTSPRO_GLOBAL_TAGS').': '.$this->details['tags'] : ''; ?>

<div class="rsepro_plugin_container">
	<div class="rsepro_plugin_image">
		<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name).$this->itemid); ?>" class="rs_event_link">
			<?php if (!empty($event->icon)) { ?>
				<img src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/events/thumbs/s_<?php echo $event->icon.'?nocache='.uniqid(''); ?>" alt="" width="<?php echo $this->config->icon_small_width; ?>" />
			<?php } else { ?>
				<img src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/blank.png" alt="" width="70" />
			<?php }  ?>
		</a>
	</div>
	<div class="rsepro_plugin_content">
		<span>
			<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name).$this->itemid); ?>" class="rsepro_plugin_link">
				<?php echo $event->name; ?>
			</a>
		</span>
		<span>
			<?php if ($event->allday) { ?>
			<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_ON'); ?> <b><?php echo rseventsproHelper::date($event->start,$this->config->global_date,true); ?></b>
			<?php } else { ?>
			<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_FROM'); ?> <b><?php echo rseventsproHelper::date($event->start,rseventsproHelper::showMask('start',$event->options),true); ?></b> <?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_TO_LOWERCASE'); ?> <b><?php echo rseventsproHelper::date($event->end,rseventsproHelper::showMask('end',$event->options),true); ?></b>
			<?php } ?>
		</span>
		<span>
			<?php if ($event->locationid && $event->lpublished) { echo JText::_('COM_RSEVENTSPRO_GLOBAL_AT'); ?> <a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=location&id='.rseventsproHelper::sef($event->locationid,$event->location).$this->itemid); ?>"><?php echo $event->location; ?></a> <?php } ?>
			<?php echo $categories.' '.$tags; ?>
		</span>
	</div>
</div>