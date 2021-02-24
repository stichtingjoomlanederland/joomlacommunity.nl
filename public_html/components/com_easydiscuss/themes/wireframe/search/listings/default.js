ed.require(['edq', 'site/src/filters'], function($, filters) {

	$('body').on('easydiscuss.search.result', function(event, headers) {
		// console.log(headers);

		if (headers === undefined) {
			$('[data-search-header]').html('');
		} else {
			$('[data-search-header]').html(headers);
		}

	});

	filters.execute('[data-posts]');
});