<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

$fieldsets = array('generalsettings','moderation','maintenance','registration', 'comments', 'media');
foreach ($fieldsets as $fieldset) {
	echo JHtml::_('rsfieldset.start', 'adminform', JText::_($this->fieldsets[$fieldset]->label));
	foreach ($this->form->getFieldset($fieldset) as $field) {
		$extra = '';
		if ($field->fieldname == 'archive_days')
			$extra = '<span class="rsextra">'.JText::_('COM_RSEVENTSPRO_DAYS').'</span>';
		else if ($field->fieldname == 'incomplete_minutes')
			$extra = '<span class="rsextra">'.JText::_('COM_RSEVENTSPRO_MINUTES').'</span>';
		else if ($field->fieldname == 'icon_small_width' || $field->fieldname == 'icon_big_width' || $field->fieldname == 'seats_width' || $field->fieldname == 'seats_height')
			$extra = '<span class="rsextra">px</span>';
		else if ($field->fieldname == 'default_image') {
			if ($field->value) {
				$extra = '<span class="btn btn-info rsextra '.rseventsproHelper::tooltipClass().'" title="'.htmlentities('<img src="'.JUri::root().'components/com_rseventspro/assets/images/default/'.$field->value.'" alt="" width="100" />').'"><i class="fa fa-image"></i> '.JText::_('COM_RSEVENTSPRO_PREVIEW').'</span> ';
			}
		}
		echo JHtml::_('rsfieldset.element', $field->label, $field->input.$extra);
	}
	echo JHtml::_('rsfieldset.end');
}