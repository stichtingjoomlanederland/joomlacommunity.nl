(function(){

// module factory: start

var moduleFactory = function($) {
// module body: start

var module = this; 
var exports = function() { 

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
					} catch(e) {}
				}

				if (!$.isArray(commands)) {

					request.rejectWith(request, ["Invalid server response."]);

				} else {

					$.each(commands, function(i, command)
					{
						var type = command.type,
							parser = self.parsers[type] || options[type];

						if ($.isFunction(parser)) {

							parser.apply(request, command.data);
						}
					});
				}

				// If server did not resolve this request
				if (request.state()==="pending") {

					// We'll resolve it ourselves
					request.resolveWith(request);
				}

			})

			.fail(function(xhr, status, response){

				response = response || ["Error retrieving data from server."];

				request.rejectWith(request, response);
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
		});
	},

	resolve: function() {

		this.resolveWith(this, arguments);
	},

	reject: function(args) {

		this.rejectWith(this, arguments);
	}
};

}; 

exports(); 
module.resolveWith(exports); 

// module body: end

}; 
// module factory: end

dispatch("server")
.containing(moduleFactory)
.to("Foundry/2.1 Modules");

}());