<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
JText::script('COM_RSEVENTSPRO_SUBSCRIBER_CONFIRMED'); ?>

<script type="text/javascript">
jQuery(document).ready(function() {
	document.getElementById('ticket').focus();
});
</script>

<form action="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=scan&id='.rseventsproHelper::sef($this->event->id,$this->event->name)); ?>" method="post" id="adminForm" name="adminForm" class="form-horizontal">
	
	<?php echo JHtml::_('rsfieldset.start', 'adminform', JText::_('COM_RSEVENTSPRO_SCAN_TITLE')); ?>
	<?php echo JHtml::_('rsfieldset.element', '<label for="ticket">'.JText::_('COM_RSEVENTSPRO_SCAN_LABEL').'</label>', '<input type="text" name="ticket" id="ticket" tabindex="1" />'); ?>
	<?php echo JHtml::_('rsfieldset.end'); ?>
	<p><?php echo JText::_('COM_RSEVENTSPRO_SCAN_DESCRIPTION'); ?></p>
	
	<?php if ($this->scan) { ?>
		<div class="subscriber_container well">
		<?php if (is_array($this->scan)) { ?>
		<?php $subscriber	= $this->scan['subscriber']; ?>
		<?php $tickets		= $this->scan['tickets']; ?>
			<div class="subscriber_event">
				<span><?php echo $this->event->name; ?> <small>(<?php echo rseventsproHelper::showdate($this->event->start).' - '.rseventsproHelper::showdate($this->event->end); ?>)</small></span>
			</div>
			
			<hr />
			
			<div class="subscriber_image">
				<?php echo rseventsproHelper::getAvatar($subscriber->idu,$subscriber->email); ?>
			</div>
		
			<div class="subscriber_details">
				<span><?php echo $subscriber->name; ?> <small>(<?php echo $subscriber->email; ?>)</small></span>
				<span><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIPTION_DATE') . ' ' . rseventsproHelper::showdate($subscriber->date); ?></span>
				<span><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIPTION_IP') . ' ' . $subscriber->ip; ?></span>
				<span><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_STATUS'). ' ' . $this->getStatus($subscriber->state); ?></span>
			</div>
			
			<hr />
			
			<div class="subscriber_confirmation">
				<span id="confirm<?php echo $subscriber->id; ?>">
					<?php if ($subscriber->confirmed) { ?>
						<span class="subscriber_confirmed"><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_CONFIRMED'); ?></span>
					<?php } else { ?>
						<a href="javascript:void(0)" onclick="rsepro_confirm_subscriber(<?php echo $subscriber->id; ?>, '<?php echo JSession::getFormToken(); ?>')"><?php echo JText::_('COM_RSEVENTSPRO_CONFIRM_SUBSCRIBER'); ?></a>
						<span id="subscriptionConfirm" style="display:none;"><br /><img src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/loader.gif" alt="" /></span>
					<?php } ?>
				</span>
			</div>
			
			<hr />
			
			<?php if (!empty($tickets)) { ?>
			<?php $total = 0; ?>
			
			<div class="subscriber_info">
				<span class="subscriber_left">
					<?php foreach ($tickets as $ticket) { ?>
					<?php if ($ticket->price > 0) { ?>
					<?php $total += $ticket->quantity * $ticket->price; ?>
					<?php echo $ticket->quantity; ?> x <?php echo $ticket->name; ?> (<?php echo rseventsproHelper::currency($ticket->price); ?>) <br />
					<?php } else { ?>
					<?php echo $ticket->quantity; ?> x <?php echo $ticket->name; ?> (<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_FREE'); ?>) <br />
					<?php } ?>
					<?php } ?>
				</span>
				<?php if ($subscriber->discount) $total = $total - $subscriber->discount; ?>
				<span class="subscriber_right">
					<span><b><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIPTION_PAYMENT'); ?></b> <?php echo rseventsproHelper::getPayment($subscriber->gateway); ?>
					
					<?php if ($subscriber->early_fee) { ?>
					<span><b><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_EARLY_FEE'); ?></b> <?php echo rseventsproHelper::currency($subscriber->early_fee); ?>
					<?php $total = $total - $subscriber->early_fee; ?>
					<?php } ?>
					
					<?php if ($subscriber->late_fee) { ?>
					<span><b><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_LATE_FEE'); ?></b> <?php echo rseventsproHelper::currency($subscriber->late_fee); ?>
					<?php $total = $total + $subscriber->late_fee; ?>
					<?php } ?>
					
					<?php if ($subscriber->tax) { ?>
					<span><b><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_TAX'); ?></b> <?php echo rseventsproHelper::currency($subscriber->tax); ?>
					<?php $total = $total + $subscriber->tax; ?>	
					<?php } ?>
					
					<?php if ($subscriber->discount) { ?>
					<span><b><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_DISCOUNT'); ?></b> <?php echo rseventsproHelper::currency($subscriber->discount); ?>
					<?php } ?>
					
					<span><b><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_TOTAL'); ?></b> <?php echo rseventsproHelper::currency($total); ?>
				</span>
			</div>
			<?php } ?>
			
			<?php } else { ?> 
			<b><?php echo $this->scan; ?></b>
			<?php } ?>
		</div>
	<?php } ?>

	<?php echo JHTML::_('form.token')."\n"; ?>
	<input type="hidden" name="id" value="<?php echo $this->event->id; ?>" />
</form>