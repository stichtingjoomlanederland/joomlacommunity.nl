FD31.plugin("mvc", function($) {

(function(){
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
		str = $.String = $.extend( $.String || {} , {


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
})();(function(){
	/**
	 * @add jQuery.String
	 */
	$.String.
	/**
	 * Splits a string with a regex correctly cross browser
	 * 
	 *     $.String.rsplit("a.b.c.d", /\./) //-> ['a','b','c','d']
	 * 
	 * @param {String} string The string to split
	 * @param {RegExp} regex A regular expression
	 * @return {Array} An array of strings
	 */
	rsplit = function( string, regex ) {
		var result = regex.exec(string),
			retArr = [],
			first_idx, last_idx;
		while ( result !== null ) {
			first_idx = result.index;
			last_idx = regex.lastIndex;
			if ( first_idx !== 0 ) {
				retArr.push(string.substring(0, first_idx));
				string = string.slice(first_idx);
			}
			retArr.push(result[0]);
			string = string.slice(result[0].length);
			result = regex.exec(string);
		}
		if ( string !== '' ) {
			retArr.push(string);
		}
		return retArr;
	};
})();(function(){
	
	var digitTest = /^\d+$/,
		keyBreaker = /([^\[\]]+)|(\[\])/g,
		plus = /\+/g,
		paramTest = /([^?#]*)(#.*)?$/;
	
	/**
	 * @add jQuery.String
	 */
	$.String = $.extend($.String || {}, { 
		
		/**
		 * @function deparam
		 * 
		 * Takes a string of name value pairs and returns a Object literal that represents those params.
		 * 
		 * @param {String} params a string like <code>"foo=bar&person[age]=3"</code>
		 * @return {Object} A JavaScript Object that represents the params:
		 * 
		 *     {
		 *       foo: "bar",
		 *       person: {
		 *         age: "3"
		 *       }
		 *     }
		 */
		deparam: function(params){
		
			if(! params || ! paramTest.test(params) ) {
				return {};
			} 
		   
		
			var data = {},
				pairs = params.split('&'),
				current;
				
			for(var i=0; i < pairs.length; i++){
				current = data;
				var pair = pairs[i].split('=');
				
				// if we find foo=1+1=2
				if(pair.length != 2) { 
					pair = [pair[0], pair.slice(1).join("=")]
				}
				  
        var key = decodeURIComponent(pair[0].replace(plus, " ")), 
          value = decodeURIComponent(pair[1].replace(plus, " ")),
					parts = key.match(keyBreaker);
		
				for ( var j = 0; j < parts.length - 1; j++ ) {
					var part = parts[j];
					if (!current[part] ) {
						// if what we are pointing to looks like an array
						current[part] = digitTest.test(parts[j+1]) || parts[j+1] == "[]" ? [] : {}
					}
					current = current[part];
				}
				lastPart = parts[parts.length - 1];
				if(lastPart == "[]"){
					current.push(value)
				}else{
					current[lastPart] = value;
				}
			}
			return data;
		}
	});
	
})();(function(){
	/**
	 * @attribute destroyed
	 * @parent specialevents
	 * @download  http://jmvcsite.heroku.com/pluginify?plugins[]=jquery/dom/destroyed/destroyed.js
	 * @test jquery/event/destroyed/qunit.html
	 * Provides a destroyed event on an element.
	 * <p>
	 * The destroyed event is called when the element
	 * is removed as a result of jQuery DOM manipulators like remove, html,
	 * replaceWith, etc. Destroyed events do not bubble, so make sure you don't use live or delegate with destroyed
	 * events.
	 * </p>
	 * <h2>Quick Example</h2>
	 * @codestart
	 * $(".foo").bind("destroyed", function(){
	 *    //clean up code
	 * })
	 * @codeend
	 * <h2>Quick Demo</h2>
	 * @demo jquery/event/destroyed/destroyed.html
	 * <h2>More Involved Demo</h2>
	 * @demo jquery/event/destroyed/destroyed_menu.html
	 */

	var oldClean = $.cleanData;

	$.cleanData = function( elems ) {
		for ( var i = 0, elem;
		(elem = elems[i]) !== undefined; i++ ) {
			$(elem).triggerHandler("destroyed");
			//$.event.remove( elem, 'destroyed' );
		}
		oldClean(elems);
	};

})();(function(){
	/**
	 * @function closest
	 * @parent dom
	 * @plugin jquery/dom/closest
	 * Overwrites closest to allow open > selectors.  This allows controller
	 * actions such as:
	 *
	 *     ">li click" : function( el, ev ) { ... }
	 */
	var oldClosest = $.fn.closest;
	$.fn.closest = function(selectors, context){
		var rooted = {}, res, result, thing, i, j, selector, rootedIsEmpty = true, selector, selectorsArr = selectors;
		if(typeof selectors == "string") selectorsArr = [selectors];

		$.each(selectorsArr, function(i, selector){
		    if(selector.indexOf(">") == 0 ){
				if(selector.indexOf(" ") != -1){
					throw " closest does not work with > followed by spaces!"
				}
				rooted[( selectorsArr[i] = selector.substr(1)  )] = selector;
				if(typeof selectors == "string") selectors = selector.substr(1);
				rootedIsEmpty = false;
			}
		})

		res = oldClosest.call(this, selectors, context);

		if(rootedIsEmpty) return res;
		i =0;
		while(i < res.length){
			result = res[i], selector = result.selector;
			if (rooted[selector] !== undefined) {
				result.selector = rooted[selector];
				rooted[selector] = false;
				if(typeof result.selector !== "string"  || result.elem.parentNode !== context ){
					res.splice(i,1);
						continue;
				}
			}
			i++;
		}
		return res;
	}
})();(function(){
    // break
    /**
     * @function jQuery.cookie
     * @parent dom
     * @plugin jquery/dom/cookie
     * @author Klaus Hartl/klaus.hartl@stilbuero.de
     *
     *  JavaScriptMVC's packaged cookie plugin is written by
     *  Klaus Hartl (stilbuero.de)<br />
	 *  Dual licensed under the MIT and GPL licenses:<br />
	 *  http://www.opensource.org/licenses/mit-license.php<br />
	 *  http://www.gnu.org/licenses/gpl.html
	 *  </p>
	 *  <p>
	 *  Create a cookie with the given name and value and other optional parameters.
	 *  / Get the value of a cookie with the given name.
	 *  </p>
	 *  <h3>Quick Examples</h3>
	 *
	 *  Set the value of a cookie.
	 *
	 *     $.cookie('the_cookie', 'the_value');
	 *
	 *  Create a cookie with all available options.
	 *  @codestart
	 *  $.cookie('the_cookie', 'the_value',
	 *  { expires: 7, path: '/', domain: 'jquery.com', secure: true });
	 *  @codeend
	 *
	 *  Create a session cookie.
	 *  @codestart
	 *  $.cookie('the_cookie', 'the_value');
	 *  @codeend
	 *
	 *  Delete a cookie by passing null as value. Keep in mind that you have to use the same path and domain
	 *  used when the cookie was set.
	 *  @codestart
	 *  $.cookie('the_cookie', null);
	 *  @codeend
	 *
	 *  Get the value of a cookie.
	 *  @codestart
	 *  $.cookie('the_cookie');
	 *  @codeend
	 *
     *
     * @param {String} [name] The name of the cookie.
     * @param {String} [value] The value of the cookie.
     * @param {Object} [options] An object literal containing key/value pairs to provide optional cookie attributes.<br />
     * @param {Number|Date} [expires] Either an integer specifying the expiration date from now on in days or a Date object.
     *                             If a negative value is specified (e.g. a date in the past), the cookie will be deleted.
     *                             If set to null or omitted, the cookie will be a session cookie and will not be retained
     *                             when the the browser exits.<br />
     * @param {String} [path] The value of the path atribute of the cookie (default: path of page that created the cookie).<br />
     * @param {String} [domain] The value of the domain attribute of the cookie (default: domain of page that created the cookie).<br />
     * @param {Boolean} secure If true, the secure attribute of the cookie will be set and the cookie transmission will
     *                        require a secure protocol (like HTTPS).<br />
     * @return {String} the value of the cookie or {undefined} when setting the cookie.
     */
    $.cookie = function(name, value, options) {
        if (typeof value != 'undefined') { // name and value given, set cookie
            options = options ||
            {};
            if (value === null) {
                value = '';
                options.expires = -1;
            }
            if (typeof value == 'object' && jQuery.toJSON) {
                value = jQuery.toJSON(value);
            }
            var expires = '';
            if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
                var date;
                if (typeof options.expires == 'number') {
                    date = new Date();
                    date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
                }
                else {
                    date = options.expires;
                }
                expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
            }
            // CAUTION: Needed to parenthesize options.path and options.domain
            // in the following expressions, otherwise they evaluate to undefined
            // in the packed version for some reason...
            var path = options.path ? '; path=' + (options.path) : '';
            var domain = options.domain ? '; domain=' + (options.domain) : '';
            var secure = options.secure ? '; secure' : '';
            document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
        }
        else { // only name given, get cookie
            var cookieValue = null;
            if (document.cookie && document.cookie != '') {
                var cookies = document.cookie.split(';');
                for (var i = 0; i < cookies.length; i++) {
                    var cookie = jQuery.trim(cookies[i]);
                    // Does this cookie string begin with the name we want?
                    if (cookie.substring(0, name.length + 1) == (name + '=')) {
                        cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                        break;
                    }
                }
            }
            if (jQuery.evalJSON && cookieValue && cookieValue.match(/^\s*\{/)) {
                try {
                    cookieValue = jQuery.evalJSON(cookieValue);
                }
                catch (e) {
                }
            }
            return cookieValue;
        }
    };

})();(function(){

	// =============== HELPERS =================

	    // if we are initializing a new class
	var initializing = false,
		makeArray = $.makeArray,
		isFunction = $.isFunction,
		isArray = $.isArray,
		extend = $.extend,
		getObject = $.String.getObject,
		concatArgs = function(arr, args){
			return arr.concat(makeArray(args));
		},

		// tests if we can get super in .toString()
		fnTest = /xyz/.test(function() {
			xyz;
		}) ? /\b_super\b/ : /.*/,

		// overwrites an object with methods, sets up _super
		//   newProps - new properties
		//   oldProps - where the old properties might be
		//   addTo - what we are adding to
		inheritProps = function( newProps, oldProps, addTo ) {
			addTo = addTo || newProps
			for ( var name in newProps ) {
				// Check if we're overwriting an existing function
				addTo[name] = isFunction(newProps[name]) &&
							  isFunction(oldProps[name]) &&
							  fnTest.test(newProps[name]) ? (function( name, fn ) {
					return function() {
						var tmp = this._super,
							ret;

						// Add a new ._super() method that is the same method
						// but on the super-class
						this._super = oldProps[name];

						// The method only need to be bound temporarily, so we
						// remove it when we're done executing
						ret = fn.apply(this, arguments);
						this._super = tmp;
						return ret;
					};
				})(name, newProps[name]) : newProps[name];
			}
		},
		STR_PROTOTYPE = 'prototype'

	/**
	 * @class jQuery.Class
	 * @plugin jquery/class
	 * @parent jquerymx
	 * @download dist/jquery/jquery.class.js
	 * @test jquery/class/qunit.html
	 * @description Easy inheritance in JavaScript.
	 *
	 * Class provides simulated inheritance in JavaScript. Use clss to bridge the gap between
	 * jQuery's functional programming style and Object Oriented Programming. It
	 * is based off John Resig's [http://ejohn.org/blog/simple-javascript-inheritance/|Simple Class]
	 * Inheritance library.  Besides prototypal inheritance, it includes a few important features:
	 *
	 *   - Static inheritance
	 *   - Introspection
	 *   - Namespaces
	 *   - Setup and initialization methods
	 *   - Easy callback function creation
	 *
	 *
	 * The [mvc.class Get Started with jQueryMX] has a good walkthrough of $.Class.
	 *
	 * ## Static v. Prototype
	 *
	 * Before learning about Class, it's important to
	 * understand the difference between
	 * a class's __static__ and __prototype__ properties.
	 *
	 *     //STATIC
	 *     MyClass.staticProperty  //shared property
	 *
	 *     //PROTOTYPE
	 *     myclass = new MyClass()
	 *     myclass.prototypeMethod() //instance method
	 *
	 * A static (or class) property is on the Class constructor
	 * function itself
	 * and can be thought of being shared by all instances of the
	 * Class. Prototype propertes are available only on instances of the Class.
	 *
	 * ## A Basic Class
	 *
	 * The following creates a Monster class with a
	 * name (for introspection), static, and prototype members.
	 * Every time a monster instance is created, the static
	 * count is incremented.
	 *
	 * @codestart
	 * $.Class('Monster',
	 * /* @static *|
	 * {
	 *   count: 0
	 * },
	 * /* @prototype *|
	 * {
	 *   init: function( name ) {
	 *
	 *     // saves name on the monster instance
	 *     this.name = name;
	 *
	 *     // sets the health
	 *     this.health = 10;
	 *
	 *     // increments count
	 *     this.constructor.count++;
	 *   },
	 *   eat: function( smallChildren ){
	 *     this.health += smallChildren;
	 *   },
	 *   fight: function() {
	 *     this.health -= 2;
	 *   }
	 * });
	 *
	 * hydra = new Monster('hydra');
	 *
	 * dragon = new Monster('dragon');
	 *
	 * hydra.name        // -> hydra
	 * Monster.count     // -> 2
	 * Monster.shortName // -> 'Monster'
	 *
	 * hydra.eat(2);     // health = 12
	 *
	 * dragon.fight();   // health = 8
	 *
	 * @codeend
	 *
	 *
	 * Notice that the prototype <b>init</b> function is called when a new instance of Monster is created.
	 *
	 *
	 * ## Inheritance
	 *
	 * When a class is extended, all static and prototype properties are available on the new class.
	 * If you overwrite a function, you can call the base class's function by calling
	 * <code>this._super</code>.  Lets create a SeaMonster class.  SeaMonsters are less
	 * efficient at eating small children, but more powerful fighters.
	 *
	 *
	 *     Monster("SeaMonster",{
	 *       eat: function( smallChildren ) {
	 *         this._super(smallChildren / 2);
	 *       },
	 *       fight: function() {
	 *         this.health -= 1;
	 *       }
	 *     });
	 *
	 *     lockNess = new SeaMonster('Lock Ness');
	 *     lockNess.eat(4);   //health = 12
	 *     lockNess.fight();  //health = 11
	 *
	 * ### Static property inheritance
	 *
	 * You can also inherit static properties in the same way:
	 *
	 *     $.Class("First",
	 *     {
	 *         staticMethod: function() { return 1;}
	 *     },{})
	 *
	 *     First("Second",{
	 *         staticMethod: function() { return this._super()+1;}
	 *     },{})
	 *
	 *     Second.staticMethod() // -> 2
	 *
	 * ## Namespaces
	 *
	 * Namespaces are a good idea! We encourage you to namespace all of your code.
	 * It makes it possible to drop your code into another app without problems.
	 * Making a namespaced class is easy:
	 *
	 *
	 *     $.Class("MyNamespace.MyClass",{},{});
	 *
	 *     new MyNamespace.MyClass()
	 *
	 *
	 * <h2 id='introspection'>Introspection</h2>
	 *
	 * Often, it's nice to create classes whose name helps determine functionality.  Ruby on
	 * Rails's [http://api.rubyonrails.org/classes/ActiveRecord/Base.html|ActiveRecord] ORM class
	 * is a great example of this.  Unfortunately, JavaScript doesn't have a way of determining
	 * an object's name, so the developer must provide a name.  Class fixes this by taking a String name for the class.
	 *
	 *     $.Class("MyOrg.MyClass",{},{})
	 *     MyOrg.MyClass.shortName //-> 'MyClass'
	 *     MyOrg.MyClass.fullName //->  'MyOrg.MyClass'
	 *
	 * The fullName (with namespaces) and the shortName (without namespaces) are added to the Class's
	 * static properties.
	 *
	 *
	 * ## Setup and initialization methods
	 *
	 * <p>
	 * Class provides static and prototype initialization functions.
	 * These come in two flavors - setup and init.
	 * Setup is called before init and
	 * can be used to 'normalize' init's arguments.
	 * </p>
	 * <div class='whisper'>PRO TIP: Typically, you don't need setup methods in your classes. Use Init instead.
	 * Reserve setup methods for when you need to do complex pre-processing of your class before init is called.
	 *
	 * </div>
	 * @codestart
	 * $.Class("MyClass",
	 * {
	 *   setup: function() {} //static setup
	 *   init: function() {} //static constructor
	 * },
	 * {
	 *   setup: function() {} //prototype setup
	 *   init: function() {} //prototype constructor
	 * })
	 * @codeend
	 *
	 * ### Setup
	 *
	 * Setup functions are called before init functions.  Static setup functions are passed
	 * the base class followed by arguments passed to the extend function.
	 * Prototype static functions are passed the Class constructor
	 * function arguments.
	 *
	 * If a setup function returns an array, that array will be used as the arguments
	 * for the following init method.  This provides setup functions the ability to normalize
	 * arguments passed to the init constructors.  They are also excellent places
	 * to put setup code you want to almost always run.
	 *
	 *
	 * The following is similar to how [jQuery.Controller.prototype.setup]
	 * makes sure init is always called with a jQuery element and merged options
	 * even if it is passed a raw
	 * HTMLElement and no second parameter.
	 *
	 *     $.Class("jQuery.Controller",{
	 *       ...
	 *     },{
	 *       setup: function( el, options ) {
	 *         ...
	 *         return [$(el),
	 *                 $.extend(true,
	 *                    this.Class.defaults,
	 *                    options || {} ) ]
	 *       }
	 *     })
	 *
	 * Typically, you won't need to make or overwrite setup functions.
	 *
	 * ### Init
	 *
	 * Init functions are called after setup functions.
	 * Typically, they receive the same arguments
	 * as their preceding setup function.  The Foo class's <code>init</code> method
	 * gets called in the following example:
	 *
	 *     $.Class("Foo", {
	 *       init: function( arg1, arg2, arg3 ) {
	 *         this.sum = arg1+arg2+arg3;
	 *       }
	 *     })
	 *     var foo = new Foo(1,2,3);
	 *     foo.sum //-> 6
	 *
	 * ## Proxies
	 *
	 * Similar to jQuery's proxy method, Class provides a
	 * [jQuery.Class.static.proxy proxy]
	 * function that returns a callback to a method that will always
	 * have
	 * <code>this</code> set to the class or instance of the class.
	 *
	 *
	 * The following example uses this.proxy to make sure
	 * <code>this.name</code> is available in <code>show</code>.
	 *
	 *     $.Class("Todo",{
	 *       init: function( name ) {
	 *       	this.name = name
	 *       },
	 *       get: function() {
	 *         $.get("/stuff",this.proxy('show'))
	 *       },
	 *       show: function( txt ) {
	 *         alert(this.name+txt)
	 *       }
	 *     })
	 *     new Todo("Trash").get()
	 *
	 * Callback is available as a static and prototype method.
	 *
	 * ##  Demo
	 *
	 * @demo jquery/class/class.html
	 *
	 *
	 * @constructor
	 *
	 * To create a Class call:
	 *
	 *     $.Class( [NAME , STATIC,] PROTOTYPE ) -> Class
	 *
	 * <div class='params'>
	 *   <div class='param'><label>NAME</label><code>{optional:String}</code>
	 *   <p>If provided, this sets the shortName and fullName of the
	 *      class and adds it and any necessary namespaces to the
	 *      window object.</p>
	 *   </div>
	 *   <div class='param'><label>STATIC</label><code>{optional:Object}</code>
	 *   <p>If provided, this creates static properties and methods
	 *   on the class.</p>
	 *   </div>
	 *   <div class='param'><label>PROTOTYPE</label><code>{Object}</code>
	 *   <p>Creates prototype methods on the class.</p>
	 *   </div>
	 * </div>
	 *
	 * When a Class is created, the static [jQuery.Class.static.setup setup]
	 * and [jQuery.Class.static.init init]  methods are called.
	 *
	 * To create an instance of a Class, call:
	 *
	 *     new Class([args ... ]) -> instance
	 *
	 * The created instance will have all the
	 * prototype properties and methods defined by the PROTOTYPE object.
	 *
	 * When an instance is created, the prototype [jQuery.Class.prototype.setup setup]
	 * and [jQuery.Class.prototype.init init]  methods
	 * are called.
	 */

	clss = $.Class = function() {
		if (arguments.length) {
			clss.extend.apply(clss, arguments);
		}
	};

	/* @Static*/
	extend(clss, {
		/**
		 * @function proxy
		 * Returns a callback function for a function on this Class.
		 * Proxy ensures that 'this' is set appropriately.
		 * @codestart
		 * $.Class("MyClass",{
		 *     getData: function() {
		 *         this.showing = null;
		 *         $.get("data.json",this.proxy('gotData'),'json')
		 *     },
		 *     gotData: function( data ) {
		 *         this.showing = data;
		 *     }
		 * },{});
		 * MyClass.showData();
		 * @codeend
		 * <h2>Currying Arguments</h2>
		 * Additional arguments to proxy will fill in arguments on the returning function.
		 * @codestart
		 * $.Class("MyClass",{
		 *    getData: function( <b>callback</b> ) {
		 *      $.get("data.json",this.proxy('process',<b>callback</b>),'json');
		 *    },
		 *    process: function( <b>callback</b>, jsonData ) { //callback is added as first argument
		 *        jsonData.processed = true;
		 *        callback(jsonData);
		 *    }
		 * },{});
		 * MyClass.getData(showDataFunc)
		 * @codeend
		 * <h2>Nesting Functions</h2>
		 * Proxy can take an array of functions to call as
		 * the first argument.  When the returned callback function
		 * is called each function in the array is passed the return value of the prior function.  This is often used
		 * to eliminate currying initial arguments.
		 * @codestart
		 * $.Class("MyClass",{
		 *    getData: function( callback ) {
		 *      //calls process, then callback with value from process
		 *      $.get("data.json",this.proxy(['process2',callback]),'json')
		 *    },
		 *    process2: function( type,jsonData ) {
		 *        jsonData.processed = true;
		 *        return [jsonData];
		 *    }
		 * },{});
		 * MyClass.getData(showDataFunc);
		 * @codeend
		 * @param {String|Array} fname If a string, it represents the function to be called.
		 * If it is an array, it will call each function in order and pass the return value of the prior function to the
		 * next function.
		 * @return {Function} the callback function.
		 */
		proxy: function( funcs ) {

			//args that should be curried
			var args = makeArray(arguments),
				self;

			// get the functions to callback
			funcs = args.shift();

			// if there is only one function, make funcs into an array
			if (!isArray(funcs) ) {
				funcs = [funcs];
			}

			// keep a reference to us in self
			self = this;

			
			return function class_cb() {
				// add the arguments after the curried args
				var cur = concatArgs(args, arguments),
					isString,
					length = funcs.length,
					f = 0,
					func;

				// go through each function to call back
				for (; f < length; f++ ) {
					func = funcs[f];
					if (!func ) {
						continue;
					}

					// set called with the name of the function on self (this is how this.view works)
					isString = typeof func == "string";
					if ( isString && self._set_called ) {
						self.called = func;
					}

					// call the function
					cur = (isString ? self[func] : func).apply(self, cur || []);

					// pass the result to the next function (if there is a next function)
					if ( f < length - 1 ) {
						cur = !isArray(cur) || cur._use_call ? [cur] : cur
					}
				}
				return cur;
			}
		},
		/**
		 * @function newInstance
		 * Creates a new instance of the class.  This method is useful for creating new instances
		 * with arbitrary parameters.
		 * <h3>Example</h3>
		 * @codestart
		 * $.Class("MyClass",{},{})
		 * var mc = MyClass.newInstance.apply(null, new Array(parseInt(Math.random()*10,10))
		 * @codeend
		 * @return {class} instance of the class
		 */
		newInstance: function() {
			// get a raw instance objet (init is not called)
			var inst = this.rawInstance(),
				args;

			// call setup if there is a setup
			if ( inst.setup ) {
				args = inst.setup.apply(inst, arguments);
			}
			// call init if there is an init, if setup returned args, use those as the arguments
			if ( inst.init ) {
				inst.init.apply(inst, isArray(args) ? args : arguments);
			}
			return inst;
		},
		/**
		 * Setup gets called on the inherting class with the base class followed by the
		 * inheriting class's raw properties.
		 *
		 * Setup will deeply extend a static defaults property on the base class with
		 * properties on the base class.  For example:
		 *
		 *     $.Class("MyBase",{
		 *       defaults : {
		 *         foo: 'bar'
		 *       }
		 *     },{})
		 *
		 *     MyBase("Inheriting",{
		 *       defaults : {
		 *         newProp : 'newVal'
		 *       }
		 *     },{}
		 *
		 *     Inheriting.defaults -> {foo: 'bar', 'newProp': 'newVal'}
		 *
		 * @param {Object} baseClass the base class that is being inherited from
		 * @param {String} fullName the name of the new class
		 * @param {Object} staticProps the static properties of the new class
		 * @param {Object} protoProps the prototype properties of the new class
		 */
		setup: function( baseClass, fullName ) {
			// set defaults as the merger of the parent defaults and this object's defaults
			this.defaults = extend(true, {}, baseClass.defaults, this.defaults);
			return arguments;
		},
		rawInstance: function() {
			// prevent running init
			initializing = true;
			var inst = new this();
			initializing = false;
			// allow running init
			return inst;
		},
		/**
		 * Extends a class with new static and prototype functions.  There are a variety of ways
		 * to use extend:
		 *
		 *     // with className, static and prototype functions
		 *     $.Class('Task',{ STATIC },{ PROTOTYPE })
		 *     // with just classname and prototype functions
		 *     $.Class('Task',{ PROTOTYPE })
		 *     // with just a className
		 *     $.Class('Task')
		 *
		 * You no longer have to use <code>.extend</code>.  Instead, you can pass those options directly to
		 * $.Class (and any inheriting classes):
		 *
		 *     // with className, static and prototype functions
		 *     $.Class('Task',{ STATIC },{ PROTOTYPE })
		 *     // with just classname and prototype functions
		 *     $.Class('Task',{ PROTOTYPE })
		 *     // with just a className
		 *     $.Class('Task')
		 *
		 * @param {String} [fullName]  the classes name (used for classes w/ introspection)
		 * @param {Object} [klass]  the new classes static/class functions
		 * @param {Object} [proto]  the new classes prototype functions
		 *
		 * @return {jQuery.Class} returns the new class
		 */
		extend: function( fullName, klass, proto ) {
			// figure out what was passed and normalize it
			if ( typeof fullName != 'string' ) {
				proto = klass;
				klass = fullName;
				fullName = null;
			}
			if (!proto ) {
				proto = klass;
				klass = null;
			}

			proto = proto || {};
			var _super_class = this,
				_super = this[STR_PROTOTYPE],
				name, shortName, namespace, prototype;

			// Instantiate a base class (but only create the instance,
			// don't run the init constructor)
			initializing = true;
			prototype = new this();
			initializing = false;

			// Copy the properties over onto the new prototype
			inheritProps(proto, _super, prototype);

			// The dummy class constructor
			function Class() {
				// All construction is actually done in the init method
				if ( initializing ) return;

				// we are being called w/o new, we are extending
				if ( this.constructor !== Class && arguments.length ) {
					return arguments.callee.extend.apply(arguments.callee, arguments)
				} else { //we are being called w/ new
					return this.Class.newInstance.apply(this.Class, arguments)
				}
			}
			// Copy old stuff onto class
			for ( name in this ) {
				if ( this.hasOwnProperty(name) ) {
					Class[name] = this[name];
				}
			}

			// copy new static props on class
			inheritProps(klass, this, Class);

			// do namespace stuff
			if ( fullName ) {

				var root;
				if (klass && klass.root) {
					root = klass.root;
					if ($.isString(root)) {
						root = getObject(root, window, true);
					}
				}

				var parts = fullName.split(/\./),
					shortName = parts.pop(),
					current = getObject(parts.join('.'), root || window, true),
					namespace = current;

				

				// !-- FOUNDRY HACK --! //
				// Inherit any existing properties from the namespace where Class is being assigned to.
				extend(true, Class, current[shortName]);

				current[shortName] = Class;
			}

			// set things that can't be overwritten
			extend(Class, {
				prototype: prototype,
				/**
				 * @attribute namespace
				 * The namespaces object
				 *
				 *     $.Class("MyOrg.MyClass",{},{})
				 *     MyOrg.MyClass.namespace //-> MyOrg
				 *
				 */
				namespace: namespace,
				/**
				 * @attribute shortName
				 * The name of the class without its namespace, provided for introspection purposes.
				 *
				 *     $.Class("MyOrg.MyClass",{},{})
				 *     MyOrg.MyClass.shortName //-> 'MyClass'
				 *     MyOrg.MyClass.fullName //->  'MyOrg.MyClass'
				 *
				 */
				shortName: shortName,
				constructor: Class,
				/**
				 * @attribute fullName
				 * The full name of the class, including namespace, provided for introspection purposes.
				 *
				 *     $.Class("MyOrg.MyClass",{},{})
				 *     MyOrg.MyClass.shortName //-> 'MyClass'
				 *     MyOrg.MyClass.fullName //->  'MyOrg.MyClass'
				 *
				 */
				fullName: fullName
			});

			//make sure our prototype looks nice
			Class[STR_PROTOTYPE].Class = Class[STR_PROTOTYPE].constructor = Class;



			// call the class setup
			var args = Class.setup.apply(Class, concatArgs([_super_class],arguments));

			// call the class init
			if ( Class.init ) {
				Class.init.apply(Class, args || concatArgs([_super_class],arguments));
			}

			/* @Prototype*/
			return Class;
			/**
			 * @function setup
			 * If a setup method is provided, it is called when a new
			 * instances is created.  It gets passed the same arguments that
			 * were given to the Class constructor function (<code> new Class( arguments ... )</code>).
			 *
			 *     $.Class("MyClass",
			 *     {
			 *        setup: function( val ) {
			 *           this.val = val;
			 *         }
			 *     })
			 *     var mc = new MyClass("Check Check")
			 *     mc.val //-> 'Check Check'
			 *
			 * Setup is called before [jQuery.Class.prototype.init init].  If setup
			 * return an array, those arguments will be used for init.
			 *
			 *     $.Class("jQuery.Controller",{
			 *       setup : function(htmlElement, rawOptions){
			 *         return [$(htmlElement),
			 *                   $.extend({}, this.Class.defaults, rawOptions )]
			 *       }
			 *     })
			 *
			 * <div class='whisper'>PRO TIP:
			 * Setup functions are used to normalize constructor arguments and provide a place for
			 * setup code that extending classes don't have to remember to call _super to
			 * run.
			 * </div>
			 *
			 * Setup is not defined on $.Class itself, so calling super in inherting classes
			 * will break.  Don't do the following:
			 *
			 *     $.Class("Thing",{
			 *       setup : function(){
			 *         this._super(); // breaks!
			 *       }
			 *     })
			 *
			 * @return {Array|undefined} If an array is return, [jQuery.Class.prototype.init] is
			 * called with those arguments; otherwise, the original arguments are used.
			 */
			//break up
			/**
			 * @function init
			 * If an <code>init</code> method is provided, it gets called when a new instance
			 * is created.  Init gets called after [jQuery.Class.prototype.setup setup], typically with the
			 * same arguments passed to the Class
			 * constructor: (<code> new Class( arguments ... )</code>).
			 *
			 *     $.Class("MyClass",
			 *     {
			 *        init: function( val ) {
			 *           this.val = val;
			 *        }
			 *     })
			 *     var mc = new MyClass(1)
			 *     mc.val //-> 1
			 *
			 * [jQuery.Class.prototype.setup Setup] is able to modify the arguments passed to init.  Read
			 * about it there.
			 *
			 */
			//Breaks up code
			/**
			 * @attribute constructor
			 *
			 * A reference to the Class (or constructor function).  This allows you to access
			 * a class's static properties from an instance.
			 *
			 * ### Quick Example
			 *
			 *     // a class with a static property
			 *     $.Class("MyClass", {staticProperty : true}, {});
			 *
			 *     // a new instance of myClass
			 *     var mc1 = new MyClass();
			 *
			 *     // read the static property from the instance:
			 *     mc1.constructor.staticProperty //-> true
			 *
			 * Getting static properties with the constructor property, like
			 * [jQuery.Class.static.fullName fullName], is very common.
			 *
			 */
		}

	})





	clss.callback = clss[STR_PROTOTYPE].callback = clss[STR_PROTOTYPE].
	/**
	 * @function proxy
	 * Returns a method that sets 'this' to the current instance.  This does the same thing as
	 * and is described better in [jQuery.Class.static.proxy].
	 * The only difference is this proxy works
	 * on a instance instead of a class.
	 * @param {String|Array} fname If a string, it represents the function to be called.
	 * If it is an array, it will call each function in order and pass the return value of the prior function to the
	 * next function.
	 * @return {Function} the callback function
	 */
	proxy = clss.proxy;


})();(function(){
	// ------- HELPER FUNCTIONS  ------

	// Binds an element, returns a function that unbinds
	var bind = function( el, ev, callback ) {
		var wrappedCallback,
			binder = el.bind && el.unbind ? el : $(isFunction(el) ? [el] : el);
		//this is for events like >click.
		if ( ev.indexOf(">") === 0 ) {
			ev = ev.substr(1);
			wrappedCallback = function( event ) {
				if ( event.target === el ) {
					callback.apply(this, arguments);
				}
			};
		}
		binder.bind(ev, wrappedCallback || callback);
		// if ev name has >, change the name and bind
		// in the wrapped callback, check that the element matches the actual element
		return function() {
			binder.unbind(ev, wrappedCallback || callback);
			el = ev = callback = wrappedCallback = null;
		};
	},
		makeArray = $.makeArray,
		isArray = $.isArray,
		isFunction = $.isFunction,
		isString = $.isString,
		extend = $.extend,
		Str = $.String,
		each = $.each,
		getObject = Str.getObject,

		STR_PROTOTYPE = 'prototype',
		STR_CONSTRUCTOR = 'constructor',
		slice = Array[STR_PROTOTYPE].slice,

		// Binds an element, returns a function that unbinds
		delegate = function( el, selector, ev, callback ) {

			// !-- FOUNDRY HACK --! //
			// Make event delegation work with direct child selector
			if ( selector.indexOf(">") === 0 ) {
				selector = (el.data("directSelector") + " " || "") + selector;
			}

			var binder = el.delegate && el.undelegate ? el : $(isFunction(el) ? [el] : el)
			binder.delegate(selector, ev, callback);

			return function() {
				binder.undelegate(selector, ev, callback);
				binder = el = ev = callback = selector = null;
			};
		},

		// calls bind or unbind depending if there is a selector
		binder = function( el, ev, callback, selector ) {
			return selector ? delegate(el, selector, ev, callback) : bind(el, ev, callback);
		},

		// moves 'this' to the first argument, wraps it with jQuery if it's an element
		shifter = function shifter(context, name) {
			var method = typeof name == "string" ? context[name] : name;
			return function() {
				context.called = name;
    			return method.apply(context, [this.nodeName ? $(this) : this].concat( slice.call(arguments, 0) ) );
			};
		},
		// matches dots
		dotsReg = /\./g,
		// matches controller
		controllersReg = /_?controllers?/ig,
		//used to remove the controller from the name
		underscoreAndRemoveController = function( className ) {
			return Str.underscore(className.replace($.globalNamespace + ".", "").replace(dotsReg, '_').replace(controllersReg, ""));
		},
		// checks if it looks like an action
		// actionMatcher = /[^\w]/,

		// !-- FOUNDRY HACK --! //
		// Prevent inclusion of single word property name that starts with a symbol, e.g. $family from MooTools.
		// This is coming from an environment where jQuery and MooTools may coexist.
		actionMatcher = /^\S(.*)\s(.*)/,

		// handles parameterized action names
		parameterReplacer = /\{([^\}]+)\}/g,
		controllerReplacer = /\{([^\.]+[\.][^\.]+)\}/g,
		breaker = /^(?:(.*?)\s)?([\w\.\:>]+)$/,
		basicProcessor,
		data = function(el, data){
			return $.data(el, "controllers", data)
		};
	/**
	 * @class jQuery.Controller
	 * @parent jquerymx
	 * @plugin jquery/controller
	 * @download  http://jmvcsite.heroku.com/pluginify?plugins[]=jquery/controller/controller.js
	 * @test jquery/controller/qunit.html
	 * @inherits jQuery.Class
	 * @description jQuery widget factory.
	 *
	 * jQuery.Controller helps create organized, memory-leak free, rapidly performing
	 * jQuery widgets.  Its extreme flexibility allows it to serve as both
	 * a traditional View and a traditional Controller.
	 *
	 * This means it is used to
	 * create things like tabs, grids, and contextmenus as well as
	 * organizing them into higher-order business rules.
	 *
	 * Controllers make your code deterministic, reusable, organized and can tear themselves
	 * down auto-magically. Read about [http://jupiterjs.com/news/writing-the-perfect-jquery-plugin
	 * the theory behind controller] and
	 * a [http://jupiterjs.com/news/organize-jquery-widgets-with-jquery-controller walkthrough of its features]
	 * on Jupiter's blog. [mvc.controller Get Started with jQueryMX] also has a great walkthrough.
	 *
	 * Controller inherits from [jQuery.Class $.Class] and makes heavy use of
	 * [http://api.jquery.com/delegate/ event delegation]. Make sure
	 * you understand these concepts before using it.
	 *
	 * ## Basic Example
	 *
	 * Instead of
	 *
	 *
	 *     $(function(){
	 *       $('#tabs').click(someCallbackFunction1)
	 *       $('#tabs .tab').click(someCallbackFunction2)
	 *       $('#tabs .delete click').click(someCallbackFunction3)
	 *     });
	 *
	 * do this
	 *
	 *     $.Controller('Tabs',{
	 *       click: function() {...},
	 *       '.tab click' : function() {...},
	 *       '.delete click' : function() {...}
	 *     })
	 *     $('#tabs').tabs();
	 *
	 *
	 * ## Tabs Example
	 *
	 * @demo jquery/controller/controller.html
	 *
	 * ## Using Controller
	 *
	 * Controller helps you build and organize jQuery plugins.  It can be used
	 * to build simple widgets, like a slider, or organize multiple
	 * widgets into something greater.
	 *
	 * To understand how to use Controller, you need to understand
	 * the typical lifecycle of a jQuery widget and how that maps to
	 * controller's functionality:
	 *
	 * ### A controller class is created.
	 *
	 *     $.Controller("MyWidget",
	 *     {
	 *       defaults :  {
	 *         message : "Remove Me"
	 *       }
	 *     },
	 *     {
	 *       init : function(rawEl, rawOptions){
	 *         this.element.append(
	 *            "<div>"+this.options.message+"</div>"
	 *           );
	 *       },
	 *       "div click" : function(div, ev){
	 *         div.remove();
	 *       }
	 *     })
	 *
	 * This creates a <code>$.fn.my_widget</code> jQuery helper function
	 * that can be used to create a new controller instance on an element. Find
	 * more information [jquery.controller.plugin  here] about the plugin gets created
	 * and the rules around its name.
	 *
	 * ### An instance of controller is created on an element
	 *
	 *     $('.thing').my_widget(options) // calls new MyWidget(el, options)
	 *
	 * This calls <code>new MyWidget(el, options)</code> on
	 * each <code>'.thing'</code> element.
	 *
	 * When a new [jQuery.Class Class] instance is created, it calls the class's
	 * prototype setup and init methods. Controller's [jQuery.Controller.prototype.setup setup]
	 * method:
	 *
	 *  - Sets [jQuery.Controller.prototype.element this.element] and adds the controller's name to element's className.
	 *  - Merges passed in options with defaults object and sets it as [jQuery.Controller.prototype.options this.options]
	 *  - Saves a reference to the controller in <code>$.data</code>.
	 *  - [jquery.controller.listening Binds all event handler methods].
	 *
	 *
	 * ### The controller responds to events
	 *
	 * Typically, Controller event handlers are automatically bound.  However, there are
	 * multiple ways to [jquery.controller.listening listen to events] with a controller.
	 *
	 * Once an event does happen, the callback function is always called with 'this'
	 * referencing the controller instance.  This makes it easy to use helper functions and
	 * save state on the controller.
	 *
	 *
	 * ### The widget is destroyed
	 *
	 * If the element is removed from the page, the
	 * controller's [jQuery.Controller.prototype.destroy] method is called.
	 * This is a great place to put any additional teardown functionality.
	 *
	 * You can also teardown a controller programatically like:
	 *
	 *     $('.thing').my_widget('destroy');
	 *
	 * ## Todos Example
	 *
	 * Lets look at a very basic example -
	 * a list of todos and a button you want to click to create a new todo.
	 * Your HTML might look like:
	 *
	 * @codestart html
	 * &lt;div id='todos'>
	 *  &lt;ol>
	 *    &lt;li class="todo">Laundry&lt;/li>
	 *    &lt;li class="todo">Dishes&lt;/li>
	 *    &lt;li class="todo">Walk Dog&lt;/li>
	 *  &lt;/ol>
	 *  &lt;a class="create">Create&lt;/a>
	 * &lt;/div>
	 * @codeend
	 *
	 * To add a mousover effect and create todos, your controller might look like:
	 *
	 *     $.Controller('Todos',{
	 *       ".todo mouseover" : function( el, ev ) {
	 *         el.css("backgroundColor","red")
	 *       },
	 *       ".todo mouseout" : function( el, ev ) {
	 *         el.css("backgroundColor","")
	 *       },
	 *       ".create click" : function() {
	 *         this.find("ol").append("<li class='todo'>New Todo</li>");
	 *       }
	 *     })
	 *
	 * Now that you've created the controller class, you've must attach the event handlers on the '#todos' div by
	 * creating [jQuery.Controller.prototype.setup|a new controller instance].  There are 2 ways of doing this.
	 *
	 * @codestart
	 * //1. Create a new controller directly:
	 * new Todos($('#todos'));
	 * //2. Use jQuery function
	 * $('#todos').todos();
	 * @codeend
	 *
	 * ## Controller Initialization
	 *
	 * It can be extremely useful to add an init method with
	 * setup functionality for your widget.
	 *
	 * In the following example, I create a controller that when created, will put a message as the content of the element:
	 *
	 *     $.Controller("SpecialController",
	 *     {
	 *       init: function( el, message ) {
	 *         this.element.html(message)
	 *       }
	 *     })
	 *     $(".special").special("Hello World")
	 *
	 * ## Removing Controllers
	 *
	 * Controller removal is built into jQuery.  So to remove a controller, you just have to remove its element:
	 *
	 * @codestart
	 * $(".special_controller").remove()
	 * $("#containsControllers").html("")
	 * @codeend
	 *
	 * It's important to note that if you use raw DOM methods (<code>innerHTML, removeChild</code>), the controllers won't be destroyed.
	 *
	 * If you just want to remove controller functionality, call destroy on the controller instance:
	 *
	 * @codestart
	 * $(".special_controller").controller().destroy()
	 * @codeend
	 *
	 * ## Accessing Controllers
	 *
	 * Often you need to get a reference to a controller, there are a few ways of doing that.  For the
	 * following example, we assume there are 2 elements with <code>className="special"</code>.
	 *
	 * @codestart
	 * //creates 2 foo controllers
	 * $(".special").foo()
	 *
	 * //creates 2 bar controllers
	 * $(".special").bar()
	 *
	 * //gets all controllers on all elements:
	 * $(".special").controllers() //-> [foo, bar, foo, bar]
	 *
	 * //gets only foo controllers
	 * $(".special").controllers(FooController) //-> [foo, foo]
	 *
	 * //gets all bar controllers
	 * $(".special").controllers(BarController) //-> [bar, bar]
	 *
	 * //gets first controller
	 * $(".special").controller() //-> foo
	 *
	 * //gets foo controller via data
	 * $(".special").data("controllers")["FooController"] //-> foo
	 * @codeend
	 *
	 * ## Calling methods on Controllers
	 *
	 * Once you have a reference to an element, you can call methods on it.  However, Controller has
	 * a few shortcuts:
	 *
	 * @codestart
	 * //creates foo controller
	 * $(".special").foo({name: "value"})
	 *
	 * //calls FooController.prototype.update
	 * $(".special").foo({name: "value2"})
	 *
	 * //calls FooController.prototype.bar
	 * $(".special").foo("bar","something I want to pass")
	 * @codeend
	 *
	 * These methods let you call one controller from another controller.
	 *
	 */
	var controllerRoot = $.globalNamespace + ".Controller";

	$.Controller = function(name) {

		// !-- FOUNDRY HACK --! //
		// By default, all controllers are created under the
		// $.Controller root namespace.
		var args = makeArray(arguments),
			_static = {
				root: controllerRoot
			},
			_prototype;

		if (args.length > 2) {
			// Namespace can be overriden
			_static = $.extend(_static, args[1]);
			_prototype = args[2];
		} else {
			_prototype = args[1];
		}

		if (_static.namespace) {
			name = _static.namespace + "." + name;
		}

		return $.Controller.Class(name, _static, _prototype);
	}

	var controllerClass = controllerRoot + ".Class";

	$.Class(controllerClass,
	/**
	 * @Static
	 */
	{
		/**
		 * Does 2 things:
		 *
		 *   - Creates a jQuery helper for this controller.</li>
		 *   - Calculates and caches which functions listen for events.</li>
		 *
		 * ### jQuery Helper Naming Examples
		 *
		 *
		 *     "TaskController" -> $().task_controller()
		 *     "Controllers.Task" -> $().controllers_task()
		 *
		 */
		setup: function(baseClass, name) {

			// Allow contollers to inherit "defaults" from superclasses as it done in $.Class
			this._super.apply(this, arguments);

			// if you didn't provide a name, or are controller, don't do anything
			if (!this.shortName || this.fullName == controllerClass) {
				return;
			}

			// cache the underscored names
			this._fullName = underscoreAndRemoveController(this.fullName);
			this._shortName = underscoreAndRemoveController(this.shortName);

			var controller = this,
				/**
				 * @attribute pluginName
				 * Setting the <code>pluginName</code> property allows you
				 * to change the jQuery plugin helper name from its
				 * default value.
				 *
				 *     $.Controller("Mxui.Layout.Fill",{
				 *       pluginName: "fillWith"
				 *     },{});
				 *
				 *     $("#foo").fillWith();
				 */
				funcName, forLint;

			// !-- FOUNDRY HACK --! //
			// Make creation of jQuery plugin by testing the existence of pluginName.
			if (isString(this.pluginName)) {

				// !-- FOUNDRY HACK --! //
				// Add a reference to the fullname
				var _fullName = this._fullName;
				var pluginname = this.pluginName;

				// create jQuery plugin
				if (!$.fn[pluginname] ) {
					$.fn[pluginname] = function( options ) {

						var args = makeArray(arguments);

						// Returning controller instance if it exists
						if ($.isString(options) && options==="controller") {

							var controllers = data(this[0]),
								instance = controllers && controllers[_fullName];

							return instance;
						}

						return this.each(function() {
							//check if created
							var controllers = data(this),
								//plugin is actually the controller instance
								//plugin = controllers && controllers[pluginname];

								// !-- FOUNDRY HACK --! //
								// Check using controller full name
								instance = controllers && controllers[_fullName];

							if (instance) {

								// call a method on the controller with the remaining args
								if ($.isString(options)) {
									var method = instance[options];
									$.isFunction(method) && method.apply(instance, args.slice(1));
									return;
								}

								// call the plugin's update method
								instance.update.apply(instance, args);

							} else {
								//create a new controller instance
								controller.newInstance.apply(controller, [this].concat(args));
							}
						});
					};
				}
			}

			// !-- FOUNDRY HACK --! //
			// If a prototype factory function was given instead of a prototype object,
			// we expect the factory function to return the prototype object upon execution
			// of the factory function. This factory function gets executed during the
			// instantiation of the controller.

			var args         = makeArray(arguments),
				prototype    = this[STR_PROTOTYPE],
				protoFactory = args[(args.length > 3) ? 3 : 2];

			if (isFunction(protoFactory)) {

				// Remap the factory function
				this.protoFactory = protoFactory;

				// Attempt to execute the prototype factory once to get
				// a list of actions that we can cache first.
				prototype = this.protoFactory.call(this, null);
			}

			// calculate and cache actions
			this.actions = {};

			for (funcName in prototype) {
				if (funcName == 'constructor' || !isFunction(prototype[funcName]) ) {
					continue;
				}
				if ( this._isAction(funcName) ) {
					this.actions[funcName] = this._action(funcName);
				}
			}

			// !-- FOUNDRY HACK --! //
			// Controller has been created. Resolve module.
			$.module("$:/Controllers/" + this.fullName).resolve(this);
		},

		hookup: function( el ) {
			return new this(el);
		},

		/**
		 * @hide
		 * @param {String} methodName a prototype function
		 * @return {Boolean} truthy if an action or not
		 */
		_isAction: function( methodName ) {
			if ( actionMatcher.test(methodName) ) {
				return true;
			} else {
				return $.inArray(methodName, this.listensTo) > -1 || $.event.special[methodName] || processors[methodName];
			}

		},
		/**
		 * @hide
		 * This takes a method name and the options passed to a controller
		 * and tries to return the data necessary to pass to a processor
		 * (something that binds things).
		 *
		 * For performance reasons, this called twice.  First, it is called when
		 * the Controller class is created.  If the methodName is templated
		 * like : "{window} foo", it returns null.  If it is not templated
		 * it returns event binding data.
		 *
		 * The resulting data is added to this.actions.
		 *
		 * When a controller instance is created, _action is called again, but only
		 * on templated actions.
		 *
		 * @param {Object} methodName the method that will be bound
		 * @param {Object} [options] first param merged with class default options
		 * @return {Object} null or the processor and pre-split parts.
		 * The processor is what does the binding/subscribing.
		 */
		_action: function( methodName, options ) {
			// reset the test index
			parameterReplacer.lastIndex = 0;

			//if we don't have options (a controller instance), we'll run this later
			if (!options && parameterReplacer.test(methodName) ) {
				return null;
			}

			// !-- FOUNDRY HACK --! //
			// Ability to bind custom event to self.
			// "{self} customEvent"
			methodName = methodName.replace("{self} ", "");

			// If we have options, run sub to replace templates "{}" with a value from the options
			// or the window
			var convertedName = methodName;

			if (options) {

				var bindingOtherController = false;

				if (controllerReplacer.test(methodName)) {

					var controller, selector = "";
					convertedName =
						methodName
							.replace(controllerReplacer, function(whole, inside){
								var parts = inside.split(".");
								controller = options["{"+parts[0]+"}"] || {};
								if ($.isControllerInstance(controller)) {
									selector = (controller[parts[1]] || {})["selector"];
								}
								return selector;
							})
							.match(breaker);

					// If there is a selector, this will be true.
					bindingOtherController = !!selector;

					convertedName = [controller.element].concat(convertedName || []);
				}

				if (!bindingOtherController) {

					convertedName = Str.sub(methodName, [options, window]);
				}
			}

			// If a "{}" resolves to an object, convertedName will be an array
			var arr = isArray(convertedName),

				// get the parts of the function = [convertedName, delegatePart, eventPart]
				parts = (arr ? convertedName[1] : convertedName).match(breaker),
				event = parts[2],
				processor = processors[event] || basicProcessor;

			return {
				processor: processor,
				parts: parts,
				delegate : arr ? convertedName[0] : undefined
			};
		},

		/**
		 * @attribute processors
		 * An object of {eventName : function} pairs that Controller uses to hook up events
		 * auto-magically.  A processor function looks like:
		 *
		 *     jQuery.Controller.processors.
		 *       myprocessor = function( el, event, selector, cb, controller ) {
		 *          //el - the controller's element
		 *          //event - the event (myprocessor)
		 *          //selector - the left of the selector
		 *          //cb - the function to call
		 *          //controller - the binding controller
		 *       };
		 *
		 * This would bind anything like: "foo~3242 myprocessor".
		 *
		 * The processor must return a function that when called,
		 * unbinds the event handler.
		 *
		 * Controller already has processors for the following events:
		 *
		 *   - change
		 *   - click
		 *   - contextmenu
		 *   - dblclick
		 *   - focusin
		 *   - focusout
		 *   - keydown
		 *   - keyup
		 *   - keypress
		 *   - mousedown
		 *   - mouseenter
		 *   - mouseleave
		 *   - mousemove
		 *   - mouseout
		 *   - mouseover
		 *   - mouseup
		 *   - reset
		 *   - resize
		 *   - scroll
		 *   - select
		 *   - submit
		 *
		 * Listen to events on the document or window
		 * with templated event handlers:
		 *
		 *
		 *     $.Controller('Sized',{
		 *       "{window} resize" : function(){
		 *         this.element.width(this.element.parent().width() / 2);
		 *       }
		 *     });
		 *
		 *     $('.foo').sized();
		 */
		processors: {},
		/**
		 * @attribute listensTo
		 * An array of special events this controller
		 * listens too.  You only need to add event names that
		 * are whole words (ie have no special characters).
		 *
		 *     $.Controller('TabPanel',{
		 *       listensTo : ['show']
		 *     },{
		 *       'show' : function(){
		 *         this.element.show();
		 *       }
		 *     })
		 *
		 *     $('.foo').tab_panel().trigger("show");
		 *
		 */
		listensTo: [],
		/**
		 * @attribute defaults
		 * A object of name-value pairs that act as default values for a controller's
		 * [jQuery.Controller.prototype.options options].
		 *
		 *     $.Controller("Message",
		 *     {
		 *       defaults : {
		 *         message : "Hello World"
		 *       }
		 *     },{
		 *       init : function(){
		 *         this.element.text(this.options.message);
		 *       }
		 *     })
		 *
		 *     $("#el1").message(); //writes "Hello World"
		 *     $("#el12").message({message: "hi"}); //writes hi
		 *
		 * In [jQuery.Controller.prototype.setup setup] the options passed to the controller
		 * are merged with defaults.  This is not a deep merge.
		 */
		defaults: {},

		hostname: "parent"
	},
	/**
	 * @Prototype
	 */
	{
		/**
		 * Setup is where most of controller's magic happens.  It does the following:
		 *
		 * ### 1. Sets this.element
		 *
		 * The first parameter passed to new Controller(el, options) is expected to be
		 * an element.  This gets converted to a jQuery wrapped element and set as
		 * [jQuery.Controller.prototype.element this.element].
		 *
		 * ### 2. Adds the controller's name to the element's className.
		 *
		 * Controller adds it's plugin name to the element's className for easier
		 * debugging.  For example, if your Controller is named "Foo.Bar", it adds
		 * "foo_bar" to the className.
		 *
		 * ### 3. Saves the controller in $.data
		 *
		 * A reference to the controller instance is saved in $.data.  You can find
		 * instances of "Foo.Bar" like:
		 *
		 *     $("#el").data("controllers")['foo_bar'].
		 *
		 * ### Binds event handlers
		 *
		 * Setup does the event binding described in [jquery.controller.listening Listening To Events].
		 *
		 * @param {HTMLElement} element the element this instance operates on.
		 * @param {Object} [options] option values for the controller.  These get added to
		 * this.options and merged with [jQuery.Controller.static.defaults defaults].
		 * @return {Array} return an array if you wan to change what init is called with. By
		 * default it is called with the element and options passed to the controller.
		 */
		setup: function(elem, options) {

			var instance  = this,
				Class     = instance[STR_CONSTRUCTOR],
				prototype = instance[STR_PROTOTYPE];

			var _fullName = Class._fullName;
			
			// !-- FOUNDRY HACK --! //
			// Unique id for every controller instance.
			instance.instanceId = $.uid(_fullName + '_');

			// !-- FOUNDRY HACK --! //
			// Added defaultOptions as an alternative to defaults
			var instanceOptions = instance.options
								= extend(true, {}, Class.defaults, Class.defaultOptions, options);

			// Convert HTML element into a jQuery element
			// and store it inside instance.element.
			var element = instance.element
						= $(elem);												

			// !-- FOUNDRY HACK --! //
			// Execute factory function if exists, extends the properties
			// of the returned object onto the instance.
			if (Class.protoFactory) {

				// This is where "self" keyword is passed as first argument.
				prototype = Class.protoFactory.apply(Class, [instance, instanceOptions, element]);

				// Extend the properties of the prototype object onto the instance.
				extend(true, instance, prototype);
			}

			// !-- FOUNDRY HACK --! //
			// Use _fullName instead
			// This actually does $(e).data("controllers", _fullName);
			(data(elem) || data(elem, {}))[_fullName] = instance;

			// !-- FOUNDRY HACK --~ //
			// Add a unique direct selector for every controller instance.
			if (!element.data("directSelector")) {
				var selector = $.uid("DS");
				element
					.addClass(selector)
					.data("directSelector", "." + selector);
			}

			// !-- FOUNDRY HACK --! //
			// Augment selector properties into selector functions.
			// The rest are passed in as controller properties.
			for (name in instanceOptions) {

				if (!name.match(/^\{.+\}$/)) continue;

				var key = name.replace(/^\{|\}$/g,''),
					val = instanceOptions[name];

				// Augmented selector function
				if (isString(val)) {

					instance[key] = (function(instance, selector, funcName) {

						// Selector shorthand for controllers
						selector = /^(\.|\#)$/.test(selector) ? selector + funcName : selector;

						// Create selector function
						var selectorFunc = function(filter) {

							var elements = instance.element.find(selector);

							if (filter) {
								elements = elements.filter(filter);
							}

							return elements;
						};

						// Keep the selector as a property of the function
						selectorFunc.selector = selector;

						selectorFunc.css = function() {

							var cssRule = selectorFunc.cssRule;

							if (!cssRule) {

								var directSelector = element.data("directSelector"),

									ruleSelector = $.map(selector.split(","), function(selector) {
														return directSelector + " " + selector
													});

								cssRule = selectorFunc.cssRule = $.cssRule(ruleSelector);
								cssRule.important = true;
							}

							return (arguments.length) ? cssRule.css.apply(cssRule, arguments) : cssRule;
						};

						return selectorFunc;

					})(instance, val, key);

				// Else just reference it, e.g. controller instance
				} else {

					instance[key] = val;
				}
			}

			// !-- FOUNDRY HACK --! //
			// Augment view properties into view functions.
			// self.view.listItem(useHtml, data, callback);
			var views = instanceOptions.view;

			// Prevent augmented functions from being
			// extended onto the prototype view function.
			var __view = instance.view;

			instance.view = function() {
				return __view.apply(this, arguments);
			};

			each(views || {}, function(name, view){

				instance.view[name] = function(useHtml) {

					var args = makeArray(arguments);

					if ($.isBoolean(useHtml)) {
						args = args.slice(1);
					} else {
						useHtml = false;
					}

					return instance.view.apply(instance, [useHtml, name].concat(args));
				}
			});

			// !-- FOUNDRY HACK --! //
			// Instance property override
			$.extend(instance, instanceOptions.controller);

			// !--- FOUNDRY HACK --! //
			instance.pluginInstances = {};

			/**
			 * @attribute called
			 * String name of current function being called on controller instance.  This is
			 * used for picking the right view in render.
			 * @hide
			 */
			instance.called = "init";

			// bind all event handlers
			instance._bind();

			var __init = instance.init || $.noop;

			// !-- FOUNDRY HACK --! //
			// Trigger init event when controller is created.		
			instance.init = function(){
				instance.init = __init;
				result = __init.apply(instance, arguments);
				instance.trigger("init." + Class.fullName.toLowerCase(), [instance]);
				return result;
			}

			/**
			 * @attribute element
			 * The controller instance's delegated element. This
			 * is set by [jQuery.Controller.prototype.setup setup]. It
			 * is a jQuery wrapped element.
			 *
			 * For example, if I add MyWidget to a '#myelement' element like:
			 *
			 *     $.Controller("MyWidget",{
			 *       init : function(){
			 *         this.element.css("color","red")
			 *       }
			 *     })
			 *
			 *     $("#myelement").my_widget()
			 *
			 * MyWidget will turn #myelement's font color red.
			 *
			 * ## Using a different element.
			 *
			 * Sometimes, you want a different element to be this.element.  A
			 * very common example is making progressively enhanced form widgets.
			 *
			 * To change this.element, overwrite Controller's setup method like:
			 *
			 *     $.Controller("Combobox",{
			 *       setup : function(el, options){
			 *          this.oldElement = $(el);
			 *          var newEl = $('<div/>');
			 *          this.oldElement.wrap(newEl);
			 *          this._super(newEl, options);
			 *       },
			 *       init : function(){
			 *          this.element //-> the div
			 *       },
			 *       ".option click" : function(){
			 *         // event handler bound on the div
			 *       },
			 *       destroy : function(){
			 *          var div = this.element; //save reference
			 *          this._super();
			 *          div.replaceWith(this.oldElement);
			 *       }
			 *     }
			 */
			return [element, instanceOptions].concat(makeArray(arguments).slice(2));
			/**
			 * @function init
			 *
			 * Implement this.
			 */
		},
		/**
		 * Bind attaches event handlers that will be
		 * removed when the controller is removed.
		 *
		 * This used to be a good way to listen to events outside the controller's
		 * [jQuery.Controller.prototype.element element].  However,
		 * using templated event listeners is now the prefered way of doing this.
		 *
		 * ### Example:
		 *
		 *     init: function() {
		 *        // calls somethingClicked(el,ev)
		 *        this.bind('click','somethingClicked')
		 *
		 *        // calls function when the window is clicked
		 *        this.bind(window, 'click', function(ev){
		 *          //do something
		 *        })
		 *     },
		 *     somethingClicked: function( el, ev ) {
		 *
		 *     }
		 *
		 * @param {HTMLElement|jQuery.fn|Object} [el=this.element]
		 * The element to be bound.  If an eventName is provided,
		 * the controller's element is used instead.
		 *
		 * @param {String} eventName The event to listen for.
		 * @param {Function|String} func A callback function or the String name of a controller function.  If a controller
		 * function name is given, the controller function is called back with the bound element and event as the first
		 * and second parameter.  Otherwise the function is called back like a normal bind.
		 * @return {Integer} The id of the binding in this._bindings
		 */

		on: function(eventName) {

			var args = makeArray(arguments),
				element = this.element,
				length = args.length;

			// Listen to the controller's element
			// on(eventName, eventHandler);
			if (length==2) {
				return this._binder(element, eventName, args[1]);
			}

			// Listen to controller's child elements matching the selector
			// on(eventName, selector, eventHandler);
			// args[1] == selector, jquery collection or dom node.
			// args[2] == eventHandler.
			if (length==3 && isString(args[1])) {
				return this._binder(element, eventName, args[2], args[1]);
			} else {
				return this._binder(args[1], eventName, args[2]);
			}

			// Listen to an element from another element
			// on(eventName, element, selector, eventHandler);
			if (length==4) {
				return this._binder($(args[1]), eventName, args[3], args[2]);
			}
		},

		// !-- FOUNDRY HACK --! //
		// Rename this.bind from this_bind. Conflict with mootools.
		// _bind: function( el, eventName, func ) {
		_bind: function() {

			var instance = this,
				Class    = instance[STR_CONSTRUCTOR],
				actions  = Class.actions,
				bindings = instance._bindings = [],
				element  = instance.element;

			each(actions || {}, function(name, action){

				if (!actions.hasOwnProperty(name)) return;

				var ready = Class.actions[name] || Class._action(name, instance.options);

				// Translate to the controller element first
				if ($.isControllerInstance(ready.delegate)) {
					ready.delegate = ready.delegate.element;
				}

				bindings.push(
					ready.processor(
						ready.delegate || element,
						ready.parts[2],
						ready.parts[1],
						name,
						instance
					)
				);
			});

			//setup to be destroyed ... don't bind b/c we don't want to remove it
			var destroyCB = shifter(this,"destroy");
			element.bind("destroyed", destroyCB);
			bindings.push(function( el ) {
				$(el).unbind("destroyed", destroyCB);
			});
			return bindings.length;
		},
		_binder: function( el, eventName, func, selector ) {
			if ( typeof func == 'string' ) {
				func = shifter(this,func);
			}
			this._bindings.push(binder(el, eventName, func, selector));
			return this._bindings.length;
		},
		_unbind : function(){
			var el = this.element[0];
			each(this._bindings, function( key, value ) {
				value(el);
			});
			//adds bindings
			this._bindings = [];
		},
		// !-- FOUNDRY HACK --! //
		// Element event triggering
		trigger: function(name) {

			var el = this.element;
			if (!el) return;

			var event = $.Event(name);
				el.trigger.apply(el, [event].concat($.makeArray(arguments).slice(1)));

			return event;
		},
		/**
		 * Delegate will delegate on an elememt and will be undelegated when the controller is removed.
		 * This is a good way to delegate on elements not in a controller's element.<br/>
		 * <h3>Example:</h3>
		 * @codestart
		 * // calls function when the any 'a.foo' is clicked.
		 * this.delegate(document.documentElement,'a.foo', 'click', function(ev){
		 *   //do something
		 * })
		 * @codeend
		 * @param {HTMLElement|jQuery.fn} [element=this.element] the element to delegate from
		 * @param {String} selector the css selector
		 * @param {String} eventName the event to bind to
		 * @param {Function|String} func A callback function or the String name of a controller function.  If a controller
		 * function name is given, the controller function is called back with the bound element and event as the first
		 * and second parameter.  Otherwise the function is called back like a normal bind.
		 * @return {Integer} The id of the binding in this._bindings
		 */
		delegate: function( element, selector, eventName, func ) {
			if ( typeof element == 'string' ) {
				func = eventName;
				eventName = selector;
				selector = element;
				element = this.element;
			}
			return this._binder(element, eventName, func, selector);
		},
		/**
		 * Update extends [jQuery.Controller.prototype.options this.options]
		 * with the `options` argument and rebinds all events.  It basically
		 * re-configures the controller.
		 *
		 * For example, the following controller wraps a recipe form. When the form
		 * is submitted, it creates the recipe on the server.  When the recipe
		 * is `created`, it resets the form with a new instance.
		 *
		 *     $.Controller('Creator',{
		 *       "{recipe} created" : function(){
		 *         this.update({recipe : new Recipe()});
		 *         this.element[0].reset();
		 *         this.find("[type=submit]").val("Create Recipe")
		 *       },
		 *       "submit" : function(el, ev){
		 *         ev.preventDefault();
		 *         var recipe = this.options.recipe;
		 *         recipe.attrs( this.element.formParams() );
		 *         this.find("[type=submit]").val("Saving...")
		 *         recipe.save();
		 *       }
		 *     });
		 *     $('#createRecipes').creator({recipe : new Recipe()})
		 *
		 *
		 * @demo jquery/controller/demo-update.html
		 *
		 * Update is called if a controller's [jquery.controller.plugin jQuery helper] is
		 * called on an element that already has a controller instance
		 * of the same type.
		 *
		 * For example, a widget that listens for model updates
		 * and updates it's html would look like.
		 *
		 *     $.Controller('Updater',{
		 *       // when the controller is created, update the html
		 *       init : function(){
		 *         this.updateView();
		 *       },
		 *
		 *       // update the html with a template
		 *       updateView : function(){
		 *         this.element.html( "content.ejs",
		 *                            this.options.model );
		 *       },
		 *
		 *       // if the model is updated
		 *       "{model} updated" : function(){
		 *         this.updateView();
		 *       },
		 *       update : function(options){
		 *         // make sure you call super
		 *         this._super(options);
		 *
		 *         this.updateView();
		 *       }
		 *     })
		 *
		 *     // create the controller
		 *     // this calls init
		 *     $('#item').updater({model: recipe1});
		 *
		 *     // later, update that model
		 *     // this calls "{model} updated"
		 *     recipe1.update({name: "something new"});
		 *
		 *     // later, update the controller with a new recipe
		 *     // this calls update
		 *     $('#item').updater({model: recipe2});
		 *
		 *     // later, update the new model
		 *     // this calls "{model} updated"
		 *     recipe2.update({name: "something newer"});
		 *
		 * _NOTE:_ If you overwrite `update`, you probably need to call
		 * this._super.
		 *
		 * ### Example
		 *
		 *     $.Controller("Thing",{
		 *       init: function( el, options ) {
		 *         alert( 'init:'+this.options.prop )
		 *       },
		 *       update: function( options ) {
		 *         this._super(options);
		 *         alert('update:'+this.options.prop)
		 *       }
		 *     });
		 *     $('#myel').thing({prop : 'val1'}); // alerts init:val1
		 *     $('#myel').thing({prop : 'val2'}); // alerts update:val2
		 *
		 * @param {Object} options A list of options to merge with
		 * [jQuery.Controller.prototype.options this.options].  Often, this method
		 * is called by the [jquery.controller.plugin jQuery helper function].
		 */
		update: function( options ) {
			extend(this.options, options);
			this._unbind();
			this._bind();
		},
		/**
		 * Destroy unbinds and undelegates all event handlers on this controller,
		 * and prevents memory leaks.  This is called automatically
		 * if the element is removed.  You can overwrite it to add your own
		 * teardown functionality:
		 *
		 *     $.Controller("ChangeText",{
		 *       init : function(){
		 *         this.oldText = this.element.text();
		 *         this.element.text("Changed!!!")
		 *       },
		 *       destroy : function(){
		 *         this.element.text(this.oldText);
		 *         this._super(); //Always call this!
		 *     })
		 *
		 * Make sure you always call <code>_super</code> when overwriting
		 * controller's destroy event.  The base destroy functionality unbinds
		 * all event handlers the controller has created.
		 *
		 * You could call destroy manually on an element with ChangeText
		 * added like:
		 *
		 *     $("#changed").change_text("destroy");
		 *
		 */
		destroy: function() {
			
			if ( this._destroyed ) {
				return;
			}
			var fname = this[STR_CONSTRUCTOR]._fullName,
				controllers;

			// remove all plugins
			for (pname in this.pluginInstances) {
				this.removePlugin(pname);
			}

			// mark as destroyed
			this._destroyed = true;

			// remove the className
			this.element.removeClass(fname);

			// unbind bindings
			this._unbind();
			// clean up
			delete this._actions;

			delete this.element.data("controllers")[fname];

			$(this).triggerHandler("destroyed"); //in case we want to know if the controller is removed

			// !-- FOUNDRY HACK --! //
			// Reassign this.element to an empty jQuery element instead.
			this.element = $();
		},
		/**
		 * Queries from the controller's element.
		 * @codestart
		 * ".destroy_all click" : function() {
		 *    this.find(".todos").remove();
		 * }
		 * @codeend
		 * @param {String} selector selection string
		 * @return {jQuery.fn} returns the matched elements
		 */
		find: function( selector ) {
			return this.element.find(selector);
		},

		// !-- FOUNDRY HACK --! //
		// Quick acccess to views.
		view: function() {

			var args = makeArray(arguments),
				name,
				options = args,
				useHtml = false,
				context = this[STR_CONSTRUCTOR].component || $,
				html = "",
				view = this.options.view || {};

			if (typeof args[0] == "boolean") {
				useHtml = args[0];
				options = args.slice(1);
			}

			name = options[0] = view[options[0]];

			// If view is not assigned, return empty string.
			if (name==undefined) {
				return (useHtml) ? "" : $("");
			}

			html = context.View.apply(context, options);

			return (useHtml) ? html : $($.parseHTML($.trim(html)));
		},

		getPlugin: function(name) {

			return this.pluginInstances[name];
		},

		addSubscriber: function(instance) {

			var instances = ($.isArray(instance)) ? instance : [instance || {}];

			// Prep options
			var host = this,
				hostname = this.Class.hostname,
				options = {};
				options["{" + hostname + "}"] = host;

			$.map(instances, function(instance, i){

				// If this is not a controller instance.
				if (!$.isControllerInstance(instance)) return false;

				// If instance is already a subscriber,skip.
				if (instance.options[hostname]===this) return instance;

				// Also map itself as a method name
				instance[hostname] = host;

				// Attach publisher to subscriber
				return instance.update(options);
			});

			return instances;
		},		

		// addPlugin(name, object, [options]);
		// The object should consist of a method called destroy();

		// addPlugin(name, function, [options]);
		// The function should return an object with a method called destroy();

		addPlugin: function(name, plugin, options) {

			if (!name) return;

			// This means we are working with plugin shorthand
			if ((!plugin && !options) || $.isPlainObject(plugin)) {
				options = plugin;
				plugin = [this.Class.root, this.Class.fullName, $.String.capitalize(name)].join(".");
			}

			// If plugin is a string, get the controller from it.
			if ($.isString(plugin)) {
				plugin = $.getController(plugin);
			}

			var isPluginInstance = $.isControllerInstance(plugin);

			// Controller class are also functions,
			// so this simple test is good enough.
			if (!isFunction(plugin) && !isPluginInstance) return;

			// Normalize plugin options
			options = $.extend(true, {element: this.element}, options, ((this.options.plugin || {})[name] || {}));

			// Determine plugin type
			var type =
				((isPluginInstance) ? "instance" :
				(($.isController(plugin)) ? "controller" : "function"));

			// Trigger addPlugin event so controller can decorate the options
			this.trigger("addPlugin", [name, plugin, options, type]);

			var hostname = this.Class.hostname;

			// Subcontrollers should have a way to listen back to host controller
			options["{" + hostname + "}"] = this;

			var pluginInstance;

			switch(type) {

				// Plugin instance
				case "instance":

					pluginInstance = plugin;

					// Update child plugin with custom plugin options from host
					plugin.update(options);

					plugin[hostname] = this;
					break;

				// Plugin controller
				case "controller":
					pluginInstance = options.element.addController(plugin, options);
					break;

				// Plugin function
				case "function":
					pluginInstance = plugin(this, options);
					break;
			}

			// If pluginInstance could not be created, stop.
			if (!pluginInstance) return;

			// Register plugin
			this.pluginInstances[name] = pluginInstance;

			// Host controller should also have a way to listen back to the child controller
			if (type!=="function") {

				var hostOptions = {};
				hostOptions["{" + name + "}"] = pluginInstance;

				this.update(hostOptions);
			}

			// Trigger registerPlugin
			this.trigger("registerPlugin", [name, pluginInstance, options, type]);

			return pluginInstance;
		},

		removePlugin: function(name) {

			var plugin = this.getPlugin(name);

			if (!plugin) return;

			// Trigger removePlugin
			this.trigger("removePlugin", [name, plugin]);

			delete this.pluginInstances[name];

			return $.isFunction(plugin.destroy) ? plugin.destroy() : null;
		},

		invokePlugin: function(name, method, args) {

			var plugin = this.getPlugin(name);

			// If plugin not exist, stop.
			if (!plugin) return;

			// If plugin method not exist, stop.
			if (!$.isFunction(plugin[method])) return;

			// Let any third party modify the arguments if required
			this.trigger("invokePlugin", [name, plugin, args]);

			return plugin[method].apply(this, args);
		},

		getMessageGroup: function() {

			// Find parent element
			var messageGroup = ($.isFunction(this.messageGroup)) ? this.messageGroup() : this.element.find("[data-message-group]");

			if (messageGroup.length < 1) {
				messageGroup = $("<div data-message-group></div>").prependTo(this.element);
			}

			return messageGroup;
		},

		setMessage: function(message, type) {

			// Normalize arguments
			var defaultOptions = {
					type   : "warning", // type: info, error, success
					message: "",
					parent : this.getMessageGroup(),
					element: $('<div class="alert fade in"><button type="button" class="close" data-foundry-dismiss="alert">×</button></div>')
				},
				userOptions = {},
				isDeferred = $.isDeferred(message);

			// Normalize user options
			if ($.isPlainObject(message) && !isDeferred) {
				userOptions = message;
			} else {
				userOptions = {
					message: message,
					type   : type || "warning"
				}
			}

			var options = $.extend({}, defaultOptions, userOptions),
				element = options.element;

			if ($.isDeferred(message)) {

				var myself = arguments.callee,
					context = this;

				message.done(function(message, type) {
					options.message = message;
					options.type = type || "warning";
					myself.call(context, options);
					element.show();
				});

			} else {

				element
					.addClass("alert-" + options.type)
					.append(options.message);

				if ($('html').has(element).length < 1) {
					element.appendTo(options.parent);
				}
			}

			return element;
		},

		clearMessage: function() {

			this.getMessageGroup().empty();
		},

		//tells callback to set called on this.  I hate this.
		_set_called: true
	});

	var processors = $.Controller.Class.processors,

	//------------- PROCESSSORS -----------------------------
	//processors do the binding.  They return a function that
	//unbinds when called.
	//the basic processor that binds events
	basicProcessor = function( el, event, selector, methodName, controller ) {
		return binder(el, event, shifter(controller, methodName), selector);
	};




	//set common events to be processed as a basicProcessor
	each("change click contextmenu dblclick keydown keyup keypress mousedown mousemove mouseout mouseover mouseup reset resize scroll select submit focusin focusout mouseenter mouseleave".split(" "), function( i, v ) {
		processors[v] = basicProcessor;
	});
	/**
	 *  @add jQuery.fn
	 */

	//used to determine if a controller instance is one of controllers
	//controllers can be strings or classes

	var normalizeController = function(controller) {
		return controller.replace("$.Controller", controllerRoot);
	}

	var getController = function(controller) {
		if (isString(controller)) {
			controller = normalizeController(controller);
			controller = getObject(controller) || getObject(controllerRoot + "." + controller);
		};
		if (isController(controller)) {
			return controller;
		};
	}

	var isController = function(controller) {
		return isFunction(controller) && controller.hasOwnProperty("_fullName");
	}

	var flattenControllers = function(controllers) {
		return $.map(controllers, function(controller){
			return (isArray(controller)) ? flattenControllers(controller) : getController(controller);
		});
	};

	$.getController = getController;

	$.isController = function(controller) {
		return !!getController(controller);
	}

	$.isControllerInstance = function(instance) {
		return instance && instance[STR_CONSTRUCTOR] && isController(instance[STR_CONSTRUCTOR]);
	}

	$.isControllerOf = function(instance, controllers) {

		if (!controllers) return false;

		if (!isArray(controllers)) {
			controllers = [controllers];
		}

		for (var i=0; i<controllers.length; i++) {
			var controller = getController(controllers[i]);
			if (instance instanceof controller) return true;
		}

		return false;
	};

	$.fn.extend({
		/**
		 * @function controllers
		 * Gets all controllers in the jQuery element.
		 * @return {Array} an array of controller instances.
		 */
		controllers: function() {

			var candidates = flattenControllers(makeArray(arguments)),
				instances = [];

			this.each(function() {

				var controllers = $.data(this, "controllers");

				each(controllers || {}, function(_fullName, instance){

					if (!controllers.hasOwnProperty(_fullName)) return;

					if (!candidates.length || $.isControllerOf(instance, candidates)) {
						instances.push(instance);
					}
				});
			});

			return instances;
		},

		/**
		 * @function controller
		 * Gets a controller in the jQuery element.  With no arguments, returns the first one found.
		 * @param {Object} controller (optional) if exists, the first controller instance with this class type will be returned.
		 * @return {jQuery.Controller} the first controller.
		 */
		controller: function(controller, options) {

			// Getter
			if (options===undefined) {
				return this.controllers(controller)[0];
			}

			// Setter
			this.addController.apply(this, arguments);
			return this;
		},

		hasController: function(controller) {

			var _fullName =
				(getController(controller) || {})._fullName ||
				(isString(controller) ? underscoreAndRemoveController(normalizeController(controller)) : "");

			return (!_fullName) ? false : (($(this).data("controllers") || {}).hasOwnProperty(_fullName));
		},

		addController: function(controller, options, callback) {

			var Controller = getController(controller);

			if (!Controller) return;

			var instances = [];

			this.each(function(){

				// Do not add controller on script node or non-element nodes.
				if (this.nodeType!==1 || this.nodeName=="SCRIPT") return;				

				// Just return existing instance
				var existingInstance = $(this).controller(controller);
				if (existingInstance) {
					instances.push(existingInstance);
					return;
				}

				// Or create a new instance
				var instance = new Controller(this, options);
				isFunction(callback) && callback.apply(instance, [$(this), instance]);
				instances.push(instance);
			});

			return (instances.length > 1) ? instances : instances[0];
		},

		removeController: function(controller) {
			this.each(function(){
				var instances = $(this).controllers(controller);
				while (instances.length) {
					instances.shift().destroy();
				}
			});
			return this;
		},

		addControllerWhenAvailable: function(controller) {

			var elements = this,
				args = arguments,
				task = $.Deferred();

			if ($.isController(controller)) {
				controller = controller.fullName;
			}

			if (!isString(controller)) {
				return task.reject();
			}

			$.module("$:/Controllers/" + controller)
				.pipe(
					function(){
						var instance = elements.addController.apply(elements, args);
						task.resolveWith(instance, [elements, instance]);
					},
					task.reject,
					task.fail
				);

			return task;
		},

		// @deprecated 2.2
		implement: function() {
			this.addController.apply(this, arguments);
			return this;
		}

	});

})();(function(){

	// a path like string into something that's ok for an element ID
	var toId = function( src ) {
		return src.replace(/^\/\//, "").replace(/[\/\.]/g, "_");
	},
		makeArray = $.makeArray,
		// used for hookup ids
		id = 1;
	// this might be useful for testing if html
	// htmlTest = /^[\s\n\r\xA0]*<(.|[\r\n])*>[\s\n\r\xA0]*$/
	/**
	 * @class jQuery.View
	 * @parent jquerymx
	 * @plugin jquery/view
	 * @test jquery/view/qunit.html
	 * @download dist/jquery.view.js
	 *
	 * @description A JavaScript template framework.
	 *
	 * View provides a uniform interface for using templates with
	 * jQuery. When template engines [jQuery.View.register register]
	 * themselves, you are able to:
	 *
	 *  - Use views with jQuery extensions [jQuery.fn.after after], [jQuery.fn.append append],
	 *   [jQuery.fn.before before], [jQuery.fn.html html], [jQuery.fn.prepend prepend],
	 *   [jQuery.fn.replaceWith replaceWith], [jQuery.fn.text text].
	 *  - Template loading from html elements and external files.
	 *  - Synchronous and asynchronous template loading.
	 *  - [view.deferreds Deferred Rendering].
	 *  - Template caching.
	 *  - Bundling of processed templates in production builds.
	 *  - Hookup jquery plugins directly in the template.
	 *
	 * The [mvc.view Get Started with jQueryMX] has a good walkthrough of $.View.
	 *
	 * ## Use
	 *
	 *
	 * When using views, you're almost always wanting to insert the results
	 * of a rendered template into the page. jQuery.View overwrites the
	 * jQuery modifiers so using a view is as easy as:
	 *
	 *     $("#foo").html('mytemplate.ejs',{message: 'hello world'})
	 *
	 * This code:
	 *
	 *  - Loads the template a 'mytemplate.ejs'. It might look like:
	 *    <pre><code>&lt;h2>&lt;%= message %>&lt;/h2></pre></code>
	 *
	 *  - Renders it with {message: 'hello world'}, resulting in:
	 *    <pre><code>&lt;div id='foo'>"&lt;h2>hello world&lt;/h2>&lt;/div></pre></code>
	 *
	 *  - Inserts the result into the foo element. Foo might look like:
	 *    <pre><code>&lt;div id='foo'>&lt;h2>hello world&lt;/h2>&lt;/div></pre></code>
	 *
	 * ## jQuery Modifiers
	 *
	 * You can use a template with the following jQuery modifiers:
	 *
	 * <table>
	 * <tr><td>[jQuery.fn.after after]</td><td> <code>$('#bar').after('temp.jaml',{});</code></td></tr>
	 * <tr><td>[jQuery.fn.append append] </td><td>  <code>$('#bar').append('temp.jaml',{});</code></td></tr>
	 * <tr><td>[jQuery.fn.before before] </td><td> <code>$('#bar').before('temp.jaml',{});</code></td></tr>
	 * <tr><td>[jQuery.fn.html html] </td><td> <code>$('#bar').html('temp.jaml',{});</code></td></tr>
	 * <tr><td>[jQuery.fn.prepend prepend] </td><td> <code>$('#bar').prepend('temp.jaml',{});</code></td></tr>
	 * <tr><td>[jQuery.fn.replaceWith replaceWith] </td><td> <code>$('#bar').replaceWith('temp.jaml',{});</code></td></tr>
	 * <tr><td>[jQuery.fn.text text] </td><td> <code>$('#bar').text('temp.jaml',{});</code></td></tr>
	 * </table>
	 *
	 * You always have to pass a string and an object (or function) for the jQuery modifier
	 * to user a template.
	 *
	 * ## Template Locations
	 *
	 * View can load from script tags or from files.
	 *
	 * ## From Script Tags
	 *
	 * To load from a script tag, create a script tag with your template and an id like:
	 *
	 * <pre><code>&lt;script type='text/ejs' id='recipes'>
	 * &lt;% for(var i=0; i &lt; recipes.length; i++){ %>
	 *   &lt;li>&lt;%=recipes[i].name %>&lt;/li>
	 * &lt;%} %>
	 * &lt;/script></code></pre>
	 *
	 * Render with this template like:
	 *
	 * @codestart
	 * $("#foo").html('recipes',recipeData)
	 * @codeend
	 *
	 * Notice we passed the id of the element we want to render.
	 *
	 * ## From File
	 *
	 * You can pass the path of a template file location like:
	 *
	 *     $("#foo").html('templates/recipes.ejs',recipeData)
	 *
	 * However, you typically want to make the template work from whatever page they
	 * are called from.  To do this, use // to look up templates from JMVC root:
	 *
	 *     $("#foo").html('//app/views/recipes.ejs',recipeData)
	 *
	 * Finally, the [jQuery.Controller.prototype.view controller/view] plugin can make looking
	 * up a thread (and adding helpers) even easier:
	 *
	 *     $("#foo").html( this.view('recipes', recipeData) )
	 *
	 * ## Packaging Templates
	 *
	 * If you're making heavy use of templates, you want to organize
	 * them in files so they can be reused between pages and applications.
	 *
	 * But, this organization would come at a high price
	 * if the browser has to
	 * retrieve each template individually. The additional
	 * HTTP requests would slow down your app.
	 *
	 * Fortunately, [steal.static.views steal.views] can build templates
	 * into your production files. You just have to point to the view file like:
	 *
	 *     steal.views('path/to/the/view.ejs');
	 *
	 * ## Asynchronous
	 *
	 * By default, retrieving requests is done synchronously. This is
	 * fine because StealJS packages view templates with your JS download.
	 *
	 * However, some people might not be using StealJS or want to delay loading
	 * templates until necessary. If you have the need, you can
	 * provide a callback paramter like:
	 *
	 *     $("#foo").html('recipes',recipeData, function(result){
	 *       this.fadeIn()
	 *     });
	 *
	 * The callback function will be called with the result of the
	 * rendered template and 'this' will be set to the original jQuery object.
	 *
	 * ## Deferreds (3.0.6)
	 *
	 * If you pass deferreds to $.View or any of the jQuery
	 * modifiers, the view will wait until all deferreds resolve before
	 * rendering the view.  This makes it a one-liner to make a request and
	 * use the result to render a template.
	 *
	 * The following makes a request for todos in parallel with the
	 * todos.ejs template.  Once todos and template have been loaded, it with
	 * render the view with the todos.
	 *
	 *     $('#todos').html("todos.ejs",Todo.findAll());
	 *
	 * ## Just Render Templates
	 *
	 * Sometimes, you just want to get the result of a rendered
	 * template without inserting it, you can do this with $.View:
	 *
	 *     var out = $.View('path/to/template.jaml',{});
	 *
	 * ## Preloading Templates
	 *
	 * You can preload templates asynchronously like:
	 *
	 *     $.get('path/to/template.jaml',{},function(){},'view');
	 *
	 * ## Supported Template Engines
	 *
	 * JavaScriptMVC comes with the following template languages:
	 *
	 *   - EmbeddedJS
	 *     <pre><code>&lt;h2>&lt;%= message %>&lt;/h2></code></pre>
	 *
	 *   - JAML
	 *     <pre><code>h2(data.message);</code></pre>
	 *
	 *   - Micro
	 *     <pre><code>&lt;h2>{%= message %}&lt;/h2></code></pre>
	 *
	 *   - jQuery.Tmpl
	 *     <pre><code>&lt;h2>${message}&lt;/h2></code></pre>

	 *
	 * The popular <a href='http://awardwinningfjords.com/2010/08/09/mustache-for-javascriptmvc-3.html'>Mustache</a>
	 * template engine is supported in a 2nd party plugin.
	 *
	 * ## Using other Template Engines
	 *
	 * It's easy to integrate your favorite template into $.View and Steal.  Read
	 * how in [jQuery.View.register].
	 *
	 * @constructor
	 *
	 * Looks up a template, processes it, caches it, then renders the template
	 * with data and optional helpers.
	 *
	 * With [stealjs StealJS], views are typically bundled in the production build.
	 * This makes it ok to use views synchronously like:
	 *
	 * @codestart
	 * $.View("//myplugin/views/init.ejs",{message: "Hello World"})
	 * @codeend
	 *
	 * If you aren't using StealJS, it's best to use views asynchronously like:
	 *
	 * @codestart
	 * $.View("//myplugin/views/init.ejs",
	 *        {message: "Hello World"}, function(result){
	 *   // do something with result
	 * })
	 * @codeend
	 *
	 * @param {String} view The url or id of an element to use as the template's source.
	 * @param {Object} data The data to be passed to the view.
	 * @param {Object} [helpers] Optional helper functions the view might use. Not all
	 * templates support helpers.
	 * @param {Object} [callback] Optional callback function.  If present, the template is
	 * retrieved asynchronously.  This is a good idea if you aren't compressing the templates
	 * into your view.
	 * @return {String} The rendered result of the view or if deferreds
	 * are passed, a deferred that will resolve to
	 * the rendered result of the view.
	 */
	var $view = $.View = function( view, data, helpers, callback ) {
		// if helpers is a function, it is actually a callback
		if ( typeof helpers === 'function' ) {
			callback = helpers;
			helpers = undefined;
		}

		// see if we got passed any deferreds
		var deferreds = getDeferreds(data);


		if ( deferreds.length ) { // does data contain any deferreds?
			// the deferred that resolves into the rendered content ...
			var deferred = $.Deferred();

			// add the view request to the list of deferreds
			deferreds.push(get(view, true))

			// wait for the view and all deferreds to finish
			$.when.apply($, deferreds).then(function( resolved ) {
				// get all the resolved deferreds
				var objs = makeArray(arguments),
					// renderer is last [0] is the data
					renderer = objs.pop()[0],
					// the result of the template rendering with data
					result;

				// make data look like the resolved deferreds
				if ( isDeferred(data) ) {
					data = usefulPart(resolved);
				}
				else {
					// go through each prop in data again,
					// replace the defferreds with what they resolved to
					for ( var prop in data ) {
						if ( isDeferred(data[prop]) ) {
							data[prop] = usefulPart(objs.shift());
						}
					}
				}
				// get the rendered result
				result = renderer(data, helpers);

				//resolve with the rendered view
				deferred.resolve(result);
				// if there's a callback, call it back with the result
				callback && callback(result);
			});
			// return the deferred ....
			return deferred.promise();
		}
		else {
			// no deferreds, render this bad boy
			var response,
				// if there's a callback function
				async = typeof callback === "function",
				// get the 'view' type
				deferred = get(view, async);

			// if we are async,
			if ( async ) {
				// return the deferred
				response = deferred;
				// and callback callback with the rendered result
				deferred.done(function( renderer ) {
					callback(renderer(data, helpers))
				})
			} else {
				// otherwise, the deferred is complete, so
				// set response to the result of the rendering
				deferred.done(function( renderer ) {
					response = renderer(data, helpers);
				});
			}

			return response;
		}
	},
		// makes sure there's a template, if not, has steal provide a warning
		checkText = function( text, url ) {
			if (!text.match(/[^\s]/) ) {
				
				throw "$.View ERROR: There is no template or an empty template at " + url;
			}
		},
		// returns a 'view' renderer deferred
		// url - the url to the view template
		// async - if the ajax request should be synchronous
		get = function( url, async ) {
			return $.ajax({
				url: url,
				dataType: "view",
				async: async
			});
		},
		// returns true if something looks like a deferred
		isDeferred = function( obj ) {
			return obj && $.isFunction(obj.always) // check if obj is a $.Deferred
		},
		// gets an array of deferreds from an object
		// this only goes one level deep
		getDeferreds = function( data ) {
			var deferreds = [];

			// pull out deferreds
			if ( isDeferred(data) ) {
				return [data]
			} else {
				for ( var prop in data ) {
					if ( isDeferred(data[prop]) ) {
						deferreds.push(data[prop]);
					}
				}
			}
			return deferreds;
		},
		// gets the useful part of deferred
		// this is for Models and $.ajax that resolve to array (with success and such)
		// returns the useful, content part
		usefulPart = function( resolved ) {
			return $.isArray(resolved) && resolved.length === 3 && resolved[1] === 'success' ? resolved[0] : resolved
		};



	// you can request a view renderer (a function you pass data to and get html)
	// Creates a 'view' transport.  These resolve to a 'view' renderer
	// a 'view' renderer takes data and returns a string result.
	// For example:
	//
	//  $.ajax({dataType : 'view', src: 'foo.ejs'}).then(function(renderer){
	//     renderer({message: 'hello world'})
	//  })
	$.ajaxTransport("view", function( options, orig ) {
		// the url (or possibly id) of the view content
		var url = orig.url,
			// check if a suffix exists (ex: "foo.ejs")
			suffix = url.match(/\.[\w\d]+$/),
			type,
			// if we are reading a script element for the content of the template
			// el will be set to that script element
			el,
			// a unique identifier for the view (used for caching)
			// this is typically derived from the element id or
			// the url for the template
			id,
			// the AJAX request used to retrieve the template content
			jqXHR,

			// used to generate the response
			response = function( text ) {
				// get the renderer function
				var func = type.renderer(id, text);
				// cache if if we are caching
				if ( $view.cache ) {
					$view.cached[id] = func;
				}
				// return the objects for the response's dataTypes
				// (in this case view)
				return {
					view: func
				};
			};

		// if we have an inline template, derive the suffix from the 'text/???' part
		// this only supports '<script></script>' tags
		if ( el = document.getElementById(url) ) {
			suffix = "."+el.type.match(/\/(x\-)?(.+)/)[2];
		}

		// if there is no suffix, add one
		if (!suffix ) {
			suffix = $view.ext;
			url = url + $view.ext;
		}

		// convert to a unique and valid id
		id = toId(url);

		// if a absolute path, use steal to get it
		// you should only be using // if you are using steal
		if ( url.match(/^\/\//) ) {
			var sub = url.substr(2);
			url = typeof steal === "undefined" ?
				url = "/" + sub :
				steal.root.mapJoin(sub) +'';
		}

		//set the template engine type
		type = $view.types[suffix];

		// !-- FOUNDRY HACK --! //
		// Retrieve templates stored within $.template
		var template = $.template()[orig.url];

		// return the ajax transport contract: http://api.jquery.com/extending-ajax/
		return {
			send: function( headers, callback ) {

				// !-- FOUNDRY HACK --! //
				// Retrieve templates stored within $.template
				if ( template ) {

					type = $view.types["." + template.type];

					return callback(200, "success", response(template.content));

				// if it is cached,
				} else if ( $view.cached[id] ) {

					// return the catched renderer
					return callback(200, "success", {
						view: $view.cached[id]
					});

				// otherwise if we are getting this from a script elment
				} else if ( el ) {
					// resolve immediately with the element's innerHTML
					callback(200, "success", response(el.innerHTML));
				} else {
					// make an ajax request for text
					jqXHR = $.ajax({
						async: orig.async,
						url: url,
						dataType: "text",
						error: function() {
							checkText("", url);
							callback(404);
						},
						success: function( text ) {
							// make sure we got some text back
							checkText(text, url);
							// cache and send back text
							callback(200, "success", response(text))
						}
					});
				}
			},
			abort: function() {
				jqXHR && jqXHR.abort();
			}
		}
	})
	$.extend($view, {
		/**
		 * @attribute hookups
		 * @hide
		 * A list of pending 'hookups'
		 */
		hookups: {},
		/**
		 * @function hookup
		 * Registers a hookup function that can be called back after the html is
		 * put on the page.  Typically this is handled by the template engine.  Currently
		 * only EJS supports this functionality.
		 *
		 *     var id = $.View.hookup(function(el){
		 *            //do something with el
		 *         }),
		 *         html = "<div data-view-id='"+id+"'>"
		 *     $('.foo').html(html);
		 *
		 *
		 * @param {Function} cb a callback function to be called with the element
		 * @param {Number} the hookup number
		 */
		hookup: function( cb ) {
			var myid = ++id;
			$view.hookups[myid] = cb;
			return myid;
		},
		/**
		 * @attribute cached
		 * @hide
		 * Cached are put in this object
		 */
		cached: {},
		/**
		 * @attribute cache
		 * Should the views be cached or reloaded from the server. Defaults to true.
		 */
		cache: true,
		/**
		 * @function register
		 * Registers a template engine to be used with
		 * view helpers and compression.
		 *
		 * ## Example
		 *
		 * @codestart
		 * $.View.register({
		 * 	suffix : "tmpl",
		 *  plugin : "jquery/view/tmpl",
		 * 	renderer: function( id, text ) {
		 * 		return function(data){
		 * 			return jQuery.render( text, data );
		 * 		}
		 * 	},
		 * 	script: function( id, text ) {
		 * 		var tmpl = $.tmpl(text).toString();
		 * 		return "function(data){return ("+
		 * 		  	tmpl+
		 * 			").call(jQuery, jQuery, data); }";
		 * 	}
		 * })
		 * @codeend
		 * Here's what each property does:
		 *
		 *    * plugin - the location of the plugin
		 *    * suffix - files that use this suffix will be processed by this template engine
		 *    * renderer - returns a function that will render the template provided by text
		 *    * script - returns a string form of the processed template function.
		 *
		 * @param {Object} info a object of method and properties
		 *
		 * that enable template integration:
		 * <ul>
		 *   <li>plugin - the location of the plugin.  EX: 'jquery/view/ejs'</li>
		 *   <li>suffix - the view extension.  EX: 'ejs'</li>
		 *   <li>script(id, src) - a function that returns a string that when evaluated returns a function that can be
		 *    used as the render (i.e. have func.call(data, data, helpers) called on it).</li>
		 *   <li>renderer(id, text) - a function that takes the id of the template and the text of the template and
		 *    returns a render function.</li>
		 * </ul>
		 */
		register: function( info ) {
			this.types["." + info.suffix] = info;

			if ( window.steal ) {
				steal.type(info.suffix + " view js", function( options, success, error ) {
					var type = $view.types["." + options.type],
						id = toId(options.rootSrc+'');

					options.text = type.script(id, options.text)
					success();
				})
			}
		},
		types: {},
		/**
		 * @attribute ext
		 * The default suffix to use if none is provided in the view's url.
		 * This is set to .ejs by default.
		 */
		ext: ".ejs",
		/**
		 * Returns the text that
		 * @hide
		 * @param {Object} type
		 * @param {Object} id
		 * @param {Object} src
		 */
		registerScript: function( type, id, src ) {
			return "$.View.preload('" + id + "'," + $view.types["." + type].script(id, src) + ");";
		},
		/**
		 * @hide
		 * Called by a production script to pre-load a renderer function
		 * into the view cache.
		 * @param {String} id
		 * @param {Function} renderer
		 */
		preload: function( id, renderer ) {
			$view.cached[id] = function( data, helpers ) {
				return renderer.call(data, data, helpers);
			};
		}

	});
	if ( window.steal ) {
		steal.type("view js", function( options, success, error ) {
			var type = $view.types["." + options.type],
				id = toId(options.rootSrc+'');

			options.text = "steal('" + (type.plugin || "jquery/view/" + options.type) + "').then(function($){" + "$.View.preload('" + id + "'," + options.text + ");\n})";
			success();
		})
	}

	//---- ADD jQUERY HELPERS -----
	//converts jquery functions to use views
	var convert, modify, isTemplate, isHTML, isDOM, getCallback, hookupView, funcs,
		// text and val cannot produce an element, so don't run hookups on them
		noHookup = {'val':true,'text':true};

	convert = function( func_name ) {
		// save the old jQuery helper
		var old = $.fn[func_name];

		// replace it wiht our new helper
		$.fn[func_name] = function() {

			var args = makeArray(arguments),
				callbackNum,
				callback,
				self = this,
				result;

			// if the first arg is a deferred
			// wait until it finishes, and call
			// modify with the result
			if ( isDeferred(args[0]) ) {
				args[0].done(function( res ) {
					modify.call(self, [res], old);
				})
				return this;
			}
			//check if a template
			else if ( isTemplate(args) ) {

				// if we should operate async
				if ((callbackNum = getCallback(args))) {
					callback = args[callbackNum];
					args[callbackNum] = function( result ) {
						modify.call(self, [result], old);
						callback.call(self, result);
					};
					$view.apply($view, args);
					return this;
				}
				// call view with args (there might be deferreds)
				result = $view.apply($view, args);

				// if we got a string back
				if (!isDeferred(result) ) {
					// we are going to call the old method with that string
					args = [result];
				} else {
					// if there is a deferred, wait until it is done before calling modify
					result.done(function( res ) {
						modify.call(self, [res], old);
					})
					return this;
				}
			}
			return noHookup[func_name] ? old.apply(this,args) :
				modify.call(this, args, old);
		};
	};

	// modifies the content of the element
	// but also will run any hookup
	modify = function( args, old ) {
		var res, stub, hooks;

		//check if there are new hookups
		for ( var hasHookups in $view.hookups ) {
			break;
		}

		//if there are hookups, get jQuery object
		if ( hasHookups && args[0] && isHTML(args[0]) ) {
			hooks = $view.hookups;
			$view.hookups = {};
			args[0] = $(args[0]);
		}
		res = old.apply(this, args);

		//now hookup the hookups
		if ( hooks
		/* && args.length*/
		) {
			hookupView(args[0], hooks);
		}
		return res;
	};

	// returns true or false if the args indicate a template is being used
	// $('#foo').html('/path/to/template.ejs',{data})
	// in general, we want to make sure the first arg is a string
	// and the second arg is data
	isTemplate = function( args ) {
		// save the second arg type
		var secArgType = typeof args[1];

		// the first arg is a string
		return typeof args[0] == "string" &&
				// the second arg is an object or function
		       (secArgType == 'object' || secArgType == 'function') &&
			   // but it is not a dom element
			   !isDOM(args[1]);
	};
	// returns true if the arg is a jQuery object or HTMLElement
	isDOM = function(arg){
		return arg.nodeType || arg.jquery
	};
	// returns whether the argument is some sort of HTML data
	isHTML = function( arg ) {
		if ( isDOM(arg) ) {
			// if jQuery object or DOM node we're good
			return true;
		} else if ( typeof arg === "string" ) {
			// if string, do a quick sanity check that we're HTML
			arg = $.trim(arg);
			return arg.substr(0, 1) === "<" && arg.substr(arg.length - 1, 1) === ">" && arg.length >= 3;
		} else {
			// don't know what you are
			return false;
		}
	};

	//returns the callback arg number if there is one (for async view use)
	getCallback = function( args ) {
		return typeof args[3] === 'function' ? 3 : typeof args[2] === 'function' && 2;
	};

	hookupView = function( els, hooks ) {
		//remove all hookups
		var hookupEls, len, i = 0,
			id, func;
		els = els.filter(function() {
			return this.nodeType != 3; //filter out text nodes
		})
		hookupEls = els.add("[data-view-id]", els);
		len = hookupEls.length;
		for (; i < len; i++ ) {
			if ( hookupEls[i].getAttribute && (id = hookupEls[i].getAttribute('data-view-id')) && (func = hooks[id]) ) {
				func(hookupEls[i], id);
				delete hooks[id];
				hookupEls[i].removeAttribute('data-view-id');
			}
		}
		//copy remaining hooks back
		$.extend($view.hookups, hooks);
	};

	/**
	 *  @add jQuery.fn
	 *  @parent jQuery.View
	 *  Called on a jQuery collection that was rendered with $.View with pending hookups.  $.View can render a
	 *  template with hookups, but not actually perform the hookup, because it returns a string without actual DOM
	 *  elements to hook up to.  So hookup performs the hookup and clears the pending hookups, preventing errors in
	 *  future templates.
	 *
	 * @codestart
	 * $($.View('//views/recipes.ejs',recipeData)).hookup()
	 * @codeend
	 */
	$.fn.hookup = function() {
		var hooks = $view.hookups;
		$view.hookups = {};
		hookupView(this, hooks);
		return this;
	};

	/**
	 *  @add jQuery.fn
	 */
	$.each([
	/**
	 *  @function prepend
	 *  @parent jQuery.View
	 *
	 *  Extending the original [http://api.jquery.com/prepend/ jQuery().prepend()]
	 *  to render [jQuery.View] templates inserted at the beginning of each element in the set of matched elements.
	 *
	 *  	$('#test').prepend('path/to/template.ejs', { name : 'javascriptmvc' });
	 *
	 *  @param {String|Object|Function} content A template filename or the id of a view script tag
	 *  or a DOM element, array of elements, HTML string, or jQuery object.
	 *  @param {Object} [data] The data to render the view with.
	 *  If rendering a view template this parameter always has to be present
	 *  (use the empty object initializer {} for no data).
	 */
	"prepend",
	/**
	 *  @function append
	 *  @parent jQuery.View
	 *
	 *  Extending the original [http://api.jquery.com/append/ jQuery().append()]
	 *  to render [jQuery.View] templates inserted at the end of each element in the set of matched elements.
	 *
	 *  	$('#test').append('path/to/template.ejs', { name : 'javascriptmvc' });
	 *
	 *  @param {String|Object|Function} content A template filename or the id of a view script tag
	 *  or a DOM element, array of elements, HTML string, or jQuery object.
	 *  @param {Object} [data] The data to render the view with.
	 *  If rendering a view template this parameter always has to be present
	 *  (use the empty object initializer {} for no data).
	 */
	"append",
	/**
	 *  @function after
	 *  @parent jQuery.View
	 *
	 *  Extending the original [http://api.jquery.com/after/ jQuery().after()]
	 *  to render [jQuery.View] templates inserted after each element in the set of matched elements.
	 *
	 *  	$('#test').after('path/to/template.ejs', { name : 'javascriptmvc' });
	 *
	 *  @param {String|Object|Function} content A template filename or the id of a view script tag
	 *  or a DOM element, array of elements, HTML string, or jQuery object.
	 *  @param {Object} [data] The data to render the view with.
	 *  If rendering a view template this parameter always has to be present
	 *  (use the empty object initializer {} for no data).
	 */
	"after",
	/**
	 *  @function before
	 *  @parent jQuery.View
	 *
	 *  Extending the original [http://api.jquery.com/before/ jQuery().before()]
	 *  to render [jQuery.View] templates inserted before each element in the set of matched elements.
	 *
	 *  	$('#test').before('path/to/template.ejs', { name : 'javascriptmvc' });
	 *
	 *  @param {String|Object|Function} content A template filename or the id of a view script tag
	 *  or a DOM element, array of elements, HTML string, or jQuery object.
	 *  @param {Object} [data] The data to render the view with.
	 *  If rendering a view template this parameter always has to be present
	 *  (use the empty object initializer {} for no data).
	 */
	"before",
	/**
	 *  @function text
	 *  @parent jQuery.View
	 *
	 *  Extending the original [http://api.jquery.com/text/ jQuery().text()]
	 *  to render [jQuery.View] templates as the content of each matched element.
	 *  Unlike [jQuery.fn.html] jQuery.fn.text also works with XML, escaping the provided
	 *  string as necessary.
	 *
	 *  	$('#test').text('path/to/template.ejs', { name : 'javascriptmvc' });
	 *
	 *  @param {String|Object|Function} content A template filename or the id of a view script tag
	 *  or a DOM element, array of elements, HTML string, or jQuery object.
	 *  @param {Object} [data] The data to render the view with.
	 *  If rendering a view template this parameter always has to be present
	 *  (use the empty object initializer {} for no data).
	 */
	"text",
	/**
	 *  @function html
	 *  @parent jQuery.View
	 *
	 *  Extending the original [http://api.jquery.com/html/ jQuery().html()]
	 *  to render [jQuery.View] templates as the content of each matched element.
	 *
	 *  	$('#test').html('path/to/template.ejs', { name : 'javascriptmvc' });
	 *
	 *  @param {String|Object|Function} content A template filename or the id of a view script tag
	 *  or a DOM element, array of elements, HTML string, or jQuery object.
	 *  @param {Object} [data] The data to render the view with.
	 *  If rendering a view template this parameter always has to be present
	 *  (use the empty object initializer {} for no data).
	 */
	"html",
	/**
	 *  @function replaceWith
	 *  @parent jQuery.View
	 *
	 *  Extending the original [http://api.jquery.com/replaceWith/ jQuery().replaceWith()]
	 *  to render [jQuery.View] templates replacing each element in the set of matched elements.
	 *
	 *  	$('#test').replaceWith('path/to/template.ejs', { name : 'javascriptmvc' });
	 *
	 *  @param {String|Object|Function} content A template filename or the id of a view script tag
	 *  or a DOM element, array of elements, HTML string, or jQuery object.
	 *  @param {Object} [data] The data to render the view with.
	 *  If rendering a view template this parameter always has to be present
	 *  (use the empty object initializer {} for no data).
	 */
	"replaceWith", "val"],function(i, func){
		convert(func);
	});

	//go through helper funcs and convert


})();(function(){

	// HELPER METHODS ==============
	var myEval = function( script ) {
		eval(script);
	},
		// removes the last character from a string
		// this is no longer needed
		// chop = function( string ) {
		//	return string.substr(0, string.length - 1);
		//},
		rSplit = $.String.rsplit,
		extend = $.extend,
		isArray = $.isArray,
		// regular expressions for caching
		returnReg = /\r\n/g,
		retReg = /\r/g,
		newReg = /\n/g,
		nReg = /\n/,
		slashReg = /\\/g,
		quoteReg = /"/g,
		singleQuoteReg = /'/g,
		tabReg = /\t/g,
		leftBracket = /\{/g,
		rightBracket = /\}/g,
		quickFunc = /\s*\(([\$\w]+)\)\s*->([^\n]*)/,
		// escapes characters starting with \
		clean = function( content ) {
			return content.replace(slashReg, '\\\\').replace(newReg, '\\n').replace(quoteReg, '\\"').replace(tabReg, '\\t');
		},
		// escapes html
		// - from prototype  http://www.prototypejs.org/
		escapeHTML = function( content ) {
			return content.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(quoteReg, '&#34;').replace(singleQuoteReg, "&#39;");
		},
		$View = $.View,
		bracketNum = function(content){
			var lefts = content.match(leftBracket),
				rights = content.match(rightBracket);

			return (lefts ? lefts.length : 0) -
				   (rights ? rights.length : 0);
		},
		/**
		 * @class jQuery.EJS
		 *
		 * @plugin jquery/view/ejs
		 * @parent jQuery.View
		 * @download  http://jmvcsite.heroku.com/pluginify?plugins[]=jquery/view/ejs/ejs.js
		 * @test jquery/view/ejs/qunit.html
		 *
		 *
		 * Ejs provides <a href="http://www.ruby-doc.org/stdlib/libdoc/erb/rdoc/">ERB</a>
		 * style client side templates.  Use them with controllers to easily build html and inject
		 * it into the DOM.
		 *
		 * ###  Example
		 *
		 * The following generates a list of tasks:
		 *
		 * @codestart html
		 * &lt;ul>
		 * &lt;% for(var i = 0; i < tasks.length; i++){ %>
		 *     &lt;li class="task &lt;%= tasks[i].identity %>">&lt;%= tasks[i].name %>&lt;/li>
		 * &lt;% } %>
		 * &lt;/ul>
		 * @codeend
		 *
		 * For the following examples, we assume this view is in <i>'views\tasks\list.ejs'</i>.
		 *
		 *
		 * ## Use
		 *
		 * ### Loading and Rendering EJS:
		 *
		 * You should use EJS through the helper functions [jQuery.View] provides such as:
		 *
		 *   - [jQuery.fn.after after]
		 *   - [jQuery.fn.append append]
		 *   - [jQuery.fn.before before]
		 *   - [jQuery.fn.html html],
		 *   - [jQuery.fn.prepend prepend],
		 *   - [jQuery.fn.replaceWith replaceWith], and
		 *   - [jQuery.fn.text text].
		 *
		 * or [jQuery.Controller.prototype.view].
		 *
		 * ### Syntax
		 *
		 * EJS uses 5 types of tags:
		 *
		 *   - <code>&lt;% CODE %&gt;</code> - Runs JS Code.
		 *     For example:
		 *
		 *         <% alert('hello world') %>
		 *
		 *   - <code>&lt;%= CODE %&gt;</code> - Runs JS Code and writes the _escaped_ result into the result of the template.
		 *     For example:
		 *
		 *         <h1><%= 'hello world' %></h1>
		 *
		 *   - <code>&lt;%== CODE %&gt;</code> - Runs JS Code and writes the _unescaped_ result into the result of the template.
		 *     For example:
		 *
		 *         <h1><%== '<span>hello world</span>' %></h1>
		 *
		 *   - <code>&lt;%%= CODE %&gt;</code> - Writes <%= CODE %> to the result of the template.  This is very useful for generators.
		 *
		 *         <%%= 'hello world' %>
		 *
		 *   - <code>&lt;%# CODE %&gt;</code> - Used for comments.  This does nothing.
		 *
		 *         <%# 'hello world' %>
		 *
		 * ## Hooking up controllers
		 *
		 * After drawing some html, you often want to add other widgets and plugins inside that html.
		 * View makes this easy.  You just have to return the Contoller class you want to be hooked up.
		 *
		 * @codestart
		 * &lt;ul &lt;%= Mxui.Tabs%>>...&lt;ul>
		 * @codeend
		 *
		 * You can even hook up multiple controllers:
		 *
		 * @codestart
		 * &lt;ul &lt;%= [Mxui.Tabs, Mxui.Filler]%>>...&lt;ul>
		 * @codeend
		 *
		 * To hook up a controller with options or any other jQuery plugin use the
		 * [jQuery.EJS.Helpers.prototype.plugin | plugin view helper]:
		 *
		 * @codestart
		 * &lt;ul &lt;%= plugin('mxui_tabs', { option: 'value' }) %>>...&lt;ul>
		 * @codeend
		 *
		 * Don't add a semicolon when using view helpers.
		 *
		 *
		 * <h2>View Helpers</h2>
		 * View Helpers return html code.  View by default only comes with
		 * [jQuery.EJS.Helpers.prototype.view view] and [jQuery.EJS.Helpers.prototype.text text].
		 * You can include more with the view/helpers plugin.  But, you can easily make your own!
		 * Learn how in the [jQuery.EJS.Helpers Helpers] page.
		 *
		 * @constructor Creates a new view
		 * @param {Object} options A hash with the following options
		 * <table class="options">
		 *     <tbody><tr><th>Option</th><th>Default</th><th>Description</th></tr>
		 *     <tr>
		 *      <td>text</td>
		 *      <td>&nbsp;</td>
		 *      <td>uses the provided text as the template. Example:<br/><code>new View({text: '&lt;%=user%>'})</code>
		 *      </td>
		 *     </tr>
		 *     <tr>
		 *      <td>type</td>
		 *      <td>'<'</td>
		 *      <td>type of magic tags.  Options are '&lt;' or '['
		 *      </td>
		 *     </tr>
		 *     <tr>
		 *      <td>name</td>
		 *      <td>the element ID or url </td>
		 *      <td>an optional name that is used for caching.
		 *      </td>
		 *     </tr>
		 *    </tbody></table>
		 */
		EJS = function( options ) {
			// If called without new, return a function that
			// renders with data and helpers like
			// EJS({text: '<%= message %>'})({message: 'foo'});
			// this is useful for steal's build system
			if ( this.constructor != EJS ) {
				var ejs = new EJS(options);
				return function( data, helpers ) {
					return ejs.render(data, helpers);
				};
			}
			// if we get a function directly, it probably is coming from
			// a steal-packaged view
			if ( typeof options == "function" ) {
				this.template = {
					fn: options
				};
				return;
			}
			//set options on self
			extend(this, EJS.options, options);
			this.template = compile(this.text, this.type, this.name);
		};
	// add EJS to jQuery if it exists
	$ && ($.EJS = EJS);
	/**
	 * @Prototype
	 */
	EJS.prototype.
	/**
	 * Renders an object with view helpers attached to the view.
	 *
	 *     new EJS({text: "<%= message %>"}).render({
	 *       message: "foo"
	 *     },{helper: function(){ ... }})
	 *
	 * @param {Object} object data to be rendered
	 * @param {Object} [extraHelpers] an object with view helpers
	 * @return {String} returns the result of the string
	 */
	render = function( object, extraHelpers ) {
		object = object || {};
		this._extra_helpers = extraHelpers;
		var v = new EJS.Helpers(object, extraHelpers || {});
		return this.template.fn.call(object, object, v);
	};
	/**
	 * @Static
	 */

	extend(EJS, {
		/**
		 * Used to convert what's in &lt;%= %> magic tags to a string
		 * to be inserted in the rendered output.
		 *
		 * Typically, it's a string, and the string is just inserted.  However,
		 * if it's a function or an object with a hookup method, it can potentially be
		 * be ran on the element after it's inserted into the page.
		 *
		 * This is a very nice way of adding functionality through the view.
		 * Usually this is done with [jQuery.EJS.Helpers.prototype.plugin]
		 * but the following fades in the div element after it has been inserted:
		 *
		 * @codestart
		 * &lt;%= function(el){$(el).fadeIn()} %>
		 * @codeend
		 *
		 * @param {String|Object|Function} input the value in between the
		 * write magic tags: &lt;%= %>
		 * @return {String} returns the content to be added to the rendered
		 * output.  The content is different depending on the type:
		 *
		 *   * string - the original string
		 *   * null or undefined - the empty string ""
		 *   * an object with a hookup method - the attribute "data-view-id='XX'", where XX is a hookup number for jQuery.View
		 *   * a function - the attribute "data-view-id='XX'", where XX is a hookup number for jQuery.View
		 *   * an array - the attribute "data-view-id='XX'", where XX is a hookup number for jQuery.View
		 */
		text: function( input ) {
			// if it's a string, return
			if ( typeof input == 'string' ) {
				return input;
			}
			// if has no value
			if ( input === null || input === undefined ) {
				return '';
			}

			// if it's an object, and it has a hookup method
			var hook = (input.hookup &&
			// make a function call the hookup method

			function( el, id ) {
				input.hookup.call(input, el, id);
			}) ||
			// or if it's a function, just use the input
			(typeof input == 'function' && input) ||
			// of it its an array, make a function that calls hookup or the function
			// on each item in the array
			(isArray(input) &&
			function( el, id ) {
				for ( var i = 0; i < input.length; i++ ) {
					input[i].hookup ? input[i].hookup(el, id) : input[i](el, id);
				}
			});
			// finally, if there is a funciton to hookup on some dom
			// pass it to hookup to get the data-view-id back
			if ( hook ) {
				return "data-view-id='" + $View.hookup(hook) + "'";
			}
			// finally, if all else false, toString it
			return input.toString ? input.toString() : "";
		},
		/**
		 * Escapes the text provided as html if it's a string.
		 * Otherwise, the value is passed to EJS.text(text).
		 *
		 * @param {String|Object|Array|Function} text to escape.  Otherwise,
		 * the result of [jQuery.EJS.text] is returned.
		 * @return {String} the escaped text or likely a $.View data-view-id attribute.
		 */
		clean: function( text ) {
			//return sanatized text
			if ( typeof text == 'string' ) {
				return escapeHTML(text);
			} else if ( typeof text == 'number' ) {
				return text;
			} else {
				return EJS.text(text);
			}
		},
		/**
		 * @attribute options
		 * Sets default options for all views.
		 *
		 *     $.EJS.options.type = '['
		 *
		 * Only one option is currently supported: type.
		 *
		 * Type is the left hand magic tag.
		 */
		options: {
			type: '[',
			ext: '.ejs'
		}
	});
	// ========= SCANNING CODE =========
	// Given a scanner, and source content, calls block  with each token
	// scanner - an object of magicTagName : values
	// source - the source you want to scan
	// block - function(token, scanner), called with each token
	var scan = function( scanner, source, block ) {
		// split on /\n/ to have new lines on their own line.
		var source_split = rSplit(source, nReg),
			i = 0;
		for (; i < source_split.length; i++ ) {
			scanline(scanner, source_split[i], block);
		}

	},
		scanline = function( scanner, line, block ) {
			scanner.lines++;
			var line_split = rSplit(line, scanner.splitter),
				token;
			for ( var i = 0; i < line_split.length; i++ ) {
				token = line_split[i];
				if ( token !== null ) {
					block(token, scanner);
				}
			}
		},
		// creates a 'scanner' object.  This creates
		// values for the left and right magic tags
		// it's splitter property is a regexp that splits content
		// by all tags
		makeScanner = function( left, right ) {
			var scanner = {};
			extend(scanner, {
				left: left + '%',
				right: '%' + right,
				dLeft: left + '%%',
				dRight: '%%' + right,
				eeLeft : left + '%==',
				eLeft: left + '%=',
				cmnt: left + '%#',
				cleanLeft: left+"%~",
				scan: scan,
				lines: 0
			});
			scanner.splitter = new RegExp("(" + [scanner.dLeft, scanner.dRight, scanner.eeLeft, scanner.eLeft, scanner.cmnt, scanner.left, scanner.right + '\n', scanner.right, '\n'].join(")|(").
			replace(/\[/g, "\\[").replace(/\]/g, "\\]") + ")");
			return scanner;
		},


		// compiles a template where
		// source - template text
		// left - the left magic tag
		// name - the name of the template (for debugging)
		// returns an object like: {out : "", fn : function(){ ... }} where
		//   out -  the converted JS source of the view
		//   fn - a function made from the JS source
		compile = function( source, left, name ) {
			// make everything only use \n
			source = source.replace(returnReg, "\n").replace(retReg, "\n");
			// if no left is given, assume <
			left = left || '[';

			// put and insert cmds are used for adding content to the template
			// currently they are identical, I am not sure why
			var put_cmd = "___v1ew.push(",
				insert_cmd = put_cmd,
				// the text that starts the view code (or block function)
				startTxt = 'var ___v1ew = [];',
				// the text that ends the view code (or block function)
				finishTxt = "return ___v1ew.join('')",
				// initialize a buffer
				buff = new EJS.Buffer([startTxt], []),
				// content is used as the current 'processing' string
				// this is the content between magic tags
				content = '',
				// adds something to be inserted into the view template
				// this comes out looking like __v1ew.push("CONENT")
				put = function( content ) {
					buff.push(put_cmd, '"', clean(content), '");');
				},
				// the starting magic tag
				startTag = null,
				// cleans the running content
				empty = function() {
					content = ''
				},
				// what comes after clean or text
				doubleParen = "));",
				// a stack used to keep track of how we should end a bracket }
				// once we have a <%= %> with a leftBracket
				// we store how the file should end here (either '))' or ';' )
				endStack =[];

			// start going token to token
			scan(makeScanner(left, left === '[' ? ']' : '>'), source || "", function( token, scanner ) {
				// if we don't have a start pair
				var bn;
				if ( startTag === null ) {
					switch ( token ) {
					case '\n':
						content = content + "\n";
						put(content);
						buff.cr();
						empty();
						break;
						// set start tag, add previous content (if there is some)
						// clean content
					case scanner.left:
					case scanner.eLeft:
					case scanner.eeLeft:
					case scanner.cmnt:
						// a new line, just add whatever content w/i a clean
						// reset everything
						startTag = token;
						if ( content.length > 0 ) {
							put(content);
						}
						empty();
						break;

					case scanner.dLeft:
						// replace <%% with <%
						content += scanner.left;
						break;
					default:
						content += token;
						break;
					}
				}
				else {
					//we have a start tag
					switch ( token ) {
					case scanner.right:
						// %>
						switch ( startTag ) {
						case scanner.left:
							// <%

							// get the number of { minus }
							bn = bracketNum(content);
							// how are we ending this statement
							var last =
								// if the stack has value and we are ending a block
								endStack.length && bn == -1 ?
								// use the last item in the block stack
								endStack.pop() :
								// or use the default ending
								";";

							// if we are ending a returning block
							// add the finish text which returns the result of the
							// block
							if(last === doubleParen) {
								buff.push(finishTxt)
							}
							// add the remaining content
							buff.push(content, last);

							// if we have a block, start counting
							if(bn === 1 ){
								endStack.push(";")
							}
							break;
						case scanner.eLeft:
							// <%= clean content
							bn = bracketNum(content);
							if( bn ) {
								endStack.push(doubleParen)
							}
							if(quickFunc.test(content)){
								var parts = content.match(quickFunc)
								content = "function(__){var "+parts[1]+"=$(__);"+parts[2]+"}"
							}
							buff.push(insert_cmd, $.globalNamespace + ".EJS.clean(", content,bn ? startTxt : doubleParen);
							break;
						case scanner.eeLeft:
							// <%== content

							// get the number of { minus }
							bn = bracketNum(content);
							// if we have more {, it means there is a block
							if( bn ){
								// when we return to the same # of { vs } end wiht a doubleParen
								endStack.push(doubleParen)
							}

							buff.push(insert_cmd, $.globalNamespace + ".EJS.text(", content,
								// if we have a block
								bn ?
								// start w/ startTxt "var _v1ew = [])"
								startTxt :
								// if not, add doubleParent to close push and text
								doubleParen
								);
							break;
						}
						startTag = null;
						empty();
						break;
					case scanner.dRight:
						content += scanner.right;
						break;
					default:
						content += token;
						break;
					}
				}
			})
			if ( content.length > 0 ) {
				// Should be content.dump in Ruby
				buff.push(put_cmd, '"', clean(content) + '");');
			}
			var template = buff.close(),
				out = {
					out: 'try { with(_VIEW) { with (_CONTEXT) {' + template + " "+finishTxt+"}}}catch(e){e.lineNumber=null;throw e;}"
				};
			//use eval instead of creating a function, b/c it is easier to debug
			// myEval.call(out, 'this.fn = (function(_CONTEXT,_VIEW){' + out.out + '});\r\n//@ sourceURL=' + name + ".js");

			// !-- FOUNDRY HACK --! //
			// Removed //@ sourceURL as it will break with conditional compilation turned on in IE.
			myEval.call(out, 'this.fn = (function(_CONTEXT,_VIEW){ var $ = ' + $.globalNamespace + ';' + out.out + '});');

			return out;
		};


	// A Buffer used to add content to.
	// This is useful for performance and simplifying the
	// code above.
	// We also can use this so we know line numbers when there
	// is an error.
	// pre_cmd - code that sets up the buffer
	// post - code that finalizes the buffer
	EJS.Buffer = function( pre_cmd, post ) {
		// the current line we are on
		this.line = [];
		// the combined content added to this buffer
		this.script = [];
		// content at the end of the buffer
		this.post = post;
		// add the pre commands to the first line
		this.push.apply(this, pre_cmd);
	};
	EJS.Buffer.prototype = {
		// add content to this line
		// need to maintain your own semi-colons (for performance)
		push: function() {
			this.line.push.apply(this.line, arguments);
		},
		// starts a new line
		cr: function() {
			this.script.push(this.line.join(''), "\n");
			this.line = [];
		},
		//returns the script too
		close: function() {
			// if we have ending line content, add it to the script
			if ( this.line.length > 0 ) {
				this.script.push(this.line.join(''));
				this.line = [];
			}
			// if we have ending content, add it
			this.post.length && this.push.apply(this, this.post);
			// always end in a ;
			this.script.push(";");
			return this.script.join("");
		}

	};

	/**
	 * @class jQuery.EJS.Helpers
	 * @parent jQuery.EJS
	 * By adding functions to jQuery.EJS.Helpers.prototype, those functions will be available in the
	 * views.
	 *
	 * The following helper converts a given string to upper case:
	 *
	 * 	$.EJS.Helpers.prototype.toUpper = function(params)
	 * 	{
	 * 		return params.toUpperCase();
	 * 	}
	 *
	 * Use it like this in any EJS template:
	 *
	 * 	<%= toUpper('javascriptmvc') %>
	 *
	 * To access the current DOM element return a function that takes the element as a parameter:
	 *
	 * 	$.EJS.Helpers.prototype.upperHtml = function(params)
	 * 	{
	 * 		return function(el) {
	 * 			$(el).html(params.toUpperCase());
	 * 		}
	 * 	}
	 *
	 * In your EJS view you can then call the helper on an element tag:
	 *
	 * 	<div <%= upperHtml('javascriptmvc') %>></div>
	 *
	 *
	 * @constructor Creates a view helper.  This function
	 * is called internally.  You should never call it.
	 * @param {Object} data The data passed to the
	 * view.  Helpers have access to it through this._data
	 */
	EJS.Helpers = function( data, extras ) {
		this._data = data;
		this._extras = extras;
		extend(this, extras);
	};
	/**
	 * @prototype
	 */
	EJS.Helpers.prototype = {
		/**
		 * Hooks up a jQuery plugin on.
		 * @param {String} name the plugin name
		 */
		plugin: function( name ) {
			var args = $.makeArray(arguments),
				widget = args.shift();
			return function( el ) {
				var jq = $(el);
				jq[widget].apply(jq, args);
			};
		},
		/**
		 * Renders a partial view.  This is deprecated in favor of <code>$.View()</code>.
		 */
		view: function( url, data, helpers ) {
			helpers = helpers || this._extras;
			data = data || this._data;
			return $View(url, data, helpers); //new EJS(options).render(data, helpers);
		}
	};

	// options for steal's build
	$View.register({
		suffix: "ejs",
		//returns a function that renders the view
		script: function( id, src ) {
			return $.globalNamespace + ".EJS(function(_CONTEXT,_VIEW) { " + new EJS({
				text: src,
				name: id
			}).template.out + " })";
		},
		renderer: function( id, text ) {
			return EJS({
				text: text,
				name: id
			});
		}
	});
})();(function(){

	// Alias helpful methods from jQuery
	var isArray = $.isArray,
		isObject = function( obj ) {
			return typeof obj === 'object' && obj !== null && obj;
		},
		makeArray = $.makeArray,
		each = $.each,
		// listens to changes on val and 'bubbles' the event up
		// - val the object to listen to changes on
		// - prop the property name val is at on
		// - parent the parent object of prop
		hookup = function( val, prop, parent ) {
			// if it's an array make a list, otherwise a val
			if (val instanceof $.Observe){
				// we have an observe already
				// make sure it is not listening to this already
				unhookup([val], parent._namespace)
			} else if ( isArray(val) ) {
				val = new $.Observe.List(val)
			} else {
				val = new $.Observe(val)
			}
			// attr (like target, how you (delegate) to get to the target)
            // currentAttr (how to get to you)
            // delegateAttr (hot to get to the delegated Attr)

			//
			//
			//listen to all changes and trigger upwards
			val.bind("change" + parent._namespace, function( ev, attr ) {
				// trigger the type on this ...
				var args = $.makeArray(arguments),
					ev = args.shift();
				if(prop === "*"){
					args[0] = parent.indexOf(val)+"." + args[0]
				} else {
					args[0] = prop +  "." + args[0]
				}
				// change the attr
				//ev.origTarget = ev.origTarget || ev.target;
				// the target should still be the original object ...
				$.event.trigger(ev, args, parent)
			});

			return val;
		},
		unhookup = function(items, namespace){
			var item;
			for(var i =0; i < items.length; i++){
				item = items[i]
				if(  item && item.unbind ){
					item.unbind("change" + namespace)
				}
			}
		},
		// an id to track events for a given observe
		id = 0,
		collecting = null,
		// call to start collecting events (Observe sends all events at once)
		collect = function() {
			if (!collecting ) {
				collecting = [];
				return true;
			}
		},
		// creates an event on item, but will not send immediately
		// if collecting events
		// - item - the item the event should happen on
		// - event - the event name ("change")
		// - args - an array of arguments
		trigger = function( item, event, args ) {
			// send no events if initalizing
			if (item._init) {
				return;
			}
			if (!collecting ) {
				return $.event.trigger(event, args, item, true)
			} else {
				collecting.push({
					t: item,
					ev: event,
					args: args
				})
			}
		},
		// which batch of events this is for, might not want to send multiple
		// messages on the same batch.  This is mostly for
		// event delegation
		batchNum = 0,
		// sends all pending events
		sendCollection = function() {
			var len = collecting.length,
				items = collecting.slice(0),
				cur;
			collecting = null;
			batchNum ++;
			for ( var i = 0; i < len; i++ ) {
				cur = items[i];
				// batchNum
				$.event.trigger({
					type: cur.ev,
					batchNum : batchNum
				}, cur.args, cur.t)
			}

		},
		// a helper used to serialize an Observe or Observe.List where:
		// observe - the observable
		// how - to serialize with 'attrs' or 'serialize'
		// where - to put properties, in a {} or [].
		serialize = function( observe, how, where ) {
			// go through each property
			observe.each(function( name, val ) {
				// if the value is an object, and has a attrs or serialize function
				where[name] = isObject(val) && typeof val[how] == 'function' ?
				// call attrs or serialize to get the original data back
				val[how]() :
				// otherwise return the value
				val
			})
			return where;
		};

	/**
	 * @class jQuery.Observe
	 * @parent jquerymx.lang
	 * @test jquery/lang/observe/qunit.html
	 *
	 * Observe provides the awesome observable pattern for
	 * JavaScript Objects and Arrays. It lets you
	 *
	 *   - Set and remove property or property values on objects and arrays
	 *   - Listen for changes in objects and arrays
	 *   - Work with nested properties
	 *
	 * ## Creating an $.Observe
	 *
	 * To create an $.Observe, or $.Observe.List, you can simply use
	 * the `$.O(data)` shortcut like:
	 *
	 *     var person = $.O({name: 'justin', age: 29}),
	 *         hobbies = $.O(['programming', 'basketball', 'nose picking'])
	 *
	 * Depending on the type of data passed to $.O, it will create an instance of either:
	 *
	 *   - $.Observe, which is used for objects like: `{foo: 'bar'}`, and
	 *   - [jQuery.Observe.List $.Observe.List], which is used for arrays like `['foo','bar']`
	 *
	 * $.Observe.List and $.Observe are very similar. In fact,
	 * $.Observe.List inherits $.Observe and only adds a few extra methods for
	 * manipulating arrays like [jQuery.Observe.List.prototype.push push].  Go to
	 * [jQuery.Observe.List $.Observe.List] for more information about $.Observe.List.
	 *
	 * You can also create a `new $.Observe` simply by pass it the data you want to observe:
	 *
	 *     var data = {
	 *       addresses : [
	 *         {
	 *           city: 'Chicago',
	 *           state: 'IL'
	 *         },
	 *         {
	 *           city: 'Boston',
	 *           state : 'MA'
	 *         }
	 *         ],
	 *       name : "Justin Meyer"
	 *     },
	 *     o = new $.Observe(data);
	 *
	 * _o_ now represents an observable copy of _data_.
	 *
	 * ## Getting and Setting Properties
	 *
	 * Use [jQuery.Observe.prototype.attr attr] and [jQuery.Observe.prototype.attr attrs]
	 * to get and set properties.
	 *
	 * For example, you can read the property values of _o_ with
	 * `observe.attr( name )` like:
	 *
	 *     // read name
	 *     o.attr('name') //-> Justin Meyer
	 *
	 * And set property names of _o_ with
	 * `observe.attr( name, value )` like:
	 *
	 *     // update name
	 *     o.attr('name', "Brian Moschel") //-> o
	 *
	 * Observe handles nested data.  Nested Objects and
	 * Arrays are converted to $.Observe and
	 * $.Observe.Lists.  This lets you read nested properties
	 * and use $.Observe methods on them.  The following
	 * updates the second address (Boston) to 'New York':
	 *
	 *     o.attr('addresses.1').attrs({
	 *       city: 'New York',
	 *       state: 'NY'
	 *     })
	 *
	 * `attrs()` can be used to get all properties back from the observe:
	 *
	 *     o.attrs() // ->
	 *     {
	 *       addresses : [
	 *         {
	 *           city: 'Chicago',
	 *           state: 'IL'
	 *         },
	 *         {
	 *           city: 'New York',
	 *           state : 'MA'
	 *         }
	 *       ],
	 *       name : "Brian Moschel"
	 *     }
	 *
	 * ## Listening to property changes
	 *
	 * When a property value is changed, it creates events
	 * that you can listen to.  There are two ways to listen
	 * for events:
	 *
	 *   - [jQuery.Observe.prototype.bind bind] - listen for any type of change
	 *   - [jQuery.Observe.prototype.delegate delegate] - listen to a specific type of change
	 *
	 * With `bind( "change" , handler( ev, attr, how, newVal, oldVal ) )`, you can listen
	 * to any change that happens within the
	 * observe. The handler gets called with the property name that was
	 * changed, how it was changed ['add','remove','set'], the new value
	 * and the old value.
	 *
	 *     o.bind('change', function( ev, attr, how, nevVal, oldVal ) {
	 *
	 *     })
	 *
	 * `delegate( attr, event, handler(ev, newVal, oldVal ) )` lets you listen
	 * to a specific event on a specific attribute.
	 *
	 *     // listen for name changes
	 *     o.delegate("name","set", function(){
	 *
	 *     })
	 *
	 * Delegate lets you specify multiple attributes and values to match
	 * for the callback. For example,
	 *
	 *     r = $.O({type: "video", id : 5})
	 *     r.delegate("type=images id","set", function(){})
	 *
	 * This is used heavily by [jQuery.route $.route].
	 *
	 * @constructor
	 *
	 * @param {Object} obj a JavaScript Object that will be
	 * converted to an observable
	 */
	$.Class($.globalNamespace + '.Observe',
	/**
	 * @prototype
	 */
	{
		init: function( obj ) {
			// _data is where we keep the properties
			this._data = {};
			// the namespace this object uses to listen to events
			this._namespace = ".observe" + (++id);
			// sets all attrs
			this._init = true;
			this.attrs(obj);
			delete this._init;
		},
		/**
		 * Get or set an attribute on the observe.
		 *
		 *     o = new $.Observe({});
		 *
		 *     // sets a user property
		 *     o.attr('user',{name: 'hank'});
		 *
		 *     // read the user's name
		 *     o.attr('user.name') //-> 'hank'
		 *
		 * If a value is set for the first time, it will trigger
		 * an `'add'` and `'set'` change event.  Once
		 * the value has been added.  Any future value changes will
		 * trigger only `'set'` events.
		 *
		 *
		 * @param {String} attr the attribute to read or write.
		 *
		 *     o.attr('name') //-> reads the name
		 *     o.attr('name', 'Justin') //-> writes the name
		 *
		 * You can read or write deep property names.  For example:
		 *
		 *     o.attr('person', {name: 'Justin'})
		 *     o.attr('person.name') //-> 'Justin'
		 *
		 * @param {Object} [val] if provided, sets the value.
		 * @return {Object} the observable or the attribute property.
		 *
		 * If you are reading, the property value is returned:
		 *
		 *     o.attr('name') //-> Justin
		 *
		 * If you are writing, the observe is returned for chaining:
		 *
		 *     o.attr('name',"Brian").attr('name') //-> Justin
		 */
		attr: function( attr, val ) {

			if ( val === undefined ) {
				// if we are getting a value
				return this._get(attr)
			} else {
				// otherwise we are setting
				this._set(attr, val);
				return this;
			}
		},
		/**
		 * Iterates through each attribute, calling handler
		 * with each attribute name and value.
		 *
		 *     new Observe({foo: 'bar'})
		 *       .each(function(name, value){
		 *         equals(name, 'foo')
		 *         equals(value,'bar')
		 *       })
		 *
		 * @param {function} handler(attrName,value) A function that will get
		 * called back with the name and value of each attribute on the observe.
		 *
		 * Returning `false` breaks the looping.  The following will never
		 * log 3:
		 *
		 *     new Observe({a : 1, b : 2, c: 3})
		 *       .each(function(name, value){
		 *         console.log(value)
		 *         if(name == 2){
		 *           return false;
		 *         }
		 *       })
		 *
		 * @return {jQuery.Observe} the original observable.
		 */
		each: function() {
			return each.apply(null, [this.__get()].concat(makeArray(arguments)))
		},
		/**
		 * Removes a property
		 *
		 *     o =  new $.Observe({foo: 'bar'});
		 *     o.removeAttr('foo'); //-> 'bar'
		 *
		 * This creates a `'remove'` change event. Learn more about events
		 * in [jQuery.Observe.prototype.bind bind] and [jQuery.Observe.prototype.delegate delegate].
		 *
		 * @param {String} attr the attribute name to remove.
		 * @return {Object} the value that was removed.
		 */
		removeAttr: function( attr ) {
			// convert the attr into parts (if nested)
			var parts = isArray(attr) ? attr : attr.split("."),
				// the actual property to remove
				prop = parts.shift(),
				// the current value
				current = this._data[prop];

			// if we have more parts, call removeAttr on that part
			if ( parts.length ) {
				return current.removeAttr(parts)
			} else {
				// otherwise, delete
				delete this._data[prop];
				// create the event
				trigger(this, "change", [prop, "remove", undefined, current]);
				return current;
			}
		},
		// reads a property from the object
		_get: function( attr ) {
			var parts = isArray(attr) ? attr : (""+attr).split("."),
				current = this.__get(parts.shift());
			if ( parts.length ) {
				return current ? current._get(parts) : undefined
			} else {
				return current;
			}
		},
		// reads a property directly if an attr is provided, otherwise
		// returns the 'real' data object itself
		__get: function( attr ) {
			return attr ? this._data[attr] : this._data;
		},
		// sets attr prop as value on this object where
		// attr - is a string of properties or an array  of property values
		// value - the raw value to set
		// description - an object with converters / serializers / defaults / getterSetters?
		_set: function( attr, value ) {
			// convert attr to attr parts (if it isn't already)
			var parts = isArray(attr) ? attr : ("" + attr).split("."),
				// the immediate prop we are setting
				prop = parts.shift(),
				// its current value
				current = this.__get(prop);

			// if we have an object and remaining parts
			if ( isObject(current) && parts.length ) {
				// that object should set it (this might need to call attr)
				current._set(parts, value)
			} else if (!parts.length ) {
				// otherwise, we are setting it on this object
				// todo: check if value is object and transform
				// are we changing the value
				if ( value !== current ) {

					// check if we are adding this for the first time
					// if we are, we need to create an 'add' event
					var changeType = this.__get().hasOwnProperty(prop) ? "set" : "add";

					// set the value on data
					this.__set(prop,
					// if we are getting an object
					isObject(value) ?
					// hook it up to send event to us
					hookup(value, prop, this) :
					// value is normal
					value);



					// trigger the change event
					trigger(this, "change", [prop, changeType, value, current]);

					// if we can stop listening to our old value, do it
					current && unhookup([current], this._namespace);
				}

			} else {
				throw "jQuery.Observe: set a property on an object that does not exist"
			}
		},
		// directly sets a property on this object
		__set: function( prop, val ) {
			this._data[prop] = val;
			// add property directly for easy writing
			// check if its on the prototype so we don't overwrite methods like attrs
			if (!(prop in this.constructor.prototype)) {
				this[prop] = val
			}
		},
		/**
		 * Listens to changes on a jQuery.Observe.
		 *
		 * When attributes of an observe change, including attributes on nested objects,
		 * a `'change'` event is triggered on the observe.  These events come
		 * in three flavors:
		 *
		 *   - `add` - a attribute is added
		 *   - `set` - an existing attribute's value is changed
		 *   - `remove` - an attribute is removed
		 *
		 * The change event is fired with:
		 *
		 *  - the attribute changed
		 *  - how it was changed
		 *  - the newValue of the attribute
		 *  - the oldValue of the attribute
		 *
		 * Example:
		 *
		 *     o = new $.Observe({name : "Payal"});
		 *     o.bind('change', function(ev, attr, how, newVal, oldVal){
		 *       // ev    -> {type: 'change'}
		 *       // attr  -> "name"
		 *       // how   -> "add"
		 *       // newVal-> "Justin"
		 *       // oldVal-> undefined
		 *     })
		 *
		 *     o.attr('name', 'Justin')
		 *
		 * Listening to `change` is only useful for when you want to
		 * know every change on an Observe.  For most applications,
		 * [jQuery.Observe.prototype.delegate delegate] is
		 * much more useful as it lets you listen to specific attribute
		 * changes and sepecific types of changes.
		 *
		 *
		 * @param {String} eventType the event name.  Currently,
		 * only 'change' events are supported. For more fine
		 * grained control, use [jQuery.Observe.prototype.delegate].
		 *
		 * @param {Function} handler(event, attr, how, newVal, oldVal) A
		 * callback function where
		 *
		 *   - event - the event
		 *   - attr - the name of the attribute changed
		 *   - how - how the attribute was changed (add, set, remove)
		 *   - newVal - the new value of the attribute
		 *   - oldVal - the old value of the attribute
		 *
		 * @return {$.Observe} the observe for chaining.
		 */
		bind: function( eventType, handler ) {
			$.fn.bind.apply($([this]), arguments);
			return this;
		},
		/**
		 * Unbinds a listener.  This uses [http://api.jquery.com/unbind/ jQuery.unbind]
		 * and works very similar.  This means you can
		 * use namespaces or unbind all event handlers for a given event:
		 *
		 *     // unbind a specific event handler
		 *     o.unbind('change', handler)
		 *
		 *     // unbind all change event handlers bound with the
		 *     // foo namespace
		 *     o.unbind('change.foo')
		 *
		 *     // unbind all change event handlers
		 *     o.unbind('change')
		 *
		 * @param {String} eventType - the type of event with
		 * any optional namespaces.  Currently, only `change` events
		 * are supported with bind.
		 *
		 * @param {Function} [handler] - The original handler function passed
		 * to [jQuery.Observe.prototype.bind bind].
		 *
		 * @return {jQuery.Observe} the original observe for chaining.
		 */
		unbind: function( eventType, handler ) {
			$.fn.unbind.apply($([this]), arguments);
			return this;
		},
		/**
		 * Get the serialized Object form of the observe.  Serialized
		 * data is typically used to send back to a server.
		 *
		 *     o.serialize() //-> { name: 'Justin' }
		 *
		 * Serialize currently returns the same data
		 * as [jQuery.Observe.prototype.attrs].  However, in future
		 * versions, serialize will be able to return serialized
		 * data similar to [jQuery.Model].  The following will work:
		 *
		 *     new Observe({time: new Date()})
		 *       .serialize() //-> { time: 1319666613663 }
		 *
		 * @return {Object} a JavaScript Object that can be
		 * serialized with `JSON.stringify` or other methods.
		 *
		 */
		serialize: function() {
			return serialize(this, 'serialize', {});
		},
		/**
		 * Set multiple properties on the observable
		 * @param {Object} props
		 * @param {Boolean} remove true if you should remove properties that are not in props
		 */
		attrs: function( props, remove ) {
			if ( props === undefined ) {
				return serialize(this, 'attrs', {})
			}

			props = $.extend(true, {}, props);
			var prop, collectingStarted = collect();

			for ( prop in this._data ) {
				var curVal = this._data[prop],
					newVal = props[prop];

				// if we are merging ...
				if ( newVal === undefined ) {
					remove && this.removeAttr(prop);
					continue;
				}
				if ( isObject(curVal) && isObject(newVal) ) {
					curVal.attrs(newVal, remove)
				} else if ( curVal != newVal ) {
					this._set(prop, newVal)
				} else {

				}
				delete props[prop];
			}
			// add remaining props
			for ( var prop in props ) {
				newVal = props[prop];
				this._set(prop, newVal)
			}
			if ( collectingStarted ) {
				sendCollection();
			}
		}
	});
	// Helpers for list
	/**
	 * @class jQuery.Observe.List
	 * @inherits jQuery.Observe
	 * @parent jQuery.Observe
	 *
	 * An observable list.  You can listen to when items are push, popped,
	 * spliced, shifted, and unshifted on this array.
	 *
	 *
	 */
	var list = $.Observe($.globalNamespace + '.Observe.List',
	/**
	 * @prototype
	 */
	{
		init: function( instances, options ) {
			this.length = 0;
			this._namespace = ".list" + (++id);
			this._init = true;
			this.bind('change',this.proxy('_changes'));
			this.push.apply(this, makeArray(instances || []));
			$.extend(this, options);
			if(this.comparator){
				this.sort()
			}
			delete this._init;
		},
		_changes : function(ev, attr, how, newVal, oldVal){
			// detects an add, sorts it, re-adds?
			//console.log("")



			// if we are sorting, and an attribute inside us changed
			if(this.comparator && /^\d+./.test(attr) ) {

				// get the index
				var index = +(/^\d+/.exec(attr)[0]),
					// and item
					item = this[index],
					// and the new item
					newIndex = this.sortedIndex(item);

				if(newIndex !== index){
					// move ...
					[].splice.call(this, index, 1);
					[].splice.call(this, newIndex, 0, item);

					trigger(this, "move", [item, newIndex, index]);
					ev.stopImmediatePropagation();
					trigger(this,"change", [
						attr.replace(/^\d+/,newIndex),
						how,
						newVal,
						oldVal
					]);
					return;
				}
			}


			// if we add items, we need to handle
			// sorting and such

			// trigger direct add and remove events ...
			if(attr.indexOf('.') === -1){

				if( how === 'add' ) {
					trigger(this, how, [newVal,+attr]);
				} else if( how === 'remove' ) {
					trigger(this, how, [oldVal, +attr])
				}

			}
			// issue add, remove, and move events ...
		},
		sortedIndex : function(item){
			var itemCompare = item.attr(this.comparator),
				equaled = 0,
				i;
			for(var i =0; i < this.length; i++){
				if(item === this[i]){
					equaled = -1;
					continue;
				}
				if(itemCompare <= this[i].attr(this.comparator) ) {
					return i+equaled;
				}
			}
			return i+equaled;
		},
		__get : function(attr){
			return attr ? this[attr] : this;
		},
		__set : function(attr, val){
			this[attr] = val;
		},
		/**
		 * Returns the serialized form of this list.
		 */
		serialize: function() {
			return serialize(this, 'serialize', []);
		},
		/**
		 * Iterates through each item of the list, calling handler
		 * with each index and value.
		 *
		 *     new Observe.List(['a'])
		 *       .each(function(index, value){
		 *         equals(index, 1)
		 *         equals(value,'a')
		 *       })
		 *
		 * @param {function} handler(index,value) A function that will get
		 * called back with the index and value of each item on the list.
		 *
		 * Returning `false` breaks the looping.  The following will never
		 * log 'c':
		 *
		 *     new Observe(['a','b','c'])
		 *       .each(function(index, value){
		 *         console.log(value)
		 *         if(index == 1){
		 *           return false;
		 *         }
		 *       })
		 *
		 * @return {jQuery.Observe.List} the original observable.
		 */
		// placeholder for each
		/**
		 * Remove items or add items from a specific point in the list.
		 *
		 * ### Example
		 *
		 * The following creates a list of numbers and replaces 2 and 3 with
		 * "a", and "b".
		 *
		 *     var l = new $.Observe.List([0,1,2,3]);
		 *
		 *     l.bind('change', function( ev, attr, how, newVals, oldVals, where ) { ... })
		 *
		 *     l.splice(1,2, "a", "b"); // results in [0,"a","b",3]
		 *
		 * This creates 2 change events.  The first event is the removal of
		 * numbers one and two where it's callback values will be:
		 *
		 *   - attr - "1" - indicates where the remove event took place
		 *   - how - "remove"
		 *   - newVals - undefined
		 *   - oldVals - [1,2] -the array of removed values
		 *   - where - 1 - the location of where these items where removed
		 *
		 * The second change event is the addition of the "a", and "b" values where
		 * the callback values will be:
		 *
		 *   - attr - "1" - indicates where the add event took place
		 *   - how - "added"
		 *   - newVals - ["a","b"]
		 *   - oldVals - [1, 2] - the array of removed values
		 *   - where - 1 - the location of where these items where added
		 *
		 * @param {Number} index where to start removing or adding items
		 * @param {Object} count the number of items to remove
		 * @param {Object} [added] an object to add to
		 */
		splice: function( index, count ) {
			var args = makeArray(arguments),
				i;

			for ( i = 2; i < args.length; i++ ) {
				var val = args[i];
				if ( isObject(val) ) {
					args[i] = hookup(val, "*", this)
				}
			}
			if ( count === undefined ) {
				count = args[1] = this.length - index;
			}
			var removed = [].splice.apply(this, args);
			if ( count > 0 ) {
				trigger(this, "change", [""+index, "remove", undefined, removed]);
				unhookup(removed, this._namespace);
			}
			if ( args.length > 2 ) {
				trigger(this, "change", [""+index, "add", args.slice(2), removed]);
			}
			return removed;
		},
		/**
		 * Updates an array with a new array.  It is able to handle
		 * removes in the middle of the array.
		 *
		 * @param {Array} props
		 * @param {Boolean} remove
		 */
		attrs: function( props, remove ) {
			if ( props === undefined ) {
				return serialize(this, 'attrs', []);
			}

			// copy
			props = props.slice(0);

			var len = Math.min(props.length, this.length),
				collectingStarted = collect();
			for ( var prop = 0; prop < len; prop++ ) {
				var curVal = this[prop],
					newVal = props[prop];

				if ( isObject(curVal) && isObject(newVal) ) {
					curVal.attrs(newVal, remove)
				} else if ( curVal != newVal ) {
					this._set(prop, newVal)
				} else {

				}
			}
			if ( props.length > this.length ) {
				// add in the remaining props
				this.push(props.slice(this.length))
			} else if ( props.length < this.length && remove ) {
				this.splice(props.length)
			}
			//remove those props didn't get too
			if ( collectingStarted ) {
				sendCollection()
			}
		},
		sort: function(method, silent){
			var comparator = this.comparator,
				args = comparator ? [function(a, b){
					a = a[comparator]
					b = b[comparator]
					return a === b ? 0 : (a < b ? -1 : 1);
				}] : [],
				res = [].sort.apply(this, args);

			!silent && trigger(this, "reset");

		}
	}),


		// create push, pop, shift, and unshift
		// converts to an array of arguments
		getArgs = function( args ) {
			if ( args[0] && ($.isArray(args[0])) ) {
				return args[0]
			}
			else {
				return makeArray(args)
			}
		};
	// describes the method and where items should be added
	each({
		/**
		 * @function push
		 * Add items to the end of the list.
		 *
		 *     var l = new $.Observe.List([]);
		 *
		 *     l.bind('change', function(
		 *         ev,        // the change event
		 *         attr,      // the attr that was changed, for multiple items, "*" is used
		 *         how,       // "add"
		 *         newVals,   // an array of new values pushed
		 *         oldVals,   // undefined
		 *         where      // the location where these items where added
		 *         ) {
		 *
		 *     })
		 *
		 *     l.push('0','1','2');
		 *
		 * @return {Number} the number of items in the array
		 */
		push: "length",
		/**
		 * @function unshift
		 * Add items to the start of the list.  This is very similar to
		 * [jQuery.Observe.prototype.push].
		 */
		unshift: 0
	},
	// adds a method where
	// - name - method name
	// - where - where items in the array should be added


	function( name, where ) {
		list.prototype[name] = function() {
			// get the items being added
			var args = getArgs(arguments),
				// where we are going to add items
				len = where ? this.length : 0;

			// go through and convert anything to an observe that needs to be converted
			for ( var i = 0; i < args.length; i++ ) {
				var val = args[i];
				if ( isObject(val) ) {
					args[i] = hookup(val, "*", this)
				}
			}

			// if we have a sort item, add that
			if( args.length == 1 && this.comparator ) {
				// add each item ...
				// we could make this check if we are already adding in order
				// but that would be confusing ...
				var index = this.sortedIndex(args[0]);
				this.splice(index, 0, args[0]);
				return this.length;
			}

			// call the original method
			var res = [][name].apply(this, args)

			// cause the change where the args are:
			// len - where the additions happened
			// add - items added
			// args - the items added
			// undefined - the old value
			if ( this.comparator  && args.length > 1) {
				this.sort(null, true);
				trigger(this,"reset", [args])
			} else {
				trigger(this, "change", [""+len, "add", args, undefined])
			}


			return res;
		}
	});

	each({
		/**
		 * @function pop
		 *
		 * Removes an item from the end of the list.
		 *
		 *     var l = new $.Observe.List([0,1,2]);
		 *
		 *     l.bind('change', function(
		 *         ev,        // the change event
		 *         attr,      // the attr that was changed, for multiple items, "*" is used
		 *         how,       // "remove"
		 *         newVals,   // undefined
		 *         oldVals,   // 2
		 *         where      // the location where these items where added
		 *         ) {
		 *
		 *     })
		 *
		 *     l.pop();
		 *
		 * @return {Object} the element at the end of the list
		 */
		pop: "length",
		/**
		 * @function shift
		 * Removes an item from the start of the list.  This is very similar to
		 * [jQuery.Observe.prototype.pop].
		 *
		 * @return {Object} the element at the start of the list
		 */
		shift: 0
	},
	// creates a 'remove' type method


	function( name, where ) {
		list.prototype[name] = function() {

			var args = getArgs(arguments),
				len = where && this.length ? this.length - 1 : 0;


			var res = [][name].apply(this, args)

			// create a change where the args are
			// "*" - change on potentially multiple properties
			// "remove" - items removed
			// undefined - the new values (there are none)
			// res - the old, removed values (should these be unbound)
			// len - where these items were removed
			trigger(this, "change", [""+len, "remove", undefined, [res]])

			if ( res && res.unbind ) {
				res.unbind("change" + this._namespace)
			}
			return res;
		}
	});

	list.prototype.
	/**
	 * @function indexOf
	 * Returns the position of the item in the array.  Returns -1 if the
	 * item is not in the array.
	 * @param {Object} item
	 * @return {Number}
	 */
	indexOf = [].indexOf || function(item){
		return $.inArray(item, this)
	}

	/**
	 * @class $.O
	 */
	$.O = function(data, options){
		if(isArray(data) || data instanceof $.Observe.List){
			return new $.Observe.List(data, options)
		} else {
			return new $.Observe(data, options)
		}
	}
})();

});