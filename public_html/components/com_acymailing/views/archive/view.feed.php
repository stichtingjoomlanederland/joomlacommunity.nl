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

jimport( 'joomla.application.component.view');
class archiveViewArchive extends acymailingView
{
	function display($tpl = null){

		global $Itemid;
		$db			= JFactory::getDBO();
		$app = JFactory::getApplication();
		$doc	= JFactory::getDocument();
		$jsite = JFactory::getApplication('site');
		$menus = $jsite->getMenu();
		$menu	= $menus->getActive();
		if(empty($menu) AND !empty($Itemid)){
			$menus->setActive($Itemid);
			$menu	= $menus->getItem($Itemid);
		}
		$myItem = empty($Itemid) ? '' : '&Itemid='.$Itemid;
		if (is_object( $menu )) {
			jimport('joomla.html.parameter');
			$menuparams = new acyParameter( $menu->params );
		}
 		$listid = acymailing_getCID('listid');
			if(empty($listid) AND !empty($menuparams)){
				$listid = $menuparams->get('listid');
			}
		$doc->link = acymailing_completeLink('archive&listid='.intval($listid));
		 $listClass = acymailing_get('class.list');
 		if(empty($listid)){
				return JError::raiseError( 404, 'Mailing List not found' );
			}
			$oneList = $listClass->get($listid);
			if(empty($oneList->listid)){
				return JError::raiseError( 404, 'Mailing List not found : '.$listid );
			}
			if(!acymailing_isAllowed($oneList->access_sub) || !$oneList->published || !$oneList->visible){
				return JError::raiseError( 404, JText::_('ACY_NOTALLOWED') );
			}

		$config = acymailing_config();
		$filters = array();
		$filters[] = 'a.type = \'news\'';
		$filters[] = 'a.published = 1';
		$filters[] = 'a.visible = 1';
		$filters[] = 'c.listid = '.$oneList->listid;
		$query = 'SELECT a.*';
		$query .= ' FROM '.acymailing_table('listmail').' as c';
		$query .= ' LEFT JOIN '.acymailing_table('mail').' as a on a.mailid = c.mailid ';
		$query .= ' WHERE ('.implode(') AND (',$filters).')';
		$query .= ' ORDER BY a.'.$config->get('acyrss_order','senddate').' '.($config->get('acyrss_order','senddate') == 'subject' ? 'ASC' : 'DESC');
		$query .= ' LIMIT '.$config->get('acyrss_element','20');
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$doc->title = $config->get('acyrss_name','');
		$doc->description = $config->get('acyrss_description','');

		$receiver = new stdClass();
		$receiver->name = JText::_('VISITOR');
		$receiver->subid = 0;

		$mailClass = acymailing_get('helper.mailer');

		foreach ( $rows as $row )
		{
			$mailClass->loadedToSend = false;
			$oneMail = $mailClass->load($row->mailid);
			$oneMail->sendHTML = true;
			$mailClass->dispatcher->trigger('acymailing_replaceusertags',array(&$oneMail,&$receiver,false));
			$title = $this->escape( $oneMail->subject );
			$title = html_entity_decode( $title );
			$link = JRoute::_('index.php?option=com_acymailing&amp;ctrl=archive&amp;task=view&amp;listid='.$oneList->listid.'-'.$oneList->alias.'&amp;mailid='.$row->mailid.'-'.$row->alias);

			$author			= $oneMail->userid;
			$item = new JFeedItem();
			$item->title 		= $title;
			$item->link 		= $link;
			$item->description 	= $oneMail->body;
			$item->date			= $oneMail->created;
			$item->category   	= $oneMail->type;
			$item->author		= $author;

			$doc->addItem( $item );
		}
	}
}
