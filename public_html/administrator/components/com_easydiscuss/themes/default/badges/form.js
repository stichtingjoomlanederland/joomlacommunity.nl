ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	// Toggle options between achieving type
	$('[data-ed-badges-achieve-type]').on('change', function() {
		var type = $(this).val();

		// toggle points form
		if (type == 'points') {

			console.log('here');
			
			$('[data-ed-badges-points]').removeClass('t-hidden');
			$('[data-ed-badges-frequency]').addClass('t-hidden');

			return;
		}

		$('[data-ed-badges-points]').addClass('t-hidden');
		$('[data-ed-badges-frequency]').removeClass('t-hidden');
	})
});