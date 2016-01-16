<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

$fieldsets = array('payments','ideal'); 
foreach ($fieldsets as $fieldset) {
	if ($fieldset == 'ideal' && !rseventsproHelper::ideal()) continue;
	echo JHtml::_('rsfieldset.start', 'adminform', JText::_($this->fieldsets[$fieldset]->label));
	foreach ($this->form->getFieldset($fieldset) as $field) {
		if (!rseventsproHelper::paypal() && $field->fieldname == 'payment_paypal')
			continue;
		
		echo JHtml::_('rsfieldset.element', $field->label, $field->input);
	}
	echo JHtml::_('rsfieldset.end');
}