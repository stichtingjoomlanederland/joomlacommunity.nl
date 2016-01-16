<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class rseventsproModelSubscription extends JModelAdmin
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
	public function getTable($type = 'Subscription', $prefix = 'rseventsproTable', $config = array()) {
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
		return $item = parent::getItem($pk);
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
		$form = $this->loadForm('com_rseventspro.subscription', 'subscription', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_rseventspro.edit.subscription.data', array());

		if (empty($data))
			$data = $this->getItem();

		return $data;
	}
	
	/**
	 * Method to toggle the subscriber status.
	 *
	 * @param	array	The ids of the items to toggle.
	 * @param	int		The value to toggle to.
	 *
	 * @return	boolean	True on success.
	 */
	public function status($pks, $value = 0) {
		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);
		
		if (empty($pks)) {
			$this->setError(JText::_('JERROR_NO_ITEMS_SELECTED'));
			return false;
		}
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		foreach ($pks as $pk) {
			$query->clear()
				->select($db->qn('state'))
				->from($db->qn('#__rseventspro_users'))
				->where($db->qn('id').' = '.$pk);
			
			$db->setQuery($query);
			$oldstate = $db->loadResult();
			
			$query->clear()
				->update($db->qn('#__rseventspro_users'))
				->set($db->qn('state').' = '.(int) $value)
				->where($db->qn('id').' = '.$pk);
			
			$db->setQuery($query);
			$db->execute();
			
			// Send activation email
			if ($oldstate != 1 && $value == 1) {
				rseventsproHelper::confirm($pk);
			}
			
			// Send denied email
			if ($oldstate != 2 && $value == 2) {
				rseventsproHelper::denied($pk);
			}
		}
		
		return true;
	}
	
	/**
	 * Method to get Card details.
	 */
	public function getCard() {
		$id = JFactory::getApplication()->input->getInt('id');
		return rseventsproHelper::getCardDetails($id);
	}
	
	/**
	 * Method to get the RSForm!Pro fields.
	 */
	public function getFields() {
		$id = JFactory::getApplication()->input->getInt('id',0);
		return rseventsproHelper::getRSFormData($id);
	}
	
	/**
	 * Method to get all events that have registration ON.
	 */
	public function getEvents() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$active	= rseventsproHelper::getConfig('active_events');
		$tz		= JFactory::getConfig()->get('offset');
		
		$query->clear()
			->select($db->qn('id','value'))
			->select($db->qn('name','text'))
			->select($db->qn('start'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('registration').' = 1');
		
		$today = JFactory::getDate();
		$today->setTime(0,0,0);
		$today = $today->toSql();
		
		$today = JFactory::getDate($today, $tz);
		$today->setTimezone(new DateTimezone('UTC'));
		$today = $today->toSql();
		
		if ($active) {
			$query->where('(('.$db->qn('end').' >= '.$db->q(JFactory::getDate()->toSql()).' AND '.$db->qn('end').' != '.$db->q($db->getNullDate()).') OR ('.$db->qn('end').' = '.$db->q($db->getNullDate()).' AND '.$db->qn('start').' >= '.$db->q($today).'))');
		}
		
		$db->setQuery($query);
		if ($events = $db->loadObjectList()) {
			foreach ($events as $i => $event) {
				$events[$i]->text = $event->text.' ('.rseventsproHelper::showdate($event->start).')';
			}
		}
		
		return array_merge(
				array(JHTML::_('select.option', 0, JText::_('COM_RSEVENTSPRO_SELECT_EVENT'))), 
				$events
			);
	}
	
	/**
	 * Method to get the registration type
	 */
	public function getType() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$id		= JFactory::getApplication()->input->getInt('id',0);
		
		$query->clear()
			->select($db->qn('ticketsconfig'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('id').' = '.$id);
		
		$db->setQuery($query);
		$ticketsconfig = $db->loadResult();
		
		$query->clear()
			->select('COUNT('.$db->qn('id').')')
			->from($db->qn('#__rseventspro_tickets'))
			->where($db->qn('ide').' = '.(int) $id);
		$db->setQuery($query);
		$count = $db->loadResult();
		
		return $ticketsconfig && $count;
	}
	
	/**
	 * Method to save the form data.
	 *
	 * @param	array	The form data.
	 *
	 * @return	boolean	True on success.
	 * @since	1.6
	 */
	public function save($data) {
		// Initialise variables;
		$table = $this->getTable();
		$db = $table->getDbo();
		$query = $db->getQuery(true);
		$pk = (!empty($data['id'])) ? $data['id'] : (int) $this->getState($this->getName() . '.id');
		$isNew = true;
		$seats = array();
		$jinput	= JFactory::getApplication()->input;

		// Load the row if saving an existing tag.
		if ($pk > 0) {
			$table->load($pk);
			$isNew = false;
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
		
		$query->clear()
			->select($db->qn('state'))
			->from($db->qn('#__rseventspro_users'))
			->where($db->qn('id').' = '. (int) $pk);
		$db->setQuery($query);
		$state = (int) $db->loadResult();
		
		// Store the data.
		if (!$table->store()) {
			$this->setError($table->getError());
			return false;
		}
		
		if ($isNew) {
			
			$query->clear()
				->select($db->qn('ticketsconfig'))
				->from($db->qn('#__rseventspro_events'))
				->where($db->qn('id').' = '.$table->ide);
			$db->setQuery($query);
			$ticketsconfig = (int) $db->loadResult();
			
			$query->clear()
				->select('COUNT('.$db->qn('id').')')
				->from($db->qn('#__rseventspro_tickets'))
				->where($db->qn('ide').' = '.(int) $table->ide);
			$db->setQuery($query);
			$count = $db->loadResult();
			
			if ($ticketsconfig && $count) {
				$tickets	= array();
				$thetickets	= $jinput->get('tickets',array(),'array');
				$unlimited	= $jinput->get('unlimited',array(),'array');
				
				foreach ($thetickets as $tid => $theticket) {
					$tickets[$tid] = count($theticket);
				}
				
				if (!empty($unlimited)) {
					JArrayHelper::toInteger($unlimited);
					foreach ($unlimited as $unlimitedid => $quantity)
						$tickets[$unlimitedid] = $quantity;
				}
				
				$seats = $thetickets;
			} else {
				$tickets = $jinput->get('tickets',array(),'array');
			}
			
			if (!empty($tickets)) {
				JArrayHelper::toInteger($tickets);
				foreach ($tickets as $ticket => $quantity) {
					if (strpos($ticket,'ev') !== false)
						$ticket = 0;
					
					$query->clear()
						->insert($db->qn('#__rseventspro_user_tickets'))
						->set($db->qn('ids').' = '.(int) $table->id)
						->set($db->qn('idt').' = '.(int) $ticket)
						->set($db->qn('quantity').' = '.(int) $quantity);
					
					$db->setQuery($query);
					$db->execute();
					
					// Add seats
					if (isset($seats[$ticket]) && !empty($seats[$ticket])) {
						$theseats = $seats[$ticket];
						
						if (!empty($theseats)) {
							foreach ($theseats as $seat) {
								$query->clear()
									->insert($db->qn('#__rseventspro_user_seats'))
									->set($db->qn('ids').' = '.(int) $table->id)
									->set($db->qn('idt').' = '.(int) $ticket)
									->set($db->qn('seat').' = '.(int) $seat);
								
								$db->setQuery($query);
								$db->execute();
							}
						}
					}	
				}
			}
			
			// Send registration email
			if (JFactory::getApplication()->input->getInt('registration',0))
				rseventsproHelper::confirm($table->id, true, false);
		}
		
		// Send activation email
		if ($state != 1 && $data['state'] == 1)
			rseventsproHelper::confirm($table->id);
		
		// Send denied email
		if ($state != 2 && $data['state'] == 2)
			rseventsproHelper::denied($table->id);
		
		$this->setState($this->getName() . '.id', $table->id);
		
		return true;
	}
	
	/**
	 * Method to confirm subscriber.
	 */
	public function confirmsubscriber($pks) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->update('#__rseventspro_users')
			->set($db->qn('confirmed').' = 1')
			->where($db->qn('id').' IN ('.implode(',',$pks).')');
		$db->setQuery($query);
		if ($db->execute()) {
			return 1;
		}
		
		return 0;
	}
}