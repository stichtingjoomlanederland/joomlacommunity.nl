<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Mollie\Api\Resources\Subscription;

defined('_JEXEC') or die;

/** @var JdidealgatewayViewSubscriptions $this */
?>
<h2><?php echo Text::_('COM_ROPAYMENTS_SUBSCRIPTIONS_LABEL'); ?></h2>
<?php if ($this->subscriptions->count() === 0) :?>
	<div><?php echo Text::_('COM_ROPAYMENTS_SUBSCRIPTIONS_NOT_FOUND'); ?></div>
<?php else: ?>
	<?php /** @var Subscription $subscription */ ?>
	<?php foreach ($this->subscriptions as $subscription) : ?>
		<dl>
			<dt><?php echo Text::_('COM_ROPAYMENTS_SUBSCRIPTION_DESCRIPTION'); ?></dt>
			<dd><?php echo $subscription->description; ?></dd>
			<dt><?php echo Text::_('COM_ROPAYMENTS_SUBSCRIPTION_AMOUNT'); ?></dt>
			<dd><?php echo $subscription->amount->currency . ' ' . $subscription->amount->value; ?></dd>
			<dt><?php echo Text::_('COM_ROPAYMENTS_SUBSCRIPTION_INTERVAL'); ?></dt>
			<dd><?php echo $subscription->interval; ?></dd>
			<dt><?php echo Text::_('COM_ROPAYMENTS_SUBSCRIPTION_CREATED'); ?></dt>
			<dd><?php echo HTMLHelper::_('date', $subscription->createdAt); ?></dd>
			<?php if ($subscription->isCanceled()) : ?>
				<dt><?php echo Text::_('COM_ROPAYMENTS_SUBSCRIPTION_CANCELED'); ?></dt>
				<dd><?php echo HTMLHelper::_('date', $subscription->canceledAt); ?></dd>
			<?php endif; ?>
			<?php if ($subscription->isActive()) : ?>
				<dt><?php echo Text::_('COM_ROPAYMENTS_SUBSCRIPTION_NEXT_PAYMENT'); ?></dt>
				<dd><?php echo HTMLHelper::_('date', $subscription->nextPaymentDate); ?></dd>
			<?php endif; ?>
		</dl>
		<?php if ($subscription->isActive()) : ?>
			<div>
				<form action="<?php echo Route::_('index.php?option=com_jdidealgateway&task=subscription.cancel'); ?>"
				      method="post">
					<button type="submit" class="btn btn-danger">
						<?php echo Text::_('COM_ROPAYMENTS_CANCEL_SUBSCRIPTION'); ?>
					</button>
					<input type="hidden" name="subscriptionId" value="<?php echo $subscription->id; ?>"/>
					<?php echo HTMLHelper::_('form.token'); ?>
				</form>
			</div>
		<?php endif; ?>
	<?php endforeach; ?>
<?php endif;
