ed.require(['edq', 'iconpicker'], function($) {
		
	var picker = $('[data-icon-selection]');

	picker.iconpicker();

	// This is to prevent iconpicker container disappear when click on the search input
	$('.iconpicker-search').on('click', function(e) {
		e.stopPropagation();
	});

	// Updated the input value when icon is selected
	var updateIcon = function(e) {

		$('[data-icon-input]').val(e.iconpickerValue);

		if (e.iconpickerValue) {
			$('[data-icon-remove]').removeClass('t-hidden');
		}
	};

	picker.on('iconpickerSelected', updateIcon);
	picker.on('iconpickerSetValue', updateIcon);

	$('[data-icon-remove]').on('click', function(event) {
		event.preventDefault();

		var iconpicker = picker.data('iconpicker');

		iconpicker.setValue('');
		iconpicker.setSourceValue('');

		iconpicker._updateComponents();

		$('[data-icon-remove]').addClass('t-hidden');
	});
});