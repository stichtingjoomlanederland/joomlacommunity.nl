<?php
/**
 * @package     FOF
 * @copyright   2010-2016 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 */

namespace FOF30\Form\Field;

use FOF30\Form\FieldInterface;

defined('_JEXEC') or die;

/**
 * Form Field class for the FOF framework
 * Supports a numeric field and currency symbols.
 */
class Numeric extends Text implements FieldInterface
{
	/**
	 * Print out the number as requested by the attributes
	 */
	public function getRepeatable()
	{
		$currencyPos = $this->getAttribute('currency_position', false);
		$currencySymbol = $this->getAttribute('currency_symbol', false);

		// Initialise
		$class             = $this->id;

		// Get field parameters
		if ($this->element['class'])
		{
			$class = (string) $this->element['class'];
		}

		// Start the HTML output
		$html = '<span class="' . $class . '">';

		// Prepend currency?
		if ($currencyPos == 'before' && $currencySymbol)
		{
			$html .= $currencySymbol;
		}

		$number = $this->value;

		// Should we format the number too?
		$formatNumber = false;
		if (isset($this->element['format_number']))
		{
			$formatNumberValue = (string)$this->element['format_number'];
			$formatNumber = in_array(strtolower($formatNumberValue), array('yes', 'true', 'on', 1));
		}

		// Format the number correctly
		if ($formatNumber)
		{
			$numDecimals 	= $this->getAttribute('decimals', 2);
			$minNumDecimals = $this->getAttribute('min_decimals', 2);
			$decimalsSep 	= $this->getAttribute('decimals_separator', '.');
			$thousandSep 	= $this->getAttribute('thousand_separator', ',');

			// Format the number
			$number = number_format((float)$this->value, $numDecimals, $decimalsSep, $thousandSep);
		}

		// Put it all together
		$html .= $number;

		// Append currency?
		if ($currencyPos == 'after' && $currencySymbol)
		{
			$html .= $currencySymbol;
		}

		// End the HTML output
		$html .= '</span>';

		return $html;
	}
}
