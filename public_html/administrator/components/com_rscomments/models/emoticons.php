<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RSCommentsModelEmoticons extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array()) {
		parent::__construct($config);
	}
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null) {
		// List state information.
		parent::populateState('id', 'ASC');
		$this->setState('list.limit',0);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery() {
		$db 	= JFactory::getDBO();
		$query 	= $db->getQuery(true);

		// Select fields
		$query->select('*');

		// Select from table
		$query->from($db->qn('#__rscomments_emoticons'));

		// Add the list ordering clause
		$listOrdering  = $this->getState('list.ordering', 'id');
		$listDirection = $db->escape($this->getState('list.direction', 'ASC'));
		$query->order($db->qn($listOrdering).' '.$listDirection);
		
		return $query;
	}

	public function getSideBar() {
		require_once JPATH_ADMINISTRATOR.'/components/com_rscomments/helpers/toolbar.php';
		return RSCommentsToolbarHelper::render();
	}
	
	public function add() {
		$db 	= JFactory::getDBO();
		$query 	= $db->getQuery(true);
		
		$query->insert($db->qn('#__rscomments_emoticons'))
			->set($db->qn('replace').' = '.$db->q(''))
			->set($db->qn('with').' = '.$db->q(''));
		$db->setQuery($query);
		$db->execute();
		
		return $db->insertid();
	}
	
	public function save() {
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$input	= JFactory::getApplication()->input;
		$replace= $input->getString('replace','');
		$with	= $input->getString('with','');
		$id		= $input->getInt('id',0);
		$data	= array();
		
		$query->select($db->qn('id'))
			->from($db->qn('#__rscomments_emoticons'))
			->where($db->qn('replace').' = '.$db->q($replace));
		$db->setQuery($query);
		$eid = (int) $db->loadResult();
		
		if (!empty($eid) && $eid != $id) {
			$data['error'] = JText::_('COM_RSCOMMENTS_EMOTICON_SAVE_ERROR');
			$data['success'] = false;
		} else {
			$query->clear()->update($db->qn('#__rscomments_emoticons'))
				->set($db->qn('replace').' = '.$db->q($replace))
				->set($db->qn('with').' = '.$db->q($with))
				->where($db->qn('id').' = '.$db->q($id));
		
			$db->setQuery($query);
			$db->execute();
			$data['success'] = true;
			
			if (strpos($with,'http') !== false) {
				$data['image'] = $with;
			} else {
				$data['image'] = JURI::root().$with;
			}
		}
		
		return $data;
	}
	
	public function delete() {
		$id		= JFactory::getApplication()->input->getInt('id',0);
		
		try {
			$db 	= JFactory::getDBO();
			$query 	= $db->getQuery(true);
		
			$query->delete($db->qn('#__rscomments_emoticons'))
				->where($db->qn('id').' = '.$db->q($id));
			$db->setQuery($query);
			$db->execute();
			return true;
		} catch (Exception $e) {
			return false;
		}
		
		return false;
	}
}