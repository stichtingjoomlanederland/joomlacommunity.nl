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


class ActionViewAction extends acymailingView{
	function display($tpl = null){
		$function = $this->getLayout();
		if(method_exists($this, $function)) $this->$function();

		parent::display($tpl);
	}

	function listing(){
		$app = JFactory::getApplication();
		$config = acymailing_config();
		$pageInfo = new stdClass();
		$pageInfo->filter = new stdClass();
		$pageInfo->filter->order = new stdClass();
		$pageInfo->limit = new stdClass();
		$pageInfo->elements = new stdClass();

		$paramBase = ACYMAILING_COMPONENT.'.'.$this->getName();

		$pageInfo->filter->order->value = $app->getUserStateFromRequest($paramBase.".filter_order", 'filter_order', 'a.ordering', 'cmd');
		$pageInfo->filter->order->dir = $app->getUserStateFromRequest($paramBase.".filter_order_Dir", 'filter_order_Dir', 'asc', 'word');
		if(strtolower($pageInfo->filter->order->dir) !== 'desc') $pageInfo->filter->order->dir = 'asc';
		$pageInfo->search = $app->getUserStateFromRequest($paramBase.".search", 'search', '', 'string');
		$pageInfo->search = JString::strtolower(trim($pageInfo->search));
		$selectedCreator = $app->getUserStateFromRequest($paramBase."filter_creator", 'filter_creator', 0, 'int');

		$pageInfo->limit->value = $app->getUserStateFromRequest($paramBase.'.list_limit', 'limit', $app->getCfg('list_limit'), 'int');
		$pageInfo->limit->start = $app->getUserStateFromRequest($paramBase.'.limitstart', 'limitstart', 0, 'int');

		$database = JFactory::getDBO();

		$filters = array();
		if(!empty($pageInfo->search)){
			$searchVal = '\'%'.acymailing_getEscaped($pageInfo->search, true).'%\'';
			$filters[] = "a.name LIKE $searchVal OR a.description LIKE $searchVal OR a.action_id LIKE $searchVal OR a.username LIKE $searchVal OR d.name LIKE $searchVal";
		}
		if(!empty($selectedCreator)) $filters[] = 'a.userid = '.$selectedCreator;

		$query = 'SELECT a.*, d.name AS creatorname, d.username AS creatorusername, d.email';
		$query .= ' FROM '.acymailing_table('action').' AS a';
		$query .= ' LEFT JOIN '.acymailing_table('users', false).' AS d ON a.userid = d.id';
		if(!empty($filters)) $query .= ' WHERE ('.implode(') AND (', $filters).')';
		if(!empty($pageInfo->filter->order->value)){
			$query .= ' ORDER BY '.$pageInfo->filter->order->value.' '.$pageInfo->filter->order->dir;
		}

		$database->setQuery($query, $pageInfo->limit->start, $pageInfo->limit->value);
		$rows = $database->loadObjectList();
		if(!empty($pageInfo->search)){
			$rows = acymailing_search($pageInfo->search, $rows);
		}

		$queryCount = 'SELECT COUNT(a.action_id) FROM '.acymailing_table('action').' AS a';
		if(!empty($pageInfo->search)) $queryCount .= ' LEFT JOIN '.acymailing_table('users', false).' AS d ON a.userid = d.id';
		if(!empty($filters)) $queryCount .= ' WHERE ('.implode(') AND (', $filters).')';

		$database->setQuery($queryCount);
		$pageInfo->elements->total = $database->loadResult();

		$actionids = array();
		foreach($rows as $oneRow){
			$actionids[] = $oneRow->action_id;
		}

		$pageInfo->elements->page = count($rows);

		jimport('joomla.html.pagination');
		$pagination = new JPagination($pageInfo->elements->total, $pageInfo->limit->start, $pageInfo->limit->value);

		$acyToolbar = acymailing::get('helper.toolbar');
		if(acymailing_isAllowed($config->get('acl_distribution_manage', 'all'))) $acyToolbar->add();
		if(acymailing_isAllowed($config->get('acl_distribution_manage', 'all'))) $acyToolbar->edit();
		if(acymailing_isAllowed($config->get('acl_distribution_copy', 'all'))) $acyToolbar->copy();
		if(acymailing_isAllowed($config->get('acl_distribution_delete', 'all'))) $acyToolbar->delete();
		if(acymailing_isAllowed($config->get('acl_distribution_manage', 'all')) || acymailing_isAllowed($config->get('acl_distribution_copy', 'all')) || acymailing_isAllowed($config->get('acl_distribution_delete', 'all'))) $acyToolbar->divider();
		$acyToolbar->help('distributionlists#listing');
		$acyToolbar->setTitle(JText::_('ACY_DISTRIBUTION'), 'action');
		$acyToolbar->display();

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
		$creatorfilterType = acymailing_get('type.creatorfilter');
		$filters->creator = $creatorfilterType->display('filter_creator', $selectedCreator, 'action');

		$this->assignRef('filters', $filters);
		$this->assignRef('order', $order);
		$toggleClass = acymailing_get('helper.toggle');
		$this->assignRef('toggleClass', $toggleClass);
		$this->assignRef('rows', $rows);
		$this->assignRef('pageInfo', $pageInfo);
		$this->assignRef('pagination', $pagination);
	}

