ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {


$(document).on('click.tab', '[data-ed-toggle=tab]', function() {
	var element = $(this);
	var parent = element.parent();

	$('[ data-ed-tabs]').removeClass('active');

	parent.addClass('active');
});


});
