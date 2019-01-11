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
		<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.removersvp&id='.rseventsproHelper::sef($row->id,$row->name).'&ide='.rseventsproHelper::sef($this->row->id,$this->row->name),false); ?>"  onclick="return confirm('<?php echo JText::_('COM_RSEVENTSPRO_DELETE_RSVP_GUEST_CONFIRMATION'); ?>');">
			<i class="fa fa-trash fa-fw"></i>
		</a>
		<a class="hasTooltip" href="javascript:void(0)" onclick="rsepro_rsvp_status('<?php echo $row->id; ?>', 'going');" title="<?php echo JText::_('COM_RSEVENTSPRO_RSVP_GOING'); ?>">
			<i class="fa fa-check fa-fw"></i>
		</a>
		<a class="hasTooltip" href="javascript:void(0)" onclick="rsepro_rsvp_status('<?php echo $row->id; ?>', 'interested');" title="<?php echo JText::_('COM_RSEVENTSPRO_RSVP_INTERESTED'); ?>">
			<i class="fa fa-exclamation-triangle fa-fw"></i>
		</a>
		<a class="hasTooltip" href="javascript:void(0)" onclick="rsepro_rsvp_status('<?php echo $row->id; ?>', 'notgoing');" title="<?php echo JText::_('COM_RSEVENTSPRO_RSVP_NOT_GOING'); ?>">
			<i class="fa fa-minus-circle fa-fw"></i>
		</a>
	</div>
	<div class="rs_event_details rs_inline">
		<?php echo $row->name; ?> <br />
		<?php echo rseventsproHelper::showdate($row->date,null,true); ?> <br />
		<?php echo $row->email; ?>
	</div>
	<div class="rs_status"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_STATUS'); ?>: <span id="status<?php echo $row->id ?>"><?php echo rseventsproHelper::RSVPStatus($row->rsvp); ?></span></div>
</li>
<?php } ?>
<?php } ?>