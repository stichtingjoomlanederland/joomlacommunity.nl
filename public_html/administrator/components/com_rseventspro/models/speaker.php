<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproModelSpeaker extends JModelAdmin
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
	public function getTable($type = 'Speaker', $prefix = 'RseventsproTable', $config = array()) {
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
		$form = $this->loadForm('com_rseventspro.speaker', 'speaker', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_rseventspro.edit.speaker.data', array());

		if (empty($data))
			$data = $this->getItem();

		return $data;
	}
	
	public function save($data) {
		// Initialise variables;
		$table	= $this->getTable();
		$pk 	= (!empty($data['id'])) ? $data['id'] : (int) $this->getState($this->getName() . '.id');
		
		// Load the row if saving an existing tag.
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
		
		// Upload the image
		$table->uploadImage();

		$this->setState($this->getName() . '.id', $table->id);

		return true;
	}
	
	public function deleteimage() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$id		= JFactory::getApplication()->input->getInt('id',0);
		$path	= JPATH_SITE.'/components/com_rseventspro/assets/images/speakers/';
		
		$query->select($db->qn('image'))
			->from($db->qn('#__rseventspro_speakers'))
			->where($db->qn('id').' = '.$id);
		$db->setQuery($query);
		if ($image = $db->loadResult()) {
			jimport('joomla.filesystem.file');
			
			if (file_exists($path.$image)) {
				if (JFile::delete($path.$image)) {
					$query->clear()
						->update($db->qn('#__rseventspro_speakers'))
						->set($db->qn('image').' = '.$db->q(''))
						->where($db->qn('id').' = '.$id);
					$db->setQuery($query);
					$db->execute();
					
					return true;
				}
			}
			
			return false;
		}
		
		return false;
	}
}