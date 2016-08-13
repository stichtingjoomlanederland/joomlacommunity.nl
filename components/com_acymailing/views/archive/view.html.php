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


class archiveViewArchive extends acymailingView{
	function display($tpl = null){
		$function = $this->getLayout();
		if(method_exists($this, $function)) $this->$function();

		parent::display($tpl);
	}

	function forward(){
		$my = JFactory::getUser();
		$subkeys = JRequest::getString('subid', JRequest::getString('sub'));
		if(!empty($subkeys)){
			$db = JFactory::getDBO();
			$subid = intval(substr($subkeys, 0, strpos($subkeys, '-')));
			$subkey = substr($subkeys, strpos($subkeys, '-') + 1);
			$db->setQuery('SELECT * FROM '.acymailing_table('subscriber').' WHERE `subid` = '.intval($subid).' AND `key` = '.$db->Quote($subkey).' LIMIT 1');
			$receiver = $db->loadObject();
		}
		if(empty($receiver) AND !empty($my->email)){
			$userClass = acymailing_get('class.subscriber');
			$receiver = $userClass->get($my->email);
		}
		if(empty($receiver)){
			$receiver = new stdClass();
			$receiver->name = '';
			$receiver->email = '';
		}
		$this->assignRef('senderName', $receiver->name);
		$this->assignRef('senderMail', $receiver->email);
		$config = acymailing_config();
		$this->assignRef('config', $config);

		$js = 'var numForwarders = 1;function addLine(){
							if(numForwarders > 4) return;
							var myTable = window.document.getElementById("friend_table");
							var line1 = document.createElement("tr");
							var tdname = document.createElement("td");
							var itdname = document.createElement("td");
							var line2 = document.createElement("tr");
							var tdemail = document.createElement("td");
							var itdemail = document.createElement("td");

							var inputName = document.createElement("input");
							inputName.type = \'text\';
							inputName.name = \'forwardusers[\'+numForwarders+\'][name]\';
							inputName.style.width = "200px";

							var inputEmail = document.createElement("input");
							inputEmail.type = \'text\';
							inputEmail.name = \'forwardusers[\'+numForwarders+\'][email]\';
							inputEmail.style.width = "200px";

							var nameLabel = document.createElement("label");
							nameLabel.innerHTML="'.JText::_('FRIEND_NAME', true).'";

							var emailLabel = document.createElement("label");
							emailLabel.innerHTML="'.JText::_('FRIEND_EMAIL', true).'";

							tdname.appendChild(nameLabel);
							itdname.appendChild(inputName);
							line1.appendChild(tdname);
							line1.appendChild(itdname);
							myTable.appendChild(line1);

							tdemail.appendChild(emailLabel);
							itdemail.appendChild(inputEmail);
							line2.appendChild(tdemail);
							line2.appendChild(itdemail);
							myTable.appendChild(line2);
							numForwarders++;
			}
';

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);
		return $this->view();
	}

	private function addFeed(){

		$config = acymailing_config();
		$document = JFactory::getDocument();

		$link = '&format=feed&limitstart=';
		if($config->get('acyrss_format') == 'rss' || $config->get('acyrss_format') == 'both'){
			$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
			$document->addHeadLink(JRoute::_($link.'&type=rss'), 'alternate', 'rel', $attribs);
		}
		if($config->get('acyrss_format') == 'atom' || $config->get('acyrss_format') == 'both'){
			$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
			$document->addHeadLink(JRoute::_($link.'&type=atom'), 'alternate', 'rel', $attribs);
		}
	}

	function listing(){
		global $Itemid;

		$app = JFactory::getApplication();
		$my = JFactory::getUser();

		$values = new stdClass();
		$jsite = JFactory::getApplication('site');
		$menus = $jsite->getMenu();
		$menu = $menus->getActive();

		if(empty($menu) AND !empty($Itemid)){
			$menus->setActive($Itemid);
			$menu = $menus->getItem($Itemid);
		}

		$myItem = empty($Itemid) ? '' : '&Itemid='.$Itemid;
		$this->assignRef('item', $myItem);

		if(is_object($menu)){
			jimport('joomla.html.parameter');
			$menuparams = new acyParameter($menu->params);
		}

		$pageInfo = new stdClass();
		$pageInfo->filter = new stdClass();
		$pageInfo->filter->order = new stdClass();
		$pageInfo->limit = new stdClass();
		$pageInfo->elements = new stdClass();

		$paramBase = ACYMAILING_COMPONENT.'.'.$this->getName();
		$pageInfo->filter->order->dir = $app->getUserStateFromRequest($paramBase.".ordering_dir", 'ordering_dir', 'DESC', 'word');
		$pageInfo->filter->order->value = $app->getUserStateFromRequest($paramBase.".ordering", 'ordering', 'senddate', 'cmd');
		if(strtolower($pageInfo->filter->order->dir) !== 'desc') $pageInfo->filter->order->dir = 'asc';
		$pageInfo->search = $app->getUserStateFromRequest($paramBase.".search", 'search', '', 'string');
		$pageInfo->search = JString::strtolower(trim($pageInfo->search));

		$pageInfo->limit->value = $app->getUserStateFromRequest($paramBase.'.list_limit', 'limit', $app->getCfg('list_limit'), 'int');
		$pageInfo->limit->start = JRequest::getInt('limitstart', 0);

		$listClass = acymailing_get('class.list');
		$listid = acymailing_getCID('listid');

		if(empty($listid) AND !empty($menuparams)){
			$listid = $menuparams->get('listid');
		}

		if(empty($listid)){
			$allLists = $listClass->getLists('listid');
			if(acymailing_level(1)) $allLists = $listClass->onlyCurrentLanguage($allLists);
			if(acymailing_level(3)) $allLists = $listClass->onlyAllowedLists($allLists);
		}else{
			$oneList = $listClass->get($listid);
			if(empty($oneList->listid)) return JError::raiseError(404, 'Mailing List not found : '.$listid);
			$allLists = array($oneList->listid => $oneList);
			if($oneList->access_sub != 'all' && ($oneList->access_sub == 'none' || empty($my->id) || !acymailing_isAllowed($oneList->access_sub))) $allLists = array();
		}

		if(empty($allLists)){
			if(empty($my->id)){
				$usercomp = ACYMAILING_J16 ? 'com_users' : 'com_user';
				$uri = JFactory::getURI();
				$app->redirect('index.php?option='.$usercomp.'&view=login&return='.base64_encode($uri->toString()), JText::_('ACY_NOTALLOWED'));
			}else{
				$app->enqueueMessage(JText::_('ACY_NOTALLOWED'), 'error');
				$app->redirect(acymailing_completeLink('lists', false, true));
			}
			return false;
		}

		$doc = JFactory::getDocument();
		$db = JFactory::getDBO();
		$pathway = $app->getPathway();
		$config = acymailing_config();

		if(!empty($menuparams)){
			$values->suffix = $menuparams->get('pageclass_sfx', '');
			$values->page_title = $menuparams->get('page_title');
			$values->page_heading = ACYMAILING_J16 ? $menuparams->get('page_heading') : $menuparams->get('page_title');
			$values->show_page_heading = ACYMAILING_J16 ? $menuparams->get('show_page_heading', 1) : $menuparams->get('show_page_title', 1);
		}else{
			$values->suffix = '';
			$values->show_page_heading = 1;
		}

		$values->show_description = $config->get('show_description', 1);
		$values->show_senddate = $config->get('show_senddate', 1);
		$values->show_receiveemail = $config->get('show_receiveemail', 0) && acymailing_level(1);
		$values->filter = $config->get('show_filter', 1);

		if(empty($values->page_title)) $values->page_title = (count($allLists) > 1 || empty($listid)) ? JText::_('NEWSLETTERS') : $allLists[$listid]->name;
		if(empty($values->page_heading)) $values->page_heading = (count($allLists) > 1 || empty($listid)) ? JText::_('NEWSLETTERS') : $allLists[$listid]->name;

		if(empty($menuparams)){
			$pathway->addItem(JText::_('MAILING_LISTS'), acymailing_completeLink('lists'));
			$pathway->addItem($values->page_title);
		}elseif(!$menuparams->get('listid')){
			$pathway->addItem($values->page_title);
		}

		acymailing_setPageTitle($values->page_title);

		$this->addFeed();

		$searchMap = array('a.mailid', 'a.subject', 'a.alias');
		$filters = array();
		if(!empty($pageInfo->search)){
			$searchVal = '\'%'.acymailing_getEscaped($pageInfo->search, true).'%\'';
			$filters[] = implode(" LIKE $searchVal OR ", $searchMap)." LIKE $searchVal";
		}

		$filters[] = 'a.type = \'news\'';

		$noManageableLists = array();
		foreach($allLists as &$oneList){
			if(empty($my->id)) $noManageableLists[] = $oneList->listid;
			if((int)$my->id == (int)$oneList->userid) continue;
			if($oneList->access_manage == 'all' || acymailing_isAllowed($oneList->access_manage)) continue;
			$noManageableLists[] = $oneList->listid;
		}

		$accessFilter = '';
		$manageableLists = array_diff(array_keys($allLists), $noManageableLists);
		if(!empty($manageableLists)) $accessFilter = 'c.listid IN ('.implode(',', $manageableLists).')';
		if(!empty($noManageableLists)){
			if(empty($accessFilter)){
				$accessFilter = 'c.listid IN ('.implode(',', $noManageableLists).') AND a.published = 1 AND a.visible = 1';
			}else $accessFilter .= ' OR (c.listid IN ('.implode(',', $noManageableLists).') AND a.published = 1 AND a.visible = 1)';
		}
		if(!empty($accessFilter)) $filters[] = $accessFilter;

		if($config->get('open_popup', 1) || !empty($manageableLists)) JHTML::_('behavior.modal', 'a.modal');

		$selection = array_merge($searchMap, array('a.senddate', 'a.created', 'a.visible', 'a.published', 'a.fromname', 'a.fromemail', 'a.replyname', 'a.replyemail', 'a.userid', 'a.summary', 'a.thumb', 'c.listid'));

		$query = 'SELECT "" AS body, "" AS altbody, html AS sendHTML, '.implode(',', $selection);
		$query .= ' FROM '.acymailing_table('listmail').' as c';
		$query .= ' JOIN '.acymailing_table('mail').' as a on a.mailid = c.mailid ';
		$query .= ' WHERE ('.implode(') AND (', $filters).')';
		$query .= ' GROUP BY c.mailid';
		$query .= ' ORDER BY a.'.acymailing_secureField($pageInfo->filter->order->value).' '.acymailing_secureField($pageInfo->filter->order->dir).', c.mailid DESC';

		$db->setQuery($query, $pageInfo->limit->start, $pageInfo->limit->value);
		$rows = $db->loadObjectList();
		$pageInfo->elements->page = count($rows);

		if($pageInfo->limit->value > $pageInfo->elements->page){
			$pageInfo->elements->total = $pageInfo->limit->start + $pageInfo->elements->page;
		}else{
			$queryCount = 'SELECT COUNT(DISTINCT c.mailid) FROM '.acymailing_table('listmail').' as c';
			$queryCount .= ' JOIN '.acymailing_table('mail').' as a on a.mailid = c.mailid ';
			$queryCount .= ' WHERE ('.implode(') AND (', $filters).')';
			$db->setQuery($queryCount);
			$pageInfo->elements->total = $db->loadResult();
		}

		if(!empty($my->email)){
			$userClass = acymailing_get('class.subscriber');
			$receiver = $userClass->get($my->email);
		}
		if(empty($receiver)){
			$receiver = new stdClass();
			$receiver->name = JText::_('VISITOR');
		}
		JPluginHelper::importPlugin('acymailing');
		$dispatcher = JDispatcher::getInstance();
		foreach($rows as $mail){
			if(strpos($mail->subject, "{") !== false){
				$dispatcher->trigger('acymailing_replacetags', array(&$mail, false));
				$dispatcher->trigger('acymailing_replaceusertags', array(&$mail, &$receiver, false));
			}
		}

		jimport('joomla.html.pagination');
		$pagination = new JPagination($pageInfo->elements->total, $pageInfo->limit->start, $pageInfo->limit->value);

		$js = 'function tableOrdering( order, dir, task ){
			var form = document.adminForm;

			form.filter_order.value 	= order;
			form.filter_order_Dir.value	= dir;
			document.adminForm.submit( task );
		}

		function changeReceiveEmail(checkedbox){
			var form = document.adminForm;
			if(checkedbox){
				form.nbreceiveemail.value++;
			}else{
				form.nbreceiveemail.value--;
			}

			if(form.nbreceiveemail.value > 0 ){
				document.getElementById(\'receiveemailbox\').className = \'receiveemailbox receiveemailbox_visible\';
			}else{
				document.getElementById(\'receiveemailbox\').className = \'receiveemailbox receiveemailbox_hidden\';
			}
		}
		';

		$doc->addScriptDeclaration($js);

		$orderValues = array();
		$orderValues[] = JHTML::_('select.option', 'senddate', JText::_('SEND_DATE'));
		$orderValues[] = JHTML::_('select.option', 'subject', JText::_('JOOMEXT_SUBJECT'));
		$orderValues[] = JHTML::_('select.option', 'created', JText::_('CREATED_DATE'));
		$orderValues[] = JHTML::_('select.option', 'mailid', JText::_('ACY_ID'));

		$ordering = '';
		if($config->get('show_order', 1) == 1){
			$ordering = '<span style="float:right;" id="orderingoption">';
			$ordering .= JHTML::_('select.genericlist', $orderValues, 'ordering', 'size="1" style="width:100px;" onchange="this.form.submit();"', 'value', 'text', $pageInfo->filter->order->value);

			$orderDir = array();
			$orderDir[] = JHTML::_('select.option', 'ASC', JText::_('ACY_ASC'));
			$orderDir[] = JHTML::_('select.option', 'DESC', JText::_('ACY_DESC'));
			$ordering .= ' '.JHTML::_('select.genericlist', $orderDir, 'ordering_dir', 'size="1" style="width:75px;" onchange="this.form.submit();"', 'value', 'text', $pageInfo->filter->order->dir);
			$ordering .= '</span>';
		}

		$this->assignRef('ordering', $ordering);
		$this->assignRef('rows', $rows);
		$this->assignRef('values', $values);
		if(count($allLists) > 1){
			$list = new stdClass();
			$list->listid = 0;
			$list->description = '';
		}else{
			$list = array_pop($allLists);
		}
		$this->assignRef('list', $list);
		$this->assignRef('manageableLists', $manageableLists);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('pageInfo', $pageInfo);
		$this->assignRef('config', $config);
		$this->assignRef('my', $my);
	}

	function view(){

		global $Itemid;

		$app = JFactory::getApplication();

		$document = JFactory::getDocument();

		$this->addFeed();



		$pathway = $app->getPathway();
		$my = JFactory::getUser();

		$frontEndManagement = false;
		$listid = acymailing_getCID('listid');

		$values = new stdClass();
		$values->suffix = '';
		$jsite = JFactory::getApplication('site');
		$menus = $jsite->getMenu();
		$menu = $menus->getActive();

		if(empty($menu) AND !empty($Itemid)){
			$menus->setActive($Itemid);
			$menu = $menus->getItem($Itemid);
		}

		if(is_object($menu)){
			jimport('joomla.html.parameter');
			$menuparams = new acyParameter($menu->params);
		}

		if(!empty($menuparams)){
			$values->suffix = $menuparams->get('pageclass_sfx', '');
		}

		if(empty($listid) && !empty($menuparams)){
			$listid = $menuparams->get('listid');
			if($menuparams->get('menu-meta_description')) $document->setDescription($menuparams->get('menu-meta_description'));
			if($menuparams->get('menu-meta_keywords')) $document->setMetadata('keywords', $menuparams->get('menu-meta_keywords'));
			if($menuparams->get('robots')) $document->setMetadata('robots', $menuparams->get('robots'));
			if($menuparams->get('page_title')) acymailing_setPageTitle($menuparams->get('page_title'));
		}

		$config = acymailing_config();
		$indexFollow = $config->get('indexFollow', '');
		$tagIndFol = array();
		if(strpos($indexFollow, 'noindex') !== false) $tagIndFol[] = 'noindex';
		if(strpos($indexFollow, 'nofollow') !== false) $tagIndFol[] = 'nofollow';
		if(!empty($tagIndFol)) $document->setMetadata('robots', implode(',', $tagIndFol));

		if(!empty($listid)){
			$listClass = acymailing_get('class.list');
			$oneList = $listClass->get($listid);
			if(!empty($oneList->visible) AND $oneList->published AND (empty($menuparams) || !$menuparams->get('listid'))){
				$pathway->addItem($oneList->name, acymailing_completeLink('archive&listid='.$oneList->listid.':'.$oneList->alias));
			}

			if(!empty($oneList->listid) AND acymailing_level(3)){
				if(!empty($my->id) AND (int)$my->id == (int)$oneList->userid){
					$frontEndManagement = true;
				}
				if(!empty($my->id)){
					if($oneList->access_manage == 'all' OR acymailing_isAllowed($oneList->access_manage)){
						$frontEndManagement = true;
					}
				}
			}
		}

		$mailid = JRequest::getString('mailid', 'nomailid');
		if(empty($mailid)){
			die('This is a Newsletter-template... and you can not access the online version of a Newsletter-template!<br />Please <a href="administrator/index.php?option=com_acymailing&ctrl=newsletter&task=edit" >create a Newsletter</a> using your template and then try again your "view it online" link!');
			exit;
		}

		if($mailid == 'nomailid'){
			$db = JFactory::getDBO();
			$query = 'SELECT m.`mailid` FROM `#__acymailing_list` as l JOIN `#__acymailing_listmail` as lm ON l.listid=lm.listid JOIN `#__acymailing_mail` as m on lm.mailid = m.mailid';
			$query .= ' WHERE l.`visible` = 1 AND l.`published` = 1 AND m.`visible`= 1 AND m.`published` = 1 AND m.`type` = "news" AND l.`type` = "list"';
			if(!empty($listid)) $query .= ' AND l.`listid` = '.(int)$listid;
			$query .= ' ORDER BY m.`senddate` DESC, m.`mailid` DESC LIMIT 1';
			$db->setQuery($query);
			$mailid = $db->loadResult();
		}
		$mailid = intval($mailid);
		if(empty($mailid)) return JError::raiseError(404, 'Newsletter not found');

		$access_sub = true;
		if(acymailing_level(3)){
			$listmail = acymailing_get('class.listmail');
			$allLists = $listmail->getLists($mailid);
			$access_sub = false;
			if(!empty($allLists)){
				foreach($allLists as $alist){
					if(empty($alist->mailid)) continue;
					if(!$alist->published OR !$alist->visible OR $alist->access_sub == 'none') continue;
					if(acymailing_isAllowed($alist->access_sub)){
						$access_sub = true;
						break;
					}
				}
			}
		}

		$mailClass = acymailing_get('helper.mailer');
		$mailClass->loadedToSend = false;
		$oneMail = $mailClass->load($mailid);

		if(empty($oneMail->mailid)){
			return JError::raiseError(404, 'Newsletter not found : '.$mailid);
		}

		if(!$frontEndManagement AND (!$access_sub OR !$oneMail->published OR !$oneMail->visible)){
			$key = JRequest::getCmd('key');
			if(empty($key) OR $key !== $oneMail->key){
				$reason = (!$oneMail->published) ? 'Newsletter not published' : (!$oneMail->visible ? 'Newsletter not visible' : (!$access_sub ? 'Access not allowed' : ''));
				$app->enqueueMessage('You can not have access to this e-mail : '.$reason, 'error');
				$app->redirect(acymailing_completeLink('lists', false, true));
				return false;
			}
		}

		$fshare = '';
		if(preg_match('#<img[^>]*id="pictshare"[^>]*>#i', $oneMail->body, $pregres) && preg_match('#src="([^"]*)"#i', $pregres[0], $pict)){
			$fshare = $pict[1];
		}elseif(preg_match('#<img[^>]*class="[^"]*pictshare[^"]*"[^>]*>#i', $oneMail->body, $pregres) && preg_match('#src="([^"]*)"#i', $pregres[0], $pict)){
			$fshare = $pict[1];
		}elseif(preg_match('#class="acymailing_content".*(<img[^>]*>)#is', $oneMail->body, $pregres) && preg_match('#src="([^"]*)"#i', $pregres[1], $pict)){
			if(strpos($pregres[1], JText::_('JOOMEXT_READ_MORE')) === false) $fshare = $pict[1];
		}

		if(!empty($fshare)){
			$document->setMetadata('og:image', $fshare);
		}

		$document->setMetadata('og:url', acymailing_frontendLink('index.php?option=com_acymailing&ctrl=archive&task=view&mailid='.$oneMail->mailid, JRequest::getCmd('tmpl') == 'component' ? true : false));
		$document->setMetadata('og:title', $oneMail->subject);
		if(!empty($oneMail->metadesc)) $document->setMetadata('og:description', $oneMail->metadesc);

		$subkeys = JRequest::getString('subid', JRequest::getString('sub'));
		if(!empty($subkeys)){
			$db = JFactory::getDBO();
			$subid = intval(substr($subkeys, 0, strpos($subkeys, '-')));
			$subkey = substr($subkeys, strpos($subkeys, '-') + 1);
			$db->setQuery('SELECT * FROM '.acymailing_table('subscriber').' WHERE `subid` = '.$db->Quote($subid).' AND `key` = '.$db->Quote($subkey).' LIMIT 1');
			$receiver = $db->loadObject();
		}

		if(empty($receiver) AND !empty($my->email)){
			$userClass = acymailing_get('class.subscriber');
			$receiver = $userClass->get($my->email);
		}

		if(empty($receiver)){
			$receiver = new stdClass();
			$receiver->name = JText::_('VISITOR');
		}

		$oneMail->sendHTML = true;
		$mailClass->dispatcher->trigger('acymailing_replaceusertags', array(&$oneMail, &$receiver, false));

		$pathway->addItem($oneMail->subject);

		$document = JFactory::getDocument();
		acymailing_setPageTitle($oneMail->subject);

		if(!empty($oneMail->metadesc)){
			$document->setDescription($oneMail->metadesc);
		}
		if(!empty($oneMail->metakey)){
			$document->setMetadata('keywords', $oneMail->metakey);
		}

		$this->assignRef('mail', $oneMail);
		$this->assignRef('frontEndManagement', $frontEndManagement);
		$this->assignRef('list', $oneList);
		$config =& acymailing_config();
		$this->assignRef('config', $config);
		$this->assignRef('my', $my);
		$this->assignRef('receiver', $receiver);
		$this->assignRef('values', $values);

		if($oneMail->html){
			$templateClass = acymailing_get('class.template');
			$templateClass->archiveSection = true;
			$templateClass->displayPreview('newsletter_preview_area', $oneMail->tempid, $oneMail->subject);
		}
	}
}
