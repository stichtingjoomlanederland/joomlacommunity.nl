ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

$(document).on('onValidatePost.easydiscuss.post', function(event, tasks, button) {
	var dfd = $.Deferred();

	tasks.push(dfd);

	var wrapper = $('[data-ed-custom-fields]');

	// highlight the field tab
	var tab = $('[data-tab-group="field"]');

	// Remove any error classes
	tab.removeClass('has-error');

	// Get all required groups
	var requiredFields = wrapper.find('[data-fields-required]');

	if (requiredFields.length <= 0) {
		dfd.resolve();
		return;
	}

	var fieldRequired = false;

	requiredFields.each(function(index, field) {
		var field = $(field);
		var type = field.data('field-type');

		if (type == 'text') {
			var textbox = field.find('[data-ed-textbox-fields]');

			if (textbox.val() == '') {
				fieldRequired = true;
				field.addClass('has-error');
			}
		}

		if (type == 'area') {
			var textarea = field.find('[data-ed-textarea-fields]');

			if (textarea.val() == '') {
				fieldRequired = true;
				field.addClass('has-error');
			}
		}

		if (type == 'radio') {
			var radio = field.find('[data-ed-radio-fields]');

			if (!radio.is(':checked')) {
				fieldRequired = true;
				field.addClass('has-error');
			}
		}

		if (type == 'check') {
			var checkbox = field.find('[data-ed-checkbox-fields]');

			if (!checkbox.is(':checked')) {
				fieldRequired = true;
				field.addClass('has-error');
			}
		}

		if (type == 'select') {
			var select = field.find('[data-ed-select-fields]');

			if (!select.is(':selected')) {
				fieldRequired = true;
				field.addClass('has-error');
			}
		}

		if (type == 'multiple') {
			var multiple = field.find('[data-ed-select-multiple-fields]');

			if (!multiple.is(':selected')) {
				fieldRequired = true;
				field.addClass('has-error');
			}
		}
	});

	if (fieldRequired) {
		tab.addClass('has-error');
		dfd.reject();

		return false;
	}

	dfd.resolve();
});



});