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
include(ACYMAILING_BACK.'views'.DS.'newsletter'.DS.'view.html.php');

class NotificationViewNotification extends NewsletterViewNewsletter{
	var $type = 'joomlanotification';
	var $ctrl = 'notification';
	var $nameListing = 'JOOMLA_NOTIFICATIONS';
	var $nameForm = 'JOOMLA_NOTIFICATIONS';
	var $doc = 'joomlanotification';
	var $icon = 'joomlanotification';
	var $filters = array();


	function listing(){
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$config = acymailing_config();

		if(!class_exists('plgSystemAcymailingClassMail')){
			$warning_msg = JText::_('ACY_WARNINGOVERRIDE_DISABLED_1').' <a href="index.php?option=com_acymailing&ctrl=cpanel">'.JText::sprintf('ACY_WARNINGOVERRIDE_DISABLED_2', ' acymailingclassmail (Override Joomla mailing system plugin)').'</a>';
			acymailing_enqueueMessage($warning_msg, 'notice');
		}

		$pageInfo = new stdClass();
		$pageInfo->filter = new stdClass();
		$pageInfo->filter->order = new stdClass();
		$pageInfo->elements = new stdClass();
		$pageInfo->limit = new stdClass();
		$this->filters[] = '`type` = '.$db->Quote($this->type);

		$paramBase = ACYMAILING_COMPONENT.'.'.$this->getName();
		$pageInfo->filter->order->value = $app->getUserStateFromRequest($paramBase.".filter_order", 'filter_order', 'mailid', 'cmd');
		$pageInfo->filter->order->dir = $app->getUserStateFromRequest($paramBase.".filter_order_Dir", 'filter_order_Dir', 'asc', 'word');
		if(strtolower($pageInfo->filter->order->dir) !== 'asc') $pageInfo->filter->order->dir = 'desc';

		$pageInfo->search = $app->getUserStateFromRequest($paramBase.".search", 'search', '', 'string');
		$pageInfo->search = JString::strtolower(trim($pageInfo->search));
		$pageInfo->limit->value = $app->getUserStateFromRequest($paramBase.'.list_limit', 'limit', $app->getCfg('list_limit'), 'int');
		$pageInfo->limit->start = $app->getUserStateFromRequest($paramBase.'.limitstart', 'limitstart', 0, 'int');

		if(!empty($pageInfo->search)){
			$searchVal = '\'%'.acymailing_getEscaped($pageInfo->search, true).'%\'';
			$this->filters[] = "subject LIKE $searchVal OR body LIKE $searchVal";
		}

		$filters = new stdClass();
		if(ACYMAILING_J16){
			$pageInfo->category = $app->getUserStateFromRequest($paramBase.".category", 'category', '0', 'string');
			if(!empty($pageInfo->category)){
				$this->filters[] = "alias LIKE '".acymailing_getEscaped($pageInfo->category, true)."-%'";
			}
			$catvalues = array();
			$catvalues[] = JHTML::_('select.option', '0', JText::_('ACY_ALL'));
			$catvalues[] = JHTML::_('select.option', 'joomla', 'Joomla!');
			$catvalues[] = JHTML::_('select.option', 'jomsocial', 'JomSocial');
			$catvalues[] = JHTML::_('select.option', 'seblod', 'SEBLOD');
			$filters->category = JHTML::_('select.genericlist', $catvalues, 'category', 'size="1" style="width:150px" onchange="javascript:submitform();"', 'value', 'text', $pageInfo->category);
		}

		$query = 'SELECT mailid, subject, alias, fromname, published, fromname, fromemail, replyname, replyemail FROM #__acymailing_mail WHERE ('.implode(') AND (', $this->filters).')';

		if(!empty($pageInfo->filter->order->value)){
			$query .= ' ORDER BY '.$pageInfo->filter->order->value.' '.$pageInfo->filter->order->dir;
		}

		$db->setQuery($query, $pageInfo->limit->start, $pageInfo->limit->value);
		$rows = $db->loadObjectList();

		jimport('joomla.html.pagination');
		$queryCount = 'SELECT count(mailid) FROM #__acymailing_mail WHERE ('.implode(') AND (', $this->filters).')';
		$db->setQuery($queryCount);
		$pageInfo->elements->total = $db->loadResult();
		$pageInfo->elements->page = count($rows);
		$pagination = new JPagination($pageInfo->elements->total, $pageInfo->limit->start, $pageInfo->limit->value);

		$acyToolbar = acymailing::get('helper.toolbar');
		$acyToolbar->custom('preview', JText::_('ACY_PREVIEW'), 'search', true);
		$acyToolbar->edit();
		$acyToolbar->delete();

		$acyToolbar->divider();
		$acyToolbar->help($this->doc);
		$acyToolbar->setTitle(JText::_($this->nameListing), $this->ctrl);
		$acyToolbar->display();

		$toggleClass = acymailing_get('helper.toggle');
		$this->assignRef('toggleClass', $toggleClass);
		$this->assignRef('pageInfo', $pageInfo);
		$this->assign('config', $config);
		$this->assign('rows', $rows);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('filters', $filters);
	}

	function form(){
		JHTML::_('behavior.modal', 'a.modal');
		return parent::form();
	}

	function preview(){
		JHTML::_('behavior.modal', 'a.modal');
		return parent::preview();
	}

}
