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

class FilterViewFilter extends acymailingView{

	var $chosen = false;

	function display($tpl = null){
		$function = $this->getLayout();
		if(method_exists($this, $function)) $this->$function();

		parent::display($tpl);
	}

	function form(){

		$app = JFactory::getApplication();
		$config = acymailing_config();
		$pageInfo = new stdClass();
		$pageInfo->filter = new stdClass();
		$pageInfo->filter->order = new stdClass();
		$pageInfo->limit = new stdClass();
		$pageInfo->elements = new stdClass();

		$paramBase = ACYMAILING_COMPONENT.'.'.$this->getName();

		$pageInfo->filter->order->value = $app->getUserStateFromRequest($paramBase.".filter_order", 'filter_order', 'name', 'cmd');
		$pageInfo->filter->order->dir = $app->getUserStateFromRequest($paramBase.".filter_order_Dir", 'filter_order_Dir', 'asc', 'word');
		if(strtolower($pageInfo->filter->order->dir) !== 'desc') $pageInfo->filter->order->dir = 'asc';
		$pageInfo->search = $app->getUserStateFromRequest($paramBase.".search", 'search', '', 'string');
		$pageInfo->search = JString::strtolower(trim($pageInfo->search));


		$pageInfo->limit->value = $app->getUserStateFromRequest($paramBase.'.list_limit', 'limit', $app->getCfg('list_limit'), 'int');
		$pageInfo->limit->start = $app->getUserStateFromRequest($paramBase.'.limitstart', 'limitstart', 0, 'int');

		$db = JFactory::getDBO();

		if(JRequest::getVar('task') == 'filterDisplayUsers'){
			$action = array();
			$action['type'] = array('displayUsers');
			$action[] = array('displayUsers' => array());

			$filterClass = acymailing_get('class.filter');
			$filterClass->subid = JRequest::getString('subid');
			$filterClass->execute(JRequest::getVar('filter'), $action);

			if(!empty($filterClass->report)){
				$this->assignRef('filteredUsers', $filterClass->report[0]);
			}
		}


		$filid = acymailing_getCID('filid');

		$filterClass = acymailing_get('class.filter');
		$testFilter = JRequest::getVar('filter');
		if(!empty($filid) && empty($testFilter)){
			$filter = $filterClass->get($filid);
		}else{
			$filter = new stdClass();
			$filter->action = JRequest::getVar('action');
			$filter->filter = JRequest::getVar('filter');
			$filter->published = 1;
		}

		JPluginHelper::importPlugin('acymailing');
		$this->dispatcher = JDispatcher::getInstance();

		$typesFilters = array();
		$typesActions = array();

		$outputFilters = implode('', $this->dispatcher->trigger('onAcyDisplayFilters', array(&$typesFilters, 'massactions')));
		$outputActions = implode('', $this->dispatcher->trigger('onAcyDisplayActions', array(&$typesActions)));

		$typevaluesFilters = array();
		$typevaluesActions = array();
		$typevaluesFilters[] = JHTML::_('select.option', '', JText::_('FILTER_SELECT'));
		$typevaluesActions[] = JHTML::_('select.option', '', JText::_('ACTION_SELECT'));
		$doc = JFactory::getDocument();
		foreach($typesFilters as $oneType => $oneName){
			$typevaluesFilters[] = JHTML::_('select.option', $oneType, $oneName);
		}
		foreach($typesActions as $oneType => $oneName){
			$typevaluesActions[] = JHTML::_('select.option', $oneType, $oneName);
		}

		$js = "function updateAction(actionNum){
				var actiontype = window.document.getElementById('actiontype'+actionNum);
				if(actiontype == 'undefined' || actiontype == null) return;
				currentActionType = actiontype.value;
				if(!currentActionType){
					window.document.getElementById('actionarea_'+actionNum).innerHTML = '';
					return;
				}
				actionArea = 'action__num__'+currentActionType;
				window.document.getElementById('actionarea_'+actionNum).innerHTML = window.document.getElementById(actionArea).innerHTML.replace(/__num__/g,actionNum);
				if(typeof(window['onAcyDisplayAction_'+currentActionType]) == 'function') {
					try{ window['onAcyDisplayAction_'+currentActionType](actionNum); }catch(e){alert('Error in the onAcyDisplayAction_'+currentActionType+' function : '+e); }
				}

			}";

		$js .= "var numActions = 0;
				function addAction(){
					var newdiv = document.createElement('div');
					newdiv.id = 'action'+numActions;
					newdiv.className = 'plugarea';
					newdiv.innerHTML = document.getElementById('actions_original').innerHTML.replace(/__num__/g, numActions);
					var allactions = document.getElementById('allactions');
					if(allactions != 'undefined' && allactions != null) allactions.appendChild(newdiv); updateAction(numActions); numActions++;
				}";

