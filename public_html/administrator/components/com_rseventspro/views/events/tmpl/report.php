<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<form method="post" action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=events'); ?>" name="adminForm" id="adminForm" autocomplete="off">
	<?php echo RSEventsproAdapterGrid::sidebar(); ?>
		<table class="table table-striped">
			<thead>
				<th width="1%" class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo JHtml::_('grid.checkall'); ?></th>
				<th width="84%"><?php echo JText::_('COM_RSEVENTSPRO_REPORT_MESSAGE'); ?></th>
				<th width="10%" class="nowrap hidden-phone <?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo JText::_('COM_RSEVENTSPRO_REPORT_USER'); ?></th>
				<th width="5%" class="nowrap hidden-phone <?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo JText::_('COM_RSEVENTSPRO_REPORT_IP'); ?></th>
			</thead>
			<tbody>
				<?php if (!empty($this->reports['data'])) { ?>
				<?php foreach ($this->reports['data'] as $i => $report) { ?>
				<tr class="row<?php echo $i%2; ?>">
					<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo JHTML::_('grid.id',$i,$report->id); ?></td>
					<td class="has-context"><?php echo $report->text; ?></td>
					<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo $report->idu ? $report->name : JText::_('COM_RSEVENTSPRO_GLOBAL_GUEST'); ?></td>
					<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo $report->ip; ?></td>
				</tr>
				<?php } ?>
				<?php } ?>
			</tbody>
		</table>
	</div>

	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="ide" value="<?php echo JFactory::getApplication()->input->getInt('id',0); ?>" />
</form>