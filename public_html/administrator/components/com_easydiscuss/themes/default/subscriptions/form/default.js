ed.require(['edq'], function($) {

	$('[data-subscription-type]').on('change', function() {
		var value = $(this).val();

		$('[data-subscriptions]').addClass('hide');

		if (value == 'site') {
			return;
		}

		$('[data-subscriptions=' + value + ']').removeClass('hide');
		return;
	});

	$.Joomla('submitbutton', function(action) {

		if (action == 'cancel') {
			window.location = '<?php echo JURI::base();?>index.php?option=com_easydiscuss&view=subscription';
			return;
		}

		if (action == 'save' || action == 'apply') {
			return $.Joomla('submitform', [action]);
		}

		return;
	});
});