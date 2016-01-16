<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

$fieldsets = array('generalsettings','moderation','maintenance','registration', 'media');
foreach ($fieldsets as $fieldset) {
	echo JHtml::_('rsfieldset.start', 'adminform', JText::_($this->fieldsets[$fieldset]->label));
	foreach ($this->form->getFieldset($fieldset) as $field) {
		if ($field->fieldname == 'barcode' && !rseventsproHelper::pdf())
			continue;
		
		$extra = '';
		if ($field->fieldname == 'archive_days')
			$extra = '<span class="rsextra">'.JText::_('COM_RSEVENTSPRO_DAYS').'</span>';
		else if ($field->fieldname == 'incomplete_minutes')
			$extra = '<span class="rsextra">'.JText::_('COM_RSEVENTSPRO_MINUTES').'</span>';
		else if ($field->fieldname == 'icon_small_width' || $field->fieldname == 'icon_big_width')
			$extra = '<span class="rsextra">px</span>';
		echo JHtml::_('rsfieldset.element', $field->label, $field->input.$extra);
	}
	echo JHtml::_('rsfieldset.end');
}