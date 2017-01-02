<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Model;

defined('_JEXEC') or die;

use Akeeba\AdminTools\Admin\Helper\Storage;
use FOF30\Model\Model;

class GeographicBlocking extends Model
{
	private $allContinents = array(
		'AF' => 'Africa',
		'NA' => 'North America',
		'SA' => 'South America',
		'AN' => 'Antartica',
		'AS' => 'Asia',
		'EU' => 'Europe',
		'OC' => 'Oceania'
	);

	private $allCountries = array(
		"A1" => "Anonymous Proxy", "A2" => "Satellite Provider", "O1" => "Other Country", "AD" => "Andorra", "AE" => "United Arab Emirates",
		"AF" => "Afghanistan", "AG" => "Antigua and Barbuda", "AI" => "Anguilla", "AL" => "Albania", "AM" => "Armenia", "AN" => "Netherlands Antilles",
		"AO" => "Angola", "AP" => "Asia/Pacific Region", "AQ" => "Antarctica", "AR" => "Argentina", "AS" => "American Samoa", "AT" => "Austria",
		"AU" => "Australia", "AW" => "Aruba", "AX" => "Aland Islands", "AZ" => "Azerbaijan", "BA" => "Bosnia and Herzegovina", "BB" => "Barbados",
		"BD" => "Bangladesh", "BE" => "Belgium", "BF" => "Burkina Faso", "BG" => "Bulgaria", "BH" => "Bahrain", "BI" => "Burundi", "BJ" => "Benin",
		"BM" => "Bermuda", "BN" => "Brunei Darussalam", "BO" => "Bolivia", "BR" => "Brazil", "BS" => "Bahamas", "BT" => "Bhutan", "BV" => "Bouvet Island",
		"BW" => "Botswana", "BY" => "Belarus", "BZ" => "Belize", "CA" => "Canada", "CC" => "Cocos (Keeling) Islands", "CD" => "Congo, The Democratic Republic of the",
		"CF" => "Central African Republic", "CG" => "Congo", "CH" => "Switzerland", "CI" => "Cote d'Ivoire", "CK" => "Cook Islands", "CL" => "Chile", "CM" => "Cameroon",
		"CN" => "China", "CO" => "Colombia", "CR" => "Costa Rica", "CU" => "Cuba", "CV" => "Cape Verde", "CX" => "Christmas Island", "CY" => "Cyprus",
		"CZ" => "Czech Republic", "DE" => "Germany", "DJ" => "Djibouti", "DK" => "Denmark", "DM" => "Dominica", "DO" => "Dominican Republic", "DZ" => "Algeria",
		"EC" => "Ecuador", "EE" => "Estonia", "EG" => "Egypt", "EH" => "Western Sahara", "ER" => "Eritrea", "ES" => "Spain", "ET" => "Ethiopia",
		"EU" => "Europe", "FI" => "Finland", "FJ" => "Fiji", "FK" => "Falkland Islands (Malvinas)", "FM" => "Micronesia, Federated States of",
		"FO" => "Faroe Islands", "FR" => "France", "GA" => "Gabon", "GB" => "United Kingdom", "GD" => "Grenada", "GE" => "Georgia", "GF" => "French Guiana",
		"GG" => "Guernsey", "GH" => "Ghana", "GI" => "Gibraltar", "GL" => "Greenland", "GM" => "Gambia", "GN" => "Guinea", "GP" => "Guadeloupe", "GQ" => "Equatorial Guinea",
		"GR" => "Greece", "GS" => "South Georgia and the South Sandwich Islands", "GT" => "Guatemala", "GU" => "Guam", "GW" => "Guinea-Bissau", "GY" => "Guyana",
		"HK" => "Hong Kong", "HM" => "Heard Island and McDonald Islands", "HN" => "Honduras", "HR" => "Croatia", "HT" => "Haiti", "HU" => "Hungary",
		"ID" => "Indonesia", "IE" => "Ireland", "IL" => "Israel", "IM" => "Isle of Man", "IN" => "India", "IO" => "British Indian Ocean Territory", "IQ" => "Iraq",
		"IR" => "Iran, Islamic Republic of", "IS" => "Iceland", "IT" => "Italy", "JE" => "Jersey", "JM" => "Jamaica", "JO" => "Jordan", "JP" => "Japan",
		"KE" => "Kenya", "KG" => "Kyrgyzstan", "KH" => "Cambodia", "KI" => "Kiribati", "KM" => "Comoros", "KN" => "Saint Kitts and Nevis",
		"KP" => "Korea, Democratic People's Republic of", "KR" => "Korea, Republic of", "KW" => "Kuwait", "KY" => "Cayman Islands", "KZ" => "Kazakhstan",
		"LA" => "Lao People's Democratic Republic", "LB" => "Lebanon", "LC" => "Saint Lucia", "LI" => "Liechtenstein", "LK" => "Sri Lanka", "LR" => "Liberia",
		"LS" => "Lesotho", "LT" => "Lithuania", "LU" => "Luxembourg", "LV" => "Latvia", "LY" => "Libyan Arab Jamahiriya", "MA" => "Morocco", "MC" => "Monaco",
		"MD" => "Moldova, Republic of", "ME" => "Montenegro", "MG" => "Madagascar", "MH" => "Marshall Islands", "MK" => "Macedonia, Former Yugoslav Republic of",
		"ML" => "Mali", "MM" => "Myanmar", "MN" => "Mongolia", "MO" => "Macao", "MP" => "Northern Mariana Islands", "MQ" => "Martinique", "MR" => "Mauritania",
		"MS" => "Montserrat", "MT" => "Malta", "MU" => "Mauritius", "MV" => "Maldives", "MW" => "Malawi", "MX" => "Mexico", "MY" => "Malaysia", "MZ" => "Mozambique",
		"NA" => "Namibia", "NC" => "New Caledonia", "NE" => "Niger", "NF" => "Norfolk Island", "NG" => "Nigeria", "NI" => "Nicaragua", "NL" => "Netherlands",
		"NO" => "Norway", "NP" => "Nepal", "NR" => "Nauru", "NU" => "Niue", "NZ" => "New Zealand", "OM" => "Oman", "PA" => "Panama", "PE" => "Peru", "PF" => "French Polynesia",
		"PG" => "Papua New Guinea", "PH" => "Philippines", "PK" => "Pakistan", "PL" => "Poland", "PM" => "Saint Pierre and Miquelon", "PN" => "Pitcairn",
		"PR" => "Puerto Rico", "PS" => "Palestinian Territory", "PT" => "Portugal", "PW" => "Palau", "PY" => "Paraguay", "QA" => "Qatar", "RE" => "Reunion",
		"RO" => "Romania", "RS" => "Serbia", "RU" => "Russian Federation", "RW" => "Rwanda", "SA" => "Saudi Arabia", "SB" => "Solomon Islands", "SC" => "Seychelles",
		"SD" => "Sudan", "SE" => "Sweden", "SG" => "Singapore", "SH" => "Saint Helena", "SI" => "Slovenia", "SJ" => "Svalbard and Jan Mayen", "SK" => "Slovakia",
		"SL" => "Sierra Leone", "SM" => "San Marino", "SN" => "Senegal", "SO" => "Somalia", "SR" => "Suriname", "ST" => "Sao Tome and Principe", "SV" => "El Salvador",
		"SY" => "Syrian Arab Republic", "SZ" => "Swaziland", "TC" => "Turks and Caicos Islands", "TD" => "Chad", "TF" => "French Southern Territories",
		"TG" => "Togo", "TH" => "Thailand", "TJ" => "Tajikistan", "TK" => "Tokelau", "TL" => "Timor-Leste", "TM" => "Turkmenistan", "TN" => "Tunisia",
		"TO" => "Tonga", "TR" => "Turkey", "TT" => "Trinidad and Tobago", "TV" => "Tuvalu", "TW" => "Taiwan", "TZ" => "Tanzania, United Republic of",
		"UA" => "Ukraine", "UG" => "Uganda", "UM" => "United States Minor Outlying Islands", "US" => "United States", "UY" => "Uruguay", "UZ" => "Uzbekistan",
		"VA" => "Holy See (Vatican City State)", "VC" => "Saint Vincent and the Grenadines", "VE" => "Venezuela", "VG" => "Virgin Islands, British",
		"VI" => "Virgin Islands, U.S.", "VN" => "Vietnam", "VU" => "Vanuatu", "WF" => "Wallis and Futuna", "WS" => "Samoa", "YE" => "Yemen", "YT" => "Mayotte",
		"ZA" => "South Africa", "ZM" => "Zambia", "ZW" => "Zimbabwe"
	);

