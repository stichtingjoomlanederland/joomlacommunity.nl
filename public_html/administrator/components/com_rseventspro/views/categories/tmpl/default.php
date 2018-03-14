<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
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
	JHtml::_('sortablelist.sortable', 'categoriesList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
} ?>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=categories'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row-fluid">
		<div id="j-sidebar-container" class="span2">
			<?php echo JHtmlSidebar::render(); ?>
		</div>
		<div id="j-main-container" class="span10 j-main-container">
			<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
			
			<?php if (empty($this->items)) { ?>
			<div class="alert alert-no-items">
				<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
			<?php } else { ?>
				<table class="table table-striped adminlist" id="categoriesList">
					<thead>
						<tr>
							<th width="1%" class="nowrap center hidden-phone">
								<?php echo JHtml::_('searchtools.sort', '', 'a.lft', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
							</th>
							<th width="1%">
								<input type="checkbox" name="checkall-toggle" value="" class="<?php echo rseventsproHelper::tooltipClass(); ?>" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this);"/>
							</th>
							<th width="1%" class="nowrap center">
								<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
							</th>
							<th>
								<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
							</th>
							<th width="10%" class="nowrap hidden-phone center" align="center">
								<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
							</th>
							<th width="5%" class="nowrap hidden-phone center" align="center">
								<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'language_title', $listDirn, $listOrder); ?>
							</th>
							<th width="1%" class="nowrap hidden-phone center" align="center">
								<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
							</th>
						</tr>
					</thead>
					<tbody id="rseprocontainer">
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
							<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->parent_id; ?>" item-id="<?php echo $item->id ?>" parents="<?php echo $parentsStr ?>" level="<?php echo $item->level ?>">
								<td class="order nowrap center hidden-phone">
									<?php $iconClass = !$saveOrder ? ' inactive tip-top hasTooltip" title="' . JHtml::_('tooltipText', 'JORDERINGDISABLED') : ''; ?>
									<span class="sortable-handler<?php echo $iconClass ?>">
										<span class="icon-menu" aria-hidden="true"></span>
									</span>
									<?php if ($saveOrder) : ?>
										<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->lft; ?>" class="width-20 text-area-order" />
									<?php endif; ?>
								</td>
								
								<td class="center">
									<?php echo JHtml::_('grid.id', $i, $item->id); ?>
								</td>
								
								<td class="center">
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
								
								<td class="small hidden-phone center" align="center">
									<?php echo $this->escape($item->access_level); ?>
								</td>
								
								<td class="small nowrap hidden-phone center" align="center">
									<?php if ($item->language == '*') { ?>
										<?php echo JText::alt('JALL', 'language'); ?>
									<?php } else { ?>
										<?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
									<?php } ?>
								</td>
								
								<td class="center hidden-phone center" align="center">
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
			<?php } ?>
			
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</div>
</form>