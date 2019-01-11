<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

$saveOrder = ($order == 'lft' && $orderDirection == 'asc');
$originalOrders	= array();
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" data-ed-form>
	<div class="app-filter-bar">
		<div class="app-filter-bar__cell app-filter-bar__cell--search">
			<?php echo $this->html('table.search', 'search', $search); ?>
		</div>

		<div class="app-filter-bar__cell app-filter-bar__cell--auto-size app-filter-bar__cell--divider-left">
			<div class="app-filter-bar__filter-wrap">
				<?php echo $this->html('table.filter', 'filter_state', $state, array('P' => 'COM_EASYDISCUSS_PUBLISHED', 'U' => 'COM_EASYDISCUSS_UNPUBLISHED')); ?>
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
					<th width="1%">
						<?php echo $this->html('table.checkall'); ?>
					</th>
					<th style="text-align:left;">
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_CATEGORIES_CATEGORY_TITLE') , 'title', $orderDirection, $order); ?>
					</th>
					<th width="5%" class="center">
						<?php echo JText::_('COM_EASYDSCUSS_CATEGORIES_DEFAULT'); ?>
					</th>
					<th width="5%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_PUBLISHED'); ?>
					</th>
					<?php if (count($categories) > 1) { ?>
					<th width="10%" class="center">
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_ORDER'), 'lft', 'desc', $order); ?>
						<?php echo JHTML::_('grid.order', $categories); ?>
					</th>
					<?php } ?>
					<th width="5%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_ENTRIES'); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_CHILD_COUNT'); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_EDIT_CATEGORY_LANGUAGE'); ?>
					</th>
					<th width="8%" class="center">
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_CATEGORIES_AUTHOR') , 'created_by', $orderDirection, $order); ?>
					</th>
					<th width="1%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_ID'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if ($categories) { ?>
					<?php $i = 0; ?>
					<?php foreach ($categories as $category) { ?>
					<?php $orderkey	= array_search($category->id, $ordering[$category->parent_id]);?>
					<tr>
						<td class="center">
							<?php echo $this->html('table.checkbox', $i++, $category->id); ?>
						</td>

						<td style="text-align:left;">
							<?php echo str_repeat('|&mdash;', $category->depth); ?>
							<span><a href="<?php echo $category->link; ?>"><?php echo $category->title; ?></a></span>
						</td>

						<td class="center">
							<?php echo $this->html('table.featured', 'categories', $category, 'default', 'makeDefault'); ?>
						</td>
						<td class="center">
							<?php echo $this->html('table.state', 'categories', $category, 'published'); ?>
						</td>
						<?php if (count($categories) > 1) { ?>
						<td class="order center">
							<?php echo $this->html( 'table.ordering', 'order', $orderkey + 1, count($ordering[$category->parent_id]), true); ?>
							<?php $originalOrders[] = $orderkey + 1; ?>
						</td>
						<?php } ?>
						<td class="center">
							<?php echo $category->count;?>
						</td>
						<td class="center">
							<?php echo $category->child_count; ?>
						</td>
						<td class="center">
							<?php if (!$category->language || $category->language == '*') { ?>
								<?php echo JText::_('COM_ED_LANGUAGE_ALL');?>
							<?php } else { ?>
								<?php echo $category->language;?>
							<?php } ?>
						</td>
						<td class="center">
							<a href="<?php echo JRoute::_('index.php?option=com_easydiscuss&controller=user&id=' . $category->created_by . '&task=edit'); ?>"><?php echo $category->user->name; ?></a>
						</td>
						<td class="center">
							<?php echo $category->id;?>
						</td>
					</tr>
					<?php } ?>
				<?php } else { ?>
					<tr>
						<td colspan="9" class="center">
							<?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_NO_CATEGORY_CREATED_YET');?>
						</td>
					</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="9">
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

	<?php echo $this->html('form.hidden', 'category', 'categories'); ?>
</form>
