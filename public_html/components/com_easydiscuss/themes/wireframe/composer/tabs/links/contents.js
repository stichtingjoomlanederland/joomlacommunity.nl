ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	var wrapper = $('[<?php echo $editorId;?>]');
	var list = wrapper.find('[data-ed-links-list]');
	var insert = wrapper.find('[data-ed-links-insert]');
	var remove = wrapper.find('[data-ed-links-remove]');

	// When a reply form is edited / replied, reset the form
	$(document)
	.on('composer.form.reset', '[data-ed-composer-form]', function(){

		resetForm($(this));
	});

	function resetForm(form) {

		list
			.children(":not(:first-child)")
			.remove();

	};

	// Bind the add url reference
	$(document)
		.on('click.ed.links.insert', insert.selector, function() {

			// Clone the first item on the list.
			var item = list.children(':first').clone();

			// Clear the input
			item.find('input').val('');

			// Append item into the list.
			list.append(item);
		});

	// Bind the remove url reference
	$(document)
		.on('click.ed.links.remove', remove.selector, function() {

			// Remove the parent
			$(this).parent().remove();
		});
});