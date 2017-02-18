<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

class RsformViewConditions extends JViewLegacy
{
	function display($tpl = null)
	{		
		$lists 			= array();
		$condition		= $this->get('condition');
		$optionFields 	= $this->get('optionFields');
		$allFields 		= $this->get('allFields');
		
		foreach ($allFields as $field)
			foreach ($optionFields as $i => $optionField)
				if ($field->ComponentId == $optionField->ComponentId)
				{
					$optionField->ComponentName = $field->PropertyValue;
					$optionFields[$i] = $optionField;
					break;
				}
		
		$actions = array(
			JHTML::_('select.option', 'show', JText::_('RSFP_CONDITION_SHOW')),
			JHTML::_('select.option', 'hide', JText::_('RSFP_CONDITION_HIDE'))
		);
		$lists['action'] = JHTML::_('select.genericlist', $actions, 'action', '', 'value', 'text', $condition->action);
		
		$blocks = array(
			JHTML::_('select.option', 1, JText::_('RSFP_CONDITION_BLOCK')),
			JHTML::_('select.option', 0, JText::_('RSFP_CONDITION_FIELD'))
		);
		$lists['block'] = JHTML::_('select.genericlist', $blocks, 'block', '', 'value', 'text', $condition->block);
		
		$conditions = array(
			JHTML::_('select.option', 'all', JText::_('RSFP_CONDITION_ALL')),
			JHTML::_('select.option', 'any', JText::_('RSFP_CONDITION_ANY'))
		);
		$lists['condition'] = JHTML::_('select.genericlist', $conditions, 'condition', '', 'value', 'text', $condition->condition);
		
		$operators = array(
			JHTML::_('select.option', 'is', JText::_('RSFP_CONDITION_IS')),
			JHTML::_('select.option', 'is_not', JText::_('RSFP_CONDITION_IS_NOT'))
		);
		
		$lists['allfields'] = JHTML::_('select.genericlist', $allFields, 'component_id', '', 'ComponentId', 'PropertyValue', $condition->component_id);
		
		$this->lang = $this->get('lang');
		$this->operators = $operators;
		$this->allFields = $allFields;
		$this->optionFields = $optionFields;
		$this->formId = $this->get('formId');
		$this->close = JFactory::getApplication()->input->getInt('close');
		$this->condition = $condition;
		$this->lists = $lists;
		
		parent::display($tpl);
	}
}