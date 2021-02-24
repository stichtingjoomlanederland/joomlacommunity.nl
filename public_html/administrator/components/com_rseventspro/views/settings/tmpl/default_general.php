<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

$fieldsets = array('license','datetime','miscellaneous');
foreach ($fieldsets as $fieldset) {
	echo '<fieldset class="options-form">';
	echo '<legend>'.JText::_($this->fieldsets[$fieldset]->label).'</legend>';
	
	foreach ($this->form->getFieldset($fieldset) as $field) {
		if (!file_exists(JPATH_SITE.'/components/com_community/libraries/core.php') && $field->fieldname == 'jsactivity') {
			continue;
		}
		
		echo $field->renderField();
	}
	
	echo '</fieldset>';
}