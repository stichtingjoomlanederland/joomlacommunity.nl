<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

/** @var JdidealgatewayViewStatuses $this */

if (JVERSION < 4)
{
	HTMLHelper::_('formbehavior.chosen');
}

$listOrdering  = $this->escape($this->state->get('list.ordering'));
$listDirection = $this->escape($this->state->get('list.direction'));
$db            = Factory::getDbo();
?>
<form name="adminForm" id="adminForm" method="post" action="index.php?option=com_jdidealgateway&view=subscriptions">
	<?php if (JVERSION < 4) : ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
	<?php endif; ?>
	<div id="j-main-container" class="span10">
		<?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>
		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items alert-info">
				<?php if (JVERSION >= 4) : ?>
					<span class="fas fa-info-circle" aria-hidden="true"></span><span class="sr-only"><?php echo Text::_('INFO'); ?></span>
				<?php endif; ?>
				<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
			<table class="table table-striped">
				<thead>
					<tr>
						<th><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
						<th><?php echo HTMLHelper::_('searchtools.sort', Text::_('COM_ROPAYMENTS_SUBSCRIPTION_NAME'), 'customers.name', $listDirection, $listOrdering); ?></th>
						<th><?php echo HTMLHelper::_('searchtools.sort', Text::_('COM_ROPAYMENTS_SUBSCRIPTION_STATUS'), 'subscriptions.status', $listDirection, $listOrdering); ?></th>
						<th><?php echo HTMLHelper::_('searchtools.sort', Text::_('COM_ROPAYMENTS_SUBSCRIPTION_CURRENCY'), 'subscriptions.currency', $listDirection, $listOrdering); ?></th>
						<th><?php echo HTMLHelper::_('searchtools.sort', Text::_('COM_ROPAYMENTS_SUBSCRIPTION_AMOUNT'), 'subscriptions.amount', $listDirection, $listOrdering); ?></th>
						<th><?php echo HTMLHelper::_('searchtools.sort', Text::_('COM_ROPAYMENTS_SUBSCRIPTION_TIMES'), 'subscriptions.times', $listDirection, $listOrdering); ?></th>
						<th><?php echo HTMLHelper::_('searchtools.sort', Text::_('COM_ROPAYMENTS_SUBSCRIPTION_INTERVAL'), 'subscriptions.interval', $listDirection, $listOrdering); ?></th>
						<th><?php echo HTMLHelper::_('searchtools.sort', Text::_('COM_ROPAYMENTS_SUBSCRIPTION_DESCRIPTION'), 'subscriptions.description', $listDirection, $listOrdering); ?></th>
						<th><?php echo HTMLHelper::_('searchtools.sort', Text::_('COM_ROPAYMENTS_SUBSCRIPTION_SUBSCRIPTIONID'), 'subscriptions.subscriptionId', $listDirection, $listOrdering); ?></th>
						<th><?php echo HTMLHelper::_('searchtools.sort', Text::_('COM_ROPAYMENTS_SUBSCRIPTION_START'), 'subscriptions.start', $listDirection, $listOrdering); ?></th>
						<th><?php echo HTMLHelper::_('searchtools.sort', Text::_('COM_ROPAYMENTS_SUBSCRIPTION_CANCELLED'), 'subscriptions.cancelled', $listDirection, $listOrdering); ?></th>
						<th><?php echo HTMLHelper::_('searchtools.sort', Text::_('COM_ROPAYMENTS_SUBSCRIPTION_CREATED'), 'subscriptions.created', $listDirection, $listOrdering); ?></th>
					</tr>
				</thead>
				<?php if (JVERSION < 4) : ?>
					<tfoot>
						<tr>
							<td colspan="12"><?php echo $this->pagination->getListFooter(); ?></td>
						</tr>
					</tfoot>
				<?php endif; ?>
				<tbody>
	                <?php if ($this->items) : ?>
					<?php foreach ($this->items as $i => $item) : ?>
							<tr>
								<td>
									<?php echo HTMLHelper::_('grid.checkedout',  $item, $i, 'id'); ?>
								</td>
								<td><?php echo $item->name; ?></td>
								<td><?php echo $item->status; ?></td>
								<td><?php echo $item->currency; ?></td>
								<td><?php echo $item->amount; ?></td>
								<td><?php echo $item->times; ?></td>
								<td><?php echo $item->interval; ?></td>
								<td><?php echo $item->description; ?></td>
								<td><?php echo $item->subscriptionId; ?></td>
								<td><?php echo (new Date($item->start))->format('d-m-Y H:i:s'); ?></td>
								<td>
									<?php
										if ($item->cancelled !== '1000-01-01' && $item->cancelled !== '0000-00-00' && $item->cancelled !== $db->getNullDate())
										{
											echo HTMLHelper::_('date', $item->cancelled, 'd-m-Y H:i:s');
										}
									?>
								</td>
								<td><?php echo HTMLHelper::_('date', $item->created, 'd-m-Y H:i:s'); ?></td>
							</tr>
					<?php endforeach; ?>
	                <?php endif; ?>
				</tbody>
			</table>
			<?php if (JVERSION >= 4) : ?>
				<?php echo $this->pagination->getListFooter(); ?>
			<?php endif; ?>
		<?php endif; ?>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>
