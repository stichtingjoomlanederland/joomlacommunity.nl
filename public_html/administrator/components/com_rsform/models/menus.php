<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformModelMenus extends JModelLegacy
{
	public $_data = null;
	public $_total = 0;
	public $_query = '';
	public $_pagination = null;
	public $_db = null;

	public function __construct($config = array())
	{
		parent::__construct($config);

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
	
	protected function _buildQuery()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('#__menu_types'))
			->order($db->qn('menutype') . ' ' . $db->escape('asc'));
		
		return $query;
	}
	
	public function getMenus()
	{		
		if (empty($this->_data))
		{
			$this->_data = $this->_getList($this->_query, $this->getState('com_rsform.menus.limitstart'), $this->getState('com_rsform.menus.limit'));
		}

		return $this->_data;
	}
	
	public function getTotal()
	{
		if (empty($this->_total))
		{
			$this->_total = $this->_getListCount($this->_query);
		}

		return $this->_total;
	}
	
	public function getPagination()
	{
		if (empty($this->_pagination))
		{
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('com_rsform.menus.limitstart'), $this->getState('com_rsform.menus.limit'));
		}

		return $this->_pagination;
	}
	
	public function getFormTitle()
	{
		$formId = JFactory::getApplication()->input->getInt('formId');
		$query = $this->_db->getQuery(true)
			->select($this->_db->qn('FormTitle'))
			->from($this->_db->qn('#__rsform_forms'))
			->where($this->_db->qn('FormId') . ' = ' . $this->_db->q($formId));

		return $this->_db->setQuery($query)->loadResult();
	}
}