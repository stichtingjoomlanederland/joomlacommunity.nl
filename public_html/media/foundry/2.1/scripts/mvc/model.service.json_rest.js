!function(){var moduleFactory=function($){var module=this;$.require().script("mvc/model.service").done(function(){var exports=function(){$.Model.service.jsonRest=$.Model.service({url:"",type:".json",name:"",getSingularUrl:function(Class,id){return this.singularUrl?this.singularUrl+"/"+id+this.type:this.url+this.getName(Class)+"s/"+id+this.type},getPluralUrl:function(Class){return this.pluralUrl||this.url+this.getName(Class)+"s"+this.type},getName:function(Class){return this.name||Class.name},findAll:function(params){var plural=this._service.getPluralUrl(this);
$.ajax({url:plural,type:"get",dataType:"json",data:params,success:this.proxy(["wrapMany",success]),error:error,fixture:!0})},getParams:function(attrs){var name=this.getName(this),params={};for(var n in attrs)params[name+"["+n+"]"]=attrs[n];return params},update:function(id,attrs,success,error){var params=this._service.getParams(attrs),singular=this._service.getSingularUrl(this,id),plural=this._service.getPluralUrl(this),self=this;$.ajax({url:singular,type:"put",dataType:"text",data:params,complete:function(xhr,status){return/\w+/.test(xhr.responseText)?error(eval("("+xhr.responseText+")")):(success({}),void 0)
},fixture:"-restUpdate"})},destroy:function(id,success,error){var singular=this._service.getSingularUrl(this,id);$.ajax({url:singular,type:"delete",dataType:"text",success:success,error:error,fixture:"-restDestroy"})},create:function(attrs,success,error){var params=this._service.getParams(attrs),plural=this._service.getPluralUrl(this),self=this,name=this._service.getName(this);$.ajax({url:plural,type:"post",dataType:"text",complete:function(xhr,status){if("success"!=status&&error(xhr,status),/\w+/.test(xhr.responseText)){var res=eval("("+xhr.responseText+")");
return res[name]?(success(res[name]),void 0):error(res)}var loc=xhr.responseText;try{loc=xhr.getResponseHeader("location")}catch(e){}if(loc){var mtcs=loc.match(/\/[^\/]*?(\w+)?$/);if(mtcs)return success({id:parseInt(mtcs[1])})}success({})},data:params,fixture:"-restCreate"})}})};exports(),module.resolveWith(exports)})};dispatch("mvc/model.service.json_rest").containing(moduleFactory).to("Foundry/2.1 Modules")}();