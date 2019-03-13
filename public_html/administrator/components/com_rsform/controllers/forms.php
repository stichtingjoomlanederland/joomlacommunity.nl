<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformControllerForms extends RsformController
{
	public function __construct()
	{
		parent::__construct();
		
		$this->registerTask('apply', 	 'save');
		$this->registerTask('new', 	 	 'add');
		$this->registerTask('publish',   'changestatus');
		$this->registerTask('unpublish', 'changestatus');
		
		$this->_db = JFactory::getDbo();
	}

	public function manage()
	{
		JFactory::getApplication()->input->set('view', 'forms');
		JFactory::getApplication()->input->set('layout', 'default');
		
		parent::display();
	}
	
	public function directory() {
		$formId = JFactory::getApplication()->input->getInt('formId',0);
		$this->setRedirect('index.php?option=com_rsform&view=directory&layout=edit&formId='.$formId);
	}
	
	public function edit()
	{
		JFactory::getApplication()->input->set('view', 	'forms');
		JFactory::getApplication()->input->set('layout', 	'edit');
		
		parent::display();
	}
	
	public function add()
	{
		JFactory::getApplication()->input->set('view', 	'forms');
		JFactory::getApplication()->input->set('layout', 	'new');
		
		parent::display();
	}
	
	public function emails()
	{
		JFactory::getApplication()->input->set('view', 	'forms');
		JFactory::getApplication()->input->set('layout', 	'emails');
		
		parent::display();
	}
	
	public function menuAddScreen()
	{
		JFactory::getApplication()->input->set('view', 	'menus');
		JFactory::getApplication()->input->set('layout', 	'default');
		
		parent::display();
	}
	
	public function setMenu()
	{
		$app    	= JFactory::getApplication();
		$formId 	= $app->input->getInt('formId');
		$component 	= JComponentHelper::getComponent('com_rsform');

		$app->setUserState('com_menus.edit.item.type', 'component');
		$app->setUserState('com_menus.edit.item.link', 'index.php?option=com_rsform&view=rsform&formId='.$formId);
		$app->setUserState('com_menus.edit.item.data', array(
			'component_id' => $component->id,
			'type'		   => 'component',
			'menutype'	   => $app->input->getString('menutype'),
			'formId'	   => $formId
		));
		$this->setRedirect(JRoute::_('index.php?option=com_menus&view=item&layout=edit', false));
	}
	
	public function menuAddBackend()
	{
		$db		= JFactory::getDbo();
		$app	= JFactory::getApplication();
		$formId	= $app->input->getInt('formId');
		
		// No form ID provided, redirect back.
		if (!$formId)
		{
			$app->redirect('index.php?option=com_rsform&view=forms');
		}
		
		// Get the form title
		$query = $db->getQuery(true)
			->select($db->qn('FormTitle'))
			->from($db->qn('#__rsform_forms'))
			->where($db->qn('FormId') . ' = ' . $db->q($formId));
		$title = $db->setQuery($query)->loadResult();
		
		// Use a default title to prevent showing an empty menu item
		if (!strlen($title))
		{
			$title = JText::_('RSFP_FORM_DEFAULT_TITLE');
		}

		if ($component = JComponentHelper::getComponent('com_rsform'))
		{
			$componentId = $component->id;
		}
		
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_menus/tables');
		
		$table = JTable::getInstance('Menu', 'MenusTable');
		$data = array(
			'menutype' 		=> 'main',
			'title'			=> trim($title),
			'alias'			=> JFilterOutput::stringURLSafe(trim($title)),
			'link'			=> 'index.php?option=com_rsform&view=forms&layout=show&formId=' . $formId,
			'component_id' 	=> $componentId,
			'type'			=> 'component',
			'published' 	=> 1,
			'parent_id' 	=> 1,
			'img'			=> 'class:component',
			'home'			=> 0,
			'path'			=> '',
			'params'		=> '',
			'client_id'		=> 1
		);
		
		try
		{
			$table->setLocation(1, 'last-child');
		}
		catch (InvalidArgumentException $e)
		{
			if (class_exists('JLog'))
			{
				JLog::add($e->getMessage(), JLog::WARNING, 'jerror');
			}

			$app->enqueueMessage($e->getMessage(), 'error');
			$app->redirect('index.php?option=com_rsform&view=forms');
			return false;
		}
		
		if (!$table->bind($data) || !$table->check() || !$table->store())
		{
			$app->enqueueMessage($table->getError(), 'error');
			$app->redirect('index.php?option=com_rsform&view=forms');
			return false;
		}
		
		$table->rebuild(1);
		
		// Mark this form as added
		$query = $db->getQuery(true)
			->update($db->qn('#__rsform_forms'))
			->set($db->qn('Backendmenu') . ' = ' . $db->q(1))
			->where($db->qn('FormId') . ' = ' . $db->q($formId));
		$db->setQuery($query)->execute();
		
		// Redirect
		$app->redirect('index.php?option=com_rsform&view=forms', JText::_('RSFP_FORM_ADDED_BACKEND'));
	}
	
	/**
	 * Forms Menu Remove Backend
	 */
	public function menuRemoveBackend()
	{
		$db		= JFactory::getDbo();
		$app	= JFactory::getApplication();
		$formId	= $app->input->getInt('formId');
		
		// No form ID provided, redirect back.
		if (!$formId)
		{
			$app->redirect('index.php?option=com_rsform&view=forms');
		}
		
		// Remove from menu
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_menus/tables');
		
		$table = JTable::getInstance('Menu', 'MenusTable');
		$query = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__menu'))
			->where($db->qn('client_id') . ' = ' . $db->q(1))
			->where($db->qn('link') . ' = ' . $db->q('index.php?option=com_rsform&view=forms&layout=show&formId=' . $formId));
		if ($ids = $db->setQuery($query)->loadColumn())
		{
			foreach ($ids as $id)
			{
				$table->delete($id);
				$table->rebuild(1);
			}
		}
		
		// Mark this form as removed
		$query = $db->getQuery(true)
			->update($db->qn('#__rsform_forms'))
			->set($db->qn('Backendmenu') . ' = ' . $db->q(0))
			->where($db->qn('FormId') . ' = ' . $db->q($formId));
		$db->setQuery($query)->execute();
		
		// Redirect
		$app->redirect('index.php?option=com_rsform&view=forms', JText::_('RSFP_FORM_REMOVED_BACKEND'));
	}
	
	public function newStepTwo()
	{
		JFactory::getApplication()->input->set('view', 'forms');
		JFactory::getApplication()->input->set('layout', 'new2');
		
		parent::display();
	}
	
	public function newStepThree()
	{
	    $app     = JFactory::getApplication();
		$session = JFactory::getSession();

		$session->set('com_rsform.wizard.FormTitle', $app->input->get('FormTitle', '', 'raw'));
		$session->set('com_rsform.wizard.FormLayout', $app->input->get('FormLayout', '', 'raw'));
		$session->set('com_rsform.wizard.ScrollToThankYou', $app->input->get('ScrollToThankYou', 0, 'int'));
		$session->set('com_rsform.wizard.ThankYouMessagePopUp', $app->input->get('ThankYouMessagePopUp', 0, 'int'));
		$session->set('com_rsform.wizard.AdminEmail', $app->input->get('AdminEmail', 0, 'int'));
		$session->set('com_rsform.wizard.AdminEmailTo', $app->input->get('AdminEmailTo', '', 'raw'));
		$session->set('com_rsform.wizard.UserEmail', $app->input->get('UserEmail', 0, 'int'));
		$session->set('com_rsform.wizard.SubmissionAction', $app->input->get('SubmissionAction', '', 'cmd'));
		$session->set('com_rsform.wizard.Thankyou', $app->input->get('Thankyou', '', 'raw'));
		$session->set('com_rsform.wizard.ReturnUrl', $app->input->get('ReturnUrl', '', 'raw'));
		
		$app->input->set('view', 'forms');
		$app->input->set('layout', 'new3');
		
		parent::display();
	}
	
	public function newStepFinal()
	{
		$session = JFactory::getSession();
		$config = JFactory::getConfig();
		
		$row = JTable::getInstance('RSForm_Forms', 'Table');
		$row->Lang = JFactory::getLanguage()->getDefault();
		$row->FormTitle = $session->get('com_rsform.wizard.FormTitle');
		$row->ScrollToThankYou = $session->get('com_rsform.wizard.ScrollToThankYou');
		if (empty($row->ScrollToThankYou)) {
			$row->ThankYouMessagePopUp = $session->get('com_rsform.wizard.ThankYouMessagePopUp');
		}
		if (empty($row->FormTitle))
			$row->FormTitle = JText::_('RSFP_FORM_DEFAULT_TITLE');
		$row->FormName = JFilterOutput::stringURLSafe($row->FormTitle);
		$row->FormLayoutName = $session->get('com_rsform.wizard.FormLayout');		
		if (empty($row->FormLayoutName))
		{
		    $rsformConfig = RSFormProConfig::getInstance();
            $row->FormLayoutName = $rsformConfig->get('global.default_layout', 'responsive');
            $row->LoadFormLayoutFramework = $rsformConfig->get('global.default_load_layout_framework', 1);
        }
		
		$AdminEmail = $session->get('com_rsform.wizard.AdminEmail');
		if ($AdminEmail)
		{
			$row->AdminEmailTo = $session->get('com_rsform.wizard.AdminEmailTo');
			$row->AdminEmailFrom = $config->get('mailfrom');
			$row->AdminEmailFromName = $config->get('fromname');
			$row->AdminEmailSubject = JText::sprintf('RSFP_ADMIN_EMAIL_DEFAULT_SUBJECT', $row->FormTitle);
			$row->AdminEmailText = JText::_('RSFP_ADMIN_EMAIL_DEFAULT_MESSAGE');
		}
		
		$UserEmail = $session->get('com_rsform.wizard.UserEmail');
		if ($UserEmail)
		{
			$row->UserEmailFrom = $config->get('mailfrom');
			$row->UserEmailFromName = $config->get('fromname');
			$row->UserEmailSubject = JText::_('RSFP_USER_EMAIL_DEFAULT_SUBJECT');
			$row->UserEmailText = JText::_('RSFP_USER_EMAIL_DEFAULT_MESSAGE');
		}
		
		$action = $session->get('com_rsform.wizard.SubmissionAction');
		if ($action == 'thankyou')
			$row->Thankyou = $session->get('com_rsform.wizard.Thankyou');
		elseif ($action == 'redirect')
			$row->ReturnUrl = $session->get('com_rsform.wizard.ReturnUrl');
		
		$filter = JFilterInput::getInstance();
		
		$layout = JPATH_ADMINISTRATOR.'/components/com_rsform/layouts/'.$filter->clean($row->FormLayoutName, 'path').'.php';
		
		$predefinedForm = JFactory::getApplication()->input->get('predefinedForm', '', 'string');
		
		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/quickfields.php';
		
		if (file_exists($layout) && !$predefinedForm)
		{
			$quickfields = array();
			$requiredfields = array();
			$this->_form = $row;
			
			$showFormTitle =  1;
			$requiredMarker = '(*)';
			$formOptions = false;
			
			$fieldsets = RSFormProQuickFields::getFieldNames('fieldsets');
			
			ob_start();
				// include the layout selected
				include $layout;
				$out = ob_get_contents();
			ob_end_clean();
			$row->FormLayout = $out;
		}
		
		if ($row->store())
		{
			if ($predefinedForm)
			{
				$path = JPATH_ADMINISTRATOR.'/components/com_rsform/assets/forms/'.$filter->clean($predefinedForm);
				if (file_exists($path.'/install.xml'))
				{
					$GLOBALS['q_FormId'] = $row->FormId;
					JFactory::getApplication()->input->set('formId', $row->FormId);
					
					$options = array();
					$options['cleanup'] = 0;
					
					require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/restore/legacy.php';
					
					$restore = new RSFormProRestore($options);
					$restore->setInstallDir($path);
					
					if ($restore->restore())
					{
						$row->load($row->FormId);
						
						$model = $this->getModel('forms');
						$quickfields = $model->getQuickFields();
						
						if ($AdminEmail && !empty($quickfields)){
							foreach ($quickfields as $quickfield) {
								$row->AdminEmailText .= "\n".'<p>{'.$quickfield['name'].':caption}: {'.$quickfield['name'].':value}</p>';
							}	
						}
						
						if ($UserEmail)
						{
							$row->UserEmailTo = '{Email:value}';
							if (!empty($quickfields)) {
								foreach ($quickfields as $quickfield) {
									$row->UserEmailText .= "\n".'<p>{'.$quickfield['name'].':caption}: {'.$quickfield['name'].':value}</p>';
								}	
							}		
						}
						
						// Genereate the layout
						if (file_exists($layout)) {
							$requiredfields = array();
							$this->_form = $row;
							$formId = $row->FormId;
							
							$showFormTitle =  1;
							$requiredMarker = '(*)';
							$formOptions = false;
							
							$fieldsets = RSFormProQuickFields::getFieldNames('fieldsets');
							
							ob_start();
								// include the layout selected
								include $layout;
								$out = ob_get_contents();
							ob_end_clean();
							$row->FormLayout = $out;
						}
						
						$row->store();
					}
				}
			}
		}
		
		$session->clear('com_rsform.wizard.FormTitle');
		$session->clear('com_rsform.wizard.FormLayout');
		$session->clear('com_rsform.wizard.AdminEmail');
		$session->clear('com_rsform.wizard.AdminEmailTo');
		$session->clear('com_rsform.wizard.UserEmail');
		$session->clear('com_rsform.wizard.SubmissionAction');
		$session->clear('com_rsform.wizard.Thankyou');
		$session->clear('com_rsform.wizard.ReturnUrl');
		
		$this->setRedirect('index.php?option=com_rsform&task=forms.edit&formId='.$row->FormId);
	}
	
	public function getProperty($fieldData, $prop, $default=null) {
		$model = $this->getModel('forms');
		
		return $model->getProperty($fieldData, $prop, $default);
	}
	
	public function getComponentType($componentId, $formId){
		$model = $this->getModel('forms');
		
		return $model->getComponentType($componentId, $formId);
	}
	
	public function save()
	{
		$formId = JFactory::getApplication()->input->getInt('formId');
		
		$model = $this->getModel('forms');
		$model->save();

		$task = $this->getTask();
		switch ($task)
		{
			case 'save':
				$link = 'index.php?option=com_rsform&view=forms';
			break;
			
			case 'apply':
				$tabposition = JFactory::getApplication()->input->getInt('tabposition', 0);
				$tab		 = JFactory::getApplication()->input->getInt('tab', 0);
				$link		 = 'index.php?option=com_rsform&task=forms.edit&formId='.$formId.'&tabposition='.$tabposition.'&tab='.$tab;
			break;
		}
		
		if (JFactory::getApplication()->input->getCmd('tmpl') == 'component') {
            $link .= '&tmpl=component';
        }
		
		$this->setRedirect($link, JText::_('RSFP_FORM_SAVED'));
	}
	
	public function cancel()
	{
		$this->setRedirect('index.php?option=com_rsform&view=forms');
	}
	
	public function delete() {
		$db = JFactory::getDbo();
		
		// Get the selected items
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
		
		// Force array elements to be integers
		$cid = array_map('intval', $cid);
		
		$total = count($cid);
		foreach ($cid as $formId) {
			// No point in continuing if FormId = 0.
			if (!$formId) {
				$total--;
				continue;
			}
			
			// Delete forms
			$query = $db->getQuery(true);
			$query->delete('#__rsform_forms')
				  ->where($db->qn('FormId').' = '.$db->q($formId));
			$db->setQuery($query)->execute();
			
			// Get all form fields
			$query = $db->getQuery(true);
			$query->select($db->qn('ComponentId'))
				  ->from('#__rsform_components')
				  ->where($db->qn('FormId').' = '.$db->q($formId));
			if ($fields = $db->setQuery($query)->loadColumn()) {
				// Delete fields
				$query = $db->getQuery(true);
				$query->delete('#__rsform_components')
					  ->where($db->qn('FormId').' = '.$db->q($formId));
				$db->setQuery($query)->execute();
				
				// Delete field properties
				$query = $db->getQuery(true);
				$query->delete('#__rsform_properties')
					  ->where($db->qn('ComponentId').' IN ('.implode(',', $fields).')');
				$db->setQuery($query)->execute();
			}

			// Delete calculations
			$query = $db->getQuery(true);
			$query->delete('#__rsform_calculations')
				  ->where($db->qn('formId').' = '.$db->q($formId));
			$db->setQuery($query)->execute();
			
			// Get all conditions
			$query = $db->getQuery(true);
			$query->select($db->qn('id'))
				  ->from('#__rsform_conditions')
				  ->where($db->qn('form_id').' = '.$db->q($formId));
			if ($conditions = $db->setQuery($query)->loadColumn()) {
				// Delete conditions
				$query = $db->getQuery(true);
				$query->delete('#__rsform_conditions')
					  ->where($db->qn('form_id').' = '.$db->q($formId));
				$db->setQuery($query)->execute();
				
				// Delete condition details
				$query = $db->getQuery(true);
				$query->delete('#__rsform_condition_details')
					  ->where($db->qn('condition_id').' IN ('.implode(',', $conditions).')');
				$db->setQuery($query)->execute();
			}
			
			// Delete directory
			$query = $db->getQuery(true);
			$query->delete('#__rsform_directory')
				  ->where($db->qn('formId').' = '.$db->q($formId));
			$db->setQuery($query)->execute();
			$query = $db->getQuery(true);
			$query->delete('#__rsform_directory_fields')
				  ->where($db->qn('formId').' = '.$db->q($formId));
			$db->setQuery($query)->execute();
			
			// Delete extra emails
			$query = $db->getQuery(true);
			$query->delete('#__rsform_emails')
				  ->where($db->qn('formId').' = '.$db->q($formId));
			$db->setQuery($query)->execute();
			
			// Delete mappings
			$query = $db->getQuery(true);
			$query->delete('#__rsform_mappings')
				  ->where($db->qn('formId').' = '.$db->q($formId));
			$db->setQuery($query)->execute();
			
			// Delete post to location
			$query = $db->getQuery(true);
			$query->delete('#__rsform_posts')
				  ->where($db->qn('form_id').' = '.$db->q($formId));
			$db->setQuery($query)->execute();
			
			// Delete translations
			$query = $db->getQuery(true);
			$query->delete('#__rsform_translations')
				  ->where($db->qn('form_id').' = '.$db->q($formId));
			$db->setQuery($query)->execute();
			
			// Remove from menu
			JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_menus/tables');
			
			$table = JTable::getInstance('Menu', 'MenusTable');
			$query = $db->getQuery(true)
				->select($db->qn('id'))
				->from($db->qn('#__menu'))
				->where($db->qn('client_id') . ' = ' . $db->q(1))
				->where($db->qn('link') . ' = ' . $db->q('index.php?option=com_rsform&view=forms&layout=show&formId=' . $formId));
			if ($ids = $db->setQuery($query)->loadColumn())
			{
				foreach ($ids as $id)
				{
					$table->delete($id);
					$table->rebuild(1);
				}
			}

            require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/submissions.php';
			RSFormProSubmissionsHelper::deleteAllSubmissions($formId);
			
			// Trigger Event - onFormDelete
			JFactory::getApplication()->triggerEvent('rsfp_onFormDelete', array(
				'formId' => $formId
			));
		}
		
		$this->setRedirect('index.php?option=com_rsform&view=forms', JText::sprintf('RSFP_FORMS_DELETED', $total));
	}
	
	public function changeStatus()
	{
		$task = $this->getTask();
		$db   = JFactory::getDbo();
		
		// Get the selected items
		$cid = JFactory::getApplication()->input->post->get('cid', array(), 'array');
		
		// Force array elements to be integers
		$cid = array_map('intval', $cid);
		
		$value = $task == 'publish' ? 1 : 0;
		
		$total = count($cid);
		if ($total > 0)
		{
			$formIds = implode(',', $cid);
			$db->setQuery("UPDATE #__rsform_forms SET Published = '".$value."' WHERE FormId IN (".$formIds.")");
			$db->execute();
		}
		
		$msg = $value ? JText::sprintf('RSFP_FORMS_PUBLISHED', $total) : JText::sprintf('RSFP_FORMS_UNPUBLISHED', $total);

		$this->setRedirect('index.php?option=com_rsform&view=forms', $msg);
	}
	
	public function copy()
	{
		$db 	= JFactory::getDbo();
		$app 	= JFactory::getApplication();
		$model 	= $this->getModel('forms');
		
		// Get the selected items
		$cid = $app->input->get('cid', array(), 'array');
		
		// Force array elements to be integers
		$cid = array_map('intval', $cid);
		
		$total = 0;
		foreach ($cid as $formId)
		{
			if (empty($formId))
				continue;
				
			$total++;
			
			$original = JTable::getInstance('RSForm_Forms', 'Table');
			$original->load($formId);
			$original->FormName .= ' copy';
			$original->FormTitle .= ' copy';
			$original->FormId = null;
			
			$copy = JTable::getInstance('RSForm_Forms', 'Table');
			$copy->bind($original);
			$copy->store();
			
			$copy->FormLayout = str_replace('rsform_'.$formId.'_page', 'rsform_'.$copy->FormId.'_page', $copy->FormLayout);
			if ($copy->FormLayout != $original->FormLayout)
				$copy->store();
			
			$newFormId = $copy->FormId;
			
			$componentRelations = array();
			$conditionRelations = array();
			$emailRelations		= array();
			
			// copy language
			$db->setQuery("SELECT * FROM #__rsform_translations WHERE `reference`='forms' AND `form_id`='".$formId."'");
			if ($translations = $db->loadObjectList())
			{
				foreach ($translations as $translation)
				{
				    $translation->id = null;
				    $translation->form_id = $newFormId;

                    $db->insertObject('#__rsform_translations', $translation);
				}
			}
			
			// copy additional emails
			$db->setQuery("SELECT * FROM #__rsform_emails WHERE `type` = 'additional' AND `formId`='".$formId."'");
			if ($emails = $db->loadObjectList()) {
				foreach ($emails as $email) {
					$new_email = JTable::getInstance('RSForm_Emails', 'Table');
					$new_email->bind($email);
					$new_email->id = null;
					$new_email->formId = $newFormId;
					$new_email->store();
					
					$emailRelations[$email->id] = $new_email->id;
				}

                // Copy language
                $query = $db->getQuery(true)
                    ->select('*')
                    ->from($db->qn('#__rsform_translations'))
                    ->where($db->qn('form_id') . ' = ' . $db->q($formId))
                    ->where($db->qn('reference') . ' = ' . $db->q('emails'));
				if ($translatedEmails = $db->setQuery($query)->loadObjectList())
                {
                    foreach ($translatedEmails as $translatedEmail) {

                        list($oldEmailId, $property) = explode('.', $translatedEmail->reference_id, 2);

                        if (!isset($emailRelations[$oldEmailId])) {
                            continue;
                        }

                        $emailTranslation = (object) array(
                            'form_id'       => $newFormId,
                            'lang_code'     => $translatedEmail->lang_code,
                            'reference'     => 'emails',
                            'reference_id'  => $emailRelations[$oldEmailId] . '.' . $property,
                            'value'         => $translatedEmail->value
                        );

                        $db->insertObject('#__rsform_translations', $emailTranslation);
                    }
                }
			}
			
			// copy mappings
			$db->setQuery("SELECT * FROM #__rsform_mappings WHERE `formId`='".$formId."'");
			if ($mappings = $db->loadObjectList()) {
				foreach ($mappings as $mapping) {
					$new_mapping = JTable::getInstance('RSForm_Mappings', 'Table');
					$new_mapping->bind($mapping);
					$new_mapping->id = null;
					$new_mapping->formId = $newFormId;
					$new_mapping->store();
				}
			}
			
			// copy post to location
			$db->setQuery("SELECT * FROM #__rsform_posts WHERE form_id='".$formId."'");
			if ($post = $db->loadObject())
			{
				$db->setQuery("INSERT INTO #__rsform_posts SET `form_id`='".(int) $newFormId."', `enabled`='".(int) $post->enabled."', `method`='".(int) $post->method."', `fields`=".$db->q($post->fields).", `silent`='".(int) $post->silent."', `url`=".$db->quote($post->url));
				$db->execute();
			}
			
			// copy calculations
			$db->setQuery("SELECT * FROM #__rsform_calculations WHERE formId='".$formId."'");
			if ($calculations = $db->loadObjectList()) {
				foreach ($calculations as $calculation) {
					$db->setQuery("INSERT INTO #__rsform_calculations SET `formId` = ".$db->q($newFormId).", `total` = ".$db->q($calculation->total).", `expression` = ".$db->q($calculation->expression).", `ordering` = ".$db->q($calculation->ordering));
					$db->execute();
				}
			}
			
			$db->setQuery("SELECT ComponentId FROM #__rsform_components WHERE FormId='".$formId."' ORDER BY `Order`");
			$components = $db->loadColumn();
			foreach ($components as $r)
			{
				$componentRelations[$r] = $model->copyComponent($r, $newFormId);
			}
			
			// Handle dynamic properties
			$db->setQuery("SELECT * FROM #__rsform_properties WHERE ComponentId IN (".implode(',', $componentRelations).") AND PropertyName IN ('EMAILATTACH', 'VALIDATIONCALENDAR')");
			if ($properties = $db->loadObjectList())
			{
				foreach ($properties as $property)
				{
					if ($property->PropertyName == 'EMAILATTACH' && $property->PropertyValue)
					{
						$values 	= explode(',', $property->PropertyValue);
						$newValues 	= array();
						
						foreach ($values as $value)
						{
							if (isset($emailRelations[$value]))
							{
								$newValues[] = $emailRelations[$value];
							}
							elseif (in_array($value, array('adminemail', 'useremail')))
							{
								$newValues[] = $value;
							}
						}
						
						$property->PropertyValue = implode(',', $newValues);
					}
					
					if ($property->PropertyName == 'VALIDATIONCALENDAR' && $property->PropertyValue)
					{
						list($type, $oldCalendarId) = explode(' ', $property->PropertyValue, 2);
						if (isset($componentRelations[$oldCalendarId]))
						{
							$property->PropertyValue = $type.' '.$componentRelations[$oldCalendarId];
						}
					}
					
					$db->setQuery("UPDATE #__rsform_properties SET PropertyValue=".$db->quote($property->PropertyValue)." WHERE PropertyId=".$db->quote($property->PropertyId));
					$db->execute();
				}
			}
			
			// copy conditions
			$db->setQuery("SELECT * FROM #__rsform_conditions WHERE form_id='".$formId."'");
			if ($conditions = $db->loadObjectList())
			{
				foreach ($conditions as $condition)
				{
					$new_condition = JTable::getInstance('RSForm_Conditions', 'Table');
					$new_condition->bind($condition);
					$new_condition->id = null;
					$new_condition->form_id = $newFormId;
					$new_condition->component_id = $componentRelations[$condition->component_id];
					$new_condition->store();
					
					$conditionRelations[$condition->id] = $new_condition->id;
				}
				
				$db->setQuery("SELECT * FROM #__rsform_condition_details WHERE condition_id IN (".implode(',', array_keys($conditionRelations)).")");
				if ($details = $db->loadObjectList())
				{
					foreach ($details as $detail)
					{
						$new_detail = JTable::getInstance('RSForm_Condition_Details', 'Table');
						$new_detail->bind($detail);
						$new_detail->id = null;
						$new_detail->condition_id = $conditionRelations[$detail->condition_id];
						$new_detail->component_id = $componentRelations[$detail->component_id];
						$new_detail->store();
					}
				}
			}

			// Rebuild Grid Layout
            if (!empty($copy->GridLayout))
            {
                $data   = json_decode($copy->GridLayout, true);
                $rows 	= array();
                $hidden	= array();

                // If decoding is successful, we should have $rows and $hidden
                if (is_array($data) && isset($data[0], $data[1]))
                {
                    $rows 	= $data[0];
                    $hidden = $data[1];
                }

                if ($rows)
                {
                    foreach ($rows as $row_index => &$row)
                    {
                        foreach ($row['columns'] as $column_index => $fields)
                        {
                            foreach ($fields as $position => $id)
                            {
                                if (isset($componentRelations[$id]))
                                {
                                    $row['columns'][$column_index][$position] = $componentRelations[$id];
                                }
                                else
                                {
                                    // Field doesn't exist, remove it from grid
                                    unset($row['columns'][$column_index][$position]);
                                }
                            }
                        }
                    }
					unset($row);
                }

                if ($hidden)
                {
                    foreach ($hidden as $hidden_index => $id)
                    {
                        if (isset($componentRelations[$id]))
                        {
                            $hidden[$hidden_index] = $componentRelations[$id];
                        }
                        else
                        {
                            // Field doesn't exist, remove it from grid
                            unset($hidden[$hidden_index]);
                        }
                    }
                }

                $query = $db->getQuery(true);
                $query->update('#__rsform_forms')
                    ->set($db->qn('GridLayout') .'='. $db->q(json_encode(array($rows, $hidden))))
                    ->where($db->qn('FormId') .'='. $db->q($copy->FormId));
                $db->setQuery($query)->execute();
            }
			
			//Trigger Event - onFormCopy
			$app->triggerEvent('rsfp_bk_onFormCopy', array(
				array(
					'formId' => $formId,
					'newFormId' => $newFormId,
					'components' => $components,
					'componentRelations' => $componentRelations
				)
			));
		}
		
		$this->setRedirect('index.php?option=com_rsform&view=forms', JText::sprintf('RSFP_FORMS_COPIED', $total));
	}
	
	public function changeAutoGenerateLayout()
	{
		$app			= JFactory::getApplication();
		$formId 		= $app->input->getInt('formId');
		$status 		= $app->input->getInt('status');
		$formLayoutName = $app->input->getCmd('formLayoutName');
		$db 			= JFactory::getDbo();

		$query = $db->getQuery(true)
			->update($db->qn('#__rsform_forms'))
			->set($db->qn('FormLayoutAutogenerate').'='.$db->q($status))
			->set($db->qn('FormLayoutName').'='.$db->q($formLayoutName))
			->where($db->qn('FormId').'='.$db->q($formId));

		$db->setQuery($query)
			->execute();

		echo json_encode(array(
			'status' => true
		));

		$app->close();
	}

    public function changeFormLayoutFlow()
    {
        $app			= JFactory::getApplication();
        $formId 		= $app->input->getInt('formId');
        $status 		= $app->input->getInt('status');
        $db 			= JFactory::getDbo();

        $query = $db->getQuery(true)
            ->update($db->qn('#__rsform_forms'))
            ->set($db->qn('FormLayoutFlow').'='.$db->q($status))
            ->where($db->qn('FormId').'='.$db->q($formId));

        $db->setQuery($query)
            ->execute();

        echo json_encode(array(
            'status' => true
        ));

        $app->close();
    }
	
	public function calculations() {
		$db 		= JFactory::getDbo();
		$formId 	= JFactory::getApplication()->input->getInt('formId');
		$total		= JFactory::getApplication()->input->get('total', '', 'raw');
		$expression	= JFactory::getApplication()->input->get('expression', '', 'raw');
		
		$db->setQuery("SELECT MAX(`ordering`) FROM #__rsform_calculations WHERE `formId` = ".$formId);
		$ordering = (int) $db->loadResult() + 1;
		
		$db->setQuery("INSERT INTO #__rsform_calculations SET `formId` = ".$formId.", `total` = ".$db->q($total).", `expression` = ".$db->q($expression).", `ordering` = ".(int) $ordering." ");
		$db->execute();
		
		echo $db->insertid().'|'.$ordering;
		jexit();
	}
	
	public function removeCalculation() {
		$db 		= JFactory::getDbo();
		$id		 	= JFactory::getApplication()->input->getInt('id');

		$query = $db->getQuery(true)
            ->delete($db->qn('#__rsform_calculations'))
            ->where($db->qn('id') . ' = ' . $db->q($id));
		
		$db->setQuery($query);
		if ($db->execute()) {
			echo 1;
		} else {
            echo 0;
        }

		jexit();
	}
	
	public function saveCalculationsOrdering() {
		$db		= JFactory::getDbo();
		$cids	= JFactory::getApplication()->input->get('cid', array(), 'array');
		$formId	= JFactory::getApplication()->input->getInt('formId',0);
		
		foreach ($cids as $key => $order) {
			$db->setQuery("UPDATE #__rsform_calculations SET `ordering`='".$order."' WHERE id='".$key."' AND `formId` = '".$formId."' ");
			$db->execute();
		}
		
		echo 'Ok';
		exit();
	}
	
	public function saveGridLayout()
	{
		$app	= JFactory::getApplication();
		$db 	= JFactory::getDbo();
		$query  = $db->getQuery(true);
		$formId = $app->input->getInt('formId');
		$layout = $app->input->getString('GridLayout');
		$model	= $this->getModel('forms');
		
		$data = json_decode($layout, true);
		
		if (is_array($data) && isset($data[0], $data[1]))
		{
			$rows 	= $data[0];
			$hidden = $data[1];
			
			$flat = array();
			foreach ($rows as $row)
			{
				foreach ($row['columns'] as $column => $fields)
				{
					foreach ($fields as $field)
					{
						$flat[] = $field;
					}
				}
			}
			
			$flat = array_merge($flat, $hidden);
			
			foreach ($flat as $position => $id)
			{
				$query->update($db->qn('#__rsform_components'))
					->set($db->qn('Order').'='.$db->q($position))
					->where($db->qn('ComponentId').'='.$db->q($id));

				$db->setQuery($query)
					->execute();
				
				$query->clear();
			}
		}

		$query->update($db->qn('#__rsform_forms'))
			->set($db->qn('GridLayout').'='.$db->q($layout))
			->where($db->qn('FormId').'='.$db->q($formId));

		$db->setQuery($query)
			->execute();

		// Auto generate layout
		$model->getForm();
		if ($model->_form->FormLayoutAutogenerate)
		{
			$model->autoGenerateLayout();
		}
		
		echo $model->_form->FormLayout;

		$app->close();
	}
}