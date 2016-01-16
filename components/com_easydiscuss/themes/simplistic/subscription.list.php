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
<script type="text/javascript">
EasyDiscuss.require().library('dialog').done(function($){

	var html = '';

	$('#unsubscribe-all').click(function() {
			$.dialog({
				title: '<?php echo JText::_("COM_EASYDISCUSS_SUBSCRIPTION_UNSUBSCRIBE_FROM_ALL"); ?>',
				content: '<?php echo JText::_("COM_EASYDISCUSS_SUBSCRIPTION_UNSUBSCRIBE_FROM_ALL_DESC"); ?>',
				showOverlay: false,
				body: {
					css: {
						minWidth: 200,
						minHeight: 100
					}
				},
				buttons: {
					'yes': {
						name: '<?php echo JText::_("COM_EASYDISCUSS_BUTTON_YES"); ?>',
						click: function() {
							EasyDiscuss.ajax(
								"site.views.subscriptions.unsubscribeMe",
								{

								},
								{
									success: function(message) {

										$('#unsubscription-message').append( message );
										$('#dc_subscription').hide();
										$('#unsubscribe-all').hide();
										$.dialog().close();
									},
									fail: function(message) {

									}
								});
						}
					},
					'no': {
						name: '<?php echo JText::_("COM_EASYDISCUSS_BUTTON_NO"); ?>',
						click: function() {
							$.dialog().close();
						}
					}
				}
			});
		});

});
</script>
<h2 class="discuss-component-title"><?php echo JText::_('COM_EASYDISCUSS_HEADING_SUBSCRIPTIONS'); ?></h2>
<hr />
<?php if (!empty($email)) : ?>
<div><?php echo JText::sprintf('COM_EASYDISCUSS_UNSUBSCRIBE_FROM_EMAIL_ADDRESS', $email); ?></div>
<div id="unsubscribe-all">
	<a id="unsubscribe-all-link" href="javascript:void(0);">
		<?php echo JText::_('COM_EASYDISCUSS_SUBSCRIPTION_UNSUBSCRIBE_FROM_ALL'); ?>
	</a>
</div>
<div id="unsubscription-message"></div>
<?php endif; ?>

<?php if ( !empty( $subscriptions ) ) { ?>
<div id="dc_subscription">
	<div class="small ttu fs-11 bt-sd pt-15 mt-15"><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIPTION_SITE'); ?></div>
	<ul id="dc_list" class="reset-ul mt-15">
		<li>
			<?php echo DiscussHelper::getSubscriptionHTML($system->my->id, 0, 'site', 'button-link', false); ?>
		</li>
	</ul>
	<?php if ( !empty($subscriptions['category'])) : ?>
		<div class="small ttu fs-11 bt-sd pt-15 mt-15"><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIPTION_CATEGORY'); ?></div>
		<ol class="subscription-index reset-ul mt-10">
			<?php foreach ($subscriptions['category'] as $sub) : ?>
				<li>
					<span><a href="<?php echo $sub->link; ?>" class="fs-14 fwb"><?php echo $sub->title; ?></a></span>
					<span class="small fs-11">- <a href="<?php echo $sub->unsublink; ?>"><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIPTION_UNSUBSCRIBE'); ?></a></span>
				</li>
			<?php endforeach; ?>
		</ol>
	<?php endif; ?>
	<?php if ( !empty($subscriptions['user'])) : ?>
		<div class="small ttu fs-11 bt-sd pt-15 mt-15"><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIPTION_USER'); ?></div>
		<ol class="subscription-index reset-ul mt-10">
			<?php foreach ($subscriptions['user'] as $sub) : ?>
				<li>
					<span><a href="<?php echo $sub->link; ?>" class="fs-14 fwb"><?php echo $sub->title; ?></a></span>
					<span class="small fs-11">- <a href="<?php echo $sub->unsublink; ?>"><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIPTION_UNSUBSCRIBE'); ?></a></span>
				</li>
			<?php endforeach; ?>
		</ol>
	<?php endif; ?>
	<?php if ( !empty($subscriptions['post'])) : ?>
		<div class="small ttu fs-11 bt-sd pt-15 mt-15"><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIPTION_POST'); ?></div>
		<ol class="subscription-index reset-ul mt-10">
			<?php foreach ($subscriptions['post'] as $sub) : ?>
				<li>
					<span><a href="<?php echo $sub->link; ?>" class="fs-14 fwb"><?php echo $sub->title; ?></a></span>
					<span class="small fs-11">- <a href="<?php echo $sub->unsublink; ?>"><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIPTION_UNSUBSCRIBE'); ?></a></span>
				</li>
			<?php endforeach; ?>
		</ol>
	<?php endif; ?>
</div>
<?php } else { ?>
<div class="bg-f5 b-sd tac pa-15 mt-15 fwb"><?php echo JText::_('COM_EASYDISCUSS_NO_SUBSCRIPTIONS_FOUND'); ?></div>
<?php } ?>
