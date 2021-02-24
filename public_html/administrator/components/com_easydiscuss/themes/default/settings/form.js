ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	// Append the settings search to the toolbar
	$(document).ready(function() {
		var searchWrapper = $('[data-search-wrapper]');
		var searchResult = $('[data-search-result]');
		var searchInput = $('[data-settings-search]');

		searchWrapper
			.appendTo('#toolbar')
			.removeClass('t-hidden');


		searchInput.on('keyup', $.debounce(function() {
			var search = $(this).val();

			if (search == '') {
				searchResult.html('')
					.addClass('hidden');
				return;
			}
			
			EasyDiscuss.ajax('admin/views/settings/search', {
				'text': search
			}).done(function(output) {
				searchResult
					.html(output)
					.removeClass('hidden');
			});

		}, 250));

		$('body').on('click', function(event) {
			var target = $(event.target);

			if (target.is(searchInput) || target.is(searchResult) || target.is(searchWrapper) || target.parents().is(searchResult)) {
				return;
			}

			searchResult.addClass('hidden');
		});

		$('[data-toggle]').on('click', function() {
			var tab = $(this);
			var id = tab.data('id');

			$('[data-pp-active-tab]').val(id);
		});

		<?php if ($goto) { ?>
		var element = $('[data-uid=<?php echo $goto;?>]');
		var wrapper = element.parents('.o-form-group');

		wrapper.css({
			'background': '#fff9c4',
			'transition': 'background 1.0s ease-in-out'
		});

		var resetBackground = function() {
			wrapper.css({
				'background': 'none'
			});
		};

		setInterval(function() {
			resetBackground();
		}, 5000);

		$.scrollTo(element);
		<?php } ?>

	});

	$(document)
		.on('click', '[data-form-tabs]', function() {
			var id = $(this).data('id');
			var activeInput = $('[data-ed-active-tab]');

			// Set the active tab value
			activeInput.val(id);
		});


});