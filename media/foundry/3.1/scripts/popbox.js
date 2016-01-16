(function(){

// module factory: start

var moduleFactory = function($) {
// module body: start

var module = this; 
$.require() 
 .script("ui/position") 
 .done(function() { 
var exports = function() { 

/*
<div
	data-popbox="module://popbox/options/exporter"
	data-popbox-class="profile"
	data-popbox-position="bottom-left"></div>

<div class="popbox" data-popbox-tooltip>
<div class="arrow"></div>
<div class="popbox-content">
</div>
</div>
*/

$.fn.popbox = function(options) {

	// Creating or updating popbox options
	if ($.isPlainObject(options)) {

		this.each(function(){

			var button = $(this),
				popbox = Popbox.get(button);

			// Update popbox options
			if (popbox) {
				popbox.update(options);

			// Or create a new popbox
			} else {

				popbox = new Popbox(button, options);
			}
		});

		return this;
	}

	// Calling a method in popbox
	if ($.isString(options)) {

		var button = $(this[0]),

			// Create new popbox instance if
			// it hasn't been created yet
			popbox = Popbox.get(button) || new Popbox(button),

			method = popbox[options],

			ret;

		if ($.isFunction(method)) {

			ret = method.apply(popbox, $.makeArray(arguments).slice(1));
		}

		return ret || this;
	}

	return this;
}

var Popbox = function(button, options) {

	var popbox = this;

	// Store popbox instance within button
	button.data("popbox", popbox);

	// Normalize arguments
	if ($.isString(options)) {
		options = {content: options}
	}

	// Gather element options
	var elementOptions = {},
		content   = button.attr("data-popbox"),
		toggle    = button.attr("data-popbox-toggle"),
		position  = button.attr("data-popbox-position"),
		collision = button.attr("data-popbox-collision"),
		type      = button.attr("data-popbox-type"),
		id        = button.attr("data-popbox-id");

	if (content)   elementOptions.content   = content;
	if (toggle)    elementOptions.toggle    = toggle;
	if (type)      elementOptions.type      = type;
	if (position)  elementOptions.position  = position;
	if (collision) elementOptions.collision = collision;
	if (id)        elementOptions.id        = id;

	// If popbox was set up via jQuery, the element may not
	// have the data-popbox attribute. We need this attribute
	// for click and hover events to work (and keep things DRY).
	if (content===undefined) button.attr("data-popbox", "");

	// Build final options
	popbox.update(
		$.extend(true,
			{},
			Popbox.defaultOptions,
			{
				tooltip: $(),
				loader : $('<div class="popbox loading" data-popbox-tooltip><div class="arrow"></div></div>'),
				uid    : $.uid(),
				button : button
			},
			elementOptions,
			options
		)
	);
};

// Default options
Popbox.defaultOptions = {
	content: "",
	id: null,
	type: "",
	enabled: false,
	wait: false,
	locked: false,
	exclusive: false,
	hideTimer: null,
	hideDelay: 50,
	toggle: "hover",
	position: "bottom",
	collision: "flip",
	cache: true
};

Popbox.get = function(el) {

	var popbox = $(el).data("popbox");

	if (popbox instanceof Popbox) return popbox;
}

Popbox.toggleEvent = navigator.userAgent.match(/iPhone|iPad|iPod/i) ? "touchstart" : "click";

$.extend(Popbox.prototype, {

	positions: "top top-left top-right top-center bottom bottom-left bottom-right bottom-center left left-top left-bottom left-center right right-top right-bottom right-center",

	update: function(options) {

		var popbox = this;

		// Update popbox options
		$.extend(true, popbox, options);

		// If popbox content is a module
		if ($.isModule(popbox.content)) {

			// Don't let anything happen until module is resolved.
			popbox.wait = true;

			$.module(popbox.content)
				.done(function(options){

					// Popbox options
					if ($.isPlainObject(options)) {
						popbox.update(options);
					}

					// Callback that returns customized popbox options
					if ($.isFunction(options)) {

						popbox.update({
							content: options
						});
					}
				})
				.fail(function(){

					popbox.update({
						content: "Unable to load tooltip content."
					});
				})
				.always(function(){
					popbox.wait = false;
				});

			return;
		}

		// If popbox content is a string,
		// we'll just rewrap it in deferred.
		if ($.isString(popbox.content)) {
			popbox.content = $.Deferred().resolve(popbox.content);
		}

		var position = popbox.position;

		if ($.isString(position)) {

			// Determine position
			var pos = position.split("-"),
				x1, y1, x2, y2;

			switch (pos[0]) {

				case "top":
				case "bottom":
					x1 = x2 = pos[1] || "center";
					// y1 = pos[0]=="top" ? "bottom-10" : "top+10";
					y1 = pos[0]=="top" ? "bottom" : "top";
					y2 = pos[0]=="top" ? "top"    : "bottom";
					break;

				case "left":
				case "right":
					y1 = y2 = pos[1] || "center";
					// x1 = pos[0]=="left" ? "right-10" : "left+10";
					x1 = pos[0]=="left" ? "right" : "left";
					x2 = pos[0]=="left" ? "left"  : "right";
					break;
			}

			popbox.position = {
				classname: position,
				my: x1 + " " + y1,
				at: x2 + " " + y2,
				using: function(coords, feedback) {

					var tooltip   = $(this),
						classname = popbox.position.classname,
						top       = coords.top,
						left      = coords.left;

					switch (pos[0]) {

						case "top":
						case "bottom":
							var vertical = feedback.vertical;
							if (vertical==pos[0]) {
								classname = classname.replace(/top|bottom/gi, (vertical=="top") ? "bottom" : "top");
							}
							top = (vertical=="top") ? top + 10 : top - 10;
							break;

						case "left":
						case "right":
							var horizontal = feedback.horizontal;
							if (feedback.horizontal==pos[0]) {
								classname = classname.replace(/left|right/gi, (feedback.horizontal=="left") ? "right" : "left");
							}
							left = (horizontal=="left") ? left + 10 : left - 10;
							break;
					}

					tooltip
						.css({
							top : top  + 'px',
							left: left + 'px'
						})
						.removeClass(popbox.positions)
						.addClass(classname);
				}
			};
		}

		$.extend(popbox.position, {
			of: popbox.button,
			collision: popbox.collision
		});

		// Popbox loader
		popbox.loader
			.attr({
				"id": popbox.id,
				"data-popbox-tooltip": popbox.type
			})
			.addClass("popbox-" + popbox.type);

		// If popbox is enabled, show tooltip with new options.
		if (popbox.enabled) {
			popbox.show();
		}
	},

	trigger: function(event, args) {

		var popbox = this;

		this.tooltip.trigger(event, args);
		this.button.trigger(event, args);
	},

	show: function() {

		var popbox = this;

		// Enable popbox
		popbox.enabled = true;

		// If we're waiting for module to resolve, stop.
		if (popbox.wait) return;

		// Stop any task that hides popover
		clearTimeout(popbox.hideTimer);

		// If this popbox can only be shown exclusively,
		// then hide other popbox.
		if (popbox.exclusive) {

			$("[data-popbox-tooltip]").each(function(){

				var popbox = Popbox.get($(this));

				if (!popbox) return;

				popbox.hide();
			});
		}

		// Hide when popbox is blurred
		if (popbox.toggle=="click") {

			var doc = $(document),
				hideOnClick = Popbox.toggleEvent + ".popbox." + popbox.uid;

			doc
				.off(hideOnClick)
				.on(hideOnClick, function(event){

					// Collect list of bubbled elements
					var targets = $(event.target).parents().andSelf();

					// Don't hide popbox is popbox button or tooltip is one of those elements.
					if (targets.filter(popbox.button).length  > 0 ||
						targets.filter(popbox.tooltip).length > 0) return;

					// Unbind hiding
					doc.off(hideOnClick);

					popbox.hide();
				});
		}

		// If tooltip exists, just show tootip
		if (popbox.tooltip.length > 0) {

			popbox.tooltip
				.appendTo("body")
				.position(popbox.position);

			// Trigger popboxActivate event
			popbox.trigger("popboxActivate", [popbox]);

			return;
		}

		// If popbox content is a function,
		if ($.isFunction(popbox.content)) {

			// Execute the function and to get popbox options
			var options = popbox.content(popbox);

			// Update popbox with the new options
			popbox.update(options);

			// If updating popbox causes it to fall into wait mode, stop.
			if (popbox.wait) return;
		}

		// If at this point, popbox is not a deferred object,
		// then we don't have any tooltip to show.
		if (!$.isDeferred(popbox.content)) return;

		// If the popbox content is still loading,
		// show loading indicator.
		if (popbox.content.state()=="pending") {

			popbox.loader
				.appendTo("body")
				.position(popbox.position);
		}

		popbox.content
			.always(function(){

				popbox.wait = false;
			})
			.done(function(html){

				// If popbox already has a tooltip, stop.
				if (popbox.tooltip.length > 0) return;

				// If popbox is disabled, don't show it.
				if (!popbox.enabled) return;

				// Remove loading indicator
				popbox.loader.detach();

				var tooltip = $.buildHTML(html);

				if (tooltip.filter("[data-popbox-tooltip]").length < 1) {

					var content = tooltip;

					tooltip =
						// Create wrapper and
						$('<div class="popbox" data-popbox-tooltip><div class="arrow"></div><div class="popbox-content" data-popbox-content></div></div>')
							.attr({
								"id": popbox.id,
								"data-popbox-tooltip": popbox.type
							})
							.addClass("popbox-" + popbox.type)
							// append to body first because
							.appendTo("body");

					// We want any possible scripts within the tooltip
					// content to execute when it is visible in DOM.
					tooltip
						.find('[data-popbox-content]')
						.append(content);

				} else {

					tooltip =
						// This tooltip might be an array of elements, e.g.
						// tooltip div, scripts and text nodes.
						tooltip
							// we append to body first to
							// let the scripts execute
							.appendTo("body")
							// then filter out the popbox tooltip
							// to assign it back as our variable
							.filter("[data-popbox-tooltip]");
				}

				// Store tooltip property in popbox
				popbox.tooltip =
					tooltip
						// and let tooltip has a reference back to popbox
						.data("popbox", popbox)
						// reposition tooltip
						.position(popbox.position);

				// Trigger popboxActivate event
				popbox.trigger("popboxActivate", [popbox]);
			})
			.fail(function(){

				popbox.update({
					content: "Unable to load tooltip content."
				});
			});
	},

	hide: function(force) {

		var popbox = this;

		// Disable popbox
		popbox.enabled = false;

		// Stop any previous hide timer
		clearTimeout(popbox.hideTimer);

		// Detach popbox loader
		popbox.loader.detach();

		var hide = function(){

			if (popbox.locked && !force) return;

			// Detach tooltip
			popbox.tooltip
				.detach();

			// Trigger popboxDeactivate event
			popbox.trigger("popboxDeactivate", [popbox]);

			if (!popbox.cache) {
				popbox.destroy();
			}
		}

		popbox.hideTimer = setTimeout(hide, popbox.hideDelay);
	},

	destroy: function() {

		this.button.removeData("popbox");
	},

	widget: function() {

		return this;
	}
});

// Data API
$(document)
	.on(Popbox.toggleEvent + '.popbox', '[data-popbox]', function(){

		var popbox = $(this).popbox("widget");

		if (popbox.enabled) {
			popbox.hide();
		} else {
			popbox.show();
		}
	})
	.on('mouseover.popbox', '[data-popbox]', function(){

		var popbox = $(this).popbox("widget");

		if (popbox.toggle=="hover") popbox.show();
	})
	.on('mouseout.popbox', '[data-popbox]', function(){

		var popbox = $(this).popbox("widget");

		if (popbox.toggle=="hover") popbox.hide();
	})
	.on('mouseover.popbox.tooltip', '[data-popbox-tooltip]', function(){

		var popbox = Popbox.get(this);

		if (!popbox) return;

		if (popbox.toggle!=="hover") return;

		// Lock popbox
		popbox.locked = true;

		clearTimeout(popbox.hideTimer);
	})
	.on('mouseout.popbox.tooltip', '[data-popbox-tooltip]', function(){

		var popbox = Popbox.get(this);

		if (!popbox) return;

		if (popbox.toggle!=="hover") return;

		// Unlock popbox
		popbox.locked = false;

		// Hide popbox
		popbox.hide();
 	})
 	.on('click.popbox.close', '[data-popbox-close]', function(){

 		var popbox = Popbox.get($(this).parents('[data-popbox-tooltip]'));

 		if (!popbox) return;

 		popbox.hide();
 	});
}; 

exports(); 
module.resolveWith(exports); 

}); 
// module body: end

}; 
// module factory: end

FD31.module("popbox", moduleFactory);

}());