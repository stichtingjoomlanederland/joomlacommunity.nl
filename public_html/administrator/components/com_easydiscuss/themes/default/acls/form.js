ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	var checkRules = function(type) {

		var value = type == 'yes' ? 1 : 0;

		$('.btn-group-yesno .btn').removeClass('active');
		$('.btn-group-yesno .btn-' + type).addClass('active');
		$('.btn-group-yesno input[type="hidden"]').val(value);
	}

	$('[data-select-all]').on('click', function() {
		var parent = $(this).parents('[data-tab-item]');

		parent.find('[data-ed-acl-rule]')
			.attr('checked', 'checked')
			.trigger('change');
	});

	$('[data-select-none]').on('click', function() {
		var parent = $(this).parents('[data-tab-item]');

		parent.find('[data-ed-acl-rule]')
			.removeAttr('checked')
			.trigger('change');
	});

	$('[data-ed-acl-rule]').on('change', function() {
		var checked = $(this).is(':checked');
		var parent = $(this).parents('[data-ed-acl-option]');

		if (checked) {

			parent.find('[data-ed-acl-disallowed]')
				.addClass('t-hidden');

			parent.find('[data-ed-acl-allowed]')
				.removeClass('t-hidden');

			return;
		}
		
		parent.find('[data-ed-acl-disallowed]')
			.removeClass('t-hidden');
			
		parent.find('[data-ed-acl-allowed]')
			.addClass('t-hidden');
	});	
});