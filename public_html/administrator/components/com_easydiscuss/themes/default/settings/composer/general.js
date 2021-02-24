ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {
	var moderatePosts = $('[data-moderation-threshold]')
	var moderateReplies = $('[data-reply-moderation-threshold]')

	$('[data-max-title-option]').on('change', function() {
		var checked = $(this).is(':checked');
		var form = $('[data-max-title-form]');

		if (checked) {
			form.removeClass('t-hidden');
			return;
		}

		form.addClass('t-hidden');
	});

	// 'Moderate New Posts' settings
	moderatePosts.on('change', function() {
		var checked = $(this).is(':checked');
		var replyChecked = moderateReplies.is(':checked');
		var form = $('[data-moderation-threshold-wrapper]');

		if (checked || replyChecked) {
			form.removeClass('t-hidden');
			return;
		}

		form.addClass('t-hidden');
	});

	// 'Moderate New Replies' settings
	moderateReplies.on('change', function() {
		var checked = $(this).is(':checked');
		var postChecked = moderatePosts.is(':checked');
		var form = $('[data-moderation-threshold-wrapper]');

		if (checked || postChecked) {
			form.removeClass('t-hidden');
			return;
		}

		form.addClass('t-hidden');
	});
});