<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsModelComment extends JModelAdmin
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
	public function getTable($type = 'Comment', $prefix = 'RscommentsTable', $config = array()) {
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
			$item->subject = html_entity_decode($item->subject, ENT_COMPAT, 'UTF-8');
			$item->name = html_entity_decode($item->name, ENT_COMPAT, 'UTF-8');
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
		$jinput = JFactory::getApplication()->input;
		
		// Get the form.
		$form = $this->loadForm('com_rscomments.comment', 'comment', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_rscomments.edit.comment.data', array());

		if (empty($data))
			$data = $this->getItem();

		return $data;
	}
	
	public function getRSFieldset() {
		$fieldset = new RSFieldset();
		return $fieldset;
	}
	
	public function votes($pks) {
		if ($pks) {
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);
			
			$query->delete($db->qn('#__rscomments_votes'))
				->where($db->qn('IdComment').' IN ('.implode(',',$pks).')');
			$db->setQuery($query);
			$db->execute();
			
			return true;
		}
		
		$this->setError(JText::_('COM_RSCOMMENTS_PLEASE_SELECT_OPTIONS'));
		return false;
	}
}