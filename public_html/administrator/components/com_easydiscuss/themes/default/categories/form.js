ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	$.Joomla('submitbutton', function(action){
		$.Joomla('submitform', [action]);
	});

	$(document)
		.on('click', '[data-ed-tab]', function() {

			var id = $(this).data('id');

			var activeInput = $('[data-ed-active-tab]');

			// Set the active tab value
			activeInput.val(id);
		});

	function groupCollapse(type) {
		if ($('#collapse-'+type).hasClass("in")) {
			$('#collapse-'+type).removeClass("in");
			$('#collapse-'+type).addClass(" collapse");
		} else {
			$('#collapse-'+type).removeClass("collapse");
			$('#collapse-'+type).addClass(" in");
		}
	}

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