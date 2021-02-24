ed.require(['edq', 'iconpicker'], function($) {
	
	$('[data-icon-selection]').iconpicker();

	// This is to prevent iconpicker container disappear when click on the search input
	$('.iconpicker-search').on('click', function(e) {
		e.stopPropagation();
	});

	// Updated the input value when icon is selected
	$('[data-icon-selection]').on('iconpickerSelected', function (e) {
		$('[data-icon-input]').val(e.iconpickerValue);
	});
});