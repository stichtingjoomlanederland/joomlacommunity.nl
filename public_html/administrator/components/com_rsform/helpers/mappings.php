<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

define('RSFP_MAPPINGS_INSERT', 0);
define('RSFP_MAPPINGS_UPDATE', 1);
define('RSFP_MAPPINGS_DELETE', 2);
define('RSFP_MAPPINGS_REPLACE', 3);

class RSFormProMappings
{
	public static function getMappingQuery($row) {
		static $model;
		if (!$model) {
			JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_rsform/models');
			$model = JModelLegacy::getInstance('mappings', 'RsformModel');
		}
		
		$config = array(
			'connection' => $row->connection,
			'host' 		 => $row->host,
			'driver'	 => $row->driver,
			'port' 		 => $row->port,
			'username' 	 => $row->username,
			'password' 	 => $row->password,
			'database'   => $row->database
		);
		
		$db 	= $model->getMappingDbo($config);
		$query 	= $db->getQuery(true);
		
		// Get the fields
		$data = @unserialize($row->data);
		if ($data === false) {
			$data = array();
		}
		
		// Get the WHERE fields
		$wheredata = @unserialize($row->wheredata);
		if ($wheredata === false) {
			$wheredata = array();
		}
		
		// Get the operators
		$extra = @unserialize($row->extra);
		if ($extra === false) {
			$extra = array();
		}
		
		// Get the and / or operators
		$andor = @unserialize($row->andor);
		if ($andor === false) {
			$andor = array();
		}
		
		// Create the WHERE cause
		if (!empty($wheredata)) {
			$where 	= '';
			$i 		= 0;
			foreach ($wheredata as $column => $field) {
				$andorop = isset($andor[$column]) ? $andor[$column] : 0;
				$andorop = $andorop ? 'OR' : 'AND';
				
				$operator = isset($extra[$column]) ? $extra[$column] : '=';
				$where .= $i ? " ".$andorop." " : '';
				
				if ($operator == '%..%') {
					$where .= ' '.$db->qn($column).' LIKE '.$db->q('%'.$db->escape($field, true).'%', false);
				} elseif ($operator == '%..') {
					$where .= ' '.$db->qn($column).' LIKE '.$db->q('%'.$db->escape($field, true), false);
				} elseif ($operator == '..%') {
					$where .= ' '.$db->qn($column).' LIKE '.$db->q($db->escape($field, true).'%', false);
				} else {
					$where .= ' '.$db->qn($column).' '.$operator.' '.$db->q($field, true);
				}
				
				$i++;
			}
			
			if ($where) {
				$query->where($where);
			}
		}
		
		// Create the SET clause
		if (!empty($data)) {
			$fields = array();
			$values = array();
			
			foreach ($data as $column => $field) {
				$query->set($db->qn($column).'='.$db->q($field));
				
				$fields[] = $db->qn($column);
				$values[] = $db->q($field);
			}
		}
		
		// Prefix the database name
		$table = $row->table;
		
		switch ($row->method) {
			case RSFP_MAPPINGS_INSERT:
				$query->clear();
				$query->insert($db->qn($table));

				if (!empty($fields))
                {
                    $query->columns($fields);
                }
                if (!empty($values))
                {
                    $query->values(implode(',', $values));
                }
			break;
			case RSFP_MAPPINGS_REPLACE:
				$query = 'REPLACE INTO '.$db->qn($table).' SET ';
				$set = array();
				// Create the SET clause
				if (!empty($data)) {
					foreach ($data as $column => $field) {
						$set[] = $db->qn($column).'='.$db->q($field);
					}
				}

				if ($set) {
					$query .= implode(', ', $set);
				}
			break;
			
			case RSFP_MAPPINGS_UPDATE:
				$query->update($db->qn($table));
			break;
			
			case RSFP_MAPPINGS_DELETE:
				$query->delete($db->qn($table));
			break;
		}
		
		return $query;
	}
	
