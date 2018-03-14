ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	$('[data-ed-moderation-post-approve]').on('click', function() {
		var button = $(this);
		var parent = button.parents('[data-ed-moderation-post]');
		var postId = parent.data('id');

		// Popup the dialog
		EasyDiscuss.dialog({
			content: EasyDiscuss.ajax('site/views/dashboard/confirmApprovePost', {
				'id' : postId
			}),
			bindings: {
				"{approveButtonDialog} click" : function() {
					this.form().submit();
				}
			}
		});
	});

	$('[data-ed-moderation-post-reject]').on('click', function() {
		var button = $(this);
		var parent = button.parents('[data-ed-moderation-post]');
		var postId = parent.data('id');

		// throw a dialog
		EasyDiscuss.dialog({
			content: EasyDiscuss.ajax('site/views/dashboard/confirmRejectPost', {
				'id' : postId
			})
		})
 	});
});