	private $countries = array();
	private $continents = array();

	/**
	 * Load the configuration
	 */
	public function getConfig()
	{
		$params           = Storage::getInstance();
		$countries        = $params->getValue('geoblockcountries', '');
		$continents       = $params->getValue('geoblockcontinents', '');
		$this->countries  = array();
		$this->continents = array();

		if (!empty($countries))
		{
			if (strstr($countries, ','))
			{
				$this->countries = explode(',', $countries);
			}
			else
			{
				$this->countries = array($countries);
			}
		}

		if (!empty($continents))
		{
			if (strstr($continents, ','))
			{
				$this->continents = explode(',', $continents);
			}
			else
			{
				$this->continents = array($continents);
			}
		}
	}

	/**
	 * Save the configuration to the database
	 *
	 * @param   array  $newConfig
	 */
	public function saveConfig(array $newConfig)
	{
		$params = Storage::getInstance();

		$countries = $newConfig['countries'];
		$continents = $newConfig['continents'];

		$params->setValue('geoblockcountries', $countries);
		$params->setValue('geoblockcontinents', $continents);
		$params->save();
	}

	/**
	 * Returns all continents
	 *
	 * @return  array
	 */
	public function getAllContinents()
	{
		return $this->allContinents;
	}

	/**
	 * Returns all countries
	 *
	 * @return  array
	 */
	public function getAllCountries()
	{
		return $this->allCountries;
	}

