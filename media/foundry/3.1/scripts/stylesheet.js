FD31.plugin("stylesheet", function($) {

/**
 * jquery.stylesheet
 * Stylesheet injector utility with workarounds
 * for IE's 31 stylesheet limitation.
 *
 * Copyright (c) 2012 Jensen Tonne
 * www.jstonne.com
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

var head = document.getElementsByTagName('head')[0],
	stylesheets = document.styleSheets,
	IE_STYLESHEET = document.createStyleSheet,
	IE_MAX_STYLESHEET = 31,
	IE_MAX_IMPORT = 31,
	IE_MAX_RULE = 4095;

$.stylesheet = (function() {

	var self = function(url, attr) {

		var options = {};

		$.extend(

			options,

			self.defaultOptions,

			($.isPlainObject(url)) ?
				url :
				{
					url: url,
					attr: attr || {}
				}
		);

		// Create a new stylesheet object
		if (options.url===undefined) {

			return self.create(options);
		}

		// Loading an external stylesheet
		return self.load(options);
	};

	$.extend(self, {

		defaultOptions: {

			type: "text/css",

			rel: "stylesheet",

			media: "all",

			title: "",

			// Force link injection, ignores IE workarounds, overrides XHR value.
			forceInject: false,

			// @TODO: XHR loading.
			xhr: false
		},

		setup: function(options) {

			$.extend(self.defaultOptions, options);
		},

		availability: function() {

			// @TODO: Also calculate bleedImports.
			var stat = {},
				links = $('link[rel*="stylesheet"]')
				styles = $('style');

			stat.groups = IE_MAX_STYLESHEET - links.length - styles.length;

			stat.slots = stat.groups * IE_MAX_IMPORT;

			if (self.currentGroup) {
				stat.slots += IE_MAX_IMPORT - self.currentGroup.imports.length;
			}

			return stat;
		},

		get: function(style) {

			var i=0;

			for (; i<stylesheets.length; i++) {

				var stylesheet = stylesheets[i];

				if ((stylesheet.ownerNode || stylesheet.owningElement)==style) {
					return stylesheet;
				}
			}
		},

		create: function(options) {

			var stylesheet,
				style,
				length = stylesheets.length;

			if (IE_STYLESHEET) {
				// Unable to create further stylesheets
				if (length>=IE_MAX_STYLESHEET) return;
				stylesheet = document.createStyleSheet();
				style = stylesheet.ownerNode || stylesheet.owningElement;
			} else {
				style = document.createElement('style');
				head.appendChild(style);
			}

			$.extend(style, {
				type : options.type,
				title: options.title,
				media: options.media,
				rel  : options.rel
			});

			// Insert stylesheet content
			var content = options.content;
			if (content!==undefined) {
				if (IE_STYLESHEET) {
					stylesheet.cssText = content;
				} else {
					style.appendChild(document.createTextNode(content));
				}
			}

			return style;
		},

		nextAvailable: function(alsoCreateIfUnavailable) {

			var stylesheet,
				length = stylesheets.length;

			if (length) {

				var i;

				for (i=length; i--; i<0) {

					stylesheet = stylesheets[i];

					// If this is IE and the maximum amount of rules have exceeded,
					if (IE_STYLESHEET && ((stylesheet.cssRules || stylesheet.rules).length >= IE_MAX_RULE)) {

						// then this stylesheet cannot be used.
						stylesheet = undefined;

						// try an older stylesheet.
						continue;
					}

					break;
				}
			}

			return stylesheet.ownerNode || stylesheet.owningElement || ((alsoCreateIfUnavailable) ? self() : undefined);
		},

		load: function(options) {

			if ($.browser.msie && !options.forceInject) {

				return self._import(options);

			} else {

				// @TODO: Use onload/onerror events on browsers that support them.
				var link =
					$('<link>')
						.attr({
							href: options.url,
							type: options.type,
							rel: options.rel,
							media: options.media
						})
						.appendTo('head');

				return link[0];
			}
		},

		_import: function(options) {

			var failed;

			if (self.currentGroup===undefined) {

				var group;

				try {

					group = document.createStyleSheet();

					// It is only a getter on IE.
					// group.type = "text/css";

					group.media = "all";
					group.title = "jquery_stylesheet";

				} catch(e) {

					failed = true;

					if (options.verbose) {
						console.error('There is not enough slots left to create a new stylesheet group.');
					}
				}

				if (failed) return false;

				self.currentGroup = group;
			}

			try {

				self.currentGroup.addImport(options.url);

			} catch(e) {

				failed = true;

				if (options.verbose) {
					console.info('Import slots exceeded. Creating a new stylesheet group.');
				}
			}

			if (failed) {

				self.currentGroup = undefined;

				return self._import(options);
			}

			return true;
		}

	});

	return self;

})();

(function(){

var cssRule = function(selectors, rules, style) {

	$.extend(this, {
		id       : $.uid(),
		style    : style,
		selectors: [],
		preRule  : "",
		rules    : {},
		legacy   : $.IE===8,
		important: false
	});

	// If selector is given, automatically add rule.
	// Else assume caller wants a blank rule object.
	if (selectors) {
		this.set(selectors, rules);
	}
}

$.extend(cssRule.prototype, {

	set: function(selectors, rules) {

		// Normalize selectors into array
		if ($.isString(selectors)) {
			this.selectors = selectors.split(",");
		} else {
			this.selectors = selectors;
		}

		// Normalize rules
		if ($.isString(rules)) {
			this.preRule = rules + "\n";
			this.rules = {};
		} else {
			this.preRule = "";
			this.rules = rules || this.rules;
		}

		this.update();

		return this;
	},

	cssText: function() {
		return this.selectors.join(",") + "{" + this.ruleText() + "}\n";
	},

	ruleText: function() {

		var important = this.important;
		return this.preRule +
		       ((this.legacy) ? "-rule-id:" + this.id + ";" : "") +
			   $.map(this.rules, function(val, prop) {
			   		if ($.isNumeric(val) && !$.cssNumber[prop]) val += "px";
			   		if (important) val += " !important";
			   		return prop + ":" + val;
			   }).join(";");
	},

	update: function() {

		if (this.legacy) return this.updateLegacy();

		// Generate css text
		var cssText = this.cssText();

		// If new, insert textnode
		if (this.textNode===undefined) {
			this.textNode = document.createTextNode(cssText);
			this.style.appendChild(this.textNode);

		// Or update existing textnode.
		} else {
			this.textNode.nodeValue = cssText;
		}

		return this;
	},

	updateLegacy: function() {

		this.removeLegacy();

		var stylesheet = $.stylesheet.get(this.style),
			selectors = this.selectors,
			ruleText = this.ruleText(),
			i=0;

		for (;i<selectors.length;i++) {
			stylesheet.addRule(selectors[i], ruleText);
		}

		return this;
	},

	remove: function() {

		if (this.legacy) return this.removeLegacy();

		if (this.textNode!==undefined) {

			// Removing text node is so much quicker
			// than searching for the rule
			this.style.removeChild(this.textNode);

			delete this.textNode;
		}

		return this;
	},

	removeLegacy: function() {

		var stylesheet = $.stylesheet.get(this.style),
			rules = stylesheet.rules,
			i = 0;

		for (;i<rules.length;i++) {

			if (rules[i].cssText.match(this.id)!==null) {
				stylesheet.removeRule(i);
			}
		}

		return this;
	},

	css: function(prop, val) {

		// Getter
		if ($.isString(prop) && val===undefined) {
			return this.rules[prop];
		}

		// Setter
		if ($.isPlainObject(prop)) {
			$.extend(this.rules, prop);
		} else {
			this.rules[prop] = val;
		}

		this.update();

		return this;
	}
});

var self = $.cssRule = function(selector, rules, style) {

	var style = style || self.style || $.stylesheet.nextAvailable(true);

	// If no stylesheet available at this point, stop.
	if (!style) return;

	return new cssRule(selector, rules, style);
};

self.style = $.stylesheet();

})();

(function(){
$.cssUrl = function(url) {
	return 'url("' + encodeURI(url) + '")';
}
})();

});