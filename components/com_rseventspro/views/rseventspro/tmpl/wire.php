<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
$tickets	= $this->data['tickets'];
$data		= $this->data['data'];
$event		= $this->data['event']; 
$total		= 0; ?>

<h1><?php echo $this->payment->name; ?></h1>

<?php if (!empty($this->data)) { ?>
<?php if (!empty($tickets)) { ?>
	<div>
		<label class="rs_wire"><b><?php echo JText::_('COM_RSEVENTSPRO_WIRE_TICKETS'); ?></b>:</label>
		<div style="float:left;">
		<?php foreach ($tickets as $ticket) { ?>
		<?php if ($ticket->price > 0) { ?>
		<?php $total += $ticket->quantity * $ticket->price; ?>
		<?php echo $ticket->quantity; ?> x <?php echo $ticket->name; ?> (<?php echo rseventsproHelper::currency($ticket->price); ?>) <br />
		<?php } else { ?>
		<?php echo $ticket->quantity; ?> x <?php echo $ticket->name; ?> (<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_FREE'); ?>) <br />
		<?php } ?>
		<?php } ?>
		</div>
	</div>
	<div class="rs_clear"></div>
	<?php $total = $total - $data->discount; ?>
<?php } ?>
	
	<div>
		<label class="rs_wire"><b><?php echo JText::_('COM_RSEVENTSPRO_WIRE_DATE'); ?></b></label>
		<?php echo rseventsproHelper::showdate($data->date,null,true); ?>
	</div>
	<div class="rs_clear"></div>
	
	<?php if (!empty($data->early_fee)) { ?>
	<?php $total = $total - $data->early_fee; ?>
	<div>
		<label class="rs_wire"><b><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_EARLY_FEE'); ?></b></label>
		<?php echo rseventsproHelper::currency($data->early_fee); ?>
	</div>
	<div class="rs_clear"></div>
	<?php } ?>
	
	<?php if (!empty($data->late_fee)) { ?>
	<?php $total = $total + $data->late_fee; ?>
	<div>
		<label class="rs_wire"><b><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_LATE_FEE'); ?></b></label>
		<?php echo rseventsproHelper::currency($data->late_fee); ?>
	</div>
	<div class="rs_clear"></div>
	<?php } ?>
	
	<?php if (!empty($data->tax)) { ?>
	<?php $total = $total + $data->tax; ?>	
	<div>
		<label class="rs_wire"><b><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_TAX'); ?></b></label>
		<?php echo rseventsproHelper::currency($data->tax); ?>
	</div>
	<div class="rs_clear"></div>
	<?php } ?>
	
	<?php if (!empty($data->discount)) { ?>
	<div>
		<label class="rs_wire"><b><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_DISCOUNT'); ?></b></label>
		<?php echo rseventsproHelper::currency($data->discount); ?>
	</div>
	<div class="rs_clear"></div>
	<?php } ?>
	
	<?php if ($total > 0) { ?>
	<div>
		<label class="rs_wire"><b><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_TOTAL'); ?></b></label>
		<?php echo rseventsproHelper::currency($total); ?>
	</div>
	<div class="rs_clear"></div>
	<?php } ?>
<?php } ?>

<div class="rs_clear"></div>
<br /><br />
<div>
	<?php echo rseventsproEmails::placeholders($this->payment->details, $data->ide, ''); ?>
</div>
<div class="rs_clear"></div>

<?php if (!empty($this->payment->redirect)) { ?>
<button type="button" class="button" onclick="document.location='<?php echo $this->payment->redirect; ?>'"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CONTINUE'); ?></button>
 <?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_OR'); ?>
<?php } ?>
 <a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($data->ide,$event->name),false,rseventsproHelper::itemid($data->ide)); ?>"><?php echo JText::_('COM_RSEVENTSPRO_BACK_TO_EVENT'); ?></a>