FD31.plugin("utils", function($) {

$.IE = (function(){

    if (navigator.appVersion.indexOf("MSIE 10") != -1) return 10;
    
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

}());/**
* jquery.uid
* Generates a unique id with optional prefix/suffix.
* https://github.com/jstonne/jquery.uid
*
* Copyright (c) 2012 Jensen Tonne
* www.jstonne.com
*
* Dual licensed under the MIT and GPL licenses:
* http://www.opensource.org/licenses/mit-license.php
* http://www.gnu.org/licenses/gpl.html
*
*/

$.uid = function(p,s) {
	return ((p) ? p : '') + Math.random().toString().replace('.','') + ((s) ? s : '');
};
/**
* jquery.isDeferred
* Tests if an object is a jQuery Deferred object.
* https://github.com/jstonne/jquery.isDeferred
*
* Copyright (c) 2012 Jensen Tonne
* www.jstonne.com
*
* Dual licensed under the MIT and GPL licenses:
* http://www.opensource.org/licenses/mit-license.php
* http://www.gnu.org/licenses/gpl.html
*
*/

$.isDeferred = function(obj) {
	return obj && $.isFunction(obj.always);
};
/**
 * jquery.distinct
 * Enhanced version of jQuery.unique that also removes
 * removes object/string/integer duplicates within an array.
 * https://github.com/jstonne/jquery.distinct
 *
 * Copyright (c) 2012 Jensen Tonne
 * www.jstonne.com
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
/**
* jquery.trimSeparators
* Trims whitespace and separators.
* https://github.com/jstonne/jquery.trimSeparators
*
* Turns this: ",df        ,,,  ,,,abc, sdasd sdfsdf    ,   asdsad, ,, , "
* into this : "df,abc,sdasd sdfsdf,asdsad"
*
* Requires jquery.distinct
* https://github.com/jstonne/jquery.distinct
*
* Copyright (c) 2012 Jensen Tonne
* www.jstonne.com
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
/*!
 * jquery.number.
 * Helpers for numbers.
 *
 * Copyright (c) 2012 Jensen Tonne
 * www.jstonne.com
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

$.Number = {
	rotate: function(n, min, max, offset) {
		if (offset===undefined)
			offset = 0;

		n += offset;
		if (n < min) {
			n += max + 1;
		} else if (n > max) {
			n -= max + 1;
		}

		return n;
	}
};
/**
* jquery.stretchToFit
* Stretch any element to fit its parent container.
* https://github.com/jstonne/jquery.stretchToFit
*
* Copyright (c) 2012 Jensen Tonne
* www.jstonne.com
*
* Dual licensed under the MIT and GPL licenses:
* http://www.opensource.org/licenses/mit-license.php
* http://www.gnu.org/licenses/gpl.html
*
*/

$.fn.stretchToFit = function() {
	return $.each(this, function()
	{
		var $this = $(this);

		$this
			.css('width', '100%')
			.css('width', $this.width() * 2 - $this.outerWidth(true) - parseInt($this.css('borderLeftWidth')) - parseInt($this.css('borderRightWidth')));
	});
};

/**
* jquery.fn.serializeJSON
* Serializes form values to JSON.
* https://github.com/jstonne/jquery.fn.serializeJSON
*
* Copyright (c) 2012 Jensen Tonne
* www.jstonne.com
*
* Dual licensed under the MIT and GPL licenses:
* http://www.opensource.org/licenses/mit-license.php
* http://www.gnu.org/licenses/gpl.html
*
*/

$.fn.serializeObject = function() {

	var obj = {};

	$.each($(this).serializeArray(), function(i, prop)
	{
		if (obj.hasOwnProperty(prop.name))
		{
			// Convert it into an array
			if (!$.isArray(obj[prop.name]))
			{
				obj[prop.name] = [obj[prop.name]];
			}

			obj[prop.name].push(prop.value);

		} else {

			obj[prop.name] = prop.value;

		}
	});

	return obj;
};

$.fn.serializeJSON = function() {

	return JSON.stringify($(this).serializeObject());
}
/**
* jquery.toHTML
* Decode html entities. Get HTML of element.
*
* https://github.com/jstonne/jquery.toHTML
*
* Copyright (c) 2012 Jensen Tonne
* www.jstonne.com
*
* Dual licensed under the MIT and GPL licenses:
* http://www.opensource.org/licenses/mit-license.php
* http://www.gnu.org/licenses/gpl.html
*
*/

$.toHTML = function(str) {
	return $("<div>").html(str).html();
}

$.fn.toHTML = function() {
	return $.toHTML(this.clone());
};/**
* jquery.Bloop
* Binary loop helper.
* https://github.com/jstonne/jquery.Bloop
*
* Copyright (c) 2012 Jensen Tonne & Jason Rey
* www.jstonne.com
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
/**
* jquery.remap
* Utility for remapping properties of an object selectively from another object.
* https://github.com/jstonne/jquery.remap
*
* Copyright (c) 2012 Jensen Tonne
* www.jstonne.com
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
/**
* jquery.deletes
* A mass delete version of the native javascript delete method.
* https://github.com/jstonne/jquery.deletes
*
* Copyright (c) 2012 Jensen Tonne
* www.jstonne.com
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
/**
 * jquery.Threads
 * A manager that controls threads a.k.a. execution of function simultaneously.
 * https://github.com/jstonne/jquery.Threads
 *
 * Copyright (c) 2012 Jensen Tonne & Jason Rey
 * www.jstonne.com
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
					catch(e) { console.error(e); }

					!thread.deferred && self.next();
				}

			}, self.threadDelay);
		}
	});

	$.Threads = function(options) {

		return new Threads(options);
	};

})();
/**
 * jquery.Enqueue
 * Execute only the last added callback.
 * https://github.com/jstonne/jquery.Enqueue
 *
 * Copyright (c) 2012 Jensen Tonne & Jason Rey
 * www.jstonne.com
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

(function() {

	var isFunction = $.isFunction;

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

		if ($.isFunction(fn)) self.fn = fn;

		var func = $.proxy(self.queue, self);

		func.reset = function() {
			self.lastId = 0;
		};

		return func;
	};
})();
/**
* jquery.eventable
* Extend objects with events.
* https://github.com/jstonne/jquery.eventable
*
* Copyright (c) 2012 Jensen Tonne
* www.jstonne.com
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
/*!
 * jquery.Chunk.
 * Utility to handle large arrays by processing them in smaller manageable chunks.
 *
 * Copyright (c) 2012 Jensen Tonne
 * www.jstonne.com
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
/**
* jquery.disabled
* Checks if element is disabled and adds a disable class
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
/**
* jquery.throttledAjax
* jQuery AJAX with throttling.
* https://github.com/jstonne/jquery.throttledAjax
*
* Requires jquery.Threads
* https://github.com/jstonne/jquery.Threads
*
* Copyright (c) 2012 Jensen Tonne
* www.jstonne.com
*
* Dual licensed under the MIT and GPL licenses:
* http://www.opensource.org/licenses/mit-license.php
* http://www.gnu.org/licenses/gpl.html
*
*/

(function(){

var self = $.Ajax = function(options) {

	var request = $.Deferred(),
		args = arguments;

	// Allow others to decorate the request object
	$.isPlainObject(options) && $.isFunction(options.beforeCreate) && options.beforeCreate(request);

	self.queue.addDeferred(function(queue){

		request.xhr =
			$.ajax.apply(null, args)
				.pipe(
					request.resolve,
					request.reject,
					request.notify
				);

		// Mark this queue as resolved
		setTimeout(queue.resolve, self.requestInterval);

	});

	return request;
}

self.queue = $.Threads({threadLimit: 1});

self.requestInterval = 1200;

})();
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
};$.fn.visible = function(partial){

	var $t	= $(this),
		$w	= $(window);

	if ($t.length < 1) return;
	
	var viewTop	= $w.scrollTop(),
	viewBottom	= viewTop + $w.height(),
	_top		= $t.offset().top,
	_bottom		= _top + $t.height(),
	compareTop	= partial === true ? _bottom : _top,
	compareBottom	= partial === true ? _top : _bottom;

	return ((compareBottom <= viewBottom) && (compareTop >= viewTop));
};

$.fn.selectAll = function() {
	return this.each(function(){ this.select() });
};

$.fn.unselect = function() {
	return this.each(function(){
		var input = this,
			value = input.value;
			input.value += " ";
			input.value = value;
	});
};$.fn.addTransitionClass = function(classname, duration){

	var $el = this.addClass(classname);

	setTimeout(function(){
		$el.removeClass(classname);
	}, duration || 0);

	return this;
};

$.fn.switchClass = function(classname, delimiter){

	var delimiter = delimiter || "-",
		prefix = classname.split(delimiter)[0] + delimiter,
		length = prefix.length;

	return this.each(function(){

		var $el = $(this),
			classnames =
				$.map($el.attr("class").split(' '), function(classname){
					return (classname.slice(0, length)==prefix || classname=="") ? null : classname;
				});
			classnames.push(classname);

		$el.attr("class", classnames.join(" "));
	});
};
$.sanitizeHTML = function(html) {
	return $($.parseHTML(html, document, true)).toHTML();
};$.buildHTML = function(html, keepScripts) {

	// If a jquery element was passed in, return as it is.
	if (html instanceof $) return html;

	// Trim out any whitespace so no unusable text nodes are introduced.
	var html = $.trim(html),

		// Build html fragment while keeping a separate reference to the script
		scripts = [],
		fragment = $.buildFragment([html], document, scripts),

		// Convert childNodes into a proper array
		nodes = $.merge([], fragment.childNodes);

	// If we want to remove the script after
	// it is appended to the DOM & executed
	if (!keepScripts && scripts.length > 0) {

		// Create script remover
		var script = document.createElement("script");
			script.text = $.callback(function(){$(scripts).remove();}) + "();";

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
};$.fn.filterBy = function(key, val, operator) {

	var operator = operator || "=",
		selector = "[data-" + key.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase() + operator + val + "]";

	return this.filter(selector);
}
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
};$.download = function(src) {
    return $("<iframe>").hide().appendTo("body").bind("load", function(){$(this).remove()}).attr("src", src);
};
});