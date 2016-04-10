<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFirewallConfig
{
	protected $config;
	protected $types;
	protected $db;
	
	public function __construct() {
		$this->db = JFactory::getDbo();
		$this->load();
	}
	
	public function get($key, $default=false, $explode=false) {
		if (isset($this->config->$key)) {
			return $explode ? $this->explode($this->config->$key) : $this->config->$key;
		}
		
		return $default;
	}
	
	public function getKeys() {
		return array_keys((array) $this->config);
	}
	
	public function getData() {
		return $this->config;
	}
	
	public function reload() {
		$this->load();
	}
	
	protected function load() {
		// reset the values
		$this->config = new stdClass();
		$this->types  = new stdClass();
		
		// need this to be added as well
		$query 	= $this->db->getQuery(true);
		$query->select($this->db->qn('path'))
			  ->from('#__rsfirewall_ignored')
			  ->where($this->db->qn('type').'='.$this->db->q('ignore_folder').' OR '.$this->db->qn('type').'='.$this->db->q('ignore_file'));
		$this->db->setQuery($query);
		$this->config->ignore_files_folders = $this->implode($this->db->loadColumn());
		
		$query->clear();
		$query->select($this->db->qn('file'))
			  ->from('#__rsfirewall_hashes')
			  ->where($this->db->qn('type').'='.$this->db->q('protect'));
		$this->db->setQuery($query);
		$this->config->monitor_files = $this->implode($this->db->loadColumn());
		
		// prepare the query
		$query 	= $this->db->getQuery(true);
		$query->select('*')->from('#__rsfirewall_configuration');
		$this->db->setQuery($query);
		
		// run the query
		if ($results = $this->db->loadObjectList()) {
			foreach ($results as $result) {
				if (substr($result->type, 0, 5) == 'array') {
					$result->value = $this->explode($result->value);
				}
				
				$this->types->{$result->name}  = $result->type;
				$this->config->{$result->name} = $result->value;
			}
		}
	}
	
	protected function explode($string) {
		$string = str_replace(array("\r\n", "\r"), "\n", $string);
		return explode("\n", $string);
	}
	
	protected function implode($string) {
		return implode("\n", $string);
	}
	
	protected function convert($key, &$value) {
		if (isset($this->types->$key)) {
			switch ($this->types->$key) {
				case 'int':
					$value = (int) $value;
				break;
				
				case 'array-int':
					if (is_array($value)) {
						JArrayHelper::toInteger($value);
						$value = implode("\n", $value);
					}
				break;
				
				case 'array-text':
					if (is_array($value)) {
						$value = implode("\n", $value);
					}
				break;
			}
		}
	}
	
	public function set($key, $value) {
		if (isset($this->config->$key)) {
			// convert values to appropriate type
			$this->convert($key, $value);
			
			// refresh our value
			$this->config->$key = $value;
			
			// arrays are converted to strings here
			if (is_array($value)) {
				$value = implode("\n", $value);
			}
			
			// prepare the query
			$query = $this->db->getQuery(true);
			$query->update('#__rsfirewall_configuration')
				  ->set($this->db->qn('value').'='.$this->db->q($value))
				  ->where($this->db->qn('name').'='.$this->db->q($key));
			$this->db->setQuery($query);
			
			// run the query
			return $this->db->execute();
		}
		
		return false;
	}
	
	public function append($key, $value) {
		// Ignore files and folders
		if ($key == 'ignore_files_folders') {
			$value = trim($value);
			if (!is_file($value) && !is_dir($value)) {
				return false;
			}
			
			$table = JTable::getInstance('Ignored', 'RSFirewallTable');
			$table->bind(array(
				'path' => $value,
				'type' => is_dir($value) ? 'ignore_folder' : 'ignore_file'
			));
			return $table->store();
		}
		
		return false;
	}
	
	public static function getInstance() {
		static $inst;
		if (!$inst) {
			$inst = new RSFirewallConfig();
		}
		
		return $inst;
	}
}