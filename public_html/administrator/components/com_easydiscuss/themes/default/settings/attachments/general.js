ed.require(['edq', 'easydiscuss'], function($) {

	$('input[name=enable_attachment_limit]').on('change', function() {
		var element = $(this);

		if (element.val() == 1) {
			$('[data-ed-attachment-limit]').removeClass('t-hidden');
			return;
		}

		$('[data-ed-attachment-limit]').addClass('t-hidden');

	});
});