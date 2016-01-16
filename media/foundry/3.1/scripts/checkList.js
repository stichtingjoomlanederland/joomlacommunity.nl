(function(){

// module factory: start

var moduleFactory = function($) {
// module body: start

var module = this; 
var exports = function() { 

/**
 * jquery.checkList.
 * Multiple checkbox handler.
 *
 * Copyright (c) 2012 Jensen Tonne
 * www.jstonne.com
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 * $(e).checkList({
 *    check  : function(){},   // this refers to the input being checked
 *    uncheck: function(){},   // this refers to the input being unchecked
 *
 *    // returns checked elements & unchecked elements in separate arguments
 *    change : function(checked, unchecked){}
 * })
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
}

}; 

exports(); 
module.resolveWith(exports); 

// module body: end

}; 
// module factory: end

FD31.module("checkList", moduleFactory);

}());