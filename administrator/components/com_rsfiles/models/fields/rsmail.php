<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('JPATH_PLATFORM') or die;
JFormHelper::loadFieldClass('list');

class JFormFieldRsmail extends JFormFieldList
{
	public $type = 'Rsmail';
	
	public function __construct($parent = null) {
		parent::__construct($parent);
	}

	protected function getOptions() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$options= array();
		$default= array(JHtml::_('select.option',0,JText::_('COM_RSFILES_SELECT_LIST')));
		
		// Get the selected users
		$query->clear();
		$query->select($db->qn('IdList','value'));
		$query->select($db->qn('ListName','text'));
		$query->from($db->qn('#__rsmail_lists'));
		
		$db->setQuery($query);
		if ($licenses = $db->loadObjectList()) {
			return array_merge($default,$licenses);
		}
		
		return $default;
	}
}