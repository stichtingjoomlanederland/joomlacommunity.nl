<?php
/**
 * @package    Pwtimage
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

defined('_JEXEC') or die;

/**
 * The PWT Image form field Image.
 *
 * @since  1.0
 */
class PwtimageFormFieldRatio extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 * @since  1.0
	 */
	protected $type = 'Ratio';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   1.0
	 */
	protected function getInput()
	{
		$ratioWidth = 0;
		$ratioHeight = 0;

		// Check if we have any value to load in the form
		if (!(isset($this->value) && is_string($this->value)))
		{
			$this->value = '';
		}

		// Check if ratio contains a forward slash
		$dashlocation = strpos($this->value, '/');

		if (is_int($dashlocation) && $dashlocation > 0)
		{
			// The ratioWidth should be a valid int
			$ratioWidth = (int) substr($this->value, 0, $dashlocation);

			if (!(is_int($ratioWidth) && $ratioWidth > 0))
			{
				// Somehow ratioWidth isn't valid so set a default value
				$ratioWidth = 0;
			}

			// The radioHeight should be a valid int
			$ratioHeight = (int) substr($this->value, $dashlocation + 1);

			if (!(is_int($ratioHeight) && $ratioHeight > 0))
			{
				$ratioWidth = 0;
			}
		}

		$html = array();

		$html[] = '<input type="number" value ="' . $ratioWidth . '" 
			name="' . str_replace('ratio', 'ratioWidth', $this->name) . '" class="input-mini"/>';
		$html[] = ' / ';
		$html[] = '<input type="number" value ="' . $ratioHeight . '" 
			name="' . str_replace('ratio', 'ratioHeight', $this->name) . '" class="input-mini"/>';

		return implode("\n", $html);
	}
}
