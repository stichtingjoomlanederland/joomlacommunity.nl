ed.require(['edq', 'admin/vendors/jquery.joomla'], function($) {

	$.Joomla( 'submitbutton' , function(action){
		$.Joomla('submitform', [action]);
	});

});