dispatch.to("Foundry/2.1 Core Plugins").at(function($, manifest) {

/**
 * jquery.component.
 * Boilerplate for client-side MVC application.
 *
 * Copyright (c) 2011 Jason Ramos
 * www.stackideas.com
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

var Component = $.Component = function(name, options, callback) {

    if (arguments.length < 1) {
        return Component.registry;
    }

    if (arguments.length < 2) {
        return Component.registry[name];
    }

    return Component.register(name, options, callback);
}

Component.registry = {};

Component.proxy = function(component, property, value) {

    // If it's a method
    if ($.isFunction(value)) {

        // Change the "this" context to the component itself
        component[property] = $.proxy(value, component);

    } else {

        component[property] = value;
    }
}

Component.register = function(name, options, callback) {

    // If an abstract component was found,
    // extract the execution queue.
    var queue = (window[name]) ? window[name].queue || [] : [];

    var self =

        // Put it in component registry
        Component.registry[name] =

        // Set it to the global namespace
        window[name] =

        // When called as a function, it will return the correct jQuery object.
        function(command) {

            return ($.isFunction(command)) ? command($) : component;
        };

    // @TODO: Component should be a deferred object, replace $.module("component/mvc").done().

    // Extend component with properties in component prototype
    $.each(Component.prototype, function(property, value) {

        Component.proxy(self, property, value);
    });

    self.$             = $;
    self.options       = options;
    self.className     = name;
    self.identifier    = name.toLowerCase();
    self.componentName = "com_" + self.identifier;
    self.version       = options.version;
    self.safeVersion   = self.version.replace(/\./g,"");

    self.environment   = options.environment  || $.environment;
    self.debug         = (self.environment=='development');
    self.language      = options.language || $.locale.lang || "en";

    self.baseUrl       = options.baseUrl      || $.indexUrl + "?option=" + self.componentName;
    self.scriptPath    = options.scriptPath   || $.rootPath + "media/" + self.componentName + ((self.debug) ? "/scripts_/" : "/scripts/");
    self.templatePath  = options.templatePath || options.scriptPath;
    self.languagePath  = options.languagePath || self.baseUrl + '&tmpl=component&no_html=1&controller=lang&task=getLanguage';
    self.viewPath      = options.viewPath     || self.baseUrl + '&tmpl=component&no_html=1&controller=themes&task=getAjaxTemplate';
    self.prefix        = self.identifier + "/";

    self.optimizeResources  = options.optimizeResources || (self.environment==="optimized") ? true : false;
    self.resourcePath       = options.resourcePath || self.baseUrl + '&tmpl=component&no_html=1&controller=foundry&task=getResource';
    self.resourceCollectionInterval = 1200; // Joomla session timestamp is per second, we add another 200ms just to be safe.

    self.scriptVersioning = options.scriptVersioning || false;

    self.initRecovery     = options.initRecovery || false;

    self.isReady       = false;
    self.dependencies  = $.Deferred();

    // Added console
    self.console = function(method) {

        if (!self.debug) return;

        var console = window.console;

        if (!console) return;

        var method = console[method];

        if (!method) return;

        var args = $.makeArray(arguments).slice(1);

        // Normal browsers
        if (method.apply) {
            method.apply(console, args);
        // IE
        } else {
            method(args.join(" "));
        }
    }

    var resolveComponent = function() {

        self.dependencies.resolve();

        self.ready(function() {
            self.isReady = true;
            self.run(callback);
        });
    }

    // Load component dependencies,
    if ($.isFunction(options.dependencies)) {

        var require = self.require({loadingComponentDependencies: true});

        options.dependencies.call(self, require);

        require.done(resolveComponent);

    // or resolve component straightaway.
    } else {
        resolveComponent();
    }

    // Go through each execution queue and run it
    $.each(queue, function(i, func) {

        if ($.isPlainObject(func)) {

            self[func.method].apply(self, func.args);
        }

        if ($.isArray(func)) {

            var chain = func,
                context = self;

            $.each(chain, function(i, func) {

                context = context[func.method].apply(context, func.args);
            });
        }
    });

    // If the component supports init recovery,
    // then see if it were broken.
    if (self.initRecovery) {

        // When the document is ready
        $(document).ready(function(){

            // If the initializer is resolved, skip.
            if (self.module("init").state()!=="pending") return;

            // If the initializer is still pending, look for it.
            var initializer = $("script[data-id='" + self.identifier + "-init']")[0];

            // If initializer could not be found, skip.
            if (!initializer) return;

            // Try to execute initializer again.
            try {

                eval(initializer.innerHTML);
            } catch(e) {

                // If there was an error executing the initializer, report it.
                throw "Unable to recover component initializer for " + self.className + ". " + e;
            }
        });
    }
}

Component.extend = function(property, value) {

    // For later components
    Component.prototype[property] = value;

    // For existing components
    $.each(Component.registry, function(name, component) {
        Component.proxy(component, property, value);
    });
}

$.extend(Component.prototype, {

    run: function(command) {

        return ($.isFunction(command)) ? command($) : component;
    },

    ready: function(callback) {

        if (!$.isFunction(callback))
            return;

        var self = this;

        // Only when MVC is loaded
        $.module('component/mvc').done(function() {

            // and intial dependencies are loaded
            self.dependencies
                .done(function() {

                    // and document is ready
                    $(document).ready(function() {

                        // then only execute ready callback
                        self.run(callback);

                    });
                });
        });
    },

    template: function(name) {

        var self = this;

        // Get all component templates
        if (name==undefined) {

            return $.grep($.template(), function(template) {

                return template.indexOf(self.prefix)==0;
            });
        }

        // Prepend component prefix
        arguments[0] = self.prefix + name;

        // Getter or setter
        return $.template.apply(null, arguments);
    },

    // Component require extends $.require with the following additional methods:
    // - resource()
    // - view()
    // - language()
    //
    // It also changes the behaviour of existing methods to load in component-specific behaviour.
    require: function(options) {

        var self = this,

            options = options || {},

            require = $.require($.extend({path: self.scriptPath}, options)),

            _require = {};

            // Keep a copy of the original method so the duck punchers below can use it.
            $.each(["library", "script", "language", "template", "done"], function(i, method){
                _require[method] = require[method];
            });

        // Resource call should NOT be called directly.
        // .resource({type: "view", name: "photo.item", loader: deferredObject})
        require.resource = function(resource) {

            // If this is not a valid resource object, skip.
            if (!$.isPlainObject(resource)) return;
            if (!resource.type || !resource.name || !$.isDeferred(resource.loader)) return;

            var batch = this;

            // Get resource collector
            var resourceCollector = self.resourceCollector;

            // If we haven't started collecting resources
            if (!resourceCollector) {

                // Then start collecting resources
                resourceCollector = self.resourceCollector = $.Deferred();

                $.extend(resourceCollector, {

                    name: $.uid("ResourceCollector"),

                    manifest: [],

                    loaderList: [],

                    loaders: [],

                    load: function() {

                        // End this batch of resource collecting
                        delete self.resourceCollector;

                        // If there are not resources to pull,
                        // just resolve resource collector.
                        if (resourceCollector.manifest.length < 0) {
                            resourceCollector.resolve();
                            return;
                        }

                        $.Ajax(
                            {
                                type: 'POST',
                                url: self.resourcePath,
                                dataType: "json",
                                data: {
                                    resource: resourceCollector.manifest
                                }
                            })
                            .done(function(manifest) {

                                if (!$.isArray(manifest)) {
                                    resourceCollector.reject("Server did not return a valid resource manifest.");
                                    return;
                                }

                                $.each(manifest, function(i, resource) {

                                    var content = resource.content;

                                    resourceCollector.loaders[resource.id]
                                        [content!==undefined ? "resolve" : "reject"]
                                        (content);
                                });
                            });

                        // Resolve resource collector when all is done
                        $.when.apply(null, resourceCollector.loaderList)
                            .done(resourceCollector.resolve)
                            .fail(resourceCollector.reject);
                    }
                });

                setTimeout(resourceCollector.load, self.resourceCollectionInterval);
            }

            // Create a resource id
            var id = resource.id = $.uid("Resource");

            // Add to the loader map
            // - to be used to resolve the loader with the returned content
            resourceCollector.loaders[id] = resource.loader;

            // Add to the loader list
            // - to be used with $.when()
            resourceCollector.loaderList.push(resource.loader);

            // Remove the reference to the loader
            // - so the loader doesn't get included in the manifest that gets sent to the server
            delete resource.loader;

            // Then add it to our list of resource manifest
            resourceCollector.manifest.push(resource);

            // Note: Only resource loaders are batch tasks, not resource collectors.
            // var task = resourceCollector;
            // batch.addTask(task);
            return require;
        };

        require.view = function() {

            var batch   = this,

                request = batch.expand(arguments, {path: self.viewPath}),

                loaders = {},

                options = request.options,

                names = $.map(request.names, function(name) {

                    // Get template loader
                    var absoluteName = self.prefix + name,
                        loader = $.template.loader(absoluteName);

                    // See if we need to reload this template
                    if (/resolved|failed/.test(loader.state()) && options.reload) {
                        loader = loader.reset();
                    }

                    // Add template loader as a task of this batch
                    batch.addTask(loader);

                    if (loader.state()!=="pending") return;

                    // Load as part of a coalesced ajax call if enabled
                    if (self.optimizeResources) {

                        require.resource({
                            type: "view",
                            name: name,
                            loader: loader
                        });

                        return;

                    } else {

                        loaders[name] = loader;
                        return name;
                    }
                });

            // Load using regular ajax call
            // This will always be zero when optimizeResources is enabled.
            if (names.length > 0) {

                $.Ajax(
                    {
                        url: options.path,
                        dataType: "json",
                        data: { names: names }
                    })
                    .done(function(templates) {

                        if (!$.isArray(templates)) return;

                        $.each(templates, function(i, template) {

                            var content = template.content;

                            loaders[template.name]
                                [content!==undefined ? "resolve" : "reject"]
                                (content);
                        });
                    });
            }

            return require;
        };

        require.language = function() {

            var batch   = this,

                request = batch.expand(arguments, {path: self.languagePath});

            // Load as part of a coalesced ajax call if enabled
            if (self.optimizeResources) {

                $.each(request.names, function(i, name) {

                    var loader = $.Deferred();

                    loader.name = name;

                    loader.done(function(val){

                        $.language.add(name, val);
                    });

                    batch.addTask(loader);

                    require.resource({
                        type: "language",
                        name: name,
                        loader: loader
                    });
                });

            } else {

                _require.language.apply(require, [request.options].concat(request.names));
            }

            return require;
        };

        require.library = function() {

            // Keep a copy of the component script method
            var o = require.script;

            // Replace component script method
            // with foundry script method
            require.script = _require.script;

            // Execute library method
            _require.library.apply(require, arguments);

            // Reverse script method replacement
            require.script = o;

            return require;
        };

        require.script = function() {

            var batch = this,

                request = batch.expand(arguments)

                names = $.map(request.names, function(name) {

                    // Ignore module definitions
                    if ($.isArray(name) ||

                        // and urls
                        $.isUrl(name) ||

                        // and relative paths.
                        /^(\/|\.)/.test(name)) return name;

                    var moduleName = self.prefix + name,

                        moduleUrl =

                            $.uri(batch.options.path)
                                .toPath(
                                    './' + name + '.' + (request.options.extension || 'js') +
                                    ((self.scriptVersioning) ? "?" + "version=" + self.safeVersion : "")
                                )
                                .toString();

                    return [[moduleName, moduleUrl, true]];
                });

            return _require.script.apply(require, names);
        };

        // Override path
        require.template = function() {

            var batch   = this,

                request = batch.expand(arguments, {path: self.templatePath});

            return _require.template.apply(require, [request.options].concat(

                $.map(request.names, function(name) {

                    return [[self.prefix + name, name]];
                })
            ));
        };

        // To ensure all require callbacks are executed after the component's dependencies are ready,
        // every callback made through component.require() is wrapped in a component.ready() function.
        require.done = function(callback) {

            return _require.done.call(require, function(){

                $.module('component/mvc').done(

                    (options.loadingComponentDependencies) ?

                        function() {
                            callback.call(self, $);
                        } :

                        function() {
                            self.ready(callback);
                        }
                );
            });
        };

        return require;
    },

    module: function(name, factory) {

        var self = this;

        // TODO: Support for multiple module factory assignment
        if ($.isArray(name)) {
            return;
        }

        var fullname = self.prefix + name;

        return (factory) ?

            // Set module
            $.module.apply(null, [fullname, function(){

                var module = this;

                if (name==="init") {

                    factory.call(module, $);
                    return;
                }

                // Wait until MVC is loaded
                $.module('component/mvc').done(function(){

                    factory.call(module, $);

                });
            }])

            :

            // Get module
            $.module(fullname);
    }
});
$.module('component/mvc', function() {

var module = this;

$.require()
    .library(
        'server',
        'mvc/controller',
        'mvc/model',
        'mvc/model.list',
        'mvc/view',
        'mvc/view.ejs',
        'mvc/lang.json'
    )
    .done(function() {

        $.Component.extend("ajax", function(namespace, params, callback) {

            var self = this;

            var options = {
                    url: self.baseUrl,
                    data: $.extend(
                        params,
                        {
                            option: self.componentName,
                            namespace: namespace
                        }
                    )
                };

            options = $.extend(true, options, self.options.ajax);

            // Look for an updated token replaced by Joomla on page load and use
            // that token instead. This is for sites where cache is turned on.
            var token = $("span#" + self.identifier + "-token input").attr("name");

            if (token) {
                options.data[token] = 1;
            }

            // This is for server-side function arguments
            if (options.data.hasOwnProperty('args')) {
                options.data.args = $.toJSON(options.data.args);
            }

            if ($.isPlainObject(callback)) {

                if (callback.type) {

                    switch (callback.type) {

                        case 'jsonp':

                            callback.dataType = 'jsonp';

                            // This ensure jQuery doesn't use XHR should it detect the ajax url is a local domain.
                            callback.crossDomain = true;

                            options.data.transport = 'jsonp';
                            break;

                        case 'iframe':

                            // For use with iframe-transport
                            callback.iframe = true;

                            callback.processData = false;

                            callback.files = options.data.files;

                            delete options.data.files;

                            options.data.transport = 'iframe';
                            break;
                    }

                    delete callback.type;
                }

                $.extend(options, callback);
            }

            if ($.isFunction(callback)) {
                options.success = callback;
            }

            return $.server(options);
        });

        $.Component.extend("Controller", function() {

            var self = this,
                args = $.makeArray(arguments),
                name = self.className + '.Controller.' + args[0],
                staticProps,
                protoFactory;

            // Getter
            if (args.length==1) {
                return $.String.getObject(args[0]);
            };

            // Setter
            if (args.length > 2) {
                staticProps = args[1],
                protoFactory = args[2]
            } else {
                staticProps = {},
                protoFactory = args[1]
            }

            // Map component as a static property
            // of the controller class
            $.extend(staticProps, {
                component: self
            });

            return $.Controller.apply(this, [name, staticProps, protoFactory]);
        });

        $.Component.extend("Model", function() {
            var self = this,
                args = $.makeArray(arguments),
                name = self.className + '.Model.' + args[0],
                staticProps,
                protoFactory;

            // Getter
            if (args.length==1) {
                return $.String.getObject(args[0]);
            }

            if( args.length==2) {
                staticProps = {},
                protoFactory = args[1]
            }

            if( args.length > 2) {
                staticProps = args[1],
                protoFactory = args[2]
            }

            // Map component as a static property
            // of the model class
            $.extend(staticProps, {
                component: self
            });

            return $.Model.apply(this, [name, staticProps, protoFactory]);
        });

        $.Component.extend("Model.List", function() {
            var self = this,
                args = $.makeArray(arguments),
                name = self.className + '.Model.List.' + args[0],
                staticProps,
                protoFactory;

            // Getter
            if (args.length==1) {
                return $.String.getObject(args[0]);
            }

            if( args.length==2) {
                staticProps = {},
                protoFactory = args[1]
            }

            if( args.length > 2) {
                staticProps = args[1],
                protoFactory = args[2]
            }

            // Map component as a static property
            // of the model class
            $.extend(staticProps, {
                component: self
            });

            return $.Model.List.apply(this, [name, staticProps, protoFactory]);
        });

        $.Component.extend("View", function(name) {

            var self = this;

            // Gett all component views
            if (arguments.length < 1) {
                return self.template();
            }

            // Prepend component prefix
            arguments[0] = self.prefix + arguments[0];

            // Getter or setter
            return $.View.apply(this, arguments);
        });

        module.resolve();

    });

});
// Component should always be the last core plugin to load.
// Now that Component is done loading, we open the flood gate,
// distribute Foundry to all.

dispatch("Foundry/2.1").toAll();

}); // dispatch: end