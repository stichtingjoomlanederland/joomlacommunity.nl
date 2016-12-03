ed.require(['edq'], function($) {

    $('[data-ed-captcha-type]').on('change', function() {
        var selected = $(this).val();
        var options = $('[data-captcha-settings]');

        options.addClass('t-hidden');

        if (selected == 'none') {
            return;
        }

        var option = options.filter('[data-type=' + selected + ']');

        if (option.length <= 0) {
            return;
        }

        option.removeClass('t-hidden');
    });
});
