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

    $('[data-schema-logo-restore-default-button]').on('click', function() {
        EasyDiscuss.dialog({
            content: EasyDiscuss.ajax('admin/views/settings/confirmRestoreLogo'),
            bindings: {
                '{restoreButton} click': function() {
                    EasyDiscuss.ajax('admin/controllers/settings/restoreLogo', {'type': 'schema'})
                    .done(function() {
                        window.location = 'index.php?option=com_easydiscuss&view=settings&layout=layout';

                        EasyDiscuss.dialog().close();
                    });
                }
            }
        });
    });
});