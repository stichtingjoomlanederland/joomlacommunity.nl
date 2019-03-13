ed.require(['edq'], function($) {

	$.Joomla('submitbutton', function(action) {
		if (action == 'cancel') {
			window.location.href = '<?php echo JURI::root();?>administrator/index.php?option=com_easydiscuss&view=priorities';
			return;
		}

		$.Joomla('submitform', [action]);
	});
});