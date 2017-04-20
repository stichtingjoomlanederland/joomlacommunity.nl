<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsModelMessage extends JModelAdmin
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
	public function getTable($type = 'Message', $prefix = 'RscommentsTable', $config = array()) {
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
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$tag	= JFactory::getApplication()->input->get('tag');

		$query->select('*')
			->from($db->qn('#__rscomments_messages'))
			->where($db->qn('tag').' = '. $db->q($tag));
		
		$db->setQuery($query);
		$messages = $db->loadObjectList();

		$messages_config = new stdClass();
		foreach($messages as $param)
			$messages_config->{$param->type} = $param->content;

		$messages_config->tag = $tag;
		return $messages_config;
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
		$form = $this->loadForm('com_rscomments.message', 'message', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_rscomments.edit.message.data', array());

		if (empty($data))
			$data = $this->getItem();

		return $data;
	}

	/**
	 * Method to save data
	 *
	 * @return	boolean
	 * @since	1.6
	 */
	public function save($data = null) {
		$db 	=& JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$jinput = JFactory::getApplication()->input;
		$jform	= $jinput->get('jform', array(), 'array');

		foreach($jform as $field => $value) {
			if($field == 'tag') continue;

			$query->select('COUNT('.$db->qn('id').')')
				->from($db->qn('#__rscomments_messages'))
				->where($db->qn('tag').' = '.$db->q($jform['tag']))
				->where($db->qn('type').' = '.$db->q($field));
			
			$db->setQuery($query);
			$check_param = $db->loadResult();
			$query->clear();

			// check if param exists
			if(!empty($check_param)) {
				$query->update($db->qn('#__rscomments_messages'));
				$query->set($db->qn('content').' = '. $db->q($value));
				$query->where($db->qn('tag').' = '.$db->q($jform['tag']));
				$query->where($db->qn('type').' = '.$db->q($field));
				
				$db->setQuery($query) ;
				$db->execute();
				$query->clear();
			} else {
				$query->insert($db->qn('#__rscomments_messages'));
				$query->set($db->qn('type').' = '.$db->q($field));
				$query->set($db->qn('tag').' = '.$db->q($jform['tag']));
				$query->set($db->qn('content').' = '. $db->q($value));
				$db->setQuery($query) ;
				$db->execute();
				$query->clear();
			}
		}

		return true;
	}
		
	public function getLanguages() {
		$lang = JFactory::getLanguage();
		return $lang->getKnownLanguages();
	}

	public function getRSTabs() {
		$tabs = new RSTabs('com-rscomments-group');
		return $tabs;
	}
}