<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class rseventsproController extends JControllerLegacy
{	
	/**
	 *	Main constructor
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		
		// Set the table directory
		JTable::addIncludePath(JPATH_COMPONENT.'/tables');
	}
	
	/**
	 * Method to display a view.
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false) {
		// Add the submenu
		rseventsproHelper::subMenu();
		
		parent::display();
		return $this;
	}
	
	/**
	 *	Method to display the RSEvents!Pro Dashboard
	 *
	 * @return void
	 */
	public function rseventspro() {		
		$this->setRedirect('index.php?option=com_rseventspro');
	}
	
	/**
	 *	Method to save payment rules
	 *
	 * @return int		The id of the recent created rule.
	 */
	public function saverule() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$jinput = JFactory::getApplication()->input;
		
		$query->clear();
		$query->insert($db->qn('#__rseventspro_rules'))
			->set($db->qn('payment').' = '.$db->q($jinput->getString('payment')))
			->set($db->qn('status').' = '.$db->q($jinput->getInt('status')))
			->set($db->qn('interval').' = '.$db->q($jinput->getInt('interval')))
			->set($db->qn('rule').' = '.$db->q($jinput->getInt('rule')))
			->set($db->qn('mid').' = '.$db->q($jinput->getInt('mid')));
		
		$db->setQuery($query);
		$db->execute();
		
		echo 'RS_DELIMITER0';
		echo $db->insertid();
		echo 'RS_DELIMITER1';
		JFactory::getApplication()->close();
	}
	
	/**
	 *	Method to get the total
	 *
	 * @return number
	 */
	public function total() {
		$app 		= JFactory::getApplication();
		$jinput		= $app->input;
		$db 		= JFactory::getDBO();
		$query		= $db->getQuery(true);
		$tickets	= $jinput->get('tickets',array(),'array');
		$total		= 0;
		
		if (!empty($tickets)) {
			foreach ($tickets as $tid => $quantity) {
				$query->clear()
					->select($db->qn('price'))
					->from($db->qn('#__rseventspro_tickets'))
					->where($db->qn('id').' = '.(int) $tid);
				
				$db->setQuery($query);
				$price = $db->loadResult();
				
				// Calculate the total
				if ($price > 0) {
					$price = $price * $quantity;
					$total += $price;
				}
			}
		}
		
		$total 	= $total < 0 ? 0 : $total;
		$total 	= rseventsproHelper::currency($total);
		header('Content-type: text/html; charset=utf-8');
		echo 'RS_DELIMITER0'.$total.'RS_DELIMITER1';
		exit();
	}
	
	/**
	 *	Method to load search results
	 *
	 * @return void
	 */
	public function filter() {
		$method = JFactory::getApplication()->input->get('method','');
		if (!$method) echo 'RS_DELIMITER0';
		echo rseventsproHelper::filter();
		if (!$method) echo 'RS_DELIMITER1';
		JFactory::getApplication()->close();
	}
	
	/**
	 *	Method to display location results
	 *
	 * @return void
	 */
	public function locations() {
		echo rseventsproHelper::filterlocations();
		JFactory::getApplication()->close();
	}
	
	/**
	 *	Method to check how many repeats the current event has.
	 *
	 * @return void
	 */
	public function repeats() {
		require_once JPATH_SITE . '/components/com_rseventspro/helpers/recurring.php';
		
		$input		= JFactory::getApplication()->input;
		$registry	= new JRegistry;
		
		$registry->set('interval', $input->getInt('interval',0));
		$registry->set('type', $input->getInt('type',0));
		$registry->set('start', $input->getString('start'));
		$registry->set('end', $input->getString('end'));
		$registry->set('days', $input->get('days',array(),'array'));
		$registry->set('also', $input->get('also',array(),'array'));
		$registry->set('exclude', $input->get('exclude',array(),'array'));
		
		$registry->set('repeat_on_type', $input->getInt('repeat_on_type',0));
		$registry->set('repeat_on_day', $input->getInt('repeat_on_day',0));
		$registry->set('repeat_on_day_order', $input->getInt('repeat_on_day_order',0));
		$registry->set('repeat_on_day_type', $input->getInt('repeat_on_day_type',0));
		
		$recurring = RSEventsProRecurring::getInstance($registry);
		$dates = $recurring->getDates(true);
		
		echo 'RS_DELIMITER0';
		echo count($dates);
		echo 'RS_DELIMITER1';
		JFactory::getApplication()->close();
	}
	
	/**
	 *	Method to save data
	 *
	 * @return void
	 */
	public function savedata() {
		$type	= JFactory::getApplication()->input->get('type');
		$format	= JFactory::getApplication()->input->get('format');
		$data	= JFactory::getApplication()->input->get('jform',array(),'array');
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		if ($type == 'location') {
			$table = JTable::getInstance('Location', 'rseventsproTable');
			$table->save($data);
			echo $table->id;
		} elseif ($type == 'category') {
			$data['extension'] = 'com_rseventspro';
			$data['language'] = '*';
			$table = JTable::getInstance('Category', 'rseventsproTable');
			$table->setLocation($data['parent_id'], 'last-child');
			$table->save($data);
			$table->rebuildPath($table->id);
			$table->rebuild($table->id, $table->lft, $table->level, $table->path);
			echo json_encode(JHtml::_('category.options','com_rseventspro', array('filter.published' => array(1))));
		} elseif ($type == 'ticket') {
			$data = (object) $data;
			
			$query->select('MAX('.$db->qn('order').')')
				->from($db->qn('#__rseventspro_tickets'))
				->where($db->qn('ide').' = '.$db->q($data->ide));
			$db->setQuery($query);
			$ordering = (int) $db->loadResult();
			$data->order = $ordering + 1;
			
			$groups = JFactory::getApplication()->input->get('groups',array(),'array');
			if (!empty($groups)) {
				$registry = new JRegistry;
				$registry->loadArray($groups);
				$data->groups = $registry->toString();
			}
			$db->insertObject('#__rseventspro_tickets', $data, 'id');
			
			if ($format == 'raw') {
				return $data->id;
			} else {
				echo 'RS_DELIMITER0';
				echo $data->id;
				echo 'RS_DELIMITER1';
			}
		} elseif ($type == 'coupon') {
			$query = $db->getQuery(true);
			$data = (object) $data;
			$groups = JFactory::getApplication()->input->get('groups',array(),'array');
			if (!empty($groups)) {
				$registry = new JRegistry;
				$registry->loadArray($groups);
				$data->groups = $registry->toString();
			}
			
			if (!empty($data->from) && $data->from != $db->getNullDate()) {
				$start = JFactory::getDate($data->from, rseventsproHelper::getTimezone());
				$data->from = $start->format('Y-m-d H:i:s');
			}
			
			if (!empty($data->to) && $data->to != $db->getNullDate()) {
				$end = JFactory::getDate($data->to, rseventsproHelper::getTimezone());
				$data->to = $end->format('Y-m-d H:i:s');
			}
			
			$db->insertObject('#__rseventspro_coupons', $data, 'id');
			
			if ($codes = JFactory::getApplication()->input->getString('codes')) {
				$codes = explode("\n",$codes);
				if (!empty($codes)) {
					foreach ($codes as $code) {
						$code = trim($code);
						$query->clear()
							->insert($db->qn('#__rseventspro_coupon_codes'))
							->set($db->qn('idc').' = '.(int) $data->id)
							->set($db->qn('code').' = '.$db->q($code));
						
						$db->setQuery($query);
						$db->execute();
					}
				}
			}
			
			if ($format == 'raw') {
				return $data->id;
			} else {
				echo 'RS_DELIMITER0';
				echo $data->id;
				echo 'RS_DELIMITER1';
			}
		}
		JFactory::getApplication()->close();
	}
	
	public function loadfile() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$id		= JFactory::getApplication()->input->getInt('id');
		
		$query->select('*')
			->from($db->qn('#__rseventspro_files'))
			->where($db->qn('id').' = '.$id);
		
		$db->setQuery($query);
		if ($file = $db->loadObject()) {
			if ($file->permissions == '') {
				$file->permissions = '000000';
			}
		}
		
		echo json_encode($file);
		JFactory::getApplication()->close();
	}
	
	// Create a backup of your events
	public function backup() {
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/backup.php';
		
		$app	= JFactory::getApplication();
		$step	= $app->input->getInt('step',0);
		$backup = new RSEBackup;
		$backup->process($step);
		
		$app->close();
	}
	
	// Delete a backup
	public function backupdelete() {
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/backup.php';
		
		$app	= JFactory::getApplication();
		$file	= $app->input->getString('file');
		$backup = new RSEBackup;
		$backup->delete($file);
		
		$app->close();
	}
	
	// Extract the uploaded archive
	public function extract() {
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/backup.php';
		
		try {
			$backup = new RSEBackup;
			$backup->extract();
			$extract = $backup->getRestoreFolder();
		} catch(Exception $e) {
			$this->setMessage($e->getMessage(),'error');
			$this->setRedirect(JRoute::_('index.php?option=com_rseventspro&view=backup',false));
		}
		
		$overwrite	= JFactory::getApplication()->input->getInt('overwrite',0);
		$this->setRedirect(JRoute::_('index.php?option=com_rseventspro&view=backup'.($extract ? '&hash='.$extract : '').($overwrite ? '&overwrite=1' : ''),false));
	}
	
	// Restore backup
	public function restore() {
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/backup.php';
		
		$backup = new RSEBackup;
		$backup->set('limit', 200);
		$backup->restore($hash, $step);
		
		JFactory::getApplication()->close();
	}
	
	// Trigger plugin functions
	public function trigger() {
		JFactory::getApplication()->triggerEvent('rsepro_adminTrigger');
	}
}