ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	var indexSortTab = $('[data-index-sort-filter]');

	indexSortTab.on('change', function() {
		var sortType = $(this).val();
		var wrapper = $('[data-list-wrapper]');
		var list = $('[data-list-result]');
		var pagination = $('[data-tags-pagination]');

		// clear existing content.
		list.html('');

		// Show loading
		wrapper.addClass('is-loading');

		// Hide empty
		wrapper.removeClass('is-empty');

		EasyDiscuss.ajax('site/views/tags/filter', {
			'sort'  : sortType
		}).done(function(contents, paginationHTML) {

			if (contents.length <= 0) {
				wrapper.addClass('is-empty');
			} 

			if (contents.length > 0) {
				list.append(contents);
			}

			// Append pagination
			pagination.html(paginationHTML);

		})
		.always(function(){
			wrapper.removeClass('is-loading');
		});
	});
});
