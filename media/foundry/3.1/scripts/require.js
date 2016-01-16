FD31.plugin("require", function($) {

/**
 * jquery.require.
 * A dependency loader built on top of $.Deferred() backbone.
 * An alternative take on RequireJS.
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

$.require = (function() {

	// internal function
	var getFolderPath = function(path) {
		return $.uri(path).setAnchor('').setQuery('').toPath('../').toString();
	};

	var self = function(options) {

		var batch = new Batch(options);

		self.batches[batch.id] = batch;

		return batch;
	};

	// Require methods & properties

	$.extend(self, {

		defaultOptions: {

			// Path selection order:
			path: (function() {

				var path =

					$.path + "/scripts/" ||

					// By "require_path" attribute
					$('[require-path]').attr('require-path') ||

					// By last script tag's "src" attribute
					getFolderPath($('script:last').attr('src')) ||

					// By window location
					getFolderPath(window.location.href);

				if (/^(\/|\.)/.test(path)) {
					path = $.uri(window.location.href).toPath(path).toString();
				}

				return path;
			})(),

			timeout: 10000,

			retry: 3,

			verbose: ($.environment=="development")
		},

		setup: function(options) {

			$.extend(self.defaultOptions, options);
		},

		batches: {},

		status: function(filter) {

			$.each(self.batches, function(i, batch){

				var count = {pending: 0, resolved: 0, rejected: 0, ready: 0, total: 0},
					messages = [];

				// Calculate statistics
				$.each(batch.tasks, function(i, task){

					state = (task.module && task.module.status=="ready") ? "ready" : task.state();
					count[state]++;
					count.total++;

					messages.push({
						state: state,
						content: '[' + state + '] ' + task.name 
					});
				});

				var batchName = batch.id + ": " + batch.state() + " [" + count.resolved + "/" + count.total + "]";

				if (filter && count[filter] < 1) return;

				if ($.IE) {

					console.log("$.require.batches[\"" + batch.id + "\"]");
					$.each(messages, function(i, message){
						console.log(message.content);
					});
					console.log("");

				} else {

					// Create log group
					console.groupCollapsed(batchName);

					// Generate list
					console.log("$.require.batches[\"" + batch.id + "\"]", batch);

					$.each(messages, function(i, message){

						var state   = message.state,
							content = message.content;

						if (!filter || state==filter) {
							switch (state) {
								case 'pending' : console.warn(content);  break;
								case 'rejected': console.error(content); break;
								default        : console.info(content);  break;
							}
						}
					});

					console.groupEnd(batchName);
				}
			});

			return "$.require.status(pending|resolved|rejected|ready);";
		},

		loaders: {},

		addLoader: function(name, factory) {

			// Static call, e.g.
			// $.require.script.setup({});
			self[name] = factory;

			// Create proxy functions to require loaders,
			// assigning current batch to factory's "this".
			Batch.prototype[name] = function() {

				var batch = this;

				// Reset auto-finalize timer
				batch.autoFinalize();

				// this == batch
				factory.apply(batch, arguments);

				// Ensure require calls are chainable
				return batch;
			};

			self.loaders[name] = self[name] = factory;
		},

		removeLoader: function(name) {
			delete Batch.prototype[name];
			delete self[name];
		}

	});

	// This serves as batch id counter, it increments
	// whenever a new batch instance is created.
	var id = 0;

	// Batch class.
	// When calling $.require(), it is actually
	// returning an new instance of this class.
	var Batch = function(options) {

		var required = $.Callbacks("once memory"),
		    isRequired = false;

		// We are extending the batch instance
		// with the following properties.
		var batch = $.extend(this, {

			// Unique ID for this batch.
			id: ++id,

			// This array keeps a list of tasks to load.
			tasks: [],

			// Stores options like load path, timeout and retry count. 
			options: $.extend({}, self.defaultOptions, options),

			// Require chain automatically finalizes itself after
			// 300ms if no promise methods were called in the require chain.
			// Set false to disable.
			autoFinalizeDuration: 300,

			// When batch is finalized, further loader calls will be ignored.
			finalized: false,

			// Determine if the contents of the loaded task is required.
			required: function(fn) {
				if (fn===true) isRequired=true && required.fire();
				if ($.isFunction(fn)) required.add(fn);
				return isRequired;
			}
		});

		return batch;
	}

	$.extend(Batch.prototype, {

		addTask: function(task) {

			var batch = this;

			// Don't add invalid tasks.
			// Tasks should be a deferred object.
			if (!$.isDeferred(task)) return;

			// Don't accept anymore tasks if this batch is finalized.
			// Batch is finalized upon calling any of the promises, e.g.
			// done, fail, progress, always, then, pipe
			if (batch.finalized) return;

			// Add this task to the batch's task list
			batch.tasks.push(task);

			// Decorate task with a reference to the current batch
			task.batch = batch;
		},

		autoFinalize: function() {

			var batch = this,
				duration = batch.autoFinalizeDuration;

			// If autoFinalize is disabled, stop.
			if (duration===false) return;

			// Clear previous timer
			clearTimeout(batch.autoFinalizeTimer);

			// Start a new timer
			batch.autoFinalizeTimer = 
				setTimeout(function(){
					batch.finalize();
				}, duration);
		},

		finalize: function() {

			var batch = this;

			// If this batch has been finalized, stop.
			if (batch.finalized) return;

			// Finalize all tasks so no further
			// tasks can be added to this batch.
			batch.finalized = true;

			// Create batch manager which is a
			// master deferred object for all tasks.
			var manager = batch.manager = $.when.apply(null, batch.tasks);

			// Now that tasks are finalized, we can override
			// this batch's pseudo-promise methods with actual
			// promise methods from batch manager.
			var promise  = manager.promise(),
				progress = $.Callbacks();

			$.extend(batch, promise, {

				// Progress & notify method behaves differently.
				// We want progress callback to continue executing
				// even after after manager has been resolved or rejected.
				progress: progress.add,
				notify  : progress.fire,

				// Done method also behaves differently.
				// It will trigger an event notifying all tasks that
				// there is a demand for the content of the task.
				// This is currently used to lazy execute module factories
				// to ensure they don't execute until they are asked for.
				done: function(){

					// Trigger required event
					batch.required(true);

					// After done has been called once, it will be
					// replaced with the actual done method from the
					// master deferred object.
					batch.done = promise.done;

					// And the actual done method gets executed.
					return batch.done.apply(batch, arguments);
				}
			});

			// Flag to indicate whether to make
			// generate debug messages.
			var verbose = batch.options.verbose;

			manager
				.progress(function(state, task){
					if (verbose && state=="rejected") {
						console.warn('Require: Task ' + task.name + ' failed to load.', task);
					}
				})
				.fail(function(){
					if (verbose) {
						console.warn('Require: Batch ' + batch.id + ' failed.', batch);
					}
				});

			// We wrap this in a setTimeout to let existing require chain
			// to continue execute. This ensures that progress call in that
			// require chain receives the activities of each task below.
			setTimeout(function(){

				// Always notify whenever there is an activity on every task.
				$.each(batch.tasks, function(i, task){
					task.then(
						function(){ batch.notify("resolved", task) },
						function(){ batch.notify("rejected", task) },
						function(){ batch.notify("progress", task) }
					);
				});
			}, 1);
		},

		expand: function(args, opts) {

			var args = $.makeArray(args),
				options = opts || {},
				names = [];

	        if ($.isPlainObject(args[0])) {
	            options = $.extend(args[0], opts);
	            names = args.slice(1);
	        } else {
	            names = args;
	        }

	        return {
	        	options: options,
	        	names: names
	        }
		}
	});

	// Masquerade newly created batch instances as a pseudo-promise object
	// until one of those promise's method is called. This is to ensure that
	// no callbacks are fired too early until all require tasks are finalized.
	$.each(["done","fail","progress","always","then"], function(i, method) {

		Batch.prototype[method] = function() {

			var batch = this;

			// Finalize batch
			batch.finalize();

			// Execute method that was originally called
			return batch[method].apply(batch, arguments);
		}
	});

	return self;

})();
/**
 * jquery.require.script
 * Script loader plugin for $.require.
 *
 * Part of jquery.require family.
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

$.require.addLoader('script', (function() {

	// IE & Opera thinks punycoded urls are cross-domain requests,
	// and rejects the ajax request because they think they don't have
	// the necesary transport to facilitate such requests.

	var ajaxHost = $.uri($.indexUrl).host(),
		documentHost = $.uri(document.location.href).host();

	if (ajaxHost!==documentHost && ajaxHost.match("xn--")) {
		$.support.cors = true;
	}

	var canAsync = document.createElement("script").async === true || "MozAppearance" in document.documentElement.style || window.opera;

	var self = function() {

		var batch = this,
			args = $.makeArray(arguments),
			options,
			names;

		// Expand arguments into its actual definition
		if ($.isPlainObject(args[0])) {
			options = args[0];
			names = args.slice(1);
		} else {
			names = args;
		}

		options = $.extend(
			{},
			self.defaultOptions,
			batch.options,
			options,
			{batch: batch}
		);

		// Create tasks and add it to the batch.
		var taskBefore;

		$.each(names, function(i, name) {

			var task = new self.task(name, options, taskBefore);

			batch.addTask(task);

			// Serial script loading
			if (options.serial && taskBefore!==undefined) {

				// Only start current task when the
				// task before is resolved/rejected.
				taskBefore.always(task.start);

			} else {

				task.start();
			}

			taskBefore = task;

		});

	};

	$.extend(self, {

		defaultOptions: {
			// Overrides require path.
			path: '',

			extension: (($.mode=='compressed') ? 'min.js' : 'js'),

			// Serial script loading. Default: Parallel script loading.
			serial: false,

			// Asynchronous script execution. Default: Synchronous script execution.
			async: false,

			// Use XHR to load script. Default: Script injection.
			xhr: false
		},

		setup: function() {

			$.extend(self.defaultOptions, options);
		},

		scripts: {},

		task: function(name, options, taskBefore) {

			var task = $.extend(this, $.Deferred());

			task.name = name;

			task.options = options;

			task.taskBefore = taskBefore;

			// Module assignment or module url override
			if ($.isArray(name)) {

				task.name = name[0] + "@" + name[1];

				task.moduleName = name[0];

				var overrideModuleUrl = name[2];

				// Module assignment
				if (!overrideModuleUrl) {

					// Set module flag
					task.defineModule = true;

					// Raise a warning if the module already exist
					if ($.module.registry[task.moduleName]) {
						console.warn("$.require.script: " + task.moduleName + ' exists! Using existing module instead.');
					}

					// Use XHR for module assignments
					task.options.xhr = true;
				}

				// Assign path to be resolved
				name = name[1];

				task.module = $.module(task.moduleName);
			}

			// Resolve name to paths

			// Absolute path
			if ($.isUrl(name)) {

				task.url = name;

			// Relative path
			} else if (/^(\/|\.)/.test(name)) {

				task.url = $.uri(task.options.path)
							.toPath(name)
							.toString();

			// Module path
			} else {

				task.url = $.uri(task.options.path)
							.toPath('./' + name + '.' + task.options.extension)
							.toString();

				task.module = $.module(name);
			}
		}

	});

	$.extend(self.task.prototype, {

		start: function() {

			var task = this,
				module = task.module;

			// If module has already been loaded,
			// we can skip the whole script loading process.
			if (module && module.status!=="pending") {
				task.waitForModule();
				return;
			}

			// Else load the script that has this module.
			task.load();
		},

		waitForModule: function() {

			var task = this,
				module = task.module;

			// Listen to the events in the module
			// without causing the module factory to execute.
			module.then(
				task.resolve,
				task.reject,
				task.notify
			);

			// When there is demand for this module,
			// we will call the module's done method.
			task.batch.required(function(){

				// This will execute the module factory
				// in case it wasn't executed before.
				module.done(task.resolve);
			});
		},

		load: function() {

			var task = this,
				taskBefore = task.taskBefore,
				options = {};

			// Use previously created script instance if exists,
			// else create a new one.
			task.script = self.scripts[task.url] || (function() {

				var script = (task.options.xhr) ?

					// Load script via ajax.
					$.ajax({

						url: task.url,

						dataType: "text"

					}) :

					// Load script using script injection.
					$.script({

						url: task.url,

						type: "text/javascript",

						async: task.options.async,

						timeout: task.batch.options.timeout,

						retry: task.batch.options.retry,

						verbose: task.batch.options.verbose

					});

				return self.scripts[task.url] = script;

			})();

			// At this point, script may be loaded, BUT may yet
			// to be executed under the following conditions:
			// - Module loaded via script injection/xhr.
			// - Script loaded via via xhr.
			task.script
				.done(function(data) {

					var resolveTask = function() {

						// If task loads a module, resolve/reject task only when
						// the module is resolved/rejected as the module itself
						// may perform additional require tasks.
						if (task.module) {

							task.waitForModule();

						} else {

							task.resolve();
						}
					};

					if (task.options.xhr) {

						if (task.defineModule) {

							// Create our own module factory
							task.module = $.module(task.moduleName, function() {

								var module = this;

								$.globalEval(data);

								module.resolveWith(data);
							});
						};

						// For XHR, if scripts needs to be executed synchronously
						// a.k.a. ordered script execution, then only eval it when
						// the task before it is resolved.
						if (!task.options.async || taskBefore) {

							taskBefore.done(function() {

								$.globalEval(data);

								resolveTask();

							});

							return;
						}

					};

					resolveTask();

				})
				.fail(function() {

					task.reject();
				});
		}
	});

	return self;

})()
);
/**
 * jquery.require.stylesheet
 * Stylesheet loader plugin for $.require.
 *
 * Part of jquery.require family.
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

$.require.addLoader('stylesheet', (function() {

	var self = function() {

		var batch = this,
			args = $.makeArray(arguments),
			options,
			names;

		// Expand arguments into its actual definition
		if ($.isPlainObject(args[0])) {
			options = args[0];
			names = args.slice(1);
		} else {
			names = args;
		}

		options = $.extend(
			{},
			self.defaultOptions,
			batch.options,
			{
				path: $.path + '/styles/'
			},
			options,
			{batch: batch}
		);

		$.each(names, function(i, name) {

			var task = new self.task(name, options),
				existingTask = self.stylesheets[task.url];

			task = existingTask || task;

			batch.addTask(task);

			if (!existingTask) {
				self.stylesheets[task.url] = task;
				task.start();
			}
		});
	};

	$.extend(self, {

		defaultOptions: {
			// Overrides require path.
			path: '',

			extension: (($.mode=='compressed') ? 'min.css' : 'css'),

			// @TODO: XHR loading.
			// Use XHR to load stylesheet. Default: Link injection. @import() for IE.
			xhr: false
		},

		setup: function() {

			$.extend(self.defaultOptions, options);
		},

		stylesheets: {},

		task: function(name, options) {

			var task = $.extend(this, $.Deferred());

			task.name = name;

			task.options = options;

			// Absolute path
			if ($.isUrl(name)) {

				task.url = name;

			// Relative path
			} else if (/^(\/|\.)/.test(name)) {

				task.url = $.uri(task.options.path)
							.toPath(name)
							.toString();

			// Module path
			} else {

				task.url = $.uri(task.options.path)
							.toPath('./' + name + '.' + task.options.extension)
							.toString();
			}

			// Remap task.url to task.options.url
			task.options.url = task.url;
		},

		loaders: {},

		loader: function(name) {

			// Pre-define loaders
			if ($.isArray(name)) {
				return $.map(name, function(name){
					return self.loader(name);
				});
			}

			// Resolve loaders
			if ($.isPlainObject(name)) {
				return $.map(name, function(name, options){
					return self.loader(name).resolve(options);
				});
			}

			// Get loader or create loaders
			var loader = self.loaders[name];

			if (!loader) {
				loader = self.loaders[name] = 
					$.Deferred()
						.done(function(options){
							if ($.isPlainObject(options)) return;
							$.stylesheet(options);
						});
			}

			return loader;
		}		
	});

	$.extend(self.task.prototype, {

		start: function() {

			var task = this;

			var loader = self.loaders[task.name];

			// If this stylesheet hasn't been requested yet
			if (!loader) {

				// Create a stylesheet loader
				loader = self.loader(task.name);

				// Insert the stylesheet
				if ($.stylesheet(task.options)) {
					loader.resolve();
				} else {
					loader.reject();
				}
			}

			loader.then(task.resolve, task.reject);
		}

	});

	return self;

})()
);
/**
 * jquery.require.template
 * Template loader plugin for $.require.
 *
 * Part of jquery.require family.
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

$.require.addLoader('template', (function() {

	var self = function() {

		var batch = this,
			args = $.makeArray(arguments),
			options,
			names;

		// Expand arguments into its actual definition
		if ($.isPlainObject(args[0])) {
			options = args[0];
			names = args.slice(1);
		} else {
			names = args;
		}

		options = $.extend(
			{},
			self.defaultOptions,
			batch.options,
			options,
			{batch: batch}
		);

		$.each(names, function(i, name) {

			var task = new self.task(name, options);

			batch.addTask(task);

			task.start();
		});

	};

	$.extend(self, {

		defaultOptions: {
			// Overrides require path.
			path: '',

			extension: 'htm'
		},

		setup: function() {

			$.extend(self.defaultOptions, options);
		},

		task: function(name, options) {

			var task = $.extend(this, $.Deferred());

			task.name = name;

			task.options = options;

			// Template definition
			if ($.isArray(name)) {

				task.name = name[0];

				// Assign path to be resolved
				name = name[1];
			}

			// Absolute path
			if ($.isUrl(name)) {

				task.url = name;

			// Relative path
			} else if (/^(\/|\.)/.test(name)) {

				task.url = $.uri(task.options.path)
							.toPath(name)
							.toString();

			// Template module
			} else {

				task.url = $.uri(task.options.path)
							.toPath('./' + name + '.' + task.options.extension)
							.toString();
			}
		},

		loaders: {},

		loader: function(name) {

			// Pre-define loaders
			if ($.isArray(name)) {
				return $.map(name, function(name){
					return self.loader(name);
				});
			}

			// Resolve loaders
			if ($.isPlainObject(name)) {
				return $.map(name, function(content, name){
					return self.loader(name).resolve(content);
				});
			}

			// Get loader or create loaders
			var loader = self.loaders[name];

			if (!loader) {
				loader = self.loaders[name] = 
					$.Deferred()
						.done(function(content){
							$.template(name, content);
						});
			}

			return loader;
		}
	});

	$.extend(self.task.prototype, {

		start: function() {

			var task = this;

			// See if there is an existing loader
			var loader = self.loaders[task.name];

			if (!loader) {

				loader = self.loader(task.name);

				loader.xhr = 
					$.Ajax({
							url: task.url,
							dataType: "text"
						})
						.then(loader.resolve, loader.reject)
						.then(task.resolve, task.reject);
			}

			// Keep a reference to the loader in the task
			task.loader = loader;

			return task;
		}
	});

	return self;

})()
);
/**
 * jquery.require.language
 * Language loader plugin for $.require.
 *
 * Part of foundry-module/require family.
 * https://github.com/foundry-modules/require
 *
 * Copyright (c) 2011 Jason Ramos
 * www.stackideas.com
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */


$.require.addLoader('language', (function() {

	var self = function() {

		var batch = this,
			args = $.makeArray(arguments),
			options,
			names;

		// Expand arguments into its actual definition
		if ($.isPlainObject(args[0])) {
			options = args[0];
			names = args.slice(1);
		} else {
			names = args;
		}

		options = $.extend(
			{},
			self.defaultOptions,
			batch.options,
			options,
			{batch: batch}
		);

		var task = new self.task(names, options);

		batch.addTask(task);

		task.start();
	};

	$.extend(self, {

		defaultOptions: {
			// Overrides require path.
			path: ''
		},

		setup: function() {

			$.extend(self.defaultOptions, options);
		},

		loaders: {},

		task: function(names, options) {

			var task = $.extend(this, $.Deferred());

			task.name = names.join(',');
			
			task.options = options;

			task.url = options.path;

			task.names = names;
		},

		loaders: {},

		loader: function(name) {

			// Pre-define loaders
			if ($.isArray(name)) {
				return $.map(name, function(name){
					return self.loader(name);
				});
			}

			// Resolve loaders
			if ($.isPlainObject(name)) {
				return $.map(name, function(content, name){
					return self.loader(name).resolve(content);
				});
			}

			// Get loader or create loaders
			var loader = self.loaders[name];

			if (!loader) {
				loader = self.loaders[name] = 
					$.Deferred()
						.done(function(string){
							$.language.add(name, string);
						});
			}

			return loader;
		}
	});

	$.extend(self.task.prototype, {

		start: function() {

			var task = this;

			var loaders = [];

			var names = 
				$.map(task.names, function(name){

					// Get existing loader or predefine loaders
					// so that subsequent require calls requesting
					// the same language keys won't be loaded again.
					var loader = self.loader(name);

					// Keep it to our array of loaders
					loader.push(loader);

					// If the language has resolved or rejected
					// remove it from list of language keys to load
					if (/resolved|rejected/.test(loader.state())) return null;

					return name;
				});

			// When unable to load language strings,
			// reject language loaders.
			task.fail(function(){
				$.each(names, function(i, name){
					self.loader(name).reject();
				});
			});

			// When all language strings has been loaded,
			// then we can resolve this task.
			$.when.apply(null, loaders)
				.then(task.resolve, task.reject);

			// If there are no language strings to load,
			// then wait for existing loaders to resolve or reject itself.
			if (names.length < 1) return task;

			task.xhr = 
				$.Ajax({
					url: task.url,
					type: "POST",
					data: {
						keys: names
					}
				})
				.done(function(strings){

					// If returned data is a language key-pair object, resolve task.
					if ($.isPlainObject(strings)) {

						self.loader(strings);
						// We don't need to resolve as the $.when above will resolve for us.
					} else {
						task.reject();
					}
				})
				.fail(function(){
					task.reject();
				});

			return task;
		}
	});

	return self;

})()
);
/**
 * jquery.require.library
 * Foundry script loader.
 *
 * Copyright (c) 2011 Jason Ramos
 * www.stackideas.com
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

$.require.addLoader('library', function() {

	var batch = this,
		args = $.makeArray(arguments),
		options = {},
		names;

	// Expand arguments into its actual definition
	if ($.isPlainObject(args[0])) {
		options = args[0];
		names = args.slice(1);
	} else {
		names = args;
	}

	$.extend(options, {
		path: $.scriptPath
	});

	return batch.script.apply(batch, [options].concat(names));

});
/**
 * jquery.require.stylesheet
 * Stylesheet loader plugin for $.require.
 *
 * Part of jquery.require family.
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

$.require.addLoader('image', (function() {

	var self = function() {

		var batch = this,
			args = $.makeArray(arguments),
			options,
			names;

		// Expand arguments into its actual definition
		if ($.isPlainObject(args[0])) {
			options = args[0];
			names = args.slice(1);
		} else {
			names = args;
		}

		options = $.extend(
			{},
			self.defaultOptions,
			batch.options,
			options,
			{batch: batch}
		);

		$.each(names, function(i, name) {

			var task = new self.task(name, options),
				existingTask = self.images[task.url];

			task = existingTask || task;

			batch.addTask(task);

			if (!existingTask) {
				self.images[task.url] = task;
				task.start();
			}
		});
	};

	$.extend(self, {

		defaultOptions: {
			// Overrides require path.
			path: ''
		},

		setup: function() {

			$.extend(self.defaultOptions, options);
		},

		images: {},

		task: function(name, options) {

			var task = $.extend(this, $.Deferred());

			task.name = name;

			task.options = options;

			// Absolute path
			if ($.isUrl(name)) {

				task.url = name;

			// Relative path
			} else if (/^(\/|\.)/.test(name)) {

				task.url = $.uri(task.options.path)
							.toPath(name)
							.toString();

			// Module path
			} else {

				task.url = $.uri(task.options.path)
							.toPath('./' + name)
							.toString();
			}

			// Remap task.url to task.options.url
			task.options.url = task.url;
		}

	});

	$.extend(self.task.prototype, {

		start: function() {

			var task = this;

			task.image = $(new Image())
							.load(function(){
								task.resolve();
							})
							.error(function(){
								task.reject();
							})
							.attr("src", task.options.url);
		}

	});

	return self;

})()
);

});