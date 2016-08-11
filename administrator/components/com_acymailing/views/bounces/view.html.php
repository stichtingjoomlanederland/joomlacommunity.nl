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

class BouncesViewBounces extends acymailingView{

	function display($tpl = null){
		$function = $this->getLayout();
		if(method_exists($this, $function)) $this->$function();

		parent::display($tpl);
	}

	function form(){
		$ruleid = acymailing_getCID('ruleid');
		$rulesClass = acymailing_get('class.rules');
		if(!empty($ruleid)){
			$rule = $rulesClass->get($ruleid);
		}else{
			$rule = new stdClass();
			$rule->published = 1;
		}

		$acyToolbar = acymailing::get('helper.toolbar');
		$acyToolbar->addButtonOption('apply', JText::_('ACY_APPLY'), 'apply', false);
		$acyToolbar->save();
		$acyToolbar->cancel();
		$acyToolbar->divider();
		$acyToolbar->help('bounce');
		$acyToolbar->setTitle(JText::_('ACY_RULE'), 'bounces&task=edit&ruleid='.$ruleid);
		$acyToolbar->display();

		$lists = acymailing_get('type.lists');
		$lists->getValues();
		array_shift($lists->values);
		$this->assignRef('lists', $lists);
		$this->assignRef('rule', $rule);
	}

	function listing(){

		JHTML::_('behavior.modal', 'a.modal');


		$rulesClass = acymailing_get('class.rules');
		$rows = $rulesClass->getRules();
		$config = acymailing_config();
		$doc = JFactory::getDocument();
		$listClass = acymailing_get('class.list');
		$elements = new stdClass();
		$elements->bounce = JHTML::_('acyselect.booleanlist', "config[bounce]", '', $config->get('bounce', 0));

		$connections = array('imap' => 'IMAP', 'pop3' => 'POP3', 'pear' => 'POP3 (without imap extension)', 'nntp' => 'NNTP');

		$connecvals = array();
		foreach($connections as $code => $string){
			$connecvals[] = JHTML::_('select.option', $code, $string);
		}

		$elements->bounce_connection = JHTML::_('select.genericlist', $connecvals, 'config[bounce_connection]', 'size="1"', 'value', 'text', $config->get('bounce_connection', 'imap'));

		$securedVals = array();
		$securedVals[] = JHTML::_('select.option', '', '- - -');
		$securedVals[] = JHTML::_('select.option', 'ssl', 'SSL');
		$securedVals[] = JHTML::_('select.option', 'tls', 'TLS');

		$elements->bounce_secured = JHTML::_('select.genericlist', $securedVals, "config[bounce_secured]", 'size="1"', 'value', 'text', $config->get('bounce_secured'));
		$elements->bounce_certif = JHTML::_('acyselect.booleanlist', "config[bounce_certif]", '', $config->get('bounce_certif', 0));

		$js = "function displayBounceFrequency(newvalue){ if(newvalue == '1') {window.document.getElementById('bouncefrequency').style.display = 'block';}else{window.document.getElementById('bouncefrequency').style.display = 'none';}} ";
		$js .= 'window.addEvent(\'load\', function(){ displayBounceFrequency(\''.$config->get('auto_bounce', 0).'\');});';
		$doc->addScriptDeclaration($js);

		$acyToolbar = acymailing::get('helper.toolbar');
		$acyToolbar->custom('test', JText::_('BOUNCE_PROCESS'), 'bounce', false);
		$onClickBounce = "if (confirm('".JText::_('CONFIRM_REINSTALL_RULES')." ".JText::_('PROCESS_CONFIRMATION')."')){Joomla.submitbutton('reinstall');}";
		$acyToolbar->custom('installbounces', JText::_('REINSTALL_RULES'), 'generate', false, $onClickBounce);
		$acyToolbar->divider();
		$acyToolbar->custom('saveconfig', JText::_('ACY_SAVE'), 'save', false);
		$acyToolbar->cancel();
		$acyToolbar->divider();
		$acyToolbar->help('bounce');
		$acyToolbar->setTitle(JText::_('BOUNCE_HANDLING'), 'bounces');
		$acyToolbar->display();

		$updateClass = acymailing_get('helper.update');
		if($config->get('bouncerulesversion', 0) < $updateClass->bouncerulesversion){
			acymailing_display('<a href="index.php?option=com_acymailing&ctrl=bounces&task=reinstall" title="'.JText::_('REINSTALL_RULES').'" >'.JText::_('WANNA_REINSTALL_RULES').'</a>', 'warning');
		}

		jimport('joomla.html.pagination');
		$total = count($rows);
		$pagination = new JPagination($total, 0, $total);

		$lists = $listClass->getLists('listid');
		$this->assignRef('rows', $rows);
		$this->assignRef('lists', $lists);
		$this->assignRef('elements', $elements);
		$this->assignRef('config', $config);
		$toggleClass = acymailing_get('helper.toggle');
		$this->assignRef('toggleClass', $toggleClass);
		$this->assignRef('pagination', $pagination);
	}

	function chart(){
		$mailid = JRequest::getInt('mailid');
		if(empty($mailid)) return;

		$doc = JFactory::getDocument();
		$doc->addStyleSheet(ACYMAILING_CSS.'acyprint.css', 'text/css', 'print');

		$db = JFactory::getDBO();
		$db->setQuery('SELECT bouncedetails FROM #__acymailing_stats WHERE mailid = '.intval($mailid));
		$data = $db->loadObject();

		if(empty($data->bouncedetails)){
			acymailing_display("No data recorded for that Newsletter", 'warning');
			return;
		}

		$acyToolbar = acymailing::get('helper.toolbar');
		$acyToolbar->topfixed = false;
		$acyToolbar->link(acymailing_completeLink('bounces&task=chart&export=1&mailid='.$mailid, true), JText::_('ACY_EXPORT'), 'export');
		$acyToolbar->directPrint();
		$acyToolbar->setTitle(JText::_('BOUNCE_HANDLING'));
		$acyToolbar->display();

		$data->bouncedetails = unserialize($data->bouncedetails);

		arsort($data->bouncedetails);

		$doc = JFactory::getDocument();
		$doc->addScript("https://www.google.com/jsapi");

		$this->assignRef('data', $data);

		if(JRequest::getCmd('export')){
			$exportHelper = acymailing_get('helper.export');
			$exportHelper->exportOneData($data->bouncedetails, 'bounce_'.JRequest::getInt('mailid'));
		}
	}
}
