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
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" data-ed-form>
	<div class="app-filter-bar">
		<div class="app-filter-bar__cell app-filter-bar__cell--search">
			<?php echo $this->html('table.search', 'search', $search); ?>
		</div>

		<div class="app-filter-bar__cell app-filter-bar__cell--auto-size app-filter-bar__cell--divider-left">
			<div class="app-filter-bar__filter-wrap">
				<?php echo $this->html('table.filter', 'filter_state', $filter, array('P' => 'COM_EASYDISCUSS_PUBLISHED', 'U' => 'COM_EASYDISCUSS_UNPUBLISHED')); ?>
			</div>
		</div>

		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left"></div>

		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left app-filter-bar__cell--last t-text--center">
			<div class="app-filter-bar__filter-wrap app-filter-bar__filter-wrap--limit">
				<?php echo $this->html('table.limit', $pagination->limit); ?>
			</div>
		</div>
	</div>

	<div class="panel-table">
		<table class="app-table table" data-ed-table>
			<thead>
				<tr>
					<th width="1%" class="center">
						<?php echo $this->html('table.checkall'); ?>
					</th>
					<th style="text-align:left;">
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_CUSTOMFIELDS_TITLE') , 'title', $orderDirection, $order); ?>
					</th>
					<th width="3%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_PUBLISHED'); ?>
					</th>
					<th width="12%" class="center">
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_CUSTOMFIELDS_TYPE') , 'type', $orderDirection, $order); ?>
					</th>
					<th width="8%" class="center">
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_CUSTOMFIELDS_SECTION') , 'type', $orderDirection, $order); ?>
					</th>
					<th width="20%" class="center">
						<?php echo JHtml::_('grid.sort', JText::_('COM_EASYDISCUSS_ORDERING'), 'a.ordering', $orderDirection, $order); ?>
					</th>
					<th width="1%" class="center">
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_ID') , 'id', $orderDirection, $order); ?>
					</th>
				</tr>
			</thead>

			<tbody>
			<?php if ($fields) { ?>
				<?php $i = 0; ?>
				<?php foreach ($fields as $field) { ?>
				<tr>
					<td class="center">
						<?php echo $this->html('table.checkbox', $i++, $field->id); ?>
					</td>
					<td>
						<a href="<?php echo JRoute::_('index.php?option=com_easydiscuss&view=customfields&layout=form&id='. $field->id); ?>"><?php echo $field->title; ?></a>
					</td>
					
					<td class="center">
						<?php echo $this->html('table.state', 'customfields', $field, 'published'); ?>
					</td>
					
					<td class="center">
						<?php echo $field->getFriendlyType(); ?>
					</td>

					<td class="center">
						<?php echo $field->getSection(); ?>
					</td>

					<td class="order center">
						<?php echo $this->html('table.ordering', 'order', $field->orderingIndex, count($fields), $allowOrdering); ?>
					</td>
					<td class="center">
						<?php echo $field->id; ?>
					</td>
				</tr>
				<?php } ?>
			<?php } else { ?>
				<tr>
					<td colspan="7" class="empty">
						<i class="fa fa-table"></i>
						<?php echo JText::_('COM_EASYDISCUSS_NO_CUSTOM_FIELDS_CREATED_YET'); ?>
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

	<input type="hidden" name="filter_order" value="<?php echo $order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $orderDirection; ?>" />
	<input type="hidden" name="original_order_values" value="<?php echo implode($originalOrders, ','); ?>" />

	<?php echo $this->html('form.hidden', 'customfields', 'customfields'); ?>

</form>
