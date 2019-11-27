<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproModelWaitinglist extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'name', 'email', 'date', 'sent', 'confirmed'
			);
		}
		
		parent::__construct($config);
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
		$id		= JFactory::getApplication()->input->getInt('id',0);
		
		// Select fields
		$query->select('*');
		
		// Select from table
		$query->from($db->qn('#__rseventspro_waitinglist'));
		
		$query->where($db->qn('ide').' = '.$db->q($id));
		
		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->Quote('%'.$db->escape($search, true).'%');
			$query->where($db->qn('name').' LIKE '.$search.' OR '.$db->qn('email').' LIKE '.$search);
		}
		
		// Add the list ordering clause
		$listOrdering = $this->getState('list.ordering', 'date');
		$listDirn = $db->escape($this->getState('list.direction', 'asc'));
		$query->order($db->escape($listOrdering).' '.$listDirn);
		
		return $query;
	}
	
	public function getForm() {
		$form = JForm::getInstance('waitinglist', JPATH_ADMINISTRATOR.'/components/com_rseventspro/models/forms/waitinglist.xml', array('control' => 'jform')); 
		$data = $this->getItem();
		$form->bind($data);
		
		return $form;
	}
	
	public function getItem() {
		$db 	= JFactory::getDBO();
		$query 	= $db->getQuery(true);
		$id		= JFactory::getApplication()->input->getInt('id',0);
		
		$query->clear()
			->select('*')
			->from($db->qn('#__rseventspro_waitinglist'))
			->where($db->qn('id').' = '.$db->q($id));
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	public function save($data) {
		$db		= JFactory::getDBO();
		
		$form = JForm::getInstance('waitinglist', JPATH_ADMINISTRATOR.'/components/com_rseventspro/models/forms/waitinglist.xml', array('control' => 'jform'));
		$form->bind($data);
		
		$data = $form->filter($data);
		$return = $form->validate($data);

		// Check for an error.
		if ($return instanceof \Exception) {
			$this->setError($return->getMessage());
			return false;
		}

		// Check the validation results.
		if ($return === false) {
			// Get the validation messages from the form.
			foreach ($form->getErrors() as $message) {
				$this->setError($message);
			}

			return false;
		}
		
		$data = (object) $data;
		$db->updateObject('#__rseventspro_waitinglist', $data, 'id');
		
		return true;
	}
	
	public function delete($pks) {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$pks	= array_map('intval', $pks);
		
		$query->clear()
			->delete($db->qn('#__rseventspro_waitinglist'))
			->where($db->qn('id').' IN ('.implode(',',$pks).')');
		$db->setQuery($query);
		$db->execute();
	}
	
	public function approve($pks) {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$pks	= array_map('intval', $pks);
		$now	= JFactory::getDate()->toSql();
		$lang	= JFactory::getLanguage()->getTag();
		
		$query->clear()
			->select('w.*')->select($db->qn('e.waitinglist_time'))
			->from($db->qn('#__rseventspro_waitinglist','w'))
			->join('LEFT',$db->qn('#__rseventspro_events','e').' ON '.$db->qn('w.ide').' = '.$db->qn('e.id'))
			->where($db->qn('w.id').' IN ('.implode(',', $pks).')')
			->where($db->qn('w.hash').' = '.$db->q(''));
		$db->setQuery($query);
		if ($items = $db->loadObjectList()) {
			foreach ($items as $item) {
				$hash = md5($item->id.$item->email.$item->date);
				$date = '';
				
				if ($item->waitinglist_time) {
					$date = JFactory::getDate();
					$date->modify('+'.$item->waitinglist_time.' second');
					$date = rseventsproHelper::showdate($date->toSql());
				}
				
				$query->clear()
					->update($db->qn('#__rseventspro_waitinglist'))
					->set($db->qn('hash').' = '.$db->q($hash))
					->set($db->qn('sent').' = '.$db->q($now))
					->where($db->qn('id').' = '.$db->q($item->id));
				$db->setQuery($query);
				if ($db->execute()) {
					rseventsproEmails::waitinglist($item->email, $item->ide, $item->name, $hash, $date, $lang);
				}
			}
		}
	}
}