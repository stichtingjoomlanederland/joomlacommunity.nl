<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformModelSubmissions extends JModelLegacy
{
	public $_form = null;
	public $_data = array();
	public $_total = 0;
	public $_query = '';
	public $_pagination = null;
	public $_db = null;
	
	public $formId = 1;
	public $params;
	public $replacements;
	
	public function __construct()
	{
		parent::__construct();
		
		$app 			= JFactory::getApplication();
		$this->_db 		= JFactory::getDbo();
		$this->params 	= $app->getParams('com_rsform');
		$this->formId 	= (int) $this->params->get('formId');
		
		// The parameter is not enabled, throw an error to prevent other people from crafting a link and seeing submissions
		if (!$this->params->get('enable_submissions', 0)) {
		    $app->enqueueMessage(JText::_('RSFP_VIEW_SUBMISSIONS_NOT_ENABLED_FORGOT'), 'warning');
			return $app->redirect(JUri::root());
		}
		
		// Get pagination request variables
		$limit		= $app->input->get('limit', JFactory::getConfig()->get('list_limit'), 'int');
		$limitstart	= $app->input->get('limitstart', 0, 'int');

		$mainframe = JFactory::getApplication();
		$previousFiltersHash = $mainframe->getUserState('com_rsform.submissions.currentfilterhash', '');

		// get the current filters hashes
		$currentFiltersHash = $this->getFiltersHash();

		// reset the pagination if the filters are not the same
		if ($previousFiltersHash != $currentFiltersHash)
		{
			$limitstart = 0;
		}

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('com_rsform.submissions.formId'.$this->formId.'.limit', $limit);
		$this->setState('com_rsform.submissions.formId'.$this->formId.'.limitstart', $limitstart);
		
		$this->_query = $this->_buildQuery();
	}
	
	public function getForm() {
		if (empty($this->_form)) {
			$this->_db->setQuery("SELECT * FROM #__rsform_forms WHERE FormId='".$this->formId."'");
			$this->_form = $this->_db->loadObject();
			
			$this->_form->MultipleSeparator = str_replace(array('\n', '\r', '\t'), array("\n", "\r", "\t"), $this->_form->MultipleSeparator);
		}
		
		return $this->_form;
	}
	
	public function _buildQuery()
	{
		$query  = "SELECT SQL_CALC_FOUND_ROWS DISTINCT(sv.SubmissionId), s.* FROM #__rsform_submissions s";
		$query .= " LEFT JOIN #__rsform_submission_values sv ON (s.SubmissionId=sv.SubmissionId)";
		$query .= " WHERE s.FormId='".$this->formId."'";
		
		$filter = $this->_db->escape($this->getFilter());
		
		$confirmed = $this->params->get('show_confirmed', 0);
		if ($confirmed)
			$query .= " AND s.confirmed='1'";
		
		$lang = $this->params->get('lang', '');
		if ($lang)
			$query .= " AND s.Lang='".$this->_db->escape($lang)."'";
		
		if ($filter != '')
		{
			$query .= " AND (sv.FieldValue LIKE '%".$filter."%'";
			if (!preg_match('#([^0-9\-: ])#', $filter))
				$query .= " OR s.DateSubmitted LIKE '%".$filter."%'";
			$query .= " OR s.Username LIKE '%".$filter."%'";
			$query .= " OR s.UserIp LIKE '%".$filter."%')";
		}
		
		$userId = $this->params->def('userId', 0);
		if ($userId == 'login')
		{
			$user = JFactory::getUser();
			if ($user->get('guest'))
				$query .= " AND 1>2";
			
			$query .= " AND s.UserId='".(int) $user->get('id')."'";
		}
		elseif ($userId == 0)
		{
			// Show all submissions
		}
		else
		{
			$userId = explode(',', $userId);
			$userId = array_map('intval', $userId);
			
			$query .= " AND s.UserId IN (".implode(',', $userId).")";
		}
		
		$dir = $this->params->get('sort_submissions') ? 'ASC' : 'DESC';
		
		$query .= " ORDER BY s.DateSubmitted $dir";

		// set the current filters hash
		JFactory::getApplication()->setUserState('com_rsform.submissions.currentfilterhash', $this->getFiltersHash());
		
		return $query;
	}

	protected function getFiltersHash() {
		static $hash;

		if (is_null($hash))
		{
			$filter          = $this->_db->escape($this->getFilter());
			$lang            = $this->params->get('lang', '');

			$currentFiltersHash = $filter . $lang;
			$hash = md5($currentFiltersHash);
		}

		return $hash;
	}
	
	public function getPagination() {
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('com_rsform.submissions.formId'.$this->formId.'.limitstart'), $this->getState('com_rsform.submissions.formId'.$this->formId.'.limit'));
		}
		
		return $this->_pagination;
	}
	
	public function getTotal() {
		return $this->_total;
	}
	
	public function getSubmissions() {
		if (empty($this->_data))
		{
			jimport('joomla.filesystem.file');
			
			$this->getComponents();

			$this->_db->setQuery("SET SQL_BIG_SELECTS=1");
			$this->_db->execute();
			
			$submissionIds = array();
			
			$this->_db->setQuery($this->_query, $this->getState('com_rsform.submissions.formId'.$this->formId.'.limitstart'), $this->getState('com_rsform.submissions.formId'.$this->formId.'.limit'));
			$results = $this->_db->loadObjectList();
			$this->_db->setQuery("SELECT FOUND_ROWS()");
			$this->_total = $this->_db->loadResult();
			foreach ($results as $result)
			{
				$submissionIds[] = $result->SubmissionId;
				
				$this->_data[$result->SubmissionId]['FormId'] = $result->FormId;
				$this->_data[$result->SubmissionId]['DateSubmitted'] = $result->DateSubmitted;
				$this->_data[$result->SubmissionId]['UserIp'] = $result->UserIp;
				$this->_data[$result->SubmissionId]['Username'] = $result->Username;
				$this->_data[$result->SubmissionId]['UserId'] = $result->UserId;
				$this->_data[$result->SubmissionId]['Lang'] = $result->Lang;
				$this->_data[$result->SubmissionId]['confirmed'] = $result->confirmed ? JText::_('RSFP_YES') : JText::_('RSFP_NO');
				$this->_data[$result->SubmissionId]['SubmissionValues'] = array();
			}
			
			$form = $this->getForm();
			
			if (!empty($submissionIds))
			{
				$this->_db->setQuery("SELECT * FROM `#__rsform_submission_values` WHERE `SubmissionId` IN (".implode(',',$submissionIds).")");
				$results = $this->_db->loadObjectList();
				
				$config = JFactory::getConfig();
				$secret = $config->get('secret');
				foreach ($results as $result)
				{
					// Check if this is an upload field
					if (in_array($result->FieldName, $this->uploadFields) && !empty($result->FieldValue))
					{
						$result->FilePath = $result->FieldValue;
						$result->FieldValue = '<a href="'.JUri::root().'index.php?option=com_rsform&amp;task=submissions.view.file&amp;hash='.md5($result->SubmissionId.$secret.$result->FieldName).'">'.JFile::getName($result->FieldValue).'</a>';
					}
					// Check if this is a multiple field
					elseif (in_array($result->FieldName, $this->multipleFields))
						$result->FieldValue = str_replace("\n", $form->MultipleSeparator, $result->FieldValue);
					elseif ($form->TextareaNewLines && in_array($result->FieldName, $this->textareaFields))
						$result->FieldValue = nl2br($result->FieldValue);
						
					$this->_data[$result->SubmissionId]['SubmissionValues'][$result->FieldName] = array('Value' => $result->FieldValue, 'Id' => $result->SubmissionValueId);
					if (in_array($result->FieldName, $this->uploadFields) && !empty($result->FieldValue))
					{
						$filepath = $result->FilePath;
						$filepath = str_replace(JPATH_SITE.DIRECTORY_SEPARATOR, JUri::root(), $filepath);
						$filepath = str_replace(array('\\', '\\/', '//\\'), '/', $filepath);
						
						$this->_data[$result->SubmissionId]['SubmissionValues'][$result->FieldName]['Path'] = $filepath;
					}
				}
			}
			unset($results);
		}
		
		return $this->_data;
	}
	
	public function getReplacements($user_id) {
		static $sitename, $siteurl, $mailfrom, $fromname;
		
		if (is_null($siteurl)) {
			$config 	= JFactory::getConfig();
			$sitename 	= $config->get('sitename');
			$siteurl	= JUri::root();
			$mailfrom	= $config->get('mailfrom');
			$fromname	= $config->get('fromname');
		}
		
		$user    = JFactory::getUser((int) $user_id);
		$replace = array('{global:sitename}', '{global:siteurl}', '{global:userid}', '{global:username}', '{global:email}', '{global:useremail}', '{global:fullname}', '{global:mailfrom}', '{global:fromname}', '{/details}', '{/detailspdf}');
		$with 	 = array($sitename, $siteurl, $user->id, $user->username, $user->email, $user->email, $user->name, $mailfrom, $fromname, '</a>', '</a>');
		
		$this->replacements = array($replace, $with);
		
		return $this->replacements;
	}
	
	public function getComponents() {
		$this->_db->setQuery("SELECT c.ComponentTypeId, p.ComponentId, p.PropertyName, p.PropertyValue FROM #__rsform_components c LEFT JOIN #__rsform_properties p ON (c.ComponentId=p.ComponentId) WHERE c.FormId='".$this->formId."' AND c.Published='1' AND p.PropertyName IN ('NAME', 'WYSIWYG')");
		$components = $this->_db->loadObjectList();
		$this->uploadFields   = array();
		$this->multipleFields = array();
		$this->textareaFields = array();
		
		foreach ($components as $component)
		{
			// Upload fields
			if ($component->ComponentTypeId == RSFORM_FIELD_FILEUPLOAD)
			{
				$this->uploadFields[] = $component->PropertyValue;
			}
			// Multiple fields
			elseif (in_array($component->ComponentTypeId, array(RSFORM_FIELD_SELECTLIST, RSFORM_FIELD_CHECKBOXGROUP)))
			{
				$this->multipleFields[] = $component->PropertyValue;
			}
			// Textarea fields
			elseif ($component->ComponentTypeId == RSFORM_FIELD_TEXTAREA)
			{
				if ($component->PropertyName == 'WYSIWYG' && $component->PropertyValue == 'NO')
					$this->textareaFields[] = $component->ComponentId;
			}
		}
		
		if (!empty($this->textareaFields))
		{
			$this->_db->setQuery("SELECT p.PropertyValue FROM #__rsform_components c LEFT JOIN #__rsform_properties p ON (c.ComponentId=p.ComponentId) WHERE c.ComponentId IN (".implode(',', $this->textareaFields).")");
			$this->textareaFields = $this->_db->loadColumn();
		}
	}
	
	public function getHeaders() {
		$query  = "SELECT p.PropertyValue FROM #__rsform_components c";
		$query .= " LEFT JOIN #__rsform_properties p ON (c.ComponentId=p.ComponentId AND p.PropertyName='NAME')";
		$query .= " LEFT JOIN #__rsform_component_types ct ON (c.ComponentTypeId=ct.ComponentTypeId)";
		$query .= " WHERE c.FormId='".$this->formId."' AND c.Published='1'";
		
		$this->_db->setQuery($query);
		$headers = $this->_db->loadColumn();
		
		return $headers;
	}
	
	public function getTemplate() {
		$app 		= JFactory::getApplication();
		$db 		= JFactory::getDbo();
		$Itemid		= $this->getItemId();
		$has_suffix = JFactory::getConfig()->get('sef') && JFactory::getConfig()->get('sef_suffix');
		$layout 	= $app->input->getCmd('layout', 'default');
		
		// Templates
		$template_module      = $this->params->def('template_module', '');
		$template_formdatarow = $this->params->def('template_formdatarow', '');
		$template_formdetail  = $this->params->def('template_formdetail', '');

        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->qn('#__rsform_forms'))
            ->where($db->qn('FormId') . ' = ' . $db->q($this->params->get('formId')));

        $form = $db->setQuery($query)->loadObject();
		
		if ($layout == 'default') {
			$formdata 		= '';
			$submissions 	= $this->getSubmissions();
			$headers 		= $this->getHeaders();
			$pagination 	= $this->getPagination();
			
			$i = 0;
			foreach ($submissions as $SubmissionId => $submission)
			{
				list($replace, $with) = $this->getReplacements($submission['UserId']);
				
				$pdf_link = JRoute::_('index.php?option=com_rsform&view=submissions&layout=view&cid='.$SubmissionId.'&format=pdf'.$Itemid);
				if ($has_suffix) {
					$pdf_link .= strpos($pdf_link, '?') === false ? '?' : '&';
					$pdf_link .= 'format=pdf';
				}
				$details_link = JRoute::_('index.php?option=com_rsform&view=submissions&layout=view&cid='.$SubmissionId.$Itemid);
				
				$replacements = array(
					// Global placeholders
					'{global:userip}' 		 => $submission['UserIp'],
					'{global:date_added}' 	 => RSFormProHelper::getDate($submission['DateSubmitted']),
					'{global:submissionid}'  => $SubmissionId,
					'{global:submission_id}' => $SubmissionId,
					'{global:counter}'		 => $pagination->getRowOffset($i),
					'{global:naturalcounter}'=> $this->params->get('sort_submissions') ? $pagination->getRowOffset($i) : ($pagination->total + 1 - $pagination->getRowOffset($i)),
					'{global:confirmed}'	 => $submission['confirmed'],
					// Details links
					'{details}'				 => '<a href="'.$details_link.'">',
					'{details_link}'		 => $details_link,
					// PDF links
					'{detailspdf}'			 => '<a href="'.$pdf_link.'">',
					'{detailspdf_link}'		 => $pdf_link,
					'{global:formid}'		 => $submission['FormId']
				);
				
				$replace = array_merge($replace, array_keys($replacements));
				$with 	 = array_merge($with, array_values($replacements));

                $app->triggerEvent('rsfp_onAfterCreatePlaceholders', array(array('form' => &$form, 'placeholders' => &$replace, 'values' => &$with, 'submission' => $this->convertSubmissionObject($submission, $SubmissionId))));
				
				foreach ($headers as $header) {
					if (!isset($submission['SubmissionValues'][$header]['Value']))
						$submission['SubmissionValues'][$header]['Value'] = '';
					
					if (!empty($submission['SubmissionValues'][$header]['Path'])) {
						$replace[] 	= '{'.$header.':path}';
						$with[] 	= $submission['SubmissionValues'][$header]['Path'];
					}
				}
				
				list($replace2, $with2) = RSFormProHelper::getReplacements($SubmissionId, true);
				$replace	= array_merge($replace, $replace2);
				$with		= array_merge($with, $with2);
				
				$rowdata = $template_formdatarow;
				
				// Add scripting
				if (strpos($rowdata, '{/if}') !== false) {
					require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/scripting.php';
					RSFormProScripting::compile($rowdata, $replace, $with);
				}
				
				$formdata .= str_replace($replace, $with, $rowdata);
				
				$i++;
			}
			
			$html = str_replace('{formdata}', $formdata, $template_module);
		} else {
			$cid 	= $app->input->getInt('cid');
			$format = $app->input->getCmd('format');
			$user   = JFactory::getUser();
			$userId = $this->params->def('userId', 0);
			if ($userId != 'login' && $userId != 0) {
				$userId = explode(',', $userId);
				$userId = array_map('intval', $userId);
			}
			
			// Grab submission
			$this->_db->setQuery("SELECT * FROM #__rsform_submissions WHERE SubmissionId='".$cid."'");
			$submission = $this->_db->loadObject();
			
			// Submission doesn't exist
			if (!$submission) {
                $app->enqueueMessage(JText::sprintf('RSFP_SUBMISSION_DOES_NOT_EXIST', $cid), 'warning');
				return $app->redirect(JUri::root());
			}
			
			// Submission doesn't belong to the configured form ID OR
			// can view only own submissions and not his own OR
			// can view only specified user IDs and this doesn't belong to any of the IDs
			if (($submission->FormId != $this->params->get('formId')) || ($userId == 'login' && $submission->UserId != $user->get('id')) || (is_array($userId) && !in_array($user->get('id'), $userId))) {
                $app->enqueueMessage(JText::sprintf('RSFP_SUBMISSION_NOT_ALLOWED', $cid), 'warning');
				return $app->redirect(JUri::root());
			}
			
			if ($this->params->get('show_confirmed', 0) && !$submission->confirmed) {
                $app->enqueueMessage(JText::sprintf('RSFP_SUBMISSION_NOT_CONFIRMED', $cid), 'warning');
				return $app->redirect(JUri::root());
			}
			
			$pdf_link = JRoute::_('index.php?option=com_rsform&view=submissions&layout=view&cid='.$cid.'&format=pdf'.$Itemid);
			if ($has_suffix) {
				$pdf_link .= strpos($pdf_link, '?') === false ? '?' : '&';
				$pdf_link .= 'format=pdf';
			}
			
			list($replace, $with) 	= RSFormProHelper::getReplacements($cid, true);
			list($replace2, $with2) = $this->getReplacements($submission->UserId);
			
			$replacements = array(
				// Global
				'{global:userip}' 		 => $submission->UserIp,
				'{global:date_added}'	 => RSFormProHelper::getDate($submission->DateSubmitted),
				'{global:submissionid}'	 => $cid,
				'{global:submission_id}' => $cid,
				'{global:confirmed}'	 => $submission->confirmed ? JText::_('RSFP_YES') : JText::_('RSFP_NO'),
				// PDF
				'{detailspdf}'			 => '<a href="'.$pdf_link.'">',
				'{detailspdf_link}'		 => $pdf_link,
				'{global:formid}'		 => $submission->FormId
			);
			
			$replace = array_merge($replace, $replace2, array_keys($replacements));
			$with 	 = array_merge($with, 	 $with2,    array_values($replacements));
			
			if ($format == 'pdf' && preg_match_all('#{detailspdf}(.*?){\/detailspdf}#is', $template_formdetail, $matches)) {
				foreach ($matches[0] as $fullmatch) {
					$template_formdetail = str_replace($fullmatch, '', $template_formdetail);
				}
			}
			
			// Add scripting
			if (strpos($template_formdetail, '{/if}') !== false) {
				require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/scripting.php';
				RSFormProScripting::compile($template_formdetail, $replace, $with);
			}
			
			$html = str_replace($replace, $with, $template_formdetail);
		}
		
		return $html;
	}
	
	public function getFilter() {
		$app 	= JFactory::getApplication();
		$formId = $this->formId;
		
		return $app->getUserStateFromRequest('com_rsform.submissions.formId'.$formId.'.filter', 'filter', '');
	}
	
	public function getItemid() {
		$itemid = JFactory::getApplication()->input->getInt('itemid');
		
		return !empty($itemid) ? '&Itemid='.$itemid : '';
	}

	private function convertSubmissionObject($submission, $SubmissionId)
    {
        $obj = new stdClass();
        $obj->SubmissionId = $SubmissionId;
        $obj->values = array();

        foreach ($submission as $key => $value)
        {
            if ($key == 'SubmissionValues')
            {
                foreach ($value as $fieldName => $fieldProperties)
                {
                    $obj->values[$fieldName] = $fieldProperties['Value'];
                }
            }
            else
            {
                $obj->{$key} = $value;
            }
        }

        return $obj;
    }
}