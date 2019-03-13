<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformControllerComponents extends RsformController
{
	public function __construct()
	{
		parent::__construct();

		$this->registerTask('apply', 	 'save');
		$this->registerTask('new', 	 	 'add');
		$this->registerTask('publish',   'changestatus');
		$this->registerTask('unpublish', 'changestatus');

		$this->registerTask('setrequired',   'changerequired');
		$this->registerTask('unsetrequired', 'changerequired');

		$this->_db = JFactory::getDbo();
	}

	public function save()
	{
		$db = JFactory::getDbo();
		$app               = JFactory::getApplication();
		$componentType 	   = $app->input->getInt('COMPONENTTYPE');
		$componentIdToEdit = $app->input->getInt('componentIdToEdit');
		$formId 		   = $app->input->getInt('formId');

        $params = $app->input->post->get('param', array(), 'raw');
		$params['EMAILATTACH'] = !empty($params['EMAILATTACH']) ? implode(',',$params['EMAILATTACH']) : '';
		if (isset($params['VALIDATIONRULE']) && $params['VALIDATIONRULE'] == 'multiplerules') {
			$params['VALIDATIONMULTIPLE'] = !empty($params['VALIDATIONMULTIPLE']) ? implode(',',$params['VALIDATIONMULTIPLE']) : '';
			$params['VALIDATIONEXTRA'] = !empty($params['VALIDATIONEXTRA']) ? json_encode($params['VALIDATIONEXTRA']) : '';
		}

		$just_added = false;
		if ($componentIdToEdit < 1)
		{
		    $query = $db->getQuery(true)
                ->select('MAX( ' . $db->qn('Order') . ')')
                ->from($db->qn('#__rsform_components'))
                ->where($db->qn('FormId') . ' = ' . $db->q($formId));
		    $nextOrder = (int) $db->setQuery($query)->loadResult() + 1;

		    $component = (object) array(
		        'FormId'            => $formId,
                'ComponentTypeId'   => $componentType,
                'Order'             => $nextOrder
            );

		    $db->insertObject('#__rsform_components', $component, 'ComponentId');

			$componentIdToEdit = $component->ComponentId;
			$just_added = true;
		}

		$model = $this->getModel('forms');
		$lang  = $model->getLang();

		if (!$just_added && isset($params['ITEMS'])) {
			$db->setQuery("SELECT cd.* FROM #__rsform_condition_details cd LEFT JOIN #__rsform_conditions c ON (cd.condition_id=c.id) WHERE cd.component_id='".$componentIdToEdit."' AND c.lang_code=".$db->quote($lang));
			if ($conditions = $db->loadObjectList()) {
				$data 		= RSFormProHelper::getComponentProperties($componentIdToEdit);
				$oldvalues 	= RSFormProHelper::explode(RSFormProHelper::isCode($data['ITEMS']));
				$newvalues 	= RSFormProHelper::explode(RSFormProHelper::isCode($params['ITEMS']));

				foreach ($oldvalues as $i => $oldvalue) {
					$tmp = explode('|', $oldvalue, 2);
					$oldvalue = reset($tmp);
					$oldvalue = str_replace(array('[c]', '[g]'), '', $oldvalue);

					$oldvalues[$i] = $oldvalue;
				}

				foreach ($newvalues as $i => $newvalue) {
					$tmp = explode('|', $newvalue, 2);
					$newvalue = reset($tmp);
					$newvalue = str_replace(array('[c]', '[g]'), '', $newvalue);

					$newvalues[$i] = $newvalue;
				}

				foreach ($conditions as $condition) {
					$oldPos = array_search($condition->value, $oldvalues);
					$newPos = array_search($condition->value, $newvalues);

					if ($newPos === false && $oldPos !== false && isset($newvalues[$oldPos])) {
						$newvalue = $newvalues[$oldPos];
						if ($condition->value != $newvalue) {
							$db->setQuery("UPDATE #__rsform_condition_details SET `value`=".$db->quote($newvalue)." WHERE id='".$condition->id."'");
							$db->execute();
						}
					}
				}
			}
		}

		$properties = array();
		if ($componentIdToEdit > 0)
		{
            $query = $db->getQuery(true);
            $query->select($db->qn('PropertyName'))
                ->from($db->qn('#__rsform_properties'))
                ->where($db->qn('ComponentId') . ' = ' . $db->q($componentIdToEdit))
                ->where($db->qn('PropertyName') . ' IN (' . implode(',', $db->q(array_keys($params))) . ')');
            $db->setQuery($query);
            $properties = $db->loadColumn();
        }

		if ($model->_form->Lang != $lang && !RSFormProHelper::getConfig('global.disable_multilanguage')) {
            $model->saveFormPropertyTranslation($formId, $componentIdToEdit, $params, $lang, $just_added, $properties);
        }

		if ($componentIdToEdit > 0)
		{
			foreach ($params as $key => $val)
			{
				/**
				 * Sanitize the file extensions field
				 */
				if($key == 'ACCEPTEDFILES')
				{
					$sanitized = array();

					foreach (explode('\r\n', $val) as $extension)
					{
						$sanitized[] = ltrim($extension, '.');
					}

					$val = implode('\r\n', $sanitized);
				}

				$property = (object) array(
				    'PropertyValue' => $val,
                    'PropertyName'  => $key,
                    'ComponentId'   => $componentIdToEdit
                );

				if (in_array($key, $properties))
				{
                    $db->updateObject('#__rsform_properties', $property, array('PropertyName', 'ComponentId'));
				}
				else
				{
                    $db->insertObject('#__rsform_properties', $property);
				}
			}
		}

		$link = 'index.php?option=com_rsform&task=forms.edit&formId='.$formId;
        if ($app->input->getInt('tabposition')) {
            $link .= '&tabposition=1';
            if ($tab = $app->input->getInt('tab')) {
                $link .= '&tab=' . $tab;
            }
        }
		if ($app->input->getCmd('tmpl') == 'component') {
            $link .= '&tmpl=component';
        }

		$this->setRedirect($link);
	}

    public function saveOrdering()
    {
        $db 	= JFactory::getDbo();
        $query 	= $db->getQuery(true);
        $input 	= JFactory::getApplication()->input;
        $keys 	= $input->post->get('cid', array(), 'array');

        foreach ($keys as $key => $val)
        {
            $query->update($db->qn('#__rsform_components'))
                ->set($db->qn('Order') . ' = ' . $db->q($val))
                ->where($db->qn('ComponentId') . ' = ' . $db->q($key));

            $db->setQuery($query)->execute();

            $query->clear();
        }

        echo 'Ok';

        exit();
    }

	public function validateName()
	{
		try {
			$input = JFactory::getApplication()->input;

			// Make sure field name doesn't contain invalid characters
			$name = $input->get('componentName', '', 'raw');

			if (empty($name)) {
				throw new Exception(JText::_('RSFP_SAVE_FIELD_EMPTY_NAME'), 0);
			}

			if (preg_match('#[^a-zA-Z0-9_\- ]#', $name)) {
				throw new Exception(JText::_('RSFP_SAVE_FIELD_NOT_VALID_NAME'), 0);
			}

			if ($name == 'elements') {
				throw new Exception(JText::sprintf('RSFP_SAVE_FIELD_RESERVED_NAME', $name), 0);
			}

			$componentType 		= $input->post->getInt('componentType');
			$currentComponentId = $input->getInt('currentComponentId');
			$formId				= $input->getInt('formId');

			if (RSFormProHelper::componentNameExists($name, $formId, $currentComponentId)) {
				throw new Exception(JText::_('RSFP_SAVE_FIELD_ALREADY_EXISTS'), 0);
			}

			// On File upload field, check destination
			if ($componentType == RSFORM_FIELD_FILEUPLOAD) {
				$destination = RSFormProHelper::getRelativeUploadPath($input->get('destination', '', 'raw'));

				if (empty($destination)) {
					throw new Exception(JText::_('RSFP_ERROR_DESTINATION_MSG'), 2);
				} elseif (!is_dir($destination)) {
					throw new Exception(JText::_('RSFP_ERROR_DESTINATION_MSG'), 2);
				} elseif (!is_writable($destination)) {
					throw new Exception(JText::_('RSFP_ERROR_DESTINATION_WRITABLE_MSG'), 2);
				}

			}

			echo json_encode(array(
				'result' => true
			));

		} catch (Exception $e) {
			echo json_encode(array(
				'message' => $e->getMessage(),
				'result'  => false,
				'tab'	  => (int) $e->getCode()
			));
		}

		$this->close();
	}

	protected function close() {
		JFactory::getApplication()->close();
	}

	public function display($cachable = false, $urlparams = false)
	{
		JFactory::getApplication()->input->set('view', 	'formajax');
		JFactory::getApplication()->input->set('layout', 	'component');
		JFactory::getApplication()->input->set('format', 	'raw');

		parent::display($cachable, $urlparams);
	}

    public function copyProcess()
	{
		$toFormId 	= JFactory::getApplication()->input->getInt('toFormId');
		$cids 		= JFactory::getApplication()->input->get('cid', array(), 'array');
		$model 		= $this->getModel('forms');

		$cids = array_map('intval', $cids);

		foreach ($cids as $cid) {
			$model->copyComponent($cid, $toFormId);
		}

		$this->setRedirect('index.php?option=com_rsform&task=forms.edit&formId='.$toFormId, JText::sprintf('RSFP_COMPONENTS_COPIED', count($cids)));
	}

    public function copy()
	{
		$formId = JFactory::getApplication()->input->getInt('formId');
		$db = JFactory::getDbo();
		$db->setQuery("SELECT FormId FROM #__rsform_forms WHERE FormId != '".$formId."'");
		if (!$db->loadResult())
			return $this->setRedirect('index.php?option=com_rsform&task=forms.edit&formId='.$formId, JText::_('RSFP_NEED_MORE_FORMS'));

		JFactory::getApplication()->input->set('view', 'forms');
		JFactory::getApplication()->input->set('layout', 'component_copy');

		parent::display();
	}

    public function copyCancel()
	{
		$formId = JFactory::getApplication()->input->getInt('formId');
		$this->setRedirect('index.php?option=com_rsform&task=forms.edit&formId='.$formId);
	}

    public function duplicate()
	{
		$formId = JFactory::getApplication()->input->getInt('formId');
        $cids 	= JFactory::getApplication()->input->get('cid', array(), 'array');
		$model 	= $this->getModel('forms');

		$cids = array_map('intval', $cids);
		foreach ($cids as $cid) {
			$model->copyComponent($cid, $formId);
		}

		$this->setRedirect('index.php?option=com_rsform&task=forms.edit&formId='.$formId, JText::sprintf('RSFP_COMPONENTS_COPIED', count($cids)));
	}

    public function changeStatus()
	{
		$model = $this->getModel('formajax');
		$model->componentsChangeStatus();
		$componentId = $model->getComponentId();

		if (is_array($componentId))
		{
			$formId = JFactory::getApplication()->input->getInt('formId');

			$task = $this->getTask();
			$msg = 'RSFP_ITEMS_UNPUBLISHED';
			if ($task == 'publish')
				$msg = 'RSFP_ITEMS_PUBLISHED';

			$this->setRedirect('index.php?option=com_rsform&task=forms.edit&formId='.$formId, JText::sprintf($msg, count($componentId)));
		}
		// Ajax request
		else
		{
			JFactory::getApplication()->input->set('view', 'formajax');
			JFactory::getApplication()->input->set('layout', 'component_published');
			JFactory::getApplication()->input->set('format', 'raw');

			parent::display();
		}
	}

    public function changeRequired()
	{
		$model = $this->getModel('formajax');
		$model->componentsChangeRequired();
		$componentId = $model->getComponentId();

		JFactory::getApplication()->input->set('view', 'formajax');
		JFactory::getApplication()->input->set('layout', 'component_required');
		JFactory::getApplication()->input->set('format', 'raw');

		parent::display();
	}

	public function remove()
	{
		$app	= JFactory::getApplication();
		$db 	= JFactory::getDbo();
		$formId = $app->input->getInt('formId');
		$ajax 	= $app->input->getInt('ajax');
		$cids 	= $app->input->get('cid', array(), 'array');

		// Escape IDs and implode them so they can be used in the queries below
		$componentIds = $cids;
		array_walk($componentIds, array('RSFormProHelper', 'quoteArray'));
		$componentIds = implode(',', $componentIds);

		if ($cids) {
			// Delete form fields
			$query = $db->getQuery(true)
				->delete($db->qn('#__rsform_components'))
				->where($db->qn('ComponentId').' IN ('.$componentIds.')');
			$db->setQuery($query)
				->execute();

			// Delete leftover properties
			$query->clear()
				->delete($db->qn('#__rsform_properties'))
				->where($db->qn('ComponentId').' IN ('.$componentIds.')');
			$db->setQuery($query)
				->execute();

			// Delete translations
			$query->clear()
				->delete($db->qn('#__rsform_translations'));
			foreach ($cids as $cid) {
				$query->where($db->qn('reference_id').' LIKE '.$db->q((int) $cid.'.%'), 'OR');
			}
			$db->setQuery($query)
				->execute();
			
			// Delete conditions
			$query->clear()
				->select($db->qn('id'))
				->from($db->qn('#__rsform_conditions'))
				->where($db->qn('component_id').' IN ('.$componentIds.')');
			if ($condition_ids = $db->setQuery($query)->loadColumn())
			{
				$query->clear()
					->delete($db->qn('#__rsform_condition_details'))
					->where($db->qn('condition_id').' IN ('.implode(',', $condition_ids).')');
				$db->setQuery($query)
					->execute();

				$query->clear()
					->delete($db->qn('#__rsform_conditions'))
					->where($db->qn('component_id').' IN ('.$componentIds.')');
				$db->setQuery($query)
					->execute();
			}
			$query->clear()
				->delete($db->qn('#__rsform_condition_details'))
				->where($db->qn('component_id').' IN ('.$componentIds.')');
			$db->setQuery($query)
				->execute();
			
			// Reorder
			$query->clear()
				->select($db->qn('ComponentId'))
				->from($db->qn('#__rsform_components'))
				->where($db->qn('FormId').'='.$db->q($formId))
				->order($db->qn('Order'));
			$components = $db->setQuery($query)->loadColumn();

			$i = 1;
			foreach ($components as $componentId) {
				$query->clear()
					->update($db->qn('#__rsform_components'))
					->set($db->qn('Order').'='.$db->q($i))
					->where($db->qn('ComponentId').'='.$db->q($componentId));
				$db->setQuery($query)
					->execute();
				$i++;
			}
		}

		if ($ajax)
		{
			echo json_encode(array(
				'result' 	=> true,
				'submit' 	=> $this->getModel('forms')->getHasSubmitButton()
			));

			$app->close();
		}

		$this->setRedirect('index.php?option=com_rsform&task=forms.edit&formId='.$formId, JText::sprintf('ITEMS REMOVED', count($cids)));
	}
}