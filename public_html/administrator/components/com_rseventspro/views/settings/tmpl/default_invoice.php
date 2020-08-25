<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

echo JHtml::_('rsfieldset.start', 'adminform');
foreach ($this->form->getFieldset('invoice') as $field) {
	echo JHtml::_('rsfieldset.element', $field->label, $field->input);
}
echo JHtml::_('rsfieldset.element', '', '<div class="alert alert-info">'.JText::_('COM_RSEVENTSPRO_INVOICE_PLACEHOLDERS').'</div>');
echo JHtml::_('rsfieldset.end'); ?>