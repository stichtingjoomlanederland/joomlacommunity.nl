<?php
/**
 * @package         DB Replacer
 * @version         6.3.4PRO
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2019 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\HTML\HTMLHelper as JHtml;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\MVC\View\HtmlView as JView;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Parameters as RL_Parameters;

// Import VIEW object class
jimport('joomla.application.component.view');

/**
 * DB Replacer Default View
 */
class DBReplacerViewDefault extends JView
{
	/**
	 * Custom Constructor
	 */
	public function __construct($config = [])
	{
		/** set up global variable for sorting etc
		 * $context is used in VIEW abd in MODEL
		 **/
		global $context;
		$context = 'list.list.';

		parent::__construct($config);
	}

	/**
	 * Display the view
	 * take data from MODEL and put them into
	 * reference variables
	 */

	public function display($tpl = null)
	{
		$this->config = RL_Parameters::getInstance()->getComponentParams('com_dbreplacer');

		RL_Document::style('regularlabs/style.min.css');
		RL_Document::style('dbreplacer/style.min.css', '6.3.4.p');

		// Set document title
		JFactory::getDocument()->setTitle(JText::_('DB_REPLACER'));
		// Set ToolBar title
		JToolbarHelper::title(JText::_('DB_REPLACER'), 'dbreplacer icon-reglab');
		// Set toolbar items for the page

		if (JFactory::getUser()->authorise('core.admin', 'com_dbreplacer'))
		{
			JToolbarHelper::preferences('com_dbreplacer', '300');
		}

		$uri    = JFactory::getURI()->toString();
		$tables = $this->renderTables();
		$this->assignRef('request_url', $uri);
		$this->assignRef('tables', $tables);

		// call parent display
		parent::display($tpl);
	}

	private function renderTables()
	{
		$db = JFactory::getDbo();

		$ignore   = explode(',', trim($this->config->ignore_tables));
		$selected = JFactory::getApplication()->input->get('table');
		if (empty($selected))
		{
			$selected = trim(str_replace('#__', $db->getPrefix(), $this->config->default_table));
		}

		$query = 'SHOW TABLES';
		$db->setQuery($query);
		$tables = $db->loadColumn();

		if ( ! empty($ignore))
		{
			$ignores = [];
			foreach ($ignore as $table)
			{
				if (trim($table) != '')
				{
					$query = 'SHOW TABLES LIKE ' . $db->quote(trim($table) . '%');
					$db->setQuery($query);
					$ignores = array_merge($ignores, $db->loadColumn());
				}
			}
			if ( ! empty($ignores))
			{
				$tables = array_diff($tables, $ignores);
			}
		}

		$options = [];
		$prefix  = 0;
		$first   = 1;
		foreach ($tables as $table)
		{
			$name = $table;
			if (strpos($name, $db->getPrefix()) === 0)
			{
				if ( ! $prefix)
				{
					if ( ! $first)
					{
						$options[] = JHtml::_('select.option', '-', '&nbsp;', 'value', 'text', true);
					}
					$options[] = JHtml::_('select.option', '-', $db->getPrefix(), 'value', 'text', true);
					$prefix    = 1;
				}
				$name = substr($name, strlen($db->getPrefix()));
			}
			else
			{
				if ($prefix)
				{
					$options[] = JHtml::_('select.option', '-', '&nbsp;', 'value', 'text', true);
					$prefix    = 0;
				}
			}
			$options[] = JHtml::_('select.option', $table, $name, 'value', 'text', 0);
			$first     = 0;
		}

		return JHtml::_('select.genericlist', $options, 'table', 'size="20"', 'value', 'text', $selected, 'dbr-table');
	}
}
