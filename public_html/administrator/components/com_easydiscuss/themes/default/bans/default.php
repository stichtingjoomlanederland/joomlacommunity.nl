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
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_BANNED_USERNAME') , 'banned_username', $orderDirection, $order); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_BANNED_IP_ADDRESS'); ?>
					</th>
					<th style="text-align:left;">
						<?php echo JText::_('COM_EASYDISCUSS_BANNED_REASON'); ?>
					</th>
					<th width="15%" class="center">
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_BANNED_CREATED_BY') , 'created_by', $orderDirection, $order); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_BANNED_STARTED_DATE'); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_BANNED_ENDED_DATE'); ?>
					</th>
					<th width="6%" class="center">
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_BANNED_BLOCKED_STATUS') , 'blocked', $orderDirection, $order); ?>
					</th>
					<th width="6%" class="center">
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_BANNED_ID') , 'id', $orderDirection, $order); ?>
					</th>
				</tr>
			</thead>

			<tbody>
				<?php if ($bans) { ?>
					<?php $i = 0; ?>
					<?php foreach ($bans as $ban) { ?>
					<tr>
						<td class="center">
							<?php echo $this->html('table.checkbox', $i++, $ban->id); ?>
						</td>
						<td style="text-align:left;">
							<?php echo $ban->banned_username; ?>
						</td>
						<td class="center">
							<?php echo $ban->ip; ?>
						</td>

						<td style="text-align:left;">
							<?php echo $ban->reason; ?>
						</td>
						<td class="center">
							<?php echo $ban->created_by; ?>
						</td>
						<td class="center">
							<?php echo $ban->start;?>
						</td>
						<td class="center">
							<?php echo $ban->end;?>
						</td>
						<td class="center">
							<?php echo $ban->blocked;?>
						</td>
						<td class="center">
							<?php echo $ban->id;?>
						</td>
					</tr>
					<?php } ?>
				<?php } else { ?>
					<tr>
						<td colspan="9" class="empty">
							<?php echo JText::_('COM_EASYDISCUSS_BANS_NOT_CREATED');?>
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

	<?php echo $this->html('form.hidden', 'bans', 'bans'); ?>
</form>
