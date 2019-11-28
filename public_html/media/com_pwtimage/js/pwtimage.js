function _typeof2(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof2 = function _typeof2(obj) { return typeof obj; }; } else { _typeof2 = function _typeof2(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof2(obj); }

/*!
 * Cropper.js v1.5.4
 * https://fengyuanchen.github.io/cropperjs
 *
 * Copyright 2015-present Chen Fengyuan
 * Released under the MIT license
 *
 * Date: 2019-07-20T02:37:47.411Z
 */
(function (global, factory) {
  (typeof exports === "undefined" ? "undefined" : _typeof2(exports)) === 'object' && typeof module !== 'undefined' ? module.exports = factory() : typeof define === 'function' && define.amd ? define(factory) : (global = global || self, global.Cropper = factory());
})(this, function () {
  'use strict';

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

  function _toConsumableArray(arr) {
    return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _nonIterableSpread();
  }

  function _arrayWithoutHoles(arr) {
    if (Array.isArray(arr)) {
      for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) {
        arr2[i] = arr[i];
      }

      return arr2;
    }
  }

  function _iterableToArray(iter) {
    if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter);
  }

  function _nonIterableSpread() {
    throw new TypeError("Invalid attempt to spread non-iterable instance");
  }

  var IS_BROWSER = typeof window !== 'undefined';
  var WINDOW = IS_BROWSER ? window : {};
  var IS_TOUCH_DEVICE = IS_BROWSER ? 'ontouchstart' in WINDOW.document.documentElement : false;
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
    minContainerWidth: 200,
    minContainerHeight: 100,
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
   * Check out {@link http://0.30000000000000004.com/}
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
    var pointers2 = assign({}, pointers);
    var ratios = [];
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
        ratios.push(ratio);
      });
    });
    ratios.sort(function (a, b) {
      return Math.abs(a) < Math.abs(b);
    });
    return ratios[0];
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
    return endOnly ? end : assign({
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

      default:
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
      addClass(cropper, CLASS_HIDDEN);
      removeClass(element, CLASS_HIDDEN);
      var containerData = {
        width: Math.max(container.offsetWidth, Number(options.minContainerWidth) || 200),
        height: Math.max(container.offsetHeight, Number(options.minContainerHeight) || 100)
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
      canvasData.left = (containerData.width - canvasWidth) / 2;
      canvasData.top = (containerData.height - canvasHeight) / 2;
      canvasData.oldLeft = canvasData.left;
      canvasData.oldTop = canvasData.top;
      this.canvasData = canvasData;
      this.limited = viewMode === 1 || viewMode === 2;
      this.limitCanvas(true, true);
      this.initialImageData = assign({}, imageData);
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
      var options = this.options,
          container = this.container,
          containerData = this.containerData;
      var minContainerWidth = Number(options.minContainerWidth) || MIN_CONTAINER_WIDTH;
      var minContainerHeight = Number(options.minContainerHeight) || MIN_CONTAINER_HEIGHT;

      if (this.disabled || containerData.width <= minContainerWidth || containerData.height <= minContainerHeight) {
        return;
      }

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

      if (this.disabled // No primary button (Usually the left button)
      // Note that touch events have no `buttons` or `button` property
      || isNumber(buttons) && buttons !== 1 || isNumber(button) && button !== 0 // Open context menu
      || event.ctrlKey) {
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

          default:
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

        default:
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

  var Cropper =
  /*#__PURE__*/
  function () {
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
          } // e.g.: "http://example.com/img/picture.jpg"


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
        var crossOrigin;
        var crossOriginUrl;

        if (this.options.checkCrossOrigin && isCrossOriginURL(url)) {
          crossOrigin = element.crossOrigin;

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
/**!
 * Sortable
 * @author	RubaXa   <trash@rubaxa.org>
 * @author	owenm    <owen23355@gmail.com>
 * @license MIT
 */


(function sortableModule(factory) {
  "use strict";

  if (typeof define === "function" && define.amd) {
    define(factory);
  } else if (typeof module != "undefined" && typeof module.exports != "undefined") {
    module.exports = factory();
  } else {
    /* jshint sub:true */
    window["Sortable"] = factory();
  }
})(function sortableFactory() {
  "use strict";

  if (typeof window === "undefined" || !window.document) {
    return function sortableError() {
      throw new Error("Sortable.js requires a window with a document");
    };
  }

  var dragEl,
      parentEl,
      ghostEl,
      cloneEl,
      rootEl,
      nextEl,
      lastDownEl,
      scrollEl,
      scrollParentEl,
      scrollCustomFn,
      oldIndex,
      newIndex,
      oldDraggableIndex,
      newDraggableIndex,
      activeGroup,
      putSortable,
      autoScrolls = [],
      scrolling = false,
      awaitingDragStarted = false,
      ignoreNextClick = false,
      sortables = [],
      pointerElemChangedInterval,
      lastPointerElemX,
      lastPointerElemY,
      tapEvt,
      touchEvt,
      moved,
      lastTarget,
      lastDirection,
      pastFirstInvertThresh = false,
      isCircumstantialInvert = false,
      lastMode,
      // 'swap' or 'insert'
  targetMoveDistance,
      // For positioning ghost absolutely
  ghostRelativeParent,
      ghostRelativeParentInitialScroll = [],
      // (left, top)
  realDragElRect,
      // dragEl rect after current animation

  /** @const */
  R_SPACE = /\s+/g,
      expando = 'Sortable' + new Date().getTime(),
      win = window,
      document = win.document,
      parseInt = win.parseInt,
      setTimeout = win.setTimeout,
      $ = win.jQuery || win.Zepto,
      Polymer = win.Polymer,
      captureMode = {
    capture: false,
    passive: false
  },
      IE11OrLess = !!navigator.userAgent.match(/(?:Trident.*rv[ :]?11\.|msie|iemobile)/i),
      Edge = !!navigator.userAgent.match(/Edge/i),
      FireFox = !!navigator.userAgent.match(/firefox/i),
      Safari = !!(navigator.userAgent.match(/safari/i) && !navigator.userAgent.match(/chrome/i) && !navigator.userAgent.match(/android/i)),
      IOS = !!navigator.userAgent.match(/iP(ad|od|hone)/i),
      PositionGhostAbsolutely = IOS,
      CSSFloatProperty = Edge || IE11OrLess ? 'cssFloat' : 'float',
      // This will not pass for IE9, because IE9 DnD only works on anchors
  supportDraggable = 'draggable' in document.createElement('div'),
      supportCssPointerEvents = function () {
    // false when <= IE11
    if (IE11OrLess) {
      return false;
    }

    var el = document.createElement('x');
    el.style.cssText = 'pointer-events:auto';
    return el.style.pointerEvents === 'auto';
  }(),
      _silent = false,
      _alignedSilent = false,
      abs = Math.abs,
      min = Math.min,
      max = Math.max,
      savedInputChecked = [],
      _detectDirection = function _detectDirection(el, options) {
    var elCSS = _css(el),
        elWidth = parseInt(elCSS.width) - parseInt(elCSS.paddingLeft) - parseInt(elCSS.paddingRight) - parseInt(elCSS.borderLeftWidth) - parseInt(elCSS.borderRightWidth),
        child1 = _getChild(el, 0, options),
        child2 = _getChild(el, 1, options),
        firstChildCSS = child1 && _css(child1),
        secondChildCSS = child2 && _css(child2),
        firstChildWidth = firstChildCSS && parseInt(firstChildCSS.marginLeft) + parseInt(firstChildCSS.marginRight) + _getRect(child1).width,
        secondChildWidth = secondChildCSS && parseInt(secondChildCSS.marginLeft) + parseInt(secondChildCSS.marginRight) + _getRect(child2).width;

    if (elCSS.display === 'flex') {
      return elCSS.flexDirection === 'column' || elCSS.flexDirection === 'column-reverse' ? 'vertical' : 'horizontal';
    }

    if (elCSS.display === 'grid') {
      return elCSS.gridTemplateColumns.split(' ').length <= 1 ? 'vertical' : 'horizontal';
    }

    if (child1 && firstChildCSS["float"] !== 'none') {
      var touchingSideChild2 = firstChildCSS["float"] === 'left' ? 'left' : 'right';
      return child2 && (secondChildCSS.clear === 'both' || secondChildCSS.clear === touchingSideChild2) ? 'vertical' : 'horizontal';
    }

    return child1 && (firstChildCSS.display === 'block' || firstChildCSS.display === 'flex' || firstChildCSS.display === 'table' || firstChildCSS.display === 'grid' || firstChildWidth >= elWidth && elCSS[CSSFloatProperty] === 'none' || child2 && elCSS[CSSFloatProperty] === 'none' && firstChildWidth + secondChildWidth > elWidth) ? 'vertical' : 'horizontal';
  },

  /**
   * Detects first nearest empty sortable to X and Y position using emptyInsertThreshold.
   * @param  {Number} x      X position
   * @param  {Number} y      Y position
   * @return {HTMLElement}   Element of the first found nearest Sortable
   */
  _detectNearestEmptySortable = function _detectNearestEmptySortable(x, y) {
    for (var i = 0; i < sortables.length; i++) {
      if (_lastChild(sortables[i])) continue;

      var rect = _getRect(sortables[i]),
          threshold = sortables[i][expando].options.emptyInsertThreshold,
          insideHorizontally = x >= rect.left - threshold && x <= rect.right + threshold,
          insideVertically = y >= rect.top - threshold && y <= rect.bottom + threshold;

      if (threshold && insideHorizontally && insideVertically) {
        return sortables[i];
      }
    }
  },
      _isClientInRowColumn = function _isClientInRowColumn(x, y, el, axis, options) {
    var targetRect = _getRect(el),
        targetS1Opp = axis === 'vertical' ? targetRect.left : targetRect.top,
        targetS2Opp = axis === 'vertical' ? targetRect.right : targetRect.bottom,
        mouseOnOppAxis = axis === 'vertical' ? x : y;

    return targetS1Opp < mouseOnOppAxis && mouseOnOppAxis < targetS2Opp;
  },
      _isElInRowColumn = function _isElInRowColumn(el1, el2, axis) {
    var el1Rect = el1 === dragEl && realDragElRect || _getRect(el1),
        el2Rect = el2 === dragEl && realDragElRect || _getRect(el2),
        el1S1Opp = axis === 'vertical' ? el1Rect.left : el1Rect.top,
        el1S2Opp = axis === 'vertical' ? el1Rect.right : el1Rect.bottom,
        el1OppLength = axis === 'vertical' ? el1Rect.width : el1Rect.height,
        el2S1Opp = axis === 'vertical' ? el2Rect.left : el2Rect.top,
        el2S2Opp = axis === 'vertical' ? el2Rect.right : el2Rect.bottom,
        el2OppLength = axis === 'vertical' ? el2Rect.width : el2Rect.height;

    return el1S1Opp === el2S1Opp || el1S2Opp === el2S2Opp || el1S1Opp + el1OppLength / 2 === el2S1Opp + el2OppLength / 2;
  },
      _getParentAutoScrollElement = function _getParentAutoScrollElement(el, includeSelf) {
    // skip to window
    if (!el || !el.getBoundingClientRect) return _getWindowScrollingElement();
    var elem = el;
    var gotSelf = false;

    do {
      // we don't need to get elem css if it isn't even overflowing in the first place (performance)
      if (elem.clientWidth < elem.scrollWidth || elem.clientHeight < elem.scrollHeight) {
        var elemCSS = _css(elem);

        if (elem.clientWidth < elem.scrollWidth && (elemCSS.overflowX == 'auto' || elemCSS.overflowX == 'scroll') || elem.clientHeight < elem.scrollHeight && (elemCSS.overflowY == 'auto' || elemCSS.overflowY == 'scroll')) {
          if (!elem || !elem.getBoundingClientRect || elem === document.body) return _getWindowScrollingElement();
          if (gotSelf || includeSelf) return elem;
          gotSelf = true;
        }
      }
      /* jshint boss:true */

    } while (elem = elem.parentNode);

    return _getWindowScrollingElement();
  },
      _getWindowScrollingElement = function _getWindowScrollingElement() {
    if (IE11OrLess) {
      return document.documentElement;
    } else {
      return document.scrollingElement;
    }
  },
      _scrollBy = function _scrollBy(el, x, y) {
    el.scrollLeft += x;
    el.scrollTop += y;
  },
      _autoScroll = _throttle(function (
  /**Event*/
  evt,
  /**Object*/
  options,
  /**HTMLElement*/
  rootEl,
  /**Boolean*/
  isFallback) {
    // Bug: https://bugzilla.mozilla.org/show_bug.cgi?id=505521
    if (options.scroll) {
      var _this = rootEl ? rootEl[expando] : window,
          sens = options.scrollSensitivity,
          speed = options.scrollSpeed,
          x = evt.clientX,
          y = evt.clientY,
          winScroller = _getWindowScrollingElement(),
          scrollThisInstance = false; // Detect scrollEl


      if (scrollParentEl !== rootEl) {
        _clearAutoScrolls();

        scrollEl = options.scroll;
        scrollCustomFn = options.scrollFn;

        if (scrollEl === true) {
          scrollEl = _getParentAutoScrollElement(rootEl, true);
          scrollParentEl = scrollEl;
        }
      }

      var layersOut = 0;
      var currentParent = scrollEl;

      do {
        var el = currentParent,
            rect = _getRect(el),
            top = rect.top,
            bottom = rect.bottom,
            left = rect.left,
            right = rect.right,
            width = rect.width,
            height = rect.height,
            scrollWidth,
            scrollHeight,
            css,
            vx,
            vy,
            canScrollX,
            canScrollY,
            scrollPosX,
            scrollPosY;

        scrollWidth = el.scrollWidth;
        scrollHeight = el.scrollHeight;
        css = _css(el);
        scrollPosX = el.scrollLeft;
        scrollPosY = el.scrollTop;

        if (el === winScroller) {
          canScrollX = width < scrollWidth && (css.overflowX === 'auto' || css.overflowX === 'scroll' || css.overflowX === 'visible');
          canScrollY = height < scrollHeight && (css.overflowY === 'auto' || css.overflowY === 'scroll' || css.overflowY === 'visible');
        } else {
          canScrollX = width < scrollWidth && (css.overflowX === 'auto' || css.overflowX === 'scroll');
          canScrollY = height < scrollHeight && (css.overflowY === 'auto' || css.overflowY === 'scroll');
        }

        vx = canScrollX && (abs(right - x) <= sens && scrollPosX + width < scrollWidth) - (abs(left - x) <= sens && !!scrollPosX);
        vy = canScrollY && (abs(bottom - y) <= sens && scrollPosY + height < scrollHeight) - (abs(top - y) <= sens && !!scrollPosY);

        if (!autoScrolls[layersOut]) {
          for (var i = 0; i <= layersOut; i++) {
            if (!autoScrolls[i]) {
              autoScrolls[i] = {};
            }
          }
        }

        if (autoScrolls[layersOut].vx != vx || autoScrolls[layersOut].vy != vy || autoScrolls[layersOut].el !== el) {
          autoScrolls[layersOut].el = el;
          autoScrolls[layersOut].vx = vx;
          autoScrolls[layersOut].vy = vy;
          clearInterval(autoScrolls[layersOut].pid);

          if (el && (vx != 0 || vy != 0)) {
            scrollThisInstance = true;
            /* jshint loopfunc:true */

            autoScrolls[layersOut].pid = setInterval(function () {
              // emulate drag over during autoscroll (fallback), emulating native DnD behaviour
              if (isFallback && this.layer === 0) {
                Sortable.active._emulateDragOver(true);

                Sortable.active._onTouchMove(touchEvt, true);
              }

              var scrollOffsetY = autoScrolls[this.layer].vy ? autoScrolls[this.layer].vy * speed : 0;
              var scrollOffsetX = autoScrolls[this.layer].vx ? autoScrolls[this.layer].vx * speed : 0;

              if ('function' === typeof scrollCustomFn) {
                if (scrollCustomFn.call(_this, scrollOffsetX, scrollOffsetY, evt, touchEvt, autoScrolls[this.layer].el) !== 'continue') {
                  return;
                }
              }

              _scrollBy(autoScrolls[this.layer].el, scrollOffsetX, scrollOffsetY);
            }.bind({
              layer: layersOut
            }), 24);
          }
        }

        layersOut++;
      } while (options.bubbleScroll && currentParent !== winScroller && (currentParent = _getParentAutoScrollElement(currentParent, false)));

      scrolling = scrollThisInstance; // in case another function catches scrolling as false in between when it is not
    }
  }, 30),
      _clearAutoScrolls = function _clearAutoScrolls() {
    autoScrolls.forEach(function (autoScroll) {
      clearInterval(autoScroll.pid);
    });
    autoScrolls = [];
  },
      _prepareGroup = function _prepareGroup(options) {
    function toFn(value, pull) {
      return function (to, from, dragEl, evt) {
        var sameGroup = to.options.group.name && from.options.group.name && to.options.group.name === from.options.group.name;

        if (value == null && (pull || sameGroup)) {
          // Default pull value
          // Default pull and put value if same group
          return true;
        } else if (value == null || value === false) {
          return false;
        } else if (pull && value === 'clone') {
          return value;
        } else if (typeof value === 'function') {
          return toFn(value(to, from, dragEl, evt), pull)(to, from, dragEl, evt);
        } else {
          var otherGroup = (pull ? to : from).options.group.name;
          return value === true || typeof value === 'string' && value === otherGroup || value.join && value.indexOf(otherGroup) > -1;
        }
      };
    }

    var group = {};
    var originalGroup = options.group;

    if (!originalGroup || _typeof2(originalGroup) != 'object') {
      originalGroup = {
        name: originalGroup
      };
    }

    group.name = originalGroup.name;
    group.checkPull = toFn(originalGroup.pull, true);
    group.checkPut = toFn(originalGroup.put);
    group.revertClone = originalGroup.revertClone;
    options.group = group;
  },
      _checkAlignment = function _checkAlignment(evt) {
    if (!dragEl || !dragEl.parentNode) return;
    dragEl.parentNode[expando] && dragEl.parentNode[expando]._computeIsAligned(evt);
  },
      _hideGhostForTarget = function _hideGhostForTarget() {
    if (!supportCssPointerEvents && ghostEl) {
      _css(ghostEl, 'display', 'none');
    }
  },
      _unhideGhostForTarget = function _unhideGhostForTarget() {
    if (!supportCssPointerEvents && ghostEl) {
      _css(ghostEl, 'display', '');
    }
  }; // #1184 fix - Prevent click event on fallback if dragged but item not changed position


  document.addEventListener('click', function (evt) {
    if (ignoreNextClick) {
      evt.preventDefault();
      evt.stopPropagation && evt.stopPropagation();
      evt.stopImmediatePropagation && evt.stopImmediatePropagation();
      ignoreNextClick = false;
      return false;
    }
  }, true);

  var nearestEmptyInsertDetectEvent = function nearestEmptyInsertDetectEvent(evt) {
    if (dragEl) {
      evt = evt.touches ? evt.touches[0] : evt;

      var nearest = _detectNearestEmptySortable(evt.clientX, evt.clientY);

      if (nearest) {
        // Create imitation event
        var event = {};

        for (var i in evt) {
          event[i] = evt[i];
        }

        event.target = event.rootEl = nearest;
        event.preventDefault = void 0;
        event.stopPropagation = void 0;

        nearest[expando]._onDragOver(event);
      }
    }
  };
  /**
   * @class  Sortable
   * @param  {HTMLElement}  el
   * @param  {Object}       [options]
   */


  function Sortable(el, options) {
    if (!(el && el.nodeType && el.nodeType === 1)) {
      throw 'Sortable: `el` must be HTMLElement, not ' + {}.toString.call(el);
    }

    this.el = el; // root element

    this.options = options = _extend({}, options); // Export instance

    el[expando] = this; // Default options

    var defaults = {
      group: null,
      sort: true,
      disabled: false,
      store: null,
      handle: null,
      scroll: true,
      scrollSensitivity: 30,
      scrollSpeed: 10,
      bubbleScroll: true,
      draggable: /[uo]l/i.test(el.nodeName) ? '>li' : '>*',
      swapThreshold: 1,
      // percentage; 0 <= x <= 1
      invertSwap: false,
      // invert always
      invertedSwapThreshold: null,
      // will be set to same as swapThreshold if default
      removeCloneOnHide: true,
      direction: function direction() {
        return _detectDirection(el, this.options);
      },
      ghostClass: 'sortable-ghost',
      chosenClass: 'sortable-chosen',
      dragClass: 'sortable-drag',
      ignore: 'a, img',
      filter: null,
      preventOnFilter: true,
      animation: 0,
      easing: null,
      setData: function setData(dataTransfer, dragEl) {
        dataTransfer.setData('Text', dragEl.textContent);
      },
      dropBubble: false,
      dragoverBubble: false,
      dataIdAttr: 'data-id',
      delay: 0,
      delayOnTouchOnly: false,
      touchStartThreshold: parseInt(window.devicePixelRatio, 10) || 1,
      forceFallback: false,
      fallbackClass: 'sortable-fallback',
      fallbackOnBody: false,
      fallbackTolerance: 0,
      fallbackOffset: {
        x: 0,
        y: 0
      },
      supportPointer: Sortable.supportPointer !== false && 'PointerEvent' in window,
      emptyInsertThreshold: 5
    }; // Set default options

    for (var name in defaults) {
      !(name in options) && (options[name] = defaults[name]);
    }

    _prepareGroup(options); // Bind all private methods


    for (var fn in this) {
      if (fn.charAt(0) === '_' && typeof this[fn] === 'function') {
        this[fn] = this[fn].bind(this);
      }
    } // Setup drag mode


    this.nativeDraggable = options.forceFallback ? false : supportDraggable;

    if (this.nativeDraggable) {
      // Touch start threshold cannot be greater than the native dragstart threshold
      this.options.touchStartThreshold = 1;
    } // Bind events


    if (options.supportPointer) {
      _on(el, 'pointerdown', this._onTapStart);
    } else {
      _on(el, 'mousedown', this._onTapStart);

      _on(el, 'touchstart', this._onTapStart);
    }

    if (this.nativeDraggable) {
      _on(el, 'dragover', this);

      _on(el, 'dragenter', this);
    }

    sortables.push(this.el); // Restore sorting

    options.store && options.store.get && this.sort(options.store.get(this) || []);
  }

  Sortable.prototype =
  /** @lends Sortable.prototype */
  {
    constructor: Sortable,
    _computeIsAligned: function _computeIsAligned(evt) {
      var target;

      if (ghostEl && !supportCssPointerEvents) {
        _hideGhostForTarget();

        target = document.elementFromPoint(evt.clientX, evt.clientY);

        _unhideGhostForTarget();
      } else {
        target = evt.target;
      }

      target = _closest(target, this.options.draggable, this.el, false);
      if (_alignedSilent) return;
      if (!dragEl || dragEl.parentNode !== this.el) return;
      var children = this.el.children;

      for (var i = 0; i < children.length; i++) {
        // Don't change for target in case it is changed to aligned before onDragOver is fired
        if (_closest(children[i], this.options.draggable, this.el, false) && children[i] !== target) {
          children[i].sortableMouseAligned = _isClientInRowColumn(evt.clientX, evt.clientY, children[i], this._getDirection(evt, null), this.options);
        }
      } // Used for nulling last target when not in element, nothing to do with checking if aligned


      if (!_closest(target, this.options.draggable, this.el, true)) {
        lastTarget = null;
      }

      _alignedSilent = true;
      setTimeout(function () {
        _alignedSilent = false;
      }, 30);
    },
    _getDirection: function _getDirection(evt, target) {
      return typeof this.options.direction === 'function' ? this.options.direction.call(this, evt, target, dragEl) : this.options.direction;
    },
    _onTapStart: function _onTapStart(
    /** Event|TouchEvent */
    evt) {
      if (!evt.cancelable) return;

      var _this = this,
          el = this.el,
          options = this.options,
          preventOnFilter = options.preventOnFilter,
          type = evt.type,
          touch = evt.touches && evt.touches[0],
          target = (touch || evt).target,
          originalTarget = evt.target.shadowRoot && (evt.path && evt.path[0] || evt.composedPath && evt.composedPath()[0]) || target,
          filter = options.filter,
          startIndex,
          startDraggableIndex;

      _saveInputCheckedState(el); // Don't trigger start event when an element is been dragged, otherwise the evt.oldindex always wrong when set option.group.


      if (dragEl) {
        return;
      }

      if (/mousedown|pointerdown/.test(type) && evt.button !== 0 || options.disabled) {
        return; // only left button and enabled
      } // cancel dnd if original target is content editable


      if (originalTarget.isContentEditable) {
        return;
      }

      target = _closest(target, options.draggable, el, false);

      if (lastDownEl === target) {
        // Ignoring duplicate `down`
        return;
      } // Get the index of the dragged element within its parent


      startIndex = _index(target);
      startDraggableIndex = _index(target, options.draggable); // Check filter

      if (typeof filter === 'function') {
        if (filter.call(this, evt, target, this)) {
          _dispatchEvent(_this, originalTarget, 'filter', target, el, el, startIndex, undefined, startDraggableIndex);

          preventOnFilter && evt.cancelable && evt.preventDefault();
          return; // cancel dnd
        }
      } else if (filter) {
        filter = filter.split(',').some(function (criteria) {
          criteria = _closest(originalTarget, criteria.trim(), el, false);

          if (criteria) {
            _dispatchEvent(_this, criteria, 'filter', target, el, el, startIndex, undefined, startDraggableIndex);

            return true;
          }
        });

        if (filter) {
          preventOnFilter && evt.cancelable && evt.preventDefault();
          return; // cancel dnd
        }
      }

      if (options.handle && !_closest(originalTarget, options.handle, el, false)) {
        return;
      } // Prepare `dragstart`


      this._prepareDragStart(evt, touch, target, startIndex, startDraggableIndex);
    },
    _handleAutoScroll: function _handleAutoScroll(evt, fallback) {
      if (!dragEl || !this.options.scroll) return;

      var x = evt.clientX,
          y = evt.clientY,
          elem = document.elementFromPoint(x, y),
          _this = this; // IE does not seem to have native autoscroll,
      // Edge's autoscroll seems too conditional,
      // MACOS Safari does not have autoscroll,
      // Firefox and Chrome are good


      if (fallback || Edge || IE11OrLess || Safari) {
        _autoScroll(evt, _this.options, elem, fallback); // Listener for pointer element change


        var ogElemScroller = _getParentAutoScrollElement(elem, true);

        if (scrolling && (!pointerElemChangedInterval || x !== lastPointerElemX || y !== lastPointerElemY)) {
          pointerElemChangedInterval && clearInterval(pointerElemChangedInterval); // Detect for pointer elem change, emulating native DnD behaviour

          pointerElemChangedInterval = setInterval(function () {
            if (!dragEl) return; // could also check if scroll direction on newElem changes due to parent autoscrolling

            var newElem = _getParentAutoScrollElement(document.elementFromPoint(x, y), true);

            if (newElem !== ogElemScroller) {
              ogElemScroller = newElem;

              _clearAutoScrolls();

              _autoScroll(evt, _this.options, ogElemScroller, fallback);
            }
          }, 10);
          lastPointerElemX = x;
          lastPointerElemY = y;
        }
      } else {
        // if DnD is enabled (and browser has good autoscrolling), first autoscroll will already scroll, so get parent autoscroll of first autoscroll
        if (!_this.options.bubbleScroll || _getParentAutoScrollElement(elem, true) === _getWindowScrollingElement()) {
          _clearAutoScrolls();

          return;
        }

        _autoScroll(evt, _this.options, _getParentAutoScrollElement(elem, false), false);
      }
    },
    _prepareDragStart: function _prepareDragStart(
    /** Event */
    evt,
    /** Touch */
    touch,
    /** HTMLElement */
    target,
    /** Number */
    startIndex,
    /** Number */
    startDraggableIndex) {
      var _this = this,
          el = _this.el,
          options = _this.options,
          ownerDocument = el.ownerDocument,
          dragStartFn;

      if (target && !dragEl && target.parentNode === el) {
        rootEl = el;
        dragEl = target;
        parentEl = dragEl.parentNode;
        nextEl = dragEl.nextSibling;
        lastDownEl = target;
        activeGroup = options.group;
        oldIndex = startIndex;
        oldDraggableIndex = startDraggableIndex;
        tapEvt = {
          target: dragEl,
          clientX: (touch || evt).clientX,
          clientY: (touch || evt).clientY
        };
        this._lastX = (touch || evt).clientX;
        this._lastY = (touch || evt).clientY;
        dragEl.style['will-change'] = 'all'; // undo animation if needed

        dragEl.style.transition = '';
        dragEl.style.transform = '';

        dragStartFn = function dragStartFn() {
          // Delayed drag has been triggered
          // we can re-enable the events: touchmove/mousemove
          _this._disableDelayedDragEvents();

          if (!FireFox && _this.nativeDraggable) {
            dragEl.draggable = true;
          } // Bind the events: dragstart/dragend


          _this._triggerDragStart(evt, touch); // Drag start event


          _dispatchEvent(_this, rootEl, 'choose', dragEl, rootEl, rootEl, oldIndex, undefined, oldDraggableIndex); // Chosen item


          _toggleClass(dragEl, options.chosenClass, true);
        }; // Disable "draggable"


        options.ignore.split(',').forEach(function (criteria) {
          _find(dragEl, criteria.trim(), _disableDraggable);
        });

        _on(ownerDocument, 'dragover', nearestEmptyInsertDetectEvent);

        _on(ownerDocument, 'mousemove', nearestEmptyInsertDetectEvent);

        _on(ownerDocument, 'touchmove', nearestEmptyInsertDetectEvent);

        _on(ownerDocument, 'mouseup', _this._onDrop);

        _on(ownerDocument, 'touchend', _this._onDrop);

        _on(ownerDocument, 'touchcancel', _this._onDrop); // Make dragEl draggable (must be before delay for FireFox)


        if (FireFox && this.nativeDraggable) {
          this.options.touchStartThreshold = 4;
          dragEl.draggable = true;
        } // Delay is impossible for native DnD in Edge or IE


        if (options.delay && (options.delayOnTouchOnly ? touch : true) && (!this.nativeDraggable || !(Edge || IE11OrLess))) {
          // If the user moves the pointer or let go the click or touch
          // before the delay has been reached:
          // disable the delayed drag
          _on(ownerDocument, 'mouseup', _this._disableDelayedDrag);

          _on(ownerDocument, 'touchend', _this._disableDelayedDrag);

          _on(ownerDocument, 'touchcancel', _this._disableDelayedDrag);

          _on(ownerDocument, 'mousemove', _this._delayedDragTouchMoveHandler);

          _on(ownerDocument, 'touchmove', _this._delayedDragTouchMoveHandler);

          options.supportPointer && _on(ownerDocument, 'pointermove', _this._delayedDragTouchMoveHandler);
          _this._dragStartTimer = setTimeout(dragStartFn, options.delay);
        } else {
          dragStartFn();
        }
      }
    },
    _delayedDragTouchMoveHandler: function _delayedDragTouchMoveHandler(
    /** TouchEvent|PointerEvent **/
    e) {
      var touch = e.touches ? e.touches[0] : e;

      if (max(abs(touch.clientX - this._lastX), abs(touch.clientY - this._lastY)) >= Math.floor(this.options.touchStartThreshold / (this.nativeDraggable && window.devicePixelRatio || 1))) {
        this._disableDelayedDrag();
      }
    },
    _disableDelayedDrag: function _disableDelayedDrag() {
      dragEl && _disableDraggable(dragEl);
      clearTimeout(this._dragStartTimer);

      this._disableDelayedDragEvents();
    },
    _disableDelayedDragEvents: function _disableDelayedDragEvents() {
      var ownerDocument = this.el.ownerDocument;

      _off(ownerDocument, 'mouseup', this._disableDelayedDrag);

      _off(ownerDocument, 'touchend', this._disableDelayedDrag);

      _off(ownerDocument, 'touchcancel', this._disableDelayedDrag);

      _off(ownerDocument, 'mousemove', this._delayedDragTouchMoveHandler);

      _off(ownerDocument, 'touchmove', this._delayedDragTouchMoveHandler);

      _off(ownerDocument, 'pointermove', this._delayedDragTouchMoveHandler);
    },
    _triggerDragStart: function _triggerDragStart(
    /** Event */
    evt,
    /** Touch */
    touch) {
      touch = touch || (evt.pointerType == 'touch' ? evt : null);

      if (!this.nativeDraggable || touch) {
        if (this.options.supportPointer) {
          _on(document, 'pointermove', this._onTouchMove);
        } else if (touch) {
          _on(document, 'touchmove', this._onTouchMove);
        } else {
          _on(document, 'mousemove', this._onTouchMove);
        }
      } else {
        _on(dragEl, 'dragend', this);

        _on(rootEl, 'dragstart', this._onDragStart);
      }

      try {
        if (document.selection) {
          // Timeout neccessary for IE9
          _nextTick(function () {
            document.selection.empty();
          });
        } else {
          window.getSelection().removeAllRanges();
        }
      } catch (err) {}
    },
    _dragStarted: function _dragStarted(fallback, evt) {
      awaitingDragStarted = false;

      if (rootEl && dragEl) {
        if (this.nativeDraggable) {
          _on(document, 'dragover', this._handleAutoScroll);

          _on(document, 'dragover', _checkAlignment);
        }

        var options = this.options; // Apply effect

        !fallback && _toggleClass(dragEl, options.dragClass, false);

        _toggleClass(dragEl, options.ghostClass, true); // In case dragging an animated element


        _css(dragEl, 'transform', '');

        Sortable.active = this;
        fallback && this._appendGhost(); // Drag start event

        _dispatchEvent(this, rootEl, 'start', dragEl, rootEl, rootEl, oldIndex, undefined, oldDraggableIndex, undefined, evt);
      } else {
        this._nulling();
      }
    },
    _emulateDragOver: function _emulateDragOver(forAutoScroll) {
      if (touchEvt) {
        if (this._lastX === touchEvt.clientX && this._lastY === touchEvt.clientY && !forAutoScroll) {
          return;
        }

        this._lastX = touchEvt.clientX;
        this._lastY = touchEvt.clientY;

        _hideGhostForTarget();

        var target = document.elementFromPoint(touchEvt.clientX, touchEvt.clientY);
        var parent = target;

        while (target && target.shadowRoot) {
          target = target.shadowRoot.elementFromPoint(touchEvt.clientX, touchEvt.clientY);
          if (target === parent) break;
          parent = target;
        }

        if (parent) {
          do {
            if (parent[expando]) {
              var inserted;
              inserted = parent[expando]._onDragOver({
                clientX: touchEvt.clientX,
                clientY: touchEvt.clientY,
                target: target,
                rootEl: parent
              });

              if (inserted && !this.options.dragoverBubble) {
                break;
              }
            }

            target = parent; // store last element
          }
          /* jshint boss:true */
          while (parent = parent.parentNode);
        }

        dragEl.parentNode[expando]._computeIsAligned(touchEvt);

        _unhideGhostForTarget();
      }
    },
    _onTouchMove: function _onTouchMove(
    /**TouchEvent*/
    evt, forAutoScroll) {
      if (tapEvt) {
        var options = this.options,
            fallbackTolerance = options.fallbackTolerance,
            fallbackOffset = options.fallbackOffset,
            touch = evt.touches ? evt.touches[0] : evt,
            matrix = ghostEl && _matrix(ghostEl),
            scaleX = ghostEl && matrix && matrix.a,
            scaleY = ghostEl && matrix && matrix.d,
            relativeScrollOffset = PositionGhostAbsolutely && ghostRelativeParent && _getRelativeScrollOffset(ghostRelativeParent),
            dx = (touch.clientX - tapEvt.clientX + fallbackOffset.x) / (scaleX || 1) + (relativeScrollOffset ? relativeScrollOffset[0] - ghostRelativeParentInitialScroll[0] : 0) / (scaleX || 1),
            dy = (touch.clientY - tapEvt.clientY + fallbackOffset.y) / (scaleY || 1) + (relativeScrollOffset ? relativeScrollOffset[1] - ghostRelativeParentInitialScroll[1] : 0) / (scaleY || 1),
            translate3d = evt.touches ? 'translate3d(' + dx + 'px,' + dy + 'px,0)' : 'translate(' + dx + 'px,' + dy + 'px)'; // only set the status to dragging, when we are actually dragging


        if (!Sortable.active && !awaitingDragStarted) {
          if (fallbackTolerance && min(abs(touch.clientX - this._lastX), abs(touch.clientY - this._lastY)) < fallbackTolerance) {
            return;
          }

          this._onDragStart(evt, true);
        }

        !forAutoScroll && this._handleAutoScroll(touch, true);
        moved = true;
        touchEvt = touch;

        _css(ghostEl, 'webkitTransform', translate3d);

        _css(ghostEl, 'mozTransform', translate3d);

        _css(ghostEl, 'msTransform', translate3d);

        _css(ghostEl, 'transform', translate3d);

        evt.cancelable && evt.preventDefault();
      }
    },
    _appendGhost: function _appendGhost() {
      // Bug if using scale(): https://stackoverflow.com/questions/2637058
      // Not being adjusted for
      if (!ghostEl) {
        var container = this.options.fallbackOnBody ? document.body : rootEl,
            rect = _getRect(dragEl, true, container, !PositionGhostAbsolutely),
            css = _css(dragEl),
            options = this.options; // Position absolutely


        if (PositionGhostAbsolutely) {
          // Get relatively positioned parent
          ghostRelativeParent = container;

          while (_css(ghostRelativeParent, 'position') === 'static' && _css(ghostRelativeParent, 'transform') === 'none' && ghostRelativeParent !== document) {
            ghostRelativeParent = ghostRelativeParent.parentNode;
          }

          if (ghostRelativeParent !== document) {
            var ghostRelativeParentRect = _getRect(ghostRelativeParent, true);

            rect.top -= ghostRelativeParentRect.top;
            rect.left -= ghostRelativeParentRect.left;
          }

          if (ghostRelativeParent !== document.body && ghostRelativeParent !== document.documentElement) {
            if (ghostRelativeParent === document) ghostRelativeParent = _getWindowScrollingElement();
            rect.top += ghostRelativeParent.scrollTop;
            rect.left += ghostRelativeParent.scrollLeft;
          } else {
            ghostRelativeParent = _getWindowScrollingElement();
          }

          ghostRelativeParentInitialScroll = _getRelativeScrollOffset(ghostRelativeParent);
        }

        ghostEl = dragEl.cloneNode(true);

        _toggleClass(ghostEl, options.ghostClass, false);

        _toggleClass(ghostEl, options.fallbackClass, true);

        _toggleClass(ghostEl, options.dragClass, true);

        _css(ghostEl, 'box-sizing', 'border-box');

        _css(ghostEl, 'margin', 0);

        _css(ghostEl, 'top', rect.top);

        _css(ghostEl, 'left', rect.left);

        _css(ghostEl, 'width', rect.width);

        _css(ghostEl, 'height', rect.height);

        _css(ghostEl, 'opacity', '0.8');

        _css(ghostEl, 'position', PositionGhostAbsolutely ? 'absolute' : 'fixed');

        _css(ghostEl, 'zIndex', '100000');

        _css(ghostEl, 'pointerEvents', 'none');

        container.appendChild(ghostEl);
      }
    },
    _onDragStart: function _onDragStart(
    /**Event*/
    evt,
    /**boolean*/
    fallback) {
      var _this = this;

      var dataTransfer = evt.dataTransfer;
      var options = _this.options; // Setup clone

      cloneEl = _clone(dragEl);
      cloneEl.draggable = false;
      cloneEl.style['will-change'] = '';

      this._hideClone();

      _toggleClass(cloneEl, _this.options.chosenClass, false); // #1143: IFrame support workaround


      _this._cloneId = _nextTick(function () {
        if (!_this.options.removeCloneOnHide) {
          rootEl.insertBefore(cloneEl, dragEl);
        }

        _dispatchEvent(_this, rootEl, 'clone', dragEl);
      });
      !fallback && _toggleClass(dragEl, options.dragClass, true); // Set proper drop events

      if (fallback) {
        ignoreNextClick = true;
        _this._loopId = setInterval(_this._emulateDragOver, 50);
      } else {
        // Undo what was set in _prepareDragStart before drag started
        _off(document, 'mouseup', _this._onDrop);

        _off(document, 'touchend', _this._onDrop);

        _off(document, 'touchcancel', _this._onDrop);

        if (dataTransfer) {
          dataTransfer.effectAllowed = 'move';
          options.setData && options.setData.call(_this, dataTransfer, dragEl);
        }

        _on(document, 'drop', _this); // #1276 fix:


        _css(dragEl, 'transform', 'translateZ(0)');
      }

      awaitingDragStarted = true;
      _this._dragStartId = _nextTick(_this._dragStarted.bind(_this, fallback, evt));

      _on(document, 'selectstart', _this);

      if (Safari) {
        _css(document.body, 'user-select', 'none');
      }
    },
    // Returns true - if no further action is needed (either inserted or another condition)
    _onDragOver: function _onDragOver(
    /**Event*/
    evt) {
      var el = this.el,
          target = evt.target,
          dragRect,
          targetRect,
          revert,
          options = this.options,
          group = options.group,
          activeSortable = Sortable.active,
          isOwner = activeGroup === group,
          canSort = options.sort,
          _this = this;

      if (_silent) return; // Return invocation when dragEl is inserted (or completed)

      function completed(insertion) {
        if (insertion) {
          if (isOwner) {
            activeSortable._hideClone();
          } else {
            activeSortable._showClone(_this);
          }

          if (activeSortable) {
            // Set ghost class to new sortable's ghost class
            _toggleClass(dragEl, putSortable ? putSortable.options.ghostClass : activeSortable.options.ghostClass, false);

            _toggleClass(dragEl, options.ghostClass, true);
          }

          if (putSortable !== _this && _this !== Sortable.active) {
            putSortable = _this;
          } else if (_this === Sortable.active) {
            putSortable = null;
          } // Animation


          dragRect && _this._animate(dragRect, dragEl);
          target && targetRect && _this._animate(targetRect, target);
        } // Null lastTarget if it is not inside a previously swapped element


        if (target === dragEl && !dragEl.animated || target === el && !target.animated) {
          lastTarget = null;
        } // no bubbling and not fallback


        if (!options.dragoverBubble && !evt.rootEl && target !== document) {
          _this._handleAutoScroll(evt);

          dragEl.parentNode[expando]._computeIsAligned(evt); // Do not detect for empty insert if already inserted


          !insertion && nearestEmptyInsertDetectEvent(evt);
        }

        !options.dragoverBubble && evt.stopPropagation && evt.stopPropagation();
        return true;
      } // Call when dragEl has been inserted


      function changed() {
        _dispatchEvent(_this, rootEl, 'change', target, el, rootEl, oldIndex, _index(dragEl), oldDraggableIndex, _index(dragEl, options.draggable), evt);
      }

      if (evt.preventDefault !== void 0) {
        evt.cancelable && evt.preventDefault();
      }

      moved = true;
      target = _closest(target, options.draggable, el, true); // target is dragEl or target is animated

      if (dragEl.contains(evt.target) || target.animated) {
        return completed(false);
      }

      if (target !== dragEl) {
        ignoreNextClick = false;
      }

      if (activeSortable && !options.disabled && (isOwner ? canSort || (revert = !rootEl.contains(dragEl)) // Reverting item into the original list
      : putSortable === this || (this.lastPutMode = activeGroup.checkPull(this, activeSortable, dragEl, evt)) && group.checkPut(this, activeSortable, dragEl, evt))) {
        var axis = this._getDirection(evt, target);

        dragRect = _getRect(dragEl);

        if (revert) {
          this._hideClone();

          parentEl = rootEl; // actualization

          if (nextEl) {
            rootEl.insertBefore(dragEl, nextEl);
          } else {
            rootEl.appendChild(dragEl);
          }

          return completed(true);
        }

        var elLastChild = _lastChild(el);

        if (!elLastChild || _ghostIsLast(evt, axis, el) && !elLastChild.animated) {
          // assign target only if condition is true
          if (elLastChild && el === evt.target) {
            target = elLastChild;
          }

          if (target) {
            targetRect = _getRect(target);
          }

          if (isOwner) {
            activeSortable._hideClone();
          } else {
            activeSortable._showClone(this);
          }

          if (_onMove(rootEl, el, dragEl, dragRect, target, targetRect, evt, !!target) !== false) {
            el.appendChild(dragEl);
            parentEl = el; // actualization

            realDragElRect = null;
            changed();
            return completed(true);
          }
        } else if (target && target !== dragEl && target.parentNode === el) {
          var direction = 0,
              targetBeforeFirstSwap,
              aligned = target.sortableMouseAligned,
              differentLevel = dragEl.parentNode !== el,
              side1 = axis === 'vertical' ? 'top' : 'left',
              scrolledPastTop = _isScrolledPast(target, 'top') || _isScrolledPast(dragEl, 'top'),
              scrollBefore = scrolledPastTop ? scrolledPastTop.scrollTop : void 0;

          if (lastTarget !== target) {
            lastMode = null;
            targetBeforeFirstSwap = _getRect(target)[side1];
            pastFirstInvertThresh = false;
          } // Reference: https://www.lucidchart.com/documents/view/10fa0e93-e362-4126-aca2-b709ee56bd8b/0


          if (_isElInRowColumn(dragEl, target, axis) && aligned || differentLevel || scrolledPastTop || options.invertSwap || lastMode === 'insert' || // Needed, in the case that we are inside target and inserted because not aligned... aligned will stay false while inside
          // and lastMode will change to 'insert', but we must swap
          lastMode === 'swap') {
            // New target that we will be inside
            if (lastMode !== 'swap') {
              isCircumstantialInvert = options.invertSwap || differentLevel;
            }

            direction = _getSwapDirection(evt, target, axis, options.swapThreshold, options.invertedSwapThreshold == null ? options.swapThreshold : options.invertedSwapThreshold, isCircumstantialInvert, lastTarget === target);
            lastMode = 'swap';
          } else {
            // Insert at position
            direction = _getInsertDirection(target);
            lastMode = 'insert';
          }

          if (direction === 0) return completed(false);
          realDragElRect = null;
          lastTarget = target;
          lastDirection = direction;
          targetRect = _getRect(target);
          var nextSibling = target.nextElementSibling,
              after = false;
          after = direction === 1;

          var moveVector = _onMove(rootEl, el, dragEl, dragRect, target, targetRect, evt, after);

          if (moveVector !== false) {
            if (moveVector === 1 || moveVector === -1) {
              after = moveVector === 1;
            }

            _silent = true;
            setTimeout(_unsilent, 30);

            if (isOwner) {
              activeSortable._hideClone();
            } else {
              activeSortable._showClone(this);
            }

            if (after && !nextSibling) {
              el.appendChild(dragEl);
            } else {
              target.parentNode.insertBefore(dragEl, after ? nextSibling : target);
            } // Undo chrome's scroll adjustment


            if (scrolledPastTop) {
              _scrollBy(scrolledPastTop, 0, scrollBefore - scrolledPastTop.scrollTop);
            }

            parentEl = dragEl.parentNode; // actualization
            // must be done before animation

            if (targetBeforeFirstSwap !== undefined && !isCircumstantialInvert) {
              targetMoveDistance = abs(targetBeforeFirstSwap - _getRect(target)[side1]);
            }

            changed();
            return completed(true);
          }
        }

        if (el.contains(dragEl)) {
          return completed(false);
        }
      }

      return false;
    },
    _animate: function _animate(prevRect, target) {
      var ms = this.options.animation;

      if (ms) {
        var currentRect = _getRect(target);

        if (target === dragEl) {
          realDragElRect = currentRect;
        }

        if (prevRect.nodeType === 1) {
          prevRect = _getRect(prevRect);
        } // Check if actually moving position


        if (prevRect.left + prevRect.width / 2 !== currentRect.left + currentRect.width / 2 || prevRect.top + prevRect.height / 2 !== currentRect.top + currentRect.height / 2) {
          var matrix = _matrix(this.el),
              scaleX = matrix && matrix.a,
              scaleY = matrix && matrix.d;

          _css(target, 'transition', 'none');

          _css(target, 'transform', 'translate3d(' + (prevRect.left - currentRect.left) / (scaleX ? scaleX : 1) + 'px,' + (prevRect.top - currentRect.top) / (scaleY ? scaleY : 1) + 'px,0)');

          this._repaint(target);

          _css(target, 'transition', 'transform ' + ms + 'ms' + (this.options.easing ? ' ' + this.options.easing : ''));

          _css(target, 'transform', 'translate3d(0,0,0)');
        }

        typeof target.animated === 'number' && clearTimeout(target.animated);
        target.animated = setTimeout(function () {
          _css(target, 'transition', '');

          _css(target, 'transform', '');

          target.animated = false;
        }, ms);
      }
    },
    _repaint: function _repaint(target) {
      return target.offsetWidth;
    },
    _offMoveEvents: function _offMoveEvents() {
      _off(document, 'touchmove', this._onTouchMove);

      _off(document, 'pointermove', this._onTouchMove);

      _off(document, 'dragover', nearestEmptyInsertDetectEvent);

      _off(document, 'mousemove', nearestEmptyInsertDetectEvent);

      _off(document, 'touchmove', nearestEmptyInsertDetectEvent);
    },
    _offUpEvents: function _offUpEvents() {
      var ownerDocument = this.el.ownerDocument;

      _off(ownerDocument, 'mouseup', this._onDrop);

      _off(ownerDocument, 'touchend', this._onDrop);

      _off(ownerDocument, 'pointerup', this._onDrop);

      _off(ownerDocument, 'touchcancel', this._onDrop);

      _off(document, 'selectstart', this);
    },
    _onDrop: function _onDrop(
    /**Event*/
    evt) {
      var el = this.el,
          options = this.options;
      awaitingDragStarted = false;
      scrolling = false;
      isCircumstantialInvert = false;
      pastFirstInvertThresh = false;
      clearInterval(this._loopId);
      clearInterval(pointerElemChangedInterval);

      _clearAutoScrolls();

      _cancelThrottle();

      clearTimeout(this._dragStartTimer);

      _cancelNextTick(this._cloneId);

      _cancelNextTick(this._dragStartId); // Unbind events


      _off(document, 'mousemove', this._onTouchMove);

      if (this.nativeDraggable) {
        _off(document, 'drop', this);

        _off(el, 'dragstart', this._onDragStart);

        _off(document, 'dragover', this._handleAutoScroll);

        _off(document, 'dragover', _checkAlignment);
      }

      if (Safari) {
        _css(document.body, 'user-select', '');
      }

      this._offMoveEvents();

      this._offUpEvents();

      if (evt) {
        if (moved) {
          evt.cancelable && evt.preventDefault();
          !options.dropBubble && evt.stopPropagation();
        }

        ghostEl && ghostEl.parentNode && ghostEl.parentNode.removeChild(ghostEl);

        if (rootEl === parentEl || putSortable && putSortable.lastPutMode !== 'clone') {
          // Remove clone
          cloneEl && cloneEl.parentNode && cloneEl.parentNode.removeChild(cloneEl);
        }

        if (dragEl) {
          if (this.nativeDraggable) {
            _off(dragEl, 'dragend', this);
          }

          _disableDraggable(dragEl);

          dragEl.style['will-change'] = ''; // Remove class's

          _toggleClass(dragEl, putSortable ? putSortable.options.ghostClass : this.options.ghostClass, false);

          _toggleClass(dragEl, this.options.chosenClass, false); // Drag stop event


          _dispatchEvent(this, rootEl, 'unchoose', dragEl, parentEl, rootEl, oldIndex, null, oldDraggableIndex, null, evt);

          if (rootEl !== parentEl) {
            newIndex = _index(dragEl);
            newDraggableIndex = _index(dragEl, options.draggable);

            if (newIndex >= 0) {
              // Add event
              _dispatchEvent(null, parentEl, 'add', dragEl, parentEl, rootEl, oldIndex, newIndex, oldDraggableIndex, newDraggableIndex, evt); // Remove event


              _dispatchEvent(this, rootEl, 'remove', dragEl, parentEl, rootEl, oldIndex, newIndex, oldDraggableIndex, newDraggableIndex, evt); // drag from one list and drop into another


              _dispatchEvent(null, parentEl, 'sort', dragEl, parentEl, rootEl, oldIndex, newIndex, oldDraggableIndex, newDraggableIndex, evt);

              _dispatchEvent(this, rootEl, 'sort', dragEl, parentEl, rootEl, oldIndex, newIndex, oldDraggableIndex, newDraggableIndex, evt);
            }

            putSortable && putSortable.save();
          } else {
            if (dragEl.nextSibling !== nextEl) {
              // Get the index of the dragged element within its parent
              newIndex = _index(dragEl);
              newDraggableIndex = _index(dragEl, options.draggable);

              if (newIndex >= 0) {
                // drag & drop within the same list
                _dispatchEvent(this, rootEl, 'update', dragEl, parentEl, rootEl, oldIndex, newIndex, oldDraggableIndex, newDraggableIndex, evt);

                _dispatchEvent(this, rootEl, 'sort', dragEl, parentEl, rootEl, oldIndex, newIndex, oldDraggableIndex, newDraggableIndex, evt);
              }
            }
          }

          if (Sortable.active) {
            /* jshint eqnull:true */
            if (newIndex == null || newIndex === -1) {
              newIndex = oldIndex;
              newDraggableIndex = oldDraggableIndex;
            }

            _dispatchEvent(this, rootEl, 'end', dragEl, parentEl, rootEl, oldIndex, newIndex, oldDraggableIndex, newDraggableIndex, evt); // Save sorting


            this.save();
          }
        }
      }

      this._nulling();
    },
    _nulling: function _nulling() {
      rootEl = dragEl = parentEl = ghostEl = nextEl = cloneEl = lastDownEl = scrollEl = scrollParentEl = autoScrolls.length = pointerElemChangedInterval = lastPointerElemX = lastPointerElemY = tapEvt = touchEvt = moved = newIndex = oldIndex = lastTarget = lastDirection = realDragElRect = putSortable = activeGroup = Sortable.active = null;
      savedInputChecked.forEach(function (el) {
        el.checked = true;
      });
      savedInputChecked.length = 0;
    },
    handleEvent: function handleEvent(
    /**Event*/
    evt) {
      switch (evt.type) {
        case 'drop':
        case 'dragend':
          this._onDrop(evt);

          break;

        case 'dragenter':
        case 'dragover':
          if (dragEl) {
            this._onDragOver(evt);

            _globalDragOver(evt);
          }

          break;

        case 'selectstart':
          evt.preventDefault();
          break;
      }
    },

    /**
     * Serializes the item into an array of string.
     * @returns {String[]}
     */
    toArray: function toArray() {
      var order = [],
          el,
          children = this.el.children,
          i = 0,
          n = children.length,
          options = this.options;

      for (; i < n; i++) {
        el = children[i];

        if (_closest(el, options.draggable, this.el, false)) {
          order.push(el.getAttribute(options.dataIdAttr) || _generateId(el));
        }
      }

      return order;
    },

    /**
     * Sorts the elements according to the array.
     * @param  {String[]}  order  order of the items
     */
    sort: function sort(order) {
      var items = {},
          rootEl = this.el;
      this.toArray().forEach(function (id, i) {
        var el = rootEl.children[i];

        if (_closest(el, this.options.draggable, rootEl, false)) {
          items[id] = el;
        }
      }, this);
      order.forEach(function (id) {
        if (items[id]) {
          rootEl.removeChild(items[id]);
          rootEl.appendChild(items[id]);
        }
      });
    },

    /**
     * Save the current sorting
     */
    save: function save() {
      var store = this.options.store;
      store && store.set && store.set(this);
    },

    /**
     * For each element in the set, get the first element that matches the selector by testing the element itself and traversing up through its ancestors in the DOM tree.
     * @param   {HTMLElement}  el
     * @param   {String}       [selector]  default: `options.draggable`
     * @returns {HTMLElement|null}
     */
    closest: function closest(el, selector) {
      return _closest(el, selector || this.options.draggable, this.el, false);
    },

    /**
     * Set/get option
     * @param   {string} name
     * @param   {*}      [value]
     * @returns {*}
     */
    option: function option(name, value) {
      var options = this.options;

      if (value === void 0) {
        return options[name];
      } else {
        options[name] = value;

        if (name === 'group') {
          _prepareGroup(options);
        }
      }
    },

    /**
     * Destroy
     */
    destroy: function destroy() {
      var el = this.el;
      el[expando] = null;

      _off(el, 'mousedown', this._onTapStart);

      _off(el, 'touchstart', this._onTapStart);

      _off(el, 'pointerdown', this._onTapStart);

      if (this.nativeDraggable) {
        _off(el, 'dragover', this);

        _off(el, 'dragenter', this);
      } // Remove draggable attributes


      Array.prototype.forEach.call(el.querySelectorAll('[draggable]'), function (el) {
        el.removeAttribute('draggable');
      });

      this._onDrop();

      sortables.splice(sortables.indexOf(this.el), 1);
      this.el = el = null;
    },
    _hideClone: function _hideClone() {
      if (!cloneEl.cloneHidden) {
        _css(cloneEl, 'display', 'none');

        cloneEl.cloneHidden = true;

        if (cloneEl.parentNode && this.options.removeCloneOnHide) {
          cloneEl.parentNode.removeChild(cloneEl);
        }
      }
    },
    _showClone: function _showClone(putSortable) {
      if (putSortable.lastPutMode !== 'clone') {
        this._hideClone();

        return;
      }

      if (cloneEl.cloneHidden) {
        // show clone at dragEl or original position
        if (rootEl.contains(dragEl) && !this.options.group.revertClone) {
          rootEl.insertBefore(cloneEl, dragEl);
        } else if (nextEl) {
          rootEl.insertBefore(cloneEl, nextEl);
        } else {
          rootEl.appendChild(cloneEl);
        }

        if (this.options.group.revertClone) {
          this._animate(dragEl, cloneEl);
        }

        _css(cloneEl, 'display', '');

        cloneEl.cloneHidden = false;
      }
    }
  };

  function _closest(
  /**HTMLElement*/
  el,
  /**String*/
  selector,
  /**HTMLElement*/
  ctx, includeCTX) {
    if (el) {
      ctx = ctx || document;

      do {
        if (selector != null && (selector[0] === '>' ? el.parentNode === ctx && _matches(el, selector) : _matches(el, selector)) || includeCTX && el === ctx) {
          return el;
        }

        if (el === ctx) break;
        /* jshint boss:true */
      } while (el = _getParentOrHost(el));
    }

    return null;
  }

  function _getParentOrHost(el) {
    return el.host && el !== document && el.host.nodeType ? el.host : el.parentNode;
  }

  function _globalDragOver(
  /**Event*/
  evt) {
    if (evt.dataTransfer) {
      evt.dataTransfer.dropEffect = 'move';
    }

    evt.cancelable && evt.preventDefault();
  }

  function _on(el, event, fn) {
    el.addEventListener(event, fn, IE11OrLess ? false : captureMode);
  }

  function _off(el, event, fn) {
    el.removeEventListener(event, fn, IE11OrLess ? false : captureMode);
  }

  function _toggleClass(el, name, state) {
    if (el && name) {
      if (el.classList) {
        el.classList[state ? 'add' : 'remove'](name);
      } else {
        var className = (' ' + el.className + ' ').replace(R_SPACE, ' ').replace(' ' + name + ' ', ' ');
        el.className = (className + (state ? ' ' + name : '')).replace(R_SPACE, ' ');
      }
    }
  }

  function _css(el, prop, val) {
    var style = el && el.style;

    if (style) {
      if (val === void 0) {
        if (document.defaultView && document.defaultView.getComputedStyle) {
          val = document.defaultView.getComputedStyle(el, '');
        } else if (el.currentStyle) {
          val = el.currentStyle;
        }

        return prop === void 0 ? val : val[prop];
      } else {
        if (!(prop in style) && prop.indexOf('webkit') === -1) {
          prop = '-webkit-' + prop;
        }

        style[prop] = val + (typeof val === 'string' ? '' : 'px');
      }
    }
  }

  function _matrix(el) {
    var appliedTransforms = '';

    do {
      var transform = _css(el, 'transform');

      if (transform && transform !== 'none') {
        appliedTransforms = transform + ' ' + appliedTransforms;
      }
      /* jshint boss:true */

    } while (el = el.parentNode);

    if (window.DOMMatrix) {
      return new DOMMatrix(appliedTransforms);
    } else if (window.WebKitCSSMatrix) {
      return new WebKitCSSMatrix(appliedTransforms);
    } else if (window.CSSMatrix) {
      return new CSSMatrix(appliedTransforms);
    }
  }

  function _find(ctx, tagName, iterator) {
    if (ctx) {
      var list = ctx.getElementsByTagName(tagName),
          i = 0,
          n = list.length;

      if (iterator) {
        for (; i < n; i++) {
          iterator(list[i], i);
        }
      }

      return list;
    }

    return [];
  }

  function _dispatchEvent(sortable, rootEl, name, targetEl, toEl, fromEl, startIndex, newIndex, startDraggableIndex, newDraggableIndex, originalEvt) {
    sortable = sortable || rootEl[expando];
    var evt,
        options = sortable.options,
        onName = 'on' + name.charAt(0).toUpperCase() + name.substr(1); // Support for new CustomEvent feature

    if (window.CustomEvent && !IE11OrLess && !Edge) {
      evt = new CustomEvent(name, {
        bubbles: true,
        cancelable: true
      });
    } else {
      evt = document.createEvent('Event');
      evt.initEvent(name, true, true);
    }

    evt.to = toEl || rootEl;
    evt.from = fromEl || rootEl;
    evt.item = targetEl || rootEl;
    evt.clone = cloneEl;
    evt.oldIndex = startIndex;
    evt.newIndex = newIndex;
    evt.oldDraggableIndex = startDraggableIndex;
    evt.newDraggableIndex = newDraggableIndex;
    evt.originalEvent = originalEvt;
    evt.pullMode = putSortable ? putSortable.lastPutMode : undefined;

    if (rootEl) {
      rootEl.dispatchEvent(evt);
    }

    if (options[onName]) {
      options[onName].call(sortable, evt);
    }
  }

  function _onMove(fromEl, toEl, dragEl, dragRect, targetEl, targetRect, originalEvt, willInsertAfter) {
    var evt,
        sortable = fromEl[expando],
        onMoveFn = sortable.options.onMove,
        retVal; // Support for new CustomEvent feature

    if (window.CustomEvent && !IE11OrLess && !Edge) {
      evt = new CustomEvent('move', {
        bubbles: true,
        cancelable: true
      });
    } else {
      evt = document.createEvent('Event');
      evt.initEvent('move', true, true);
    }

    evt.to = toEl;
    evt.from = fromEl;
    evt.dragged = dragEl;
    evt.draggedRect = dragRect;
    evt.related = targetEl || toEl;
    evt.relatedRect = targetRect || _getRect(toEl);
    evt.willInsertAfter = willInsertAfter;
    evt.originalEvent = originalEvt;
    fromEl.dispatchEvent(evt);

    if (onMoveFn) {
      retVal = onMoveFn.call(sortable, evt, originalEvt);
    }

    return retVal;
  }

  function _disableDraggable(el) {
    el.draggable = false;
  }

  function _unsilent() {
    _silent = false;
  }
  /**
   * Gets nth child of el, ignoring hidden children, sortable's elements (does not ignore clone if it's visible)
   * and non-draggable elements
   * @param  {HTMLElement} el       The parent element
   * @param  {Number} childNum      The index of the child
   * @param  {Object} options       Parent Sortable's options
   * @return {HTMLElement}          The child at index childNum, or null if not found
   */


  function _getChild(el, childNum, options) {
    var currentChild = 0,
        i = 0,
        children = el.children;

    while (i < children.length) {
      if (children[i].style.display !== 'none' && children[i] !== ghostEl && children[i] !== dragEl && _closest(children[i], options.draggable, el, false)) {
        if (currentChild === childNum) {
          return children[i];
        }

        currentChild++;
      }

      i++;
    }

    return null;
  }
  /**
   * Gets the last child in the el, ignoring ghostEl or invisible elements (clones)
   * @param  {HTMLElement} el       Parent element
   * @return {HTMLElement}          The last child, ignoring ghostEl
   */


  function _lastChild(el) {
    var last = el.lastElementChild;

    while (last && (last === ghostEl || _css(last, 'display') === 'none')) {
      last = last.previousElementSibling;
    }

    return last || null;
  }

  function _ghostIsLast(evt, axis, el) {
    var elRect = _getRect(_lastChild(el)),
        mouseOnAxis = axis === 'vertical' ? evt.clientY : evt.clientX,
        mouseOnOppAxis = axis === 'vertical' ? evt.clientX : evt.clientY,
        targetS2 = axis === 'vertical' ? elRect.bottom : elRect.right,
        targetS1Opp = axis === 'vertical' ? elRect.left : elRect.top,
        targetS2Opp = axis === 'vertical' ? elRect.right : elRect.bottom,
        spacer = 10;

    return axis === 'vertical' ? mouseOnOppAxis > targetS2Opp + spacer || mouseOnOppAxis <= targetS2Opp && mouseOnAxis > targetS2 && mouseOnOppAxis >= targetS1Opp : mouseOnAxis > targetS2 && mouseOnOppAxis > targetS1Opp || mouseOnAxis <= targetS2 && mouseOnOppAxis > targetS2Opp + spacer;
  }

  function _getSwapDirection(evt, target, axis, swapThreshold, invertedSwapThreshold, invertSwap, isLastTarget) {
    var targetRect = _getRect(target),
        mouseOnAxis = axis === 'vertical' ? evt.clientY : evt.clientX,
        targetLength = axis === 'vertical' ? targetRect.height : targetRect.width,
        targetS1 = axis === 'vertical' ? targetRect.top : targetRect.left,
        targetS2 = axis === 'vertical' ? targetRect.bottom : targetRect.right,
        dragRect = _getRect(dragEl),
        invert = false;

    if (!invertSwap) {
      // Never invert or create dragEl shadow when target movemenet causes mouse to move past the end of regular swapThreshold
      if (isLastTarget && targetMoveDistance < targetLength * swapThreshold) {
        // multiplied only by swapThreshold because mouse will already be inside target by (1 - threshold) * targetLength / 2
        // check if past first invert threshold on side opposite of lastDirection
        if (!pastFirstInvertThresh && (lastDirection === 1 ? mouseOnAxis > targetS1 + targetLength * invertedSwapThreshold / 2 : mouseOnAxis < targetS2 - targetLength * invertedSwapThreshold / 2)) {
          // past first invert threshold, do not restrict inverted threshold to dragEl shadow
          pastFirstInvertThresh = true;
        }

        if (!pastFirstInvertThresh) {
          var dragS1 = axis === 'vertical' ? dragRect.top : dragRect.left,
              dragS2 = axis === 'vertical' ? dragRect.bottom : dragRect.right; // dragEl shadow (target move distance shadow)

          if (lastDirection === 1 ? mouseOnAxis < targetS1 + targetMoveDistance // over dragEl shadow
          : mouseOnAxis > targetS2 - targetMoveDistance) {
            return lastDirection * -1;
          }
        } else {
          invert = true;
        }
      } else {
        // Regular
        if (mouseOnAxis > targetS1 + targetLength * (1 - swapThreshold) / 2 && mouseOnAxis < targetS2 - targetLength * (1 - swapThreshold) / 2) {
          return _getInsertDirection(target);
        }
      }
    }

    invert = invert || invertSwap;

    if (invert) {
      // Invert of regular
      if (mouseOnAxis < targetS1 + targetLength * invertedSwapThreshold / 2 || mouseOnAxis > targetS2 - targetLength * invertedSwapThreshold / 2) {
        return mouseOnAxis > targetS1 + targetLength / 2 ? 1 : -1;
      }
    }

    return 0;
  }
  /**
   * Gets the direction dragEl must be swapped relative to target in order to make it
   * seem that dragEl has been "inserted" into that element's position
   * @param  {HTMLElement} target       The target whose position dragEl is being inserted at
   * @return {Number}                   Direction dragEl must be swapped
   */


  function _getInsertDirection(target) {
    var dragElIndex = _index(dragEl),
        targetIndex = _index(target);

    if (dragElIndex < targetIndex) {
      return 1;
    } else {
      return -1;
    }
  }
  /**
   * Generate id
   * @param   {HTMLElement} el
   * @returns {String}
   * @private
   */


  function _generateId(el) {
    var str = el.tagName + el.className + el.src + el.href + el.textContent,
        i = str.length,
        sum = 0;

    while (i--) {
      sum += str.charCodeAt(i);
    }

    return sum.toString(36);
  }
  /**
   * Returns the index of an element within its parent for a selected set of
   * elements
   * @param  {HTMLElement} el
   * @param  {selector} selector
   * @return {number}
   */


  function _index(el, selector) {
    var index = 0;

    if (!el || !el.parentNode) {
      return -1;
    }

    while (el && (el = el.previousElementSibling)) {
      if (el.nodeName.toUpperCase() !== 'TEMPLATE' && el !== cloneEl && (!selector || _matches(el, selector))) {
        index++;
      }
    }

    return index;
  }

  function _matches(
  /**HTMLElement*/
  el,
  /**String*/
  selector) {
    if (!selector) return;
    selector[0] === '>' && (selector = selector.substring(1));

    if (el) {
      try {
        if (el.matches) {
          return el.matches(selector);
        } else if (el.msMatchesSelector) {
          return el.msMatchesSelector(selector);
        } else if (el.webkitMatchesSelector) {
          return el.webkitMatchesSelector(selector);
        }
      } catch (_) {
        return false;
      }
    }

    return false;
  }

  var _throttleTimeout;

  function _throttle(callback, ms) {
    return function () {
      if (!_throttleTimeout) {
        var args = arguments,
            _this = this;

        _throttleTimeout = setTimeout(function () {
          if (args.length === 1) {
            callback.call(_this, args[0]);
          } else {
            callback.apply(_this, args);
          }

          _throttleTimeout = void 0;
        }, ms);
      }
    };
  }

  function _cancelThrottle() {
    clearTimeout(_throttleTimeout);
    _throttleTimeout = void 0;
  }

  function _extend(dst, src) {
    if (dst && src) {
      for (var key in src) {
        if (src.hasOwnProperty(key)) {
          dst[key] = src[key];
        }
      }
    }

    return dst;
  }

  function _clone(el) {
    if (Polymer && Polymer.dom) {
      return Polymer.dom(el).cloneNode(true);
    } else if ($) {
      return $(el).clone(true)[0];
    } else {
      return el.cloneNode(true);
    }
  }

  function _saveInputCheckedState(root) {
    savedInputChecked.length = 0;
    var inputs = root.getElementsByTagName('input');
    var idx = inputs.length;

    while (idx--) {
      var el = inputs[idx];
      el.checked && savedInputChecked.push(el);
    }
  }

  function _nextTick(fn) {
    return setTimeout(fn, 0);
  }

  function _cancelNextTick(id) {
    return clearTimeout(id);
  }
  /**
   * Returns the "bounding client rect" of given element
   * @param  {HTMLElement} el                The element whose boundingClientRect is wanted
   * @param  {[HTMLElement]} container       the parent the element will be placed in
   * @param  {[Boolean]} adjustForTransform  Whether the rect should compensate for parent's transform
   * @return {Object}                        The boundingClientRect of el
   */


  function _getRect(el, adjustForTransform, container, adjustForFixed) {
    if (!el.getBoundingClientRect && el !== win) return;
    var elRect, top, left, bottom, right, height, width;

    if (el !== win && el !== _getWindowScrollingElement()) {
      elRect = el.getBoundingClientRect();
      top = elRect.top;
      left = elRect.left;
      bottom = elRect.bottom;
      right = elRect.right;
      height = elRect.height;
      width = elRect.width;
    } else {
      top = 0;
      left = 0;
      bottom = window.innerHeight;
      right = window.innerWidth;
      height = window.innerHeight;
      width = window.innerWidth;
    }

    if (adjustForFixed && el !== win) {
      // Adjust for translate()
      container = container || el.parentNode; // solves #1123 (see: https://stackoverflow.com/a/37953806/6088312)
      // Not needed on <= IE11

      if (!IE11OrLess) {
        do {
          if (container && container.getBoundingClientRect && _css(container, 'transform') !== 'none') {
            var containerRect = container.getBoundingClientRect(); // Set relative to edges of padding box of container

            top -= containerRect.top + parseInt(_css(container, 'border-top-width'));
            left -= containerRect.left + parseInt(_css(container, 'border-left-width'));
            bottom = top + elRect.height;
            right = left + elRect.width;
            break;
          }
          /* jshint boss:true */

        } while (container = container.parentNode);
      }
    }

    if (adjustForTransform && el !== win) {
      // Adjust for scale()
      var matrix = _matrix(container || el),
          scaleX = matrix && matrix.a,
          scaleY = matrix && matrix.d;

      if (matrix) {
        top /= scaleY;
        left /= scaleX;
        width /= scaleX;
        height /= scaleY;
        bottom = top + height;
        right = left + width;
      }
    }

    return {
      top: top,
      left: left,
      bottom: bottom,
      right: right,
      width: width,
      height: height
    };
  }
  /**
   * Checks if a side of an element is scrolled past a side of it's parents
   * @param  {HTMLElement}  el       The element who's side being scrolled out of view is in question
   * @param  {String}       side     Side of the element in question ('top', 'left', 'right', 'bottom')
   * @return {HTMLElement}           The parent scroll element that the el's side is scrolled past, or null if there is no such element
   */


  function _isScrolledPast(el, side) {
    var parent = _getParentAutoScrollElement(el, true),
        elSide = _getRect(el)[side];
    /* jshint boss:true */


    while (parent) {
      var parentSide = _getRect(parent)[side],
          visible;

      if (side === 'top' || side === 'left') {
        visible = elSide >= parentSide;
      } else {
        visible = elSide <= parentSide;
      }

      if (!visible) return parent;
      if (parent === _getWindowScrollingElement()) break;
      parent = _getParentAutoScrollElement(parent, false);
    }

    return false;
  }
  /**
   * Returns the scroll offset of the given element, added with all the scroll offsets of parent elements.
   * The value is returned in real pixels.
   * @param  {HTMLElement} el
   * @return {Array}             Offsets in the format of [left, top]
   */


  function _getRelativeScrollOffset(el) {
    var offsetLeft = 0,
        offsetTop = 0,
        winScroller = _getWindowScrollingElement();

    if (el) {
      do {
        var matrix = _matrix(el),
            scaleX = matrix.a,
            scaleY = matrix.d;

        offsetLeft += el.scrollLeft * scaleX;
        offsetTop += el.scrollTop * scaleY;
      } while (el !== winScroller && (el = el.parentNode));
    }

    return [offsetLeft, offsetTop];
  } // Fixed #973:


  _on(document, 'touchmove', function (evt) {
    if ((Sortable.active || awaitingDragStarted) && evt.cancelable) {
      evt.preventDefault();
    }
  }); // Export utils


  Sortable.utils = {
    on: _on,
    off: _off,
    css: _css,
    find: _find,
    is: function is(el, selector) {
      return !!_closest(el, selector, el, false);
    },
    extend: _extend,
    throttle: _throttle,
    closest: _closest,
    toggleClass: _toggleClass,
    clone: _clone,
    index: _index,
    nextTick: _nextTick,
    cancelNextTick: _cancelNextTick,
    detectDirection: _detectDirection,
    getChild: _getChild
  };
  /**
   * Create sortable instance
   * @param {HTMLElement}  el
   * @param {Object}      [options]
   */

  Sortable.create = function (el, options) {
    return new Sortable(el, options);
  }; // Export


  Sortable.version = '1.9.0';
  return Sortable;
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
  var viewMode = 1;
  var sessionValue;
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
    var options = Joomla.getOptions('PWTImageConfig');
    sessionValue = options.sessionToken;
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
     * @param tokenValue
     * @param createNew
     */


  pwtImage.saveImage = function (id, tokenValue, createNew) {
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
          'X-CSRF-TOKEN': tokenValue
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

            window.parent.jQuery("#".concat(targetId, "_value")).val(resultFile).trigger('change');
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

        pwtImage.loadFolder("#".concat(id), subPath, 'select', tokenValue); // Reset the Edit page

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
     * @param tokenValue
     * @returns {boolean}
     */


  pwtImage.loadFolder = function (element, folder, target, tokenValue) {
    var id = getParentId(element);
    var data = new FormData();
    var postUrl = jQuery('#post-url').val(); // Add the form data

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
        'X-CSRF-TOKEN': tokenValue
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
          } // Construct the breadcrumb path


          jQuery(folderItems).each(function (index, folderItem) {
            structure += folderItem;

            if (structure.length > 0) {
              folderPath[index + 1] = "<a onclick=\"pwtImage.loadFolder('.pwt-gallery__items--folders', '/".concat(structure, "', '").concat(target, "', '").concat(tokenValue, "'); return false;\"><span class=\"icon-folder-2\"></span>").concat(folderItem, "</a>");
              structure += '/';
            }
          });
          jQuery("#".concat(target, " .js-breadcrumb")).html(folderPath.join(' ')); // Collect all folders to show

          jQuery(response.data.folders).each(function (index, item) {
            var itemPath = item;

            if (folder !== '/') {
              itemPath = "".concat(folder, "/").concat(item);
            }

            link.push("<div class=\"pwt-gallery__item\"><a onclick=\"pwtImage.loadFolder('.pwt-gallery__items--folders', '".concat(itemPath, "', '").concat(target, "', '").concat(tokenValue, "'); return false;\">") + '<div class="pwt-gallery__item__content">' + '<span class="pwt-gallery__item__icon icon-folder-2"></span>' + "<span class=\"pwt-gallery__item__title\">".concat(item, "</span>") + '</div>' + '</a></div>');
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
     * @param tokenValue
     * @returns {boolean}
     */


  pwtImage.loadSelectFolders = function (folder, tokenValue) {
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
        'X-CSRF-TOKEN': tokenValue
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
    var imageName = itemPath.substring(itemPath.lastIndexOf('/') + 1);

    switch (target) {
      case 'select':
        imageElement = "".concat('<div class="pwt-gallery__item">' + '<a onclick="return pwtImage.previewImage(\'.pwt-gallery__items--images\', \'').concat(itemPath, "');\" ") + "title=\"".concat(imageName, "\">") + '<div class="pwt-gallery__item__image">' + '<div class="pwt-gallery__item__center">' + "<img src=\"".concat(itemPath, "\" alt=\"").concat(baseName(item), "\" />") + '</div>' + "<div class=\"pwt-gallery__item__imagename\">".concat(imageName, "</div>") + '</div>' + '</a>' + '</div>';
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
          break;

        case 'custom':
          jQuery("#".concat(id, "_enterFolder")).addClass('is-visible').removeClass('is-hidden');
          jQuery("#".concat(id, "_subPath")).prop('disabled', false);
          jQuery("#".concat(id, "_selectFolder")).addClass('is-hidden').removeClass('is-visible');
          break;
      }

      jQuery("#".concat(id, "_selectFolder")).trigger('change');
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


    document.getElementById('useOriginal').parentElement.style.display = 'block'; // Show some basic info of the original file

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
        'X-CSRF-TOKEN': sessionValue
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
    var basePath = getFromLocalStorage(id, 'basePath');
    var filteredItems = storedFiles;
    var target = 'select';
    var start = page === 1 ? 0 : (page - 1) * imageLimit;
    var end = page * imageLimit;
    var pagination = []; // If we have a search filter, filter the images

    if (search) {
      filteredItems = filterItems(storedFiles, search);
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
  };
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
  } // Return the public parts


  return pwtImage;
}();

(function webpackUniversalModuleDefinition(root, factory) {
  //CommonJS2
  if ((typeof exports === "undefined" ? "undefined" : _typeof2(exports)) === 'object' && (typeof module === "undefined" ? "undefined" : _typeof2(module)) === 'object') module.exports = factory(); //AMD
  else if (typeof define === 'function' && define.amd) define([], factory); //CommonJS
    else if ((typeof exports === "undefined" ? "undefined" : _typeof2(exports)) === 'object') exports["Choices"] = factory(); //Window
      else root["Choices"] = factory();
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

      return __webpack_require__(__webpack_require__.s = 9);
      /******/
    }(
    /************************************************************************/

    /******/
    [
    /* 0 */

    /***/
    function (module, exports, __webpack_require__) {
      "use strict";

      Object.defineProperty(exports, "__esModule", {
        value: true
      });
      exports.diff = exports.cloneObject = exports.existsInArray = exports.isIE11 = exports.fetchFromObject = exports.getWindowHeight = exports.dispatchEvent = exports.sortByScore = exports.sortByAlpha = exports.calcWidthOfInput = exports.strToEl = exports.sanitise = exports.isScrolledIntoView = exports.getAdjacentEl = exports.findAncestorByAttrName = exports.wrap = exports.isElement = exports.isType = exports.getType = exports.generateId = exports.generateChars = exports.getRandomNumber = void 0;

      var _this = void 0;

      var getRandomNumber = function getRandomNumber(min, max) {
        return Math.floor(Math.random() * (max - min) + min);
      };

      exports.getRandomNumber = getRandomNumber;

      var generateChars = function generateChars(length) {
        var chars = '';

        for (var i = 0; i < length; i++) {
          var randomChar = getRandomNumber(0, 36);
          chars += randomChar.toString(36);
        }

        return chars;
      };

      exports.generateChars = generateChars;

      var generateId = function generateId(element, prefix) {
        var id = element.id || element.name && "".concat(element.name, "-").concat(generateChars(2)) || generateChars(4);
        id = id.replace(/(:|\.|\[|\]|,)/g, '');
        id = "".concat(prefix, "-").concat(id);
        return id;
      };

      exports.generateId = generateId;

      var getType = function getType(obj) {
        return Object.prototype.toString.call(obj).slice(8, -1);
      };

      exports.getType = getType;

      var isType = function isType(type, obj) {
        return obj !== undefined && obj !== null && getType(obj) === type;
      };

      exports.isType = isType;

      var isElement = function isElement(element) {
        return element instanceof Element;
      };

      exports.isElement = isElement;

      var wrap = function wrap(element) {
        var wrapper = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : document.createElement('div');

        if (element.nextSibling) {
          element.parentNode.insertBefore(wrapper, element.nextSibling);
        } else {
          element.parentNode.appendChild(wrapper);
        }

        return wrapper.appendChild(element);
      };

      exports.wrap = wrap;

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

      exports.findAncestorByAttrName = findAncestorByAttrName;

      var getAdjacentEl = function getAdjacentEl(startEl, className) {
        var direction = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 1;

        if (!startEl || !className) {
          return;
        }

        var parent = startEl.parentNode.parentNode;
        var children = Array.from(parent.querySelectorAll(className));
        var startPos = children.indexOf(startEl);
        var operatorDirection = direction > 0 ? 1 : -1;
        return children[startPos + operatorDirection];
      };

      exports.getAdjacentEl = getAdjacentEl;

      var isScrolledIntoView = function isScrolledIntoView(el, parent) {
        var direction = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 1;

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

      exports.isScrolledIntoView = isScrolledIntoView;

      var sanitise = function sanitise(value) {
        if (!isType('String', value)) {
          return value;
        }

        return value.replace(/&/g, '&amp;').replace(/>/g, '&rt;').replace(/</g, '&lt;').replace(/"/g, '&quot;');
      };

      exports.sanitise = sanitise;

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


      exports.strToEl = strToEl;

      var calcWidthOfInput = function calcWidthOfInput(input, callback) {
        var value = input.value || input.placeholder;
        var width = input.offsetWidth;

        if (value) {
          var testEl = strToEl("<span>".concat(sanitise(value), "</span>"));
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
              testEl.style.padding = inputStyle.padding;
            }
          }

          document.body.appendChild(testEl);
          requestAnimationFrame(function () {
            if (value && testEl.offsetWidth !== input.offsetWidth) {
              width = testEl.offsetWidth + 4;
            }

            document.body.removeChild(testEl);
            callback.call(_this, "".concat(width, "px"));
          });
        } else {
          callback.call(_this, "".concat(width, "px"));
        }
      };

      exports.calcWidthOfInput = calcWidthOfInput;

      var sortByAlpha = function sortByAlpha(a, b) {
        var labelA = "".concat(a.label || a.value).toLowerCase();
        var labelB = "".concat(b.label || b.value).toLowerCase();

        if (labelA < labelB) {
          return -1;
        }

        if (labelA > labelB) {
          return 1;
        }

        return 0;
      };

      exports.sortByAlpha = sortByAlpha;

      var sortByScore = function sortByScore(a, b) {
        return a.score - b.score;
      };

      exports.sortByScore = sortByScore;

      var dispatchEvent = function dispatchEvent(element, type) {
        var customArgs = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
        var event = new CustomEvent(type, {
          detail: customArgs,
          bubbles: true,
          cancelable: true
        });
        return element.dispatchEvent(event);
      };

      exports.dispatchEvent = dispatchEvent;

      var getWindowHeight = function getWindowHeight() {
        var body = document.body;
        var html = document.documentElement;
        return Math.max(body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight);
      };

      exports.getWindowHeight = getWindowHeight;

      var fetchFromObject = function fetchFromObject(object, path) {
        var index = path.indexOf('.');

        if (index > -1) {
          return fetchFromObject(object[path.substring(0, index)], path.substr(index + 1));
        }

        return object[path];
      };

      exports.fetchFromObject = fetchFromObject;

      var isIE11 = function isIE11() {
        return !!(navigator.userAgent.match(/Trident/) && navigator.userAgent.match(/rv[ :]11/));
      };

      exports.isIE11 = isIE11;

      var existsInArray = function existsInArray(array, value) {
        var key = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'value';
        return array.some(function (item) {
          if (isType('String', value)) {
            return item[key] === value.trim();
          }

          return item[key] === value;
        });
      };

      exports.existsInArray = existsInArray;

      var cloneObject = function cloneObject(obj) {
        return JSON.parse(JSON.stringify(obj));
      };

      exports.cloneObject = cloneObject;

      var diff = function diff(a, b) {
        var aKeys = Object.keys(a).sort();
        var bKeys = Object.keys(b).sort();
        return aKeys.filter(function (i) {
          return bKeys.indexOf(i) < 0;
        });
      };

      exports.diff = diff;
      /***/
    },
    /* 1 */

    /***/
    function (module, exports, __webpack_require__) {
      "use strict";

      Object.defineProperty(exports, "__esModule", {
        value: true
      });
      exports.SCROLLING_SPEED = exports.KEY_CODES = exports.ACTION_TYPES = exports.EVENTS = exports.DEFAULT_CONFIG = exports.DEFAULT_CLASSNAMES = void 0;

      var _utils = __webpack_require__(0);

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
      exports.DEFAULT_CLASSNAMES = DEFAULT_CLASSNAMES;
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
        sortFn: _utils.sortByAlpha,
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
          return "Press Enter to add <b>\"".concat((0, _utils.sanitise)(value), "\"</b>");
        },
        maxItemText: function maxItemText(maxItemCount) {
          return "Only ".concat(maxItemCount, " values can be added");
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
      exports.DEFAULT_CONFIG = DEFAULT_CONFIG;
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
      exports.EVENTS = EVENTS;
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
      exports.ACTION_TYPES = ACTION_TYPES;
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
      exports.KEY_CODES = KEY_CODES;
      var SCROLLING_SPEED = 4;
      exports.SCROLLING_SPEED = SCROLLING_SPEED;
      /***/
    },
    /* 2 */

    /***/
    function (module, __webpack_exports__, __webpack_require__) {
      "use strict";
      /* WEBPACK VAR INJECTION */

      (function (global, module) {
        /* harmony import */
        var _ponyfill_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(7);
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
      }).call(this, __webpack_require__(3), __webpack_require__(14)(module));
      /***/
    },
    /* 3 */

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
    /* 4 */

    /***/
    function (module, exports, __webpack_require__) {
      "use strict";

      Object.defineProperty(exports, "__esModule", {
        value: true
      });
      exports["default"] = void 0;

      var _utils = __webpack_require__(0);

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

      var WrappedElement =
      /*#__PURE__*/
      function () {
        function WrappedElement(_ref) {
          var element = _ref.element,
              classNames = _ref.classNames;

          _classCallCheck(this, WrappedElement);

          Object.assign(this, {
            element: element,
            classNames: classNames
          });

          if (!(0, _utils.isElement)(element)) {
            throw new TypeError('Invalid element passed');
          }

          this.isDisabled = false;
        }

        _createClass(WrappedElement, [{
          key: "conceal",
          value: function conceal() {
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
          }
        }, {
          key: "reveal",
          value: function reveal() {
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
          }
        }, {
          key: "enable",
          value: function enable() {
            this.element.removeAttribute('disabled');
            this.element.disabled = false;
            this.isDisabled = false;
          }
        }, {
          key: "disable",
          value: function disable() {
            this.element.setAttribute('disabled', '');
            this.element.disabled = true;
            this.isDisabled = true;
          }
        }, {
          key: "triggerEvent",
          value: function triggerEvent(eventType, data) {
            (0, _utils.dispatchEvent)(this.element, eventType, data);
          }
        }, {
          key: "value",
          get: function get() {
            return this.element.value;
          }
        }]);

        return WrappedElement;
      }();

      exports["default"] = WrappedElement;
      /***/
    },
    /* 5 */

    /***/
    function (module, exports, __webpack_require__) {
      "use strict";

      Object.defineProperty(exports, "__esModule", {
        value: true
      });
      exports["default"] = exports.TEMPLATES = void 0;

      var _classnames = _interopRequireDefault(__webpack_require__(27));

      var _utils = __webpack_require__(0);

      function _interopRequireDefault(obj) {
        return obj && obj.__esModule ? obj : {
          "default": obj
        };
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

      var TEMPLATES = {
        containerOuter: function containerOuter(globalClasses, direction, isSelectElement, isSelectOneElement, searchEnabled, passedElementType) {
          var tabIndex = isSelectOneElement ? 'tabindex="0"' : '';
          var role = isSelectElement ? 'role="listbox"' : '';
          var ariaAutoComplete = '';

          if (isSelectElement && searchEnabled) {
            role = 'role="combobox"';
            ariaAutoComplete = 'aria-autocomplete="list"';
          }

          return (0, _utils.strToEl)("\n      <div\n        class=\"".concat(globalClasses.containerOuter, "\"\n        data-type=\"").concat(passedElementType, "\"\n        ").concat(role, "\n        ").concat(tabIndex, "\n        ").concat(ariaAutoComplete, "\n        aria-haspopup=\"true\"\n        aria-expanded=\"false\"\n        dir=\"").concat(direction, "\"\n        >\n      </div>\n    "));
        },
        containerInner: function containerInner(globalClasses) {
          return (0, _utils.strToEl)("\n      <div class=\"".concat(globalClasses.containerInner, "\"></div>\n    "));
        },
        itemList: function itemList(globalClasses, isSelectOneElement) {
          var _classNames;

          var localClasses = (0, _classnames["default"])(globalClasses.list, (_classNames = {}, _defineProperty(_classNames, globalClasses.listSingle, isSelectOneElement), _defineProperty(_classNames, globalClasses.listItems, !isSelectOneElement), _classNames));
          return (0, _utils.strToEl)("\n      <div class=\"".concat(localClasses, "\"></div>\n    "));
        },
        placeholder: function placeholder(globalClasses, value) {
          return (0, _utils.strToEl)("\n      <div class=\"".concat(globalClasses.placeholder, "\">\n        ").concat(value, "\n      </div>\n    "));
        },
        item: function item(globalClasses, data, removeItemButton) {
          var _classNames2;

          var ariaSelected = data.active ? 'aria-selected="true"' : '';
          var ariaDisabled = data.disabled ? 'aria-disabled="true"' : '';
          var localClasses = (0, _classnames["default"])(globalClasses.item, (_classNames2 = {}, _defineProperty(_classNames2, globalClasses.highlightedState, data.highlighted), _defineProperty(_classNames2, globalClasses.itemSelectable, !data.highlighted), _defineProperty(_classNames2, globalClasses.placeholder, data.placeholder), _classNames2));

          if (removeItemButton) {
            var _classNames3;

            localClasses = (0, _classnames["default"])(globalClasses.item, (_classNames3 = {}, _defineProperty(_classNames3, globalClasses.highlightedState, data.highlighted), _defineProperty(_classNames3, globalClasses.itemSelectable, !data.disabled), _defineProperty(_classNames3, globalClasses.placeholder, data.placeholder), _classNames3));
            return (0, _utils.strToEl)("\n        <div\n          class=\"".concat(localClasses, "\"\n          data-item\n          data-id=\"").concat(data.id, "\"\n          data-value=\"").concat(data.value, "\"\n          data-custom-properties='").concat(data.customProperties, "'\n          data-deletable\n          ").concat(ariaSelected, "\n          ").concat(ariaDisabled, "\n          >\n          ").concat(data.label, "<!--\n       --><button\n            type=\"button\"\n            class=\"").concat(globalClasses.button, "\"\n            data-button\n            aria-label=\"Remove item: '").concat(data.value, "'\"\n            >\n            Remove item\n          </button>\n        </div>\n      "));
          }

          return (0, _utils.strToEl)("\n      <div\n        class=\"".concat(localClasses, "\"\n        data-item\n        data-id=\"").concat(data.id, "\"\n        data-value=\"").concat(data.value, "\"\n        ").concat(ariaSelected, "\n        ").concat(ariaDisabled, "\n        >\n        ").concat(data.label, "\n      </div>\n    "));
        },
        choiceList: function choiceList(globalClasses, isSelectOneElement) {
          var ariaMultiSelectable = !isSelectOneElement ? 'aria-multiselectable="true"' : '';
          return (0, _utils.strToEl)("\n      <div\n        class=\"".concat(globalClasses.list, "\"\n        dir=\"ltr\"\n        role=\"listbox\"\n        ").concat(ariaMultiSelectable, "\n        >\n      </div>\n    "));
        },
        choiceGroup: function choiceGroup(globalClasses, data) {
          var ariaDisabled = data.disabled ? 'aria-disabled="true"' : '';
          var localClasses = (0, _classnames["default"])(globalClasses.group, _defineProperty({}, globalClasses.itemDisabled, data.disabled));
          return (0, _utils.strToEl)("\n      <div\n        class=\"".concat(localClasses, "\"\n        data-group\n        data-id=\"").concat(data.id, "\"\n        data-value=\"").concat(data.value, "\"\n        role=\"group\"\n        ").concat(ariaDisabled, "\n        >\n        <div class=\"").concat(globalClasses.groupHeading, "\">").concat(data.value, "</div>\n      </div>\n    "));
        },
        choice: function choice(globalClasses, data, itemSelectText) {
          var _classNames5;

          var role = data.groupId > 0 ? 'role="treeitem"' : 'role="option"';
          var localClasses = (0, _classnames["default"])(globalClasses.item, globalClasses.itemChoice, (_classNames5 = {}, _defineProperty(_classNames5, globalClasses.itemDisabled, data.disabled), _defineProperty(_classNames5, globalClasses.itemSelectable, !data.disabled), _defineProperty(_classNames5, globalClasses.placeholder, data.placeholder), _classNames5));
          return (0, _utils.strToEl)("\n      <div\n        class=\"".concat(localClasses, "\"\n        data-select-text=\"").concat(itemSelectText, "\"\n        data-choice\n        data-id=\"").concat(data.id, "\"\n        data-value=\"").concat(data.value, "\"\n        ").concat(data.disabled ? 'data-choice-disabled aria-disabled="true"' : 'data-choice-selectable', "\n        id=\"").concat(data.elementId, "\"\n        ").concat(role, "\n        >\n        ").concat(data.label, "\n      </div>\n    "));
        },
        input: function input(globalClasses) {
          var localClasses = (0, _classnames["default"])(globalClasses.input, globalClasses.inputCloned);
          return (0, _utils.strToEl)("\n      <input\n        type=\"text\"\n        class=\"".concat(localClasses, "\"\n        autocomplete=\"off\"\n        autocapitalize=\"off\"\n        spellcheck=\"false\"\n        role=\"textbox\"\n        aria-autocomplete=\"list\"\n        >\n    "));
        },
        dropdown: function dropdown(globalClasses) {
          var localClasses = (0, _classnames["default"])(globalClasses.list, globalClasses.listDropdown);
          return (0, _utils.strToEl)("\n      <div\n        class=\"".concat(localClasses, "\"\n        aria-expanded=\"false\"\n        >\n      </div>\n    "));
        },
        notice: function notice(globalClasses, label) {
          var _classNames6;

          var type = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : '';
          var localClasses = (0, _classnames["default"])(globalClasses.item, globalClasses.itemChoice, (_classNames6 = {}, _defineProperty(_classNames6, globalClasses.noResults, type === 'no-results'), _defineProperty(_classNames6, globalClasses.noChoices, type === 'no-choices'), _classNames6));
          return (0, _utils.strToEl)("\n      <div class=\"".concat(localClasses, "\">\n        ").concat(label, "\n      </div>\n    "));
        },
        option: function option(data) {
          return (0, _utils.strToEl)("\n      <option value=\"".concat(data.value, "\" ").concat(data.active ? 'selected' : '', " ").concat(data.disabled ? 'disabled' : '', " ").concat(data.customProperties ? "data-custom-properties=".concat(data.customProperties) : '', ">").concat(data.label, "</option>\n    "));
        }
      };
      exports.TEMPLATES = TEMPLATES;
      var _default = TEMPLATES;
      exports["default"] = _default;
      /***/
    },
    /* 6 */

    /***/
    function (module, __webpack_exports__, __webpack_require__) {
      "use strict";

      __webpack_require__.r(__webpack_exports__); // EXTERNAL MODULE: ./node_modules/lodash-es/_freeGlobal.js


      var _freeGlobal = __webpack_require__(8); // CONCATENATED MODULE: ./node_modules/lodash-es/_root.js

      /** Detect free variable `self`. */


      var freeSelf = (typeof self === "undefined" ? "undefined" : _typeof2(self)) == 'object' && self && self.Object === Object && self;
      /** Used as a reference to the global object. */

      var root = _freeGlobal["a"
      /* default */
      ] || freeSelf || Function('return this')();
      /* harmony default export */

      var _root = root; // CONCATENATED MODULE: ./node_modules/lodash-es/_Symbol.js

      /** Built-in value references. */

      var _Symbol2 = _root.Symbol;
      /* harmony default export */

      var _Symbol = _Symbol2; // CONCATENATED MODULE: ./node_modules/lodash-es/_getRawTag.js

      /** Used for built-in method references. */

      var objectProto = Object.prototype;
      /** Used to check objects for own properties. */

      var _getRawTag_hasOwnProperty = objectProto.hasOwnProperty;
      /**
       * Used to resolve the
       * [`toStringTag`](http://ecma-international.org/ecma-262/7.0/#sec-object.prototype.tostring)
       * of values.
       */

      var nativeObjectToString = objectProto.toString;
      /** Built-in value references. */

      var symToStringTag = _Symbol ? _Symbol.toStringTag : undefined;
      /**
       * A specialized version of `baseGetTag` which ignores `Symbol.toStringTag` values.
       *
       * @private
       * @param {*} value The value to query.
       * @returns {string} Returns the raw `toStringTag`.
       */

      function getRawTag(value) {
        var isOwn = _getRawTag_hasOwnProperty.call(value, symToStringTag),
            tag = value[symToStringTag];

        try {
          value[symToStringTag] = undefined;
          var unmasked = true;
        } catch (e) {}

        var result = nativeObjectToString.call(value);

        if (unmasked) {
          if (isOwn) {
            value[symToStringTag] = tag;
          } else {
            delete value[symToStringTag];
          }
        }

        return result;
      }
      /* harmony default export */


      var _getRawTag = getRawTag; // CONCATENATED MODULE: ./node_modules/lodash-es/_objectToString.js

      /** Used for built-in method references. */

      var _objectToString_objectProto = Object.prototype;
      /**
       * Used to resolve the
       * [`toStringTag`](http://ecma-international.org/ecma-262/7.0/#sec-object.prototype.tostring)
       * of values.
       */

      var _objectToString_nativeObjectToString = _objectToString_objectProto.toString;
      /**
       * Converts `value` to a string using `Object.prototype.toString`.
       *
       * @private
       * @param {*} value The value to convert.
       * @returns {string} Returns the converted string.
       */

      function objectToString(value) {
        return _objectToString_nativeObjectToString.call(value);
      }
      /* harmony default export */


      var _objectToString = objectToString; // CONCATENATED MODULE: ./node_modules/lodash-es/_baseGetTag.js

      /** `Object#toString` result references. */

      var nullTag = '[object Null]',
          undefinedTag = '[object Undefined]';
      /** Built-in value references. */

      var _baseGetTag_symToStringTag = _Symbol ? _Symbol.toStringTag : undefined;
      /**
       * The base implementation of `getTag` without fallbacks for buggy environments.
       *
       * @private
       * @param {*} value The value to query.
       * @returns {string} Returns the `toStringTag`.
       */


      function baseGetTag(value) {
        if (value == null) {
          return value === undefined ? undefinedTag : nullTag;
        }

        return _baseGetTag_symToStringTag && _baseGetTag_symToStringTag in Object(value) ? _getRawTag(value) : _objectToString(value);
      }
      /* harmony default export */


      var _baseGetTag = baseGetTag; // CONCATENATED MODULE: ./node_modules/lodash-es/_overArg.js

      /**
       * Creates a unary function that invokes `func` with its argument transformed.
       *
       * @private
       * @param {Function} func The function to wrap.
       * @param {Function} transform The argument transform.
       * @returns {Function} Returns the new function.
       */

      function overArg(func, transform) {
        return function (arg) {
          return func(transform(arg));
        };
      }
      /* harmony default export */


      var _overArg = overArg; // CONCATENATED MODULE: ./node_modules/lodash-es/_getPrototype.js

      /** Built-in value references. */

      var getPrototype = _overArg(Object.getPrototypeOf, Object);
      /* harmony default export */


      var _getPrototype = getPrototype; // CONCATENATED MODULE: ./node_modules/lodash-es/isObjectLike.js

      /**
       * Checks if `value` is object-like. A value is object-like if it's not `null`
       * and has a `typeof` result of "object".
       *
       * @static
       * @memberOf _
       * @since 4.0.0
       * @category Lang
       * @param {*} value The value to check.
       * @returns {boolean} Returns `true` if `value` is object-like, else `false`.
       * @example
       *
       * _.isObjectLike({});
       * // => true
       *
       * _.isObjectLike([1, 2, 3]);
       * // => true
       *
       * _.isObjectLike(_.noop);
       * // => false
       *
       * _.isObjectLike(null);
       * // => false
       */

      function isObjectLike(value) {
        return value != null && _typeof2(value) == 'object';
      }
      /* harmony default export */


      var lodash_es_isObjectLike = isObjectLike; // CONCATENATED MODULE: ./node_modules/lodash-es/isPlainObject.js

      /** `Object#toString` result references. */

      var objectTag = '[object Object]';
      /** Used for built-in method references. */

      var funcProto = Function.prototype,
          isPlainObject_objectProto = Object.prototype;
      /** Used to resolve the decompiled source of functions. */

      var funcToString = funcProto.toString;
      /** Used to check objects for own properties. */

      var isPlainObject_hasOwnProperty = isPlainObject_objectProto.hasOwnProperty;
      /** Used to infer the `Object` constructor. */

      var objectCtorString = funcToString.call(Object);
      /**
       * Checks if `value` is a plain object, that is, an object created by the
       * `Object` constructor or one with a `[[Prototype]]` of `null`.
       *
       * @static
       * @memberOf _
       * @since 0.8.0
       * @category Lang
       * @param {*} value The value to check.
       * @returns {boolean} Returns `true` if `value` is a plain object, else `false`.
       * @example
       *
       * function Foo() {
       *   this.a = 1;
       * }
       *
       * _.isPlainObject(new Foo);
       * // => false
       *
       * _.isPlainObject([1, 2, 3]);
       * // => false
       *
       * _.isPlainObject({ 'x': 0, 'y': 0 });
       * // => true
       *
       * _.isPlainObject(Object.create(null));
       * // => true
       */

      function isPlainObject(value) {
        if (!lodash_es_isObjectLike(value) || _baseGetTag(value) != objectTag) {
          return false;
        }

        var proto = _getPrototype(value);

        if (proto === null) {
          return true;
        }

        var Ctor = isPlainObject_hasOwnProperty.call(proto, 'constructor') && proto.constructor;
        return typeof Ctor == 'function' && Ctor instanceof Ctor && funcToString.call(Ctor) == objectCtorString;
      }
      /* harmony default export */


      var lodash_es_isPlainObject = isPlainObject; // EXTERNAL MODULE: ./node_modules/symbol-observable/es/index.js

      var es = __webpack_require__(2); // CONCATENATED MODULE: ./node_modules/redux/es/createStore.js

      /**
       * These are private action types reserved by Redux.
       * For any unknown actions, you must return the current state.
       * If the current state is undefined, you must return the initial state.
       * Do not reference these action types directly in your code.
       */


      var ActionTypes = {
        INIT: '@@redux/INIT'
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

      };

      function createStore_createStore(reducer, preloadedState, enhancer) {
        var _ref2;

        if (typeof preloadedState === 'function' && typeof enhancer === 'undefined') {
          enhancer = preloadedState;
          preloadedState = undefined;
        }

        if (typeof enhancer !== 'undefined') {
          if (typeof enhancer !== 'function') {
            throw new Error('Expected the enhancer to be a function.');
          }

          return enhancer(createStore_createStore)(reducer, preloadedState);
        }

        if (typeof reducer !== 'function') {
          throw new Error('Expected the reducer to be a function.');
        }

        var currentReducer = reducer;
        var currentState = preloadedState;
        var currentListeners = [];
        var nextListeners = currentListeners;
        var isDispatching = false;

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
            throw new Error('Expected listener to be a function.');
          }

          var isSubscribed = true;
          ensureCanMutateNextListeners();
          nextListeners.push(listener);
          return function unsubscribe() {
            if (!isSubscribed) {
              return;
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
          if (!lodash_es_isPlainObject(action)) {
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

          currentReducer = nextReducer;
          dispatch({
            type: ActionTypes.INIT
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
              if (_typeof2(observer) !== 'object') {
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
      } // CONCATENATED MODULE: ./node_modules/redux/es/utils/warning.js

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
          /* eslint-disable no-empty */
        } catch (e) {}
        /* eslint-enable no-empty */

      } // CONCATENATED MODULE: ./node_modules/redux/es/combineReducers.js


      function getUndefinedStateErrorMessage(key, action) {
        var actionType = action && action.type;
        var actionName = actionType && '"' + actionType.toString() + '"' || 'an action';
        return 'Given action ' + actionName + ', reducer "' + key + '" returned undefined. ' + 'To ignore an action, you must explicitly return the previous state. ' + 'If you want this reducer to hold no value, you can return null instead of undefined.';
      }

      function getUnexpectedStateShapeWarningMessage(inputState, reducers, action, unexpectedKeyCache) {
        var reducerKeys = Object.keys(reducers);
        var argumentName = action && action.type === ActionTypes.INIT ? 'preloadedState argument passed to createStore' : 'previous state received by the reducer';

        if (reducerKeys.length === 0) {
          return 'Store does not have a valid reducer. Make sure the argument passed ' + 'to combineReducers is an object whose values are reducers.';
        }

        if (!lodash_es_isPlainObject(inputState)) {
          return 'The ' + argumentName + ' has unexpected type of "' + {}.toString.call(inputState).match(/\s([a-z|A-Z]+)/)[1] + '". Expected argument to be an object with the following ' + ('keys: "' + reducerKeys.join('", "') + '"');
        }

        var unexpectedKeys = Object.keys(inputState).filter(function (key) {
          return !reducers.hasOwnProperty(key) && !unexpectedKeyCache[key];
        });
        unexpectedKeys.forEach(function (key) {
          unexpectedKeyCache[key] = true;
        });

        if (unexpectedKeys.length > 0) {
          return 'Unexpected ' + (unexpectedKeys.length > 1 ? 'keys' : 'key') + ' ' + ('"' + unexpectedKeys.join('", "') + '" found in ' + argumentName + '. ') + 'Expected to find one of the known reducer keys instead: ' + ('"' + reducerKeys.join('", "') + '". Unexpected keys will be ignored.');
        }
      }

      function assertReducerShape(reducers) {
        Object.keys(reducers).forEach(function (key) {
          var reducer = reducers[key];
          var initialState = reducer(undefined, {
            type: ActionTypes.INIT
          });

          if (typeof initialState === 'undefined') {
            throw new Error('Reducer "' + key + '" returned undefined during initialization. ' + 'If the state passed to the reducer is undefined, you must ' + 'explicitly return the initial state. The initial state may ' + 'not be undefined. If you don\'t want to set a value for this reducer, ' + 'you can use null instead of undefined.');
          }

          var type = '@@redux/PROBE_UNKNOWN_ACTION_' + Math.random().toString(36).substring(7).split('').join('.');

          if (typeof reducer(undefined, {
            type: type
          }) === 'undefined') {
            throw new Error('Reducer "' + key + '" returned undefined when probed with a random type. ' + ('Don\'t try to handle ' + ActionTypes.INIT + ' or other actions in "redux/*" ') + 'namespace. They are considered private. Instead, you must return the ' + 'current state for any unknown actions, unless it is undefined, ' + 'in which case you must return the initial state, regardless of the ' + 'action type. The initial state may not be undefined, but can be null.');
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

        var finalReducerKeys = Object.keys(finalReducers);
        var unexpectedKeyCache = void 0;

        if (false) {}

        var shapeAssertionError = void 0;

        try {
          assertReducerShape(finalReducers);
        } catch (e) {
          shapeAssertionError = e;
        }

        return function combination() {
          var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
          var action = arguments[1];

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
      } // CONCATENATED MODULE: ./node_modules/redux/es/bindActionCreators.js


      function bindActionCreator(actionCreator, dispatch) {
        return function () {
          return dispatch(actionCreator.apply(undefined, arguments));
        };
      }
      /**
       * Turns an object whose values are action creators, into an object with the
       * same keys, but with every function wrapped into a `dispatch` call so they
       * may be invoked directly. This is just a convenience method, as you can call
       * `store.dispatch(MyActionCreators.doSomething())` yourself just fine.
       *
       * For convenience, you can also pass a single function as the first argument,
       * and get a function in return.
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
          throw new Error('bindActionCreators expected an object or a function, instead received ' + (actionCreators === null ? 'null' : _typeof2(actionCreators)) + '. ' + 'Did you write "import ActionCreators from" instead of "import * as ActionCreators from"?');
        }

        var keys = Object.keys(actionCreators);
        var boundActionCreators = {};

        for (var i = 0; i < keys.length; i++) {
          var key = keys[i];
          var actionCreator = actionCreators[key];

          if (typeof actionCreator === 'function') {
            boundActionCreators[key] = bindActionCreator(actionCreator, dispatch);
          }
        }

        return boundActionCreators;
      } // CONCATENATED MODULE: ./node_modules/redux/es/compose.js

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
        for (var _len = arguments.length, funcs = Array(_len), _key = 0; _key < _len; _key++) {
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
            return a(b.apply(undefined, arguments));
          };
        });
      } // CONCATENATED MODULE: ./node_modules/redux/es/applyMiddleware.js


      var _extends = Object.assign || function (target) {
        for (var i = 1; i < arguments.length; i++) {
          var source = arguments[i];

          for (var key in source) {
            if (Object.prototype.hasOwnProperty.call(source, key)) {
              target[key] = source[key];
            }
          }
        }

        return target;
      };
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
        for (var _len = arguments.length, middlewares = Array(_len), _key = 0; _key < _len; _key++) {
          middlewares[_key] = arguments[_key];
        }

        return function (createStore) {
          return function (reducer, preloadedState, enhancer) {
            var store = createStore(reducer, preloadedState, enhancer);
            var _dispatch = store.dispatch;
            var chain = [];
            var middlewareAPI = {
              getState: store.getState,
              dispatch: function dispatch(action) {
                return _dispatch(action);
              }
            };
            chain = middlewares.map(function (middleware) {
              return middleware(middlewareAPI);
            });
            _dispatch = compose.apply(undefined, chain)(store.dispatch);
            return _extends({}, store, {
              dispatch: _dispatch
            });
          };
        };
      } // CONCATENATED MODULE: ./node_modules/redux/es/index.js

      /* concated harmony reexport createStore */


      __webpack_require__.d(__webpack_exports__, "createStore", function () {
        return createStore_createStore;
      });
      /* concated harmony reexport combineReducers */


      __webpack_require__.d(__webpack_exports__, "combineReducers", function () {
        return combineReducers;
      });
      /* concated harmony reexport bindActionCreators */


      __webpack_require__.d(__webpack_exports__, "bindActionCreators", function () {
        return bindActionCreators;
      });
      /* concated harmony reexport applyMiddleware */


      __webpack_require__.d(__webpack_exports__, "applyMiddleware", function () {
        return applyMiddleware;
      });
      /* concated harmony reexport compose */


      __webpack_require__.d(__webpack_exports__, "compose", function () {
        return compose;
      });
      /*
      * This is a dummy function to check if the function name has been altered by minification.
      * If the function has been minified and NODE_ENV !== 'production', warn the user.
      */


      function isCrushed() {}

      if (false) {}
      /***/

    },
    /* 7 */

    /***/
    function (module, __webpack_exports__, __webpack_require__) {
      "use strict";
      /* harmony export (binding) */

      __webpack_require__.d(__webpack_exports__, "a", function () {
        return symbolObservablePonyfill;
      });

      function symbolObservablePonyfill(root) {
        var result;
        var _Symbol3 = root.Symbol;

        if (typeof _Symbol3 === 'function') {
          if (_Symbol3.observable) {
            result = _Symbol3.observable;
          } else {
            result = _Symbol3('observable');
            _Symbol3.observable = result;
          }
        } else {
          result = '@@observable';
        }

        return result;
      }

      ;
      /***/
    },
    /* 8 */

    /***/
    function (module, __webpack_exports__, __webpack_require__) {
      "use strict";
      /* WEBPACK VAR INJECTION */

      (function (global) {
        /** Detect free variable `global` from Node.js. */
        var freeGlobal = _typeof2(global) == 'object' && global && global.Object === Object && global;
        /* harmony default export */

        __webpack_exports__["a"] = freeGlobal;
        /* WEBPACK VAR INJECTION */
      }).call(this, __webpack_require__(3));
      /***/
    },
    /* 9 */

    /***/
    function (module, exports, __webpack_require__) {
      module.exports = __webpack_require__(10);
      /***/
    },
    /* 10 */

    /***/
    function (module, exports, __webpack_require__) {
      "use strict";

      var _fuse = _interopRequireDefault(__webpack_require__(11));

      var _deepmerge = _interopRequireDefault(__webpack_require__(12));

      var _store = _interopRequireDefault(__webpack_require__(13));

      var _components = __webpack_require__(20);

      var _constants = __webpack_require__(1);

      var _templates = __webpack_require__(5);

      var _choices = __webpack_require__(28);

      var _items = __webpack_require__(29);

      var _groups = __webpack_require__(30);

      var _misc = __webpack_require__(31);

      var _general = __webpack_require__(32);

      var _utils = __webpack_require__(0);

      function _interopRequireDefault(obj) {
        return obj && obj.__esModule ? obj : {
          "default": obj
        };
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
      /**
       * Choices
       * @author Josh Johnson<josh@joshuajohnson.co.uk>
       */


      var Choices =
      /*#__PURE__*/
      function () {
        function Choices() {
          var element = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '[data-choice]';
          var userConfig = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};

          _classCallCheck(this, Choices);

          if ((0, _utils.isType)('String', element)) {
            var elements = Array.from(document.querySelectorAll(element)); // If there are multiple elements, create a new instance
            // for each element besides the first one (as that already has an instance)

            if (elements.length > 1) {
              return this._generateInstances(elements, userConfig);
            }
          }

          this.config = _deepmerge["default"].all([_constants.DEFAULT_CONFIG, Choices.userDefaults, userConfig], // When merging array configs, replace with a copy of the userConfig array,
          // instead of concatenating with the default array
          {
            arrayMerge: function arrayMerge(destinationArray, sourceArray) {
              return [].concat(sourceArray);
            }
          });
          var invalidConfigOptions = (0, _utils.diff)(this.config, _constants.DEFAULT_CONFIG);

          if (invalidConfigOptions.length) {
            console.warn('Unknown config option(s) passed', invalidConfigOptions.join(', '));
          }

          if (!['auto', 'always'].includes(this.config.renderSelectedChoices)) {
            this.config.renderSelectedChoices = 'auto';
          } // Retrieve triggering element (i.e. element with 'data-choice' trigger)


          var passedElement = (0, _utils.isType)('String', element) ? document.querySelector(element) : element;

          if (!passedElement) {
            return console.error('Could not find passed element or passed element was of an invalid type');
          }

          this._isTextElement = passedElement.type === 'text';
          this._isSelectOneElement = passedElement.type === 'select-one';
          this._isSelectMultipleElement = passedElement.type === 'select-multiple';
          this._isSelectElement = this._isSelectOneElement || this._isSelectMultipleElement;

          if (this._isTextElement) {
            this.passedElement = new _components.WrappedInput({
              element: passedElement,
              classNames: this.config.classNames,
              delimiter: this.config.delimiter
            });
          } else if (this._isSelectElement) {
            this.passedElement = new _components.WrappedSelect({
              element: passedElement,
              classNames: this.config.classNames
            });
          }

          if (!this.passedElement) {
            return console.error('Passed element was of an invalid type');
          }

          if (this.config.shouldSortItems === true && this._isSelectOneElement && !this.config.silent) {
            console.warn("shouldSortElements: Type of passed element is 'select-one', falling back to false.");
          }

          this.initialised = false;
          this._store = new _store["default"](this.render);
          this._initialState = {};
          this._currentState = {};
          this._prevState = {};
          this._currentValue = '';
          this._canSearch = this.config.searchEnabled;
          this._isScrollingOnIe = false;
          this._highlightPosition = 0;
          this._wasTap = true;
          this._placeholderValue = this._generatePlaceholderValue();
          this._baseId = (0, _utils.generateId)(this.passedElement.element, 'choices-');
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
          this._onDeleteKey = this._onDeleteKey.bind(this); // If element has already been initialised with Choices, fail silently

          if (this.passedElement.element.getAttribute('data-choice') === 'active') {
            console.warn('Trying to initialise Choices on element already initialised');
          } // Let's go


          this.init();
        }
        /* ========================================
        =            Public functions            =
        ======================================== */


        _createClass(Choices, [{
          key: "init",
          value: function init() {
            if (this.initialised) {
              return;
            }

            this._createTemplates();

            this._createElements();

            this._createStructure(); // Set initial state (We need to clone the state because some reducers
            // modify the inner objects properties in the state) 🤢


            this._initialState = (0, _utils.cloneObject)(this._store.state);

            this._store.subscribe(this._render);

            this._render();

            this._addEventListeners();

            var shouldDisable = !this.config.addItems || this.passedElement.element.hasAttribute('disabled');

            if (shouldDisable) {
              this.disable();
            }

            this.initialised = true;
            var callbackOnInit = this.config.callbackOnInit; // Run callback if it is a function

            if (callbackOnInit && (0, _utils.isType)('Function', callbackOnInit)) {
              callbackOnInit.call(this);
            }
          }
        }, {
          key: "destroy",
          value: function destroy() {
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
          }
        }, {
          key: "enable",
          value: function enable() {
            if (this.passedElement.isDisabled) {
              this.passedElement.enable();
            }

            if (this.containerOuter.isDisabled) {
              this._addEventListeners();

              this.input.enable();
              this.containerOuter.enable();
            }

            return this;
          }
        }, {
          key: "disable",
          value: function disable() {
            if (!this.passedElement.isDisabled) {
              this.passedElement.disable();
            }

            if (!this.containerOuter.isDisabled) {
              this._removeEventListeners();

              this.input.disable();
              this.containerOuter.disable();
            }

            return this;
          }
        }, {
          key: "highlightItem",
          value: function highlightItem(item) {
            var runEvent = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;

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

            this._store.dispatch((0, _items.highlightItem)(id, true));

            if (runEvent) {
              this.passedElement.triggerEvent(_constants.EVENTS.highlightItem, {
                id: id,
                value: value,
                label: label,
                groupValue: group && group.value ? group.value : null
              });
            }

            return this;
          }
        }, {
          key: "unhighlightItem",
          value: function unhighlightItem(item) {
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

            this._store.dispatch((0, _items.highlightItem)(id, false));

            this.passedElement.triggerEvent(_constants.EVENTS.highlightItem, {
              id: id,
              value: value,
              label: label,
              groupValue: group && group.value ? group.value : null
            });
            return this;
          }
        }, {
          key: "highlightAll",
          value: function highlightAll() {
            var _this = this;

            this._store.items.forEach(function (item) {
              return _this.highlightItem(item);
            });

            return this;
          }
        }, {
          key: "unhighlightAll",
          value: function unhighlightAll() {
            var _this2 = this;

            this._store.items.forEach(function (item) {
              return _this2.unhighlightItem(item);
            });

            return this;
          }
        }, {
          key: "removeActiveItemsByValue",
          value: function removeActiveItemsByValue(value) {
            var _this3 = this;

            this._store.activeItems.filter(function (item) {
              return item.value === value;
            }).forEach(function (item) {
              return _this3._removeItem(item);
            });

            return this;
          }
        }, {
          key: "removeActiveItems",
          value: function removeActiveItems(excludedId) {
            var _this4 = this;

            this._store.activeItems.filter(function (_ref) {
              var id = _ref.id;
              return id !== excludedId;
            }).forEach(function (item) {
              return _this4._removeItem(item);
            });

            return this;
          }
        }, {
          key: "removeHighlightedItems",
          value: function removeHighlightedItems() {
            var _this5 = this;

            var runEvent = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;

            this._store.highlightedActiveItems.forEach(function (item) {
              _this5._removeItem(item); // If this action was performed by the user
              // trigger the event


              if (runEvent) {
                _this5._triggerChange(item.value);
              }
            });

            return this;
          }
        }, {
          key: "showDropdown",
          value: function showDropdown(preventInputFocus) {
            var _this6 = this;

            if (this.dropdown.isActive) {
              return this;
            }

            requestAnimationFrame(function () {
              _this6.dropdown.show();

              _this6.containerOuter.open(_this6.dropdown.distanceFromTopWindow());

              if (!preventInputFocus && _this6._canSearch) {
                _this6.input.focus();
              }

              _this6.passedElement.triggerEvent(_constants.EVENTS.showDropdown, {});
            });
            return this;
          }
        }, {
          key: "hideDropdown",
          value: function hideDropdown(preventInputBlur) {
            var _this7 = this;

            if (!this.dropdown.isActive) {
              return this;
            }

            requestAnimationFrame(function () {
              _this7.dropdown.hide();

              _this7.containerOuter.close();

              if (!preventInputBlur && _this7._canSearch) {
                _this7.input.removeActiveDescendant();

                _this7.input.blur();
              }

              _this7.passedElement.triggerEvent(_constants.EVENTS.hideDropdown, {});
            });
            return this;
          }
        }, {
          key: "getValue",
          value: function getValue() {
            var valueOnly = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;

            var values = this._store.activeItems.reduce(function (selectedItems, item) {
              var itemValue = valueOnly ? item.value : item;
              selectedItems.push(itemValue);
              return selectedItems;
            }, []);

            return this._isSelectOneElement ? values[0] : values;
          }
        }, {
          key: "setValue",
          value: function setValue(args) {
            var _this8 = this;

            if (!this.initialised) {
              return this;
            }

            [].concat(args).forEach(function (value) {
              return _this8._setChoiceOrItem(value);
            });
            return this;
          }
        }, {
          key: "setChoiceByValue",
          value: function setChoiceByValue(value) {
            var _this9 = this;

            if (!this.initialised || this._isTextElement) {
              return this;
            } // If only one value has been passed, convert to array


            var choiceValue = (0, _utils.isType)('Array', value) ? value : [value]; // Loop through each value and

            choiceValue.forEach(function (val) {
              return _this9._findAndSelectChoiceByValue(val);
            });
            return this;
          }
        }, {
          key: "setChoices",
          value: function setChoices() {
            var _this10 = this;

            var choices = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
            var value = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
            var label = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : '';
            var replaceChoices = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;

            if (!this._isSelectElement || !value) {
              return this;
            } // Clear choices if needed


            if (replaceChoices) {
              this.clearChoices();
            }

            this.containerOuter.removeLoadingState();

            var addGroupsAndChoices = function addGroupsAndChoices(groupOrChoice) {
              if (groupOrChoice.choices) {
                _this10._addGroup({
                  group: groupOrChoice,
                  id: groupOrChoice.id || null,
                  valueKey: value,
                  labelKey: label
                });
              } else {
                _this10._addChoice({
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
          }
        }, {
          key: "clearChoices",
          value: function clearChoices() {
            this._store.dispatch((0, _choices.clearChoices)());
          }
        }, {
          key: "clearStore",
          value: function clearStore() {
            this._store.dispatch((0, _misc.clearAll)());

            return this;
          }
        }, {
          key: "clearInput",
          value: function clearInput() {
            var shouldSetInputWidth = !this._isSelectOneElement;
            this.input.clear(shouldSetInputWidth);

            if (!this._isTextElement && this._canSearch) {
              this._isSearching = false;

              this._store.dispatch((0, _choices.activateChoices)(true));
            }

            return this;
          }
        }, {
          key: "ajax",
          value: function ajax(fn) {
            var _this11 = this;

            if (!this.initialised || !this._isSelectElement || !fn) {
              return this;
            }

            requestAnimationFrame(function () {
              return _this11._handleLoadingState(true);
            });
            fn(this._ajaxCallback());
            return this;
          }
          /* =====  End of Public functions  ====== */

          /* =============================================
          =                Private functions            =
          ============================================= */

        }, {
          key: "_render",
          value: function _render() {
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
          }
        }, {
          key: "_renderChoices",
          value: function _renderChoices() {
            var _this12 = this;

            var _this$_store = this._store,
                activeGroups = _this$_store.activeGroups,
                activeChoices = _this$_store.activeChoices;
            var choiceListFragment = document.createDocumentFragment();
            this.choiceList.clear();

            if (this.config.resetScrollPosition) {
              requestAnimationFrame(function () {
                return _this12.choiceList.scrollToTop();
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
                notice = (0, _utils.isType)('Function', this.config.noResultsText) ? this.config.noResultsText() : this.config.noResultsText;
                dropdownItem = this._getTemplate('notice', notice, 'no-results');
              } else {
                notice = (0, _utils.isType)('Function', this.config.noChoicesText) ? this.config.noChoicesText() : this.config.noChoicesText;
                dropdownItem = this._getTemplate('notice', notice, 'no-choices');
              }

              this.choiceList.append(dropdownItem);
            }
          }
        }, {
          key: "_renderItems",
          value: function _renderItems() {
            var activeItems = this._store.activeItems || [];
            this.itemList.clear(); // Create a fragment to store our list items
            // (so we don't have to update the DOM for each item)

            var itemListFragment = this._createItemsFragment(activeItems); // If we have items to add, append them


            if (itemListFragment.childNodes) {
              this.itemList.append(itemListFragment);
            }
          }
        }, {
          key: "_createGroupsFragment",
          value: function _createGroupsFragment(groups, choices, fragment) {
            var _this13 = this;

            var groupFragment = fragment || document.createDocumentFragment();

            var getGroupChoices = function getGroupChoices(group) {
              return choices.filter(function (choice) {
                if (_this13._isSelectOneElement) {
                  return choice.groupId === group.id;
                }

                return choice.groupId === group.id && (_this13.config.renderSelectedChoices === 'always' || !choice.selected);
              });
            }; // If sorting is enabled, filter groups


            if (this.config.shouldSort) {
              groups.sort(this.config.sortFn);
            }

            groups.forEach(function (group) {
              var groupChoices = getGroupChoices(group);

              if (groupChoices.length >= 1) {
                var dropdownGroup = _this13._getTemplate('choiceGroup', group);

                groupFragment.appendChild(dropdownGroup);

                _this13._createChoicesFragment(groupChoices, groupFragment, true);
              }
            });
            return groupFragment;
          }
        }, {
          key: "_createChoicesFragment",
          value: function _createChoicesFragment(choices, fragment) {
            var _this14 = this;

            var withinGroup = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false; // Create a fragment to store our list items (so we don't have to update the DOM for each item)

            var choicesFragment = fragment || document.createDocumentFragment();
            var _this$config = this.config,
                renderSelectedChoices = _this$config.renderSelectedChoices,
                searchResultLimit = _this$config.searchResultLimit,
                renderChoiceLimit = _this$config.renderChoiceLimit;
            var filter = this._isSearching ? _utils.sortByScore : this.config.sortFn;

            var appendChoice = function appendChoice(choice) {
              var shouldRender = renderSelectedChoices === 'auto' ? _this14._isSelectOneElement || !choice.selected : true;

              if (shouldRender) {
                var dropdownItem = _this14._getTemplate('choice', choice, _this14.config.itemSelectText);

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
          }
        }, {
          key: "_createItemsFragment",
          value: function _createItemsFragment(items) {
            var _this15 = this;

            var fragment = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null; // Create fragment to add elements to

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
              var listItem = _this15._getTemplate('item', item, removeItemButton); // Append it to list


              itemListFragment.appendChild(listItem);
            }; // Add each list item to list


            items.forEach(function (item) {
              return addItemToFragment(item);
            });
            return itemListFragment;
          }
        }, {
          key: "_triggerChange",
          value: function _triggerChange(value) {
            if (value === undefined || value === null) {
              return;
            }

            this.passedElement.triggerEvent(_constants.EVENTS.change, {
              value: value
            });
          }
        }, {
          key: "_selectPlaceholderChoice",
          value: function _selectPlaceholderChoice() {
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
          }
        }, {
          key: "_handleButtonAction",
          value: function _handleButtonAction(activeItems, element) {
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
          }
        }, {
          key: "_handleItemAction",
          value: function _handleItemAction(activeItems, element) {
            var _this16 = this;

            var hasShiftKey = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;

            if (!activeItems || !element || !this.config.removeItems || this._isSelectOneElement) {
              return;
            }

            var passedId = element.getAttribute('data-id'); // We only want to select one item with a click
            // so we deselect any items that aren't the target
            // unless shift is being pressed

            activeItems.forEach(function (item) {
              if (item.id === parseInt(passedId, 10) && !item.highlighted) {
                _this16.highlightItem(item);
              } else if (!hasShiftKey && item.highlighted) {
                _this16.unhighlightItem(item);
              }
            }); // Focus input as without focus, a user cannot do anything with a
            // highlighted item

            this.input.focus();
          }
        }, {
          key: "_handleChoiceAction",
          value: function _handleChoiceAction(activeItems, element) {
            if (!activeItems || !element) {
              return;
            } // If we are clicking on an option


            var id = element.getAttribute('data-id');

            var choice = this._store.getChoiceById(id);

            var passedKeyCode = activeItems[0] && activeItems[0].keyCode ? activeItems[0].keyCode : null;
            var hasActiveDropdown = this.dropdown.isActive; // Update choice keyCode

            choice.keyCode = passedKeyCode;
            this.passedElement.triggerEvent(_constants.EVENTS.choice, {
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
          }
        }, {
          key: "_handleBackspace",
          value: function _handleBackspace(activeItems) {
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
          }
        }, {
          key: "_setLoading",
          value: function _setLoading(isLoading) {
            this._store.dispatch((0, _general.setIsLoading)(isLoading));
          }
        }, {
          key: "_handleLoadingState",
          value: function _handleLoadingState() {
            var setLoading = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;
            var placeholderItem = this.itemList.getChild(".".concat(this.config.classNames.placeholder));

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
          }
        }, {
          key: "_handleSearch",
          value: function _handleSearch(value) {
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

              this.passedElement.triggerEvent(_constants.EVENTS.search, {
                value: value,
                resultCount: resultCount
              });
            } else if (hasUnactiveChoices) {
              // Otherwise reset choices to active
              this._isSearching = false;

              this._store.dispatch((0, _choices.activateChoices)(true));
            }
          }
        }, {
          key: "_canAddItem",
          value: function _canAddItem(activeItems, value) {
            var canAddItem = true;
            var notice = (0, _utils.isType)('Function', this.config.addItemText) ? this.config.addItemText(value) : this.config.addItemText;

            if (!this._isSelectOneElement) {
              var isDuplicateValue = (0, _utils.existsInArray)(activeItems, value);

              if (this.config.maxItemCount > 0 && this.config.maxItemCount <= activeItems.length) {
                // If there is a max entry limit and we have reached that limit
                // don't update
                canAddItem = false;
                notice = (0, _utils.isType)('Function', this.config.maxItemText) ? this.config.maxItemText(this.config.maxItemCount) : this.config.maxItemText;
              }

              if (!this.config.duplicateItemsAllowed && isDuplicateValue && canAddItem) {
                canAddItem = false;
                notice = (0, _utils.isType)('Function', this.config.uniqueItemText) ? this.config.uniqueItemText(value) : this.config.uniqueItemText;
              }

              if (this._isTextElement && this.config.addItems && canAddItem && (0, _utils.isType)('Function', this.config.addItemFilterFn) && !this.config.addItemFilterFn(value)) {
                canAddItem = false;
                notice = (0, _utils.isType)('Function', this.config.customAddItemText) ? this.config.customAddItemText(value) : this.config.customAddItemText;
              }
            }

            return {
              response: canAddItem,
              notice: notice
            };
          }
        }, {
          key: "_ajaxCallback",
          value: function _ajaxCallback() {
            var _this17 = this;

            return function (results, value, label) {
              if (!results || !value) {
                return;
              }

              var parsedResults = (0, _utils.isType)('Object', results) ? [results] : results;

              if (parsedResults && (0, _utils.isType)('Array', parsedResults) && parsedResults.length) {
                // Remove loading states/text
                _this17._handleLoadingState(false);

                _this17._setLoading(true); // Add each result as a choice


                parsedResults.forEach(function (result) {
                  if (result.choices) {
                    _this17._addGroup({
                      group: result,
                      id: result.id || null,
                      valueKey: value,
                      labelKey: label
                    });
                  } else {
                    _this17._addChoice({
                      value: (0, _utils.fetchFromObject)(result, value),
                      label: (0, _utils.fetchFromObject)(result, label),
                      isSelected: result.selected,
                      isDisabled: result.disabled,
                      customProperties: result.customProperties,
                      placeholder: result.placeholder
                    });
                  }
                });

                _this17._setLoading(false);

                if (_this17._isSelectOneElement) {
                  _this17._selectPlaceholderChoice();
                }
              } else {
                // No results, remove loading state
                _this17._handleLoadingState(false);
              }
            };
          }
        }, {
          key: "_searchChoices",
          value: function _searchChoices(value) {
            var newValue = (0, _utils.isType)('String', value) ? value.trim() : value;
            var currentValue = (0, _utils.isType)('String', this._currentValue) ? this._currentValue.trim() : this._currentValue;

            if (newValue.length < 1 && newValue === "".concat(currentValue, " ")) {
              return 0;
            } // If new value matches the desired length and is not the same as the current value with a space


            var haystack = this._store.searchableChoices;
            var needle = newValue;
            var keys = [].concat(this.config.searchFields);
            var options = Object.assign(this.config.fuseOptions, {
              keys: keys
            });
            var fuse = new _fuse["default"](haystack, options);
            var results = fuse.search(needle);
            this._currentValue = newValue;
            this._highlightPosition = 0;
            this._isSearching = true;

            this._store.dispatch((0, _choices.filterChoices)(results));

            return results.length;
          }
        }, {
          key: "_addEventListeners",
          value: function _addEventListeners() {
            document.addEventListener('keyup', this._onKeyUp);
            document.addEventListener('keydown', this._onKeyDown);
            document.addEventListener('click', this._onClick);
            document.addEventListener('touchmove', this._onTouchMove);
            document.addEventListener('touchend', this._onTouchEnd);
            document.addEventListener('mousedown', this._onMouseDown);
            document.addEventListener('mouseover', this._onMouseOver);

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
          }
        }, {
          key: "_removeEventListeners",
          value: function _removeEventListeners() {
            document.removeEventListener('keyup', this._onKeyUp);
            document.removeEventListener('keydown', this._onKeyDown);
            document.removeEventListener('click', this._onClick);
            document.removeEventListener('touchmove', this._onTouchMove);
            document.removeEventListener('touchend', this._onTouchEnd);
            document.removeEventListener('mousedown', this._onMouseDown);
            document.removeEventListener('mouseover', this._onMouseOver);

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
          }
        }, {
          key: "_onKeyDown",
          value: function _onKeyDown(event) {
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
            var BACK_KEY = _constants.KEY_CODES.BACK_KEY,
                DELETE_KEY = _constants.KEY_CODES.DELETE_KEY,
                ENTER_KEY = _constants.KEY_CODES.ENTER_KEY,
                A_KEY = _constants.KEY_CODES.A_KEY,
                ESC_KEY = _constants.KEY_CODES.ESC_KEY,
                UP_KEY = _constants.KEY_CODES.UP_KEY,
                DOWN_KEY = _constants.KEY_CODES.DOWN_KEY,
                PAGE_UP_KEY = _constants.KEY_CODES.PAGE_UP_KEY,
                PAGE_DOWN_KEY = _constants.KEY_CODES.PAGE_DOWN_KEY;
            var hasCtrlDownKeyPressed = ctrlKey || metaKey; // If a user is typing and the dropdown is not active

            if (!this._isTextElement && /[a-zA-Z0-9-_ ]/.test(keyString)) {
              this.showDropdown();
            } // Map keys to key actions


            var keyDownActions = (_keyDownActions = {}, _defineProperty(_keyDownActions, A_KEY, this._onAKey), _defineProperty(_keyDownActions, ENTER_KEY, this._onEnterKey), _defineProperty(_keyDownActions, ESC_KEY, this._onEscapeKey), _defineProperty(_keyDownActions, UP_KEY, this._onDirectionKey), _defineProperty(_keyDownActions, PAGE_UP_KEY, this._onDirectionKey), _defineProperty(_keyDownActions, DOWN_KEY, this._onDirectionKey), _defineProperty(_keyDownActions, PAGE_DOWN_KEY, this._onDirectionKey), _defineProperty(_keyDownActions, DELETE_KEY, this._onDeleteKey), _defineProperty(_keyDownActions, BACK_KEY, this._onDeleteKey), _keyDownActions); // If keycode has a function, run it

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
          }
        }, {
          key: "_onKeyUp",
          value: function _onKeyUp(_ref2) {
            var target = _ref2.target,
                keyCode = _ref2.keyCode;

            if (target !== this.input.element) {
              return;
            }

            var value = this.input.value;
            var activeItems = this._store.activeItems;

            var canAddItem = this._canAddItem(activeItems, value);

            var backKey = _constants.KEY_CODES.BACK_KEY,
                deleteKey = _constants.KEY_CODES.DELETE_KEY; // We are typing into a text input and have a value, we want to show a dropdown
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

                this._store.dispatch((0, _choices.activateChoices)(true));
              } else if (canSearch) {
                this._handleSearch(this.input.value);
              }
            }

            this._canSearch = this.config.searchEnabled;
          }
        }, {
          key: "_onAKey",
          value: function _onAKey(_ref3) {
            var hasItems = _ref3.hasItems,
                hasCtrlDownKeyPressed = _ref3.hasCtrlDownKeyPressed; // If CTRL + A or CMD + A have been pressed and there are items to select

            if (hasCtrlDownKeyPressed && hasItems) {
              this._canSearch = false;
              var shouldHightlightAll = this.config.removeItems && !this.input.value && this.input.element === document.activeElement;

              if (shouldHightlightAll) {
                this.highlightAll();
              }
            }
          }
        }, {
          key: "_onEnterKey",
          value: function _onEnterKey(_ref4) {
            var event = _ref4.event,
                target = _ref4.target,
                activeItems = _ref4.activeItems,
                hasActiveDropdown = _ref4.hasActiveDropdown;
            var enterKey = _constants.KEY_CODES.ENTER_KEY;
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
              var highlightedChoice = this.dropdown.getChild(".".concat(this.config.classNames.highlightedState));

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
          }
        }, {
          key: "_onEscapeKey",
          value: function _onEscapeKey(_ref5) {
            var hasActiveDropdown = _ref5.hasActiveDropdown;

            if (hasActiveDropdown) {
              this.hideDropdown(true);
              this.containerOuter.focus();
            }
          }
        }, {
          key: "_onDirectionKey",
          value: function _onDirectionKey(_ref6) {
            var event = _ref6.event,
                hasActiveDropdown = _ref6.hasActiveDropdown,
                keyCode = _ref6.keyCode,
                metaKey = _ref6.metaKey;
            var downKey = _constants.KEY_CODES.DOWN_KEY,
                pageUpKey = _constants.KEY_CODES.PAGE_UP_KEY,
                pageDownKey = _constants.KEY_CODES.PAGE_DOWN_KEY; // If up or down key is pressed, traverse through options

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
                var currentEl = this.dropdown.element.querySelector(".".concat(this.config.classNames.highlightedState));

                if (currentEl) {
                  nextEl = (0, _utils.getAdjacentEl)(currentEl, selectableChoiceIdentifier, directionInt);
                } else {
                  nextEl = this.dropdown.element.querySelector(selectableChoiceIdentifier);
                }
              }

              if (nextEl) {
                // We prevent default to stop the cursor moving
                // when pressing the arrow
                if (!(0, _utils.isScrolledIntoView)(nextEl, this.choiceList.element, directionInt)) {
                  this.choiceList.scrollToChoice(nextEl, directionInt);
                }

                this._highlightChoice(nextEl);
              } // Prevent default to maintain cursor position whilst
              // traversing dropdown options


              event.preventDefault();
            }
          }
        }, {
          key: "_onDeleteKey",
          value: function _onDeleteKey(_ref7) {
            var event = _ref7.event,
                target = _ref7.target,
                hasFocusedInput = _ref7.hasFocusedInput,
                activeItems = _ref7.activeItems; // If backspace or delete key is pressed and the input has no value

            if (hasFocusedInput && !target.value && !this._isSelectOneElement) {
              this._handleBackspace(activeItems);

              event.preventDefault();
            }
          }
        }, {
          key: "_onTouchMove",
          value: function _onTouchMove() {
            if (this._wasTap) {
              this._wasTap = false;
            }
          }
        }, {
          key: "_onTouchEnd",
          value: function _onTouchEnd(event) {
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
          }
        }, {
          key: "_onMouseDown",
          value: function _onMouseDown(event) {
            var target = event.target,
                shiftKey = event.shiftKey; // If we have our mouse down on the scrollbar and are on IE11...

            if (this.choiceList.element.contains(target) && (0, _utils.isIE11)()) {
              this._isScrollingOnIe = true;
            }

            if (!this.containerOuter.element.contains(target) || target === this.input.element) {
              return;
            }

            var activeItems = this._store.activeItems;
            var hasShiftKey = shiftKey;
            var buttonTarget = (0, _utils.findAncestorByAttrName)(target, 'data-button');
            var itemTarget = (0, _utils.findAncestorByAttrName)(target, 'data-item');
            var choiceTarget = (0, _utils.findAncestorByAttrName)(target, 'data-choice');

            if (buttonTarget) {
              this._handleButtonAction(activeItems, buttonTarget);
            } else if (itemTarget) {
              this._handleItemAction(activeItems, itemTarget, hasShiftKey);
            } else if (choiceTarget) {
              this._handleChoiceAction(activeItems, choiceTarget);
            }

            event.preventDefault();
          }
        }, {
          key: "_onMouseOver",
          value: function _onMouseOver(_ref9) {
            var target = _ref9.target;
            var targetWithinDropdown = target === this.dropdown || this.dropdown.element.contains(target);
            var shouldHighlightChoice = targetWithinDropdown && target.hasAttribute('data-choice');

            if (shouldHighlightChoice) {
              this._highlightChoice(target);
            }
          }
        }, {
          key: "_onClick",
          value: function _onClick(_ref10) {
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
              var hasHighlightedItems = this._store.highlightedActiveItems;

              if (hasHighlightedItems) {
                this.unhighlightAll();
              }

              this.containerOuter.removeFocusState();
              this.hideDropdown(true);
            }
          }
        }, {
          key: "_onFocus",
          value: function _onFocus(_ref11) {
            var _this18 = this;

            var target = _ref11.target;
            var focusWasWithinContainer = this.containerOuter.element.contains(target);

            if (!focusWasWithinContainer) {
              return;
            }

            var focusActions = {
              text: function text() {
                if (target === _this18.input.element) {
                  _this18.containerOuter.addFocusState();
                }
              },
              'select-one': function selectOne() {
                _this18.containerOuter.addFocusState();

                if (target === _this18.input.element) {
                  _this18.showDropdown(true);
                }
              },
              'select-multiple': function selectMultiple() {
                if (target === _this18.input.element) {
                  _this18.showDropdown(true); // If element is a select box, the focused element is the container and the dropdown
                  // isn't already open, focus and show dropdown


                  _this18.containerOuter.addFocusState();
                }
              }
            };
            focusActions[this.passedElement.element.type]();
          }
        }, {
          key: "_onBlur",
          value: function _onBlur(_ref12) {
            var _this19 = this;

            var target = _ref12.target;
            var blurWasWithinContainer = this.containerOuter.element.contains(target);

            if (blurWasWithinContainer && !this._isScrollingOnIe) {
              var activeItems = this._store.activeItems;
              var hasHighlightedItems = activeItems.some(function (item) {
                return item.highlighted;
              });
              var blurActions = {
                text: function text() {
                  if (target === _this19.input.element) {
                    _this19.containerOuter.removeFocusState();

                    if (hasHighlightedItems) {
                      _this19.unhighlightAll();
                    }

                    _this19.hideDropdown(true);
                  }
                },
                'select-one': function selectOne() {
                  _this19.containerOuter.removeFocusState();

                  if (target === _this19.input.element || target === _this19.containerOuter.element && !_this19._canSearch) {
                    _this19.hideDropdown(true);
                  }
                },
                'select-multiple': function selectMultiple() {
                  if (target === _this19.input.element) {
                    _this19.containerOuter.removeFocusState();

                    _this19.hideDropdown(true);

                    if (hasHighlightedItems) {
                      _this19.unhighlightAll();
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
          }
        }, {
          key: "_onFormReset",
          value: function _onFormReset() {
            this._store.dispatch((0, _misc.resetTo)(this._initialState));
          }
        }, {
          key: "_highlightChoice",
          value: function _highlightChoice() {
            var _this20 = this;

            var el = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
            var choices = Array.from(this.dropdown.element.querySelectorAll('[data-choice-selectable]'));

            if (!choices.length) {
              return;
            }

            var passedEl = el;
            var highlightedChoices = Array.from(this.dropdown.element.querySelectorAll(".".concat(this.config.classNames.highlightedState))); // Remove any highlighted choices

            highlightedChoices.forEach(function (choice) {
              choice.classList.remove(_this20.config.classNames.highlightedState);
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
            this.passedElement.triggerEvent(_constants.EVENTS.highlightChoice, {
              el: passedEl
            });

            if (this.dropdown.isActive) {
              // IE11 ignores aria-label and blocks virtual keyboard
              // if aria-activedescendant is set without a dropdown
              this.input.setActiveDescendant(passedEl.id);
              this.containerOuter.setActiveDescendant(passedEl.id);
            }
          }
        }, {
          key: "_addItem",
          value: function _addItem(_ref13) {
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
            var passedValue = (0, _utils.isType)('String', value) ? value.trim() : value;
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

            this._store.dispatch((0, _items.addItem)({
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


            this.passedElement.triggerEvent(_constants.EVENTS.addItem, {
              id: id,
              value: passedValue,
              label: passedLabel,
              customProperties: passedCustomProperties,
              groupValue: group && group.value ? group.value : undefined,
              keyCode: passedKeyCode
            });
            return this;
          }
        }, {
          key: "_removeItem",
          value: function _removeItem(item) {
            if (!item || !(0, _utils.isType)('Object', item)) {
              return this;
            }

            var id = item.id,
                value = item.value,
                label = item.label,
                choiceId = item.choiceId,
                groupId = item.groupId;
            var group = groupId >= 0 ? this._store.getGroupById(groupId) : null;

            this._store.dispatch((0, _items.removeItem)(id, choiceId));

            if (group && group.value) {
              this.passedElement.triggerEvent(_constants.EVENTS.removeItem, {
                id: id,
                value: value,
                label: label,
                groupValue: group.value
              });
            } else {
              this.passedElement.triggerEvent(_constants.EVENTS.removeItem, {
                id: id,
                value: value,
                label: label
              });
            }

            return this;
          }
        }, {
          key: "_addChoice",
          value: function _addChoice(_ref14) {
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
            var choiceElementId = "".concat(this._baseId, "-").concat(this._idNames.itemChoice, "-").concat(choiceId);

            this._store.dispatch((0, _choices.addChoice)({
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
          }
        }, {
          key: "_addGroup",
          value: function _addGroup(_ref15) {
            var _this21 = this;

            var group = _ref15.group,
                id = _ref15.id,
                _ref15$valueKey = _ref15.valueKey,
                valueKey = _ref15$valueKey === void 0 ? 'value' : _ref15$valueKey,
                _ref15$labelKey = _ref15.labelKey,
                labelKey = _ref15$labelKey === void 0 ? 'label' : _ref15$labelKey;
            var groupChoices = (0, _utils.isType)('Object', group) ? group.choices : Array.from(group.getElementsByTagName('OPTION'));
            var groupId = id || Math.floor(new Date().valueOf() * Math.random());
            var isDisabled = group.disabled ? group.disabled : false;

            if (groupChoices) {
              this._store.dispatch((0, _groups.addGroup)(group.label, groupId, true, isDisabled));

              var addGroupChoices = function addGroupChoices(choice) {
                var isOptDisabled = choice.disabled || choice.parentNode && choice.parentNode.disabled;

                _this21._addChoice({
                  value: choice[valueKey],
                  label: (0, _utils.isType)('Object', choice) ? choice[labelKey] : choice.innerHTML,
                  isSelected: choice.selected,
                  isDisabled: isOptDisabled,
                  groupId: groupId,
                  customProperties: choice.customProperties,
                  placeholder: choice.placeholder
                });
              };

              groupChoices.forEach(addGroupChoices);
            } else {
              this._store.dispatch((0, _groups.addGroup)(group.label, group.id, false, group.disabled));
            }
          }
        }, {
          key: "_getTemplate",
          value: function _getTemplate(template) {
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
          }
        }, {
          key: "_createTemplates",
          value: function _createTemplates() {
            var callbackOnCreateTemplates = this.config.callbackOnCreateTemplates;
            var userTemplates = {};

            if (callbackOnCreateTemplates && (0, _utils.isType)('Function', callbackOnCreateTemplates)) {
              userTemplates = callbackOnCreateTemplates.call(this, _utils.strToEl);
            }

            this.config.templates = (0, _deepmerge["default"])(_templates.TEMPLATES, userTemplates);
          }
        }, {
          key: "_createElements",
          value: function _createElements() {
            this.containerOuter = new _components.Container({
              element: this._getTemplate('containerOuter', this._direction, this._isSelectElement, this._isSelectOneElement, this.config.searchEnabled, this.passedElement.element.type),
              classNames: this.config.classNames,
              type: this.passedElement.element.type,
              position: this.config.position
            });
            this.containerInner = new _components.Container({
              element: this._getTemplate('containerInner'),
              classNames: this.config.classNames,
              type: this.passedElement.element.type,
              position: this.config.position
            });
            this.input = new _components.Input({
              element: this._getTemplate('input'),
              classNames: this.config.classNames,
              type: this.passedElement.element.type
            });
            this.choiceList = new _components.List({
              element: this._getTemplate('choiceList', this._isSelectOneElement)
            });
            this.itemList = new _components.List({
              element: this._getTemplate('itemList', this._isSelectOneElement)
            });
            this.dropdown = new _components.Dropdown({
              element: this._getTemplate('dropdown'),
              classNames: this.config.classNames,
              type: this.passedElement.element.type
            });
          }
        }, {
          key: "_createStructure",
          value: function _createStructure() {
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
          }
        }, {
          key: "_addPredefinedChoices",
          value: function _addPredefinedChoices() {
            var _this22 = this;

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
                return _this22._addGroup({
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

                if (_this22._isSelectElement) {
                  // If the choice is actually a group
                  if (choice.choices) {
                    _this22._addGroup({
                      group: choice,
                      id: choice.id || null
                    });
                  } else {
                    // If there is a selected choice already or the choice is not
                    // the first in the array, add each choice normally
                    // Otherwise pre-select the first choice in the array if it's a single select
                    var shouldPreselect = _this22._isSelectOneElement && !hasSelectedChoice && index === 0;
                    var isSelected = shouldPreselect ? true : choice.selected;
                    var isDisabled = shouldPreselect ? false : choice.disabled;

                    _this22._addChoice({
                      value: value,
                      label: label,
                      isSelected: isSelected,
                      isDisabled: isDisabled,
                      customProperties: customProperties,
                      placeholder: placeholder
                    });
                  }
                } else {
                  _this22._addChoice({
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
          }
        }, {
          key: "_addPredefinedItems",
          value: function _addPredefinedItems() {
            var _this23 = this;

            var handlePresetItem = function handlePresetItem(item) {
              var itemType = (0, _utils.getType)(item);

              if (itemType === 'Object' && item.value) {
                _this23._addItem({
                  value: item.value,
                  label: item.label,
                  choiceId: item.id,
                  customProperties: item.customProperties,
                  placeholder: item.placeholder
                });
              } else if (itemType === 'String') {
                _this23._addItem({
                  value: item
                });
              }
            };

            this._presetItems.forEach(function (item) {
              return handlePresetItem(item);
            });
          }
        }, {
          key: "_setChoiceOrItem",
          value: function _setChoiceOrItem(item) {
            var _this24 = this;

            var itemType = (0, _utils.getType)(item).toLowerCase();
            var handleType = {
              object: function object() {
                if (!item.value) {
                  return;
                } // If we are dealing with a select input, we need to create an option first
                // that is then selected. For text inputs we can just add items normally.


                if (!_this24._isTextElement) {
                  _this24._addChoice({
                    value: item.value,
                    label: item.label,
                    isSelected: true,
                    isDisabled: false,
                    customProperties: item.customProperties,
                    placeholder: item.placeholder
                  });
                } else {
                  _this24._addItem({
                    value: item.value,
                    label: item.label,
                    choiceId: item.id,
                    customProperties: item.customProperties,
                    placeholder: item.placeholder
                  });
                }
              },
              string: function string() {
                if (!_this24._isTextElement) {
                  _this24._addChoice({
                    value: item,
                    label: item,
                    isSelected: true,
                    isDisabled: false
                  });
                } else {
                  _this24._addItem({
                    value: item
                  });
                }
              }
            };
            handleType[itemType]();
          }
        }, {
          key: "_findAndSelectChoiceByValue",
          value: function _findAndSelectChoiceByValue(val) {
            var _this25 = this;

            var choices = this._store.choices; // Check 'value' property exists and the choice isn't already selected

            var foundChoice = choices.find(function (choice) {
              return _this25.config.itemComparer(choice.value, val);
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
          }
        }, {
          key: "_generateInstances",
          value: function _generateInstances(elements, config) {
            return elements.reduce(function (instances, element) {
              instances.push(new Choices(element, config));
              return instances;
            }, [this]);
          }
        }, {
          key: "_generatePlaceholderValue",
          value: function _generatePlaceholderValue() {
            if (this._isSelectOneElement) {
              return false;
            }

            return this.config.placeholder ? this.config.placeholderValue || this.passedElement.element.getAttribute('placeholder') : false;
          }
          /* =====  End of Private functions  ====== */

        }]);

        return Choices;
      }();

      Choices.userDefaults = {}; // We cannot export default here due to Webpack: https://github.com/webpack/webpack/issues/3929

      module.exports = Choices;
      /***/
    },
    /* 11 */

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

              var Bitap =
              /*#__PURE__*/
              function () {
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

              var Fuse =
              /*#__PURE__*/
              function () {
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
    /* 12 */

    /***/
    function (module, __webpack_exports__, __webpack_require__) {
      "use strict";

      __webpack_require__.r(__webpack_exports__);

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

      function mergeObject(target, source, options) {
        var destination = {};

        if (options.isMergeableObject(target)) {
          Object.keys(target).forEach(function (key) {
            destination[key] = cloneUnlessOtherwiseSpecified(target[key], options);
          });
        }

        Object.keys(source).forEach(function (key) {
          if (!options.isMergeableObject(source[key]) || !target[key]) {
            destination[key] = cloneUnlessOtherwiseSpecified(source[key], options);
          } else {
            destination[key] = deepmerge(target[key], source[key], options);
          }
        });
        return destination;
      }

      function deepmerge(target, source, options) {
        options = options || {};
        options.arrayMerge = options.arrayMerge || defaultArrayMerge;
        options.isMergeableObject = options.isMergeableObject || isMergeableObject;
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
      /* harmony default export */

      __webpack_exports__["default"] = deepmerge_1;
      /***/
    },
    /* 13 */

    /***/
    function (module, exports, __webpack_require__) {
      "use strict";

      Object.defineProperty(exports, "__esModule", {
        value: true
      });
      exports["default"] = void 0;

      var _redux = __webpack_require__(6);

      var _index = _interopRequireDefault(__webpack_require__(15));

      function _interopRequireDefault(obj) {
        return obj && obj.__esModule ? obj : {
          "default": obj
        };
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

      var Store =
      /*#__PURE__*/
      function () {
        function Store() {
          _classCallCheck(this, Store);

          this._store = (0, _redux.createStore)(_index["default"], window.__REDUX_DEVTOOLS_EXTENSION__ && window.__REDUX_DEVTOOLS_EXTENSION__());
        }
        /**
         * Subscribe store to function call (wrapped Redux method)
         * @param  {Function} onChange Function to trigger when state changes
         * @return
         */


        _createClass(Store, [{
          key: "subscribe",
          value: function subscribe(onChange) {
            this._store.subscribe(onChange);
          }
          /**
           * Dispatch event to store (wrapped Redux method)
           * @param  {Function} action Action function to trigger
           * @return
           */

        }, {
          key: "dispatch",
          value: function dispatch(action) {
            this._store.dispatch(action);
          }
          /**
           * Get store object (wrapping Redux method)
           * @return {Object} State
           */

        }, {
          key: "isLoading",

          /**
           * Get loading state from store
           * @return {Boolean} Loading State
           */
          value: function isLoading() {
            return this.state.general.loading;
          }
          /**
           * Get single choice by it's ID
           * @return {Object} Found choice
           */

        }, {
          key: "getChoiceById",
          value: function getChoiceById(id) {
            if (id) {
              var choices = this.activeChoices;
              var foundChoice = choices.find(function (choice) {
                return choice.id === parseInt(id, 10);
              });
              return foundChoice;
            }

            return false;
          }
          /**
           * Get group by group id
           * @param  {Number} id Group ID
           * @return {Object}    Group data
           */

        }, {
          key: "getGroupById",
          value: function getGroupById(id) {
            return this.groups.find(function (group) {
              return group.id === parseInt(id, 10);
            });
          }
        }, {
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
            var groups = this.groups;
            var choices = this.choices;
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
      }();

      exports["default"] = Store;
      /***/
    },
    /* 14 */

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
    /* 15 */

    /***/
    function (module, exports, __webpack_require__) {
      "use strict";

      Object.defineProperty(exports, "__esModule", {
        value: true
      });
      exports["default"] = void 0;

      var _redux = __webpack_require__(6);

      var _items = _interopRequireDefault(__webpack_require__(16));

      var _groups = _interopRequireDefault(__webpack_require__(17));

      var _choices = _interopRequireDefault(__webpack_require__(18));

      var _general = _interopRequireDefault(__webpack_require__(19));

      var _utils = __webpack_require__(0);

      function _interopRequireDefault(obj) {
        return obj && obj.__esModule ? obj : {
          "default": obj
        };
      }

      var appReducer = (0, _redux.combineReducers)({
        items: _items["default"],
        groups: _groups["default"],
        choices: _choices["default"],
        general: _general["default"]
      });

      var rootReducer = function rootReducer(passedState, action) {
        var state = passedState; // If we are clearing all items, groups and options we reassign
        // state and then pass that state to our proper reducer. This isn't
        // mutating our actual state
        // See: http://stackoverflow.com/a/35641992

        if (action.type === 'CLEAR_ALL') {
          state = undefined;
        } else if (action.type === 'RESET_TO') {
          return (0, _utils.cloneObject)(action.state);
        }

        return appReducer(state, action);
      };

      var _default = rootReducer;
      exports["default"] = _default;
      /***/
    },
    /* 16 */

    /***/
    function (module, exports, __webpack_require__) {
      "use strict";

      Object.defineProperty(exports, "__esModule", {
        value: true
      });
      exports["default"] = items;
      exports.defaultState = void 0;
      var defaultState = [];
      exports.defaultState = defaultState;

      function items() {
        var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : defaultState;
        var action = arguments.length > 1 ? arguments[1] : undefined;

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
      }
      /***/

    },
    /* 17 */

    /***/
    function (module, exports, __webpack_require__) {
      "use strict";

      Object.defineProperty(exports, "__esModule", {
        value: true
      });
      exports["default"] = groups;
      exports.defaultState = void 0;
      var defaultState = [];
      exports.defaultState = defaultState;

      function groups() {
        var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : defaultState;
        var action = arguments.length > 1 ? arguments[1] : undefined;

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
      }
      /***/

    },
    /* 18 */

    /***/
    function (module, exports, __webpack_require__) {
      "use strict";

      Object.defineProperty(exports, "__esModule", {
        value: true
      });
      exports["default"] = choices;
      exports.defaultState = void 0;
      var defaultState = [];
      exports.defaultState = defaultState;

      function choices() {
        var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : defaultState;
        var action = arguments.length > 1 ? arguments[1] : undefined;

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
              return defaultState;
            }

          default:
            {
              return state;
            }
        }
      }
      /***/

    },
    /* 19 */

    /***/
    function (module, exports, __webpack_require__) {
      "use strict";

      Object.defineProperty(exports, "__esModule", {
        value: true
      });
      exports["default"] = exports.defaultState = void 0;
      var defaultState = {
        loading: false
      };
      exports.defaultState = defaultState;

      var general = function general() {
        var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : defaultState;
        var action = arguments.length > 1 ? arguments[1] : undefined;

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

      var _default = general;
      exports["default"] = _default;
      /***/
    },
    /* 20 */

    /***/
    function (module, exports, __webpack_require__) {
      "use strict";

      Object.defineProperty(exports, "__esModule", {
        value: true
      });
      Object.defineProperty(exports, "Dropdown", {
        enumerable: true,
        get: function get() {
          return _dropdown["default"];
        }
      });
      Object.defineProperty(exports, "Container", {
        enumerable: true,
        get: function get() {
          return _container["default"];
        }
      });
      Object.defineProperty(exports, "Input", {
        enumerable: true,
        get: function get() {
          return _input["default"];
        }
      });
      Object.defineProperty(exports, "List", {
        enumerable: true,
        get: function get() {
          return _list["default"];
        }
      });
      Object.defineProperty(exports, "WrappedInput", {
        enumerable: true,
        get: function get() {
          return _wrappedInput["default"];
        }
      });
      Object.defineProperty(exports, "WrappedSelect", {
        enumerable: true,
        get: function get() {
          return _wrappedSelect["default"];
        }
      });

      var _dropdown = _interopRequireDefault(__webpack_require__(21));

      var _container = _interopRequireDefault(__webpack_require__(22));

      var _input = _interopRequireDefault(__webpack_require__(23));

      var _list = _interopRequireDefault(__webpack_require__(24));

      var _wrappedInput = _interopRequireDefault(__webpack_require__(25));

      var _wrappedSelect = _interopRequireDefault(__webpack_require__(26));

      function _interopRequireDefault(obj) {
        return obj && obj.__esModule ? obj : {
          "default": obj
        };
      }
      /***/

    },
    /* 21 */

    /***/
    function (module, exports, __webpack_require__) {
      "use strict";

      Object.defineProperty(exports, "__esModule", {
        value: true
      });
      exports["default"] = void 0;

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

      var Dropdown =
      /*#__PURE__*/
      function () {
        function Dropdown(_ref) {
          var element = _ref.element,
              type = _ref.type,
              classNames = _ref.classNames;

          _classCallCheck(this, Dropdown);

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


        _createClass(Dropdown, [{
          key: "distanceFromTopWindow",
          value: function distanceFromTopWindow() {
            this.dimensions = this.element.getBoundingClientRect();
            this.position = Math.ceil(this.dimensions.top + window.pageYOffset + this.element.offsetHeight);
            return this.position;
          }
          /**
           * Find element that matches passed selector
           * @return {HTMLElement}
           */

        }, {
          key: "getChild",
          value: function getChild(selector) {
            return this.element.querySelector(selector);
          }
          /**
           * Show dropdown to user by adding active state class
           * @return {Object} Class instance
           * @public
           */

        }, {
          key: "show",
          value: function show() {
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

        }, {
          key: "hide",
          value: function hide() {
            this.element.classList.remove(this.classNames.activeState);
            this.element.setAttribute('aria-expanded', 'false');
            this.isActive = false;
            return this;
          }
        }]);

        return Dropdown;
      }();

      exports["default"] = Dropdown;
      /***/
    },
    /* 22 */

    /***/
    function (module, exports, __webpack_require__) {
      "use strict";

      Object.defineProperty(exports, "__esModule", {
        value: true
      });
      exports["default"] = void 0;

      var _utils = __webpack_require__(0);

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

      var Container =
      /*#__PURE__*/
      function () {
        function Container(_ref) {
          var element = _ref.element,
              type = _ref.type,
              classNames = _ref.classNames,
              position = _ref.position;

          _classCallCheck(this, Container);

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


        _createClass(Container, [{
          key: "addEventListeners",
          value: function addEventListeners() {
            this.element.addEventListener('focus', this._onFocus);
            this.element.addEventListener('blur', this._onBlur);
          }
          /**
           * Remove event listeners
           */

          /** */

        }, {
          key: "removeEventListeners",
          value: function removeEventListeners() {
            this.element.removeEventListener('focus', this._onFocus);
            this.element.removeEventListener('blur', this._onBlur);
          }
          /**
           * Determine whether container should be flipped
           * based on passed dropdown position
           * @param {Number} dropdownPos
           * @returns
           */

        }, {
          key: "shouldFlip",
          value: function shouldFlip(dropdownPos) {
            var windowHeight = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : (0, _utils.getWindowHeight)();

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

        }, {
          key: "setActiveDescendant",
          value: function setActiveDescendant(activeDescendantID) {
            this.element.setAttribute('aria-activedescendant', activeDescendantID);
          }
          /**
           * Remove active descendant attribute
           */

        }, {
          key: "removeActiveDescendant",
          value: function removeActiveDescendant() {
            this.element.removeAttribute('aria-activedescendant');
          }
        }, {
          key: "open",
          value: function open(dropdownPos) {
            this.element.classList.add(this.classNames.openState);
            this.element.setAttribute('aria-expanded', 'true');
            this.isOpen = true;

            if (this.shouldFlip(dropdownPos)) {
              this.element.classList.add(this.classNames.flippedState);
              this.isFlipped = true;
            }
          }
        }, {
          key: "close",
          value: function close() {
            this.element.classList.remove(this.classNames.openState);
            this.element.setAttribute('aria-expanded', 'false');
            this.removeActiveDescendant();
            this.isOpen = false; // A dropdown flips if it does not have space within the page

            if (this.isFlipped) {
              this.element.classList.remove(this.classNames.flippedState);
              this.isFlipped = false;
            }
          }
        }, {
          key: "focus",
          value: function focus() {
            if (!this.isFocussed) {
              this.element.focus();
            }
          }
        }, {
          key: "addFocusState",
          value: function addFocusState() {
            this.element.classList.add(this.classNames.focusState);
          }
        }, {
          key: "removeFocusState",
          value: function removeFocusState() {
            this.element.classList.remove(this.classNames.focusState);
          }
          /**
           * Remove disabled state
           */

        }, {
          key: "enable",
          value: function enable() {
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

        }, {
          key: "disable",
          value: function disable() {
            this.element.classList.add(this.classNames.disabledState);
            this.element.setAttribute('aria-disabled', 'true');

            if (this.type === 'select-one') {
              this.element.setAttribute('tabindex', '-1');
            }

            this.isDisabled = true;
          }
        }, {
          key: "wrap",
          value: function wrap(element) {
            (0, _utils.wrap)(element, this.element);
          }
        }, {
          key: "unwrap",
          value: function unwrap(element) {
            // Move passed element outside this element
            this.element.parentNode.insertBefore(element, this.element); // Remove this element

            this.element.parentNode.removeChild(this.element);
          }
          /**
           * Add loading state to element
           */

        }, {
          key: "addLoadingState",
          value: function addLoadingState() {
            this.element.classList.add(this.classNames.loadingState);
            this.element.setAttribute('aria-busy', 'true');
            this.isLoading = true;
          }
          /**
           * Remove loading state from element
           */

        }, {
          key: "removeLoadingState",
          value: function removeLoadingState() {
            this.element.classList.remove(this.classNames.loadingState);
            this.element.removeAttribute('aria-busy');
            this.isLoading = false;
          }
          /**
           * Set focussed state
           */

        }, {
          key: "_onFocus",
          value: function _onFocus() {
            this.isFocussed = true;
          }
          /**
           * Remove blurred state
           */

        }, {
          key: "_onBlur",
          value: function _onBlur() {
            this.isFocussed = false;
          }
        }]);

        return Container;
      }();

      exports["default"] = Container;
      /***/
    },
    /* 23 */

    /***/
    function (module, exports, __webpack_require__) {
      "use strict";

      Object.defineProperty(exports, "__esModule", {
        value: true
      });
      exports["default"] = void 0;

      var _utils = __webpack_require__(0);

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

      var Input =
      /*#__PURE__*/
      function () {
        function Input(_ref) {
          var element = _ref.element,
              type = _ref.type,
              classNames = _ref.classNames,
              placeholderValue = _ref.placeholderValue;

          _classCallCheck(this, Input);

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

        _createClass(Input, [{
          key: "addEventListeners",
          value: function addEventListeners() {
            this.element.addEventListener('input', this._onInput);
            this.element.addEventListener('paste', this._onPaste);
            this.element.addEventListener('focus', this._onFocus);
            this.element.addEventListener('blur', this._onBlur);

            if (this.element.form) {
              this.element.form.addEventListener('reset', this._onFormReset);
            }
          }
        }, {
          key: "removeEventListeners",
          value: function removeEventListeners() {
            this.element.removeEventListener('input', this._onInput);
            this.element.removeEventListener('paste', this._onPaste);
            this.element.removeEventListener('focus', this._onFocus);
            this.element.removeEventListener('blur', this._onBlur);

            if (this.element.form) {
              this.element.form.removeEventListener('reset', this._onFormReset);
            }
          }
        }, {
          key: "enable",
          value: function enable() {
            this.element.removeAttribute('disabled');
            this.isDisabled = false;
          }
        }, {
          key: "disable",
          value: function disable() {
            this.element.setAttribute('disabled', '');
            this.isDisabled = true;
          }
        }, {
          key: "focus",
          value: function focus() {
            if (!this.isFocussed) {
              this.element.focus();
            }
          }
        }, {
          key: "blur",
          value: function blur() {
            if (this.isFocussed) {
              this.element.blur();
            }
          }
          /**
           * Set value of input to blank
           * @return {Object} Class instance
           * @public
           */

        }, {
          key: "clear",
          value: function clear() {
            var setWidth = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;

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

        }, {
          key: "setWidth",
          value: function setWidth(enforceWidth) {
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
          }
        }, {
          key: "calcWidth",
          value: function calcWidth(callback) {
            return (0, _utils.calcWidthOfInput)(this.element, callback);
          }
        }, {
          key: "setActiveDescendant",
          value: function setActiveDescendant(activeDescendantID) {
            this.element.setAttribute('aria-activedescendant', activeDescendantID);
          }
        }, {
          key: "removeActiveDescendant",
          value: function removeActiveDescendant() {
            this.element.removeAttribute('aria-activedescendant');
          }
        }, {
          key: "_onInput",
          value: function _onInput() {
            if (this.type !== 'select-one') {
              this.setWidth();
            }
          }
        }, {
          key: "_onPaste",
          value: function _onPaste(event) {
            var target = event.target;

            if (target === this.element && this.preventPaste) {
              event.preventDefault();
            }
          }
        }, {
          key: "_onFocus",
          value: function _onFocus() {
            this.isFocussed = true;
          }
        }, {
          key: "_onBlur",
          value: function _onBlur() {
            this.isFocussed = false;
          }
        }, {
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
            return (0, _utils.sanitise)(this.element.value);
          }
        }]);

        return Input;
      }();

      exports["default"] = Input;
      /***/
    },
    /* 24 */

    /***/
    function (module, exports, __webpack_require__) {
      "use strict";

      Object.defineProperty(exports, "__esModule", {
        value: true
      });
      exports["default"] = void 0;

      var _constants = __webpack_require__(1);

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

      var List =
      /*#__PURE__*/
      function () {
        function List(_ref) {
          var element = _ref.element;

          _classCallCheck(this, List);

          Object.assign(this, {
            element: element
          });
          this.scrollPos = this.element.scrollTop;
          this.height = this.element.offsetHeight;
          this.hasChildren = !!this.element.children;
        }

        _createClass(List, [{
          key: "clear",
          value: function clear() {
            this.element.innerHTML = '';
          }
        }, {
          key: "append",
          value: function append(node) {
            this.element.appendChild(node);
          }
        }, {
          key: "getChild",
          value: function getChild(selector) {
            return this.element.querySelector(selector);
          }
        }, {
          key: "scrollToTop",
          value: function scrollToTop() {
            this.element.scrollTop = 0;
          }
        }, {
          key: "scrollToChoice",
          value: function scrollToChoice(choice, direction) {
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
          }
        }, {
          key: "_scrollDown",
          value: function _scrollDown(scrollPos, strength, endpoint) {
            var easing = (endpoint - scrollPos) / strength;
            var distance = easing > 1 ? easing : 1;
            this.element.scrollTop = scrollPos + distance;
          }
        }, {
          key: "_scrollUp",
          value: function _scrollUp(scrollPos, strength, endpoint) {
            var easing = (scrollPos - endpoint) / strength;
            var distance = easing > 1 ? easing : 1;
            this.element.scrollTop = scrollPos - distance;
          }
        }, {
          key: "_animateScroll",
          value: function _animateScroll(time, endpoint, direction) {
            var _this2 = this;

            var strength = _constants.SCROLLING_SPEED;
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
          }
        }]);

        return List;
      }();

      exports["default"] = List;
      /***/
    },
    /* 25 */

    /***/
    function (module, exports, __webpack_require__) {
      "use strict";

      Object.defineProperty(exports, "__esModule", {
        value: true
      });
      exports["default"] = void 0;

      var _wrappedElement = _interopRequireDefault(__webpack_require__(4));

      function _interopRequireDefault(obj) {
        return obj && obj.__esModule ? obj : {
          "default": obj
        };
      }

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

      function _possibleConstructorReturn(self, call) {
        if (call && (_typeof(call) === "object" || typeof call === "function")) {
          return call;
        }

        return _assertThisInitialized(self);
      }

      function _assertThisInitialized(self) {
        if (self === void 0) {
          throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
        }

        return self;
      }

      function _get(target, property, receiver) {
        if (typeof Reflect !== "undefined" && Reflect.get) {
          _get = Reflect.get;
        } else {
          _get = function _get(target, property, receiver) {
            var base = _superPropBase(target, property);

            if (!base) return;
            var desc = Object.getOwnPropertyDescriptor(base, property);

            if (desc.get) {
              return desc.get.call(receiver);
            }

            return desc.value;
          };
        }

        return _get(target, property, receiver || target);
      }

      function _superPropBase(object, property) {
        while (!Object.prototype.hasOwnProperty.call(object, property)) {
          object = _getPrototypeOf(object);
          if (object === null) break;
        }

        return object;
      }

      function _getPrototypeOf(o) {
        _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) {
          return o.__proto__ || Object.getPrototypeOf(o);
        };
        return _getPrototypeOf(o);
      }

      function _inherits(subClass, superClass) {
        if (typeof superClass !== "function" && superClass !== null) {
          throw new TypeError("Super expression must either be null or a function");
        }

        subClass.prototype = Object.create(superClass && superClass.prototype, {
          constructor: {
            value: subClass,
            writable: true,
            configurable: true
          }
        });
        if (superClass) _setPrototypeOf(subClass, superClass);
      }

      function _setPrototypeOf(o, p) {
        _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) {
          o.__proto__ = p;
          return o;
        };

        return _setPrototypeOf(o, p);
      }

      var WrappedInput =
      /*#__PURE__*/
      function (_WrappedElement) {
        _inherits(WrappedInput, _WrappedElement);

        function WrappedInput(_ref) {
          var _this;

          var element = _ref.element,
              classNames = _ref.classNames,
              delimiter = _ref.delimiter;

          _classCallCheck(this, WrappedInput);

          _this = _possibleConstructorReturn(this, _getPrototypeOf(WrappedInput).call(this, {
            element: element,
            classNames: classNames
          }));
          _this.delimiter = delimiter;
          return _this;
        }

        _createClass(WrappedInput, [{
          key: "value",
          set: function set(items) {
            var itemValues = items.map(function (_ref2) {
              var value = _ref2.value;
              return value;
            });
            var joinedValues = itemValues.join(this.delimiter);
            this.element.setAttribute('value', joinedValues);
            this.element.value = joinedValues;
          } // @todo figure out why we need this? Perhaps a babel issue
          ,
          get: function get() {
            return _get(_getPrototypeOf(WrappedInput.prototype), "value", this);
          }
        }]);

        return WrappedInput;
      }(_wrappedElement["default"]);

      exports["default"] = WrappedInput;
      /***/
    },
    /* 26 */

    /***/
    function (module, exports, __webpack_require__) {
      "use strict";

      Object.defineProperty(exports, "__esModule", {
        value: true
      });
      exports["default"] = void 0;

      var _wrappedElement = _interopRequireDefault(__webpack_require__(4));

      var _templates = _interopRequireDefault(__webpack_require__(5));

      function _interopRequireDefault(obj) {
        return obj && obj.__esModule ? obj : {
          "default": obj
        };
      }

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

      function _possibleConstructorReturn(self, call) {
        if (call && (_typeof(call) === "object" || typeof call === "function")) {
          return call;
        }

        return _assertThisInitialized(self);
      }

      function _assertThisInitialized(self) {
        if (self === void 0) {
          throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
        }

        return self;
      }

      function _getPrototypeOf(o) {
        _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) {
          return o.__proto__ || Object.getPrototypeOf(o);
        };
        return _getPrototypeOf(o);
      }

      function _inherits(subClass, superClass) {
        if (typeof superClass !== "function" && superClass !== null) {
          throw new TypeError("Super expression must either be null or a function");
        }

        subClass.prototype = Object.create(superClass && superClass.prototype, {
          constructor: {
            value: subClass,
            writable: true,
            configurable: true
          }
        });
        if (superClass) _setPrototypeOf(subClass, superClass);
      }

      function _setPrototypeOf(o, p) {
        _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) {
          o.__proto__ = p;
          return o;
        };

        return _setPrototypeOf(o, p);
      }

      var WrappedSelect =
      /*#__PURE__*/
      function (_WrappedElement) {
        _inherits(WrappedSelect, _WrappedElement);

        function WrappedSelect(_ref) {
          var element = _ref.element,
              classNames = _ref.classNames;

          _classCallCheck(this, WrappedSelect);

          return _possibleConstructorReturn(this, _getPrototypeOf(WrappedSelect).call(this, {
            element: element,
            classNames: classNames
          }));
        }

        _createClass(WrappedSelect, [{
          key: "appendDocFragment",
          value: function appendDocFragment(fragment) {
            this.element.innerHTML = '';
            this.element.appendChild(fragment);
          }
        }, {
          key: "placeholderOption",
          get: function get() {
            return this.element.querySelector('option[placeholder]');
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
            var fragment = document.createDocumentFragment();

            var addOptionToFragment = function addOptionToFragment(data) {
              // Create a standard select option
              var template = _templates["default"].option(data); // Append it to fragment


              fragment.appendChild(template);
            }; // Add each list item to list


            options.forEach(function (optionData) {
              return addOptionToFragment(optionData);
            });
            this.appendDocFragment(fragment);
          }
        }]);

        return WrappedSelect;
      }(_wrappedElement["default"]);

      exports["default"] = WrappedSelect;
      /***/
    },
    /* 27 */

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
    /* 28 */

    /***/
    function (module, exports, __webpack_require__) {
      "use strict";

      Object.defineProperty(exports, "__esModule", {
        value: true
      });
      exports.clearChoices = exports.activateChoices = exports.filterChoices = exports.addChoice = void 0;

      var _constants = __webpack_require__(1);

      var addChoice = function addChoice(_ref) {
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
          type: _constants.ACTION_TYPES.ADD_CHOICE,
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

      exports.addChoice = addChoice;

      var filterChoices = function filterChoices(results) {
        return {
          type: _constants.ACTION_TYPES.FILTER_CHOICES,
          results: results
        };
      };

      exports.filterChoices = filterChoices;

      var activateChoices = function activateChoices() {
        var active = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;
        return {
          type: _constants.ACTION_TYPES.ACTIVATE_CHOICES,
          active: active
        };
      };

      exports.activateChoices = activateChoices;

      var clearChoices = function clearChoices() {
        return {
          type: _constants.ACTION_TYPES.CLEAR_CHOICES
        };
      };

      exports.clearChoices = clearChoices;
      /***/
    },
    /* 29 */

    /***/
    function (module, exports, __webpack_require__) {
      "use strict";

      Object.defineProperty(exports, "__esModule", {
        value: true
      });
      exports.highlightItem = exports.removeItem = exports.addItem = void 0;

      var _constants = __webpack_require__(1);

      var addItem = function addItem(_ref) {
        var value = _ref.value,
            label = _ref.label,
            id = _ref.id,
            choiceId = _ref.choiceId,
            groupId = _ref.groupId,
            customProperties = _ref.customProperties,
            placeholder = _ref.placeholder,
            keyCode = _ref.keyCode;
        return {
          type: _constants.ACTION_TYPES.ADD_ITEM,
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

      exports.addItem = addItem;

      var removeItem = function removeItem(id, choiceId) {
        return {
          type: _constants.ACTION_TYPES.REMOVE_ITEM,
          id: id,
          choiceId: choiceId
        };
      };

      exports.removeItem = removeItem;

      var highlightItem = function highlightItem(id, highlighted) {
        return {
          type: _constants.ACTION_TYPES.HIGHLIGHT_ITEM,
          id: id,
          highlighted: highlighted
        };
      };

      exports.highlightItem = highlightItem;
      /***/
    },
    /* 30 */

    /***/
    function (module, exports, __webpack_require__) {
      "use strict";

      Object.defineProperty(exports, "__esModule", {
        value: true
      });
      exports.addGroup = void 0;

      var _constants = __webpack_require__(1);
      /* eslint-disable import/prefer-default-export */


      var addGroup = function addGroup(value, id, active, disabled) {
        return {
          type: _constants.ACTION_TYPES.ADD_GROUP,
          value: value,
          id: id,
          active: active,
          disabled: disabled
        };
      };

      exports.addGroup = addGroup;
      /***/
    },
    /* 31 */

    /***/
    function (module, exports, __webpack_require__) {
      "use strict";

      Object.defineProperty(exports, "__esModule", {
        value: true
      });
      exports.resetTo = exports.clearAll = void 0;

      var clearAll = function clearAll() {
        return {
          type: 'CLEAR_ALL'
        };
      };

      exports.clearAll = clearAll;

      var resetTo = function resetTo(state) {
        return {
          type: 'RESET_TO',
          state: state
        };
      };

      exports.resetTo = resetTo;
      /***/
    },
    /* 32 */

    /***/
    function (module, exports, __webpack_require__) {
      "use strict";

      Object.defineProperty(exports, "__esModule", {
        value: true
      });
      exports.setIsLoading = void 0;
      /* eslint-disable import/prefer-default-export */

      var setIsLoading = function setIsLoading(isLoading) {
        return {
          type: 'SET_IS_LOADING',
          isLoading: isLoading
        };
      };

      exports.setIsLoading = setIsLoading;
      /***/
    }])
  );
});
//# sourceMappingURL=pwtimage.js.map
