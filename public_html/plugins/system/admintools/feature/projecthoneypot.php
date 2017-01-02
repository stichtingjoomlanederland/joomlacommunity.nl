<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

class AtsystemFeatureProjecthoneypot extends AtsystemFeatureAbstract
{
	protected $loadOrder = 300;

	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		if (!$this->helper->isFrontend())
		{
			return false;
		}

		return ($this->cparams->getValue('httpblenable', 0) == 1);
	}

	/**
	 * Runs the Project Honeypot HTTP:BL integration
	 */
	public function onAfterInitialise()
	{
		// Load parameters
		$httpbl_key = $this->cparams->getValue('bbhttpblkey', '');
		$minthreat  = $this->cparams->getValue('httpblthreshold', 25);
		$maxage     = $this->cparams->getValue('httpblmaxage', 30);
		$suspicious = $this->cparams->getValue('httpblblocksuspicious', 0);

		// Make sure we have an HTTP:BL  key set
		if (empty($httpbl_key))
		{
			return;
		}

		// Get the IP address
		$reqip = AtsystemUtilFilter::getIp();

		if ($reqip == '0.0.0.0')
		{
			return;
		}

		if (strpos($reqip, '::') === 0)
		{
			$reqip = substr($reqip, strrpos($reqip, ':') + 1);
		}

		// No point continuing if we can't get an address, right?
		if (empty($reqip))
		{
			return;
		}

		// IPv6 addresses are not supported by HTTP:BL yet
		if (strpos($reqip, ":"))
		{
			return;
		}

		$find   = implode('.', array_reverse(explode('.', $reqip)));
		$result = gethostbynamel($httpbl_key . ".${find}.dnsbl.httpbl.org.");

		if (empty($result))
		{
			return;
		}

		$ip = explode('.', $result[0]);

		// Make sure it's a valid response
		if ($ip[0] != 127)
		{
			return;
		}

		// Do not block search engines
		if ($ip[3] == 0)
		{
			return;
		}

		// Block harvesters and comment spammers
		$block = ($ip[3] & 2) || ($ip[3] & 4);

		// Do not block "suspicious" (not confirmed) IPs unless asked so
		if (!$suspicious && ($ip[3] & 1))
		{
			$block = false;
		}

		$block = $block && ($ip[1] <= $maxage);
		$block = $block && ($ip[2] >= $minthreat);

		if ($block)
		{
			$classes = array();

			if ($ip[3] & 1)
			{
				$classes[] = 'Suspicious';
			}

			if ($ip[3] & 2)
			{
				$classes[] = 'Email Harvester';
			}

			if ($ip[3] & 4)
			{
				$classes[] = 'Comment Spammer';
			}

			$class = implode(', ', $classes);
			$extraInfo = <<<ENDINFO
HTTP:BL analysis for blocked spammer's IP address $reqip
	Attacker class		: $class
	Last activity		: $ip[1] days ago
	Threat level		: $ip[2] --> see http://is.gd/mAwMTo for more info

ENDINFO;
			$this->exceptionsHandler->blockRequest('httpbl', '', $extraInfo);
		}
	}
} 