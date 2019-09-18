<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class RsformControllerDirectory extends RsformController
{
	public function __construct($config = array())
	{
		parent::__construct($config);
		
		$this->registerTask('apply', 'save');
	}
	
	public function download()
	{
		$app 		= JFactory::getApplication();
		$model		= $this->getModel('directory');
		$directory	= $model->getDirectory();
		
		if (!$directory->enablecsv)
		{
		    $app->enqueueMessage(JText::_('RSFP_VIEW_DIRECTORY_NO_CSV'), 'warning');
			return $app->redirect(JUri::root());
		}
		
		if (!$model->isValid())
		{
            $app->enqueueMessage($model->getError(), 'warning');
			return $app->redirect(JUri::root());
		}
		
		$db 	= JFactory::getDbo();
		$params = $app->getParams('com_rsform');
		$menu	= $app->getMenu();
		$active = $menu->getActive();
		$formId = $params->get('formId');
		$cids 	= $app->input->get('cid', array(), 'array');
		$cids 	= array_map('intval', $cids);
		$root 	= JUri::getInstance()->toString(array('scheme', 'host', 'port'));
		
		$fields  = RSFormProHelper::getDirectoryFields($formId);
		$headers = RSFormProHelper::getDirectoryStaticHeaders();
		
		$downloadableFields 		= array();
		$downloadableFieldCaptions 	= array();
		foreach ($fields as $field)
		{
			if ($field->incsv)
			{
				$downloadableFields[] = (object) array(
					'name'	 => $field->FieldName,
					'static' => $field->componentId < 0 && isset($headers[$field->componentId]) ? 1 : 0
				);
				$downloadableFieldCaptions[] = $field->FieldCaption;
			}
		}

		list($multipleSeparator, $uploadFields, $multipleFields, $textareaFields, $secret) = RSFormProHelper::getDirectoryFormProperties($formId);
		
		// Get submissions
        $query = $db->getQuery(true);
        $query->select('*')
            ->from($db->qn('#__rsform_submissions'))
            ->where($db->qn('FormId') . ' = ' . $db->q($formId))
            ->where($db->qn('SubmissionId') . ' IN (' . implode(',', $db->q($cids)) . ')');

		$db->setQuery($query);
		$submissions = $db->loadObjectList('SubmissionId');
		
		// Get values
		$names = array();
		foreach ($downloadableFields as $field)
		{
			if (!$field->static)
			{
				$names[] = $db->q($field->name);
			}
		}
		
		$query = $db->getQuery(true);
		$query->select($db->qn('SubmissionId'))
			  ->select($db->qn('FieldName'))
			  ->select($db->qn('FieldValue'))
			  ->from($db->qn('#__rsform_submission_values'))
			  ->where($db->qn('FormId').'='.$db->q($formId));
		if ($cids)
		{
			$query->where($db->qn('SubmissionId').' IN ('.implode(',', $cids).')');
		}
		if ($names)
		{
			$query->where($db->qn('FieldName').' IN ('.implode(',', $names).')');
		}
		
		$db->setQuery($query);
		$values = $db->loadObjectList();
		
		// Combine them
		foreach ($values as $item)
		{
			if (!isset($submissions[$item->SubmissionId]->values))
			{
				$submissions[$item->SubmissionId]->values = array();
			}
			
			// process here
			if (in_array($item->FieldName, $uploadFields))
			{
				if ($item->FieldValue)
				{
					$files = RSFormProHelper::explode($item->FieldValue);
					$actualValues = array();
					foreach ($files as $file)
					{
						$actualValues[] = '<a href="' . $root . JRoute::_('index.php?option=com_rsform&task=submissions.view.file&hash=' . md5($item->SubmissionId.$secret.$item->FieldName) . '&file=' . md5($file)) . '">' . RSFormProHelper::htmlEscape(basename($file)) . '</a>';
					}

					$item->FieldValue = implode("\n", $actualValues);
				}
			}
			elseif (in_array($item->FieldName, $multipleFields))
			{
				$item->FieldValue = str_replace("\n", $multipleSeparator, $item->FieldValue);
			}
			
			$submissions[$item->SubmissionId]->values[$item->FieldName] = $item->FieldValue;
		}

		$app->triggerEvent('rsfp_f_onDownloadCSV', array(&$submissions, $formId));
		
		$enclosure = $params->get('enclosure', '"');
		$delimiter = $params->get('delimiter', ',');
		
		$download_name = $active->alias.'.csv';

		$app->setHeader('Cache-Control', 'public, must-revalidate');
		$app->setHeader('Cache-Control', 'pre-check=0, post-check=0, max-age=0');
		$app->setHeader('Pragma', 'must-revalidate');
		$app->setHeader('Expires', '0');
		$app->setHeader('Content-Description', 'File Transfer');
		$app->setHeader('Content-Type', 'text/csv');
		$app->setHeader('Content-Disposition', 'attachment; filename="'.$download_name.'"');
		$app->setHeader('Transfer-Encoding', 'binary');

		$app->sendHeaders();
		
		ob_end_clean();
		echo $enclosure.implode($enclosure.$delimiter.$enclosure, $downloadableFieldCaptions).$enclosure."\n";
		foreach ($cids as $cid) {
			$row = array();
			foreach ($downloadableFields as $field) {
				$value = '';
				if (!$field->static && isset($submissions[$cid]->values[$field->name])) {
					$value = $submissions[$cid]->values[$field->name];
				} elseif ($field->static && isset($submissions[$cid]->{$field->name})) {
					// Show a text for the "confirmed" column.
					if ($field->name == 'confirmed') {
						$value = $submissions[$cid]->{$field->name} ? JText::_('RSFP_YES') : JText::_('RSFP_NO');
					} else if ($field->name == 'DateSubmitted') {
						$value = RSFormProHelper::getDate($submissions[$cid]->{$field->name});
					} else {
						$value = $submissions[$cid]->{$field->name};
					}
				}
				
				$row[] = $this->fixValue($value);
			}
			
			echo $enclosure.implode($enclosure.$delimiter.$enclosure, str_replace($enclosure, $enclosure.$enclosure, $row)).$enclosure."\n";
		}

		$app->close();
	}

	protected function fixValue($string)
	{
		if (strlen($string) && in_array($string[0], array('=', '+', '-', '@')))
		{
			$string = ' ' . $string;
		}

		return $string;
	}
	
	public function save()
	{
		$app 	= JFactory::getApplication();
		$formId	= $app->input->getInt('formId',0);
		$id		= $app->input->getInt('id',0);
		$task	= $this->getTask();
		
		// Get the model
		$model = $this->getModel('directory');
		
		// Save
		if (!RSFormProHelper::canEdit($formId, $id))
		{
			$this->setMessage(JText::_('COM_RSFORM_SUBMISSIONS_DIRECTORY_CANNOT_SAVE'),'error');
			$this->setRedirect(JRoute::_('index.php?option=com_rsform&view=directory',false));

			return false;
		}

		if (!$model->save())
		{
			$app->enqueueMessage(JText::_('RSFP_SUBM_DIR_SAVE_ERROR'),'error');

			$app->input->set('view', 'directory');
			$app->input->set('layout', 'edit');
			$app->input->set('id', $id);

			parent::display();

			return false;
		}

		$this->setMessage(JText::_('RSFP_SUBM_DIR_SAVE_OK'));

		if ($task == 'apply')
		{
			$this->setRedirect(JRoute::_('index.php?option=com_rsform&view=directory&layout=edit&id='.$id,false));
		}
		else
		{
			$this->setRedirect(JRoute::_('index.php?option=com_rsform&view=directory',false));
		}
	}

    public function delete()
	{
        $app 	= JFactory::getApplication();
        $formId	= $app->getParams('com_rsform')->get('formId');
        $id		= $app->input->getInt('id',0);

        // Get the model
        $model = $this->getModel('directory');

		// Check if we can delete
		if (!RSFormProHelper::canDelete($formId, $id))
		{
			$this->setMessage(JText::_('COM_RSFORM_SUBMISSIONS_DIRECTORY_CANNOT_DELETE'),'error');
		}
		else
		{
			// Set message
			$this->setMessage(JText::sprintf('RSFP_SUBM_DIR_DELETE_OK', $id));

			// Delete
			$model->delete($id);
		}

		// Set the redirect
		$this->setRedirect(JRoute::_('index.php?option=com_rsform&view=directory',false));
    }
	
	public function back() {
		$this->setRedirect(JRoute::_('index.php?option=com_rsform&view=directory', false));
	}
}