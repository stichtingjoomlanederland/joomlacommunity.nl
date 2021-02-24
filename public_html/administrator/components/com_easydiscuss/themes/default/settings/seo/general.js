ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {


$('[data-routing-behavior]').on('change', function() {
var selected = $(this).val();
var info = $('[data-routing-info]');

info.addClass('t-hidden');
info.filter('[data-type=' + selected + ']').removeClass('t-hidden');

});

$('[data-amp-logo-restore-default-button]').on('click', function() {
	EasyDiscuss.dialog({
		content: EasyDiscuss.ajax('admin/views/settings/confirmRestoreLogo'),
		bindings: {
			'{restoreButton} click': function() {
				EasyDiscuss.ajax('admin/controllers/settings/restoreLogo', {'type': 'amp'})
				.done(function() {
				window.location = 'index.php?option=com_easydiscuss&view=settings&layout=seo';

				EasyDiscuss.dialog().close();
				});
			}
		}
	});
});


$('[data-routing-post]').on('change', function() {
var selected = $(this).val();
var example = $('[data-post-example]');

example.addClass('t-hidden');
example.filter('[data-type=' + selected + ']').removeClass('t-hidden');
});

});