	function form(){
		JHTML::_('behavior.modal', 'a.modal');
		$action_id = acymailing_getCID('action_id');
		$user = JFactory::getUser();
		$actionClass = acymailing_get('class.action');
		$db = JFactory::getDBO();

		if(!empty($action_id)){
			$action = $actionClass->get($action_id);

			if(empty($action->action_id)){
				acymailing_display('Action '.$action_id.' not found', 'error');
				$action_id = 0;
			}

			$action->conditions = json_decode($action->conditions, true);
			$action->actions = json_decode($action->actions, true);
		}else{
			$action = new stdClass();
			$action->published = 1;
			$action->creatorname = $user->name;
			$action->userid = $user->id;
		}

		if(!ACYMAILING_J16){
			$script = 'function submitbutton(pressbutton){
						if (pressbutton == \'cancel\') {
							submitform(pressbutton);
							return;
						}';
		}else{
			$script = 'Joomla.submitbutton = function(pressbutton) {
						if (pressbutton == \'cancel\') {
							Joomla.submitform(pressbutton, document.adminForm);
							return;
						}';
		}
		$script .= 'if(window.document.getElementById("name").value.length < 2){alert(\''.JText::_('ENTER_TITLE', true).'\'); return false;}';
		if(!ACYMAILING_J16){
			$script .= 'submitform(pressbutton);}';
		}else{
			$script .= 'Joomla.submitform(pressbutton, document.adminForm);}; ';
		}
		$script .= 'function affectUser(idcreator,name,email){
			window.document.getElementById("creatorname").innerHTML = name;
			window.document.getElementById("actioncreator").value = idcreator;
		}';

		$script .= 'function displayAllowedOptions(value){
			if(value == "specific"){
				document.getElementById("dataconditionsspecific").style.display = "";
			}else{
				document.getElementById("dataconditionsspecific").style.display = "none";
			}

			if(value == "group"){
				document.getElementById("allowedgroupsblock").style.display = "";
			}else{
				document.getElementById("allowedgroupsblock").style.display = "none";
			}

