ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {
		$.Joomla( 'submitbutton' , function( action ){
		$.Joomla( 'submitform' , [action] );
	});
});
