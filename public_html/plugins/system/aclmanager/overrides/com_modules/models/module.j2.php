<?php
/**
 * @package		ACL Manager for Joomla
 * @copyright 	Copyright (c) 2011-2016 Sander Potjer
 * @license 	GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Extend Joomla core class
 */
class ModulesModelModule extends ModulesModelModuleCore
{
	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		// Check for existing module.
		if (!empty($record->id))
		{
			return $user->authorise('core.edit.state', 'com_modules.module.' . (int) $record->id);
		}
		// Default to component settings if module not known.
		else
		{
			return parent::canEditState('com_modules');
		}
	}

	public function delete(&$pks)
	{
		// Initialise variables.
		$pks	= (array) $pks;
		$user	= JFactory::getUser();
		$table	= $this->getTable();

		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk)
		{
			if ($table->load($pk))
			{
				// Access checks.
				if (!$user->authorise('core.delete', 'com_modules.module.'.(int) $pk) || $table->published != -2)
				{
					JError::raiseWarning(403, JText::_('JERROR_CORE_DELETE_NOT_PERMITTED'));
					//	throw new Exception(JText::_('JERROR_CORE_DELETE_NOT_PERMITTED'));
					return;
				}

				if (!$table->delete($pk))
				{
					throw new Exception($table->getError());
				}
				else
				{
					// Delete the menu assignments
					$db		= $this->getDbo();
					$query	= $db->getQuery(true);
					$query->delete();
					$query->from('#__modules_menu');
					$query->where('moduleid='.(int)$pk);
					$db->setQuery((string)$query);
					$db->query();
				}

				$query	= $db->getQuery(true);
				$query->delete('#__assets');
				$query->where('name = ' . $db->quote('com_modules.module.'.$pk));
				$db->setQuery($query);
				if (!$db->query())
				{
					$this->setError($db->getErrorMsg());
					return false;
				}

				// Clear module cache
				parent::cleanCache($table->module, $table->client_id);
			}
			else
			{
				throw new Exception($table->getError());
			}
		}

		require_once JPATH_ADMINISTRATOR.'/components/com_aclmanager/models/diagnostic.php';
		$aclmanager = JModelLegacy::getInstance('diagnostic', 'AclmanagerModel');
		$aclmanager->rebuild();

		// Clear modules cache
		$this->cleanCache();

		return true;
	}

	public function getForm($data = array(), $loadData = true)
	{
		// The folder and element vars are passed when saving the form.
		if (empty($data))
		{
			$item		= $this->getItem();
			$clientId	= $item->client_id;
			$module		= $item->module;
			$id      	= $item->id;
		}
		else
		{
			$clientId	= JArrayHelper::getValue($data, 'client_id');
			$module		= JArrayHelper::getValue($data, 'module');
			$id      	= JArrayHelper::getValue($data, 'id');
		}

		// These variables are used to add data from the plugin XML files.
		$this->setState('item.client_id',	$clientId);
		$this->setState('item.module',		$module);

		// Get the form.
		$form = $this->loadForm('com_modules.module', 'module', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}

		$form->setFieldAttribute('position', 'client', $this->getState('item.client_id') == 0 ? 'site' : 'administrator');

		$user = JFactory::getUser();

		// Check for existing module
		// Modify the form based on access controls.
		if ($id != 0 && (!$user->authorise('core.edit.state', 'com_modules.module.'.(int) $id))
			|| ($id == 0 && !$user->authorise('core.edit.state', 'com_modules'))
		)
		{
			// Disable fields for display.
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('published', 'disabled', 'true');
			$form->setFieldAttribute('publish_up', 'disabled', 'true');
			$form->setFieldAttribute('publish_down', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('published', 'filter', 'unset');
			$form->setFieldAttribute('publish_up', 'filter', 'unset');
			$form->setFieldAttribute('publish_down', 'filter', 'unset');
		}

		return $form;
	}

	public function save($data)
	{
		// Initialise variables;
		$dispatcher = JDispatcher::getInstance();
		$table		= $this->getTable();
		$pk			= (!empty($data['id'])) ? $data['id'] : (int) $this->getState('module.id');
		$isNew		= true;

		// Include the content modules for the onSave events.
		JPluginHelper::importPlugin('extension');

		// Load the row if saving an existing record.
		if ($pk > 0)
		{
			$table->load($pk);
			$isNew = false;
		}

		// Alter the title and published state for Save as Copy
		if (JRequest::getVar('task') == 'save2copy')
		{
			$orig_data	= JRequest::getVar('jform', array(), 'post', 'array');
			$orig_table = clone($this->getTable());
			$orig_table->load((int) $orig_data['id']);

			if ($data['title'] == $orig_table->title)
			{
				$data['title'] .= ' '.JText::_('JGLOBAL_COPY');
				$data['published'] = 0;
			}
		}

		// Bind the data.
		if (!$table->bind($data))
		{
			$this->setError($table->getError());
			return false;
		}

		// Prepare the row for saving
		$this->prepareTable($table);

		// Check the data.
		if (!$table->check())
		{
			$this->setError($table->getError());
			return false;
		}

		// Trigger the onExtensionBeforeSave event.
		$result = $dispatcher->trigger('onExtensionBeforeSave', array('com_modules.module', &$table, $isNew));
		if (in_array(false, $result, true))
		{
			$this->setError($table->getError());
			return false;
		}

		// Store the data.
		if (!$table->store())
		{
			$this->setError($table->getError());
			return false;
		}

		//
		// Process the menu link mappings.
		//

		$assignment = isset($data['assignment']) ? $data['assignment'] : 0;

		// Delete old module to menu item associations
		// $db->setQuery(
		//	'DELETE FROM #__modules_menu'.
		//	' WHERE moduleid = '.(int) $table->id
		// );

		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$query->delete();
		$query->from('#__modules_menu');
		$query->where('moduleid = '.(int)$table->id);
		$db->setQuery((string)$query);
		$db->query();

		if (!$db->query())
		{
			$this->setError($db->getErrorMsg());
			return false;
		}

		// If the assignment is numeric, then something is selected (otherwise it's none).
		if (is_numeric($assignment))
		{
			// Variable is numeric, but could be a string.
			$assignment = (int) $assignment;

			// Logic check: if no module excluded then convert to display on all.
			if ($assignment == -1 && empty($data['assigned']))
			{
				$assignment = 0;
			}

			// Check needed to stop a module being assigned to `All`
			// and other menu items resulting in a module being displayed twice.
			if ($assignment === 0)
			{
				// assign new module to `all` menu item associations
				// $this->_db->setQuery(
				//	'INSERT INTO #__modules_menu'.
				//	' SET moduleid = '.(int) $table->id.', menuid = 0'
				// );

				$query->clear();
				$query->insert('#__modules_menu');
				$query->columns(array($db->quoteName('moduleid'), $db->quoteName('menuid')));
				$query->values((int)$table->id . ', 0');
				$db->setQuery((string)$query);
				if (!$db->query())
				{
					$this->setError($db->getErrorMsg());
					return false;
				}
			}
			elseif (!empty($data['assigned']))
			{
				// Get the sign of the number.
				$sign = $assignment < 0 ? -1 : +1;

				// Preprocess the assigned array.
				$tuples = array();
				foreach ($data['assigned'] as &$pk)
				{
					$tuples[] = '('.(int) $table->id.','.(int) $pk * $sign.')';
				}

				$this->_db->setQuery(
					'INSERT INTO #__modules_menu (moduleid, menuid) VALUES '.
					implode(',', $tuples)
				);

				if (!$db->query())
				{
					$this->setError($db->getErrorMsg());
					return false;
				}
			}
		}

		// Update/create asset reference
		if($isNew) {
			// Get com_modules asset_id
			$query	= $db->getQuery(true);
			$query->select('id');
			$query->from('#__assets');
			$query->where('name = ' . $db->quote('com_modules'));
			$db->setQuery($query);
			$parent_asset_id = $db->loadResult();

			// Insert new asset for module
			$query	= $db->getQuery(true);
			$query->insert('#__assets');
			$query->set('parent_id = ' . (int) ($parent_asset_id));
			$query->set('level = 2');
			$query->set('name = ' . $db->quote('com_modules.module.'.$table->id));
			$query->set('title = ' . $db->quote($data['title']));
			$query->set('rules = ' . $db->quote('{"core.delete":[],"core.edit":[],"core.edit.state":[]}'));
			$db->setQuery($query);
			if (!$db->query())
			{
				$this->setError($db->getErrorMsg());
				return false;
			}

			// Rebuild asset tree
			require_once JPATH_ADMINISTRATOR.'/components/com_aclmanager/models/diagnostic.php';
			$aclmanager = JModelLegacy::getInstance('diagnostic', 'AclmanagerModel');
			$aclmanager->rebuild();
		} else {
			// Update title of asset
			$query	= $db->getQuery(true);
			$query->update('#__assets');
			$query->where('name = ' . $db->quote('com_modules.module.'.$data['id']));
			$query->set('title = ' . $db->quote($data['title']));
			$db->setQuery($query);
			if (!$db->query())
			{
				$this->setError($db->getErrorMsg());
				return false;
			}
		}

		// Trigger the onExtensionAfterSave event.
		$dispatcher->trigger('onExtensionAfterSave', array('com_modules.module', &$table, $isNew));

		// Compute the extension id of this module in case the controller wants it.
		$query	= $db->getQuery(true);
		$query->select('extension_id');
		$query->from('#__extensions AS e');
		$query->leftJoin('#__modules AS m ON e.element = m.module');
		$query->where('m.id = '.(int) $table->id);
		$db->setQuery($query);

		$extensionId = $db->loadResult();

		if ($error = $db->getErrorMsg())
		{
			JError::raiseWarning(500, $error);
			return;
		}

		$this->setState('module.extension_id',	$extensionId);
		$this->setState('module.id',			$table->id);

		// Clear modules cache
		$this->cleanCache();

		// Clean module cache
		parent::cleanCache($table->module, $table->client_id);

		return true;
	}
}
