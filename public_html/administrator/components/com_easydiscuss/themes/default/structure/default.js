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

        $.ajax({
            url: "<?php echo ED_SERVICE_VERSION;?>",
            jsonp: "callback",
            dataType: "jsonp",
            data: {
                "apikey": "<?php echo $this->config->get('main_apikey');?>",
                "current": "<?php echo $version;?>"
            },
            success: function(data) {
                var version = {
                    "latest": data.version,
                    "local": "<?php echo $version;?>"
                };

                var outdated = version.latest > version.local;
                
                // Applicable only on dashboard
                $('[data-online-version]').html(version.latest);
                $('[data-local-version]').html(version.local);

                if (outdated) {
                    wrapper.addClass('is-outdated');

                    // This is only applicable to the dashboard view
                    $('[data-version-checks]').toggleClass('require-updates');

                    return;
                }

                // This is only applicable to the dashboard view
                $('[data-version-checks]').toggleClass('latest-updates');
            }
        });

    });

});
