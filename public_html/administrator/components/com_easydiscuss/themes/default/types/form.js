ed.require(['edq'], function($) {
	
	$('[data-type]').on('change', function() {
		var value = $(this).val();

		$('[data-type-category]').toggleClass('hide', value != 'category');
	});
});