/* jce - 2.7.17 | 2019-08-22 | https://www.joomlacontenteditor.net | Copyright (C) 2006 - 2019 Ryan Demmer. All rights reserved | GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html */
!function(){var each=tinymce.each,fontIconRe=/<([a-z0-9]+)([^>]+)class="([^"]*)(glyph|uk-)?(fa|icon)-([\w-]+)([^"]*)"([^>]*)>(&nbsp;|\u00a0)?<\/\1>/gi;tinymce.create("tinymce.plugins.TemplatePlugin",{init:function(ed,url){function isEmpty(){var s=ed.getContent();return""==s||"<p>&nbsp;</p>"==s}var t=this;t.editor=ed,t.contentLoaded=!1,ed.addCommand("mceTemplate",function(ui){ed.windowManager.open({file:ed.getParam("site_url")+"index.php?option=com_jce&task=plugin.display&plugin=templatemanager",width:820+ed.getLang("templatemanager_delta_width",0),height:400+ed.getLang("templatemanager_delta_height",0),inline:1,popup_css:!1},{plugin_url:url})}),ed.onInit.add(function(){ed&&ed.plugins.contextmenu&&ed.plugins.contextmenu.onContextMenu.add(function(th,m,e){m.add({title:"templatemanager.desc",icon:"templatemanager",cmd:"mceTemplate"})})}),ed.addCommand("mceInsertTemplate",t._insertTemplate,t),ed.addButton("templatemanager",{title:"templatemanager.desc",cmd:"mceTemplate"}),ed.onPreProcess.add(function(ed,o){var dom=ed.dom;each(dom.select("div",o.node),function(e){dom.hasClass(e,"mceTmpl")&&(each(dom.select("*",e),function(e){dom.hasClass(e,ed.getParam("templatemanager_mdate_classes","mdate modifieddate").replace(/\s+/g,"|"))&&(e.innerHTML=t._getDateTime(new Date,ed.getParam("templatemanager_mdate_format",ed.getLang("templatemanager.mdate_format"))))}),t._replaceVals(e))})}),ed.getParam("templatemanager_content_url")&&ed.onInit.add(function(){if(!t.contentLoaded&&isEmpty()){var u=ed.getParam("templatemanager_content_url");/http(s)?:\/\//.test(u)||(ed.setProgressState(!0),tinymce.util.XHR.send({url:ed.settings.document_base_url+"/"+u,success:function(x){var s=/<body[^>]*>([\s\S]+?)<\/body>/.exec(x)||["",x];ed.setContent(s[1]),ed.setProgressState(!1),t.contentLoaded=!0},error:function(e,x){ed.setProgressState(!1),t.contentLoaded=!0}}))}})},_insertTemplate:function(ui,v){function hasClass(n,c){var cls=ed.dom.getAttrib(n,"class","");return new RegExp("\\b"+c+"\\b","g").test(cls)}var h,el,t=this,ed=t.editor,dom=ed.dom,sel=ed.selection.getContent();h=v.content,each(t.editor.getParam("templatemanager_replace_values"),function(v,k){"function"!=typeof v&&(h=h.replace(new RegExp("\\{\\$"+k+"\\}","g"),v))}),el=dom.create("div",null,h),each(dom.select("*",el),function(n){hasClass(n,ed.getParam("templatemanager_cdate_classes","cdate creationdate").replace(/\s+/g,"|"))&&(n.innerHTML=t._getDateTime(new Date,ed.getParam("templatemanager_cdate_format",ed.getLang("templatemanager.cdate_format")))),hasClass(n,ed.getParam("templatemanager_mdate_classes","mdate modifieddate").replace(/\s+/g,"|"))&&(n.innerHTML=t._getDateTime(new Date,ed.getParam("templatemanager_mdate_format",ed.getLang("templatemanager.mdate_format")))),hasClass(n,ed.getParam("templatemanager_selected_content_classes","selcontent").replace(/\s+/g,"|"))&&(n.innerHTML=sel)}),t._replaceVals(el),ed.settings.validate===!1&&(ed.settings.validate=!0);var html=el.innerHTML;html=html.replace(fontIconRe,'<$1$2class="$3$4$5-$6$7"$8>&nbsp;</$1>'),html=html.replace(/<(a|i|span)([^>]+)><\/\1>/gi,"<$1$2>&nbsp;</$1>"),ed.execCommand("mceInsertContent",!1,html),ed.settings.verify_html===!1&&(ed.settings.validate=!1),ed.addVisual()},_replaceVals:function(e){var dom=this.editor.dom,vl=this.editor.getParam("templatemanager_replace_values");each(dom.select("*",e),function(e){each(vl,function(v,k){dom.hasClass(e,k)&&"function"==typeof vl[k]&&vl[k](e)})})},_getDateTime:function(d,fmt){function addZeros(value,len){var i;if(value=""+value,value.length<len)for(i=0;i<len-value.length;i++)value="0"+value;return value}var ed=this.editor;return fmt?(fmt=fmt.replace("%D","%m/%d/%y"),fmt=fmt.replace("%r","%I:%M:%S %p"),fmt=fmt.replace("%Y",""+d.getFullYear()),fmt=fmt.replace("%y",""+d.getYear()),fmt=fmt.replace("%m",addZeros(d.getMonth()+1,2)),fmt=fmt.replace("%d",addZeros(d.getDate(),2)),fmt=fmt.replace("%H",""+addZeros(d.getHours(),2)),fmt=fmt.replace("%M",""+addZeros(d.getMinutes(),2)),fmt=fmt.replace("%S",""+addZeros(d.getSeconds(),2)),fmt=fmt.replace("%I",""+((d.getHours()+11)%12+1)),fmt=fmt.replace("%p",""+(d.getHours()<12?"AM":"PM")),fmt=fmt.replace("%B",""+ed.getLang("templatemanager_months_long").split(",")[d.getMonth()]),fmt=fmt.replace("%b",""+ed.getLang("templatemanager_months_short").split(",")[d.getMonth()]),fmt=fmt.replace("%A",""+ed.getLang("templatemanager_day_long").split(",")[d.getDay()]),fmt=fmt.replace("%a",""+ed.getLang("templatemanager_day_short").split(",")[d.getDay()]),fmt=fmt.replace("%%","%")):""}}),tinymce.PluginManager.add("templatemanager",tinymce.plugins.TemplatePlugin)}();