/* jce - 2.8.0 | 2019-11-20 | https://www.joomlacontenteditor.net | Copyright (C) 2006 - 2019 Ryan Demmer. All rights reserved | GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html */
!function(){var each=tinymce.each;tinymce.create("tinymce.plugins.JoomlaPlugin",{init:function(ed,url){var self=this;self.editor=ed},createControl:function(n,cm){var ed=this.editor;if("joomla"!==n)return null;var plugins=ed.settings.joomla_xtd_buttons||[];if(!plugins.length)return null;var ctrl=cm.createSplitButton("joomla",{title:"joomla.buttons",icon:"joomla"});return ctrl.onRenderMenu.add(function(ctrl,menu){var vp=ed.dom.getViewPort();each(plugins,function(item){var href=item.href||"";href&&(href=ed.dom.decode(href),href=href.replace(/(\$jce)/gi,ed.id)),menu.add({id:ed.dom.uniqueId(),title:item.title,icon:item.icon,onclick:function(){href&&ed.windowManager.open({file:href,title:item.title,width:Math.max(vp.w-40,896),height:Math.max(vp.h-40,707),size:"mce-modal-landscape-full",addver:!1}),item.onclick&&new Function(item.onclick).apply()}})})}),ed.onRemove.add(function(){ctrl.destroy()}),ctrl}}),tinymce.PluginManager.add("joomla",tinymce.plugins.JoomlaPlugin)}();