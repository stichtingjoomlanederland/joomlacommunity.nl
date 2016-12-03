<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFirewallModelDBCheck extends JModelLegacy
{	
	public function __construct() {
		parent::__construct();
	}
	
	public function getIsSupported() {
		return (strpos(JFactory::getConfig()->get('dbtype'), 'mysql') !== false && $this->getTables());
	}
	
	public function getTables() {
		static $cache;
		if (is_null($cache)) {
			$db = $this->getDbo();
			$db->setQuery("SHOW TABLE STATUS");
			$tables = $db->loadObjectList();
			foreach ($tables as $i => $table) {
				if (isset($table->Engine) && $table->Engine != 'MyISAM') {
					unset($tables[$i]);
				}
			}
			
			$cache = array_values($tables);
		}
		
		return $cache;
	}
	
	public function optimizeTables() {
		$app 	= JFactory::getApplication();
		$db 	= $this->getDbo();
		$query	= $db->getQuery(true);
		$table 	= $app->input->getVar('table');
		$return = array(
			'optimize' => '',
			'repair' => ''
		);
		
		try {
			// Optimize
			$db->setQuery("OPTIMIZE TABLE ".$db->qn($table));
			$result = $db->loadObject();
			$return['optimize'] = $result->Msg_text;
		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}
		
		try {
			// Repair
			$db->setQuery("REPAIR TABLE ".$db->qn($table));
			$result = $db->loadObject();
			$return['repair'] = $result->Msg_text;
		} catch (Exception $e) {
			return false;
		}
		
		return $return;
	}
	
	public function getSideBar() {
		require_once JPATH_COMPONENT.'/helpers/toolbar.php';
		
		return RSFirewallToolbarHelper::render();
	}
}