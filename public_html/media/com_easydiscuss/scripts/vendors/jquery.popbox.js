ed.define('jquery.popbox', ['edjquery'], function($) {

	isAjax = function(namespace) {

		if ($.isString(namespace)) {
			return !!namespace.match("ajax://");
		}

		return false;
	}

	$.fn.popbox = function(options) {

		// Creating or updating popbox options using jquery
		if ($.isPlainObject(options)) {
			this.each(function(){

				var button = $(this);
				var popbox = Popbox.get(button);

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

			var button = $(this[0]);

			// Create new popbox instance if
			// it hasn't been created yet
			var popbox = Popbox.get(button) || new Popbox(button);
			var method = popbox[options];
			var ret;

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

		if (!options) {
			options = {};
		}

		// Popbox button that is placed in a
		// fixed position needs special handling.
		button
			.parentsUntil("body")
			.addBack()
			.each(function(){
				var parent = $(this);

				if (parent.css("position")==="fixed") {
					options.fixed = true;
					return false;
				}
			});

		// Gather element options
		var elementOptions = {};

		// Takes content from data-popbox attribute, else take it from inline content.
		var content = button.attr("data-ed-popbox") || button.find("[data-ed-popbox-content]").html() || $(button.attr("data-ed-popbox-target")).html();

		if (content) {
			elementOptions.content = content;
		}

		$(["id", "component", "type", "toggle", "position", "collision", "cache"])
			.each(function(i, key){

				var attribute = "data-ed-popbox-" + key;
				var val = button.attr(attribute);

				if (key == "cache" && val == 0) {
					val = false;
				}

				elementOptions[key] = val;
			});

		// Quick Hack
		if (button.attr("data-ed-popbox-offset")!==undefined) {
			elementOptions["offset"] = parseInt(button.attr("data-ed-popbox-offset"));
		}

		// If popbox was set up via jQuery, the element may not
		// have the data-popbox attribute. We need this attribute
		// for click and hover events to work (and keep things DRY).
		if (content === undefined) {
			button.attr("data-ed-popbox", "");
		}

		// Build final options
		popbox.update(
			$.extend(true,
				{},
				Popbox.defaultOptions, {
					tooltip: $(),
					loader: $('<div id="ed" class="popbox loading" data-ed-popbox-tooltip><div class="arrow"></div></div>'),
					uid: $.uid(),
					button: button
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
		toggle: "click",
		position: "bottom",
		collision: "flip",
		cache: true,
		fixed: false,
		offset: 10,
		namespace: ""
	};

	Popbox.get = function(el) {
		var popbox = $(el).data('popbox');

		if (popbox instanceof Popbox) {
			return popbox;
		}
	}

	$.extend(Popbox.prototype, {

		positions: "top top-left top-right top-center bottom bottom-left bottom-right bottom-center left left-top left-bottom left-center right right-top right-bottom right-center",

		update: function(options) {

			var popbox = this;

			// Update popbox options
			$.extend(true, popbox, options);

			// When content uses the ajax:// protocol, we know they want to perform an ajax call
			if (isAjax(popbox.content)) {
				popbox.wait = true;

				// Since we know that the content will be a namespace, map it on the namespace property first.
				popbox.namespace = popbox.content.replace('ajax://', '');

				var ajaxArgs = {};

				// Get all arguments for the ajax requests
				popbox.button.each(function() {
					var attributes = this.attributes;

					$.each(attributes, function() {

						if (this.specified) {
							if (this.name.indexOf('data-args-') !== -1) {

								// If the argument starts with data-args-xxx then we add it into the list of arguments
								// e.g. data-args-id="10"
								//

								// Replace key from data-args-id with id

								var pkey = this.name.replace('data-args-', '');
								ajaxArgs[pkey] = this.value;
							}
						}
					});

				});

				var callback = function(namespace) {
								return {
									content: EasyDiscuss.ajax(namespace, ajaxArgs)
								};
							};

				popbox.update({
					"content": callback
				});

				popbox.wait = false;

				return;
			}

			// When the content is a string, we know that we should perform the ajax calls to get content
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
							left      = coords.left,
							offset    = popbox.offset,
							buttonOffset = popbox.button.offset();

						switch (pos[0]) {

							case "top":
							case "bottom":
								var vertical = feedback.vertical;
								if (vertical==pos[0]) {
									classname = classname.replace(/top|bottom/gi, (vertical=="top") ? "bottom" : "top");
								}
								top = (vertical=="top") ? top + offset : top - offset;

								if (pos[1]=="left" && (left < Math.floor(buttonOffset.left))) {
									classname = classname.replace(/left|right/gi, (pos[1]=="left") ? "right" : "left");
								}
								break;

							case "left":
							case "right":
								var horizontal = feedback.horizontal;
								if (feedback.horizontal==pos[0]) {
									classname = classname.replace(/left|right/gi, (feedback.horizontal=="left") ? "right" : "left");
								}
								left = (horizontal=="left") ? left + offset : left - offset;
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
					"data-ed-popbox-tooltip": popbox.type,
					"style": popbox.fixed ? 'position: fixed' : ''
				})
				.addClass(popbox.component)
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
			if (popbox.wait) {
				return;
			}

			// Stop any task that hides popover
			clearTimeout(popbox.hideTimer);

			// If this popbox can only be shown exclusively,
			// then hide other popbox.
			if (popbox.exclusive) {

				$("[data-ed-popbox-tooltip]").each(function(){

					var popbox = Popbox.get($(this));

					if (!popbox) return;

					popbox.hide();
				});
			}

			// Insert active for button
			popbox.button.addClass("is-active");

			// Hide when popbox is blurred
			if (popbox.toggle == "click") {

				var doc = $(document);
				var hideOnClick = Popbox.toggleEvent + ".popbox." + popbox.uid;

				doc.off(hideOnClick)
					.on(hideOnClick, function(event){

						// Collect list of bubbled elements
						var targets = $(event.target).parents().andSelf();

						// Don't hide popbox is popbox button or tooltip is one of those elements.
						if (targets.filter(popbox.button).length  > 0 || targets.filter(popbox.tooltip).length > 0) {
							return;
						}

						// Unbind hiding
						doc.off(hideOnClick);

						popbox.hide();
					});
			}

			// Reposition popbox when browser resized or zoomed
			var win = $(window);
			var repositionOnResize = "resize.popbox" + popbox.uid;

			win
				.off(repositionOnResize)
				.on(repositionOnResize, function(){

					// Reposition popbox
					if (popbox.tooltip.length > 0) {
						popbox.tooltip
							.position(popbox.position);
					}
				});

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
				var options = popbox.content(popbox.namespace);

				// Update popbox with the new options
				popbox.update(options);

				// If updating popbox causes it to fall into wait mode, stop.
				if (popbox.wait) {
					return;
				}
			}

			// If at this point, popbox is not a deferred object,
			// then we don't have any tooltip to show.
			if (!$.isDeferred(popbox.content)) {
				return;
			}

			// If the popbox content is still loading,
			// show loading indicator.
			if (popbox.content.state()=="pending") {

				popbox.loader
					.appendTo("body")
					.position(popbox.position);

				// Trigger popboxLoading event
				popbox.trigger("popboxLoading", [popbox]);
			}

			popbox.content
				.always(function(){

					popbox.wait = false;
				})
				.done(function(html){

					// If popbox already has a tooltip, stop.
					if (popbox.tooltip.length > 0) {
						return;
					}

					// If popbox is disabled, don't show it.
					if (!popbox.enabled) {
						return;
					}

					// Remove loading indicator
					popbox.loader.detach();

					// Build the tooltip
					var tooltip = $(html);

					if (tooltip.filter("[data-ed-popbox-tooltip]").length < 1) {

						var content = tooltip;

						tooltip =
							// Create wrapper and
							$('<div id="ed" class="popbox" data-ed-popbox-tooltip><div class="arrow"></div><div class="popbox-content" data-ed-popbox-content></div></div>')
								.attr({
									"data-ed-popbox-tooltip": popbox.type,
									"style": popbox.fixed ? 'position: fixed' : ''
								})
								.addClass(popbox.component)
								.addClass("popbox-" + popbox.type)
								.appendTo("body");

						// We want any possible scripts within the tooltip
						// content to execute when it is visible in DOM.
						tooltip
							.find('[data-ed-popbox-content]')
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
								.filter("[data-ed-popbox-tooltip]");
					}

					// Store tooltip property in popbox
					// Let tooltip has a reference back to popbox
					popbox.tooltip = tooltip
						.data("popbox", popbox)
						.position(popbox.position);

					// Find any labels on the popbox and we need to prevent it from doing any bubbling up
					popbox
						.tooltip
						.find('label')
						.on('click', function(event) {
							event.preventDefault();
							event.stopPropagation();

							// Find the target
							var target = popbox.tooltip.find('#' + $(this).attr('for'));

							if (target.length > 0) {
								if (target.is(':checkbox')) {
									target.click();
								} else {
									target.focus();
								}
							}

							return;

						});
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

			var hide = function() {

				if (popbox.locked && !force) {
					return;
				}

				// Detach tooltip
				popbox.tooltip
					.detach();

				// Detach repositionOnResize
				$(window).off("resize.popbox" + popbox.uid);

				// Trigger popboxDeactivate event
				popbox.trigger("popboxDeactivate", [popbox]);

				if (!popbox.cache) {
					popbox.destroy();
				}
			}

			// Removed active for button
			popbox.button.removeClass("is-active");

			popbox.hideTimer = setTimeout(hide, popbox.hideDelay);
		},

		destroy: function() {
			this.button.removeData("popbox");
		},

		widget: function() {
			return this;
		}
	});

	Popbox.toggleEvent = navigator.userAgent.match(/iPhone|iPad|iPod/i) ? "touchstart" : "click";

	// Data API
	$(document)
		.on(Popbox.toggleEvent + '.popbox', '[data-ed-popbox]', function(){

			var popbox = $(this).popbox("widget");

			if (popbox.enabled) {
				popbox.hide();
			} else {
				popbox.show();
			}
		})
		.on('mouseover.popbox', '[data-ed-popbox]', function(){

			var popbox = $(this).popbox("widget");

			if (popbox.toggle == "hover" && !(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent))) {
				popbox.show();
			}
		})
		.on('mouseout.popbox', '[data-ed-popbox]', function(){

			var popbox = $(this).popbox("widget");

			if (popbox.toggle=="hover") {
				popbox.hide();
			}
		})
		.on('mouseover.popbox.tooltip', '[data-ed-popbox-tooltip]', function(){

			var popbox = Popbox.get(this);

			if (!popbox) return;

			if (popbox.toggle!=="hover") return;

			// Lock popbox
			popbox.locked = true;

			clearTimeout(popbox.hideTimer);
		})
		.on('mouseout.popbox.tooltip', '[data-ed-popbox-tooltip]', function(){

			var popbox = Popbox.get(this);

			if (!popbox) return;

			if (popbox.toggle!=="hover") return;

			// Unlock popbox
			popbox.locked = false;

			// Hide popbox
			popbox.hide();
	 	})
	 	.on('click.popbox.close', '[data-ed-popbox-close]', function(){

	 		var popbox = Popbox.get($(this).parents('[data-ed-popbox-tooltip]'));

	 		if (!popbox) {
	 			return;
	 		}

	 		popbox.hide();
	 	});

	return $;
});
