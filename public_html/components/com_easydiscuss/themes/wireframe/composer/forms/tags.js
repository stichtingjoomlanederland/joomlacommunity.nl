ed.require(['edq', 'select2'], function($) {

	var selector = $('[data-ed-tags-select]');
	selector.select2({
		tags: <?php echo ($this->acl->allowed('add_tag')) ? 'true' : 'false' ?>,
		maximumSelectionLength: 10
	});

	var checkCount = function(n) {
		var count = $('[data-ed-tags-list] .select2-selection__choice').length;

		$('[data-ed-used-tags]').html(count);
	};

	// Checks if a tag already exist in the list
	var tagExists = function(tag) {
		var selected = [];

		selector
			.children(':selected')
			.each(function() {
				var val = $(this).val();

				if (val == "") {
					return;
				}

				selected.push($(this).val());
			});

		var exists = $.inArray(tag, selected);

		return exists !== -1;
	}

	// Bind chosen tags
	selector.on('change', checkCount);
});