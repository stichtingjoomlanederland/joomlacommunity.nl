/* jce - 2.8.2 | 2019-12-18 | https://www.joomlacontenteditor.net | Copyright (C) 2006 - 2019 Ryan Demmer. All rights reserved | GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html */
var IframeDialog={settings:{},init:function(){var v,self=this,ed=tinyMCEPopup.editor,s=ed.selection,n=s.getNode(),data={};if(tinyMCEPopup.restoreSelection(),TinyMCE_Utils.fillClassList("classlist"),Wf.init(),this.settings.file_browser&&Wf.createBrowsers($("#src"),function(files,data){file=data[0],$("#src").val(file.url),file.width&&$("#width").val(file.width).data("tmp",file.width).trigger("change"),file.height&&$("#height").val(file.height).data("tmp",file.height).trigger("change")}),$("#insert").on("click",function(){self.insert()}),n=ed.dom.getParent(n,".mce-item-iframe")){if("IMG"===n.nodeName)try{data=JSON.parse(ed.dom.getAttrib(n,"data-mce-json"))}catch(e){}else if(ed.dom.hasClass(n,"mce-item-preview")){var ifr=n.firstChild,data={iframe:{}};tinymce.each(ifr.attributes,function(attr){data.iframe[attr.name]=attr.value}),n=ifr}data&&data.iframe&&($(".uk-button-text","#insert").text(tinyMCEPopup.getLang("update","Update",!0)),data=data.iframe,$.each(data,function(k,v){"scrolling"===k&&"auto"===v&&(v=""),$("#"+k).is(":checkbox")?$("#"+k).prop("checked",!!v):("src"==k&&(v=ed.convertURL(v)),$("#"+k).val(v))}),$.each(["class","width","height","style","id","longdesc","align"],function(i,k){switch(v=ed.dom.getAttrib(n,k),k){case"class":$("#classes").val(function(){var elm=this;return v=v.replace(/mce-item-(\w+)/gi,"").replace(/\s+/g," "),v=$.trim(v),v=v.split(" "),$.each(v,function(i,value){return value=$.trim(value),!value||" "===value||void(0==$('option[value="'+value+'"]',elm).length&&$(elm).append(new Option(value,value)))}),v}).trigger("change");break;case"width":case"height":v=ed.dom.getAttrib(n,k)||ed.dom.getStyle(n,k)||"",v.indexOf("%")===-1&&(v=parseFloat(v)),$("#"+k).val(v).data("tmp",v).trigger("change");break;case"align":$("#"+k).val(self.getAttrib(n,k));break;case"style":var styles=ed.dom.parseStyle(v);styles.width="",styles.height="",$("#"+k).val(ed.dom.serializeStyle(styles));break;default:$("#"+k).val(v)}}),$.each(["top","right","bottom","left"],function(i,k){v=self.getAttrib(n,"margin-"+k),$("#margin_"+k).val(v)}))}else Wf.setDefaults(this.settings.defaults);WFAggregator.setup({embed:!1}),$("#src").on("change",function(){var data={},v=this.value;(s=WFAggregator.isSupported(v))?(data=WFAggregator.getAttributes(s,v),$(".aggregator_option, .options_description","#options_tab").hide().filter("."+s).show()):$(".options_description","#options_tab").show();for(n in data){var $el=$("#"+n),v=data[n];"width"==n||"height"==n?""!==$el.val()&&$el.hasClass("edited")!==!1||$("#"+n).val(data[n]).data("tmp",data[n]).trigger("change"):$el.is(":checkbox")?(v=parseInt(v),$el.attr("checked",v).prop("checked",v)):$el.val(v)}}).trigger("change"),$(".uk-equalize-checkbox").trigger("equalize:update"),$(".uk-form-controls select:not(.uk-datalist)").datalist({input:!1}).trigger("datalist:update"),$(".uk-datalist").trigger("datalist:update")},getAttrib:function(e,at){return Wf.getAttrib(e,at)},checkPrefix:function(n){var self=this,v=$(n).val();/^\s*www./i.test(v)?Wf.Modal.confirm(tinyMCEPopup.getLang("iframe_dlg.is_external","The URL you entered seems to be an external link, do you want to add the required http:// prefix?"),function(state){state&&$(n).val("http://"+v),self.insert()}):this.insertAndClose()},insert:function(){tinyMCEPopup.editor;return""===$("#src").val()?(Wf.Modal.alert(tinyMCEPopup.getLang("iframe_dlg.no_src","Please enter a url for the iframe")),!1):""===$("#width").val()||""===$("#height").val()?(Wf.Modal.alert(tinyMCEPopup.getLang("iframe_dlg.no_dimensions","Please enter a width and height for the iframe")),!1):this.checkPrefix($("#src"))},insertAndClose:function(){tinyMCEPopup.restoreSelection();var ed=tinyMCEPopup.editor,args={},n=ed.selection.getNode();tinymce.each(["classes","style","id","title"],function(k){var v=$("#"+k).val();""!==v&&("classes"==k&&("array"===$.type(v)&&(v=v.join(" ")),k="class"),args[k]=v)});var attr=this.getParameters(),w=$("#width").val()||384,h=$("#height").val()||216;if(args.width=w,args.height=h,args.class=$.trim("mce-item-media mce-item-iframe "+args.class),n=ed.dom.getParent(n,".mce-item-iframe"),n&&"IMG"===n.nodeName)$.extend(args,{src:"data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7","data-mce-json":this.serializeParameters(attr),"data-mce-type":"iframe","data-mce-width":w,"data-mce-height":h}),ed.dom.setAttribs(n,args),ed.dom.setStyles(n,{width:w,height:h});else{var innerHTML=$("#html").val();tinymce.each(attr,function(v,k){""!==v&&(args[k]=v)});var html=ed.dom.createHTML("iframe",args,innerHTML);ed.execCommand("mceInsertContent",!1,html,{skip_undo:1}),ed.undoManager.add()}tinyMCEPopup.close()},getParameters:function(){var s,v,ed=tinyMCEPopup.editor,data={};return tinymce.each(["src","name","scrolling","frameborder","allowtransparency","allowfullscreen"],function(k){if(!$("#"+k).prop("disabled")&&(v=$("#"+k).is(":checkbox")?$("#"+k).is(":checked")?1:0:$("#"+k).val(),""!==v)){if("src"==k&&(v=v.replace(/&amp;/gi,"&"),v=ed.convertURL(v)),"html4"!==ed.settings.schema&&"frameborder"===k)return!0;"scrolling"===k&&"auto"===v&&(v=null),null!==v&&tinymce.is(v)&&(data[k]=v)}}),(s=WFAggregator.isSupported(data.src))&&$.extend(!0,data,WFAggregator.getValues(s,data.src)),data},serializeParameters:function(attr){var o={iframe:attr};return JSON.stringify(o)}};tinyMCEPopup.onInit.add(IframeDialog.init,IframeDialog);