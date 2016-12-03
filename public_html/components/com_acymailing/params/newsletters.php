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

	class JElementNewsletters extends JElement
	{
		function fetchElement($name, $value, &$node, $control_name)
		{
			$db = JFactory::getDBO();
			$db->setQuery("SELECT `mailid`, CONCAT(subject,' ( ',mailid,' )') as `title` FROM #__acymailing_mail WHERE `type`='news' AND (`senddate` IS NULL OR `senddate` < 1)AND `type` = 'news' ORDER BY `subject` ASC");
			$results = $db->loadObjectList();
			$novalue = new stdClass();
			$novalue->mailid = 0;
			$novalue->title = ' - - - - - ';
			array_unshift($results,$novalue);

			return JHTML::_('select.genericlist', $results, $control_name.'['.$name.']' , 'size="1"', 'mailid', 'title', $value);
		}
	}

}else{
	class JFormFieldNewsletters extends JFormField
	{
		var $type = 'newsletters';

		function getInput() {

			$db = JFactory::getDBO();
			$db->setQuery("SELECT `mailid`, CONCAT(subject,' ( ',mailid,' )') as `title` FROM #__acymailing_mail WHERE `type`='news' AND (`senddate` IS NULL OR `senddate` < 1)AND `type` = 'news' ORDER BY `subject` ASC");
			$results = $db->loadObjectList();
			$novalue = new stdClass();
			$novalue->mailid = 0;
			$novalue->title = ' - - - - - ';
			array_unshift($results,$novalue);

			return JHTML::_('select.genericlist', $results, $this->name , 'size="1"', 'mailid', 'title', $this->value);
		}
	}
}
