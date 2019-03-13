ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	$('[data-max-title-option]').on('change', function() {
		var checked = $(this).is(':checked');
		var form = $('[data-max-title-form]');

		if (checked) {
			form.removeClass('t-hidden');
			return;
		}

		form.addClass('t-hidden');
	});

	$('[data-moderation-threshold]').on('change', function() {
		var checked = $(this).is(':checked');
		var form = $('[data-moderation-threshold-wrapper]');

		if (checked) {
			form.removeClass('t-hidden');
			return;
		}

		form.addClass('t-hidden');

	});
});