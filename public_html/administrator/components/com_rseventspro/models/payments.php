<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.helper' );

class RseventsproModelPayments extends JModelList
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
		
		JFactory::getApplication()->triggerEvent('rsepro_paymentPluginsList', array(array('container' => &$container)));
		
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
				$name = strtolower($name);
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