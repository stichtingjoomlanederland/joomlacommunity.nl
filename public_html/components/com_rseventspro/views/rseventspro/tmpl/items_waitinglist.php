<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');?>
<?php if (!empty($this->data)) { ?>
<?php foreach($this->data as $row) { ?>
<li class="rs_event_detail">
		<div class="rs_options" style="display:none;">
			<a class="hasTooltip" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.approvewaiting&id='.rseventsproHelper::sef($row->id,$row->name),false); ?>" title="<?php echo JText::_('COM_RSEVENTSPRO_WAITINGLIST_APPROVE'); ?>">
				<i class="fa fa-check fa-fw"></i>
			</a>
			<a class="hasTooltip" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.removewaiting&id='.rseventsproHelper::sef($row->id,$row->name),false); ?>" onclick="return confirm('<?php echo JText::_('COM_RSEVENTSPRO_WAITINGLIST_DELETE_CONFIRM'); ?>');" title="<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_DELETE'); ?>">
				<i class="fa fa-trash fa-fw"></i>
			</a>
		</div>
		<div class="rs_event_details rs_inline">
			<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=editwaitinglist&id='.rseventsproHelper::sef($row->id,$row->name),false); ?>"><?php echo $row->name; ?></a> (<?php echo $row->email; ?>) <br />
			<?php if ($row->date != '0000-00-00 00:00:00') { echo JText::_('COM_RSEVENTSPRO_WAITINGLIST_ADDED'); ?>: <?php echo rseventsproHelper::showdate($row->date,null,true); ?> <br /><?php } ?>
			<?php if ($row->sent != '0000-00-00 00:00:00') { echo JText::_('COM_RSEVENTSPRO_WAITINGLIST_SENT'); ?>: <?php echo rseventsproHelper::showdate($row->sent,null,true); ?> <br /><?php } ?>
			<?php if ($row->confirmed != '0000-00-00 00:00:00') { echo JText::_('COM_RSEVENTSPRO_WAITINGLIST_CONFIRMED'); ?>: <?php echo rseventsproHelper::showdate($row->confirmed,null,true); ?><?php } ?>
		</div>
	</li>
<?php } ?>
<?php } ?>