ed.require(['edq'], function($) {

	$.Joomla('submitbutton', function(action) {
		$.Joomla('submitform', [action]);
	});
});