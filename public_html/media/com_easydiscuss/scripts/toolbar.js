EasyDiscuss.module('toolbar', function($) {

  (function() {
	var event = $.event,

		//helper that finds handlers by type and calls back a function, this is basically handle
		// events - the events object
		// types - an array of event types to look for
		// callback(type, handlerFunc, selector) - a callback
		// selector - an optional selector to filter with, if there, matches by selector
		//     if null, matches anything, otherwise, matches with no selector
		findHelper = function(events, types, callback, selector ) {
			var t, type, typeHandlers, all, h, handle,
				namespaces, namespace,
				match;
			for (t = 0; t < types.length; t++) {
				type = types[t];
				all = type.indexOf('.') < 0;
				if (!all) {
					namespaces = type.split('.');
					type = namespaces.shift();
					namespace = new RegExp('(^|\\.)' + namespaces.slice(0).sort().join('\\.(?:.*\\.)?') + '(\\.|$)');
				}
				typeHandlers = (events[type] || []).slice(0);

				for (h = 0; h < typeHandlers.length; h++) {
					handle = typeHandlers[h];

					match = (all || namespace.test(handle.namespace));

					if (match) {
						if (selector) {
							if (handle.selector === selector) {
								callback(type, handle.origHandler || handle.handler);
							}
						} else if (selector === null) {
							callback(type, handle.origHandler || handle.handler, handle.selector);
						}
						else if (!handle.selector) {
							callback(type, handle.origHandler || handle.handler);

						}
					}


				}
			}
		};

	/**
	 * Finds event handlers of a given type on an element.
	 * @param {HTMLElement} el
	 * @param {Array} types an array of event names.
	 * @param {String} [selector] optional selector.
	 * @return {Array} an array of event handlers.
	 */
	event.find = function(el, types, selector ) {
		var events = ($._data(el) || {}).events,
			handlers = [],
			t, liver, live;

		if (!events) {
			return handlers;
		}
		findHelper(events, types, function(type, handler ) {
			handlers.push(handler);
		}, selector);
		return handlers;
	};
	/**
	 * Finds all events.  Group by selector.
	 * @param {HTMLElement} el the element.
	 * @param {Array} types event types.
	 */
	event.findBySelector = function(el, types ) {
		var events = $._data(el).events,
			selectors = {},
			//adds a handler for a given selector and event
			add = function(selector, event, handler ) {
				var select = selectors[selector] || (selectors[selector] = {}),
					events = select[event] || (select[event] = []);
				events.push(handler);
			};

		if (!events) {
			return selectors;
		}
		//first check live:
		/*$.each(events.live || [], function( i, live ) {
			if ( $.inArray(live.origType, types) !== -1 ) {
				add(live.selector, live.origType, live.origHandler || live.handler);
			}
		});*/
		//then check straight binds
		findHelper(events, types, function(type, handler, selector ) {
			add(selector || '', type, handler);
		}, null);

		return selectors;
	};
	event.supportTouch = 'ontouchend' in document;

	$.fn.respondsTo = function(events ) {
		if (!this.length) {
			return false;
		} else {
			//add default ?
			return event.find(this[0], $.isArray(events) ? events : [events]).length > 0;
		}
	};
	$.fn.triggerHandled = function(event, data ) {
		event = (typeof event == 'string' ? $.Event(event) : event);
		this.trigger(event, data);
		return event.handled;
	};
	/**
	 * Only attaches one event handler for all types ...
	 * @param {Array} types llist of types that will delegate here.
	 * @param {Object} startingEvent the first event to start listening to.
	 * @param {Object} onFirst a function to call.
	 */
	event.setupHelper = function(types, startingEvent, onFirst ) {

		if (!onFirst) {
			onFirst = startingEvent;
			startingEvent = null;
		}
		var add = function(handleObj ) {

			var bySelector, selector = handleObj.selector || '';
			if (selector) {
				bySelector = event.find(this, types, selector);
				if (!bySelector.length) {
					$(this).delegate(selector, startingEvent, onFirst);
				}
			}
			else {
				//var bySelector = event.find(this, types, selector);
				if (!event.find(this, types, selector).length) {
					event.add(this, startingEvent, onFirst, {
						selector: selector,
						delegate: this
					});
				}

			}

		};

		var remove = function(handleObj) {
			var bySelector, selector = handleObj.selector || '';
			if (selector) {
				bySelector = event.find(this, types, selector);
				if (!bySelector.length) {
					$(this).undelegate(selector, startingEvent, onFirst);
				}
			}
			else {
				if (!event.find(this, types, selector).length) {
					event.remove(this, startingEvent, onFirst, {
						selector: selector,
						delegate: this
					});
				}
			}
		};

		$.each(types, function() {
			event.special[this] = {
				add: add,
				remove: remove,
				setup: function() {},
				teardown: function() {}
			};
		});
	};

	var supportTouch = 'ontouchend' in document,
		scrollEvent = 'touchmove scroll',
		touchStartEvent = supportTouch ? 'touchstart' : 'mousedown',
		touchStopEvent = supportTouch ? 'touchend' : 'mouseup',
		touchMoveEvent = supportTouch ? 'touchmove' : 'mousemove',
		data = function(event) {
			var d = event.originalEvent.touches ?
				event.originalEvent.touches[0] || event.originalEvent.changedTouches[0] :
				event;
			return {
				time: (new Date).getTime(),
				coords: [d.pageX, d.pageY],
				origin: $(event.target)
			};
		};

	/**
	 * @add jQuery.event.special
	 */
	$.event.setupHelper(['tap'], touchStartEvent, function(ev) {

		//listen to mouseup
		var start = data(ev),
			stop,
			delegate = ev.delegateTarget || ev.currentTarget,
			selector = ev.handleObj.selector,
			entered = this,
			moved = false,
			touching = true,
			timer;


		function upHandler(event) {
			stop = data(event);
			if ((Math.abs(start.coords[0] - stop.coords[0]) < 10) ||
			    (Math.abs(start.coords[1] - stop.coords[1]) < 10)) {
				$.each($.event.find(delegate, ['tap'], selector), function() {
					this.call(entered, ev, {start: start, end: stop});
				});
			}
		};

		timer = setTimeout(function() {
			$(delegate).unbind(touchStopEvent, upHandler);
		}, 500);

		$(delegate).one(touchStopEvent, upHandler);

	});

  })();


	var module = this;

	EasyDiscuss
	.require()
	.done(function($) {

		EasyDiscuss.Controller(

			'Toolbar',
			{
				defaultOptions:
				{

					'{items}'	: '.toolbarItem',
					'{dropdowns}'	: '.dropdown-menu',

					// Notifications
					'{notificationLink}' : '.notificationLink',
					'{notificationDropDown}'	: '.notificationDropDown',
					'{notificationResult}'	: '.notificationResult',
					'{notificationItems}'	: '.notificationItem',
					'{notificationLoader}'	: '.notificationLoader',

					// Messaging
					'{messageLink}' : '.messageLink',
					'{messageDropDown}'	: '.messageDropDown',
					'{messageResult}'	: '.messageResult',
					'{messageLoader}'	: '.messageLoader',
					'{messageItems}'	: '.messageItem',

					// Logout
					'{logoutForm}'	: '#logoutForm',
					'{logoutButton}'	: '.logoutButton',

					// Login
					'{loginLink}'	: '.loginLink',
					'{loginDropDown}'	: '.loginDropDown',

					// Profile
					'{profileLink}'	: '.profileLink',
					'{profileDropDown}'	: '.profileDropDown'

				}
			},

			function(self) { return {

				init: function()
				{
					// Apply responsive layout on the toolbar.
					$.responsive(self.element, {

						elementWidth: function()
						{
							return self.element.outerWidth(true) - 15;
						},
						conditions:
						{
							at: (function() {

								var listWidth = 0;

								self.items().each(function(i , element ) {
									listWidth += $(element).outerWidth(true);
								});

								return listWidth;
							})(),

							alsoSwitch:
							{
								'.dc_toolbar-wrapper'	: 'narrow',
								'.dc-button'	: 'show',
								'#dc_toolbar'	: 'hidden-height'
							}
						}
					});

				},

				'{logoutButton} click' : function()
				{
					self.logoutForm().submit();
				},

				'{loginLink} click' : function()
				{
					self.messageDropDown().hide();
					self.notificationDropDown().hide();

					self.loginDropDown().toggle();
				},

				'{profileLink} tap' : function()
				{
					self.messageDropDown().hide();
					self.notificationDropDown().hide();

					self.profileDropDown().toggle();
				},

				'{messageLink} tap' : function()
				{
					// Hide other drop downs.
					self.profileDropDown().hide();
					self.notificationDropDown().hide();

					// If the current drop down is not active, we need to get the data.
					if (self.messageDropDown().css('display') == 'none')
					{
						var params	= {};

						params[$('.easydiscuss-token').val()]	= 1;

						EasyDiscuss.ajax('site.views.conversation.load', params,
						{
							beforeSend: function()
							{
								// Ensure that the loader is shown all the time.
								self.messageLoader().show();

								// Clear off all notification items first.
								self.messageItems().remove();
							},
							success: function(html)
							{
								// Remove loading indicator.
								self.messageLoader().hide();

								self.messageResult().append(html);
							}
						});
					}

					// Toggle the notification drop down
					self.messageDropDown().toggle();
				},

				'{notificationLink} tap' : function()
				{
					self.messageDropDown().hide();
					self.profileDropDown().hide();

					// If the current drop down is not active, we need to get the data.
					if (self.notificationDropDown().css('display') == 'none')
					{
						var params	= {};

						params[$('.easydiscuss-token').val()]	= 1;

						EasyDiscuss.ajax('site.views.notifications.load', params,
						{
							beforeSend: function()
							{
								// Ensure that the loader is shown all the time.
								self.notificationLoader().show();

								// Clear off all notification items first.
								self.notificationItems().remove();
							},
							success: function(html)
							{
								// Remove loading indicator.
								self.notificationLoader().hide();

								self.notificationResult().append(html);
							}
						});
					}

					// Toggle the notification drop down
					self.notificationDropDown().toggle();

				}

			} }
		);

		EasyDiscuss.Controller(

			'mod_notifications',
			{
				defaultOptions:
				{

					'{items}'	: '.toolbarItem',
					'{dropdowns}'	: '.dropdown-menu',

					// Notifications
					'{notificationLink}' : '.notificationLink',
					'{notificationDropDown}'	: '.notificationDropDown',
					'{notificationResult}'	: '.notificationResult',
					'{notificationItems}'	: '.notificationItem',
					'{notificationLoader}'	: '.notificationLoader',

					// Messaging
					'{messageLink}' : '.messageLink',
					'{messageDropDown}'	: '.messageDropDown',
					'{messageResult}'	: '.messageResult',
					'{messageLoader}'	: '.messageLoader',
					'{messageItems}'	: '.messageItem',

					// Logout
					'{logoutForm}'	: '#logoutForm',
					'{logoutButton}'	: '.logoutButton',

					// Login
					'{loginLink}'	: '.loginLink',
					'{loginDropDown}'	: '.loginDropDown',

					// Profile
					'{profileLink}'	: '.profileLink',
					'{profileDropDown}'	: '.profileDropDown'

				}
			},

			function(self) { return {

				init: function()
				{
					// Apply responsive layout on the toolbar.
					$.responsive(self.element, {

						elementWidth: function()
						{
							return self.element.outerWidth(true) - 15;
						},
						conditions:
						{
							at: (function() {

								var listWidth = 0;

								self.items().each(function(i , element ) {
									listWidth += $(element).outerWidth(true);
								});

								return listWidth;
							})(),

							alsoSwitch:
							{
								'.dc_toolbar-wrapper'	: 'narrow',
								'.dc-button'	: 'show',
								'#dc_toolbar'	: 'hidden-height'
							}
						}
					});

				},

				'{logoutButton} click' : function()
				{
					self.logoutForm().submit();
				},

				'{loginLink} click' : function()
				{
					self.messageDropDown().hide();
					self.notificationDropDown().hide();

					self.loginDropDown().toggle();
				},

				'{profileLink} click' : function()
				{
					self.messageDropDown().hide();
					self.notificationDropDown().hide();

					self.profileDropDown().toggle();
				},

				'{messageLink} click' : function()
				{
					// Hide other drop downs.
					self.profileDropDown().hide();
					self.notificationDropDown().hide();

					// If the current drop down is not active, we need to get the data.
					if (self.messageDropDown().css('display') == 'none')
					{
						var params	= {};

						params[$('.easydiscuss-token').val()]	= 1;

						EasyDiscuss.ajax('site.views.conversation.load', params,
						{
							beforeSend: function()
							{
								// Ensure that the loader is shown all the time.
								self.messageLoader().show();

								// Clear off all notification items first.
								self.messageItems().remove();
							},
							success: function(html)
							{
								// Remove loading indicator.
								self.messageLoader().hide();

								self.messageResult().append(html);
							}
						});
					}

					// Toggle the notification drop down
					self.messageDropDown().toggle();
				},

				'{notificationLink} click' : function()
				{
					self.messageDropDown().hide();
					self.profileDropDown().hide();

					// If the current drop down is not active, we need to get the data.
					if (self.notificationDropDown().css('display') == 'none')
					{
						var params	= {};

						params[$('.easydiscuss-token').val()]	= 1;

						EasyDiscuss.ajax('site.views.notifications.load', params,
						{
							beforeSend: function()
							{
								// Ensure that the loader is shown all the time.
								self.notificationLoader().show();

								// Clear off all notification items first.
								self.notificationItems().remove();
							},
							success: function(html)
							{
								// Remove loading indicator.
								self.notificationLoader().hide();

								self.notificationResult().append(html);
							}
						});
					}

					// Toggle the notification drop down
					self.notificationDropDown().toggle();

				}

			} }
		);


		module.resolve();
	});


});