			if(value == "list"){
				document.getElementById("allowedlistsblock").style.display = "";
			}else{
				document.getElementById("allowedlistsblock").style.display = "none";
			}
		}';

		$acyToolbar = acymailing::get('helper.toolbar');
		$acyToolbar->custom('test', JText::_('ABTESTING_TEST'), 'connection', false);
		$acyToolbar->divider();
		$acyToolbar->addButtonOption('apply', JText::_('ACY_APPLY'), 'apply', false);
		$acyToolbar->save();
		$acyToolbar->cancel();
		$acyToolbar->divider();
		$acyToolbar->help('distributionlists#edit');
		$acyToolbar->setTitle(JText::_('ACY_DISTRIBUTION'), 'action&task=edit&action_id='.$action_id);
		$acyToolbar->display();

		$allowedoptions = new stdClass();
		$allowedoptions->specific = '<input type="text" name="data[conditions][specific]" id="dataconditionsspecific" class="inputbox" style="'.(@$action->conditions['sender'] == 'specific' ? '' : 'display:none;').'width:200px" value="'.$this->escape(empty($action->conditions['specific']) ? $user->email : $action->conditions['specific']).'"/>';

		if(!ACYMAILING_J16){
			$acl = JFactory::getACL();
			$groups = $acl->get_group_children_tree(null, 'USERS', false);
		}else{
			$db->setQuery('SELECT a.*, a.title as text, a.id as value FROM #__usergroups AS a ORDER BY a.lft ASC');
			$groups = $db->loadObjectList('id');
			foreach($groups as $id => $group){
				if(isset($groups[$group->parent_id])){
					$groups[$id]->level = empty($groups[$group->parent_id]->level) ? 1 : intval($groups[$group->parent_id]->level + 1);
					$groups[$id]->text = str_repeat('- - ', $groups[$id]->level).$groups[$id]->text;
				}
			}
		}

		$allgroups = new stdClass();
		$allgroups->text = JText::_('ACY_ALL');
		$allgroups->value = 'all';
		array_unshift($groups, $allgroups);
		$allowedoptions->group = '<span id="allowedgroupsblock"'.(@$action->conditions['sender'] == 'group' ? '' : 'style="display:none;"').'>'.JHTML::_('select.genericlist', $groups, "data[conditions][group]", 'class="inputbox" size="1"', 'value', 'text', @$action->conditions['group']).'</span>';

		$allowedoptions->list = '<span id="allowedlistsblock"'.(@$action->conditions['sender'] == 'list' ? '' : 'style="display:none;"').'><input class="inputbox" id="dataconditionslistids" name="data[conditions][listids]" type="text" style="width:75px" value="'.@$action->conditions['listids'].'">';
		$allowedoptions->list .= '<a class="modal" id="linkdataconditionslistids" title="'.JText::_('SELECT_LISTS').'" href="index.php?option=com_acymailing&tmpl=component&ctrl=chooselist&task=listids&values='.@$action->conditions['listids'].'&control=dataconditions" rel="{handler: \'iframe\', size: {x: 650, y: 375}}"><button class="acymailing_button_grey" onclick="return false">'.JText::_('SELECT').'</button></a></span>';

		$possibleActions = array();
		$possibleActions[] = JHTML::_('select.option', 'none', JText::_('ACTION_SELECT'));
		$possibleActions[] = JHTML::_('select.option', 'forwardlist', JText::_('ACY_FORWARD_LIST'));
		$possibleActions[] = JHTML::_('select.option', 'forward', JText::_('FORWARD_EMAIL'));
		$possibleActions[] = JHTML::_('select.option', 'subscribe', JText::_('SUBSCRIBECAPTION'));
		$possibleActions[] = JHTML::_('select.option', 'unsubscribe', JText::_('UNSUBSCRIBECAPTION'));
		$possibleActions[] = JHTML::_('select.option', 'newsletter', JText::_('ACY_SEND_NEWSLETTER'));

		$listClass = acymailing_get('class.list');
		$lists = $listClass->getLists();

		$db->setQuery('SELECT tempid, name FROM '.acymailing_table('template').' WHERE published = 1 AND body LIKE "%{emailcontent}%" ORDER BY name ASC');
		$templates = $db->loadObjectList();
		$noTemplateFound = empty($templates) ? $noTemplateFound = ' '.JText::_('ACY_EMAILCONTENT_TEMPLATE') : '';

		$defaultTemplate = new stdClass();
		$defaultTemplate->tempid = 0;
		$defaultTemplate->name = JText::_('ACY_NO_TEMPLATE');
		array_unshift($templates, $defaultTemplate);

		$db->setQuery('SELECT mailid, subject FROM '.acymailing_table('mail').' WHERE published = 1 AND type = "news" ORDER BY mailid DESC LIMIT 100');
		$newsletters = $db->loadObjectList();

		$defaultNewsletter = new stdClass();
		$defaultNewsletter->mailid = 0;
		$defaultNewsletter->subject = JText::_('LATEST_NEWSLETTER');
		array_unshift($newsletters, $defaultNewsletter);

		$includeIn = '<br />'.JText::_('ACY_INCLUDE_MSG_IN').' ';

		$js = "var typesOptions = new Array();
		typesOptions['none'] = '';
		typesOptions['forwardlist'] = '".str_replace(array("'", "\n"), array("\\'", ''), JHTML::_('select.genericlist', $lists, "data[actions][__num__][list]", 'class="inputbox chzn-done" size="1"', 'listid', 'name').$includeIn.JHTML::_('select.genericlist', $templates, "data[actions][__num__][template]", 'class="inputbox chzn-done" size="1"', 'tempid', 'name').$noTemplateFound)."';
		typesOptions['forward'] = '<input id=\"dataactions__num__forward\" type=\"text\" placeholder=\"address1@example.com,address2@example.com\" name=\"data[actions][__num__][forward]\" /> ".str_replace(array("'", "\n"), array("\\'", ''), $includeIn.JHTML::_('select.genericlist', $templates, "data[actions][__num__][template]", 'class="inputbox chzn-done" size="1"', 'tempid', 'name').$noTemplateFound)."';
		typesOptions['subscribe'] = '".str_replace(array("'", "\n"), array("\\'", ''), JHTML::_('select.genericlist', $lists, "data[actions][__num__][list]", 'class="inputbox chzn-done" size="1"', 'listid', 'name'))."';
		typesOptions['unsubscribe'] = '".str_replace(array("'", "\n"), array("\\'", ''), JHTML::_('select.genericlist', $lists, "data[actions][__num__][list]", 'class="inputbox chzn-done" size="1"', 'listid', 'name'))."';
		typesOptions['newsletter'] = '".str_replace(array("'", "\n"), array("\\'", ''), JHTML::_('select.genericlist', $newsletters, "data[actions][__num__][newsletter]", 'class="inputbox chzn-done" size="1"', 'mailid', 'subject'))."';

		function updateAction(number){
			var selectedType = document.getElementById('dataactions'+number+'type').value;
			document.getElementById('actions'+number+'area').innerHTML = typesOptions[selectedType].replace(/__num__/g, number);
		}

		var numActions = 0;
		function addAction(){
			var newdiv = document.createElement('div');
			newdiv.id = 'actionscontainer'+numActions;

			var content = '".str_replace(array("'", "\n"), array("\\'", ''), JHTML::_('select.genericlist', $possibleActions, "data[actions][__num__][type]", 'class="inputbox chzn-done" size="1" onchange="updateAction(__num__);"', 'value', 'text'))."<div class=\"acyfilterarea\" id=\"actions'+numActions+'area\"></div>';
			newdiv.innerHTML = content.replace(/__num__/g, numActions);

			var actionsarea = document.getElementById('actionsarea');
			if(actionsarea != 'undefined' && actionsarea != null) actionsarea.appendChild(newdiv);
			numActions++;
		}";

		$ready = "addAction();";
		if(!empty($action->actions)){
			foreach($action->actions as $oneAction){
				if(empty($oneAction['type']) || $oneAction['type'] == 'none') continue;
				$ready .= "document.getElementById('dataactions'+(numActions-1)+'type').value = '".$oneAction['type']."';
				updateAction(numActions-1);";
				unset($oneAction['type']);
				foreach($oneAction as $element => $value){
					$ready .= "document.getElementById('dataactions'+(numActions-1)+'".$element."').value = '".$value."';";
				}
				$ready .= "addAction();";
			}
		}

		$js .= "window.addEvent('domready', function(){ ".$ready." });";
		$script .= $js;

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($script);

		$this->assignRef('allowedoptions', $allowedoptions);
		$this->assignRef('action', $action);
	}
}
