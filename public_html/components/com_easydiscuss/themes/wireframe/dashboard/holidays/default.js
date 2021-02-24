ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	/**
	 * Toggle publishing state for a holiday
	 */
	$(document).on('click.publish.holiday', '[data-holiday-toggle]', function() {
		var element = $(this);
		var checked = element.is(':checked');
		var id = element.data('id');

		EasyDiscuss.ajax('site/views/dashboard/toggleHolidayState', {
			"id": id,
			"state": checked ? 1 : 0
		});
	});

	// Bind the filters actions
	$(document).on('click.delete.holiday', '[data-delete-holiday]', function() {
		var element = $(this);
		var item = element.parents('[data-holiday-item]');
		var id = item.data('id');
		
		EasyDiscuss.dialog({
			content: EasyDiscuss.ajax('site/views/dashboard/confirmDelete', { "id": id }),
			bindings: {
				"{submitButton} click": function() {

					// Remove the holiday item
					item.remove();

					EasyDiscuss.ajax('site/views/dashboard/deleteHoliday', {
						"id": id
					}).done(function() {
						EasyDiscuss.dialog().close();
					});
				}
			}
		})
	});
});
