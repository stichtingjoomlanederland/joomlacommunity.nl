<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access'); 
$event = $this->details['event']; ?>

<div class="rsepro-calendar-tooltip-block">
	<?php if ($event->icon) { ?>
	<div class="rsepro-calendar-tooltip-image">
		<img src="<?php echo rseventsproHelper::thumb($event->id,100); ?>" alt="" />
	</div>
	<?php } ?>

	<div class="rsepro-calendar-tooltip-content">
		<?php if ($event->allday) { ?>
		<strong><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_ON'); ?></strong> <?php echo rseventsproHelper::showdate($event->start, $this->config->global_date, true); ?> <br />
		<?php } else { ?>
		<strong><?php echo JText::_('COM_RSEVENTSPRO_CALENDAR_FROM'); ?></strong> <?php echo rseventsproHelper::showdate($event->start,null,true); ?> <br />
		<strong><?php echo JText::_('COM_RSEVENTSPRO_CALENDAR_TO'); ?></strong> <?php echo rseventsproHelper::showdate($event->end,null,true); ?> <br />
		<?php } ?>
	</div>

	<?php if (!empty($event->small_description)) { ?>
	<div class="rsepro-calendar-tooltip-description">
		<?php echo $event->small_description; ?>
	</div>
	<?php } ?>
</div>