		$js .= "window.addEvent('domready', function(){ addAcyFilter(); addAction(); });";

		if(!ACYMAILING_J16){
			$js .= 'function submitbutton(pressbutton){
						if (pressbutton != \'save\') {
							submitform( pressbutton );
							return;
						}';
		}else{
			$js .= 'Joomla.submitbutton = function(pressbutton) {
						if (pressbutton != \'save\') {
							Joomla.submitform(pressbutton,document.adminForm);
							return;
						}';
		}
		if(ACYMAILING_J30){
			$js .= "if(window.document.getElementById('filterinfo').style.display == 'none'){
						window.document.getElementById('filterinfo').style.display = 'block';
						return false;}
					if(window.document.getElementById('title').value.length < 2){alert('".JText::_('ENTER_TITLE', true)."'); return false;}";
		}else{
			$js .= "if(window.document.getElementById('filterinfo').style.display == 'none'){
						window.document.getElementById('filterinfo').style.display = 'block';
						return false;}
					if(window.document.getElementById('title').value.length < 2){alert('".JText::_('ENTER_TITLE', true)."'); return false;}";
		}
		if(!ACYMAILING_J16){
			$js .= "submitform( pressbutton );} ";
		}else{
			$js .= "Joomla.submitform(pressbutton,document.adminForm);}; ";
		}

		$doc->addScriptDeclaration($js);

		$filterClass->addJSFilterFunctions();

		$js = '';
		$data = array('addAction' => 'action', 'addAcyFilter' => 'filter');
		foreach($data as $jsFunction => $datatype){
			if(empty($filter->$datatype)) continue;
			foreach($filter->{$datatype}['type'] as $num => $oneType){
				if(empty($oneType)) continue;
				$js .= "while(!document.getElementById('".$datatype."type$num')){".$jsFunction."();}
						document.getElementById('".$datatype."type$num').value= '$oneType';
						update".ucfirst($datatype)."($num);";
				if(empty($filter->{$datatype}[$num][$oneType])) continue;
				foreach($filter->{$datatype}[$num][$oneType] as $key => $value){
					if(is_array($value)){
						$js .= "try{";
						foreach($value as $subkey => $subval){
							$js .= "document.adminForm.elements['".$datatype."[$num][$oneType][$key][$subkey]'].value = '".addslashes(str_replace(array("\n", "\r"), ' ', $subval))."';";
							$js .= "if(document.adminForm.elements['".$datatype."[$num][$oneType][$key][$subkey]'].type && document.adminForm.elements['".$datatype."[$num][$oneType][$key][$subkey]'].type == 'checkbox'){ document.adminForm.elements['".$datatype."[$num][$oneType][$key][$subkey]'].checked = 'checked'; }";
						}
						$js .= "}catch(e){}";
					}
					$myVal = is_array($value) ? implode(',', $value) : $value;
					$js .= "try{";
					$js .= "document.adminForm.elements['".$datatype."[$num][$oneType][$key]'].value = '".addslashes(str_replace(array("\n", "\r"), ' ', $myVal))."';";
					$js .= "if(document.adminForm.elements['".$datatype."[$num][$oneType][$key]'].type && document.adminForm.elements['".$datatype."[$num][$oneType][$key]'].type == 'checkbox'){ document.adminForm.elements['".$datatype."[$num][$oneType][$key]'].checked = 'checked'; }";
					$js .= "}catch(e){}";
				}

				$js .= "\n"." if(typeof(onAcyDisplay".ucfirst($datatype)."_".$oneType.") == 'function'){
					try{ onAcyDisplay".ucfirst($datatype)."_".$oneType."($num); }catch(e){alert('Error in the onAcyDisplay".ucfirst($datatype)."_".$oneType." function : '+e); }
				}";

				if($datatype == 'filter') $js .= " countresults($num);";
			}
		}

		$listid = JRequest::getInt('listid');
		if(!empty($listid)){
			$js .= "document.getElementById('actiontype0').value = 'list'; updateAction(0); document.adminForm.elements['action[0][list][selectedlist]'].value = '".$listid."';";
		}

		$doc->addScriptDeclaration("window.addEvent('domready', function(){ $js });");

		$triggers = array();
		$triggers['daycron'] = JText::_('AUTO_CRON_FILTER');
		$nextDate = $config->get('cron_plugins_next');

		$listHours = array();
		$listMinutess = array();
		for($i = 0; $i < 24; $i++){
			$listHours[] = JHTML::_('select.option', $i, ($i < 10 ? '0'.$i : $i));
		}
		$hours = JHTML::_('select.genericlist', $listHours, 'triggerhours', 'class="inputbox" size="1" style="width:60px;"', 'value', 'text', acymailing_getDate($nextDate, 'H'));
		for($i = 0; $i < 60; $i += 5){
			$listMinutess[] = JHTML::_('select.option', $i, ($i < 10 ? '0'.$i : $i));
		}
		$defaultMin = floor(acymailing_getDate($nextDate, 'i') / 5) * 5;
		$minutes = JHTML::_('select.genericlist', $listMinutess, 'triggerminutes', 'class="inputbox" size="1" style="width:60px;"', 'value', 'text', $defaultMin);
		$this->assign('hours', $hours);
		$this->assign('minutes', $minutes);

		$this->assign('nextDate', !empty($nextDate) ? ' ('.JText::_('NEXT_RUN').' : '.acymailing_getDate($nextDate, '%d %B %Y  %H:%M').')' : '');

		$triggers['allcron'] = JText::_('ACY_EACH_TIME');
		$triggers['subcreate'] = JText::_('ON_USER_CREATE');
		$triggers['subchange'] = JText::_('ON_USER_CHANGE');
		$this->dispatcher->trigger('onAcyDisplayTriggers', array(&$triggers));

		$name = empty($filter->name) ? '' : ' : '.$filter->name;

		if(JRequest::getCmd('tmpl', '') != 'component'){
			$acyToolbar = acymailing::get('helper.toolbar');
			$acyToolbar->custom('filterDisplayUsers', JText::_('FILTER_VIEW_USERS'), 'user', false, '');
			$acyToolbar->custom('process', JText::_('PROCESS'), 'process', false, '');
			$acyToolbar->divider();
			if(acymailing_level(3)){
				$acyToolbar->save();
				if(!empty($filter->filid)) $acyToolbar->link(acymailing_completeLink('filter&task=edit&filid=0'), JText::_('ACY_NEW'), 'new');
			}
			$acyToolbar->link(acymailing_completeLink('dashboard'), JText::_('ACY_CLOSE'), 'cancel');
			$acyToolbar->divider();
			$acyToolbar->help('filter');
			$acyToolbar->setTitle(JText::_('ACY_MASS_ACTIONS').$name, 'filter&task=edit&filid='.$filid);
			$acyToolbar->display();
		}else{
			acymailing_setPageTitle(JText::_('ACY_MASS_ACTIONS').$name);
		}

		$subid = JRequest::getString('subid');
		if(!empty($subid)){
			$subArray = explode(',', trim($subid, ','));
			JArrayHelper::toInteger($subArray);

			$db->setQuery('SELECT `name`,`email` FROM `#__acymailing_subscriber` WHERE `subid` IN ('.implode(',', $subArray).')');
			$users = $db->loadObjectList();
			if(!empty($users)){
				$this->assignRef('users', $users);
				$this->assignRef('subid', $subid);
			}
		}

		$this->assignRef('typevaluesFilters', $typevaluesFilters);
		$this->assignRef('typevaluesActions', $typevaluesActions);
		$this->assignRef('outputFilters', $outputFilters);
		$this->assignRef('outputActions', $outputActions);
		$this->assignRef('filter', $filter);
		$this->assignRef('pageInfo', $pageInfo);

		$this->assignRef('triggers', $triggers);
		if(JRequest::getCmd('tmpl') == 'component'){
			$doc->addStyleSheet(ACYMAILING_CSS.'frontendedition.css?v='.filemtime(ACYMAILING_MEDIA.'css'.DS.'frontendedition.css'));
		}

		if(acymailing_level(3) AND JRequest::getCmd('tmpl') != 'component'){
			$query = 'SELECT * FROM '.acymailing_table('filter');

			if(!empty($pageInfo->search)){
				$searchVal = '\'%'.acymailing_getEscaped($pageInfo->search, true).'%\'';
				$query .= ' WHERE LOWER(name) LIKE'.$searchVal;
			}

			if(!empty($pageInfo->filter->order->value) && (($pageInfo->filter->order->value === "name") || ($pageInfo->filter->order->value === "filid"))){
				$query .= ' ORDER BY '.$pageInfo->filter->order->value.' '.$pageInfo->filter->order->dir;
			}

			$db->setQuery($query);
			$filters = $db->loadObjectList();

			$toggleClass = acymailing_get('helper.toggle');
			$this->assignRef('toggleClass', $toggleClass);
			$this->assignRef('filters', $filters);
		}
	}
}
