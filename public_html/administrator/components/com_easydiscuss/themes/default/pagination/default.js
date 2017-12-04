
ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	$(document)
		.on('click.pagination.link', '[data-grid-pagination] [data-pagination-link]', function(event) {
			event.preventDefault();
			event.stopPropagation();

			var link = $(this);
			var limitstart = link.data('limitstart');

			if (link.hasClass('active') || link.hasClass('disabled')) {
				return;
			}

			$('[data-limitstart-value]').val(limitstart);

			$.Joomla('submitform', []);
		});
});