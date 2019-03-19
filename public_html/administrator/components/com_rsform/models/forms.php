<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

class RsformModelForms extends JModelLegacy
{
	public $_data = null;
	public $_mdata = null;
	public $_conditionsdata = null;
	public $_total = 0;
	public $_mtotal = 0;
	public $_query = '';
	public $_mquery = '';
	public $_pagination = null;
	public $_db = null;
	public $_form = null;

	public function __construct()
	{
		parent::__construct();
		$this->_db = JFactory::getDbo();
		$mainframe = JFactory::getApplication();

		// set the search filter first
		$filter_search = $mainframe->getUserStateFromRequest('com_rsform.forms.filter_search', 'filter_search', '', 'string');
		$this->setState('com_rsform.forms.filter_search', 	$filter_search);

		// set the query
		$this->_query = $this->_buildQuery();

		if ($mainframe->input->getCmd('layout', 'default') != 'default')
		{
			$this->_mquery = $this->_buildMQuery();
			$this->_conditionsquery = $this->_buildConditionsQuery();
		}

		// Get pagination request variables
		$limit 		= $mainframe->getUserStateFromRequest('com_rsform.forms.limit', 'limit', JFactory::getConfig()->get('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest('com_rsform.forms.limitstart', 'limitstart', 0, 'int');


		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('com_rsform.forms.limit', 		$limit);
		$this->setState('com_rsform.forms.limitstart', 	$limitstart);
	}

	public function _buildQuery()
	{
		$filter_search = $this->getState('com_rsform.forms.filter_search');
		$lang		   = JFactory::getLanguage();
		$or 	= array();
		$ids 	= array();
		
		// Flag to know if we need translations - no point in doing a join if we're only using the default language.
        if (RSFormProHelper::getConfig('global.disable_multilanguage'))
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
                        $or[] 	= "(t.lang_code = ".$this->_db->q($data->lang)." AND t.form_id = ".$this->_db->q($id).")";
                    }
                }

                // Now that we've joined the session forms, we must remove them so they do not show up as duplicates.
                if ($ids)
                {
                    $or[] = "(t.lang_code = ".$this->_db->q($lang->getTag())." AND t.form_id NOT IN (".implode(",", $ids)."))";
                }
            }

