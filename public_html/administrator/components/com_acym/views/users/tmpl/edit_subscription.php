<?php
defined('_JEXEC') or die('Restricted access');
?><div class="cell grid-x acym__users__display__subscriptions--list">
	<h5 class="cell font-bold"><?php echo acym_translation('ACYM_LISTS'); ?></h5>
	<div class="cell acym__content__tab">
        <?php $data['tab']->startTab(acym_translation('ACYM_SUBSCRIBED').' ('.count($data['subscriptions']).')', false, '', !empty($data['subscriptions'])); ?>
        <?php include acym_getView('users', 'edit_subscription_subscribed', true); ?>
        <?php $data['tab']->endTab(); ?>

        <?php $data['tab']->startTab(acym_translation('ACYM_UNSUBSCRIBED').' ('.count($data['unsubscribe']).')', false, '', !empty($data['unsubscribe'])); ?>
        <?php include acym_getView('users', 'edit_subscription_unsubscribed', true); ?>
        <?php $data['tab']->endTab(); ?>

        <?php $data['tab']->display('lists_user'); ?>
	</div>
</div>

