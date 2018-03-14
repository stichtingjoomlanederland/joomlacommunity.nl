<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformModelMenus extends JModelLegacy
{
	var $_data = null;
	var $_total = 0;
	var $_query = '';
	var $_pagination = null;
	var $_db = null;
	
	function __construct()
	{
		parent::__construct();
		$this->_db = JFactory::getDbo();
		$this->_query = $this->_buildQuery();
		
		$mainframe = JFactory::getApplication();
		
		// Get pagination request variables
		$limit 		= $mainframe->getUserStateFromRequest('com_rsform.menus.limit', 'limit', JFactory::getConfig()->get('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest('com_rsform.menus.limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('com_rsform.menus.limit', 		$limit);
		$this->setState('com_rsform.menus.limitstart', 	$limitstart);
	}
	
	function _buildQuery()
	{
		$query  = "SELECT * FROM #__menu_types ORDER BY `menutype` ASC";
		
		return $query;
	}
	
	function getMenus()
	{		
		if (empty($this->_data))
			$this->_data = $this->_getList($this->_query, $this->getState('com_rsform.menus.limitstart'), $this->getState('com_rsform.menus.limit'));
		
		return $this->_data;
	}
	
	function getTotal()
	{
		if (empty($this->_total))
			$this->_total = $this->_getListCount($this->_query); 
		
		return $this->_total;
	}
	
	function getPagination()
	{
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('com_rsform.menus.limitstart'), $this->getState('com_rsform.menus.limit'));
		}
		
		return $this->_pagination;
	}
	
	function getFormTitle()
	{
		$formId = JFactory::getApplication()->input->getInt('formId');
		
		$this->_db->setQuery("SELECT FormTitle FROM #__rsform_forms WHERE FormId='".$formId."'");
		return $this->_db->loadResult();
	}
}