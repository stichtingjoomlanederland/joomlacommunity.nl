<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

$fieldsets = array('maps'); 
foreach ($fieldsets as $fieldset) {
	echo JHtml::_('rsfieldset.start', 'adminform', JText::_($this->fieldsets[$fieldset]->label));
	foreach ($this->form->getFieldset($fieldset) as $field) {
		$extra = '';
		if ($field->fieldname == 'google_maps_center')
			$extra = '<span class="rsextra"> - '.JText::_('COM_RSEVENTSPRO_CONF_CHANGE_CENTER').'</span>';
		echo JHtml::_('rsfieldset.element', $field->label, $field->input.$extra);
	}
	echo JHtml::_('rsfieldset.end');
}