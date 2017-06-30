ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	$('#integration_google_adsense_responsive').bind('change', function() {
		var checked = $(this).is(':checked');

		if (checked) {
			$('[data-responsive-form]').removeClass('hide');
			$('[data-code-form]').addClass('hide');

			return;
		}

		$('[data-responsive-form]').addClass('hide');
		$('[data-code-form]').removeClass('hide');
	});
});