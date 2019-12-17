<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformModelConditions extends JModelLegacy
{
	public function getFormId()
	{
		return JFactory::getApplication()->input->getInt('formId');
	}
	
	public function getAllFields()
	{
	    static $cache;

	    if ($cache === null)
	    {
            $formId = $this->getFormId();

            $query = $this->_db->getQuery(true)
                ->select($this->_db->qn('p.PropertyValue'))
                ->select($this->_db->qn('p.ComponentId'))
                ->select($this->_db->qn('c.ComponentTypeId'))
                ->from($this->_db->qn('#__rsform_components', 'c'))
                ->join('LEFT', $this->_db->qn('#__rsform_properties', 'p') . ' ON (' . $this->_db->qn('c.ComponentId') . '=' . $this->_db->qn('p.ComponentId') . ')')
                ->where($this->_db->qn('c.FormId') . '=' . $this->_db->q($formId))
                ->where($this->_db->qn('p.PropertyName') . '=' . $this->_db->q('NAME'))
                ->order($this->_db->qn('c.Order') . ' ' . $this->_db->escape('ASC'));

            $cache = $this->_db->setQuery($query)->loadObjectList();
        }

        return $cache;
	}
	
	public function getOptionFields()
	{
		$app 	= JFactory::getApplication();
        $formId = $this->getFormId();
		$types 	= array(
            RSFORM_FIELD_SELECTLIST,
            RSFORM_FIELD_CHECKBOXGROUP,
            RSFORM_FIELD_RADIOGROUP,
			RSFORM_FIELD_RANGE_SLIDER
        );
		
		$app->triggerEvent('rsfp_bk_onCreateConditionOptionFields', array(array('types' => &$types, 'formId' => $formId)));
		$types = array_map('intval', $types);

		$optionFields = array();
		if ($fields = $this->getAllFields())
        {
            foreach ($fields as $field)
            {
                if (in_array($field->ComponentTypeId, $types))
                {
                    $optionFields[] = $field;
                }
            }
        }

        if ($optionFields)
        {
            $properties = RSFormProHelper::getComponentProperties($optionFields);

            require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/fielditem.php';
            require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fieldmultiple.php';

            foreach ($optionFields as $optionField)
            {
                // Some cleanup
                $optionField->ComponentName = $optionField->PropertyValue;
                $optionField->items = array();
                unset($optionField->PropertyValue);

                $config = array(
                    'formId' 			=> $formId,
                    'componentId' 		=> $optionField->ComponentId,
                    'data' 				=> $properties[$optionField->ComponentId],
                    'value' 			=> array(),
                    'invalid' 			=> false
                );

				// A workaround to allow Range Slider fields
				if ($optionField->ComponentTypeId == RSFORM_FIELD_RANGE_SLIDER)
				{
					if ($config['data']['USEVALUES'] == 'YES')
					{
						$config['data']['ITEMS'] = $config['data']['VALUES'];
					}
					else
					{
						$config['data']['ITEMS'] = implode("\n", range($config['data']['MINVALUE'], $config['data']['MAXVALUE']));
					}
				}

                $field = new RSFormProFieldMultiple($config);

                if ($items = $field->getItems())
                {
                    foreach ($items as $item)
                    {
						$item = new RSFormProFieldItem($item);
						
						$app->triggerEvent('rsfp_bk_onCreateConditionOptionFieldItem', array(array('field' => &$optionField, 'item' => &$item, 'formId' => $formId)));
						
                        $optionField->items[] = $item;
                    }
                }
            }
        }

        return $optionFields;
	}
	
	public function getCondition()
	{
		$cid = JFactory::getApplication()->input->getInt('cid');
		$row = JTable::getInstance('RSForm_Conditions', 'Table');
		$row->load($cid);
		
		return $row;
	}
	
	public function getLang()
	{
		return RSFormProHelper::getCurrentLanguage($this->getFormId());
	}
	
	public function save()
	{
		$post		= JFactory::getApplication()->input->post->getArray(array(), null, 'raw');
		$condition 	= JTable::getInstance('RSForm_Conditions', 'Table');

		try
        {
            $condition->save($post);
            return $condition->id;
        }
        catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			return false;
		}
	}
	
	public function remove()
	{
		$condition = JTable::getInstance('RSForm_Conditions', 'Table');
		$cid	   = JFactory::getApplication()->input->getInt('cid');

		try
		{
			return $condition->delete($cid);
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			return false;
		}
	}
}