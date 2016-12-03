<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

$this->field->startFieldset();

$field = $this->form->getField('backup');
$this->field->showField($field->label, $field->input);

$field = $this->form->getField('overwrite');
$this->field->showField($field->label, $field->input);

$field = $this->form->getField('keepid');
$this->field->showField($field->label, $field->input);

// Button
$this->field->showField('', '<button type="button" class="btn btn-primary" onclick="submitbutton(\'restore.start\')">'.JText::_('RSFP_RESTORE').'</button>', array('class' => 'form-actions'));

$this->field->endFieldset();