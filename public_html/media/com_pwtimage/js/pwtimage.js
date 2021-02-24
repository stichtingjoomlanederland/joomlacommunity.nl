function _typeof2(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof2 = function _typeof2(obj) { return typeof obj; }; } else { _typeof2 = function _typeof2(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof2(obj); }

/*!
 * Cropper.js v1.5.9
 * https://fengyuanchen.github.io/cropperjs
 *
 * Copyright 2015-present Chen Fengyuan
 * Released under the MIT license
 *
 * Date: 2020-09-10T13:16:26.743Z
 */
(function (global, factory) {
  (typeof exports === "undefined" ? "undefined" : _typeof2(exports)) === 'object' && typeof module !== 'undefined' ? module.exports = factory() : typeof define === 'function' && define.amd ? define(factory) : (global = typeof globalThis !== 'undefined' ? globalThis : global || self, global.Cropper = factory());
})(this, function () {
  'use strict';

  function _typeof(obj) {
    "@babel/helpers - typeof";

    if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
      _typeof = function _typeof(obj) {
        return typeof obj;
      };
    } else {
      _typeof = function _typeof(obj) {
        return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
      };
    }

    return _typeof(obj);
  }

  function _classCallCheck(instance, Constructor) {
    if (!(instance instanceof Constructor)) {
      throw new TypeError("Cannot call a class as a function");
    }
  }

  function _defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
      var descriptor = props[i];
      descriptor.enumerable = descriptor.enumerable || false;
      descriptor.configurable = true;
      if ("value" in descriptor) descriptor.writable = true;
      Object.defineProperty(target, descriptor.key, descriptor);
    }
  }

  function _createClass(Constructor, protoProps, staticProps) {
    if (protoProps) _defineProperties(Constructor.prototype, protoProps);
    if (staticProps) _defineProperties(Constructor, staticProps);
    return Constructor;
  }

  function _defineProperty(obj, key, value) {
    if (key in obj) {
      Object.defineProperty(obj, key, {
        value: value,
        enumerable: true,
        configurable: true,
        writable: true
      });
    } else {
      obj[key] = value;
    }

    return obj;
  }

  function ownKeys(object, enumerableOnly) {
    var keys = Object.keys(object);

    if (Object.getOwnPropertySymbols) {
      var symbols = Object.getOwnPropertySymbols(object);
      if (enumerableOnly) symbols = symbols.filter(function (sym) {
        return Object.getOwnPropertyDescriptor(object, sym).enumerable;
      });
      keys.push.apply(keys, symbols);
    }

    return keys;
  }

  function _objectSpread2(target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i] != null ? arguments[i] : {};

      if (i % 2) {
        ownKeys(Object(source), true).forEach(function (key) {
          _defineProperty(target, key, source[key]);
        });
      } else if (Object.getOwnPropertyDescriptors) {
        Object.defineProperties(target, Object.getOwnPropertyDescriptors(source));
      } else {
        ownKeys(Object(source)).forEach(function (key) {
          Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key));
        });
      }
    }

    return target;
  }

  function _toConsumableArray(arr) {
    return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread();
  }

  function _arrayWithoutHoles(arr) {
    if (Array.isArray(arr)) return _arrayLikeToArray(arr);
  }

  function _iterableToArray(iter) {
    if (typeof Symbol !== "undefined" && Symbol.iterator in Object(iter)) return Array.from(iter);
  }

  function _unsupportedIterableToArray(o, minLen) {
    if (!o) return;
    if (typeof o === "string") return _arrayLikeToArray(o, minLen);
    var n = Object.prototype.toString.call(o).slice(8, -1);
    if (n === "Object" && o.constructor) n = o.constructor.name;
    if (n === "Map" || n === "Set") return Array.from(o);
    if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen);
  }

  function _arrayLikeToArray(arr, len) {
    if (len == null || len > arr.length) len = arr.length;

    for (var i = 0, arr2 = new Array(len); i < len; i++) {
      arr2[i] = arr[i];
    }

    return arr2;
  }

  function _nonIterableSpread() {
    throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
  }

  var IS_BROWSER = typeof window !== 'undefined' && typeof window.document !== 'undefined';
  var WINDOW = IS_BROWSER ? window : {};
  var IS_TOUCH_DEVICE = IS_BROWSER && WINDOW.document.documentElement ? 'ontouchstart' in WINDOW.document.documentElement : false;
  var HAS_POINTER_EVENT = IS_BROWSER ? 'PointerEvent' in WINDOW : false;
  var NAMESPACE = 'cropper'; // Actions

  var ACTION_ALL = 'all';
  var ACTION_CROP = 'crop';
  var ACTION_MOVE = 'move';
  var ACTION_ZOOM = 'zoom';
  var ACTION_EAST = 'e';
  var ACTION_WEST = 'w';
  var ACTION_SOUTH = 's';
  var ACTION_NORTH = 'n';
  var ACTION_NORTH_EAST = 'ne';
  var ACTION_NORTH_WEST = 'nw';
  var ACTION_SOUTH_EAST = 'se';
  var ACTION_SOUTH_WEST = 'sw'; // Classes

  var CLASS_CROP = "".concat(NAMESPACE, "-crop");
  var CLASS_DISABLED = "".concat(NAMESPACE, "-disabled");
  var CLASS_HIDDEN = "".concat(NAMESPACE, "-hidden");
  var CLASS_HIDE = "".concat(NAMESPACE, "-hide");
  var CLASS_INVISIBLE = "".concat(NAMESPACE, "-invisible");
  var CLASS_MODAL = "".concat(NAMESPACE, "-modal");
  var CLASS_MOVE = "".concat(NAMESPACE, "-move"); // Data keys

  var DATA_ACTION = "".concat(NAMESPACE, "Action");
  var DATA_PREVIEW = "".concat(NAMESPACE, "Preview"); // Drag modes

  var DRAG_MODE_CROP = 'crop';
  var DRAG_MODE_MOVE = 'move';
  var DRAG_MODE_NONE = 'none'; // Events

  var EVENT_CROP = 'crop';
  var EVENT_CROP_END = 'cropend';
  var EVENT_CROP_MOVE = 'cropmove';
  var EVENT_CROP_START = 'cropstart';
  var EVENT_DBLCLICK = 'dblclick';
  var EVENT_TOUCH_START = IS_TOUCH_DEVICE ? 'touchstart' : 'mousedown';
  var EVENT_TOUCH_MOVE = IS_TOUCH_DEVICE ? 'touchmove' : 'mousemove';
  var EVENT_TOUCH_END = IS_TOUCH_DEVICE ? 'touchend touchcancel' : 'mouseup';
  var EVENT_POINTER_DOWN = HAS_POINTER_EVENT ? 'pointerdown' : EVENT_TOUCH_START;
  var EVENT_POINTER_MOVE = HAS_POINTER_EVENT ? 'pointermove' : EVENT_TOUCH_MOVE;
  var EVENT_POINTER_UP = HAS_POINTER_EVENT ? 'pointerup pointercancel' : EVENT_TOUCH_END;
  var EVENT_READY = 'ready';
  var EVENT_RESIZE = 'resize';
  var EVENT_WHEEL = 'wheel';
  var EVENT_ZOOM = 'zoom'; // Mime types

  var MIME_TYPE_JPEG = 'image/jpeg'; // RegExps

  var REGEXP_ACTIONS = /^e|w|s|n|se|sw|ne|nw|all|crop|move|zoom$/;
  var REGEXP_DATA_URL = /^data:/;
  var REGEXP_DATA_URL_JPEG = /^data:image\/jpeg;base64,/;
  var REGEXP_TAG_NAME = /^img|canvas$/i; // Misc
  // Inspired by the default width and height of a canvas element.

  var MIN_CONTAINER_WIDTH = 200;
  var MIN_CONTAINER_HEIGHT = 100;
  var DEFAULTS = {
    // Define the view mode of the cropper
    viewMode: 0,
    // 0, 1, 2, 3
    // Define the dragging mode of the cropper
    dragMode: DRAG_MODE_CROP,
    // 'crop', 'move' or 'none'
    // Define the initial aspect ratio of the crop box
    initialAspectRatio: NaN,
    // Define the aspect ratio of the crop box
    aspectRatio: NaN,
    // An object with the previous cropping result data
    data: null,
    // A selector for adding extra containers to preview
    preview: '',
    // Re-render the cropper when resize the window
    responsive: true,
    // Restore the cropped area after resize the window
    restore: true,
    // Check if the current image is a cross-origin image
    checkCrossOrigin: true,
    // Check the current image's Exif Orientation information
    checkOrientation: true,
    // Show the black modal
    modal: true,
    // Show the dashed lines for guiding
    guides: true,
    // Show the center indicator for guiding
    center: true,
    // Show the white modal to highlight the crop box
    highlight: true,
    // Show the grid background
    background: true,
    // Enable to crop the image automatically when initialize
    autoCrop: true,
    // Define the percentage of automatic cropping area when initializes
    autoCropArea: 0.8,
    // Enable to move the image
    movable: true,
    // Enable to rotate the image
    rotatable: true,
    // Enable to scale the image
    scalable: true,
    // Enable to zoom the image
    zoomable: true,
    // Enable to zoom the image by dragging touch
    zoomOnTouch: true,
    // Enable to zoom the image by wheeling mouse
    zoomOnWheel: true,
    // Define zoom ratio when zoom the image by wheeling mouse
    wheelZoomRatio: 0.1,
    // Enable to move the crop box
    cropBoxMovable: true,
    // Enable to resize the crop box
    cropBoxResizable: true,
    // Toggle drag mode between "crop" and "move" when click twice on the cropper
    toggleDragModeOnDblclick: true,
    // Size limitation
    minCanvasWidth: 0,
    minCanvasHeight: 0,
    minCropBoxWidth: 0,
    minCropBoxHeight: 0,
    minContainerWidth: MIN_CONTAINER_WIDTH,
    minContainerHeight: MIN_CONTAINER_HEIGHT,
    // Shortcuts of events
    ready: null,
    cropstart: null,
    cropmove: null,
    cropend: null,
    crop: null,
    zoom: null
  };
  var TEMPLATE = '<div class="cropper-container" touch-action="none">' + '<div class="cropper-wrap-box">' + '<div class="cropper-canvas"></div>' + '</div>' + '<div class="cropper-drag-box"></div>' + '<div class="cropper-crop-box">' + '<span class="cropper-view-box"></span>' + '<span class="cropper-dashed dashed-h"></span>' + '<span class="cropper-dashed dashed-v"></span>' + '<span class="cropper-center"></span>' + '<span class="cropper-face"></span>' + '<span class="cropper-line line-e" data-cropper-action="e"></span>' + '<span class="cropper-line line-n" data-cropper-action="n"></span>' + '<span class="cropper-line line-w" data-cropper-action="w"></span>' + '<span class="cropper-line line-s" data-cropper-action="s"></span>' + '<span class="cropper-point point-e" data-cropper-action="e"></span>' + '<span class="cropper-point point-n" data-cropper-action="n"></span>' + '<span class="cropper-point point-w" data-cropper-action="w"></span>' + '<span class="cropper-point point-s" data-cropper-action="s"></span>' + '<span class="cropper-point point-ne" data-cropper-action="ne"></span>' + '<span class="cropper-point point-nw" data-cropper-action="nw"></span>' + '<span class="cropper-point point-sw" data-cropper-action="sw"></span>' + '<span class="cropper-point point-se" data-cropper-action="se"></span>' + '</div>' + '</div>';
  /**
   * Check if the given value is not a number.
   */

  var isNaN = Number.isNaN || WINDOW.isNaN;
  /**
   * Check if the given value is a number.
   * @param {*} value - The value to check.
   * @returns {boolean} Returns `true` if the given value is a number, else `false`.
   */

  function isNumber(value) {
    return typeof value === 'number' && !isNaN(value);
  }
  /**
   * Check if the given value is a positive number.
   * @param {*} value - The value to check.
   * @returns {boolean} Returns `true` if the given value is a positive number, else `false`.
   */


  var isPositiveNumber = function isPositiveNumber(value) {
    return value > 0 && value < Infinity;
  };
  /**
   * Check if the given value is undefined.
   * @param {*} value - The value to check.
   * @returns {boolean} Returns `true` if the given value is undefined, else `false`.
   */


  function isUndefined(value) {
    return typeof value === 'undefined';
  }
  /**
   * Check if the given value is an object.
   * @param {*} value - The value to check.
   * @returns {boolean} Returns `true` if the given value is an object, else `false`.
   */


  function isObject(value) {
    return _typeof(value) === 'object' && value !== null;
  }

  var hasOwnProperty = Object.prototype.hasOwnProperty;
  /**
   * Check if the given value is a plain object.
   * @param {*} value - The value to check.
   * @returns {boolean} Returns `true` if the given value is a plain object, else `false`.
   */

  function isPlainObject(value) {
    if (!isObject(value)) {
      return false;
    }

    try {
      var _constructor = value.constructor;
      var prototype = _constructor.prototype;
      return _constructor && prototype && hasOwnProperty.call(prototype, 'isPrototypeOf');
    } catch (error) {
      return false;
    }
  }
  /**
   * Check if the given value is a function.
   * @param {*} value - The value to check.
   * @returns {boolean} Returns `true` if the given value is a function, else `false`.
   */


  function isFunction(value) {
    return typeof value === 'function';
  }

  var slice = Array.prototype.slice;
  /**
   * Convert array-like or iterable object to an array.
   * @param {*} value - The value to convert.
   * @returns {Array} Returns a new array.
   */

  function toArray(value) {
    return Array.from ? Array.from(value) : slice.call(value);
  }
  /**
   * Iterate the given data.
   * @param {*} data - The data to iterate.
   * @param {Function} callback - The process function for each element.
   * @returns {*} The original data.
   */


  function forEach(data, callback) {
    if (data && isFunction(callback)) {
      if (Array.isArray(data) || isNumber(data.length)
      /* array-like */
      ) {
          toArray(data).forEach(function (value, key) {
            callback.call(data, value, key, data);
          });
        } else if (isObject(data)) {
        Object.keys(data).forEach(function (key) {
          callback.call(data, data[key], key, data);
        });
      }
    }

    return data;
  }
  /**
   * Extend the given object.
   * @param {*} target - The target object to extend.
   * @param {*} args - The rest objects for merging to the target object.
   * @returns {Object} The extended object.
   */


  var assign = Object.assign || function assign(target) {
    for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
      args[_key - 1] = arguments[_key];
    }

    if (isObject(target) && args.length > 0) {
      args.forEach(function (arg) {
        if (isObject(arg)) {
          Object.keys(arg).forEach(function (key) {
            target[key] = arg[key];
          });
        }
      });
    }

    return target;
  };

  var REGEXP_DECIMALS = /\.\d*(?:0|9){12}\d*$/;
  /**
   * Normalize decimal number.
   * Check out {@link https://0.30000000000000004.com/}
   * @param {number} value - The value to normalize.
   * @param {number} [times=100000000000] - The times for normalizing.
   * @returns {number} Returns the normalized number.
   */

  function normalizeDecimalNumber(value) {
    var times = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 100000000000;
    return REGEXP_DECIMALS.test(value) ? Math.round(value * times) / times : value;
  }

  var REGEXP_SUFFIX = /^width|height|left|top|marginLeft|marginTop$/;
  /**
   * Apply styles to the given element.
   * @param {Element} element - The target element.
   * @param {Object} styles - The styles for applying.
   */

  function setStyle(element, styles) {
    var style = element.style;
    forEach(styles, function (value, property) {
      if (REGEXP_SUFFIX.test(property) && isNumber(value)) {
        value = "".concat(value, "px");
      }

      style[property] = value;
    });
  }
  /**
   * Check if the given element has a special class.
   * @param {Element} element - The element to check.
   * @param {string} value - The class to search.
   * @returns {boolean} Returns `true` if the special class was found.
   */


  function hasClass(element, value) {
    return element.classList ? element.classList.contains(value) : element.className.indexOf(value) > -1;
  }
  /**
   * Add classes to the given element.
   * @param {Element} element - The target element.
   * @param {string} value - The classes to be added.
   */


  function addClass(element, value) {
    if (!value) {
      return;
    }

    if (isNumber(element.length)) {
      forEach(element, function (elem) {
        addClass(elem, value);
      });
      return;
    }

    if (element.classList) {
      element.classList.add(value);
      return;
    }

    var className = element.className.trim();

    if (!className) {
      element.className = value;
    } else if (className.indexOf(value) < 0) {
      element.className = "".concat(className, " ").concat(value);
    }
  }
  /**
   * Remove classes from the given element.
   * @param {Element} element - The target element.
   * @param {string} value - The classes to be removed.
   */


  function removeClass(element, value) {
    if (!value) {
      return;
    }

    if (isNumber(element.length)) {
      forEach(element, function (elem) {
        removeClass(elem, value);
      });
      return;
    }

    if (element.classList) {
      element.classList.remove(value);
      return;
    }

    if (element.className.indexOf(value) >= 0) {
      element.className = element.className.replace(value, '');
    }
  }
  /**
   * Add or remove classes from the given element.
   * @param {Element} element - The target element.
   * @param {string} value - The classes to be toggled.
   * @param {boolean} added - Add only.
   */


  function toggleClass(element, value, added) {
    if (!value) {
      return;
    }

    if (isNumber(element.length)) {
      forEach(element, function (elem) {
        toggleClass(elem, value, added);
      });
      return;
    } // IE10-11 doesn't support the second parameter of `classList.toggle`


    if (added) {
      addClass(element, value);
    } else {
      removeClass(element, value);
    }
  }

  var REGEXP_CAMEL_CASE = /([a-z\d])([A-Z])/g;
  /**
   * Transform the given string from camelCase to kebab-case
   * @param {string} value - The value to transform.
   * @returns {string} The transformed value.
   */

  function toParamCase(value) {
    return value.replace(REGEXP_CAMEL_CASE, '$1-$2').toLowerCase();
  }
  /**
   * Get data from the given element.
   * @param {Element} element - The target element.
   * @param {string} name - The data key to get.
   * @returns {string} The data value.
   */


  function getData(element, name) {
    if (isObject(element[name])) {
      return element[name];
    }

    if (element.dataset) {
      return element.dataset[name];
    }

    return element.getAttribute("data-".concat(toParamCase(name)));
  }
  /**
   * Set data to the given element.
   * @param {Element} element - The target element.
   * @param {string} name - The data key to set.
   * @param {string} data - The data value.
   */


  function setData(element, name, data) {
    if (isObject(data)) {
      element[name] = data;
    } else if (element.dataset) {
      element.dataset[name] = data;
    } else {
      element.setAttribute("data-".concat(toParamCase(name)), data);
    }
  }
  /**
   * Remove data from the given element.
   * @param {Element} element - The target element.
   * @param {string} name - The data key to remove.
   */


  function removeData(element, name) {
    if (isObject(element[name])) {
      try {
        delete element[name];
      } catch (error) {
        element[name] = undefined;
      }
    } else if (element.dataset) {
      // #128 Safari not allows to delete dataset property
      try {
        delete element.dataset[name];
      } catch (error) {
        element.dataset[name] = undefined;
      }
    } else {
      element.removeAttribute("data-".concat(toParamCase(name)));
    }
  }

  var REGEXP_SPACES = /\s\s*/;

  var onceSupported = function () {
    var supported = false;

    if (IS_BROWSER) {
      var once = false;

      var listener = function listener() {};

      var options = Object.defineProperty({}, 'once', {
        get: function get() {
          supported = true;
          return once;
        },

        /**
         * This setter can fix a `TypeError` in strict mode
         * {@link https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Errors/Getter_only}
         * @param {boolean} value - The value to set
         */
        set: function set(value) {
          once = value;
        }
      });
      WINDOW.addEventListener('test', listener, options);
      WINDOW.removeEventListener('test', listener, options);
    }

    return supported;
  }();
  /**
   * Remove event listener from the target element.
   * @param {Element} element - The event target.
   * @param {string} type - The event type(s).
   * @param {Function} listener - The event listener.
   * @param {Object} options - The event options.
   */


  function removeListener(element, type, listener) {
    var options = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : {};
    var handler = listener;
    type.trim().split(REGEXP_SPACES).forEach(function (event) {
      if (!onceSupported) {
        var listeners = element.listeners;

        if (listeners && listeners[event] && listeners[event][listener]) {
          handler = listeners[event][listener];
          delete listeners[event][listener];

          if (Object.keys(listeners[event]).length === 0) {
            delete listeners[event];
          }

          if (Object.keys(listeners).length === 0) {
            delete element.listeners;
          }
        }
      }

      element.removeEventListener(event, handler, options);
    });
  }
  /**
   * Add event listener to the target element.
   * @param {Element} element - The event target.
   * @param {string} type - The event type(s).
   * @param {Function} listener - The event listener.
   * @param {Object} options - The event options.
   */


  function addListener(element, type, listener) {
    var options = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : {};
    var _handler = listener;
    type.trim().split(REGEXP_SPACES).forEach(function (event) {
      if (options.once && !onceSupported) {
        var _element$listeners = element.listeners,
            listeners = _element$listeners === void 0 ? {} : _element$listeners;

        _handler = function handler() {
          delete listeners[event][listener];
          element.removeEventListener(event, _handler, options);

          for (var _len2 = arguments.length, args = new Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
            args[_key2] = arguments[_key2];
          }

          listener.apply(element, args);
        };

        if (!listeners[event]) {
          listeners[event] = {};
        }

        if (listeners[event][listener]) {
          element.removeEventListener(event, listeners[event][listener], options);
        }

        listeners[event][listener] = _handler;
        element.listeners = listeners;
      }

      element.addEventListener(event, _handler, options);
    });
  }
  /**
   * Dispatch event on the target element.
   * @param {Element} element - The event target.
   * @param {string} type - The event type(s).
   * @param {Object} data - The additional event data.
   * @returns {boolean} Indicate if the event is default prevented or not.
   */


  function dispatchEvent(element, type, data) {
    var event; // Event and CustomEvent on IE9-11 are global objects, not constructors

    if (isFunction(Event) && isFunction(CustomEvent)) {
      event = new CustomEvent(type, {
        detail: data,
        bubbles: true,
        cancelable: true
      });
    } else {
      event = document.createEvent('CustomEvent');
      event.initCustomEvent(type, true, true, data);
    }

    return element.dispatchEvent(event);
  }
  /**
   * Get the offset base on the document.
   * @param {Element} element - The target element.
   * @returns {Object} The offset data.
   */


  function getOffset(element) {
    var box = element.getBoundingClientRect();
    return {
      left: box.left + (window.pageXOffset - document.documentElement.clientLeft),
      top: box.top + (window.pageYOffset - document.documentElement.clientTop)
    };
  }

  var location = WINDOW.location;
  var REGEXP_ORIGINS = /^(\w+:)\/\/([^:/?#]*):?(\d*)/i;
  /**
   * Check if the given URL is a cross origin URL.
   * @param {string} url - The target URL.
   * @returns {boolean} Returns `true` if the given URL is a cross origin URL, else `false`.
   */

  function isCrossOriginURL(url) {
    var parts = url.match(REGEXP_ORIGINS);
    return parts !== null && (parts[1] !== location.protocol || parts[2] !== location.hostname || parts[3] !== location.port);
  }
  /**
   * Add timestamp to the given URL.
   * @param {string} url - The target URL.
   * @returns {string} The result URL.
   */


  function addTimestamp(url) {
    var timestamp = "timestamp=".concat(new Date().getTime());
    return url + (url.indexOf('?') === -1 ? '?' : '&') + timestamp;
  }
  /**
   * Get transforms base on the given object.
   * @param {Object} obj - The target object.
   * @returns {string} A string contains transform values.
   */


  function getTransforms(_ref) {
    var rotate = _ref.rotate,
        scaleX = _ref.scaleX,
        scaleY = _ref.scaleY,
        translateX = _ref.translateX,
        translateY = _ref.translateY;
    var values = [];

    if (isNumber(translateX) && translateX !== 0) {
      values.push("translateX(".concat(translateX, "px)"));
    }

    if (isNumber(translateY) && translateY !== 0) {
      values.push("translateY(".concat(translateY, "px)"));
    } // Rotate should come first before scale to match orientation transform


    if (isNumber(rotate) && rotate !== 0) {
      values.push("rotate(".concat(rotate, "deg)"));
    }

    if (isNumber(scaleX) && scaleX !== 1) {
      values.push("scaleX(".concat(scaleX, ")"));
    }

    if (isNumber(scaleY) && scaleY !== 1) {
      values.push("scaleY(".concat(scaleY, ")"));
    }

    var transform = values.length ? values.join(' ') : 'none';
    return {
      WebkitTransform: transform,
      msTransform: transform,
      transform: transform
    };
  }
  /**
   * Get the max ratio of a group of pointers.
   * @param {string} pointers - The target pointers.
   * @returns {number} The result ratio.
   */


  function getMaxZoomRatio(pointers) {
    var pointers2 = _objectSpread2({}, pointers);

    var maxRatio = 0;
    forEach(pointers, function (pointer, pointerId) {
      delete pointers2[pointerId];
      forEach(pointers2, function (pointer2) {
        var x1 = Math.abs(pointer.startX - pointer2.startX);
        var y1 = Math.abs(pointer.startY - pointer2.startY);
        var x2 = Math.abs(pointer.endX - pointer2.endX);
        var y2 = Math.abs(pointer.endY - pointer2.endY);
        var z1 = Math.sqrt(x1 * x1 + y1 * y1);
        var z2 = Math.sqrt(x2 * x2 + y2 * y2);
        var ratio = (z2 - z1) / z1;

        if (Math.abs(ratio) > Math.abs(maxRatio)) {
          maxRatio = ratio;
        }
      });
    });
    return maxRatio;
  }
  /**
   * Get a pointer from an event object.
   * @param {Object} event - The target event object.
   * @param {boolean} endOnly - Indicates if only returns the end point coordinate or not.
   * @returns {Object} The result pointer contains start and/or end point coordinates.
   */


  function getPointer(_ref2, endOnly) {
    var pageX = _ref2.pageX,
        pageY = _ref2.pageY;
    var end = {
      endX: pageX,
      endY: pageY
    };
    return endOnly ? end : _objectSpread2({
      startX: pageX,
      startY: pageY
    }, end);
  }
  /**
   * Get the center point coordinate of a group of pointers.
   * @param {Object} pointers - The target pointers.
   * @returns {Object} The center point coordinate.
   */


  function getPointersCenter(pointers) {
    var pageX = 0;
    var pageY = 0;
    var count = 0;
    forEach(pointers, function (_ref3) {
      var startX = _ref3.startX,
          startY = _ref3.startY;
      pageX += startX;
      pageY += startY;
      count += 1;
    });
    pageX /= count;
    pageY /= count;
    return {
      pageX: pageX,
      pageY: pageY
    };
  }
  /**
   * Get the max sizes in a rectangle under the given aspect ratio.
   * @param {Object} data - The original sizes.
   * @param {string} [type='contain'] - The adjust type.
   * @returns {Object} The result sizes.
   */


  function getAdjustedSizes(_ref4) // or 'cover'
  {
    var aspectRatio = _ref4.aspectRatio,
        height = _ref4.height,
        width = _ref4.width;
    var type = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'contain';
    var isValidWidth = isPositiveNumber(width);
    var isValidHeight = isPositiveNumber(height);

    if (isValidWidth && isValidHeight) {
      var adjustedWidth = height * aspectRatio;

      if (type === 'contain' && adjustedWidth > width || type === 'cover' && adjustedWidth < width) {
        height = width / aspectRatio;
      } else {
        width = height * aspectRatio;
      }
    } else if (isValidWidth) {
      height = width / aspectRatio;
    } else if (isValidHeight) {
      width = height * aspectRatio;
    }

    return {
      width: width,
      height: height
    };
  }
  /**
   * Get the new sizes of a rectangle after rotated.
   * @param {Object} data - The original sizes.
   * @returns {Object} The result sizes.
   */


  function getRotatedSizes(_ref5) {
    var width = _ref5.width,
        height = _ref5.height,
        degree = _ref5.degree;
    degree = Math.abs(degree) % 180;

    if (degree === 90) {
      return {
        width: height,
        height: width
      };
    }

    var arc = degree % 90 * Math.PI / 180;
    var sinArc = Math.sin(arc);
    var cosArc = Math.cos(arc);
    var newWidth = width * cosArc + height * sinArc;
    var newHeight = width * sinArc + height * cosArc;
    return degree > 90 ? {
      width: newHeight,
      height: newWidth
    } : {
      width: newWidth,
      height: newHeight
    };
  }
  /**
   * Get a canvas which drew the given image.
   * @param {HTMLImageElement} image - The image for drawing.
   * @param {Object} imageData - The image data.
   * @param {Object} canvasData - The canvas data.
   * @param {Object} options - The options.
   * @returns {HTMLCanvasElement} The result canvas.
   */


  function getSourceCanvas(image, _ref6, _ref7, _ref8) {
    var imageAspectRatio = _ref6.aspectRatio,
        imageNaturalWidth = _ref6.naturalWidth,
        imageNaturalHeight = _ref6.naturalHeight,
        _ref6$rotate = _ref6.rotate,
        rotate = _ref6$rotate === void 0 ? 0 : _ref6$rotate,
        _ref6$scaleX = _ref6.scaleX,
        scaleX = _ref6$scaleX === void 0 ? 1 : _ref6$scaleX,
        _ref6$scaleY = _ref6.scaleY,
        scaleY = _ref6$scaleY === void 0 ? 1 : _ref6$scaleY;
    var aspectRatio = _ref7.aspectRatio,
        naturalWidth = _ref7.naturalWidth,
        naturalHeight = _ref7.naturalHeight;
    var _ref8$fillColor = _ref8.fillColor,
        fillColor = _ref8$fillColor === void 0 ? 'transparent' : _ref8$fillColor,
        _ref8$imageSmoothingE = _ref8.imageSmoothingEnabled,
        imageSmoothingEnabled = _ref8$imageSmoothingE === void 0 ? true : _ref8$imageSmoothingE,
        _ref8$imageSmoothingQ = _ref8.imageSmoothingQuality,
        imageSmoothingQuality = _ref8$imageSmoothingQ === void 0 ? 'low' : _ref8$imageSmoothingQ,
        _ref8$maxWidth = _ref8.maxWidth,
        maxWidth = _ref8$maxWidth === void 0 ? Infinity : _ref8$maxWidth,
        _ref8$maxHeight = _ref8.maxHeight,
        maxHeight = _ref8$maxHeight === void 0 ? Infinity : _ref8$maxHeight,
        _ref8$minWidth = _ref8.minWidth,
        minWidth = _ref8$minWidth === void 0 ? 0 : _ref8$minWidth,
        _ref8$minHeight = _ref8.minHeight,
        minHeight = _ref8$minHeight === void 0 ? 0 : _ref8$minHeight;
    var canvas = document.createElement('canvas');
    var context = canvas.getContext('2d');
    var maxSizes = getAdjustedSizes({
      aspectRatio: aspectRatio,
      width: maxWidth,
      height: maxHeight
    });
    var minSizes = getAdjustedSizes({
      aspectRatio: aspectRatio,
      width: minWidth,
      height: minHeight
    }, 'cover');
    var width = Math.min(maxSizes.width, Math.max(minSizes.width, naturalWidth));
    var height = Math.min(maxSizes.height, Math.max(minSizes.height, naturalHeight)); // Note: should always use image's natural sizes for drawing as
    // imageData.naturalWidth === canvasData.naturalHeight when rotate % 180 === 90

    var destMaxSizes = getAdjustedSizes({
      aspectRatio: imageAspectRatio,
      width: maxWidth,
      height: maxHeight
    });
    var destMinSizes = getAdjustedSizes({
      aspectRatio: imageAspectRatio,
      width: minWidth,
      height: minHeight
    }, 'cover');
    var destWidth = Math.min(destMaxSizes.width, Math.max(destMinSizes.width, imageNaturalWidth));
    var destHeight = Math.min(destMaxSizes.height, Math.max(destMinSizes.height, imageNaturalHeight));
    var params = [-destWidth / 2, -destHeight / 2, destWidth, destHeight];
    canvas.width = normalizeDecimalNumber(width);
    canvas.height = normalizeDecimalNumber(height);
    context.fillStyle = fillColor;
    context.fillRect(0, 0, width, height);
    context.save();
    context.translate(width / 2, height / 2);
    context.rotate(rotate * Math.PI / 180);
    context.scale(scaleX, scaleY);
    context.imageSmoothingEnabled = imageSmoothingEnabled;
    context.imageSmoothingQuality = imageSmoothingQuality;
    context.drawImage.apply(context, [image].concat(_toConsumableArray(params.map(function (param) {
      return Math.floor(normalizeDecimalNumber(param));
    }))));
    context.restore();
    return canvas;
  }

  var fromCharCode = String.fromCharCode;
  /**
   * Get string from char code in data view.
   * @param {DataView} dataView - The data view for read.
   * @param {number} start - The start index.
   * @param {number} length - The read length.
   * @returns {string} The read result.
   */

  function getStringFromCharCode(dataView, start, length) {
    var str = '';
    length += start;

    for (var i = start; i < length; i += 1) {
      str += fromCharCode(dataView.getUint8(i));
    }

    return str;
  }

  var REGEXP_DATA_URL_HEAD = /^data:.*,/;
  /**
   * Transform Data URL to array buffer.
   * @param {string} dataURL - The Data URL to transform.
   * @returns {ArrayBuffer} The result array buffer.
   */

  function dataURLToArrayBuffer(dataURL) {
    var base64 = dataURL.replace(REGEXP_DATA_URL_HEAD, '');
    var binary = atob(base64);
    var arrayBuffer = new ArrayBuffer(binary.length);
    var uint8 = new Uint8Array(arrayBuffer);
    forEach(uint8, function (value, i) {
      uint8[i] = binary.charCodeAt(i);
    });
    return arrayBuffer;
  }
  /**
   * Transform array buffer to Data URL.
   * @param {ArrayBuffer} arrayBuffer - The array buffer to transform.
   * @param {string} mimeType - The mime type of the Data URL.
   * @returns {string} The result Data URL.
   */


  function arrayBufferToDataURL(arrayBuffer, mimeType) {
    var chunks = []; // Chunk Typed Array for better performance (#435)

    var chunkSize = 8192;
    var uint8 = new Uint8Array(arrayBuffer);

    while (uint8.length > 0) {
      // XXX: Babel's `toConsumableArray` helper will throw error in IE or Safari 9
      // eslint-disable-next-line prefer-spread
      chunks.push(fromCharCode.apply(null, toArray(uint8.subarray(0, chunkSize))));
      uint8 = uint8.subarray(chunkSize);
    }

    return "data:".concat(mimeType, ";base64,").concat(btoa(chunks.join('')));
  }
  /**
   * Get orientation value from given array buffer.
   * @param {ArrayBuffer} arrayBuffer - The array buffer to read.
   * @returns {number} The read orientation value.
   */


  function resetAndGetOrientation(arrayBuffer) {
    var dataView = new DataView(arrayBuffer);
    var orientation; // Ignores range error when the image does not have correct Exif information

    try {
      var littleEndian;
      var app1Start;
      var ifdStart; // Only handle JPEG image (start by 0xFFD8)

      if (dataView.getUint8(0) === 0xFF && dataView.getUint8(1) === 0xD8) {
        var length = dataView.byteLength;
        var offset = 2;

        while (offset + 1 < length) {
          if (dataView.getUint8(offset) === 0xFF && dataView.getUint8(offset + 1) === 0xE1) {
            app1Start = offset;
            break;
          }

          offset += 1;
        }
      }

      if (app1Start) {
        var exifIDCode = app1Start + 4;
        var tiffOffset = app1Start + 10;

        if (getStringFromCharCode(dataView, exifIDCode, 4) === 'Exif') {
          var endianness = dataView.getUint16(tiffOffset);
          littleEndian = endianness === 0x4949;

          if (littleEndian || endianness === 0x4D4D
          /* bigEndian */
          ) {
              if (dataView.getUint16(tiffOffset + 2, littleEndian) === 0x002A) {
                var firstIFDOffset = dataView.getUint32(tiffOffset + 4, littleEndian);

                if (firstIFDOffset >= 0x00000008) {
                  ifdStart = tiffOffset + firstIFDOffset;
                }
              }
            }
        }
      }

      if (ifdStart) {
        var _length = dataView.getUint16(ifdStart, littleEndian);

        var _offset;

        var i;

        for (i = 0; i < _length; i += 1) {
          _offset = ifdStart + i * 12 + 2;

          if (dataView.getUint16(_offset, littleEndian) === 0x0112
          /* Orientation */
          ) {
              // 8 is the offset of the current tag's value
              _offset += 8; // Get the original orientation value

              orientation = dataView.getUint16(_offset, littleEndian); // Override the orientation with its default value

              dataView.setUint16(_offset, 1, littleEndian);
              break;
            }
        }
      }
    } catch (error) {
      orientation = 1;
    }

    return orientation;
  }
  /**
   * Parse Exif Orientation value.
   * @param {number} orientation - The orientation to parse.
   * @returns {Object} The parsed result.
   */


  function parseOrientation(orientation) {
    var rotate = 0;
    var scaleX = 1;
    var scaleY = 1;

    switch (orientation) {
      // Flip horizontal
      case 2:
        scaleX = -1;
        break;
      // Rotate left 180°

      case 3:
        rotate = -180;
        break;
      // Flip vertical

      case 4:
        scaleY = -1;
        break;
      // Flip vertical and rotate right 90°

      case 5:
        rotate = 90;
        scaleY = -1;
        break;
      // Rotate right 90°

      case 6:
        rotate = 90;
        break;
      // Flip horizontal and rotate right 90°

      case 7:
        rotate = 90;
        scaleX = -1;
        break;
      // Rotate left 90°

      case 8:
        rotate = -90;
        break;
    }

    return {
      rotate: rotate,
      scaleX: scaleX,
      scaleY: scaleY
    };
  }

  var render = {
    render: function render() {
      this.initContainer();
      this.initCanvas();
      this.initCropBox();
      this.renderCanvas();

      if (this.cropped) {
        this.renderCropBox();
      }
    },
    initContainer: function initContainer() {
      var element = this.element,
          options = this.options,
          container = this.container,
          cropper = this.cropper;
      var minWidth = Number(options.minContainerWidth);
      var minHeight = Number(options.minContainerHeight);
      addClass(cropper, CLASS_HIDDEN);
      removeClass(element, CLASS_HIDDEN);
      var containerData = {
        width: Math.max(container.offsetWidth, minWidth >= 0 ? minWidth : MIN_CONTAINER_WIDTH),
        height: Math.max(container.offsetHeight, minHeight >= 0 ? minHeight : MIN_CONTAINER_HEIGHT)
      };
      this.containerData = containerData;
      setStyle(cropper, {
        width: containerData.width,
        height: containerData.height
      });
      addClass(element, CLASS_HIDDEN);
      removeClass(cropper, CLASS_HIDDEN);
    },
    // Canvas (image wrapper)
    initCanvas: function initCanvas() {
      var containerData = this.containerData,
          imageData = this.imageData;
      var viewMode = this.options.viewMode;
      var rotated = Math.abs(imageData.rotate) % 180 === 90;
      var naturalWidth = rotated ? imageData.naturalHeight : imageData.naturalWidth;
      var naturalHeight = rotated ? imageData.naturalWidth : imageData.naturalHeight;
      var aspectRatio = naturalWidth / naturalHeight;
      var canvasWidth = containerData.width;
      var canvasHeight = containerData.height;

      if (containerData.height * aspectRatio > containerData.width) {
        if (viewMode === 3) {
          canvasWidth = containerData.height * aspectRatio;
        } else {
          canvasHeight = containerData.width / aspectRatio;
        }
      } else if (viewMode === 3) {
        canvasHeight = containerData.width / aspectRatio;
      } else {
        canvasWidth = containerData.height * aspectRatio;
      }

      var canvasData = {
        aspectRatio: aspectRatio,
        naturalWidth: naturalWidth,
        naturalHeight: naturalHeight,
        width: canvasWidth,
        height: canvasHeight
      };
      this.canvasData = canvasData;
      this.limited = viewMode === 1 || viewMode === 2;
      this.limitCanvas(true, true);
      canvasData.width = Math.min(Math.max(canvasData.width, canvasData.minWidth), canvasData.maxWidth);
      canvasData.height = Math.min(Math.max(canvasData.height, canvasData.minHeight), canvasData.maxHeight);
      canvasData.left = (containerData.width - canvasData.width) / 2;
      canvasData.top = (containerData.height - canvasData.height) / 2;
      canvasData.oldLeft = canvasData.left;
      canvasData.oldTop = canvasData.top;
      this.initialCanvasData = assign({}, canvasData);
    },
    limitCanvas: function limitCanvas(sizeLimited, positionLimited) {
      var options = this.options,
          containerData = this.containerData,
          canvasData = this.canvasData,
          cropBoxData = this.cropBoxData;
      var viewMode = options.viewMode;
      var aspectRatio = canvasData.aspectRatio;
      var cropped = this.cropped && cropBoxData;

      if (sizeLimited) {
        var minCanvasWidth = Number(options.minCanvasWidth) || 0;
        var minCanvasHeight = Number(options.minCanvasHeight) || 0;

        if (viewMode > 1) {
          minCanvasWidth = Math.max(minCanvasWidth, containerData.width);
          minCanvasHeight = Math.max(minCanvasHeight, containerData.height);

          if (viewMode === 3) {
            if (minCanvasHeight * aspectRatio > minCanvasWidth) {
              minCanvasWidth = minCanvasHeight * aspectRatio;
            } else {
              minCanvasHeight = minCanvasWidth / aspectRatio;
            }
          }
        } else if (viewMode > 0) {
          if (minCanvasWidth) {
            minCanvasWidth = Math.max(minCanvasWidth, cropped ? cropBoxData.width : 0);
          } else if (minCanvasHeight) {
            minCanvasHeight = Math.max(minCanvasHeight, cropped ? cropBoxData.height : 0);
          } else if (cropped) {
            minCanvasWidth = cropBoxData.width;
            minCanvasHeight = cropBoxData.height;

            if (minCanvasHeight * aspectRatio > minCanvasWidth) {
              minCanvasWidth = minCanvasHeight * aspectRatio;
            } else {
              minCanvasHeight = minCanvasWidth / aspectRatio;
            }
          }
        }

        var _getAdjustedSizes = getAdjustedSizes({
          aspectRatio: aspectRatio,
          width: minCanvasWidth,
          height: minCanvasHeight
        });

        minCanvasWidth = _getAdjustedSizes.width;
        minCanvasHeight = _getAdjustedSizes.height;
        canvasData.minWidth = minCanvasWidth;
        canvasData.minHeight = minCanvasHeight;
        canvasData.maxWidth = Infinity;
        canvasData.maxHeight = Infinity;
      }

      if (positionLimited) {
        if (viewMode > (cropped ? 0 : 1)) {
          var newCanvasLeft = containerData.width - canvasData.width;
          var newCanvasTop = containerData.height - canvasData.height;
          canvasData.minLeft = Math.min(0, newCanvasLeft);
          canvasData.minTop = Math.min(0, newCanvasTop);
          canvasData.maxLeft = Math.max(0, newCanvasLeft);
          canvasData.maxTop = Math.max(0, newCanvasTop);

          if (cropped && this.limited) {
            canvasData.minLeft = Math.min(cropBoxData.left, cropBoxData.left + (cropBoxData.width - canvasData.width));
            canvasData.minTop = Math.min(cropBoxData.top, cropBoxData.top + (cropBoxData.height - canvasData.height));
            canvasData.maxLeft = cropBoxData.left;
            canvasData.maxTop = cropBoxData.top;

            if (viewMode === 2) {
              if (canvasData.width >= containerData.width) {
                canvasData.minLeft = Math.min(0, newCanvasLeft);
                canvasData.maxLeft = Math.max(0, newCanvasLeft);
              }

              if (canvasData.height >= containerData.height) {
                canvasData.minTop = Math.min(0, newCanvasTop);
                canvasData.maxTop = Math.max(0, newCanvasTop);
              }
            }
          }
        } else {
          canvasData.minLeft = -canvasData.width;
          canvasData.minTop = -canvasData.height;
          canvasData.maxLeft = containerData.width;
          canvasData.maxTop = containerData.height;
        }
      }
    },
    renderCanvas: function renderCanvas(changed, transformed) {
      var canvasData = this.canvasData,
          imageData = this.imageData;

      if (transformed) {
        var _getRotatedSizes = getRotatedSizes({
          width: imageData.naturalWidth * Math.abs(imageData.scaleX || 1),
          height: imageData.naturalHeight * Math.abs(imageData.scaleY || 1),
          degree: imageData.rotate || 0
        }),
            naturalWidth = _getRotatedSizes.width,
            naturalHeight = _getRotatedSizes.height;

        var width = canvasData.width * (naturalWidth / canvasData.naturalWidth);
        var height = canvasData.height * (naturalHeight / canvasData.naturalHeight);
        canvasData.left -= (width - canvasData.width) / 2;
        canvasData.top -= (height - canvasData.height) / 2;
        canvasData.width = width;
        canvasData.height = height;
        canvasData.aspectRatio = naturalWidth / naturalHeight;
        canvasData.naturalWidth = naturalWidth;
        canvasData.naturalHeight = naturalHeight;
        this.limitCanvas(true, false);
      }

      if (canvasData.width > canvasData.maxWidth || canvasData.width < canvasData.minWidth) {
        canvasData.left = canvasData.oldLeft;
      }

      if (canvasData.height > canvasData.maxHeight || canvasData.height < canvasData.minHeight) {
        canvasData.top = canvasData.oldTop;
      }

      canvasData.width = Math.min(Math.max(canvasData.width, canvasData.minWidth), canvasData.maxWidth);
      canvasData.height = Math.min(Math.max(canvasData.height, canvasData.minHeight), canvasData.maxHeight);
      this.limitCanvas(false, true);
      canvasData.left = Math.min(Math.max(canvasData.left, canvasData.minLeft), canvasData.maxLeft);
      canvasData.top = Math.min(Math.max(canvasData.top, canvasData.minTop), canvasData.maxTop);
      canvasData.oldLeft = canvasData.left;
      canvasData.oldTop = canvasData.top;
      setStyle(this.canvas, assign({
        width: canvasData.width,
        height: canvasData.height
      }, getTransforms({
        translateX: canvasData.left,
        translateY: canvasData.top
      })));
      this.renderImage(changed);

      if (this.cropped && this.limited) {
        this.limitCropBox(true, true);
      }
    },
    renderImage: function renderImage(changed) {
      var canvasData = this.canvasData,
          imageData = this.imageData;
      var width = imageData.naturalWidth * (canvasData.width / canvasData.naturalWidth);
      var height = imageData.naturalHeight * (canvasData.height / canvasData.naturalHeight);
      assign(imageData, {
        width: width,
        height: height,
        left: (canvasData.width - width) / 2,
        top: (canvasData.height - height) / 2
      });
      setStyle(this.image, assign({
        width: imageData.width,
        height: imageData.height
      }, getTransforms(assign({
        translateX: imageData.left,
        translateY: imageData.top
      }, imageData))));

      if (changed) {
        this.output();
      }
    },
    initCropBox: function initCropBox() {
      var options = this.options,
          canvasData = this.canvasData;
      var aspectRatio = options.aspectRatio || options.initialAspectRatio;
      var autoCropArea = Number(options.autoCropArea) || 0.8;
      var cropBoxData = {
        width: canvasData.width,
        height: canvasData.height
      };

      if (aspectRatio) {
        if (canvasData.height * aspectRatio > canvasData.width) {
          cropBoxData.height = cropBoxData.width / aspectRatio;
        } else {
          cropBoxData.width = cropBoxData.height * aspectRatio;
        }
      }

      this.cropBoxData = cropBoxData;
      this.limitCropBox(true, true); // Initialize auto crop area

      cropBoxData.width = Math.min(Math.max(cropBoxData.width, cropBoxData.minWidth), cropBoxData.maxWidth);
      cropBoxData.height = Math.min(Math.max(cropBoxData.height, cropBoxData.minHeight), cropBoxData.maxHeight); // The width/height of auto crop area must large than "minWidth/Height"

      cropBoxData.width = Math.max(cropBoxData.minWidth, cropBoxData.width * autoCropArea);
      cropBoxData.height = Math.max(cropBoxData.minHeight, cropBoxData.height * autoCropArea);
      cropBoxData.left = canvasData.left + (canvasData.width - cropBoxData.width) / 2;
      cropBoxData.top = canvasData.top + (canvasData.height - cropBoxData.height) / 2;
      cropBoxData.oldLeft = cropBoxData.left;
      cropBoxData.oldTop = cropBoxData.top;
      this.initialCropBoxData = assign({}, cropBoxData);
    },
    limitCropBox: function limitCropBox(sizeLimited, positionLimited) {
      var options = this.options,
          containerData = this.containerData,
          canvasData = this.canvasData,
          cropBoxData = this.cropBoxData,
          limited = this.limited;
      var aspectRatio = options.aspectRatio;

      if (sizeLimited) {
        var minCropBoxWidth = Number(options.minCropBoxWidth) || 0;
        var minCropBoxHeight = Number(options.minCropBoxHeight) || 0;
        var maxCropBoxWidth = limited ? Math.min(containerData.width, canvasData.width, canvasData.width + canvasData.left, containerData.width - canvasData.left) : containerData.width;
        var maxCropBoxHeight = limited ? Math.min(containerData.height, canvasData.height, canvasData.height + canvasData.top, containerData.height - canvasData.top) : containerData.height; // The min/maxCropBoxWidth/Height must be less than container's width/height

        minCropBoxWidth = Math.min(minCropBoxWidth, containerData.width);
        minCropBoxHeight = Math.min(minCropBoxHeight, containerData.height);

        if (aspectRatio) {
          if (minCropBoxWidth && minCropBoxHeight) {
            if (minCropBoxHeight * aspectRatio > minCropBoxWidth) {
              minCropBoxHeight = minCropBoxWidth / aspectRatio;
            } else {
              minCropBoxWidth = minCropBoxHeight * aspectRatio;
            }
          } else if (minCropBoxWidth) {
            minCropBoxHeight = minCropBoxWidth / aspectRatio;
          } else if (minCropBoxHeight) {
            minCropBoxWidth = minCropBoxHeight * aspectRatio;
          }

          if (maxCropBoxHeight * aspectRatio > maxCropBoxWidth) {
            maxCropBoxHeight = maxCropBoxWidth / aspectRatio;
          } else {
            maxCropBoxWidth = maxCropBoxHeight * aspectRatio;
          }
        } // The minWidth/Height must be less than maxWidth/Height


        cropBoxData.minWidth = Math.min(minCropBoxWidth, maxCropBoxWidth);
        cropBoxData.minHeight = Math.min(minCropBoxHeight, maxCropBoxHeight);
        cropBoxData.maxWidth = maxCropBoxWidth;
        cropBoxData.maxHeight = maxCropBoxHeight;
      }

      if (positionLimited) {
        if (limited) {
          cropBoxData.minLeft = Math.max(0, canvasData.left);
          cropBoxData.minTop = Math.max(0, canvasData.top);
          cropBoxData.maxLeft = Math.min(containerData.width, canvasData.left + canvasData.width) - cropBoxData.width;
          cropBoxData.maxTop = Math.min(containerData.height, canvasData.top + canvasData.height) - cropBoxData.height;
        } else {
          cropBoxData.minLeft = 0;
          cropBoxData.minTop = 0;
          cropBoxData.maxLeft = containerData.width - cropBoxData.width;
          cropBoxData.maxTop = containerData.height - cropBoxData.height;
        }
      }
    },
    renderCropBox: function renderCropBox() {
      var options = this.options,
          containerData = this.containerData,
          cropBoxData = this.cropBoxData;

      if (cropBoxData.width > cropBoxData.maxWidth || cropBoxData.width < cropBoxData.minWidth) {
        cropBoxData.left = cropBoxData.oldLeft;
      }

      if (cropBoxData.height > cropBoxData.maxHeight || cropBoxData.height < cropBoxData.minHeight) {
        cropBoxData.top = cropBoxData.oldTop;
      }

      cropBoxData.width = Math.min(Math.max(cropBoxData.width, cropBoxData.minWidth), cropBoxData.maxWidth);
      cropBoxData.height = Math.min(Math.max(cropBoxData.height, cropBoxData.minHeight), cropBoxData.maxHeight);
      this.limitCropBox(false, true);
      cropBoxData.left = Math.min(Math.max(cropBoxData.left, cropBoxData.minLeft), cropBoxData.maxLeft);
      cropBoxData.top = Math.min(Math.max(cropBoxData.top, cropBoxData.minTop), cropBoxData.maxTop);
      cropBoxData.oldLeft = cropBoxData.left;
      cropBoxData.oldTop = cropBoxData.top;

      if (options.movable && options.cropBoxMovable) {
        // Turn to move the canvas when the crop box is equal to the container
        setData(this.face, DATA_ACTION, cropBoxData.width >= containerData.width && cropBoxData.height >= containerData.height ? ACTION_MOVE : ACTION_ALL);
      }

      setStyle(this.cropBox, assign({
        width: cropBoxData.width,
        height: cropBoxData.height
      }, getTransforms({
        translateX: cropBoxData.left,
        translateY: cropBoxData.top
      })));

      if (this.cropped && this.limited) {
        this.limitCanvas(true, true);
      }

      if (!this.disabled) {
        this.output();
      }
    },
    output: function output() {
      this.preview();
      dispatchEvent(this.element, EVENT_CROP, this.getData());
    }
  };
  var preview = {
    initPreview: function initPreview() {
      var element = this.element,
          crossOrigin = this.crossOrigin;
      var preview = this.options.preview;
      var url = crossOrigin ? this.crossOriginUrl : this.url;
      var alt = element.alt || 'The image to preview';
      var image = document.createElement('img');

      if (crossOrigin) {
        image.crossOrigin = crossOrigin;
      }

      image.src = url;
      image.alt = alt;
      this.viewBox.appendChild(image);
      this.viewBoxImage = image;

      if (!preview) {
        return;
      }

      var previews = preview;

      if (typeof preview === 'string') {
        previews = element.ownerDocument.querySelectorAll(preview);
      } else if (preview.querySelector) {
        previews = [preview];
      }

      this.previews = previews;
      forEach(previews, function (el) {
        var img = document.createElement('img'); // Save the original size for recover

        setData(el, DATA_PREVIEW, {
          width: el.offsetWidth,
          height: el.offsetHeight,
          html: el.innerHTML
        });

        if (crossOrigin) {
          img.crossOrigin = crossOrigin;
        }

        img.src = url;
        img.alt = alt;
        /**
         * Override img element styles
         * Add `display:block` to avoid margin top issue
         * Add `height:auto` to override `height` attribute on IE8
         * (Occur only when margin-top <= -height)
         */

        img.style.cssText = 'display:block;' + 'width:100%;' + 'height:auto;' + 'min-width:0!important;' + 'min-height:0!important;' + 'max-width:none!important;' + 'max-height:none!important;' + 'image-orientation:0deg!important;"';
        el.innerHTML = '';
        el.appendChild(img);
      });
    },
    resetPreview: function resetPreview() {
      forEach(this.previews, function (element) {
        var data = getData(element, DATA_PREVIEW);
        setStyle(element, {
          width: data.width,
          height: data.height
        });
        element.innerHTML = data.html;
        removeData(element, DATA_PREVIEW);
      });
    },
    preview: function preview() {
      var imageData = this.imageData,
          canvasData = this.canvasData,
          cropBoxData = this.cropBoxData;
      var cropBoxWidth = cropBoxData.width,
          cropBoxHeight = cropBoxData.height;
      var width = imageData.width,
          height = imageData.height;
      var left = cropBoxData.left - canvasData.left - imageData.left;
      var top = cropBoxData.top - canvasData.top - imageData.top;

      if (!this.cropped || this.disabled) {
        return;
      }

      setStyle(this.viewBoxImage, assign({
        width: width,
        height: height
      }, getTransforms(assign({
        translateX: -left,
        translateY: -top
      }, imageData))));
      forEach(this.previews, function (element) {
        var data = getData(element, DATA_PREVIEW);
        var originalWidth = data.width;
        var originalHeight = data.height;
        var newWidth = originalWidth;
        var newHeight = originalHeight;
        var ratio = 1;

        if (cropBoxWidth) {
          ratio = originalWidth / cropBoxWidth;
          newHeight = cropBoxHeight * ratio;
        }

        if (cropBoxHeight && newHeight > originalHeight) {
          ratio = originalHeight / cropBoxHeight;
          newWidth = cropBoxWidth * ratio;
          newHeight = originalHeight;
        }

        setStyle(element, {
          width: newWidth,
          height: newHeight
        });
        setStyle(element.getElementsByTagName('img')[0], assign({
          width: width * ratio,
          height: height * ratio
        }, getTransforms(assign({
          translateX: -left * ratio,
          translateY: -top * ratio
        }, imageData))));
      });
    }
  };
  var events = {
    bind: function bind() {
      var element = this.element,
          options = this.options,
          cropper = this.cropper;

      if (isFunction(options.cropstart)) {
        addListener(element, EVENT_CROP_START, options.cropstart);
      }

      if (isFunction(options.cropmove)) {
        addListener(element, EVENT_CROP_MOVE, options.cropmove);
      }

      if (isFunction(options.cropend)) {
        addListener(element, EVENT_CROP_END, options.cropend);
      }

      if (isFunction(options.crop)) {
        addListener(element, EVENT_CROP, options.crop);
      }

      if (isFunction(options.zoom)) {
        addListener(element, EVENT_ZOOM, options.zoom);
      }

      addListener(cropper, EVENT_POINTER_DOWN, this.onCropStart = this.cropStart.bind(this));

      if (options.zoomable && options.zoomOnWheel) {
        addListener(cropper, EVENT_WHEEL, this.onWheel = this.wheel.bind(this), {
          passive: false,
          capture: true
        });
      }

      if (options.toggleDragModeOnDblclick) {
        addListener(cropper, EVENT_DBLCLICK, this.onDblclick = this.dblclick.bind(this));
      }

      addListener(element.ownerDocument, EVENT_POINTER_MOVE, this.onCropMove = this.cropMove.bind(this));
      addListener(element.ownerDocument, EVENT_POINTER_UP, this.onCropEnd = this.cropEnd.bind(this));

      if (options.responsive) {
        addListener(window, EVENT_RESIZE, this.onResize = this.resize.bind(this));
      }
    },
    unbind: function unbind() {
      var element = this.element,
          options = this.options,
          cropper = this.cropper;

      if (isFunction(options.cropstart)) {
        removeListener(element, EVENT_CROP_START, options.cropstart);
      }

      if (isFunction(options.cropmove)) {
        removeListener(element, EVENT_CROP_MOVE, options.cropmove);
      }

      if (isFunction(options.cropend)) {
        removeListener(element, EVENT_CROP_END, options.cropend);
      }

      if (isFunction(options.crop)) {
        removeListener(element, EVENT_CROP, options.crop);
      }

      if (isFunction(options.zoom)) {
        removeListener(element, EVENT_ZOOM, options.zoom);
      }

      removeListener(cropper, EVENT_POINTER_DOWN, this.onCropStart);

      if (options.zoomable && options.zoomOnWheel) {
        removeListener(cropper, EVENT_WHEEL, this.onWheel, {
          passive: false,
          capture: true
        });
      }

      if (options.toggleDragModeOnDblclick) {
        removeListener(cropper, EVENT_DBLCLICK, this.onDblclick);
      }

      removeListener(element.ownerDocument, EVENT_POINTER_MOVE, this.onCropMove);
      removeListener(element.ownerDocument, EVENT_POINTER_UP, this.onCropEnd);

      if (options.responsive) {
        removeListener(window, EVENT_RESIZE, this.onResize);
      }
    }
  };
  var handlers = {
    resize: function resize() {
      if (this.disabled) {
        return;
      }

      var options = this.options,
          container = this.container,
          containerData = this.containerData;
      var ratio = container.offsetWidth / containerData.width; // Resize when width changed or height changed

      if (ratio !== 1 || container.offsetHeight !== containerData.height) {
        var canvasData;
        var cropBoxData;

        if (options.restore) {
          canvasData = this.getCanvasData();
          cropBoxData = this.getCropBoxData();
        }

        this.render();

        if (options.restore) {
          this.setCanvasData(forEach(canvasData, function (n, i) {
            canvasData[i] = n * ratio;
          }));
          this.setCropBoxData(forEach(cropBoxData, function (n, i) {
            cropBoxData[i] = n * ratio;
          }));
        }
      }
    },
    dblclick: function dblclick() {
      if (this.disabled || this.options.dragMode === DRAG_MODE_NONE) {
        return;
      }

      this.setDragMode(hasClass(this.dragBox, CLASS_CROP) ? DRAG_MODE_MOVE : DRAG_MODE_CROP);
    },
    wheel: function wheel(event) {
      var _this = this;

      var ratio = Number(this.options.wheelZoomRatio) || 0.1;
      var delta = 1;

      if (this.disabled) {
        return;
      }

      event.preventDefault(); // Limit wheel speed to prevent zoom too fast (#21)

      if (this.wheeling) {
        return;
      }

      this.wheeling = true;
      setTimeout(function () {
        _this.wheeling = false;
      }, 50);

      if (event.deltaY) {
        delta = event.deltaY > 0 ? 1 : -1;
      } else if (event.wheelDelta) {
        delta = -event.wheelDelta / 120;
      } else if (event.detail) {
        delta = event.detail > 0 ? 1 : -1;
      }

      this.zoom(-delta * ratio, event);
    },
    cropStart: function cropStart(event) {
      var buttons = event.buttons,
          button = event.button;

      if (this.disabled // Handle mouse event and pointer event and ignore touch event
      || (event.type === 'mousedown' || event.type === 'pointerdown' && event.pointerType === 'mouse') && ( // No primary button (Usually the left button)
      isNumber(buttons) && buttons !== 1 || isNumber(button) && button !== 0 // Open context menu
      || event.ctrlKey)) {
        return;
      }

      var options = this.options,
          pointers = this.pointers;
      var action;

      if (event.changedTouches) {
        // Handle touch event
        forEach(event.changedTouches, function (touch) {
          pointers[touch.identifier] = getPointer(touch);
        });
      } else {
        // Handle mouse event and pointer event
        pointers[event.pointerId || 0] = getPointer(event);
      }

      if (Object.keys(pointers).length > 1 && options.zoomable && options.zoomOnTouch) {
        action = ACTION_ZOOM;
      } else {
        action = getData(event.target, DATA_ACTION);
      }

      if (!REGEXP_ACTIONS.test(action)) {
        return;
      }

      if (dispatchEvent(this.element, EVENT_CROP_START, {
        originalEvent: event,
        action: action
      }) === false) {
        return;
      } // This line is required for preventing page zooming in iOS browsers


      event.preventDefault();
      this.action = action;
      this.cropping = false;

      if (action === ACTION_CROP) {
        this.cropping = true;
        addClass(this.dragBox, CLASS_MODAL);
      }
    },
    cropMove: function cropMove(event) {
      var action = this.action;

      if (this.disabled || !action) {
        return;
      }

      var pointers = this.pointers;
      event.preventDefault();

      if (dispatchEvent(this.element, EVENT_CROP_MOVE, {
        originalEvent: event,
        action: action
      }) === false) {
        return;
      }

      if (event.changedTouches) {
        forEach(event.changedTouches, function (touch) {
          // The first parameter should not be undefined (#432)
          assign(pointers[touch.identifier] || {}, getPointer(touch, true));
        });
      } else {
        assign(pointers[event.pointerId || 0] || {}, getPointer(event, true));
      }

      this.change(event);
    },
    cropEnd: function cropEnd(event) {
      if (this.disabled) {
        return;
      }

      var action = this.action,
          pointers = this.pointers;

      if (event.changedTouches) {
        forEach(event.changedTouches, function (touch) {
          delete pointers[touch.identifier];
        });
      } else {
        delete pointers[event.pointerId || 0];
      }

      if (!action) {
        return;
      }

      event.preventDefault();

      if (!Object.keys(pointers).length) {
        this.action = '';
      }

      if (this.cropping) {
        this.cropping = false;
        toggleClass(this.dragBox, CLASS_MODAL, this.cropped && this.options.modal);
      }

      dispatchEvent(this.element, EVENT_CROP_END, {
        originalEvent: event,
        action: action
      });
    }
  };
  var change = {
    change: function change(event) {
      var options = this.options,
          canvasData = this.canvasData,
          containerData = this.containerData,
          cropBoxData = this.cropBoxData,
          pointers = this.pointers;
      var action = this.action;
      var aspectRatio = options.aspectRatio;
      var left = cropBoxData.left,
          top = cropBoxData.top,
          width = cropBoxData.width,
          height = cropBoxData.height;
      var right = left + width;
      var bottom = top + height;
      var minLeft = 0;
      var minTop = 0;
      var maxWidth = containerData.width;
      var maxHeight = containerData.height;
      var renderable = true;
      var offset; // Locking aspect ratio in "free mode" by holding shift key

      if (!aspectRatio && event.shiftKey) {
        aspectRatio = width && height ? width / height : 1;
      }

      if (this.limited) {
        minLeft = cropBoxData.minLeft;
        minTop = cropBoxData.minTop;
        maxWidth = minLeft + Math.min(containerData.width, canvasData.width, canvasData.left + canvasData.width);
        maxHeight = minTop + Math.min(containerData.height, canvasData.height, canvasData.top + canvasData.height);
      }

      var pointer = pointers[Object.keys(pointers)[0]];
      var range = {
        x: pointer.endX - pointer.startX,
        y: pointer.endY - pointer.startY
      };

      var check = function check(side) {
        switch (side) {
          case ACTION_EAST:
            if (right + range.x > maxWidth) {
              range.x = maxWidth - right;
            }

            break;

          case ACTION_WEST:
            if (left + range.x < minLeft) {
              range.x = minLeft - left;
            }

            break;

          case ACTION_NORTH:
            if (top + range.y < minTop) {
              range.y = minTop - top;
            }

            break;

          case ACTION_SOUTH:
            if (bottom + range.y > maxHeight) {
              range.y = maxHeight - bottom;
            }

            break;
        }
      };

      switch (action) {
        // Move crop box
        case ACTION_ALL:
          left += range.x;
          top += range.y;
          break;
        // Resize crop box

        case ACTION_EAST:
          if (range.x >= 0 && (right >= maxWidth || aspectRatio && (top <= minTop || bottom >= maxHeight))) {
            renderable = false;
            break;
          }

          check(ACTION_EAST);
          width += range.x;

          if (width < 0) {
            action = ACTION_WEST;
            width = -width;
            left -= width;
          }

          if (aspectRatio) {
            height = width / aspectRatio;
            top += (cropBoxData.height - height) / 2;
          }

          break;

        case ACTION_NORTH:
          if (range.y <= 0 && (top <= minTop || aspectRatio && (left <= minLeft || right >= maxWidth))) {
            renderable = false;
            break;
          }

          check(ACTION_NORTH);
          height -= range.y;
          top += range.y;

          if (height < 0) {
            action = ACTION_SOUTH;
            height = -height;
            top -= height;
          }

          if (aspectRatio) {
            width = height * aspectRatio;
            left += (cropBoxData.width - width) / 2;
          }

          break;

        case ACTION_WEST:
          if (range.x <= 0 && (left <= minLeft || aspectRatio && (top <= minTop || bottom >= maxHeight))) {
            renderable = false;
            break;
          }

          check(ACTION_WEST);
          width -= range.x;
          left += range.x;

          if (width < 0) {
            action = ACTION_EAST;
            width = -width;
            left -= width;
          }

          if (aspectRatio) {
            height = width / aspectRatio;
            top += (cropBoxData.height - height) / 2;
          }

          break;

        case ACTION_SOUTH:
          if (range.y >= 0 && (bottom >= maxHeight || aspectRatio && (left <= minLeft || right >= maxWidth))) {
            renderable = false;
            break;
          }

          check(ACTION_SOUTH);
          height += range.y;

          if (height < 0) {
            action = ACTION_NORTH;
            height = -height;
            top -= height;
          }

          if (aspectRatio) {
            width = height * aspectRatio;
            left += (cropBoxData.width - width) / 2;
          }

          break;

        case ACTION_NORTH_EAST:
          if (aspectRatio) {
            if (range.y <= 0 && (top <= minTop || right >= maxWidth)) {
              renderable = false;
              break;
            }

            check(ACTION_NORTH);
            height -= range.y;
            top += range.y;
            width = height * aspectRatio;
          } else {
            check(ACTION_NORTH);
            check(ACTION_EAST);

            if (range.x >= 0) {
              if (right < maxWidth) {
                width += range.x;
              } else if (range.y <= 0 && top <= minTop) {
                renderable = false;
              }
            } else {
              width += range.x;
            }

            if (range.y <= 0) {
              if (top > minTop) {
                height -= range.y;
                top += range.y;
              }
            } else {
              height -= range.y;
              top += range.y;
            }
          }

          if (width < 0 && height < 0) {
            action = ACTION_SOUTH_WEST;
            height = -height;
            width = -width;
            top -= height;
            left -= width;
          } else if (width < 0) {
            action = ACTION_NORTH_WEST;
            width = -width;
            left -= width;
          } else if (height < 0) {
            action = ACTION_SOUTH_EAST;
            height = -height;
            top -= height;
          }

          break;

        case ACTION_NORTH_WEST:
          if (aspectRatio) {
            if (range.y <= 0 && (top <= minTop || left <= minLeft)) {
              renderable = false;
              break;
            }

            check(ACTION_NORTH);
            height -= range.y;
            top += range.y;
            width = height * aspectRatio;
            left += cropBoxData.width - width;
          } else {
            check(ACTION_NORTH);
            check(ACTION_WEST);

            if (range.x <= 0) {
              if (left > minLeft) {
                width -= range.x;
                left += range.x;
              } else if (range.y <= 0 && top <= minTop) {
                renderable = false;
              }
            } else {
              width -= range.x;
              left += range.x;
            }

            if (range.y <= 0) {
              if (top > minTop) {
                height -= range.y;
                top += range.y;
              }
            } else {
              height -= range.y;
              top += range.y;
            }
          }

          if (width < 0 && height < 0) {
            action = ACTION_SOUTH_EAST;
            height = -height;
            width = -width;
            top -= height;
            left -= width;
          } else if (width < 0) {
            action = ACTION_NORTH_EAST;
            width = -width;
            left -= width;
          } else if (height < 0) {
            action = ACTION_SOUTH_WEST;
            height = -height;
            top -= height;
          }

          break;

        case ACTION_SOUTH_WEST:
          if (aspectRatio) {
            if (range.x <= 0 && (left <= minLeft || bottom >= maxHeight)) {
              renderable = false;
              break;
            }

            check(ACTION_WEST);
            width -= range.x;
            left += range.x;
            height = width / aspectRatio;
          } else {
            check(ACTION_SOUTH);
            check(ACTION_WEST);

            if (range.x <= 0) {
              if (left > minLeft) {
                width -= range.x;
                left += range.x;
              } else if (range.y >= 0 && bottom >= maxHeight) {
                renderable = false;
              }
            } else {
              width -= range.x;
              left += range.x;
            }

            if (range.y >= 0) {
              if (bottom < maxHeight) {
                height += range.y;
              }
            } else {
              height += range.y;
            }
          }

          if (width < 0 && height < 0) {
            action = ACTION_NORTH_EAST;
            height = -height;
            width = -width;
            top -= height;
            left -= width;
          } else if (width < 0) {
            action = ACTION_SOUTH_EAST;
            width = -width;
            left -= width;
          } else if (height < 0) {
            action = ACTION_NORTH_WEST;
            height = -height;
            top -= height;
          }

          break;

        case ACTION_SOUTH_EAST:
          if (aspectRatio) {
            if (range.x >= 0 && (right >= maxWidth || bottom >= maxHeight)) {
              renderable = false;
              break;
            }

            check(ACTION_EAST);
            width += range.x;
            height = width / aspectRatio;
          } else {
            check(ACTION_SOUTH);
            check(ACTION_EAST);

            if (range.x >= 0) {
              if (right < maxWidth) {
                width += range.x;
              } else if (range.y >= 0 && bottom >= maxHeight) {
                renderable = false;
              }
            } else {
              width += range.x;
            }

            if (range.y >= 0) {
              if (bottom < maxHeight) {
                height += range.y;
              }
            } else {
              height += range.y;
            }
          }

          if (width < 0 && height < 0) {
            action = ACTION_NORTH_WEST;
            height = -height;
            width = -width;
            top -= height;
            left -= width;
          } else if (width < 0) {
            action = ACTION_SOUTH_WEST;
            width = -width;
            left -= width;
          } else if (height < 0) {
            action = ACTION_NORTH_EAST;
            height = -height;
            top -= height;
          }

          break;
        // Move canvas

        case ACTION_MOVE:
          this.move(range.x, range.y);
          renderable = false;
          break;
        // Zoom canvas

        case ACTION_ZOOM:
          this.zoom(getMaxZoomRatio(pointers), event);
          renderable = false;
          break;
        // Create crop box

        case ACTION_CROP:
          if (!range.x || !range.y) {
            renderable = false;
            break;
          }

          offset = getOffset(this.cropper);
          left = pointer.startX - offset.left;
          top = pointer.startY - offset.top;
          width = cropBoxData.minWidth;
          height = cropBoxData.minHeight;

          if (range.x > 0) {
            action = range.y > 0 ? ACTION_SOUTH_EAST : ACTION_NORTH_EAST;
          } else if (range.x < 0) {
            left -= width;
            action = range.y > 0 ? ACTION_SOUTH_WEST : ACTION_NORTH_WEST;
          }

          if (range.y < 0) {
            top -= height;
          } // Show the crop box if is hidden


          if (!this.cropped) {
            removeClass(this.cropBox, CLASS_HIDDEN);
            this.cropped = true;

            if (this.limited) {
              this.limitCropBox(true, true);
            }
          }

          break;
      }

      if (renderable) {
        cropBoxData.width = width;
        cropBoxData.height = height;
        cropBoxData.left = left;
        cropBoxData.top = top;
        this.action = action;
        this.renderCropBox();
      } // Override


      forEach(pointers, function (p) {
        p.startX = p.endX;
        p.startY = p.endY;
      });
    }
  };
  var methods = {
    // Show the crop box manually
    crop: function crop() {
      if (this.ready && !this.cropped && !this.disabled) {
        this.cropped = true;
        this.limitCropBox(true, true);

        if (this.options.modal) {
          addClass(this.dragBox, CLASS_MODAL);
        }

        removeClass(this.cropBox, CLASS_HIDDEN);
        this.setCropBoxData(this.initialCropBoxData);
      }

      return this;
    },
    // Reset the image and crop box to their initial states
    reset: function reset() {
      if (this.ready && !this.disabled) {
        this.imageData = assign({}, this.initialImageData);
        this.canvasData = assign({}, this.initialCanvasData);
        this.cropBoxData = assign({}, this.initialCropBoxData);
        this.renderCanvas();

        if (this.cropped) {
          this.renderCropBox();
        }
      }

      return this;
    },
    // Clear the crop box
    clear: function clear() {
      if (this.cropped && !this.disabled) {
        assign(this.cropBoxData, {
          left: 0,
          top: 0,
          width: 0,
          height: 0
        });
        this.cropped = false;
        this.renderCropBox();
        this.limitCanvas(true, true); // Render canvas after crop box rendered

        this.renderCanvas();
        removeClass(this.dragBox, CLASS_MODAL);
        addClass(this.cropBox, CLASS_HIDDEN);
      }

      return this;
    },

    /**
     * Replace the image's src and rebuild the cropper
     * @param {string} url - The new URL.
     * @param {boolean} [hasSameSize] - Indicate if the new image has the same size as the old one.
     * @returns {Cropper} this
     */
    replace: function replace(url) {
      var hasSameSize = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;

      if (!this.disabled && url) {
        if (this.isImg) {
          this.element.src = url;
        }

        if (hasSameSize) {
          this.url = url;
          this.image.src = url;

          if (this.ready) {
            this.viewBoxImage.src = url;
            forEach(this.previews, function (element) {
              element.getElementsByTagName('img')[0].src = url;
            });
          }
        } else {
          if (this.isImg) {
            this.replaced = true;
          }

          this.options.data = null;
          this.uncreate();
          this.load(url);
        }
      }

      return this;
    },
    // Enable (unfreeze) the cropper
    enable: function enable() {
      if (this.ready && this.disabled) {
        this.disabled = false;
        removeClass(this.cropper, CLASS_DISABLED);
      }

      return this;
    },
    // Disable (freeze) the cropper
    disable: function disable() {
      if (this.ready && !this.disabled) {
        this.disabled = true;
        addClass(this.cropper, CLASS_DISABLED);
      }

      return this;
    },

    /**
     * Destroy the cropper and remove the instance from the image
     * @returns {Cropper} this
     */
    destroy: function destroy() {
      var element = this.element;

      if (!element[NAMESPACE]) {
        return this;
      }

      element[NAMESPACE] = undefined;

      if (this.isImg && this.replaced) {
        element.src = this.originalUrl;
      }

      this.uncreate();
      return this;
    },

    /**
     * Move the canvas with relative offsets
     * @param {number} offsetX - The relative offset distance on the x-axis.
     * @param {number} [offsetY=offsetX] - The relative offset distance on the y-axis.
     * @returns {Cropper} this
     */
    move: function move(offsetX) {
      var offsetY = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : offsetX;
      var _this$canvasData = this.canvasData,
          left = _this$canvasData.left,
          top = _this$canvasData.top;
      return this.moveTo(isUndefined(offsetX) ? offsetX : left + Number(offsetX), isUndefined(offsetY) ? offsetY : top + Number(offsetY));
    },

    /**
     * Move the canvas to an absolute point
     * @param {number} x - The x-axis coordinate.
     * @param {number} [y=x] - The y-axis coordinate.
     * @returns {Cropper} this
     */
    moveTo: function moveTo(x) {
      var y = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : x;
      var canvasData = this.canvasData;
      var changed = false;
      x = Number(x);
      y = Number(y);

      if (this.ready && !this.disabled && this.options.movable) {
        if (isNumber(x)) {
          canvasData.left = x;
          changed = true;
        }

        if (isNumber(y)) {
          canvasData.top = y;
          changed = true;
        }

        if (changed) {
          this.renderCanvas(true);
        }
      }

      return this;
    },

    /**
     * Zoom the canvas with a relative ratio
     * @param {number} ratio - The target ratio.
     * @param {Event} _originalEvent - The original event if any.
     * @returns {Cropper} this
     */
    zoom: function zoom(ratio, _originalEvent) {
      var canvasData = this.canvasData;
      ratio = Number(ratio);

      if (ratio < 0) {
        ratio = 1 / (1 - ratio);
      } else {
        ratio = 1 + ratio;
      }

      return this.zoomTo(canvasData.width * ratio / canvasData.naturalWidth, null, _originalEvent);
    },

    /**
     * Zoom the canvas to an absolute ratio
     * @param {number} ratio - The target ratio.
     * @param {Object} pivot - The zoom pivot point coordinate.
     * @param {Event} _originalEvent - The original event if any.
     * @returns {Cropper} this
     */
    zoomTo: function zoomTo(ratio, pivot, _originalEvent) {
      var options = this.options,
          canvasData = this.canvasData;
      var width = canvasData.width,
          height = canvasData.height,
          naturalWidth = canvasData.naturalWidth,
          naturalHeight = canvasData.naturalHeight;
      ratio = Number(ratio);

      if (ratio >= 0 && this.ready && !this.disabled && options.zoomable) {
        var newWidth = naturalWidth * ratio;
        var newHeight = naturalHeight * ratio;

        if (dispatchEvent(this.element, EVENT_ZOOM, {
          ratio: ratio,
          oldRatio: width / naturalWidth,
          originalEvent: _originalEvent
        }) === false) {
          return this;
        }

        if (_originalEvent) {
          var pointers = this.pointers;
          var offset = getOffset(this.cropper);
          var center = pointers && Object.keys(pointers).length ? getPointersCenter(pointers) : {
            pageX: _originalEvent.pageX,
            pageY: _originalEvent.pageY
          }; // Zoom from the triggering point of the event

          canvasData.left -= (newWidth - width) * ((center.pageX - offset.left - canvasData.left) / width);
          canvasData.top -= (newHeight - height) * ((center.pageY - offset.top - canvasData.top) / height);
        } else if (isPlainObject(pivot) && isNumber(pivot.x) && isNumber(pivot.y)) {
          canvasData.left -= (newWidth - width) * ((pivot.x - canvasData.left) / width);
          canvasData.top -= (newHeight - height) * ((pivot.y - canvasData.top) / height);
        } else {
          // Zoom from the center of the canvas
          canvasData.left -= (newWidth - width) / 2;
          canvasData.top -= (newHeight - height) / 2;
        }

        canvasData.width = newWidth;
        canvasData.height = newHeight;
        this.renderCanvas(true);
      }

      return this;
    },

    /**
     * Rotate the canvas with a relative degree
     * @param {number} degree - The rotate degree.
     * @returns {Cropper} this
     */
    rotate: function rotate(degree) {
      return this.rotateTo((this.imageData.rotate || 0) + Number(degree));
    },

    /**
     * Rotate the canvas to an absolute degree
     * @param {number} degree - The rotate degree.
     * @returns {Cropper} this
     */
    rotateTo: function rotateTo(degree) {
      degree = Number(degree);

      if (isNumber(degree) && this.ready && !this.disabled && this.options.rotatable) {
        this.imageData.rotate = degree % 360;
        this.renderCanvas(true, true);
      }

      return this;
    },

    /**
     * Scale the image on the x-axis.
     * @param {number} scaleX - The scale ratio on the x-axis.
     * @returns {Cropper} this
     */
    scaleX: function scaleX(_scaleX) {
      var scaleY = this.imageData.scaleY;
      return this.scale(_scaleX, isNumber(scaleY) ? scaleY : 1);
    },

    /**
     * Scale the image on the y-axis.
     * @param {number} scaleY - The scale ratio on the y-axis.
     * @returns {Cropper} this
     */
    scaleY: function scaleY(_scaleY) {
      var scaleX = this.imageData.scaleX;
      return this.scale(isNumber(scaleX) ? scaleX : 1, _scaleY);
    },

    /**
     * Scale the image
     * @param {number} scaleX - The scale ratio on the x-axis.
     * @param {number} [scaleY=scaleX] - The scale ratio on the y-axis.
     * @returns {Cropper} this
     */
    scale: function scale(scaleX) {
      var scaleY = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : scaleX;
      var imageData = this.imageData;
      var transformed = false;
      scaleX = Number(scaleX);
      scaleY = Number(scaleY);

      if (this.ready && !this.disabled && this.options.scalable) {
        if (isNumber(scaleX)) {
          imageData.scaleX = scaleX;
          transformed = true;
        }

        if (isNumber(scaleY)) {
          imageData.scaleY = scaleY;
          transformed = true;
        }

        if (transformed) {
          this.renderCanvas(true, true);
        }
      }

      return this;
    },

    /**
     * Get the cropped area position and size data (base on the original image)
     * @param {boolean} [rounded=false] - Indicate if round the data values or not.
     * @returns {Object} The result cropped data.
     */
    getData: function getData() {
      var rounded = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
      var options = this.options,
          imageData = this.imageData,
          canvasData = this.canvasData,
          cropBoxData = this.cropBoxData;
      var data;

      if (this.ready && this.cropped) {
        data = {
          x: cropBoxData.left - canvasData.left,
          y: cropBoxData.top - canvasData.top,
          width: cropBoxData.width,
          height: cropBoxData.height
        };
        var ratio = imageData.width / imageData.naturalWidth;
        forEach(data, function (n, i) {
          data[i] = n / ratio;
        });

        if (rounded) {
          // In case rounding off leads to extra 1px in right or bottom border
          // we should round the top-left corner and the dimension (#343).
          var bottom = Math.round(data.y + data.height);
          var right = Math.round(data.x + data.width);
          data.x = Math.round(data.x);
          data.y = Math.round(data.y);
          data.width = right - data.x;
          data.height = bottom - data.y;
        }
      } else {
        data = {
          x: 0,
          y: 0,
          width: 0,
          height: 0
        };
      }

      if (options.rotatable) {
        data.rotate = imageData.rotate || 0;
      }

      if (options.scalable) {
        data.scaleX = imageData.scaleX || 1;
        data.scaleY = imageData.scaleY || 1;
      }

      return data;
    },

    /**
     * Set the cropped area position and size with new data
     * @param {Object} data - The new data.
     * @returns {Cropper} this
     */
    setData: function setData(data) {
      var options = this.options,
          imageData = this.imageData,
          canvasData = this.canvasData;
      var cropBoxData = {};

      if (this.ready && !this.disabled && isPlainObject(data)) {
        var transformed = false;

        if (options.rotatable) {
          if (isNumber(data.rotate) && data.rotate !== imageData.rotate) {
            imageData.rotate = data.rotate;
            transformed = true;
          }
        }

        if (options.scalable) {
          if (isNumber(data.scaleX) && data.scaleX !== imageData.scaleX) {
            imageData.scaleX = data.scaleX;
            transformed = true;
          }

          if (isNumber(data.scaleY) && data.scaleY !== imageData.scaleY) {
            imageData.scaleY = data.scaleY;
            transformed = true;
          }
        }

        if (transformed) {
          this.renderCanvas(true, true);
        }

        var ratio = imageData.width / imageData.naturalWidth;

        if (isNumber(data.x)) {
          cropBoxData.left = data.x * ratio + canvasData.left;
        }

        if (isNumber(data.y)) {
          cropBoxData.top = data.y * ratio + canvasData.top;
        }

        if (isNumber(data.width)) {
          cropBoxData.width = data.width * ratio;
        }

        if (isNumber(data.height)) {
          cropBoxData.height = data.height * ratio;
        }

        this.setCropBoxData(cropBoxData);
      }

      return this;
    },

    /**
     * Get the container size data.
     * @returns {Object} The result container data.
     */
    getContainerData: function getContainerData() {
      return this.ready ? assign({}, this.containerData) : {};
    },

    /**
     * Get the image position and size data.
     * @returns {Object} The result image data.
     */
    getImageData: function getImageData() {
      return this.sized ? assign({}, this.imageData) : {};
    },

    /**
     * Get the canvas position and size data.
     * @returns {Object} The result canvas data.
     */
    getCanvasData: function getCanvasData() {
      var canvasData = this.canvasData;
      var data = {};

      if (this.ready) {
        forEach(['left', 'top', 'width', 'height', 'naturalWidth', 'naturalHeight'], function (n) {
          data[n] = canvasData[n];
        });
      }

      return data;
    },

    /**
     * Set the canvas position and size with new data.
     * @param {Object} data - The new canvas data.
     * @returns {Cropper} this
     */
    setCanvasData: function setCanvasData(data) {
      var canvasData = this.canvasData;
      var aspectRatio = canvasData.aspectRatio;

      if (this.ready && !this.disabled && isPlainObject(data)) {
        if (isNumber(data.left)) {
          canvasData.left = data.left;
        }

        if (isNumber(data.top)) {
          canvasData.top = data.top;
        }

        if (isNumber(data.width)) {
          canvasData.width = data.width;
          canvasData.height = data.width / aspectRatio;
        } else if (isNumber(data.height)) {
          canvasData.height = data.height;
          canvasData.width = data.height * aspectRatio;
        }

        this.renderCanvas(true);
      }

      return this;
    },

    /**
     * Get the crop box position and size data.
     * @returns {Object} The result crop box data.
     */
    getCropBoxData: function getCropBoxData() {
      var cropBoxData = this.cropBoxData;
      var data;

      if (this.ready && this.cropped) {
        data = {
          left: cropBoxData.left,
          top: cropBoxData.top,
          width: cropBoxData.width,
          height: cropBoxData.height
        };
      }

      return data || {};
    },

    /**
     * Set the crop box position and size with new data.
     * @param {Object} data - The new crop box data.
     * @returns {Cropper} this
     */
    setCropBoxData: function setCropBoxData(data) {
      var cropBoxData = this.cropBoxData;
      var aspectRatio = this.options.aspectRatio;
      var widthChanged;
      var heightChanged;

      if (this.ready && this.cropped && !this.disabled && isPlainObject(data)) {
        if (isNumber(data.left)) {
          cropBoxData.left = data.left;
        }

        if (isNumber(data.top)) {
          cropBoxData.top = data.top;
        }

        if (isNumber(data.width) && data.width !== cropBoxData.width) {
          widthChanged = true;
          cropBoxData.width = data.width;
        }

        if (isNumber(data.height) && data.height !== cropBoxData.height) {
          heightChanged = true;
          cropBoxData.height = data.height;
        }

        if (aspectRatio) {
          if (widthChanged) {
            cropBoxData.height = cropBoxData.width / aspectRatio;
          } else if (heightChanged) {
            cropBoxData.width = cropBoxData.height * aspectRatio;
          }
        }

        this.renderCropBox();
      }

      return this;
    },

    /**
     * Get a canvas drawn the cropped image.
     * @param {Object} [options={}] - The config options.
     * @returns {HTMLCanvasElement} - The result canvas.
     */
    getCroppedCanvas: function getCroppedCanvas() {
      var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

      if (!this.ready || !window.HTMLCanvasElement) {
        return null;
      }

      var canvasData = this.canvasData;
      var source = getSourceCanvas(this.image, this.imageData, canvasData, options); // Returns the source canvas if it is not cropped.

      if (!this.cropped) {
        return source;
      }

      var _this$getData = this.getData(),
          initialX = _this$getData.x,
          initialY = _this$getData.y,
          initialWidth = _this$getData.width,
          initialHeight = _this$getData.height;

      var ratio = source.width / Math.floor(canvasData.naturalWidth);

      if (ratio !== 1) {
        initialX *= ratio;
        initialY *= ratio;
        initialWidth *= ratio;
        initialHeight *= ratio;
      }

      var aspectRatio = initialWidth / initialHeight;
      var maxSizes = getAdjustedSizes({
        aspectRatio: aspectRatio,
        width: options.maxWidth || Infinity,
        height: options.maxHeight || Infinity
      });
      var minSizes = getAdjustedSizes({
        aspectRatio: aspectRatio,
        width: options.minWidth || 0,
        height: options.minHeight || 0
      }, 'cover');

      var _getAdjustedSizes = getAdjustedSizes({
        aspectRatio: aspectRatio,
        width: options.width || (ratio !== 1 ? source.width : initialWidth),
        height: options.height || (ratio !== 1 ? source.height : initialHeight)
      }),
          width = _getAdjustedSizes.width,
          height = _getAdjustedSizes.height;

      width = Math.min(maxSizes.width, Math.max(minSizes.width, width));
      height = Math.min(maxSizes.height, Math.max(minSizes.height, height));
      var canvas = document.createElement('canvas');
      var context = canvas.getContext('2d');
      canvas.width = normalizeDecimalNumber(width);
      canvas.height = normalizeDecimalNumber(height);
      context.fillStyle = options.fillColor || 'transparent';
      context.fillRect(0, 0, width, height);
      var _options$imageSmoothi = options.imageSmoothingEnabled,
          imageSmoothingEnabled = _options$imageSmoothi === void 0 ? true : _options$imageSmoothi,
          imageSmoothingQuality = options.imageSmoothingQuality;
      context.imageSmoothingEnabled = imageSmoothingEnabled;

      if (imageSmoothingQuality) {
        context.imageSmoothingQuality = imageSmoothingQuality;
      } // https://developer.mozilla.org/en-US/docs/Web/API/CanvasRenderingContext2D.drawImage


      var sourceWidth = source.width;
      var sourceHeight = source.height; // Source canvas parameters

      var srcX = initialX;
      var srcY = initialY;
      var srcWidth;
      var srcHeight; // Destination canvas parameters

      var dstX;
      var dstY;
      var dstWidth;
      var dstHeight;

      if (srcX <= -initialWidth || srcX > sourceWidth) {
        srcX = 0;
        srcWidth = 0;
        dstX = 0;
        dstWidth = 0;
      } else if (srcX <= 0) {
        dstX = -srcX;
        srcX = 0;
        srcWidth = Math.min(sourceWidth, initialWidth + srcX);
        dstWidth = srcWidth;
      } else if (srcX <= sourceWidth) {
        dstX = 0;
        srcWidth = Math.min(initialWidth, sourceWidth - srcX);
        dstWidth = srcWidth;
      }

      if (srcWidth <= 0 || srcY <= -initialHeight || srcY > sourceHeight) {
        srcY = 0;
        srcHeight = 0;
        dstY = 0;
        dstHeight = 0;
      } else if (srcY <= 0) {
        dstY = -srcY;
        srcY = 0;
        srcHeight = Math.min(sourceHeight, initialHeight + srcY);
        dstHeight = srcHeight;
      } else if (srcY <= sourceHeight) {
        dstY = 0;
        srcHeight = Math.min(initialHeight, sourceHeight - srcY);
        dstHeight = srcHeight;
      }

      var params = [srcX, srcY, srcWidth, srcHeight]; // Avoid "IndexSizeError"

      if (dstWidth > 0 && dstHeight > 0) {
        var scale = width / initialWidth;
        params.push(dstX * scale, dstY * scale, dstWidth * scale, dstHeight * scale);
      } // All the numerical parameters should be integer for `drawImage`
      // https://github.com/fengyuanchen/cropper/issues/476


      context.drawImage.apply(context, [source].concat(_toConsumableArray(params.map(function (param) {
        return Math.floor(normalizeDecimalNumber(param));
      }))));
      return canvas;
    },

    /**
     * Change the aspect ratio of the crop box.
     * @param {number} aspectRatio - The new aspect ratio.
     * @returns {Cropper} this
     */
    setAspectRatio: function setAspectRatio(aspectRatio) {
      var options = this.options;

      if (!this.disabled && !isUndefined(aspectRatio)) {
        // 0 -> NaN
        options.aspectRatio = Math.max(0, aspectRatio) || NaN;

        if (this.ready) {
          this.initCropBox();

          if (this.cropped) {
            this.renderCropBox();
          }
        }
      }

      return this;
    },

    /**
     * Change the drag mode.
     * @param {string} mode - The new drag mode.
     * @returns {Cropper} this
     */
    setDragMode: function setDragMode(mode) {
      var options = this.options,
          dragBox = this.dragBox,
          face = this.face;

      if (this.ready && !this.disabled) {
        var croppable = mode === DRAG_MODE_CROP;
        var movable = options.movable && mode === DRAG_MODE_MOVE;
        mode = croppable || movable ? mode : DRAG_MODE_NONE;
        options.dragMode = mode;
        setData(dragBox, DATA_ACTION, mode);
        toggleClass(dragBox, CLASS_CROP, croppable);
        toggleClass(dragBox, CLASS_MOVE, movable);

        if (!options.cropBoxMovable) {
          // Sync drag mode to crop box when it is not movable
          setData(face, DATA_ACTION, mode);
          toggleClass(face, CLASS_CROP, croppable);
          toggleClass(face, CLASS_MOVE, movable);
        }
      }

      return this;
    }
  };
  var AnotherCropper = WINDOW.Cropper;

  var Cropper = /*#__PURE__*/function () {
    /**
     * Create a new Cropper.
     * @param {Element} element - The target element for cropping.
     * @param {Object} [options={}] - The configuration options.
     */
    function Cropper(element) {
      var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};

      _classCallCheck(this, Cropper);

      if (!element || !REGEXP_TAG_NAME.test(element.tagName)) {
        throw new Error('The first argument is required and must be an <img> or <canvas> element.');
      }

      this.element = element;
      this.options = assign({}, DEFAULTS, isPlainObject(options) && options);
      this.cropped = false;
      this.disabled = false;
      this.pointers = {};
      this.ready = false;
      this.reloading = false;
      this.replaced = false;
      this.sized = false;
      this.sizing = false;
      this.init();
    }

    _createClass(Cropper, [{
      key: "init",
      value: function init() {
        var element = this.element;
        var tagName = element.tagName.toLowerCase();
        var url;

        if (element[NAMESPACE]) {
          return;
        }

        element[NAMESPACE] = this;

        if (tagName === 'img') {
          this.isImg = true; // e.g.: "img/picture.jpg"

          url = element.getAttribute('src') || '';
          this.originalUrl = url; // Stop when it's a blank image

          if (!url) {
            return;
          } // e.g.: "https://example.com/img/picture.jpg"


          url = element.src;
        } else if (tagName === 'canvas' && window.HTMLCanvasElement) {
          url = element.toDataURL();
        }

        this.load(url);
      }
    }, {
      key: "load",
      value: function load(url) {
        var _this = this;

        if (!url) {
          return;
        }

        this.url = url;
        this.imageData = {};
        var element = this.element,
            options = this.options;

        if (!options.rotatable && !options.scalable) {
          options.checkOrientation = false;
        } // Only IE10+ supports Typed Arrays


        if (!options.checkOrientation || !window.ArrayBuffer) {
          this.clone();
          return;
        } // Detect the mime type of the image directly if it is a Data URL


        if (REGEXP_DATA_URL.test(url)) {
          // Read ArrayBuffer from Data URL of JPEG images directly for better performance
          if (REGEXP_DATA_URL_JPEG.test(url)) {
            this.read(dataURLToArrayBuffer(url));
          } else {
            // Only a JPEG image may contains Exif Orientation information,
            // the rest types of Data URLs are not necessary to check orientation at all.
            this.clone();
          }

          return;
        } // 1. Detect the mime type of the image by a XMLHttpRequest.
        // 2. Load the image as ArrayBuffer for reading orientation if its a JPEG image.


        var xhr = new XMLHttpRequest();
        var clone = this.clone.bind(this);
        this.reloading = true;
        this.xhr = xhr; // 1. Cross origin requests are only supported for protocol schemes:
        // http, https, data, chrome, chrome-extension.
        // 2. Access to XMLHttpRequest from a Data URL will be blocked by CORS policy
        // in some browsers as IE11 and Safari.

        xhr.onabort = clone;
        xhr.onerror = clone;
        xhr.ontimeout = clone;

        xhr.onprogress = function () {
          // Abort the request directly if it not a JPEG image for better performance
          if (xhr.getResponseHeader('content-type') !== MIME_TYPE_JPEG) {
            xhr.abort();
          }
        };

        xhr.onload = function () {
          _this.read(xhr.response);
        };

        xhr.onloadend = function () {
          _this.reloading = false;
          _this.xhr = null;
        }; // Bust cache when there is a "crossOrigin" property to avoid browser cache error


        if (options.checkCrossOrigin && isCrossOriginURL(url) && element.crossOrigin) {
          url = addTimestamp(url);
        }

        xhr.open('GET', url);
        xhr.responseType = 'arraybuffer';
        xhr.withCredentials = element.crossOrigin === 'use-credentials';
        xhr.send();
      }
    }, {
      key: "read",
      value: function read(arrayBuffer) {
        var options = this.options,
            imageData = this.imageData; // Reset the orientation value to its default value 1
        // as some iOS browsers will render image with its orientation

        var orientation = resetAndGetOrientation(arrayBuffer);
        var rotate = 0;
        var scaleX = 1;
        var scaleY = 1;

        if (orientation > 1) {
          // Generate a new URL which has the default orientation value
          this.url = arrayBufferToDataURL(arrayBuffer, MIME_TYPE_JPEG);

          var _parseOrientation = parseOrientation(orientation);

          rotate = _parseOrientation.rotate;
          scaleX = _parseOrientation.scaleX;
          scaleY = _parseOrientation.scaleY;
        }

        if (options.rotatable) {
          imageData.rotate = rotate;
        }

        if (options.scalable) {
          imageData.scaleX = scaleX;
          imageData.scaleY = scaleY;
        }

        this.clone();
      }
    }, {
      key: "clone",
      value: function clone() {
        var element = this.element,
            url = this.url;
        var crossOrigin = element.crossOrigin;
        var crossOriginUrl = url;

        if (this.options.checkCrossOrigin && isCrossOriginURL(url)) {
          if (!crossOrigin) {
            crossOrigin = 'anonymous';
          } // Bust cache when there is not a "crossOrigin" property (#519)


          crossOriginUrl = addTimestamp(url);
        }

        this.crossOrigin = crossOrigin;
        this.crossOriginUrl = crossOriginUrl;
        var image = document.createElement('img');

        if (crossOrigin) {
          image.crossOrigin = crossOrigin;
        }

        image.src = crossOriginUrl || url;
        image.alt = element.alt || 'The image to crop';
        this.image = image;
        image.onload = this.start.bind(this);
        image.onerror = this.stop.bind(this);
        addClass(image, CLASS_HIDE);
        element.parentNode.insertBefore(image, element.nextSibling);
      }
    }, {
      key: "start",
      value: function start() {
        var _this2 = this;

        var image = this.image;
        image.onload = null;
        image.onerror = null;
        this.sizing = true; // Match all browsers that use WebKit as the layout engine in iOS devices,
        // such as Safari for iOS, Chrome for iOS, and in-app browsers.

        var isIOSWebKit = WINDOW.navigator && /(?:iPad|iPhone|iPod).*?AppleWebKit/i.test(WINDOW.navigator.userAgent);

        var done = function done(naturalWidth, naturalHeight) {
          assign(_this2.imageData, {
            naturalWidth: naturalWidth,
            naturalHeight: naturalHeight,
            aspectRatio: naturalWidth / naturalHeight
          });
          _this2.initialImageData = assign({}, _this2.imageData);
          _this2.sizing = false;
          _this2.sized = true;

          _this2.build();
        }; // Most modern browsers (excepts iOS WebKit)


        if (image.naturalWidth && !isIOSWebKit) {
          done(image.naturalWidth, image.naturalHeight);
          return;
        }

        var sizingImage = document.createElement('img');
        var body = document.body || document.documentElement;
        this.sizingImage = sizingImage;

        sizingImage.onload = function () {
          done(sizingImage.width, sizingImage.height);

          if (!isIOSWebKit) {
            body.removeChild(sizingImage);
          }
        };

        sizingImage.src = image.src; // iOS WebKit will convert the image automatically
        // with its orientation once append it into DOM (#279)

        if (!isIOSWebKit) {
          sizingImage.style.cssText = 'left:0;' + 'max-height:none!important;' + 'max-width:none!important;' + 'min-height:0!important;' + 'min-width:0!important;' + 'opacity:0;' + 'position:absolute;' + 'top:0;' + 'z-index:-1;';
          body.appendChild(sizingImage);
        }
      }
    }, {
      key: "stop",
      value: function stop() {
        var image = this.image;
        image.onload = null;
        image.onerror = null;
        image.parentNode.removeChild(image);
        this.image = null;
      }
    }, {
      key: "build",
      value: function build() {
        if (!this.sized || this.ready) {
          return;
        }

        var element = this.element,
            options = this.options,
            image = this.image; // Create cropper elements

        var container = element.parentNode;
        var template = document.createElement('div');
        template.innerHTML = TEMPLATE;
        var cropper = template.querySelector(".".concat(NAMESPACE, "-container"));
        var canvas = cropper.querySelector(".".concat(NAMESPACE, "-canvas"));
        var dragBox = cropper.querySelector(".".concat(NAMESPACE, "-drag-box"));
        var cropBox = cropper.querySelector(".".concat(NAMESPACE, "-crop-box"));
        var face = cropBox.querySelector(".".concat(NAMESPACE, "-face"));
        this.container = container;
        this.cropper = cropper;
        this.canvas = canvas;
        this.dragBox = dragBox;
        this.cropBox = cropBox;
        this.viewBox = cropper.querySelector(".".concat(NAMESPACE, "-view-box"));
        this.face = face;
        canvas.appendChild(image); // Hide the original image

        addClass(element, CLASS_HIDDEN); // Inserts the cropper after to the current image

        container.insertBefore(cropper, element.nextSibling); // Show the image if is hidden

        if (!this.isImg) {
          removeClass(image, CLASS_HIDE);
        }

        this.initPreview();
        this.bind();
        options.initialAspectRatio = Math.max(0, options.initialAspectRatio) || NaN;
        options.aspectRatio = Math.max(0, options.aspectRatio) || NaN;
        options.viewMode = Math.max(0, Math.min(3, Math.round(options.viewMode))) || 0;
        addClass(cropBox, CLASS_HIDDEN);

        if (!options.guides) {
          addClass(cropBox.getElementsByClassName("".concat(NAMESPACE, "-dashed")), CLASS_HIDDEN);
        }

        if (!options.center) {
          addClass(cropBox.getElementsByClassName("".concat(NAMESPACE, "-center")), CLASS_HIDDEN);
        }

        if (options.background) {
          addClass(cropper, "".concat(NAMESPACE, "-bg"));
        }

        if (!options.highlight) {
          addClass(face, CLASS_INVISIBLE);
        }

        if (options.cropBoxMovable) {
          addClass(face, CLASS_MOVE);
          setData(face, DATA_ACTION, ACTION_ALL);
        }

        if (!options.cropBoxResizable) {
          addClass(cropBox.getElementsByClassName("".concat(NAMESPACE, "-line")), CLASS_HIDDEN);
          addClass(cropBox.getElementsByClassName("".concat(NAMESPACE, "-point")), CLASS_HIDDEN);
        }

        this.render();
        this.ready = true;
        this.setDragMode(options.dragMode);

        if (options.autoCrop) {
          this.crop();
        }

        this.setData(options.data);

        if (isFunction(options.ready)) {
          addListener(element, EVENT_READY, options.ready, {
            once: true
          });
        }

        dispatchEvent(element, EVENT_READY);
      }
    }, {
      key: "unbuild",
      value: function unbuild() {
        if (!this.ready) {
          return;
        }

        this.ready = false;
        this.unbind();
        this.resetPreview();
        this.cropper.parentNode.removeChild(this.cropper);
        removeClass(this.element, CLASS_HIDDEN);
      }
    }, {
      key: "uncreate",
      value: function uncreate() {
        if (this.ready) {
          this.unbuild();
          this.ready = false;
          this.cropped = false;
        } else if (this.sizing) {
          this.sizingImage.onload = null;
          this.sizing = false;
          this.sized = false;
        } else if (this.reloading) {
          this.xhr.onabort = null;
          this.xhr.abort();
        } else if (this.image) {
          this.stop();
        }
      }
      /**
       * Get the no conflict cropper class.
       * @returns {Cropper} The cropper class.
       */

    }], [{
      key: "noConflict",
      value: function noConflict() {
        window.Cropper = AnotherCropper;
        return Cropper;
      }
      /**
       * Change the default options.
       * @param {Object} options - The new default options.
       */

    }, {
      key: "setDefaults",
      value: function setDefaults(options) {
        assign(DEFAULTS, isPlainObject(options) && options);
      }
    }]);

    return Cropper;
  }();

  assign(Cropper.prototype, render, preview, events, handlers, change, methods);
  return Cropper;
});
/*
 * ES2015 accessible modal window system, using ARIA
 * Website: https://van11y.net/accessible-modal/
 * License MIT: https://github.com/nico3333fr/van11y-accessible-modal-aria/blob/master/LICENSE
 */


'use strict';

(function (doc) {
  'use strict';

  var MODAL_JS_CLASS = 'js-modal';
  var MODAL_ID_PREFIX = 'label_modal_';
  var MODAL_CLASS_SUFFIX = 'modal';
  var MODAL_DATA_BACKGROUND_ATTR = 'data-modal-background-click';
  var MODAL_PREFIX_CLASS_ATTR = 'data-modal-prefix-class';
  var MODAL_TEXT_ATTR = 'data-modal-text';
  var MODAL_CONTENT_ID_ATTR = 'data-modal-content-id';
  var MODAL_DESCRIBEDBY_ID_ATTR = 'data-modal-describedby-id';
  var MODAL_TITLE_ATTR = 'data-modal-title';
  var MODAL_FOCUS_TO_ATTR = 'data-modal-focus-toid';
  var MODAL_CLOSE_TEXT_ATTR = 'data-modal-close-text';
  var MODAL_CLOSE_TITLE_ATTR = 'data-modal-close-title';
  var MODAL_CLOSE_IMG_ATTR = 'data-modal-close-img';
  var MODAL_ROLE = 'dialog';
  var MODAL_BUTTON_CLASS_SUFFIX = 'modal-close';
  var MODAL_BUTTON_JS_ID = 'js-modal-close';
  var MODAL_BUTTON_JS_CLASS = 'js-modal-close';
  var MODAL_BUTTON_CONTENT_BACK_ID = 'data-content-back-id';
  var MODAL_BUTTON_FOCUS_BACK_ID = 'data-focus-back';
  var MODAL_WRAPPER_CLASS_SUFFIX = 'modal__wrapper';
  var MODAL_CONTENT_CLASS_SUFFIX = 'modal__content';
  var MODAL_CONTENT_JS_ID = 'js-modal-content';
  var MODAL_CLOSE_IMG_CLASS_SUFFIX = 'modal__closeimg';
  var MODAL_CLOSE_TEXT_CLASS_SUFFIX = 'modal-close__text';
  var MODAL_TITLE_ID = 'modal-title';
  var MODAL_TITLE_CLASS_SUFFIX = 'modal-title';
  var FOCUSABLE_ELEMENTS_STRING = "a[href], area[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not([disabled]), iframe, object, embed, *[tabindex], *[contenteditable]";
  var WRAPPER_PAGE_JS = 'js-modal-page';
  var MODAL_JS_ID = 'js-modal';
  var MODAL_OVERLAY_ID = 'js-modal-overlay';
  var MODAL_OVERLAY_CLASS_SUFFIX = 'modal-overlay';
  var MODAL_OVERLAY_TXT = 'Close modal';
  var MODAL_OVERLAY_BG_ENABLED_ATTR = 'data-background-click';
  var VISUALLY_HIDDEN_CLASS = 'invisible';
  var NO_SCROLL_CLASS = 'no-scroll';
  var ATTR_ROLE = 'role';
  var ATTR_OPEN = 'open';
  var ATTR_LABELLEDBY = 'aria-labelledby';
  var ATTR_DESCRIBEDBY = 'aria-describedby';
  var ATTR_HIDDEN = 'aria-hidden'; //const ATTR_MODAL = 'aria-modal="true"';

  var ATTR_HASPOPUP = 'aria-haspopup';
  var ATTR_HASPOPUP_VALUE = 'dialog';

  var findById = function findById(id) {
    return doc.getElementById(id);
  };

  var addClass = function addClass(el, className) {
    if (el.classList) {
      el.classList.add(className); // IE 10+
    } else {
      el.className += ' ' + className; // IE 8+
    }
  };

  var removeClass = function removeClass(el, className) {
    if (el.classList) {
      el.classList.remove(className); // IE 10+
    } else {
      el.className = el.className.replace(new RegExp('(^|\\b)' + className.split(' ').join('|') + '(\\b|$)', 'gi'), ' '); // IE 8+
    }
  };

  var hasClass = function hasClass(el, className) {
    if (el.classList) {
      return el.classList.contains(className); // IE 10+
    } else {
      return new RegExp('(^| )' + className + '( |$)', 'gi').test(el.className); // IE 8+ ?
    }
  };
  /*const wrapInner = (el, wrapper_el) => { // doesn't work on IE/Edge, f…
      while (el.firstChild)
          wrapper_el.append(el.firstChild);
      el.append(wrapper_el);
   }*/


  function wrapInner(parent, wrapper) {
    if (typeof wrapper === "string") wrapper = document.createElement(wrapper);
    parent.appendChild(wrapper);

    while (parent.firstChild !== wrapper) {
      wrapper.appendChild(parent.firstChild);
    }
  }

  function remove(el) {
    /* node.remove() is too modern for IE≤11 */
    el.parentNode.removeChild(el);
  }
  /* gets an element el, search if it is child of parent class, returns id of the parent */


  var searchParent = function searchParent(el, parentClass) {
    var found = false;
    var parentElement = el.parentNode;

    while (parentElement && found === false) {
      if (hasClass(parentElement, parentClass) === true) {
        found = true;
      } else {
        parentElement = parentElement.parentNode;
      }
    }

    if (found === true) {
      return parentElement.getAttribute('id');
    } else {
      return '';
    }
  };
  /**
   * Create the template for an overlay
   * @param  {Object} config
   * @return {String}
   */


  var createOverlay = function createOverlay(config) {
    var id = MODAL_OVERLAY_ID;
    var overlayText = config.text || MODAL_OVERLAY_TXT;
    var overlayClass = config.prefixClass + MODAL_OVERLAY_CLASS_SUFFIX;
    var overlayBackgroundEnabled = config.backgroundEnabled === 'disabled' ? 'disabled' : 'enabled';
    return '<span\n                    id="' + id + '"\n                    class="' + overlayClass + '"\n                    ' + MODAL_OVERLAY_BG_ENABLED_ATTR + '="' + overlayBackgroundEnabled + '"\n                    title="' + overlayText + '"\n                    >\n                    <span class="' + VISUALLY_HIDDEN_CLASS + '">' + overlayText + '</span>\n                  </span>';
  };
  /**
   * Create the template for a modal
   * @param  {Object} config
   * @return {String}
   */


  var createModal = function createModal(config) {
    var id = MODAL_JS_ID;
    var modalClassName = config.modalPrefixClass + MODAL_CLASS_SUFFIX;
    var modalClassWrapper = config.modalPrefixClass + MODAL_WRAPPER_CLASS_SUFFIX;
    var buttonCloseClassName = config.modalPrefixClass + MODAL_BUTTON_CLASS_SUFFIX;
    var buttonCloseInner = config.modalCloseImgPath ? '<img src="' + config.modalCloseImgPath + '" alt="' + config.modalCloseText + '" class="' + config.modalPrefixClass + MODAL_CLOSE_IMG_CLASS_SUFFIX + '" />' : '<span class="' + config.modalPrefixClass + MODAL_CLOSE_TEXT_CLASS_SUFFIX + '">\n                                          ' + config.modalCloseText + '\n                                         </span>';
    var contentClassName = config.modalPrefixClass + MODAL_CONTENT_CLASS_SUFFIX;
    var titleClassName = config.modalPrefixClass + MODAL_TITLE_CLASS_SUFFIX;
    var title = config.modalTitle !== '' ? '<h1 id="' + MODAL_TITLE_ID + '" class="' + titleClassName + '">\n                                          ' + config.modalTitle + '\n                                         </h1>' : '';
    var button_close = '<button type="button" class="' + MODAL_BUTTON_JS_CLASS + ' ' + buttonCloseClassName + '" id="' + MODAL_BUTTON_JS_ID + '" title="' + config.modalCloseTitle + '" ' + MODAL_BUTTON_CONTENT_BACK_ID + '="' + config.modalContentId + '" ' + MODAL_BUTTON_FOCUS_BACK_ID + '="' + config.modalFocusBackId + '">\n                               ' + buttonCloseInner + '\n                              </button>';
    var content = config.modalText;
    var describedById = config.modalDescribedById !== '' ? ATTR_DESCRIBEDBY + '="' + config.modalDescribedById + '"' : ''; // If there is no content but an id we try to fetch content id

    if (content === '' && config.modalContentId) {
      var contentFromId = findById(config.modalContentId);

      if (contentFromId) {
        content = '<div id="' + MODAL_CONTENT_JS_ID + '">\n                              ' + contentFromId.innerHTML + '\n                             </div'; // we remove content from its source to avoid id duplicates, etc.

        contentFromId.innerHTML = '';
      }
    }

    return '<dialog id="' + id + '" class="' + modalClassName + '" ' + ATTR_ROLE + '="' + MODAL_ROLE + '" ' + describedById + ' ' + ATTR_OPEN + ' ' + ATTR_LABELLEDBY + '="' + MODAL_TITLE_ID + '">\n                    <div role="document" class="' + modalClassWrapper + '">\n                      ' + button_close + '\n                      <div class="' + contentClassName + '">\n                        ' + title + '\n                        ' + content + '\n                      </div>\n                    </div>\n                  </dialog>';
  };

  var closeModal = function closeModal(config) {
    remove(config.modal);
    remove(config.overlay);

    if (config.contentBackId !== '') {
      var contentBack = findById(config.contentBackId);

      if (contentBack) {
        contentBack.innerHTML = config.modalContent;
      }
    }

    if (config.modalFocusBackId) {
      var contentFocus = findById(config.modalFocusBackId);

      if (contentFocus) {
        contentFocus.focus();
      }
    }
  };
  /** Find all modals inside a container
   * @param  {Node} node Default document
   * @return {Array}
   */


  var $listModals = function $listModals() {
    var node = arguments.length <= 0 || arguments[0] === undefined ? doc : arguments[0];
    return [].slice.call(node.querySelectorAll('.' + MODAL_JS_CLASS));
  };
  /**
   * Build modals for a container
   * @param  {Node} node
   */


  var attach = function attach(node) {
    var addListeners = arguments.length <= 1 || arguments[1] === undefined ? true : arguments[1];
    $listModals(node).forEach(function (modal_node) {
      var iLisible = Math.random().toString(32).slice(2, 12);
      var wrapperBody = findById(WRAPPER_PAGE_JS);
      var body = doc.querySelector('body');
      modal_node.setAttribute('id', MODAL_ID_PREFIX + iLisible);
      modal_node.setAttribute(ATTR_HASPOPUP, ATTR_HASPOPUP_VALUE);

      if (wrapperBody === null || wrapperBody.length === 0) {
        var wrapper = doc.createElement('DIV');
        wrapper.setAttribute('id', WRAPPER_PAGE_JS);
        wrapInner(body, wrapper);
      }
    });

    if (addListeners) {
      /* listeners */
      ['click', 'keydown'].forEach(function (eventName) {
        doc.body.addEventListener(eventName, function (e) {
          // click on link modal
          var parentModalLauncher = searchParent(e.target, MODAL_JS_CLASS);

          if ((hasClass(e.target, MODAL_JS_CLASS) === true || parentModalLauncher !== '') && eventName === 'click') {
            var body = doc.querySelector('body');
            var modalLauncher = parentModalLauncher !== '' ? findById(parentModalLauncher) : e.target;
            var modalPrefixClass = modalLauncher.hasAttribute(MODAL_PREFIX_CLASS_ATTR) === true ? modalLauncher.getAttribute(MODAL_PREFIX_CLASS_ATTR) + '-' : '';
            var modalText = modalLauncher.hasAttribute(MODAL_TEXT_ATTR) === true ? modalLauncher.getAttribute(MODAL_TEXT_ATTR) : '';
            var modalContentId = modalLauncher.hasAttribute(MODAL_CONTENT_ID_ATTR) === true ? modalLauncher.getAttribute(MODAL_CONTENT_ID_ATTR) : '';
            var modalDescribedById = modalLauncher.hasAttribute(MODAL_DESCRIBEDBY_ID_ATTR) === true ? modalLauncher.getAttribute(MODAL_DESCRIBEDBY_ID_ATTR) : '';
            var modalTitle = modalLauncher.hasAttribute(MODAL_TITLE_ATTR) === true ? modalLauncher.getAttribute(MODAL_TITLE_ATTR) : '';
            var modalCloseText = modalLauncher.hasAttribute(MODAL_CLOSE_TEXT_ATTR) === true ? modalLauncher.getAttribute(MODAL_CLOSE_TEXT_ATTR) : MODAL_OVERLAY_TXT;
            var modalCloseTitle = modalLauncher.hasAttribute(MODAL_CLOSE_TITLE_ATTR) === true ? modalLauncher.getAttribute(MODAL_CLOSE_TITLE_ATTR) : modalCloseText;
            var modalCloseImgPath = modalLauncher.hasAttribute(MODAL_CLOSE_IMG_ATTR) === true ? modalLauncher.getAttribute(MODAL_CLOSE_IMG_ATTR) : '';
            var backgroundEnabled = modalLauncher.hasAttribute(MODAL_DATA_BACKGROUND_ATTR) === true ? modalLauncher.getAttribute(MODAL_DATA_BACKGROUND_ATTR) : '';
            var modalGiveFocusToId = modalLauncher.hasAttribute(MODAL_FOCUS_TO_ATTR) === true ? modalLauncher.getAttribute(MODAL_FOCUS_TO_ATTR) : '';
            var wrapperBody = findById(WRAPPER_PAGE_JS); // insert overlay

            body.insertAdjacentHTML('beforeEnd', createOverlay({
              text: modalCloseTitle,
              backgroundEnabled: backgroundEnabled,
              prefixClass: modalPrefixClass
            })); // insert modal

            body.insertAdjacentHTML('beforeEnd', createModal({
              modalText: modalText,
              modalPrefixClass: modalPrefixClass,
              backgroundEnabled: modalContentId,
              modalTitle: modalTitle,
              modalCloseText: modalCloseText,
              modalCloseTitle: modalCloseTitle,
              modalCloseImgPath: modalCloseImgPath,
              modalContentId: modalContentId,
              modalDescribedById: modalDescribedById,
              modalFocusBackId: modalLauncher.getAttribute('id')
            })); // hide page

            wrapperBody.setAttribute(ATTR_HIDDEN, 'true'); // add class noscroll to body

            addClass(body, NO_SCROLL_CLASS); // give focus to close button or specified element

            var closeButton = findById(MODAL_BUTTON_JS_ID);

            if (modalGiveFocusToId !== '') {
              var focusTo = findById(modalGiveFocusToId);

              if (focusTo) {
                focusTo.focus();
              } else {
                closeButton.focus();
              }
            } else {
              closeButton.focus();
            }

            e.preventDefault();
          } // click on close button or on overlay not blocked


          var parentButton = searchParent(e.target, MODAL_BUTTON_JS_CLASS);

          if ((e.target.getAttribute('id') === MODAL_BUTTON_JS_ID || parentButton !== '' || e.target.getAttribute('id') === MODAL_OVERLAY_ID || hasClass(e.target, MODAL_BUTTON_JS_CLASS) === true) && eventName === 'click') {
            var body = doc.querySelector('body');
            var wrapperBody = findById(WRAPPER_PAGE_JS);
            var modal = findById(MODAL_JS_ID);
            var modalContent = findById(MODAL_CONTENT_JS_ID) ? findById(MODAL_CONTENT_JS_ID).innerHTML : '';
            var overlay = findById(MODAL_OVERLAY_ID);
            var modalButtonClose = findById(MODAL_BUTTON_JS_ID);
            var modalFocusBackId = modalButtonClose.getAttribute(MODAL_BUTTON_FOCUS_BACK_ID);
            var contentBackId = modalButtonClose.getAttribute(MODAL_BUTTON_CONTENT_BACK_ID);
            var backgroundEnabled = overlay.getAttribute(MODAL_OVERLAY_BG_ENABLED_ATTR);

            if (!(e.target.getAttribute('id') === MODAL_OVERLAY_ID && backgroundEnabled === 'disabled')) {
              closeModal({
                modal: modal,
                modalContent: modalContent,
                overlay: overlay,
                modalFocusBackId: modalFocusBackId,
                contentBackId: contentBackId,
                backgroundEnabled: backgroundEnabled,
                fromId: e.target.getAttribute('id')
              }); // show back page

              wrapperBody.removeAttribute(ATTR_HIDDEN); // remove class noscroll to body

              removeClass(body, NO_SCROLL_CLASS);
            }
          } // strike a key when modal opened


          if (findById(MODAL_JS_ID) && eventName === 'keydown') {
            var body = doc.querySelector('body');
            var wrapperBody = findById(WRAPPER_PAGE_JS);
            var modal = findById(MODAL_JS_ID);
            var modalContent = findById(MODAL_CONTENT_JS_ID) ? findById(MODAL_CONTENT_JS_ID).innerHTML : '';
            var overlay = findById(MODAL_OVERLAY_ID);
            var modalButtonClose = findById(MODAL_BUTTON_JS_ID);
            var modalFocusBackId = modalButtonClose.getAttribute(MODAL_BUTTON_FOCUS_BACK_ID);
            var contentBackId = modalButtonClose.getAttribute(MODAL_BUTTON_CONTENT_BACK_ID);
            var $listFocusables = [].slice.call(modal.querySelectorAll(FOCUSABLE_ELEMENTS_STRING)); // esc

            if (e.keyCode === 27) {
              closeModal({
                modal: modal,
                modalContent: modalContent,
                overlay: overlay,
                modalFocusBackId: modalFocusBackId,
                contentBackId: contentBackId
              }); // show back page

              wrapperBody.removeAttribute(ATTR_HIDDEN); // remove class noscroll to body

              removeClass(body, NO_SCROLL_CLASS);
            } // tab or Maj Tab in modal => capture focus


            if (e.keyCode === 9 && $listFocusables.indexOf(e.target) >= 0) {
              // maj-tab on first element focusable => focus on last
              if (e.shiftKey) {
                if (e.target === $listFocusables[0]) {
                  $listFocusables[$listFocusables.length - 1].focus();
                  e.preventDefault();
                }
              } else {
                // tab on last element focusable => focus on first
                if (e.target === $listFocusables[$listFocusables.length - 1]) {
                  $listFocusables[0].focus();
                  e.preventDefault();
                }
              }
            } // tab outside modal => put it in focus


            if (e.keyCode === 9 && $listFocusables.indexOf(e.target) === -1) {
              e.preventDefault();
              $listFocusables[0].focus();
            }
          }
        }, true);
      });
    }
  };

  var onLoad = function onLoad() {
    attach();
    document.removeEventListener('DOMContentLoaded', onLoad);
  };

  document.addEventListener('DOMContentLoaded', onLoad);
  window.van11yAccessibleModalWindowAria = attach;
})(document);

(function (root, factory) {
  if (typeof define === 'function' && define.amd) {
    define([], factory(root));
  } else if ((typeof exports === "undefined" ? "undefined" : _typeof2(exports)) === 'object') {
    module.exports = factory(root);
  } else {
    root.vanillaTabScroller = factory(root);
  }
})(typeof global !== 'undefined' ? global : this.window || this.global, function (root) {
  //
  // Variables
  //
  var vanillaTabScroller = {}; // Object for public APIs

  var supports = 'querySelector' in document && 'addEventListener' in root; // Feature test

  var eventTimeout;
  var settings; // Default settings

  var defaults = {
    scroller: '[data-tabs-scroller]',
    wrapper: '[data-tabs-wrapper]',
    tabs: '[data-tabs]',
    tab: '[data-tab]',
    tabsButtonPrevClass: 'pwt-tabs-scroller-prev',
    tabsButtonNextClass: 'pwt-tabs-scroller-next',
    tabsOverflowClass: 'has-tabs-overflow',
    tabsOverflowLeftClass: 'has-tabs-left-overflow',
    tabsOverflowRightClass: 'has-tabs-right-overflow',
    tabsScrollAmount: 0.8,
    tabsAnimationSpeed: 400,
    // Callbacks
    beforeSetWidth: function beforeSetWidth() {},
    afterSetWidth: function afterSetWidth() {}
  }; //
  // Methods
  //

  /**
     * A simple forEach() implementation for Arrays, Objects and NodeLists.
     * @private
     * @author Todd Motto
     * @link   https://github.com/toddmotto/foreach
     * @param {Array|Object|NodeList} collection Collection of items to iterate
     * @param {Function}              callback   Callback function for each iteration
     * @param {Array|Object|NodeList} scope      Object/NodeList/Array that forEach is iterating over (aka `this`)
     */

  var forEach = function forEach(collection, callback, scope) {
    if (Object.prototype.toString.call(collection) === '[object Object]') {
      for (var prop in collection) {
        if (Object.prototype.hasOwnProperty.call(collection, prop)) {
          callback.call(scope, collection[prop], prop, collection);
        }
      }
    } else {
      for (var i = collection.length - 1; i >= 0; i--) {
        callback.call(scope, collection[i], i, collection);
      }
    }
  };
  /**
     * Merge defaults with user options
     * @private
     * @param {Object} defaults Default settings
     * @param {Object} options User options
     * @returns {Object} Merged values of defaults and options
     */


  var extend = function extend() {
    // Variables
    var extended = {};
    var deep = false;
    var i = 0;
    var length = arguments.length; // Check if a deep merge

    if (Object.prototype.toString.call(arguments[0]) === '[object Boolean]') {
      deep = arguments[0];
      i++;
    } // Merge the object into the extended object


    var merge = function merge(obj) {
      for (var prop in obj) {
        if (Object.prototype.hasOwnProperty.call(obj, prop)) {
          // If deep merge and property is an object, merge properties
          if (deep && Object.prototype.toString.call(obj[prop]) === '[object Object]') {
            extended[prop] = extend(true, extended[prop], obj[prop]);
          } else {
            extended[prop] = obj[prop];
          }
        }
      }
    }; // Loop through each object and conduct a merge


    for (; i < length; i++) {
      var obj = arguments[i];
      merge(obj);
    }

    return extended;
  };
  /**
     * Get the closest matching element up the DOM tree.
     * @private
     * @param  {Element} elem     Starting element
     * @param  {String}  selector Selector to match against
     * @return {Boolean|Element}  Returns null if not match found
     */


  var getClosest = function getClosest(elem, selector) {
    // Element.matches() polyfill
    if (!Element.prototype.matches) {
      Element.prototype.matches = Element.prototype.matchesSelector || Element.prototype.mozMatchesSelector || Element.prototype.msMatchesSelector || Element.prototype.oMatchesSelector || Element.prototype.webkitMatchesSelector || function (s) {
        var matches = (this.document || this.ownerDocument).querySelectorAll(s);
        var i = matches.length;

        while (--i >= 0 && matches.item(i) !== this) {}

        return i > -1;
      };
    } // Get closest match


    for (; elem && elem !== document; elem = elem.parentNode) {
      if (elem.matches(selector)) {
        return elem;
      }
    }

    return null;
  };
  /**
     * Scroll animation
     * @private
     * @param {Object} defaults Default settings
     * @param {Object} options User options
     * @returns {Object} Merged values of defaults and options
     */


  function scrollTo(element, from, to, duration) {
    var start = from;
    var change = to - start;
    var currentTime = 0;
    var increment = 20;

    var animateScroll = function animateScroll() {
      currentTime += increment;
      var val = Math.easeInOutQuad(currentTime, start, change, duration);
      element.scrollLeft = val;

      if (currentTime < duration) {
        setTimeout(animateScroll, increment);
      }
    };

    animateScroll();
  } // t = current time
  // b = start value
  // c = change in value
  // d = duration


  Math.easeInOutQuad = function (t, b, c, d) {
    t /= d / 2;

    if (t < 1) {
      return c / 2 * t * t + b;
    }

    t--;
    return -c / 2 * (t * (t - 2) - 1) + b;
  };
  /**
     * Setup and active
     * @private
     */


  var setup = function setup() {
    // Find gallery and return if not found
    var tabScroller = document.querySelectorAll(settings.scroller);

    if (!tabScroller) {
      return;
    }

    forEach(tabScroller, function (value) {
      // Prev button
      var beforeButton = document.createElement('button');
      beforeButton.setAttribute('type', 'button');
      beforeButton.innerHTML = 'prev';
      beforeButton.classList.add(settings.tabsButtonPrevClass);
      value.parentNode.insertBefore(beforeButton, value.parentNode.firstChild); // Next button

      var afterButton = document.createElement('button');
      afterButton.setAttribute('type', 'button');
      afterButton.innerHTML = 'next';
      afterButton.classList.add(settings.tabsButtonNextClass);
      value.parentNode.appendChild(afterButton); // scroll to current tab

      setTimeout(function () {
        vanillaTabScroller.scrollToTab(value.querySelector('.is-active'), value);
      }, settings.tabsAnimationSpeed); // Run on scrolling the tab container

      value.addEventListener('scroll', function () {
        if (!eventTimeout) {
          eventTimeout = setTimeout(function () {
            eventTimeout = null; // Reset timeout

            vanillaTabScroller.tabsCalculateScroll();
          }, 200);
        }
      }); // Find gallery and return if not found

      var tabs = value.querySelectorAll(settings.tab);

      if (!tabs) {
        return;
      }

      forEach(tabs, function (elem) {
        elem.addEventListener('click', function () {
          vanillaTabScroller.scrollToTab(elem, value);
        });
      });
    });
    vanillaTabScroller.tabsCalculateScroll();
  }; // Calculate wether there is a scrollable area and apply classes accordingly


  vanillaTabScroller.tabsCalculateScroll = function (options) {
    // Merge user options with existing settings or defaults
    var localSettings = extend(settings || defaults, options || {}); // Find gallery and return if not found

    var scroller = document.querySelectorAll(localSettings.scroller);

    if (!scroller) {
      return;
    }

    forEach(scroller, function (value) {
      // Variables
      var tabscroller = value;
      var tabsWidth = tabscroller.querySelector(settings.tabs).offsetWidth;
      var scrollerWidth = tabscroller.clientWidth;
      var scrollLeft = tabscroller.scrollLeft;
      var wrapper = tabscroller.parentNode; // Show / hide buttons

      if (tabsWidth > scrollerWidth) {
        wrapper.classList.add(settings.tabsOverflowClass);
      } else {
        wrapper.classList.remove(settings.tabsOverflowClass);
      } // "Activate" left button


      if (tabsWidth > scrollerWidth && scrollLeft > 0) {
        wrapper.classList.add(settings.tabsOverflowLeftClass);
      } // "Activate" right button


      if (tabsWidth > scrollerWidth) {
        wrapper.classList.add(settings.tabsOverflowRightClass);
      } // "Deactivate" left button


      if (tabsWidth <= scrollerWidth || scrollLeft <= 0) {
        wrapper.classList.remove(settings.tabsOverflowLeftClass);
      } // "Deactivate" right button


      if (tabsWidth <= scrollerWidth || scrollLeft >= tabsWidth - scrollerWidth) {
        wrapper.classList.remove(settings.tabsOverflowRightClass);
      }
    });
  }; // Calculate the amount of scrolling to do


  vanillaTabScroller.calculateScroll = function (wrapper, direction) {
    // Variables
    var tabsWidth = wrapper.querySelector(settings.tabs).offsetWidth;
    var scrollerWidth = wrapper.querySelector(settings.scroller).clientWidth;
    var scrollLeft = wrapper.querySelector(settings.scroller).scrollLeft;
    var scroll; // Left button (scroll to right)

    if (direction == 'prev') {
      scroll = scrollLeft - scrollerWidth * settings.tabsScrollAmount;

      if (scroll < 0) {
        scroll = 0;
      }
    } // Right button (scroll to left)


    if (direction == 'next') {
      scroll = scrollLeft + scrollerWidth * settings.tabsScrollAmount;

      if (scroll > tabsWidth - scrollerWidth) {
        scroll = tabsWidth - scrollerWidth;
      }
    } // Animate the scroll


    scrollTo(wrapper.querySelector(settings.scroller), scrollLeft, scroll, settings.tabsAnimationSpeed);
  }; // Scroll active tab into screen


  vanillaTabScroller.scrollToTab = function (element, scroller) {
    if (!element) {
      return;
    }

    var positionLeft = element.offsetLeft;
    var positionRight = positionLeft + element.offsetWidth;
    var parentPaddingLeft = parseInt(window.getComputedStyle(scroller.parentNode, null).getPropertyValue('padding-left'), 10);
    var parentPaddingRight = parseInt(window.getComputedStyle(scroller.parentNode, null).getPropertyValue('padding-right'), 10);
    var scrollerOffset = scroller.scrollLeft;
    var scrollerWidth = scroller.clientWidth;
    var scroll; // When item falls of on the right side

    if (positionRight > scrollerOffset + scrollerWidth) {
      scroll = scrollerOffset + (positionRight - (scrollerWidth + scrollerOffset) + parentPaddingRight);
    } // When item falls of on the left side


    if (positionLeft < scrollerOffset) {
      scroll = scrollerOffset - (scrollerOffset - positionLeft + parentPaddingLeft);
    }

    if (!scroll) {
      return;
    } // Animate the scroll


    scrollTo(scroller, scrollerOffset, scroll, settings.tabsAnimationSpeed);
  };
  /**
     * Handle toggle click events
     * @private
     */


  var clickHandler = function clickHandler(event) {
    // Don't run if right-click or command/control + click
    if (event.button !== 0 || event.metaKey || event.ctrlKey) {
      return;
    } // Check if event target is a tab toggle


    var wrapper = getClosest(event.target, settings.wrapper);

    if (!wrapper) {
      return;
    }

    if (event.target.className == settings.tabsButtonPrevClass) {
      vanillaTabScroller.calculateScroll(wrapper, 'prev');
    }

    if (event.target.className == settings.tabsButtonNextClass) {
      vanillaTabScroller.calculateScroll(wrapper, 'next');
    }
  };
  /**
     * On window scroll and resize, only run events at a rate of 15fps for better performance
     * @private
     * @param  {Function} eventTimeout Timeout function
     * @param  {Object} settings
     */


  var resizeThrottler = function resizeThrottler() {
    if (!eventTimeout) {
      eventTimeout = setTimeout(function () {
        eventTimeout = null; // Reset timeout

        vanillaTabScroller.tabsCalculateScroll();
      }, 200);
    }
  };
  /**
     * Destroy the current initialization.
     * @public
     */


  vanillaTabScroller.destroy = function () {
    // If plugin isn't already initialized, stop
    if (!settings) {
      return;
    } // Remove event listeners


    document.removeEventListener('click', clickHandler, false);
    root.removeEventListener('resize', resizeThrottler, false); // Reset variables

    settings = null;
  };
  /**
     * Initialize vanillaTabScroller
     * @public
     * @param {Object} options User settings
     */


  vanillaTabScroller.init = function (options) {
    // feature test
    if (!supports) {
      return;
    } // Destroy any existing initializations


    vanillaTabScroller.destroy(); // Merge user options with defaults

    settings = extend(defaults, options || {}); // Listen

    document.addEventListener('click', clickHandler, false);
    root.addEventListener('resize', resizeThrottler, false); // Run on default

    setup();
  }; //
  // Public APIs
  //


  return vanillaTabScroller;
});

var pwtImage = function () {
  var pwtImage = {};
  var croppers = [];
  var targetId = '';
  var iFrameLink = '';
  var wysiwyg = false;
  var resultFile = '';
  var altText = '';
  var captionText = '';
  var uploadImage = '';
  var imageLimit = 100;
  var showImageDirect = 0;
  var rootPath = '';
  var imagePrefix = '';
  var imageSuffix = '';
  var viewMode = 1;
  /**
   * Initialise PWT Image
   */

  pwtImage.initialise = function () {
    jQuery('.js-edit-image-button').on('click', function () {
      jQuery('.js-image-cropper').removeClass('is-hidden');
      jQuery('.js-image-preview').addClass('is-hidden');
    });
    jQuery('.js-select-existing').on('click', function () {
      jQuery('[href=#select]').trigger('click');
    });
    jQuery('.js-upload-new').on('click', function () {
      jQuery('[href=#upload]').trigger('click');
    });
  };
  /**
   * Initialise the drag and drop area
   */


  pwtImage.initialiseDragnDrop = function () {
    var dragZone = jQuery('#js-dragarea');
    dragZone.on('dragenter', function (e) {
      e.preventDefault();
      e.stopPropagation();
      dragZone.addClass('hover');
      return false;
    }); // Notify user when file is over the drop area

    dragZone.on('dragover', function (e) {
      e.preventDefault();
      e.stopPropagation();
      dragZone.addClass('hover');
      return false;
    });
    dragZone.on('dragleave', function (e) {
      e.preventDefault();
      e.stopPropagation();
      dragZone.removeClass('hover');
      return false;
    });
    dragZone.on('drop', function (e) {
      e.preventDefault();
      e.stopPropagation();
      dragZone.removeClass('hover');
      var files = e.originalEvent.target.files || e.originalEvent.dataTransfer.files;

      if (!files.length) {
        return;
      }

      var element = '.pwt-content'; // Store the file into the form

      pwtImage.createPreview(element, files); // Clean up after the upload

      pwtImage.cleanUpAfterUpload(element);

      if (showImageDirect) {
        pwtImage.directToCanvas(element);
      }
    });
  };
  /**
   * Creates another image selector instance.
   * @param element The Add another image button
   */


  pwtImage.addRepeatImage = function (element) {
    // Get a new unique ID
    var modalId = new Date().getTime(); // Change the ID on the another image button

    var addNewButton = jQuery(element).clone();
    addNewButton.prop('id', "addmore".concat(modalId)); // Duplicate the controls

    var imageField = jQuery(element).parent(); // Duplicate the first image block

    var imageBlock = imageField.first().clone(); // Get the current ID value

    var currentId = imageBlock.children(':first').prop('id').split('_')[0]; // Replace all IDs with a new ID

    imageBlock.prop('id', modalId); // Set the required IDs on the Select button

    var selectButton = imageBlock.children('[id^=label_modal_]');
    selectButton.prop('id', "label_modal_".concat(modalId));
    var newOnClick = selectButton.attr('onclick').replace("setTargetId('".concat(currentId), "setTargetId('".concat(modalId));
    newOnClick = newOnClick.replace("modalId=".concat(currentId), "modalId=".concat(modalId));
    selectButton.attr('onclick', newOnClick); // Set the other required IDs

    imageBlock.children("#".concat(currentId, "_preview")).prop('id', "".concat(modalId, "_preview")).html('');
    imageBlock.children("#".concat(currentId, "_value")).prop('id', "".concat(modalId, "_value"));
    imageBlock.children("#".concat(currentId, "_clear")).prop('id', "".concat(modalId, "_clear")).addClass('hidden').prop('onclick', "pwtImage.clearImage('".concat(modalId, "');"));
    imageBlock.children("#addmore".concat(currentId)).prop('id', "addmore".concat(modalId)); // Add the new image to the DOM

    imageField.first().parent().append(imageBlock);
  };
  /**
   * Prepare everything to allow a new image upload
   * @param id
   */


  pwtImage.prepareUpload = function (id) {
    jQuery("#".concat(id, " button.js-button-save-new")).prop('disabled', true);
    jQuery("#".concat(id, " .pwt-image-localfile")).val('');
    jQuery("#".concat(id, " .pwt-image-targetfile")).val('');
    jQuery("#".concat(id, " .pwt-message")).removeClass('is-visible');
  };
  /**
   * Set the ID of the image controls to use
   *
   * @param id The unique ID of the image controls to use.
   */


  pwtImage.setTargetId = function (id) {
    targetId = id;
  };
  /**
   * Set the iFrame link to empty out the iFrame
   */


  pwtImage.setIframeLink = function (link) {
    iFrameLink = link;
  };
  /**
   * Set the root path of the installation
   */


  pwtImage.setRootPath = function (root) {
    rootPath = root;
  };
  /**
   * Set the prefix of an image
   */


  pwtImage.setImagePrefix = function (prefix) {
    imagePrefix = prefix;
  };
  /**
   * Set the suffix of an image
   */


  pwtImage.setImageSuffix = function (suffix) {
    imageSuffix = suffix;
  };
  /**
   * Set if we are called from a WYSIWYG editor
   */


  pwtImage.setWysiwyg = function (value) {
    wysiwyg = value;
  };
  /**
   * Set if we are called from a WYSIWYG editor
   */


  pwtImage.setViewMode = function (value) {
    viewMode = value;
  };
  /**
   * Set if the image should be opened in the canvas instead of preview mode
   */


  pwtImage.showImageDirect = function (value) {
    showImageDirect = value;
  };
  /**
   * Return the ID of the image controls to use
   */


  pwtImage.getTargetId = function () {
    return targetId;
  };
  /**
   * Store the image on the server.
   *
   * @param id
   * @param createNew
   */


  pwtImage.saveImage = function (id, createNew) {
    var cropper = false; // Check if we have an existing cropper

    if (croppers[id] !== undefined) {
      cropper = croppers[id];
    }

    if (createNew === 'undefined') {
      createNew = false;
    }

    var postUrl = jQuery('#post-url').val();
    var image = jQuery("#".concat(id, "_upload"))[0].files[0];
    var crop = jQuery("#".concat(id, " .js-pwt-image-data")).val();
    var width = jQuery("#".concat(id, " .js-pwt-image-width")).val();
    var ratio = jQuery("#".concat(id, " .js-pwt-image-ratio")).val();
    var keepOriginal = jQuery("#".concat(id, " .js-pwt-image-keepOriginal")).is(':checked');
    var useOriginal = jQuery("#".concat(id, " .js-pwt-image-useOriginal")).is(':checked');
    var sourcePath = jQuery("#".concat(id, " .js-pwt-image-sourcePath")).val();
    var subPath = jQuery("#".concat(id, " .js-pwt-image-subPath")).val();
    var localfile = jQuery("#".concat(id, " .js-pwt-image-localfile")).val();
    var targetfile = jQuery("#".concat(id, " .js-pwt-image-targetfile")).val();
    var backgroundColor = jQuery("#".concat(id, " .js-pwt-image-backgroundColor")).val();
    var origin = jQuery("#".concat(id, " .js-pwt-image-origin")).val();
    var data = new FormData(); // Check if an actual file has been uploaded

    if (uploadImage && image === undefined) {
      image = uploadImage[0];
    } // Check if an alt text has been set by the image selector, if not we take the one from the edit tab


    if (altText === '') {
      altText = jQuery("#".concat(id, " #alt")).val();
    } // Check if a caption text has been set by the image selector, if not we take the one from the edit tab


    if (captionText === '') {
      captionText = jQuery("#".concat(id, " #caption")).val();
    } // Get the store folder


    switch (jQuery("#".concat(id, "_destinationFolder")).val()) {
      case 'select':
        subPath = jQuery("#".concat(id, "_selectedFolderOptions")).val();
        break;
    } // Check if we have a width from the field definition, if not we take the width from the cropped area


    if (Number(width) === 0) {
      if (cropper) {
        width = cropper.getCropBoxData().width;
      } else {
        var img = new Image();

        img.onload = function () {
          width = this.width;
        };

        img.src = localfile;
      }
    } // Add the form data


    data.append('option', 'com_pwtimage');
    data.append('task', 'image.processImage');
    data.append('format', 'json');
    data.append('pwt-image-localFile', localfile);
    data.append('pwt-image-targetFile', targetfile);
    data.append('alt', altText);
    data.append('caption', captionText);
    data.append('pwt-image-data', crop);
    data.append('pwt-image-width', width);
    data.append('pwt-image-ratio', ratio);
    data.append('pwt-image-keepOriginal', keepOriginal);
    data.append('pwt-image-useOriginal', useOriginal);
    data.append('pwt-image-sourcePath', sourcePath);
    data.append('pwt-image-subPath', subPath);
    data.append('pwt-image-backgroundColor', backgroundColor);
    data.append('pwt-image-origin', origin);
    data.append('image', image); // Find the target ID

    var targetId = pwtImage.getTargetId(); // Try to upload and process the image

    try {
      jQuery.ajax({
        type: 'POST',
        data: data,
        contentType: false,
        url: postUrl,
        cache: false,
        processData: false,
        async: false,
        headers: {
          'X-CSRF-TOKEN': Joomla.getOptions('csrf.token')
        },
        success: function success(response) {
          if (response instanceof Object === false) {
            // Check if we have a know error message
            var friendlyMessage = findErrorMessage(response);

            if (friendlyMessage.length === 0) {
              friendlyMessage = response;
            }

            renderMessage({
              error: [friendlyMessage]
            }, true);
            throw false;
          }

          if (response.message) {
            renderMessage({
              warning: [response.message]
            });
          }

          if (response.messages) {
            renderMessage(response.messages);
          } // Check if there are multiple images returned, if so, take the first one


          resultFile = response.data;

          if (!wysiwyg) {
            resultFile = resultFile.split(',');

            if (resultFile.length > 0) {
              resultFile = resultFile[0];
            }

            var value = window.parent.jQuery("#".concat(targetId, "_preview+input"));

            if (value.length === 0) {
              value = window.parent.jQuery("#".concat(targetId, "_value"));
            }

            value.val(resultFile).trigger('change');
            window.parent.jQuery("#".concat(targetId, "_preview")).html("<img src=\"".concat(rootPath).concat(resultFile, "\" />"));
            window.parent.jQuery("#".concat(targetId, "_clear")).removeClass('hidden');
          }

          if (createNew) {
            jQuery("#".concat(id, " .pwt-message")).addClass('is-visible');
            jQuery("#".concat(id, " span.has_folder")).html(response.data);
          } // All done, self-destruction is imminent if we don't want to create a new image


          if (cropper) {
            cropper.destroy();
          }

          var imageCanvas = document.getElementById("".concat(id, "_js-pwtimage-image"));
          imageCanvas.removeAttribute('src');

          if (!createNew) {
            pwtImage.closeModal();
          }
        },
        error: function error(response) {
          renderMessage({
            error: [Joomla.JText._('COM_PWTIMAGE_SAVE_FAILED', 'There was a problem to save the file')]
          });
          console.log("Image upload failed: ".concat(response.responseText));
          throw false;
        }
      }); // Switch back to upload tab if user wants to create another image

      if (createNew) {
        // Clear variables
        altText = '';
        captionText = '';
        resultFile = '';
        jQuery("#".concat(id, " .js-pwt-image-localfile")).val('');
        jQuery("#".concat(id, "_upload")).replaceWith(jQuery("#".concat(id, "_upload")).val('').clone(true)); // Clean up the crop data

        jQuery("#".concat(id, " .js-pwt-image-data")).val(''); // Reload the images

        if (subPath.length === 0) {
          sourcePath = jQuery("#".concat(id, " .js-sourcePath")).text();
          subPath = sourcePath.substring(0, sourcePath.length - 1);
        }

        pwtImage.loadFolder("#".concat(id), subPath, 'select'); // Reset the Edit page

        var fulltab = jQuery('.pwt-fulltab-message');
        fulltab.removeClass('is-hidden');
        fulltab.next().addClass('is-hidden');
        jQuery('[href="#select"]').trigger('click');
      }
    } catch (exception) {
      return false;
    }

    pwtImage.cancelImage(id);
    return true;
  };
  /**
   * Clears the current image and preview.
   * @param id  The ID of the image
   */


  pwtImage.clearImage = function (id) {
    jQuery("#".concat(id, "_value")).val('');
    jQuery("#".concat(id, "_preview img")).prop('src', '');
    jQuery("#".concat(id, "_clear")).addClass('hidden');
    jQuery("#".concat(id, "_js-pwtimage-image")).prop('src', '');
  };
  /**
   * Control the toolbar actions
   *
   * @param element  The element that has been clicked.
   */


  pwtImage.imageToolbar = function (element) {
    var id = getParentId(element);
    var data = jQuery(element).data(); // Check if the button is active

    if (jQuery(this).prop('disabled') || jQuery(this).hasClass('disabled')) {
      return;
    } // Check if we have a valid cropper


    if (croppers[id] === undefined) {
      return;
    } // Instantiate the cropper


    var cropper = croppers[id]; // Set some values if needed

    switch (data.method) {
      case 'scaleX':
      case 'scaleY':
        cropper[data.method](data.option);
        jQuery(element).data('option', data.option === 1 ? -1 : 1);
        break;

      case 'rotate':
      case 'zoom':
        cropper[data.method](data.option);
        break;

      case 'ratio':
        var ratio = data.option;

        if (typeof ratio === 'string' && ratio.indexOf('/') > 0) {
          var ratios = data.option.split('/');
          ratio = ratios[0] / ratios[1];
        }

        cropper.setAspectRatio(ratio);
        jQuery("#".concat(id, " .js-pwt-image-ratio")).val(data.option);
        break;
    }
  };
  /**
   * Open the server image selection page
   *
   * @param element  The element that has been clicked.
   */


  pwtImage.openImage = function (element) {
    // Get the parent ID
    var id = getParentId(element);
    jQuery("#".concat(id, " .js-pwt-image-toolbar")).hide();
    jQuery("#".concat(id, " .js-cropper-container")).hide();
    jQuery('form.js-image-form > div.pull-right').hide();
  };
  /**
   * Get the parent ID of a given element
   *
   * @param element
   * @returns string|boolean
   *
   * @todo Check with multiple blocks
   */


  function getParentId(element) {
    var identifier = jQuery(element).closest('.js-pwtimage-id').prop('id');

    if (identifier === undefined) {
      console.log('Cannot find parent ID for element');
      console.log(element);
      return false;
    }

    return identifier;
  }
  /**
   * Load subfolders for a selected folder
   *
   * @param element
   * @param folder
   * @param target
   * @returns {boolean}
   */


  pwtImage.loadFolder = function (element, folder, target) {
    var id = getParentId(element);
    var data = new FormData();
    var postUrl = jQuery('#post-url').val();
    jQuery('#' + id + ' #selectFilter').val(''); // Add the form data

    data.append('option', 'com_pwtimage');
    data.append('task', 'image.loadFolder');
    data.append('format', 'json');
    data.append('folder', folder); // Load the subfolders of given folder

    jQuery.ajax({
      type: 'POST',
      data: data,
      url: postUrl,
      contentType: false,
      cache: false,
      processData: false,
      headers: {
        'X-CSRF-TOKEN': Joomla.getOptions('csrf.token')
      },
      success: function success(response) {
        try {
          if (response.message) {
            console.log("Failed to load subfolders from folder ".concat(folder, ". Message: ").concat(response.message));
            renderMessage({
              warning: [response.message]
            });
            return false;
          }

          if (response.messages) {
            renderMessage(response.messages);
            return false;
          }

          if (response.data === undefined) {
            return false;
          }
        } catch (e) {
          console.log(e.message);
        }

        var link = [];

        if (response.data.folders) {
          var folderItems;
          var folderPath = [];
          var structure = '';

          if (folder.indexOf('/') > 0 || folder.length > 1) {
            folderItems = folder.split('/');
          } // Store folders to localStorage


          addToLocalStorage(id, 'folders', response.data.folders); // Construct the breadcrumb path

          jQuery(folderItems).each(function (index, folderItem) {
            structure += folderItem;

            if (structure.length > 1) {
              folderPath[index + 1] = '';

              if (index > 1) {
                folderPath[index + 1] = "<span class=\"format-slash\">/</span>";
              }

              folderPath[index + 1] += "<a onclick=\"pwtImage.loadFolder('.pwt-gallery__items--folders', '/".concat(structure, "', '").concat(target, "'); return false;\"><span class=\"icon-folder-2\"></span>").concat(folderItem, "</a>");
              structure += '/';
            }
          });
          jQuery("#".concat(target, " .js-breadcrumb")).html(folderPath.join(' ')); // Collect all folders to show

          jQuery(response.data.folders).each(function (index, item) {
            var itemPath = item;

            if (folder !== '/') {
              itemPath = "".concat(folder, "/").concat(item);
            }

            link.push("<div class=\"pwt-gallery__item\"><a onclick=\"pwtImage.loadFolder('.pwt-gallery__items--folders', '".concat(itemPath, "', '").concat(target, "'); return false;\">") + '<div class="pwt-gallery__item__content">' + '<span class="pwt-gallery__item__icon icon-folder-2"></span>' + "<span class=\"pwt-gallery__item__title\">".concat(item, "</span>") + '</div>' + '</a></div>');
          }); // Add the folders

          var itemFolders = jQuery("#".concat(id, " #").concat(target, " .pwt-gallery__items--folders"));
          itemFolders.html('');

          if (link.length) {
            itemFolders.html(link.join(' '));
          }
        } // Add the files


        var pagination = [];

        if (response.data.files) {
          // Setup pagination
          var pages = Math.ceil(response.data.files.length / imageLimit);

          for (var page = 1; page <= pages; page++) {
            pagination.push("<a onclick=\"pwtImage.showMoreImages('.pwt-gallery__items--images', ".concat(page, ", '").concat(target, "');\">").concat(page, "</a>"));
          } // Store the data in localStorage


          addToLocalStorage(id, 'files', response.data.files);
          addToLocalStorage(id, 'basePath', response.data.basePath); // Get the list of images to display

          var files = response.data.files.slice(0, imageLimit); // Prepare the images for display

          link = prepareImages(files, folder, target); // Add the files

          var itemImages = jQuery("#".concat(id, " #").concat(target, " .pwt-gallery__items--images"));
          itemImages.html('');

          if (link.length) {
            itemImages.html(link.join(' '));
          } // Add the pagination


          var paginationBar = jQuery("#".concat(id, " #").concat(target, " .pwt-pagination"));
          paginationBar.html('');

          if (pagination.length) {
            paginationBar.html("<div class=\"pwt-pagination__pages\">".concat(pagination.join(' '), "</div>"));
          }
        } // Filter the page


        pwtImage.selectFilter(jQuery('#selectFilter').val());
      },
      error: function error(response) {
        console.log("Failed to load folder: ".concat(response.responseText));
        console.log("Response code: ".concat(response.status, " ").concat(response.statusText));
      }
    });
    return false;
  };
  /**
   * Load the folders for the select picker
   *
   * @param folder
   * @returns {boolean}
   */


  pwtImage.loadSelectFolders = function (folder) {
    var data = new FormData();
    var postUrl = jQuery('#post-url').val(); // Add the form data

    data.append('option', 'com_pwtimage');
    data.append('task', 'image.loadSelectFolders');
    data.append('format', 'json');
    data.append('sourcePath', folder); // Load the folders

    jQuery.ajax({
      type: 'POST',
      data: data,
      url: postUrl,
      contentType: false,
      cache: false,
      processData: false,
      headers: {
        'X-CSRF-TOKEN': Joomla.getOptions('csrf.token')
      },
      success: function success(response) {
        // Empty the list
        choicesFolder.clearChoices(); // Add the folders

        response.data[0].forEach(function (item) {
          choicesFolder.setChoices([{
            value: item,
            label: item
          }], 'value', 'label', false);
        }); // Set the default value

        choicesFolder.setChoiceByValue('/');
      },
      error: function error(response) {
        console.log("Failed to load folder: ".concat(response.responseText));
        console.log("Response code: ".concat(response.status, " ").concat(response.statusText));
      }
    });
    return false;
  };
  /**
   * Pagination class
   *
   * @param element
   * @param page
   * @param target
   */


  pwtImage.showMoreImages = function (element, page, target) {
    var id = getParentId(element);
    var storedFiles = getFromLocalStorage(id, 'files');
    var basePath = getFromLocalStorage(id, 'basePath');
    var start = page === 1 ? 0 : (page - 1) * imageLimit;
    var end = page * imageLimit;
    var files = storedFiles.slice(start, end); // Prepare the images for display

    var link = prepareImages(files, basePath, target); // Add the files

    var itemImages = jQuery("#".concat(id, " #").concat(target, " .pwt-gallery__items--images"));
    itemImages.html('');

    if (link.length) {
      itemImages.html(link.join(' '));
    } // Filter the page


    pwtImage.selectFilter(jQuery('#selectFilter').val(), page);
  };
  /**
   * Prepare a list of images for display
   */


  function prepareImages(files, folder, target) {
    var link = []; // Clean up the folder as the rootPath ends with a slash and the folder starts with a slash

    if (folder.substring(0, 1) === '/') {
      folder = "".concat(folder.substring(1), "/");
    }

    jQuery(files).each(function (index, item) {
      var itemPath = rootPath + folder + item;
      link.push(getImageElement(item, itemPath, target));
    });
    return link;
  }
  /**
   * Construct an image element
   */


  function getImageElement(item, itemPath, target) {
    var imageElement = '';
    var imageUrl = itemPath;

    if (imagePrefix !== '') {
      imageUrl = imagePrefix + imageUrl.replace(rootPath, '') + imageSuffix;
    }

    switch (target) {
      case 'select':
        imageElement = "".concat('<div class="pwt-gallery__item">' + '<a onclick="return pwtImage.previewImage(\'.pwt-gallery__items--images\', \'').concat(itemPath, "');\" ") + "title=\"".concat(item, "\">") + '<div class="pwt-gallery__item__image">' + '<div class="pwt-gallery__item__center">' + "<img src=\"".concat(imageUrl, "\" alt=\"").concat(item, "\" />") + '</div>' + "<div class=\"pwt-gallery__item__imagename\">".concat(item, "</div>") + '</div>' + '</a>' + '</div>';
        break;
    }

    return imageElement;
  }
  /**
   * Find a known error message
   *
   * @param response  The response message to analyze
   *
   * @returns {string}
   */


  function findErrorMessage(response) {
    // 'Fatal error: Allowed memory size';
    var pattern = 'Allowed memory size';

    if (response.indexOf(pattern) !== -1) {
      return Joomla.JText._('COM_PWTIMAGE_ERROR_ALLOWED_MEMORY_SIZE');
    }

    return '';
  }
  /**
   * This adds a file on the server to the canvas for cropping
   *
   * @param element  The page element to find the ID for.
   * @param file     The selected image on the server
   * @param image
   * @param upload
   *
   * @returns {boolean}
   */


  pwtImage.addImageToCanvas = function (element, file, image, upload) {
    var id = getParentId(element);

    if (upload === undefined || upload === null) {
      upload = false;
    } // Remove any existing cropper boxes


    jQuery("#".concat(id, " div.pwt-body div.cropper-container > div.cropper-container")).remove(); // Get the ratio

    var ratio = jQuery("#".concat(id, " .js-pwt-image-ratio")).val();
    var ratioSplit = ratio.split('/'); // Get the image field

    if (!upload) {
      image = document.getElementById("".concat(id, "_js-pwtimage-image"));
    } // Instantiate the cropper


    var cropper = new Cropper(image, {
      aspectRatio: ratioSplit[0] / ratioSplit[1],
      viewMode: viewMode,
      crop: function crop(e) {
        var json = ["{\"x\":".concat(e.detail.x), "\"y\":".concat(e.detail.y), "\"height\":".concat(e.detail.height), "\"width\":".concat(e.detail.width), "\"rotate\":".concat(e.detail.rotate), "\"scaleX\":".concat(e.detail.scaleX), "\"scaleY\":".concat(e.detail.scaleY, "}")].join();
        jQuery("#".concat(id, " .js-pwt-image-data")).val(json);
      },
      ready: function ready() {
        var imageData = cropper.getImageData();
        cropper.setCropBoxData({
          top: 0,
          width: imageData.width
        });
      }
    }); // Add the cropper to the list of croppers

    if (upload) {
      croppers[id] = cropper;
      cropper.init(); // Set the image value in the form

      window.parent.jQuery("#".concat(id, "_value")).prop('value', file.name).trigger('change');
    } else {
      // Replace the image
      cropper.replace(file);
      croppers[id] = cropper;
    } // Hide the message


    jQuery("#".concat(id, " .pwt-fulltab-message")).addClass('is-hidden'); // Enable the Save & new button

    jQuery("#".concat(id, " button.js-button-save-new")).prop('disabled', false);
    jQuery("#".concat(id, " .js-image-info")).removeClass('is-hidden'); // Make the edit tab visible

    jQuery('[href="#edit"]').trigger('click');
    jQuery("#".concat(id, " .js-button-image")).removeClass('hidden');
    return false;
  };
  /**
   * Get the basename of a filename with path
   *
   * @param str
   * @returns {string}
   */


  function baseName(str) {
    var base = str.substring(str.lastIndexOf('/') + 1);

    if (base.lastIndexOf('.') !== -1) {
      base = base.substring(0, base.lastIndexOf('.'));
    }

    return base;
  }
  /**
   * Close the modal window
   */


  pwtImage.closeModal = function () {
    if (wysiwyg) {
      if (resultFile) {
        jQuery('form.js-image-form #formPath').val(resultFile);
      }

      if (altText) {
        jQuery('form.js-image-form #alt').val(altText);
      }

      if (captionText) {
        jQuery('form.js-image-form #caption').val(captionText);
      }

      jQuery('form.js-image-form #layout').val('close');
      jQuery('form.js-image-form').submit();
    } else {
      window.parent.jQuery("iframe#pwtImageFrame-".concat(targetId)).prop('src', iFrameLink);
      window.parent.jQuery('#js-modal-close').click();
    }
  };
  /**
   * Show the applicable destination option
   */


  pwtImage.setDestination = function (element) {
    var id = getParentId(element);

    if (choicesDestination !== undefined) {
      switch (choicesDestination.getValue().value) {
        case 'default':
          jQuery("#".concat(id, "_enterFolder")).addClass('is-visible').removeClass('is-hidden');
          jQuery("#".concat(id, "_subPath")).prop('disabled', true);
          jQuery("#".concat(id, "_selectFolder")).addClass('is-hidden').removeClass('is-visible');
          break;

        case 'select':
          jQuery("#".concat(id, "_enterFolder")).addClass('is-hidden').removeClass('is-visible');
          jQuery("#".concat(id, "_selectFolder")).addClass('is-visible').removeClass('is-hidden');
          jQuery("#".concat(id, "_selectFolder")).trigger('change');
          break;

        case 'custom':
          jQuery("#".concat(id, "_enterFolder")).addClass('is-visible').removeClass('is-hidden');
          jQuery("#".concat(id, "_subPath")).prop('disabled', false);
          jQuery("#".concat(id, "_selectFolder")).addClass('is-hidden').removeClass('is-visible');
          break;
      }
    }
  };
  /**
   * Shows the alt and caption input fields for a selected gallery image
   *
   * @param element
   */


  pwtImage.imageInfo = function (element) {
    // Check if we should act on the click, only act in the sortable result div
    if (jQuery(element).closest('#js-sortable-result').length === 0) {
      return;
    } // Check if we are selected


    var isSelected = jQuery(element).parent().find('a').hasClass('is-selected'); // Remove all selected borders

    jQuery('#js-sortable-result').find('a.is-selected').removeClass('is-selected'); // Hide any existing inputs

    jQuery('#gallery-image-info .js-image-info').hide(); // If we were selected, don't select it again

    if (isSelected) {
      // Show the Alt and Caption box
      jQuery('#gallery-image-info').hide();
      return;
    } // Add border to selected image


    jQuery(element).parent().find('a').addClass('is-selected'); // Get the unique image name

    if (element.type === 'button') {
      var imageName = jQuery(element).siblings().find('img').prop('src').replace(/[\/\.]/g, '_');
    } else {
      var imageName = jQuery(element).find('img').prop('src').replace(/[\/\.]/g, '_');
    } // Check if an input exists


    if (jQuery("#gallery-image-info input[name=\"input_".concat(imageName, "_alt\"]")).length === 0) {
      // Create the text field image name
      var pathField = document.createElement('div');
      pathField.className = "js-image-info ".concat(imageName, "_path");
      pathField.innerHTML = jQuery(element).parent().find('img').prop('src');
      jQuery('#gallery-image-info .pwt-form-group:first').append(pathField); // Create the alt input box with a unique name

      var altInput = document.createElement('input');
      altInput.type = 'text';
      altInput.name = "input_".concat(imageName, "_alt");
      altInput.className = 'pwt-form-control js-image-info js-pwt-image-alt';
      jQuery('#gallery-image-info .pwt-form-group:nth-child(3)').append(altInput); // Create the caption input box with a unique name

      var captionInput = document.createElement('input');
      captionInput.type = 'text';
      captionInput.name = "input_".concat(imageName, "_caption");
      captionInput.className = 'pwt-form-control js-image-info js-pwt-image-caption';
      jQuery('#gallery-image-info .pwt-form-group:nth-child(4)').append(captionInput);
    } else {
      // Show existing input boxes
      jQuery("#gallery-image-info div.".concat(imageName, "_path")).show();
      jQuery("#gallery-image-info input[name=\"input_".concat(imageName, "_alt\"]")).show();
      jQuery("#gallery-image-info input[name=\"input_".concat(imageName, "_caption\"]")).show();
    } // Show the Alt and Caption box


    jQuery('#gallery-image-info').show();
    jQuery("#gallery-image-info input[name=\"input_".concat(imageName, "_alt\"]")).focus();
  };
  /**
   * Clean up after uploading an image
   *
   * @param element
   */


  pwtImage.cleanUpAfterUpload = function (element) {
    var id = getParentId(element); // Make the edit tab visible

    jQuery('[href="#edit"]').trigger('click'); // Set the Save to folder selector to default

    if (choicesDestination !== undefined) {
      choicesDestination.setChoiceByValue('default');
      pwtImage.setDestination("#".concat(id, "_destinationFolder"));
    } // Clear the local file


    jQuery('.js-pwt-image-localfile').val('');
  };
  /**
   * Create a preview of the manual uploaded image
   *
   * @param element
   * @returns {boolean}
   */


  pwtImage.uploadImagePreview = function (element) {
    // Get the image details
    var url = window.URL || window.webkitURL; // Check if we have a URL

    if (url) {
      // Get the image field
      var id = getParentId(element);

      var _imageUpload = document.getElementById("".concat(id, "_upload"));

      _imageUpload.onchange = function () {
        // Create the preview
        pwtImage.createPreview(element, this.files); // Clean up

        pwtImage.cleanUpAfterUpload(element); // Hide the use original image option, if use original is available

        var useOriginal = document.getElementById('useOriginal');

        if (useOriginal !== null) {
          // Perform the operations
          pwtImage.useOriginal(id, element); // Uncheck the box

          document.getElementById('useOriginal').checked = false;
          document.getElementById('useOriginal').value = 0; // Hide the box

          document.getElementById('useOriginal').parentElement.style.display = 'none';
        } // Make the edit tab active


        jQuery('[href="#edit"]')[0].click();

        if (showImageDirect) {
          pwtImage.directToCanvas(element);
        }
      };
    }

    return false;
  };
  /**
   * Shortcut for adding image to canvas from preview window
   *
   * @param element
   */


  pwtImage.directToCanvas = function (element) {
    var id = getParentId(element); // Set the correct classes

    jQuery("#".concat(id, " .js-image-preview")).addClass('is-hidden');
    jQuery("#".concat(id, " .js-image-cropper")).removeClass('is-hidden'); // Get the file to show

    var file = jQuery("#".concat(id, "_preview img")).prop('src'); // Disable the keep original size option

    jQuery("#".concat(id, " #keepOriginal")).prop('checked', false); // Add the file to the canvas

    pwtImage.addImageToCanvas(element, file);
  };
  /**
   * Create an image preview for a manual or drag and drop uploaded image
   *
   * @param element
   * @param files
   * @returns {boolean}
   */


  pwtImage.createPreview = function (element, files) {
    // Get the parent ID
    var id = getParentId(element);
    var image = jQuery("#".concat(id, "_preview img")); // Check if any file has been uploaded

    if (files && files.length) {
      // Store the uploaded files
      uploadImage = files; // Get the values

      var file = files[0];
      var imageMaxSize = jQuery('.js-pwt-image-maxsize').val();
      var dimensionSizes = jQuery('.js-pwt-image-dimensionsize'); // Check if the image is within our maximum sizes

      if (file.size > imageMaxSize && imageMaxSize > 0) {
        window.alert(jQuery('.js-pwt-image-maxsize-message').val()); // Clear the images

        imageUpload.value = '';
        image.removeAttribute('src');
        return false;
      } // Check if the image is of an image type


      if (/^image\/\w+$/.test(file.type)) {
        // Load the image
        image.prop('src', URL.createObjectURL(file)); // Now check for dimension size

        if (image.naturalHeight > dimensionSizes.val() || image.naturalWidth > dimensionSizes.val()) {
          // Show the error message
          window.alert(jQuery('.js-pwt-image-maxsize-message').val()); // Clear the images

          imageUpload.value = '';
          image.removeAttribute('src');
          return false;
        } // Set the filename in the Save to folder location


        var fileName = file.name.split('/').slice(-1)[0];
        jQuery("#".concat(id, " #pwt-image-targetFile")).val(fileName); // Set the width
      } else {
        window.alert(Joomla.JText._('COM_PWTIMAGE_CHOOSE_IMAGE', 'Please choose an image file.'));
      }
    } // Hide the message


    jQuery("#".concat(id, " .pwt-message")).removeClass('is-visible'); // Show the preview

    jQuery("#".concat(id, " .pwt-edit-block")).removeClass('is-hidden');
    jQuery("#".concat(id, " .pwt-fulltab-message")).addClass('is-hidden'); // Enable the insert button on the edit page

    jQuery("#".concat(id, " .js-button-image")).prop('disabled', false); // Show some basic info of the original file

    image.off('load').on('load', function () {
      jQuery('.js-pwt-filename').html(file.name);
      jQuery('.js-pwt-filesize').html(filesize(file.size));
      jQuery('.js-pwt-fileext').html(file.type.replace('image/', ''));
      jQuery('.js-pwt-filedimensions').html("".concat(this.naturalWidth, " x ").concat(this.naturalHeight));
    });
  };
  /**
   * Add an image selected from server to the edit tab. Not in the cropper but in a div.
   *
   * @param element
   * @param file
   */


  pwtImage.previewImage = function (element, file) {
    var id = getParentId(element);
    pwtImage.cancelImage(id); // Get the local path of the file

    var cleanFile = file.replace(rootPath, '/');
    jQuery("#".concat(id, "_preview img")).prop('src', file);
    jQuery("#".concat(id, "_js-pwtimage-image")).prop('src', cleanFile);
    jQuery("#".concat(id, " .js-pwt-image-localfile")).val(cleanFile); // Make the edit tab visible

    jQuery('[href="#edit"]').trigger('click'); // Hide the message

    jQuery("#".concat(id, " .pwt-fulltab-message")).addClass('is-hidden');
    jQuery("#".concat(id, " .pwt-edit-block")).removeClass('is-hidden'); // Show the preview

    jQuery("#".concat(id, " .js-image-preview")).removeClass('is-hidden'); // Set the folder option to select

    if (choicesDestination !== undefined) {
      // Check if the select option exists
      choicesDestination.setChoiceByValue('select');
      var selected = choicesDestination.getValue().value;

      if (selected !== 'select') {
        choicesDestination.setChoiceByValue('default');
      }

      pwtImage.setDestination("#".concat(id, "_destinationFolder"));
    } // Remove the domain form the file


    if (file.indexOf(rootPath) === 0) {
      file = file.substring(rootPath.length - 1);
    } // Set the path of the image


    var basePath = jQuery("#".concat(id, "_selectFolder")).prev().text();
    var path = file.substring(basePath.length, file.lastIndexOf('/'));
    var filename = file.substring(file.lastIndexOf('/') + 1);
    var sourcePath = jQuery("#".concat(id, " .js-sourcePath")).text();

    if (path.lastIndexOf(sourcePath) === 0) {
      path = path.substring(sourcePath.length);
    }

    if (path.length > 1) {
      if (selected === 'select' && choicesFolder !== undefined) {
        choicesFolder.setChoiceByValue(path);
      } else {
        jQuery("#".concat(id, "_subPath")).val("".concat(path, "/"));
      }
    }

    if (filename.length > 0) {
      jQuery("#".concat(id, " #pwt-image-targetFile")).val(filename);
    } // Enable the insert button on the edit page


    jQuery("#".concat(id, " .js-button-image")).prop('disabled', false);

    if (showImageDirect) {
      pwtImage.directToCanvas('.pwt-content');
    } // Show the Use original image option


    var useOriginal = document.getElementById('useOriginal');

    if (useOriginal) {
      useOriginal.parentElement.style.display = 'block';
    } // Show some basic info of the original file


    jQuery.ajax({
      type: 'POST',
      data: {
        option: 'com_pwtimage',
        task: 'image.loadMetaData',
        format: 'json',
        image: file
      },
      url: jQuery('#post-url').val(),
      headers: {
        'X-CSRF-TOKEN': Joomla.getOptions('csrf.token')
      },
      success: function success(response) {
        if (response && response.success === true) {
          file = response.data;
          jQuery('.js-pwt-filename').html(file.name);
          jQuery('.js-pwt-filesize').html(filesize(file.size));
          jQuery('.js-pwt-fileext').html(file.mime.replace('image/', ''));
          jQuery('.js-pwt-filedimensions').html("".concat(file[0], " x ").concat(file[1]));
        }
      },
      error: function error(response) {
        // Hide the info section
        console.log(response);
      }
    });
  };
  /**
   * Cancel the editing of the current image
   */


  pwtImage.cancelImage = function (id) {
    jQuery("#".concat(id, " button.js-button-save-new")).prop('disabled', true);
    jQuery("#".concat(id, " .js-image-cropper")).addClass('is-hidden');
    jQuery("#".concat(id, " .js-image-info")).addClass('is-hidden');
    jQuery("#".concat(id, " .js-image-preview")).removeClass('is-hidden');
  };
  /**
   * Filter the select list of images
   *
   * @param search The search value to filter on
   * @param page   The page that is active
   */


  pwtImage.selectFilter = function (search, page) {
    // Remove all images
    jQuery('#select .pwt-filepicker__content .pwt-gallery__items--images').html('');

    if (page === undefined) {
      page = 1;
    } // Initialise all variables


    var id = getParentId('#selectFilter');
    var storedFiles = getFromLocalStorage(id, 'files');
    var storedFolders = getFromLocalStorage(id, 'folders');
    var basePath = getFromLocalStorage(id, 'basePath');
    var filteredItems = storedFiles;
    var filteredFolders = storedFolders;
    var target = 'select';
    var start = page === 1 ? 0 : (page - 1) * imageLimit;
    var end = page * imageLimit;
    var pagination = []; // If we have a search filter, filter the images

    if (search) {
      filteredItems = filterItems(storedFiles, search);
      filteredFolders = filterFolders(storedFolders, search);
    } // Get the final list of items


    var files = filteredItems.slice(start, end); // Create the list of images to show

    var links = prepareImages(files, basePath, target); // Add the files

    var itemImages = jQuery("#".concat(id, " #").concat(target, " .pwt-gallery__items--images"));
    itemImages.html('');

    if (links.length) {
      itemImages.html(links.join(' '));
    } // Setup pagination


    var pages = Math.ceil(filteredItems.length / imageLimit);

    for (page = 1; page <= pages; page++) {
      pagination.push("<a onclick=\"pwtImage.showMoreImages('.pwt-gallery__items--images', ".concat(page, ", '").concat(target, "');\">").concat(page, "</a>"));
    } // Add the pagination


    var paginationBar = jQuery("#".concat(id, " #").concat(target, " .pwt-pagination"));
    paginationBar.html('');

    if (pagination.length) {
      paginationBar.html("<div class=\"pwt-pagination__pages\">".concat(pagination.join(' '), "</div>"));
    }

    pushFolders(id, filteredFolders, target);
  };
  /**
   * Push the filtered (or not) folders
   *
   * @param id
   * @param folders
   * @param target
   */


  function pushFolders(id, folders, target) {
    var link = [];
    var folder = getFromLocalStorage(id, 'basePath');
    jQuery(folders).each(function (index, item) {
      var itemPath = item;

      if (folder !== '/') {
        itemPath = "".concat(folder, "/").concat(item);
      }

      link.push("<div class=\"pwt-gallery__item\"><a onclick=\"pwtImage.loadFolder('.pwt-gallery__items--folders', '".concat(itemPath, "', '").concat(target, "'); return false;\">") + '<div class="pwt-gallery__item__content">' + '<span class="pwt-gallery__item__icon icon-folder-2"></span>' + "<span class=\"pwt-gallery__item__title\">".concat(item, "</span>") + '</div>' + '</a></div>');
    }); // Add the folders

    var itemFolders = jQuery('#' + id + ' #select .pwt-gallery__items--folders');
    itemFolders.html('');

    if (link.length) {
      itemFolders.html(link.join(' '));
    }
  }
  /**
   * Restore canvas as user wants to keep the original image
   */


  pwtImage.useOriginal = function (id, element) {
    var elements = document.getElementsByClassName('pwt-image-modify');
    var display = '';

    if (jQuery(element).is(':checked')) {
      pwtImage.cancelImage(id); // Hide modify elements

      display = 'none';
    }

    for (var i = 0; i < elements.length; i++) {
      elements[i].style.display = display;
    }
  };
  /**
   * Restore canvas as user wants to keep the original image
   */


  pwtImage.keepOriginal = function (id, element) {
    if (jQuery(element).is(':checked')) {
      pwtImage.cancelImage(id);
    }
  };
  /**
   * Array filters items based on search criteria (query)
   *
   * @param items
   * @param query
   */


  function filterItems(items, query) {
    return items.filter(function (el) {
      return el.toLowerCase().indexOf(query.toLowerCase()) > -1;
    });
  }
  /**
   * Array filters items based on search criteria (query)
   *
   * @param folders
   * @param query
   */


  function filterFolders(folders, query) {
    return folders.filter(function (el) {
      return el.toLowerCase().indexOf(query.toLowerCase()) > -1;
    });
  }
  /**
   * Get the filesize in a human readable string
   *
   * @param size The size in bits
   *
   * @return string Human readable string
   */


  function filesize(size) {
    var kb = (size / 1024).toFixed(2);
    var mb = (size / 1048576).toFixed(2);
    return mb >= 1 ? "".concat(mb, " MB") : "".concat(kb, " KB");
  }
  /**
   * Add the data from the localStorage
   *
   * @param id The unique session ID
   * @param type The type of data to be stored
   * @param payload The data to be stored
   */


  function addToLocalStorage(id, type, payload) {
    // Get our container
    var container = JSON.parse(localStorage.getItem('pwtImage')); // Make sure the container is defined

    if (container === undefined || container === null) {
      container = {};
    } // Construct a unique key


    var key = "".concat(id, "_").concat(type); // Check for any existing IDs, remove obsolete ones

    for (var storedKey in container) {
      if (storedKey.indexOf(type) > 0 && key !== storedKey && container.hasOwnProperty(storedKey)) {
        delete container[storedKey];
      }
    } // Fill the container


    container[key] = payload; // Store the container

    localStorage.setItem('pwtImage', JSON.stringify(container));
  }
  /**
   * Get the data from the localStorage
   */


  function getFromLocalStorage(id, type) {
    // Get our container
    var container = JSON.parse(localStorage.getItem('pwtImage')); // Make sure the container is defined

    if (container === undefined || container === null) {
      return [];
    } // Construct a unique key


    var key = "".concat(id, "_").concat(type); // Check if the key exist

    if (container.hasOwnProperty(key)) {
      return container[key];
    }
  }
  /**
   * Render a Joomla system message
   */


  function renderMessage(message, local) {
    if (local === 'undefined') {
      // Render the Joomla message, first make sure there is a message container present
      var messageContainer = window.parent.document.getElementById('system-message-container');

      if (!messageContainer) {
        return true;
      }

      window.parent.Joomla.renderMessages(message);
    } else {
      // Render the Joomla message, first make sure there is a message container present
      var messageContainer = window.document.getElementById('system-message-container');

      if (!messageContainer) {
        return true;
      }

      window.Joomla.renderMessages(message);
    }
  }
  /**
   * Set the hotkeys
   */


  pwtImage.setHotkeys = function (modalId) {
    document.addEventListener('keyup', function (e) {
      if (e.key === 'Escape') {
        pwtImage.closeModal();
      } else if (e.key === 'Enter') {
        var _modalId = window.Joomla.optionsStorage.PWTImageConfig.modalId;
        pwtImage.saveImage(_modalId);
      }
    });
  }; // Return the public parts


  return pwtImage;
}();
/*! choices.js v7.1.5 | © 2019 Josh Johnson | https://github.com/jshjohnson/Choices#readme */


(function webpackUniversalModuleDefinition(root, factory) {
  if ((typeof exports === "undefined" ? "undefined" : _typeof2(exports)) === 'object' && (typeof module === "undefined" ? "undefined" : _typeof2(module)) === 'object') module.exports = factory();else if (typeof define === 'function' && define.amd) define([], factory);else if ((typeof exports === "undefined" ? "undefined" : _typeof2(exports)) === 'object') exports["Choices"] = factory();else root["Choices"] = factory();
})(window, function () {
  return (
    /******/
    function (modules) {
      // webpackBootstrap

      /******/
      // The module cache

      /******/
      var installedModules = {};
      /******/

      /******/
      // The require function

      /******/

      function __webpack_require__(moduleId) {
        /******/

        /******/
        // Check if module is in cache

        /******/
        if (installedModules[moduleId]) {
          /******/
          return installedModules[moduleId].exports;
          /******/
        }
        /******/
        // Create a new module (and put it into the cache)

        /******/


        var module = installedModules[moduleId] = {
          /******/
          i: moduleId,

          /******/
          l: false,

          /******/
          exports: {}
          /******/

        };
        /******/

        /******/
        // Execute the module function

        /******/

        modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
        /******/

        /******/
        // Flag the module as loaded

        /******/

        module.l = true;
        /******/

        /******/
        // Return the exports of the module

        /******/

        return module.exports;
        /******/
      }
      /******/

      /******/

      /******/
      // expose the modules object (__webpack_modules__)

      /******/


      __webpack_require__.m = modules;
      /******/

      /******/
      // expose the module cache

      /******/

      __webpack_require__.c = installedModules;
      /******/

      /******/
      // define getter function for harmony exports

      /******/

      __webpack_require__.d = function (exports, name, getter) {
        /******/
        if (!__webpack_require__.o(exports, name)) {
          /******/
          Object.defineProperty(exports, name, {
            enumerable: true,
            get: getter
          });
          /******/
        }
        /******/

      };
      /******/

      /******/
      // define __esModule on exports

      /******/


      __webpack_require__.r = function (exports) {
        /******/
        if (typeof Symbol !== 'undefined' && Symbol.toStringTag) {
          /******/
          Object.defineProperty(exports, Symbol.toStringTag, {
            value: 'Module'
          });
          /******/
        }
        /******/


        Object.defineProperty(exports, '__esModule', {
          value: true
        });
        /******/
      };
      /******/

      /******/
      // create a fake namespace object

      /******/
      // mode & 1: value is a module id, require it

      /******/
      // mode & 2: merge all properties of value into the ns

      /******/
      // mode & 4: return value when already ns object

      /******/
      // mode & 8|1: behave like require

      /******/


      __webpack_require__.t = function (value, mode) {
        /******/
        if (mode & 1) value = __webpack_require__(value);
        /******/

        if (mode & 8) return value;
        /******/

        if (mode & 4 && _typeof2(value) === 'object' && value && value.__esModule) return value;
        /******/

        var ns = Object.create(null);
        /******/

        __webpack_require__.r(ns);
        /******/


        Object.defineProperty(ns, 'default', {
          enumerable: true,
          value: value
        });
        /******/

        if (mode & 2 && typeof value != 'string') for (var key in value) {
          __webpack_require__.d(ns, key, function (key) {
            return value[key];
          }.bind(null, key));
        }
        /******/

        return ns;
        /******/
      };
      /******/

      /******/
      // getDefaultExport function for compatibility with non-harmony modules

      /******/


      __webpack_require__.n = function (module) {
        /******/
        var getter = module && module.__esModule ?
        /******/
        function getDefault() {
          return module['default'];
        } :
        /******/
        function getModuleExports() {
          return module;
        };
        /******/

        __webpack_require__.d(getter, 'a', getter);
        /******/


        return getter;
        /******/
      };
      /******/

      /******/
      // Object.prototype.hasOwnProperty.call

      /******/


      __webpack_require__.o = function (object, property) {
        return Object.prototype.hasOwnProperty.call(object, property);
      };
      /******/

      /******/
      // __webpack_public_path__

      /******/


      __webpack_require__.p = "/public/assets/scripts/";
      /******/

      /******/

      /******/
      // Load entry module and return exports

      /******/

      return __webpack_require__(__webpack_require__.s = 5);
      /******/
    }(
    /************************************************************************/

    /******/
    [
    /* 0 */

    /***/
    function (module, exports, __webpack_require__) {
      var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;
      /*!
      Copyright (c) 2017 Jed Watson.
      Licensed under the MIT License (MIT), see
      http://jedwatson.github.io/classnames
      */

      /* global define */


      (function () {
        'use strict';

        var hasOwn = {}.hasOwnProperty;

        function classNames() {
          var classes = [];

          for (var i = 0; i < arguments.length; i++) {
            var arg = arguments[i];
            if (!arg) continue;

            var argType = _typeof2(arg);

            if (argType === 'string' || argType === 'number') {
              classes.push(arg);
            } else if (Array.isArray(arg) && arg.length) {
              var inner = classNames.apply(null, arg);

              if (inner) {
                classes.push(inner);
              }
            } else if (argType === 'object') {
              for (var key in arg) {
                if (hasOwn.call(arg, key) && arg[key]) {
                  classes.push(key);
                }
              }
            }
          }

          return classes.join(' ');
        }

        if (true && module.exports) {
          classNames["default"] = classNames;
          module.exports = classNames;
        } else if (true) {
          // register as 'classnames', consistent with npm package name
          !(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_RESULT__ = function () {
            return classNames;
          }.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
        } else {}
      })();
      /***/

    },
    /* 1 */

    /***/
    function (module, exports, __webpack_require__) {
      "use strict";

      var isMergeableObject = function isMergeableObject(value) {
        return isNonNullObject(value) && !isSpecial(value);
      };

      function isNonNullObject(value) {
        return !!value && _typeof2(value) === 'object';
      }

      function isSpecial(value) {
        var stringValue = Object.prototype.toString.call(value);
        return stringValue === '[object RegExp]' || stringValue === '[object Date]' || isReactElement(value);
      } // see https://github.com/facebook/react/blob/b5ac963fb791d1298e7f396236383bc955f916c1/src/isomorphic/classic/element/ReactElement.js#L21-L25


      var canUseSymbol = typeof Symbol === 'function' && Symbol["for"];
      var REACT_ELEMENT_TYPE = canUseSymbol ? Symbol["for"]('react.element') : 0xeac7;

      function isReactElement(value) {
        return value.$$typeof === REACT_ELEMENT_TYPE;
      }

      function emptyTarget(val) {
        return Array.isArray(val) ? [] : {};
      }

      function cloneUnlessOtherwiseSpecified(value, options) {
        return options.clone !== false && options.isMergeableObject(value) ? deepmerge(emptyTarget(value), value, options) : value;
      }

      function defaultArrayMerge(target, source, options) {
        return target.concat(source).map(function (element) {
          return cloneUnlessOtherwiseSpecified(element, options);
        });
      }

      function getMergeFunction(key, options) {
        if (!options.customMerge) {
          return deepmerge;
        }

        var customMerge = options.customMerge(key);
        return typeof customMerge === 'function' ? customMerge : deepmerge;
      }

      function getEnumerableOwnPropertySymbols(target) {
        return Object.getOwnPropertySymbols ? Object.getOwnPropertySymbols(target).filter(function (symbol) {
          return target.propertyIsEnumerable(symbol);
        }) : [];
      }

      function getKeys(target) {
        return Object.keys(target).concat(getEnumerableOwnPropertySymbols(target));
      } // Protects from prototype poisoning and unexpected merging up the prototype chain.


      function propertyIsUnsafe(target, key) {
        try {
          return key in target && // Properties are safe to merge if they don't exist in the target yet,
          !(Object.hasOwnProperty.call(target, key) // unsafe if they exist up the prototype chain,
          && Object.propertyIsEnumerable.call(target, key)); // and also unsafe if they're nonenumerable.
        } catch (unused) {
          // Counterintuitively, it's safe to merge any property on a target that causes the `in` operator to throw.
          // This happens when trying to copy an object in the source over a plain string in the target.
          return false;
        }
      }

      function mergeObject(target, source, options) {
        var destination = {};

        if (options.isMergeableObject(target)) {
          getKeys(target).forEach(function (key) {
            destination[key] = cloneUnlessOtherwiseSpecified(target[key], options);
          });
        }

        getKeys(source).forEach(function (key) {
          if (propertyIsUnsafe(target, key)) {
            return;
          }

          if (!options.isMergeableObject(source[key]) || !target[key]) {
            destination[key] = cloneUnlessOtherwiseSpecified(source[key], options);
          } else {
            destination[key] = getMergeFunction(key, options)(target[key], source[key], options);
          }
        });
        return destination;
      }

      function deepmerge(target, source, options) {
        options = options || {};
        options.arrayMerge = options.arrayMerge || defaultArrayMerge;
        options.isMergeableObject = options.isMergeableObject || isMergeableObject; // cloneUnlessOtherwiseSpecified is added to `options` so that custom arrayMerge()
        // implementations can use it. The caller may not replace it.

        options.cloneUnlessOtherwiseSpecified = cloneUnlessOtherwiseSpecified;
        var sourceIsArray = Array.isArray(source);
        var targetIsArray = Array.isArray(target);
        var sourceAndTargetTypesMatch = sourceIsArray === targetIsArray;

        if (!sourceAndTargetTypesMatch) {
          return cloneUnlessOtherwiseSpecified(source, options);
        } else if (sourceIsArray) {
          return options.arrayMerge(target, source, options);
        } else {
          return mergeObject(target, source, options);
        }
      }

      deepmerge.all = function deepmergeAll(array, options) {
        if (!Array.isArray(array)) {
          throw new Error('first argument should be an array');
        }

        return array.reduce(function (prev, next) {
          return deepmerge(prev, next, options);
        }, {});
      };

      var deepmerge_1 = deepmerge;
      module.exports = deepmerge_1;
      /***/
    },
    /* 2 */

    /***/
    function (module, __webpack_exports__, __webpack_require__) {
      "use strict";
      /* WEBPACK VAR INJECTION */

      (function (global, module) {
        /* harmony import */
        var _ponyfill_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(4);
        /* global window */


        var root;

        if (typeof self !== 'undefined') {
          root = self;
        } else if (typeof window !== 'undefined') {
          root = window;
        } else if (typeof global !== 'undefined') {
          root = global;
        } else if (true) {
          root = module;
        } else {}

        var result = Object(_ponyfill_js__WEBPACK_IMPORTED_MODULE_0__[
        /* default */
        "a"])(root);
        /* harmony default export */

        __webpack_exports__["a"] = result;
        /* WEBPACK VAR INJECTION */
      }).call(this, __webpack_require__(7), __webpack_require__(8)(module));
      /***/
    },
    /* 3 */

    /***/
    function (module, exports, __webpack_require__) {
      /*!
       * Fuse.js v3.4.2 - Lightweight fuzzy-search (http://fusejs.io)
       * 
       * Copyright (c) 2012-2017 Kirollos Risk (http://kiro.me)
       * All Rights Reserved. Apache Software License 2.0
       * 
       * http://www.apache.org/licenses/LICENSE-2.0
       */
      (function webpackUniversalModuleDefinition(root, factory) {
        if (true) module.exports = factory();else {}
      })(this, function () {
        return (
          /******/
          function (modules) {
            // webpackBootstrap

            /******/
            // The module cache

            /******/
            var installedModules = {};
            /******/

            /******/
            // The require function

            /******/

            function __webpack_require__(moduleId) {
              /******/

              /******/
              // Check if module is in cache

              /******/
              if (installedModules[moduleId]) {
                /******/
                return installedModules[moduleId].exports;
                /******/
              }
              /******/
              // Create a new module (and put it into the cache)

              /******/


              var module = installedModules[moduleId] = {
                /******/
                i: moduleId,

                /******/
                l: false,

                /******/
                exports: {}
                /******/

              };
              /******/

              /******/
              // Execute the module function

              /******/

              modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
              /******/

              /******/
              // Flag the module as loaded

              /******/

              module.l = true;
              /******/

              /******/
              // Return the exports of the module

              /******/

              return module.exports;
              /******/
            }
            /******/

            /******/

            /******/
            // expose the modules object (__webpack_modules__)

            /******/


            __webpack_require__.m = modules;
            /******/

            /******/
            // expose the module cache

            /******/

            __webpack_require__.c = installedModules;
            /******/

            /******/
            // define getter function for harmony exports

            /******/

            __webpack_require__.d = function (exports, name, getter) {
              /******/
              if (!__webpack_require__.o(exports, name)) {
                /******/
                Object.defineProperty(exports, name, {
                  enumerable: true,
                  get: getter
                });
                /******/
              }
              /******/

            };
            /******/

            /******/
            // define __esModule on exports

            /******/


            __webpack_require__.r = function (exports) {
              /******/
              if (typeof Symbol !== 'undefined' && Symbol.toStringTag) {
                /******/
                Object.defineProperty(exports, Symbol.toStringTag, {
                  value: 'Module'
                });
                /******/
              }
              /******/


              Object.defineProperty(exports, '__esModule', {
                value: true
              });
              /******/
            };
            /******/

            /******/
            // create a fake namespace object

            /******/
            // mode & 1: value is a module id, require it

            /******/
            // mode & 2: merge all properties of value into the ns

            /******/
            // mode & 4: return value when already ns object

            /******/
            // mode & 8|1: behave like require

            /******/


            __webpack_require__.t = function (value, mode) {
              /******/
              if (mode & 1) value = __webpack_require__(value);
              /******/

              if (mode & 8) return value;
              /******/

              if (mode & 4 && _typeof2(value) === 'object' && value && value.__esModule) return value;
              /******/

              var ns = Object.create(null);
              /******/

              __webpack_require__.r(ns);
              /******/


              Object.defineProperty(ns, 'default', {
                enumerable: true,
                value: value
              });
              /******/

              if (mode & 2 && typeof value != 'string') for (var key in value) {
                __webpack_require__.d(ns, key, function (key) {
                  return value[key];
                }.bind(null, key));
              }
              /******/

              return ns;
              /******/
            };
            /******/

            /******/
            // getDefaultExport function for compatibility with non-harmony modules

            /******/


            __webpack_require__.n = function (module) {
              /******/
              var getter = module && module.__esModule ?
              /******/
              function getDefault() {
                return module['default'];
              } :
              /******/
              function getModuleExports() {
                return module;
              };
              /******/

              __webpack_require__.d(getter, 'a', getter);
              /******/


              return getter;
              /******/
            };
            /******/

            /******/
            // Object.prototype.hasOwnProperty.call

            /******/


            __webpack_require__.o = function (object, property) {
              return Object.prototype.hasOwnProperty.call(object, property);
            };
            /******/

            /******/
            // __webpack_public_path__

            /******/


            __webpack_require__.p = "";
            /******/

            /******/

            /******/
            // Load entry module and return exports

            /******/

            return __webpack_require__(__webpack_require__.s = "./src/index.js");
            /******/
          }(
          /************************************************************************/

          /******/
          {
            /***/
            "./src/bitap/bitap_matched_indices.js":
            /*!********************************************!*\
              !*** ./src/bitap/bitap_matched_indices.js ***!
              \********************************************/

            /*! no static exports found */

            /***/
            function srcBitapBitap_matched_indicesJs(module, exports) {
              module.exports = function () {
                var matchmask = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
                var minMatchCharLength = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 1;
                var matchedIndices = [];
                var start = -1;
                var end = -1;
                var i = 0;

                for (var len = matchmask.length; i < len; i += 1) {
                  var match = matchmask[i];

                  if (match && start === -1) {
                    start = i;
                  } else if (!match && start !== -1) {
                    end = i - 1;

                    if (end - start + 1 >= minMatchCharLength) {
                      matchedIndices.push([start, end]);
                    }

                    start = -1;
                  }
                } // (i-1 - start) + 1 => i - start


                if (matchmask[i - 1] && i - start >= minMatchCharLength) {
                  matchedIndices.push([start, i - 1]);
                }

                return matchedIndices;
              };
              /***/

            },

            /***/
            "./src/bitap/bitap_pattern_alphabet.js":
            /*!*********************************************!*\
              !*** ./src/bitap/bitap_pattern_alphabet.js ***!
              \*********************************************/

            /*! no static exports found */

            /***/
            function srcBitapBitap_pattern_alphabetJs(module, exports) {
              module.exports = function (pattern) {
                var mask = {};
                var len = pattern.length;

                for (var i = 0; i < len; i += 1) {
                  mask[pattern.charAt(i)] = 0;
                }

                for (var _i = 0; _i < len; _i += 1) {
                  mask[pattern.charAt(_i)] |= 1 << len - _i - 1;
                }

                return mask;
              };
              /***/

            },

            /***/
            "./src/bitap/bitap_regex_search.js":
            /*!*****************************************!*\
              !*** ./src/bitap/bitap_regex_search.js ***!
              \*****************************************/

            /*! no static exports found */

            /***/
            function srcBitapBitap_regex_searchJs(module, exports) {
              var SPECIAL_CHARS_REGEX = /[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g;

              module.exports = function (text, pattern) {
                var tokenSeparator = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : / +/g;
                var regex = new RegExp(pattern.replace(SPECIAL_CHARS_REGEX, '\\$&').replace(tokenSeparator, '|'));
                var matches = text.match(regex);
                var isMatch = !!matches;
                var matchedIndices = [];

                if (isMatch) {
                  for (var i = 0, matchesLen = matches.length; i < matchesLen; i += 1) {
                    var match = matches[i];
                    matchedIndices.push([text.indexOf(match), match.length - 1]);
                  }
                }

                return {
                  // TODO: revisit this score
                  score: isMatch ? 0.5 : 1,
                  isMatch: isMatch,
                  matchedIndices: matchedIndices
                };
              };
              /***/

            },

            /***/
            "./src/bitap/bitap_score.js":
            /*!**********************************!*\
              !*** ./src/bitap/bitap_score.js ***!
              \**********************************/

            /*! no static exports found */

            /***/
            function srcBitapBitap_scoreJs(module, exports) {
              module.exports = function (pattern, _ref) {
                var _ref$errors = _ref.errors,
                    errors = _ref$errors === void 0 ? 0 : _ref$errors,
                    _ref$currentLocation = _ref.currentLocation,
                    currentLocation = _ref$currentLocation === void 0 ? 0 : _ref$currentLocation,
                    _ref$expectedLocation = _ref.expectedLocation,
                    expectedLocation = _ref$expectedLocation === void 0 ? 0 : _ref$expectedLocation,
                    _ref$distance = _ref.distance,
                    distance = _ref$distance === void 0 ? 100 : _ref$distance;
                var accuracy = errors / pattern.length;
                var proximity = Math.abs(expectedLocation - currentLocation);

                if (!distance) {
                  // Dodge divide by zero error.
                  return proximity ? 1.0 : accuracy;
                }

                return accuracy + proximity / distance;
              };
              /***/

            },

            /***/
            "./src/bitap/bitap_search.js":
            /*!***********************************!*\
              !*** ./src/bitap/bitap_search.js ***!
              \***********************************/

            /*! no static exports found */

            /***/
            function srcBitapBitap_searchJs(module, exports, __webpack_require__) {
              var bitapScore = __webpack_require__(
              /*! ./bitap_score */
              "./src/bitap/bitap_score.js");

              var matchedIndices = __webpack_require__(
              /*! ./bitap_matched_indices */
              "./src/bitap/bitap_matched_indices.js");

              module.exports = function (text, pattern, patternAlphabet, _ref) {
                var _ref$location = _ref.location,
                    location = _ref$location === void 0 ? 0 : _ref$location,
                    _ref$distance = _ref.distance,
                    distance = _ref$distance === void 0 ? 100 : _ref$distance,
                    _ref$threshold = _ref.threshold,
                    threshold = _ref$threshold === void 0 ? 0.6 : _ref$threshold,
                    _ref$findAllMatches = _ref.findAllMatches,
                    findAllMatches = _ref$findAllMatches === void 0 ? false : _ref$findAllMatches,
                    _ref$minMatchCharLeng = _ref.minMatchCharLength,
                    minMatchCharLength = _ref$minMatchCharLeng === void 0 ? 1 : _ref$minMatchCharLeng;
                var expectedLocation = location; // Set starting location at beginning text and initialize the alphabet.

                var textLen = text.length; // Highest score beyond which we give up.

                var currentThreshold = threshold; // Is there a nearby exact match? (speedup)

                var bestLocation = text.indexOf(pattern, expectedLocation);
                var patternLen = pattern.length; // a mask of the matches

                var matchMask = [];

                for (var i = 0; i < textLen; i += 1) {
                  matchMask[i] = 0;
                }

                if (bestLocation !== -1) {
                  var score = bitapScore(pattern, {
                    errors: 0,
                    currentLocation: bestLocation,
                    expectedLocation: expectedLocation,
                    distance: distance
                  });
                  currentThreshold = Math.min(score, currentThreshold); // What about in the other direction? (speed up)

                  bestLocation = text.lastIndexOf(pattern, expectedLocation + patternLen);

                  if (bestLocation !== -1) {
                    var _score = bitapScore(pattern, {
                      errors: 0,
                      currentLocation: bestLocation,
                      expectedLocation: expectedLocation,
                      distance: distance
                    });

                    currentThreshold = Math.min(_score, currentThreshold);
                  }
                } // Reset the best location


                bestLocation = -1;
                var lastBitArr = [];
                var finalScore = 1;
                var binMax = patternLen + textLen;
                var mask = 1 << patternLen - 1;

                for (var _i = 0; _i < patternLen; _i += 1) {
                  // Scan for the best match; each iteration allows for one more error.
                  // Run a binary search to determine how far from the match location we can stray
                  // at this error level.
                  var binMin = 0;
                  var binMid = binMax;

                  while (binMin < binMid) {
                    var _score3 = bitapScore(pattern, {
                      errors: _i,
                      currentLocation: expectedLocation + binMid,
                      expectedLocation: expectedLocation,
                      distance: distance
                    });

                    if (_score3 <= currentThreshold) {
                      binMin = binMid;
                    } else {
                      binMax = binMid;
                    }

                    binMid = Math.floor((binMax - binMin) / 2 + binMin);
                  } // Use the result from this iteration as the maximum for the next.


                  binMax = binMid;
                  var start = Math.max(1, expectedLocation - binMid + 1);
                  var finish = findAllMatches ? textLen : Math.min(expectedLocation + binMid, textLen) + patternLen; // Initialize the bit array

                  var bitArr = Array(finish + 2);
                  bitArr[finish + 1] = (1 << _i) - 1;

                  for (var j = finish; j >= start; j -= 1) {
                    var currentLocation = j - 1;
                    var charMatch = patternAlphabet[text.charAt(currentLocation)];

                    if (charMatch) {
                      matchMask[currentLocation] = 1;
                    } // First pass: exact match


                    bitArr[j] = (bitArr[j + 1] << 1 | 1) & charMatch; // Subsequent passes: fuzzy match

                    if (_i !== 0) {
                      bitArr[j] |= (lastBitArr[j + 1] | lastBitArr[j]) << 1 | 1 | lastBitArr[j + 1];
                    }

                    if (bitArr[j] & mask) {
                      finalScore = bitapScore(pattern, {
                        errors: _i,
                        currentLocation: currentLocation,
                        expectedLocation: expectedLocation,
                        distance: distance
                      }); // This match will almost certainly be better than any existing match.
                      // But check anyway.

                      if (finalScore <= currentThreshold) {
                        // Indeed it is
                        currentThreshold = finalScore;
                        bestLocation = currentLocation; // Already passed `loc`, downhill from here on in.

                        if (bestLocation <= expectedLocation) {
                          break;
                        } // When passing `bestLocation`, don't exceed our current distance from `expectedLocation`.


                        start = Math.max(1, 2 * expectedLocation - bestLocation);
                      }
                    }
                  } // No hope for a (better) match at greater error levels.


                  var _score2 = bitapScore(pattern, {
                    errors: _i + 1,
                    currentLocation: expectedLocation,
                    expectedLocation: expectedLocation,
                    distance: distance
                  }); // console.log('score', score, finalScore)


                  if (_score2 > currentThreshold) {
                    break;
                  }

                  lastBitArr = bitArr;
                } // console.log('FINAL SCORE', finalScore)
                // Count exact matches (those with a score of 0) to be "almost" exact


                return {
                  isMatch: bestLocation >= 0,
                  score: finalScore === 0 ? 0.001 : finalScore,
                  matchedIndices: matchedIndices(matchMask, minMatchCharLength)
                };
              };
              /***/

            },

            /***/
            "./src/bitap/index.js":
            /*!****************************!*\
              !*** ./src/bitap/index.js ***!
              \****************************/

            /*! no static exports found */

            /***/
            function srcBitapIndexJs(module, exports, __webpack_require__) {
              function _classCallCheck(instance, Constructor) {
                if (!(instance instanceof Constructor)) {
                  throw new TypeError("Cannot call a class as a function");
                }
              }

              function _defineProperties(target, props) {
                for (var i = 0; i < props.length; i++) {
                  var descriptor = props[i];
                  descriptor.enumerable = descriptor.enumerable || false;
                  descriptor.configurable = true;
                  if ("value" in descriptor) descriptor.writable = true;
                  Object.defineProperty(target, descriptor.key, descriptor);
                }
              }

              function _createClass(Constructor, protoProps, staticProps) {
                if (protoProps) _defineProperties(Constructor.prototype, protoProps);
                if (staticProps) _defineProperties(Constructor, staticProps);
                return Constructor;
              }

              var bitapRegexSearch = __webpack_require__(
              /*! ./bitap_regex_search */
              "./src/bitap/bitap_regex_search.js");

              var bitapSearch = __webpack_require__(
              /*! ./bitap_search */
              "./src/bitap/bitap_search.js");

              var patternAlphabet = __webpack_require__(
              /*! ./bitap_pattern_alphabet */
              "./src/bitap/bitap_pattern_alphabet.js");

              var Bitap = /*#__PURE__*/function () {
                function Bitap(pattern, _ref) {
                  var _ref$location = _ref.location,
                      location = _ref$location === void 0 ? 0 : _ref$location,
                      _ref$distance = _ref.distance,
                      distance = _ref$distance === void 0 ? 100 : _ref$distance,
                      _ref$threshold = _ref.threshold,
                      threshold = _ref$threshold === void 0 ? 0.6 : _ref$threshold,
                      _ref$maxPatternLength = _ref.maxPatternLength,
                      maxPatternLength = _ref$maxPatternLength === void 0 ? 32 : _ref$maxPatternLength,
                      _ref$isCaseSensitive = _ref.isCaseSensitive,
                      isCaseSensitive = _ref$isCaseSensitive === void 0 ? false : _ref$isCaseSensitive,
                      _ref$tokenSeparator = _ref.tokenSeparator,
                      tokenSeparator = _ref$tokenSeparator === void 0 ? / +/g : _ref$tokenSeparator,
                      _ref$findAllMatches = _ref.findAllMatches,
                      findAllMatches = _ref$findAllMatches === void 0 ? false : _ref$findAllMatches,
                      _ref$minMatchCharLeng = _ref.minMatchCharLength,
                      minMatchCharLength = _ref$minMatchCharLeng === void 0 ? 1 : _ref$minMatchCharLeng;

                  _classCallCheck(this, Bitap);

                  this.options = {
                    location: location,
                    distance: distance,
                    threshold: threshold,
                    maxPatternLength: maxPatternLength,
                    isCaseSensitive: isCaseSensitive,
                    tokenSeparator: tokenSeparator,
                    findAllMatches: findAllMatches,
                    minMatchCharLength: minMatchCharLength
                  };
                  this.pattern = this.options.isCaseSensitive ? pattern : pattern.toLowerCase();

                  if (this.pattern.length <= maxPatternLength) {
                    this.patternAlphabet = patternAlphabet(this.pattern);
                  }
                }

                _createClass(Bitap, [{
                  key: "search",
                  value: function search(text) {
                    if (!this.options.isCaseSensitive) {
                      text = text.toLowerCase();
                    } // Exact match


                    if (this.pattern === text) {
                      return {
                        isMatch: true,
                        score: 0,
                        matchedIndices: [[0, text.length - 1]]
                      };
                    } // When pattern length is greater than the machine word length, just do a a regex comparison


                    var _this$options = this.options,
                        maxPatternLength = _this$options.maxPatternLength,
                        tokenSeparator = _this$options.tokenSeparator;

                    if (this.pattern.length > maxPatternLength) {
                      return bitapRegexSearch(text, this.pattern, tokenSeparator);
                    } // Otherwise, use Bitap algorithm


                    var _this$options2 = this.options,
                        location = _this$options2.location,
                        distance = _this$options2.distance,
                        threshold = _this$options2.threshold,
                        findAllMatches = _this$options2.findAllMatches,
                        minMatchCharLength = _this$options2.minMatchCharLength;
                    return bitapSearch(text, this.pattern, this.patternAlphabet, {
                      location: location,
                      distance: distance,
                      threshold: threshold,
                      findAllMatches: findAllMatches,
                      minMatchCharLength: minMatchCharLength
                    });
                  }
                }]);

                return Bitap;
              }(); // let x = new Bitap("od mn war", {})
              // let result = x.search("Old Man's War")
              // console.log(result)


              module.exports = Bitap;
              /***/
            },

            /***/
            "./src/helpers/deep_value.js":
            /*!***********************************!*\
              !*** ./src/helpers/deep_value.js ***!
              \***********************************/

            /*! no static exports found */

            /***/
            function srcHelpersDeep_valueJs(module, exports, __webpack_require__) {
              var isArray = __webpack_require__(
              /*! ./is_array */
              "./src/helpers/is_array.js");

              var deepValue = function deepValue(obj, path, list) {
                if (!path) {
                  // If there's no path left, we've gotten to the object we care about.
                  list.push(obj);
                } else {
                  var dotIndex = path.indexOf('.');
                  var firstSegment = path;
                  var remaining = null;

                  if (dotIndex !== -1) {
                    firstSegment = path.slice(0, dotIndex);
                    remaining = path.slice(dotIndex + 1);
                  }

                  var value = obj[firstSegment];

                  if (value !== null && value !== undefined) {
                    if (!remaining && (typeof value === 'string' || typeof value === 'number')) {
                      list.push(value.toString());
                    } else if (isArray(value)) {
                      // Search each item in the array.
                      for (var i = 0, len = value.length; i < len; i += 1) {
                        deepValue(value[i], remaining, list);
                      }
                    } else if (remaining) {
                      // An object. Recurse further.
                      deepValue(value, remaining, list);
                    }
                  }
                }

                return list;
              };

              module.exports = function (obj, path) {
                return deepValue(obj, path, []);
              };
              /***/

            },

            /***/
            "./src/helpers/is_array.js":
            /*!*********************************!*\
              !*** ./src/helpers/is_array.js ***!
              \*********************************/

            /*! no static exports found */

            /***/
            function srcHelpersIs_arrayJs(module, exports) {
              module.exports = function (obj) {
                return !Array.isArray ? Object.prototype.toString.call(obj) === '[object Array]' : Array.isArray(obj);
              };
              /***/

            },

            /***/
            "./src/index.js":
            /*!**********************!*\
              !*** ./src/index.js ***!
              \**********************/

            /*! no static exports found */

            /***/
            function srcIndexJs(module, exports, __webpack_require__) {
              function _typeof(obj) {
                if (typeof Symbol === "function" && _typeof2(Symbol.iterator) === "symbol") {
                  _typeof = function _typeof(obj) {
                    return _typeof2(obj);
                  };
                } else {
                  _typeof = function _typeof(obj) {
                    return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : _typeof2(obj);
                  };
                }

                return _typeof(obj);
              }

              function _classCallCheck(instance, Constructor) {
                if (!(instance instanceof Constructor)) {
                  throw new TypeError("Cannot call a class as a function");
                }
              }

              function _defineProperties(target, props) {
                for (var i = 0; i < props.length; i++) {
                  var descriptor = props[i];
                  descriptor.enumerable = descriptor.enumerable || false;
                  descriptor.configurable = true;
                  if ("value" in descriptor) descriptor.writable = true;
                  Object.defineProperty(target, descriptor.key, descriptor);
                }
              }

              function _createClass(Constructor, protoProps, staticProps) {
                if (protoProps) _defineProperties(Constructor.prototype, protoProps);
                if (staticProps) _defineProperties(Constructor, staticProps);
                return Constructor;
              }

              var Bitap = __webpack_require__(
              /*! ./bitap */
              "./src/bitap/index.js");

              var deepValue = __webpack_require__(
              /*! ./helpers/deep_value */
              "./src/helpers/deep_value.js");

              var isArray = __webpack_require__(
              /*! ./helpers/is_array */
              "./src/helpers/is_array.js");

              var Fuse = /*#__PURE__*/function () {
                function Fuse(list, _ref) {
                  var _ref$location = _ref.location,
                      location = _ref$location === void 0 ? 0 : _ref$location,
                      _ref$distance = _ref.distance,
                      distance = _ref$distance === void 0 ? 100 : _ref$distance,
                      _ref$threshold = _ref.threshold,
                      threshold = _ref$threshold === void 0 ? 0.6 : _ref$threshold,
                      _ref$maxPatternLength = _ref.maxPatternLength,
                      maxPatternLength = _ref$maxPatternLength === void 0 ? 32 : _ref$maxPatternLength,
                      _ref$caseSensitive = _ref.caseSensitive,
                      caseSensitive = _ref$caseSensitive === void 0 ? false : _ref$caseSensitive,
                      _ref$tokenSeparator = _ref.tokenSeparator,
                      tokenSeparator = _ref$tokenSeparator === void 0 ? / +/g : _ref$tokenSeparator,
                      _ref$findAllMatches = _ref.findAllMatches,
                      findAllMatches = _ref$findAllMatches === void 0 ? false : _ref$findAllMatches,
                      _ref$minMatchCharLeng = _ref.minMatchCharLength,
                      minMatchCharLength = _ref$minMatchCharLeng === void 0 ? 1 : _ref$minMatchCharLeng,
                      _ref$id = _ref.id,
                      id = _ref$id === void 0 ? null : _ref$id,
                      _ref$keys = _ref.keys,
                      keys = _ref$keys === void 0 ? [] : _ref$keys,
                      _ref$shouldSort = _ref.shouldSort,
                      shouldSort = _ref$shouldSort === void 0 ? true : _ref$shouldSort,
                      _ref$getFn = _ref.getFn,
                      getFn = _ref$getFn === void 0 ? deepValue : _ref$getFn,
                      _ref$sortFn = _ref.sortFn,
                      sortFn = _ref$sortFn === void 0 ? function (a, b) {
                    return a.score - b.score;
                  } : _ref$sortFn,
                      _ref$tokenize = _ref.tokenize,
                      tokenize = _ref$tokenize === void 0 ? false : _ref$tokenize,
                      _ref$matchAllTokens = _ref.matchAllTokens,
                      matchAllTokens = _ref$matchAllTokens === void 0 ? false : _ref$matchAllTokens,
                      _ref$includeMatches = _ref.includeMatches,
                      includeMatches = _ref$includeMatches === void 0 ? false : _ref$includeMatches,
                      _ref$includeScore = _ref.includeScore,
                      includeScore = _ref$includeScore === void 0 ? false : _ref$includeScore,
                      _ref$verbose = _ref.verbose,
                      verbose = _ref$verbose === void 0 ? false : _ref$verbose;

                  _classCallCheck(this, Fuse);

                  this.options = {
                    location: location,
                    distance: distance,
                    threshold: threshold,
                    maxPatternLength: maxPatternLength,
                    isCaseSensitive: caseSensitive,
                    tokenSeparator: tokenSeparator,
                    findAllMatches: findAllMatches,
                    minMatchCharLength: minMatchCharLength,
                    id: id,
                    keys: keys,
                    includeMatches: includeMatches,
                    includeScore: includeScore,
                    shouldSort: shouldSort,
                    getFn: getFn,
                    sortFn: sortFn,
                    verbose: verbose,
                    tokenize: tokenize,
                    matchAllTokens: matchAllTokens
                  };
                  this.setCollection(list);
                }

                _createClass(Fuse, [{
                  key: "setCollection",
                  value: function setCollection(list) {
                    this.list = list;
                    return list;
                  }
                }, {
                  key: "search",
                  value: function search(pattern) {
                    var opts = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {
                      limit: false
                    };

                    this._log("---------\nSearch pattern: \"".concat(pattern, "\""));

                    var _this$_prepareSearche = this._prepareSearchers(pattern),
                        tokenSearchers = _this$_prepareSearche.tokenSearchers,
                        fullSearcher = _this$_prepareSearche.fullSearcher;

                    var _this$_search = this._search(tokenSearchers, fullSearcher),
                        weights = _this$_search.weights,
                        results = _this$_search.results;

                    this._computeScore(weights, results);

                    if (this.options.shouldSort) {
                      this._sort(results);
                    }

                    if (opts.limit && typeof opts.limit === 'number') {
                      results = results.slice(0, opts.limit);
                    }

                    return this._format(results);
                  }
                }, {
                  key: "_prepareSearchers",
                  value: function _prepareSearchers() {
                    var pattern = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
                    var tokenSearchers = [];

                    if (this.options.tokenize) {
                      // Tokenize on the separator
                      var tokens = pattern.split(this.options.tokenSeparator);

                      for (var i = 0, len = tokens.length; i < len; i += 1) {
                        tokenSearchers.push(new Bitap(tokens[i], this.options));
                      }
                    }

                    var fullSearcher = new Bitap(pattern, this.options);
                    return {
                      tokenSearchers: tokenSearchers,
                      fullSearcher: fullSearcher
                    };
                  }
                }, {
                  key: "_search",
                  value: function _search() {
                    var tokenSearchers = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
                    var fullSearcher = arguments.length > 1 ? arguments[1] : undefined;
                    var list = this.list;
                    var resultMap = {};
                    var results = []; // Check the first item in the list, if it's a string, then we assume
                    // that every item in the list is also a string, and thus it's a flattened array.

                    if (typeof list[0] === 'string') {
                      // Iterate over every item
                      for (var i = 0, len = list.length; i < len; i += 1) {
                        this._analyze({
                          key: '',
                          value: list[i],
                          record: i,
                          index: i
                        }, {
                          resultMap: resultMap,
                          results: results,
                          tokenSearchers: tokenSearchers,
                          fullSearcher: fullSearcher
                        });
                      }

                      return {
                        weights: null,
                        results: results
                      };
                    } // Otherwise, the first item is an Object (hopefully), and thus the searching
                    // is done on the values of the keys of each item.


                    var weights = {};

                    for (var _i = 0, _len = list.length; _i < _len; _i += 1) {
                      var item = list[_i]; // Iterate over every key

                      for (var j = 0, keysLen = this.options.keys.length; j < keysLen; j += 1) {
                        var key = this.options.keys[j];

                        if (typeof key !== 'string') {
                          weights[key.name] = {
                            weight: 1 - key.weight || 1
                          };

                          if (key.weight <= 0 || key.weight > 1) {
                            throw new Error('Key weight has to be > 0 and <= 1');
                          }

                          key = key.name;
                        } else {
                          weights[key] = {
                            weight: 1
                          };
                        }

                        this._analyze({
                          key: key,
                          value: this.options.getFn(item, key),
                          record: item,
                          index: _i
                        }, {
                          resultMap: resultMap,
                          results: results,
                          tokenSearchers: tokenSearchers,
                          fullSearcher: fullSearcher
                        });
                      }
                    }

                    return {
                      weights: weights,
                      results: results
                    };
                  }
                }, {
                  key: "_analyze",
                  value: function _analyze(_ref2, _ref3) {
                    var key = _ref2.key,
                        _ref2$arrayIndex = _ref2.arrayIndex,
                        arrayIndex = _ref2$arrayIndex === void 0 ? -1 : _ref2$arrayIndex,
                        value = _ref2.value,
                        record = _ref2.record,
                        index = _ref2.index;
                    var _ref3$tokenSearchers = _ref3.tokenSearchers,
                        tokenSearchers = _ref3$tokenSearchers === void 0 ? [] : _ref3$tokenSearchers,
                        _ref3$fullSearcher = _ref3.fullSearcher,
                        fullSearcher = _ref3$fullSearcher === void 0 ? [] : _ref3$fullSearcher,
                        _ref3$resultMap = _ref3.resultMap,
                        resultMap = _ref3$resultMap === void 0 ? {} : _ref3$resultMap,
                        _ref3$results = _ref3.results,
                        results = _ref3$results === void 0 ? [] : _ref3$results; // Check if the texvaluet can be searched

                    if (value === undefined || value === null) {
                      return;
                    }

                    var exists = false;
                    var averageScore = -1;
                    var numTextMatches = 0;

                    if (typeof value === 'string') {
                      this._log("\nKey: ".concat(key === '' ? '-' : key));

                      var mainSearchResult = fullSearcher.search(value);

                      this._log("Full text: \"".concat(value, "\", score: ").concat(mainSearchResult.score));

                      if (this.options.tokenize) {
                        var words = value.split(this.options.tokenSeparator);
                        var scores = [];

                        for (var i = 0; i < tokenSearchers.length; i += 1) {
                          var tokenSearcher = tokenSearchers[i];

                          this._log("\nPattern: \"".concat(tokenSearcher.pattern, "\"")); // let tokenScores = []


                          var hasMatchInText = false;

                          for (var j = 0; j < words.length; j += 1) {
                            var word = words[j];
                            var tokenSearchResult = tokenSearcher.search(word);
                            var obj = {};

                            if (tokenSearchResult.isMatch) {
                              obj[word] = tokenSearchResult.score;
                              exists = true;
                              hasMatchInText = true;
                              scores.push(tokenSearchResult.score);
                            } else {
                              obj[word] = 1;

                              if (!this.options.matchAllTokens) {
                                scores.push(1);
                              }
                            }

                            this._log("Token: \"".concat(word, "\", score: ").concat(obj[word])); // tokenScores.push(obj)

                          }

                          if (hasMatchInText) {
                            numTextMatches += 1;
                          }
                        }

                        averageScore = scores[0];
                        var scoresLen = scores.length;

                        for (var _i2 = 1; _i2 < scoresLen; _i2 += 1) {
                          averageScore += scores[_i2];
                        }

                        averageScore = averageScore / scoresLen;

                        this._log('Token score average:', averageScore);
                      }

                      var finalScore = mainSearchResult.score;

                      if (averageScore > -1) {
                        finalScore = (finalScore + averageScore) / 2;
                      }

                      this._log('Score average:', finalScore);

                      var checkTextMatches = this.options.tokenize && this.options.matchAllTokens ? numTextMatches >= tokenSearchers.length : true;

                      this._log("\nCheck Matches: ".concat(checkTextMatches)); // If a match is found, add the item to <rawResults>, including its score


                      if ((exists || mainSearchResult.isMatch) && checkTextMatches) {
                        // Check if the item already exists in our results
                        var existingResult = resultMap[index];

                        if (existingResult) {
                          // Use the lowest score
                          // existingResult.score, bitapResult.score
                          existingResult.output.push({
                            key: key,
                            arrayIndex: arrayIndex,
                            value: value,
                            score: finalScore,
                            matchedIndices: mainSearchResult.matchedIndices
                          });
                        } else {
                          // Add it to the raw result list
                          resultMap[index] = {
                            item: record,
                            output: [{
                              key: key,
                              arrayIndex: arrayIndex,
                              value: value,
                              score: finalScore,
                              matchedIndices: mainSearchResult.matchedIndices
                            }]
                          };
                          results.push(resultMap[index]);
                        }
                      }
                    } else if (isArray(value)) {
                      for (var _i3 = 0, len = value.length; _i3 < len; _i3 += 1) {
                        this._analyze({
                          key: key,
                          arrayIndex: _i3,
                          value: value[_i3],
                          record: record,
                          index: index
                        }, {
                          resultMap: resultMap,
                          results: results,
                          tokenSearchers: tokenSearchers,
                          fullSearcher: fullSearcher
                        });
                      }
                    }
                  }
                }, {
                  key: "_computeScore",
                  value: function _computeScore(weights, results) {
                    this._log('\n\nComputing score:\n');

                    for (var i = 0, len = results.length; i < len; i += 1) {
                      var output = results[i].output;
                      var scoreLen = output.length;
                      var currScore = 1;
                      var bestScore = 1;

                      for (var j = 0; j < scoreLen; j += 1) {
                        var weight = weights ? weights[output[j].key].weight : 1;
                        var score = weight === 1 ? output[j].score : output[j].score || 0.001;
                        var nScore = score * weight;

                        if (weight !== 1) {
                          bestScore = Math.min(bestScore, nScore);
                        } else {
                          output[j].nScore = nScore;
                          currScore *= nScore;
                        }
                      }

                      results[i].score = bestScore === 1 ? currScore : bestScore;

                      this._log(results[i]);
                    }
                  }
                }, {
                  key: "_sort",
                  value: function _sort(results) {
                    this._log('\n\nSorting....');

                    results.sort(this.options.sortFn);
                  }
                }, {
                  key: "_format",
                  value: function _format(results) {
                    var finalOutput = [];

                    if (this.options.verbose) {
                      var cache = [];

                      this._log('\n\nOutput:\n\n', JSON.stringify(results, function (key, value) {
                        if (_typeof(value) === 'object' && value !== null) {
                          if (cache.indexOf(value) !== -1) {
                            // Circular reference found, discard key
                            return;
                          } // Store value in our collection


                          cache.push(value);
                        }

                        return value;
                      }));

                      cache = null;
                    }

                    var transformers = [];

                    if (this.options.includeMatches) {
                      transformers.push(function (result, data) {
                        var output = result.output;
                        data.matches = [];

                        for (var i = 0, len = output.length; i < len; i += 1) {
                          var item = output[i];

                          if (item.matchedIndices.length === 0) {
                            continue;
                          }

                          var obj = {
                            indices: item.matchedIndices,
                            value: item.value
                          };

                          if (item.key) {
                            obj.key = item.key;
                          }

                          if (item.hasOwnProperty('arrayIndex') && item.arrayIndex > -1) {
                            obj.arrayIndex = item.arrayIndex;
                          }

                          data.matches.push(obj);
                        }
                      });
                    }

                    if (this.options.includeScore) {
                      transformers.push(function (result, data) {
                        data.score = result.score;
                      });
                    }

                    for (var i = 0, len = results.length; i < len; i += 1) {
                      var result = results[i];

                      if (this.options.id) {
                        result.item = this.options.getFn(result.item, this.options.id)[0];
                      }

                      if (!transformers.length) {
                        finalOutput.push(result.item);
                        continue;
                      }

                      var data = {
                        item: result.item
                      };

                      for (var j = 0, _len2 = transformers.length; j < _len2; j += 1) {
                        transformers[j](result, data);
                      }

                      finalOutput.push(data);
                    }

                    return finalOutput;
                  }
                }, {
                  key: "_log",
                  value: function _log() {
                    if (this.options.verbose) {
                      var _console;

                      (_console = console).log.apply(_console, arguments);
                    }
                  }
                }]);

                return Fuse;
              }();

              module.exports = Fuse;
              /***/
            }
            /******/

          })
        );
      });
      /***/

    },
    /* 4 */

    /***/
    function (module, __webpack_exports__, __webpack_require__) {
      "use strict";
      /* harmony export (binding) */

      __webpack_require__.d(__webpack_exports__, "a", function () {
        return symbolObservablePonyfill;
      });

      function symbolObservablePonyfill(root) {
        var result;
        var _Symbol = root.Symbol;

        if (typeof _Symbol === 'function') {
          if (_Symbol.observable) {
            result = _Symbol.observable;
          } else {
            result = _Symbol('observable');
            _Symbol.observable = result;
          }
        } else {
          result = '@@observable';
        }

        return result;
      }

      ;
      /***/
    },
    /* 5 */

    /***/
    function (module, exports, __webpack_require__) {
      module.exports = __webpack_require__(9);
      /***/
    },
    /* 6 */

    /***/
    function (module, exports) {
      window.delegateEvent = function delegateEvent() {
        var events;
        var addedListenerTypes;

        if (typeof events === 'undefined') {
          events = new Map();
        }

        if (typeof addedListenerTypes === 'undefined') {
          addedListenerTypes = [];
        }

        function _callback(event) {
          var type = events.get(event.type);
          if (!type) return;
          type.forEach(function (fn) {
            return fn(event);
          });
        }

        return {
          add: function add(type, fn) {
            // Cache list of events.
            if (events.has(type)) {
              events.get(type).push(fn);
            } else {
              events.set(type, [fn]);
            } // Setup events.


            if (addedListenerTypes.indexOf(type) === -1) {
              document.documentElement.addEventListener(type, _callback, true);
              addedListenerTypes.push(type);
            }
          },
          remove: function remove(type, fn) {
            if (!events.get(type)) return;
            events.set(type, events.get(type).filter(function (item) {
              return item !== fn;
            }));

            if (!events.get(type).length) {
              addedListenerTypes.splice(addedListenerTypes.indexOf(type), 1);
            }
          }
        };
      }();
      /***/

    },
    /* 7 */

    /***/
    function (module, exports) {
      var g; // This works in non-strict mode

      g = function () {
        return this;
      }();

      try {
        // This works if eval is allowed (see CSP)
        g = g || new Function("return this")();
      } catch (e) {
        // This works if the window reference is available
        if ((typeof window === "undefined" ? "undefined" : _typeof2(window)) === "object") g = window;
      } // g can still be undefined, but nothing to do about it...
      // We return undefined, instead of nothing here, so it's
      // easier to handle this case. if(!global) { ...}


      module.exports = g;
      /***/
    },
    /* 8 */

    /***/
    function (module, exports) {
      module.exports = function (originalModule) {
        if (!originalModule.webpackPolyfill) {
          var module = Object.create(originalModule); // module.parent = undefined by default

          if (!module.children) module.children = [];
          Object.defineProperty(module, "loaded", {
            enumerable: true,
            get: function get() {
              return module.l;
            }
          });
          Object.defineProperty(module, "id", {
            enumerable: true,
            get: function get() {
              return module.i;
            }
          });
          Object.defineProperty(module, "exports", {
            enumerable: true
          });
          module.webpackPolyfill = 1;
        }

        return module;
      };
      /***/

    },
    /* 9 */

    /***/
    function (module, __webpack_exports__, __webpack_require__) {
      "use strict";

      __webpack_require__.r(__webpack_exports__); // EXTERNAL MODULE: ./node_modules/fuse.js/dist/fuse.js


      var dist_fuse = __webpack_require__(3);

      var fuse_default = /*#__PURE__*/__webpack_require__.n(dist_fuse); // EXTERNAL MODULE: ./node_modules/deepmerge/dist/cjs.js


      var cjs = __webpack_require__(1);

      var cjs_default = /*#__PURE__*/__webpack_require__.n(cjs); // EXTERNAL MODULE: ./src/scripts/lib/delegate-events.js


      var delegate_events = __webpack_require__(6); // EXTERNAL MODULE: ./node_modules/symbol-observable/es/index.js


      var es = __webpack_require__(2); // CONCATENATED MODULE: ./node_modules/redux/es/redux.js

      /**
       * These are private action types reserved by Redux.
       * For any unknown actions, you must return the current state.
       * If the current state is undefined, you must return the initial state.
       * Do not reference these action types directly in your code.
       */


      var randomString = function randomString() {
        return Math.random().toString(36).substring(7).split('').join('.');
      };

      var ActionTypes = {
        INIT: "@@redux/INIT" + randomString(),
        REPLACE: "@@redux/REPLACE" + randomString(),
        PROBE_UNKNOWN_ACTION: function PROBE_UNKNOWN_ACTION() {
          return "@@redux/PROBE_UNKNOWN_ACTION" + randomString();
        }
      };
      /**
       * @param {any} obj The object to inspect.
       * @returns {boolean} True if the argument appears to be a plain object.
       */

      function isPlainObject(obj) {
        if (_typeof2(obj) !== 'object' || obj === null) return false;
        var proto = obj;

        while (Object.getPrototypeOf(proto) !== null) {
          proto = Object.getPrototypeOf(proto);
        }

        return Object.getPrototypeOf(obj) === proto;
      }
      /**
       * Creates a Redux store that holds the state tree.
       * The only way to change the data in the store is to call `dispatch()` on it.
       *
       * There should only be a single store in your app. To specify how different
       * parts of the state tree respond to actions, you may combine several reducers
       * into a single reducer function by using `combineReducers`.
       *
       * @param {Function} reducer A function that returns the next state tree, given
       * the current state tree and the action to handle.
       *
       * @param {any} [preloadedState] The initial state. You may optionally specify it
       * to hydrate the state from the server in universal apps, or to restore a
       * previously serialized user session.
       * If you use `combineReducers` to produce the root reducer function, this must be
       * an object with the same shape as `combineReducers` keys.
       *
       * @param {Function} [enhancer] The store enhancer. You may optionally specify it
       * to enhance the store with third-party capabilities such as middleware,
       * time travel, persistence, etc. The only store enhancer that ships with Redux
       * is `applyMiddleware()`.
       *
       * @returns {Store} A Redux store that lets you read the state, dispatch actions
       * and subscribe to changes.
       */


      function createStore(reducer, preloadedState, enhancer) {
        var _ref2;

        if (typeof preloadedState === 'function' && typeof enhancer === 'function' || typeof enhancer === 'function' && typeof arguments[3] === 'function') {
          throw new Error('It looks like you are passing several store enhancers to ' + 'createStore(). This is not supported. Instead, compose them ' + 'together to a single function.');
        }

        if (typeof preloadedState === 'function' && typeof enhancer === 'undefined') {
          enhancer = preloadedState;
          preloadedState = undefined;
        }

        if (typeof enhancer !== 'undefined') {
          if (typeof enhancer !== 'function') {
            throw new Error('Expected the enhancer to be a function.');
          }

          return enhancer(createStore)(reducer, preloadedState);
        }

        if (typeof reducer !== 'function') {
          throw new Error('Expected the reducer to be a function.');
        }

        var currentReducer = reducer;
        var currentState = preloadedState;
        var currentListeners = [];
        var nextListeners = currentListeners;
        var isDispatching = false;
        /**
         * This makes a shallow copy of currentListeners so we can use
         * nextListeners as a temporary list while dispatching.
         *
         * This prevents any bugs around consumers calling
         * subscribe/unsubscribe in the middle of a dispatch.
         */

        function ensureCanMutateNextListeners() {
          if (nextListeners === currentListeners) {
            nextListeners = currentListeners.slice();
          }
        }
        /**
         * Reads the state tree managed by the store.
         *
         * @returns {any} The current state tree of your application.
         */


        function getState() {
          if (isDispatching) {
            throw new Error('You may not call store.getState() while the reducer is executing. ' + 'The reducer has already received the state as an argument. ' + 'Pass it down from the top reducer instead of reading it from the store.');
          }

          return currentState;
        }
        /**
         * Adds a change listener. It will be called any time an action is dispatched,
         * and some part of the state tree may potentially have changed. You may then
         * call `getState()` to read the current state tree inside the callback.
         *
         * You may call `dispatch()` from a change listener, with the following
         * caveats:
         *
         * 1. The subscriptions are snapshotted just before every `dispatch()` call.
         * If you subscribe or unsubscribe while the listeners are being invoked, this
         * will not have any effect on the `dispatch()` that is currently in progress.
         * However, the next `dispatch()` call, whether nested or not, will use a more
         * recent snapshot of the subscription list.
         *
         * 2. The listener should not expect to see all state changes, as the state
         * might have been updated multiple times during a nested `dispatch()` before
         * the listener is called. It is, however, guaranteed that all subscribers
         * registered before the `dispatch()` started will be called with the latest
         * state by the time it exits.
         *
         * @param {Function} listener A callback to be invoked on every dispatch.
         * @returns {Function} A function to remove this change listener.
         */


        function subscribe(listener) {
          if (typeof listener !== 'function') {
            throw new Error('Expected the listener to be a function.');
          }

          if (isDispatching) {
            throw new Error('You may not call store.subscribe() while the reducer is executing. ' + 'If you would like to be notified after the store has been updated, subscribe from a ' + 'component and invoke store.getState() in the callback to access the latest state. ' + 'See https://redux.js.org/api-reference/store#subscribe(listener) for more details.');
          }

          var isSubscribed = true;
          ensureCanMutateNextListeners();
          nextListeners.push(listener);
          return function unsubscribe() {
            if (!isSubscribed) {
              return;
            }

            if (isDispatching) {
              throw new Error('You may not unsubscribe from a store listener while the reducer is executing. ' + 'See https://redux.js.org/api-reference/store#subscribe(listener) for more details.');
            }

            isSubscribed = false;
            ensureCanMutateNextListeners();
            var index = nextListeners.indexOf(listener);
            nextListeners.splice(index, 1);
          };
        }
        /**
         * Dispatches an action. It is the only way to trigger a state change.
         *
         * The `reducer` function, used to create the store, will be called with the
         * current state tree and the given `action`. Its return value will
         * be considered the **next** state of the tree, and the change listeners
         * will be notified.
         *
         * The base implementation only supports plain object actions. If you want to
         * dispatch a Promise, an Observable, a thunk, or something else, you need to
         * wrap your store creating function into the corresponding middleware. For
         * example, see the documentation for the `redux-thunk` package. Even the
         * middleware will eventually dispatch plain object actions using this method.
         *
         * @param {Object} action A plain object representing “what changed”. It is
         * a good idea to keep actions serializable so you can record and replay user
         * sessions, or use the time travelling `redux-devtools`. An action must have
         * a `type` property which may not be `undefined`. It is a good idea to use
         * string constants for action types.
         *
         * @returns {Object} For convenience, the same action object you dispatched.
         *
         * Note that, if you use a custom middleware, it may wrap `dispatch()` to
         * return something else (for example, a Promise you can await).
         */


        function dispatch(action) {
          if (!isPlainObject(action)) {
            throw new Error('Actions must be plain objects. ' + 'Use custom middleware for async actions.');
          }

          if (typeof action.type === 'undefined') {
            throw new Error('Actions may not have an undefined "type" property. ' + 'Have you misspelled a constant?');
          }

          if (isDispatching) {
            throw new Error('Reducers may not dispatch actions.');
          }

          try {
            isDispatching = true;
            currentState = currentReducer(currentState, action);
          } finally {
            isDispatching = false;
          }

          var listeners = currentListeners = nextListeners;

          for (var i = 0; i < listeners.length; i++) {
            var listener = listeners[i];
            listener();
          }

          return action;
        }
        /**
         * Replaces the reducer currently used by the store to calculate the state.
         *
         * You might need this if your app implements code splitting and you want to
         * load some of the reducers dynamically. You might also need this if you
         * implement a hot reloading mechanism for Redux.
         *
         * @param {Function} nextReducer The reducer for the store to use instead.
         * @returns {void}
         */


        function replaceReducer(nextReducer) {
          if (typeof nextReducer !== 'function') {
            throw new Error('Expected the nextReducer to be a function.');
          }

          currentReducer = nextReducer; // This action has a similiar effect to ActionTypes.INIT.
          // Any reducers that existed in both the new and old rootReducer
          // will receive the previous state. This effectively populates
          // the new state tree with any relevant data from the old one.

          dispatch({
            type: ActionTypes.REPLACE
          });
        }
        /**
         * Interoperability point for observable/reactive libraries.
         * @returns {observable} A minimal observable of state changes.
         * For more information, see the observable proposal:
         * https://github.com/tc39/proposal-observable
         */


        function observable() {
          var _ref;

          var outerSubscribe = subscribe;
          return _ref = {
            /**
             * The minimal observable subscription method.
             * @param {Object} observer Any object that can be used as an observer.
             * The observer object should have a `next` method.
             * @returns {subscription} An object with an `unsubscribe` method that can
             * be used to unsubscribe the observable from the store, and prevent further
             * emission of values from the observable.
             */
            subscribe: function subscribe(observer) {
              if (_typeof2(observer) !== 'object' || observer === null) {
                throw new TypeError('Expected the observer to be an object.');
              }

              function observeState() {
                if (observer.next) {
                  observer.next(getState());
                }
              }

              observeState();
              var unsubscribe = outerSubscribe(observeState);
              return {
                unsubscribe: unsubscribe
              };
            }
          }, _ref[es["a"
          /* default */
          ]] = function () {
            return this;
          }, _ref;
        } // When a store is created, an "INIT" action is dispatched so that every
        // reducer returns their initial state. This effectively populates
        // the initial state tree.


        dispatch({
          type: ActionTypes.INIT
        });
        return _ref2 = {
          dispatch: dispatch,
          subscribe: subscribe,
          getState: getState,
          replaceReducer: replaceReducer
        }, _ref2[es["a"
        /* default */
        ]] = observable, _ref2;
      }
      /**
       * Prints a warning in the console if it exists.
       *
       * @param {String} message The warning message.
       * @returns {void}
       */


      function warning(message) {
        /* eslint-disable no-console */
        if (typeof console !== 'undefined' && typeof console.error === 'function') {
          console.error(message);
        }
        /* eslint-enable no-console */


        try {
          // This error was thrown as a convenience so that if you enable
          // "break on all exceptions" in your console,
          // it would pause the execution at this line.
          throw new Error(message);
        } catch (e) {} // eslint-disable-line no-empty

      }

      function getUndefinedStateErrorMessage(key, action) {
        var actionType = action && action.type;
        var actionDescription = actionType && "action \"" + String(actionType) + "\"" || 'an action';
        return "Given " + actionDescription + ", reducer \"" + key + "\" returned undefined. " + "To ignore an action, you must explicitly return the previous state. " + "If you want this reducer to hold no value, you can return null instead of undefined.";
      }

      function getUnexpectedStateShapeWarningMessage(inputState, reducers, action, unexpectedKeyCache) {
        var reducerKeys = Object.keys(reducers);
        var argumentName = action && action.type === ActionTypes.INIT ? 'preloadedState argument passed to createStore' : 'previous state received by the reducer';

        if (reducerKeys.length === 0) {
          return 'Store does not have a valid reducer. Make sure the argument passed ' + 'to combineReducers is an object whose values are reducers.';
        }

        if (!isPlainObject(inputState)) {
          return "The " + argumentName + " has unexpected type of \"" + {}.toString.call(inputState).match(/\s([a-z|A-Z]+)/)[1] + "\". Expected argument to be an object with the following " + ("keys: \"" + reducerKeys.join('", "') + "\"");
        }

        var unexpectedKeys = Object.keys(inputState).filter(function (key) {
          return !reducers.hasOwnProperty(key) && !unexpectedKeyCache[key];
        });
        unexpectedKeys.forEach(function (key) {
          unexpectedKeyCache[key] = true;
        });
        if (action && action.type === ActionTypes.REPLACE) return;

        if (unexpectedKeys.length > 0) {
          return "Unexpected " + (unexpectedKeys.length > 1 ? 'keys' : 'key') + " " + ("\"" + unexpectedKeys.join('", "') + "\" found in " + argumentName + ". ") + "Expected to find one of the known reducer keys instead: " + ("\"" + reducerKeys.join('", "') + "\". Unexpected keys will be ignored.");
        }
      }

      function assertReducerShape(reducers) {
        Object.keys(reducers).forEach(function (key) {
          var reducer = reducers[key];
          var initialState = reducer(undefined, {
            type: ActionTypes.INIT
          });

          if (typeof initialState === 'undefined') {
            throw new Error("Reducer \"" + key + "\" returned undefined during initialization. " + "If the state passed to the reducer is undefined, you must " + "explicitly return the initial state. The initial state may " + "not be undefined. If you don't want to set a value for this reducer, " + "you can use null instead of undefined.");
          }

          if (typeof reducer(undefined, {
            type: ActionTypes.PROBE_UNKNOWN_ACTION()
          }) === 'undefined') {
            throw new Error("Reducer \"" + key + "\" returned undefined when probed with a random type. " + ("Don't try to handle " + ActionTypes.INIT + " or other actions in \"redux/*\" ") + "namespace. They are considered private. Instead, you must return the " + "current state for any unknown actions, unless it is undefined, " + "in which case you must return the initial state, regardless of the " + "action type. The initial state may not be undefined, but can be null.");
          }
        });
      }
      /**
       * Turns an object whose values are different reducer functions, into a single
       * reducer function. It will call every child reducer, and gather their results
       * into a single state object, whose keys correspond to the keys of the passed
       * reducer functions.
       *
       * @param {Object} reducers An object whose values correspond to different
       * reducer functions that need to be combined into one. One handy way to obtain
       * it is to use ES6 `import * as reducers` syntax. The reducers may never return
       * undefined for any action. Instead, they should return their initial state
       * if the state passed to them was undefined, and the current state for any
       * unrecognized action.
       *
       * @returns {Function} A reducer function that invokes every reducer inside the
       * passed object, and builds a state object with the same shape.
       */


      function combineReducers(reducers) {
        var reducerKeys = Object.keys(reducers);
        var finalReducers = {};

        for (var i = 0; i < reducerKeys.length; i++) {
          var key = reducerKeys[i];

          if (false) {}

          if (typeof reducers[key] === 'function') {
            finalReducers[key] = reducers[key];
          }
        }

        var finalReducerKeys = Object.keys(finalReducers); // This is used to make sure we don't warn about the same
        // keys multiple times.

        var unexpectedKeyCache;

        if (false) {}

        var shapeAssertionError;

        try {
          assertReducerShape(finalReducers);
        } catch (e) {
          shapeAssertionError = e;
        }

        return function combination(state, action) {
          if (state === void 0) {
            state = {};
          }

          if (shapeAssertionError) {
            throw shapeAssertionError;
          }

          if (false) {
            var warningMessage;
          }

          var hasChanged = false;
          var nextState = {};

          for (var _i = 0; _i < finalReducerKeys.length; _i++) {
            var _key = finalReducerKeys[_i];
            var reducer = finalReducers[_key];
            var previousStateForKey = state[_key];
            var nextStateForKey = reducer(previousStateForKey, action);

            if (typeof nextStateForKey === 'undefined') {
              var errorMessage = getUndefinedStateErrorMessage(_key, action);
              throw new Error(errorMessage);
            }

            nextState[_key] = nextStateForKey;
            hasChanged = hasChanged || nextStateForKey !== previousStateForKey;
          }

          return hasChanged ? nextState : state;
        };
      }

      function bindActionCreator(actionCreator, dispatch) {
        return function () {
          return dispatch(actionCreator.apply(this, arguments));
        };
      }
      /**
       * Turns an object whose values are action creators, into an object with the
       * same keys, but with every function wrapped into a `dispatch` call so they
       * may be invoked directly. This is just a convenience method, as you can call
       * `store.dispatch(MyActionCreators.doSomething())` yourself just fine.
       *
       * For convenience, you can also pass an action creator as the first argument,
       * and get a dispatch wrapped function in return.
       *
       * @param {Function|Object} actionCreators An object whose values are action
       * creator functions. One handy way to obtain it is to use ES6 `import * as`
       * syntax. You may also pass a single function.
       *
       * @param {Function} dispatch The `dispatch` function available on your Redux
       * store.
       *
       * @returns {Function|Object} The object mimicking the original object, but with
       * every action creator wrapped into the `dispatch` call. If you passed a
       * function as `actionCreators`, the return value will also be a single
       * function.
       */


      function bindActionCreators(actionCreators, dispatch) {
        if (typeof actionCreators === 'function') {
          return bindActionCreator(actionCreators, dispatch);
        }

        if (_typeof2(actionCreators) !== 'object' || actionCreators === null) {
          throw new Error("bindActionCreators expected an object or a function, instead received " + (actionCreators === null ? 'null' : _typeof2(actionCreators)) + ". " + "Did you write \"import ActionCreators from\" instead of \"import * as ActionCreators from\"?");
        }

        var boundActionCreators = {};

        for (var key in actionCreators) {
          var actionCreator = actionCreators[key];

          if (typeof actionCreator === 'function') {
            boundActionCreators[key] = bindActionCreator(actionCreator, dispatch);
          }
        }

        return boundActionCreators;
      }

      function _defineProperty(obj, key, value) {
        if (key in obj) {
          Object.defineProperty(obj, key, {
            value: value,
            enumerable: true,
            configurable: true,
            writable: true
          });
        } else {
          obj[key] = value;
        }

        return obj;
      }

      function ownKeys(object, enumerableOnly) {
        var keys = Object.keys(object);

        if (Object.getOwnPropertySymbols) {
          keys.push.apply(keys, Object.getOwnPropertySymbols(object));
        }

        if (enumerableOnly) keys = keys.filter(function (sym) {
          return Object.getOwnPropertyDescriptor(object, sym).enumerable;
        });
        return keys;
      }

      function _objectSpread2(target) {
        for (var i = 1; i < arguments.length; i++) {
          var source = arguments[i] != null ? arguments[i] : {};

          if (i % 2) {
            ownKeys(source, true).forEach(function (key) {
              _defineProperty(target, key, source[key]);
            });
          } else if (Object.getOwnPropertyDescriptors) {
            Object.defineProperties(target, Object.getOwnPropertyDescriptors(source));
          } else {
            ownKeys(source).forEach(function (key) {
              Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key));
            });
          }
        }

        return target;
      }
      /**
       * Composes single-argument functions from right to left. The rightmost
       * function can take multiple arguments as it provides the signature for
       * the resulting composite function.
       *
       * @param {...Function} funcs The functions to compose.
       * @returns {Function} A function obtained by composing the argument functions
       * from right to left. For example, compose(f, g, h) is identical to doing
       * (...args) => f(g(h(...args))).
       */


      function compose() {
        for (var _len = arguments.length, funcs = new Array(_len), _key = 0; _key < _len; _key++) {
          funcs[_key] = arguments[_key];
        }

        if (funcs.length === 0) {
          return function (arg) {
            return arg;
          };
        }

        if (funcs.length === 1) {
          return funcs[0];
        }

        return funcs.reduce(function (a, b) {
          return function () {
            return a(b.apply(void 0, arguments));
          };
        });
      }
      /**
       * Creates a store enhancer that applies middleware to the dispatch method
       * of the Redux store. This is handy for a variety of tasks, such as expressing
       * asynchronous actions in a concise manner, or logging every action payload.
       *
       * See `redux-thunk` package as an example of the Redux middleware.
       *
       * Because middleware is potentially asynchronous, this should be the first
       * store enhancer in the composition chain.
       *
       * Note that each middleware will be given the `dispatch` and `getState` functions
       * as named arguments.
       *
       * @param {...Function} middlewares The middleware chain to be applied.
       * @returns {Function} A store enhancer applying the middleware.
       */


      function applyMiddleware() {
        for (var _len = arguments.length, middlewares = new Array(_len), _key = 0; _key < _len; _key++) {
          middlewares[_key] = arguments[_key];
        }

        return function (createStore) {
          return function () {
            var store = createStore.apply(void 0, arguments);

            var _dispatch = function dispatch() {
              throw new Error('Dispatching while constructing your middleware is not allowed. ' + 'Other middleware would not be applied to this dispatch.');
            };

            var middlewareAPI = {
              getState: store.getState,
              dispatch: function dispatch() {
                return _dispatch.apply(void 0, arguments);
              }
            };
            var chain = middlewares.map(function (middleware) {
              return middleware(middlewareAPI);
            });
            _dispatch = compose.apply(void 0, chain)(store.dispatch);
            return _objectSpread2({}, store, {
              dispatch: _dispatch
            });
          };
        };
      }
      /*
       * This is a dummy function to check if the function name has been altered by minification.
       * If the function has been minified and NODE_ENV !== 'production', warn the user.
       */


      function isCrushed() {}

      if (false) {} // CONCATENATED MODULE: ./src/scripts/reducers/items.js


      var defaultState = [];

      function items_items(state, action) {
        if (state === void 0) {
          state = defaultState;
        }

        switch (action.type) {
          case 'ADD_ITEM':
            {
              // Add object to items array
              var newState = [].concat(state, [{
                id: action.id,
                choiceId: action.choiceId,
                groupId: action.groupId,
                value: action.value,
                label: action.label,
                active: true,
                highlighted: false,
                customProperties: action.customProperties,
                placeholder: action.placeholder || false,
                keyCode: null
              }]);
              return newState.map(function (obj) {
                var item = obj;
                item.highlighted = false;
                return item;
              });
            }

          case 'REMOVE_ITEM':
            {
              // Set item to inactive
              return state.map(function (obj) {
                var item = obj;

                if (item.id === action.id) {
                  item.active = false;
                }

                return item;
              });
            }

          case 'HIGHLIGHT_ITEM':
            {
              return state.map(function (obj) {
                var item = obj;

                if (item.id === action.id) {
                  item.highlighted = action.highlighted;
                }

                return item;
              });
            }

          default:
            {
              return state;
            }
        }
      } // CONCATENATED MODULE: ./src/scripts/reducers/groups.js


      var groups_defaultState = [];

      function groups(state, action) {
        if (state === void 0) {
          state = groups_defaultState;
        }

        switch (action.type) {
          case 'ADD_GROUP':
            {
              return [].concat(state, [{
                id: action.id,
                value: action.value,
                active: action.active,
                disabled: action.disabled
              }]);
            }

          case 'CLEAR_CHOICES':
            {
              return [];
            }

          default:
            {
              return state;
            }
        }
      } // CONCATENATED MODULE: ./src/scripts/reducers/choices.js


      var choices_defaultState = [];

      function choices_choices(state, action) {
        if (state === void 0) {
          state = choices_defaultState;
        }

        switch (action.type) {
          case 'ADD_CHOICE':
            {
              /*
                  A disabled choice appears in the choice dropdown but cannot be selected
                  A selected choice has been added to the passed input's value (added as an item)
                  An active choice appears within the choice dropdown
               */
              return [].concat(state, [{
                id: action.id,
                elementId: action.elementId,
                groupId: action.groupId,
                value: action.value,
                label: action.label || action.value,
                disabled: action.disabled || false,
                selected: false,
                active: true,
                score: 9999,
                customProperties: action.customProperties,
                placeholder: action.placeholder || false,
                keyCode: null
              }]);
            }

          case 'ADD_ITEM':
            {
              // If all choices need to be activated
              if (action.activateOptions) {
                return state.map(function (obj) {
                  var choice = obj;
                  choice.active = action.active;
                  return choice;
                });
              } // When an item is added and it has an associated choice,
              // we want to disable it so it can't be chosen again


              if (action.choiceId > -1) {
                return state.map(function (obj) {
                  var choice = obj;

                  if (choice.id === parseInt(action.choiceId, 10)) {
                    choice.selected = true;
                  }

                  return choice;
                });
              }

              return state;
            }

          case 'REMOVE_ITEM':
            {
              // When an item is removed and it has an associated choice,
              // we want to re-enable it so it can be chosen again
              if (action.choiceId > -1) {
                return state.map(function (obj) {
                  var choice = obj;

                  if (choice.id === parseInt(action.choiceId, 10)) {
                    choice.selected = false;
                  }

                  return choice;
                });
              }

              return state;
            }

          case 'FILTER_CHOICES':
            {
              return state.map(function (obj) {
                var choice = obj; // Set active state based on whether choice is
                // within filtered results

                choice.active = action.results.some(function (_ref) {
                  var item = _ref.item,
                      score = _ref.score;

                  if (item.id === choice.id) {
                    choice.score = score;
                    return true;
                  }

                  return false;
                });
                return choice;
              });
            }

          case 'ACTIVATE_CHOICES':
            {
              return state.map(function (obj) {
                var choice = obj;
                choice.active = action.active;
                return choice;
              });
            }

          case 'CLEAR_CHOICES':
            {
              return choices_defaultState;
            }

          default:
            {
              return state;
            }
        }
      } // CONCATENATED MODULE: ./src/scripts/reducers/general.js


      var general_defaultState = {
        loading: false
      };

      var general = function general(state, action) {
        if (state === void 0) {
          state = general_defaultState;
        }

        switch (action.type) {
          case 'SET_IS_LOADING':
            {
              return {
                loading: action.isLoading
              };
            }

          default:
            {
              return state;
            }
        }
      };
      /* harmony default export */


      var reducers_general = general; // CONCATENATED MODULE: ./src/scripts/lib/utils.js

      var utils_this = undefined;

      var getRandomNumber = function getRandomNumber(min, max) {
        return Math.floor(Math.random() * (max - min) + min);
      };

      var generateChars = function generateChars(length) {
        var chars = '';

        for (var i = 0; i < length; i++) {
          var randomChar = getRandomNumber(0, 36);
          chars += randomChar.toString(36);
        }

        return chars;
      };

      var generateId = function generateId(element, prefix) {
        var id = element.id || element.name && element.name + "-" + generateChars(2) || generateChars(4);
        id = id.replace(/(:|\.|\[|\]|,)/g, '');
        id = prefix + "-" + id;
        return id;
      };

      var getType = function getType(obj) {
        return Object.prototype.toString.call(obj).slice(8, -1);
      };

      var isType = function isType(type, obj) {
        return obj !== undefined && obj !== null && getType(obj) === type;
      };

      var isElement = function isElement(element) {
        return element instanceof Element;
      };

      var utils_wrap = function wrap(element, wrapper) {
        if (wrapper === void 0) {
          wrapper = document.createElement('div');
        }

        if (element.nextSibling) {
          element.parentNode.insertBefore(wrapper, element.nextSibling);
        } else {
          element.parentNode.appendChild(wrapper);
        }

        return wrapper.appendChild(element);
      };

      var findAncestorByAttrName = function findAncestorByAttrName(el, attr) {
        var target = el;

        while (target) {
          if (target.hasAttribute(attr)) {
            return target;
          }

          target = target.parentElement;
        }

        return null;
      };

      var getAdjacentEl = function getAdjacentEl(startEl, className, direction) {
        if (direction === void 0) {
          direction = 1;
        }

        if (!startEl || !className) {
          return;
        }

        var parent = startEl.parentNode.parentNode;
        var children = Array.from(parent.querySelectorAll(className));
        var startPos = children.indexOf(startEl);
        var operatorDirection = direction > 0 ? 1 : -1;
        return children[startPos + operatorDirection];
      };

      var isScrolledIntoView = function isScrolledIntoView(el, parent, direction) {
        if (direction === void 0) {
          direction = 1;
        }

        if (!el) {
          return;
        }

        var isVisible;

        if (direction > 0) {
          // In view from bottom
          isVisible = parent.scrollTop + parent.offsetHeight >= el.offsetTop + el.offsetHeight;
        } else {
          // In view from top
          isVisible = el.offsetTop >= parent.scrollTop;
        }

        return isVisible;
      };

      var sanitise = function sanitise(value) {
        if (!isType('String', value)) {
          return value;
        }

        return value.replace(/&/g, '&amp;').replace(/>/g, '&rt;').replace(/</g, '&lt;').replace(/"/g, '&quot;');
      };

      var strToEl = function () {
        var tmpEl = document.createElement('div');
        return function (str) {
          var cleanedInput = str.trim();
          tmpEl.innerHTML = cleanedInput;
          var firldChild = tmpEl.children[0];

          while (tmpEl.firstChild) {
            tmpEl.removeChild(tmpEl.firstChild);
          }

          return firldChild;
        };
      }();
      /**
       * Determines the width of a passed input based on its value and passes
       * it to the supplied callback function.
       */


      var calcWidthOfInput = function calcWidthOfInput(input, callback) {
        var value = input.value || input.placeholder;
        var width = input.offsetWidth;

        if (value) {
          var testEl = strToEl("<span>" + sanitise(value) + "</span>");
          testEl.style.position = 'absolute';
          testEl.style.padding = '0';
          testEl.style.top = '-9999px';
          testEl.style.left = '-9999px';
          testEl.style.width = 'auto';
          testEl.style.whiteSpace = 'pre';

          if (document.body.contains(input) && window.getComputedStyle) {
            var inputStyle = window.getComputedStyle(input);

            if (inputStyle) {
              testEl.style.fontSize = inputStyle.fontSize;
              testEl.style.fontFamily = inputStyle.fontFamily;
              testEl.style.fontWeight = inputStyle.fontWeight;
              testEl.style.fontStyle = inputStyle.fontStyle;
              testEl.style.letterSpacing = inputStyle.letterSpacing;
              testEl.style.textTransform = inputStyle.textTransform;
              testEl.style.paddingLeft = inputStyle.paddingLeft;
              testEl.style.paddingRight = inputStyle.paddingRight;
            }
          }

          document.body.appendChild(testEl);
          requestAnimationFrame(function () {
            if (value && testEl.offsetWidth !== input.offsetWidth) {
              width = testEl.offsetWidth + 4;
            }

            document.body.removeChild(testEl);
            callback.call(utils_this, width + "px");
          });
        } else {
          callback.call(utils_this, width + "px");
        }
      };

      var sortByAlpha = function sortByAlpha(a, b) {
        var labelA = ("" + (a.label || a.value)).toLowerCase();
        var labelB = ("" + (b.label || b.value)).toLowerCase();

        if (labelA < labelB) {
          return -1;
        }

        if (labelA > labelB) {
          return 1;
        }

        return 0;
      };

      var sortByScore = function sortByScore(a, b) {
        return a.score - b.score;
      };

      var dispatchEvent = function dispatchEvent(element, type, customArgs) {
        if (customArgs === void 0) {
          customArgs = null;
        }

        var event = new CustomEvent(type, {
          detail: customArgs,
          bubbles: true,
          cancelable: true
        });
        return element.dispatchEvent(event);
      };

      var getWindowHeight = function getWindowHeight() {
        var _document = document,
            body = _document.body;
        var html = document.documentElement;
        return Math.max(body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight);
      };

      var fetchFromObject = function fetchFromObject(object, path) {
        var index = path.indexOf('.');

        if (index > -1) {
          return fetchFromObject(object[path.substring(0, index)], path.substr(index + 1));
        }

        return object[path];
      };

      var isIE11 = function isIE11() {
        return !!(navigator.userAgent.match(/Trident/) && navigator.userAgent.match(/rv[ :]11/));
      };

      var existsInArray = function existsInArray(array, value, key) {
        if (key === void 0) {
          key = 'value';
        }

        return array.some(function (item) {
          if (isType('String', value)) {
            return item[key] === value.trim();
          }

          return item[key] === value;
        });
      };

      var cloneObject = function cloneObject(obj) {
        return JSON.parse(JSON.stringify(obj));
      };

      var diff = function diff(a, b) {
        var aKeys = Object.keys(a).sort();
        var bKeys = Object.keys(b).sort();
        return aKeys.filter(function (i) {
          return bKeys.indexOf(i) < 0;
        });
      }; // CONCATENATED MODULE: ./src/scripts/reducers/index.js


      var appReducer = combineReducers({
        items: items_items,
        groups: groups,
        choices: choices_choices,
        general: reducers_general
      });

      var reducers_rootReducer = function rootReducer(passedState, action) {
        var state = passedState; // If we are clearing all items, groups and options we reassign
        // state and then pass that state to our proper reducer. This isn't
        // mutating our actual state
        // See: http://stackoverflow.com/a/35641992

        if (action.type === 'CLEAR_ALL') {
          state = undefined;
        } else if (action.type === 'RESET_TO') {
          return cloneObject(action.state);
        }

        return appReducer(state, action);
      };
      /* harmony default export */


      var reducers = reducers_rootReducer; // CONCATENATED MODULE: ./src/scripts/store/store.js

      function _defineProperties(target, props) {
        for (var i = 0; i < props.length; i++) {
          var descriptor = props[i];
          descriptor.enumerable = descriptor.enumerable || false;
          descriptor.configurable = true;
          if ("value" in descriptor) descriptor.writable = true;
          Object.defineProperty(target, descriptor.key, descriptor);
        }
      }

      function _createClass(Constructor, protoProps, staticProps) {
        if (protoProps) _defineProperties(Constructor.prototype, protoProps);
        if (staticProps) _defineProperties(Constructor, staticProps);
        return Constructor;
      }

      var store_Store = /*#__PURE__*/function () {
        function Store() {
          this._store = createStore(reducers, window.__REDUX_DEVTOOLS_EXTENSION__ && window.__REDUX_DEVTOOLS_EXTENSION__());
        }
        /**
         * Subscribe store to function call (wrapped Redux method)
         * @param  {Function} onChange Function to trigger when state changes
         * @return
         */


        var _proto = Store.prototype;

        _proto.subscribe = function subscribe(onChange) {
          this._store.subscribe(onChange);
        }
        /**
         * Dispatch event to store (wrapped Redux method)
         * @param  {Function} action Action function to trigger
         * @return
         */
        ;

        _proto.dispatch = function dispatch(action) {
          this._store.dispatch(action);
        }
        /**
         * Get store object (wrapping Redux method)
         * @return {Object} State
         */
        ;
        /**
         * Get loading state from store
         * @return {Boolean} Loading State
         */


        _proto.isLoading = function isLoading() {
          return this.state.general.loading;
        }
        /**
         * Get single choice by it's ID
         * @return {Object} Found choice
         */
        ;

        _proto.getChoiceById = function getChoiceById(id) {
          if (id) {
            return this.activeChoices.find(function (choice) {
              return choice.id === parseInt(id, 10);
            });
          }

          return false;
        }
        /**
         * Get group by group id
         * @param  {Number} id Group ID
         * @return {Object}    Group data
         */
        ;

        _proto.getGroupById = function getGroupById(id) {
          return this.groups.find(function (group) {
            return group.id === parseInt(id, 10);
          });
        };

        _createClass(Store, [{
          key: "state",
          get: function get() {
            return this._store.getState();
          }
          /**
           * Get items from store
           * @return {Array} Item objects
           */

        }, {
          key: "items",
          get: function get() {
            return this.state.items;
          }
          /**
           * Get active items from store
           * @return {Array} Item objects
           */

        }, {
          key: "activeItems",
          get: function get() {
            return this.items.filter(function (item) {
              return item.active === true;
            });
          }
          /**
           * Get highlighted items from store
           * @return {Array} Item objects
           */

        }, {
          key: "highlightedActiveItems",
          get: function get() {
            return this.items.filter(function (item) {
              return item.active && item.highlighted;
            });
          }
          /**
           * Get choices from store
           * @return {Array} Option objects
           */

        }, {
          key: "choices",
          get: function get() {
            return this.state.choices;
          }
          /**
           * Get active choices from store
           * @return {Array} Option objects
           */

        }, {
          key: "activeChoices",
          get: function get() {
            var choices = this.choices;
            var values = choices.filter(function (choice) {
              return choice.active === true;
            });
            return values;
          }
          /**
           * Get selectable choices from store
           * @return {Array} Option objects
           */

        }, {
          key: "selectableChoices",
          get: function get() {
            return this.choices.filter(function (choice) {
              return choice.disabled !== true;
            });
          }
          /**
           * Get choices that can be searched (excluding placeholders)
           * @return {Array} Option objects
           */

        }, {
          key: "searchableChoices",
          get: function get() {
            return this.selectableChoices.filter(function (choice) {
              return choice.placeholder !== true;
            });
          }
          /**
           * Get placeholder choice from store
           * @return {Object} Found placeholder
           */

        }, {
          key: "placeholderChoice",
          get: function get() {
            return [].concat(this.choices).reverse().find(function (choice) {
              return choice.placeholder === true;
            });
          }
          /**
           * Get groups from store
           * @return {Array} Group objects
           */

        }, {
          key: "groups",
          get: function get() {
            return this.state.groups;
          }
          /**
           * Get active groups from store
           * @return {Array} Group objects
           */

        }, {
          key: "activeGroups",
          get: function get() {
            var groups = this.groups,
                choices = this.choices;
            return groups.filter(function (group) {
              var isActive = group.active === true && group.disabled === false;
              var hasActiveOptions = choices.some(function (choice) {
                return choice.active === true && choice.disabled === false;
              });
              return isActive && hasActiveOptions;
            }, []);
          }
        }]);

        return Store;
      }(); // CONCATENATED MODULE: ./src/scripts/components/dropdown.js


      var Dropdown = /*#__PURE__*/function () {
        function Dropdown(_ref) {
          var element = _ref.element,
              type = _ref.type,
              classNames = _ref.classNames;
          Object.assign(this, {
            element: element,
            type: type,
            classNames: classNames
          });
          this.isActive = false;
        }
        /**
         * Determine how far the top of our element is from
         * the top of the window
         * @return {Number} Vertical position
         */


        var _proto = Dropdown.prototype;

        _proto.distanceFromTopWindow = function distanceFromTopWindow() {
          this.dimensions = this.element.getBoundingClientRect();
          this.position = Math.ceil(this.dimensions.top + window.pageYOffset + this.element.offsetHeight);
          return this.position;
        }
        /**
         * Find element that matches passed selector
         * @return {HTMLElement}
         */
        ;

        _proto.getChild = function getChild(selector) {
          return this.element.querySelector(selector);
        }
        /**
         * Show dropdown to user by adding active state class
         * @return {Object} Class instance
         * @public
         */
        ;

        _proto.show = function show() {
          this.element.classList.add(this.classNames.activeState);
          this.element.setAttribute('aria-expanded', 'true');
          this.isActive = true;
          return this;
        }
        /**
         * Hide dropdown from user
         * @return {Object} Class instance
         * @public
         */
        ;

        _proto.hide = function hide() {
          this.element.classList.remove(this.classNames.activeState);
          this.element.setAttribute('aria-expanded', 'false');
          this.isActive = false;
          return this;
        };

        return Dropdown;
      }(); // CONCATENATED MODULE: ./src/scripts/components/container.js


      var container_Container = /*#__PURE__*/function () {
        function Container(_ref) {
          var element = _ref.element,
              type = _ref.type,
              classNames = _ref.classNames,
              position = _ref.position;
          Object.assign(this, {
            element: element,
            classNames: classNames,
            type: type,
            position: position
          });
          this.isOpen = false;
          this.isFlipped = false;
          this.isFocussed = false;
          this.isDisabled = false;
          this.isLoading = false;
          this._onFocus = this._onFocus.bind(this);
          this._onBlur = this._onBlur.bind(this);
        }
        /**
         * Add event listeners
         */


        var _proto = Container.prototype;

        _proto.addEventListeners = function addEventListeners() {
          this.element.addEventListener('focus', this._onFocus);
          this.element.addEventListener('blur', this._onBlur);
        }
        /**
         * Remove event listeners
         */

        /** */
        ;

        _proto.removeEventListeners = function removeEventListeners() {
          this.element.removeEventListener('focus', this._onFocus);
          this.element.removeEventListener('blur', this._onBlur);
        }
        /**
         * Determine whether container should be flipped
         * based on passed dropdown position
         * @param {Number} dropdownPos
         * @returns
         */
        ;

        _proto.shouldFlip = function shouldFlip(dropdownPos, windowHeight) {
          if (windowHeight === void 0) {
            windowHeight = getWindowHeight();
          }

          if (dropdownPos === undefined) {
            return false;
          } // If flip is enabled and the dropdown bottom position is
          // greater than the window height flip the dropdown.


          var shouldFlip = false;

          if (this.position === 'auto') {
            shouldFlip = dropdownPos >= windowHeight;
          } else if (this.position === 'top') {
            shouldFlip = true;
          }

          return shouldFlip;
        }
        /**
         * Set active descendant attribute
         * @param {Number} activeDescendant ID of active descendant
         */
        ;

        _proto.setActiveDescendant = function setActiveDescendant(activeDescendantID) {
          this.element.setAttribute('aria-activedescendant', activeDescendantID);
        }
        /**
         * Remove active descendant attribute
         */
        ;

        _proto.removeActiveDescendant = function removeActiveDescendant() {
          this.element.removeAttribute('aria-activedescendant');
        };

        _proto.open = function open(dropdownPos) {
          this.element.classList.add(this.classNames.openState);
          this.element.setAttribute('aria-expanded', 'true');
          this.isOpen = true;

          if (this.shouldFlip(dropdownPos)) {
            this.element.classList.add(this.classNames.flippedState);
            this.isFlipped = true;
          }
        };

        _proto.close = function close() {
          this.element.classList.remove(this.classNames.openState);
          this.element.setAttribute('aria-expanded', 'false');
          this.removeActiveDescendant();
          this.isOpen = false; // A dropdown flips if it does not have space within the page

          if (this.isFlipped) {
            this.element.classList.remove(this.classNames.flippedState);
            this.isFlipped = false;
          }
        };

        _proto.focus = function focus() {
          if (!this.isFocussed) {
            this.element.focus();
          }
        };

        _proto.addFocusState = function addFocusState() {
          this.element.classList.add(this.classNames.focusState);
        };

        _proto.removeFocusState = function removeFocusState() {
          this.element.classList.remove(this.classNames.focusState);
        }
        /**
         * Remove disabled state
         */
        ;

        _proto.enable = function enable() {
          this.element.classList.remove(this.classNames.disabledState);
          this.element.removeAttribute('aria-disabled');

          if (this.type === 'select-one') {
            this.element.setAttribute('tabindex', '0');
          }

          this.isDisabled = false;
        }
        /**
         * Set disabled state
         */
        ;

        _proto.disable = function disable() {
          this.element.classList.add(this.classNames.disabledState);
          this.element.setAttribute('aria-disabled', 'true');

          if (this.type === 'select-one') {
            this.element.setAttribute('tabindex', '-1');
          }

          this.isDisabled = true;
        };

        _proto.wrap = function wrap(element) {
          utils_wrap(element, this.element);
        };

        _proto.unwrap = function unwrap(element) {
          // Move passed element outside this element
          this.element.parentNode.insertBefore(element, this.element); // Remove this element

          this.element.parentNode.removeChild(this.element);
        }
        /**
         * Add loading state to element
         */
        ;

        _proto.addLoadingState = function addLoadingState() {
          this.element.classList.add(this.classNames.loadingState);
          this.element.setAttribute('aria-busy', 'true');
          this.isLoading = true;
        }
        /**
         * Remove loading state from element
         */
        ;

        _proto.removeLoadingState = function removeLoadingState() {
          this.element.classList.remove(this.classNames.loadingState);
          this.element.removeAttribute('aria-busy');
          this.isLoading = false;
        }
        /**
         * Set focussed state
         */
        ;

        _proto._onFocus = function _onFocus() {
          this.isFocussed = true;
        }
        /**
         * Remove blurred state
         */
        ;

        _proto._onBlur = function _onBlur() {
          this.isFocussed = false;
        };

        return Container;
      }(); // CONCATENATED MODULE: ./src/scripts/components/input.js


      function input_defineProperties(target, props) {
        for (var i = 0; i < props.length; i++) {
          var descriptor = props[i];
          descriptor.enumerable = descriptor.enumerable || false;
          descriptor.configurable = true;
          if ("value" in descriptor) descriptor.writable = true;
          Object.defineProperty(target, descriptor.key, descriptor);
        }
      }

      function input_createClass(Constructor, protoProps, staticProps) {
        if (protoProps) input_defineProperties(Constructor.prototype, protoProps);
        if (staticProps) input_defineProperties(Constructor, staticProps);
        return Constructor;
      }

      var input_Input = /*#__PURE__*/function () {
        function Input(_ref) {
          var element = _ref.element,
              type = _ref.type,
              classNames = _ref.classNames,
              placeholderValue = _ref.placeholderValue;
          Object.assign(this, {
            element: element,
            type: type,
            classNames: classNames,
            placeholderValue: placeholderValue
          });
          this.element = element;
          this.classNames = classNames;
          this.isFocussed = this.element === document.activeElement;
          this.isDisabled = false;
          this._onPaste = this._onPaste.bind(this);
          this._onInput = this._onInput.bind(this);
          this._onFocus = this._onFocus.bind(this);
          this._onBlur = this._onBlur.bind(this);
        }

        var _proto = Input.prototype;

        _proto.addEventListeners = function addEventListeners() {
          this.element.addEventListener('input', this._onInput);
          this.element.addEventListener('paste', this._onPaste);
          this.element.addEventListener('focus', this._onFocus);
          this.element.addEventListener('blur', this._onBlur);

          if (this.element.form) {
            this.element.form.addEventListener('reset', this._onFormReset);
          }
        };

        _proto.removeEventListeners = function removeEventListeners() {
          this.element.removeEventListener('input', this._onInput);
          this.element.removeEventListener('paste', this._onPaste);
          this.element.removeEventListener('focus', this._onFocus);
          this.element.removeEventListener('blur', this._onBlur);

          if (this.element.form) {
            this.element.form.removeEventListener('reset', this._onFormReset);
          }
        };

        _proto.enable = function enable() {
          this.element.removeAttribute('disabled');
          this.isDisabled = false;
        };

        _proto.disable = function disable() {
          this.element.setAttribute('disabled', '');
          this.isDisabled = true;
        };

        _proto.focus = function focus() {
          if (!this.isFocussed) {
            this.element.focus();
          }
        };

        _proto.blur = function blur() {
          if (this.isFocussed) {
            this.element.blur();
          }
        }
        /**
         * Set value of input to blank
         * @return {Object} Class instance
         * @public
         */
        ;

        _proto.clear = function clear(setWidth) {
          if (setWidth === void 0) {
            setWidth = true;
          }

          if (this.element.value) {
            this.element.value = '';
          }

          if (setWidth) {
            this.setWidth();
          }

          return this;
        }
        /**
         * Set the correct input width based on placeholder
         * value or input value
         * @return
         */
        ;

        _proto.setWidth = function setWidth(enforceWidth) {
          var _this = this;

          var callback = function callback(width) {
            _this.element.style.width = width;
          };

          if (this._placeholderValue) {
            // If there is a placeholder, we only want to set the width of the input when it is a greater
            // length than 75% of the placeholder. This stops the input jumping around.
            var valueHasDesiredLength = this.element.value.length >= this._placeholderValue.length / 1.25;

            if (this.element.value && valueHasDesiredLength || enforceWidth) {
              this.calcWidth(callback);
            }
          } else {
            // If there is no placeholder, resize input to contents
            this.calcWidth(callback);
          }
        };

        _proto.calcWidth = function calcWidth(callback) {
          return calcWidthOfInput(this.element, callback);
        };

        _proto.setActiveDescendant = function setActiveDescendant(activeDescendantID) {
          this.element.setAttribute('aria-activedescendant', activeDescendantID);
        };

        _proto.removeActiveDescendant = function removeActiveDescendant() {
          this.element.removeAttribute('aria-activedescendant');
        };

        _proto._onInput = function _onInput() {
          if (this.type !== 'select-one') {
            this.setWidth();
          }
        };

        _proto._onPaste = function _onPaste(event) {
          var target = event.target;

          if (target === this.element && this.preventPaste) {
            event.preventDefault();
          }
        };

        _proto._onFocus = function _onFocus() {
          this.isFocussed = true;
        };

        _proto._onBlur = function _onBlur() {
          this.isFocussed = false;
        };

        input_createClass(Input, [{
          key: "placeholder",
          set: function set(placeholder) {
            this.element.placeholder = placeholder;
          }
        }, {
          key: "value",
          set: function set(value) {
            this.element.value = value;
          },
          get: function get() {
            return sanitise(this.element.value);
          }
        }]);
        return Input;
      }(); // CONCATENATED MODULE: ./src/scripts/constants.js


      var DEFAULT_CLASSNAMES = {
        containerOuter: 'choices',
        containerInner: 'choices__inner',
        input: 'choices__input',
        inputCloned: 'choices__input--cloned',
        list: 'choices__list',
        listItems: 'choices__list--multiple',
        listSingle: 'choices__list--single',
        listDropdown: 'choices__list--dropdown',
        item: 'choices__item',
        itemSelectable: 'choices__item--selectable',
        itemDisabled: 'choices__item--disabled',
        itemChoice: 'choices__item--choice',
        placeholder: 'choices__placeholder',
        group: 'choices__group',
        groupHeading: 'choices__heading',
        button: 'choices__button',
        activeState: 'is-active',
        focusState: 'is-focused',
        openState: 'is-open',
        disabledState: 'is-disabled',
        highlightedState: 'is-highlighted',
        hiddenState: 'is-hidden',
        flippedState: 'is-flipped',
        loadingState: 'is-loading',
        noResults: 'has-no-results',
        noChoices: 'has-no-choices'
      };
      var DEFAULT_CONFIG = {
        items: [],
        choices: [],
        silent: false,
        renderChoiceLimit: -1,
        maxItemCount: -1,
        addItems: true,
        addItemFilterFn: null,
        removeItems: true,
        removeItemButton: false,
        editItems: false,
        duplicateItemsAllowed: true,
        delimiter: ',',
        paste: true,
        searchEnabled: true,
        searchChoices: true,
        searchFloor: 1,
        searchResultLimit: 4,
        searchFields: ['label', 'value'],
        position: 'auto',
        resetScrollPosition: true,
        shouldSort: true,
        shouldSortItems: false,
        sortFn: sortByAlpha,
        placeholder: true,
        placeholderValue: null,
        searchPlaceholderValue: null,
        prependValue: null,
        appendValue: null,
        renderSelectedChoices: 'auto',
        loadingText: 'Loading...',
        noResultsText: 'No results found',
        noChoicesText: 'No choices to choose from',
        itemSelectText: 'Press to select',
        uniqueItemText: 'Only unique values can be added',
        customAddItemText: 'Only values matching specific conditions can be added',
        addItemText: function addItemText(value) {
          return "Press Enter to add <b>\"" + sanitise(value) + "\"</b>";
        },
        maxItemText: function maxItemText(maxItemCount) {
          return "Only " + maxItemCount + " values can be added";
        },
        itemComparer: function itemComparer(choice, item) {
          return choice === item;
        },
        fuseOptions: {
          includeScore: true
        },
        callbackOnInit: null,
        callbackOnCreateTemplates: null,
        classNames: DEFAULT_CLASSNAMES
      };
      var EVENTS = {
        showDropdown: 'showDropdown',
        hideDropdown: 'hideDropdown',
        change: 'change',
        choice: 'choice',
        search: 'search',
        addItem: 'addItem',
        removeItem: 'removeItem',
        highlightItem: 'highlightItem',
        highlightChoice: 'highlightChoice'
      };
      var ACTION_TYPES = {
        ADD_CHOICE: 'ADD_CHOICE',
        FILTER_CHOICES: 'FILTER_CHOICES',
        ACTIVATE_CHOICES: 'ACTIVATE_CHOICES',
        CLEAR_CHOICES: 'CLEAR_CHOICES',
        ADD_GROUP: 'ADD_GROUP',
        ADD_ITEM: 'ADD_ITEM',
        REMOVE_ITEM: 'REMOVE_ITEM',
        HIGHLIGHT_ITEM: 'HIGHLIGHT_ITEM',
        CLEAR_ALL: 'CLEAR_ALL'
      };
      var KEY_CODES = {
        BACK_KEY: 46,
        DELETE_KEY: 8,
        ENTER_KEY: 13,
        A_KEY: 65,
        ESC_KEY: 27,
        UP_KEY: 38,
        DOWN_KEY: 40,
        PAGE_UP_KEY: 33,
        PAGE_DOWN_KEY: 34
      };
      var SCROLLING_SPEED = 4; // CONCATENATED MODULE: ./src/scripts/components/list.js

      var list_List = /*#__PURE__*/function () {
        function List(_ref) {
          var element = _ref.element;
          Object.assign(this, {
            element: element
          });
          this.scrollPos = this.element.scrollTop;
          this.height = this.element.offsetHeight;
          this.hasChildren = !!this.element.children;
        }

        var _proto = List.prototype;

        _proto.clear = function clear() {
          this.element.innerHTML = '';
        };

        _proto.append = function append(node) {
          this.element.appendChild(node);
        };

        _proto.getChild = function getChild(selector) {
          return this.element.querySelector(selector);
        };

        _proto.scrollToTop = function scrollToTop() {
          this.element.scrollTop = 0;
        };

        _proto.scrollToChoice = function scrollToChoice(choice, direction) {
          var _this = this;

          if (!choice) {
            return;
          }

          var dropdownHeight = this.element.offsetHeight;
          var choiceHeight = choice.offsetHeight; // Distance from bottom of element to top of parent

          var choicePos = choice.offsetTop + choiceHeight; // Scroll position of dropdown

          var containerScrollPos = this.element.scrollTop + dropdownHeight; // Difference between the choice and scroll position

          var endpoint = direction > 0 ? this.element.scrollTop + choicePos - containerScrollPos : choice.offsetTop;
          requestAnimationFrame(function (time) {
            _this._animateScroll(time, endpoint, direction);
          });
        };

        _proto._scrollDown = function _scrollDown(scrollPos, strength, endpoint) {
          var easing = (endpoint - scrollPos) / strength;
          var distance = easing > 1 ? easing : 1;
          this.element.scrollTop = scrollPos + distance;
        };

        _proto._scrollUp = function _scrollUp(scrollPos, strength, endpoint) {
          var easing = (scrollPos - endpoint) / strength;
          var distance = easing > 1 ? easing : 1;
          this.element.scrollTop = scrollPos - distance;
        };

        _proto._animateScroll = function _animateScroll(time, endpoint, direction) {
          var _this2 = this;

          var strength = SCROLLING_SPEED;
          var choiceListScrollTop = this.element.scrollTop;
          var continueAnimation = false;

          if (direction > 0) {
            this._scrollDown(choiceListScrollTop, strength, endpoint);

            if (choiceListScrollTop < endpoint) {
              continueAnimation = true;
            }
          } else {
            this._scrollUp(choiceListScrollTop, strength, endpoint);

            if (choiceListScrollTop > endpoint) {
              continueAnimation = true;
            }
          }

          if (continueAnimation) {
            requestAnimationFrame(function () {
              _this2._animateScroll(time, endpoint, direction);
            });
          }
        };

        return List;
      }(); // CONCATENATED MODULE: ./src/scripts/components/wrapped-element.js


      function wrapped_element_defineProperties(target, props) {
        for (var i = 0; i < props.length; i++) {
          var descriptor = props[i];
          descriptor.enumerable = descriptor.enumerable || false;
          descriptor.configurable = true;
          if ("value" in descriptor) descriptor.writable = true;
          Object.defineProperty(target, descriptor.key, descriptor);
        }
      }

      function wrapped_element_createClass(Constructor, protoProps, staticProps) {
        if (protoProps) wrapped_element_defineProperties(Constructor.prototype, protoProps);
        if (staticProps) wrapped_element_defineProperties(Constructor, staticProps);
        return Constructor;
      }

      var wrapped_element_WrappedElement = /*#__PURE__*/function () {
        function WrappedElement(_ref) {
          var element = _ref.element,
              classNames = _ref.classNames;
          Object.assign(this, {
            element: element,
            classNames: classNames
          });

          if (!isElement(element)) {
            throw new TypeError('Invalid element passed');
          }

          this.isDisabled = false;
        }

        var _proto = WrappedElement.prototype;

        _proto.conceal = function conceal() {
          // Hide passed input
          this.element.classList.add(this.classNames.input);
          this.element.classList.add(this.classNames.hiddenState); // Remove element from tab index

          this.element.tabIndex = '-1'; // Backup original styles if any

          var origStyle = this.element.getAttribute('style');

          if (origStyle) {
            this.element.setAttribute('data-choice-orig-style', origStyle);
          }

          this.element.setAttribute('aria-hidden', 'true');
          this.element.setAttribute('data-choice', 'active');
        };

        _proto.reveal = function reveal() {
          // Reinstate passed element
          this.element.classList.remove(this.classNames.input);
          this.element.classList.remove(this.classNames.hiddenState);
          this.element.removeAttribute('tabindex'); // Recover original styles if any

          var origStyle = this.element.getAttribute('data-choice-orig-style');

          if (origStyle) {
            this.element.removeAttribute('data-choice-orig-style');
            this.element.setAttribute('style', origStyle);
          } else {
            this.element.removeAttribute('style');
          }

          this.element.removeAttribute('aria-hidden');
          this.element.removeAttribute('data-choice'); // Re-assign values - this is weird, I know

          this.element.value = this.element.value;
        };

        _proto.enable = function enable() {
          this.element.removeAttribute('disabled');
          this.element.disabled = false;
          this.isDisabled = false;
        };

        _proto.disable = function disable() {
          this.element.setAttribute('disabled', '');
          this.element.disabled = true;
          this.isDisabled = true;
        };

        _proto.triggerEvent = function triggerEvent(eventType, data) {
          dispatchEvent(this.element, eventType, data);
        };

        wrapped_element_createClass(WrappedElement, [{
          key: "value",
          get: function get() {
            return this.element.value;
          },
          set: function set(value) {
            // you must define setter here otherwise it will be readonly property
            this.element.value = value;
          }
        }]);
        return WrappedElement;
      }(); // CONCATENATED MODULE: ./src/scripts/components/wrapped-input.js


      function wrapped_input_defineProperties(target, props) {
        for (var i = 0; i < props.length; i++) {
          var descriptor = props[i];
          descriptor.enumerable = descriptor.enumerable || false;
          descriptor.configurable = true;
          if ("value" in descriptor) descriptor.writable = true;
          Object.defineProperty(target, descriptor.key, descriptor);
        }
      }

      function wrapped_input_createClass(Constructor, protoProps, staticProps) {
        if (protoProps) wrapped_input_defineProperties(Constructor.prototype, protoProps);
        if (staticProps) wrapped_input_defineProperties(Constructor, staticProps);
        return Constructor;
      }

      function _inheritsLoose(subClass, superClass) {
        subClass.prototype = Object.create(superClass.prototype);
        subClass.prototype.constructor = subClass;
        subClass.__proto__ = superClass;
      }

      var WrappedInput = /*#__PURE__*/function (_WrappedElement) {
        _inheritsLoose(WrappedInput, _WrappedElement);

        function WrappedInput(_ref) {
          var _this;

          var element = _ref.element,
              classNames = _ref.classNames,
              delimiter = _ref.delimiter;
          _this = _WrappedElement.call(this, {
            element: element,
            classNames: classNames
          }) || this;
          _this.delimiter = delimiter;
          return _this;
        }

        wrapped_input_createClass(WrappedInput, [{
          key: "value",
          set: function set(items) {
            var itemValues = items.map(function (_ref2) {
              var value = _ref2.value;
              return value;
            });
            var joinedValues = itemValues.join(this.delimiter);
            this.element.setAttribute('value', joinedValues);
            this.element.value = joinedValues;
          },
          get: function get() {
            return this.element.value;
          }
        }]);
        return WrappedInput;
      }(wrapped_element_WrappedElement); // CONCATENATED MODULE: ./src/scripts/components/wrapped-select.js


      function wrapped_select_defineProperties(target, props) {
        for (var i = 0; i < props.length; i++) {
          var descriptor = props[i];
          descriptor.enumerable = descriptor.enumerable || false;
          descriptor.configurable = true;
          if ("value" in descriptor) descriptor.writable = true;
          Object.defineProperty(target, descriptor.key, descriptor);
        }
      }

      function wrapped_select_createClass(Constructor, protoProps, staticProps) {
        if (protoProps) wrapped_select_defineProperties(Constructor.prototype, protoProps);
        if (staticProps) wrapped_select_defineProperties(Constructor, staticProps);
        return Constructor;
      }

      function wrapped_select_inheritsLoose(subClass, superClass) {
        subClass.prototype = Object.create(superClass.prototype);
        subClass.prototype.constructor = subClass;
        subClass.__proto__ = superClass;
      }

      var WrappedSelect = /*#__PURE__*/function (_WrappedElement) {
        wrapped_select_inheritsLoose(WrappedSelect, _WrappedElement);

        function WrappedSelect(_ref) {
          var _this;

          var element = _ref.element,
              classNames = _ref.classNames,
              template = _ref.template;
          _this = _WrappedElement.call(this, {
            element: element,
            classNames: classNames
          }) || this;
          _this.template = template;
          return _this;
        }

        var _proto = WrappedSelect.prototype;

        _proto.appendDocFragment = function appendDocFragment(fragment) {
          this.element.innerHTML = '';
          this.element.appendChild(fragment);
        };

        wrapped_select_createClass(WrappedSelect, [{
          key: "placeholderOption",
          get: function get() {
            return this.element.querySelector('option[value=""]') || // Backward compatibility layer for the non-standard placeholder attribute supported in older versions.
            this.element.querySelector('option[placeholder]');
          }
        }, {
          key: "optionGroups",
          get: function get() {
            return Array.from(this.element.getElementsByTagName('OPTGROUP'));
          }
        }, {
          key: "options",
          get: function get() {
            return Array.from(this.element.options);
          },
          set: function set(options) {
            var _this2 = this;

            var fragment = document.createDocumentFragment();

            var addOptionToFragment = function addOptionToFragment(data) {
              // Create a standard select option
              var option = _this2.template(data); // Append it to fragment


              fragment.appendChild(option);
            }; // Add each list item to list


            options.forEach(function (optionData) {
              return addOptionToFragment(optionData);
            });
            this.appendDocFragment(fragment);
          }
        }]);
        return WrappedSelect;
      }(wrapped_element_WrappedElement); // CONCATENATED MODULE: ./src/scripts/components/index.js
      // EXTERNAL MODULE: ./node_modules/classnames/index.js


      var classnames = __webpack_require__(0);

      var classnames_default = /*#__PURE__*/__webpack_require__.n(classnames); // CONCATENATED MODULE: ./src/scripts/templates.js


      var TEMPLATES = {
        containerOuter: function containerOuter(globalClasses, direction, isSelectElement, isSelectOneElement, searchEnabled, passedElementType) {
          var tabIndex = isSelectOneElement ? 'tabindex="0"' : '';
          var role = isSelectElement ? 'role="listbox"' : '';
          var ariaAutoComplete = '';

          if (isSelectElement && searchEnabled) {
            role = 'role="combobox"';
            ariaAutoComplete = 'aria-autocomplete="list"';
          }

          return strToEl("\n      <div\n        class=\"" + globalClasses.containerOuter + "\"\n        data-type=\"" + passedElementType + "\"\n        " + role + "\n        " + tabIndex + "\n        " + ariaAutoComplete + "\n        aria-haspopup=\"true\"\n        aria-expanded=\"false\"\n        dir=\"" + direction + "\"\n        >\n      </div>\n    ");
        },
        containerInner: function containerInner(globalClasses) {
          return strToEl("\n      <div class=\"" + globalClasses.containerInner + "\"></div>\n    ");
        },
        itemList: function itemList(globalClasses, isSelectOneElement) {
          var _classNames;

          var localClasses = classnames_default()(globalClasses.list, (_classNames = {}, _classNames[globalClasses.listSingle] = isSelectOneElement, _classNames[globalClasses.listItems] = !isSelectOneElement, _classNames));
          return strToEl("\n      <div class=\"" + localClasses + "\"></div>\n    ");
        },
        placeholder: function placeholder(globalClasses, value) {
          return strToEl("\n      <div class=\"" + globalClasses.placeholder + "\">\n        " + value + "\n      </div>\n    ");
        },
        item: function item(globalClasses, data, removeItemButton) {
          var _classNames2;

          var ariaSelected = data.active ? 'aria-selected="true"' : '';
          var ariaDisabled = data.disabled ? 'aria-disabled="true"' : '';
          var localClasses = classnames_default()(globalClasses.item, (_classNames2 = {}, _classNames2[globalClasses.highlightedState] = data.highlighted, _classNames2[globalClasses.itemSelectable] = !data.highlighted, _classNames2[globalClasses.placeholder] = data.placeholder, _classNames2));

          if (removeItemButton) {
            var _classNames3;

            localClasses = classnames_default()(globalClasses.item, (_classNames3 = {}, _classNames3[globalClasses.highlightedState] = data.highlighted, _classNames3[globalClasses.itemSelectable] = !data.disabled, _classNames3[globalClasses.placeholder] = data.placeholder, _classNames3));
            return strToEl("\n        <div\n          class=\"" + localClasses + "\"\n          data-item\n          data-id=\"" + data.id + "\"\n          data-value=\"" + data.value + "\"\n          data-custom-properties='" + data.customProperties + "'\n          data-deletable\n          " + ariaSelected + "\n          " + ariaDisabled + "\n          >\n          " + data.label + "<!--\n       --><button\n            type=\"button\"\n            class=\"" + globalClasses.button + "\"\n            data-button\n            aria-label=\"Remove item: '" + data.value + "'\"\n            >\n            Remove item\n          </button>\n        </div>\n      ");
          }

          return strToEl("\n      <div\n        class=\"" + localClasses + "\"\n        data-item\n        data-id=\"" + data.id + "\"\n        data-value=\"" + data.value + "\"\n        " + ariaSelected + "\n        " + ariaDisabled + "\n        >\n        " + data.label + "\n      </div>\n    ");
        },
        choiceList: function choiceList(globalClasses, isSelectOneElement) {
          var ariaMultiSelectable = !isSelectOneElement ? 'aria-multiselectable="true"' : '';
          return strToEl("\n      <div\n        class=\"" + globalClasses.list + "\"\n        dir=\"ltr\"\n        role=\"listbox\"\n        " + ariaMultiSelectable + "\n        >\n      </div>\n    ");
        },
        choiceGroup: function choiceGroup(globalClasses, data) {
          var _classNames4;

          var ariaDisabled = data.disabled ? 'aria-disabled="true"' : '';
          var localClasses = classnames_default()(globalClasses.group, (_classNames4 = {}, _classNames4[globalClasses.itemDisabled] = data.disabled, _classNames4));
          return strToEl("\n      <div\n        class=\"" + localClasses + "\"\n        data-group\n        data-id=\"" + data.id + "\"\n        data-value=\"" + data.value + "\"\n        role=\"group\"\n        " + ariaDisabled + "\n        >\n        <div class=\"" + globalClasses.groupHeading + "\">" + data.value + "</div>\n      </div>\n    ");
        },
        choice: function choice(globalClasses, data, itemSelectText) {
          var _classNames5;

          var role = data.groupId > 0 ? 'role="treeitem"' : 'role="option"';
          var localClasses = classnames_default()(globalClasses.item, globalClasses.itemChoice, (_classNames5 = {}, _classNames5[globalClasses.itemDisabled] = data.disabled, _classNames5[globalClasses.itemSelectable] = !data.disabled, _classNames5[globalClasses.placeholder] = data.placeholder, _classNames5));
          return strToEl("\n      <div\n        class=\"" + localClasses + "\"\n        data-select-text=\"" + itemSelectText + "\"\n        data-choice\n        data-id=\"" + data.id + "\"\n        data-value=\"" + data.value + "\"\n        " + (data.disabled ? 'data-choice-disabled aria-disabled="true"' : 'data-choice-selectable') + "\n        id=\"" + data.elementId + "\"\n        " + role + "\n        >\n        " + data.label + "\n      </div>\n    ");
        },
        input: function input(globalClasses, placeholderValue) {
          var localClasses = classnames_default()(globalClasses.input, globalClasses.inputCloned);
          return strToEl("\n      <input\n        type=\"text\"\n        class=\"" + localClasses + "\"\n        autocomplete=\"off\"\n        autocapitalize=\"off\"\n        spellcheck=\"false\"\n        role=\"textbox\"\n        aria-autocomplete=\"list\"\n        aria-label=\"" + placeholderValue + "\"\n        >\n    ");
        },
        dropdown: function dropdown(globalClasses) {
          var localClasses = classnames_default()(globalClasses.list, globalClasses.listDropdown);
          return strToEl("\n      <div\n        class=\"" + localClasses + "\"\n        aria-expanded=\"false\"\n        >\n      </div>\n    ");
        },
        notice: function notice(globalClasses, label, type) {
          var _classNames6;

          if (type === void 0) {
            type = '';
          }

          var localClasses = classnames_default()(globalClasses.item, globalClasses.itemChoice, (_classNames6 = {}, _classNames6[globalClasses.noResults] = type === 'no-results', _classNames6[globalClasses.noChoices] = type === 'no-choices', _classNames6));
          return strToEl("\n      <div class=\"" + localClasses + "\">\n        " + label + "\n      </div>\n    ");
        },
        option: function option(data) {
          return strToEl("\n      <option value=\"" + data.value + "\" " + (data.active ? 'selected' : '') + " " + (data.disabled ? 'disabled' : '') + " " + (data.customProperties ? "data-custom-properties=" + data.customProperties : '') + ">" + data.label + "</option>\n    ");
        }
      };
      /* harmony default export */

      var templates = TEMPLATES; // CONCATENATED MODULE: ./src/scripts/actions/choices.js

      var choices_addChoice = function addChoice(_ref) {
        var value = _ref.value,
            label = _ref.label,
            id = _ref.id,
            groupId = _ref.groupId,
            disabled = _ref.disabled,
            elementId = _ref.elementId,
            customProperties = _ref.customProperties,
            placeholder = _ref.placeholder,
            keyCode = _ref.keyCode;
        return {
          type: ACTION_TYPES.ADD_CHOICE,
          value: value,
          label: label,
          id: id,
          groupId: groupId,
          disabled: disabled,
          elementId: elementId,
          customProperties: customProperties,
          placeholder: placeholder,
          keyCode: keyCode
        };
      };

      var choices_filterChoices = function filterChoices(results) {
        return {
          type: ACTION_TYPES.FILTER_CHOICES,
          results: results
        };
      };

      var choices_activateChoices = function activateChoices(active) {
        if (active === void 0) {
          active = true;
        }

        return {
          type: ACTION_TYPES.ACTIVATE_CHOICES,
          active: active
        };
      };

      var choices_clearChoices = function clearChoices() {
        return {
          type: ACTION_TYPES.CLEAR_CHOICES
        };
      }; // CONCATENATED MODULE: ./src/scripts/actions/items.js


      var items_addItem = function addItem(_ref) {
        var value = _ref.value,
            label = _ref.label,
            id = _ref.id,
            choiceId = _ref.choiceId,
            groupId = _ref.groupId,
            customProperties = _ref.customProperties,
            placeholder = _ref.placeholder,
            keyCode = _ref.keyCode;
        return {
          type: ACTION_TYPES.ADD_ITEM,
          value: value,
          label: label,
          id: id,
          choiceId: choiceId,
          groupId: groupId,
          customProperties: customProperties,
          placeholder: placeholder,
          keyCode: keyCode
        };
      };

      var items_removeItem = function removeItem(id, choiceId) {
        return {
          type: ACTION_TYPES.REMOVE_ITEM,
          id: id,
          choiceId: choiceId
        };
      };

      var items_highlightItem = function highlightItem(id, highlighted) {
        return {
          type: ACTION_TYPES.HIGHLIGHT_ITEM,
          id: id,
          highlighted: highlighted
        };
      }; // CONCATENATED MODULE: ./src/scripts/actions/groups.js

      /* eslint-disable import/prefer-default-export */


      var groups_addGroup = function addGroup(value, id, active, disabled) {
        return {
          type: ACTION_TYPES.ADD_GROUP,
          value: value,
          id: id,
          active: active,
          disabled: disabled
        };
      }; // CONCATENATED MODULE: ./src/scripts/actions/misc.js


      var clearAll = function clearAll() {
        return {
          type: 'CLEAR_ALL'
        };
      };

      var resetTo = function resetTo(state) {
        return {
          type: 'RESET_TO',
          state: state
        };
      }; // CONCATENATED MODULE: ./src/scripts/actions/general.js

      /* eslint-disable import/prefer-default-export */


      var setIsLoading = function setIsLoading(isLoading) {
        return {
          type: 'SET_IS_LOADING',
          isLoading: isLoading
        };
      }; // CONCATENATED MODULE: ./src/scripts/choices.js

      /**
       * Choices
       * @author Josh Johnson<josh@joshuajohnson.co.uk>
       */


      var choices_Choices = /*#__PURE__*/function () {
        function Choices(element, userConfig) {
          var _this = this;

          if (element === void 0) {
            element = '[data-choice]';
          }

          if (userConfig === void 0) {
            userConfig = {};
          }

          if (isType('String', element)) {
            var elements = Array.from(document.querySelectorAll(element)); // If there are multiple elements, create a new instance
            // for each element besides the first one (as that already has an instance)

            if (elements.length > 1) {
              return this._generateInstances(elements, userConfig);
            }
          }

          this.config = cjs_default.a.all([DEFAULT_CONFIG, Choices.userDefaults, userConfig], // When merging array configs, replace with a copy of the userConfig array,
          // instead of concatenating with the default array
          {
            arrayMerge: function arrayMerge(destinationArray, sourceArray) {
              return [].concat(sourceArray);
            }
          });
          var invalidConfigOptions = diff(this.config, DEFAULT_CONFIG);

          if (invalidConfigOptions.length) {
            console.warn('Unknown config option(s) passed', invalidConfigOptions.join(', '));
          }

          if (!['auto', 'always'].includes(this.config.renderSelectedChoices)) {
            this.config.renderSelectedChoices = 'auto';
          } // Retrieve triggering element (i.e. element with 'data-choice' trigger)


          var passedElement = isType('String', element) ? document.querySelector(element) : element;

          if (!passedElement) {
            if (!this.config.silent) {
              console.error('Could not find passed element or passed element was of an invalid type');
            }

            return;
          }

          this._isTextElement = passedElement.type === 'text';
          this._isSelectOneElement = passedElement.type === 'select-one';
          this._isSelectMultipleElement = passedElement.type === 'select-multiple';
          this._isSelectElement = this._isSelectOneElement || this._isSelectMultipleElement;

          if (this._isTextElement) {
            this.passedElement = new WrappedInput({
              element: passedElement,
              classNames: this.config.classNames,
              delimiter: this.config.delimiter
            });
          } else if (this._isSelectElement) {
            this.passedElement = new WrappedSelect({
              element: passedElement,
              classNames: this.config.classNames,
              template: function template(data) {
                return _this.config.templates.option(data);
              }
            });
          }

          if (!this.passedElement) {
            return console.error('Passed element was of an invalid type');
          }

          this.initialised = false;
          this._store = new store_Store(this.render);
          this._initialState = {};
          this._currentState = {};
          this._prevState = {};
          this._currentValue = '';
          this._canSearch = this.config.searchEnabled;
          this._isScrollingOnIe = false;
          this._highlightPosition = 0;
          this._wasTap = true;
          this._placeholderValue = this._generatePlaceholderValue();
          this._baseId = generateId(this.passedElement.element, 'choices-');
          this._direction = this.passedElement.element.getAttribute('dir') || 'ltr';
          this._idNames = {
            itemChoice: 'item-choice'
          }; // Assign preset choices from passed object

          this._presetChoices = this.config.choices; // Assign preset items from passed object first

          this._presetItems = this.config.items; // Then add any values passed from attribute

          if (this.passedElement.value) {
            this._presetItems = this._presetItems.concat(this.passedElement.value.split(this.config.delimiter));
          }

          this._render = this._render.bind(this);
          this._onFocus = this._onFocus.bind(this);
          this._onBlur = this._onBlur.bind(this);
          this._onKeyUp = this._onKeyUp.bind(this);
          this._onKeyDown = this._onKeyDown.bind(this);
          this._onClick = this._onClick.bind(this);
          this._onTouchMove = this._onTouchMove.bind(this);
          this._onTouchEnd = this._onTouchEnd.bind(this);
          this._onMouseDown = this._onMouseDown.bind(this);
          this._onMouseOver = this._onMouseOver.bind(this);
          this._onFormReset = this._onFormReset.bind(this);
          this._onAKey = this._onAKey.bind(this);
          this._onEnterKey = this._onEnterKey.bind(this);
          this._onEscapeKey = this._onEscapeKey.bind(this);
          this._onDirectionKey = this._onDirectionKey.bind(this);
          this._onDeleteKey = this._onDeleteKey.bind(this);

          if (!this.config.silent) {
            if (this.config.shouldSortItems === true && this._isSelectOneElement) {
              console.warn("shouldSortElements: Type of passed element is 'select-one', falling back to false.");
            } // If element has already been initialised with Choices, fail silently


            if (this.passedElement.element.getAttribute('data-choice') === 'active') {
              console.warn('Trying to initialise Choices on element already initialised');
            }
          } // Let's go


          this.init();
        }
        /* ========================================
        =            Public functions            =
        ======================================== */


        var _proto = Choices.prototype;

        _proto.init = function init() {
          if (this.initialised) {
            return;
          }

          this._createTemplates();

          this._createElements();

          this._createStructure(); // Set initial state (We need to clone the state because some reducers
          // modify the inner objects properties in the state) 🤢


          this._initialState = cloneObject(this._store.state);

          this._store.subscribe(this._render);

          this._render();

          this._addEventListeners();

          var shouldDisable = !this.config.addItems || this.passedElement.element.hasAttribute('disabled');

          if (shouldDisable) {
            this.disable();
          }

          this.initialised = true;
          var callbackOnInit = this.config.callbackOnInit; // Run callback if it is a function

          if (callbackOnInit && isType('Function', callbackOnInit)) {
            callbackOnInit.call(this);
          }
        };

        _proto.destroy = function destroy() {
          if (!this.initialised) {
            return;
          }

          this._removeEventListeners();

          this.passedElement.reveal();
          this.containerOuter.unwrap(this.passedElement.element);

          if (this._isSelectElement) {
            this.passedElement.options = this._presetChoices;
          }

          this.clearStore();
          this.config.templates = null;
          this.initialised = false;
        };

        _proto.enable = function enable() {
          if (this.passedElement.isDisabled) {
            this.passedElement.enable();
          }

          if (this.containerOuter.isDisabled) {
            this._addEventListeners();

            this.input.enable();
            this.containerOuter.enable();
          }

          return this;
        };

        _proto.disable = function disable() {
          if (!this.passedElement.isDisabled) {
            this.passedElement.disable();
          }

          if (!this.containerOuter.isDisabled) {
            this._removeEventListeners();

            this.input.disable();
            this.containerOuter.disable();
          }

          return this;
        };

        _proto.highlightItem = function highlightItem(item, runEvent) {
          if (runEvent === void 0) {
            runEvent = true;
          }

          if (!item) {
            return this;
          }

          var id = item.id,
              _item$groupId = item.groupId,
              groupId = _item$groupId === void 0 ? -1 : _item$groupId,
              _item$value = item.value,
              value = _item$value === void 0 ? '' : _item$value,
              _item$label = item.label,
              label = _item$label === void 0 ? '' : _item$label;
          var group = groupId >= 0 ? this._store.getGroupById(groupId) : null;

          this._store.dispatch(items_highlightItem(id, true));

          if (runEvent) {
            this.passedElement.triggerEvent(EVENTS.highlightItem, {
              id: id,
              value: value,
              label: label,
              groupValue: group && group.value ? group.value : null
            });
          }

          return this;
        };

        _proto.unhighlightItem = function unhighlightItem(item) {
          if (!item) {
            return this;
          }

          var id = item.id,
              _item$groupId2 = item.groupId,
              groupId = _item$groupId2 === void 0 ? -1 : _item$groupId2,
              _item$value2 = item.value,
              value = _item$value2 === void 0 ? '' : _item$value2,
              _item$label2 = item.label,
              label = _item$label2 === void 0 ? '' : _item$label2;
          var group = groupId >= 0 ? this._store.getGroupById(groupId) : null;

          this._store.dispatch(items_highlightItem(id, false));

          this.passedElement.triggerEvent(EVENTS.highlightItem, {
            id: id,
            value: value,
            label: label,
            groupValue: group && group.value ? group.value : null
          });
          return this;
        };

        _proto.highlightAll = function highlightAll() {
          var _this2 = this;

          this._store.items.forEach(function (item) {
            return _this2.highlightItem(item);
          });

          return this;
        };

        _proto.unhighlightAll = function unhighlightAll() {
          var _this3 = this;

          this._store.items.forEach(function (item) {
            return _this3.unhighlightItem(item);
          });

          return this;
        };

        _proto.removeActiveItemsByValue = function removeActiveItemsByValue(value) {
          var _this4 = this;

          this._store.activeItems.filter(function (item) {
            return item.value === value;
          }).forEach(function (item) {
            return _this4._removeItem(item);
          });

          return this;
        };

        _proto.removeActiveItems = function removeActiveItems(excludedId) {
          var _this5 = this;

          this._store.activeItems.filter(function (_ref) {
            var id = _ref.id;
            return id !== excludedId;
          }).forEach(function (item) {
            return _this5._removeItem(item);
          });

          return this;
        };

        _proto.removeHighlightedItems = function removeHighlightedItems(runEvent) {
          var _this6 = this;

          if (runEvent === void 0) {
            runEvent = false;
          }

          this._store.highlightedActiveItems.forEach(function (item) {
            _this6._removeItem(item); // If this action was performed by the user
            // trigger the event


            if (runEvent) {
              _this6._triggerChange(item.value);
            }
          });

          return this;
        };

        _proto.showDropdown = function showDropdown(preventInputFocus) {
          var _this7 = this;

          if (this.dropdown.isActive) {
            return this;
          }

          requestAnimationFrame(function () {
            _this7.dropdown.show();

            _this7.containerOuter.open(_this7.dropdown.distanceFromTopWindow());

            if (!preventInputFocus && _this7._canSearch) {
              _this7.input.focus();
            }

            _this7.passedElement.triggerEvent(EVENTS.showDropdown, {});
          });
          return this;
        };

        _proto.hideDropdown = function hideDropdown(preventInputBlur) {
          var _this8 = this;

          if (!this.dropdown.isActive) {
            return this;
          }

          requestAnimationFrame(function () {
            _this8.dropdown.hide();

            _this8.containerOuter.close();

            if (!preventInputBlur && _this8._canSearch) {
              _this8.input.removeActiveDescendant();

              _this8.input.blur();
            }

            _this8.passedElement.triggerEvent(EVENTS.hideDropdown, {});
          });
          return this;
        };

        _proto.getValue = function getValue(valueOnly) {
          if (valueOnly === void 0) {
            valueOnly = false;
          }

          var values = this._store.activeItems.reduce(function (selectedItems, item) {
            var itemValue = valueOnly ? item.value : item;
            selectedItems.push(itemValue);
            return selectedItems;
          }, []);

          return this._isSelectOneElement ? values[0] : values;
        };

        _proto.setValue = function setValue(args) {
          var _this9 = this;

          if (!this.initialised) {
            return this;
          }

          [].concat(args).forEach(function (value) {
            return _this9._setChoiceOrItem(value);
          });
          return this;
        };

        _proto.setChoiceByValue = function setChoiceByValue(value) {
          var _this10 = this;

          if (!this.initialised || this._isTextElement) {
            return this;
          } // If only one value has been passed, convert to array


          var choiceValue = isType('Array', value) ? value : [value]; // Loop through each value and

          choiceValue.forEach(function (val) {
            return _this10._findAndSelectChoiceByValue(val);
          });
          return this;
        };

        _proto.setChoices = function setChoices(choices, value, label, replaceChoices) {
          var _this11 = this;

          if (choices === void 0) {
            choices = [];
          }

          if (value === void 0) {
            value = '';
          }

          if (label === void 0) {
            label = '';
          }

          if (replaceChoices === void 0) {
            replaceChoices = false;
          }

          if (!this._isSelectElement || !value) {
            return this;
          } // Clear choices if needed


          if (replaceChoices) {
            this.clearChoices();
          }

          this.containerOuter.removeLoadingState();

          var addGroupsAndChoices = function addGroupsAndChoices(groupOrChoice) {
            if (groupOrChoice.choices) {
              _this11._addGroup({
                group: groupOrChoice,
                id: groupOrChoice.id || null,
                valueKey: value,
                labelKey: label
              });
            } else {
              _this11._addChoice({
                value: groupOrChoice[value],
                label: groupOrChoice[label],
                isSelected: groupOrChoice.selected,
                isDisabled: groupOrChoice.disabled,
                customProperties: groupOrChoice.customProperties,
                placeholder: groupOrChoice.placeholder
              });
            }
          };

          this._setLoading(true);

          choices.forEach(addGroupsAndChoices);

          this._setLoading(false);

          return this;
        };

        _proto.clearChoices = function clearChoices() {
          this._store.dispatch(choices_clearChoices());
        };

        _proto.clearStore = function clearStore() {
          this._store.dispatch(clearAll());

          return this;
        };

        _proto.clearInput = function clearInput() {
          var shouldSetInputWidth = !this._isSelectOneElement;
          this.input.clear(shouldSetInputWidth);

          if (!this._isTextElement && this._canSearch) {
            this._isSearching = false;

            this._store.dispatch(choices_activateChoices(true));
          }

          return this;
        };

        _proto.ajax = function ajax(fn) {
          var _this12 = this;

          if (!this.initialised || !this._isSelectElement || !fn) {
            return this;
          }

          requestAnimationFrame(function () {
            return _this12._handleLoadingState(true);
          });
          fn(this._ajaxCallback());
          return this;
        }
        /* =====  End of Public functions  ====== */

        /* =============================================
        =                Private functions            =
        ============================================= */
        ;

        _proto._render = function _render() {
          if (this._store.isLoading()) {
            return;
          }

          this._currentState = this._store.state;
          var stateChanged = this._currentState.choices !== this._prevState.choices || this._currentState.groups !== this._prevState.groups || this._currentState.items !== this._prevState.items;
          var shouldRenderChoices = this._isSelectElement;
          var shouldRenderItems = this._currentState.items !== this._prevState.items;

          if (!stateChanged) {
            return;
          }

          if (shouldRenderChoices) {
            this._renderChoices();
          }

          if (shouldRenderItems) {
            this._renderItems();
          }

          this._prevState = this._currentState;
        };

        _proto._renderChoices = function _renderChoices() {
          var _this13 = this;

          var _this$_store = this._store,
              activeGroups = _this$_store.activeGroups,
              activeChoices = _this$_store.activeChoices;
          var choiceListFragment = document.createDocumentFragment();
          this.choiceList.clear();

          if (this.config.resetScrollPosition) {
            requestAnimationFrame(function () {
              return _this13.choiceList.scrollToTop();
            });
          } // If we have grouped options


          if (activeGroups.length >= 1 && !this._isSearching) {
            // If we have a placeholder choice along with groups
            var activePlaceholders = activeChoices.filter(function (activeChoice) {
              return activeChoice.placeholder === true && activeChoice.groupId === -1;
            });

            if (activePlaceholders.length >= 1) {
              choiceListFragment = this._createChoicesFragment(activePlaceholders, choiceListFragment);
            }

            choiceListFragment = this._createGroupsFragment(activeGroups, activeChoices, choiceListFragment);
          } else if (activeChoices.length >= 1) {
            choiceListFragment = this._createChoicesFragment(activeChoices, choiceListFragment);
          } // If we have choices to show


          if (choiceListFragment.childNodes && choiceListFragment.childNodes.length > 0) {
            var activeItems = this._store.activeItems;

            var canAddItem = this._canAddItem(activeItems, this.input.value); // ...and we can select them


            if (canAddItem.response) {
              // ...append them and highlight the first choice
              this.choiceList.append(choiceListFragment);

              this._highlightChoice();
            } else {
              // ...otherwise show a notice
              this.choiceList.append(this._getTemplate('notice', canAddItem.notice));
            }
          } else {
            // Otherwise show a notice
            var dropdownItem;
            var notice;

            if (this._isSearching) {
              notice = isType('Function', this.config.noResultsText) ? this.config.noResultsText() : this.config.noResultsText;
              dropdownItem = this._getTemplate('notice', notice, 'no-results');
            } else {
              notice = isType('Function', this.config.noChoicesText) ? this.config.noChoicesText() : this.config.noChoicesText;
              dropdownItem = this._getTemplate('notice', notice, 'no-choices');
            }

            this.choiceList.append(dropdownItem);
          }
        };

        _proto._renderItems = function _renderItems() {
          var activeItems = this._store.activeItems || [];
          this.itemList.clear(); // Create a fragment to store our list items
          // (so we don't have to update the DOM for each item)

          var itemListFragment = this._createItemsFragment(activeItems); // If we have items to add, append them


          if (itemListFragment.childNodes) {
            this.itemList.append(itemListFragment);
          }
        };

        _proto._createGroupsFragment = function _createGroupsFragment(groups, choices, fragment) {
          var _this14 = this;

          var groupFragment = fragment || document.createDocumentFragment();

          var getGroupChoices = function getGroupChoices(group) {
            return choices.filter(function (choice) {
              if (_this14._isSelectOneElement) {
                return choice.groupId === group.id;
              }

              return choice.groupId === group.id && (_this14.config.renderSelectedChoices === 'always' || !choice.selected);
            });
          }; // If sorting is enabled, filter groups


          if (this.config.shouldSort) {
            groups.sort(this.config.sortFn);
          }

          groups.forEach(function (group) {
            var groupChoices = getGroupChoices(group);

            if (groupChoices.length >= 1) {
              var dropdownGroup = _this14._getTemplate('choiceGroup', group);

              groupFragment.appendChild(dropdownGroup);

              _this14._createChoicesFragment(groupChoices, groupFragment, true);
            }
          });
          return groupFragment;
        };

        _proto._createChoicesFragment = function _createChoicesFragment(choices, fragment, withinGroup) {
          var _this15 = this;

          if (withinGroup === void 0) {
            withinGroup = false;
          } // Create a fragment to store our list items (so we don't have to update the DOM for each item)


          var choicesFragment = fragment || document.createDocumentFragment();
          var _this$config = this.config,
              renderSelectedChoices = _this$config.renderSelectedChoices,
              searchResultLimit = _this$config.searchResultLimit,
              renderChoiceLimit = _this$config.renderChoiceLimit;
          var filter = this._isSearching ? sortByScore : this.config.sortFn;

          var appendChoice = function appendChoice(choice) {
            var shouldRender = renderSelectedChoices === 'auto' ? _this15._isSelectOneElement || !choice.selected : true;

            if (shouldRender) {
              var dropdownItem = _this15._getTemplate('choice', choice, _this15.config.itemSelectText);

              choicesFragment.appendChild(dropdownItem);
            }
          };

          var rendererableChoices = choices;

          if (renderSelectedChoices === 'auto' && !this._isSelectOneElement) {
            rendererableChoices = choices.filter(function (choice) {
              return !choice.selected;
            });
          } // Split array into placeholders and "normal" choices


          var _rendererableChoices$ = rendererableChoices.reduce(function (acc, choice) {
            if (choice.placeholder) {
              acc.placeholderChoices.push(choice);
            } else {
              acc.normalChoices.push(choice);
            }

            return acc;
          }, {
            placeholderChoices: [],
            normalChoices: []
          }),
              placeholderChoices = _rendererableChoices$.placeholderChoices,
              normalChoices = _rendererableChoices$.normalChoices; // If sorting is enabled or the user is searching, filter choices


          if (this.config.shouldSort || this._isSearching) {
            normalChoices.sort(filter);
          }

          var choiceLimit = rendererableChoices.length; // Prepend placeholeder

          var sortedChoices = [].concat(placeholderChoices, normalChoices);

          if (this._isSearching) {
            choiceLimit = searchResultLimit;
          } else if (renderChoiceLimit > 0 && !withinGroup) {
            choiceLimit = renderChoiceLimit;
          } // Add each choice to dropdown within range


          for (var i = 0; i < choiceLimit; i += 1) {
            if (sortedChoices[i]) {
              appendChoice(sortedChoices[i]);
            }
          }

          return choicesFragment;
        };

        _proto._createItemsFragment = function _createItemsFragment(items, fragment) {
          var _this16 = this;

          if (fragment === void 0) {
            fragment = null;
          } // Create fragment to add elements to


          var _this$config2 = this.config,
              shouldSortItems = _this$config2.shouldSortItems,
              sortFn = _this$config2.sortFn,
              removeItemButton = _this$config2.removeItemButton;
          var itemListFragment = fragment || document.createDocumentFragment(); // If sorting is enabled, filter items

          if (shouldSortItems && !this._isSelectOneElement) {
            items.sort(sortFn);
          }

          if (this._isTextElement) {
            // Update the value of the hidden input
            this.passedElement.value = items;
          } else {
            // Update the options of the hidden input
            this.passedElement.options = items;
          }

          var addItemToFragment = function addItemToFragment(item) {
            // Create new list element
            var listItem = _this16._getTemplate('item', item, removeItemButton); // Append it to list


            itemListFragment.appendChild(listItem);
          }; // Add each list item to list


          items.forEach(function (item) {
            return addItemToFragment(item);
          });
          return itemListFragment;
        };

        _proto._triggerChange = function _triggerChange(value) {
          if (value === undefined || value === null) {
            return;
          }

          this.passedElement.triggerEvent(EVENTS.change, {
            value: value
          });
        };

        _proto._selectPlaceholderChoice = function _selectPlaceholderChoice() {
          var placeholderChoice = this._store.placeholderChoice;

          if (placeholderChoice) {
            this._addItem({
              value: placeholderChoice.value,
              label: placeholderChoice.label,
              choiceId: placeholderChoice.id,
              groupId: placeholderChoice.groupId,
              placeholder: placeholderChoice.placeholder
            });

            this._triggerChange(placeholderChoice.value);
          }
        };

        _proto._handleButtonAction = function _handleButtonAction(activeItems, element) {
          if (!activeItems || !element || !this.config.removeItems || !this.config.removeItemButton) {
            return;
          }

          var itemId = element.parentNode.getAttribute('data-id');
          var itemToRemove = activeItems.find(function (item) {
            return item.id === parseInt(itemId, 10);
          }); // Remove item associated with button

          this._removeItem(itemToRemove);

          this._triggerChange(itemToRemove.value);

          if (this._isSelectOneElement) {
            this._selectPlaceholderChoice();
          }
        };

        _proto._handleItemAction = function _handleItemAction(activeItems, element, hasShiftKey) {
          var _this17 = this;

          if (hasShiftKey === void 0) {
            hasShiftKey = false;
          }

          if (!activeItems || !element || !this.config.removeItems || this._isSelectOneElement) {
            return;
          }

          var passedId = element.getAttribute('data-id'); // We only want to select one item with a click
          // so we deselect any items that aren't the target
          // unless shift is being pressed

          activeItems.forEach(function (item) {
            if (item.id === parseInt(passedId, 10) && !item.highlighted) {
              _this17.highlightItem(item);
            } else if (!hasShiftKey && item.highlighted) {
              _this17.unhighlightItem(item);
            }
          }); // Focus input as without focus, a user cannot do anything with a
          // highlighted item

          this.input.focus();
        };

        _proto._handleChoiceAction = function _handleChoiceAction(activeItems, element) {
          if (!activeItems || !element) {
            return;
          } // If we are clicking on an option


          var id = element.getAttribute('data-id');

          var choice = this._store.getChoiceById(id);

          var passedKeyCode = activeItems[0] && activeItems[0].keyCode ? activeItems[0].keyCode : null;
          var hasActiveDropdown = this.dropdown.isActive; // Update choice keyCode

          choice.keyCode = passedKeyCode;
          this.passedElement.triggerEvent(EVENTS.choice, {
            choice: choice
          });

          if (choice && !choice.selected && !choice.disabled) {
            var canAddItem = this._canAddItem(activeItems, choice.value);

            if (canAddItem.response) {
              this._addItem({
                value: choice.value,
                label: choice.label,
                choiceId: choice.id,
                groupId: choice.groupId,
                customProperties: choice.customProperties,
                placeholder: choice.placeholder,
                keyCode: choice.keyCode
              });

              this._triggerChange(choice.value);
            }
          }

          this.clearInput(); // We wont to close the dropdown if we are dealing with a single select box

          if (hasActiveDropdown && this._isSelectOneElement) {
            this.hideDropdown(true);
            this.containerOuter.focus();
          }
        };

        _proto._handleBackspace = function _handleBackspace(activeItems) {
          if (!this.config.removeItems || !activeItems) {
            return;
          }

          var lastItem = activeItems[activeItems.length - 1];
          var hasHighlightedItems = activeItems.some(function (item) {
            return item.highlighted;
          }); // If editing the last item is allowed and there are not other selected items,
          // we can edit the item value. Otherwise if we can remove items, remove all selected items

          if (this.config.editItems && !hasHighlightedItems && lastItem) {
            this.input.value = lastItem.value;
            this.input.setWidth();

            this._removeItem(lastItem);

            this._triggerChange(lastItem.value);
          } else {
            if (!hasHighlightedItems) {
              // Highlight last item if none already highlighted
              this.highlightItem(lastItem, false);
            }

            this.removeHighlightedItems(true);
          }
        };

        _proto._setLoading = function _setLoading(isLoading) {
          this._store.dispatch(setIsLoading(isLoading));
        };

        _proto._handleLoadingState = function _handleLoadingState(setLoading) {
          if (setLoading === void 0) {
            setLoading = true;
          }

          var placeholderItem = this.itemList.getChild("." + this.config.classNames.placeholder);

          if (setLoading) {
            this.disable();
            this.containerOuter.addLoadingState();

            if (this._isSelectOneElement) {
              if (!placeholderItem) {
                placeholderItem = this._getTemplate('placeholder', this.config.loadingText);
                this.itemList.append(placeholderItem);
              } else {
                placeholderItem.innerHTML = this.config.loadingText;
              }
            } else {
              this.input.placeholder = this.config.loadingText;
            }
          } else {
            this.enable();
            this.containerOuter.removeLoadingState();

            if (this._isSelectOneElement) {
              placeholderItem.innerHTML = this._placeholderValue || '';
            } else {
              this.input.placeholder = this._placeholderValue || '';
            }
          }
        };

        _proto._handleSearch = function _handleSearch(value) {
          if (!value || !this.input.isFocussed) {
            return;
          }

          var choices = this._store.choices;
          var _this$config3 = this.config,
              searchFloor = _this$config3.searchFloor,
              searchChoices = _this$config3.searchChoices;
          var hasUnactiveChoices = choices.some(function (option) {
            return !option.active;
          }); // Check that we have a value to search and the input was an alphanumeric character

          if (value && value.length >= searchFloor) {
            var resultCount = searchChoices ? this._searchChoices(value) : 0; // Trigger search event

            this.passedElement.triggerEvent(EVENTS.search, {
              value: value,
              resultCount: resultCount
            });
          } else if (hasUnactiveChoices) {
            // Otherwise reset choices to active
            this._isSearching = false;

            this._store.dispatch(choices_activateChoices(true));
          }
        };

        _proto._canAddItem = function _canAddItem(activeItems, value) {
          var canAddItem = true;
          var notice = isType('Function', this.config.addItemText) ? this.config.addItemText(value) : this.config.addItemText;

          if (!this._isSelectOneElement) {
            var isDuplicateValue = existsInArray(activeItems, value);

            if (this.config.maxItemCount > 0 && this.config.maxItemCount <= activeItems.length) {
              // If there is a max entry limit and we have reached that limit
              // don't update
              canAddItem = false;
              notice = isType('Function', this.config.maxItemText) ? this.config.maxItemText(this.config.maxItemCount) : this.config.maxItemText;
            }

            if (!this.config.duplicateItemsAllowed && isDuplicateValue && canAddItem) {
              canAddItem = false;
              notice = isType('Function', this.config.uniqueItemText) ? this.config.uniqueItemText(value) : this.config.uniqueItemText;
            }

            if (this._isTextElement && this.config.addItems && canAddItem && isType('Function', this.config.addItemFilterFn) && !this.config.addItemFilterFn(value)) {
              canAddItem = false;
              notice = isType('Function', this.config.customAddItemText) ? this.config.customAddItemText(value) : this.config.customAddItemText;
            }
          }

          return {
            response: canAddItem,
            notice: notice
          };
        };

        _proto._ajaxCallback = function _ajaxCallback() {
          var _this18 = this;

          return function (results, value, label) {
            if (!results || !value) {
              return;
            }

            var parsedResults = isType('Object', results) ? [results] : results;

            if (parsedResults && isType('Array', parsedResults) && parsedResults.length) {
              // Remove loading states/text
              _this18._handleLoadingState(false);

              _this18._setLoading(true); // Add each result as a choice


              parsedResults.forEach(function (result) {
                if (result.choices) {
                  _this18._addGroup({
                    group: result,
                    id: result.id || null,
                    valueKey: value,
                    labelKey: label
                  });
                } else {
                  _this18._addChoice({
                    value: fetchFromObject(result, value),
                    label: fetchFromObject(result, label),
                    isSelected: result.selected,
                    isDisabled: result.disabled,
                    customProperties: result.customProperties,
                    placeholder: result.placeholder
                  });
                }
              });

              _this18._setLoading(false);

              if (_this18._isSelectOneElement) {
                _this18._selectPlaceholderChoice();
              }
            } else {
              // No results, remove loading state
              _this18._handleLoadingState(false);
            }
          };
        };

        _proto._searchChoices = function _searchChoices(value) {
          var newValue = isType('String', value) ? value.trim() : value;
          var currentValue = isType('String', this._currentValue) ? this._currentValue.trim() : this._currentValue;

          if (newValue.length < 1 && newValue === currentValue + " ") {
            return 0;
          } // If new value matches the desired length and is not the same as the current value with a space


          var haystack = this._store.searchableChoices;
          var needle = newValue;
          var keys = [].concat(this.config.searchFields);
          var options = Object.assign(this.config.fuseOptions, {
            keys: keys
          });
          var fuse = new fuse_default.a(haystack, options);
          var results = fuse.search(needle);
          this._currentValue = newValue;
          this._highlightPosition = 0;
          this._isSearching = true;

          this._store.dispatch(choices_filterChoices(results));

          return results.length;
        };

        _proto._addEventListeners = function _addEventListeners() {
          window.delegateEvent.add('keyup', this._onKeyUp);
          window.delegateEvent.add('keydown', this._onKeyDown);
          window.delegateEvent.add('click', this._onClick);
          window.delegateEvent.add('touchmove', this._onTouchMove);
          window.delegateEvent.add('touchend', this._onTouchEnd);
          window.delegateEvent.add('mousedown', this._onMouseDown);
          window.delegateEvent.add('mouseover', this._onMouseOver);

          if (this._isSelectOneElement) {
            this.containerOuter.element.addEventListener('focus', this._onFocus);
            this.containerOuter.element.addEventListener('blur', this._onBlur);
          }

          this.input.element.addEventListener('focus', this._onFocus);
          this.input.element.addEventListener('blur', this._onBlur);

          if (this.input.element.form) {
            this.input.element.form.addEventListener('reset', this._onFormReset);
          }

          this.input.addEventListeners();
        };

        _proto._removeEventListeners = function _removeEventListeners() {
          window.delegateEvent.remove('keyup', this._onKeyUp);
          window.delegateEvent.remove('keydown', this._onKeyDown);
          window.delegateEvent.remove('click', this._onClick);
          window.delegateEvent.remove('touchmove', this._onTouchMove);
          window.delegateEvent.remove('touchend', this._onTouchEnd);
          window.delegateEvent.remove('mousedown', this._onMouseDown);
          window.delegateEvent.remove('mouseover', this._onMouseOver);

          if (this._isSelectOneElement) {
            this.containerOuter.element.removeEventListener('focus', this._onFocus);
            this.containerOuter.element.removeEventListener('blur', this._onBlur);
          }

          this.input.element.removeEventListener('focus', this._onFocus);
          this.input.element.removeEventListener('blur', this._onBlur);

          if (this.input.element.form) {
            this.input.element.form.removeEventListener('reset', this._onFormReset);
          }

          this.input.removeEventListeners();
        };

        _proto._onKeyDown = function _onKeyDown(event) {
          var _keyDownActions;

          var target = event.target,
              keyCode = event.keyCode,
              ctrlKey = event.ctrlKey,
              metaKey = event.metaKey;

          if (target !== this.input.element && !this.containerOuter.element.contains(target)) {
            return;
          }

          var activeItems = this._store.activeItems;
          var hasFocusedInput = this.input.isFocussed;
          var hasActiveDropdown = this.dropdown.isActive;
          var hasItems = this.itemList.hasChildren;
          var keyString = String.fromCharCode(keyCode);
          var BACK_KEY = KEY_CODES.BACK_KEY,
              DELETE_KEY = KEY_CODES.DELETE_KEY,
              ENTER_KEY = KEY_CODES.ENTER_KEY,
              A_KEY = KEY_CODES.A_KEY,
              ESC_KEY = KEY_CODES.ESC_KEY,
              UP_KEY = KEY_CODES.UP_KEY,
              DOWN_KEY = KEY_CODES.DOWN_KEY,
              PAGE_UP_KEY = KEY_CODES.PAGE_UP_KEY,
              PAGE_DOWN_KEY = KEY_CODES.PAGE_DOWN_KEY;
          var hasCtrlDownKeyPressed = ctrlKey || metaKey; // If a user is typing and the dropdown is not active

          if (!this._isTextElement && /[a-zA-Z0-9-_ ]/.test(keyString)) {
            this.showDropdown();
          } // Map keys to key actions


          var keyDownActions = (_keyDownActions = {}, _keyDownActions[A_KEY] = this._onAKey, _keyDownActions[ENTER_KEY] = this._onEnterKey, _keyDownActions[ESC_KEY] = this._onEscapeKey, _keyDownActions[UP_KEY] = this._onDirectionKey, _keyDownActions[PAGE_UP_KEY] = this._onDirectionKey, _keyDownActions[DOWN_KEY] = this._onDirectionKey, _keyDownActions[PAGE_DOWN_KEY] = this._onDirectionKey, _keyDownActions[DELETE_KEY] = this._onDeleteKey, _keyDownActions[BACK_KEY] = this._onDeleteKey, _keyDownActions); // If keycode has a function, run it

          if (keyDownActions[keyCode]) {
            keyDownActions[keyCode]({
              event: event,
              target: target,
              keyCode: keyCode,
              metaKey: metaKey,
              activeItems: activeItems,
              hasFocusedInput: hasFocusedInput,
              hasActiveDropdown: hasActiveDropdown,
              hasItems: hasItems,
              hasCtrlDownKeyPressed: hasCtrlDownKeyPressed
            });
          }
        };

        _proto._onKeyUp = function _onKeyUp(_ref2) {
          var target = _ref2.target,
              keyCode = _ref2.keyCode;

          if (target !== this.input.element) {
            return;
          }

          var value = this.input.value;
          var activeItems = this._store.activeItems;

          var canAddItem = this._canAddItem(activeItems, value);

          var backKey = KEY_CODES.BACK_KEY,
              deleteKey = KEY_CODES.DELETE_KEY; // We are typing into a text input and have a value, we want to show a dropdown
          // notice. Otherwise hide the dropdown

          if (this._isTextElement) {
            var canShowDropdownNotice = canAddItem.notice && value;

            if (canShowDropdownNotice) {
              var dropdownItem = this._getTemplate('notice', canAddItem.notice);

              this.dropdown.element.innerHTML = dropdownItem.outerHTML;
              this.showDropdown(true);
            } else {
              this.hideDropdown(true);
            }
          } else {
            var userHasRemovedValue = (keyCode === backKey || keyCode === deleteKey) && !target.value;
            var canReactivateChoices = !this._isTextElement && this._isSearching;
            var canSearch = this._canSearch && canAddItem.response;

            if (userHasRemovedValue && canReactivateChoices) {
              this._isSearching = false;

              this._store.dispatch(choices_activateChoices(true));
            } else if (canSearch) {
              this._handleSearch(this.input.value);
            }
          }

          this._canSearch = this.config.searchEnabled;
        };

        _proto._onAKey = function _onAKey(_ref3) {
          var hasItems = _ref3.hasItems,
              hasCtrlDownKeyPressed = _ref3.hasCtrlDownKeyPressed; // If CTRL + A or CMD + A have been pressed and there are items to select

          if (hasCtrlDownKeyPressed && hasItems) {
            this._canSearch = false;
            var shouldHightlightAll = this.config.removeItems && !this.input.value && this.input.element === document.activeElement;

            if (shouldHightlightAll) {
              this.highlightAll();
            }
          }
        };

        _proto._onEnterKey = function _onEnterKey(_ref4) {
          var event = _ref4.event,
              target = _ref4.target,
              activeItems = _ref4.activeItems,
              hasActiveDropdown = _ref4.hasActiveDropdown;
          var enterKey = KEY_CODES.ENTER_KEY;
          var targetWasButton = target.hasAttribute('data-button');

          if (this._isTextElement && target.value) {
            var value = this.input.value;

            var canAddItem = this._canAddItem(activeItems, value);

            if (canAddItem.response) {
              this.hideDropdown(true);

              this._addItem({
                value: value
              });

              this._triggerChange(value);

              this.clearInput();
            }
          }

          if (targetWasButton) {
            this._handleButtonAction(activeItems, target);

            event.preventDefault();
          }

          if (hasActiveDropdown) {
            var highlightedChoice = this.dropdown.getChild("." + this.config.classNames.highlightedState);

            if (highlightedChoice) {
              // add enter keyCode value
              if (activeItems[0]) {
                activeItems[0].keyCode = enterKey; // eslint-disable-line no-param-reassign
              }

              this._handleChoiceAction(activeItems, highlightedChoice);
            }

            event.preventDefault();
          } else if (this._isSelectOneElement) {
            this.showDropdown();
            event.preventDefault();
          }
        };

        _proto._onEscapeKey = function _onEscapeKey(_ref5) {
          var hasActiveDropdown = _ref5.hasActiveDropdown;

          if (hasActiveDropdown) {
            this.hideDropdown(true);
            this.containerOuter.focus();
          }
        };

        _proto._onDirectionKey = function _onDirectionKey(_ref6) {
          var event = _ref6.event,
              hasActiveDropdown = _ref6.hasActiveDropdown,
              keyCode = _ref6.keyCode,
              metaKey = _ref6.metaKey;
          var downKey = KEY_CODES.DOWN_KEY,
              pageUpKey = KEY_CODES.PAGE_UP_KEY,
              pageDownKey = KEY_CODES.PAGE_DOWN_KEY; // If up or down key is pressed, traverse through options

          if (hasActiveDropdown || this._isSelectOneElement) {
            this.showDropdown();
            this._canSearch = false;
            var directionInt = keyCode === downKey || keyCode === pageDownKey ? 1 : -1;
            var skipKey = metaKey || keyCode === pageDownKey || keyCode === pageUpKey;
            var selectableChoiceIdentifier = '[data-choice-selectable]';
            var nextEl;

            if (skipKey) {
              if (directionInt > 0) {
                nextEl = Array.from(this.dropdown.element.querySelectorAll(selectableChoiceIdentifier)).pop();
              } else {
                nextEl = this.dropdown.element.querySelector(selectableChoiceIdentifier);
              }
            } else {
              var currentEl = this.dropdown.element.querySelector("." + this.config.classNames.highlightedState);

              if (currentEl) {
                nextEl = getAdjacentEl(currentEl, selectableChoiceIdentifier, directionInt);
              } else {
                nextEl = this.dropdown.element.querySelector(selectableChoiceIdentifier);
              }
            }

            if (nextEl) {
              // We prevent default to stop the cursor moving
              // when pressing the arrow
              if (!isScrolledIntoView(nextEl, this.choiceList.element, directionInt)) {
                this.choiceList.scrollToChoice(nextEl, directionInt);
              }

              this._highlightChoice(nextEl);
            } // Prevent default to maintain cursor position whilst
            // traversing dropdown options


            event.preventDefault();
          }
        };

        _proto._onDeleteKey = function _onDeleteKey(_ref7) {
          var event = _ref7.event,
              target = _ref7.target,
              hasFocusedInput = _ref7.hasFocusedInput,
              activeItems = _ref7.activeItems; // If backspace or delete key is pressed and the input has no value

          if (hasFocusedInput && !target.value && !this._isSelectOneElement) {
            this._handleBackspace(activeItems);

            event.preventDefault();
          }
        };

        _proto._onTouchMove = function _onTouchMove() {
          if (this._wasTap) {
            this._wasTap = false;
          }
        };

        _proto._onTouchEnd = function _onTouchEnd(event) {
          var _ref8 = event || event.touches[0],
              target = _ref8.target;

          var touchWasWithinContainer = this._wasTap && this.containerOuter.element.contains(target);

          if (touchWasWithinContainer) {
            var containerWasExactTarget = target === this.containerOuter.element || target === this.containerInner.element;

            if (containerWasExactTarget) {
              if (this._isTextElement) {
                this.input.focus();
              } else if (this._isSelectMultipleElement) {
                this.showDropdown();
              }
            } // Prevents focus event firing


            event.stopPropagation();
          }

          this._wasTap = true;
        };

        _proto._onMouseDown = function _onMouseDown(event) {
          var target = event.target,
              shiftKey = event.shiftKey; // If we have our mouse down on the scrollbar and are on IE11...

          if (this.choiceList.element.contains(target) && isIE11()) {
            this._isScrollingOnIe = true;
          }

          if (!this.containerOuter.element.contains(target) || target === this.input.element) {
            return;
          }

          var activeItems = this._store.activeItems;
          var hasShiftKey = shiftKey;
          var buttonTarget = findAncestorByAttrName(target, 'data-button');
          var itemTarget = findAncestorByAttrName(target, 'data-item');
          var choiceTarget = findAncestorByAttrName(target, 'data-choice');

          if (buttonTarget) {
            this._handleButtonAction(activeItems, buttonTarget);
          } else if (itemTarget) {
            this._handleItemAction(activeItems, itemTarget, hasShiftKey);
          } else if (choiceTarget) {
            this._handleChoiceAction(activeItems, choiceTarget);
          }

          event.preventDefault();
        };

        _proto._onMouseOver = function _onMouseOver(_ref9) {
          var target = _ref9.target;
          var targetWithinDropdown = target === this.dropdown || this.dropdown.element.contains(target);
          var shouldHighlightChoice = targetWithinDropdown && target.hasAttribute('data-choice');

          if (shouldHighlightChoice) {
            this._highlightChoice(target);
          }
        };

        _proto._onClick = function _onClick(_ref10) {
          var target = _ref10.target;
          var clickWasWithinContainer = this.containerOuter.element.contains(target);

          if (clickWasWithinContainer) {
            if (!this.dropdown.isActive && !this.containerOuter.isDisabled) {
              if (this._isTextElement) {
                if (document.activeElement !== this.input.element) {
                  this.input.focus();
                }
              } else {
                this.showDropdown();
                this.containerOuter.focus();
              }
            } else if (this._isSelectOneElement && target !== this.input.element && !this.dropdown.element.contains(target)) {
              this.hideDropdown();
            }
          } else {
            var hasHighlightedItems = this._store.highlightedActiveItems.length > 0;

            if (hasHighlightedItems) {
              this.unhighlightAll();
            }

            this.containerOuter.removeFocusState();
            this.hideDropdown(true);
          }
        };

        _proto._onFocus = function _onFocus(_ref11) {
          var _this19 = this;

          var target = _ref11.target;
          var focusWasWithinContainer = this.containerOuter.element.contains(target);

          if (!focusWasWithinContainer) {
            return;
          }

          var focusActions = {
            text: function text() {
              if (target === _this19.input.element) {
                _this19.containerOuter.addFocusState();
              }
            },
            'select-one': function selectOne() {
              _this19.containerOuter.addFocusState();

              if (target === _this19.input.element) {
                _this19.showDropdown(true);
              }
            },
            'select-multiple': function selectMultiple() {
              if (target === _this19.input.element) {
                _this19.showDropdown(true); // If element is a select box, the focused element is the container and the dropdown
                // isn't already open, focus and show dropdown


                _this19.containerOuter.addFocusState();
              }
            }
          };
          focusActions[this.passedElement.element.type]();
        };

        _proto._onBlur = function _onBlur(_ref12) {
          var _this20 = this;

          var target = _ref12.target;
          var blurWasWithinContainer = this.containerOuter.element.contains(target);

          if (blurWasWithinContainer && !this._isScrollingOnIe) {
            var activeItems = this._store.activeItems;
            var hasHighlightedItems = activeItems.some(function (item) {
              return item.highlighted;
            });
            var blurActions = {
              text: function text() {
                if (target === _this20.input.element) {
                  _this20.containerOuter.removeFocusState();

                  if (hasHighlightedItems) {
                    _this20.unhighlightAll();
                  }

                  _this20.hideDropdown(true);
                }
              },
              'select-one': function selectOne() {
                _this20.containerOuter.removeFocusState();

                if (target === _this20.input.element || target === _this20.containerOuter.element && !_this20._canSearch) {
                  _this20.hideDropdown(true);
                }
              },
              'select-multiple': function selectMultiple() {
                if (target === _this20.input.element) {
                  _this20.containerOuter.removeFocusState();

                  _this20.hideDropdown(true);

                  if (hasHighlightedItems) {
                    _this20.unhighlightAll();
                  }
                }
              }
            };
            blurActions[this.passedElement.element.type]();
          } else {
            // On IE11, clicking the scollbar blurs our input and thus
            // closes the dropdown. To stop this, we refocus our input
            // if we know we are on IE *and* are scrolling.
            this._isScrollingOnIe = false;
            this.input.element.focus();
          }
        };

        _proto._onFormReset = function _onFormReset() {
          this._store.dispatch(resetTo(this._initialState));
        };

        _proto._highlightChoice = function _highlightChoice(el) {
          var _this21 = this;

          if (el === void 0) {
            el = null;
          }

          var choices = Array.from(this.dropdown.element.querySelectorAll('[data-choice-selectable]'));

          if (!choices.length) {
            return;
          }

          var passedEl = el;
          var highlightedChoices = Array.from(this.dropdown.element.querySelectorAll("." + this.config.classNames.highlightedState)); // Remove any highlighted choices

          highlightedChoices.forEach(function (choice) {
            choice.classList.remove(_this21.config.classNames.highlightedState);
            choice.setAttribute('aria-selected', 'false');
          });

          if (passedEl) {
            this._highlightPosition = choices.indexOf(passedEl);
          } else {
            // Highlight choice based on last known highlight location
            if (choices.length > this._highlightPosition) {
              // If we have an option to highlight
              passedEl = choices[this._highlightPosition];
            } else {
              // Otherwise highlight the option before
              passedEl = choices[choices.length - 1];
            }

            if (!passedEl) {
              passedEl = choices[0];
            }
          }

          passedEl.classList.add(this.config.classNames.highlightedState);
          passedEl.setAttribute('aria-selected', 'true');
          this.passedElement.triggerEvent(EVENTS.highlightChoice, {
            el: passedEl
          });

          if (this.dropdown.isActive) {
            // IE11 ignores aria-label and blocks virtual keyboard
            // if aria-activedescendant is set without a dropdown
            this.input.setActiveDescendant(passedEl.id);
            this.containerOuter.setActiveDescendant(passedEl.id);
          }
        };

        _proto._addItem = function _addItem(_ref13) {
          var value = _ref13.value,
              _ref13$label = _ref13.label,
              label = _ref13$label === void 0 ? null : _ref13$label,
              _ref13$choiceId = _ref13.choiceId,
              choiceId = _ref13$choiceId === void 0 ? -1 : _ref13$choiceId,
              _ref13$groupId = _ref13.groupId,
              groupId = _ref13$groupId === void 0 ? -1 : _ref13$groupId,
              _ref13$customProperti = _ref13.customProperties,
              customProperties = _ref13$customProperti === void 0 ? null : _ref13$customProperti,
              _ref13$placeholder = _ref13.placeholder,
              placeholder = _ref13$placeholder === void 0 ? false : _ref13$placeholder,
              _ref13$keyCode = _ref13.keyCode,
              keyCode = _ref13$keyCode === void 0 ? null : _ref13$keyCode;
          var passedValue = isType('String', value) ? value.trim() : value;
          var passedKeyCode = keyCode;
          var passedCustomProperties = customProperties;
          var items = this._store.items;
          var passedLabel = label || passedValue;
          var passedOptionId = parseInt(choiceId, 10) || -1;
          var group = groupId >= 0 ? this._store.getGroupById(groupId) : null;
          var id = items ? items.length + 1 : 1; // If a prepended value has been passed, prepend it

          if (this.config.prependValue) {
            passedValue = this.config.prependValue + passedValue.toString();
          } // If an appended value has been passed, append it


          if (this.config.appendValue) {
            passedValue += this.config.appendValue.toString();
          }

          this._store.dispatch(items_addItem({
            value: passedValue,
            label: passedLabel,
            id: id,
            choiceId: passedOptionId,
            groupId: groupId,
            customProperties: customProperties,
            placeholder: placeholder,
            keyCode: passedKeyCode
          }));

          if (this._isSelectOneElement) {
            this.removeActiveItems(id);
          } // Trigger change event


          this.passedElement.triggerEvent(EVENTS.addItem, {
            id: id,
            value: passedValue,
            label: passedLabel,
            customProperties: passedCustomProperties,
            groupValue: group && group.value ? group.value : undefined,
            keyCode: passedKeyCode
          });
          return this;
        };

        _proto._removeItem = function _removeItem(item) {
          if (!item || !isType('Object', item)) {
            return this;
          }

          var id = item.id,
              value = item.value,
              label = item.label,
              choiceId = item.choiceId,
              groupId = item.groupId;
          var group = groupId >= 0 ? this._store.getGroupById(groupId) : null;

          this._store.dispatch(items_removeItem(id, choiceId));

          if (group && group.value) {
            this.passedElement.triggerEvent(EVENTS.removeItem, {
              id: id,
              value: value,
              label: label,
              groupValue: group.value
            });
          } else {
            this.passedElement.triggerEvent(EVENTS.removeItem, {
              id: id,
              value: value,
              label: label
            });
          }

          return this;
        };

        _proto._addChoice = function _addChoice(_ref14) {
          var value = _ref14.value,
              _ref14$label = _ref14.label,
              label = _ref14$label === void 0 ? null : _ref14$label,
              _ref14$isSelected = _ref14.isSelected,
              isSelected = _ref14$isSelected === void 0 ? false : _ref14$isSelected,
              _ref14$isDisabled = _ref14.isDisabled,
              isDisabled = _ref14$isDisabled === void 0 ? false : _ref14$isDisabled,
              _ref14$groupId = _ref14.groupId,
              groupId = _ref14$groupId === void 0 ? -1 : _ref14$groupId,
              _ref14$customProperti = _ref14.customProperties,
              customProperties = _ref14$customProperti === void 0 ? null : _ref14$customProperti,
              _ref14$placeholder = _ref14.placeholder,
              placeholder = _ref14$placeholder === void 0 ? false : _ref14$placeholder,
              _ref14$keyCode = _ref14.keyCode,
              keyCode = _ref14$keyCode === void 0 ? null : _ref14$keyCode;

          if (typeof value === 'undefined' || value === null) {
            return;
          } // Generate unique id


          var choices = this._store.choices;
          var choiceLabel = label || value;
          var choiceId = choices ? choices.length + 1 : 1;
          var choiceElementId = this._baseId + "-" + this._idNames.itemChoice + "-" + choiceId;

          this._store.dispatch(choices_addChoice({
            value: value,
            label: choiceLabel,
            id: choiceId,
            groupId: groupId,
            disabled: isDisabled,
            elementId: choiceElementId,
            customProperties: customProperties,
            placeholder: placeholder,
            keyCode: keyCode
          }));

          if (isSelected) {
            this._addItem({
              value: value,
              label: choiceLabel,
              choiceId: choiceId,
              customProperties: customProperties,
              placeholder: placeholder,
              keyCode: keyCode
            });
          }
        };

        _proto._addGroup = function _addGroup(_ref15) {
          var _this22 = this;

          var group = _ref15.group,
              id = _ref15.id,
              _ref15$valueKey = _ref15.valueKey,
              valueKey = _ref15$valueKey === void 0 ? 'value' : _ref15$valueKey,
              _ref15$labelKey = _ref15.labelKey,
              labelKey = _ref15$labelKey === void 0 ? 'label' : _ref15$labelKey;
          var groupChoices = isType('Object', group) ? group.choices : Array.from(group.getElementsByTagName('OPTION'));
          var groupId = id || Math.floor(new Date().valueOf() * Math.random());
          var isDisabled = group.disabled ? group.disabled : false;

          if (groupChoices) {
            this._store.dispatch(groups_addGroup(group.label, groupId, true, isDisabled));

            var addGroupChoices = function addGroupChoices(choice) {
              var isOptDisabled = choice.disabled || choice.parentNode && choice.parentNode.disabled;

              _this22._addChoice({
                value: choice[valueKey],
                label: isType('Object', choice) ? choice[labelKey] : choice.innerHTML,
                isSelected: choice.selected,
                isDisabled: isOptDisabled,
                groupId: groupId,
                customProperties: choice.customProperties,
                placeholder: choice.placeholder
              });
            };

            groupChoices.forEach(addGroupChoices);
          } else {
            this._store.dispatch(groups_addGroup(group.label, group.id, false, group.disabled));
          }
        };

        _proto._getTemplate = function _getTemplate(template) {
          var _templates$template;

          if (!template) {
            return null;
          }

          var _this$config4 = this.config,
              templates = _this$config4.templates,
              classNames = _this$config4.classNames;

          for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
            args[_key - 1] = arguments[_key];
          }

          return (_templates$template = templates[template]).call.apply(_templates$template, [this, classNames].concat(args));
        };

        _proto._createTemplates = function _createTemplates() {
          var callbackOnCreateTemplates = this.config.callbackOnCreateTemplates;
          var userTemplates = {};

          if (callbackOnCreateTemplates && isType('Function', callbackOnCreateTemplates)) {
            userTemplates = callbackOnCreateTemplates.call(this, strToEl);
          }

          this.config.templates = cjs_default()(TEMPLATES, userTemplates);
        };

        _proto._createElements = function _createElements() {
          this.containerOuter = new container_Container({
            element: this._getTemplate('containerOuter', this._direction, this._isSelectElement, this._isSelectOneElement, this.config.searchEnabled, this.passedElement.element.type),
            classNames: this.config.classNames,
            type: this.passedElement.element.type,
            position: this.config.position
          });
          this.containerInner = new container_Container({
            element: this._getTemplate('containerInner'),
            classNames: this.config.classNames,
            type: this.passedElement.element.type,
            position: this.config.position
          });
          this.input = new input_Input({
            element: this._getTemplate('input', this._placeholderValue),
            classNames: this.config.classNames,
            type: this.passedElement.element.type
          });
          this.choiceList = new list_List({
            element: this._getTemplate('choiceList', this._isSelectOneElement)
          });
          this.itemList = new list_List({
            element: this._getTemplate('itemList', this._isSelectOneElement)
          });
          this.dropdown = new Dropdown({
            element: this._getTemplate('dropdown'),
            classNames: this.config.classNames,
            type: this.passedElement.element.type
          });
        };

        _proto._createStructure = function _createStructure() {
          // Hide original element
          this.passedElement.conceal(); // Wrap input in container preserving DOM ordering

          this.containerInner.wrap(this.passedElement.element); // Wrapper inner container with outer container

          this.containerOuter.wrap(this.containerInner.element);

          if (this._isSelectOneElement) {
            this.input.placeholder = this.config.searchPlaceholderValue || '';
          } else if (this._placeholderValue) {
            this.input.placeholder = this._placeholderValue;
            this.input.setWidth(true);
          }

          this.containerOuter.element.appendChild(this.containerInner.element);
          this.containerOuter.element.appendChild(this.dropdown.element);
          this.containerInner.element.appendChild(this.itemList.element);

          if (!this._isTextElement) {
            this.dropdown.element.appendChild(this.choiceList.element);
          }

          if (!this._isSelectOneElement) {
            this.containerInner.element.appendChild(this.input.element);
          } else if (this.config.searchEnabled) {
            this.dropdown.element.insertBefore(this.input.element, this.dropdown.element.firstChild);
          }

          if (this._isSelectElement) {
            this._addPredefinedChoices();
          } else if (this._isTextElement) {
            this._addPredefinedItems();
          }
        };

        _proto._addPredefinedChoices = function _addPredefinedChoices() {
          var _this23 = this;

          var passedGroups = this.passedElement.optionGroups;
          this._highlightPosition = 0;
          this._isSearching = false;

          this._setLoading(true);

          if (passedGroups && passedGroups.length) {
            // If we have a placeholder option
            var placeholderChoice = this.passedElement.placeholderOption;

            if (placeholderChoice && placeholderChoice.parentNode.tagName === 'SELECT') {
              this._addChoice({
                value: placeholderChoice.value,
                label: placeholderChoice.innerHTML,
                isSelected: placeholderChoice.selected,
                isDisabled: placeholderChoice.disabled,
                placeholder: true
              });
            }

            passedGroups.forEach(function (group) {
              return _this23._addGroup({
                group: group,
                id: group.id || null
              });
            });
          } else {
            var passedOptions = this.passedElement.options;
            var filter = this.config.sortFn;
            var allChoices = this._presetChoices; // Create array of options from option elements

            passedOptions.forEach(function (o) {
              allChoices.push({
                value: o.value,
                label: o.innerHTML,
                selected: o.selected,
                disabled: o.disabled || o.parentNode.disabled,
                placeholder: o.hasAttribute('placeholder'),
                customProperties: o.getAttribute('data-custom-properties')
              });
            }); // If sorting is enabled or the user is searching, filter choices

            if (this.config.shouldSort) allChoices.sort(filter); // Determine whether there is a selected choice

            var hasSelectedChoice = allChoices.some(function (choice) {
              return choice.selected;
            });

            var handleChoice = function handleChoice(choice, index) {
              var value = choice.value,
                  label = choice.label,
                  customProperties = choice.customProperties,
                  placeholder = choice.placeholder;

              if (_this23._isSelectElement) {
                // If the choice is actually a group
                if (choice.choices) {
                  _this23._addGroup({
                    group: choice,
                    id: choice.id || null
                  });
                } else {
                  // If there is a selected choice already or the choice is not
                  // the first in the array, add each choice normally
                  // Otherwise pre-select the first choice in the array if it's a single select
                  var shouldPreselect = _this23._isSelectOneElement && !hasSelectedChoice && index === 0;
                  var isSelected = shouldPreselect ? true : choice.selected;
                  var isDisabled = shouldPreselect ? false : choice.disabled;

                  _this23._addChoice({
                    value: value,
                    label: label,
                    isSelected: isSelected,
                    isDisabled: isDisabled,
                    customProperties: customProperties,
                    placeholder: placeholder
                  });
                }
              } else {
                _this23._addChoice({
                  value: value,
                  label: label,
                  isSelected: choice.selected,
                  isDisabled: choice.disabled,
                  customProperties: customProperties,
                  placeholder: placeholder
                });
              }
            }; // Add each choice


            allChoices.forEach(function (choice, index) {
              return handleChoice(choice, index);
            });
          }

          this._setLoading(false);
        };

        _proto._addPredefinedItems = function _addPredefinedItems() {
          var _this24 = this;

          var handlePresetItem = function handlePresetItem(item) {
            var itemType = getType(item);

            if (itemType === 'Object' && item.value) {
              _this24._addItem({
                value: item.value,
                label: item.label,
                choiceId: item.id,
                customProperties: item.customProperties,
                placeholder: item.placeholder
              });
            } else if (itemType === 'String') {
              _this24._addItem({
                value: item
              });
            }
          };

          this._presetItems.forEach(function (item) {
            return handlePresetItem(item);
          });
        };

        _proto._setChoiceOrItem = function _setChoiceOrItem(item) {
          var _this25 = this;

          var itemType = getType(item).toLowerCase();
          var handleType = {
            object: function object() {
              if (!item.value) {
                return;
              } // If we are dealing with a select input, we need to create an option first
              // that is then selected. For text inputs we can just add items normally.


              if (!_this25._isTextElement) {
                _this25._addChoice({
                  value: item.value,
                  label: item.label,
                  isSelected: true,
                  isDisabled: false,
                  customProperties: item.customProperties,
                  placeholder: item.placeholder
                });
              } else {
                _this25._addItem({
                  value: item.value,
                  label: item.label,
                  choiceId: item.id,
                  customProperties: item.customProperties,
                  placeholder: item.placeholder
                });
              }
            },
            string: function string() {
              if (!_this25._isTextElement) {
                _this25._addChoice({
                  value: item,
                  label: item,
                  isSelected: true,
                  isDisabled: false
                });
              } else {
                _this25._addItem({
                  value: item
                });
              }
            }
          };
          handleType[itemType]();
        };

        _proto._findAndSelectChoiceByValue = function _findAndSelectChoiceByValue(val) {
          var _this26 = this;

          var choices = this._store.choices; // Check 'value' property exists and the choice isn't already selected

          var foundChoice = choices.find(function (choice) {
            return _this26.config.itemComparer(choice.value, val);
          });

          if (foundChoice && !foundChoice.selected) {
            this._addItem({
              value: foundChoice.value,
              label: foundChoice.label,
              choiceId: foundChoice.id,
              groupId: foundChoice.groupId,
              customProperties: foundChoice.customProperties,
              placeholder: foundChoice.placeholder,
              keyCode: foundChoice.keyCode
            });
          }
        };

        _proto._generateInstances = function _generateInstances(elements, config) {
          return elements.reduce(function (instances, element) {
            instances.push(new Choices(element, config));
            return instances;
          }, [this]);
        };

        _proto._generatePlaceholderValue = function _generatePlaceholderValue() {
          if (this._isSelectOneElement) {
            return false;
          }

          return this.config.placeholder ? this.config.placeholderValue || this.passedElement.element.getAttribute('placeholder') : false;
        }
        /* =====  End of Private functions  ====== */
        ;

        return Choices;
      }();

      choices_Choices.userDefaults = {};
      /* harmony default export */

      var scripts_choices = __webpack_exports__["default"] = choices_Choices;
      /***/
    }
    /******/
    ])["default"]
  );
});
//# sourceMappingURL=pwtimage.js.map
