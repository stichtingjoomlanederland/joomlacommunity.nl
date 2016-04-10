<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class RSFirewallSpamCheck {
	protected $resolver;
	protected $ip;
	protected $dnsbls = array('dnsbl.tornevall.org', 'sbl-xbl.spamhaus.org');
	protected $interpreter = array(
		'dnsbl.tornevall.org' => array(
			'64' => 'IP marked as "abusive host"'
		),
		'sbl-xbl.spamhaus.org' => array(
			'2' =>  'Direct UBE sources, spam operations & spam services',
			'3' =>  'Direct snowshoe spam sources detected via automation',
			'4' =>  'CBL (3rd party exploits such as proxies, trojans, etc.)',
			'5' =>  'CBL (3rd party exploits such as proxies, trojans, etc.)',
			'6' =>  'CBL (3rd party exploits such as proxies, trojans, etc.)',
			'7' =>  'CBL (3rd party exploits such as proxies, trojans, etc.)'
		)
	);
	
	public function __construct($ip) {
		try {
			require_once dirname(__FILE__).'/ip/ip.php';
			
			// check if the ip is IPv4 compatible
			$ipClass = new RSFirewallIP($ip);
			if ($ipClass->version != 4) {
				return false;
			}
			
			$this->ip = $ip;
		
			require_once dirname(__FILE__).'/Net/DNS2.php';
			$this->resolver = new Net_DNS2_Resolver(array(
				'nameservers' => array(
					'208.67.222.222', '208.67.220.220', // Open DNS
					'8.26.56.26', '8.20.247.20', // Comodo Secure DNS
					'37.235.1.174', '37.235.1.177' // Free DNS
				),
				'timeout' => 2
			));
		} catch (Exception $e) {
			return false;
		}
		
		return true;
	}
	
	public function isListed() {
		// The resolver could not be initialized - can't check so assuming IP is safe.
		if (!$this->resolver) {
			return false;
		}
		
		// Get the reverse IP
		$reverseip = implode('.', array_reverse(explode('.', $this->ip)));
		
		// Loop through DNSBL lists
		foreach ($this->dnsbls as $dnsbl) {
			try {
				$result = $this->resolver->query($reverseip.'.'.$dnsbl, 'A');
				if ($result && isset($result->answer[0]->address)) {
					// Start parsing the result
					$parts = explode('.', $result->answer[0]->address);
					// Get the last bit of the address
					$bit = end($parts);
					if (isset($this->interpreter[$dnsbl][$bit])) {
						return (object) array(
							'dnsbl'  => $dnsbl,
							'reason' => $this->interpreter[$dnsbl][$bit]
						);
					}
				}
			} catch (Net_DNS2_Exception $e) {
				continue;
			}
		}
		
		return false;
	}
}