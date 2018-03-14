<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

class AtsystemFeatureGeoblock extends AtsystemFeatureAbstract
{
	protected $loadOrder = 30;

	/** @var  string  Extra info to log when geo-blocking an IP */
	private $extraInfo = null;

	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		$cnt = $this->cparams->getValue('geoblockcountries', '');
		$con = $this->cparams->getValue('geoblockcontinents', '');

		return ((!empty($cnt) || !empty($con)) && class_exists('AkeebaGeoipProvider'));
	}

	public function onAfterInitialise()
	{
		if (!$this->isIPBlocked())
		{
			return;
		}

		$this->exceptionsHandler->blockRequest('geoblocking', null, $this->extraInfo);
	}

	/**
	 * Is the IP blocked by a Geo-blocking rule?
	 *
	 * @param   string  $ip  The IP address to check. Skip or pass empty string / null to use the current visitor's IP.
	 *
	 * @return  bool
	 */
	public function isIPBlocked($ip = null)
	{
		if (empty($ip))
		{
			// Get the visitor's IP address
			$ip = AtsystemUtilFilter::getIp();
		}

		$continents = $this->cparams->getValue('geoblockcontinents', '');
		$continents = empty($continents) ? array() : explode(',', $continents);
		$countries  = $this->cparams->getValue('geoblockcountries', '');
		$countries  = empty($countries) ? array() : explode(',', $countries);

		$geoip     = new AkeebaGeoipProvider();
		$country   = $geoip->getCountryCode($ip);
		$continent = $geoip->getContinent($ip);

		if (empty($country))
		{
			$country = '(unknown country)';
		}

		if (empty($continent))
		{
			$continent = '(unknown continent)';
		}

		if (($continent) && !empty($continents) && in_array($continent, $continents))
		{
			$this->extraInfo = 'Continent : ' . $continent;

			return true;
		}

		if (($country) && !empty($countries) && in_array($country, $countries))
		{
			$this->extraInfo = 'Country : ' . $country;

			return true;
		}

		return false;
	}
}
