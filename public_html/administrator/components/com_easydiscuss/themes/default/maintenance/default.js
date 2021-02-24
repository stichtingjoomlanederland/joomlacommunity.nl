ed.require(['edq'], function($) {
	$.Joomla('submitbutton', function(task) {
		if (task == 'maintenance.form') {
			document.adminForm.layout.value = 'form';
			$.Joomla('submitform');
		}
	});
});
