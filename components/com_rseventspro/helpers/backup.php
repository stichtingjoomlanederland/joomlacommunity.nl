<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class RSEBackup {
	
	/**
	 *	List the tables needed
	 */
	protected $tables = array(
		'#__rseventspro_groups',		
		'#__rseventspro_locations',
		'#__rseventspro_tags',
		'#__rseventspro_payments',
		'#__categories',		
		'#__rseventspro_events',
		'#__rseventspro_coupons',
		'#__rseventspro_coupon_codes',
		'#__rseventspro_tickets',
		'#__rseventspro_users',
		'#__rseventspro_user_seats',
		'#__rseventspro_user_tickets',
		'#__rseventspro_taxonomy'
	);
	
	/**
	 *	The folder where the files
	 *	will be temporary stored
	 */
	protected $folder = null;
	
	/**
	 *	The name of the archive
	 */
	protected $filename = null;
	
	/**
	 *	Database connector
	 */
	protected $db = null;
	
	/**
	 *	The restore archive
	 */
	protected $restore;
	
	/**
	 *	The folder that contains the extracted XML files
	 */
	protected $extract;
	
	
	public function __construct() {
		$this->setVariables();
	}
	
	// Process request
	public function process($step) {
		$table	  = $this->tables[$step];
		$folder	  = JPATH_ADMINISTRATOR.'/components/com_rseventspro/assets/backups/'.$this->folder;
		$archive  = JPATH_ADMINISTRATOR.'/components/com_rseventspro/assets/backups/'.$this->filename.'.zip';
		$download = JURI::root().'administrator/components/com_rseventspro/assets/backups/'.$this->filename.'.zip';
		$total	  = count($this->tables);
		$query	  = $this->db->getQuery(true);
		$next	  = $step + 1;
        $next	  = $next > $total ? $total : $next;
        
		$response = array(
			'nextstep' => $next,
			'percentage' => number_format(($next * 100) / $total,1)
		);
		
		// Create the folder
		if ($step == 0) {
			if (JFolder::exists($folder)) {
				JFile::delete(JFolder::files($folder, '.xml$', 1, true));
			} else {
				JFolder::create($folder);
			}
		}
		
		// Get details from the selected table
		$query->select('*')->from($this->db->qn($table));
		
		if ($table == '#__categories') 	{
			$query->where($this->db->qn('extension').' = '.$this->db->q('com_rseventspro'));
		}
		
		if ($table == '#__rseventspro_events') 	{
			$query->order($this->db->qn('parent').' ASC');
			$query->order($this->db->qn('start').' DESC');
		}
		
		$this->db->setQuery($query);
		if ($rows = $this->db->loadObjectList()) {
			$xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
			$xml .= '<rows>'."\n";
			
			foreach ($rows as $row) {
				$xml .= "\t".'<row>'."\n";
				foreach ($row as $tag => $value) {
					$xml .= "\t\t".'<'.$tag.'>'.$this->xmlentities($value).'</'.$tag.'>'."\n";
				}
				$xml .= "\t".'</row>'."\n";
			}
			$xml .= '</rows>';
			
			JFile::write($folder . "/$table.xml", $xml);
		}
		
		// Archive the files
		if ($step == $total - 1) {
			jimport('joomla.filesystem.archive');
			$zip = JArchive::getAdapter('zip');

			// Get the files
			$files_list = JFolder::files($folder, '.xml$', 1, true);
			
			$files = array();

			// Build the files array required by the zip adapter
			foreach ($files_list as $file) {
				$files[] = array(
					'data' => JFile::read($file),
					'name' => JFile::getName($file)
				);
			}

			// Archive the files
			if ($zip->create($archive, $files)) {
				JFile::delete(JFolder::files($folder, '.xml$', 1, true));
				JFolder::delete($folder);
				$response['name'] = $this->filename.'.zip';
				$response['date'] = JFactory::getDate(filemtime($archive))->toSql();
				$response['download'] = $download;
			}
		}
		
		echo json_encode($response);
	}
	
	// Get backup archives
	public function getBackups() {
		$backups = array();
		$folder  = JPATH_ADMINISTRATOR.'/components/com_rseventspro/assets/backups';
		
		if ($files = JFolder::files($folder, '.zip$', 1, true)) {
			foreach ($files as $file) {
				$name	= JFile::getName($file);
				$date	= JFactory::getDate(filemtime($file))->toSql();
				$url	= JURI::root().'administrator/components/com_rseventspro/assets/backups/'.$name;
				
				$backups[] = (object) array('name' => $name, 'date' => $date, 'url' => $url); 
			}
		}
		
		uasort($backups, array($this, 'sortFiles'));
		$backups = array_values($backups);
		
		return $backups;
	}
	
	// Delete a backup archive
	public function delete($file) {
		$folder	= JPATH_ADMINISTRATOR.'/components/com_rseventspro/assets/backups/';
		$return	= array('success' => false);
		
		if (JFile::exists($folder.$file)) {
			if (JFile::delete($folder.$file)) {
				$return['success'] = true;
			}
		}
		
		echo json_encode($return);
	}
	
	// Extract files from the archive
	public function extract() {
		$app	= JFactory::getApplication();
		$file	= $app->input->files->get('restore');
		$local	= $app->input->getString('local','');
		$ovrw	= $app->input->getInt('overwrite',0);
		
		if (empty($local) && empty($file['name'])) {
			throw new Exception(JText::_('COM_RSEVENTSPRO_RESTORE_NO_FILE_SELECTED'));
		}
		
		if (!empty($file['name']) && $file['size'] > 0 && $file['error'] == 0) {
			
			if (JFile::getExt($file['name']) != 'zip') {
				throw new Exception(JText::_('COM_RSEVENTSPRO_RESTORE_INVALID_EXTENSION'));
			}
			
			$this->restore = JPATH_ADMINISTRATOR.'/components/com_rseventspro/assets/restore/'.md5($file['name']).'.zip';
			JFile::upload($file['tmp_name'], $this->restore);
		} else {
			if (JFile::getExt($local) != 'zip') {
				throw new Exception(JText::_('COM_RSEVENTSPRO_RESTORE_INVALID_EXTENSION'));
			}
			
			$this->restore = JPATH_ADMINISTRATOR.'/components/com_rseventspro/assets/restore/'.md5($local).'.zip';
			JFile::copy(JPATH_ADMINISTRATOR.'/components/com_rseventspro/assets/backups/'.$local, $this->restore);
		}
		
		if (JFile::exists($this->restore)) {
			jimport('joomla.filesystem.archive');
			
			$this->extract = JPATH_ADMINISTRATOR.'/components/com_rseventspro/assets/restore/'.JFile::stripExt(JFile::getName($this->restore));
			$zip = JArchive::getAdapter('zip');
			if ($extract = $zip->extract($this->restore,$this->extract)) {
				JFile::delete($this->restore);
				
				// Check for a valid RSEvents!Pro backup archive
				$files = JFolder::files($this->extract, '#__rseventspro');
				if (empty($files)) {
					JFolder::delete($this->extract);
					throw new Exception(JText::_('COM_RSEVENTSPRO_RESTORE_INVALID_ARCHIVE'));
				}
				
				if ($ovrw) {
					foreach ($this->tables as $table) {
						if ($table == '#__categories') {
							$this->db->setQuery('DELETE FROM '.$this->db->qn($table).' WHERE '.$this->db->qn('extension').' = '.$this->db->q('com_rseventspro').' ');
							$this->db->execute();
						} else {
							$this->db->truncateTable($table);
						}
					}
				}
				
				return true;
			}
		} else {
			throw new Exception(JText::_('COM_RSEVENTSPRO_RESTORE_NO_FILE'));
		}
	}
	
	// Restore tables 
	public function restore() {
		$app	= JFactory::getApplication();
		$step	= $app->input->getInt('step',0);
		$offset	= $app->input->getInt('offset',0);
		$count	= $app->input->getInt('count',0);
		$hash	= $app->input->getString('hash');
		$limit	= $this->limit;
		$return	= array();
		
		if ($step == 0) {
			$this->db->truncateTable('#__rseventspro_tmp');
		}
		
		if ($hash) {
			$total = $this->total($hash);
			$query = $this->db->getQuery(true);
			
			$folder = JPATH_ADMINISTRATOR.'/components/com_rseventspro/assets/restore/'.$hash.'/';
			if (JFolder::exists($folder)) {
				$table = $this->tables[$step];
				if (isset($table) && JFile::exists($folder.$table.'.xml')) {
					$xml		= simplexml_load_file($folder.$table.'.xml');
					$xmltotal	= (int) $xml->count();
					$columns	= array_keys($this->db->getTableColumns($table));
					$queries	= 0;
					
					if ($rows = array_slice($xml->xpath('row'), $offset, $limit)) {
						foreach ($rows as $row) {
							$values = array();
							
							foreach ($row->children() as $element) {
								$column = $element->getName();
								$value	= (string) $element;
								
								// Skip columns that are not in the current version of the table
								if (!in_array($column,$columns)) {
									continue;
								}
								
								if ($column == 'id' && $table != '#__rseventspro_taxonomy') {
									$old = $value;
									$value = 0;
								}
								
								// Update location from the events table
								if ($table == '#__rseventspro_events') {
									if ($column == 'location') {
										$value = $this->getId($hash, '#__rseventspro_locations', $value);
									}
									
									if ($column == 'payments') {
										$registry = new JRegistry;
										$registry->loadString($value);
										if ($payments = $registry->toArray()) {
											foreach ($payments as &$payment) {
												$payment = $this->getId($hash, '#__rseventspro_payments', $payment);
											}
											
											$registry->loadArray($payments);
											$value = $registry->toString();
										}
									}
								}
								
								// Update the event ID from the coupons table
								if ($table == '#__rseventspro_coupons') {
									if ($column == 'ide') {
										$value = $this->getId($hash, '#__rseventspro_events', $value);
									}
									
									if ($column == 'groups') {
										$registry = new JRegistry;
										$registry->loadString($value);
										if ($groups = $registry->toArray()) {
											foreach ($groups as &$group) {
												$group = $this->getId($hash, '#__rseventspro_groups', $group);
											}
											
											$registry->loadArray($groups);
											$value = $registry->toString();
										}
									}
								}
								
								// Update the coupon ID from the coupon codes table
								if ($table == '#__rseventspro_coupon_codes') {
									if ($column == 'idc') {
										$value = $this->getId($hash, '#__rseventspro_coupons', $value);
									}
								}
								
								// Update the event ID from the tickets table
								if ($table == '#__rseventspro_tickets') {
									if ($column == 'ide') {
										$value = $this->getId($hash, '#__rseventspro_events', $value);
									}
									
									if ($column == 'groups') {
										$registry = new JRegistry;
										$registry->loadString($value);
										if ($groups = $registry->toArray()) {
											foreach ($groups as &$group) {
												$group = $this->getId($hash, '#__rseventspro_groups', $group);
											}
											
											$registry->loadArray($groups);
											$value = $registry->toString();
										}
									}
								}
								
								// Update the event ID from the subscriptions table
								if ($table == '#__rseventspro_users') {
									if ($column == 'ide') {
										$value = $this->getId($hash, '#__rseventspro_events', $value);
									}
								}
								
								// Update the subscription ID and the ticket ID from the subscriptions seats table
								if ($table == '#__rseventspro_user_seats') {
									if ($column == 'ids') {
										$value = $this->getId($hash, '#__rseventspro_users', $value);
									}
									
									if ($column == 'idt') {
										$value = $this->getId($hash, '#__rseventspro_tickets', $value);
									}
								}
								
								// Update the subscription ID and the ticket ID from the subscriptions seats table
								if ($table == '#__rseventspro_user_tickets') {
									if ($column == 'ids') {
										$value = $this->getId($hash, '#__rseventspro_users', $value);
									}
									
									if ($column == 'idt') {
										$value = $this->getId($hash, '#__rseventspro_tickets', $value);
									}
								}
								
								$values[] = $this->db->qn($column). ' = '.$this->db->q($value);
							}
							
							if ($table == '#__rseventspro_taxonomy') {
								$values = $this->taxonomy($values, $hash);
							}
							
							if ($table == '#__categories') {
								$new = $this->savecategory($values, $hash);
							} else {							
								$query->clear()
									->insert($this->db->qn($table))
									->set($values);
								
								$this->db->setQuery($query);
								$this->db->execute();
								
								$new = $this->db->insertid();
							}
							
							if ($table != '#__rseventspro_taxonomy') {
								$query->clear()
									->insert($this->db->qn('#__rseventspro_tmp'))
									->set($this->db->qn('hash').' = '.$this->db->q($hash))
									->set($this->db->qn('table').' = '.$this->db->q($table))
									->set($this->db->qn('old').' = '.$this->db->q($old))
									->set($this->db->qn('new').' = '.$this->db->q($new));
								$this->db->setQuery($query);
								$this->db->execute();
							}
							
							$queries++;
						}
						
						$offset = $offset + $limit;
					}
					
					if ($limit >= $xmltotal || $offset >= $xmltotal) {
						$step = $step + 1;
						$offset = 0;
					}
					
					$count = $count + $queries;
				} else {
					$step++;
				}
				
				if ($total == $count) {
					$this->updateparents($hash);
					$this->cleartotal($hash);
					JFolder::delete($folder);
				}
				
				$return['step']			= $step;
				$return['offset']		= $offset;
				$return['count']		= $count;
				$return['percentage']	= $total ? number_format(($count * 100) / $total, 1) : 0;
			}
		}
		
		echo json_encode($return);
	}
	
	// Get the name of the extracted folder
	public function getRestoreFolder() {
		return JFile::getName($this->extract);
	}
	
	// Set custom variables
	public function set($name, $value) {
		$this->{$name} = $value;
	}
	
	// Compute the total rows that need to be added
	protected function total($hash) {
		$session = JFactory::getSession();
		
		if ($session->has('rsepro'.$hash)) {
			return (int) $session->get('rsepro'.$hash);
		} else {
			$total	= 0;
			$folder = JPATH_ADMINISTRATOR.'/components/com_rseventspro/assets/restore/'.$hash.'/';
			if (JFolder::exists($folder)) {
				if ($files = JFolder::files($folder, '.xml$', 1, true)) {
					foreach ($files as $file) {
						$xml = simplexml_load_file($file);
						$total += (int) $xml->count();
					}
					
					$session->set('rsepro'.$hash, $total);
					return (int) $total;
				}
			}
		}
	}
	
	// Clear the total rows
	protected function cleartotal($hash) {
		JFactory::getSession()->clear('rsepro'.$hash);
	}
	
	// Update event children ids
	protected function updateparents($hash) {
		$query = $this->db->getQuery(true);
		
		$query->clear()
			->select($this->db->qn('e.id'))->select($this->db->qn('e.parent'))
			->from($this->db->qn('#__rseventspro_events','e'))
			->join('LEFT', $this->db->qn('#__rseventspro_tmp','t').' ON '.$this->db->qn('e.id').' = '.$this->db->qn('t.new'))
			->where($this->db->qn('t.table').' = '.$this->db->q('#__rseventspro_events'))
			->where($this->db->qn('t.hash').' = '.$this->db->q($hash))
			->where($this->db->qn('e.parent').' <> 0');
		$this->db->setQuery($query);
		if ($events = $this->db->loadObjectList()) {
			foreach ($events as $event) {
				$query->clear()
					->update($this->db->qn('#__rseventspro_events'))
					->set($this->db->qn('parent').' = '.$this->db->q($this->getId($hash,'#__rseventspro_events',$event->parent)))
					->where($this->db->qn('id').' = '.$this->db->q($event->id));
				$this->db->setQuery($query);
				$this->db->execute();
			}
		}
	}
	
	// Save the categories
	protected function savecategory($rows, $hash) {
		$data	= array();
		$table	= JTable::getInstance('Category', 'rseventsproTable');
		
		foreach ($rows as $row) {
			list($column, $value) = explode('=', $row, 2);
			
			$column			= str_replace(array('`', ' '), '', $column);
			$value			= str_replace(array('\'', ' '), '', $value);
			$data[$column]	= $value;
		}
		
		unset($data['id']);
		unset($data['asset_id']);
		unset($data['lft']);
		unset($data['rgt']);
		unset($data['level']);
		unset($data['path']);
		unset($data['checked_out']);
		unset($data['checked_out_time']);
		unset($data['access']);
		unset($data['created_user_id']);
		unset($data['created_time']);
		unset($data['modified_user_id']);
		unset($data['modified_time']);
		unset($data['hits']);
		$data['parent_id'] = $data['parent_id'] == 1 ? $data['parent_id'] : $this->getId($hash,'#__categories',$data['parent_id']);
		
		$this->db->setQuery('SELECT '.$this->db->qn('id').' FROM '.$this->db->qn('#__categories').' WHERE '.$this->db->qn('extension').' = '.$this->db->q('com_rseventspro').' AND '.$this->db->qn('title').' = '.$this->db->q($data['title']).' AND '.$this->db->qn('alias').' = '.$this->db->q($data['alias']).' AND '.$this->db->qn('parent_id').' = '.$this->db->q($data['parent_id']).' ');
		if ($id = (int) $this->db->loadResult()) {
			return $id;
		}
		
		$table->setLocation($data['parent_id'], 'last-child');
		$table->save($data);
		$table->rebuildPath($table->id);
		$table->rebuild($table->id, $table->lft, $table->level, $table->path);
		return $table->id;
	}
	
	// Update the taxonomy
	protected function taxonomy($rows, $hash) {
		$return = array();
		$tmp	= array();
		
		foreach ($rows as $row) {
			list($column, $value) = explode('=', $row, 2);
			
			$column			= str_replace(array('`', ' '), '', $column);
			$value			= str_replace(array('\'', ' '), '', $value);
			$tmp[$column]	= $value;
		}
		
		$type = $tmp['type'];
		
		if ($type == 'days' || $type == 'rating' || $type == 'reminder' || $type == 'preminder') {
			$tmp['ide'] = $this->getId($hash, '#__rseventspro_events', $tmp['ide']);
		} elseif ($type == 'groups') {
			$tmp['ide'] = $this->getId($hash, '#__rseventspro_events', $tmp['ide']);
			$tmp['id'] 	= $this->getId($hash, '#__rseventspro_groups', $tmp['id']);
		} elseif ($type == 'category') {
			$tmp['ide'] = $this->getId($hash, '#__rseventspro_events', $tmp['ide']);
			$tmp['id'] 	= $this->getId($hash, '#__categories', $tmp['id']);
		} elseif ($type == 'tag') {
			$tmp['ide'] = $this->getId($hash, '#__rseventspro_events', $tmp['ide']);
			$tmp['id'] 	= $this->getId($hash, '#__rseventspro_tags', $tmp['id']);
		}
		
		foreach ($tmp as $col => $val) {
			$return[] = $this->db->qn($col).' = '.$this->db->q($val);
		}
		
		return $return;
	}
	
	// Get the new id
	protected function getId($hash, $table, $old) {
		$query = $this->db->getQuery(true);
		
		$query->clear()
			->select($this->db->qn('new'))
			->from($this->db->qn('#__rseventspro_tmp'))
			->where($this->db->qn('hash').' = '.$this->db->q($hash))
			->where($this->db->qn('table').' = '.$this->db->q($table))
			->where($this->db->qn('old').' = '.$this->db->q($old));
		$this->db->setQuery($query);
		return (int) $this->db->loadResult();
	}
	
	// Set variables
	protected function setVariables() {
		$config = JFactory::getConfig();
		$secret = $config->get('secret');
		$date	= JFactory::getDate();
		
		$this->db		= JFactory::getDbo();
		$this->folder	= md5($secret.$date->format('Y-m-d'));
		$this->filename	= 'rseprobck-'.$date->format('Y-m-d-H-i-s');
	}
	
	// Sort files based on their time
	protected function sortFiles($a, $b) {
		$a_time = JFactory::getDate($a->date)->toUnix();
		$b_time = JFactory::getDate($b->date)->toUnix();
		
		if ($a_time == $b_time)
			return 0;

		return $a_time > $b_time ? -1 : 1;
	}
	
	// Escape xml entries
	protected function xmlentities($string) {
		return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
	}
}