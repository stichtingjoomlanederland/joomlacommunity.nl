FD31.plugin("server", function($) {

/*!
 * jquery.server.
 * Extension of jquery.ajax with ability to parse server commands.
 *
 * Copyright (c) 2011 Jason Ramos
 * www.stackideas.com
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */
var self = $.server = function(options) {

	var request = $.Deferred(),

		ajaxOptions = $.extend(true, {}, self.defaultOptions, options, {success: function(){}});

		request.xhr = $.Ajax(ajaxOptions)

			.done(function(commands){

				if (typeof commands==="string") {
					try {
						commands = $.parseJSON(commands);
					} catch(e) {
						request.rejectWith(request, ["Unable to parse Ajax commands.", "error"])
					}
				}
				
				if (!$.isArray(commands)) {

					request.rejectWith(request, ["Invalid ajax commands.", "error"]);

				} else {

					var parse = function(command){
						var type = command.type,
							parser = self.parsers[type] || options[type];

						if ($.isFunction(parser)) {
							return parser.apply(request, command.data);
						}
					}

					// Execute all the notifications first
					var commands = $.map(commands, function(command) {
						if (command.type=="notify") {
							parse(command);
						} else {
							return command;
						}
					})

					$.each(commands, function(i, command) {
						parse(command);
					});
				}

				// If server did not resolve this request
				if (request.state()==="pending") {

					// We'll resolve it ourselves
					request.resolveWith(request);
				}

			})

			.fail(function(xhr, status, statusText){

				request.rejectWith(request, [statusText, status]);
			});

	return request;
};

self.defaultOptions = {
	type: 'POST',
	data: {
		tmpl: 'component',
		format: 'ajax',
		no_html: 1
	},
	dataType: 'json'
};

self.parsers = {

	script: function() {

		var data = $.makeArray(arguments);

		// For hardcoded javascript codes
		if (typeof data[0] == 'string') {
			try { eval(data[0]) } catch(err) {};
			return;
		}

		/**
		* Execute each method and assign returned object back to the chain.
		*
		* Foundry().attr('checked', true);
		* 	is equivalent to:
		* window['Foundry']('.element')[attr]('checked', true);
		*/
		var chain = window, chainBroken = false;

		$.each(data, function(i, chainer)
		{
			if (chainer.property==="Foundry") {
				chainer.property = $.globalNamespace;
			}

			if (chainer.method==="Foundry") {
				chainer.method = $.globalNamespace;
			}

			try {
				switch(chainer.type)
				{
					case 'get':
						chain = chain[chainer.property];
						break;

					case 'set':
						chain[chainer.property] = chainer.value;
						chainBroken=true;
						break;

					case 'call':
						chain = chain[chainer.method].apply(chain, chainer.args);
						break;
				}
			} catch(err) {
				chainBroken = true;
			}
		})
	},

	resolve: function() {

		this.resolveWith(this, arguments);
	},

	reject: function() {

		this.rejectWith(this, arguments);
	},

	notify: function() {

		this.notifyWith(this, arguments);
	},

	redirect: function(url) {

		window.location = url;
	}
};

});