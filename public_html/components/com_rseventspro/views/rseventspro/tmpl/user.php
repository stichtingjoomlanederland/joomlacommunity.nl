<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
$col = $this->data->image ? '9' : '12'; ?>

<div class="rsepro-user-info">
	<h1>
		<?php echo $this->data->name; ?>
		
		<?php if ($this->canEdit) { ?>
		<a href="<?php echo JRoute::_('index.php?option=com_rseventspro&layout=edituser&id='.rseventsproHelper::sef($this->id,$this->data->name), false); ?>" class="<?php echo RSEventsproAdapterGrid::styles(array('btn','btn-small','pull-right')); ?>">
			<i class="fa fa-pencil"></i> <?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_EDIT'); ?>
		</a>
		<?php } ?>
	</h1>
	
	<div class="<?php echo RSEventsproAdapterGrid::row(); ?>">
		<?php if ($this->data->image) { ?>
		<div class="<?php echo RSEventsproAdapterGrid::column(3); ?>">
			<img src="<?php echo JUri::root(); ?>components/com_rseventspro/assets/images/users/<?php echo $this->data->image; ?>" alt=""/>
		</div>
		<?php } ?>
		
		<div class="<?php echo RSEventsproAdapterGrid::column($col); ?>">
			<?php if ($this->data->description) echo $this->data->description.' <hr />'; ?>
	
			<?php if ($this->created) { ?>
			<h3><?php echo JText::_('COM_RSEVENTSPRO_CREATED_EVENTS'); ?></h3>
			<ul class="<?php echo RSEventsproAdapterGrid::styles(array('unstyled')); ?> rsepro-events-ul">
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
			<ul class="<?php echo RSEventsproAdapterGrid::styles(array('unstyled')); ?> rsepro-events-ul">
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
	</div>
</div>