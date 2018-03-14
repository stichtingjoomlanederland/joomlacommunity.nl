<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

$date_mask	 = rseventsproHelper::getConfig('global_date');
$event		 = $this->details['event'];
$icon		 =
$alldayStart = $this->escaped ? addslashes(rseventsproHelper::showdate($event->start,$date_mask,true)) : rseventsproHelper::showdate($event->start,$date_mask,true);
$eventname	 = $this->escaped ? addslashes($event->name) : $event->name;
$lname		 = $this->escaped ? addslashes($event->location) : $event->location;
$start		 = $this->escaped ? addslashes(rseventsproHelper::showdate($event->start,null,true)) : rseventsproHelper::showdate($event->start,null,true);
$end		 = $this->escaped ? addslashes(rseventsproHelper::showdate($event->end,null,true)) : rseventsproHelper::showdate($event->end,null,true); ?>

<div class="rsepro-map-info">
	
	<?php if (!empty($this->details['image_s'])) { ?>
	<div class="rsepro-map-info-image">
		<img src="<?php echo $this->details['image_s']; ?>" alt="<?php echo $eventname; ?>" width="<?php echo rseventsproHelper::getConfig('icon_small_width','int'); ?>px" />
	</div>
	<?php } ?>
	
	<?php if ($event->allday) { ?>
	<div class="rsepro-map-info-block">
		<a class="rsepro-map-info-name" target="_blank" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name),false,$this->itemid); ?>">
			<?php echo $eventname; ?>
		</a>
	</div>
	<div class="rsepro-map-info-block"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_ON',true).' '.$alldayStart; ?></div>
	<div class="rsepro-map-info-block">
		<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_AT',true); ?> 
		<a target="_blank" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=location&id='.rseventsproHelper::sef($event->locationid,$event->location),false,$this->itemid); ?>">
			<?php echo $lname; ?>
		</a>
	</div>
	<?php } else { ?>
	<div class="rsepro-map-info-block">
		<a class="rsepro-map-info-name" target="_blank" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name),false,$this->itemid); ?>">
			<?php echo $eventname; ?>
		</a>
	</div>
	<div class="rsepro-map-info-block"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_STARTS',true).' '.$start; ?></div>
	<div class="rsepro-map-info-block"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_ENDS',true).' '. $end; ?></div>
	<div class="rsepro-map-info-block">
		<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_AT',true); ?>
		<a target="_blank" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=location&id='.rseventsproHelper::sef($event->locationid,$event->location),false,$this->itemid); ?>">
			<?php echo $lname; ?>
		</a>
	</div>
	<?php } ?>
			
	<?php if (!$this->single) { ?>
	<br />
	<br />
	<a style="float:right;" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&location='.rseventsproHelper::sef($event->locationid,$event->location),false,$this->itemid);?>">
		<?php echo JText::_('COM_RSEVENTSPRO_VIEW_OTHER_EVENTS',true); ?> 
	</a>
	<?php } ?>
</div>