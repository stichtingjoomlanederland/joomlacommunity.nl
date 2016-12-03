ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	var filters = $('[data-ed-badge-filters');

	// Bind the filters actions
	filters.on('click', function() {

		// Remove all active class on the filters
		filters.removeClass('active');

		// Add an active class to this current filter
		var filter = $(this);
		var type = filter.data('type');

		filter.addClass('active');

		var items = $('[data-ed-badges-item]');

		// Show everything for all filter
		if (type == 'all') {
			items.show();
		}

		if (type == 'mine') {
			// Hide all items first.
			items.hide();

			// Show only earned badges
			items.filter('.is-earned').show();
		}
	});
});
