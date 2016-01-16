<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.helper' );

class rseventsproModelPayments extends JModelList
{	
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'name', 'published'
			);
		}
		
		parent::__construct($config);
	}
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null) {
		$this->setState('filter.search', $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search'));
		
		// List state information.
		parent::populateState('name', 'asc');
	}
	
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery() {
		$db 	= JFactory::getDBO();
		$query 	= $db->getQuery(true);
		
		// Select fields
		$query->select('*');
		
		// Select from table
		$query->from($db->qn('#__rseventspro_payments'));
		
		// Filter by search in name or description
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->q('%'.$db->escape($search, true).'%');
			$query->where($db->qn('name').' LIKE '.$search.' ');
		}
		
		// Add the list ordering clause
		$listOrdering = $this->getState('list.ordering', 'name');
		$listDirn = $db->escape($this->getState('list.direction', 'asc'));
		$query->order($db->qn($listOrdering).' '.$listDirn);

		return $query;
	}
	
	/**
	 * Method to set the side bar.
	 */
	public function getSidebar() {
		if (rseventsproHelper::isJ3()) {
			return JHtmlSidebar::render();
		}
		
		return;
	}
	
	/**
	 * Method to set the filter bar.
	 */
	public function getFilterBar() {
		$options = array();
		$options['search'] = array(
			'label' => JText::_('JSEARCH_FILTER'),
			'value' => $this->getState('filter.search')
		);
		$options['listDirn']  = $this->getState('list.direction', 'asc');
		$options['listOrder'] = $this->getState('list.ordering', 'name');
		$options['sortFields'] = array(
			JHtml::_('select.option', 'id', JText::_('COM_RSEVENTSPRO_GLOBAL_SORT_ID')),
			JHtml::_('select.option', 'name', JText::_('COM_RSEVENTSPRO_PAYMENTS_SORT_NAME')),
			JHtml::_('select.option', 'published', JText::_('JSTATUS'))
		);
		
		$bar = new RSFilterBar($options);
		return $bar;
	}
	
	/**
	 * Method to get plugins.
	 */
	public function getPlugins() {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$return		= array();
		$container	= array();
		$plugins	= JPluginHelper::getPlugin('system');
		$lang		= JFactory::getLanguage();
		
		if (!empty($plugins)) {
			foreach ($plugins as $plugin) {
				if (substr($plugin->name,0,6) == 'rsepro' && $plugin->name != 'rsepropdf')
					$container[] = $plugin->name;
			}
		}
		
		if (!empty($container)) {
			foreach ($container as $element) {
				$tmp = new stdClass();
				
				$query->clear();
				$query->select($db->qn('extension_id'))
					->select($db->qn('name'))
					->select($db->qn('enabled'))
					->from($db->qn('#__extensions'))
					->where($db->qn('type').' = '.$db->q('plugin'))
					->where($db->qn('folder').' = '.$db->q('system'))
					->where($db->qn('client_id').' = '.$db->q('0'))
					->where($db->qn('element').' = '.$db->q($element));
				
				$db->setQuery($query,0,1);
				$details = $db->loadObject();
				
				$name = isset($details->name) && !empty($details->name) ? $details->name : $element;
				$lang->load($name, JPATH_ADMINISTRATOR);
				$name = str_replace(array('System','system','-'),'',JText::_($name));
				$name = trim($name);
				
				$tmp->id = $details->extension_id;
				$tmp->name = $name;
				$tmp->published = $details->enabled;
				
				$return[] = $tmp;
			}
		}
		
		return $return;
	}
}