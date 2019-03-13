<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformController extends JControllerLegacy
{	
	public function captcha() {
		require_once JPATH_SITE.'/components/com_rsform/helpers/captcha.php';
		
		$componentId 	= JFactory::getApplication()->input->getInt('componentId');
		$captcha 		= new RSFormProCaptcha($componentId);

		JFactory::getSession()->set('com_rsform.captcha.captchaId'.$componentId, $captcha->getCaptcha());
		
		if (JFactory::getDocument()->getType() != 'image')
		{
			JFactory::getApplication()->close();
		}
	}
	
	public function plugin() {
		JFactory::getApplication()->triggerEvent('rsfp_f_onSwitchTasks');
	}
	
	/* deprecated */
	public function showForm() {}
	
	public function submissionsViewFile()
    {
		$db 	= JFactory::getDbo();
		$secret = JFactory::getConfig()->get('secret');
		$hash 	= JFactory::getApplication()->input->getCmd('hash');
		
		// Load language file
		JFactory::getLanguage()->load('com_rsform', JPATH_ADMINISTRATOR);
		
		if (strlen($hash) != 32)
		{
		    throw new Exception(JText::_('RSFP_VIEW_FILE_NOT_FOUND'));
		}

		$query = $db->getQuery(true);
		$query->select('*')
            ->from($db->qn('#__rsform_submission_values'))
            ->where('MD5(CONCAT('.$db->qn('SubmissionId').', '.$db->q($secret).', '.$db->qn('FieldName').')) = ' . $db->q($hash));
		$db->setQuery($query);
		if ($result = $db->loadObject())
		{
			// Check if it's an upload field
            $query->clear()
                ->select($db->qn('c.ComponentTypeId'))
                ->from($db->qn('#__rsform_properties', 'p'))
                ->join('left', $db->qn('#__rsform_components', 'c') . ' ON ('.$db->qn('p.ComponentId') . ' = ' . $db->qn('c.ComponentId') .')')
                ->where($db->qn('p.PropertyName') . '=' . $db->q('NAME'))
                ->where($db->qn('p.PropertyValue') . '=' . $db->q($result->FieldName))
                ->where($db->qn('c.FormId') . '=' . $db->q($result->FormId));
			$db->setQuery($query);
			$type = $db->loadResult();
			if ($type != RSFORM_FIELD_FILEUPLOAD)
			{
                throw new Exception(JText::_('RSFP_VIEW_FILE_NOT_UPLOAD'));
			}
			
			if (!file_exists($result->FieldValue))
			{
                throw new Exception(JText::_('RSFP_VIEW_FILE_NOT_FOUND'));
			}

            RSFormProHelper::readFile($result->FieldValue);
		}
		else
        {
            throw new Exception(JText::_('RSFP_VIEW_FILE_NOT_FOUND'));
		}
	}
	
	public function ajaxValidate()
	{
		$db     = JFactory::getDbo();
		$app    = JFactory::getApplication();
		$form   = $app->input->post->get('form', array(), 'array');
		$formId = isset($form['formId']) ? $form['formId'] : 0;

		$query = $db->getQuery(true)
            ->select($db->qn('ComponentId'))
            ->select($db->qn('ComponentTypeId'))
            ->from($db->qn('#__rsform_components'))
            ->where($db->qn('FormId') . ' = ' . $db->q($formId))
            ->where($db->qn('Published') . ' = ' . $db->q(1))
            ->order($db->qn('Order'));

		$db->setQuery($query);
		$components = $db->loadObjectList();
		
		$page = $app->input->getInt('page');
		if ($page)
		{
			$current_page = 1;
			foreach ($components as $i => $component)
			{
				if ($current_page != $page)
				{
                    unset($components[$i]);
                }
				if ($component->ComponentTypeId == RSFORM_FIELD_PAGEBREAK)
				{
                    $current_page++;
                }
			}
		}
		
		$removeUploads   = array();
		$formComponents  = array();
		foreach ($components as $component)
		{
			$formComponents[] = $component->ComponentId;
			if ($component->ComponentTypeId == RSFORM_FIELD_FILEUPLOAD)
			{
                $removeUploads[] = $component->ComponentId;
            }
		}
		
		echo implode(',', $formComponents);
		
		echo "\n";
		
		$invalid = RSFormProHelper::validateForm($formId);
		
		//Trigger Event - onBeforeFormValidation
		$app->triggerEvent('rsfp_f_onBeforeFormValidation', array(array('invalid'=>&$invalid, 'formId' => $formId, 'post' => &$form)));
		
		if (count($invalid))
		{
			foreach ($invalid as $i => $componentId)
			{
                if (in_array($componentId, $removeUploads))
                {
                    unset($invalid[$i]);
                }
            }
			
			$invalidComponents = array_intersect($formComponents, $invalid);
			
			echo implode(',', $invalidComponents);
		}
		
		if (isset($invalidComponents))
		{
			echo "\n";

			$pages = RSFormProHelper::componentExists($formId, RSFORM_FIELD_PAGEBREAK);
			$pages = count($pages);
			
			if ($pages && !$page)
			{
				$first = reset($invalidComponents);
				$current_page = 1;
				foreach ($components as $i => $component)
				{
					if ($component->ComponentId == $first)
					{
                        break;
                    }
					if ($component->ComponentTypeId == RSFORM_FIELD_PAGEBREAK)
					{
                        $current_page++;
                    }
				}
				echo $current_page;
				
				echo "\n";
				
				echo $pages;
			}
		}
		
		$app->close();
	}
	
	public function confirm()
    {
		$db 	= JFactory::getDbo();
		$app	= JFactory::getApplication();
		$hash 	= $app->input->getCmd('hash');
		
		if (strlen($hash) == 32)
		{
		    $query = $db->getQuery(true)
                ->select($db->qn('SubmissionId'))
                ->from($db->qn('#__rsform_submissions'))
                ->where('MD5(CONCAT('.$db->qn('SubmissionId').', '.$db->qn('FormId').', '.$db->qn('DateSubmitted').')) = ' . $db->q($hash));
			$db->setQuery($query);
			if ($SubmissionId = $db->loadResult())
			{
			    $query->clear()
                    ->update($db->qn('#__rsform_submissions'))
                    ->set($db->qn('confirmed') . ' = ' . $db->q(1))
                    ->where($db->qn('SubmissionId') . ' = ' . $db->q($SubmissionId));
				$db->setQuery($query);
				$db->execute();
				
				$app->triggerEvent('rsfp_f_onSubmissionConfirmation', array(array('SubmissionId' => $SubmissionId, 'hash' => $hash)));
				$app->enqueueMessage(JText::_('RSFP_SUBMISSION_CONFIRMED'), 'notice');
			}
		}
		else
        {
            $app->enqueueMessage(JText::_('RSFP_SUBMISSION_CONFIRMED_ERROR'), 'warning');
		}
	}

    public function deleteSubmission()
    {
        $db 	= JFactory::getDbo();
        $app	= JFactory::getApplication();
        $hash 	= $app->input->getCmd('hash');

        if (strlen($hash) == 32)
        {
            $query = $db->getQuery(true)
                ->select($db->qn('SubmissionId'))
                ->select($db->qn('FormId'))
                ->from($db->qn('#__rsform_submissions'))
                ->where($db->qn('SubmissionHash') . ' = ' . $db->q($hash));
            $db->setQuery($query);
            if ($submission = $db->loadObject())
            {
                require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/submissions.php';

                RSFormProSubmissionsHelper::deleteSubmissions($submission->SubmissionId, true);

                $app->triggerEvent('rsfp_f_onSubmissionDeletion', array(array('SubmissionId' => $submission->SubmissionId, 'hash' => $hash)));
                $app->enqueueMessage(JText::_('COM_RSFORM_SUBMISSION_DELETED'));
            }
            else
            {
                $app->enqueueMessage(JText::_('COM_RSFORM_INVALID_HASH'), 'warning');
            }
        }
        else
        {
            $app->enqueueMessage(JText::_('COM_RSFORM_INVALID_HASH'), 'warning');
        }
    }
	
	public function display($cachable = false, $safeurlparams = false)
	{
		$app	= JFactory::getApplication();
		$vName	= $app->input->getCmd('view', '');
		
		jimport('joomla.filesystem.folder');
		
		$allowed = JFolder::folders(JPATH_COMPONENT.'/views');
		
		if (!in_array($vName, $allowed))
		{
			$app->input->set('view', 'rsform');
		}

		parent::display($cachable, $safeurlparams);
	}
}