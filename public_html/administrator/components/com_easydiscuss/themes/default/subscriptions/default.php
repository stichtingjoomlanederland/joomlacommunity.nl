<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
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
		<div class="app-filter-bar__cell app-filter-bar__cell--search">
			<?php echo $this->html('table.search', 'search', $search); ?>
		</div>

		<div class="app-filter-bar__cell app-filter-bar__cell--auto-size app-filter-bar__cell--divider-left">
			<div class="app-filter-bar__filter-wrap">
				<?php echo $this->html('table.filter', 'filter', $filter, array('site' => 'COM_EASYDISCUSS_SITE_OPTION', 'category' => 'COM_EASYDISCUSS_CATEGORY_OPTION', 'post' => 'COM_EASYDISCUSS_POST_OPTION')); ?>
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

					<th style="text-align: left;">
						<?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBER_EMAIL'); ?>
					</th>

					<?php if ($filter == 'post') { ?>
					<th class="center">
						<?php echo JHTML::_('grid.sort', 'COM_EASYDISCUSS_DISCUSSION_TITLE', 'bname', $orderDirection, $order); ?>
					</th>
					<?php } ?>

					<?php if ($filter == 'category') { ?>
					<th class="center">
						<?php echo JHTML::_('grid.sort', 'COM_EASYDISCUSS_CATEGORY_TITLE', 'c.title', $orderDirection, $order); ?>
					</th>
					<?php } ?>

					<th width="20%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_TABLE_COLUMN_DATE'); ?>
					</th>

					<th width="1%" class="center">
						<?php echo JHTML::_('grid.sort', 'COM_EASYDISCUSS_ID', 'a.id', $orderDirection, $order ); ?>
					</th>
				</tr>
			</thead>

			<tbody>
			<?php if ($subscriptions) { ?>
				<?php $i = 0; ?>
				<?php foreach ($subscriptions as $subscription) { ?>
				<tr>
					<td class="center">
						<?php echo $this->html('table.checkbox', $i++, $subscription->id); ?>
					</td>

					<td>
						<a href="index.php?option=com_easydiscuss&view=subscription&layout=form&id=<?php echo $subscription->id;?>"><?php echo $subscription->email;?></a> 
						(<?php echo (empty($subscription->name)) ? $subscription->fullname :  $subscription->name;?>)
					</td>

					<?php if ($filter != 'site') { ?>
					<td class="center">
						<?php echo $subscription->bname;?>						
					</td>
					<?php } ?>

					<td class="center">
						<?php echo $subscription->created; ?>
					</td>

					<td class="center">
						<?php echo $subscription->id;?>
					</td>
				</tr>
				<?php } ?>
			<?php } else { ?>
				<tr>
					<td colspan="<?php echo $filter != 'site' ? 6 : 5;?>" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_NO_SUBSCRIPTION_FOUND');?>
					</td>
				</tr>
			<?php } ?>
			</tbody>

			<tfoot>
				<tr>
					<td colspan="<?php echo $filter != 'site' ? 6 : 5;?>">
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

	<?php echo $this->html('form.hidden', 'subscription', 'subscription'); ?>
</form>
