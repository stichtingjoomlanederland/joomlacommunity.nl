<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" data-ed-form>
	<?php if (!$browse) { ?>
	<div class="app-filter-bar">
		<div class="app-filter-bar__cell app-filter-bar__cell--search">
			<?php echo $this->html('table.search', 'search', $search); ?>
		</div>

		<div class="app-filter-bar__cell app-filter-bar__cell--auto-size app-filter-bar__cell--divider-left">
			<div class="app-filter-bar__filter-wrap">
				<?php echo $this->html('table.filter', 'filter_state', $state, array('P' => 'COM_EASYDISCUSS_PUBLISHED', 'U' => 'COM_EASYDISCUSS_UNPUBLISHED')); ?>
			</div>
		</div>

		<div class="app-filter-bar__cell app-filter-bar__cell--empty"></div>

		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left app-filter-bar__cell--last t-text--center">
			<div class="app-filter-bar__filter-wrap app-filter-bar__filter-wrap--limit">
				<?php echo $this->html('table.limit', $pagination->limit); ?>
			</div>
		</div>
	</div>
	<?php } ?>

	<div class="panel-table">
		<table class="app-table table" data-ed-table>
			<thead>
				<tr>
					<th width="1%" class="center">
						<?php echo $this->html('table.checkall'); ?>
					</th>
					<th style="text-align:left;">
						<?php echo JText::_('COM_ED_POST_LABEL_TITLE_COLUMN');?>
					</th>
					<th width="5%" class="center">
						<?php echo JText::_('COM_ED_POST_LABEL_PUBLISHED_COLUMN');?>
					</th>
				
					<th width="10%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_TABLE_COLUMN_COLOR'); ?>
					</th>
				
					<th width="10%" class="center">
						<?php echo JText::_('COM_ED_POST_LABEL_CREATED_COLUMN'); ?>
					</th>
					
					<th width="5%" class="center">
						<?php echo JHTML::_('grid.sort', JText::_('COM_ED_POST_LABEL_ID_COLUMN'), 'a.id', $orderDirection, $order); ?>
					</th>
				</tr>
			</thead>

		<tbody>
		<?php if ($labels) { ?>
			<?php $i = 0; ?>
			<?php foreach ($labels as $label) { ?>
				<tr>
					<td class="center">
						<?php echo $this->html('table.checkbox', $i++, $label->id); ?>
					</td>

					<td style="text-align:left;">
						<a href="<?php echo 'index.php?option=com_easydiscuss&view=labels&layout=form&id=' . $label->id; ?>">
							<?php echo $label->title; ?>
						</a>
					</td>

					<td class="center">
						<?php echo $this->html('table.state', 'labels', $label, 'published'); ?>
					</td>
					
					<td class="center">
						<?php echo $this->html('string.bubble', $label->colour); ?>
					</td>

					<td class="center">
						<?php echo $label->created; ?>
					</td>

					<td class="center">
						<?php echo $label->id;?>
					</td>
				</tr>
			<?php } ?>
		<?php } else { ?>
			<tr>
				<td colspan="9" class="center">
					<?php echo JText::_('COM_ED_NO_POST_LABELS_YET');?>
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
	<input type="hidden" name="filter_order_Dir" value="" />

	<?php echo $this->html('form.action', 'labels', 'labels'); ?>
</form>