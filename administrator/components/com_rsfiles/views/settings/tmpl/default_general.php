<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

$fieldsets = array('general');
foreach ($fieldsets as $fieldset) {
	echo JHtml::_('rsfieldset.start', 'adminform', JText::_($this->fieldsets[$fieldset]->label));
	foreach ($this->form->getFieldset($fieldset) as $field) {
		$extra = $field->fieldname == 'remove_days' ? '<span class="rsf_text_conf"> '.JText::_('COM_RSFILES_CONF_DAYS').'</span>' : '';
		
		echo JHtml::_('rsfieldset.element', $field->label, $field->input.$extra);
	}
	echo JHtml::_('rsfieldset.end');
}