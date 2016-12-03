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

class listsViewlists  extends acymailingView
{
	function display($tpl = null){

		global $Itemid;
		$db			= JFactory::getDBO();
		$app = JFactory::getApplication();
		$doc	= JFactory::getDocument();
		$feedEmail = (@$app->getCfg('feed_email')) ? $app->getCfg('feed_email') : 'author';
		$siteEmail = $app->getCfg('mailfrom');
		$jsite = JFactory::getApplication('site');
		$menus = $jsite->getMenu();
		$menu	= $menus->getActive();
		$listed = array();

		if(empty($menu) AND !empty($Itemid)){
			$menus->setActive($Itemid);
			$menu	= $menus->getItem($Itemid);
		}

		$myItem = empty($Itemid) ? '' : '&Itemid='.$Itemid;
		$selectedLists = 'all';
		if (is_object( $menu )) {
			jimport('joomla.html.parameter');
			$menuparams = new acyParameter( $menu->params );
			$selectedLists = $menuparams->get('lists','all');
		}
		$listsClass = acymailing_get('class.list');
		$allLists = $listsClass->getLists('listid',$selectedLists);
		foreach($allLists as $oneList){
			if($oneList->published && $oneList->visible && acymailing_isAllowed($oneList->access_sub)){
				$listed[] = $oneList->listid;
			}
		}

		$config = acymailing_config();
		$filters = array();
		$filters[] = 'a.type = \'news\'';
		$filters[] = 'a.published = 1';
		$filters[] = 'a.visible = 1';
		$filters[] = 'c.listid IN ('.implode(',',$listed).')';
		$query = 'SELECT a.*,c.listid';
		$query .= ' FROM '.acymailing_table('listmail').' as c';
		$query .= ' LEFT JOIN '.acymailing_table('mail').' as a on a.mailid = c.mailid ';
		$query .= ' WHERE ('.implode(') AND (',$filters).')';
		$query .= ' GROUP BY a.mailid ORDER BY a.'.$config->get('acyrss_order','senddate').' '.($config->get('acyrss_order','senddate') == 'subject' ? 'ASC' : 'DESC');
		$query .= ' LIMIT '.$config->get('acyrss_element','20');
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$doc->title = $config->get('acyrss_name','');
		$doc->description = $config->get('acyrss_description','');

		$receiver = new stdClass();
		$receiver->name = JText::_('VISITOR');
		$receiver->subid = 0;
		$mailClass = acymailing_get('helper.mailer');
		$mailClass->loadedToSend = false;

		foreach ( $rows as $row )
		{
			$oneMail = $mailClass->load($row->mailid);
			$oneMail->sendHTML = true;
			$mailClass->dispatcher->trigger('acymailing_replaceusertags',array(&$oneMail,&$receiver,false));
			$title = $this->escape( $oneMail->subject );
			$title = html_entity_decode( $title );
			$oneList = $allLists[$row->listid];
			$link = JRoute::_('index.php?option=com_acymailing&amp;ctrl=archive&amp;task=view&amp;listid='.$oneList->listid.'-'.$oneList->alias.'&amp;mailid='.$row->mailid.'-'.$row->alias);

			$description	= $oneMail->body;
			$author			= $oneMail->userid;
			$item = new JFeedItem();
			$item->title 		= $title;
			$item->link 		= $link;
			$item->description 	= $description;
			$item->date			= acymailing_getDate($oneMail->senddate,'%Y-%m-%d %H:%M:%S');
			$item->category   	= JText::_('NEWSLETTER');

			$doc->addItem( $item );
		}
	}
}
