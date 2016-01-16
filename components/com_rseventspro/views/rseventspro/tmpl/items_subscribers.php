<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');?>
<?php if (!empty($this->subscribers)) { ?>
<?php foreach($this->subscribers as $row) { ?>
<li class="rs_event_detail">
	<div class="rs_options" style="display:none;">
		<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=editsubscriber&id='.rseventsproHelper::sef($row->id,$row->name)); ?>">
			<i class="fa fa-pencil"></i>
		</a>
		<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.removesubscriber&id='.rseventsproHelper::sef($row->id,$row->name)); ?>"  onclick="return confirm('<?php echo JText::_('COM_RSEVENTSPRO_DELETE_SUBSCRIBER_CONFIRMATION'); ?>');">
			<i class="fa fa-trash"></i>
		</a>
		<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.approve&id='.rseventsproHelper::sef($row->id,$row->name)); ?>" title="<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_APPROVE'); ?>">
			<i class="fa fa-check"></i>
		</a>
		<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.pending&id='.rseventsproHelper::sef($row->id,$row->name)); ?>" title="<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_PENDING'); ?>">
			<i class="fa fa-exclamation-triangle"></i>
		</a>
		<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.denied&id='.rseventsproHelper::sef($row->id,$row->name)); ?>" title="<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_DENIED'); ?>">
			<i class="fa fa-minus-circle"></i>
		</a>
	</div>
	<div class="rs_event_details rs_inline">
		<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=editsubscriber&id='.rseventsproHelper::sef($row->id,$row->name)); ?>"><?php echo $row->name; ?></a> 
		<?php if ($row->gateway) { ?>(<?php echo rseventsproHelper::getPayment($row->gateway); ?>)<?php } ?> <br />
		<?php echo rseventsproHelper::showdate($row->date); ?> <br />
		<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=editsubscriber&id='.rseventsproHelper::sef($row->id,$row->name)); ?>"><?php echo $row->email; ?></a> - <?php echo $this->getUser($row->idu); ?> - <?php echo $row->ip; ?>
	</div>
	<div class="rs_status"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_STATUS'); ?>: <?php echo $this->getStatus($row->state); ?></div>
</li>
<?php } ?>
<?php } ?>