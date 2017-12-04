ed.require(['edq', 'easydiscuss'], function($) {

	$('select[name=storage_attachments]').on('change', function() {
		var value = $(this).val();

		if (value == 'amazon') {
			$('[data-storage-amazon]').removeClass('t-hidden');

			return;
		}
	});
});