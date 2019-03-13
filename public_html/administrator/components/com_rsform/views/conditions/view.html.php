<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformViewConditions extends JViewLegacy
{
	public function display($tpl = null)
	{
        if (!JFactory::getUser()->authorise('forms.manage', 'com_rsform'))
        {
            throw new Exception(JText::_('COM_RSFORM_NOT_AUTHORISED_TO_USE_THIS_SECTION'));
        }

		$lists 			= array();
		$condition		= $this->get('condition');
		$allFields 		= $this->get('allFields');

        $lists['allfields'] = JHtml::_('select.genericlist', $allFields, 'component_id', '', 'ComponentId', 'PropertyValue', $condition->component_id);

		$actions = array(
			JHtml::_('select.option', 'show', JText::_('RSFP_CONDITION_SHOW')),
			JHtml::_('select.option', 'hide', JText::_('RSFP_CONDITION_HIDE'))
		);
		$lists['action'] = JHtml::_('select.genericlist', $actions, 'action', '', 'value', 'text', $condition->action);
		
		$blocks = array(
			JHtml::_('select.option', 1, JText::_('RSFP_CONDITION_BLOCK')),
			JHtml::_('select.option', 0, JText::_('RSFP_CONDITION_FIELD'))
		);
		$lists['block'] = JHtml::_('select.genericlist', $blocks, 'block', '', 'value', 'text', $condition->block);
		
		$conditions = array(
			JHtml::_('select.option', 'all', JText::_('RSFP_CONDITION_ALL')),
			JHtml::_('select.option', 'any', JText::_('RSFP_CONDITION_ANY'))
		);
		$lists['condition'] = JHtml::_('select.genericlist', $conditions, 'condition', '', 'value', 'text', $condition->condition);
		
		$operators = array(
			JHtml::_('select.option', 'is', JText::_('RSFP_CONDITION_IS')),
			JHtml::_('select.option', 'is_not', JText::_('RSFP_CONDITION_IS_NOT'))
		);

        $this->lang         = $this->get('lang');
        $this->operators    = $operators;
        $this->allFields    = $allFields;
        $this->optionFields = $this->get('optionFields');
        $this->formId       = $this->get('formId');
        $this->close        = JFactory::getApplication()->input->getInt('close');
        $this->condition    = $condition;
        $this->lists        = $lists;
		
		parent::display($tpl);
	}
}