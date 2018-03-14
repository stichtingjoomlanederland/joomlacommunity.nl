ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

    $('[data-ed-check-alias]').on('click', function() {

        var alias = $('[data-ed-alias-input]').val();
        var loading = $('[data-ed-alias-loading]');
        var status = $('[data-ed-alias-status]');
        
        if (alias != '') {

            status.hide();
            loading.addClass('is-loading');

            EasyDiscuss.ajax('site/views/profile/ajaxCheckAlias', {
                "alias": alias
            }).done(function(exists, message) {

                var className = exists ? 'o-alert--danger-o' : 'o-alert--success-o';

                status.removeClass('o-alert--danger-o')
                status.removeClass('o-alert--success-o')
                status.removeClass('t-hidden')
                status.addClass(className);

                status.show();

                // Set the message
                status.html(message);

                loading.removeClass('is-loading');
            });
        }

    });

});
