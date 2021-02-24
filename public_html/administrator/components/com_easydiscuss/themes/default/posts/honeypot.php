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
	<div class="app-filter-bar">
		<div class="app-filter-bar__cell app-filter-bar__cell--empty"></div>

		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left app-filter-bar__cell--last t-text--center">
			<div class="app-filter-bar__filter-wrap app-filter-bar__filter-wrap--limit">
				<?php echo $this->html('table.limit', $limit); ?>
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
						<?php echo JText::_('COM_ED_TABLE_COLUMN_DATA'); ?>
					</th>

					<th width="15%" class="center">
						<?php echo JText::_('COM_ED_TABLE_COLUMN_TYPE'); ?>
					</th>

					<th width="15%" class="center">
						<?php echo JText::_('COM_ED_TABLE_COLUMN_CREATED'); ?>
					</th>

					<th width="1%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_COLUMN_ID');?>
					</th>
				</tr>
			</thead>
			<tbody>

			<?php if ($items) { ?>
				<?php $i = 0; ?>
				<?php foreach ($items as $honeypot) { ?>
				<tr>
					<td class="center">
						<?php echo $this->html('table.checkbox', $i++, $honeypot->id); ?>
					</td>

					<td>
						<a href="javascript:void(0);" data-view-item data-id="<?php echo $honeypot->id;?>"><?php echo JText::_('View Data');?></a>
					</td>

					<td class="center">
						<?php echo $honeypot->type;?>
					</td>

					<td class="center">
						<?php echo $honeypot->created;?>
					</td>

					<td class="center">
						<?php echo $honeypot->id; ?>
					</td>
				</tr>
				<?php } ?>
			<?php } else { ?>
				<tr>
					<td colspan="5" class="center empty">
						<?php echo JText::_('COM_EASYDISCUSS_NO_DISCUSSIONS_YET'); ?>
					</td>
				</tr>
			<?php } ?>
			</tbody>

			<tfoot>
				<tr>
					<td colspan="5">
						<?php echo $pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php echo $this->html('form.action', 'posts', 'honeypot'); ?>
</form>
