FD31.plugin("joomla", function($) {

/**
 * joomla
 * Abstraction layer for Joomla client-side API.
 * https://github.com/foundry-modules/joomla
 *
 * Copyright (c) 2012 Jason Ramos
 * www.stackideas.com
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

var parser = {
	squeezebox: function() {
		return (self.isJoomla15) ? window.parent.document.getElementById('sbox-window') : window.parent.SqueezeBox;
	}
};

var self = $.Joomla = function(method, args) {

	// Overriding function
	if ($.isFunction(args)) {

		var fn = args;

		if (self.isJoomla15) {
			window[method] = fn;
		} else {
			window.Joomla[method] = fn;
		};

		return;
	}

	// Calling function
	var method = parser[method] || ((self.isJoomla15) ? window[method] : window.Joomla[method]);

	if ($.isFunction(method)) {
		return method.apply(window, args);
	}
};

var version = parseFloat($.joomla.version);
self.isJoomla15 = version <= 1.5;
self.isJoomla25 = version >= 1.6 && version <= 2.5;
self.isJoomla30 = version >= 3.0;
});