
(function(){

// Create recaptcha task
var task = [
    'recaptcha_<?php echo $recaptchaUid;?>', {
        'sitekey': '<?php echo $public;?>',
        'theme': '<?php echo $colorScheme;?>'
    }
];

var runTask = function() {
    grecaptcha.render.apply(grecaptcha, task);
}

// If grecaptcha is not ready, add to task queue
if (!window.grecaptcha) {
    var tasks = window.recaptchaTasks || (window.recaptchaTasks = []);
    tasks.push(task);
// Else run task straightaway
} else {
    runTask(task);
}

// If recaptacha script is not loaded
if (!window.recaptchaScriptLoaded) {

    // Load the recaptcha library
    ed.require(["//www.google.com/recaptcha/api.js?onload=recaptchaCallback&render=explicit&hl=<?php echo $language;?>"]);

    window.recaptchaCallback = function() {
        var task;

        while (task = tasks.shift()) {
            runTask(task);
        }
    };

    window.recaptchaScriptLoaded = true;
}

})();


// We need to bind composer events
ed.require(['edq'], function($) {

    // Composer form being reset
    $(document)
    .on('composer.form.reset', '[data-ed-composer-form]', function(){

        // Reset recaptcha's form
        grecaptcha.reset();
    });

});