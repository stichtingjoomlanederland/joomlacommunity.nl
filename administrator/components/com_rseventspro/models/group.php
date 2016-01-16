<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class rseventsproModelGroup extends JModelAdmin
{
	protected $text_prefix = 'COM_RSEVENTSPRO';

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 *
	 * @return	JTable	A database object
	*/
	public function getTable($type = 'Group', $prefix = 'rseventsproTable', $config = array()) {
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
		if ($item = parent::getItem($pk)) {
			// Convert the Joomla groups field to an array.
			$registry = new JRegistry;
			$registry->loadString($item->jgroups);
			$item->jgroups = $registry->toArray();
			
			// Convert the Joomla users field to an array.
			$registry = new JRegistry;
			$registry->loadString($item->jusers);
			$item->jusers = $registry->toArray();
			
			// Convert the event options.
			$registry = new JRegistry;
			$registry->loadString($item->event);
			$item->event = $registry->toArray();
			
			// Convert the restricted categories.
			$registry = new JRegistry;
			$registry->loadString($item->restricted_categories);
			$item->restricted_categories = $registry->toArray();
		}
		
		return $item;
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
		$form = $this->loadForm('com_rseventspro.group', 'group', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_rseventspro.edit.group.data', array());

		if (empty($data))
			$data = $this->getItem();

		return $data;
	}
	
	/**
	 * Method to get the excluded Joomla! users.
	 */
	public function getExcludes() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$excludes = array();
		$jinput = JFactory::getApplication()->input;
		
		$query->clear();
		$query->select($db->qn('jusers'))
			->from($db->qn('#__rseventspro_groups'))
			->where($db->qn('jusers').' <> '.$db->q(''))
			->where($db->qn('id').' <> '.$db->q($jinput->getInt('id',0)));
		
		$db->setQuery($query);
		if ($options = $db->loadColumn()) {
			foreach ($options as $option) {
				$registry = new JRegistry;
				$registry->loadString($option);
				$option = $registry->toArray();
				JArrayHelper::toInteger($option);
				$excludes = array_merge($excludes, $option);
			}
		}
		
		$excludes = array_unique($excludes);
		return !empty($excludes) ? $excludes : '';
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
		$query->select($db->qn('jgroups'))
			->from($db->qn('#__rseventspro_groups'))
			->where($db->qn('jgroups').' <> '.$db->q(''))
			->where($db->qn('id').' <> '.$db->q($jinput->getInt('id',0)));
		
		$db->setQuery($query);
		if ($options = $db->loadColumn()) {
			foreach ($options as $option) {
				$registry = new JRegistry;
				$registry->loadString($option);
				$option = $registry->toArray();
				JArrayHelper::toInteger($option);
				$used = array_merge($used, $option);
			}
		}
		
		$used = array_unique($used);
		return !empty($used) ? $used : '';
	}
	
	/**
	 * Method to get Tabs
	 *
	 * @return	mixed	The Joomla! Tabs.
	 * @since	1.6
	 */
	public function getTabs() {
		$tabs = new RSTabs('groups');
		return $tabs;
	}
}