	public static function mappingsColumns($config, $method, $row = null)
	{
		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_rsform/models');
		$model = JModelLegacy::getInstance('mappings', 'RsformModel');
		
		$columns = $model->getColumns($config);
		
		$data = @unserialize($row->data);
		if ($data === false) $data = array();
		
		$where = @unserialize($row->wheredata);
		if ($where === false) $where = array();
		
		$extra = @unserialize($row->extra);
		if ($extra === false) $extra = array();
		
		$andor = @unserialize($row->andor);
		if ($andor === false) $andor = array();
		
		$operators = array(
			JHtml::_('select.option', '=', JText::_( 'RSFP_OPERATOR_EQUALS' ) ),
			JHtml::_('select.option', '!=', JText::_( 'RSFP_OPERATOR_NOTEQUAL' ) ),
			JHtml::_('select.option', '>', JText::_( 'RSFP_OPERATOR_GREATER_THAN' ) ),
			JHtml::_('select.option', '<', JText::_( 'RSFP_OPERATOR_LESS_THAN' ) ),
			JHtml::_('select.option', '>=', JText::_( 'RSFP_OPERATOR_EQUALS_GREATHER_THAN' ) ),
			JHtml::_('select.option', '<=', JText::_( 'RSFP_OPERATOR_EQUALS_LESS_THAN' ) ),
			JHtml::_('select.option', '%..%', JText::_( 'RSFP_OPERATOR_LIKE' ) ),
			JHtml::_('select.option', '%..', JText::_( 'RSFP_OPERATOR_STARTS_WITH' ) ),
			JHtml::_('select.option', '..%', JText::_( 'RSFP_OPERATOR_ENDS_WITH' ) ),
		);
		
		$html = '';
		
		$html .= ($method == 'set') ? JText::_('RSFP_SET').'<hr />' : JText::_('RSFP_WHERE').'<hr />';
		$html .= '<table class="admintable table">';
		
		if (!empty($columns))
		{
			$html .= '<tr>';
			$html .= '<td>&nbsp;</td>';
			if ($method == 'where')
			{
				$html .= '<td>&nbsp;</td>';
				$html .= '<td>&nbsp;</td>';
			}
			$html .= '<td align="right"><button class="btn btn-primary" type="submit">'.JText::_('JSAVE').'</button></td>';
			$html .= '</tr>';
		}
		
		if (!empty($columns)) {
			$i = 0;
			foreach ($columns as $column => $type) {
				if ($method == 'set') {
					$value = isset($data[$column]) ? $data[$column] : '';
					$name  = 'f_'.$column;
				} else {
					$value	= isset($where[$column]) ? $where[$column] : '';
					$name	= 'w_'.$column;
					$op		= isset($extra[$column]) ? $extra[$column] : '=';
					$op2	= isset($andor[$column]) ? $andor[$column] : 0;
				}
				
				$html .= '<tr>';
				$html .= '<td width="80" nowrap="nowrap" align="right" class="key">'.$column.' ('.$type.')</td>';
				if ($method == 'where') {
					$html .= '<td>'.JHtml::_('select.genericlist',  $operators, 'o_'.$column, 'class="inputbox"', 'value', 'text',$op).'</td>';
				}
				if (strpos($type, 'text') !== false) {
					$html .= '<td><textarea class="rs_textarea" style="width:300px; height: 200px;" id="'.RSFormProHelper::htmlEscape($name).'" name="'.RSFormProHelper::htmlEscape($name).'">'.RSFormProHelper::htmlEscape($value).'</textarea></td>';
				} else {
					$html .= '<td><input type="text" class="rs_inp rs_80" data-delimiter=" "  data-placeholders="display" size="35" value="'.RSFormProHelper::htmlEscape($value).'" id="'.RSFormProHelper::htmlEscape($name).'" name="'.RSFormProHelper::htmlEscape($name).'"></td>';
				}
				if ($method == 'where' && $i) {
					$html .= '<td>'.JHtml::_('select.booleanlist', 'c_'.$column, 'class="inputbox"', $op2, 'RSFP_OR', 'RSFP_AND').'</td>';
				}
				$html .= '</tr>';
				$i++;
			}
		}
		
		if (!empty($columns))
		{
			$html .= '<tr>';
			$html .= '<td>&nbsp;</td>';
			if ($method == 'where')
			{
				$html .= '<td>&nbsp;</td>';
				$html .= '<td>&nbsp;</td>';
			}
			$html .= '<td align="right"><button class="btn btn-primary" type="submit">'.JText::_('JSAVE').'</button></td>';
			$html .= '</tr>';
		}
		
		$html .= '</table>';
		
		return $html;
	}
}