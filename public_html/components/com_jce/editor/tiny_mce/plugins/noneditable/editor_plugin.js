/* jce - 2.8.2 | 2019-12-18 | https://www.joomlacontenteditor.net | Copyright (C) 2006 - 2019 Ryan Demmer. All rights reserved | GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html */
!function(){tinymce.create("tinymce.plugins.NonEditablePlugin",{init:function(editor){function hasClass(checkClassName){return function(node){return(" "+node.attr("class")+" ").indexOf(checkClassName)!==-1}}function convertRegExpsToNonEditable(e){function replaceMatchWithSpan(match){var args=arguments,index=args[args.length-2],prevChar=index>0?content.charAt(index-1):"";if('"'===prevChar)return match;if(">"===prevChar){var findStartTagIndex=content.lastIndexOf("<",index);if(findStartTagIndex!==-1){var tagHtml=content.substring(findStartTagIndex,index);if(tagHtml.indexOf('contenteditable="false"')!==-1)return match}}return'<span class="'+cls+'" data-mce-content="'+editor.dom.encode(args[0])+'">'+editor.dom.encode("string"==typeof args[1]?args[1]:args[0])+"</span>"}var i=nonEditableRegExps.length,content=e.content,cls=tinymce.trim(nonEditClass);if("raw"!=e.format){for(;i--;)content=content.replace(nonEditableRegExps[i],replaceMatchWithSpan);e.content=content}}var nonEditableRegExps,contentEditableAttrName="contenteditable",editClass=tinymce.trim(editor.getParam("noneditable_editable_class","mceEditable")),nonEditClass=tinymce.trim(editor.getParam("noneditable_noneditable_class","mceNonEditable")),hasEditClass=hasClass(editClass),hasNonEditClass=hasClass(nonEditClass);nonEditableRegExps=editor.getParam("noneditable_regexp"),nonEditableRegExps&&!nonEditableRegExps.length&&(nonEditableRegExps=[nonEditableRegExps]),editor.onPreInit.add(function(){editor.formatter.register("noneditable",{block:"div",wrapper:!0,onformat:function(elm,fmt,vars){tinymce.each(vars,function(value,key){editor.dom.setAttrib(elm,key,value)})}}),nonEditableRegExps&&editor.on("BeforeSetContent",convertRegExpsToNonEditable),editor.parser.addAttributeFilter("class",function(nodes){for(var node,i=nodes.length;i--;)node=nodes[i],hasEditClass(node)?node.attr(contentEditableAttrName,"true"):hasNonEditClass(node)&&node.attr(contentEditableAttrName,"false")}),editor.serializer.addAttributeFilter(contentEditableAttrName,function(nodes){for(var node,i=nodes.length;i--;)node=nodes[i],(hasEditClass(node)||hasNonEditClass(node))&&(nonEditableRegExps&&node.attr("data-mce-content")?(node.name="#text",node.type=3,node.raw=!0,node.value=node.attr("data-mce-content")):node.attr(contentEditableAttrName,null))})})}}),tinymce.PluginManager.add("noneditable",tinymce.plugins.NonEditablePlugin)}();