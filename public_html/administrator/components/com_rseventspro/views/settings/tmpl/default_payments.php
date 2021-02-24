<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<fieldset class="options-form">
	<legend><?php echo JText::_($this->fieldsets['payments']->label); ?></legend>
	<?php 
		foreach ($this->form->getFieldset('payments') as $field) {
			if (!rseventsproHelper::paypal() && $field->fieldname == 'payment_paypal') {
				continue;
			}
	
			echo $field->renderField();
		} 
	?>
</fieldset>

<?php JFactory::getApplication()->triggerEvent('onrseproIdealSettings', array(array('data' => $this->config, 'form' => $this->form))); ?>