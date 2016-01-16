<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );?>

<?php if ($this->params->get('show_page_heading', 1)) { ?>
<?php $title = $this->params->get('page_heading', ''); ?>
<h1><?php echo !empty($title) ? $this->escape($title) : JText::_('COM_RSEVENTSPRO_MY_SUBSCRIPTIONS'); ?></h1>
<?php } ?>

<?php if (!empty($this->subscriptions)) { ?>
<?php $i = 1; ?>
<div class="rs_my_subscription rs_my_subscription<?php echo $this->pdf ? '4' : '3'; ?> rs_subscription_header">
	<span class="rs_subscription_id">#</span>
	<span><?php echo JText::_('COM_RSEVENTSPRO_MY_SUBSCRIPTION_DATE'); ?></span>
	<span><?php echo JText::_('COM_RSEVENTSPRO_MY_SUBSCRIPTION_EVENT'); ?></span>
	<span><?php echo JText::_('COM_RSEVENTSPRO_MY_SUBSCRIPTION_STATUS'); ?></span>
	<?php if ($this->pdf) { ?><span><?php echo JText::_('COM_RSEVENTSPRO_MY_SUBSCRIPTION_TICKET'); ?></span><?php } ?>
</div>
<div class="rs_clear"></div>

<?php foreach ($this->subscriptions as $subscription) { ?>
<div class="rs_my_subscription rs_my_subscription<?php echo $this->pdf ? '4' : '3'; ?>">
	<span class="rs_subscription_id"><?php echo $i; ?></span> 
	<span><?php echo rseventsproHelper::showdate($subscription->subscribe_date,null,true); ?></span>
	<span><a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($subscription->id,$subscription->name),false,rseventsproHelper::itemid($subscription->id)); ?>"><?php echo $subscription->name; ?></a></span>
	<span>
		<span class="subscription_state<?php echo $subscription->state; ?>">
		<?php if ($subscription->state == 1) { ?>
		<i class="fa fa-check"></i>
		<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_STATUS_COMPLETED'); ?>
		<?php } else if ($subscription->state == 0) { ?>
		<?php if (!empty($subscription->URL)) { ?>
		<a href="<?php echo $subscription->URL; ?>">
		<?php } ?>
		<i class="fa fa-exclamation-triangle"></i>
		<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_STATUS_INCOMPLETE'); ?>
		<?php if (!empty($subscription->URL)) { ?>
		</a>
		<?php } ?>
		<?php } else if ($subscription->state == 2) { ?>
		<i class="fa fa-minus-circle"></i>
		<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_STATUS_DENIED'); ?>
		<?php } ?>
		</span>
	</span>
	<?php if ($this->pdf) { ?>
	<?php if ($subscription->state == 1 && $subscription->ticket_pdf == 1 && !empty($subscription->ticket_pdf_layout)) { ?>
	<span><a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=ticket&from=subscriptions&format=raw&id='.rseventsproHelper::sef($subscription->ids,$subscription->iname)); ?>"><?php echo JText::_('COM_RSEVENTSPRO_MY_SUBSCRIPTION_DOWNLOAD_TICKET'); ?></a></span>
	<?php } else { ?>
	<span>-</span>
	<?php } ?>
	<?php } ?>
</div>
<?php $i++; ?>
<?php } ?>
<?php } else { ?>
<h2><?php echo JText::_('COM_RSEVENTSPRO_MY_SUBSCRIPTIONS_NO_SUBSCRIPTIONS'); ?></h2>
<?php } ?>