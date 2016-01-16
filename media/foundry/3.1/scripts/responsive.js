FD31.plugin("responsive", function($) {

// $(selector).responsive({condition});
// $(selector).responsive([{condition1}, {condition2}]);

/*var defaultOptions = {
	// main element width to calculate
	elementWidth: function() {}, // a function that returns pixel value

	// array of conditions of ascending thresholdWidth
	conditions: [{

		// threshold for this condition
		at: 0,// threshold value

		// condition specific options
		switchTo: '',// classname to apply to the node
		alsoSwitch: {
			'selector': 'class'
		}, //  objects with element and class
		switchStylesheet: '',
		whenApplied: '', // function to run
		whenRemoved: '' // reverse function that reverses any action in target function
	}]
}*/

$.responsive = function(elem, options) {
	
	return new Responsive(elem, options);
};

$.fn.responsive = function(conditions) {

	if (conditions) {
		
		var elem = this,
			options = {
				elementWidth: function(elem) {
					return elem.outerWidth(true);
				},
				conditions: conditions
			};

		$.responsive(elem, options);
	}

	return this;
};

var $window = $(window),
	$isFunc = $.isFunction;

var Responsive = function(elem, options) {

	var self = this,
		elem = $(elem),
		instance = elem.data("$responsive");

	// If there is an existing instance, kill it.
	if (instance) instance.destroy();

	// Construct instance
	$.extend(self, {
		// Accept node, selectors, jQuery elements.
		elem      : elem,
		options   : options,
		conditions: $.sortBy($.makeArray(options.conditions), function(condition){ return condition.at; }),
		event     : "resize.responsive" + $.uid(),
		handler   : $.debounce(function(){ self.set(); }, 250)
	});

	// Delete conditions prop from options
	delete options.conditions;

	// Store instance within element
	elem.data("$responsive", self)
	
	// Wait until document is ready before
	// applying responsive events
	$(function(){

		// Attach resize handler to window
		$window.on(self.event, self.handler);

		// Set conditions
		self.set();

	});

	// Set conditions once again
	// on window load event.
	$(window).load(function(){

		self.set();
	});
}

$.extend(Responsive.prototype, {

	set: function() {

		var self = this,
			elementWidth = self.options.elementWidth;

		self.elem.each(function(){

			var elem = $(this),
				currentWidth = ($isFunc(elementWidth)) ? elementWidth(elem) : elementWidth;

			// Remove current condition
			self.removeCondition(elem.data("currentCondition"), elem);

			// Analyze all conditions
			$.each(self.conditions, function(i, condition) {

				var thresholdWidth = condition.at;

				if (currentWidth <= thresholdWidth) {
					self.applyCondition(condition, elem);
					return false;
				}
			});
		});
	},

	applyCondition: function(condition, elem) {

		var switchTo, alsoSwitch, switchStylesheet, whenApplied;

		// Classnames to remove
		(switchTo = condition.switchTo) &&
			elem.addClass(switchTo);

		// Classnames to remove on other elements
		(alsoSwitch = condition.alsoSwitch) &&
			$.each(alsoSwitch, function(selector, classname) {
				$(selector).addClass(classname);
			});

		// Stylesheets to remove
		(switchStylesheet = condition.switchStylesheet) &&
			$.each($.makeArray(switchStylesheet), function(i, url) {
				// Load stylesheet if it hasn't been loaded.
				var stylesheet = $('link[href$="' + url + '"]');
				if (stylesheet.length < 1) {
					$('<link/>')
						.attr({
							rel : 'stylesheet',
							type: 'text/css',
							href: url
						})
						.appendTo('head');
				}
			});

		// Callback to execute when this condition is removed.
		(whenApplied = condition.whenApplied) &&
			$isFunc(whenApplied) && whenApplied();

		elem.data("currentCondition", condition)
			.trigger("responsive", [condition]);
	},

	removeCondition: function(condition, elem) {

		if (!condition) return;

		var switchTo, alsoSwitch, switchStylesheet, whenRemoved;

		// Classnames to remove
		(switchTo = condition.switchTo) &&
			elem.removeClass(switchTo);

		// Classnames to remove on other elements
		(alsoSwitch = condition.alsoSwitch) &&
			$.each(alsoSwitch, function(selector, classname) {
				$(selector).removeClass(classname);
			});

		// Stylesheets to remove
		(switchStylesheet = condition.switchStylesheet) &&
			$.each($.makeArray(switchStylesheet), function(i, url) {
				$('link[href$="' + url + '"]').remove();
			});

		// Callback to execute when this condition is removed.
		(whenRemoved = condition.whenRemoved) &&
			$isFunc(whenRemoved) && whenRemoved();

		elem.removeData("currentCondition");
	},

	resetToDefault: function(current) {

		var self = this,
			elem = self.elem;

		$.each(self.conditions, function(i, condition) {
			if (current && i == current) return;
			self.removeCondition(condition, elem);
		});
	},

	destroy: function() {
		$window.off(this.event);

		var self = this;

		self.elem.each(function(){
			var elem = $(this);
			self.removeCondition(elem.data("currentCondition"), elem);
		});
	}	
});
});