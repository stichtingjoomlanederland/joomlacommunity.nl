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


class archiveViewArchive extends acymailingView
{
	function display($tpl = null)
	{
		$function = $this->getLayout();
		if(method_exists($this,$function)) $this->$function();

	}

	function view(){

			$mailid = acymailing_getCID('mailid');

		if(empty($mailid)){
			$db=&JFactory::getDBO();
			$query = 'SELECT m.`mailid` FROM `#__acymailing_list` as l LEFT JOIN `#__acymailing_listmail` as lm ON l.listid=lm.listid LEFT JOIN `#__acymailing_mail` as m on lm.mailid = m.mailid';
			$query .= ' WHERE l.`visible` = 1 AND l.`published` = 1 AND m.`visible`= 1 AND m.`published` = 1';
			if(!empty($listid)) $query .= ' AND l.`listid` = '.(int) $listid;
			$query .= ' ORDER BY m.`mailid` DESC LIMIT 1';
			$db->setQuery($query);
			$mailid = $db->loadResult();

			if(empty($mailid)) return JError::raiseError( 404, 'Newsletter not found');
			}

		$access_sub = true;
			 if(acymailing_level(3)){
			$listmail = acymailing_get('class.listmail');
			$allLists = $listmail->getLists($mailid);
			$access_sub = false;
			if(!empty($allLists)){
				$my = JFactory::getUser();
				foreach($allLists as $alist){
					if(empty($alist->mailid)) continue;
					if(!$alist->published OR !$alist->visible OR $alist->access_sub == 'none') continue;
					if($alist->access_sub == 'all'){
						$access_sub = true;
						break;
					}
					if(empty($my->id)) continue;
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
				return JError::raiseError( 404, 'Newsletter not found : '.$mailid );
			}

			if(!$access_sub OR !$oneMail->published OR !$oneMail->visible){
				$key = JRequest::getString('key');
				if(empty($key) OR $key !== $oneMail->key){
					$app = JFactory::getApplication();
					$app->enqueueMessage('You can not have access to this e-mail','error');
					$app->redirect(acymailing_completeLink('lists',false,true));
					return false;
				}
			}

		$user = JFactory::getUser();
		if(!empty($user->email)){
			$userClass = acymailing_get('class.subscriber');
			$receiver = $userClass->get($user->email);
		}else{
			$receiver = new stdClass();
			$receiver->name = JText::_('VISITOR');
		}

		$oneMail->sendHTML = true;
		$mailClass->dispatcher->trigger('acymailing_replaceusertags',array(&$oneMail,&$receiver,false));

		acymailing_setPageTitle($oneMail->subject );

		if(!empty($oneMail->text)) echo nl2br($mailClass->textVersion($oneMail->text,false));
			else echo nl2br($mailClass->textVersion($oneMail->body,true));

	}
}
