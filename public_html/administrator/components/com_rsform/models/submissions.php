<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
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

	public function __construct($config = array())
	{
		parent::__construct($config);
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
		$query = $this->_db->getQuery(true)
			->select($this->_db->qn('SubmissionValueId'))
			->from($this->_db->qn('#__rsform_submission_values'))
			->where($this->_db->qn('FieldName') . ' = ' . $this->_db->q($field))
			->where($this->_db->qn('FormId') . ' = ' . $this->_db->q($this->getFormId()));

		$this->_db->setQuery($query);
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
	
	public function getSpecialFields()
	{
		return RSFormProHelper::getDirectoryFormProperties($this->getFormId(), true);
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
			$app	= JFactory::getApplication();

			$query = $this->_db->getQuery(true)
				->select($this->_db->qn('MultipleSeparator'))
				->select($this->_db->qn('TextareaNewLines'))
				->from($this->_db->qn('#__rsform_forms'))
				->where($this->_db->qn('FormId') . ' = ' . $this->_db->q($formId));

			$form = $this->_db->setQuery($query)->loadObject();
			if (empty($form))
			{
				return $this->_data;
			}
			
			$uploadFields 	= array();
			$multipleFields = array();
			$textareaFields = array();
			$fieldTypes = $this->getSpecialFields();
			if (isset($fieldTypes['uploadFields']))
			{
				$uploadFields = $fieldTypes['uploadFields'];	
			}
			if (isset($fieldTypes['multipleFields']))
			{
				$multipleFields = $fieldTypes['multipleFields'];	
			}
			if (isset($fieldTypes['textareaFields']))
			{
				$textareaFields = $fieldTypes['textareaFields'];	
			}
			
			$this->_db->setQuery("SET SQL_BIG_SELECTS=1")->execute();
			
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
				$must_escape = $app->input->get('view') == 'submissions' && $app->input->get('layout') == 'default';

				$query = $this->_db->getQuery(true)
					->select('*')
					->from($this->_db->qn('#__rsform_submission_values'))
					->where($this->_db->qn('SubmissionId') . ' IN (' . implode(',', $this->_db->q($submissionIds)) . ')');
				
				$results = $this->_db->setQuery($query)->loadObjectList();
				foreach ($results as $result)
				{
					// Check if this is an upload field
					if (in_array($result->FieldName, $uploadFields) && !empty($result->FieldValue) && !$this->export)
					{
						$files = RSFormProHelper::explode($result->FieldValue);

						$values = array();
						foreach ($files as $file)
						{
							$values[] = '<a href="index.php?option=com_rsform&amp;task=submissions.view.file&amp;id='.$result->SubmissionValueId.'&amp;file='.md5($file).'">'.RSFormProHelper::htmlEscape(basename($file)).'</a>';
						}

						$result->FieldValue = implode('<br />', $values);
					}
					else
					{
						// Check if this is a multiple field
						if (in_array($result->FieldName, $multipleFields))
						{
							$result->FieldValue = str_replace("\n", $form->MultipleSeparator, $result->FieldValue);
						}
						// Transform new lines
						elseif ($form->TextareaNewLines && in_array($result->FieldName, $textareaFields))
						{
							if ($must_escape)
							{
								$result->FieldValue = RSFormProHelper::htmlEscape($result->FieldValue);
							}
						}
						else
						{
							if ($must_escape)
							{
								$result->FieldValue = RSFormProHelper::htmlEscape($result->FieldValue);
							}
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
		$query = $this->_db->getQuery(true)
			->select('*')
			->from($this->_db->qn('#__rsform_submissions'))
			->where($this->_db->qn('SubmissionId') . ' = ' . $this->_db->q($this->getSubmissionId()));

		return $this->_db->setQuery($query)->loadObject();
	}
	
	public function getHeaders()
	{
		$db 	= JFactory::getDbo();
		$task 	= strtolower(JFactory::getApplication()->input->getWord('task'));

		$query = $db->getQuery(true)
			->select($db->qn('p.PropertyValue'))
			->from($db->qn('#__rsform_components', 'c'))
			->join('left', $db->qn('#__rsform_properties', 'p') . ' ON (' . $db->qn('c.ComponentId') . '=' . $db->qn('p.ComponentId') . ' AND ' . $db->qn('p.PropertyName') . ' = ' . $db->q('NAME') . ')')
			->join('left', $db->qn('#__rsform_component_types', 'ct') . ' ON (' . $db->qn('c.ComponentTypeId') . '=' . $db->qn('ct.ComponentTypeId') . ')')
			->where($db->qn('c.FormId') . ' = ' . $db->q($this->getFormId()))
			->where($db->qn('c.Published') . ' = ' . $db->q(1));

		if (strpos($task, 'submissionsexport') !== false)
		{
			$query->where($db->qn('ct.ComponentTypeId') . ' NOT IN (' . implode(',', $db->q(array(RSFORM_FIELD_BUTTON, RSFORM_FIELD_CAPTCHA, RSFORM_FIELD_FREETEXT, RSFORM_FIELD_SUBMITBUTTON))) . ')');
		}

		$query->order($db->qn('c.Order'));

		$headers = $this->_db->setQuery($query)->loadColumn();

        JFactory::getApplication()->triggerEvent('rsfp_bk_onGetSubmissionHeaders', array(&$headers, $this->getFormId()));

		return $headers;
	}
	
	public function getUnescapedFields()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->qn('p.PropertyValue'))
			->from($db->qn('#__rsform_components', 'c'))
			->join('left', $db->qn('#__rsform_properties', 'p') . ' ON (' . $db->qn('c.ComponentId') . '=' . $db->qn('p.ComponentId') . ')')
			->where($db->qn('c.FormId') . ' = ' . $db->q($this->getFormId()))
			->where($db->qn('c.ComponentTypeId') . ' = ' . $db->q(RSFORM_FIELD_FILEUPLOAD))
			->where($db->qn('p.PropertyName') . ' = ' . $db->q('NAME'));
		$fields = $db->setQuery($query)->loadColumn();
		
		JFactory::getApplication()->triggerEvent('rsfp_b_onManageSubmissionsCreateUnescapedFields', array(array(
            'formId'    => $this->getFormId(),
            'fields'    => &$fields
        )));
        
        return $fields;
		
	}
	
	public function getHeadersEnabled()
	{
		$return = new stdClass();
		$return->staticHeaders 	= array();
		$return->headers 		= array();

		$query = $this->_db->getQuery(true)
			->select($this->_db->qn('ColumnName'))
			->select($this->_db->qn('ColumnStatic'))
			->from($this->_db->qn('#__rsform_submission_columns'))
			->where($this->_db->qn('FormId') . ' = ' . $this->_db->q($this->getFormId()));

		$results = $this->_db->setQuery($query)->loadObjectList();
		
		foreach ($results as $result)
		{
			if ($result->ColumnStatic)
			{
				$return->staticHeaders[] = $result->ColumnName;
			}
			else
			{
				$return->headers[] = $result->ColumnName;
			}
		}
		
		return $return;
	}
	
	public function getStaticHeaders()
	{		
		$return = array('SubmissionId', 'DateSubmitted', 'UserIp', 'Username', 'UserId', 'Lang');

		if ($this->addConfirmedHeader())
		{
			$return[] = 'confirmed';
		}
		
		return $return;
	}
	
	public function addConfirmedHeader()
	{
		static $found = null;
		if (is_null($found)) {
			$formId = $this->getFormId();

			$query = $this->_db->getQuery(true)
				->select($this->_db->qn('ConfirmSubmission'))
				->from($this->_db->qn('#__rsform_forms'))
				->where($this->_db->qn('FormId') . ' = ' . $this->_db->q($formId));

			$this->_db->setQuery($query);
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

		if ($translations = RSFormProHelper::getTranslations('forms', $formId, RSFormProHelper::getCurrentLanguage($formId)))
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
		$mainframe 	= JFactory::getApplication();
		$db        	= JFactory::getDbo();
		$sortColumn = $mainframe->getUserState('com_rsform.forms.filter_order', 'FormId');
		$sortOrder  = $mainframe->getUserState('com_rsform.forms.filter_order_Dir', 'ASC');
		$return 	= array();

        $query = $db->getQuery(true)
            ->select($db->qn('FormId'))
            ->select($db->qn('FormTitle'))
            ->select($db->qn('Lang'))
            ->from($db->qn('#__rsform_forms'))
            ->order($db->qn($sortColumn) . ' ' . $db->escape($sortOrder));
        if ($results = $db->setQuery($query)->loadObjectList())
        {
            foreach ($results as $result)
            {
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
			{
				$this->firstFormId = $results[0]->FormId;
			}
        }
		
		return $return;
	}
	
	public function getSortColumn()
	{
		return JFactory::getApplication()->getUserStateFromRequest('com_rsform.submissions.filter_order', 'filter_order', 'DateSubmitted', 'string');
	}
	
	public function getSortOrder()
	{
		return JFactory::getApplication()->getUserStateFromRequest('com_rsform.submissions.filter_order_Dir', 'filter_order_Dir', 'DESC', 'word');
	}
	
	public function getFilter()
	{
		return JFactory::getApplication()->getUserStateFromRequest('com_rsform.submissions.filter', 'search', '');
	}
	
	public function getFormId()
	{
		$mainframe = JFactory::getApplication();
		
		if (empty($this->firstFormId))
		{
			$this->getForms();
		}

		$formId = $mainframe->getUserStateFromRequest('com_rsform.submissions.formId', 'formId', $this->firstFormId, 'int');
		if ($formId && !in_array($formId, $this->allFormIds))
		{
			$formId = $this->firstFormId;
			$mainframe->setUserState('com_rsform.submissions.formId', $formId);
		}
		
		return $formId;
	}
	
	public function getSubmissionId()
	{
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');

		if (is_array($cid))
		{
			$cid = (int) reset($cid);
		}
		else
		{
			$cid = (int) $cid;
		}
		
		return $cid;
	}
	
	public function getEditFields()
	{
        $mainframe  = JFactory::getApplication();
        $db         = $this->_db;
		$isPDF      = $mainframe->input->get('format') == 'pdf';
		$cid        = $this->getSubmissionId();
		$return     = array();

        require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/submissions.php';
        $submission = RSFormProSubmissionsHelper::getSubmission($cid);
		
		if (empty($submission))
		{
			$mainframe->redirect('index.php?option=com_rsform&task=submissions.manage');
			return $return;
		}
		
		if ($isPDF)
		{
		    $query = $this->_db->getQuery(true)
                ->select($this->_db->qn('MultipleSeparator'))
                ->select($this->_db->qn('TextareaNewLines'))
                ->from($this->_db->qn('#__rsform_forms'))
                ->where($this->_db->qn('FormId') . ' = ' . $this->_db->q($submission->FormId));
			$form = $this->_db->setQuery($query)->loadObject();

			$form->MultipleSeparator = str_replace(array('\n', '\r', '\t'), array("\n", "\r", "\t"), $form->MultipleSeparator);
		}

        $query = $db->getQuery(true);
        $query->select($db->qn('p.PropertyValue'))
            ->select($db->qn('ct.ComponentTypeName'))
            ->select($db->qn('c.ComponentId'))
            ->from($db->qn('#__rsform_components','c'))
            ->join('left', $db->qn('#__rsform_properties','p').' ON '.$db->qn('p.ComponentId').' = '.$db->qn('c.ComponentId'))
            ->join('left', $db->qn('#__rsform_component_types','ct').' ON '.$db->qn('c.ComponentTypeId').' = '.$db->qn('ct.ComponentTypeId'))
            ->where($db->qn('c.FormId') . ' = ' . $db->q($submission->FormId))
            ->where($db->qn('p.PropertyName') . ' = ' . $db->q('NAME'))
            ->where($db->qn('c.Published') . ' = ' . $db->q(1))
            ->order($db->qn('c.Order') . ' ' . $db->escape('asc'));
        $fields = $db->setQuery($query)->loadObjectList();

		if (empty($fields))
        {
            return $return;
        }

		$componentIds = array();
		foreach ($fields as $field)
        {
            $componentIds[] = $field->ComponentId;
        }

		$properties = RSFormProHelper::getComponentProperties($componentIds);

		foreach ($fields as $field)
		{
			$data = $properties[$field->ComponentId];
            $name = $field->PropertyValue;
			
			$new_field = array();
			$new_field[0] = $name;
			$new_field[3] = $name;

			$value = isset($submission->values[$field->PropertyValue]) ? $submission->values[$field->PropertyValue] : '';
			
			switch ($field->ComponentTypeName)
			{
				// skip this field for now, no need to edit it
				case 'freeText':
					continue 2;
				break;
				
				default:
					if ($isPDF)
					{
						$new_field[1] = RSFormProHelper::htmlEscape($value);
					}
					else
					{
						if (strpos($value, "\n") !== false || strpos($value, "\r") !== false)
						{
							$new_field[1] = '<textarea style="width: 95%" class="rs_textarea" rows="10" cols="60" name="form['.$name.']">'.RSFormProHelper::htmlEscape($value).'</textarea>';
						}
						else
						{
							$new_field[1] = '<input class="rs_inp rs_80" size="105" type="text" name="form['.$name.']" value="'.RSFormProHelper::htmlEscape($value).'" />';
						}
					}
				break;
				
				case 'textArea':
					if ($isPDF)
					{
						if ($form->TextareaNewLines && (!isset($data['WYSIWYG']) || $data['WYSIWYG'] == 'NO'))
                        {
                            $value = nl2br(RSFormProHelper::htmlEscape($value));
                        }

						$new_field[1] = $value;
					}
					elseif (isset($data['WYSIWYG']) && $data['WYSIWYG'] == 'YES')
					{
						$new_field[1] = RSFormProHelper::WYSIWYG('form['.$name.']', RSFormProHelper::htmlEscape($value), '', 600, 100, 60, 10);
					}
					else
					{
						$new_field[1] = '<textarea style="width: 95%" class="rs_textarea" rows="10" cols="60" name="form['.$name.']">'.RSFormProHelper::htmlEscape($value).'</textarea>';
					}
				break;
				
				case 'radioGroup':
				case 'checkboxGroup':
				case 'selectList':
					if ($isPDF)
					{
						$new_field[1] = str_replace("\n", $form->MultipleSeparator, $value);
						break;
					}

					$options = array();
					
					if ($field->ComponentTypeName == 'radioGroup')
					{
						$data['SIZE'] = 0;
						$data['MULTIPLE'] = 'NO';
						$options[] = JHtml::_('select.option', '', JText::_('COM_RSFORM_NO_VALUE'));
					}
					elseif ($field->ComponentTypeName == 'checkboxGroup')
					{
						$data['SIZE'] = 5;
						$data['MULTIPLE'] = 'YES';
					}
					
					$value = RSFormProHelper::explode($value);

                    require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/fielditem.php';
                    require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fieldmultiple.php';
                    $f = new RSFormProFieldMultiple(array(
                        'formId' 			=> $submission->FormId,
                        'componentId' 		=> $field->ComponentId,
                        'data' 				=> $data,
                        'value' 			=> array('formId' => $submission->FormId, $data['NAME'] => $value),
                        'invalid' 			=> false
                    ));

                    if ($items = $f->getItems())
                    {
                        foreach ($items as $item)
                        {
                            $item = new RSFormProFieldItem($item);

                            if ($item->flags['optgroup'])
                            {
                                $options[] = JHtml::_('select.option', '<OPTGROUP>', $item->label, 'value', 'text');
                            }
                            elseif ($item->flags['/optgroup'])
							{
                                $options[] = JHtml::_('select.option', '</OPTGROUP>', $item->label, 'value', 'text');
                            }
                            else
                            {
                                $options[] = JHtml::_('select.option', $item->value, $item->label, 'value', 'text', $item->flags['disabled']);
                            }
                        }
                    }

                    $attribs = array();

                    if ((int) $data['SIZE'] > 0)
                    {
                        $attribs[] = 'size="'.(int) $data['SIZE'].'"';
                    }

                    if ($data['MULTIPLE'] == 'YES')
                    {
                        $attribs[] = 'multiple="multiple"';
                    }

                    $attribs = implode(' ', $attribs);
					
					$new_field[1] = JHtml::_('select.genericlist', $options, 'form['.$name.'][]', $attribs, 'value', 'text', $value);
				break;
				
				case 'fileUpload':
					if ($isPDF)
					{
						if (!empty($data['FILESSEPARATOR']))
						{
							$separator = str_replace(array('\n', '\r', '\t'), array("\n", "\r", "\t"), $data['FILESSEPARATOR']);
						}
						else
						{
							$separator = '<br />';
						}

						$new_field[1] = implode($separator, RSFormProHelper::explode($value));

						break;
					}

					if ($value)
					{
						$files = RSFormProHelper::explode($value);
					}
					else
					{
						$files = array();
					}

					$new_field[1] = '<ul class="rsfp-multiupload-list">';

					foreach ($files as $file)
					{
						$new_field[1] .= '<li><button type="button" class="btn btn-mini" onclick="RSFormPro.removeFile(this);">' . JText::_('COM_RSFORM_REMOVE_FILE') . '</button> <input type="hidden" name="form[' . $data['NAME'] . '][]" value="' . RSFormProHelper::htmlEscape($file) . '" />' . RSFormProHelper::htmlEscape(basename($file)) . '</li>';
					}

					$multiple =  !empty($data['MULTIPLE']) && $data['MULTIPLE'] == 'YES';
					$new_field[1] .= '</ul>';

					$new_field[1] .= '<input size="45" type="file" name="upload['.$name.']' . ($multiple ? '[]' : '') . '" ' . ($multiple ? 'multiple' : '') . ' />';


				break;
			}
			
			$return[] = $new_field;
		}

        $mainframe->triggerEvent('rsfp_bk_onGetEditFields', array(&$return, $submission));
		
		return $return;
	}
	
	public function save()
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		
		$app	= JFactory::getApplication();
        $formId = $app->input->getInt('formId');
		$cid    = $this->getSubmissionId();
		$form   = $app->input->post->get('form', array(), 'array');
        $static = $app->input->post->get('formStatic', array(), 'array');
        $files  = $app->input->files->get('upload', array(), 'array');
		$date	= JFactory::getDate($static['DateSubmitted'], JFactory::getConfig()->get('offset'));

		$static['DateSubmitted'] = $date->toSql();

		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/submissions.php';
		$submission = RSFormProSubmissionsHelper::getSubmission($cid);

		// Check if submission exists
		if (!$submission)
		{
			return false;
		}

		// Handle file uploads first
		if (!empty($files))
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/fileupload.php';

            foreach ($files as $field => $file)
            {
				if ($componentId = RSFormProHelper::getComponentId($field, $formId))
				{
					$data = RSFormProHelper::getComponentProperties($componentId);

					// If we've cleared all files
					if (!isset($form[$field]))
					{
						$form[$field] = array();
					}

					$valueSent = (array) $form[$field];

					if (isset($submission->values[$field]))
					{
						$originals = RSFormProHelper::explode($submission->values[$field]);

						foreach ($originals as $k => $original)
						{
							// File has been removed from list, remove the original file to save up space
							if (!in_array($original, $valueSent) && file_exists($original) && is_file($original))
							{
								JFile::delete($original);
							}
						}
					}

					$f = new RSFormProFieldFileUpload(array(
						'formId' 		=> $formId,
						'componentId' 	=> $componentId,
						'data' 			=> $data,
					));

					if ($object = $f->processBeforeStore($submission->SubmissionId, $form, $files, false))
					{
						if (!is_array($form[$field]))
						{
							$form[$field] = (array) $form[$field];
						}

						if (!empty($data['MULTIPLE']) && $data['MULTIPLE'] === 'YES')
						{
							$form[$field] = array_merge($form[$field], RSFormProHelper::explode($object->FieldValue));
						}
						else
						{
							$form[$field] = RSFormProHelper::explode($object->FieldValue);
						}
					}
				}
            }
        }

        // Static submission data
        if ($static)
        {
            $object = new stdClass();
			$object->SubmissionId = $submission->SubmissionId;
            foreach ($static as $field => $value)
            {
				$object->{$field} = $value;
            }

            $this->_db->updateObject('#__rsform_submissions', $object, array('SubmissionId'));
        }

		// Update dynamic fields
		foreach ($form as $field => $value)
		{
			if (is_array($value))
            {
                $value = implode("\n", $value);
            }

			$object = (object) array(
				'FormId'        => $formId,
				'SubmissionId'  => $submission->SubmissionId,
				'FieldName'     => $field,
				'FieldValue'    => $value
			);

			if (!isset($submission->values[$field]))
			{
			    $this->_db->insertObject('#__rsform_submission_values', $object);
			}
			elseif ($submission->values[$field] !== $value)
			{
				// Update only if we've changed something
				$this->_db->updateObject('#__rsform_submission_values', $object, array('SubmissionId', 'FormId', 'FieldName'));
			}
		}
		
		// Checkboxes don't send a value if nothing is checked
		$query = $this->_db->getQuery(true)
			->select($this->_db->qn('p.PropertyValue'))
			->from($this->_db->qn('#__rsform_components', 'c'))
			->join('left', $this->_db->qn('#__rsform_properties', 'p') . ' ON (' . $this->_db->qn('c.ComponentId') . ' = ' . $this->_db->qn('p.ComponentId') . ')')
			->where($this->_db->qn('c.ComponentTypeId') . ' = ' . $this->_db->q(RSFORM_FIELD_CHECKBOXGROUP))
			->where($this->_db->qn('p.PropertyName') . ' = ' .$this->_db->q('NAME'))
			->where($this->_db->qn('c.FormId') . ' = ' . $this->_db->q($formId));
		$checkboxes = $this->_db->setQuery($query)->loadColumn();
		foreach ($checkboxes as $checkbox)
		{
			$value = isset($form[$checkbox]) ? $form[$checkbox] : '';
			if (is_array($value))
			{
				$value = implode("\n", $value);
			}

			$query->clear()
				->update($this->_db->qn('#__rsform_submission_values'))
				->set($this->_db->qn('FieldValue') . ' = ' . $this->_db->q($value))
				->where($this->_db->qn('FieldName') . ' = ' . $this->_db->q($checkbox))
				->where($this->_db->qn('FormId') . ' = ' . $this->_db->q($formId))
				->where($this->_db->qn('SubmissionId') . ' = ' . $this->_db->q($cid));

			$this->_db->setQuery($query)->execute();
		}
	}
	
	public function getSubmissionFormId()
	{
		$query = $this->_db->getQuery(true)
			->select($this->_db->qn('FormId'))
			->from($this->_db->qn('#__rsform_submissions'))
			->where($this->_db->qn('SubmissionId') . ' = ' . $this->_db->q($this->getSubmissionId()));

		return $this->_db->setQuery($query)->loadResult();
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

		$query = $this->_db->getQuery(true)
			->select('*')
			->from($this->_db->qn('#__rsform_submissions'))
			->where($this->_db->qn('SubmissionId') . ' = ' . $this->_db->q($submissionid));
		
		$this->_db->setQuery($query);
		$submission = $this->_db->loadObject();
		
		if ($submission)
		{
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

	public function getImportTotal()
    {
        $config = JFactory::getConfig();
        $file   = $config->get('tmp_path') . '/' . md5($config->get('secret'));

        return file_exists($file) ? filesize($file) : 0;
    }
	
	public function getLanguages()
	{
		$languages 	= JFactory::getLanguage()->getKnownLanguages(JPATH_SITE);
		$return 	= array();

		$return[] = JHtml::_('select.option', '', JText::_('RSFP_SUBMISSIONS_ALL_LANGUAGES'));
		foreach ($languages as $tag => $properties)
		{
			$return[] = JHtml::_('select.option', $tag, $properties['name']);
		}

		return $return;
	}
	
	public function getLang()
	{
		return JFactory::getApplication()->getUserStateFromRequest('com_rsform.submissions.lang', 'Language', '');
	}
	
	public function getRSTabs()
	{
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

	public function getPreviewImportData()
    {
        $session    = JFactory::getSession();
        $config     = JFactory::getConfig();
        $file       = $config->get('tmp_path') . '/' . md5($config->get('secret'));
        $options    = $session->get('com_rsform.import.options', array());

        $skipHeaders    = !empty($options['skipHeaders']);
        $delimiter      = empty($options['delimiter']) ? ',' : $options['delimiter'];
        $enclosure      = empty($options['enclosure']) ? '"' : $options['enclosure'];
        $lines          = array();

        ini_set('auto_detect_line_endings', true);
        setlocale(LC_ALL, 'en_US.UTF-8');

        $h = fopen($file, 'r');

        if (is_resource($h))
        {
            for ($i = 0; $i < 5; $i++)
            {
                $data = fgetcsv($h, 0, $delimiter, $enclosure);

                if ($data !== false)
                {
                    if ($skipHeaders && $i == 0)
                    {
                        continue;
                    }
                    $lines[] = $data;
                }
                else
                {
                    break;
                }
            }
            fclose($h);
        }

        return $lines;
    }

    public function confirm($cid)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->update($db->qn('#__rsform_submissions'))
			->set($db->qn('confirmed') . ' = ' . $db->q(1))
			->where($db->qn('SubmissionId') . ' IN (' . implode(',', $db->q($cid)) . ')');

		return $db->setQuery($query)->execute();
	}
}