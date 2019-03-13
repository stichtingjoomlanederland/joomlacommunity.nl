<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class RsformControllerDirectory extends RsformController
{
	public function __construct() {
		parent::__construct();
		
		$this->registerTask('apply', 'save');
	}
	
	public function download() {
		$app 		= JFactory::getApplication();
		$model		= $this->getModel('directory');
		$directory	= $model->getDirectory();
		
		jimport('joomla.filesystem.file');
		
		if (!$directory->enablecsv) {
		    $app->enqueueMessage(JText::_('RSFP_VIEW_DIRECTORY_NO_CSV'), 'warning');
			return $app->redirect(JUri::root());
		}
		
		if (!$model->isValid()) {
            $app->enqueueMessage($model->getError(), 'warning');
			return $app->redirect(JUri::root());
		}
		
		$db 	= JFactory::getDbo();
		$params = $app->getParams('com_rsform');
		$menu	= $app->getMenu();
		$active = $menu->getActive();
		$formId = $params->get('formId');
		$cids 	= $app->input->get('cid', array(), 'array');
		$cids = array_map('intval', $cids);
		
		$fields  = RSFormProHelper::getDirectoryFields($formId);
		$headers = RSFormProHelper::getDirectoryStaticHeaders();
		
		$downloadableFields 		= array();
		$downloadableFieldCaptions 	= array();
		foreach ($fields as $field) {
			if ($field->incsv) {
				$downloadableFields[] = (object) array(
					'name'	 => $field->FieldName,
					'static' => $field->componentId < 0 && isset($headers[$field->componentId]) ? 1 : 0
				);
				$downloadableFieldCaptions[] = $field->FieldCaption;
			}
		}

		list($multipleSeparator, $uploadFields, $multipleFields, $secret) = RSFormProHelper::getDirectoryFormProperties($formId);
		
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
		foreach ($downloadableFields as $field) {
			if (!$field->static) {
				$names[] = $db->q($field->name);
			}
		}
		
		$query = $db->getQuery(true);
		$query->select($db->qn('SubmissionId'))
			  ->select($db->qn('FieldName'))
			  ->select($db->qn('FieldValue'))
			  ->from($db->qn('#__rsform_submission_values'))
			  ->where($db->qn('FormId').'='.$db->q($formId));
		if ($cids) {
			$query->where($db->qn('SubmissionId').' IN ('.implode(',', $cids).')');
		}
		if ($names) {
			$query->where($db->qn('FieldName').' IN ('.implode(',', $names).')');
		}
		
		$db->setQuery($query);
		$values = $db->loadObjectList();
		
		// Combine them
		foreach ($values as $item) {
			if (!isset($submissions[$item->SubmissionId]->values)) {
				$submissions[$item->SubmissionId]->values = array();
			}
			
			// process here
			if (in_array($item->FieldName, $uploadFields)) {
				$item->FieldValue = '<a href="'.JUri::root().'index.php?option=com_rsform&amp;task=submissions.view.file&amp;hash='.md5($item->SubmissionId.$secret.$item->FieldName).'">'.JFile::getName($item->FieldValue).'</a>';
			} elseif (in_array($item->FieldName, $multipleFields)) {
				$item->FieldValue = str_replace("\n", $multipleSeparator, $item->FieldValue);
			}
			
			$submissions[$item->SubmissionId]->values[$item->FieldName] = $item->FieldValue;
		}

		$app->triggerEvent('rsfp_f_onDownloadCSV', array(&$submissions, $formId));
		
		$enclosure = '"';
		$delimiter = ',';
		
		$download_name = $active->alias.'.csv';
		header('Cache-Control: public, must-revalidate');
		header('Cache-Control: pre-check=0, post-check=0, max-age=0');
		if (!preg_match('#MSIE#', $_SERVER['HTTP_USER_AGENT']))
			header("Pragma: no-cache");
		header("Expires: 0"); 
		header("Content-Description: File Transfer");
		header("Expires: Sat, 01 Jan 2000 01:00:00 GMT");
		if (preg_match('#Opera#', $_SERVER['HTTP_USER_AGENT']))
			header("Content-Type: application/octetstream"); 
		else 
			header("Content-Type: application/octet-stream");
		header('Content-Disposition: attachment; filename="'.$download_name.'"');
		header("Content-Transfer-Encoding: binary\n");
		
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
				
				$row[] = $value;
			}
			
			echo $enclosure.implode($enclosure.$delimiter.$enclosure, str_replace($enclosure, $enclosure.$enclosure, $row)).$enclosure."\n";
		}
		
		$app->close();
	}
	
	public function save() {
		$app 	= JFactory::getApplication();
		$formId	= $app->input->getInt('formId',0);
		$id		= $app->input->getInt('id',0);
		$task	= $this->getTask();
		
		// Get the model
		$model = $this->getModel('directory');
		
		// Save
		if (RSFormProHelper::canEdit($formId,$id)) {
			if ($model->save()) {
				$this->setMessage(JText::_('RSFP_SUBM_DIR_SAVE_OK'));
				
				if ($task == 'apply') {
					$this->setRedirect(JRoute::_('index.php?option=com_rsform&view=directory&layout=edit&id='.$id,false));
				} else {
					$this->setRedirect(JRoute::_('index.php?option=com_rsform&view=directory',false));
				}
			} else {
				$app->enqueueMessage(JText::_('RSFP_SUBM_DIR_SAVE_ERROR'),'error');
				JFactory::getApplication()->input->set('view','directory');
				JFactory::getApplication()->input->set('layout','edit');
				JFactory::getApplication()->input->set('id', $id);
				
				parent::display();
			}
		} else {
			$this->setMessage(JText::_('JERROR_ALERTNOAUTHOR'),'error');
			$this->setRedirect(JRoute::_('index.php?option=com_rsform&view=directory',false));
		}
	}

    public function delete() {
        $app 	= JFactory::getApplication();
        $params = $app->getParams('com_rsform');
        $formId	= $params->get('formId');
        $id		= $app->input->getInt('id',0);

        // Get the model
        $model = $this->getModel('directory');

        // Save
        if (RSFormProHelper::canDelete($formId, $id))
        {
            $this->setMessage(JText::sprintf('RSFP_SUBM_DIR_DELETE_OK', $id));
            $model->delete($id);
        }
        else
        {
            $this->setMessage(JText::_('JERROR_ALERTNOAUTHOR'),'error');
        }

        $this->setRedirect(JRoute::_('index.php?option=com_rsform&view=directory',false));
    }
	
	public function back() {
		$this->setRedirect(JRoute::_('index.php?option=com_rsform&view=directory', false));
	}
}