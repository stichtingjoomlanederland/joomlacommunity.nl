ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	var deleteHoliday = $('[data-delete-holiday');
	var publishToggle = $('[data-holiday-toggle');

	publishToggle.on('click', function() {

		var state = $(this).attr('checked') ? 1 : 0;
		var id = $(this).data('id');

		EasyDiscuss.ajax('site/views/dashboard/toggleState', {
			"id": id,
			"state": state
		});
	});

	// Bind the filters actions
	deleteHoliday.on('click', function() {

		var id = $(this).data('id');
		var row = $(this).parents('[data-holiday-item]');
		
		EasyDiscuss.dialog({
			content: EasyDiscuss.ajax('site/views/dashboard/confirmDelete', { "id": id }),
			bindings: {
				"{submitButton} click": function() {

					// Remove the holiday item
					row.remove();

					// When the delete button is clicked, call the ajax method to remove the holiday item
					EasyDiscuss.ajax('site/views/dashboard/delete', {
						"id": id
					}).done(function() {

						// Hide the dialog
						EasyDiscuss.dialog().close();
					});
				}
			}
		})
	});
});