            $needs_translation = $lang->getTag() != $lang->getDefault() || $ids;
        }

		$query =
			"SELECT".
			($needs_translation ? " IFNULL(t.value, FormTitle) AS FormTitle," : " f.FormTitle,").
			" f.FormId,".
			" f.FormName,".
			" f.Backendmenu,".
			" f.Published".
			" FROM #__rsform_forms f";
		
		if ($needs_translation)
		{
			$query .=
				" LEFT JOIN #__rsform_translations t ON".
				" (".
				"	f.FormId = t.form_id".
				"	AND t.reference = 'forms'".
				"	AND t.reference_id = 'FormTitle'";

			if ($or)
			{
				$query .= " AND (".implode(" OR ", $or).")";
			}
			else
			{
				$query .= " AND t.lang_code = ".$this->_db->q($lang->getTag());
			}

			$query .= " )";
		}

		if (!empty($filter_search))
		{
			$query .= " HAVING (`FormTitle` LIKE '%".$this->_db->escape($filter_search)."%' OR `FormName` LIKE '%".$this->_db->escape($filter_search)."%')";
		}

		$query .= " ORDER BY `".$this->getSortColumn()."` ".$this->getSortOrder();
		
		return $query;
	}

	public function _buildMQuery()
	{
		$formId	= JFactory::getApplication()->input->getInt('formId');
		$query  = "SELECT * FROM `#__rsform_mappings` WHERE `formId` = ".$formId." ORDER BY `ordering` ASC";

		return $query;
	}

	public function _buildConditionsQuery()
	{
		$formId	= JFactory::getApplication()->input->getInt('formId');
		$lang	= $this->getLang();
		$query  = "SELECT c.*,p.PropertyValue AS ComponentName FROM `#__rsform_conditions` c LEFT JOIN #__rsform_properties p ON (c.component_id = p.ComponentId) WHERE c.`form_id` = ".$formId." AND c.lang_code='".$this->_db->escape($lang)."' AND p.PropertyName='NAME' ORDER BY c.`id` ASC";

		return $query;
	}

	public function getForms()
	{
		if (empty($this->_data))
		{
			$this->_data = $this->_getList($this->_query, $this->getState('com_rsform.forms.limitstart'), $this->getState('com_rsform.forms.limit'));

            $formIds = array();
            foreach ($this->_data as $row)
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

                foreach ($this->_data as $form)
                {
                    $form->_todaySubmissions = isset($todaySubmissions[$form->FormId]) ? $todaySubmissions[$form->FormId]->total : 0;
                    $form->_monthSubmissions = isset($monthSubmissions[$form->FormId]) ? $monthSubmissions[$form->FormId]->total : 0;
                    $form->_allSubmissions = isset($allSubmissions[$form->FormId]) ? $allSubmissions[$form->FormId]->total : 0;
                }
            }
		}

		return $this->_data;
	}

	public function getTotal()
	{
		if (empty($this->_total))
			$this->_total = $this->_getListCount($this->_query);

		return $this->_total;
	}

	public function getPagination()
	{
		if (empty($this->_pagination))
		{
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('com_rsform.forms.limitstart'), $this->getState('com_rsform.forms.limit'));
		}

		return $this->_pagination;
	}

	public function getFilterBar()
	{
		require_once JPATH_COMPONENT.'/helpers/adapters/filterbar.php';
		// Search filter
		$options['search'] = array(
			'label' => JText::_('JSEARCH_FILTER'),
			'value' => $this->getState('com_rsform.forms.filter_search')
		);
		$options['reset_button'] = true;


		$options['limitBox'] = $this->getPagination()->getLimitBox();
		$options['orderDir'] = false;

		$bar = new RSFilterBar($options);

		return $bar;
	}

	public function getSortColumn()
	{
		return JFactory::getApplication()->getUserStateFromRequest('com_rsform.forms.filter_order', 'filter_order', 'FormId', 'word');
	}

	public function getSortOrder()
	{
		return JFactory::getApplication()->getUserStateFromRequest('com_rsform.forms.filter_order_Dir', 'filter_order_Dir', 'ASC', 'word');
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

			$field->required = '-';
			if (!empty($data['REQUIRED'])) {
				$field->required = $data['REQUIRED'] == 'YES';
			}

			$field->validation = '-';
			if (isset($data['VALIDATIONRULE']) && $data['VALIDATIONRULE'] != 'none') {
				$field->validation = '<b>'.$data['VALIDATIONRULE'].'</b>';
			}
			if (isset($data['VALIDATIONRULE_DATE']) && $data['VALIDATIONRULE_DATE'] != 'none') {
				$field->validation = '<b>'.$data['VALIDATIONRULE_DATE'].'</b>';
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

		$this->_db->setQuery("SELECT COUNT(ComponentId) FROM #__rsform_components WHERE FormId='".$formId."'");
		return $this->_db->loadResult();
	}

	public function getFieldsPagination()
	{
		jimport('joomla.html.pagination');

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

		if (!empty($post->fields)) {
			$post->fields = json_decode($post->fields);

			if (!is_array($post->fields)) {
				$post->fields = array();
			}
		}

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
		$filter_search = $this->getState('com_rsform.forms.filter_search');
		$this->setState('com_rsform.forms.filter_search', null);
		
		$query = $this->_buildQuery();
		
		// Revert
		$this->setState('com_rsform.forms.filter_search', $filter_search);

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
		$formId = JFactory::getApplication()->input->getInt('formId');
		$opener = JFactory::getApplication()->input->getCmd('opener');

		$this->_db->setQuery("SELECT `".$opener."` FROM #__rsform_forms WHERE FormId='".$formId."'");
		$value = $this->_db->loadResult();

		$lang = $this->getLang();
		$translations = RSFormProHelper::getTranslations('forms', $formId, $lang);
		if ($translations && isset($translations[$opener]))
			$value = $translations[$opener];

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

			$db->setQuery("SELECT form_id FROM #__rsform_posts WHERE form_id='".(int) $formId."'");
			if (!$db->loadResult())
			{
				$db->setQuery("INSERT INTO #__rsform_posts SET form_id='".(int) $formId."'");
				$db->execute();
			}
			$row = JTable::getInstance('RSForm_Posts', 'Table');
			$row->form_id = $formId;

            $form_post = $mainframe->input->get('form_post', array(), 'array');
			$form_post['fields'] = array();
			if (isset($form_post['name'], $form_post['value']) && is_array($form_post['name']) && is_array($form_post['value'])) {
				for ($i = 0; $i < count($form_post['name']); $i++) {
					$form_post['fields'][] = array(
						'name'  => $form_post['name'][$i],
						'value' => $form_post['value'][$i],
					);
				}
			}
			$form_post['fields'] = json_encode($form_post['fields']);

			$row->bind($form_post);
			$row->store();

			// Calculations
			if ($calculations = $mainframe->input->get('calculations', array(), 'array')) {
				foreach ($calculations as $id => $calculation) {
					$string = array();
					foreach ($calculation as $key => $value) {
						$string[] = $db->qn($key).' = '.$db->q($value);
					}

					if ($string) {
						$db->setQuery("UPDATE #__rsform_calculations SET ".implode(', ',$string)." WHERE id = ".$id);
						$db->execute();
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
		if ($form->Lang == $lang || RSFormProHelper::getConfig('global.disable_multilanguage'))
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

			$reference_id = $componentId.".".$this->_db->escape($field);

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
        $lang = JFactory::getLanguage();
	    if (RSFormProHelper::getConfig('global.disable_multilanguage'))
        {
            return $lang->getDefault();
        }

		if (empty($this->_form))
		{
            $this->getForm();
        }
		
		return JFactory::getSession()->get('com_rsform.form.formId'.$this->_form->FormId.'.lang', $lang->getTag());
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
		$lang 	   = JFactory::getLanguage();
		$languages = $lang->getKnownLanguages(JPATH_SITE);

		$return = array();
		foreach ($languages as $tag => $properties)
			$return[] = JHtml::_('select.option', $tag, $properties['name']);

		return $return;
	}

	public function getMappings()
	{
		if (empty($this->_mdata))
			$this->_mdata = $this->_getList($this->_mquery);

		return $this->_mdata;
	}

	public function getMTotal()
	{
		if (empty($this->_mtotal))
			$this->_mtotal = $this->_getListCount($this->_mquery);

		return $this->_mtotal;
	}

	public function getMPagination()
	{
		jimport('joomla.html.pagination');

		$pagination	= new JPagination($this->getMTotal(), 1, 0);
		// hack to show the order up icon for the first item
		$pagination->limitstart = 1;
		return $pagination;
	}

	public function getConditions()
	{
		if (empty($this->_conditionsdata))
			$this->_conditionsdata = $this->_getList($this->_conditionsquery);

		return $this->_conditionsdata;
	}

	public function getEmails()
	{
		$formId = JFactory::getApplication()->input->getInt('formId',0);
		$session = JFactory::getSession();
		$lang = JFactory::getLanguage();
		if (!$formId) return array();

		$emails = $this->_getList("SELECT `id`, `to`, `subject`, `formId` FROM `#__rsform_emails` WHERE `type` = 'additional' AND `formId` = ".$formId." ");
		if (!empty($emails))
		{
			$translations = RSFormProHelper::getTranslations('emails', $formId, $session->get('com_rsform.form.formId'.$formId.'.lang', $lang->getDefault()));
			foreach ($emails as $id => $email) {
				if (isset($translations[$email->id.'.fromname'])) {
					$emails[$id]->fromname = $translations[$email->id.'.fromname'];
				}
				if (isset($translations[$email->id.'.subject'])) {
					$emails[$id]->subject = $translations[$email->id.'.subject'];
				}
				if (isset($translations[$email->id.'.message'])) {
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