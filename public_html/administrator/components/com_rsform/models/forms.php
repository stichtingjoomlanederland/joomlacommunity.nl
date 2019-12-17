<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2019 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

class RsformModelForms extends JModelList
{
	public $_mdata = null;
	public $_mtotal = 0;

	/* @var TableRSForm_Forms */
	public $_form = null;

	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'FormTitle',
				'FormName',
				'Published',
				'FormId'
			);
		}

		parent::__construct($config);
	}

	protected function populateState($ordering = 'FormId', $direction = 'asc')
	{
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string');
		$this->setState('filter_search', $search);

		// List state information.
		parent::populateState($ordering, $direction);
	}

	protected function getListQuery()
	{
		$filter_search = $this->getState('filter_search');
		$lang		   = JFactory::getLanguage();
		$query		   = $this->_db->getQuery(true);
		$or 	= array();
		$ids 	= array();

		// Flag to know if we need translations - no point in doing a join if we're only using the default language.
		if (RSFormProHelper::getConfig('global.disable_multilanguage') && RSFormProHelper::getConfig('global.default_language') == 'en-GB')
		{
			$needs_translation = false;
		}
		else
		{
			// Must check if we've changed the language for some forms (each form has its own remembered language).
			if ($sessions = JFactory::getSession()->get('com_rsform.form'))
			{
				// For each form in the session, we join a specific language and form id.
				foreach ($sessions as $form => $data)
				{
					if (strpos($form, 'formId') === 0 && isset($data->lang))
					{
						$id 	= (int) substr($form, strlen('formId'));
						$ids[] 	= $id;
						$or[] 	= '(' . $this->_db->qn('t.lang_code') . ' = ' . $this->_db->q($data->lang) . ' AND ' . $this->_db->qn('t.form_id') . ' = ' . $this->_db->q($id) . ')';
					}
				}

				// Now that we've joined the session forms, we must remove them so they do not show up as duplicates.
				if ($ids)
				{
					$or[] = '(' . $this->_db->qn('t.lang_code') . ' = ' . $this->_db->q($lang->getTag()) . ' AND ' . $this->_db->qn('t.form_id') . ' NOT IN (' . implode(',', $this->_db->q($ids)) . '))';
				}
			}

			$needs_translation = $lang->getTag() != $lang->getDefault() || $ids || (RSFormProHelper::getConfig('global.disable_multilanguage') && RSFormProHelper::getConfig('global.default_language') != 'en-GB');
		}

		$query->select($this->_db->qn('f.FormId'))
			->select($this->_db->qn('f.FormName'))
			->select($this->_db->qn('f.Backendmenu'))
			->select($this->_db->qn('f.Published'))
			->from($this->_db->qn('#__rsform_forms', 'f'));

		if ($needs_translation)
		{
			$query->select('IFNULL(' . $this->_db->qn('t.value') . ', ' . $this->_db->qn('f.FormTitle') . ') AS FormTitle');
		}
		else
		{
			$query->select($this->_db->qn('f.FormTitle'));
		}

		if ($needs_translation)
		{
			$on = array(
				$this->_db->qn('f.FormId') . ' = ' . $this->_db->qn('t.form_id'),
				$this->_db->qn('t.reference') . ' = ' . $this->_db->q('forms'),
				$this->_db->qn('t.reference_id') . ' = ' . $this->_db->q('FormTitle')
			);

			if ($or && !RSFormProHelper::getConfig('global.disable_multilanguage'))
			{
				$on[] = '(' . implode(' OR ', $or) . ')';
			}
			else
			{
				if (RSFormProHelper::getConfig('global.default_language') == 'en-GB')
				{
					$on[] = $this->_db->qn('t.lang_code') . ' = ' . $this->_db->q($lang->getTag());
				}
				else
				{
					$on[] = $this->_db->qn('t.lang_code') . ' = ' . $this->_db->q(RSFormProHelper::getConfig('global.default_language'));
				}
			}

			$query->join('left', $this->_db->qn('#__rsform_translations', 't') . ' ON (' . implode(' AND ', $on) . ')');
		}

		if (!empty($filter_search))
		{
			$query->having('(' . $this->_db->qn('FormTitle') . ' LIKE ' . $this->_db->q('%' . $filter_search . '%') . ' OR ' . $this->_db->qn('FormName') . ' LIKE ' . $this->_db->q('%' . $filter_search . '%') . ')');
		}

		$query->order($this->_db->qn($this->getSortColumn()) . ' ' . $this->_db->escape($this->getSortOrder()));

		return $query;
	}

	public function _buildMQuery()
	{
		$db 	= JFactory::getDbo();
		$formId	= JFactory::getApplication()->input->getInt('formId');

		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('#__rsform_mappings'))
			->where($db->qn('formId') . ' = ' . $db->q($formId))
			->order($db->qn('ordering') . ' ' . $db->escape('asc'));

		return $query;
	}

	public function getForms()
	{
		$items = $this->getItems();

		$formIds = array();
		foreach ($items as $row)
		{
			$formIds[] = $row->FormId;
		}

		if ($formIds)
		{
			$date = JFactory::getDate();

			// Count submissions
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('COUNT(' . $db->qn('SubmissionId') . ') AS ' . $db->qn('total'))
				->select($db->qn('FormId'))
				->from($db->qn('#__rsform_submissions'))
				->where($db->qn('FormId') . ' IN (' . implode(',', $db->q($formIds)) . ')')
				->group($db->qn('FormId'));
			$allSubmissions = $db->setQuery($query)->loadObjectList('FormId');

			$query = $db->getQuery(true)
				->select('COUNT(' . $db->qn('SubmissionId') . ') AS ' . $db->qn('total'))
				->select($db->qn('FormId'))
				->from($db->qn('#__rsform_submissions'))
				->where($db->qn('FormId') . ' IN (' . implode(',', $db->q($formIds)) . ')')
				->where('DATE_FORMAT(' . $db->qn('DateSubmitted') . ', ' . $db->q('%Y-%m') . ') = ' . $db->q($date->format('Y-m')))
				->group($db->qn('FormId'));
			$monthSubmissions = $db->setQuery($query)->loadObjectList('FormId');

			$query = $db->getQuery(true)
				->select('COUNT(' . $db->qn('SubmissionId') . ') AS ' . $db->qn('total'))
				->select($db->qn('FormId'))
				->from($db->qn('#__rsform_submissions'))
				->where($db->qn('FormId') . ' IN (' . implode(',', $db->q($formIds)) . ')')
				->where('DATE_FORMAT(' . $db->qn('DateSubmitted') . ', ' . $db->q('%Y-%m-%d') . ') = ' . $db->q($date->format('Y-m-d')))
				->group($db->qn('FormId'));
			$todaySubmissions = $db->setQuery($query)->loadObjectList('FormId');

			foreach ($items as $form)
			{
				$form->_todaySubmissions = isset($todaySubmissions[$form->FormId]) ? $todaySubmissions[$form->FormId]->total : 0;
				$form->_monthSubmissions = isset($monthSubmissions[$form->FormId]) ? $monthSubmissions[$form->FormId]->total : 0;
				$form->_allSubmissions = isset($allSubmissions[$form->FormId]) ? $allSubmissions[$form->FormId]->total : 0;
			}
		}

		return $items;
	}

	public function getFilterBar()
	{
		require_once JPATH_COMPONENT.'/helpers/adapters/filterbar.php';
		// Search filter
		$options['search'] = array(
			'label' => JText::_('JSEARCH_FILTER'),
			'value' => $this->getState('filter_search')
		);
		$options['reset_button'] = true;

		$options['limitBox'] = $this->getPagination()->getLimitBox();
		$options['orderDir'] = false;

		$bar = new RSFilterBar($options);

		return $bar;
	}

	public function getSortColumn()
	{
		return $this->getState('list.ordering', 'FormId');
	}

	public function getSortOrder()
	{
		return $this->getState('list.direction', 'ASC');
	}

	public function getHasSubmitButton()
	{
		$formId = JFactory::getApplication()->input->getInt('formId');

		$query = $this->_db->getQuery(true)
            ->select($this->_db->qn('ComponentId'))
            ->from($this->_db->qn('#__rsform_components'))
            ->where($this->_db->qn('FormId') . ' = ' . $this->_db->q($formId))
            ->where($this->_db->qn('ComponentTypeId') . ' = ' . $this->_db->q(RSFORM_FIELD_SUBMITBUTTON));

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	public function getFields()
	{
		$formId = JFactory::getApplication()->input->getInt('formId');

		$return = array();

		$query = $this->_db->getQuery(true)
			->select($this->_db->qn('p.PropertyValue', 'ComponentName'))
			->select('c.*')
			->select($this->_db->qn('ct.ComponentTypeName'))
			->from($this->_db->qn('#__rsform_components', 'c'))
			->leftJoin($this->_db->qn('#__rsform_properties', 'p') . ' ON (' . $this->_db->qn('c.ComponentId') . ' = ' . $this->_db->qn('p.ComponentId') . ' AND ' . $this->_db->qn('p.PropertyName') . ' = ' . $this->_db->q('NAME') . ')')
			->leftJoin($this->_db->qn('#__rsform_component_types', 'ct') . ' ON (' . $this->_db->qn('ct.ComponentTypeId') . ' = ' . $this->_db->qn('c.ComponentTypeId') . ')')
			->where($this->_db->qn('c.FormId') . ' = ' . $this->_db->q($formId))
			->order($this->_db->qn('c.Order') . ' ' . $this->_db->escape('asc'));

		$this->_db->setQuery($query);
		$components = $this->_db->loadObjectList();

		$properties = RSFormProHelper::getComponentProperties($components);

		$pages = array();
		foreach ($components as $component)
		{
			if ($component->ComponentTypeId == RSFORM_FIELD_PAGEBREAK) {
				$pages[] = $component->ComponentId;
			}
		}

		foreach ($components as $component)
		{
			$data = $properties[$component->ComponentId];
			$data['componentId'] = $component->ComponentId;
			$data['componentTypeId'] = $component->ComponentTypeId;
			$data['ComponentTypeName'] = $component->ComponentTypeName;

			// Pagination
			if ($component->ComponentTypeId == RSFORM_FIELD_PAGEBREAK)
			{
				$data['PAGES'] = $pages;
			}

			$field = new stdClass();
			$field->id = $component->ComponentId;
			$field->type_id = $component->ComponentTypeId;
			$field->type_name = $component->ComponentTypeName;
			$field->name = $component->ComponentName;
			$field->published = $component->Published;
			$field->ordering = $component->Order;
			$field->preview = $this->showPreview($formId, $field->id, $data);

			$field->caption = isset($data['CAPTION']) && strlen($data['CAPTION']) ? $data['CAPTION'] : $field->name;

			if (!empty($data['REQUIRED']))
			{
				$field->hasRequired = true;
				$field->required = $data['REQUIRED'] == 'YES';
			}
			else
			{
				$field->required = false;
			}

			if (isset($data['VALIDATIONRULE']) && $data['VALIDATIONRULE'] != 'none')
			{
				$field->validation = $data['VALIDATIONRULE'];
			}
			elseif (isset($data['VALIDATIONRULE_DATE']) && $data['VALIDATIONRULE_DATE'] != 'none')
			{
				$field->validation = $data['VALIDATIONRULE_DATE'];
			}

			$return[$field->id] = $field;
		}
		
		return $return;
	}

	protected function showPreview($formId, $componentId, $data)
	{
		$mainframe 		= JFactory::getApplication();
		$formId 		= (int) $formId;
		$componentId 	= (int) $componentId;

		// Legacy
		$r = array();
		$r['ComponentTypeName'] = $data['ComponentTypeName'];

		$out = '';

		//Trigger Event - rsfp_bk_onBeforeCreateComponentPreview
		$mainframe->triggerEvent('rsfp_bk_onBeforeCreateComponentPreview',array(array('out'=>&$out,'formId'=>$formId,'componentId'=>$componentId,'ComponentTypeName'=>$r['ComponentTypeName'],'data'=>$data)));

		$config    = array(
			'formId' 		=> $formId,
			'componentId' 	=> $componentId,
			'data' 			=> $data,
			'preview' 		=> true,
			'value' 		=> array(),
			'invalid' 		=> false,
			'errorClass' 	=> ''
		);

		$type = $r['ComponentTypeName'];
		$classFile = JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/'.strtolower($type).'.php';
		if (file_exists($classFile)) {
			$class = 'RSFormProField'.$type;

			if (!class_exists($class)) {
				require_once $classFile;
			}

			// Create the field
			$field = new $class($config, true);

			// Return the output
			$out .= $field->output;
		}

		if (empty($out)) {
			$out = '<td colspan="2" style="color:#333333"><em>'.JText::_('RSFP_COMP_PREVIEW_NOT_AVAILABLE').'</em></td>';
		}

		//Trigger Event - rsfp_bk_onAfterCreateComponentPreview
		$mainframe->triggerEvent('rsfp_bk_onAfterCreateComponentPreview',array(array('out'=>&$out, 'formId'=>$formId, 'componentId'=>$componentId, 'ComponentTypeName'=>$r['ComponentTypeName'],'data'=>$data)));

		return $out;
	}

	public function getFieldsTotal()
	{
		$formId = JFactory::getApplication()->input->getInt('formId');
		$db 	= &$this->_db;

		$query = $db->getQuery(true)
			->select('COUNT(' . $db->qn('ComponentId') . ')')
			->from($db->qn('#__rsform_components'))
			->where($db->qn('FormId') . ' = ' . $db->q($formId));

		return $this->_db->setQuery($query)->loadResult();
	}

	public function getFieldsPagination()
	{
		$pagination	= new JPagination($this->getFieldsTotal(), 1, 0);
		// hack to show the order up icon for the first item
		$pagination->limitstart = 1;
		return $pagination;
	}

	public function getForm()
	{
		$formId = JFactory::getApplication()->input->getInt('formId');

		if (empty($this->_form))
		{
			$this->_form = JTable::getInstance('RSForm_Forms', 'Table');
			$this->_form->load($formId);

			if (empty($this->_form->Lang))
			{
				$this->_form->Lang = JFactory::getLanguage()->getDefault();
			}

			if ($this->_form->FormLayoutAutogenerate)
				$this->autoGenerateLayout();

			$lang = $this->getLang();
			if ($lang != $this->_form->Lang)
			{
				$translations = RSFormProHelper::getTranslations('forms', $this->_form->FormId, $lang);
				if ($translations)
					foreach ($translations as $field => $value)
					{
						if (isset($this->_form->$field))
							$this->_form->$field = $value;
					}
			}
		}

		return $this->_form;
	}

	public function getFormPost()
	{
		$formId = JFactory::getApplication()->input->getInt('formId');

		$post = JTable::getInstance('RSForm_Posts', 'Table');
		$post->load($formId, false);

		return $post;
	}

	public function autoGenerateLayout()
	{
		$formId = $this->_form->FormId;
		$filter = JFilterInput::getInstance();

		$layout = JPATH_ADMINISTRATOR.'/components/com_rsform/layouts/'.$filter->clean($this->_form->FormLayoutName, 'path').'.php';
		if (!file_exists($layout)) {
			return false;
		}

		// check if the form title should be shown
		$showFormTitle =  $this->_form->ShowFormTitle;
		// set the required field marker
		$requiredMarker = isset($this->_form->Required) ? $this->_form->Required : '(*)';
		// get the form fields
		$fieldsets 		= $this->getFieldNames('fieldsets');
		// get the form options
		$formOptions = RSFormProHelper::getForm($formId);

		// Generate the layout
		ob_start();
		// include the layout selected
		include $layout;
		$out = ob_get_contents();
		ob_end_clean();

		if ($out != $this->_form->FormLayout && $this->_form->FormId) {
			// Clean it
			// Update the layout
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->update($db->qn('#__rsform_forms'))
				->set($db->qn('FormLayout').'='.$db->q($out))
				->where($db->qn('FormId').'='.$db->q($formId));

			$db->setQuery($query)->execute();
		}

		$this->_form->FormLayout = $out;
	}

	public function getProperty($fieldData, $prop, $default=null)
	{
		// Special case, we no longer use == 'YES' or == 'NO'
		if (isset($fieldData[$prop])) {
			if ($fieldData[$prop] === 'YES') {
				return true;
			} else if ($fieldData[$prop] === 'NO') {
				return false;
			} else {
				return $fieldData[$prop];
			}
		}

		if ($default === 'YES') {
			return true;
		} elseif ($default === 'NO') {
			return false;
		} else {
			return $default;
		}
	}

	public function getComponentType($componentId, $formId) {
		$db 	= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select($db->qn('ComponentTypeId'))
			->from($db->qn('#__rsform_components'))
			->where($db->qn('FormId').'='.$db->q($formId))
			->where($db->qn('ComponentId').'='.$db->q($componentId));
		
		$query->setLimit(1);
		
		$db->setQuery($query);

		return $db->loadResult();
	}

	protected function getFieldNames($type = 'all')
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/quickfields.php';
		return RSFormProQuickFields::getFieldNames($type);
	}

	public function getRequiredFields() {
		return $this->getFieldNames('required');
	}

	public function getHiddenFields() {
		return $this->getFieldNames('hidden');
	}

	public function getQuickFields() {
		return $this->getFieldNames('all');
	}

	public function getPageFields() {
		return $this->getFieldNames('pages');
	}

	public function getFormList()
	{
		$return = array();
		$formId = JFactory::getApplication()->input->getInt('formId');
		
		// Workaround to ignore searches
		$filter_search = $this->getState('filter_search');
		$this->setState('filter_search', null);
		
		$query = $this->getListQuery();
		
		// Revert
		$this->setState('filter_search', $filter_search);

		$this->_db->setQuery($query);
		$results = $this->_db->loadObjectList();
		foreach ($results as $result)
		{
			$return[] = JHtml::_('select.option', $result->FormId, $result->FormTitle, 'value', 'text', $result->FormId == $formId);
		}

		return $return;
	}

	public function getAdminEmail()
	{
		return JFactory::getUser()->get('email');
	}

	public function getPredefinedForms()
	{
		$return = array();

		$return[] = JHtml::_('select.option', '', JText::_('RSFP_PREDEFINED_BLANK_FORM'));

		jimport('joomla.filesystem.folder');
		$folders = JFolder::folders(JPATH_ADMINISTRATOR.'/components/com_rsform/assets/forms');
		foreach ($folders as $folder)
			$return[] = JHtml::_('select.option', $folder, $folder);

		return $return;
	}

	public function getEditorText()
	{
		$db =& $this->_db;
		$formId = JFactory::getApplication()->input->getInt('formId');
		$opener = JFactory::getApplication()->input->getCmd('opener');

		$query = $db->getQuery(true)
			->select($db->qn($opener))
			->from($db->qn('#__rsform_forms'))
			->where($db->qn('FormId') . ' = ' . $db->q($formId));

		$value = $this->_db->setQuery($query)->loadResult();

		$translations = RSFormProHelper::getTranslations('forms', $formId, $this->getLang());
		if ($translations && isset($translations[$opener]))
		{
			$value = $translations[$opener];
		}

		return $value;
	}

	public function save()
	{
		$mainframe = JFactory::getApplication();

        $post = RSFormProHelper::getRawPost();
		$post['FormId'] = $post['formId'];
		
		// Normalize separators
		$post['UserEmailReplyTo'] 	= str_replace(';', ',', $post['UserEmailReplyTo']);
		$post['UserEmailTo'] 		= str_replace(';', ',', $post['UserEmailTo']);
		$post['UserEmailCC'] 		= str_replace(';', ',', $post['UserEmailCC']);
		$post['UserEmailBCC'] 		= str_replace(';', ',', $post['UserEmailBCC']);
		$post['AdminEmailReplyTo'] 	= str_replace(';', ',', $post['AdminEmailReplyTo']);
		$post['AdminEmailTo'] 		= str_replace(';', ',', $post['AdminEmailTo']);
		$post['AdminEmailCC'] 		= str_replace(';', ',', $post['AdminEmailCC']);
		$post['AdminEmailBCC'] 		= str_replace(';', ',', $post['AdminEmailBCC']);
        $post['DeletionEmailReplyTo'] 	= str_replace(';', ',', $post['DeletionEmailReplyTo']);
        $post['DeletionEmailTo'] 		= str_replace(';', ',', $post['DeletionEmailTo']);
        $post['DeletionEmailCC'] 		= str_replace(';', ',', $post['DeletionEmailCC']);
        $post['DeletionEmailBCC'] 		= str_replace(';', ',', $post['DeletionEmailBCC']);

		$form = JTable::getInstance('RSForm_Forms', 'Table');
		unset($form->Thankyou);
		unset($form->UserEmailText);
		unset($form->AdminEmailText);
		unset($form->DeletionEmailText);
		unset($form->ErrorMessage);

		if (!isset($post['FormLayoutAutogenerate']))
			$post['FormLayoutAutogenerate'] = 0;

		if (!$form->bind($post))
		{
			JError::raiseWarning(500, $form->getError());
			return false;
		}

		$this->saveFormTranslation($form, $this->getLang());

		if ($form->store())
		{
			// Post to another location
			$formId = $post['formId'];
			$db 	= JFactory::getDbo();

			$row = JTable::getInstance('RSForm_Posts', 'Table');

            $form_post 				= $mainframe->input->get('form_post', array(), 'array');
            $form_post['form_id'] 	= $formId;
			$form_post['fields'] 	= array();
			if (isset($form_post['name'], $form_post['value']) && is_array($form_post['name']) && is_array($form_post['value']))
			{
				for ($i = 0; $i < count($form_post['name']); $i++)
				{
					$form_post['fields'][] = array(
						'name'  => $form_post['name'][$i],
						'value' => $form_post['value'][$i],
					);
				}
			}

			$row->bind($form_post);
			$row->store();

			// Calculations
			if ($calculations = $mainframe->input->get('calculations', array(), 'array'))
			{
				foreach ($calculations as $id => $calculation)
				{
					if (is_array($calculation))
					{
						$object = (object) array(
							'id' => $id
						);

						foreach ($calculation as $key => $value)
						{
							$object->{$key} = $value;
						}

						$db->updateObject('#__rsform_calculations', $object, array('id'));
					}
				}
			}

			// Trigger event
			$mainframe->triggerEvent('rsfp_onFormSave', array(&$form));
			return true;
		}
		else
		{
			JError::raiseWarning(500, $form->getError());
			return false;
		}
	}

	public function saveFormTranslation(&$form, $lang)
	{
		if ($form->Lang == $lang || (RSFormProHelper::getConfig('global.disable_multilanguage') && RSFormProHelper::getConfig('global.default_language') == 'en-GB'))
        {
            return true;
        }

		$fields 	  = array('FormTitle', 'UserEmailFromName', 'UserEmailSubject', 'AdminEmailFromName', 'AdminEmailSubject', 'DeletionEmailFromName', 'DeletionEmailSubject', 'MetaDesc', 'MetaKeywords');
		$translations = RSFormProHelper::getTranslations('forms', $form->FormId, $lang, 'id');
		foreach ($fields as $field)
		{
            $translation = (object) array(
                'form_id'       => $form->FormId,
                'lang_code'     => $lang,
                'reference'     => 'forms',
                'reference_id'  => $field,
                'value'         => $form->{$field}
            );

			if (!isset($translations[$field]))
			{
			    $this->_db->insertObject('#__rsform_translations', $translation);
			}
			else
			{
			    $translation->id = $translations[$field];
			    $this->_db->updateObject('#__rsform_translations', $translation, array('id'));
			}
			unset($form->$field);
		}

		return true;
	}

	public function saveFormRichtextTranslation($formId, $opener, $value, $lang)
	{
		$translations = RSFormProHelper::getTranslations('forms', $formId, $lang, 'id');

        $translation = (object) array(
            'form_id'       => $formId,
            'lang_code'     => $lang,
            'reference'     => 'forms',
            'reference_id'  => $opener,
            'value'         => $value
        );

        if (!isset($translations[$opener]))
        {
            $this->_db->insertObject('#__rsform_translations', $translation);
        }
        else
        {
            $translation->id = $translations[$opener];
            $this->_db->updateObject('#__rsform_translations', $translation, array('id'));
        }
	}

	public function saveFormPropertyTranslation($formId, $componentId, &$params, $lang, $just_added, $properties)
	{
		$fields 	  = RSFormProHelper::getTranslatableProperties();
		$translations = RSFormProHelper::getTranslations('properties', $formId, $lang, 'id');

		foreach ($fields as $field)
		{
			if (!isset($params[$field])) continue;

			$reference_id = $componentId.'.'.$this->_db->escape($field);

			$translation = (object) array(
                'form_id'       => $formId,
                'lang_code'     => $lang,
                'reference'     => 'properties',
                'reference_id'  => $reference_id,
                'value'         => $params[$field]
            );

			if (!isset($translations[$reference_id]))
			{
			    $this->_db->insertObject('#__rsform_translations', $translation);
			}
			else
			{
			    $translation->id = $translations[$reference_id];
                $this->_db->updateObject('#__rsform_translations', $translation, array('id'));
			}

			if (!$just_added && in_array($field, $properties))
			{
                unset($params[$field]);
            }
		}
	}

	public function getLang()
	{
		if (empty($this->_form))
		{
			$this->getForm();
		}

	    if (RSFormProHelper::getConfig('global.disable_multilanguage'))
        {
            return RSFormProHelper::getConfig('global.default_language');
        }
		
		return JFactory::getSession()->get('com_rsform.form.formId'.$this->_form->FormId.'.lang', JFactory::getLanguage()->getTag());
	}

	public function getEmailLang($id = null)
	{
		$session = JFactory::getSession();
		$cid	 = JFactory::getApplication()->input->getInt('cid');
		if (!is_null($id)) $cid = $id;

		// Requesting to edit in a specific language? Update the session.
		if ($lang = JFactory::getApplication()->input->getCmd('ELanguage')) {
			$session->set('com_rsform.emails.emailId'.$cid.'.lang', $lang);
		}

		return $session->get('com_rsform.emails.emailId'.$cid.'.lang', $this->getLang());
	}

	public function getLanguages()
	{
		$languages = JLanguageHelper::getKnownLanguages(JPATH_SITE);

		$return = array();
		foreach ($languages as $tag => $properties)
		{
			$return[] = JHtml::_('select.option', $tag, $properties['name']);
		}

		return $return;
	}

	public function getMappings()
	{
		if (empty($this->_mdata))
		{
			$this->_mdata = $this->_getList($this->_buildMQuery());
		}

		return $this->_mdata;
	}

	public function getMTotal()
	{
		if (empty($this->_mtotal))
		{
			$this->_mtotal = $this->_getListCount($this->_buildMQuery());
		}

		return $this->_mtotal;
	}

	public function getMPagination()
	{
		$pagination	= new JPagination($this->getMTotal(), 1, 0);
		// hack to show the order up icon for the first item
		$pagination->limitstart = 1;
		return $pagination;
	}

	public function getConditions()
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/conditions.php';

		$formId	= JFactory::getApplication()->input->getInt('formId');
		$lang	= $this->getLang();

		return RSFormProConditions::getConditions($formId, $lang, null);
	}

	public function getEmails()
	{
		$formId = JFactory::getApplication()->input->getInt('formId');
		if (!$formId)
		{
			return array();
		}

		$db 	= &$this->_db;
		$query 	= $db->getQuery(true)
			->select($db->qn(array('id', 'to', 'subject', 'formId')))
			->from($db->qn('#__rsform_emails'))
			->where($db->qn('type') . ' = ' . $db->q('additional'))
			->where($db->qn('formId') . ' = ' . $db->q($formId));

		$emails = $this->_getList($query);
		if (!empty($emails))
		{
			$translations = RSFormProHelper::getTranslations('emails', $formId, JFactory::getSession()->get('com_rsform.form.formId'.$formId.'.lang', JFactory::getLanguage()->getDefault()));
			foreach ($emails as $id => $email)
			{
				if (isset($translations[$email->id.'.fromname']))
				{
					$emails[$id]->fromname = $translations[$email->id.'.fromname'];
				}

				if (isset($translations[$email->id.'.subject']))
				{
					$emails[$id]->subject = $translations[$email->id.'.subject'];
				}

				if (isset($translations[$email->id.'.message']))
				{
					$emails[$id]->message = $translations[$email->id.'.message'];
				}
			}
		}

		return $emails;
	}

	public function getEmail()
	{
		$row		= JTable::getInstance('RSForm_Emails', 'Table');
		$cid		= JFactory::getApplication()->input->getInt('cid');
		$formId		= JFactory::getApplication()->input->getInt('formId');

		$row->load($cid);
		if ($formId && !$row->id) $row->formId = $formId;

		$translations = RSFormProHelper::getTranslations('emails', $row->formId, $this->getEmailLang());

		if (isset($translations[$row->id.'.fromname']))
			$row->fromname = $translations[$row->id.'.fromname'];
		if (isset($translations[$row->id.'.subject']))
			$row->subject = $translations[$row->id.'.subject'];
		if (isset($translations[$row->id.'.message']))
			$row->message = $translations[$row->id.'.message'];

		return $row;
	}

	public function saveEmail()
	{
		$row	= JTable::getInstance('RSForm_Emails', 'Table');
        $post 	= RSFormProHelper::getRawPost();
		
		$post['replyto'] 	= str_replace(';', ',', $post['replyto']);
		$post['to'] 		= str_replace(';', ',', $post['to']);
		$post['cc'] 		= str_replace(';', ',', $post['cc']);
		$post['bcc'] 		= str_replace(';', ',', $post['bcc']);
		
		if (!$row->bind($post))
		{
			JError::raiseWarning(500, $row->getError());
			return false;
		}

		// Saving new row twice so we can save translations
		if (!$row->id) {
			if (!$row->store()) {
				JError::raiseWarning(500, $row->getError());
				return false;
			}
		}

		if ($this->saveEmailsTranslation($row, $this->getEmailLang($row->id))) {
			$row->fromname = null;
			$row->subject = null;
			$row->message = null;
		}

		if (!$row->store()) {
			JError::raiseWarning(500, $row->getError());
			return false;
		}
		
		JFactory::getApplication()->enqueueMessage(JText::_('RSFP_CHANGES_SAVED'));

		return $row;
	}

	public function saveEmailsTranslation(&$email, $lang)
	{
		// We're saving a new email so we need to skip translations for now
		// This email is the base for future translations.
		if (!$email->id) {
			return false;
		}

		$fields 	  = array('fromname', 'subject', 'message');
		$translations = RSFormProHelper::getTranslations('emails', $email->formId, $lang, 'id');

		// $translations is false when we're trying to get translations (en-GB) for the same language the form is in (en-GB)
		if ($translations === false) {
			return false;
		}

		foreach ($fields as $field)
		{
			$reference_id = $email->id . '.' . $field;

            $translation = (object) array(
                'form_id'       => $email->formId,
                'lang_code'     => $lang,
                'reference'     => 'emails',
                'reference_id'  => $reference_id,
                'value'         => $email->{$field}
            );

            if (!isset($translations[$reference_id]))
            {
                $this->_db->insertObject('#__rsform_translations', $translation);
            }
            else
            {
                $translation->id = $translations[$reference_id];
                $this->_db->updateObject('#__rsform_translations', $translation, array('id'));
            }
			unset($email->{$field});
		}

		return true;
	}

	public function getSideBar() {
		require_once JPATH_COMPONENT.'/helpers/toolbar.php';

		return RSFormProToolbarHelper::render();
	}

	public function getTotalFields() {
		$options = array();

		if ($fields = $this->getFields()) {
			foreach ($fields as $field) {
				if (in_array($field->type_id, array(1,11)))
					$options[] = JHtml::_('select.option',$field->name,$field->name);
			}
		}

		return $options;
	}

	public function copyComponent($sourceComponentId, $toFormId)
	{
		$sourceComponentId 	= (int) $sourceComponentId;
		$toFormId 			= (int) $toFormId;
		$db 				= JFactory::getDbo();

		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('#__rsform_components'))
			->where($db->qn('ComponentId').'='.$db->q($sourceComponentId));
		if ($component = $db->setQuery($query)->loadObject()) {
			// Get max ordering
			$query->clear()
				->select('MAX('.$db->qn('Order').')')
				->from($db->qn('#__rsform_components'))
				->where($db->qn('FormId').'='.$db->q($toFormId));
			$component->Order = (int) $db->setQuery($query)->loadResult() + 1;

			// Insert the new field
			$query->clear()
				->insert($db->qn('#__rsform_components'))
				->set($db->qn('FormId').'='.$db->q($toFormId))
				->set($db->qn('ComponentTypeId').'='.$db->q($component->ComponentTypeId))
				->set($db->qn('Order').'='.$db->q($component->Order))
				->set($db->qn('Published').'='.$db->q($component->Published));
			$db->setQuery($query)->execute();

			// Get the newly created field ID
			$newComponentId = $db->insertid();

			// Get the properties of the field so we can duplicate them
			$query->clear()
				->select('*')
				->from($db->qn('#__rsform_properties'))
				->where($db->qn('ComponentId').'='.$db->q($sourceComponentId));
			$properties = $db->setQuery($query)->loadObjectList();
			foreach ($properties as $property) {
				// Handle duplicated fields
				if ($property->PropertyName == 'NAME' && $toFormId == $component->FormId) {
					$property->PropertyValue .= ' copy';

					while (RSFormProHelper::componentNameExists($property->PropertyValue, $toFormId)) {
						$property->PropertyValue .= mt_rand(0,9);
					}
				}

				$query->clear()
					->insert('#__rsform_properties')
					->set($db->qn('ComponentId').'='.$db->q($newComponentId))
					->set($db->qn('PropertyName').'='.$db->q($property->PropertyName))
					->set($db->qn('PropertyValue').'='.$db->q($property->PropertyValue));
				$db->setQuery($query)->execute();
			}

			// Copy language
			$query->clear()
				->select('*')
				->from($db->qn('#__rsform_translations'))
				->where($db->qn('reference').'='.$db->q('properties'))
				->where($db->qn('reference_id').' LIKE '.$db->q($sourceComponentId.'.%'));
			$translations = $db->setQuery($query)->loadObjectList();
			foreach ($translations as $translation) {
				list($oldComponentId, $property) = explode('.', $translation->reference_id, 2);
				$reference_id = $newComponentId.'.'.$property;

				$query->clear()
					->insert('#__rsform_translations')
					->set($db->qn('form_id').'='.$db->q($toFormId))
					->set($db->qn('lang_code').'='.$db->q($translation->lang_code))
					->set($db->qn('reference').'='.$db->q('properties'))
					->set($db->qn('reference_id').'='.$db->q($reference_id))
					->set($db->qn('value').'='.$db->q($translation->value));

				$db->setQuery($query)->execute();
			}

			return $newComponentId;
		}

		return false;
	}
}