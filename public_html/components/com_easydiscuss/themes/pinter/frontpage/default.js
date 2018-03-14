ed.require(['edq', 'site/src/frontpage', 'site/src/profile'], function($, frontpage, profile) {

	// Find anchor links inside the tab
	var filters = $('[data-filter-anchor]');

	filters.on('click', function(event) {
		event.preventDefault();

		$(this).route();
	});

	frontpage.execute('[data-posts]');
});