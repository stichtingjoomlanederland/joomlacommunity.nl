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

use DBReplacerModelDefault as Model;
use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\HTML\HTMLHelper as JHtml;
use Joomla\CMS\Language\Text as JText;
use RegularLabs\Library\Language as RL_Language;
use RegularLabs\Library\Parameters as RL_Parameters;
use RegularLabs\Library\RegEx as RL_RegEx;

if (JFactory::getApplication()->isClient('site'))
{
	die();
}

require_once __DIR__ . '/models/default.php';

echo (new DBReplacer)->render();
die;

class DBReplacer
{
	private $max_trim_length = 200;
	private $max_trim_lines  = 3;

	public function render()
	{
		$this->config = RL_Parameters::getInstance()->getComponentParams('com_dbreplacer');

		$field  = JFactory::getApplication()->input->get('field', 'table');
		$params = JFactory::getApplication()->input->getBase64('params');

		$params = str_replace(
			['[-CHAR-LT-]', '[-CHAR-GT-]'],
			['<', '>'],
			urldecode(base64_decode($params))
		);

		$params = json_decode($params);
		if (is_null($params))
		{
			$params = (object) [];
		}

		$db = JFactory::getDbo();
		if (empty($params->columns) && $params->table && $params->table == trim(str_replace('#__', $db->getPrefix(), $this->config->default_table)))
		{
			$params->columns = explode(',', $this->config->default_columns);
		}

		$this->params = $params;

		echo '<script>'
			. ' jQuery(document).ready(function() {'
			. 'RLDBReplacer.createTrimmedTogglers();'
			. '});'
			. '</script>';

		switch ($field)
		{
			case 'rows':
				return $this->renderRows();

			case 'columns':
			default:
				return $this->renderColumns();
		}
	}

	private function renderColumns()
	{
		$table    = $this->params->table;
		$selected = Model::implodeParams($this->params->columns);

		$options = [];
		if ($table)
		{
			$columns = $this->getTableColumns();
			foreach ($columns as $col)
			{
				$options[] = JHtml::_('select.option', $col, $col, 'value', 'text', 0);
			}
		}

		$html = '<strong>' . $this->params->table . '</strong><br>';
		$html .= JHtml::_('select.genericlist', $options, 'columns[]', 'multiple="multiple" size="20"', 'value', 'text', $selected, 'dbr-columns');

		return $html;
	}

	private function renderRows()
	{
		// Load plugin language

		RL_Language::load('com_dbreplacer');

		$max = (int) $this->config->max_rows;

		if ( ! $this->params->table)
		{
			return '';
		}

		$seach_columns = Model::implodeParams($this->params->columns);

		$rows = $this->getRows($max);

		if (is_null($rows))
		{
			return $this->getMessage(JText::_('DBR_INVALID_QUERY'), 'error');
		}

		if (empty($rows))
		{
			return $this->getMessage(JText::_('DBR_ROW_COUNT_NONE'));
		}

		$columns = $this->getTableColumns();

		$html = [];

		if ($this->params->search)
		{
			if (count($rows) > $max - 1)
			{
				$html[] = $this->getMessage(JText::sprintf('DBR_MAXIMUM_ROW_COUNT_REACHED', $max), 'warning');
			}
			else
			{
				$html[] = $this->getMessage(JText::sprintf('DBR_ROW_COUNT', count($rows)));
			}
		}

		$html[] = '<h4>Table: ' . $this->params->table . '</h4>';
		$html[] = '<p><a class="btn btn-default" onclick="RLDBReplacer.toggleInactiveColumns();">' . JText::_('DBR_TOGGLE_INACTIVE_COLUMNS') . '</a></p>';

		$html[] = '<table class="table table-striped" id="dbr_results">';
		$html[] = '<thead><tr>';
		foreach ($columns as $column)
		{
			$class = [];
			if ( ! in_array($column, $seach_columns))
			{
				$class[] = 'ghosted';
			}

			if ($column == 'id')
			{
				$class[] = 'is_id';
			}

			$html[] = '<th class="' . implode(' ', $class) . '">' . $column . '</th>';
		}
		$html[] = '</tr></thead>';
		if ($rows && ! empty($rows))
		{
			$html[] = '<tbody>';
			$html[] = $this->getTableRow($rows, $columns);
			$html[] = '</tbody>';
		}
		$html[] = '</table>';

		return implode("\n", $html);
	}

	private function getMessage($text = '', $type = 'info')
	{
		return '<div class="alert alert-' . $type . '">' . $text . '</div>';
	}

	private function getTableRow($rows, $cols)
	{
		foreach ($rows as $row)
		{
			$html[] = '<tr>';
			foreach ($cols as $col)
			{
				list($val, $class) = $this->getCellData($row, $col);
				$val    = nl2br($val);
				$html[] = '<td class="db_value ' . $class . '">'
					. '<div class="cell_content">'
					. $val
					. '</div>'
					. '</td>';
			}
			$html[] = '</tr>';
		}

		return implode('', $html);
	}

