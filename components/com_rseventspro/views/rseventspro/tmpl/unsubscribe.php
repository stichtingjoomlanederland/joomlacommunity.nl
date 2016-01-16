<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );?>

<?php if (!empty($this->subscriptions)) { ?>
<table class="table table-striped">
	<?php foreach ($this->subscriptions as $subscription) { ?>
	<tr>
		<td>
			<b><?php echo JText::_('COM_RSEVENTSPRO_UNSUBSCRIBE_NAME'); ?></b> <?php echo $subscription->name; ?> <br />
			<b><?php echo JText::_('COM_RSEVENTSPRO_UNSUBSCRIBE_DATE'); ?></b> <?php echo rseventsproHelper::showdate($subscription->date,null,true); ?> <br />
			<b><?php echo JText::_('COM_RSEVENTSPRO_UNSUBSCRIBE_STATUS'); ?></b> <?php echo $this->getStatus($subscription->state); ?> <br />
		</td>
		<td style="vertical-align: middle;">
			<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.unsubscribeuser&id='.rseventsproHelper::sef($subscription->id,$subscription->name)); ?>" class="btn"><?php echo JText::_('COM_RSEVENTSPRO_UNSUBSCRIBE_UNSUBSCRIBE'); ?></a>
		</td>
	</tr>
	<?php } ?>
</table>
<?php } ?>