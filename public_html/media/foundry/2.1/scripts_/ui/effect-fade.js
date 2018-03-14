(function(){

// module factory: start

var moduleFactory = function($) {
// module body: start

var module = this; 
var jQuery = $; 
$.require() 
 .script("ui/effect") 
 .done(function() { 
var exports = function() { 

/*!
 * jQuery UI Effects Fade 1.9.0pre
 * http://jqueryui.com
 *
 * Copyright 2012 jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 *
 * http://api.jqueryui.com/fade-effect/
 *
 * Depends:
 *	jquery.ui.effect.js
 */
(function( $, undefined ) {

$.effects.effect.fade = function( o, done ) {
	var el = $( this ),
		mode = $.effects.setMode( el, o.mode || "toggle" );

	el.animate({
		opacity: mode
	}, {
		queue: false,
		duration: o.duration,
		easing: o.easing,
		complete: done
	});
};

})( jQuery );

}; 

exports(); 
module.resolveWith(exports); 

}); 
// module body: end

}; 
// module factory: end

dispatch("ui/effect-fade")
.containing(moduleFactory)
.to("Foundry/2.1 Modules");

}());