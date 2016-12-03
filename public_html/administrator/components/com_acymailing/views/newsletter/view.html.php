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


class NewsletterViewNewsletter extends acymailingView{
	var $type = 'news';
	var $ctrl = 'newsletter';
	var $nameListing = 'NEWSLETTERS';
	var $nameForm = 'NEWSLETTER';
	var $icon = 'newsletter';
	var $aclCat = 'newsletters';
	var $doc = 'newsletter';

	function display($tpl = null){
		$function = $this->getLayout();
		if(method_exists($this, $function)) $this->$function();

		parent::display($tpl);
	}

	function listing(){

		JHTML::_('behavior.modal', 'a.modal');

		$app = JFactory::getApplication();
		$pageInfo = new stdClass();
		$pageInfo->filter = new stdClass();
		$pageInfo->filter->order = new stdClass();
		$pageInfo->limit = new stdClass();
		$pageInfo->elements = new stdClass();

		$config = acymailing_config();

		$paramBase = ACYMAILING_COMPONENT.'.'.$this->getName();
		$pageInfo->filter->order->value = $app->getUserStateFromRequest($paramBase.".filter_order", 'filter_order', 'a.mailid', 'cmd');
		$pageInfo->filter->order->dir = $app->getUserStateFromRequest($paramBase.".filter_order_Dir", 'filter_order_Dir', 'desc', 'word');
		if(strtolower($pageInfo->filter->order->dir) !== 'desc') $pageInfo->filter->order->dir = 'asc';

		$pageInfo->search = $app->getUserStateFromRequest($paramBase.".search", 'search', '', 'string');
		$pageInfo->search = JString::strtolower(trim($pageInfo->search));
		$selectedList = $app->getUserStateFromRequest($paramBase."filter_list", 'filter_list', 0, 'int');
		$selectedCreator = $app->getUserStateFromRequest($paramBase."filter_creator", 'filter_creator', 0, 'int');

		$pageInfo->limit->value = $app->getUserStateFromRequest($paramBase.'.list_limit', 'limit', $app->getCfg('list_limit'), 'int');
		$pageInfo->limit->start = $app->getUserStateFromRequest($paramBase.'.limitstart', 'limitstart', 0, 'int');

		$database = JFactory::getDBO();


		$searchMap = array('a.mailid', 'a.alias', 'a.subject', 'a.fromname', 'a.fromemail', 'a.replyname', 'a.replyemail', 'a.userid', 'b.name', 'b.username', 'b.email');
		$filters = array();
		if(!empty($pageInfo->search)){
			$searchVal = '\'%'.acymailing_getEscaped($pageInfo->search, true).'%\'';
			$filters[] = implode(" LIKE $searchVal OR ", $searchMap)." LIKE $searchVal";
		}

		if($this->type == 'news'){
			$database->setQuery('SELECT mailid FROM #__acymailing_mail WHERE type = "action" LIMIT 1');
			$actionExists = $database->loadResult();

			$selectedType = $app->getUserStateFromRequest($paramBase."filter_type", 'filter_type', 'news', 'string');
			if(!empty($selectedType) && $actionExists){
				$filters[] = 'a.type = '.$database->quote($selectedType);
			}else{
				$filters[] = 'a.type IN ("news","action")';
			}
		}else{
			$filters[] = 'a.type = \''.$this->type.'\'';
		}

		if(!empty($selectedList)) $filters[] = 'c.listid = '.$selectedList;
		if(!empty($selectedCreator)) $filters[] = 'a.userid = '.$selectedCreator;
		if($this->type == 'news'){
			$selectedDate = $app->getUserStateFromRequest($paramBase."filter_date", 'filter_date', 0, 'string');
			if(!empty($selectedDate)){
				if(strlen($selectedDate) > 4){
					$filters[] = 'DATE_FORMAT(FROM_UNIXTIME(senddate),"%Y-%m") = '.$database->Quote($selectedDate);
				}else $filters[] = 'DATE_FORMAT(FROM_UNIXTIME(senddate),"%Y") = '.$database->Quote($selectedDate);
			}
		}

		$selection = array_merge($searchMap, array('a.created', 'a.frequency', 'a.senddate', 'a.published', 'a.type', 'a.visible', 'a.abtesting'));

		if(empty($selectedList)){
			if($app->isAdmin()){
				$query = 'SELECT '.implode(',', $selection).' FROM '.acymailing_table('mail').' as a';
				$queryCount = 'SELECT COUNT(a.mailid) FROM '.acymailing_table('mail').' as a';
			}else{
				$query = 'SELECT '.implode(',', $selection).' FROM '.acymailing_table('listmail').' as c';
				$query .= ' JOIN '.acymailing_table('mail').' as a on a.mailid = c.mailid ';
				$queryCount = 'SELECT COUNT(DISTINCT c.mailid) FROM '.acymailing_table('listmail').' as c';
				$queryCount .= ' JOIN '.acymailing_table('mail').' as a on a.mailid = c.mailid ';
			}
		}else{
			$query = 'SELECT '.implode(',', $selection).' FROM '.acymailing_table('listmail').' as c';
			$query .= ' JOIN '.acymailing_table('mail').' as a on a.mailid = c.mailid ';
			$queryCount = 'SELECT COUNT(c.mailid) FROM '.acymailing_table('listmail').' as c';
			$queryCount .= ' JOIN '.acymailing_table('mail').' as a on a.mailid = c.mailid ';
		}

		$query .= ' LEFT JOIN '.acymailing_table('users', false).' as b on a.userid = b.id ';
		$query .= ' WHERE ('.implode(') AND (', $filters).')';

		if(count($filters) > 1) $queryCount .= ' LEFT JOIN '.acymailing_table('users', false).' as b on a.userid = b.id ';

		$queryCount .= ' WHERE ('.implode(') AND (', $filters).')';

		if(!$app->isAdmin()){
			$listClass = acymailing_get('class.list');
			$lists = $listClass->getFrontendLists();
			if(!empty($lists)){
				$frontListsIds = array();
				if(empty($selectedList)){
					foreach($lists as $oneList){
						$frontListsIds[] = $oneList->listid;
					}
					$query .= ' AND c.listid IN ('.implode(',', $frontListsIds).')';
					$queryCount .= ' AND c.listid IN ('.implode(',', $frontListsIds).')';
				}
			}
			$query .= ' GROUP BY a.mailid ';
		}

		if(!empty($pageInfo->filter->order->value)){
			$query .= ' ORDER BY '.$pageInfo->filter->order->value.' '.$pageInfo->filter->order->dir;
		}

		$database->setQuery($query, $pageInfo->limit->start, $pageInfo->limit->value);
		$rows = $database->loadObjectList('mailid');

		if(!empty($rows)){
			$database->setQuery('SELECT COUNT(*) AS countqueued, mailid FROM '.acymailing_table('queue').' WHERE mailid IN ('.implode(',', array_keys($rows)).') GROUP BY mailid');
			$queueCount = $database->loadObjectList();
			if(!empty($queueCount)){
				foreach($queueCount as $oneQueueCount){
					$rows[$oneQueueCount->mailid]->countqueued = $oneQueueCount->countqueued;
				}
			}
		}


		$database->setQuery($queryCount);
		$pageInfo->elements->total = $database->loadResult();

		$pageInfo->elements->page = count($rows);

		jimport('joomla.html.pagination');
		$pagination = new JPagination($pageInfo->elements->total, $pageInfo->limit->start, $pageInfo->limit->value);

		$isAdmin = false;
		if($app->isAdmin()){
			$isAdmin = true;

			$buttonPreview = JText::_('ACY_PREVIEW');
			$acyToolbar = acymailing::get('helper.toolbar');
			if($this->type == 'autonews'){
				$acyToolbar->custom('generate', JText::_('GENERATE'), 'process', false, '');
			}elseif($this->type == 'news'){
				$buttonPreview .= ' / '.JText::_('SEND');
			}

			$acyToolbar->custom('preview', $buttonPreview, 'search', true);

			if(acymailing_level(3) && acymailing_isAllowed($config->get('acl_'.$this->aclCat.'_abtesting', 'all')) && $this->type == 'news') $acyToolbar->popup('ABtesting', JText::_('ABTESTING'), '', 800, 600);

			if(acymailing_level(3)){
				$acyToolbar->popup('import', JText::_('IMPORT'), "index.php?option=com_acymailing&ctrl=newsletter&task=upload&tmpl=component");
			}
			if(acymailing_level(3) || acymailing_isAllowed($config->get('acl_'.$this->aclCat.'_copy', 'all'))) $acyToolbar->divider();

			$acyToolbar->add();
			$acyToolbar->edit();
			if(acymailing_isAllowed($config->get('acl_'.$this->aclCat.'_copy', 'all'))) $acyToolbar->copy();
			if(acymailing_isAllowed($config->get('acl_'.$this->aclCat.'_delete', 'all'))) $acyToolbar->delete();
			$acyToolbar->divider();
			$acyToolbar->help($this->doc);
			$acyToolbar->setTitle(JText::_($this->nameListing), $this->ctrl);
			$acyToolbar->display();
		}

		$filters = new stdClass();

		if($app->isAdmin()){
			$listmailType = acymailing_get('type.listsmail');
			$listmailType->type = $this->type;
			$filters->list = $listmailType->display('filter_list', $selectedList);
		}else{
			$accessibleLists = array();
			$accessibleLists[] = JHTML::_('select.option', '0', JText::_('ALL_LISTS'));
			foreach($lists as $oneList){
				$accessibleLists[] = JHTML::_('select.option', $oneList->listid, $oneList->name);
			}
			$filters->list = JHTML::_('select.genericlist', $accessibleLists, 'filter_list', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', (int)$selectedList);
		}
		$creatorfilterType = acymailing_get('type.creatorfilter');
		$creatorfilterType->type = $this->type;

		$filters->creator = $creatorfilterType->display('filter_creator', $selectedCreator, 'mail');

		if($this->type == 'news'){
			$database->setQuery('SELECT DATE_FORMAT(FROM_UNIXTIME(senddate),"%Y-%m") AS date FROM #__acymailing_mail WHERE senddate IS NOT NULL AND senddate != 0 AND type = "news" GROUP BY date ORDER BY date DESC');
			$senddates = acymailing_loadResultArray($database);
			$sendFilter = array();
			$sendFilter[] = JHTML::_('select.option', '0', JText::_('SEND_DATE'));
			if(!empty($senddates)){
				$currentYear = '';
				foreach($senddates as $oneSenddate){
					list($year, $month) = explode('-', $oneSenddate);
					if($year != $currentYear){
						$sendFilter[] = JHTML::_('select.option', $year, '- '.$year.' -');
						$currentYear = $year;
					}
					$sendFilter[] = JHTML::_('select.option', $oneSenddate, JHTML::_('date', strtotime($oneSenddate.'-15'), ACYMAILING_J16 ? 'F' : '%B', false));
				}
			}
			$filters->date = JHTML::_('select.genericlist', $sendFilter, 'filter_date', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $selectedDate);

			if(empty($actionExists)){
				$filters->type = '';
			}else{
				$typeFilter = array();
				$typeFilter[] = JHTML::_('select.option', '', JText::_('ACY_TYPE'));
				$typeFilter[] = JHTML::_('select.option', 'news', JText::_('NEWSLETTER'));
				$typeFilter[] = JHTML::_('select.option', 'action', JText::_('ACY_DISTRIBUTION'));
				$filters->type = JHTML::_('select.genericlist', $typeFilter, 'filter_type', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $selectedType);
			}
		}

		$this->assignRef('filters', $filters);
		$toggleClass = acymailing_get('helper.toggle');
		$this->assignRef('toggleClass', $toggleClass);
		$this->assignRef('rows', $rows);
		$this->assignRef('pageInfo', $pageInfo);
		$this->assignRef('pagination', $pagination);
		$delay = acymailing_get('type.delaydisp');
		$this->assignRef('delay', $delay);
		$this->assignRef('config', $config);
		$this->assign('app', $app);
		$this->assign('isAdmin', $isAdmin);

		if($this->type == 'autonews'){
			$frequency = acymailing_get('type.frequency');
			$this->assignRef('frequencyType', $frequency);
		}
	}

	function form(){
		JHTML::_('behavior.modal', 'a.modal');
		$this->chosen = false;
		$app = JFactory::getApplication();
		$mailid = acymailing_getCID('mailid');
		$templateClass = acymailing_get('class.template');
		$config =& acymailing_config();

		$my = JFactory::getUser();
		if(!empty($mailid)){
			$mailClass = acymailing_get('class.mail');
			$mail = $mailClass->get($mailid);

			if(empty($mail->mailid)){
				acymailing_display('Newsletter '.$mailid.' not found', 'error');
				$mailid = 0;
			}
		}

		if(empty($mailid)){
			$mail = new stdClass();
			$mail->created = time();
			$mail->published = 0;
			$mail->thumb = '';
			if($this->type == 'followup') $mail->published = 1;
			$mail->visible = 1;
			$mail->html = 1;
			$mail->body = '';
			$mail->altbody = '';
			$mail->tempid = 0;

			$templateid = JRequest::getInt('templateid');
			if(empty($templateid) AND !empty($my->email)){
				$subscriberClass = acymailing_get('class.subscriber');
				$currentSubscriber = $subscriberClass->get($my->email);
				if(!empty($currentSubscriber->template)) $templateid = $currentSubscriber->template;
			}

			if(empty($templateid)){
				$myTemplate = $templateClass->getDefault();
			}else{
				$myTemplate = $templateClass->get($templateid);
			}

			if(!empty($myTemplate->tempid)){
				$mail->body = acymailing_absoluteURL($myTemplate->body);
				$mail->altbody = $myTemplate->altbody;
				$mail->tempid = $myTemplate->tempid;
				$mail->subject = $myTemplate->subject;
				$mail->replyname = $myTemplate->replyname;
				$mail->replyemail = $myTemplate->replyemail;
				$mail->fromname = $myTemplate->fromname;
				$mail->fromemail = $myTemplate->fromemail;
			}

			if($this->type == 'autonews'){
				$mail->frequency = 2592000;
			}

			if(!$app->isAdmin()){
				if($config->get('frontend_sender', 0)){
					$mail->fromname = $my->name;
					$mail->fromemail = $my->email;
				}else{
					if(empty($mail->fromname)) $mail->fromname = $config->get('from_name');
					if(empty($mail->fromemail)) $mail->fromemail = $config->get('from_email');
				}

				if($config->get('frontend_reply', 0)){
					$mail->replyname = $my->name;
					$mail->replyemail = $my->email;
				}else{
					if(empty($mail->replyname)) $mail->replyname = $config->get('reply_name');
					if(empty($mail->replyemail)) $mail->replyemail = $config->get('reply_email');
				}
			}
		}

		$sentbyname = '';
		if(!empty($mail->sentby)){
			$db = JFactory::getDBO();
			$db->setQuery('SELECT `name` FROM `#__users` WHERE `id`= '.intval($mail->sentby).' LIMIT 1');
			$sentbyname = $db->loadResult();
		}
		$this->assignRef('sentbyname', $sentbyname);

		if(JRequest::getVar('task', '') == 'replacetags'){
			$mailerHelper = acymailing_get('helper.mailer');
			$templateClass = acymailing_get('class.template');
			$mail->template = $templateClass->get($mail->tempid);

			JPluginHelper::importPlugin('acymailing');
			$mailerHelper->triggerTagsWithRightLanguage($mail, false);

			if(!empty($mail->altbody)) $mail->altbody = $mailerHelper->textVersion($mail->altbody, false);
		}

		$extraInfos = '';
		$values = new stdClass();
		if($this->type == 'followup'){
			$campaignid = JRequest::getInt('campaign', 0);
			$extraInfos .= '&campaign='.$campaignid;

			$values->delay = acymailing_get('type.delay');
			$this->assignRef('campaignid', $campaignid);
		}else{
			$listmailClass = acymailing_get('class.listmail');
			$lists = $listmailClass->getLists($mailid);
		}

		if($app->isAdmin()){


			$acyToolbar = acymailing::get('helper.toolbar');
			if(acymailing_isAllowed($config->get('acl_templates_view', 'all'))){
				$acyToolbar->popup('template', JText::_('ACY_TEMPLATE'), "index.php?option=com_acymailing&ctrl=template&task=theme&tmpl=component", 750, 550);
			}

			if(acymailing_isAllowed($config->get('acl_tags_view', 'all'))) $acyToolbar->popup('tag', JText::_('TAGS'), JURI::base()."index.php?option=com_acymailing&ctrl=tag&task=tag&tmpl=component&type=".$this->type, 780, 550);

			if(in_array($this->type, array('news', 'followup')) && acymailing_isAllowed($config->get('acl_tags_view', 'all'))){
				$acyToolbar->custom('replacetags', JText::_('REPLACE_TAGS'), 'replacetag', false);
			}

			$buttonPreview = JText::_('ACY_PREVIEW');
			if($this->type == 'news'){
				$buttonPreview .= ' / '.JText::_('SEND');
			}
			$acyToolbar->custom('savepreview', $buttonPreview, 'search', false, '');
			$acyToolbar->divider();
			$acyToolbar->addButtonOption('apply', JText::_('ACY_APPLY'), 'apply', false);
			if($app->isAdmin() && acymailing_level(1)){
				$acyToolbar->addButtonOption('saveastmpl', JText::_('ACY_SAVEASTMPL'), 'saveastmpl', false);
			}
			$acyToolbar->save();
			$acyToolbar->cancel();
			$acyToolbar->divider();
			$acyToolbar->help($this->doc);
			$acyToolbar->setTitle(JText::_($this->nameForm), $this->ctrl.'&task=edit&mailid='.$mailid.$extraInfos);
			$acyToolbar->display();
		}

		$values->maxupload = (acymailing_bytes(ini_get('upload_max_filesize')) > acymailing_bytes(ini_get('post_max_size'))) ? ini_get('post_max_size') : ini_get('upload_max_filesize');


		$toggleClass = acymailing_get('helper.toggle');
		if(!$app->isAdmin()){
			$toggleClass->ctrl = 'frontnewsletter';
			$toggleClass->extra = '&listid='.JRequest::getInt('listid');

			$copyAllLists = $lists;
			foreach($copyAllLists as $listid => $oneList){
				if(!$oneList->published OR empty($my->id)){
					unset($lists[$listid]);
					continue;
				}
				if($oneList->access_manage == 'all') continue;
				if((int)$my->id == (int)$oneList->userid) continue;
				if(!acymailing_isAllowed($oneList->access_manage)){
					unset($lists[$listid]);
					continue;
				}
			}

			if(empty($lists)){
				$app = JFactory::getApplication();
				acymailing_enqueueMessage('You don\'t have the rights to add or edit an e-mail', 'error');
				$app->redirect(acymailing_completeLink('frontnewsletter', false, true));
			}
		}


		$editor = acymailing_get('helper.editor');
		$editor->setTemplate($mail->tempid);
		$editor->name = 'editor_body';
		$editor->content = $mail->body;
		$editor->prepareDisplay();

		$js = "function updateAcyEditor(htmlvalue){";
		$js .= 'if(htmlvalue == \'0\'){window.document.getElementById("htmlfieldset").style.display = \'none\'}else{window.document.getElementById("htmlfieldset").style.display = \'block\'}';
		$js .= '}';

		$script = '
		var attachmentNb = 1;
		function addFileLoader(){
			if(attachmentNb > 9) return;
			window.document.getElementById("attachmentsdiv"+attachmentNb).style.display = "";
			attachmentNb++;
		}';

		if(!ACYMAILING_J16){
			$script .= 'function submitbutton(pressbutton){
						if (pressbutton == \'cancel\') {
							submitform( pressbutton );
							return;
						}';
		}else{
			$script .= 'Joomla.submitbutton = function(pressbutton) {
						if (pressbutton == \'cancel\') {
							Joomla.submitform(pressbutton,document.adminForm);
							return;
						}';
		}

		$script .= 'if(pressbutton == \'save\' || pressbutton == \'apply\' || pressbutton == \'savepreview\' || pressbutton == \'replacetags\'){
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

		$script .= 'if(window.document.getElementById("subject").value.length < 2){alert(\''.JText::_('ENTER_SUBJECT', true).'\'); return false;}';
		$script .= $editor->jsCode();
		if(!ACYMAILING_J16){
			$script .= 'submitform( pressbutton );} ';
		}else{
			$script .= 'Joomla.submitform(pressbutton,document.adminForm);}; ';
		}

		$script .= "function changeTemplate(newhtml,newtext,newsubject,stylesheet,fromname,fromemail,replyname,replyemail,tempid){
			if(newhtml.length>2){".$editor->setContent('newhtml')."}
			var vartextarea =$('altbody'); if(newtext.length>2) vartextarea.innerHTML = newtext;
			document.getElementById('tempid').value = tempid;
			if(fromname.length>1){
				fromname = fromname.replace('&amp;', '&');
				document.getElementById('fromname').value = fromname;
			}
			if(fromemail.length>1){document.getElementById('fromemail').value = fromemail;}
			if(replyname.length>1){
				replyname = replyname.replace('&amp;', '&');
				document.getElementById('replyname').value = replyname;
			}
			if(replyemail.length>1){document.getElementById('replyemail').value = replyemail;}
			if(newsubject.length>1){
				newsubject = newsubject.replace('&amp;', '&');
				document.getElementById('subject').value = newsubject;
			}
			".$editor->setEditorStylesheet('tempid')."
		}
		";

		if($mail->html == 1){
			$script .= "var zoneEditor = 'editor_body';";
		}else{
			$script .= "var zoneEditor = 'altbody';";
		}
		$script .= "
			var zoneToTag = 'altbody';
			function initTagZone(html){ if(html == 0){ zoneEditor = 'altbody'; }else{ zoneEditor = 'editor_body'; }}
		";

		$script .= "var previousSelection = false;
			function insertTag(tag){
				if(zoneEditor == 'editor_body'){
					try{
						jInsertEditorText(tag,'editor_body',previousSelection);
						return true;
					} catch(err){
						alert('Your editor does not enable AcyMailing to automatically insert the tag, please copy/paste it manually in your Newsletter');
						return false;
					}
				} else{
					try{
						simpleInsert(document.getElementById(zoneToTag), tag);
						return true;
					} catch(err){
						alert('Error inserting the tag in the '+ zoneToTag + 'zone. Please copy/paste it manually in your Newsletter.');
						return false;
					}
				}
			}
			";
		$script .= "function simpleInsert(myField, myValue) {
				if (document.selection) {
					myField.focus();
					sel = document.selection.createRange();
					sel.text = myValue;
				} else if (myField.selectionStart || myField.selectionStart == '0') {
					var startPos = myField.selectionStart;
					var endPos = myField.selectionEnd;
					myField.value = myField.value.substring(0, startPos)
						+ myValue
						+ myField.value.substring(endPos, myField.value.length);
				} else {
					myField.value += myValue;
				}
			}";

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js.$script);

		if($this->type == 'autonews'){
			$this->assign('frequencyType', acymailing_get('type.frequency'));
			$this->assign('generatingMode', acymailing_get('type.generatemode'));
		}

		$this->assignRef('app', $app);
		$this->assignRef('toggleClass', $toggleClass);
		$this->assignRef('lists', $lists);
		$this->assignRef('editor', $editor);
		$this->assignRef('mail', $mail);
		$tabs = acymailing_get('helper.acytabs');
		$tabs->setOptions(array('useCookie' => true));

		$this->assignRef('tabs', $tabs);
		$this->assignRef('values', $values);
		$this->assignRef('config', $config);
	}

	function preview(){
		$app = JFactory::getApplication();
		$mailid = acymailing_getCID('mailid');
		$config = acymailing_config();

		JHTML::_('behavior.modal', 'a.modal');

		$mailerHelper = acymailing_get('helper.mailer');
		$mailerHelper->loadedToSend = false;
		$mail = $mailerHelper->load($mailid);

		$user = JFactory::getUser();
		$userClass = acymailing_get('class.subscriber');
		$receiver = $userClass->get($user->email);
		$mail->sendHTML = true;
		$mailerHelper->dispatcher->trigger('acymailing_replaceusertags', array(&$mail, &$receiver, false));
		if(!empty($mail->altbody)) $mail->altbody = $mailerHelper->textVersion($mail->altbody, false);

		$listmailClass = acymailing_get('class.listmail');
		$lists = $listmailClass->getReceivers($mail->mailid, true, false);

		$testreceiverType = acymailing_get('type.testreceiver');

		$paramBase = ACYMAILING_COMPONENT.'.'.$this->getName();
		$infos = new stdClass();
		$infos->test_selection = $app->getUserStateFromRequest($paramBase.".test_selection", 'test_selection', '', 'string');
		$infos->test_group = $app->getUserStateFromRequest($paramBase.".test_group", 'test_group', '', 'string');
		$infos->test_emails = $app->getUserStateFromRequest($paramBase.".test_emails", 'test_emails', '', 'string');
		$infos->test_html = $app->getUserStateFromRequest($paramBase.".test_html", 'test_html', 1, 'int');

		if($app->isAdmin()){


			$acyToolbar = acymailing::get('helper.toolbar');
			if(acymailing_isAllowed($config->get('acl_'.$this->aclCat.'_spam_test', 'all'))){
				$acyToolbar->popup('spamtest', JText::_('SPAM_TEST'), "index.php?option=com_acymailing&ctrl=send&task=spamtest&tmpl=component&mailid=".$mailid, 1000, 638);
			}
			if($this->type == 'news'){
				if(acymailing_level(1) && acymailing_isAllowed($config->get('acl_newsletters_schedule', 'all'))){
					if($mail->published == 2){
						$acyToolbar->custom('unschedule', JText::_('UNSCHEDULE'), 'schedule', false);
					}else{
						$acyToolbar->popup('schedule', JText::_('SCHEDULE'), "index.php?option=com_acymailing&ctrl=send&task=scheduleready&tmpl=component&mailid=".$mailid);
					}
				}
				if(acymailing_isAllowed($config->get('acl_newsletters_send', 'all'))){
					$acyToolbar->popup('send', JText::_('SEND'), "index.php?option=com_acymailing&ctrl=send&task=sendready&tmpl=component&mailid=".$mailid);
				}
			}


			if(acymailing_level(1) && $mail->html == 1){
				$acyToolbar->directPrint();
			}

			$acyToolbar->divider();
			$acyToolbar->custom('edit', JText::_('ACY_EDIT'), 'edit', false);
			$acyToolbar->cancel();
			$acyToolbar->divider();
			$acyToolbar->help($this->doc);
			$acyToolbar->setTitle(JText::_('ACY_PREVIEW').' : '.$mail->subject, $this->ctrl.'&task=preview&mailid='.$mailid);
			$acyToolbar->display();
		}

		$this->assignRef('app', $app);
		$this->assignRef('lists', $lists);
		$this->assignRef('infos', $infos);
		$this->assignRef('testreceiverType', $testreceiverType);
		$this->assignRef('mail', $mail);

		if($mail->html){
			$templateClass = acymailing_get('class.template');
			if(!empty($mail->tempid)) $templateClass->createTemplateFile($mail->tempid);
			$templateClass->displayPreview('newsletter_preview_area', $mail->tempid, $mail->subject);
		}
	}

	function upload(){
		$acyToolbar = acymailing::get('helper.toolbar');
		$acyToolbar->custom('douploadnewsletter', JText::_('IMPORT'), 'import', false);
		$acyToolbar->setTitle(JText::_('IMPORT'));
		$acyToolbar->topfixed = false;
		$acyToolbar->display();
	}

	function abtesting(){
		$app = JFactory::getApplication();
		$mailids = JRequest::getString('mailid');
		$validationStatus = JRequest::getString('validationStatus');
		$noMsg = false;
		$noBtn = false;
		if((!empty($mailids) && strpos($mailids, ',') !== false)){
			$db = JFactory::getDBO();

			$warningMsg = array();

			$mailsArray = explode(',', $mailids);
			JArrayHelper::toInteger($mailsArray);

			$mailids = implode(',', $mailsArray);
			$this->assign('mailid', $mailids);
			$query = 'SELECT abtesting FROM #__acymailing_mail WHERE mailid IN ('.implode(',', $mailsArray).') AND abtesting IS NOT NULL';
			$db->setQuery($query);
			$resDetail = acymailing_loadResultArray($db);
			if(!empty($resDetail) && count($resDetail) != count($mailsArray)){
				$titlePage = JText::_('ABTESTING');
				acymailing_display(JText::_('ABTESTING_MISSINGEMAIL'), 'warning');
				$this->assign('missingMail', true);
			}else{
				$abTestDetail = array();
				if(empty($resDetail)){
					$abTestDetail['mailids'] = $mailids;
					$abTestDetail['prct'] = 10;
					$abTestDetail['delay'] = 2;
					$abTestDetail['action'] = 'manual';
				}else{
					$abTestDetail = unserialize($resDetail[0]);
					$savedIds = explode(',', $abTestDetail['mailids']);
					sort($savedIds);
					sort($mailsArray);
					if(!empty($abTestDetail['status']) && in_array($abTestDetail['status'], array('inProgress', 'testSendOver', 'abTestFinalSend')) && $savedIds != $mailsArray){
						$warningMsg[] = JText::_('ABTESTING_TESTEXIST');
						$mailsArray = $savedIds;
						$mailids = implode(',', $mailsArray);
					}
					$this->assign('savedValues', true);
					if($abTestDetail['status'] == 'inProgress') $warningMsg[] = JText::_('ABTESTING_INPROGRESS');
				}

				if($validationStatus == 'abTestAdd') $noMsg = true;

				if(!empty($abTestDetail['status']) && $abTestDetail['status'] == 'abTestFinalSend' && !empty($abTestDetail['newMail'])){
					$mailInQueueErrorMsg = JText::_('ABTESTING_FINALMAILINQUEUE');
					$mailTocheck = '='.$abTestDetail['newMail'];
				}else{
					$mailInQueueErrorMsg = JText::_('ABTESTING_TESTMAILINQUEUE');
					$mailTocheck = ' IN ('.implode(',', $mailsArray).')';
				}
				$query = "SELECT COUNT(*) FROM #__acymailing_queue WHERE mailid".$mailTocheck;
				$db->setQuery($query);
				$queueCheck = $db->loadResult();
				if(!empty($queueCheck) && $validationStatus != 'abTestAdd'){
					acymailing_enqueueMessage($mailInQueueErrorMsg, 'error');
					$noMsg = true;
				}

				if(!empty($resDetail) && empty($queueCheck) && in_array($abTestDetail['status'], array('inProgress', 'abTestFinalSend'))){
					if($abTestDetail['status'] == 'inProgress'){
						$abTestDetail['status'] = 'testSendOver';
					}else $abTestDetail['status'] = 'completed';
					$query = "UPDATE #__acymailing_mail SET abtesting=".$db->quote(serialize($abTestDetail))." WHERE mailid IN (".implode(',', $mailsArray).")";
					$db->setQuery($query);
					$db->query();
				}

				if(!empty($abTestDetail['status']) && $abTestDetail['status'] == 'testSendOver') acymailing_enqueueMessage(JText::_('ABTESTING_READYTOSEND'), 'info');
				if(!empty($abTestDetail['status']) && $abTestDetail['status'] == 'completed') acymailing_enqueueMessage(JText::_('ABTESTING_COMPLETE'), 'info');

				$this->assign('abTestDetail', $abTestDetail);

				$nbMails = count($mailsArray);
				$titleStr = "A/B/C/D/E/F/G/H/I/J/K/L/M/N/O/P/Q/R/S/T/U/V/W/X/Y/Z";
				$titlePage = JText::sprintf('ABTESTING_TITLE', substr($titleStr, 0, min($nbMails, 26) * 2 - 1));
				$mailClass = acymailing_get('class.mail');
				$mailsDetails = array();
				foreach($mailsArray as $mailid){
					$mailsDetails[] = $mailClass->get($mailid);
				}
				$this->assign('mailsdetails', $mailsDetails);

				$mailerHelper = acymailing_get('helper.mailer');
				$mailerHelper->loadedToSend = false;
				$mailReceiver = $mailerHelper->load($mailsArray[0]);
				$listmailClass = acymailing_get('class.listmail');
				$lists = $listmailClass->getReceivers($mailReceiver->mailid, true, false);
				$this->assign('lists', $lists);
				$this->assign('mailReceiver', $mailReceiver);
				$filterClass = acymailing_get('class.filter');
				$this->assign('filterClass', $filterClass);
				$listids = array();
				foreach($lists as $oneList){
					$listids[] = $oneList->listid;
				}
				$nbTotalReceivers = $filterClass->countReceivers($listids, $this->mailReceiver->filter, $this->mailReceiver->mailid);
				if($nbTotalReceivers < 50){
					$warningMsg[] = JText::sprintf('ABTESTING_NOTENOUGHUSER', $nbTotalReceivers);
					$noBtn = true;
				}
				$this->assign('nbTotalReceivers', $nbTotalReceivers);
				$this->assign('nbTestReceivers', floor($nbTotalReceivers * $abTestDetail['prct'] / 100));

				if($noMsg || $noBtn) $noButton = true;

				$queryStat = 'SELECT mailid, openunique, clickunique, senthtml, senttext, bounceunique FROM #__acymailing_stats WHERE mailid IN ('.$mailids.')';
				$db->setQuery($queryStat);
				$resStat = $db->loadObjectList('mailid');
				if(!empty($resStat)){
					$this->assign('statMail', $resStat);
					$warningMsg[] = JText::_('ABTESTING_STAT_WARNING');
				}
				if(!empty($warningMsg) && $noMsg == false) acymailing_enqueueMessage(implode('<br />', $warningMsg), 'warning');
			}
		}else{
			$titlePage = JText::_('ABTESTING');
		}

		$acyToolbar = acymailing::get('helper.toolbar');
		if(empty($noButton)){
			$acyToolbar->custom('test', JText::_('ABTESTING_TEST'), 'test', false, "javascript:if(confirm('".JText::_('PROCESS_CONFIRMATION', true)."')){submitbutton('abtest');} return false;");
		}
		$acyToolbar->setTitle(JText::_('ABTESTING'));
		$acyToolbar->topfixed = false;
		$acyToolbar->display();


		$this->assign('validationStatus', $validationStatus);
		$this->assign('titlePage', $titlePage);
		$this->assign('app', $app);

		if($app->isAdmin()){
			acymailing_setPageTitle(JText::_('ABTESTING'));
		}
	}
}
