ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	$.Joomla('submitbutton', function(action) {

		if (action == 'rules') {
			window.location.href	= 'index.php?option=com_easydiscuss&view=rules&from=badges';
			return;
		}

		$.Joomla('submitform', [action]);
	});
});