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

class acypopupHelper{
	public $useMootools = false;

	function display($text, $title, $url, $id, $width, $height, $attr = '', $icon = '', $type = 'button', $dynamicUrl = false){
		$html = '';

		if(!ACYMAILING_J30 || $this->useMootools){
			JHTML::_('behavior.modal', 'a.modal');
			if($dynamicUrl){
				$onClick = ' onclick="this.href=' + str_replace('"', '\"', $url) + '"';
			}
			$html = '<a '.$this->getAttr($attr, 'modal').' id="'.$id.'" href="'.$url.'" rel="{handler: \'iframe\', size: {x: '.$width.', y: '.$height.'}}">';
			if($type == 'button'){
				$html .= '<button class="btn" onclick="return false">';
			}
			$html .= $text;
			if($type == 'button'){
				$html .= '</button>';
			}
			$html .= '</a>';
		}else{
			$doc = JFactory::getDocument();
			$doc->addScript(ACYMAILING_JS.'acymailing.js?v='.filemtime(ACYMAILING_MEDIA.'js'.DS.'acymailing.js'));
			if($type == 'button'){
				$html = '<button '.$this->getAttr($attr, 'btn btn-small').' id="'.$id.'" onclick="window.acymailing.openBox(this,null,true); return false;">';
			}else{
				$html = '<a '.$attr.' href="#" id="'.$id.'" onclick="window.acymailing.openBox(this,\''.$url.'\',true);">';
			}

			if(!empty($icon)){
				$html .= '<i class="icon-16-'.$icon.'"></i> ';
			}
			$html .= $text.(($type == 'button') ? '</button>' : '</a>');

			$params = array('title' => JText::_($title, true), 'url' => $url, 'height' => $height, 'width' => $width);
			if($dynamicUrl){
				$params['url'] = '\'+'.$url.'+\'';
			}
			$renderModal = JHtml::_('bootstrap.renderModal', 'modal-'.$id, $params);
			$html .= str_replace(array('id="modal-'.$id.'"'), array('id="modal-'.$id.'" style="width:'.($width + 20).'px;height:'.($height + 90).'px;margin-left:-'.(($width + 20) / 2).'px"'), $renderModal);
		}
		return $html;
	}

	function getAttr($attr, $class){
		if(empty($attr)){
			return 'class="'.$class.'"';
		}
		$attr = ' '.$attr;
		if(strpos($attr, ' class="') !== false){
			$attr = str_replace(' class="', ' class="'.$class.' ', $attr);
		}elseif(strpos($attr, ' class=\'') !== false){
			$attr = str_replace(' class=\'', ' class=\''.$class.' ', $attr);
		}else{
			$attr .= ' class="'.$class.'"';
		}
		return trim($attr);
	}
}
