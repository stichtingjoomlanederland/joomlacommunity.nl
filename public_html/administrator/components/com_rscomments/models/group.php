<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsModelGroup extends JModelAdmin
{
	protected $text_prefix = 'COM_RSCOMMENTS';

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 *
	 * @return	JTable	A database object
	*/
	public function getTable($type = 'Group', $prefix = 'RscommentsTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}
	
	/**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getItem($pk = null) {
		return parent::getItem($pk);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true) {
		// Get the form.
		$form = $this->loadForm('com_rscomments.group', 'group', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
			return false;

		return $form;
	}
	
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData() {
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_rscomments.edit.group.data', array());

		if (empty($data))
			$data = $this->getItem();

		$data->permissions = unserialize($data->permissions);

		return $data;
	}
	
	/**
	 * Method to save data
	 *
	 * @return	boolean
	 * @since	1.6
	 */
	public function save($data) {
		// Initialise variables;
		$table = $this->getTable();
		$pk = (!empty($data['IdGroup'])) ? $data['IdGroup'] : (int) $this->getState($this->getName() . '.id');

		$data['permissions'] = serialize($data['permissions']);

		// Load the row if saving an existing group.
		if ($pk > 0) {
			$table->load($pk);
		}

		// Bind the data.
		if (!$table->bind($data)) {
			$this->setError($table->getError());
			return false;
		}

		// Check the data.
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}

		// Store the data.
		if (!$table->store()) {
			$this->setError($table->getError());
			return false;
		}

		$this->setState($this->getName() . '.id', $table->IdGroup);

		return true;
	}
	
	/**
	 * Method to get the excluded Joomla! groups.
	 */
	public function getUsed() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$used = array();
		$jinput = JFactory::getApplication()->input;
		
		$query->clear();
		$query->select($db->qn('gid'))
			->from($db->qn('#__rscomments_groups'))
			->where($db->qn('gid').' <> '.$db->q(''))
			->where($db->qn('IdGroup').' <> '.$db->q($jinput->getInt('IdGroup',0)));
		
		$db->setQuery($query);
		$used = $db->loadColumn();
		
		if (!empty($used)) {
			JArrayHelper::toInteger($used);
			$used = array_unique($used);
			return $used;
		}
		
		return '';
	}

	public function getTabs() {
		$tabs = new RSTabs('com-rscomments-group');
		return $tabs;
	}
}