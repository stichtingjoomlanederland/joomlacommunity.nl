<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

class AtsystemFeatureGeoblock extends AtsystemFeatureAbstract
{
	protected $loadOrder = 30;

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
		$ip = AtsystemUtilFilter::getIp();

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
			$extraInfo = 'Continent : ' . $continent;
			$this->exceptionsHandler->blockRequest('geoblocking', null, $extraInfo);
		}

		if (($country) && !empty($countries) && in_array($country, $countries))
		{
			$extraInfo = 'Country : ' . $country;
			$this->exceptionsHandler->blockRequest('geoblocking', null, $extraInfo);
		}
	}
}