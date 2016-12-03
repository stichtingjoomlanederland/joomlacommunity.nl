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
JHTML::_('behavior.modal','a.modal');
if(!include_once(rtrim(JPATH_ADMINISTRATOR,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_acymailing'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php')){
	echo 'This module can not work without the AcyMailing Component';
}

if(!ACYMAILING_J16){
	class JElementCustomtemplate extends JElement
	{
		function fetchElement($name, $value, &$node, $control_name)
		{
			$link = 'index.php?option=com_acymailing&ctrl=tag&task=customtemplate&tmpl=component&plugin='.$value;
			if(!empty($node->_attributes['help'])) $link .= '&help='.(string)$node->_attributes['help'];
			$text = '<a class="modal" title="'.JText::_('ACY_HELP',true).'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 500}}"><button class="btn" onclick="return false">'.JText::_('ACY_CUSTOMTEMPLATE').'</button></a>';
			return $text;
		}
	}
}else{
	class JFormFieldCustomtemplate extends JFormField
	{
		var $type = 'help';

		function getInput(){
			$link = 'index.php?option=com_acymailing&ctrl=tag&task=customtemplate&tmpl=component&plugin='.$this->value;
			if(!empty($this->element['help'])) $link .= '&help='.(string)$this->element['help'];
			$text = '<a class="modal" title="'.JText::_('ACY_CUSTOMTEMPLATE').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 500}}"><button class="btn" onclick="return false">'.JText::_('ACY_CUSTOMTEMPLATE').'</button></a>';
			return $text;
		}
	}
}
