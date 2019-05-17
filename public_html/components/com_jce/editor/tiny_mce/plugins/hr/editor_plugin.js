/* jce - 2.7.13 | 2019-05-07 | https://www.joomlacontenteditor.net | Copyright (C) 2006 - 2019 Ryan Demmer. All rights reserved | GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html */
!function(){var blocks="H1,H2,H3,H4,H5,H6,P,DIV,ADDRESS,PRE,FORM,TABLE,OL,UL,CAPTION,BLOCKQUOTE,CENTER,DL,DIR,FIELDSET,NOSCRIPT,NOFRAMES,MENU,ISINDEX,SAMP,SECTION,ARTICLE,HGROUP,ASIDE,FIGURE",VK=tinymce.VK,BACKSPACE=VK.BACKSPACE,DELETE=VK.DELETE;tinymce.create("tinymce.plugins.HorizontalRulePlugin",{init:function(ed,url){function isHR(n){return"HR"===n.nodeName&&/mce-item-(pagebreak|readmore)/.test(n.className)===!1}this.editor=ed,ed.addCommand("InsertHorizontalRule",function(ui,v){var se=ed.selection,n=se.getNode();if(/^(H[1-6]|P)$/.test(n.nodeName)){ed.undoManager.add(),ed.execCommand("mceInsertContent",!1,'<span id="mce_hr_marker" data-mce-type="bookmark">\ufeff</span>',{skip_undo:1});var marker=ed.dom.get("mce_hr_marker"),hr=ed.dom.create("hr"),p=ed.dom.getParent(marker,blocks,"BODY");ed.dom.split(p,marker);var ns=marker.nextSibling;if(!ns){var el=ed.getParam("forced_root_block")||"br";ns=ed.dom.create(el),"br"!=el&&(ns.innerHTML=" "),ed.dom.insertAfter(ns,marker)}ns&&ed.selection.setCursorLocation(ns,ns.childNodes.length),ed.dom.replace(hr,marker)}else ed.execCommand("mceInsertContent",!1,"<hr />");ed.undoManager.add()}),ed.addButton("hr",{title:"advanced.hr_desc",cmd:"InsertHorizontalRule"}),ed.onNodeChange.add(function(ed,cm,n){var s=isHR(n);cm.setActive("hr",s),ed.dom.removeClass(ed.dom.select("hr.mce-item-selected:not(.mce-item-pagebreak,.mce-item-readmore)"),"mce-item-selected"),s&&ed.dom.addClass(n,"mce-item-selected")}),ed.onKeyDown.add(function(ed,e){if(e.keyCode==BACKSPACE||e.keyCode==DELETE){var s=ed.selection,n=s.getNode();if(isHR(n)){var sib=n.previousSibling;ed.dom.remove(n),e.preventDefault(),ed.dom.isBlock(sib)||(sib=n.nextSibling,ed.dom.isBlock(sib)||(sib=null)),sib&&ed.selection.setCursorLocation(sib,sib.childNodes.length)}}})}}),tinymce.PluginManager.add("hr",tinymce.plugins.HorizontalRulePlugin)}();