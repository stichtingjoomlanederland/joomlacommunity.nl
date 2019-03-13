<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2018 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFormIBAN
{
    /**
     * Semantic IBAN structure constants
     */
    const COUNTRY_CODE_OFFSET             = 0;
    const COUNTRY_CODE_LENGTH             = 2;
    const CHECKSUM_OFFSET                 = 2;
    const CHECKSUM_LENGTH                 = 2;
    const ACCOUNT_IDENTIFICATION_OFFSET   = 4;
    const INSTITUTE_IDENTIFICATION_OFFSET = 4;
    const INSTITUTE_IDENTIFICATION_LENGTH = 4;
    const BANK_ACCOUNT_NUMBER_OFFSET      = 8;
    const BANK_ACCOUNT_NUMBER_LENGTH      = 10;
    /**
     * @var array Country code to size, regex format for each country that supports IBAN
     */
    public static $ibanFormatMap = array(
        'AA' => array(12, '^[A-Z0-9]{12}$'),
        'AD' => array(20, '^[0-9]{4}[0-9]{4}[A-Z0-9]{12}$'),
        'AE' => array(19, '^[0-9]{3}[0-9]{16}$'),
        'AL' => array(24, '^[0-9]{8}[A-Z0-9]{16}$'),
        'AO' => array(21, '^[0-9]{21}$'),
        'AT' => array(16, '^[0-9]{5}[0-9]{11}$'),
        'AX' => array(14, '^[0-9]{6}[0-9]{7}[0-9]{1}$'),
        'AZ' => array(24, '^[A-Z]{4}[A-Z0-9]{20}$'),
        'BA' => array(16, '^[0-9]{3}[0-9]{3}[0-9]{8}[0-9]{2}$'),
        'BE' => array(12, '^[0-9]{3}[0-9]{7}[0-9]{2}$'),
        'BF' => array(23, '^[0-9]{23}$'),
        'BG' => array(18, '^[A-Z]{4}[0-9]{4}[0-9]{2}[A-Z0-9]{8}$'),
        'BH' => array(18, '^[A-Z]{4}[A-Z0-9]{14}$'),
        'BI' => array(12, '^[0-9]{12}$'),
        'BJ' => array(24, '^[A-Z]{1}[0-9]{23}$'),
        'BL' => array(23, '^[0-9]{5}[0-9]{5}[A-Z0-9]{11}[0-9]{2}$'),
        'BR' => array(25, '^[0-9]{8}[0-9]{5}[0-9]{10}[A-Z]{1}[A-Z0-9]{1}$'),
        'CH' => array(17, '^[0-9]{5}[A-Z0-9]{12}$'),
        'CI' => array(24, '^[A-Z]{1}[0-9]{23}$'),
        'CM' => array(23, '^[0-9]{23}$'),
        'CR' => array(17, '^[0-9]{4}[0-9]{13}$'),
        'CV' => array(21, '^[0-9]{21}$'),
        'CY' => array(24, '^[0-9]{3}[0-9]{5}[A-Z0-9]{16}$'),
        'CZ' => array(20, '^[0-9]{4}[0-9]{6}[0-9]{10}$'),
        'DE' => array(18, '^[0-9]{8}[0-9]{10}$'),
        'DK' => array(14, '^[0-9]{4}[0-9]{9}[0-9]{1}$'),
        'DO' => array(24, '^[A-Z0-9]{4}[0-9]{20}$'),
        'DZ' => array(20, '^[0-9]{20}$'),
        'EE' => array(16, '^[0-9]{2}[0-9]{2}[0-9]{11}[0-9]{1}$'),
        'ES' => array(20, '^[0-9]{4}[0-9]{4}[0-9]{1}[0-9]{1}[0-9]{10}$'),
        'FI' => array(14, '^[0-9]{6}[0-9]{7}[0-9]{1}$'),
        'FO' => array(14, '^[0-9]{4}[0-9]{9}[0-9]{1}$'),
        'FR' => array(23, '^[0-9]{5}[0-9]{5}[A-Z0-9]{11}[0-9]{2}$'),
        'GB' => array(18, '^[A-Z]{4}[0-9]{6}[0-9]{8}$'),
        'GE' => array(18, '^[A-Z]{2}[0-9]{16}$'),
        'GF' => array(23, '^[0-9]{5}[0-9]{5}[A-Z0-9]{11}[0-9]{2}$'),
        'GI' => array(19, '^[A-Z]{4}[A-Z0-9]{15}$'),
        'GL' => array(14, '^[0-9]{4}[0-9]{9}[0-9]{1}$'),
        'GP' => array(23, '^[0-9]{5}[0-9]{5}[A-Z0-9]{11}[0-9]{2}$'),
        'GR' => array(23, '^[0-9]{3}[0-9]{4}[A-Z0-9]{16}$'),
        'GT' => array(24, '^[A-Z0-9]{4}[A-Z0-9]{20}$'),
        'HR' => array(17, '^[0-9]{7}[0-9]{10}$'),
        'HU' => array(24, '^[0-9]{3}[0-9]{4}[0-9]{1}[0-9]{15}[0-9]{1}$'),
        'IE' => array(18, '^[A-Z]{4}[0-9]{6}[0-9]{8}$'),
        'IL' => array(19, '^[0-9]{3}[0-9]{3}[0-9]{13}$'),
        'IR' => array(22, '^[0-9]{22}$'),
        'IS' => array(22, '^[0-9]{4}[0-9]{2}[0-9]{6}[0-9]{10}$'),
        'IT' => array(23, '^[A-Z]{1}[0-9]{5}[0-9]{5}[A-Z0-9]{12}$'),
        'JO' => array(26, '^[A-Z]{4}[0-9]{4}[A-Z0-9]{18}$'),
        'KW' => array(26, '^[A-Z]{4}[A-Z0-9]{22}$'),
        'KZ' => array(16, '^[0-9]{3}[A-Z0-9]{13}$'),
        'LB' => array(24, '^[0-9]{4}[A-Z0-9]{20}$'),
        'LC' => array(28, '^[A-Z]{4}[A-Z0-9]{24}$'),
        'LI' => array(17, '^[0-9]{5}[A-Z0-9]{12}$'),
        'LT' => array(16, '^[0-9]{5}[0-9]{11}$'),
        'LU' => array(16, '^[0-9]{3}[A-Z0-9]{13}$'),
        'LV' => array(17, '^[A-Z]{4}[A-Z0-9]{13}$'),
        'MC' => array(23, '^[0-9]{5}[0-9]{5}[A-Z0-9]{11}[0-9]{2}$'),
        'MD' => array(20, '^[A-Z0-9]{2}[A-Z0-9]{18}$'),
        'ME' => array(18, '^[0-9]{3}[0-9]{13}[0-9]{2}$'),
        'MF' => array(23, '^[0-9]{5}[0-9]{5}[A-Z0-9]{11}[0-9]{2}$'),
        'MG' => array(23, '^[0-9]{23}$'),
        'MK' => array(15, '^[0-9]{3}[A-Z0-9]{10}[0-9]{2}$'),
        'ML' => array(24, '^[A-Z]{1}[0-9]{23}$'),
        'MQ' => array(23, '^[0-9]{5}[0-9]{5}[A-Z0-9]{11}[0-9]{2}$'),
        'MR' => array(23, '^[0-9]{5}[0-9]{5}[0-9]{11}[0-9]{2}$'),
        'MT' => array(27, '^[A-Z]{4}[0-9]{5}[A-Z0-9]{18}$'),
        'MU' => array(26, '^[A-Z]{4}[0-9]{2}[0-9]{2}[0-9]{12}[0-9]{3}[A-Z]{3}$'),
        'MZ' => array(21, '^[0-9]{21}$'),
        'NC' => array(23, '^[0-9]{5}[0-9]{5}[A-Z0-9]{11}[0-9]{2}$'),
        'NL' => array(14, '^[A-Z]{4}[0-9]{10}$'),
        'NO' => array(11, '^[0-9]{4}[0-9]{6}[0-9]{1}$'),
        'PF' => array(23, '^[0-9]{5}[0-9]{5}[A-Z0-9]{11}[0-9]{2}$'),
        'PK' => array(20, '^[A-Z]{4}[A-Z0-9]{16}$'),
        'PL' => array(24, '^[0-9]{8}[0-9]{16}$'),
        'PM' => array(23, '^[0-9]{5}[0-9]{5}[A-Z0-9]{11}[0-9]{2}$'),
        'PS' => array(25, '^[A-Z]{4}[A-Z0-9]{21}$'),
        'PT' => array(21, '^[0-9]{4}[0-9]{4}[0-9]{11}[0-9]{2}$'),
        'QA' => array(25, '^[A-Z]{4}[0-9]{4}[A-Z0-9]{17}$'),
        'RE' => array(23, '^[0-9]{5}[0-9]{5}[A-Z0-9]{11}[0-9]{2}$'),
        'RO' => array(20, '^[A-Z]{4}[A-Z0-9]{16}$'),
        'RS' => array(18, '^[0-9]{3}[0-9]{13}[0-9]{2}$'),
        'SA' => array(20, '^[0-9]{2}[A-Z0-9]{18}$'),
        'SC' => array(27, '^[A-Z]{4}[0-9]{4}[0-9]{16}[A-Z]{3}$'),
        'SE' => array(20, '^[0-9]{3}[0-9]{16}[0-9]{1}$'),
        'SI' => array(15, '^[0-9]{5}[0-9]{8}[0-9]{2}$'),
        'SK' => array(20, '^[0-9]{4}[0-9]{6}[0-9]{10}$'),
        'SM' => array(23, '^[A-Z]{1}[0-9]{5}[0-9]{5}[A-Z0-9]{12}$'),
        'SN' => array(24, '^[A-Z]{1}[0-9]{23}$'),
        'ST' => array(21, '^[0-9]{8}[0-9]{11}[0-9]{2}$'),
        'TF' => array(23, '^[0-9]{5}[0-9]{5}[A-Z0-9]{11}[0-9]{2}$'),
        'TL' => array(19, '^[0-9]{3}[0-9]{14}[0-9]{2}$'),
        'TN' => array(20, '^[0-9]{2}[0-9]{3}[0-9]{13}[0-9]{2}$'),
        'TR' => array(22, '^[0-9]{5}[0-9]{1}[A-Z0-9]{16}$'),
        'UA' => array(25, '^[0-9]{6}[A-Z0-9]{19}$'),
        'VG' => array(20, '^[A-Z]{4}[0-9]{16}$'),
        'WF' => array(23, '^[0-9]{5}[0-9]{5}[A-Z0-9]{11}[0-9]{2}$'),
        'XK' => array(16, '^[0-9]{4}[0-9]{10}[0-9]{2}$'),
        'YT' => array(23, '^[0-9]{5}[0-9]{5}[A-Z0-9]{11}[0-9]{2}$')
    );
    /**
     * @var string Internal IBAN number
     */
    private $iban;
    /**
     * IBAN constructor.
     *
     * @param $iban
     */
    public function __construct($iban)
    {
        $this->iban = $this->normalize($iban);
    }
    /**
     * Validates the supplied IBAN and provides passthrough failure message when validation fails
     *
     * @return bool
     */
    public function validate()
    {
        if (!$this->isCountryCodeValid() || !$this->isLengthValid() || !$this->isFormatValid() || !$this->isChecksumValid())
        {
            return false;
        }
        else
        {
            return true;
        }
    }
    /**
     * Pretty print IBAN
     *
     * @return string
     */
    public function format()
    {
        return sprintf(
            '%s %s %s',
            $this->getCountryCode() . $this->getChecksum(),
            substr($this->getInstituteIdentification(), 0, 4),
            implode(' ', str_split($this->getBankAccountNumber(), 4))
        );
    }
    /**
     * Extract country code from IBAN
     *
     * @return string
     */
    public function getCountryCode()
    {
        return substr($this->iban, static::COUNTRY_CODE_OFFSET, static::COUNTRY_CODE_LENGTH);
    }
    /**
     * Extract checksum number from IBAN
     *
     * @return string
     */
    public function getChecksum()
    {
        return substr($this->iban, static::CHECKSUM_OFFSET, static::CHECKSUM_LENGTH);
    }
    /**
     * Extract Account Identification from IBAN
     *
     * @return string
     */
    public function getAccountIdentification()
    {
        return substr($this->iban, static::ACCOUNT_IDENTIFICATION_OFFSET);
    }
    /**
     * Extract Institute from IBAN
     *
     * @return string
     */
    public function getInstituteIdentification()
    {
        return substr($this->iban, static::INSTITUTE_IDENTIFICATION_OFFSET, static::INSTITUTE_IDENTIFICATION_LENGTH);
    }
    /**
     * Extract Bank Account number from IBAN
     *
     * @return string
     */
    public function getBankAccountNumber()
    {
        $countryCode = $this->getCountryCode();
        $length = static::$ibanFormatMap[$countryCode][0] - static::INSTITUTE_IDENTIFICATION_LENGTH;
        return substr($this->iban, static::BANK_ACCOUNT_NUMBER_OFFSET, $length);
    }
    /**
     * Validate IBAN length boundaries
     *
     * @return bool
     */
    private function isLengthValid()
    {
        $countryCode = $this->getCountryCode();
        $validLength = static::COUNTRY_CODE_LENGTH + static::CHECKSUM_LENGTH + static::$ibanFormatMap[$countryCode][0];
        return strlen($this->iban) === $validLength;
    }
    /**
     * Validate IBAN country code
     *
     * @return bool
     */
    private function isCountryCodeValid()
    {
        $countryCode = $this->getCountryCode();
        return !(isset(static::$ibanFormatMap[$countryCode]) === false);
    }
    /**
     * Validate the IBAN format according to the country code
     *
     * @return bool
     */
    private function isFormatValid()
    {
        $countryCode = $this->getCountryCode();
        $accountIdentification = $this->getAccountIdentification();
        return !(preg_match('/' . static::$ibanFormatMap[$countryCode][1] . '/', $accountIdentification) !== 1);
    }
    /**
     * Validates if the checksum number is valid according to the IBAN
     *
     * @return bool
     */
    private function isChecksumValid()
    {
        $countryCode = $this->getCountryCode();
        $checksum = $this->getChecksum();
        $accountIdentification = $this->getAccountIdentification();
        $numericCountryCode = $this->getNumericCountryCode($countryCode);
        $numericAccountIdentification = $this->getNumericAccountIdentification($accountIdentification);
        $invertedIban = $numericAccountIdentification . $numericCountryCode . $checksum;
        return $this->bcmod($invertedIban, 97) === '1';
    }
    /**
     * Extract country code from the IBAN as numeric code
     *
     * @param $countryCode
     *
     * @return string
     */
    private function getNumericCountryCode($countryCode)
    {
        return $this->getNumericRepresentation($countryCode);
    }
    /**
     * Extract account identification from the IBAN as numeric value
     *
     * @param $accountIdentification
     *
     * @return string
     */
    private function getNumericAccountIdentification($accountIdentification)
    {
        return $this->getNumericRepresentation($accountIdentification);
    }
    /**
     * Retrieve numeric presentation of a letter part of the IBAN
     *
     * @param $letterRepresentation
     *
     * @return string
     */
    private function getNumericRepresentation($letterRepresentation)
    {
        $numericRepresentation = '';
        foreach (str_split($letterRepresentation) as $char) {
            $ord = ord($char);
            if ($ord >= 65 && $ord <= 90) {
                $numericRepresentation .= (string) ($ord - 55);
            } elseif ($ord >= 48 && $ord <= 57) {
                $numericRepresentation .= (string) ($ord - 48);
            }
        }
        return $numericRepresentation;
    }
    /**
     * Normailze IBAN by removing non-relevant characters and proper casing
     *
     * @param $iban
     *
     * @return mixed|string
     */
    private function normalize($iban)
    {
        return preg_replace('/[^a-z0-9]+/i', '', trim(strtoupper($iban)));
    }
    /**
     * Get modulus of an arbitrary precision number
     *
     * @param $x
     * @param $y
     *
     * @return string
     */
    private function bcmod($x, $y)
    {
        if (!function_exists('bcmod')) {
            $take = 5;
            $mod = '';
            do {
                $a = (int)$mod . substr($x, 0, $take);
                $x = substr($x, $take);
                $mod = $a % $y;
            } while (strlen($x));
            return (string)$mod;
        } else {
            return bcmod($x, $y);
        }
    }
}