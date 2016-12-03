(function(){

// module factory: start

var moduleFactory = function($) {
// module body: start

var module = this; 
var exports = function() { 


   var withinBox = function(x, y, left, top, width, height ){
        return (y >= top &&
                y <  top + height &&
                x >= left &&
                x <  left + width);
    }
/**
 * @function within
 * @parent dom
 * @plugin jquery/dom/within
 *
 * Returns the elements are within the position.
 *
 *     // get all elements that touch 200x200.
 *     $('*').within(200, 200);
 *
 * @param {Number} left the position from the left of the page
 * @param {Number} top the position from the top of the page
 * @param {Boolean} [useOffsetCache] cache the dimensions and offset of the elements.
 * @return {jQuery} a jQuery collection of elements whos area
 * overlaps the element position.
 */
$.fn.within= function(left, top, useOffsetCache) {
    var ret = []
    this.each(function(){
        var q = $(this);

        if (this == document.documentElement) {
			return ret.push(this);
		}
        var offset = useOffsetCache ?
						$.data(this,"offsetCache") || $.data(this,"offsetCache", q.offset()) :
						q.offset();

        var res =  withinBox(left, top,  offset.left, offset.top,
                                    this.offsetWidth, this.offsetHeight );

        if (res) {
			ret.push(this);
		}
    });

    return this.pushStack( $.unique( ret ), "within", left+","+top );
}


/**
 * @function withinBox
 * @parent jQuery.fn.within
 * returns if elements are within the box
 * @param {Object} left
 * @param {Object} top
 * @param {Object} width
 * @param {Object} height
 * @param {Object} cache
 */
$.fn.withinBox = function(left, top, width, height, cache){
  	var ret = []
    this.each(function(){
        var q = $(this);

        if(this == document.documentElement) return  this.ret.push(this);

        var offset = cache ?
			$.data(this,"offset") ||
			$.data(this,"offset", q.offset()) :
			q.offset();


        var ew = q.width(), eh = q.height();

		res =  !( (offset.top > top+height) || (offset.top +eh < top) || (offset.left > left+width ) || (offset.left+ew < left));

        if(res)
            ret.push(this);
    });
    return this.pushStack( $.unique( ret ), "withinBox", $.makeArray(arguments).join(",") );
}


}; 

exports(); 
module.resolveWith(exports); 

// module body: end

}; 
// module factory: end

dispatch("mvc/dom.within")
.containing(moduleFactory)
.to("Foundry/2.1 Modules");

}());
