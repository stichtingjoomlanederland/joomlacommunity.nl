/* jce - 2.9.3 | 2021-02-25 | https://www.joomlacontenteditor.net | Copyright (C) 2006 - 2021 Ryan Demmer. All rights reserved | GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html */
!function(){tinymce.each;tinymce.create("tinymce.plugins.IframePlugin",{init:function(ed,url){var t=this;t.editor=ed,t.url=url,ed.addCommand("mceIframe",function(){ed.windowManager.open({file:ed.getParam("site_url")+"index.php?option=com_jce&task=plugin.display&plugin=iframe",size:"mce-modal-landscape-xlarge"},{plugin_url:url})}),ed.addButton("iframe",{title:"iframe.desc",cmd:"mceIframe"}),ed.onNodeChange.add(function(ed,cm,n){n=ed.dom.getParent(n,".mce-object-iframe")||n;var state="iframe"===n.getAttribute("data-mce-object")||"IFRAME"===n.nodeName;cm.setActive("iframe",state)})}}),tinymce.PluginManager.add("iframe",tinymce.plugins.IframePlugin,["media"])}();