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

class listClass extends acymailingClass{

	var $tables = array('listsub', 'listcampaign', 'listmail', 'list');
	var $pkey = 'listid';
	var $namekey = 'alias';
	var $type = 'list';
	var $newlist = false;

	function getLists($index = '', $listids = 'all'){
		$onlyListids = array();
		if(strtolower($listids) != 'all'){
			$onlyListids = explode(',', $listids);
			JArrayHelper::toInteger($onlyListids);
		}

		$query = 'SELECT * FROM '.acymailing_table('list').' WHERE type = \''.$this->type.'\' '.(empty($onlyListids) ? '' : 'AND listid IN ('.implode(',', $onlyListids).')').' ORDER BY ordering ASC';
		$this->database->setQuery($query);
		return $this->database->loadObjectList($index);
	}

	function getAllCampaigns($index = ''){
		$query = 'SELECT * FROM '.acymailing_table('list').' WHERE type = \'campaign\' ORDER BY ordering ASC';
		$this->database->setQuery($query);
		return $this->database->loadObjectList($index);
	}


	function delete($elements){
		if(!is_array($elements)){
			$elements = array($elements);
		}

		JArrayHelper::toInteger($elements);

		if(empty($elements)) return 0;

		$this->database->setQuery('DELETE FROM #__acymailing_listcampaign WHERE `campaignid` IN ('.implode(',', $elements).')');
		$this->database->query();

		$this->database->setQuery('DELETE #__acymailing_mail, #__acymailing_listmail FROM #__acymailing_mail INNER JOIN #__acymailing_listmail WHERE #__acymailing_mail.mailid=#__acymailing_listmail.mailid AND #__acymailing_mail.type=\'followup\' AND #__acymailing_listmail.listid IN ('.implode(',', $elements).')');
		$this->database->query();

		return parent::delete($elements);
	}

	function getFrontendLists($index = ''){
		$my = JFactory::getUser();
		if(empty($my->id)) return array();

		if(!ACYMAILING_J16){
			$groups = array($my->gid);
		}else{
			jimport('joomla.access.access');
			$groups = JAccess::getGroupsByUser($my->id, false);
		}

		$possibleValues = array();
		$possibleValues[] = 'access_manage = \'all\'';
		$possibleValues[] = 'userid = '.intval($my->id);
		foreach($groups as $oneGroup){
			$possibleValues[] = 'access_manage LIKE \'%,'.intval($oneGroup).',%\'';
		}

		$query = 'SELECT * FROM '.acymailing_table('list').' WHERE published = 1 AND type = \''.$this->type.'\' AND ('.implode(' OR ', $possibleValues).') ORDER BY ordering ASC';
		$this->database->setQuery($query);
		return $this->database->loadObjectList($index);
	}

	function getFrontendCampaigns($index = ''){
		$my = JFactory::getUser();
		if(empty($my->id)) return array();

		if(!ACYMAILING_J16){
			$groups = array($my->gid);
		}else{
			jimport('joomla.access.access');
			$groups = JAccess::getGroupsByUser($my->id, false);
		}

		$possibleValues = array();
		$possibleValues[] = 'access_manage = \'all\'';
		$possibleValues[] = 'userid = '.intval($my->id);
		foreach($groups as $oneGroup){
			$possibleValues[] = 'access_manage LIKE \'%,'.intval($oneGroup).',%\'';
		}

		$query = 'SELECT DISTINCT l.* FROM '.acymailing_table('list').' AS l INNER JOIN '.acymailing_table('listcampaign').' AS lc ON l.listid = lc.campaignid WHERE lc.listid IN (SELECT DISTINCT il.listid FROM '.acymailing_table('listcampaign').' AS ilc INNER JOIN '.acymailing_table('list').' AS il ON ilc.listid = il.listid WHERE il.published = 1 AND il.type = \'list\' AND ('.implode(' OR ', $possibleValues).')) AND l.published = 1 ORDER BY ordering ASC';
		$this->database->setQuery($query);
		return $this->database->loadObjectList($index);
	}

	function get($listid, $default = null){
		$query = 'SELECT a.*, b.name as creatorname, b.username, b.email FROM '.acymailing_table('list').' as a LEFT JOIN '.acymailing_table('users', false).' as b on a.userid = b.id WHERE listid = '.intval($listid).' LIMIT 1';
		$this->database->setQuery($query);
		return $this->database->loadObject();
	}

