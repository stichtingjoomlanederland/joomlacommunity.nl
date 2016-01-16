FD31.plugin("module", function($) {

/**
 * jquery.module.
 * An AMD manager built on top of $.Deferred() backbone.
 * An alternative take on RequireJS's define().
 *
 * Part of the jquery.require family.
 * https://github.com/jstonne/jquery.require
 *
 * Copyright (c) 2012 Jensen Tonne
 * www.jstonne.com
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

var Module = function(name) {

	var module = this,
		ready = $.Callbacks("once memory");

	$.extend(this, $.Deferred(), {

		// Name of the module
		name: name,

		// Module status
		// pending, ready, executing, resolved, rejected
		status: "pending",

		// When a module factory is received,
		// this event is fired.
		ready: function(fn) {
			if (fn===true) ready.fire.call(module, $);
			if ($.isFunction(fn)) ready.add(fn);
		}
	});

	// Listen to the events of the module
	// and update the module status as necessary.
	module.then(
		function() {
			module.exports = this;
			module.status  = "resolved";
		},
		function() {
			module.status  = "rejected";
		}
	);

	// Keep a copy of the original done method.
	// This is so that we can track when this done
	// method is being called for the first time,
	// and perform the necessary actions below.
	var done = module.done;

	module.done = function() {

		// Flag this module as required
		// This indicates that we should
		// execute the module factory.
		module.required = true;

		// Execute the module factory
		// if this module has received it
		// and it hasn't been executed yet.
		var factory = module.factory;
		if (factory && module.status==="ready") {
			factory.call(module, $);
		}

		// Replace this first-time done method
		// with the original done method.
		module.done = done;

		// Execute the original done method.
		return module.done.apply(this, arguments);
	}
}

$.module = (function() {

	var self = function(name, factory) {

		var module;

		if (typeof name === "string") {

			module = self.get(name);

			/** Facade #1. Get module.
			 *
			 *  $.module('foobar'); // returns module
             *
		     */
			if (factory === undefined) {
				return module;
			}

			/** Facade #2. Factory assignment.
             *
			 *  $.module('foobar', function() {
			 *
			 *      // This is required in every module factory.
			 *      // Resolve module, return exports.
			 *
			 *      this.resolveWith(exports, [args]);
			 *
		     *  });
             *
		     */

			if ($.isFunction(factory)) {

				// If module is resolved, don't let new factory overwrite it.
				if (module.status=="resolved") return module;

				module.factory = factory;

				module.status = "ready";

				// Indicates that the module factory
				// for this module has been received.
				module.ready("true");

				// If the module is required,
				// execute the module factory.
				if (module.required) {

					module.status = "executing";

					// Execute factory
					factory.call(module, $);
				}

				return module;
			}
		}

		/** Facade #3. Multiple factory assignments / Predefine modules.
		 *	This is used by Foundry compiler when combining multiple script files into one.
         *
		 *  $.module([
	     *
	     *      // Module task object
	     *      {
	     *			name: "module.name"
	     *			factory: function(){}
	     *      }
	     *
	     *      // Module which is loading
	     *      // but factory assignment kicks in later
	     *      "module.name"
		 *	]);
		 *
		 */

		// Predefine modules
		if ($.isArray(name)) {

			var tasks = $.map(name, function(task) {

				var module = self.get($.isString(task) ? task : task.name);

				if (!module) return;

				// If module is pending, set it to ready.
				// This trick require calls into thinking that
				// the script file of this module has been loaded,
				// so it won't go and load the script file again.
				if (module.status === "pending") {
					module.status = "ready";
				}

				if ($.isPlainObject(task)) return task;
			});

			// Run through the list of tasks and assign its factory to the module.
			$.each(tasks, function(i, task) {

				// Assign factory to module
				self(task.name, task.factory);
			});
		}
	}

	// $.module static methods
	$.extend(self, {

		registry: {},

		get: function(name) {
			if (!name) return;

			if ($.isModule(name)) {
				name = name.replace("module://", "");
			}

			return self.registry[name] || self.create(name);
		},

		create: function(name) {
			return self.registry[name] = new Module(name);
		},

		remove: function(name) {
			delete self.registry[name];
		}
	});

	return self;

})();

$.isModule = function(module) {

	if ($.isString(module)) {
		return !!module.match("module://");
	}

	return module && module instanceof Module;
}

});