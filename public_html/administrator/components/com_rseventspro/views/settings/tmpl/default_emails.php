<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );?>

<div class="span12">
	<?php 
	$fieldsets = array('emails'); 
	foreach ($fieldsets as $fieldset) {
		echo JHtml::_('rsfieldset.start', 'adminform');
		foreach ($this->form->getFieldset($fieldset) as $field) {
			echo JHtml::_('rsfieldset.element', $field->label, $field->input);
		}
		echo JHtml::_('rsfieldset.end');
	}
	?>
</div>