	function saveForm(){
		$app = JFactory::getApplication();

		$list = new stdClass();
		$list->listid = acymailing_getCID('listid');

		$formData = JRequest::getVar('data', array(), '', 'array');

		if(!empty($formData['list']['category']) && $formData['list']['category'] == -1){
			$formData['list']['category'] = JRequest::getString('newcategory', '');
		}

		foreach($formData['list'] as $column => $value){
			if($app->isAdmin() || $this->allowedField('list', $column)){
				acymailing_secureField($column);
				$list->$column = strip_tags($value);
			}
		}

		$list->description = JRequest::getVar('editor_description', '', '', 'string', JREQUEST_ALLOWHTML);
		if(isset($list->published) && $list->published != 1) $list->published = 0;
		$listid = $this->save($list);
		if(!$listid) return false;

		if(empty($list->listid)){
			$orderClass = acymailing_get('helper.order');
			$orderClass->pkey = 'listid';
			$orderClass->table = 'list';
			$orderClass->groupMap = 'type';
			$orderClass->groupVal = empty($list->type) ? $this->type : $list->type;
			$orderClass->reOrder();

			$this->newlist = true;
		}

		if(!empty($formData['listcampaign'])){
			$affectedLists = array();
			foreach($formData['listcampaign'] as $affectlistid => $receiveme){
				if(!empty($receiveme)){
					$affectedLists[] = $affectlistid;
				}
			}

			$listCampaignClass = acymailing_get('class.listcampaign');
			$listCampaignClass->save($listid, $affectedLists);
		}

		JRequest::setVar('listid', $listid);

		return true;
	}

	function save($list){
		if(empty($list->listid)){
			if(empty($list->userid)){
				$user = JFactory::getUser();
				$list->userid = $user->id;
			}
			if(empty($list->alias)) $list->alias = $list->name;
		}

		if(isset($list->alias)){
			if(empty($list->alias)) $list->alias = $list->name;
			$jconfig = JFactory::getConfig();
			$method = $jconfig->get('unicodeslugs', 0) == 1 ? 'stringURLUnicodeSlug' : 'stringURLSafe';
			$list->alias = JFilterOutput::$method(trim($list->alias));
		}

		JPluginHelper::importPlugin('acymailing');
		$dispatcher = JDispatcher::getInstance();
		if(empty($list->listid)){
			$dispatcher->trigger('onAcyBeforeListCreate', array(&$list));
			$status = $this->database->insertObject(acymailing_table('list'), $list);
		}else{
			$dispatcher->trigger('onAcyBeforeListModify', array(&$list));
			$status = $this->database->updateObject(acymailing_table('list'), $list, 'listid');
		}


		if($status) return empty($list->listid) ? $this->database->insertid() : $list->listid;
		return false;
	}

	function onlyCurrentLanguage($lists){
		$currentLanguage = JFactory::getLanguage();
		$currentLang = strtolower($currentLanguage->getTag());

		$newLists = array();
		foreach($lists as $id => $oneList){
			if($oneList->languages == 'all' OR in_array($currentLang, explode(',', $oneList->languages))){
				$newLists[$id] = $oneList;
			}
		}

		return $newLists;
	}

	function onlyAllowedLists($lists){
		$newLists = array();
		foreach($lists as $id => $oneList){
			if(!$oneList->published) continue;
			if(!acymailing_isAllowed($oneList->access_sub)) continue;
			$newLists[$id] = $oneList;
		}
		return $newLists;
	}

	function getCampaigns($listid){
		if(empty($listid)) return array();

		if(is_array($listid)) $listid = implode(',', $listid);
		$query = 'SELECT  b.listid, b.campaignid FROM '.acymailing_table('list').' as a LEFT JOIN '.acymailing_table('listcampaign').' as b on a.listid = b.listid WHERE a.type = \'list\' AND b.listid IN ( '.$listid.') ORDER BY b.listid';
		$this->database->setQuery($query);
		$resSql = $this->database->loadObjectList();
		$listCampaigns = array();
		foreach($resSql as $oneList){
			$listCampaigns[$oneList->listid][] = $oneList->campaignid;
		}
		return $listCampaigns;
	}

}
