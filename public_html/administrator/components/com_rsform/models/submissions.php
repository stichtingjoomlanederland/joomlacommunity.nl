<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformModelSubmissions extends JModelLegacy
{
	public $_data = array();
	public $_total = 0;
	public $_query = '';
	public $_pagination = null;
	public $_db = null;
	
	public $firstFormId = 0;
	public $allFormIds = array();
	
	public $export = false;
	public $rows = 0;
	
	public function __construct()
	{
		parent::__construct();
		$this->_db = JFactory::getDbo();
		// get the previous filters hashes
		$mainframe = JFactory::getApplication();
		$previousFiltersHash = $mainframe->getUserState('com_rsform.submissions.currentfilterhash', '');

		// get the current filters hashes
		$currentFiltersHash = $this->getFiltersHash();

		$this->_query = $this->_buildQuery();

		// Get pagination request variables
		$limit 		= $mainframe->getUserStateFromRequest('com_rsform.submissions.limit', 'limit', JFactory::getConfig()->get('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest('com_rsform.submissions.limitstart', 'limitstart', 0, 'int');


		// reset the pagination if the filters are not the same
		if ($previousFiltersHash != $currentFiltersHash)
		{
			$limitstart = 0;
		}
		
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		$this->setState('com_rsform.submissions.limit', $limit);
		$this->setState('com_rsform.submissions.limitstart', $limitstart);
	}
	
	public function _buildQuery()
	{
		$sortColumn = $this->getSortColumn();
		$sortOrder = $this->getSortOrder();
		$formId = $this->getFormId();
		$filter = $this->_db->escape($this->getFilter());
		
		$language_filter = $this->getLang();

		// Order by static headers
		if (in_array($sortColumn, $this->getStaticHeaders()))
		{
			$query  = "SELECT SQL_CALC_FOUND_ROWS DISTINCT(sv.SubmissionId), s.* FROM #__rsform_submissions s";
			$query .= " LEFT JOIN #__rsform_submission_values sv ON (s.SubmissionId=sv.SubmissionId)";
			$query .= " WHERE s.FormId='".$formId."'";
			
			// Only for export - export selected rows
			if ($this->export && !empty($this->rows))
				$query .= " AND s.SubmissionId IN (".implode(",", $this->rows).")";
			
			// Check if there's a filter (search) set
			if (!$this->export)
			{
				if ($filter)
				{
					$query .= " AND (sv.FieldValue LIKE '%".$filter."%'";
					if (!preg_match('#([^0-9\-: ])#', $filter))
						$query .= " OR s.DateSubmitted LIKE '%".$filter."%'";
					$query .= " OR s.Username LIKE '%".$filter."%'";
					$query .= " OR s.UserIp LIKE '%".$filter."%')";
				}
				
				if ($language_filter)
				{
					$query .= " AND s.Lang='" . $this->_db->escape($language_filter) . "'";
				}
				
				$from = $this->getDateFrom();				
				if ($from) {
					$from = JFactory::getDate($from, JFactory::getConfig()->get('offset'))->toSql();
					$query .= " AND s.DateSubmitted >= '".$this->_db->escape($from)."'";
				}
				
				$to = $this->getDateTo();
				if ($to) {
					$to = JFactory::getDate($to, JFactory::getConfig()->get('offset'))->toSql();
					$query .= " AND s.DateSubmitted <= '".$this->_db->escape($to)."'";
				}
			}
			$query .= " ORDER BY " . $this->_db->qn('s.' . $sortColumn) . " " . $this->_db->escape($sortOrder);
		}
		// Order by dynamic headers (form fields)
		else
		{
			$query  = "SELECT SQL_CALC_FOUND_ROWS DISTINCT(sv.SubmissionId), s.* FROM #__rsform_submissions s";
			$query .= " LEFT JOIN #__rsform_submission_values sv ON (s.SubmissionId=sv.SubmissionId)";
			$query .= " WHERE s.FormId='".$formId."'";
			
			// Only for export - export selected rows
			if ($this->export && !empty($this->rows))
				$query .= " AND s.SubmissionId IN (".implode(",", $this->rows).")";
			
			// Check if there's a filter (search) set
			if (!$this->export)
			{
				if ($filter)
				{
					$query .= " AND (s.DateSubmitted LIKE '%".$filter."%'";
					$query .= " OR s.Username LIKE '%".$filter."%'";
					$query .= " OR s.UserIp LIKE '%".$filter."%'";
					$query .= " OR s.SubmissionId IN (SELECT DISTINCT(SubmissionId) FROM #__rsform_submission_values WHERE FieldValue LIKE '%".$filter."%'))";
				}
				
				if ($language_filter)
				{
					$query .= " AND s.Lang='" . $this->_db->escape($language_filter) . "'";
				}
				
				$from = $this->getDateFrom();				
				if ($from)
				{
					$query .= " AND s.DateSubmitted >= '" . $this->_db->escape($from) . "'";
				}
				
				$to = $this->getDateTo();
				if ($to)
				{
					$query .= " AND s.DateSubmitted <= '" . $this->_db->escape($to) . "'";
				}
			}
			
			if ($this->checkOrderingPossible($sortColumn))
				$query .= " AND sv.FieldName=".$this->_db->q($sortColumn);
				
			$query .= " ORDER BY `FieldValue` ".$this->_db->escape($sortOrder);
		}

		// set the current filters hash
		JFactory::getApplication()->setUserState('com_rsform.submissions.currentfilterhash', $this->getFiltersHash());
		
		return $query;
	}

	protected function getFiltersHash() {
		static $hash;

		if (is_null($hash))
		{
			$formId          = $this->getFormId();
			$filter          = $this->_db->escape($this->getFilter());
			$language_filter = $this->getLang();
			$from            = $this->getDateFrom();
			$to              = $this->getDateTo();

			$currentFiltersHash = $formId . $filter . $language_filter . $from . $to;
			$hash = md5($currentFiltersHash);
		}

		return $hash;
	}
	
	public function checkOrderingPossible($field)
	{
		$formId = $this->getFormId();
		$this->_db->setQuery("SELECT SubmissionValueId FROM #__rsform_submission_values WHERE FieldName='".$this->_db->escape($field)."' AND FormId='".$formId."'");
		return $this->_db->loadResult();
	}
	
	public function getDateFrom()
	{
		$app = JFactory::getApplication();
		$dateFrom = $app->getUserStateFromRequest('com_rsform.submissions.dateFrom', 'dateFrom');
		
		// Test if date is valid
		try {
			$date = JFactory::getDate($dateFrom);
			return $dateFrom;
		} catch (Exception $e) {
			$app->enqueueMessage($e->getMessage(), 'warning');
			$app->input->set('dateFrom', '');
			
			return '';
		}
	}
	
	public function getSpecialFields() {
		static $called;
	
		if (is_null($called)) {
			$fields = array();
			$formId = $this->getFormId();
			
			$this->_db->setQuery("SELECT c.ComponentTypeId, p.ComponentId, p.PropertyName, p.PropertyValue FROM #__rsform_components c LEFT JOIN #__rsform_properties p ON (c.ComponentId=p.ComponentId) WHERE c.FormId='".$formId."' AND c.Published='1' AND p.PropertyName IN ('NAME', 'WYSIWYG')");
			$components = $this->_db->loadObjectList();
			$fields['uploadFields']		= array();		
			$fields['multipleFields']	= array();		
			$fields['textareaFields']	= array();		

			foreach ($components as $component)
			{
				// Upload fields
				if ($component->ComponentTypeId == RSFORM_FIELD_FILEUPLOAD)
				{
					$fields['uploadFields'][] = $component->PropertyValue;
				}
				// Multiple fields
				elseif (in_array($component->ComponentTypeId, array(RSFORM_FIELD_SELECTLIST, RSFORM_FIELD_CHECKBOXGROUP)))
				{
					$fields['multipleFields'][] = $component->PropertyValue;
				}
				// Textarea fields
				elseif ($component->ComponentTypeId == RSFORM_FIELD_TEXTAREA)
				{
					if ($component->PropertyName == 'WYSIWYG' && $component->PropertyValue == 'NO')
						$fields['textareaFields'][] = $component->ComponentId;
				}
			}
			
			if (!empty($fields['textareaFields']))
			{
				$this->_db->setQuery("SELECT p.PropertyValue FROM #__rsform_components c LEFT JOIN #__rsform_properties p ON (c.ComponentId=p.ComponentId) WHERE c.ComponentId IN (".implode(',', $fields['textareaFields']).")");
				$fields['textareaFields'] = $this->_db->loadColumn();
			}
			
			$called = $fields;
		}
		
		return $called;
	}
	
	public function getDateTo()
	{
		$app = JFactory::getApplication();
		$dateTo = $app->getUserStateFromRequest('com_rsform.submissions.dateTo', 'dateTo');
		
		// Test if date is valid
		try {
			$date = JFactory::getDate($dateTo);
			return $dateTo;
		} catch (Exception $e) {
			$app->enqueueMessage($e->getMessage(), 'warning');
			$app->input->set('dateTo', '');
			
			return '';
		}
	}
	
	public function getSubmissions()
	{		
		jimport('joomla.filesystem.file');
		
		if (empty($this->_data))
		{
			$formId = $this->getFormId();
			
			$this->_db->setQuery("SELECT MultipleSeparator, TextareaNewLines FROM #__rsform_forms WHERE FormId='".$formId."'");
			$form = $this->_db->loadObject();
			if (empty($form))
				return $this->_data;
			
			$uploadFields 	= array();
			$multipleFields = array();
			$textareaFields = array();
			$fieldTypes = $this->getSpecialFields();
			if (isset($fieldTypes['uploadFields'])) {
				$uploadFields = $fieldTypes['uploadFields'];	
			}
			if (isset($fieldTypes['multipleFields'])) {
				$multipleFields = $fieldTypes['multipleFields'];	
			}
			if (isset($fieldTypes['textareaFields'])) {
				$textareaFields = $fieldTypes['textareaFields'];	
			}
			
			$this->_db->setQuery("SET SQL_BIG_SELECTS=1");
			$this->_db->execute();
			
			$submissionIds = array();
			
			$results = $this->_getList($this->_query, $this->getState('com_rsform.submissions.limitstart'), $this->getState('com_rsform.submissions.limit'));
			$this->_db->setQuery("SELECT FOUND_ROWS();");
			$this->_total = $this->_db->loadResult();
			foreach ($results as $result)
			{
				$submissionIds[] = $result->SubmissionId;
				
				$this->_data[$result->SubmissionId]['SubmissionId'] = $result->SubmissionId;
				$this->_data[$result->SubmissionId]['FormId'] = $result->FormId;
				$this->_data[$result->SubmissionId]['DateSubmitted'] = RSFormProHelper::getDate($result->DateSubmitted);
				$this->_data[$result->SubmissionId]['UserIp'] = $result->UserIp;
				$this->_data[$result->SubmissionId]['Username'] = $result->Username;
				$this->_data[$result->SubmissionId]['UserId'] = $result->UserId;
				$this->_data[$result->SubmissionId]['Lang'] = $result->Lang;
				$this->_data[$result->SubmissionId]['confirmed'] = $result->confirmed ? JText::_('RSFP_YES') : JText::_('RSFP_NO');
				$this->_data[$result->SubmissionId]['SubmissionValues'] = array();
			}
			
			if (!empty($submissionIds))
			{
				$layout = JFactory::getApplication()->input->get('layout');
				$view = JFactory::getApplication()->input->get('view');
				$must_escape = $view == 'submissions' && $layout == 'default';
				
				$results = $this->_getList("SELECT * FROM `#__rsform_submission_values` WHERE `SubmissionId` IN (".implode(',',$submissionIds).")");
				foreach ($results as $result)
				{
					// Check if this is an upload field
					if (in_array($result->FieldName, $uploadFields) && !empty($result->FieldValue) && !$this->export)
						$result->FieldValue = '<a href="index.php?option=com_rsform&amp;task=submissions.view.file&amp;id='.$result->SubmissionValueId.'">'.JFile::getName($result->FieldValue).'</a>';
					else
					{
						// Check if this is a multiple field
						if (in_array($result->FieldName, $multipleFields))
							$result->FieldValue = str_replace("\n", $form->MultipleSeparator, $result->FieldValue);
						// Transform new lines
						elseif ($form->TextareaNewLines && in_array($result->FieldName, $textareaFields))
						{
							if ($must_escape)
								$result->FieldValue = RSFormProHelper::htmlEscape($result->FieldValue);
						}
						// PayPal status
						elseif ($result->FieldName == '_STATUS')
							$result->FieldValue = JText::_('RSFP_PAYPAL_STATUS_'.$result->FieldValue);
						// ANZ status
						elseif ($result->FieldName == '_ANZ_STATUS')
							$result->FieldValue = JText::_('RSFP_ANZ_STATUS_'.$result->FieldValue);
						else
						{
							if ($must_escape)
								$result->FieldValue = RSFormProHelper::htmlEscape($result->FieldValue);
						}
					}
						
					$this->_data[$result->SubmissionId]['SubmissionValues'][$result->FieldName] = array('Value' => $result->FieldValue, 'Id' => $result->SubmissionValueId);
				}
				
				JFactory::getApplication()->triggerEvent('rsfp_b_onManageSubmissions', array(array(
                    'formId'   		=> $formId,
                    'submissions' 	=> &$this->_data,
                    'export'  		=> $this->export,
                    'escape'  		=> $must_escape,
                )));
			}
			unset($results);
		}
		
		return $this->_data;
	}
	
	public function getSubmission()
	{
		$this->_db->setQuery("SELECT * FROM #__rsform_submissions WHERE SubmissionId='".$this->getSubmissionId()."'");
		return $this->_db->loadObject();
	}
	
	public function getHeaders()
	{
		$query  = "SELECT p.PropertyValue FROM #__rsform_components c";
		$query .= " LEFT JOIN #__rsform_properties p ON (c.ComponentId=p.ComponentId AND p.PropertyName='NAME')";
		$query .= " LEFT JOIN #__rsform_component_types ct ON (c.ComponentTypeId=ct.ComponentTypeId)";
		$query .= " WHERE c.FormId='".$this->getFormId()."' AND c.Published='1'";
		
		$task = strtolower(JFactory::getApplication()->input->getWord('task'));
		if (strpos($task, 'submissionsexport') !== false)
			$query .= " AND ct.ComponentTypeName NOT IN ('button', 'captcha', 'freeText', 'imageButton', 'submitButton')";
			
		$query .= " ORDER BY c.Order";
		
		$this->_db->setQuery($query);
		$headers = $this->_db->loadColumn();
		
		// PayPal
		$this->_db->setQuery("SELECT SubmissionValueId FROM #__rsform_submission_values WHERE FormId='".$this->getFormId()."' AND FieldName='_STATUS' LIMIT 1");
		if ($this->_db->loadResult())
			$headers[] = '_STATUS';
		
		//ANZ
		$this->_db->setQuery("SELECT SubmissionValueId FROM #__rsform_submission_values WHERE FormId='".$this->getFormId()."' AND FieldName='_ANZ_STATUS' LIMIT 1");
		if ($this->_db->loadResult())
			$headers[] = '_ANZ_STATUS';
		
		return $headers;
	}
	
	public function getUploadFields() {
		$db = JFactory::getDbo();
		$db->setQuery("SELECT p.PropertyValue FROM #__rsform_components c LEFT JOIN #__rsform_properties p ON (c.ComponentId=p.ComponentId) WHERE c.FormId='".$this->getFormId()."' AND c.ComponentTypeId ='9' AND p.PropertyName='NAME'");
		return $db->loadColumn();
	}
	
	public function getUnescapedFields(){
		$db = JFactory::getDbo();
		$db->setQuery("SELECT p.PropertyValue FROM #__rsform_components c LEFT JOIN #__rsform_properties p ON (c.ComponentId=p.ComponentId) WHERE c.FormId='".$this->getFormId()."' AND c.ComponentTypeId ='9' AND p.PropertyName='NAME'");
		$fields = $db->loadColumn();
		
		JFactory::getApplication()->triggerEvent('rsfp_b_onManageSubmissionsCreateUnescapedFields', array(array(
            'formId'    => $this->getFormId(),
            'fields'    => &$fields
        )));
        
        return $fields;
		
	}
	
	public function getHeadersEnabled()
	{
		$return = new stdClass();
		$return->staticHeaders = array();
		$return->headers = array();
		
		$formId = $this->getFormId();
		
		$this->_db->setQuery("SELECT ColumnName, ColumnStatic FROM #__rsform_submission_columns WHERE FormId='".$formId."'");
		$results = $this->_db->loadObjectList();
		
		foreach ($results as $result) {
			if ($result->ColumnStatic)
				$return->staticHeaders[] = $result->ColumnName;
			else
				$return->headers[] = $result->ColumnName;
		}
		
		return $return;
	}
	
	public function getStaticHeaders()
	{		
		$return = array('SubmissionId', 'DateSubmitted', 'UserIp', 'Username', 'UserId', 'Lang');
		if ($this->addConfirmedHeader()) $return[] = 'confirmed';
		
		return $return;
	}
	
	public function addConfirmedHeader()
	{
		static $found = null;
		if (is_null($found)) {
			$formId = $this->getFormId();
			$this->_db->setQuery("SELECT ConfirmSubmission FROM #__rsform_forms WHERE FormId='".$formId."'");
			$found = (bool) $this->_db->loadResult();
		}
		return $found;
	}
	
	public function getTotal()
	{		
		return $this->_total;
	}
	
	public function getPagination()
	{
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('com_rsform.submissions.limitstart'), $this->getState('com_rsform.submissions.limit'));
		}
		
		return $this->_pagination;
	}
	
	public function getFormTitle()
	{
		$formId = $this->getFormId();
		
		$query = $this->_db->getQuery(true)
			->select($this->_db->qn('FormTitle'))
			->from($this->_db->qn('#__rsform_forms'))
			->where($this->_db->qn('FormId') . ' = ' . $this->_db->q($formId));
		$title = $this->_db->setQuery($query)->loadResult();
		
		$lang = RSFormProHelper::getCurrentLanguage($formId);
		if ($translations = RSFormProHelper::getTranslations('forms', $formId, $lang))
		{
			if (isset($translations['FormTitle']))
			{
				$title = $translations['FormTitle'];
			}
		}
		
		return $title;
	}
	
	public function getForms()
	{
		$mainframe = JFactory::getApplication();
		
		$return = array();
		$sortColumn = $mainframe->getUserState('com_rsform.forms.filter_order');
		if (empty($sortColumn))
			$sortColumn = 'FormId';
		$sortOrder  = $mainframe->getUserState('com_rsform.forms.filter_order_Dir');
		if (empty($sortOrder))
			$sortOrder = 'DESC';
		
		$query  = "SELECT FormId, FormTitle, Lang FROM #__rsform_forms WHERE 1";
		$query .= " ORDER BY `".$sortColumn."` ".$sortOrder;
		
		$results = $this->_getList($query);
		foreach ($results as $result) {
			$lang = RSFormProHelper::getCurrentLanguage($result->FormId);
			if ($lang != $result->Lang)
			{
				if ($translations = RSFormProHelper::getTranslations('forms', $result->FormId, $lang))
				{
					foreach ($translations as $field => $value)
					{
						if (isset($result->$field))
						{
							$result->$field = $value;
						}
					}
				}
			}

			$return[] = JHtml::_('select.option', $result->FormId, $result->FormTitle);
			$this->allFormIds[] = $result->FormId;
		}
		
		if (!empty($results[0]->FormId))
			$this->firstFormId = $results[0]->FormId;
		
		return $return;
	}
	
	public function getSortColumn()
	{
		$mainframe = JFactory::getApplication();
		return $mainframe->getUserStateFromRequest('com_rsform.submissions.filter_order', 'filter_order', 'DateSubmitted', 'string');
	}
	
	public function getSortOrder()
	{
		$mainframe = JFactory::getApplication();
		return $mainframe->getUserStateFromRequest('com_rsform.submissions.filter_order_Dir', 'filter_order_Dir', 'DESC', 'word');
	}
	
	public function getFilter()
	{
		$mainframe = JFactory::getApplication();
		return $mainframe->getUserStateFromRequest('com_rsform.submissions.filter', 'search', '');
	}
	
	public function getFormId()
	{
		$mainframe = JFactory::getApplication();
		
		if (empty($this->firstFormId))
			$this->getForms();
		
		$formId = $mainframe->getUserStateFromRequest('com_rsform.submissions.formId', 'formId', $this->firstFormId, 'int');
		if ($formId && !in_array($formId, $this->allFormIds)) {
			$formId = $this->firstFormId;
			$mainframe->setUserState('com_rsform.submissions.formId', $formId);
		}
		
		return $formId;
	}
	
	public function getSubmissionId()
	{
		$cid = JRequest::getVar('cid', array());
		if (is_array($cid))
			$cid = (int) @$cid[0];
		else
			$cid = (int) $cid;
		
		return $cid;
	}
	
	public function getEditFields()
	{
		$isPDF = JFactory::getApplication()->input->get('format') == 'pdf';
		$pattern = '#\[p(.*?)\]#is';
		$cid = $this->getSubmissionId();
		
		$return = array();
		
		$this->_db->setQuery("SELECT * FROM #__rsform_submissions WHERE SubmissionId='".$cid."'");
		$submission = $this->_db->loadObject();
		
		if (empty($submission))
		{
			$mainframe = JFactory::getApplication();
			$mainframe->redirect('index.php?option=com_rsform&task=submissions.manage');
			return $return;
		}
		
		if ($isPDF)
		{
			$this->_db->setQuery("SELECT MultipleSeparator, TextareaNewLines FROM #__rsform_forms WHERE FormId='".$submission->FormId."'");
			$form = $this->_db->loadObject();
			$form->MultipleSeparator = str_replace(array('\n', '\r', '\t'), array("\n", "\r", "\t"), $form->MultipleSeparator);
		}
		
		$this->_db->setQuery("SELECT FieldName, FieldValue FROM #__rsform_submission_values WHERE SubmissionId='".$cid."'");
		$fields = $this->_db->loadObjectList();
		foreach ($fields as $field)
			$submission->values[$field->FieldName] = $field->FieldValue;
		unset($fields);
		
		$this->_db->setQuery("SELECT p.PropertyValue, ct.ComponentTypeName, c.ComponentId FROM #__rsform_components c LEFT JOIN #__rsform_properties p ON (p.ComponentId=c.ComponentId) LEFT JOIN #__rsform_component_types ct ON (c.ComponentTypeId=ct.ComponentTypeId) WHERE c.FormId='".$submission->FormId."' AND c.Published='1' AND p.PropertyName='NAME' ORDER BY `Order`");
		$fields = $this->_db->loadObjectList();
		if (empty($fields))
			return $return;
		
		$componentIds = array();
		foreach ($fields as $field)
			$componentIds[] = $field->ComponentId;
		
		$properties = RSFormProHelper::getComponentProperties($componentIds);
		
		foreach ($fields as $field)
		{
			$data = $properties[$field->ComponentId];
			
			$new_field = array();
			$new_field[0] = $field->PropertyValue;
			
			$name = $field->PropertyValue;
			$value = isset($submission->values[$field->PropertyValue]) ? $submission->values[$field->PropertyValue] : '';
			
			if ($data['NAME'] == 'RSEProPayment')
				$field->ComponentTypeName = 'rsepropayment';
			
			switch ($field->ComponentTypeName)
			{
				// skip this field for now, no need to edit it
				case 'freeText':
					continue 2;
				break;
				
				default:
					if ($isPDF) {
						$new_field[1] = RSFormProHelper::htmlEscape($value);
					} else {
						if (strpos($value, "\n") !== false || strpos($value, "\r") !== false) {
							$new_field[1] = '<textarea style="width: 95%" class="rs_textarea" rows="10" cols="60" name="form['.$name.']">'.RSFormProHelper::htmlEscape($value).'</textarea>';
						} else {
							$new_field[1] = '<input class="rs_inp rs_80"" size="105" type="text" name="form['.$name.']" value="'.RSFormProHelper::htmlEscape($value).'" />';
						}
					}
				break;
				
				case 'textArea':
					if ($isPDF)
					{
						if (isset($data['WYSIWYG']) && $data['WYSIWYG'] == 'YES')
							$value = $value;
						elseif ($form->TextareaNewLines)
							$value = nl2br(RSFormProHelper::htmlEscape($value));
						$new_field[1] = $value;
					}
					elseif (isset($data['WYSIWYG']) && $data['WYSIWYG'] == 'YES')
						$new_field[1] = RSFormProHelper::WYSIWYG('form['.$name.']', RSFormProHelper::htmlEscape($value), '', 600, 100, 60, 10);
					else
						$new_field[1] = '<textarea style="width: 95%" class="rs_textarea" rows="10" cols="60" name="form['.$name.']">'.RSFormProHelper::htmlEscape($value).'</textarea>';
				break;
				
				case 'radioGroup':
				case 'checkboxGroup':
				case 'selectList':
					if ($isPDF)
					{
						$new_field[1] = str_replace("\n", $form->MultipleSeparator, $value);
						break;
					}
					
					if ($field->ComponentTypeName == 'radioGroup') {
						$data['SIZE'] = 0;
						$data['MULTIPLE'] = 'NO';
					} elseif ($field->ComponentTypeName == 'checkboxGroup') {
						$data['SIZE'] = 5;
						$data['MULTIPLE'] = 'YES';
					}
					
					$value = RSFormProHelper::explode($value);
					
					$items = RSFormProHelper::isCode($data['ITEMS']);
					$items = RSFormProHelper::explode($items);
					
					$options = array();
					foreach($items as $item) {
						
						if (preg_match($pattern,$item,$match)) {
							$item = preg_replace($pattern,'',$item);
						}
						
						// <OPTGROUP>
						if(preg_match('/\[g\]/',$item))
						{
							$item = str_replace('[g]', '', $item);
							$optgroup = new stdClass();
							$optgroup->value = '<OPTGROUP>';
							$optgroup->text = $item;
							$options[] = $optgroup;
							continue;
						}
						
						// </OPTGROUP>
						if(preg_match('/\[\/g\]/',$item))
						{
							$optgroup = new stdClass();
							$optgroup->value = '</OPTGROUP>';
							$optgroup->text = '';
							$options[] = $optgroup;
							continue;
						}
						
						$buf = explode('|',$item);
						
						$val = str_replace('[c]', '', $buf[0]);
						$item = str_replace('[c]', '', count($buf) == 1 ? $buf[0] : $buf[1]);
						$options[] = JHtml::_('select.option', $val, $item);
					}
					
					$attribs = array();
					if ((int) $data['SIZE'] > 0)
						$attribs[] = 'size="'.(int) $data['SIZE'].'"';
					if ($data['MULTIPLE'] == 'YES')
						$attribs[] = 'multiple="multiple"';
					$attribs = implode(' ', $attribs);
					
					$new_field[1] = JHtml::_('select.genericlist', $options, 'form['.$name.'][]', $attribs, 'value', 'text', $value);
				break;
				
				case 'fileUpload':
					if ($isPDF)
					{
						$new_field[1] = $value;
						break;
					}
					$new_field[1]  = '<input class="rs_inp rs_80" size="105" type="text" name="form['.$name.']" value="'.RSFormProHelper::htmlEscape($value).'" />';
					$new_field[1] .= '<br /><input size="45" type="file" name="upload['.$name.']" />';
				break;
			}
			
			$return[] = $new_field;
		}
		
		// PayPal
		if (isset($submission->values['_STATUS']))
		{
			$name = '_STATUS';
			$value = $submission->values['_STATUS'];
			
			$new_field[0] = JText::_('RSFP_PAYMENT_STATUS');
			
			if ($isPDF)
			{
				$new_field[1] = JText::_('RSFP_PAYPAL_STATUS_'.$value);
			}
			else
			{
				$options = array(
					JHtml::_('select.option', -1, JText::_('RSFP_PAYPAL_STATUS_-1')),
					JHtml::_('select.option', 0, JText::_('RSFP_PAYPAL_STATUS_0')),
					JHtml::_('select.option', 1, JText::_('RSFP_PAYPAL_STATUS_1'))
				);
				$new_field[1] = JHtml::_('select.genericlist', $options, 'form['.$name.'][]', null, 'value', 'text', $value);
			}
			
			$return[] = $new_field;
		}
		
		// ANZ
		if (isset($submission->values['_ANZ_STATUS']))
		{
			$name = '_ANZ_STATUS';
			$value = $submission->values['_ANZ_STATUS'];
			
			$new_field[0] = JText::_('RSFP_ANZ_STATUS');
			
			if ($isPDF)
			{
				$new_field[1] = JText::_('RSFP_ANZ_STATUS_'.$value);
			}
			else
			{
				$options = array(
					JHtml::_('select.option', -1, JText::_('RSFP_ANZ_STATUS_-1')),
					JHtml::_('select.option', 0, JText::_('RSFP_ANZ_STATUS_0')),
					JHtml::_('select.option', 1, JText::_('RSFP_ANZ_STATUS_1'))
				);
				$new_field[1] = JHtml::_('select.genericlist', $options, 'form['.$name.'][]', null, 'value', 'text', $value);
			}
			
			$return[] = $new_field;
		}
		
		return $return;
	}
	
	public function save()
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		
		$app	= JFactory::getApplication();
		$offset = JFactory::getConfig()->get('offset');
		$cid    = $this->getSubmissionId();
		$form   = JRequest::getVar('form', array(), 'post', 'none', JREQUEST_ALLOWRAW);
		$static = JRequest::getVar('formStatic', array(), 'post', 'none', JREQUEST_ALLOWRAW);
		$date	= JFactory::getDate($static['DateSubmitted'], $offset);
		$static['DateSubmitted'] = $date->toSql();
		$formId = JFactory::getApplication()->input->getInt('formId');
		$files  = JRequest::getVar('upload', array(), 'files', 'none', JREQUEST_ALLOWRAW);
		
		// Handle file uploads first
		if (!empty($files['error']))
		foreach ($files['error'] as $field => $error)
		{
			if ($error)
				continue;
				
			$this->_db->setQuery("SELECT FieldValue FROM #__rsform_submission_values WHERE FieldName='".$this->_db->escape($field)."' AND SubmissionId='".$cid."' LIMIT 1");
			$original = $this->_db->loadResult();
			
			// already uploaded
			if (!empty($form[$field]))
			{
				// Path has changed, remove the original file to save up space
				if ($original != $form[$field] && JFile::exists($original) && is_file($original))
					JFile::delete($original);
			
				if (JFolder::exists(dirname($form[$field])))
					JFile::upload($files['tmp_name'][$field], $form[$field], false, true);
			}
			// first upload
			else
			{
				$this->_db->setQuery("SELECT c.ComponentId FROM #__rsform_components c LEFT JOIN #__rsform_properties p ON (c.ComponentId=p.ComponentId) WHERE c.FormId='".$formId."' AND p.PropertyName='NAME' AND p.PropertyValue='".$this->_db->escape($field)."'");
				$componentId = $this->_db->loadResult();
				if ($componentId)
				{
					$data = RSFormProHelper::getComponentProperties($componentId);
					// Prefix
					$prefix = uniqid('').'-';
					if (isset($data['PREFIX']) && strlen(trim($data['PREFIX'])) > 0)
						$prefix = RSFormProHelper::isCode($data['PREFIX']);						
					
					if (JFolder::exists($data['DESTINATION']))
					{
						// Path
						$realpath = realpath($data['DESTINATION'].'/');
						if (substr($realpath, -1) != DIRECTORY_SEPARATOR)
							$realpath .= DIRECTORY_SEPARATOR;
						$path = $realpath.$prefix.'-'.$files['name'][$field];
						$form[$field] = $path;
						JFile::upload($files['tmp_name'][$field], $path, false, true);
					}
				}
			}
		}
		
		$update = array();
		foreach ($static as $field => $value)
			$update[] = "`".$this->_db->escape($field)."`='".$this->_db->escape($value)."'";
		
		if (!empty($update))
		{
			$this->_db->setQuery("UPDATE #__rsform_submissions SET ".implode(',', $update)." WHERE SubmissionId='".$cid."'");
			$this->_db->execute();
		}
		
		// Update fields
		foreach ($form as $field => $value)
		{
			if (is_array($value))
				$value = implode("\n", $value);
				
			$this->_db->setQuery("SELECT SubmissionValueId, FieldValue FROM #__rsform_submission_values WHERE FieldName='".$this->_db->escape($field)."' AND SubmissionId='".$cid."' LIMIT 1");
			$original = $this->_db->loadObject();
			if (!$original)
			{
				$this->_db->setQuery("INSERT INTO #__rsform_submission_values SET FormId='".$formId."', SubmissionId='".$cid."', FieldName='".$this->_db->escape($field)."', FieldValue='".$this->_db->escape($value)."'");
				$this->_db->execute();
			}
			else
			{
				// Update only if we've changed something
				if ($original->FieldValue !== $value)
				{
					// Check if this is an upload field
					if (isset($files['error'][$field]) && JFile::exists($original->FieldValue) && is_file($original->FieldValue))
					{
						// Move the file to the new location
						if (!empty($value) && JFolder::exists(dirname($value)))
							JFile::move($original->FieldValue, $value);
						// Delete the file if we've chosen to delete it
						elseif (empty($value))
							JFile::delete($original->FieldValue);
					}
						
					$this->_db->setQuery("UPDATE #__rsform_submission_values SET FieldValue='".$this->_db->escape($value)."' WHERE SubmissionValueId='".$original->SubmissionValueId."' LIMIT 1");
					$this->_db->execute();
				}
			}
		}
		
		// Checkboxes don't send a value if nothing is checked
		$this->_db->setQuery("SELECT p.PropertyValue FROM #__rsform_components c LEFT JOIN #__rsform_properties p ON (c.ComponentId=p.ComponentId) WHERE c.ComponentTypeId='4' AND p.PropertyName='NAME' AND c.FormId='".$formId."'");
		$checkboxes = $this->_db->loadColumn();
		foreach ($checkboxes as $checkbox)
		{
			$value = isset($form[$checkbox]) ? $form[$checkbox] : '';
			if (is_array($value))
				$value = implode("\n", $value);
				
			$this->_db->setQuery("UPDATE #__rsform_submission_values SET FieldValue='".$this->_db->escape($value)."' WHERE FieldName='".$this->_db->escape($checkbox)."' AND FormId='".$formId."' AND SubmissionId='".$cid."' LIMIT 1");			
			$this->_db->execute();
		}
	}
	
	public function getSubmissionFormId()
	{
		$cid = $this->getSubmissionId();
		$this->_db->setQuery("SELECT FormId FROM #__rsform_submissions WHERE SubmissionId='".$cid."' LIMIT 1");
		return $this->_db->loadResult();
	}
	
	public function getExportSelected()
	{
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
		$cid = array_map('intval', $cid);
		
		return $cid;
	}
	
	public function getExportFile()
	{
		return uniqid('');
	}
	
	public function getStaticFields()
	{
		$submissionid = $this->getSubmissionId();
		
		$this->_db->setQuery("SELECT * FROM #__rsform_submissions WHERE SubmissionId='".$submissionid."'");
		$submission = $this->_db->loadObject();
		
		if ($submission) {
			$submission->DateSubmitted = JHtml::_('date', $submission->DateSubmitted, 'Y-m-d H:i:s');
		}
		
		return $submission;
	}
	
	public function getExportType()
	{
		$task = JFactory::getApplication()->input->getCmd('task');
		$task = explode('.', $task);
		return end($task);
	}
	
	public function getExportTotal()
	{
		$formId = $this->getFormId();
		
		$ExportRows = JFactory::getApplication()->input->get('ExportRows', '', 'string');
		if (empty($ExportRows))
		{
		    $query = $this->_db->getQuery(true)
                ->select('COUNT(' . $this->_db->qn('SubmissionId') . ')')
                ->from($this->_db->qn('#__rsform_submissions'))
                ->where($this->_db->qn('FormId') . ' = ' . $this->_db->q($formId));
			$this->_db->setQuery($query);
			return $this->_db->loadResult();
		}
		
		$ExportRows = explode(',', $ExportRows);
		return count($ExportRows);
	}
	
	public function getLanguages()
	{
		$lang 	   = JFactory::getLanguage();
		$languages = $lang->getKnownLanguages(JPATH_SITE);
		
		$return = array();
		$return[] = JHtml::_('select.option', '', JText::_('RSFP_SUBMISSIONS_ALL_LANGUAGES'));
		foreach ($languages as $tag => $properties)
			$return[] = JHtml::_('select.option', $tag, $properties['name']);
		
		return $return;
	}
	
	public function getLang()
	{
		$mainframe = JFactory::getApplication();
		return $mainframe->getUserStateFromRequest('com_rsform.submissions.lang', 'Language', '');
	}
	
	public function getRSTabs() {
		require_once JPATH_COMPONENT.'/helpers/adapters/tabs.php';
		
		$tabs = new RSTabs('com-rsform-export');
		return $tabs;
	}
	
	public function getSideBar()
	{
		require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/toolbar.php';
		
		RSFormProToolbarHelper::addFilter(
			JText::_('RSFP_VIEW_SUBMISSIONS_FOR'),
			'formId',
			JHtml::_('select.options', $this->getForms(), 'value', 'text', $this->getFormId()),
			true
		);
		
		RSFormProToolbarHelper::addFilter(
			JText::_('RSFP_SHOW_SUBMISSIONS_LANGUAGE'),
			'Language',
			JHtml::_('select.options', $this->getLanguages(), 'value', 'text', $this->getLang()),
			true
		);
		
		return RSFormProToolbarHelper::render();
	}
}