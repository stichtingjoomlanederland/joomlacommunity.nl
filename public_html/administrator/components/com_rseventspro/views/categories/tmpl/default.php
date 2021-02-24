<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');

$listOrder	= $this->escape($this->state->get('list.ordering','a.lft'));
$listDirn	= $this->escape($this->state->get('list.direction','asc'));
$saveOrder 	= ($listOrder == 'a.lft' && strtolower($listDirn) == 'asc'); 

if ($saveOrder) {
	$saveOrderingUrl = 'index.php?option=com_rseventspro&task=categories.saveOrderAjax&tmpl=component';
	
	if (rseventsproHelper::isJ4()) {
		JHtml::_('draggablelist.draggable');
	} else {
		JHtml::_('sortablelist.sortable', 'categoriesList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
	}
} ?>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=categories'); ?>" method="post" name="adminForm" id="adminForm">
	
	<?php echo RSEventsproAdapterGrid::sidebar(); ?>
		<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
	
		<table class="table table-striped adminlist" id="categoriesList">
			<caption id="captionTable" class="sr-only">
				<span id="orderedBy"><?php echo JText::_('JGLOBAL_SORTED_BY'); ?> </span>,
				<span id="filteredBy"><?php echo JText::_('JGLOBAL_FILTERED_BY'); ?></span>
			</caption>
			<thead>
				<tr>
					<th width="1%" class="nowrap <?php echo RSEventsproAdapterGrid::styles(array('center')); ?> hidden-phone">
						<?php echo JHtml::_('searchtools.sort', '', 'a.lft', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
					</th>
					<th width="1%">
						<?php echo JHtml::_('grid.checkall'); ?>
					</th>
					<th width="1%" class="nowrap <?php echo RSEventsproAdapterGrid::styles(array('center')); ?>">
						<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
					</th>
					<th width="10%" class="nowrap hidden-phone <?php echo RSEventsproAdapterGrid::styles(array('center')); ?>">
						<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
					</th>
					<th width="5%" class="nowrap hidden-phone <?php echo RSEventsproAdapterGrid::styles(array('center')); ?>">
						<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'language_title', $listDirn, $listOrder); ?>
					</th>
					<th width="1%" class="nowrap hidden-phone <?php echo RSEventsproAdapterGrid::styles(array('center')); ?>">
						<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
					</th>
				</tr>
			</thead>
			<tbody id="rseprocontainer"<?php if ($saveOrder) { ?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="false"<?php } ?>>
				<?php foreach ($this->items as $i => $item) { ?>
					<?php
					$orderkey   = array_search($item->id, $this->ordering[$item->parent_id]);
					// Get the parents of item for sorting
					if ($item->level > 1) {
						$parentsStr = '';
						$_currentParentId = $item->parent_id;
						$parentsStr = ' ' . $_currentParentId;
						for ($i2 = 0; $i2 < $item->level; $i2++) {
							foreach ($this->ordering as $k => $v) {
								$v = implode('-', $v);
								$v = '-' . $v . '-';
								if (strpos($v, '-' . $_currentParentId . '-') !== false) {
									$parentsStr .= ' ' . $k;
									$_currentParentId = $k;
									break;
								}
							}
						}
					} else {
						$parentsStr = "";
					} ?>
					<tr class="row<?php echo $i % 2; ?>" data-draggable-group="<?php echo $item->parent_id; ?>" sortable-group-id="<?php echo $item->parent_id; ?>" item-id="<?php echo $item->id ?>" parents="<?php echo $parentsStr ?>" level="<?php echo $item->level ?>">
						<td class="order nowrap <?php echo RSEventsproAdapterGrid::styles(array('center')); ?> hidden-phone">
							<?php $iconClass = !$saveOrder ? ' inactive tip-top hasTooltip" title="' . JHtml::_('tooltipText', 'JORDERINGDISABLED') : ''; ?>
							<span class="sortable-handler<?php echo $iconClass ?>">
								<span class="icon-menu" aria-hidden="true"></span>
							</span>
							<?php if ($saveOrder) : ?>
								<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->lft; ?>" class="width-20 text-area-order" />
							<?php endif; ?>
						</td>
						
						<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						
						<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>">
							<?php echo JHtml::_('jgrid.published', $item->published, $i, 'categories.'); ?>
						</td>
						
						<td>
							<?php echo str_repeat('<span class="gi">&mdash;</span>', $item->level - 1) ?>
							<a href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=category.edit&id=' . $item->id); ?>">
								<?php echo $this->escape($item->title); ?>
							</a>
							
							<span class="small" title="<?php echo $this->escape($item->path); ?>">
								<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
							</span>
						</td>
						
						<td class="small hidden-phone <?php echo RSEventsproAdapterGrid::styles(array('center')); ?>">
							<?php echo $this->escape($item->access_level); ?>
						</td>
						
						<td class="small nowrap hidden-phone <?php echo RSEventsproAdapterGrid::styles(array('center')); ?>">
							<?php if ($item->language == '*') { ?>
								<?php echo JText::alt('JALL', 'language'); ?>
							<?php } else { ?>
								<?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
							<?php } ?>
						</td>
						
						<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?> hidden-phone">
							<span title="<?php echo sprintf('%d-%d', $item->lft, $item->rgt); ?>">
								<?php echo (int) $item->id; ?>
							</span>
						</td>
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
			
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>	
</form>