<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<p class="mb-10">
<?php if( $subscription ){ ?>
	<?php echo JText::_( 'COM_EASYDISCUSS_ALREADY_SUBSCRIBED' ); ?>
<?php } else { ?>
	<?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_' . strtoupper($type) . '_DESCRIPTION');?>
<?php } ?>
</p>

<form id="subscribeForm" action="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&controller=subscription&task=subscribe' );?>" method="post">
<?php if( $system->my->id ){ ?>
<div class="form-inline">
	<label><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_YOUR_EMAIL');?> : </label>
	<span class="dc_ico email"><b><?php echo $system->my->email; ?></b></span>
	<input type="hidden" id="subscribe_email" name="subscribe_email" value="<?php echo $system->my->email; ?>">
	<input type="hidden" id="subscribe_name" name="subscribe_name" value="<?php echo $system->my->name; ?>">
</div>
<?php } else {  ?>
<div class="form-horizontal">
	<div class="control-group">
		<label class="control-label" for="subscribe_email"><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_YOUR_EMAIL');?> : </label>
		<div class="controls">
			<input type="text" id="subscribe_email" name="subscribe_email" value="" />
		</div>
	</div>
</div>

<div class="form-horizontal">
	<div class="control-group">
		<label class="control-label" for="subscribe_name"><?php echo JText::_('COM_EASYDISCUSS_NAME');?> : </label>
		<div class="controls">
			<input type="text" id="subscribe_name" name="subscribe_name" value="" />
		</div>
	</div>
</div>
<?php } ?>
<div class="form-inline">
	<?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_INTERVAL');?>:

	<label class="radio" for="subscription_instant">
		<input type="radio" name="subscription_interval" value="instant" id="subscription_instant"<?php echo $interval == 'instant' ? ' checked="checked"' : '';?> />
		<?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_INSTANT');?>
	</label>

	<label class="radio" for="subscription_daily">
		<input type="radio" name="subscription_interval" value="daily" id="subscription_daily"<?php echo $interval == 'daily' ? ' checked="checked"' : '';?> />
		<?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_DAILY');?>
	</label>

	<label class="radio" for="subscription_weekly">
		<input type="radio" name="subscription_interval" value="weekly" id="subscription_weekly"<?php echo $interval == 'weekly' ? ' checked="checked"' : '';?> />
		<?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_WEEKLY');?>
	</label>

	<label class="radio" for="subscription_monthly">
		<input type="radio" name="subscription_interval" value="monthly" id="subscription_monthly"<?php echo $interval == 'monthly' ? ' checked="checked"' : '';?> />
		<?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_MONTHLY');?>
	</label>
</div>

<input type="hidden" name="cid" value="<?php echo $cid; ?>" />
<input type="hidden" name="type" value="<?php echo $type; ?>" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
