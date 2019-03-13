<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformModelMappings extends JModelLegacy
{	
	public function getMapping() {
		$row = JTable::getInstance('RSForm_Mappings', 'Table');
		$row->load(JFactory::getApplication()->input->getInt('cid'));
		
		return $row;
	}
	
	public function save() {
		$post 		= RSFormProHelper::getRawPost();
		$row 		= JTable::getInstance('RSForm_Mappings', 'Table');
		$db	 		= JFactory::getDbo();
		
		if (!$row->bind($post)) {
			JError::raiseWarning(500, $row->getError());
			return false;
		}
		
		if (empty($row->id)) {
			$row->ordering = $row->getNextOrder($db->qn('formId').'='.$db->q($row->formId));
		}
		
		$data = $where = $extra = $andor = array();
		
		if (!empty($post)) {
			foreach ($post as $key => $value) {
				if (!strlen($value)) {
					continue;
				}
				
				if (substr($key,0,2) == 'f_') {
					$datakey 		= substr($key, 2);
					$data[$datakey] = $value;
				} elseif (substr($key,0,2) == 'w_') {
					$wherekey 			= substr($key, 2);
					$where[$wherekey] 	= $value;
					$extra[$wherekey] 	= isset($post['o_'.$wherekey]) ? $post['o_'.$wherekey] : '=';
					$andor[$wherekey] 	= isset($post['c_'.$wherekey]) ? $post['c_'.$wherekey] : 0;
				}
			}
		}
		
		if (($row->method == 0 || $row->method == 1 || $row->method == 3) && empty($data)) {
			return false;
		}
		
		if ($row->method == 2 && empty($where)) {
			return false;
		}
		
		$row->data 		= serialize($data);
		$row->wheredata = serialize($where);
		$row->extra 	= serialize($extra);
		$row->andor 	= serialize($andor);
		
		if ($row->store()) {
			return $row;
		} else  {
			JError::raiseWarning(500, $row->getError());
			return false;
		}
	}
	
	public function remove() {
		$id 	= JFactory::getApplication()->input->getInt('mid');
		$db		= JFactory::getDbo();
		$row 	= JTable::getInstance('RSForm_Mappings', 'Table');
		
		$row->load($id);
		$formId = $row->formId;
		
		$row->delete($id);
		$row->reorder($db->qn('formId').'='.$db->q($formId));
	}
	
	public function getFields() {
		$db		= JFactory::getDbo();
		$query  = $db->getQuery(true);
		$formId = JFactory::getApplication()->input->getInt('formId');
		
		$query->select($db->qn('p.PropertyValue'))
			  ->from($db->qn('#__rsform_components', 'c'))
			  ->leftJoin($db->qn('#__rsform_properties', 'p').' ON ('.$db->qn('c.ComponentId').'='.$db->qn('p.ComponentId').')')
			  ->where($db->qn('c.FormId').'='.$db->q($formId))
			  ->where($db->qn('p.PropertyName').'='.$db->q('NAME'))
			  ->order($db->qn('c.Order'));
		
		return $db->setQuery($query)->loadColumn();
	}
	
	public function getQuickFields() {
		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/quickfields.php';
		return RSFormProQuickFields::getFieldNames();
	}
	
	// Get columns from a specific table
	public function getColumns($config) {
		$db 	= $this->getMappingDbo($config);
		$tables = $db->getTableList();
		$table 	= isset($config['table']) ? $config['table'] : '';
		
		if (empty($table) || !in_array($table,$tables)) {
			return false;
		} else {
			return $db->getTableColumns($table);
		}
	}
	
	// Get tables in database
	public function getTables($config) {
		$db = $this->getMappingDbo($config);
		
		return $db->getTableList();
	}
	
	// Get database connector object
	public function getMappingDbo($config) {
		if ($config['connection']) {
			if (!strlen($config['database'])) {
				throw new Exception(JText::_('RSFP_PLEASE_SELECT_A_DATABASE_FIRST'));
			}
			
			if (empty($config['driver'])) {
				throw new Exception(JText::_('RSFP_PLEASE_SELECT_A_DRIVER_FIRST'));
			}
			
			$config['user'] = $config['username'];

			$database = JDatabaseDriver::getInstance($config);
			$database->connect();
			
			return $database;
		} else {
			return JFactory::getDbo();
		}
	}
}