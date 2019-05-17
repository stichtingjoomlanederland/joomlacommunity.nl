/* jce - 2.7.13 | 2019-05-07 | https://www.joomlacontenteditor.net | Copyright (C) 2006 - 2019 Ryan Demmer. All rights reserved | GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html */
!function($,Wf){function uid(){var i,guid=(new Date).getTime().toString(32);for(i=0;i<5;i++)guid+=Math.floor(65535*Math.random()).toString(32);return"wf_"+guid+(counter++).toString(32)}function getRatio(o){function gcd(a,b){return 0==b?a:gcd(b,a%b)}var r=gcd(o.width,o.height);return o.width/r/(o.height/r)}function debounce(func,wait,immediate){var timeout;return function(){var context=this,args=arguments,later=function(){timeout=null,immediate||func.apply(context,args)},callNow=immediate&&!timeout;clearTimeout(timeout),timeout=setTimeout(later,wait),callNow&&func.apply(context,args)}}var $tmp=document.createElement("div"),prefixes=["-ms-","-moz-","-webkit-","-o-",""];$.support.Blob=!!window.Blob;var canvas=document.createElement("canvas");try{$.support.webgl=!(!canvas.getContext("webgl")&&!canvas.getContext("experimental-webgl"))}catch(e){$.support.webgl=!1}$.support.filter=function(){return!document.documentMode&&!window.opera&&($tmp.style.cssText=prefixes.join("filter:grayscale(1); "),!!$tmp.style.length)}(),$.fn.cssFilter=function(o){var prefixes=["","-moz-","-webkit-","-ms-",""],matrix=[],filter=o.filter||"",amount=o.amount||1;if(!filter)return this;switch(filter){case"desaturate":case"saturate":amount=(100+amount)/100,matrix[0]=.213+.787*amount,matrix[1]=.715-.715*amount,matrix[2]=1-(matrix[0]+matrix[1]),matrix[3]=matrix[4]=0,matrix[5]=.213-.213*amount,matrix[6]=.715+.285*amount,matrix[7]=1-(matrix[5]+matrix[6]),matrix[8]=matrix[9]=0,matrix[10]=.213-.213*amount,matrix[11]=.715-.715*amount,matrix[12]=1-(matrix[10]+matrix[11]),matrix[13]=matrix[14]=0,matrix[15]=matrix[16]=matrix[17]=matrix[19]=0,matrix[18]=1,filter="saturate";break;case"contrast":case"invert":case"grayscale":case"sepia":break;case"brightness":amount/=100,filter="brightness";break;case"blur":amount=amount/100+"px";break;case"sharpen":break;case"gamma":break;case"hue":amount+="deg",filter="hue-rotate"}return $(this).attr("style",prefixes.join("filter:"+filter+"("+amount+"); ")),this},$.fn.cssTransform=function(transform,amount){var keys=["transform","msTransform","Transform","WebkitTransform","OTransform"];return this.each(function(){var n=$(this).get(0);switch(transform){case"flip":transform="horizontal"==amount?"scaleX":"scaleY",amount=-1;break;case"rotate":amount+="deg"}$.each(keys,function(o,s){var transforms=n.style[s]||[];transforms.length&&(transforms=transforms.split(" ")),transforms.push(transform+"("+amount+")"),n.style[s]=transforms.join(" ")})}),this};var counter=0,EditorDialog={stack:[],fxstack:[],settings:{resize_quality:80,save:$.noop},_setLoader:function(){$('<div class="loading" />').appendTo("#editor-image"),$("canvas","#editor-image");this.working=!0},_removeLoader:function(){$("div.loading","#editor-image").remove(),this.working=!1},init:function(options){var self=this;if(Wf.init(options),$("#editor").removeClass("offleft"),$(window).bind("resize orientationchange",function(){self._resizeWin()}),this.src=tinyMCEPopup.getWindowArg("url"),!this._validatePath(this.src))return Wf.Modal.alert("Invalid image file"),!1;$.extend(this.settings,{width:tinyMCEPopup.getWindowArg("width"),height:tinyMCEPopup.getWindowArg("height"),save:tinyMCEPopup.getWindowArg("save")}),this._setLoader();var img="<img />";window.ActiveXObject&&(img=new Image),$(img).attr("src",this._loadImage(this.src)).one("load",function(){var n=this;$(n).data("width",n.width).data("height",n.height).appendTo("#editor-image"),$(n).canvas({width:n.width,height:n.height,onfilterprogress:function(e,n){1==n&&self._removeLoader()}});var canvas=$(n).canvas("getCanvas");$(canvas).insertAfter(n),self.position(),self._createToolBox(),self._createFX(),self._removeLoader()}).hide(),$("#transform_tab").accordion().on("accordion.activate",function(e,tab,panel){var action=$(tab).data("action");self.reset(!0),action&&self._initTransform(action)}).children(".uk-accordion-title").first().click,$("#tabs").tabs().on("tabs.activate",function(){self.reset(!0),self._resetFX()}),$("button.save").click(function(e){self.save(),e.preventDefault()}).prop("disabled",!0),$("button.revert").click(function(e){self.revert(e),e.preventDefault()}).prop("disabled",!0),$("button.undo").click(function(e){e.preventDefault(),self.undo(e)}).prop("disabled",!0),$("button.apply","#editor").click(function(e){e.preventDefault(),self._applyTransform($(this).data("function"))}),$("button.reset","#transform_tab").click(function(e){e.preventDefault(),self._resetTransform($(this).data("function"))}),$("#effects_apply").click(function(e){e.preventDefault(),self._resetFX(),$("img","#editor-image").canvas("update"),self.stack=[],$("button.undo").prop("disabled",!0)}),$("#effects_reset").click(function(e){e.preventDefault(),self._resetFX(),self.revert(e)})},_createToolBox:function(){var self=this,$img=$("img","#editor-image"),canvas=$img.canvas("getCanvas"),iw=canvas.width,ih=canvas.height;$(canvas).width(),$(canvas).height();$("#crop_presets option, #resize_presets option").each(function(){var v=$(this).val();if(v&&/[0-9]+x[0-9]+/.test(v)){v=v.split("x");var w=parseFloat(v[0]),h=parseFloat(v[1]);w>=$img.data("width")&&h>=$img.data("height")&&$(this).remove()}}),$("#resize_presets").change(function(){var v=($img.get(0),$(this).val());if(v){if(v.indexOf(":")!=-1){var r=v.split(":"),r1=parseInt($.trim(r[0])),r2=parseInt($.trim(r[1])),ratio=r1/r2;r2>r1&&(ratio=r2/r1),iw>ih&&r2>r1&&(ratio=r2/r1),w=Math.round(iw/ratio),h=Math.round(ih/ratio)}else{v=v.split("x");var w=parseFloat($.trim(v[0])),h=parseFloat($.trim(v[1]))}$("#resize_width").val(w).data("tmp",w),$("#resize_height").val(h).data("tmp",h);var ratio=!!$("#resize_constrain").prop("checked")&&w/h;$(canvas).resize("setRatio",ratio),$(canvas).resize("setSize",w,h)}}),$("option","#resize_presets").first().text(function(i,txt){return""+iw+" x "+ih+" ("+txt+")"}),$("#resize_width").val(iw).data("tmp",iw).change(function(){var w=$(this).val(),$height=$("#resize_height");if(w=w||$(this).data("tmp"),$("#resize_constrain").prop("checked")){var tw=$(this).data("tmp"),h=$height.val(),temp=(h/tw*w).toFixed(0);$height.val(temp).data("tmp",temp)}$(this).data("tmp",w),$(canvas).resize("setSize",w,$height.val())}),$("#resize_height").val(ih).data("tmp",ih).change(function(){var h=$(this).val(),$width=$("#resize_width");if(h=h||$(this).data("tmp"),$("#resize_constrain").prop("checked")){var th=$(this).data("tmp"),w=$width.val(),temp=(w/th*h).toFixed(0);$width.val(temp).data("tmp",temp)}$(this).data("tmp",h),$(canvas).resize("setSize",$width.val(),h)}),$("#resize_constrain").click(function(){var ratio=!!this.checked&&{width:$("#resize_width").val(),height:$("#resize_height").val()};$(canvas).resize("setConstrain",ratio)}),$("#crop_width").val(iw).data("tmp",iw).change(function(){var w=$(this).val(),h=$("#crop_height").val(),x=$("#crop_x").val(),y=$("#crop_y").val(),s={width:w,height:h,x:x,y:y},ratio=s.width/s.height;$("#crop_constrain").is(":checked")&&$(canvas).crop("setRatio",ratio),$("#crop_presets").val(w+"x"+h),$(canvas).crop("setArea",s)}),$("#crop_height").val(ih).data("tmp",ih).change(function(){var h=$(this).val(),w=$("#crop_width").val(),x=$("#crop_x").val(),y=$("#crop_y").val(),s={width:w,height:h,x:x,y:y},ratio=s.width/s.height;$("#crop_constrain").is(":checked")&&$(canvas).crop("setRatio",ratio),$("#crop_presets").val(w+"x"+h),$(canvas).crop("setArea",s)}),$("#crop_x").val(0).change(function(){var x=$(this).val(),w=$("#crop_width").val(),h=$("#crop_height").val(),y=$("#crop_y").val(),s={width:w,height:h,x:x,y:y},ratio=s.width/s.height;$("#crop_constrain").is(":checked")&&$(canvas).crop("setRatio",ratio),$(canvas).crop("setArea",s)}),$("#crop_y").val(0).change(function(){var y=$(this).val(),w=$("#crop_width").val(),h=$("#crop_height").val(),x=$("#crop_x").val(),s={width:w,height:h,x:x,y:y},ratio=s.width/s.height;$("#crop_constrain").is(":checked")&&$(canvas).crop("setRatio",ratio),$(canvas).crop("setArea",s)}),$("#crop_constrain").click(function(){$(this).toggleClass("checked"),$(this).is(":checked")?$("#crop_presets").change():$(canvas).crop("setConstrain",!1)}),$("#crop_presets").change(function(){var img=$img.get(0),v=$(this).val();if(v){var s={width:img.width,height:img.height};if($.extend(s,$(canvas).crop("getArea")),v.indexOf(":")!=-1){var r=v.split(":"),r1=parseInt($.trim(r[0])),r2=parseInt($.trim(r[1])),ratio=r1/r2;r2>r1&&(ratio=r2/r1),s.width>s.height?(r2>r1&&(ratio=r2/r1),s.height=Math.round(s.width/ratio)):s.width=Math.round(s.height/ratio)}else{v=v.split("x"),s.width=parseInt($.trim(v[0])),s.height=parseInt($.trim(v[1]));var ratio=s.width/s.height}$("#crop_constrain").is(":checked")&&$(canvas).crop("setRatio",ratio),$(canvas).crop("setArea",s)}}),$("option","#crop_presets").first().text(function(i,txt){return""+iw+" x "+ih+" ("+txt+")"}).val(iw+"x"+ih),$("#transform-crop-cancel").click(function(){self.reset()}),$("#rotate-angle-clockwise").click(function(){self._applyTransform("rotate",90)}),$("#rotate-angle-anticlockwise").click(function(){self._applyTransform("rotate",-90)}),$("#rotate-flip-vertical").click(function(){self._applyTransform("flip","vertical")}),$("#rotate-flip-horizontal").click(function(){self._applyTransform("flip","horizontal")})},_createFX:function(){var self=this,$img=$("img","#editor-image");$("#editor_effects").empty();var debounceApply=debounce(function(fx,amount){self._applyFx(fx,amount)},500);if($.support.canvas){Wf.sizeToFit($img.get(0),{width:70,height:70});$.each({brightness:{factor:10,preview:150},contrast:{factor:10,preview:2},hue:{factor:1,preview:90,min:-180,max:180},saturation:{factor:10,preview:200,filter:"saturate",min:-10,max:10,step:1,value:0},sharpen:{factor:10,preview:70,min:0,webgl:!0},blur:{factor:10,preview:70,min:0,webgl:!0},gamma:{factor:1,preview:50,min:-100,max:100,value:0,step:1}},function(k,v){if(!v.webgl||$.support.webgl){var canvas,fx=$img.clone().addClass("uk-responsive-width").appendTo("#editor_effects").wrap('<div class="editor_effect uk-width-1-1 uk-grid uk-grid-small" />').wrap('<div class="editor_effect_preview uk-width-1-4 uk-float-left" />'),filter=v.filter||k;v=$.extend({step:1,min:-10,max:10,value:0},v),$.support.filter?($(fx).show().cssFilter({filter:filter,amount:v.preview}),canvas=fx):($(fx).canvas().canvas("filter",[filter,v.preview]),canvas=$(fx).canvas("getCanvas"),$(canvas).insertAfter(fx));var controls=$('<div class="uk-form-row uk-width-3-4 uk-float-left"><label class="uk-form-label uk-width-7-10 uk-text-left uk-text-bold">'+tinyMCEPopup.getLang("dlg.fx_"+k,k)+'</label><div class="uk-width-3-10"><input type="number" class="uk-width-1-1" value="" /></div><div class="uk-width-1-1 uk-margin-small-top"><input type="range" class="uk-width-1-1" value="" /></div></div>').insertAfter($(fx).parent());$('input[type="number"], input[type="range"]',controls).change(function(event){var x=parseInt(this.value);$("input",controls).not(this).val(x),debounceApply(filter,x*v.factor)}),$('input[type="range"]',controls).on("input",function(event){$('input[type="number"]',controls).val(parseInt(this.value))}),$.each(v,function(attr,value){"preview"!==attr&&"factor"!==attr&&"filter"!==attr&&$('input[type="number"], input[type="range"]',controls).attr(attr,value)})}}),$.each({grayscale:1,invert:1,sepia:1,polaroid:1,vintage:1,brownie:1,kodachrome:1,technicolor:1},function(k,v){if($.support.webgl||!/(polaroid|vintage|brownie|kodachrome|technicolor)/.test(k)){var canvas,fx=$img.clone().addClass("uk-responsive-width").appendTo("#editor_effects").wrap('<div class="editor_effect uk-width-1-3 uk-margin-top"></div>').after('<span class="uk-label uk-text-bold">'+tinyMCEPopup.getLang("dlg.fx_"+k,k)+"</span>").wrap('<div class="editor_effect_preview" />');$.support.filter&&$.inArray(k,["grayscale","invert","sepia"])>=0?($(fx).show().cssFilter({filter:k}),canvas=fx):($(fx).canvas().canvas("filter",k),canvas=$(fx).canvas("getCanvas"),$(canvas).insertAfter(fx)),$(canvas).click(function(){self._applyFx(k,v)})}})}},_resetFX:function(){$('input[type="range"], input[type="number"]',"#editor_effects").val(0)},_resizeWin:function(){},_initTransform:function(fn){var img=$("img","#editor-image").get(0),canvas=$(img).canvas("getCanvas");switch(this.position(),fn){case"resize":$(canvas).resize({width:canvas.width,height:canvas.height,ratio:!!$("span.checkbox","#resize_constrain").is(".checked")&&getRatio(canvas),resize:function(e,size){$("#resize_width").val(size.width).data("tmp",size.width),$("#resize_height").val(size.height).data("tmp",size.height)},stop:function(){$("#resize_reset").prop("disabled",!1)}});break;case"crop":$(canvas).crop({width:canvas.width,height:canvas.height,ratio:!!$("#crop_constrain").is(":checked")&&getRatio(canvas),clone:$(img).canvas("copy"),start:function(){$("#crop_presets").val("")},stop:function(e,props){$("#crop_reset").prop("disabled",!1),$("#crop_presets").val(props.width+"x"+props.height)},change:function(e,props){$("#crop_width").val(props.width),$("#crop_height").val(props.height),$("#crop_x").val(props.x),$("#crop_y").val(props.y)}});break;case"rotate":}},_resetTransform:function(fn){var img=$("img","#editor-image").get(0),canvas=$(img).canvas("getCanvas"),w=canvas.width||$(canvas).width(),h=canvas.height||$(canvas).height();switch(fn){case"resize":this.position(),$.data(canvas,"uiResize")&&$(canvas).resize("reset"),$("#resize_reset").prop("disabled",!0),$("#resize_width").val(w).data("tmp",w),$("#resize_height").val(h).data("tmp",h),$("#resize_presets").val($("#resize_presets option:first").val());break;case"crop":$.data(canvas,"uiCrop")&&$(canvas).crop("reset"),$("#crop_reset").prop("disabled",!0),$("#crop_presets").val($("#crop_presets option:first").val()),$("#crop_width").val(w).data("tmp",w),$("#crop_height").val(h).data("tmp",h),$("#crop_x, #crop_y").val(0);break;case"rotate":}},updateCSSTransform:function(k,v){$("#rotate_angle img, #rotate_flip img, #editor_effects img, #editor_effects canvas").cssTransform(k,v)},undoCSSTransform:function(revert){var keys=["transform","msTransform","Transform","WebkitTransform","OTransform"];$("#rotate_angle img, #rotate_flip img").each(function(){var n=$(this).get(0);$.each(keys,function(i,s){var transforms=n.style[s]||[];transforms.length&&(transforms=transforms.split(" ")),revert?transforms=[transforms.shift()]:transforms.pop(),n.style[s]=transforms.join(" ")})}),$("#editor_effects img, #editor_effects canvas").each(function(){var n=$(this).get(0);$.each(keys,function(i,s){var transforms=n.style[s]||[];transforms.length&&(transforms=transforms.split(" ")),revert?transforms=[]:transforms.pop(),n.style[s]=transforms.join(" ")})})},undo:function(e){this.stack.pop(),$("img","#editor-image").canvas("undo"),this.stack.length||$("button.undo, button.revert, button.save").prop("disabled",!0),this.position(),e&&this._resetFX()},revert:function(e){var $img=$("img","#editor-image"),img=$img.get(0);$img.canvas("clear").canvas("draw",img,img.width,img.height),this.stack=[],$("button.undo, button.revert, button.save").prop("disabled",!0),e&&this._resetFX(),this.reset(!0)},reset:function(rw){var self=this,$img=$("img","#editor-image"),canvas=$img.canvas("getCanvas");$.each(["resize","crop","rotate"],function(i,fn){self._resetTransform(fn)}),rw&&($.data(canvas,"uiResize")&&$(canvas).resize("remove"),$.data(canvas,"uiCrop")&&$(canvas).crop("remove"),$.data(canvas,"uiRotate")&&$(canvas).rotate("remove")),this.position()},position:function(){var w,h,$img=$("img","#editor-image"),canvas=$img.canvas("getCanvas"),pw=$("#editor-image").width()-20,ph=$("#editor-image").height()-20,pct=10;$(canvas).css({width:"",height:""}),$(canvas).width()>pw&&(w=Math.round(pw-pw/100*pct),h=canvas.height*(w/canvas.width),$(canvas).width(w).height(h),pct+=10),$(canvas).height()>ph&&(h=ph-ph/100*pct,w=canvas.width*(h/canvas.height),$(canvas).height(h).width(w),pct+=10);var ch=$(canvas).height()||canvas.height;$(canvas).css({top:(ph-ch)/2})},_apply:function(k,v){function cleanTemp(src){Wf.JSON.request("cleanEditorTmp",{json:[src]})}var self=this,deferred=$.Deferred(),$img=$("img","#editor-image"),name=($img.canvas("getCanvas"),Wf.String.basename(self.src)),src=tinyMCEPopup.getWindowArg("src"),data=$img.canvas("output",self.getMime(name),100,!0);return self.sendBinary(data,{method:"applyEdit",id:uid(),params:[src,k,v]}).then(function(o){if(o.files){var img=new Image;img.onload=function(){return $img.canvas("draw",img,img.width,img.height),self.position(),self._removeLoader(),cleanTemp(src),deferred.resolve(),!0},img.onerror=function(){return self._removeLoader(),Wf.Modal.alert('Action "'+k+'" failed. Temp image could not be loaded.'),cleanTemp(src),deferred.reject(),!1};var tmp=o.files[0]||"";if(tmp=tmp.replace(/[^\w\.\-~\/\\\\\s ]/gi,""),!tmp)return cleanTemp(src),!1;img.src=self._loadImage(Wf.String.path(Wf.getURI(),tmp))}}).fail(function(s){}).always(function(){self._removeLoader()}),deferred},_applyFx:function(){var self=this,length=this.stack.length,$img=$("img","#editor-image"),args=$.makeArray(arguments),filter=args.shift(),amount=args.shift();if(self._setLoader(),length){var last=this.stack[length-1];args&&last.task===filter&&this.undo()}self.addUndo({task:filter,args:amount}),$img.canvas("filter",filter,amount,!0),this._removeLoader(),$("button.undo, button.revert, button.save").prop("disabled",!1)},addUndo:function(data){this.stack.push(data)},_applyTransform:function(){function done(fn){$("button.undo, button.revert, button.save").prop("disabled",!1),self.reset(!0),self._initTransform(fn)}var self=this,$img=$("img","#editor-image"),canvas=$img.canvas("getCanvas"),args=$.makeArray(arguments),fn=args.shift(),amount=args.shift();switch(self._setLoader(),fn){case"resize":var w=$("#resize_width").val(),h=$("#resize_height").val();this._apply("resize",{width:w,height:h}).then(function(){args=[w,h],self.addUndo({task:fn,args:args})}).always(function(){done(fn)});break;case"crop":var s=$(canvas).crop("getArea");this._apply("crop",s).then(function(){args=[s.width,s.height,s.x,s.y],self.addUndo({task:fn,args:args})}).always(function(){done(fn)});break;case"rotate":$img.canvas("rotate",amount,!0),self.position(),self._removeLoader(),this.addUndo({task:fn,args:amount}),done(fn);break;case"flip":$img.canvas("flip",amount,!0),self.position(),self._removeLoader(),this.addUndo({task:fn,args:amount}),done(fn)}},getMime:function(s){var mime="image/jpeg",ext=Wf.String.getExt(s);switch(ext){case"jpg":case"jpeg":mime="image/jpeg";break;case"png":mime="image/png";break;case"bmp":mime="image/bmp"}return mime},save:function(name){var self=this,$img=$("img","#editor-image"),extras=($img.canvas("getCanvas"),'<div class="uk-form-row"><label for="image_quality" class="uk-form-label uk-width-3-10">'+tinyMCEPopup.getLang("dlg.quality","Quality")+'</label><div class="uk-form-controls uk-width-7-10 uk-margin-remove"><input type="range" min="1" max="100" id="image_quality_slider" value="100" class="uk-width-3-5" /><input type="number" id="image_quality" min="1" max="100" value="100" class="quality" /> %</div></div>'),name=Wf.String.basename(this.src);name=Wf.String.stripExt(name);var ext=Wf.String.getExt(this.src);Wf.Modal.prompt(tinyMCEPopup.getLang("dlg.save_image","Save Image"),function(name){var quality=$("#image_quality").val()||100;self._setLoader(),name=name+"."+ext||Wf.String.basename(self.src);var src=tinyMCEPopup.getWindowArg("src");if($.support.Blob){var data=$img.canvas("output",self.getMime(name),quality,!0);data&&self.sendBinary(data,{method:"saveEdit",id:uid(),params:[src,name]}).then(function(o){if(o.files){if(self.src=o.files[0]||"",!self.src||!self._validatePath(self.src))return Wf.Modal.alert("Invalid image file"),!1;var img=new Image;img.onload=function(){$("img","#editor-image").attr("src",img.src).on("load",function(){self._createFX(),$(this).canvas("draw",img,img.width,img.height)})},img.src=self._loadImage(Wf.getURI()+self.src);var s=self.settings;s.save.apply(s.scope||self,[self.src]),self.stack=[],$("button.undo, button.revert, button.save").prop("disabled",!0)}}).fail(function(s){Wf.Modal.alert(s)}).always(function(){self._removeLoader()})}else{for(var stack=self.stack,args=[],i=0;i<stack.length;i++){var s=stack[i],n=args[args.length-1];n&&s.task===n.task&&1===s.args.length?n.args[0]+=s.args[0]:args.push(s)}Wf.JSON.request("saveEdit",{json:[src,name,args,quality]},cb)}},{text:tinyMCEPopup.getLang("dlg.name","Name"),elements:extras,height:200,value:name,onOpen:function(){$("#dialog-prompt-input").parent().addClass("uk-form-icon uk-form-icon-flip").append('<span class="uk-text-muted uk-icon-none">.'+ext+"</span>"),$("#image_quality_slider").change(function(){$("#image_quality").val(this.value)}),$("#image_quality").change(function(){$("#image_quality_slider").val(this.value)})}})},sendBinary:function(data,json,cb){var ed=tinyMCEPopup.editor,deferred=$.Deferred(),url=document.location.href;url=url.replace(/&wf([a-z0-9]+)=1/,"");var args={context:ed.settings.context};args[ed.settings.token]="1";var fd=new FormData,xhr=new XMLHttpRequest;xhr.open("POST",url,!0),xhr.setRequestHeader("X-Requested-With","XMLHttpRequest"),xhr.onload=function(){var r={},error="An error occured processing this image.";if(data=fd=null,200==this.status)try{r=JSON.parse(this.response)}catch(e){return deferred.reject("The server returned an invalid JSON response."),!1}if($.isPlainObject(r)){if(r.error)return deferred.reject(r.error.message||error),!1;if(!r.result)return deferred.reject(error),!1;deferred.resolve(r.result)}else/[{}]/.test(r)&&(error="The server returned an invalid JSON response."),deferred.reject(error)},$.each(args,function(k,v){"string"==$.type(v)&&fd.append(k,v)}),fd.append("json",JSON.stringify(json));var name=Wf.String.basename(json.params[0]);return fd.append("file",data,name),xhr.send(fd),deferred},_validatePath:function(s){function _toUnicode(c){for(c=c.toString(16).toUpperCase();c.length<4;)c="0"+c;return"\\u"+c}if(/\.{2,}/.test(s)||/:\/\//.test(s)&&s.indexOf(Wf.getURI(!0))==-1)return!1;if(/:\/\//.test(s)&&(s=Wf.URL.toRelative(s)),/[^\w\.\-~\s \/\\\\]/i.test(s))for(var i=0,ln=s.length;i<ln;i++){var ch=s[i];if(/[^\w\.\-~\s \/\\\\]/i.test(ch)&&_toUnicode(ch.charCodeAt(0))<"\\u007F")return!1}return!0},_loadImage:function(src){return src+"?"+(new Date).getTime()}};window.EditorDialog=EditorDialog}(jQuery,Wf);