<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');

$listOrder	= $this->escape($this->state->get('list.ordering','date'));
$listDirn	= $this->escape($this->state->get('list.direction', 'asc')); ?>

<form method="post" action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=waitinglist&id='.$this->id); ?>" name="adminForm" id="adminForm">
	<?php echo RSEventsproAdapterGrid::sidebar(); ?>
		<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
		
		<table class="table table-striped">
			<caption id="captionTable" class="sr-only">
				<span id="orderedBy"><?php echo JText::_('JGLOBAL_SORTED_BY'); ?> </span>,
				<span id="filteredBy"><?php echo JText::_('JGLOBAL_FILTERED_BY'); ?></span>
			</caption>
			<thead>
				<th width="1%" class="small <?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo JHtml::_('grid.checkall'); ?></th>
				<th><?php echo JHtml::_('searchtools.sort', 'COM_RSEVENTSPRO_WAITING_NAME', 'name', $listDirn, $listOrder); ?></th>
				<th class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo JHtml::_('searchtools.sort', 'COM_RSEVENTSPRO_WAITING_EMAIL', 'email', $listDirn, $listOrder); ?></th>
				<th class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo JHtml::_('searchtools.sort', 'COM_RSEVENTSPRO_WAITING_DATE', 'date', $listDirn, $listOrder); ?></th>
				<th class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo JHtml::_('searchtools.sort', 'COM_RSEVENTSPRO_WAITING_SENT', 'sent', $listDirn, $listOrder); ?></th>
				<th class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo JHtml::_('searchtools.sort', 'COM_RSEVENTSPRO_WAITING_CONFIRMED', 'confirmed', $listDirn, $listOrder); ?></th>
				<th width="1%" class="nowrap hidden-phone <?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?></th>
			</thead>
			<tbody id="rseprocontainer">
				<?php foreach ($this->items as $i => $item) { ?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
						<td class="nowrap has-context">
							<a href="index.php?option=com_rseventspro&view=waitinglist&layout=edit&id=<?php echo $item->id; ?>">
								<?php echo $item->name; ?>
							</a>
						</td>
						<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo $item->email; ?></td>
						<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo rseventsproHelper::showdate($item->date); ?></td>
						<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo $item->sent != '0000-00-00 00:00:00' ? rseventsproHelper::showdate($item->sent) : ''; ?></td>
						<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo $item->confirmed != '0000-00-00 00:00:00' ? rseventsproHelper::showdate($item->confirmed) : ''; ?></td>
						<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?> hidden-phone"><?php echo (int) $item->id; ?></td>
					</tr>
				<?php } ?>
			</tbody>
			<tfoot>
			<tr>
				<td colspan="7" style="text-align: center;">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		</table>
	</div>
	
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="" />
</form>