	private function getCellData($row, $col)
	{
		$columns = Model::implodeParams($this->params->columns);

		$class = '';
		$value = $row->{$col};

		if ( ! in_array($col, $columns))
		{
			$class = ['ghosted'];

			if ($col == 'id')
			{
				$class[] = 'is_id';
			}

			$class = implode(' ', $class);

			if ($value == '' || $value === 0 || $value === null || $value == '0000-00-00')
			{
				if ($value === null)
				{
					$value = 'NULL';
				}
				if ($value === '')
				{
					$value = '&nbsp;';
				}
				$value = '<span class="null">' . $value . '</span>';

				return [$value, $class];
			}

			$value = $this->rtrim($value);
			$value = $this->replacePlaceholders($value);

			return [$value, $class];
		}

		$search  = str_replace('||space||', ' ', $this->params->search);
		$replace = str_replace('||space||', ' ', $this->params->replace);

		if ($search == 'NULL')
		{
			if ($value == '' || $value == 0 || $value === null || $value == '0000-00-00')
			{
				if ($value === null)
				{
					$value = 'NULL';
				}
				if ($value === '')
				{
					$value = '&nbsp;';
				}
				$value = '<span class="search_string"><span class="null">' . $value . '</span></span><span class="replace_string">' . $replace . '</span>';

				return [$value, $class];
			}

			$value = $this->ltrim($value);
			$value = $this->replacePlaceholders($value);

			return [$value, $class];
		}

		if ($search == '*')
		{
			$value_class = 'search_string';
			if (strlen($value) > 50)
			{
				$value       = '*';
				$value_class .= ' no-strikethrough';
			}

			$value = '<span class="' . $value_class . '"><span class="null">' . $value . '</span></span><span class="replace_string">' . $replace . '</span>';

			return [$value, $class];
		}

		if ($value === null)
		{
			$value = '<span class="null">NULL</span>';

			return [$value, $class];
		}

		$match   = 0;
		$options = '';

		if ($search != '')
		{
			if ( ! $this->params->regex)
			{
				$search = RL_RegEx::quote($search);
				// replace multiple whitespace (with at least one enter) with regex whitespace match
				$search = RL_RegEx::replace('\s*\n\s*', '\s*', $search);
			}
			$options = 's';
			if ( ! $this->params->case)
			{
				$options .= 'i';
			}
			if ($this->params->regex && $this->params->utf8)
			{
				$options .= 'u';
			}

			$match = @RL_RegEx::match($search, $value, $matches, $options);
		}

		$value = $this->prepareResultString($value, $match, $search, $replace, $options);

		if ($value == '0000-00-00')
		{
			$value = '<span class="null">' . $value . '</span>';
		}

		$class = $match ? 'has_search' : 0;

		return [$value, $class];
	}

	private function prepareResultString($value, $match, $search, $replace, $options)
	{
		// If there is no search, show entire cell content
		if ($search == '')
		{
			$value = $this->replacePlaceholders($value);

			return $value;
		}

		// If there is no match, do a simple rtrim
		if ( ! $match)
		{
			$value = $this->rtrim($value);
			$value = $this->replacePlaceholders($value);

			return $value;
		}

		$s1 = '|' . md5('<SEARCH TAG>') . '|';
		$s2 = '|' . md5('</SEARCH TAG>') . '|';
		$r1 = '|' . md5('<REPLACE TAG>') . '|';
		$r2 = '|' . md5('</REPLACE TAG>') . '|';

		$split = '[:DBR::SPLIT:]';

		$value = RL_RegEx::replace($search, $split . $s1 . '\0' . $s2 . $r1 . $replace . $r2 . $split, $value, $options);
		$parts = explode($split, $value);

		foreach ($parts as $i => &$part)
		{
			if ($i == 0)
			{
				$part = $this->ltrim($part);
				continue;
			}

			if ($i == count($parts) - 1)
			{
				$part = $this->rtrim($part);
				continue;
			}

			if ($i % 2 == 0)
			{
				$part = $this->trim($part);
			}
		}

		$value = implode('', $parts);

		$value = str_replace($s1, '[:DBR:START-SEARCH:]', str_replace($s2, '[:DBR:END-SEARCH:]', $value));
		$value = str_replace($r1, '[:DBR:START-REPLACE:]', str_replace($r2, '[:DBR:END-REPLACE:]', $value));

		$value = $this->replacePlaceholders($value);

		return $value;
	}

	private function replacePlaceholders($string)
	{
		$string = htmlentities($string, ENT_COMPAT, 'utf-8');

		// Replace all spaces with non-breaking spaces
		$string = str_replace(' ', '&nbsp;', $string);
		// Replace non-breaking space after 80 characters with a normal space to prevent really long lines
		$string = RL_RegEx::replace('(([^\n ]){80})&nbsp;', '\1 ', $string);

		$string = str_replace(['[:DBR:START-SEARCH:]', '[:DBR:END-SEARCH:]'], ['<span class="search_string">', '</span>'], $string);
		$string = str_replace(['[:DBR:START-REPLACE:]', '[:DBR:END-REPLACE:]'], ['<span class="replace_string">', '</span>'], $string);
		$string = str_replace('[:DBR:ELIPSIS:]', '<span class="ellipses">&hellip;</span>', $string);

		$string = RL_RegEx::replace(
			'\[:DBR:START-TRIMMED:([a-z0-9-]+):\](.*?)\[:DBR:END-TRIMMED:\]',
			'<span class="trimmed" id="trimmed-\1">\2</span>',
			$string
		);

		return $string;
	}

