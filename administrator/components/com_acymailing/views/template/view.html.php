<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php


class TemplateViewTemplate extends acymailingView{

	var $selection = array('a.tempid', 'a.name', 'a.description', 'a.created', 'a.published', 'a.premium', 'a.ordering', 'a.thumb');
	var $filters = array();
	var $button = true;
	var $chosen = false;

	function display($tpl = null){

		$function = $this->getLayout();
		if(method_exists($this, $function)) $this->$function();

		parent::display($tpl);
	}

	function listing(){
		$app = JFactory::getApplication();
		$pageInfo = new stdClass();
		$pageInfo->filter = new stdClass();
		$pageInfo->filter->order = new stdClass();
		$pageInfo->limit = new stdClass();
		$pageInfo->elements = new stdClass();
		$config = acymailing_config();

		$paramBase = ACYMAILING_COMPONENT.'.'.$this->getName().$this->getLayout();
		$pageInfo->filter->order->value = $app->getUserStateFromRequest($paramBase.".filter_order", 'filter_order', 'a.ordering', 'cmd');
		$pageInfo->filter->order->dir = $app->getUserStateFromRequest($paramBase.".filter_order_Dir", 'filter_order_Dir', 'asc', 'word');
		if(strtolower($pageInfo->filter->order->dir) !== 'desc') $pageInfo->filter->order->dir = 'asc';
		$pageInfo->search = $app->getUserStateFromRequest($paramBase.".search", 'search', '', 'string');
		$pageInfo->search = JString::strtolower(trim($pageInfo->search));
		$pageInfo->category = $app->getUserStateFromRequest($paramBase.".category", 'category', '0', 'string');

		$pageInfo->limit->value = $app->getUserStateFromRequest($paramBase.'.list_limit', 'limit', $app->getCfg('list_limit'), 'int');
		$pageInfo->limit->start = $app->getUserStateFromRequest($paramBase.'.limitstart', 'limitstart', 0, 'int');

		$database = JFactory::getDBO();

		if(!empty($pageInfo->search)){
			$searchVal = '\'%'.acymailing_getEscaped($pageInfo->search, true).'%\'';
			$this->filters[] = "a.name LIKE $searchVal OR a.description LIKE $searchVal OR a.tempid LIKE $searchVal";
		}

		if(!empty($pageInfo->category) && $pageInfo->category != JText::_('ACY_ALL_CATEGORIES')){
			$this->filters[] = 'a.category LIKE '.$database->Quote($pageInfo->category);
		}

		$query = 'SELECT '.implode(',', $this->selection).' FROM '.acymailing_table('template').' as a';
		if(!empty($this->filters)){
			$query .= ' WHERE ('.implode(') AND (', $this->filters).')';
		}
		if(!empty($pageInfo->filter->order->value)){
			$query .= ' ORDER BY '.$pageInfo->filter->order->value.' '.$pageInfo->filter->order->dir;
		}
		$database->setQuery($query, $pageInfo->limit->start, $pageInfo->limit->value);

		try{
			$this->rows = $database->loadObjectList();
		}catch(Exception $e){
			$this->rows = null;
		}

		if($this->rows === null){
			acymailing_display(isset($e) ? $e->getMessage() : substr(strip_tags($database->getErrorMsg()), 0, 200).'...', 'error');
			if(file_exists(ACYMAILING_BACK.'install.acymailing.php')){
				include_once(ACYMAILING_BACK.'install.acymailing.php');
				$installClass = new acymailingInstall();
				$installClass->fromVersion = '4.1.0';
				$installClass->update = true;
				$installClass->updateSQL();
			}
		}

		$queryCount = 'SELECT COUNT(a.tempid) FROM '.acymailing_table('template').' as a';
		if(!empty($this->filters)){
			$queryCount .= ' WHERE ('.implode(') AND (', $this->filters).')';
		}
		$database->setQuery($queryCount);
		$pageInfo->elements->total = $database->loadResult();

		$pageInfo->elements->page = count($this->rows);

		jimport('joomla.html.pagination');
		$pagination = new JPagination($pageInfo->elements->total, $pageInfo->limit->start, $pageInfo->limit->value);

		if($this->button){
			$acyToolbar = acymailing::get('helper.toolbar');
			$acyToolbar->popup('import', JText::_('IMPORT'), "index.php?option=com_acymailing&ctrl=template&task=upload&tmpl=component");

			$acyToolbar->custom('export', JText::_('ACY_EXPORT'), 'export', true);
			$acyToolbar->divider();
			$acyToolbar->add();
			$acyToolbar->edit();
			if(acymailing_isAllowed($config->get('acl_templates_copy', 'all'))){
				$acyToolbar->copy();
			}
			if(acymailing_isAllowed($config->get('acl_templates_delete', 'all'))) $acyToolbar->delete();

			$acyToolbar->divider();
			$acyToolbar->help('template', 'listing');
			$acyToolbar->setTitle(JText::_('ACY_TEMPLATES'), 'template');
			$acyToolbar->display();
		}


		$toggleClass = acymailing_get('helper.toggle');

		$order = new stdClass();
		$order->ordering = false;
		$order->orderUp = 'orderup';
		$order->orderDown = 'orderdown';
		$order->reverse = false;
		if($pageInfo->filter->order->value == 'a.ordering'){
			$order->ordering = true;
			if($pageInfo->filter->order->dir == 'desc'){
				$order->orderUp = 'orderdown';
				$order->orderDown = 'orderup';
				$order->reverse = true;
			}
		}

		$filters = new stdClass();

		if(acymailing_level(3)){
			$database->setQuery('SELECT DISTINCT category FROM #__acymailing_template WHERE category != ""');
			$categories = $database->loadObjectList();

			$defaultValue = new stdClass();
			$defaultValue->category = JText::_('ACY_ALL_CATEGORIES');
			array_unshift($categories, $defaultValue);
			$filters->category = JHTML::_('select.genericlist', $categories, 'category', 'size="1" style="width:150px" onchange="javascript:submitform();"', 'category', 'category', $pageInfo->category);
		}

		$this->assignRef('filters', $filters);
		$this->assignRef('order', $order);
		$this->assignRef('toggleClass', $toggleClass);
		$this->assignRef('rows', $this->rows);
		$this->assignRef('pageInfo', $pageInfo);
		$this->assignRef('pagination', $pagination);
	}

