ed.require(['edq', 'select2'], function($) {

	var selector = $('[data-ed-tags-select]');
	selector.select2({
		'theme': 'default select2-container--ed-front',
		tags: <?php echo ($this->acl->allowed('add_tag')) ? 'true' : 'false' ?>,
		maximumSelectionLength: <?php echo $this->config->get('max_tags_allowed'); ?>,
		createTag: function(params) {
			<?php if (!$this->acl->allowed('create_tag')) { ?>
				return null;
			<?php } ?>

			return {
				"id": params.term,
				"text": params.term
			}
		}
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