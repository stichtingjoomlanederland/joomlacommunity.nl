<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

echo JHtml::_('rsfieldset.start', 'adminform', JText::_($this->fieldsets['payments']->label));
foreach ($this->form->getFieldset('payments') as $field) {
	if (!rseventsproHelper::paypal() && $field->fieldname == 'payment_paypal')
		continue;
	
	echo JHtml::_('rsfieldset.element', $field->label, $field->input);
}
echo JHtml::_('rsfieldset.end');

JFactory::getApplication()->triggerEvent('rseproIdealSettings', array(array('data' => $this->config)));