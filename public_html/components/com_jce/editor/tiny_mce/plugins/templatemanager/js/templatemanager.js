/* jce - 2.8.12 | 2020-05-12 | https://www.joomlacontenteditor.net | Copyright (C) 2006 - 2020 Ryan Demmer. All rights reserved | GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html */
var TemplateManager={settings:{},templateHTML:null,init:function(){var self=this;$("button#insert").on("click",function(e){self.insert(),e.preventDefault()});var ed=tinyMCEPopup.editor,s=ed.selection,n=s.getNode(),src=ed.convertURL(ed.dom.getAttrib(n,"src"));$(document.body).append('<input type="hidden" id="src" value="'+src+'" />'),Wf.init(),$("#src").filebrowser().on("filebrowser:onfileclick",function(e,file){self.selectFile(file)}).on("filebrowser:createtemplate",function(e,file){self.createTemplate()}),$("#insert").prop("disabled",!0)},insert:function(){tinyMCEPopup.execCommand("mceInsertTemplate",!1,{content:this.getHTML(),selection:tinyMCEPopup.editor.selection.getContent()}),tinyMCEPopup.close()},getHTML:function(){return this.templateHTML},setHTML:function(h){this.templateHTML=tinymce.trim(h)},createTemplate:function(){var ed=tinyMCEPopup.editor,content=ed.getContent(),selection=ed.selection.getContent();""===selection&&(selection=content),Wf.Modal.prompt(ed.getLang("templatemanager_dlg.new_template","Create Template"),function(name){$.fn.filebrowser.status({message:ed.getLang("dlg.message_load","Loading..."),state:"load"});var dir=$.fn.filebrowser.getcurrentdir();Wf.JSON.request("createTemplate",{json:[dir,name],data:selection},function(o){$("#src").trigger("filebrowser:load",name)})},{text:ed.getLang("dlg.name","Name"),open:function(e){$(".uk-modal-footer .uk-text",e.target).text(Wf.translate("create","Create"))}})},selectFile:function(file){var self=this;$("#insert").addClass("loading").prop("disabled",!0),Wf.JSON.request("loadTemplate",file.id,function(o){o&&!o.error&&self.setHTML(o),$("#insert").removeClass("loading").prop("disabled",!1)})}};tinyMCEPopup.onInit.add(TemplateManager.init,TemplateManager);