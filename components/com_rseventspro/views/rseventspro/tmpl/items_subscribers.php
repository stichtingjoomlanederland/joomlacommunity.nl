<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');?>
<?php if (!empty($this->subscribers)) { ?>
<?php foreach($this->subscribers as $row) { ?>
<li class="rs_event_detail">
	<div class="rs_options" style="display:none;">
		<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=editsubscriber&id='.rseventsproHelper::sef($row->id,$row->name)); ?>">
			<img src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/edit.png" alt="<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_EDIT'); ?>" />
		</a>
		<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.removesubscriber&id='.rseventsproHelper::sef($row->id,$row->name)); ?>"  onclick="return confirm('<?php echo JText::_('COM_RSEVENTSPRO_DELETE_SUBSCRIBER_CONFIRMATION'); ?>');">
			<img src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/delete.png" alt="<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_DELETE'); ?>" />
		</a>
		<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.approve&id='.rseventsproHelper::sef($row->id,$row->name)); ?>" title="<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_APPROVE'); ?>">
			<img src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/ok.png" alt="<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_APPROVE'); ?>" />
		</a>
		<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.pending&id='.rseventsproHelper::sef($row->id,$row->name)); ?>" title="<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_PENDING'); ?>">
			<img src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/pending.png" alt="<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_PENDING'); ?>" />
		</a>
		<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.denied&id='.rseventsproHelper::sef($row->id,$row->name)); ?>" title="<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_DENIED'); ?>">
			<img src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/denied.png" alt="<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_DENIED'); ?>" />
		</a>
	</div>
	<div class="rs_event_details rs_inline">
		<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=editsubscriber&id='.rseventsproHelper::sef($row->id,$row->name)); ?>"><?php echo $row->name; ?></a> <br />
		<?php echo rseventsproHelper::date($row->date); ?> <br />
		<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=editsubscriber&id='.rseventsproHelper::sef($row->id,$row->name)); ?>"><?php echo $row->email; ?></a> - <?php echo $this->getUser($row->idu); ?> - <?php echo $row->ip; ?>
	</div>
	<div class="rs_status"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_STATUS'); ?>: <?php echo $this->getStatus($row->state); ?></div>
</li>
<?php } ?>
<?php } ?>