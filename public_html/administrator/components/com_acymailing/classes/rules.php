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

class rulesClass extends acymailingClass{

	var $tables = array('rules');
	var $pkey = 'ruleid';
	var $errors = array();

	function getRules($all = true){
		$this->database->setQuery('SELECT * FROM `#__acymailing_rules` '.($all ? '' : 'WHERE published = 1').' ORDER BY `ordering` ASC');
		$rules = $this->database->loadObjectList();

		foreach($rules as $id => $rule){
			$rules[$id] = $this->_prepareRule($rule);
		}
		return $rules;
	}

	function get($ruleid, $default = null){
		$query = 'SELECT * FROM '.acymailing_table('rules').' WHERE `ruleid` = '.intval($ruleid).' LIMIT 1';
		$this->database->setQuery($query);

		$rule = $this->database->loadObject();
		return $this->_prepareRule($rule);
	}

	function _prepareRule($rule){
		$vals = array('executed_on','action_message','action_user');
		foreach($vals as $oneVal){
			if(!empty($rule->$oneVal)) $rule->$oneVal = unserialize($rule->$oneVal);
		}

		return $rule;
	}

	function saveForm(){

		$rule = new stdClass();
		$rule->ruleid = acymailing_getCID('ruleid');
		if(empty( $rule->ruleid)){
			$this->database->setQuery('SELECT max(ordering) FROM `#__acymailing_rules`');
		$rule->ordering = intval($this->database->loadResult()) + 1;
		}
		$rule->executed_on = '';
		$rule->action_message = '';
		$rule->action_user = '';

		$formData = JRequest::getVar( 'data', array(), '', 'array' );

		foreach($formData['rule'] as $column => $value){
			acymailing_secureField($column);
		if(is_array($value)){
			$rule->$column = serialize($value);
		}else{
			$rule->$column = strip_tags($value);
		}
		}


		$ruleid = $this->save($rule);
		if(!$ruleid) return false;

		JRequest::setVar( 'ruleid', $ruleid);
		return true;

	}
}
