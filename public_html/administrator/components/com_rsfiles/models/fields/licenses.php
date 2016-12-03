<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('JPATH_PLATFORM') or die;
JFormHelper::loadFieldClass('list');

class JFormFieldLicenses extends JFormFieldList
{
	public $type = 'Licenses';
	
	public function __construct($parent = null) {
		parent::__construct($parent);
	}

	protected function getOptions() {
		$db			= JFactory::getDbo();
		$options	= array();
		$default	= array(JHtml::_('select.option',0,JText::_('COM_RSFILES_NO_LICENSE')));
		$xmloptions	= parent::getOptions();
		
		// Get the selected users
		$query = $db->getQuery(true)->select($db->qn('IdLicense','value'))
			->select($db->qn('LicenseName','text'))
			->from($db->qn('#__rsfiles_licenses'))
			->where($db->qn('published').' = 1');
		
		$db->setQuery($query);
		if ($licenses = $db->loadObjectList()) {
			return array_merge($xmloptions, $default, $licenses);
		}
		
		return $default;
	}
}