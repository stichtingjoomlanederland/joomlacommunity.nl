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

class actionClass extends acymailingClass{

	var $tables = array('action');
	var $pkey = 'action_id';

	function getActions($index = '', $actionIds = 'all'){
		$onlyActionIds = array();
		if(strtolower($actionIds) != 'all'){
			$onlyActionIds = explode(',', $actionIds);
			JArrayHelper::toInteger($onlyActionIds);
		}

		$this->database->setQuery('SELECT * FROM '.acymailing_table('action').(empty($onlyActionIds) ? '' : ' WHERE listid IN ('.implode(',', $onlyActionIds).')').' ORDER BY ordering ASC');
		return $this->database->loadObjectList($index);
	}

	function delete($elements){
		if(!is_array($elements)) $elements = array($elements);
		JArrayHelper::toInteger($elements);
		if(empty($elements)) return 0;

		return parent::delete($elements);
	}

	function get($actionid, $default = null){
		$query = 'SELECT a.*, b.name AS creatorname, b.username AS creatorusername, b.email FROM '.acymailing_table('action').' AS a LEFT JOIN '.acymailing_table('users', false).' AS b on a.userid = b.id WHERE action_id = '.intval($actionid).' LIMIT 1';
		$this->database->setQuery($query);
		return $this->database->loadObject();
	}

	function saveForm(){
		$app = JFactory::getApplication();

		$action = new stdClass();
		$action->action_id = acymailing_getCID('action_id');

		$formData = JRequest::getVar('data', array(), '', 'array');

		foreach($formData['action'] as $column => $value){
			if($app->isAdmin() || $this->allowedField('action', $column)){
				acymailing_secureField($column);
				$action->$column = strip_tags($value);
			}
		}

		if(empty($action->action_id)) $action->nextdate = time() + intval($action->frequency);
		if($action->password == '********') unset($action->password);

		$action->conditions = json_encode($formData['conditions']);
		$action->actions = json_encode($formData['actions']);

		if(isset($action->published) && $action->published != 1) $action->published = 0;
		$action_id = $this->save($action);
		if(!$action_id) return false;

		JRequest::setVar('action_id', $action_id);
		return true;
	}

	function save($action){
		if(empty($action->action_id) && empty($action->userid)){
			$user = JFactory::getUser();
			$action->userid = $user->id;
		}

		JPluginHelper::importPlugin('acymailing');
		$dispatcher = JDispatcher::getInstance();
		if(empty($action->action_id)){
			$dispatcher->trigger('onAcyBeforeActionCreate', array(&$action));
			$status = $this->database->insertObject(acymailing_table('action'), $action);
		}else{
			$dispatcher->trigger('onAcyBeforeActionModify', array(&$action));
			$status = $this->database->updateObject(acymailing_table('action'), $action, 'action_id');
		}

		if($status) return empty($action->action_id) ? $this->database->insertid() : $action->action_id;
		return false;
	}
}
