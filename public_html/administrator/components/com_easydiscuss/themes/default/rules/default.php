<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
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
				<?php echo $this->html('table.limit', $pagination); ?>
			</div>
		</div>
	</div>

	<div class="app-content-table">
		<table class="app-table table" data-ed-table>
			<thead>
				<tr>
					<th width="1%" class="center">
						<?php echo $this->html('table.checkall'); ?>					
					</th>
					<th class="title" style="text-align:left;">
						<?php echo JHTML::_('grid.sort', 'COM_ED_COLUMN_TITLE', 'a.title', $orderDirection, $order); ?>
					</th>
					<th width="20%" class="text-center"><?php echo JText::_('COM_ED_LABEL_COMMAND'); ?></th>
					<th width="20%" class="text-center">
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_CREATED'), 'a.created', $orderDirection, $order); ?>
					</th>
					<th width="1%" class="text-center">
						<?php echo JHTML::_('grid.sort', 'ID', 'a.id', $orderDirection, $order); ?>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php if ($rules) { ?>
				<?php $i = 0; ?>
				<?php foreach ($rules as $rule) { ?>
				<tr>
					<td width="1%" class="center">
						<?php echo $this->html('table.checkbox', $i++, $rule->id); ?>
					</td>
					<td>
						<?php echo $rule->title; ?>
					</td>
					<td class="center">
						<?php echo $rule->command;?>
					</td>
					<td class="center">
						<?php echo ED::date($rule->created)->toMySQL(true);?>
					</td>
					<td class="center">
						<?php echo $rule->id; ?>
					</td>
				</tr>
				<?php } ?>
			<?php } else { ?>
				<tr>
					<td colspan="6" align="center">
						<?php echo JText::_('COM_ED_NO_RULES_CREATED');?>
					</td>
				</tr>
			<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="10">
						<div class="footer-pagination">
							<?php echo $pagination->getPagesLinks(); ?>
						</div>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_easydiscuss" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="rules" />
	<input type="hidden" name="view" value="rules" />
	<input type="hidden" name="filter_order" value="<?php echo $order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<?php echo JHTML::_('form.token'); ?>
</form>
