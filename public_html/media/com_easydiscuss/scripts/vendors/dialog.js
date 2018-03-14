ed.define('dialog', ['edjquery', 'easydiscuss'], function($, EasyDiscuss) {

	var dialogHtml = '<div id="ed" class="ed-dialog"> <div class="ed-dialog-modal"> <div class="ed-dialog-header"> <div class="ed-dialog-header__grid"> <div class="ed-dialog-back-button t-hidden"><i class="fa fa-chevron-left"></i></div> <div class="ed-dialog-header__cell"><span class="ed-dialog-title"></span></div> <div class="ed-dialog-close-button"><i class="fa fa-close"></i></div> </div> </div> <div class="ed-dialog-body"> <div class="ed-dialog-container"> <div class="ed-dialog-content"></div> <div class="o-loader"></div> <div class="o-empty"> <div class="o-empty__content"><i class="o-empty__icon fa fa-exclamation-triangle"></i> <div class="o-empty__text"><span class="ed-dialog-error-message"></span></div> </div> </div> </div> </div> <div class="ed-dialog-footer"> <div class=""> <div class="ed-dialog-footer-content"></div> </div> </div> </div></div>';
	var dialog_ = ".ed-dialog";
	var dialogModal_ = ".ed-dialog-modal";
	var dialogContent_ = ".ed-dialog-content";
	var dialogHeader_ = ".ed-dialog-header";
	var dialogFooter_ = ".ed-dialog-footer";
	var dialogFooterContent_ = ".ed-dialog-footer-content";
	var dialogCloseButton_ = ".ed-dialog-close-button";
	var dialogTitle_ = ".ed-dialog-title";
	var dialogErrorMessage_ = ".ed-dialog-error-message";

	var isFailed = "is-failed";
	var isLoading = "is-loading";
	var rxBraces = /\{|\}/gi;

	var self = EasyDiscuss.dialog = function(options) {

		// For places calling EasyDiscuss.dialog().close();
		if (options===undefined) return self;

		// Normalize options
		if ($.isString(options)) {
			options = {content: options};
		}

		var method = self.open;

		// When dialog is loaded via iframe
		if (window.parentEasyBlogDialog) {
			method = window.parentEasyBlogDialog.open;
		}

		method.apply(self, [options]);

		return self;
	}

	$.extend(self, {

		defaultOptions: {
			title: "",
			content: "",
			buttons: "",
			classname: "",
			width: "auto",
			height: "auto",
			escapeKey: true
		},

		open: function(options) {

			// Get dialog
			var dialog = $(dialog_);
			if (dialog.length < 1) {
				dialog = $(dialogHtml).appendTo("body");
			}

			// Normalize options
			var options = $.extend({}, self.defaultOptions, options);

			// Set title
			var dialogTitle = $(dialogTitle_);
			dialogTitle.text(options.title);

			// Set buttons
			var dialogFooterContent = $(dialogFooterContent_);
			dialogFooterContent.html(options.buttons);
			dialog.toggleClass("has-footer", !!options.buttons)

			// Set bindings
			self.setBindings(options);

			// Set content
			var dialogContent = $(dialogContent_).empty();
			var content = options.content;
			var contentType = self.getContentType(content);
			dialog.switchClass("type-" + contentType)

			// Set width & height
			var dialogModal = $(dialogModal_);
			var dialogWidth = options.width;
			var dialogHeight = options.height;

			if ($.isNumeric(dialogHeight)) {
				var dialogHeader = $(dialogHeader_);
				var dialogFooter = $(dialogFooter_);
				dialogHeight += dialogHeader.height() + dialogFooter.height();
			}

			dialogModal.css({
				width: dialogWidth,
				height: dialogHeight
			});

			dialog.addClassAfter("active");

			// HTML
			switch (contentType) {

				case "html":
					dialogContent.html(content);
					break;

				case "iframe":
					var iframe = $("<iframe>");
					var iframeUrl = content;
					iframe
						.appendTo(dialogContent)
						.one("load", function(){
							// Expose dialog object to iframe
							// Inside a try catch because does not work on cross-site domain,
							// and url checking takes a lot more code to write.
							try { iframe[0].contentWindow.parentEasyBlogDialog = self; } catch(err) {};
						})
						.attr("src", iframeUrl);
					break;

				case "deferred":
					dialog.switchClass(isLoading);
					content
						.done(function(content) {

							// Options
							if ($.isPlainObject(content)) {
								self.reopen($.extend(true, options, content));
							// Content
							} else if ($.isString(content)) {
								options.content = content;
								self.reopen(options);
							// Unknown
							} else {
								dialog.switchClass(isFailed);
							}
						})
						.fail(function(exception){

							dialog.switchClass(isFailed);

							var dialogErrorMessage = $(dialogErrorMessage_);

							// Error message
							if ($.isString(exception)) {
								dialogErrorMessage.html(exception);
							}

							// Exception object
							if ($.isPlainObject(exception) && exception.message) {
								dialogErrorMessage.html(exception.message);
							}
						});
					return;
					break;

				case "dialog":
					var xmlOptions = self.parseXMLOptions(content);
					self.open($.extend(true, options, xmlOptions));
					return;
					break;
			}
		},

		reopen: function(options) {
			self.close();
			self.open(options);
		},

		close: function() {

			// Unset bindings
			self.unsetBindings();

			// Remove dialog
			var dialog = $(dialog_);
			dialog.remove();
		},

		getContentType: function(content) {

			if (/<dialog>(.*?)/.test(content)) {
				return "dialog";
			}

			if ($.isUrl(content)) {
				return "iframe";
			}

			if ($.isDeferred(content)) {
				return "deferred";
			}

			return "html";
		},

		parseXMLOptions: function(xml) {

			var xmlOptions = $.buildHTML(xml);
			var newOptions = {};

			$.each(xmlOptions.children(), function(i, node){

				var node = $(node);
				var key  = $.String.camelize(this.nodeName.toLowerCase());
				var val  = node.html();
				var type = node.attr("type");

				switch (type) {
					case "json":
						try {
							val = $.parseJSON(val);
						} catch(e) {};
						break;

					case "javascript":
						try {
							val = eval('(function($){ return ' + $.trim(val) + ' })(' + $.globalNamespace + ')');
						} catch(e) {};
						break;

					case "text":
						val = node.text();
						break;
				}

				// Automatically convert numerical values
				if ($.isNumeric(val)) val = parseFloat(val);

				newOptions[key] = val;
			});

			return newOptions;
		},

		bindings: {},

		setBindings: function(options) {

			// Remove previous bindings
			self.unsetBindings();

			// Create new bindings
			var selectors = options.selectors;
			var bindings  = options.bindings;

			if (selectors && bindings) {

				// Simulate a controller instance
				var controller = {parent: self};
				var dialog = $(dialog_);

				$.each(selectors, function(element, selector){

					var element = element.replace(rxBraces, "");

					// Create selector fn
					var selectorFn = controller[element] = function() {
						return dialog.find(selector);
					};
					selectorFn.selector = selector;
				});

				$.each(bindings, function(binder, eventHandler){

					// Get element and event name
					var parts = binder.split(" ");
					var element = parts[0].replace(rxBraces, "");
					var eventName = parts[1] + ".ed.dialog";

					// Get selector fn
					var selectorFn = controller[element];

					// No binding if selector fn is not found
					if (!selectorFn) return;

					// Bind event handler
					var selector = selectorFn.selector;
					dialog.on(eventName, selector, function(){
						eventHandler.apply(controller, [this].concat(arguments));
					});

					// Add to bindings
					self.bindings[eventName] = eventHandler;
				});
			}

			if (options.escapeKey) {
				$(document).on("keydown.ed.dialog", function(event){
					if (event.keyCode==27) {
						self.close();
					}
				});
			}
		},

		unsetBindings: function() {

			// Get dialog
			var dialog = $(dialog_);

			// Unbind bindings
			$.each(self.bindings, function(eventName, eventHandler){
				dialog.off(eventName);
			});

			// Unbind escape
			$(document).off("keydown.ed.dialog");
		}
	});

	$(document)
		.on("click", dialogCloseButton_, function(){
			self.close();
		})
		.on("click", dialog_, function(event){
			var dialog = $(dialog_);
			if (event.target==dialog[0]) {
				self.close();
			}
		});

});