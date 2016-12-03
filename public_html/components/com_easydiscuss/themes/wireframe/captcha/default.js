ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

    var reloadCaptchaButton = $('[data-ed-captcha-reload]');

    // Composer form being reset
    $(document)
    .on('composer.form.reset', '[data-ed-composer-form]', function(){
        var form = $(this);
        var reload = form.find(reloadCaptchaButton.selector);
            
        // Run the reload
        reload.click();
    });

	$(document)
	.on('click.ed.captcha.reload', reloadCaptchaButton.selector, function() {

		var captchaId = $('[data-ed-captcha-id]');
        var captchaImage = $('[data-ed-captcha-image]');

        // Disable the button first
        var reloadButton = $(this);

        reloadButton.prop('disabled', true);

		// Reload captcha
		EasyDiscuss.ajax('site/views/captcha/reload', {
			"id": captchaId.val()
		}).done(function(newId, imageSource) {
            
            // Update the hidden input
            captchaId.val(newId);

            // Update the new image
            captchaImage.attr('src', imageSource);
		}).always(function() {

            // Always re-enable the button
            reloadButton.prop('disabled', false);
        });
	});

});