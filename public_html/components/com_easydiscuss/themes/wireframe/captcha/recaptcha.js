ed.require(['edq'], function($) {

var widgetId = null;

// Create recaptcha task
var task = [
	'recaptcha_<?php echo $recaptchaUid;?>', {
		'sitekey': '<?php echo $public;?>',
		'theme': '<?php echo $colorScheme;?>'
	}
];


var runTask = function(task) {
	<?php if (!$invisible) { ?>
	// Captcha input
	grecaptcha.render.apply(grecaptcha, task);
	<?php } ?>

	<?php if ($invisible) { ?>
	// Invisible captcha
	widgetId = grecaptcha.render($('[data-ed-recaptcha-invisible]')[0], {
				"sitekey": "<?php echo $key;?>"
	});
	<?php } ?>
}

<?php if ($invisible) { ?>
window.recaptchaDfd = $.Deferred();

window.getResponse = function() {

	var token = grecaptcha.getResponse(widgetId);
	var responseField = $('[data-ed-recaptcha-response]');

	if (token) {
		responseField.val(token);

		window.recaptchaDfd.resolve();

		return;
	}

	grecaptcha.reset(widgetId);

	window.recaptchaDfd.reject();
};

$(document).on('onSubmitPost', function(event, save) {
	save.push(window.recaptchaDfd);

	grecaptcha.execute(widgetId);
});

$(document).on('reloadCaptcha', function(event) {
	setTimeout(function() {
		grecaptcha.reset(widgetId);

		window.recaptchaDfd = $.Deferred();
	}, 500);
});
<?php } ?>

// If grecaptcha is not ready, add to task queue
if (!window.recaptchaScriptLoaded) {
	var tasks = window.recaptchaTasks || (window.recaptchaTasks = []);

	var found = false;
	// check if this task already registered or not.
	$(tasks).each(function(idx, item) {
		if (item[0] == task[0]) {
			found = true;
			return false;
		}
	});

	if (found === false) {
		tasks.push(task);
		window.recaptchaTasks = tasks;
	}

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
		while (task = window.recaptchaTasks.shift()) {
			runTask(task);
		}
	};

	window.recaptchaScriptLoaded = true;
}

});

// We need to bind composer events
ed.require(['edq'], function($) {
	// Composer form being reset
	$(document)
		.on('composer.form.reset', '[data-ed-composer-form]', function() {

			<?php if (!$invisible) { ?>
				grecaptcha.reset();
			<?php } ?>
		});
});