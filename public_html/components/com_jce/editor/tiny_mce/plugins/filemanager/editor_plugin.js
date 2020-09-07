/* jce - 2.8.17 | 2020-08-27 | https://www.joomlacontenteditor.net | Copyright (C) 2006 - 2020 Ryan Demmer. All rights reserved | GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html */
!function(){var openwith={googledocs:{supported:["doc","docx","xls","xlsx","ppt","pptx","pdf","pages","ai","psd","tiff","dxf","svg","ps","ttf","xps","rar"],link:"https://docs.google.com/viewer?url=",embed:"https://docs.google.com/viewer?embedded=true&url="},officeapps:{supported:["doc","docx","xls","xlsx","ppt","pptx"],link:"https://view.officeapps.live.com/op/view.aspx?src=",embed:"https://view.officeapps.live.com/op/embed.aspx?src="}};tinymce.create("tinymce.plugins.FileManager",{init:function(ed,url){function isFile(n){return n&&ed.dom.is(n,".jce_file, .wf_file, .mce-item-iframe")}ed.addCommand("mceFileManager",function(){ed.selection.getNode();ed.windowManager.open({file:ed.getParam("site_url")+"index.php?option=com_jce&task=plugin.display&plugin=filemanager",size:"mce-modal-portrait-full"},{plugin_url:url})}),this.editor=ed,this.url=url,ed.addButton("filemanager",{title:"filemanager.desc",cmd:"mceFileManager"}),ed.onNodeChange.add(function(ed,cm,n,co){"IMG"!==n.nodeName&&"SPAN"!==n.nodeName||(n=ed.dom.getParent(n,"A")),cm.setActive("filemanager",co&&isFile(n)),n&&isFile(n)&&cm.setActive("filemanager",!0)}),ed.onInit.add(function(ed){ed.settings.compress.css||ed.dom.loadCSS(url+"/css/content.css"),ed&&ed.plugins.contextmenu&&ed.plugins.contextmenu.onContextMenu.add(function(th,m,e){m.add({title:"filemanager.desc",icon:"filemanager",cmd:"mceFileManager"})})})},getAttributes:function(data){var ed=this.editor,attr={style:{}},supported=["target","id","dir","class","charset","style","hreflang","lang","type","rev","rel","tabindex","accesskey"];return data.style&&tinymce.is(data.style,"string")&&(data.style=ed.dom.serializeStyle(ed.dom.parseStyle(data.style))),tinymce.each(supported,function(key){tinymce.is(data[key])&&(attr[key]=data[key])}),attr},insertUploadedFile:function(o){var ed=this.editor,data=this.getUploadConfig();if(data&&data.filetypes&&new RegExp(".("+data.filetypes.join("|")+")$","i").test(o.file)){var html,args={href:o.file,title:o.title||o.name},method=o.method||"link";if(o.openwith){var config=openwith[o.openwith]||!1;config&&(args.href=encodeURIComponent(decodeURIComponent(ed.documentBaseURI.toAbsolute(args.href,ed.settings.remove_script_host))),new RegExp(".("+config.supported.join("|")+")$","i").test(o.file)&&(args.href=config[method]+args.href))}if("embed"===method){args=tinymce.extend(args,{seamless:"seamless",src:args.href,width:o.width||640,height:o.height||480}),delete args.href;var html=ed.dom.createHTML("iframe",args,"");return ed.execCommand("mceInsertContent",!1,html,{skip_undo:1}),!0}html=[],o.features&&tinymce.each(o.features,function(n){var item=ed.dom.createHTML(n.node,n.attribs||{},n.html||"");html.push(item)});var cls=["wf_file"],attribs=this.getAttributes(o.attributes||{});return tinymce.each(attribs,function(val,key){"class"==key&&val?cls=cls.concat(val.split(" ")):args[key]=val}),args.class=cls.join(" "),1===html.length&&(html=[o.name]),ed.dom.create("a",args,html.join(""))}return!1},getUploadURL:function(file){var ed=this.editor,data=this.getUploadConfig();if(data&&data.filetypes){if(/\.(jpg|jpeg|png|tiff|bmp|gif|avif)$/i.test(file.name)&&(ed.plugins.imgmanager||ed.plugins.imgmanager_ext))return!1;if(/\.(html|htm|txt|md)$/i.test(file.name)&&ed.plugins.templatemanager)return!1;if(/\.(mp4|m4v|ogg|webm|ogv|mp3|oga)$/i.test(file.name)&&ed.plugins.mediamanager)return!1;if(new RegExp(".("+data.filetypes.join("|")+")$","i").test(file.name))return ed.getParam("site_url")+"index.php?option=com_jce&task=plugin.display&plugin=filemanager"}return!1},getUploadConfig:function(){var ed=this.editor,data=ed.getParam("filemanager",{});return data.upload||{}}}),tinymce.PluginManager.add("filemanager",tinymce.plugins.FileManager)}();