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

class acytoggleHelper{

	var $ctrl = 'toggle';
	var $extra = '';

	private function _getToggle($column, $table = ''){

		$params = new stdClass();
		$params->mode = 'pictures';
		if($column == 'published' && !in_array($table, array('plugins', 'list'))){
			$params->aclass = array(0 => 'acyicon-cancel', 1 => 'acyicon-apply', 2 => 'acyicon-schedule');
			$params->description = array(0 => JText::_('PUBLISH_CLICK'), 1 => JText::_('UNPUBLISH_CLICK'), 2 => JText::_('UNSCHEDULE_CLICK'));
			$params->values = array(0 => 1, 1 => 0, 2 => 0);
			return $params;
		}elseif($column == 'status'){
			$params->mode = 'class';
			$params->class = array(-1 => 'roundsubscrib roundunsub', 1 => 'roundsubscrib roundsub', 2 => 'roundsubscrib roundconf');
			$params->description = array(-1 => JText::_('SUBSCRIBE_CLICK'), 1 => JText::_('UNSUBSCRIBE_CLICK'), 2 => JText::_('CONFIRMATION_CLICK'));
			$params->values = array(-1 => 1, 1 => -1, 2 => 1);
			return $params;
		}

		$params->aclass = array(0 => 'acyicon-cancel', 1 => 'acyicon-apply');
		$params->values = array(0 => 1, 1 => 0);
		return $params;
	}

	function toggleText($action = '', $value = '', $table = '', $text = ''){
		static $jsincluded = false;
		static $id = 0;
		$id++;
		if(!$jsincluded){
			$jsincluded = true;
			$js = "function joomToggleText(id,newvalue,table){
				window.document.getElementById(id).className = 'onload';
				try{
					new Ajax('index.php?option=com_acymailing&tmpl=component&ctrl=toggle&task='+id+'&value='+newvalue+'&table='+table+'&".acymailing_getFormToken()."=1',{ method: 'get', update: $(id), onComplete: function() {	window.document.getElementById(id).className = 'loading'; }}).request();
				}catch(err){
					new Request({url:'index.php?option=com_acymailing&tmpl=component&ctrl=toggle&task='+id+'&value='+newvalue+'&table='+table+'&".acymailing_getFormToken()."=1',method: 'get', onComplete: function(response) { $(id).innerHTML = response; window.document.getElementById(id).className = 'loading'; }}).send();
				}
			}";
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration($js);
		}

		if(!$action) return;

