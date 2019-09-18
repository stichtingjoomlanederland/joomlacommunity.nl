<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformController extends JControllerLegacy
{
	public function captcha()
	{
		require_once JPATH_SITE.'/components/com_rsform/helpers/captcha.php';

		$componentId 	= JFactory::getApplication()->input->getInt('componentId');
		$captcha 		= new RSFormProCaptcha($componentId);

		JFactory::getSession()->set('com_rsform.captcha.captchaId'.$componentId, $captcha->getCaptcha());

		if (JFactory::getDocument()->getType() != 'image')
		{
			JFactory::getApplication()->close();
		}
	}

	public function plugin()
	{
		JFactory::getApplication()->triggerEvent('rsfp_f_onSwitchTasks');
	}
	
	/* deprecated */
	public function showForm() {}
	
	public function submissionsViewFile()
    {
		$db 	= JFactory::getDbo();
		$secret = JFactory::getConfig()->get('secret');
		$app	= JFactory::getApplication();
		$hash 	= $app->input->getCmd('hash');
		$file   = $app->input->getCmd('file');
		
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
			$allowedTypes = array(RSFORM_FIELD_FILEUPLOAD);

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

			$app->triggerEvent('rsfp_onSubmissionsViewFile', array(&$allowedTypes, &$result));

			if (!in_array($type, $allowedTypes))
			{
                throw new Exception(JText::_('RSFP_VIEW_FILE_NOT_UPLOAD'));
			}

			$foundFile = false;
			if ($file && strlen($file) == 32)
			{
				$values = RSFormProHelper::explode($result->FieldValue);

				foreach ($values as $value)
				{
					if (md5($value) == $file)
					{
						$foundFile = $value;
						break;
					}
				}
			}
			else
			{
				$foundFile = $result->FieldValue;
			}
			
			if (!$foundFile || !file_exists($foundFile))
			{
                throw new Exception(JText::_('RSFP_VIEW_FILE_NOT_FOUND'));
			}

            RSFormProHelper::readFile($foundFile);
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
		$post   = $app->input->post->get('form', array(), 'array');
		$formId = isset($post['formId']) ? $post['formId'] : 0;
		$isAjax = true;
		$data	= array();

		if (!$formId)
		{
			$app->close();
		}

		$form = RSFormProHelper::getForm($formId);

		if (!$form || !$form->Published)
		{
			$app->close();
		}

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

		$data['formComponents'] = $formComponents;
		
		$invalid = RSFormProHelper::validateForm($formId);
		
		//Trigger Event - onBeforeFormValidation
		$app->triggerEvent('rsfp_f_onBeforeFormValidation', array(array('invalid'=>&$invalid, 'formId' => $formId, 'post' => &$post)));

		$_POST['form'] = $post;

		eval($form->ScriptProcess);

		$post = $_POST['form'];
		
		if (count($invalid))
		{
			foreach ($invalid as $i => $componentId)
			{
                if (in_array($componentId, $removeUploads))
                {
                    unset($invalid[$i]);
                }
            }

            // Using array_values to reindex keys so we have an Array in JSON
			$invalidComponents = array_values(array_intersect($formComponents, $invalid));

			$data['invalidComponents'] = $invalidComponents;
		}
		
		if (!empty($invalidComponents))
		{
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
				$data['currentPage'] = $current_page;

				$data['allPages'] = $pages;
			}

			// Update error messages on the page
			if ($results = RSFormProHelper::getComponentProperties($invalidComponents))
			{
				$data['validationMessages'] = array();
				foreach ($results as $componentId => $properties)
				{
					if (!empty($properties['VALIDATIONMESSAGE']))
					{
						$data['validationMessages'][$componentId] = $properties['VALIDATIONMESSAGE'];
					}
				}
			}

		}

		$this->showJson($data);
		
		$app->close();
	}

	protected function showJson($data)
	{
		$app	= JFactory::getApplication();
		$accept = $app->input->server->get('HTTP_ACCEPT', '', 'raw');

		if (strpos($accept, 'application/json') === false && strpos($accept, 'text/html') !== false && strpos($accept, '*/*') === false)
		{
			// Internet Explorer < 10
			$mime = 'text/plain';
		}
		else
		{
			// Browser other than Internet Explorer < 10
			$mime = 'application/json';

			$app->setHeader('Content-Disposition', 'attachment; filename="ajaxvalidate.json"', true);
		}

		$app->allowCache(false);

		$app->setHeader('Content-Type', $mime, true);

		$app->sendHeaders();

		echo $this->encode($data);

		$app->close();
	}

	protected function encode($response)
	{
		$result = json_encode($response);

		// Added a failsafe in case the response isn't encoded - at least we'll have something to work with.
		if ($result === false)
		{
			$result = $this->jsonEncode($response);
		}

		// Let's see if the JSON can be decoded to avoid JSON.parse errors
		$decoded = json_decode($result);
		if ($decoded === null)
		{
			// Remove wrong control chars
			$result = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x80-\x9F]/', '', $result);
		}

		return $result;
	}

	protected function jsonEncode($val)
	{
		if (is_string($val))
		{
			$val = str_replace(array("\n", "\r", "\t", "\v", "\f"), array('\n', '\r', '\t', '\v', '\f'), $val);
			return '"'.addcslashes($val, '"\\').'"';
		}

		if (is_numeric($val))
		{
			return $val;
		}

		if ($val === null)
		{
			return 'null';
		}

		if ($val === true)
		{
			return 'true';
		}

		if ($val === false)
		{
			return 'false';
		}

		$assoc = is_array($val) ? array_keys($val) !== range(0, count($val) - 1) : true;

		$res = array();

		foreach ($val as $k => $v)
		{
			$v = $this->jsonEncode($v);
			if ($assoc)
			{
				$k = '"'.addcslashes($k, '"\\').'"';
				$v = $k.':'.$v;
			}
			$res[] = $v;
		}

		$res = implode(',', $res);
		return ($assoc) ? '{'.$res.'}' : '['.$res.']';
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
                ->select($db->qn('FormId'))
                ->from($db->qn('#__rsform_submissions'))
                ->where('MD5(CONCAT('.$db->qn('SubmissionId').', '.$db->qn('FormId').', '.$db->qn('DateSubmitted').')) = ' . $db->q($hash));
			$db->setQuery($query);
			if ($submission = $db->loadObject())
			{
				$SubmissionId 	= $submission->SubmissionId;
				$formId			= $submission->FormId;

			    $query->clear()
                    ->update($db->qn('#__rsform_submissions'))
                    ->set($db->qn('confirmed') . ' = ' . $db->q(1))
                    ->where($db->qn('SubmissionId') . ' = ' . $db->q($SubmissionId));
				$db->setQuery($query);
				$db->execute();
				
				$app->triggerEvent('rsfp_f_onSubmissionConfirmation', array(array('SubmissionId' => $SubmissionId, 'hash' => $hash)));
				$app->enqueueMessage(JText::_('RSFP_SUBMISSION_CONFIRMED'), 'notice');

				$form = RSFormProHelper::getForm($formId);
				if (!empty($form->ConfirmSubmissionUrl))
				{
					list($replace, $with) = RSFormProHelper::getReplacements($SubmissionId);

					$url = str_replace($replace, $with, $form->ConfirmSubmissionUrl);
					$app->redirect(JRoute::_($url, false));
				}
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