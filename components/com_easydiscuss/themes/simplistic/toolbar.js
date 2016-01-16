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

EasyDiscuss
.require()
.script( 'legacy', 'toolbar' )
.done(function($){

	<?php if( $system->my->id > 0 && $system->config->get( 'main_conversations') && $system->config->get( 'main_conversations_notification' ) ){ ?>
	discuss.conversation.interval = <?php echo $system->config->get( 'main_conversations_notification_interval' ) * 1000 ?>;
	discuss.conversation.startMonitor();
	<?php } ?>

	<?php if( $system->my->id > 0 && $system->config->get( 'main_notifications' ) ){ ?>
	discuss.notifications.interval = <?php echo $system->config->get( 'main_notifications_interval' ) * 1000 ?>;
	discuss.notifications.startMonitor();
	<?php } ?>


	// Implement toolbar controller.
	$( '.discuss-toolbar' ).implement( EasyDiscuss.Controller.Toolbar );

	<?php if( $system->config->get( 'main_responsive' ) ){ ?>

	$.responsive($('.discuss-toolbar'), {
		elementWidth: function() {
			return $('.discuss-toolbar').outerWidth(true) - 80;

		},
		conditions: {
			at: (function() {
				var listWidth = 0;

				$('.discuss-toolbar .nav > li').each(function(i, element) {
					listWidth += $(element).outerWidth(true);
				});
				return listWidth;

			})(),
			alsoSwitch: {
				'.discuss-toolbar' : 'narrow'
			},
			targetFunction: function() {
				$('.discuss-toolbar').removeClass('wide');
			},
			reverseFunction: function() {
				$('.discuss-toolbar').addClass('wide');
			}
		}

	});
	<?php } ?>

	$('.discuss-toolbar .btn-navbar').click(function() {
		$('.ed-nav-collapse').toggleClass("collapse in",250); //transition effect required jQueryUI
		return false;
	});
});
