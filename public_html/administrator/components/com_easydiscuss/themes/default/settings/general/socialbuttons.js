ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {
	$('[data-social-button-types]').on('change', function(el){
		var val = $(this).val();
		var defaultWrapper = $('[data-social-buttons-default-wrapper]');
		var addThisWrapper = $('[data-social-buttons-addthis-wrapper]');
		var shareThisWrapper = $('[data-social-buttons-sharethis-wrapper]');

		// Hide all first
		defaultWrapper.addClass('hidden');
		addThisWrapper.addClass('hidden');

		if (val == 'default') {
			defaultWrapper.removeClass('hidden');
		}

		if (val == 'addthis') {
			addThisWrapper.removeClass('hidden');
		}

		if (val == 'sharethis') {
			shareThisWrapper.removeClass('hidden');
		}
	});
});
