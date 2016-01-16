<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');
?>
<h3><?php echo JText::_( 'COM_EASYDISCUSS_USER_EDIT_SUBSCRIPTIONS' );?></h3>
<hr />
<?php if( $subscriptions ){ ?>
	<?php foreach( $subscriptions as $subtype => $subcription) { ?>

		<?php if( count( $subcription ) > 0 ) { ?>

		<h4 class="pt-15 mt-15"><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIPTION_' . $subtype); ?></h4>

		<ol class="discuss-subscribe-list">
			<?php if( $subtype == 'site' ) { ?>
				<li>
					<div class="discuss-subscribe-title"><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIPTION_' . $subtype); ?></div>
					<span class="discuss-unsubscribe pull-right"><?php echo DiscussHelper::getSubscriptionHTML($system->my->id, 0, 'site', 'button-link', false); ?></span>
				</li>
			<?php } else { ?>
				<?php foreach( $subcription as $sub ) { ?>
				<li>
					<div class="discuss-subscribe-title"><a href="<?php echo $sub->link; ?>" class=""><?php echo $sub->title; ?></a></div>
					<span class="discuss-unsubscribe">
						<a class="btn btn-mini btn-danger" href="<?php echo $sub->unsublink; ?>"><i class="icon-remove-sign"></i> <?php echo JText::_('COM_EASYDISCUSS_SUBSCRIPTION_UNSUBSCRIBE'); ?></a>
					</span>
				</li>
				<?php } ?>
			<?php } ?>
		</ol>
		<?php } ?>
	<?php } ?>
<?php } else { ?>
<div class="empty">
	<?php echo JText::_( 'COM_EASYDISCUSS_NO_SUBSCRIPTIONS_YET' );?>
</div>
<?php } ?>
