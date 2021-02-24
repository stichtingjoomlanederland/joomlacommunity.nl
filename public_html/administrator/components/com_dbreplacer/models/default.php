<?php
/**
 * @package         DB Replacer
 * @version         6.3.8PRO
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2020 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\MVC\Model\BaseDatabaseModel as JModel;
use RegularLabs\Library\RegEx as RL_RegEx;

/**
 * DB Replacer Default Model
 */
class DBReplacerModelDefault extends JModel
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Method to replace in the database
	 */
	public function replace(&$params)
	{
		if (empty($params->columns))
		{
			return;
		}

		$where = self::getWhereClause(
			$params->search,
			$params->columns,
			$params->case,
			$params->regex
		);

		$custom_where = self::prepareCustomWhereClause($params->where, $params->table);

		if ($custom_where)
		{
			if ($where)
			{
				$where .= ' AND ';
			}

			$where .= $custom_where;
		}

		$query = 'SHOW COLUMNS FROM `' . $params->table . '`';
		$this->_db->setQuery($query);
		$all_columns = $this->_db->loadObjectList();

		$index_columns = [];

		foreach ($all_columns as $column)
		{
			if ($column->Key != 'PRI')
			{
				continue;
			}

			$index_columns[] = $column->Field;
		}

		if (empty($index_columns))
		{
			foreach ($all_columns as $column)
			{
				if (strpos($column->Type, 'float') !== 0)
				{
					continue;
				}

				$index_columns[] = $column->Field;
			}
		}

		$select_columns = array_merge($index_columns, $params->columns);

		$query = 'SELECT `' . implode('`,`', $select_columns) . '`'
			. ' FROM `' . $params->table . '`'
			. ($where ? ' WHERE ' . $where : '')
			. ' LIMIT ' . (int) $params->max;
		$this->_db->setQuery($query);

		$rows = $this->_db->loadObjectList();

		$search  = str_replace('||space||', ' ', $params->search);
		$replace = str_replace('||space||', ' ', $params->replace);

		$count = 0;
		foreach ($rows as $row)
		{
			$set   = [];
			$where = [];

			foreach ($row as $key => $val)
			{
				if (in_array($key, $index_columns) && $val != '' && $val !== null && $val != '0000-00-00')
				{
					$where[] = $this->_db->quoteName(trim($key)) . ' = ' . $this->_db->quote($val);
				}

				if ( ! in_array($key, $params->columns))
				{
					continue;
				}

				if ($search == 'NULL')
				{
					if ($val == '' || $val === null || $val == '0000-00-00')
					{
						$set[] = $this->_db->quoteName(trim($key)) . ' = ' . $this->_db->quote($replace);
					}
					continue;
				}

				if ($search == '*')
				{
					$set[] = $this->_db->quoteName(trim($key)) . ' = ' . $this->_db->quote($replace);
					continue;
				}

				$dbs = $search;
				if ( ! $params->regex)
				{
					$dbs = RL_RegEx::quote($dbs);
					// replace multiple whitespace (with at least one enter) with regex whitespace match
					$dbs = RL_RegEx::replace('\s*\n\s*', '\s*', $dbs);
					$dbs = str_replace('\[[:space:]]', '\s*', $dbs);
				}

				$options = 's';
				if ( ! $params->case)
				{
					$options .= 'i';
				}
				if ($params->regex && $params->utf8)
				{
					$options .= 'u';
				}

				if ( ! @RL_RegEx::match($dbs, $val, $matches, $options))
				{
					continue;
				}

				$set[] = $this->_db->quoteName(trim($key)) . ' = ' . $this->_db->quote(RL_RegEx::replace($dbs, $replace, $val, $options));
			}

			// No specific indexed columns found, so add search columns to where
			if (empty($where))
			{
				foreach ($row as $key => $val)
				{
					$where[] = $this->_db->quoteName(trim($key)) . ' = ' . $this->_db->quote($val);
				}
			}

			if (empty($set) || empty($where))
			{
				continue;
			}

			$where = ' WHERE (' . implode(' AND ', $where) . ')';
			if ($custom_where)
			{

				$where .= ' AND (' . $custom_where . ')';
			}

			$query = 'UPDATE `' . $params->table . '`'
				. ' SET ' . implode(', ', $set)
				. $where
				. ' LIMIT 1';
			$this->_db->setQuery($query);

			if ( ! $this->_db->execute())
			{
				JFactory::getApplication()->enqueueMessage(JText::_('???'), 'error');
				continue;
			}

			$count++;
		}

		if ( ! $count)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('DBR_NO_ROWS_UPDATED'), 'notice');

			return;
		}

		$table = trim(str_replace($this->_db->getPrefix(), '', $params->table));
		JEventDispatcher::getInstance()->trigger('onAfterDatabaseReplace', ['com_dbreplacer', $table]);

		JFactory::getApplication()->enqueueMessage(JText::sprintf('DBR_ROWS_UPDATED', $count), 'message');
	}

	public static function getWhereClause($search, $columns = [], $case = false, $regex = false)
	{
		if (empty($columns))
		{
			return false;
		}

		$s = str_replace('||space||', ' ', $search);

		if (empty($s))
		{
			return false;
		}

		$likes = [];

		switch ($s)
		{
			case 'NULL' :
				$likes[] = 'IS NULL';
				$likes[] = '= ""';
				break;

			case '*':
				//$likes[] = ' != \'-something it would never be!!!-\'';
				break;

			default:
				$dbs = $s;

				if ( ! $regex)
				{
					$dbs = RL_RegEx::quote($dbs);
					// replace multiple whitespace (with at least one enter) with regex whitespace match
					$dbs = RL_RegEx::replace('\s*\n\s*', '\s*', $dbs);
				}

				// escape slashes
				$dbs = str_replace('\\', '\\\\', $dbs);
				// escape single quotes
				$dbs = str_replace('\'', '\\\'', $dbs);
				// remove the lazy character: doesn't work in mysql
				$dbs = str_replace(['*?', '+?'], ['*', '+'], $dbs);
				// change \s to [[:space:]]
				$dbs = str_replace('\s', '[[:space:]]', $dbs);

				$likes[] = $case
					? 'RLIKE BINARY \'' . $dbs . '\''
					: 'RLIKE \'' . $dbs . '\'';
				break;
		}

		$db      = JFactory::getDbo();
		$columns = self::implodeParams($columns);

		$wheres = [];

		foreach ($columns as $column)
		{
			foreach ($likes as $like)
			{
				$wheres[] = $db->quoteName(trim($column)) . ' ' . $like;
			}
		}

		if (empty($wheres))
		{
			return false;
		}

		return '(' . implode(' OR ', $wheres) . ')';
	}

	public static function prepareCustomWhereClause($where, $table = '')
	{
		$where = trim(RL_Regex::replace('^\s*WHERE ', '', $where));

		if (empty($where))
		{
			return false;
		}

		$columns = self::getTableColumns($table);

		if (empty($columns))
		{
			return $where;
		}

		$columns = RL_RegEx::quote($columns);

		$regex = '(^| )' . $columns . '( +(?:=|\!|IS |IN |LIKE ))';
		RL_RegEx::matchAll($regex, $where, $matches);

		if (empty($matches))
		{
			return $where;
		}

		$db = JFactory::getDbo();

		foreach ($matches as $match)
		{
			$where = str_replace(
				$match[0],
				$match[1] . $db->quoteName($match[2]) . $match[3],
				$where
			);
		}

		return $where;
	}

	public static function getTableColumns($table)
	{
		if (RL_RegEx::match('[^a-z0-9-_\#]', trim($table)))
		{
			die('Invalid data found in URL!');
		}

		$db = JFactory::getDbo();

		$query = 'SHOW COLUMNS FROM `' . trim($table) . '`';
		$db->setQuery($query);

		return $db->loadColumn();
	}

	public static function implodeParams($params)
	{
		if (is_array($params))
		{
			return $params;
		}

		$params = explode(',', $params);
		$p      = [];

		foreach ($params as $param)
		{
			if (trim($param) != '')
			{
				$p[] = trim($param);
			}
		}

		return array_unique($p);
	}
}
