/* jce - 2.6.18 | 2017-07-20 | http://www.joomlacontenteditor.net | Copyright (C) 2006 - 2017 Ryan Demmer. All rights reserved | GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html */
!function(){function filter(content,items){return each(items,function(v){content=v.constructor==RegExp?content.replace(v,""):content.replace(v[0],v[1])}),content}function isWordContent(editor,content){return!!editor.settings.clipboard_paste_force_cleanup||(!!(/(content=\"OpenOffice.org[^\"]+\")/i.test(content)||ooRe.test(content)||/@page {/.test(content))||(/<font face="Times New Roman"|class="?Mso|style="[^"]*\bmso-|style='[^'']*\bmso-|w:WordDocument/i.test(content)||/class="OutlineElement/.test(content)||/id="?docs\-internal\-guid\-/.test(content)))}function trimHtml(html){function trimSpaces(all,s1,s2){return s1||s2?" ":" "}function getInnerFragment(html){var startFragment="<!--StartFragment-->",endFragment="<!--EndFragment-->",startPos=html.indexOf(startFragment);if(startPos!==-1){var fragmentHtml=html.substr(startPos+startFragment.length),endPos=fragmentHtml.indexOf(endFragment);if(endPos!==-1&&/^<\/(p|h[1-6]|li)>/i.test(fragmentHtml.substr(endPos+endFragment.length,5)))return fragmentHtml.substr(0,endPos)}return html}return html=filter(getInnerFragment(html),[/^[\s\S]*<body[^>]*>\s*|\s*<\/body[^>]*>[\s\S]*$/g,/<!--StartFragment-->|<!--EndFragment-->/g,[/( ?)<span class="Apple-converted-space">(\u00a0|&nbsp;)<\/span>( ?)/g,trimSpaces],/<br class="Apple-interchange-newline">/g,/<br>$/i])}function innerText(html){function walk(node){var name=node.name,currentNode=node;if("br"===name)return void(text+="\n");if(shortEndedElements[name]&&(text+=" "),ignoreElements[name])return void(text+=" ");if(3==node.type&&(text+=node.value),!node.shortEnded&&(node=node.firstChild))do walk(node);while(node=node.next);blockElements[name]&&currentNode.next&&(text+="\n","p"==name&&(text+="\n"))}var schema=new Schema,domParser=new DomParser({},schema),text="",shortEndedElements=schema.getShortEndedElements(),ignoreElements=tinymce.makeMap("script noscript style textarea video audio iframe object"," "),blockElements=schema.getBlockElements();return html=filter(html,[/<!\[[^\]]+\]>/g]),walk(domParser.parse(html)),text}function removeExplorerBrElementsAfterBlocks(self,o){var editor=self.editor,html=o.content,blockElements=[];each(editor.schema.getBlockElements(),function(block,blockName){blockElements.push(blockName)});var explorerBlocksRegExp=new RegExp("(?:<br>&nbsp;[\\s\\r\\n]+|<br>)*(<\\/?("+blockElements.join("|")+")[^>]*>)(?:<br>&nbsp;[\\s\\r\\n]+|<br>)*","g");html=filter(html,[[explorerBlocksRegExp,"$1"]]),html=filter(html,[[/<br><br>/g,"<BR><BR>"],[/<br>/g," "],[/<BR><BR>/g,"<br>"]]),o.content=html}function removeWebKitStyles(self,o){var editor=self.editor,content=o.content;if(!isWordContent(editor,o.content)){var webKitStyles=editor.settings.paste_webkit_styles;if(editor.settings.clipboard_paste_remove_styles_if_webkit!==!1&&"all"!=webKitStyles){if(webKitStyles&&(webKitStyles=webKitStyles.split(/[, ]/)),webKitStyles){var dom=editor.dom,node=editor.selection.getNode();content=content.replace(/(<[^>]+) style="([^"]*)"([^>]*>)/gi,function(all,before,value,after){var inputStyles=dom.parseStyle(value,"span"),outputStyles={};if("none"===webKitStyles)return before+after;for(var i=0;i<webKitStyles.length;i++){var inputValue=inputStyles[webKitStyles[i]],currentValue=dom.getStyle(node,webKitStyles[i],!0);/color/.test(webKitStyles[i])&&(inputValue=dom.toHex(inputValue),currentValue=dom.toHex(currentValue)),currentValue!=inputValue&&(outputStyles[webKitStyles[i]]=inputValue)}return outputStyles=dom.serializeStyle(outputStyles,"span"),outputStyles?before+' style="'+outputStyles+'"'+after:before+after})}else content=content.replace(/(<[^>]+) style="([^"]*)"([^>]*>)/gi,"$1$3");content=content.replace(/(<[^>]+) data-mce-style="([^"]+)"([^>]*>)/gi,function(all,before,value,after){return before+' style="'+value+'"'+after}),o.content=content}}}function preProcess(self,o){var rb,ed=self.editor,h=o.content;if(h=h.replace(/^\s*(&nbsp;)+/g,""),h=h.replace(/(&nbsp;|<br[^>]*>)+\s*$/g,""),self.pasteAsPlainText)return h;if(o.wordContent=isWordContent(ed,h),o.wordContent&&(h=WordFilter(ed,h)),ed.getParam("clipboard_paste_remove_attributes")){var attribs=ed.getParam("clipboard_paste_remove_attributes").split(",");h=h.replace(new RegExp(" ("+attribs.join("|")+')="([^"]+)"',"gi"),"")}if(rb=ed.getParam("forced_root_block")){var blocks="";h.indexOf("<br><br>")!=-1&&(tinymce.each(h.split("<br><br>"),function(block){blocks+="<"+rb+">"+block+"</"+rb+">"}),h=blocks)}ed.getParam("clipboard_paste_remove_spans")&&(h=h.replace(/<\/?(u|strike)[^>]*>/gi,""),ed.settings.convert_fonts_to_spans&&(h=h.replace(/<\/?(font)[^>]*>/gi,"")),h=h.replace(/<\/?(span)[^>]*>/gi,"")),ed.getParam("forced_root_block")||(ed.getParam("clipboard_paste_remove_empty_paragraphs",!0)&&(h=h.replace(/<p([^>]*)>(\s|&nbsp;|\u00a0)*<\/p>/gi,"")),h=h.replace(/<\/(p|div)>/gi,"<br /><br />").replace(/<(p|div)([^>]*)>/g,"").replace(/(<br \/>){2}$/g,"")),ed.getParam("clipboard_paste_convert_urls",!0)&&(h=convertURLs(ed,h)),ed.settings.verify_html===!1&&(h=h.replace(/<b\b([^>]*)>/gi,"<strong$1>"),h=h.replace(/<\/b>/gi,"</strong>")),o.content=h}function postProcess(self,o){var h,ed=self.editor,dom=ed.dom;if(self.pasteAsPlainText)return h;each(dom.select("span.Apple-style-span",o.node),function(n){dom.remove(n,1)}),ed.getParam("clipboard_paste_remove_styles")?each(dom.select("*[style]",o.node),function(el){el.removeAttribute("style"),el.removeAttribute("data-mce-style")}):processStyles(ed,o.node),o.wordContent&&each(dom.select("table[style], td[style], th[style]",o.node),function(n){var styles={};each(borderStyles,function(name){if(/-(top|right|bottom|left)-/.test(name)){var value=dom.getStyle(n,name);"currentcolor"===value&&(value=""),value&&/^\d[a-z]?/.test(value)&&(value=convertToPixels(value),value&&(value+="px")),styles[name]=value}}),each(styles,function(v,k){if(k.indexOf("-width")!==-1&&""===v){var s=k.replace(/-width/,"");delete styles[s+"-style"],delete styles[s+"-color"],delete styles[k]}}),dom.setStyle(n,"border",""),dom.setAttrib(n,"style",dom.serializeStyle(dom.parseStyle(dom.serializeStyle(styles,n.nodeName))),n.nodeName)});var imgRe=/(file:|data:image)\//i,uploader=ed.plugins.upload,canUpload=uploader&&uploader.plugins.length;if(each(dom.select("img",o.node),function(el){var s=dom.getAttrib(el,"src");!s||imgRe.test(s)?ed.getParam("clipboard_paste_upload_images",!0)&&canUpload?ed.dom.setAttrib(el,"data-mce-upload-marker","1"):dom.remove(el):dom.setAttrib(el,"src",ed.convertURL(s))}),isIE&&each(dom.select("a",o.node),function(el){each(dom.select("font,u"),function(n){dom.remove(n,1)})}),ed.getParam("clipboard_paste_remove_tags")&&dom.remove(dom.select(ed.getParam("clipboard_paste_remove_tags"),o.node),1),ed.getParam("clipboard_paste_keep_tags")){var tags=ed.getParam("clipboard_paste_keep_tags");dom.remove(dom.select("*:not("+tags+")",o.node),1)}ed.getParam("clipboard_paste_remove_spans")?dom.remove(dom.select("span",o.node),1):(ed.dom.remove(dom.select("span:empty",o.node)),each(dom.select("span",o.node),function(n){n.childNodes&&0!==n.childNodes.length||dom.remove(n),0===dom.getAttribs(n).length&&dom.remove(n,1)})),ed.getParam("clipboard_paste_remove_empty_paragraphs",!0)&&(dom.remove(dom.select("p:empty",o.node)),each(dom.select("p",o.node),function(n){var h=n.innerHTML;n.childNodes&&0!==n.childNodes.length&&!/^(\s|&nbsp;|\u00a0)?$/.test(h)||dom.remove(n)}))}function processStyles(editor,node){var dom=editor.dom,s=editor.getParam("clipboard_paste_retain_style_properties");s&&tinymce.is(s,"string")&&(styleProps=tinymce.explode(s),each(styleProps,function(style,i){if("border"===style)return styleProps=styleProps.concat(borderStyles),!0})),each(dom.select("*[style]",node),function(n){var ns={},x=0,styles=dom.parseStyle(n.style.cssText);each(styles,function(v,k){tinymce.inArray(styleProps,k)!=-1&&(ns[k]=v,x++)}),dom.setAttrib(n,"style",""),x>0?dom.setStyles(n,ns):"SPAN"!=n.nodeName||n.className||dom.remove(n,!0),tinymce.isWebKit&&n.removeAttribute("data-mce-style")}),each(dom.select("*[align]",node),function(el){var v=dom.getAttrib(el,"align");"left"!==v&&"right"!==v&&"center"!==v||(/(IFRAME|IMG|OBJECT|VIDEO|AUDIO|EMBED)/i.test(el.nodeName)?"center"===v?dom.setStyles(el,{margin:"auto",display:"block"}):dom.setStyle(el,"float",v):dom.setStyle(el,"text-align",v)),el.removeAttribute("align")})}function convertToPixels(v){return 0===parseInt(v,10)?0:(v.indexOf("pt")!==-1&&(v=parseInt(v,10),v=Math.ceil(v/1.33333),v=Math.abs(v)),v)}function isNumericList(text){var patterns,found="";return patterns={"uppper-roman":/^[IVXLMCD]{1,2}\.[ \u00a0]/,"lower-roman":/^[ivxlmcd]{1,2}\.[ \u00a0]/,"upper-alpha":/^[A-Z]{1,2}[\.\)][ \u00a0]/,"lower-alpha":/^[a-z]{1,2}[\.\)][ \u00a0]/,numeric:/^[0-9]+\.[ \u00a0]/,japanese:/^[\u3007\u4e00\u4e8c\u4e09\u56db\u4e94\u516d\u4e03\u516b\u4e5d]+\.[ \u00a0]/,chinese:/^[\u58f1\u5f10\u53c2\u56db\u4f0d\u516d\u4e03\u516b\u4e5d\u62fe]+\.[ \u00a0]/},text=text.replace(/^[\u00a0 ]+/,""),each(patterns,function(pattern,type){if(pattern.test(text))return found=type,!1}),found}function isBulletList(text){return/^[\s\u00a0]*[\u2022\u00b7\u00a7\u25CF]\s*/.test(text)}function WordFilter(editor,content){function convertFakeListsToProperLists(node){function getText(node){var txt="";if(3===node.type)return node.value;if(node=node.firstChild)do txt+=getText(node);while(node=node.next);return txt}function trimListStart(node,regExp){if(3===node.type&&regExp.test(node.value))return node.value=node.value.replace(regExp,""),!1;if(node=node.firstChild)do if(!trimListStart(node,regExp))return!1;while(node=node.next);return!0}function removeIgnoredNodes(node){if(node._listIgnore)return void node.remove();if(node=node.firstChild)do removeIgnoredNodes(node);while(node=node.next)}function convertParagraphToLi(paragraphNode,listName,start,type){var level=paragraphNode._listLevel||lastLevel;if(level!=lastLevel&&(level<lastLevel?currentListNode&&(currentListNode=currentListNode.parent.parent):(prevListNode=currentListNode,currentListNode=null)),currentListNode&&currentListNode.name==listName)currentListNode.append(paragraphNode);else{if(prevListNode=prevListNode||currentListNode,currentListNode=new Node(listName,1),type&&/roman|alpha/.test(type)){var style="list-style-type:"+type;currentListNode.attr({style:style,"data-mce-style":style})}start>1&&currentListNode.attr("start",""+start),paragraphNode.wrap(currentListNode)}paragraphNode.name="li",level>lastLevel&&prevListNode&&prevListNode.lastChild.append(currentListNode),lastLevel=level,removeIgnoredNodes(paragraphNode),trimListStart(paragraphNode,/^\u00a0+/),trimListStart(paragraphNode,/^\s*([\u2022\u00b7\u00a7\u25CF]|\w+\.)/),trimListStart(paragraphNode,/^\u00a0+/)}for(var currentListNode,prevListNode,lastLevel=1,elements=[],child=node.firstChild;"undefined"!=typeof child&&null!==child;)if(elements.push(child),child=child.walk(),null!==child)for(;"undefined"!=typeof child&&child.parent!==node;)child=child.walk();for(var i=0;i<elements.length;i++)if(node=elements[i],"p"==node.name&&node.firstChild){var type,nodeText=getText(node);if(isBulletList(nodeText)){convertParagraphToLi(node,"ul");continue}if(type=isNumericList(nodeText)){var matches=/([0-9]+)\./.exec(nodeText),start=1;matches&&(start=parseInt(matches[1],10)),convertParagraphToLi(node,"ol",start,type);continue}if(node._listLevel){convertParagraphToLi(node,"ul",1);continue}currentListNode=null}else prevListNode=currentListNode,currentListNode=null}function filterStyles(node,styleValue){var matches,outputStyles={},styles=editor.dom.parseStyle(styleValue);return each(styles,function(value,name){switch(name){case"mso-list":matches=/\w+ \w+([0-9]+)/i.exec(styleValue),matches&&(node._listLevel=parseInt(matches[1],10)),/Ignore/i.test(value)&&node.firstChild&&(node._listIgnore=!0,node.firstChild._listIgnore=!0);break;case"horiz-align":name="text-align";break;case"vert-align":name="vertical-align";break;case"font-color":case"mso-foreground":case"color":name="color","windowtext"==value&&(value="");break;case"mso-background":case"mso-highlight":name="background";break;case"font-weight":case"font-style":return void("normal"!=value&&(outputStyles[name]=value));case"mso-element":if(/^(comment|comment-list)$/i.test(value))return void node.remove()}return 0===name.indexOf("mso-comment")?void node.remove():void(0!==name.indexOf("mso-")&&(tinymce.inArray(pixelStyles,name)!==-1&&(value=convertToPixels(value)),validStyles&&validStyles[name]&&(outputStyles[name]=value)))}),/(bold)/i.test(outputStyles["font-weight"])&&(delete outputStyles["font-weight"],node.wrap(new Node("b",1))),/(italic)/i.test(outputStyles["font-style"])&&(delete outputStyles["font-style"],node.wrap(new Node("i",1))),outputStyles=editor.dom.serializeStyle(outputStyles,node.name),outputStyles?outputStyles:null}var retainStyleProperties,settings=editor.settings,validStyles={};if(content=content.replace(/<meta([^>]+)>/,""),content=content.replace(/<style([^>]*)>([\w\W]*?)<\/style>/gi,""),content=content.replace(/Version:[\d.]+\nStartHTML:\d+\nEndHTML:\d+\nStartFragment:\d+\nEndFragment:\d+/gi,""),content=content.replace(/<b[^>]+id="?docs-internal-[^>]*>/gi,""),content=content.replace(/<br class="?Apple-interchange-newline"?>/gi,""),retainStyleProperties=settings.clipboard_paste_retain_style_properties,retainStyleProperties&&each(retainStyleProperties.split(/[, ]/),function(style){return"border"===style?(each(borderStyles,function(name){validStyles[name]={}}),!0):void(validStyles[style]={})}),settings.paste_enable_default_filters!==!1){content=filter(content,[/<!--[\s\S]+?-->/gi,/<(!|script[^>]*>.*?<\/script(?=[>\s])|\/?(\?xml(:\w+)?|meta|link|style|\w:\w+)(?=[\s\/>]))[^>]*>/gi,[/<(\/?)s>/gi,"<$1strike>"],[/&nbsp;/gi," "],[/<span\s+style\s*=\s*"\s*mso-spacerun\s*:\s*yes\s*;?\s*"\s*>([\s\u00a0]*)<\/span>/gi,function(str,spaces){return spaces.length>0?spaces.replace(/./," ").slice(Math.floor(spaces.length/2)).split("").join(" "):""}]]),content=content.replace(/<table([^>]+)>/,function($1,$2){return"html4"!==settings.schema&&($2=$2.replace(/(border|cell(padding|spacing))="([^"]+)"/gi,"")),"<table"+$2+">"}),settings.forced_root_block&&(content=content.replace(/<br><br>/gi,""));var validElements=settings.paste_word_valid_elements;validElements||(validElements="-strong/b,-em/i,-u,-span,-p,-ol,-ul,-li,-h1,-h2,-h3,-h4,-h5,-h6,-p/div,-a[href|name],img[src|alt|width|height],sub,sup,strike,br,del,table[width],tr,td[colspan|rowspan|width],th[colspan|rowspan|width],thead,tfoot,tbody");var schema=new Schema({valid_elements:validElements,valid_children:"-li[p]"});each(schema.elements,function(rule){rule.attributes.class||(rule.attributes.class={},rule.attributesOrder.push("class")),rule.attributes.style||(rule.attributes.style={},rule.attributesOrder.push("style"))});var domParser=new DomParser({},schema);domParser.addAttributeFilter("style",function(nodes){for(var node,i=nodes.length;i--;)node=nodes[i],node.attr("style",filterStyles(node,node.attr("style"))),"span"==node.name&&node.parent&&!node.attributes.length&&node.unwrap()}),domParser.addAttributeFilter("class",function(nodes){for(var node,className,i=nodes.length;i--;)node=nodes[i],className=node.attr("class"),/^(MsoCommentReference|MsoCommentText|msoDel)$/i.test(className)&&node.remove(),node.attr("class",null)}),domParser.addNodeFilter("del",function(nodes){for(var i=nodes.length;i--;)nodes[i].remove()});var footnotes=editor.getParam("clipboard_paste_process_footnotes","convert");domParser.addNodeFilter("a",function(nodes){for(var node,href,name,i=nodes.length;i--;)if(node=nodes[i],href=node.attr("href"),name=node.attr("name"),href&&href.indexOf("#_msocom_")!=-1)node.remove();else if(href&&!name&&(href=editor.convertURL(href)),href&&0===href.indexOf("file://")&&(href=href.split("#")[1],href&&(href="#"+href)),href||name){if(name&&!/^_?(?:toc|edn|ftn)/i.test(name)){node.unwrap();continue}if(name&&"remove"===footnotes){node.remove();continue}if(name&&"unlink"===footnotes){node.unwrap();continue}node.attr({href:href,name:null}),"html4"===settings.schema?node.attr("name",name):node.attr("id",name)}else node.unwrap()});var rootNode=domParser.parse(content);return settings.paste_convert_word_fake_lists!==!1&&convertFakeListsToProperLists(rootNode),content=new Serializer({validate:settings.validate},schema).serialize(rootNode)}}function convertURLs(ed,content){var ex="([-!#$%&'*+\\./0-9=?A-Z^_`a-z{|}~]+@[-!#$%&'*+\\/0-9=?A-Z^_`a-z{|}~]+.[-!#$%&'*+\\./0-9=?A-Z^_`a-z{|}~]+)",ux="((news|telnet|nttp|file|http|ftp|https)://[-!#$%&'*+\\/0-9=?A-Z^_`a-z{|}~;]+.[-!#$%&'*+\\./0-9=?A-Z^_`a-z{|}~;]+)";return ed.getParam("autolink_url",!0)&&(content=content.replace(new RegExp("(=[\"']|>)?"+ux,"g"),function(a,b,c){return b?a:'<a href="'+c+'">'+c+"</a>"})),ed.getParam("autolink_email",!0)&&(content=content.replace(new RegExp("(=[\"']mailto:|>)?"+ex,"g"),function(a,b,c){return b?a:'<a href="mailto:'+c+'">'+c+"</a>"})),content}function getDataTransferItems(dataTransfer){var items={};if(dataTransfer){if(dataTransfer.getData){var legacyText=dataTransfer.getData("Text");legacyText&&legacyText.length>0&&(items["text/plain"]=legacyText)}if(dataTransfer.types)for(var i=0;i<dataTransfer.types.length;i++){var contentType=dataTransfer.types[i];items[contentType]=dataTransfer.getData(contentType)}}return items}function getClipboardContent(clipboardEvent){var content=getDataTransferItems(clipboardEvent.clipboardData||ed.getDoc().dataTransfer);return navigator.userAgent.indexOf(" Edge/")!==-1&&(content=tinymce.extend(content,{"text/html":""})),content}function isKeyboardPasteEvent(e){return VK.metaKeyPressed(e)&&86==e.keyCode||e.shiftKey&&45==e.keyCode}function hasContentType(clipboardContent,mimeType){return mimeType in clipboardContent&&clipboardContent[mimeType].length>0}var each=tinymce.each,VK=tinymce.VK,Event=tinymce.dom.Event,Schema=tinymce.html.Schema,DomParser=tinymce.html.DomParser,Serializer=tinymce.html.Serializer,Node=tinymce.html.Node,styleProps=(tinymce.html.Entities,["background","background-attachment","background-color","background-image","background-position","background-repeat","border","border-bottom","border-bottom-color","border-bottom-style","border-bottom-width","border-color","border-left","border-left-color","border-left-style","border-left-width","border-right","border-right-color","border-right-style","border-right-width","border-style","border-top","border-top-color","border-top-style","border-top-width","border-width","outline","outline-color","outline-style","outline-width","height","max-height","max-width","min-height","min-width","width","font","font-family","font-size","font-style","font-variant","font-weight","content","counter-increment","counter-reset","quotes","list-style","list-style-image","list-style-position","list-style-type","margin","margin-bottom","margin-left","margin-right","margin-top","padding","padding-bottom","padding-left","padding-right","padding-top","bottom","clear","clip","cursor","display","float","left","overflow","position","right","top","visibility","z-index","orphans","page-break-after","page-break-before","page-break-inside","widows","border-collapse","border-spacing","caption-side","empty-cells","table-layout","color","direction","letter-spacing","line-height","text-align","text-decoration","text-indent","text-shadow","text-transform","unicode-bidi","vertical-align","white-space","word-spacing"]),pixelStyles=["width","height","min-width","max-width","min-height","max-height","margin-top","margin-right","margin-bottom","margin-left","padding-top","padding-right","padding-bottom","padding-left","border-top-width","border-right-width","border-bottom-width","border-left-width"],borderStyles=["border","border-width","border-style","border-color","border-top","border-right","border-bottom","border-left","border-top-width","border-right-width","border-bottom-width","border-left-width","border-top-color","border-right-color","border-bottom-color","border-left-color","border-top-style","border-right-style","border-bottom-style","border-left-style"],ooRe=/(Version:[\d\.]+)\s*?((Start|End)(HTML|Fragment):[\d]+\s*?){4}/,Newlines=function(){var Entities=tinymce.html.Entities,isPlainText=function(text){return!/<(?:(?!\/?(?:div|p|br))[^>]*|(?:div|p|br)\s+\w[^>]+)>/.test(text)},toBRs=function(text){return text.replace(/\r?\n/g,"<br>")},openContainer=function(rootTag,rootAttrs){var key,attrs=[],tag="<"+rootTag;if("object"==typeof rootAttrs){for(key in rootAttrs)rootAttrs.hasOwnProperty(key)&&attrs.push(key+'="'+Entities.encodeAllRaw(rootAttrs[key])+'"');attrs.length&&(tag+=" "+attrs.join(" "))}return tag+">"},toBlockElements=function(text,rootTag,rootAttrs){var isLast,newlineFollows,isSingleNewline,pieces=text.split(/\r?\n/),i=0,len=pieces.length,stack=[],blocks=[],tagOpen=openContainer(rootTag,rootAttrs),tagClose="</"+rootTag+">";if(1===pieces.length)return text;for(;i<len;i++)isLast=i===len-1,newlineFollows=!isLast&&!pieces[i+1],isSingleNewline=!pieces[i]&&!stack.length,stack.push(pieces[i]?pieces[i]:"&nbsp;"),(isLast||newlineFollows||isSingleNewline)&&(blocks.push(stack.join("<br>")),stack=[]),newlineFollows&&i++;return 1===blocks.length?blocks[0]:tagOpen+blocks.join(tagClose+tagOpen)+tagClose},convert=function(text,rootTag,rootAttrs){return rootTag?toBlockElements(text,rootTag,rootAttrs):toBRs(text)};return{isPlainText:isPlainText,convert:convert,toBRs:toBRs,toBlockElements:toBlockElements}},isIE=tinymce.isIE||tinymce.isIE12;tinymce.create("tinymce.plugins.ClipboardPlugin",{init:function(ed,url){function pasteText(text){text=text.replace(/\r\n/g,"\n"),text=(new Newlines).convert(text,ed.settings.forced_root_block,ed.settings.forced_root_block_attrs),pasteHtml(text)}function pasteHtml(content){var o={content:content};self.onPreProcess.dispatch(self,o),o.node=ed.dom.create("div",0,o.content),self.onPostProcess.dispatch(self,o),o.content=ed.serializer.serialize(o.node,{getInner:1,forced_root_block:""}),self._insert(o.content),self.pasteAsPlainText=!1}function insertClipboardContent(clipboardContent){var content,isPlainTextHtml;if(content=clipboardContent["text/html"],content=trimHtml(content),content!==pasteBinDefaultContent)return self.pasteAsPlainText?(isPlainTextHtml=(new Newlines).isPlainText(content),content=hasContentType(clipboardContent,"text/plain")&&isPlainTextHtml?clipboardContent["text/plain"]:innerText(content),pasteText(content),!0):void pasteHtml(content)}function createPasteBin(){function getCaretRect(rng){var rects,textNode,node,container=rng.startContainer;if(rects=rng.getClientRects(),rects.length)return rects[0];if(rng.collapsed&&1==container.nodeType){for(node=container.childNodes[lastRng.startOffset];node&&3==node.nodeType&&!node.data.length;)node=node.nextSibling;if(node)return"BR"==node.tagName&&(textNode=dom.doc.createTextNode("\ufeff"),node.parentNode.insertBefore(textNode,node),rng=dom.createRng(),rng.setStartBefore(textNode),rng.setEndAfter(textNode),rects=rng.getClientRects(),dom.remove(textNode)),rects.length?rects[0]:void 0}}var scrollContainer,dom=ed.dom,body=ed.getBody(),viewport=ed.dom.getViewPort(ed.getWin()),scrollTop=viewport.y,top=20;if(lastRng=ed.selection.getRng(),lastRng.getClientRects){var rect=getCaretRect(lastRng);if(rect)top=scrollTop+(rect.top-dom.getPos(body).y);else{top=scrollTop;var container=lastRng.startContainer;container&&(3==container.nodeType&&container.parentNode!=body&&(container=container.parentNode),1==container.nodeType&&(top=dom.getPos(container,scrollContainer||body).y))}}pasteBinElm=dom.add(ed.getBody(),"div",{id:"mcepastebin",contentEditable:!0,"data-mce-bogus":"all",style:"position: absolute; top: "+top+"px;width: 10px; height: 10px; overflow: hidden; opacity: 0"},pasteBinDefaultContent),(isIE||tinymce.isGecko)&&dom.setStyle(pasteBinElm,"left","rtl"==dom.getStyle(body,"direction",!0)?65535:-65535),pasteBinElm.focus(),ed.selection.select(pasteBinElm,!0)}function removePasteBin(){if(pasteBinElm){for(var pasteBinClone;pasteBinClone=ed.dom.get("mcepastebin");)ed.dom.remove(pasteBinClone),ed.dom.unbind(pasteBinClone);lastRng&&ed.selection.setRng(lastRng)}pasteBinElm=lastRng=null}function getPasteBinHtml(){var pasteBinClones,i,clone,cloneHtml,html="";for(pasteBinClones=ed.dom.select("div[id=mcepastebin]"),i=0;i<pasteBinClones.length;i++)clone=pasteBinClones[i],clone.firstChild&&"mcepastebin"==clone.firstChild.id&&(clone=clone.firstChild),cloneHtml=clone.innerHTML,html!=pasteBinDefaultContent&&(html+=cloneHtml);return html}function getContentFromPasteBin(e){function block(e){e.preventDefault()}var dom=ed.dom;if(!dom.get("mcepastebin")){if(createPasteBin(),isIE&&isIE<11){var rng;rng=dom.doc.body.createTextRange(),rng.moveToElementText(pasteBinElm),rng.execCommand("Paste");var html=pasteBinElm.innerHTML;return html===pasteBinDefaultContent?void e.preventDefault():(removePasteBin(),setTimeout(function(){insertClipboardContent({"text/html":html})},0),void Event.cancel(e))}dom.bind(ed.getDoc(),"mousedown",block),dom.bind(ed.getDoc(),"keydown",block),window.setTimeout(function(){var html=getPasteBinHtml();removePasteBin(),insertClipboardContent({"text/html":html}),dom.unbind(ed.getDoc(),"mousedown",block),dom.unbind(ed.getDoc(),"keydown",block)},0)}}var self=this;self.editor=ed,self.url=url;var pasteBinElm,lastRng,keyboardPastePlainTextState,pasteBinDefaultContent="%MCEPASTEBIN%";this.canPaste=!1,this.pasteAsPlainText=!1,self.onPreProcess=new tinymce.util.Dispatcher(this),self.onPostProcess=new tinymce.util.Dispatcher(this),tinymce.isWebKit&&self.onPreProcess.add(function(self,o){removeWebKitStyles(self,o)}),isIE&&self.onPreProcess.add(function(self,o){removeExplorerBrElementsAfterBlocks(self,o)}),self.onPreProcess.add(function(self,o){preProcess(self,o)}),self.onPostProcess.add(function(self,o){postProcess(self,o)}),self.onPreProcess.add(function(pl,o){ed.execCallback("paste_preprocess",pl,o)}),self.onPostProcess.add(function(pl,o){ed.execCallback("paste_postprocess",pl,o)}),self.pasteText=ed.getParam("clipboard_paste_text",1),self.pasteHtml=ed.getParam("clipboard_paste_html",1),ed.addCommand("mceInsertClipboardContent",function(u,data){self.pasteAsPlainText=!1,data.text&&(self.pasteAsPlainText=!0,pasteText(data.text)),data.content&&pasteHtml(data.content)}),ed.onInit.add(function(){ed.plugins.contextmenu&&ed.plugins.contextmenu.onContextMenu.add(function(th,m,e){var c=ed.selection.isCollapsed();ed.getParam("clipboard_cut",1)&&m.add({title:"advanced.cut_desc",icon:"cut",cmd:"Cut"}).setDisabled(c),ed.getParam("clipboard_copy",1)&&m.add({title:"advanced.copy_desc",icon:"copy",cmd:"Copy"}).setDisabled(c),self.pasteHtml&&m.add({title:"clipboard.paste_desc",icon:"paste",cmd:"mcePaste"}),self.pasteText&&m.add({title:"clipboard.paste_text_desc",icon:"pastetext",cmd:"mcePasteText"})})}),ed.onKeyDown.add(function(ed,e){return!ed.getParam("clipboard_allow_cut",1)&&VK.metaKeyPressed&&88==e.keyCode?(e.preventDefault(),!1):!ed.getParam("clipboard_allow_copy",1)&&VK.metaKeyPressed&&67==e.keyCode?(e.preventDefault(),!1):void(isKeyboardPasteEvent(e)&&!e.isDefaultPrevented()&&(e.stopImmediatePropagation(),keyboardPastePlainTextState=e.shiftKey&&86==e.keyCode,keyboardPastePlainTextState&&(self.pasteAsPlainText=!0,getContentFromPasteBin(e))))}),ed.onPaste.add(function(ed,e){var clipboardContent=getClipboardContent(e);if(self.pasteAsPlainText&&hasContentType(clipboardContent,"text/plain")){e.preventDefault();var text=clipboardContent["text/plain"];return pasteText(text)}hasContentType(clipboardContent,"text/html")?(e.preventDefault(),insertClipboardContent(clipboardContent)):getContentFromPasteBin(e)}),ed.getParam("clipboard_paste_block_drop")&&ed.onInit.add(function(){ed.dom.bind(ed.getBody(),["dragend","dragover","draggesture","dragdrop","drop","drag"],function(e){return e.preventDefault(),e.stopPropagation(),!1})}),each(["Cut","Copy"],function(command){ed.addCommand(command,function(){var failed,doc=ed.getDoc();try{doc.execCommand(command,!1,null)}catch(ex){failed=!0}var msg=ed.getLang("clipboard_msg","");msg=msg.replace(/\%s/g,tinymce.isMac?"CMD":"CTRL"),!failed&&doc.queryCommandSupported(command)||(tinymce.isGecko?ed.windowManager.confirm(msg,function(state){state&&open("http://www.mozilla.org/editor/midasdemo/securityprefs.html","_blank")}):ed.windowManager.alert(ed.getLang("clipboard_no_support")))})}),each(["mcePasteText","mcePaste"],function(cmd){ed.addCommand(cmd,function(){var failed,doc=ed.getDoc();if(ed.getParam("clipboard_paste_use_dialog")||isIE&&!doc.queryCommandEnabled("Paste"))return self._openWin(cmd);try{self.pasteAsPlainText="mcePasteText"===cmd,doc.execCommand("Paste",!1,null)}catch(e){failed=!0}return doc.queryCommandEnabled("Paste")||(failed=!0),failed?self._openWin(cmd):void 0})}),self.pasteHtml&&ed.addButton("paste",{title:"clipboard.paste_desc",cmd:"mcePaste",ui:!0}),self.pasteText&&ed.addButton("pastetext",{title:"clipboard.paste_text_desc",cmd:"mcePasteText",ui:!0}),ed.getParam("clipboard_cut",1)&&ed.addButton("cut",{title:"advanced.cut_desc",cmd:"Cut",icon:"cut"}),ed.getParam("clipboard_copy",1)&&ed.addButton("copy",{title:"advanced.copy_desc",cmd:"Copy",icon:"copy"})},_openWin:function(cmd){var ed=this.editor;ed.windowManager.open({file:ed.getParam("site_url")+"index.php?option=com_jce&view=editor&plugin=clipboard&cmd="+cmd,width:parseInt(ed.getParam("clipboard_paste_dialog_width","450")),height:parseInt(ed.getParam("clipboard_paste_dialog_height","400")),inline:1,popup_css:!1},{cmd:cmd})},_insert:function(h,skip_undo){var ed=this.editor;if(ed.getParam("clipboard_paste_remove_empty_paragraphs",!0)&&(h=h.replace(/<p([^>]+)>(&nbsp;|\u00a0)?<\/p>/g,"")),ed.getParam("clipboard_paste_filter")){var re,rules=ed.getParam("clipboard_paste_filter").split(";");each(rules,function(s){re=/^\/.*\/(g|i|m)*$/.test(s)?new Function("return "+s)():new RegExp(s),h=h.replace(re,"")})}ed.execCommand("mceInsertContent",!1,h)}}),tinymce.PluginManager.add("clipboard",tinymce.plugins.ClipboardPlugin)}();