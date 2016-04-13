ed.require(['edq'], function($){

    // Script to handle expand / hide of sidebar items
    $('[data-sidebar-link]').on('click', function() {

        var link = $(this);

        link
            .parents('[data-sidebar-item]')
            .toggleClass('active');
    });

    // Fix the header for mobile view
    $('.container-nav').appendTo($('.header'));

    $(window).scroll(function () {
        if ($(this).scrollTop() > 50) {
            $('.header').addClass('header-stick');
        } else if ($(this).scrollTop() < 50) {
            $('.header').removeClass('header-stick');
        }
    });

    $('.nav-sidebar-toggle').click(function(){
        $('html').toggleClass('show-easydiscuss-sidebar');
        $('.subhead-collapse').removeClass('in').css('height', 0);
    });

    var wrapper = $('[data-ed-wrapper]');

    $(document).ready(function() {
        // Perform version checks
        EasyDiscuss
        .ajax('admin/views/easydiscuss/versionChecks')
        .done(function(localVersion, serverVersion, outdated) {

            // Applicable only on dashboard
            $('[data-online-version]').html(serverVersion);
            $('[data-local-version]').html(localVersion);

            if (outdated) {
                wrapper.addClass('is-outdated');

                // This is only applicable to the dashboard view
                $('[data-version-checks]').toggleClass('require-updates');

                return;
            }

            // This is only applicable to the dashboard view
            $('[data-version-checks]').toggleClass('latest-updates');
        })
        .fail(function() {
            // Add a class on the wrapper showing errors.
            $('[data-version-checks]')
                .addClass('has-errors');
        });
    });

});
