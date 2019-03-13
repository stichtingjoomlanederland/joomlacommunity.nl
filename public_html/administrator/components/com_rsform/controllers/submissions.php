<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformControllerSubmissions extends RsformController
{
    public $_db;

	public function __construct()
	{
		parent::__construct();
		
		$this->registerTask('apply', 			'save');
		$this->registerTask('exportCSV', 		'export');
		$this->registerTask('exportODS', 		'export');
		$this->registerTask('exportExcel', 		'export');
		$this->registerTask('exportExcelXML', 	'export');
		$this->registerTask('exportXML', 		'export');
		
		$this->_db = JFactory::getDbo();
	}

	public function manage()
	{
		$app	= JFactory::getApplication();
		$model	= $this->getModel('submissions');
		$formId = $model->getFormId();
		
		// if the form is changed we need to reset the limitstart
		$app->setUserState('com_rsform.submissions.limitstart', 0);
		
		$app->redirect('index.php?option=com_rsform&view=submissions'.($formId ? '&formId='.$formId : ''));
	}
	
	public function back() {
		$app	= JFactory::getApplication();
		$formId = $app->input->getInt('formId');
		$app->redirect('index.php?option=com_rsform&view=submissions&formId='.$formId);
	}
	
	public function edit()
	{
		$model = $this->getModel('submissions');
		$cid   = $model->getSubmissionId();
        JFactory::getApplication()->redirect('index.php?option=com_rsform&view=submissions&layout=edit&cid='.$cid);
	}
	
	public function columns()
	{
		$app 	= JFactory::getApplication();
		$formId = $app->input->getInt('formId');
		$query  = $this->_db->getQuery(true);
        $staticcolumns  = $app->input->get('staticcolumns', array(), 'raw');
        $columns        = $app->input->get('columns', array(), 'raw');

		$query->delete($this->_db->qn('#__rsform_submission_columns'))
            ->where($this->_db->qn('FormId') . ' = ' . $this->_db->q($formId));
		$this->_db->setQuery($query);
		$this->_db->execute();

		if ($staticcolumns || $columns) {
            $query->clear();
            $query->insert($this->_db->qn('#__rsform_submission_columns'))
                ->columns($this->_db->qn(array('FormId', 'ColumnName', 'ColumnStatic')));

            if ($staticcolumns) {
                foreach ($staticcolumns as $column) {
                    $query->values(implode(',', array($this->_db->q($formId), $this->_db->q($column), $this->_db->q(1))));
                }
            }

            if ($columns) {
                foreach ($columns as $column) {
                    $query->values(implode(',', array($this->_db->q($formId), $this->_db->q($column), $this->_db->q(0))));
                }
            }

            $this->_db->setQuery($query)->execute();
        }
		
		$app->redirect('index.php?option=com_rsform&view=submissions&formId='.$formId);
	}
	
	public function save()
	{
		// Get the model
		$model = $this->getModel('submissions');
		
		// Save
		$model->save();
		
		$task = $this->getTask();
		switch ($task)
		{
			case 'apply':
				$cid  = $model->getSubmissionId();
				$link = 'index.php?option=com_rsform&view=submissions&layout=edit&cid='.$cid;
			break;
		
			case 'save':
				$link = 'index.php?option=com_rsform&view=submissions';
			break;
		}
		
		$this->setRedirect($link, JText::_('RSFP_SUBMISSION_SAVED'));
	}
	
	public function resend()
	{
		$app 	= JFactory::getApplication();
		$formId = $app->input->getInt('formId');
        $cid	= $app->input->post->get('cid', array(), 'array');
		$cid = array_map('intval', $cid);
		
		foreach ($cid as $SubmissionId) {
            RSFormProHelper::sendSubmissionEmails($SubmissionId);
        }
		
		$this->setRedirect('index.php?option=com_rsform&view=submissions&formId='.$formId, JText::_('RSFP_SUBMISSION_MAILS_RESENT'));
	}
	
	public function cancel()
	{
		$app = JFactory::getApplication();
		$app->redirect('index.php?option=com_rsform');
	}
	
	public function cancelForm()
	{
		$app 	= JFactory::getApplication();
		$formId = $app->input->getInt('formId');
		$app->redirect('index.php?option=com_rsform&view=forms&layout=edit&formId='.$formId);
	}
	
	public function clear()
	{
	    JSession::checkToken('get') or jexit('Invalid Token');

        if (!JFactory::getUser()->authorise('submissions.manage', 'com_rsform'))
        {
            throw new Exception(JText::_('COM_RSFORM_NOT_AUTHORISED_TO_USE_THIS_SECTION'));
        }

        require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/submissions.php';

		$formId = JFactory::getApplication()->input->getInt('formId');
		$total  = RSFormProSubmissionsHelper::deleteAllSubmissions($formId);
		
		$this->setRedirect('index.php?option=com_rsform&view=forms', JText::sprintf('RSFP_SUBMISSIONS_CLEARED', $total));
	}
	
	public function delete()
	{
        require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/submissions.php';

		$app 	= JFactory::getApplication();
		$formId = $app->input->getInt('formId');
        $cid	= $app->input->post->get('cid', array(), 'array');
		$cid    = array_map('intval', $cid);

		RSFormProSubmissionsHelper::deleteSubmissions($cid);
		
		$app->redirect('index.php?option=com_rsform&view=submissions&formId='.$formId);
	}
	
	public function export()
	{
		$config   = JFactory::getConfig();
		$tmp_path = $config->get('tmp_path');
		if (!is_writable($tmp_path))
		{
            $app = JFactory::getApplication();
			$app->enqueueMessage(JText::sprintf('RSFP_EXPORT_ERROR_MSG', $tmp_path), 'warning');
			$app->redirect('index.php?option=com_rsform&view=submissions');
		}
		
		$view 	= $this->getView('submissions', 'html');
		$model 	= $this->getModel('submissions');
		$view->setLayout('export');
		$view->setModel($model, true);
		
		$view->display();
	}

	public function importCsv()
    {
        $app        = JFactory::getApplication();
        $config     = JFactory::getConfig();
        $tmp_path   = $config->get('tmp_path');
        $file       = $app->input->files->get('importFile');
        $options    = $app->input->get('import', array(), 'array');
        $session    = JFactory::getSession();

        $session->set('com_rsform.import.options', $options);

        try
        {
            if (!is_writable($tmp_path))
            {
                throw new Exception(JText::sprintf('COM_RSFORM_IMPORT_ERROR_MSG', $tmp_path));
            }

            if ($file['error'] != UPLOAD_ERR_OK)
            {
                // Parse the error message
                switch ($file['error'])
                {
                    default:
                        // File has not been uploaded correctly
                        throw new Exception(JText::_('RSFP_FILE_HAS_NOT_BEEN_UPLOADED_DUE_TO_AN_UNKNOWN_ERROR'));
                        break;

                    case UPLOAD_ERR_INI_SIZE:
                        throw new Exception(JText::_('RSFP_UPLOAD_ERR_INI_SIZE'));
                        break;

                    case UPLOAD_ERR_FORM_SIZE:
                        throw new Exception(JText::_('RSFP_UPLOAD_ERR_FORM_SIZE'));
                        break;

                    case UPLOAD_ERR_PARTIAL:
                        throw new Exception(JText::_('RSFP_UPLOAD_ERR_PARTIAL'));
                        break;

                    case UPLOAD_ERR_NO_TMP_DIR:
                        throw new Exception(JText::_('RSFP_UPLOAD_ERR_NO_TMP_DIR'));
                        break;

                    case UPLOAD_ERR_CANT_WRITE:
                        throw new Exception(JText::_('RSFP_UPLOAD_ERR_CANT_WRITE'));
                        break;

                    case UPLOAD_ERR_EXTENSION:
                        throw new Exception(JText::_('RSFP_UPLOAD_ERR_EXTENSION'));
                        break;

                    case UPLOAD_ERR_NO_FILE:
                        throw new Exception(JText::_('COM_RSFORM_PLEASE_UPLOAD_A_FILE'));
                        break;
                }
            }

            $extParts   = explode('.', $file['name']);
            $ext 	    = strtolower(end($extParts));

            if ($ext !== 'csv')
            {
                throw new Exception(JText::_('COM_RSFORM_PLEASE_UPLOAD_ONLY_CSV_FILES'));
            }

            jimport('joomla.filesystem.file');
            if (!JFile::upload($file['tmp_name'], $tmp_path . '/' . md5($config->get('secret'))))
            {
                throw new Exception(JText::_('COM_RSFORM_COULD_NOT_MOVE_FILE'));
            }
        }
        catch (Exception $e)
        {
            $app->enqueueMessage($e->getMessage(), 'error');
            $app->redirect('index.php?option=com_rsform&view=submissions');
        }

        $view 	= $this->getView('submissions', 'html');
        $model 	= $this->getModel('submissions');
        $view->setLayout('import');
        $view->setModel($model, true);

        $view->display();
    }

    public function importProcess()
    {
        $session    = JFactory::getSession();
        $config     = JFactory::getConfig();
        $db         = JFactory::getDbo();
        $app        = JFactory::getApplication();
        $model      = $this->getModel('submissions');
        $file       = $config->get('tmp_path') . '/' . md5($config->get('secret'));
        $options    = $session->get('com_rsform.import.options', array());

        $defaultLang    = JFactory::getLanguage()->getDefault();
        $defaultDate    = JFactory::getDate()->toSql();
        $skipHeaders    = !empty($options['skipHeaders']);
        $delimiter      = empty($options['delimiter']) ? ',' : $options['delimiter'];
        $enclosure      = empty($options['enclosure']) ? '"' : $options['enclosure'];
        $headers        = empty($options['headers']) ? array() : $options['headers'];
        $staticHeaders  = $model->getStaticHeaders();

        $start  = $app->input->getInt('importStart');
        $limit  = $app->input->getInt('importLimit', 500);
        $formId = $app->input->getInt('formId');

        ini_set('auto_detect_line_endings', true);
        setlocale(LC_ALL, 'en_US.UTF-8');

        $h = fopen($file, 'r');

        if (is_resource($h))
        {
            if ($start)
            {
                fseek($h, $start);
            }
            for ($i = 0; $i < $limit; $i++)
            {
                $data = fgetcsv($h, 0, $delimiter, $enclosure);

                if ($data !== false)
                {
                    if ($skipHeaders && !$start && $i == 0)
                    {
                        continue;
                    }

                    $tmpHeaders = $headers;

                    $submission = new stdClass();
                    $submission->FormId         = $formId;
                    $submission->DateSubmitted  = $defaultDate;
                    $submission->Lang           = $defaultLang;
                    foreach ($staticHeaders as $staticHeader)
                    {
                        if (($position = array_search($staticHeader, $tmpHeaders)) !== false)
                        {
                            $submission->{$staticHeader} = isset($data[$position]) ? $data[$position] : '';

                            unset($tmpHeaders[$position]);
                            unset($data[$position]);
                        }
                    }

                    // We've mapped a Submission ID, this means we should update values based on this, if it exists
                    $exists = false;
                    if (!empty($submission->SubmissionId))
                    {
                        $query = $db->getQuery(true)
                            ->select($db->qn('SubmissionId'))
                            ->from($db->qn('#__rsform_submissions'))
                            ->where($db->qn('SubmissionId') . ' = ' . $db->q($submission->SubmissionId))
                            ->where($db->qn('FormId') . ' = ' . $db->q($formId));
                        $exists = $db->setQuery($query)->loadResult() !== null;
                    }

                    if ($exists)
                    {
                        $db->updateObject('#__rsform_submissions', $submission, array('SubmissionId'));
                    }
                    else
                    {
                        $db->insertObject('#__rsform_submissions', $submission, 'SubmissionId');
                    }

                    foreach ($tmpHeaders as $position => $header)
                    {
                        $submissionValue = new stdClass();
                        $submissionValue->FormId        = $formId;
                        $submissionValue->SubmissionId  = $submission->SubmissionId;
                        $submissionValue->FieldName     = $header;
                        $submissionValue->FieldValue    = isset($data[$position]) ? $data[$position] : '';

                        if ($exists)
                        {
                            $query = $db->getQuery(true)
                                ->delete($db->qn('#__rsform_submission_values'))
                                ->where($db->qn('FieldName') . ' = ' . $db->q($submissionValue->FieldName))
                                ->where($db->qn('SubmissionId') . ' = ' . $db->q($submissionValue->SubmissionId));
                            $db->setQuery($query)->execute();
                        }

                        $db->insertObject('#__rsform_submission_values', $submissionValue);
                    }
                }
            }

            $offset = ftell($h);

            if (feof($h) || $offset === false)
            {
                echo 'END';
            }
            else
            {
                echo $offset;
            }

            fclose($h);
        }

        $app->close();
    }
	
	public function exportProcess()
	{
		$mainframe = JFactory::getApplication();
		
		$config = JFactory::getConfig();
	
		// Get post
		$session = JFactory::getSession();
		$post = $session->get('com_rsform.export.data', serialize(array()));
		$post = unserialize($post);
		
		// Limit
		$start = $mainframe->input->getInt('exportStart');
		$mainframe->setUserState('com_rsform.submissions.limitstart', $start);
		$limit = $mainframe->input->getInt('exportLimit', 500);
		$mainframe->setUserState('com_rsform.submissions.limit', $limit);
		
		// Tmp path
		$tmp_path = $config->get('tmp_path');
		$file = $tmp_path.'/'.$post['ExportFile'];
		
		$formId = $post['formId'];
		
		// Type
		$type = strtolower($post['exportType']);
		
		// Selected rows or all rows
		$rows = !empty($post['ExportRows']) ? explode(',', $post['ExportRows']) : '';
		
		// Use headers ?
		$use_headers = !empty($post['ExportHeaders']);
		
		// Headers and ordering
		$staticHeaders = $post['ExportSubmission'];
		$headers = $post['ExportComponent'];
		$order = $post['ExportOrder'];
		
		// Remove headers that we're not going to export
		foreach ($order as $name => $id)
		{
			if (!isset($staticHeaders[$name]) && !isset($headers[$name]))
				unset($order[$name]);
		}
		
		// Adjust order array
		$order = array_flip($order);
		ksort($order);
		
		$model = $this->getModel('submissions');
		$model->export = true;
		$model->rows = $rows;
		$model->_query = $model->_buildQuery();
		$submissions = $model->getSubmissions();
		
		// CSV Options
		if ($type == 'csv')
		{
			$delimiter = str_replace(array('\t', '\n', '\r'), array("\t","\n","\r"), $post['ExportDelimiter']);
			$enclosure = str_replace(array('\t', '\n', '\r'), array("\t","\n","\r"), $post['ExportFieldEnclosure']);
			
			// Create and open file for writing if this is the first call
			// If not, just append to the file
			// Using fopen() because JFile::write() lacks such options
			$handle = fopen($file, $start == 0 ? 'w' : 'a');
			
			if ($start == 0 && $use_headers)
			{
				fwrite($handle, $enclosure.implode($enclosure.$delimiter.$enclosure,$order).$enclosure);
				fwrite($handle, "\n");
			}
			
			if (empty($submissions))
			{
				fclose($handle);
				// Adjust pagination
				$mainframe->setUserState('com_rsform.submissions.limitstart', 0);
				$mainframe->setUserState('com_rsform.submissions.limit', JFactory::getConfig()->get('list_limit'));
				echo 'END';
			}
			else
			{
				foreach ($submissions as $submissionId => $submission)
				{
					foreach ($order as $orderId => $header)
					{
						if (isset($submission['SubmissionValues'][$header]))
						{
							$submission['SubmissionValues'][$header]['Value'] = str_replace(array("\r\n", "\r"), "\n", $submission['SubmissionValues'][$header]['Value']);
							// Is this right ?
							if (strpos($submission['SubmissionValues'][$header]['Value'],"\n") !== false)
								$submission['SubmissionValues'][$header]['Value'] = str_replace("\n",' ',$submission['SubmissionValues'][$header]['Value']);
						}
						fwrite($handle, $enclosure.(isset($submission['SubmissionValues'][$header]) ? str_replace(array('\\r','\\n','\\t',$enclosure), array("\015","\012","\011",$enclosure.$enclosure), $submission['SubmissionValues'][$header]['Value']) : (isset($submission[$header]) ? $submission[$header] : '')).$enclosure.($header != end($order) ? $delimiter : ""));
					}
					fwrite($handle, "\n");
				}
				fclose($handle);
			}
		}
		// Excel XML Options
		elseif ($type == 'excelxml')
		{
			require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/excelxml.php';
			
			$xls = new RSFormProXLS($model->getFormTitle());
			$xls->open($file, $start == 0 ? 'w' : 'a');
			
			if ($start == 0 && $use_headers)
				$xls->writeHeaders($order);
			
			if (empty($submissions))
			{
				$xls->close();
				// Adjust pagination
				$mainframe->setUserState('com_rsform.submissions.limitstart', 0);
				$mainframe->setUserState('com_rsform.submissions.limit', JFactory::getConfig()->get('list_limit'));
				echo 'END';
			}
			else
			{
				$array = array();
				foreach ($submissions as $submissionId => $submission)
				{
					$item = array();
					foreach ($order as $orderId => $header)
					{
						if (isset($submission['SubmissionValues'][$header]))
							$item[$header] = $submission['SubmissionValues'][$header]['Value'];
						elseif (isset($submission[$header]))
							$item[$header] = $submission[$header];
						else
							$item[$header] = '';
					}
					
					$array[] = $item;
				}
				$xls->write($array);
				$xls->close();
			}
		}
		// Excel Options
		elseif ($type == 'excel')
		{
			require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/excel.php';
			
			$xls = new RSFormProXLSX();
			$xls->name 			= $model->getFormTitle();
			$xls->useHeaders 	= $use_headers;
			
			if ($start == 0) {
				$xls->open($file, 'w', $start, $model->getTotal(), count($order) - 1);
			} else {
				$xls->open($file, 'a', $start);
			}
			
			if ($start == 0 && $use_headers) {
				$xls->writeHeaders($order);
			}
			
			if (empty($submissions))
			{
				$xls->close();
				// Adjust pagination
				$mainframe->setUserState('com_rsform.submissions.limitstart', 0);
				$mainframe->setUserState('com_rsform.submissions.limit', JFactory::getConfig()->get('list_limit'));
				echo 'END';
			}
			else
			{
				$array = array();
				foreach ($submissions as $submissionId => $submission)
				{
					$item = array();
					foreach ($order as $orderId => $header)
					{
						if (isset($submission['SubmissionValues'][$header]))
							$item[$header] = $submission['SubmissionValues'][$header]['Value'];
						elseif (isset($submission[$header]))
							$item[$header] = $submission[$header];
						else
							$item[$header] = '';
					}
					
					$array[] = $item;
				}
				$xls->write($array);
			}
		}
		// XML Options
		elseif ($type == 'xml')
		{
			$handle = fopen($file, $start == 0 ? 'w' : 'a');
			
			if ($start == 0)
			{
				$buffer = '';
				$buffer .= '<?xml version="1.0" encoding="utf-8"?>'."\n";
				$buffer .= '<form>'."\n";
				$buffer .= '<title><![CDATA['.$model->getFormTitle().']]></title>'."\n";
				$buffer .= "\t".'<submissions>'."\n";
				fwrite($handle, $buffer);
			}
			
			if (empty($submissions))
			{
				$buffer = '';
				$buffer .= "\t".'</submissions>'."\n";
				$buffer .= '</form>';
				fwrite($handle, $buffer);
				fclose($handle);
				// Adjust pagination
				$mainframe->setUserState('com_rsform.submissions.limitstart', 0);
				$mainframe->setUserState('com_rsform.submissions.limit', JFactory::getConfig()->get('list_limit'));
				echo 'END';
			}
			else
			{
				foreach ($submissions as $submissionId => $submission)
				{
					fwrite($handle, "\t\t".'<submission>'."\n");
					$buffer = '';
					foreach ($order as $orderId => $header)
					{
						if (isset($submission['SubmissionValues'][$header]))
							$item = $submission['SubmissionValues'][$header]['Value'];
						elseif (isset($submission[$header]))
							$item = $submission[$header];
						else
							$item = '';
						
						if (!is_numeric($item))
							$item = '<![CDATA['.$item.']]>';
						
						$header = preg_replace('#\s+#', '', $header);
						
						$buffer .= "\t\t\t".'<'.$header.'>'.$item.'</'.$header.'>'."\n";
					}
					fwrite($handle, $buffer);
					fwrite($handle, "\t\t".'</submission>'."\n");
				}
				fclose($handle);
			}
		} elseif ($type == 'ods') {
			require_once JPATH_COMPONENT.'/helpers/ods.php';
			
			$ods = new RSFormProODS($file);
			if ($start == 0) {
				$ods->startDoc();
				$ods->startSheet();
				if ($use_headers) {
					foreach ($order as $orderId => $header) {
						$ods->addCell($orderId, $header, 'string');
					}
					$ods->saveRow();
				}
			}
			
			if (empty($submissions)) {
				$ods->endSheet();
				$ods->endDoc();
				$ods->saveOds();
				
				// Adjust pagination
				$mainframe->setUserState('com_rsform.submissions.limitstart', 0);
				$mainframe->setUserState('com_rsform.submissions.limit', JFactory::getConfig()->get('list_limit'));
				echo 'END';
			} else {
				foreach ($submissions as $submissionId => $submission) {
					foreach ($order as $orderId => $header) {
						if (isset($submission['SubmissionValues'][$header]))
							$item = $submission['SubmissionValues'][$header]['Value'];
						elseif (isset($submission[$header]))
							$item = $submission[$header];
						else
							$item = '';
						
						if (is_numeric($item)) {
							$ods->addCell($orderId, (float) $item, 'float');
						} else {
							$ods->addCell($orderId, $item, 'string');
						}
					}
					$ods->saveRow();
				}
			}
		}
		
		exit();
	}
	
	public function exportTask()
	{
		$session = JFactory::getSession();
		$session->set('com_rsform.export.data', serialize(RSFormProHelper::getRawPost()));
		
		$view 	= $this->getView('submissions', 'html');
		$model 	= $this->getModel('submissions');
		$view->setLayout('exportprocess');
		$view->setModel($model, true);
		$view->display();
	}

    public function importTask()
    {
        $session = JFactory::getSession();
        $headers = JFactory::getApplication()->input->get('header', array(), 'array');

        $options = (array) $session->get('com_rsform.import.options', array());
        $options['headers'] = array_filter($headers);

        $session->set('com_rsform.import.options', $options);

        $view 	= $this->getView('submissions', 'html');
        $model 	= $this->getModel('submissions');
        $view->setLayout('importprocess');
        $view->setModel($model, true);
        $view->display();
    }
	
	public function exportFile()
	{
		$config = JFactory::getConfig();
		$file = JFactory::getApplication()->input->getCmd('ExportFile');
		$file = $config->get('tmp_path').'/'.$file;
		
		$type = JFactory::getApplication()->input->getCmd('ExportType');
		$extension = 'csv';
		
		switch ($type) {
			default:
				$extension = $type;
			break;
			
			case 'ods':	
				$extension = 'ods';
				$file = $file.'.ods';
			break;
			
			case 'excelxml':
				$extension = 'xml';
			break;
			
			case 'excel':
				$file .= '.zip';
				$extension = 'xlsx';
			break;
		}
		
		RSFormProHelper::readFile($file, JFactory::getDate()->format('Y-m-d').'_rsform.'.$extension);
	}
	
	public function viewFile()
	{
		$app	= JFactory::getApplication();
		$db		= &$this->_db;
		$id 	= $app->input->getInt('id');
		
		$query = $db->getQuery(true);
		$query->select('*')
			  ->from($db->qn('#__rsform_submission_values'))
			  ->where($db->qn('SubmissionValueId').'='.$db->q($id));
		$result = $db->setQuery($query)->loadObject();
		
		// Not found
		if (empty($result)) {
			$app->redirect('index.php?option=com_rsform&view=submissions');
		}
		
		$query->clear()
			  ->select($db->qn('c.ComponentTypeId'))
			  ->from($db->qn('#__rsform_properties', 'p'))
			  ->leftJoin($db->qn('#__rsform_components', 'c').' ON ('.$db->qn('p.ComponentId').' = '.$db->qn('c.ComponentId').')')
			  ->where($db->qn('p.PropertyName').' = '.$db->q('NAME'))
			  ->where($db->qn('p.PropertyValue').' = '.$db->q($result->FieldName))
			  ->where($db->qn('c.FormId').' = '.$db->q($result->FormId));
		$type = $db->setQuery($query)->loadResult();
		
		// Not an upload field
		if ($type != 9) {
			return $this->setRedirect('index.php?option=com_rsform&view=submissions', JText::_('RSFP_VIEW_FILE_NOT_UPLOAD'));
		}
		
		if (file_exists($result->FieldValue)) {
			RSFormProHelper::readFile($result->FieldValue);
		}
		
		$this->setRedirect('index.php?option=com_rsform&view=submissions', JText::_('RSFP_VIEW_FILE_NOT_FOUND'));
	}
}