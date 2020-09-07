/* jce - 2.8.17 | 2020-08-27 | https://www.joomlacontenteditor.net | Copyright (C) 2006 - 2020 Ryan Demmer. All rights reserved | GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html */
!function(){var ImageManagerDialog={settings:{},selectedItems:[],init:function(){tinyMCEPopup.restoreSelection();var br,ed=tinyMCEPopup.editor,n=ed.selection.getNode(),self=this;$("button#insert").on("click",function(e){self.insert(),e.preventDefault()});var src=ed.convertURL(ed.dom.getAttrib(n,"src"));if(src=decodeURIComponent(src),$.each(this.settings.attributes,function(k,v){parseFloat(v)||$("#attributes-"+k).hide()}),Wf.init(),$("#alt").on("change",function(){""===this.value?$(this).removeClass("uk-edited"):$(this).addClass("uk-edited")}).addClass("uk-input-multiple"),$("#src, #popup_src").addClass("uk-input-multiple-disabled").attr("placeholder",tinyMCEPopup.getLang("imgmanager_ext.select_multiple","Multiple Image Selection")),$("#onmouseover, #onmouseout").addClass("uk-persistent-focus").on("click focus",function(){$("#onmouseover, #onmouseout").removeClass("uk-active"),$(this).addClass("uk-active")}),$("#responsive_picture").on("click",function(){$('input[name^="responsive_media_query"]').prop("disabled",!this.checked)}),$('input[name^="responsive_width_descriptor"], input[name^="responsive_pixel_density"]').on("change",function(){this.value=this.value.replace(/[^0-9\.]+/g,"")}),$("#responsive_tab").on("click focus",'input[name^="responsive_source"]',function(e){$('input[name^="responsive_source"]').removeClass("uk-active"),$(this).addClass("uk-active")}),$("body").on("click.persistent-focus",function(e){$(e.target).is(".uk-persistent-focus, li.file")||$(e.target).parents("li.file").length||$(".uk-persistent-focus").removeClass("uk-active")}),WFPopups.setup(),$("#popup_list").on("change",function(){var selected=self.selectedItems;1===selected.length&&self.selectFile(selected[0]),selected.length>1&&self.selectMultiple()}),n&&"IMG"==n.nodeName){$("#insert").button("option","label",tinyMCEPopup.getLang("update","Update",!0)),$("#src").val(src);var w=Wf.getAttrib(n,"width"),h=Wf.getAttrib(n,"height");$("#width").val(function(){return w?($(this).addClass("uk-isdirty"),w):h?void 0:n.width}),$("#height").val(function(){return h?($(this).addClass("uk-isdirty"),h):w?void 0:n.height}),$("#alt").val(function(){var val=ed.dom.getAttrib(n,"alt");if(val)return $(this).addClass("uk-edited"),val}),$("#title").val(ed.dom.getAttrib(n,"title")),$.each(["top","right","bottom","left"],function(){$("#margin_"+this).val(Wf.getAttrib(n,"margin-"+this))}),$("#border_width").val(function(){var v=Wf.getAttrib(n,"border-width");return 0==$('option[value="'+v+'"]',this).length&&$(this).append(new Option(v,v)),v}),$("#border_style").val(Wf.getAttrib(n,"border-style")),$("#border_color").val(Wf.getAttrib(n,"border-color")),$("#border").is(":checked")||$.each(["border_width","border_style","border_color"],function(i,k){$("#"+k).val(self.settings.defaults[k]).trigger("change")}),$("#align").val(Wf.getAttrib(n,"align")),$("#classes").val(function(){var values=ed.dom.getAttrib(n,"class");return $.trim(values)}).trigger("change"),$("#style").val(ed.dom.getAttrib(n,"style")),$("#id").val(ed.dom.getAttrib(n,"id")),$("#dir").val(ed.dom.getAttrib(n,"dir")),$("#lang").val(ed.dom.getAttrib(n,"lang")),$("#usemap").val(ed.dom.getAttrib(n,"usemap")),$("#loading").val(ed.dom.getAttrib(n,"loading")),$("#insert").button("option","label",ed.getLang("update","Update")),$("#longdesc").val(ed.convertURL(ed.dom.getAttrib(n,"longdesc"))),$("#onmouseout").val(src),$.each(["onmouseover","onmouseout"],function(i,key){var val=ed.dom.getAttrib(n,key);val=$.trim(val),val=val.replace(/^\s*this.src\s*=\s*\'([^\']+)\';?\s*$/,"$1").replace(/^\s*|\s*$/g,""),val=ed.convertURL(val),$("#"+key).val(val)}),br=n.nextSibling,br&&"BR"==br.nodeName&&br.style.clear&&$("#clear").val(br.style.clear),(popup=WFPopups.getPopup(n))&&$("#popup_src").val(popup.src);var srcset=ed.dom.getAttrib(n,"srcset");if(srcset){var sets=srcset.split(",");sets.length>1&&$(".uk-repeatable","#responsive_tab").trigger("repeatable:clone",sets.length-1),$.each(sets,function(i,set){var values=set.split(" ");$('input[name^="responsive_source"]').eq(i).val(values.shift()),$.each(values,function(x,v){v.indexOf("w")!==-1&&(v=v.replace(/[^0-9]+/g,""),$('input[name^="responsive_width_descriptor"]').eq(i).val(v)),v.indexOf("x")!==-1&&(v=v.replace(/[^0-9\.]+/g,""),$('input[name^="responsive_pixel_density"]').eq(i).val(v))})})}$("#responsive_sizes").val(ed.dom.getAttrib(n,"sizes"))}else Wf.setDefaults(this.settings.defaults);var currentSrc=$("#src").val();$("#src").filebrowser().on("filebrowser:onfileclick",function(e,file,data){$(file).data("thumbnail-src")&&(data.thumbnail={},$.each($(file).data(),function(key,val){key.indexOf("thumbnail-")!==-1&&(key=key.replace("thumbnail-"),data.thumbnail[key]=val)})),self.selectFile(file,data),currentSrc=$(file).data("url")}).on("filebrowser:onfileinsert",function(e,file,data){self.selectFile(file,data)}).on("filebrowser:createthumbnail",function(){self._createThumbnail()}).on("filebrowser:deletethumbnail",function(){self._deleteThumbnail()}).on("filebrowser:selectmultiple",function(){self.selectMultiple()}).on("filebrowser:onfiletoggle",function(e,file,data){self.selectOnToggle(file,data)}),Wf.updateStyles(),$(".uk-repeatable").on("repeatable:create",function(e,o,n){$('input[name^="responsive_source"]',n).focus()}),$(".uk-constrain-checkbox").on("constrain:change",function(e,elms){$(elms).addClass("uk-isdirty")}).trigger("constrain:update"),$(".uk-equalize-checkbox").trigger("equalize:update"),$("#border").change(),$(".uk-tabs").on("tabs.activate",function(e,tab,panel){if("popups_tab"===$(panel).attr("id")){var value=$("#popup_src").val();if(!value||value.indexOf("://")!==-1||value===currentSrc)return;currentSrc=value,$("#src").trigger("filebrowser:load",value)}if("image_tab"===$(panel).attr("id")){var value=$("#src").val();if(!value||value.indexOf("://")!==-1||value===currentSrc)return;currentSrc=value,$("#src").trigger("filebrowser:load",value)}}),$("#src, #popup_src").on("change",function(){var value=this.value;value&&value.indexOf("://")===-1&&value!==currentSrc&&$("#src").trigger("filebrowser:load",value)}),$(".uk-form-controls select:not(.uk-datalist)").datalist({input:!1}).trigger("datalist:update"),$(".uk-datalist").trigger("datalist:update")},refresh:function(){$("#src").trigger("filebrowser:refresh")},insert:function(){var ed=tinyMCEPopup.editor,self=this,n=ed.selection.getNode();return""===$("#src:enabled").val()?(Wf.Modal.alert(tinyMCEPopup.getLang("imgmanager_ext_dlg.no_src","Please enter a url for the image")),!1):(n&&"IMG"===n.nodeName&&""===ed.dom.getAttrib(n,"alt")&&this.insertAndClose(),void(""===$("#alt:enabled").val()?Wf.Modal.confirm(tinyMCEPopup.getLang("imgmanager_ext_dlg.missing_alt"),function(state){state&&self.insertAndClose()},{width:360,height:240}):this.insertAndClose()))},insertAndClose:function(){function _insert(el,args){if("string"==typeof el){ed.execCommand("mceInsertContent",!1,el,{skip_undo:1});var el=ed.dom.get("__mce_tmp");return n&&"A"===n.nodeName&&ed.dom.insertAfter(el,n),tinymce.each(ed.dom.select("img[data-popup-src]",el),function(img){popups.push({el:img,src:img.getAttribute("data-popup-src")}),img.removeAttribute("data-popup-src")}),void ed.dom.remove(el,1)}if(el&&"IMG"===el.nodeName)ed.dom.setAttribs(el,args),br&&"BR"===br.nodeName?(($("#clear").is(":disabled")||""===$("#clear").val())&&ed.dom.remove(br),$("#clear").is(":disabled")||""===$("#clear").val()||ed.dom.setStyle(br,"clear",$("#clear").val())):$("#clear").is(":disabled")||""===$("#clear").val()||(br=ed.dom.create("br"),ed.dom.setStyle(br,"clear",$("#clear").val()),ed.dom.insertAfter(br,el));else{var n=el;ed.execCommand("mceInsertContent",!1,'<img id="__mce_tmp" src="" />',{skip_undo:1});var el=ed.dom.get("__mce_tmp");n&&"A"===n.nodeName&&ed.dom.insertAfter(el,n),$("#clear").is(":disabled")||""===$("#clear").val()||(br=ed.dom.create("br"),ed.dom.setStyle(br,"clear",$("#clear").val()),ed.dom.insertAfter(br,el)),ed.dom.setAttrib(el,"id",""),ed.dom.setAttribs(el,args)}var stamp="";/\.(jpg|jpeg|png|avif|webp)$/.test(args.src)&&(stamp="?"+(new Date).getTime()),args.src=ed.convertURL(args.src,"src","IMG"),ed.dom.setAttrib(el,"src",args.src+stamp),ed.dom.setAttrib(el,"data-mce-src",args.src),el.removeAttribute("data-popup-src"),popups.push({el:el,src:args["data-popup-src"]})}var v,el,ed=tinyMCEPopup.editor,self=this,args={},br="";Wf.updateStyles(),tinyMCEPopup.restoreSelection(),ed.settings.inline_styles!==!1&&(args={vspace:"",hspace:"",border:"",align:""}),$.each(["src","width","height","alt","title","classes","style","id","dir","lang","usemap","longdesc","loading"],function(i,k){v=$("#"+k+":enabled").val(),"width"!=k&&"height"!=k||(v=self.settings.always_include_dimensions?$("#"+k).val():$("#"+k+".uk-isdirty").val()||""),"src"==k&&v&&(v=Wf.String.buildURI(v)),"classes"==k&&(k="class",v=$.trim(v)),args[k]=tinymce.is(v)?v:""});var srcset=[];$('input[name^="responsive_source"]').each(function(i){var values=[],s=$(this).val(),w=$('input[name^="responsive_width_descriptor"]').eq(i).val(),x=$('input[name^="responsive_pixel_density"]').eq(i).val();s&&(s=Wf.String.buildURI(s),s=ed.convertURL(s,"srcset","IMG"),values.push(s),w&&values.push(w+"w"),x&&values.push(x+"x"),srcset.push(values.join(" ")))}),srcset.length&&(args.srcset=srcset.join(",")),args.sizes=$("#responsive_sizes").val(),args.onmouseover=args.onmouseout="";var files=self.selectedItems;files.length>1&&$("#src").prop("disabled")===!1&&(files=$.grep(files,function(item){return item.url===$("#src").val()})),files.length||files.push($("#src").val());var popups=[],complete=!1,w=args.width,h=args.height,group=[];el=ed.selection.getNode(),br=el.nextSibling,$.each(files,function(i,file){if(files.length>1){var alt=$("input[name^=alt]").eq(i).val();$.extend(args,{src:file.url,alt:alt});var fw=file.width,fh=file.height;if(WFPopups.isEnabled()&&(args["data-popup-src"]=args.src,file.thumbnail)){var fw=file.thumbnail.width,fh=file.thumbnail.height;args.src=file.thumbnail.src}w&&h&&$.extend(args,Wf.sizeToFit({width:fw,height:fh},{width:w||fw,height:h||fh})),args.width!==fw&&w||delete args.width,args.height!==fh&&h||delete args.height;for(var k in args)""===args[k]&&delete args[k];group.push(ed.dom.createHTML("img",args))}else{var over=$("#onmouseover").val(),out=$("#onmouseout").val();over&&out&&over&&out&&(args=$.extend(args,{onmouseover:"this.src='"+ed.convertURL(over)+"';",onmouseout:"this.src='"+ed.convertURL(out)+"';"})),WFPopups.isEnabled()&&(args["data-popup-src"]=$("#popup_src").val()||args.src),_insert(el,args)}i==files.length-1&&(complete=!0)}),group.length&&_insert('<span id="__mce_tmp">'+group.join("")+"</span>",args),$.each(popups,function(i,o){var args={popup_src:o.src,title:o.title};i&&tinymce.each(ed.dom.select("img[data-mce-popup]"),function(n){var link=ed.dom.getParent(n,"a");link&&(o.el=link.nextSibling)}),ed.dom.setAttrib(o.el,"data-mce-popup",1),ed.selection.select(o.el),WFPopups.createPopup(o.el,args,i)}),ed.dom.setAttrib(ed.dom.select("img[data-mce-popup]"),"data-mce-popup",null),ed.undoManager.add(),ed.nodeChanged(),complete&&tinyMCEPopup.close()},resetMultipleInputs:function(){$(".uk-input-multiple").each(function(){var el=this,id=$(el).attr("id");$('input[type="hidden"][name="'+id+'[]"]').remove(),$(el).parent().removeClass("uk-form-icon uk-form-icon-flip").find(".uk-icon-edit").remove()})},toggleMultipleInputs:function(){var self=this,ed=tinyMCEPopup.editor;$(".uk-input-multiple").each(function(){function openModal(modal){var html="";$.each(self.selectedItems,function(i){var inp=$('input[name="'+id+'[]"]').eq(i),value=$(inp).val();html+='<div class="uk-form-row uk-grid uk-grid-collapse"><label class="uk-form-label uk-width-1-10">'+(i+1)+'.</label>     <div class="uk-form-controls uk-width-8-10">         <input type="text" value="'+value+'" />     </div></div>'}),$(".uk-modal-body",modal).append(html).find('input[type="text"]').each(function(i){$(this).on("change",function(){$('input[name="'+id+'[]"]').eq(i).val(this.value),selected[i][id]=this.value})})}var el=this,id=$(el).attr("id");$(el).attr("name",id+"[]"),$('input[type="hidden"][name="'+id+'[]"]').remove();var selected=self.selectedItems;$(el).parent().toggleClass("uk-form-icon uk-form-icon-flip",selected.length>1),selected.length<2||($.each(selected,function(i,item){var value="";value=item[id]||"",value||"alt"!==id||(value=Wf.String.stripExt(item.title).replace(/[_-]+/g," ")),0===i?$(el).val(value):$(el).parent().append('<input type="hidden" name="'+id+'[]" value="'+value+'" />'),item[id]=value}),$(el).siblings(".uk-icon-edit").length||($(el).prop("disabled",!0),$('<i class="uk-icon uk-icon-edit" role="button" />').on("click",function(){Wf.Modal.open($('label[for="'+el.id+'"]').text(),{width:300,buttons:[{text:ed.getLang("dlg.ok","Ok"),icon:"uk-icon-check",attributes:{class:"uk-button uk-modal-close"}}],open:function(){openModal(this)}})}).insertBefore(el)))})},setPopupSrc:function(file){var ed=tinyMCEPopup.editor,src=$(file).data("url");$(file).hasClass("thumbnail")&&Wf.Modal.confirm(ed.getLang("imgmanager_ext_dlg.use_thumbnail","Use associated thumbnail for popup link?"),function(state){if(state){var data={src:$(file).data("thumbnail-src"),width:$(file).data("thumbnail-width"),height:$(file).data("thumbnail-height")};$("#src").val(data.src);var name=Wf.String.stripExt($(file).attr("title")).replace(/[_-]+/g," ");$("#alt").val(name),data.width&&data.height?($("#width").val(data.width).data("tmp",data.width),$("#height").val(data.height).data("tmp",data.height)):($("#width").parent().append('<span class="loader"/>'),$("<img/>").attr("src",Wf.URL.toAbsolute(data.src)).on("load",function(){var w=this.width,h=this.height;$("#width").val(w).data("tmp",w),$("#height").val(h).data("tmp",h),$("#width~span.loader").remove()}))}}),$("#popup_src").val(src)},selectFile:function(file,data){$("#item-list").hasClass("ui-sortable")&&$("#item-list").sortable("destroy"),$(".uk-input-multiple, .uk-input-multiple-disabled").prop("disabled",!1),this.resetMultipleInputs(),data||(data={title:$(file).attr("title")},$.each(["url","preview","description","width","height"],function(i,key){data[key]=$(file).data(key)||""}));var name=data.title,src=data.url;if(!data.description){var filename=Wf.String.stripExt(name);data.description=filename.replace(/[-_]+/g," ")}this.selectedItems=[data];var tab=$(".uk-tabs-panel > .uk-active").attr("id");if("rollover_tab"===tab)""===$("#onmouseout").val()?$("#onmouseout").val(src):$("#onmouseover").val(src);else if("popups_tab"===tab)this.setPopupSrc(file);else if("responsive_tab"===tab)$("input.uk-active","#responsive_tab").val(src);else if($("#alt").hasClass("uk-edited")||$("#alt").val(data.description),$("#onmouseout").val(src),$("#src").val(src),data.width&&data.height)$.each(["width","height"],function(i,k){$("#"+k).val(data[k]).data("tmp",data[k]).removeClass("uk-edited").addClass("uk-text-muted")});else{var img=new Image;img.onload=function(){$.each(["width","height"],function(i,k){$("#"+k).val(img[k]).data("tmp",img[k]).removeClass("uk-isdirty")})},img.src=src}$("#sample").attr({src:data.preview}).attr(Wf.sizeToFit({width:data.width,height:data.height},{width:80,height:60}))},updateSelectedItems:function(){var self=this,deffered=$.Deferred();return $("#src").trigger("filebrowser:insert",function(selected,data){$.each(selected,function(i,item){$(item).data("thumbnail-src")&&(data[i].thumbnail={src:$(item).data("thumbnail-src"),width:$(item).data("thumbnail-width"),height:$(item).data("thumbnail-height")})}),self.selectedItems=data,deffered.resolve(selected,data)}),deffered.promise()},selectOnToggle:function(file,data){if(!data.state)return this.selectMultiple();var items=this.selectedItems;items.length>0&&data.url!==items[0].url&&this.selectMultiple()},selectMultiple:function(){var self=this,ed=tinyMCEPopup.editor;$("#item-list").hasClass("ui-sortable")&&$("#item-list").sortable("destroy"),$(".uk-input-multiple, .uk-input-multiple-disabled").prop("disabled",!1);var tab=$(".uk-tabs-panel > .uk-active").attr("id");this.selectedItems=[],self.updateSelectedItems().done(function(selected,data){if(!data.length)return!1;var file=data[0];if(data.length>1&&"image_tab"===tab&&($("#src").val(file.url),self.toggleMultipleInputs(),$(".uk-input-multiple, .uk-input-multiple-disabled").prop("disabled",!0),$.each(["width","height"],function(i,key){var val=file[key]||"";val&&$("#"+key).val(val).data("tmp",val)})),data.length&&"responsive_tab"===tab){var inp=$('input[name^="responsive_source"]'),diff=data.length-inp.length;diff&&$(".uk-repeatable","#responsive_tab").trigger("repeatable:clone",diff),$.each(data,function(i,props){$('input[name^="responsive_source"]').eq(i).val(props.url)}),self.resetMultipleInputs()}var popup=WFPopups.isEnabled();if(popup&&"popups_tab"===tab){$(".uk-input-multiple, .uk-input-multiple-disabled").prop("disabled",!0);var item=selected[0];$(item).hasClass("thumbnail")&&Wf.Modal.confirm(ed.getLang("imgmanager_ext_dlg.use_thumbnail","Use associated thumbnail for popup link?"),function(state){state&&$.each(["width","height"],function(i,k){$("#"+k).val($(item).data("thumbnail-"+k)).data("tmp",$(item).data("thumbnail-"+k))})}),self.toggleMultipleInputs()}});var grid=$("#browser").hasClass("view-mode-grid");$("#item-list").sortable({items:".file.selected",axis:!grid&&"y",placeholder:"uk-state-highlight",start:function(e,ui){$(ui.placeholder).css({width:$(ui.item).width(),height:$(ui.item).height()}),grid&&$(ui.placeholder).addClass("file thumbnail-preview thumbnail-loaded"),$(".file","#item-list").not(".selected, .uk-state-highlight").addClass("uk-state-disabled")},stop:function(e,ui){$(".uk-state-disabled","#item-list").removeClass("uk-state-disabled"),self.updateSelectedItems().done(function(){self.selectMultiple()}),$("#src").trigger("filebrowser:sort")}}).disableSelection()},_deleteThumbnail:function(){var item=$.fn.filebrowser.getselected()[0];Wf.Modal.confirm(tinyMCEPopup.getLang("imgmanager_ext_dlg.delete_thumbnail","Delete Thumbnail?"),function(state){if(state){var id=$(item).attr("id");$.fn.filebrowser.status({message:tinyMCEPopup.getLang("imgmanager_ext_dlg.delete_thumbnail_message","Deleting Thumbnail..."),state:"load"}),Wf.JSON.request("deleteThumbnail",[id],function(o){o.error.length&&Wf.Modal.alert(o.error),$.fn.filebrowser.load($(item).attr("url"))})}})},_createThumbnail:function(){var self=this,s=self.settings,item=$.fn.filebrowser.getselected()[0];s.thumbnail_width||s.thumbnail_height||(s.thumbnail_width=120,s.thumbnail_height=90),Wf.Modal.dialog(tinyMCEPopup.getLang("imgmanager_ext_dlg.create_thumbnail","Create Thumbnail"),"",{text:tinyMCEPopup.getLang("dlg.name","Name"),id:"thumbnail-create-dialog",width:680,height:420,dialogClass:"thumbnail",onOpen:function(){$("#thumbnail-create-dialog").thumbnail({src:$(item).data("preview")+"?"+(new Date).getTime(),values:{width:s.thumbnail_width,height:s.thumbnail_height,quality:s.thumbnail_quality}})},buttons:[{text:Wf.translate("save","Save"),icon:"uk-icon-check",attributes:{class:"uk-button-primary uk-modal-close"},click:function(){var data=$("#thumbnail-create-dialog").thumbnail("save"),args={json:[$(item).attr("id")]},box={sw:data.sw,sh:data.sh,sx:data.sx,sy:data.sy};$.merge(args.json,[data.width,data.height,data.quality,box]),$.fn.filebrowser.status({message:tinyMCEPopup.getLang("imgmanager_ext_dlg.create_thumbnail_message","Creating Thumbnail..."),state:"load"}),Wf.JSON.request("createThumbnail",args,function(o){o&&o.error&&o.error.length&&Wf.Modal.alert(o.error||"Unable to create thumbnail"),$.fn.filebrowser.load($(item).attr("url"))},self)}}]})}};window.ImageManagerDialog=ImageManagerDialog,$(document).ready(function(){ImageManagerDialog.init()})}();