<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

echo '<div class="row-fluid">';
echo '<div class="span12">';
echo JHtml::_('rsfieldset.start', 'adminform', '');
echo JHtml::_('rsfieldset.element', $this->form->getLabel('GroupName'), $this->form->getInput('GroupName'));
echo JHtml::_('rsfieldset.element', $this->form->getLabel('gid'), $this->form->getInput('gid'));
echo JHtml::_('rsfieldset.end');
echo '</div>';
echo '</div>';

echo '<div class="row-fluid">';
echo '<div class="span4">';
echo JHtml::_('rsfieldset.start', 'adminform', JText::_($this->fieldsets['commenting']->label));
foreach ($this->form->getFieldset('commenting') as $field) 
	echo JHtml::_('rsfieldset.element', $field->label, $field->input);
echo JHtml::_('rsfieldset.end');
echo '</div>';

echo '<div class="span4">';
echo JHtml::_('rsfieldset.start', 'adminform', JText::_($this->fieldsets['security']->label));
foreach ($this->form->getFieldset('security') as $field) 
	echo JHtml::_('rsfieldset.element', $field->label, $field->input);
echo JHtml::_('rsfieldset.end');
echo '</div>';

echo '<div class="span4">';
echo JHtml::_('rsfieldset.start', 'adminform', JText::_($this->fieldsets['publishing']->label));
foreach ($this->form->getFieldset('publishing') as $field) 
	echo JHtml::_('rsfieldset.element', $field->label, $field->input);
echo JHtml::_('rsfieldset.end');
echo '</div>';
echo '</div>';