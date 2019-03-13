<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformModelConditions extends JModelLegacy
{
	public $_data 	= null;
	public $_total  = 0;
	public $_query  = '';
	public $_db 	= null;
	
	public function __construct()
	{
		parent::__construct();
		$this->_db = JFactory::getDbo();
	}
	
	public function getFormId()
	{
		static $formId;
		if (empty($formId)) {
            $formId = JFactory::getApplication()->input->getInt('formId');
        }
		
		return $formId;
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
            RSFORM_FIELD_RADIOGROUP
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
		
		$row->details = array();
		if ($row->id)
		{
		    $query = $this->_db->getQuery(true);
		    $query->select('*')
                ->from($this->_db->qn('#__rsform_condition_details'))
                ->where($this->_db->qn('condition_id') . ' = ' . $this->_db->q($row->id));
			$this->_db->setQuery($query);
			$row->details = $this->_db->loadObjectList();
		}
		
		return $row;
	}
	
	public function getLang()
	{
		return RSFormProHelper::getCurrentLanguage($this->getFormId());
	}
	
	public function save()
	{
		$post		= RSFormProHelper::getRawPost();
		$app        = JFactory::getApplication();
        $input		= $app->input;
		$condition 	= JTable::getInstance('RSForm_Conditions', 'Table');

		try
        {
            $condition->bind($post);
            $condition->store();

            $query = $this->_db->getQuery(true)
                ->delete($this->_db->qn('#__rsform_condition_details'))
                ->where($this->_db->qn('condition_id') . ' = ' . $this->_db->q($condition->id));
            $this->_db->setQuery($query)
                ->execute();

            $component_ids 	= $input->get('detail_component_id', array(), 'array');
            $operators 		= $input->get('operator', array(), 'array');
            $values 		= $input->get('value', array(), 'raw');

            for ($i=0; $i<count($component_ids); $i++)
            {
                $detail = JTable::getInstance('RSForm_Condition_Details', 'Table');
                $detail->condition_id 	= $condition->id;
                $detail->component_id 	= $component_ids[$i];
                $detail->operator 		= $operators[$i];
                $detail->value 			= $values[$i];
                $detail->store();
            }

            return $condition->id;
        }
        catch (Exception $e)
		{
            $app->enqueueMessage($e->getMessage(), 'error');
			return false;
		}
	}
	
	public function remove()
	{
		$cid = JFactory::getApplication()->input->getInt('cid');

		$query = $this->_db->getQuery(true);

		$query->delete($this->_db->qn('#__rsform_conditions'))
            ->where($this->_db->qn('id') . ' = ' . $this->_db->q($cid));
		$this->_db->setQuery($query)
            ->execute();

        $query->clear()
            ->delete($this->_db->qn('#__rsform_condition_details'))
            ->where($this->_db->qn('condition_id') . ' = ' . $this->_db->q($cid));
        $this->_db->setQuery($query)
            ->execute();
	}
}