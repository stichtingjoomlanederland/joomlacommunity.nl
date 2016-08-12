<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSFormModelFormAjax extends JModelLegacy
{
	function __construct()
	{
		parent::__construct();

		$this->_db = JFactory::getDBO();
	}

	public static function sortFields($a,$b)
	{
		if ($a->Ordering == $b->Ordering) return 0;
		return ($a->Ordering < $b->Ordering) ? -1 : 1;
	}
	
	protected function getTooltip($name) {
		static $lang;
		if (!$lang) {
			$lang = JFactory::getLanguage();
		}
		
		$tooltip = '';
		
		if ($lang->hasKey('RSFP_COMP_FIELD_'.$name.'_DESC')) {
			$title = JText::_('RSFP_COMP_FIELD_'.$name.'_DESC');
			
			if (!RSFormProHelper::isJ('3.0')) {
				$title = htmlspecialchars($title, ENT_COMPAT, 'utf-8');
			}
			
			$tooltip .= ' class="fieldHasTooltip" title="' . $title . '"';
		}
		
		return $tooltip;
	}

	function getComponentFields()
	{
		$db = JFactory::getDBO();
		$lang = JFactory::getLanguage();
		$return = array();
		$data = $this->getComponentData();
		$formId = JFactory::getApplication()->input->getInt('formId');

		$general		= array('NAME','CAPTION','LABEL','DEFAULTVALUE','ITEMS','TEXT','DESCRIPTION','COMPONENTTYPE');
		$validations	= array('REQUIRED','VALIDATIONRULE','VALIDATIONMESSAGE','VALIDATIONEXTRA');

		$componentId = $this->getComponentId();
		$componentType = $this->getComponentType();
		$results = $this->_getList("SELECT * FROM #__rsform_component_type_fields WHERE ComponentTypeId='".$componentType."' ORDER BY Ordering ASC");

		$translatable = RSFormProHelper::getTranslatableProperties();

		foreach ($results as $i => $result)
		{
			if ($result->FieldName == 'ADDITIONALATTRIBUTES')
				$results[$i]->Ordering = 1001;
		}

		usort($results, array('RSFormModelFormAjax', 'sortFields'));

		$taborder = 1;

		foreach ($results as $result)
		{
			$field = new stdClass();
			$field->name = $result->FieldName;
			$field->body = '';

			switch ($result->FieldType)
			{
				case 'textbox':
				{
					if ($lang->hasKey('RSFP_COMP_FIELD_'.$field->name))
						$field->body = JText::_('RSFP_COMP_FIELD_'.$field->name);
					else
						$field->body = $field->name;

					$field->body = '<span id="caption'.$field->name.'" '.$this->getTooltip($field->name).'>'.$field->body.'</span> ';

					$field->body .= '<br/>';

					if ($componentId > 0)
						$value = isset($data[$field->name]) ? $data[$field->name] : '';
					else
					{
						$values = RSFormProHelper::isCode($result->FieldValues);

						if ($lang->hasKey('RSFP_COMP_FVALUE_'.$values))
							$value = JText::_('RSFP_COMP_FVALUE_'.$values);
						else
							$value = $values;
					}

					$additional = '';
					if ($result->FieldName == 'FILESIZE'){
						$additional = 'onkeyup="javascript:this.value=this.value.replace(/[^0-9]/g, \'\');"';
					}

					if ($result->Properties != ''){
						$additional .= ' data-properties="'. $result->Properties .'"';
					}

					$field->body .= '<input type="text" id="'.$field->name.'" name="param['.$field->name.']" tabindex="'.$taborder.'" value="'.RSFormProHelper::htmlEscape($value).'" '.$additional.' class="rsform_inp" />';
				}
					break;

				case 'textarea':
				{
					if ($lang->hasKey('RSFP_COMP_FIELD_'.$field->name))
						$field->body = JText::_('RSFP_COMP_FIELD_'.$field->name);
					else
						$field->body = $field->name;

					$field->body = '<span id="caption'.$field->name.'" '.$this->getTooltip($field->name).'>'.$field->body.'</span>';
					$field->body .= '<br />';

					if ($componentId > 0)
					{
						if (!isset($data[$field->name]))
							$data[$field->name] = '';

						if ($lang->hasKey('RSFP_COMP_FVALUE_'.$data[$field->name]))
							$value = JText::_('RSFP_COMP_FVALUE_'.$data[$field->name]);
						else
							$value = $data[$field->name];
					}
					else
					{
						$values = RSFormProHelper::isCode($result->FieldValues);

						if ($lang->hasKey('RSFP_COMP_FVALUE_'.$values))
							$value = JText::_('RSFP_COMP_FVALUE_'.$values);
						else
							$value = $values;
					}

					$additional = '';

					if ($result->Properties != ''){
						$additional .= 'data-properties="'. $result->Properties .'"';
						$additional .= ' data-tags="' .RSFormProHelper::htmlEscape($value). '" ';
					}

					$field->body .= '<textarea id="'.$field->name.'" name="param['.$field->name.']" tabindex="'.$taborder.'" rows="5" cols="20" class="rsform_txtarea" '. $additional .'>'.RSFormProHelper::htmlEscape($value).'</textarea>';
				}
					break;

				case 'select':
				case 'selectmultiple':
				{
					if ($lang->hasKey('RSFP_COMP_FIELD_'.$field->name))
						$field->body = JText::_('RSFP_COMP_FIELD_'.$field->name);
					else
						$field->body = $field->name;

					$field->body = '<span id="caption'.$field->name.'" '.$this->getTooltip($field->name).'>'.$field->body.'</span>';
					$field->body .= '<br />';

					$additional = '';
					/**
					 * determine if we have a json in the properties.
					 * used to create the conditional fields
					 * the JSON should have the following structure
					 * case -> value -> array (fields to be toggled)
					 */
					if (json_decode($result->Properties))
					{
						$additional .= ' data-toggle="' . RSFormProHelper::htmlEscape($result->Properties) . '" data-properties="toggler"';
					}
					
					// set the multiple attribute and select size if needed
					if ($result->FieldType == 'selectmultiple') {
						$additional .= ' size="5" multiple="multiple"';
					}

					$field->body .= '<select name="param['.$field->name.']'.($result->FieldType == 'selectmultiple' ? '[]' : '').'" '. $additional .' tabindex="'.$taborder.'" id="'.$field->name.'" onchange="changeValidation(this);">';

					if (!isset($data[$field->name]))
						$data[$field->name] = '';
						
					if ($result->FieldType == 'selectmultiple') {
						if(!empty($data[$field->name])) {
							$data[$field->name] = explode(',', $data[$field->name]);
						}
					}

					$result->FieldValues = str_replace("\r", '', $result->FieldValues);
					$items = RSFormProHelper::isCode($result->FieldValues);
					$items = explode("\n",$items);
					foreach($items as $item)
					{
						$buf = explode('|',$item);

						$option_value = $buf[0];
						$option_shown = count($buf) == 1 ? $buf[0] : $buf[1];

						if ($lang->hasKey('RSFP_COMP_FVALUE_'.$option_shown))
							$label = JText::_('RSFP_COMP_FVALUE_'.$option_shown);
						else
							$label = $option_shown;

						$field->body .= '<option '.($componentId > 0 && ((!is_array($data[$field->name]) && $data[$field->name] == $option_value) || (is_array($data[$field->name]) && in_array($option_value, $data[$field->name]))) ? 'selected="selected"' : '').' value="'.$option_value.'">'.RSFormProHelper::htmlEscape($label).'</option>';
					}
					$field->body .= '</select>';
				}
					break;

				case 'hidden':
				{
					$values = $result->FieldValues;
					if (defined('RSFP_COMP_FVALUE_'.$values))
						$value = constant('RSFP_COMP_FVALUE_'.$values);
					else
						$value = $values;

					$field->body = '<input type="hidden" id="'.$field->name.'" name="'.$field->name.'" value="'.RSFormProHelper::htmlEscape($value).'" />';
				}
					break;

				case 'hiddenparam':
					$field->body = '<input type="hidden" id="'.$field->name.'" name="param['.$field->name.']" value="'.RSFormProHelper::htmlEscape($result->FieldValues).'" />';
					break;

				case 'emailattach':
				{
					if ($lang->hasKey('RSFP_COMP_FIELD_'.$field->name))
						$field->body = JText::_('RSFP_COMP_FIELD_'.$field->name);
					else
						$field->body = $field->name;

					$field->body = '<span id="caption'.$field->name.'" '.$this->getTooltip($field->name).'>'.$field->body.'</span>';
					$field->body .= '<br />';

					if (!isset($data[$field->name]))
						$data[$field->name] = '';

					$values = (trim($data[$field->name]) != '') ? explode(',',$data[$field->name]) : array();

					$db->setQuery("SELECT id, subject FROM #__rsform_emails WHERE `type` = 'additional' AND formId = ".$formId." ");
					$emails = $db->loadObjectList();

					$field->body .= '<select name="param['.$field->name.'][]" id="'.$field->name.'" tabindex="'.$taborder.'" onchange="changeValidation(this);" multiple="multiple" size="5" style="width:414px">';
					$field->body .= '<option '.($componentId > 0 && in_array('useremail',$values) ? 'selected="selected"' : '').' value="useremail">'.RSFormProHelper::htmlEscape(JText::_('RSFP_COMP_ATTACH_UEMAIL')).'</option>';
					$field->body .= '<option '.($componentId > 0 && in_array('adminemail',$values) ? 'selected="selected"' : '').' value="adminemail">'.RSFormProHelper::htmlEscape(JText::_('RSFP_COMP_ATTACH_AEMAIL')).'</option>';
					$field->body .= '<optgroup label="'.RSFormProHelper::htmlEscape(JText::_('RSFP_COMP_ATTACH_ADEMAIL')).'">';

					if (!empty($emails))
					{
						foreach ($emails as $email)
						{
							$field->body .= '<option '.($componentId > 0 && in_array($email->id,$values) ? 'selected="selected"' : '').' value="'.$email->id.'">'.RSFormProHelper::htmlEscape($email->subject).'</option>';
						}
					}

					$field->body .= '</optgroup>';
					$field->body .= '</select>';

				}
					break;
					$taborder++;
			}

			if (in_array($result->FieldName, $translatable) && $result->FieldType != 'hiddenparam' && $result->FieldType != 'hidden')
				$field->body = RSFormProHelper::translateIcon().' '.$field->body;

			if (in_array($field->name, $general) || $result->FieldType == 'hidden' || $result->FieldType == 'hiddenparam')
				$return['general'][] = $field;
			elseif (in_array($field->name, $validations) || strpos($field->name, 'VALIDATION') !== false)
				$return['validations'][] = $field;
			else
				$return['attributes'][] = $field;
		}

		return $return;
	}

	function getComponentData()
	{
		$componentId = $this->getComponentId();

		$data = array();
		if ($componentId > 0)
			$data = RSFormProHelper::getComponentProperties($componentId);

		return $data;
	}

	function getComponentType()
	{
		return JFactory::getApplication()->input->getInt('componentType');
	}

	function getComponentId()
	{
		$cid = JFactory::getApplication()->input->getInt('componentId');

		$cids = JRequest::getVar('cid', array());
		if (is_array($cids) && count($cids)) {
			JArrayHelper::toInteger($cids);
			$cid = $cids;
		}

		return $cid;
	}

	function getI()
	{
		return JFactory::getApplication()->input->getInt('i');
	}

	function getComponent()
	{
		$componentId = $this->getComponentId();

		$return = new stdClass();
		$this->_db->setQuery("SELECT Published FROM #__rsform_components WHERE ComponentId='".$componentId."'");
		$return->published = $this->_db->loadResult();

		// required?
		$data = $this->getComponentData();
		if (isset($data['REQUIRED'])) {
			$return->required = $data['REQUIRED'] == 'YES';
		}

		return $return;
	}

	function componentsChangeStatus()
	{
		$componentId = $this->getComponentId();

		$task = strtolower(JFactory::getApplication()->input->getWord('task'));
		$published = 0;
		if ($task == 'componentspublish')
			$published = 1;

		if (is_array($componentId))
			$this->_db->setQuery("UPDATE #__rsform_components SET Published='".$published."' WHERE ComponentId IN (".implode(',', $componentId).")");
		else
			$this->_db->setQuery("UPDATE #__rsform_components SET Published='".$published."' WHERE ComponentId='".$componentId."'");

		$this->_db->execute();
	}

	function componentsChangeRequired()
	{
		$componentId = $this->getComponentId();

		$task = strtolower(JFactory::getApplication()->input->getWord('task'));
		$required = 'NO';
		if ($task == 'componentssetrequired')
			$required = 'YES';

		if (is_array($componentId))
			$this->_db->setQuery("UPDATE #__rsform_properties SET PropertyValue='".$required."' WHERE PropertyName='REQUIRED' AND ComponentId IN (".implode(',', $componentId).")");
		else
			$this->_db->setQuery("UPDATE #__rsform_properties SET PropertyValue='".$required."' WHERE PropertyName='REQUIRED' AND ComponentId='".$componentId."'");

		$this->_db->execute();
	}
}