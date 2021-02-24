<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');

$listOrder	= $this->escape($this->state->get('list.ordering','ordering'));
$listDirn	= $this->escape($this->state->get('list.direction', 'asc')); 
$saveOrder	= $listOrder == 'ordering'; 

if ($saveOrder) {
	$saveOrderingUrl = 'index.php?option=com_rseventspro&task=locations.saveOrderAjax&tmpl=component';
	
	if (rseventsproHelper::isJ4()) {
		JHtml::_('draggablelist.draggable');
	} else {
		JHtml::_('sortablelist.sortable', 'locationsList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
	}
}
?>

<form method="post" action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=locations'); ?>" name="adminForm" id="adminForm">

<?php echo RSEventsproAdapterGrid::sidebar(); ?>
	<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
	
	<table class="table table-striped adminlist" id="locationsList">
		<caption id="captionTable" class="sr-only">
			<span id="orderedBy"><?php echo JText::_('JGLOBAL_SORTED_BY'); ?> </span>,
			<span id="filteredBy"><?php echo JText::_('JGLOBAL_FILTERED_BY'); ?></span>
		</caption>
		<thead>
			<th width="1%" class="nowrap <?php echo RSEventsproAdapterGrid::styles(array('center')); ?> hidden-phone">
				<?php echo JHtml::_('searchtools.sort', '', 'ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
			</th>
			<th width="1%" class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo JHtml::_('grid.checkall'); ?></th>
			<th><?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'name', $listDirn, $listOrder); ?></th>
			<th width="1%" style="min-width:55px" class="nowrap <?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?></th>
			<th width="1%" class="nowrap hidden-phone <?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?></th>
		</thead>
		<tbody id="rseprocontainer"  <?php if ($saveOrder) { ?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="false"<?php } ?>>
			<?php foreach ($this->items as $i => $item) { ?>
				<tr class="row<?php echo $i % 2; ?>" sortable-group-id="1" data-draggable-group="1">
					<td class="order nowrap <?php echo RSEventsproAdapterGrid::styles(array('center')); ?> hidden-phone">
						<?php $iconClass = !$saveOrder ? ' inactive tip-top hasTooltip" title="' . JHtml::_('tooltipText', 'JORDERINGDISABLED') : ''; ?>
						<span class="sortable-handler<?php echo $iconClass ?>">
							<span class="icon-menu" aria-hidden="true"></span>
						</span>
						<?php if ($saveOrder) : ?>
							<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order" />
						<?php endif; ?>
					</td>
					<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td class="nowrap has-context">
						<a href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=location.edit&id='.$item->id); ?>"><?php echo $item->name; ?></a>
						<?php if (!empty($item->address)) { ?>
						<br /> <small><?php echo $item->address; ?></small>
						<?php } ?>
					</td>
					<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>">
						<?php echo JHtml::_('jgrid.published', $item->published, $i, 'locations.'); ?>
					</td>
					<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?> hidden-phone">
						<?php echo (int) $item->id; ?>
					</td>
				</tr>
			<?php } ?>
		</tbody>
		<tfoot>
		<tr>
			<td colspan="5" style="text-align: center;">
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