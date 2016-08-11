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

$config = acymailing_config();
$doc = JFactory::getDocument();
$doc->addScript(ACYMAILING_JS.'acymailing_compat.js?v='.str_replace('.','',$config->get('version')));

if(!ACYMAILING_J16){

	class JElementTermscontent extends JElement
	{

		function fetchElement($name, $value, &$node, $control_name)
		{
			$link = 'index.php?option=com_content&amp;task=element&amp;tmpl=component&amp;object=content';
			$text = '<input class="inputbox" id="'.$control_name.'termscontent" name="'.$control_name.'[termscontent]" type="text" style="width:100px" value="'.$value.'">';
			$text .= '<a class="modal" id="termscontent" title="Select one content which will be displayed for the Terms & Conditions"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 650, y: 375}}"><button class="btn" onclick="return false">'.JText::_('SELECT').'</button></a>';

			$js = "function jSelectArticle(id, title, object) {
				document.getElementById('".$control_name."termscontent').value = id;
				acymailing_js.closeBox(true);
			}";
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration($js);

			return $text;
		}
	}
}else{
	class JFormFieldTermscontent extends JFormField
	{
		var $type = 'termscontent';

		function getInput() {

			$link = 'index.php?option=com_content&amp;view=articles&amp;layout=modal&amp;tmpl=component&amp;object=content&amp;function=jSelectArticle';
			$text = '<input class="inputbox" id="termscontent" name="'.$this->name.'" type="text" style="width:100px" value="'.$this->value.'">';
			$text .= '<a class="modal" id="termscontent" title="Select one content which will be displayed for the Terms & Conditions"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 650, y: 375}}"><button class="btn" onclick="return false">'.JText::_('SELECT').'</button></a>';

			$js = "function jSelectArticle(id, title,catid, object) {
				document.getElementById('termscontent').value = id;
				acymailing_js.closeBox(true);
			}";

			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration($js);
			return $text;
		}
	}
}
