<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsfilesModelLicenses extends JModelList
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
				'IdLicense','LicenseName','published'
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
		parent::populateState('LicenseName', 'desc');
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
		$query->select($db->qn('IdLicense').', '.$db->qn('LicenseName').', '.$db->qn('published'));
		
		// Select from table
		$query->from($db->qn('#__rsfiles_licenses'));
		
		// Filter by search in name or description
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->q('%'.$db->escape($search, true).'%');
			$query->where('('.$db->qn('LicenseName').' LIKE '.$search.' OR '.$db->qn('LicenseText').' LIKE '.$search.')');
		}
		
		// Add the list ordering clause
		$listOrdering = $this->getState('list.ordering', 'LicenseName');
		$listDirn = $db->escape($this->getState('list.direction', 'desc'));
		$query->order($db->qn($listOrdering).' '.$listDirn);
		
		return $query;
	}
	
	/**
	 * Method to set the side bar.
	 */
	public function getSidebar() {
		if (rsfilesHelper::isJ3()) {
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
		$options['listDirn']  = $this->getState('list.direction', 'desc');
		$options['listOrder'] = $this->getState('list.ordering', 'LicenseName');
		$options['sortFields'] = array(
			JHtml::_('select.option', 'IdLicense', JText::_('JGRID_HEADING_ID')),
			JHtml::_('select.option', 'LicenseName', JText::_('COM_RSFILES_LICENSE_NAME')),
			JHtml::_('select.option', 'published', JText::_('JSTATUS'))
		);
		$options['limitBox']   = $this->getPagination()->getLimitBox();
		
		$bar = new RSFilterBar($options);
		return $bar;
	}
}