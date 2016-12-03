(function(){

// module factory: start

var moduleFactory = function($) {
// module body: start

var module = this; 
var exports = function() { 

/**
 * jquery.autogrow
 * Expands textarea as your type.
 *
 * Copyright (c) 2012 Jensen Tonne
 * www.jstonne.com
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

$.fn.autogrow = function(options) {

	// options: minHeight, maxHeight, lineHeight, lineBleed (multiplier of lineHeight)

    this.filter('textarea').each(function(){

		var textarea = $(this);

		if (textarea.hasClass('shadow')) return;

		textarea.unbind('focus.autogrow blur.autogrow keyup.autogrow keypress.autogrow autogrow');

		var o = options || {},
			shadow = textarea.data('shadow'),
			offset = textarea.outerHeight() - textarea.innerHeight();

		// Always rebuild shadow
		if (shadow) shadow.remove();

		shadow =
			textarea
				.clone()
				.unbind()

				// So that it doesn't affect the form submission value
				// where this textarea belongs to.
				//
				// (Note: id attribute remains intact)
				.removeAttr('name')

				.addClass('shadow')
				.css(
				{
					visibility: 'hidden',
					height: 0,
					minHeight: 0,
					margin: 0
				});

		if (textarea.css('position')!=='absolute')
		{
			shadow.css(
			{
				// Absolute positioned shadow has a width
				// that is a sum of textarea's width + padding width.
				// Hence, it cannot be used.
				//
				// 'position'   : 'absolute',
				position: 'relative',

				// Relative positioned shadow will occupy
				// vertical space betlow the original textarea.
				//
				// So we need to remove its vertical border width & margin width.
				borderTop: 'none',
				borderBottom: 'none',
				marginTop: 0,

				// Then, we need to offset its vertical padding.
				marginBottom: -1 * (parseInt(textarea.css('paddingTop')) + parseInt(textarea.css('paddingBottom')))
			});
		}

		shadow.insertAfter(textarea);

		textarea
			.data('shadow', shadow)
			.bind('focus.autogrow blur.autogrow keyup.autogrow keypress.autogrow autogrow', autogrow);

		// The most accurate way of determining the lineHeight is not by its CSS property,
		// but by adding a blank character on the shadow textarea, and then retrieve the scrollHeight.
		if (o.lineHeight==undefined) o.lineHeight = shadow.val(' ')[0].scrollHeight;
		if (o.minHeight==undefined) o.minHeight = textarea.height();
		if (o.maxHeight==undefined) o.maxHeight = 0;
		if (o.lineBleed==undefined) o.lineBleed = 0;

		function autogrow()
		{
			shadow.val(textarea.val());

			// IE bug
			shadow[0].scrollHeight;

			var height = shadow[0].scrollHeight;

			if (height > o.maxHeight && o.maxHeight > 0)
			{
				height = o.maxHeight;
				textarea.css('overflow', 'auto');
			} else {
				height = (height < o.minHeight) ? o.minHeight : height;
				textarea.css('overflow', 'hidden');
			}

			textarea.height(height + (o.lineHeight * o.lineBleed));
		}

        autogrow();
    });

    return this;
}


}; 

exports(); 
module.resolveWith(exports); 

// module body: end

}; 
// module factory: end

dispatch("autogrow")
.containing(moduleFactory)
.to("Foundry/2.1 Modules");

}());