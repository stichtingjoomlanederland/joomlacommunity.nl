ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

    // Upon changing the avatar integrations, we need to hide items accordingly.
    $( '#layout_avatarIntegration' ).bind( 'change' , function(){

        if( $(this).val() == 'phpbb' )
        {
            $( '.phpbbWrapper' ).show();
        }
        else
        {
            $( '.phpbbWrapper' ).hide();
        }
    });
});