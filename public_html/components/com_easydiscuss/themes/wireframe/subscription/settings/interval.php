<?php
/**
* @package    EasyDiscuss
* @copyright  Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license    GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="o-dropdown" data-subscription-settings="interval" data-method="updateSubscribeInterval">
	<a href="javascript:void(0);" class="o-btn o-btn--default-o sm:t-d--block sm:t-mb--md" data-ed-toggle="dropdown">
		<b><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_INTERVAL'); ?></b> 
		<span data-preview>
			<?php if ($subscription->interval == 'instant') { ?>
				<?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_INSTANT'); ?>
			<?php } ?>

			<?php if ($subscription->interval == 'daily') { ?>
				<?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_DAILY'); ?>
			<?php } ?>

			<?php if ($subscription->interval == 'weekly') { ?>
				<?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_WEEKLY'); ?>
			<?php } ?>

			<?php if ($subscription->interval == 'monthly') { ?>
				<?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_MONTHLY'); ?>
			<?php } ?>
		</span> 
		<i class="fa fa-caret-down t-ml--xs"></i>
	</a>

	<ul class="o-dropdown-menu t-mt--2xs sm:t-w--100 has-active-markers">
		<li class="<?php echo $subscription->interval == 'instant' ? 'active' : '';?>"
			data-settings="instant"
		>
			<a href="javascript:void(0);" class="o-dropdown__item">
				<?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_INSTANT'); ?>
			</a>
		</li>
		
		<?php if (!$this->config->get('main_email_digest') && $subscription->interval != 'instant') { ?>
			<li class="<?php echo $subscription->interval == 'daily' ? 'active' : '';?>">
				<a href="javascript:void(0);" class="o-dropdown__item">
					<?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_' . strtoupper($subscription->interval)); ?>
				</a>
			</li>
		<?php } ?>

		<?php if ($this->config->get('main_email_digest')) {  ?>
			<li class="<?php echo $subscription->interval == 'daily' ? 'active' : '';?>"
				data-settings="daily"
			>
				<a href="javascript:void(0);" class="o-dropdown__item">
					<?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_DAILY'); ?>
				</a>
			</li>
			<li class="<?php echo $subscription->interval == 'weekly' ? 'active' : '';?>"
				data-settings="weekly"
			>
				<a href="javascript:void(0);" class="o-dropdown__item">
					<?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_WEEKLY'); ?>
				</a>
			</li>
			<li class="<?php echo $subscription->interval == 'monthly' ? 'active' : '';?>"
				data-settings="monthly"
			>
				<a href="javascript:void(0);" class="o-dropdown__item">
					<?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_MONTHLY'); ?>
				</a>
			</li>
		<?php } ?>
	</ul>
</div>