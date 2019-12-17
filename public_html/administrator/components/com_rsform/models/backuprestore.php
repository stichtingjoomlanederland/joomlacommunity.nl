<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformModelBackuprestore extends JModelAdmin
{
	protected $_data;
	protected $_query;
	protected $_db;
	
	public function __construct() {
		parent::__construct();
		$this->_db = JFactory::getDbo();
		$this->_query = $this->_buildQuery();
	}
	
	public function getForm($data = array(), $loadData = true) {
		// Get the form.
		$form = $this->loadForm('com_rsform.backuprestore', 'backuprestore', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}
	
	protected function loadFormData() {
		$data = array(
			'name' => RSFormProHelper::getConfig('backup.mask')
		);
		
		return $data;
	}
	
	protected function _buildQuery() {
		$query 	=  $this->_db->getQuery(true);
		$db		=& $this->_db;
		
		$query->select($db->qn('FormId'))
			  ->select($db->qn('FormTitle'))
			  ->select($db->qn('FormName'))
			  ->select($db->qn('Lang'))
			  ->from($db->qn('#__rsform_forms'))
			  ->order($db->qn($this->getSortColumn()).' '.$db->escape($this->getSortOrder()));
		
		return $query;
	}
	
	public function getForms()
	{
		if (empty($this->_data))
		{
			$this->_data = $this->_getList($this->_query);

			// Count submissions
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('COUNT(' . $db->qn('SubmissionId') . ') AS ' . $db->qn('total'))
				->select($db->qn('FormId'))
				->from($db->qn('#__rsform_submissions'))
				->group($db->qn('FormId'));
			$allSubmissions = $db->setQuery($query)->loadObjectList('FormId');
			
			foreach ($this->_data as $i => $row)
			{
				$lang = RSFormProHelper::getCurrentLanguage($row->FormId);
				if ($lang != $row->Lang)
				{
					if ($translations = RSFormProHelper::getTranslations('forms', $row->FormId, $lang))
					{
						foreach ($translations as $field => $value)
						{
							if (isset($row->{$field}))
							{
								$row->{$field} = $value;
							}
						}
					}
				}

				$row->_allSubmissions = isset($allSubmissions[$row->FormId]) ? $allSubmissions[$row->FormId]->total : 0;
			}
		}
		
		return $this->_data;
	}

	public function getSubmissions()
	{
		$result = array();

		if (!empty($this->_data))
		{
			$query = $this->_db->getQuery(true)
				->select('COUNT(' . $this->_db->qn('SubmissionId') . ') AS cnt')
				->select($this->_db->qn('FormId'))
				->from($this->_db->qn('#__rsform_submissions'))
				->where($this->_db->qn('FormId') . ' <> ' . $this->_db->q(0))
				->group($this->_db->qn('FormId'));

			$result = $this->_db->setQuery($query)->loadObjectList('FormId');
		}

		return $result;
	}
	
	public function getSortColumn() {
		return JFactory::getApplication()->getUserStateFromRequest('com_rsform.forms.filter_order', 'filter_order', 'FormId', 'word');
	}
	
	public function getSortOrder() {
		return JFactory::getApplication()->getUserStateFromRequest('com_rsform.forms.filter_order_Dir', 'filter_order_Dir', 'ASC', 'word');
	}
	
	public function getIsWritable() {
		return is_writable($this->getTempDir());
	}
	
	public function getTempDir() {
		return JFactory::getConfig()->get('tmp_path');
	}
	
	public function getRSFieldset() {
		require_once JPATH_COMPONENT.'/helpers/adapters/fieldset.php';
		
		$fieldset = new RSFieldset();
		return $fieldset;
	}
	
	public function getRSTabs() {
		require_once JPATH_COMPONENT.'/helpers/adapters/tabs.php';
		
		$tabs = new RSTabs('com-rsform-configuration');
		return $tabs;
	}
	
	public function getSideBar() {
		require_once JPATH_COMPONENT.'/helpers/toolbar.php';

		return RSFormProToolbarHelper::render();
	}
}