/* jce - 2.9.3 | 2021-02-25 | https://www.joomlacontenteditor.net | Copyright (C) 2006 - 2021 Ryan Demmer. All rights reserved | GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html */
!function(){function validateContent(ed,content){var args={no_events:!0,format:"raw"},settings={};if(extend(settings,ed.settings),args.content=content,ed.settings.validate&&settings.source_validate_content!==!1){args.format="html",args.load=!0,ed.onBeforeSetContent.dispatch(ed,args),settings.verify_html=!1,settings.forced_root_block=!1,settings.validate=!0;var parser=new DomParser(settings,ed.schema),serializer=new HtmlSerializer(settings,ed.schema);args.content=serializer.serialize(parser.parse(args.content),args),args.get=!0,ed.onPostProcess.dispatch(ed,args),content=args.content}return content}var DOM=tinymce.DOM,Event=tinymce.dom.Event,extend=tinymce.extend,DomParser=tinymce.html.DomParser,HtmlSerializer=tinymce.html.Serializer;tinymce.html.SaxParser,tinymce.html.Schema;tinymce.create("tinymce.plugins.SourcePlugin",{init:function(ed,url){var self=this;self.editor=ed,ed.plugins.fullscreen&&(ed.onFullScreen.add(function(ed,state){var element=ed.getElement(),header=DOM.getPrev(element,".wf-editor-header"),iframe=DOM.get(ed.id+"_editor_source_iframe");if(state){ed.settings.container_height||(ed.settings.container_height=iframe.clientHeight);var vp=DOM.getViewPort();DOM.setStyle(iframe,"height",vp.h-header.offsetHeight-42),DOM.setStyle(iframe,"max-width","100%")}else DOM.setStyle(iframe,"height",ed.settings.container_height-42),DOM.setStyle(iframe,"max-width",ed.settings.container_width||"100%");if(iframe){var editor=iframe.contentWindow.SourceEditor,w=iframe.clientWidth,h=iframe.clientHeight;editor.resize(w,h)}}),ed.onFullScreenResize.add(function(ed,vp){var element=ed.getElement(),header=DOM.getPrev(element,".wf-editor-header"),iframe=DOM.get(ed.id+"_editor_source_iframe");DOM.setStyle(iframe,"height",vp.h-header.offsetHeight-42)})),ed.onSetContent.add(function(ed,o){self.setContent(ed.getContent(),!0)}),ed.onInit.add(function(ed){var activeTab=sessionStorage.getItem("wf-editor-tabs-"+ed.id)||ed.settings.active_tab||"";"wf-editor-source"===activeTab&&(DOM.hide(ed.getContainer()),DOM.hide(ed.getElement()),window.setTimeout(function(){self.toggle()},10))})},getSourceEditor:function(){var ed=this.editor,iframe=DOM.get(ed.id+"_editor_source_iframe");return iframe?iframe.contentWindow.SourceEditor||null:null},setContent:function(v){var editor=this.getSourceEditor();return!!editor&&editor.setContent(v)},insertContent:function(v){var editor=this.getSourceEditor();return!!editor&&editor.insertContent(v)},getContent:function(){var ed=this.editor,editor=this.getSourceEditor();return editor&&!DOM.isHidden(ed.id+"_editor_source")?editor.getContent():null},hide:function(){var ed=this.editor,editor=this.getSourceEditor();editor&&editor.setActive(!1),DOM.hide(ed.id+"_editor_source")},getActionState:function(key,value){var ed=this.editor;if(ed.settings.use_state_cookies!==!1){var state=tinymce.util.Cookie.get("wf_source_"+key);if(tinymce.is(state)&&null!==state)return parseInt(state)}return value},setActionState:function(key,value){var ed=this.editor;ed.settings.use_state_cookies!==!1&&tinymce.util.Cookie.set("wf_source_"+key,value?1:0)},save:function(content,debounced){var ed=this.editor,el=ed.getElement();content=tinymce.is(content)?content:this.getContent();var args={no_events:!0,format:"raw"};return args.content=content,content=validateContent(ed,content),/TEXTAREA|INPUT/i.test(el.nodeName)?el.value=content:el.innerHTML=content,debounced&&(args.content=content,ed.onWfEditorChange.dispatch(ed,args)),content},getActiveLine:function(){var ed=this.editor,blocks=[],line=0;tinymce.each(ed.schema.getBlockElements(),function(value,name){return!!/\W/.test(name)||void blocks.push(name.toLowerCase())});var node=ed.selection.getNode(),nodes=ed.getBody().querySelectorAll(blocks.join(","));if(!node)return line;1===node.nodeType&&"bookmark"!==node.getAttribute("data-mce-type")||(node=node.parentNode);for(var i=0,len=nodes.length;i<len;i++)if(nodes[i]===node){line=i;break}return line},createToolbar:function(container){var cm,searchBox,replaceBox,regexBtn,self=this,ed=this.editor,ControlManager=new tinymce.ControlManager(ed),toolbar=DOM.add(container,"form",{class:"mceToolbar mceToolbarSource",onsubmit:"return false;"}),toolbarRow=ControlManager.createToolbar("source_toolbar",{name:ed.getLang("advanced.toolbar"),tab_focus_toolbar:ed.getParam("theme_advanced_tab_focus_toolbar"),class:"mceFlex mceFlexAuto"}),toolbarActions=ControlManager.createToolbar("source_toolbar_actions",{class:"mceSourceActions"});if(ed.plugins.fullscreen){var fullscreen_btn=ControlManager.createButton("source_fullscreen",{title:ed.getLang("source.fullscreen","Fullscreen"),onclick:function(){var state=!fullscreen_btn.isActive();return fullscreen_btn.setActive(state),ed.execCommand("mceFullScreen")}});fullscreen_btn.setActive(ed.fullscreen_enabled),toolbarActions.add(fullscreen_btn)}tinymce.each(["undo","redo"],function(name){var btn=ControlManager.createButton("source_"+name,{title:ed.getLang("source."+name,name),onclick:function(){return cm||(cm=self.getSourceEditor()),cm.execCommand(name)}});toolbarActions.add(btn)}),tinymce.each(["highlight","linenumbers","wrap"],function(name){var btn=ControlManager.createButton("source_"+name,{title:ed.getLang("source."+name,name),onclick:function(){var state=!btn.isActive();return btn.setActive(state),self.setActionState(name,state),cm||(cm=self.getSourceEditor()),cm.execCommand(name,state)}});btn.onPostRender.add(function(){var state=self.getActionState(name,ed.getParam("source_"+name,!0));btn.setActive(!!state)}),toolbarActions.add(btn)});var format_btn=ControlManager.createButton("source_format",{title:ed.getLang("source.format","Format"),onclick:function(){return cm||(cm=self.getSourceEditor()),cm.execCommand("format")}});toolbarActions.add(format_btn),toolbarRow.add(toolbarActions);var toolbarSearch=ControlManager.createToolbar("source_toolbar_search",{class:"mceSourceSearch"});searchBox=ControlManager.createTextBox("source_search_value",{title:ed.getLang("source.search","Search"),attributes:{placeholder:ed.getLang("source.search_value","Search")}}),searchBox.onChange.add(function(){var value=searchBox.value();if(""===value)return cm||(cm=self.getSourceEditor()),cm.execCommand("clearSearch")}),searchBox.onPostRender.add(function(e,elm){Event.add(elm,"keydown",function(e){if(13===e.keyCode){e.preventDefault();var value=searchBox.value();if(""===value)return!1;cm||(cm=self.getSourceEditor());var regex=regexBtn.isActive();return cm.execCommand("search",value,!0,!!regex)}})}),toolbarSearch.add(searchBox),tinymce.each({previous:"search_prev",next:"search"},function(label,name){var btn=ControlManager.createButton("source_search_"+name,{title:ed.getLang("source."+label,name),onclick:function(e){cm||(cm=self.getSourceEditor());var value=searchBox.value(),regex=regexBtn.isActive();return cm.execCommand("search",value,"previous"===name,!!regex)}});toolbarSearch.add(btn)}),replaceBox=ControlManager.createTextBox("source_replace_value",{title:ed.getLang("source.replace","Replace"),attributes:{placeholder:ed.getLang("source.replace_value","Replace")}}),replaceBox.onPostRender.add(function(e,elm){Event.add(elm,"keydown",function(e){if(13===e.keyCode){e.preventDefault();var value=searchBox.value();if(""===value)return!1;cm||(cm=self.getSourceEditor());var replace=replaceBox.value(),regex=regexBtn.isActive();return cm.execCommand("replace",value,replace,!1,!!regex)}})}),toolbarSearch.add(replaceBox),tinymce.each(["replace","replace_all"],function(name){var btn=ControlManager.createButton("source_"+name,{title:ed.getLang("source."+name,name),onclick:function(){cm||(cm=self.getSourceEditor());var value=searchBox.value(),replace=replaceBox.value(),regex=regexBtn.isActive();return cm.execCommand("replace",value,replace,"replace_all"===name,!!regex)}});toolbarSearch.add(btn)});var regexBtnState=!1;regexBtn=ControlManager.createButton("source_search_regex",{title:ed.getLang("source.search_regex","Regular Expression"),onclick:function(){regexBtnState=!regexBtnState,regexBtn.setActive(regexBtnState)}}),toolbarSearch.add(regexBtn),toolbarRow.add(toolbarSearch),toolbarRow.renderTo(toolbar),ControlManager.onPostRender.dispatch()},toggle:function(){var ed=this.editor,self=this,s=ed.settings,element=ed.getElement(),container=element.parentNode,header=DOM.getPrev(element,".wf-editor-header"),div=DOM.get(ed.id+"_editor_source"),iframe=DOM.get(ed.id+"_editor_source_iframe"),statusbar=DOM.get(ed.id+"_editor_source_resize"),ifrHeight=parseInt(DOM.get(ed.id+"_ifr").style.height)||s.height,o=tinymce.util.Cookie.getHash("TinyMCE_"+ed.id+"_size");o&&o.height&&(ifrHeight=o.height);var content=tinymce.is(element.value)?element.value:element.innerHTML;content=validateContent(ed,content);var selection="",line=this.getActiveLine();if(!ed.selection.isCollapsed()){var node=ed.selection.getNode();node!==ed.getBody()&&(selection=node.outerHTML)}if(div){DOM.show(div);var editor=iframe.contentWindow.SourceEditor,w=iframe.clientWidth,h=iframe.clientHeight;editor.resize(w,h),editor.setContent(content,!0),selection&&editor.setSelection(line,selection),editor.setCursor(line),editor.setActive(!0),DOM.removeClass(container,"mce-loading")}else{var div=DOM.add(container,"div",{role:"textbox",id:ed.id+"_editor_source",class:"wf-editor-source"}),skin=s.skin_class||"defaultSkin";DOM.addClass(div,skin);var query=ed.getParam("site_url")+"index.php?option=com_jce",args={task:"plugin.display",plugin:"source",context:ed.getParam("context")};args[ed.settings.token]=1;for(k in args)query+="&"+k+"="+encodeURIComponent(args[k]);var iframe=DOM.create("iframe",{frameborder:0,scrolling:"no",id:ed.id+"_editor_source_iframe",src:query});Event.add(iframe,"load",function(){var editor=iframe.contentWindow.SourceEditor,w=iframe.clientWidth,h=iframe.clientHeight,options={theme:ed.getParam("source_theme","codemirror"),format:ed.getParam("source_format",!0),tag_closing:ed.getParam("source_tag_closing",!0),selection_match:ed.getParam("source_selection_match",!0),font_size:ed.getParam("source_font_size",""),fullscreen:DOM.hasClass(container,"mce-fullscreen"),load:function(){DOM.removeClass(container,"mce-loading"),editor.resize(w,h),selection&&editor.setSelection(line,selection),editor.setCursor(line)},format_options:ed.getParam("source_format_options",{}),editor:ed};tinymce.each(["wrap","linenumbers","highlight"],function(key){options[key]=self.getActionState(key,ed.getParam("source_"+key,!0))}),editor.init(options,content)}),this.createToolbar(div);var iframeContainer=DOM.add(div,"div",{class:"mceIframeContainer"});DOM.add(iframeContainer,iframe),statusbar=DOM.add(div,"div",{id:ed.id+"_editor_source_statusbar",class:"mceStatusbar mceLast"},'<div class="mcePathRow"></div><div tabindex="-1" class="mceResize" id="'+ed.id+'_editor_source_resize"><span class="mceIcon mce_resize"></span></div>');var resize=DOM.get(ed.id+"_editor_source_resize");Event.add(resize,"click",function(e){e.preventDefault()}),Event.add(resize,"mousedown",function(e){function resizeTo(w,h){w=Math.max(w,300),h=Math.max(h,200),iframe.style.maxWidth=w+"px",iframe.style.height=h+"px",container.style.maxWidth=w+"px",editor.resize(w,h),ed.settings.container_width=w,ed.settings.container_height=h+statusbar.offsetHeight,h-=ed.settings.interface_height||0,ed.theme.resizeTo(w,h)}function resizeOnMove(e){e.preventDefault(),w=sw+(e.screenX-sx),h=sh+(e.screenY-sy),resizeTo(w,h),DOM.addClass(resize,"wf-editor-source-resizing")}function endResize(e){e.preventDefault(),Event.remove(DOM.doc,"mousemove",mm1),Event.remove(DOM.doc,"mouseup",mu1),Event.remove(ifrDoc,"mousemove",mm2),Event.remove(ifrDoc,"mouseup",mu2),w=sw+(e.screenX-sx),h=sh+(e.screenY-sy),resizeTo(w,h),DOM.removeClass(resize,"wf-editor-source-resizing")}var sx,sy,sw,sh,w,h,ifrDoc=iframe.contentWindow.document,editor=iframe.contentWindow.SourceEditor;return e.preventDefault(),DOM.hasClass(resize,"wf-editor-source-resizing")?(endResize(e),!1):(sx=e.screenX,sy=e.screenY,sw=w=container.offsetWidth,sh=h=iframe.clientHeight,mm1=Event.add(DOM.doc,"mousemove",resizeOnMove),mu1=Event.add(DOM.doc,"mouseup",endResize),mm2=Event.add(ifrDoc,"mousemove",resizeOnMove),void(mu2=Event.add(ifrDoc,"mouseup",endResize)))})}var height=ed.settings.container_height||sessionStorage.getItem("wf-editor-container-height")||ifrHeight+statusbar.offsetHeight,width=ed.settings.container_width||sessionStorage.getItem("wf-editor-container-width");if(DOM.hasClass(container,"mce-fullscreen")){var vp=DOM.getViewPort();height=vp.h-header.offsetHeight}DOM.setStyle(iframe,"height",height-statusbar.offsetHeight);var editor=iframe.contentWindow.SourceEditor;editor&&editor.resize(width,height-statusbar.offsetHeight)},getCursorPos:function(){var iframe=DOM.get(this.editor.id+"_editor_source_iframe");if(iframe){var editor=iframe.contentWindow.SourceEditor;if(editor)return editor.getCursorPos()}return 0}}),tinymce.PluginManager.add("source",tinymce.plugins.SourcePlugin)}();