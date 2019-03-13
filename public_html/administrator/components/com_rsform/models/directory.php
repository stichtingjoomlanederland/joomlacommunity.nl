<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

class RsformModelDirectory extends JModelLegacy
{
	protected $_data = array();
	protected $_total = 0;
	protected $_query = '';
	protected $_pagination = null;
	protected $_db = null;

	public $_directory = null;

	public function __construct() {
		parent::__construct();

		$this->_db 		= JFactory::getDbo();
		$app			= JFactory::getApplication();
		$this->_query 	= $this->_buildQuery();

		// Get pagination request variables
		$limit 		= $app->getUserStateFromRequest('com_rsform.directory.limit', 'limit', JFactory::getConfig()->get('list_limit'), 'int');
		$limitstart = $app->getUserStateFromRequest('com_rsform.directory.limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('com_rsform.directory.limit', $limit);
		$this->setState('com_rsform.directory.limitstart', $limitstart);
	}

	public function _buildQuery() {
		$sortColumn	= $this->getSortColumn();
		$sortOrder	= $this->getSortOrder();
		
		$query = $this->_db->getQuery(true)
			->select($this->_db->qn('FormId'))
			->select($this->_db->qn('FormTitle'))
			->select($this->_db->qn('FormName'))
			->select($this->_db->qn('Lang'))
			->from($this->_db->qn('#__rsform_forms'))
			->order($this->_db->qn($sortColumn) . ' ' . $this->_db->escape($sortOrder));

		return (string) $query;
	}

	public function getForms() {
		if (empty($this->_data)) {
			$this->_db->setQuery($this->_query, $this->getState('com_rsform.directory.limitstart'), $this->getState('com_rsform.directory.limit'));
			$this->_data = $this->_db->loadObjectList();
			
			foreach ($this->_data as $form)
			{
				$lang = RSFormProHelper::getCurrentLanguage($form->FormId);
				if ($lang != $form->Lang)
				{
					if ($translations = RSFormProHelper::getTranslations('forms', $form->FormId, $lang))
					{
						foreach ($translations as $field => $value)
						{
							if (isset($form->$field))
							{
								$form->$field = $value;
							}
						}
					}
				}
			}
		}

		return $this->_data;
	}

	public function getTotal() {
		if (empty($this->_total)) {
			$this->_db->setQuery($this->_query);
			$this->_db->execute();

			$this->_total = $this->_db->getNumRows();
		}

		return $this->_total;
	}

	public function getPagination() {
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('com_rsform.directory.limitstart'), $this->getState('com_rsform.directory.limit'));
		}

		return $this->_pagination;
	}

	public function getSortColumn() {
		return JFactory::getApplication()->getUserStateFromRequest('com_rsform.directory.filter_order', 'filter_order', 'FormId', 'string');
	}

	public function getSortOrder() {
		return JFactory::getApplication()->getUserStateFromRequest('com_rsform.directory.filter_order_Dir', 'filter_order_Dir', 'ASC', 'word');
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

		// Check if the entry exists
		$this->_db->setQuery('SELECT COUNT('.$this->_db->qn('formId').') FROM '.$this->_db->qn('#__rsform_directory').' WHERE '.$this->_db->qn('formId').' = '.(int) $data['formId'].' ');
		if (!$this->_db->loadResult()) {
			$this->_db->setQuery('INSERT INTO '.$this->_db->qn('#__rsform_directory').' SET '.$this->_db->qn('formId').' = '.(int) $data['formId'].' ');
			$this->_db->execute();
		}

		// Bind the data.
		if (!$table->bind($data)) {
			$this->setError($table->getError());
			return false;
		}

		// Store the data.
		if (!$table->store()) {
			$this->setError($table->getError());
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

	public function getEmails() {
		$formId = JFactory::getApplication()->input->getInt('formId',0);
		$session = JFactory::getSession();
		$lang = JFactory::getLanguage();
		if (!$formId) return array();

		$emails = $this->_getList("SELECT `id`, `to`, `subject`, `formId` FROM `#__rsform_emails` WHERE `type` = 'directory' AND `formId` = ".$formId." ");
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

	public function getQuickFields() {
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
			$mainframe = JFactory::getApplication();
			$all = array();
			$query->clear();
			$query->select($this->_db->qn('c.ComponentId'))
				->select($this->_db->qn('c.ComponentTypeId'))
				->select($this->_db->qn('ct.ComponentTypeName'))
				->from($this->_db->qn('#__rsform_components', 'c'))
				->join('LEFT', $this->_db->qn('#__rsform_component_types', 'ct') .'ON ('.$this->_db->qn('ct.ComponentTypeId').'='.$this->_db->qn('c.ComponentTypeId').')')
				->join('LEFT',$this->_db->qn('#__rsform_directory_fields','d').' ON '.$this->_db->qn('d.ComponentId').' = '.$this->_db->qn('c.ComponentId'))
				->where($this->_db->qn('c.FormId').'='.$this->_db->q($formId))
				->where($this->_db->qn('c.Published').'='.$this->_db->q(1))
				->where($this->_db->qn('d.indetails').'='.$this->_db->q(1))
				->order($this->_db->qn('c.Order').' '.$this->_db->escape('asc'));
			$this->_db->setQuery($query);

			if ($components = $this->_db->setQuery($query)->loadObjectList()) {
				$data = RSFormProHelper::getComponentProperties($components);
				$i = 0;
				foreach ($components as $component) {
					if (!empty($data[$component->ComponentId])) {
						$properties =& $data[$component->ComponentId];
						if (isset($properties['NAME'])) {
							// Populate the 'all' array
							$componentPlaceholders = array(
								'name' => $properties['NAME'],
								'id'   => $component->ComponentTypeId,
								'generate' => array(
									'{' . $properties['NAME'] . ':caption}',
									'{' . $properties['NAME'] . ':body}',
									'{' . $properties['NAME'] . ':description}',
									'{' . $properties['NAME'] . ':validation}',
								),
								'display'  => array(
									'{' . $properties['NAME'] . ':caption}',
									'{' . $properties['NAME'] . ':value}',
									'{' . $properties['NAME'] . ':description}'
								),
							);

							if ($component->ComponentTypeId == RSFORM_FIELD_FREETEXT) {
								array_pop($componentPlaceholders['display']);
							}

							if ($component->ComponentTypeId == RSFORM_FIELD_FILEUPLOAD) {
								$componentPlaceholders['display'][] = '{' . $properties['NAME'] . ':path}';
								$componentPlaceholders['display'][] = '{' . $properties['NAME'] . ':localpath}';
								$componentPlaceholders['display'][] = '{' . $properties['NAME'] . ':filename}';
							}

							if ($component->ComponentTypeId == RSFORM_FIELD_SELECTLIST || $component->ComponentTypeId == RSFORM_FIELD_CHECKBOXGROUP || $component->ComponentTypeId == RSFORM_FIELD_RADIOGROUP) {
								$componentPlaceholders['display'][] = '{' . $properties['NAME'] . ':text}';
							}

							if (isset($properties['ITEMS'])) {
								if (strpos($properties['ITEMS'], '[p') !== false) {
									$componentPlaceholders['display'][] = '{' . $properties['NAME'] . ':price}';
								}
							}

							$mainframe->triggerEvent('rsfp_onAfterCreateQuickAddPlaceholders', array(&$componentPlaceholders, $component->ComponentTypeId));

							$all[] = $componentPlaceholders;
						}
					}
				}
				return $all;
			}
		}

		return array();
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
				->where($this->_db->qn('c.ComponentTypeId').' = 9')
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
}