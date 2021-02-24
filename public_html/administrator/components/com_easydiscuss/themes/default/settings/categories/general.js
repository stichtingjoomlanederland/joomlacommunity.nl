ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	$.Joomla('submitbutton', function(action){
		$.Joomla('submitform', [action]);
	});

	// Permission tab selector
	$(document).on('click', '[data-select-all]', function() {
		var element = $(this);
		var parent = element.parents('[data-permissions-container]');
		var items = parent.find('input:checkbox');

		parent.find('input[type=checkbox]')
			.attr('checked', 'checked')
			.trigger('change');
	});

	$(document).on('click', '[data-select-none]', function() {
		var element = $(this);
		var parent = element.parents('[data-permissions-container]');
		var items = parent.find('input:checkbox');

		parent.find('input[type=checkbox]')
			.removeAttr('checked')
			.trigger('change');
	});

});
