<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
$info = rseventsproHelper::getEventInfo($this->item->id); 
$subscribers = rseventsproHelper::getSubscribers($this->item->id);
$coupons = rseventsproHelper::getCoupons($this->item->id);
$guests = rseventsproHelper::getRSVP($this->item->id); ?>

<div class="row-fluid">
	<div class="span4">
		<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_DASH_GENERAL_INFO'); ?></legend>

		<table class="table table-striped">
			<?php if ($info) { ?>
			<?php foreach ($info as $name => $value) { ?>
			<tr>
				<td><?php echo JText::_('COM_RSEVENTSPRO_EVENT_DASH_'.strtoupper($name)); ?></td>
				<td><?php echo $value; ?></td>
			</tr>
			<?php } ?>
			<?php } ?>
		</table>
	</div>
	
	<?php if ($this->item->registration) { ?>
	<div class="span5">
		<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_DASH_TICKETS'); ?></legend>
		<table class="table table-striped">
			<thead>
				<th><?php echo JText::_('COM_RSEVENTSPRO_EVENT_DASH_TICKET'); ?></th>
				<th width="20%" class="center"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_DASH_TICKET_PRICE'); ?></th>
				<th width="20%" class="center"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_DASH_TICKET_SOLD'); ?></th>
				<th width="30%" class="center"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_DASH_TICKET_EXPIRES'); ?></th>
			</thead>
		
			<?php if ($this->tickets) { ?>
			<?php foreach ($this->tickets as $ticket) { ?>
			<tr>
				<td><?php echo $ticket->name; ?></td>
				<td class="center"><?php echo $ticket->price > 0 ? rseventsproHelper::currency($ticket->price) : JText::_('COM_RSEVENTSPRO_GLOBAL_FREE'); ?></td>
				<td class="center"><?php echo rseventsproHelper::getTicketCount($ticket); ?></td>
				<td class="center"><?php echo $ticket->to == JFactory::getDbo()->getNullDate() ? rseventsproHelper::showdate($this->item->end) : rseventsproHelper::showdate($ticket->to); ?></td>
			</tr>
			<?php } ?>
			<?php } else { ?>
			<tr>
				<td><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIPTION_FREE_ENTRANCE'); ?></td>
				<td class="center">-</td>
				<td class="center"><?php echo rseventsproHelper::getTicketCountNoEntrance($this->item->id); ?></td>
				<td><?php echo rseventsproHelper::showdate($this->item->end); ?></td>
			</tr>
			<?php } ?>
		</table>
	</div>
	<?php } ?>
	
	<?php if ($coupons) { ?>
	<div class="span3">
		<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_DASH_COUPONS'); ?></legend>
		<table class="table table-striped">
			<thead>
				<th><?php echo JText::_('COM_RSEVENTSPRO_EVENT_DASH_COUPON'); ?></th>
				<th class="center"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_DASH_COUPON_USED'); ?></th>
			</thead>
			<tbody>
				<?php foreach ($coupons as $coupon) { ?>
				<tr>
					<td><?php echo $coupon->coupon; ?></td>
					<td class="center"><?php echo $coupon->nr; ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<?php } ?>
</div>
<div class="row-fluid">
	<?php if ($this->item->registration) { ?>
	<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_DASH_SUBSCRIBERS'); ?></legend>
	<?php if ($subscribers) { ?>
	<table class="table table-striped">
		<thead>
			<th><?php echo JText::_('COM_RSEVENTSPRO_EVENT_DASH_SUBSCRIBER_NAME'); ?></th>
			<th class="center"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_DASH_SUBSCRIBER_DATE'); ?></th>
			<th class="center"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_DASH_SUBSCRIBER_GATEWAY'); ?></th>
			<th class="center"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_DASH_SUBSCRIBER_STATUS'); ?></th>
		</thead>
		<tbody>
			<?php foreach ($subscribers as $subscriber) { ?>
			<tr>
				<td>
					<a target="_blank" href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=subscription.edit&id='.$subscriber->id); ?>"><?php echo $subscriber->name; ?></a> (<?php echo $subscriber->email; ?>)
				</td>
				<td class="center"><?php echo rseventsproHelper::showdate($subscriber->date,null,true); ?></td>
				<td class="center">
					<?php $payment = rseventsproHelper::getPayment($subscriber->gateway); ?>
					<?php echo $payment ? $payment : '-'; ?>
				</td>
				<td class="center"><?php echo rseventsproHelper::getStatuses($subscriber->state); ?></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
	<?php } else { ?>
	<?php echo JText::_('COM_RSEVENTSPRO_EVENT_DASH_NO_SUBSCRIBERS'); ?>
	<?php } ?>
	<?php } ?>
	
	<?php if ($this->item->rsvp) { ?>
	<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_DASH_GUESTS'); ?></legend>
	<?php if ($guests) { ?>
	<table class="table table-striped">
		<thead>
			<th><?php echo JText::_('COM_RSEVENTSPRO_EVENT_DASH_GUEST_NAME'); ?></th>
			<th class="center"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_DASH_GUEST_DATE'); ?></th>
			<th class="center"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_DASH_GUEST_STATUS'); ?></th>
		</thead>
		<tbody>
			<?php foreach ($guests as $guest) { ?>
			<tr>
				<td><?php echo $guest->name; ?> (<?php echo $guest->email; ?>)</td>
				<td class="center"><?php echo rseventsproHelper::showdate($guest->date,null,true); ?></td>
				<td class="center"><?php echo rseventsproHelper::RSVPStatus($guest->rsvp); ?></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
	<?php } else { ?>
	<?php echo JText::_('COM_RSEVENTSPRO_EVENT_DASH_NO_GUESTS'); ?>
	<?php } ?>
	<?php } ?>
</div>