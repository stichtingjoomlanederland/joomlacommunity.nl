(function(){

// module factory: start

var moduleFactory = function($) {
// module body: start

var module = this; 
$.require() 
 .stylesheet("ajaxPopover/default") 
 .done(function() { 
var exports = function() { 

$.fn.ajaxPopover = function(popoverOptions) {
	var elements = this;

	var defaultOptions = {
			trigger: 'click',
			beforeAjax: '<div class="ajaxPopover loading"></div>'
		},
		options = $.extend({}, defaultOptions, popoverOptions);

	$.each(elements, function(i, element) {

		element = $(element);

		if(typeof popoverOptions === 'string') {
			if(popoverOptions === 'toggle') {
				if(element.data('popover') === undefined) {
					popoverOptions = 'show';
				} else {
					popoverOptions = element.data('popover').$tip.hasClass('in') ? 'hide' : 'show';
				}
			}

			element
				.popover(popoverOptions)
				.trigger('ajaxPopover' + $.String.capitalize(popoverOptions));
			return;
		}

		var originalContent = options.content || element.data('content') || '';

		options.content = options.beforeAjax;

		if(options.ajax === undefined && element.data('ajax') !== undefined) {
			options.ajax = function() {
				return eval(element.data('ajax'));
			}
		}

		if(options.ajax !== undefined) {
			var trigger = options.trigger === 'manual' ? 'ajaxPopoverShow' : options.trigger;

			element.one(trigger, function() {
				var task = options.ajax;

				if($.isFunction(task)) {
					task = options.ajax();
				}

				if(typeof task === 'string') {
					options.content = task;
					rePopover(element, options);
				}

				if($.isDeferred(task)) {
					task
						.done(function(html) {
							options.content = html;
							rePopover(element, options);
						})
						.fail(function(html) {
							options.content = (typeof html !== 'string' || html === '') ? '<div class="ajaxPopover error">Failed loading.</div>' : html;
							rePopover(element, options);
						});
				}
			});
		}

		element.popover(options);

	});

	return elements;
};

var rePopover = function(element, options) {
	$(element)
		.popover('destroy')
		.data('popover', null)
		.popover(options)
		.popover('show');
}

}; 

exports(); 
module.resolveWith(exports); 

}); 
// module body: end

}; 
// module factory: end

dispatch("ajaxPopover")
.containing(moduleFactory)
.to("Foundry/2.1 Modules");

}());