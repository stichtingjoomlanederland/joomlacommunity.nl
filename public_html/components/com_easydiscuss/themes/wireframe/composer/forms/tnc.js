ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

var Tnc = {
	wrapper: '[data-ed-tnc]',
	preview: '[data-ed-tnc-preview]'
};

$(document).on('click.tnc.preview', Tnc.preview, function() {
	EasyDiscuss.dialog({
		content: EasyDiscuss.ajax('site/views/comment/showTnc', {
		})
	});
});


});