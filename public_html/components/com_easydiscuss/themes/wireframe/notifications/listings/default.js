ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	$(document).on('click.notifications.readall', '[data-ed-notifications-read-all]', function() {

		EasyDiscuss.ajax('site/views/notifications/markreadall')
		.done(function() {

			$('[data-ed-notifications-item]')
				.removeClass('is-unread')
				.addClass('is-read');
		});
	});
});