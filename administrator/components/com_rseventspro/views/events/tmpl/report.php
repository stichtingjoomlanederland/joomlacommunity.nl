<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<script type="text/javascript">
Joomla.submitbutton = function(task) {
	if(task == 'back') {
		document.location = '<?php echo JRoute::_('index.php?option=com_rseventspro&view=events',false); ?>';
		return false;
	} else {
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}
</script>

<form method="post" action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=events'); ?>" name="adminForm" id="adminForm" autocomplete="off">
	<div class="row-fluid">
		<div class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div class="span10">
			<table class="table table-striped adminlist">
				<thead>
					<th width="1%" align="center" class="center"><input type="checkbox" name="checkall-toggle" id="rscheckbox" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this);"/></th>
					<th width="84%"><?php echo JText::_('COM_RSEVENTSPRO_REPORT_MESSAGE'); ?></th>
					<th width="10%" class="nowrap hidden-phone center"><?php echo JText::_('COM_RSEVENTSPRO_REPORT_USER'); ?></th>
					<th width="5%" class="nowrap hidden-phone center"><?php echo JText::_('COM_RSEVENTSPRO_REPORT_IP'); ?></th>
				</thead>
				<tbody>
					<?php if (!empty($this->reports['data'])) { ?>
					<?php foreach ($this->reports['data'] as $i => $report) { ?>
					<tr class="row<?php echo $i%2; ?>">
						<td align="center" class="center"><?php echo JHTML::_('grid.id',$i,$report->id); ?></td>
						<td class="has-context"><?php echo $report->text; ?></td>
						<td align="center" class="center"><?php echo $report->idu ? $report->name : JText::_('COM_RSEVENTSPRO_GLOBAL_GUEST'); ?></td>
						<td align="center" class="center"><?php echo $report->ip; ?></td>
					</tr>
					<?php } ?>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>

	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="ide" value="<?php echo JFactory::getApplication()->input->getInt('id',0); ?>" />
</form>