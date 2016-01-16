(function(){

// module factory: start

var moduleFactory = function($) {
// module body: start

var module = this; 
var exports = function() { 

/*!
 * jquery.checked.
 * Checked/unchecked event handler for checkbox & radio button.
 *
 * Copyright (c) 2012 Jensen Tonne
 * www.jstonne.com
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

$.fn.checked = function(checked, unchecked)
{
	// Return checked value if no arguments are given;
	if (arguments.length < 1)
		return this.is(':checked');

	this.each(function(i)
	{
		var input = $(this);

		if (typeof checked == "boolean")
		{
			input.attr('checked', checked).prop('checked', checked).trigger('change');
			return;
		}

		if (input.is('input[type=checkbox]') || input.is('input[type=radio]'))
		{
			input
				.unbind('change.checked')
				.bind('change.checked', function()
				{
					try {
						return (input.is(':checked')) ? checked.apply(input) : unchecked.apply(input);
					} catch(e) {};
				});
		}
	});

	return this;
}

}; 

exports(); 
module.resolveWith(exports); 

// module body: end

}; 
// module factory: end

FD31.module("checked", moduleFactory);

}());