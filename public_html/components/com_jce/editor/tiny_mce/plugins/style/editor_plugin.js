/* jce - 2.6.5 | 2016-12-27 | http://www.joomlacontenteditor.net | Copyright (C) 2006 - 2016 Ryan Demmer. All rights reserved | GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html */
!function(){tinymce.create("tinymce.plugins.StylePlugin",{init:function(ed,url){ed.addCommand("mceStyleProps",function(){var applyStyleToBlocks=!1,blocks=ed.selection.getSelectedBlocks(),styles=[];1===blocks.length?styles.push(ed.selection.getNode().style.cssText):(tinymce.each(blocks,function(block){styles.push(ed.dom.getAttrib(block,"style"))}),applyStyleToBlocks=!0),ed.windowManager.open({file:ed.getParam("site_url")+"index.php?option=com_jce&view=editor&layout=plugin&plugin=style",width:620+parseInt(ed.getLang("style.delta_width",0)),height:420+parseInt(ed.getLang("style.delta_height",0)),inline:1,popup_css:!1},{applyStyleToBlocks:applyStyleToBlocks,plugin_url:url,styles:styles})}),ed.addCommand("mceSetElementStyle",function(ui,v){(e=ed.selection.getNode())&&(ed.dom.setAttrib(e,"style",v),ed.execCommand("mceRepaint"))}),ed.onNodeChange.add(function(ed,cm,n){cm.setDisabled("style","BODY"===n.nodeName||"BR"===n.nodeName&&n.getAttribute("data-mce-bogus"))}),ed.addButton("style",{title:"style.desc",cmd:"mceStyleProps"})},getInfo:function(){return{longname:"Style",author:"Moxiecode Systems AB",authorurl:"http://tinymce.moxiecode.com",infourl:"http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/style",version:tinymce.majorVersion+"."+tinymce.minorVersion}}}),tinymce.PluginManager.add("style",tinymce.plugins.StylePlugin)}();