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

$app = JFactory::getApplication();
$doc = JFactory::getDocument();
$doc->addStyleSheet(ACYMAILING_CSS.'frontendedition.css?v='.filemtime(ACYMAILING_MEDIA.'css'.DS.'frontendedition.css'));
$config = acymailing_config();

$pageInfo = new stdClass();
$pageInfo->filter = new stdClass();
$pageInfo->filter->order = new stdClass();
$pageInfo->limit = new stdClass();
$pageInfo->elements = new stdClass();

$paramBase = ACYMAILING_COMPONENT.'.'.$this->getName().$this->getLayout();
$pageInfo->filter->order->value = $app->getUserStateFromRequest($paramBase.".filter_order", 'filter_order', '', 'cmd');
$pageInfo->filter->order->dir = $app->getUserStateFromRequest($paramBase.".filter_order_Dir", 'filter_order_Dir', 'desc', 'word');
if(strtolower($pageInfo->filter->order->dir) !== 'desc') $pageInfo->filter->order->dir = 'asc';
$pageInfo->search = $app->getUserStateFromRequest($paramBase.".search", 'search', '', 'string');
$pageInfo->search = JString::strtolower(trim($pageInfo->search));
$selectedMail = $app->getUserStateFromRequest($paramBase."filter_mail", 'filter_mail', 0, 'int');
$selectedUrl = $app->getUserStateFromRequest($paramBase."filter_url", 'filter_url', 0, 'int');

$pageInfo->limit->value = $app->getUserStateFromRequest($paramBase.'.list_limit', 'limit', $app->getCfg('list_limit'), 'int');
$pageInfo->limit->start = $app->getUserStateFromRequest($paramBase.'.limitstart', 'limitstart', 0, 'int');

$database = JFactory::getDBO();

$filters = array();
if(!empty($pageInfo->search)){
	$searchVal = '\'%'.acymailing_getEscaped($pageInfo->search, true).'%\'';
	$filters[] = implode(" LIKE $searchVal OR ", $this->detailSearchFields)." LIKE $searchVal";
}

if(!empty($selectedMail)) $filters[] = 'a.mailid = '.$selectedMail;
if(!empty($selectedUrl)) $filters[] = 'a.urlid = '.$selectedUrl;

$query = 'SELECT '.implode(' , ', $this->detailSelectFields);
$query .= ' FROM '.acymailing_table('urlclick').' as a';
$query .= ' JOIN '.acymailing_table('mail').' as b on a.mailid = b.mailid';
$query .= ' JOIN '.acymailing_table('url').' as c on a.urlid = c.urlid';
$query .= ' JOIN '.acymailing_table('subscriber').' as d on a.subid = d.subid';
if(!empty($filters)) $query .= ' WHERE ('.implode(') AND (', $filters).')';
if(!empty($pageInfo->filter->order->value)){
	$query .= ' ORDER BY '.$pageInfo->filter->order->value.' '.$pageInfo->filter->order->dir;
}

$database->setQuery($query, $pageInfo->limit->start, $pageInfo->limit->value);
$rows = $database->loadObjectList();

$countQuery = 'SELECT COUNT(a.subid) FROM #__acymailing_urlclick as a';
if(!empty($pageInfo->search)){
	$countQuery .= ' JOIN '.acymailing_table('mail').' as b on a.mailid = b.mailid';
	$countQuery .= ' JOIN '.acymailing_table('url').' as c on a.urlid = c.urlid';
	$countQuery .= ' JOIN '.acymailing_table('subscriber').' as d on a.subid = d.subid';
}
if(!empty($filters)) $countQuery .= ' WHERE ('.implode(') AND (', $filters).')';

$database->setQuery($countQuery);
$pageInfo->elements->total = $database->loadResult();

$pageInfo->elements->page = count($rows);

jimport('joomla.html.pagination');
$pagination = new JPagination($pageInfo->elements->total, $pageInfo->limit->start, $pageInfo->limit->value);

$filtersType = new stdClass();
$mailType = acymailing_get('type.urlmail');
$urlType = acymailing_get('type.url');
$filtersType->mail = $mailType->display('filter_mail', $selectedMail);
$filtersType->url = $urlType->display('filter_url', $selectedUrl);

if($app->isAdmin()){
	$acyToolbar = acymailing::get('helper.toolbar');
	if(JRequest::getCmd('tmpl') == 'component'){
		$acyToolbar->topfixed = false;
	}
	$acyToolbar->custom('export', JText::_('ACY_EXPORT'), 'export', false, '');
	$acyToolbar->custom('', JText::_('ACY_CANCEL'), 'cancel', false, 'location.href=\''.acymailing_completeLink('diagram&task=mailing&mailid='.JRequest::getInt('filter_mail'), true).'\';');
	$acyToolbar->setTitle(JText::_('CLICK_STATISTICS'));
	$acyToolbar->display();
}
$this->assignRef('filters', $filtersType);
$this->assignRef('rows', $rows);
$this->assignRef('pageInfo', $pageInfo);
$this->assignRef('pagination', $pagination);
$this->assignRef('config', $config);
