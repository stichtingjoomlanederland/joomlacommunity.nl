ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	var indexSortTab = $('[data-index-sort-filter]');

	indexSortTab.on('change', function() {
		var sortType = $(this).val();
		var list = $('[data-list-item]');
		var pagination = $('[data-tags-pagination]');

		// clear existing content.
		list.children('div.ed-tags__item').remove();

		// Show loading
		list.addClass('is-loading');

		// Hide empty
		list.removeClass('is-empty');

		EasyDiscuss.ajax('site/views/tags/filter', {
			'sort'  : sortType
		}).done(function(contents, paginationHTML) {

			if (contents.length <= 0) {
				list.addClass('is-empty');
			} else {
				// now append the content.
				list.append(contents);
			}

			// Append pagination
			pagination.html(paginationHTML);

		})
		.always(function(){
			list.removeClass('is-loading');
		});
	});
});
