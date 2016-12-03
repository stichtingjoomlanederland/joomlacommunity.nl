<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class rseventsproModelEmails extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array()) {
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
		$query->select($db->qn('id').', '.$db->qn('subject'));
		
		// Select from table
		$query->from($db->qn('#__rseventspro_emails'));
		$query->where($db->qn('type').' = '.$db->q('rule'));
		$query->where($db->qn('parent').' = '.$db->q(0));
		
		// Add the list ordering clause
		$listOrdering = $this->getState('list.ordering', 'subject');
		$listDirn = $db->escape($this->getState('list.direction', 'ASC'));
		$query->order($db->qn($listOrdering).' '.$listDirn);

		return $query;
	}
}