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

class uploadfileType{
	function display($picture, $map, $value, $mapdelete = ''){
		$app = JFactory::getApplication();
		if(!$picture){
			return '<input type="hidden" name="'.$map.'[]" id="'.$map.$value.'" />
			<a class="modal acyupload acymailing_button_grey" rel="{handler: \'iframe\', size: {x: 850, y: 600}}" href="index.php?option=com_acymailing&ctrl='.($app->isAdmin() ? '' : 'front').'file&task=select&id='.$map.$value.'&tmpl=component">'.JText::_('SELECT').'</a>
			<span id="'.$map.$value.'selection"></span>';
		}

		$result = '<input type="hidden" name="'.$mapdelete.'" id="'.$map.'" />
		<a class="modal acyupload acymailing_button_grey" rel="{handler: \'iframe\', size: {x: 850, y: 600}}" href="index.php?option=com_acymailing&ctrl='.($app->isAdmin() ? '' : 'front').'file&task=select&id='.$map.'&tmpl=component">'.JText::_('SELECT').'</a>';


		if(empty($value)) $value = 'media/com_acymailing/images/emptyimg.png';
		$result .= '<img id="'.$map.'preview" src="'.ACYMAILING_LIVE.$value.'" style="float:left;max-height:50px;margin-right:10px;" />
		<br /><input type="checkbox" name="'.$mapdelete.'" value="delete" id="delete'.$map.'" /> <label for="delete'.$map.'">'.JText::_('DELETE_PICT').'</label>';

		return $result;
	}
}
