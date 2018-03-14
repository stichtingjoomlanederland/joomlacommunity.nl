ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {
	$('[data-ed-spoiler-toggle]').click(function() {
		var content = $(this).siblings('[data-ed-spoiler-content]');

		if (content.hasClass('t-hidden')) {
			content.removeClass('t-hidden');
		} else {
			content.addClass('t-hidden');
		}
	});
});
