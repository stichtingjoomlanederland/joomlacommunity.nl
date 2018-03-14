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
	jQuery('#ticket').focus();
});
</script>

<form method="post" action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=subscriptions&layout=scan'); ?>" name="adminForm" id="adminForm" class="form-horizontal">
<div class="row-fluid">
	<div class="span2">
		<?php echo JHtmlSidebar::render(); ?>
	</div>
	<div class="span10">
		<?php echo JHtml::_('rsfieldset.start', 'adminform', JText::_('COM_RSEVENTSPRO_SCAN_TITLE')); ?>
		<?php echo JHtml::_('rsfieldset.element', '<label for="ticket">'.JText::_('COM_RSEVENTSPRO_SCAN_LABEL').'</label>', '<input type="text" name="ticket" id="ticket" tabindex="1" />'); ?>
		<?php echo JHtml::_('rsfieldset.end'); ?>
		<p><?php echo JText::_('COM_RSEVENTSPRO_SCAN_DESCRIPTION'); ?></p>
		
		<?php if ($this->scan) { ?>
			<div class="subscriber_container well">
			<?php if (is_array($this->scan)) { ?>
			<?php $subscriber	= $this->scan['subscriber']; ?>
			<?php $ticket		= $this->scan['ticket']; ?>
			<?php $total		= $this->scan['total']; ?>
			<?php $event		= $this->scan['event']; ?>
			<?php $code			= $this->scan['code']; ?>
			<?php $confirmed	= $this->scan['confirmed']; ?>
				<div class="subscriber_event">
					<h3><?php echo $event->name; ?> <small>(<?php echo $event->allday ? rseventsproHelper::showdate($event->start) : rseventsproHelper::showdate($event->start).' - '.rseventsproHelper::showdate($event->end); ?>)</small></h3>
				</div>
				
				<hr />
				
				<div class="subscriber_image">
					<?php echo rseventsproHelper::getAvatar($subscriber->idu,$subscriber->email); ?>
				</div>
			
				<div class="subscriber_details">
					<span><?php echo $subscriber->name; ?> <small>(<?php echo $subscriber->email; ?>)</small></span>
					<span><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBED_ON') . ' ' . rseventsproHelper::showdate($subscriber->date); ?></span>
					<span><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_IP') . ' ' . $subscriber->ip; ?></span>
					<span><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_STATE'). ' ' . $this->getStatus($subscriber->state); ?></span>
				</div>
				
				<hr />
			
				<div class="subscriber_confirmation">
					<span>
						<?php if ($confirmed) { ?>
							<span class="subscriber_confirmed"><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_CONFIRMED'); ?></span>
						<?php } else { ?>
							<a href="javascript:void(0)" onclick="rsepro_confirm_ticket(<?php echo $subscriber->id; ?>, '<?php echo $code; ?>', this);"><?php echo JText::_('COM_RSEVENTSPRO_CONFIRM_SUBSCRIBER'); ?></a>
							<span id="subscriptionConfirm" style="display:none;"><br /><?php echo JHtml::image('com_rseventspro/loader.gif', '', array(), true); ?></span>
						<?php } ?>
					</span>
				</div>
				
				<hr />
				
				<div class="subscriber_info">
					<span class="subscriber_left">
						<?php 
							if ($ticket) {
								echo $ticket->name.' ('.($ticket->price > 0 ? rseventsproHelper::currency($ticket->price) : JText::_('COM_RSEVENTSPRO_GLOBAL_FREE')).')';
							}
						?>
					</span>
					
					<span class="subscriber_right">
						<span><b><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_PAYMENT'); ?></b> <?php echo rseventsproHelper::getPayment($subscriber->gateway); ?></span>
						
						<?php if ($subscriber->early_fee) { ?>
						<span><b><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_EARLY_FEE'); ?></b> <?php echo rseventsproHelper::currency($subscriber->early_fee); ?></span>
						<?php } ?>
						
						<?php if ($subscriber->late_fee) { ?>
						<span><b><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_LATE_FEE'); ?></b> <?php echo rseventsproHelper::currency($subscriber->late_fee); ?></span>
						<?php } ?>
						
						<?php if ($subscriber->tax) { ?>
						<span><b><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_TAX'); ?></b> <?php echo rseventsproHelper::currency($subscriber->tax); ?></span>
						<?php } ?>
						
						<?php if ($subscriber->discount) { ?>
						<span><b><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_DISCOUNT'); ?></b> <?php echo rseventsproHelper::currency($subscriber->discount); ?></span>
						<?php } ?>
						
						<span><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBER_TOTAL'); ?> <?php echo rseventsproHelper::currency($total); ?></span>
					</span>
				</div>
				
				<?php } else { ?> 
				<b><?php echo $this->scan; ?></b>
				<?php } ?>
			</div>
		<?php } ?>
	</div>
	
	<?php echo JHTML::_( 'form.token' ); ?>
</form>