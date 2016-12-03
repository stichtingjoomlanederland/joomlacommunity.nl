// Misc jquery enhancements
ed.define('jquery.utils', ['edjquery'], function($) {

    var jQuery = $;
	
    /**
     * jquery.Bloop
     * Binary loop helper.
     * https://github.com/jstonne/jquery.Bloop
     *
     * Part of the jQuery Utils family:
     * https://github.com/jstonne/jquery.utils
     *
     * Copyright (c) 2014 Jensen Tonne & Jason Rey
     * http://jstonne.me
     *
     * Dual licensed under the MIT and GPL licenses:
     * http://www.opensource.org/licenses/mit-license.php
     * http://www.gnu.org/licenses/gpl.html
     *
     */

    (function(){

    	var Bloop = function(items) {

    		this.items = items;
    		this.start = 0;
    		this.end = items.length - 1;
    		this.node = null;
    		this.stopped = false;
    	};

    	$.extend(Bloop.prototype, {

    		isLooping: function() {

    			if (this.stopped) return false;

    			if (Math.abs(this.start - this.end) > 1) {
    				this.node = Math.floor((this.start + this.end) / 2);
    				return true;
    			}

    			return false;
    		},

    		flip: function(flip) {

    			if (flip) {
    				this.end = this.node - 1;
    			} else {
    				this.start = this.node + 1;
    			}
    		},

    		stop: function() {
    			this.stop = true;
    		}
    	});


    	$.Bloop = function(items){

    		return new Bloop(items);
    	}

    })();
    ;/*!
     * jquery.Chunk
     * Utility to handle large arrays by processing
     * them in smaller manageable chunks.
     *
     * Part of the jQuery Utils family:
     * https://github.com/jstonne/jquery.utils
     *
     * Copyright (c) 2014 Jensen Tonne
     * http://jstonne.me
     *
     * Dual licensed under the MIT and GPL licenses:
     * http://www.opensource.org/licenses/mit-license.php
     * http://www.gnu.org/licenses/gpl.html
     *
     */

    $.Chunk = function(array, options) {

    	if ($.isArray(array)) {
    		array = [];
    	}

    	var options = $.extend({},
    		{
    			size: 256,
    			every: 1000
    		},
    		options
    	);

    	var self = $.extend($.Deferred(), {

    		size: options.size,

    		every: options.every,

    		from: 0,

    		to: array.length,

    		process: function(callback) {

    			self.process.fn = callback;

    			return self;
    		},

    		chunkStart: function(callback) {

    			self.chunkStart.fn = callback;

    			return self;
    		},

    		chunkEnd: function(callback) {

    			self.chunkEnd.fn = callback;

    			return self;
    		},

    		start: function() {

    			self.stopped = false;

    			self.iterate();

    			return self;
    		},

    		iterate: function() {

    			if (self.stopped) return;

    			var iterator = self.process.fn;

    			if (!iterator) return;

    			self.to = from.size + self.size;

    			var max = array.length;

    			if (self.to > max) {

    				self.to = max;
    			}

    			var range = {from: self.from, to: self.to};

    			// Trigger chunkStart event
    			self.chunkStart.fn && self.chunkStart.fn.call(self, range.from, range.to);

    			while (self.from < self.to) {

    				if (self.stopped) break;

    				iterator.call(self, array[self.from]);

    				self.from++;
    			}

    			// Trigger chunkEnd event
    			self.chunkEnd.fn && self.chunkEnd.fn.call(self, range.from, range.to);

    			// Always get the latest array length because
    			// it may change through iteration
    			self.completed = (self.from >= array.length - 1);

    			if (self.completed) {

    				self.resolveWith(self);

    			} else {

    				self.nextIteration = setTimeout(self.iterate, self.every);
    			}

    			return self;
    		},

    		pause: function() {

    			self.stopped = true;

    			clearTimeout(self.nextIteration);

    			return self;
    		},

    		restart: function() {

    			if (self.state()==="rejected") return self;

    			self.from = 0;

    			self.start();

    			return self;
    		},

    		stop: function() {

    			self.pause();

    			self.rejectWith(self, [self.from]);

    			return self;
    		}
    	});

    	return self;
    };
    ;/**
     * jquery.Enqueue
     * Execute only the last added callback.
     *
     * Part of the jQuery Utils family:
     * https://github.com/jstonne/jquery.utils
     *
     * Copyright (c) 2014 Jensen Tonne & Jason Rey
     * http://jstonne.me
     *
     * Dual licensed under the MIT and GPL licenses:
     * http://www.opensource.org/licenses/mit-license.php
     * http://www.gnu.org/licenses/gpl.html
     *
     */

    (function(isFunction) {

    	var Enqueue = function() {
    		this.lastId = 0;
    	};

    	Enqueue.prototype.queue = function(filter) {

     		var self = this,
     			id = $.uid();
     			self.lastId = id;

    		return function() {

    			if (self.lastId===id) {

    				var args = arguments,
    					args = (isFunction(filter)) ? filter.apply(this, args) : args;

    				return (isFunction(self.fn)) ? self.fn.apply(this, args) : args;
    			}
    		}
    	};

    	$.Enqueue = function(fn) {

    		var self = new Enqueue();

    		if (isFunction(fn)) self.fn = fn;

    		var func = $.proxy(self.queue, self);

    		func.reset = function() {
    			self.lastId = 0;
    		};

    		return func;
    	};
    })($.isFunction);
    ;


    /**
     * jquery.Exception
     * Standardized exception object.
     *
     * Part of the jQuery Utils family:
     * https://github.com/jstonne/jquery.utils
     *
     * Copyright (c) 2014 Jensen Tonne
     * http://jstonne.me
     *
     * Dual licensed under the MIT and GPL licenses:
     * http://www.opensource.org/licenses/mit-license.php
     * http://www.gnu.org/licenses/gpl.html
     *
     */

    (function(){
        var consoleMethod = {
            error: "error",
            warning: "warn",
            success: "log",
            info: "info"
        };

        // $.Exception("message");
        // $.Exception("success", "message");
        // $.Exception("error", "message", data);
        // $.Exception({type: "info", message: "message", foo: "bar", key: "val"});
        $.Exception = function(exception) {

            // Normalize arguments
            var args = arguments,
                simple = args.length==1,
                hasData = args.length==3;

            exception = $.isPlainObject(exception) ?
                exception :
                {
                    type   : simple ? "error" : args[0],
                    message: simple ? args[0] : args[1]
                }

            hasData && $.extend(exception, args[2]);

            if ($.environment=="development") {
                console[consoleMethod[exception.type]](exception.message, exception);
            }

            return exception;
        }
    })();;/**
     * jquery.IE
     * Returns the current IE version.
     *
     * Based on Padolsey's IE detection script.
     * https://gist.github.com/padolsey/527683
     *
     * Part of the jQuery Utils family:
     * https://github.com/jstonne/jquery.utils
     *
     * Copyright (c) 2014 Jensen Tonne
     * http://jstonne.me
     *
     * Dual licensed under the MIT and GPL licenses:
     * http://www.opensource.org/licenses/mit-license.php
     * http://www.gnu.org/licenses/gpl.html
     *
     */

    $.IE = (function(){

        // It seems Padolsey's IE detection script
        // doesn't work on IE10 and 11.
        var ua = navigator.userAgent;
        if (ua.match(/MSIE 9/)) return 9;
        if (ua.match(/MSIE 10/)) return 10;
        if (ua.match(/rv:11/i)) return 11;

        var undef,
            v = 3,
            div = document.createElement('div'),
            all = div.getElementsByTagName('i');

        while (
            v++,
            div.innerHTML = '<!--[if gt IE ' + v + ']><i></i><![endif]-->',
            all[0]
        );

        return v > 4 ? v : undef;

    }());;/**
     * jquery.Task
     * Task runner utility.
     *
     * Part of the jQuery Utils family:
     * https://github.com/jstonne/jquery.utils
     *
     * Copyright (c) 2014 Jensen Tonne
     * http://jstonne.me
     *
     * Dual licensed under the MIT and GPL licenses:
     * http://www.opensource.org/licenses/mit-license.php
     * http://www.gnu.org/licenses/gpl.html
     *
     */

    $.Task = function(props) {

        var task = $.extend(
            $.Deferred(),
            {
                data: {},
                list: [],
                add: function(name) {

                    var item = $.extend(
                        $.Deferred(),
                        {
                            name: name,
                            item: item
                        }
                    );

                    task.list.push(item);

                    return task;
                },
                process: function() {

                    if (!task._promise) {

                        task._promise =
                            $.when.apply($, task.list)
                                .then(
                                    task.resolve,
                                    task.reject,
                                    task.progress
                                );
                    }

                    return task;
                }
            },
            props
        );

        return task;
    };;/**
     * jquery.Threads
     * A manager that controls threads a.k.a. execution of function simultaneously.
     *
     * Part of the jQuery Utils family:
     * https://github.com/jstonne/jquery.utils
     *
     * Copyright (c) 2014 Jensen Tonne & Jason Rey
     * http://jstonne.me
     *
     * Dual licensed under the MIT and GPL licenses:
     * http://www.opensource.org/licenses/mit-license.php
     * http://www.gnu.org/licenses/gpl.html
     *
     */

    (function() {

    	var Threads = function(options) {
    		this.threads = [];
    		this.threadCount = 0;
    		this.threadLimit = options.threadLimit || 1;
    		this.threadDelay = options.threadDelay || 0;
    	}

    	$.extend(Threads.prototype, {

    		add: function(thread, type) {

    			if (!$.isFunction(thread)) return;

    			thread.type = type || "normal";

    			if (type=="deferred") {
    				thread.deferred = $.Deferred().always($.proxy(this.next, this));
    			}

    			this.threads.push(thread);

    			this.run();
    		},

    		addDeferred: function(thread) {

    			return this.add(thread, "deferred");
    		},

    		next: function() {

    			// Reduce thread count
    			this.threadCount--;

    			// And see if there's anymore task to run
    			this.run();
    		},

    		run: function() {

    			var self = this;

    			setTimeout(function(){

    				if (self.threads.length < 1) return;

    				if (self.threadCount < self.threadLimit) {

    					self.threadCount++;

    					var thread = self.threads.shift();

    					// Wrap in a try catch in case if the thread
    					// throws an error it doesn't break our chain.
    					try { thread.call(thread, thread.deferred); }
    					catch(e) { }

    					!thread.deferred && self.next();
    				}

    			}, self.threadDelay);
    		}
    	});

    	$.Threads = function(options) {

    		return new Threads(options);
    	};

    })();
    ;/**
     * jquery.callback
     * Creates a global callback function that gets
     * removed from the window object after it has executed.
     *
     * Part of the jQuery Utils family:
     * https://github.com/jstonne/jquery.utils
     *
     * Copyright (c) 2014 Jensen Tonne
     * http://jstonne.me
     *
     * Dual licensed under the MIT and GPL licenses:
     * http://www.opensource.org/licenses/mit-license.php
     * http://www.gnu.org/licenses/gpl.html
     *
     */

    $.callback = function(func, persist){

    	// Create callback
    	if ($.isFunction(func)) {

    		var funcName = $.uid("cb");

    		window[funcName] = function(){

    			// Destroy itself after callback has been called
    			if (!persist) {
    				delete window[funcName];
    			}

    			return func.apply(null, arguments);
    		}

    		return funcName;
    	}

    	// Callback method
    	if ($.isString(func)) {
    		switch (func) {
    			case "destroy":
    				var funcName = persist;
    				delete window[funcName];
    				break;
    		}
    	}
    };/**
     * jquery.fn.checkList.
     * Multiple checkbox handler.
     *
     * $(e).checkList({
     *    check  : function(){},   // callback when an input is checked
     *    uncheck: function(){},   // callback when an input is unchecked
     *
     *    // returns checked elements & unchecked elements in separate arguments
     *    change : function(checked, unchecked){}
     * })
     *
     * Part of the jQuery Utils family:
     * https://github.com/jstonne/jquery.utils
     *
     * Copyright (c) 2014 Jensen Tonne
     * http://jstonne.me
     *
     * Dual licensed under the MIT and GPL licenses:
     * http://www.opensource.org/licenses/mit-license.php
     * http://www.gnu.org/licenses/gpl.html
     *
     */

    $.fn.checkList = function(options) {

        var defaultOptions = {
            checkbox: ".checkbox",
            masterCheckbox: ".master-checkbox",
            check: function() {},
            uncheck: function() {},
            change: function() {}
        }

        var options = $.extend({}, defaultOptions, options),
            checkList       = this,
            checkboxes      = checkList.find(options.checkbox),
            masterCheckbox  = checkList.find(options.masterCheckbox),
            disableChangeEvent = false;

        var change = function() {

            if (!disableChangeEvent) {

                var checked = checkboxes.filter(':checked'),
                    unchecked = checkboxes.not(':checked');

                if (checked.length < 1) {
                    masterCheckbox.removeAttr("checked");
                }

                if (checked.length == checkboxes.length) {
                    masterCheckbox.prop("checked", true);
                }

                options.change.call(checkList, checked, unchecked);
            }
        }

        checkboxes.checked(

            // checked
            function() {
                options.check.apply(checkList);
                change();
            },

            // unchecked
            function() {
                options.uncheck.apply(checkList);
                change();
            }
        );

        masterCheckbox.checked(

            // checked
            function() {
                disableChangeEvent = true;
                checkboxes.checked(true);
                disableChangeEvent = false;
                change();
            },

            // unchecked
            function() {
                disableChangeEvent = true;
                checkboxes.checked(false);
                disableChangeEvent = false;
                change();
            }
        );

        change();

        return this;
    };;/**
     * jquery.classManip
     * Utilities to manipulate classnames.
     *
     * Part of the jQuery Utils family:
     * https://github.com/jstonne/jquery.utils
     *
     * Copyright (c) 2014 Jensen Tonne
     * http://jstonne.me
     *
     * Dual licensed under the MIT and GPL licenses:
     * http://www.opensource.org/licenses/mit-license.php
     * http://www.gnu.org/licenses/gpl.html
     *
     */

    /**
     * $.fn.switchClass
     * Swaps a classname for another classname that bears identical prefix.
     *
     * $("div").switchClass("state-busy")l;
     *
     * Before:
     * <div class="state-idle"></div>
     *
     * After:
     * <div class="state-busy"></div>
     */
    $.fn.switchClass = function(classname, delimiter){

    	var delimiter = delimiter || "-",
    		prefix = classname.split(delimiter)[0] + delimiter,
    		length = prefix.length;

    	return this.each(function(){

    		var $el = $(this),
    			classnames =
    				$.map(($el.attr("class") || "").split(" "), function(classname){
    					return (classname.slice(0, length)==prefix || classname=="") ? null : classname;
    				});
    			classnames.push(classname);

    		$el.attr("class", classnames.join(" "));
    	});
    };

    /**
     * $.fn.activateClass
     * Add classname on current set of elements and
     * remove classname on previous set of elements.
     *
     * $(".item").find("[data-id=64]").activateClass("active");
     *
     * Before:
     * <div class="item active" data-id="62"></div>
     * <div class="item" data-id="63"></div>
     * <div class="item" data-id="64"></div>
     *
     * After:
     * <div class="item" data-id="62"></div>
     * <div class="item" data-id="63"></div>
     * <div class="item active" data-id="64"></div>
     */
    $.fn.activateClass = function(className) {
        this.prevObject.removeClass(className);
        return $(this).addClass(className);
    };;/**
     * jquery.color
     * Color helpers.
     *
     * Part of the jQuery Utils family:
     * https://github.com/jstonne/jquery.utils
     *
     * Copyright (c) 2014 Jensen Tonne
     * http://jstonne.me
     *
     * Dual licensed under the MIT and GPL licenses:
     * http://www.opensource.org/licenses/mit-license.php
     * http://www.gnu.org/licenses/gpl.html
     *
     */
    (function(){

    var hexToRgb = function(hex) {
        var hex = parseInt(((hex.indexOf('#') > -1) ? hex.substring(1) : hex), 16);
        return {r: hex >> 16, g: (hex & 0x00FF00) >> 8, b: (hex & 0x0000FF)};
    };

    var hexToHsb = function(hex) {
        return rgbToHsb(hexToRgb(hex));
    };

    var rgbToHsb = function(rgb) {
        var hsb = {h: 0, s: 0, b: 0};
        var min = Math.min(rgb.r, rgb.g, rgb.b);
        var max = Math.max(rgb.r, rgb.g, rgb.b);
        var delta = max - min;
        hsb.b = max;
        hsb.s = max != 0 ? 255 * delta / max : 0;
        if (hsb.s != 0) {
            if (rgb.r == max) hsb.h = (rgb.g - rgb.b) / delta;
            else if (rgb.g == max) hsb.h = 2 + (rgb.b - rgb.r) / delta;
            else hsb.h = 4 + (rgb.r - rgb.g) / delta;
            hsb.h *= 60;
        } else hsb.h = 360;
        if (hsb.h < 0) hsb.h += 360;
        hsb.s *= 100/255;
        hsb.b *= 100/255;
        return hsb;
    };

    var hsbToRgb = function(hsb) {
        var rgb = {};
        var h = hsb.h;
        var s = hsb.s*255/100;
        var v = hsb.b*255/100;
        if(s == 0) {
            rgb.r = rgb.g = rgb.b = v;
        } else {
            var t1 = v;
            var t2 = (255-s)*v/255;
            var t3 = (t1-t2)*(h%60)/60;
            if(h==360) h = 0;
            if(h<60) {rgb.r=t1; rgb.b=t2; rgb.g=t2+t3}
            else if(h<120) {rgb.g=t1; rgb.b=t2; rgb.r=t1-t3}
            else if(h<180) {rgb.g=t1; rgb.r=t2; rgb.b=t2+t3}
            else if(h<240) {rgb.b=t1; rgb.r=t2; rgb.g=t1-t3}
            else if(h<300) {rgb.b=t1; rgb.g=t2; rgb.r=t2+t3}
            else if(h<360) {rgb.r=t1; rgb.g=t2; rgb.b=t1-t3}
            else {rgb.r=0; rgb.g=0; rgb.b=0}
        }
        return {r:Math.round(rgb.r), g:Math.round(rgb.g), b:Math.round(rgb.b)};
    };

    var rgbToHex = function(rgb) {
        var hex = [
            rgb.r.toString(16),
            rgb.g.toString(16),
            rgb.b.toString(16)
        ];
        $.each(hex, function (nr, val) {
            if (val.length == 1) {
                hex[nr] = '0' + val;
            }
        });
        return hex.join('');
    };

    var hsbToHex = function (hsb) {
        return rgbToHex(hsbToRgb(hsb));
    };

    var fixHsb = function (hsb) {
        return {
            h: Math.min(360, Math.max(0, hsb.h)),
            s: Math.min(100, Math.max(0, hsb.s)),
            b: Math.min(100, Math.max(0, hsb.b))
        };
    };

    var fixRgb = function (rgb) {
        return {
            r: Math.min(255, Math.max(0, rgb.r)),
            g: Math.min(255, Math.max(0, rgb.g)),
            b: Math.min(255, Math.max(0, rgb.b))
        };
    };

    var fixHex = function (hex) {
        var len = 6 - hex.length;

        if (len == 3) {
            var chars = hex.split(""), chr, hex = "";
            while (chr = chars.shift()) hex += chr + chr;
        } else {
            while (len--) hex = "0" + hex;
        }

        hex.replace(/[^A-Fa-f0-9]/g, "0");

        return hex;
    };

    $.extend($, {
        hexToRgb: hexToRgb,
        hexToHsb: hexToHsb,
        rgbToHsb: rgbToHsb,
        hsbToRgb: hsbToRgb,
        rgbToHex: rgbToHex,
        hsbToHex: hsbToHex,
        fixHsb: fixHsb,
        fixRgb: fixRgb,
        fixHex: fixHex
    });

    })();;;/**
     * jquery.fn.htmlData
     * Utilities to handle data within jQuery elements.
     *
     * Part of the jQuery Utils family:
     * https://github.com/jstonne/jquery.utils
     *
     * Copyright (c) 2014 Jensen Tonne
     * http://jstonne.me
     *
     * Dual licensed under the MIT and GPL licenses:
     * http://www.opensource.org/licenses/mit-license.php
     * http://www.gnu.org/licenses/gpl.html
     *
     */

    /**
     * jquery.fn.htmlData
     * Converts inline data attributes into objects.
     */
    $.fn.htmlData = function(prefix, nested) {

        var nested = nested===undefined ? true : nested,
            re = new RegExp("^" + "data-" + (prefix ? prefix + "-" : "") + "(.*)", "i"),
            parts,
            data = {};

        // Extract options from data attributes
        $.each(this[0].attributes, function(i, attr){

            if (attr.specified && (parts = attr.name.match(re)) && parts[1]) {
                if (nested) {
                    var props = parts[1].split("-"),
                        i, prop, obj = data; max = props.length - 1;

                    for (i=0; i<=max; i++) {
                        prop = props[i];
                        if (i==max) {
                            obj[prop] = attr.value;
                        } else {
                            !obj[prop] && (obj[prop] = {});
                            obj = obj[prop];
                        }
                    }
                } else {
                    data[parts[1]] = attr.value;
                }
            }
        });

        return data;
    };

    /**
     * jquery.fn.defineData
     * Creates persistent data that cannot be changed.
     */
    $.fn.defineData = function(name, value) {

        if (this.data(name)===undefined) {
            this.data(name, value);
        }

        return this;
    };/**
     * jquery.deletes
     * Remove properties from objects.
     *
     * Part of the jQuery Utils family:
     * https://github.com/jstonne/jquery.utils
     *
     * Copyright (c) 2014 Jensen Tonne
     * http://jstonne.me
     *
     * Dual licensed under the MIT and GPL licenses:
     * http://www.opensource.org/licenses/mit-license.php
     * http://www.gnu.org/licenses/gpl.html
     *
     */

    $.deletes = function(obj, props) {
        $.each(props, function(i, prop){
            delete obj[prop];
        });
    };
    ;/**
     * jquery.fn.disabled
     * jquery.fn.enabled
     *
     * Determine if an element is disabled.
     * Also lets you disable or enable an element.
     *
     * Part of the jQuery Utils family:
     * https://github.com/jstonne/jquery.utils
     *
     * Copyright (c) 2014 Jensen Tonne
     * http://jstonne.me
     *
     * Dual licensed under the MIT and GPL licenses:
     * http://www.opensource.org/licenses/mit-license.php
     * http://www.gnu.org/licenses/gpl.html
     *
     */


    $.fn.disabled = function(state) {
    	return (state===undefined) ?
    				(this.is(":disabled") || this.hasClass('disabled')) :
    				this.prop('disabled', !!state).toggleClass("disabled", !!state);
    };

    $.fn.enabled = function(state) {
    	return (state===undefined) ? !this.disabled() : this.disabled(!state);
    };
    ;/**
     * jquery.distinct
     * Enhanced version of jQuery.unique that also removes
     * removes object/string/integer duplicates within an array.
     * https://github.com/jstonne/jquery.distinct
     *
     * Part of the jQuery Utils family:
     * https://github.com/jstonne/jquery.utils
     *
     * Copyright (c) 2014 Jensen Tonne
     * http://jstonne.me
     *
     * Dual licensed under the MIT and GPL licenses:
     * http://www.opensource.org/licenses/mit-license.php
     * http://www.gnu.org/licenses/gpl.html
     *
     */

    $.distinct = function(items) {

    	var uniqueElements = $.unique;

    	if (items.length < 1) {
    		return;
    	};

    	// If item is an array of DOM elements
    	if (items[0].nodeType) {

    		return uniqueElements.apply(this, arguments);
    	};

    	// If item is an array of objects
    	if (typeof items[0]=='object') {

    		var unique = Math.random(),
    			uniqueObjects = [];

    		$.each(items, function(i) {

    			if (!items[i][unique]) {

    				uniqueObjects.push(items[i]);

    				items[i][unique] = true;
    			}
    		});

    		$.each(uniqueObjects, function(i) {

    			delete uniqueObjects[i][unique];
    		});

    		return uniqueObjects;
    	};

    	// Anything else (can be combination of string, integers and boolean)
    	return $.grep(items, function(item, i) {

    		return $.inArray(item, items) === i;
    	});

    };
    ;/**
     * jquery.fn.domManip
     * Shorthands for common DOM operations.
     *
     * Part of the jQuery Utils family:
     * https://github.com/jstonne/jquery.utils
     *
     * Copyright (c) 2014 Jensen Tonne
     * http://jstonne.me
     *
     * Dual licensed under the MIT and GPL licenses:
     * http://www.opensource.org/licenses/mit-license.php
     * http://www.gnu.org/licenses/gpl.html
     *
     */

    $.fn.tagName = function(){
        return (this[0] || {}).tagName;
    };

    $.create = function(tagName) {
        return $(document.createElement(tagName));
    };

    $.fn.editable = function(editable) {
        if ($.isUndefined(editable)) return this.prop("contenteditable")==="true";
        this.prop("contenteditable", editable);
        editable===false && this.removeAttr("contenteditable");
        return this;
    };/**
     * jquery.download
     * Simulate a download programatically.
     *
     * The download url should return the correct
     * Content-Type in the response headers to work.
     *
     * Part of the jQuery Utils family:
     * https://github.com/jstonne/jquery.utils
     *
     * Copyright (c) 2014 Jensen Tonne
     * http://jstonne.me
     *
     * Dual licensed under the MIT and GPL licenses:
     * http://www.opensource.org/licenses/mit-license.php
     * http://www.gnu.org/licenses/gpl.html
     *
     */

    $.download = function(src) {
        return $("<iframe>").hide().appendTo("body").bind("load", function(){$(this).remove()}).attr("src", src);
    };;/**
     * jquery.eventManip
     * Utilities to handle events in jQuery.
     *
     * Part of the jQuery Utils family:
     * https://github.com/jstonne/jquery.utils
     *
     * Copyright (c) 2014 Jensen Tonne
     * http://jstonne.me
     *
     * Dual licensed under the MIT and GPL licenses:
     * http://www.opensource.org/licenses/mit-license.php
     * http://www.gnu.org/licenses/gpl.html
     *
     */

    /**
     * jquery.ns
     * Adds namespace to events.
     * $(el).on($.ns("mousedown keyup keydown", ".foobar"), function(){});
     */
    $.ns = function(event, ns) {
        return event.split(" ").join(ns + " ") + ns;
    };


    /**
     * jquery.getPointerPosition
     * Get pointer position whether it came from mouse or touch events.
     */
    $.getPointerPosition = function(event) {

        return event.type.match("touch") ?
            {
                x: event.originalEvent.changedTouches[0].pageX,
                y: event.originalEvent.changedTouches[0].pageY
            } :
            {
                x: event.pageX,
                y: event.pageY
            };
    };;/**
     * jquery.eventable
     * Extend objects with simple event system.
     *
     * Requires jquery.deletes.
     *
     * Part of the jQuery Utils family:
     * https://github.com/jstonne/jquery.utils
     *
     * Copyright (c) 2014 Jensen Tonne
     * http://jstonne.me
     *
     * Dual licensed under the MIT and GPL licenses:
     * http://www.opensource.org/licenses/mit-license.php
     * http://www.gnu.org/licenses/gpl.html
     *
     */

    (function() {

    	var instance = "___eventable",
    		publicMethods = ["on", "off", "fire"],
    		getEventName = function(name){
    			return name.split(".")[0];
    		};

    	var Eventable = function(mode) {
    		this.fnList = {};
    		this.events = {};
    		this.mode = mode;
    	}

    	$.extend(
    		Eventable.prototype,
    		{
    			createEvent: function(name) {

    				return this.events[name] = $.Callbacks(this.mode);
    			},

    			on: function(name, fn) {

    				if (!name || !$.isFunction(fn)) return this;

    				var fnList = this.fnList;

    				(fnList[name] || (fnList[name] = [])).push(fn);

    				// Translate into base event name
    				var basename = getEventName(name);

    				// Add the event
    				(this.events[basename] || this.createEvent(basename)).add(fn);

    				return this;
    			},

    			off: function(name) {

    				if (!name) return this;

    				var basename = getEventName(name),
    					event = this.events[basename];

    				if (!event) return this;

    				var removeCallbacks = function(fnList) {

    					$.each(fnList, function(i, fn) {
    						event.remove(fn);
    					});
    				}

    				if (basename!==name) {

    					$.each(this.fnList, function(name, fnList) {

    						if (name.indexOf(basename) > -1) {

    							removeCallbacks(fnList);
    						}
    					});

    				} else {

    					removeCallbacks(this.fnList[name]);
    				}

    				return this;
    			},

    			fire: function(name) {

    				var event = this.events[name];

    				if (!event) return;

    				event.fire.apply(event, $.makeArray(arguments).slice(1));

    				return this;
    			},

    			destroy: function() {
    				for (name in this.events) {
    					this.events[name].disable();
    				}
    			}
    		}
    	);

    	$.eventable = function(obj, mode) {

    		var eventable = obj[instance];

    		if (eventable && mode==="destroy") {
    			eventable.destroy();
    			$.deletes(obj, publicMethods);
    			return delete obj[instance];
    		}

    		eventable = obj[instance] = new Eventable(mode);

    		obj.on = $.proxy(eventable.on, eventable);
    		obj.off = $.proxy(eventable.off, eventable);
    		obj.fire = $.proxy(eventable.fire, eventable);

    		return obj;
    	}

    })();
    ;/**
     * jquery.fn.checkList.
     * Multiple checkbox handler.
     *
     * $(e).checkList({
     *    check  : function(){},   // callback when an input is checked
     *    uncheck: function(){},   // callback when an input is unchecked
     *
     *    // returns checked elements & unchecked elements in separate arguments
     *    change : function(checked, unchecked){}
     * })
     *
     * Part of the jQuery Utils family:
     * https://github.com/jstonne/jquery.utils
     *
     * Copyright (c) 2014 Jensen Tonne
     * http://jstonne.me
     *
     * Dual licensed under the MIT and GPL licenses:
     * http://www.opensource.org/licenses/mit-license.php
     * http://www.gnu.org/licenses/gpl.html
     *
     */

    $.fn.checkList = function(options) {

        var defaultOptions = {
            checkbox: ".checkbox",
            masterCheckbox: ".master-checkbox",
            check: function() {},
            uncheck: function() {},
            change: function() {}
        }

        var options = $.extend({}, defaultOptions, options),
            checkList       = this,
            checkboxes      = checkList.find(options.checkbox),
            masterCheckbox  = checkList.find(options.masterCheckbox),
            disableChangeEvent = false;

        var change = function() {

            if (!disableChangeEvent) {

                var checked = checkboxes.filter(':checked'),
                    unchecked = checkboxes.not(':checked');

                if (checked.length < 1) {
                    masterCheckbox.removeAttr("checked");
                }

                if (checked.length == checkboxes.length) {
                    masterCheckbox.prop("checked", true);
                }

                options.change.call(checkList, checked, unchecked);
            }
        }

        checkboxes.checked(

            // checked
            function() {
                options.check.apply(checkList);
                change();
            },

            // unchecked
            function() {
                options.uncheck.apply(checkList);
                change();
            }
        );

        masterCheckbox.checked(

            // checked
            function() {
                disableChangeEvent = true;
                checkboxes.checked(true);
                disableChangeEvent = false;
                change();
            },

            // unchecked
            function() {
                disableChangeEvent = true;
                checkboxes.checked(false);
                disableChangeEvent = false;
                change();
            }
        );

        change();

        return this;
    };/**
     * jquery.fn.checked
     * Checked/unchecked event handler for checkbox & radio button.
     *
     * Part of the jQuery Utils family:
     * https://github.com/jstonne/jquery.utils
     *
     * Copyright (c) 2014 Jensen Tonne
     * http://jstonne.me
     *
     * Dual licensed under the MIT and GPL licenses:
     * http://www.opensource.org/licenses/mit-license.php
     * http://www.gnu.org/licenses/gpl.html
     *
     */

    $.fn.checked = function(checked, unchecked) {

    	// Return checked value if no arguments are given;
    	if (arguments.length < 1)
    		return this.is(':checked');

    	this.each(function(i) {

    		var input = $(this);

    		if (typeof checked == "boolean") {
    			input.attr('checked', checked).trigger('change');
    			return;
    		}

    		if (input.is('input[type=checkbox]') || input.is('input[type=radio]')) {
    			input
    				.off('change.checked')
    				.on('change.checked', function() {
    					try {
    						return (input.is(':checked')) ? checked.apply(input) : unchecked.apply(input);
    					} catch(e) {};
    				});
    		}
    	});

    	return this;
    };
    ;/**
     * jquery.fn.locate
     * Locate a related child element based on data attribute.
     *
     * Part of the jQuery Utils family:
     * https://github.com/jstonne/jquery.utils
     *
     * Copyright (c) 2014 Jensen Tonne
     * http://jstonne.me
     *
     * Dual licensed under the MIT and GPL licenses:
     * http://www.opensource.org/licenses/mit-license.php
     * http://www.gnu.org/licenses/gpl.html
     *
     */

    $.fn.locate = function(key) {

        var prefix = "data";

        $.each(this[0].attributes, function(i, attr){
            if (attr.specified && attr.value==="$") {
                prefix = attr.name;
                return false;
            }
        });

        return this.find("[" + prefix + "-" + key.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase() + "]");
    };
    ;/**
     * jquery.fn.noscroll
     * Disable scrollbar on elements
     * with the ability to restore it.
     *
     * Part of the jQuery Utils family:
     * https://github.com/jstonne/jquery.utils
     *
     * Copyright (c) 2014 Jensen Tonne
     * http://jstonne.me
     *
     * Dual licensed under the MIT and GPL licenses:
     * http://www.opensource.org/licenses/mit-license.php
     * http://www.gnu.org/licenses/gpl.html
     *
     */

    (function(){

        var props = ["overflow", "overflow-x", "overflow-y"];

        $.fn.noscroll = function(lock) {

            if (lock===undefined) lock = true;

            return this.each(function(){

                var el = $(this),
                    overflow = el.data("noscroll");

                // No original overflow values was stored before
                if (!overflow && lock) {

                    // Get the original overflow values
                    overflow = {};
                    $.each(props, function(i, prop){
                        overflow[prop] = el.css(prop);
                    });

                    // Store original values
                    el.data("noscroll", overflow);
                }

                if (lock) {
                    $.each(props, function(i, prop){
                        el.css(prop, "hidden");
                    });
                } else {
                    overflow && el.css(overflow);
                }
            });
        };

    })();
    ;/**
    * Copyright 2012, Digital Fusion
    * Licensed under the MIT license.
    * http://teamdf.com/jquery-plugins/license/
    *
    * @author Sam Sehnert
    * @desc A small plugin that checks whether elements are within
    * the user visible viewport of a web browser.
    * only accounts for vertical position, not horizontal.
    */

    $.fn.visible = function(partial) {

    	var $t = $(this),
    		$w = $(window);

    	if ($t.length < 1) return;

    	var viewTop      = $w.scrollTop(),
    		viewBottom   = viewTop + $w.height(),
    		_top         = $t.offset().top,
    		_bottom      = _top + $t.height(),
    		compareTop    = partial === true ? _bottom : _top,
    		compareBottom = partial === true ? _top : _bottom;

    	return ((compareBottom <= viewBottom) && (compareTop >= viewTop));
    };;/**
     * jquery.fn.where
     * Filter jQuery elements by data attributes.
     *
     * Part of the jQuery Utils family:
     * https://github.com/jstonne/jquery.utils
     *
     * Copyright (c) 2014 Jensen Tonne
     * http://jstonne.me
     *
     * Dual licensed under the MIT and GPL licenses:
     * http://www.opensource.org/licenses/mit-license.php
     * http://www.gnu.org/licenses/gpl.html
     *
     */

    $.fn.where = $.fn.filterBy = function(key, val, operator) {

    	var operator = operator || "=",
    		selector = "[data-" + key.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase() + operator + val + "]";

    	return this.filter(selector);
    };
    ;/**
     * jquery.formManip
     * Utilities to manipulate form elements.
     *
     * Part of the jQuery Utils family:
     * https://github.com/jstonne/jquery.utils
     *
     * Copyright (c) 2014 Jensen Tonne
     * http://jstonne.me
     *
     * Dual licensed under the MIT and GPL licenses:
     * http://www.opensource.org/licenses/mit-license.php
     * http://www.gnu.org/licenses/gpl.html
     *
     */

    // For checkboxes and radio buttons
    $.fn.checked = function(checked, unchecked) {

        // Return checked value if no arguments are given;
        if (arguments.length < 1)
            return this.is(':checked');

        this.each(function(i) {

            var input = $(this);

            if (typeof checked == "boolean") {
                input.attr('checked', checked).trigger('change');
                return;
            }

            if (input.is('input[type=checkbox]') || input.is('input[type=radio]')) {
                input
                    .off('change.checked')
                    .on('change.checked', function() {
                        try {
                            return (input.is(':checked')) ? checked.apply(input) : unchecked.apply(input);
                        } catch(e) {};
                    });
            }
        });

        return this;
    };

    // For select boxes
    $.fn.selectAll = function() {
        return this.each(function(){this.select()});
    };

    $.fn.unselect = function() {
        return this.each(function(){
            var input = this,
                value = input.value;
                input.value += " ";
                input.value = value;
        });
    };;/**
     * jquery.formSerializers
     * Serializes form values to Object or JSON.
     * Utilities to manipulate html content.
     *
     * Part of the jQuery Utils family:
     * https://github.com/jstonne/jquery.utils
     *
     * Copyright (c) 2014 Jensen Tonne
     * http://jstonne.me
     *
     * Dual licensed under the MIT and GPL licenses:
     * http://www.opensource.org/licenses/mit-license.php
     * http://www.gnu.org/licenses/gpl.html
     *
     */

    $.fn.toObject = $.fn.serializeObject = function() {

    	var obj = {};

    	$.each($(this).serializeArray(), function(i, prop) {
    		if (obj.hasOwnProperty(prop.name)) {
    			// Convert it into an array
    			if (!$.isArray(obj[prop.name])) {
    				obj[prop.name] = [obj[prop.name]];
    			}
    			obj[prop.name].push(prop.value);
    		} else {
    			obj[prop.name] = prop.value;
    		}
    	});

    	return obj;
    };

    $.fn.toJSON = $.fn.serializeJSON = function() {

    	return JSON.stringify($(this).serializeObject());
    };
    ;/**
     * jquery.htmlManip
     * Utilities to manipulate html content.
     *
     * Part of the jQuery Utils family:
     * https://github.com/jstonne/jquery.utils
     *
     * Copyright (c) 2014 Jensen Tonne
     * http://jstonne.me
     *
     * Dual licensed under the MIT and GPL licenses:
     * http://www.opensource.org/licenses/mit-license.php
     * http://www.gnu.org/licenses/gpl.html
     *
     */

    $.sanitizeHTML = function(html) {
        return $($.parseHTML(html, document, true)).toHTML();
    };

    // This also encodes html entities.
    $.toHTML = function(str) {
        return $("<div>").html(str).html();
    };

    $.fn.toHTML = function() {
        return $.toHTML(this.clone());
    };

    // Based on http://stackoverflow.com/questions/1231770/innerhtml-removes-attribute-quotes-in-internet-explorer
    $.toXHTML = function(obj, maintainUppercaseTag) {

        var zz = obj.innerHTML ? String(obj.innerHTML) : obj,
            z  = zz.match(/(<.+[^>])/g);

        if (z) {
            for (var i=0; i<z.length; (i=i+1)) {

                var y,
                    zSaved = z[i],
                    attrRE = /\=[a-zA-Z\.\:\[\]_\(\)\&\$\%#\@\!0-9\/]+[?\s+|?>]/g;

                z[i] =
                    z[i].replace(/([<|<\/].+?\w+).+[^>]/, function(a){
                        return a;
                    });

                y = z[i].match(attrRE);

                if (y) {
                    var j = 0,
                        len = y.length;

                    while (j < len) {

                        var replaceRE = /(\=)([a-zA-Z\.\:\[\]_\(\)\&\$\%#\@\!0-9\/]+)?([\s+|?>])/g,
                            replacer = function() {
                                var args = Array.prototype.slice.call(arguments);
                                return '="' + (maintainUppercaseTag ? args[2] : args[2].toLowerCase()) + '"' + args[3];
                            };

                        z[i] = z[i].replace(y[j], y[j].replace(replaceRE,replacer));
                        j += 1;
                    }
                }

                zz = zz.replace(zSaved,z[i]);
            }
        }

        return zz;
    };

    $.fn.xhtml = function() {
        return $.IE ? $.toXHTML(this[0]) : this.html();
    };

    /**
     * jquery.buildHTML
     * Converts html string into jQuery element where
     * script tags within it gets removed after it is
     * inserted into the DOM.
     *
     * Using $.buildHTML(html) over $(html) also circumvents
     * CloudFlare from modifying the execution behaviour of
     * script elements.
     */

    $.buildHTML = function(html, keepScripts) {

        // If a jquery element was passed in, return as it is.
        if (html instanceof $) return html;

        var doc = document;

        // If CloudFlare exists, use document from iframe
        // because CloudFlare Rocketscript overrides native methods.
        if (window["CloudFlare"]) {

            var iframe = $.buildHTML.iframe;

            // If iframe wasn't created, or iframe was removed or detached,
            // create the iframe element again;
            if (!iframe || !iframe.contentDocument) {

                // Create iframe
                var iframe =
                    $.buildHTML.iframe =
                    document.createElement("iframe");

                // Hide iframe
                iframe.style.display = "none";

                // Append iframe to body
                document.body.appendChild(iframe);
            }

            doc = iframe.contentDocument;
        }

        // Trim out any whitespace so no unusable text nodes are introduced.
        var html = $.trim(html),

            // Build html fragment while keeping a separate reference to the script
            scripts = [],
            fragment = $.buildFragment([html], doc, scripts),

            // Convert childNodes into a proper array
            nodes = $.merge([], fragment.childNodes);

        // If we want to remove the script after
        // it is appended to the DOM & executed
        if (!keepScripts && scripts.length > 0) {

            // Create script remover
            var script = doc.createElement("script");
                // This is wrapped in try..catch because Cloudflare's
                // proxy node executes this twice for some reason.
                // The second time this executes, the callback has been removed,
                // so let it fail silently.
                script.text = "try{" + $.callback(function(){$(scripts).remove();}) + "();}catch(e){}";

            // Go through nodes in reverse
            var i = nodes.length-1, node, inserted;

            while (node = nodes[i--]) {

                // If a script node is found first, we'll just append
                // script remover next to it to ensure this last script
                // executes before any script removal happens.
                if (node.nodeName==="SCRIPT") {
                    inserted = nodes.push(script);
                } else if (node.nodeType===1) {
                    inserted = node.appendChild(script);
                }

                if (inserted) break;
            }

            // If script remover was not inserted,
            // then just add it to the array of nodes
            if (!inserted) nodes.push(script);

            // Add script remover itself to the
            // array of scripts to be removed.
            scripts.push(script);
        }

        // Convert nodes into jquery instance and return
        return $(nodes);
    };;/**
     * jquery.intersects
     * jquery.fn.intersectsWith
     *
     * Determines if an area intersects with another area.
     *
     * Part of the jQuery Utils family:
     * https://github.com/jstonne/jquery.utils
     *
     * Copyright (c) 2014 Jensen Tonne
     * http://jstonne.me
     *
     * Dual licensed under the MIT and GPL licenses:
     * http://www.opensource.org/licenses/mit-license.php
     * http://www.gnu.org/licenses/gpl.html
     *
     */

    $.intersects = function(a, b) {

    	if ($.isArray(b)) {
    	   b = {top: b.y, left: b.x, bottom: b.y, right: b.x}
    	}

    	return (
    	   b.left <= a.right  &&
    	   a.left <= b.right  &&
    	   b.top  <= a.bottom &&
    	   a.top  <= b.bottom
    	);
    };

    $.fn.intersectsWith = function(top, left, width, height) {

    	// TODO: intersectsWith(element)

    	var offset = this.offset(),

    	   reference = {
    	        top   : offset.top,
    	        left  : offset.left,
    	        bottom: offset.top  + (sourceHeight = this.height()),
    	        right : offset.left + (sourceWidth  = this.width()),
    	        width : sourceWidth,
    	        height: sourceHeight
    	   },

    	   subject = {
    	        top   : top,
    	        left  : left,
    	        bottom: top  + (height || (height = 0)),
    	        right : left + (width  || (width  = 0)),
    	        width : width,
    	        height: height
    	   };

    	return ($.intersects(reference, subject)) ? {reference: reference, subject: subject} : false;
    };;/**
     * jquery.isDeferred
     * Test if an object is a jQuery Deferred object.
     *
     * Part of the jQuery Utils family:
     * https://github.com/jstonne/jquery.utils
     *
     * Copyright (c) 2014 Jensen Tonne
     * http://jstonne.me
     *
     * Dual licensed under the MIT and GPL licenses:
     * http://www.opensource.org/licenses/mit-license.php
     * http://www.gnu.org/licenses/gpl.html
     *
     */

    $.isDeferred = function(obj) {
    	return obj && $.isFunction(obj.always);
    };
    ;/**
     * jquery.number
     * Utilities to deal with numbers.
     *
     * Part of the jQuery Utils family:
     * https://github.com/jstonne/jquery.utils
     *
     * Copyright (c) 2014 Jensen Tonne
     * http://jstonne.me
     *
     * Dual licensed under the MIT and GPL licenses:
     * http://www.opensource.org/licenses/mit-license.php
     * http://www.gnu.org/licenses/gpl.html
     *
     */

    $.isNumeric = function(n) {
    	// http://stackoverflow.com/questions/18082/validate-numbers-in-javascript-isnumeric
    	return !isNaN(parseFloat(n)) && isFinite(n);
    };

    $.rotateNumber = function(n, min, max, offset) {

    	if (offset===undefined) {
    		offset = 0;
    	}

    	n += offset;

    	if (n < min) {
    		n += max + 1;
    	} else if (n > max) {
    		n -= max + 1;
    	}

    	return n;
    };;/**
     * jquery.regExpEscape
     * Makes string regex safe.
     * http://stackoverflow.com/questions/2593637/how-to-escape-regular-expression-in-javascript
     */

    $.regExpEscape = function(str) {
        return str.replace(/([-()\[\]{}+?*.$\^|,:#<!\\])/g, '\\$1').replace(/\x08/g, '\\x08');
    }
    ;/**
     * jquery.remap
     * Utility for remapping properties of an object selectively from another object.
     *
     * Part of the jQuery Utils family:
     * https://github.com/jstonne/jquery.utils
     *
     * Copyright (c) 2014 Jensen Tonne
     * http://jstonne.me
     *
     * Dual licensed under the MIT and GPL licenses:
     * http://www.opensource.org/licenses/mit-license.php
     * http://www.gnu.org/licenses/gpl.html
     *
     */

    $.remap = function(to, from, props) {
    	$.each(props, function(i, prop){
    		to[prop] = from[prop];
    	});
    	return obj;
    };
    ;


    /**
     * jquery.throttledAjax
     * jQuery AJAX with throttling.
     *
     * Requires jquery.Threads.
     *
     * Part of the jQuery Utils family:
     * https://github.com/jstonne/jquery.utils
     *
     * Copyright (c) 2014 Jensen Tonne
     * http://jstonne.me
     *
     * Dual licensed under the MIT and GPL licenses:
     * http://www.opensource.org/licenses/mit-license.php
     * http://www.gnu.org/licenses/gpl.html
     *
     */

    (function(){

    var self = $.Ajax = function(options) {

        // Start ajax manually
        options.autostart = false;

    	var ajax = $.ajax(options);

        self.queue
            .addDeferred(function(queue){
                // Start ajax now
                ajax.send();

        		// Mark this queue as resolved
        		setTimeout(queue.resolve, self.interval);
        	});

    	return ajax;
    }

    self.queue    = $.Threads({threadLimit: 1});
    self.interval = 1200;

    // Do not throttle ajax calls on Joomla 3.2 and above.
    // var version = $.joomla.version.split("."),
    //     majorVersion = version[0],
    //     minorVersion = version[1];

    // if (majorVersion >= 3 && minorVersion >= 2) {
        self.interval = 0;
    // }

    })();;/*!


     * jquery.transitionClass.
     * jQuery functions to invoke classnames that has CSS3 transitions.
     *
     * Copyright (c) 2014 Jensen Tonne
     * http://www.jstonne.com
     *
     * Dual licensed under the MIT and GPL licenses:
     * http://www.opensource.org/licenses/mit-license.php
     * http://www.gnu.org/licenses/gpl.html
     *
     */

    (function(){

    // addTransitoryClass
    $.fn.addTransitoryClass = function(classname, duration) {
        var elem = this.addClass(classname);
        setTimeout(function(){elem.removeClass(classname)}, duration || 1);
        return this;
    };

    // addClassAfter
    // removeClassAfter
    var classAfter = function(operation, classname, timer) {
        var elem = this;
        setTimeout(function(){elem[operation+"Class"](classname)}, timer || 50);
        return this;
    };

    $.fn.addClassAfter = function(classname, timer) {
        return classAfter.call(this, "add", classname, timer);
    };

    $.fn.removeClassAfter = function(classname, timer) {
        return classAfter.call(this, "remove", classname, timer);
    };

    // addTransitionClass
    // removeTransitionClass
    var transitionClass = function(toggle, classname, duration, callback) {
        var suffix = toggle ? "-in" : "-out";
        this.addTransitoryClass(classname.replace(/ /g, suffix + " ") + suffix, duration || 1000)
            [(toggle ? "add" : "remove") + "ClassAfter"](classname);
        callback && setTimeout(callback, duration);
        return this;
    };

    $.fn.addTransitionClass = function(classname, duration, callback) {
        return transitionClass.call(this, true, classname, duration, callback);
    };

    $.fn.removeTransitionClass = function(classname, duration, callback) {
        return transitionClass.call(this, false, classname, duration, callback);
    };

    })();;/**
     * jquery.trimSeparators
     * Trims whitespace and separators.
     *
     * Turns this: ",df        ,,,  ,,,abc, sdasd sdfsdf    ,   asdsad, ,, , "
     * into this : "df,abc,sdasd sdfsdf,asdsad"
     *
     * Requires jquery.distinct
     *
     * Part of the jQuery Utils family:
     * https://github.com/jstonne/jquery.utils
     *
     * Copyright (c) 2014 Jensen Tonne
     * http://jstonne.me
     *
     * Dual licensed under the MIT and GPL licenses:
     * http://www.opensource.org/licenses/mit-license.php
     * http://www.gnu.org/licenses/gpl.html
     *
     */

    $.trimSeparators = function(keyword, separator, removeDuplicates) {

    	var s = separator;

    	keyword = keyword
    		.replace(new RegExp('^['+s+'\\s]+|['+s+',\\s]+$','g'), '') // /^[,\s]+|[,\s]+$/g
    		.replace(new RegExp(s+'['+s+'\\s]*'+s,'g'), s)             // /,[,\s]*,/g
    		.replace(new RegExp('[\\s]+'+s,'g'), s)                    // /[\s]+,/g
    		.replace(new RegExp(s+'[\\s]+','g'), s);                   // /,[\s]+/g

    	if (removeDuplicates) {
    		keyword = $.distinct(keyword.split(s)).join(s);
    	}

    	return keyword;
    };
    ;/**
     * jquery.uid
     * Generates a unique id with optional prefix/suffix.
     *
     * Part of the jQuery Utils family:
     * https://github.com/jstonne/jquery.utils
     *
     * Copyright (c) 2014 Jensen Tonne
     * http://jstonne.me
     *
     * Dual licensed under the MIT and GPL licenses:
     * http://www.opensource.org/licenses/mit-license.php
     * http://www.gnu.org/licenses/gpl.html
     *
     */

    $.uid = function(p,s) {
    	return ((p) ? p : '') + Math.random().toString().replace('.','') + ((s) ? s : '');
    };

    // Several of the methods in this plugin use code adapated from Prototype
    //  Prototype JavaScript framework, version 1.6.0.1
    //  (c) 2005-2007 Sam Stephenson
    var regs = {
        undHash: /_|-/,
        colons: /::/,
        words: /([A-Z]+)([A-Z][a-z])/g,
        lowUp: /([a-z\d])([A-Z])/g,
        dash: /([a-z\d])([A-Z])/g,
        replacer: /\{([^\}]+)\}/g,
        dot: /\./
    },
        // gets the nextPart property from current
        // add - if true and nextPart doesnt exist, create it as an empty object
        getNext = function(current, nextPart, add){
            return current[nextPart] !== undefined ? current[nextPart] : ( add && (current[nextPart] = {}) );
        },
        // returns true if the object can have properties (no nulls)
        isContainer = function(current){
            var type = typeof current;
            return current && ( type == 'function' || type == 'object' );
        },
        // a reference
        getObject,
        /**
         * @class jQuery.String
         * @parent jquerymx.lang
         *
         * A collection of useful string helpers. Available helpers are:
         * <ul>
         *   <li>[jQuery.String.capitalize|capitalize]: Capitalizes a string (some_string &raquo; Some_string)</li>
         *   <li>[jQuery.String.camelize|camelize]: Capitalizes a string from something undercored
         *       (some_string &raquo; someString, some-string &raquo; someString)</li>
         *   <li>[jQuery.String.classize|classize]: Like [jQuery.String.camelize|camelize],
         *       but the first part is also capitalized (some_string &raquo; SomeString)</li>
         *   <li>[jQuery.String.niceName|niceName]: Like [jQuery.String.classize|classize], but a space separates each 'word' (some_string &raquo; Some String)</li>
         *   <li>[jQuery.String.underscore|underscore]: Underscores a string (SomeString &raquo; some_string)</li>
         *   <li>[jQuery.String.sub|sub]: Returns a string with {param} replaced values from data.
         *       <code><pre>
         *       $.String.sub("foo {bar}",{bar: "far"})
         *       //-> "foo far"</pre></code>
         *   </li>
         * </ul>
         *
         */

        str = $.String = $.extend($.String || {} , {


            /**
             * @function getObject
             * Gets an object from a string.  It can also modify objects on the
             * 'object path' by removing or adding properties.
             *
             *     Foo = {Bar: {Zar: {"Ted"}}}
             *     $.String.getObject("Foo.Bar.Zar") //-> "Ted"
             *
             * @param {String} name the name of the object to look for
             * @param {Array} [roots] an array of root objects to look for the
             *   name.  If roots is not provided, the window is used.
             * @param {Boolean} [add] true to add missing objects to
             *  the path. false to remove found properties. undefined to
             *  not modify the root object
             * @return {Object} The object.
             */
            getObject : getObject = function( name, roots, add ) {

                // the parts of the name we are looking up
                // ['App','Models','Recipe']
                var parts = name ? name.split(regs.dot) : [],
                    length =  parts.length,
                    current,
                    ret,
                    i,
                    r = 0,
                    type;

                // make sure roots is an array
                roots = $.isArray(roots) ? roots : [roots || window];

                if(length == 0){
                    return roots[0];
                }
                // for each root, mark it as current
                while( current = roots[r++] ) {
                    // walk current to the 2nd to last object
                    // or until there is not a container
                    for (i =0; i < length - 1 && isContainer(current); i++ ) {
                        current = getNext(current, parts[i], add);
                    }
                    // if we can get a property from the 2nd to last object
                    if( isContainer(current) ) {

                        // get (and possibly set) the property
                        ret = getNext(current, parts[i], add);

                        // if there is a value, we exit
                        if( ret !== undefined ) {
                            // if add is false, delete the property
                            if ( add === false ) {
                                delete current[parts[i]];
                            }
                            return ret;

                        }
                    }
                }
            },
            /**
             * Capitalizes a string
             * @param {String} s the string.
             * @return {String} a string with the first character capitalized.
             */
            capitalize: function( s, cache ) {
                return s.charAt(0).toUpperCase() + s.substr(1);
            },
            /**
             * Capitalizes a string from something undercored. Examples:
             * @codestart
             * jQuery.String.camelize("one_two") //-> "oneTwo"
             * "three-four".camelize() //-> threeFour
             * @codeend
             * @param {String} s
             * @return {String} a the camelized string
             */
            camelize: function( s ) {
                s = str.classize(s);
                return s.charAt(0).toLowerCase() + s.substr(1);
            },
            /**
             * Like [jQuery.String.camelize|camelize], but the first part is also capitalized
             * @param {String} s
             * @return {String} the classized string
             */
            classize: function( s , join) {
                var parts = s.split(regs.undHash),
                    i = 0;
                for (; i < parts.length; i++ ) {
                    parts[i] = str.capitalize(parts[i]);
                }

                return parts.join(join || '');
            },
            /**
             * Like [jQuery.String.classize|classize], but a space separates each 'word'
             * @codestart
             * jQuery.String.niceName("one_two") //-> "One Two"
             * @codeend
             * @param {String} s
             * @return {String} the niceName
             */
            niceName: function( s ) {
                return str.classize(s,' ');
            },

            /**
             * Underscores a string.
             * @codestart
             * jQuery.String.underscore("OneTwo") //-> "one_two"
             * @codeend
             * @param {String} s
             * @return {String} the underscored string
             */
            underscore: function( s ) {
                return s.replace(regs.colons, '/').replace(regs.words, '$1_$2').replace(regs.lowUp, '$1_$2').replace(regs.dash, '_').toLowerCase();
            },
            /**
             * Returns a string with {param} replaced values from data.
             *
             *     $.String.sub("foo {bar}",{bar: "far"})
             *     //-> "foo far"
             *
             * @param {String} s The string to replace
             * @param {Object} data The data to be used to look for properties.  If it's an array, multiple
             * objects can be used.
             * @param {Boolean} [remove] if a match is found, remove the property from the object
             */
            sub: function( s, data, remove ) {
                var obs = [];
                obs.push(s.replace(regs.replacer, function( whole, inside ) {

                    // !-- FOUNDRY HACK --! //
                    // Prefer {foobar} over foobar

                    //convert inside to type
                    var ob = getObject(whole, data, typeof remove == 'boolean' ? !remove : remove) ||
                             getObject(inside, data, typeof remove == 'boolean' ? !remove : remove),
                        type = typeof ob;

                    if ((type === 'object' || type === 'function') && type !== null) {
                        obs.push(ob);
                        return "";
                    } else {
                        return ""+ob;
                    }
                }));
                return obs.length <= 1 ? obs[0] : obs;
            },
            _regs : regs
        });

    // !-- FOUNDRY HACK --! //
    // Expose string methods to $.
    $.extend($, str);

    return $;
});