<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFirewallModelLog extends JModelAdmin
{
	public function getTable($type = 'Logs', $prefix = 'RSFirewallTable', $config = array()) {
		$table = JTable::getInstance($type, $prefix, $config);
		return $table;
	}
	
	public function getForm($data = array(), $loadData = true) {
		// Get the form.
		$form = $this->loadForm('com_rsfirewall.log', 'log', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}
	
	protected function loadFormData() {
		// Check the session for previously entered form data.
		$app  = JFactory::getApplication();
		$data = $app->getUserState('com_rsfirewall.edit.log.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}
	
	public function truncate() {
		$db = JFactory::getDBO();
		$db->truncateTable('#__rsfirewall_logs');
		
		RSFirewallLogger::getInstance()->add('critical', 'LOG_EMPTIED')->save();
	}
	
	public function prepareData($ids) {
		$table = $this->getTable();
		
		$data = array();
		foreach ($ids as $id) {
			if ($table->load($id)) {
				$data[] = $table->ip;
			}
		}
		
		return array_unique($data);
	}
}