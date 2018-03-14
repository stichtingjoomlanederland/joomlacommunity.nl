ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

// Something is not right with datepicker code below. Will comment it out for now.
	// $("#datepicker").datepicker({
	// 	dateFormat: "DD, d MM, yy"
	// });

	// $.datepicker.setDefaults($.datepicker.regional[ "" ]);

	// window.showDescription = function(id) {
	// 	$('.rule-description').hide();
	// 	$('#rule-' + id).show();
	// }

	// $.Joomla('submitbutton', function(action){
	// 	if (action == 'save' || action == 'saveNew'){
	// 		if(action == 'saveNew') {
	// 			$('#savenew').val('1');
	// 			action = 'save';
	// 		}
	// 	}

	// 	$("#datepicker").datepicker("option", "dateFormat", "yy-mm-dd");

	// 	$.Joomla('submitform', [action]);
	// });

	// Toggle options between achieving type
	$('[data-ed-badges-achieve-type]').on('change', function() {
		var type = $(this).val();

		// toggle points form
		if (type == 'points') {
			$('[data-ed-badges-points]').removeClass('hidden');
			$('[data-ed-badges-frequency]').addClass('hidden');
		} else {
			$('[data-ed-badges-points]').addClass('hidden');
			$('[data-ed-badges-frequency]').removeClass('hidden');
		}
	})
});