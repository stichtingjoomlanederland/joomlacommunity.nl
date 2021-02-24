<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );?>

<?php if (!empty($this->subscriptions)) { ?>
<table class="table table-striped">
	<?php foreach ($this->subscriptions as $subscription) { ?>
	<?php $tickets = rseventsproHelper::getUserTickets($subscription->id, true); ?>
	<?php $tickets = $tickets ? explode('<br />', $tickets) : array(); ?>
	<?php $tickets = array_filter($tickets); ?>
	<?php $tickets = implode(', ', $tickets); ?>
	<tr>
		<td>
			<b><?php echo JText::_('COM_RSEVENTSPRO_UNSUBSCRIBE_NAME'); ?></b> <?php echo $subscription->name; ?> <br />
			<b><?php echo JText::_('COM_RSEVENTSPRO_UNSUBSCRIBE_DATE'); ?></b> <?php echo rseventsproHelper::showdate($subscription->date,null,true); ?> <br />
			<b><?php echo JText::_('COM_RSEVENTSPRO_UNSUBSCRIBE_STATUS'); ?></b> <?php echo $this->getStatus($subscription->state); ?> <br />
			<b><?php echo JText::_('COM_RSEVENTSPRO_UNSUBSCRIBE_TICKETS'); ?></b> <?php echo $tickets; ?> <br />
		</td>
		<td style="vertical-align: middle;" class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>">
			<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.unsubscribeuser&ide='.rseventsproHelper::sef($this->event->id,$this->event->name).'&id='.rseventsproHelper::sef($subscription->id,$subscription->name), false); ?>" class="<?php echo RSEventsproAdapterGrid::styles(array('btn')); ?>"><?php echo JText::_('COM_RSEVENTSPRO_UNSUBSCRIBE_UNSUBSCRIBE'); ?></a>
		</td>
	</tr>
	<?php } ?>
</table>
<?php } ?>