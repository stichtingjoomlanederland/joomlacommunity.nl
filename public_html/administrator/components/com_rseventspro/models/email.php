<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class rseventsproModelEmail extends JModelAdmin
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
	public function getTable($type = 'Email', $prefix = 'rseventsproTable', $config = array()) {
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
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);		
		$lang	= $this->getLanguage();
		$id		= $this->getId();
		$parent	= $this->getParent();
		$item	= parent::getItem($pk);
		
		if ($id) {
			$query->clear();
			$query->select('*')
				->from($db->qn('#__rseventspro_emails'))
				->where($db->qn('lang').' = '.$db->q($lang))
				->where($db->qn('parent').' = '.$db->q($id));
			$db->setQuery($query);
			$item = $db->loadObject();
			
			if (empty($item)) {
				$query->clear();
				$query->select($db->qn('subject'))
					->select($db->qn('message'))
					->from($db->qn('#__rseventspro_emails'))
					->where($db->qn('id').' = '.$db->q($id));
				$db->setQuery($query);
				$details = $db->loadObject();
				
				$query->clear();
				$query->select($db->qn('id'))
					->from($db->qn('#__rseventspro_emails'))
					->where($db->qn('lang').' = '.$db->q($lang))
					->where($db->qn('id').' = '.$db->q($id));
				$db->setQuery($query);
				$emailID = (int) $db->loadResult();
				
				$item = new stdClass();
				$item->id = $emailID ? $emailID : 0;
				$item->parent = $id;
				$item->lang = $lang;
				$item->mode = 1;
				$item->subject = !empty($details) && !empty($details->subject) ? $details->subject : '';
				$item->message = !empty($details) && !empty($details->message) ? $details->message : '';
			}
		}
		
		if (empty($id) && !empty($parent))
			$item = parent::getItem($parent);
		
		JFactory::getApplication()->setUserState('com_rseventspro.edit.email.id', array($item->id));
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
		$form = $this->loadForm('com_rseventspro.email', 'email', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_rseventspro.edit.email.data', array());

		if (empty($data))
			$data = $this->getItem();
			
		return $data;
	}
	
	/**
	 * Method to get the current selected message.
	 *
	 * @return	mixed	The current selected message.
	 * @since	1.6
	 */
	public function getLanguage() {
		$jform	= JFactory::getApplication()->input->get('jform',array(),'array');
		return !empty($jform['lang']) ? $jform['lang'] : JFactory::getLanguage()->getTag();
	}
	
	/**
	 * Method to get the parent.
	 *
	 * @return	mixed	The parent.
	 * @since	1.6
	 */
	public function getParent() {
		$jform	= JFactory::getApplication()->input->get('jform',array(),'array');
		return !empty($jform['parent']) ? $jform['parent'] : 0;
	}
	
	/**
	 * Method to get the ID.
	 *
	 * @return	mixed	The ID.
	 * @since	1.6
	 */
	public function getId() {
		$jform	= JFactory::getApplication()->input->get('jform',array(),'array');
		return !empty($jform['id']) ? $jform['id'] : 0;
	}
}