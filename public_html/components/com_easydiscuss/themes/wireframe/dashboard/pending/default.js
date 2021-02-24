ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

$(document).on('click.post.moderate', '[data-ed-moderate]', function() {

	var element = $(this);
	var parent = element.parents('[data-ed-pending]');
	var postId = parent.data('id');
	var task = element.data('ed-moderate');

	EasyDiscuss.dialog({
		content: EasyDiscuss.ajax('site/views/dashboard/' + task, {
			'id' : postId
		})
	});
});

});