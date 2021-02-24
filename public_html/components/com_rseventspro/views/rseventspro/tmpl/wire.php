<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
$tickets	= $this->data['tickets'];
$data		= $this->data['data'];
$event		= $this->data['event']; 
$total		= 0; ?>

<h1><?php echo $this->payment->name; ?></h1>

<?php if (!empty($this->data)) { ?>
<table class="table table-bordered">
	<?php if (!empty($tickets)) { ?>
	<tr>
		<td><strong><?php echo JText::_('COM_RSEVENTSPRO_WIRE_TICKETS'); ?></strong></td>
		<td>
			<?php foreach ($tickets as $ticket) { ?>
			<?php if ($ticket->price > 0) { ?>
			<?php $total += $ticket->quantity * $ticket->price; ?>
			<?php echo $ticket->quantity; ?> x <?php echo $ticket->name; ?> (<?php echo rseventsproHelper::currency($ticket->price); ?>) <br />
			<?php } else { ?>
			<?php echo $ticket->quantity; ?> x <?php echo $ticket->name; ?> (<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_FREE'); ?>) <br />
			<?php } ?>
			<?php } ?>
		</td>
	</tr>
	<?php $total = $total - $data->discount; ?>
	<?php } ?>
	<tr>
		<td><strong><?php echo JText::_('COM_RSEVENTSPRO_WIRE_DATE'); ?></strong></td>
		<td><?php echo rseventsproHelper::showdate($data->date,null,true); ?></td>
	</tr>
	<?php if (!empty($data->early_fee)) { ?>
	<?php $total = $total - $data->early_fee; ?>
	<tr>
		<td><strong><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_EARLY_FEE'); ?></strong></td>
		<td><?php echo rseventsproHelper::currency($data->early_fee); ?></td>
	</tr>
	<?php } ?>
	<?php if (!empty($data->late_fee)) { ?>
	<?php $total = $total + $data->late_fee; ?>
	<tr>
		<td><strong><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_LATE_FEE'); ?></strong></td>
		<td><?php echo rseventsproHelper::currency($data->late_fee); ?></td>
	</tr>
	<?php } ?>
	<?php if (!empty($data->tax)) { ?>
	<?php $total = $total + $data->tax; ?>	
	<tr>
		<td><strong><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_TAX'); ?></strong></td>
		<td><?php echo rseventsproHelper::currency($data->tax); ?></td>
	</tr>
	<?php } ?>
	
	<?php if (!empty($data->discount)) { ?>
	<tr>
		<td><strong><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_DISCOUNT'); ?></strong></td>
		<td><?php echo rseventsproHelper::currency($data->discount); ?></td>
	</tr>
	<?php } ?>
	<?php if ($total > 0) { ?>
	<tr>
		<td><strong><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_TOTAL'); ?></strong></td>
		<td><?php echo rseventsproHelper::currency($total); ?></td>
	</tr>
	<?php } ?>
</table>
<?php } ?>

<br /><br />
<?php echo rseventsproEmails::placeholders($this->payment->details, $data->ide, ''); ?>

<?php if (!empty($this->payment->redirect)) { ?>
<button type="button" class="btn btn-primary" onclick="document.location='<?php echo $this->payment->redirect; ?>'"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CONTINUE'); ?></button> <?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_OR'); ?>
<?php } ?>
 <a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($data->ide,$event->name),false,rseventsproHelper::itemid($data->ide)); ?>"><?php echo JText::_('COM_RSEVENTSPRO_BACK_TO_EVENT'); ?></a>