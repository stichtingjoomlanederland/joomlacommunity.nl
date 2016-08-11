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
JHTML::_('behavior.modal', 'a.modal');
if(!include_once(rtrim(JPATH_ADMINISTRATOR, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_acymailing'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php')){
	echo 'This module can not work without the AcyMailing Component';
}

if(!ACYMAILING_J16){
	class JElementPluginsfield extends JElement{
		function fetchElement($name, $value, &$node, $control_name){
			$app = JFactory::getApplication();
			$link = 'index.php?option=com_acymailing&ctrl='.($app->isAdmin() ? '' : 'front').'tag&task=plgtrigger&plg='.$value.'&fctName='.$value.'&tmpl=component';
			$text = '<a class="modal" title="'.JText::_('ACY_CONFIGURATION').'" href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 500}}"><button class="btn" onclick="return false">'.JText::_('ACY_CONFIGURATION').'</button></a>';
			return $text;
		}
	}
}else{
	class JFormFieldPluginsfield extends JFormField{
		var $type = 'pluginsfield';

		function getInput(){
			$app = JFactory::getApplication();
			$link = 'index.php?option=com_acymailing&ctrl='.($app->isAdmin() ? '' : 'front').'tag&task=plgtrigger&plg='.$this->value.'&fctName='.$this->value.'&tmpl=component';
			$text = '<a class="modal" title="'.JText::_('ACY_CONFIGURATION').'" href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 500}}"><button class="btn" onclick="return false">'.JText::_('ACY_CONFIGURATION').'</button></a>';
			return $text;
		}
	}
}
