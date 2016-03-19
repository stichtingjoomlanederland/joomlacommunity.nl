<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class rseventsproModelEvent extends JModelAdmin
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
	public function getTable($type = 'Event', $prefix = 'rseventsproTable', $config = array()) {
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
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			
			if (empty($item->location))
				$item->location = 0;
			
			$query->clear()
				->select($db->qn('id'))
				->select($db->qn('name'))
				->from($db->qn('#__rseventspro_locations'))
				->where($db->qn('id').' = '.$item->location);
			$db->setQuery($query);
			$location = $db->loadObject();
			$item->locationname = $location->name;
			
			// Convert image properties
			$registry = new JRegistry;
			$registry->loadString($item->properties);
			$item->properties = $registry->toArray();
			
			if (empty($item->start) || $item->start == $db->getNullDate()) {
				$item->start = JFactory::getDate()->toSql();
			}
			
			if (empty($item->end) || $item->end == $db->getNullDate()) {
				$end = JFactory::getDate();
				$end->modify('+2 hours');
				$item->end = $end->toSql();
			}
			
			if (empty($item->id)) 
				$item->published = 1;
			
			if (empty($item->URL) && empty($item->id)) 
				$item->URL = 'http://';
			
			if (empty($item->itemid)) 
				$item->itemid = '';
			
			if (empty($item->repeat_end) || $item->repeat_end == $db->getNullDate()) 
				$item->repeat_end = '';
			
			$item->repeat_end = str_replace(' 00:00:00','',$item->repeat_end);
			
			if (empty($item->start_registration) || $item->start_registration == $db->getNullDate()) 
				$item->start_registration = '';
			
			if (empty($item->end_registration) || $item->end_registration == $db->getNullDate()) 
				$item->end_registration = '';
				
			if (empty($item->unsubscribe_date) || $item->unsubscribe_date == $db->getNullDate()) 
				$item->unsubscribe_date = '';
				
			if (empty($item->early_fee_end) || $item->early_fee_end == $db->getNullDate()) 
				$item->early_fee_end = '';
				
			if (empty($item->late_fee_start) || $item->late_fee_start == $db->getNullDate()) 
				$item->late_fee_start = '';
				
			if (empty($item->repeat_type)) 
				$item->repeat_type = 1;
				
			if (empty($item->repeat_interval)) 
				$item->repeat_interval = 0;	
				
			if (empty($item->owner)) 
				$item->owner = JFactory::getUser()->get('id');
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
		$form = $this->loadForm('com_rseventspro.event', 'event', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_rseventspro.edit.event.data', array());

		if (empty($data))
			$data = $this->getItem();

		return $data;
	}
	
	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	public function save($data) {
		// Initialise variables;
		$table = $this->getTable();
		$pk = (!empty($data['id'])) ? $data['id'] : (int) $this->getState($this->getName() . '.id');
		$isNew = true;
		
		// Load the row if saving an existing tag.
		if ($pk > 0) {
			$table->load($pk);
			$isNew = false;
		}
		
		// Verify data
		if (!$table->verify($data)) {
			$this->setError($table->getError());
			return false;
		}
		
		// Bind the data.
		if (!$table->bind($data)) {
			$this->setError($table->getError());
			return false;
		}
		
		if (isset($data['from']))
			$table->from = $data['from'];
		
		// Check the data.
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}
		
		JFactory::getApplication()->triggerEvent('rsepro_beforeEventStore',array(array('data'=>&$table)));
		
		// Store the data.
		if (!$table->store()) {
			$this->setError($table->getError());
			return false;
		}
		
		// After store
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/events.php';
		$event = RSEvent::getInstance($table->id);
		$event->save($table, $isNew);
		
		JFactory::getApplication()->triggerEvent('rsepro_afterEventStore',array(array('data'=>&$table, 'event' => $event)));
		
		$this->setState($this->getName() . '.id', $table->id);
		$this->setState($this->getName() . '.name', $table->name);
		
		return true;
	}
	
	/**
	 * Method to export events to iCal format.
	 *
	 * @return	.ics file
	 */
	public function exportical($pks) {
		if (!empty($pks)) {
			require_once JPATH_SITE.'/components/com_rseventspro/helpers/ical.php';
			$ical = RSEventsProiCal::getInstance($pks);
			
			$ical->toIcal();
		} else {
			$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
			return false;
		}
	}
	
	/**
	 * Method to export events to CSV format.
	 *
	 * @return	.csv file
	 */
	public function exportcsv($pks) {
		if (!empty($pks)) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$csv = '';
			
			$query->clear()
				->select($db->qn('e.name'))->select($db->qn('e.start'))->select($db->qn('e.end'))->select($db->qn('e.description'))
				->select($db->qn('l.name','locationname'))->select($db->qn('l.address'))
				->from($db->qn('#__rseventspro_events','e'))
				->join('left', $db->qn('#__rseventspro_locations','l').' ON '.$db->qn('l.id').' = '.$db->qn('e.location'))
				->where($db->qn('e.id').' IN ('.implode(',',$pks).')');
			
			$db->setQuery($query);
			if ($events = $db->loadObjectList()) {
				foreach ($events as $event)
				{
					$name = strip_tags($event->name);
					$name = str_replace(array('\\r','\\n','\\t','"'),array("\015","\012","\011",'""'),$name);
					$description = strip_tags($event->description);
					$description = str_replace(array('\\r','\\n','\\t','"'),array("\015","\012","\011",'""'),$description);
					$url = strip_tags($event->URL);
					$url = str_replace(array('\\r','\\n','\\t','"'),array("\015","\012","\011",'""'),$url);
					$email = strip_tags($event->email);
					$email = str_replace(array('\\r','\\n','\\t','"'),array("\015","\012","\011",'""'),$email);
					$phone = strip_tags($event->phone);
					$phone = str_replace(array('\\r','\\n','\\t','"'),array("\015","\012","\011",'""'),$phone);
					$locationname = strip_tags($event->locationname);
					$locationname = str_replace(array('\\r','\\n','\\t','"'),array("\015","\012","\011",'""'),$locationname);
					$address = strip_tags($event->address);
					$address = str_replace(array('\\r','\\n','\\t','"'),array("\015","\012","\011",'""'),$address);
					
					$start	= JFactory::getDate($event->start);
					$end	= JFactory::getDate($event->end);
					
					$csv .= '"'.$name.'","'.$start->toSql().'","'.$end->toSql().'","'.$description.'","'.$url.'","'.$email.'","'.$phone.'","'.$locationname.'","'.$address.'"'."\n";
				}
				
				$file = 'Events.csv';
				header("Content-type: text/csv; charset=UTF-8");
				header("Content-Disposition: attachment; filename=$file");
				echo rtrim($csv,"\n");
				JFactory::getApplication()->close();
			} else {
				$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
				return false;
			}
		} else {
			$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
			return false;
		}
	}
	
	/**
	 * Method to clear event rating.
	 *
	 * @return	boolean
	 */
	public function rating($pks) {
		if (!empty($pks)) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			
			$query->clear()
				->delete()
				->from($db->qn('#__rseventspro_taxonomy'))
				->where($db->qn('type').' = '.$db->q('rating'))
				->where($db->qn('ide').' IN ('.implode(',',$pks).')');
			
			$db->setQuery($query);
			$db->execute();
			
			return true;
		} else {
			$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
			return false;
		}
	}
	
	/**
	 * Method to copy events.
	 *
	 * @return	void
	 */
	public function copy($pks) {
		if (!empty($pks)) {
			foreach ($pks as $pk) {
				rseventsproHelper::copy($pk,0);
			}
			return true;
		} else {
			$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
			return false;
		}
	}
	
	/**
	 * Method to remove ticket.
	 *
	 * @return	boolean
	 */
	public function removeticket() {
		$id = JFactory::getApplication()->input->getInt('id');
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->clear()
			->delete()
			->from($db->qn('#__rseventspro_tickets'))
			->where($db->qn('id').' = '.$id);
		
		$db->setQuery($query);
		return $db->execute();
	}
	
	/**
	 * Method to remove coupon.
	 *
	 * @return	boolean
	 */
	public function removecoupon() {
		$id = JFactory::getApplication()->input->getInt('id');
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->clear()
			->delete()
			->from($db->qn('#__rseventspro_coupons'))
			->where($db->qn('id').' = '.$id);
		
		$db->setQuery($query);
		if ($db->execute()) {
			$query->clear()
				->delete()
				->from($db->qn('#__rseventspro_coupon_codes'))
				->where($db->qn('idc').' = '.$id);
			$db->setQuery($query);
			$db->execute();
			return true;
		} else return false;
	}
	
	/**
	 * Method to upload event icon.
	 *
	 * @return	boolean
	 */
	public function upload() {
		jimport('joomla.filesystem.file');
		
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$input	= JFactory::getApplication()->input;
		$id		= $input->getInt('id');
		$icon	= $input->files->get('icon',array(),'array');
		$local	= $input->getString('local');
		$path	= JPATH_SITE.'/components/com_rseventspro/assets/images/events/';
		$empty  = empty($local) && $icon['size'] == 0;
		$type	= '';
		
		if ($icon['size'] > 0 && $icon['error'] == 0) {
			$type = 'upload';
		} elseif (!empty($local)) {
			$type = 'local';
		}
		
		if (!$empty || empty($type)) {
			// Check for file extension
			$ext = JFile::getExt($type == 'upload' ? $icon['name'] : $local);
			if (!in_array(strtolower($ext),array('jpg','png','jpeg'))) {
				$this->setError(JText::_('COM_RSEVENTSPRO_WRONG_FILE_TYPE'));
				return false;
			}
			
			// Remove old icon
			$query->clear()
				->select($db->qn('icon'))
				->from($db->qn('#__rseventspro_events'))
				->where($db->qn('id').' = '.$id);
			
			$db->setQuery($query);
			if ($eventicon = $db->loadResult()) {
				if (JFile::exists(JPATH_SITE.'/components/com_rseventspro/assets/images/events/'.$eventicon)) {
					JFile::delete(JPATH_SITE.'/components/com_rseventspro/assets/images/events/'.$eventicon);
				}
			}
			
			// Copy or upload the new icon
			if ($type == 'upload') {
				// Upload file
				if ($icon['error'] == 0) {
					$file		= JFile::makeSafe($icon['name']);
					$filename	= JFile::getName(JFile::stripExt($file));
					
					while(JFile::exists($path.$filename.'.'.$ext)) {
						$filename .= rand(1,999);
					}
						
					if (JFile::upload($icon['tmp_name'],$path.$filename.'.'.$ext)) {
						$query->clear()
							->update($db->qn('#__rseventspro_events'))
							->set($db->qn('icon').' = '.$db->q($filename.'.'.$ext))
							->set($db->qn('properties').' = '.$db->q(''))
							->where($db->qn('id').' = '.$id);
						
						$db->setQuery($query);
						$db->execute();
						
						$this->setState('com_rseventspro.edit.icon', $filename.'.'.$ext);
					} else { 
						$this->setError(JText::_('COM_RSEVENTSPRO_UPLOAD_ERROR'));
						return false;
					}
				} else {
					$this->setError(JText::_('COM_RSEVENTSPRO_FILE_ERROR'));
					return false;
				}
			} else {
				// Copy local file
				$params 	= JComponentHelper::getParams('com_media');
				$root		= JPATH_ROOT . '/' . $params->get('image_path', 'images');
				$root		= str_replace(DIRECTORY_SEPARATOR, '/', $root . '/');
				$file		= $root.$local;
				$filename	= JFile::getName(JFile::stripExt($file));
				
				while(JFile::exists($path.$filename.'.'.$ext)) {
					$filename .= rand(1,999);
				}
				
				if (JFile::copy($file,$path.$filename.'.'.$ext)) {
					$query->clear()
						->update($db->qn('#__rseventspro_events'))
						->set($db->qn('icon').' = '.$db->q($filename.'.'.$ext))
						->set($db->qn('properties').' = '.$db->q(''))
						->where($db->qn('id').' = '.$id);
					
					$db->setQuery($query);
					$db->execute();
					
					$this->setState('com_rseventspro.edit.icon', $filename.'.'.$ext);
				} else { 
					$this->setError(JText::_('COM_RSEVENTSPRO_COPY_ERROR'));
					return false;
				}
			}
			
		} else {
			$this->setError(JText::_('COM_RSEVENTSPRO_NO_FILE_SELECTED'));
			return false;
		}
		
		return true;
	}
	
	public function getIcon() {
		if ($icon = JFactory::getApplication()->input->getString('icon','')) {
			return base64_decode($icon);
		}
		
		return false;
	}
	
	/**
	 * Method to delete event icon.
	 *
	 * @return	boolean
	 */
	public function deleteicon() {
		jimport('joomla.filesystem.file');
		$id		= JFactory::getApplication()->input->getInt('id');
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('icon'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('id').' = '.$id);
		
		$db->setQuery($query);
		if ($icon = $db->loadResult()) {
			if (JFile::exists(JPATH_SITE.'/components/com_rseventspro/assets/images/events/'.$icon))
				JFile::delete(JPATH_SITE.'/components/com_rseventspro/assets/images/events/'.$icon);
			
			$query->clear()
				->update($db->qn('#__rseventspro_events'))
				->set($db->qn('icon').' = '.$db->q(''))
				->set($db->qn('properties').' = '.$db->q(''))
				->where($db->qn('id').' = '.$id);
			
			$db->setQuery($query);
			$db->execute();
		}
		return true;
	}
	
	/**
	 * Method to crop the event icon.
	 *
	 * @return	boolean
	 */
	public function crop() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$input	= JFactory::getApplication()->input;
		$id		= $input->getInt('id');
		$path	= JPATH_SITE.'/components/com_rseventspro/assets/images/events/';
		
		$query->clear()
			->select($db->qn('icon'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('id').' = '.$id);
		
		$db->setQuery($query);
		$icon = $db->loadResult();
		
		$this->setState('com_rseventspro.crop.icon', $icon);
		
		$left	= $input->getInt('x1');
		$top	= $input->getInt('y1');
		$width	= $input->getInt('width');
		$height	= $input->getInt('height');
		
		$properties = array('left' => $left, 'top' => $top, 'width' => $width, 'height' => $height);
		$registry = new JRegistry;
		$registry->loadArray($properties);
		$properties = $registry->toString();
		
		$query->clear()
			->update($db->qn('#__rseventspro_events'))
			->set($db->qn('properties').' = '.$db->q($properties))
			->set($db->qn('aspectratio').' = '.$db->q($input->getInt('aspectratio',0)))
			->where($db->qn('id').' = '.$id);
		
		$db->setQuery($query);
		$db->execute();
		
		return true;
	}
	
	/**
	 * Method to save file details
	 *
	 * @return	boolean
	 */
	public function savefile() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$jinput	= JFactory::getApplication()->input->post;
		$id		= $jinput->getInt('id');
		$permissions = '';
		
		$fp0 = $jinput->get('fp0');
		$fp1 = $jinput->get('fp1');
		$fp2 = $jinput->get('fp2');
		$fp3 = $jinput->get('fp3');
		$fp4 = $jinput->get('fp4');
		$fp5 = $jinput->get('fp5');
		
		if (isset($fp0) && $fp0 == 1) $permissions .= '1'; else $permissions .= '0';
		if (isset($fp1) && $fp1 == 1) $permissions .= '1'; else $permissions .= '0';
		if (isset($fp2) && $fp2 == 1) $permissions .= '1'; else $permissions .= '0';
		if (isset($fp3) && $fp3 == 1) $permissions .= '1'; else $permissions .= '0';
		if (isset($fp4) && $fp4 == 1) $permissions .= '1'; else $permissions .= '0';
		if (isset($fp5) && $fp5 == 1) $permissions .= '1'; else $permissions .= '0';
		
		$query->clear()
			->update($db->qn('#__rseventspro_files'))
			->set($db->qn('name').' = '.$db->q($jinput->getString('name')))
			->set($db->qn('permissions').' = '.$db->q($permissions))
			->where($db->qn('id').' = '.$db->q($id));
		
		$db->setQuery($query);
		$db->execute();
		
		$this->setState('com_rseventspro.file.id',$id);
		$this->setState('com_rseventspro.file.name',$jinput->getString('name'));
		
		return true;
	}
	
	/**
	 * Method to remove event files
	 *
	 * @return	boolean
	 */
	public function removefile() {
		jimport('joomla.filesystem.file');
		
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$id		= JFactory::getApplication()->input->getInt('id');
		
		$query->clear()
			->select($db->qn('location'))
			->from($db->qn('#__rseventspro_files'))
			->where($db->qn('id').' = '.$id);
		
		$db->setQuery($query);
		if ($file = $db->loadResult()) {
			if (JFile::exists(JPATH_SITE.'/components/com_rseventspro/assets/images/files/'.$file)) {
				if (JFile::delete(JPATH_SITE.'/components/com_rseventspro/assets/images/files/'.$file)) {
					$query->clear()
						->delete()
						->from($db->qn('#__rseventspro_files'))
						->where($db->qn('id').' = '.$id);
						
					$db->setQuery($query);
					$db->execute();
					return true;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Method to get save tickets configuration
	 *
	 * @return	array
	 */
	public function tickets() {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$input		= JFactory::getApplication()->input;
		$params		= $input->get('params',array(),'array');
		
		if (!empty($params)) {
			foreach ($params as $i => $param) {
				$registry = new JRegistry;
				$registry->loadArray($param);
				$position = $registry->toString();
				
				$query->clear()
					->update($db->qn('#__rseventspro_tickets'))
					->set($db->qn('position').' = '.$db->q($position))
					->where($db->qn('id').' = '.(int) $i);
				
				$db->setQuery($query);
				$db->execute();
			}
		}
	}
	
	/**
	 * Method to delete the reports.
	 */
	public function deletereports($pks) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->delete()
			->from($db->qn('#__rseventspro_reports'))
			->where($db->qn('id').' IN ('.implode(',',$pks).')');
		$db->setQuery($query);
		$db->execute();
	}
	
	/**
	 * Method to toggle the featured setting of events.
	 *
	 * @param   array    The ids of the items to toggle.
	 * @param   integer  The value to toggle to.
	 *
	 * @return  boolean  True on success.
	 */
	public function featured($pks, $value = 0) {
		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);

		if (empty($pks)) {
			$this->setError(JText::_('JERROR_NO_ITEMS_SELECTED'));
			return false;
		}

		try {
			$db		= $this->getDbo();
			$query	= $db->getQuery(true);
			
			$query->update($db->qn('#__rseventspro_events'))
				->set($db->qn('featured').' = '.(int) $value)
				->where($db->qn('id').' IN ('.implode(',', $pks).')');
			
			$db->setQuery($query);
			$db->execute();
		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}

		return true;
	}
	
	public function batchProcess($pks) {
		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);
		
		$batch	 = JFactory::getApplication()->input->get('batch',array(),'array');
		$all	 = isset($batch['all']) ? $batch['all'] : 0;
		$options = isset($batch['options']) ? $batch['options'] : array();
		$enable	 = isset($batch['enable_options']) ? 1 : 0;
		
		$categories	= isset($batch['categories']) ? $batch['categories'] : array();
		$tags		= isset($batch['tags']) ? $batch['tags'] : array();
		$location	= isset($batch['location']) ? $batch['location'] : 0;
		$itemid		= isset($batch['itemid']) ? $batch['itemid'] : '';
		$type		= isset($batch['type']) ? $batch['type'] : 'r';
		
		if (empty($pks) && !$all) {
			$this->setError(JText::_('JERROR_NO_ITEMS_SELECTED'));
			return false;
		}
		
		try {
			$db		 = $this->getDbo();
			$query	 = $db->getQuery(true);
			
			if ($enable) {
				$defaults = rseventsproHelper::getDefaultOptions();
				$registry = new JRegistry;
				$registry->loadString($defaults);
				$defaults = $registry->toArray();
				
				foreach ($defaults as $name => $value) {
					if (!isset($options[$name]))
						$options[$name] = 0;
				}
			
				$registry = new JRegistry;
				$registry->loadArray($options);
			
				$query->update($db->qn('#__rseventspro_events'))->set($db->qn('options').' = '.$db->q($registry->toString()));
			
				if ($location) {
					$query->set($db->qn('location').' = '.$db->q($location));
				}
				
				if ($itemid) {
					$query->set($db->qn('itemid').' = '.$db->q($itemid));
				}
				
				if (!$all) {
					$query->where($db->qn('id').' IN ('.implode(',', $pks).')');
				}
				
				$db->setQuery($query);
				$db->execute();
			} else {
				if ($location || $itemid) {
					$query->update($db->qn('#__rseventspro_events'));
					
					if ($location) {
						$query->set($db->qn('location').' = '.$db->q($location));
					}
					
					if ($itemid) {
						$query->set($db->qn('itemid').' = '.$db->q($itemid));
					}
					
					if (!$all) {
						$query->where($db->qn('id').' IN ('.implode(',', $pks).')');
					}
					
					$db->setQuery($query);
					$db->execute();
				}
			}
			
			// Update categories
			if (!empty($categories)) {
				JArrayHelper::toInteger($categories);
				
				if ($all) {
					if ($type == 'r') {
						$query->clear()
							->delete($db->qn('#__rseventspro_taxonomy'))
							->where($db->qn('type').' = '.$db->q('category'));
						$db->setQuery($query);
						$db->execute();
					}
					
					$query->clear()
						->select($db->qn('id'))
						->from($db->qn('#__rseventspro_events'));
					$db->setQuery($query);
					if ($events = $db->loadColumn()) {
						foreach ($events as $id) {
							foreach ($categories as $category) {
								$query->clear()
									->select($db->qn('id'))
									->from($db->qn('#__rseventspro_taxonomy'))
									->where($db->qn('type').' = '.$db->q('category'))
									->where($db->qn('ide').' = '.$db->q($id))
									->where($db->qn('id').' = '.$db->q($category));
								$db->setQuery($query);
								$txid = (int) $db->loadResult();
								
								if (!$txid) {
									$query->clear()
										->insert($db->qn('#__rseventspro_taxonomy'))
										->set($db->qn('type').' = '.$db->q('category'))
										->set($db->qn('ide').' = '.$db->q($id))
										->set($db->qn('id').' = '.$db->q($category));
									$db->setQuery($query);
									$db->execute();
								}
							}
						}
					}
				} else {
					if ($type == 'r') {
						$query->clear()
							->delete($db->qn('#__rseventspro_taxonomy'))
							->where($db->qn('type').' = '.$db->q('category'))
							->where($db->qn('ide').' IN ('.implode(',', $pks).')');
						$db->setQuery($query);
						$db->execute();
					}
					
					foreach ($pks as $id) {
						foreach ($categories as $category) {
							$query->clear()
								->select($db->qn('id'))
								->from($db->qn('#__rseventspro_taxonomy'))
								->where($db->qn('type').' = '.$db->q('category'))
								->where($db->qn('ide').' = '.$db->q($id))
								->where($db->qn('id').' = '.$db->q($category));
							$db->setQuery($query);
							$txid = (int) $db->loadResult();
							
							if (!$txid) {
								$query->clear()
									->insert($db->qn('#__rseventspro_taxonomy'))
									->set($db->qn('type').' = '.$db->q('category'))
									->set($db->qn('ide').' = '.$db->q($id))
									->set($db->qn('id').' = '.$db->q($category));
								$db->setQuery($query);
								$db->execute();
							}
						}
					}
				}
			}
			
			// Update tags
			if (!empty($tags)) {
				// Get the tags ids
				$ids = array();
				foreach ($tags as $tag) {
					$tag = trim($tag);
					$query->clear()
						->select($db->qn('id'))
						->from($db->qn('#__rseventspro_tags'))
						->where($db->qn('name').' = '.$db->q($tag));
					$db->setQuery($query);
					if ($tid = (int) $db->loadResult()) {
						$ids[] = $tid;
					} else {
						$query->clear()
							->insert($db->qn('#__rseventspro_tags'))
							->set($db->qn('name').' = '.$db->q($tag))
							->set($db->qn('published').' = '.$db->q(1));
						
						$db->setQuery($query);
						$db->execute();
						$tid = $db->insertid();
						$ids[] = $tid;
					}
				}
				
				if ($all) {
					if ($type == 'r') {					
						$query->clear()
							->delete($db->qn('#__rseventspro_taxonomy'))
							->where($db->qn('type').' = '.$db->q('tag'));
						$db->setQuery($query);
						$db->execute();
					}
					
					$query->clear()
						->select($db->qn('id'))
						->from($db->qn('#__rseventspro_events'));
					$db->setQuery($query);
					if ($events = $db->loadColumn()) {
						foreach ($events as $id) {
							foreach ($ids as $idt) {
								$query->clear()
									->select($db->qn('id'))
									->from($db->qn('#__rseventspro_taxonomy'))
									->where($db->qn('type').' = '.$db->q('tag'))
									->where($db->qn('ide').' = '.$db->q($id))
									->where($db->qn('id').' = '.$db->q($idt));
								$db->setQuery($query);
								$txid = (int) $db->loadResult();
								
								if (!$txid) {
									$query->clear()
										->insert($db->qn('#__rseventspro_taxonomy'))
										->set($db->qn('type').' = '.$db->q('tag'))
										->set($db->qn('ide').' = '.$db->q($id))
										->set($db->qn('id').' = '.$db->q($idt));
									$db->setQuery($query);
									$db->execute();
								}
							}
						}
					}
				} else {
					if ($type == 'r') {
						$query->clear()
							->delete($db->qn('#__rseventspro_taxonomy'))
							->where($db->qn('type').' = '.$db->q('tag'))
							->where($db->qn('ide').' IN ('.implode(',', $pks).')');
						$db->setQuery($query);
						$db->execute();
					}
					
					foreach ($pks as $id) {
						foreach ($ids as $idt) {
							$query->clear()
								->select($db->qn('id'))
								->from($db->qn('#__rseventspro_taxonomy'))
								->where($db->qn('type').' = '.$db->q('tag'))
								->where($db->qn('ide').' = '.$db->q($id))
								->where($db->qn('id').' = '.$db->q($idt));
							$db->setQuery($query);
							$txid = (int) $db->loadResult();
							
							if (!$txid) {
								$query->clear()
									->insert($db->qn('#__rseventspro_taxonomy'))
									->set($db->qn('type').' = '.$db->q('tag'))
									->set($db->qn('ide').' = '.$db->q($id))
									->set($db->qn('id').' = '.$db->q($idt));
								$db->setQuery($query);
								$db->execute();
							}
						}
					}
				}
			}
			
			return true;
		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}
	}
	
	public function sync() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$config	= JFactory::getConfig();
		$offset	= $config->get('offset');
		
		$query->select($db->qn('id'))
			->select($db->qn('start'))->select($db->qn('timezone'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('allday').' = 1');
		
		$db->setQuery($query);
		if ($events = $db->loadObjectList()) {
			foreach ($events as $event) {
				if ($event->timezone == $offset) {
					continue;
				}
				
				if (empty($event->timezone)) {
					$event->timezone = 'UTC';
				}
				
				$date = new DateTime($event->start, new DateTimezone('UTC'));
				$date->setTimezone(new DateTimezone($event->timezone));
				
				$date = new DateTime($date->format('Y-m-d H:i:s'), new DateTimezone($offset));
				$date->setTimezone(new DateTimezone('UTC'));
				
				$start = $date->format('Y-m-d H:i:s');
				
				$query->clear()
					->update($db->qn('#__rseventspro_events'))
					->set($db->qn('start').' = '.$db->q($start))
					->set($db->qn('timezone').' = '.$db->q($offset))
					->where($db->qn('id').' = '.$db->q($event->id));
				$db->setQuery($query);
				$db->execute();
			}
		}
		
		return true;
	}
	
	public function ticketsorder() {
		$db		 = JFactory::getDbo();
		$query	 = $db->getQuery(true);
		$input	 = JFactory::getApplication()->input;
		$id		 = $input->getInt('id',0);
		$tickets = $input->get('ticket', array(), 'array');
		
		foreach ($tickets as $i => $ticket) {
			$query->clear()
				->update($db->qn('#__rseventspro_tickets'))
				->set($db->qn('order').' = '.$db->q($i))
				->where($db->qn('id').' = '.$db->q($ticket))
				->where($db->qn('ide').' = '.$db->q($id));
			$db->setQuery($query);
			$db->execute();
		}
	}
}