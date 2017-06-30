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
<div data-ed-reports>
	<form action="index.php" method="post" name="adminForm" id="adminForm" data-ed-form>
		<div class="post-app-filter-bar">
			<div class="app-filter-bar" style="display: none;">
			</div>

			<div class="app-filter-bar">
				<div class="app-filter-bar__cell">
					<div class="form-inline">
						<div class="form-group">
							<div class="app-filter-select-group">
								<?php echo $this->html('table.filter', 'filter_state', $filter_state, array('P' => 'COM_EASYDISCUSS_PUBLISHED', 'U' => 'COM_EASYDISCUSS_UNPUBLISHED')); ?>
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

		<div class="panel-table">
			<table class="app-table app-table-middle table table-striped" data-ed-table>
				<thead>
					<tr>
						<td width="1%">
							<?php echo $this->html('table.checkall'); ?>
						</td>

						<td style="text-align:left;">
							<?php echo JText::_('COM_EASYDISCUSS_REPORTED_REASON'); ?>
						</td>
						<td width="15%" class="center">
							<?php echo JText::_('COM_EASYDISCUSS_LAST_REPORTED_BY'); ?>
						</td>
						<td width="10%" class="center">
							<?php echo JText::_('COM_EASYDISCUSS_NUM_REPORT');?>
						</td>
						<td width="10%" class="center">
							<?php echo JText::_('COM_EASYDISCUSS_LAST_REPORT_DATE'); ?>
						</td>
						<td width="1%" class="center">
							<?php echo JText::_('COM_EASYDISCUSS_REPORT_PUBLISHED'); ?>
						</td>
						<td width="30%" class="center">
							<?php echo JText::_( 'COM_EASYDISCUSS_ACTION' ); ?>
						</td>
						<td width="6%" class="center">
							<?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_ID'), 'a.id', $orderDirection, $order); ?>
						</td>
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
							<a href="<?php echo $report->viewLink;?>" target="_blank">
								<?php echo JText::_('COM_EASYDISCUSS_VIEW_POST'); ?>
							</a>
						</td>

						<td class="center">
							<?php if ($report->user_id) { ?>
								<?php echo $report->user->name; ?>
							<?php } else { ?>
								<?php echo JText::_('COM_EASYDISCUSS_GUEST'); ?>
							<?php } ?>
						</td>

						<td class="center">
							<?php if ($report->reportCnt > 1) { ?>
								<a href="javascript:void(0);" data-reports-preview data-id="<?php echo $report->id;?>"><?php echo $report->reportCnt;?></a>
							<?php } else { ?>
								<?php echo $report->reportCnt; ?>
							<?php } ?>
						</td>

						<td class="center">
							<?php echo ED::date($report->date)->display(JText::_('DATE_FORMAT_LC2')); ?>
						</td>

						<td class="center">
							<?php echo $this->html('table.publish', $report, $i-1); ?>
						</td>

						<td align="left">
							<div data-ed-report-item data-id="<?php echo $report->id;?>" id="action-container-<?php echo $report->id;?>">
								<?php echo $report->actions; ?>

								<input type="button" class="btn btn-primary" name="actions-btn-<?php echo $report->id;?>"
									data-ed-report-button
									data-id="<?php echo $report->id;?>"
									onclick="return false;"
									value="Submit"
								/>

								<div data-ed-report-msg>
								</div>

								<div data-ed-report-email style="display:none;">
									<br />
									<div><?php echo JText::_('COM_EASYDISCUSS_YOUR_TEXT'); ?> : </div>
									<textarea data-ed-report-textarea name="email_text" id="email-text-<?php echo $report->id;?>" class="inputbox textarea" style="width:300px;"></textarea>
								</div>
							</div>
						</td>
						<td align="center">
							<?php echo $report->id; ?>
						</td>
					</tr>
					<?php } ?>
				<?php } else { ?>
					<tr>
						<td colspan="9" class="center">
							<?php echo JText::_('COM_EASYDISCUSS_NO_REPORTS');?>
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

		<input type="hidden" id="post_id" name="post_id" value="" />
		<input type="hidden" id="post_val" name="post_val" value="" />
		<input type="hidden" name="filter_order" value="<?php echo $order; ?>" />
		<input type="hidden" name="filter_order_Dir" value="" />

		<?php echo $this->html('form.hidden', 'reports', 'reports'); ?>
	</form>
</div>
