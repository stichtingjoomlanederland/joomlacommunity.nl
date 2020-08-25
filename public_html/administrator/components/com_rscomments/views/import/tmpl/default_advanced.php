<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

foreach ($this->fieldsets as $name => $fieldset) {
	if($name == 'columns') echo '<div id="rsc_columns">';
	foreach ($this->form->getFieldset($name) as $field) 
		echo $field->renderField();
	if($name == 'columns') echo '</div>';
}