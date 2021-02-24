ed.require(['edq', 'site/src/filters', 'site/src/postcount'], function($, filters, counter) {

	filters.execute('[data-category]');

	// get category post count
	counter.execute('[data-category]');
});