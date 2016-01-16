<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

$fieldsets = $this->form->getFieldsets('gallery'); 
foreach ($fieldsets as $name => $fieldset) {
	echo JHtml::_('rsfieldset.start', 'adminform', JText::_($fieldset->label));
	foreach ($this->form->getFieldset($name) as $field) {
		echo JHtml::_('rsfieldset.element', $field->label, $field->input);
	}
	echo JHtml::_('rsfieldset.end');
}