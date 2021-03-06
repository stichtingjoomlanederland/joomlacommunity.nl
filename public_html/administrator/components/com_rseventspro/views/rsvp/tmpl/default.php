<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');

$listOrder	= $this->escape($this->state->get('list.ordering','r.date'));
$listDirn	= $this->escape($this->state->get('list.direction','asc')); ?>

<form method="post" action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=rsvp&id='.JFactory::getApplication()->input->getInt('id',0)); ?>" name="adminForm" id="adminForm">
	<?php echo RSEventsproAdapterGrid::sidebar(); ?>
		<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
	
		<table class="table table-striped">
			<caption id="captionTable" class="sr-only">
				<span id="orderedBy"><?php echo JText::_('JGLOBAL_SORTED_BY'); ?> </span>,
				<span id="filteredBy"><?php echo JText::_('JGLOBAL_FILTERED_BY'); ?></span>
			</caption>
			<thead>
				<th width="1%" class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo JHtml::_('grid.checkall'); ?></th>
				<th><?php echo JText::_('COM_RSEVENTSPRO_RSVP_GUEST_NAME'); ?></th>
				<th width="15%" class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo JHtml::_('searchtools.sort', 'COM_RSEVENTSPRO_RSVP_GUEST_DATE', 'r.date', $listDirn, $listOrder); ?></th>
				<th width="10%" class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo JText::_('COM_RSEVENTSPRO_RSVP_GUEST_STATUS'); ?></th>
				<th width="1%" class="nowrap hidden-phone <?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'r.id', $listDirn, $listOrder); ?></th>
			</thead>
			
			<tbody>
				<?php foreach ($this->items as $i => $item) { ?>
				<tr class="row<?php echo $i % 2; ?>">
					<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo JHTML::_('grid.id',$i,$item->id); ?></td>
					<td><?php echo $item->name; ?></td>
					<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo rseventsproHelper::showdate($item->date); ?></td>
					<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo rseventsproHelper::RSVPStatus($item->rsvp); ?></td>
					<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo $item->id; ?></td>
				</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="11" style="text-align:center;">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
	
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="ide" value="<?php echo JFactory::getApplication()->input->getInt('id',0); ?>" />
	<input type="hidden" name="task" value="" />
</form>