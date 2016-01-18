<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsfilesModelReports extends JModelLegacy
{
	protected $_query;
	protected $_data;
	protected $_total=null;
	protected $_pagination=null;
	
	public function __construct() {
		parent::__construct();
		$this->_buildQuery();
		$app = JFactory::getApplication();
		
		// Get pagination request variables
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = $app->getUserStateFromRequest('com_rsfiles.limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}
	
	protected function _buildQuery() {
		$db	 	= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$cid	= JFactory::getApplication()->input->getString('id','');
		
		if (rsfilesHelper::external($cid)) {
			$query->clear()
				->select('*')
				->from($db->qn('#__rsfiles_reports'))
				->where($db->qn('IdFile').' = '.(int) $cid)
				->order($db->qn('date').' DESC');
		} else {
			$query->clear()
				->select($db->qn('r.IdReport'))->select($db->qn('r.ReportMessage'))->select($db->qn('r.date'))
				->from($db->qn('#__rsfiles_reports','r'))
				->join('LEFT', $db->qn('#__rsfiles_files','f').' ON '.$db->qn('r.IdFile').' = '.$db->qn('f.IdFile'))
				->where($db->qn('f.FilePath').' = '.$db->q($cid))
				->order($db->qn('r.date').' DESC');
		}
		
		$this->_query = $query;
	}
	
	public function getData() {
		if (empty($this->_data)) {
			$db	= JFactory::getDbo();
			$db->setQuery($this->_query,$this->getState('com_rsfiles.reports.limitstart'), $this->getState('com_rsfiles.reports.limit'));
			$this->_data = $db->loadObjectList();
		}

		return $this->_data;
	}
	
	public function getTotal() {
		if (empty($this->_total)) {
			$db	= JFactory::getDbo();
			$db->setQuery($this->_query);
			$db->execute();
			$this->_total = $db->getNumRows();
		}

		return $this->_total;
	}
	
	public function getPagination() {
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('com_rsfiles.reports.limitstart'), $this->getState('com_rsfiles.reports.limit'));
		}
		return $this->_pagination;
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
	
	public function getReport() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$id		= JFactory::getApplication()->input->getInt('id',0);
		
		$query->clear()
			->select($db->qn('r.ip'))->select($db->qn('r.ReportMessage'))
			->select($db->qn('r.date'))->select($db->qn('u.name'))
			->from($db->qn('#__rsfiles_reports','r'))
			->join('LEFT', $db->qn('#__users','u').' ON '.$db->qn('u.id').' = '.$db->qn('r.uid'))
			->where($db->qn('r.IdReport').' = '.(int) $id);
		
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	public function delete($pks) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		if (!empty($pks)) {
			JArrayHelper::toInteger($pks);
			
			$query->clear()
				->delete()
				->from($db->qn('#__rsfiles_reports'))
				->where($db->qn('IdReport').' IN ('.implode(',',$pks).')');
			
			$db->setQuery($query);
			$db->execute();
		}
		
		return true;
	}
}