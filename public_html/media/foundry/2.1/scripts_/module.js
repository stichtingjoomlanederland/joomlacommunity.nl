dispatch.to("Foundry/2.1 Core Plugins").at(function($, manifest) {

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

				module.factory = factory;

				module.status = "loading";

				// Execute factory
				factory.call(module, $);

				return module;
			}
		}

		/** Facade #3. Multiple factory assignments.
		 *	This is used by $.require builder when combining multiple script files into one.
         *
		 *  $.module([
		 *		function(){},
		 *		['foobar', function(){}]
		 *	]);
		 */

		var _this = this;
		if ($.isArray(name)) {

			// Build a list of defined-but-unresolved modules,
			// if the module hasn't been created yet.
			//
			// Defined-but-unresolved modules will trick $.require calls made within the
			// module factory into thinking that the script file of the required module
			// has already been loaded.
			//
			// This prevents $.require from making additional http request to fetch the
			// module from the server.

			var tasks = $.map(name, function(task) {

				if ($.isFunction(task)) {
					return task;
				}

				if ($.isPlainObject(task)) {

					var module = self.get(task.name);

					if (module===undefined) {

						return null;
					}

					if (module.status === "pending") {

						module.status = "loading";
					}

					task.module = module;

					return task;
				}
			});

			// Run through the list of tasks and assign its factory to the module.
			$.each(tasks, function(i, task) {

				// If factory is function, treat it as codes that needs to be executed
				// before the execution of the module factory.
				if ($.isFunction(task)) {

					task(); return;
				}

				// Assign factory to module
				if ($.isPlainObject(task)) {

					self.apply(_this, [task.name, task.factory]);
				}

			});
		}
	}

	// $.module static methods
	$.extend(self, {

		registry: {},

		get: function(name) {
			if (name===undefined) return;
			return self.registry[name] || self.create(name);
		},

		create: function(name) {
			var module = $.Deferred();

			$.extend(module, {
				name: name,
				type: "module",
				status: "pending"
			});

			module.done(function() {
				module.exports = this;
				module.status = "ready";
			});

			return self.registry[name] = module;
		},

		remove: function(name) {
			delete self.registry[name];
		}
	});

	return self;

})();

dispatch
	.to("Foundry/2.1 Modules")
	.at(function(exports, manifest) {
		$.module(manifest.name, exports);
	});

}); // dispatch: end