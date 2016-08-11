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


class dataViewdata extends acymailingView{
	function display($tpl = null){
		$function = $this->getLayout();
		if(method_exists($this, $function)) $this->$function();

		parent::display($tpl);
	}

	function genericimport(){
		$this->chosen = false;

		$app = JFactory::getApplication();

		$isAdmin = false;
		if($app->isAdmin()){
			$isAdmin = true;

			$acyToolbar = acymailing::get('helper.toolbar');
			$acyToolbar->custom('finalizeimport', JText::_('IMPORT'), 'import', false, '');
			$acyToolbar->link(acymailing_completeLink('subscriber'), JText::_('ACY_CANCEL'), 'cancel');
			$acyToolbar->divider();
			$acyToolbar->help('data-import');
			$acyToolbar->setTitle(JText::_('IMPORT'), 'data&task=import');
			$acyToolbar->display();
		}

		$config = acymailing_config();
		$this->assignRef('config', $config);

		$selectedParams = array();
		$selectedParams = explode(',', $config->get('import_params', 'import_confirmed,generatename'));

		$this->assignRef('selectedParams', $selectedParams);

		$lists = JRequest::getVar('importlists', array());
		$listClass = acymailing_get('class.list');
		$allLists = $app->isAdmin() ? $listClass->getLists() : $listClass->getFrontendLists();

		$listsName = array();
		$unsubListsName = array();
		foreach($allLists as $oneList){
			if($lists[$oneList->listid] == -1) $unsubListsName[] = $oneList->name;
			if($lists[$oneList->listid] == 1) $listsName[] = $oneList->name;
			if($lists[$oneList->listid] == 2) $listsName[] = $oneList->name.' + '.JText::_('CAMPAIGN');
		}
		$createList = JRequest::getString('createlist');
		if(!empty($createList)) $listsName[] = $createList;
		if(!empty($listsName)) $this->assign('lists', implode(', ', $listsName));
		if(!empty($unsubListsName)) $this->assign('unsublists', implode(', ', $unsubListsName));

		$importFrom = JRequest::getCmd('importfrom');
		$this->assignRef('type', $importFrom);
		$this->assignRef('isAdmin', $isAdmin);
	}

