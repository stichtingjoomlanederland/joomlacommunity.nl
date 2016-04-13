ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {


	$( "#datepicker" ).datepicker({
		dateFormat: "DD, d MM, yy"
	});

	$.datepicker.setDefaults( $.datepicker.regional[ "" ] );

	window.showDescription = function(id) {
		$( '.rule-description').hide();
		$( '#rule-' + id ).show();
	}

	$.Joomla( 'submitbutton' , function(action ){
		if( action == 'save' || action == 'saveNew' ){
			if( action == 'saveNew' ) {
				$( '#savenew' ).val( '1' );
				action = 'save';
			}
		}

		$( "#datepicker" ).datepicker( "option", "dateFormat", "yy-mm-dd" );

		$.Joomla( 'submitform' , [action] );
	});

});
