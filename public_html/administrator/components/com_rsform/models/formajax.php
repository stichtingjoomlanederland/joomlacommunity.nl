<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

class RsformModelFormajax extends JModelLegacy
{
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
			$title = JText::_('RSFP_COMP_FIELD_'.$name);
			$content = JText::_('RSFP_COMP_FIELD_'.$name.'_DESC');
			$tooltip .= ' class="fieldHasTooltip" data-content="'. $content .'" data-title="' . $title . '"';
		}
		
		return $tooltip;
	}

	public function getComponentFields()
	{
		$db = JFactory::getDbo();
		$lang = JFactory::getLanguage();
		$return = array(
			'general'		=> array(),
			'validations' 	=> array(),
			'attributes' 	=> array()
		);
		$data = $this->getComponentData();

		$general		= array('NAME','CAPTION','LABEL','DEFAULTVALUE','ITEMS','TEXT','DESCRIPTION','COMPONENTTYPE');
		$validations	= array('REQUIRED','VALIDATIONRULE','VALIDATIONMESSAGE','VALIDATIONEXTRA', 'VALIDATIONDATE');

		$componentId = $this->getComponentId();
		$componentType = $this->getComponentType();

		$query = $db->getQuery(true)
            ->select('*')
            ->from($db->qn('#__rsform_component_type_fields'))
            ->where($db->qn('ComponentTypeId') . ' = ' . $db->q($componentType))
            ->order($db->qn('Ordering') . ' ' . $db->escape('asc'));
		$results = $db->setQuery($query)->loadObjectList();

		$translatable = RSFormProHelper::getTranslatableProperties();

		foreach ($results as $i => $result)
		{
			if ($result->FieldName == 'ADDITIONALATTRIBUTES')
				$results[$i]->Ordering = 1001;
		}

		usort($results, array($this, 'sortFields'));

		foreach ($results as $result)
		{
			$field = new stdClass();
			$field->name = $result->FieldName;
			$field->caption = $lang->hasKey('RSFP_COMP_FIELD_'.$field->name) ? JText::_('RSFP_COMP_FIELD_'.$field->name) : $field->name;
			$field->label = '<label for="'.$field->name.'" id="caption' . $field->name.'" '.$this->getTooltip($field->name).'>'.$field->caption.'</label>';
			$field->body = '';
			$field->type = $result->FieldType;

			switch ($result->FieldType)
			{
				case 'color':
				case 'textbox':
				{
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

					if ($result->Properties != ''){
						$additional .= ' data-properties="'. $result->Properties .'"';
					}

					$type = $result->FieldType == 'textbox' ? 'text' : 'color';

					$field->body .= '<input type="' . $type . '" id="'.$field->name.'" name="param['.$field->name.']" value="'.RSFormProHelper::htmlEscape($value).'" '.$additional.' class="rsform_inp" />';
				}
					break;

				case 'textarea':
				{
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

					$field->body .= '<textarea id="'.$field->name.'" name="param['.$field->name.']" rows="5" cols="20" class="rsform_txtarea" '. $additional .'>'.RSFormProHelper::htmlEscape($value).'</textarea>';
				}
					break;

				case 'select':
				case 'selectmultiple':
				{					
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

					if (in_array($field->name, array('VALIDATIONRULE', 'VALIDATIONMULTIPLE')))
                    {
                        $additional .=  'onchange="changeValidation(this);"';
                    }

					$field->body .= '<select name="param['.$field->name.']'.($result->FieldType == 'selectmultiple' ? '[]' : '').'" '. $additional .' id="'.$field->name.'">';

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
					foreach ($items as $item)
					{
						$buf = explode('|', $item, 2);

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
			}

			$field->translatable = (in_array($result->FieldName, $translatable) && $result->FieldType != 'hiddenparam' && $result->FieldType != 'hidden');

			if (in_array($field->name, $general) || $result->FieldType == 'hidden' || $result->FieldType == 'hiddenparam')
				$return['general'][] = $field;
			elseif (in_array($field->name, $validations) || strpos($field->name, 'VALIDATION') !== false)
				$return['validations'][] = $field;
			else
				$return['attributes'][] = $field;
		}

		return $return;
	}

	public function getComponentData()
	{
		$componentId = $this->getComponentId();
		$data 		 = array();
		
		if ($componentId > 0)
		{
			$data = RSFormProHelper::getComponentProperties($componentId);
		}

		return $data;
	}

	public function getComponentType()
	{
		return JFactory::getApplication()->input->get->getInt('componentType');
	}

	public function getComponentId()
	{
		$cid    = JFactory::getApplication()->input->getInt('componentId');
		$cids   = JFactory::getApplication()->input->get('cid', array(), 'array');
		if (is_array($cids) && count($cids)) {
			$cids = array_map('intval', $cids);
			$cid = $cids;
		}

		return $cid;
	}

	public function getI()
	{
		return JFactory::getApplication()->input->getInt('i');
	}

	public function getComponent()
	{
		$componentId = $this->getComponentId();
		$return 	 = new stdClass();
		
		$query = $this->_db->getQuery(true)
			->select($this->_db->qn('Published'))
			->from($this->_db->qn('#__rsform_components'))
			->where($this->_db->qn('ComponentId') . ' = ' . $this->_db->q($componentId));
		
		$return->published = $this->_db->setQuery($query)->loadResult();

		// required?
		$data = $this->getComponentData();
		if (isset($data['REQUIRED']))
		{
			$return->required = $data['REQUIRED'] == 'YES';
		}

		return $return;
	}

	public function componentsChangeStatus()
	{
		$componentId = $this->getComponentId();
		$task 		 = strtolower(JFactory::getApplication()->input->getWord('task'));
		$published 	 = $task == 'componentspublish' ? 1 : 0;
		
		$query = $this->_db->getQuery(true)
			->update($this->_db->qn('#__rsform_components'))
			->set($this->_db->qn('Published') . ' = ' . $this->_db->q($published));
		
		if (is_array($componentId))
		{
			$query->where($this->_db->qn('ComponentId') . ' IN (' . implode(',', $componentId) . ')');
		}
		else
		{
			$query->where($this->_db->qn('ComponentId') . ' = ' . $this->_db->q($componentId));
		}

		$this->_db->setQuery($query)->execute();
	}

	public function componentsChangeRequired()
	{
		$componentId = $this->getComponentId();
		$task 		 = strtolower(JFactory::getApplication()->input->getWord('task'));
		$required 	 = $task == 'componentssetrequired' ? 'YES' : 'NO';
		
		$query = $this->_db->getQuery(true)
			->update($this->_db->qn('#__rsform_properties'))
			->set($this->_db->qn('PropertyValue') . ' = ' . $this->_db->q($required))
			->where($this->_db->qn('PropertyName') . ' = ' . $this->_db->q('REQUIRED'));
		
		if (is_array($componentId))
		{
			$query->where($this->_db->qn('ComponentId') . ' IN (' . implode(',', $componentId) . ')');
		}
		else
		{
			$query->where($this->_db->qn('ComponentId') . ' = ' . $this->_db->q($componentId));
		}

		$this->_db->setQuery($query)->execute();
	}
}