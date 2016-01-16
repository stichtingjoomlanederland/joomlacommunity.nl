<?php

/**
 * @package		SP Libraries
 * @subpackage	Geoip
 * @copyright	KAINOTOMO PH LTD - All rights reserved.
 * @author		KAINOTOMO PH LTD
 * @link		http://www.kainotomo.com
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('JPATH_PLATFORM') or die;

require_once 'vendor/autoload.php';
use GeoIp2\Database\Reader;

/**
 * SPGeneral is a class with various frequent used functions
 *
 * @package     spcyend.utilities.factory
 * @subpackage  Utilities
 * @since       1.0.0
 */
class CYENDGeoip {

    /**
     * Constructor.
     *
     * @since   1.0.0
     *
     */
    public function __construct() {
        JFactory::getLanguage()->load('lib_spcyend', JPATH_SITE); //Load library language        
    }

    /**
	 * Gets the country name from an IP address
	 *
	 * @param   string  $ip      The IP address to look up
	 * @param   string  $locale  The locale of the country name, e.g 'de' to return the country names in German. If not specified the English (US) names are returned.
	 *
	 * @return  mixed  A string with the country name if found, false if the IP address is not found, null if the db can't be loaded
	 */
    public static function getCountryCode($ip, $locale = null) {
        $reader = new Reader(JPATH_LIBRARIES . '/spcyend/geoip/GeoLite2-Country.mmdb', $locale);
        $record = $reader->country($ip);
        return $record->country->isoCode;
    }
    
    /**
     * Decide if IP is from EU countries
     * 
     * @param string $ip        The IP address to look up
     * @param string $locale    The locale of the country name, e.g 'de' to return the country names in German. If not specified the English (US) names are returned.
     * 
     * @return boolean True if $ip in EU country, false if not
     */
    public static function isEUCountry($ip, $locale = null) {
        $iso_code = CYENDGeoip::getCountryCode($ip, $locale);
        
        $eu_iso_codes = array();
        $eu_iso_codes['Austria'] = 'AT';
        $eu_iso_codes['Belgium'] = 'BE';
        $eu_iso_codes['Bulgaria'] = 'BG';
        $eu_iso_codes['Croatia'] = 'HR';
        $eu_iso_codes['Cyprus'] = 'CY';
        $eu_iso_codes['Czech Republic'] = 'CZ';
        $eu_iso_codes['Denmark'] = 'DK';
        $eu_iso_codes['Estonia'] = 'EE';
        $eu_iso_codes['Finland'] = 'FI';
        $eu_iso_codes['France'] = 'FR';
        $eu_iso_codes['Germany'] = 'DE';
        $eu_iso_codes['Greece'] = 'EL';
        $eu_iso_codes['Hungary'] = 'HU';
        $eu_iso_codes['Ireland'] = 'IE';
        $eu_iso_codes['Italy'] = 'IT';
        $eu_iso_codes['Latvia'] = 'LV';
        $eu_iso_codes['Lithuania'] = 'LT';
        $eu_iso_codes['Luxembourg'] = 'LU';
        $eu_iso_codes['Malta'] = 'MT';
        $eu_iso_codes['Netherlands'] = 'NL';
        $eu_iso_codes['Poland'] = 'PL';
        $eu_iso_codes['Portugal'] = 'PT';
        $eu_iso_codes['Romaina'] = 'RO';
        $eu_iso_codes['Slovakia'] = 'SK';
        $eu_iso_codes['Slovenia'] = 'SI';
        $eu_iso_codes['Spain'] = 'ES';
        $eu_iso_codes['Sweeden'] = 'SE';
        $eu_iso_codes['United Kingdom and Isle of Man'] = 'GB';        
        
        foreach ($eu_iso_codes as $key => $value) {
            if ($value == $iso_code) return true;
        }
        
        return false;
        
    }
   
}