	function form(){
		JHTML::_('behavior.modal', 'a.modal');
		$tempid = acymailing_getCID('tempid');
		$app = JFactory::getApplication();
		$config = acymailing_config();

		if(!empty($tempid)){
			$templateClass = acymailing_get('class.template');
			$template = $templateClass->get($tempid);
			if(!empty($template->body)) $template->body = acymailing_absoluteURL($template->body);

			if(empty($template->tempid)){
				acymailing_display('Template '.$tempid.' not found', 'error');
				$tempid = 0;
			}
		}

		if(empty($tempid)){
			$template = new stdClass();
			$template->body = '';
			$template->tempid = 0;
			$template->published = 1;
			$template->access = 'all';
			$template->category = '';
			$template->thumb = '';
			$template->readmore = '';
		}

		$editor = acymailing_get('helper.editor');
		$editor->setTemplate($template->tempid);
		$editor->name = 'editor_body';
		$editor->content = $template->body;
		$editor->prepareDisplay();

		if(!ACYMAILING_J16){
			$script = 'function submitbutton(pressbutton){
						if (pressbutton == \'cancel\') {
							submitform( pressbutton );
							return;
						}';
		}else{
			$script = 'Joomla.submitbutton = function(pressbutton) {
						if (pressbutton == \'cancel\') {
							Joomla.submitform(pressbutton,document.adminForm);
							return;
						}';
		}
		$script .= 'if(pressbutton == \'save\' || pressbutton == \'test\' || pressbutton == \'apply\'){
						var emailVars = ["fromemail","replyemail"];
						var val = "";
						for(var key in emailVars){
							if(isNaN(key)) continue;
							val = document.getElementById(emailVars[key]).value;
							if(!validateEmail(val, emailVars[key])){
								return;
							}
						}
					}';
		$script .= 'if(window.document.getElementById("name").value.length < 2){alert(\''.JText::_('ENTER_TITLE', true).'\'); return false;}';
		$script .= "if(pressbutton == 'test' && window.document.getElementById('sendtest') && window.document.getElementById('sendtest').style.display == 'none'){ window.document.getElementById('sendtest').style.display = 'block'; return false;}";
		$script .= $editor->jsCode();
		if(!ACYMAILING_J16){
			$script .= 'submitform( pressbutton );} ';
		}else{
			$script .= 'Joomla.submitform(pressbutton,document.adminForm);}; ';
		}

		$script .= "function insertTag(tag){ try{jInsertEditorText(tag,'editor_body'); return true;} catch(err){alert('Your editor does not enable AcyMailing to automatically insert the tag, please copy/paste it manually in your Newsletter'); return false;}}";

