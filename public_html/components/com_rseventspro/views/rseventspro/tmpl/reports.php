<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<h1><?php echo JText::sprintf('COM_RSEVENTSPRO_REPORTS_FOR',$this->event->name); ?></h1>

<form action="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro'); ?>" id="adminForm" name="adminForm" method="post">
	<div style="text-align: right;">
		<button type="button" class="btn btn-danger" onclick="Joomla.submitbutton('rseventspro.deletereports');"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_DELETE'); ?></button>
		<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false,rseventsproHelper::itemid($this->event->id)); ?>" class="btn btn-primary"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_BACK'); ?></a>
	</div>
	<br />

	<?php if (!empty($this->reports['data'])) { ?>
	<div class="rs_table_layout rs_table_header">
		<span class="rs_table_id"><input type="checkbox" name="checkall-toggle" id="rscheckbox" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this);"/></span>
		<span style="width:70%;"><?php echo JText::_('COM_RSEVENTSPRO_REPORT_MESSAGE_TEXT'); ?></span>
		<span style="width:10%; text-align: center;"><?php echo JText::_('COM_RSEVENTSPRO_REPORT_NAME'); ?></span>
		<span style="width:10%; text-align: center;"><?php echo JText::_('COM_RSEVENTSPRO_REPORT_IP'); ?></span>
	</div>
	<div class="rs_clear"></div>

	<?php foreach ($this->reports['data'] as $i => $report) { ?>
	<div class="rs_table_layout">
		<span class="rs_table_id"><?php echo JHTML::_('grid.id',$i,$report->id); ?></span>
		<span style="width:70%;"><?php echo $report->text; ?></span>
		<span style="width:10%; text-align: center;"><?php echo $report->idu ? $report->name : JText::_('COM_RSEVENTSPRO_GLOBAL_GUEST'); ?></span>
		<span style="width:10%; text-align: center;"><?php echo $report->ip; ?></span>
	</div>
	<?php } ?>
	<?php } ?>

	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="return" value="<?php echo base64_encode(JURI::getInstance()); ?>" />
	<input type="hidden" name="task" value="" />
</form>