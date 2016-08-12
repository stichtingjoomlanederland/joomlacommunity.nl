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


class listsViewLists extends acymailingView{
	function display($tpl = null){
		$function = $this->getLayout();
		if(method_exists($this, $function)) $this->$function();

		parent::display($tpl);
	}

	function listing(){
		$app = JFactory::getApplication();

		global $Itemid;
		$config = acymailing_config();

		$jsite = JFactory::getApplication('site');
		$menus = $jsite->getMenu();
		$menu = $menus->getActive();

		if(empty($menu) AND !empty($Itemid)){
			$menus->setActive($Itemid);
			$menu = $menus->getItem($Itemid);
		}

		if(empty($menu)) {
			acymailing_enqueueMessage(JText::_('ACY_NOTALLOWED'));
			$app->redirect('index.php');
		}

		$selectedLists = 'all';

		if(is_object($menu)){
			jimport('joomla.html.parameter');
			$menuparams = new acyParameter($menu->params);


			$this->assign('listsintrotext', $menuparams->get('listsintrotext'));
			$this->assign('listsfinaltext', $menuparams->get('listsfinaltext'));
			$selectedLists = $menuparams->get('lists', 'all');

			$document = JFactory::getDocument();
			if($menuparams->get('menu-meta_description')) $document->setDescription($menuparams->get('menu-meta_description'));
			if($menuparams->get('menu-meta_keywords')) $document->setMetadata('keywords', $menuparams->get('menu-meta_keywords'));
			if($menuparams->get('robots')) $document->setMetadata('robots', $menuparams->get('robots'));
			if($menuparams->get('page_title')) acymailing_setPageTitle($menuparams->get('page_title'));
		}

		if(empty($menuparams)){
			$pathway = $app->getPathway();
			$pathway->addItem(JText::_('MAILING_LISTS'));
		}

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

		$listsClass = acymailing_get('class.list');
		$allLists = $listsClass->getLists('', $selectedLists);

		if(acymailing_level(1)){
			$allLists = $listsClass->onlyCurrentLanguage($allLists);
		}

		$myItem = empty($Itemid) ? '' : '&Itemid='.$Itemid;
		$this->assignRef('rows', $allLists);
		$this->assignRef('item', $myItem);
	}
}
