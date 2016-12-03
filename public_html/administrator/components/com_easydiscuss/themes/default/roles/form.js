ed.require(['edq', 'easydiscuss', 'ui/datepicker'], function($, EasyDiscuss) {

	// Apply datepicker
	$( "#datepicker" ).datepicker({
		dateFormat: "DD, d MM, yy"
	});

	$.datepicker.setDefaults( $.datepicker.regional[ "" ] );

	$.Joomla( 'submitbutton' , function(action){
		if ( action != 'remove' || action == 'savePublishNew' ) {
			$.Joomla( 'submitform' , [action] );
			if( action == 'savePublishNew' ) {
				action = 'save';
				$( '#savenew' ).val( '1' );
			}
			$( "#datepicker" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
			$.Joomla( 'submitform' , [action] );
		}
	});
});