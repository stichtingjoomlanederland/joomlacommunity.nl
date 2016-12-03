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
	class JElementHelp extends JElement
	{
		function fetchElement($name, $value, &$node, $control_name)
		{
			$config =& acymailing_config();
			$level = $config->get('level');
			$link = ACYMAILING_HELPURL.$value.'&level='.$level;
			$text = '<a class="modal" title="'.JText::_('ACY_HELP',true).'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 500}}"><button class="btn" onclick="return false">'.JText::_('ACY_HELP').'</button></a>';
			return $text;
		}
	}
}else{
	class JFormFieldHelp extends JFormField
	{
		var $type = 'help';

		function getInput() {
			$config =& acymailing_config();
			$level = $config->get('level');
			$link = ACYMAILING_HELPURL.$this->value.'&level='.$level;
			$text = '<a class="modal" title="'.JText::_('ACY_HELP',true).'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 500}}"><button class="btn" onclick="return false">'.JText::_('ACY_HELP').'</button></a>';
			return $text;
		}
	}
}
