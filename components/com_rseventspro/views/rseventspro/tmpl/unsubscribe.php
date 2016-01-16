<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );?>

<?php if (!empty($this->subscriptions)) { ?>
<table class="rs_table" width="100%" cellspacing="0" cellpadding="10">
<?php foreach ($this->subscriptions as $subscription) { ?>
<tr style="border-bottom:1px solid red;">
	<td>
		<b><?php echo JText::_('COM_RSEVENTSPRO_UNSUBSCRIBE_NAME'); ?></b> <?php echo $subscription->name; ?> <br />
		<b><?php echo JText::_('COM_RSEVENTSPRO_UNSUBSCRIBE_DATE'); ?></b> <?php echo rseventsproHelper::date($subscription->date,null,true); ?> <br />
		<b><?php echo JText::_('COM_RSEVENTSPRO_UNSUBSCRIBE_STATUS'); ?></b> <?php echo $this->getStatus($subscription->state); ?> <br />
	</td>
	<td>
		<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.unsubscribeuser&id='.rseventsproHelper::sef($subscription->id,$subscription->name)); ?>" class="rs_button_control"><?php echo JText::_('COM_RSEVENTSPRO_UNSUBSCRIBE_UNSUBSCRIBE'); ?></a>
	</td>
</tr>
<?php } ?>
</table>
<?php } ?>