		$script .= 'function addStyle(){
			var myTable=window.document.getElementById("classtable");
			var newline = document.createElement(\'tr\');
			var column = document.createElement(\'td\');
			var column2 = document.createElement(\'td\');
			var input = document.createElement(\'input\');
			var input2 = document.createElement(\'input\');
			input.type = \'text\';
			input2.type = \'text\';
			input.style.width = \'180px\';
			input2.style.width = \'200px\';
			input.name = \'otherstyles[classname][]\';
			input2.name = \'otherstyles[style][]\';
			input.placeholder = "'.str_replace('"', '\"', JText::_('CLASS_NAME', true)).'";
			input2.placeholder = "'.str_replace('"', '\"', JText::_('CSS_STYLE', true)).'";
			column.appendChild(input);
			column2.appendChild(input2);
			newline.appendChild(column);
			newline.appendChild(column2);
			myTable.appendChild(newline);
		}';

		$script .= 'var currentValueId = \'\';
				function showthediv(valueid, e){
					if(currentValueId != valueid){
						try{
							document.getElementById(\'wysija\').style.left = jQuery(e.target).position().left-50+"px";
							document.getElementById(\'wysija\').style.top = jQuery(e.target).position().top-40+"px";
						}catch(err){
							document.getElementById(\'wysija\').style.left = e.x-50+"px";
							document.getElementById(\'wysija\').style.top = e.y-40+"px";
						}
						currentValueId = valueid;
					}
					document.getElementById(\'wysija\').style.display = \'block\';
					initDiv();
				}

				function spanChange(span){
					input = currentValueId;
					if (document.getElementById(span).className == span.toLowerCase()+"elementselected"){
						document.getElementById(span).className = span.toLowerCase()+"element";
						if(span == "B"){
							document.getElementById("name_"+currentValueId).style.fontWeight = "";
							document.getElementById("style_"+currentValueId).value = document.getElementById("style_"+currentValueId).value.replace(/font-weight *: *bold(;)?/i, "");
						}
						if(span == "I"){
							document.getElementById("name_"+currentValueId).style.fontStyle = "";
							document.getElementById("style_"+currentValueId).value = document.getElementById("style_"+currentValueId).value.replace(/font-style *: *italic(;)?/i, "");
						}
						if(span == "U"){
							document.getElementById("name_"+currentValueId).style.textDecoration="";
							document.getElementById("style_"+currentValueId).value = document.getElementById("style_"+currentValueId).value.replace(/text-decoration *: *underline(;)?/i,"");
						}

					}else{
						 document.getElementById(span).className = span.toLowerCase()+"elementselected";
						if(span == "B"){
							document.getElementById("name_"+currentValueId).style.fontWeight = "bold";
							document.getElementById("style_"+currentValueId).value = document.getElementById("style_"+currentValueId).value + "font-weight:bold;";
						}
						if(span == "I"){
							document.getElementById("name_"+currentValueId).style.fontStyle = "italic";
							document.getElementById("style_"+currentValueId).value = document.getElementById("style_"+currentValueId).value + "font-style:italic;";
						}
						if(span == "U"){
							document.getElementById("name_"+currentValueId).style.textDecoration="underline";
							document.getElementById("style_"+currentValueId).value = document.getElementById("style_"+currentValueId).value + "text-decoration:underline;";
						}
					}
				}
				function getValueSelect(){
					selec = currentValueId;
					var myRegex2 = new RegExp(/font-size *:[^;]*;/i);
					var MyValue = document.getElementById("style_select_wysija").value;
					document.getElementById("name_"+currentValueId).style.fontSize = MyValue;
					if(document.getElementById("style_"+currentValueId).value.search(myRegex2) != -1){
						if(MyValue == ""){
							document.getElementById("style_"+currentValueId).value = document.getElementById("style_"+currentValueId).value.replace(myRegex2, "");
						}else{
							document.getElementById("style_"+currentValueId).value = document.getElementById("style_"+currentValueId).value.replace(myRegex2, "font-size:"+MyValue+";");
						}
					}else{
						document.getElementById("style_"+currentValueId).value = document.getElementById("style_"+currentValueId).value + "font-size:"+MyValue+";";
					}
				}

				function initDiv(){

					var RegexSize = new RegExp(/font-size *:[^;]*(;)?/gi);
					var RegexColor = new RegExp(/([^a-z-])color *:[^;]*(;)?/gi);


					document.getElementById("colorexamplewysijacolor").style.backgroundColor = "#000000";
					document.getElementById("colordivwysijacolor").style.display = "none";
					spaced = document.getElementById("style_"+currentValueId).value.substr(0,1);
					if(spaced != " "){
						stringToQuery = \' \' + document.getElementById("style_"+currentValueId).value;
					}else{
						stringToQuery = document.getElementById("style_"+currentValueId).value;
					}
					NewColor = stringToQuery.match(RegexColor);
					if(NewColor != null){
						NewColor = NewColor[0].match(/:[^;!]*/gi);
						NewColor = NewColor[0].replace(/(:| )/gi,"");
						document.getElementById("colorexamplewysijacolor").style.backgroundColor = NewColor;
					}


					document.getElementById("U").className = "uelement";
					document.getElementById("I").className = "ielement";
					document.getElementById("B").className = "belement";

					if(document.getElementById("style_"+currentValueId).value.search(/font-weight: *bold(;)?/i) != -1){
						document.getElementById("B").className += "selected";
					}
					if(document.getElementById("style_"+currentValueId).value.search(/font-style: *italic(;)?/i) != -1){
						document.getElementById("I").className += "selected";
					}
					if(document.getElementById("style_"+currentValueId).value.search(/text-decoration: *underline(;)?/i) != -1){
						document.getElementById("U").className += "selected";
					}


					NewSize = stringToQuery.match(RegexSize);
					document.getElementById("style_select_wysija").options[0].selected = true;
					if(NewSize != null){
						NewSize = NewSize[0].match(/:[^;]*/gi);
						NewSize = NewSize[0].replace(" ","");
						NewSize = NewSize.substr(1);
						for(var i = 0; i < document.getElementById("style_select_wysija").length; i++){
							if(document.getElementById("style_select_wysija").options[i].value == NewSize){
								document.getElementById("style_select_wysija").options[i].selected = true;
							}
						}
					}
				}';

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($script);

		$paramBase = ACYMAILING_COMPONENT.'.'.$this->getName();
		$infos = new stdClass();
		$infos->test_selection = $app->getUserStateFromRequest($paramBase.".test_selection", 'test_selection', '', 'string');
		$infos->test_group = $app->getUserStateFromRequest($paramBase.".test_group", 'test_group', '', 'string');
		$infos->test_emails = $app->getUserStateFromRequest($paramBase.".test_emails", 'test_emails', '', 'string');


		$acyToolbar = acymailing::get('helper.toolbar');
		if(acymailing_isAllowed($config->get('acl_tags_view', 'all'))) $acyToolbar->popup('tag', JText::_('TAGS'), JURI::base()."index.php?option=com_acymailing&ctrl=tag&task=tag&tmpl=component&type=news", 780, 550);
		$acyToolbar->custom('test', JText::_('SEND_TEST'), 'send', false);
		$acyToolbar->divider();
		$acyToolbar->addButtonOption('apply', JText::_('ACY_APPLY'), 'apply', false);
		$acyToolbar->save();
		$acyToolbar->cancel();
		$acyToolbar->divider();
		$acyToolbar->help('template', 'templatecreation');
		$acyToolbar->setTitle(JText::_('ACY_TEMPLATE'), 'template&task=edit&tempid='.$tempid);
		$acyToolbar->display();


		$this->assignRef('editor', $editor);
		$testreceiverType = acymailing_get('type.testreceiver');
		$this->assignRef('testreceiverType', $testreceiverType);
		$this->assignRef('template', $template);
		$colorBox = acymailing_get('type.color');
		$this->assignRef('colorBox', $colorBox);
		$this->assignRef('infos', $infos);

		$tabs = acymailing_get('helper.acytabs');
		$tabs->setOptions(array('useCookie' => true));
		$this->assignRef('tabs', $tabs);
	}

	function theme(){
		$this->selection[] = 'a.*';
		$this->filters[] = 'a.published = 1';

		if(acymailing_level(3)){
			$my = JFactory::getUser();
			if(!ACYMAILING_J16){
				$groups = $my->gid;
				$condGroup = ' OR a.access LIKE (\'%,'.$groups.',%\')';
			}else{
				jimport('joomla.access.access');
				$groups = JAccess::getGroupsByUser($my->id, false);
				$condGroup = '';
				foreach($groups as $group){
					$condGroup .= ' OR a.access LIKE (\'%,'.$group.',%\')';
				}
			}
			$this->filters[] = 'a.access = \'all\''.$condGroup;
		}

		$this->button = false;
		acymailing_display(JText::_('CHANGE_TEMPLATE'), 'warning', false);
		$this->listing();

		$js = "function applyTemplate(tempid){
			window.parent.changeTemplate(window.document.getElementById('htmlcontent_'+tempid).innerHTML,window.document.getElementById('textcontent_'+tempid).innerHTML,window.document.getElementById('subject_'+tempid).innerHTML,window.document.getElementById('stylesheet_'+tempid).innerHTML,window.document.getElementById('fromname_'+tempid).innerHTML,window.document.getElementById('fromemail_'+tempid).innerHTML,window.document.getElementById('replyname_'+tempid).innerHTML,window.document.getElementById('replyemail_'+tempid).innerHTML,tempid);
			acymailing_js.closeBox(true); }";
		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);
	}

	function upload(){
		if(JRequest::getString('tmpl') == 'component'){
			$acyToolbar = acymailing::get('helper.toolbar');
			$acyToolbar->custom('doupload', JText::_('IMPORT'), 'import', false);
			$acyToolbar->divider();
			$acyToolbar->help('template-upload');
			$acyToolbar->setTitle(JText::_('ACY_TEMPLATE'));
			$acyToolbar->topfixed = false;
			$acyToolbar->display();
		}
	}
}
