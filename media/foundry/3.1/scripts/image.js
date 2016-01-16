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

				var data = {
					width: w = image.width(),
					height: h = image.height(),
					aspectRatio: r = w / h,
					orientation: o = (r===1) ? "square" : (r<1) ? "tall" : "wide"
				}	

				image
					.css({
						position: "absolute",
						left: "-99999px"
					})
					.data(data)
					.addClass("orientation-" + o)
					.removeAttr("style");

				if (!existingImage) {
					image.detach();
				}

				imageLoader.resolve(image, data);
			})
			.error(function(){

				imageLoader.reject();
			})
			.attr("src", url);

		return imageLoader;
	},

	aspectRatio: function(width, height) {

		// Normalize values
		if ($.isPlainObject(width)) {
			width  = width.width;
			height = width.height;
		}

		return width / height;
	},

	orientation: function(width, height) {

		// Normalize values
		if ($.isPlainObject(width)) {
			width  = width.width;
			height = width.height;
		}

		if (width===height) return "square";

		if (width > height) return "wide";

		return "tall";
	},

	resizeProportionate: function(sourceWidth, sourceHeight, maxWidth, maxHeight, mode) {

		var targetWidth = sourceWidth,
			targetHeight = sourceHeight;

		// Resize the width first
		var ratio        = maxWidth / sourceWidth;
			targetWidth  = sourceWidth  * ratio;
			targetHeight = sourceHeight * ratio;


		// inner resize (default)
		var condition = targetHeight > maxHeight;

		// outer resize
		if (mode=="outer") {
			condition = targetHeight < maxHeight;
		}

		if (condition) {
			ratio        = maxHeight / sourceHeight;
			targetWidth  = sourceWidth  * ratio;
			targetHeight = sourceHeight * ratio;
		}

		return {
			top   : (maxHeight - targetHeight) / 2,
			left  : (maxWidth - targetWidth) / 2,
			width : targetWidth,
			height: targetHeight
		};
	},

	resizeWithin: function(sourceWidth, sourceHeight, maxWidth, maxHeight) {

		return $.Image.resizeProportionate(
			sourceWidth,
			sourceHeight,
			maxWidth,
			maxHeight,
			"inner"
		);
	},

	resizeToFill: function(sourceWidth, sourceHeight, maxWidth, maxHeight) {

		return $.Image.resizeProportionate(
			sourceWidth,
			sourceHeight,
			maxWidth,
			maxHeight,
			"outer"
		);
	}
};

}; 

exports(); 
module.resolveWith(exports); 

// module body: end

}; 
// module factory: end

FD31.module("image", moduleFactory);

}());