	/**
	 * Get the blocked countries
	 *
	 * @return  array
	 */
	public function getCountries()
	{
		if (empty($this->countries))
		{
			$this->getConfig();
		}

		return $this->countries;
	}

	/**
	 * Get the blocked continents
	 *
	 * @return  array
	 */
	public function getContinents()
	{
		if (empty($this->continents))
		{
			$this->getConfig();
		}

		return $this->continents;
	}

	/**
	 * Do we have the Akeeba GeoIP provider plugin installed?
	 *
	 * @return  boolean  False = not installed, True = installed
	 */
	public function hasGeoIPPlugin()
	{
		static $result = null;

		if (is_null($result))
		{
			$db = $this->container->db;

			$query = $db->getQuery(true)
				->select('COUNT(*)')
				->from($db->qn('#__extensions'))
				->where($db->qn('type') . ' = ' . $db->q('plugin'))
				->where($db->qn('folder') . ' = ' . $db->q('system'))
				->where($db->qn('element') . ' = ' . $db->q('akgeoip'));
			$db->setQuery($query);
			$result = $db->loadResult();
		}

		return ($result != 0);
	}

	/**
	 * Does the GeoIP database need update?
	 *
	 * @param   integer $maxAge The maximum age of the db in days (default: 15)
	 *
	 * @return  boolean
	 */
	public function dbNeedsUpdate($maxAge = 15)
	{
		$needsUpdate = false;

		if (!$this->hasGeoIPPlugin())
		{
			return $needsUpdate;
		}

		// Get the modification time of the database file
		$filePath = JPATH_ROOT . '/plugins/system/akgeoip/db/GeoLite2-Country.mmdb';
		$modTime = @filemtime($filePath);

		// This is now
		$now = time();

		// Minimum time difference we want (15 days) in seconds
		if ($maxAge <= 0)
		{
			$maxAge = 15;
		}

		$threshold = $maxAge * 24 * 3600;

		// Do we need an update?
		$needsUpdate = ($now - $modTime) > $threshold;

		return $needsUpdate;
	}
}