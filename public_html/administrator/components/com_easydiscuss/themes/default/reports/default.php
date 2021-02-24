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
<div data-ed-reports>
	<form action="index.php" method="post" name="adminForm" id="adminForm" data-ed-form>
		<div class="panel-table">
			<table class="app-table table" data-ed-table>
				<thead>
					<tr>
						<th width="1%">
							<?php echo $this->html('table.checkall'); ?>
						</th>

						<th style="text-align:left;">
							<?php echo JText::_('COM_EASYDISCUSS_REPORTED_REASON'); ?>
						</th>

						<th width="10%" class="center">
							<?php echo JText::_('COM_ED_TABLE_COLUMN_TYPE'); ?>
						</th>
						<th width="20%" class="center">
							<?php echo JText::_('COM_EASYDISCUSS_NUM_REPORT');?>
						</th>
						<th width="10%" class="center">
							<?php echo JText::_('COM_EASYDISCUSS_LAST_REPORT_DATE'); ?>
						</th>
						<th width="5%" class="center">
							<?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_ID'), 'a.id', $orderDirection, $order); ?>
						</th>
					</tr>
				</thead>
				<tbody>

				<?php if ($reports) { ?>
					<?php $i = 0; ?>
					<?php foreach ($reports as $report) { ?>
					<tr>
						<td class="center">
							<?php echo $this->html('table.checkbox', $i++, $report->id); ?>
						</td>

						<td align="left">
							<?php echo $this->escape($report->reason);?>
							
							<div class="t-mt--sm">
								<?php echo JText::_('Post');?>:
								<a href="index.php?option=com_easydiscuss&view=posts&layout=redirectPost&id=<?php echo $report->id; ?>" target="_blank">
									<?php echo $report->post->getTitle();?>
								</a>
							</div>
						</td>

						<td class="center">
							<?php if ($report->post->isQuestion()) { ?>
								<span class="o-label o-label--primary"><?php echo JText::_('COM_ED_TYPE_QUESTION'); ?></span>
							<?php } else { ?>
								<span class="o-label o-label--info"><?php echo JText::_('COM_ED_TYPE_REPLY'); ?></span>
							<?php } ?>
						</td>

						<td class="center">
							<a href="javascript:void(0);" class="o-btn o-btn--default-o o-btn--sm" data-reports-preview data-id="<?php echo $report->id;?>">
								<?php echo JText::_('COM_ED_VIEW_REPORTS');?>
							</a>
						</td>

						<td class="center">
							<?php echo ED::date($report->date)->toSql(); ?>
						</td>

						<td class="center">
							<?php echo $report->id; ?>
						</td>
					</tr>
					<?php } ?>
				<?php } else { ?>
					<tr>
						<td colspan="6" class="center">
							<?php echo JText::_('COM_EASYDISCUSS_NO_REPORTS');?>
						</td>
					</tr>
				<?php } ?>
				</tbody>

				<tfoot>
					<tr>
						<td colspan="6" class="center">
							<?php echo $pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
			</table>
		</div>

		<input type="hidden" name="filter_order" value="<?php echo $order; ?>" />
		<input type="hidden" name="filter_order_Dir" value="" />

		<?php echo $this->html('form.action', 'reports', 'reports'); ?>
	</form>
</div>

<?php echo $this->output('admin/reports/actions'); ?>