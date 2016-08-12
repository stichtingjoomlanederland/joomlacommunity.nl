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

class cpanelClass extends acymailingClass{

	function load(){
		$query = 'SELECT * FROM '.acymailing_table('config');
		$this->database->setQuery($query);
		$this->values = $this->database->loadObjectList('namekey');
	}

	function get($namekey,$default = ''){
		if(isset($this->values[$namekey])) return $this->values[$namekey]->value;
		return $default;
	}

	function save($configObject){
		$query = 'REPLACE INTO '.acymailing_table('config').' (namekey,value) VALUES ';
		$params = array();
		$i = 0;
		foreach($configObject as $namekey => $value){
			if(strpos($namekey,'password') !== false && !empty($value) && trim($value,'*') == '') continue;
			$i++;
			if(is_array($value)) $value = implode(',', $value);
			if($i>100){
				$query .= implode(',',$params);
				$this->database->setQuery($query);
				if(!$this->database->query()) return false;
				$i = 0;
				$query = 'REPLACE INTO '.acymailing_table('config').' (namekey,value) VALUES ';
				$params = array();
			}
			if (empty($this->values[$namekey])) $this->values[$namekey] = new stdClass();
			$this->values[$namekey]->value = $value;
			$params[] = '('.$this->database->Quote(strip_tags($namekey)).','.$this->database->Quote(strip_tags($value)).')';
		}
		if(empty($params)) return true;
		$query .= implode(',',$params);
		$this->database->setQuery($query);

		try{
			$status = $this->database->query();
		}catch(Exception $e){
			$status = false;
		}
		if(!$status) acymailing_display(isset($e) ? $e->getMessage() : substr(strip_tags($this->database->getErrorMsg()),0,200).'...','error');

		return $status;
	}

}
