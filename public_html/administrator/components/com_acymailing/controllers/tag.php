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

class TagController extends acymailingController
{
	var $aclCat = 'tags';

	function __construct($config = array()){
		parent::__construct($config);
		JHTML::_('behavior.tooltip');
		JRequest::setVar('tmpl','component');

		$this->registerDefaultTask('tag');
	}

	function tag(){
		if(!$this->isAllowed($this->aclCat,'view')) return;
		JRequest::setVar( 'layout', 'tag'  );
		return parent::display();
	}

	function plgtrigger(){
		if(!require_once(ACYMAILING_BACK.DS.'controllers'.DS.'cpanel.php')) return;
		$cPanelController = acymailing_get('controller.cpanel');
		$cPanelController->plgtrigger();
		return;
	}

	function customtemplate(){
		JRequest::setVar('layout', 'form');
		return parent::display();
	}

	function store(){
		JRequest::checkToken() or die('Invalid Token');

		$plugin = JRequest::getString('plugin');
		$plugin = preg_replace('#[^a-zA-Z0-9]#Uis', '', $plugin);
		$body = JRequest::getVar('templatebody','','','string',JREQUEST_ALLOWRAW);

		if(empty($body)){ acymailing_display(JText::_('FILL_ALL'),'error'); return; }

		$pluginsFolder = ACYMAILING_MEDIA.'plugins';
		if(!file_exists($pluginsFolder)) acymailing_createDir($pluginsFolder);

		try{
			jimport('joomla.filesystem.file');
			$status = JFile::write($pluginsFolder.DS.$plugin.'.php',$body);
		}catch(Exception $e){
			$status = false;
		}

		if($status) acymailing_display(JText::_('JOOMEXT_SUCC_SAVED'),'success');
		else acymailing_display(JText::sprintf('FAIL_SAVE', $pluginsFolder.DS.$plugin.'.php'),'error');
	}
}
