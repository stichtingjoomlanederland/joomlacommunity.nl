<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<div class="rsepro-user-info">
	<h1>
		<?php echo $this->data->name; ?>
		
		<?php if ($this->canEdit) { ?>
		<a href="<?php echo JRoute::_('index.php?option=com_rseventspro&layout=edituser&id='.rseventsproHelper::sef($this->id,$this->data->name), false); ?>" class="btn btn-small pull-right">
			<i class="fa fa-pencil"></i> <?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_EDIT'); ?>
		</a>
		<?php } ?>
	</h1>
	
	<?php if ($this->data->image) { ?>
	<div class="rsepro-user-image">
		<img src="<?php echo JUri::root(); ?>components/com_rseventspro/assets/images/users/<?php echo $this->data->image; ?>" alt=""/>
	</div>
	<?php } ?>
	
	<div class="rsepro-user-description">
		<?php echo $this->data->description; ?>
	</div>
	
	<div class="clearfix"></div>
	<hr />
	
	<?php if ($this->created) { ?>
	<h3><?php echo JText::_('COM_RSEVENTSPRO_CREATED_EVENTS'); ?></h3>
	<ul class="unstyled rsepro-events-ul">
		<?php foreach ($this->created as $createdEvent) { ?>
		<li>
			<?php if ($createdEvent->published == 2) echo JText::_('COM_RSEVENTSPRO_ARCHIVED').' '; ?>
			<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($createdEvent->id, $createdEvent->name), false, $createdEvent->itemid); ?>">
				<?php echo $createdEvent->name; ?>
			</a>
			(<?php echo rseventsproHelper::date($createdEvent->start); ?><?php if (!$createdEvent->allday) { ?> - <?php echo rseventsproHelper::date($createdEvent->end); ?><?php } ?>)
		</li>
		<?php } ?>
	</ul>
	<?php } ?>
	
	<?php if ($this->joined) { ?>
	<h3><?php echo JText::_('COM_RSEVENTSPRO_JOINED_EVENTS'); ?></h3>
	<ul class="unstyled rsepro-events-ul">
		<?php foreach ($this->joined as $joinedEvent) { ?>
		<li>
			<?php if ($joinedEvent->published == 2) echo JText::_('COM_RSEVENTSPRO_ARCHIVED').' '; ?>
			<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($joinedEvent->id, $joinedEvent->name), false, $joinedEvent->itemid); ?>">
				<?php echo $joinedEvent->name; ?>
			</a>
			(<?php echo rseventsproHelper::date($createdEvent->start); ?><?php if (!$createdEvent->allday) { ?> - <?php echo rseventsproHelper::date($createdEvent->end); ?><?php } ?>)
		</li>
		<?php } ?>
	</ul>
	<?php } ?>
	
</div>