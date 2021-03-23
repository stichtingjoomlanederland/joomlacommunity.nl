ed.require(['edq'], function($) {
	
	$('[data-association-type]').on('change', function() {
		var value = $(this).val();

		$('[data-type-category]').toggleClass('t-hidden', value != 'category');
	});
});