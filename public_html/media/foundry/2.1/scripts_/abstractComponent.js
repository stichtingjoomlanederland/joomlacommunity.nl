/**
 * abstractComponent
 * Creates an abstraction of the actual component class.
 *
 * Copyright (c) 2012 Jason Ramos
 * www.stackideas.com
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

window.abstractComponent = function() {

	var self = function() {

		self.queue.push({
			method: "run",
			context: this,
			args: arguments
		});
	}

	// Remap to run
	self.run = self;

	// Where the list of function calls are stored.
	self.queue = [];

	self.ready = function() {
		self.queue.push({
			method: "ready",
			context: this,
			args: arguments
		});
	}

	self.module = function() {
		self.queue.push({
			method: "module",
			context: this,
			args: arguments
		});
	}

	self.template = function() {
		self.queue.push({
			method: "template",
			context: this,
			args: arguments
		});
	}

	self.require = function() {

		var chain = [{
			method: "require",
			context: this,
			args: arguments
		}];

		var methods = "library,script,stylesheet,language,template,view,done,always,fail,progress".split(","),
			instance = {};

		var i;
		for(i=0; i<methods.length; i++) {

			var method = methods[i];

			instance[method] = (function(method) {

				return function() {

					chain.push({
						method: method,
						context: this,
						args: arguments
					});

					return instance;
				};

			}(method)); // Creates a closure for the method variable.
		}

		self.queue.push(chain);

		return instance;
	}

	return self;
};
