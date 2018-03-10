ed.require(['edq', 'site/src/forums'], function($, forums) {

	// Find anchor links inside the tab
	var filters = $('[data-filter-anchor]');

	filters.on('click', function(event) {
		event.preventDefault();

		$(this).route();
	});

	forums.execute('[data-forums]');
});