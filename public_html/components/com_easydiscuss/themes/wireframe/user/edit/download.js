ed.require(['edq'], function($) {
	$('[data-ed-gdpr-request]').on('click', function() {
		EasyDiscuss.dialog({
			content: EasyDiscuss.ajax('site/views/profile/confirmDownload')
		});
	});
});
