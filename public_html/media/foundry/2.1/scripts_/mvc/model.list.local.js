(function(){

// module factory: start

var moduleFactory = function($) {
// module body: start

var module = this; 
$.require() 
 .script("mvc/dom.cookie","mvc/model.list") 
 .done(function() { 
var exports = function() { 


/**
 * @class jQuery.Model.List.Local
 * @plugin jquery/model/list/local
 * @download  http://jmvcsite.heroku.com/pluginify?plugins[]=jquery/model/list/local/local.js
 * @parent jQuery.Model.List
 * Works exactly the same as [jQuery.Model.List.Cookie] except uses
 * a local store instead of cookies.
 */
$.Model.List($.globalNamespace + ".Model.List.Local",
{
	retrieve : function(name){
		// each also needs what they are referencd by ?
		var props = window.localStorage[ name ] || "[]",
			instances = [],
			Class = props.type ? $.String.getObject(props.type) :  null;
		for(var i =0; i < props.ids.length;i++){
			var identity = props.ids[i],
				instanceData = window.localStorage[ identity ];
			instances.push( new Class(instanceData) )
		}
		this.push.apply(this,instances);
		return this;
	},
	store : function(name){
		//  go through and listen to instance updating
		var ids = [], days = this.days;
		this.each(function(i, inst){
			window.localStorage[inst.identity()] = instance.attrs();
			ids.push(inst.identity());
		});
		window.localStorage[name] = {
			type: this[0] && this[0].constructor.fullName,
			ids: ids
		};
		return this;
	}

});


}; 

exports(); 
module.resolveWith(exports); 

}); 
// module body: end

}; 
// module factory: end

dispatch("mvc/model.list.local")
.containing(moduleFactory)
.to("Foundry/2.1 Modules");

}());
