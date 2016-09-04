var RSFormProReCAPTCHAv2 = {
	loaders: [],
	onLoad: function() {
		window.setTimeout(function(){
			for (var i = 0; i < RSFormProReCAPTCHAv2.loaders.length; i++) {
				var func = RSFormProReCAPTCHAv2.loaders[i];
				if (typeof func == "function") {
					try {
						func();
					} catch (err) {
						if (console && typeof console.log == 'function') {
							console.log(err);
						}
					}
				}
			}
		}, 500)
	}
};

if (typeof jQuery !== 'undefined') {
	jQuery(document).ready(function($) {
		$(window).load(RSFormProReCAPTCHAv2.onLoad);
	});
} else if (typeof MooTools !== 'undefined') {
	window.addEvent('domready', function(){
		 window.addEvent('load', RSFormProReCAPTCHAv2.onLoad);
	});
} else {
	RSFormProUtils.addEvent(window, 'load', function() {
		RSFormProReCAPTCHAv2.onLoad();
	});
}