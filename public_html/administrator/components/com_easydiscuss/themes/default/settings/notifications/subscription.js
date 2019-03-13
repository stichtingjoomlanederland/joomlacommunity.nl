ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	$('#main_email_digest').bind('change', function() {
		var checked = $(this).is(':checked');

		if (checked) {
			$('[data-subscription-interval]').removeClass('hide');
			return;
		}

		$('[data-subscription-interval]').addClass('hide');
	});
});