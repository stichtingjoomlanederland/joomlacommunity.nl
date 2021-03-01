ed.define('site/vendors/mmenu', ['edjquery'], function($) {

var jQuery = $;



/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);

// CONCATENATED MODULE: ./dist/_version.js
/* harmony default export */ var _version = ('8.5.3');

// CONCATENATED MODULE: ./dist/core/oncanvas/_options.js
var _options_options = {
	hooks: {},
	extensions: [],
	wrappers: [],
	navbar: {
		add: true,
		sticky: true,
		title: 'Menu',
		titleLink: 'parent'
	},
	onClick: {
		close: null,
		preventDefault: null,
		setSelected: true
	},
	slidingSubmenus: true
};
/* harmony default export */ var _options = (_options_options);

// CONCATENATED MODULE: ./dist/core/oncanvas/_configs.js
var _configs_configs = {
	classNames: {
		inset: 'Inset',
		nolistview: 'NoListview',
		nopanel: 'NoPanel',
		panel: 'Panel',
		selected: 'Selected',
		vertical: 'Vertical'
	},
	language: null,
	openingInterval: 25,
	panelNodetype: ['ul', 'ol', 'div'],
	transitionDuration: 400
};
/* harmony default export */ var _configs = (_configs_configs);

// CONCATENATED MODULE: ./dist/_modules/helpers.js
/**
 * Deep extend an object with the given defaults.
 * Note that the extended object is not a clone, meaning the original object will also be updated.
 *
 * @param 	{object}	orignl	The object to extend to.
 * @param 	{object}	dfault	The object to extend from.
 * @return	{object}			The extended "orignl" object.
 */
function extend(orignl, dfault) {
	if (type(orignl) != 'object') {
		orignl = {};
	}
	if (type(dfault) != 'object') {
		dfault = {};
	}
	for (var k in dfault) {
		if (!dfault.hasOwnProperty(k)) {
			continue;
		}
		if (typeof orignl[k] == 'undefined') {
			orignl[k] = dfault[k];
		}
		else if (type(orignl[k]) == 'object') {
			extend(orignl[k], dfault[k]);
		}
	}
	return orignl;
}
/**
 * Detect the touch / dragging direction on a touch device.
 *
 * @param   {HTMLElement} surface   The element to monitor for touch events.
 * @return  {object}                Object with "get" function.
 */
function touchDirection(surface) {
	var direction = '';
	surface.addEventListener('touchmove', function (evnt) {
		direction = '';
		if (evnt.movementY > 0) {
			direction = 'down';
		}
		else if (evnt.movementY < 0) {
			direction = 'up';
		}
	});
	return {
		get: function () { return direction; }
	};
}
/**
 * Get the type of any given variable. Improvement of "typeof".
 *
 * @param 	{any}		variable	The variable.
 * @return	{string}				The type of the variable in lowercase.
 */
function type(variable) {
	return {}.toString
		.call(variable)
		.match(/\s([a-zA-Z]+)/)[1]
		.toLowerCase();
}
/**
 * Find the value from an option or function.
 * @param 	{HTMLElement} 	element 	Scope for the function.
 * @param 	{any} 			[option] 	Value or function.
 * @param 	{any} 			[dfault] 	Default fallback value.
 * @return	{any}						The given evaluation of the given option, or the default fallback value.
 */
function valueOrFn(element, option, dfault) {
	if (typeof option == 'function') {
		var value = option.call(element);
		if (typeof value != 'undefined') {
			return value;
		}
	}
	if ((option === null ||
		typeof option == 'function' ||
		typeof option == 'undefined') &&
		typeof dfault != 'undefined') {
		return dfault;
	}
	return option;
}
/**
 * Set and invoke a (single) transition-end function with fallback.
 *
 * @param {HTMLElement} 	element 	Scope for the function.
 * @param {function}		func		Function to invoke.
 * @param {number}			duration	The duration of the animation (for the fallback).
 */
function transitionend(element, func, duration) {
	var _ended = false, _fn = function (evnt) {
		if (typeof evnt !== 'undefined') {
			if (evnt.target !== element) {
				return;
			}
		}
		if (!_ended) {
			element.removeEventListener('transitionend', _fn);
			element.removeEventListener('webkitTransitionEnd', _fn);
			func.call(element);
		}
		_ended = true;
	};
	element.addEventListener('transitionend', _fn);
	element.addEventListener('webkitTransitionEnd', _fn);
	setTimeout(_fn, duration * 1.1);
}
/**
 * Get a (page wide) unique ID.
 */
function uniqueId() {
	return 'mm-' + __id++;
}
var __id = 0;
/**
 * Get the original ID from a possibly prefixed ID.
 * @param id The possibly prefixed ID.
 */
function originalId(id) {
	if (id.slice(0, 3) == 'mm-') {
		return id.slice(3);
	}
	return id;
}

// CONCATENATED MODULE: ./dist/_modules/i18n.js

var translations = {};
/**
 * Add translations to a language.
 * @param {object}  text        Object of key/value translations.
 * @param {string}  language    The translated language.
 */
function i18n_add(text, language) {
	if (typeof translations[language] == 'undefined') {
		translations[language] = {};
	}
	extend(translations[language], text);
}
/**
 * Find a translated text in a language.
 * @param   {string} text       The text to find the translation for.
 * @param   {string} language   The language to search in.
 * @return  {string}            The translated text.
 */
function get(text, language) {
	if (typeof language == 'string' &&
		typeof translations[language] != 'undefined') {
		return translations[language][text] || text;
	}
	return text;
}
/**
 * Get all translated text in a language.
 * @param   {string} language   The language to search for.
 * @return  {object}            The translations.
 */
function i18n_all(language) {
	return translations;
}

// CONCATENATED MODULE: ./dist/core/oncanvas/translations/nl.js
/* harmony default export */ var nl = ({
	'Menu': 'Menu'
});

// CONCATENATED MODULE: ./dist/core/oncanvas/translations/fa.js
/* harmony default export */ var fa = ({
	'Menu': 'منو'
});

// CONCATENATED MODULE: ./dist/core/oncanvas/translations/de.js
/* harmony default export */ var de = ({
	'Menu': 'Menü'
});

// CONCATENATED MODULE: ./dist/core/oncanvas/translations/ru.js
/* harmony default export */ var ru = ({
	'Menu': 'Меню'
});

// CONCATENATED MODULE: ./dist/core/oncanvas/translations/translate.js





/* harmony default export */ var translate = (function () {
	i18n_add(nl, 'nl');
	i18n_add(fa, 'fa');
	i18n_add(de, 'de');
	i18n_add(ru, 'ru');
});

// CONCATENATED MODULE: ./dist/_modules/dom.js
/**
 * Create an element with classname.
 *
 * @param 	{string}		selector	The nodeName and classnames for the element to create.
 * @return	{HTMLElement}				The created element.
 */
function create(selector) {
	var args = selector.split('.');
	var elem = document.createElement(args.shift());
	//  IE11:
	args.forEach(function (classname) {
		elem.classList.add(classname);
	});
	//  Better browsers:
	// elem.classList.add(...args);
	return elem;
}
/**
 * Find all elements matching the selector.
 * Basically the same as element.querySelectorAll() but it returns an actuall array.
 *
 * @param 	{HTMLElement} 	element Element to search in.
 * @param 	{string}		filter	The filter to match.
 * @return	{array}					Array of elements that match the filter.
 */
function find(element, filter) {
	return Array.prototype.slice.call(element.querySelectorAll(filter));
}
/**
 * Find all child elements matching the (optional) selector.
 *
 * @param 	{HTMLElement} 	element Element to search in.
 * @param 	{string}		filter	The filter to match.
 * @return	{array}					Array of child elements that match the filter.
 */
function dom_children(element, filter) {
	var children = Array.prototype.slice.call(element.children);
	return filter ? children.filter(function (child) { return child.matches(filter); }) : children;
}
/**
 * Find text excluding text from within child elements.
 * @param   {HTMLElement}   element Element to search in.
 * @return  {string}                The text.
 */
function dom_text(element) {
	return Array.prototype.slice
		.call(element.childNodes)
		.filter(function (child) { return child.nodeType == 3; })
		.map(function (child) { return child.textContent; })
		.join(' ');
}
/**
 * Find all preceding elements matching the selector.
 *
 * @param 	{HTMLElement} 	element Element to start searching from.
 * @param 	{string}		filter	The filter to match.
 * @return	{array}					Array of preceding elements that match the selector.
 */
function dom_parents(element, filter) {
	/** Array of preceding elements that match the selector. */
	var parents = [];
	/** Array of preceding elements that match the selector. */
	var parent = element.parentElement;
	while (parent) {
		parents.push(parent);
		parent = parent.parentElement;
	}
	return filter ? parents.filter(function (parent) { return parent.matches(filter); }) : parents;
}
/**
 * Find all previous siblings matching the selecotr.
 *
 * @param 	{HTMLElement} 	element Element to start searching from.
 * @param 	{string}		filter	The filter to match.
 * @return	{array}					Array of previous siblings that match the selector.
 */
function prevAll(element, filter) {
	/** Array of previous siblings that match the selector. */
	var previous = [];
	/** Current element in the loop */
	var current = element.previousElementSibling;
	while (current) {
		if (!filter || current.matches(filter)) {
			previous.push(current);
		}
		current = current.previousElementSibling;
	}
	return previous;
}
/**
 * Get an element offset relative to the document.
 *
 * @param 	{HTMLElement}	 element 			Element to start measuring from.
 * @param 	{string}		 [direction=top] 	Offset top or left.
 * @return	{number}							The element offset relative to the document.
 */
function offset(element, direction) {
	return (element.getBoundingClientRect()[direction] +
		document.body[direction === 'left' ? 'scrollLeft' : 'scrollTop']);
}
/**
 * Filter out non-listitem listitems.
 * @param  {array} listitems 	Elements to filter.
 * @return {array}				The filtered set of listitems.
 */
function filterLI(listitems) {
	return listitems.filter(function (listitem) { return !listitem.matches('.mm-hidden'); });
}
/**
 * Find anchors in listitems (excluding anchor that open a sub-panel).
 * @param  {array} 	listitems 	Elements to filter.
 * @return {array}				The found set of anchors.
 */
function filterLIA(listitems) {
	var anchors = [];
	filterLI(listitems).forEach(function (listitem) {
		anchors.push.apply(anchors, dom_children(listitem, 'a.mm-listitem__text'));
	});
	return anchors.filter(function (anchor) { return !anchor.matches('.mm-btn_next'); });
}
/**
 * Refactor a classname on multiple elements.
 * @param {HTMLElement} element 	Element to refactor.
 * @param {string}		oldClass 	Classname to remove.
 * @param {string}		newClass 	Classname to add.
 */
function reClass(element, oldClass, newClass) {
	if (element.matches('.' + oldClass)) {
		element.classList.remove(oldClass);
		element.classList.add(newClass);
	}
}

// CONCATENATED MODULE: ./dist/_modules/matchmedia.js
/** Collection of callback functions for media querys. */
var listeners = {};
/**
 * Bind functions to a matchMedia listener (subscriber).
 *
 * @param {string|number} 	query 	Media query to match or number for min-width.
 * @param {function} 		yes 	Function to invoke when the media query matches.
 * @param {function} 		no 		Function to invoke when the media query doesn't match.
 */
function matchmedia_add(query, yes, no) {
	if (typeof query == 'number') {
		query = '(min-width: ' + query + 'px)';
	}
	listeners[query] = listeners[query] || [];
	listeners[query].push({ yes: yes, no: no });
}
/**
 * Initialize the matchMedia listener.
 */
function watch() {
	var _loop_1 = function (query) {
		var mqlist = window.matchMedia(query);
		fire(query, mqlist);
		mqlist.onchange = function (evnt) {
			fire(query, mqlist);
		};
	};
	for (var query in listeners) {
		_loop_1(query);
	}
}
/**
 * Invoke the "yes" or "no" function for a matchMedia listener (publisher).
 *
 * @param {string} 			query 	Media query to check for.
 * @param {MediaQueryList} 	mqlist 	Media query list to check with.
 */
function fire(query, mqlist) {
	var fn = mqlist.matches ? 'yes' : 'no';
	for (var m = 0; m < listeners[query].length; m++) {
		listeners[query][m][fn]();
	}
}

// CONCATENATED MODULE: ./dist/core/oncanvas/mmenu.oncanvas.js








//  Add the translations.
translate();
/**
 * Class for a mobile menu.
 */
var mmenu_oncanvas_Mmenu = /** @class */ (function () {
	/**
	 * Create a mobile menu.
	 * @param {HTMLElement|string} 	menu						The menu node.
	 * @param {object} 				[options=Mmenu.options]		Options for the menu.
	 * @param {object} 				[configs=Mmenu.configs]		Configuration options for the menu.
	 */
	function Mmenu(menu, options, configs) {
		//	Extend options and configuration from defaults.
		this.opts = extend(options, Mmenu.options);
		this.conf = extend(configs, Mmenu.configs);
		//	Methods to expose in the API.
		this._api = [
			'bind',
			'initPanel',
			'initListview',
			'openPanel',
			'closePanel',
			'closeAllPanels',
			'setSelected'
		];
		//	Storage objects for nodes, variables, hooks and click handlers.
		this.node = {};
		this.vars = {};
		this.hook = {};
		this.clck = [];
		//	Get menu node from string or element.
		this.node.menu =
			typeof menu == 'string' ? document.querySelector(menu) : menu;
		if (typeof this._deprecatedWarnings == 'function') {
			this._deprecatedWarnings();
		}
		this._initWrappers();
		this._initAddons();
		this._initExtensions();
		this._initHooks();
		this._initAPI();
		this._initMenu();
		this._initPanels();
		this._initOpened();
		this._initAnchors();
		watch();
		return this;
	}
	/**
	 * Open a panel.
	 * @param {HTMLElement} panel				Panel to open.
	 * @param {boolean}		[animation=true]	Whether or not to open the panel with an animation.
	 */
	Mmenu.prototype.openPanel = function (panel, animation) {
		var _this = this;
		//	Invoke "before" hook.
		this.trigger('openPanel:before', [panel]);
		//	Find panel.
		if (!panel) {
			return;
		}
		if (!panel.matches('.mm-panel')) {
			panel = panel.closest('.mm-panel');
		}
		if (!panel) {
			return;
		}
		//	/Find panel.
		if (typeof animation != 'boolean') {
			animation = true;
		}
		//	Open a "vertical" panel.
		if (panel.parentElement.matches('.mm-listitem_vertical')) {
			//	Open current and all vertical parent panels.
			dom_parents(panel, '.mm-listitem_vertical').forEach(function (listitem) {
				listitem.classList.add('mm-listitem_opened');
				dom_children(listitem, '.mm-panel').forEach(function (panel) {
					panel.classList.remove('mm-hidden');
				});
			});
			//	Open first non-vertical parent panel.
			var parents = dom_parents(panel, '.mm-panel').filter(function (panel) { return !panel.parentElement.matches('.mm-listitem_vertical'); });
			this.trigger('openPanel:start', [panel]);
			if (parents.length) {
				this.openPanel(parents[0]);
			}
			this.trigger('openPanel:finish', [panel]);
			//	Open a "horizontal" panel.
		}
		else {
			if (panel.matches('.mm-panel_opened')) {
				return;
			}
			var panels = dom_children(this.node.pnls, '.mm-panel'), current_1 = dom_children(this.node.pnls, '.mm-panel_opened')[0];
			//	Close all child panels.
			panels
				.filter(function (parent) { return parent !== panel; })
				.forEach(function (parent) {
				parent.classList.remove('mm-panel_opened-parent');
			});
			//	Open all parent panels.
			var parent_1 = panel['mmParent'];
			while (parent_1) {
				parent_1 = parent_1.closest('.mm-panel');
				if (parent_1) {
					if (!parent_1.parentElement.matches('.mm-listitem_vertical')) {
						parent_1.classList.add('mm-panel_opened-parent');
					}
					parent_1 = parent_1['mmParent'];
				}
			}
			//	Add classes for animation.
			panels.forEach(function (panel) {
				panel.classList.remove('mm-panel_highest');
			});
			panels
				.filter(function (hidden) { return hidden !== current_1; })
				.filter(function (hidden) { return hidden !== panel; })
				.forEach(function (hidden) {
				hidden.classList.add('mm-hidden');
			});
			panel.classList.remove('mm-hidden');
			/**	Start opening the panel. */
			var openPanelStart_1 = function () {
				if (current_1) {
					current_1.classList.remove('mm-panel_opened');
				}
				panel.classList.add('mm-panel_opened');
				if (panel.matches('.mm-panel_opened-parent')) {
					if (current_1) {
						current_1.classList.add('mm-panel_highest');
					}
					panel.classList.remove('mm-panel_opened-parent');
				}
				else {
					if (current_1) {
						current_1.classList.add('mm-panel_opened-parent');
					}
					panel.classList.add('mm-panel_highest');
				}
				//	Invoke "start" hook.
				_this.trigger('openPanel:start', [panel]);
			};
			/**	Finish opening the panel. */
			var openPanelFinish_1 = function () {
				if (current_1) {
					current_1.classList.remove('mm-panel_highest');
					current_1.classList.add('mm-hidden');
				}
				panel.classList.remove('mm-panel_highest');
				//	Invoke "finish" hook.
				_this.trigger('openPanel:finish', [panel]);
			};
			if (animation && !panel.matches('.mm-panel_noanimation')) {
				//	Without the timeout the animation will not work because the element had display: none;
				setTimeout(function () {
					//	Callback
					transitionend(panel, function () {
						openPanelFinish_1();
					}, _this.conf.transitionDuration);
					openPanelStart_1();
				}, this.conf.openingInterval);
			}
			else {
				openPanelStart_1();
				openPanelFinish_1();
			}
		}
		//	Invoke "after" hook.
		this.trigger('openPanel:after', [panel]);
	};
	/**
	 * Close a panel.
	 * @param {HTMLElement} panel Panel to close.
	 */
	Mmenu.prototype.closePanel = function (panel) {
		//	Invoke "before" hook.
		this.trigger('closePanel:before', [panel]);
		var li = panel.parentElement;
		//	Only works for "vertical" panels.
		if (li.matches('.mm-listitem_vertical')) {
			li.classList.remove('mm-listitem_opened');
			panel.classList.add('mm-hidden');
			//	Invoke main hook.
			this.trigger('closePanel', [panel]);
		}
		//	Invoke "after" hook.
		this.trigger('closePanel:after', [panel]);
	};
	/**
	 * Close all opened panels.
	 * @param {HTMLElement} panel Panel to open after closing all other panels.
	 */
	Mmenu.prototype.closeAllPanels = function (panel) {
		//	Invoke "before" hook.
		this.trigger('closeAllPanels:before');
		//	Close all "vertical" panels.
		var listitems = this.node.pnls.querySelectorAll('.mm-listitem');
		listitems.forEach(function (listitem) {
			listitem.classList.remove('mm-listitem_selected');
			listitem.classList.remove('mm-listitem_opened');
		});
		//	Close all "horizontal" panels.
		var panels = dom_children(this.node.pnls, '.mm-panel'), opened = panel ? panel : panels[0];
		dom_children(this.node.pnls, '.mm-panel').forEach(function (panel) {
			if (panel !== opened) {
				panel.classList.remove('mm-panel_opened');
				panel.classList.remove('mm-panel_opened-parent');
				panel.classList.remove('mm-panel_highest');
				panel.classList.add('mm-hidden');
			}
		});
		//	Open first panel.
		this.openPanel(opened, false);
		//	Invoke "after" hook.
		this.trigger('closeAllPanels:after');
	};
	/**
	 * Toggle a panel opened/closed.
	 * @param {HTMLElement} panel Panel to open or close.
	 */
	Mmenu.prototype.togglePanel = function (panel) {
		var listitem = panel.parentElement;
		//	Only works for "vertical" panels.
		if (listitem.matches('.mm-listitem_vertical')) {
			this[listitem.matches('.mm-listitem_opened')
				? 'closePanel'
				: 'openPanel'](panel);
		}
	};
	/**
	 * Display a listitem as being "selected".
	 * @param {HTMLElement} listitem Listitem to mark.
	 */
	Mmenu.prototype.setSelected = function (listitem) {
		//	Invoke "before" hook.
		this.trigger('setSelected:before', [listitem]);
		//	First, remove the selected class from all listitems.
		find(this.node.menu, '.mm-listitem_selected').forEach(function (li) {
			li.classList.remove('mm-listitem_selected');
		});
		//	Next, add the selected class to the provided listitem.
		listitem.classList.add('mm-listitem_selected');
		//	Invoke "after" hook.
		this.trigger('setSelected:after', [listitem]);
	};
	/**
	 * Bind functions to a hook (subscriber).
	 * @param {string} 		hook The hook.
	 * @param {function} 	func The function.
	 */
	Mmenu.prototype.bind = function (hook, func) {
		//	Create an array for the hook if it does not yet excist.
		this.hook[hook] = this.hook[hook] || [];
		//	Push the function to the array.
		this.hook[hook].push(func);
	};
	/**
	 * Invoke the functions bound to a hook (publisher).
	 * @param {string} 	hook  	The hook.
	 * @param {array}	[args] 	Arguments for the function.
	 */
	Mmenu.prototype.trigger = function (hook, args) {
		if (this.hook[hook]) {
			for (var h = 0, l = this.hook[hook].length; h < l; h++) {
				this.hook[hook][h].apply(this, args);
			}
		}
	};
	/**
	 * Create the API.
	 */
	Mmenu.prototype._initAPI = function () {
		var _this = this;
		//	We need this=that because:
		//	1) the "arguments" object can not be referenced in an arrow function in ES3 and ES5.
		var that = this;
		this.API = {};
		this._api.forEach(function (fn) {
			_this.API[fn] = function () {
				var re = that[fn].apply(that, arguments); // 1)
				return typeof re == 'undefined' ? that.API : re;
			};
		});
		//	Store the API in the HTML node for external usage.
		this.node.menu['mmApi'] = this.API;
	};
	/**
	 * Bind the hooks specified in the options (publisher).
	 */
	Mmenu.prototype._initHooks = function () {
		for (var hook in this.opts.hooks) {
			this.bind(hook, this.opts.hooks[hook]);
		}
	};
	/**
	 * Initialize the wrappers specified in the options.
	 */
	Mmenu.prototype._initWrappers = function () {
		//	Invoke "before" hook.
		this.trigger('initWrappers:before');
		for (var w = 0; w < this.opts.wrappers.length; w++) {
			var wrpr = Mmenu.wrappers[this.opts.wrappers[w]];
			if (typeof wrpr == 'function') {
				wrpr.call(this);
			}
		}
		//	Invoke "after" hook.
		this.trigger('initWrappers:after');
	};
	/**
	 * Initialize all available add-ons.
	 */
	Mmenu.prototype._initAddons = function () {
		//	Invoke "before" hook.
		this.trigger('initAddons:before');
		for (var addon in Mmenu.addons) {
			Mmenu.addons[addon].call(this);
		}
		//	Invoke "after" hook.
		this.trigger('initAddons:after');
	};
	/**
	 * Initialize the extensions specified in the options.
	 */
	Mmenu.prototype._initExtensions = function () {
		var _this = this;
		//	Invoke "before" hook.
		this.trigger('initExtensions:before');
		//	Convert array to object with array.
		if (type(this.opts.extensions) == 'array') {
			this.opts.extensions = {
				all: this.opts.extensions
			};
		}
		//	Loop over object.
		Object.keys(this.opts.extensions).forEach(function (query) {
			var classnames = _this.opts.extensions[query].map(function (extension) { return 'mm-menu_' + extension; });
			if (classnames.length) {
				matchmedia_add(query, function () {
					//  IE11:
					classnames.forEach(function (classname) {
						_this.node.menu.classList.add(classname);
					});
					//  Better browsers:
					// this.node.menu.classList.add(...classnames);
				}, function () {
					//  IE11:
					classnames.forEach(function (classname) {
						_this.node.menu.classList.remove(classname);
					});
					//  Better browsers:
					// this.node.menu.classList.remove(...classnames);
				});
			}
		});
		//	Invoke "after" hook.
		this.trigger('initExtensions:after');
	};
	/**
	 * Initialize the menu.
	 */
	Mmenu.prototype._initMenu = function () {
		var _this = this;
		//	Invoke "before" hook.
		this.trigger('initMenu:before');
		//	Add class to the wrapper.
		this.node.wrpr = this.node.wrpr || this.node.menu.parentElement;
		this.node.wrpr.classList.add('mm-wrapper');
		//	Add an ID to the menu if it does not yet have one.
		this.node.menu.id = this.node.menu.id || uniqueId();
		//	Wrap the panels in a node.
		var panels = create('div.mm-panels');
		dom_children(this.node.menu).forEach(function (panel) {
			if (_this.conf.panelNodetype.indexOf(panel.nodeName.toLowerCase()) >
				-1) {
				panels.append(panel);
			}
		});
		this.node.menu.append(panels);
		this.node.pnls = panels;
		//	Add class to the menu.
		this.node.menu.classList.add('mm-menu');
		//	Invoke "after" hook.
		this.trigger('initMenu:after');
	};
	/**
	 * Initialize panels.
	 */
	Mmenu.prototype._initPanels = function () {
		var _this = this;
		//	Invoke "before" hook.
		this.trigger('initPanels:before');
		//	Open / close panels.
		this.clck.push(function (anchor, args) {
			if (args.inMenu) {
				var href = anchor.getAttribute('href');
				if (href && href.length > 1 && href.slice(0, 1) == '#') {
					try {
						var panel = find(_this.node.menu, href)[0];
						if (panel && panel.matches('.mm-panel')) {
							if (anchor.parentElement.matches('.mm-listitem_vertical')) {
								_this.togglePanel(panel);
							}
							else {
								_this.openPanel(panel);
							}
							return true;
						}
					}
					catch (err) { }
				}
			}
		});
		/** The panels to initiate */
		var panels = dom_children(this.node.pnls);
		panels.forEach(function (panel) {
			_this.initPanel(panel);
		});
		//	Invoke "after" hook.
		this.trigger('initPanels:after');
	};
	/**
	 * Initialize a single panel and its children.
	 * @param {HTMLElement} panel The panel to initialize.
	 */
	Mmenu.prototype.initPanel = function (panel) {
		var _this = this;
		/** Query selector for possible node-types for panels. */
		var panelNodetype = this.conf.panelNodetype.join(', ');
		if (panel.matches(panelNodetype)) {
			//  Only once
			if (!panel.matches('.mm-panel')) {
				panel = this._initPanel(panel);
			}
			if (panel) {
				/** The sub panels. */
				var children_1 = [];
				//	Find panel > panel
				children_1.push.apply(children_1, dom_children(panel, '.' + this.conf.classNames.panel));
				//	Find panel listitem > panel
				dom_children(panel, '.mm-listview').forEach(function (listview) {
					dom_children(listview, '.mm-listitem').forEach(function (listitem) {
						children_1.push.apply(children_1, dom_children(listitem, panelNodetype));
					});
				});
				//  Initiate subpanel(s).
				children_1.forEach(function (child) {
					_this.initPanel(child);
				});
			}
		}
	};
	/**
	 * Initialize a single panel.
	 * @param  {HTMLElement} 		panel 	Panel to initialize.
	 * @return {HTMLElement|null} 			Initialized panel.
	 */
	Mmenu.prototype._initPanel = function (panel) {
		var _this = this;
		//	Invoke "before" hook.
		this.trigger('initPanel:before', [panel]);
		//	Refactor panel classnames
		reClass(panel, this.conf.classNames.panel, 'mm-panel');
		reClass(panel, this.conf.classNames.nopanel, 'mm-nopanel');
		reClass(panel, this.conf.classNames.inset, 'mm-listview_inset');
		if (panel.matches('.mm-listview_inset')) {
			panel.classList.add('mm-nopanel');
		}
		//	Stop if not supposed to be a panel.
		if (panel.matches('.mm-nopanel')) {
			return null;
		}
		/** The original ID on the node. */
		var id = panel.id || uniqueId();
		//  Vertical panel.
		var vertical = panel.matches('.' + this.conf.classNames.vertical) ||
			!this.opts.slidingSubmenus;
		panel.classList.remove(this.conf.classNames.vertical);
		//	Wrap UL/OL in DIV
		if (panel.matches('ul, ol')) {
			panel.removeAttribute('id');
			/** The panel. */
			var wrapper = create('div');
			//	Wrap the listview in the panel.
			panel.before(wrapper);
			wrapper.append(panel);
			panel = wrapper;
		}
		panel.id = id;
		panel.classList.add('mm-panel');
		panel.classList.add('mm-hidden');
		/** The parent listitem. */
		var parent = [panel.parentElement].filter(function (listitem) {
			return listitem.matches('li');
		})[0];
		if (vertical) {
			if (parent) {
				parent.classList.add('mm-listitem_vertical');
			}
		}
		else {
			this.node.pnls.append(panel);
		}
		if (parent) {
			//	Store parent/child relation.
			parent['mmChild'] = panel;
			panel['mmParent'] = parent;
			//	Add open link to parent listitem
			if (parent && parent.matches('.mm-listitem')) {
				if (!dom_children(parent, '.mm-btn').length) {
					/** The text node. */
					var item = dom_children(parent, '.mm-listitem__text')[0];
					if (item) {
						/** The open link. */
						var button = create('a.mm-btn.mm-btn_next.mm-listitem__btn');
						button.setAttribute('href', '#' + panel.id);
						//  If the item has no link,
						//      Replace the item with the open link.
						if (item.matches('span')) {
							button.classList.add('mm-listitem__text');
							button.innerHTML = item.innerHTML;
							parent.insertBefore(button, item.nextElementSibling);
							item.remove();
						}
						//  Otherwise, insert the button after the text.
						else {
							parent.insertBefore(button, dom_children(parent, '.mm-panel')[0]);
						}
					}
				}
			}
		}
		this._initNavbar(panel);
		dom_children(panel, 'ul, ol').forEach(function (listview) {
			_this.initListview(listview);
		});
		//	Invoke "after" hook.
		this.trigger('initPanel:after', [panel]);
		return panel;
	};
	/**
	 * Initialize a navbar.
	 * @param {HTMLElement} panel Panel for the navbar.
	 */
	Mmenu.prototype._initNavbar = function (panel) {
		//	Invoke "before" hook.
		this.trigger('initNavbar:before', [panel]);
		//	Only one navbar per panel.
		if (dom_children(panel, '.mm-navbar').length) {
			return;
		}
		/** The parent listitem. */
		var parentListitem = null;
		/** The parent panel. */
		var parentPanel = null;
		//  The parent panel was specified in the data-mm-parent attribute.
		if (panel.getAttribute('data-mm-parent')) {
			parentPanel = find(this.node.pnls, panel.getAttribute('data-mm-parent'))[0];
		}
		// if (panel.dataset.mmParent) { // IE10 has no dataset
		// parentPanel = DOM.find(this.node.pnls, panel.dataset.mmParent)[0];
		// }
		//  The parent panel from a listitem.
		else {
			parentListitem = panel['mmParent'];
			if (parentListitem) {
				parentPanel = parentListitem.closest('.mm-panel');
			}
		}
		//  No navbar needed for vertical submenus.
		if (parentListitem && parentListitem.matches('.mm-listitem_vertical')) {
			return;
		}
		/** The navbar element. */
		var navbar = create('div.mm-navbar');
		//  Hide navbar if specified in options.
		if (!this.opts.navbar.add) {
			navbar.classList.add('mm-hidden');
		}
		//  Sticky navbars.
		else if (this.opts.navbar.sticky) {
			navbar.classList.add('mm-navbar_sticky');
		}
		//  Add the back button.
		if (parentPanel) {
			/** The back button. */
			var prev = create('a.mm-btn.mm-btn_prev.mm-navbar__btn');
			prev.setAttribute('href', '#' + parentPanel.id);
			navbar.append(prev);
		}
		/** The anchor that opens the panel. */
		var opener = null;
		//  The anchor is in a listitem.
		if (parentListitem) {
			opener = dom_children(parentListitem, '.mm-listitem__text')[0];
		}
		//  The anchor is in a panel.
		else if (parentPanel) {
			opener = find(parentPanel, 'a[href="#' + panel.id + '"]')[0];
		}
		//  Add the title.
		var title = create('a.mm-navbar__title');
		var titleText = create('span');
		title.append(titleText);
		titleText.innerHTML =
			// panel.dataset.mmTitle || // IE10 has no dataset :(
			panel.getAttribute('data-mm-title') ||
				(opener ? opener.textContent : '') ||
				this.i18n(this.opts.navbar.title) ||
				this.i18n('Menu');
		switch (this.opts.navbar.titleLink) {
			case 'anchor':
				if (opener) {
					title.setAttribute('href', opener.getAttribute('href'));
				}
				break;
			case 'parent':
				if (parentPanel) {
					title.setAttribute('href', '#' + parentPanel.id);
				}
				break;
		}
		navbar.append(title);
		panel.prepend(navbar);
		//	Invoke "after" hook.
		this.trigger('initNavbar:after', [panel]);
	};
	/**
	 * Initialize a listview.
	 * @param {HTMLElement} listview Listview to initialize.
	 */
	Mmenu.prototype.initListview = function (listview) {
		var _this = this;
		//	Invoke "before" hook.
		this.trigger('initListview:before', [listview]);
		reClass(listview, this.conf.classNames.nolistview, 'mm-nolistview');
		if (!listview.matches('.mm-nolistview')) {
			listview.classList.add('mm-listview');
			dom_children(listview).forEach(function (listitem) {
				listitem.classList.add('mm-listitem');
				reClass(listitem, _this.conf.classNames.selected, 'mm-listitem_selected');
				dom_children(listitem, 'a, span').forEach(function (item) {
					if (!item.matches('.mm-btn')) {
						item.classList.add('mm-listitem__text');
					}
				});
			});
		}
		//	Invoke "after" hook.
		this.trigger('initListview:after', [listview]);
	};
	/**
	 * Find and open the correct panel after creating the menu.
	 */
	Mmenu.prototype._initOpened = function () {
		//	Invoke "before" hook.
		this.trigger('initOpened:before');
		/** The selected listitem(s). */
		var listitems = this.node.pnls.querySelectorAll('.mm-listitem_selected');
		/** The last selected listitem. */
		var lastitem = null;
		//	Deselect the listitems.
		listitems.forEach(function (listitem) {
			lastitem = listitem;
			listitem.classList.remove('mm-listitem_selected');
		});
		//	Re-select the last listitem.
		if (lastitem) {
			lastitem.classList.add('mm-listitem_selected');
		}
		/**	The current opened panel. */
		var current = lastitem
			? lastitem.closest('.mm-panel')
			: dom_children(this.node.pnls, '.mm-panel')[0];
		//	Open the current opened panel.
		this.openPanel(current, false);
		//	Invoke "after" hook.
		this.trigger('initOpened:after');
	};
	/**
	 * Initialize anchors in / for the menu.
	 */
	Mmenu.prototype._initAnchors = function () {
		var _this = this;
		//	Invoke "before" hook.
		this.trigger('initAnchors:before');
		document.addEventListener('click', function (evnt) {
			/** The clicked element. */
			var target = evnt.target.closest('a[href]');
			if (!target) {
				return;
			}
			/** Arguments passed to the bound methods. */
			var args = {
				inMenu: target.closest('.mm-menu') === _this.node.menu,
				inListview: target.matches('.mm-listitem > a'),
				toExternal: target.matches('[rel="external"]') ||
					target.matches('[target="_blank"]')
			};
			var onClick = {
				close: null,
				setSelected: null,
				preventDefault: target.getAttribute('href').slice(0, 1) == '#'
			};
			//	Find hooked behavior.
			for (var c = 0; c < _this.clck.length; c++) {
				var click = _this.clck[c].call(_this, target, args);
				if (click) {
					if (typeof click == 'boolean') {
						evnt.preventDefault();
						return;
					}
					if (type(click) == 'object') {
						onClick = extend(click, onClick);
					}
				}
			}
			//	Default behavior for anchors in lists.
			if (args.inMenu && args.inListview && !args.toExternal) {
				//	Set selected item, Default: true
				if (valueOrFn(target, _this.opts.onClick.setSelected, onClick.setSelected)) {
					_this.setSelected(target.parentElement);
				}
				//	Prevent default / don't follow link. Default: false.
				if (valueOrFn(target, _this.opts.onClick.preventDefault, onClick.preventDefault)) {
					evnt.preventDefault();
				}
				//	Close menu. Default: false
				if (valueOrFn(target, _this.opts.onClick.close, onClick.close)) {
					if (_this.opts.offCanvas &&
						typeof _this.close == 'function') {
						_this.close();
					}
				}
			}
		}, true);
		//	Invoke "after" hook.
		this.trigger('initAnchors:after');
	};
	/**
	 * Get the translation for a text.
	 * @param  {string} text 	Text to translate.
	 * @return {string}			The translated text.
	 */
	Mmenu.prototype.i18n = function (text) {
		return get(text, this.conf.language);
	};
	/**	Plugin version. */
	Mmenu.version = _version;
	/**	Default options for menus. */
	Mmenu.options = _options;
	/**	Default configuration for menus. */
	Mmenu.configs = _configs;
	/**	Available add-ons for the plugin. */
	Mmenu.addons = {};
	/** Available wrappers for the plugin. */
	Mmenu.wrappers = {};
	/**	Globally used HTML elements. */
	Mmenu.node = {};
	/** Globally used variables. */
	Mmenu.vars = {};
	return Mmenu;
}());
/* harmony default export */ var mmenu_oncanvas = (mmenu_oncanvas_Mmenu);

// CONCATENATED MODULE: ./dist/core/offcanvas/_options.js
var offcanvas_options_options = {
	blockUI: true,
	moveBackground: true
};
/* harmony default export */ var offcanvas_options = (offcanvas_options_options);
/**
 * Extend shorthand options.
 *
 * @param  {object} options The options to extend.
 * @return {object}			The extended options.
 */
function extendShorthandOptions(options) {
	if (typeof options != 'object') {
		options = {};
	}
	return options;
}
;

// CONCATENATED MODULE: ./dist/core/offcanvas/_configs.js
var offcanvas_configs_configs = {
	clone: false,
	menu: {
		insertMethod: 'prepend',
		insertSelector: 'body'
	},
	page: {
		nodetype: 'div',
		selector: null,
		noSelector: []
	}
};
/* harmony default export */ var offcanvas_configs = (offcanvas_configs_configs);

// CONCATENATED MODULE: ./dist/_modules/eventlisteners.js
/**
 * Make the first letter in a word uppercase.
 * @param {string} word The word.
 */
function ucFirst(word) {
	if (!word) {
		return '';
	}
	return word.charAt(0).toUpperCase() + word.slice(1);
}
/**
 * Bind an event listener to an element.
 * @param {HTMLElement} element     The element to bind the event listener to.
 * @param {string}      evnt        The event to listen to.
 * @param {funcion}     handler     The function to invoke.
 */
function on(element, evnt, handler) {
	//  Extract the event name and space from the event (the event can include a namespace (click.foo)).
	var evntParts = evnt.split('.');
	evnt = 'mmEvent' + ucFirst(evntParts[0]) + ucFirst(evntParts[1]);
	element[evnt] = element[evnt] || [];
	element[evnt].push(handler);
	element.addEventListener(evntParts[0], handler);
}
/**
 * Remove an event listener from an element.
 * @param {HTMLElement} element The element to remove the event listeners from.
 * @param {string}      evnt    The event to remove.
 */
function off(element, evnt) {
	//  Extract the event name and space from the event (the event can include a namespace (click.foo)).
	var evntParts = evnt.split('.');
	evnt = 'mmEvent' + ucFirst(evntParts[0]) + ucFirst(evntParts[1]);
	(element[evnt] || []).forEach(function (handler) {
		element.removeEventListener(evntParts[0], handler);
	});
}
/**
 * Trigger the bound event listeners on an element.
 * @param {HTMLElement} element     The element of which to trigger the event listeners from.
 * @param {string}      evnt        The event to trigger.
 * @param {object}      [options]   Options to pass to the handler.
 */
function trigger(element, evnt, options) {
	var evntParts = evnt.split('.');
	evnt = 'mmEvent' + ucFirst(evntParts[0]) + ucFirst(evntParts[1]);
	(element[evnt] || []).forEach(function (handler) {
		handler(options || {});
	});
}

// CONCATENATED MODULE: ./dist/core/offcanvas/mmenu.offcanvas.js







//  Add the options and configs.
mmenu_oncanvas.options.offCanvas = offcanvas_options;
mmenu_oncanvas.configs.offCanvas = offcanvas_configs;
/* harmony default export */ var mmenu_offcanvas = (function () {
	var _this = this;
	if (!this.opts.offCanvas) {
		return;
	}
	var options = extendShorthandOptions(this.opts.offCanvas);
	this.opts.offCanvas = extend(options, mmenu_oncanvas.options.offCanvas);
	var configs = this.conf.offCanvas;
	//	Add methods to the API.
	this._api.push('open', 'close', 'setPage');
	//	Setup the menu.
	this.vars.opened = false;
	//	Add off-canvas behavior.
	this.bind('initMenu:before', function () {
		//	Clone if needed.
		if (configs.clone) {
			//	Clone the original menu and store it.
			_this.node.menu = _this.node.menu.cloneNode(true);
			//	Prefix all ID's in the cloned menu.
			if (_this.node.menu.id) {
				_this.node.menu.id = 'mm-' + _this.node.menu.id;
			}
			find(_this.node.menu, '[id]').forEach(function (elem) {
				elem.id = 'mm-' + elem.id;
			});
		}
		_this.node.wrpr = document.body;
		//	Prepend to the <body>
		document
			.querySelector(configs.menu.insertSelector)[configs.menu.insertMethod](_this.node.menu);
	});
	this.bind('initMenu:after', function () {
		//	Setup the UI blocker.
		initBlocker.call(_this);
		//	Setup the page.
		_this.setPage(mmenu_oncanvas.node.page);
		//	Setup window events.
		initWindow.call(_this);
		//	Setup the menu.
		_this.node.menu.classList.add('mm-menu_offcanvas');
		//	Open if url hash equals menu id (usefull when user clicks the hamburger icon before the menu is created)
		var hash = window.location.hash;
		if (hash) {
			var id = originalId(_this.node.menu.id);
			if (id && id == hash.slice(1)) {
				setTimeout(function () {
					_this.open();
				}, 1000);
			}
		}
	});
	//	Sync the blocker to target the page.
	this.bind('setPage:after', function (page) {
		if (mmenu_oncanvas.node.blck) {
			dom_children(mmenu_oncanvas.node.blck, 'a').forEach(function (anchor) {
				anchor.setAttribute('href', '#' + page.id);
			});
		}
	});
	//	Add screenreader / aria support
	this.bind('open:start:sr-aria', function () {
		mmenu_oncanvas.sr_aria(_this.node.menu, 'hidden', false);
	});
	this.bind('close:finish:sr-aria', function () {
		mmenu_oncanvas.sr_aria(_this.node.menu, 'hidden', true);
	});
	this.bind('initMenu:after:sr-aria', function () {
		mmenu_oncanvas.sr_aria(_this.node.menu, 'hidden', true);
	});
	//	Add screenreader / text support
	this.bind('initBlocker:after:sr-text', function () {
		dom_children(mmenu_oncanvas.node.blck, 'a').forEach(function (anchor) {
			anchor.innerHTML = mmenu_oncanvas.sr_text(_this.i18n(_this.conf.screenReader.text.closeMenu));
		});
	});
	//	Add click behavior.
	//	Prevents default behavior when clicking an anchor
	this.clck.push(function (anchor, args) {
		//	Open menu if the clicked anchor links to the menu
		var id = originalId(_this.node.menu.id);
		if (id) {
			if (anchor.matches('[href="#' + id + '"]')) {
				//	Opening this menu from within this menu
				//		-> Open menu
				if (args.inMenu) {
					_this.open();
					return true;
				}
				//	Opening this menu from within a second menu
				//		-> Close the second menu before opening this menu
				var menu = anchor.closest('.mm-menu');
				if (menu) {
					var api = menu['mmApi'];
					if (api && api.close) {
						api.close();
						transitionend(menu, function () {
							_this.open();
						}, _this.conf.transitionDuration);
						return true;
					}
				}
				//	Opening this menu
				_this.open();
				return true;
			}
		}
		//	Close menu
		id = mmenu_oncanvas.node.page.id;
		if (id) {
			if (anchor.matches('[href="#' + id + '"]')) {
				_this.close();
				return true;
			}
		}
		return;
	});
});
/**
 * Open the menu.
 */
mmenu_oncanvas.prototype.open = function () {
	var _this = this;
	//	Invoke "before" hook.
	this.trigger('open:before');
	if (this.vars.opened) {
		return;
	}
	this._openSetup();
	//	Without the timeout, the animation won't work because the menu had display: none;
	setTimeout(function () {
		_this._openStart();
	}, this.conf.openingInterval);
	//	Invoke "after" hook.
	this.trigger('open:after');
};
mmenu_oncanvas.prototype._openSetup = function () {
	var _this = this;
	var options = this.opts.offCanvas;
	//	Close other menus
	this.closeAllOthers();
	//	Store style and position
	mmenu_oncanvas.node.page['mmStyle'] = mmenu_oncanvas.node.page.getAttribute('style') || '';
	//	Trigger window-resize to measure height
	trigger(window, 'resize.page', { force: true });
	var clsn = ['mm-wrapper_opened'];
	//	Add options
	if (options.blockUI) {
		clsn.push('mm-wrapper_blocking');
	}
	if (options.blockUI == 'modal') {
		clsn.push('mm-wrapper_modal');
	}
	if (options.moveBackground) {
		clsn.push('mm-wrapper_background');
	}
	//  IE11:
	clsn.forEach(function (classname) {
		_this.node.wrpr.classList.add(classname);
	});
	//  Better browsers:
	// this.node.wrpr.classList.add(...clsn);
	//	Open
	//	Without the timeout, the animation won't work because the menu had display: none;
	setTimeout(function () {
		_this.vars.opened = true;
	}, this.conf.openingInterval);
	this.node.menu.classList.add('mm-menu_opened');
};
/**
 * Finish opening the menu.
 */
mmenu_oncanvas.prototype._openStart = function () {
	var _this = this;
	//	Callback when the page finishes opening.
	transitionend(mmenu_oncanvas.node.page, function () {
		_this.trigger('open:finish');
	}, this.conf.transitionDuration);
	//	Opening
	this.trigger('open:start');
	this.node.wrpr.classList.add('mm-wrapper_opening');
};
mmenu_oncanvas.prototype.close = function () {
	var _this = this;
	//	Invoke "before" hook.
	this.trigger('close:before');
	if (!this.vars.opened) {
		return;
	}
	//	Callback when the page finishes closing.
	transitionend(mmenu_oncanvas.node.page, function () {
		_this.node.menu.classList.remove('mm-menu_opened');
		var classnames = [
			'mm-wrapper_opened',
			'mm-wrapper_blocking',
			'mm-wrapper_modal',
			'mm-wrapper_background'
		];
		//  IE11:
		classnames.forEach(function (classname) {
			_this.node.wrpr.classList.remove(classname);
		});
		//  Better browsers:
		// this.node.wrpr.classList.remove(...classnames);
		//	Restore style and position
		mmenu_oncanvas.node.page.setAttribute('style', mmenu_oncanvas.node.page['mmStyle']);
		_this.vars.opened = false;
		_this.trigger('close:finish');
	}, this.conf.transitionDuration);
	//	Closing
	this.trigger('close:start');
	this.node.wrpr.classList.remove('mm-wrapper_opening');
	//	Invoke "after" hook.
	this.trigger('close:after');
};
/**
 * Close all other menus.
 */
mmenu_oncanvas.prototype.closeAllOthers = function () {
	var _this = this;
	find(document.body, '.mm-menu_offcanvas').forEach(function (menu) {
		if (menu !== _this.node.menu) {
			var api = menu['mmApi'];
			if (api && api.close) {
				api.close();
			}
		}
	});
};
/**
 * Set the "page" node.
 *
 * @param {HTMLElement} page Element to set as the page.
 */
mmenu_oncanvas.prototype.setPage = function (page) {
	//	Invoke "before" hook.
	this.trigger('setPage:before', [page]);
	var configs = this.conf.offCanvas;
	//	If no page was specified, find it.
	if (!page) {
		/** Array of elements that are / could be "the page". */
		var pages = typeof configs.page.selector == 'string'
			? find(document.body, configs.page.selector)
			: dom_children(document.body, configs.page.nodetype);

		// If site uses nav on the top levelthere could be an issue
		var items = $(document.body).children().get();
		pages = pages.concat(items);

		//	Filter out elements that are absolutely not "the page".
		pages = pages.filter(function (page) { return !page.matches('.mm-menu, .mm-wrapper__blocker'); });
		//	Filter out elements that are configured to not be "the page".
		if (configs.page.noSelector.length) {
			pages = pages.filter(function (page) { return !page.matches(configs.page.noSelector.join(', ')); });
		}
		//	Wrap multiple pages in a single element.
		if (pages.length > 1) {
			var wrapper_1 = create('div');
			pages[0].before(wrapper_1);
			pages.forEach(function (page) {
				wrapper_1.append(page);
			});
			pages = [wrapper_1];
		}
		page = pages[0];
	}
	page.classList.add('mm-page');
	page.classList.add('mm-slideout');
	page.id = page.id || uniqueId();
	mmenu_oncanvas.node.page = page;
	//	Invoke "after" hook.
	this.trigger('setPage:after', [page]);
};
/**
 * Initialize the window.
 */
var initWindow = function () {
	var _this = this;
	//	Prevent tabbing
	//	Because when tabbing outside the menu, the element that gains focus will be centered on the screen.
	//	In other words: The menu would move out of view.
	off(document.body, 'keydown.tabguard');
	on(document.body, 'keydown.tabguard', function (evnt) {
		if (evnt.keyCode == 9) {
			if (_this.node.wrpr.matches('.mm-wrapper_opened')) {
				evnt.preventDefault();
			}
		}
	});
};
/**
 * Initialize "blocker" node
 */
var initBlocker = function () {
	var _this = this;
	//	Invoke "before" hook.
	this.trigger('initBlocker:before');
	var options = this.opts.offCanvas, configs = this.conf.offCanvas;
	if (!options.blockUI) {
		return;
	}
	//	Create the blocker node.
	if (!mmenu_oncanvas.node.blck) {
		var blck = create('div.mm-wrapper__blocker.mm-slideout');
		blck.innerHTML = '<a></a>';
		//	Append the blocker node to the body.
		document.querySelector(configs.menu.insertSelector).append(blck);
		//	Store the blocker node.
		mmenu_oncanvas.node.blck = blck;
	}
	//	Close the menu when
	//		1) clicking,
	//		2) touching or
	//		3) dragging the blocker node.
	var closeMenu = function (evnt) {
		evnt.preventDefault();
		evnt.stopPropagation();
		if (!_this.node.wrpr.matches('.mm-wrapper_modal')) {
			_this.close();
		}
	};
	mmenu_oncanvas.node.blck.addEventListener('mousedown', closeMenu); // 1
	mmenu_oncanvas.node.blck.addEventListener('touchstart', closeMenu); // 2
	mmenu_oncanvas.node.blck.addEventListener('touchmove', closeMenu); // 3
	//	Invoke "after" hook.
	this.trigger('initBlocker:after');
};

// CONCATENATED MODULE: ./dist/core/screenreader/_options.js
var screenreader_options_options = {
	aria: true,
	text: true
};
/* harmony default export */ var screenreader_options = (screenreader_options_options);
/**
 * Extend shorthand options.
 *
 * @param  {object} options The options to extend.
 * @return {object}			The extended options.
 */
function _options_extendShorthandOptions(options) {
	if (typeof options == 'boolean') {
		options = {
			aria: options,
			text: options
		};
	}
	if (typeof options != 'object') {
		options = {};
	}
	return options;
}
;

// CONCATENATED MODULE: ./dist/core/screenreader/_configs.js
var screenreader_configs_configs = {
	text: {
		closeMenu: 'Close menu',
		closeSubmenu: 'Close submenu',
		openSubmenu: 'Open submenu',
		toggleSubmenu: 'Toggle submenu'
	}
};
/* harmony default export */ var screenreader_configs = (screenreader_configs_configs);

// CONCATENATED MODULE: ./dist/core/screenreader/translations/nl.js
/* harmony default export */ var translations_nl = ({
	'Close menu': 'Menu sluiten',
	'Close submenu': 'Submenu sluiten',
	'Open submenu': 'Submenu openen',
	'Toggle submenu': 'Submenu wisselen'
});

// CONCATENATED MODULE: ./dist/core/screenreader/translations/fa.js
/* harmony default export */ var translations_fa = ({
	'Close menu': 'بستن منو',
	'Close submenu': 'بستن زیرمنو',
	'Open submenu': 'بازکردن زیرمنو',
	'Toggle submenu': 'سوییچ زیرمنو'
});

// CONCATENATED MODULE: ./dist/core/screenreader/translations/de.js
/* harmony default export */ var translations_de = ({
	'Close menu': 'Menü schließen',
	'Close submenu': 'Untermenü schließen',
	'Open submenu': 'Untermenü öffnen',
	'Toggle submenu': 'Untermenü wechseln'
});

// CONCATENATED MODULE: ./dist/core/screenreader/translations/ru.js
/* harmony default export */ var translations_ru = ({
	'Close menu': 'Закрыть меню',
	'Close submenu': 'Закрыть подменю',
	'Open submenu': 'Открыть подменю',
	'Toggle submenu': 'Переключить подменю'
});

// CONCATENATED MODULE: ./dist/core/screenreader/translations/translate.js





/* harmony default export */ var translations_translate = (function () {
	i18n_add(translations_nl, 'nl');
	i18n_add(translations_fa, 'fa');
	i18n_add(translations_de, 'de');
	i18n_add(translations_ru, 'ru');
});

// CONCATENATED MODULE: ./dist/core/screenreader/mmenu.screenreader.js







//  Add the translations.
translations_translate();
//  Add the options and configs.
mmenu_oncanvas.options.screenReader = screenreader_options;
mmenu_oncanvas.configs.screenReader = screenreader_configs;
/* harmony default export */ var mmenu_screenreader = (function () {
	var _this = this;
	//	Extend options.
	var options = _options_extendShorthandOptions(this.opts.screenReader);
	this.opts.screenReader = extend(options, mmenu_oncanvas.options.screenReader);
	//	Extend configs.
	var configs = this.conf.screenReader;
	//	Add Aria-* attributes
	if (options.aria) {
		//	Add screenreader / aria hooks for add-ons
		//	In orde to keep this list short, only extend hooks that are actually used by other add-ons.
		this.bind('initAddons:after', function () {
			_this.bind('initMenu:after', function () {
				this.trigger('initMenu:after:sr-aria', [].slice.call(arguments));
			});
			_this.bind('initNavbar:after', function () {
				this.trigger('initNavbar:after:sr-aria', [].slice.call(arguments));
			});
			_this.bind('openPanel:start', function () {
				this.trigger('openPanel:start:sr-aria', [].slice.call(arguments));
			});
			_this.bind('close:start', function () {
				this.trigger('close:start:sr-aria', [].slice.call(arguments));
			});
			_this.bind('close:finish', function () {
				this.trigger('close:finish:sr-aria', [].slice.call(arguments));
			});
			_this.bind('open:start', function () {
				this.trigger('open:start:sr-aria', [].slice.call(arguments));
			});
			_this.bind('initOpened:after', function () {
				this.trigger('initOpened:after:sr-aria', [].slice.call(arguments));
			});
		});
		//	Update aria-hidden for hidden / visible listitems
		this.bind('updateListview', function () {
			_this.node.pnls
				.querySelectorAll('.mm-listitem')
				.forEach(function (listitem) {
				mmenu_oncanvas.sr_aria(listitem, 'hidden', listitem.matches('.mm-hidden'));
			});
		});
		//	Update aria-hidden for the panels when opening and closing a panel.
		this.bind('openPanel:start', function (panel) {
			/** Panels that should be considered "hidden". */
			var hidden = find(_this.node.pnls, '.mm-panel')
				.filter(function (hide) { return hide !== panel; })
				.filter(function (hide) { return !hide.parentElement.matches('.mm-panel'); });
			/** Panels that should be considered "visible". */
			var visible = [panel];
			find(panel, '.mm-listitem_vertical .mm-listitem_opened').forEach(function (listitem) {
				visible.push.apply(visible, dom_children(listitem, '.mm-panel'));
			});
			//	Set the panels to be considered "hidden" or "visible".
			hidden.forEach(function (panel) {
				mmenu_oncanvas.sr_aria(panel, 'hidden', true);
			});
			visible.forEach(function (panel) {
				mmenu_oncanvas.sr_aria(panel, 'hidden', false);
			});
		});
		this.bind('closePanel', function (panel) {
			mmenu_oncanvas.sr_aria(panel, 'hidden', true);
		});
		//	Add aria-haspopup and aria-owns to prev- and next buttons.
		this.bind('initPanel:after', function (panel) {
			find(panel, '.mm-btn').forEach(function (button) {
				mmenu_oncanvas.sr_aria(button, 'haspopup', true);
				var href = button.getAttribute('href');
				if (href) {
					mmenu_oncanvas.sr_aria(button, 'owns', href.replace('#', ''));
				}
			});
		});
		//	Add aria-hidden for navbars in panels.
		this.bind('initNavbar:after', function (panel) {
			/** The navbar in the panel. */
			var navbar = dom_children(panel, '.mm-navbar')[0];
			/** Whether or not the navbar should be considered "hidden". */
			var hidden = navbar.matches('.mm-hidden');
			//	Set the navbar to be considered "hidden" or "visible".
			mmenu_oncanvas.sr_aria(navbar, 'hidden', hidden);
		});
		//	Text
		if (options.text) {
			//	Add aria-hidden to titles in navbars
			if (this.opts.navbar.titleLink == 'parent') {
				this.bind('initNavbar:after', function (panel) {
					/** The navbar in the panel. */
					var navbar = dom_children(panel, '.mm-navbar')[0];
					/** Whether or not the navbar should be considered "hidden". */
					var hidden = navbar.querySelector('.mm-btn_prev')
						? true
						: false;
					//	Set the navbar-title to be considered "hidden" or "visible".
					mmenu_oncanvas.sr_aria(find(navbar, '.mm-navbar__title')[0], 'hidden', hidden);
				});
			}
		}
	}
	//	Add screenreader text
	if (options.text) {
		//	Add screenreader / text hooks for add-ons
		//	In orde to keep this list short, only extend hooks that are actually used by other add-ons.
		this.bind('initAddons:after', function () {
			_this.bind('setPage:after', function () {
				this.trigger('setPage:after:sr-text', [].slice.call(arguments));
			});
			_this.bind('initBlocker:after', function () {
				this.trigger('initBlocker:after:sr-text', [].slice.call(arguments));
			});
		});
		//	Add text to the prev-buttons.
		this.bind('initNavbar:after', function (panel) {
			var navbar = dom_children(panel, '.mm-navbar')[0];
			if (navbar) {
				var button = dom_children(navbar, '.mm-btn_prev')[0];
				if (button) {
					button.innerHTML = mmenu_oncanvas.sr_text(_this.i18n(configs.text.closeSubmenu));
				}
			}
		});
		//	Add text to the next-buttons.
		this.bind('initListview:after', function (listview) {
			var parent = listview.closest('.mm-panel')['mmParent'];
			if (parent) {
				var next = dom_children(parent, '.mm-btn_next')[0];
				if (next) {
					var text = _this.i18n(configs.text[next.parentElement.matches('.mm-listitem_vertical')
						? 'toggleSubmenu'
						: 'openSubmenu']);
					next.innerHTML += mmenu_oncanvas.sr_text(text);
				}
			}
		});
	}
});
//	Methods
(function () {
	var attr = function (element, attr, value) {
		element[attr] = value;
		if (value) {
			element.setAttribute(attr, value.toString());
		}
		else {
			element.removeAttribute(attr);
		}
	};
	/**
	 * Add aria (property and) attribute to a HTML element.
	 *
	 * @param {HTMLElement} 	element	The node to add the attribute to.
	 * @param {string}			name	The (non-aria-prefixed) attribute name.
	 * @param {string|boolean}	value	The attribute value.
	 */
	mmenu_oncanvas.sr_aria = function (element, name, value) {
		attr(element, 'aria-' + name, value);
	};
	/**
	 * Add role attribute to a HTML element.
	 *
	 * @param {HTMLElement}		element	The node to add the attribute to.
	 * @param {string|boolean}	value	The attribute value.
	 */
	mmenu_oncanvas.sr_role = function (element, value) {
		attr(element, 'role', value);
	};
	/**
	 * Wrap a text in a screen-reader-only node.
	 *
	 * @param 	{string} text	The text to wrap.
	 * @return	{string}		The wrapped text.
	 */
	mmenu_oncanvas.sr_text = function (text) {
		return '<span class="mm-sronly">' + text + '</span>';
	};
})();

// CONCATENATED MODULE: ./dist/core/scrollbugfix/_options.js
var scrollbugfix_options_options = {
	fix: true
};
/* harmony default export */ var scrollbugfix_options = (scrollbugfix_options_options);
/**
 * Extend shorthand options.
 *
 * @param  {object} options The options to extend.
 * @return {object}			The extended options.
 */
function scrollbugfix_options_extendShorthandOptions(options) {
	if (typeof options == 'boolean') {
		options = {
			fix: options
		};
	}
	if (typeof options != 'object') {
		options = {};
	}
	return options;
}
;

// CONCATENATED MODULE: ./dist/_modules/support.js
/** Whether or not touch gestures are supported by the browser. */
var touch = 'ontouchstart' in window ||
	(navigator.msMaxTouchPoints ? true : false) ||
	false;

// CONCATENATED MODULE: ./dist/core/scrollbugfix/mmenu.scrollbugfix.js






//  Add the options.
mmenu_oncanvas.options.scrollBugFix = scrollbugfix_options;
/* harmony default export */ var mmenu_scrollbugfix = (function () {
	var _this = this;
	//	The scrollBugFix add-on fixes a scrolling bug
	//		1) on touch devices
	//		2) in an off-canvas menu
	//		3) that -when opened- blocks the UI from interaction
	if (!touch || // 1
		!this.opts.offCanvas || // 2
		!this.opts.offCanvas.blockUI // 3
	) {
		return;
	}
	//	Extend options.
	var options = scrollbugfix_options_extendShorthandOptions(this.opts.scrollBugFix);
	this.opts.scrollBugFix = extend(options, mmenu_oncanvas.options.scrollBugFix);
	if (!options.fix) {
		return;
	}
	var touchDir = touchDirection(this.node.menu);
	/**
	 * Prevent an event from doing its default and stop its propagation.
	 * @param {ScrollBehavior} evnt The event to stop.
	 */
	function stop(evnt) {
		evnt.preventDefault();
		evnt.stopPropagation();
	}
	//  Prevent the page from scrolling when scrolling in the menu.
	this.node.menu.addEventListener('scroll', stop, {
		//  Make sure to tell the browser the event will be prevented.
		passive: false
	});
	//  Prevent the page from scrolling when dragging in the menu.
	this.node.menu.addEventListener('touchmove', function (evnt) {
		var panel = evnt.target.closest('.mm-panel');
		if (panel) {
			//  When dragging a non-scrollable panel,
			//      we can simple preventDefault and stopPropagation.
			if (panel.scrollHeight === panel.offsetHeight) {
				stop(evnt);
			}
			//  When dragging a scrollable panel,
			//      that is fully scrolled up (or down).
			//      It will not trigger the scroll event when dragging down (or up) (because you can't scroll up (or down)),
			//      so we need to match the dragging direction with the scroll position before preventDefault and stopPropagation,
			//      otherwise the panel would not scroll at all in any direction.
			else if (
			//  When scrolled up and dragging down
			(panel.scrollTop == 0 && touchDir.get() == 'down') ||
				//  When scrolled down and dragging up
				(panel.scrollHeight ==
					panel.scrollTop + panel.offsetHeight &&
					touchDir.get() == 'up')) {
				stop(evnt);
			}
			//  When dragging anything other than a panel.
		}
		else {
			stop(evnt);
		}
	}, {
		//  Make sure to tell the browser the event can be prevented.
		passive: false
	});
	//  Some small additional improvements
	//	Scroll the current opened panel to the top when opening the menu.
	this.bind('open:start', function () {
		var panel = dom_children(_this.node.pnls, '.mm-panel_opened')[0];
		if (panel) {
			panel.scrollTop = 0;
		}
	});
	//	Fix issue after device rotation change.
	window.addEventListener('orientationchange', function (evnt) {
		var panel = dom_children(_this.node.pnls, '.mm-panel_opened')[0];
		if (panel) {
			panel.scrollTop = 0;
			//	Apparently, changing the overflow-scrolling property triggers some event :)
			panel.style['-webkit-overflow-scrolling'] = 'auto';
			panel.style['-webkit-overflow-scrolling'] = 'touch';
		}
	});
});

// CONCATENATED MODULE: ./dist/addons/autoheight/_options.js
var opts = {
	height: 'default'
};
/* harmony default export */ var autoheight_options = (opts);
/**
 * Extend shorthand options.
 *
 * @param  {object} options The options to extend.
 * @return {object}			The extended options.
 */
function autoheight_options_extendShorthandOptions(options) {
	if (typeof options == 'boolean' && options) {
		options = {
			height: 'auto'
		};
	}
	if (typeof options == 'string') {
		options = {
			height: options
		};
	}
	if (typeof options != 'object') {
		options = {};
	}
	return options;
}
;

// CONCATENATED MODULE: ./dist/addons/autoheight/mmenu.autoheight.js





//	Add the options.
mmenu_oncanvas.options.autoHeight = autoheight_options;
/* harmony default export */ var mmenu_autoheight = (function () {
	var _this = this;
	var options = autoheight_options_extendShorthandOptions(this.opts.autoHeight);
	this.opts.autoHeight = extend(options, mmenu_oncanvas.options.autoHeight);
	if (options.height != 'auto' && options.height != 'highest') {
		return;
	}
	var setHeight = (function () {
		var getCurrent = function () {
			var panel = dom_children(_this.node.pnls, '.mm-panel_opened')[0];
			if (panel) {
				panel = measurablePanel(panel);
			}
			//	Fallback, just to be sure we have a panel.
			if (!panel) {
				panel = dom_children(_this.node.pnls, '.mm-panel')[0];
			}
			return panel.scrollHeight;
		};
		var getHighest = function () {
			var highest = 0;
			dom_children(_this.node.pnls, '.mm-panel').forEach(function (panel) {
				panel = measurablePanel(panel);
				highest = Math.max(highest, panel.scrollHeight);
			});
			return highest;
		};
		var measurablePanel = function (panel) {
			//	If it's a vertically expanding panel...
			if (panel.parentElement.matches('.mm-listitem_vertical')) {
				//	...find the first parent panel that isn't.
				panel = dom_parents(panel, '.mm-panel').filter(function (panel) {
					return !panel.parentElement.matches('.mm-listitem_vertical');
				})[0];
			}
			return panel;
		};
		return function () {
			if (_this.opts.offCanvas && !_this.vars.opened) {
				return;
			}
			var _hgh = 0;
			var _dif = _this.node.menu.offsetHeight - _this.node.pnls.offsetHeight;
			//	The "measuring" classname undoes some CSS to be able to measure the height.
			_this.node.menu.classList.add('mm-menu_autoheight-measuring');
			//	Measure the height.
			if (options.height == 'auto') {
				_hgh = getCurrent();
			}
			else if (options.height == 'highest') {
				_hgh = getHighest();
			}
			//	Set the height.
			_this.node.menu.style.height = _hgh + _dif + 'px';
			//	Remove the "measuring" classname.
			_this.node.menu.classList.remove('mm-menu_autoheight-measuring');
		};
	})();
	//	Add the autoheight class to the menu.
	this.bind('initMenu:after', function () {
		_this.node.menu.classList.add('mm-menu_autoheight');
	});
	if (this.opts.offCanvas) {
		//	Measure the height when opening the off-canvas menu.
		this.bind('open:start', setHeight);
	}
	if (options.height == 'highest') {
		//	Measure the height when initiating panels.
		this.bind('initPanels:after', setHeight);
	}
	if (options.height == 'auto') {
		//	Measure the height when updating listviews.
		this.bind('updateListview', setHeight);
		//	Measure the height when opening a panel.
		this.bind('openPanel:start', setHeight);
	}
});

// CONCATENATED MODULE: ./dist/addons/backbutton/_options.js
var backbutton_options_options = {
	close: false,
	open: false
};
/* harmony default export */ var backbutton_options = (backbutton_options_options);
/**
 * Extend shorthand options.
 *
 * @param  {object} options The options to extend.
 * @return {object}			The extended options.
 */
function backbutton_options_extendShorthandOptions(options) {
	if (typeof options == 'boolean') {
		options = {
			close: options
		};
	}
	if (typeof options != 'object') {
		options = {};
	}
	return options;
}
;

// CONCATENATED MODULE: ./dist/addons/backbutton/mmenu.backbutton.js





//	Add the options.
mmenu_oncanvas.options.backButton = backbutton_options;
/* harmony default export */ var mmenu_backbutton = (function () {
	var _this = this;
	if (!this.opts.offCanvas) {
		return;
	}
	var options = backbutton_options_extendShorthandOptions(this.opts.backButton);
	this.opts.backButton = extend(options, mmenu_oncanvas.options.backButton);
	var _menu = '#' + this.node.menu.id;
	//	Close menu
	if (options.close) {
		var states = [];
		var setStates = function () {
			states = [_menu];
			dom_children(_this.node.pnls, '.mm-panel_opened, .mm-panel_opened-parent').forEach(function (panel) {
				states.push('#' + panel.id);
			});
		};
		this.bind('open:finish', function () {
			history.pushState(null, document.title, _menu);
		});
		this.bind('open:finish', setStates);
		this.bind('openPanel:finish', setStates);
		this.bind('close:finish', function () {
			states = [];
			history.back();
			history.pushState(null, document.title, location.pathname + location.search);
		});
		window.addEventListener('popstate', function (evnt) {
			if (_this.vars.opened) {
				if (states.length) {
					states = states.slice(0, -1);
					var hash = states[states.length - 1];
					if (hash == _menu) {
						_this.close();
					}
					else {
						_this.openPanel(_this.node.menu.querySelector(hash));
						history.pushState(null, document.title, _menu);
					}
				}
			}
		});
	}
	if (options.open) {
		window.addEventListener('popstate', function (evnt) {
			if (!_this.vars.opened && location.hash == _menu) {
				_this.open();
			}
		});
	}
});

// CONCATENATED MODULE: ./dist/addons/columns/_options.js
var columns_options_options = {
	add: false,
	visible: {
		min: 1,
		max: 3
	}
};
/* harmony default export */ var columns_options = (columns_options_options);
/**
 * Extend shorthand options.
 *
 * @param  {object} options The options to extend.
 * @return {object}			The extended options.
 */
function columns_options_extendShorthandOptions(options) {
	if (typeof options == 'boolean') {
		options = {
			add: options
		};
	}
	if (typeof options == 'number') {
		options = {
			add: true,
			visible: options
		};
	}
	if (typeof options != 'object') {
		options = {};
	}
	if (typeof options.visible == 'number') {
		options.visible = {
			min: options.visible,
			max: options.visible
		};
	}
	return options;
}
;

// CONCATENATED MODULE: ./dist/addons/columns/mmenu.columns.js





//	Add the options.
mmenu_oncanvas.options.columns = columns_options;
/* harmony default export */ var mmenu_columns = (function () {
	var _this = this;
	var options = columns_options_extendShorthandOptions(this.opts.columns);
	this.opts.columns = extend(options, mmenu_oncanvas.options.columns);
	//	Add the columns
	if (options.add) {
		options.visible.min = Math.max(1, Math.min(6, options.visible.min));
		options.visible.max = Math.max(options.visible.min, Math.min(6, options.visible.max));
		/** Columns related clasnames for the menu. */
		var colm = [];
		/** Columns related clasnames for the panels. */
		var colp = [];
		/** Classnames to remove from panels in favor of showing columns. */
		var rmvc = [
			'mm-panel_opened',
			'mm-panel_opened-parent',
			'mm-panel_highest'
		];
		for (var i = 0; i <= options.visible.max; i++) {
			colm.push('mm-menu_columns-' + i);
			colp.push('mm-panel_columns-' + i);
		}
		rmvc.push.apply(rmvc, colp);
		//	Close all later opened panels
		this.bind('openPanel:before', function (panel) {
			/** The parent panel. */
			var parent;
			if (panel) {
				parent = panel['mmParent'];
			}
			if (!parent) {
				return;
			}
			parent = parent.closest('.mm-panel');
			if (!parent) {
				return;
			}
			var classname = parent.className;
			if (!classname.length) {
				return;
			}
			classname = classname.split('mm-panel_columns-')[1];
			if (!classname) {
				return;
			}
			var colnr = parseInt(classname.split(' ')[0], 10) + 1;
			while (colnr > 0) {
				panel = dom_children(_this.node.pnls, '.mm-panel_columns-' + colnr)[0];
				if (panel) {
					colnr++;
					panel.classList.add('mm-hidden');
					//  IE11:
					rmvc.forEach(function (classname) {
						panel.classList.remove(classname);
					});
					//  Better browsers:
					// panel.classList.remove(...rmvc);
				}
				else {
					colnr = -1;
					break;
				}
			}
		});
		this.bind('openPanel:start', function (panel) {
			var columns = dom_children(_this.node.pnls, '.mm-panel_opened-parent').length;
			if (!panel.matches('.mm-panel_opened-parent')) {
				columns++;
			}
			columns = Math.min(options.visible.max, Math.max(options.visible.min, columns));
			//  IE11:
			colm.forEach(function (classname) {
				_this.node.menu.classList.remove(classname);
			});
			//  Better browsers:
			// this.node.menu.classList.remove(...colm);
			_this.node.menu.classList.add('mm-menu_columns-' + columns);
			var panels = [];
			dom_children(_this.node.pnls, '.mm-panel').forEach(function (panel) {
				//  IE11:
				colp.forEach(function (classname) {
					panel.classList.remove(classname);
				});
				//  Better browsers:
				// panel.classList.remove(...colp);
				if (panel.matches('.mm-panel_opened-parent')) {
					panels.push(panel);
				}
			});
			panels.push(panel);
			panels.slice(-options.visible.max).forEach(function (panel, p) {
				panel.classList.add('mm-panel_columns-' + p);
			});
		});
	}
});

// CONCATENATED MODULE: ./dist/addons/counters/_options.js
var counters_options_options = {
	add: false,
	addTo: 'panels',
	count: false
};
/* harmony default export */ var counters_options = (counters_options_options);
/**
 * Extend shorthand options.
 *
 * @param  {object} options The options to extend.
 * @return {object}			The extended options.
 */
function counters_options_extendShorthandOptions(options) {
	if (typeof options == 'boolean') {
		options = {
			add: options,
			addTo: 'panels',
			count: options
		};
	}
	if (typeof options != 'object') {
		options = {};
	}
	if (options.addTo == 'panels') {
		options.addTo = '.mm-listview';
	}
	return options;
}

// CONCATENATED MODULE: ./dist/addons/counters/mmenu.counters.js





//	Add the options.
mmenu_oncanvas.options.counters = counters_options;
//	Add the classnames.
mmenu_oncanvas.configs.classNames.counters = {
	counter: 'Counter'
};
/* harmony default export */ var mmenu_counters = (function () {
	var _this = this;
	var options = counters_options_extendShorthandOptions(this.opts.counters);
	this.opts.counters = extend(options, mmenu_oncanvas.options.counters);
	//	Refactor counter class
	this.bind('initListview:after', function (listview) {
		var cntrclss = _this.conf.classNames.counters.counter, counters = find(listview, '.' + cntrclss);
		counters.forEach(function (counter) {
			reClass(counter, cntrclss, 'mm-counter');
		});
	});
	//	Add the counters after a listview is initiated.
	if (options.add) {
		this.bind('initListview:after', function (listview) {
			if (!listview.matches(options.addTo)) {
				return;
			}
			var parent = listview.closest('.mm-panel')['mmParent'];
			if (parent) {
				//	Check if no counter already excists.
				if (!find(parent, '.mm-counter').length) {
					var btn = dom_children(parent, '.mm-btn')[0];
					if (btn) {
						btn.prepend(create('span.mm-counter'));
					}
				}
			}
		});
	}
	if (options.count) {
		var count = function (listview) {
			var panels = listview
				? [listview.closest('.mm-panel')]
				: dom_children(_this.node.pnls, '.mm-panel');
			panels.forEach(function (panel) {
				var parent = panel['mmParent'];
				if (!parent) {
					return;
				}
				var counter = find(parent, '.mm-counter')[0];
				if (!counter) {
					return;
				}
				var listitems = [];
				dom_children(panel, '.mm-listview').forEach(function (listview) {
					listitems.push.apply(listitems, dom_children(listview));
				});
				counter.innerHTML = filterLI(listitems).length.toString();
			});
		};
		this.bind('initListview:after', count);
		this.bind('updateListview', count);
	}
});

// CONCATENATED MODULE: ./dist/addons/dividers/_options.js
var dividers_options_options = {
	add: false,
	addTo: 'panels'
};
/* harmony default export */ var dividers_options = (dividers_options_options);
/**
 * Extend shorthand options.
 *
 * @param  {object} options The options to extend.
 * @return {object}			The extended options.
 */
function dividers_options_extendShorthandOptions(options) {
	if (typeof options == 'boolean') {
		options = {
			add: options
		};
	}
	if (typeof options != 'object') {
		options = {};
	}
	if (options.addTo == 'panels') {
		options.addTo = '.mm-listview';
	}
	return options;
}

// CONCATENATED MODULE: ./dist/addons/dividers/mmenu.dividers.js





//	Add the options.
mmenu_oncanvas.options.dividers = dividers_options;
//  Add the classnames.
mmenu_oncanvas.configs.classNames.divider = 'Divider';
/* harmony default export */ var mmenu_dividers = (function () {
	var _this = this;
	var options = dividers_options_extendShorthandOptions(this.opts.dividers);
	this.opts.dividers = extend(options, mmenu_oncanvas.options.dividers);
	//	Refactor divider classname
	this.bind('initListview:after', function (listview) {
		dom_children(listview).forEach(function (listitem) {
			reClass(listitem, _this.conf.classNames.divider, 'mm-divider');
			if (listitem.matches('.mm-divider')) {
				listitem.classList.remove('mm-listitem');
			}
		});
	});
	//	Add dividers
	if (options.add) {
		this.bind('initListview:after', function (listview) {
			if (!listview.matches(options.addTo)) {
				return;
			}
			find(listview, '.mm-divider').forEach(function (divider) {
				divider.remove();
			});
			var lastletter = '', listitems = dom_children(listview);
			filterLI(listitems).forEach(function (listitem) {
				var letter = dom_children(listitem, '.mm-listitem__text')[0]
					.textContent.trim()
					.toLowerCase()[0];
				if (letter.length && letter != lastletter) {
					lastletter = letter;
					var divider = create('li.mm-divider');
					divider.textContent = letter;
					listview.insertBefore(divider, listitem);
				}
			});
		});
	}
});

// CONCATENATED MODULE: ./dist/addons/drag/_options.js
var drag_options_options = {
	open: false,
	node: null
};
/* harmony default export */ var drag_options = (drag_options_options);
/**
 * Extend shorthand options.
 *
 * @param  {object} options The options to extend.
 * @return {object}			The extended options.
 */
function drag_options_extendShorthandOptions(options) {
	if (typeof options == 'boolean') {
		options = {
			open: options
		};
	}
	if (typeof options != 'object') {
		options = {};
	}
	return options;
}

// CONCATENATED MODULE: ./dist/_modules/dragevents/_support.js
/** Whether or not touch gestures are supported by the browser. */
var _support_touch = 'ontouchstart' in window ||
	(navigator.msMaxTouchPoints ? true : false) ||
	false;

// CONCATENATED MODULE: ./dist/_modules/dragevents/_defaults.js
/** How far from the sides the gesture can start. */
var _defaults_area = {
	top: 0,
	right: 0,
	bottom: 0,
	left: 0
};
/** Tresholds for gestures. */
var _defaults_treshold = {
	start: 15,
	swipe: 15
};

// CONCATENATED MODULE: ./dist/_modules/dragevents/_settings.js
/** Names of the possible directions. */
var directionNames = {
	x: ['Right', 'Left'],
	y: ['Down', 'Up']
};
/** States for the gesture. */
var _settings_state = {
	inactive: 0,
	watching: 1,
	dragging: 2
};

// CONCATENATED MODULE: ./dist/_modules/dragevents/_helpers.js
/**
 * Calculate a distance from a percentage.
 * @param  {string|number}   position   The percentage (e.g. "75%").
 * @param  {number}          size       The available width or height in pixels.
 * @return {number}                     The calculated distance.
 */
var percentage2number = function (position, size) {
	if (typeof position == 'string') {
		if (position.slice(-1) == '%') {
			position = parseInt(position.slice(0, -1), 10);
			position = size * (position / 100);
		}
	}
	return position;
};

// CONCATENATED MODULE: ./dist/_modules/dragevents/index.js





var dragevents_DragEvents = /** @class */ (function () {
	/**
	 * Create the gestures.
	 * @param {HTMLElement} surface     The surface for the gesture.
	 * @param {object}      area        Restriction where on the surface the gesture can be started.
	 * @param {object}      treshold    Treshold for the gestures.
	 */
	function DragEvents(surface, area, treshold) {
		this.surface = surface;
		this.area = extend(area, _defaults_area);
		this.treshold = extend(treshold, _defaults_treshold);
		//	Set the mouse/touch events.
		if (!this.surface['mmHasDragEvents']) {
			this.surface.addEventListener(_support_touch ? 'touchstart' : 'mousedown', this.start.bind(this));
			this.surface.addEventListener(_support_touch ? 'touchend' : 'mouseup', this.stop.bind(this));
			this.surface.addEventListener(_support_touch ? 'touchleave' : 'mouseleave', this.stop.bind(this));
			this.surface.addEventListener(_support_touch ? 'touchmove' : 'mousemove', this.move.bind(this));
		}
		this.surface['mmHasDragEvents'] = true;
	}
	/**
	 * Starting the touch gesture.
	 * @param {Event} event The touch event.
	 */
	DragEvents.prototype.start = function (event) {
		this.currentPosition = {
			x: event.touches ? event.touches[0].pageX : event.pageX || 0,
			y: event.touches ? event.touches[0].pageY : event.pageY || 0
		};
		/** The widht of the surface. */
		var width = this.surface.clientWidth;
		/** The height of the surface. */
		var height = this.surface.clientHeight;
		//  Check if the gesture started below the area.top.
		var top = percentage2number(this.area.top, height);
		if (typeof top == 'number') {
			if (this.currentPosition.y < top) {
				return;
			}
		}
		//  Check if the gesture started before the area.right.
		var right = percentage2number(this.area.right, width);
		if (typeof right == 'number') {
			right = width - right;
			if (this.currentPosition.x > right) {
				return;
			}
		}
		//  Check if the gesture started above the area.bottom.
		var bottom = percentage2number(this.area.bottom, height);
		if (typeof bottom == 'number') {
			bottom = height - bottom;
			if (this.currentPosition.y > bottom) {
				return;
			}
		}
		//  Check if the gesture started after the area.left.
		var left = percentage2number(this.area.left, width);
		if (typeof left == 'number') {
			if (this.currentPosition.x < left) {
				return;
			}
		}
		//	Store the start x- and y-position.
		this.startPosition = {
			x: this.currentPosition.x,
			y: this.currentPosition.y
		};
		//	Set the state of the gesture to "watching".
		this.state = _settings_state.watching;
	};
	/**
	 * Stopping the touch gesture.
	 * @param {Event} event The touch event.
	 */
	DragEvents.prototype.stop = function (event) {
		//	Dispatch the "dragEnd" events.
		if (this.state == _settings_state.dragging) {
			/** The direction. */
			var dragDirection = this._dragDirection();
			/** The event information. */
			var detail = this._eventDetail(dragDirection);
			this._dispatchEvents('drag*End', detail);
			//	Dispatch the "swipe" events.
			if (Math.abs(this.movement[this.axis]) > this.treshold.swipe) {
				/** The direction. */
				var swipeDirection = this._swipeDirection();
				detail.direction = swipeDirection;
				this._dispatchEvents('swipe*', detail);
			}
		}
		//	Set the state of the gesture to "inactive".
		this.state = _settings_state.inactive;
	};
	/**
	 * Doing the touch gesture.
	 * @param {Event} event The touch event.
	 */
	DragEvents.prototype.move = function (event) {
		switch (this.state) {
			case _settings_state.watching:
			case _settings_state.dragging:
				var position = {
					x: event.changedTouches
						? event.touches[0].pageX
						: event.pageX || 0,
					y: event.changedTouches
						? event.touches[0].pageY
						: event.pageY || 0
				};
				this.movement = {
					x: position.x - this.currentPosition.x,
					y: position.y - this.currentPosition.y
				};
				this.distance = {
					x: position.x - this.startPosition.x,
					y: position.y - this.startPosition.y
				};
				this.currentPosition = {
					x: position.x,
					y: position.y
				};
				this.axis =
					Math.abs(this.distance.x) > Math.abs(this.distance.y)
						? 'x'
						: 'y';
				/** The direction. */
				var dragDirection = this._dragDirection();
				/** The event information. */
				var detail = this._eventDetail(dragDirection);
				//	Watching for the gesture to go past the treshold.
				if (this.state == _settings_state.watching) {
					if (Math.abs(this.distance[this.axis]) > this.treshold.start) {
						this._dispatchEvents('drag*Start', detail);
						//	Set the state of the gesture to "inactive".
						this.state = _settings_state.dragging;
					}
				}
				//	Dispatch the "drag" events.
				if (this.state == _settings_state.dragging) {
					this._dispatchEvents('drag*Move', detail);
				}
				break;
		}
	};
	/**
	 * Get the event details.
	 * @param {string}  direction   Direction for the event (up, right, down, left).
	 * @return {object}             The event details.
	 */
	DragEvents.prototype._eventDetail = function (direction) {
		var distX = this.distance.x;
		var distY = this.distance.y;
		if (this.axis == 'x') {
			distX -= distX > 0 ? this.treshold.start : 0 - this.treshold.start;
		}
		if (this.axis == 'y') {
			distY -= distY > 0 ? this.treshold.start : 0 - this.treshold.start;
		}
		return {
			axis: this.axis,
			direction: direction,
			movementX: this.movement.x,
			movementY: this.movement.y,
			distanceX: distX,
			distanceY: distY
		};
	};
	/**
	 * Dispatch the events
	 * @param {string} eventName    The name for the events to dispatch.
	 * @param {object} detail       The event details.
	 */
	DragEvents.prototype._dispatchEvents = function (eventName, detail) {
		/** General event, e.g. "drag" */
		var event = new CustomEvent(eventName.replace('*', ''), { detail: detail });
		this.surface.dispatchEvent(event);
		/** Axis event, e.g. "dragX" */
		var axis = new CustomEvent(eventName.replace('*', this.axis.toUpperCase()), { detail: detail });
		this.surface.dispatchEvent(axis);
		/** Direction event, e.g. "dragLeft" */
		var direction = new CustomEvent(eventName.replace('*', detail.direction), {
			detail: detail
		});
		this.surface.dispatchEvent(direction);
	};
	/**
	 * Get the dragging direction.
	 * @return {string} The direction in which the user is dragging.
	 */
	DragEvents.prototype._dragDirection = function () {
		return directionNames[this.axis][this.distance[this.axis] > 0 ? 0 : 1];
	};
	/**
	 * Get the dragging direction.
	 * @return {string} The direction in which the user is dragging.
	 */
	DragEvents.prototype._swipeDirection = function () {
		return directionNames[this.axis][this.movement[this.axis] > 0 ? 0 : 1];
	};
	return DragEvents;
}());
/* harmony default export */ var dragevents = (dragevents_DragEvents);

// CONCATENATED MODULE: ./dist/addons/drag/_drag.open.js




/** Instance of the DragEvents class. */
var dragInstance = null;
/** THe node that can be dragged. */
var dragNode = null;
/** How far the page (or menu) can be dragged. */
var maxDistance = 0;
/* harmony default export */ var _drag_open = (function (page) {
	var _this = this;
	/** Variables that vary for each menu position (top, right, bottom, left. front, back). */
	var vars = {};
	/** Whether or not the page or menu is actually being moved. */
	var moving = false;
	/**
	 * Add the dragging events.
	 */
	var addEvents = function () {
		if (dragNode) {
			//  Prepare the page or menu to be moved.
			on(dragNode, 'dragStart', function (evnt) {
				if (evnt['detail'].direction == vars.direction) {
					moving = true;
					//  Class prevents interaction with the page.
					_this.node.wrpr.classList.add('mm-wrapper_dragging');
					//  Prepare the menu to be opened.
					_this._openSetup();
					_this.trigger('open:start');
					//  Get the maximum distance to move out the page or menu.
					maxDistance = _this.node.menu[vars.axis == 'x' ? 'clientWidth' : 'clientHeight'];
				}
			});
			//  Move the page or menu when dragging.
			on(dragNode, 'dragMove', function (evnt) {
				if (evnt['detail'].axis == vars.axis) {
					if (moving) {
						var distance = evnt['detail']['distance' + vars.axis.toUpperCase()];
						switch (vars.position) {
							case 'right':
							case 'bottom':
								distance = Math.min(Math.max(distance, -maxDistance), 0);
								break;
							default:
								distance = Math.max(Math.min(distance, maxDistance), 0);
						}
						//  Deviate for position front (the menu starts out of view).
						if (vars.zposition == 'front') {
							switch (vars.position) {
								case 'right':
								case 'bottom':
									distance += maxDistance;
									break;
								default:
									distance -= maxDistance;
									break;
							}
						}
						vars.slideOutNodes.forEach(function (node) {
							node.style['transform'] =
								'translate' +
									vars.axis.toUpperCase() +
									'(' +
									distance +
									'px)';
						});
					}
				}
			});
			//  Stop the page or menu from being moved.
			on(dragNode, 'dragEnd', function (evnt) {
				if (evnt['detail'].axis == vars.axis) {
					if (moving) {
						moving = false;
						_this.node.wrpr.classList.remove('mm-wrapper_dragging');
						vars.slideOutNodes.forEach(function (node) {
							node.style['transform'] = '';
						});
						//  Determine if the menu should open or close.
						var open_1 = Math.abs(evnt['detail']['distance' + vars.axis.toUpperCase()]) >=
							maxDistance * 0.75;
						if (!open_1) {
							var movement = evnt['detail']['movement' + vars.axis.toUpperCase()];
							switch (vars.position) {
								case 'right':
								case 'bottom':
									open_1 = movement <= 0;
									break;
								default:
									open_1 = movement >= 0;
									break;
							}
						}
						if (open_1) {
							_this._openStart();
						}
						else {
							_this.close();
						}
					}
				}
			});
		}
	};
	/**
	 * Remove the dragging events.
	 */
	var removeEvents = function () {
		if (dragNode) {
			off(dragNode, 'dragStart');
			off(dragNode, 'dragMove');
			off(dragNode, 'dragEnd');
		}
	};
	var addMatchMedia = function () {
		var queries = Object.keys(_this.opts.extensions);
		if (queries.length) {
			//  A media query that'll match if any of the other media query matches:
			//    set the defaults if it doesn't match.
			matchmedia_add(queries.join(', '), function () { }, function () {
				vars = getPositionVars(vars, [], _this.node.menu);
			});
			//  The other media queries.
			queries.forEach(function (query) {
				matchmedia_add(query, function () {
					vars = getPositionVars(vars, _this.opts.extensions[query], _this.node.menu);
				}, function () { });
			});
			//  No extensions, just use the defaults.
		}
		else {
			vars = getPositionVars(vars, [], _this.node.menu);
		}
	};
	//  Remove events from previous "page"
	removeEvents();
	//  Store new "page"
	dragNode = page;
	//  Initialize the drag events.
	dragInstance = new dragevents(dragNode);
	addMatchMedia();
	addMatchMedia = function () { };
	addEvents();
});
var getPositionVars = function (vars, extensions, menu) {
	//  Default position and z-position.
	vars.position = 'left';
	vars.zposition = 'back';
	//  Find position.
	['right', 'top', 'bottom'].forEach(function (pos) {
		if (extensions.indexOf('position-' + pos) > -1) {
			vars.position = pos;
		}
	});
	//  Find z-position.
	['front', 'top', 'bottom'].forEach(function (pos) {
		if (extensions.indexOf('position-' + pos) > -1) {
			vars.zposition = 'front';
		}
	});
	//  Set the area where the dragging can start.
	dragInstance.area = {
		top: vars.position == 'bottom' ? '75%' : 0,
		right: vars.position == 'left' ? '75%' : 0,
		bottom: vars.position == 'top' ? '75%' : 0,
		left: vars.position == 'right' ? '75%' : 0
	};
	//  What side of the menu to measure (width or height).
	//  What axis to drag the menu along (x or y).
	switch (vars.position) {
		case 'top':
		case 'bottom':
			vars.axis = 'y';
			break;
		default:
			vars.axis = 'x';
	}
	//  What direction to drag in.
	switch (vars.position) {
		case 'top':
			vars.direction = 'Down';
			break;
		case 'right':
			vars.direction = 'Left';
			break;
		case 'bottom':
			vars.direction = 'Up';
			break;
		default:
			vars.direction = 'Right';
	}
	//  What nodes to slide out while dragging.
	switch (vars.zposition) {
		case 'front':
			vars.slideOutNodes = [menu];
			break;
		default:
			vars.slideOutNodes = find(document.body, '.mm-slideout');
	}
	return vars;
};

// CONCATENATED MODULE: ./dist/addons/drag/mmenu.drag.js





//	Add the options and configs.
mmenu_oncanvas.options.drag = drag_options;
/* harmony default export */ var mmenu_drag = (function () {
	var _this = this;
	if (!this.opts.offCanvas) {
		return;
	}
	var options = drag_options_extendShorthandOptions(this.opts.drag);
	this.opts.drag = extend(options, mmenu_oncanvas.options.drag);
	//	Drag open the menu
	if (options.open) {
		this.bind('setPage:after', function (page) {
			_drag_open.call(_this, options.node || page);
		});
	}
});

// CONCATENATED MODULE: ./dist/addons/dropdown/_options.js
var dropdown_options_options = {
	drop: false,
	fitViewport: true,
	event: 'click',
	position: {},
	tip: true
};
/* harmony default export */ var dropdown_options = (dropdown_options_options);
/**
 * Extend shorthand options.
 *
 * @param  {object} options The options to extend.
 * @return {object}			The extended options.
 */
function dropdown_options_extendShorthandOptions(options) {
	if (typeof options == 'boolean' && options) {
		options = {
			drop: options
		};
	}
	if (typeof options != 'object') {
		options = {};
	}
	if (typeof options.position == 'string') {
		options.position = {
			of: options.position
		};
	}
	return options;
}
;

// CONCATENATED MODULE: ./dist/addons/dropdown/_configs.js
var dropdown_configs_configs = {
	offset: {
		button: {
			x: -5,
			y: 5
		},
		viewport: {
			x: 20,
			y: 20
		}
	},
	height: {
		max: 880
	},
	width: {
		max: 440
	}
};
/* harmony default export */ var dropdown_configs = (dropdown_configs_configs);

// CONCATENATED MODULE: ./dist/addons/dropdown/mmenu.dropdown.js






//	Add the options and configs.
mmenu_oncanvas.options.dropdown = dropdown_options;
mmenu_oncanvas.configs.dropdown = dropdown_configs;
/* harmony default export */ var mmenu_dropdown = (function () {
	var _this = this;
	if (!this.opts.offCanvas) {
		return;
	}
	var options = dropdown_options_extendShorthandOptions(this.opts.dropdown);
	this.opts.dropdown = extend(options, mmenu_oncanvas.options.dropdown);
	var configs = this.conf.dropdown;
	if (!options.drop) {
		return;
	}
	var button;
	this.bind('initMenu:after', function () {
		_this.node.menu.classList.add('mm-menu_dropdown');
		if (typeof options.position.of != 'string') {
			var id = originalId(_this.node.menu.id);
			if (id) {
				options.position.of = '[href="#' + id + '"]';
			}
		}
		if (typeof options.position.of != 'string') {
			return;
		}
		//	Get the button to put the menu next to
		button = find(document.body, options.position.of)[0];
		//	Emulate hover effect
		var events = options.event.split(' ');
		if (events.length == 1) {
			events[1] = events[0];
		}
		if (events[0] == 'hover') {
			button.addEventListener('mouseenter', function () {
				_this.open();
			}, { passive: true });
		}
		if (events[1] == 'hover') {
			_this.node.menu.addEventListener('mouseleave', function () {
				_this.close();
			}, { passive: true });
		}
	});
	//	Add/remove classname and style when opening/closing the menu
	this.bind('open:start', function () {
		_this.node.menu['mmStyle'] = _this.node.menu.getAttribute('style');
		_this.node.wrpr.classList.add('mm-wrapper_dropdown');
	});
	this.bind('close:finish', function () {
		_this.node.menu.setAttribute('style', _this.node.menu['mmStyle']);
		_this.node.wrpr.classList.remove('mm-wrapper_dropdown');
	});
	/**
	 * Find the position (x, y) and sizes (width, height) for the menu.
	 *
	 * @param  {string} dir The direction to measure ("x" for horizontal, "y" for vertical)
	 * @param  {object} obj The object where (previously) measured values are stored.
	 * @return {object}		The object where measered values are stored.
	 */
	var getPosition = function (dir, obj) {
		var css = obj[0], cls = obj[1];
		var _outerSize = dir == 'x' ? 'offsetWidth' : 'offsetHeight', _startPos = dir == 'x' ? 'left' : 'top', _stopPos = dir == 'x' ? 'right' : 'bottom', _size = dir == 'x' ? 'width' : 'height', _winSize = dir == 'x' ? 'innerWidth' : 'innerHeight', _maxSize = dir == 'x' ? 'maxWidth' : 'maxHeight', _position = null;
		var startPos = offset(button, _startPos), stopPos = startPos + button[_outerSize], windowSize = window[_winSize];
		/** Offset for the menu relative to the button. */
		var offs = configs.offset.button[dir] + configs.offset.viewport[dir];
		//	Position set in option
		if (options.position[dir]) {
			switch (options.position[dir]) {
				case 'left':
				case 'bottom':
					_position = 'after';
					break;
				case 'right':
				case 'top':
					_position = 'before';
					break;
			}
		}
		//	Position not set in option, find most space
		if (_position === null) {
			_position =
				startPos + (stopPos - startPos) / 2 < windowSize / 2
					? 'after'
					: 'before';
		}
		//	Set position and max
		var val, max;
		if (_position == 'after') {
			val = dir == 'x' ? startPos : stopPos;
			max = windowSize - (val + offs);
			css[_startPos] = val + configs.offset.button[dir] + 'px';
			css[_stopPos] = 'auto';
			if (options.tip) {
				cls.push('mm-menu_tip-' + (dir == 'x' ? 'left' : 'top'));
			}
		}
		else {
			val = dir == 'x' ? stopPos : startPos;
			max = val - offs;
			css[_stopPos] =
				'calc( 100% - ' + (val - configs.offset.button[dir]) + 'px )';
			css[_startPos] = 'auto';
			if (options.tip) {
				cls.push('mm-menu_tip-' + (dir == 'x' ? 'right' : 'bottom'));
			}
		}
		if (options.fitViewport) {
			css[_maxSize] = Math.min(configs[_size].max, max) + 'px';
		}
		return [css, cls];
	};
	function position() {
		var _this = this;
		if (!this.vars.opened) {
			return;
		}
		this.node.menu.setAttribute('style', this.node.menu['mmStyle']);
		var obj = [{}, []];
		obj = getPosition.call(this, 'y', obj);
		obj = getPosition.call(this, 'x', obj);
		for (var s in obj[0]) {
			this.node.menu.style[s] = obj[0][s];
		}
		if (options.tip) {
			var classnames = [
				'mm-menu_tip-left',
				'mm-menu_tip-right',
				'mm-menu_tip-top',
				'mm-menu_tip-bottom'
			];
			//  IE11:
			classnames.forEach(function (classname) {
				_this.node.menu.classList.remove(classname);
			});
			obj[1].forEach(function (classname) {
				_this.node.menu.classList.add(classname);
			});
			//  Better browsers:
			// this.node.menu.classList.remove(...classnames);
			// this.node.menu.classList.add(...obj[1]);
		}
	}
	this.bind('open:start', position);
	window.addEventListener('resize', function (evnt) {
		position.call(_this);
	}, { passive: true });
	if (!this.opts.offCanvas.blockUI) {
		window.addEventListener('scroll', function (evnt) {
			position.call(_this);
		}, { passive: true });
	}
});

// CONCATENATED MODULE: ./dist/addons/fixedelements/_configs.js
var fixedelements_configs_configs = {
	insertMethod: 'append',
	insertSelector: 'body'
};
/* harmony default export */ var fixedelements_configs = (fixedelements_configs_configs);

// CONCATENATED MODULE: ./dist/addons/fixedelements/mmenu.fixedelements.js



//	Add the configs.
mmenu_oncanvas.configs.fixedElements = fixedelements_configs;
//	Add the classnames.
mmenu_oncanvas.configs.classNames.fixedElements = {
	fixed: 'Fixed'
};
/* harmony default export */ var mmenu_fixedelements = (function () {
	var _this = this;
	if (!this.opts.offCanvas) {
		return;
	}
	var configs = this.conf.fixedElements;
	var _fixd, fixed, wrppr;
	this.bind('setPage:after', function (page) {
		_fixd = _this.conf.classNames.fixedElements.fixed;
		wrppr = find(document, configs.insertSelector)[0];
		fixed = find(page, '.' + _fixd);
		fixed.forEach(function (fxd) {
			reClass(fxd, _fixd, 'mm-slideout');
			wrppr[configs.insertMethod](fxd);
		});
	});
});

// CONCATENATED MODULE: ./dist/addons/iconbar/_options.js

var iconbar_options_options = {
	use: false,
	top: [],
	bottom: [],
	position: 'left',
	type: 'default'
};
/* harmony default export */ var iconbar_options = (iconbar_options_options);
/**
 * Extend shorthand options.
 *
 * @param  {object} options The options to extend.
 * @return {object}			The extended options.
 */
function iconbar_options_extendShorthandOptions(options) {
	if (type(options) == 'array') {
		options = {
			use: true,
			top: options
		};
	}
	if (type(options) != 'object') {
		options = {};
	}
	if (typeof options.use == 'undefined') {
		options.use = true;
	}
	if (typeof options.use == 'boolean' && options.use) {
		options.use = true;
	}
	return options;
}

// CONCATENATED MODULE: ./dist/addons/iconbar/mmenu.iconbar.js






//  Add the options.
mmenu_oncanvas.options.iconbar = iconbar_options;
/* harmony default export */ var mmenu_iconbar = (function () {
	var _this = this;
	var options = iconbar_options_extendShorthandOptions(this.opts.iconbar);
	this.opts.iconbar = extend(options, mmenu_oncanvas.options.iconbar);
	if (!options.use) {
		return;
	}
	var iconbar;
	['top', 'bottom'].forEach(function (position, n) {
		var ctnt = options[position];
		//	Extend shorthand options
		if (type(ctnt) != 'array') {
			ctnt = [ctnt];
		}
		//	Create node
		var part = create('div.mm-iconbar__' + position);
		//	Add content
		for (var c = 0, l = ctnt.length; c < l; c++) {
			if (typeof ctnt[c] == 'string') {
				part.innerHTML += ctnt[c];
			}
			else {
				part.append(ctnt[c]);
			}
		}
		if (part.children.length) {
			if (!iconbar) {
				iconbar = create('div.mm-iconbar');
			}
			iconbar.append(part);
		}
	});
	//	Add to menu
	if (iconbar) {
		//	Add the iconbar.
		this.bind('initMenu:after', function () {
			_this.node.menu.prepend(iconbar);
		});
		//	En-/disable the iconbar.
		var classname_1 = 'mm-menu_iconbar-' + options.position;
		var enable = function () {
			_this.node.menu.classList.add(classname_1);
			mmenu_oncanvas.sr_aria(iconbar, 'hidden', false);
		};
		var disable = function () {
			_this.node.menu.classList.remove(classname_1);
			mmenu_oncanvas.sr_aria(iconbar, 'hidden', true);
		};
		if (typeof options.use == 'boolean') {
			this.bind('initMenu:after', enable);
		}
		else {
			matchmedia_add(options.use, enable, disable);
		}
		//	Tabs
		if (options.type == 'tabs') {
			iconbar.classList.add('mm-iconbar_tabs');
			iconbar.addEventListener('click', function (evnt) {
				var anchor = evnt.target;
				if (!anchor.matches('a')) {
					return;
				}
				if (anchor.matches('.mm-iconbar__tab_selected')) {
					evnt.stopImmediatePropagation();
					return;
				}
				try {
					var panel = _this.node.menu.querySelector(anchor.getAttribute('href'))[0];
					if (panel && panel.matches('.mm-panel')) {
						evnt.preventDefault();
						evnt.stopImmediatePropagation();
						_this.openPanel(panel, false);
					}
				}
				catch (err) { }
			});
			var selectTab_1 = function (panel) {
				find(iconbar, 'a').forEach(function (anchor) {
					anchor.classList.remove('mm-iconbar__tab_selected');
				});
				var anchor = find(iconbar, '[href="#' + panel.id + '"]')[0];
				if (anchor) {
					anchor.classList.add('mm-iconbar__tab_selected');
				}
				else {
					var parent_1 = panel['mmParent'];
					if (parent_1) {
						selectTab_1(parent_1.closest('.mm-panel'));
					}
				}
			};
			this.bind('openPanel:start', selectTab_1);
		}
	}
});

// CONCATENATED MODULE: ./dist/addons/iconpanels/_options.js
var iconpanels_options_options = {
	add: false,
	blockPanel: true,
	hideDivider: false,
	hideNavbar: true,
	visible: 3
};
/* harmony default export */ var iconpanels_options = (iconpanels_options_options);
/**
 * Extend shorthand options.
 *
 * @param  {object} options The options to extend.
 * @return {object}			The extended options.
 */
function iconpanels_options_extendShorthandOptions(options) {
	if (typeof options == 'boolean') {
		options = {
			add: options
		};
	}
	if (typeof options == 'number' ||
		typeof options == 'string') {
		options = {
			add: true,
			visible: options
		};
	}
	if (typeof options != 'object') {
		options = {};
	}
	return options;
}
;

// CONCATENATED MODULE: ./dist/addons/iconpanels/mmenu.iconpanels.js





//	Add the options.
mmenu_oncanvas.options.iconPanels = iconpanels_options;
/* harmony default export */ var mmenu_iconpanels = (function () {
	var _this = this;
	var options = iconpanels_options_extendShorthandOptions(this.opts.iconPanels);
	this.opts.iconPanels = extend(options, mmenu_oncanvas.options.iconPanels);
	var keepFirst = false;
	if (options.visible == 'first') {
		keepFirst = true;
		options.visible = 1;
	}
	options.visible = Math.min(3, Math.max(1, options.visible));
	options.visible++;
	//	Add the iconpanels
	if (options.add) {
		this.bind('initMenu:after', function () {
			var classnames = ['mm-menu_iconpanel'];
			if (options.hideNavbar) {
				classnames.push('mm-menu_hidenavbar');
			}
			if (options.hideDivider) {
				classnames.push('mm-menu_hidedivider');
			}
			//  IE11:
			classnames.forEach(function (classname) {
				_this.node.menu.classList.add(classname);
			});
			//  Better browsers:
			// this.node.menu.classList.add(...classnames);
		});
		var classnames_1 = [];
		if (!keepFirst) {
			for (var i = 0; i <= options.visible; i++) {
				classnames_1.push('mm-panel_iconpanel-' + i);
			}
		}
		this.bind('openPanel:start', function (panel) {
			var panels = dom_children(_this.node.pnls, '.mm-panel');
			panel = panel || panels[0];
			if (panel.parentElement.matches('.mm-listitem_vertical')) {
				return;
			}
			if (keepFirst) {
				panels.forEach(function (panel, p) {
					panel.classList[p == 0 ? 'add' : 'remove']('mm-panel_iconpanel-first');
				});
			}
			else {
				//	Remove the "iconpanel" classnames from all panels.
				panels.forEach(function (panel) {
					//  IE11:
					classnames_1.forEach(function (classname) {
						panel.classList.remove(classname);
					});
					//  Better browsers:
					// panel.classList.remove(...classnames);
				});
				//	Filter out panels that are not opened.
				panels = panels.filter(function (panel) {
					return panel.matches('.mm-panel_opened-parent');
				});
				//	Add the current panel to the list.
				var panelAdded_1 = false;
				panels.forEach(function (elem) {
					if (panel === elem) {
						panelAdded_1 = true;
					}
				});
				if (!panelAdded_1) {
					panels.push(panel);
				}
				//	Remove the "hidden" classname from all opened panels.
				panels.forEach(function (panel) {
					panel.classList.remove('mm-hidden');
				});
				//	Slice the opened panels to the max visible amount.
				panels = panels.slice(-options.visible);
				//	Add the "iconpanel" classnames.
				panels.forEach(function (panel, p) {
					panel.classList.add('mm-panel_iconpanel-' + p);
				});
			}
		});
		this.bind('initPanel:after', function (panel) {
			if (options.blockPanel &&
				!panel.parentElement.matches('.mm-listitem_vertical') &&
				!dom_children(panel, '.mm-panel__blocker')[0]) {
				var blocker = create('a.mm-panel__blocker');
				blocker.setAttribute('href', '#' + panel.closest('.mm-panel').id);
				panel.prepend(blocker);
			}
		});
	}
});

// CONCATENATED MODULE: ./dist/addons/keyboardnavigation/_options.js
var keyboardnavigation_options_options = {
	enable: false,
	enhance: false
};
/* harmony default export */ var keyboardnavigation_options = (keyboardnavigation_options_options);
/**
 * Extend shorthand options.
 *
 * @param  {object} options The options to extend.
 * @return {object}			The extended options.
 */
function keyboardnavigation_options_extendShorthandOptions(options) {
	if (typeof options == 'boolean' || typeof options == 'string') {
		options = {
			enable: options
		};
	}
	if (typeof options != 'object') {
		options = {};
	}
	return options;
}
;

// CONCATENATED MODULE: ./dist/addons/keyboardnavigation/mmenu.keyboardnavigation.js







//  Add the options.
mmenu_oncanvas.options.keyboardNavigation = keyboardnavigation_options;
/* harmony default export */ var mmenu_keyboardnavigation = (function () {
	var _this = this;
	//	Keyboard navigation on touchscreens opens the virtual keyboard :/
	//	Lets prevent that.
	if (touch) {
		return;
	}
	var options = keyboardnavigation_options_extendShorthandOptions(this.opts.keyboardNavigation);
	this.opts.keyboardNavigation = extend(options, mmenu_oncanvas.options.keyboardNavigation);
	//	Enable keyboard navigation
	if (options.enable) {
		var menuStart_1 = create('button.mm-tabstart.mm-sronly'), menuEnd_1 = create('button.mm-tabend.mm-sronly'), blockerEnd_1 = create('button.mm-tabend.mm-sronly');
		this.bind('initMenu:after', function () {
			if (options.enhance) {
				_this.node.menu.classList.add('mm-menu_keyboardfocus');
			}
			mmenu_keyboardnavigation_initWindow.call(_this, options.enhance);
		});
		this.bind('initOpened:before', function () {
			_this.node.menu.prepend(menuStart_1);
			_this.node.menu.append(menuEnd_1);
			dom_children(_this.node.menu, '.mm-navbars-top, .mm-navbars-bottom').forEach(function (navbars) {
				navbars.querySelectorAll('.mm-navbar__title').forEach(function (title) {
					title.setAttribute('tabindex', '-1');
				});
			});
		});
		this.bind('initBlocker:after', function () {
			mmenu_oncanvas.node.blck.append(blockerEnd_1);
			dom_children(mmenu_oncanvas.node.blck, 'a')[0].classList.add('mm-tabstart');
		});
		var focusable_1 = 'input, select, textarea, button, label, a[href]';
		var setFocus = function (panel) {
			panel =
				panel || dom_children(_this.node.pnls, '.mm-panel_opened')[0];
			var focus = null;
			//	Focus already is on an element in a navbar in this menu.
			var navbar = document.activeElement.closest('.mm-navbar');
			if (navbar) {
				if (navbar.closest('.mm-menu') == _this.node.menu) {
					return;
				}
			}
			//	Set the focus to the first focusable element by default.
			if (options.enable == 'default') {
				//	First visible anchor in a listview in the current panel.
				focus = find(panel, '.mm-listview a[href]:not(.mm-hidden)')[0];
				//	First focusable and visible element in the current panel.
				if (!focus) {
					focus = find(panel, focusable_1 + ':not(.mm-hidden)')[0];
				}
				//	First focusable and visible element in a navbar.
				if (!focus) {
					var elements_1 = [];
					dom_children(_this.node.menu, '.mm-navbars_top, .mm-navbars_bottom').forEach(function (navbar) {
						elements_1.push.apply(elements_1, find(navbar, focusable_1 + ':not(.mm-hidden)'));
					});
					focus = elements_1[0];
				}
			}
			//	Default.
			if (!focus) {
				focus = dom_children(_this.node.menu, '.mm-tabstart')[0];
			}
			if (focus) {
				focus.focus();
			}
		};
		this.bind('open:finish', setFocus);
		this.bind('openPanel:finish', setFocus);
		//	Add screenreader / aria support.
		this.bind('initOpened:after:sr-aria', function () {
			[_this.node.menu, mmenu_oncanvas.node.blck].forEach(function (element) {
				dom_children(element, '.mm-tabstart, .mm-tabend').forEach(function (tabber) {
					mmenu_oncanvas.sr_aria(tabber, 'hidden', true);
					mmenu_oncanvas.sr_role(tabber, 'presentation');
				});
			});
		});
	}
});
/**
 * Initialize the window for keyboard navigation.
 * @param {boolean} enhance - Whether or not to also rich enhance the keyboard behavior.
 **/
var mmenu_keyboardnavigation_initWindow = function (enhance) {
	var _this = this;
	//	Re-enable tabbing in general
	off(document.body, 'keydown.tabguard');
	//	Intersept the target when tabbing.
	off(document.body, 'focusin.tabguard');
	on(document.body, 'focusin.tabguard', function (evnt) {
		if (_this.node.wrpr.matches('.mm-wrapper_opened')) {
			var target = evnt.target;
			if (target.matches('.mm-tabend')) {
				var next = void 0;
				//	Jump from menu to blocker.
				if (target.parentElement.matches('.mm-menu')) {
					if (mmenu_oncanvas.node.blck) {
						next = mmenu_oncanvas.node.blck;
					}
				}
				//	Jump to opened menu.
				if (target.parentElement.matches('.mm-wrapper__blocker')) {
					next = find(document.body, '.mm-menu_offcanvas.mm-menu_opened')[0];
				}
				//	If no available element found, stay in current element.
				if (!next) {
					next = target.parentElement;
				}
				if (next) {
					dom_children(next, '.mm-tabstart')[0].focus();
				}
			}
		}
	});
	//	Add Additional keyboard behavior.
	off(document.body, 'keydown.navigate');
	on(document.body, 'keydown.navigate', function (evnt) {
		var target = evnt.target;
		var menu = target.closest('.mm-menu');
		if (menu) {
			var api = menu['mmApi'];
			if (!target.matches('input, textarea')) {
				switch (evnt.keyCode) {
					//	press enter to toggle and check
					case 13:
						if (target.matches('.mm-toggle') ||
							target.matches('.mm-check')) {
							target.dispatchEvent(new Event('click'));
						}
						break;
					//	prevent spacebar or arrows from scrolling the page
					case 32: //	space
					case 37: //	left
					case 38: //	top
					case 39: //	right
					case 40: //	bottom
						evnt.preventDefault();
						break;
				}
			}
			if (enhance) {
				//	special case for input
				if (target.matches('input')) {
					switch (evnt.keyCode) {
						//	empty searchfield with esc
						case 27:
							target.value = '';
							break;
					}
				}
				else {
					var api_1 = menu['mmApi'];
					switch (evnt.keyCode) {
						//	close submenu with backspace
						case 8:
							var parent_1 = find(menu, '.mm-panel_opened')[0]['mmParent'];
							if (parent_1) {
								api_1.openPanel(parent_1.closest('.mm-panel'));
							}
							break;
						//	close menu with esc
						case 27:
							if (menu.matches('.mm-menu_offcanvas')) {
								api_1.close();
							}
							break;
					}
				}
			}
		}
	});
};

// CONCATENATED MODULE: ./dist/addons/lazysubmenus/_options.js
var lazysubmenus_options_options = {
	load: false
};
/* harmony default export */ var lazysubmenus_options = (lazysubmenus_options_options);
/**
 * Extend shorthand options.
 *
 * @param  {object} options The options to extend.
 * @return {object}			The extended options.
 */
function lazysubmenus_options_extendShorthandOptions(options) {
	if (typeof options == 'boolean') {
		options = {
			load: options
		};
	}
	if (typeof options != 'object') {
		options = {};
	}
	return options;
}
;

// CONCATENATED MODULE: ./dist/addons/lazysubmenus/mmenu.lazysubmenus.js





//	Add the options.
mmenu_oncanvas.options.lazySubmenus = lazysubmenus_options;
/* harmony default export */ var mmenu_lazysubmenus = (function () {
	var _this = this;
	var options = lazysubmenus_options_extendShorthandOptions(this.opts.lazySubmenus);
	this.opts.lazySubmenus = extend(options, mmenu_oncanvas.options.lazySubmenus);
	if (options.load) {
		//	Prevent all sub panels from being initialized.
		this.bind('initMenu:after', function () {
			var panels = [];
			//	Find all potential subpanels.
			find(_this.node.pnls, 'li').forEach(function (listitem) {
				panels.push.apply(panels, dom_children(listitem, _this.conf.panelNodetype.join(', ')));
			});
			//	Filter out all non-panels and add the lazyload classes
			panels
				.filter(function (panel) { return !panel.matches('.mm-listview_inset'); })
				.filter(function (panel) { return !panel.matches('.mm-nolistview'); })
				.filter(function (panel) { return !panel.matches('.mm-nopanel'); })
				.forEach(function (panel) {
				var classnames = [
					'mm-panel_lazysubmenu',
					'mm-nolistview',
					'mm-nopanel'
				];
				//  IE11:
				classnames.forEach(function (classname) {
					panel.classList.add(classname);
				});
				//  Better browsers:
				// panel.classList.add(...classnames);
			});
		});
		//	Prepare current and one level sub panels for initPanels
		this.bind('initPanels:before', function () {
			var panels = dom_children(_this.node.pnls, _this.conf.panelNodetype.join(', '));
			panels.forEach(function (panel) {
				var filter = '.mm-panel_lazysubmenu', children = find(panel, filter);
				if (panel.matches(filter)) {
					children.unshift(panel);
				}
				children
					.filter(function (child) {
					return !child.matches('.mm-panel_lazysubmenu .mm-panel_lazysubmenu');
				})
					.forEach(function (child) {
					var classnames = [
						'mm-panel_lazysubmenu',
						'mm-nolistview',
						'mm-nopanel'
					];
					//  IE11:
					classnames.forEach(function (classname) {
						child.classList.remove(classname);
					});
					//  Better browsers:
					// child.classList.remove(...classnames);
				});
			});
		});
		//	initPanels for the default opened panel
		this.bind('initOpened:before', function () {
			var panels = [];
			find(_this.node.pnls, '.' + _this.conf.classNames.selected).forEach(function (listitem) {
				panels.push.apply(panels, dom_parents(listitem, '.mm-panel_lazysubmenu'));
			});
			if (panels.length) {
				panels.forEach(function (panel) {
					var classnames = [
						'mm-panel_lazysubmenu',
						'mm-nolistview',
						'mm-nopanel'
					];
					//  IE11:
					classnames.forEach(function (classname) {
						panel.classList.remove(classname);
					});
					//  Better browsers:
					// panel.classList.remove(...classnames);
				});
				_this.initPanel(panels[panels.length - 1]);
			}
		});
		//	initPanels for current- and sub panels before openPanel
		this.bind('openPanel:before', function (panel) {
			var filter = '.mm-panel_lazysubmenu', panels = find(panel, filter);
			if (panel.matches(filter)) {
				panels.unshift(panel);
			}
			panels = panels.filter(function (panel) {
				return !panel.matches('.mm-panel_lazysubmenu .mm-panel_lazysubmenu');
			});
			panels.forEach(function (panel) {
				_this.initPanel(panel);
			});
		});
	}
});

// CONCATENATED MODULE: ./dist/addons/navbars/_options.js
var navbars_options_options = [];
/* harmony default export */ var navbars_options = (navbars_options_options);
/**
 * Extend shorthand options.
 *
 * @param  {object} options The options to extend.
 * @return {object}			The extended options.
 */
function navbars_options_extendShorthandOptions(options) {
	if (typeof options == 'boolean' && options) {
		options = {};
	}
	if (typeof options != 'object') {
		options = {};
	}
	if (typeof options.content == 'undefined') {
		options.content = ['prev', 'title'];
	}
	if (!(options.content instanceof Array)) {
		options.content = [options.content];
	}
	if (typeof options.use == 'undefined') {
		options.use = true;
	}
	if (typeof options.use == 'boolean' && options.use) {
		options.use = true;
	}
	return options;
}
;

// CONCATENATED MODULE: ./dist/addons/navbars/_configs.js
var navbars_configs_configs = {
	breadcrumbs: {
		separator: '/',
		removeFirst: false
	}
};
/* harmony default export */ var navbars_configs = (navbars_configs_configs);

// CONCATENATED MODULE: ./dist/addons/navbars/_navbar.breadcrumbs.js


/* harmony default export */ var _navbar_breadcrumbs = (function (navbar) {
	var _this = this;
	//	Add content
	var breadcrumbs = create('div.mm-navbar__breadcrumbs');
	navbar.append(breadcrumbs);
	this.bind('initNavbar:after', function (panel) {
		if (panel.querySelector('.mm-navbar__breadcrumbs')) {
			return;
		}
		dom_children(panel, '.mm-navbar')[0].classList.add('mm-hidden');
		var crumbs = [], breadcrumbs = create('span.mm-navbar__breadcrumbs'), current = panel, first = true;
		while (current) {
			current = current.closest('.mm-panel');
			if (!current.parentElement.matches('.mm-listitem_vertical')) {
				var title = find(current, '.mm-navbar__title span')[0];
				if (title) {
					var text = title.textContent;
					if (text.length) {
						crumbs.unshift(first
							? '<span>' + text + '</span>'
							: '<a href="#' +
								current.id +
								'">' +
								text +
								'</a>');
					}
				}
				first = false;
			}
			current = current['mmParent'];
		}
		if (_this.conf.navbars.breadcrumbs.removeFirst) {
			crumbs.shift();
		}
		breadcrumbs.innerHTML = crumbs.join('<span class="mm-separator">' +
			_this.conf.navbars.breadcrumbs.separator +
			'</span>');
		dom_children(panel, '.mm-navbar')[0].append(breadcrumbs);
	});
	//	Update for to opened panel
	this.bind('openPanel:start', function (panel) {
		var crumbs = panel.querySelector('.mm-navbar__breadcrumbs');
		breadcrumbs.innerHTML = crumbs ? crumbs.innerHTML : '';
	});
	//	Add screenreader / aria support
	this.bind('initNavbar:after:sr-aria', function (panel) {
		find(panel, '.mm-breadcrumbs a').forEach(function (anchor) {
			mmenu_oncanvas.sr_aria(anchor, 'owns', anchor.getAttribute('href').slice(1));
		});
	});
});

// CONCATENATED MODULE: ./dist/addons/navbars/_navbar.close.js


/* harmony default export */ var _navbar_close = (function (navbar) {
	var _this = this;
	//	Add content
	var close = create('a.mm-btn.mm-btn_close.mm-navbar__btn');
	navbar.append(close);
	//	Update to page node
	this.bind('setPage:after', function (page) {
		close.setAttribute('href', '#' + page.id);
	});
	//	Add screenreader / text support
	this.bind('setPage:after:sr-text', function () {
		close.innerHTML = mmenu_oncanvas.sr_text(_this.i18n(_this.conf.screenReader.text.closeMenu));
		mmenu_oncanvas.sr_aria(close, 'owns', close.getAttribute('href').slice(1));
	});
});

// CONCATENATED MODULE: ./dist/addons/navbars/_navbar.prev.js


/* harmony default export */ var _navbar_prev = (function (navbar) {
	var _this = this;
	//	Add content.
	var prev = create('a.mm-btn.mm-btn_prev.mm-navbar__btn');
	navbar.append(prev);
	this.bind('initNavbar:after', function (panel) {
		dom_children(panel, '.mm-navbar')[0].classList.add('mm-hidden');
	});
	//	Update to opened panel.
	var org;
	var _url, _txt;
	this.bind('openPanel:start', function (panel) {
		if (panel.parentElement.matches('.mm-listitem_vertical')) {
			return;
		}
		org = panel.querySelector('.' + _this.conf.classNames.navbars.panelPrev);
		if (!org) {
			org = panel.querySelector('.mm-navbar__btn.mm-btn_prev');
		}
		_url = org ? org.getAttribute('href') : '';
		_txt = org ? org.innerHTML : '';
		if (_url) {
			prev.setAttribute('href', _url);
		}
		else {
			prev.removeAttribute('href');
		}
		prev.classList[_url || _txt ? 'remove' : 'add']('mm-hidden');
		prev.innerHTML = _txt;
	});
	//	Add screenreader / aria support
	this.bind('initNavbar:after:sr-aria', function (panel) {
		mmenu_oncanvas.sr_aria(panel.querySelector('.mm-navbar'), 'hidden', true);
	});
	this.bind('openPanel:start:sr-aria', function (panel) {
		mmenu_oncanvas.sr_aria(prev, 'hidden', prev.matches('.mm-hidden'));
		mmenu_oncanvas.sr_aria(prev, 'owns', (prev.getAttribute('href') || '').slice(1));
	});
});

// CONCATENATED MODULE: ./dist/addons/navbars/_navbar.searchfield.js


/* harmony default export */ var _navbar_searchfield = (function (navbar) {
	if (type(this.opts.searchfield) != 'object') {
		this.opts.searchfield = {};
	}
	var searchfield = create('div.mm-navbar__searchfield');
	navbar.append(searchfield);
	this.opts.searchfield.add = true;
	this.opts.searchfield.addTo = [searchfield];
});

// CONCATENATED MODULE: ./dist/addons/navbars/_navbar.title.js


/* harmony default export */ var _navbar_title = (function (navbar) {
	var _this = this;
	//	Add content to the navbar.
	var title = create('a.mm-navbar__title');
	var titleText = create('span');
	title.append(titleText);
	navbar.append(title);
	//	Update the title to the opened panel.
	var _url, _txt;
	var original;
	this.bind('openPanel:start', function (panel) {
		//	Do nothing in a vertically expanding panel.
		if (panel.parentElement.matches('.mm-listitem_vertical')) {
			return;
		}
		//	Find the original title in the opened panel.
		original = panel.querySelector('.' + _this.conf.classNames.navbars.panelTitle);
		if (!original) {
			original = panel.querySelector('.mm-navbar__title span');
		}
		//	Get the URL for the title.
		_url =
			original && original.closest('a')
				? original.closest('a').getAttribute('href')
				: '';
		if (_url) {
			title.setAttribute('href', _url);
		}
		else {
			title.removeAttribute('href');
		}
		//	Get the text for the title.
		_txt = original ? original.innerHTML : '';
		titleText.innerHTML = _txt;
	});
	//	Add screenreader / aria support
	var prev;
	this.bind('openPanel:start:sr-aria', function (panel) {
		if (_this.opts.screenReader.text) {
			if (!prev) {
				var navbars = dom_children(_this.node.menu, '.mm-navbars_top, .mm-navbars_bottom');
				navbars.forEach(function (navbar) {
					var btn = navbar.querySelector('.mm-btn_prev');
					if (btn) {
						prev = btn;
					}
				});
			}
			if (prev) {
				var hidden = true;
				if (_this.opts.navbar.titleLink == 'parent') {
					hidden = !prev.matches('.mm-hidden');
				}
				mmenu_oncanvas.sr_aria(title, 'hidden', hidden);
			}
		}
	});
});

// CONCATENATED MODULE: ./dist/addons/navbars/_navbar.tabs.js

/* harmony default export */ var _navbar_tabs = (function (navbar) {
	var _this = this;
	navbar.classList.add('mm-navbar_tabs');
	navbar.parentElement.classList.add('mm-navbars_has-tabs');
	var anchors = dom_children(navbar, 'a');
	navbar.addEventListener('click', function (evnt) {
		var anchor = evnt.target;
		if (!anchor.matches('a')) {
			return;
		}
		if (anchor.matches('.mm-navbar__tab_selected')) {
			evnt.stopImmediatePropagation();
			return;
		}
		try {
			_this.openPanel(_this.node.menu.querySelector(anchor.getAttribute('href')), false);
			evnt.stopImmediatePropagation();
		}
		catch (err) { }
	});
	function selectTab(panel) {
		anchors.forEach(function (anchor) {
			anchor.classList.remove('mm-navbar__tab_selected');
		});
		var anchor = anchors.filter(function (anchor) {
			return anchor.matches('[href="#' + panel.id + '"]');
		})[0];
		if (anchor) {
			anchor.classList.add('mm-navbar__tab_selected');
		}
		else {
			var parent = panel['mmParent'];
			if (parent) {
				selectTab.call(this, parent.closest('.mm-panel'));
			}
		}
	}
	this.bind('openPanel:start', selectTab);
});

// CONCATENATED MODULE: ./dist/addons/navbars/mmenu.navbars.js






//  Add the options and configs.
mmenu_oncanvas.options.navbars = navbars_options;
mmenu_oncanvas.configs.navbars = navbars_configs;
//  Add the classnames.
mmenu_oncanvas.configs.classNames.navbars = {
	panelPrev: 'Prev',
	panelTitle: 'Title'
};





Navbars.navbarContents = {
	breadcrumbs: _navbar_breadcrumbs,
	close: _navbar_close,
	prev: _navbar_prev,
	searchfield: _navbar_searchfield,
	title: _navbar_title
};

Navbars.navbarTypes = {
	tabs: _navbar_tabs
};
function Navbars() {
	var _this = this;
	var navs = this.opts.navbars;
	if (typeof navs == 'undefined') {
		return;
	}
	if (!(navs instanceof Array)) {
		navs = [navs];
	}
	var navbars = {};
	if (!navs.length) {
		return;
	}
	navs.forEach(function (options) {
		options = navbars_options_extendShorthandOptions(options);
		if (!options.use) {
			return false;
		}
		//	Create the navbar element.
		var navbar = create('div.mm-navbar');
		//	Get the position for the navbar.
		var position = options.position;
		//	Restrict the position to either "bottom" or "top" (default).
		if (position !== 'bottom') {
			position = 'top';
		}
		//	Create the wrapper for the navbar position.
		if (!navbars[position]) {
			navbars[position] = create('div.mm-navbars_' + position);
		}
		navbars[position].append(navbar);
		//	Add content to the navbar.
		for (var c = 0, l = options.content.length; c < l; c++) {
			var ctnt = options.content[c];
			//	The content is a string.
			if (typeof ctnt == 'string') {
				var func = Navbars.navbarContents[ctnt];
				//	The content refers to one of the navbar-presets ("prev", "title", etc).
				if (typeof func == 'function') {
					//	Call the preset function.
					func.call(_this, navbar);
					//	The content is just HTML.
				}
				else {
					//	Add the HTML.
					//  Wrap the HTML in a single node
					var node = create('span');
					node.innerHTML = ctnt;
					//  If there was only a single node, use that.
					var children = dom_children(node);
					if (children.length == 1) {
						node = children[0];
					}
					navbar.append(node);
				}
				//	The content is not a string, it must be an element.
			}
			else {
				navbar.append(ctnt);
			}
		}
		//	The type option is set.
		if (typeof options.type == 'string') {
			//	The function refers to one of the navbar-presets ("tabs").
			var func = Navbars.navbarTypes[options.type];
			if (typeof func == 'function') {
				//	Call the preset function.
				func.call(_this, navbar);
			}
		}
		//	En-/disable the navbar.
		var enable = function () {
			navbar.classList.remove('mm-hidden');
			mmenu_oncanvas.sr_aria(navbar, 'hidden', false);
		};
		var disable = function () {
			navbar.classList.add('mm-hidden');
			mmenu_oncanvas.sr_aria(navbar, 'hidden', true);
		};
		if (typeof options.use != 'boolean') {
			matchmedia_add(options.use, enable, disable);
		}
	});
	//	Add to menu.
	this.bind('initMenu:after', function () {
		for (var position in navbars) {
			_this.node.menu[position == 'bottom' ? 'append' : 'prepend'](navbars[position]);
		}
	});
}

// CONCATENATED MODULE: ./dist/addons/pagescroll/_options.js
var pagescroll_options_options = {
	scroll: false,
	update: false
};
/* harmony default export */ var pagescroll_options = (pagescroll_options_options);
/**
 * Extend shorthand options.
 *
 * @param  {object} options The options to extend.
 * @return {object}			The extended options.
 */
function pagescroll_options_extendShorthandOptions(options) {
	if (typeof options == 'boolean') {
		options = {
			scroll: options
		};
	}
	if (typeof options != 'object') {
		options = {};
	}
	return options;
}
;

// CONCATENATED MODULE: ./dist/addons/pagescroll/_configs.js
var pagescroll_configs_configs = {
	scrollOffset: 0,
	updateOffset: 50
};
/* harmony default export */ var pagescroll_configs = (pagescroll_configs_configs);

// CONCATENATED MODULE: ./dist/addons/pagescroll/mmenu.pagescroll.js






//	Add the options and configs.
mmenu_oncanvas.options.pageScroll = pagescroll_options;
mmenu_oncanvas.configs.pageScroll = pagescroll_configs;
/* harmony default export */ var mmenu_pagescroll = (function () {
	var _this = this;
	var options = pagescroll_options_extendShorthandOptions(this.opts.pageScroll);
	this.opts.pageScroll = extend(options, mmenu_oncanvas.options.pageScroll);
	var configs = this.conf.pageScroll;
	/** The currently "active" section */
	var section;
	function scrollTo() {
		if (section) {
			// section.scrollIntoView({ behavior: 'smooth' });
			window.scrollTo({
				top: section.getBoundingClientRect().top +
					document.scrollingElement.scrollTop -
					configs.scrollOffset,
				behavior: 'smooth'
			});
		}
		section = null;
	}
	function anchorInPage(href) {
		try {
			if (href != '#' && href.slice(0, 1) == '#') {
				return mmenu_oncanvas.node.page.querySelector(href);
			}
			return null;
		}
		catch (err) {
			return null;
		}
	}
	//	Scroll to section after clicking menu item.
	if (options.scroll) {
		this.bind('close:finish', function () {
			scrollTo();
		});
	}
	//	Add click behavior.
	//	Prevents default behavior when clicking an anchor.
	if (this.opts.offCanvas && options.scroll) {
		this.clck.push(function (anchor, args) {
			section = null;
			//	Don't continue if the clicked anchor is not in the menu.
			if (!args.inMenu) {
				return;
			}
			//	Don't continue if the targeted section is not on the page.
			var href = anchor.getAttribute('href');
			section = anchorInPage(href);
			if (!section) {
				return;
			}
			//	If the sidebar add-on is "expanded"...
			if (_this.node.menu.matches('.mm-menu_sidebar-expanded') &&
				_this.node.wrpr.matches('.mm-wrapper_sidebar-expanded')) {
				//	... scroll the page to the section.
				scrollTo();
				//	... otherwise...
			}
			else {
				//	... close the menu.
				return {
					close: true
				};
			}
		});
	}
	//	Update selected menu item after scrolling.
	if (options.update) {
		var scts_1 = [];
		this.bind('initListview:after', function (listview) {
			var listitems = dom_children(listview, '.mm-listitem');
			filterLIA(listitems).forEach(function (anchor) {
				var href = anchor.getAttribute('href');
				var section = anchorInPage(href);
				if (section) {
					scts_1.unshift(section);
				}
			});
		});
		var _selected_1 = -1;
		window.addEventListener('scroll', function (evnt) {
			var scrollTop = window.scrollY;
			for (var s = 0; s < scts_1.length; s++) {
				if (scts_1[s].offsetTop < scrollTop + configs.updateOffset) {
					if (_selected_1 !== s) {
						_selected_1 = s;
						var panel = dom_children(_this.node.pnls, '.mm-panel_opened')[0];
						var listitems = find(panel, '.mm-listitem');
						var anchors = filterLIA(listitems);
						anchors = anchors.filter(function (anchor) {
							return anchor.matches('[href="#' + scts_1[s].id + '"]');
						});
						if (anchors.length) {
							_this.setSelected(anchors[0].parentElement);
						}
					}
					break;
				}
			}
		});
	}
});

// CONCATENATED MODULE: ./dist/addons/searchfield/_options.js
var searchfield_options_options = {
	add: false,
	addTo: 'panels',
	cancel: false,
	noResults: 'No results found.',
	placeholder: 'Search',
	panel: {
		add: false,
		dividers: true,
		fx: 'none',
		id: null,
		splash: null,
		title: 'Search'
	},
	search: true,
	showTextItems: false,
	showSubPanels: true
};
/* harmony default export */ var searchfield_options = (searchfield_options_options);
/**
 * Extend shorthand options.
 *
 * @param  {object} options The options to extend.
 * @return {object}			The extended options.
 */
function searchfield_options_extendShorthandOptions(options) {
	if (typeof options == 'boolean') {
		options = {
			add: options
		};
	}
	if (typeof options != 'object') {
		options = {};
	}
	if (typeof options.panel == 'boolean') {
		options.panel = {
			add: options.panel
		};
	}
	if (typeof options.panel != 'object') {
		options.panel = {};
	}
	//	Extend logical options.
	if (options.addTo == 'panel') {
		options.panel.add = true;
	}
	if (options.panel.add) {
		options.showSubPanels = false;
		if (options.panel.splash) {
			options.cancel = true;
		}
	}
	return options;
}
;

// CONCATENATED MODULE: ./dist/addons/searchfield/_configs.js
var searchfield_configs_configs = {
	clear: false,
	form: false,
	input: false,
	submit: false
};
/* harmony default export */ var searchfield_configs = (searchfield_configs_configs);

// CONCATENATED MODULE: ./dist/addons/searchfield/translations/nl.js
/* harmony default export */ var searchfield_translations_nl = ({
	Search: 'Zoeken',
	'No results found.': 'Geen resultaten gevonden.',
	cancel: 'annuleren'
});

// CONCATENATED MODULE: ./dist/addons/searchfield/translations/fa.js
/* harmony default export */ var searchfield_translations_fa = ({
	Search: 'جستجو',
	'No results found.': 'نتیجه‌ای یافت نشد.',
	cancel: 'انصراف'
});

// CONCATENATED MODULE: ./dist/addons/searchfield/translations/de.js
/* harmony default export */ var searchfield_translations_de = ({
	Search: 'Suche',
	'No results found.': 'Keine Ergebnisse gefunden.',
	cancel: 'beenden'
});

// CONCATENATED MODULE: ./dist/addons/searchfield/translations/ru.js
/* harmony default export */ var searchfield_translations_ru = ({
	Search: 'Найти',
	'No results found.': 'Ничего не найдено.',
	cancel: 'отменить'
});

// CONCATENATED MODULE: ./dist/addons/searchfield/translations/translate.js





/* harmony default export */ var searchfield_translations_translate = (function () {
	i18n_add(searchfield_translations_nl, 'nl');
	i18n_add(searchfield_translations_fa, 'fa');
	i18n_add(searchfield_translations_de, 'de');
	i18n_add(searchfield_translations_ru, 'ru');
});

// CONCATENATED MODULE: ./dist/addons/searchfield/mmenu.searchfield.js








//  Add the translations.
searchfield_translations_translate();
//  Add the options and configs.
mmenu_oncanvas.options.searchfield = searchfield_options;
mmenu_oncanvas.configs.searchfield = searchfield_configs;
/* harmony default export */ var mmenu_searchfield = (function () {
	var _this = this;
	var options = searchfield_options_extendShorthandOptions(this.opts.searchfield);
	this.opts.searchfield = extend(options, mmenu_oncanvas.options.searchfield);
	var configs = this.conf.searchfield;
	if (!options.add) {
		return;
	}
	//	Blur searchfield
	this.bind('close:start', function () {
		find(_this.node.menu, '.mm-searchfield').forEach(function (input) {
			input.blur();
		});
	});
	this.bind('initPanel:after', function (panel) {
		var searchpanel = null;
		//	Add the search panel
		if (options.panel.add) {
			searchpanel = initSearchPanel.call(_this);
		}
		//	Add the searchfield
		var addTo = null;
		switch (options.addTo) {
			case 'panels':
				addTo = [panel];
				break;
			case 'panel':
				addTo = [searchpanel];
				break;
			default:
				if (typeof options.addTo == 'string') {
					addTo = find(_this.node.menu, options.addTo);
				}
				else if (type(options.addTo) == 'array') {
					addTo = options.addTo;
				}
				break;
		}
		addTo.forEach(function (form) {
			form = initSearchfield.call(_this, form);
			if (options.search && form) {
				initSearching.call(_this, form);
			}
		});
		//	Add the no-results message
		if (options.noResults) {
			initNoResultsMsg.call(_this, options.panel.add ? searchpanel : panel);
		}
	});
	//	Add click behavior.
	//	Prevents default behavior when clicking an anchor
	this.clck.push(function (anchor, args) {
		if (args.inMenu) {
			if (anchor.matches('.mm-searchfield__btn')) {
				//	Clicking the clear button
				if (anchor.matches('.mm-btn_close')) {
					var form = anchor.closest('.mm-searchfield'), input = find(form, 'input')[0];
					input.value = '';
					_this.search(input);
					return true;
				}
				//	Clicking the submit button
				if (anchor.matches('.mm-btn_next')) {
					var form = anchor.closest('form');
					if (form) {
						form.submit();
					}
					return true;
				}
			}
		}
	});
});
var initSearchPanel = function () {
	var options = this.opts.searchfield, configs = this.conf.searchfield;
	var searchpanel = dom_children(this.node.pnls, '.mm-panel_search')[0];
	//	Only once
	if (searchpanel) {
		return searchpanel;
	}
	searchpanel = create('div.mm-panel.mm-panel_search.mm-hidden');
	if (options.panel.id) {
		searchpanel.id = options.panel.id;
	}
	if (options.panel.title) {
		searchpanel.setAttribute('data-mm-title', options.panel.title);
		// searchpanel.dataset.mmTitle = options.panel.title; // IE10 has no dataset :(
	}
	var listview = create('ul');
	searchpanel.append(listview);
	this.node.pnls.append(searchpanel);
	this.initListview(listview);
	this._initNavbar(searchpanel);
	switch (options.panel.fx) {
		case false:
			break;
		case 'none':
			searchpanel.classList.add('mm-panel_noanimation');
			break;
		default:
			searchpanel.classList.add('mm-panel_fx-' + options.panel.fx);
			break;
	}
	//	Add splash content
	if (options.panel.splash) {
		var splash = create('div.mm-panel__content');
		splash.innerHTML = options.panel.splash;
		searchpanel.append(splash);
	}
	searchpanel.classList.add('mm-panel');
	searchpanel.classList.add('mm-hidden');
	this.node.pnls.append(searchpanel);
	return searchpanel;
};
var initSearchfield = function (wrapper) {
	var options = this.opts.searchfield, configs = this.conf.searchfield;
	//	No searchfield in vertical submenus
	if (wrapper.parentElement.matches('.mm-listitem_vertical')) {
		return null;
	}
	//	Only one searchfield per panel
	var form = find(wrapper, '.mm-searchfield')[0];
	if (form) {
		return form;
	}
	function addAttributes(element, attr) {
		if (attr) {
			for (var a in attr) {
				element.setAttribute(a, attr[a]);
			}
		}
	}
	var form = create((configs.form ? 'form' : 'div') + '.mm-searchfield'), field = create('div.mm-searchfield__input'), input = create('input');
	input.type = 'text';
	input.autocomplete = 'off';
	input.placeholder = this.i18n(options.placeholder);
	field.append(input);
	form.append(field);
	wrapper.prepend(form);
	//	Add attributes to the input
	addAttributes(input, configs.input);
	//	Add the clear button
	if (configs.clear) {
		var anchor = create('a.mm-btn.mm-btn_close.mm-searchfield__btn');
		anchor.setAttribute('href', '#');
		field.append(anchor);
	}
	//	Add attributes and submit to the form
	addAttributes(form, configs.form);
	if (configs.form && configs.submit && !configs.clear) {
		var anchor = create('a.mm-btn.mm-btn_next.mm-searchfield__btn');
		anchor.setAttribute('href', '#');
		field.append(anchor);
	}
	if (options.cancel) {
		var anchor = create('a.mm-searchfield__cancel');
		anchor.setAttribute('href', '#');
		anchor.textContent = this.i18n('cancel');
		form.append(anchor);
	}
	return form;
};
var initSearching = function (form) {
	var _this = this;
	var options = this.opts.searchfield, configs = this.conf.searchfield;
	var data = {};
	//	In the searchpanel.
	if (form.closest('.mm-panel_search')) {
		data.panels = find(this.node.pnls, '.mm-panel');
		data.noresults = [form.closest('.mm-panel')];
		//	In a panel
	}
	else if (form.closest('.mm-panel')) {
		data.panels = [form.closest('.mm-panel')];
		data.noresults = data.panels;
		//	Not in a panel, global
	}
	else {
		data.panels = find(this.node.pnls, '.mm-panel');
		data.noresults = [this.node.menu];
	}
	//	Filter out search panel
	data.panels = data.panels.filter(function (panel) { return !panel.matches('.mm-panel_search'); });
	//	Filter out vertical submenus
	data.panels = data.panels.filter(function (panel) { return !panel.parentElement.matches('.mm-listitem_vertical'); });
	//  Find listitems and dividers.
	data.listitems = [];
	data.dividers = [];
	data.panels.forEach(function (panel) {
		var _a, _b;
		(_a = data.listitems).push.apply(_a, find(panel, '.mm-listitem'));
		(_b = data.dividers).push.apply(_b, find(panel, '.mm-divider'));
	});
	var searchpanel = dom_children(this.node.pnls, '.mm-panel_search')[0], input = find(form, 'input')[0], cancel = find(form, '.mm-searchfield__cancel')[0];
	input['mmSearchfield'] = data;
	//	Open the splash panel when focussing the input.
	if (options.panel.add && options.panel.splash) {
		off(input, 'focus.splash');
		on(input, 'focus.splash', function (evnt) {
			_this.openPanel(searchpanel);
		});
	}
	if (options.cancel) {
		//	Show the cancel button when focussing the input.
		off(input, 'focus.cancel');
		on(input, 'focus.cancel', function (evnt) {
			cancel.classList.add('mm-searchfield__cancel-active');
		});
		//	Close the splash panel when clicking the cancel button.
		off(cancel, 'click.splash');
		on(cancel, 'click.splash', function (evnt) {
			evnt.preventDefault();
			cancel.classList.remove('mm-searchfield__cancel-active');
			if (searchpanel.matches('.mm-panel_opened')) {
				var parents = dom_children(_this.node.pnls, '.mm-panel_opened-parent');
				if (parents.length) {
					_this.openPanel(parents[parents.length - 1]);
				}
			}
		});
	}
	//	Focus the input in the searchpanel when opening the searchpanel.
	if (options.panel.add && options.addTo == 'panel') {
		this.bind('openPanel:finish', function (panel) {
			if (panel === searchpanel) {
				input.focus();
			}
		});
	}
	//	Search while typing.
	off(input, 'input.search');
	on(input, 'input.search', function (evnt) {
		switch (evnt.keyCode) {
			case 9: //	tab
			case 16: //	shift
			case 17: //	control
			case 18: //	alt
			case 37: //	left
			case 38: //	top
			case 39: //	right
			case 40: //	bottom
				break;
			default:
				_this.search(input);
				break;
		}
	});
	//	Search initially.
	this.search(input);
};
var initNoResultsMsg = function (wrapper) {
	if (!wrapper) {
		return;
	}
	var options = this.opts.searchfield, configs = this.conf.searchfield;
	//	Not in a panel
	if (!wrapper.closest('.mm-panel')) {
		wrapper = dom_children(this.node.pnls, '.mm-panel')[0];
	}
	//	Only once
	if (dom_children(wrapper, '.mm-panel__noresultsmsg').length) {
		return;
	}
	//	Add no-results message
	var message = create('div.mm-panel__noresultsmsg.mm-hidden');
	message.innerHTML = this.i18n(options.noResults);
	wrapper.append(message);
};
mmenu_oncanvas.prototype.search = function (input, query) {
	var _this = this;
	var _a;
	var options = this.opts.searchfield, configs = this.conf.searchfield;
	query = query || '' + input.value;
	query = query.toLowerCase().trim();
	var data = input['mmSearchfield'];
	var form = input.closest('.mm-searchfield'), buttons = find(form, '.mm-btn'), searchpanel = dom_children(this.node.pnls, '.mm-panel_search')[0];
	/** The panels. */
	var panels = data.panels;
	/** The "no results" messages in a cloned array. */
	var noresults = data.noresults;
	/** The listitems in a cloned array. */
	var listitems = data.listitems;
	/** Tje dividers in a cloned array. */
	var dividers = data.dividers;
	//	Reset previous results
	listitems.forEach(function (listitem) {
		listitem.classList.remove('mm-listitem_nosubitems');
		listitem.classList.remove('mm-listitem_onlysubitems');
		listitem.classList.remove('mm-hidden');
	});
	if (searchpanel) {
		dom_children(searchpanel, '.mm-listview')[0].innerHTML = '';
	}
	panels.forEach(function (panel) {
		panel.scrollTop = 0;
	});
	//	Search
	if (query.length) {
		//	Initially hide all dividers.
		dividers.forEach(function (divider) {
			divider.classList.add('mm-hidden');
		});
		//	Hide listitems that do not match.
		listitems.forEach(function (listitem) {
			var text = dom_children(listitem, '.mm-listitem__text')[0];
			var add = false;
			//  The listitem should be shown if:
			//          1) The text matches the query and
			//          2a) The text is a open-button and
			//          2b) the option showSubPanels is set to true.
			//      or  3a) The text is not an anchor and
			//          3b) the option showTextItems is set to true.
			//      or  4)  The text is an anchor.
			//  1
			if (text &&
				dom_text(text)
					.toLowerCase()
					.indexOf(query) > -1) {
				//  2a
				if (text.matches('.mm-listitem__btn')) {
					//  2b
					if (options.showSubPanels) {
						add = true;
					}
				}
				//  3a
				else if (!text.matches('a')) {
					//  3b
					if (options.showTextItems) {
						add = true;
					}
				}
				// 4
				else {
					add = true;
				}
			}
			if (!add) {
				listitem.classList.add('mm-hidden');
			}
		});
		/** Whether or not the query yielded results. */
		var hasResults = listitems.filter(function (listitem) { return !listitem.matches('.mm-hidden'); }).length;
		//	Show all mached listitems in the search panel
		if (options.panel.add) {
			//	Clone all matched listitems into the search panel
			var allitems_1 = [];
			panels.forEach(function (panel) {
				var listitems = filterLI(find(panel, '.mm-listitem'));
				listitems = listitems.filter(function (listitem) { return !listitem.matches('.mm-hidden'); });
				if (listitems.length) {
					//  Add a divider to indicate in what panel the listitems were.
					if (options.panel.dividers) {
						var divider = create('li.mm-divider');
						var title = find(panel, '.mm-navbar__title')[0];
						if (title) {
							divider.innerHTML = title.innerHTML;
							allitems_1.push(divider);
						}
					}
					listitems.forEach(function (listitem) {
						allitems_1.push(listitem.cloneNode(true));
					});
				}
			});
			//	Remove toggles and checks.
			allitems_1.forEach(function (listitem) {
				listitem
					.querySelectorAll('.mm-toggle, .mm-check')
					.forEach(function (element) {
					element.remove();
				});
			});
			//	Add to the search panel.
			(_a = dom_children(searchpanel, '.mm-listview')[0]).append.apply(_a, allitems_1);
			//	Open the search panel.
			this.openPanel(searchpanel);
		}
		else {
			//	Also show listitems in sub-panels for matched listitems
			if (options.showSubPanels) {
				panels.forEach(function (panel) {
					var listitems = find(panel, '.mm-listitem');
					filterLI(listitems).forEach(function (listitem) {
						var child = listitem['mmChild'];
						if (child) {
							find(child, '.mm-listitem').forEach(function (listitem) {
								listitem.classList.remove('mm-hidden');
							});
						}
					});
				});
			}
			//	Update parent for sub-panel
			//  .reverse() mutates the original array, therefor we "clone" it first using [...panels].
			panels.slice().reverse().forEach(function (panel, p) {
				var parent = panel['mmParent'];
				if (parent) {
					//	The current panel has mached listitems
					var listitems_1 = find(panel, '.mm-listitem');
					if (filterLI(listitems_1).length) {
						//	Show parent
						if (parent.matches('.mm-hidden')) {
							parent.classList.remove('mm-hidden');
						}
						parent.classList.add('mm-listitem_onlysubitems');
					}
					else if (!input.closest('.mm-panel')) {
						if (panel.matches('.mm-panel_opened') ||
							panel.matches('.mm-panel_opened-parent')) {
							//	Compensate the timeout for the opening animation
							setTimeout(function () {
								_this.openPanel(parent.closest('.mm-panel'));
							}, (p + 1) * (_this.conf.openingInterval * 1.5));
						}
						parent.classList.add('mm-listitem_nosubitems');
					}
				}
			});
			//	Show parent panels of vertical submenus
			panels.forEach(function (panel) {
				var listitems = find(panel, '.mm-listitem');
				filterLI(listitems).forEach(function (listitem) {
					dom_parents(listitem, '.mm-listitem_vertical').forEach(function (parent) {
						if (parent.matches('.mm-hidden')) {
							parent.classList.remove('mm-hidden');
							parent.classList.add('mm-listitem_onlysubitems');
						}
					});
				});
			});
			//	Show first preceeding divider of parent
			panels.forEach(function (panel) {
				var listitems = find(panel, '.mm-listitem');
				filterLI(listitems).forEach(function (listitem) {
					var divider = prevAll(listitem, '.mm-divider')[0];
					if (divider) {
						divider.classList.remove('mm-hidden');
					}
				});
			});
		}
		//	Show submit / clear button
		buttons.forEach(function (button) { return button.classList.remove('mm-hidden'); });
		//	Show/hide no results message
		noresults.forEach(function (wrapper) {
			find(wrapper, '.mm-panel__noresultsmsg').forEach(function (message) {
				return message.classList[hasResults ? 'add' : 'remove']('mm-hidden');
			});
		});
		if (options.panel.add) {
			//	Hide splash
			if (options.panel.splash) {
				find(searchpanel, '.mm-panel__content').forEach(function (splash) {
					return splash.classList.add('mm-hidden');
				});
			}
			//	Re-show original listitems when in search panel
			listitems.forEach(function (listitem) {
				return listitem.classList.remove('mm-hidden');
			});
			dividers.forEach(function (divider) { return divider.classList.remove('mm-hidden'); });
		}
		//	Don't search
	}
	else {
		//	Show all items
		listitems.forEach(function (listitem) { return listitem.classList.remove('mm-hidden'); });
		dividers.forEach(function (divider) { return divider.classList.remove('mm-hidden'); });
		//	Hide submit / clear button
		buttons.forEach(function (button) { return button.classList.add('mm-hidden'); });
		//	Hide no results message
		noresults.forEach(function (wrapper) {
			find(wrapper, '.mm-panel__noresultsmsg').forEach(function (message) {
				return message.classList.add('mm-hidden');
			});
		});
		if (options.panel.add) {
			//	Show splash
			if (options.panel.splash) {
				find(searchpanel, '.mm-panel__content').forEach(function (splash) {
					return splash.classList.remove('mm-hidden');
				});
				//	Close panel
			}
			else if (!input.closest('.mm-panel_search')) {
				var opened = dom_children(this.node.pnls, '.mm-panel_opened-parent');
				this.openPanel(opened.slice(-1)[0]);
			}
		}
	}
	//	Update for other addons
	this.trigger('updateListview');
};

// CONCATENATED MODULE: ./dist/addons/sectionindexer/_options.js
var sectionindexer_options_options = {
	add: false,
	addTo: 'panels'
};
/* harmony default export */ var sectionindexer_options = (sectionindexer_options_options);
/**
 * Extend shorthand options.
 *
 * @param  {object} options The options to extend.
 * @return {object}			The extended options.
 */
function sectionindexer_options_extendShorthandOptions(options) {
	if (typeof options == 'boolean') {
		options = {
			add: options
		};
	}
	if (typeof options != 'object') {
		options = {};
	}
	return options;
}
;

// CONCATENATED MODULE: ./dist/addons/sectionindexer/mmenu.sectionindexer.js






//  Add the options.
mmenu_oncanvas.options.sectionIndexer = sectionindexer_options;
/* harmony default export */ var mmenu_sectionindexer = (function () {
	var _this = this;
	var options = sectionindexer_options_extendShorthandOptions(this.opts.sectionIndexer);
	this.opts.sectionIndexer = extend(options, mmenu_oncanvas.options.sectionIndexer);
	if (!options.add) {
		return;
	}
	this.bind('initPanels:after', function () {
		//	Add the indexer, only if it does not allready excists
		if (!_this.node.indx) {
			var buttons_1 = '';
			'abcdefghijklmnopqrstuvwxyz'.split('').forEach(function (letter) {
				buttons_1 += '<a href="#">' + letter + '</a>';
			});
			var indexer = create('div.mm-sectionindexer');
			indexer.innerHTML = buttons_1;
			_this.node.pnls.prepend(indexer);
			_this.node.indx = indexer;
			//	Prevent default behavior when clicking an anchor
			_this.node.indx.addEventListener('click', function (evnt) {
				var anchor = evnt.target;
				if (anchor.matches('a')) {
					evnt.preventDefault();
				}
			});
			//	Scroll onMouseOver / onTouchStart
			var mouseOverEvent = function (evnt) {
				if (!evnt.target.matches('a')) {
					return;
				}
				var letter = evnt.target.textContent, panel = dom_children(_this.node.pnls, '.mm-panel_opened')[0];
				var newTop = -1, oldTop = panel.scrollTop;
				panel.scrollTop = 0;
				find(panel, '.mm-divider')
					.filter(function (divider) { return !divider.matches('.mm-hidden'); })
					.forEach(function (divider) {
					if (newTop < 0 &&
						letter ==
							divider.textContent
								.trim()
								.slice(0, 1)
								.toLowerCase()) {
						newTop = divider.offsetTop;
					}
				});
				panel.scrollTop = newTop > -1 ? newTop : oldTop;
			};
			if (touch) {
				_this.node.indx.addEventListener('touchstart', mouseOverEvent);
				_this.node.indx.addEventListener('touchmove', mouseOverEvent);
			}
			else {
				_this.node.indx.addEventListener('mouseover', mouseOverEvent);
			}
		}
		//	Show or hide the indexer
		_this.bind('openPanel:start', function (panel) {
			var active = find(panel, '.mm-divider').filter(function (divider) { return !divider.matches('.mm-hidden'); }).length;
			_this.node.indx.classList[active ? 'add' : 'remove']('mm-sectionindexer_active');
		});
	});
});

// CONCATENATED MODULE: ./dist/addons/setselected/_options.js
var setselected_options_options = {
	current: true,
	hover: false,
	parent: false
};
/* harmony default export */ var setselected_options = (setselected_options_options);
/**
 * Extend shorthand options.
 *
 * @param  {object} options The options to extend.
 * @return {object}			The extended options.
 */
function setselected_options_extendShorthandOptions(options) {
	if (typeof options == 'boolean') {
		options = {
			hover: options,
			parent: options
		};
	}
	if (typeof options != 'object') {
		options = {};
	}
	return options;
}
;

// CONCATENATED MODULE: ./dist/addons/setselected/mmenu.setselected.js





//	Add the options.
mmenu_oncanvas.options.setSelected = setselected_options;
/* harmony default export */ var mmenu_setselected = (function () {
	var _this = this;
	var options = setselected_options_extendShorthandOptions(this.opts.setSelected);
	this.opts.setSelected = extend(options, mmenu_oncanvas.options.setSelected);
	//	Find current by URL
	if (options.current == 'detect') {
		var findCurrent_1 = function (url) {
			url = url.split('?')[0].split('#')[0];
			var anchor = _this.node.menu.querySelector('a[href="' + url + '"], a[href="' + url + '/"]');
			if (anchor) {
				_this.setSelected(anchor.parentElement);
			}
			else {
				var arr = url.split('/').slice(0, -1);
				if (arr.length) {
					findCurrent_1(arr.join('/'));
				}
			}
		};
		this.bind('initMenu:after', function () {
			findCurrent_1.call(_this, window.location.href);
		});
		//	Remove current selected item
	}
	else if (!options.current) {
		this.bind('initListview:after', function (listview) {
			dom_children(listview, '.mm-listitem_selected').forEach(function (listitem) {
				listitem.classList.remove('mm-listitem_selected');
			});
		});
	}
	//	Add :hover effect on items
	if (options.hover) {
		this.bind('initMenu:after', function () {
			_this.node.menu.classList.add('mm-menu_selected-hover');
		});
	}
	//	Set parent item selected for submenus
	if (options.parent) {
		this.bind('openPanel:finish', function (panel) {
			//	Remove all
			find(_this.node.pnls, '.mm-listitem_selected-parent').forEach(function (listitem) {
				listitem.classList.remove('mm-listitem_selected-parent');
			});
			//	Move up the DOM tree
			var parent = panel['mmParent'];
			while (parent) {
				if (!parent.matches('.mm-listitem_vertical')) {
					parent.classList.add('mm-listitem_selected-parent');
				}
				parent = parent.closest('.mm-panel');
				parent = parent['mmParent'];
			}
		});
		this.bind('initMenu:after', function () {
			_this.node.menu.classList.add('mm-menu_selected-parent');
		});
	}
});

// CONCATENATED MODULE: ./dist/addons/sidebar/_options.js
var sidebar_options_options = {
	collapsed: {
		use: false,
		blockMenu: true,
		hideDivider: false,
		hideNavbar: true
	},
	expanded: {
		use: false,
		initial: 'open'
	}
};
/* harmony default export */ var sidebar_options = (sidebar_options_options);
/**
 * Extend shorthand options.
 *
 * @param  {object} options The options to extend.
 * @return {object}			The extended options.
 */
function sidebar_options_extendShorthandOptions(options) {
	if (typeof options == 'string' ||
		(typeof options == 'boolean' && options) ||
		typeof options == 'number') {
		options = {
			expanded: options
		};
	}
	if (typeof options != 'object') {
		options = {};
	}
	//	Extend collapsed shorthand options.
	if (typeof options.collapsed == 'boolean' && options.collapsed) {
		options.collapsed = {
			use: true
		};
	}
	if (typeof options.collapsed == 'string' ||
		typeof options.collapsed == 'number') {
		options.collapsed = {
			use: options.collapsed
		};
	}
	if (typeof options.collapsed != 'object') {
		options.collapsed = {};
	}
	//	Extend expanded shorthand options.
	if (typeof options.expanded == 'boolean' && options.expanded) {
		options.expanded = {
			use: true
		};
	}
	if (typeof options.expanded == 'string' ||
		typeof options.expanded == 'number') {
		options.expanded = {
			use: options.expanded
		};
	}
	if (typeof options.expanded != 'object') {
		options.expanded = {};
	}
	return options;
}

// CONCATENATED MODULE: ./dist/addons/sidebar/mmenu.sidebar.js






//  Add the options.
mmenu_oncanvas.options.sidebar = sidebar_options;
/* harmony default export */ var mmenu_sidebar = (function () {
	var _this = this;
	if (!this.opts.offCanvas) {
		return;
	}
	var options = sidebar_options_extendShorthandOptions(this.opts.sidebar);
	this.opts.sidebar = extend(options, mmenu_oncanvas.options.sidebar);
	//	Collapsed
	if (options.collapsed.use) {
		//	Make the menu collapsable.
		this.bind('initMenu:after', function () {
			_this.node.menu.classList.add('mm-menu_sidebar-collapsed');
			if (options.collapsed.blockMenu &&
				_this.opts.offCanvas &&
				!dom_children(_this.node.menu, '.mm-menu__blocker')[0]) {
				var anchor = create('a.mm-menu__blocker');
				anchor.setAttribute('href', '#' + _this.node.menu.id);
				_this.node.menu.prepend(anchor);
			}
			if (options.collapsed.hideNavbar) {
				_this.node.menu.classList.add('mm-menu_hidenavbar');
			}
			if (options.collapsed.hideDivider) {
				_this.node.menu.classList.add('mm-menu_hidedivider');
			}
		});
		//	En-/disable the collapsed sidebar.
		var enable = function () {
			_this.node.wrpr.classList.add('mm-wrapper_sidebar-collapsed');
		};
		var disable = function () {
			_this.node.wrpr.classList.remove('mm-wrapper_sidebar-collapsed');
		};
		if (typeof options.collapsed.use == 'boolean') {
			this.bind('initMenu:after', enable);
		}
		else {
			matchmedia_add(options.collapsed.use, enable, disable);
		}
	}
	//	Expanded
	if (options.expanded.use) {
		//	Make the menu expandable
		this.bind('initMenu:after', function () {
			_this.node.menu.classList.add('mm-menu_sidebar-expanded');
		});
		//	En-/disable the expanded sidebar.
		var enable = function () {
			_this.node.wrpr.classList.add('mm-wrapper_sidebar-expanded');
			if (!_this.node.wrpr.matches('.mm-wrapper_sidebar-closed')) {
				_this.open();
			}
		};
		var disable = function () {
			_this.node.wrpr.classList.remove('mm-wrapper_sidebar-expanded');
			_this.close();
		};
		if (typeof options.expanded.use == 'boolean') {
			this.bind('initMenu:after', enable);
		}
		else {
			matchmedia_add(options.expanded.use, enable, disable);
		}
		//  Manually en-/disable the expanded sidebar (open / close the menu)
		this.bind('close:start', function () {
			if (_this.node.wrpr.matches('.mm-wrapper_sidebar-expanded')) {
				_this.node.wrpr.classList.add('mm-wrapper_sidebar-closed');
				if (options.expanded.initial == 'remember') {
					window.localStorage.setItem('mmenuExpandedState', 'closed');
				}
			}
		});
		this.bind('open:start', function () {
			if (_this.node.wrpr.matches('.mm-wrapper_sidebar-expanded')) {
				_this.node.wrpr.classList.remove('mm-wrapper_sidebar-closed');
				if (options.expanded.initial == 'remember') {
					window.localStorage.setItem('mmenuExpandedState', 'open');
				}
			}
		});
		//  Set the initial state
		var initialState = options.expanded.initial;
		if (options.expanded.initial == 'remember') {
			var state = window.localStorage.getItem('mmenuExpandedState');
			switch (state) {
				case 'open':
				case 'closed':
					initialState = state;
					break;
			}
		}
		if (initialState == 'closed') {
			this.bind('initMenu:after', function () {
				_this.node.wrpr.classList.add('mm-wrapper_sidebar-closed');
			});
		}
		//	Add click behavior.
		//	Prevents default behavior when clicking an anchor
		this.clck.push(function (anchor, args) {
			if (args.inMenu && args.inListview) {
				if (_this.node.wrpr.matches('.mm-wrapper_sidebar-expanded')) {
					return {
						close: options.expanded.initial == 'closed'
					};
				}
			}
		});
	}
});

// CONCATENATED MODULE: ./dist/addons/toggles/mmenu.toggles.js


//	Add the classnames.
mmenu_oncanvas.configs.classNames.toggles = {
	toggle: 'Toggle',
	check: 'Check'
};
/* harmony default export */ var mmenu_toggles = (function () {
	var _this = this;
	this.bind('initPanel:after', function (panel) {
		//	Refactor toggle classes
		find(panel, 'input').forEach(function (input) {
			reClass(input, _this.conf.classNames.toggles.toggle, 'mm-toggle');
			reClass(input, _this.conf.classNames.toggles.check, 'mm-check');
		});
	});
});

// CONCATENATED MODULE: ./dist/wrappers/angular/mmenu.angular.js
/* harmony default export */ var mmenu_angular = (function () {
	this.opts.onClick = {
		close: true,
		preventDefault: false,
		setSelected: true
	};
});
;

// CONCATENATED MODULE: ./dist/wrappers/bootstrap/mmenu.bootstrap.js

/* harmony default export */ var mmenu_bootstrap = (function () {
	var _this = this;
	//	Create the menu
	if (this.node.menu.matches('.navbar-collapse')) {
		//	No need for cloning the menu...
		if (this.conf.offCanvas) {
			this.conf.offCanvas.clone = false;
		}
		//	... We'll create a new menu
		var nav = create('nav'), panel = create('div');
		nav.append(panel);
		dom_children(this.node.menu).forEach(function (child) {
			switch (true) {
				case child.matches('.navbar-nav'):
					panel.append(cloneNav(child));
					break;
				case child.matches('.dropdown-menu'):
					panel.append(cloneDropdown(child));
					break;
				case child.matches('.form-inline'):
					_this.conf.searchfield.form = {
						action: child.getAttribute('action') || null,
						method: child.getAttribute('method') || null
					};
					_this.conf.searchfield.input = {
						name: child.querySelector('input').getAttribute('name') ||
							null
					};
					_this.conf.searchfield.clear = false;
					_this.conf.searchfield.submit = true;
					break;
				default:
					panel.append(child.cloneNode(true));
					break;
			}
		});
		//	Set the menu
		this.bind('initMenu:before', function () {
			document.body.prepend(nav);
			_this.node.menu = nav;
		});
		//	Hijack the toggler.
		var parent_1 = this.node.menu.parentElement;
		if (parent_1) {
			var toggler = parent_1.querySelector('.navbar-toggler');
			if (toggler) {
				toggler.removeAttribute('data-target');
				// delete toggler.dataset.target; // IE10 has no dataset :(
				toggler.removeAttribute('aria-controls');
				//	Remove all bound events.
				toggler.outerHTML = toggler.outerHTML;
				toggler = parent_1.querySelector('.navbar-toggler');
				//  Open the menu on-click.
				toggler.addEventListener('click', function (evnt) {
					evnt.preventDefault();
					evnt.stopImmediatePropagation();
					_this[_this.vars.opened ? 'close' : 'open']();
				});
			}
		}
	}
	function cloneLink(anchor) {
		var link = create(anchor.matches('a') ? 'a' : 'span');
		//	Copy attributes
		var attr = ['href', 'title', 'target'];
		for (var a = 0; a < attr.length; a++) {
			if (typeof anchor.getAttribute(attr[a]) != 'undefined') {
				link.setAttribute(attr[a], anchor.getAttribute(attr[a]));
			}
		}
		//	Copy contents
		link.innerHTML = anchor.innerHTML;
		//	Remove Screen reader text.
		find(link, '.sr-only').forEach(function (sro) {
			sro.remove();
		});
		return link;
	}
	function cloneDropdown(dropdown) {
		var list = create('ul');
		dom_children(dropdown).forEach(function (anchor) {
			var item = create('li');
			if (anchor.matches('.dropdown-divider')) {
				item.classList.add('Divider');
			}
			else if (anchor.matches('.dropdown-item')) {
				item.append(cloneLink(anchor));
			}
			list.append(item);
		});
		return list;
	}
	function cloneNav(nav) {
		var list = create('ul');
		find(nav, '.nav-item').forEach(function (anchor) {
			var item = create('li');
			if (anchor.matches('.active')) {
				item.classList.add('Selected');
			}
			if (!anchor.matches('.nav-link')) {
				var dropdown = dom_children(anchor, '.dropdown-menu')[0];
				if (dropdown) {
					item.append(cloneDropdown(dropdown));
				}
				anchor = dom_children(anchor, '.nav-link')[0];
			}
			item.prepend(cloneLink(anchor));
			list.append(item);
		});
		return list;
	}
});

// CONCATENATED MODULE: ./dist/wrappers/olark/mmenu.olark.js
/* harmony default export */ var mmenu_olark = (function () {
	this.conf.offCanvas.page.noSelector.push('#olark');
});
;

// CONCATENATED MODULE: ./dist/wrappers/turbolinks/mmenu.turbolinks.js
/* harmony default export */ var mmenu_turbolinks = (function () {
	var classnames;
	document.addEventListener('turbolinks:before-visit', function (evnt) {
		classnames = document
			.querySelector('.mm-wrapper')
			.className.split(' ')
			.filter(function (name) { return /mm-/.test(name); });
	});
	document.addEventListener('turbolinks:load', function (evnt) {
		if (typeof classnames === 'undefined') {
			return;
		}
		document.querySelector('.mm-wrapper').className = classnames;
	});
});

// CONCATENATED MODULE: ./dist/wrappers/wordpress/mmenu.wordpress.js
/* harmony default export */ var mmenu_wordpress = (function () {
	this.conf.classNames.selected = 'current-menu-item';
	var wpadminbar = document.getElementById('wpadminbar');
	if (wpadminbar) {
		wpadminbar.style.position = 'fixed';
		wpadminbar.classList.add('mm-slideout');
	}
});

// CONCATENATED MODULE: ./src/mmenu.js
/*!
 * mmenu.js
 * mmenujs.com
 *
 * Copyright (c) Fred Heusschen
 * frebsite.nl
 *
 * License: CC-BY-NC-4.0
 * http://creativecommons.org/licenses/by-nc/4.0/
 */

//	Core


//	Core add-ons




//	Add-ons




















//	Wrappers






mmenu_oncanvas.addons = {
	//	Core add-ons
	offcanvas: mmenu_offcanvas,
	screenReader: mmenu_screenreader,
	scrollBugFix: mmenu_scrollbugfix,

	//	Add-ons
	autoHeight: mmenu_autoheight,
	backButton: mmenu_backbutton,
	columns: mmenu_columns,
	counters: mmenu_counters,
	dividers: mmenu_dividers,
	drag: mmenu_drag,
	dropdown: mmenu_dropdown,
	fixedElements: mmenu_fixedelements,
	iconbar: mmenu_iconbar,
	iconPanels: mmenu_iconpanels,
	keyboardNavigation: mmenu_keyboardnavigation,
	lazySubmenus: mmenu_lazysubmenus,
	navbars: Navbars,
	pageScroll: mmenu_pagescroll,
	searchfield: mmenu_searchfield,
	sectionIndexer: mmenu_sectionindexer,
	setSelected: mmenu_setselected,
	sidebar: mmenu_sidebar,
	toggles: mmenu_toggles
};

//	Wrappers
mmenu_oncanvas.wrappers = {
	angular: mmenu_angular,
	bootstrap: mmenu_bootstrap,
	olark: mmenu_olark,
	turbolinks: mmenu_turbolinks,
	wordpress: mmenu_wordpress
};

//  Export module
/* harmony default export */ var mmenu = __webpack_exports__["default"] = (mmenu_oncanvas);

//	Global namespace
if (window) {
	window.Mmenu = mmenu_oncanvas;
}

//	jQuery plugin
(function($) {
	if ($) {
		$.fn.mmenu = function(options, configs) {
			var $result = $();

			this.each(function(e, element) {
				//	Don't proceed if the element already is a mmenu.
				if (element.mmApi) {
					return;
				}

				var menu = new mmenu_oncanvas(element, options, configs),
					$menu = $(menu.node.menu);

				//	Store the API for backward compat.
				$menu.data('mmenu', menu.API);

				$result = $result.add($menu);
			});

			return $result;
		};
	}
})(window.jQuery || window.Zepto || null);


/***/ })
/******/ ]);



});
