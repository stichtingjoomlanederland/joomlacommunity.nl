/* jce - 2.7.13 | 2019-05-07 | https://www.joomlacontenteditor.net | Copyright (C) 2006 - 2019 Ryan Demmer. All rights reserved | GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html */
!function(){function ucfirst(s){return s.substring(0,1).toUpperCase()+s.substring(1)}function updateWithTouchData(e){var keys,i;if(e.changedTouches)for(keys="screenX screenY pageX pageY clientX clientY".split(" "),i=0;i<keys.length;i++)e[keys[i]]=e.changedTouches[0][keys[i]]}var DOM=tinymce.DOM,Event=(tinymce.dom.Element,tinymce.dom.Event),each=tinymce.each;tinymce.is;tinymce.create("tinymce.plugins.InlinePopups",{init:function(ed,url){ed.onBeforeRenderUI.add(function(){ed.windowManager=new tinymce.InlineWindowManager(ed),ed.settings.compress.css||DOM.loadCSS(url+"/css/window.css")})}}),tinymce.create("tinymce.InlineWindowManager:tinymce.WindowManager",{InlineWindowManager:function(ed){var self=this;self.parent(ed),self.zIndex=300002,self.count=0,self.windows={}},open:function(f,p){var id,vp,mdf,clf,rsf,w,u,self=this,ed=self.editor,dw=0,dh=0;if(f=f||{},p=p||{},!f.inline)return self.parent(f,p);f.type||(self.bookmark=ed.selection.getBookmark(1)),id=DOM.uniqueId("mce_inlinepopups_"),vp=DOM.getViewPort(),f.width=parseInt(f.width||0),f.height=parseInt(f.height||0)+(tinymce.isIE?8:0),p.mce_inline=!0,p.mce_window_id=id,f.popup_css=!1,self.features=f,self.params=p,f.translate_i18n=!1,self.onOpen.dispatch(self,f,p);var html='<div class="mceModalBody" id="'+id+'">   <div class="mceModalContainer">       <div class="mceModalHeader mceModalMove" id="'+id+'_header">           <button class="mceModalClose" type="button"></button>           <h3 class="mceModalTitle" id="'+id+'_title">'+(f.title||"")+'</h3>       </div>       <div class="mceModalContent" id="'+id+'_content"></div>   </div>   </div>',modal=DOM.select(".mceModal");if(modal.length||(modal=DOM.add(DOM.doc.body,"div",{class:"mceModal",role:"dialog"},""),f.overlay!==!1&&DOM.add(modal,"div",{class:"mceModalOverlay"})),DOM.add(modal,"div",{class:"mceModalFrame",id:id+"_frame"},html),u=f.url||f.file,u&&(tinymce.relaxedDomain&&(u+=(u.indexOf("?")==-1?"?":"&")+"mce_rdomain="+tinymce.relaxedDomain),u=tinymce._addVer(u)),f.type)DOM.setHTML(id+"_title",""),DOM.addClass(id,"mceModal"+ucfirst(f.type)),DOM.add(DOM.select(".mceModalContainer",id),"div",{class:"mceModalFooter",id:id+"_footer"}),DOM.add(id+"_footer","button",{id:id+"_ok",class:"mceButton mceOk",type:"button"},"confirm"==f.type?"Yes":"Ok"),"confirm"==f.type&&DOM.add(id+"_footer","button",{type:"button",class:"mceButton mceCancel"},"No"),DOM.setHTML(id+"_content","<div>"+f.content.replace("\n","<br />")+"</div>"),Event.add(id,"keyup",function(evt){if(27===evt.keyCode)return f.button_func(!1),Event.cancel(evt)}),Event.add(id,"keydown",function(evt){if(9===evt.keyCode){var cancelButton=DOM.select(".mceCancel",id+"_footer")[0];return cancelButton&&cancelButton!==evt.target?cancelButton.focus():DOM.get(id+"_ok").focus(),Event.cancel(evt)}});else{DOM.addClass(id,"mceLoading");var iframe=DOM.add(id+"_content","iframe",{id:id+"_ifr",src:'javascript:""',frameBorder:0});try{}catch(e){}DOM.setAttrib(iframe,"src",u),Event.add(iframe,"load",function(){DOM.removeClass(id,"mceLoading")})}return f.type||(dh+=DOM.get(id+"_header").clientHeight),f.size?DOM.addClass(id,f.size):(f.width&&DOM.setStyle(id,"width",f.width+dw),f.height&&DOM.setStyle(id,"height",f.height+dh)),rsf=Event.add(DOM.win,"resize orientationchange",function(){DOM.get(id)&&self.position(id)}),mdf=Event.add(id,"mousedown",function(e){var w,n=e.target;if(w=self.windows[id],self.focus(id),"BUTTON"==n.nodeName){if(DOM.hasClass(n,"mceModalClose"))return self.close(null,id),Event.cancel(e)}else if(DOM.hasClass(n,"mceModalMove")||DOM.hasClass(n.parentNode,"mceModalMove"))return self._startDrag(id,e,"Move")}),clf=Event.add(id,"click",function(e){var n=e.target;if(self.focus(id),"BUTTON"==n.nodeName)return DOM.hasClass(n,"mceModalClose")&&self.close(null,id),DOM.hasClass(n,"mceButton")&&f.button_func(DOM.hasClass(n,"mceOk")),Event.cancel(e)}),w=self.windows[id]={id:id,mousedown_func:mdf,click_func:clf,resize_func:rsf,features:f,deltaWidth:dw,deltaHeight:dh},DOM.setAttrib(id,"aria-hidden","false"),this.position(id),self.focus(id),DOM.get(id+"_ok")&&DOM.get(id+"_ok").focus(),self.count++,w},position:function(id){var p=DOM.getRect(id),vp=DOM.getViewPort(),top=Math.round(Math.max(vp.y+10,vp.y+vp.h/2-p.h/2)),left=Math.round(Math.max(vp.x+10,vp.x+vp.w/2-p.w/2));DOM.setStyles(id,{left:left,top:top})},focus:function(id){var w,self=this;(w=self.windows[id])&&(w.zIndex=this.zIndex++,DOM.setStyle(id,"zIndex",w.zIndex),DOM.removeClass(self.lastId,"mceFocus"),DOM.addClass(id+"_frame","mceFocus"),self.lastId=id+"_frame",w.focussedElement?w.focussedElement.focus():DOM.get(id+"_ok")?DOM.get(w.id+"_ok").focus():DOM.get(w.id+"_ifr")&&DOM.get(w.id+"_ifr").focus())},_startDrag:function(id,se,ac){function end(){Event.remove(d,"mouseup touchend",mu),Event.remove(d,"mousemove touchmove",mm),DOM.removeClass(id,"dragging")}var mu,mm,sx,sy,cp,self=this,d=DOM.doc;self.windows[id];return DOM.hasClass(id,"dragging")?(end(),Event.cancel(se)):(updateWithTouchData(se),p=DOM.getRect(id),vp=DOM.getViewPort(),cp={x:0,y:0},vp.w-=2,vp.h-=2,DOM.addClass(id,"dragging"),sx=se.screenX,sy=se.screenY,mu=Event.add(d,"mouseup touchend",function(e){return updateWithTouchData(e),end(),Event.cancel(e)}),mm=Event.add(d,"mousemove touchmove",function(e){var x,y;return updateWithTouchData(e),x=e.screenX-sx,y=e.screenY-sy,dx=Math.max(p.x+x,10),dy=Math.max(p.y+y,10),DOM.setStyles(id,{left:dx,top:dy}),Event.cancel(e)}),Event.cancel(se))},close:function(win,id){var w,id,self=this,d=DOM.doc;return id=self._findId(id||win),self.windows[id]?(self.count--,0===self.count&&(DOM.remove(DOM.select(".mceModal")),DOM.setAttrib(DOM.doc.body,"aria-hidden","false"),self.editor.focus()),void((w=self.windows[id])&&(self.onClose.dispatch(self),Event.remove(d,"mousedown",w.mousedown_func),Event.remove(d,"click",w.click_func),Event.remove(win,"resize orientationchange",w.resize_func),Event.clear(id),Event.clear(id+"_ifr"),DOM.setAttrib(id+"_ifr","src",'javascript:""'),DOM.remove(id+"_frame"),DOM.remove(id),delete self.windows[id]))):void self.parent(win)},_frontWindow:function(){var fw,ix=0;return each(this.windows,function(w){w.zIndex>ix&&(fw=w,ix=w.zIndex)}),fw},setTitle:function(w,ti){var e;w=this._findId(w),(e=DOM.get(w+"_title"))&&(e.innerHTML||(e.innerHTML=DOM.encode(ti)))},alert:function(txt,cb,s){var w,self=this;w=self.open({title:self,type:"alert",button_func:function(s){cb&&cb.call(s||self,s),self.close(null,w.id)},content:DOM.encode(self.editor.getLang(txt,txt)),inline:1})},confirm:function(txt,cb,s){var w,self=this;w=self.open({title:self,type:"confirm",button_func:function(s){cb&&cb.call(s||self,s),self.close(null,w.id)},content:DOM.encode(self.editor.getLang(txt,txt)),inline:1})},resizeBy:function(dw,dh,id){this.windows[id]},_findId:function(w){var self=this;return"string"==typeof w?w:(each(self.windows,function(wo){var ifr=DOM.get(wo.id+"_ifr");if(ifr&&w==ifr.contentWindow)return w=wo.id,!1}),w)}}),tinymce.PluginManager.add("inlinepopups",tinymce.plugins.InlinePopups)}();