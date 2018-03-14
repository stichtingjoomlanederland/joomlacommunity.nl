ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	$.Joomla('submitbutton' , function(action){
		if ( action == 'save' || action == 'savePublishNew' ) {
			if( action == 'savePublishNew' ) {
				action = 'save';
				$( '#savenew' ).val( '1' );
			}
		}
		$.Joomla( 'submitform' , [action] );
	});
});