ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

    $('[data-ed-mark-allread]').on('click', function() {


        EasyDiscuss.ajax('site/views/profile/ajaxMarkAllRead', {
        }).done(function(message) {

            $('[data-ed-allread-status]')
                .addClass('o-alert--success-o');
                
            // Set the message
            $('[data-ed-allread-status]').html(message);
        });

    });

});
