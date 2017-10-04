<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

$saveOrder = ($order == 'lft' && $orderDirection == 'asc');
$originalOrders	= array();

?>
<form action="index.php" method="post" name="adminForm" id="adminForm" data-ed-form>
	<?php if (!$browse) { ?>
	<div class="post-app-filter-bar">
		<div class="app-filter-bar">
			<?php echo $this->html('table.search', 'search', $search); ?>
		</div>

		<div class="app-filter-bar">
			<div class="app-filter-bar__cell">
				<div class="form-inline">
					<div class="form-group">
						<div class="app-filter-select-group">
							<?php echo $this->html('table.filter', 'filter_state', $state, array('P' => 'COM_EASYDISCUSS_PUBLISHED', 'U' => 'COM_EASYDISCUSS_UNPUBLISHED')); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="app-filter-bar__cell app-filter-bar__cell--last">
				<div class="form-inline">
					<div class="app-filter-select-group">
						<?php echo $this->html('table.limit', $pagination); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>

	<div class="panel-table">
		<table class="app-table app-table-middle table table-striped" data-ed-table>
			<thead>
				<tr>
					<td width="1%" class="center">
						<?php echo $this->html('table.checkall'); ?>
					</td>
					<td style="text-align:left;">
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_ADMIN_POST_TYPES_TITLE'), 'a.title', $orderDirection, $order); ?>
					</td>
					<td width="5%" class="center">
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_ADMIN_POST_TYPES_PUBLISHED'), 'a.published', $orderDirection, $order); ?>
					</td>
					<?php if (count($types) > 1) { ?>
					<td width="10%" class="center">
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_ORDER'), 'lft', 'desc', $order); ?>
						<?php echo JHTML::_('grid.order', $types); ?>
					</td>
					<?php } ?>
					<td width="10%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_TABLE_COLUMN_TYPE');?>
					</td>
					<td width="10%" class="center">
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_ADMIN_POST_TYPES_SUFFIX'), 'a.suffix', $orderDirection, $order); ?>
					</td>
					<td width="10%" class="center">
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_ADMIN_POST_TYPES_CREATED'), 'a.created', $orderDirection, $order); ?>
					</td>
					<td width="10%" class="center">
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_ADMIN_POST_TYPES_ALIAS'), 'a.alias', $orderDirection, $order); ?>
					</td>
					<td width="5%" class="center">
						<?php echo JHTML::_('grid.sort', JText::_('Id'), 'a.id', $orderDirection, $order); ?>
					</td>
				</tr>
			</thead>

		<tbody>
		<?php if ($types) { ?>
			<?php $i = 0; ?>
			<?php foreach ($types as $type) { ?>
			<?php $orderkey	= array_search($type->id, $ordering);?>
				<tr>
					<td class="center">
						<?php echo $this->html('table.checkbox', $i++, $type->id); ?>
					</td>

					<td style="text-align:left;">
						<a href="<?php echo 'index.php?option=com_easydiscuss&view=types&layout=form&id=' . $type->id; ?>"><?php echo $type->title; ?></a>
					</td>

					<td class="center">
						<?php echo $this->html('table.state', 'types', $type, 'published'); ?>
					</td>

					<?php if (count($types) > 1) { ?>
					<td class="order">
						<?php echo $this->html('table.ordering', 'order', $orderkey + 1, count($ordering), true); ?>
						<?php $originalOrders[] = $orderkey + 1; ?>
					</td>
					<?php } ?>
					
					<td class="center">
						<?php
						if (!$type->type) {
							$type->type = 'global';
						}
						?>
						<?php echo JText::_('COM_EASYDISCUSS_POST_TYPES_TYPE_' . strtoupper($type->type)); ?>
					</td>

					<td class="center">	
						<?php if ($type->suffix) { ?>
							<?php echo $type->suffix; ?>
						<?php } else { ?>
							<?php echo JText::_('COM_EASYDISCUSS_NOT_AVAILABLE'); ?>
						<?php } ?>
					</td>

					<td class="center">
						<?php echo $type->created; ?>
					</td>

					<td class="center">
						<?php echo $type->alias; ?>
					</td>
					<td class="center">
						<?php echo $type->id;?>
					</td>
				</tr>
			<?php } ?>
		<?php } else { ?>
			<tr>
				<td colspan="7" class="center">
					<?php echo JText::_('COM_EASYDISCUSS_NO_POST_TYPES_YET');?>
				</td>
			</tr>
		<?php } ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="7">
					<div class="footer-pagination center">
						<?php echo $pagination->getListFooter(); ?>
					</div>
				</td>
			</tr>
		</tfoot>
		</table>
	</div>

	<input type="hidden" name="original_order_values" value="<?php echo implode($originalOrders, ','); ?>" />
	<input type="hidden" name="filter_order" value="<?php echo $order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />

	<?php echo $this->html('form.hidden', 'post_types', 'types'); ?>
</form>