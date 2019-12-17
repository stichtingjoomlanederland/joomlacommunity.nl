<?php
/**
 * @package    Pwtsitemap
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Table\Table;
use Joomla\Utilities\ArrayHelper;

/**
 * Menu Item Model for Menus.
 *
 * @since  1.3.0
 */
class PwtSitemapModelMenu extends FormModel
{
	
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.3.0
	 */
	protected $text_prefix = 'COM_PWTSITEMAP_MENU';

	/**
	 * Model context string.
	 *
	 * @var  string
	 * @since  1.3.0
	 */
	protected $_context = 'com_pwtsitemap.menu';
	

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 *
	 * @since   1.3.0
	 */
	protected function canDelete($record)
	{
		$user = Factory::getUser();

		return $user->authorise('core.delete', 'com_menus.menu.' . (int) $record->id);
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  Table  A database object
	 *
	 * @since   1.3.0
	 */
	public function getTable($type = 'Menu', $prefix = 'PwtSitemapTable', $config = [])
	{
		Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_pwtsitemap/tables');

		return Table::getInstance($type, $prefix, $config);
	}

	/**
	 * Auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 *
	 * @since   1.3.0
	 *
	 * @throws  Exception
	 */
	protected function populateState()
	{
		$app = Factory::getApplication('administrator');

		// Load the User state.
		$id = $app->input->getInt('id');
		$this->setState('menu.id', $id);

		// Load the parameters.
		$params = ComponentHelper::getParams('com_menus');
		$this->setState('params', $params);
	}

	/**
	 * Method to get a menu item.
	 *
	 * @param   integer  $itemId  The id of the menu item to get.
	 *
	 * @return  mixed  Menu item data object on success, false on failure.
	 *
	 * @since   1.3.0
	 */
	public function &getItem($itemId = null)
	{
		$itemId = !empty($itemId) ? $itemId : (int) $this->getState('menu.id');

		$db = $this->getDbo();

		$query = $db->getQuery(true)
			->select(
				$db->quoteName(
					[
						'menuTypes.id',
						'menuTypes.menutype',
						'menuTypes.title',
						'menuTypes.description',
						'menuTypes.client_id',
						'menu.custom_title',
						'menu.ordering'
					]
				)
			)
			->from($db->quoteName('#__menu_types', 'menuTypes'))
			->leftJoin(
				$db->quoteName('#__pwtsitemap_menu_types', 'menu')
				. ' ON ' . $db->quoteName('menuTypes.id') . ' = ' . $db->quoteName('menu.menu_types_id')
			)
			->where($db->quoteName('menuTypes.id') . ' = ' . $db->quote($itemId));

		$properties = $db->setQuery($query)->loadAssoc();

		// Check for a table object error.
		if ($properties === null)
		{
			return false;
		}

		$value = ArrayHelper::toObject($properties, CMSObject::class);

		return $value;
	}

	/**
	 * Method to get the menu item form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  Form|boolean A JForm object on success, false on failure
	 *
	 * @since   1.3.0
	 *
	 * @throws  Exception
	 */
	public function getForm($data = [], $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_pwtsitemap.menu', 'menu', ['control' => 'jform', 'load_data' => $loadData]);

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.3.0
	 *
	 * @throws  Exception
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_menus.edit.menu.data', []);

		if (empty($data))
		{
			$data = $this->getItem();
		}
		else
		{
			unset($data['preset']);
		}

		$this->preprocessData('com_menus.menu', $data);

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.3.0
	 */
	public function save($data)
	{
		$dispatcher = JEventDispatcher::getInstance();
		$id         = !empty($data['id']) ? $data['id'] : (int) $this->getState('menu.id');
		$isNew      = true;

		// Get a row instance.
		$table = $this->getTable();

		// Load the row if saving an existing item.
		if ($id > 0 && $table->load($id))
		{
			$isNew = false;
		}

		/**
		 * Cannot use JTable bind/store to store this data because it has a conflict with CONSTRAINT
		 * declared in #__pwtsitemap_menu_types
		 * Error: Cannot add or update a child row: a foreign key constraint fails
		 */
		$insertData = (object) [
			'menu_types_id' => $data['id'],
			'custom_title'  => $data['custom_title'],
			'ordering'      => $data['ordering'] ?: 1
		];

		// Declare fields to save
		$insertFields = ['menu_types_id', 'custom_title', 'ordering'];

		// Trigger the before event.
		$dispatcher->trigger('onContentBeforeSave', [$this->_context, &$table, $isNew]);

		// If a primary key exists update the object, otherwise insert it.
		if ($isNew === false)
		{
			$this->_db->updateObject($table->getTableName(), $insertData, 'menu_types_id');
		}
		else
		{
			$this->_db->insertObject($table->getTableName(), $insertData, $insertFields);
		}

		// Trigger the after save event.
		$dispatcher->trigger('onContentAfterSave', [$this->_context, &$table, $isNew]);

		// Store the saved id to state
		$this->setState('menu.id', $table->id);

		// Clean the cache
		$this->cleanCache();

		return true;
	}

	

	/**
	 * Custom clean the cache
	 *
	 * @param   string   $group      Cache group name.
	 * @param   integer  $client_id  Application client id.
	 *
	 * @return  void
	 *
	 * @since   1.3.0
	 */
	protected function cleanCache($group = null, $client_id = 0)
	{
		parent::cleanCache('com_menus', 0);
		parent::cleanCache('com_modules');
		parent::cleanCache('mod_menu', 0);
		parent::cleanCache('mod_menu', 1);
	}
	
}
