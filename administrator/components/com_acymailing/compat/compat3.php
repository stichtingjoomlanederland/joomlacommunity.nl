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

class acymailingView extends JViewLegacy{

	var $chosen = true;

	function display($tpl = null){
		$app = JFactory::getApplication();
		if($this->chosen && $app->isAdmin()){
			JHtml::_('formbehavior.chosen', 'select');
		}

		return parent::display($tpl);
	}

}

class acymailingControllerCompat extends JControllerLegacy{

}

function acymailing_loadResultArray(&$db){
	return $db->loadColumn();
}

function acymailing_loadMootools($loadMootoolsMoreLib = false){
	JHTML::_('behavior.framework', $loadMootoolsMoreLib);
}

function acymailing_getColumns($table){
	$db = JFactory::getDBO();
	return $db->getTableColumns($table);
}

function acymailing_getEscaped($value, $extra = false) {
	$db = JFactory::getDBO();
	return $db->escape($value, $extra);
}

function acymailing_getFormToken() {
	return JSession::getFormToken();
}

class acyParameter extends JRegistry {

	function get($path, $default = null){
		$value = parent::get($path, 'noval');
		if($value === 'noval') $value = parent::get('data.'.$path,$default);
		return $value;
	}
}
