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
 * jQuery UI Effects Highlight 1.9.0pre
 * http://jqueryui.com
 *
 * Copyright 2012 jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 *
 * http://api.jqueryui.com/highlight-effect/
 *
 * Depends:
 *	jquery.ui.effect.js
 */
(function( $, undefined ) {

$.effects.effect.highlight = function( o, done ) {
	var elem = $( this ),
		props = [ "backgroundImage", "backgroundColor", "opacity" ],
		mode = $.effects.setMode( elem, o.mode || "show" ),
		animation = {
			backgroundColor: elem.css( "backgroundColor" )
		};

	if (mode === "hide") {
		animation.opacity = 0;
	}

	$.effects.save( elem, props );
	
	elem
		.show()
		.css({
			backgroundImage: "none",
			backgroundColor: o.color || "#ffff99"
		})
		.animate( animation, {
			queue: false,
			duration: o.duration,
			easing: o.easing,
			complete: function() {
				if ( mode === "hide" ) {
					elem.hide();
				}
				$.effects.restore( elem, props );
				done();
			}
		});
};

})(jQuery);

}; 

exports(); 
module.resolveWith(exports); 

}); 
// module body: end

}; 
// module factory: end

dispatch("ui/effect-highlight")
.containing(moduleFactory)
.to("Foundry/2.1 Modules");

}());