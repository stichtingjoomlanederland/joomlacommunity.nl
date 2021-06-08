<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Mollie\Api\Resources\Mandate;
use Mollie\Api\Resources\Subscription;

defined('_JEXEC') or die;

/** @var JdidealgatewayViewCustomer $this */

HTMLHelper::_('formbehavior.chosen');
?>
<form action="index.php?option=com_jdidealgateway&layout=edit&id=<?php echo (int) $this->item->id; ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
	<div class="span12">
		<?php echo $this->form->renderFieldset('customer'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>

<h2><?php echo Text::_('COM_ROPAYMENTS_CUSTOMER_MANDATES'); ?></h2>
<table class="table table-striped table-condensed">
	<thead>
	<tr>
		<th><?php echo Text::_('COM_ROPAYMENTS_MANDATE_ID'); ?></th>
		<th><?php echo Text::_('COM_ROPAYMENTS_MANDATE_STATUS'); ?></th>
		<th><?php echo Text::_('COM_ROPAYMENTS_MANDATE_METHOD'); ?></th>
		<th><?php echo Text::_('COM_ROPAYMENTS_MANDATE_SIGNATURE_DATE'); ?></th>
		<th><?php echo Text::_('COM_ROPAYMENTS_MANDATE_NAME'); ?></th>
		<th><?php echo Text::_('COM_ROPAYMENTS_MANDATE_ACCOUNT'); ?></th>
		<th><?php echo Text::_('COM_ROPAYMENTS_MANDATE_BIC'); ?></th>
		<th><?php echo Text::_('COM_ROPAYMENTS_MANDATE_CARDLABEL'); ?></th>
		<th><?php echo Text::_('COM_ROPAYMENTS_MANDATE_CARD_EXPIRYDATE'); ?></th>
	</tr>
	</thead>
    <tbody>
	<?php /** @var Mandate $subscription */ ?>
	<?php foreach ($this->mandates as $mandate) : ?>
		<tr>
			<td><?php echo $mandate->id; ?></td>
			<td><?php echo $mandate->status; ?></td>
			<td><?php echo $mandate->method; ?></td>
			<td><?php echo HTMLHelper::_('date', $mandate->signatureDate, Text::_('DATE_FORMAT_LC6')); ?></td>
			<?php if ($mandate->method === 'creditcard') : ?>
				<td><?php echo $mandate->details->cardHolder ?? ''; ?></td>
				<td>**** **** **** <?php echo $mandate->details->cardNumber ?? ''; ?></td>
				<td></td>
				<td><?php echo $mandate->details->cardLabel ?? ''; ?></td>
				<td><?php echo $mandate->details->cardExpiryDate ?? ''; ?></td>
			<?php else : ?>
				<td><?php echo $mandate->details->consumerName ?? ''; ?></td>
				<td><?php echo $mandate->details->consumerAccount ?? ''; ?></td>
				<td><?php echo $mandate->details->consumerBic ?? ''; ?></td>
				<td></td>
				<td></td>
			<?php endif; ?>
		</tr>
	<?php endforeach; ?>
    </tbody>
</table>

<h2><?php echo Text::_('COM_ROPAYMENTS_SUBSCRIPTIONS'); ?></h2>
<table class="table table-striped table-condensed">
	<thead>
	<tr>
		<th><?php echo Text::_('COM_ROPAYMENTS_SUBSCRIPTION_ID'); ?></th>
        <th><?php echo Text::_('COM_ROPAYMENTS_SUBSCRIPTION_STATUS'); ?></th>
        <th><?php echo Text::_('COM_ROPAYMENTS_SUBSCRIPTION_CURRENCY'); ?></th>
        <th><?php echo Text::_('COM_ROPAYMENTS_SUBSCRIPTION_AMOUNT'); ?></th>
        <th><?php echo Text::_('COM_ROPAYMENTS_SUBSCRIPTION_TIMES'); ?></th>
        <th><?php echo Text::_('COM_ROPAYMENTS_SUBSCRIPTION_INTERVAL'); ?></th>
        <th><?php echo Text::_('COM_ROPAYMENTS_SUBSCRIPTION_DESCRIPTION'); ?></th>
        <th><?php echo Text::_('COM_ROPAYMENTS_SUBSCRIPTION_START_DATE'); ?></th>
        <th><?php echo Text::_('COM_ROPAYMENTS_SUBSCRIPTION_CANCELLED'); ?></th>
        <th><?php echo Text::_('COM_ROPAYMENTS_SUBSCRIPTION_SIGNATURE_DATE'); ?></th>
	</tr>
	</thead>
    <tbody>
	<?php /** @var Subscription $subscription */ ?>
	<?php foreach ($this->subscriptions as $subscription) : ?>
		<tr>
			<td><?php echo $subscription->id; ?></td>
			<td><?php echo $subscription->status; ?></td>
            <td><?php echo $subscription->amount->currency; ?></td>
            <td><?php echo $subscription->amount->value; ?></td>
            <td><?php echo $subscription->times ?: Text::_('COM_ROPAYMENTS_SUBSCRIPTION_NO_END'); ?></td>
            <td><?php echo $subscription->interval; ?></td>
            <td><?php echo $subscription->description; ?></td>
            <td><?php echo HTMLHelper::_('date', $subscription->startDate, Text::_('DATE_FORMAT_LC4')); ?></td>
            <td><?php echo $subscription->canceledAt ? HTMLHelper::_('date', $subscription->canceledAt, Text::_('DATE_FORMAT_LC6')) : ''; ?></td>
            <td><?php echo HTMLHelper::_('date', $subscription->createdAt, Text::_('DATE_FORMAT_LC6')); ?></td>
		</tr>
	<?php endforeach; ?>
    </tbody>
</table>
