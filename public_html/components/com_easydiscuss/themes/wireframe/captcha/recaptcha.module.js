(function($){

var moduleWidgetId = null;

// Create recaptcha task
var task = [
	'mod_recaptcha_<?php echo $recaptchaUid;?>', {
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
	moduleWidgetId = grecaptcha.render($('[data-ed-module-recaptcha-invisible]')[0], {
				"sitekey": "<?php echo $key;?>"
	});
	<?php } ?>
}

<?php if ($invisible) { ?>

window.getResponseModule = function() {
	var token = grecaptcha.getResponse(moduleWidgetId);
	$('[data-ed-module-recaptcha-response]').val(token);

	$('[data-ed-module-form]').submit();
};

$(document).on('click', '[data-ed-module-submit]', function(event) {
	event.preventDefault();

	grecaptcha.execute(moduleWidgetId);
});
<?php } ?>

// If grecaptcha is not ready, add to task queue
if (!window.recaptchaScriptLoadedModule) {
	var tasks = window.recaptchaModuleTasks || (window.recaptchaModuleTasks = []);

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
		window.recaptchaModuleTasks = tasks;
	}
// Else run task straightaway
} else {
	runTask(task);
}

// If recaptacha script is not loaded
if (!window.recaptchaScriptLoadedModule) {
	
	// Load the recaptcha library
	ed.require(["//www.google.com/recaptcha/api.js?onload=recaptchaCallbackModule&render=explicit&hl=<?php echo $language;?>"]);

	window.recaptchaCallbackModule = function() {
		var task;

		while (task = window.recaptchaModuleTasks.shift()) {
			runTask(task);
		}
	};

	window.recaptchaScriptLoadedModule = true;
}

})(jQuery);


// We need to bind composer events
ed.require(['edq'], function($) {

	// Composer form being reset
	$(document)
	.on('composer.form.reset', '[data-ed-composer-form]', function(){

		<?php if (!$invisible) { ?>
		// Reset recaptcha's form
		grecaptcha.reset();
		<?php } ?>
	});

});
