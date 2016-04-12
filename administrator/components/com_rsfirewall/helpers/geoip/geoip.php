<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class RSFirewallGeoIP
{
	protected $handle;
	protected $codes 	= array();
	protected $flags 	= array();
	protected $errors   = false; 
	
	public function __construct() {
		// detect if there's a built-in function
		if (!function_exists('geoip_database_info')) {
			$file = JPATH_ADMINISTRATOR.'/components/com_rsfirewall/assets/geoip/GeoIP.dat';
			// do we have our database?
			if (file_exists($file)) {
				// load our own wrapper functions
				require_once JPATH_ADMINISTRATOR.'/components/com_rsfirewall/helpers/geoip/database.php';
				// open database
				$this->handle = rsfirewall_geoip_open($file, RSF_GEOIP_STANDARD);
			}
		}
	}
	
	public static function getInstance() {
		static $inst;
		if (!$inst) {
			$inst = new RSFirewallGeoIP;
		}
		
		return $inst;
	}
	
	public function getCountryCode($ip) {
		if (!isset($this->codes[$ip])) {
			$this->codes[$ip] = '';
			if ($this->handle) {
				try {
					$this->codes[$ip] = rsfirewall_geoip_country_code_by_addr($this->handle, $ip);
				} catch(Exception $e) {
					if (!$this->errors) {
						$app = JFactory::getApplication();
						$app->enqueueMessage($e->getMessage(), 'error');
						$this->errors = true;
					}
				}
			} elseif (function_exists('geoip_country_code_by_name')) {
				// use the built in functions if available
				$this->codes[$ip] = @geoip_country_code_by_name($ip);
			}
		}
		
		return $this->codes[$ip];
	}
	
	public function getCountryFlag($ip) {
		$code = $this->getCountryCode($ip);
		
		if (!isset($this->flags[$code])) {
			if (file_exists(JPATH_ADMINISTRATOR.'/components/com_rsfirewall/assets/images/flags/'.strtolower($code).'.png')) {
				$this->flags[$code] = strtolower($code).'.png';
			} else {
				$this->flags[$code] = 'generic.png';
			}
		}
		
		return $this->flags[$code];
	}
	
	public function show($ip) {
		static $placeholders = array();
		if (empty($placeholders)) {
			// Load the config to get our variables
			$config = RSFirewallConfig::getInstance();
			$placeholders['ipv4'] = $config->get('ipv4_whois');
			$placeholders['ipv6'] = $config->get('ipv6_whois');
			
			// Also require our IP class
			require_once JPATH_ADMINISTRATOR.'/components/com_rsfirewall/helpers/ip/ip.php';
		}
		
		$placeholder = '';
		if (RSFirewallIPv4::test($ip)) {
			$placeholder = $placeholders['ipv4'];
		} elseif (RSFirewallIPv6::test($ip)) {
			$placeholder = $placeholders['ipv6'];
		}
		
		if ($placeholder) {
			$link = str_ireplace('{ip}', urlencode($ip), $placeholder);
			return '<a target="_blank" href="'.$link.'" class="rsf-ip-address">'.htmlentities($ip, ENT_COMPAT, 'utf-8').'</a>';
		}
		
		return '<span class="rsf-ip-address">'.htmlentities($ip, ENT_COMPAT, 'utf-8').'</span>';
	}
}