	function import(){

		$listClass = acymailing_get('class.list');
		$app = JFactory::getApplication();
		$config = acymailing_config();

		$isAdmin = false;
		if($app->isAdmin()){
			$isAdmin = true;

			$acyToolbar = acymailing::get('helper.toolbar');
			$acyToolbar->custom('doimport', JText::_('IMPORT'), 'import', false, '');
			$acyToolbar->link(acymailing_completeLink('subscriber'), JText::_('ACY_CANCEL'), 'cancel');
			$acyToolbar->divider();
			$acyToolbar->help('data-import');
			$acyToolbar->setTitle(JText::_('IMPORT'), 'data&task=import');
			$acyToolbar->display();
		}


		$db = JFactory::getDBO();

		$importData = array();
		$importData['textarea'] = JText::_('IMPORT_TEXTAREA');
		$importData['file'] = JText::_('ACY_FILE');
		if(acymailing_isAllowed($config->get('acl_subscriber_zohoimport', 'all'))) $importData['zohocrm'] = 'ZohoCRM';


		$isAdmin = false;
		if($app->isAdmin()){
			$isAdmin = true;
			$importData['joomla'] = JText::_('IMPORT_JOOMLA');
			$importData['contact'] = 'com_contact';
			$importData['database'] = JText::_('DATABASE');
			$importData['ldap'] = 'LDAP';
			$importData['zohocrm'] = 'ZohoCRM';


			$possibleImport = array();
			$possibleImport[$db->getPrefix().'acajoom_subscribers'] = array('acajoom', 'Acajoom');
			$possibleImport[$db->getPrefix().'ccnewsletter_subscribers'] = array('ccnewsletter', 'ccNewsletter');
			$possibleImport[$db->getPrefix().'letterman_subscribers'] = array('letterman', 'Letterman');
			$possibleImport[$db->getPrefix().'communicator_subscribers'] = array('communicator', 'Communicator');
			$possibleImport[$db->getPrefix().'yanc_subscribers'] = array('yanc', 'Yanc');
			$possibleImport[$db->getPrefix().'vemod_news_mailer_users'] = array('vemod', 'Vemod News Mailer');
			$possibleImport[$db->getPrefix().'jnews_subscribers'] = array('jnews', 'jNews');
			$possibleImport['civicrm_email'] = array('civi', 'CiviCRM');
			$possibleImport[$db->getPrefix().'sobipro_field'] = array('sobipro', 'SobiPro');
			$possibleImport[$db->getPrefix().'nspro_subs'] = array('nspro', 'NS Pro');

			$tables = $db->getTableList();
			foreach($tables as $mytable){
				if(isset($possibleImport[$mytable])){
					$importData[$possibleImport[$mytable][0]] = $possibleImport[$mytable][1];
				}
			}

			$this->assignRef('tables', $tables);

			$civifile = ACYMAILING_ROOT.'administrator'.DS.'components'.DS.'com_civicrm'.DS.'civicrm.settings.php';
			if(empty($importData['civicrm_email']) && file_exists($civifile)){
				$importData['civi'] = 'CiviCRM';
			}
		}


		$importvalues = array();
		foreach($importData as $div => $name){
			$importvalues[] = JHTML::_('select.option', $div, $name);
		}
		$js = 'var currentoption = \'textarea\';
		function updateImport(newoption){document.getElementById(currentoption).style.display = "none";document.getElementById(newoption).style.display = \'block\';currentoption = newoption;}';
		$doc = JFactory::getDocument();

		$function = JRequest::getCmd('importfrom');
		if(!empty($function)){
			$js .= 'window.addEvent(\'load\', function(){ updateImport(\''.$function.'\'); });';
		}
		if($config->get('ldap_host') && $app->isAdmin()){
			$js .= 'window.addEvent(\'load\', function(){ updateldap(); });';
		}
		$doc->addScriptDeclaration($js);

		$this->assignRef('importvalues', $importvalues);
		$this->assignRef('importdata', $importData);

		$lists = $app->isAdmin() ? $listClass->getLists() : $listClass->getFrontendLists();

		$subscribeOptions = array();
		$subscribeOptions[] = JHTML::_('select.option', 0, JTEXT::_('JOOMEXT_NO'));
		$subscribeOptions[] = JHTML::_('select.option', -1, JTEXT::_('UNSUBSCRIBE'));
		$subscribeOptions[] = JHTML::_('select.option', 1, JTEXT::_('SUBSCRIBE'));
		$campaignValues = $subscribeOptions;
		$campaignValues[] = JHTML::_('select.option', 2, JTEXT::_('JOOMEXT_YES_CAMPAIGN'));
		if(acymailing_level(3)){
			$listsOfId = array();
			foreach($lists as $oneList){
				$listsOfId[] = $oneList->listid;
			}
			$listCampaign = $listClass->getCampaigns($listsOfId);
			foreach($lists as $key => $oneList){
				if(!empty($listCampaign[$oneList->listid])){
					$lists[$key]->campaign = implode(',', $listCampaign[$oneList->listid]);
				}
			}
		}

		$this->assignRef('lists', $lists);
		$this->assignRef('subscribeOptions', $subscribeOptions);
		$this->assignRef('campaignValues', $campaignValues);
		$this->assignRef('config', $config);
		$this->assignRef('isAdmin', $isAdmin);
	}

	function export(){
		$listClass = acymailing_get('class.list');
		$db = JFactory::getDBO();
		$fields = acymailing_getColumns('#__acymailing_subscriber');
		$fieldsList = array();
		$fieldsList['listid'] = 'smallint unsigned';
		$fieldsList['listname'] = 'varchar';

		$config = acymailing_config();
		$selectedFields = explode(',', $config->get('export_fields', 'email,name'));
		$selectedLists = explode(',', $config->get('export_lists'));
		$selectedFilters = explode(',', $config->get('export_filters', 'subscribed'));

		$app = JFactory::getApplication();
		$isAdmin = false;
		if($app->isAdmin()){
			$isAdmin = true;

			$acyToolbar = acymailing::get('helper.toolbar');
			if(JRequest::getString('tmpl') == 'component'){
				$acyToolbar->custom('doexport', JText::_('ACY_EXPORT'), 'export', false, '');
				$acyToolbar->setTitle(JText::_('ACY_EXPORT'));
				$acyToolbar->topfixed = false;
			}else{
				$acyToolbar->custom('doexport', JText::_('ACY_EXPORT'), 'export', false, '');
				$acyToolbar->link(acymailing_completeLink('subscriber'), JText::_('ACY_CANCEL'), 'cancel');
				$acyToolbar->divider();
				$acyToolbar->help('data-export');
				$acyToolbar->setTitle(JText::_('ACY_EXPORT'), 'data&task=export');
			}
			$acyToolbar->display();
		}

		$charsetType = acymailing_get('type.charset');
		$this->assignRef('charset', $charsetType);

		if($app->isAdmin()){
			$lists = $listClass->getLists();
		}else $lists = $listClass->getFrontendLists();

		$this->assignRef('lists', $lists);
		$this->assignRef('fields', $fields);
		$this->assignRef('fieldsList', $fieldsList);
		$this->assignRef('selectedfields', $selectedFields);
		$this->assignRef('selectedlists', $selectedLists);
		$this->assignRef('selectedFilters', $selectedFilters);
		$this->assignRef('config', $config);
		$this->assignRef('isAdmin', $isAdmin);

		if(JRequest::getInt('sessionvalues')){
			if(!empty($_SESSION['acymailing']['exportusers'])){
				$i = 1;
				$subids = array();
				foreach($_SESSION['acymailing']['exportusers'] as $subid){
					$subids[] = (int)$subid;
					$i++;
					if($i > 10) break;
				}

				if(!empty($subids)){
					$db->setQuery('SELECT DISTINCT `name`,`email` FROM `#__acymailing_subscriber` WHERE `subid` IN ('.implode(',', $subids).') LIMIT 10');
					$users = $db->loadObjectList();
					$this->assignRef('users', $users);
				}
			}elseif(!empty($_SESSION['acymailing']['exportlist'])){
				$filterList = $_SESSION['acymailing']['exportlist'];
				$this->assignRef('exportlist', $filterList);
				$filterListStatus = $_SESSION['acymailing']['exportliststatus'];
				$this->assignRef('exportliststatus', $filterListStatus);
			}
		}

		if(JRequest::getInt('fieldfilters')) $this->assign('fieldfilters', true);

		if(JRequest::getInt('sessionquery')){
			$currentSession = JFactory::getSession();
			$exportQuery = $currentSession->get('acyexportquery');
			if(!empty($exportQuery)){
				$db->setQuery('SELECT DISTINCT s.`name`,s.`email` '.$exportQuery.' LIMIT 10');
				$users = $db->loadObjectList();
				$this->assignRef('users', $users);

				if(strpos($exportQuery, 'userstats')){
					$otherFields = array('userstats.senddate', 'userstats.open', 'userstats.opendate', 'userstats.bounce', 'userstats.bouncerule', 'userstats.ip', 'userstats.html', 'userstats.fail', 'userstats.sent', 'userstats.browser', 'userstats.browser_version', 'userstats.is_mobile', 'userstats.mobile_os', 'userstats.user_agent');
					$this->assignRef('otherfields', $otherFields);
				}
				if(strpos($exportQuery, 'urlclick')){
					$otherFields = array('url.name', 'url.url', 'urlclick.date', 'urlclick.ip', 'urlclick.click');
					$this->assignRef('otherfields', $otherFields);
				}
				if(strpos($exportQuery, 'history')){
					$otherFields = array('hist.data', 'hist.date');
					$this->assignRef('otherfields', $otherFields);
				}
			}
		}

		if(acymailing_level(3)){
			$geolocFields = acymailing_getColumns('#__acymailing_geolocation');
			$this->assign('geolocfields', $geolocFields);
		}
	}
}
