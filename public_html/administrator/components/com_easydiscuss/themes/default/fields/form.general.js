ed.require(['edq', 'easydiscuss', 'admin/src/tabs'], function($, EasyDiscuss) {

	var fieldType = $('[data-ed-field-type]');

	$(document)
		.on('change.ed.field.type', fieldType.selector, function() {

			var type = $(this).val();

			EasyDiscuss.ajax('admin/views/customfields/getOptions', {
				"type": type,
				"id": "<?php echo $field->id;?>"
			}).done(function(output) {

				// Append the output on the right column
				$('[data-ed-fields-options]').html(output);
			});

		});

	
	// Add new custom field options
	var addOptionButton = $('[data-ed-field-add-option]');

	$(document)
		.on('click.ed.field.add.option', addOptionButton.selector, function() {

			var initialOption = $('[data-ed-field-option-initial]');
			var optionsList = $('[data-ed-field-options]');

			var clone = initialOption.clone();

			// Reset the field
			clone.find('input').val('');
			clone.removeAttr('data-ed-field-option-initial');

			// Append to the input
			clone.appendTo(optionsList);

			// Focus on the new input
			clone.find('input').focus();
		});

	// Remove a custom field option
	var removeOption = $('[data-ed-field-remove-option]');

	$(document)
		.on('click.ed.field.remove.option', removeOption.selector, function() {

			var parent = $(this).parents('[data-ed-field-option]');

			parent.remove();
		});

	// Bind the enter key on the field
	var optionInput = $('[data-ed-field-option] input[type=text]');

	$(document)
		.on('keyup.ed.field.option', optionInput.selector, function(event) {

			if (event.keyCode == 13) {
				addOptionButton.click();
			}
		});


	$.Joomla('submitbutton', function(action){

		$.Joomla('submitform', [action]);

		return;
	});

});