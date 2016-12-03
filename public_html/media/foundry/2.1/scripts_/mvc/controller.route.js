(function(){

// module factory: start

var moduleFactory = function($) {
// module body: start

var module = this; 
$.require() 
 .script("mvc/dom.route","mvc/controller") 
 .done(function() { 
var exports = function() { 


	/**
	 *
	 *     ":type route" //
	 *
	 * @param {Object} el
	 * @param {Object} event
	 * @param {Object} selector
	 * @param {Object} cb
	 */
	$.Controller.processors.route = function(el, event, selector, funcName, controller){
		$.route(selector||"")
		var batchNum;
		var check = function(ev, attr, how){
			if($.route.attr('route') === (selector||"") &&
			 (ev.batchNum === undefined || ev.batchNum !== batchNum ) ){

				batchNum = ev.batchNum;

				var d = $.route.attrs();
				delete d.route;

				controller[funcName](d)
			}
		}
		$.route.bind('change',check);
		return function(){
			$.route.unbind('change',check)
		}
	}

}; 

exports(); 
module.resolveWith(exports); 

}); 
// module body: end

}; 
// module factory: end

dispatch("mvc/controller.route")
.containing(moduleFactory)
.to("Foundry/2.1 Modules");

}());
