ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	console.log('loaded');
	$('[data-email-logo-restore]').on('click', function(){
		EasyDiscuss.dialog({
			content: EasyDiscuss.ajax('admin/views/settings/confirmRestoreEmailLogo'),
			bindings: {
				"{restoreButton} click": function() {
					EasyDiscuss.ajax('admin/controllers/settings/restoreEmailLogo')
					.done(function(url) {
						EasyDiscuss.dialog().close();

						window.location = url;
					});
				}
			}
		});
	});
});