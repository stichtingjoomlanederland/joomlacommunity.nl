<?php
/**
 * @package        RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link           https://www.rsjoomla.com
 * @license        GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class RSFirewallGeoIP
{
	protected $handle;
	protected $codes = array();
	protected $flags = array();
	protected $ip = array();
	protected $errors = false;

	public function __construct($ip = '')
	{
		$this->buildIp($ip);

		// detect if there's a built-in function
		if (!function_exists('geoip_database_info'))
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_rsfirewall/helpers/geoip/database.php';

			switch ($this->ip['type'])
			{
				case 'ipv4':
					$file = JPATH_ADMINISTRATOR . '/components/com_rsfirewall/assets/geoip/GeoIP.dat';
					if (file_exists($file))
					{
						$this->handle = rsfirewall_geoip_open($file, RSF_GEOIP_STANDARD);
					}
					break;
				case 'ipv6':
					$file = JPATH_ADMINISTRATOR . '/components/com_rsfirewall/assets/geoip/GeoIPv6.dat';
					if (file_exists($file))
					{
						$this->handle = rsfirewall_geoip_open($file, RSF_GEOIP_STANDARD);
					}
					break;
				default:
					$file = JPATH_ADMINISTRATOR . '/components/com_rsfirewall/assets/geoip/GeoIP.dat';
					if (file_exists($file))
					{
						$this->handle = rsfirewall_geoip_open($file, RSF_GEOIP_STANDARD);
					}
					break;
			}
		}
	}

	public static function getInstance($ip = '')
	{
		static $inst;
		if (!$inst)
		{
			$inst = new RSFirewallGeoIP($ip);
		}

		return $inst;
	}

	public function buildIp($ip)
	{
		if (empty($ip))
		{
			$ip = RSFirewallIP::get();
		}
		$ipClass = new RSFirewallIP($ip);

		$ipArray = array(
			'ip'   => $ip,
			'type' => 'ipv' . $ipClass->version,
		);

		$this->ip = $ipArray;
	}

	public function getCountryCode($ip)
	{
		if (!isset($this->codes[$ip]))
		{
			$this->codes[$ip] = '';
			if ($this->handle)
			{
				try
				{
					if (RSFirewallIPv4::test($ip))
					{
						$this->codes[$ip] = rsfirewall_geoip_country_code_by_addr($this->handle, $ip);
					}
					elseif (RSFirewallIPv6::test($ip))
					{
						$this->codes[$ip] = rsfirewall_geoip_country_code_by_addr_v6($this->handle, $ip);
					}
				}
				catch (Exception $e)
				{
					if (!$this->errors)
					{
						$app = JFactory::getApplication();
						$app->enqueueMessage($e->getMessage(), 'error');
						$this->errors = true;
					}
				}
			}
			elseif (function_exists('geoip_country_code_by_name'))
			{
				// use the built in functions if available
				$this->codes[$ip] = @geoip_country_code_by_name($ip);
			}
		}

		return $this->codes[$ip];
	}

	public function getCountryFlag($ip)
	{
		$code = $this->getCountryCode($ip);

		if (!isset($this->flags[$code]))
		{
			if (file_exists(JPATH_ADMINISTRATOR . '/components/com_rsfirewall/assets/images/flags/' . strtolower($code) . '.png'))
			{
				$this->flags[$code] = strtolower($code) . '.png';
			}
			else
			{
				$this->flags[$code] = 'generic.png';
			}
		}

		return $this->flags[$code];
	}

	public function show($ip)
	{
		static $placeholders = array();
		if (empty($placeholders))
		{
			// Load the config to get our variables
			$config               = RSFirewallConfig::getInstance();
			$placeholders['ipv4'] = $config->get('ipv4_whois');
			$placeholders['ipv6'] = $config->get('ipv6_whois');

			// Also require our IP class
			require_once JPATH_ADMINISTRATOR . '/components/com_rsfirewall/helpers/ip/ip.php';
		}

		$placeholder = '';
		if (RSFirewallIPv4::test($ip))
		{
			$placeholder = $placeholders['ipv4'];
		}
		elseif (RSFirewallIPv6::test($ip))
		{
			$placeholder = $placeholders['ipv6'];
		}

		if ($placeholder)
		{
			$link = str_ireplace('{ip}', urlencode($ip), $placeholder);

			return '<a target="_blank" href="' . $link . '" class="rsf-ip-address">' . htmlentities($ip, ENT_COMPAT, 'utf-8') . '</a>';
		}

		return '<span class="rsf-ip-address">' . htmlentities($ip, ENT_COMPAT, 'utf-8') . '</span>';
	}
}