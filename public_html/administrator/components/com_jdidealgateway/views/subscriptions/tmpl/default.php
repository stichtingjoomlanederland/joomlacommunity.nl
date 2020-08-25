<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

defined('_JEXEC') or die;

/** @var JdidealgatewayViewStatuses $this */

HTMLHelper::_('formbehavior.chosen');

$listOrdering  = $this->escape($this->state->get('list.ordering'));
$listDirection = $this->escape($this->state->get('list.direction'));
$db            = Factory::getDbo();
?>
<form name="adminForm" id="adminForm" method="post" action="index.php?option=com_jdidealgateway&view=subscriptions">
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<?php
		echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
		?>
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
			<tfoot>
				<tr>
					<td colspan="12"><?php echo $this->pagination->getListFooter(); ?></td>
				</tr>
			</tfoot>
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
									if ($item->cancelled !== $db->getNullDate() && $item->cancelled !== '1000-01-01 00:00:00')
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
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>