	private function isTooLong($string, $max_length = 0, $max_lines = 0)
	{
		$max_length = $max_length ?: $this->max_trim_length;
		$max_lines  = $max_lines ?: $this->max_trim_lines;

		// return TRUE if string is longer than 110% of max
		if (strlen($string) > ($max_length * 1.1))
		{
			return true;
		}

		// return TRUE if string has more lines than the max
		if (RL_RegEx::match('(.*?\n){' . $max_lines . '}', $string))
		{
			return true;
		}

		return false;
	}

	private function ltrim($string, $max_length = 0, $max_lines = 0)
	{
		$max_length = $max_length ?: $this->max_trim_length;
		$max_lines  = $max_lines ?: $this->max_trim_lines;

		if ( ! $this->isTooLong($string, $max_length, $max_lines))
		{
			return $string;
		}

		$parts = [substr($string, 0, strlen($string) - $max_length), substr($string, -$max_length)];
		$parts = $this->ltrimLines($parts, $max_lines);

		return '[:DBR:START-TRIMMED:' . uniqid() . ':]' . $parts[0] . '[:DBR:END-TRIMMED:]'
			. $parts[1];
	}

	private function rtrim($string, $max_length = 0, $max_lines = 0)
	{
		$max_length = $max_length ?: $this->max_trim_length;
		$max_lines  = $max_lines ?: $this->max_trim_lines;

		if ( ! $this->isTooLong($string, $max_length, $max_lines))
		{
			return $string;
		}

		$parts = [substr($string, 0, $max_length), substr($string, $max_length)];
		$parts = $this->rtrimLines($parts, $max_lines);

		return $parts[0]
			. '[:DBR:START-TRIMMED:' . uniqid() . ':]' . $parts[1] . '[:DBR:END-TRIMMED:]';
	}

	private function trim($string, $max_length = 0, $max_lines = 0)
	{
		$max_length = $max_length ?: $this->max_trim_length;
		$max_lines  = $max_lines ?: $this->max_trim_lines;

		if ( ! $this->isTooLong($string, $max_length * 2, $max_lines * 2))
		{
			return $string;
		}

		// First right trim the whole string
		$start_parts = [substr($string, 0, $max_length), substr($string, $max_length)];
		$start_parts = $this->rtrimLines($start_parts, $max_lines);

		// Now left trim the last part
		$string    = $start_parts[1];
		$end_parts = [substr($string, 0, strlen($string) - $max_length), substr($string, -$max_length)];
		$end_parts = $this->ltrimLines($end_parts, $max_lines);

		return $start_parts[0]
			. '[:DBR:START-TRIMMED:' . uniqid() . ':]' . $end_parts[0] . '[:DBR:END-TRIMMED:]'
			. $end_parts[1];
	}

	private function ltrimLines($parts, $max_lines = 3)
	{
		if ( ! RL_RegEx::match('^(.*\n)((?:[^\n]*\n[^\n]*){' . ($max_lines - 1) . '})$', $parts[1], $matches))
		{
			return $parts;
		}

		$parts[0] .= $matches[1];
		$parts[1] = $matches[2];

		return $parts;
	}

	private function rtrimLines($parts, $max_lines = 3)
	{
		if ( ! RL_RegEx::match('^((?:[^\n]*\n[^\n]*){' . ($max_lines - 1) . '})(\n.*)$', $parts[0], $matches))
		{
			return $parts;
		}

		$parts[0] = $matches[1];
		$parts[1] = $matches[2] . $parts[1];

		return $parts;
	}

	private function getRows($max = 100)
	{
		if (RL_RegEx::match('[^a-z0-9-_\#]', $this->params->table))
		{
			die('Invalid data found in URL!');
		}

		$db    = JFactory::getDbo();
		$table = $this->params->table;

		$columns = $this->getTableColumns();
		array_walk($columns, function (&$column, $key, $db) {
			$column = $db->quoteName($column);
		}, $db);

		$query = $db->getQuery(true)
			->select($columns)
			->from($db->quoteName(trim($table)));

		$this->addWhereClause($query);

		$this->addCustomWhereClause($query);

		$db->setQuery($query, 0, $max);

		return $db->loadObjectList();
	}

	private function getTableColumns()
	{
		return Model::getTableColumns($this->params->table);
	}

	private function addWhereClause(&$query)
	{
		$where = Model::getWhereClause(
			$this->params->search,
			$this->params->columns,
			$this->params->case,
			$this->params->regex
		);

		if (empty($where))
		{
			return;
		}

		$query->where($where);
	}

	private function addCustomWhereClause(&$query)
	{
		$where = Model::prepareCustomWhereClause($this->params->where, $this->params->table);

		if (empty($where))
		{
			return;
		}

		$query->where($where);
	}
}
