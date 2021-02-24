<?php if ($loadScript) { ?>
ed.require(['edq'], function($) {
	$('[data-colorpicker-revert]').on('click', function() {
		var button = $(this);
		var revert = button.data('color');
		var input = button.parent().find('input');

		input.val(revert);

		// Since the colorpicker in Joomla is attached to joomla's jquery, use Joomla's jquery to trigger
		window.jQuery(input).trigger('paste.minicolors');
	});
});
<?php } ?>
