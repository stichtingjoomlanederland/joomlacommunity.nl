(function(){

// module factory: start

var moduleFactory = function($) {
// module body: start

var module = this; 
var exports = function() { 

/**
 * jquery.bookmarklet
 * Generates social bookmarks that doesn't slow down initial page load.
 *
 * Copyright (c) 2012 Jensen Tonne
 * www.jstonne.com
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

$.bookmarklet = function(elem, type, options, callback) {
	var node = this[type].call($(elem), options);

    // On IE9, addEventListener() does not necessary fire the onload event
    // after the script is loaded, therefore we use the attachEvent() method,
    // as it behaves correctly.
    if (node.attachEvent && !$.browser.opera)
    {
        node.attachEvent("onreadystatechange", callback);
    } else {
        node.addEventListener("load", callback, false);
    }
};

$.fn.bookmarklet = function(type, options, callback) {
	var node = this,
		type = type,
		options = options,
		callback = callback;

	$(document).ready(function(){
		$.bookmarklet[type].call(node, options, callback);
	});
};

$.bookmarklet.tweetMeme = function(options) {
	var node = this[0],
		parent = node.parentNode,
		iframe = document.createElement("iframe")

	options.url = options.url.replace(/\+/g, "%2b");

    switch (options.style) {
	    case "compact":
	        var h = 20;
	        var w = 90;
	        break;
	    default:
	        var h = 61;
	        var w = 50;
	        break
    }

    var src = "http://api.tweetmeme.com/button.js?" + $.param(options);

    if (document && document.referrer) {
        var ref = document.referrer;
        if (ref) {
            src += "&o=" + escape(ref)
        }
    }

	parent.insertBefore(iframe, node);
	parent.removeChild(node);

    $(iframe).attr({
    	src: src,
    	width: w,
    	height: h,
    	frameborder: 0,
    	scrolling: "no"
    });

    return iframe;
};

$.bookmarklet.linkedIn = function(options) {
	var node = this[0],
		parent = node.parentNode,
		config = document.createElement("script"),
		script = document.createElement("script");

	$(config)
		.attr({
			"type": "in/share",
			"data-url": options.url,
			"data-counter": options.counter
		});

	parent.insertBefore(config, node);
	parent.insertBefore(script, node);
	parent.removeChild(node);

	$(script)
		.attr({
			type: "text/javascript",
			src: "https://platform.linkedin.com/in.js"
		});

	return script;
};

$.bookmarklet.digg = function(options) {
	var node = this[0],
		parent = node.parentNode,
		button = document.createElement("a"),
		script = document.createElement("script");

	$(button)
		.addClass("DiggThisButton")
		.addClass(options["classname"])
		.attr({
			href: "https://digg.com/submit?url=" + options.url + "&title=" + options.title
		});

	parent.insertBefore(button, node);
	parent.insertBefore(script, node);
	parent.removeChild(node);

	$(script)
		.attr({
			type: "text/javascript",
			async: "true",
			src: "http://widgets.digg.com/buttons.js"
		});

	return script;
};

$.bookmarklet.stumbleUpon = function(options) {
	var node = this[0],
		parent = node.parentNode,
		button = document.createElement("su:badge"),
		script = document.createElement("script");

	$(button)
		.attr({
			layout: options.layout,
			location: options.url
		});

	parent.insertBefore(button, node);
	parent.insertBefore(script, node);
	parent.removeChild(node);

	$(script)
		.attr({
			type: "text/javascript",
			src: "https://platform.stumbleupon.com/1/widgets.js"
		});

	return script;
};

$.bookmarklet.twitter = function(options) {
	var node = this[0],
		parent = node.parentNode,
		button = document.createElement("a"),
		script = document.createElement("script");

	$(button)
		.attr({
			"class": "twitter-share-button",
			"href": "https://twitter.com/share",
			"data-url": options.url,
			"data-counturl": options.url,
			"data-count": options.count,
			"data-via": options.via,
			"data-text": options.text
		})
		.html("Tweet");

	parent.insertBefore(button, node);
	parent.insertBefore(script, node);
	parent.removeChild(node);

	$(script)
		.attr({
			type: "text/javascript",
			src: "https://platform.twitter.com/widgets.js"
		});

	return script;
};

var hasPlusOne,
	installPlusOne;

$.bookmarklet.googlePlusOne = function(options) {
	var node = this[0],
		parent = node.parentNode,
		button = document.createElement("g:plusone");

	$(button)
		.attr({
			size: options.size,
			href: options.href
		});

	parent.insertBefore(button, node);
	parent.removeChild(node);

	// TODO: Check if gapi.plusone already exist (loaded by 3PD);

	if (!hasPlusOne) {

		clearTimeout(installPlusOne);

		installPlusOne = setTimeout(function(){

			var head = document.getElementsByTagName("head")[0],
				script = document.createElement("script");

				head.appendChild(script);
				script.type = "text/javascript";
				script.src = "//apis.google.com/js/plusone.js";

			hasPlusOne = true;

		}, 1000);

	} else if (gapi && gapi.plusone) {

		gapi.plusone.go(parent);
	}

	return node;
};

var hasFBSDK,
	FBInited,
	parseXFBMLTask,
	parseXFBML = function() {

		// Collect all the FB like calls first
		clearTimeout(parseXFBMLTask);

		parseXFBMLTask = setTimeout(function(){

			// Then finally parse it.
			try {
				FB.XFBML.parse();
			} catch(e) {};

		}, 1000);
	};

$.bookmarklet.facebookLike = function(options) {

	var node = this[0],
		parent = node.parentNode,
		button = document.createElement("fb:like");

	$(button)
		.attr({
			"class": "fb-like",
			"data-href": options.url,
			"data-send": options.send,
			"data-layout": options.layout,
			"data-action": options.verb,
			"data-locale": options.locale,
			"data-colorscheme": options.theme,
			"data-show-faces": options.faces,
			"data-width" : options.width
		})
		.css({
			height: options.height,
			width: options.width
		});

	parent.insertBefore(button, node);
	parent.removeChild(node);

	// If FBSDK isn't loaded, load it,
	// the social buttons will be parsed by itself.
	if (!window.FB) {

		if (!document.getElementById("fb-root")) {
			$("<div id='fb-root'></div>").prependTo("body");
		}

		var jssdk = document.getElementById("facebook-jssdk");

		// No JSSDK
		if (!jssdk) {

			var head = document.getElementsByTagName("head")[0],
				script = document.createElement("script");

				head.appendChild(script);
				script.id = "facebook-jssdk";
				script.src = "//connect.facebook.net/" + options.locale + "/all.js#xfbml=1";

		// Has JSSDK, but no XFBML support.
		} else if (!FBInited) {

			if (!/xfbml/.test(jssdk.src)) {

				var _fbAsyncInit = window.fbAsyncInit;

				window.fbAsyncInit = function(){

					if ($.isFunction(_fbAsyncInit)) _fbAsyncInit();

					parseXFBML();
				}
			}

			FBInited = true;
		}

	// If FBSDK is already loaded
	} else {

		parseXFBML();
	}

	return node;
};

$.bookmarklet.pinterest = function(options) {
	var node = this[0],
		parent = node.parentNode,
		button = document.createElement("a"),
		script = document.createElement("script");

	$(button)
		.attr({
			"class": "pin-it-button",
			"href": "http://pinterest.com/pin/create/button/?url=" + options.url + "&media=" + options.media + "&description=" + options.title,
			"count-layout": options.style
		})
		.html("Pin It");

	parent.insertBefore(button, node);
	parent.insertBefore(script, node);
	parent.removeChild(node);

	$(script)
		.attr({
			type: "text/javascript",
			src: "https://assets.pinterest.com/js/pinit.js"
		});

	return script;
};
}; 

exports(); 
module.resolveWith(exports); 

// module body: end

}; 
// module factory: end

FD31.module("bookmarklet", moduleFactory);

}());