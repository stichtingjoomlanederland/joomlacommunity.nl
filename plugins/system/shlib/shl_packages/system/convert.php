<?php
/**
 * Shlib - programming library
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier 2015
 * @package      shlib
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      0.3.1.540
 * @date                2016-07-18
 */

// no direct access
defined('_JEXEC') or die;

class ShlSystem_Convert
{

	public static function hexToDecimal($originalHex)
	{

		if (!extension_loaded('bcmath'))
		{
			throw new ShlException(__METHOD__ . ': Using ShlSystem_Convert::hexToDecimal without BCMATH extension', 500);
		}

		$dec         = hexdec(substr($originalHex, -4));
		$originalHex = substr($originalHex, 0, -4);
		$running     = 1;
		while (!empty($originalHex))
		{
			$hex         = hexdec(substr($originalHex, -4));
			$running     = bcmul($running, 65536);
			$dec1        = bcmul($running, $hex);
			$dec         = bcadd($dec1, $dec);
			$originalHex = substr($originalHex, 0, -4);
		}

		return $dec;
	}

	/**
	 * Internal method to get a JavaScript object notation string from an array
	 * Customizd to handle systems wich decimal separator is not a dot
	 * An alternative would be to set locale to C before handling each numeric value
	 * and restore afterwards.
	 *
	 * @param   array $array The array to convert to JavaScript object notation
	 *
	 * @return  string  JavaScript object notation representation of the array
	 *
	 * @since   3.0
	 */
	public static function arrayToJSObject($array = array())
	{
		static $decimalPoint = null;
		static $thousandsSep = null;

		if (is_null($decimalPoint) || is_null($thousandsSep))
		{
			$localeInfo   = localeconv();
			$decimalPoint = $localeInfo['decimal_point'];
			$thousandsSep = $localeInfo['thousands_sep'];
		}

		$elements = array();

		foreach ($array as $k => $v)
		{
			// Don't encode either of these types
			if (is_null($v) || is_resource($v))
			{
				continue;
			}

			// Safely encode as a Javascript string
			$key = json_encode((string) $k);

			if (is_bool($v))
			{
				$elements[] = $key . ': ' . ($v ? 'true' : 'false');
			}
			elseif (is_numeric($v))
			{
				$value      = str_replace($thousandsSep, '', ($v + 0));
				$value      = str_replace($decimalPoint, '.', $value);
				$elements[] = $key . ': ' . $value;
			}
			elseif (is_string($v))
			{
				if (strpos($v, '\\') === 0)
				{
					// Items such as functions and JSON objects are prefixed with \, strip the prefix and don't encode them
					$elements[] = $key . ': ' . substr($v, 1);
				}
				else
				{
					// The safest way to insert a string
					$elements[] = $key . ': ' . json_encode((string) $v);
				}
			}
			else
			{
				$elements[] = $key . ': ' . self::arrayToJSObject(is_object($v) ? get_object_vars($v) : $v);
			}
		}

		return '{' . implode(',', $elements) . '}';
	}

}
