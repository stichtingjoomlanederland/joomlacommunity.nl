<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2019 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

class RsformModelDirectory extends JModelList
{
	public $_directory = null;

	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'FormTitle',
				'FormName',
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
			->select($this->_db->qn('d.formId', 'DirectoryFormId'))
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

		$query->join('left', $this->_db->qn('#__rsform_directory', 'd') . ' ON (' . $this->_db->qn('f.FormId') . ' = ' . $this->_db->qn('d.formId') . ')');

		$query->order($this->_db->qn($this->getSortColumn()) . ' ' . $this->_db->escape($this->getSortOrder()));

		return $query;
	}

	public function getForms()
	{
		return $this->getItems();
	}

	public function getFormTitle()
	{
		$formId = JFactory::getApplication()->input->getInt('formId');

		$query = $this->_db->getQuery(true)
			->select($this->_db->qn('FormTitle'))
			->from($this->_db->qn('#__rsform_forms'))
			->where($this->_db->qn('FormId') . ' = ' . $this->_db->q($formId));
		$title = $this->_db->setQuery($query)->loadResult();

		$lang = RSFormProHelper::getCurrentLanguage($formId);
		if ($translations = RSFormProHelper::getTranslations('forms', $formId, $lang))
		{
			if (isset($translations['FormTitle']))
			{
				$title = $translations['FormTitle'];
			}
		}

		return $title;
	}

	public function getSortColumn()
	{
		return $this->getState('list.ordering', 'FormId');
	}

	public function getSortOrder()
	{
		return $this->getState('list.direction', 'ASC');
	}

	public function getSideBar() {
		require_once JPATH_COMPONENT.'/helpers/toolbar.php';

		return RSFormProToolbarHelper::render();
	}

	public function getDirectory() {
		$formId = JFactory::getApplication()->input->getInt('formId');
		$table 	= JTable::getInstance('RSForm_Directory', 'Table');

		$table->load($formId);

		if (!$table->formId) {
			$table->enablecsv = 0;
			$table->enablepdf = 0;
			$table->HideEmptyValues = 0;
			$table->ShowGoogleMap = 0;
			$table->ViewLayoutAutogenerate = 1;
			$table->ViewLayoutName = 'dir-inline';
		}

		if ($table->groups) {
			$registry = new JRegistry;
			$registry->loadString($table->groups);
			$table->groups = $registry->toArray();
		} else {
			$table->groups = array();
		}

        if ($table->DeletionGroups) {
            $registry = new JRegistry;
            $registry->loadString($table->DeletionGroups);
            $table->DeletionGroups = $registry->toArray();
        } else {
            $table->DeletionGroups = array();
        }

		$this->_directory = $table;

		if ($this->_directory->ViewLayoutAutogenerate) {
			$this->autoGenerateLayout();
		}

		return $table;
	}

	public function save($data) {
		$table	= JTable::getInstance('RSForm_Directory', 'Table');
		$input	= JFactory::getApplication()->input;
		$db		= JFactory::getDbo();

		if (isset($data['groups']) && is_array($data['groups']))
		{
			$registry = new JRegistry;
			$registry->loadArray($data['groups']);
			$data['groups'] = $registry->toString();
		}
		else
        {
            $data['groups'] = '';
        }

        if (isset($data['DeletionGroups']) && is_array($data['DeletionGroups']))
        {
            $registry = new JRegistry;
            $registry->loadArray($data['DeletionGroups']);
            $data['DeletionGroups'] = $registry->toString();
        }
        else
        {
            $data['DeletionGroups'] = '';
        }

		if (!$table->save($data))
		{
			return false;
		}

		// Store directory fields
		$fields				= RSFormProHelper::getAllDirectoryFields($table->formId);
		$listingFields   	= $input->get('dirviewable',array(),'array');
		$searchableFields 	= $input->get('dirsearchable',array(),'array');
		$editableFields	  	= $input->get('direditable',array(),'array');
		$detailsFields	  	= $input->get('dirindetails',array(),'array');
		$csvFields		  	= $input->get('dirincsv',array(),'array');
		$cids	  		  	= $input->get('dircid',array(),'array');
		$orderingFields	  	= $input->get('dirorder',array(),'array');

		// empty
        $query = $db->getQuery(true)
            ->delete($db->qn('#__rsform_directory_fields'))
            ->where($db->qn('formId') . ' = ' . $db->q($table->formId));

		$db->setQuery($query);
		$db->execute();

		foreach ($fields as $field) {
			$object = (object) array(
			    'formId'        => $table->formId,
			    'componentId'   => $field->FieldId,
                'viewable'      => (int) in_array($field->FieldId, $listingFields),
                'searchable'    => (int) in_array($field->FieldId, $searchableFields),
                'editable'      => (int) in_array($field->FieldId, $editableFields),
                'indetails'     => (int) in_array($field->FieldId, $detailsFields),
                'incsv'         => (int) in_array($field->FieldId, $csvFields),
                'ordering'      => $orderingFields[array_search($field->FieldId, $cids)]
            );

			$db->insertObject('#__rsform_directory_fields', $object);
		}

		return true;
	}

	public function getEmails()
	{
		$formId 	= JFactory::getApplication()->input->getInt('formId');
		$db			= JFactory::getDbo();
		$session 	= JFactory::getSession();
		$lang 		= JFactory::getLanguage();
		if (!$formId)
		{
			return array();
		}

		$query = $db->getQuery(true)
			->select($db->qn(array('id', 'to', 'subject', 'formId')))
			->from($db->qn('#__rsform_emails'))
			->where($db->qn('type') . ' = ' . $db->q('directory'))
			->where($db->qn('formId') . ' = ' . $db->q($formId));

		$emails = $db->setQuery($query)->loadObjectList();
		if (!empty($emails))
		{
			$translations = RSFormProHelper::getTranslations('emails', $formId, $session->get('com_rsform.form.formId'.$formId.'.lang', $lang->getDefault()));
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

	public function autoGenerateLayout() {
		$formId = $this->_directory->formId;
		$filter = JFilterInput::getInstance();

		$layout = JPATH_ADMINISTRATOR.'/components/com_rsform/layouts/'.$filter->clean($this->_directory->ViewLayoutName, 'path').'.php';
		if (!file_exists($layout))
			return false;

		$headers	  = RSFormProHelper::getDirectoryStaticHeaders();
		$fields 	  = RSFormProHelper::getDirectoryFields($formId);
		$quickfields  = $this->getQuickFields();
		$imagefields  = $this->getImagesFields();

		$hideEmptyValues = $this->_directory->HideEmptyValues;
		$showGoogleMap = $this->_directory->ShowGoogleMap;

		$out = include $layout;

		if ($out != $this->_directory->ViewLayout && $this->_directory->formId) {
			// Clean it
			// Update the layout
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->update($db->qn('#__rsform_directory'))
				->set($db->qn('ViewLayout').'='.$db->q($out))
				->where($db->qn('formId').'='.$db->q($this->_directory->formId));

			$db->setQuery($query);
			$db->execute();
		}

		$this->_directory->ViewLayout = $out;
	}

	protected function getStaticPlaceholder($header) {
		if ($header == 'DateSubmitted') {
			return '{global:date_added}';
		} else {
			return '{global:'.strtolower($header).'}';
		}
	}

	public function getQuickFields()
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/quickfields.php';
		return RSFormProQuickFields::getFieldNames('all');
	}

	public function getImagesFields() {
		$cids	= array();
		$query	= $this->_db->getQuery(true);
		$formId = JFactory::getApplication()->input->getInt('formId');
		$fields = RSFormProHelper::getDirectoryFields($formId);

		if (!empty($fields)) {
			foreach ($fields as $field) {
				if ($field->indetails)
					$cids[] = $field->componentId;
			}
		}
		$cids = array_map('intval', $cids);

		if (!empty($cids)) {
			$query->clear()
				->select($this->_db->qn('p.PropertyValue'))
				->from($this->_db->qn('#__rsform_properties','p'))
				->join('LEFT',$this->_db->qn('#__rsform_components','c').' ON '.$this->_db->qn('p.ComponentId').' = '.$this->_db->qn('c.ComponentId'))
				->join('LEFT',$this->_db->qn('#__rsform_directory_fields','d').' ON '.$this->_db->qn('d.ComponentId').' = '.$this->_db->qn('c.ComponentId'))
				->where($this->_db->qn('c.FormId').' = '.(int) $formId)
				->where($this->_db->qn('p.PropertyName').' = '.$this->_db->q('NAME'))
				->where($this->_db->qn('c.ComponentId').' IN ('.implode(',',$cids).')')
				->where($this->_db->qn('c.ComponentTypeId').' = ' . $this->_db->q(RSFORM_FIELD_FILEUPLOAD))
				->where($this->_db->qn('c.Published').' = 1')
				->order($this->_db->qn('d.ordering'));

			$this->_db->setQuery($query);

			return $this->_db->loadColumn();
		}

		return array();
	}

	public function remove($pks) {
		if ($pks) {
			$pks = array_map('intval', $pks);

			$query = $this->_db->getQuery(true)
                ->delete('#__rsform_directory')
                ->where($this->_db->qn('formId') . ' IN (' . implode(',', $this->_db->q($pks)) . ')');
			$this->_db->setQuery($query);
			$this->_db->execute();

            $query = $this->_db->getQuery(true)
                ->delete('#__rsform_directory_fields')
                ->where($this->_db->qn('formId') . ' IN (' . implode(',', $this->_db->q($pks)) . ')');
            $this->_db->setQuery($query);
			$this->_db->execute();

            $query = $this->_db->getQuery(true)
                ->delete('#__rsform_emails')
                ->where($this->_db->qn('formId') . ' IN (' . implode(',', $this->_db->q($pks)) . ')')
                ->where($this->_db->qn('type') . ' = ' . $this->_db->q('directory'));
            $this->_db->setQuery($query);
            $this->_db->execute();
		}

		return true;
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
}