		return '<span id="'.$action.'_'.$value.'" ><a href="javascript:void(0);" onclick="joomToggleText(\''.$action.'_'.$value.'\',\''.$value.'\',\''.$table.'\')">'.$text.'</a></span>';
	}

	function toggle($id, $value, $table, $extra = null){
		$column = substr($id, 0, strpos($id, '_'));
		$params = $this->_getToggle($column, $table);
		if(!isset($params->values[$value])) return;
		$newValue = $params->values[$value];
		if($params->mode == 'pictures'){
			static $pictureincluded = false;
			if(!$pictureincluded){
				$pictureincluded = true;
				$js = "function joomTogglePicture(id,newvalue,table){
					window.document.getElementById(id).className = 'onload';
					try{
						new Ajax('index.php?option=com_acymailing&tmpl=component&ctrl=toggle&task='+id+'&value='+newvalue+'&table='+table+'&".acymailing_getFormToken()."=1',{ method: 'get', update: $(id), onComplete: function() {	window.document.getElementById(id).className = 'loading'; }}).request();
					}catch(err){
						new Request({url:'index.php?option=com_acymailing&tmpl=component&ctrl=toggle&task='+id+'&value='+newvalue+'&table='+table+'&".acymailing_getFormToken()."=1',method: 'get', onComplete: function(response) { $(id).innerHTML = response; window.document.getElementById(id).className = 'loading'; }}).send();
					}
				}";
				$doc = JFactory::getDocument();
				$doc->addScriptDeclaration($js);
			}

			$desc = empty($params->description[$value]) ? '' : $params->description[$value];

			if(empty($params->pictures)){
				$text = ' ';
				$class = 'class="'.$params->aclass[$value].'"';
			}else{
				$text = '<img src="'.$params->pictures[$value].'"/>';
				$class = '';
			}

			return '<a href="javascript:void(0);" style="font-style: normal;" '.$class.' onclick="joomTogglePicture(\''.$id.'\',\''.$newValue.'\',\''.$table.'\')" title="'.str_replace('"', '\"', $desc).'">'.$text.'</a>';
		}elseif($params->mode == 'class'){
			if(empty($extra)) return;
			static $classincluded = false;
			if(!$classincluded){
				$classincluded = true;
				$js = "function joomToggleClass(id,newvalue,table,extra){
					var mydiv=$(id); mydiv.innerHTML = ''; mydiv.className = 'onload';
					try{
						new Ajax('index.php?option=com_acymailing&tmpl=component&ctrl=toggle&task='+id+'&value='+newvalue+'&table='+table+'&".acymailing_getFormToken()."=1&extra[color]='+extra,{ method: 'get', update: $(id), onComplete: function() {	window.document.getElementById(id).className = 'loading'; }}).request();
					}catch(err){
						new Request({url:'index.php?option=com_acymailing&tmpl=component&ctrl=toggle&task='+id+'&value='+newvalue+'&table='+table+'&".acymailing_getFormToken()."=1&extra[color]='+extra,method: 'get', onComplete: function(response) { $(id).innerHTML = response; window.document.getElementById(id).className = 'loading'; }}).send();
					}
				}";
				$doc = JFactory::getDocument();
				$doc->addScriptDeclaration($js);
			}


			$desc = empty($params->description[$value]) ? '' : $params->description[$value];
			$return = '<a href="javascript:void(0);" onclick="joomToggleClass(\''.$id.'\',\''.$newValue.'\',\''.$table.'\',\''.htmlspecialchars(urlencode($extra['color']), ENT_COMPAT, 'UTF-8').'\');" title="'.str_replace('"', '\"', $desc).'"><div class="'.$params->class[$value].'" style="background-color:'.htmlspecialchars($extra['color'], ENT_COMPAT, 'UTF-8').';border-color:'.htmlspecialchars($extra['color'], ENT_COMPAT, 'UTF-8').'">';
			if(!empty($extra['tooltip'])) $return .= acymailing_tooltip($extra['tooltip'], @$extra['tooltiptitle'], '', '&nbsp;&nbsp;&nbsp;&nbsp;');
			$return .= '</div></a>';

			return $return;
		}
	}

	function display($column, $value){
		$params = $this->_getToggle($column);

		$title = '';
		if($column == 'published') $title = 'title="'.($value == 1 ? JText::_('ENABLED') : JText::_('DISABLED')).'"';

		if(empty($params->pictures)){
			return '<a style="cursor:default;" class="'.$params->aclass[$value].'" '.$title.'></a>';
		}else{
			return '<img src="'.$params->pictures[$value].'"/>';
		}
	}

	function delete($lineId, $elementids, $table, $confirm = false, $text = '', $extraJsOnClick = ''){
		static $deleteJS = false;
		if(!$deleteJS){
			$deleteJS = true;
			$js = "function joomDelete(lineid,elementids,table,reqconfirm){
				if(reqconfirm){
					if(!confirm('".JText::_('ACY_VALIDDELETEITEMS', true)."')) return false;
				}
				try{
					new Ajax('index.php?option=com_acymailing&tmpl=component&ctrl=".$this->ctrl.$this->extra."&task=delete&value='+elementids+'&table='+table+'&".acymailing_getFormToken()."=1', { method: 'get', onComplete: function() {window.document.getElementById(lineid).style.display = 'none';}}).request();
				}catch(err){
					new Request({url:'index.php?option=com_acymailing&tmpl=component&ctrl=".$this->ctrl.$this->extra."&task=delete&value='+elementids+'&table='+table+'&".acymailing_getFormToken()."=1',method: 'get', onComplete: function() { window.document.getElementById(lineid).style.display = 'none'; }}).send();
				}
			}";

			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration($js);
		}

		if(empty($text)){
			$app = JFactory::getApplication();
			if($app->isAdmin()){
				$text = '<span class="hasTooltip acyicon-delete" data-original-title="'.JText::_('ACY_DELETE').'" title="'.JText::_('ACY_DELETE').'"/>';
			}else{
				$text = '<img src="media/com_acymailing/images/delete.png" title="Delete">';
			}
		}
		return '<a href="javascript:void(0);" onclick="joomDelete(\''.$lineId.'\',\''.$elementids.'\',\''.$table.'\','.($confirm ? 'true' : 'false').'); '.$extraJsOnClick.'">'.$text.'</a>';
	}
}
