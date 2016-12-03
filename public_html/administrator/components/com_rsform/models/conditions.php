<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSFormModelConditions extends JModelLegacy
{
	var $_data 	= null;
	var $_total = 0;
	var $_query = '';
	var $_db 	= null;
	
	function __construct()
	{
		parent::__construct();
		$this->_db = JFactory::getDBO();
	}
	
	function getFormId()
	{
		static $formId;
		if (empty($formId))
			$formId = JFactory::getApplication()->input->getInt('formId');
		
		return $formId;
	}
	
	function getAllFields()
	{
		$formId = $this->getFormId();
		
		$this->_db->setQuery("SELECT p.PropertyValue, p.ComponentId FROM #__rsform_properties p LEFT JOIN #__rsform_components c ON (p.ComponentId = c.ComponentId) WHERE c.FormId='".$formId."' AND p.PropertyName='NAME' ORDER BY c.Order");
		
		return $this->_db->loadObjectList();
	}
	
	function getOptionFields()
	{
		$app 	= JFactory::getApplication();
		$types 	= array(3, 4, 5, 22);
		$formId = $this->getFormId();
		
		$app->triggerEvent('rsfp_bk_onCreateConditionOptionFields',array(array('types' => &$types, 'formId' => $formId)));
		
		JArrayHelper::toInteger($types);
		
		$this->_db->setQuery("SELECT p.PropertyValue, p.PropertyName, p.ComponentId, c.ComponentTypeId FROM #__rsform_properties p LEFT JOIN #__rsform_components c ON (p.ComponentId = c.ComponentId) WHERE c.FormId='".$formId."' AND (p.PropertyName='DEFAULTVALUE' OR p.PropertyName='ITEMS') AND c.ComponentTypeId IN (".implode(',', $types).") ORDER BY c.Order");
		$results = $this->_db->loadObjectList();
		
		$lang 		  = RSFormProHelper::getCurrentLanguage($formId);
		$translations = RSFormProHelper::getTranslations('properties', $formId, $lang);
		
		foreach ($results as $i => $result)
		{
			$reference_id = $result->ComponentId.'.'.$result->PropertyName;
			if (isset($translations[$reference_id]))
				$result->PropertyValue = $translations[$reference_id];
			
			$result->PropertyValue = RSFormProHelper::isCode($result->PropertyValue);
			$result->PropertyValue = preg_replace('#\[p(.*?)\]#is','',$result->PropertyValue);
			$result->PropertyValue = str_replace(array("\r\n", "\r"), "\n", $result->PropertyValue);
			$result->PropertyValue = str_replace(array('[c]', '[g]'), '', $result->PropertyValue);
			$result->PropertyValue = explode("\n", $result->PropertyValue);
			
			foreach ($result->PropertyValue as $k => $v)
			{
				$v = explode('|', $v, 2);
				// paypal ?
				if ($result->ComponentTypeId == 22)
				{
					$v[0] = $v[1];
				}
				else
				{
					if (!isset($v[1]))
						$v[1] = $v[0];
				}
				
				$result->PropertyValue[$k] = $v;
			}
			
			$results[$i] = $result;
		}
		
		return $results;
	}
	
	function getCondition()
	{
		$cid = JFactory::getApplication()->input->getInt('cid');
		$row = JTable::getInstance('RSForm_Conditions', 'Table');
		$row->load($cid);
		
		$row->details = array();
		if ($row->id)
		{
			$this->_db->setQuery("SELECT * FROM #__rsform_condition_details WHERE condition_id='".(int) $row->id."'");
			$row->details = $this->_db->loadObjectList();
		}
		
		return $row;
	}
	
	function getLang()
	{
		$formId = $this->getFormId();
		
		return RSFormProHelper::getCurrentLanguage($formId);
	}
	
	function save()
	{
		$post		= JRequest::get('post', JREQUEST_ALLOWRAW);
		$condition 	= JTable::getInstance('RSForm_Conditions', 'Table');
		
		if (!$condition->bind($post))
		{
			JError::raiseWarning(500, $condition->getError());
			return false;
		}
		
		if ($condition->store())
		{
			$this->_db->setQuery("DELETE FROM #__rsform_condition_details WHERE condition_id='".(int) $condition->id."'");
			$this->_db->execute();
			
			$component_ids 	= JRequest::getVar('detail_component_id');
			$operators 		= JRequest::getVar('operator');
			$values 		= JRequest::getVar('value', null, 'default', 'none', JREQUEST_ALLOWRAW);
			
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
		else 
		{
			JError::raiseWarning(500, $condition->getError());
			return false;
		}
	}
	
	function remove()
	{
		$cid = JFactory::getApplication()->input->getInt('cid');
		
		$this->_db->setQuery("DELETE FROM #__rsform_conditions WHERE id='".$cid."'");
		$this->_db->execute();
		$this->_db->setQuery("DELETE FROM #__rsform_condition_details WHERE condition_id='".$cid."'");
		$this->_db->execute();
	}
}