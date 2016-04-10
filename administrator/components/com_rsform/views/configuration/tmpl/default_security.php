<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

// set description if required
if (isset($this->fieldset->description) && !empty($this->fieldset->description)) { ?>
	<p><?php echo JText::_($this->fieldset->description); ?></p>
<?php } ?>
<?php
$this->field->startFieldset();
foreach ($this->fields as $field) {
	if ($field->fieldname == 'allow_unsafe' && !RSFormProHelper::isJ('3.4')) {
		continue;
	}
	$this->field->showField($field->hidden ? '' : $field->label, $field->input);
}
$this->field->endFieldset();