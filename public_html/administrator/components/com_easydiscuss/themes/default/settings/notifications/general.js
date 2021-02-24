ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {
	$('[data-email-logo-restore]').on('click', function(){
		EasyDiscuss.dialog({
			content: EasyDiscuss.ajax('admin/views/settings/confirmRestoreLogo'),
			bindings: {
				"{restoreButton} click": function() {
					EasyDiscuss.ajax('admin/controllers/settings/restoreLogo')
					.done(function() {
						EasyDiscuss.dialog().close();

						window.location = 'index.php?option=com_easydiscuss&view=settings&layout=notifications';
					});
				}
			}
		});
	});
});