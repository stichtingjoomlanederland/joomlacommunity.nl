(function(){

// module factory: start

var moduleFactory = function($) {
// module body: start

var module = this; 
var exports = function() { 

/**
* jquery.Image
* Image helper for jQuery.
* https://github.com/jstonne/jquery.Image
*
* Copyright (c) 2012 Jensen Tonne
* www.jstonne.com
*
* Dual licensed under the MIT and GPL licenses:
* http://www.opensource.org/licenses/mit-license.php
* http://www.gnu.org/licenses/gpl.html
*
*/

$.fn.image = function(method) {
	var method = $.Image[method];
	return method && method.apply(this[0], $.makeArray(arguments).slice(1));
}

$.Image = {

	get: function(url) {

		var existingImage = this.nodeName==="IMG";

		var image = $(existingImage ? this : new Image()),
			imageLoader = $.Deferred();

		image
			.load(function() {

				var w, h, r, o;

				if (!existingImage) { image.appendTo("body"); }

				image
					.css({
						position: "absolute",
						left: "-99999px"
					})
					.data({
						width: w = image.width(),
						height: h = image.height(),
						aspectRatio: r = w / h,
						orientation: o = (r===1) ? "square" : (r<1) ? "tall" : "wide"
					})
					.addClass("orientation-" + o)
					.removeAttr("style");

				if (!existingImage) {
					image.detach();
				}

				imageLoader.resolve(image);
			})
			.error(function(){

				imageLoader.reject();
			})
			.attr("src", url);

		return imageLoader;
	},

	resizeWithin: function(sourceWidth, sourceHeight, maxWidth, maxHeight) {

		var targetWidth = sourceWidth,
			targetHeight = sourceHeight;

		// Resize the width first
		var ratio = maxWidth / sourceWidth;

		targetWidth  = sourceWidth  * ratio;
		targetHeight = sourceHeight * ratio;

		if (targetHeight > maxHeight)
		{
			var ratio = maxHeight / sourceHeight;

			targetWidth  = sourceWidth  * ratio;
			targetHeight = sourceHeight * ratio;
		}

		return {
			width: targetWidth,
			height: targetHeight
		};
	},

	resizeToFill: function(sourceWidth, sourceHeight, maxWidth, maxHeight) {

		var targetWidth = sourceWidth,
			targetHeight = sourceHeight;

		var ratio = maxWidth / sourceWidth;

		targetWidth  = sourceWidth  * ratio;
		targetHeight = sourceHeight * ratio;

		if (targetHeight < maxHeight) {

			var ratio = maxHeight / sourceHeight;

			targetWidth  = sourceWidth  * ratio;
			targetHeight = sourceHeight * ratio;
		}

		return {
			top: (maxHeight - targetHeight) / 2,
			left: (maxWidth - targetWidth) / 2,
			width: targetWidth,
			height: targetHeight
		};
	}
};

}; 

exports(); 
module.resolveWith(exports); 

// module body: end

}; 
// module factory: end

dispatch("image")
.containing(moduleFactory)
.to("Foundry/2.1 Modules");

}());